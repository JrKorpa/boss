<?php
/**
 *  -------------------------------------------------
 *   @file		: TydprintController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Liulinyan <939942478@qq.com>
 *   @date		: 2015-07-31 10:15:23
 *   @update	:
 *  -------------------------------------------------
 */
class TydprintController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('printDetail','orderExpress');
	/**
	*	index，搜索框
	*/
	public function index ($params)
	{
		$this->render('tydprint_search_form.html', array('bar' => Auth::getBar()));
	}

	/**
	* 	search，列表
	*/
	public function search($params)
	{
		$args = array(
			'mod' => _Request::get("mod"),
			'con' => substr(__CLASS__, 0, -10),
			'act' => __FUNCTION__,
			'shop_name' => _Request::get("shop_name"),
			'shop_type' => _Request::getInt('shop_type'),
			'accepter_company' => _Request::get("accepter_company"),
			'is_delete' => 0,
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
		$where = array();
		$where['shop_name'] = $args['shop_name'];
		$where['is_delete'] = $args['is_delete'];
		$where['shop_type'] = $args['shop_type'];
		$where['accepter_company'] = $args['accepter_company'];
		$model = new ShopCfgModel(1);
		$data = $model->pageListTyd($where, $page, 10, false);		
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'shop_cfg_search_page';
		 
		$this->render('tydprint_search_list.html', array(
			'pa' => Util::page($pageData),
			'page_list' => $data
		));
	}
	
	
	/**
	* 	edit，渲染修改页面
	*/
	public function edit($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0, 'error' => '');
		
		$accepter = array(
			'name'=>'',
			'mobile'=>''
		);
		//获取收货人信息
		$acceptModel = new TydprintaccepterModel($id, 2);
		//获取表中是否有该id
		$accepterobj = $acceptModel->getDataObject();
		if(!empty($accepterobj))
		{
			$accepter['name'] = $accepterobj['accepter_name'];
			$accepter['mobile'] = $accepterobj['accepter_mobile'];
			$accepter['company'] = $accepterobj['accepter_company'];
			$accepter['address'] = $accepterobj['accepter_address'];
		}
		$result['content'] = $this->fetch('tydprint_info.html', array(
		'view' => new ShopCfgView(new ShopCfgModel($id, 1)),
		'accepter'=>$accepter,
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	* 	show，渲染查看页面
	*/
	public function show($params)
	{
		$id = intval($params["id"]);
		
		$model = new ShopCfgModel(1);
		$data = $model->getUinfo($id);
		
		$this->render('tydprint_show.html', array(
				'd' => $data,
		));
	}

		
	/**
	* 	update，更新信息
	*/
	public function update($params)
	{
		$result = array('success' => 0, 'error' => '');
		$id = _Post::getInt('id');
		//收货人
		$accepter_name = _Post::getString('accepter_name');
		$accepter_mobile = _Post::getString('accepter_mobile');
		$accepter_company = _Post::getString('accepter_company');
		$accepter_address = _Post::getString('accepter_address');

		//update by liulinyan 2015-07-31 10：03 for 体验店收货人和收货电话关联信息
		$accept['id'] = $id;
		$accept['accepter_name'] = $accepter_name;
		$accept['accepter_mobile'] = $accepter_mobile;
		$accept['accepter_company'] = $accepter_company;
		$accept['accepter_address'] = $accepter_address;
	
		$acceptModel = new TydprintaccepterModel($id, 2);
		//获取表中是否有该id
		$oldaccept = $acceptModel->getDataObject();
		if(!empty($oldaccept))
		{
			$res = $acceptModel->saveData($accept, $oldaccept);
			//修改
		}else{
			//添加
			$addmodel = new TydprintaccepterModel(2);
			$res = $addmodel->insertDate($id,$accepter_name,$accepter_mobile,$accepter_company,$accepter_address);
		}
		if ($res !== false)
		{
			$result['success'] = 1;
		}else{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}
	
	//打印
	public function printDetail($params)
	{
		$ids = $params["ids"];
		$ids = explode(',',$ids);
		$ids = array_filter($ids);
		$muti = count($ids);
		$express_id='';
		if(isset($params["express_id"]) && !empty($params["express_id"])){
			$express=explode('|', $params["express_id"]);
            if($express[0])
            	$express_id= $express[0];
		}
          

        $print_num=1;
		if(isset($params["print_num"]) && $params["print_num"]>0)
			$print_num=$params["print_num"];
		if(isset($params["express_type"]) && in_array($params["express_type"],array('1','2')))
			$express_type=$params["express_type"];
		else
		    $express_type=1;			
		//数据处理
		if($muti >= 1)
		{
			$data = array();
			$key=0;
			$express_time=time();
			foreach($ids as $id)
			{
				for($i=0;$i<$print_num;$i++){
				    $key++;   
					$info = $this->combinedata($id);
					if($express_id){
						$exdata=array();
				        $exdata['j_company'] =EXPRESS_J_COMPANY;
				        $exdata['j_contact']=EXPRESS_J_CONTACT;
				        $exdata['j_tel']=EXPRESS_J_TEL;
				        $exdata['j_address']=EXPRESS_J_ADDRESS;
				        $exdata['goods_name']=EXPRESS_GOODS_NAME;        
				        $exdata['d_company']=$info['accepter_company'];
				        $exdata['d_contact']=$info['acceptername'];
				        $exdata['d_tel']=$info['acceptermobile'];
				        $exdata['d_address']=$info['accepter_address'];
				        $exdata['express_type']=$express_type;
				        $info['express_id']=$express_id;

				        $file_path = APP_ROOT."shipping/modules/express_api/Express_api.php";
				        require_once($file_path);
				        //$express_order_id=($express_time+$key) .rand(10,99);
			 		    $olddo = array();
						$newdo=array(
							'address'=>$exdata['d_address'],
							'd_tel'=>$exdata['d_tel'],
							'd_contact'=>$exdata['d_contact'],
							"express_id"=>$express_id,				
							'create_time'=>date('Y-m-d H:i:s'),
							'create_user'=>$_SESSION['userName'],			
						);       	
						$expresslistmodel=new ExpressListModel(43);
						$express_order_id =$expresslistmodel->saveData($newdo,$olddo); 
				        $res=Express_api::makeOrder($express_order_id,$express_id,$exdata);
				        if($res['result']==1){
				        	$info['acceptermobile'] = substr_replace($info['acceptermobile'],'****',3,4);
	                        $info=array_merge($info,$res);
	                        $info['express_type']=$express_type;
	                        $expresslistmodel->updateExpressNO($express_order_id,$res['express_no']);
				        }else{
				        	exit($res['error']);
				        }					
					}
					array_push($data,$info);
				}			
			}
		}
		//echo "<pre>";
		// print_r($data);
		// die();
		//定义体验店类型
		$allshop_type = array();
		$this->render('tydprint_template.html',array(
			'data'=>$data,
			'ids'=>$ids,
			'is_muti'=>1,
			'time' => date('Y-m-d H:i:s'),
			'peoplename'=>'郭伟',
			'express_id'=>$express_id
		));	
	}
	
	
	
	public function combinedata($id)
	{
		$Model = new ShopCfgModel(1);
		//根据传过来的id把体验店的信息都拿出来
		$data = $Model->getUinfo($id);
		//定义体验店返回信息数组
		$tyddata = array(
			'shop_id' => $id,
			'shop_name' => '',      //体验店名称
			'shop_type' => '',      //体验店类型
			'shop_address' => '',   //地址
			'shop_phone' => '',     //体验店电话				
			'acceptername'  => '',   //联系人     关联表中收货联系人
			'acceptermobile'   => '' //联系电话   关联表中收货人电话
		);
		if(!empty($data))
		{
			$tyddata['shop_name'] = $data['shop_name'];
			$tyddata['shop_type'] = $data['shop_type'];
			$tyddata['shop_address'] = $data['shop_address'];
			$tyddata['shop_phone'] = $data['shop_phone'];
			
			
			//查找收货人信息
			$acceptMod = new TydprintaccepterModel($id, 2);
			$acceptinfo = $acceptMod->getDataObject();
			if(!empty($acceptinfo))
			{
				$tyddata['acceptername'] = $acceptinfo['accepter_name'];
				$tyddata['acceptermobile'] = $acceptinfo['accepter_mobile'];
				$tyddata['accepter_company'] = $acceptinfo['accepter_company'];
				$tyddata['accepter_address'] = $acceptinfo['accepter_address'];
			}
		}
		return $tyddata;
	}
	
	/** 更改体验店模板打印状态 **/
	public function changePrintStatus($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = $params['id'];
		$ids=  explode(',', $id);
		foreach($ids as $id)
		{
			//$model = new ShipParcelModel($id, 44);
			//$model->setValue('is_print', 1);
			//$sta = $model->save();
			//if($model->insertLog($id)){
			$result['success'] = 1;
			//}
		}
		Util::jsonExit($result);
	}
	
	public function getProvince()
	{
		$count_id = _Post::getInt('count');
		$reginModel = new RegionModel(1);
		$provincedata = $reginModel->getRegion($count_id);
		$res = $this->fetch('province_option.html', array('provincedata' => $provincedata));
		echo $res;
	}

    public function orderExpress($params){
		$ids = $params["_ids"];		
		$ids = array_filter($ids); 
		$ids=implode(',', $ids);
		
		$ex_model		= new ExpressModel(1);
		$info_express   = $ex_model->getAllExpress(); 
		$result['content'] = $this->fetch('tydprint_elexpress_form.html',array(			
			'info_express' =>$info_express,
			'ids'=>$ids
		));
		
		$result['title'] = '选择快递公司';
		Util::jsonExit($result);		

    }	
}?>