<?php
/**
 * Created by PhpStorm.
 * User: jinyi
 * Date: 2018/7/30
 * Time: 下午1:10
 */

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
//获取数据
$spiderCore->init_dir();
$key = $spiderCore->check_api_file('PixabayApiKey') ?: die("PixabayKey为空");
$q = $spiderCore->user_input("请输入一个需要查询的字符串");
$result = json_decode($result = $spiderCore->curl_get(PIXABAY_API_URL."?key=".$key."&q=".$q,$user_agent));
$images_arr=[];
foreach ($result['hits'] as $images){
    $format=explode('.',$images['largeImageURL']);
    array_push($images_arr,[$images['id'].$format['1']=>$images['largeImageURL']]);
}

