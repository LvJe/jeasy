<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016-12-31
 * Time: 18:38
 */
function IsAjaxRequest(){
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

//判断当前协议
function IsSSL()
{
    if (!isset($_SERVER['HTTPS']))
        return false;
    if ($_SERVER['HTTPS'] === 1) { //Apache
        return true;
    } elseif ($_SERVER['HTTPS'] === 'on') { //IIS
        return true;
    } elseif ($_SERVER['SERVER_PORT'] == 443) { //其他
        return true;
    }
    return false;
}
//获取时间戳
function getTimeStamp() {
    $MicroTime = microtime();
    $MicroTime = explode(' ', $MicroTime);
    $MicroTime = $MicroTime[1] + $MicroTime[0];
    return $MicroTime;
}
function getTimeStamp2() {
    $timestr = microtime();
    $timestrary = explode(' ', $timestr);
    $result = intval($timestrary[1])*1000 + intval(floatval($timestrary[0])*1000);
    return $result;
}

function getRandomString($len=6, $chars=null)
{
    if (is_null($chars)){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
    }
    mt_srand(10000000*(double)microtime());
    for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
        $str .= $chars[mt_rand(0, $lc)];
    }
    return $str;
}