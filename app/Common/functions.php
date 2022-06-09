<?php

use App\Models\Site;
use Illuminate\Support\Facades\DB;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\Redis;

//$type:1 加密   2：解密
function jwtCode($data, $type = 1)
{
    $jwt = new JWT;
    $key = "#t2HSj2dh+3Gxj*d31ux3c!ak2kida!a";
    if ($type == "1") {
        return $jwt->encode($data, $key);
    } else {
        return $jwt->decode($data, $key, array('HS256'));
    }
}

//图片base64加密
function encodeImg($img = '', $imgHtmlCode = true)
{
    //$imageInfo = getimagesize($img);
    //$base64 = "" . chunk_split(base64_encode(file_get_contents($img)));
    return chunk_split(base64_encode(file_get_contents($img)));;
}


/**
 * curl上传文件
 *
 * @param unknown $url
 * @param unknown $filename
 * @param unknown $path
 * @param unknown $type
 */
function curl_upload_file($url, $filename, $path, $type, $folder)
{
    //php 5.5以上的用法
    $token = 'eyJ0eXAiOiJKV1QiL6JhbGciOiJ6UzI1NiJ9';
    if (class_exists('\CURLFile')) {
        $data = array(
            'imgs' => new \CURLFile(realpath($path), $type, $filename),
            'folder' => $folder,
            'token' => md5($token)
        );
    } else {
        $data = array(
            'imgs' => '@' . realpath($path) . ";type=" . $type . ";filename=" . $filename,
            'folder' => $folder
        );
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $return_data = curl_exec($ch);
    $return_data = trim($return_data);
    curl_close($ch);
    return $return_data;
}

/**
 * curl删除文件
 *
 * @param unknown $url
 * @param unknown $filename
 */
function curl_del_file($url, $filename)
{
    $token = 'eyJ0eXAiOiJKV1QiL6JhbGciOiJ6UzI1NiJ9';
    $data = array(
        'file' => $filename,
        'token' => md5($token)
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $return_data = curl_exec($ch);
    $return_data = trim($return_data);
    curl_close($ch);
    return $return_data;
}

/**
 * 上传文件
 * @param $file
 * @return array
 */
function upload_file($file)
{
    if ($file['error'][0] != 4) {
        //$url = env('IMG_URL'); //图片API服务器
        $url = WebSites()['img_domain'];
        $count = count($file['name']);       // 上传图片的数量
        $err_count = 0;
        if ($count > 10) echo json_encode(array('code' => 0, 'msg' => '批量上传图片一次最多上传10张图片'));;
        for ($i = 0; $i < $count; $i++) {
            //$tmpName = $file->getClientOriginalName(); //上传上来的文件名,单张
            $tmpName = $file['name'][$i]; //上传上来的文件
            $tmpFile = $file['tmp_name'][$i]; //上传上来的临时存储路径
            $tmpType = $file['type'][$i]; //上传上来的文件类型
            $folder = 'img'; //存储路径
            //$entension = $file->getClientOriginalExtension();
            //$newName = md5($clientName) . "." . $entension;
            //执行上传
            $file_temp_data = json_decode(curl_upload_file($url, $tmpName, $tmpFile, $tmpType, $folder), true);
            if ($file_temp_data['code'] == 0) {
                $file_erro_data[]['erro'] = '第' . ($i + 1) . '张图片上传失败:' . $file_temp_data['msg'];
                $err_count++;
            } else {
                $file_filename_data[]['file_name'] = $file_temp_data['full_name'];
            }
        }
        if ($err_count == $count) {
            $file_data['status'] = 0;
            $file_data['erro'] = implode(',', array_column($file_erro_data, 'erro'));
        } else {
            $file_data['status'] = 1;
            $file_data['file_name'] = implode(',', array_column($file_filename_data, 'file_name'));
            if (isset($file_data['erro'])) {
                $file_data['erro'] = implode(',', array_column($file_erro_data, 'erro'));
            }
        }

        return $file_data;
    } else {
        return array('status' => 0, 'msg' => '请选择上传的文件');
    }
}

/**
 * @return mixed
 * 配置文件站点信息
 */
function WebSites()
{
    $domain = unserialize(Redis::get("system:sites"));
    if (!$domain) {
        $domain_data = Site::first();
        $domain = json_decode($domain_data['value'], true);
        Redis::set("system:sites", serialize($domain));
    }
    return $domain;
    //$domain['img_domain']
}

/**
 * 解密字符串
 * @param string $data 字符串
 * @param string $key 加密key
 * @return string
 */

function decryptWithOpenssl($data)
{
    $key = config("public.app.AppKey");
    $iv = config("public.app.AppIV");
    return openssl_decrypt(base64_decode($data), "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv);
}

/**
 * 加密字符串
 * @param string $data 字符串
 * @param string $key 加密key
 * @return string
 */
function encryptWithOpenssl($data)
{
    $key = config("public.app.AppKey");
    $iv = config("public.app.AppIV");
    return base64_encode(openssl_encrypt($data, "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv));
}

function getClientIp()
{
    /*if (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
    }
    if (getenv('HTTP_X_REAL_IP')) {
        $ip = getenv('HTTP_X_REAL_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
        $ips = explode(',', $ip);
        $ip = $ips[0];
    } elseif (getenv('REMOTE_ADDR')) {
        $ip = getenv('REMOTE_ADDR');
    } else {
        $ip = '0.0.0.0';
    }
    return $ip;*/
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s',
            $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        foreach ($matches[0] as $xip) {
            if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                $ip = $xip;
                break;
            }
        }
    } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/',
            $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/',
            $_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/',
            $_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    return $ip;
}

//获取浏览器

function get_broswer(){
    $sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
    $exp[0] = "未知浏览器";
    $exp[1] = "";
    //stripos() 函数查找字符串在另一字符串中第一次出现的位置（不区分大小写）    preg_match()执行匹配正则表达式
    if (stripos($sys, "Firefox/") > 0) {
        preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
        $exp[0] = "Firefox";
        $exp[1] = $b[1];  //获取火狐浏览器的版本号
    }  if (stripos($sys, "Maxthon") > 0) {
        preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
        $exp[0] = "傲游";
        $exp[1] = $aoyou[1];
    }  if (stripos($sys, "MSIE") > 0) {
        preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
        $exp[0] = "IE";
        $exp[1] = $ie[1];  //获取IE的版本号
    }  if (stripos($sys, "OPR") > 0) {
        preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
        $exp[0] = "Opera";
        $exp[1] = $opera[1];
    }  if(stripos($sys, "Edge") > 0) {
        //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
        preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
        $exp[0] = "Edge";
        $exp[1] = $Edge[1];
    }  if (stripos($sys, "Chrome") > 0) {
        preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
        $exp[0] = "Chrome";
        $exp[1] = $google[1];  //获取google chrome的版本号
    }  if(stripos($sys,'rv:')>0 && stripos($sys,'Gecko')>0){
        preg_match("/rv:([\d\.]+)/", $sys, $IE);
        $exp[0] = "IE";
        $exp[1] = $IE[1];
    }
    return $exp[0].'('.$exp[1].')';
}

?>
