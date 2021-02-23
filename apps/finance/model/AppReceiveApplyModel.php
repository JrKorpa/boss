<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReceiveApplyModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-26 18:44:32
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiveApplyModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_receive_apply';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"应收申请ID",
            "apply_number"=>"应收申请单号",
            "status"=>"应收申请状态：1、新增，2、待审核、3、已驳回、4、已取消、5、待生成应收单，6、已生成应收单",
            "should_number"=>"财务应收单单号",
            "from_ad"=>"订单来源",
            "cash_type"=>"收款类型：1、销售收款，2、退货退款",
            "make_time"=>"制单时间",
            "make_name"=>"制单人",
            "check_time"=>"审核时间",
            "check_name"=>"审核人",
            "amount"=>"总数量",
            "total"=>"应收总金额",
            "external_total_all"=>"外部总金额",
            "kela_total_all"=>"BDD金额",
            "jxc_total_all"=>"销账金额",
            "check_sale_number"=>"核销单单号");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppReceiveApplyController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT pa.*,(pa.`jxc_total_all` - pa.`kela_total_all`) as sale_total_cha,(pa.`kela_total_all` - pa.`external_total_all`) as make_total_cha,pa.from_ad FROM `".$this->table()."` AS pa";
        $str = " 1 AND ";

        if(!empty($where['apply_number'])){
            $str.= " pa.`apply_number` LIKE '".addslashes($where['apply_number'])."%' AND ";
        }
        if(!empty($where['cash_type'])){
            $str.= " pa.`cash_type` = ".$where['cash_type']." AND ";
        }
        if(!empty($where['from_ad'])){
            $str.= " pa.`from_ad` = '".$where['from_ad']."' AND ";
        }
        if(!empty($where['status'])){
            $str.= " pa.`status` = ".$where['status']." AND ";
        }
        if(!empty($where['check_sale_number'])){
            $str.= " pa.`check_sale_number` LIKE '".addslashes($where['check_sale_number'])."%' AND ";
        }
        if(!empty($where['make_time_start'])){
            $str.= " pa.`make_time` >= '".$where['make_time_start']."' AND ";
        }
        if(!empty($where['make_time_end'])){
            $str.= " pa.`make_time` <= '".$where['make_time_end']."' AND ";
        }
        if(!empty($where['check_time_start'])){
            $str.= " pa.`check_time` >= '".$where['check_time_start']."' AND ";
        }
        if(!empty($where['check_time_end'])){
            $str.= " pa.`check_time` <= '".$where['check_time_end']."' AND ";
        }
        if(!empty($where['sale_total_cha'])){
			$sql .= ' (pa.`jxc_total_all` - pa.`kela_total_all`) !=0  AND ';
		}
		if(!empty($where['make_total_cha'])){
			$sql .= ' (pa.`kela_total_all` - pa.`external_total_all`) !=0  AND ';
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY pa.`id` DESC";
        //echo $sql;die;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}


    /**
	*	获取指定信息集合
	*	@param $fields 	String 	-	所查字段 (例: id,name,sex）
	*	@param $table 	String 	-	数据表
	*	@param $condition 	String 	-	查询条件 (例: where id = 1 order by id)
	*	@return Array
	*/
	public function getInfo($fields='*',$table='',$condition='',$all_row = false){
		if (empty($table)) return array();
		$sql = 'SELECT '.$fields.' FROM `'.$table.'` '.$condition;
		if(!$all_row){
			return $this->db()->getAll($sql);
		}else{
			return $this->db()->getRow($sql);
		}
	}

	/**
	 *	导出
	 *
	 *	@url AppReceiveApplyController/search
	 */
	function allData ($where)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT pa.*,(pa.`jxc_total_all` - pa.`kela_total_all`) as sale_total_cha,(pa.`kela_total_all` - pa.`external_total_all`) as make_total_cha,ecs.`ad_name` FROM `".$this->table()."` AS pa , ecs_ad AS ecs ";
        $str = " pa.from_ad = ecs.ad_sn AND ";

        if(!empty($where['apply_number'])){
            $str.= " pa.`apply_number` LIKE '".addslashes($where['apply_number'])."%' AND ";
        }
        if(!empty($where['cash_type'])){
            $str.= " pa.`cash_type` = ".$where['cash_type']." AND ";
        }
        if(!empty($where['from_ad'])){
            $str.= " pa.`from_ad` = '".$where['from_ad']."' AND ";
        }
        if(!empty($where['status'])){
            $str.= " pa.`status` = ".$where['status']." AND ";
        }
        if(!empty($where['check_sale_number'])){
            $str.= " pa.`check_sale_number` LIKE '".addslashes($where['check_sale_number'])."%' AND ";
        }
        if(!empty($where['make_time_start'])){
            $str.= " pa.`make_time` >= '".$where['make_time_start']."' AND ";
        }
        if(!empty($where['make_time_end'])){
            $str.= " pa.`make_time` <= '".$where['make_time_end']."' AND ";
        }
        if(!empty($where['check_time_start'])){
            $str.= " pa.`check_time` >= '".$where['check_time_start']."' AND ";
        }
        if(!empty($where['check_time_end'])){
            $str.= " pa.`check_time` <= '".$where['check_time_end']."' AND ";
        }
        if(!empty($where['sale_total_cha'])){
			$sql .= ' (pa.`jxc_total_all` - pa.`kela_total_all`) !=0  AND ';
		}
		if(!empty($where['make_total_cha'])){
			$sql .= ' (pa.`kela_total_all` - pa.`external_total_all`) !=0  AND ';
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY pa.`id` DESC";
        //echo $sql;die;
		$data = $this->db()->getAll($sql);
		return $data;
	}

    public function saveDatas($checkdata,$data)
	{
		$payapply_detail_model  = new AppReceiveApplyDetailModel(30);
		if(empty($checkdata['id']))//添加数据
		{
			$id= $this->saveData($checkdata,array()); //1、保存应付单数据
            $sql = "update `".$this->table() ."` set `apply_number` = 'YSSQ".$id."' where `id` = {$id}";
			$this->db()->query($sql,array());//修改应付单
            $payapply_detail_model->save_vd($data,$id);//2、 保存应付单详细订单记录

		}
		else  //修改数据
		{
			// 1、修改应付申请单单据信息
			$id = $checkdata['id'];
			$arr = $this->saveData($checkdata,$this->getDataObject());//修改单据内容
			// 2、修改应付单详细单据信息 （删除原有  增加现在）
			$payapply_detail_model->deleteOfId($id);
			$payapply_detail_model->save_vd($data,$id);

		}
			/*将订单系统中的应收申请号写入  (注意：只是销售收款时写入)*/
		if ($checkdata['cash_type'] == 1)
		{
			$kelaorder          = $payapply_detail_model->getDataOfapply_Id($id);//通过应付申请号获取所有BDD订单号
			$PayOrderInfo_model     = new PayOrderInfoModel(29);
			$PayOrderInfo_model->update_pl(array('apply_number'=>'YSSQ'.$id , 'status'=>2),$kelaorder);

		}
		return array('result'=>'1','id'=>$id);  //返回核销id

	}


    /**
	* 修改不含ID的数据
	* @param $arr Array 修改的 字段=>值
	* @param $id Int 主键
	*/
	public function updateNoId($arr,$id){
		$sql = 'UPDATE `'.$this->table().'` SET ';
		foreach($arr as $k =>$v){
			$sql .= " $k='$v',";
		}
		$sql=rtrim($sql,',');
		$sql.='where `id`='.$id;
		$this->db()->query($sql);
	}

    /**
     * 修改   只能修改数据中有id的
     */
	function update($checkdata)
	{
		$arr = $this->saveData($checkdata,$this->getDataObject());//修改单据内容
	}

	/*获取应收申请单中的 详细单据中的 BDD订单号+外部金额+收款类型*/
	public function renovate($apply_id){
		$sql = "SELECT pay.cash_type,pad.kela_sn,pad.external_total FROM ".$this->table()." AS pay RIGHT JOIN app_receive_apply_detail AS pad ON pad.apply_id=pay.id where pad.apply_id=".$apply_id;
		return $this->db()->getAll($sql);
	}


	/**
	* 判断传入的ID的某一列的值是不是一样的。
	* @param $col String 检测的字段
	* @param $ids String 数据id
	*/
	public function checkDistinct($col,$ids){
		$sql = "select count(distinct $col) from ".$this->table()." where id in($ids)";
		$count = $this->db()->getOne($sql);
		if($count == '1'){return true;}return false;
	}

	/*获取指定单条数据*/
	public function getRow($id,$col = "*")
	{
		$sql = "select $col from ".$this->table()." where id = ".$id;
		return $this->db()->getRow($sql);
	}
	/**
	* 计算传入的ID的金额总和。
	* @param $ids String id
	* @return 金额
	*/
	public function getTotalOfIds($ids){
		$sql = 'SELECT sum(total) FROM '.$this->table().' WHERE id in ('.$ids.')';
		$total = $this->db()->getOne($sql);
		return $total;
	}

	public function update2($status,$apply_nums){
		$apply_numbers = '';
		foreach($apply_nums as $v){
			$apply_numbers.=' \''.$v.'\',';
		}
		$apply_numbers = rtrim($apply_numbers,',');
		$sql ='UPDATE '.$this->table().' SET status = '.$status.', should_number = "" WHERE apply_number in('.$apply_numbers.')';
		$rows = $this->db()->query($sql);
		return $rows->rowCount();
	}

}

?>