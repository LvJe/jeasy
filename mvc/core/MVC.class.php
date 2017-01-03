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
    public static function  handle(Request $request=null){
        if(isset($request)){
            self::$request=$request;
        }else{
            self::$request=new Request();
        }
        $router=new Router(self::$request->path_info());

        $controller=$router->controller;
        $action=$router->action;
        $params=$router->parameters;


        $controller->doAction($action, self::$request->method(), array_merge($params,self::$request->input_get(),self::$request->input_post()));
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