<?php
/**
 * Copy Chenjinyi/Image-Spider src/bingspider.php
 * chenjinyi666@gmail.com
 * Created by PhpStorm.
 * User: jinyi
 * Date: 2018/8/1
 * Time: 上午10:12
 */

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//抓取
$result = curl_exec($ch);
$result = json_decode($result, true);//链接
$save_url = "http://www.bing.com" . $result["images"][0]['url'];
$save = file_get_contents($save_url);
file_put_contents('Resource' . DIRECTORY_SEPARATOR . "Bing-" . $result["images"][0]["startdate"] . ".jpg", $save);//下载