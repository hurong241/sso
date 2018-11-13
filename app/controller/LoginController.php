<?php
/**
 * 说明:
 *
 * User: 胡熔
 * Date: 2018/11/7
 * Time: 11:03
 */

namespace app\controller;

use Predis;
use Aes;

class LoginController extends Controller
{

    private $config;
    private $redis;

    public function __construct($controller, $action)
    {
        parent::__construct($controller, $action);
        date_default_timezone_set('PRC');
        $this->config = include 'config/sso.php';
        $this->redis = new Predis\Client($this->config['redis']);
    }

    /**
     * 登录页面
     */
    public function index()
    {
        print_r($_SESSION);
//        $token = isset($_SESSION['token']) ? $_SESSION['token'] : '';
        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : '';
        $redirect = !empty($_GET['redirect']) ? trim($_GET['redirect']) : '';
        if ($redirect) {
            $this->_isLegalDomain($redirect);
        }
        if ($token) {
            //已登录,判断token合法性
            $uid = isLegalToken($token, $this->config['aes_key']);
            if (!$uid) {
                $this->logout();
            }
            $tokenValue = $this->redis->get($token);
            if (empty($tokenValue)) {
                $this->logout();
            }
            if ($redirect) {
                //从子站来的，跳回子站
                if (stripos($redirect, 'http://') === false) {
                    $redirect = 'http://' . $redirect;
                }
                if (stripos($redirect, '?') !== false) {
                    $redirect .= '&token=' . urlencode($token);
                } else {
                    $redirect .= '?token=' . urlencode($token);
                }
            } else {
                //否则到sso中心
                $redirect = getHost();
            }
            redirect($redirect);
        } else {
            //未登录
            $this->view('usercenter.login');
        }
    }

    /**
     * 非本公司域名不处理转发
     * @param $redirect
     */
    private function _isLegalDomain($redirect)
    {
        if (!empty($redirect) && (stripos($redirect, 'http://') === false)) {
            $redirect = 'http://' . $redirect;
        }
        $arr = parse_url($redirect);
        if (!in_array($arr['host'], $this->config['redirect_hosts'])) {
            die('非法域名');
        }
    }

    public function checklogin()
    {
        $post = $_POST;
        $username = $post['username'];
        $password = $post['password'];
        $redirect = $post['goto'];
        if ($username == 'test3' && $password == 'test3') {
            //@todo 临时检查,暂时写死
            $userId = 999;
            $token = $this->_createToken($userId);
            $tokenEncode = urlencode($token);
            $this->_saveCookieAndToken($token);
            if ($redirect) {
                if (stripos($redirect, 'http://') === false) {
                    $redirect = 'http://' . $redirect;
                }
                if (stripos($redirect, '?') !== false) {
                    $url = $redirect . '&token=' . $tokenEncode;
                } else {
                    $url = $redirect . '?token=' . $tokenEncode;
                }
            } else {
                $url = getHost();
            }

            redirect($url);
        }
    }

    /**
     * 创建token，如果cookie中已存在，则取出
     *
     * @return string
     */
    private function _createToken($userId)
    {
        if (isset($_COOKIE['token'])) {
            $token = $_COOKIE['token'];
            if (isLegalToken($token, $this->config['aes_key'])) {
                $token = $_COOKIE['token'];
            } else {
                $this->logout();
            }
        } else {
            $str = str_random(16);
            $key = $this->config['aes_key'];
            $data = [
                'str' => $str,
                'uid' => $userId,
                'key' => $key
            ];
            $str = json_encode($data);
            $aes = new \Aes($key);
            $token = $aes->encrypt($str);
        }

        return $token;
    }

    private function _saveCookieAndToken($token)
    {
//        if (preg_match('/^[0-9a-zA-Z\+\/]{32,128}$/', $token)) {
        $redisExpire = $this->config['redis_expire'];
        $sessionExpire = $this->config['session_expire'];
        $redis = new Predis\Client($this->config['redis']);
        $sessionId = session_id();
        $redis->setex($token, $redisExpire, $sessionId);
        //  setcookie('PHPSESSID', $sessionId, time() + $redisExpire, '/', $this->config['sso_root_domain']);
        setcookie('token', $token, time() + $redisExpire, '/', $this->config['sso_root_domain']);
//        $_SESSION['token'] = $token;
//        $_SESSION['expire'] = time() + $sessionExpire;
        // echo $token;exit();
//        }
    }


    public function logout()
    {
//        $token = $_SESSION['token'];
//        session_unset();
//        session_destroy();
        if (!empty($_GET['token'])) {
            //子站请求退出
            $token = trim($_GET['token']);
        } else {
            //sso主动退出
            $token = isset($_COOKIE['token']) ? trim($_COOKIE['token']) : '';
        }
        if (!empty($_GET['token'])) {
            //验证子站传过来的token合法性
            if (!isLegalToken($token,$this->config['aes_key'])) {
                die('非法操作');
            }
        }

        $redis = new Predis\Client($this->config['redis']);
        if ($token) {
            $redis->setex($token, $this->config['redis_expire'], 0);
        }
        //   setcookie('PHPSESSID', session_id(), time() - 3600, '/', $this->config['sso_root_domain']);
        setcookie('token', $this->token, time() - 3600, '/', $this->config['sso_root_domain']);
        $host = getHost();//带http://
        $redirect = !empty($_GET['redirect']) ? trim($_GET['redirect']) : '';
        if ($redirect) {
            if (stripos($redirect, 'http://') === false) {
                $redirect = 'http://' . $redirect;
            }
            $arr = parse_url($redirect);
            $refererDomain = $arr['host'];
            $this->_isLegalDomain($redirect);
            $host .= '/login?redirect=' . $refererDomain;
        }
        redirect($host);
    }


}