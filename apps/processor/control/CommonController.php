<?php
/**
 *  -------------------------------------------------
 *   @file		: CommonController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-03
 *   @update	:
 *  -------------------------------------------------
 */
class CommonController extends Controller
{
	protected $whitelist = array();
	protected $gifts = array(1=>'赠送珍珠耳钉',2=>'赠送S925银链',3=>'赠送黑皮绳',4=>'赠送红玛瑙手链',5=>'赠送白色手提袋',6=>'赠送情人节礼盒',7=>'赠送红绳',8=>'赠送手绳',9=>'赠送砗磲手链',10=>'赠送粉晶手链',11=>'赠送金条红包0.02g',12=>'赠送首饰盒',13=>'耳堵');
	protected function init ($act,$c)
	{
		if(!Util::isAjax())
		{
			if(!in_array($act,$this->whitelist))
			{
				Route::setAsAutoLaunch($_SERVER['REQUEST_URI'], _Request::getString("title", "生产管理"));
				header('Location:/index.php');
				die();
			}
		}
		else
		{
			if($c!='Main')
			{
				if(!Auth::getMenuAuth($c)){
					die('没有菜单权限');
				}
				if(!Auth::getOperationAuth($c,$act))
				{
					die('没有操作权限');
				}
			}
		}
		$this->dd = new DictView(new DictModel(1));
		$this->assign('dd',$this->dd);//数据字典

	}
	public function index ($params){
		die('forbidden');
	}
	//根据工厂id+款号获取对应模号和镶口
	public function GetFactoryStyleInfo($arr=array())
	{
		if(!empty($arr))
		{
		    /*
			$ori_str=$arr;
			ksort($ori_str);
			$ori_str=json_encode($ori_str);
			$data=array("filter"=>$ori_str,"sign"=>md5('style'.$ori_str.'style'));
			$ret=Util::httpCurl(Util::getDomain().'/api.php?con=style&act=GetFactryInfo',$data);

			$ret=json_decode($ret,true);
			if($ret['error']!=1)
			{
				return $ret['return_msg'];
			}
			else
			{

				return array();
			}
			*/
			foreach ($arr as $k->$v) {
			    $keys[] = $k;
			    $vals[] = $v;
			}
			
			$api = new ApiModel();
			return $api->style_api($keys, $vals, 'GetFactryInfo');
		}
		return array();

	}
	//取模号和镶口
	  public function GetBcNote($params){
	     
        $id = isset($params['id'])?$params['id']:0;
        $style_sn = isset($params['style_sn'])?$params['style_sn']:'';        
        if($style_sn=='' || $style_sn=='QIBAN'){
            return array();
        }
		$attrModel =  new ProductInfoAttrModel(13);
		$productModel = new ProductInfoModel(13);
		$row=$productModel->getAttrInfoByBcID($id);
		$cart=isset($row['cart'])?$row['cart']:'';
		if(empty($cart))
		{
			$cart=isset($row['diamond_size'])?$row['diamond_size']:'';
		}
		if(empty($cart))
		{
			$cart=isset($row['zuanshidaxiao'])?$row['zuanshidaxiao']:'';
		}
		//获取镶口
		$xiangkou = "";
		if(!empty($cart)){
			if(is_numeric($cart))
			{
				$xiangkou = $productModel->GetXiangKou($cart);
			}else{
				$xiangkou = str_ireplace("ct", "", $cart);
				$xiangkou = $productModel->GetXiangKou($xiangkou);
			}
		}
		//根据款号+镶口 获取布产提示 （既工厂 与 模号）
		$purchaseModel = new ProductInfoPurchaseModel(13);
		$res = $purchaseModel->getStyleXiangKouByWhere(array('style_sn' => $style_sn, 'xiangkou' => $xiangkou));
		//$res = ApiStyleModel::GetStyleXiangKouByWhere($style_sn , $xiangkou);
		//查不到信息,
		if(isset($res[0])){
			return array();
		}else{
    		$proModel = new AppProcessorInfoModel(13);
    		$arr=array(
    			'factory_name' => $proModel->getProcessorName($res['factory_id']) ,
    			'factory_sn' => $res['factory_sn'],
    			'xiangkou'=>$xiangkou
    		);
    		return $arr;
		}
    }

	//开始生产布产数据推送给工厂 ADD BY ZHANGRUIYING
	public function send_to_factory($id)
	{
		$res=array('success'=>0,'error'=>'');
		$client = new SoapClient("http://121.199.48.176:11101/service/PowerthinkInterface.asmx?wsdl",array('trace'=> true,'exceptions'=>0,'cache_wsdl'=>WSDL_CACHE_NONE));
		$xml=file_get_contents('./public/xml/to_factory.xml');
		$model=new ProductInfoModel($id,13);
		$style_attr=$this->GetBcNote(array('id'=>$id,'style_sn'=>$model->getValue('style_sn')));
		if(empty($style_attr['factory_sn']))
		{
			$res['error']='模号为空不允许推送工厂生产';
			$res['success']=0;
			return $res;
		}
		$attr=$model->getAttrInfoByBcID($id);
		//BDD材质转换为中豪颜色
		$cz = array(
		"18k白"		=>	array('color'=>"18K白",'code'=>'01'),
		"18k白色"	=>	array('color'=>"18K白",'code'=>'01'),
		"18k玫瑰金"	=>	array('color'=>"18K红",'code'=>'02'),
		"18k黄"		=>	array('color'=>"18K黄",'code'=>'03'),
		"pt950"		=>	array('color'=>"Pt950",'code'=>'07'),
		"pt950白"	=>	array('color'=>"Pt950",'code'=>'07'),
		"18k彩金"	=>	array('color'=>"18K分色",'code'=>'04'),
		"14k白"		=>	array('color'=>"14K白",'code'=>'14'),
		"14k玫瑰金"	=>	array('color'=>"14K红",'code'=>'15'),
		"14k黄"		=>	array('color'=>"14K黄",'code'=>'16'),
		"9k白"		=>	array('color'=>"9K白",'code'=>'17'),
		"9k黄"		=>	array('color'=>"9K黄",'code'=>'18'),
		"9k玫瑰金"	=>	array('color'=>"9K红",'code'=>'19')
		);
		$from_type=$model->getValue('from_type');
		$kezi=isset($attr['kezi'])?$attr['kezi']:'';
		if(empty($style_attr['xiangkou']))
		{
			$style_attr['xiangkou']=isset($attr['xiangkou'])?$attr['xiangkou']:'';
		}
		$color=isset($attr['18kkezuoyanse'])?$attr['18kkezuoyanse']:'';
		$caizhi=isset($attr['caizhi'])?strtolower($attr['caizhi']):'';
		$zhiquan=isset($attr['zhiquan'])?$attr['zhiquan']:'';
		$num=isset($attr['g_num'])?$attr['g_num']:'';
		if(empty($color) and $color!='无')
		{
			$color=isset($attr['jinse'])?$attr['jinse']:'';
		}
		if(empty($color) and $color!='无')
		{
			$color=isset($attr['18k_color'])?$attr['18k_color']:'';
		}
		if(empty($color) and $color!='无')
		{
			$color=isset($attr['yanse'])?$attr['yanse']:'';
		}
		if(empty($color) and $color!='无')
		{
			$color=isset($attr['color'])?$attr['color']:'';
		}
		if(empty($num))
		{
			$num=isset($attr['num'])?$attr['num']:1;
		}
		$zhuchengse=isset($cz[$caizhi.$color]['code'])?$cz[$caizhi.$color]['code']:'00';
		$esmt_time=$model->getValue('esmt_time');
		if($esmt_time=='0000-00-00')
		{
			$normal_time = time()+(3600*24);
			$esmt_time = date("Y-m-d",$normal_time);
		}
		$arr=array(
			'{$order_id}'=>$model->getValue('p_sn'),
			'{$shipping_date}'=>$esmt_time,
			'{$material_code}'=>$zhuchengse,//成色
			'{$printed_word}'=>$kezi?$kezi:'',//刻字
			'{$remark}'=>'客户：'.$model->getValue('consignee')." 款号：".$model->getValue('style_sn').' 镶口：'.$style_attr['xiangkou'],
			'{$customer_name}'=>$model->getValue('consignee'),
			'{$style_sn}'=>$style_attr['factory_sn'],//模号
			'{$ring_size}'=>$zhiquan,
			'{$size}'=>$style_attr['xiangkou'],
			'{$quantity}'=>1,
			'{$production_id}'=>$model->getValue('bc_sn')
		);
		$xml=str_replace(array_keys($arr),array_values($arr),$xml);
		$client->soap_defencoding = 'utf-8';
		$client->xml_encoding = 'utf-8';
		$o_res=$client->Save($params=array('user'=>'016','password'=>'2B4A4F12-5E72-4A9F-A002-1EA87C624D74','tb'=>'','xmlAdd'=>$xml,'xmlModify'=>'','xmlDel'=>''));
		if($o_res->SaveResult=='添加成功')
		{
			$res['success']=1;
		}
		else
		{
			$res['error']=$o_res->SaveResult;
			file_put_contents('./apierror.txt',print_r($res['error']."\n",true),FILE_APPEND);
		}
		return $res;
	}
	 public function getChannelArr()
	{
        $SalesChannelsModel = new SalesChannelsModel(1);
        $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
		$arr=array();
		if(!empty($channellist))
		{
			foreach($channellist as $key=>$v)
			{
				$arr[$v['id']]=$v['channel_name'];
			}
		}
		return $arr;
    }

    /**
     * 替换刻字特殊字符串
     * @param type $kezi
     * @return string
     */
    public function replaceTsKezi($kezi='')
    {
        if($kezi!=''){

            //替换刻字特殊字符串
            $kezi = str_replace('a01','\\',$kezi);
            $kezi = str_replace('a02','\'',$kezi);
            $kezi = str_replace('a03','"',$kezi);
        }
        return $kezi;
    }
}
?>