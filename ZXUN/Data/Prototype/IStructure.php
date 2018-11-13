<?php
namespace ZXUN\Data\Prototype;

interface IStructure{
	/// 创建删除声明
	//public function CreateDeleteStatement($tableName, $whereStr);
	
	/// 创建数据库备份声明
	//public function CreateDataBaseBackup($path);
	/*public function CreateInsert($tableName, $includeColumns, $includeColumnValues);
	public function CreateDelete($tableName, $whereStr);
	
	public function CreateSum($tableName, $whereStr, $includeColumns);*/
	/// <summary>
	/// 创建查询声明
	/// </summary>
	public function CreateSelect($tableName,$top, $whereStr, $groupByStr, $havingStr, $orderByStr, $includeColumnsStr);
	
	/*public function CreateSelectPaging($tableName, $page, $columnStr, $whereStr, $groupByStr, $havingStr, $orderByStr);
	
	/// <summary>
	/// 创建更新声明
	/// </summary>
	public function CreateUpdate($tableName, $whereStr, $includeColumns, $includeColumnValues);*/
}
?>