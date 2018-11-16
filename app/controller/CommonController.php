<?php
/**
 * 说明:
 *
 * User: 胡熔
 * Date: 2018/11/7
 * Time: 10:18
 */
namespace app\controller;

use Predis;
use Aes;
use Wechat;

class CommonController extends Controller
{

    protected $config;
    protected $redis;
    private $userId;
    protected $token;

    public function __construct($controller, $action)
    {
        parent::__construct($controller, $action);
        date_default_timezone_set('PRC');
        $this->config = include 'config/sso.php';
        $this->redis = new Predis\Client($this->config['redis']);
        $this->_checkAccess();
    }

    /**
     * 检查登录是否合法，不合法退出，否则设置$userId
     */
    private function _checkAccess()
    {
        $token = !empty($_COOKIE['token'])?$_COOKIE['token']:'';
        if (!$token) {
            $this->_logout('');
        } else {
            //@todo这里考虑到安全性，可以将token解密验证一下，以免redis被攻击
            $uid = isLegalToken($token, $this->config['aes_key']);
            if (!$uid) {
                $this->_logout('');
            }
            $tokenValue = $this->redis->get($token);
            if (empty($tokenValue)) {
                //正常或过期退出：tokenValue==0或null
                $this->_logout($token);
            } else {
                //如果token值为1:表示token在子站中还在使用
                $redisExpire = $this->config['redis_expire'];
                $this->redis->expire($token, $redisExpire);
                setcookie('token', $token, time() + $redisExpire, '/', $this->config['sso_root_domain']);
                $this->userId = $uid;
                $this->token=$token;
            }
        }
    }

    /**
     * 清除全局会话并引导到登录页
     * 由于调用此方法的只有私有方法_checkAccess(),此方法已对token合法性进行了验证，这里不需要对token合法性进行验证
     *
     * @param string $token
     */
    private function _logout($token)
    {
        if ($token) {
            //其它页面的退出操作可能清除了此token
            $this->redis->setex($token, $this->config['redis_expire'], 0);
        }
        setcookie('token', '', time() - 3600, '/', $this->config['sso_root_domain']);
        $url = getHost() . '/login';
        redirect($url);
    }

    /**
     * 取登录用户信息
     */
    public function loginUser()
    {
        //@todo
        if ($this->userId) {
            return $this->userId;
        } else {
            return 0;
        }
    }

    private function qrCode()
    {
        //需要gd库支持
        $wechat = new Wechat();
        $authUrl = $wechat->getAuthorizeUrl($this->userId);
        include ROOT.'/app/library/phpqrcode.php';
        \QRcode::png($authUrl);
    }

}