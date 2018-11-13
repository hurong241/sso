<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/11/2018
 * Time: 11:39 PM
 */
namespace ZXUN\Web;

/**
 * Class Page
 * @package ZXUN\Web
 * 页面基类
 * 以下类继承当前类
 * ZXUN/MVC/Core/View
 */
class Page extends Authorization
{
    function __construct()
    {
        //启动session会话
        if (!session_id())
            session_start();
        parent::__construct();
    }
}