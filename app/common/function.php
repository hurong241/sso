<?php
/**
 * 生成随机字符串
 * @param $num
 * @return string
 */
function str_random($num)
{
    $str = '0123456789abcedfghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = str_shuffle($str);
    $str = substr($str, 0, $num);
    return $str;
}

/**
 * 跳转到url
 * @param $url
 */
function redirect($url)
{
    header('Location:' . $url);
}

/*
 * 取域名（带协议）
 */
function getHost()
{
    $server = $_SERVER;
    $port = $server['SERVER_PORT'];
    $port = $port != 80 ?: '';
    $host = $server['REQUEST_SCHEME'] . '://' . $server['HTTP_HOST'] . $port;
    return $host;
}

/**
 * 输出json格式错误提示
 * 
 * @param $code
 * @param $msg
 */
function responseError($code, $msg)
{
    echo json_encode([
        'code' => $code,
        'msg' => $msg
    ]);
    exit();
}

/**
 * 是否合法token,如果是则返回登录用户的Id
 *
 * @param string $token
 * @param string $key
 * @return bool
 */
function isLegalToken($token,$key)
{
    $result = false;
    if (empty($token)) {
        return $result;
    }
    $aes = new \Aes($key);
    $arr = json_decode($aes->decrypt($token), true);
    if (!empty($arr) && ($arr['key'] == $key)) {
        $result = $arr['uid'];
    }
    return $result;
}

/**
 * 模拟post进行url请求
 * @param string $url
 * @param string $param
 * @return bool|mixed
 */
function curlPost($url = '', $param = '') {
    if (empty($url) || empty($param)) {
        return false;
    }

    $postUrl = $url;
    $curlPost = $param;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);

    return $data;
}

/**
 * 发送post请求(timeout单位：秒)
 * @param string $url 请求地址
 * @param string $post_data 数组健值对http_build_query后的字符串或json
 * @return string
 */
function send_post($url, $post_data) {
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type:application/x-www-form-urlencoded',
            'content' => $post_data,
            'timeout' => 10
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}