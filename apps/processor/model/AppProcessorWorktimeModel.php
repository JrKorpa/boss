<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorWorktimeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-01 10:14:48
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorWorktimeModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_processor_worktime';
		$this->pk='pw_id';
		$this->_prefix='';
        $this->_dataObject = array(	"pw_id"=>" ",
									"processor_id"=>"供应商id",
									"normal_day"=>"标准出货周期",
									"wait_dia"=>"等钻加时",
									"behind_wait_dia"=>"等钻后加时",
									"ykqbzq"=>"有款起版周期",
									'wkqbzq'=>'无款起版周期',
									"is_rest"=>"工作休息；1为不休；2为单休；3为双休",
									"order_problem"=>"订单问题加时",
									'order_type'=>'订单类型',
									'now_wait_dia'=>'现货等钻加时',
									'is_work'=>'周末上班日期',
									'holiday_time'=>'放假日期'

		);
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppProcessorWorktimeController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";

		$str = '';
		if(!empty($where['_id']))
		{
			$str .="`processor_id`='".$where['_id']."' AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}


		$sql .= " ORDER BY `pw_id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

    /**
     * 检查数据表中是否有数据
     * @return type
     */
    function isHave($processor_id){
        if($processor_id<1){
            return FALSE;
        }
        $sql = "SELECT COUNT(1) FROM `".$this->table()."` WHERE `processor_id`=$processor_id";
        return $this->db()->getOne($sql);
    }

	/*******
	fun:getInfoById
	通过id获取加时信息
	*******/
	function getInfoById($id,$order_type=1)
	{
		$sql = "select pw_id,processor_id,normal_day,wait_dia,behind_wait_dia,ykqbzq,order_problem,is_rest,is_work,holiday_time from `".$this->table()."` where `processor_id`=$id and order_type={$order_type} order by pw_id desc";
		return $this->db()->getRow($sql);
	}

	/*计算标准出厂时间
	** normal_day要加时的天数
	** is_rest 是否休息
	** esmt_time 从哪天开始算
	*/
	function js_normal_time($normal_day,$is_rest,$esmt_time='')
	{
		$ziranri = 1;
		$gongzuori = 0;
		if(empty($esmt_time)){$esmt_time = time();}
		$normal_time = $esmt_time;
		while($gongzuori < $normal_day){
			$normal_time = $esmt_time+3600*24*$ziranri;//流水日期
			$ziranri++;
			if(date("w",$normal_time) == 6){
				if($is_rest ==2){//单休
					$gongzuori++;
				}elseif($is_rest ==1){//不休
					$gongzuori++;
				}
			}elseif(date("w",$normal_time) == 0){
				if($is_rest ==1){//不休
					$gongzuori++;
				}
			}else{
				$gongzuori++;
			}
		}
		$time = date("Y-m-d",$normal_time);
		return $time;
	}

	/**
	* 普通查询
	*/
	public function select2($fields = ' * ', $where = " 1 LIMIT 1", $type = 'one'){
		$sql = "SELECT {$fields} FROM `app_processor_worktime` WHERE {$where}";
		if($type == 'one'){
			return $this->db()->getOne($sql);
		}else if($type == 'row'){
			return $this->db()->getRow($sql);
		}else if($type == 'all'){
			return $this->db()->getAll($sql);
		}
	}

	/*
	* 更新布产单标准出厂时间
	* @params $pro_id  供应商ID
	* @params $type 1 现货 2 期货
	*/
	public function updateEsmttimeByprc_id($pro_id,$bc_sn,$type=1){

		if($type == 1){
			$sql = "select now_wait_dia from ".$this->table()." where processor_id=".$pro_id;
			$now_wait_dia = $this->db()->getOne($sql);		//获得现货等钻加时天数
			$now_wait_dia = empty($now_wait_dia)?0:$now_wait_dia;
			$sql ="select esmt_time from kela_supplier.product_info where bc_sn='".$bc_sn."'";
			$esmt = $this->db()->getOne($sql);
			$esmt_time = date('Y-m-d',strtotime($esmt)+intval($now_wait_dia*3600*24));
			$sql = "update kela_supplier.product_info set esmt_time='".$esmt_time."' where bc_sn='".$bc_sn."'";
			$res = $this->db()->query($sql);
		}else{
			$sql1 = "select wait_dia from ".$this->table()." where processor_id=".$pro_id;
			$wait_dia = $this->db()->getOne($sql1);
			$wait_dia = empty($wait_dia)?0:$wait_dia;
			$sql2 ="select esmt_time from kela_supplier.product_info where bc_sn='".$bc_sn."'";
			$esmt = $this->db()->getOne($sql2);	
			$esmt_time = date('Y-m-d',strtotime($esmt)+intval($wait_dia*3600*24));
			$sql = "update kela_supplier.product_info set esmt_time='".$esmt_time."' where bc_sn='".$bc_sn."'";
			$res = $this->db()->query($sql);
		}
	
		if(!$res){
			return false;
		}else{
			return true;
		}

	}

	/*
    *通过供应商的ID获取供应商名称
    *
    */
    public function getSupplierInfoByIds($ids){
            $sql ="select id,name from app_processor_info";
            return $this->db()->getAll($sql);
    }


    /*
    *批量更新加工时间信息
    *
    */
    public function updateProcessorWorktimeById($args,$type=1,$order_type){
    	$arr_ids = implode("','",$args['ids']);
    	if($type==1){
    		$sql = "update ".$this->table()." set is_rest='".$args['is_rest']."',is_work='".$args['is_work']."',holiday_time='".$args['holiday_time']."' where order_type=".$order_type." AND processor_id in('".$arr_ids."')";

    	}else{
    		$sql = "update ".$this->table()." set is_work='".$args['is_work']."',holiday_time='".$args['holiday_time']."' where order_type=".$order_type." AND processor_id in('".$arr_ids."')";

    	}
    	return $this->db()->query($sql);

    }

    /*
	*通过供应商的ID等钻后加时信息
	*
	*/
	public function getProcessorInfoByProId($pro_id,$order_type=''){
		if($order_type ==''){
			$sql ="select * from ".$this->table()." where processor_id='".$pro_id."';";
		}else{
			$sql ="select * from ".$this->table()." where processor_id='".$pro_id."' AND order_type='".$order_type."'";
		}
		return $this->db()->getRow($sql);
	}


	/*
	*通过多个供应商的ID获取所有信息
	*
	*/
	public function getAllInfoByPrc_id($pro_id){
		$sql ="select * from ".$this->table()." where processor_id in('".$pro_id."')";
		return $this->db()->getAll($sql);

	}

	/*
	* 只能保持两条数据
	*
	*/
	public function isExists($pro_id){

		$sql = "select count(1) from ".$this->table()." where  processor_id =".$pro_id;
		return $this->db()->getOne($sql);
	}


	/*
	* 检测是否有客订单
	*
	*/
	public function isExistsOrder1($pro_id){
		$sql = "select count(1) from ".$this->table()." where order_type=1 AND processor_id =".$pro_id;
		return $this->db()->getOne($sql);
	}

	/*
	* 检测是否有备货单
	*
	*/
	public function isExistsOrder2($pro_id){
		$sql = "select count(1) from ".$this->table()." where order_type=2 AND processor_id =".$pro_id;
		return $this->db()->getOne($sql);
	}

	/*
	*通过供应商ID删除所有加工时间
	*
	*/
	public function delAllInfoById($pro_id){
		$sql ="delete from ".$this->table()." where processor_id=".$pro_id;
		return $this->db()->query($sql);
	}

	/*
	*通过供应商ID和订单类型取加工信息
	*
	*/

	public function getProcessorInfoByTypeAndId($prc_id,$order_type=1){
		$sql = "select * from ".$this->table()." where processor_id=".$prc_id." AND order_type=".$order_type;
		return $this->db()->getRow($sql);
	}


	
	/*
	 *更加供应商ID更新出厂时间(等钻后工厂操作时间比较)
	 *
	 */
	public function getEsmttimeById($id){
	
		$newmodel = new AppProcessorWorktimeModel(14);
		$productModel = new ProductInfoModel($id,14);
		$from_type = $productModel->getValue('from_type');
		$order_type=$from_type==1?2:1;
		$stylemodel = new StyleModel(11);
		$purchasemodel = new PurchaseModel(23);
		$proInfos = $productModel->getBuChanInfoById($id);
		$infos = $newmodel->getProcessorInfoByProId($proInfos['prc_id'],$order_type);
		$behind_wait_dia = !empty($infos['behind_wait_dia'])?$infos['behind_wait_dia']:0;
		//更新出厂时间:未出厂 && 出厂时间大于当前时间
		$qiban_exists = $purchasemodel->getQiBanInfosByStyle_Sn($proInfos['style_sn'],$proInfos['p_sn']);
		if($order_type ==1){
			
			if($proInfos['qiban_type']==0){
				$cycle = $infos['wkqbzq'];
			}elseif($proInfos['qiban_type']==1){
				$cycle = $infos['ykqbzq'];
			}else{
				$cycle = $infos['normal_day'];
			}
		}else{
			//备货单
			$is_style = $purchasemodel->getStyleInfoByCgd($proInfos['p_sn']);
			if($is_style ==1){
				//采购列表  --有款采购
				$cycle = $infos['ykqbzq'];
			}elseif($is_style ==0){
				//采购列表  --无款采购
				$cycle = $infos['wkqbzq'];
			}else{
				//采购列表  --标准采购
				$cycle = $infos['normal_day'];
			}
		}
	
	
		if(!empty($cycle)){
			$order_time = strtotime($proInfos['order_time']);
			for($i=1;$i<=$cycle;$i++){
				$day = date('Y-m-d',strtotime('+'.$i.' day',$order_time));
				//放假日期
				if(strpos($infos['holiday_time'],$day) !== false){
					++$cycle;
					continue;
				}
				//暂时只能获得周末休息天数(默认周天休息)
				switch ($infos['is_rest']) {
					case '1':
						break;
					case '2':
						if(date('w',strtotime($day))== 0){
							$cycle = $cycle+1;
						}
						break;
					default:
						if(date('w',strtotime($day))== 6 || date('w',strtotime($day))== 0){
							$cycle = $cycle+1;
						}
						break;
				}
				//周末上班
				if(strpos($infos['is_work'],$day) !== false && strpos('60',date('w',strtotime($day))) !== false){
					--$cycle;
				}
			}
		}
	
		if(!empty($behind_wait_dia)){
			for($i=1;$i<=$behind_wait_dia;$i++){
				$new_day = date('Y-m-d',strtotime('+'.$i.' day',time()));
				//放假日期
				if(strpos($infos['holiday_time'],$new_day) !== false){
					++$behind_wait_dia;
					continue;
				}
				//暂时只能获得周末休息天数(默认周天休息)
				switch ($infos['is_rest']) {
					case '1':
						break;
					case '2':
						if(date('w',strtotime($new_day))== 0){
							$behind_wait_dia = $behind_wait_dia+1;
						}
						break;
					default:
						if(date('w',strtotime($new_day))== 6 || date('w',strtotime($new_day))== 0){
							$behind_wait_dia = $behind_wait_dia+1;
						}
						break;
				}
				//周末上班
				if(strpos($infos['is_work'],$new_day) !== false && strpos('60',date('w',strtotime($new_day))) !== false){
					--$behind_wait_dia;
				}
			}
		}
		
		
		$esmt_time = max($new_day,$day);
		return $esmt_time;	
		
		
	
		
	}
	
	function getEsmttimeByIdOnWait($id){
		$salesmodel = new SalesModel(27);
		$WarehouseModel = new WarehouseModel(21);
		$attrmodel = new ProductInfoAttrModel(13);
		$model_pw = new AppProcessorWorktimeModel(13);
		$diamodel = new DiamondModel(20);
		$model = new ProductInfoModel($id,13);
		$pro_id = $model->getValue('prc_id');
		$order_sn = $model->getValue('p_sn');
		$style_sn = $model->getValue('style_sn');
		$from_type = $model->getValue('from_type');
		$order_type=($from_type==1)?2:1;	//数据表存储不一样
		$goodsinfos = $salesmodel->getStockGoodsByOrderSn($order_sn,$style_sn);
		//通过货号在商品列表中找到即为现货
		$is_exists = $WarehouseModel->isExistsByGoodsId($goodsinfos['goods_id']);
		$cert_id =$attrmodel->getCertNumById($id);
		$infos = $model_pw->getProcessorInfoByTypeAndId($pro_id,$order_type);
		if(empty($goodsinfos['goods_id']) && empty($cert_id)){
			//货号和证书号都为空，就是现货
			$order_time = $model->getValue('order_time');
			
			//获得现货等钻加时、假期、周末上班天数、周末休息时间
			$cycle = intval($infos['now_wait_dia']);
		
		}else{
			if($is_exists){
				//现货：现货等钻加时更新出厂时间
				
				//获得现货等钻加时、假期、周末上班天数、周末休息时间
				$cycle = intval($infos['now_wait_dia']);
		
			}else{
				//货号没找到，通过证书号去裸钻列表查找判断是期货还是现货
				$cert_id2 = preg_replace('/[a-zA-Z]{0,10}/', '', $cert_id);
				$goods_type = $diamodel->getGoodsTypeByCertId($cert_id,$cert_id2);
				if($goods_type==2){
					//期货
					$pro_id = $model->getValue('prc_id');
					
					//获得现货等钻加时、假期、周末上班天数、周末休息时间
					$cycle = intval($infos['wait_dia']);
				}else{
					//现货
					$pro_id = $model->getValue('prc_id');
					
					//获得现货等钻加时、假期、周末上班天数、周末休息时间
					$cycle = intval($infos['now_wait_dia']);
				}
			}
		}
		//遇到放假出厂时间往后延(周末时间做不到,默认单休为周六)
		if(!empty($cycle)){
			for($i=1;$i<=$cycle;$i++){
				$day = date('Y-m-d',strtotime('+'.$i.' day',time()));
				//放假休息
				if(strpos($infos['holiday_time'],$day) !== false){
					//有假期就延后一天
					++$cycle;
					continue;
		
				}
				//暂时只能获得周末休息天数
				switch ($infos['is_rest']) {
					case '1':
						break;
					case '2':
						//有周末就后延后一天
						if(date('w',strtotime($day))== 0){
							$cycle = $cycle+1;
						}
						break;
					default:
						if(date('w',strtotime($day))== 6 || date('w',strtotime($day))== 0){
							$cycle = $cycle+1;
						}
						break;
				}
				//周末上班
				if(strpos($infos['is_work'],$day) !== false && strpos('60',date('w',strtotime($day))) !== false){
					--$cycle;
				}
					
			}
    
          return $day;
	   }else{
           return date('Y-m-d',time());
        }

     

	}

}
?>