<?php

/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016-12-18
 * Time: 15:46
 */
class IndexController extends Controller
{
    public $param='123';
    public function index(){
        $param='123';
        $this->assign(compact('param'));
        return 'index';
    }
}