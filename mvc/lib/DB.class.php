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
    //private $_connection = null;
    private $_conName;

    /**
     * 构造函数
     * Model constructor.
     * @param string $instance
     * @throws Exception
     */
    public static function getConnection($instance='default'){
        if(!is_string($instance))
            throw new Exception( "instance must be string",500);
        if(!isset(self::$_connections[$instance])){
            //读取数据库配置
            $config = C('database');
            if (!is_array($config[$instance]))
                throw new Exception( "$instance config must be array",500);
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

    public function __construct($instance = 'default'){
        $dbh=self::getConnection('default');
        $this->_conName=$instance;
    }

    /**
     * 防止克隆
     *
     */
    protected function __clone() {}
    /**
     * 执行数据库操作，返回影响行数
     * @param $sql
     * @return int|null
     * @throws Exception
     */
    protected function _execute($sql){
        $dbh=self::getConnection($this->_conName);
        $num = $dbh->exec($sql);
        if(FALSE === $num)
            throw new PDOException(var_export($dbh->errorInfo(), true));
        return $num;
    }

    /**
     * 查询数据库，并以数组方式返回
     * @param $sql
     * @return array|null
     * @throws Exception
     */
    protected function _query($sql){
        $dbh=self::getConnection($this->_conName);

        $res = $dbh->query($sql);
        //如果产生SQL语句错误，直接抛出
        if(FALSE === $res)
            throw new PDOException(var_export($dbh->errorInfo(), true));
        return $res->fetchAll(PDO::FETCH_ASSOC); //不返回数字索引
    }

    /**
     * 执行数据库预操作，返回执行结果
     * @param $sql
     * @param array|null $params
     * @return mixed
     * @throws Exception
     */
    protected function _prepareExecute($sql, array $params=null){
        $dbh=self::getConnection($this->_conName);
        $ps = $dbh->prepare($sql);
        if(FALSE === $ps)
            throw new PDOException(var_export($dbh->errorInfo(), true));

        $ret = $ps->execute($params);
        if(FALSE === $ret)
            throw new PDOException(var_export($ps->errorInfo(), true));
        return $ret;
    }

    /**
     * 查询数据库，返回查询结果
     * @param $sql
     * @param array|null $params
     * @return mixed
     * @throws Exception
     */
    protected function _prepareQuery($sql, array $params=null){
        $dbh=self::getConnection($this->_conName);
        $ps = $dbh->prepare($sql);
        if(FALSE === $ps)
            throw new PDOException(var_export($dbh->errorInfo(), true));

        $ret = $ps->execute($params);
        if(FALSE === $ret)
            throw new PDOException(var_export($ps->errorInfo(), true));
        return $ps->fetchAll(PDO::FETCH_ASSOC); //不返回数字索引
    }

    /**
     * 获取插入操作生成的自增ID
     * @return int|string
     * @throws Exception
     */
    protected function _lastInsertId(){
        $dbh=self::getConnection($this->_conName);
        return $dbh->lastInsertId();
    }


    /**
     * 开启一个数据库事务
     * @return bool
     * @throws Exception
     */
    public function begin(){
        $dbh=self::getConnection($this->_conName);
        return $dbh->beginTransaction();
    }

    /**
     * 提交数据库操作
     * @return bool
     * @throws Exception
     */
    public function commit(){
        $dbh=self::getConnection($this->_conName);
        return $dbh>commit();
    }

    /**
     * 回退数据库操作
     * @return bool
     * @throws Exception
     */
    public function rollback(){
        $dbh=self::getConnection($this->_conName);
        return $dbh->rollBack();
    }
}

