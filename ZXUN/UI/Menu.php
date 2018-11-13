<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/4/2018
 * Time: 4:51 PM
 */

namespace ZXUN\UI;

class Menu extends Control
{
    public function Set($attr,$param){
    ?>
        <div <?php echo isset($attr["class"])?"class=\"".$attr["class"]."\"":"" ?>>
            <?php
            foreach ($param as $key=>$value) {
                ?><a href="<?php echo $value?>"><?php echo $key;?></a><?php
            }
            if($attr["class"] == "ListMenu"){
            ?>
            <a href="" style="background-color: #ca0000;">刷新</a>
            <?}?>
        </div>
<?php
    }
}