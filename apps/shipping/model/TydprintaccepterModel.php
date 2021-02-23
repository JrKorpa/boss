<?php
/**
 *  -------------------------------------------------
 *   @file		: ShipParcelModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Liulinyan <939942457@qq.com>
 *   @date		: 2015-07-30 10:15:23
 *   @update	:
 *  -------------------------------------------------
 */
class TydprintaccepterModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'shop_cfg_accepter';
		$this->pk='id';
		$this->_prefix='';
	    $this->_dataObject = array("id"=>" ",
		"accepter_name"=>"体验店收货人",
		"accepter_mobile"=>"体验店收货人电话号码",
	    "accepter_company"=>"体验店收货人单位名称",
	    "accepter_address"=>"体验店收货人地址"
	    );
		parent::__construct($id,$strConn);
	}
	/**
	* 添加收货人信息
	* @param $express_sn 快递单号
	* @param $express_id 快递公司
	* @param $company_id 目标展厅
	* @param  $operate_content 操作备注
	*/
	public function insertDate($id, $acceptername, $acceptermobile, $accepter_company, $accepter_address){
		
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//业务逻辑开始
			$sql = "INSERT INTO `shop_cfg_accepter` (`id`, `accepter_name` ,`accepter_mobile`,`accepter_company`,`accepter_address`) VALUES ( {$id}, '{$acceptername}' , '{$acceptermobile}', '{$accepter_company}', '{$accepter_address}')";
			$pdo->query($sql);
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
	public function getaccepter($id){
		$sql = "SELECT * FROM `shop_cfg_accepter` WHERE `id` = {$id}";
		return $this->db()->getOne($sql);
	}
}

?>