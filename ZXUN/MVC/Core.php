<?php

namespace ZXUN\MVC;
use ZXUN\Configruation\Config;

// 框架根目录
defined('CORE_PATH') or define('CORE_PATH', __DIR__);

//内核框架
class Core
{
    //安全监测
    public function __construct()
    {

    }

    /**
     * 启动入口
     */
    public function startup()
    {
        spl_autoload_register(array($this, 'loadClass'));
        $this->setReporting();
        $this->removeMagicQuotes();
        $this->unregisterGlobals();
        $this->route();
    }

    // 路由处理
    public function route()
    {
        //获取起始化控制器
        $config = new Config();
        $controllerName = $config->Site->MVC->Startup["controller"];
        $actionName = $config->Site->MVC->Startup["action"];

        $param = array();

        $url = $_SERVER['REQUEST_URI'];
        // 清除?之后的内容
        $position = strpos($url, '?');
        $url = $position === false ? $url : substr($url, 0, $position);
        // 删除前后的“/”
        $url = trim($url, '/');

        if ($url) {
            // 使用“/”分割字符串，并保存在数组中
            $urlArray = explode('/', $url);
            // 删除空的数组元素
            $urlArray = array_filter($urlArray);

            // 获取控制器名
            $controllerName = ucfirst($urlArray[0]);

            // 获取动作名
            array_shift($urlArray);
            $actionName = $urlArray ? $urlArray[0] : $actionName;

            // 获取URL参数
            array_shift($urlArray);
            $param = $urlArray ? $urlArray : array();
        }
        //如果是config配置目录则退出
        if($controllerName == "config")
        {
            exit("配置文件目录，禁止访问");
        }
        //判断控制器和操作是否存在
        $controller ='\\app\\controller\\{Controller}Controller';
        //路由操作类 获取 控制器
        $route = \Route::_handle($url);

        //如果存在路由
        if($route!=null){
            $controllerName = $route["controller"];
            $controller = str_replace("{Controller}",$route["controller"],$controller);
            $actionName = $route["action"];
        }
        else{
            $controller = str_replace("{Controller}",$controllerName,$controller);
        }
        //判断控制器类是否存在
        if (!class_exists($controller)) {
            exit("<b>ERROR:</b>".$controller . ' 控制器(Controller)不存在');
        }
        if (!method_exists($controller, $actionName)) {
            exit("<b>ERROR:</b>:".$actionName . ' 控制器方法(Action)不存在');
        }

        // 如果控制器和操作名存在，则实例化控制器，因为控制器对象里面
        // 还会用到控制器名和操作名，所以实例化的时候把他们俩的名称也
        // 传进去。结合Controller基类一起看
        $dispatch = new $controller($controllerName, $actionName);

        // $dispatch保存控制器实例化后的对象，我们就可以调用它的方法，
        // 也可以像方法中传入参数，以下等同于：$dispatch->$actionName($param)
        $result = call_user_func_array(array($dispatch, $actionName), $param);
        //如果返回值是字符串
        if(gettype($result)=="string"){
            echo $result;
        }
    }

    // 检测开发环境
    public function setReporting()
    {
        if (DEBUG === true) {
            error_reporting(E_ALL);
            ini_set('display_errors','On');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors','Off');
            ini_set('log_errors', 'On');
        }
    }

    // 删除敏感字符
    public function stripSlashesDeep($value)
    {
        $value = is_array($value) ? array_map(array($this, 'stripSlashesDeep'), $value) : stripslashes($value);
        return $value;
    }

    // 检测敏感字符并删除
    public function removeMagicQuotes()
    {
        if (get_magic_quotes_gpc()) {
            $_GET = isset($_GET) ? $this->stripSlashesDeep($_GET ) : '';
            $_POST = isset($_POST) ? $this->stripSlashesDeep($_POST ) : '';
            $_COOKIE = isset($_COOKIE) ? $this->stripSlashesDeep($_COOKIE) : '';
            $_SESSION = isset($_SESSION) ? $this->stripSlashesDeep($_SESSION) : '';
        }
    }

    // 检测自定义全局变量并移除。因为 register_globals 已经弃用，如果
    // 已经弃用的 register_globals 指令被设置为 on，那么局部变量也将
    // 在脚本的全局作用域中可用。 例如， $_POST['foo'] 也将以 $foo 的
    // 形式存在，这样写是不好的实现，会影响代码中的其他变量。 相关信息，
    // 参考: http://php.net/manual/zh/faq.using.php#faq.register-globals
    public function unregisterGlobals()
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

    // 自动加载类
    public function loadClass($className)
    {
        $classMap = $this->classMap();

        if (isset($classMap[$className])) {
            // 包含内核文件
            $file = $classMap[$className];
        } elseif (strpos($className, '\\') !== false) {
            // 包含应用（application目录）文件
            $file = PATH . str_replace('\\', '/', $className) . '.php';
            if (!is_file($file)) {
                return;
            }
        } else {
            return;
        }

        include $file;

        // 这里可以加入判断，如果名为$className的类、接口或者性状不存在，则在调试模式下抛出错误
    }

    // 内核文件命名空间映射关系
    protected function classMap()
    {
        return [
            'Controller' => CORE_PATH . '/Core/Controller.php',
            'Model' => CORE_PATH . '/Core/Model.php',
            'View' => CORE_PATH . '/Core/View.php',
            'DB' => $_SERVER["DOCUMENT_ROOT"] . '/ZXUN/Data/DataBase.php',
            'SQL' => $_SERVER["DOCUMENT_ROOT"] . '/ZXUN/Data/SQL.php',
        ];
    }
}