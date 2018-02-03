<?php
/**
 * 数据库结果集类
 */
class CResult {
    var $resultobj = NULL;
    public function __construct($obj) {
        $this->resultobj = $obj;
    }

    public function row_array() {
        return $this->_row_array();
    }

    /**
     * 返回查询列表
     * @param string $key 列表键字段名
     * @param string $prefix 键前辍，主要用于将键转成字符串用于array_merge操作
     * @return mixed
     */
    public function list_array($key = '', $prefix = '') {
        return $this->_list_array($key, $prefix);
    }

    /**
     * 查询表的单一字段一维数组数据
     * @param $column 查询的表字段
     * @param string $key 列表键字段名
     * @param string $prefix 键前辍，主要用于将键转成字符串用于array_merge操作
     * @return mixed
     */
    public function list_field($column, $key = '', $prefix = '') {
        return $this->_list_field($column, $key, $prefix);
    }
    public function __destruct() {
        $this->close();
    }
}