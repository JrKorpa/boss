<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehousePandianPlanModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-16 15:10:46
 *   @update	:
 *  -------------------------------------------------
 */
class WarehousePandianPlanModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_pandian_plan';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
		"type"=>"类型:1:ttp库2:总公司后库",
		"guiwei_list"=>"此次盘点的格子",
		"lock_guiwei"=>"正在盘点的柜位",
		"all_num"=>"预计会盘点的产品数",
		"all_price"=>"盘点产品总金额",
		"nomal"=>"正常产品数",
		"overage"=>"盘盈产品数",
		"loss"=>"盘亏产品数",
		"opt_admin"=>"盘点人",
		"opt_date"=>"盘点日期",
		"verify_admin"=>"审核人",
		"verify_date"=>"审核日期",
		"status"=>"周盘点单据状态 (数字字典warehouse.pandian_plan)",
		"info"=>"备注");
		parent::__construct($id,$strConn);
	}

	/**
	* 周盘点单列表
	*/
	public function pandianList($where,$page,$pageSize=10,$useCache=true){
		$sql = "SELECT * FROM `warehouse_pandian_plan`";
		$str = '';
		if($where['id'] != ""){
			$str .= "`id` like \"%".addslashes($where['id'])."%\" AND ";
		}
		if($where['type'] != ''){
			$str .= "`type`={$where['type']} AND ";
		}
		if($where['status'] != ''){
			$str .= "`status`={$where['status']} AND ";
		}
		if($where['opt_admin'] != ""){
			$str .= "`opt_admin` like \"%".addslashes($where['opt_admin'])."%\" AND ";
		}
		if($where['verify_admin'] != ""){
			$str .= "`verify_admin` like \"%".addslashes($where['verify_admin'])."%\" AND ";
		}
		if(!empty($where['create_time_start']))
		{
			$str .= "`opt_date`>='{$where['create_time_start']}' AND ";
		}
		if(!empty($where['create_time_end']))
		{
			$str .= "`opt_date`<='{$where['create_time_end']}' AND ";
		}
		if(!empty($where['start_time_start']))
		{
			$str .= "`verify_date`>='{$where['start_time_start']}' AND ";
		}
		if(!empty($where['start_time_end']))
		{
			$str .= "`verify_date`<='{$where['start_time_end']}' AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}

		$sql .= " ORDER BY `id` DESC";
		// echo $sql;die;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 *	pageList，查询货品明细
	 *
	 *	@url WarehousePandianPlanController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true , $down = false)
	{
		$sql = "SELECT `a`.`id`, `a`.`type`, `a`.`opt_date`, `a`.`verify_date`, `b`.`goods_id`, `b`.`price`,`b`.`status`,`c`.`goods_sn`,`c`.`goods_name` FROM `warehouse_pandian_plan` AS `a` LEFT JOIN `warehouse_pandian_report` AS `b` ON `a`.`id` = `b`.`plan_id` INNER JOIN `warehouse_goods` AS `c` ON `b`.`goods_id` = `c`.`goods_id`";
		$str = '';
		if($where['goods_id'] != "")
		{
			$str .= "`b`.`goods_id` like \"%".addslashes($where['goods_id'])."%\" AND ";
		}
		if(!empty($where['id']))
		{
			$str .= "`a`.`id` like \"%".addslashes($where['id'])."%\" AND ";
		}
		if(!empty($where['type']))
		{
			$str .= "`a`.`type`=".$where['type']." AND ";
		}
		if(!empty($where['status']))
		{
			$str .= "`a`.`status`=".$where['status']." AND ";
		}
		if(!empty($where['create_time_start']))
		{
			$str .= "`a`.`opt_date`>='{$where['create_time_start']}' AND ";
		}
		if(!empty($where['create_time_end']))
		{
			$str .= "`a`.`opt_date`<='{$where['create_time_end']}' AND ";
		}
		if(!empty($where['start_time_start']))
		{
			$str .= "`a`.`verify_date`>='{$where['start_time_start']}' AND ";
		}
		if(!empty($where['start_time_end']))
		{
			$str .= "`a`.`verify_date`<='{$where['start_time_end']}' AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `a`.`id` DESC";
		// echo $sql;
		if($down == true){
			$data = $this->db()->getAll($sql);
		}else{
			$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		}
		return $data;
	}

	/**
	 *	pageList，查询单据明细
	 */
	function pageList2($where,$page,$pageSize=10,$useCache=true , $down = false)
	{
		$sql = "SELECT `id`, `type`, `all_num`, `all_price` , `opt_admin` , `opt_date`, `verify_admin`, `verify_date`, `status` FROM `warehouse_pandian_plan`";
		$str = '';
		if(!empty($where['id']))
		{
			$str .= "`id` like \"%".addslashes($where['id'])."%\" AND ";
		}
		if(!empty($where['type']))
		{
			$str .= "`type`=".$where['type']." AND ";
		}
		if(isset($where['status_in']) && !empty($where['status_in']))
		{
			$str .= "`status` in(".$where['status_in'].") AND ";
		}
		elseif(!empty($where['status']))
		{
			$str .= "`status`=".$where['status']." AND ";
		}
		if(!empty($where['create_time_start']))
		{
			$str .= "`opt_date`>='{$where['create_time_start']}' AND ";
		}
		if(!empty($where['create_time_end']))
		{
			$str .= "`opt_date`<='{$where['create_time_end']}' AND ";
		}
		if(!empty($where['start_time_start']))
		{
			$str .= "`verify_date`>='{$where['start_time_start']}' AND ";
		}
		if(!empty($where['start_time_end']))
		{
			$str .= "`verify_date`<='{$where['start_time_end']}' AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);

		if($down == true){
			$data['data'] = $this->db()->getAll($sql);
		}
		foreach($data['data'] as $key => &$val){
			$val['real_num'] = 0;  //实盘数量  总共实盘的数量（正常num+  盘盈num+ 盘亏num）
			$val['real_price'] = 0;  //实盘金额 = 总共实盘的金额（正常+  盘盈+ 盘亏）

			//正常统计
			$sql = "SELECT count(*) AS num ,sum(`price`) AS `price` FROM `warehouse_pandian_report` WHERE `plan_id` = {$val['id']} AND `status` = 4";
			$row = $this->db()->getRow($sql);
			$val['real_num'] += $row['num'];
			$val['real_price'] += $row['price'];
			$val['nomal_num'] = $row['num'];
			$val['nomal_price'] = $row['price'];

			//盘盈统计
			$sql = "SELECT count(*) AS num ,sum(`price`) AS `price` FROM `warehouse_pandian_report` WHERE `plan_id` = {$val['id']} AND `status` = 3";
			$row = $this->db()->getRow($sql);
			$val['real_num'] += $row['num'];
			$val['real_price'] += $row['price'];

			$all_overage_num = $val['overage_num'] = $row['num'];
			$val['overage_price'] = $row['price'];

			//盘亏统计
			$sql = "SELECT count(*) AS num ,sum(`price`) AS `price` FROM `warehouse_pandian_report` WHERE `plan_id` = {$val['id']} AND `status` = 2";
			$row = $this->db()->getRow($sql);
			$val['real_num'] += $row['num'];
			$val['real_price'] += $row['price'];

			$all_loss_num = $val['loss_num'] = $row['num'];
			$val['loss_price'] = $row['price'];
			$val['overage_rate']=$val['loss_rate']=$val['accurate_rate']=0;
			//计算错误率
			if ($val['all_num'] != 0)
			{
				$val['error_rate'] = round(($all_loss_num +$all_overage_num)/$val['all_num']*100 , 2);		//总盘亏+总盘盈 / 应该盘点数
			}
			//计算盘点准确率
			if ($val['nomal_num'] != 0)
			{
				$val['accurate_rate'] = round(($val['nomal_num'])/$val['real_num']*100 , 2);		//盘点准确率=正常数量/实盘数量
			}
			//盘亏占比
			if ($val['loss_num'] != 0)
			{
				$val['loss_rate'] = round(($val['loss_num'])/$val['real_num']*100 , 2);		//盘亏占比=盘亏数量/实盘数量；
			}
			//盘盈占比
			if ($val['overage_num'] != 0)
			{
				//$val['overage_rate'] = round(($val['overage_num'])/$val['real_num']*100 , 2);		//盘盈占比=盘盈数量/实盘数量
                $val['overage_rate'] = round(100 - $val['loss_rate'] - $val['accurate_rate'],4);
			}
		}
		return $data;
	}
	/**
	 * 盘点的具体商品情况
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 * @param string $down
	 */
	public function get_detail_goods_list($where,$page,$pageSize=10,$useCache=true , $down = false){
		$sql="select r.plan_id,r.status,r.price,r.guiwei,r.goods_id,g.goods_sn,g.goods_name from warehouse_pandian_report r JOIN `warehouse_goods` g ON g.goods_id=r.goods_id WHERE 1 ";
		if($where['plan_id']){
			$sql.=" and r.plan_id='{$where['plan_id']}'";
		}
		if($where['status']){
			$sql.=" and r.status='{$where['status']}'";
		}
		if($where['goods_id']){
			$sql.=" and g.goods_id='{$where['goods_id']}'";
		}
		$sql .= " ORDER BY r.`status` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
		
	}

}?>