<?php
/**
 * Created by PhpStorm.
 * User: jinyi
 * Date: 2018/7/31
 * Time: 下午9:01
 */

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
$dir=print_dir('src');

$spider="";
foreach ($dir as $path ){

}

$print ="
=============================
PHP Images Spider
"
    .$spider.
    "
Chenjinyi:https://github.com/Chenjinyi
=============================
";