<?php
use Illuminate\Support\Facades\Redis;

// 获取今天id是否访问过
function recordUniqueVisitors($key, $ip) {
    $today = now()->toDateString(); // 获取今天的日期
    $yesterday = now()->subDay(1)->toDateString(); // 获取昨天的日期

    // 检查 Redis 中是否已经记录了今天的访问
    if (!Redis::sismember("{$key}:$today", $ip)) {
        // 如果该 IP 今天还没有访问过，增加 {$key} 计数
        Redis::sadd("{$key}:$today", $ip);
        Redis::incr("{$key}:count:$today");

        // 设置今天数据的过期时间为明天的时间（到第二天凌晨）
        Redis::expire("{$key}:$today", now()->startOfDay()->addDay(1)->diffInSeconds(now()));
    }

    // 清除昨天的数据
    Redis::del("{$key}:$yesterday");
    Redis::del("{$key}:count:$yesterday");
}
// 删除前缀$prifixredis信息
function delRedisPrefix($prefix){
    // 使用 KEYS 命令获取所有匹配的键
    $keys = Redis::keys("{$prefix}*");
    if (!empty($keys)) {
        // 遍历所有匹配的键，逐个删除
        foreach ($keys as $key) {
            Redis::del(ltrim($key,'laravel_database_'));
        }
    }
}
// 转时间格式
function toDate($time){
    if(!$time) return null;
    $userTimeZone = new DateTimeZone('Asia/Shanghai');
    // 创建 DateTime 对象并设置时区为 UTC
    $dateTime = new DateTime($time, $userTimeZone);

    // 格式化为所需的日期和时间格式
    return $dateTime->format('Y-m-d H:i:s');
}
