<?php

/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016-12-20
 * Time: 2:07
 */

class DB1
{
    public function __construct()
    {
        echo('123');
        $dsn = 'mysql:host=localhost;port=0;dbname=hjtm';
        $user = 'root';
        $password = 'root';

        try {
            $dbh = new PDO($dsn, $user, $password, array(
                //PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,//错误处理
                //PDO::ATTR_PERSISTENT => true//持久化连接
            ));
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
            exit;
        }
        //$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//错误处理
        // 这里将导致 PDO 抛出一个 E_WARNING 级别的错误，而不是 一个异常 （当数据表不存在时）
        $res=$dbh->query("SELECT wrongcolumn FROM wrongtable");
        if(FALSE === $res)
            throw new PDOException(var_export($this->_connection->errorInfo(), true));
        echo '123123123123';
    }
}
class DB
{
    private static  $_connections = array();
    //防止克隆
    protected function __clone() {}

    public static function getConnection($instance='default'){
        if(!is_string($instance))
            throw new JException( "instance must be string",500);
        if(!isset(self::$_connections[$instance])){
            //读取数据库配置
            $config = C('database');
            if (!is_array($config[$instance]))
                throw new JException( "$instance config must be array",500);
            //连接数据库
            $db = $config[$instance];
            $type=$db['type'];
            $host= $db['host'];
            $port=$db['port'];
            $database= $db['database'];
            $username=$db['username'] ;
            $password=$db['password'];

            $dsn = "$type:host=$host;";
            if($port > 0) $dsn .= "port=$port;";
            if(isset($database)) $dsn .= "dbname=$database";

            $_con = new PDO($dsn, $username, $password,array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION//错误处理
                //PDO::ATTR_PERSISTENT => true//持久化连接
            ));
            $_con->exec("SET NAMES 'utf8'");
            self::$_connections[$instance]=$_con;
        }
        return self::$_connections[$instance];
    }
    public static function closeConnection($instance='default'){
        if((!is_string($instance))||(!isset(self::$_connections[$instance]))) return;
        self::$_connections[$instance]=null;
        unset(self::$_connections[$instance]);
    }

    /*
    //开启所有连接的事务
    public static function beginTransaction(){
        foreach(DB::$_connections as $dbh){
            $dbh->beginTransaction();
        }
    }
    //提交数据库操作
    public static function commit(){
        foreach(DB::$_connections as $dbh){
            $dbh->commit();
        }
    }
    //回退数据库操作
    public static function rollBack(){
        foreach(DB::$_connections as $dbh){
            $dbh->rollBack();
        }
    }
    */
}

