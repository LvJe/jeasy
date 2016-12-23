<?php

require_once ROOT_PATH."plugins/smarty/libs/Smarty.class.php";
//require_once ROOT_PATH."plugins/smarty/libs/Autoloader.php";
//Smarty_Autoloader::register();

/**
 * 处理模板文件中的内容请求
 * @param $params
 * @param $smarty
 * @return string
 */
function do_request($params, $smarty){
    $content = '';
    //extract($params);
    if(isset($params['action'])){
        $pathInfo = $params['action'];
        $method = isset($params['method']) ? $params['method'] : 'GET';
        $parameters = $params;

        unset($parameters['action']);
        unset($parameters['method']);

        $content = Cute::Handle($pathInfo, $method, $params);
    }
    return $content;
}

/**
 * 模板解析类（封装Smarty）
 * Class Template
 */
class SmartyJE extends Smarty
{
    public function __construct($path){
        parent::__construct();
        $this->compile_check = true;
        $this->force_compile = false;
        $this->caching = false;
        $this->left_delimiter = "<{";
        $this->right_delimiter = "}>";
        $this->registerPlugin("function", "request", "do_request");
        //setTemplatePath
        $this->setTemplateDir($path);
        $this->setCacheDir($path."tpl_cache");
        $this->setCompileDir($path."tpl_compile");
        $this->setConfigDir($path."tpl_config");
    }

    public function render($page){
        $content = $this->fetch($page);
        return $content;
    }
}