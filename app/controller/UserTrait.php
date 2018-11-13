<?php
/**
 * 说明:用户信息相关字段，提高代码复用性
 *
 * User: 胡熔
 * Date: 2018/11/12
 * Time: 11:55
 */
namespace app\controller;
trait UserTrait
{

    private $tel;//手机号
    private $smsCode;//手机验证码
    private $username;//用户名
    private $pwd;//密码
    private $pwd2;//确认密码
    private $nickName;//昵称
    private $name;//姓名
    private $email;//邮箱
    private $company;//公司名
    private $position;//职位
    private $trade;//行业
    private $address;//地址
    private $companyTel;//公司电话

    private function _setTel()
    {
        $tel = !empty($_POST['tel']) ? trim($_POST['tel']) : '';
        if (!preg_match('/^1\d{10}$/', $tel)) {
            responseError(601, '手机号不正确');
        }
        $this->tel = $tel;
    }

    private function _setSmsCode()
    {
        $code = !empty($_POST['sms_code']) ? trim($_POST['sms_code']) : '';
        if (!preg_match('/^[a-zA-Z0-9]{6}$/', $code)) {
            responseError(602, '手机验证码不正确');
        }
        $this->smsCode = $code;
    }

    private function _setPwd()
    {
        $pwd = !empty($_POST['pwd']) ? trim($_POST['pwd']) : '';
        if (!preg_match('/^[a-zA-Z0-9]{8,30}$/', $pwd)) {
            responseError(603, '密码格式不正确');
        }
        $this->pwd = $pwd;
    }

    private function _setPwd2()
    {
        $pwd2 = !empty($_POST['pwd2']) ? trim($_POST['pwd2']) : '';
        if (!preg_match('/^[a-zA-Z0-9]{8,30}$/', $pwd2)) {
            responseError(604, '确认密码格式不正确');
        }
        $this->pwd2 = $pwd2;
    }

    private function _isPwdEqPwd2()
    {
        if ($this->pwd != $this->pwd2) {
            $this->pwd = null;
            $this->pwd2 = null;
            responseError(605, '两次输入的密码不一样');
        }
    }

    private function _setNickName()
    {
        $nickName = !empty($_POST['nick_name']) ? trim($_POST['nick_name']) : '';
        if (!empty($nickName) && (strlen($nickName)) > 16) {
            responseError(606, '昵称16个字符以内');
        }
        $this->nickName = $nickName;
    }

    private function _setName()
    {
        $name = !empty($_POST['name']) ? trim($_POST['name']) : '';
        if (!empty($name) && (strlen($name)) > 16) {
            responseError(607, '姓名16个字符以内');
        }
        $this->name = $name;
    }

    private function _setEmail()
    {
        $email = !empty($_POST['email']) ? trim($_POST['email']) : '';
        if (!empty($email) && (!preg_match('/^\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}$/', $email))) {
            responseError(608, '邮箱格式不正确');
        }
        $this->email = $email;
    }

    private function _setCompany()
    {
        $company = !empty($_POST['company']) ? trim($_POST['company']) : '';
        if (!empty($company) && (strlen($company)) > 200) {
            responseError(609, '公司名称200个字符以内');
        }
        $this->company = $company;
    }

    private function _setPosition()
    {
        $position = !empty($_POST['position']) ? trim($_POST['position']) : '';
        if (!empty($position) && (strlen($position)) > 16) {
            responseError(610, '职位16个字符以内');
        }
        $this->position = $position;
    }

    private function _setTrade()
    {
        $trade = !empty($_POST['trade']) ? trim($_POST['trade']) : '';
        if (!empty($trade) && (strlen($trade)) > 200) {
            responseError(611, '行业200个字符以内');
        }
        $this->trade = $trade;
    }

    private function _setAddress()
    {
        $address = !empty($_POST['address']) ? trim($_POST['address']) : '';
        if (!empty($address) && (strlen($address)) > 200) {
            responseError(612, '地址200个字符以内');
        }
        $this->address = $address;
    }

    private function _setCompanyTel()
    {
        $companyTel = !empty($_POST['company_tel']) ? trim($_POST['company_tel']) : '';
        if (!empty($companyTel) && (!preg_match('/^[0-9-()（）]{7,18}$/', $companyTel))) {
            responseError(613, '公司信息->联系电话格式不正确');
        }
        $this->companyTel = $companyTel;
    }
}