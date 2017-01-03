<?php
/**
 * Created by JE.Xie.
 * Date: 2016-12-17
 * Time: 17:12
 */
define('J_EASY','1.0.0_alpha');

defined('MVC_PATH')  or define('MVC_PATH',    ROOT_PATH.'mvc'.DIRECTORY_SEPARATOR);
defined('APP_PATH')   or define('APP_PATH',     ROOT_PATH.'application'.DIRECTORY_SEPARATOR);
defined('TPL_PATH')   or define('TPL_PATH',     ROOT_PATH.'templates'.DIRECTORY_SEPARATOR);
defined('CTRL_PATH')  or define('CTRL_PATH',   APP_PATH.'controller'.DIRECTORY_SEPARATOR);
defined('MODEL_PATH') or define('MODEL_PATH',  APP_PATH.'model'.DIRECTORY_SEPARATOR);

defined('ATTACH_PATH')   or define('ATTACH_PATH',     ROOT_PATH.'attach'.DIRECTORY_SEPARATOR);
//defined('LOCALE_PATH')   or define('LOCALE_PATH',     ROOT_PATH.'locale'.DIRECTORY_SEPARATOR);
defined('TEMP_PATH')   or define('TEMP_PATH',     ROOT_PATH.'temp'.DIRECTORY_SEPARATOR);

/* FILE_EXT */
defined('TPL_EXT') or define('TPL_EXT',  '.tpl');
defined('CLASS_EXT') or define('CLASS_EXT',  '.class.php');

/* 字符编码*/
defined('CHARSET') or define('CHARSET',  'utf-8');

/* 默认开启调试模式 */
defined('DEBUG_MODE') or define('DEBUG_MODE',  TRUE);

/* 解析主机地址 和 项目目录 */
define('HOST_ADDRESS', (isset($_SERVER['HTTPS']) ? 'https://':'http://') . $_SERVER['HTTP_HOST']);
define('CONTEXT_PATH', substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'))); //即index.php的路径

require_once "functions.php";
require_once 'autoLoader.php';

/* 开启SESSION */
session_start();

/* 设置时区 为 中国 */
date_default_timezone_set('PRC');

/* 读入配置文件 */
$config = LoadIni(MVC_PATH.'config/cute.ini');
if(is_array($config))
    C($config);
/* 读入数据库配置文件 */
$db_config = LoadIni(MVC_PATH.'config/database.ini');
if(is_array($db_config))
    C('database', $db_config);

/* 初始化多语言环境 */
/*InitLocale();
InitTranslation('templates');

V('languages', array('en_US'=>_('English'), 'zh_CN'=>_('Chinese Simple')));*/

/* 解析HTTP请求 */
$pathInfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '');
$method = $_SERVER['REQUEST_METHOD'];
$params = array_merge($_GET, $_POST);
/* 处理请求 */
$request=new Request();
$response = MVC::Handle($request);


/*记录访问量*/
/*if(is_null($_SESSION['visit'])){
    $_SESSION['visit']=1;
    $model=new VisitModel();
    $model->visit();
}*/

/* 打印结果 */

print($response);

/* 打印缓存 */
P();


