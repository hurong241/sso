<?php
namespace ZXUN\Data\Prototype\MSSQL;

require_once $_SERVER["DOCUMENT_ROOT"].'/ZXUN/Data/Prototype/IProvider.php';

class Provider implements \ZXUN\Data\Prototype\IProvider{
	
	function __construct(){
		//echo '__construct';
	}

	//分析条件
	function AnalysisWhere($where){
		
		return $where;
		
		$str ="";
		foreach($where as $item){
			//$str .=$item.' ';
		}
		while ($item = current($where)) {
			//if ($item == 'apple') {
				//echo key($item).'<br />';
			//}
			
			if(empty(key($where))==1){
				$str .=$item.' ';
			}
			else if(1==1){
				
			}
			else{
				$str .= key($where)."='".$item."' ";
			}
			
			//echo empty(key($where)).'=<br/>';
			next($where);
		}
		
		echo $str;
		echo '<br/><br/><br/>';
		return "RefCode='MainList'";
	}

	/// 绑定栏目名称
	function BuildColumn($column){
		$cols = explode(',',$column);
		$arr = array();
		foreach ($cols as $col){
			switch (strtolower($col)){
				case "group":
				case "top":
				case "where":
				case "order":
				case "insert":
					array_push($arr, '['.$col.']');
					break;
				default:
					array_push($arr, $col);
					break;
			}
		}
		return implode(',', $arr);
	}
	
    /// 绑定参数
    function BuildParameter($name, $value){
        	
    }
    /// 绑定参数名称
    function BuildParameterName($name){
        	
    }
}
?>