<?php
namespace ZXUN\Configruation;

class Config{

	var $Site;// 站点配置
	var $Url;// 站点Url
	var $Data;// 数据配置文件

    private static $site =null;
    private static $url =null;
    private static $data =null;

        //构造方法
	function __construct(){
	    //初始化数据
        if (self::$data == null) {
            self::$data= new \ZXUN\Configruation\Data();
        }
        if (self::$site == null) {
            self::$site = new \ZXUN\Configruation\Site();
        }

        $this->Data = self::$data;
        $this->Site = self::$site;
	}
}
?>