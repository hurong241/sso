<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 5/4/2018
 * Time: 5:50 PM
 */

namespace ZXUN\UI;

class Page {
    var $Setting;

    public static function View($param){
        //获取
        $page = null;
        $params = func_get_args();
        $param=(object)[];
        switch (func_num_args()){
            case 2:
                //匹配 ZXUN\UI\Page::View($page,"?page={index}");
                $page = $params[0];
                $param->Url = $params[1];
                break;
            case 4:
                //匹配 ZXUN\UI\Page::View($page,10,10,"?page={index}");
                //匹配 ZXUN\UI\Page::View(1,10,100,"?page={index}");
                if(gettype($params[0]) == "array"){
                    $page = $params[0];
                }
                else{
                    $param->PageIndex =$params[0];
                }
                $param->AfterCount = $params[1];
                $param->BeforeCount = $params[2];
                $param->Url = $params[3];
                break;
            case 6:
                //匹配 ZXUN\UI\Page::View(1,10,100,10,10,"?page={index}");
                $param->PageIndex =$params[0];
                $param->PageSize = $params[1];
                $param->DataCount = $params[2];
                $param->AfterCount = $params[3];
                $param->BeforeCount = $params[4];
                $param->Url = $params[5];
                break;
        }
        if($page){
            $param->PageIndex = $page["index"];
            $param->PageSize = $page["size"];
            $param->AfterCount = isset($param->AfterCount)?$param->AfterCount:5;
            $param->BeforeCount =isset($param->BeforeCount)?$param->BeforeCount:5;
            $param->DataCount = $page["totaldata"];
        }

        $page = new Page($param);
        $page->Output();
    }

    function __construct($param){
        $this->Setting = $param;
        if(empty($param)){
            print_r("Cannot found PageSetting Instance");
            return;
        }
    }

    //计算
    //输出
    function Output(){
        $index = $this->Setting->PageIndex;
        $after = $this->Setting->AfterCount;
        $before = $this->Setting->BeforeCount;
        $show = $after+$before;
        $count = intval($this->Setting->DataCount/$this->Setting->PageSize);

        $format = "{index}";
        //如果没有格式
        if(isset($this->Setting->Url)){
            $format = $this->Setting->Url;
        }

        if($count < $index){
            return;
        }
        ?>
        <div class="ZXUN_PAGE">
        <?php
        if($index > 1)
        {
            ?>
            <a href="<?php echo str_replace("{index}",$index-1,$format)?>">上一页</a>
            <?php
        }
        ?>
        <a href="<?php echo str_replace("{index}",1,$format)?>">1</a>
        <?php
        if($index-$after>0){
            ?><a href="javascript:void(0)">...</a><?php
            for($i=$index-$after;$i<$index;$i++){
                ?>
                <a href="<?php echo str_replace("{index}",$i,$format)?>"><?php echo $i?></a>
                <?php
            }
        }
        else{
            for($i=2;$i<=$index;$i++){
                ?>
                <a href="<?php echo str_replace("{index}",$i,$format)?>"><?php echo $i?></a>
                <?php
            }
        }
        if($index+$before<$count){
            for($i=$index+1;$i<$index+$before;$i++){
                ?>
                <a href="<?php echo str_replace("{index}",$i,$format)?>"><?php echo $i?></a>
                <?php
            }
            ?><a href="javascript:void(0)">...</a><?php
        }
        else{
            for($i=$index+1;$i<$count;$i++){
                ?>
                <a href="<?php echo str_replace("{index}",$i,$format)?>"><?php echo $i?></a>
                <?php
            }
        }
        ?>
        <a href="<?php echo str_replace("{index}",($count),$format)?>"><?php echo ($count)?></a>
        <?php
        //是否显示下一页
        if($index < $count){
            ?>
            <a href="<?php echo str_replace("{index}",$this->Setting->PageIndex+1,$format)?>">下一页</a>
            <?php
        }
        ?>
        </div>
        <?php
    }
}