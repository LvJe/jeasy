<?php
/**
 * Class Router
 *
 * Copyright 2016 JE.Xie
 * Author   JE.Xie
 * Date     2016.12.17
 * Version  V1.0
 *
 */


class Router
{
    public static $defaultControllerName='index';
    public static $defaultAction='index';

    public $controller;
    public $action;
    public $pathInfo = '/';

    public $method;
    public $parameters=array();

    //public $controllerName;
    //public $controllerPath;

    /**
     * Engine constructor.
     */
    public function __construct($pathInfo='/',$method='GET',$parameters=array()){
        if(is_string($pathInfo)&&trim($pathInfo)!='') $this->pathInfo=$pathInfo;
        if(is_string($method)&&trim($method)!='') $this->method=strtoupper($method);
        if(is_array($parameters)) $this->parameters=array_merge($this->parameters,$parameters);

        $this->_route();
    }

    protected function _route(){
        $path_arr=explode('/',$this->pathInfo);
        //var_dump($path_info);  //输出：['','news']
        /* 所以应该去掉第一个空白 */
        array_splice($path_arr,0,1);
        //当$path_arr为空时，赋第一个值为空,从而在下面解析的时候会创建默认的Controller
        if(empty($path_arr)) $path_arr[]='';

         //当前controller路径
        $controllerPath = '';

        //当前controller
        //$controllerName='';

        //路径参数KEY
        $param_key = null;

        /* 解析PATH_INFO */
        $path_arr_len=count($path_arr);
        for($i=0;$i<$path_arr_len;$i++){
            $str=trim($path_arr[$i]);

            //首先创建controller
            if(is_null($this->controller)){
                //确定controllerName和controllerPath
                if($str==''){
                    $controllerName=self::$defaultControllerName;
                } else{
                    /* 解析Controller */
                    $str = strtolower($str); //转小写处理,路径约定为小写
                    //用于支持多级Controller
                    //查看是否存在目录，如果存在则进入下一级
                    if(is_dir(CTRL_PATH.$controllerPath.$str)){
                        $controllerPath=$controllerPath.$str.DIRECTORY_SEPARATOR;
                        //子目录默认页
                        if($i===$path_arr_len-1){
                            $path_arr[]='';
                            $path_arr_len++;
                        }
                        continue;
                    }
                    $controllerName=$str;
                }
                //根据$controllerName和$controllerPath创建controller
                $controllerClass=$this->getControllerClass($controllerName);
                $controllerFile=CTRL_PATH.$controllerPath.$controllerClass.CLASS_EXT;
                $this->controller=$this->createController($controllerClass,$controllerFile);

                //TODO::如果是多级目录，将Control目录以及对应的Model目录添加至自动加载中
                /*if($controllerPath != '') {
                    cute_classpath(CTRL_PATH . $controllerPath);
                    if (is_dir(MODEL_PATH . $controllerPath))
                        cute_classpath(MODEL_PATH . $controllerPath);
                }*/
            }elseif(is_null($this->action)){
                $this->action=strtolower($str);
            }else{
                //解析路径参数
                if(is_null($param_key)){
                    if($str=='') continue;
                    $param_key=strtolower($str);
                }else{
                    $this->parameters[$param_key]=$str;
                    $param_key=null;
                }
            }
        }
        //如果没有找到action, 则使用默认action
        if(is_null($this->action)||$this->action=='')
            $this->action=self::$defaultAction;
        /* 解析GET POST参数 */
        $this->parameters = array_merge( $_GET, $_POST,$this->parameters);
    }

    /**
     * 类名风格转换，test_abc -> TestAbc
     * @param $str
     * @return string
     */
    protected function getClassName($str){
        $name = '';
        foreach(explode('_', $str) as $s)
            $name .= ucfirst($s);
        return $name;
    }
    protected function getControllerClass($str){
        return $this->getClassName($str).'Controller';
    }

    /**
     * @param $className
     * @param $classFile
     * @return instance of Controller
     * @throws Exception
     */
    protected function createController($className,$classFile){
        if(file_exists($classFile)){
            //引入Controller类文件
            include_once($classFile);
            if(class_exists($className,false)){
                $controller=new $className();
                //如果不是Controller的子类，则抛出异常
                if(!($controller instanceof Controller)){
                    throw new Exception('Specific Controller is not a really Controller.',500);
                }
                //暂时去掉 $controller->controller = $className; //指定Controller的名称
            }else {
                throw new Exception("Create instance of $className failed.",500);
            }
            return $controller;
        }else{
            throw new Exception("Cannot find specific Controller",404);
        }
    }
}