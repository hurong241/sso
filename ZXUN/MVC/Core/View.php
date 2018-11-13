<?php
namespace ZXUN\MVC\Core;

use ZXUN\Configruation\Config;
use ZXUN\Web\Page;

/**
 * 视图基类
 */
class View extends Page
{
    protected $variables;
    protected $_controller;
    protected $_action;
    protected $_layout;//布局文件

    //Layout布局界面

    function __construct($controller, $action)
    {
        $this->variables  = array();
        //echo '#3';
        $this->_controller = strtolower($controller);
        $this->_action = strtolower($action);
    }

    // 分配变量
    public function param($name, $value)
    {
        //echo '#2';
        $this->variables[$name] = $value;
    }

    // 渲染显示
    public function render($__path)
    {
        //echo '#1';
        if($this->variables) {
            extract($this->variables);
        }

        $__config = new Config();
        $__layouts = $__config->Site->Reference->Layout;
        foreach($__layouts as $__item){
            switch($__item["type"]){
                case "page":
                    //如果指定绘制视图
                    if($__path){
                        $controller = 'app/view/'.$__path.'.php';
                    }
                    else{
                        $controller = 'app/view/' . $this->_controller . '/' . $this->_action . '.php';
                    }
                    //判断视图文件是否存在
                    if (is_file(PATH.$controller)) {
                        include (PATH.$controller);
                    } else {
                        echo "<h2>".$controller." 视图文件不存在</h2>";
                    }
                    break;
                default:
                    //if($this->_layout != NULL) {
                    $__page = PATH .$__item["path"];//判断视图文件是否存在
                    $__enabled = $__item["enabled"];
                    if($__enabled == "false"){
                        break;
                    }
                    if (is_file($__page)) {
                        include($__page);
                    } else {
                        echo "<h2>" . $__page . " 视图文件不存在</h2>";
                    }
                    //}
                    break;
            }
        }
    }

    //模板布局
    public function layout(){

        //echo '#4';
        //如果带参数
        if(func_num_args()>0){
            for($i=0;$i<func_num_args();$i++){
                $__value = func_get_arg($i);
                switch($__value){
                    case 0:
                        break;
                    default:
                        break;
                }
                //switch($value){

                //}
            }
            //echo '#5';
        }
        else{
            //echo '#6';
            $this->_layout = "_";
        }
        //ob_end_clean();
        //print_r($this->_layout == null);
        //exit;
    }
}
