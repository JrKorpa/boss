<?php
/**
 *  -------------------------------------------------
 *   @file		: PeishiListModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-14 21:00:23
 *   @update	:
 *  -------------------------------------------------
 */
class PeishiListModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'peishi_list';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array(
            "id"=>"id主键",
            "order_sn"=>"订单号",
            "rec_id"=>"布产id",
            "peishi_status"=>"配石状态",
            "add_time"=>"添加时间",
            "last_time"=>"最后修改时间",
            "add_user"=>"添加人",
            "color"=>"钻石颜色",
            "clarity"=>"钻石净度",
            "shape"=>"钻石形状",
            "cert"=>"证书类型",
            "zhengshuhao"=>"证书号",
            "carat"=>"钻石大小",
            "stone_num"=>"钻石数量",
            "stone_cat"=>"钻石类型",
            "stone_position"=>"石头位置(0:主石 1:副石1 2:副石2 3:副石3)",
            'caigou_time'=>'采购时间记录最新一次采购时间）' ,
            'songshi_time'=>'已送生产部时间(已送生产部的最新一次时间)',
            'peishi_time'=>'配石中时间（操作配石中的最新时间）' ,
            'caigou_user'=>'采购人（操作采购中的人员）' ,
            'songshi_user'=>'送石人（已送生产部操作人员）' ,
            'peishi_user'=>'配石人（配石中操作人员）'             
        );
		parent::__construct($id,$strConn);
	}
	
    function getDataChangeLog($newdo,$olddo,$filterFields){
        
        $fields = $this->_dataObject;
        $remark = '';
        foreach($newdo as $key=>$vo){
            if(in_array($key,$filterFields)){
                continue;
            }        
            if(isset($olddo[$key]) && $vo != $olddo[$key]){                 
                if(isset($fields[$key]) && count($fields[$key])<20){
                    $field_name = $fields[$key];
                }else{
                    $field_name = $key;
                }                 
                $remark.="[".$field_name."]由【".$olddo[$key]."】改为【".$vo."】,";
            }
        }
        if($remark==''){
            $remark ="";
        }else{
            $remark ="".trim($remark,',')."";
        }
        return $remark;
    }
    /**
     * 获取配石单信息
     * @param unknown $id
     */
    public function getPeishiInfo($id){
        $sql  ="select a.*,b.bc_sn from ".$this->table()." a left join product_info b on a.rec_id=b.id where a.id={$id}";
        return $this->db()->getRow($sql);
    }
	/**
	 *	pageList，分页列表
	 *
	 *	@url PeishiListController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true,$orderby="orderby1")
	{
	   
		//不要用*,修改为具体字段
		$sql = "SELECT 	p.`id`,	p.`rec_id`,	p.`order_sn`,p.`peishi_status`,p.stone_position,
    	pi.prc_name,pi.from_type,pi.p_sn,pi.xiangqian,pi.bc_style AS bc_type,p.add_time,p.add_user,
		p.peishi_time,p.peishi_user,p.caigou_time,p.caigou_user,p.songshi_time,p.songshi_user,    
		pi.`STATUS` AS bc_status,'' AS goods_id,pi.style_sn, pi.`id` AS bc_id,
		pi.`bc_sn`,	pi.`consignee`,	pi.`opra_uname`,pi.`order_time`,
    	pi.`info`,pi.is_quick_diy,p.cert,p.carat,pi.num * p.stone_num AS num,
    	p.color,p.clarity,	p.shape,p.stone_cat,p.zhengshuhao,	pi.`buchan_times`,
    	pi.`status`,  `sc`.`channel_class`, p.peishi_remark,oi.department_id,sc.company_id,pi.info as bc_remark   
		  FROM `kela_supplier`.`".$this->table()."` as p inner join `kela_supplier`.`product_info` as pi on p.`rec_id` = pi.`id` left join `app_order`.`base_order_info` `oi` on `pi`.`p_sn` = `oi`.`order_sn` left join `cuteframe`.`sales_channels` `sc` on `oi`.`department_id` = `sc`.`id` ";
		
		$str = "where 1=1";
		if(!empty($where['color'])){		    
		    if($where['color'] == "无"){
		        $str.=" and (p.color like '%{$where['color']}%' or ifnull(p.color,'') ='')";
		    }else{
		        $str.=" and p.color like '%{$where['color']}%'";
		    }
		}
		if(!empty($where['clarity'])){
		    if($where['clarity'] == "无"){
		        $str.=" and (p.clarity like '%{$where['clarity']}%' or ifnull(p.clarity,'') ='')";
		    }else{
		        $str.=" and p.clarity like '%{$where['clarity']}%'";
		    }
		}
		    
	    if(!empty($where['carat_min'])){
	        $str.=" and p.carat>='{$where['carat_min']}'";
	    }
	    if(!empty($where['carat_max'])){
	        $str.=" and p.carat<='{$where['carat_max']}'";
	    }
		
		if(!empty($where['cert'])){
		    if($where['cert'] == "无"){
		        $str.=" and (p.cert='{$where['cert']}' or ifnull(p.cert,'') ='')";
		    }else{
		       $str.=" and p.cert='{$where['cert']}'";
		    }
		}
		if(!empty($where['bc_type'])){
		    $str.=" and pi.bc_style='{$where['bc_type']}'";//布产类型 普通 加急
		}
		if(!empty($where['from_type'])){
		    $str.=" and pi.from_type={$where['from_type']}";//布产分类 1采购 2订单
		}
		if(!empty($where['bc_status'])){
		    $str.=" and pi.status='{$where['bc_status']}'";//布产分类 1采购 2订单
		}
        if (!empty($where['bc_sn']) && is_array($where['bc_sn'])) {
            $bc_sn = "'".implode("','",$where['bc_sn'])."'";            
            $str .= " and pi.`bc_sn` in({$bc_sn})";
        }
        if (isset($where['peishi_status']) && $where['peishi_status'] !== '') {
            $str .= " and p.`peishi_status`= '{$where['peishi_status']}' ";
        }
        if (isset($where['channel_class']) && $where['channel_class'] !== '') {
            $str .= " and `sc`.`channel_class`= '{$where['channel_class']}' ";
        }
        if(isset($where['is_quick_diy']) && $where['is_quick_diy']!=""){
            $sql .= " AND pi.is_quick_diy = ".$where['is_quick_diy'];
        }
        if (!empty($where['stone_cat'])) {
            $str .= " and p.stone_cat like '%{$where['stone_cat']}%' ";
        }
        if (!empty($where['shape'])) {
            $str .= " and p.shape like '%{$where['shape']}%' ";
        }  
        if(!empty($where['goods_id']) && is_array($where['goods_id'])){
            $goods_ids = "'".implode("','",$where['goods_id'])."'";
            $str .=" and p.id in(select distinct peishi_id from peishi_list_goods where goods_id in({$goods_ids}))";
        }    
        if(!empty($where['peishi_ids'])) {
            if(is_array($where['peishi_ids'])){
                $peishi_ids = implode(',',$where['peishi_ids']);
                $str .=" and p.id in({$peishi_ids})";
            }else{
                $peishi_ids = $where['peishi_ids'];
                $str .=" and p.id ={$peishi_ids}";
            }
            $orderby = "orderby2";
        } 
        //配石中时间
        if(!empty($where['add_time_begin'])){
            $str .= " and p.add_time >= '{$where['add_time_begin']}'";
        }
        if(!empty($where['add_time_end'])){
            $str .= " and p.add_time <= '{$where['add_time_end']} 23:59:59'";
        }
        //配石中时间
        if(!empty($where['peishi_time_begin'])){
            $str .= " and p.peishi_time >= '{$where['peishi_time_begin']}'";
        }   
        if(!empty($where['peishi_time_end'])){
            $str .= " and p.peishi_time <= '{$where['peishi_time_end']} 23:59:59'";
        }  
        //已送工厂时间
        if(!empty($where['songshi_time_begin'])){
            $str .= " and p.songshi_time >= '{$where['songshi_time_begin']}'";
        }        
        if(!empty($where['songshi_time_end'])){
            $str .= " and p.songshi_time <= '{$where['songshi_time_end']} 23:59:59'";
        }
        //采购时间
        if(!empty($where['caigou_time_begin'])){
            $str .= " and p.caigou_time >= '{$where['caigou_time_begin']}'";
        }        
        if(!empty($where['caigou_time_end'])){
            $str .= " and p.caigou_time <= '{$where['caigou_time_end']} 23:59:59'";
        }
        if(!empty($where['caigou_user'])){
            $str .= " and p.caigou_user = '{$where['caigou_user']}'";
        }
        if(!empty($where['peishi_user'])){
            $str .= " and p.peishi_user = '{$where['peishi_user']}'";
        }
        if(!empty($where['songshi_user'])){
            $str .= " and p.songshi_user = '{$where['songshi_user']}'";
        }
		if($orderby == "orderby1"){
		   $sql .= $str." ORDER BY p.`id` desc,sc.channel_class,p.carat,p.color,p.clarity,p.shape,p.cert";		
		}else{
		   $sql .= $str." ORDER BY p.`id` desc";
		}
		//echo $sql; 
		$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
		 //拼接属性数据	
		 $stonePostionsType =  array(0=>'主石',1=>'副石1',2=>'副石2',3=>'副石3');
		foreach($data['data'] as $key=>$val){
		    if(!empty($val['from_type'])){
		       $val['from_type'] =  $val['from_type']==1?"备货(采购单)":"客单(订单)";
		    }
		    if(!empty($val['channel_class'])){
		       $val['channel_class'] =  $val['channel_class']==1?"线上":"线下";
		    }
		    
		    $posId = (int)$val['stone_position'];
		    $val['stone_position'] =  isset($stonePostionsType[$posId])?$stonePostionsType[$posId]:'';
		    
		    $goods_id_all=$this->db()->getAll("select goods_id from kela_supplier.peishi_list_goods where peishi_id={$val['id']}");
			$goods_id_all = array_column($goods_id_all, 'goods_id');
			$val['goods_id'] = implode(',',$goods_id_all);
			
			$data['data'][$key] = $val;
		} 

		return $data;
	}
	//分页查询汇总
	public function pageListSum($where,$page,$pageSize=10,$useCache=true){
	    $pageData = $this->pageList ($where,$page,$pageSize,$useCache);
	    //$pageData = $this->getPeishiListNew($where,$page,$pageSize,$useCache);
	    if(!empty($pageData['data'])){
	        $peishiIdArr = array();	   
	        $data = array();
	        $fields = array('color','clarity','carat','channel_class','shape','cert');
	        $childData = $pageData['data'];
	        foreach ($pageData['data'] as $key=>$vo){
	            $sumData = array();//汇总缓存数组
	            $sumFlag = true;
	            if(in_array($vo['id'],$peishiIdArr)){
	                continue;
	            }
	            $vo['id_sum'] = '';
	            $vo['num_sum'] = 0;
	            foreach ($childData as $k=>$v){ 
	                if(in_array($v['id'],$peishiIdArr)){
	                    continue;
	                }
	                foreach ($fields as $field){	
	                    $vo[$field] = trim($vo[$field]);
	                    $v[$field]  = trim($v[$field]);
	                    $arr1 = explode('|',$vo[$field]);
	                    $arr2 = explode('|',$v[$field]);
	                    $arr_intersect = array_intersect($arr1,$arr2);
	                    if(empty($arr_intersect)){
	                        $sumFlag = false;
	                        break;
	                    }
	                    /**
	                     * 'color','clarity','carat','channel_class','shape','cert' 汇总
	                     * 汇总字段存入
	                     * 'color_sum','clarity_sum','carat_sum','channel_class_sum','shape_sum','cert_sum'
	                     * 
	                     */
	                    if(!isset($vo[$field.'_sum'])){
	                        $vo[$field.'_sum'] = '';
	                    }
	                    $arr1 = explode('|',$vo[$field]);
	                    $arr2 = explode('|',$vo[$field.'_sum']);
	                    $arr_merge = array_unique(array_merge($arr1,$arr2));	                     
	                    $vo[$field.'_sum'] = trim(implode("|",$arr_merge),'|');

	                }//end foreach $childData
	                if($sumFlag === false){
	                    continue;
	                }
    	            $v['clarity'] = $vo['clarity'];
    	            $v['carat'] = $vo['carat'];
    	            $v['cert'] = $vo['cert'];
    	            $v['shape'] = $vo['shape'];
    	            $v['channel_class'] = $vo['channel_class'];
    	            $vo['id_sum'] .= 'N'.$v['id'];
    	            $vo['num_sum'] += $v['num'];
	                $sumData['list'][] = $v;	                	                	                
	                $peishiIdArr[] = $v['id'];	                
	            }//end foreach list
	            $sumData['sumData'] = array(
	                'color' => $vo['color_sum'],
	                'clarity' => $vo['clarity_sum'],
	                'shape' => $vo['shape_sum'],
	                'carat' => $vo['carat_sum'],
	                'cert' => $vo['cert_sum'],
	                'channel_class' => $vo['channel_class_sum'],
	                'group_ids'=>trim($vo['id_sum'],'N'),
	                'num' =>$vo['num_sum']
	            );
	            $data[] = $sumData;
	            
	        }//end foreach $pageData['data']
	        
	        $pageData['data'] = $data;
	        
	        unset($sumData);
	        unset($childData);
	        unset($peishiIdArr);
	    }
	    //print_r($pageData);
	    return $pageData;
	}	
	/**
	 * 添加配石单日志
	 * @param unknown $id
	 * @param unknown $remark
	 */
	function addLog($id,$remark){	    
	    $data = array(
	        'peishi_id'=>$id,
	        'remark'=>$remark,
	        'add_time'=>date('Y-m-d H:i:s'),
	        'action_name'=>$_SESSION['userName']
	    );
	    $sql = $this->insertSql($data,'peishi_list_log');
	    return $this->db()->query($sql);
	}
	
	/**
	 * 普通查询
	 * @param $type one 查询单个字段， row查询一条记录 all 查询多条记录
	 */
	public function select2($fields = ' * ' , $where = " 1 " , $type = 'one'){
		$sql = "SELECT {$fields} FROM `peishi_list` WHERE {$where}";
		if($type == 'one'){
			$res = $this->db()->getOne($sql);
		}else if($type == 'row'){
			$res = $this->db()->getRow($sql);
		}else if($type == 'all'){
			$res = $this->db()->getAll($sql);
		}
		return $res;
	}
	
	//判断状态是否一致
	function checkPeishiStatusEqual($ids)
	{   
	    if(empty($ids)){
	        return false;
	    }
	    $where = 'where 1=1 ';
	    if(is_array($ids)){
	        $ids = implode(',',$ids);
	        $where .= " and id in({$ids})";
	    } else{	        
	        $where .= " and id ={$ids}";
	    } 
		$sql = "SELECT id FROM `peishi_list` {$where} group by peishi_status";
		$res = $this->db()->getAll($sql);
		if(count($res)>1){
		    return false;
		}else{
		    return true;
		}
	}
	
	function pagePeishiLogList ($where,$page,$pageSize=10,$useCache=true)
	{   
	    $str = "1=1";
        if(!empty($where['peishi_id'])){
            $str .=" and peishi_id={$where['peishi_id']}";
        }
		$sql = "SELECT `id`,`peishi_id`,`action_name`,`add_time`,`remark` FROM `peishi_list_log` where {$str}";
		//$data = $this->db()->getAll($sql);
		$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
		return $data;		

	}
	function deletePeishiGoods($id){
	    $sql = "delete from peishi_list_goods where peishi_id={$id}";
	    return $this->db()->query($sql);
	}

    //保存
    public function mutiPeishiUpdate($data)
    {
        $result = array('error'=>'','success'=>0);
        $dd = new DictView(new DictModel(1));
        //处理 配石中，送石，采购最后操作时间，操作人 数组
        $actionArr = array(
            2=>'peishi',4=>'songshi',5=>'caigou'
        );
        try {            
            $pdo = $this->db()->db();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            $add_time=date('Y-m-d H:i:s');
            foreach ($data as $vo){
                //更改配石单状态、备注
                $updata = array();
                if(isset($vo['peishi_status'])){
                    $updata['peishi_status'] = $vo['peishi_status'];
                    if(!empty($actionArr[$updata['peishi_status']])){
                        $actionName = $actionArr[$updata['peishi_status']];
                        $field_time = $actionName.'_time';
                        $field_user = $actionName.'_user';
                        $updata[$field_time] = $add_time;
                        if($vo['old_peishi_status']==5 && $vo['peishi_status']==2){
                            //采购中 更改为 配石中 ，配石人不变
                        }else{
                            $updata[$field_user] = $_SESSION['userName'];
                        }
                    }
                }
                $peishi_status_name = $dd->getEnum('peishi_status',$vo['peishi_status']);
                $peishi_status_name = $peishi_status_name?$peishi_status_name:'未操作';
                if(isset($vo['peishi_remark'])){
                    $updata['peishi_remark'] = $peishi_status_name.' 特殊备注:'.$vo['peishi_remark'];
                }else{
                    $updata['peishi_remark'] = $peishi_status_name;
                }
                
                if(!empty($updata)){
                    $sql = $this->updateSql($updata);
                    $sql = preg_replace('/ WHERE .*/is',' WHERE id='.$vo['id'], $sql);
                    $pdo->query($sql);
                }

                //货号条码处理
                $bc_logstr="";
                if(!empty($vo['goods_ids'])){
                    $sql = "delete from `peishi_list_goods` where peishi_id=".$vo['id'];
                    $pdo->query($sql);
                    foreach ($vo['goods_ids'] as $goods_id) {
                        $sql ="INSERT INTO `peishi_list_goods`(`peishi_id`,`goods_id`) VALUES(".$vo['id'].",'".$goods_id."')";
                        $pdo->query($sql);
                    }
                    $sql="select  goods_id,zhushiyanse,zhushijingdu,zhengshuleibie,zhengshuhao,zuanshidaxiao from warehouse_shipping.warehouse_goods where goods_id in ('". implode("','",$vo['goods_ids'])."')";
                    $peishi_goods=$this->db()->getAll($sql);
              
                    if($peishi_goods){
                    	$bc_logstr="配石单自动配石:<br>";
                    	foreach ($peishi_goods as $key => $v) {
                    		$bc_logstr.="货号[".$v['goods_id']."] 颜色[".$v['zhushiyanse']."] 净度[".$v['zhushijingdu']."] 证书类型[".$v['zhengshuleibie']."] 证书号[".$v['zhengshuhao']."] 钻石大小[".$v['zuanshidaxiao']."]<br>";
                    	}                    	                     
                    }
                }
                if($vo['peishi_status']=='4'){
                	$bc_logstr="配石状态:已送生产部 ".$bc_logstr." 特殊备注 - ".$vo['peishi_remark'];
                	$bc_logstr = $pdo->quote($bc_logstr);
                   	$sql="insert into product_opra_log select 0,b.id,b.status,{$bc_logstr},'".$_SESSION['userId']."','".$_SESSION['userName']."','".$add_time."' from peishi_list p,product_info b where p.rec_id=b.id and p.id='".$vo['id']."'";
                    $pdo->query($sql);
                }       
                $remark_log = $peishi_status_name;
                if(isset($vo['peishi_remark'])){
                    $remark_log .= ' 特殊备注:'.$vo['peishi_remark'];
                }
                $this->addLog($vo['id'],$remark_log);
            }
            $pdo->commit();
            //$pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return array('error'=>'','success'=>1);
        } catch (Exception $e) {
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return array('error'=>'保存失败，请联系技术人员处理！<!--sql:'.@$sql.'-->','success'=>0);
        }

    }   
    
    
    public function getExistsGoodsId($goods_ids){
         if(empty($goods_ids)){
             return array();
         }
         $sql = "select distinct goods_id from warehouse_shipping.warehouse_goods where 1=1";
         if(is_array($goods_ids) ){
             $sql .=" AND goods_id in('".implode("','",$goods_ids)."')";
         }else {
             $sql .=" AND goods_id ='".$goods_ids."'";
         }
         $data = $this->db()->getAll($sql);
         $data = array_column($data,'goods_id');
         return $data;
    }
    
    /**
     * 生成配石单
     * @param unknown $id
     * @return multitype:number string
     */
    public function createPeishiList($id,$act="insert",$type="现货组合镶嵌"){
    
        $result = array('success' => 0,'error' => '');
    
        $peishiListModel = new PeishiListModel(14);
        $opraLogModel = new ProductOpraLogModel(14);
        $model = new ProductInfoModel($id,14);
        $attrModel = new ProductInfoAttrModel(14);
        $dd = new DictModel(1);
        $olddo = $model->getDataObject();
        if(empty($olddo)){
            $result['error'] = "布产单信息查询失败";
            return $result;
        }
        
        $bc_sn = $olddo['bc_sn'];
        $p_sn = $olddo['p_sn'];
        $bc_status = $olddo['status'];
        $xiangqian = $olddo['xiangqian'];
        $style_sn = $olddo['style_sn'];
        $factory_id = $olddo['prc_id'];
        //布产列表，采购类型的布产单，不允许点击【分配工厂】，只能在采购列表或者采购布产列表分配工厂
        $from_type = $olddo['from_type'];
        //初始化、不需布产、已出厂、已取消，作废,配石单不需要做任何判断
        //不需布产11，已取消10，已出厂9，作废8，部分出厂7，质检完成6，质检中5，生产中4，已分配3，待分配2,初始化1
        if($act == 'update'){
            if(in_array($bc_status,array(1,8,11,9,10))){
             $result['success'] = 1;
             $result['error'] = "初始化、不需布产、已出厂、已取消，作废的布产单 配石单不需同步";
             return $result;
            } 
        }
        /***** 处理是否生成 配石单逻辑  开始*****/
        //查询封装配石信息
        $attrList = $attrModel->getGoodsAttr($id);
    
        $attrList = array_column($attrList,'value','code');
    
        $zhushi_carat = isset($attrList['cart'])?$attrList['cart']:'';//主石重
        $zhushi_carat = isset($attrList['carat'])?$attrList['carat']:$zhushi_carat;//主石重
        $zhushi_carat = isset($attrList['zuanshidaxiao'])?$attrList['zuanshidaxiao']:$zhushi_carat;//主石重
               
        $zhushi_num = isset($attrList['zhushi_num'])?$attrList['zhushi_num']:'0';//主石粒数
        $zhushi_num = $zhushi_carat>0 && $zhushi_num<=0?1:$zhushi_num;
        //特殊主石重拆分处理
        if(!is_numeric(trim($zhushi_carat))){
            $zhushi_carat = str_replace(" ", '', $zhushi_carat);
            if(preg_match("/(\d+(\.\d+)?)ct/is", $zhushi_carat,$arr)) {
                $zhushi_carat = $arr[1]/1;
            }
            if(preg_match("/(\d+?)p/is", $zhushi_carat,$arr)){
                $zhushi_num = $arr[1]/1;
            }            
        }
        
        $zhushi_cat = isset($attrList['zhushi_cat'])?$attrList['zhushi_cat']:'';//主石类型
        $zhushi_yanse = isset($attrList['color'])?$attrList['color']:'';//主石颜色
        $zhushi_yanse = isset($attrList['yanse'])?$attrList['yanse']:$zhushi_yanse;//主石颜色
    
        $zhushi_jingdu = isset($attrList['clarity'])?$attrList['clarity']:'';//主石净度
        $zhushi_jingdu = isset($attrList['jingdu'])?$attrList['jingdu']:$zhushi_jingdu;//主石净度
         
        $zhushi_shape = isset($attrList['zhushi_shape'])?$attrList['zhushi_shape']:'';//主石形状
        $zhushi_cert = isset($attrList['cert'])?$attrList['cert']:'';//主石证书类型
        $zhushi_zhengshuhao = isset($attrList['zhengshuhao'])?$attrList['zhengshuhao']:'';//主石证书类型
        $zhushi_zhengshuhao = isset($attrList['zhengshu'])?$attrList['zhengshu']:$zhushi_zhengshuhao;//主石证书类型
    
        $fushi_zhong1 = isset($attrList['fushi_zhong_total1'])?$attrList['fushi_zhong_total1']:'';//副石1重
        $fushi_num1 = isset($attrList['fushi_num1'])?$attrList['fushi_num1']:'';//副石1粒数
        $fushi_zhong2 = isset($attrList['fushi_zhong_total2'])?$attrList['fushi_zhong_total2']:'';//副石2重
        $fushi_num2 = isset($attrList['fushi_num2'])?$attrList['fushi_num2']:'';//副石2粒数
        $fushi_zhong3 = isset($attrList['fushi_zhong_total3'])?$attrList['fushi_zhong_total3']:'';//副石3重
        $fushi_num3 = isset($attrList['fushi_num3'])?$attrList['fushi_num3']:'';//副石3粒数
        $fushi_cat = isset($attrList['fushi_cat'])?$attrList['fushi_cat']:'';//副石类型
        $fushi_yanse = isset($attrList['fushi_yanse'])?$attrList['fushi_yanse']:'';//副石类型
        $fushi_jingdu = isset($attrList['fushi_jingdu'])?$attrList['fushi_jingdu']:'';//副石类型
        $fushi_shape = isset($attrList['fushi_shape'])?$attrList['fushi_shape']:'';//副石类型
    
        $xiangqianArr = array("工厂配钻，工厂镶嵌");
        $stone_position = array(0=>'主石',1=>'副石1',2=>'副石2',3=>'副石3');
        $stoneList = array();
        $isPeishiList = array();
        foreach ($stone_position as $p=>$posName){
            $stoneList[$p] = array("order_sn"=>$p_sn,
                "rec_id"=>$id,
                "peishi_status"=>0,
                "add_time"=>date('Y-m-d H:i:s'),
                "last_time"=>date('Y-m-d H:i:s'),
                "add_user"=>$_SESSION['userName'],
                "color"=>"",
                "clarity"=>"",
                "shape"=>"",
                "cert"=>"",
                "zhengshuhao"=>"",
                "carat"=>"",
                "stone_num"=>"",
                "stone_cat"=>"",
                "stone_position"=>$p
            );
            $isPeishiList[$p] = true;
            if($p == 0){
    
                $stoneList[$p]['color'] = $zhushi_yanse;
                $stoneList[$p]['clarity'] = $zhushi_jingdu;
                $stoneList[$p]['shape'] = $zhushi_shape;
                $stoneList[$p]['cert'] = $zhushi_cert;
                $stoneList[$p]['zhengshuhao'] = $zhushi_zhengshuhao;
                $stoneList[$p]['carat'] = $zhushi_carat;
                $stoneList[$p]['stone_num'] = $zhushi_num;
                $stoneList[$p]['stone_cat'] = $zhushi_cat;
               if(in_array($xiangqian,$xiangqianArr)){
                    if($zhushi_carat<=0 || $zhushi_num<=0 || strtoupper($style_sn)=='DIA')
                    {
                        $isPeishiList[$p] = false;
                        continue;
                    }
                }else{
                    $isPeishiList[$p] = false;
                    continue;
                }
    
    
            }else{
    
                if($p==1){
                    if($fushi_num1<=0){
                        $isPeishiList[$p] = false;
                        $stoneList[$p]['carat'] = 0;
                        $stoneList[$p]['stone_num'] = 0;
                    }else{
                        $stoneList[$p]['carat'] = sprintf("%.4f",$fushi_zhong1/$fushi_num1)/1;
                        $stoneList[$p]['stone_num'] = $fushi_num1;
                    }
                }else if($p==2){
                    if($fushi_num2<=0){
                        $isPeishiList[$p] = false;
                        $stoneList[$p]['carat'] = 0;
                        $stoneList[$p]['stone_num'] = 0;
                    }else{
                        $stoneList[$p]['carat'] = sprintf("%.4f",$fushi_zhong2/$fushi_num2)/1;
                        $stoneList[$p]['stone_num'] = $fushi_num2;
                    }
    
                }else if($p==3){
                    if($fushi_num3<=0){
                        $isPeishiList[$p] = false;
                        $stoneList[$p]['carat'] = 0;
                        $stoneList[$p]['stone_num'] = 0;
                    }else{
                        $stoneList[$p]['carat'] = sprintf("%.4f",$fushi_zhong3/$fushi_num3)/1;
                        $stoneList[$p]['stone_num'] = $fushi_num3;
                    }
                }
                $stoneList[$p]['color'] = $fushi_yanse;
                $stoneList[$p]['clarity'] = $fushi_jingdu;
                $stoneList[$p]['shape'] = $fushi_shape;
                $stoneList[$p]['zhengshuhao'] = "";
                $stoneList[$p]['cert'] = "";
                $stoneList[$p]['stone_cat'] = $fushi_cat;
            }
    
        }
        //提交保存配石信息
        try{
            foreach ($isPeishiList as $p=>$is_peishi){
                $peishiData = $stoneList[$p];
                //7.1布产单分配工厂，产生配石单时，配石状态根据以下规则自动更新：
                $peishiData['peishi_status'] = $this->getNewPeishiStatus($factory_id,$peishiData);
                $stoneTypeName = isset($stone_position[$p])?$stone_position[$p]:'';
                $exists = $peishiListModel->select2('*',"rec_id={$id} and stone_position={$p}",'row');
                if(!empty($exists)){
                    $exists['peishi_status'] = (int)$exists['peishi_status'];
                    //a .由【有】到【无】
                    if($is_peishi==false){
                        //a 1、配石单的原有状态是不需配石1\已送生产部4，则新的配石状态不变
                        if(in_array($exists['peishi_status'],array(1,4))){
                            $peishiData['peishi_status'] = $exists['peishi_status'];
                        }
                        //a 2、配石单的原有状态是未操作0\配石中2\厂配石3\备用钻6\采购中5，则新的配石状态更新为不需配石1
                        if(in_array($exists['peishi_status'],array(0,2,3,5,6))){
                            $peishiData['peishi_status'] = 1;
                        }
                    }else{
                        //b 由【有】到【有】                                               
                        //b 2 配石单的原有状态是已送生产部4，则新的配石状态不变
                        if(in_array($exists['peishi_status'],array(4))){
                            $peishiData['peishi_status'] = $exists['peishi_status'];
                        }else{
                            //b 1.1如果判断后的状态应该是未操作0，再分情况更新
                            if($peishiData['peishi_status']==0){
                                //b 1.1.1如果原状态是未操作0、配石中2、采购中5，则状态不变
                                if(in_array($exists['peishi_status'],array(0,2,5))){
                                    $peishiData['peishi_status'] = $exists['peishi_status'];
                                }
                                //b 1.1.2如果原状态是不需配石1\厂配石3\备用钻6,则状态更新为未操作0
                                if(in_array($exists['peishi_status'],array(1,3,6))){
                                    $peishiData['peishi_status'] = 0;//未操作
                                }
                                //b 1.2如果判断后的状态应该是厂配石，直接把状态更新为厂配石
                                //b 1.3如果判断后的状态应该是不需配石，直接把状态更新为不需配石                                
                            }
                            
                        }

                    }//end if($is_peishi==false){
                    
                    $peishiRemark = '';
                    $peishi_id = $exists['id'];
                    $peishi_status_old_name = $dd->getEnum('peishi_status',$exists['peishi_status']);
                    $peishi_status_old_name = $peishi_status_old_name?$peishi_status_old_name:'未操作';
                    $peishi_status_new_name = $dd->getEnum('peishi_status',$peishiData['peishi_status']);
                    $peishi_status_new_name = $peishi_status_new_name?$peishi_status_new_name:'未操作';
                                       
                    $fielterFields = array('add_time','last_time','peishi_status');
                    $changeDataLog = $peishiListModel->getDataChangeLog($peishiData,$exists,$fielterFields);
                          
                    $peishiListModel->update($peishiData,"id={$peishi_id}");
                    if($exists['peishi_status']==$peishiData['peishi_status']){
                        $peishiRemark = "{$type}：{$stoneTypeName}配石单{$peishi_id}已存在<br/>更新数据：".$changeDataLog;
                        if($changeDataLog==''){
                            $peishiRemark = "";
                        }    
                    }else{
                        $peishiListModel->update($peishiData,"id={$peishi_id}");
                        $peishiRemark = "{$type}：{$stoneTypeName}配石单{$peishi_id}已存在，更新配石单：配石状态由【{$peishi_status_old_name}】改为【{$peishi_status_new_name}】";
                        if($changeDataLog){
                            $peishiRemark .= ",".$changeDataLog;
                        }
                    }
    
                }else {
                    //c 由【无】到【有】，重新生成配石单，状态判断操作7.1
                    if($is_peishi==false){
                        continue;
                    }
                    $peishi_status_new_name = $dd->getEnum('peishi_status',$peishiData['peishi_status']);
                    $peishi_status_new_name = $peishi_status_new_name?$peishi_status_new_name:'未操作';
                    $peishi_id = $peishiListModel->saveData($peishiData,array());
                    $peishiRemark= "{$type}：布产单{$bc_sn}生成{$stoneTypeName}配石单{$peishi_id},默认状态【{$peishi_status_new_name}】";
                }
    
                if($peishiRemark!=''){
                    //配石列表日志
                    $peishiListModel->addLog($peishi_id,$peishiRemark);
                    //布产记录操作日志
                    $opraLogModel->addLog($id,$peishiRemark);
                }
    
            }
            $result['success'] = 1;
            return $result;
        }catch (Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    
    }
    /**
     * 自动识别配石状态
     * @param unknown $data
     * @return boolean|number
     * 1.证书号不为空，生成的配石单的状态默认为'不需配石'
     * 2. 证书号为空，根据布产单上的钻石大小、工厂、证书类型去裸石供料类型配置表判断更新，如果匹配到多个值，取优先级高（数字越小越优先）的供料类型：
        1）	如果匹配到的值是厂配钻，生成的配石单的状态默认为“厂配石”
        2）	如果匹配到的值是BDD配钻，生成的配石单的状态默认为“未操作”
        3）	如果匹配不到任何值，生成的配石单的状态默认为'未操作'
     */
    protected function getNewPeishiStatus($factory_id,$data){

        $cert = isset($data['cert'])?$data['cert']:'';
        $carat = isset($data['carat'])?(float)$data['carat']:0;
        $zhengshuhao = isset($data['zhengshuhao'])?$data['zhengshuhao']:"";        
        if(!empty($zhengshuhao)){
            return 1;//不需配石
        }
        $sql = "select feed_type from stone_feed_config where is_enable=1 and factory_id={$factory_id} and carat_min<={$carat} and carat_max>={$carat} and (cert='{$cert}' or cert='ALL') order by prority_sort asc";
       // echo $sql.';';
        $data = $this->db()->getRow($sql);
        if(!empty($data)){
            $feed_type = $data['feed_type'];//1BDD配钻 2厂配钻
            if($feed_type == 2){
                return 3;//厂配石
            }else if ($feed_type == 1){
                return 0;//未操作
            }
        }
        return 0;//未操作        
    }
    
    /**
     * 批量更新布产单，配石单旧数据
     * @param unknown $id
     * @return multitype:number string
     */
    public function updateOldBCAndPeishi($id,$act="update",$type="清洗配石单"){
    
        $result = array('success' => 0,'error' => '');
    
        $peishiListModel = new PeishiListModel(14);
        $opraLogModel = new ProductOpraLogModel(14);
        $model = new ProductInfoModel($id,14);
        $attrModel = new ProductInfoAttrModel(14);
        $applyInfoModel = new ProductApplyInfoModel($id,14);
        $dd = new DictModel(1);
        $olddo = $model->getDataObject();
        if(empty($olddo)){
            $result['error'] = "布产单信息查询失败";
            return $result;
        }
    
        $bc_sn = $olddo['bc_sn'];
        $p_sn = $olddo['p_sn'];
        $bc_status = $olddo['status'];
        $xiangqian = $olddo['xiangqian'];
        $style_sn = $olddo['style_sn'];
    
        //布产列表，采购类型的布产单，不允许点击【分配工厂】，只能在采购列表或者采购布产列表分配工厂
        $from_type = $olddo['from_type'];

        //查询封装配石信息
        $attrList = $attrModel->getGoodsAttr($id);
        $attrKeyVal = array_column($attrList,'value','code');
        $extAttr = array();
        if($from_type ==2){
            $sql = "select cert,xiangkou from app_order.app_order_details where id={$olddo['p_id']}";
            $orderDetail = $this->db()->getRow($sql);
            if(!empty($orderDetail)){
                if(!array_key_exists('xiangkou',$attrKeyVal)){
                    $extAttr[] = array('code'=>'xiangkou','name'=>"镶口",'value'=>$orderDetail['xiangkou']);
                }
                if(!array_key_exists('cert',$attrKeyVal)){
                    $extAttr[] = array('code'=>'cert','name'=>"证书类型",'value'=>$orderDetail['cert']);
                }
            }
        
        }
        $styleModel = new CStyleModel(11);
        
        $oldAttr2 = $attrList;//重新获取布产属性所有属性
        $newAttr2 = $styleModel->getStoneAttrList($style_sn,$oldAttr2);
        $newAttr2 = array_merge($extAttr,$newAttr2);
        foreach ($newAttr2 as $key=>$vo){
            //过滤掉副石信息
            if(preg_match("/fushi_/is",$vo['code'])){
                unset($newAttr2[$key]);
            }
        }
        $res = $applyInfoModel->saveProductAttrData($id, $newAttr2, $oldAttr2);
        if($res['success']==0){
            $result['error'] = "操作失败:同步主石，副石信息失败。".$res['error'];
            return $result;
        }
        $attrList = $attrModel->getGoodsAttr($id);
        $attrList = array_column($attrList,'value','code');
        
        
        
        $zhushi_carat = isset($attrList['cart'])?$attrList['cart']:'';//主石重
        $zhushi_carat = isset($attrList['carat'])?$attrList['carat']:$zhushi_carat;//主石重
        $zhushi_carat = isset($attrList['zuanshidaxiao'])?$attrList['zuanshidaxiao']:$zhushi_carat;//主石重
        $zhushi_carat = str_replace(" ", '', $zhushi_carat);
        if(preg_match("/(\d+(\.\d+)?)ct/is", $zhushi_carat,$arr)) {
            $zhushi_carat = $arr[1]/1;
        }
        
        $zhushi_num = 1;//主石数量
        if(preg_match("/(\d+?)p/is", $zhushi_carat,$arr)){
            $zhushi_num = $arr[1]/1;
        }
        $zhushi_cat = isset($attrList['zhushi_cat'])?$attrList['zhushi_cat']:'';//主石类型
        $zhushi_yanse = isset($attrList['color'])?$attrList['color']:'';//主石颜色
        $zhushi_yanse = isset($attrList['yanse'])?$attrList['yanse']:$zhushi_yanse;//主石颜色
    
        $zhushi_jingdu = isset($attrList['clarity'])?$attrList['clarity']:'';//主石净度
        $zhushi_jingdu = isset($attrList['jingdu'])?$attrList['jingdu']:$zhushi_jingdu;//主石净度
         
        $zhushi_shape = isset($attrList['zhushi_shape'])?$attrList['zhushi_shape']:'';//主石形状
        $zhushi_cert = isset($attrList['cert'])?$attrList['cert']:'';//主石证书类型
        $zhushi_zhengshuhao = isset($attrList['zhengshuhao'])?$attrList['zhengshuhao']:'';//主石证书类型
        $zhushi_zhengshuhao = isset($attrList['zhengshu'])?$attrList['zhengshu']:$zhushi_zhengshuhao;//主石证书类型
    
        $fushi_zhong1 = isset($attrList['fushi_zhong_total1'])?$attrList['fushi_zhong_total1']:'';//副石1重
        $fushi_num1 = isset($attrList['fushi_num1'])?$attrList['fushi_num1']:'';//副石1粒数
        $fushi_zhong2 = isset($attrList['fushi_zhong_total2'])?$attrList['fushi_zhong_total2']:'';//副石2重
        $fushi_num2 = isset($attrList['fushi_num2'])?$attrList['fushi_num2']:'';//副石2粒数
        $fushi_zhong3 = isset($attrList['fushi_zhong_total3'])?$attrList['fushi_zhong_total3']:'';//副石3重
        $fushi_num3 = isset($attrList['fushi_num3'])?$attrList['fushi_num3']:'';//副石3粒数
        $fushi_cat = isset($attrList['fushi_cat'])?$attrList['fushi_cat']:'';//副石类型
        $fushi_yanse = isset($attrList['fushi_yanse'])?$attrList['fushi_yanse']:'';//副石类型
        $fushi_jingdu = isset($attrList['fushi_jingdu'])?$attrList['fushi_jingdu']:'';//副石类型
        $fushi_shape = isset($attrList['fushi_shape'])?$attrList['fushi_shape']:'';//副石类型
    
        $xiangqianArr = array("工厂配钻，工厂镶嵌");
        $stone_position = array(0=>'主石',1=>'副石1',2=>'副石2',3=>'副石3');
        $stoneList = array();
        $isPeishiList = array();
        foreach ($stone_position as $p=>$posName){
            $stoneList[$p] = array(
                "order_sn"=>$p_sn,
                "rec_id"=>$id,
                "peishi_status"=>0,
                "last_time"=>date('Y-m-d H:i:s'),
                "color"=>"",
                "clarity"=>"",
                "shape"=>"",
                "cert"=>"",
                "zhengshuhao"=>"",
                "carat"=>"",
                "stone_num"=>"",
                "stone_cat"=>"",
                "stone_position"=>$p
            );
            $isPeishiList[$p] = true;
            if($p == 0){
    
                $stoneList[$p]['color'] = $zhushi_yanse;
                $stoneList[$p]['clarity'] = $zhushi_jingdu;
                $stoneList[$p]['shape'] = $zhushi_shape;
                $stoneList[$p]['cert'] = $zhushi_cert;
                $stoneList[$p]['zhengshuhao'] = $zhushi_zhengshuhao;
                $stoneList[$p]['carat'] = $zhushi_carat;
                $stoneList[$p]['stone_num'] = $zhushi_num;
                $stoneList[$p]['stone_cat'] = $zhushi_cat;
                if(in_array($xiangqian,$xiangqianArr)){
                    if($zhushi_carat<=0 || $zhushi_num<=0 || strtoupper($style_sn)=='DIA')
                    {
                        $isPeishiList[$p] = false;
                        continue;
                    }
                }else{
                    $isPeishiList[$p] = false;
                    continue;
                }
    
    
            }else{
    
                if($p==1){
                    if($fushi_num1<=0){
                        $isPeishiList[$p] = false;
                        $stoneList[$p]['carat'] = 0;
                        $stoneList[$p]['stone_num'] = 0;
                    }else{
                        $stoneList[$p]['carat'] = sprintf("%.4f",$fushi_zhong1/$fushi_num1)/1;
                        $stoneList[$p]['stone_num'] = $fushi_num1;
                    }
                }else if($p==2){
                    if($fushi_num2<=0){
                        $isPeishiList[$p] = false;
                        $stoneList[$p]['carat'] = 0;
                        $stoneList[$p]['stone_num'] = 0;
                    }else{
                        $stoneList[$p]['carat'] = sprintf("%.4f",$fushi_zhong2/$fushi_num2)/1;
                        $stoneList[$p]['stone_num'] = $fushi_num2;
                    }
    
                }else if($p==3){
                    if($fushi_num3<=0){
                        $isPeishiList[$p] = false;
                        $stoneList[$p]['carat'] = 0;
                        $stoneList[$p]['stone_num'] = 0;
                    }else{
                        $stoneList[$p]['carat'] = sprintf("%.4f",$fushi_zhong3/$fushi_num3)/1;
                        $stoneList[$p]['stone_num'] = $fushi_num3;
                    }
                }
                $stoneList[$p]['color'] = $fushi_yanse;
                $stoneList[$p]['clarity'] = $fushi_jingdu;
                $stoneList[$p]['shape'] = $fushi_shape;
                $stoneList[$p]['zhengshuhao'] = "";
                $stoneList[$p]['cert'] = "";
                $stoneList[$p]['stone_cat'] = $fushi_cat;
            }
    
        }
        //提交保存配石信息
        try{
            foreach ($isPeishiList as $p=>$is_peishi){
                $stoneTypeName = isset($stone_position[$p])?$stone_position[$p]:'';
                $exists = $peishiListModel->select2('*',"rec_id={$id} and stone_position={$p}",'row');
                if(!empty($exists)){
                    $peishiRemark = '';
                    $peishi_id = $exists['id'];
                    $peishi_status = $exists['peishi_status'];
                    $peishi_status_old = $dd->getEnum('peishi_status',$exists['peishi_status']);
                    $peishi_status_old = $peishi_status_old?$peishi_status_old:'未操作';
                    $peishiData = $stoneList[$p];
                    if(isset($peishiData['order_sn'])) unset($peishiData['order_sn']);
                    if(isset($peishiData['rec_id'])) unset($peishiData['rec_id']);
                    if(isset($peishiData['peishi_status'])) unset($peishiData['peishi_status']);

                    $fielterFields = array('add_time','last_time','peishi_status');
                    $changeDataLog = $peishiListModel->getDataChangeLog($peishiData,$exists,$fielterFields);
                    if($is_peishi==false){
                        //$peishiData['peishi_status'] = 1;
                        $peishiListModel->update($peishiData,"id={$peishi_id}");
                        if($exists['peishi_status']==1){
                            $peishiRemark = "{$type}：{$stoneTypeName}配石单{$peishi_id}已存在，配石单不需配石<br/>更新数据：".$changeDataLog;
                            if($changeDataLog==''){
                                $peishiRemark = "";
                            }
                        }else{
                            $peishiRemark = "{$type}：{$stoneTypeName}配石单{$peishi_id}已存在，更新配石单：配石状态由【{$peishi_status_old}】改为【不需配石】";
                            if($changeDataLog){
                                $peishiRemark .= ",".$changeDataLog;
                            }
                        }
    
                    }else{
                        $peishiListModel->update($peishiData,"id={$peishi_id}");
                        $peishiRemark = "{$type}：{$stoneTypeName}配石单{$peishi_id}已存在,更新配石单:".$changeDataLog;
                        if($changeDataLog == ''){
                            $peishiRemark = '';
                        }
                    }
    
                }else {
                    if($is_peishi==false){
                        continue;
                    }
                    //$peishiData = $stoneList[$p];
                    //$peishi_id = $peishiListModel->saveData($peishiData,array());
                    //$peishiRemark= "{$type}：布产单{$bc_sn}生成{$stoneTypeName}配石单{$peishi_id}";
                }
    
                //if($peishiRemark!=''){
                    //配石列表日志
                    //$peishiListModel->addLog($peishi_id,$peishiRemark);
                    //布产记录操作日志
                    //$opraLogModel->addLog($id,$peishiRemark);
                //}
    
            }
            $result['success'] = 1;
            return $result;
        }catch (Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    
    }
   
}

?>