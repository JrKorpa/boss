<?php

/**
 *  -------------------------------------------------
 *   @file		: RelStyleFactoryModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 10:34:21
 *   @update	:
 *  -------------------------------------------------
 */
class RelStyleFactoryModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'rel_style_factory';
        $this->pk = 'f_id';
        $this->_prefix = '';
        $this->_dataObject = array("f_id" => " ",
            "style_id" => "款式ID",
            "style_sn" => "款式编号",
            "factory_id" => "工厂id",
            "factory_sn" => "工厂模号",
            "factory_fee" => "工厂费用",
            "xiangkou" => "镶口",
            "is_cancel" => "是否作废，1正常，2作废",
            "is_def" => "是否默认;0为否;1为是;",
            "is_factory" => "是否默认工厂；0为否 ；1为是");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url RelStyleFactoryController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        //$sql = "SELECT a.style_sn,a.factory_name,a.factory_sn,a.xiangkou,a.f_id,r.is_def,r.is_factory FROM `" . $this->table() . "` r,app_factory_apply a WHERE 1 and r.f_id=a.f_id and a.status=2 and r.is_cancel=1 and a.type=1";
        $sql = "SELECT `r`.* FROM `" . $this->table() . "` as `r`,base_style_info s WHERE `r`.style_id=s.style_id and `r`.`is_cancel`=1";
        if (isset($where['style_sn']) && $where['style_sn'] != '') {
            $sql .= " and `r`.`style_sn` = '{$where['style_sn']}' ";
        }
        if (isset($where['style_id']) && $where['style_id'] > 0) {
            $sql .= " and `r`.`style_id` = '{$where['style_id']}' ";
        }
       
        if($_SESSION['userType']<>1){          
            $groupUser = new GroupUserModel(1);
            $is_wukong_user = $groupUser->checkGroupUser(7,$_SESSION['userId']);
            $is_style_edit_user = $groupUser->checkGroupUser(6,$_SESSION['userId']);
            if($is_wukong_user)
                $sql .= " and s.is_wukong='1' ";
            elseif($is_style_edit_user)
                $sql .= " and (s.is_wukong<>'1' or s.is_wukong is null ) ";
            else
                $sql .=" and s.is_wukong='-1'";
        }    


        $sql .= " ORDER BY `r`.`f_id` DESC";
        //echo $sql;
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }
    

    /**
     * 获取所有满足条件的数据
     * @param type $where
     * @return type
     */
    function getAllList($where) {
        $sql = "SELECT `r`.* FROM `" . $this->table() . "` as `r` WHERE `r`.`is_cancel`=1";
        if (isset($where['style_sn']) && $where['style_sn'] != '') {
            $sql .= " and `r`.`style_sn` = '{$where['style_sn']}' ";
        }
        if (isset($where['style_id']) && $where['style_id'] > 0) {
            $sql .= " and `r`.`style_id` = '{$where['style_id']}' ";
        }
        $sql .= " ORDER BY `r`.`f_id` DESC";
        $data = $this->db()->getAll($sql);
        return $data;
    }
    

    function updateIsFactory($id, $style_id, $factory_id) {
        $sql = "update `" . $this->table() . "` set is_def=0,is_factory=0 WHERE style_id = $style_id";
        $is_succ = $this->db()->query($sql);
        if ($is_succ != FALSE) {
            $sql = "update `" . $this->table() . "` set is_def=1 where f_id=$id";
            $is_succ = $this->db()->query($sql);
            if ($is_succ != FALSE) {
                $sql = "update `" . $this->table() . "` set is_factory=1 where style_id = $style_id and factory_id = $factory_id";
                $is_succ = $this->db()->query($sql);
            }
        }
        return $is_succ;
    }

    /**
     * 更新默认工厂
     * @param type $style_id
     * @return type
     */
    function updateAllIIsFactory($style_id, $factory_id) {
        $sql = "update `" . $this->table() . "` set is_factory=1 where style_id = $style_id and factory_id = $factory_id";
        $is_succ = $this->db()->query($sql);
        return $is_succ;
    }

    /**
     * 更新默认镶口
     * @param type $is_def
     * @return type
     */
    function updateIsDef($f_id) {
        $sql = "update `" . $this->table() . "` set is_def=1 where f_id = $f_id";
        $is_succ = $this->db()->query($sql);
        return $is_succ;
    }

    /**
     * 检查是否有默认工厂
     * @param type $style_id
     * @return type
     */
    function getIsFactory($style_id) {
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE `style_id` = $style_id and `is_factory`=1";
        $data = $this->db()->getRow($sql);
        return $data;
    }

    /**
     * 检查是否有默认工厂
     * @param type $style_id
     * @return type
     */
    function getAllIsFactory($style_id) {
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE `style_id` = $style_id and `is_factory`=1";
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /**
     * 检查当前是否是默认工厂
     * @param type $style_id
     * @return type
     */
    function getIsFactoryByf_id($f_id) {
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE `f_id` = $f_id and `is_factory` = 1 AND `is_def` = 1";
        $data = $this->db()->getRow($sql);
        return $data;
    }
    
    /*
     * 取消工厂数据
     */
    function updateFactoryCancel($f_id){
        $sql = "UPDATE  `".$this->table()."` SET `is_cancel`=2 WHERE `f_id`=$f_id";
        return $this->db()->query($sql);
    }

    /*
     * 获取工厂信息
     */
    function getFactoryInfo($where) {
        $sql = "SELECT COUNT(1) FROM ".$this->table()." WHERE 1";
        if (isset($where['style_sn']) && $where['style_sn'] != '') {
            $sql .= " and style_sn = '".$where['style_sn']."'";
        }
        if (isset($where['style_id']) && $where['style_id'] > 0) {
            $sql .= " and style_id = '".$where['style_id']."'";
        }
        $res = $this->db()->getOne($sql);
        return $res;
    }

    /**
     *  getStyleInfoByFactorySn 工厂模号获取款式信息
     */
    public function getStyleInfoByFactorySn($factory_sn) {
        $sql = "SELECT `style_sn` FROM `".$this->table()."` WHERE `factory_sn` = '{$factory_sn}'";
        //echo $sql;die;
        $res = $this->db()->getAll($sql);
        return $res;
    }

    /**
     *  getStyleFactoryInfo，取得款式工厂相关信息；
     */
    public function getStyleFactoryInfo($condition) {

        if($condition['style_id'] != '' && $condition['style_sn'] != ''){

            $sql = "SELECT `is_factory`,`is_def`,`factory_id`,`factory_sn`,`xiangkou`,`factory_fee` FROM `".$this->table()."` WHERE `style_id` = {$condition['style_id']} AND `style_sn` = '{$condition['style_sn']}' AND `is_cancel` = 1 ORDER BY `is_factory` DESC,`is_def` DESC";
            return $this->db()->getAll($sql);
        }else{
            return false;
        }
    }

    public function getAllMoRenFactory($style_id,$factory_id)
    {
        # code...
        $sql = "select * from `".$this->table()."` where `style_id` = {$style_id} and `factory_id` = {$factory_id} and `is_factory` = 1";
        return $this->db()->getAll($sql);
    }

    /**
     *  getFactorySn 取工厂信息
     */
    public function getFactorySn($factory_id) {
        $sql = "SELECT * FROM `kela_supplier`.`app_processor_info` WHERE `id` = '{$factory_id}'";
        //echo $sql;die;
        $res = $this->db()->getRow($sql);
        return $res;
    }

    /**
     *  getStyleFactoryInfos，取得款式工厂相关信息；
     */
    public function getStyleFactoryInfos($condition) {

        if($condition['style_id'] != '' && $condition['style_sn'] != ''){

            $sql = "SELECT `is_factory`,`is_def`,`factory_id`,`factory_sn`,`xiangkou`,`factory_fee` FROM `".$this->table()."` WHERE `style_id` = {$condition['style_id']} AND `style_sn` = '{$condition['style_sn']}' AND `is_cancel` = 1 ORDER BY `is_factory` DESC,`is_def` DESC";
            return $this->db()->getAll($sql);
        }else{
            return false;
        }
    }

    public function getStyleIdByFactoryInfo($where){
        $sql = "SELECT `style_id`,`factory_sn` FROM ".$this->table()." WHERE 1";
        if (isset($where['factory_id']) && $where['factory_id'] != '') {
            $sql .= " and `factory_id` = '".$where['factory_id']."'";
        }
        if (isset($where['factory_sn']) && $where['factory_sn'] != '') {
            $sql .= " and `factory_sn` = '".$where['factory_sn']."'";
        }
        if (isset($where['xiangkou']) && $where['xiangkou'] != '') {
            $sql .= " and `xiangkou` = '".$where['xiangkou']."'";
        }
        if (isset($where['style_id']) && $where['style_id'] != '') {
            $sql .= " and `style_id` = '".$where['style_id']."'";
        }
        return $this->db()->getRow($sql);
    }

    public function insertFactorySn($data){

        $sql =" INSERT INTO `".$this->table()."` SET  `style_sn` = '".$data['style_sn']."' , `xiangkou` = '".$data['xiangkou']."' ,`factory_sn` = '".$data['factory_sn']."',factory_id='".$data['factory_id']."',style_id='".$data['style_id']."',factory_fee = 0";
        return $this->db()->query($sql);
    }
    
    public function checkFactoryInfoIsUnique($factory_id, $factory_sn, $excluding_style_sn) {
    	$result = array('error'=>'');
    	if (!empty($factory_sn)) {
    		/*
    		 * 申请工厂验证工厂+模号唯一时，全部是X的情况下不做判断，这个不管是一个还是多个；模号里面有2个或者2个以上XX不做判断，不管是否连续；模号位数大于1，且只有一个x，是需要判断的
    		*/
    		$mt = array();
    		if (preg_match_all('/X/i', $factory_sn, $mt)) {
    			$num = count($mt[0]);
    			if ($num >= 2 || $num == strlen($factory_sn)) return $result;
    		}
    		
    		// 判断唯一性: factory_sn + factory_id; 当前仅有新增，没有修改，因此不需要考虑编辑的情况
    		$list = $this->getValidStyleFactoryList(array(
    				'factory_sn' => $factory_sn,
    				'factory_id' => $factory_id
    		));

    		if (!empty($list)) {
    			$repeat_data = null;
    			foreach ($list as $l) {
    				// 排除
    				if ($l['style_sn'] == $excluding_style_sn) continue;
    				$repeat_data = $l;
    				break;
    			}
    			if (!empty($repeat_data)) {
    				$result['error'] = '工厂和模号信息与'.$repeat_data['style_sn'].'款号重复，不允许保存！';
    			}
    		}
    	}
    	return $result;
    }

    //判断同款同厂同模号同镶口是否重复
    public function checkFactoryIsCun($factory_id, $factory_sn, $style_sn, $xiangkou)
    {

        # code...
        $sql = "select * from `".$this->table()."` where `style_sn` = '{$style_sn}' and `factory_id` = {$factory_id} and `factory_sn` = '{$factory_sn}' and `xiangkou` = '{$xiangkou}'";
        return $this->db()->getRow($sql);
    }
    
    private function getValidStyleFactoryList($where) {
    	$sql = "select distinct style_sn from ".$this->table()." where `is_cancel` = 1 ";
    	if(isset($where['factory_id'])) {
    		$sql .= " AND `factory_id` = {$where['factory_id']} ";
    	}
    	if(isset($where['factory_sn'])) {
    		$sql .= " AND `factory_sn` = '{$where['factory_sn']}' ";
    	}
    	// ensure base_style_info is valid.
    	//$sql .= " AND exists(select 1 from `base_style_info` where style_sn = ".$this->table().".style_sn and check_status in (1,2,3))";
    	//$sql .= ' ORDER BY `is_factory` DESC';

    	return $this->db()->getAll($sql);
    }
}

?>