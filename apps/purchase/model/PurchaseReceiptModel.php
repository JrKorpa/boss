<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseReceiptModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-15 11:24:34
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseReceiptModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'purchase_receipt';
        $this->_dataObject = array("id"=>"id",
			"status"=>"见数据字典：采购收货单状态",
			"prc_id"=>"加工商id	",
			"prc_name"=>"加工商名称",
			"ship_num"=>"出货单号",
			"remark"=>"备注",
			"num"=>"数量",
			"all_amount"=>"总金额",
			"purchase_sn"=>"对应采购单id",
			"user_id"=>"操作人id",
			"user_name"=>"操作人",
			"create_time"=>"操作时间"
		);
		parent::__construct($id,$strConn);
	}
	function getSql($where)
	{
		$sql = "SELECT main.`id`, main.`status`, main.`prc_id`, main.`prc_name`, main.`ship_num`, main.`remark`, main.`num`, main.`all_amount`, main.`user_id`, main.`user_name`, main.`create_time`, main.`chengbenjia`,main.`edit_user_name`,main.`edit_time` FROM `".$this->table()."` as main";
		//editby zhangruiying
		$sql .=" where 1 ";
		if (!empty($where['ship_num']))
		{
				$sql .= " AND main.ship_num like \"%".addslashes($where['ship_num'])."%\"";
		}
		if (!empty($where['id']))
		{
				$sql .= " AND main.id = ".$where['id'];
		}
		if (!empty($where['prc_id']))
		{
				$sql .= " AND main.prc_id = ".$where['prc_id'];
		}
		if(isset($where['prc_ids']) and $where['is_all']!=1 AND $_SESSION['userType']==3)
		{
			if(empty($where['prc_ids']))
			{
				$sql.=" and main.prc_id='n'";
			}
			else
			{
				$where['prc_ids']=implode(',',$where['prc_ids']);
				if(!empty($where['prc_ids'])){
				   $sql.=" and main.prc_id in({$where['prc_ids']})";
				}
			}

		}
		if (!empty($where['status']))
		{
				$sql .= " AND main.status = ".$where['status'];
		}
		if(isset($where['start_time']) and $where['start_time'] != "")
		{
			$sql .= " AND main.create_time >= '{$where['start_time']} 00:00:00'";
		}
 		if(isset($where['end_time']) and $where['end_time'] != "")
		{
			$sql .= " AND main.create_time <= '{$where['end_time']} 23:59:59'";
		}
		if (!empty($where['user_name']))
		{
				$sql .= " AND main.user_name like \"%".addslashes($where['user_name'])."%\"";
		}

		if(isset($where['hidden']) && $where['hidden'] != ''){
		    $sql .= " AND main.hidden = ".$where['hidden'];
        }

		$sql .= " ORDER BY main.id DESC";

		return $sql;
	}


	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql=$this->getSql($where);
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	function getList($where)
	{
		$sql=$this->getSql($where);
		$row=$this->db()->getAll($sql);
		return $row;
	}

	function getCount($where)
	{
		$sql = "SELECT count(1) FROM `".$this->table()."` where status != 3 ";
		if (isset($where['ship_num']) && $where['ship_num'] != "")
		{
				$sql .= " AND ship_num = '".$where['ship_num']."'";
		}
		return $this->db()->getOne($sql);
	}
	/***
	fun:add_caigou_info
	添加采购收货单
	****/
	public function add_caigou_info($info,$data)
	{
		$pdo = $this->db()->db();//pdo对象
		$res = array('success'=>false,'id'=>0);
		try
		{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//1、添加收货单信息
			$sql = "INSERT INTO `purchase_receipt` (`prc_id`,`prc_name`,`ship_num`,`chengbenjia`,`remark`,`num`,`all_amount`,`user_id`,`user_name`,`create_time`) VALUES ('{$info['prc_id']}','{$info['prc_name']}','{$info['ship_num']}',{$info['chengbenjia']},'{$info['remark']}','{$info['num']}','{$info['all_amount']}','{$info['user_id']}','{$info['user_name']}','{$info['create_time']}') ";
			$pdo->query($sql);
			$id = $pdo->lastInsertId();
			//var_dump($data);exit;

			//2、添加收货单明细信息
			foreach ($data as $value)
			{
				//$sql = "insert into `purchase_receipt_detail`(`purchase_receipt_id`,`xuhao`,`customer_name`,`purchase_sn`,`bc_sn`,`style_sn`,`factory_sn`,`ring_mouth`,`is_cp_kt`,`cat_type`,`hand_inch`,`material`,`gross_weight`,`net_gold_weight`,`gold_loss`,`gold_price`,`main_stone`,`main_stone_weight`,`main_stone_num`,`work_fee`,`extra_stone_fee`,`other_fee`,`fittings_cost_fee`,`tax_fee`,`customer_info_stone`,`chengbenjia`,`zhushiyanse`,`zhushijingdu`,`zhushidanjia`,`fushi`,`fushilishu`,`fushizhong`,`fushidanjia`,`zhengshuhao`,`shi2`,`shi2lishu`,`shi2zhong`,`shi2danjia`,`shi3`,`shi3lishu`,`shi3zhong`,`shi3danjia`,`edit_user_id`,`edit_user_name`,`edit_time`) values ({$id},'{$value['xuhao']}','{$value['customer_name']}','{$value['purchase_sn']}','{$value['bc_sn']}','{$value['style_sn']}','{$value['factory_sn']}','{$value['ring_mouth']}','{$value['is_cp_kt']}','{$value['cat_type']}','{$value['hand_inch']}','{$value['material']}','{$value['gross_weight']}','{$value['net_gold_weight']}','{$value['gold_loss']}','{$value['gold_price']}','{$value['main_stone']}','{$value['main_stone_weight']}','{$value['main_stone_num']}','{$value['work_fee']}','{$value['extra_stone_fee']}','{$value['other_fee']}','{$value['fittings_cost_fee']}','{$value['tax_fee']}','{$value['customer_info_stone']}','{$value['chengbenjia']}','{$value['zhushiyanse']}','{$value['zhushijingdu']}','{$value['zhushidanjia']}','{$value['fushi']}','{$value['fushilishu']}','{$value['fushizhong']}','{$value['fushidanjia']}','{$value['zhengshuhao']}','{$value['shi2']}','{$value['shi2lishu']}','{$value['shi2zhong']}','{$value['shi2danjia']}','{$value['shi3']}','{$value['shi3lishu']}','{$value['shi3zhong']}','{$value['shi3danjia']}','{$_SESSION['userId']}','{$_SESSION['userName']}',CURRENT_TIME)";
                            $sql = "insert into `purchase_receipt_detail`(`purchase_receipt_id`,`xuhao`,`customer_name`,`purchase_sn`,`bc_sn`,`style_sn`,`factory_sn`,`ring_mouth`,`is_cp_kt`,`cat_type`,`hand_inch`,`material`,`gross_weight`,`net_gold_weight`,`gold_loss`,`gold_price`,`main_stone`,`main_stone_weight`,`main_stone_num`,`work_fee`,`extra_stone_fee`,`other_fee`,`fittings_cost_fee`,`tax_fee`,`customer_info_stone`,`chengbenjia`,`zhushiyanse`,`zhushijingdu`,`zhushidanjia`,`fushi`,`fushilishu`,`fushizhong`,`fushidanjia`,`zhengshuhao`,`shi2`,`shi2lishu`,`shi2zhong`,`shi2danjia`,`shi3`,`shi3lishu`,`shi3zhong`,`shi3danjia`) values ({$id},'{$value['xuhao']}','{$value['customer_name']}','{$value['purchase_sn']}','{$value['bc_sn']}','{$value['style_sn']}','{$value['factory_sn']}','{$value['ring_mouth']}','{$value['is_cp_kt']}','{$value['cat_type']}','{$value['hand_inch']}','{$value['material']}','{$value['gross_weight']}','{$value['net_gold_weight']}','{$value['gold_loss']}','{$value['gold_price']}','{$value['main_stone']}','{$value['main_stone_weight']}','{$value['main_stone_num']}','{$value['work_fee']}','{$value['extra_stone_fee']}','{$value['other_fee']}','{$value['fittings_cost_fee']}','{$value['tax_fee']}','{$value['customer_info_stone']}','{$value['chengbenjia']}','{$value['zhushiyanse']}','{$value['zhushijingdu']}','{$value['zhushidanjia']}','{$value['fushi']}','{$value['fushilishu']}','{$value['fushizhong']}','{$value['fushidanjia']}','{$value['zhengshuhao']}','{$value['shi2']}','{$value['shi2lishu']}','{$value['shi2zhong']}','{$value['shi2danjia']}','{$value['shi3']}','{$value['shi3lishu']}','{$value['shi3zhong']}','{$value['shi3danjia']}')";

				$pdo->query($sql);
				$detail_id = $pdo->lastInsertId();
				//var_dump($detail_id);exit;

				//添加日志
				$uid		= $_SESSION['userId'];
				$uname   	= $_SESSION['userName'];
				$time   	= date('Y-m-d H:i:s');
				$remark = "生成采购收货单，单据流水号：".$id.",货品生成。";
				$sql = "insert `purchase_log`( `rece_detail_id`,`status`,`remark`,`uid`,`uname`,`time`) values ({$detail_id},'1','{$remark}','{$uid}','{$uname}','{$time}') ";
				//echo $sql;exit;
				$pdo->query($sql);
			}

			//业务逻辑结束
		}
		catch(Exception $e)
		{
			// die($sql);
			//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		$res['success']	= true;
		$res['id']		= $id;
		return $res;

	}
	/*
	修改采购收货单
	*/
	public function update_caigou_info($info,$data)
	{
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			//1、删除已有的货品明细
			$id = $info['id'];
			$sql = "DELETE FROM `purchase_receipt_detail` WHERE purchase_receipt_id = {$info['id']}";
			//echo $sql;
			$pdo->query($sql);

			//2、修改收货单
			$sql = "update `purchase_receipt` set ship_num = '{$info['ship_num']}',prc_id='{$info['prc_id']}',prc_name='{$info['prc_name']}',remark='{$info['remark']}',num='{$info['num']}',all_amount='{$info['all_amount']}',chengbenjia='{$info['chengbenjia']}',status=1,edit_user_id='{$_SESSION['userId']}',edit_user_name='{$_SESSION['userName']}',edit_time=CURRENT_TIME  where id=$id";
			$pdo->query($sql);
			//var_dump($sql);exit;
			//3、重新添加数据明细
			foreach ($data as $value)
			{

				$sql = "insert into `purchase_receipt_detail`(`purchase_receipt_id`,`xuhao`,`customer_name`,`purchase_sn`,`bc_sn`,`style_sn`,`factory_sn`,`ring_mouth`,`is_cp_kt`,`cat_type`,					`hand_inch`,`material`,`gross_weight`,`net_gold_weight`,`gold_loss`,`gold_price`,`main_stone`,`main_stone_weight`,`main_stone_num`,`work_fee`,`extra_stone_fee`,`other_fee`,`fittings_cost_fee`,`tax_fee`,`customer_info_stone`,`chengbenjia`,`zhushiyanse`,`zhushijingdu`,`zhushidanjia`,`fushi`,`fushilishu`,`fushizhong`,`fushidanjia`,`zhengshuhao`,`shi2`,`shi2lishu`,`shi2zhong`,`shi2danjia`,`shi3`,`shi3lishu`,`shi3zhong`,`shi3danjia`) values ({$id},'{$value['xuhao']}','{$value['customer_name']}','{$value['purchase_sn']}','{$value['bc_sn']}','{$value['style_sn']}','{$value['factory_sn']}','{$value['ring_mouth']}','{$value['is_cp_kt']}','{$value['cat_type']}','{$value['hand_inch']}','{$value['material']}','{$value['gross_weight']}','{$value['net_gold_weight']}','{$value['gold_loss']}','{$value['gold_price']}','{$value['main_stone']}','{$value['main_stone_weight']}','{$value['main_stone_num']}','{$value['work_fee']}','{$value['extra_stone_fee']}','{$value['other_fee']}','{$value['fittings_cost_fee']}','{$value['tax_fee']}','{$value['customer_info_stone']}','{$value['chengbenjia']}','{$value['zhushiyanse']}','{$value['zhushijingdu']}','{$value['zhushidanjia']}','{$value['fushi']}','{$value['fushilishu']}','{$value['fushizhong']}','{$value['fushidanjia']}','{$value['zhengshuhao']}','{$value['shi2']}','{$value['shi2lishu']}','{$value['shi2zhong']}','{$value['shi2danjia']}','{$value['shi3']}','{$value['shi3lishu']}','{$value['shi3zhong']}','{$value['shi3danjia']}')";
				$pdo->query($sql);
				$detail_id = $pdo->lastInsertId();
				//var_dump($detail_id);exit;

				//添加日志
				$uid		= $_SESSION['userId'];
				$uname   	= $_SESSION['userName'];
				$time   	= date('Y-m-d H:i:s');
				$remark = "生成采购收货单，单据流水号：".$id.",货品生成。";
				$sql1 = "insert `purchase_log`( `rece_detail_id`,`status`,`remark`,`uid`,`uname`,`time`) values ({$detail_id},'1','{$remark}','{$uid}','{$uname}','{$time}') ";
				//echo $sql1;exit;
				$pdo->query($sql1);
			}
			//业务逻辑结束
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}
	/***
	fun:check;审核收货单
	****/
	public function check_caigou_info($id)
	{
		$pdo = $this->db()->db();//pdo对象
		try
		{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//1、修改收货单状态为审核
			$sql = "update `purchase_receipt` set status=2,edit_user_id='{$_SESSION['userId']}',edit_user_name='{$_SESSION['userName']}',edit_time=CURRENT_TIME where `id` ={$id}";
			$pdo->query($sql);
			//2、修改货品明细状态为待质检
			$sql1 = "update `purchase_receipt_detail` set status=3 where purchase_receipt_id ={$id}";
			$pdo->query($sql1);
			//3、添加日志
			$detailModel = new PurchaseReceiptDetailModel(23);
			$logModel = new PurchaseLogModel(23);
			$arr = $detailModel->getListForRid($id,"id,status");
			$uid		= $_SESSION['userId'];
			$uname   	= $_SESSION['userName'];
			$time   	= date('Y-m-d H:i:s');

			foreach($arr as $k => $v)
			{
				$remark = "采购收货单流水号：".$id." 审核成功，货品等待质检。";
				$sql2 = "insert `purchase_log`( `rece_detail_id`,`status`,`remark`,`uid`,`uname`,`time`) values ({$v['id']},'3','{$remark}','{$uid}','{$uname}','{$time}') ";
				$pdo->query($sql2);
				//$logModel->addLog($v['id'],$v['status'],'{$remark}');
			}
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}
		/***
	fun:cancle_caigou_info;取消收货单
	****/
	public function cancle_caigou_info($id,$status=0,$status_info=0)
	{
		$pdo = $this->db()->db();//pdo对象
		try
		{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//1、修改收货单状态
			$sql = "update `purchase_receipt` set status={$status},edit_user_id='{$_SESSION['userId']}',edit_user_name='{$_SESSION['userName']}',edit_time=CURRENT_TIME where `id` ={$id}";
			$pdo->query($sql);
			//2、修改货品明细状态
			$sql1 = "update `purchase_receipt_detail` set status={$status_info} where purchase_receipt_id ={$id}";
			$pdo->query($sql1);
			//3、添加日志
			$detailModel = new PurchaseReceiptDetailModel(23);
			$logModel = new PurchaseLogModel(23);
			$arr = $detailModel->getListForRid($id,"id,status");
			$uid		= $_SESSION['userId'];
			$uname   	= $_SESSION['userName'];
			$time   	= date('Y-m-d H:i:s');

			foreach($arr as $k => $v)
			{
				$remark = "采购收货单流水号：".$id." 取消成功，货品作废。";
				$sql2 = "insert `purchase_log`( `rece_detail_id`,`status`,`remark`,`uid`,`uname`,`time`) values ({$v['id']},'8','{$remark}','{$uid}','{$uname}','{$time}') ";
				$pdo->query($sql2);
				//$logModel->addLog($v['id'],$v['status'],"采购收货单流水号：".$id." 取消成功，货品作废。");
			}
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}
	//ADD BY ZHANGRUIYING
	function GetPurchaseList($ids)
	{
		if(!empty($ids))
		{
			$sql="select rr.pinfo_id,m.style_sn from (select r.pinfo_id,count(r.id) as num from purchase_goods as r where r.pinfo_id in($ids) group by r.pinfo_id having num<2) as rr left join purchase_goods as m on m.pinfo_id=rr.pinfo_id";
			$rows=$this->db()->getAll($sql);
			$arr=array();
			if($rows)
			{
				foreach($rows as $k=>$r)
				{

					$arr[$r['pinfo_id']]=$r['style_sn'];
				}
			}
			return $arr;
		}
		else
		{
			return array();
		}
	}
	//ADD END
}

?>