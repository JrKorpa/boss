<?php
/**
 * This contains the Retrieval API .
 *
 */
class api
{
    private  $db = null;
    private  $error_msg = '';
    private  $return_msg = '';
    private  $return_sql = '';
    private  $filter = array();
    public function __construct($_filter)
    {
        global $config;
        $this->db= new KELA_API_DB($config);
		$this->filter = $_filter;
    }

    /*
     * 获取款式分类
     */
    public function getGoodsInfo()
    {
        $s_time = microtime();
      //  $sql = "SELECT b.`isXianhuo`,b.`sale_price` FROM `app_salepolicy_channel` as a, `app_salepolicy_goods` as b  WHERE a.`policy_id` = b.`policy_id` AND a.`status` = 3 AND a.is_delete=1   AND b.`status`= 3 AND b.`is_delete`=1";
        $now_time = date("Y-m-d");
        $sql = "SELECT b.`isXianhuo`,b.`sale_price`,c.`goods_sn`,c.`goods_name`,c.`category`,c.`product_type`,b.policy_id,b.goods_id  FROM `base_salepolicy_info` as o,`app_salepolicy_channel` as a, `app_salepolicy_goods` as b,`base_salepolicy_goods` as c,`base_salepolicy_info` as d WHERE c.`is_sale`=1  and o.`policy_id`= a.`policy_id` and o.`policy_id`= b.`policy_id` AND b.`goods_id`=c.`goods_id` AND o.is_delete=0 AND  b.is_delete=1 AND b.policy_id= d.policy_id AND d.bsi_status=3";
		if(isset($this->filter['department']) && !empty(trim($this->filter['department']))){
			$department = trim($this->filter['department']);
			$sql .=" AND a.`channel`= ".$department;
		}else{
			$this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "部门不能为空";
			$this->display();
		}
        if(isset($this->filter['policy_id']) && !empty(trim($this->filter['policy_id']))){
            $policy_id = trim($this->filter['policy_id']);
            $sql .=" AND b.`policy_id`= ".$policy_id;
        }


		if(isset($this->filter['goods_sn']) && !empty(trim($this->filter['goods_sn']))){
			$goods_sn = trim($this->filter['goods_sn']);
			$sql .=" AND b.`goods_id`='".$goods_sn."'";
		}else{
			$this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "商品编码不能为空";
			$this->display();
		}

	   $row = $this->db->getAll($sql);

        // 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}else{
            //获取价格
            $sqlj = "select ag.goods_id,ag.sale_price,b.is_default,b.policy_name,b.policy_id from app_salepolicy_goods as ag LEFT JOIN `base_salepolicy_info` as b ON  ag.policy_id=b.policy_id LEFT JOIN `base_salepolicy_goods` as bsg on  bsg.goods_id=ag.goods_id LEFT JOIN app_salepolicy_channel as a on a.policy_id=ag.policy_id where ag.goods_id ='$goods_sn' and b.`policy_start_time` <= '$now_time' and b.`policy_end_time` >= '$now_time' and ag.`is_delete` = '1' and bsg.`is_sale` = '1' and b.`bsi_status`= '3'";
            $sqlj .=" AND a.`channel`= ".$department;
            $jarray = $this->db->query($sqlj);
            $carr = array();
            foreach($jarray as $k=>$v){
                if($v['is_default']==1){
                    $carr[$v['goods_id']][$v['policy_id']]=array('policy_name'=>$v['policy_name'],'sale_price'=>$v['sale_price']);
                }else{
                    $carr[$v['goods_id']][$v['policy_id']]=array('policy_name'=>$v['policy_name'],'sale_price'=>$v['sale_price']);
                }
            }
            $row['sprice']=$carr;

			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
    }


    /**
     * 可销售列表数据接口
     * @param array $insert_data 推送过来的数据
     */
    public function AddAppPayDetail() {

        $s_time = microtime();
        $_id = 0;
        if (isset($this->filter['insert_data'])) {
            $data = $this->filter['insert_data'];
            $info = array();
            if(count($data) > 0){
                foreach ($data as $val){
                    if(is_array($val) && isset($val['goods_id'])){
                        $_sql = "select `id`,`is_sale`,`is_valid` from `base_salepolicy_goods` where `goods_id` = '{$val['goods_id']}'";
                        $info = $this->db->getRow($_sql);
                        //如果有更新它的数据
                        if($info){
                            //如果已经下架并且此货已经销售是不更改此货的状态的
                            if($info['is_sale'] ==0 && $info['is_valid']==2){
                                unset($val['is_sale']);
                            }
                            //判断此货品是否已经绑定销售政策，并且销售政策没有审核才能修改此商品的信息，否则不修改
                            /*$goods_id = $val['goods_id'];
                            $sql_1 = "select `a`.`goods_id` from `app_salepolicy_goods` as a,`base_salepolicy_info` as o WHERE`a`.`policy_id` = `o`.`policy_id` AND `a`.`goods_id` ='".$goods_id."' AND `o`.`bsi_status`=3 ";
                            $is_salepolicy = $this->db->getOne($sql_1);
                            $val['id'] =$is_salepolicy;

                            if($is_salepolicy){
                                continue;
                            }*/

                            $where = " `goods_id` = '{$val['goods_id']}'";
                            unset($val['goods_id']);
                            $this -> db -> autoExecute('base_salepolicy_goods',$val,'UPDATE',$where, "SILENT");
                        }else{
                            $val['add_time'] = date("Y-m-d H:i:s");
                            $this -> db -> autoExecute('base_salepolicy_goods',$val,'INSERT','', "SILENT");
                        }

    				}else{
                                        if(!isset($data['goods_id']))
                                        {
                                                $this->error = 1;
                                                $this->return_sql = '';
                                                $this->error_msg = "data没有goods_id";
                                                $this->return_msg = 0;
                                                $this->display();
                                        }
						$_sql = "select `id`,`is_sale`,`is_valid` from `base_salepolicy_goods` where `goods_id` = '{$data['goods_id']}'";
                        $info = $this->db->getRow($_sql);
                        if($info){
                            //如果已经下架并且此货已经销售是不更改此货的状态的
                            if($info['is_sale'] ==0 && $info['is_valid']==2){
                                 unset($data['is_sale']);
                            }
                            /*$goods_id = $data['goods_id'];
                            $sql_1 = "select `a`.`goods_id` from `app_salepolicy_goods` as a,`base_salepolicy_info` as o WHERE`a`.`policy_id` = `o`.`policy_id` AND `a`.`goods_id` ='".$goods_id."' AND `o`.`bsi_status`=3 ";
                            $is_salepolicy = $this->db->getOne($sql_1);
                            $val['id'] =$is_salepolicy;

                            if($is_salepolicy){
                                continue;
                            }*/
                            $where = " `goods_id` = '{$data['goods_id']}'";
                            unset($data['goods_id']);
                            $this -> db -> autoExecute('base_salepolicy_goods',$data,'UPDATE',$where, "SILENT");
                        }else{
                            $data['add_time'] = date("Y-m-d H:i:s");
                            $this->db->autoExecute('base_salepolicy_goods',$data,'INSERT','', "SILENT");
                        }
                    }
                }


				if($info){
					$_id = 1;
				}else{
					$_id = $this->db->insertId();
				}
            }else{
                $this->error = 1;
				$this->return_sql = '';
				$this->error_msg = "insert_data是个空数组";
				$this->return_msg = 0;
				$this->display();
            }
        }else{
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数insert_data";
            $this->return_msg = 0;
            $this->display();
        }
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
		if(!$_id)
		{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "失败";
			$this -> return_msg = array();
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = '';
			$this -> return_msg = $_id;
			$this->display();
		}
    }


	/**
     * 可销售列表数据接口
     * @param array $update_data 更新推送过来的数据
     */
    public function UpdateAppPayDetail() {
        $s_time = microtime();
        $_id = 0;
        if (isset($this->filter['update_data'])) {
            $data = $this->filter['update_data'];
            if(count($data) > 0){
                foreach ($data as $val){
					$where = " `goods_id` = '{$val['goods_id']}'";
					unset($val['goods_id']);
					$res = $this -> db -> autoExecute('base_salepolicy_goods',$val,'UPDATE',$where, "SILENT");
                }


            }else{
                $this->error = 1;
				$this->return_sql = '';
				$this->error_msg = "update_data是个空数组";
				$this->return_msg = 0;
				$this->display();
            }
        }else{
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数update_data";
            $this->return_msg = 0;
            $this->display();
        }
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
		if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "失败";
			$this -> return_msg = array();
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = '';
			$this -> return_msg = $_id;
			$this->display();
		}
    }

	/**
     * 销售政策的商品数据接口
     * @param array $update_data 此商品的对的所以状态，指针对货品
     * app_salepolicy_goods
     */
    public function UpdateSalepolicyGoodsStatus() {
        $s_time = microtime();
        $_id = 0;
        if (isset($this->filter['update_data'])) {
            $data = $this->filter['update_data'];
            if(count($data) > 0){
            	$this->error = 1;
                foreach ($data as $val){
		$where = " `goods_id` = '{$val['goods_id']}'";
		unset($val['goods_id']);
		$res = $this -> db -> autoExecute('app_salepolicy_goods',$val,'UPDATE',$where, "SILENT");
                }
             }else{
		$this->error = 1;
		$this->return_sql = '';
		$this->error_msg = "update_data是个空数组";
		$this->return_msg = 0;
		$this->display();
            }
        }else{
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数update_data";
            $this->return_msg = 0;
            $this->display();
        }
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
		if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $res;
			$this -> error_msg = "失败";
			$this -> return_msg = array();
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $res;
			$this -> return_msg = '成功';
			$this->display();
		}
    }


    /**
     * 销售系统调用添加订单商品优惠
     */
    public function addOrderFavorable() {
        $s_time = microtime();
        $_id = 0;
        if (isset($this->filter['add_data'])) {
            $data = $this->filter['add_data'];
            if(count($data) > 0){
                $info = $this->db->getOne("select `id` from `app_order_favorable` where `detail_id`={$data['detail_id']}");
                if($info){
                    $_data = array('favorable_price'=>$data['favorable_price'],'check_user_id'=>$data['check_user_id'],'check_user'=>$data['check_user'],'check_status'=>1,'consignee'=>$data['consignee'],'create_user'=>$data['create_user']);
                    $where = " `detail_id`=".$data['detail_id'];
                    $_id = $this -> db -> autoExecute('app_order_favorable',$_data,'UPDATE',$where, "SILENT");
                }else{
                    $_id = $this -> db -> autoExecute('app_order_favorable',$data,'INSERT','', "SILENT");
                }

             }else{
                $this->error = 1;
                $this->return_sql = '';
                $this->error_msg = "add_data是个空数组";
                $this->return_msg = 0;
                $this->display();
            }
        }else{
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数add_data";
            $this->return_msg = 0;
            $this->display();
        }
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$_id)
        {
            $this -> error = 1;
            $this -> return_sql = $_id;
            $this -> error_msg = "失败";
            $this -> return_msg = array();
            $this->display();
        }
        else
        {
            $this -> error = 0;
            $this -> return_sql = $_id;
            $this -> return_msg = '成功';
            $this->display();
        }
    }

    /**
     * 销售系统取消订单商品优惠  非裸钻
     */
    public function addOrderFavorable_chengpin() {
        $s_time = microtime();
        $_id = 0;
        if (isset($this->filter['add_data'])) {
            $data = $this->filter['add_data'];
            if(count($data) > 0){
                $info = $this->db->getOne("select `id` from `app_order_favorable` where `detail_id`={$data['detail_id']}");
                if($info){
                    $_data = array('favorable_price'=>$data['favorable_price'],'check_user_id'=>$data['check_user_id'],'check_user'=>$data['check_user'],'check_status'=>1,'consignee'=>$data['consignee'],'create_user'=>$data['create_user'],'create_time'=>$data['create_time']);
                    $where = " `detail_id`=".$data['detail_id'];
                    $_id = $this -> db -> autoExecute('app_order_favorable',$_data,'UPDATE',$where, "SILENT");
                }else{
                    $_id = $this -> db -> autoExecute('app_order_favorable',$data,'INSERT','', "SILENT");
                }

             }else{
                $this->error = 1;
                $this->return_sql = '';
                $this->error_msg = "add_data是个空数组";
                $this->return_msg = 0;
                $this->display();
            }
        }else{
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数add_data";
            $this->return_msg = 0;
            $this->display();
        }
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$_id)
        {
            $this -> error = 1;
            $this -> return_sql = $_id;
            $this -> error_msg = "失败";
            $this -> return_msg = array();
            $this->display();
        }
        else
        {
            $this -> error = 0;
            $this -> return_sql = $_id;
            $this -> return_msg = '成功';
            $this->display();
        }
    }

    /*
     * 获取可售商品信息
     */
    public function getProductGoodsInfo()
    {
        $s_time = microtime();
        $goods_id=$this->filter['goods_id'];//货号或款号
        $stone=$this->filter['stone'];//主石
        $finger=$this->filter['finger'];//手寸
        $caizhi=$this->filter['caizhi'];//材质
        $yanse=$this->filter['yanse'];//颜色
        $isXianhuo=$this->filter['isXianhuo'];//是否现货
        $channel=$this->filter['channel'];//部门
        $type = $this->filter['type'];
        $is_base_style=$this->filter['is_base_style'];
        $xiangkou = $this->filter['xiangkou'];
        $policy_name = $this->filter['policy_name'];
        $update_start_time = $this->filter['update_start_time'];
        $update_end_time = $this->filter['update_end_time'];
       	$times = date('Y-m-d');
        $is_delete =1;
        $is_sale=1;
        $bsi_status='';//3;

        $where = "";
        if(!empty($goods_id))
        {
           if(is_numeric($goods_id)){
                $where .= " and ag.`goods_id` like \"%".addslashes($goods_id)."%\" ";
            }else{
                $where .= " and `bsg`.`goods_sn` like '%".addslashes($goods_id)."%' ";
            }
        }
        if($isXianhuo!='')
        {
        	$where .= " and ag.`isXianhuo` = {$isXianhuo}";
    	}

        if($times!=''){
       		$bwhere .= " and b.`policy_start_time` <= '{$times}' and b.`policy_end_time` >= '{$times}'";
       		//$where .= " and b.`policy_start_time` <= '{$times}' and b.`policy_end_time` >= '{$times}'";
      	}

      	if($channel!=''){
       		$bwhere .= " and c.`channel` in ('{$channel}')";
      		$where .= " and c.`channel` in ('{$channel}')";
      	}

        if($is_delete!=''){
            $where .= " and ag.`is_delete` = '{$is_delete}'";
        }

        if($update_start_time!=''){
            $where .= " and ag.`update_time` >= '{$update_start_time} 00:00:00'";
        }

        if($update_end_time!=''){
            $where .= " and ag.`update_time` <= '{$update_end_time} 23:59:59'";
        }

        if($is_sale!=''){
            $where .= " and bsg.`is_sale` = '{$is_sale}'";
        }

        if($bsi_status!=''){
            $bwhere .= " and b.`bsi_status`= '{$bsi_status}'";
            //$where .= " and b.`bsi_status`= '{$bsi_status}'";
        }

        if($policy_name!=''){
            $bwhere .= " and b.`policy_name`= '{$policy_name}'";
            //$where .= " and b.`bsi_status`= '{$bsi_status}'";
        }
        
        if($xiangkou!=''){
            if (isset($this->filter['xiangkou_strict'])) {
                if (floatval($xiangkou) == 0) {
                    $where .= ' and (`bsg`.`xiangkou` is null or `bsg`.`xiangkou` = 0)';
                } else {
                    $where .= " and `bsg`.`xiangkou`= {$xiangkou}";
                }
            } else {
                $where .= " and `bsg`.`xiangkou`= {$xiangkou}";
            }
        }
        
        if($type!=''){
            $where .= " and `bsg`.`type`= '{$type}'";
        }
        if($is_base_style!=''){
            $where .= " and `bsg`.`is_base_style`= '{$is_base_style}'";
        }
        if($stone!=''){
            $where .= " and `bsg`.`stone`= '{$stone}'";
        }
        if($finger!=''){
            if (isset($this->filter['finger_strict'])) {
                if (floatval($finger) == 0) {
                    $where .= ' and (`bsg`.`finger` is null or `bsg`.`finger` = 0)';
                } else {
                    $where .= " and `bsg`.`finger`= '{$finger}'";
                }
            } else {
                $where .= " and `bsg`.`finger`= '{$finger}'";
            }
        }
        if($caizhi!=''){
            $where .= " and `bsg`.`caizhi`= '{$caizhi}'";
        }
        if($yanse!=''){
            $where .= " and `bsg`.`yanse`= '{$yanse}'";
        }

		$policy_sql="SELECT DISTINCT b.policy_id FROM  `base_salepolicy_info` AS b,`app_salepolicy_channel` AS c where c.policy_id = b.policy_id $bwhere";
		$policy_list = $this->db->getAll($policy_sql);

        //file_put_contents('zyy.log',$policy_sql);

		if(empty($policy_list)){
			$this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到数据";
            $this -> return_msg = array();
            $this->display();
		}else{
			$policy_ids = array_column($policy_list,'policy_id');
		}
		$where_policy = " AND ag.policy_id in (".implode(',',$policy_ids).") ";
        $sqlc = "select COUNT(ag.goods_id) from `app_salepolicy_goods` as ag  
        LEFT JOIN `base_salepolicy_goods` as bsg on  bsg.goods_id=ag.goods_id 
        LEFT JOIN `app_salepolicy_channel` as c on  c.policy_id=ag.policy_id
        WHERE 1".$where." ".$where_policy." GROUP BY ag.goods_id";
        
       // file_put_contents('zyy.log1',$sqlc);
		$data['recordCount'] = $this->db->getOne($sqlc);
		if (isset($where['check_exist']) && intval($where['check_exist']) == 1) {
		    $this -> error = 0;
		    $this -> return_sql = $sqlc;
		    $this -> return_msg = $data;
		    $this->display();
		}
		
		$data['pageSize'] = isset($this->filter['pageSize']) && (int) $this->filter['pageSize'] > 1 ? (int) $this->filter['pageSize'] : 10;
        $data['pageCount'] = round($data['recordCount'] / $data['pageSize']);
        $data['page'] = isset($this->filter['page']) && (int) $this->filter['page'] > 1 ? (int) $this->filter['page'] : 1;
        $data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
        $data['isFirst'] = $data['page'] > 1 ? false : true;
        $data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
        $data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;

        $sql = "SELECT ag.goods_id, ag.id, ag.policy_id, ag.chengben, ag.sale_price, ag.jiajia, ag.sta_value, ag.chengben_compare, ag.isXianhuo, ag.create_time, ag.update_time,ag.create_user,ag.check_time,ag.check_user,ag.status,ag.is_delete,bsg.is_base_style,bsg.xiangkou,bsg.stone,bsg.finger,bsg.caizhi,bsg.yanse,bsg.type,bsg.is_base_style,bsg.goods_sn,bsg.warehouse,c.channel,bsg.is_sale FROM `app_salepolicy_goods` as ag 
		LEFT JOIN `base_salepolicy_goods` as bsg on  bsg.goods_id=ag.goods_id  
		LEFT JOIN `app_salepolicy_channel` as c on  c.policy_id=ag.policy_id
		where 1 ".$where." ".$where_policy." GROUP BY ag.goods_id ORDER BY id desc LIMIT ".($data['start']-1).','.$data['pageSize'];
//file_put_contents('zyy.log2',$sql);
        $data['data'] = $this->db->getAll($sql);
        if(!empty($data['data'])){
            foreach ($data['data'] as $key => $value) {
                $data['data'][$key]['thumb_img'] = '';
                if ($value['goods_sn'] == '仅售现货') continue;
                $sqls = "SELECT `thumb_img` FROM `app_style_gallery` WHERE `style_sn` = '{$value['goods_sn']}' AND `image_place` = 1";
                $rest = $this->db->getOne($sqls);
                if($rest != ''){
                    $data['data'][$key]['thumb_img'] = $rest;//45°图片
                }
            }
        }
        //查出销售政策的价格
        if(!empty($data['data'])){
            $goods_ids = implode("','",array_column($data['data'],'goods_id'));
            $sqlj = "select ag.goods_id,ag.sale_price,b.is_default,b.policy_name,bsg.warehouse from app_salepolicy_goods as ag LEFT JOIN `base_salepolicy_info` as b ON  ag.policy_id=b.policy_id LEFT JOIN `base_salepolicy_goods` as bsg on  bsg.goods_id=ag.goods_id INNER JOIN app_salepolicy_channel as c ON c.policy_id=ag.policy_id  WHERE ag.goods_id in('$goods_ids') and b.`policy_start_time` <= '$times' and b.`policy_end_time` >= '$times' and ag.`is_delete` = '1' and bsg.`is_sale` = '1' and b.`bsi_status`= '3' and b.`is_delete`=0";
            if($channel!=''){
                $sqlj .= " and c.`channel` in ('{$channel}')";
            }
            $sqlj.= " group by ag.policy_id";

            $jarray = $this->db->getAll($sqlj);
            $carr = array();
            foreach($jarray as $k=>$v){
                    $carr[$v['goods_id']][]=array($v['policy_name']=>$v['sale_price']);

            }
            $data['sprice']=$carr;
        }



        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(empty($data['data'])){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到数据";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $data;
            $this->display();
        }
    }

    //批量修改价格  通过goods_id
    public function changeCostPrice(){
        $s_time = microtime();
       if(isset($this->filter['goods_id']) && !empty($this -> filter["goods_id"]))
        {
            $data = $this ->filter["goods_id"];
       }else{
            $this -> error = 1;
            $this -> return_msg = "goods_id不能为空";
            $this->display();
        }


       if(isset($this->filter['chengben']) && !empty($this -> filter["chengben"]))
        {
            $chengben = $this ->filter["chengben"];

       }else{
            $this -> error = 1;
            $this -> return_msg = "成本不能为空";
            $this->display();
        }

      if(count($chengben)!=count($data)){
           $this -> error = 1;
           $this -> return_msg = "成本和goods_id数量不符无法进行修改";
           $this->display();
       }
        //合并成数组
        $data =  array_combine($data,$chengben);

        $newtime  = date('Y-m-d');
        //批量更改
        //这里批量更改,base_salepolicy_goods.chengbenjia信息并且把所有的未审核的销售政策的商品 app_salepolicy_goods.status=1的chengben_compare更改为这个价格

        foreach($data as $key=>$val){

            $sql = "update base_salepolicy_goods set chengbenjia=$val  WHERE goods_id=$key";
            $rea = $this->db->query($sql);

            $sql1 = "SELECT asg.id,asg.policy_id as policy_ids,asg.jiajia,asg.sta_value FROM app_salepolicy_goods AS asg LEFT JOIN base_salepolicy_info AS bsi ON asg.policy_id=bsi.policy_id WHERE asg.goods_id='$key' AND bsi.bsi_status=1 AND  policy_start_time<='$newtime' AND policy_end_time>='$newtime' AND bsi.is_delete=1";
            //file_put_contents("sql.txt",$sql."\r\n",FILE_APPEND );
            $res = $this->db->getAll($sql1);


            foreach($res as $k=>$v){
                //计算销售价
                $xiaoshoujia = $v['jiajia']*$val+$v['sta_value'];
                $sql2 = "update app_salepolicy_goods set sale_price =$xiaoshoujia,chengben=$val WHERE id= '$v[id]'";

                // file_put_contents("sql1.txt",$sql1."\r\n",FILE_APPEND );
                $xiugai = $this->db->query($sql2);
                $this -> error = 1;
                $this -> return_sql =$sql2;
                $this -> return_msg = $xiugai;
                $this->display();
            }

        }

        //返回信息
        if(!$rea){
            $this -> error = 1;
            $this -> return_sql =$sql1;
            $this -> return_msg = '修改失败';
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql1;
            $this -> return_msg = '修改成功';
            $this->display();
        }
    }



    /**
    //可销售状态批量更改接口
    public function EditIsSaleStatus()
    {
        //若 接收到的数据不为空 则拼接where条件
        if(isset($this->filter['goods_id']) && !empty($this -> filter["goods_id"]))
        {
            $data = $this ->filter["goods_id"];
        }else{
            $this -> error = 1;
            $this -> return_msg = "goods_id不能为空";
            $this->display();
        }
        if((isset($this->filter['is_sale']) && !empty($this -> filter["is_sale"]))||($this -> filter["is_sale"]===0))
        {
            $sale_status = $this -> filter["is_sale"];
        }else{
            $this -> error = 1;
            $this -> return_msg = "销售状态不能为空";
            $this->display();
        }

        if(isset($this->filter['is_valid'])){
            $is_valid = $this->filter['is_valid'];
        }else{
            $this -> error = 1;
            $this -> return_msg = "当前的状态不能为空";
            $this->display();
        }
        //遍历订单数组更新
        foreach($data as $v){
            $_sql = "select `id`,`is_sale`,`is_valid` from `base_salepolicy_goods` where `goods_id` = '{$v}'";
            $info = $this->db->getRow($_sql);
            //如果有更新它的数据
            if($info){
                //如果已经下架并且此货已经销售是不更改此货的状态的
                if($info['is_sale'] ==0 && $info['is_valid']==2){
                    continue;
                }
            }
            $sql = "UPDATE `base_salepolicy_goods` SET `is_sale`= {$sale_status} ,`is_valid`={$is_valid} WHERE `goods_id`='{$v}'";
           // file_put_contents("D:\u22.txt",$sql."\r\n",FILE_APPEND );
            $row=$this->db->query($sql);
        }

        //返回信息
        if(!$row){
            $this -> error = 1;
            $this -> return_sql = var_export($data,true);
            $this -> return_msg = false;
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = true;
            $this->display();
        }
    }

    */

    /**
    *可销售状态批量更改接口
    * @param goods_id Array 批量操作的货号
    * @param del_goods Array 编辑仓储单据时，被删除的明细，既要自动上架的货品 （改参数在接口里可选，不传也没事）
    * @param  is_sale  Int 上架状态，1上架，0下架
    * @param is_valid Int 商品是否有效 数字字典font.sale_goods_valid
    * @author caocao
    * @update 2015-04-10
    */
    public function EditIsSaleStatus()
    {
    	$this -> error = 0;
    	$this -> return_sql = '';
    	$this -> return_msg = '操作成功';
    	$this->display();
    	
            $s_time = microtime();
        define("root_path",dirname(dirname(dirname(dirname(__FILE__)))));
        require_once(root_path.'/frame/init.php');
        $pdo = DB::cn(12)->db();//pdo对象

        //若 接收到的数据不为空 则拼接where条件
        if(isset($this->filter['goods_id']) && !empty($this -> filter["goods_id"]))
        {
            $data = $this ->filter["goods_id"];
        }else{
            $this -> error = 1;
            $this -> return_msg = "推送可销售货品接口：goods_id不能为空";
            $this->display();
        }

        $auto_shangjia = array();
        if( isset($this->filter['del_goods']) && !empty($this->filter['del_goods'])){
            if(is_array($this->filter['del_goods'])){
                $auto_shangjia = $this->filter['del_goods'];
            }else{
                $this -> error = 1;
                $this -> return_msg = "操作失败，推送可销售货品接口参数 del_goods 需要数组形式";
                $this->display();
            }
        }

        if((isset($this->filter['is_sale']) && !empty($this -> filter["is_sale"]))||($this -> filter["is_sale"]==0))
        {
            $sale_status = $this -> filter["is_sale"];
        }else{
            $this -> error = 1;
            $this -> return_msg = "推送可销售货品接口：必须传递货品的销售状态";
            $this->display();
        }

        if(isset($this->filter['is_valid'])){
            $is_valid = $this->filter['is_valid'];
        }else{
            $this -> error = 1;
            $this -> return_msg = "推送可销售货品接口：商品的有效状态不能为空";
            $this->display();
        }

        try{
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
                $pdo->beginTransaction();//开启事务
                //注意先执行自动上架操作
                if( !empty($auto_shangjia) ){
                        //仓储单据，编辑时老的明细要自动上架
                        foreach($auto_shangjia as $val){
                            $sql = "UPDATE `base_salepolicy_goods` SET `is_sale`= 1 ,`is_valid`= 1 WHERE `goods_id`='{$val}'";
                            // file_put_contents("D:\u22.txt",$sql."\r\n",FILE_APPEND );
                            $pdo->query($sql);
                        }
                }



                //遍历订单数组更新
                foreach($data as $v){

                        $_sql = "select `id`,`is_sale`,`is_valid` from `base_salepolicy_goods` where `goods_id` = '{$v}'";
                        $info = $this->db->getRow($_sql);
                        //如果有更新它的数据
                        if($info){
                            //如果已经下架并且此货已经销售是不更改此货的状态的
                            if($info['is_sale'] ==0 && $info['is_valid']==2){
                                continue;
                            }
                        }
                        //批量更新订单配送状态
                        $sql = "UPDATE `base_salepolicy_goods` SET `is_sale`= {$sale_status} ,`is_valid`={$is_valid} WHERE `goods_id`='{$v}'";
                        // file_put_contents("D:\u22.txt",$sql."\r\n",FILE_APPEND );
                        $pdo->query($sql);
                }

        }catch(Exception $e){//捕获异常
                //print_r($e);exit;
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                $this->error = 1;
                $this->return_sql = $sql;
                $this->error_msg = "推送可销售货品接口：事务执行";
                $this->return_msg = 0;
                $this->display();
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交

         // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //执行成功返回信息
        $this -> error = 0;
        $this -> return_sql = '';
        $this -> return_msg = '操作成功';
        $this->display();
    }


    //改变销售状态
    public function updateSalepolicySalestatus()
    {
    	$this -> error = 0;
    	$this -> return_sql = 1;
    	$this -> return_msg = "编辑成功";
    	$this->display();
    	
        //若 接收到的数据不为空 则拼接where条件
        if(isset($this->filter['goods_id']) && !empty($this -> filter["goods_id"]))
        {
            $goods_id = $this ->filter["goods_id"];
        }else{
            $this -> error = 1;
            $this -> return_msg = "goods_id不能为空";
            $this->display();
        }
        if((isset($this->filter['sale_status']) && !empty($this -> filter["sale_status"]))||($this -> filter["sale_status"]===0))
        {
            $sale_status = $this -> filter["sale_status"];
        }else{
            $this -> error = 1;
            $this -> return_msg = "销售状态不能为空";
            $this->display();
        }
        $where = " `goods_id` = ".$goods_id." AND `is_delete` = 1";
        $data['sale_status']=$sale_status;
        $row=$this -> db -> autoExecute('app_salepolicy_goods',$data,'UPDATE',$where, "SILENT");

        //返回信息
        if(!$row){
            $this -> error = 1;
            $this -> return_sql = $row;
            $this -> return_msg = "编辑失败";
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $row;
            $this -> return_msg = "编辑成功";
            $this->display();
        }
    }


        /**
     * 更新销售政策的商品数据接口
     * @param array $update_data 更新商品上下架状态及有效状态
     * UpdateSalepolicyGoodsIsSale
     */
    public function UpdateSalepolicyGoodsIsSale() {
    	$this -> error = 0;
    	$this -> return_sql = 1;
    	$this -> return_msg = '成功';
    	$this->display();
    	
        $s_time = microtime();
        $_id = 0;
        if (isset($this->filter['update_data'])) {
            $data = $this->filter['update_data'];
            if(count($data) > 0){

            	$this->error = 0;

                $this->error = 1;
                foreach ($data as $val){
                	if($val['goods_id']){
                		$where = " `goods_id` = '{$val['goods_id']}'";
                		unset($val['goods_id']);
                		$res = $this -> db -> autoExecute('base_salepolicy_goods',$val,'UPDATE',$where, "SILENT");
                	}else{
                		$where = " `goods_sn` = '{$val['goods_sn']}'";
                		unset($val['goods_sn']);
                		$res = $this -> db -> autoExecute('base_salepolicy_goods',$val,'UPDATE',$where, "SILENT");
                	}

                }
             }else{
                $this->error = 1;
                $this->return_sql = '';
                $this->error_msg = "update_data是个空数组";
                $this->return_msg = 0;
                $this->display();
            }
        }else{
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数update_data";
            $this->return_msg = 0;
            $this->display();
        }
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$res)
        {
            $this -> error = 1;
            $this -> return_sql = $res;
            $this -> error_msg = "失败";
            $this -> return_msg = array();
            $this->display();
        }
        else
        {
            $this -> error = 0;
            $this -> return_sql = $res;
            $this -> return_msg = '成功';
            $this->display();
        }
    }

    /**
     * 获取销售政策信息
     */
    public function SalePolicyInfo(){
        $policy_id = $this->filter['policy_id'];
        if(!empty($policy_id)){
            $sql = "SELECT `policy_id`,`policy_name`,`policy_start_time`,`policy_end_time`,`create_time`,`create_user`,`create_remark`,`check_user`,`check_time`,`zuofei_time`,`check_remark`,`bsi_status`,`is_delete`,`is_together`,`jiajia`,`sta_value`,`is_favourable` FROM `base_salepolicy_info` WHERE `policy_id`=".$policy_id;
           $data =  $this->db->getRow($sql);
            if(!empty($data)){
                $this -> error = 0;
                $this -> return_sql = $sql;
                $this -> error_msg = "成功";
                $this -> return_msg = $data;
                $this->display();
            }else{
                $this -> error = 1;
                $this -> return_sql = $sql;
                $this -> error_msg = "没有该销售政策的信息";
                $this -> return_msg = array();
                $this->display();
            }
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "没有传销售政策的id";
            $this -> return_msg = array();
            $this->display();
        }
    }

    /**
     * 获取默认销售政策信息
     */
    public function SalePolicyInfoIs_default(){
        $goods_id = $this->filter['goods_id'];
        if(!empty($goods_id)){
            $sql = "SELECT `bg`.`policy_id`,`bs`.`policy_name`,`bg`.`update_time`,`bg`.`sale_price`,`bg`.`goods_id` FROM `base_salepolicy_info` as `bs`,`app_salepolicy_goods` as bg WHERE `bs`.`policy_id`=`bg`.`policy_id` AND  `bg`.`goods_id`='".$goods_id."' AND `bg`.`is_delete`=1 AND `bs`.`is_default`=1 AND `bs`.`is_delete`=0 AND `bs`.`bsi_status`!=4";
           $data =  $this->db->getRow($sql);
            if(!empty($data)){
                $this -> error = 0;
                $this -> return_sql = $sql;
                $this -> error_msg = "成功";
                $this -> return_msg = $data;
                $this->display();
            }else{
                $this -> error = 1;
                $this -> return_sql = $sql;
                $this -> error_msg = "没有该销售政策的信息";
                $this -> return_msg = array();
                $this->display();
            }
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "没有传销售政策的id";
            $this -> return_msg = array();
            $this->display();
        }
    }

    /**
     * 获取创建时间非默认销售政策信息
     */
    public function SalePolicyInfoNew(){
        $goods_id = $this->filter['goods_id'];
        if(!empty($goods_id)){
            $sql = "SELECT `bg`.`policy_id`,`bs`.`policy_name`,`bg`.`update_time`,`bg`.`create_time`,`bg`.`sale_price`,`bg`.`goods_id` FROM `base_salepolicy_info` as `bs`,`app_salepolicy_goods` as bg WHERE `bs`.`policy_id`=`bg`.`policy_id` AND  `bg`.`goods_id`='".$goods_id."' AND `bs`.`is_default`=2 AND `bs`.`is_delete`=0 AND `bs`.`bsi_status`!=4 ORDER BY `bg`.`create_time` DESC LIMIT 1";
           $data =  $this->db->getRow($sql);
            if(!empty($data)){
                $this -> error = 0;
                $this -> return_sql = $sql;
                $this -> error_msg = "成功";
                $this -> return_msg = $data;
                $this->display();
            }else{
                $this -> error = 1;
                $this -> return_sql = $sql;
                $this -> error_msg = "没有该销售政策的信息";
                $this -> return_msg = array();
                $this->display();
            }
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "没有传销售政策的id";
            $this -> return_msg = array();
            $this->display();
        }
    }

    /**
     * 获取更新时间非默认销售政策信息
     */
    public function SalePolicyInfoUp(){
        $goods_id = $this->filter['goods_id'];
        if(!empty($goods_id)){
            $sql = "SELECT `bg`.`policy_id`,`bs`.`policy_name`,`bg`.`update_time`,`bg`.`create_time`,`bg`.`sale_price`,`bg`.`goods_id` FROM `base_salepolicy_info` as `bs`,`app_salepolicy_goods` as bg WHERE `bs`.`policy_id`=`bg`.`policy_id` AND  `bg`.`goods_id`='".$goods_id."' AND `bs`.`is_default`=2 AND `bs`.`is_delete`=0 AND `bs`.`bsi_status`!=4 ORDER BY `bg`.`update_time` DESC LIMIT 1";
           $data =  $this->db->getRow($sql);
            if(!empty($data)){
                $this -> error = 0;
                $this -> return_sql = $sql;
                $this -> error_msg = "成功";
                $this -> return_msg = $data;
                $this->display();
            }else{
                $this -> error = 1;
                $this -> return_sql = $sql;
                $this -> error_msg = "没有该销售政策的信息";
                $this -> return_msg = array();
                $this->display();
            }
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "没有传销售政策的id";
            $this -> return_msg = array();
            $this->display();
        }
    }

    /*
     * 在产品搜索时根据多个货号，和具体的渠道来获取货品信息
     */
    public function getAppSalepolicyGoodsInfo()
    {

        $s_time = microtime();
      //  $sql = "SELECT b.`isXianhuo`,b.`sale_price` FROM `app_salepolicy_channel` as a, `app_salepolicy_goods` as b  WHERE a.`policy_id` = b.`policy_id` AND a.`status` = 3 AND a.is_delete=1   AND b.`status`= 3 AND b.`is_delete`=1";
        $now_time = date("Y-m-d");
        $base_sql ="SELECT type,goods_id FROM `base_salepolicy_goods` where 1 ";
        //判断货品是打包政策还是普通政策
        $putong_sql = "SELECT b.`id`,a.`channel`,c.`type`,c.`goods_id`,`c`.`goods_name`,c.`goods_sn`,c.`isXianhuo`,b.`sale_price`,`o`.`policy_name` FROM `base_salepolicy_info` as o,`app_salepolicy_channel` as a, `app_salepolicy_goods` as b,`base_salepolicy_goods` as c WHERE c.`is_sale`=1  and o.`policy_id`= a.`policy_id` and o.`policy_id`= b.`policy_id` AND b.`goods_id`=c.`goods_id` AND o.is_delete=0 AND o.bsi_status=3 AND b.is_delete=1 AND o.`policy_start_time`<= '".$now_time."' AND o.`policy_end_time`>='".$now_time."' ";
        //o.bsi_status=3 AND
        $dabao_sql = "SELECT `atg`.`together_id`,`atg`.`id`,a.`channel`,`c`.`type`,c.`goods_id`,`c`.`goods_sn`,`c`.`goods_name`,`ast`.`together_name`, `atp`.`policy_id`,`atg`.`sale_price`,`o`.`policy_name` FROM  `app_salepolicy_together_goods` as `ast`, `app_together_policey_related` as `atp`, `app_together_goods_related` as `atg` ,`base_salepolicy_info` as `o`, `base_salepolicy_goods` as `c`,`app_salepolicy_channel` as `a`  WHERE `atp`.`id`=`atg`.`together_id` AND `ast`.`id`=`atp`.`together_id` AND `atp`.`policy_id` = `o`.`policy_id` AND `atg`.`goods_id`=c.`goods_id` AND `c`.`type`=2 AND   `o`.`policy_id`= `a`.`policy_id` AND  o.is_delete=0 AND  o.`policy_start_time`<= '".$now_time."' AND o.`policy_end_time`>='".$now_time."' ";

		if(isset($this->filter['department']) && !empty(trim($this->filter['department']))){
			$department = trim($this->filter['department']);
            $dabao_sql .= " AND a.`channel`= ".$department;
            $putong_sql .=" AND a.`channel`= ".$department;
		}else{
			$this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "部门不能为空";
			$this->display();
		}

		if(isset($this->filter['goods_id_in']) && !empty(trim($this->filter['goods_id_in']))){
			$goods_id_in = trim($this->filter['goods_id_in']);
			$base_sql .=" AND `goods_id` in ('".$goods_id_in."')";
           // $dabao_sql .= " AND b.`goods_id` in ('".$goods_id_in."')";
            $putong_sql .=" AND b.`goods_id` in ('".$goods_id_in."')";
		}else{
			$this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "商品编码不能为空";
			$this->display();
		}

        //查看此货品是打包商品还是普通商品
	   $row = $this->db->getAll($base_sql);


       if(empty($row)){
            $this -> error = 0;
			$this -> return_sql = $base_sql;
			$this -> error_msg = "此商品不在可销售列表";
			$this -> return_msg = array();
			$this->display();
       }




       $putong_goods_id_arr = array();
       $dabao_goods_id_arr = array();
       foreach ($row as $val){
           if($val['type'] == 2){//打包
               $dabao_goods_id_arr[$val['goods_id']] = $val['goods_id'];
           }else{
               $putong_goods_id_arr[$val['goods_id']] = $val['goods_id'];
           }
       }

       $dabao_data = array();
       $putong_data = array();

	   //普遍商品
      if(!empty($putong_goods_id_arr)){
          $putong_goods_id = implode("','", $putong_goods_id_arr);
          $putong_sql .= " AND c.`goods_id` in ('".$putong_goods_id."')";
          if($this->filter['is_default']){
              $putong_sql.=" AND o.`is_default` = 1";
          }
          $putong_sql.= " GROUP BY c.`goods_id`";
          $info = $this->db->getAll($putong_sql);
          foreach ($info as $val){
              $id = $val['id'];
              $putong_data[$id] = $val;
          }
          if(!empty($info)){
              $sqlj = "select ag.id, ag.goods_id,ag.sale_price,b.is_default,b.policy_name ,b.policy_id from app_salepolicy_goods as ag LEFT JOIN `base_salepolicy_info` as b ON  ag.policy_id=b.policy_id LEFT JOIN `base_salepolicy_goods` as bsg on  bsg.goods_id=ag.goods_id LEFT JOIN  app_salepolicy_channel as a on a.policy_id=ag.policy_id where ag.goods_id in('$putong_goods_id') and b.`policy_start_time` <= '$now_time' and b.`policy_end_time` >= '$now_time' and ag.`is_delete` = '1' and bsg.`is_sale` = '1' and b.`bsi_status`= '3'";
              $sqlj .=" AND a.`channel`= ".$department;
              $jarray = $this->db->query($sqlj);
              $carr = array();
              foreach($jarray as $k=>$v){
                  if($v['is_default']==1){
                      $carr[$v['goods_id']][$v['id']]=array('policy_name'=>$v['policy_name'],'sale_price'=>$v['sale_price'],'_id'=>$v['id']);
                  }else{
                      $carr[$v['goods_id']][$v['id']]=array('policy_name'=>$v['policy_name'],'sale_price'=>$v['sale_price'],'_id'=>$v['id']);
                  }
              }
              $putong_data['sprice']=$carr;
          }
      }

      //打包信息
      if(!empty($dabao_goods_id_arr)){
          $dabao_goods_id = implode("','", $dabao_goods_id_arr);
          $dabao_sql .= " AND c.`goods_id` in ('".$dabao_goods_id."')";
          if(!empty($this->filter('is_default'))&&$this->filter('is_default')==1){
              $dabao_sql.=" AND o.`is_default` = 1";
          }
          $dabao_sql .=" GROUP BY c.`goods_id`";
          $info = $this->db->getAll($dabao_sql);
          foreach ($info as $val){
             $id = $val['id'];
             $dabao_data[$id] = $val;
          }

          if(!empty($dabao_data)){
              $sqlj = "select ag.id, ag.goods_id,ag.sale_price,b.is_default,b.policy_name,b.policy_id from app_salepolicy_goods as ag LEFT JOIN `base_salepolicy_info` as b ON  ag.policy_id=b.policy_id LEFT JOIN `base_salepolicy_goods` as bsg on  bsg.goods_id=ag.goods_id LEFT JOIN  app_salepolicy_channel as a on a.policy_id=ag.policy_id where ag.goods_id in('$dabao_goods_id') and b.`policy_start_time` <= '$now_time' and b.`policy_end_time` >= '$now_time' and ag.`is_delete` = '1' and bsg.`is_sale` = '1' and b.`bsi_status`= '3'";
              $sqlj .=" AND a.`channel`= ".$department;
              $jarray = $this->db->query($sqlj);
              $carr = array();
              foreach($jarray as $k=>$v){
                  if($v['is_default']==1){
                      $carr[$v['goods_id']]=array($v['id']=>array($v['policy_name'],$v['sale_price']));
                  }else{
                      $carr[$v['goods_id']]=array($v['id']=>array($v['policy_name'],$v['sale_price']));
                  }
              }
              $dabao_data['sprice']=$carr;
          }
      }

        // 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(empty($dabao_data) && empty($putong_data)){
			$this -> error = 0;
			$this -> return_sql = "";
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = "";
			$this -> return_msg = array('dabao_data'=>$dabao_data,'putong_data'=>$putong_data);
			$this->display();
		}
    }

    /*
     * 知道货品所在的政策，政策
     * id: 政策对商品的id
     * type: 1 普通 2 打包
     * department_id 渠道
     * 根据上面的条件，货品信息
     */
    public function getAppSalepolicyGoodsByWhere()
    {

        $s_time = microtime();
      //  $sql = "SELECT b.`isXianhuo`,b.`sale_price` FROM `app_salepolicy_channel` as a, `app_salepolicy_goods` as b  WHERE a.`policy_id` = b.`policy_id` AND a.`status` = 3 AND a.is_delete=1   AND b.`status`= 3 AND b.`is_delete`=1";
        $now_time = date("Y-m-d");
        //判断货品是打包政策还是普通政策
        $putong_sql = "SELECT b.id,b.goods_id,a.`channel`,`o`.`policy_name`,b.`isXianhuo`,b.`sale_price`,`c`.`type`,`c`.`caizhi`,`c`.`stone`,`c`.`xiangkou`,`c`.`yanse`,`c`.`finger`,`c`.`goods_sn`,`c`.`goods_name` ,`c`.`category`,`c`.`product_type` FROM `base_salepolicy_info` as o,`app_salepolicy_channel` as a, `app_salepolicy_goods` as b,`base_salepolicy_goods` as c WHERE c.`is_sale`=1  and o.`policy_id`= a.`policy_id` and o.`policy_id`= b.`policy_id` AND b.`goods_id`=c.`goods_id` AND o.is_delete=0 AND o.bsi_status=3 AND b.is_delete=1 AND o.`policy_start_time`<= '".$now_time."' AND o.`policy_end_time`>='".$now_time."' ";
        //o.bsi_status=3 AND
        $dabao_sql = "SELECT `atg`.`together_id` as id,a.`channel`,`o`.`policy_name`,c.`isXianhuo`,`atg`.`sale_price`,`c`.`type`,`c`.`caizhi`,`c`.`stone`,`c`.`xiangkou`,`c`.`yanse`,`c`.`finger`,`c`.`goods_sn`,`c`.`goods_name`,`c`.`category`,`c`.`product_type`,`ast`.`together_name`, `atp`.`policy_id`,`atg`.`sale_price`,`atg`.`goods_id` ,`o`.`policy_name` FROM  `app_salepolicy_together_goods` as `ast`, `app_together_policey_related` as `atp`, `app_together_goods_related` as `atg` ,`base_salepolicy_info` as `o`, `base_salepolicy_goods` as `c`,`app_salepolicy_channel` as `a`  WHERE `atp`.`id`=`atg`.`together_id` AND `ast`.`id`=`atp`.`together_id` AND `atp`.`policy_id` = `o`.`policy_id` AND `atg`.`goods_id`=c.`goods_id` AND `c`.`type`=2 AND   `o`.`policy_id`= `a`.`policy_id` AND  o.is_delete=0 AND  o.`policy_start_time`<= '".$now_time."' AND o.`policy_end_time`>='".$now_time."' ";

		if(isset($this->filter['department']) && !empty(trim($this->filter['department']))){
			$department = trim($this->filter['department']);
            $dabao_sql .= " AND a.`channel`= ".$department;
            $putong_sql .=" AND a.`channel`= ".$department;
		}else{
			$this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "部门不能为空";
			$this->display();
		}

        if(isset($this->filter['type']) && !empty(trim($this->filter['type']))){
			$type = trim($this->filter['type']);
			$dabao_sql .=" AND `c`.`type` = '".$type."'";
            $putong_sql .=" AND `c`.`type` = '".$type."'";
		}else{
			$this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "销售政策类型不能为空";
			$this->display();
		}

        if(isset($this->filter['id']) && !empty(trim($this->filter['id']))){
			$id = trim($this->filter['id']);
			$dabao_sql .=" AND `atg`.`together_id` = $id";
            $putong_sql .=" AND `b`.`id` = $id";
		}else{
			$this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "渠道对应商品的id不能为空";
			$this->display();
		}

		if(isset($this->filter['goods_id_in']) && !empty(trim($this->filter['goods_id_in']))){
			$goods_id_in = trim($this->filter['goods_id_in']);
            $dabao_sql .= " AND b.`goods_id` in ('".$goods_id_in."')";
            $putong_sql .=" AND b.`goods_id` in ('".$goods_id_in."')";
		}

		if(isset($this->filter['goods_id']) && !empty(trim($this->filter['goods_id']))){
			$goods_id = trim($this->filter['goods_id']);
			$dabao_sql .=" AND `goods_id` = '".$goods_id."'";
            $putong_sql .=" AND b.`goods_id` = '".$goods_id."'";
		}

        $data = array();
        if($type ==2){//打包
            $data = $this->db->getAll($dabao_sql);
        }else{//普通
            $data = $this->db->getAll($putong_sql);
        }
        // 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(empty($data)){
			$this -> error = 0;
			$this -> return_sql = $dabao_sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $dabao_sql;
			$this -> return_msg = $data;
			$this->display();
		}
    }

    /**
     * 设置商品上架状态及有效状态 yxt
     */
    public function setGoodsUnsell()
    {
    	$this -> error = 0;
    	$this -> return_sql = '';
    	$this -> return_msg = 1;
    	$this->display();
    	
        $s_time = microtime();
        if(isset($this->filter['change']) && !empty($this->filter['change'])){
            $data = $this->filter['change'];
        }else{
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "参数有误";
            $this->display();
        }
        if(isset($this->filter['where']) && !empty($this->filter['where'])){
            $where = $this->filter['where'];
        }else{
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "参数有误";
            $this->display();
        }
        if(count($data) != count($where)){
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "参数有误";
            $this->display();
        }

        $res = $this->db->autoExecALL($data,'base_salepolicy_goods','UPDATE',$where);

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$res){
            $this -> error = 0;
            $this -> return_sql = '';
            $this -> error_msg = "操作失败";
            $this -> return_msg = false;
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = '';
            $this -> return_msg = $res;
            $this->display();
        }


    }


     /**
     * 设置商品上架状态及有效状态 BY linian 备用(上边的方法有问题)
     */
    public function setGoodsUnsell_t()
    {
    	$this -> error = 0;
    	$this -> return_sql = '';
    	$this -> return_msg = 1;
    	$this->display();
    	
        $s_time = microtime();
        if(isset($this->filter['change']) && !empty($this->filter['change'])){
            $data = $this->filter['change'];
        }else{
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "参数有误";
            $this->display();
        }
        if(isset($this->filter['where']) && !empty($this->filter['where'])){
            $where = $this->filter['where'];
        }else{
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "参数有误";
            $this->display();
        }

       	 
        if(count($data) != count($where)){
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "参数有误";
            $this->display();
        }
        foreach($where as $key=>$val){
        	$sql = "update `base_salepolicy_goods` set `is_valid`={$data[$key]['is_valid']},`is_sale`={$data[$key]['is_sale']} where `goods_id`='{$val['goods_id']}'";
       		$res = $this->db->query($sql);
       		if(!$res){
	       		$this -> error = 1;
	            $this -> return_sql = '';
	            $this -> error_msg = "操作失败";
	            $this -> return_msg = false;
	            $this->display();
       		}
        }       
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$res){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "操作失败";
            $this -> return_msg = false;
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = '';
            $this -> return_msg = $res;
            $this->display();
        }


    }




    /**
     * 检查优惠券code是否有效，有效返回该条数据
     */
    public function checkCouponCode()
    {
        $s_time = microtime();
        if(!isset($this->filter['coupon_code']) || empty($this->filter['coupon_code'])){
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "参数有误";
            $this->display();
        }
        $sql = "select `coupon_price` from `base_coupon` where `coupon_status`=1 and `coupon_code`='{$this->filter['coupon_code']}'";
        $res = $this->db->getRow($sql);

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$res){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "操作失败";
            $this -> return_msg = false;
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }


    }

    /**
     * 回写使用优惠券的订单号
     */
    public function updateCouponInfo()
    {
        $s_time = microtime();
        if(!isset($this->filter['coupon_code']) || empty($this->filter['coupon_code'])){
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "参数有误";
            $this->display();
        }
        if(!isset($this->filter['order_sn']) || empty($this->filter['order_sn'])){
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "参数有误";
            $this->display();
        }
        $sql = "update `base_coupon` set `order_sn`='{$this->filter['order_sn']}',`use_time`='".date("Y-m-d H:i:s")."',`coupon_status`=2 where `coupon_code`='{$this->filter['coupon_code']}'";
        $res = $this->db->query($sql);

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$res){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "操作失败";
            $this -> return_msg = false;
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }


    }


    /*
     *  获取商品的单条记录
     *  goods_id
     */
    public function getBaseSaleplicyGoods() {
        $s_time = microtime();
        $sql = "SELECT * FROM `base_salepolicy_goods` WHERE  ";

		if(isset($this->filter['goods_id']) && !empty(trim($this->filter['goods_id']))){
			$goods_id = trim($this->filter['goods_id']);
			$sql .="  `goods_id`= '".$goods_id."'";
		}else{
			$this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "货号不能为空";
			$this->display();
		}

	   $row = $this->db->getRow($sql);
        // 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
    }
    
    //根据app_salepolicy_goods id 查info
        public function  AppSalePolicyGoodsById(){
            if(isset($this->filter['_ids']) && !empty(trim($this->filter['_ids']))){
                $sql = "SELECT `id`,`policy_id`,`goods_id`,`chengben`,`sale_price`,`jiajia`,`sta_value`,`chengben_compare`,`isXianhuo`,`create_time`,`create_user`,`check_time`,`check_user`,`status`,`is_delete` FROM `app_salepolicy_goods` WHERE `id` in (".$this->filter['_ids'].")";
                $res = $this->db->getAll($sql);
                if (!$res) {
                    $this->error = 1;
                    $this->return_sql = $sql;
                    $this->error_msg = "没有查到有用的数据";
                    $this->return_msg = array();
                    $this->display();
                }else{
                    $this->error = 0;
                    $this->return_sql = $sql;
                    $this->error_msg = "成功";
                    $this->return_msg = $res;
                    $this->display();
                }
            }else{
                $this -> error = 1;
                $this -> return_sql = '';
                $this -> error_msg = "没有传必须参数id";
                $this -> return_msg = array();
                $this->display();
            }
        }
   /**
    * 根据goods_id修改base_salepolicy_goods表单条记录
    */      
   public function updateBaseSalepolicyGoods(){
   		$this -> error = 0;
   		$this -> return_sql = '';
   		$this -> return_msg = 1;
   		$this->display();
   	
       $s_time = microtime();
              
       $data = empty($this->filter['data'])?array():$this->filter['data'];
       if(empty($data)){
           $this -> error = 1;
           $this -> return_sql = '';
           $this -> error_msg = "修改字段为空";
           $this -> return_msg = false;
           $this->display();
       }
       
       if(!isset($this->filter['goods_id']) || empty(trim($this->filter['goods_id']))){
           $this -> error = 1;
           $this -> return_sql = $sql;
           $this -> error_msg = "商品货号不能为空";
           $this->display();
       }
       
       $sql = 'update base_salepolicy_goods set ';
       foreach($data as $key=>$vo){
           $sql.="`{$key}`='{$vo}',";
       }
       
       $sql = trim($sql,',').' where 1=1 ';
       $goods_id = trim($this->filter['goods_id']);
       $sql .="and `goods_id`= '".$goods_id."' ";

       $res = $this->db->query($sql);
       $reponse_time = microtime() - $s_time;
       $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
       
       //返回信息
       if(!$res){
           $this -> error = 1;
           $this -> return_sql = $sql;
           $this -> error_msg = "操作失败";
           $this -> return_msg = false;
           $this->display();
       }else{
           $this -> error = 0;
           $this -> return_sql = $sql;
           $this -> return_msg = $res;
           $this->display();
       }
   }

    /*------------------------------------------------------ */
	//-- 返回内容
	//-- by col
	/*------------------------------------------------------ */
	public function display()
	{
		$res = array("error" => intval($this -> error), "error_msg" => $this -> error_msg, "return_msg" => $this -> return_msg, "return_sql" => $this -> return_sql);
		die (json_encode($res));
	}

	/*------------------------------------------------------ */
	//-- 记录日志信息
	//-- by haibo
	/*------------------------------------------------------ */
	public function recordLog($api, $response_time, $str)
	{
        define('ROOT_LOG_PATH',str_replace('api/api.php', '', str_replace('\\', '/', __FILE__)));
		if (!file_exists(ROOT_LOG_PATH . 'logs/api_logs'))
		{
			mkdir(ROOT_LOG_PATH . 'logs/api_logs', 0777);
			chmod(ROOT_LOG_PATH . 'logs/api_logs', 0777);
		}
		$content = $api."||".$response_time."||".$str."||".date('Y-m-d H:i:s')."\n";
		$file_path =  ROOT_LOG_PATH . 'logs/api_logs/'.date('Y')."_".date('m')."_".date('d')."_api_log.txt";
		file_put_contents($file_path, $content, FILE_APPEND );
	}
}
?>
