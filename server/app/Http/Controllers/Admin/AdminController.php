<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Statistic;
use App\Models\AdminUser;
use App\Models\OperationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class AdminController extends BaseController
{
    public function login(Request $request): Response
    {

        $data = $request->all();
        $fail = AdminUser::getNotPassValidator($data);
        if ($fail) {
            return $this->error('Username and password are required');
        }
        $credentials = $request->only('userName', 'password');
        if (!$token = auth('admin')->attempt($credentials)) {
            return $this->error( 'Account password is wrong');
        }
        $user = auth('admin')->user();
        if ($user->status !== 0) {
            auth('admin')->logout();
            return $this->error('User has been disabled');
        }
        // Add log to database
        $from = [
            'controller' => 'AdminController',
            'method' => 'login',
            'parameters' => '[]',
            'start_time' => date('Y-m-d H:i:s',time()),
            'end_time' => date('Y-m-d H:i:s',time()),
            'admin_id' => $user['id'],
            'nickname' => $user['nickname'],
        ];
        OperationLog::saveInfo($from);

        return $this->success(['access_token' => 'Bearer '.$token,'userName'=>$user['nickname']]);
    }
    public function logout (): Response
    {
        auth('admin')->logout();
        return $this->success(null);
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
    public function saveAdmin(Request $request) {
        $data = $request->all();
        $fail = AdminUser::getNotPassValidator($data);
        if($fail){
            return $this->error('Username and password are required!');
        }
        if(!$data['nickname'] ){
            return $this->error('Required for nickname');
        }
        if(!isset($data['id']) || !AdminUser::getInfo([['id' , '=' , $data['id'] ] ])){
            $info = AdminUser::getInfo([['userName' , '=' , $data['userName'] ] ]);

            if($info){
                return $this->error('Cannot add role with same name');
            }
        }
        $from = [
            'id' => $data['id'] ?? null,
            'userName' => $data['userName'] ?? '',
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
        if($id == 1) return $this->error('Disable deletion of super administrator');
        AdminUser::deleteInfo($id);
        return $this->success($id);
    }
}
