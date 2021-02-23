<?php
/**
 * 生产模块 跨模块Model
 * C开头的Model 为 跨模块基类Model 可被不同模块下的 SelfProcessorModel继承 实现API共享
 *  -------------------------------------------------
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2017-05-12 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class CProcessorModel extends SelfModel
{
    protected $db;
	function __construct ($strConn="")
	{
		parent::__construct($strConn);
	}
	
    /**
    * 新增布产单
    * @param array $data 商品属性
    * @param unknown $from_type 单据类型 1采购单 2 布产单
    * @param string $transMode 是否自动使用事物
    * @return multitype:number string multitype: NULL Ambigous <multitype:, unknown>
    */
   public function addProductInfo($data,$from_type,$transMode = true) {
       		
        $CStyleModel = new CStyleModel(11); //跨模块款书库类
        $bc_id = 0;
        $result = array('success'=>0,'error'=>'','returnData'=>array());
        $returnData = array();//二位数组用来存放 订单明细id 、布产号 返回值       
        $order_bc_event_data = array();
        //特殊属性code值映射
        $attrCodeMaps = array('18k_color'=>'jinse','yanse'=>'color','zuanshidaxiao'=>'cart','g_name'=>'goods_name','bc_type'=>'bc_style');
        //特殊属性name值映射
        $attrNameMaps = array(
            'face_work'=>'表面工艺', 'caizhi'=>'材质', 'jinse'=>'金色', 'zhiquan'=>'指圈', 'cart'=>'主石单颗重','zhushi_num'=>'主石粒数', 'xiangkou'=>'镶口', 'cert'=>'证书类型','zhengshuhao'=>'证书号', 'color'=>'主石颜色', 'jingdu'=>'主石净度', 'kezi'=>'刻字', 'info'=>'布产备注'
        );
        $num = count($data);
        $pdo = $this->db()->db();//pdo对象
        try{
            if($transMode==true){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
                $pdo->beginTransaction();//开启事务
                $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            }
			$time=date('Y-m-d H:i:s');
            foreach ($data as $key => $value){
                $is_peishi = 0;
                //$djbh_bc = $value['t_id'] == 9 ? 'EC' : '';//boss_1246
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
			    $sql="select * from product_info where p_id='{$value['p_id']}' and p_sn='{$value['p_sn']}'";
                $product_info_row = $this->db->getRow($sql);
                if(!empty($product_info_row)){
                    if($transMode==true){
                        $pdo->rollback();//事务回滚
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                    }
                    $result['error'] = '布产单已经存在,疑似重复提交';
                    return $result; //TODO
                    break;
                }
            	$sql = "INSERT INTO `product_info`(`bc_sn`, `p_id`, `p_sn`, `style_sn`, `status`, `num`, `prc_id`,`prc_name`, `opra_uname`, `add_time`, `edit_time`, `info`,`from_type`,`consignee`,`bc_style`,`goods_name`,`xiangqian`,`customer_source_id`,`channel_id`,`caigou_info`,`create_user`,`is_alone`,`qiban_type`,`diamond_type`,`origin_dia_type`,`to_factory_time`) VALUES ('',{$value['p_id']},'{$value['p_sn']}','{$value['style_sn']}',{$value['status']},{$value['num']},{$value['prc_id']},'{$value['prc_name']}','{$value['opra_uname']}','{$time}','{$time}','{$value['info']}',{$from_type},'{$value['consignee']}','{$value['bc_style']}','{$value['goods_name']}','{$value['xiangqian']}','{$value['customer_source_id']}','{$value['channel_id']}','{$value['caigou_info']}','{$value['create_user']}','{$value['is_alone']}','{$value['qiban_type']}','{$value['diamond_type']}','{$value['origin_dia_type']}','{$value['to_factory_time']}')";
			    //EDIT END
				//file_put_contents("./u223.txt",$sql."\r\n",FILE_APPEND);
                $pdo->query($sql);
                $bc_id = $pdo->lastInsertId();
                
                //订单中只要有4C销售裸钻配石信息入库
                $is_peishi = isset($value['is_peishi'])?$value['is_peishi']:0;
                if($is_peishi==1){
                    if(!is_array($value['diamond'])){
                         $pdo->query('');
                    }
                    $is_peishi =1;
                    $d = $value['diamond'];
                    $d['chengben_jia'] = !empty($d['chengben_jia'])?$d['chengben_jia']:0;
                    $d['source_discount'] = !empty($d['source_discount'])?$d['source_discount']:0; 
                    $fileds ="`id`,`order_sn`,`zhengshuhao`,`zhengshuhao_org`,`price_org`,`price`,`discount_org`,`discount`,`color`,`carat`,`shape`,`clarity`,`cut`,`peishi_status`";
                    $values ="{$bc_id},'{$value['p_sn']}','{$d['cert_id']}','{$d['cert_id']}','{$d['chengben_jia']}','{$d['chengben_jia']}','{$d['source_discount']}','{$d['source_discount']}','{$d['color']}','{$d['carat']}','{$d['shape']}','{$d['clarity']}','{$d['cut']}',0";
                    $sql = "INSERT INTO `product_info_4c`({$fileds}) VALUES ($values)";
                    $pdo->query($sql);
                }                        
                $bc_sn = BCD_PREFIX.$bc_id;
                
				$returnData[$key]['id'] = $value['p_id'];
				$returnData[$key]['buchan_sn'] = $bc_id;//布产ID
				
				if ($from_type == '2') {
				    $bc_sn = $this->createBcSn($value['p_sn'], $bc_id);
				    $order_bc_event_data[$bc_id] = $value['p_id'];
				    
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
                if ($from_type == '1' && SYS_SCOPE=='zhanting'){
                   if($value['t_id']=='5') //经销商备货
                        $bc_sn=BCD_PREFIX.'XBH'.$bc_id;      
                   if($value['t_id']=='10') //托管店备货
                        $bc_sn=BCD_PREFIX.'TBH'.$bc_id; 
                } 

				$returnData[$key]['final_bc_sn'] = $bc_sn;
				$returnData[$key]['final_bc_id'] = $bc_id;
				
                $pdo->query("UPDATE `product_info` SET bc_sn = '".$bc_sn."',is_peishi=".$is_peishi." WHERE id =".$bc_id);
                //获取款式主石，副石相关属性列表
                $attrExtend = $CStyleModel->getStoneAttrList($value['style_sn'],$value['attr']);
                if(!empty($attrExtend)){
                    $value['attr'] = array_merge($value['attr'],$attrExtend);
                }
                //插入属性
                foreach($value['attr'] as $k => $v)
                {                 
                    //特殊字段映射，主要针对 采购单 布产属性code不统一问题
                    $v['code'] = isset($attrCodeMaps[$v['code']])?$attrCodeMaps[$v['code']]:$v['code'];
                    //特殊字段映射，主要针对 采购单 布产属性name不统一问题
                    $v['name'] = isset($attrNameMaps[$v['code']])?$attrNameMaps[$v['code']]:$v['name'];
					$sql = "INSERT INTO `product_info_attr`(`g_id`, `code`, `name`, `value`) VALUES (".$bc_id.",'".$v['code']."','".$v['name']."','".$v['value']."')";
					$pdo->query($sql);
                }
				//file_put_contents("/data/www/cuteframe_boss/apps/processor/logs/u223.txt",$t."\r\n",FILE_APPEND );
                //插入布产表后增加一条日志
                $remark = "系统自动生成布产单：".$bc_sn."，来源单号：".$value['p_sn'];
                $sql = "INSERT INTO `product_opra_log`(`bc_id`, `status`, `remark`, `uid`, `uname`, `time`) VALUES ({$bc_id},{$value['status']},'{$remark}',0,'{$value['create_user']}','{$time}')";
                $pdo->query($sql);
				//file_put_contents("/data/www/cuteframe_boss/apps/processor/logs/u223.txt",$sql."\r\n",FILE_APPEND );
                //如果是订单来源的布产单，插入数据到布产和货品关系表中
                if($from_type == 2){
                    $sql = "INSERT INTO `product_goods_rel`(`bc_id`,`goods_id`) VALUES (".$bc_id.",".$value['p_id'].")";
                    $pdo->query($sql);
                    
                    //将布产单ID回写到订单明细
                    $sql = "update app_order.app_order_details set bc_id={$bc_id} where id={$value['p_id']}";
                    $pdo->query($sql);
                }   
            }
            if($transMode==true){
                $pdo->commit();//如果没有异常，就提交事务
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            }
            $result['success'] = 1;
            $result['returnData'] = $returnData;
                        
        } catch(Exception $e) {//捕获异常
            if($transMode==true){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            }   
            $result['error'] = $e->getMessage(); 
            
        }
        
        // 代码执行到这里，说明已经没有错误，根据布产单来源情况触发事件
        if (!empty( $order_bc_event_data) && $result['success'] ) {
            //AsyncDelegate::dispatch('buchan', array('event' => 'order_bcd_upserted', 'bc_infos' => $order_bc_event_data));
        }
        
        
        return $result;
    }
    /**
     * 生成布产编号
     * @param unknown $order_sn
     * @param unknown $bc_id
     * @return string
     */
    public function createBcSn($order_sn, $bc_id) {
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
     * 根据布产单ID更新标准金重-历史金重 
     * @param unknown $bc_id
     * @param string $transMode 是否自动使用事物
     */
    function upateJinzhongByBcID($bc_id,$transMode=true){
        $result = array('success'=>0,'error'=>'');        
        try{            
            if($transMode==true){
                $pdo = $this->db()->db();//pdo对象
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
                $pdo->beginTransaction();//开启事务
            }
            $sql="select p.bc_sn,s.style_sn,s.check_status,s.style_id,ifnull(sz.value,'') as stone,ifnull(xk.value,'') as xiangkou,ifnull(cz.value,'') as caizhi,ifnull(zq.value,'') as zhiquan,p.qiban_type 
     from kela_supplier.product_info p left join front.base_style_info s on p.style_sn=s.style_sn left join kela_supplier.product_info_attr sz on p.id=sz.g_id and sz.code='cart' left join kela_supplier.product_info_attr xk on p.id=xk.g_id and xk.code='xiangkou' left join kela_supplier.product_info_attr cz on p.id=cz.g_id and cz.code='caizhi' left join kela_supplier.product_info_attr zq on p.id=zq.g_id and zq.code='zhiquan'
     where s.check_status=3 and if(ifnull(sz.value,'')<>'',sz.value,ifnull(xk.value,''))<>'' and ifnull(zq.value,'')<>'' and ifnull(cz.value,'')<>'' and p.id='".$bc_id."'";
            //echo $sql;
            $res=$this->db()->getRow($sql);
            $row=null;
            $jingzhongmax='';
            $jingzhongmin='';
            if($res && in_array(trim($res['caizhi']),array('18K','PT950')) && is_numeric(trim($res['zhiquan']))){
                if($res['stone']<>''  && is_numeric(trim($res['stone'])) && $res['stone']>0){
                    $sql="select * from front.app_xiangkou where round(stone*1-0.05,3) <= ".trim($res['stone'])." and ".trim($res['stone'])."<= round(stone*1+0.04,3) and substring_index(finger,'-',1)*1 <= ".trim($res['zhiquan'])."  and ".trim($res['zhiquan'])." <= substring_index(finger,'-',-1)*1 and style_id='".$res['style_id']."' order by abs(stone-".trim($res['stone']).") limit 1";
                    //echo $sql;
                    $row=$this->db()->getRow($sql);
                    if(empty($row)){
                        $sql="select * from front.app_xiangkou where round(stone*1-0.05,3) <= ".trim($res['stone'])." and ".trim($res['stone'])."<= round(stone*1+0.04,3) and substring_index(finger,'-',1)*1 <= ".round(trim($res['zhiquan']),0)."  and ".round(trim($res['zhiquan']),0)." <= substring_index(finger,'-',-1)*1 and style_id='".$res['style_id']."' order by abs(stone-".trim($res['stone']).") limit 1";
                        //echo $sql;
                        $row=$this->db()->getRow($sql);
                    }
                    $sql="select att_value_name, round(att_value_name*1-0.05,3) as stonemin,round(att_value_name*1+0.04,3) as stonemax  from front.app_attribute_value where attribute_id=1 and att_value_status=1 and  round(att_value_name*1-0.05,3) <= ".trim($res['stone'])." and ".trim($res['stone'])." <= round(att_value_name*1+0.04,3) order by abs(".trim($res['stone'])."-att_value_name*1) limit 1";
                    $rowstone=$this->db()->getRow($sql);
                    if($rowstone)
                        $sql="select max(jinzhong) as lishi_jinzhong_max,min(jinzhong) as lishi_jinzhong_min from warehouse_shipping.warehouse_goods g where g.is_on_sale in (1,2,3,4,5,6,8,10,11) and goods_sn='".trim($res['style_sn'])."' and caizhi like '".trim($res['caizhi'])."%' and  shoucun='".trim($res['zhiquan'])."' and ((zuanshidaxiao >= ".$rowstone['stonemin']." and zuanshidaxiao <= ".$rowstone['stonemax'].")  or (jietuoxiangkou-0.05<= ".trim($res['stone'])." and jietuoxiangkou+0.04 >= ".trim($res['stone'])."))";
                    else
                        $sql="select max(jinzhong) as lishi_jinzhong_max,min(jinzhong) as lishi_jinzhong_min from warehouse_shipping.warehouse_goods g where g.is_on_sale in (1,2,3,4,5,6,8,10,11) and goods_sn='".trim($res['style_sn'])."' and caizhi like '".trim($res['caizhi'])."%' and  shoucun='".trim($res['zhiquan'])."' and jietuoxiangkou-0.05<= ".trim($res['stone'])." and jietuoxiangkou+0.04 >= ".trim($res['stone']);
                    //echo $sql;
                    $row_lishi=$this->db()->getRow($sql);
                }elseif($res['xiangkou']<>'' && is_numeric(trim($res['xiangkou']))){
                    $sql="select * from front.app_xiangkou where stone*1 = '".trim($res['xiangkou'])."'  and substring_index(finger,'-',1)*1 <= ".trim($res['zhiquan'])."  and ".trim($res['zhiquan'])." <= substring_index(finger,'-',-1)*1 and style_id='".$res['style_id']."'";
                    //echo $sql;
                    $row=$this->db()->getRow($sql);
                    if(empty($row)){
                        $sql="select * from front.app_xiangkou where stone*1 = '".trim($res['xiangkou'])."'  and substring_index(finger,'-',1)*1 <= ".round(trim($res['zhiquan']),0)."  and ".round(trim($res['zhiquan']),0)." <= substring_index(finger,'-',-1)*1 and style_id='".$res['style_id']."'";
                        //echo $sql;
                        $row=$this->db()->getRow($sql);
                    }
                    $sql="select max(jinzhong) as lishi_jinzhong_max,min(jinzhong) as lishi_jinzhong_min from warehouse_shipping.warehouse_goods g where g.is_on_sale in (1,2,3,4,5,6,8,10,11) and goods_sn='".trim($res['style_sn'])."' and caizhi like '".trim($res['caizhi'])."%' and  shoucun='".trim($res['zhiquan'])."' and ((zuanshidaxiao>= ".round(trim($res['xiangkou'])-0.05,3) ." and zuanshidaxiao <= ".round(trim($res['xiangkou'])+0.04,3) .") or jietuoxiangkou='".trim($res['xiangkou'])."')" ;
                    //echo $sql;
                    $row_lishi=$this->db()->getRow($sql);
                }
                if($row){
                    if(trim($res['caizhi'])=='18K'){
                        $jingzhongmax=$row['g18_weight']+$row['g18_weight_more'];
                        $jingzhongmin=$row['g18_weight']-$row['g18_weight_more2'];
                    }
                    if(trim($res['caizhi'])=='PT950'){
                        $jingzhongmax=$row['gpt_weight']+$row['gpt_weight_more'];
                        $jingzhongmin=$row['gpt_weight']-$row['gpt_weight_more2'];
                    }
                    $sql="update kela_supplier.product_info set biaozhun_jinzhong_max='".$jingzhongmax."',biaozhun_jinzhong_min='".$jingzhongmin."' where id='".$bc_id."'";
                    //echo $sql;
                    $this->db()->query($sql);
                }else{
                    $sql="update kela_supplier.product_info set biaozhun_jinzhong_max=null,biaozhun_jinzhong_min=null where id='".$bc_id."'";
                    //echo $sql;
                    $this->db()->query($sql);
                }
                if($row_lishi['lishi_jinzhong_max'] && $row_lishi['lishi_jinzhong_min']){
                    $lishi_jinzhong_max=$row_lishi['lishi_jinzhong_max'];
                    $lishi_jinzhong_min=$row_lishi['lishi_jinzhong_min'];
                    $sql="update kela_supplier.product_info set lishi_jinzhong_max='".$lishi_jinzhong_max."',lishi_jinzhong_min='".$lishi_jinzhong_min."' where id='".$bc_id."'";
                    //echo $sql;
                    $this->db()->query($sql);
                }else{
                    $sql="update kela_supplier.product_info set lishi_jinzhong_max=null,lishi_jinzhong_min=null where id='".$bc_id."'";
                    //echo $sql;
                    $this->db()->query($sql);
                }
            }else{
                $sql="update kela_supplier.product_info set biaozhun_jinzhong_max=null,biaozhun_jinzhong_min=null where id='".$bc_id."'";
                //echo $sql;
                $this->db()->query($sql);
                $sql="update kela_supplier.product_info set lishi_jinzhong_max=null,lishi_jinzhong_min=null where id='".$bc_id."'";
                //echo $sql;
                $this->db()->query($sql);
            }

            if($res && in_array($res['qiban_type'], array('0','1'))){
                $sql="select q.jinzhong_min,q.jinzhong_max from kela_supplier.product_info p,kela_supplier.product_goods_rel r,app_order.app_order_details d,purchase.purchase_qiban_goods q 
    where p.id=r.bc_id and r.goods_id=d.id and d.ext_goods_sn=q.addtime and p.id='{$bc_id}'";
                $qiban_res=$this->db()->getRow($sql);
                if(!empty($qiban_res)){
                    $sql="update kela_supplier.product_info set biaozhun_jinzhong_max='".$qiban_res['jinzhong_max']."',biaozhun_jinzhong_min='".$qiban_res['jinzhong_min']."' where id='".$bc_id."'";
                    //echo $sql;
                    $this->db()->query($sql);                   
                } 

            }

            if($transMode==true){
                $pdo->commit();//如果没有异常，就提交事务
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            }
            $result['success'] = 1;
            
        } catch(Exception $e) {//捕获异常
            if($transMode==true){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            }   
            $result['error'] = $e->getMessage();             
        }
        
        return $result;
    }
    
}

?>