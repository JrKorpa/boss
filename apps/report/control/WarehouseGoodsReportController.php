<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseGoodsReportController.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseGoodsReportController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array("search","daochu","printCode");

	/****
	获取公司 列表
	****/
	public function company()
	{
		$model     = new CompanyModel(1);
		$company   = $model->getCompanyTree();//公司列表
		return $company;
	}
	/***
	获取有效的仓库
	***/
	public function warehouse()
	{
		$model_w	= new WarehouseModel(21);
		$warehouse  = $model_w->select(array('is_delete'=>1),array("id","name"));
		return $warehouse;
	}
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$args=array(	
			'company_id'	=> _Request::get("company_id"),
			'warehouse_id'	=> _Request::get("warehouse_id"),
		);
		$goodsAttrModel = new GoodsAttributeModel(17);
		$caizhi_arr = $goodsAttrModel->getCaizhiList();
		$jinse_arr  = $goodsAttrModel->getJinseList();
                //供应商
		$model_p = new ApiProModel();
		$pro_list = $model_p->GetSupplierList(array('status'=>1));
		
		$model = new WarehouseGoodsModel(21);
		$productTypeArr=$model->getProducts_type();		
		$cutTypeArr=$model->getCat_type();
		//echo '<pre>';
		//print_r($productTypeArr);die();

		 /** 主石颜色/净度 **/
		$color_arr = array('D','D-E','E','F','F-G','G','H','I','I-J','J','K','K-L','白色','彩钻','蓝','粉','橙','绿','红','香槟','格雷恩','紫','混色','蓝紫色','黑','变色','其他');
		$clarty_arr= array('FL','IF','VVS', 'VVS1','VVS2','VS', 'VS1','VS2','SI', 'SI1','SI2','I1','P','不分级');
		//类型/
		//$jintuo_type = array('成品','女戒','空托女戒');
		$jinshi_type = array('3D','精工','普通');
		$zhengshuleibie = array('NGDTC','GIA','IGI','NGTC','HRD','AGL','EGL','NGGC','NGSTC','HRD-D');
                $luozuanzhengshu = array('NGDTC','GIA','IGI','NGTC','HRD','AGL','EGL','HRD-D','NGGC');
		$zhushi = array('钻石','彩钻','蓝宝','红宝','珍珠','翡翠','锆石','水晶','珍珠贝','和田玉','砭石','玛瑙','砗磲','淡水珍珠','海水珍珠');
		$chanpinxian = array('其他饰品','黄金等投资产品','素金饰品','黄金饰品及工艺品','钻石饰品','彩钻饰品','珍珠饰品','彩宝饰品','成品钻','翡翠饰品','配件及特殊包装','非珠宝');
		$xilie = array('天鹅湖','天使之吻','怦然心动','UNO','天使之翼','星耀','小黄鸭','天生一对','基本款','O2O爆款','轻奢','PINK EMILY','Attractive','缤纷','挚爱','城堡',);
		$apiStyleModel = new ApiStyleModel();
		$catList = $apiStyleModel->getCatTypeInfo();
		$this->render('warehouse_goods_report_search_form.html',array(
			'bar'	=> Auth::getBar(),
			'dd'	=> new DictView(new DictModel(1)),
			'caizhi_arr'=>$caizhi_arr,
		    'jinse_arr'=>$jinse_arr,
			'catList' => $catList,
			'pro_list' => $pro_list,
			'warehouselist' => $this->warehouse(),	//仓库列表
			'companylist' => $this->company(),		//公司列表
			'color_arr' =>$color_arr,
			'clarty_arr' => $clarty_arr,
			//'jintuo_type' => $jintuo_type,
			'jinshi_type' => $jinshi_type,
			'zhengshuleibie'=> $zhengshuleibie,
            'luozuanzhengshu' =>$luozuanzhengshu,
			'zhushi' => $zhushi,
			'chanpinxian' => $chanpinxian,
			'xilie' =>$xilie,
			'chanpinxian1' =>$productTypeArr,
			'catList1' => $cutTypeArr,
			'args'=>$args,
		));
	}

	/**
	 *	search，列表
	 */
	public function search($params)
	{
	    ini_set('memory_limit', '-1');
	    set_time_limit(0);
		//echo '<div style="display:none;"><pre>';print_r($params);echo '</pre></div>';
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'down_info' => 	_Request::get('down_info')?_Request::get('down_info'):'',
			'put_in_type'=> _Request::get("put_in_type"),
			'weixiu_status'=> _Request::get("weixiu_status"),
			'is_on_sale'=> _Request::get("is_on_sale"),
			'caizhi'=> _Request::get("caizhi"),
		    'jinse'=> _Request::get("jinse"),
			'goods_id'	=> _Request::get("goods_id"),
            'pinpai'         =>  _Request::get("pinpai"),
			'style_sn'	=> _Request::get("style_sn"),
			'cat_type'	=> _Request::get("cat_type"),
			'cat_type1'	=> _Request::get("cat_type1"),
			'company_id'	=> _Request::get("company_id"),
			'warehouse_id'	=> _Request::get("warehouse_id"),
			'company_ids_string'	=> _Request::get("company_ids_string"),
			'warehouse_ids_string'	=> _Request::get("warehouse_ids_string"),
			'types_string'=>trim(_Request::get("types_string")),
			'zhengshuhao'   => _Request::get("zhengshuhao"),
			'order_goods_ids' => _Request::get("order_goods_ids"),//是否绑定
			'shoucun'    => _Request::get("shoucun"),
			'kucunstart' => _Request::get("kucun_start"),
			'kucunend'   => _Request::get("kucun_end"),
			'processor'  => _Request::get("processor"),
			'buchan'     => _Request::get("buchan"),//布产号
			'mohao'      => _Request::get("mohao"),
			'zhushi'     => _Request::get("zhushi"),
			'zhengshuleibie' => _Request::get("zhengshu_type"),
            'luozuanzhengshu' => _Request::get("luozuanzhengshu"),
            'xilie_name' => _Request::get("xilie_name"),
			'jinzhong_begin' => _Request::get("jinzhong_begin"),
			'jinzhong_end'   => _Request::get("jinzhong_end"),
			'zs_color'   => _Request::get("zs_color"),
			'zs_clarity' => _Request::get("zs_clarity"),
			'jintuo_type' => _Request::get('jintuo_type'),
			'jinshi_type' => _Request::get("jinshi_type"),
			'jiejia'     => _Request::get("jiejia"),
			'guiwei'     => _Request::get("guiwei"),
			'chanpinxian' => _Request::get("chanpinxian"),
			'chanpinxian1' => _Request::get("chanpinxian1"),
			'zhushi_begin'=> _Request::get("zhushi_begin"),
			'zhushi_end'  => _Request::get("zhushi_end"),
			'weixiu_company_id'  => _Request::get("weixiu_company_id"),
			'weixiu_warehouse_id'  => _Request::get("weixiu_warehouse_id"),
			'hbh'  => _Request::get("hbh"),
			'xilie_val'  => _Request::get("xilie_val"),
		);
		 $model = new WarehouseGoodsModel(21);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
                if ($args['xilie_name'] !=''){  
                 $sql = "SELECT  id from front.app_style_xilie  WHERE `name` = '".$args['xilie_name']."' ";
		 $id = $model->db()->getOne($sql);
                 $sql = "SELECT style_sn from front.base_style_info WHERE   INSTR (xilie, ',".$id.",') > 0";
                 $style_sn = $model->db()->getAll($sql);
                 $xilie_val = array();
                 foreach ($style_sn as $k => $v){
                     $xilie_val[] ="'". $v['style_sn']."'";
                 }
                 
                 $xilie_val = join(',', $xilie_val);
                  
                 $args['xilie_val'] = $xilie_val;
                }
		$where = array(
			'put_in_type'	=> $args['put_in_type'],
			'weixiu_status'	=> $args['weixiu_status'],
			'is_on_sale'	=> $args['is_on_sale'],
			'caizhi'	=> $args['caizhi'],
		    'jinse'	=> $args['jinse'],
			'goods_id'		=> $args['goods_id'],
			'style_sn'		=> $args['style_sn'],
			'cat_type'		=> $args['cat_type'],
			'cat_type1'		=> $args['cat_type1'],
			'company_id'	=> $args['company_id'],
			'warehouse_id'	=> $args['warehouse_id'],
			'company_ids_string'	=> $args['company_ids_string'],
			'warehouse_ids_string'	=> $args['warehouse_ids_string'],
			'zhengshuhao'   => $args['zhengshuhao'],
			'order_goods_ids' => $args['order_goods_ids'],
			'shoucun'       => $args['shoucun'],
			'kucunstart'    => $args['kucunstart'],
			'kucunend'      => $args['kucunend'],
			'processor'     => $args['processor'],
			'buchan'        => $args['buchan'],
            'pinpai'         => $args['pinpai'],
			'mohao'         => $args['mohao'],
			'zhushi'        => $args['zhushi'],
			'zhengshuleibie'=> $args['zhengshuleibie'],
            'luozuanzhengshu' =>$args['luozuanzhengshu'],
            'xilie_name' =>$args['xilie_val'],
			'jinzhong_begin'    => $args['jinzhong_begin'],
			'jinzhong_end'      => $args['jinzhong_end'],
			'zs_color'      => $args['zs_color'],
			'zs_clarity'    => $args['zs_clarity'],
			'jintuo_type'   => $args['jintuo_type'],
			'jinshi_type'   => $args['jinshi_type'],
			'jiejia'        => $args['jiejia'],
			'guiwei'        => $args['guiwei'],
			'chanpinxian'   => $args['chanpinxian'],
			'chanpinxian1'   => $args['chanpinxian1'],
			'zhushi_begin'  => $args['zhushi_begin'],
			'zhushi_end'    => $args['zhushi_end'],
			'weixiu_company_id'    => $args['weixiu_company_id'],
			'weixiu_warehouse_id'    => $args['weixiu_warehouse_id'],
			'hbh'    => $args['hbh']
		);
				//var_dump($where);die;
		$model = new WarehouseGoodsModel(21);
		//导出功能
		//var_dump($where);die;
		if($args['down_info']=='down_info'){
			//ini_set('memory_limit',"2256M");
			$data = $model->pageList($where,$page,9000000,false);
			/* $data = $this->search_tsyd($data);
			$print = [];
			foreach ($data['data'] as $key => $value) {
				$print[] = $value;
				if(isset($value['tsyd']) && !empty($value['tsyd'])){
					$print[] = $value['tsyd'];
				}
			}
			$data['data'] = $print; */
		//	 echo '<pre>';print_r($data['data']);echo '</pre>';die;
			$this->download($data);
			exit;
		}
		if($args['hbh']==1){
			$data = $model->hbhpageList($where,$page,90000,false);
			//print_r($data);die;
			foreach($data['data'] as $k=>$v){
				if($v['bill_no'] !=''){
					if(isset($newdata[$v['goods_id']])){
						if($v['check_time'] > $newdata[$v['goods_id']]['check_time']){
							$newdata[$v['goods_id']]=$v;
						}
					}else{
						$newdata[$v['goods_id']]=$v;
					}
				}else{
					$newdata[$v['goods_id']]=$v;
				}
			}
			$this->hbhdownload($newdata);
			exit;
		}
		$data = $model->pageList($where,$page,40,false);
		//统计订单数量 以及订单名义价总金额
		$tongji = $data['tongji'];
		//unset($data['tongji']);
		$pageData = $data;

		$data = $this->search_tsyd($data);		//寻找天生一对

	    $pageData['jsFuncs'] = 'warehouse_goods_report_search_page';
			
		$this->render('warehouse_goods_report_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'tongji'=>$tongji,
		    //'chengben_right' => $this->checkChengbenViewRights($model),
			'dd' => new DictView(new DictModel(1))
		));
	}
	/**
	 * 寻找天生一对
	 */
	private function search_tsyd($data){
		/**
		 * 判断是否是货品镶嵌了天生一对的钻石，货号后面显示天生一对钻石的款号。
		 * 1、检测 pinpai 字段不为空
		 * 2、根据pinpai = '证书号', 找到钻，检测钻 zhengshuleibie 是否为 “HRD-D”
		 * 3、是的话，再根据这个钻去找另外一个货
			*/
		$model = new WarehouseGoodsModel(21);
		foreach($data['data'] AS $k => $val){
			$sql="select g.*,d.kuan_sn from warehouse_goods g inner join rel_hrd d on g.goods_id=d.tuo_b where d.tuo_a='{$val['goods_id']}' limit 0,1";
	
			$res = $model->db()->getAll($sql);
			foreach($res as $key=>$val2){
				$data['data'][$k]['tsyd']=$val2;
				$data['data'][$k]['tsyd_goods_sn']= $val2['kuan_sn'];
			}
		}
		 
		foreach($data['data'] as $k => $val){
			$sql="select xilie from front.base_style_info where style_sn='{$val['goods_sn']}' limit 0,1";
	
			$xilie = $model->db()->getOne($sql);
			$sql = "select name from front.app_style_xilie where  id in(0{$xilie}0)";
			 
			$xilie_name = $model->db()->getAll($sql);
	
			$name = '';
			if(!empty($xilie_name))
			{
				foreach ($xilie_name as $kk => $v){
					$name .= $v['name'].' ';
				}
			}
	
			$data['data'][$k]['xilie_name'] = $name;
	
		}
	
		return $data;
	}
	//导出
	public function download($data) {
		$dd = new DictModel ( 1 );
		if ($data ['data']) {
			$down = $data ['data'];
			$status = array ();
			$list = $dd->getEnumArray ( "warehouse.goods_status" );
			foreach ( $list as $k => $v ) {
				$status ['warehouse.goods_status'] [$v ['name']] = $v ['label'];
			}
			$list = $dd->getEnumArray ( "warehouse_goods.tuo_type" );
			foreach ( $list as $k => $v ) {
				$status ['warehouse_goods.tuo_type'] [$v ['name']] = $v ['label'];
			}
			$list = $dd->getEnumArray ( "warehouse.put_in_type" );
			foreach ( $list as $k => $v ) {
				$status ['warehouse.put_in_type'] [$v ['name']] = $v ['label'];
			}
			$list = $dd->getEnumArray ( "warehouse.weixiu_status" );
			foreach ( $list as $k => $v ) {
				$status ['warehouse.weixiu_status'] [$v ['name']] = $v ['label'];
			}
			
			// $xls_content = "产品线,新产品线,款式分类,新款式分类,货号,供应商,入库方式,状态,所在仓库,款号,模号,名称,名义价,原始采购价,最新采购价,材质,金重,手寸,金托类型,主石,主石粒数,主石形状,主石大小,主石颜色,主石净度,主石切工,抛光,对称,荧光,主石规格,副石1,副石1粒数,副石1重,副石2,副石2粒数,副石2重,证书号,证书类型,金饰类型,数量,是否结价,是否绑定,所在公司,戒托实际镶口,维修状态,维修公司,维修仓库,金耗,最后销售时间,本库库龄,库龄,国际报价,折扣,柜位\r\n";
			
			$xls_content = "产品线,新产品线,款式分类,新款式分类,货号,供应商,入库方式,状态,所在仓库,款号,模号,名称,名义价,原始采购价,最新采购价,材质,金重,手寸,金托类型,主石,主石粒数,主石形状,主石大小,主石颜色,主石净度,主石切工,抛光,对称,荧光,主石规格,副石1,副石1粒数,副石1重,副石2,副石2粒数,副石2重,证书号,证书类型,金饰类型,数量,是否结价,是否绑定,所在公司,戒托实际镶口,维修状态,维修公司,维修仓库,金耗,最后销售时间,本库库龄,库龄,国际报价,折扣,品牌,裸钻证书类型,供应商货品条码,系列及款式归属,柜位\r\n";
			
			$jiejia = array (
					0 => '未结价',
					1 => '结价' 
			);
			foreach ( $down as $key => $val ) {
				// $val['jiejia']=isset($jiejia[$val['jiejia']])?$jiejia[$val['jiejia']]:'';
				$val ['order_goods_id'] = $val ['order_goods_id'] ? '绑定' : '未绑定';
				// $val['tuo_type']=$dd->getEnum('warehouse_goods.tuo_type',$val['tuo_type']);
				$val ['mingyichengben'] = Auth::canRead ( "warehouse_goods.nominal_price", 2, $val ['warehouse_id'] ) ? $val ['mingyichengben'] : '';
				$val ['chengbenjia'] = Auth::canRead ( "warehouse_goods.purchase_price", 2, $val ['warehouse_id'] ) ? $val ['chengbenjia'] : '';
				$xls_content .= $val ['product_type'] . ",";
				$xls_content .= $val ['product_type1'] . ",";
				$xls_content .= $val ['cat_type'] . ",";
				$xls_content .= $val ['cat_type1'] . ",";
				$xls_content .= $val ['goods_id'] . ",";
				$xls_content .= $val ['prc_name'] . ",";
				$xls_content .= isset ( $status ['warehouse.put_in_type'] [$val ['put_in_type']] ) ? $status ['warehouse.put_in_type'] [$val ['put_in_type']] . "," : $val ['put_in_type'] . ",";
				$xls_content .= isset ( $status ['warehouse.goods_status'] [$val ['is_on_sale']] ) ? $status ['warehouse.goods_status'] [$val ['is_on_sale']] . "," : $val ['is_on_sale'] . ",";
				$xls_content .= $val ['warehouse'] . ",";
				$xls_content .= $val ['goods_sn'] . ",";
				$xls_content .= $val ['mo_sn'] . ",";
				$xls_content .= $val ['goods_name'] . ",";
				$xls_content .= $val ['mingyichengben'] . ",";
				$xls_content .= $val ['yuanshichengbenjia'] . ",";
				$xls_content .= $val ['chengbenjia'] . ",";
				$xls_content .= $val ['caizhi'] . ",";
				$xls_content .= $val ['jinzhong'] . ",";
				$xls_content .= $val ['shoucun'] . ",";
				$xls_content .= isset ( $status ['warehouse_goods.tuo_type'] [$val ['tuo_type']] ) ? $status ['warehouse_goods.tuo_type'] [$val ['tuo_type']] . "," : $val ['tuo_type'] . ",";
				$xls_content .= $val ['zhushi'] . ",";
				$xls_content .= $val ['zhushilishu'] . ",";
				$xls_content .= $val ['zhushixingzhuang'] . ",";
				$xls_content .= $val ['zuanshidaxiao'] . ",";
				$xls_content .= $val ['zhushiyanse'] . ",";
				$xls_content .= $val ['zhushijingdu'] . ",";
				$xls_content .= $val ['zhushiqiegong'] . ",";
				$xls_content .= $val ['paoguang'] . ",";
				$xls_content .= $val ['duichen'] . ",";
				$xls_content .= $val ['yingguang'] . ",";
				$xls_content .= $val ['zhushiguige'] . ",";
				$xls_content .= $val ['fushi'] . ",";
				$xls_content .= $val ['fushilishu'] . ",";
				$xls_content .= $val ['fushizhong'] . ",";
				$xls_content .= $val ['shi2'] . ",";
				$xls_content .= $val ['shi2lishu'] . ",";
				$xls_content .= $val ['shi2zhong'] . ",";
				$xls_content .= $val ['zhengshuhao'] . ",";
				$xls_content .= $val ['zhengshuleibie'] . ",";
				$xls_content .= $val ['ziyin'] . ",";
				$xls_content .= $val ['num'] . ",";
				if ($val ['jiejia'] == 0)
					$xls_content .= "未结价" . ",";
				if ($val ['jiejia'] == 1)
					$xls_content .= "已结价" . ",";
				
				if ($val ['order_goods_id'] == 0 || $val ['order_goods_id'] == '') {
					$xls_content .= "未绑定" . ",";
				} else {
					$xls_content .= "绑定" . ",";
				}
				$xls_content .= $val ['company'] . ",";
				$xls_content .= $val ['jietuoxiangkou'] . ",";
				if ($weixiu_status = isset ( $status ['warehouse.weixiu_status'] [$val ['weixiu_status']] ) ? $status ['warehouse.weixiu_status'] [$val ['weixiu_status']] : $val ['weixiu_status']) {
					$xls_content .= $weixiu_status . ",";
				} else {
					$xls_content .= '--' . ",";
				}
				$xls_content .= $val ['weixiu_company_name'] . ",";
				$xls_content .= $val ['weixiu_warehouse_name'] . ",";
				$xls_content .= $val ['jinhao'] . ",";
				$xls_content .= $val ['account_time'] . ",";
				$kuling = ceil ( (time () - strtotime ( $val ['addtime'] )) / (3600 * 24) ) . '天';
				if (empty ( $val ['change_time'] )) {
					$thiskuling = '0';
				} else {
					$thiskuling = ceil ( (time () - strtotime ( $val ['change_time'] )) / (3600 * 24) ) . '天';
				}
				$xls_content .= $thiskuling . ","; // 本库库龄
				$xls_content .= $kuling . ","; // 库龄
				$xls_content .= $val ['guojibaojia'] . ",";
				$xls_content .= $val ['zuanshizhekou'] . ",";
				$xls_content .= $val ['pinpai'] . ",";
				$xls_content .= $val ['luozuanzhengshu'] . ",";
				$xls_content .= $val ['supplier_code'] . ",";
				$val ['xilie_name']=isset($val ['xilie_name'])?$val ['xilie_name']:' ';
				$xls_content .= $val ['xilie_name'] . ",";
				
				$xls_content .= $val ['box_sn'] . "\n";
			}
		} else {
			$xls_content = '没有数据！';
		}
		/*
		 * header("Content-type: text/html; charset=gbk"); header("Content-type:aplication/vnd.ms-excel"); header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv"); echo iconv("utf-8", "gbk", $xls_content);
		 */
		header ( "Content-type:text/csv;charset=gbk" );
		header ( "Content-Disposition:filename=" . iconv ( "utf-8", "GB18030", "库存详细报表" . date ( "Y-m-d" ) ) . ".csv" );
		header ( 'Cache-Control:must-revalidate,post-check=0,pre-check=0' );
		header ( 'Expires:0' );
		header ( 'Pragma:public' );
		echo iconv ( "utf-8", "GB18030", $xls_content );
		
		exit ();
	}
}
?>