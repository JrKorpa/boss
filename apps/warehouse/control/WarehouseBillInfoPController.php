<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoPController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-23 21:35:23
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoPController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('printBill','printSum','print_q', 'getWholesaleUser');
	private $_put_in_type = '';
	/****
	获取公司 列表
	****/
	public function company()
	{
		$model     = new CompanyModel(1);
	    $company   = $model->getCompanyTree();//公司列表
		return $company;
	}
	/**
	 * 出库公司 只能是省代或总公司或外协供应商
	 * @return unknown
	 */
	private function getFromCompany($id=0)
	{
	    $model     = new CompanyModel(1);
	    if(SYS_SCOPE == 'boss'){
	        return $model->getCompanyTree();//公司列表	       	        	    
	    }
	    $otherFitler = " OR id in(58,515)";    
	    if($id > 0 ){
	        $otherFitler = " OR id={$id}";
	    }
	    
    	if (SYS_SCOPE == 'zhanting') {
    		$company = $model->select2("*","id={$_SESSION['companyId']} and (is_shengdai=1 {$otherFitler} or company_type = 4)");//公司列表
    	}
	    	    
	    return $company;
	}
	
	
	
	//入库公司（批发客户）
	private function getWhoList($sd_company_id=null)
	{   
	    if($sd_company_id ==null){
	        $sd_company_id = $_SESSION['companyId'];
	    }
	    $model = new WarehouseModel(21);
	    if(SYS_SCOPE == 'boss'){
	        $sql = "select a.`wholesale_id` , a.`wholesale_sn` , a.`wholesale_name`, a.`sign_company`,b.company_name as sign_company_name from warehouse_shipping.jxc_wholesale a left join cuteframe.company b on a.sign_company=b.id where a.wholesale_status = 1";
	    }else if(in_array($sd_company_id,array(58,515))){
	        $sql = "select a.`wholesale_id` , a.`wholesale_sn` , a.`wholesale_name`, a.`sign_company`,b.company_name as sign_company_name from warehouse_shipping.jxc_wholesale a left join cuteframe.company b on a.sign_company=b.id where a.wholesale_status = 1";
	    }else{
	    	$put_in_type = $this->get_put_in_type();
	    	if ($put_in_type) {
	    		return array();
	    		$sql = "select a.`wholesale_id` , a.`wholesale_sn` , a.`wholesale_name`, a.`sign_company`,b.company_name as sign_company_name from warehouse_shipping.jxc_wholesale a left join cuteframe.company b on a.sign_company=b.id where a.wholesale_status = 1";
	    	} else {
	    		// 省代公司，限制批发客户只能是所属当前省代公司的。
	        	$sql = "select a.`wholesale_id` , a.`wholesale_sn` , a.`wholesale_name`, a.`sign_company`,b.company_name as sign_company_name from warehouse_shipping.jxc_wholesale a inner join cuteframe.company b on a.sign_company=b.id where b.sd_company_id={$sd_company_id} and a.wholesale_status = 1";
	    	}
	    }
	    $company = $model->db()->getAll($sql);
	    return $company;
	}
	
	private function get_put_in_type() {
		if ($this->_put_in_type == '') {
			$put_in_type = 0;
			if (SYS_SCOPE == 'zhanting' && isset($_SESSION['companyId']) && !empty($_SESSION['companyId'])) {
				$company = new CompanyModel(1);
				$put_in_type = $company->select2('processor_id', ' id='.$_SESSION['companyId'], 3);
				if (!empty($put_in_type)) {
					$put_in_type = 5; // 外协供应商做批发单，入库方式强制为“自采”
				}
			}
			
			$this->_put_in_type = $put_in_type;
		}
		
		return $this->_put_in_type;
	}
	
	private function getWarehouses() {
	    $model = new WarehouseModel(21);
	    return $model->getAllhouse();
	}
    public function getWhoListHtml(){
        $sd_company_id = _Request::getInt('sd_company_id');
        $data = $this->getWhoList($sd_company_id);
        echo '<option value=""></option>';
        if(is_array($data) && !empty($data)){            
            foreach ($data as $vo){
                echo "<option value=\"{$vo['wholesale_id']}|{$vo['sign_company']}\">{$vo['wholesale_sn']} | {$vo['wholesale_name']}</option>";
            }
        }
    }
    public function getToCompanyHtml(){
        $company_id = _Request::getInt('company_id');      
        $companyModel = new CompanyModel(1);
        $row = $companyModel->select2("*","id={$company_id}",2);
        if(is_array($row) && !empty($row)){
           echo "<option value=\"{$row['id']}|{$row['company_name']}\" selected>{$row['company_name']}</option>";
        }
    }
	/**
	 *	新建页面
	 */
	public function index ($params)
	{
		//获取批发用户

        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }		
		$this->render('warehouse_bill_info_p_add.html',array(
			'bar'=>Auth::getBar(),
			'pview'=> new WarehouseBillInfoPView(new WarehouseBillInfoPModel(21)),
			'view'=>new WarehouseBillView(new WarehouseBillModel(21)),
			'bill_status' => 1,
			'dd' => new DictModel(1),
			'from_company' =>$this->getFromCompany(),
		    //'company' =>$this->company(),
			'whoList' => $this->getWhoList(),
		    'from_company_id'=>@$_SESSION['companyId'] //所在公司
		    //'warehouse' => $this->getWarehouses()
		));
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$model = new WarehouseBillInfoPModel(21);
		$billModel = new WarehouseBillModel($id,21);

		$from_company_id = $billModel->getValue('from_company_id');

		$wholesale_user = $billModel->getValue('to_customer_id');
		$to_company_id = $billModel->getValue('to_company_id');
		$to_warehouse_id = $billModel->getValue('to_warehouse_id');
		$to_customer_id = $billModel->getValue('to_customer_id');
		//获取批发用户
		$wholesModel = new JxcWholesaleModel(21);
		$row = $wholesModel->select2("`wholesale_name`, sign_company, wholesale_sn",  " wholesale_id =  {$to_customer_id} ", 'row');

		$bar = $this->get_detail_view_bar($billModel);
		$this->render('warehouse_bill_info_p_add.html',array(
			'view'=>new WarehouseBillView($billModel),
			//'pview'=> new WarehouseBillInfoPView(new WarehouseBillInfoPModel($wholesale_user['id'] , 21)),
			'tab_id'=>$tab_id,
			'wholesale_user' => $wholesale_user,
			'bill_status' => $billModel->getValue('bill_status'),
			'dd' => new DictModel(1),
			'from_company' =>$this->getFromCompany($from_company_id),
			'from_company_id' => $from_company_id,
			'whoList' => $this->getWhoList($from_company_id),
			'bar'=>$bar[0],
		    //'warehouse' => $this->getWarehouses(),
		    'to_company_id' => $to_company_id,
		    'to_customer_id' => $to_customer_id,
			'wholesale_user_val' => $row['wholesale_sn'].'|'. $row['wholesale_name'],
			'wholesale_user' => $to_customer_id.'|'.$row['sign_company'],
		    'to_warehouse_id' => $to_warehouse_id,
			'show_pifajia' => $bar[1],
            'show_private_data_zt'=>Auth::user_is_from_base_company(),
            'show_mingyichenggben'  =>$bar[2],
            'is_show_caigoujia'=>$this->checkBillHCaiGouJia($id)
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
	    $result = array('success' => 0,'error' =>'','submits'=>0);
	    
	    $submits = _Request::get('submits');
	    
		$company_id = strstr($params['from_company'], '|' , true);
		$company_name = strstr($params['from_company'], '|');
		$company_name = substr($company_name, 1);
        $out_warehouse_type = $params['out_warehouse_type'];
        $p_type = $params['p_type'];

		$wholesale_user = $params['wholesale_user'];

		$bill_info = array(
			'bill_note'=> _Request::get("bill_note"),
			'goods_num' => 0,
			'goods_total'=>0,
			'shijia' => 0,
			'pifajia' => 0,
		    'label_price_total'=>0,
			'from_company_id' => trim($company_id),
			'from_company_name' => trim($company_name),
            'out_warehouse_type' => trim($out_warehouse_type),
            'p_type'=>trim($p_type),
		);		
		$put_in_type = $this->get_put_in_type();
		if ($put_in_type) {
			$bill_info['put_in_type'] = $put_in_type;
		}
		
		$styleModel = new SelfStyleModel(17);
		
		$this->checkSignCompany($params, $bill_info);
     	//if(SYS_SCOPE =="zhanting"){
		    $bill_info['is_invoice'] = _Post::getInt("is_invoice",0);
   
		//}
		if( !isset($_POST['data'])){
			$result['error'] = '请输入货品明细';
			Util::jsonExit($result);
		}
		$model =  new WarehouseBillInfoPModel(22);		
		$goods_list = $_POST['data'];
		foreach($goods_list AS $key => $val){
		    $goods_id = $val[0];
			//提出没有填的栏
			if($goods_id == ''){
				unset($goods_list[$key]);
				continue;
			}
			/*添加是可以为空或者0；在审核时判断
			if($val[7]<=0){
			    $result['error'] = "货号{$goods_id}批发价不能小于0!";
			    Util::jsonExit($result);
			}
			*/
			$style_sn = $val[1];
			$isSanShengSanShi = $styleModel->isSanShengSanShi($style_sn);
			if(empty($submits) && $isSanShengSanShi){
			    $result['submits'] = 1;//提交次数
			    $result['error'] = "“三生三世”产品，请核对防伪标是否配齐!";
			    Util::jsonExit($result);
			}
			
			
			$bill_info['goods_total'] += $val[5];//采购价  sale_price
			$bill_info['pifajia'] += $val[6];//名义价  pifajia
			$bill_info['shijia'] += $val[7];//实价 shijia
            $bill_info['label_price_total'] += $val[29];//展厅标签价
			if($bill_info['from_company_id'] ==58){
				$item = $model->getGoodsP($goods_id);
				if($item){
					$checksession = $this->checkSession($item['warehouse_id']);
					if(is_string($checksession)){
						$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$item['warehouse']."</b>".$checksession."</span>的权限请联系管理员开通");
						Util::jsonExit($result);
					}
				}else{
					$result['error'] = "不能获取货号:{$val[0]}的货品信息，不能制批发销售单!";
					Util::jsonExit($result);					
				}	
			}              
		}
		if (!count($goods_list)){
			$result['error'] = '请输入货品明细';
			Util::jsonExit($result);
		}
		$bill_info['goods_num'] = count($goods_list);
		$res = $model->add_shiwu($bill_info,$goods_list);



		if($res['success'] == 1)
		{
			$result['success'] = 1;
			$result['x_id'] = $res['x_id'];
			$result['label'] = $res['label'];
			$result['tab_id'] = mt_rand();
			$result['error'] = '添加成功！';
		}
		else
		{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}
	
	private function checkSignCompany($params, &$bill_info) {
	    // 判断批发用户信息
	    $wholesale_user = explode('|', $params['wholesale_user']);
	    if (count($wholesale_user) !== 2) {
	        $result['error'] = '批发客户数据提交异常';
	        Util::jsonExit($result);
	    }
	    
	    $bill_info['wholesale_user'] = $wholesale_user[0];
	    $bill_info['to_warehouse_id'] = 0;  //初始化
	    $bill_info['to_warehouse_name'] = ''; //初始化
	    
	    // 判断入库公司
	    if (intval($wholesale_user[1]) > 0) {
	        $to_company = explode('|',$params['to_company']);
	        if (count($to_company) !== 2 ||  $to_company[0] != $wholesale_user[1]){
                $result['error'] = '入库公司与批发客户设置的签收公司不一致';
                Util::jsonExit($result);
	        } else if ($to_company == '' || count($to_company) == 1) {
	        	// 根据批发客户配置，强行设置(既然批发客户有设置签收公司)
	        	$to_company = array($wholesale_user[1], $wholesale_user[0]);
	        }
	        
	        $bill_info['to_company_id'] = $to_company[0];
	        $bill_info['to_company_name'] = $to_company[1];
	    } else {
	        $bill_info['to_company_id'] = 0;
	        $bill_info['to_company_name'] = '';
	    }
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'','submits'=>0);
		$id = _Post::getInt('id');		//单据主表warehouse_bill 主键
        $submits = _Request::get('submits');
        $styleModel = new SelfStyleModel(17);
		$wmodel = new WarehouseBillModel($id , 21);
		$status = $wmodel->getValue('bill_status');
		if($status == 2){
			$result['error'] = '单据已审核，不能修改';
			Util::jsonExit($result);
		}else if($status == 3){
			$result['error'] = '单据已取消，不能修改';
			Util::jsonExit($result);
		}
		$create_user = $wmodel->getValue('create_user');
		if($create_user !== $_SESSION['userName'] && $_SESSION['userName']!='admin'){
			$result['error'] = '不能修改别人的单据';
			Util::jsonExit($result);
		}
		
        $out_warehouse_type = $params['out_warehouse_type'];
        $p_type = $params['p_type'];
		$bill_info = array(
		    'id' => $id,
		    'bill_note'=>_Post::getString("bill_note"),
		    'goods_num' => 0,
		    'goods_total'=>0,
		    'shijia' => 0,
		    'pifajia' => 0,
            'out_warehouse_type'=>$out_warehouse_type,
            'p_type'=>$p_type,
		    'label_price_total'=>0
		);
		
		$put_in_type = $this->get_put_in_type();
		if ($put_in_type) {
			$bill_info['put_in_type'] = $put_in_type;
		}
		
		$this->checkSignCompany($params, $bill_info);

		$model =  new WarehouseBillInfoPModel($id,22);

		if( !isset($_POST['data'])){
			$result['error'] = '请输入货品明细';
			Util::jsonExit($result);
		}

		$goods_list = $_POST['data'];
		foreach($goods_list AS $key => $val){
			//提出没有填的栏
			if($val[0] == ''){
				unset($goods_list[$key]);
				continue;
			}
            /*添加是可以为空或者0；在审核时判断
            if($val[7]<=0){
                $result['error'] = "货号{$val[0]}批发价不能小于0!";
                Util::jsonExit($result);
            }
            */
            $style_sn = $val[1];
            $isSanShengSanShi = $styleModel->isSanShengSanShi($style_sn);
            if(empty($submits) && $isSanShengSanShi){
                $result['submits'] = 1;//提交次数
                $result['error'] = "“三生三世”产品，请核对防伪标是否配齐!";
                Util::jsonExit($result);
            }
            $bill_info['goods_total'] += $val[5];
            $bill_info['pifajia'] += $val[6];
            $bill_info['shijia'] += $val[7];
            $bill_info['label_price_total'] += $val[29];
            if($model->getValue('from_company_id') ==58){
                $item=$model->getGoodsP($val[0]);
                if($item){
                    $checksession = $this->checkSession($item['warehouse_id']);
                    if(is_string($checksession)){
                        $result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$item['warehouse']."</b>".$checksession."</span>的权限请联系管理员开通");
                        Util::jsonExit($result);
                    }
                }else{
                    $result['error'] = "不能获取货号:{$val[0]}的货品信息，不能制批发销售单!";
                    Util::jsonExit($result);
                }
            }

        }
        if (!count($goods_list))
        {
            $result['error'] = '请输入货品明细';
            Util::jsonExit($result);
        }

        $bill_info['goods_num'] = count($goods_list);
        $res = $model->update_shiwu($bill_info , $goods_list);
        if($res['success'] == 1)
        {
            $result['success'] = 1;
            $result['title'] = '修改批发销售单成功';
        }
        else
        {
            $result['error'] = $res['error'];
        }
        Util::jsonExit($result);
    }

    //详情页
    public function show($params){
        $id = $params['id'];		//获取单据的id
        $model = new WarehouseBillModel($id,21);
        $status = $model->getValue('bill_status');

        //获取批发客户
        $pmodel = new WarehouseBillInfoPModel(21);
        $whoMan = $model->getValue('to_customer_id');

        $wholesModel = new JxcWholesaleModel(21);
        $whoList = $wholesModel->select2($fields = ' `wholesale_id` , `wholesale_sn` , `wholesale_name`,`sign_company` ' , $where = " 1= 1 " , $type = 'all');
        $sign_company = 0;
        foreach($whoList AS $key => $val){
            if($val['wholesale_id'] == $whoMan){
                $whoMan = $val['wholesale_name'];
                $sign_company = $val['sign_company'];
            }
        }

        //获取批发客户签收公司类型
        $company_type = 0;
        if($sign_company != 0){
            $companyModel = new CompanyModel(1);
            $company_info = $companyModel->select2('company_type',"id= ".$sign_company,2);
            $company_type = $company_info['company_type'];
        }
       

        //

        //获取取单据取消时的操作用户和操作时间
        $WarehouseBillModel = new WarehouseBillModel(21);
        $billcloseArr=$WarehouseBillModel->get_bill_close_status($id);

        //如果不需要进行签收，则移除签收按钮
        $bar = $this->get_detail_view_bar($model);
        $show_private_data_zt = Auth::user_is_from_base_company();
        #获取单据附加表ID warehouse_bill_info_p
        $model = new WarehouseBillInfoPModel(21);
        $this->render('warehouse_bill_info_p_show.html',array(
            'view' => new WarehouseBillView(new WarehouseBillModel($id, 21)),
            'dd' => new DictView(new DictModel(1)),
            'bar'=>$bar[0],
            'status'=>$status,
            'whoMan' => $whoMan,
            'userType'=>$_SESSION['userType'],
            'billcloseArr'=>$billcloseArr,
            'isViewChengbenjia'=>$this->isViewChengbenjia(),
            'show_pifajia' => $bar[1],
            'show_private_data_zt'=>$show_private_data_zt,
            'show_mingyichenggben'	=>$bar[2],
            'is_show_caigoujia'=>$this->checkBillHCaiGouJia($id),
            'company_type'=>$company_type
        ));
    }

    //详情明细展示
    public function getGoodsInDetails($params){
        $bill_id = $params['bill_id'];
        $args = array(
            'mod'	=> _Request::get("mod"),
            'con'	=> substr(__CLASS__, 0, -10),
            'act'	=> __FUNCTION__,
            'bill_id'	=>$bill_id
        );
        $model = new WarehouseBillModel(21);
        $g_model = new WarehouseBillGoodsModel(21);
        $ProccesorModel = new SelfProccesorModel(13);
        $where = array('bill_id'=>$bill_id);
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
        $data = $g_model->pageList($where,$page,10,false);
        foreach ($data['data'] as $k =>$v){
            if($v['buchan_sn'] !=''){
                $data['data'][$k]['face_work']=$ProccesorModel->getFaceworkByBcNo($v['buchan_sn']);
            }else{
                $data['data'][$k]['face_work']='';
            }
        }
        $bar = $this->get_detail_view_bar($model);
        $pageData = $data;
        $pageData['filter'] = $args;
        //$pageData['recordCount'] = count($pageData['data']);
        $pageData['jsFuncs'] = 'warehouse_bill_goods_show_page';
        $show_private_data_zt = Auth::user_is_from_base_company();
        $this->render('warehouse_bill_goods_p.html',array(
            'pa' =>Util::page($pageData),
            'dd' => new DictView(new DictModel(1)),
            'data' => $data,
            'isViewChengbenjia'=>$this->isViewChengbenjia(),
            'show_pifajia'=>$bar[1],
            'show_private_data_zt'=>$show_private_data_zt,
            'userType'=>$_SESSION['userType'],
            'show_mingyichenggben'  =>$bar[2],
            'show_caigou_price'  =>$bar[3],
            'is_show_caigoujia'=>$this->checkBillHCaiGouJia($bill_id)
        ));
    }

    /** table 插件*
     * 差价=批发价-名义价
     采购价：原始成本价
     名义价：名义成本
     批发价：默认等于名义成本，可修改
    */
	public function mkJson(){
		$id = _Post::getInt('id');
        $from_company = _Post::getInt('from_company');
       
		$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_bill_p.tab');
		if(!$id){
			$arr['data_bill_p'] = [];
		}else{
			$arr['data_bill_p'] = array();
			$model = new WarehouseBillInfoPModel(21);
			$data = $model->GetDetailByBillId($id);
			foreach($data as $k => $v){
	    		if(!isset($v['face_work'])){
				    $v['face_work'] = '';
				}
				$arr['data_bill_p'][] =[
					"{$v['goods_id']}", "{$v['goods_sn']}", "{$v['jinzhong']}","{$v['zuanshidaxiao']}",
					"{$v['zhengshuhao']}", "{$v['sale_price']}","{$v['pifajia']}","{$v['shijia']}", 
					"{$v['chajia']}", "{$v['shoucun']}", "{$v['changdu']}", "{$v['caizhi']}","{$v['zhushi']}" ,
					"{$v['zhushilishu']}" , "{$v['fushi']}" , "{$v['fushilishu']}" , "{$v['fushizhong']}" , "{$v['zongzhong']}" ,
					"{$v['jingdu']}" ,"{$v['yanse']}" , "{$v['goods_name']}","{$v['order_sn']}","{$v['pinhao']}","{$v['xiangci']}","{$v['p_sn_out']}","","{$v['dep_settlement_type']}","{$v['settlement_time']}","{$v['management_fee']}","{$v['label_price']}"
				];
			}
		}
		$json = json_encode($arr);
		echo $json;
	}

	/** 根据货号，查询货品信息 (table插件查询使用) **/
	public function getGoodsInfoByGoodsID($params){
		//差价 = 名义价 - 批发价 === 0
		$fields = " `goods_id` , `goods_sn` , `jinzhong` , `zuanshidaxiao` , `zhengshuhao` , `chengbenjia` , `mingyichengben`  , `mingyichengben`  , `mingyichengben` - `mingyichengben` AS `chajia`, `shoucun` , `changdu` , `caizhi` , `zhushi` , `zhushilishu` , `fushi` , `fushilishu` , `fushizhong` , `zongzhong` , `jingdu` , `yanse` , `goods_name` , `is_on_sale` , `order_goods_id`, `warehouse_id`";

		$goods_id = $params['goods_id'];
		if($goods_id){
			$model = new WarehouseBillModel(21);
			$goods = $model->GetGoodsByBill($fields , $goods_id);
			if(!empty($goods)){
				/*if($goods['order_goods_id'] != 0){
					$error = "货号为<span style='color:red;'>{$goods_id}</span>的货品已经绑定订单，不能制批量销售单";
					$return_json = ['success' =>0 , 'error'=>$error];
					echo json_encode($return_json);exit;
				}*/
				if($goods['warehouse_id'] != 517 ){		//<option value="517"> PFJH | 批发借货</option>
					$error = "货号为<span style='color:red;'>{$goods_id}</span>的货品不是批发借货库的货品，不能批量销售单";
					$return_json = ['success' =>0 , 'error'=>$error];
					echo json_encode($return_json);exit;
				}
				if($goods['is_on_sale'] == 2){	//库存状态
					$return_json = [
					"{$goods['goods_sn']}", "{$goods['jinzhong']}", "{$goods['zuanshidaxiao']}",
					"{$goods['zhengshuhao']}", "{$goods['chengbenjia']}", "{$goods['mingyichengben']}", "{$goods['mingyichengben']}",
					"{$goods['chajia']}", "{$goods['shoucun']}", "{$goods['changdu']}",
					"{$goods['caizhi']}", "{$goods['zhushi']}","{$goods['zhushilishu']}" ,
					"{$goods['fushi']}" ,"{$goods['fushilishu']}" ,"{$goods['fushizhong']}" ,
					"{$goods['zongzhong']}" ,"{$goods['jingdu']}" ,"{$goods['yanse']}" ,
					"{$goods['goods_name']}"];
					echo json_encode($return_json);exit;
				}else{
					$error = "货号为<span style='color:red;'>{$goods_id}</span>的货品不是库存状态，不能批量销售单";
					$return_json = ['success' =>0 , 'error'=>$error];
					echo json_encode($return_json);exit;
				}
			}else{
				$error = "仓库查不到货号为<span style='color:red;'>{$goods_id}</span>的货品";
				$return_json = ['success' =>0 , 'error'=>$error];
				echo json_encode($return_json);exit;
			}
		}
	}

	//审核单据
	public function checkBill($params){
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];

		$model = new WarehouseBillModel($bill_id,21);
		$pmodel = new WarehouseBillInfoPModel(22);

		/** 如果单据是审核/取消状态 不允许修改 **/
		$status = $model->getValue('bill_status');
		if($status == 2){
			$result['error'] = '单据已审核，不能审核';
			Util::jsonExit($result);
		}else if($status == 3){
			$result['error'] = '单据已取消，不能审核';
			Util::jsonExit($result);
		}
		
        if($_SESSION['companyId']<>$model->getValue('from_company_id')){
            $result['error'] = '不能审核非本公司所制的批发单.';
            Util::jsonExit($result);
        }

		$is_tsyd = $model->getValue('is_tsyd');
		if($is_tsyd==1){
			$result['error'] = '经销商批发订单不允许手工审核';
			Util::jsonExit($result);
		}
		
		$create_user = $model->getValue('create_user');
		//TODO: 仅针对boss
		if($create_user === $_SESSION['userName']){
			$result['error'] = '不能审核自己的单据';
			Util::jsonExit($result);
		}
		$res = $pmodel->checkBill($bill_id , $bill_no);
		if($res['success'] == 1)
		{
			$result['success'] = 1;
			$result['error'] = '审核批发销售单成功';
			//AsyncDelegate::dispatch('warehouse', array('event' => 'bill_P_checked', 'bill_id' => $bill_id));
		}
		else
		{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}

	/**
	* 取消单据
	*/
	public function CloseBill($params){
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];

		$model = new WarehouseBillModel($bill_id,21);
		$pmodel = new WarehouseBillInfoPModel(22);

		/** 如果单据是审核/取消状态 不允许修改 **/
		$status = $model->getValue('bill_status');
		if($status == 2){
			$result['error'] = '单据已审核，不能取消';
			Util::jsonExit($result);
		}else if($status == 3){
			$result['error'] = '单据已取消，不能重复操作';
			Util::jsonExit($result);
		}
		$create_user = $model->getValue('create_user');
		if($create_user !== $_SESSION['userName']){
			$result['error'] = '亲~ 非本人单据，你是不能取消的哦！#^_^#  ';
			Util::jsonExit($result);
		}
		$res = $pmodel->closebill($bill_id , $bill_no);
		if($res['success'] == 1)
		{
			$result['success'] = 1;
			$result['error'] = '取消批发销售单成功';
		}
		else
		{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}
	/**
	 * 批量打印明细(支持单个或批量打印)
	 * @param unknown $params
	 */
    public function printBill($params){
        //获取单据bill_id
        $ids = _Request::get('_ids');
        if(empty($ids)){
            $id_list = array(_Request::get('id'));
        }else{        
            $id_list = explode(",",$ids);
        }
        
        $wholesModel = new JxcWholesaleModel(21);
        $whoList = $wholesModel->select2(' `wholesale_id`, `wholesale_name` '," `wholesale_status` = 1 ", 'all');
        $wholesaleArr=array();
        foreach ($whoList as $v){
            $wholesaleArr[$v['wholesale_id']]=$v['wholesale_name'];
        }
        $bill_list = array();
        foreach ($id_list as $id){
            $model = new WarehouseBillModel($id,21);
            $bill_info  = $model->getDataObject();
            if(empty($bill_info)){
                continue;
            }
            //获取货品明细
            $pmodel = new WarehouseBillInfoPModel(21);
            $goods_list = $pmodel->GetDetailByBillId($id);
            //统计
            $zhushilishu = $zuanshidaxiao = $fushilishu = $fushizhong = $jinzhong = $num = $xiaoshoujia = $management_fee=$label_price= 0;
            foreach($goods_list as $key => $val){
                $zhushilishu += $val['zhushilishu'];
                $zuanshidaxiao += $val['zuanshidaxiao'];
                $fushilishu += $val['fushilishu'];
                $fushizhong += $val['fushizhong'] ;
                $jinzhong += $val['jinzhong'];
                $num += $val['num'];
                $xiaoshoujia += $val['shijia'];
                $management_fee += $val['management_fee'];
                $label_price += $val['label_price'];
            }
            $tongji = array(
                'zhushilishu' => $zhushilishu,
                'zuanshidaxiao' => $zuanshidaxiao,
                'fushilishu' => $fushilishu,
                'fushizhong' => $fushizhong,
                'jinzhong' => $jinzhong,
                'num' => $num,
                'xiaoshoujia' => $xiaoshoujia,
                'management_fee'=>$management_fee,
                'label_price'=>$label_price,
            );
            $bill_list[] = array('bill_info'=>$bill_info,'goods_list'=>$goods_list,'tongji'=>$tongji);
        }
        $this->render('print_bill.html', array(
            'bill_list'=>$bill_list,
            'wholesaleArr' => $wholesaleArr
        ));
    }

	/** 打印汇总 **/
	public function printSum($params){
		//获取单据bill_id
		$id = _Request::get('id');
		$model = new WarehouseBillModel($id,21);
		$data  = $model->getDataObject();

        $wholesModel = new JxcWholesaleModel(21);
        $whoList = $wholesModel->select2(' `wholesale_id`, `wholesale_name` '," `wholesale_status` = 1 ", 'all');
        $wholesaleArr=array();
        foreach ($whoList as $v){
            $wholesaleArr[$v['wholesale_id']]=$v['wholesale_name'];
        }

		//获取货品明细
		$pmodel = new WarehouseBillInfoPModel(21);
		$tongji = $pmodel->HuiZongTongJi($id);
		$this->render('print_sum.html', array(
			'data' => $data,
			'tongji' => $tongji,
            'wholesaleArr' => $wholesaleArr
		));
	}

	/**
	* 打印条码
	*/
	public function print_q($params){
/*		$bill_id = $params['bill_id'];
			$str = "货号, 款号 , 手寸 , 长度 , 主石粒数 , 主石重 , 副石粒数 , 副石重 , 加工商编号 , 总重 , 净度 , 颜色 , 证书号, 国际证书, 主石切工 , 标签备注 , 主石 , 副石 , 主成色 , 饰品分类 , 款式分类 , 名称 , 石3副石 , 石3粒数 , 石3重 , 石4副石 , 石4粒数 , 石4重 , 石5副石 , 石5粒数 , 石5重 , 主成色重 , 副成色 , 副成色重 , 买入工费 , 计价工费 , 加价率 , 最新零售价 , 模号 , 品牌 , 证书数量 , 配件数量 , 时尚款 , 系列 , 属性 , 类别 , 成本价 , 入库日期 , 加价率代码 , 主石粒重 , 副石粒重 , 标签手寸 , 字印 , 货币符号零售价 , 新成本价 , 新零售价 , 一口价 , 标价 , 定制价 , A , B , C , D , E , F , G , H , I , HB_G, HB_H\n";
			foreach ($data as $key => $val) {

				// ."\n";
			}
			header("Content-type: text/html; charset=gbk");
			header("Content-type:aplication/vnd.ms-excel");
			header("Content-Disposition:filename=" . iconv("utf-8", "gbk", date("Y-m-d")) . "p_print_detail.csv");
			echo iconv("utf-8", "gbk", $str);*/
	}
	
	/** 根据货号，查询货品信息 (table插件查询使用) **/
	public function getGoodsInfos(){
		//var_dump($_REQUEST);exit;
		$g_ids = _Request::getList('g_ids');
		$g_ids = array_filter($g_ids);
		$bill_id = _Request::getInt('bill_id');
		$from_company_info=0;
		if(_Request::get('from_company')){
			list($from_company_id,$from_company) =explode("|", _Request::get('from_company'));			
		}
		if(_Request::get('wholesale_user')){
		    list($wholesale_user,$wholesale_company_id) =explode("|", _Request::get('wholesale_user'));
		}
		$is_invoice = _Request::getInt('is_invoice');
				
		$model = new WarehouseGoodsModel(21);
		$view = new WarehouseBillInfoPView($model);
        if(SYS_SCOPE=='zhanting'){
            $warehouseRelModel = new WarehouseRelModel(21);
            if($from_company_id>0 && !in_array($from_company_id,array(58,515))){
                $rows = $warehouseRelModel->select2("warehouse_id","company_id={$from_company_id}","getAll");
                $warehouse_id_arr = array_column($rows,"warehouse_id");
            }else{
                $warehouse_id_arr = array('517','518','653','695','1874');
            }            
        }else{
            $warehouse_id_arr = array('517','518','653','695');
        } 
        $put_in_type = $this->get_put_in_type();
		$res = $model->table_GetGoodsInfoP($g_ids,$view->js_table['lable'],2,$bill_id,0,$warehouse_id_arr,1,$from_company_id,$put_in_type);

		if(SYS_SCOPE=='zhanting' && !empty($res['success'])){
            $companyModel = new CompanyModel(1);
            if($wholesale_company_id>0){
                $wholesale_company_type = $companyModel->select2("company_type","id={$wholesale_company_id}",3);
            }else{
                $wholesale_company_type = 3;
            }
            $tax_rate = $is_invoice?1.043:1;
            $pmodel = new WarehouseBillInfoPModel(21);
            
            foreach ($res['success'] as $key=>$vo){
              //展厅自动计算批发价 begin
              $goods_id = $g_ids[$key];
              $ginfo = $pmodel->getGoodsP($goods_id);
              $pifajia = 0;
             


              $mingyichengben = (float)$vo[5];//名义成本
              $label_price = (float)$vo[28];//标签价
              if($wholesale_company_type == 3){
                  //如果批发客户为经销商：商品如果有展厅标签价
                  //批发价=展厅标签价*0.25*税点

                  if($label_price>0){
                      $pifajia = sprintf("%.2f",$label_price*$tax_rate*0.25);
                      //用展厅标签价算的批发价小于名义价的时候，系统自动用名义价算批发价
                      if($mingyichengben > $pifajia){
                          $pifajia = sprintf("%.2f",$mingyichengben*$tax_rate*0.25);
                      }
                  }else{
                      $sql = "select count(*) from front.app_style_jxs where style_sn='{$ginfo['goods_sn']}'";
                      $is_jxs_style = $model->db()->getOne($sql)?true:false;
                      //$pifa_jiajialv = $is_jxs_style?1.21:1.17;
                      if($is_jxs_style){
                        $pifa_jiajialv = 1.21;
                    }else{
                        if($ginfo['product_type1']=='K金'){
                            $pifa_jiajialv = 1.1;
                        }else{
                            $pifa_jiajialv = 1.17;
                        }
                    }
          
                      if($ginfo['with_fee']==0)  {
                          if($ginfo['cat_type']=='裸石' && $ginfo['zhengshuhao']!=""){
                              $gendan_fee = 0;
                          }else{
                              $gendan_fee = 20;
                          }
                      }else{
                          $gendan_fee = $ginfo['with_fee'];
                      }
                      if($ginfo['goods_sn'] == "QIBAN"){
                          $qiban_fee = 300;
                      }else{
                          $qiban_fee = 0;
                      }
                      /**
                                                                           、非自采的并且商品款式分类：非裸石或者款式分类为裸石证书号列为空时
                                                                           批发价=（名义成本*1.1X+跟单费+起版费）*税点
                       */
                      if(!in_array($ginfo['put_in_type'],array(5))){
                          if($ginfo['cat_type']<>'裸石'|| ($ginfo['cat_type']=='裸石' && $ginfo['zhengshuhao']=="")){
                              $pifajia = ($mingyichengben*$pifa_jiajialv+$gendan_fee+$qiban_fee)*$tax_rate;
                              //echo $pifa_jiajialv."-".$gendan_fee."-".$qiban_fee."-".$tax_rate;
                              $pifajia = sprintf("%.2f",$pifajia);
                          }
                      }
          
                  }
                  
                  if($_SESSION['companyId']<>58){
              	  	  $pifajia =  $mingyichengben;              	      
                  }
              }	else if($wholesale_company_type == 2){
                  $pifajia = $mingyichengben;
              }
              $vo[6] = $pifajia;
              $vo[7] = $pifajia - $mingyichengben;
              $res['success'][$key] = $vo;
           }//展厅自动计算批发价 end
		}
		//print_r($res);
		echo json_encode($res);exit;
	}
	
	//确认发货
	public function confirmP($params){
		$result = array('success' => 0,'error' =>'');		
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];
	
		$model = new WarehouseBillModel($bill_id,21);
		$pmodel = new WarehouseBillInfoPModel(22);
	
		/** 只有未审核未确认的批发销售单才允许点击发货确认 **/
		$status = $model->getValue('bill_status');
		if($status == 2){
			$result['error'] = '单据已审核，不能点击确认发货';
			Util::jsonExit($result);
		}else if($status == 3){
			$result['error'] = '单据已取消，不能点击确认发货';
			Util::jsonExit($result);
		}
		
		
		$confirm_delivery = $model->getValue('confirm_delivery');
		if($confirm_delivery == 1){
			$result['error'] = '单据已确认发货';
			Util::jsonExit($result);
		}
		
		$res1=$pmodel->CheckBillGoods($bill_id);		
		if ($res1['success']==0){
			$result['error'] = $res1['error'];
			Util::jsonExit($result);
		}
		
		$res = $pmodel->confirmBillP($bill_id);
		if($res['success'] == 1)
		{
			$result['success'] = 1;
			$result['error'] = $res['error'];
		}
		else
		{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}
	
	public function sign_p_bill($params) {
	  
	    $result = array('success' => 0,'error' => '');	    
	    $ops = _Request::get("ops");
	    if ($ops == '') {
	        $result['error'] = '未知的操作';
	        Util::jsonExit($result);
	    }
	    
	    $id = _Request::getInt("bill_id");
	    if ($ops == 'presign') {
    	    $com_id = _Request::getInt("to_comp");
    	    if ($com_id <= 0) {
    	        $result['error'] = '找不到签收公司';
    	        Util::jsonExit($result);
    	    }
    	    
    	    $warehouse_model = new WarehouseModel(21);
    	    $company_warehouses = $warehouse_model->getMasterWarehouse($com_id);
    	    
    	    $result['content'] = $this->fetch('sign_p_bill.html',array(
    	        'warehouse'=>$company_warehouses,
    	        'bill_id'=>$id
    	    ));
    	    $result['title'] = '签收批发单';
    	    Util::jsonExit($result);
	    } else if ($ops == 'postsign'){

	        $model = new WarehouseBillModel($id,21);
	        $bill_type = $model->getValue('bill_type');
	        $com_id = $model->getValue('to_company_id');
	        
	        $create_time = strtotime( $model->getValue('create_time'));
	        $from_time = strtotime('2017-1-1');
	        
	        if ($com_id <= 0|| $bill_type !='P' || $create_time < $from_time) {
	            $result['error'] = '该单不需要签收';
	            Util::jsonExit($result);
	        }
	        
	        $status = $model->getValue('bill_status');
	        if ($status != '2') {
	            $result['error'] = '仅审核通过的批发单才能签收';
	            Util::jsonExit($result);
	        }
	        
	        $to_w_id = $model->getValue('to_warehouse_id');
	        if ($to_w_id > 0 || $status == 4) {
	            $result['error'] = '该批发单已经签收';
	            Util::jsonExit($result);
	        }
	        
	        $to_warehouse = explode('|', $params['to_warehouse']);
	        if (count($to_warehouse) !== 2 || $to_warehouse[0] <= 0) {
                $result['error'] = '签收仓库数据提交异常';
                Util::jsonExit($result);
	        }
	    
            // 判断入库仓库是否归属于入库公司
            $warehouse_model = new WarehouseModel(21);
            $company_warehouses = $warehouse_model->getMasterWarehouse($com_id);
            if (!in_array($to_warehouse[0], array_column($company_warehouses, 'id'))) {
                $result['error'] = '入库公司没有此仓库';
                Util::jsonExit($result);
            }
    
           $resp = $model->sign_p_bill(array(
                'id' => $id, 
                'to_warehouse_id' =>$to_warehouse[0], 
                'to_warehouse_name' => $to_warehouse[1],  
                'sign_user' => $_SESSION['userName'],
           		'sign_time' => date('Y-m-d H:i:s')
            ));
           
           if ($resp !== false) {
           	   //AsyncDelegate::dispatch('warehouse', array('event' => 'bill_P_signed', 'bill_id' => $id));
           	
               $result['success'] = '1';
               $result['error'] = '签收成功';
               Util::jsonExit($result);
           }
	    }
	}


    /**浩鹏P单据结算**/
    public function jieJiaBillInfoP($params){
        $result = array('success' => 0,'error' =>'');
        $bill_id = $params['id'];
        $model = new WarehouseBillModel($bill_id,21);

        /** 如果单据是审核/取消状态 不允许修改 **/
        $status = $model->getValue('fin_check_status');

        if($status == 2){
            $result['error'] = '单据已经结算';
            Util::jsonExit($result);
        }

        /*
        $status = $model->getValue('bill_status');
        if($status <> 4){
            $result['error'] = '单据不是已签收状态，不能审核';
            Util::jsonExit($result);
        }
       \*/

        //取得单据信息
        $olds  = $model->getDataObject();
        $news = array('id'=>$bill_id,'fin_check_status'=>2,'fin_check_user'=>$_SESSION['userName'],'fin_check_time'=>date('Y-m-d H:i:s'));

        //data:单据信息 goods_ids：所有货品拼接
        $res = $model->saveData($news,$olds);
        if($res){
            $result['success'] = 1;
            $result['error'] = '结算成功';
        }else{
            $result['error'] = '结算失败';
        }
        Util::jsonExit($result);
    }


	 private function get_detail_view_bar($model) {
  
          // 已审核状态的，且需要签收的P单，才保留签收按钮
          $create_time = strtotime( $model->getValue('create_time'));
          $from_time = strtotime('2017-1-1');  //批发单签收功能开始使用时间
          //TODO: 去除历史单据
          $remove_sign_btn = !($model->getValue('bill_status') == '2' && $model->getValue('to_company_id') > 0 && $model->getValue('to_warehouse_id') == 0 && $create_time >= $from_time);
          
          //如果不需要进行签收，则移除签收按钮
          $bar = Auth::build_view_bar(array('view_p_pifajia' => true, 'show_p_mingyichengben' => true,'show_caigou_price'=>true, 'sign_p_bill' => $remove_sign_btn));
          
          $show_pifajia = SYS_SCOPE == "boss";
          $show_p_mingyichenggben = SYS_SCOPE == "boss";
          $show_caigou_price = SYS_SCOPE == "boss";
          if (is_array($bar)) {
            $show_pifajia = in_array('view_p_pifajia', $bar[1]);
            $show_p_mingyichenggben = in_array('show_p_mingyichengben', $bar[1]);
            $show_caigou_price = in_array('show_caigou_price', $bar[1]);
            $bar = $bar[0];
          }
          
          return array($bar, $show_pifajia, $show_p_mingyichenggben, $show_caigou_price);
      }
	
	public function getWholesaleUser($params) {
		$result = array('success' => 0,'content' => '');	    
		if (!isset($params['w_sn']) || empty($params['w_sn'])) {
			Util::jsonExit($result);
		}
		
		$w_sn = $params['w_sn'];
		$wholesModel = new JxcWholesaleModel(21);
		$row = $wholesModel->select2("`wholesale_name`, sign_company, wholesale_id",  " wholesale_sn = '{$w_sn}' ", 'row');
		if ($row) {
			$result['success'] = 1;
			$result['wholesale_name'] = $row['wholesale_name'];
			$result['wholesale_user'] = $row['wholesale_id'].'|'.$row['sign_company'];
		}
		
		Util::jsonExit($result);
	}
	

}?>