<?php

/**
 * Class MVC
 *
 * Copyright 2016 JE.Xie
 * Author   JE.Xie
 * Date     2016.12.17
 * Version  V1.0
 *
 */
class MVC
{
    public static $request;

    /**
     * @param Request|null $request
     * @return mixed
     *
     * 可以传入外部自定义的request;
     * 也可以传入null内部会new一个新的Request实例;
     */

    //TODO 传入数组，支持内部自定义request
    public static function  handle(Request $request=null){
        if(isset($request)){
            self::$request=$request;
        }else{
            self::$request=isset(self::$request)?self::$request:new Request();
        }
        $router=new Router(self::$request->path_info());

        $controller=$router->controller;
        $action=$router->action;
        $path_params=$router->parameters;

        $_get=self::$request->input_get();
        self::$request->custom_get(array_merge($path_params,$_get));
        $controller->doAction($action,self::$request);
       // var_export($request);
      //  exit;
        /*$forward = $this->controller->forward();
        //如果在执行Action过程中进行页面跳转，则无需调用View显示
        if(is_array($forward)){
            $pathInfo = $forward['url'];
            $method = $forward['method'];
            if(isset($forward['params']))
                $params = array_merge($params, $forward['params']);
            //使用新的路由
            $router = new Engine();
            return $router->handle($pathInfo, $method, $params);
        }*/

        //调用View显示
        return $controller->doDisplay();
    }
}