<?php 
class DataBaseConfig{
	var $Item;
        
   	//查找指定名称数据库
	function Find($p)
	{
		$result = 0;//ZXUN.Config.Data.DataBase.Item.Find(a => a.Name == p);
		//如果不为空
		if (_result != null)
			return _result;
		throw new Exception("数据库 [" + p + "] 不存在,请联系系统管理员");
	}
}

class DataBaseItem{

	//名称
	var $Name;
	
	//描述
	var $Description;
	
	var $ConnectionString;
	
	var $Type;
}

?>