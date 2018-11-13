<?php
/**
 * Created by PhpStorm.
 * User: johnson
 * Date: 3/17/18
 * Time: 9:39 PM
 */
namespace ZXUN\Configruation;

use DOMDocument;
use DOMXPath;

require_once 'Base.php';

class Site extends Base{

    var $MVC;
	private $xmlDoc;

	function __construct(){
		parent::__construct();
		parent::Top();

		$doc = new DOMDocument();
		if(defined("CONFIG_PATH"))
			$doc->load(ROOT.CONFIG_PATH.'/site.xml');
		else
			$doc->load(ROOT."/config/site.xml");

		//print_r($doc);exit;
		$this->xmlDoc = $doc;

		//$this->MVC["ABC"]=array("A"=>array("B"=>"C"));
        $nodes = $this->xmlDoc->documentElement->childNodes;
        $this->getNodes($nodes,function($nodes){
            foreach($nodes as $item){
                //文本节点跳出
                if($item->nodeName =="#text")
                    continue;
                //处理单个节点
                switch($item->nodeName){
                    case "MVC"://MVC控制枢纽
                        $this->MVC = (object)[];
                        $this->MVC->Startup = parent::getAttribute($item->getElementsByTagName("Startup")[0]);
                        break;
                    case "Reference"://引用管理
                        $this->Reference = (object)[];
                        //获取Reference子节点
                        $items = $item->childNodes;
                        $this->getNodes($items,function($nodes){
                            foreach($nodes as $item){
                                //文本节点跳出
                                if($item->nodeName =="#text")
                                    continue;
                                switch($item->nodeName){
                                    case "Layout"://布局文件
                                        $this->Reference->Layout = $this->getItems($item->getElementsByTagName("Item"));
                                        break;
                                }
                            }
                        });
                        break;
                }
            }
        });
	}

	function getNodes($nodes,$func){
	    $func($nodes);
    }

    function getItems($items){
	    //echo count($items);
        $arr = [];
        foreach($items as $item){
            array_push($arr,parent::getAttribute($item));
        }
        return $arr;
    }

    //开发环境
    public function Debug()
    {
        if (DEBUG === true) {
            error_reporting(E_ALL);
            ini_set('display_errors','On');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors','Off');
            ini_set('log_errors', 'On');
        }
    }
}