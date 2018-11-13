<?php
/**
 * Created by PhpStorm.
 * User: johnson
 * Date: 3/18/18
 * Time: 12:52 AM
 * Version: 1.1
 */
//phpinfo();
//exit;
//define('ROOT',$_SERVER["DOCUMENT_ROOT"]);   //程序根目录
define('ROOT',dirname(__FILE__));   //程序根目录
define('PATH', __DIR__ . '/');              //程序当前目录
define('DEBUG', true);                      //调试模式

define("UPLOAD_TEMPLATE","/source/upload/template/");
define("EXPORT","/source/export/");

//框架配置文件路径 默认路径/config/
//define('CONFIG_PATH','');
require_once ROOT . '/app/common/function.php';//加载公共类库

require_once ROOT.'/ZXUN/MVC/Core.php';//加载MVC内核框架
require_once ROOT . '/ZXUN/MVC/Route.php';//加载MVC路由
require_once ROOT . '/ZXUN/MVC/Route/View.php';//加载MVC路由视图

require_once ROOT . '/config/route.php';//配置路由器
require ROOT . "/app/library/predis-1.1.1/autoload.php";
require ROOT . "/app/library/aes.class.php";
require ROOT . "/app/library/wechat.class.php";

//实例化框架类
$core = new ZXUN\MVC\Core();
$core->startup();