<?php
/**
 * This contains the Retrieval API .
 *
 */
class api {

    private $db = null;
    private $error_msg = '';
    private $return_msg = '';
    private $return_sql = '';
    private $filter = array();
    public function __construct($_filter)
    {
        global $config;
        $this->db= new KELA_API_DB($config);
		$this->filter = $_filter;
    }

    /**
     * 查询供应商信息
     * @param id
     * @param name
     * @param code
     * @param is_open
     * @return json
     */
    public function GetProcessorList() {
        $s_time = microtime();
        $where = '';
        $sql = "SELECT `id`, `code`, `name`, `business_scope`, `is_open`, `password`, `business_license`, `tax_registry_no`, `business_license_region`, `business_license_address`, `pro_region`, `pro_address`, `cycle`, `pay_type`, `tax_invoice`, `tax_point`, `balance_type`, `purchase_amount`, `pro_contact`, `pro_phone`, `pro_qq`, `contact`, `kela_phone`, `kela_qq`, `bank_name`, `account_name`, `account`, `is_invoice`, `pro_email`, `status`, `create_time`, `create_user`, `info` FROM `app_processor_info` WHERE 1";
        if (isset($this->filter['id'])) {
            $id = intval(trim($this->filter['id']));
            if (!empty($id)) {
                $sql .= " and `id` = " . $id;
            }
        }
        if (!empty($this->filter['name'])) {
            $sql .= " and `name` = '{$this->filter['name']}'";
        }
        if (!empty($this->filter['code'])) {
            $sql .= " and `code` = '{$this->filter['code']}'";
        }
        if (!empty($this->filter['is_open'])) {
            $sql .= " and `is_open` = '{$this->filter['is_open']}'";
        }
        if (!empty($this->filter['status'])) {
            $sql .= " and `status` = '{$this->filter['status']}'";
        }
        $sql .= " ORDER BY `id` DESC";
        $data['pageSize'] = isset($this->filter['pageSize']) && (int) $this->filter['pageSize'] > 1 ? (int) $this->filter['pageSize'] : 10;
        $data['recordCount'] = $this->db->getOne($sql);
        $data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
        $data['page'] = isset($this->filter['page']) && (int) $this->filter['page'] > 1 ? (int) $this->filter['page'] : 1;
        $data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
        $data['isFirst'] = $data['page'] > 1 ? false : true;
        $data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
        $data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
	$sql .= " limit ".($data['start']-1).','.$data['pageSize'];

        $data['data'] = $this->db->getAll($sql);

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if (!$data['data']) {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->error_msg = "未查询到供应商数据";
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        }
    }

    /**
     * 查询供应商名称
     * @param id
     * @param name
     * @param code
     * @param is_open
     * @return json
     */
    public function GetProcessorName() {
        $s_time = microtime();
        $where = '';
        $sql = "SELECT main.`name`,main.`id`,i.opra_uname FROM `app_processor_info` as main left join product_factory_oprauser as i on i.prc_id=main.id WHERE 1";
        $id =isset($this->filter['id'])?intval(trim($this->filter['id'])):0;
        $flag = isset($this->filter['flag'])?intval(trim($this->filter['flag'])):FALSE;
        
        if (!empty($id)) {
            $sql .= " and main.`id` = " . $id;
            if($flag){
                $data['data'] = $this->db->getRow($sql);
            }else{
                $data['data'] = $this->db->getOne($sql);
            }
        }else{
			if(isset($this->filter['ids']) and !empty($this->filter['ids']))
			{
				$sql .= " and main.`id` in({$this->filter['ids']})";
			}
            $data['data'] = $this->db->getAll($sql);
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if (!$data['data']) {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->error_msg = "未查询到此供应商";
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        }
    }

    /**
     * 获取供应商名称
     */
    public function GetSupplierName(){
        $s_time = microtime();
        $where = '';
        $sql = "SELECT `name` FROM `app_processor_info` WHERE 1";
        $id = intval(trim($this->filter['id']));
        if (!empty($id)) {
            $sql .= ' and `id` = ' . $id;
            $data['data'] = $this->db->getOne($sql);
        }else{
            $data['data'] = $this->db->getAll($sql);
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if (!$data['data']) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = "未查询到此供应商";
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        }
    }

    /**
     * 获取布产状态
     */
    public function get_bc_status(){
        $s_time = microtime();
		$sql = "SELECT `status` FROM `product_info` WHERE 1";
		if(isset($this->filter['p_id']) && $this->filter['p_id'] != '') {
			$sql.=" and `p_id` = '{$this->filter['p_id']}'";
        }
        if(isset($this->filter['cg_sn']) && $this->filter['cg_sn'] != '') {
			$sql.=" and `p_sn` = '{$this->filter['cg_sn']}'";
        }
        if(isset($this->filter['style_sn']) && $this->filter['style_sn'] != '') {
			$sql.=" and `style_sn` = '{$this->filter['style_sn']}'";
        }
        $res = $this->db->getOne($sql);
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        //返回信息
        if (!$res) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = 0;
            $this->return_msg = 0;
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $res;
            $this->display();
        }
    }

    //采购修改布产商品信息
    public function UpdatePurGoodsAttr(){
        $s_time = microtime();

        if(isset($this->filter['update_info']) && $this->filter['update_info'] != '') {
            $updata = $this->filter['update_info'];
        }
        if(isset($this->filter['base']) && $this->filter['base'] != '') {
            $base = $this->filter['base'];
        }
        if(isset($this->filter['label']) && $this->filter['label'] != '') {
            $label = $this->filter['label'];
        }
        if(!isset($updata) || !isset($base) || !isset($label)){
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "参数有误";
            $this->return_msg = false;
            $this->display();
        }

        $sql = "SELECT `id` FROM `product_info` WHERE `p_sn` = '".$base['cg_sn']."' AND `style_sn` = '".$base['style_sn']."'  and `p_id`='{$base['p_id']}' ORDER BY `id` DESC";
        $p_id = $this->db->getOne($sql);

        if(!$p_id){
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = "未找到布产信息";
            $this->return_msg = false;
            $this->display();
        }

        $pdo = $this->db->db();
        try{
            $pdo->beginTransaction();//开启事务处理
            //开始修改信息
            $fields = ['style_sn','num','g_name','xiangqian','info'];
            foreach ($updata as $k=>$v) {
                if(in_array($k,$fields)){
                    if($k == 'g_name'){$k = 'goods_name';}
                    $sql = "UPDATE `product_info` SET `".$k."` = '".$v."' WHERE `id` = '".$p_id."'";
                }else{
//                    $sql = "SELECT `value` FROM `product_info_attr` WHERE `g_id` = '".$p_id."' AND `code` = '".$k."'";
//                    $res = $this->db->getOne($sql);
//                    if($res){//update
                       $sql = "UPDATE `product_info_attr` SET `value` = '".$v."' WHERE `g_id` = '".$p_id."' AND `code` = '".$k."'";
//                    }else{//insert
//                        $sql = "INSERT INTO `product_info_attr` (`g_id`,`name`,`code`,`value`) VALUES (".$p_id.",'{$label[$k]}','{$k}','{$v}')";
//                    }
                }
                $pdo->exec($sql);
            }
        }catch(PDOException $e){
            $pdo->rollback();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
        }
        $res = $pdo->commit();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        //返回信息
        if (!$res) {
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "信息修改失败";
            $this->return_msg = $res;
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = '';
            $this->return_msg = $res;
            $this->display();
        }
    }

    /**
     * 获取供应商ID,NAME数组 采购和仓储都在调用，莫要删除和减少条件
     */
    public function GetSupplierList(){
        $s_time = microtime();
        $sql = "SELECT  `id`,`code`,`name` FROM `app_processor_info` WHERE 1";

		 if (!empty($this->filter['status'])) {
            $sql .= " and `status` = '{$this->filter['status']}'";
        }
        if (!empty($this->filter['code'])) {
            $sql .= " and `code` = '{$this->filter['code']}'";
        }
        if (!empty($this->filter['p_id'])) {
            $sql .= " and `id` = '{$this->filter['p_id']}'";
        }

		$sql .= " order by id desc";

        $data['data'] = $this->db->getAll($sql);
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        //返回信息
        if (!$data['data']) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = "未查询到此供应商";
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        }

    }

    public function GetProcessorId() {
        $s_time = microtime();
        $sql = "SELECT `pi`.`id` as prc_id ,`pi`.`name` as prc_name,`pw`.* FROM `app_processor_info` as `pi`,`app_processor_worktime` as `pw`
                WHERE `pi`.`id`=`pw`.`processor_id` ";
        if (isset($this->filter['processor_id']) && $this->filter['processor_id'] != '') {
            $sql .= " and pw.processor_id=".$this->filter['processor_id'];
        }

        $data = $this->db->getAll($sql);

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        if(!$data) {
            $this->error = 1;
            $this->error_msg = '没有查询到信息';
            $this->return_msg = array();
            $this->display();
        }else{
            $this->error = 0;
            $this->error_msg = '';
            $this->return_msg = $data;
            $this->display();
        }

    }

    /**
     * 获取一条加工时间数据
     */
    public function GetWorktimeInfoByProcessorId() {
        $s_time = microtime();
        $sql = "select pw_id,processor_id,normal_day,wait_dia,behind_wait_dia,ykqbzq,order_problem,is_rest from `app_processor_worktime` where 1 ";
        if(isset($this->filter['processor_id']) && $this->filter['processor_id'] != ''){
            $sql .= " and processor_id='".$this->filter['processor_id']."'";
        }
        $data = $this->db->getAll($sql);
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        if(!$data) {
            $this->error = 1;
            $this->error_msg = '没有查询到信息';
            $this->return_msg = array();
            $this->display();
        }else{
            $this->error = 0;
            $this->error_msg = '';
            $this->return_msg = $data;
            $this->display();
        }

    }

    /**
     * 获取供应商支付信息
     */
    public function GetSupplierPay(){
        $s_time = microtime();
        $sql = "SELECT `id`,`name`,`balance_type`,`bank_name`,`account_name`,`account` FROM `app_processor_info` WHERE 1";
        $id = intval(trim($this->filter['id']));
        if (!empty($id)){
            $sql .= " and `id` = " . $id;
        }
        $data['data'] = $this->db->getRow($sql);

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        //返回信息
        if (!$data['data']) {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->error_msg = "未查询到此供应商";
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        }

    }    
    
    /**
     * 布产列表添加一条数据
	 * 这个接口涉及到订单布产和采购布产两方面的数据来源，所以接口调整后，其他两个地方务必都要调整
	 *  JUAN
     */
    public function AddProductInfo() {
        $s_time = microtime();
		$arr = array();//二位数组用来存放 订单明细id 、布产号 返回值
        $_id = 0;
        $CStyleModel = new CStyleModel(11); //跨模块款书库类
        $order_bc_event_data = array();
        //特殊属性code值映射
        $attrCodeMaps = array('18k_color'=>'jinse','yanse'=>'color','zuanshidaxiao'=>'cart','g_name'=>'goods_name','bc_type'=>'bc_style');
        //特殊属性name值映射
        $attrNameMaps = array(
            'face_work'=>'表面工艺', 'caizhi'=>'材质', 'jinse'=>'金色', 'zhiquan'=>'指圈', 'cart'=>'主石单颗重','zhushi_num'=>'主石粒数', 'xiangkou'=>'镶口', 'cert'=>'证书类型','zhengshuhao'=>'证书号', 'color'=>'主石颜色', 'jingdu'=>'主石净度', 'kezi'=>'刻字', 'info'=>'布产备注'
        );
        if (isset($this->filter['insert_data']) && isset($this->filter['from_type'])) {
            $info = $this->filter['insert_data'];
            $num = count($info);
            if ($num > 0) {
                $from_type = $this->filter['from_type'];//来源：1、采购 2、订单
                //事务添加数据，如果处理错误则返回false
                define("root_path",dirname(dirname(dirname(dirname(__FILE__)))));
                require_once(root_path.'/frame/init.php');
                $pdo = DB::cn(14)->db();//pdo对象
                try{
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
                    $pdo->beginTransaction();//开启事务
					$time=date('Y-m-d H:i:s');
                    foreach ($info as $key => $value){
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
						$sql = "INSERT INTO `product_info`(`bc_sn`, `p_id`, `p_sn`, `style_sn`, `status`, `num`, `prc_id`,`prc_name`, `opra_uname`, `add_time`, `edit_time`, `info`,`from_type`,`consignee`,`bc_style`,`goods_name`,`xiangqian`,`customer_source_id`,`channel_id`,`caigou_info`,`create_user`,`is_alone`,`qiban_type`,`diamond_type`,`origin_dia_type`,`to_factory_time`) VALUES ('',{$value['p_id']},'{$value['p_sn']}','{$value['style_sn']}',{$value['status']},{$value['num']},{$value['prc_id']},'{$value['prc_name']}','{$value['opra_uname']}','{$time}','{$time}','{$value['info']}',{$from_type},'{$value['consignee']}','{$value['bc_style']}','{$value['goods_name']}','{$value['xiangqian']}','{$value['customer_source_id']}','{$value['channel_id']}','{$value['caigou_info']}','{$value['create_user']}','{$value['is_alone']}','{$value['qiban_type']}','{$value['diamond_type']}','{$value['origin_dia_type']}','{$value['to_factory_time']}')";
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
                                $this->error = 1;
                                $this->return_sql = $sql;
                                $this->error_msg = "4C裸钻基本信息为空";
                                $this->return_msg = 0;
                                $this->display();
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
						
						$bc_sn = BCD_PREFIX.$_id;
						if ($from_type == '2') {
						    $bc_sn = $this->create_bc_sn($value['p_sn'], $_id);
						    $order_bc_event_data[$_id] = $value['p_id'];
						    
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
						$arr[$key]['final_bc_sn'] = $bc_sn;
						$arr[$key]['final_bc_id'] = $_id;
						
                        $pdo->query("UPDATE `product_info` SET bc_sn = '".$bc_sn."',is_peishi=".$is_peishi." WHERE id =".$_id);
                        //获取款式主石，副石相关属性列表
                        $attrExt = $CStyleModel->getStoneAttrList($value['style_sn'],$value['attr']);
                        if(!empty($attrExt)){
                            $value['attr'] = array_merge($value['attr'],$attrExt);
                        }
                        //$logss =  var_export($value['attr'],true);
                        //file_put_contents('buchan.txt',$logss);
                        //插入属性
						$t = "";
                        foreach($value['attr'] as $k => $v)
                        {                 
                            //特殊字段映射，主要针对 采购单 布产属性code不统一问题
                            $v['code'] = isset($attrCodeMaps[$v['code']])?$attrCodeMaps[$v['code']]:$v['code'];
                            //特殊字段映射，主要针对 采购单 布产属性name不统一问题
                            $v['name'] = isset($attrNameMaps[$v['code']])?$attrNameMaps[$v['code']]:$v['name'];
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
                        }   
                    }
                    
                    $pdo->commit();//如果没有异常，就提交事务
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                } catch(Exception $e) {//捕获异常
                  //  print_r($e);exit;
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                    $this->error = 1;
                    $this->return_sql = $sql;
                    $this->error_msg = "数据异常，推送布产信息失败。".$sql;
                    $this->return_msg = 0;
                    $this->display();
                }
            } else {
                $this->error = 1;
                $this->return_sql = '';
                $this->error_msg = "insert_data是个空数组";
                $this->return_msg = 0;
                $this->display();
	        }
        } else {
	        $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数";
            $this->return_msg = 0;
            $this->display();
        }

        // 记录日志
        //$reponse_time = microtime() - $s_time;
        //$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        // 代码执行到这里，说明已经没有错误，根据布产单来源情况触发事件
        if (!empty( $order_bc_event_data) ) {
            //AsyncDelegate::dispatch('buchan', array('event' => 'order_bcd_upserted', 'bc_infos' => $order_bc_event_data));
        }
        //返回信息
        $this->error = 0;
        $this->return_sql = "";
        $this->return_msg = $arr;
        $this->display();
    }

	 /**
     * 取一条布产详情---JUAN
     */
    public function GetProductInfo() {
        $s_time = microtime();
        $where = '';
        $sql = "SELECT `id`, `bc_sn`, `p_id`, `p_sn`, `style_sn`, `status`, `num`, `prc_id`, `prc_name`, `opra_uname`, `add_time`, `esmt_time`, `rece_time`, `info`,`from_type`,`consignee`,`is_alone` FROM `product_info` WHERE 1";

        if (!empty($this->filter['bc_sn'])) {
            $bc_sn = $this->filter['bc_sn'];
            $sql .= " and `bc_sn` = '{$bc_sn}'";
        }
        if (!empty($this->filter['consignee'])) {
            $consignee = $this->filter['consignee'];
            $sql .= " and `consignee` like '%".$consignee."%'";
        }
        if (!empty($this->filter['order_sn'])) {
            $order_sn = $this->filter['order_sn'];
            $sql .= " and `p_sn`='{$order_sn}'";
        }
        if (!empty($this->filter['p_id'])) {
            $p_id = $this->filter['p_id'];
            $sql .= " and `p_id`='{$p_id}' ";
        }
        $sql .= " ORDER BY `id` DESC";

        if (!empty($consignee)){
            $data = $this->db->getAll($sql);
        }else{
            $data = $this->db->getRow($sql);
        }


        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if (!$data) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = "未查询到此订单";
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        }
    }
    /**
     * 根据款号取信息 by Linphie 
     */
    public function GetProductAttr(){
        $s_time = microtime();
        $where = '';
        $sql ="select i.* from product_info as p left join product_info_attr as i on p.id=i.g_id ";
        if(!empty($this->filter['style_sn'])){
            $sql .= " where  p.`style_sn` = '{$this->filter['style_sn']}'";
        }
        $sql .= " ORDER BY i.`id` DESC";
        $data = $this->db->getAll($sql);
         // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if (!$data) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = "未查询到此款";
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        }
    }
		 /**
     * 取一条布产详情及相关属性---JUAN
     */
    public function GetProductInfoDatail() {
        $s_time = microtime();
        $where = '';

		$sql ="select i.* from product_info as p left join product_info_attr as i on p.id=i.g_id ";
        if (!empty($this->filter['bc_sn'])) {
            $sql .= " where  p.`bc_sn` = '{$this->filter['bc_sn']}'";
        }
        $sql .= " ORDER BY i.`id` DESC";
        $info_detail = $this->db->getAll($sql);

        $sql = "SELECT `id`, `bc_sn`, `p_id`, `p_sn`, `style_sn`, `status`, `num`, `prc_id`, `prc_name`, `opra_uname`, `add_time`, `esmt_time`, `rece_time`, `info`,`from_type`,`consignee`,`xiangqian`,`bc_style`,`factory_opra_status` FROM `product_info` WHERE 1";
		 if (!empty($this->filter['bc_sn']))
		{
            $sql .= " and  `bc_sn` = '{$this->filter['bc_sn']}'";
        }
		$info = $this->db->getRow($sql);
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if (!$info) {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->error_msg = "未查询到此订单";
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = array('info_detail'=>$info_detail,'info'=>$info);
            $this->display();
        }
    }
    
    /**
     * 验证订单期货商品是否已布产
     * @param int $goods_id 商品明细id
     * @param string $order_sn 订单号
     * @return array 布产明细
     */
    public function CheckGoodsProductInfo() {
        $s_time = microtime();
        $sql = "SELECT `gr`.* FROM `product_goods_rel` as `gr`,`product_info` as `pi` WHERE `gr`.`bc_id`=`pi`.`id` and `gr`.`status` = 0 ";

        if (!empty($this->filter['goods_id'])) {
            $sql .= " and `gr`.`goods_id` = {$this->filter['goods_id']}";
        }else{
            $this->error = 1;
            $this->error_msg = "缺少参数goods_id";
            $this->display();
        }
        
        if (!empty($this->filter['order_sn'])) {
            $sql .= " and `pi`.`p_sn` = '{$this->filter['order_sn']}'";
        }else{
            $this->error = 1;
            $this->error_msg = "缺少参数order_sn";
            $this->display();
        }

        $data = $this->db->getRow($sql);

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if (!$data) {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->error_msg = "没有数据";
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        }
    }
    
    
	 /**
     * 订单货品和布产单关系 根据布产号id查询是否有绑定关系
     */
    public function GetGoodsRelInfo() {
        $s_time = microtime();
        $where = '';
        $sql = "SELECT * FROM `product_goods_rel` WHERE 1 and status =0 ";

        if (!empty($this->filter['bc_id'])) {
            $sql .= " and `bc_id` = '{$this->filter['bc_id']}'";
        }

        if (!empty($this->filter['goods_id'])) {
            $sql .= " and `goods_id` = '{$this->filter['goods_id']}'";
        }

        $sql .= " ORDER BY `id` DESC";

        $data = $this->db->getRow($sql);

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if (!$data) {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->error_msg = "没有数据";
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        }
    }
	/**
	* 取log日志
	**/
	public function getBcLog()
	{
		 $s_time = microtime();
        $where = '';
        $sql = "SELECT log.`id`, log.`bc_id`,info.`bc_sn`, log.`status`, log.`remark`, log.`uid`, log.`uname`, log.`time` FROM `product_opra_log` AS log LEFT JOIN `product_info` AS info ON log.bc_id = info.id WHERE 1";

        if (!empty($this->filter['bc_sn'])) {
            $sql .= " and info.`bc_sn` = '{$this->filter['bc_sn']}'";
        }

        $sql .= " ORDER BY log.`id` DESC";

        $data = $this->db->getAll($sql);

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if (!$data) {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->error_msg = "未查询到此订单";
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        }
	}

	/**
	 *   接口功能：订单申请退货，解除订单和布产单的关系。 BY linian
	 */
	public function relieveProduct() {
		$s_time = microtime();
		$where = '';
		//事务开始
		//require_once('/frame/init.php');
		define("root_path",dirname(dirname(dirname(dirname(__FILE__)))));
		require_once(root_path.'/frame/init.php');
		$pdo = DB::cn(14)->db();//pdo对象
		//$pdo = $this->db;
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//接收货号ID 数组
			$info = $this->filter['arr'];
			//判断传递数据必须设置
			if(isset($this->filter['arr']) && count($info)>0){
				foreach ($info as $key => $value){
					//根据货号goods_id 连表查询是否存在布产信息
					$sql = "SELECT  pg.`bc_id`, pg.`goods_id`,p.`status` FROM `product_goods_rel` as pg left join `product_info` as p on pg.`bc_id`=p.`id` WHERE 1";
					if (!empty($value)) {
						$sql .= " and pg.`goods_id` = '{$value}'";
					}

					$res = $this->db->getRow($sql);
                    $this->error = 21;
                    $this->return_sql = $sql;
                    $this->error_msg = $res;
                    $this->return_msg = 0;
                    $this->display();
					if(!$res){
						$this->error = 1;
						$this->return_sql = $sql;
						$this->error_msg = "货品ID错误，与布产不存在关联";
						$this->return_msg = $value;
						$this->display();
					}
					//判断布产状态 若 已经生产
					if($res['status']<=3){
						//更新布产表中 生产状态为停止生产
						$sql = "UPDATE `product_info` SET `status` = 10  WHERE `id` =".$res['bc_id'];
						$this->db->query($sql);

					}
					//更新关系表中状态为无效
					$sql = "UPDATE `product_goods_rel` SET `status` = 1 WHERE `bc_id` =".$res['bc_id'];
					$this->db->query($sql);

				}
			}

		}catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			$this->error = 1;
			$this->return_sql = '';
			$this->error_msg = "数据异常，推送布产信息失败。";
			$this->return_msg = 0;
			$this->display();
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交

		$this->error = 0;
		$this->return_sql = $sql;
		$this->error_msg = "已解除成功";
		$this->display();
	}

    /**
     * 添加布产修改信息
     * 传递参数: apply_data  类型array()
     * KEY：detail_id，apply_info，old_info,apply_id，apply_name
     */
    public function AddProductApplyInfo(){
        $s_time = microtime();
        $key = ['detail_id','apply_info','apply_id','old_info','apply_name'];
        $newdo = array();
        if (isset($this->filter['apply_data']) &&!empty($this->filter['apply_data'])) {
            $newdo = $this->filter['apply_data'];
        }else{
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "参数有误";
            $this->return_msg = 0;
            $this->display();
        }
        foreach ($key as $v) {
           if(!array_key_exists($v,$newdo)){
               $this->error = 1;
               $this->return_sql = '';
               $this->error_msg = "缺少参数".$v;
               $this->return_msg = 0;
               $this->display();
           }
        }
        if(!is_array($newdo['apply_info'])){
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "请检查参数'apply_info'";
            $this->return_msg = 0;
            $this->display();
        }
        if(!is_array($newdo['old_info'])){
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "请检查参数'old_info'";
            $this->return_msg = 0;
            $this->display();
        }
        $tmp = end($newdo['apply_info']);
        if($tmp['code'] == 'special'){
            $newdo['special'] = $tmp['value'];
        }
        $newdo['apply_info'] = serialize($newdo['apply_info']);
        $newdo['old_info'] = serialize($newdo['old_info']);

        $sql = "SELECT `p_sn` AS order_sn,`bc_sn`,`style_sn` FROM `product_info` WHERE `from_type`='2' AND `p_id`='".$newdo['detail_id']."'";
        $info = $this->db->getRow($sql);

        if(!$info){
            $this->error = 2;
            $this->return_sql = $sql;
            $this->error_msg = "未查到该商品布产信息";
            $this->return_msg = 0;
            $this->display();
        }else{
            $newdo['order_sn'] = $info['order_sn'];
            $newdo['bc_sn'] = $info['bc_sn'];
            $newdo['style_sn'] = $info['style_sn'];
            $newdo['apply_time'] = date('Y-m-d H:i:s');
        }
        $res = $this->db->autoExecute('product_apply_info',$newdo);

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if (!$res) {
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = '失败';
            $this->return_msg = false;
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = '';
            $this->return_msg = true;
            $this->display();
        }

    }

    /**
     * 通过布产获取客户姓名
     */
    public function getConsigneeBybc_sn(){
        $s_time = microtime();
        if (isset($this->filter['bc_sn']) &&!empty($this->filter['bc_sn'])){
            $bc_sn = $this->filter['bc_sn'];
        }else{
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数bc_sn";
            $this->return_msg = 0;
            $this->display();
        }

        $sql = "SELECT `consignee`,`p_sn`,`from_type` FROM `product_info` WHERE `bc_sn` = '".$bc_sn."'";
        $res = $this->db->getRow($sql);

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if (!$res) {
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = '';
            $this->return_msg = false;
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = '';
            $this->return_msg = $res;
            $this->display();
        }
    }

    /**
     * 根据订单号，获取布产单和客户姓名
     */
    public function getConsigneeOrder_sn(){
        $s_time = microtime();
        if (isset($this->filter['order_sn']) &&!empty($this->filter['order_sn'])){
            $p_sn = $this->filter['order_sn'];
        }else{
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数order_sn";
            $this->return_msg = 0;
            $this->display();
        }

        $sql = "SELECT `consignee`,`p_sn`,`from_type`,`bc_sn` FROM `product_info` WHERE `p_sn` = '".$p_sn."'";
        $res = $this->db->getRow($sql);

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if (!$res) {
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = '';
            $this->return_msg = false;
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = '';
            $this->return_msg = $res;
            $this->display();
        }
    }

	/*
	*通过用户ID查相关联的供应商
	*add by zhangruiying
	*/
	function getPrcIdsByUserId()
	{
		$this->error = 1;
		$this->return_msg =array();
		if (!isset($this->filter['id']) or empty($this->filter['id'])){
             $this->error_msg = "缺少参数id";
        }
		else
		{
			$sql = "SELECT `prc_id` FROM `product_factory_oprauser` WHERE `opra_user_id` = '".$this->filter['id']."'";
			$res = $this->db->getAll($sql);
			$arr=array();
			if($res)
			{
				foreach($res as $key=>$v)
				{
					$arr[]=$v['prc_id'];
				}
			}
			$this->return_sql =$sql;
			if (!$res) {
				$this->error_msg = '';
			} else {
				$this->error = 0;
				$this->return_msg =$arr;
			}
		}
		$this->display();

	}
	/*
	* 添加布产日志
	* add by linian
	*/
	public function AddOrderLog()
	{


		$bc_id=$this->filter['rec_id'];
		$create_user=$this->filter['create_user'];
		$create_user_id=$this->filter['create_user_id'];
		$remark=$this->filter['remark'];
		$sql="select id,status from product_info where bc_sn='{$bc_id}'";
		$row=$this->db->getRow($sql);
		$sql="insert into product_opra_log(`bc_id`,`status`,`uid`,`uname`,`time`,`remark`) values ('{$row['id']}','{$row['status']}','{$create_user_id}','{$create_user}','".date('Y-m-d H:i:s')."','{$remark}')";

		$res = $this->db->query($sql);
		if($this->filter['weixiu_status']){
			$weixiu_status = $this->filter['weixiu_status'];
			$sql="UPDATE product_info  SET weixiu_status={$weixiu_status}  WHERE id='{$row['id']}'";
            $res = $this->db->query($sql);
		}

		//返回信息
		if($res)
		{
			$this -> error = 0;
			$this -> return_sql =$sql;
			$this -> return_msg =true;

		}
		else
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> return_msg =false;

		}
		$this->display();
	}

    /* ------------------------------------------------------ */

    //-- 返回内容
    //-- by col
    /* ------------------------------------------------------ */
    public function display() {
        $res = array("error" => intval($this->error), "error_msg" => $this->error_msg, "return_msg" => $this->return_msg, "return_sql" => $this->return_sql);
        die(json_encode($res));
    }

    /* ------------------------------------------------------ */

    //-- 记录日志信息
    //-- by haibo
    /* ------------------------------------------------------ */
    public function recordLog($api, $response_time, $str) {
        define('ROOT_LOG_PATH', str_replace('api/api.php', '', str_replace('\\', '/', __FILE__)));
        if (!file_exists(ROOT_LOG_PATH . 'logs/api_logs')) {
            mkdir(ROOT_LOG_PATH . 'logs/api_logs', 0777);
            chmod(ROOT_LOG_PATH . 'logs/api_logs', 0777);
        }
        $content = $api . "||" . $response_time . "||" . $str . "||" . date('Y-m-d H:i:s') . "\n";
        $file_path = ROOT_LOG_PATH . 'logs/api_logs/' . date('Y') . "_" . date('m') . "_" . date('d') . "_api_log.txt";
        file_put_contents($file_path, $content, FILE_APPEND);
    }
	//add by zhangruiying
	public function getProductInfoByIDS()
	{
		if (!isset($this->filter['ids']) or empty($this->filter['ids'])){
			$this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "参数为空或非法，必须是数组";
            $this->return_msg = 0;
        }else{
			$ids="'".implode("','",$this->filter['ids'])."'";
			$sql="select style_sn,bc_sn from product_info where bc_sn in($ids)";
			$res = $this->db->getAll($sql);
			if($res!=false)
			{
				$arr=array();
				foreach($res as $k=>$v)
				{
					$arr[$v['bc_sn']]=$v['style_sn'];
				}
				$this->error = 0;
				$this->return_sql = $sql;
				$this->error_msg = "";
				$this->return_msg =$arr;
			}
			else
			{
				$this->error = 1;
				$this->return_sql = $sql;
				$this->error_msg = "程序执行出错请检查";
				$this->return_msg = 0;
			}
        }
		$this->display();

	}
	//获取当前供应商相关联的供应商add by zhangruiying
	function GetRelFactoryIds()
	{
		$sql="select main.id from app_processor_info as main  left join product_factory_oprauser as fu on fu.prc_id=main.id ";
		$str="";
		if(isset($this->filter['id']) and !empty($this->filter['id'])){
			$str.=" AND main.id in(select 473 as supplier_id union select supplier_id from app_processor_group where group_id=(select group_id from app_processor_group where supplier_id={$this->filter['id']}))";

			if(isset($this->filter['opra_user_same']) and $this->filter['opra_user_same']==true)
			{
				$str.=" and fu.opra_user_id=(select opra_user_id from product_factory_oprauser where prc_id={$this->filter['id']})";
			}
		}
		//跟单人和当前供应商相同的关联供应商
		if(!empty($str))
		{
			$str=ltrim($str,' AND');
			$sql.=" where ".$str;
		}
		$row = $this->db->getAll($sql);
		$this->error =0;
		$this->return_sql = $sql;
		$this->return_msg = $row;
		$this->display();

	}

    //根据供应商名称查找id
    public function GetSupplierIdsByName(){
        if (!empty($this->filter['name'])){
            $sql = 'select id from app_processor_info where name like \''.$this->filter['name'].'%\'';
            $res = $this->db->getAll($sql);
            //返回信息
            if (!$res) {
                $this->error = 1;
                $this->return_sql = '';
                $this->error_msg = '';
                $this->return_msg = false;
                $this->display();
            } else {
                $this->error = 0;
                $this->return_sql = '';
                $this->return_msg = $res;
                $this->display();
            }
        }
    }
    //根据布产ID获取商品属性
    public function getProductAttrByBCId(){
        $sql = "select * from product_info_attr where 1=1";
        if (!empty($this->filter['bc_id'])){
            $sql .=" and g_id='".$this->filter['bc_id']."'";
        }else{
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = '布产Id(bc_id)为空 ';
            $this->return_msg = false;
            $this->display();
        }
        $res = $this->db->getAll($sql);

        if(!$res){
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = '查询结果为空';
            $this->return_msg = array();
            $this->display();
        }else{
            $this->error = 0;
            $this->return_sql = $sql;
            $this->error_msg = '查询成功';
            $this->return_msg = $res;
            $this->display();
        } 
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

}

?>
