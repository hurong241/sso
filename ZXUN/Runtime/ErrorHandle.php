<?php
//错误处理类
class ErrorHandle{
	var $Url;
	function __construct($url){
		//错误页面
		$this->Url = $url;
		//error_reporting(E_ALL);
		set_error_handler(array($this, 'Handler'));
		register_shutdown_function(array($this, 'Capture'));
	}
	
	//捕获异常
	function Capture()
	{
		$e = error_get_last();
		switch($e['type']){
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
				Handler($e['type'],$e['message'],$e['file'],$e['line']);
				break;
		}
	}
	
	//处理异常
	function Handler($errno,$errstr,$errfile,$errline){
		$arr = [
				"Date"=>'['.date('Y-m-d H:i:s').']',
				"Message"=>$errstr,
				"Path"=>$errfile,
				"File"=>"",
				"Line"=>$errline,
		];
		//写入错误日志
		//格式 ：  时间 uri | 错误消息 文件位置 第几行
		//print_r($arr);
		//header('Location:'.$this->Url);
		//error_log(implode(' ',$arr)."<br/>",3,'./error.txt','extra');
		//echo implode(' ',$arr)."<br/>";
		exit;
	}
}

/*class Test{
	public function index(){
		//这里发生一个警告错误，出发errorHandler
		echo $undefinedVarible;
	}
}*/
/*$test = new Test();
////这里发生一个警告错误,被errorHandler 捕获
$test->index();
//发生致命错误，脚本停止运行触发 fatalErrorHandler
$test = new Tesdt();
$test->index();*/
?>