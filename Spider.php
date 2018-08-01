<?php
/**
 * Created by PhpStorm.
 * User: jinyi
 * Date: 2018/7/31
 * Time: 下午9:01
 */

require_once "src/PublicCore.php";
require_once "Config.php";

//遍历目录文件
function print_dir($dir_path)
{
    $files = array();
    if (@$handle = opendir($dir_path)) { //注意这里要加一个@，不然会有warning错误提示：）
        while (($file = readdir($handle)) !== false) {
            if ($file != ".." && $file != ".") { //排除根目录；
                $files[] = $file;
            }
        }
        closedir($handle);
        return $files;
    }
}

$dir = print_dir('src' . DIRECTORY_SEPARATOR . 'spider');


//输出可以选择的爬虫
$spider = "";
foreach ($dir as $key => $value) {
    $spider .= PHP_EOL . $key . " : " . $value . PHP_EOL;
}
$print = "
=============================
PHP Images Spider".
PHP_EOL
. $spider .
PHP_EOL.
"Chenjinyi:https://github.com/Chenjinyi
=============================
" . PHP_EOL . "请输入你选择的爬虫:";
print_r($print);
$input = trim(fgets(STDIN));


//使用爬虫
$spider_path = 'src' . DIRECTORY_SEPARATOR . 'spider/';
empty($dir[$input]) ? die('参数错误') : include_once $spider_path . $dir[$input];
