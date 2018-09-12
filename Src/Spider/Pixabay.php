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
$user_agent = array(
    "Host:pixabay.com",
    "Connection: keep-alive",
    "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
    "Upgrade-Insecure-Requests: 1",
    "DNT:1",
    "Accept-Language:zh-CN,zh;q=0.8",
    "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.101 Safari/537.36"
);;

//获取数据
$key = $spiderCore->check_api_file('PixabayApiKey') ?: die("PixabayKey为空" . PHP_EOL . "请在/API文件夹里输入你的API KEY");//获取用户Key
$q = $spiderCore->user_input("请输入一个需要查询的字符串(不输入就随缘了):", RAND_KEYWORD[mt_rand(0, count(RAND_KEYWORD) - 1)]); //获取查询内容
$per_page = $spiderCore->user_input($spiderCore->eol("每次最多尝试下载200张") . "尝试爬取的图片数量（每页图片张数)" . $spiderCore->eol("最终下载图片数量=(图片张数*多次执行的图片页数)") . "请输一页的图片数量3~200（默认为30）:", 30); //获取查询内容
$page = $spiderCore->user_input(PHP_EOL . "请输入获取的图片页数（默认为1）:", 1); //获取查询内容

$result = json_decode($result = $spiderCore->curl_get(PIXABAY_API_URL . "?key=" . $key . "&q=" . $q . "&per_page=" . $per_page . "&page=" . $page, $user_agent));//通过Api得到数据
$images_arr = [];
//获取图片下载链接以及名字
foreach ($result->hits as $images) {
    $format = explode('.', $images->largeImageURL);
    array_push($images_arr, ["pixabay-" . $images->id . "." . $format['2'] => $images->largeImageURL]);
}
$spiderCore->quick_down_img("pixabay-" . $q, $images_arr, "Pixabay");