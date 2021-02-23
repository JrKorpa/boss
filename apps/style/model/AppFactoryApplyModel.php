<?php
/**
 *  -------------------------------------------------
 *   @file		: AppFactoryApplyModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 10:37:58
 *   @update	:
 *  -------------------------------------------------
 */
class AppFactoryApplyModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_factory_apply';
		$this->pk='apply_id';
		$this->_prefix='';
        $this->_dataObject = array("apply_id"=>" ",
"style_id"=>"款式ID",
"style_sn"=>"款号",
"f_id"=>"更新ID",
"factory_id"=>"工厂id",
"factory_name"=>"工厂名称",
"factory_sn"=>"工厂模号",
"xiangkou"=>"镶口",
"factory_fee"=>"工厂费用",
"type"=>"操作:1 添加;2 删除",
"status"=>"状态  1、待审核；2、审核通过；3、审核未通过；4、取消",
"apply_num"=>"申请次数",
"make_name"=>"创建人",
"crete_time"=>"创建时间",
"check_name"=>"审核人",
"check_time"=>"审核时间",
"info"=>"备注");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppFactoryApplyController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{		
		$sql = "SELECT a.*, i.style_name, p.product_type_name, c.cat_type_name FROM `".$this->table()."` a 
	 left join `base_style_info` i on a.style_sn = i.style_sn 
	 left join `app_product_type` p on p.product_type_id = i.product_type and p.product_type_status = 1 
	 left join `app_cat_type` c on c.cat_type_id = i.style_type and c.cat_type_status = 1
	 WHERE 1 = 1 ";
        
        if($where['style_sn']){
            $sql .= " and a.`style_sn` like '%{$where['style_sn']}%'";
        }
        
        if (!empty($where['factory_id'])) {
        	$sql .= ' and a.`factory_id` ='.$where['factory_id'];
        }
        
        if (!empty($where['applier'])) {
        	$sql .= " and a.`check_name` like '%{$where['applier']}%'";
        }
        
        if (!empty($where['apply_status'])) {
        	$sql .= ' and a.`status` = '.$where['apply_status'];
        }

        if (!empty($where['apply_type'])) {
        	if (count($where['apply_type']) == 1) {
        		$sql .= ' and a.`type` = '.$where['apply_type'][0];
        	} else {
        		$sql .= ' and a.`type` in ('.implode(',', $where['apply_type']).')';
        	}
        }
        
        if (!empty($where['apply_start_time'])) {
        	$sql.=" and a.`crete_time` >= '".$where['apply_start_time']." 00:00:00'";
        }
        
	 	if (!empty($where['apply_end_time'])) {
        	$sql.=" AND a.`crete_time` <= '".$where['apply_end_time']." 23:59:59'";
        }
        
        if (!empty($where['check_name'])) {
        	$sql .= " and a.`check_name` like '%{$where['check_name']}%'";
        }

        if (!empty($where['check_start_time'])) {
        	$sql.=" and a.`check_time` >= '".$where['check_start_time']." 00:00:00'";
        }
        
        if (!empty($where['check_end_time'])) {
        	$sql.=" AND a.`check_time` <= '".$where['check_end_time']." 23:59:59'";
        }
        
        if($_SESSION['userType']<>1){          
            $groupUser = new GroupUserModel(1);
            $is_wukong_user = $groupUser->checkGroupUser(7,$_SESSION['userId']);
            $is_style_edit_user = $groupUser->checkGroupUser(6,$_SESSION['userId']);
            if($is_wukong_user)
                $sql .= " and i.is_wukong='1' ";
            elseif($is_style_edit_user)
                $sql .= " and (i.is_wukong<>'1' or i.is_wukong is null ) ";
            else
                $sql .=" and i.is_wukong='-1'";
        } 

		$sql .= " ORDER BY a.apply_id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	} 
        
    function getStatusVal($status) {
            $data = array(1=>'待审核',2=>'审核通过',3=>'审核驳回',4=>'作废审核驳回','5'=>'作废审核通过');
            return $data[$status];
    }
    
    
    function getResByFid($f_id) {
            $sql = "SELECT * FROM `".$this->table()."` WHERE 1 and f_id=$f_id order by apply_id desc";
            return $this->db()->getRow($sql);
    }
    
    function getResByStatus($where) {
            $sql = "SELECT * FROM `".$this->table()."` WHERE 1";
            if(isset($where['style_id']) && !empty($where['style_id']))
            {
                $sql .= " AND `style_id` = '{$where['style_id']}'";
            }
            if(isset($where['type']) && !empty($where['type']))
            {
                $sql .= " AND `type` = '{$where['type']}'";
            }
            if(isset($where['status']) && !empty($where['status']))
            {
                $sql .= " AND `status` = '{$where['status']}'";
            }
            return $this->db()->getRow($sql);
    }
    
    function isAllowAdd($style_id,$factory_id,$factory_sn,$xiangkou) {
            $sql = "SELECT * FROM `".$this->table()."` WHERE 1 and style_id = $style_id and factory_id = $factory_id and factory_sn = '{$factory_sn}' and xiangkou = '{$xiangkou}' order by apply_id desc limit 1";
            //echo $sql;die;
            return $this->db()->getRow($sql);
    }

    public function checkUpdatFactory($style_id,$factory_id,$factory_sn)
    {
        # code...
        $sql = "select * from `".$this->table()."` where `style_id` = $style_id and `type` = 4 and `factory_id` = $factory_id and `factory_sn` = '{$factory_sn}' and `status` = 1";
        return $this->db()->getRow($sql);
    }
        
    /**
     *  getStyleFactoryInfo，取得款式工厂相关信息；
     */
    public function getStyleFactoryInfo($condition) {

        if($condition['style_id'] != '' && $condition['style_sn'] != ''){

            $sql = "SELECT `factory_name`,`factory_sn`,`xiangkou`,`factory_fee` FROM `".$this->table()."` WHERE `style_id` = {$condition['style_id']} AND `style_sn` = '{$condition['style_sn']}' AND `status` = 2";
            return $this->db()->getAll($sql);
        }else{
            return false;
        }
    }
    
    public function auditFactoryApply($ids, $status, $check_name) {
    	if (count($ids) == 1) {
    		$sql = "update `".$this->table()."` set `status`={$status},`check_name`='{$check_name}',`check_time`=NOW() where apply_id =".$ids[0];
    	} else {
    		$sql = "update `".$this->table()."` set `status`={$status},`check_name`='{$check_name}',`check_time`=NOW() where apply_id in (".implode(',', $ids).")";
    	}
    	
    	return $this->db()->query($sql);
    }

    /**
     *查询款式工厂模号申请信息
     */
    public function getStyleFactoryList($where)
    {
        $sql_where = 'WHERE 1';
        if(isset($where['style_id']) && !empty($where['style_id']))
        {
            $sql_where .= " AND `style_id` = {$where['style_id']}";
        }

        if(isset($where['factory_id']) && !empty($where['factory_id']))
        {
            $sql_where .= " AND `factory_id` = {$where['factory_id']}";
        }

        if(isset($where['factory_sn']) && !empty($where['factory_sn']))
        {
            $sql_where .= " AND `factory_sn` = '{$where['factory_sn']}'";
        }

        if(isset($where['xiangkou']) && !empty($where['xiangkou']))
        {
            $sql_where .= " AND `xiangkou` = '{$where['xiangkou']}'";
        }
        
        if (isset($where['_string']) && !empty($where['_string'])) {
        	$sql_where .= " AND ({$where['_string']})";
        }

        $sql = "SELECT * FROM `".$this->table()."` {$sql_where} ORDER BY `style_id` DESC";
        return $this->db()->getAll($sql);
    }

    /**
     *修改工费 批量导入工费更新；
     */
    public function updateStyleFactoryFee($where)
    {
        if($where['apply_id'] == ''){
            return false;
        }

        $sql = "UPDATE `".$this->table()."` SET `factory_fee` = {$where['factory_fee']} WHERE `apply_id` = {$where['apply_id']}";
        # code...
        return $this->db()->query($sql);
    }
    
    /**
     * 获取申请次数
     * @param unknown $where
     * @return Ambigous <multitype:, boolean>
     */
    public function getApplyNum($where) {
    	$sql = "select max(apply_num) as apply_num from `".$this->table()."` where 1 ";
    	if (isset($where['style_sn'])) {
    		$sql .= " AND `style_sn`='{$where['style_sn']}'";
    	}
    	if (isset($where['type'])) {
    		$sql .= " AND `type`={$where['type']}";
    	}

    	return $this->db()->getOne($sql);
    }
}

?>