<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductApplyInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-17 16:30:13
 *   @update	:
 *  -------------------------------------------------
 */
class ProductApplyInfoModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'product_apply_info';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"order_sn"=>"订单号",
"bc_sn"=>"布产单号",
"detail_id"=>"订单明细ID",
"style_sn"=>"款号",
"apply_info"=>"申请修改属性信息",
"old_info"=>"原布产属性信息",
"apply_status"=>"申请状态:0=未操作,1=同意,2=拒绝",
"factory_status"=>"工厂接受状态：1=未接受,2=已接受",
"factory_time"=>"工厂接受时间",
"goods_status"=>"布产状态",
"apply_id"=>"申请人ID",
"apply_name"=>"申请人姓名",
"apply_time"=>"申请时间",
"check_id"=>"审核人ID",
"check_name"=>"审核人姓名",
"check_time"=>"审核时间",
"refuse_remark"=>"拒绝理由",
"special"=>"特别要求");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ProductApplyInfoController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
                $sql = "select pi.`id` as bc_id,pi.`consignee` as consignee, pi.`bc_sn`,pi.`status`,pi.`prc_name`,pi.`info`,pa.`id`,pa.`order_sn`,pa.`style_sn`,pa.`apply_info`,pa.`apply_status`,pa.`apply_name`,pa.`apply_time`,pa.`check_name`,pa.`check_time`,pa.`refuse_remark`,pa.`special`,pi.channel_id FROM `product_apply_info` as pa,`product_info` as pi,product_goods_rel as r where pa.`detail_id`=r.`goods_id` and r.bc_id=pi.id ";
		$str = ' 1=1 ';
		if(isset($where['style_sn']) && $where['style_sn'] != "")
		{
			$str .= " and pa.`style_sn` like \"%".addslashes($where['style_sn'])."%\" ";
		}
		if(isset($where['order_sn']) and !empty($where['order_sn']))
		{
			$str .= " AND pa.`order_sn`='".$where['order_sn']."' ";
		}
                if(isset($where['bc_sn']) and !empty($where['bc_sn'])) {
                        $str .= "  AND pi.`bc_sn`='".$where['bc_sn']."' ";
                }
                if(isset($where['apply_status']) and $where['apply_status']!=='') {
                        $str .= " AND  pa.`apply_status`='".$where['apply_status']."' ";
                }
                if(isset($where['time_start']) and !empty($where['time_start'])) {
                        $str .= "  AND pa.`apply_time`>='".$where['time_start']." 00:00:00' ";;
                }
                if(isset($where['time_end']) and !empty($where['time_end'])) {
                        $str .= " AND  pa.`apply_time`<='".$where['time_end']." 23:59:59 ' ";
                }
                if(isset($where['processor']) and !empty($where['processor'])) {
                        $str .= "  AND pi.`prc_id`='".$where['processor']."' ";
                }
                if(isset($where['factory_status']) and !empty($where['factory_status'])) {
                        $str .= " AND  pa.`factory_status`='".$where['factory_status']."' ";
                }
                if(isset($where['buchan_status']) and !empty($where['buchan_status'])) {
                        $str .= "  AND pi.`status`='".$where['buchan_status']."' ";
                }
		if($str)
		{

			$sql .= " AND ". $str;
		}

		$sql .= " ORDER BY pa.`id` DESC";
                //echo $sql;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);

		return $data;
	}
        //获取姓名
        public function getConsignee($order_sn) {

            $ret = ApiModel::sales_api(array("order_sn"), array($order_sn), "GetOrderInfoByOrdersn");
            return $ret;
        }

        //获取部门id
        public function getDepartmentId($order_sn) {
           $ret = ApiModel::sales_api(array("order_sn"), array($order_sn), "GetOrderInfoByOrdersn");
           if($ret['error']){
           	return 0;
           }
            return $ret['return_msg']['data'][0]['department_id'];
        }
        
    public function saveProductAttrData($bc_id,$newdo,$olddo){
        
        $result = array('success'=>0,'error'=>'','data'=>array());
        
        $olddo = array_column($olddo,'value','code');        
        $bc_zhengshuhao_changed = false;
        $bc_diy_dependency_changed = false;
        $zhengshuhao = '';
        try{        
            //布产类型 bc_type,镶嵌要求 xiangqian,是否支持4C配石is_peishi
            $fitler = array('bc_type','xiangqian','is_peishi');
            foreach ($newdo as $v) {
                if(in_array($v['code'],$fitler)){
                    if($v['code'] == 'bc_type'){
                        $v['code'] = 'bc_style';
                    }
                    $sql = "UPDATE `product_info` SET `".$v['code']."` = '".$v['value']."' WHERE `id` = ".$bc_id;
                    $this->db()->query($sql);
                }elseif(array_key_exists($v['code'],$olddo)){
                    $sql = "UPDATE `product_info_attr` SET `value`='".$v['value']."',`name`='".$v['name']."' WHERE `g_id`=".$bc_id." AND `code` = '".$v['code']."'";
                    $this->db()->query($sql);
                }else{
                    $sql = "INSERT INTO `product_info_attr` (`g_id`,`code`,`name`,`value`) VALUES (".$bc_id.",'".$v['code']."','".$v['name']."','".$v['value']."')";
                    $this->db()->query($sql);
                }
        
                if ($v['code'] == 'zhengshuhao') {
                    $bc_zhengshuhao_changed = true;
                    $zhengshuhao = $v['value'];
                } else {
                    $bc_diy_dependency_changed = true;
                }
            }
            /*--------------*/
            $result['success'] = 1;
            $result['data'] = array(
                'bc_zhengshuhao_changed'=>$bc_zhengshuhao_changed? $zhengshuhao: false,
                'bc_diy_dependency_changed'=>$bc_diy_dependency_changed
            );
            return $result;
        }
        catch(Exception $e){
            $result['error'] = "更新失败:".$e->getMessage();
            return $result;
        }
        //$pdo->commit();
        //$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);        
    }    
	/**
	 * 修改布产属性
	 */
	public function mkDataTOAttr($info,$bc_id){

		$view = new ProductApplyInfoView($this);
		$attr = $view->get_attr($bc_id);//原布产属性所有属性
		
		$olddo = array_column($attr,'value','code');
		/*-------------*/
		$pdo = $this->db()->db();
		$bc_zhengshuhao_changed = false;
		$bc_diy_dependency_changed = false;
		$zhengshuhao = '';
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);
			$pdo->beginTransaction();

			//布产类型 bc_type,镶嵌要求 xiangqian,是否支持4C配石is_peishi
			$fitler = ['bc_type','xiangqian','is_peishi'];
			foreach ($info as $v) {
				if(in_array($v['code'],$fitler)){
					if($v['code'] == 'bc_type'){$v['code'] = 'bc_style';}
					$sql = "UPDATE `product_info` SET `".$v['code']."` = '".$v['value']."' WHERE `id` = ".$bc_id;
					$this->db()->query($sql);
				}elseif(array_key_exists($v['code'],$olddo)){
				    $sql = "UPDATE `product_info_attr` SET `value`='".$v['value']."' WHERE `g_id`='".$bc_id."' AND `code` = '".$v['code']."'";
					$this->db()->query($sql);
				}else{
					$sql = "INSERT INTO `product_info_attr` (`g_id`,`code`,`name`,`value`) VALUES ('".$bc_id."','".$v['code']."','".$v['name']."','".$v['value']."')";
					$this->db()->query($sql);
				}
				
				if ($v['code'] == 'zhengshuhao') {
				    $bc_zhengshuhao_changed = true;
				    $zhengshuhao = $v['value'];
				} else {
					$bc_diy_dependency_changed = true;
				}
			}
		/*--------------*/
		}
		catch(Exception $e){
			//$pdo->rollback();
			//$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
			return false;
		}
		//$pdo->commit();
		//$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
		
		return array($bc_zhengshuhao_changed ? $zhengshuhao: false,  $bc_diy_dependency_changed);
		/*---------------*/
	}

	/**
	 * 布产修改通过日志
	 */
	public function getProductLog(){
		$view = new ProductApplyInfoView($this);
		$info = '[订单号'.$view->get_order_sn().']'.$view->get_apply_name().'->申请修改;&nbsp;布产号:'.$view->get_bc_sn();
		$info .= '['.$_SESSION['realName'].'->审核通过;]<br/>';

		$newInfo = $view->get_apply_info();
		$oldInfo = $view->get_old_info();
		
		if($oldInfo[0]['code'] == 'id'){unset($oldInfo[0]);}
		$newInfo = array_column($newInfo,'value','name');
		$oldInfo = array_column($oldInfo,'value','name');
		$c_info = '';
		foreach ($newInfo as $key=>$vo){
		    if(isset($oldInfo[$key]) && $vo<>$oldInfo[$key]){
		        $c_info .= $key." 由【{$oldInfo[$key]}】改为【{$vo}】<br/>";
		    }
		}
		$info = ($c_info)?"修改属性：".$c_info:"无修改信息";
		return $info;
		/* print_r($newInfo);
		print_r($oldInfo);
		$change = array_diff($newInfo,$oldInfo);
		print_r($change);
		$c_info = '';
		if(!empty($change)){
			foreach ($change as $k => $v) {
				if(isset($oldInfo[$k])){
					//$c_info .= '【修改属性：'.$k.':修改为->'.$v.'】<br/>';
					//<span class="red">* </span>供应商编码：</label>
					$c_info .= "【修改属性：".$k.":->".$v."】<br/>";
				}else{
					$c_info .= '【新增属性：'.$k.'->'.$v.'】<br/>';
				}
			}
		}
		$info = ($c_info)?$c_info:"无修改信息";
		return $info; */
	}

	public function createProductLog($bc_id){
		$view = new ProductApplyInfoView($this);
		$log  = $this->getProductLog();
		$logModel = new ProductOpraLogModel(14);
		$res = $logModel->addLog($bc_id,"布产单审核通过".$log);

		//$sql = 'INSERT INTO `product_opra_log` (`bc_id`,`status`,`remark`,`uid`,`uname`,`time`) VALUES (';
		//$sql .= $bc_id.",".$view->get_goods_status().",'".$this->getProductLog()."',".$_SESSION['userId'].",'".$_SESSION['realName']."','".date('Y-m-d H:i:s')."')";
		//$res = $this->db()->query($sql);
		return $res;
	}
}

?>