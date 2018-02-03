<?php
/**
 * 网校模块类
 */
class RoommodulesModel extends CModel{
    public function getRoomModulesForStudent($crid) {
        $crid = (int) $crid;
        $sql = "SELECT `moduleid`,`modulename`,`url` FROM `ebh_appmodules` WHERE `system`=1 AND (`tors`=1 OR `tors`=2)";
        $default_modules = $this->db->query($sql)->list_array('moduleid');
        $sql = "SELECT `rm`.`nickname`,`rm`.`moduleid`,`rm`.`available`,`m`.`modulename`,`m`.`url` FROM `ebh_roommodules` AS `rm` LEFT JOIN ".
            "`ebh_appmodules` AS `m` ON `rm`.`moduleid`=`m`.`moduleid` WHERE `rm`.`crid`=$crid AND `rm`.`tors`=0";
        $self_modules = $this->db->query($sql)->list_array('moduleid');
        if (empty($self_modules) === true) {
            return $default_modules;
        }
        foreach ($self_modules as $id => $module) {
            if (key_exists($id, $default_modules)) {
                if ($module['available'] == 0) {
                    unset($default_modules[$id]);
                    continue;
                }
                if (empty($module['nickname']) === false) {
                    $default_modules[$id]['modulename'] = $module['nickname'];
                }
                continue;
            }

            if ($module['available'] == 0) {
                unset($default_modules[$id]);
                continue;
            }
            $default_modules[$id] = array(
                'moduleid'      => $module['moduleid'],
                'modulename'    => empty($module['nickname']) ? $module['modulename'] : $module['nickname']
            );
        }
        return $default_modules;
    }
}