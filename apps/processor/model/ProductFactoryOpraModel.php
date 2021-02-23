<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductFactoryOpraModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-12 10:06:14
 *   @update	:
 *  -------------------------------------------------
 */
class ProductFactoryOpraModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'product_factory_opra';
        $this->_dataObject = array("id"=>"ID",
"bc_id"=>"布产号",
"opra_action"=>"工厂操作动作",
"opra_uname"=>"操作人",
"opra_uid"=>"操作人ID",
"opra_time"=>"操作时间",
"opra_info"=>"操作备注");
		parent::__construct($id,$strConn);
	}

	//获取所有工厂操作列表
	public function getActionFactory(){
		$FactoryOpraModel = new FactoryOpraDictModel(13);
		$factory_config = $FactoryOpraModel->GetInfo($fields = '`name` , `dict_value`' , $where = '`status` = 1 ', $type = 'all');
		return $factory_config;
	}
	//根据工厂操作id,获取工厂操作名
	public function getActionName($action_id){
		$arr = $this->getActionFactory();
		foreach ($arr as $key => $value) {
			if($value['dict_value'] == $action_id){
				return $value['name'];
			}
		}
	}

	function pageList ($where)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";

		if($where['bc_id'] !== "")
		{
			$sql .= " AND bc_id = ".$where['bc_id'];
		}
		if(isset($where['opra_action']) && $where['opra_action'] !== '')
		{
			$sql .= " AND opra_action = ".$where['opra_action'];
		}

		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getAll($sql);
		return $data;
	}
	/**获取最新操作的布产状态根据布产号*/
	public function getNewStatus($id)
	{
		$sql = "select opra_action from `".$this->table()."` where bc_id={$id} order by id desc";
		return $this->db()->getOne($sql);
	}

	/**
	* 写入
	* @param $bc_id Array 布产单的ID
	* @param $fac_opra Int 提交过来的工厂操作
	* @param $opra_info String 操作备注
	*/
	public function addinsert($bc_id , $fac_opra , $opra_info){
		$dd = new DictModel(1);
		$model_pw = new AppProcessorWorktimeModel(13);
		$logModel = new ProductOpraLogModel(14);
		$salesModel = new SalesModel(27);
		$warehouseModel = new WarehouseModel(21);
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			foreach($bc_id as $key => $id)
			{
				$model = new ProductInfoModel($id,13);
				$prc_id = $model->getValue('prc_id');
				$esmt_time=$model->getValue('esmt_time');
				

				//取得当前布产单的最新操作状态
				$action_status = $this->getNewStatus($id);

				//检测当前工厂操作和上一次工厂操作是否相同，如果相同，阻止这种不文明的行为
				if($fac_opra == $action_status && $fac_opra != 11){
					$note = $this->getActionName($fac_opra);
					return array('success' => 0 , 'error' => "布产单：<span style='color:red;'>{$model->getValue('bc_sn')}</span> 最近一次已经操作\"{$note}\"操作");
				}
				/*
				 $row   = $model_pw->getInfoById($prc_id);
				$wait_dia         = 25;//(等钻加时) 默认25天 没有则按默认走
				$behind_wait_dia  = 5;//(等钻后加时) 默认5天 没有则按默认走
				$is_rest          = 1;//默认不休
				$prc_id = $model->getValue('prc_id');
				
				if ($row)
				{
					$wait_dia			= $row['wait_dia'];
					$behind_wait_dia	= $row['behind_wait_dia'];
					$is_rest			= $row['is_rest'];
				}
               */
				 // 之前的逻辑
				//等钻 标准出厂时间加时 只有第一次等钻才加时
				$opra_list = $this->pageList(array('bc_id'=>$id,'opra_action'=>11));		//这里的数字 是数据字典 “布产单生产状态”（buchan_fac_opra）里的 "等钻"枚举Key ， 不是工厂操作维护的 ID
				//如果提交过来的操作时等钻，并且是第一次操作等钻
				if ($fac_opra == 11 && (!count($opra_list)))
				{
					//$time = $model_pw->js_normal_time($wait_dia,$is_rest);
					$time = $model_pw->getEsmttimeByIdOnWait($id);
					if(strtotime($time)>strtotime($esmt_time)){
					  $model->setValue('esmt_time',$time);
					} 
					 $model->setValue('wait_dia_starttime',date('Y-m-d H:i:s',time()));
					 $model->setValue('wait_dia_finishtime',$time);
					
				}
				else if ($action_status == 11)//上次操作是等钻、需要等钻后加时 按工作日加
				{
					//$time = $model_pw->js_normal_time($behind_wait_dia,$is_rest);
					$time = $model_pw->getEsmttimeById($id);
					if(strtotime($time)>strtotime($esmt_time)){
					  $model->setValue('esmt_time',$time);
					}  
					  $model->setValue('wait_dia_endtime',date('Y-m-d H:i:s',time()));
					
				}		
				
				
				//出厂 和 送钻 不写入这个字段。这个字段是记录上一次工厂操作，而出厂和送钻从工厂操作提取出来，不再属于工厂操作
				if($fac_opra != 6 && $fac_opra != 3){
					$model->setValue('factory_opra_status',$fac_opra);
					//写入工厂操作日志
					$olddo = array();
					$opra_info =preg_replace("/\s|　/","",$opra_info);
					$newdo=array(
						"bc_id"			=> $id,
						"opra_action"	=> $fac_opra,
						"opra_uid"		=> $_SESSION['userId']?$_SESSION['userId']:0,
						"opra_uname"	=> $_SESSION['userName']?$_SESSION['userName']:'第三方',
						"opra_time"		=> date("Y-m-d H:i:s"),
						"opra_info"		=> $opra_info
					);
					$res = $this->saveData($newdo,$olddo);
					if($res === false)
					{
						$pdo->query(''); 		//制造错误回滚
					}

				}
				$model->setValue('buchan_fac_opra',$fac_opra);		//记录生产状态

				//add by zhangruiying布产最后修改时间
				$model->setValue('edit_time',date('Y-m-d H:i:s'));
				$model->setValue('remark',"布产单工厂操作：".$dd->getEnum('buchan_fac_opra',$fac_opra)."，备注：".$opra_info);
				$res1 = $model->save(true);
				if($res1 === false)
				{
					$pdo->query(''); 		//制造错误回滚
				}

				//写入操作日志
				$opra_info = $opra_info ? $opra_info : '无';
				//$logModel->addLog($id,4,"工厂操作：".$dd->getEnum('buchan_fac_opra',$fac_opra)."，备注：".$opra_info);
				$logModel->addLog($id,"布产单工厂操作：".$dd->getEnum('buchan_fac_opra',$fac_opra)."，备注：".$opra_info);//操作日志
				//$model->Writeback($id, "工厂操作：".$dd->getEnum('buchan_fac_opra',$fac_opra)."，备注：".$opra_info);	//回写订单操作日志 BY hulichao
			}

		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' => '事物执行不成功，导致操作失败');
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return array('success' => 1 , 'error' => '操作成功');
	}
}?>