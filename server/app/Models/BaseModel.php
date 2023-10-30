<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class BaseModel extends Model
{
    protected $table; // 表名
    protected $fillable; //可编辑字段
    protected $primaryKey = 'id';

    protected $rules = []; // 添加字段时，需要的规则
    protected $customAttributes = []; // 缺少字段放回的提示（未使用）

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    // 分页
    protected $page = 1;
    protected $pageSize = 20;
    // 验证数据
    // 数组转为条件
    // $where = [
    // ['id' , '=', 1],
    // ['' , 'or' , [['id' , '=', 2],['title' , 'like' , 'mgmg']]],
    // ['','or' , ['name' ,'=', 'aaa'],['id' , '=' , 100]],
    // ]

    // selete * where id=1 or (id = 2 and title list 'mgmg') or (name = 'aaa' and id =)


    public static function getNotPassValidator($data){
        $model = self::initBase();
        if(!count($model->rules)) return false;
        $validator = Validator::make($data, $model->rules);
        if ($validator->fails()) {
            // 获取不满足条件的字段
            $failedFields = $validator->failed();
            return $failedFields;
        }
        return false;
    }

    public function handleConditions($objQuery,$where,$or = false)
    {
        $w = $or ? 'or' : 'and';
        foreach ($where as $key => $condition) {
            if(!is_array($condition)){
                $objQuery->where($key, '=', $condition, $w);
            }else{
                list($column, $operator, $value) = $condition;
                switch ($operator) {
                    case 'in':
                        $values = !is_array($value)  ? explode(',', $value) : $value;
                        $objQuery->whereIn($column, $values, $w);
                        break;
                    case 'notIn':
                        $values = !is_array($value)  ? explode(',', $value) : $value;
                        $objQuery->whereNotIn($column, $values, $w);
                        break;
                    case 'like':
                        $objQuery->where($column, 'like', $value, $w);
                        break;
                    case 'find_in_set':
                        $objQuery->whereRaw("FIND_IN_SET(?, $column)", [$value], $w);
                        break;
                    case 'or':
                        $self = $this;
                        $objQuery->where(function ($query) use ($value, $self) { // 使用 $self 代替 $this
                            $self->handleConditions($query, $value, true);
                        });
                        break;
                    default:
                        $objQuery->where($column, $operator, $value);
                        break;
                    // 添加更多操作符的处理
                }
            }
        }
        return $objQuery;
    }
    /**
     *
     * @param $db_obj
     * @param $join     ['type' => 'left','table' => 'user','on' =>]
     * @return mixed
     */
    public function parseJoin($dbObj, $join){
        foreach ($join as $item) {
            list($table, $on, $type) = $item;
            if(count($on) < 3) return $dbObj;
            $type = strtolower($type);

            switch ($type) {
                case "left":
                    $dbObj->leftJoin($table, $on[0], $on[1], $on[2]);
                    break;
                case "right":
                    $dbObj->rightJoin($table, $on[0], $on[1], $on[2]);
                    break;
                default:
                    $dbObj->join($table, $on[0], $on[1], $on[2]);
                    break;
            }
        }

        return $dbObj;
    }
   /**
     * 获取列表数据
     * @param array $condition
     * @param array|bool $field
     * @param string $order
     * @param string $alias
     * @param array $join
     * @param string $group
     * @param null $limit
     * @return array
     */
    public static function getList(array $condition = [], array|bool $field = ['*'], string $order = 'id desc', $limit = null, $from = null, array $join = [], string $group = '', $cache = true)
    {
        $model = self::initBase();
        if($cache){
            // 首先尝试从 Redis 缓存获取数据
            $result = Redis::get('Table-'.$model->table.'Redis:'.json_encode([$condition,$join,$field]));
            if ($result) {
                // 如果缓存中有数据，直接返回它
                return json_decode($result ,true);
            }
        }
        $objQuery = self::query(); // 创建一个查询构建器


        // 处理条件
        $condition && $objQuery = $model->handleConditions($objQuery,$condition);

        if(in_array('is_delete',$model->fillable)){
                $objQuery->where('is_delete', '=', 0);
        }
        // 设置排序
        $order && $objQuery->orderByRaw($order);

        // 如果存在 JOIN 条件
        if (!empty($join)) {
            $objQuery = $model->parseJoin($objQuery, $join);
        }

        // 如果有 GROUP BY 条件
        if (!empty($group)) {
            $objQuery->groupBy($group);
        }

        // 如果有限制数量
        if (!empty($limit)) {
            $objQuery->limit($limit);
            if(!empty($from)) {
                $objQuery->offset($from - 1);
            }
        }

        // 获取结果
        $result = $objQuery->select($field)->get()->toArray();

        // 将获取到的数据存储到 Redis 缓存
        $cache && Redis::set('Table-'.$model->table.'Redis:'.json_encode([$condition,$join,$field]), json_encode($result));
        return $result;
    }

    /**
     * 获取分页列表数据
     * @param unknown $where
     * @param array|string $field
     * @param string $order
     * @param int $page
     * @param string $list_rows
     * @param string $alias
     * @param unknown $join
     * @param string $group
     * @param string $limit
     */
    public static function pageList(array $condition = [], array|string $field = ['*'], int $page = 0, $pageSize = 0, string $order = 'id desc', array $join = [], $group = null, $limit = null){
        $model = self::initBase();
        // 首先尝试从 Redis 缓存获取数据
        $result = Redis::get('Table-'.$model->table.'Redis:'.json_encode([($condition ?? []),($join ?? []),$field]).$page.$pageSize);
        // print_r('Table-'.$model->table.'Redis:');die;
        if ($result) {
            // 如果缓存中有数据，直接返回它
            return json_decode($result ,true);
        }
        // 创建一个查询构建器
        $objQuery = self::query(); // 创建一个查询构建器

        // 处理条件
        $condition && $objQuery = $model->handleConditions($objQuery,$condition);

        if(in_array('is_delete',$model->fillable)){
                $objQuery->where('is_delete', '=', 0);
        }
        // 设置排序
        $order && $objQuery->orderByRaw($order);

        // 如果存在 JOIN 条件
        if (!empty($join)) {
            $objQuery = $model->parseJoin($objQuery, $join);
        }

        // 如果有 GROUP BY 条件
        if (!empty($group)) {
            $objQuery->groupBy($group);
        }

        // 如果有限制数量
        if (!empty($limit)) {
            $objQuery->limit($limit);
        }

        // 获取总记录数
        $count = $objQuery->count();

        // 如果每页显示的记录数为 0，则查询全部
        if ($pageSize == 0) {
            // $resultData = $objQuery->select($field)->get();
            $pageSize = 20;
            $pageCount = 1;
        }

        // 分页查询
        $resultData = $objQuery->forPage($page, $pageSize)->select($field)->get();
        $pageCount = ceil($count / $pageSize);

        // 返回结果数组
        $result = [
            'count' => $count,
            'page_count' => $pageCount,
            'list' => $resultData,
        ];
        // 将获取到的数据存储到 Redis 缓存
        Redis::set('Table-'.$model->table.'Redis:'.json_encode([($condition ?? []),($join ?? []) ,$field]).$page.$pageSize, json_encode($result));

        return $result;
    }
    // 获取总数
    public static function count($condition = [], $field = ['*'],$join = [], $group = null){

        $model = self::initBase();
        // 首先尝试从 Redis 缓存获取数据
        $result = Redis::get('Table-'.$model->table.'Redis:count'.json_encode([$condition,$join]));
        if ($result) {
            // return $result;
        }
        // 创建一个查询构建器
        $objQuery = self::query(); // 创建一个查询构建器

        // 处理条件
        $condition && $objQuery = $model->handleConditions($objQuery,$condition);

        if(in_array('is_delete',$model->fillable)){
                $objQuery->where('is_delete', '=', 0);
        }

        // 如果存在 JOIN 条件
        if (!empty($join)) {
            $objQuery = $model->parseJoin($objQuery, $join);
        }

        // 如果有 GROUP BY 条件
        if (!empty($group)) {
            $objQuery->groupBy($group);
        }

        // 获取总记录数
        $count = $objQuery->count();
        // 将获取到的数据存储到 Redis 缓存
        Redis::set('Table-'.$model->table.'Redis:'.json_encode([$condition,$join]), $count);

        return $count;
    }
    /**
     * 获取单条数据
     * @param array $where
     * @param string $field
     * @param string $join
     * @param string $data
     * @return mixed
     */
    public static function getInfo($condition = [], $field = '*', $join = null, $data = null)
    {
        $model = self::initBase();
        // 创建一个查询构建器
        $objQuery = self::query();

        if(in_array('is_delete',$model->fillable)){
                $objQuery->where('is_delete', '=', 0);
        }
        // 处理条件
        $objQuery = $model->handleConditions($objQuery,$condition);
        // 如果没有 JOIN 条件
        if (empty($join)) {
            // 没有 JOIN 条件，直接查询
            $result = $objQuery->select($field)->first();
        } else {
            $objQuery = $model->parseJoin($objQuery, $join);
            // 获取单条数据
            $result = $objQuery->first();
        }

        return $result?$result->toArray():$result;
    }


    /**
     * 新增多条数据
     * @param array $data 数据
     * @param int $limit 限制插入行数
     */
    public static function addList($data = [], $limit = null)
    {
        // 获取数据表的模型实例
        $model = self::getModel();

        // 使用 insert 方法插入多条记录
        $result = $model->insert($data);

        // 删除此表格redis数据
        delRedisPrefix('Table-'.$model->table.'Redis:');
        return $result;
    }
    public static function saveInfo($data = []){
        $model = self::initBase();
        if (isset($data['id'])) {
            if($data['id']){
                // 如果数据中包含ID，执行编辑操作
                $record = self::find($data['id']); // 根据ID查找记录
                if ($record) {
                    // 更新数据
                    $record->fill($data); // 使用 fill 方法填充数据
                    // 删除此表格redis数据
                    $id =  $record->save(); // 使用 save 方法保存更改
                    delRedisPrefix('Table-'.$model->table.'Redis:');
                    return $id;
                }else{
                    return false;
                }
            }
            // 没有数据存在id则删除
            unset($data['id']);
        }
        $obj = self::create($data);
        // $data =   $obj->save(); // 使用 save 方法保存更改
        // 删除此表格redis数据
        delRedisPrefix('Table-'.$model->table.'Redis:');
        return $obj->id;
    }
    // 更新
    public static function change($where, $data){
        $model = self::initBase();
        // 创建一个查询构建器
        $objQuery = self::query();
        // 处理条件
        $objQuery = $model->handleConditions($objQuery,$where);
        $id = $objQuery->update($data);
        delRedisPrefix('Table-'.$model->table.'Redis:');
        return $id;

    }
    // 删除一条数据，存在软删除则使用软删除
    public static function deleteInfo( $id){
        $model = self::initBase();
        // 创建一个查询构建器
        $objQuery = self::query();
        // 处理条件
        $objQuery = $model->handleConditions($objQuery,[['id' , '=' , $id]]);
        if(in_array('is_delete',$model->fillable)){
            $id = $objQuery->update(['is_delete' => 1]);
            delRedisPrefix('Table-'.$model->table.'Redis:');
            return $id;
        }
        $id =  $objQuery->delete();
        // 删除此表格redis数据
        delRedisPrefix('Table-'.$model->table.'Redis:');
        return $id;

    }

    // 自增方法
    public static function inc($condition,$field, $amount = 1)
    {
        $model = self::initBase();
        $objQuery = self::query();
        $data = $model->handleConditions($objQuery,$condition)->get();
        // 遍历记录并递增特定列的值
        foreach ($data as $record) {
            $record->increment($field, $amount); // 递增指定的列
        }
        // 删除此表格redis数据
        delRedisPrefix('Table-'.$model->table.'Redis:');
        return ;
    }
    // 自增方法
    public static function dec($condition, $amount = 1)
    {
        $model = self::initBase();
        $objQuery = self::query();
        $objQuery = $model->handleConditions($objQuery,$condition);
        $objQuery->decrement($field, $amount);
        // 删除此表格redis数据
        delRedisPrefix('Table-'.$model->table.'Redis:');
        return ;
    }
    /**
     * 获取某个字段的值
     *
     * @param array $where 条件
     * @param string $field 字段名
     * @param mixed $default 默认值
     * @param bool $force 强制转为数字类型
     * @return mixed
     */
    public static function getValue($where = [], $field = '', $default = null, $force = false)
    {
        $model = self::initBase();
        // 创建一个查询构建器
        $objQuery = self::query();
        if(in_array('is_delete',$model->fillable)){
                $objQuery->where('is_delete', '=', 0);
        }
        // 处理条件
        $objQuery = $model->handleConditions($objQuery,$where);

        // 查询数据并返回字段值
        return $objQuery->value($field, $default, $force);
    }

}
