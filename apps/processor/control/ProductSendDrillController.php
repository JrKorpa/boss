<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductsenddrillController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-11 11:16:16
 *   @note		: 生成送钻信息
 *  -------------------------------------------------
 */
class ProductSendDrillController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array('downSendDrillCSV');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('product_send_drill_search_form.html');
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$bc_arr = $params['bc_sn_arr'];
//		$bc_arr = str_replace("\r"," ",$bc_arr);
//		$bc_arr = str_replace("\n"," ",$bc_arr);
//		$bc_arr = explode(" ",$bc_arr);
		$model = new ProductInfoModel(13);
		$data = $model->filterBcsn($bc_arr);
		echo !empty($data)?1:0;
	}

	public function proofSearch(){
		$zhengshuhao  = _Post::getString('zhengshuhao');
		$model = new ProductInfoModel(13);
		$info = $model->getInfoByCretificate($zhengshuhao);
		//print_r($info);exit;
		$saleView = new SalesChannelsView(new SalesChannelsModel(1));
		$custView = new CustomerSourcesView(new CustomerSourcesModel(1));
		$this->render('product_zhengshu_search_list.html',[
			'bc_info'=>$info['bc_info'],'saleView'=>$saleView,
			'order_info'=>$info['order_info'],'custView'=>$custView
		]);
	}

	/**
	 * 下载送钻信息
	 */
	public function downSendDrillCSV(){
		$bc_arr = _Request::getString('bc_sn_arr');
//		$bc_arr = str_replace("\r"," ",$bc_arr);
//		$bc_arr = str_replace("\n"," ",$bc_arr);
//		$bc_arr = explode(" ",$bc_arr);
		$bc_arr = explode(',',$bc_arr);
		foreach ($bc_arr as $k=>$v) {
			if(empty($v)){unset($bc_arr[$k]);}
		}
		$model = new ProductInfoModel(13);
		$data = $model->getSendDrillInfo($bc_arr);
		$title = ['布产号','客户姓名','石重','颜色','净度','证书号'];
		foreach ($data as $val) {
			$cart_unit = !empty($val['cart']) && strtoupper(substr($val['cart'],-2,2))!= 'CT' ? 'ct ' : '';
			$zhengshuhao_unit = !empty($val['zhengshuhao']) ? 'KL-' : '';
			$zhengshuhao_space = !empty($val['zhengshuhao']) ? ' ' : '';
			$val = [$val['bc_sn'],$val['consignee'],$val['cart'].$cart_unit,$val['color'],$val['clarity'],$zhengshuhao_unit.$val['zhengshuhao'].$zhengshuhao_space];
			$val = eval('return '.iconv('utf-8','gbk',var_export($val,true).';')) ;
			$content[] = $val;
		}
		$model->createCSV('dialist',$title,$content);
	}


	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		die('开发中');
		$id = intval($params["id"]);
		$this->render('product_send_drill_show.html',array(
			'view'=>new ProductsenddrillView(new ProductsenddrillModel($id,1))
		));
	}

}

?>