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