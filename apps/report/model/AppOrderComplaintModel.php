<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderComplaintModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2016-02-27 14:29:07
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderComplaintModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_order_complaint';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID",
"order_id"=>"订单ID",
"cl_feedback_id"=>"客诉选项",
"cl_other"=>"客诉备注",
"cl_user"=>"添加人",
"cl_time"=>"添加时间",
"cl_url"=>"图片地址");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppOrderComplaintController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT `a`.`cl_other`,`a`.`id`,`a`.`cl_user`,`a`.`cl_time`,`a`.`cl_url`,`b`.`order_sn`,`c`.`ks_option` FROM `app_order`.`".$this->table()."` `a` inner join `app_order`.`base_order_info` `b` on `a`.`order_id` = `b`.`id` left join `app_order`.`app_order_feedback` `c` on `a`.`cl_feedback_id` = `c`.`id`";
		$str = '';
        if(!empty($where['order_sn']))
        {
            $str .= "`b`.`order_sn`='".$where['order_sn']."' AND ";
        }
        if(!empty($where['cl_feedback_id']))
        {
            $str .= "`a`.`cl_feedback_id`='".$where['cl_feedback_id']."' AND ";
        }
		if($where['cl_user'] != "")
		{
			$str .= "`a`.`cl_user` like \"".addslashes($where['cl_user'])."%\" AND ";
		}
        if($where['cl_other'] != "")
        {
            $str .= "`a`.`cl_other` like \"".addslashes($where['cl_other'])."%\" AND ";
        }
		if(!empty($where['cl_time_start']))
		{
			$str .= "`a`.`cl_time`>='".$where['cl_time_start']." 00:00:00' AND ";
		}
        if(!empty($where['cl_time_end']))
        {
            $str .= "`a`.`cl_time`<='".$where['cl_time_end']." 23:59:59' AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `a`.`id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

    /**
     *取得客诉选项
     */
    public function getFeedbackInfo()
    {

        $sql="SELECT `id`,`ks_option` FROM `app_order`.`app_order_feedback`";

        return $this->db()->getAll($sql);
    }

    /**
     *  pageList_down 导出
     *
     *  @url AppOrderComplaintController/search
     */
    function pageList_down ($where,$page,$pageSize=10,$useCache=true)
    {
        //不要用*,修改为具体字段
        $sql = "SELECT `b`.`id`,`b`.`order_sn`,`b`.`buchan_status`,`b`.`consignee`,`d`.`channel_name`,`e`.`source_name`,`b`.`genzong`,`b`.`create_user`,`a`.`cl_other`,`a`.`cl_user`,`a`.`cl_time`,`a`.`cl_url`,`c`.`ks_option`,if(`e`.`source_class` = 1, '线上', '线下') 'source_class' FROM `app_order`.`".$this->table()."` `a` inner join `app_order`.`base_order_info` `b` on `a`.`order_id` = `b`.`id` inner join `cuteframe`.`sales_channels` `d` on `b`.`department_id` = `d`.`id` inner join `cuteframe`.`customer_sources` `e` on `b`.`customer_source_id` = `e`.`id` left join `app_order`.`app_order_feedback` `c` on `a`.`cl_feedback_id` = `c`.`id`";
        $str = '';
        if(!empty($where['order_sn']))
        {
            $str .= "`b`.`order_sn`='".$where['order_sn']."' AND ";
        }
        if(!empty($where['cl_feedback_id']))
        {
            $str .= "`a`.`cl_feedback_id`='".$where['cl_feedback_id']."' AND ";
        }
        if($where['cl_user'] != "")
        {
            $str .= "`a`.`cl_user` like \"".addslashes($where['cl_user'])."%\" AND ";
        }
        if($where['cl_other'] != "")
        {
            $str .= "`a`.`cl_other` like \"".addslashes($where['cl_other'])."%\" AND ";
        }
        if(!empty($where['cl_time_start']))
        {
            $str .= "`a`.`cl_time`>='".$where['cl_time_start']." 00:00:00' AND ";
        }
        if(!empty($where['cl_time_end']))
        {
            $str .= "`a`.`cl_time`<='".$where['cl_time_end']." 23:59:59' AND ";
        }
        if($str)
        {
            $str = rtrim($str,"AND ");//这个空格很重要
            $sql .=" WHERE ".$str;
        }
        $sql .= " ORDER BY `a`.`id` DESC";
        $data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
        return $data;
    }
}

?>