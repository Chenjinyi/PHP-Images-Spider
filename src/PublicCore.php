<?php
/**
 * Created by PhpStorm.
 * User: jinyi
 * Date: 2018/7/31
 * Time: 下午8:39
 */


class PublicCore
{
    /**
     * CURL GET请求
     * @param $url string 请求URL
     * @return mixed 返回获取信息
     */
    public function curl_get($url,$user_agent)
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
     * 下载图片（单线程）多线程版容易请求太频繁
     * @param $file_url array 下载链接-文件名
     * @param $dir_name string 保存的文件夹
     */
    public function image_save($file_url, $dir_name)
    { //下载
        foreach ($file_url as $url) {
            $image_name = array_keys($file_url);
            $url = "http://" . $url;

            if (file_exists($dir_name . DIRECTORY_SEPARATOR . $image_name)) {//检测是否存在
                echo "已存在" . PHP_EOL;
                continue;
            } else {
                if ($image_save = file_get_contents($url)) {
                    @file_put_contents($dir_name . DIRECTORY_SEPARATOR . $image_name, $image_save);
                } else {
                    print_r("下载错误：" . $url);
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
     * @param $string
     * @return string
     */
    public function user_input($string)
    {
        print_r($string);
        $input = trim(fgets(STDIN));
        return $input;
    }

    /**
     * 初始化文件夹
     */
    public function init_dir()
    {
        $this->dir_create(API_PATH);
        $this->dir_create(FILE_PATH);
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
        $file_path=API_PATH.DIRECTORY_SEPARATOR.$filename;
        if (!file_exists($file_path)){
            touch($file_path);
        }
        $file=file_get_contents($file_path);
        if (!empty($file)){
            return $file;
        }
        return false;
    }
}