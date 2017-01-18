<?php

/**
 * Created by PhpStorm.
 * User: JE
 * Date: 2017-01-02
 * Time: 16:54
 */
class Request
{
    protected $_pathInfo;
    protected $_method;
    protected $_get;
    protected $_post;
    public function path_info(){
        return isset($this->_pathInfo)?$this->_pathInfo:$this->_pathInfo=
            isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '');
    }
    public function method(){
        return isset($this->_method)?$this->_method:$this->_method=
            $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @param string $path_info
     * @param string $method
     * @param array|null $get
     * @param array|null $post
     * 自定义request
     */
    public function custom($path_info='/',$method='get',array $get=null,array $post=null){
        $this->_pathInfo=$path_info;
        $this->_method=$method;
        $this->_get=$get;
        $this->_post=$post;
    }
    public function custom_get($get=null){
        $this->_get=$get;
    }
    /**
     * @param null $name
     * @return array
     * 获取到get数据
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
        if(is_null($this->_post)) {
            $this->_post=$_POST;
            foreach($this->_post as $k=>$v){
                $this->_post[$k]=trim($v);
            }
        }

        if(is_null($name))
            return $this->_post;
        if(is_array($name)){
            $arr=array();
            foreach($name as $k=>$v){
                if(is_int($k))
                    $arr[$v]=$this->_post[$v];
                else
                    $arr[$k]=isset($this->_post[$k])?$this->_post[$k]:$v;
            }
            return $arr;
        }

        if(is_string($name))
            return isset($this->_post[$name])?$this->_post[$name]:$default;
    }

    public function input_all(){
        return array_merge($this->input_get(),$this->input_post());
    }
}