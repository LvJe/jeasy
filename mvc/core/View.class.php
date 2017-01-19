<?php

/**
 * Class View
 *
 * Copyright 2016 JE.Xie
 * Author   JE.Xie
 * Date     2016.12.17
 * Version  V1.0
 *
 */

class View
{
    const NONE='';
    const HTML='text/html';
    const JSON='application/json';
    const TXT='text/plain';
    const XML  = 'text/xml';

    private $_model=array();
    private $_contentType=self::HTML;
    public function assign($name,$value=null){
        if(is_array($name)){
            $this->_model=array_merge($this->_model,$name);
        }else{
            $this->_model[$name]=$value;
        }
    }

    /**
     * @param $name
     * @return array|null
     */
    public function get($name){
        if($name==null){
            return $this->_model;
        }
        if(isset($this->_model[$name])){
            return $this->_model[$name];
        }
        return null;
    }

    public function contentType($contentType=null){
        if(is_null($contentType)) return $contentType;
        $this->_contentType=$contentType;
    }

    //TODO dai
    public function display($data=null){
        $content = '';
        if ($this->_contentType != self::NONE)
            header("Content-Type: $this->_contentType; charset=".CHARSET);
        switch($this->_contentType){
            case self::NONE:
            case self::TXT:
                $content = $data;
                break;
            case self::HTML:
                if(is_null(V('IsHtml')))
                    V('IsHtml', true);
                if(is_string($data)){
                    if(trim($data) != '') {
                        $template = new SmartyJE(TPL_PATH);
                        $template->assign($this->_model);
                        $template->assign('HOST_ADDRESS', HOST_ADDRESS); //主机地址
                        $template->assign('CONTEXT_PATH', CONTEXT_PATH); //项目目录
                        //TODO 多个页面共享数据的功能 暂时把user数据共享写在这里；
                        if(Authority::CheckLogin()){
                            $template->assign('user',Authority::GetUser());
                        }
                        $content = $template->render($data . TPL_EXT);
                    }
                }else{
                    $content = var_export(isset($data) ? $data : $this->_model, true);
                }
                break;
            case self::JSON:
                $content = ObjectToJSON(is_array($data) ? $data : $this->_model);
                break;
            case self::XML:
                $content = ObjectToXML(is_array($data) ? $data : $this->_model);
                break;
            default:
                $content = var_export(isset($data) ? $data : $this->_model, true);
                break;
        }
        return $content;
    }
}