<?php

abstract class Controller
{
    protected $_forward = null;

    protected $_view = null;
    protected $_page = null;

    public $controller = '';
    public $parameters = array();

    public function __construct(){
        $this->_view = new View();
    }

    /**
     * 执行请求
     * @param $action
     * @param $method
     * @param array $params
     * @throws JException
     */
    public function doAction($action, $method, $params=[]){
        $method = strtolower($method);

        $func = '';
        //默认处理
        if(method_exists($this, $action)) {
            $func = $action;
        } elseif(method_exists($this, $action.'_'.$method)) {
            $func = $action.'_'.$method;
        } else {
            //找不到Action
            throw new JException( 'Can not find action：'.$action,500);
        }

        //参数复制
        $this->parameters = $params;
        //将参数赋值给对应的成员变量
        foreach($params as $k=>$v){
            //排除以下划开始的内置成员变量
            if(is_string($k) && strlen($k)>0 && substr($k, 0, 1) != '_')
                $this->{$k} = $v;
        }

        //调用子类中的预处理
        if(method_exists($this, '_initialize'))
            call_user_func(array($this, '_initialize'));

        //调用子类中自定义的action处理
        $this->_page = call_user_func(array($this, $func));

        //调用子类中的后处理
        if(method_exists($this, '_finalize'))
            call_user_func(array($this, '_finalize'));
    }

    /**
     * 显示内容
     * @return string
     */
    public function doDisplay(){
        return $this->_view->display($this->_page);
    }

    /**
     * 向View中传递值
     * @param string|array $name
     * @param string $value
     */
    protected function assign($name, $value=''){
        if(is_array($name)){
            $arr=array();
            foreach($name as $k=>$v){
                if(is_int($k)){
                    if(is_string($v)) $arr[$v]=$this->{$v};
                }else
                    $arr[$k]=$v;
            }
            $this->_view->assign($arr, $value);
        }else{
            $this->_view->assign($name, $value);
        }
    }

    /**
     * 读取或设置页面ContentType
     * @param null $type
     * @return mixed
     */
    public function contentType($type=null){
        return $this->_view->contentType($type);
    }

    /**
     * 页面跳转
     * @param $url
     */
    protected function redirect($url){
        header("HTTP/1.1 303 See Other");
        header('Location: '. $url);
        exit;
    }

    /**
     * 内部跳转，无需转向浏览器
     * @param null $url
     * @param string $method
     * @param $params
     * @return null
     */
    public function forward($url=null, $method='GET', $params=[]){
        if(is_null($url))
            return $this->_forward;
        $this->_forward = array(
            'url'=>$url,
            'method'=>$method,
            'params'=>$params
        );
    }

    /**
     * 设置超时秒数，系统默认30秒，执行长任务时，需要设置<br>
     * 0代表不超时
     * @param int $sec
     */
    public function setTimeout($sec = 0){
        set_time_limit($sec);
    }

}