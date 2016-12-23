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
    private $_connection = null;
    private $_error = '';


    /**
     * 构造函数
     * Model constructor.
     * @param string $instance
     * @throws CuteException
     */
    public function __construct($instance = 'default'){
        if(!is_string($instance))
            throw new Exception( "instance must be string",500);
        if(isset(self::$_connections[$instance])){
            $this->_connection=self::$_connections[$instance];
            return;
        }

        //读取数据库配置
        $config = Cute::C('database');
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

        $this->_connection = new PDO($dsn, $username, $password,array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION//错误处理
            //PDO::ATTR_PERSISTENT => true//持久化连接
        ));
        $this->_connection->exec("SET NAMES 'utf8'");
        self::$_connections[$instance]=$this->_connection;
    }


    /**
     * 连接数据库，如果不指定参数，但使用配置文件中默认的数据
     * @param string $type
     * @param string $host
     * @param int    $port
     * @param string $database
     * @param string $username
     * @param string $password
     * @return bool
     */

    /**
     * 获取数据库连接
     * @return null|PDO
     */
    public function getConnection(){
        return $this->_connection;
    }

    /**
     * 查询数据库，并以数组方式返回
     * @param $sql
     * @return array|null
     * @throws Exception
     */
    protected function _query($sql){
        if(is_null($this->_connection))
            throw new Exception('Connection is null.');

        $res = $this->_connection->query($sql);
        //如果产生SQL语句错误，直接抛出
        if(FALSE === $res)
            throw new PDOException(var_export($this->_connection->errorInfo(), true));
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
        if(is_null($this->_connection))
            throw new Exception('Connection is null.');

        $ps = $this->_connection->prepare($sql);
        if(FALSE === $ps)
            throw new PDOException(var_export($this->_connection->errorInfo(), true));

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
        if(is_null($this->_connection))
            throw new Exception('Connection is null.');

        $ps = $this->_connection->prepare($sql);
        if(FALSE === $ps)
            throw new PDOException(var_export($this->_connection->errorInfo(), true));

        $ret = $ps->execute($params);
        if(FALSE === $ret)
            throw new PDOException(var_export($ps->errorInfo(), true));
        return $ps->fetchAll(PDO::FETCH_ASSOC); //不返回数字索引
    }

    /**
     * 按ID查询某表中的一个字段
     * @param $table
     * @param $field
     * @param $id
     * @return null
     * @throws Exception
     */
    protected function _queryFieldById($table, $field, $id){
        if(!is_string($table) || !is_string($field) || !is_int($id))
            throw new Exception('Parameters invalid.');

        $res = $this->_prepareQuery(
            "SELECT $field FROM $table WHERE id=:id ",
            [':id'=>$id]);

        if(count($res) > 0)
            return $res[0][$field];
        else
            return null;
    }

    /**
     * 按ID修改某表中的一个字段
     * @param $table
     * @param $field
     * @param $id
     * @param $value
     * @return bool
     * @throws Exception
     */
    protected function _updateFieldById($table, $field, $id, $value){
        if(is_null($this->_connection))
            throw new Exception('Connection is null.');

        return $this->_prepareExecute(
            "UPDATE $table SET $field=:value WHERE id=:id ",
            [":value"=>$value, ':id'=>$id]);
    }

    /**
     * 获取插入操作生成的自增ID
     * @return int|string
     * @throws Exception
     */
    protected function _lastInsertId(){
        if(is_null($this->_connection))
            throw new Exception('Connection is null.');
        return $this->_connection->lastInsertId();
    }






//这封装的一点都不好

    /**
     * 执行数据库操作，返回影响行数
     * @param $sql
     * @return int|null
     * @throws Exception
     */
    protected function _execute($sql){
        if(is_null($this->_connection))
            throw new Exception('Connection is null.');

        $num = $this->_connection->exec($sql);
        if(FALSE === $num)
            throw new PDOException(var_export($this->_connection->errorInfo(), true));
        return $num;
    }

    /**
     * 开启一个数据库事务
     * @return bool
     * @throws Exception
     */
    public function begin(){
        if(is_null($this->_connection))
            throw new Exception('Connection is null.');
        return $this->_connection->beginTransaction();
    }

    /**
     * 提交数据库操作
     * @return bool
     * @throws Exception
     */
    public function commit(){
        if(is_null($this->_connection))
            throw new Exception('Connection is null.');
        return $this->_connection->commit();
    }

    /**
     * 回退数据库操作
     * @return bool
     * @throws Exception
     */
    public function rollback(){
        if(is_null($this->_connection))
            throw new Exception('Connection is null.');
        return $this->_connection->rollBack();
    }
}

