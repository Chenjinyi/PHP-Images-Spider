<?php
/**
 * Created by PhpStorm.
 * User: jinyi
 * Date: 2018/7/31
 * Time: 下午8:53
 */

date_default_timezone_set('PRC'); //设置时区
//定义目录名
define("API_PATH", "Api"); //存放API Key文件夹
define("FILE_PATH", "Resource"); //存放文件文件夹名


//网站API链接
define("PIXABAY_API_URL", "https://pixabay.com/api/");

//是否记录图片链接到数据库
define("SAVE_IMAGES_URL_DATABASE", "false");

//数据库连接
define("DATABASE_URL", "localhost");
define("DATABASE_USERNAME", "root");
define("DATABASE_PASSWORD", "");
define("DATABASE_DATABASE", "images");

define("DATE_FORMAT", "n-d");
//随机搜索关键词
define("RAND_KEYWORD", array(
    'Coffee',
    'OverWatch',
    'Magic',
    'Red',
    '天使',
    'mercy',
    'Franary',
    "cos",
    "lolita",
    '次元',
    'tea',
    'women',
    'jk',
    'game',
));
//artatstion执行一次循环，睡一会觉觉（误）
define("ARTSTATION_SLEEP","true");
define("ARTSTATION_SLEEP_TIME","20");
//bilibili执行一次循环， 就-1s
define("BILIBILI_SLEEP","true");
define("BILIBILI_SLEEP_TIME","10");



//数据库链接
define("DB_MS",'mysql');//数据库类型
define("DB_USERNAME","root");//数据库用户名
define("DB_PASSWORD","");//数据库密码
define("DB_ADDRESS","127.0.0.1");//数据库地址
define("DB_NAME","images");//数据库民