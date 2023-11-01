<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseResponseController;
use App\Models\Statistic;
use App\Models\AdminUser;
use App\Models\OperationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends BaseResponseController
{
    public function login(Request $request)
    {
        // 验证用户提供的用户名和密码
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->error('用户名和密码为必填项');
        }
        $credentials = $request->only('username', 'password');
        if (! $token = auth('admin')->attempt($credentials)) {
            return $this->error( '账号密码错误');
        }
        $user = auth('admin')->user();

        if ($user->status !== 1) {
            auth('admin')->logout();
            return $this->error('用户已被禁用');
        }

        // 添加日志到数据库
        OperationLog::saveInfo([
            'controller' => 'AdminController',
            'method' => 'login',
            'parameters' => '[]',
            'start_time' => date('Y-m-d H:i:s',time()),
            'end_time' => date('Y-m-d H:i:s',time()),
            'admin_id' => $user['id'],
            'nickname' => $user['nickname'],
        ]);
        return $this->success(['access_token' => 'Bearer '.$token,'userName'=>$user['nickname']]);
    }
    public function logout ()
    {
        auth('admin')->logout();

        return response()->json('success');
    }
    // 存储角色信息
    function adminList(Request $request){
        $name = $request->input('name' , null);
        $page = $request->input('page' , 1);
        $pageSize = $request->input('pageSize' , 20);
        if($this->user->role_ids != 0){
            return $this->success(['list'=>[$this->user]]);
        }
        $where = [];
        if($name){
            $where[] = ['name' , 'like',"%{$name}%"];
        }

        $data = AdminUser::pageList($where,'*',$page,$pageSize);

        return $this->success($data);
    }
    // 存储管理员信息
    function saveAdmin(Request $request){
        $data = $request->all();
        // 验证信息
        $fail = AdminUser::getNotPassValidator($data);
        if($fail){
            return $this->error('用户名和密码为必填项');
        }
        if(!$data['nickname'] ){
            return $this->error('为昵称必填项');
        }
        if(!isset($data['id']) || !AdminUser::getInfo([['id' , '=' , $data['id'] ] ])){

            $info = AdminUser::getInfo([['username' , '=' , $data['username'] ] ]);
            if($info){
                return $this->error('无法添加相同名称的角色');
            }
        }
        $from = [
            'id' => $data['id'] ?? null,
            'username' => $data['username'] ?? '',
            'password' =>  bcrypt($data['password']) ?? '',
            'role_ids' =>  $data['role_ids'] ?? '',
            'overdue_time' =>  $data['overdue_time'] ?? null,
            'describe' =>  $data['describe'] ?? '',
            'status' =>  $data['status'] ?? 0,
            'nickname' =>  $data['nickname'] ?? '',
        ];
        $id = AdminUser::saveInfo($from);
        return $this->success($id);
    }
    public function getHomeStatistics(){

        $today = Carbon::now();
        $startOfDay = $today->startOfDay()->format('Y-m-d H:i:s');
        // 今日最热文章列表
        $list = Statistic::getArticleTotalList($startOfDay);
        // 总访问
        $allTotal = Statistic::getInfoTotal('allTotal');
        // ip访问
        $allTotalLimit = Statistic::getInfoTotal('allTotalLimit');
        // 获取15天内的范围数量
        $fifDaysAgo = Carbon::now()->subDays(29);
        $fifteenDaysAgo = Carbon::now()->subDays(15);
        $endDate = Carbon::now()->endOfDay();

        // 15日对比数据
        $statistics = [
            'lastStatis' => [],
            'nowStatis' => [],
        ];
        $statisticsIp = [
            'lastStatis' => [],
            'nowStatis' => [],
        ];
        while ($fifDaysAgo <= $endDate) {
            $ft = $fifDaysAgo->format('Y-m-d');
            $k = $fifDaysAgo <= $fifteenDaysAgo ? 'lastStatis' : 'nowStatis';
            $statistics[$k][] = [
                'date' => $ft,
                'value' => Statistic::getInfoTotal('allTotal' , null,$ft)
            ];
            $statisticsIp[$k][] = [
                'date' => $ft,
                'value' => Statistic::getInfoTotal('allTotalLimit' , null,$ft)
            ];
            $fifDaysAgo->addDay();
        }
        // 获取今年总数据
        $yearStart = Carbon::now()->startOfYear()->format('Y-m-d H:i:s');
        $yearAllTotal = Statistic::getSectionTotal('allTotal' ,$yearStart);
        $yearAllTotalLimit = Statistic::getSectionTotal('allTotalLimit',$yearStart);

//        $historyList = Article::getList(null , ['title','id','click_num'],'click_num desc',15);
//
//        $row = ConfigurationGroup::getInfo(['name' => '应用配置']);
//        $where = ['configuration_group_id' => $row['id']];
//        $configuration = Configuration::getList($where,['key','value'],'id asc');
//        $configForKey = [];
//        foreach($configuration as $v){
//            $configForKey[$v['key']] = $v['value'];
//        }
//        return $this->success(['articleList' => $list,'historyHotList' => $historyList,'allTotal' => $allTotal , 'allTotalLimit' => $allTotalLimit,'statistics' => $statistics,'statisticsIp' => $statisticsIp,'yearAllTotal' => $yearAllTotal,'yearAllTotalLimit' => $yearAllTotalLimit,'configuration' => $configForKey]);
    }
    // 存储角色信息
    function deleteAdmin(Request $request){
        $id = $request->input('id' , null);
        if($id == 1) return $this->error('禁止删除超级管理员');
        AdminUser::deleteInfo($id);
        return $this->success($id);
    }
}
