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
use Aes;

class AuthController extends Controller
{

    protected $config;

    public function __construct($controller, $action)
    {
        parent::__construct($controller, $action);
        date_default_timezone_set('PRC');
        $this->config = include 'config/sso.php';
    }

    /**
     * 这个叫法没弄好，这个方法的作用是：
     * 用户通过公众号发送信息时，微信服务器收到后，会转发到此接口，对用户发送的信息进行响应
     * 可能的信息有：关注、取消关注、发送文本，图片等
     */
    public function token()
    {
        //$this->valid();已验证通过了
        $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        if (!empty($postStr)) {
            $this->_savePostData($postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $msgType = $postObj->MsgType;
            switch ($msgType) {
                case 'text'://文本消息
                    $contentStr = "感谢关注,业务开发中";
                    $this->_textMessage($postObj, $contentStr);
                    break;
                case 'event'://事件：关注，取消关注等
                    $event = $postObj->Event;
                    switch ($event) {
                        case 'subscribe'://关注
                            $contentStr = "感谢关注!";
                            $this->_textMessage($postObj, $contentStr);
                            break;
                        case 'unsubscribe'://取消关注
                            break;
                        default:
                            break;
                    }
                    break;
                default:
                    $contentStr = "感谢关注";
                    $this->_textMessage($postObj, $contentStr);
                    break;
            }
        } else {
            echo "";
            exit;
        }
    }

    /**
     * 文本消息
     * @param $postObj
     */
    private function _textMessage($postObj, $contentStr)
    {
        $keyword = trim($postObj->Content);//用户发送的文字
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $msgType = 'text';
        $time = time();
        $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
        echo $resultStr;
    }

    /**
     * 配置token时的较验方法
     * 基本配置-》服务器配置-》令牌token
     */
    public function valid()
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

    /**
     * 用户授权成功后的回调
     */
    public function callback()
    {
        $code = trim($_GET['code']);
        $token = trim($_GET['token']);
        if ($code && $token) {
            $uid = isLegalToken($token, $this->config['aes_key']);
            if ($uid) {
                //@todo 判断用户是否绑定，未绑定则绑定（更新数据库），绑定则跳到某页
                if ($this->_isUserBindWechat()) {
                    die('您已绑定过微信,无需重复绑定');
                } else {
                    $wechat = new Wechat();
                    $arr = $wechat->getAccessToken($code);
                    $accessToken = $arr['access_token'];
                    $openId = $arr['openid'];
                    $wechatUserInfo = $wechat->getUserInfo($accessToken, $openId);
                    print_r($wechatUserInfo);
                    die('绑定成功');
                }
            }
        }
    }

    private function _isUserBindWechat()
    {
        //@todo 逻辑
        return false;
    }

    /**
     * 保存微信服务器推送的数据，便于分析
     * @param $data
     */
    private function _savePostData($data)
    {
        //@todo 这个方法是为了便于在本地测试观察线上数据用，调试没问题了可以删除
        $file = 'p.txt';
        $fp = fopen($file, 'a+');
        $data = "时间:" . date('Y-m-d H:i:s', time()) . "----------------------------↓\r\n\r\n$data\r\n\r\n";
        $data = iconv('utf-8', 'gbk', $data);
        fwrite($fp, $data);
        fclose($fp);
    }

    public function clear()
    {
        file_put_contents('p.txt', '');
    }

}