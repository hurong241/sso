<?php
//Linux平台下SQLServer链接方式

namespace ZXUN\Data\DataBase;

//Microsoft SQL Server 数据库对象
class SQLServer{
	var $DataBase;
	var $ConnInfo;
	var $Server;
	var $CONN;//链接对象
	
	//构造方法
	function __construct($db){
		
		$this->DataBase = $db;

		//链接变量
		$server = "";$userid = "";$password = "";$dbname = "";
		
		$connString = $this->DataBase->ConnectionString;
		$info = explode(';',$connString);
		foreach($info as $item){
			$str = explode('=',$item);
			$key = $str[0];
			$value = $str[1];
			switch(strtolower($key)){
				case  "server":
					$server = $value;
					break;
				case  "database":
					$dbname = $value;
					break;
				case "password":
				case "pwd":
					$password = $value;
					break;
				case "user id";
				case "uid";
					$userid = $value;
					break;
			}
		}
		
		/*
		echo $server.'<br/>';
		echo $userid.'<br/>';
		echo $password.'<br/>';
		echo $dbname.'<br/>';*/
		
		$this->Server = $server;
		$this->ConnInfo = array (
				"UID" => $userid,
				"PWD" => $password,
				"Database" => $dbname
		);
	}
	
	function Connection(){
		$this->CONN = mssql_connect  ( $this->Server,$this->ConnInfo["UID"],$this->ConnInfo["PWD"]);
		
		mssql_select_db($this->ConnInfo["Database"], $this->CONN);
		if ($this->CONN) {
			// echo "Connection Success";
		} else {
			echo "<error>Server ".$this->Server." Connection failed</error>";
			exit;
		}
		return $this->CONN;
	}

	function Execute($func,$sql,$param=null){
		try{
			if(!empty($_GET["type"])){
				//print_r($sql.'<br/>');
				//$sql = "upload cms_Article set ArticleID=123 where id=4356";
				//print_r($sql);
			}
			$result = mssql_query($sql,$this->CONN);
			if( $result === false ) {
			     die( print_r("MSSQL Execute 执行错误"));
			}
		}
		catch (Exception $e) {
			print $e->getMessage();
		}
		return $result;
	}
}
?>