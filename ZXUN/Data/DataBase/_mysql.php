<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 3/20/2018
 * Time: 2:40 PM
 */
namespace ZXUN\Data\DataBase;

class _mysql extends \mysqli {
    public function prepare($query) {
        return new _stmt($this,$query);
    }
}
