<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseGoodsEditController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-29 19:14:03
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseGoodsEditController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('warehouse_goods_search_form_edit.html',array(
		));
	}
	/***
	* getGoodsAttr
	*/
	public  function  getGoodsAttr()
	{   
	    
		#主石形状
		$result = array();
		//update by liulinyan 2015-07-27 根据商品信息 那里是写死的  bug for NEW-2409
		$goodsAttrModel = new GoodsAttributeModel(17);
		
		//$result['xingzhuang'] = array('圆形'=>'圆形','公主方'=>'公主方','菱形'=>'菱形','祖母绿'=>'祖母绿','心形'=>'心形','椭圆形'=>'椭圆形','梨形'=>'梨形','垫形'=>'垫形','长方形'=>'长方形','马眼'=>'马眼');
		$shape = $goodsAttrModel->getShapeList();
		foreach($shape as $key=>$vo){
		    $shape[$vo] = $vo;
		    unset($shape[$key]);
		}
		$result['xingzhuang'] = $shape;
		//主石颜色
		$result['yanse']      = $goodsAttrModel->getColorList();
		//钻石净度
		$result['jingdu']     = $goodsAttrModel->getClarityList();		
		#主石切工
		$result['qiegong'] = array(
			'EX' =>'EX',
			'VG' => 'VG',
			'G'	 => 'G',
			'F'	 => 'F',
			'P'	 => 'P'
		);
		#抛光
		$result['paoguang'] = array(
			'EX' =>'EX',
			'VG' => 'VG',
			'G'	 => 'G',
			'F'	 => 'F',
			'P'	 => 'P',
			'好' => '好'
		);
		#主石对称
		$result['duichen'] = array(
			'EX' =>'EX',
			'VG' => 'VG',
			'G'	 => 'G',
			'F'	 => 'F',
			'P'	 => 'P',
			'好' => '好'
		);
		#主石荧光
		$result['yingguang'] = array(
			'N' =>'N',
			'F' => 'F',
			'M'	 => 'M',
			'S'	 => 'S',
            'SLT'=> 'SLT'
		);
		#托类型
		$result['tuo_type'] = array(
			'1' => '成品',
			'2' => '托',
			'3'	=> '空托女戒'
		);
		//主成色列表
		$result['caizhi'] = $goodsAttrModel->getZhuchengseList();
		//金料（键值一一对应）
		$result['jinliao'] = $goodsAttrModel->getCaizhiList();
        //颜色
        $result['jinse'] = $goodsAttrModel->getJinseList();
		return $result;
	}


	/**
	 *	search，列表
	 */
	public function search($params)
	{
		$result = array('success' => 0,'error' =>'');
		$goods_id = isset($params['goods_id']) ? trim($params['goods_id']) : '';
		if($goods_id == "")
		{
			$result['error'] = "货号不能为空";
			Util::jsonExit($result);
		}

		$model = new WarehouseGoodsModel(21);
		/** 获取商品详细 **/
		$g_result = $model->getGoodsByGoods_id($goods_id);
        $groupUser=new GroupUserModel(1);
        //获取 经销商批发价编辑组id=3 用户
        $userlist= $groupUser->getGroupUser(3);
        $is_edit_jingxiaoshangchengbenjia=0;
        if(SYS_SCOPE=='zhanting' && $userlist && in_array($_SESSION['userId'], array_column($userlist,'user_id')))
            $is_edit_jingxiaoshangchengbenjia=1;
		if(!count($g_result)){
			$result['error'] = "货号不存在，请检查";
			Util::jsonExit($result);
		}

		 /**获取商品图片**/
		$gallerymodel = new ApiStyleModel(21);
		$image_place = 1;
		$style_sn = $g_result['goods_sn'];
		//$res_pic_list = $gallerymodel->getProductGallery($style_sn,$image_place);
		$res_pic_list=$gallerymodel->getStyleGalleryList($style_sn);

		/**************/
		#取得钻石属性信息
		$goodsAttr = $this->getGoodsAttr();
		//var_dump($g_result);exit;
		$result['success'] = 1;
		$result['content'] = $this->fetch('warehouse_goods_search_list_edit.html',array(
			'dd' => new DictView(new DictModel(1)),
			'result' => $g_result,
            'res_pic_list' => $res_pic_list,
			'goodsAttr'=>$goodsAttr,
			'is_edit_jingxiaoshangchengbenjia' =>$is_edit_jingxiaoshangchengbenjia,
		));
		Util::jsonExit($result);
	}


	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = $params['id'];
		$goods_sn = trim($params['goods_sn']);
		if($id != true){
			$result['error'] = '请先输入货号';
			Util::jsonExit($result);
		}
		$newdo = array(
		    'id'=>$id,
		    'goods_sn'=>_Post::getString('goods_sn'),
		    'goods_name'=>_Post::getString('goods_name'),
		    'shoucun'=>_Post::getString('shoucun'),
		    'changdu'=>_Post::getString('changdu'),
		    'zhengshuleibie'=>_Post::getString('zhengshuleibie'),
		    'zhengshuhao'=>_Post::getString('zhengshuhao'),
		    'gemx_zhengshu'=>_Post::getString('gemx_zhengshu'),
		    'pinpai'=>_Post::getString('pinpai'),
		    //'zhushitiaoma'=>_Post::getString('zhushitiaoma'),
		    'buchan_sn'=>_Post::getString('buchan_sn'),
		    'caizhi'=>_Post::getString('caizhi'),
		    'jinzhong'=>_Post::getString('jinzhong'),
            'zongzhong'=>_Post::getString('zongzhong'),
		    'zuanshidaxiao'=>_Post::getString('zuanshidaxiao'),
		    'fushizhong'=>_Post::getString('fushizhong'),
		    'jietuoxiangkou'=>_Post::getString('jietuoxiangkou'),
		    'zhushixingzhuang'=>_Post::getString('zhushixingzhuang'),
		    'zhushiyanse'=>_Post::getString('zhushiyanse'),
		    'yanse'	=> _Post::getString('zhushiyanse'),
		    'qiegong'=>_Post::getString('qiegong'),
		    'zhushiqiegong'=>_Post::getString('qiegong'),
		    'duichen'=>_Post::getString('duichen'),
		    'yingguang'=>_Post::getString('yingguang'),
		    'paoguang'=>_Post::getString('paoguang'),
		    'tuo_type'=>_Post::getString('tuo_type'),
		    'zhushijingdu'=>_Post::getString('zhushijingdu'),
		    'jingdu'=>_Post::getString('zhushijingdu'),
		    );

		$newmodel =  new WarehouseGoodsModel($id,22);
		$olddo = $newmodel->getDataObject();
        $groupUser=new GroupUserModel(1);
        //获取 经销商批发价编辑组id=3 用户
        $userlist= $groupUser->getGroupUser(3);
        $is_edit_jingxiaoshangchengbenjia=0;
        if(SYS_SCOPE=='zhanting' && is_numeric(_Post::getString('jingxiaoshangchengbenjia')) && is_numeric(_Post::getString('management_fee')) && $userlist && in_array($_SESSION['userId'], array_column($userlist,'user_id')) && _Post::getString('jingxiaoshangchengbenjia')+_Post::getString('management_fee') >= $olddo['mingyichengben']){
            $is_edit_jingxiaoshangchengbenjia=1;
            $newdo['jingxiaoshangchengbenjia']=_Post::getString('jingxiaoshangchengbenjia')+_Post::getString('management_fee');
            $newdo['management_fee']=_Post::getString('management_fee');
        }


		$info = '';	//初始化修改明细




		/** 各种验证 **/
		$info = '';
		if(empty($goods_sn))
		{
			$result['error'] = '请输入货品款号';
			Util::jsonExit($result);
		}
		else if($goods_sn == $olddo['goods_sn'])
		{
			//$result['error'] = '您输入的款号和原来的一样';
			//Util::jsonExit($result);
		}
		else
		{   //款式修改了 ，需要验证款式有效性
			//如果款号是 “仅售现货” 或者是 裸石 那就绕过判断
			if($goods_sn !== "仅售现货" && $olddo['cat_type'] != '裸石' && $olddo['cat_type'] != '裸钻'  && $olddo['cat_type'] != '彩钻' && $goods_sn !='QIBAN')
			{
				$styleModel = new ApiStyleModel();
				$apiRes = $styleModel->GetStyleInfoBySn($goods_sn);
				//var_dump($apiRes);exit;
				if(empty($apiRes))
				{
					$result['error'] = '货品款号不存在';
					Util::jsonExit($result);
				}
				if($apiRes['check_status'] != 3)
				{
					$result['error'] = '款式非已审核状态';
					Util::jsonExit($result);
				}
			}
			$info .= "货品款号：【{$olddo['goods_sn']}】修改为【{$newdo['goods_sn']}】；";
		}
		
		if(trim($newdo['caizhi'])!='' && trim($newdo['caizhi']) !='无' && trim($newdo['caizhi']) !='其它' && trim($newdo['caizhi']) !='裸石'){
			if(strstr($newdo['goods_name'],$newdo['caizhi'])==false){
				$result['error'] = '商品名称必须包含材质:'.$newdo['caizhi'];
				Util::jsonExit($result);
			}
		}
		
		
		if(trim($olddo['zhushi'])=='钻石' && trim($olddo['cat_type1'])!='裸石' && strstr($newdo['goods_name'],$olddo['zhushi'])==false){
			$result['error'] = '新产品线为钻石的商品名称必须包含"'.$olddo['zhushi'].'"';
			Util::jsonExit($result);
		}
		if(!empty($newdo['pinpai'])){
		    $pinpaiArr = explode('/',$newdo['pinpai']);
		    $notExistStr = "";
		    foreach ($pinpaiArr as $vo){
    		    $num = $newmodel->select2("count(*)","zhengshuhao ='{$vo}'",1);
    		    if(!$num){
    		        $notExistStr .= "【".$vo."】";
    		    }
		    }
		    if($notExistStr !=''){
		    	//暂时注释 换完AGL证书号再加回证书
    		    $result['error'] = "品牌有问题:证书号{$notExistStr}不存在";
    		    Util::jsonExit($result);
		    }
		}
	    if(!empty($newdo['zhushitiaoma'])){
		    $zhushitiaomaArr = explode('/',$newdo['zhushitiaoma']);
		    $notExistStr = "";
			foreach ($zhushitiaomaArr as $vo){
    		    $num = $newmodel->select2("*","goods_id ='{$vo}'",1);
    		    if(!$num){
    		        $notExistStr .= "【".$vo."】";
    		    }
		    }
		   
		    if($notExistStr !=''){
    		    $result['error'] = "主石条码有问题:货号{$notExistStr}不存在";
    		    Util::jsonExit($result);
		    }
		}

		if(trim($olddo['shoucun']) != trim($newdo['shoucun']))
			$info .= "指圈号:【{$olddo['shoucun']}】修改为【{$newdo['shoucun']}】；";
		if(trim($olddo['goods_name']) != trim($newdo['goods_name']))
			$info .= "商品名字:【{$olddo['goods_name']}】修改为【{$newdo['goods_name']}】；";
		
		if(trim($olddo['changdu']) != trim($newdo['changdu']))
			$info .= "长度:【{$olddo['changdu']}】修改为【{$newdo['changdu']}】；";

		if(trim($olddo['zhengshuleibie']) != trim($newdo['zhengshuleibie']))
			$info .= "证书类型:【{$olddo['zhengshuleibie']}】修改为【{$newdo['zhengshuleibie']}】；";

		if(trim($olddo['zhengshuhao']) != trim($newdo['zhengshuhao']))
			$info .= "证书号:【{$olddo['zhengshuhao']}】修改为【{$newdo['zhengshuhao']}】；";

		if(trim($olddo['gemx_zhengshu']) != trim($newdo['gemx_zhengshu']))
			$info .= "GEMX证书号:【{$olddo['gemx_zhengshu']}】修改为【{$newdo['gemx_zhengshu']}】；";

		if(trim($olddo['pinpai']) != trim($newdo['pinpai']))
			$info .= "品牌:【{$olddo['pinpai']}】修改为【{$newdo['pinpai']}】；";
		
		//if($olddo['zhushitiaoma'] != $newdo['zhushitiaoma'])
		//    $info .= "主石条码:【{$olddo['zhushitiaoma']}】修改为【{$newdo['zhushitiaoma']}】；";

		if(trim($olddo['buchan_sn']) != trim($newdo['buchan_sn']))
			$info .= "布产号:【{$olddo['buchan_sn']}】修改为【{$newdo['buchan_sn']}】；";

		if(trim($olddo['caizhi']) != trim($newdo['caizhi']))
			$info .= "材质:【{$olddo['caizhi']}】修改为【{$newdo['caizhi']}】；";

		if(trim($olddo['jinzhong']) != trim($newdo['jinzhong']))
			$info .= "主成色重:【{$olddo['jinzhong']}】修改为【{$newdo['jinzhong']}】；";

        if(trim($olddo['zongzhong']) != trim($newdo['zongzhong']))
            $info .= "总重:【{$olddo['zongzhong']}】修改为【{$newdo['zongzhong']}】；";

		if(trim($olddo['zuanshidaxiao']) != trim($newdo['zuanshidaxiao']))
			$info .= "主石重:【{$olddo['zuanshidaxiao']}】修改为【{$newdo['zuanshidaxiao']}】；";

		if(trim($olddo['fushizhong']) != trim($newdo['fushizhong']))
			$info .= "副石重:【{$olddo['fushizhong']}】修改为【{$newdo['fushizhong']}】；";

		if(trim($olddo['jietuoxiangkou']) != trim($newdo['jietuoxiangkou']))
			$info .= "戒托实际镶口:【{$olddo['jietuoxiangkou']}】修改为【{$newdo['jietuoxiangkou']}】；";

		if(trim($olddo['zhushixingzhuang']) != trim($newdo['zhushixingzhuang']))
			$info .= "主石形状:【{$olddo['zhushixingzhuang']}】修改为【{$newdo['zhushixingzhuang']}】；";

		if(trim($olddo['zhushiyanse']) != trim($newdo['zhushiyanse']))
			$info .= "主石颜色:【{$olddo['zhushiyanse']}】修改为【{$newdo['zhushiyanse']}】；";

		if(trim($olddo['zhushijingdu']) != trim($newdo['zhushijingdu']))
			$info .= "主石净度:【{$olddo['zhushijingdu']}】修改为【{$newdo['zhushijingdu']}】；";

		if(trim($olddo['zhushiqiegong']) != trim($newdo['zhushiqiegong']))
			$info .= "主石切工:【{$olddo['zhushiqiegong']}】修改为【{$newdo['zhushiqiegong']}】；";

		if(trim($olddo['paoguang']) != trim($newdo['paoguang']))
			$info .= "主石抛光:【{$olddo['paoguang']}】修改为【{$newdo['paoguang']}】；";

		if(trim($olddo['duichen']) != trim($newdo['duichen']))
			$info .= "主石对称:【{$olddo['duichen']}】修改为【{$newdo['duichen']}】；";

		if(trim($olddo['yingguang']) != trim($newdo['yingguang']))
			$info .= "主石荧光:【{$olddo['yingguang']}】修改为【{$newdo['yingguang']}】；";

		if($is_edit_jingxiaoshangchengbenjia==1 && trim($olddo['jingxiaoshangchengbenjia']) != trim($newdo['jingxiaoshangchengbenjia']))
			$info .= "经销商批发价:【{$olddo['jingxiaoshangchengbenjia']}】修改为【".($newdo['jingxiaoshangchengbenjia']-$newdo['management_fee'])."】；";

		if($is_edit_jingxiaoshangchengbenjia==1 &&  trim($olddo['management_fee']) != trim($newdo['management_fee']))
			$info .= "管理费:【{$olddo['management_fee']}】修改为【{$newdo['management_fee']}】；";



		$attr  = $this->getGoodsAttr();
		$tuo_type = $attr['tuo_type'];
		$old_tuo_type = $olddo['tuo_type'];
		$new_tuo_type = $newdo['tuo_type'];
		if(trim($olddo['tuo_type']) != trim($newdo['tuo_type']))
			$info .= "托类型:【{$tuo_type[$old_tuo_type]}】修改为【{$tuo_type[$new_tuo_type]}】；";


		/** 各种验证 end **/
		//echo $olddo['caizhi'];
		//$goods_name = str_replace($olddo['caizhi'], trim($params['caizhi']), $olddo['goods_name']);
		//$newdo['goods_name'] = trim($goods_name);//exit;
		$res = $newmodel->saveData($newdo,$olddo);
		//update by liulinyan 20150721 需要在这里增加一个判断,如果影响的行数大于0并且返回值不等于1则运行下面添加日志记录操作
		$goods_id = $newmodel->getValue('goods_id');
		if($res === false)
		{
			$result['error'] = '货品修改失败';
		}
		elseif($res >= $id && $id !=1)
		{
			$newmodel->InsertLog($goods_id,"无");
			$result['success'] = 1;
			$result['error'] = '货品修改成功';
			//$result['error'] = '货品没做任何修改';
		}else{			
			$newmodel->InsertLog($goods_id,$info);
			$result['success'] = 1;
			$result['error'] = '货品修改成功';
		}
		if($res !==false){
			/*
    		//Api同步更新base_salepolicy_goods表砖石信息 begin
    		$data = array(
    		    'goods_name'=>$newdo['goods_name'],
    		    'xiangkou'=>$newdo['jietuoxiangkou'],
    		    'stone'   =>$newdo['jietuoxiangkou'],
    		    'finger'  =>$newdo['shoucun'],
    		);    		
    		$goods_attr = $this->getGoodsAttr();
    		$caizhi_arr = $goods_attr['jinliao'];
    		$jinse_arr = $goods_attr['jinse'];
    		
    		$caizhi_keys_arr = array_flip($caizhi_arr);
    		$jinse_keys_arr  = array_flip($jinse_arr);
    		$caizhi_upper = strtoupper($newdo['caizhi']);
    		if(preg_match('/[0-9a-z]+/i',$caizhi_upper,$caizhi_jinse)){
    		    $caizhi = strtoupper($caizhi_jinse[0]);
    		    $jinse  = substr($caizhi_upper,strlen($caizhi_jinse[0]));
    		    if (isset($caizhi_keys_arr[$caizhi])) {
    		        $data['caizhi'] = $caizhi_keys_arr[$caizhi];
    		    }
    		    if(isset($jinse_keys_arr[$jinse])){
    		        $data['yanse'] = $jinse_keys_arr[$jinse];
    		    }
    		}else{
    		    if (isset($caizhi_keys_arr[$caizhi_upper])) {
    		        $data['caizhi'] = $caizhi_keys_arr[$caizhi_upper];
    		    }
    		    $data['yanse'] = 0;
    		}
    		$baseSalepolicyGoodsModel = new BaseSalepolicyGoodsModel(17);
    		$res = $baseSalepolicyGoodsModel->update($data,"goods_id='{$goods_id}'");    		
    		//Api同步更新base_salepolicy_goods表砖石信息 end
    		 * */
		}
		Util::jsonExit($result);
	}

	/** 货品修改明细 **/
	public function getUpdateLog($params){
		$goods_id = $params['goods_id'];
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'goods_id'	=>$goods_id
		);

		$model = new WarehouseGoodsModel(21);

		$where = array('goods_id'=>$goods_id);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$data = $model->pageListByLog($where,$page,5,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'loglist';

		$this->render('log_list.html',array(
			'pa' =>Util::page($pageData),
			'data' => $data,
		));
	}
	
	private function resolveCaizhiAndYanse() {
	    
	}

}?>