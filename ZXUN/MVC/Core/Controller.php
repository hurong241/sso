<?php
namespace ZXUN\MVC\Core;
use ZXUN\Web\Page;

/**
 * 控制器基类
 */
class Controller extends Page
{
    protected $_controller;
    protected $_action;
    protected $_view;

    /**
     * Controller constructor.
     * @param $controller
     * @param $action
     * 构造函数
     */
    public function __construct($controller, $action)
    {
        //调用上级构造方法
        parent::__construct();

        $this->_controller = $controller;
        $this->_action = $action;
        $this->_view = new View($controller, $action);
    }

    //添加变量
    public function param($name, $value)
    {
        $this->_view->param($name, $value);
    }

    // 渲染视图
    public function view()
    {
        $page = null;
        //匿名参数
        if(func_num_args()>0){
            for($i=0;$i<func_num_args();$i++){
                $items = func_get_args()[$i];
                $type = gettype($items);
                switch($type){
                    case "array"://参数类型
                        foreach ($items as $key=>$value){
                            $this->param($key,$value);
                        }
                        break;
                    case "string"://指定路径类型
                        $page = str_replace(".","/",$items);
                        break;
                }
            }
        }
        $this->_view->render($page);
    }

    //重定向
    public function redirect($path){
        header("Location:".$path);
    }
}