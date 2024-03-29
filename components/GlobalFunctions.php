<?php
/**
 * Created by PhpStorm.
 * User: maxwelldu
 * Date: 11/12/2015
 * Time: 11:56 AM
 */
/**
 * 生成随机数
 * @param int $length 长度,默认为6位
 * @param int $numeric 数字,默认为字母
 * @return string
 */

function random($length = 6, $numeric = 0)
{
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    if ($numeric) {
        $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
    } else {
        $hash = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, 6)];
        }
    }
    return $hash;
}

/**
 * 获取本周到前一个月的时间
 * @param $time integer 当前活动的时间
 * @return bool true:本周活动 | false:上周活动
 */
function getRecenMtonths()
{
    $date = date('Y-m-d');  //当前日期
    $first = 1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
    $w = date('w', strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
    $now_start = date('Y-m-d', strtotime("$date -".($w ? $w - $first : 6).' days')); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
    $last_end_time = strtotime($now_start." 00:00:00");
    return $last_end_time - 30*86400;
}

/**
 * 判断是否是本周活动
 * @param $time integer 当前活动的时间
 * @return bool true:本周活动 | false:上周活动
 */
function getLastEndTime()
{
    $date = date('Y-m-d');  //当前日期
    $first = 1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
    $w = date('w', strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
    $now_start = date('Y-m-d', strtotime("$date -".($w ? $w - $first : 6).' days')); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
    $last_end_time = strtotime($now_start." 00:00:00");
    return $last_end_time;
}

/**
 * 判断是否为上周活动
 * @param $time integer 当前活动的时间
 * @return bool true:本周活动 | false:上周活动
 */
function getWeekBefore()
{
    $date = date('Y-m-d');  //当前日期
    $first = 1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
    $w = date('w', strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
    $now_start = date('Y-m-d', strtotime("$date -".($w ? $w - $first : 6).' days')); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
    $last_end_time = strtotime($now_start." 00:00:00");
    $get_week_before = $last_end_time - 604800;
    return $get_week_before;
}


/**
 * 获取 对应的城市名
 * @return mixed
 */
function getIpCity()
{
    $ipContent  = file_get_contents("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js");
    $jsonData = explode("=", $ipContent);
    $jsonAddress = substr($jsonData[1], 0, -1);
    return json_decode($jsonAddress)->city;
}

/**
 * 获取城市编号
 * @return mixed
 */
function getCityId()
{
    $session = Yii::$app->session;
    return $session->get('city_id', '2');
}

/**
 * 设置城市编号
 * @param int $city_id
 */
function setCityId($city_id=2)
{
    $session = Yii::$app->session;
    $session->set('city_id', $city_id);
}

/**
 * 获取城市名称
 * @return mixed
 */
function getCity()
{
    $session = Yii::$app->session;
    return $session->get('city', '北京');
}

/**
 * 设置城市
 * @param string $city
 */
function setCity($city = '北京')
{
    $session = Yii::$app->session;
    $session->set('city', $city);
}

function hasCityId()
{
    $session = Yii::$app->session;
    return $session->has('city_id');
}

/**
 * 前台获取用户选择的城市
 * @return int
 */
function getUserCityId()
{
    $user_id = Yii::$app->user->id;
    if (!$user_id) {
        return 2;
    }

    $user = \someet\common\models\User::findOne($user_id);
    if (!$user) {
        return 2;
    }
    return $user->city_id;
}

/**
 * 前台设置用户选择的城市
 * @param int $city_id
 */
function setUserCityId($city_id = 2)
{
    $user_id = Yii::$app->user->id;
    if ($user_id > 0 && $city_id!=getUserCityId()) {
        \someet\common\models\User::updateAll(['city_id' => $city_id], ['id' => $user_id]);
    }
}