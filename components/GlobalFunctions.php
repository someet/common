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
function random($length = 6 , $numeric = 0) {
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    if($numeric) {
        $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
    } else {
        $hash = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
        $max = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, 6)];
        }
    }
    return $hash;
}