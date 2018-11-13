<?php
namespace ZXUN\Text;

class JSON{
	function ClassToArray($obj){
		if(is_array($obj)){
			return $obj;
		}
		$_arr = is_object($obj)? get_object_vars($obj) :$obj;
		foreach ($_arr as $key => $val){
			$val=(is_array($val)) || is_object($val) ? object_to_array($val) :$val;
			$arr[$key] = $val;
		}
		
		return $arr;
		
	}
	/**
	 * 把对象转化为json
	 */
	function ClassToJson($obj){
		$arr2=ClassToArray($obj);//先把对象转化为数组
		return json_encode($arr2);
	}
}
?>