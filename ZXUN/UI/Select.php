<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/4/2018
 * Time: 4:30 PM
 */

namespace ZXUN\UI;

use ZXUN\Runtime\Loader;

/**
 * Class Select
 * @package ZXUN\UI
 * 下拉框控件
 */
class Select extends Control
{
    /**
     * @param $param
     * @param $data
     * @param $val
     * 绑定数据
     */
    public static function Bind($param,$data,$attrs=null){
        $sel = "";
        $n = "";
        $k = "";
        $v = "";
        $s = "";
        switch (gettype($param)){
            case "string":
                $n = $param;
                break;
            case "array":
                $n = $param["name"];
                if(isset($param["key"]))
                    $k = $param["key"];
                if(isset($param["value"]))
                    $v = $param["value"];
                if(isset($param["select"]))
                    $s = $param["select"];
                break;
        }
        switch(gettype($attrs)){
            case "integer":
            case "string":
                $s = $attrs;
                break;
            case "array":
                break;
        }
        ?>
        <select name="<?echo $n?>" <?php
            //添加属性
            if($attrs !=null && gettype($attrs)=="array"){
                foreach ($attrs as $key=>$value){
                    switch($key){
                        case "onchange":
                            echo "onchange=\"$value\"";
                            break;
                        case "":
                            break;
                    }
                }
            }
        ?>>
            <?php
            //填充绘制
            switch(gettype($param)) {
                case "string":
                    foreach ($data as $key => $value) {
                        if($s !=0)
                            $sel = $key==$s?"selected":"";
                    ?>
                        <option value="<?echo $key?>" <?php echo $sel;?>><?php echo $value?></option>
                    <?php
                    }
                    break;
                case "array":
                    foreach ($data as $item) {
                        if($s !=0)
                            $sel = $item[$k]==$s?"selected":"";
                        ?>
                        <option value="<?echo $item[$k]?>" <?php echo $sel;?>><?php echo $item[$v]?></option>
                        <?php
                    }
                    break;
            }
            ?>
        </select>
        <?php
    }
}