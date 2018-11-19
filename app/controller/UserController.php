<?php
/**
 * 说明:用户注册，登录，找回密码相关
 *
 * User: 胡熔
 * Date: 2018/11/12
 * Time: 11:42
 */
namespace app\controller;
class UserController extends CommonController
{
    use UserTrait;

    public function __construct($controller, $action)
    {
        parent::__construct($controller, $action);
    }

    public function info()
    {
        $this->loginUser();
    }

    public function edit()
    {
        $post = $_POST;
        if (empty($post)) {
            responseError(600, '内容不能为空');
        }

        $this->_setNickName();
//        $this->_setName();
//        $this->_setEmail();
//        $this->_setTel();
//        $this->_setCompany();
//        $this->_setPosition();
//        $this->_setTrade();
//        $this->_setAddress();
//        $this->_setCompanyTel();
        echo $this->nickName;
        //@todo

    }

    public function setPassword()
    {
        $this->_setTel();
        $this->_setSmsCode();
        $this->_setPwd();
    }


}