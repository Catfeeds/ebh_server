<?php
/**
 * 系统设置模型
 */
class SystemsettingModel extends CModel {
	/**
	 * 获取系统设置信息
	 */
	public function getSetting($crid) {
		$sql = 'SELECT * FROM ebh_systemsettings WHERE crid=' . intval($crid);
		$row = $this->db->query($sql)->row_array();
		if (empty($row)) {
			$row = array(
				'crid' => $crid,
				'metakeywords' => '',
				'metadescription' => '',
				'favicon' => '',
				'faviconimg' => '',
				'ipbanlist' => '',
				'analytics' => '',
				'limitnum' => 0,
				'service' => 0,
				'opservicetime' => 0,
				'opserviceuid' => 0,
			);
		}
		return $row;
	}
}