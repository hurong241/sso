<?php
namespace ZXUN\Data;

use Exception;

require_once $_SERVER["DOCUMENT_ROOT"].'/ZXUN/Configruation/Data.php';
require_once 'DataBase.php';
require_once 'Prototype/DataHandle.php';

class DataTable{

	var $Connection;//链接源
	var $DataBase;//数据库对象
	var $config;//配置对象
	var $DataTable;//表对象
	var $DataHandle;//数据操作对象
	var $ErrorMessage = "数据操作类型 %s 方法%s参数不能为空";
	
	function __construct(){
		$num   =func_num_args();
		$params = func_get_args();
		switch ($num)
		{
			case 1:
				$name= $params[0];
				break;
			case 2:
				$name= $params[0];
				$db = $params[1];
				break;
		}
		$config = new \ZXUN\Configruation\Config();
		//空数据对象
		if(empty($db)){
			$this->DataTable = $config->Data->DataTable[$name];
		}
		else{
			$this->DataTable = $config->Data->GetDataTable($name,$db);
		}
		$this->DataBase = $config->Data->DataBase[$this->DataTable["Database"]];

		//构造链接源对象
		$this->Connection = new \ZXUN\Data\DataBase($this->DataTable["Database"]);
		$this->DataHandle = new \ZXUN\Data\Prototype\DataHandle(array(
				"Connection"=>$this->Connection,
				"DataBase"=>$this->DataBase,
				"DataTable"=>$this->DataTable
		));
	}

	/**
	 * 获取数据
	 * @param  object $param->column [查询字段]
	 * @param  object $param->where [*查询条件]
	 * @return  bool [true:添加成功 false:添加失败]
	 * @example (object)[]=>Field=Value
	 */
	function Get($param){

        $where = "";
        if(gettype($param) == "object") {
            if (!isset($param->where))
                throw new Exception(sprintf($this->ErrorMessage, __FUNCTION__, " where "));

            $where = $param->where;
        }
        else{
            $where = $param;
        }
        //如果没有条件值
        if (gettype($param) == "object" && !isset($param->value) && !isset($value)) {
            throw new Exception(sprintf($this->ErrorMessage, __FUNCTION__, " value "));
        }
        else{
            if(isset($value))
                $value = $value;
            else
                $value = $param->value;
        }

        $param->top = 1;
        $param->where = $where;
        $param->value = $value;
		$result = $this->GetList($param);
		return current($result);
	}

	/**
	 * GetList 获取数据集合
	 * @param  object $param->top [*查询数量 0:代表查询所有]
	 * @param  object $param->column [查询字段]
	 * @param  object $param->where [查询条件]
	 * @param  object $param->group [查询分组]
	 * @param  object $param->having [分组条件]
	 * @param  object $param->order [排序]
	 * @return  bool [true:添加成功 false:添加失败]
	 * @example (object)[]=>Field=Value
	 */
	function GetList($param){
        if(!isset($param->top))
            throw new Exception(sprintf($this->ErrorMessage,__FUNCTION__," top "));

        if(!isset($param->where))
            throw new Exception(sprintf($this->ErrorMessage,__FUNCTION__," where "));

        if(!isset($param->value))
            throw new Exception(sprintf($this->ErrorMessage,__FUNCTION__," value "));

		return $this->DataHandle->GetList($param);
	}
	/**
	 * GetPageList 获取分页数据集合
	 * 除了特有的 page 属性 其他与 GetList 属性相同
	 * @param object $param->page [*分页信息]
     * @param $param->page->index = [int], size = [int],replay=[bool]
	 */
	function GetPageList($param){
		if(!isset($param->page))
			throw new Exception(sprintf($this->ErrorMessage,__FUNCTION__," page "));

		//验证
		switch(gettype($param->page)){
			case "object":
				break;
            case "array":
                break;
			default:
			case "string":
				$page = (object)[];
				$strs = explode(",",$param->page);
				foreach($strs as $row){
					$item = explode("=", $row);
					if($item[0]=="reply")
						$page->$item[0] = $item[1];
					else
						$page->$item[0] = intval($item[1]);
				}
				//分页大小不能超过100
				if($page->size > 100){
					throw new Exception("分页大小 Size 属性不能大于 100");
				}
				$param->page = $page;
				break;
		}
		
		return $this->DataHandle->GetPageList($param);
	}
	
    /**
     * 添加数据
     * @param  object $params [参数信息 ]
     * @param ["ID"=>0] 单条数组数据插入
     * @param [["ID"=>0],["ID"=>1]] 批量数组数据插入
     * @return  bool [true:添加成功 false:添加失败]
     * @example (object)[]=>Field=Value
     */
	function Add($param){
	    if(gettype($param)=="array")
        {

        }
		else if(empty(get_object_vars($param)))
			throw new Exception(sprintf($this->ErrorMessage,__FUNCTION__,""));
		return $this->DataHandle->Add($param);
	}

	/**
	 * 更新数据
	 * @param  object $param [修改字段]
	 * @param  string where [*条件]
	 * @return  bool [true:修改成功 false:修改失败]
	 * @example (object)[]=>Field=Value
	 */
	function Update($param){

        //if(empty(get_object_vars($param)))
            //throw new Exception(sprintf($this->ErrorMessage,__FUNCTION__,""));
        if (!isset($param->where))
            throw new Exception(sprintf($this->ErrorMessage, __FUNCTION__, " where "));
        if (!isset($param->value))
            throw new Exception(sprintf($this->ErrorMessage, __FUNCTION__, " value "));
        if (!isset($param->set))
            throw new Exception(sprintf($this->ErrorMessage, __FUNCTION__, " set "));

        return $this->DataHandle->Update($param);
	}

	/**
     * 删除数据
	 * @param  object $param->where [*条件]
	 * @return  bool [true:删除成功 false:删除失败]
	 * @example (object)[]=>Field=Value
	 */
	function Delete($param,$value=null){
	    $where = "";
	    if(gettype($param) == "object") {
            if (!isset($param->where))
                throw new Exception(sprintf($this->ErrorMessage, __FUNCTION__, " where "));

            $where = $param->where;
        }
        else{
            $where = $param;
        }
        //如果没有条件值
        if (gettype($param) == "object" && !isset($param->value) && !isset($value)) {
            throw new Exception(sprintf($this->ErrorMessage, __FUNCTION__, " value "));
        }
        else{
	        if(isset($value))
	            $value = $value;
            else
	            $value = $param->value;
        }
		return $this->DataHandle->Delete($where,$value);
	}
}
?>