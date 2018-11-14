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
        //$this->jiemi();

        $this->param('url', $authUrl);
        $this->view('usercenter.index');
    }
}