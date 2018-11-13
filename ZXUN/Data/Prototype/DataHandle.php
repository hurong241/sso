<?php
namespace ZXUN\Data\Prototype;

use Exception;

class DataHandle{
    var $Connection;//数据库连接对象
    var $DataTable;//数据库表对象
    var $Provider;//供应器
    var $Structure;//结构构造器
    var $Handle;//操作器

    function __construct($conn){
        $this->DataBase = $conn["DataBase"];
        $this->DataTable = $conn["DataTable"];
        $this->Connection = $conn["Connection"];

        //echo $this->DataBase->Type.'=========================';
        //根据类型处理相关类型数据
        switch ($this->DataBase["Type"]){
            case "MSSQL":
                require_once 'MSSQL/Provider.php';
                require_once 'MSSQL/Structure.php';
                $this->Structure  = new \ZXUN\Data\Prototype\MSSQL\Structure();
                $this->Structure->Provider  = new \ZXUN\Data\Prototype\MSSQL\Provider();
                break;
            case "SQLServer":
                require_once 'SQLServer/Provider.php';
                require_once 'SQLServer/Structure.php';
                $this->Structure  = new \ZXUN\Data\Prototype\SQLServer\Structure();
                $this->Structure->Provider  = new \ZXUN\Data\Prototype\SQLServer\Provider();
                break;
            case "MySQL":
                require_once 'MySQL/Provider.php';
                require_once 'MySQL/Structure.php';
                $this->Structure  = new \ZXUN\Data\Prototype\MySQL\Structure();
                $this->Structure->Provider  = new \ZXUN\Data\Prototype\MySQL\Provider();
                break;
            case "Oracle":
                //$this->Provider  = new \ZXUN\Data\Prototype\Oracle\Provider();
                //$this->Structure->Provider  = new \ZXUN\Data\Prototype\Oracle\Provider();
                break;
        }
    }

    //添加信息
    function Add($params){
        $sql = $this->Structure->CreateInsert($this->DataTable["Name"],$params);

        //print_r($params);exit;
        //执行/ZXUN/Data/DataBase的Execute方法
        $result = $this->Connection->Execute(__FUNCTION__,$sql,$params);

        return $result;
    }

    //删除信息
    function Delete($where,$value){
        $sql = $this->Structure->CreateDelete($this->DataTable["Name"],$where);
        $result = $this->Connection->Execute(__FUNCTION__,$sql,$value);
        return $result;
    }

    //修改信息
    function Update($param){
        $sql = $this->Structure->CreateModify($this->DataTable["Name"],$param->set,$param->where);
        $arr = array_merge($param->set,$param->value);
        //print_r($arr);exit;
        $result = $this->Connection->Execute(__FUNCTION__,$sql,$arr);
        return $result;
    }

    //获取数据集合
    function GetList($param){

        //$tableName,$top, $whereStr, $groupByStr, $havingStr, $orderByStr, $includeColumnsStr
        //
        //$this->Provider->AnalysisWhere($param->where);
        //echo gettype($param->column)=="NULL"?"0":$param->column;

        $sql = $this->Structure->CreateSelect(
            $this->DataTable["Name"],
            $param->top,
            empty($param->column)?null:$param->column,
            empty($param->where)?null:$param->where,
            empty($param->group)?null:$param->group,
            empty($param->having)?null:$param->having,
            empty($param->order)?null:$param->order
        );
        //echo $sql.'<br/>';
        //exit;
        //返回数据
        $arr = $param->value;
        if($param->top >0)
            array_push($arr,["Top"=>$param->top]);

        //print_r($arr);
        //exit;

        $result = $this->Connection->Execute(__FUNCTION__,$sql,$arr);
        //print_r($result);
        //exit;
        return $result;
    }

    //获取分页数据
    function GetPageList($param){

        $sql = $this->Structure->CreatePageSelect(
            $this->DataTable["Name"],
            $param->page,
            empty($param->column)?null:$param->column,
            empty($param->where)?null:$param->where,
            empty($param->group)?null:$param->group,
            empty($param->having)?null:$param->having,
            empty($param->order)?null:$param->order
        );

        $reply = $param->page["reply"];
        $param->page["index"] = $param->page["index"]-1;
        unset($param->page["reply"]);
        $arr = array_merge($param->value,$param->page);

        //print_r($arr);
        //echo '<br/>';
        //echo $sql;
        //exit;
        $result = $this->Connection->Execute(__FUNCTION__,$sql,$arr);
        var_dump($sql);
        //print_r($result);
        //exit;
        $page = [];
        $page["index"] = $param->page["index"];
        $page["size"] = $param->page["size"];
        //判断是否需要响应页数信息
        if(isset($reply) && $reply ==true){
            $res = $this->Count($param);
            $page["totaldata"]  = $res;
            $page["totalpage"] = intval($res/$page["size"]);
            //如果存在数据则加1
            if($page["totaldata"]>0)
                $page["totalpage"] = $page["totalpage"] +1;
        }
        //print_r($page);
        /*Reply
        PageIndex
        PageSize
        TotalData
        TotalPage*/
        //exit;
        $obj = (object)[];
        $obj->Result = $result;
        $obj->Page = $page;

        return $obj;
    }

    function Count($param){
        $param->top = 1;
        $param->column = "count(ID) as ID";
        $param->group = null;
        $param->having = null;
        $param->order = null;
        $result = $this->GetList($param);
        foreach($result as $row){
            return intval($row["ID"]);
        }
        return 0;
    }
}
?>