<?php
/**
 * Created by PhpStorm.
 * User: jinyi
 * Date: 2018/7/31
 * Time: 下午9:01
 */
//引用文件
require_once "Config.php";
require_once "Src/DbCore.php";
require_once "Src/PublicCore.php";

//遍历目录文件
$spiderCore = new PublicCore();
$dir = $spiderCore->print_dir('Src' . DIRECTORY_SEPARATOR . 'Spider');

//输出可以选择的爬虫
$spider = "";
foreach ($dir as $key => $value) {
    $spider .= PHP_EOL . $key . " : " . $value . PHP_EOL;
}
$dlc =PHP_EOL.
    "
    \e[33m 
                      _ooOoo_                      
                     o8888888o                     
                     88\" . \"88                     
                     (| -_- |)                     
                     O\  =  /O                     
                  ____/`---'\____                  
                .'  \\|     |//  `.                
               /  \\|||  :  |||//  \               
              /  _||||| -:- |||||_  \              
              |   | \\\  -  /'| |   |              
              | \_|  `\`---'//  |_/ |              
              \  .-\__ `-. -'__/-.  /              
            ___`. .'  /--.--\  `. .'___            
         .\"\" '<  `.___\_<|>_/___.' _> \\         
        | | :  `- \`. ;`. _/; .'/ /  .' ; |        
        \  \ `-.   \_\_`. _.'_/_/  -' _.' /        
      ===`-.`___`-.__\ \___  /__.-'_.'_.-'===      
                      `=--=-'                      
                                        
   \033[0m\ ".PHP_EOL;
$print = "
====================================================
    \033[33m PHP Images Spider \033[0m" .
        "\033[34m".$spiderCore->eol($spider)."\033[0m".
    "
    \033[33m Chenjinyi:https://github.com/Chenjinyi \033[0m
====================================================
" . PHP_EOL .
    "请输入你选择的爬虫: ";
print_r($dlc.$print);
$input = trim(fgets(STDIN));

$t1 = microtime(true);//记录运行时间

//使用爬虫
$spider_path = 'Src' . DIRECTORY_SEPARATOR . 'Spider/';
empty($dir[$input]) ? die(PHP_EOL . '参数错误') : include_once $spider_path . $dir[$input];

$t2 = microtime(true);//记录运行结束时间

print_r(PHP_EOL . '耗时' . round($t2 - $t1, 3) . "秒");//输入运行时间