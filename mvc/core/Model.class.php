<?php

/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016-12-23
 * Time: 15:52
 */
class Model extends DB
{
    protected $table;
    /**
     * 按ID查询某表中的一个字段
     * @param $table
     * @param $field
     * @param $id
     * @return null
     * @throws JException
     */

    public function __construct($instance = 'default')
    {

        parent::__construct($instance);
        $className=get_called_class(); // 或者get_class($this);
        $this->table = strtolower(substr($className,0,strlen($className)-5));
    }

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

    public function count(){
        $res=$this->_prepareQuery("SELECT count(*) FROM $this->table WHERE valid=1");
        return $res[0]['count(*)'];
    }

    public function listAll($offset, $length){
        return $this->_prepareQuery("SELECT * FROM user WHERE valid=1 LIMIT $offset, $length ");
    }

}