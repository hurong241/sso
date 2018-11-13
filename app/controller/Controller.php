<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/18/2018
 * Time: 4:34 PM
 */

namespace app\controller;

use app\handler\ErrorHandler;
use app\handler\ExceptionHandler;

/**
 * Class Controller
 * @package app\controller
 * 主核心容器
 */
class Controller extends \ZXUN\MVC\Core\Controller
{
    protected $_controller;
    protected $_action;
    
    public function __construct($controller, $action)
    {
        $this->_controller = $controller;
        $this->_action = $action;
        parent::__construct($controller, $action);
        /**
         * 全局错误示例示例
         * Create By tangzx 2018-04-24 15:19:21
         */
        // set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        //     echo '<pre>';
        //     var_dump(compact('errno', 'errstr', 'errfile', 'errline'));
        //     die('</pre>');
        // });
        /**
         * 全局异常捕获示例
         * Create By tangzx 2018-04-24 15:19:27
         */
        // set_exception_handler(function ($exception) {
        //     echo '<pre>';
        //     var_dump($exception);
        //     die('</pre>');
        // });
    }
    // protected static function setErrorHandler(ErrorHandler $handler)
    // {
    //     set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($handler) {
    //         $handler::handler($errno, $errstr, $errfile, $errline);
    //     });
    // }
    protected static function setExceptionHandler(ExceptionHandler $handler)
    {
        set_exception_handler(function ($exception) use ($handler) {
            $handler::handler($exception);
        });
    }
    
    public function __destruct() {
        // 网站暂停统计敬告
        $controller = strtolower($this->_controller);
        if ( ! isset($_SESSION['ZXUN_ACCOUNT_SESSION']['isTmpUser']) && 
                in_array($controller, ['flow', 'source', 'transform', 'visit', 'visitor', 'main'])) {
            $this->view('account.notice');
        }
    }
}
