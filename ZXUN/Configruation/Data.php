<?php
namespace ZXUN\Configruation;

use DOMDocument;
use DOMXPath;

require_once 'Base.php';


class Data extends Base{
	
	var $DataBase;
	var $DataTable;
	var $StoredProcedure;
	private $xmlDoc;
	private $xmlPath;
	
	function __construct(){
		parent::__construct();
		parent::Top();

		$doc = new DOMDocument();
		if(defined("CONFIG_PATH")) {
            $doc->load(ROOT . CONFIG_PATH . '/data.xml');
        }
		else {
            $doc->load(ROOT . "/config/data.xml");
        }
		
		$this->xmlDoc = $doc;
		$this->xmlPath = new DOMXPath($doc);

		//节点结合对象
		$arrs = [
			"DataBase"=>array("name","type","desc","connectionString"),
			"DataTable"=>array("name","desc","status","database"),
			"StoredProcedure"=>array("name","params","desc","status","database")
		];

		foreach($arrs as $key=>$value){
			switch($key){
				case "DataBase"://数据库集合
					$this->DataBase = $this->getItems($key,$value);
					break;
				case "DataTable"://数据表集合
                    $this->DataTable = $this->getItems($key,$value);
					break;
				case "StoredProcedure"://存储集合
                    $this->StoredProcedure = $this->getItems($key,$value);
					break;
			}
		}
	}

	//获取节点项目集合
	private function getItems($key,$values){
        $nodes = $this->xmlPath->query('//Configuration/'.$key.'/Item');

        $arrs = [];
        foreach($nodes as $item){
            $arr = [];
        	foreach($values as $value){
                $arr[ucfirst($value)] =$item->getAttribute($value);
			}
			$arrs[$arr["Name"]] = $arr;
        }
        return $arrs;
	}

}
?>
