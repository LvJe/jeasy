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

class Authority
{
    /**
     * Encrypt password (use MD5)
     * @param $password
     * @return string
     */
    public static function EncryptPassword($password,$salt=''){
        $encrypt = false;//如果没填写配置，默认为false；
        $secure = C('secure');
        if(is_array($secure) && isset($secure['encryptPassword'])){
            $encrypt = $secure['encryptPassword'];
        }
        if($encrypt){
            if(empty($salt)) $salt=getRandomString(6);
            $password = md5($salt.$password);
        }
        return array('password'=>$password,'salt'=>$salt);
    }

    /**
     * Login operator
     * @param $username
     * @param $password
     * @param bool $remember
     * @return bool
     * 将user信息存入session，如果记住密码则把username和password存入cookie
     */
    public static function Login($username, $password, $remember=false){
        //暂时提到前面调用 观察会不会出错。为了解决的bug：登录第一个用户记住账号写入cookie，登录第二个账户不记住，第一个账户cookie仍然存在。17-01-19
        self::Logout();

        $model = new UsersModel();
        $user = $model->queryFirstBy('username',$username);
        if(is_array($user)){
            $salt=$user['salt'];

            //兼容不开启安全模式时注册的用户
            $password = empty($salt)?$password:self::EncryptPassword($password,$salt)['password'];

            if($password === $user['password']){
                $_SESSION['user'] = $user;
                if($remember){
                    setcookie(COOKIE_PREFIX.'username', $username, time()+3600*24*15, '/'); //15天
                    setcookie(COOKIE_PREFIX.'password', md5($password.$salt), time()+3600*24*15, '/');
                }
                return true;
            }
        }
        //删除Cookie(已经提到函数开头17-01-19)
        //setcookie(COOKIE_PREFIX.'username', '', time()-3600*24*15, '/');
        //setcookie(COOKIE_PREFIX.'password', '', time()-3600*24*15, '/');
        //self::Logout();
        return false;
    }

    /**
     * Logout operator
     */
    public static function Logout(){
        unset($_SESSION['user']);
        setcookie(COOKIE_PREFIX.'username', '', time()-3600*24*15, '/');
        setcookie(COOKIE_PREFIX.'password', '', time()-3600*24*15, '/');
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
        $username = $_COOKIE[COOKIE_PREFIX.'username'];
        $password = $_COOKIE[COOKIE_PREFIX.'password'];
        if($username && $password){
            $model = new UsersModel();
            $user = $model->queryFirstBy('username',$username);
            if(is_array($user)){
                if($password === md5($user['password'].$user['salt'])){
                    $_SESSION['user'] = $user;
                    return true;
                }
            }
            //删除Cookie
            //setcookie(COOKIE_PREFIX.'username', '', time()-3600*24*15, '/');
            //setcookie(COOKIE_PREFIX.'password', '', time()-3600*24*15, '/');
            self::Logout();
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
            throw new JException("You haven't login!",403);
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
            throw new JException( "You are unauthorised",403);
    }

    // 获得表单校验散列
    public static function CSRF_Token()
    {
        if (self::CheckLogin())
            return substr(md5(SITE_NAME . $_SESSION['user']['password'].$_SESSION['user']['salt']. SALT), 8, 8);
        else
            return substr(md5(SITE_NAME . SALT), 8, 8);
    }
    //来源检查
    public static function CheckRefer($csrf_token)
    {
        if (empty($_SERVER['HTTP_REFERER'] || $csrf_token != self::CSRF_Token() || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) !== preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])))
            return false;
        else
            return true;
    }
}