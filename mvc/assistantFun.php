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

// 获得IP地址 //TODO 待
function CurIP()
{
    $IsCDN = false; //未使用CDN时，应直接使用 $_SERVER['REMOTE_ADDR'] 以防止客户端伪造IP
    $IP    = false;
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $IP = trim($_SERVER["HTTP_CLIENT_IP"]);
    }
    if ($IsCDN && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $IPs = array_map("trim", explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']));
        if ($IP) {
            array_unshift($IPs, $IP);//插入头部而不是尾部，提升性能
            $IP = FALSE;
        }
        //支持使用CDN后获取IP，理论上令 $IP = $IPs[0]; 即可，安全起见遍历过滤一次
        foreach ($IPs as $Key => $Value) {
            /*
            Fails validation for the following private IPv4 ranges: 10.0.0.0/8, 172.16.0.0/12 and 192.168.0.0/16.
            Fails validation for the IPv6 addresses starting with FD or FC.
            */
            if (filter_var($Value, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)){
                $IP = $Value;
                break;
            }
        }
    }
    return htmlspecialchars($IP ? $IP : $_SERVER['REMOTE_ADDR']);
}

//获取时间戳
function getMicroTime() {
    $MicroTime = microtime();
    $MicroTime = explode(' ', $MicroTime);
    $MicroTime = $MicroTime[1] + $MicroTime[0];
    return $MicroTime;
}
/*function getTimeStamp2() {
    $timestr = microtime();
    $timestrary = explode(' ', $timestr);
    $result = intval($timestrary[1])*1000 + intval(floatval($timestrary[0])*1000);
    return $result;
}*/

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

//Hash值校验，防止时序攻击法
function HashEquals($KnownString, $UserString)
{
    if (version_compare(PHP_VERSION, '5.6.0') < 0) {
        return ($KnownString === $UserString);
    } else {
        return hash_equals($KnownString, $UserString);
    }
}

//判断是否为邮件地址
function IsEmail($email)
{
    return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}


//判断是否为合法用户名
function IsName($string)
{
    return !preg_match('/^[0-9]{4,20}$/', $string) && preg_match('/^[a-zA-Z0-9\x80-\xff\-_]{4,20}$/i', $string);
}

//简单封装了一下setCookie 可以直接传个数组设置多个值-je
//批量设置Cookie
function SetCookies($CookiesArray, $Expires = 0)
{
    foreach ($CookiesArray as $key => $value) {
        if (!$Expires)
            setcookie(COOKIE_PREFIX.$key, $value, 0);
        else
            setcookie(COOKIE_PREFIX.$key, $value, time() + 86400 * $Expires);
    }
}

//获取数组中的某一列
function ArrayColumn($Input, $ColumnKey)
{
    if (version_compare(PHP_VERSION, '5.5.0') < 0) {
        $Result = array();
        if ($Input) {
            foreach ($Input as $Value) {
                $Result[] = $Value[$ColumnKey];
            }
        }
        return $Result;
    } else {
        return array_column($Input, $ColumnKey);
    }
}