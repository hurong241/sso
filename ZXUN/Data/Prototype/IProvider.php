<?php
namespace ZXUN\Data\Prototype;

interface IProvider{

	//分析条件
	public function AnalysisWhere($where);
        /// 绑定栏目名称
        public function BuildColumn($name);
        /// 绑定参数
        public function BuildParameter($name, $value);
        /// 绑定参数名称
        public function BuildParameterName($name);
}
?>