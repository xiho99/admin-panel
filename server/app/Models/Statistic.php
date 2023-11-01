<?php


namespace App\Models;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class Statistic extends BaseModel
{
    protected $table = 'statistics';
    protected $fillable = ['id', 'key', 'type', 'date', 'num', 'parameter_id', 'update_time', 'create_time'];
    protected static $initBase;

    public static function initBase()
    {
        if (!self::$initBase) {
            self::$initBase = new static();
        }
        return self::$initBase;
    }

    // 统计数据
    public static function incKeyNum($key, $t = null, $parameter_id = null, $type = 1)
    {
        !$t && $t = date('Y-m-d', strtotime('today'));
        $where = ['date' => $t, 'key' => $key];
        $parameter_id && $where['parameter_id'] = $parameter_id;
        $info = self::getInfo($where);
        // 不存在就添加
        if (!$info) {
            return self::saveInfo([
                'key' => $key,
                'date' => $t,
                'parameter_id' => $parameter_id,
                'type' => $type,
                'num' => 1
            ]);
        }
        // 存在则加1
        self::inc($where, 'num');
    }

    // 限制重复IP
    public static function incKeyNumLimitIp($key, $t = null, $parameter_id = null, $ip = null)
    {

        if (!$ip || self::visitIp($key . $parameter_id, $ip, $t)) return;

        self::incKeyNum($key, $t, $parameter_id, 2);
        return;
    }

    public static function visitIp($key, $ip, $t = null)
    {
        !$t && $t = date('Y-m-d', strtotime('today'));
        $vis = Redis::sismember($key . $t . 'visited_ips', $ip);
        Redis::sadd($key . $t . 'visited_ips', $ip);
        return $vis;
    }

    // 获取当日类型统计
    public static function getInfoTotal($key, $id = null, $t = null)
    {
        !$t && $t = date('Y-m-d', strtotime('today'));
        $where = [
            'parameter_id' => $id,
            'key' => $key,
            'date' => $t,
        ];
        return self::getValue($where, 'num', 0) ?? 0;
    }

    // 获取区间类型统计
    public static function getSectionTotal($key, $startTime, $endTime = null, $id = null)
    {
        !$endTime && $endTime = date('Y-m-d 23:59:59', strtotime('today'));
        $where = [
            ['date', '>=', $startTime],
            ['date', '<', $endTime],
            ['key', '=', $key],
            ['parameter_id', '=', $id],
        ];
        $info = self::getInfo($where, [DB::raw('SUM(num) AS total_num')]);
        return $info ? (int)$info['total_num'] : 0;
    }

    // 获取今日文章访问次数，列表
    public static function getArticleTotalList($startTime, $endTime = null, $limit = 15)
    {
        !$endTime && $endTime = date('Y-m-d 23:59:59', strtotime('today'));
        $where = [
            ['date', '>=', $startTime],
            ['date', '<', $endTime],
            ['key', '=', 'articleTotal'],
        ];
        $list = self::getList($where, ['*'], 'num desc', $limit);
        $data = [];
        $ids = [];
        foreach ($list as $v) {
            if (!$v['parameter_id']) continue;
            $ids[] = $v['parameter_id'];
            $data[$v['parameter_id']] = $v;
        }
        $list = Article::getList([['id', 'in', $ids]], ['title', 'id']);
        foreach ($list as $v) {
            $data[$v['id']]['title'] = $v['title'];
        }
        return array_values($data);
    }
}
