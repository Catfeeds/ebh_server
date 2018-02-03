<?php
/**
 * 原始文件model
 *对应表 ebh_sources
 */
class SourceModel extends CModel {
    /**
    *根据文件摘要信息获取文件记录
    */
    public function getFileByChecksum($checksum) {
        if(empty($checksum)) 
            return false;
        $checksum = $this->db->escape($checksum);
        $sql = "select sid,filename,filesuffix,filesize,ispreview,ism3u8 from ebh_sources where checksum=$checksum";
        return $this->db->query($sql)->row_array();
    }
    /**
    *根据sid获取文件信息
    */
    public function getFileBySid($sid) {
        $sql = "select sid,checksum,filename,filesuffix,source,filesize,filepath,ispreview,previewurl,apppreview,apppreviewurl,thumb,ism3u8,filelength from ebh_sources where sid=$sid";
        return $this->db->query($sql)->row_array();
    }
}