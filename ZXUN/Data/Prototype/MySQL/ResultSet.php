<?php
namespace ZXUN\Data\Prototype\MySQL;

//MSSQL 结果集处理
class ResultSet{
	//增加结果集处理
	function Add($result){
	    if(gettype($result)=="integer"){
	        return $result;
        }
		$row = mssql_fetch_row($result);
		return $row[0];//mssql_get_field($result, 0);
	}

	//删除结果集处理
	function Delete($result){

	}

	//修改结果集处理
	function Update($result){

	}

	//获取结果集处理
	function Get($result){
		return $this->GetList($result);
	}

	//获取集合结果集处理
	function GetList($result){
	    return $result;
	    /*
		$data = array ();
		while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
			if(!empty($row["ID"])){
				$data[$row["ID"]] = $row;
			}
			else{
				array_push($data, $row);
			}
		}
		return $data;*/
	}
	
	function GetPageList($result){
		return $this->GetList($result);
	}
	
	
	//批处理执行
	function BatchExecute($result){
		//SQLSRV_FETCH_ASSOC
		//SQLSRV_FETCH_NUMERIC
		//SQLSRV_FETCH_BOTH
		$data = array();
		$index=1;
		do{
			$items = $this->GetList($result);
			array_push($data, $items);
		}while($row = mysqli_next_result($result));
		return $data;
	}
}
?>