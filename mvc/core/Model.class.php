<?php

/**
 *
 * JEasy
 * https://github.com/LvJe/jeasy
 * Created by PhpStorm.
 * Author  JE.Xie
 * Date: 2016-12-23
 * Time: 15:52
 *
 * History:
 *
 * 2017-01-23 description:
     * 该类主要为模型类提供一套通用的增删改查方法，来提高开发效率，用户也可以根据实际
     * 需求自行拓展功能。
     * 所有的Model模型都继承此类，继承此类的模型类命名示例：UsersModel
     * class UsersModel extends Model{
     *      protected $table='user';
     * }
     * 默认情况下会根据UsersModel这个模型类，$table变量自动获取不区分大小写的'users',
     * 也可以根据实际需求自行给$table赋值，跟换绑定的数据库表格。
     *
     * 在使用通用方法的时候，默认认为表格有一个自增字段id，created_at,updated_at,valid这四个通用字段。
 *
 *
 * 2017-01-23
 *      增加通用的插入方法public function create_c()
 */
class Model
{
    /**
     * 按ID查询某表中的一个字段
     * @param $table
     * @param $field
     * @param $id
     * @return null
     * @throws JException
     */

    protected $_conName;
    protected $table;
    public function __construct($conName = 'default')
    {

        $this->_conName=$conName;
        $className=get_called_class(); // 或者get_class($this);
        $this->table = strtolower(substr($className,0,strlen($className)-5));
    }

    /**
     * 执行数据库操作，返回影响行数
     * @param $sql
     * @return int|null
     * @throws PDOException
     */
    protected function _execute($sql){
        $dbh=DB::getConnection($this->_conName);

        $num = $dbh->exec($sql);
        /*if(FALSE === $num)
            throw new PDOException(var_export($dbh->errorInfo(), true));*/
        return $num;
    }

    /**
     * 查询数据库，并以数组方式返回
     * @param $sql
     * @return array|null
     * @throws PDOException
     */
    protected function _query($sql){
        $dbh=DB::getConnection($this->_conName);

        $res = $dbh->query($sql);
        //如果产生SQL语句错误，直接抛出
        /*if(FALSE === $res)
            throw new PDOException(var_export($dbh->errorInfo(), true));*/
        return $res->fetchAll(PDO::FETCH_ASSOC); //不返回数字索引
    }

    /**
     * 执行数据库预操作，返回执行结果
     * @param $sql
     * @param array|null $params
     * @return mixed
     * @throws PDOException
     */
    protected function _prepareExecute($sql, array $params=null,$multi=false){
        $dbh=DB::getConnection($this->_conName);

        $ps = $dbh->prepare($sql);
        /*if(FALSE === $ps)
            throw new PDOException(var_export($dbh->errorInfo(), true));*/
        if(!$multi) {
            $ret = $ps->execute($params);
        }else{
            $ret=array();
            foreach($params as $param){
                $ret[] = $ps->execute($param);
            }
        }
        /*if(FALSE === $ret)
            throw new PDOException(var_export($ps->errorInfo(), true));*/
        return $ret;
    }

    /**
     * 查询数据库，返回查询结果
     * @param $sql
     * @param array|null $params
     * @return mixed
     * @throws PDOException
     */
    protected function _prepareQuery($sql, array $params=null){
        $dbh=DB::getConnection($this->_conName);
        $ps = $dbh->prepare($sql);
        /*if(FALSE === $ps)
            throw new PDOException(var_export($dbh->errorInfo(), true));*/

        $ret = $ps->execute($params);
        /*if(FALSE === $ret)
            throw new PDOException(var_export($ps->errorInfo(), true));*/
        return $ps->fetchAll(PDO::FETCH_ASSOC); //不返回数字索引
    }

    /**
     * 获取插入操作生成的自增ID
     * @return int|string
     * @throws PDOException
     */
    protected function _lastInsertId(){
        $dbh=DB::getConnection($this->_conName);
        return $dbh->lastInsertId();
    }


    /**
     * 开启一个数据库事务
     * @return bool
     * @throws Exception
     */
    public function beginTransaction(){
        $dbh=DB::getConnection($this->_conName);
        return $dbh->beginTransaction();
    }

    /**
     * 提交数据库操作
     * @return bool
     * @throws PDOException
     */
    public function commit(){
        $dbh=DB::getConnection($this->_conName);
        return $dbh->commit();
    }

    /**
     * 回退数据库操作
     * @return bool
     * @throws PDOException
     */
    public function rollBack(){
        $dbh=DB::getConnection($this->_conName);
        return $dbh->rollBack();
    }


    /*************- 以下 -**************/


    public function count(){
        $res=$this->_prepareQuery("SELECT count(*) FROM ". $this->table." WHERE valid=1");
        return $res[0]['count(*)'];
    }

    public function listAll($offset, $length){
        return $this->_prepareQuery("SELECT * FROM ". $this->table." WHERE valid=1 LIMIT $offset, $length ");
    }

    public function queryFirstBy($field,$value){
        if(!is_string($field)) throw new JException('Parameters invalid.');
        $res = $this->_prepareQuery(
            "SELECT * FROM  ". $this->table."  WHERE ". $field ."=:val  LIMIT 0, 1 ",
            [':val'=>$value]);
        if(count($res) > 0)
            return $res[0];
        return null;
    }

    /**
     * @param array $arr
     * @return int|string
     * 如果懒得为每个模型单独编写插入函数可调用此方法，缺点就是:
     * 每次都需要知道插入数据时需要哪些字段,同时牺牲一点点性能。
     *
     * TODO 17-01-23: 考虑把关于字段的信息，交由子类去控制，让用户去自己配置字段信息:protected $fileds=['name','content'];
     */
    public function create_c(array $arr){
        $insert_field='';
        $values_str='';
        $insert_arr=array();
        foreach($arr as $k=>$v){
            $insert_field.=$k.',';
            $values_str.=':'.$k.',';
            $insert_arr[':'.$k]=$v;
        }
        $this->_prepareExecute(
            "INSERT INTO ".$this->table." (".$insert_field."created_at,updated_at,valid) "
            ."VALUES ( ".$values_str."now(),now(),1) ",
            $insert_arr
        );
        return $this->_lastInsertId();
    }



    //可是基本没用过这个函数暂且先留着
    protected function _queryFieldById($table, $field, $id){
        if(!is_string($table) || !is_string($field) || !is_int($id))
            throw new JException('Parameters invalid.');

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
        return $this->_prepareExecute(
            "UPDATE $table SET $field=:value WHERE id=:id ",
            [":value"=>$value, ':id'=>$id]);
    }
}