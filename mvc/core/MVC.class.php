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
    public static function  handle($pathInfo, $method, $params=[]){
        $router=new Router($pathInfo,$method,$params);
        $controller=$router->controller;
        $action=$router->action;
        $method=$router->method;
        $params=$router->parameters;//array数组是引用，貌似这里没必要写 -xhj

        $controller->doAction($action, $method, $params);

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