<?php
/**
 *  -------------------------------------------------
 *   @file		: UserChannelModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-29 19:06:11
 *   @update	:
 *  -------------------------------------------------
 */
class UserChannelModel extends Model
{
	function __construct($id = NULL, $strConn = "") {
                $this->_objName = 'user_channel';
                $this->pk = 'id';
                $this->_prefix = '';
                $this->_dataObject = array("id" => "主键id",
                        "user_id" => "用户id",
                        "channel_id" => "渠道id");
                parent::__construct($id, $strConn);
        }

        public function getChannelUsers() {
                $sql = "SELECT `id`,`account`,`real_name` FROM `user` WHERE `is_deleted`='0' AND `is_on_work`=1 AND `is_channel_keeper`='1' AND `is_on_work`='1'";
                return $this->db()->getAll($sql);
        }

        public function getAllChannels() {
                $sql = "SELECT `id`,`channel_name` FROM `sales_channels` where is_deleted=0";
                return $this->db()->getAll($sql);
        }

        /**
	 *	pageList，分页列表
	 *
	 *	@url UserChannelController/search
	 */
	function pageList($where, $page, $pageSize = 10, $useCache = true) {
                //不要用*,修改为具体字段
                $sql = "SELECT `m`.`id`,`m`.`power`,`c`.`channel_name`,`c`.`channel_code`,u.real_name,u.account FROM `" . $this->table() . "` AS `m` INNER JOIN `sales_channels` AS `c` ON `c`.`id`=`m`.`channel_id` INNER JOIN `user` AS u ON m.user_id=u.id ";
                $str = '';
                if (!empty($where['user_id'])) {
                        $str .= "`m`.`user_id`='" . $where['user_id'] . "' AND ";
                }
                if (!empty($where['channel_id'])) {
                        $str .= "`m`.`channel_id`='" . $where['channel_id'] . "' AND ";
                }
                if ($str) {
                        $str = rtrim($str, "AND "); //这个空格很重要
                        $sql .=" WHERE `u`.`is_on_work`=1 AND " . $str;
                }
                $sql .= " ORDER BY `m`.`id` DESC";
                $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
                return $data;
        }

        /**
         * saveUserChannelData,保存
         */
        public function saveUserChannelData($user_id, $channel_id, $auth_check) {
                //$start = microtime(true);
                $sql = "delete u.* from user_channel AS u,(select id,concat(user_id,'-',channel_id) AS cc from user_channel group by cc having count(cc)>1 ) AS t where u.id=t.id";
                $this->db()->query($sql); //去重
                $sql_file = "user_channel_" . date('YmdHis') . '.sql';
                try {
                        //业务逻辑开始                       

                        $uids = implode(',', $user_id);
                        $cids = implode(',', $channel_id);
                        $sql = "SELECT user_id,channel_id FROM `user_channel` WHERE `user_id` IN ($uids) AND `channel_id` IN ($cids)";
                        $data = $this->db()->getAll($sql);
                        unset($uids, $cids, $sql);
                        $tdata = array();
                        foreach ($data as $value) {
                                $tdata[$value['user_id']][$value['channel_id']] = $value['channel_id'];
                        }
                        unset($data);
                        $newArr = array();

                        foreach ($user_id as $val) {
                                $_tmp = array();
                                $tmp = isset($tdata[$val]) ? $tdata[$val] : array();
                                foreach ($channel_id as $val1) {
                                        if (!isset($tmp[$val1])) {
                                                $newArr[] = array('user_id' => $val, 'channel_id' => $val1, 'power' => 1);
                                        }
                                }
                        }
                        unset($tdata, $_tmp, $tmp);
                        //记录太多，不能一次插入
                        $cnt = count($newArr);
                        if ($cnt >= 2000 && $auth_check) {
                                $notInsert = true;
                        } else {
                                $notInsert = false;
                        }
                        $length = 2000;
                        for ($i = 0; $i < $cnt; $i = $i + $length) {
                                $_tt = array_slice($newArr, $i, $length);
                                $res_sql = $this->insertAll($_tt, 'user_channel', $notInsert); //建立关联关系
                                if ($notInsert)
                                        Util::L($res_sql . ';', $sql_file);
                        }

                        unset($length, $_tt, $res_sql);

                        //创建授权 已经存在的用户不进行授权变更         
                        if ($auth_check && $cnt) {
                                //菜单权限
                                $mids = array();
                                $cids = array(); //控制器id
                                $cdata = array();
                                //操作
                                $odata = array();
                                //列表按钮
                                $listButton = array();
                                //查看按钮
                                $viewButton = array();
                                //明细对象
                                $ddata = array();
                                $dcids = array();
                                //明细操作
                                $dodata = array();
                                //明细按钮
                                $dbdata = array();
                                //属性控制
                                $fdata = array();

                                //3为渠道
                                $sql = "SELECT p.id,m.c_id,m.id AS mid FROM `permission` AS p INNER JOIN `menu` AS m ON p.resource_id=m.id AND m.is_enabled=1 AND m.is_deleted=0 AND m.type=3 WHERE p.is_deleted=0 AND p.type=1 order by p.id";
                                $data = $this->db()->getAll($sql);
                                if ($data) {//如果有需要授权的菜单
                                        $mids = array_column($data, 'id'); //菜单权限id
                                        $midArr = array_combine($mids, array_column($data, 'mid'));                                        
                                        $cids = array_column($data, 'c_id');
                                        $cdata = array_combine($cids, $mids);
                                        //过滤
                                        if(Auth::$userType==2){
                                                $myPower = $_SESSION["__menu_p"];
                                                if(isset($myPower[3][$channel_id])){
                                                        $mids = array_intersect($mids,$myPower[3][$channel_id]);
                                                        if($mids){
                                                                foreach ($midArr AS $k=>$v){
                                                                      if(!in_array($k,$_mids)){
                                                                              unset($midArr[$k]);
                                                                              continue;
                                                                      }
                                                                }
                                                                $cids = array_column($midArr, 'c_id');
                                                                $cdata = array_combine($cids, $mids);
                                                        }
                                                }
                                        }
                                        //菜单权限入库
                                        $arr = array();
                                        foreach ($mids as $val) {
                                                foreach ($newArr as $val1) {
                                                        $arr[] = array('user_id' => $val1['user_id'], 'type' => 3, 'source_id' => $val1['channel_id'], 'permission_id' => $val);
                                                        if (count($arr) == 2000) {
                                                                $res_sql = $this->insertAll($arr, 'user_extend_menu', $notInsert);
                                                                if ($notInsert)
                                                                        Util::L($res_sql . ';', $sql_file);
                                                                $arr = array();
                                                        }
                                                }
                                        }
                                        //记录太多，不能一次插入
                                        $cnt = count($arr);
                                        if ($cnt) {
                                                $res_sql = $this->insertAll($arr, 'user_extend_menu', $notInsert);
                                                if ($notInsert)
                                                        Util::L($res_sql . ';', $sql_file);
                                        }
                                        //$this->insertAll($arr,'user_extend_menu');
                                        unset($cnt, $length, $_tt, $arr, $data, $sql);

                                        $sql = "SELECT op.id,o.c_id FROM `operation` AS `o` INNER JOIN `permission` AS `op` ON o.id=op.resource_id AND o.is_deleted=op.is_deleted WHERE EXISTS (SELECT 1 FROM `permission` AS p INNER JOIN `menu` AS m ON p.resource_id=m.id AND m.is_enabled=1 AND m.is_deleted=0 AND m.type=3 WHERE p.is_deleted=0 AND p.type=1 AND m.c_id=o.c_id) AND `o`.`is_deleted`='0' AND op.type=3 ORDER BY o.id ";
                                        $data = $this->db()->getAll($sql);
                                        if ($data) {//操作权限id
                                                //过滤
                                                $myids = array();
                                                if(Auth::$userType==2){
                                                        $myPower = $_SESSION["__operation_p"];
                                                        if(isset($myPower[3][$channel_id])){
                                                                $myids = $myPower[3][$channel_id];
                                                        }
                                                }
                                                foreach ($data as $val) {
                                                        if(Auth::$userType==2 && !in_array($val['id'], $myids)){
                                                                continue;
                                                        }
                                                        if (isset($cdata[$val['c_id']])) {
                                                                
                                                                $odata[] = array('id' => $val['id'], 'parent_id' => $cdata[$val['c_id']]);
                                                        }
                                                }
                                                $arr = array();
                                                foreach ($odata as $val) {
                                                        foreach ($newArr as $val1) {
                                                                $arr[] = array('user_id' => $val1['user_id'], 'type' => 3, 'source_id' => $val1['channel_id'], 'permission_id' => $val['id'], 'parent_id' => $val['parent_id']);
                                                                if (count($arr) == 2000) {
                                                                        $res_sql = $this->insertAll($arr, 'user_extend_operation', $notInsert);
                                                                        if ($notInsert)
                                                                                Util::L($res_sql . ';', $sql_file);
                                                                        $arr = array();
                                                                }
                                                        }
                                                }
                                                //记录太多，不能一次插入
                                                if ($arr) {
                                                        $res_sql = $this->insertAll($arr, 'user_extend_operation', $notInsert);
                                                        if ($notInsert)
                                                                Util::L($res_sql . ';', $sql_file);
                                                }

                                                //$this->insertAll($arr,'user_extend_operation');
                                                unset($sql, $data, $odata, $arr);
                                        }

                                        $sql = "(SELECT `p`.`id`,`b`.`c_id` FROM `button` AS `b` INNER JOIN `permission` AS `p` ON `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE `b`.`type`='1' AND `b`.`is_deleted`='0' AND EXISTS (SELECT 1 FROM `permission` AS pp INNER JOIN `menu` AS m ON pp.resource_id=m.id AND m.is_enabled=1 AND m.is_deleted=0 AND m.type=3 WHERE pp.is_deleted=0 AND pp.type=1 AND m.c_id=b.c_id) AND `p`.`type`='2' ORDER BY `b`.`display_order` DESC) UNION (SELECT `p`.`id`,`b`.`c_id` FROM `button` AS `b` INNER JOIN `permission` AS `p` ON `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE `b`.`is_deleted`='0' AND `b`.`id`<=3 AND `p`.`type`='2' ORDER BY `b`.`display_order` DESC)";
                                        $data = $this->db()->getAll($sql);
                                        if ($data) {//列表按钮权限id
                                                if(Auth::$userType==2){
                                                        $myPower=$_SESSION['__button_p'];
                                                }
                                                foreach ($data as $val) {
                                                        if (isset($cdata[$val['c_id']])) {
                                                                if(Auth::$userType==2 && !isset($myPower[3][$cdata[$val['c_id']]][$val['id']]))  {
                                                                        continue;
                                                                }
                                                                $listButton[] = array('id' => $val['id'], 'parent_id' => $cdata[$val['c_id']]);
                                                        }
                                                        if (!$val['c_id']) {//通用按钮的处理
                                                                foreach ($mids as $v) {
                                                                        $listButton[] = array('id' => $val['id'], 'parent_id' => $v);
                                                                }
                                                        }
                                                }

                                                $arr = array();
                                                foreach ($listButton as $val) {
                                                        foreach ($newArr as $val1) {
                                                                $arr[] = array('user_id' => $val1['user_id'], 'type' => 3, 'source_id' => $val1['channel_id'], 'permission_id' => $val['id'], 'parent_id' => $val['parent_id']);
                                                                if (count($arr) == 2000) {
                                                                        $res_sql = $this->insertAll($arr, 'user_extend_list_button', $notInsert);
                                                                        if ($notInsert)
                                                                                Util::L($res_sql . ';', $sql_file);
                                                                        $arr = array();
                                                                }
                                                        }
                                                }
                                                //记录太多，不能一次插入
                                                $cnt = count($arr);
                                                if ($cnt) {
                                                        $res_sql = $this->insertAll($arr, 'user_extend_list_button', $notInsert);
                                                        if ($notInsert)
                                                                Util::L($res_sql . ';', $sql_file);
                                                }
                                                //$this->insertAll($arr,'user_extend_list_button');
                                                unset($sql, $data, $listButton, $arr, $cnt);
                                        }


                                        //查看按钮
                                        $sql = "(SELECT `p`.`id`,`b`.`c_id` FROM `button` AS `b` INNER JOIN `permission` AS `p` ON `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE `b`.`type`='2' AND `b`.`is_deleted`=0 AND EXISTS (SELECT 1 FROM `permission` AS pp INNER JOIN `menu` AS m ON pp.resource_id=m.id AND m.is_enabled=1 AND m.is_deleted=pp.is_deleted AND m.type=3 WHERE pp.is_deleted=0 AND pp.type=1 AND m.c_id=b.c_id) AND `p`.`type`='2' ORDER BY `b`.`display_order` DESC) UNION (SELECT `p`.`id`,`b`.`c_id` FROM `button` AS `b` INNER JOIN `permission` AS `p` ON `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE `b`.`is_deleted`='0' AND `b`.`id` IN (3,4) AND `p`.`type`='2' ORDER BY `b`.`display_order` DESC)";

                                        $data = $this->db()->getAll($sql);
                                        if ($data) {//查看按钮权限id
                                                if(Auth::$userType==2){
                                                        $myPower=$_SESSION['__button_p'];
                                                }
                                                foreach ($data as $val) {
                                                        if (isset($cdata[$val['c_id']])) {
                                                                if(Auth::$userType==2 && !isset($myPower[3][$cdata[$val['c_id']]][$val['id']])){
                                                                        continue;
                                                                }
                                                                $viewButton[] = array('id' => $val['id'], 'parent_id' => $cdata[$val['c_id']]);
                                                        }
                                                        if (!$val['c_id']) {//通用按钮的处理
                                                                foreach ($mids as $v) {
                                                                        $viewButton[] = array('id' => $val['id'], 'parent_id' => $v);
                                                                }
                                                        }
                                                }
                                                $arr = array();
                                                foreach ($viewButton as $val) {
                                                        foreach ($newArr as $val1) {
                                                                $arr[] = array('user_id' => $val1['user_id'], 'type' => 3, 'source_id' => $val1['channel_id'], 'permission_id' => $val['id'], 'parent_id' => $val['parent_id']);
                                                                if (count($arr) == 2000) {
                                                                        $res_sql = $this->insertAll($arr, 'user_extend_view_button', $notInsert);
                                                                        if ($notInsert)
                                                                                Util::L($res_sql . ';', $sql_file);
                                                                        $arr = array();
                                                                }
                                                        }
                                                }
                                                //记录太多，不能一次插入
                                                $cnt = count($arr);
                                                if ($cnt) {
                                                        $res_sql = $this->insertAll($arr, 'user_extend_view_button', $notInsert);
                                                        if ($notInsert)
                                                                Util::L($res_sql . ';', $sql_file);
                                                }
                                                //$this->insertAll($arr,'user_extend_view_button');
                                                unset($sql, $data, $viewButton, $arr, $cnt);
                                        }
                                        //明细对象
                                        $sql = "SELECT `p`.`id` AS `pid`,`c`.`id`,`c`.`parent_id` FROM `control` AS `c` INNER JOIN `permission` AS `p` ON `c`.`id`=`p`.`resource_id` AND `c`.`is_deleted`=`p`.`is_deleted` INNER JOIN `menu` AS m ON m.c_id=`c`.`parent_id` AND m.type=3 AND m.is_deleted=c.is_deleted AND m.is_enabled=1 WHERE `c`.`type`='3' AND `p`.`type`='4' AND `c`.`is_deleted`='0' ORDER BY `c`.`id` ";

                                        $data = $this->db()->getAll($sql);
                                        if ($data) {
                                                if(Auth::$userType==2){
                                                        $myPower = $_SESSION["__menu_p"];
                                                }
                                                foreach ($data as $val) {
                                                        if(Auth::$userType==2 && !isset($myPower[3][$channel_id][$val['id']])){
                                                                continue;
                                                        }
                                                        if (isset($cdata[$val['parent_id']])) {
                                                                $ddata[] = array('id' => $val['pid'], 'parent_id' => $cdata[$val['parent_id']]);
                                                                $dcids[] = array('id' => $val['id'], 'pid' => $val['pid']);
                                                        }
                                                }

                                                $arr = array();
                                                foreach ($ddata as $val) {
                                                        foreach ($newArr as $val1) {
                                                                $arr[] = array('user_id' => $val1['user_id'], 'type' => 3, 'source_id' => $val1['channel_id'], 'permission_id' => $val['id'], 'parent_id' => $val['parent_id']);
                                                                if (count($arr) == 2000) {
                                                                        $res_sql = $this->insertAll($arr, 'user_extend_subdetail', $notInsert);
                                                                        if ($notInsert)
                                                                                Util::L($res_sql . ';', $sql_file);
                                                                        $arr = array();
                                                                }
                                                        }
                                                }
                                                //记录太多，不能一次插入
                                                $cnt = count($arr);
                                                if ($cnt) {
                                                        $res_sql = $this->insertAll($arr, 'user_extend_subdetail', $notInsert);
                                                        if ($notInsert)
                                                                Util::L($res_sql . ';', $sql_file);
                                                }

                                                //$this->insertAll($arr,'user_extend_subdetail');
                                                unset($sql, $data, $ddata, $arr, $cnt);

                                                if ($dcids) {
                                                        $tmp = array_combine(array_column($dcids, 'id'), array_column($dcids, 'pid'));
                                                        //明细操作
                                                        $sql = "SELECT `p`.`id`,`b`.`c_id` FROM `operation` AS `b` INNER JOIN `permission` AS `p` ON `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE `b`.`is_deleted`='0' AND `b`.`c_id` IN (" . implode(',', array_column($dcids, 'id')) . ") AND `p`.`type`='3' ORDER BY `b`.`id` ASC";

                                                        $data = $this->db()->getAll($sql);
                                                        if ($data) {
                                                                if(Auth::$userType==2){
                                                                        $myPower = $_SESSION["__operation_p"];
                                                                }
                                                                foreach ($data as $val) {
                                                                        if(Auth::$userType==2 && !in_array($val['id'],$myPower[3][$channel_id])){
                                                                                continue;
                                                                        }
                                                                        if (isset($tmp[$val['c_id']])) {
                                                                                $dodata[] = array('id' => $val['id'], 'parent_id' => $tmp[$val['c_id']]);
                                                                        }
                                                                }

                                                                $arr = array();
                                                                foreach ($dodata as $val) {
                                                                        foreach ($newArr as $val1) {
                                                                                $arr[] = array('user_id' => $val1['user_id'], 'type' => 3, 'source_id' => $val1['channel_id'], 'permission_id' => $val['id'], 'parent_id' => $val['parent_id']);
                                                                                if (count($arr) == 2000) {
                                                                                        $res_sql = $this->insertAll($arr, 'user_extend_subdetail_operation', $notInsert);
                                                                                        if ($notInsert)
                                                                                                Util::L($res_sql . ';', $sql_file);
                                                                                        $arr = array();
                                                                                }
                                                                        }
                                                                }
                                                                //记录太多，不能一次插入
                                                                $cnt = count($arr);
                                                                if ($cnt) {
                                                                        $res_sql = $this->insertAll($arr, 'user_extend_subdetail_operation', $notInsert);
                                                                        if ($notInsert)
                                                                                Util::L($res_sql . ';', $sql_file);
                                                                }
                                                                //$this->insertAll($arr,'user_extend_subdetail_operation');
                                                                unset($sql, $data, $dodata, $arr, $cnt);
                                                        }

                                                        //明细按钮
                                                        $sql = "SELECT `p`.`id`,`b`.`c_id` FROM `button` AS `b` INNER JOIN `permission` AS `p` ON `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE `b`.`type`='1' AND `b`.`is_deleted`='0' AND `b`.`c_id` IN (" . implode(',', array_column($dcids, 'id')) . ") AND `p`.`type`='2' ORDER BY `b`.`display_order` DESC";

                                                        $data = $this->db()->getAll($sql);
                                                        if ($data) {
                                                                if(Auth::$userType==2){
                                                                        $myPower = $_SESSION["__button_p"];
                                                                }
                                                                foreach ($data as $val) {
                                                                        if (isset($tmp[$val['c_id']])) {
                                                                                if(Auth::$userType==2 && !isset($myPower[3][$channel_id][$tmp[$val['c_id']]][$val['id']])){
                                                                                        continue;
                                                                                }
                                                                                $dbdata[] = array('id' => $val['id'], 'parent_id' => $tmp[$val['c_id']]);
                                                                        }
                                                                }

                                                                $arr = array();
                                                                foreach ($dbdata as $val) {
                                                                        foreach ($newArr as $val1) {
                                                                                $arr[] = array('user_id' => $val1['user_id'], 'type' => 3, 'source_id' => $val1['channel_id'], 'permission_id' => $val['id'], 'parent_id' => $val['parent_id']);
                                                                                if (count($arr) == 2000) {
                                                                                        $res_sql = $this->insertAll($arr, 'user_extend_subdetail_button', $notInsert);
                                                                                        if ($notInsert)
                                                                                                Util::L($res_sql . ';', $sql_file);
                                                                                        $arr = array();
                                                                                }
                                                                        }
                                                                }
                                                                //记录太多，不能一次插入
                                                                $cnt = count($arr);
                                                                if (count($arr)) {
                                                                        $res_sql = $this->insertAll($arr, 'user_extend_subdetail_button', $notInsert);
                                                                        if ($notInsert)
                                                                                Util::L($res_sql . ';', $sql_file);
                                                                }
                                                                //$this->insertAll($arr,'user_extend_subdetail_button');
                                                                unset($sql, $data, $dbdata, $dcids, $cdata, $midArr, $mids, $arr, $cnt, $tmp);
                                                        }
                                                }
                                        }

                                        //属性控制
                                        $sql = "SELECT fs.c_id,fs.code,p.id FROM `field_scope` AS fs INNER JOIN `permission` AS p ON fs.id=p.resource_id AND fs.is_deleted=p.is_deleted AND fs.is_enabled=1";
                                        $data = $this->db()->getAll($sql);
                                        if ($data) {
                                                if(Auth::$userType==2){
                                                        $myPower = $_SESSION["__scope"];
                                                }
                                                foreach ($data as $val) {
                                                        if(Auth::$userType==2 && !isset($myPower[3][$channel_id][strtoupper($val['code'])])){
                                                                continue;
                                                        }
                                                        $fdata[] = array('id' => $val['id']);
                                                }
                                                $arr = array();
                                                foreach ($fdata as $val) {
                                                        foreach ($newArr as $val1) {
                                                                $arr[] = array('user_id' => $val1['user_id'], 'type' => 3, 'source_id' => $val1['channel_id'], 'permission_id' => $val['id'], 'scope' => 3);
                                                                if (count($arr) == 2000) {
                                                                        $res_sql = $this->insertAll($arr, 'user_scope', $notInsert);
                                                                        if ($notInsert)
                                                                                Util::L($res_sql . ';', $sql_file);
                                                                        $arr = array();
                                                                }
                                                        }
                                                }
                                                //记录太多，不能一次插入
                                                $cnt = count($arr);
                                                if ($cnt) {
                                                        $res_sql = $this->insertAll($arr, 'user_scope', $notInsert);
                                                        if ($notInsert)
                                                                Util::L($res_sql . ';', $sql_file);
                                                }
                                                //$this->insertAll($arr,'user_scope');
                                        }
                                }
                        }
                        //业务逻辑结束
                } catch (Exception $e) {//捕获异常
//                        var_dump($e);
                        return false;
                }

//                $end = microtime(true);
//                echo $end-$start;exit;
                if ($notInsert) {
                        return $sql_file;
                }
                return true;
        }

        /*
	*	授权页显示信息
	*/
	public function getInfo ()
	{
		$sql = "SELECT c.id,c.power,c.user_id,c.channel_id,u.account,u.real_name,s.channel_name,s.channel_code FROM `user_channel` AS c INNER JOIN `user` AS u ON c.user_id=u.id INNER JOIN `sales_channels` AS s ON c.channel_id=s.id WHERE c.id='".$this->pk()."' AND u.`is_deleted`='0' AND u.`is_on_work`='1' AND s.`is_deleted`='0'";
		return $this->db()->getRow($sql);
	}

	/*
	*	取用户信息通过渠道id
	*/
	public function get_user_channel_by_channel_id ($id)
	{
		$sql = "SELECT `c`.`id`,`c`.`power`,`c`.`user_id`,`c`.`channel_id`,`u`.`account`,`u`.`real_name`,`s`.`channel_name`,`s`.`channel_code` FROM `user_channel` AS c INNER JOIN `user` AS u ON `c`.`user_id`=`u`.`id` INNER JOIN `sales_channels` AS s ON `c`.`channel_id`=`s`.`id` WHERE `c`.`channel_id`='".$id."' AND `u`.`is_deleted`='0' AND `u`.`is_on_work`='1' AND `s`.`is_deleted`='0' AND `u`.`is_enabled`='1'";
		return $this->db()->getAll($sql);
	}

	/*
	*	取用户信息通过渠道id
	*/
	public function get_channels_person_by_channel_id ($id)
	{
        if($id==''){
            return false;
        }
		$sql = "SELECT `id`,`dp_leader`,`dp_leader_name`,`dp_people`,`dp_people_name` FROM `sales_channels_person` WHERE `id` IN (".$id.")";
		return $this->db()->getRow($sql);
	}

	/*
	*	取用户信息通过渠道id
	*/
	public function get_all_channels_by_channel_id ($id)
	{
        if($id==''){
            return false;
        }
		$sql = "SELECT `id`,`dp_leader`,`dp_leader_name`,`dp_people`,`dp_people_name` FROM `sales_channels_person` WHERE `id` IN (".$id.")";
		return $this->db()->getAll($sql);
	}

	//type=3为渠道
	public function getMenuData ($user_id,$channel_id)
	{
//		$sql = "SELECT id FROM `resource_type` WHERE `code`='MENU' ";
//		$row = $this->db()->getOne($sql);
//		if(!$row)
//		{
//			return false;
//		}
                $row=1;

		$sql = "SELECT p.id,p.name AS label,p.code,p.resource_id,ifnull(r.permission_id,0) AS chk FROM `permission` AS p LEFT JOIN (SELECT distinct permission_id FROM `user_extend_menu` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND `type`='3') AS r ON p.id=r.permission_id WHERE p.is_deleted=0 AND p.type='".$row."' order by p.id";
		$sql = "SELECT t.*,m.group_id AS parent_id,m.application_id FROM (".$sql.") AS t INNER JOIN `menu` AS m ON t.`resource_id`=`m`.`id` WHERE `m`.`is_deleted`='0' AND m.`is_enabled`='1' AND `m`.`type`='3' ";
		$data = $this->db()->getAll($sql);
		if(!$data)
		{
			return array();
		}

		$sql = "SELECT a.id,a.label,mg.id AS gid,mg.label AS gname FROM `application` AS a LEFT JOIN `menu_group` AS mg ON a.id=mg.application_id WHERE a.`is_deleted`=0 AND a.is_enabled=1 ";
		$data1 = $this->db()->getAll($sql);

		$datas = array();
		$relation = array();//记录新的从属关系
		$i=0;
		foreach ($data1 as $val )
		{
			$i++;
			if(!isset($relation[1][$val['id']]))
			{
				$relation[1][$val['id']] = $i;
				$datas[$i] = array('id'=>$val['id'],'label'=>$val['label'],'parent_id'=>0,'i'=>$i,'type'=>1);
				$i++;
			}
			$relation[2][$val['gid']] = $i;
			$datas[$relation[1][$val['id']]]['son'][] = $i;
			$datas[$i] = array('id'=>$val['gid'],'label'=>$val['gname'],'parent_id'=>$relation[1][$val['id']],'i'=>$i,'type'=>2);
		}
                
                if(Auth::$userType==2){
                        $myPower = $_SESSION["__menu_p"];
                }

		$last_index = $i;
		foreach ($data as $v )
		{
                        if(Auth::$userType==2 && !isset($myPower[3][$channel_id][$v['id']])){
                                continue;
                        }
                        if(isset($relation[2][$v['parent_id']]) && isset($datas[$relation[2][$v['parent_id']]]))
                        {
                            $i++;
                            $datas[$relation[2][$v['parent_id']]]['son'][] = $i;
                            $datas[$i] = array('id'=>$v['id'],'label'=>$v['label'],'code'=>$v['code'],'parent_id'=>$relation[2][$v['parent_id']],'i'=>$i,'chk'=>$v['chk'],'resource_id'=>$v['resource_id'],'type'=>2);
                        }
		}

		krsort($datas);
		foreach ($datas as $key => $val )
		{
			if($key<=$last_index)
			{
				if($val['type']==2 && empty($val['son']))
				{
					unset($datas[$val['parent_id']]['son'][array_flip($datas[$val['parent_id']]['son'])[$key]]);
					unset($datas[$key]);
				}
				else
				{
					if($val['type']==1)
					{
						foreach ($val['son'] as $k => $v )
						{
							if(!isset($datas[$v]))
							{
								unset($datas[$key]['son'][$k]);
							}
						}
						if(empty($datas[$key]['son']))
						{
							unset($datas[$key]);
						}
					}
				}
			}
		}
		return $datas;
	}

	public function saveMenu ($user_id,$channel_id,$ids)
	{
		$sql = "SELECT id,permission_id FROM `user_extend_menu` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND `type`='3'";
		$data = $this->db()->getAll($sql);
                $oldids = $addIds =$delIds =  array();
                if($data)
                {
                        $oldids = array_column($data,'permission_id');
                        $data = array_combine(array_column($data,'id'),$oldids);
                }
                $addIds = array_diff($ids,$oldids);
                $delIds = array_diff($oldids,$ids);
                if(Auth::$userType==2){
                        $myPower = $_SESSION["__menu_p"];
                        //delids  要删的（需要去掉交叉授权的）
                        $delIds = array_intersect($delIds,array_keys($myPower[3][$channel_id]));
                }
		if($delIds)
		{
			$del = array();
			$tmp = array_flip($data);

			foreach ($delIds as $val )
			{
				if(isset($tmp[$val]))
				{
					$del[] = $tmp[$val];
				}
			}

			if($del)
			{
				try{
					//todo 删属性控制
					$sql = "(SELECT pp.id FROM `permission` AS p,`menu` AS m,`field_scope` AS f,`permission` AS pp,`user_scope` AS us WHERE us.permission_id=pp.id AND us.type=3 AND us.source_id=".$channel_id." AND p.resource_id=m.id AND m.c_id=f.c_id AND pp.type=5 AND f.id=pp.resource_id AND p.`id` IN (".implode(',',$delIds).")) UNION (SELECT pp.id FROM `permission` AS p,`field_scope` AS f,`permission` AS pp WHERE p.resource_id=f.c_id AND pp.type=5 AND f.id=pp.resource_id AND EXISTS (SELECT usp.permission_id FROM `user_extend_subdetail` AS usp WHERE usp.`parent_id` IN (".implode(',',$delIds).") AND p.`id`=usp.permission_id AND usp.user_id='{$user_id}' AND usp.type=3 AND usp.source_id=".$channel_id." ))";
					$res = $this->db()->getAll($sql);
					if($res)
					{
						$is = array_column($res,'id');
						$sql = "DELETE FROM `user_scope` WHERE `user_id`='{$user_id}' AND type=3 AND `source_id`=".$channel_id." AND permission_id IN (".implode(',',$is).") ";

						$this->db()->query($sql);
					}


					//删明细
					$sql = "DELETE sp.*,op.*,sbp.* FROM `user_extend_subdetail` AS sp LEFT JOIN `user_extend_subdetail_operation` AS op ON sp.permission_id=op.parent_id AND sp.user_id=op.user_id AND sp.type=op.type AND sp.source_id=op.source_id LEFT JOIN `user_extend_subdetail_button` AS sbp ON sp.permission_id=sbp.parent_id AND sp.user_id=sbp.user_id AND sp.type=sbp.type AND sp.source_id=sbp.source_id WHERE sp.type=3 AND sp.user_id=".$user_id." AND sp.source_id=".$channel_id." AND sp.`parent_id` IN (".implode(',',$delIds).") ";

					$this->db()->query($sql);

					//删主对象
					$sql = "DELETE mp.*,bp.*,vbp.*,op.* FROM `user_extend_menu` AS mp LEFT JOIN `user_extend_list_button` AS bp ON mp.permission_id=bp.parent_id AND mp.user_id=bp.user_id AND mp.type=bp.type AND mp.source_id=bp.source_id LEFT JOIN `user_extend_view_button` AS vbp ON mp.permission_id=vbp.parent_id AND mp.user_id=vbp.user_id AND mp.type=vbp.type AND mp.source_id=vbp.source_id LEFT JOIN `user_extend_operation` AS op ON mp.permission_id=op.parent_id AND mp.user_id=op.user_id AND mp.type=op.type AND mp.source_id=op.source_id WHERE mp.type=3 AND mp.user_id=".$user_id." AND mp.source_id=".$channel_id." AND mp.`id` IN (".implode(',',$del).") ";

					$this->db()->query($sql);

				}
				catch(Exception $e){
//					var_dump ($e);
					return false;
				}

			}
		}
		if($addIds)
		{
			$arr=array();
			foreach ($addIds as $id )
			{
				$arr[] = array('user_id'=>$user_id,'permission_id'=>$id,'source_id'=>$channel_id,'type'=>3);
			}

			if($arr)
			{
				try{
					$this->insertAll($arr,'user_extend_menu');
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		return true;
	}

	public function getOperation ($user_id,$channel_id)
	{
		$data = array();
		//menu
		$sql = "SELECT p.id,m.label,c.id AS c_id FROM `user_extend_menu` AS `u` INNER JOIN `permission` AS p ON u.permission_id=p.id INNER JOIN `menu` AS m ON m.id=p.resource_id AND m.is_deleted=p.is_deleted INNER JOIN `control` AS `c` ON m.c_id=c.id WHERE u.`user_id`='{$user_id}' AND u.type=3 AND u.source_id='{$channel_id}' AND p.type='1' AND p.is_deleted=0 AND c.is_deleted=0 AND m.is_enabled=1";

                $menus = $this->db()->getAll($sql);
		if(!$menus)
		{
			return $data;
		}
		
		$pids = array_column($menus,'id');//parent_id
                $menus = array_combine($pids, $menus);
                if(Auth::$userType==2){
                        $myPower = $_SESSION["__menu_p"];
                        //delids  要删的（需要去掉交叉授权的）
                        $pids = array_intersect($pids,array_keys($myPower[3][$channel_id]));
                }
		$relation = array();//控制器与菜单权限关系
		foreach ($menus as $k=> $v )
		{
                        if(Auth::$userType==2 && !in_array($v['id'],$pids)){
                                unset($menus[$k]);
                                continue;
                        }
                        $relation[$v['c_id']] = $v['id'];  
		}
                $cids = array_column($menus,'c_id');//控制器
		$data = array_combine($cids,$menus);
   		//已授权操作
		$sql = "SELECT parent_id,permission_id FROM `user_extend_operation` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND `type`='3' AND `parent_id` IN (".implode(',',$pids).")";
		$data1 = $this->db()->getAll($sql);
		$_data = array();
		foreach ($data1 as $v1 )
		{
			$_data[$v1['parent_id']][$v1['permission_id']] = 1;
		}              
//echo $sql;exit;
                //所有操作
		$sql = "SELECT `p`.`id`,`b`.`label`,`b`.`c_id`,`b`.method_name FROM `operation` AS `b` INNER JOIN `permission` AS `p` ON `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE `p`.`is_deleted`='0' AND `b`.`c_id` IN (".implode(',',$cids).") AND `p`.`type`='3' ORDER BY `b`.`id`";

		$data2 = $this->db()->getAll($sql);
                //
                if(Auth::$userType==2){
                        $myPower=$_SESSION['__operation_p'];
                        if(!isset($myPower[3][$channel_id])){
                               return array(); 
                        }else{
                                foreach ($data2 AS $kk=>$vv){
                                        if(!in_array($vv['id'],$myPower[3][$channel_id])){
                                                unset($data2[$kk]);
                                        }
                                }
                        }
                }
           
                foreach ($data2 as $v2 )
		{
                        if(isset($data[$v2['c_id']]))
                        {
                                $v2['chk'] = false;
                                $data[$v2['c_id']]['son'][$v2['id']]=$v2;
                        }
		}

		foreach ($data as $k => $v )
		{
			if(!isset($v['son']))
			{
				unset($data[$k]);
				continue;
			}
			$data[$k]['chk'] = count($v['son']) == array_sum(array_column($v['son'],'chk'));
		}

		foreach ($data as $key2 => $val2 )
		{
			if(isset($relation[$key2]) && isset($_data[$relation[$key2]]))
			{
				$chks = $_data[$relation[$key2]];
				foreach ($chks as $key3 => $val3 )
				{
                                        if(isset($data[$key2]) && isset($data[$key2]['son'][$key3]))
                                        {
                                                $data[$key2]['son'][$key3]['chk'] = 1;
                                        }
				}
			}
			$data[$key2]['chk'] = count($data[$key2]['son']) == array_sum(array_column($data[$key2]['son'],'chk'));
		}
		return array_values($data);
	}

	public function saveOperation ($user_id,$channel_id,$oprs)
	{
		try{
                        if(Auth::$userType==2){
                                $myPower=$_SESSION['__operation_p'];
                                if(!isset($myPower[3][$channel_id])){
                                       return true; 
                                }
                        }

			$sql = "SELECT id,parent_id,permission_id FROM `user_extend_operation` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND `type`='3'";
			$olds = $this->db()->getAll($sql);
			$delArr = array();

			foreach ($olds as $key => $val )
			{
				if(!isset($oprs[$val['parent_id']]))
				{
                                        if(Auth::$userType==1 || (Auth::$userType==2 && in_array($val['permission_id'],$myPower[3][$channel_id]))){
                                                $delArr[] = $val['id'];
                                        }
				}
				else
				{
					$k = array_search($val['permission_id'],$oprs[$val['parent_id']]);
					if($k===false)
					{
                                                if(Auth::$userType==1 || (Auth::$userType==2 && in_array($val['permission_id'],$myPower[3][$channel_id]))){
                                                        $delArr[] = $val['id'];
                                                }
					}
					else
					{
						unset($oprs[$val['parent_id']][$k]);
					}
				}
			}

			if($delArr)
			{             
                                $sql = "DELETE FROM `user_extend_operation` WHERE `id` IN (".implode(',',$delArr).") ";
                                $this->db()->query($sql);
			}

			$arr = array();
			foreach ($oprs as $kk => $vv )
			{
				foreach ($vv as $val1 )
				{
					$arr[] = array('user_id'=>$user_id,'type'=>3,'source_id'=>$channel_id,'parent_id'=>$kk,'permission_id'=>$val1);
				}
			}

			if($arr)
			{
				$this->insertAll($arr,'user_extend_operation');
			}
		}
		catch(Exception $e){
			return false;
		}
		return true;
	}

	public function getListButton ($user_id,$channel_id)
	{
		$data = array();
		//menu
		$sql = "SELECT p.id,m.label,c.id AS c_id FROM `user_extend_menu` AS `u` INNER JOIN `permission` AS p ON u.permission_id=p.id INNER JOIN `menu` AS m ON m.id=p.resource_id AND m.is_deleted=p.is_deleted INNER JOIN `control` AS `c` ON m.c_id=c.id WHERE u.`user_id`='{$user_id}' AND u.type=3 AND u.source_id='{$channel_id}' AND p.type='1' AND p.is_deleted=c.is_deleted AND c.is_deleted=0 AND m.is_enabled=1";

		$menus = $this->db()->getAll($sql);
		if(!$menus)
		{
			return $data;
		}
 

                $pids = array_column($menus,'id');//parent_id
                $menus = array_combine($pids, $menus);
                if(Auth::$userType==2){
                        $myPower = $_SESSION["__menu_p"];
                        //delids  要删的（需要去掉交叉授权的）
                        $pids = array_intersect($pids,array_keys($myPower[3][$channel_id]));
                }
		$relation = array();//控制器与菜单权限关系
		foreach ($menus as $k=> $v )
		{
                        if(Auth::$userType==2 && !in_array($v['id'],$pids)){
                                unset($menus[$k]);
                                continue;
                        }
                        $relation[$v['c_id']] = $v['id'];  
		}
                $cids = array_column($menus,'c_id');//parent_id
		$data = array_combine($cids,$menus);
		//已授权按钮
		$sql = "SELECT parent_id,permission_id FROM `user_extend_list_button` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND `type`='3' AND `parent_id` IN (".implode(',',$pids).")";
		$data1 = $this->db()->getAll($sql);
		$_data = array();
		if($data1)
		{
			foreach ($data1 as $v1 )
			{
                                $_data[$v1['parent_id']][$v1['permission_id']] = 1;
			}
		}

		//所有按钮
		$sql = "(SELECT `p`.`id`,`b`.`label`,`b`.`c_id` FROM `button` AS `b` INNER JOIN `permission` AS `p` ON `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE `b`.`type`='1' AND `b`.`is_deleted`='0' AND `b`.`c_id` IN (".implode(',',$cids).") AND `p`.`type`='2' ORDER BY `b`.`display_order` DESC) UNION (SELECT `p`.`id`,`b`.`label`,`b`.`c_id` FROM `button` AS `b` INNER JOIN `permission` AS `p` ON `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE `b`.`is_deleted`='0' AND `b`.`id`<=3 AND `p`.`type`='2' ORDER BY `b`.`display_order` DESC)";

		$data2 = $this->db()->getAll($sql);
		$pubBtns = array();

                //过滤
                //
                if(Auth::$userType==2){
                        $myPower=$_SESSION['__button_p'];
                        if(!isset($myPower[3][$channel_id])){
                               return array(); 
                        }else{
                                foreach ($data2 AS $kk=>$vv){
                                        if(isset($relation[$vv['c_id']]) && !isset($myPower[3][$channel_id][$relation[$vv['c_id']]][$vv['id']])){
                                                unset($data2[$kk]);
                                        }
                                }
                        }
                }
   
		if($data2)
		{
			foreach ($data2 as $v2 )
			{
				$v2['chk'] = false;
				if($v2['c_id'])
				{
					$data[$v2['c_id']]['son'][$v2['id']]=$v2;
				}
				else
				{
					$pubBtns[$v2['id']] = $v2;
				}
			}
		}

		foreach ($pubBtns as $key => $val )
		{
			foreach ($data as $key1 => $val1 )
			{
                                if(isset($data[$key1]))
                                {
                                        $data[$key1]['son'][$key] = $val;
                                }
			}
		}

		foreach ($data as $key2 => $val2 )
		{
			if(isset($relation[$key2]) && isset($_data[$relation[$key2]]))
			{
				$chks = $_data[$relation[$key2]];
				foreach ($chks as $key3 => $val3 )
				{
                                        if(isset($data[$key2]) && isset($data[$key2]['son'][$key3]))
                                        {
                                                $data[$key2]['son'][$key3]['chk'] = 1;      
                                        }
					
				}
			}
			$data[$key2]['chk'] = count($data[$key2]['son']) == array_sum(array_column($data[$key2]['son'],'chk'));
		}

		return array_values($data);
	}

	public function saveListButton ($user_id,$channel_id,$btns)
	{
		try{
//			if(count($btns)==0)
//			{//授权全部取消
//				$sql = "DELETE FROM `user_extend_list_button` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND `type`='3' ";
//				$this->db()->query($sql);
//                                return true;
//			}
                        if(Auth::$userType==2){
                                $myPower=$_SESSION['__button_p'];
                                if(!isset($myPower[3][$channel_id])){
                                       return true; 
                                }
                        }
                        
			$sql = "SELECT id,parent_id,permission_id FROM `user_extend_list_button` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND `type`='3'";
			$olds = $this->db()->getAll($sql);
			$delArr = array();

			foreach ($olds as $key => $val )
			{
				if(!isset($btns[$val['parent_id']]))
				{
                                        
                                        if(Auth::$userType==1 || (Auth::$userType==2 && isset($myPower[3][$channel_id][$val['parent_id']][$val['permission_id']]))){
                                                $delArr[] = $val['id'];
                                        }
				}
				else
				{
					$k = array_search($val['permission_id'],$btns[$val['parent_id']]);
					if($k===false)
					{
						if(Auth::$userType==1 || (Auth::$userType==2 && isset($myPower[3][$channel_id][$val['parent_id']][$val['permission_id']]))){
                                                        $delArr[] = $val['id'];
                                                }
					}
					else
					{
						unset($btns[$val['parent_id']][$k]);
					}
				}
                                
			}

			if($delArr)
			{
				$sql = "DELETE FROM `user_extend_list_button` WHERE `id` IN (".implode(',',$delArr).") ";
				$this->db()->query($sql);
			}

			$arr = array();
			foreach ($btns as $kk => $vv )
			{
				foreach ($vv as $val1 )
				{
					$arr[] = array('user_id'=>$user_id,'type'=>3,'source_id'=>$channel_id,'parent_id'=>$kk,'permission_id'=>$val1);
				}
			}

			if($arr)
			{
				$this->insertAll($arr,'user_extend_list_button');
			}
		}
		catch(Exception $e){
			return false;
		}
		return true;
	}

	public function getViewButton ($user_id,$channel_id)
	{
		$data = array();
		//menu
		$sql = "SELECT p.id,m.label,c.id AS c_id FROM `user_extend_menu` AS `u` INNER JOIN `permission` AS p ON u.permission_id=p.id INNER JOIN `menu` AS m ON m.id=p.resource_id AND m.is_deleted=p.is_deleted INNER JOIN `control` AS `c` ON m.c_id=c.id WHERE u.`user_id`='{$user_id}' AND u.type=3 AND u.source_id='{$channel_id}' AND p.type='1' AND p.is_deleted=0 AND c.is_deleted=0 AND m.is_enabled=1";

		$menus = $this->db()->getAll($sql);
		if(!$menus)
		{
			return $data;
		}
                $pids = array_column($menus,'id');//parent_id
                $menus = array_combine($pids, $menus);
                if(Auth::$userType==2){
                        $myPower = $_SESSION["__menu_p"];
                        //delids  要删的（需要去掉交叉授权的）
                        $pids = array_intersect($pids,array_keys($myPower[3][$channel_id]));
                }
		$relation = array();//控制器与菜单权限关系
		foreach ($menus as $k=> $v )
		{
                        if(Auth::$userType==2 && !in_array($v['id'],$pids)){
                                unset($menus[$k]);
                                continue;
                        }
                        $relation[$v['c_id']] = $v['id'];  
		}
                $cids = array_column($menus,'c_id');//parent_id
		$data = array_combine($cids,$menus);
		//已授权按钮
		$sql = "SELECT parent_id,permission_id FROM `user_extend_view_button` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND `type`='3' AND `parent_id` IN (".implode(',',$pids).")";
		$data1 = $this->db()->getAll($sql);
		$_data = array();
		if($data1)
		{
			foreach ($data1 as $v1 )
			{
                                $_data[$v1['parent_id']][$v1['permission_id']] = 1;
			}
		}

		//所有按钮
		$sql = "(SELECT `p`.`id`,`b`.`label`,`b`.`c_id` FROM `button` AS `b` INNER JOIN `permission` AS `p` ON `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE `b`.`type`='2' AND `b`.`is_deleted`='0' AND `b`.`c_id` IN (".implode(',',$cids).") AND `p`.`type`='2' ORDER BY `b`.`display_order` DESC) UNION (SELECT `p`.`id`,`b`.`label`,`b`.`c_id` FROM `button` AS `b` INNER JOIN `permission` AS `p` ON `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` WHERE `b`.`is_deleted`='0' AND `b`.`id` IN (3,4) AND `p`.`type`='2' ORDER BY `b`.`display_order` DESC)";
		$data2 = $this->db()->getAll($sql);
                if(Auth::$userType==2){
                        $myPower=$_SESSION['__button_p'];
                        if(!isset($myPower[3][$channel_id])){
                               return array(); 
                        }else{
                                foreach ($data2 AS $kk=>$vv){
                                        if(isset($relation[$vv['c_id']]) && !isset($myPower[3][$channel_id][$relation[$vv['c_id']]][$vv['id']])){
                                                unset($data2[$kk]);
                                        }
                                }
                        }
                }
		$pubBtns = array();
		if($data2)
		{
			foreach ($data2 as $v2 )
			{
				$v2['chk'] = false;
				if($v2['c_id'])
				{
					$data[$v2['c_id']]['son'][$v2['id']]=$v2;
				}
				else
				{
					$pubBtns[$v2['id']] = $v2;
				}
			}
		}


		foreach ($pubBtns as $key => $val )
		{
			foreach ($data as $key1 => $val1 )
			{
                                if(isset($data[$key1]))
                                {
                                        $data[$key1]['son'][$key] = $val;
                                }
			}
		}

		foreach ($data as $key2 => $val2 )
		{
			if(isset($relation[$key2]) && isset($_data[$relation[$key2]]))
			{
				$chks = $_data[$relation[$key2]];
				foreach ($chks as $key3 => $val3 )
				{
                                        if(isset($data[$key2]) && isset($data[$key2]['son'][$key3]))
                                        {
                                                $data[$key2]['son'][$key3]['chk'] = 1;      
                                        }	
				}
			}
			$data[$key2]['chk'] = count($data[$key2]['son']) == array_sum(array_column($data[$key2]['son'],'chk'));
		}

		return array_values($data);
	}

	public function saveViewButton ($user_id,$channel_id,$btns)
	{
		try{
//			if(count($btns)==0)
//			{//授权全部取消
//				$sql = "DELETE FROM `user_extend_view_button` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND `type`='3' ";
//				return $this->db()->query($sql);
//			}

                        if(Auth::$userType==2){
                                $myPower=$_SESSION['__button_p'];
                                if(!isset($myPower[3][$channel_id])){
                                       return true; 
                                }
                        }
			//授权部分取消
			$sql = "SELECT id,parent_id,permission_id FROM `user_extend_view_button` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND `type`='3'";
			$olds = $this->db()->getAll($sql);
			$delArr = array();
			foreach ($olds as $key => $val )
			{
				if(!isset($btns[$val['parent_id']]))
				{
                                        if(Auth::$userType==1 || (Auth::$userType==2 && isset($myPower[3][$channel_id][$val['parent_id']][$val['permission_id']]))){
                                                $delArr[] = $val['id'];
                                        }
				}
				else
				{
					$k = array_search($val['permission_id'],$btns[$val['parent_id']]);
					if($k===false)
					{
						if(Auth::$userType==1 || (Auth::$userType==2 && isset($myPower[3][$channel_id][$val['parent_id']][$val['permission_id']]))){
                                                        $delArr[] = $val['id'];
                                                }
					}
					else
					{
						unset($btns[$val['parent_id']][$k]);
					}
				}
			}

			if($delArr)
			{
				$sql = "DELETE FROM `user_extend_view_button` WHERE `id` IN (".implode(',',$delArr).") ";
				$this->db()->query($sql);
			}

			$arr = array();
			foreach ($btns as $kk => $vv )
			{
				foreach ($vv as $val1 )
				{
					$arr[] = array('user_id'=>$user_id,'type'=>3,'source_id'=>$channel_id,'parent_id'=>$kk,'permission_id'=>$val1);
				}
			}

			if($arr)
			{
				$this->insertAll($arr,'user_extend_view_button');
			}
		}
		catch(Exception $e){
			return false;
		}
		return true;
	}

	public function getSubdetail ($user_id,$channel_id)
	{
		//主对象
		$sql = "SELECT `m`.`c_id`,`m`.`label`,`m`.`code`,`p`.`id` AS `pid` FROM `user_extend_menu` AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` INNER JOIN `menu` AS `m` ON `m`.`id`=`p`.`resource_id` WHERE `u`.`user_id`='{$user_id}' AND `u`.`source_id`='{$channel_id}' AND `u`.`type`='3' AND `p`.`type`='1' AND `m`.`is_enabled`='1' AND `p`.`is_deleted`=0 ";
                
		$data = $this->db()->getAll($sql);
		if(!$data)
		{
			return array();
		}
                $cids = array_column($data,'c_id');
		//明细对象
//		$sql = "SELECT `c`.`id`,`c`.`label`,`c`.`code`,`p`.`id` AS `pid`,`c`.`parent_id` FROM `control` AS `c` INNER JOIN `permission` AS `p` ON `c`.`id`=`p`.`resource_id` AND `c`.`is_deleted`=`p`.`is_deleted` WHERE EXISTS (SELECT 1 FROM `user_extend_menu` AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` INNER JOIN `menu` AS `m` ON `m`.`id`=`p`.`resource_id` WHERE `u`.`user_id`='{$user_id}' AND `u`.`source_id`='{$channel_id}' AND `u`.`type`='3' AND `m`.`c_id`=`c`.`parent_id` AND `p`.`type`='1' AND `m`.`is_enabled`='1') AND `c`.`type`='3' AND `p`.`type`='4' AND `c`.`is_deleted`='0' ORDER BY `c`.`id` ";
		$sql = "SELECT `c`.`id`,`c`.`label`,`c`.`code`,`p`.`id` AS `pid`,`c`.`parent_id` FROM `control` AS `c` INNER JOIN `permission` AS `p` ON `c`.`id`=`p`.`resource_id` AND `c`.`is_deleted`=`p`.`is_deleted` WHERE `c`.`parent_id` IN (".implode(',',$cids).") AND `c`.`type`='3' AND `p`.`type`='4' AND `c`.`is_deleted`='0' ORDER BY `c`.`id` ";

		$sql = "SELECT `main`.*,if(`r`.`permission_id`,1,0) AS `chk` FROM (".$sql.") AS `main` LEFT JOIN (SELECT `permission_id` FROM `user_extend_subdetail` WHERE `user_id`='".$user_id."' AND `source_id`='{$channel_id}' AND `type`='3') AS `r` ON `main`.`pid`=`r`.`permission_id` ORDER BY `main`.`pid`";

		$data1 = $this->db()->getAll($sql);
                Util::array_unique_fb($data1,'id');
		if(!$data1)
		{
			return array();
		}
		$cids = array_column($data,'c_id');
		$data = array_combine($cids,$data);

                if(Auth::$userType==2){
                        $myPower = $_SESSION["__menu_p"];
                }

		foreach ($data1 as $k=> $val )
		{
                        if(Auth::$userType==2 && !isset($myPower[3][$channel_id][$val['pid']])){
                                continue;
                        }
                        $data[$val['parent_id']]['son'][] = $val;
		}

		foreach ($data as $k => $v )
		{
			if(!isset($data[$k]['son']))
			{
				unset($data[$k]);
				continue;
			}

                        if(Auth::$userType==2 && !isset($myPower[3][$channel_id][$v['pid']])){
                                unset($data[$k]);
				continue;
                        }
			$data[$k]['chk'] = count($v['son']) == array_sum(array_column($v['son'],'chk'));
		}

		return array_values($data);
	}


	public function saveSubdetail ($user_id,$channel_id,$dtls)
	{
		try{
                        if(Auth::$userType==2){
                                $myPower=$_SESSION['__menu_p'];
                                if(!isset($myPower[3][$channel_id])){
                                       return true; 
                                }
                        }
			//授权取消
			$sql = "SELECT id,parent_id,permission_id FROM `user_extend_subdetail` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND `type`='3'";
			$olds = $this->db()->getAll($sql);
			$delArr = array();

			foreach ($olds as $key => $val )
			{
				if(!isset($dtls[$val['parent_id']]))
				{
                                        if(Auth::$userType==1 || (Auth::$userType==2 && isset($myPower[3][$channel_id][$val['permission_id']]))){
                                                $delArr[] = $val['id'];
                                        }
				}
				else
				{
					$k = array_search($val['permission_id'],$dtls[$val['parent_id']]);
					if($k===false)
					{
                                                if(Auth::$userType==1 || (Auth::$userType==2 && isset($myPower[3][$channel_id][$val['permission_id']]))){
                                                        $delArr[] = $val['id'];
                                                }
					}
					else
					{
						unset($dtls[$val['parent_id']][$k]);
					}
				}
			}

			if($delArr)
			{
				try{
					//删除明细属性控制
					$sql = "DELETE us.* FROM `user_scope` AS us,`user_extend_subdetail` AS ues,`permission` AS p,`field_scope` AS fs,`permission` AS pp WHERE us.permission_id=pp.id AND ues.`permission_id`=p.id AND p.resource_id=fs.c_id AND pp.resource_id=fs.id AND pp.type=5 AND ues.type=3 AND ues.user_id='{$user_id}' AND ues.source_id='{$channel_id}' AND us.type=3 AND us.source_id='{$channel_id}' AND ues.user_id='{$user_id}' AND ues.id IN (".implode(',',$delArr).")";
					$this->db()->query($sql);

					$sql = "DELETE `ues`.*,`uelb`.*,`a`.* FROM `user_extend_subdetail` AS `ues` LEFT JOIN `user_extend_subdetail_button` AS `uelb` ON `ues`.`permission_id`=`uelb`.`parent_id` AND `ues`.`user_id`=`uelb`.`user_id` AND `ues`.`type`=`uelb`.`type` AND `ues`.`source_id`=`uelb`.`source_id` LEFT JOIN `user_extend_subdetail_operation` AS `a` ON `ues`.`permission_id`=`a`.`parent_id` AND `ues`.`user_id`=`a`.`user_id` AND `ues`.`type`=`a`.`type` AND `ues`.`source_id`=`a`.`source_id` WHERE `ues`.`id` IN (".implode(',',$delArr).") ";

					$this->db()->query($sql);
				}
				catch(Exception $e){
//					echo $sql;
					return false;
				}
			}

			$arr = array();
			foreach ($dtls as $kk => $vv )
			{
				foreach ($vv as $val1 )
				{
					$arr[] = array('user_id'=>$user_id,'type'=>3,'source_id'=>$channel_id,'parent_id'=>$kk,'permission_id'=>$val1);
				}
			}

			if($arr)
			{
				$this->insertAll($arr,'user_extend_subdetail');
			}
		}
		catch(Exception $e){
			return false;
		}
		return true;
	}

	public function getSubdetailButton ($user_id,$channel_id)
	{
		$data = array();
		//明细对象
		$sql = "SELECT `c`.`label`,`p`.`id`,`c`.`id` AS `c_id` FROM `user_extend_subdetail` AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` INNER JOIN `control` AS `c` ON `c`.`id`=`p`.`resource_id` AND `p`.`is_deleted`=`c`.`is_deleted` WHERE `u`.`type`='3' AND `u`.`user_id`='{$user_id}' AND `u`.`source_id`='{$channel_id}' AND `p`.`type`='4' AND `p`.`is_deleted`='0'";
		$ctls = $this->db()->getAll($sql);
		if(!$ctls)
		{
			return $data;
		}
		
                
                $pids = array_column($ctls,'id');//parent_id
                $ctls = array_combine($pids, $ctls);
                if(Auth::$userType==2){
                        $myPower = $_SESSION["__menu_p"];
                        //delids  要删的（需要去掉交叉授权的）
                        $pids = array_intersect($pids,array_keys($myPower[3][$channel_id]));
                }
		$relation = array();//控制器与菜单权限关系
		foreach ($ctls as $k=> $v )
		{
                        if(Auth::$userType==2 && !in_array($v['id'],$pids)){
                                unset($ctls[$k]);
                                continue;
                        }
                        $relation[$v['c_id']] = $v['id'];  
		}
                $cids = array_column($ctls,'c_id');//控制器
		$data = array_combine($cids,$ctls);

		//所有按钮
		$sql = "SELECT `p`.`id`,`b`.`label`,`b`.`c_id`,IF(`u`.`permission_id`,1,0) AS `chk` FROM `button` AS `b` INNER JOIN `permission` AS `p` ON `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` LEFT JOIN (SELECT permission_id FROM `user_extend_subdetail_button` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND `type`='3' AND `parent_id` IN (".implode(',',$pids).")) AS `u` ON `u`.`permission_id`=`p`.`id` WHERE `b`.`type`='1' AND `b`.`is_deleted`='0' AND `b`.`c_id` IN (".implode(',',$cids).") AND `p`.`type`='2' ORDER BY `b`.`display_order` DESC";
		$data2 = $this->db()->getAll($sql);
                if(Auth::$userType==2){
                        $myPower=$_SESSION['__button_p'];
                        if(!isset($myPower[3][$channel_id])){
                               return array(); 
                        }else{
                                foreach ($data2 AS $kk=>$vv){
                                        if(isset($vv['c_id']) && !isset($myPower[3][$channel_id][$relation[$vv['c_id']]][$vv['id']])){
                                                unset($data2[$kk]);
                                        }
                                }
                        }
                }
		foreach ($data2 as $v2 )
		{
                        if(isset($data[$v2['c_id']]))
                        {
                                $data[$v2['c_id']]['son'][$v2['id']]=$v2;
                        }
		}

		foreach ($data as $k4 => $v4 )
		{
			if(!isset($v4['son']))
			{
				unset($data[$k4]);
				continue;
			}
			$data[$k4]['chk'] = count($v4['son']) == array_sum(array_column($v4['son'],'chk'));

		}

		return array_values($data);
	}

	public function saveSubdetailButton ($user_id,$channel_id,$btns)
	{
		try{
                        if(Auth::$userType==2){
                                $myPower=$_SESSION['__button_p'];
                                if(!isset($myPower[3][$channel_id])){
                                       return true; 
                                }
                        }
                        
			//授权取消
			$sql = "SELECT id,parent_id,permission_id FROM `user_extend_subdetail_button` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND `type`='3'";
			$olds = $this->db()->getAll($sql);
			$delArr = array();

			foreach ($olds as $key => $val )
			{
				if(!isset($btns[$val['parent_id']]))
				{
                                        if(Auth::$userType==1 || (Auth::$userType==2 && isset($myPower[3][$channel_id][$val['parent_id']][$val['permission_id']]))){
                                                $delArr[] = $val['id'];
                                        }
				}
				else
				{
					$k = array_search($val['permission_id'],$btns[$val['parent_id']]);
					if($k===false)
					{
                                                if(Auth::$userType==1 || (Auth::$userType==2 && isset($myPower[3][$channel_id][$val['parent_id']][$val['permission_id']]))){
                                                        $delArr[] = $val['id'];
                                                }
					}
					else
					{
						unset($btns[$val['parent_id']][$k]);
					}
				}
			}

			if($delArr)
			{
				$sql = "DELETE FROM `user_extend_subdetail_button` WHERE `id` IN (".implode(',',$delArr).") ";
				$this->db()->query($sql);
			}

			$arr = array();
			foreach ($btns as $kk => $vv )
			{
				foreach ($vv as $val1 )
				{
					$arr[] = array('user_id'=>$user_id,'type'=>3,'source_id'=>$channel_id,'parent_id'=>$kk,'permission_id'=>$val1);
				}
			}

			if($arr)
			{
				$this->insertAll($arr,'user_extend_subdetail_button');
			}
		}
		catch(Exception $e){
			return false;
		}
		return true;
	}

	public function getSubdetailOperation ($user_id,$channel_id)
	{
		$data = array();
		//明细对象
		$sql = "SELECT `c`.`label`,`p`.`id`,`c`.`id` AS `c_id` FROM `user_extend_subdetail` AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` INNER JOIN `control` AS `c` ON `c`.`id`=`p`.`resource_id` AND `p`.`is_deleted`=`c`.`is_deleted` WHERE `u`.`type`='3' AND `u`.`user_id`='{$user_id}' AND `u`.`source_id`='{$channel_id}' AND `p`.`type`='4' AND `p`.`is_deleted`='0'";
		$ctls = $this->db()->getAll($sql);
		if(!$ctls)
		{
			return $data;
		}
		$pids = array_column($ctls,'id');//parent_id
                if(Auth::$userType==2){
                        $myPower = $_SESSION["__menu_p"];
                        //delids  要删的（需要去掉交叉授权的）
                        $pids = array_intersect($pids,array_keys($myPower[3][$channel_id]));
                }

		$relation = array();//控制器与菜单权限关系
		foreach ($ctls as $v )
		{
                        if(Auth::$userType==2 && !in_array($v['id'],$pids)){
                                unset($menus[$k]);
                                continue;
                        }
			$relation[$v['c_id']] = $v['id'];
		}
		$cids = array_column($ctls,'c_id');//控制器

		$data = array_combine($cids,$ctls);

		//所有操作
		$sql = "SELECT `p`.`id`,`b`.`label`,`b`.`c_id`,IF(`u`.`permission_id`,1,0) AS `chk` FROM `operation` AS `b` INNER JOIN `permission` AS `p` ON `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` LEFT JOIN (SELECT permission_id FROM `user_extend_subdetail_operation` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND `type`='3' AND `parent_id` IN (".implode(',',$pids).")) AS `u` ON `u`.`permission_id`=`p`.`id` WHERE `b`.`is_deleted`='0' AND `b`.`c_id` IN (".implode(',',$cids).") AND `p`.`type`='3' ORDER BY `b`.`id` ASC";


		$data2 = $this->db()->getAll($sql);
                if(Auth::$userType==2){
                        $myPower=$_SESSION['__operation_p'];
                        if(!isset($myPower[3][$channel_id])){
                               return array(); 
                        }else{
                                foreach ($data2 AS $kk=>$vv){
                                        if(!in_array($vv['id'],$myPower[3][$channel_id])){
                                                unset($data2[$kk]);
                                        }
                                }
                        }
                }
		foreach ($data2 as $v2 )
		{
                        if(isset($data[$v2['c_id']]))
                        {
                                $data[$v2['c_id']]['son'][$v2['id']]=$v2;
                        }
		}

		foreach ($data as $k4 => $v4 )
		{
			if(!isset($v4['son']))
			{
				unset($data[$k4]);
				continue;
			}
			$data[$k4]['chk'] = count($v4['son']) == array_sum(array_column($v4['son'],'chk'));
		}

		return array_values($data);
	}

	public function saveSubdetailOperation ($user_id,$channel_id,$oprs)
	{
		try{

                        if(Auth::$userType==2){
                                $myPower=$_SESSION['__operation_p'];
                                if(!isset($myPower[3][$channel_id])){
                                       return true; 
                                }
                        }			
			//授权取消
			$sql = "SELECT id,parent_id,permission_id FROM `user_extend_subdetail_operation` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND `type`='3'";
			$olds = $this->db()->getAll($sql);

			$delArr = array();
			foreach ($olds as $key => $val )
			{
				if(!isset($oprs[$val['parent_id']]))
				{
                                        if(Auth::$userType==1 || (Auth::$userType==2 && in_array($val['permission_id'],$myPower[3][$channel_id]))){
                                                $delArr[] = $val['id'];
                                        }
				}
				else
				{
					$k = array_search($val['permission_id'],$oprs[$val['parent_id']]);
					if($k===false)
					{
                                                if(Auth::$userType==1 || (Auth::$userType==2 && in_array($val['permission_id'],$myPower[3][$channel_id]))){
                                                        $delArr[] = $val['id'];
                                                }
					}
					else
					{
						unset($oprs[$val['parent_id']][$k]);
					}
				}
			}

			if($delArr)
			{        
                                $sql = "DELETE FROM `user_extend_subdetail_operation` WHERE `id` IN (".implode(',',$delArr).") ";
                                $this->db()->query($sql);
				
			}

			$arr = array();
			foreach ($oprs as $kk => $vv )
			{
				foreach ($vv as $val1 )
				{
					$arr[] = array('user_id'=>$user_id,'type'=>3,'source_id'=>$channel_id,'parent_id'=>$kk,'permission_id'=>$val1);
				}
			}

			if($arr)
			{
				$this->insertAll($arr,'user_extend_subdetail_operation');
			}
		}
		catch(Exception $e){
			return false;
		}
		return true;
	}

	public function listScope ($user_id,$channel_id)
	{
		//菜单和明细权限
		$sql = "(SELECT `u`.`id`,`u`.`permission_id`,`p`.`name`,0 AS `parent_id`,1 AS `type` FROM `user_extend_menu` AS `u`,`permission` AS `p` WHERE `u`.`permission_id`=`p`.`id` AND `u`.`user_id`='{$user_id}' AND `u`.`type`=3 AND `u`.`source_id`='{$channel_id}' AND `p`.`type`=1)";
		$sql .= " UNION (SELECT ud.id,ud.permission_id,p.name,ud.parent_id,2 AS `type` FROM `user_extend_subdetail` AS ud INNER JOIN `user_extend_menu` AS um ON ud.parent_id=um.permission_id AND ud.user_id=um.user_id AND ud.type=um.type INNER JOIN `permission` AS p ON ud.permission_id=p.id WHERE p.type=4 AND p.is_deleted=0 AND ud.user_id='{$user_id}' AND `ud`.`source_id`='{$channel_id}' AND `ud`.`type`=3 )";

		$p1 = $this->db()->getAll($sql);
		if(!$p1)
		{
			return array();
		}
		Util::array_unique_fb($p1,'id');

		//属性权限
		$sql = "(SELECT main.id,main.label,main.parent_id,ifnull(us.permission_id,0) AS chk,ifnull(us.scope,0) AS v,1 AS type,concat(main.id,'-1') AS fb FROM (SELECT t.id,t.label,t.f_id,pp.id AS parent_id FROM (SELECT p1.id,f.label,f.c_id,f.id AS f_id FROM `permission` AS p1,`field_scope` AS f,`control` AS c WHERE p1.resource_id=f.id AND p1.is_deleted=f.is_deleted AND p1.type=5 AND f.is_enabled=1 AND f.c_id=c.id AND c.parent_id=0) AS t,`permission` AS pp,`menu` AS mm WHERE t.c_id=mm.c_id AND mm.id=pp.resource_id AND EXISTS (SELECT null FROM `user_extend_menu` AS `u`,`permission` AS `p2`,`menu` AS `m` WHERE `u`.`permission_id`=`p2`.`id` AND `u`.`user_id`=".$user_id." AND `p2`.`type`=1 AND `u`.`type`=3 AND `u`.`source_id`='{$channel_id}' AND m.id=p2.resource_id AND m.c_id=t.c_id) AND pp.type=1) AS main LEFT JOIN `user_scope` AS us ON main.id=us.permission_id AND us.user_id=".$user_id." AND us.type=3) UNION (SELECT tmp.id,tmp.label,tmp.parent_id,ifnull(us.permission_id,0) AS chk,ifnull(us.scope,0) AS v,2 AS type,concat(tmp.id,'-2') AS fb FROM (SELECT pp.id,f.label,f.c_id,m.permission_id AS parent_id FROM `field_scope` AS f,`permission` AS pp,(SELECT ud.permission_id,c.id FROM `user_extend_subdetail` AS ud INNER JOIN `user_extend_menu` AS um ON ud.parent_id=um.permission_id AND ud.user_id=um.user_id INNER JOIN `permission` AS p ON ud.permission_id=p.id AND p.type=4 INNER JOIN `control` AS c ON p.resource_id=c.id AND p.is_deleted=c.is_deleted WHERE um.user_id=".$user_id.") AS m WHERE f.id=pp.resource_id AND f.is_deleted=pp.is_deleted AND f.is_enabled=1 AND pp.type=5 AND f.c_id=m.id) AS tmp LEFT JOIN `user_scope` AS us ON tmp.id=us.permission_id AND us.user_id=".$user_id." AND us.type=3)";

		$p2 = $this->db()->getAll($sql);
		if(!$p2)
		{
			return array();
		}
		Util::array_unique_fb($p2,'fb');

		$r1 = array();
		$r2 = array();
		$relation = array();
		foreach ($p1 as $key => $val )
		{
			${"r".$val['type']}[$val['permission_id']] = $val;
			($val['type']==2) && $relation[$val['permission_id']] =$val['parent_id'] ;
		}

		foreach ($p2 as $key2 => $val2 )
		{
			${"r".$val2['type']}[$val2['parent_id']]['son'][] = $val2;
		}

		foreach ($r2 as $key3 => $val3 )
		{
			if(isset($val3['son']) && count($val3['son']))
			{
				if(isset($relation[$key3]) && isset($r1[$relation[$key3]]))
				{
					$r1[$relation[$key3]]['sub'][] = $val3;
				}
			}
		}
		foreach ($r1 as $key => $val )
		{
			if(!!(empty($val['son']) && empty($val['sub'])))
			{
				unset($r1[$key]);
			}
		}

		return array_values($r1);
	}

	public function saveScope ($user_id,$channel_id,$data)
	{
		$sql = "SELECT * FROM `user_scope` WHERE `type`=3 AND `user_id`=".$user_id." AND `source_id`=".$channel_id;
		$oldScope = $this->db()->getAll($sql);
		$oldids = array_column($oldScope,'permission_id');
		$datas = array_combine(array_column($oldScope,'id'),$oldids);
		$scopes = array_combine($oldids,array_column($oldScope,'scope'));
		$newScopeP = array_keys($data);
		$addIds = array_diff($newScopeP,$oldids);
		$delIds = array_diff($oldids,$newScopeP);
		$xids = array_intersect($oldids,$newScopeP);
		if($delIds)
		{
			$del = array();
			$tmp = array_flip($datas);

			foreach ($delIds as $val )
			{
				if(isset($tmp[$val]))
				{
					$del[] = $tmp[$val];
				}
			}

			if($del)
			{

				try{
					$sql = "DELETE FROM `user_scope` WHERE `id` IN (".implode(',',$del).") AND type=3";
					$this->db()->query($sql);
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		if($addIds)
		{
			$arr=array();
			foreach ($addIds as $id )
			{
				$arr[] = array('user_id'=>$user_id,'permission_id'=>$id,'type'=>3,'source_id'=>$channel_id,'scope'=>$data[$id]);
			}
			if($arr)
			{
				try{
					$this->insertAll($arr,'user_scope');
				}
				catch(Exception $e){
					return false;
				}
			}
		}
		if($xids)
		{
			$upArr = array();
			$datas = array_flip($datas);
			foreach ($xids as $val )
			{
				if($scopes[$val]!=$data[$val])
				{
					try{
						$sql = "UPDATE `user_scope` SET `scope`='".$data[$val]."' WHERE `id`=".$datas[$val];
						$this->db()->query($sql);
					}
					catch(Exception $e){
						return false;
					}
				}
			}
		}
		return true;
	}

	public function deleteExtendPermission ($channel_id,$user_id)
	{
		$user_scope = 'user_scope';//属性控制
		$user_extend_menu = 'user_extend_menu';//扩展菜单权限
		$user_extend_list_button = 'user_extend_list_button';//扩展列表按钮
		$user_extend_view_button = 'user_extend_view_button';//扩展查看按钮
		$user_extend_operation = 'user_extend_operation';//扩展操作权限
		$user_extend_subdetail = 'user_extend_subdetail';//扩展明细权限
		$user_extend_subdetail_button = 'user_extend_subdetail_button';//扩展明细按钮
		$user_extend_subdetail_operation = 'user_extend_subdetail_operation';//扩展明细操作

		$tables = array(
			$user_scope,
			$user_extend_menu,
			$user_extend_list_button,
			$user_extend_view_button,
			$user_extend_operation,
			$user_extend_subdetail,
			$user_extend_subdetail_button,
			$user_extend_subdetail_operation
		);

		$sqlarr = array();

		foreach ($tables as $val )
		{
			$sqlarr[] = 'DELETE FROM `'.$val.'` where user_id = '.$user_id.' AND type=3 AND source_id='.$channel_id;
		}

		return  $this->db()->commit($sqlarr);
	}

	public function getUsers ($user_id)
	{
		$sql = "SELECT distinct u.id,u.account,u.real_name FROM `user` AS u,user_channel AS s WHERE u.id=s.user_id ";
		return $this->db()->getAll($sql);
	}

	public function getChannels ($user_id,$id)
	{
		$sql = "SELECT c.id,c.channel_name FROM `".$this->table()."` AS u INNER JOIN `sales_channels` AS c ON u.channel_id=c.id AND c.is_deleted=0 WHERE u.`user_id`='{$user_id}' AND u.id<>".$id;
		return $this->db()->getAll($sql);
	}
	public function getAllChannels_onoffline ($order_type=0)
	{
		 $sql = "SELECT `id`,`channel_name` FROM `sales_channels` where is_deleted=0";
		 if($order_type)
		 	$sql.=" and channel_class='{$order_type}'";
         return $this->db()->getAll($sql);
	}
	public function savePermission ($user_id,$channel_id,$u_id,$c_id)
	{
		//
		$user_scope = 'user_scope';//属性控制
		$user_extend_menu = 'user_extend_menu';//扩展菜单权限
		$user_extend_list_button = 'user_extend_list_button';//扩展列表按钮
		$user_extend_view_button = 'user_extend_view_button';//扩展查看按钮
		$user_extend_operation = 'user_extend_operation';//扩展操作权限
		$user_extend_subdetail = 'user_extend_subdetail';//扩展明细权限
		$user_extend_subdetail_button = 'user_extend_subdetail_button';//扩展明细按钮
		$user_extend_subdetail_operation = 'user_extend_subdetail_operation';//扩展明细操作

		$table = array(
			$user_scope,
			$user_extend_menu,
			$user_extend_list_button,
			$user_extend_view_button,
			$user_extend_operation,
			$user_extend_subdetail,
			$user_extend_subdetail_button,
			$user_extend_subdetail_operation
		);

		$us_sql = "SELECT user_id,type,source_id,permission_id,scope FROM `".$user_scope."` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND type=3";
		$ue_sql = "SELECT user_id,type,source_id,permission_id FROM `".$user_extend_menu."` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND type=3";
		$ueb_sql = "SELECT user_id,type,source_id,permission_id,parent_id FROM `".$user_extend_list_button."` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND type=3";
		$uev_sql = "SELECT user_id,type,source_id,permission_id,parent_id FROM `".$user_extend_view_button."` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND type=3";
		$ueo_sql = "SELECT user_id,type,source_id,permission_id,parent_id FROM `".$user_extend_operation."` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND type=3";
		$ues_sql = "SELECT user_id,type,source_id,permission_id,parent_id FROM `".$user_extend_subdetail."` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND type=3";
		$uesb_sql = "SELECT user_id,type,source_id,permission_id,parent_id FROM `".$user_extend_subdetail_button."` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND type=3";
		$ueso_sql = "SELECT user_id,type,source_id,permission_id,parent_id FROM `".$user_extend_subdetail_operation."` WHERE `user_id`='{$user_id}' AND `source_id`='{$channel_id}' AND type=3";

		$sqlarr=array($us_sql,$ue_sql,$ueb_sql,$uev_sql,$ueo_sql,$ues_sql,$uesb_sql,$ueso_sql);

		$resarr = array();
		foreach($sqlarr as $key=>$val){
			$reaarrs= $this->db()->getAll($val);
			$resarr[] = $reaarrs;
		}
                unset($reaarrs,$sqlarr);
		$res =  $this->cancelCopyPermissions($table,$u_id,$c_id);

		if($res)
                {
        		$arr = array_combine($table,$resarr);
                        $tmp = array();
                        try{
                                foreach ($c_id as $value) 
                                {//遍历渠道
                                        foreach ($arr as $k => $v) 
                                        {//$k 表名  $v 结果集
                                                if($v===array())
                                                {
                                                        continue;
                                                }
                                                foreach($v AS $vv)
                                                {
                                                        $vv['user_id']=$u_id;
                                                        $vv['source_id']=$value;
                                                        $tmp[] = $vv;
                                                        if(count($tmp)==2000)
                                                        {
                                                            $this->insertAll($tmp,$k);
                                                            $tmp = array();                
                                                        }
                                                }
                                                //记录太多，不能一次插入
                                                $cnt = count($tmp);
                                                if($cnt)
                                                {
                                                    $this->insertAll($tmp,$k);
                                                    $tmp=array();
                                                }
                                        }
                                                                       
                                }
                        }
                        catch(Exception $e)
                        {
                                var_dump($e);
                                return false;
                        }                        
			return true;
		}

		return true;

	}

	public function cancelCopyPermissions ($table,$u_id,$c_id)
	{
		foreach($table as $key=>$val)
                {
			$sqlarr[] = 'DELETE FROM '.$val.' where user_id = '.$u_id.' AND type=3 AND `source_id` IN ('.implode(',',$c_id).')';
		}
		return  $this->db()->commit($sqlarr);
	}
	
	
	//通过渠道拿取所有销售顾问
	public function getallusersbychannelid($channelid)
	{
		$sql = "SELECT `m`.`id`,`m`.`power`,`c`.`channel_name`,`c`.`channel_code`,u.real_name,u.account FROM `" . $this->table() . "` AS `m` INNER JOIN `sales_channels` AS `c` ON `c`.`id`=`m`.`channel_id` INNER JOIN `user` AS u ON m.user_id=u.id ";
		$str = '';
		if (!empty($channelid)) {
				$str .= "`m`.`channel_id`='" . $$channelid . "' AND ";
		}
		if ($str) {
				$str = rtrim($str, "AND "); //这个空格很重要
				$sql .=" WHERE `u`.`is_on_work`=1 AND " . $str;
		}
		$sql .= " ORDER BY `m`.`id` DESC";
		$data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
		return $data;
	}
}

?>