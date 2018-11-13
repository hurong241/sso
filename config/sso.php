<?php
/**
 * 说明:
 *
 * User: 胡熔
 * Date: 2018/11/7
 * Time: 12:10
 */
return [
    'redis'=>'tcp://127.0.0.1:6379',
    'redis_expire'=>86400*2,//redis过期时间：秒,建议设置一天以上,子站点务必设置为一样
    'session_expire'=>3,//redis过期时间：秒,要比redis_expire小一个数量级
    'aes_key'=>'Http://www.yunIndex.com@4806',//aes加密Key
    'sso_root_domain'=>'sso.com',//sso中心根域名
    'redirect_hosts'=>[
        'www.a.com',
        'www.b.com',
        'www.sso.com',
    ],//允许的子站点域名
    'appId'=>'wxfa51366c2df1834e',//微信公众号appid:开发->基本配置中
    'appSecret'=>'f8518afbaf22a087793f87edcea225c1',//微信公众号appid:开发->基本配置中
    'redirectUrl'=>'http://user.yunindex.com/auth/token',//微信公众号appid:用户授权成功后的回调地址
    'token'=>'BD15CAB01AB941028F49AF1D61B57723',
    'EncodingAESKey'=>'PmQ1PaGPXJaG575jIbRsOoLpaDvfc2LbPRAKUe5Rncr'
];