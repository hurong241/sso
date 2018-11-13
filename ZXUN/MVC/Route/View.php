<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/4/2018
 * Time: 2:33 PM
 */
//用于路由器中 function return view方法
function view($path){
    //判断路径类型

    //正常的页面路径 指向page目录下静态
    if(substr($path,0,1)=="/"){
        echo '页面路径';
    }
    else{
        //mvc view路径
        //echo 'mvc路径';
        $view = new \ZXUN\MVC\Core\View("","");
        $view->render($path);
    }
}