<?php
namespace ZXUN\Data\Prototype\MySQL;

require_once $_SERVER["DOCUMENT_ROOT"].'/ZXUN/Data/Prototype/IStructure.php';

class Structure implements \ZXUN\Data\Prototype\IStructure{
	
	function __construct(){
		//echo '__construct';
	}

	//增加模板
	function CreateInsert($tableName,$params){
        $FIELD = [];//固定字段
        $fields = [];//插入字段
        $fieldvalues = [];//插入字段及值
        $values = [];//插入值
	    $list = [];
        $many = false;//单条插入 false/ 批量 true
		foreach($params as $key=>$value){
		    //print_r($key); 序号
            if(gettype($value) == "array"){
                $many = true;//是否是多条插入
                $list[$key] = [];
                foreach($value as $k=>$v){
                    //print_r("批量插入:".$k.'='.$v.'<br/>');
                    $list[$key][$k] = $v;

                    //插入字段
                    if($key ==0){
                        array_push($FIELD,"`".$k."`");
                    }
                }
            }
            else{
                //print_r("单行插入:".$key.'='.$value.'<br/>');

                $list[$key] = $value;
                array_push($FIELD,"`".$key."`");
                array_push($fieldvalues,"?");
            }
		}
		//如果批量插入
        if($many){
		    foreach($list as $key=>$value){
		        $items = $list[$key];
		        $arrs= [];
		        foreach($items as $k=>$v){
		            array_push($fields,"@`".$k."_".$key."`");
		            array_push($fieldvalues,"@`".$k."_".$key."`='$v'");
		            array_push($arrs,'?');
                }
                array_push($values,"(".implode($arrs,',').")");
            }
        }
        else{
		    array_push($values,"(".implode($fieldvalues,',').")");
        }
		/*
		 * SET @Title='Title',@Summary='Summary',@Visit=1,@`Status`=2;
		 * PREPARE STMT FROM "Insert into os_edition(Title,Summary,Visit,Status) values(?,?,?,?)";
		 * EXECUTE STMT USING @Title,@Summary,@Visit,@`Status`;
		 */
		/*$sql = sprintf("SET ".implode($fieldvalues,',')).';';
		$sql .= "PREPARE STMT FROM \"Insert into ".$tableName."(".implode($FIELD,',').") values".implode($values,',')."\";";
		$sql .= "EXECUTE STMT USING ".implode($fields,',');
		*/
        $sql = "Insert into ".$tableName."(".implode($FIELD,',').") values".implode($values,',');

        //echo $sql;
        //exit;
		return $sql;
	}

	//删除
	function CreateDelete($name,$where){
		$sql = "delete from ".$name." where ";
		$sql.= $where;
		return $sql;
	}

	//修改
	function CreateModify($name,$set,$where){
	    $arr = [];
	    foreach ($set as $key=>$value){
	        array_push($arr,'`'.$key.'`=?');
        }
		$sql = "update ".$name." set ";
		$sql.= implode($arr,',');
		$sql.=" where ";
		$sql.= $where;
		return $sql;
	}

	//查询
	public function CreateSelect($name,$top, $column, $where, $group, $having,$order){

		//构造字段关键词
		//$column = $this->Provider->BuildColumn($column);
		
		if (!empty($top) && $top >0) {
            //$top = "LIMIT " . $top;
            $top = "LIMIT ?";
        }else
            $top = "";//"LIMIT ?";
		if (empty($column))
			$column = "*";
		else {
		    if(gettype($column)=="array")
                $column = implode($column, ',');
        }

		if (!empty($where))
			$where = "Where ".$where;
		if (!empty($group))
			$group = "Group By ".$group;
		if (!empty($having))
			$having = "Having ".$having;
		if (!empty($order))
			$order = "Order By ".$order;

			$sql = sprintf("SELECT %s FROM %s %s %s %s %s %s"
					,$column,$name,$where,$group,$having,$order,$top);

			//echo $sql;
			//exit;
			return $sql;
	}
	

	public function CreatePageSelect($name,$page, $column, $where, $group, $having,$order)
	{
		//构造字段关键词
		//$column = $this->Provider->BuildColumn($column);

        if (!empty($top) && $top >0) {
            //$top = "LIMIT " . $top;
            $top = "LIMIT ?";
        }
        else
            $top = "LIMIT ?";
        if (empty($column))
            $column = "*";
        else
            $column = implode($column,',');

        if (!empty($where))
            $where = "Where ".$where;
        if (!empty($group))
            $group = "Group By ".$group;
        if (!empty($having))
            $having = "Having ".$having;
        if (!empty($order))
            $order = "Order By ".$order;

        $sql = sprintf("SELECT %s FROM %s %s and ID>=(select id from $name limit ?, 1) %s %s %s %s"
            ,$column,$name,$where,$group,$having,$order,$top);

		//$sql = "SELECT ? FROM sys_region WHERE ? and ID >=(select id from sys_region limit ?, 1) limit ?";

        //echo $sql;
        //exit;



        return $sql;
	}
}
?>