<?php

/**
 * Description of mysqli_result
 *
 * @author Administrator
 */
class CMysqli_result extends CResult {
    public function _row_array() {
        if(empty($this->resultobj) || !is_object($this->resultobj)) {
            return false;
        }
        $row = $this->resultobj->fetch_array(MYSQLI_ASSOC);
        return $row;
    }
    public function _list_array($key = '', $prefix = '') {
        if(empty($this->resultobj) || !is_object($this->resultobj)) {
            return false;
        }
        $resultarr = array();
        if (empty($key) === true) {
            while($row = $this->resultobj->fetch_array(MYSQLI_ASSOC)) {
                $resultarr[] = $row;
            }
        } else {
            while($row = $this->resultobj->fetch_array(MYSQLI_ASSOC)) {
                $k = empty($prefix) ? $row[$key] : $prefix.$row[$key];
                $resultarr[$k] = $row;
            }
        }
        return $resultarr;
    }
    /**
     * 查询表的单一字段一维数组数据
     * @param $column 查询的表字段
     * @param string $key 列表键字段名
     * @param string $prefix 键前辍，主要用于将键转成字符串用于array_merge操作
     * @return mixed
     */
    public function _list_field($column, $key = '', $prefix = '') {
        if(empty($this->resultobj) || !is_object($this->resultobj)) {
            return false;
        }
        $resultarr = array();
        if (empty($key) === true) {
            while($row = $this->resultobj->fetch_array(MYSQLI_ASSOC)) {
                $resultarr[] = $row[$column];
            }
        } else {
            while($row = $this->resultobj->fetch_array(MYSQLI_ASSOC)) {
                $k = empty($prefix) ? $row[$key] : $prefix.$row[$key];
                $resultarr[$k] = $row[$column];
            }
        }
        return $resultarr;
    }
    public function close() {
        if(!empty($this->resultobj) && is_object($this->resultobj)) {
            $this->resultobj->free();
        }
    }
}