<?php
/**
 * Created by PhpStorm.
 * User: jinyi
 * Date: 2018/8/1
 * Time: 上午10:11
 */

class Artstation
{
    public $userAgent = [
        "Host:www.artstation.com",
        "Connection: keep-alive",
        "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
        "Upgrade-Insecure-Requests: 1",
        "DNT:1",
        "Accept-Language:zh-CN,zh;q=0.8",
        "User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.75 Safari/537.36"
    ];//设置用户user-agent

    public $mode = [
        '1'=>'trending',
        'latest',
        'picks',
        'user',
    ];//可以选择的模式/可以调用的function

    public $spider_name = "Artstation";

    /**
     * 获取图片链接
     * @param $result
     * @param $spiderCore
     * @return array 下载链接
     */
    public function get_img_url($result, $spiderCore)
    {
        $images_arr = [];
        foreach ($result->data as $value) {
            $images_url = $value->cover->medium_image_url;
            $images_url = str_replace("medium", "large", $images_url);//生成更高清的图片下载地址
            $file_name = $value->title . "-" . $value->cover_asset_id;//生成图片名
            $file_name = $spiderCore->image_url_format($images_url, $file_name);
            array_push($images_arr, [$file_name => $images_url]);
        }
        return $images_arr;
    }

    public function index_spider_core($spiderCore,$spider_name)
    {
        $posts_num = $spiderCore->user_input("请输入爬取页数(1页=50个作品)/(默认为1):", 1);
        for ($start_num = 1; $start_num <= $posts_num; $start_num++) {
            $url = "https://www.artstation.com/projects.json?page=" . $start_num . "&sorting=" . $spider_name;
            $result = $spiderCore->curl_get($url, $this->userAgent);
            $result = json_decode($result);
            $images_arr = $this->get_img_url($result, $spiderCore);
            $spiderCore->quick_down_img($this->spider_name . "-" . $spider_name, $images_arr);

        }
    }

    /**
     * 指定用户爬取
     * @param $spiderCore
     */
    public function user($spiderCore)
    {
        @empty($user = $spiderCore->user_input("请输入你要抓取的用户ID（https://www.artstation.com/xxxxx）：", null)) ? die(PHP_EOL . "用户不能为空") : print_r(PHP_EOL . "开始获取图片" . PHP_EOL);
        $result = $spiderCore->curl_get("https://www.artstation.com/users/" . $user . "/projects.json", $this->userAgent); //爬取json
        $result = json_decode($result);

        $images_arr = $this->get_img_url($result, $spiderCore);
        $spiderCore->quick_down_img($this->spider_name . "-" . $user, $images_arr);
    }

    /**
     * 最新图片爬取
     * @param $spiderCore
     */
    public function latest($spiderCore)
    {
        $this->index_spider_core($spiderCore,'latest');
    }

    public function picks($spiderCore)
    {
        $this->index_spider_core($spiderCore,'picks');
    }

    public function trending($spiderCore)
    {
        $this->index_spider_core($spiderCore,'trending');
    }

    public function search($spiderCore)
    {

    }
}

$artstation = new Artstation();
print_r( //输出用户选择的菜单
    "=====================================
    \033[33m Artstation Spider \033[0m" .
    $spiderCore->eol($spiderCore->print_menu($artstation->mode)) .
    "============================================"
);
$mode = $spiderCore->user_input(PHP_EOL . "请选择你需要使用的模式：", null);
@empty($user_mode = $artstation->mode[$mode]) ? die(PHP_EOL . '没有这个爬虫模式') : $artstation->$user_mode($spiderCore); //调用爬虫，并传入公用function