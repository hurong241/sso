<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/11/2018
 * Time: 11:38 PM
 */

namespace ZXUN\Web;

/**
 * Class Authorization
 * @package ZXUN\Web
 * 帐号授权类
 */
class Authorization extends Security
{
    private $ACCOUNT_SESSION = "ZXUN_ACCOUNT_SESSION";//SESSION名称

    private $IsLogin = false;//是否已经登录
    private $Account = null;//帐号对象,用于存放登陆后帐号的Key=>Value值
    /**
     * Authorization constructor.
     * 构造方法
     */
    function __construct()
    {
        parent::__construct();
    }

    function __get($name)
    {
        //echo '__GET__';
        // TODO: Implement __get() method.
        switch ($name){
            case "IsLogin":
                return isset($_SESSION[$this->ACCOUNT_SESSION]);
                break;
            case "Account":
                if(isset($_SESSION[$this->ACCOUNT_SESSION]))
                    return $_SESSION[$this->ACCOUNT_SESSION];
                break;
        }
        //print_r($name);
    }

    function __set($name, $value)
    {
        // TODO: Implement __set() method.
        //echo '__SET__';
    }

    /**
     * 设置帐号
     */
    function SetAccount($account){
        //启动SESSION会话
        //$this->Account = $account;
        $_SESSION[$this->ACCOUNT_SESSION] = $account;
    }

    /**
     * 常规登录
     * by johnson
     */
    function Login(){
        $account = "";
        $password = "";
        if(func_num_args()>0){
            $account = func_get_arg(0);
            $password = func_get_arg(1);
        }
        else {
            $account = $_POST["account"];
            $password = $_POST["password"];
        }
        //获取用户
        $bus = new \ZXUN\Data\DataTable("mms_account");
        $param=(object)[];
        $param->where = "account=? and password = ?";
        $param->value = ["Account"=>$account,"Password"=>$password];
        $entity = $bus->Get($param);
        //设置会话
        if($entity) {
            $this->SetAccount($entity);;
            return $entity;
        }
        return false;
    }

    /**
     * 登出
     */
    function Logout(){
        $this->Account = null;
        //删除session
        if(isset($_SESSION[$this->ACCOUNT_SESSION])){}
            unset($_SESSION[$this->ACCOUNT_SESSION]);

    }
}