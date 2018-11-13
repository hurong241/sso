<?php
namespace ZXUN\MVC\Core;

use ZXUN\Data\SQL;
use ZXUN\Configruation\Config;
use ZXUN\Data\DataBase;
use ZXUN\Data\DataTable;

class Model extends SQL
{
    protected $model;

    public function __construct()
    {
        // 获取数据库表名
        if (!$this->table) {
            // 获取模型类名称
            $this->model = get_class($this);

            // 删除类名最后的 Model 字符
            $this->model = substr($this->model, 0, -5);

            // 数据库表名与类名一致
            $this->table = strtolower($this->model);

        }
        //获取数据库连接
        //$bus = new DataTable($this->table);
        //print_r($bus);
        //exit;
    }
}