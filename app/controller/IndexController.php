<?php
/**
 * 用户中心首页
 * 
 * User: Administrator
 * Date: 13/8/2018
 * Time: 11:30 AM
 */

namespace app\controller;

use Wechat;

class IndexController extends CommonController
{

    public function __construct($controller, $action)
    {
        parent::__construct($controller, $action);
    }

    /**
     * 首页
     */
    public function index()
    {
        print_r($_COOKIE);
        echo '<hr>';
        $wechat = new Wechat();
        echo $authUrl = $wechat->getAuthorizeUrl(urlencode($this->token));
        echo '<hr>';
        $superToken=$wechat->getSuperToken();
        echo '超级token:'.$superToken.'<hr/>';
        echo '客服列表:<br/>';
        print_r($wechat->getCustomers($superToken));
        $content=rand(1,999);
        echo "发消息给用户,消息内容:$content<br/>";
        //@todo 测试用户
        $openId='ovLdf0YMgJrzOqCCMxP5mFMjTF2k';
        $wechat->sendTextMessage($superToken,$openId,$content);

        $this->param('url', $authUrl);
        $this->view('usercenter.index');
    }
}