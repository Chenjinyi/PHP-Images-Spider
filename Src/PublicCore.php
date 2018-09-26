<?php
/**
 * Created by PhpStorm.
 * User: jinyi
 * Date: 2018/7/31
 * Time: 下午8:39
 */


class PublicCore
{
    public function __construct()
    {
        $this->init_dir();//初始化文件夹
    }

    /**
     * CURL GET请求
     * @param $url string 请求URL
     * @return mixed 返回获取信息
     */
    public function curl_get($url, $user_agent)
    {
        $ch = curl_init();  //初始化一个cURL会话
        curl_setopt($ch, CURLOPT_URL, $url);//设置需要获取的 URL 地址
        curl_setopt($ch, CURLOPT_HTTPHEADER, $user_agent); // 设置浏览器的特定header

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//不返回数据

        if (empty($result = curl_exec($ch))) {
            print_r('无法连接' . $url);
            die();
        };//执行一个cURL会话
        return $result;
    }

    /**
     * 写log
     * @param $spider_name string 爬虫名称
     * @param $data string 内容
     * @param null $filename_data 文件名附加字符串
     */
    public function add_log($spider_name, $data, $filename_data = null)
    {
        if (SPIDER_LOG) {
            $filename = $this->create_log($spider_name, $filename_data);
            file_put_contents($filename, $data, FILE_APPEND);
        }
    }

    /**
     * @param $spider_name string 爬虫名称
     * @param $filename_data string 内容
     * @return string 文件路径
     */
    public function create_log($spider_name, $filename_data)
    {
        $dir_path = LOG_PATH . DIRECTORY_SEPARATOR . date(DATE_FORMAT) . "-" . $spider_name . "-";
        empty($filename_data) ? $dir_path .= "log" : $dir_path .= $filename_data . "-" . "log";
        if (!file_exists($dir_path)) {
            touch($dir_path);
        }
        return $dir_path;
    }

    /**
     * 文件夹名
     * @param string 名称
     * @return string 路径
     */
    public function new_dir_name($string)
    {
        $path = FILE_PATH . DIRECTORY_SEPARATOR . date(DATE_FORMAT) . "-" . $string;
        $this->dir_create($path);
        return $path;
    }

    /**
     * 下载图片（单线程）多线程版容易请求太频繁
     * @param $file_url array array[文件名=下载链接]
     * @param $dir_name string 保存的文件夹
     */
    public function image_save($file_url, $dir_name, $spider_name, $filename_data = null)
    { //下载
        foreach ($file_url as $images) {
            foreach ($images as $key => $value) {
                print_r($key . PHP_EOL);
                if (file_exists($dir_name . DIRECTORY_SEPARATOR . $key)) {//检测是否存在
                    echo "已存在" . PHP_EOL;
                    continue;
                } else {
                    if (@$image_save = file_get_contents($value)) {
                        @$this->add_log($spider_name, $key . "=>" . $value . PHP_EOL, $filename_data);
                        @file_put_contents($dir_name . DIRECTORY_SEPARATOR . $key, $image_save);
                    } else {
                        print_r("下载错误：" . $value);
                    }
                }
            }
        }
    }

    /**
     * 创建文件夹
     * @param $dir_name string 文件夹名
     */
    public function dir_create($dir_name)
    {
        if (!file_exists($dir_name)) {
            mkdir($dir_name, 0777, true);//创建文件夹
        }
    }

    /**
     * 获取提示并用户输入
     * @param $string string 提示
     * @param $default 1|string 用户未输入时的默认职
     * @return string 用户输入内容
     */
    public function user_input($string, $default = 1)
    {
        print_r($string);
        $input = trim(fgets(STDIN));
        if (empty($input)) {
            print_r($default . PHP_EOL);
            return $default;
        }
        print_r($input . PHP_EOL);
        return $input;

    }

    /**
     * 初始化文件夹
     * 定义文件夹存放文件夹
     */
    public function init_dir()
    {
        $this->dir_create(API_PATH);
        $this->dir_create(FILE_PATH);
        $this->dir_create(LOG_PATH);
    }

    /**
     * 输出目录
     * @param $dir_path
     * @return array
     */
    public function print_dir($dir_path)
    {
        $files = array();
        if (@$handle = opendir($dir_path)) { //注意这里要加一个@，不然会有warning错误提示：）
            while (($file = readdir($handle)) !== false) {
                if ($file != ".." && $file != ".") { //排除根目录；
                    $files[] = $file;
                }
            }
            closedir($handle);
            return $files;
        }
    }

    /**
     * 确认API文件存在
     * @param $filename
     * @return bool|string
     */
    public function check_api_file($filename)
    {
        $file_path = API_PATH . DIRECTORY_SEPARATOR . $filename;
        if (!file_exists($file_path)) {
            touch($file_path);
        }
        $file = file_get_contents($file_path);
        if (!empty($file)) {
            return $file;
        }
        return false;
    }


    /**
     * 换行（没什么用的function）
     * @param $string
     * @return string
     */
    public function eol($string)
    {
        return PHP_EOL . $string . PHP_EOL;
    }

    /**
     * 获取文件夹内拥有多少个文件
     * @param $dir
     * @return int
     */
    public function images_number($dir)
    {
        return count($this->print_dir($dir));

    }

    /**
     * 通过URL进行图片格式处理（只能分辨jpg/png）
     */
    public function image_url_format($image_url, $file_name)
    {
        if (strstr($image_url, "jpg")) {
            $file_name .= ".jpg";
        } elseif (strstr($image_url, "png")) {
            $file_name .= ".png";
        } else {
            $file_name .= $image_url . ".jpeg";//不知道什么格式时的处理方式
        }
        return $file_name;
    }


    /**
     * 休息一下
     * @param bool $status 是否休息
     * @param string $min 最小休息时间
     * @param string $max 最大休息时间
     * @return string 返回休息时间
     */
    public function spider_wait($status = true, $min = SPIDERWAIT_TIME_MIN, $max = SPIDERWAIT_TIME_MAX)
    {
        if ($status) {
            $num = mt_rand($min, $max);
            echo PHP_EOL . "爬累了，我要睡觉觉zzzzzzzzzzzzzzz" . PHP_EOL . "让我先睡" . $num . "s";
            sleep($num);
        }
    }

    /**
     * 一个下载调用其他函数的封装
     * @param $string
     * @param $images_arr
     */
    public function quick_down_img($string, $images_arr, $spider_name, $filename_data = null)
    {
        if (CURL_DOWN_OPT) { //选择下载模式
            $this->curl_multi_down_images($string, $images_arr, $spider_name, $filename_data);
        } else {
            $dir_path = $this->new_dir_name($string);//生成保存路径
            $this->image_save($images_arr, $dir_path, $spider_name, $filename_data);//下载图片
            print_r("文件夹现在有:" . $this->images_number($dir_path) . "张图片");
        }
    }

    /**
     * CURL多线程下载　
     */
    public function curl_multi_down_images($string, $images_arr, $spider_name, $filename_data = null)
    {
        $dir_path = $this->new_dir_name($string);//生成保存路径
        for ($c = 1; $c <= count($images_arr); $c += CURL_DOWN_NUM) {//开始循环

            $mh = curl_multi_init();//初始化　
            $arr = array_slice($images_arr, $c - 1, CURL_DOWN_NUM);
            $conn = [];

            foreach ($arr as $title => $url) {
                if (file_exists($dir_path . DIRECTORY_SEPARATOR . $title)) {//检测是否存在
                    echo "已存在" . PHP_EOL;
                    continue;
                }
                $conn[$title] = curl_init();
                curl_setopt($conn[$title], CURLOPT_URL, $url);
                curl_setopt($conn[$title], CURLOPT_RETURNTRANSFER, 1);
                curl_multi_add_handle($mh, $conn[$title]);
            }
            // 执行批处理句柄
            $active = null;
            do {
                curl_multi_exec($mh, $active); //执行批处理句柄
            } while ($active > 0); //4

            foreach ($conn as $title => $url) {
                $res[$title] = curl_multi_getcontent($conn[$title]);
                curl_close($conn['title']);
                curl_multi_remove_handle($mh, $conn[$title]);//释放资源
            }

            curl_multi_close($mh);//释放资源

            foreach ($res as $title => $item) {//存储图片
                file_put_contents($dir_path . DIRECTORY_SEPARATOR . $title, $item);
                $this->add_log($spider_name, $title . PHP_EOL, $filename_data);
            }
            print_r("文件夹现在有:" . $this->images_number($dir_path) . "张图片" . PHP_EOL);
        }
    }

    /**
     * 输出菜单
     * @param array $spider
     * @return string 菜单
     */
    public function print_menu(array $spider)
    {
        $result = "";
        foreach ($spider as $key => $value) {
            $result .= PHP_EOL . $key . " : " . $value . PHP_EOL;
        }
        return $result;
    }

    /**
     * 原本Bilibili使用的菜单输出
     * print_menu的二次封装
     * @param $string
     * @param $spiderName string
     */
    public function bMenu($string, $spiderName)
    {
        print_r( //输出用户选择的菜单
            $this->splitLine . PHP_EOL . "
    \033[33m $spiderName Spider \033[0m" . PHP_EOL .
            $this->eol($this->print_menu($string)) .
            $this->splitLine . PHP_EOL
        );
    }

    public $splitLine = "<<<<<<<<<<<<<<<<<<================================>>>>>>>>>>>>>>>>>>";
}