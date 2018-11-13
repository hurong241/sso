<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/3/2018
 * Time: 4:57 PM
 */

/**
 * Class Route
 * @package ZXUN\MVC
 * 路由方法
 */
class Route
{
    /**
     * @var array 私有集合
     */
    public static $GETLIST = [];
    public static $POSTLIST=[];
    public static $GROUP = null;//分组前缀

    /**
     * 设置GET路由
     */
    public static function get($path,$params){
        self::_collect(__FUNCTION__,$path,$params);
    }
    /**
     * 设置POST路由
     */
    public static function post($path,$params){
        self::_collect(__FUNCTION__,$path,$params);
    }
    /**
     * 设置分组路由
     */
    public static function group($path,$params){
        self::$GROUP = $path;
        $params();
        self::$GROUP = null;//重新清空
    }

    /**
     * 私有
     * 核心处理类
     * by johnson
     */
    private static function _collect($method,$path,$params){
        if($path == "/"){
            $path = "";
        }
        if(isset(self::$GROUP)){
            $path = self::$GROUP.$path;
        }
        if(substr($path,0,1)=="/"){
            $path = substr($path,1);
        }
        switch($method){
            case "get":
                self::$GETLIST[$path] = $params;
                break;
            case "post":
                self::$POSTLIST[$path] = $params;
                break;
        }
    }

    /**
     * 控制器路由操作类
     * by johnson
     */
    static function _handle($path){
        //获取路由标记
        $item = null;
        switch($_SERVER['REQUEST_METHOD']){
            case "GET":
                //如果没有路由
                if(!isset(self::$GETLIST[$path]))
                    return;
                //有路由
                $item = self::$GETLIST[$path];
                break;
            case "POST":
                //如果没有路由
                if(!isset(self::$POSTLIST[$path]))
                    return;
                //有路由
                $item = self::$POSTLIST[$path];
                break;
        }
        //如果是个方法
        if(gettype($item) == "object"){
            $item();
            exit;
        }
        //如果空则返回空
        if(!isset($item)){
            return null;
        }
        //根据类型处理业务
        switch(gettype($item)){
            case "string"://控制器映射 如 控制器@方法
                $sp = explode("@",$item);
                return ["controller"=>$sp[0],"action"=>$sp[1]];
                break;
            case "":
                break;
        }
    }
}