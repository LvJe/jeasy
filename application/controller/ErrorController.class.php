<?php

class ErrorController extends Controller
{
    public $code;
    public $error;
    public function index(){
        $error=trim($this->error);
        $code = intval($this->code);
        $this->assign($this->parameters);
        switch ($code){
            case 403:
                send_http_status(403);
                return '403';
            case 404:
                send_http_status(404);
                return '404';
            /*case 500:
                send_http_status(500);
                return '500';*/
            default:
                return 'error';
        }
    }
}