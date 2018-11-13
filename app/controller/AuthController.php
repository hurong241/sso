<?php
/**
 * 说明:
 *
 * User: 胡熔
 * Date: 2018/11/13
 * Time: 18:54
 */

namespace app\controller;

use Wechat;

class AuthController extends Controller
{

    protected $config;
    public function __construct($controller, $action)
    {
        parent::__construct($controller, $action);
        $this->config = include 'config/sso.php';
    }

    public function token()
    {
        $signature = $_REQUEST['signature'];
        $timestamp = $_REQUEST['timestamp'];
        $nonce = $_REQUEST['nonce'];
        $echostr = $_REQUEST['echostr'];
        if ($this->checkSignature($signature, $timestamp, $nonce)) {
            echo $echostr;
            die;//这里特别注意，如果不用die结束程序会token验证失败
        } else {
            echo false;
        }
    }

    private function checkSignature($signature, $timestamp, $nonce)
    {
        $token = $this->config['token'];//这里写你在微信公众平台里面填写的token
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

}