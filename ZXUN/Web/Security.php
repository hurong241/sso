<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 3/19/2018
 * Time: 12:16 PM
 */
namespace ZXUN\Web;

/**
 * Class Security
 * @package ZXUN\Web
 * 安全基类
 */
class Security{
    /*
     * 构造方法
     */
    function __construct()
    {
    }

    //敏感字符处理
    static function Sensitive(){
        $security = new Security();
        if (get_magic_quotes_gpc()) {
            $_GET = isset($_GET) ? $security->deleteSensitive($_GET ) : '';
            $_POST = isset($_POST) ? $security->deleteSensitive($_POST ) : '';
            $_COOKIE = isset($_COOKIE) ? $security->deleteSensitive($_COOKIE) : '';
            $_SESSION = isset($_SESSION) ? $security->deleteSensitive($_SESSION) : '';
        }
    }
    // 删除敏感字符
    private function deleteSensitive($value)
    {
        $value = is_array($value) ? array_map(array($this, 'stripSlashesDeep'), $value) : stripslashes($value);
        return $value;
    }


    // 检测自定义全局变量并移除。因为 register_globals 已经弃用，如果
    // 已经弃用的 register_globals 指令被设置为 on，那么局部变量也将
    // 在脚本的全局作用域中可用。 例如， $_POST['foo'] 也将以 $foo 的
    // 形式存在，这样写是不好的实现，会影响代码中的其他变量。 相关信息，
    // 参考: http://php.net/manual/zh/faq.using.php#faq.register-globals
    public static function UnRegisterGlobals()
    {
        if (ini_get('register_globals')) {
            $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
            foreach ($array as $value) {
                foreach ($GLOBALS[$value] as $key => $var) {
                    if ($var === $GLOBALS[$key]) {
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }
}
?>