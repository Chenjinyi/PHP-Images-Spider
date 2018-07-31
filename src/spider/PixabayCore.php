<?php
/**
 * Created by PhpStorm.
 * User: jinyi
 * Date: 2018/7/30
 * Time: 下午1:10
 */

require_once "../PublicCore.php";

class PixabayCore
{

}

$spiderCore = new PublicCore();
$spiderCore->init_dir();
$key = $spiderCore->check_api_file('PixabayApiKey') ?: die("PixabayKey为空");
$q = $spiderCore->user_input("请输入一个需要查询的字符串");
$result = json_decode($result = $spiderCore->curl_get(PIXABAY_API_URL."?key=".$key."&q=".$q));
$images_arr=[];
foreach ($result['hits'] as $images){
    $format=explode('.',$images['largeImageURL']);
    array_push($images_arr,[$images['id'].$format['1']=>$images['largeImageURL']]);
}

