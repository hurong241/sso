<?php
namespace ZXUN\Data;

use PDO;
use PDOException;

class DataBase{
	var $DataBase;//数据库对象
	var $CONN;//连接对象
	var $Config;//数据库配置
    var $Names;//获取数据库名称集合
	
	function __construct($dbname=""){
	    $this->Names = [];
        //获取数据库配置
        $config = new \ZXUN\Configruation\Config();

	    //读取所有DataBase的名称
        foreach($config->Data->DataBase as $key=>$value){
            array_push($this->Names,["Name"=>$value["Name"],"Type"=>$value["Type"],"Description"=>$value["Desc"]]);
        }

		if(strlen($dbname)==0){
			//echo '<error>No database connection name specified</error>';
            return;
		}
		//读取当前指定名称数据库
		$this->Config = $config->Data->DataBase[$dbname];
		
		$this->CONN = $this->Connection($this->Config);
		return $this->CONN;
	}
	
	//链接数据库
	function Connection($database){
		//加载数据引擎文件
		require_once 'DataBase/'.$database["Type"].'.php';
		//链接对象
		$db = null;
		//根据数据库类型加载处理业务
		switch (strtoupper($database["Type"])){
			case "MSSQL":
				$db  = new \ZXUN\Data\DataBase\SQLServer($database);
				break;
			case "SQLServer":
				$db  = new \ZXUN\Data\DataBase\SQLServer($database);
				break;
			case "MYSQL":
				$db  = new \ZXUN\Data\DataBase\MySQL($database);
				break;
			case "Oracle":
				$db  = new \ZXUN\Data\DataBase\Oracle($database);
				break;
		}
		$this->DataBase = $db;
		return  $db->Connection();
	}
	
	//执行方法
	function Execute($func,$sql,$param=null){
		//执行数据库执行业务
		$result = $this->DataBase->Execute($func,$sql,$param);
		require_once 'Prototype/'.$this->Config["Type"].'/ResultSet.php';

		//根据类型处理
		switch ($this->Config["Type"]){
			case "MSSQL":
				$bResultSet  = new \ZXUN\Data\Prototype\MSSQL\ResultSet($database);
				break;
			case "SQLServer":
				$bResultSet  = new \ZXUN\Data\Prototype\SQLServer\ResultSet($database);
				break;
			case "MySQL":
				$bResultSet  = new \ZXUN\Data\Prototype\MySQL\ResultSet();
				break;
			case "Oracle":
				$bResultSet  = new \ZXUN\Data\Prototype\Oracle\ResultSet($database);
				break;
		}
		return $bResultSet->$func($result);
		/*
		//后处理操作
		switch($func){
			case "Add"://添加
				break;
			case "Delete"://删除
				break;
			case "Modify"://修改
				break;
			case "Get"://单条数据
			case "GetList"://多条数据
			case "GetPageList"://分页数据
		}*/
		//echo 'Run='.$sql;
	}
	
	//关闭
	function Close(){
		sqlsrv_close($this->CONN);
	}
	
	//批处理执行
	function BatchExecute($sql){
		$result = $this->Execute(__FUNCTION__,$sql);
		return $result;
	}



	//PDO SQL方法--------------------------
    private static $pdo = null;
    public static function SQL($database)
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }
        try {

            $config = new \ZXUN\Configruation\Config();
            //空数据对象
            if($database) {
                //获取数据源
                //$dt = $config->Data->DataTable[$table];
                $db = $config->Data->DataBase[$database];//$dt["Database"]];
                //拆分数据源
                $conn = (object)[];
                $connStr = $db["ConnectionString"];
                $arrs = explode(';',$connStr);
                foreach ($arrs as $arr){
                    $item = explode('=',$arr);
                    $value = trim($item[1]);
                    //print_r($item[0]);
                    switch(strtolower(trim($item[0]))){
                        case "data source"://链接源
                            $conn->Source = $value;
                            break;
                        case "port"://端口
                            $conn->Port = $value;
                            break;
                        case "initial catalog"://数据库
                            $conn->DataBase = $value;
                            break;
                        case "uid":
                        case "user id"://帐号
                            $conn->User = $value;
                            break;
                        case "password"://密码
                            $conn->Password = $value;
                            break;
                    }
                }
                //print_r($conn);exit;
                //构造DSN
                $dsn    = sprintf('mysql:host=%s;dbname=%s;charset=utf8', $conn->Source,$conn->DataBase);
                $option = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);


//                return self::$pdo = new PDO($dsn, $conn->User, $conn->Password, $option);
                return new PDO($dsn, $conn->User, $conn->Password, $option);
            }
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}