<?php
/**
 *  -------------------------------------------------
 *   @file		: ShipParcelModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-06 10:15:23
 *   @update	:
 *  -------------------------------------------------
 */
class ShipParcelModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'ship_parcel';
		$this->pk='id';
		$this->_prefix='';
	        $this->_dataObject = array("id"=>" ",
		"express_id"=>"快递公司ID",
		"express_sn"=>"快递单号",
		"amount"=>"单据总金额",
		"num"=>"货品数量",
		"shouhuoren"=>"包裹收货方",
		"company_id"=>"公司ID （目标展厅）",
		"sales_channels"=>"渠道类别（数字字典）",
		"create_time"=>"制单时间",
		"send_status"=>"发货状态(数字字典order.send_good_status)",
		"send_time"=>"发货时间",
		"is_print"=>"是否打印(数字字典confirm)");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ShipParcelController/search
	 */
	function pageList ($where,$page=1,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT `a`.`id`, `a`.`express_id`, `a`.`express_sn`, `a`.`company_id`, `a`.`amount`, `a`.`num`, `a`.`shouhuoren`, `a`.`send_status`, `a`.`is_print`, `a`.`create_time`, `a`.`send_time`, `a`.`sales_channels` FROM `ship_parcel` AS `a` ";
		if(!empty($where['bill_m_no']) || !empty($where['order_sn']))
		{
			$sql .= "LEFT JOIN `ship_parcel_detail` AS `b` ON `a`.`id` = `b`.`parcel_id`";
		}
		$str = '';
		if(isset($where['express_sn']) && $where['express_sn'] != "")
		{
			$str .= "`a`.`express_sn` like \"%".addslashes($where['express_sn'])."%\" AND ";
		}
		if(isset($where['express_id']) && !empty($where['express_id']))
		{
			$str .= "`a`.`express_id`='".$where['express_id']."' AND ";
		}
		if(isset($where['company_id']) && !empty($where['company_id']))
		{
			$str .= "`a`.`company_id` = '".$where['company_id']."' AND ";
		}
		if(isset($where['send_status']) && !empty($where['send_status']))
		{
			$str .= "`a`.`send_status`=".$where['send_status']." AND ";
		}
		if(isset($where['is_print']) && $where['is_print'] !== '')
		{
			$str .= "`a`.`is_print`=".$where['is_print']." AND ";
		}
        if (isset($where['date_time_s']) && !empty($where['date_time_s'])){
                $str .= " `a`.`create_time` >= '{$where['date_time_s']} 00:00:00' AND ";
        }
        if (isset($where['date_time_e']) && !empty($where['date_time_e'])){
                $str .= " `a`.`create_time` <= '{$where['date_time_e']} 23:59:59' AND ";
        }
        if (isset($where['send_date_time_s']) && !empty($where['send_date_time_s'])){
                $str .= " `a`.`send_time` >= '{$where['send_date_time_s']} 00:00:00' AND ";
        }
        if (isset($where['send_date_time_e']) && !empty($where['send_date_time_e'])){
                $str .= " `a`.`send_time` <= '{$where['send_date_time_e']} 23:59:59' AND ";
        }

		if (isset($where['bill_m_no']) && !empty($where['bill_m_no'])){
			$str .= " `b`.`zhuancang_sn` = '{$where['bill_m_no']}' AND ";
		}
		if (isset($where['order_sn']) && !empty($where['order_sn'])){
			$str .= " `b`.`order_sn` = '{$where['order_sn']}' AND ";
		}

		$order_by = "`a`.`id`";

		if(isset($where['order_by']) && $where['order_by'] != '')
		{
			$order_by = "`a`.".$where['order_by'];

		}

		$sort = "DESC";
		if(isset($where['sort']) && $where['sort'] != '')
		{
			$sort = $where['sort'];
		}

		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY ".$order_by." ".$sort;

		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		if ($_SESSION['userName'] == 'admin')
		{
			//echo $sql;var_dump($data);exit;
		}
		return $data;
	}

	/**
	* 输入调拨单号，查询 相同收货展厅 的 包裹单列表
	* @param $to_company_id 输入的调拨单号的 入货仓ID
	*/
	public function getListByDiaobo($to_company_id,$page,$pageSize=10,$useCache=true){
		$sql = "SELECT `id`, `express_sn`, `express_id`, `amount`, `num`, `company_id`,`send_status`,`shouhuoren` FROM `ship_parcel` WHERE `company_id` = {$to_company_id} ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	//检查转仓单是否有存在于其他快递单中
	public function getHaveZc($zhuancang_sn)
	{
		$sql = "SELECT sp.id,sp.express_sn FROM ship_parcel as sp,ship_parcel_detail as spd WHERE sp.id = spd.parcel_id AND spd.zhuancang_sn = '".$zhuancang_sn."'";
		return $this->db()->getRow($sql);
	}


	/**
	* 添加包裹单，写入操作日志
	* @param $express_sn 快递单号
	* @param $express_id 快递公司
	* @param $company_id 目标展厅
	* @param  $operate_content 操作备注
	*/
	public function insertDate($express_sn, $express_id, $company_id , $operate_content){
		$time = date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];

		$model = new CompanyModel(1);
		$company_list =  $model->getCompanyTree();//公司列表
		foreach($company_list as $val){
			if($val['id'] == $company_id){
				$shouhuofang = $val['company_name'];
			}
		}
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//业务逻辑开始
			$sql = "INSERT INTO `ship_parcel` (`express_id`, `express_sn` ,`shouhuoren` ,`company_id`, `create_time`, `create_user`) VALUES ( {$express_id}, '{$express_sn}' , '{$shouhuofang}', {$company_id} , '{$time}', '{$user}')";
			$pdo->query($sql);
			$id = $pdo->lastInsertId();

			$sql = "INSERT INTO `ship_parcel_log` (`parcel_id`, `operate_content`, `operate_time`, `operate_user`) VALUES ( {$id}, '{$operate_content}', '{$time}', '{$user}' )";
			$pdo->query($sql);
			//业务逻辑结束
		}
		catch(Exception $e){//捕获异常
			// die($sql);
		$pdo->rollback();//事务回滚
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}

	/**普通查询**/
	public function select2($fields , $where , $type =1 ){
		$sql = "SELECT {$fields} FROM  `ship_parcel` WHERE {$where} ORDER BY `id` DESC";
		if($type == 1){
			return $this->db()->getOne($sql);
		}else if($type ==2){
			return $this->db()->getRow($sql);
		}else if($type == 3){
			return $this->db()->getAll($sql);
		}
	}

	/** 发货操作 **/
	public function send($id){
		$time= date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//业务逻辑开始
			$sql = "UPDATE `ship_parcel` SET `send_status` = 2, `send_time` = '{$time}' WHERE `id` = {$id}";
			$pdo->query($sql);

			$sql = "INSERT INTO `ship_parcel_log` (`parcel_id`, `operate_content`, `operate_time`, `operate_user`) VALUES ( {$id}, '发货操作', '{$time}', '{$user}' )";
			$pdo->query($sql);
			//业务逻辑结束
		}
		catch(Exception $e){//捕获异常
			// die($sql);
		$pdo->rollback();//事务回滚
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}

	/** 根据包裹单id 获取操作备注 **/
	public function getBeizhu($id){
		$sql = "SELECT `operate_content` FROM `ship_parcel_log` WHERE `parcel_id` = {$id}";
		return $this->db()->getOne($sql);
	}

	/** 打印写入日志 **/
	public function insertLog($id, $content = "打印包裹单"){
		$time = date('Y-m-d H:i:s');
		$user = $_SESSION['userName'];
		$sql = "INSERT INTO `ship_parcel_log` (`parcel_id`, `operate_content`, `operate_time`, `operate_user`) VALUES ({$id} , '{$content}', '{$time}', '{$user}')";
		return $this->db()->query($sql);
	}
}

?>