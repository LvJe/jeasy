<?php

/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016-12-18
 * Time: 15:46
 */
class TestController extends Controller
{
    public function index(){
        /*//$m=new DB();
        try {
            $dbh = new PDO('mysql:host=localhost;port=0;dbname=hjtm', 'root', 'root', array(
                //PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,//错误处理
                //PDO::ATTR_PERSISTENT => true//持久化连接
            ));
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
            exit;
        }
        //$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//错误处理
        // 这里将导致 PDO 抛出一个 E_WARNING 级别的错误，而不是 一个异常 （当数据表不存在时）
        $res=$dbh->query("SELECT * FROM user1");
        if(FALSE === $res)
            throw new PDOException(var_dump( $dbh->errorInfo()));
        foreach ($res as $row) {
            var_dump($row);
        }
        var_dump($res->fetch());
        var_dump($res->fetch());*/
        echo $_SERVER['PHP_SELF'].'<br>';
        echo $_SERVER['SCRIPT_NAME'].'<br>';
        echo $_SERVER['SERVER_ADDR'].'<br>';
        echo $_SERVER['SERVER_NAME'].'<br>';
        echo $_SERVER['HTTP_HOST'].'<br>';
        echo $_SERVER['PATH_INFO'].'<br>';
        echo $_SERVER['ORIG_PATH_INFO'].'<br>';
        echo  $_SERVER['REQUEST_URI'] ;
        return 'test';
    }
}