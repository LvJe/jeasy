<?php
/**
 * functions
 *
 * Copyright(C) 2016
 * Author   JE.Xie
 * Date     2016.12.18
 * Version  V1.0
 *
 * History
 *      V1.0     2016.12.18  JE.Xie  Create
 *
 */

/**
 * 页面跳转
 * @param $url
 * @param int $delay
 * @param string $msg
 */
function redirect($url, $delay=0, $msg=''){
    header("HTTP/1.1 303 See Other");
    if (0 === $delay) {
        header('Location: ' . $url);
    } else {
        header("refresh:{$delay};url={$url}");
        echo($msg);
    }
    exit();
}

/**
 * 向浏览器发送错误代码
 * @param $code
 */
function send_http_status($code) {
    static $_status = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ',  // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );

    if(array_key_exists($code,$_status)) {
        header('HTTP/1.1 '.$code.' '.$_status[$code]);
    }
}
/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
function C($name=null, $value=null,$default=null) {
    static $_config = array();
    // 无参数时获取所有
    if (empty($name)) {
        return $_config;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtoupper($name);
            if (is_null($value)) // 如果value 为空说明是设置值
                return isset($_config[$name]) ? $_config[$name] : $default;
            $_config[$name] = $value;
            return null;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);  //  这里可能是 类似于  DB.HOST 二位数组的  获取值 其实就是  $_config[DB][HOST]
        $name[0]   =  strtoupper($name[0]);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : $default;
        $_config[$name[0]][$name[1]] = $value;
        return null;
    }
    // 批量设置
    if (is_array($name)){
        $_config = array_merge($_config, array_change_key_case($name,CASE_UPPER)); // array_change_key_case 将数组 所有键转换为大写之后 再来合并
        return null;
    }
    return null; // 避免非法参数
}
/**
 * 读取或设置 全局配置项
 * @param $name
 * @param null $value
 * @return bool|null
 */
function C1($name, $value = null)
{
    static $configurations = array();
    if (is_array($name)) {
        $configurations = array_merge($configurations, $name);
    } elseif (is_null($value)) {
        return isset($configurations[$name]) ? $configurations[$name] : false;
    } else {
        $configurations[$name] = $value;
    }
    return null;
}

/**
 * 读取或设置 全局变量
 * @param $name
 * @param null $value
 * @return bool|null
 */
function V($name, $value = null)
{
    static $variables = array();
    if (is_array($name)) {
        $variables = array_merge($variables, $name);
    } elseif (is_null($value)) {
        return isset($variables[$name]) ? $variables[$name] : null;
    } else {
        $variables[$name] = $value;
    }
    return null;
}


/**
 * 打印值到缓存中，支持多参数
 * 无参数时将缓存打印到标准输出
 */
function P()
{
    static $output = '';
    if (func_num_args() > 0) {
        $args = func_get_args();
//Append to output
        foreach ($args as $arg) {
            if (is_array($arg) || is_object($arg))
                $output .= var_export($arg, true);
            else
                $output .= $arg;
        }
    } else {
//Print to stdout directly
        print($output);
    }
}


/**
 * 设置语言
 */
function InitLocale()
{
    $lan = 'en_US';

    if (isset($_REQUEST['language'])) {
        $lang = $_REQUEST['language'];
        if ($lang == 'zh_CN') {
            $lan = 'zh_CN';
        } elseif ($lang == 'zh_TW') {
            $lan = 'zh_TW';
        } elseif ($lang == 'en_US') {
            $lan = 'en_US';
        } else {
            $lan = 'en_US';
        }
        $_SESSION['language'] = $lan;
        $lan = $_REQUEST['language'];
    } elseif (isset($_SESSION['language'])) {
        $lan = $_SESSION['language'];
    } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);
        if (preg_match("/zh-c/i", $lang))
            $lan = "zh_CN";
        else if (preg_match("/zh/i", $lang))
            $lan = "zh_TW";
        else if (preg_match("/en/i", $lang))
            $lan = "en_US";
    }

    /* 将语言保存到全局中 */
    V('language', $lan);

    /* 设置语言 */
    putenv("LANG=" . $lan);
    putenv("LANGUAGE=" . $lan);
    setlocale(LC_ALL, $lan); // 指定要用的语系，如：en_US、zh_CN、zh_TW
}

/**
 * 初始化i18n
 * @param $package
 */
function InitTranslation($package)
{
    if (function_exists('gettext')) {
        /* 设置i18n路径信息 */
        bindtextdomain($package, LOCALE_PATH);
        bind_textdomain_codeset($package, 'UTF-8');
        textdomain($package);
    } else {
        include LOCALE_PATH . 'dummy.gettext.php';
        DummyGetTextLoad(V('language'), $package);
    }
}

/**
 * 将对象转化为数组
 * @param $obj
 * @return array
 */
function ObjectToArray($obj)
{
    $arr = array();
    $vars = is_object($obj) ? get_object_vars($obj) : $obj;
    foreach ($vars as $key => $value)
        $arr[$key] = (is_array($value) || is_object($value)) ? ObjectToArray($value) : $value;
    return $arr;
}

/**
 * 将对象转为JSON
 * @param $obj
 * @return string
 */
function ObjectToJSON($obj)
{
    return json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

/**
 * 将对象转为XML
 * @param $obj
 * @param null $rootName
 * @return string
 */
function ObjectToXML($obj, $rootName = null)
{
    $data = ObjectToArray($obj);
    $doc = new DOMDocument("1.0", "utf-8");
    $root = $doc->createElement($rootName ? $rootName : 'root');
    $doc->appendChild($root);

//使用递归闭包来生成XML节点
    $array2node = function (DOMElement $node, $arr) use ($doc, &$array2node) {
        foreach ($arr as $key => $value) {
            $item = $doc->createElement(is_string($key) ? $key : 'item');
            if (is_array($value)) {
                $array2node($item, $value);
            } else {
                $text = $doc->createTextNode($value);
                $item->appendChild($text);
            }
            $node->appendChild($item);
        }
    };
    $array2node($root, $data);

    return $doc->saveXML();
}


/**
 * 交换变量值
 * @param $var1
 * @param $var2
 */
function Exchange(&$var1, &$var2)
{
    $var = $var1;
    $var1 = $var2;
    $var2 = $var;
}


/**
 * 读取INI配置文件
 * @param $filename
 * @return array
 */
function LoadIni($filename)
{
    if (file_exists($filename))
        return parse_ini_file($filename, true);
    return null;
}

/**
 * 加载一个Object文件
 * @param $filename
 * @return mixed|null
 */
function LoadObject($filename)
{
    if (file_exists($filename))
        return include($filename);
    return null;
}

/**
 * 写入一个Object文件
 * @param $filename
 * @param $obj
 */
function SaveObject($filename, $obj)
{
    file_put_contents($filename, "<?php\nreturn " . var_export($obj, TRUE) . ';');
}

/**
 * 加载一个JSON文件
 * @param $filename
 * @return array
 */
function LoadJSON($filename)
{
    if (file_exists($filename)) {
        $data = file_get_contents($filename);
        return json_decode($data, TRUE);
    }
    return null;
}

/**
 * 写入一个JSON文件
 * @param $filename
 * @param $obj
 * @return int
 */
function SaveJSON($filename, $obj)
{
    return file_put_contents($filename, ObjectToJSON($obj));
}

/**
 * 保存至缓存
 * @param $name
 * @param $data
 * @param $expire
 */
function SaveCache($name, $data, $expire)
{
    $obj = [
        'time' => time(),
        'expire' => $expire,
        'data' => $data
    ];
    SaveObject(TEMP_PATH . $name, $obj);
}

/**
 * 读取缓存
 * @param $name
 * @return null
 */
function LoadCache($name)
{
    $obj = LoadObject(TEMP_PATH . $name);
    if (is_array($obj)) {
        if (isset($obj['time']) && isset($obj['expire'])
            && $obj['time'] + $obj['expire'] > time()
        )
            return $obj['data'];
        unlink(TEMP_PATH . $name);
    }
    return null;
}

/**
 * 删除缓存
 * @param $name
 * @return bool
 */
function ClearCache($name)
{
    $filename = TEMP_PATH . $name;
    if (is_file($filename))
        return unlink($filename);
    return false;
}

require_once 'assistantFun.php';