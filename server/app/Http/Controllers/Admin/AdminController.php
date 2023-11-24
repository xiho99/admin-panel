<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\IPStatistic;
use App\Models\Statistic;
use App\Models\AdminUser;
use App\Models\OperationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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
            return $this->error('Account password is wrong');
        }
        $user = auth('admin')->user();
        if ($user->status == 0) {
            auth('admin')->logout();
            return $this->error('User has been disabled');
        }
        // Add log to database
        $from = [
            'controller' => 'AdminController',
            'method' => 'login',
            'parameters' => '[]',
            'start_time' => date('Y-m-d H:i:s', time()),
            'end_time' => date('Y-m-d H:i:s', time()),
            'admin_id' => $user['id'],
            'nickname' => $user['nickname'],
        ];
        OperationLog::saveInfo($from);

        return $this->success(['access_token' => 'Bearer ' . $token, 'userName' => $user['nickname'], 'userId' => $user['id']]);
    }

    public function logout(): Response
    {
        auth('admin')->logout();
        return $this->success(null);
    }

    // 存储角色信息
    public function adminList(Request $request)
    {

        $name = $request->input('name', null);
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 20);
        if ($this->user['role_ids'] != 0) {
            $orWhere = [['id', '=', (string)$this->user['id']], ['p_id', '=', (string)$this->user['id']]];
            $where = [['', 'or', $orWhere]];
//            $where[] = ['','or' , [['userName' , 'like' ,'%com%']]];
//            DB::enableQueryLog();
//            $queryLog = DB::getQueryLog();
            $data = AdminUser::getListData($where, ['*'], $page, $pageSize, 'created_at desc');

            return $this->success($data);
        }
        $where = [];
        if ($name) {
            $where[] = ['name', 'like', "%{$name}%"];
        }

        $data = AdminUser::pageList($where, '*', $page, $pageSize);

        return $this->success($data);
    }

    // 存储管理员信息
    public function saveAdmin(Request $request)
    {
        $data = $request->all();
        $fail = AdminUser::getNotPassValidator($data);
        if ($fail) {
            return $this->error('Username and password are required!');
        }
        if (!$data['nickname']) {
            return $this->error('Required for nickname');
        }
        if (!isset($data['id']) || !AdminUser::getInfo([['id', '=', $data['id']]])) {
            $info = AdminUser::getInfo([['userName', '=', $data['userName']]]);

            if ($info) {
                return $this->error('Cannot add role with same name');
            }
        }
        $pid = null;
        if ($data['p_id'] != $this->user['id']) {
            $pid = $data['p_id'];
        }
        $from = [
            'id' => $data['id'] ?? null,
            'p_id' => $pid,
            'userName' => $data['userName'],
            'nickname' => $data['nickname'] ?? '',
            'password' => bcrypt($data['password']) ?? '',
            'role_ids' => $data['role_ids'] ?? '',
            'overdue_time' => $data['overdue_time'] ?? null,
            'describe' => $data['describe'] ?? '',
            'status' => $data['status'] ?? 0,
        ];

        $id = AdminUser::saveInfo($from);
        return $this->success($id);
    }

    public function getHomeStatistics()
    {

        $today = Carbon::now();
//        $startOfDay = $today->startOfDay()->format('Y-m-d H:i:s');
        // 今日最热文章列表
        $currentDay = date('Y-m-d');
        $where = ['create_time' => $currentDay];
        $todayIP = count(IPStatistic::where($where)->get());
        $todayView = collect(IPStatistic::where($where)->get())->sum('ip_access');
        $totalIpCurrentYear = IPStatistic::whereBetween('create_time', [
            Carbon::now()->startOfYear(),
            Carbon::now()->endOfYear(),
        ])->count('ip');
        $totalViewCurrentYear = IPStatistic::whereBetween('create_time', [
            Carbon::now()->startOfYear(),
            Carbon::now()->endOfYear(),
        ])->sum('ip_access');
        $thisYear = date('Y');
        $groupThisYear = DB::table('ip_statistics')
            ->whereYear('create_time', $thisYear)
            ->selectRaw('MONTH(create_time) as month, COUNT(*) as count, SUM(ip_access) as ip_access')
            ->groupBy('month')
            ->get();
        $previousYear = date("Y", strtotime("-1 year"));
        $groupPreviousYear = DB::table('ip_statistics')
            ->whereYear('create_time', $previousYear)
            ->selectRaw('MONTH(create_time) as month, COUNT(*) as count, SUM(ip_access) as ip_access')
            ->groupBy('month')
            ->get();
        return $this->success([
            'todayIp' => $todayIP,
            'todayView' => $todayView,
            'totalIP' => $totalIpCurrentYear,
            'totalViewCurrentYear' => (int)$totalViewCurrentYear,
            'groupThisYear' => $groupThisYear,
            'groupPreviousYear' => $groupPreviousYear,
        ]);

        // 获取今年总数据
//        $yearStart = Carbon::now()->startOfYear()->format('Y-m-d H:i:s');
//        $yearAllTotal = Statistic::getSectionTotal('allTotal' ,$yearStart);
//        $yearAllTotalLimit = Statistic::getSectionTotal('allTotalLimit',$yearStart);

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
    function deleteAdmin(Request $request)
    {
        $id = $request->input('id', null);
        if ($id == 1) return $this->error('Disable deletion of super administrator');
        AdminUser::deleteInfo($id);
        return $this->success($id);
    }
}
