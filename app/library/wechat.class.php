<?php

/**
 * 说明:微信类
 * 文档地址：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140842
 *
 * User: 胡熔
 * Date: 2018/11/13
 * Time: 15:07
 */
class Wechat
{
    private $config;
    private $appId;
    private $appSecret;
    private $redirectUrl;//授权后回调地址

    public function __construct()
    {
        $this->config = include 'config/sso.php';
        $this->appId = $this->config['appId'];
        $this->appSecret = $this->config['appSecret'];
        $this->redirectUrl = $this->config['redirectUrl'];
    }

    /**
     * 获取授权url
     * param参数顺序不能乱
     *
     * @param string $userId 用户id，授权回调后会原样返回
     * @return string
     */
    public function getAuthorizeUrl($token, $type = 'oauth2')
    {
        if (stripos($this->redirectUrl, '?') !== false) {
            $redirectUrl = $this->redirectUrl . '&token=' . $token;
        } else {
            $redirectUrl = $this->redirectUrl . '?token=' . $token;
        }
        if ($type == 'pc') {
            //https://open.weixin.qq.com/connect/qrconnect?appid=&redirect_uri=&response_type=code&scope=snsapi_login#wechat_redirect
            //pc浏览器中:扫码,需要开放平台
            $api = 'https://open.weixin.qq.com/connect/qrconnect';
            $param = [
                'appid' => $this->appId,
                'redirect_uri' => $redirectUrl,
                'response_type' => 'code',
                'scope' => 'snsapi_login',
//                'state' => $userId,
            ];
//            $param['scope'] = 'snsapi_userinfo';//snsapi_login据说要开第三方平台
            $url = $api . '?' . http_build_query($param) . '#wechat_redirect';
        } else {
            //微信浏览器中：点击授权按钮
            $api = 'https://open.weixin.qq.com/connect/oauth2/authorize';
            $param = [
                'appid' => $this->appId,
                'redirect_uri' => $redirectUrl,//文档上说这里要用urlencode处理，实现加上了一直报:redirect_url域名与后台配置不一致,错误码:10003
                'response_type' => 'code',
                'scope' => 'snsapi_userinfo',
                'state' => '',//原样返回，这里弄成空，生成的二维码太细了怕扫不出来
            ];
            $url = $api . '?' . http_build_query($param) . '#wechat_redirect';
        }


        return $url;
    }

    /**
     * 根据code获得accessToken
     * @param string $code code作为换取access_token的票据，每次用户授权带上的code将不一样，code只能使用一次，5分钟未被使用自动过期。
     *
     * @return array
     * {
     * "access_token":"ACCESS_TOKEN",//网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
     * "expires_in":7200,
     * "refresh_token":"REFRESH_TOKEN",
     * "openid":"OPENID",//用户唯一标识，请注意，在未关注公众号时，用户访问公众号的网页，也会产生一个用户和公众号唯一的OpenID
     * "scope":"SCOPE" //用户授权的作用域，使用逗号（,）分隔
     * }
     */
    public function getAccessToken($code)
    {
        $api = 'https://api.weixin.qq.com/sns/oauth2/access_token';
        $param = [
            'appid' => $this->appId,
            'secret' => $this->appSecret,
            'code' => $code,
            'grant_type' => 'authorization_code'
        ];
        $result = $this->_getData($api, $param);

        return $result;
    }

    /**
     * 刷新access_token（如果需要）
     * 由于access_token拥有较短的有效期，当access_token超时后，可以使用refresh_token进行刷新，
     * refresh_token有效期为30天，当refresh_token失效之后，需要用户重新授权。
     *
     * @param string $refreshToken getAccessToken()获得的refresh_token
     * @return array 同getAccessToken()
     */
    public function refreshToken($refreshToken)
    {
        $api = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';
        $param = [
            'appid' => $this->appId,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken
        ];
        $result = $this->_getData($api, $param);

        return $result;
    }

    /**
     * 拉取用户信息(需getAuthorizeUrl中scope为 snsapi_userinfo)
     *
     * @param string $accessToken getAccessToken()中取的的
     * @param string $openId 用户openid
     * @param string $lang 返回国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语
     * @return mixed
     *
     * 参数    描述
     * openid    用户的唯一标识
     * nickname    用户昵称
     * sex    用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
     * province    用户个人资料填写的省份
     * city    普通用户个人资料填写的城市
     * country    国家，如中国为CN
     * headimgurl    用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
     * privilege    用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）
     * unionid    只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
     */
    public function getUserInfo($accessToken, $openId, $lang = 'zh_CN')
    {
        //@todo 这里似乎应该是supertoken
        $api = 'https://api.weixin.qq.com/sns/userinfo';
        $param = [
            'access_token' => $accessToken,
            'openid' => $openId,
            'lang' => $lang
        ];
        $result = $this->_getData($api, $param);

        return $result;
    }

    /**
     * 检验授权凭证（access_token）是否有效
     * @param string $accessToken getAccessToken()中取的的
     * @param string $openId 用户openid
     * @return mixed
     * 正确的JSON返回结果：{ "errcode":0,"errmsg":"ok"}
     */
    public function checkAccessToken($accessToken, $openId)
    {
        //@todo 这里似乎应该是supertoken
        $api = 'https://api.weixin.qq.com/sns/auth';
        $param = [
            'access_token' => $accessToken,
            'openid' => $openId,
        ];
        $result = $this->_getData($api, $param);

        return $result;
    }

    /**
     * 获取token,redis中有则从redis中取，否则调接口取并存入redis
     *
     * 此token是相对于整个app而言（如：消息推送时用)，和用户授权产生的token不同,需后台服务弄个定时任务在到期前去定时刷新
     * 其它子系统不要去刷新，以免其它系统token不一样导致失效
     *
     * @return mixed 0请求成功
     */
    public function getSuperToken()
    {
        $key = 'wechat_' . $this->appId . '_token';
        $redis = new Predis\Client($this->config['redis']);
        $token = $redis->get($key);
        if (!$token) {
            $api = 'https://api.weixin.qq.com/cgi-bin/token';
            $param = [
                'grant_type' => 'client_credential',
                'appid' => $this->appId,
                'secret' => $this->appSecret
            ];
            $result = $this->_getData($api, $param);
            $token = $result['access_token'];
            $expire = $result['expires_in'];
            $redis->setex($key, $expire, $token);
        }

        return $token;
    }

    /**
     * 获取数据
     * @param string $api api地址
     * @param array $param 参数
     * @return mixed
     */
    private function _getData($api, $param)
    {
        $url = $api . '?' . http_build_query($param);
        $result = json_decode(file_get_contents($url), true);

        return $result;
    }

    /**
     * 取微信服务器ip地址
     * 可用于安全验证，接收微信推送接口那里
     *
     * @param $accessToken
     * @return mixed
     */
    public function getWechatIps($accessToken)
    {
        $api = 'https://api.weixin.qq.com/cgi-bin/getcallbackip';
        $param = [
            'access_token' => $accessToken
        ];
        $result = $this->_getData($api, $param);

        return $result['ip_list'];
    }


    /**
     * 获取所有客服
     *
     * @param $accessToken
     * @return mixed
     */
    public function getCustomers($accessToken)
    {
        $api = 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist';
        $param = [
            'access_token' => $accessToken
        ];
        return $this->_getData($api, $param);
    }

    /**
     * 客服发送文本消息给用户
     *
     * @param $accessToken
     * @param $openId
     * @param $content
     */
    public function sendTextMessage($accessToken, $openId, $content)
    {
        $api = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $accessToken;
        $data = [
            'touser' => $openId,
            'msgtype' => 'text',
            'text' => [
                'content' => $content
            ]
        ];
        $data = json_encode($data);
        curlPost($api, $data);
    }
}