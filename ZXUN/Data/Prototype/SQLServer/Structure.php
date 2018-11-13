<?php
namespace ZXUN\Data\Prototype\SQLServer;

require_once '/../IStructure.php';

class Structure implements \ZXUN\Data\Prototype\IStructure{
	
	function __construct(){
		//echo '__construct';
	}

	//增加模板
	function CreateInsert($tableName,&$params){
	
		//构造参数字符串
		$array = array();
		$vals = "";
		foreach($params as $key=>$value){
			$vals .= ",?";
			$fields .= ",$key";
			array_push($array, $value);
		}
		$vals = substr($vals,1,strlen($vals));
		$fields = substr($fields,1,strlen($fields));
	
		//构造SQL语句
		$sql = "insert into ".$tableName."(";
		$sql.=$fields;
		$sql.=") values(";
		$sql.=$vals;
		$sql.="); SELECT SCOPE_IDENTITY() ;";
	
		$params = $array;
		return $sql;
	}
	
	function CreateDelete($name,$where){
		$sql = "delete ".$name." where ";
		$sql.= $where;
		return $sql;
	}
	
	function CreateModify($name,$set,$where){
		$sql = "upload ".$name." set ";
		$sql.= $set;
		$sql.=" where ";
		$sql.= $where;
		return $sql;
	}
	
	//
	public function CreateSelect($name,$top, $column, $where, $group, $having,$order)
	{
		//构造字段关键词
		$column = $this->Provider->BuildColumn($column);
		
		
		if (!empty($top) && $top >0)
			$top = "TOP ".$top;
		else
			$top = "";
		if (empty($column))
			$column = "*";
		if (!empty($where))
			$where = "Where ".$where;
		if (!empty($group))
			$group = "Group By ".$group;
		if (!empty($having))
			$having = "Having ".$having;
		if (!empty($order))
			$order = "Order By ".$order;
		
		$sql = sprintf("SELECT %s %s FROM %s %s %s %s %s",
				$top,$column,$name,$where,$group,$having,$order);

		
		return $sql;
	}

	//
	public function CreatePageSelect($name,$page, $column, $where, $group, $having,$order)
	{
		//构造字段关键词
		$column = $this->Provider->BuildColumn($column);
		
		if (empty($column))
			$column = "*";
		
		$_count = ($page->size * ($page->index - 1));
		$sql = "WITH __tmp AS ( ";
		$sql.= "SELECT TOP (" . $_count . "+" . $page->size . ") ".$column.", ROW_NUMBER() ";
		$sql.= "OVER (Order By " . $order;
		if (empty($order))
			$sql.="ID";
		$sql.=") as DataRow_Pos ";
		$sql.="FROM ".$name." WITH(NOLOCK) ";
		if(count($where)>0)
    		$sql.="Where (". $where . ")";
		$sql.=") ";
		$sql.="SELECT * FROM __tmp where __tmp.DataRow_Pos > ". $_count;

		//echo $sql;
		//exit;
		return $sql;
	}
}
?>