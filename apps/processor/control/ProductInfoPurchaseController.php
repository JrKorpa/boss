<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangruiying
 *   @date		: 2015/5/8
 *   @update	:
 *  -------------------------------------------------
 */
class ProductInfoPurchaseController extends CommonController
{
	protected $smartyDebugEnabled = true;
    protected $whitelist = array('PrintPurchse');
	function getPurchaseType()
	{
	    /*
			$ori_str=array();
			ksort($ori_str);
			$ori_str=json_encode($ori_str);
			$data=array("filter"=>$ori_str,"sign"=>md5('purchase'.$ori_str.'purchase'));
			$ret=Util::httpCurl(Util::getDomain().'/api.php?con=purchase&act=GetPurchaseType',$data);
			$ret=json_decode($ret,true);
			$type=$ret['return_msg'];
			return $type;
			*/
	    $api = new ApiModel();
	    $ret = $api->purchase_api(array(), array(), 'GetPurchaseType');
	    return $ret['data'];
	}
	public function index ($params)
	{
        $facmodel = new AppProcessorInfoModel(13);
		$process = $facmodel->getProList();
		$this->render('product_info_purchase_search_form.html',array(
			'bar'=>Auth::getBar(),
			'process' => $process,
		));
	}
	/**
	 *	search，列表
	 */
	public function search ($params)
	{
            $args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'p_sn'	=> _Request::get("p_sn"),
			);

		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		if(!empty($args['p_sn']))
		{
			$args['p_sn']=preg_split('/\s/',$args['p_sn']);
			$args['p_sn']="'".implode("','",$args['p_sn'])."'";
		}
		$ori_str=array('p_sn'=>$args['p_sn'],'page'=>$page,'pageSize'=>10);
		//ksort($ori_str);
		//$ori_str=json_encode($ori_str);
		//$data=array("filter"=>$ori_str,"sign"=>md5('purchase'.$ori_str.'purchase'));
		//$ret=Util::httpCurl(Util::getDomain().'/api.php?con=purchase&act=GetProductInfoPurchaseList',$data);
		//$ret=json_decode($ret,true);
		//$data =$ret['return_msg'];
		$purchasemodel = new PurchaseModel(23);
		$data=$purchasemodel->GetProductInfoPurchaseList($ori_str);
		if(!empty($data))
		{
			$type=$this->getPurchaseType();
			$ids=array();
			foreach($data['data'] as $key=>$v)
			{
				$data['data'][$key]['t_id']=isset($type[$v['t_id']])?$type[$v['t_id']]:$v['t_id'];
				$ids[]=$v['p_sn'];
			}
			$model=new ProductInfoPurchaseModel(13);
			$status=$model->getPurchaseBuchanFacOpra($ids);//生产状态统计采购单生产情况
			$opra_uname=$model->getPurchaseOpraUname($ids);//采购单接单人如果多个就显示多个
			if($status)
			{
				foreach($data['data'] as $key=>$v)
				{
					$data['data'][$key]['status']=isset($status[$v['p_sn']])?$status[$v['p_sn']]:array();
					$data['data'][$key]['opra_uname']=isset($opra_uname[$v['p_sn']])?$opra_uname[$v['p_sn']]:'';
				}
			}
		}
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'product_info_purchase_search_page';
		$this->render('product_info_purchase_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'view' => new ProductInfoView(new ProductInfoModel(13))

		));

	}
	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
	    $id = intval($params["id"]);
		//通过节口调取采购单信息
		/*
		$ori_str=array('id'=>$id);
		ksort($ori_str);
		$ori_str=json_encode($ori_str);
		$data=array("filter"=>$ori_str,"sign"=>md5('purchase'.$ori_str.'purchase'));
		$ret=Util::httpCurl(Util::getDomain().'/api.php?con=purchase&act=GetPurchaseInfo',$data);
		$ret=json_decode($ret,true);
		$row=$ret['return_msg'];
		*/
	    $api = new ApiModel();
	    $ret = $api->purchase_api(array('id'), array($id), 'GetPurchaseInfo');
	    $row=$ret['data'];
		$type=$this->getPurchaseType();
		if($row)
		{
			$row['t_id']=isset($type[$row['t_id']])?$type[$row['t_id']]:$row['t_id'];
		}
		$model=new ProductInfoPurchaseModel($id,13);
		$this->render('product_info_purchase_show.html',array(
			'bar'=>Auth::getViewBar(),
			'row'=>$row
		));
	}
	//详情采购ID对应的布产列表
	public function searchProductInfo($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'p_sn'	=> _Request::get("id"),
			'p_sn_ids'=>_Request::get("ids"),
			);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$model=new ProductInfoPurchaseModel(13);
		$data=$model->GetListByPurchaseId($args,$page);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'product_info_purchase_info_search_page';
		$this->render('product_info_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'view' => new ProductInfoView(new ProductInfoModel(13))

		));
	}
	public function check_remote_file_exists($url)
	{
			$curl = curl_init($url);
			// 不取回数据
			curl_setopt($curl, CURLOPT_NOBODY, true);
			// 发送请求
			$result = curl_exec($curl);
			$found = false;
			// 如果请求没有发送失败
			if ($result !== false) {
				// 再检查http响应码是否为200
				$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				if ($statusCode == 200) {
					$found = true;
				}
			}
			curl_close($curl);
			return $found;
	}
	
	//打印采购布产单列表/
	function PrintPurchse($params)
	{
        $p_sn_ids = _Request::get('_ids');
        $model = new ProductInfoPurchaseModel(13);
        $styleModel = new StyleModel(27);
        $ret = $model->getPurchaseList($p_sn_ids);

		if(!empty($ret))
		{
		    $type = $model->getPurchaseType();
		    
			$pmodel = new ProductInfoModel(13);
			$attrModel = new ProductInfoAttrModel(13);

			$style_imgs = array();
			foreach ($ret as $key =>$row)
			{
				$temp=$model->GetAllByPurchaseId(array('p_sn'=>$row['p_sn']));
				foreach($temp as $k=>$t)
				{
				    
				    if(!in_array($t['style_sn'],$style_imgs)) {
				        //获取image_place大于0的且image_place最小的图片记录
				        $img_ret = $styleModel->getAppStyleGalleryRow("style_sn='{$t['style_sn']}' and image_place>0");
				        $style_imgs[$t['style_sn']] = empty($img_ret['thumb_img'])?'':$img_ret['thumb_img'];
				    }
				    
				    $temp[$k]['img']=$style_imgs[$t['style_sn']];
					$temp[$k]['attr']=$attrModel->getGoodsAttr($t['id']);
					$temp[$k]['goods_name'] = $styleModel->getStyleNameByStyleSn($t['style_sn']);
					$temp[$k]['m_sn']   = $this->_getFactorySn(array('id'=>$t['id'], 'style_sn'=>$t['style_sn'] , 'prc_id' =>$t['prc_id']));
					$temp[$k]['bc_time']=$pmodel->getBcTime($t['id']);
				}
				$ret[$key]['product_list']=$temp;
				$ret[$key]['t_id']=isset($type[$row['t_id']])?$type[$row['t_id']]:'';

			}
			
		}

		$this->render('print_inventory_report.html',array(
					'data'=>$ret
		));
	}
    //获取布产单工厂模号
	private function _getFactorySn($params){
	
	    $id = isset($params['id'])?$params['id']:0;
	    $style_sn = isset($params['style_sn'])?$params['style_sn']:'';
	    if($style_sn=='' || $style_sn=='QIBAN'){
	        return '';
	    }
	    $attrModel =  new ProductInfoAttrModel(13);
	    $productModel = new ProductInfoModel(13);
	    $row=$productModel->getAttrInfoByBcID($id);
	    $cart=isset($row['cart'])?$row['cart']:'';
	    if(empty($cart))
	    {
	        $cart=isset($row['xiangkou'])?$row['xiangkou']:'';
	    }
		if(empty($cart))
	    {
	        $cart=isset($row['zuanshidaxiao'])?$row['zuanshidaxiao']:'';
	    }
	    if(!is_numeric($cart))
	    {
			//从钻石大小文字中提取数值
			if (preg_match('/(\d+)\.?(\d+)?/is',$cart,$match)){
				$cart = $match[0];
			}
	    }
	    //根据款号+镶口 获取布产提示 （既工厂 与 模号）
	    $purchaseModel = new ProductInfoPurchaseModel(13);
	    $res = $purchaseModel->GetFactoryStyleFromXiangKou($style_sn, $cart, $params['prc_id']);
	    //$res = ApiStyleModel::GetStyleXiangKouByWhere($style_sn , $xiangkou);
	    //查不到信息,
	    if(!empty($res['factory_sn'])){
	        return $res['factory_sn'];
	    }else{
	        return '';
	    }
	}
	public function GetProductImg($id,$style_sn,&$purchaseModel)
	{
		$imgModel=new ProductInfoImgModel(13);
		$img=$purchaseModel->getStyleGalleryInfo(array('style_sn'=> $style_sn,'image_place' => 1));
		$result=isset($img['thumb_img'])?$img['thumb_img']:'';
		if(empty($result)){
			$img=$imgModel->getImgList($id);
			$result=isset($img['thumb_img'])?$img['thumb_img']:'';
		}
		else
		{
			if(!$this->check_remote_file_exists($result))
			{
				$img=$imgModel->getImgList($id);
				$result=isset($img['thumb_img'])?$img['thumb_img']:'';
			}
		}
		return $result;
	}
	
	public function GetProductNameByIds($model, $style_sn_ids=array())
	{
		$ret = $model->getStyleNameListByStyleSn(array('ids'=> $style_sn_ids));
		$arr=array();
		if(!empty($ret))
		{
			foreach($ret['return_msg'] as $key=>$v)
			{
				$arr[$v['style_sn']]=$v['style_name'];
			}
		}
		return $arr;
	}
	
	//获取采购单下所有布产单
	function getPurchaseProduct($id)
	{
	    /*
		$ori_str=array('id'=>$id);
		ksort($ori_str);
		$ori_str=json_encode($ori_str);
		$data=array("filter"=>$ori_str,"sign"=>md5('purchase'.$ori_str.'purchase'));
		$ret=Util::httpCurl(Util::getDomain().'/api.php?con=purchase&act=GetPurchaseInfo',$data);
		$ret=json_decode($ret,true);*/
	    
	    $api = new ApiModel();
	    $ret = $api->purchase_api(array('id'), array($id), 'GetPurchaseInfo');
	    
		$p_sn=isset($ret['data']['p_sn'])?$ret['data']['p_sn']:'';
		if(empty($p_sn))
		{
			return array();
		}
		$model=new ProductInfoPurchaseModel(13);
		$temp=$model->GetAllByPurchaseId(array('p_sn'=>$p_sn));
		return $temp;

	}
	//采购单下布产单批量生产
	public function StartProduction($params)
	{

		$result = array('success' =>0,'error'=>'','is_refresh'=>0);
		$id = intval($params["id"]);
		$temp=$this->getPurchaseProduct($id);
		$model=new ProductInfoPurchaseModel(13);

		$str='采购单下共{$n}个布产单，执行成功{$m}个，失败{$k}个！<br />';
		$success_ids=array();
		$error_ids=array();
		$error_ids1=array();
		$prc_ids=array();
		$k=0;
		$m=0;
		if($temp)
		{
			foreach ($temp as $key => $val )
			{
				if($val['status']!=3)
				{
					$error_ids[]=$val['bc_sn'];
					$k++;
					continue;
				}
				$success_ids[]=$val['id'];
				$prc_ids[]=$val['prc_id'];
				$m++;
			}
			unset($temp);
		}
		$str=str_replace(array('{$n}','{$m}','{$k}'),array(($k+$m),$m,$k),$str);
		//更新布产状态
		if(!empty($success_ids))
		{
			$res=$model->mutiUpdateProductionStatus($success_ids,$prc_ids);
			if($res)
			{
				if(!empty($error_ids))
				{
					$error_ids=implode(',',$error_ids);
					$str.=$error_ids."：布产状态不是已分配！<br />";
				}
				if(!empty($error_ids1))
				{
					$error_ids1=implode(',',$error_ids1);
					$str.=$error_ids1."：生产状态不是未操作！<br />";
				}
				$result['error']=$str;
				$result['is_refresh']=1;
			}
			else
			{
				$result['error']='系统出错，请联系开发人员！';
			}
		}
		else
		{
			$result['error']='该采购单下没有布产单或布产单状态不是已分配，不能开始生产！';
		}
		//接口调用发送到工厂
		$ids =isset($params["ids"])?$params["ids"]:'';
		if(!empty($ids))
		{
			$ids=explode(',',$ids);
			$send_error='工厂接口返回状态<br />';
			$product_info_model = new ProductInfoModel(13);
			foreach($ids as $v)
			{
				$res=$this->send_to_factory($v);
				if($res['success']!=1)
				{
				    $bc_sn = $product_info_model->get_bc_sn($v, false);
					$send_error.=$bc_sn.':'.$res['error']."<br />";
				}
			}
			$result['success']=0;
			$result['error'].=$send_error;
		}

		Util::jsonExit($result);

	}
	//检查下选中的选项是否有需要向工厂推送数据
	function CheckToFactory($params)
	{
		$res=array('status'=>0,'success'=>0,'error'=>'');
		$id=isset($params['id'])?$params['id']:'';
		$list=$this->getPurchaseProduct($id);
		if(!empty($list))
		{
			foreach($list as $k=>$v)
			{
				if(in_array($v['prc_id'],array(452,416)) and $v['status']==3 and $v['from_type']==2)
				{
					$res['status']=1;
					$res['success']=1;
				}
				if($v['status']==3)
				{
					$ids[]=$v['id'];
				}
			}
			if(empty($ids))
			{
				$res['status']=4;
				$res['success']=1;
				$res['error']='采购单布产单状态不是已分配，不能开始生产！';
			}
		}
		else
		{
			$res['status']=2;
			$res['success']=1;
			$res['error']='采购单下无布产单';
		}
		Util::jsonExit($res);
	}
	//开始生产页面
	function StartProductionEdit($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$data=$this->getPurchaseProduct($id);
		$ids=array();
		foreach($data as $k=>$v)
		{
			if(in_array($v['prc_id'],array(452,416)) and $v['status']!=3 and $v['from_type']==2)
			{
				$ids[]=$v['bc_sn'];
			}
			else
			{
				unset($data[$k]);
			}
		}
		$result['content'] = $this->fetch('send_to_factory.html',array(
		'data'=>$data,
		'id'=>$id
		));
		$result['title'] = '推送工厂生产';
		Util::jsonExit($result);
	}





}
?>