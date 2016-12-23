<?php

class ErrorController extends Controller
{
    public $code;

    public function show(){
        $this->assign($this->parameters);

        $code = intval($this->code);
        switch ($code){
            case 403:
                send_http_status(403);
                return '403';
            case 404:
                send_http_status(404);
                return '404';
            case 500:
                send_http_status(500);
                return '500';
            default:
                return 'error';
        }
    }
}