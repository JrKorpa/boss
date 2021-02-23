<?php
/**
 * 仓库数据模块的模型（代替WareHouse/Api/api.php）
 *  -------------------------------------------------
 *   @file		: WareHouseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class SelfProductInfoModel extends SelfModel
{
    protected $db;
	function __construct ($strConn="")
	{
		$this->db = DB::cn($strConn);
	}
	public function db(){
	    return $this->db;
	}
	
	/**
	 *给予渠道id返回对应的实体信息
	 * @param $channel_id
	 * return array
	 */
	public function getChannelByOwnId($channel_id){
		$sql = "SELECT `channel_own_id`,`channel_type` FROM `sales_channels` WHERE `id`=".$channel_id;
		$res = $this->db()->getRow($sql);
	
		$ret = array();
		if($res['channel_type'] == 2){
			$table = 'shop_cfg';//体验店表
			$name = '`short_name`,`shop_address`,`country_id`,`province_id`,`city_id`,`regional_id`,`id`,`shop_name`,`shop_type`,`shop_phone`';
	
			$sql1 = "SELECT ".$name." FROM `".$table."` WHERE `id`=".$res['channel_own_id'];
			$ret = $this->db()->getRow($sql1);
			if(!$ret){
				$list = array();
				$list['short_name']='';
				$list['shop_address']='';
				$list['country_id']=0;
				$list['province_id']=0;
				$list['city_id']=0;
				$list['regional_id']=0;
				$list['id']='';
				$list['shop_name']='';
				$list['shop_type']='';
				$list['shop_phone']='';
				return $list;
			}else{
				return $ret;
			}
		}
		else{
			$list = array();
			$list['short_name']='';
			$list['shop_address']='';
			$list['country_id']=0;
			$list['province_id']=0;
			$list['city_id']=0;
			$list['regional_id']=0;
			$list['id']='';
			$list['shop_name']='';
			$list['shop_type']='';
			$list['shop_phone']='';
			return $list;
		}
	}

	public  function  getSatausById($id){
		$sql="select status from product_info where id=$id";
		return $this->db->getOne($sql);
	}
	
	
	public function updateBcStatus($order_sn){
		$sql="select id,bc_sn,status from product_info where p_sn='{$order_sn}'";
		$rows=$this->db->getAll($sql);
		if(empty($rows)){return false;}
		//更新订单对应的布产单 布产状态是初始化\已分配\不需布产 为已取消
        $sql1="update product_info set status = 10 where p_sn='{$order_sn}' and status in (1,3,11)";
        $rel=$this->db->query($sql1);
		if(!$rel){return false;}
		
		$uid = $_SESSION['userId'];
		$uname = $_SESSION['userName'];
		$time = date("Y-m-d H:i:s");
		foreach($rows as $v){
			if(in_array($v['status'],array(1,3,11))){
				$bc_id=$v['id'];
				$status=10;
				$remark="订单{$order_sn}关闭成功，布产单{$v['bc_sn']}未接单自动取消";
				//添加布产日志
				$sql2="insert into product_opra_log (bc_id,status,remark,uid,uname,time) values ({$bc_id},{$status},'{$remark}',{$uid},'{$uname}','{$time}')";
				$this->db->query($sql2);
			}
		}
		return true;
	}
	
	
	public function updateBcStatusById($id,$remark){
		$sql="select id,bc_sn,status from product_info where id='{$id}'";
		$rows=$this->db->getRow($sql);
		if(empty($rows)){return false;}
		$sql1="update product_info set status = 10 where id={$id} and status in (1,2)";
        $rel=$this->db->query($sql1);
		if(!$rel){return false;}
		
		$uid = $_SESSION['userId'];
		$uname = $_SESSION['userName'];
		$time = date("Y-m-d H:i:s");
		$bc_id=$rows['id'];
		$status=10;	
		$remark=$remark.",布产单{$rows['bc_sn']}取消";
		//添加布产日志
		$sql2="insert into product_opra_log (bc_id,status,remark,uid,uname,time) values ({$bc_id},{$status},'{$remark}',{$uid},'{$uname}','{$time}')";
		$rel2=$this->db->query($sql2);
		if(!$rel2) return false;
		
		return true;
	}
	
	
	
	  /**
     * 验证订单期货商品是否已布产
     * @param int $goods_id 商品明细id
     * @param string $order_sn 订单号
     * @return array 布产明细
     */
	public function CheckGoodsProductInfo($good_id,$order_sn) {
		
		$where = '';
		$sql = "SELECT `gr`.* FROM `product_goods_rel` as `gr`,`product_info` as `pi` WHERE `gr`.`bc_id`=`pi`.`id` and `gr`.`status` = 0 ";
	
		
		if (!empty($good_id)) {
			$sql .= " and `gr`.`goods_id` = '{$good_id}'";
		}
		if (!empty($order_sn)) {
			$sql .= " and `pi`.`p_sn` = '{$order_sn}'";
		}
	
		$sql .= " ORDER BY `id` DESC";
	
		$data = $this->db->getRow($sql);
	    return $data;    
	
	}
	
	
	
	
	/**
	 * 布产列表添加一条数据
	 * 这个接口涉及到订单布产和采购布产两方面的数据来源，所以接口调整后，其他两个地方务必都要调整
	 *  JUAN
	 */
	public function AddProductInfo($info,$from_type=2) {
		
		$arr = array();//二位数组用来存放 订单明细id 、布产号 返回值
		$_id = 0;
		$order_bc_event_data = array();
		$CStyleModel = new CStyleModel(11); //跨模块款书库类
		if (!empty($info)) {			
			$num = count($info);
			if ($num > 0) {
				
				//事务添加数据，如果处理错误则返回false
				define("root_path",dirname(dirname(dirname(dirname(__FILE__)))));
				include_once(root_path.'/frame/init.php');
				$pdo = DB::cn(14)->db();//pdo对象
				try{
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
					$pdo->beginTransaction();//开启事务
					$time=date('Y-m-d H:i:s');
					foreach ($info as $key => $value){

						$is_peishi = 0;
                        $cs_id = $value['customer_source_id'];//客户来源
                        $djbh_bc = $cs_id == 2946 ? 'EC' : '';//boss_1246
						//EDITBY ZHANGRUIYIGN
                        $attrKeyVal = array_column($value['attr'],'value','code');					
						if($from_type == 2 && !empty($value['p_sn']) && !isset($value['caigou_info'])){
                            $sql = "select order_remark from app_order.base_order_info where order_sn='{$value['p_sn']}'";
                            $orderInfo = $this->db->getRow($sql);
                            if(!empty($orderInfo)){
                                $value['caigou_info'] = $orderInfo['order_remark'];
                            }
                        }
						$value['caigou_info']=isset($value['caigou_info'])?$value['caigou_info']:'';
						$value['create_user'] = !empty($value['create_user'])?$value['create_user']:$_SESSION['userName'];
						$value['prc_id']=isset($value['prc_id'])?$value['prc_id']:0;
						$value['prc_name']=isset($value['prc_name'])?$value['prc_name']:'';
						$value['opra_uname']=isset($value['opra_uname'])?$value['opra_uname']:'';
						$value['is_alone']=isset($value['is_alone'])?$value['is_alone']:0;
						$value['style_sn']=trim($value['style_sn']);
						$value['status']=!empty($value['prc_id'])?3:1;
						$value['qiban_type']=isset($value['qiban_type'])?$value['qiban_type']: 2;
						$value['diamond_type']=!empty($value['diamond_type'])?$value['diamond_type']:0;
						$value['origin_dia_type']=!empty($value['origin_dia_type'])?$value['origin_dia_type']:0;
						$value['to_factory_time']=!empty($value['to_factory_time'])?$value['to_factory_time']:'0000-00-00 00:00:00';
						$value['is_quick_diy'] = isset($value['is_quick_diy'])?$value['is_quick_diy']: 0;
						$sql = "INSERT INTO `product_info`(`bc_sn`, `p_id`, `p_sn`, `style_sn`, `status`, `num`, `prc_id`,`prc_name`, `opra_uname`, `add_time`, `edit_time`, `info`,`from_type`,`consignee`,`bc_style`,`goods_name`,`xiangqian`,`customer_source_id`,`channel_id`,`caigou_info`,`create_user`,`is_alone`,`qiban_type`,`diamond_type`,`origin_dia_type`,`to_factory_time`,`is_quick_diy`) VALUES ('',{$value['p_id']},'{$value['p_sn']}','{$value['style_sn']}',{$value['status']},{$value['num']},{$value['prc_id']},'{$value['prc_name']}','{$value['opra_uname']}','{$time}','{$time}','{$value['info']}',{$from_type},'{$value['consignee']}','{$value['bc_style']}','{$value['goods_name']}','{$value['xiangqian']}','{$value['customer_source_id']}','{$value['channel_id']}','{$value['caigou_info']}','{$value['create_user']}','{$value['is_alone']}','{$value['qiban_type']}','{$value['diamond_type']}','{$value['origin_dia_type']}','{$value['to_factory_time']}',{$value['is_quick_diy']})";
						//EDIT END
						//file_put_contents("./u223.txt",$sql."\r\n",FILE_APPEND);
						$pdo->query($sql);
						$_id = $pdo->lastInsertId();
	
						//订单中只要有4C销售裸钻配石信息入库
						$is_peishi = isset($value['is_peishi'])?$value['is_peishi']:0;
						if($is_peishi==1){
							if(!is_array($value['diamond'])){
								$pdo->rollback();//事务回滚
								$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
								$row['error']=1;
								$row['data']= "4C裸钻基本信息为空";
								return $row;
							}
							$is_peishi =1;
							$d = $value['diamond'];
							$d['chengben_jia'] = !empty($d['chengben_jia'])?$d['chengben_jia']:0;
							$d['source_discount'] = !empty($d['source_discount'])?$d['source_discount']:0;
							$fileds ="`id`,`order_sn`,`zhengshuhao`,`zhengshuhao_org`,`price_org`,`price`,`discount_org`,`discount`,`color`,`carat`,`shape`,`clarity`,`cut`,`peishi_status`";
							$values ="{$_id},'{$value['p_sn']}','{$d['cert_id']}','{$d['cert_id']}','{$d['chengben_jia']}','{$d['chengben_jia']}','{$d['source_discount']}','{$d['source_discount']}','{$d['color']}','{$d['carat']}','{$d['shape']}','{$d['clarity']}','{$d['cut']}',0";
							$sql = "INSERT INTO `product_info_4c`({$fileds}) VALUES ($values)";
							$pdo->query($sql);
						}
	
						$arr[$key]['id'] = $value['p_id'];
						$arr[$key]['buchan_sn'] = $_id;
						$arr[$key]['final_bc_sn'] = '';
					    if ($from_type == '2') {
						    $bc_sn = $this->create_bc_sn($value['p_sn'], $_id);
						    $arr[$key]['final_bc_sn'] = $bc_sn;
						    //抓取订单其他为传递的必要属性
						    $sql ="select xiangkou,cert from app_order.app_order_details where id={$value['p_id']}";
						    $orderDetail = $this->db->getRow($sql);
					        if(!empty($orderDetail)){
					            if(!array_key_exists('zhushi_num',$attrKeyVal)){
					                $value['attr'][] = array('code'=>'zhushi_num','name'=>'主石粒数','value'=>$orderDetail['zhushi_num']);
					            }
    						    if(!array_key_exists('xiangkou',$attrKeyVal)){
    						        $value['attr'][] = array('code'=>'xiangkou','name'=>'镶口','value'=>$orderDetail['xiangkou']);
    						    }
    						    if(!array_key_exists('cert',$attrKeyVal)){
    						        $value['attr'][] = array('code'=>'cert','name'=>'证书类型','value'=>$orderDetail['cert']);
    						    }
						    }
						}
						$pdo->query("UPDATE `product_info` SET bc_sn = '".$bc_sn."',is_peishi=".$is_peishi." WHERE id =".$_id);
						//获取款式主石，副石相关属性列表
						$attrExt = $CStyleModel->getStoneAttrList($value['style_sn'],$value['attr']);
						if(!empty($attrExt)){
						    $value['attr'] = array_merge($value['attr'],$attrExt);
						}
						//$logss =  var_export($value['attr'],true);
						//file_put_contents('buchan2.txt',$logss);
						//插入属性
						$t = "";
						foreach($value['attr'] as $k => $v)
						{
							$sql = "INSERT INTO `product_info_attr`(`g_id`, `code`, `name`, `value`) VALUES (".$_id.",'".$v['code']."','".$v['name']."','".$v['value']."')";
							$t .= $sql;
							$pdo->query($sql);
						}
						//file_put_contents("/data/www/cuteframe_boss/apps/processor/logs/u223.txt",$t."\r\n",FILE_APPEND );
						//插入布产表后增加一条日志
						$remark = "系统自动生成布产单：".$bc_sn."，来源单号：".$value['p_sn'];
						$sql = "INSERT INTO `product_opra_log`(`bc_id`, `status`, `remark`, `uid`, `uname`, `time`) VALUES ({$_id},{$value['status']},'{$remark}',0,'{$value['create_user']}','{$time}')";
						$pdo->query($sql);
						//file_put_contents("/data/www/cuteframe_boss/apps/processor/logs/u223.txt",$sql."\r\n",FILE_APPEND );
						//如果是订单来源的布产单，插入数据到布产和货品关系表中
						if($from_type == 2){
							$sql = "INSERT INTO `product_goods_rel`(`bc_id`,`goods_id`) VALUES (".$_id.",".$value['p_id'].")";
							$pdo->query($sql);
							//file_put_contents("/data/www/cuteframe_boss/apps/processor/logs/u223.txt",$sql."\r\n",FILE_APPEND );
							$order_bc_event_data[$_id] = $value['p_id'];
							
						}
					}
					$pdo->commit();//如果没有异常，就提交事务
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				}
				catch(Exception $e){//捕获异常
					//  print_r($e);exit;
					$pdo->rollback();//事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					$row['error']=1;
					$row['data']= "数据异常，推送布产信息失败。".$sql;
					return $row;
				}
			}else{
				$row['error']=1;
				$row['data']= "insert_data是个空数组";
				return $row;
			}
		}else{
			$row['error']=1;
			$row['data']="缺少参数";
			return $row;
		}
	
		//返回信息
		$row['error']=0;
		$row['data']= $arr;
		
		//AsyncDelegate::dispatch('buchan', array('event' => 'order_bcd_upserted', 'bc_infos' => $order_bc_event_data));
	    return $row;
	}	
	
	/**
	 * 订单货品和布产单关系 根据布产号id查询是否有绑定关系
	 */
	public function GetGoodsRelInfo($good_id,$bc_id='') {
		$where = '';
		$sql = "SELECT * FROM `product_goods_rel` WHERE 1 and status =0 ";
	
		if (!empty($bc_id)) {
			$sql .= " and `bc_id` = '{$bc_id}'";
		}
	
		if (!empty($good_id)) {
			$sql .= " and `goods_id` = '{$good_id}'";
		}
	
		$sql .= " ORDER BY `id` DESC";
	
		return $data = $this->db->getRow($sql);
	
		
	   
	}
	
	private function create_bc_sn($order_sn, $bc_id) {
	    $bc_sn = BCD_PREFIX.$bc_id;
	    if (SYS_SCOPE == 'boss') {
	        $sql = "select sc.channel_class from app_order.base_order_info a inner join cuteframe.sales_channels sc on sc.id = a.department_id where a.order_sn = '{$order_sn}'";
	        $channel = $this->db->getOne($sql);
	        if ($channel == '1') {
	            return 'DS'.$bc_sn;
	        } else if ($channel == '2') {
	            return 'MD'.$bc_sn;
	        }
	    }
	    if(SYS_SCOPE == 'zhanting'){
	        $sql = "select c.company_type from app_order.base_order_info a inner join cuteframe.sales_channels sc on sc.id = a.department_id left join cuteframe.company c on sc.company_id=c.id where a.order_sn = '{$order_sn}'";
	        $company_type = $this->db->getOne($sql);
	        if ($company_type == '2') {
	            return BCD_PREFIX.'TGD'.$bc_id;
	        } else if ($company_type == '3') {
	            return BCD_PREFIX.'JXS'.$bc_id;
	        }
	    }
	    
	    return $bc_sn;
	}
	/**
	 * 获取布产单详情技术性信息
	 * @param unknown $id
	 * @return boolean|unknown
	 */
	public function getBuchanInfo($bc_id){
	    $sql = "select * from product_info where id={$bc_id}";
	    $row = $this->db()->getRow($sql);
	    if(empty($row)){
	        return false;
	    }
	    $sql = "select * from product_info_attr where g_id={$bc_id}";
	    $all = $this->db()->getAll($sql);
	    $attrs = array_column($all,'value','code');
	    if(!empty($attrs)){
	        $row = array_merge($row,$attrs);
	    }
	    return $row;	    
	}
	/**
	 * 查询正在申请中的布产单
	 * @param unknown $bc_id
	 */
	public function getProductApplyByDetailId($detail_id,$apply_status=0){
	    $sql = "select * from product_apply_info where detail_id={$detail_id} and apply_status={$apply_status} order by id desc";
	    return $this->db()->getRow($sql);
	}
	
    /**
     * 添加布产修改信息     
     */
    public function addProductApplyInfo($newdo){        
        $sql = $this->insertSql($newdo,'product_apply_info');
        //$sql = str_replace('"', '\\"',$sql);
        //echo $sql;exit;
        return $this->db()->query($sql);
    }


    //根据条件（款号+材质+材质颜色+指圈+镶口）查找是否快速定制
    public  function get_app_style_quickdiy($where){
        if(empty($where)){
            $where = 1;
        }
        $sql = "select * from front.app_style_quickdiy  where status =1 AND $where ";
        $data = $this->db()->getRow($sql);
        return $data;
    }

}

?>