<?php
/**
 * Created by PhpStorm.
 * User: jinyi
 * Date: 2018/8/3
 * Time: 下午1:19
 */

class Bcy
{
    //https://bcy.net/coser/index/ajaxloadtoppost?p=3&type=week&date=
    /**
     * 针对半次元Tag的爬取
     * URL规律
     * https://bcy.net/circle/timeline/loadtag?since=0&grid_type=timeline&tag_id=399&sort=hot
     * sort排序方式 recent最新
     * tag_id 标签id Cos为399
     * since 最后一天图片的时间戳
     */

    public $userAgent = [
        "Host:bcy.net",
        "Connection: keep-alive",
        "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
        "Upgrade-Insecure-Requests: 1",
        "DNT:1",
        "Accept-Language:zh-CN,zh;q=0.8",
        "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.101 Safari/537.36"
    ];

    public function serve()
    {
        $spider = [
            1=>"tags",
        ];

    }
}


$bcy = new Bcy();
$bcy->serve();
