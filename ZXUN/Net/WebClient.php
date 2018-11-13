<?php
namespace ZXUN\Net;

class WebClient{
    /**
     * 请求接口返回内容
     * @param  string $url [请求的URL地址]
     * @param  string $params [请求的参数]
     * @param  int $ipost [是否采用POST形式]
     * @return  string
     */
	var $Proxy;
	function Post($url,$params=false,$ispost=0){
		$params = http_build_query($params);

		$httpInfo = array();
		$ch = curl_init();
		
		curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
		curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
		curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if( $ispost )
		{
			curl_setopt( $ch , CURLOPT_POST , true );
			curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
			curl_setopt( $ch , CURLOPT_URL , $url );
		}
		else
		{
			if($params){
				curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
			}else{
				curl_setopt( $ch , CURLOPT_URL , $url);
			}
		}
		if(!empty($this->Proxy)){
			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式
			curl_setopt($ch, CURLOPT_PROXY, "112.65.219.72"); //代理服务器地址
			curl_setopt($ch, CURLOPT_PROXYPORT, 80); //代理服务器端口
		}
		$response = curl_exec( $ch );
		if ($response === FALSE) {
			//echo "cURL Error: " . curl_error($ch);
			return false;
		}
		$httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
		$httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
		curl_close( $ch );
		return $response;
	}
	
	//下载 $url链接,$file可选保存路径
	static function Download($url, $file="", $timeout=60) {
		$file = empty($file) ? pathinfo($url,PATHINFO_BASENAME) : $file;
		$dir = pathinfo($file,PATHINFO_DIRNAME);
		!is_dir($dir) && @mkdir($dir,0755,true);
		$url = str_replace(" ","%20",$url);
		
		if(function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$temp = curl_exec($ch);
			if(@file_put_contents($file, $temp) && !curl_error($ch)) {
				return $file;
			} else {
				return false;
			}
		} else {
			$opts = array(
					"http"=>array(
							"method"=>"GET",
							"header"=>"",
							"timeout"=>$timeout)
			);
			$context = stream_context_create($opts);
			if(@copy($url, $file, $context)) {
				//$http_response_header
				return $file;
			} else {
				return false;
			}
		}
	}
}
?>