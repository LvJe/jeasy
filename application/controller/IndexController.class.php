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
        /*$arr=['99'=>'jiu jiu','er','san','4'=>'4',5=>5,'liu'=>6,'qi'=>'7','8'=>8,9,10];
        foreach($arr as $k=>$v){
            echo is_int($v)?'true_':'false_';
        }
        var_export($arr);*/
        //var_export( ObjectToArray($this));
        $param='123';
        $this->assign(compact('param'));
        return 'index';
    }
}