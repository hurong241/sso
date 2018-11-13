<?php
//Windows平台下SQLServer链接方式

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
		$info = split(';',$connString);
		foreach($info as $item){
			$str = split('=',$item);
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
		$this->CONN = sqlsrv_connect ( $this->Server, $this->ConnInfo);
		if ($this->CONN) {
			// echo "Connection Success";
		} else {
			echo "<error>Server ".$this->Server." Connection failed</error>";
			exit;
		}
		return $this->CONN;
	}

	function Execute($sql,$param=null){
		//print_r($param);
		//exit;
		$result = sqlsrv_query($this->CONN, $sql,$param);
		if( $result === false ) {
		     die( print_r( sqlsrv_errors(), true));
		}
		//exit;
		return $result;
	}
}
?>