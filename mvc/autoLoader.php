<?php
/*
 *
 */
/* 重载autoload函数，以实现多目录自动加载 */
function jeAutoload($className){
    $directories = explode(PATH_SEPARATOR, get_include_path()) ;
    $fileName = $className.CLASS_EXT;
    foreach($directories as $dir){
        $filePath = $dir.DIRECTORY_SEPARATOR.$fileName;
        if( file_exists($filePath) ) {
            include_once "$className.class.php";
            break;
        }
    }
}
spl_autoload_register("jeAutoload");

/* 向自动加载目录列表中添加目录 */
function AddIncludePath($path){
    //使用冒号 或 分号 进行目录间隔
    set_include_path(get_include_path().PATH_SEPARATOR.$path);
}


/* 添加自动加载路径 */
AddIncludePath(MVC_PATH.'core');
AddIncludePath(MVC_PATH.'lib');
//cute_classpath(APP_PATH.'controller');
AddIncludePath(APP_PATH.'model');
AddIncludePath(ROOT_PATH.'libraries');
