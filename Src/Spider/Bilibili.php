<?php
/**
 * Created by PhpStorm.
 * User: jinyi
 * Date: 2018/8/2
 * Time: 下午2:03
 */

//https://api.bilibili.com/x/web-interface/search/type?jsonp=jsonp&search_type=photo&highlight=1&keyword=%E5%A4%A9%E4%BD%BF&page=2&callback=__jp0
class Bilibili
{
    public $userAgent = [
        "Connection: keep-alive",
        "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
        "Upgrade-Insecure-Requests: 1",
        "DNT:1",
        "Accept-Language:zh-CN,zh;q=0.8",
        "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.101 Safari/537.36"
    ];//设置用户user-agent

    public $rank_type = [ //排行榜
        1 => 'day',
        2 => 'month',
        'week',
    ];

    public $biz = [
        1 => '1',//画友
        '2' //摄影
    ];

    public $get_date = [ //日期
        1 => '当前日期',
        2 => '自定义'

    ];

    public $mode = [ //爬虫模式
        1 => 'top50',
        'search',
        'fuckBilibili'
    ];

    public $category = [ //类别
        1 => null,
        2 => 'cos',
        'sifu'
    ];

    /**
     * 掏空bilibili专用
     */
    public function fuckBilibili($spiderCore){
        //让用户输入参数
        $biz = $this->quick_input($spiderCore, $spiderCore->eol("1:画友，2:摄影") . "请输入要爬取的板块(默认为画友)：", $this->biz, "没有这个板块", '1');
        $rank_type = $this->quick_input($spiderCore, "请选择排行榜（默认为 2 月榜）：", $this->rank_type, "没有这种排行榜", '2');
        $get_date = $this->quick_input($spiderCore, "请选择日期（默认为 当日）：", $this->get_date, "日期", '1');

        if ($biz == 2) {
            $category = $this->quick_input($spiderCore, "请选择板块（默认为 2 Cos ）：", $this->category, "没有这种板块", '2');
        } else {
            $category = null;
        }
        if ($get_date == 2) {
            $spider_date = $spiderCore->user_input("请输入自定义的时间（Y-m-d）：", date('Y-m-d'));
        } else {
            $spider_date = date('Y-m-d');
        }
        while (true){
            $spider_date = $this->getYesterday($spider_date);
            print_r("开始获取：".$spider_date.PHP_EOL);
            //封装请求链接
            @$parm = "biz=" . $biz . "&category=" . $this->category[$category] . "&rank_type=" . $this->rank_type[$rank_type] . "&date=" . $spider_date . "&page_num=0&page_size=50";
            $url = "http://api.vc.bilibili.com/link_draw/v2/Doc/ranklist?" . $parm;
            print_r(PHP_EOL."爬取的参数为：".$parm.PHP_EOL);
            //下载
            $result = $spiderCore->curl_get($url, $this->userAgent);
            $result = json_decode($result);
            $images_arr = $this->get_images($result);

            if (empty($images_arr)){
                break;
            }
            @$spiderCore->quick_down_img("Bilibili" . "-" . $this->rank_type[$rank_type], $images_arr);
            $this->bilibili_sleep();//休息一下
        }
    }

    /**
     * 获取给定日期的前一天
     * @param string $date
     * @return string $yesterday
     */
    public  function getYesterday($date)
    {
        if(empty($date))
        {
            $yesterday = date("Y-m-d",strtotime("-1 day"));
        }else{
            $arr = explode('-', $date);
            $year = $arr[0];
            $month = $arr[1];
            $day = $arr[2];
            $unixtime = mktime(0,0,0,$month,$day,$year)-86400;
            $yesterday = date('Y-m-d',$unixtime);
        }
        return $yesterday;
    }

    /**
     * config设置开启时，每执行一次循环休息一下
     */
    public function bilibili_sleep(){
        if (BILIBILI_SLEEP){
            print_r(PHP_EOL."爬累了，劳资累了劳资要睡觉zzzzzzzzz".PHP_EOL);
            sleep(BILIBILI_SLEEP_TIME);
            print_r(PHP_EOL."劳资刚刚-".BILIBILI_SLEEP_TIME."S了");
        }
    }


    /**
     * 输出选择菜单
     * @param $string
     * @param $spiderCore
     */
    public function menu($string, $spiderCore)
    {
        print_r( //输出用户选择的菜单
            "============================================" . PHP_EOL . "
    \033[33m BIlibili Spider \033[0m" .
            $spiderCore->eol($spiderCore->print_menu($string)) .
            "============================================" . PHP_EOL
        );
    }

    public function search($spiderCore)
    {
        $q = $spiderCore->user_input("请输入一个需要查询的字符串(不输入就随缘了                ):",RAND_KEYWORD[mt_rand(0,count(RAND_KEYWORD)-1)]); //获取查询内容
        die('还在开发中');
    }

    /**
     * 输出菜单
     * @param $spiderCore
     * @param $string
     * @param $array
     * @param $exit_string
     * @return mixed
     */
    public function quick_input($spiderCore, $string, $array, $exit_string, $default)
    {
        $this->menu($array, $spiderCore);
        $input = $spiderCore->user_input($string, $default);
        if (@empty($array[$input])) {
            die($exit_string);
        }
        return $input;
    }

    /**
     * 生成文件名和图片链接
     * @param $result
     * @return array
     */
    public function get_images($result)
    {
        $images_arr = [];
        foreach (@$result->data->items as $items) {
            $user_name = $items->user->name;//获得用户名
            $items_obj = $items->item->pictures;
            $image_num = 1;//图片没有单独的ID 当拥有多张图片当时候防止重复
            foreach ($items_obj as $src_obj) {
                $src = $src_obj->img_src;
                $format = explode('.', $src);
                $filename = $items->item->title . "-" . $user_name . "-" . $items->item->doc_id . "-" . $image_num . "." . $format['3'];
                array_push($images_arr, [$filename => $src]);
                $image_num++;
            }
            unset($image_num);
        }
        return $images_arr;
    }

    /**
     * 爬取top50
     * @param $spiderCore
     */
    public function top50($spiderCore)
    {
        //让用户输入参数
        $biz = $this->quick_input($spiderCore, $spiderCore->eol("1:画友，2:摄影") . "请输入要爬取的板块(默认为画友)：", $this->biz, "没有这个板块", '1');
        $rank_type = $this->quick_input($spiderCore, "请选择排行榜（默认为 2 月榜）：", $this->rank_type, "没有这种排行榜", '2');
        $get_date = $this->quick_input($spiderCore, "请选择日期（默认为 当日）：", $this->get_date, "日期", '1');

        if ($biz == 2) {
            $category = $this->quick_input($spiderCore, "请选择板块（默认为 2 Cos ）：", $this->category, "没有这种板块", '2');
        } else {
            $category = null;
        }

        if ($get_date == 2) {
            $get_date = $spiderCore->user_input("请输入自定义的时间（Y-m-d）：", date('Y-m-d'));
        } else {
            $get_date = date('Y-m-d');
        }

        //封装请求链接
        @$parm = "biz=" . $biz . "&category=" . $this->category[$category] . "&rank_type=" . $this->rank_type[$rank_type] . "&date=" . $get_date . "&page_num=0&page_size=50";
        $url = "http://api.vc.bilibili.com/link_draw/v2/Doc/ranklist?" . $parm;
        //下载
        $result = $spiderCore->curl_get($url, $this->userAgent);
        $result = json_decode($result);
        $images_arr = $this->get_images($result);
        @$spiderCore->quick_down_img("Bilibili" . "-" . $this->rank_type[$rank_type], $images_arr);
    }

}

$bilibili = new Bilibili();

$bilibili->menu($bilibili->mode, $spiderCore);
$mode = $spiderCore->user_input(PHP_EOL . "请选择你需要使用的模式：", null);
@empty($user_mode = $bilibili->mode[$mode]) ? die(PHP_EOL . '没有这个爬虫模式') : $bilibili->$user_mode($spiderCore); //调用爬虫，并传入公用function
