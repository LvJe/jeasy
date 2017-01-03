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