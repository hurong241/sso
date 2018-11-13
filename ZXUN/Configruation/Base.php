<?php
namespace ZXUN\Configruation;

//Plugin.config 文件操作基类
class Base{
	var $doc;
	
	//构造方法
	function __construct(){
		//echo 'Base<br/>';
	}
	
	function Top(){
		//echo 'Base-Top<br/>';
	}


	function getAttribute($node){
        $arr = [];
        foreach ($node->attributes as $attribute) {
            $arr[$attribute->nodeName] = $attribute->value;
        }
        return $arr;
    }

}
?>