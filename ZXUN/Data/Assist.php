<?php
namespace ZXUN\Data;

class Assist{
	
	/*
	 * 查询指定字段
	 * @param object $list [内容集合]
	 * @param string $field [查询字段]
	 * @return array
	 */
	function Select($field,$list) {

		//判断类型
		$str = "";
		foreach ($list as $row){
			switch(gettype($list)){
				case "object":
					$str = $str.($row->$field).",";
					break;
				default:
					$str = $str.$row[$field].",";
					break;
			}
		 }
		 //$str = serialize($list);
		 return substr($str,0,strlen($str)-1);
	}
}
?>