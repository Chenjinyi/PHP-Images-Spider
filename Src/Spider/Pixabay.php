<?php
/**
 * Created by PhpStorm.
 * User: jinyi
 * Date: 2018/7/30
 * Time: 下午1:10
 */

//v0.1
class Pixabay
{

}
//user-agent
$user_agent=array(
    "Host:pixabay.com",
    "Connection: keep-alive",
    "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
    "Upgrade-Insecure-Requests: 1",
    "DNT:1",
    "Accept-Language:zh-CN,zh;q=0.8",
    "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.101 Safari/537.36"
);

;
//获取数据
$spiderCore->init_dir();
$key = $spiderCore->check_api_file('PixabayApiKey') ?: die("PixabayKey为空");//获取用户Key
$q = $spiderCore->user_input("请输入一个需要查询的字符串(不输入就随缘了):",RAND_KEYWORD[mt_rand(0,count(RAND_KEYWORD)-1)]); //获取查询内容
print_r($q.PHP_EOL);//输出刚刚选择的
$result = json_decode($result = $spiderCore->curl_get(PIXABAY_API_URL."?key=".$key."&q=".$q,$user_agent));//通过Api得到数据
$images_arr=[];
foreach ($result->hits as $images){
    $format=explode('.',$images->largeImageURL);
    array_push($images_arr,["pixabay-".$images->id.".".$format['2']=>$images->largeImageURL]);
}
$dir_path =$spiderCore->new_dir_name("pixabay-".$q);//生成保存路径
$spiderCore->image_save($images_arr,$dir_path);//下载图片
print_r("成功下载:".$spiderCore->images_number($dir_path)."张图片");