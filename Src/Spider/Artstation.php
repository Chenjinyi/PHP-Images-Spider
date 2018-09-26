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
        '1' => 'trending',
        'latest',
        'picks',
        'user',
        'search'
    ];//可以选择的模式 可以调用的function

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
            $images_url = $value->cover->thumb_url;
//            $images_url = str_replace("medium", "large", $images_url);//生成更高清的图片下载地址
//            $images_url=str_replace('smaller_square','large',$images_url);
//            preg_replace("/201.[0-9]{3,}/",'',$images_url);
            $rep = substr_replace($images_url,'large',63,29);
            $file_name = $value->title . "-" . $value->cover_asset_id;//生成图片名
            $file_name = $spiderCore->image_url_format($images_url, $file_name);
            array_push($images_arr, [$file_name => $images_url]);
        }
        return $images_arr;
    }

    //https://www.artstation.com/search/projects.json?direction=desc&order=likes_count&page=1&q=ne&show_pro_first=true

    /**
     * 图片爬取下载
     * @param $spiderCore
     * @param $spider_name
     * @param $parm
     */
    public function index_spider_core($spiderCore, $spider_name, $parm)
    {
        $posts_num = $spiderCore->user_input("请输入爬取页数(1页=50个作品)(默认为：1):", 1);
        for ($start_num = 1; $start_num <= $posts_num; $start_num++) {
            $url = "https://www.artstation.com/projects.json?page=" . $start_num . $parm;
            $result = $spiderCore->curl_get($url, $this->userAgent);
            $result = json_decode($result);
            $images_arr = $this->get_img_url($result, $spiderCore);

            $spiderCore->quick_down_img($this->spider_name . "-" . $spider_name, $images_arr, "Artstation");
            $spiderCore->spider_wait(ARTSTATION_SLEEP, ARTSTATION_SLEEP_TIME_MIN, ARTSTATION_SLEEP_TIME_MAX);
        }

    }

    /**
     * 图片搜索爬取下载
     * @param $spiderCore
     * @param $spider_name
     * @param $parm
     */
    //https://www.artstation.com/projects.json?page=2&sorting=trending
    public function search_core($spiderCore, $spider_name, $parm)
    {
        $posts_num = $spiderCore->user_input("请输入爬取页数(1页=50个作品)(默认为：1):", 1);
        for ($start_num = 1; $start_num <= $posts_num; $start_num++) {
            $url = "https://www.artstation.com/search/projects.json?page=" . $start_num . $parm;
            $result = $spiderCore->curl_get($url, $this->userAgent);
            $result = json_decode($result);
            $images_arr = $this->get_img_url($result, $spiderCore);
            $spiderCore->quick_down_img($this->spider_name . "-" . $spider_name, $images_arr, "Artstation");
            $spiderCore->spider_wait(ARTSTATION_SLEEP, ARTSTATION_SLEEP_TIME_MIN, ARTSTATION_SLEEP_TIME_MAX);
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
        $spiderCore->quick_down_img($this->spider_name . "-" . $user, $images_arr, "Artstation", $user);
    }

    public function latest($spiderCore) //最新图片
    {
        $this->index_spider_core($spiderCore, 'latest', '&sorting=latest');
    }

    public function picks($spiderCore) //最佳
    {
        $this->index_spider_core($spiderCore, 'picks', '&sorting=picks');
    }

    public function trending($spiderCore) //最热门
    {
        $this->index_spider_core($spiderCore, 'trending', '&sorting=trending');
    }

    //https://www.artstation.com/search/projects.json?direction=desc&order=likes_count&page=1&q=dva&show_pro_first=true
    public function search($spiderCore)
    {
        $parm = "";
        $title = $spiderCore->user_input("请输入要搜索的内容（不填则随缘）:", RAND_KEYWORD[mt_rand(0, count(RAND_KEYWORD) - 1)]);
        $parm .= "&q=" . $title;
        $show_pro_first = $spiderCore->user_input("请输入True/False" . PHP_EOL . "Pro用户优先?（默认 true）:", true);
        $show_pro_first === "false" ? $parm .= "&show_pro_first=false" : $parm .= "&show_pro_first=true";
        $order = $spiderCore->user_input("最新还是喜欢?(默认 true 喜欢优先) :", true);
        $order === "false" ? $parm .= "&order=recent" : $parm .= "&order=likes_count&direction=desc";
        $this->search_core($spiderCore, $title, $parm);
    }
}

$artstation = new Artstation();
$spiderCore->bMenu($artstation->mode, $artstation->spider_name);
$mode = $spiderCore->user_input(PHP_EOL . "请选择你需要使用的模式：", null);
@empty($user_mode = $artstation->mode[$mode]) ? die(PHP_EOL . '没有这个爬虫模式') : $artstation->$user_mode($spiderCore); //调用爬虫，并传入公用function