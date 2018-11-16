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

            //@todo 测试用
            $msgType = 'link';


            switch ($msgType) {
                case 'text'://文本消息（文字或表情),如果是用户收藏的表情，content为【收到不支持的消息类型，暂无法显示】
                    $content = $postObj->Content;
                    $contentStr = "您发送的内容是:" . $content;
                    $this->_sendTextMessage($postObj, $contentStr);
                    break;
                case 'image'://图片消息
                    $this->_receiveImageMessage($postObj);
                    break;
                case 'voice'://语音消息
                    $this->_receiveVoiceMessage($postObj);
                    break;
                case 'video'://视频（如下载的抖音)
                    $this->_receiveVideoMessage($postObj);
                    break;
                case 'shortvideo'://小视频(还不知道是哪种，响应的格式和video一样)
                    $this->_receiveVideoMessage($postObj);
                    break;
                case 'location'://用户发送位置
                    $this->_receiveLocationMessage($postObj);
                    break;
                case 'link'://发送链接，如收藏的网址
                    $this->_receiveLinkMessage($postObj);
                    break;
                case 'event'://事件：关注，取消关注等
                    $event = $postObj->Event;
                    switch ($event) {
                        case 'subscribe'://关注
                            $content = '感谢关注';
                            $this->_sendTextMessage($postObj, $content);
                            break;
                        case 'unsubscribe'://取消关注
                            $this->_receiveUnSubscribe($postObj);
                            break;
                        default:
                            break;
                    }
                    break;
                default:
                    //可能存在未开发的消息类型
                    $content = '已收到您发送的信息，稍后会有客服和您联系';
                    $this->_sendTextMessage($postObj, $content);
                    break;
            }
        } else {
            echo "";
            exit;
        }
    }

    /**
     * 收到用户发送的链接
     *<xml>
     * <ToUserName><![CDATA[gh_0a9637eaee4e]]></ToUserName>
     * <FromUserName><![CDATA[ovLdf0YMgJrzOqCCMxP5mFMjTF2k]]></FromUserName>
     * <CreateTime>1542351680</CreateTime>
     * <MsgType><![CDATA[link]]></MsgType>
     * <Title><![CDATA[½»¶򎣈�ܾ粼˹£¬21 ̪µŋ󈉧ºοª·¢³🋩nux µģ¿]]></Title>
     * <Description><![CDATA[]]></Description>
     * <Url><![CDATA[http://mp.weixin.qq.com/s?__biz=MjM5MjAwODM4MA==&mid=2650703723&idx=1&sn=ee5f2ba1dec83f38e53f726e10042b7b&chksm=bea6f6b889d17fae12d7ccb6733f45b714d12c94b87f40403392b1b109745aaa95932486ea62&scene=0#rd]]></Url>
     * <MsgId>6624350024948969844</MsgId>
     * <Encrypt><![CDATA[yXKwTewhbCuV3y51jEzHOelcbP+O7YCS9hp48f7fBLSiaKhu+b0CDqVkdxe+lEFa7Y4xYdd0FYv8g/R4paA+8JWdpKIlnUo0iCC13HAOQ8LJqK0CjIAeEeZdmp40QExLK5g5i3uT2Row6iR+1mY43G9qTrOUmjTE84bM3e8eR73FxEsYmJ8e7Vw3gnQI4+d8IU8dGbVQ8LlxE3HKS6t40OS6GY2UpQ7mYg03MlfZl6/PFuMpW1XpWPiKeQF7vaGiszoylwIZHNHHQaHyCcap+yEBVWy+TFiFvVHV1JzyaZJlSamosz1LtKbxcDmixO3IrRJBpz9eNFd137pkq8Qae76P/pNoe1dqflvf27UdmLkKRuwIpB0dFwlr5NaxsH9nXEybNbr5kbKPDes+TRrKlwuo8xOYs+v9R/XQQJYO+AT0pPg++fCvQjBRIPUjdgExKCic0QWp+JmikwafNnKx5ofTJbbJiGL6g83gXSk1qkqVihD6e7kmpy9DKYmXAmA3LvMj1Fema39+EFjNOQgpgFSvOVChaj7YzjJn1uS/+L2VlgNMJgffLtFqSbLgUpV1awjB8xeTI3upAD2WdGc/K/UzCpZxukKCnhPG0y+kXkfsrnz8nvzmvtFJgl56AIBdfeVfj2BY/1qfLh2Th45PSnWQb/FSZTUMm3A6tKNXJHICs9UBkcdICjbV5GMg8XV5yYJjiussFhoZ7tteVDGOMf+XcyiG5ElUj1vG4InUD8PTVlXBIOIJiphOuvgmKS9xucvuMowXzFPlc9/3zLGbPQranbKMGxSlVlWXyQwm/WRGonlANp2CT3VCJR7HI5Gbv+uLA/0ZcyQ14k5tL+mGYVusNoGMmEcgbEIri5NZeyCdEblxDBtU6aiq6F3mKEug]]></Encrypt>
     * </xml>
     * @param $postObj
     */
    private function _receiveLinkMessage($postObj)
    {
        //@todo 收到链接干点啥
        $title = $postObj->Title;
    }

    /**
     * 收到用户发送的位置消息
     * <xml>
     * <ToUserName><![CDATA[gh_0a9637eaee4e]]></ToUserName>
     * <FromUserName><![CDATA[ovLdf0YMgJrzOqCCMxP5mFMjTF2k]]></FromUserName>
     * <CreateTime>1542351539</CreateTime>
     * <MsgType><![CDATA[location]]></MsgType>
     * <Location_X>22.534035</Location_X>
     * <Location_Y>114.056793</Location_Y>
     * <Scale>16</Scale>
     * <Label><![CDATA[׿Խʱ´򣳡(¸£ͯȸӦͯ·4068ºé]]></Label>
     * <MsgId>6624349419358581106</MsgId>
     * <Encrypt><![CDATA[rXwDsoi+q8WBQrkLZf2UDIfF0XXGrUJvNysV16uD/ftF9Aj5kztqxlv0gPaWppxYtLAxXPDj7lMGaqoEXzWPTwprWrJmgoxDfarQgDT7NXprml1AVbY94gcm+SEHd3Un5Ismu+chHaj5gES+qyNVqwuR/EeKgxIODkjUAvGsD34lTq63DUo9isxfTicvF88vrNObfW6eS7/So4kuD6cV5p6Xcdp1YLDJFGXnb2I+uW0lScn/ud/P084oV47lvZEsbprO8Hh/2W/GpXvHOLi6dbY6GTvhdIJVswiDJkdo5EfsfJ9Qi51Y/DdmV8N8/s+OgkKHufLz4tN+6GcQAzes4oxB90I0qLzJ7qoM2imJfTFdIDcgdVCN7DkOHqucT0lioNCLfjJUret2E5tl3GaroV9R0MYp+Yk6RzcUihcJbFrCbepeKBU6QoKOZC/q0Ncs5G6ONPNar5mjZgULlEqD9ZG1RtrurER4wcbwdxMqBWiyb8D3IKB+AZeMjNmgUmSqIabZj7J49UkpqI1DGdwfC9BqXQwsTRexucFpclWI+/G3/WlwGyvfFbqqYEqWC3DnCaM9dnCXLSB11DdgmBKWmQ==]]></Encrypt>
     * </xml>
     * @param $postObj
     */
    private function _receiveLocationMessage($postObj)
    {
        //@todo 收到位置干点啥
        $label = $postObj->Label;
    }

    /**
     * 收到用户发送的视频消息
     * <xml>
     * <ToUserName><![CDATA[gh_0a9637eaee4e]]></ToUserName>
     * <FromUserName><![CDATA[ovLdf0YMgJrzOqCCMxP5mFMjTF2k]]></FromUserName>
     * <CreateTime>1542351285</CreateTime>
     * <MsgType><![CDATA[video]]></MsgType>
     * <MediaId><![CDATA[t7LjP19KW_vI0_dC_SoEtNAESTmoNlG4JNM20cUMmEr1c_v5J_xJ6LdvADfr_NuP]]></MediaId>
     * <ThumbMediaId><![CDATA[cS3bFfBy_HEcrrwMIKN9_NodPPDHFkWtI2pmxm-iJQMoWJvlVRsqM7jJKPfiz2oc]]></ThumbMediaId>
     * <MsgId>6624348328436887920</MsgId>
     * <Encrypt><![CDATA[OVBZqgfWVUo28u3svW5649itEyCGZ6sIkQmGg9ESmmiFDQKWKooIV0nt9JSVp69n8MYunE89hao3YsQdBJewODbKYFPjvOu5ZIivuV5RXa6RM6BW30a0ic1qnGLrVk2Bb61lV6Kt29xYr9HI+ABTpiSKB7i/6Rjo8ECL5imBqxP4rpMIrjG5Sn8XDdrGNrwaOjpzFsCw8ymhg3+avuXhIo62FuS0ORmaayQ+3h5Q6EzxkIGzX6pKGOyrJgQXsTjFbQPpExDp5IA6Qse5pKL07nWdU93mJUyRxaa9l0zzHPxuPleR7gymrdp/e3onqUDPuie02r3lHRYuROCZyyl1wMMUghu1dBhpDf0ISxPeZJlw4iG/mvjWkY1BQ3nfF23jDot108MEMMpXGDBXKYkruVr1EkCmAcPVf3s9ZwgFTYLg4cblYUpKSfp43nzq7V8eZEAOj+hzlegyDJ0TSuciJE2Tj/cafhvKPsTaXNQvEPSqYUoBW5qp4RAgG+kqyPKkhUGKrIJ6RCP4GNBERkkM+0YrOAhbMWyh3VAehSs9ccyYE2Yi3aFgvRDcOlO6ywI1234LRSQ7gAfbaSrjUpycgxhYc3YMN1YFWS8ivtITR2flMAYGD0uNrTmXAnHayHALvfOIuInurAp+8KEiSlkcfE8P+7any3uuFll9bzrOfuE=]]></Encrypt>
     * </xml>
     * @param $postObj
     */
    private function _receiveVideoMessage($postObj)
    {
        //@todo 收到视频干点啥
    }

    /**
     * 收到用户发送的语音消息
     * <xml>
     * <ToUserName><![CDATA[gh_0a9637eaee4e]]></ToUserName>
     * <FromUserName><![CDATA[ovLdf0YMgJrzOqCCMxP5mFMjTF2k]]></FromUserName>
     * <CreateTime>1542350673</CreateTime>
     * <MsgType><![CDATA[voice]]></MsgType>
     * <MediaId><![CDATA[G7eQJ0kXOO_LaAWm6VRUbvVh7blbTLtAlSQnG03pMThCkuP6HaRxxx3RxjoLJerT]]></MediaId>
     * <Format><![CDATA[amr]]></Format>
     * <MsgId>6624345699916902762</MsgId>
     * <Recognition><![CDATA[]]></Recognition>
     * <Encrypt><![CDATA[a08wEfyU+pdHNKznLF3F4nGpONbFe03xRUvf8Mm7vStR6LWZ4VEU6DSrSDor82A3xxvW1Ejp3EisCaPtGUGQQPM/KxXYpeeL1lQmBbciV+MiLRoPm1DPROVchVTc9YFOM3+FmIc9EEcss1nmz/VfbRzLn0QdT3vYyEp7i0jDXuUYQhgV5mnudfmUvBUyhhJv4KaB2gosfBjTzAZJuXmbTe/dYErmefGsFspjqvWf/9sozXkVOaBUqousBa0NWHpf+uLOXQyGgwzFtwQQVqe5qTSSCafTm9t1VY/mPfJdgK5vgFSmu+SFBl96s1qUx4yr5abaM+d+QgZ04acAJrA+QJsDAWC0xHM8rtE5JAhRNiKoKeFxtrY12INjenSzDcoXYCAOu+oABSD0Kmg4HXtpfHRIAkWv8ESXcucl6xSTh+ZraCPlGHbt9Mp+L4CKFQgtrYXfHeIoe+VOdtnRqK2IAiOsV92cdbvSLngzEmzF4E67GoLYCf0OLw7eFw9yCq0We7Kv9ARPX+OQaRq8j67kCRjVjkq1EqCTIs6gI68PTiODXRbt0cFBypQRy1TVYu5UurZWJMfI75MNOfOqYDeOyS0WVInzT6emxXriC0tRrsaR5/YESR1pNnYeanuT4qr0]]></Encrypt>
     * </xml>
     * @param $postObj
     */
    private function _receiveVoiceMessage($postObj)
    {
        //@todo 收到语音干点啥
    }

    /**
     * 接收到用户发送的图片
     * <xml>
     * <ToUserName><![CDATA[gh_0a9637eaee4e]]></ToUserName>
     * <FromUserName><![CDATA[ovLdf0YMgJrzOqCCMxP5mFMjTF2k]]></FromUserName>
     * <CreateTime>1542350105</CreateTime>
     * <MsgType><![CDATA[image]]></MsgType>
     * <PicUrl><![CDATA[http://mmbiz.qpic.cn/mmbiz_jpg/eXCcq9tjZ87J2pCnxrOPFj6RGTkTQriaRDkXFYvgvZJGT65LKzt3rZ3C5yJ6Dr3w47El4Yl67qFFicw4bmSAibqCg/0]]></PicUrl>
     * <MsgId>6624343260375478632</MsgId>
     * <MediaId><![CDATA[R-h2gnrxsvdfMYO649Ry-AvVlYqluINQW_YQK2cz3G69qWdeNMTg70YgidL3H3zS]]></MediaId>
     * <Encrypt><![CDATA[XlJs/pHcMh2cwVPwqIB1v68rymnGvthloy4Zbsq92AhsdgDGbUZt2DFpWKt7k2diozekcuOqBk4m55kOJTh2PXD7fFgdBLIcP7G+vpVJDf0y05learGNZQu7GT7TTQpqmzrl2gWfDJEQwaFfaXcAYr2wqeJbWd9uQRTKuR7EHeQIXGQAwNKm62odeK7wkgJMXMkBSiqUMX5Qecd4SAsvCD1iAK22K6t891nHCbegzrHWCV8JmHzgfrA3aecmNjR70KQRetzIS5EHXe32Z6XeRdjuoJDu2yTQ3HuBvkXgRnJyJvVyYnlcbCOgCpkOoWgcDnQMMSmXflo3fkWfBbl2BWY4S9kha1J0ZXyOMZjsjCEW29h+bV7EOoFXlD1CTaGwfFuLbMY5zDL/IgBVCyrZ0tvJ+5eZ74E7eOfRnbi+BTJuqw6iNi7+TtMNnr+r0vVqmk+EKyFbQOnGVreeXPU46S0Q1g396R7EwhHciIJ/9ghGOWihcwJQgRGKD4an++DvU4NFfrYxSSsnm5vWPIjXa4bb4O3Om9+46BqyvfdzPXo/gyO8Y2YDAtaBoOJA4Z/atcmLpThXQr+bkp0QPHJWeCir3oDx4DKgRnMYzvdmTaSBsasJbCNcBzM+R9/Q/LkAq2t3WuQDwOgSnE7dG4VWu5s9O53EGqYGmehkOh4fdnmfoY27m8evakpUGWMBihirONOzNEh/OnJQPxVKP9S4/A==]]></Encrypt>
     * </xml>
     * @param $postObj
     */
    private function _receiveImageMessage($postObj)
    {
        $picUrl = $postObj->PicUrl;
        $msgId = $postObj->MsgId;
        $mediaId = $postObj->MediaId;
        //@todo 收到图片干点啥
    }

    /**
     * 取消关注
     * @param $postObj
     * <xml>
     * <ToUserName><![CDATA[gh_0a9637eaee4e]]></ToUserName>
     * <FromUserName><![CDATA[ovLdf0YMgJrzOqCCMxP5mFMjTF2k]]></FromUserName>
     * <CreateTime>1542342235</CreateTime>
     * <MsgType><![CDATA[event]]></MsgType>
     * <Event><![CDATA[unsubscribe]]></Event>
     * <EventKey><![CDATA[]]></EventKey>
     * <Encrypt><![CDATA[x6q/v9iFweHeyGjIyKRqgOD/R9UEcfeJ8Zpezd7ZkMiZoTGF6y/90MyW5RTw+/9e2bZw4DbZHGz7A70q2A4wHCfzA0VfDSNlTDH/8jx/r4DSIykXDGP35KjYiV5ZUKKWG/anOIwfGCO+Lf7a3cj/NVT6dkyg7MprQ5gNrHwKVZxd+w3uhI4SoAd5+Pu6wWrjDsS1vXWdx+46wyp2Uwvz7OlbVxJmoyJ8u/VrWb6hGEd/whcvO8Y70d4M7hXMVECdL6nhQHCvWvDq3gJ/E6s4tFg/qJ43mqbVDaYwwroAC5ZwGErcuozxnABTX75LJ6VzAysG5m3LITGDx8TrH/RfgX6NuLwPcCVho+oWh4R4h1BLN0KFSJcOaZ8mvp8zYiPaOLlG3nATHhllj7yiBdlXf4H7ca0AedhrSpzM7hZbrnU=]]></Encrypt>
     * </xml>
     */
    private function _receiveUnSubscribe($postObj)
    {
        //@todo 取消关注后做点什么
        $openId = $postObj->FromUserName;
        $time = $postObj->CreateTime;
    }

    /**
     * 文本消息
     * @param $postObj
     */
    private function _sendTextMessage($postObj, $contentStr)
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
        $data = "----------time:" . date('Y-m-d H:i:s', time()) . "----------------------------↓\r\n\r\n$data\r\n\r\n";
        fwrite($fp, $data);
        fclose($fp);
    }

    public function clear()
    {
        file_put_contents('p.txt', '');
    }

}