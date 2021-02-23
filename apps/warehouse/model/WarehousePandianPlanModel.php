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
		if(!empty($where['status']))
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

			//计算错误率
			if ($val['all_num'] != 0)
			{
				$val['error_rate'] = round(($all_loss_num +$all_overage_num)/$val['all_num']*100 , 2);		//总盘亏+总盘盈 / 应该盘点数
			}
		}
		return $data;
	}

	/**
	* 新建周盘点单
	* 1/ 生成抽检柜位，并且获取第一个柜位，并锁定该柜位
	* 2/获取所有锁柜位下的货品总量+成本总计
	* 3/写入主表信息warehouse_pandian_plan (预计盘点货品数 = 第2步获取的货品总量 / 货品总金额 = 第2步的成本总计)
	* 4/ 锁定货品（被锁定柜位下，库存状态的货=>变更仓储状态为盘点中）
	* 5/ 获取锁定柜位下仓储状态为 "盘点中" 的货品信息 ，写入盘点结果表(warehouse_pandian_report) 明细初始化 盘亏状态
	* @param $type 抽验仓,1 线上仓（柜位号不带F打头的）  2 线下仓（柜位号带F打头的）
	**/
	public function CreateNewPanWeek($type , $info){
		$result = array('success'=> 0 , 'error'=>'');
		$user = $_SESSION['userName'];
		$time = date('Y-m-d H:i:s');

		/**
		* 检测是否有未审核的周盘点
		*/
		$sql = "SELECT `id` FROM `warehouse_pandian_plan` WHERE `status` IN (1,2) AND `type` = {$type}";
		$exsis = $this->db()->getOne($sql);
		if($exsis){
			$tp = ($type = 1) ? '线上' : '线下';
			$result['error'] = "已经存在一个盘点中的{$tp}周盘点单 <span style='color:red'>{$exsis}</span>，不能重复制单";
			return $result;
		}

		# 1/ 生成抽检柜位，并且获取第一个柜位，并锁定该柜位
		$_list = $this->GetRandGuiwei($type);		//获取抽检柜位
		if(empty($_list)){
			$result['error'] = '没有满足条件的柜位';
			return $result;
		}
		$box_sn_list = $where_box_sn = '';		//柜位列表
		$box_id_sn_arr = array();
		foreach ($_list AS $v) {
			$box_sn_list .= $v['box_sn'].',';			//作为参数写入warehouse_pandian_plan表
			$box_id_sn_arr[$v['id']]= $v['box_sn'];
			$where_box_sn .= ",'{$v['box_sn']}'";		//作为计算周盘点预计总数 查询条件
		}
		$box_sn_list = rtrim($box_sn_list, ',');
		$where_box_sn = ltrim($where_box_sn, ',');

		#2/获取所有锁柜位下的货品总量+成本总计 （只计算库存状态的货品）
		$next_box = $_list[0]['box_sn'];
		$sql = "SELECT SUM(`wg`.`num`) AS `nums`, SUM(`wg`.`chengbenjia`) AS `chenbens` FROM `warehouse_goods` AS `wg` , `goods_warehouse` AS `gw` , `warehouse_box` AS `bx` WHERE `wg`.`goods_id` = `gw`.`good_id` AND `bx`.`id` = `gw`.`box_id` AND `bx`.`box_sn` IN  ({$where_box_sn}) AND `wg`.`is_on_sale` = 2 ";
		$all_data = $this->db()->getRow($sql);
		if(empty($all_data)){
			$result['error'] = '抽检柜位统计失败';
			return $result;
		}

		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			# 3/写入主表信息warehose_pandian_plan (预计盘点货品数 = 第2步获取的货品总量 / 货品总金额 = 第2步的成本总计)
			$sql = "INSERT INTO `warehouse_pandian_plan` (`type` , `guiwei_list`, `lock_guiwei`, `all_num` , `all_price` , `opt_admin` , `opt_date`, `info`) VALUES ( $type,  '{$box_sn_list}' , '{$next_box}' , {$all_data['nums']} , {$all_data['chenbens']} , '{$user}' , '{$time}' , '{$info}')";
			$pdo->query($sql);
			$id = $pdo->lastInsertId();
			/*
			//获取锁定柜位的ID
			$box_sn_id_arr = array_flip($box_id_sn_arr);
			$now_box_id = $box_sn_id_arr[$next_box];
            
			# 4/ 锁定货品（被锁定柜位下，库存状态的货=>变更仓储状态为盘点中）
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale` = 4 WHERE  `is_on_sale` = 2 AND `goods_id` IN (SELECT `good_id` FROM `goods_warehouse` WHERE `box_id` = {$now_box_id})";
			$pdo->query($sql);
    
			#5/ 获取锁定柜位下仓储状态为 "盘点中" 的货品信息 ，写入盘点结果表(warehouse_pandian_report) 明细初始化 盘亏状态
			$sql_val = "SELECT {$id} , '{$next_box}' , `a`.`goods_id`, `a`.`chengbenjia`, `a`.`num`, 2 FROM `warehouse_goods` AS `a`, `goods_warehouse` AS `b` WHERE `a`.`goods_id` = `b`.`good_id` AND `b`.`box_id` = {$now_box_id} AND `a`.`is_on_sale` = 4";
			$sql = "INSERT INTO `warehouse_pandian_report` (`plan_id` , `guiwei` , `goods_id` , `price` , `num` , `status`) ".$sql_val;
			$pdo->query($sql);
			*/

		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			$result['error'] = '程序事物异常，生成周盘点单失败';
			return $result;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		$result['success'] = 1;
		return $result;
	}


	/**
	* 接收货号，盘点该货品
	* 1/接收货品货号，当前正在盘点的柜位号，当前盘点单据的id
	* 2/检测提交过来的货号是否在盘点明细中（warehouse_pandian_report）
	* 3/如果：在明细中，是否盘点过：（1）盘过(real_num > 0)，提示盘点过
	*							（2）未盘过 (real_num = 0)  更新明细信息，对应明细real_num + 1
	* 		不在明细中，获取对应货号的信息（warehouse_goods）,写入明细
	*/
	public function PandianGoods( $goods_id , $box_sn , $id ){
		$result = array('success' => 0 , 'error'=> '');

		$res = $this->GetStatusBill($id);
		if($res == 3){
			$result['error'] = "盘点单：<span style='color:red'>{$id}</span> 已经审核，不能再进行盘点操作";
			return $result;
		}
		if($res == 4){
			$result['error'] = "盘点单：<span style='color:red'>{$id}</span> 已经被取消，不能再进行盘点操作";
			return $result;
		}

		if( $goods_id == '' || $box_sn == '' || $id == '' ){
			return $result['error'] = " 参数有误：[货号：{$goods_id} / 盘点柜位号：{$box_sn} / 盘单ID ：{$id}] ";
		}
		
		//在一个盘点单中,同一个货号不能重复盘点
		$sql = "SELECT `id`,`guiwei`,`time`,`user_name` FROM `warehouse_pandian_report` WHERE `goods_id` = '{$goods_id}' AND `plan_id` = {$id} AND real_num > 0";
		$goodsInfo = $this->db()->getRow($sql);
		if(!empty($goodsInfo)){
			$result['success'] = 1;
			$result['error'] = "货号:<font color=red>{$goods_id}</font>已被{$goodsInfo['user_name']}于{$goodsInfo['time']}在{$goodsInfo['guiwei']}中盘点过";	//已经盘点过的
			return $result;
		}
		
		
		# 2/检测提交过来的货号是否在盘点明细中（warehouse_pandian_report）
		$sql = "SELECT `id`,`real_num` FROM `warehouse_pandian_report` WHERE `goods_id` = '{$goods_id}' AND `plan_id` = {$id} AND `guiwei` = '{$box_sn}'";
		$goods_info = $this->db()->getRow($sql);
		$pdo = $this->db()->db();//pdo对象
		if(!empty($goods_info))
		{	//提交过来的货号在明细中
			if($goods_info['real_num'] > 0)
			{
				$result['success'] = 1;
				$result['error'] = "商品:<font color=red>{$goods_id}</font>已经处理";	//已经盘点过的
				return $result;
			}else{
				//获取提交过来的货品所在的柜位
				$sql = "SELECT `a`.`box_sn` FROM `warehouse_box` AS `a` , `goods_warehouse` AS `b` WHERE `b`.`good_id` = '{$goods_id}' AND `b`.`box_id` = `a`.`id`";
				$goods_box_sn = $this->db()->getOne($sql);
				
				$sql = "SELECT is_on_sale FROM `warehouse_goods` WHERE goods_id = '{$goods_id}'";
				$is_on_sale = $this->db()->getOne($sql);
				try{
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
					$pdo->beginTransaction();//开启事务
					$sql = "UPDATE `warehouse_pandian_report` SET `real_num` = `real_num` + 1, `status` = 4, `user_id` = ".$_SESSION['userId'].",`user_name` = '".$_SESSION['userName']."',`time`='".date('Y-m-d :H:i:s',time())."'   WHERE `plan_id` = {$id} AND `guiwei` = '{$box_sn}' AND `goods_id` = '{$goods_id}'";
					$pdo->query($sql);
				}
				catch(Exception $e){//捕获异常
					$pdo->rollback();//事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					return $result['error'] = '程序事物异常，变更盘点明细状态失败';
				}
				$pdo->commit();//如果没有异常，就提交事务
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				
				//获取用户盘点单数
				$num=$this->getPandianNum($id);
				return $result = array('success' => 1 , 'error'=> "货号: <span style='color:red;'>{$goods_id}</span> 盘点成功",'num'=>$num);
			}
		}
		else
		{	 //提交过来的货号不在明细中
			//$sql = "SELECT `a`.`goods_id` , `a`.`num` , `a`.`chengbenjia`, `a`.`is_on_sale`, `c`.`box_sn` FROM `warehouse_goods` AS `a`  LEFT JOIN `goods_warehouse` AS `b` ON `a`.`goods_id` = `b`.`good_id` LEFT JOIN `warehouse_box` AS `c` ON `b`.`box_id` = `c`.`id` WHERE `a`.`goods_id` = '{$goods_id}'";
			$sql = "SELECT `goods_id` , `num` , `chengbenjia`, `is_on_sale`, `box_sn`,`warehouse` FROM `warehouse_goods`  WHERE `goods_id` = '{$goods_id}'";
			$add_goods = $this->db()->getRow($sql);
              /*
			if(empty($add_goods)){
				return $result['error'] = "货号：<span style='color:red'>{$goods_id}</span> 在货品仓库中找不到该货号信息";
			}*/
           /*
			if($add_goods['is_on_sale'] != 2){
				return $result['error'] = "货号：<span style='color:red'>{$goods_id}</span> 不是库存状态，不能盘点";
			}
			*/

			try{
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
				$pdo->beginTransaction();//开启事务
				//写入明细信息 (记录原来的柜位)
				$sql = "INSERT INTO `warehouse_pandian_report` (`plan_id` , `guiwei` , `goods_id` , `price`, `real_num` , `num` , `status` , `old_guiwei`,`user_id`,`user_name`,`goods_status`,`time`,`panyingcang`)  VALUES ( {$id} , '{$box_sn}' , '{$goods_id}' , {$add_goods['chengbenjia']} , 1, 0 , 3 , '{$add_goods['box_sn']}',".$_SESSION['userId'].",'".$_SESSION['userName']."',{$add_goods['is_on_sale']},'".date('Y-m-d H:i:s',time())."','{$add_goods['warehouse']}')";
				$pdo->query($sql);

			}
			catch(Exception $e){//捕获异常
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				return $result['error'] = '程序盘盈事物异常，新增盘点明细状态失败';
			}
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			$DictModel=new DictView(new DictModel(1)); 
			//获取用户盘点单数
			$num=$this->getPandianNum($id);
			return $result = array('success' => 1 , 'error'=> "货号: <span style='color:red;'>{$goods_id}</span> 货品盘盈，货品状态：{$DictModel->getEnum('warehouse.goods_status',$add_goods['is_on_sale'])}",'num'=>$num);
		}
	}


	/**
	* 切换柜位
	* 1/ 根据warehouse_pandian_plan 的guiwei_list / lock_guiwei获取下一个柜位，并且跟新 lock_guiwei 字段
	* 2/更新明细 warehouse_pandian_report
	*	real_num 大于0 ，且货品所在的柜位 == 当前盘点柜位=> 正常/ real_num > 0 货品柜位 != 当前盘点柜位 => 盘盈
	*	real_num == 0 盘亏
	* 3/更新柜位信息 warehouse_box 的盘点时间/错误数( 盘盈+盘亏的数量合 = 盘点错误数)
	* 4/释放被锁的产品 变为库存状态
	* 5/获取下一柜位的货品 写入明细，并锁定货品 ，开始盘点下一个柜位，跟新主表信息
	* 6/如果当前柜位是最后一个柜位，获取明细中当前柜位的明细，更新jxc_pandian_plan的status 字段标志单据状态为盘点完成，更新正常+盘盈+盘亏的货品数
	* @param $id 单据ID
	* @param $box_sn 盘完的柜位
	*/
	public function qieBox($id, $box_sn){
		$result = array('success'=> 0 , 'error'=>'');
		$time = date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];
		$pdo = $this->db()->db();//pdo对象

		# 1/ 根据warehouse_pandian_plan 的guiwei_list / lock_guiwei获取下一个柜位
		$sql ="SELECT `guiwei_list`, `lock_guiwei` FROM `warehouse_pandian_plan` WHERE `id` = {$id}";
		$info = $this->db()->getRow($sql);
		$box_list = explode(',', $info['guiwei_list']);
		
		//获取最新的正在盘点的柜位
		$sql="SELECT lock_guiwei FROM warehouse_pandian_action WHERE plan_id={$id} ORDER BY time DESC LIMIT 1";
		$row=$this->db()->getRow($sql);
		
		$next_box = '';
		foreach($box_list AS $k=>$v) {
			if ($v == $row['lock_guiwei']){
				if(isset($box_list[$k+1])){
					$next_box = $box_list[$k+1];
					break;
				}else{
					$next_box = '';
					break;
				}
			}
		}
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			/**
			* 2/更新明细 warehouse_pandian_report
			* real_num 大于0 ，且货品所在的柜位 == 当前盘点柜位=> 正常/ real_num > 0 货品柜位 != 当前盘点柜位 => 盘盈
			* real_num == 0 盘亏
			*/
			$sql = "UPDATE `warehouse_pandian_report` SET `status` = IF(num-real_num=0,4,3) WHERE `plan_id` = {$id} AND `guiwei`= '{$box_sn}' AND `num` - `real_num`<=0";
			$pdo->query($sql);

			#3/更新柜位信息 warehouse_box 的盘点时间/错误数( 盘盈+盘亏的数量合 = 盘点错误数)
			$sql = "UPDATE `warehouse_box` SET `last_pandian_time` = '{$time}' , `last_pandian_error` = (SELECT count(*) FROM `warehouse_pandian_report` WHERE `plan_id` = {$id} AND `guiwei` = '{$box_sn}' AND `status` IN(3,4)) WHERE `box_sn` = '{$box_sn}'";
			$pdo->query($sql);

			#4/释放被锁的产品 变为库存状态
			$sql = "SELECT `goods_id` FROM `warehouse_pandian_report` WHERE `plan_id` = {$id} AND `guiwei` = '{$box_sn}'";
			$arr = $this->db()->getAll($sql);
			$goods_ids = '';
			foreach($arr as $row){
				$goods_ids .= ",'{$row['goods_id']}'";
			}
			$goods_ids = ltrim($goods_ids , ',');
			if(strlen($goods_ids) > 0){
				$sql = "UPDATE `warehouse_goods` SET `is_on_sale` = 2 WHERE `is_on_sale` = 4 AND `goods_id` IN ({$goods_ids})";
				$pdo->query($sql);
			}

			if(!$next_box){
				
				#6/如果当前柜位是最后一个柜位，获取明细中当前柜位的明细，更新jxc_pandian_plan的status 字段标志单据状态为盘点完成，更新正常+盘盈+盘亏的货品数

				//插入盘点记录,并解锁产品
				$sql_val = "SELECT {$id} , '{$box_sn}', `a`.`goods_id`, `a`.`num` , `a`.`chengbenjia`, 2 FROM `warehouse_goods` AS `a` , `goods_warehouse` AS `b` , `warehouse_box` AS `c` WHERE `a`.`goods_id` = `b`.`good_id` AND `c`.`box_sn` = '{$box_sn}' AND `b`.`box_id` = `c`.`id` AND `a`.`is_on_sale` = 4";
				$arr = $this->db()->getAll($sql_val);
				$goods_ids = '';
				foreach ($arr as $key => $val) {
					$goods_ids .= ",'{$val['goods_id']}'";
				}
				$goods_ids = ltrim($goods_ids , ',');

				if(strlen($goods_ids) > 0){
					//将最后一个柜位的货品解锁
					$sql = "UPDATE `warehouse_goods` SET `is_on_sale` = 2 WHERE `is_on_sale` = 4 AND `goods_id` IN ({$goods_ids})";
					$pdo->query($sql);
				}
				
				//删除此用户在正在盘点的记录
				$sql="UPDATE warehouse_pandian_action SET is_delete=1 WHERE plan_id={$id} AND user_id=".$_SESSION['userId'];
				$pdo->query($sql);
				$pdo->commit();//如果没有异常，就提交事务
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				
				//计算截至盘盈盘亏正常数
				$jisuan = $this->jisuan($id);
               //查询盘点单是否还有正在盘点的柜位，如果没有，就表示表示盘点完成
				$sql="SELECT * FROM warehouse_pandian_action WHERE plan_id={$id} AND is_delete=0";
				$result1=$this->db()->getAll($sql);
				//设置为盘点完成
				if(empty($result1)){
				 $sql = "UPDATE `warehouse_pandian_plan` SET `status` = 2 , `lock_guiwei` = '', `nomal` = {$jisuan['panzheng_num']}, `overage` = {$jisuan['panying_num']} , `loss` = {$jisuan['pankui_num']}   WHERE `id` = {$id}";
				 $this->db()->query($sql);
				 $end=1;
				}else{
				 $end=2;
				}				
				
			}else{
				//计算截至盘盈盘亏正常数
				$jisuan = $this->jisuan($id);
				#5/获取下一柜位的货品 写入明细，并锁定货品 ，开始盘点下一个柜位
				//跟新主表信息
				$sql = "UPDATE `warehouse_pandian_plan` SET `lock_guiwei` = '{$next_box}', `nomal` = {$jisuan['panzheng_num']}, `overage` = {$jisuan['panying_num']} , `loss` = {$jisuan['pankui_num']} WHERE `id` = {$id}";
				$pdo->query($sql);

				//更新锁定的格子
				//$sql = "UPDATE `warehouse_pandian_plan` SET `lock_guiwei` = '{$next_box}' WHERE `id` = {$id}";
				//$pdo->query($sql);
				
				$sql = "UPDATE `warehouse_pandian_action` SET `lock_guiwei` = '{$next_box}',`time`='".date("Y-m-d H:i:s",time())."' WHERE `plan_id` = {$id} AND user_id=".$_SESSION['userId'];
				$pdo->query($sql);

				//插入盘点记录,并锁定产品
				$sql_val = "SELECT {$id} , '{$next_box}', `a`.`goods_id`, `a`.`num` , `a`.`chengbenjia`, 2, a.is_on_sale FROM `warehouse_goods` AS `a` , `goods_warehouse` AS `b` , `warehouse_box` AS `c` WHERE `a`.`goods_id` = `b`.`good_id` AND `c`.`box_sn` = '{$next_box}' AND `b`.`box_id` = `c`.`id` AND `a`.`is_on_sale` = 2";
				$arr = $this->db()->getAll($sql_val);
				$goods_ids = '';
				foreach ($arr as $key => $val) {
					$goods_ids .= ",'{$val['goods_id']}'";
				}
				$goods_ids = ltrim($goods_ids , ',');
				if(strlen($goods_ids) > 0){
					//写入明细
					$sql = "INSERT INTO `warehouse_pandian_report` (`plan_id` , `guiwei` , `goods_id` , `num` , `price` , `status`,`goods_status`) ".$sql_val;
					$pdo->query($sql);
					//锁定货品
					$sql = "UPDATE `warehouse_goods` SET `is_on_sale` = 4 WHERE `is_on_sale` = 2 AND `goods_id` IN ({$goods_ids})";
					$pdo->query($sql);
				}
				
				$pdo->commit();//如果没有异常，就提交事务
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			}

		}
		catch(Exception $e){//捕获异常
			// echo $sql;die;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			$result['error'] = '程序盘盈事物异常，切换柜位失败';
			return $result;
		}
		
		if(isset($end) && $end == 1){
			return $result = array('success' => 1 , 'error'=> '最后一个柜位盘点已经完成');
		}else if(isset($end) && $end == 2){
			return $result = array('success' => 1 , 'error'=> '已经没有未分配的柜位了');
		}
		else{
			return $result = array('success' => 1 , 'error'=> '切换成功');
		}
	}

	//生成抽检柜位
	public function GetRandGuiwei($type){
		$boxModel = new WarehouseBoxModel(21);
		//抽选类型对应可以抽选的库
		//1线上库包含以下库位：主站库、线上低值库、彩宝库、淘宝黄金、淘宝素金、线上唯品会货品库、京东素金、京东黄金、黄金网络库、深圳珍珠库、线上钻饰库、银行库、B2C库、轻奢库；
		//2线下库包含以下库位：总公司后库 ；总公司店面配货库； 黄金店面库；婚博会备货库； 半成品库，大客户部库
		$pandian_warehouse_array = array(
			//'1' => '369,2,386,482,484,546,485,483,184,79,486,399,400,672',
			'1' => '251,546,698,699,702,1830',
			'2' => '96,308,443,488,673,3',
		);
		$date = date('Y-m-d H:i:s', strtotime('-30 days'));
		//线下仓，柜位号以F开头的柜位 货品数量不为0的柜位
		$ids = '';
		if($type == 2)
		{
			// $sql = "SELECT  bx.`id`, bx.`box_sn`,count(`gw`.`good_id`) AS `num` FROM `warehouse_box` AS `bx`, `goods_warehouse` AS `gw` WHERE `bx`.`id` = `gw`.`box_id` AND  `is_deleted` = 1 AND `box_sn`  LIKE  'F%' AND `box_sn` != '0-00-0-0' GROUP BY `bx`.`box_sn` ORDER BY `num` DESC LIMIT 0,20";
			$sql = "SELECT  bx.`id`, bx.`box_sn`,count(`wg`.`goods_id`) AS `num` FROM `warehouse_box` AS `bx`, `goods_warehouse` AS `gw` , `warehouse_goods` AS `wg` WHERE `bx`.`id` = `gw`.`box_id` AND `wg`.`goods_id` = `gw`.`good_id` AND  `bx`.`is_deleted` = 1 AND `bx`.`box_sn` != '0-00-0-0' AND `wg`.`is_on_sale` = 2  AND `bx`.`box_sn`  LIKE  'F%' AND `bx`.`box_sn` !='F-32-1-A'  AND `wg`.`warehouse_id` IN ({$pandian_warehouse_array[2]}) AND `bx`.`box_sn` IN (SELECT box_sn FROM box_goods_log WHERE create_time > '{$date}' AND type=1 GROUP BY box_sn) GROUP BY `bx`.`box_sn` ORDER BY `num` DESC LIMIT 0,7";
			$rows = $this->db()->getAll($sql);
			
			$box_all=array();
			if(!empty($rows)){
					$in='';
					foreach ($rows as $row){
						$box_all[]=$row;
						$in.="'".$row['box_sn']."',";
					}
					$in=rtrim($in,',');
					$where_in="AND box_sn NOT IN ({$in})";
				}else{
					$where_in='';
				}
			//获取3个有货品上架动作的随机柜位
			$sql = "SELECT  bx.`id`, bx.`box_sn`,count(`wg`.`goods_id`) AS `num` FROM `warehouse_box` AS `bx`, `goods_warehouse` AS `gw` , `warehouse_goods` AS `wg` WHERE `bx`.`id` = `gw`.`box_id` AND `wg`.`goods_id` = `gw`.`good_id` AND  `bx`.`is_deleted` = 1 AND `bx`.`box_sn` != '0-00-0-0' AND `wg`.`is_on_sale` = 2  AND `bx`.`box_sn`  LIKE  'F%' AND `bx`.`box_sn` !='F-32-1-A'  AND `wg`.`warehouse_id` IN ({$pandian_warehouse_array[2]}) AND `bx`.`box_sn` IN (SELECT box_sn FROM box_goods_log WHERE create_time > '{$date}' AND type=2 {$where_in} GROUP BY box_sn) GROUP BY `bx`.`box_sn` ORDER BY `num` DESC LIMIT 0,3";
			$rows = $this->db()->getAll($sql);
			foreach ($rows as $row){
				$box_all[]=$row;
			}

		}else if($type == 1)
		{
			/*
			//$sql = "SELECT  bx.`id`, bx.`box_sn`,count(`gw`.`good_id`) AS `num` FROM `warehouse_box` AS `bx`, `goods_warehouse` AS `gw` WHERE `bx`.`id` = `gw`.`box_id` AND  `is_deleted` = 1 AND `box_sn` NOT LIKE  'F%' AND `box_sn` != '0-00-0-0' GROUP BY `bx`.`box_sn` ORDER BY `num` DESC LIMIT 0,20";
			 $sql = "SELECT  bx.`id`, bx.`box_sn`,count(`wg`.`goods_id`) AS `num` FROM `warehouse_box` AS `bx`, `goods_warehouse` AS `gw` , `warehouse_goods` AS `wg` WHERE `bx`.`id` = `gw`.`box_id` AND `wg`.`goods_id` = `gw`.`good_id` AND  `bx`.`is_deleted` = 1 AND `bx`.`box_sn` != '0-00-0-0' AND `wg`.`is_on_sale` = 2 AND `bx`.`box_sn` NOT LIKE  'F%' AND `wg`.`warehouse_id` IN ({$pandian_warehouse_array[1]}) AND `bx`.`box_sn` IN (SELECT box_sn FROM box_goods_log WHERE create_time > '{$date}' AND type=1 GROUP BY box_sn) GROUP BY `bx`.`box_sn` ORDER BY `num` DESC LIMIT 0,7";
			$rows = $this->db()->getAll($sql);
			$box_all=array();
			if(!empty($rows)){
				$in='';
				foreach ($rows as $row){
					$box_all[]=$row;
					$in.="'".$row['box_sn']."',";
				}
				$in=rtrim($in,',');
				$where_in="AND box_sn NOT IN ({$in})";
			}else{
				$where_in='';
			}
			//获取3个有货品上架动作的随机柜位
			 $sql = "SELECT  bx.`id`, bx.`box_sn`,count(`wg`.`goods_id`) AS `num` FROM `warehouse_box` AS `bx`, `goods_warehouse` AS `gw` , `warehouse_goods` AS `wg` WHERE `bx`.`id` = `gw`.`box_id` AND `wg`.`goods_id` = `gw`.`good_id` AND  `bx`.`is_deleted` = 1 AND `bx`.`box_sn` != '0-00-0-0' AND `wg`.`is_on_sale` = 2 AND `bx`.`box_sn` NOT LIKE  'F%' AND `wg`.`warehouse_id` IN ({$pandian_warehouse_array[1]}) AND `bx`.`box_sn` IN (SELECT box_sn FROM box_goods_log WHERE create_time > '{$date}' AND type=2 {$where_in} GROUP BY box_sn) GROUP BY `bx`.`box_sn` ORDER BY `num` DESC LIMIT 0,3";
			 $rows = $this->db()->getAll($sql);
			foreach ($rows as $row){
				$box_all[]=$row;	
			}*/
			$sql="SELECT bx.`id`,bx.`box_sn`,count(wg.goods_id) AS `num` FROM `warehouse_box` AS `bx`,`goods_warehouse` AS `gw`,`warehouse_goods` AS `wg`,warehouse w,warehouse_rel r WHERE `bx`.`id` = `gw`.`box_id` AND `wg`.`goods_id` = `gw`.`good_id` AND `bx`.`is_deleted` = 1 AND `bx`.`box_sn` != '0-00-0-0' AND `wg`.`is_on_sale` = 2 AND `bx`.`box_sn` NOT LIKE 'F%' AND `wg`.`warehouse_id`=w.id and w.id=r.warehouse_id and r.company_id=58 and w.is_delete=1 
			     and bx.box_sn in ( 	SELECT	box_sn	FROM box_goods_log 	WHERE	create_time > '{$date}' AND type = 1 and box_sn<>'0-00-0-0' GROUP BY box_sn) GROUP BY `bx`.`box_sn`";
			$rows = $this->db()->getAll($sql);
			$box_all=array();
			$arrround=array();
			while(count($arrround)<5)
			{
				$arrround[]=rand(0,count($rows)-1);
				$arrround=array_unique($arrround);
			}
			foreach ($arrround as $key => $row) {
				$box_all[]=$rows[$row];
			}

			
		}

		shuffle($box_all);		//打乱柜位顺序
		$logic_length = $real_length = 0;
		$logic_num = 0;
		foreach ($box_all AS $v) {
			//抽检的格子不足30个产品,就多检一个
			if ($v['num'] < 30)
			$logic_length --;
			//抽检格子超过300产品,当作2个格子
			if ($v['num'] > 300)
			$logic_length = $logic_length+1;
			//抽检格子超过1000产品,当作2+3个格子
			if ($v['num'] > 1000)
			$logic_length = $logic_length+3;
			$real_length ++;
			$logic_length ++;
			if ($logic_length > 10)
			break;
			//抽检产品限定
			$logic_num += $v['num'];
			if ($logic_length > 1000)
			break;
		}
		return array_slice($box_all, 0, $real_length);
	}


	/**
	* 审核周盘点
	* 1/修改主表状态信息
	* 只有盘点完成的单据才能审核
	* @param $id 单据的ID
	*/
	public function checkPandian($id){
		$result = array('success' => 0, 'error'=> '');
		$user = $_SESSION['userName'];
		$time = date('Y-m-d H:i:s');
		$res = $this->GetStatusBill($id);
		if($res == 1){
			$result['error'] = "盘点单：<span style='color:red'>{$id}</span> 在盘点中，不能审核";
			return $result;
		}
		if($res == 3){
			$result['error'] = "盘点单：<span style='color:red'>{$id}</span> 已经审核，不能重复操作";
			return $result;
		}
		if($res == 4){
			$result['error'] = "盘点单：<span style='color:red'>{$id}</span> 已经被取消，不能审核";
			return $result;
		}
		$sql = "UPDATE `warehouse_pandian_plan` SET `status` = 3, `verify_admin` = '{$user}' , `verify_date` = '{$time}' WHERE `id` = {$id}";
		if($this->db()->query($sql)){
			$result = array('success' => 1, 'error'=> '审核成功');
		}else{
			$result = array('success' => 0, 'error'=> '审核失败');
		}
		return $result;
	}

	/**
	* 取消周盘点
	* 1/修改主表状态信息
	* 2/解除正在盘点的柜位的货品 库存状态
	* @param $id 单据的ID
	*/
	public function closePandian($id){
		$result = array('success' => 0, 'error'=> '');
		$user = $_SESSION['userName'];
		$time = date('Y-m-d H:i:s');
		$res = $this->GetStatusBill($id);

		if($res == 2){
			$result['error'] = "盘点单：<span style='color:red'>{$id}</span> 已经盘点完成，不能取消";
			return $result;
		}
		if($res == 3){
			$result['error'] = "盘点单：<span style='color:red'>{$id}</span> 已经审核，不能取消";
			return $result;
		}
		if($res == 4){
			$result['error'] = "盘点单：<span style='color:red'>{$id}</span> 已经被取消，不能重复操作";
			return $result;
		}

		//盘点中的单据，变更单据状态+解除正在盘点柜位里的货品的仓库状态
		if($res == 1){
			$pdo = $this->db()->db();//pdo对象
			try{
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
				$pdo->beginTransaction();//开启事务
				$sql = "UPDATE `warehouse_pandian_plan` SET `status` = 4, `opt_admin` = '{$user}' , `opt_date` = '{$time}' WHERE `id` = {$id} LIMIT 1";
				$pdo->query($sql);

				//获取正在盘点的柜位
				//$sql = "SELECT `lock_guiwei` FROM `warehouse_pandian_plan` WHERE `id` = {$id}";
				$sql="SELECT lock_guiwei FROM warehouse_pandian_action WHERE plan_id={$id}  AND is_delete=0";
				$lock_guiwei_arr = $this->db()->getAll($sql);
			   if(!empty($lock_guiwei_arr)){
			   	   $sql = "UPDATE `warehouse_pandian_action` SET `is_delete` = 1 WHERE `is_delete` = 0 AND plan_id={$id}";
			   	   $pdo->query($sql);
					$lock_guiwei='';
					foreach ($lock_guiwei_arr as $v){
						$lock_guiwei .= ',\''.$v['lock_guiwei'].'\'';
					}
					$lock_guiwei = ltrim($lock_guiwei , ',');
					$sql = "SELECT `a`.`good_id` FROM `goods_warehouse` AS `a` , `warehouse_box` AS `b` WHERE `a`.`box_id` = `b`.`id` AND `b`.`box_sn` in ({$lock_guiwei})";
					$goods_arr = $this->db()->getAll($sql);
					if(!empty($goods_arr)){
						  $goods_list = '';
						  foreach ($goods_arr as $k => $val) {
							$goods_list .= ',\''.$val['good_id'].'\'';
						  }
						  $goods_list = ltrim($goods_list , ',');
						
						  //解锁
						  $sql = "UPDATE `warehouse_goods` SET `is_on_sale` = 2 WHERE `is_on_sale` = 4 AND goods_id IN ($goods_list)";
						  $pdo->query($sql);
					}
			   }	
			}
			catch(Exception $e){//捕获异常
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				return $result['error'] = '程序事物异常，变更单据状态或解除货品锁定过程失败';
			}
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return $result = array('success' => 1 , 'error'=> '取消成功');
		}
	}

	/**
	* 获取单据的状态
	*/
	public function GetStatusBill($id){
		$sql = "SELECT `status` FROM `warehouse_pandian_plan` WHERE `id` = {$id}";
		return $this->db()->getOne($sql);
	}

	/**
	* 计算盘盈盘亏正常数
	*/
	public function jisuan($id){
		$arr = array('pankui_num'=>0 , 'panying_num'=>0 , 'panzheng_num'=> 0);
		$sql = "SELECT count(*) AS `num` FROM `warehouse_pandian_report` WHERE `plan_id` = {$id} AND `status` = 2"; 	//盘亏
		$pankui_num = $this->db()->getOne($sql);
		$sql = "SELECT count(*) AS `num` FROM `warehouse_pandian_report` WHERE `plan_id` = {$id} AND `status` = 3"; 	//盘盈
		$panying_num = $this->db()->getOne($sql);
		$sql = "SELECT count(*) AS `num` FROM `warehouse_pandian_report` WHERE `plan_id` = {$id} AND `status` = 4"; 	//正常
		$panzheng_num = $this->db()->getOne($sql);

		$arr['pankui_num'] = isset($pankui_num) && $pankui_num > 0 ? $pankui_num : 0;
		$arr['panying_num'] = isset($panying_num) && $panying_num > 0 ? $panying_num : 0;
		$arr['panzheng_num'] = isset($panzheng_num) && $panzheng_num > 0 ? $panzheng_num : 0;
		return $arr;
	}

	//根据id查询周盘点单，以及明细
	public function showData($id){
		$sql = "SELECT `a`.`opt_admin`, `a`.`opt_date` , `b`.`guiwei` , `b`.`goods_id` , `b`.`old_guiwei`, `b`.`status`, `b`.`panyingcang`, `b`.`goods_status` , `b`.`user_name` , `b`.`time` , `c`.`goods_id` , `c`.`goods_sn` , `c`.`goods_name` , `c`.`jinzhong`, `c`.`zhushilishu`, `c`.`zuanshidaxiao`, `c`.`fushilishu`, `c`.`fushizhong` , `c`.`mingyichengben`, `c`.`is_on_sale` FROM `warehouse_pandian_plan` AS `a` LEFT JOIN `warehouse_pandian_report` AS `b` ON `a`.`id` = `b`.`plan_id` INNER JOIN `warehouse_goods` AS `c` ON `b`.`goods_id` = `c`.`goods_id` WHERE `a`.`id` = {$id}";
		return $this->db()->getAll($sql);
	}
	//转换编码格式，导出csv数据
	public function detail_csv($name,$title,$content)
	{
		$ymd = date("Ymd_His", time()+8*60*60);
		header("Content-Disposition: attachment;filename=".iconv('utf-8','gbk',$name).$ymd.".csv");
		$fp = fopen('php://output', 'w');
		$title = eval('return '.iconv('utf-8','gbk',var_export($title,true).';')) ;
		fputcsv($fp, $title);
		foreach($content as $k=>$v)
		{
			fputcsv($fp, $v);
		}
		fclose($fp);
	}
	
	
	public function getPandianNum($plan_id){
		$sql="SELECT count(*) AS num FROM warehouse_pandian_report WHERE plan_id={$plan_id} AND user_id=".$_SESSION['userId'];
		 $num = $this->db()->getOne($sql);
		 return $num?$num:0;
	}
	//先查询是否存在本用户本盘点单的柜位，如果有，直接获取；否则插入一个盘点单盘点格子的下一个柜位且获取
	public function getPandianActionGuiwei($plan_id){
		//先查询是本用户本是否盘点过，如果是，直接获取；
		$return=array('error'=>0,'guiwei'=>'');
		$sql="SELECT lock_guiwei FROM warehouse_pandian_action WHERE plan_id={$plan_id} AND user_id=".$_SESSION['userId']." AND is_delete=0";
		$row=$this->db()->getRow($sql);
		if(!empty($row)){
			$return['guiwei']=$row['lock_guiwei'];
			return $return;
		}
		//查询本盘点单是否有已盘点的柜位，如果有，取下一个，否则取第一个
		$sql ="SELECT `guiwei_list` FROM `warehouse_pandian_plan` WHERE `id` = {$plan_id}";
		$info = $this->db()->getRow($sql);
		$box_list = explode(',', $info['guiwei_list']);
		
		$sql="SELECT lock_guiwei FROM warehouse_pandian_action WHERE plan_id={$plan_id} ORDER BY time DESC LIMIT 1";
		$row=$this->db()->getRow($sql);
		
		$next_box = '';
		//判断此盘点单有没有被其他用户盘点过，如果有，就获取最新的下一个
		if(!empty($row)){			
			foreach($box_list AS $k=>$v) {
				if ($v == $row['lock_guiwei']){
					if(isset($box_list[$k+1])){
						$next_box = $box_list[$k+1];
						break;
					}else{
						//最新没有下一个，那最新就是最后一个，直接返回
						$return['guiwei']='最后一个柜位：'.$row['lock_guiwei'];
						$return['error']=1;
						return $return;
						
					}
				}
			}
			
			
			 
			
		}else{
			$next_box=$box_list[0];

		}  
		
		if($next_box != ''){
			try{
					$pdo = $this->db()->db();//pdo对象
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
					$pdo->beginTransaction();//开启事务
				  //插入柜位到正在盘点柜位表
				    $sql="INSERT INTO warehouse_pandian_action (`plan_id`,`lock_guiwei`,`user_id`,`user_name`,`time`) VALUES({$plan_id},'{$next_box}',".$_SESSION['userId'].",'".$_SESSION['userName']."','".date('Y-m-d H:i:s',time())."')";
				    $pdo->query($sql);
				  
			       //插入盘点记录,并锁定产品
					$sql_val = "SELECT {$plan_id} , '{$next_box}', `a`.`goods_id`, `a`.`num` , `a`.`chengbenjia`, 2, a.is_on_sale FROM `warehouse_goods` AS `a` , `goods_warehouse` AS `b` , `warehouse_box` AS `c` WHERE `a`.`goods_id` = `b`.`good_id` AND `c`.`box_sn` = '{$next_box}' AND `b`.`box_id` = `c`.`id` AND `a`.`is_on_sale` = 2";
					$arr = $this->db()->getAll($sql_val);
					$goods_ids = '';
					foreach ($arr as $key => $val) {
						$goods_ids .= ",'{$val['goods_id']}'";
					}
					$goods_ids = ltrim($goods_ids , ',');
					if(strlen($goods_ids) > 0){
						//写入明细
						$sql = "INSERT INTO `warehouse_pandian_report` (`plan_id` , `guiwei` , `goods_id` , `num` , `price` , `status`,`goods_status`) ".$sql_val;
						$pdo->query($sql);
						//锁定货品
						$sql = "UPDATE `warehouse_goods` SET `is_on_sale` = 4 WHERE `is_on_sale` = 2 AND `goods_id` IN ({$goods_ids})";
						$pdo->query($sql);
					}
	
				}
				catch(Exception $e){//捕获异常
					// echo $sql;die;
					$pdo->rollback();//事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					die('程序异常'.$e);
					
				}
				$pdo->commit();//如果没有异常，就提交事务
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				
		  
		}
		$return['guiwei']=$next_box;
		return $return;		

		
		
	}
	
	public function getPandianActionList($plan_id){
		$sql="SELECT lock_guiwei,user_name FROM warehouse_pandian_action WHERE plan_id={$plan_id} AND is_delete=0";
		return $this->db()->getAll($sql);
		
	}
	
	public function getYuguNum($plan_id){
		$sql ="SELECT `guiwei_list`,`lock_guiwei` FROM `warehouse_pandian_plan` WHERE `id` = {$plan_id}";
		$info = $this->db()->getRow($sql);
		$box_list = explode(',', $info['guiwei_list']);
		//已盘柜位+正在盘点中的柜位
	    $sql="SELECT lock_guiwei FROM warehouse_pandian_action WHERE plan_id={$plan_id}  ORDER BY time DESC LIMIT 1";
		$row=$this->db()->getRow($sql);
		
		$box = '';
		if(!empty($row)){			
			foreach($box_list AS $k=>$v) {
				if ($v != $row['lock_guiwei']){
					$box.="'".$v."',";
				}else{
					$box.="'".$v."',";
					break;
				}
			}
			
			$box=rtrim($box,',');
			 
			
		}else{
			
			//以前的数据
			foreach($box_list AS $k=>$v) {
				if ($v != $info['lock_guiwei']){
					$box.="'".$v."',";
				}else{
					$box.="'".$v."',";
					break;
				}
			}
				
			$box=rtrim($box,',');
		}
		
		$sql_val = "SELECT count(a.goods_id) as num FROM `warehouse_goods` AS `a` , `goods_warehouse` AS `b` , `warehouse_box` AS `c` WHERE `a`.`goods_id` = `b`.`good_id` AND `c`.`box_sn` IN ({$box}) AND `b`.`box_id` = `c`.`id` AND `a`.`is_on_sale` IN (2,4)";
		$num = $this->db()->getOne($sql_val);
	    
		return $num;
	}

	
	public function getGoodsList($plan_id){
		$sql="SELECT wpr.goods_id,wpr.user_name,wpr.status,wpr.time,wpr.guiwei,wpr.old_guiwei,wpr.goods_status,wg.goods_sn,wg.goods_name,wg.jingdu,wg.zuanshidaxiao FROM warehouse_pandian_report AS wpr , warehouse_goods AS wg  WHERE wpr.goods_id=wg.goods_id and wpr.plan_id={$plan_id} AND status IN (2,3) " ;
		return $this->db()->getAll($sql);
	}
	
	public function getGoodsNumAndPrice($w){
		$where=' WHERE 1 ';
		if(isset($w['plan_id']) && $w['plan_id'] != ''){
			$where.=" and plan_id=".$w['plan_id'];
		}
		if(isset($w['status'])&&$w['status'] != ''){
			$where.=" and status in (".$w['status'].")";
		}
	   $sql="SELECT SUM(num+real_num) as num, SUM((num+real_num) * price) as price FROM warehouse_pandian_report $where";
		return $this->db()->getRow($sql);
		
	}
	
}?>