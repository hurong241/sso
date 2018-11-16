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
        $superToken = $wechat->getSuperToken();
        echo '超级token:' . $superToken . '<hr/>';
        echo '客服列表:<hr/>';
        print_r($wechat->getCustomers($superToken));
        echo '<hr/>';
        //发消息给用户
        $content = rand(1, 999);
        echo "发消息给用户,消息内容:$content<hr/>";
        //@todo 测试用户
        $openId = 'ovLdf0YMgJrzOqCCMxP5mFMjTF2k';
        $customerAccount = 'kf2001@QHQYKJ';//客服帐号
        $mediaId='R-h2gnrxsvdfMYO649Ry-AvVlYqluINQW_YQK2cz3G69qWdeNMTg70YgidL3H3zS';
        //@todo 以上测试数据
        $wechat->customerSendText($superToken, $openId, $content, $customerAccount);
        $wechat->customerSendPic($superToken,$openId,$mediaId,$customerAccount);

        $this->param('url', $authUrl);
        $this->view('usercenter.index');
    }
}