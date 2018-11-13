<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 3/19/2018
 * Time: 11:58 AM
 */

namespace ZXUN\Runtime;


class Loader
{
    static $CLASSMAP =[];
    public function LoadClass($classname){

        if (isset(Loader::$CLASSMAP[$classname])) {
            // 包含内核文件
            $file = Loader::$CLASSMAP[$classname];
        } elseif (strpos($classname, '\\') !== false) {
            // 包含应用（application目录）文件
            $file = $_SERVER["DOCUMENT_ROOT"].str_replace('\\', '/', $classname) . '.php';
            if (!is_file($file)) {
                return;
            }
        } else {
            return;
        }

        include $file;
        // 这里可以加入判断，如果名为$className的类、接口或者性状不存在，则在调试模式下抛出错误
    }
}