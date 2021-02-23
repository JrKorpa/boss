<?php

/**
 *  -------------------------------------------------
 *   @file		: AppCouponLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-11 16:52:55
 *   @update	:
 *  -------------------------------------------------
 */
class ConfigVisitAllowModel extends Model {

    function __construct ($id=NULL,$strConn="")
    {
        $this->_objName = 'app_xilie_config';
        $this->pk='id';
        $this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"user_name"=>"用户名",
"xilie"=>"系列");
        parent::__construct($id,$strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url AppCouponTypeController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        //不要用*,修改为具体字段
        $sql = "SELECT `id`,`user_name`,`xilie` FROM `".$this->table()."`";
        $str = '';
		if($where['user_name'] != "")
		{
			$str .= "`user_name` like \"".addslashes($where['user_name'])."%\" AND ";
		}
        /*if(!empty($where['time_start']))
        {
            $str.="`exchange_time` >= '".$where['time_start']." 00:00:00' AND ";
        }
        if(!empty($where['time_end']))
        {
            $str.="`exchange_time` <= '".$where['time_end']." 23:59:59' AND ";
        }
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}*/
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $sql .= " ORDER BY `id` DESC";
        //echo $sql;die;
        $data = $this->db()->getPageListNew($sql, array(), $page, $pageSize, $useCache);
        //echo '<pre>';
        //print_r($data);die;
        return $data;
    }

    /**
     *  getAllUserConfig，查询用户权限
     *
     *  @url AppCouponTypeController/search
     */
    public function getAllUserConfig($where)
    {
        # code...
        $sql = "SELECT `id` FROM `".$this->table()."`";
        $str = '';
        /*if($where['exchange_name'] != "")
        {
            $str .= "`exchange_name` like \"%".addslashes($where['exchange_name'])."%\" AND ";
        }
        if(!empty($where['time_start']))
        {
            $str.="`exchange_time` >= '".$where['time_start']." 00:00:00' AND ";
        }
        if(!empty($where['time_end']))
        {
            $str.="`exchange_time` <= '".$where['time_end']." 23:59:59' AND ";
        }*/
        if(!empty($where['user_name']))
        {
            $str .= "`user_name`='".$where['user_name']."' AND ";
        }
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        return $this->db()->getAll($sql);
    }

    /**
     *  getCheckListUser，验证用户菜单权限
     *
     *  @url getCheckListUser
     */
    public function getCheckListUser($user_id)
    {
        # code...
        $sql = "select `id` from `cuteframe`.`user_extend_menu` where `permission_id` = 1545 and `user_id` = {$user_id}";
        //echo $sql;die;
        return $this->db()->getOne($sql);
    }

}

?>