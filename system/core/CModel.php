<?php
/**
 * Description of CModel
 */
class CModel {
   var $db = NULL;
   function __construct() {
       $this->db = Ebh::app()->getDb();
   }
   /**
    * 加载model类
    * @param string $modelname 模板名称
    * @return object model对象
    */
   public function model($modelname) {
   		return Ebh::app()->model($modelname);
   }
   /**
    * 获取存储键
    * @param unknown $key
    * @return string
    */
   public function getrediskey($key){
   		$hashCode = $key.'_'.md5($key);
   		return   $hashCode;
   }
}