<?php

/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017-01-02
 * Time: 16:54
 */
class Request
{
    protected $_pathInfo;
    protected $_get;
    protected $_post;
    protected $_method;
    public function path_info(){
        return isset($this->_pathInfo)?$this->_pathInfo:$this->_pathInfo=
            isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '');
    }
    public function method(){
        return isset($this->_method)?$this->_method:$this->_method=
            $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @param null $name
     * @return array
     *
     */
    public function input_get($name=null,$default=null){
        //TODO 统一处理，trim（，XSS防御）
        if(is_null($this->_get)) {
            $this->_get=$_GET;
            foreach($this->_get as $k=>$v){
                $this->_get[$k]=trim($v);
            }
        }

        if(is_null($name))
            return $this->_get;
        if(is_array($name)){
            $arr=array();
            foreach($name as $k=>$v){
                if(is_int($k))
                    $arr[$v]=$this->_get[$v];
                else
                    $arr[$k]=isset($this->_get[$k])?$this->_get[$k]:$v;
            }
            return $arr;
        }

        if(is_string($name))
            return isset($this->_get[$name])?$this->_get[$name]:$default;

    }
    public function input_post($name=null,$default=null){
        //TODO 统一处理，trim（，XSS防御）
        if(is_null($this->_get)) {
            $this->_get=$_GET;
            foreach($this->_get as $k=>$v){
                $this->_get[$k]=trim($v);
            }
        }

        if(is_null($name))
            return $this->_get;
        if(is_array($name)){
            $arr=array();
            foreach($name as $k=>$v){
                if(is_int($k))
                    $arr[$v]=$this->_get[$v];
                else
                    $arr[$k]=isset($this->_get[$k])?$this->_get[$k]:$v;
            }
            return $arr;
        }

        if(is_string($name))
            return isset($this->_get[$name])?$this->_get[$name]:$default;
    }

    public function input_all(){
        return array_merge($this->input_get(),$this->input_post());
    }
}