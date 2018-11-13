<?php
/**
 * 说明:注册
 *
 * User: 胡熔
 * Date: 2018/11/12
 * Time: 12:02
 */
namespace app\controller;

class RegisterController extends Controller{

    use UserTrait;

    public function __construct($controller, $action)
    {
        parent::__construct($controller, $action);
    }
    
    
    public function index(){
        $this->view('usercenter.register');
    }

    /**
     * 注册:提交
     */
    public function regCheck()
    {
        $this->_setTel();
        $this->_setSmsCode();
        $this->_setPwd();
        $this->_setPwd2();
        $this->_isPwdEqPwd2();
    }
}
