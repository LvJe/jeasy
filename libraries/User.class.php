<?php
/**
 * Class User
 *
 * Copyright(C) 2016 zGwit Co.,Ltd. All Rights Reserved.
 * Author   Jason.Zh
 * Date     2016.04.01
 * Version  V1.0
 *
 * History
 *      V1.0    2016.04.01  Jason.Zh    Create
 *
 */

class User
{
    /**
     * Encrypt password (use MD5)
     * @param $password
     * @return string
     */
    public static function EncryptPassword(&$password,&$salt){
        $encrypt = false;
        $salt='';
        $secure = C('secure');
        if(is_array($secure) && isset($secure['encryptPassword'])){
            $encrypt = $secure['encryptPassword'];
        }

        $salt=$encrypt ?getRandomString(6):$salt;
        $password = $encrypt ? md5($salt.$password) : $password;
    }

    /**
     * Login operator
     * @param $username
     * @param $password
     * @param bool $remember
     * @return bool
     */
    public static function Login($username, $password, $remember=false){
        $model = new UserModel();
        $user = $model->queryByUsername($username);
        if(is_array($user)){
            $password = self::EncryptPassword($password);
            if($password === $user['password']){
                $_SESSION['user'] = $user;
                if($remember){
                    setcookie('username', $username, time()+3600*24*15, '/');
                    setcookie('password', md5($password), time()+3600*24*15, '/');
                }
                return true;
            }
        }
        //删除Cookie
        setcookie('username', '', time()-3600*24*15, '/');
        setcookie('password', '', time()-3600*24*15, '/');
        return false;
    }

    /**
     * Logout operator
     */
    public static function Logout(){
        unset($_SESSION['user']);
        setcookie('username', '', time()-3600*24*15, '/');
        setcookie('password', '', time()-3600*24*15, '/');
    }

    /**
     * Check login status, and login automatically from cookie.
     * @return bool
     */
    public static function CheckLogin(){
        //检查Session
        $user = $_SESSION['user'];
        if($user)
            return true;
        //从Cookie中恢复登录
        $username = $_COOKIE['username'];
        $password = $_COOKIE['password'];
        if($username && $password){
            $model = new UserModel();
            $user = $model->queryByUsername($username);
            if(is_array($user)){
                if($password === md5($user['password'])){
                    $_SESSION['user'] = $user;
                    return true;
                }
            }
            //删除Cookie
            setcookie('username', '', time()-3600*24*15, '/');
            setcookie('password', '', time()-3600*24*15, '/');
        }
        return false;
    }

    /**
     * Get user from session
     * @return array | null
     */
    public static function GetUser(){
        if(self::CheckLogin())
            return $_SESSION['user'];
        return null;
    }

    /**
     * Test login
     * @throws CuteException
     */
    public static function TestLogin(){
        if(!self::CheckLogin())
            throw new CuteException(403, "You haven't login!");
    }

    /**
     * Check privilege
     * @param $privilege
     * @return bool
     */
    public static function CheckPrivilege($privilege){
        if(self::CheckLogin()){
            $user = $_SESSION['user'];
            $privileges = explode(',', $user['privilege']);
            foreach($privileges as $pri){
                if(trim($pri) == trim($privilege))
                    return true;
                if(trim($pri) == 'all')
                    return true;
            }
        }
        return false;
    }

    /**
     * Test privilege
     * @param $privilege
     * @throws CuteException
     */
    public static function TestPrivilege($privilege){
        if(!self::CheckPrivilege($privilege))
            throw new CuteException(403, "You are unauthorised");
    }

}