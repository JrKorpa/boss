<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 11:04:15
 *   @update	:
 *  仓储管理-仓储单据-单据查询
 *  -------------------------------------------------
 */
class WarehouseBillHZController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array('search','printcode','printHunbohui');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{

		$args = array(
				'mod'				=> _Request::get("mod"),
				'con'				=> substr(__CLASS__, 0, -10),
				'act'				=> __FUNCTION__,
				'bill_type'			=> _Request::get("bill_type"),		
				'bill_status'		=> _Request::get("bill_status"),			
				'create_user'		=> _Request::get("create_user"),
				'block'		=> _Request::get("block"),
								
		);	
		//从单据统计进来	
		if($args['block']==1){
			$where = array(
					'bill_no'			=> '',
					'goods_sn'			=> '',
					'send_goods_sn'		=> '',
					'bill_type'			=> $args['bill_type'],
					'order_sn'			=> '',
					'bill_status'		=> $args['bill_status'],
					'from_company_id'	=> '',
					'to_company_id'		=> '',
					'to_warehouse_id'	=> '',
					'goods_id'			=> '',
					'processors'		=> '',
					'create_user'		=> $args['create_user'],
					'check_time_start'  => '',
					'check_time_end'    => '',
					'time_start'		=> '',
					'time_end'			=> '',
					'bill_note'			=> '',
					'account_type'      => '',
					'mohao'             => '',
					'put_in_type'       => ''			
			);
			$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
			$model = new WarehouseBillModel(21);
			$data = $model->pageList($where,$page,10,false);
			$pageData = $data;
			
			$pageData['filter'] = $args;
			$pageData['jsFuncs'] = 'warehouse_bill_search_page';
			
	
			$view = new WarehouseBillView($model);
	                //供应商
			$model_p = new ApiProModel();
			$pro_list = $model_p->GetSupplierList(array('status'=>1));
			//print_r($data);exit;
			$this->render('warehouse_bill_search_form.html',array(
				'bar'=>Auth::getBar(),
	            'view'=>$view,
	            'pro_list' => $pro_list,
				'pa'=>Util::page($pageData),
				'page_list'=>$data,
				'is_goods_id'=>$where['goods_id'],
				'is_goods_sn'=>$where['goods_sn'],				
				'dd'=> new DictView(new DictModel(1)),
				'args' => $args	
					
			));
		
		}else{
			$view = new WarehouseBillView(new WarehouseBillModel(21));
			//供应商
			$model_p = new ApiProModel();
			$pro_list = $model_p->GetSupplierList(array('status'=>1));
			$this->render('warehouse_bill_search_form.html',array(
					'bar'=>Auth::getBar(),
					'view'=>$view,
					'pro_list' => $pro_list
			));
		}
	
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		
		$args = array(
			'mod'				=> _Request::get("mod"),
			'con'				=> substr(__CLASS__, 0, -10),
			'act'				=> __FUNCTION__,
			'bill_no'			=> trim(_Request::get("bill_no")),
			'goods_sn'			=> trim(_Request::get("goods_sn")),
			'send_goods_sn'	    => trim(_Request::get("send_goods_sn")),
			'bill_type'			=> _Request::get("bill_type"),
			'order_sn'			=> trim(_Request::get("order_sn")),
			'bill_status'		=> _Request::get("bill_status"),
			'from_company_id'	=> _Request::getInt("from_company_id"),
			'to_company_id'		=> _Request::getInt("to_company_id"),
			'to_warehouse_id'	=> _Request::getInt("to_warehouse_id"),
			'down_info'			=> _Request::get('down_info')?_Request::get('down_info'):'',
			'goods_id'			=> _Request::getString("goods_id"),
			'processors'		=> _Request::get("processors"),
			'create_user'		=> _Request::get("create_user"),
			'check_time_start'  => _Request::get("check_time_start"),
			'check_time_end'    => _Request::get("check_time_end"),
			'time_start'		=> _Request::get("time_start"),
			'time_end'			=> _Request::get("time_end"),
			'bill_note'			=> _Request::get("bill_note"),
                        'account_type'      => _Request::get("account_type"),
                        'mohao'             => _Request::get("mohao"),
                        'put_in_type'       => _Request::get('put_in_type')
		);
		$where = array(
			'bill_no'			=> $args['bill_no'],
			'goods_sn'			=> $args['goods_sn'],
			'send_goods_sn'		=> $args['send_goods_sn'],
			'bill_type'			=> $args['bill_type'],
			'order_sn'			=> $args['order_sn'],
			'bill_status'		=> $args['bill_status'],
			'from_company_id'	=> $args['from_company_id'],
			'to_company_id'		=> $args['to_company_id'],
			'to_warehouse_id'	=> $args['to_warehouse_id'],
			'goods_id'			=> $args['goods_id'],
			'processors'		=> $args['processors'],
			'create_user'		=> $args['create_user'],
			'check_time_start'  => $args['check_time_start'],
			'check_time_end'    => $args['check_time_end'],
			'time_start'		=> $args['time_start'],
			'time_end'			=> $args['time_end'],
			'bill_note'			=> $args['bill_note'],
                        'account_type'      => $args["account_type"],
                        'mohao'             => $args["mohao"],
                        'put_in_type'       => $args['put_in_type'],
				'laiyuan'=>'hz'
		);

		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		//$model = new WarehouseBillModel(21);
		$model = new WarehouseBillModel(55);//只读数据库
		//var_dump($where['goods_sn']);exit;
		if(!empty($where['goods_id']) ||!empty($where['goods_sn'])){
			//导出功能
			if($args['down_info']=='down_info'){
				$data = $model->goodsBillList($where,$page,90000000,false);
				$this->download($data,$where['goods_id']);
				exit;
			}
			$data = $model->goodsBillList($where,$page,10,false,1);
		}else{
			//echo 888;exit;
			//导出功能
			if($args['down_info']=='down_info'){
				$data = $model->pageList($where,$page,90000000,false);
				$this->download($data,$where['goods_id']);
				exit;
			}
			$data = $model->pageList($where,$page,10,false,1);
		}
		$total_num =$data['total_num']?$data['total_num']:0;
		$total_price =$data['total_price']?$data['total_price']:0;
		$total_shijia =$data['total_shijia']?$data['total_shijia']:0;
		$pageData = $data;
		//print_r($data);exit;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_bill_search_page';
		$this->render('warehouse_bill_search_list.html',array(
			'pa'=>Util::page($pageData),'page_list'=>$data,'is_goods_id'=>$where['goods_id'],'is_goods_sn'=>$where['goods_sn'],
			'view'=>new WarehouseBillView(new WarehouseBillModel(21)),
			'dd'=> new DictView(new DictModel(1)),
			'total_num'=>$total_num,
			'total_price'=>$total_price,
			'total_shijia'=>$total_shijia
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_bill_info.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel(1))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}
   

	public function delBill($params)
	{
		$result = array('success' => 0,'error' => '');
		$model = new WarehouseBillModel(22);
		$res = $model->delBill($params['id']);
		if($res)
		{
			$result['success'] = 1;
		}else{
			$result['error'] = "失败了";
		}
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_bill_info.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel($id,1))
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$c = new TestController();
		$c->index();exit;
		$this->render('warehouse_bill_show.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel($id,1))
		));
	}


	public function checkList(){

		$result = array('success' => 0,'error' =>'');
		$user_name = $_SESSION['userName'];
		$bill_id = _Post::getInt('bill_id');
		$model = new WarehouseBillModel($bill_id,21);

		if($model->getValue('bill_status') != 1)
		{
			$result['error'] = '单据不是<span style="color: #ff0000">&nbsp;已保存&nbsp;</span>状态不允许编辑';
			Util::jsonExit($result);
		}
		$bill_type = $model->getValue('bill_type');
		if($model->getValue('create_user') != $user_name && $bill_type != 'L' && $bill_type != 'T')
		{
			//$result['error'] = '单据非本人不能编辑';
			//Util::jsonExit($result);
		}
		/*if($model->getValue('bill_type') == 'W')
		{
			$result['error'] = '盘点单不允许编辑';
			Util::jsonExit($result);
		}*/
		if($bill_type == 'S')
		{
			$result['error'] = '销售单不允许编辑';
			Util::jsonExit($result);
		}
		if($bill_type == 'D')
		{
			//$result['error'] = '销售退货单不允许编辑';
			//Util::jsonExit($result);
		}
		$result['success'] = 1;
		Util::jsonExit($result);
	}

	/***********************************************************************************************
	fun:getOrderSnByOrderId
	description:通过订单号获取订单id
	************************************************************************************************/
	public function getOrderSnByOrderId ($params)
	{
		$result = array('success' => 0,'error' => '');
		$orderSn = $params;
		$res = ApiModel::sales_api('GetOrderInfoBySn',array('order_sn'),array($orderSn));
		//var_dump($res);exit;
		if ($res['error'])
		{
			$result['error'] = "订单号不存在";
			$result['id'] = '';
		}
		else
		{
			$result['success'] = 1;
			$result['id'] = $res['return_msg']['id'];
		}
		return $result;

	}

	//导出
	public function download($data,$goods_id) {

		$dd =new DictModel(1);
		$salemodel = new SalesModel(51);
		$view = new WarehouseBillModel(21);


		if($goods_id){
			if ($data['data']) {
				$down = $data['data'];
				$xls_content = "单据编号,单据类型,状态,货品数量,订单号,销售渠道,出库公司,实际出库公司,入库公司,入库仓,供应商,原始成本价,	销售价,送货单号,制单人,制单时间,审核人,审核时间,备注\r\n";
				foreach ($down as $key => $val) {
					$val['from_company_name']?$val['from_company_name']:'无';
					$val['to_company_name']?$val['to_company_name']:'无';
					$val['to_warehouse_name']?$val['to_warehouse_name']:'无';
					
					$xls_content .= $val['bill_no'] . ",";
					$xls_content .= $view->getBillType($val['bill_type']) . ",";
					$xls_content .= $dd->getEnum('warehouse_in_status',$val['bill_status']) . ",";
					$xls_content .= $val['num'] . ",";
					$xls_content .= $val['order_sn'] . ",";
					$xls_content .= $salemodel->getSalesChannelByOrderSn($val['order_sn']) . ",";		//销售渠道
					$xls_content .= $val['from_company_name'] . ",";
					$xls_content .= $val['company_from'] . ",";
					$xls_content .= $val['to_company_name'] . ",";
					$xls_content .= $val['to_warehouse_name']. ",";
					$xls_content .= $val['pro_name'] . ",";
					$xls_content .= $val['total_chengben'] . ",";
					$xls_content .= $val['shijia'] . ",";
					$xls_content .= $val['send_goods_sn'] . ",";
					$xls_content .= $val['create_user'] . ",";
					$xls_content .= $val['create_time'] . ",";
					$xls_content .= $val['check_user'] . ",";
					$xls_content .= $val['check_time'] . ",";
					$xls_content .= $val['bill_note'] . "\n";


				}
			} else {
				$xls_content = '没有数据！';
			}

		}else{

			if ($data['data']) {
				$down = $data['data'];
				$xls_content = "单据编号,单据类型,状态,货品数量,订单号,销售渠道,出库公司,入库公司,入库仓,供应商,原始成本价,	销售价,送货单号,制单人,制单时间,审核人,审核时间,备注\r\n";
				foreach ($down as $key => $val) {
					empty($val['from_company_name'])?$val['from_company_name']:'无';
					empty($val['to_company_name'])?$val['to_company_name']:'无';
					empty($val['to_warehouse_name'])?$val['to_warehouse_name']:'无';
					$xls_content .= $val['bill_no']. ",";
					$xls_content .= $view->getBillType($val['bill_type']) . ",";
					$xls_content .= $dd->getEnum('warehouse_in_status',$val['bill_status']) . ",";
					$xls_content .= $val['goods_num']. ",";
					$xls_content .= $val['order_sn'] . ",";
					$xls_content .= $salemodel->getSalesChannelByOrderSn($val['order_sn']) . ",";		//销售渠道
					$xls_content .= $val['from_company_name'] . ",";
					$xls_content .= $val['to_company_name'] . ",";
					$xls_content .= $val['to_warehouse_name']. ",";
					$xls_content .= $val['pro_name'] . ",";
					$xls_content .= $val['total_chengben'] . ",";
					$xls_content .= $val['shijia'] . ",";
					$xls_content .= $val['send_goods_sn'] . ",";
					$xls_content .= $val['create_user'] . ",";
					$xls_content .= $val['create_time'] . ",";
					$xls_content .= $val['check_user'] . ",";
					$xls_content .= $val['check_time'] . ",";
					$xls_content .= $val['bill_note'] . "\n";

				}
			} else {
				$xls_content = '没有数据！';
			}

		}

		header("Content-type: text/html; charset=gbk");
		header("Content-type:aplication/vnd.ms-excel");
		header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
		echo iconv("utf-8", "gbk", $xls_content);

	}

	/**
	 * 批量复制货号
	 */
	public function batchCopyGoods_id(){
		$bill_id = _Post::getInt('bill_id');
		$sql = "SELECT `id`,`goods_id` FROM `warehouse_bill_goods` WHERE `bill_id` = '".$bill_id."'";
		$model = new WarehouseBillModel(21);
		$data = $model->db()->getAll($sql);
		if(empty($data) || !$data){
			echo 0;
		}else{
			$data = array_column($data,'goods_id','id');
			$str = '';
			foreach ($data as $g) {
				$str .= $g."\r\n";
			}
			$str = rtrim($str,"\r\n");
			echo $str;
		}
	}
        //打印婚博会明晰
        public function printHunbohui(){
            //获取bill_id单据id
            $id = _Request::get('id');
            //数字词典
            $model = new WarehouseBillModel($id,21);

			$data  = $model->getDataObject();
            $newmodel = new WarehouseBillInfoMModel(21);
          //  $billinfo = $newmodel->getBillGoogsList($id,'row');

           // foreach($billinfo as $key=>$val){
         //           $data[$key]=$val;
          //  }

            //货品详情
            $goods_info = $model->getDetail($id);
            //获取加工商支付信息
            $amount=0;
            $BillPay = $model->getBillPay($id);
            foreach($BillPay as $val){
                    $amount +=$val['amount'];
            }
            //计算销售价总计 成本价总计 商品数量
            $fushizhong=0;
            $jinzhong=0;
            $zuanshidaxiao=0;
            //统计 副石重 金重
            foreach($goods_info as $key=>$val){
                    $goods_id[] = substr($val['goods_id'], -1,1);
                    //获取图片 拼接进数组
                    $gallerymodel = new ApiStyleModel();
                    $gallery_data = $gallerymodel->getProductGallery($val['goods_sn'],1);
                    if(isset($gallery_data['thumb_img'])){
                            $goods_info[$key]['goods_img']=$gallery_data['thumb_img'];
                    }else{
                            $goods_info[$key]['goods_img']='';
                    }

                    $fushizhong +=$val['fushizhong'];
                    $jinzhong +=$val['jinzhong'];
                    $zuanshidaxiao +=$val['zuanshidaxiao'];
                    $data['cat_type'] = $val['cat_type'];
            }
            array_multisort($goods_id, SORT_ASC, $goods_info);

            $this->render('print_hunbohui_detail.html', array(
				'data' => $data,
				'goods_info' => $goods_info,
				'fushizhong' => $fushizhong,
				'jinzhong' => $jinzhong,
				'zuanshidaxiao' => $zuanshidaxiao,
				'BillPay' => $BillPay,
				'amount' => $amount
		));
        }

		//打印条码
	public function printcode() {
		$bill_id =_Request::get('bill_id');
        //$policy_type =_Request::get('policy_type');
       
		$dd =new DictModel(1);
		$newmodel = new WarehouseBillModel(21);
		$codes =$newmodel->getGoodsIdinfoByBillId($bill_id);
        
		if ($codes) {
			$down = $codes;
			$xls_content = "货号,款号,入库方式,基因码,手寸,长度,主石粒数,主石重,副石粒数,副石重,加工商编号,总重,净度,颜色,证书号,国际证书,主石切工,标签备注,主石,副石,主成色,饰品分类,款式分类,名称,石3副石,石3粒数,石3重,石4副石,石4粒数,石4重,石5副石,石5粒数,石5重,主成色重,副成色,副成色重,买入工费,计价工费,加价率,最新零售价,模号,品牌,证书数量,配件数量,时尚款,系列,属性,类别,成本价,入库日期,加价率代码,主石粒重,副石粒重,标签手寸,字印,货币符号零售价,新成本价,新零售价,一口价,标价,定制价,A,B,C,D,E,F,G,H,I,HB_G,HB_H\r\n";
			$model = new WarehouseGoodsModel(21);
			foreach ($down as $key => $val) {

				$val['goods_id']= trim($val['goods_id']);
				if ($val['goods_id'] == ""){
					break;
				}
				$line =$model->getGoodsByGoods_id($val['goods_id']);
				$ageline =$model->getGoodsAgeByGoods_id($val['goods_id']);
                                $xiangkou = $line['zuanshidaxiao'];
                               
                                $baoxianfei = $newmodel->GetBaoxianFei($xiangkou);

                $xilie = [];
                $base_style = 8; //基本款
                if($line['goods_sn']){
                    $style_sql = "SELECT xilie FROM `base_style_info` WHERE style_sn = '{$line['goods_sn']}' AND check_status = 3";
                    $xilie = explode(',',DB::cn(12)->db()->query($style_sql)->fetchColumn());
                }
              
				$zhuchengse_list = array(
						'18K白金'=>'18K金',
						'18K玫瑰金'=>'18K金',
						'18K黄金'=>'18K金',
						'18K彩金'=>'18K金',
						//add
						'18K白'=>'18K金',
						'18K黄'=>'18K金',
						'18K黄白'=>'18K金',
						'18K玫瑰黄'=>'18K金',
						'18K玫瑰白'=>'18K金',
						
						'PT950'=>'铂Pt950',
						'PT900'=>'铂Pt900',
						'PT990'=>'铂Pt990',
						'9K白金'=>'9K金',
						'9K玫瑰金'=>'9K金',
						'9K黄金'=>'9K金',
						'9K彩金'=>'9K金',
						//add
						'9K白'=>'9K金',
						'9K黄'=>'9K金',
						'9K黄白'=>'9K金',
						'9K玫瑰黄'=>'9K金',
						'9K玫瑰白'=>'9K金',

						'10K白金'=>'10K金',
						'10K玫瑰金'=>'10K金',
						'10K黄金'=>'10K金',
						'10K彩金'=>'10K金',
						//add
						'10K白'=>'10K金',
						'10K黄'=>'10K金',
						'10K黄白'=>'10K金',
						'10K玫瑰黄'=>'10K金',
						'10K玫瑰白'=>'10K金',
						
						'14K金'=>'14K金',
						'14K白金'=>'14K金',
						'14K玫瑰金'=>'14K金',
						'14K黄金'=>'14K金',
						'14K彩金'=>'14K金',
						//add
						'14K白'=>'14K金',
						'14K黄'=>'14K金',
						'14K黄白'=>'14K金',
						'14K玫瑰黄'=>'14K金',
						'14K玫瑰白'=>'14K金',
						
						'Pd950'=>'钯Pd950',
						'S925'=>'银925',
						'足金'=>'足金',
						'千足金'=>'千足金',
						'千足银'=>'千足银',
						'无'=>'',
				);
                                $xiangqian_product_type = array('钻石','珍珠','翡翠','宝石','钻石饰品','珍珠饰品','翡翠饰品','宝石饰品' );//镶嵌类的产品线
				$caizhi=$line['caizhi'];
                //if($policy_type=='default')
               // {
                //    $data=$newmodel->getPriceByGoodsid($val['goods_id']);
                //    $jiajialv=$data['bj'];
                //    $jiajianum=$data['bst'];
               // }
              //  else if($policy_type=='bill_no')
              //  {
               //     $data=$newmodel->getPriceByGoodsid($val['goods_id']);
              //      $jiajialv=$data['aj'];
               //     $jiajianum=$data['ast'];
               // }
               $jiajialv=1;
               $jiajianum=0;
			     
				if($line['xianzaixiaoshou'] != 'err_AB' && $line['xianzaixiaoshou'] != 'err_WM' ){

					// $price = round($line['xianzaixiaoshou']*trim($jiajialv) + trim($jiajianum));		//取这个的话 打标C都为0
                                        //如果商品产品线镶嵌类，金托类型是空托：标签价=（名义成本+保险费）*加价率+系数
                                        if (in_array($line['product_type'], $xiangqian_product_type) && ($line['tuo_type'] == 3 || $line['tuo_type'] == 2)){
                                            
                                           $price = round(($line['mingyichengben']+$baoxianfei)*trim($jiajialv) + trim($jiajianum));
                                          
                                        }else{
                                           
                                            $price = round($line['mingyichengben']*trim($jiajialv) + trim($jiajianum));
                                        }
					if ($zhuchengse_list[$caizhi] == '18K金'){
						$other_price = $line['jinzhong'] * 400 + $price + 500 ;
						$other_price_string = "PT定制价￥" . ceil($other_price);
					}elseif ($zhuchengse_list[$caizhi] == '铂Pt950'){
						$other_price = $price - $line['jinzhong'] * 250;
						$other_price_string = "18K金定制价￥" . ceil($other_price);
					}else{
						$other_price_string = "";
					}
				}else{
					$price = '待核实';
				}

				$line['caizhi'] = $zhuchengse_list[$caizhi];
				$line['goods_name'] = str_replace($caizhi, $line['caizhi'], $line['goods_name']);
				$line['goods_name'] = str_replace(array('女戒','情侣戒','CNC情侣戒','男戒','戒托'), array('戒指','戒指','戒指','戒指','戒指'), $line['goods_name']);
				$line['goods_name'] = str_replace(array('海水海水','淡水白珠','淡水圆珠','淡水',"大溪地", "南洋金珠"), array('海水','珍珠','珍珠','',"海水","海水珍珠"), $line['goods_name']);
				if ($line['fushizhong'] > 0 && $line['fushi'] != $line['zhushi'] ){
					//$line['goods_name'] .= '配' . $line['fushi'];
				}
				if ($line['shi2zhong'] > 0 && $line['shi2'] != $line['zhushi'] && $line['shi2'] != $line['fushi']){
					//$line['goods_name'] .= '、' . $line['shi2'];
				}


				if($line['xianzaixiaoshou'] != 'err_AB' && $line['xianzaixiaoshou'] != 'err_WM' ){
					if ($caizhi == 'PT950' || $caizhi == 'PT990' || $caizhi == 'PT900'){
						$line['yikoujia'] = round($line['xianzaixiaoshou']*1.9 + 1000);
						$line['biaojia'] = round($line['yikoujia']*1.5);
					}else{
						$line['yikoujia'] = round($line['xianzaixiaoshou']*1.9 + 500);
						$line['biaojia'] = round($line['yikoujia']*1.5);
					}
				}
                
                $put_in_type=$line['put_in_type'];
                if($put_in_type==1 || $put_in_type==2)
                {
                    $put_in_type='GM';
                }
                if($put_in_type==3 || $put_in_type==4)
                {
                    $put_in_type='DX';
                }

                //按款定价
                if($ageline){
                    if(isset($ageline['is_kuanprice']) && $ageline['is_kuanprice'] == 1)
                    {
                        $line['biaojia'] = $ageline['kuanprice'];
                        $_line = $line;
                        $_line['caizhi'] = $caizhi;
                        $dz_kuan_price = $model->getDzKuanPrice($_line);
                        if($dz_kuan_price){
                            $other_price_string = $dz_kuan_price;
                        }
                    }
                }

                
				$xls_content .= "\"".$line['goods_id'] . "\",";
				$xls_content .= "\"".$line['goods_sn'] . "\",";
                $xls_content .= "\"".$put_in_type . "\",";
				$xls_content .= '' . ",";//$line['gene_sn'] 未知
				$xls_content .= "\"".$line['shoucun'] . "\",";
				$xls_content .= "\"".$line['changdu'] . "\",";
				$xls_content .= "\"".$line['zhushilishu'] . "\",";
				$xls_content .= "\"".$line['zuanshidaxiao'] . "\",";
				$xls_content .= "\"".$line['fushilishu'] . "\",";

				$xls_content .= "\"".$line['fushizhong'] . "\",";
				$xls_content .= '' . ",";
				$xls_content .= "\"".$line['zongzhong'] . "\",";
				$xls_content .= "\"".$line['jingdu'] . "\",";
				$xls_content .= "\"".$line['yanse'] . "\",";
				$xls_content .= "\"".$line['zhengshuhao'] . "\",";
				$xls_content .= "\"".$line['guojizhengshu'] . "\",";
				$xls_content .= "\"".$line['zhushiqiegong'] . "\",";
				$xls_content .= "" . ",";

				$xls_content .= "\"".$line['zhushi'] . "\",";
				$xls_content .= "\"".$line['fushi'] . "\",";
				$xls_content .= "\"".$caizhi . "\",";
				$xls_content .= "\"".$line['product_type'] . "\",";
				$xls_content .= "\"".$line['cat_type'] . "\",";
				$xls_content .= "\"".$line['goods_name'] . "\",";
				$xls_content .= "\"".$line['shi2'] . "\",";
				$xls_content .= "\"".$line['shi2lishu'] . "\",";
				$xls_content .= "\"".$line['shi2zhong'] . "\",";
				$xls_content .= '' . ",";//石4
				$xls_content .= '' . ",";
				$xls_content .= '' . ",";
				$xls_content .= '' . ",";
				$xls_content .= '' . ",";
				$xls_content .= '' . ",";

				$xls_content .= "\"".$line['jinzhong'] . "\",";
				$xls_content .= '' . ",";
				$xls_content .= '' . ",";
				$xls_content .= "\"".$line['mairugongfei'] . "\",";
				$xls_content .= "\"".$line['jijiagongfei'] . "\",";
				$xls_content .= "\"".$jiajialv . "\",";
				$xls_content .= "\"".$line['zuixinlingshoujia'] . "\",";
				$xls_content .= "\"".$line['mo_sn'] . "\",";

				$xls_content .= "\"".$line['pinpai'] . "\",";
				$xls_content .= '' . ",";//证书数量

				$xls_content .= "\"".$line['peijianshuliang'] . "\",";//
				$xls_content .= '' . ",";				//时尚款
				$xls_content .= "\"".(in_array($base_style,$xilie)?"基":"") . "\",";			//系列
				$xls_content .= '' . ",";			//属性
				$xls_content .= '' . ",";			//类别
				$xls_content .= "\"".$line['chengbenjia'] . "\",";
				$xls_content .= "\"".$line['addtime'] . "\",";
				$xls_content .= '' . ","; 			//加价率代码
				$xls_content .= '' . ",";			//主石粒重
				$xls_content .= '' . ",";			//副石粒重
				$xls_content .= '' . ",";		//标签手寸

				$xls_content .= "\"".$line['ziyin'] . "\",";
				$xls_content .= "\"".$line['zuixinlingshoujia'] . "\",";
				$xls_content .= "\"".$line['mingyichengben'] . "\",";
				$xls_content .= "\"".$price . "\",";
				$xls_content .= "\"".$line['yikoujia'] . "\",";
				$xls_content .= "\"".$line['biaojia'] . "\",";

				$xls_content .= "\"".$other_price_string . "\",";
				$xls_content .= "\"".'' . "\",";		    // A
				$xls_content .= "\"".$line['goods_name'] . "\",";	// B
				//$xls_content .= $this->get_c_col_value($line, $price) . ",";// C
				$xls_content .= "\"".$this->get_c_col_value($line, $price) . "\",";// C
				$xls_content .= "\"".$this->get_d_col_value($line) . "\",";	// d
				$xls_content .= "\"".$this->get_e_col_value($line) . "\",";	// e
				$xls_content .= "\"".$this->get_f_col_value($line). "\",";	// f
				$xls_content .= "\"".$this->get_g_col_value($line) . "\",";	// f
				$xls_content .= "\"".$this->get_h_col_value($line, $other_price) . "\",";	// h
				$xls_content .= "\"".$this->get_i_col_value($line) . "\",";// i
				$xls_content .= "\"".$this->get_hb_g_col_value($line). "\",";	// hb_f
				$xls_content .= "\"".$this->get_hb_h_col_value($line, $other_price) . "\"\n";	// hb_h

			}
		} else {
			$xls_content = '没有数据！';
		}
	header("Content-type: text/html; charset=gbk");
		header("Content-type:aplication/vnd.ms-excel");
		header("Content-Disposition:filename=" . "tiaoma.csv");
		echo iconv("utf-8", "gbk", $xls_content);
		exit;
	}

	function get_c_col_value($line, $price)
	{
		$chanpinxian = $this->get_type_with_shipin_type($line["product_type"]);

		if($chanpinxian == "sujin")
		{
			if($line["caizhi"] == "千足金" || $line["cat_type"] == "银条")
			{
				// 黄金饰品及工艺品，还有黄金等投资产品的其余产品都不打价格，只打金重和工费
				return number_format($line['jinzhong'], 2, ".", "")."g";
			}
		}
                /*
		if(in_array($line["caizhi"], array("铂Pt950", "铂Pt900", "铂Pt990", "千足金")))
		{
			// 铂金,千足金 返回主成色重
			return number_format($line['jinzhong'], 2, ".", "")."g";
		}
                */
		// 其他返回价格
		return "￥".$price;
	}


	function get_d_col_value($line)
	{
		$chanpinxian = $this->get_type_with_shipin_type($line["product_type"]);

		if(empty($chanpinxian)) return;	// 没有匹配的产品标签返回空

		// 翡翠手镯显示证书号
		if($line["zhushi"] == "翡翠" && $line["cat_type"] == "手镯")
		{
			return $line["zhengshuhao"];
		}
		// 珍珠主石
		if($line["zhushi"] == "珍珠")
		{

		}
		// 千足金戒指 并且字印不是3d和精工 显示指圈号
		if($line["caizhi"] == "千足金" && ($line["cat_type"] == "女戒" || $line["cat_type"] == "男戒" ||  $line["cat_type"] == "情侣戒" ) && (strtoupper($line["ziyin"] == "3D") || $line["ziyin"] == "精工") && $line["shoucun"] > 0)
		{
			return "规格:".$line["shoucun"]."#";
		}
		// 千足金手链手镯 并且字印不是3d和精工 显示规格长度
		if($line["caizhi"] == "千足金" && ($line["cat_type"] == "手镯" || $line["cat_type"] == "手链") && (strtoupper($line["ziyin"] == "3D") || $line["ziyin"] == "精工") && $line["changdu"] != "")
		{
			return "规格:".$line["changdu"];
		}
		
		
		//updata by liulinyan 2015-08-12
		
		//如果是非成品 那么显示镶口
		if($line['tuo_type']>1)
		{
			//如果镶口为空 就显示主石大小
			if($line['jietuoxiangkou']>0 && !empty($line['jietuoxiangkou']))
			{
				return $line['jietuoxiangkou']."CT";
			}else{
				return $line["zuanshidaxiao"]."CT";
			}
			
		}
		// 有主石和主石粒数的显示出来
		//elseif(($chanpinxian == "zuanshi" || $chanpinxian == "caibao") && $line["zuanshidaxiao"]>0)
		elseif($line["zuanshidaxiao"]>0)
		{
			$line["zhushilishu"] = empty($line["zhushilishu"]) ? "1" : $line["zhushilishu"];
			return $line["zuanshidaxiao"]."CT/".$line["zhushilishu"]."P";
		}
	}


	function get_e_col_value($line)
	{
		$shilishu = $line["fushilishu"]+$line["shi2lishu"];
		$shizhong = $line["fushizhong"]+$line["shi2zhong"];
		if($shilishu > 0 && $shizhong > 0)
		{
			return $shizhong."CT/".$shilishu."P";
		}
	}
	function get_f_col_value($line)
	{
		$chanpinxian = $this->get_type_with_shipin_type($line["product_type"]);
		if(empty($chanpinxian)) return;	// 没有匹配的产品标签返回空
		if($chanpinxian == "彩宝")
		{
			// 彩宝  主成色重, 空白(挂件)
			if($line["cat_type"] == "挂件")
			{
				return "";
			}
			elseif($line["jinzhong"] > 0)
			{
				return mb_substr($line["jinzhong"],0,-1)."g";
			}
		}
		elseif($chanpinxian == "zhenzhu")
		{
			// 珍珠  主成色重, 空白(s925)
			if(in_array($line["caizhi"], array("银925")))
			{
				return "";
			}
			elseif($line["jinzhong"] > 0)
			{
				return mb_substr($line["caizhi"],0,-1)."g";
			}
		}
		elseif($chanpinxian == "zuanshi" && $line["jinzhong"] > 0)
		{
			return mb_substr($line["jinzhong"],0,-1)."g";
		}
		else
		{
			// 素金  空白
			return "";
		}
	}
	function get_g_col_value($line)
	{
		$chanpinxian = $this->get_type_with_shipin_type($line["product_type"]);
		if(empty($chanpinxian)) return;	// 没有匹配的产品标签返回空

		if($chanpinxian == "zuanshi")
		{
			if($line["tuo_type"] == 2 || $line["tuo_type"] == 3)	// 托
			{
				//return $other_price_string;
				/*
				 if($line['zhuchengse'] == '18K金')
				 {
				return "铂PT950定制价:";
				}
				elseif($line['zhuchengse'] == '铂Pt950')
				{
				return "金18K定制价:";
				}*/
				return "";
			}
			else
			{
				return $line["zhengshuhao"];
			}
		}
		elseif($chanpinxian == "zhenzhu")
		{
			return $line["zhengshuhao"];
		}
		elseif($chanpinxian == "caibao")
		{
			// 翡翠手镯 返回宽度
			if($line["zhushi"] == "翡翠" && $line["cat_type"] == "手镯")
			{
				return $line["zhushiguige"];
			}
			else
			{	// 其他返回证书号
				return $line["zhengshuhao"];
			}
		}
		elseif($chanpinxian == "sujin")
		{
			// 素金产品按 材质和款式再分
			if(in_array($line["caizhi"], array("铂Pt950", "铂Pt900", "铂Pt990")))
			{
				// 返回工费
				//return "工费:￥".number_format($line["mairugongfei"],"0", ".","");
				return;
			}
			//elseif(in_array($line["caizhi"], array("千足金")) && $line["product_type"] != "工艺品")
			elseif(in_array($line["caizhi"], array("千足金")) && $line["product_type"] != "投资黄金")
			{// 金饰品 返回工费
				return "工费:￥".number_format($line['jijiagongfei']*3+$line["jinzhong"]*4,0,".","");
				if($line["cat_type"] == "金条")
				{
					// 返回工费
					//return "工费:￥".number_format($line["mairugongfei"],"0", ".","");
					return "工费:￥".number_format($line['jijiagongfei']*3+$line["jinzhong"]*4,0,".","");
				}
				// 金条 精品返回工费, 其他返回空
				else
				{
					if($line["ziyin"] == "精工")
					{
						// 返回工费
						//return "工费:￥".number_format($line["mairugongfei"],"0", ".","");
						return "工费:￥".number_format($line['jijiagongfei']*3+$line["jinzhong"]*4,0,".","");
					}
				}
			}
			elseif(in_array($line["caizhi"], array("银925", "千足银")))
			{
				// 戒指 返回指圈号
				if(strpos($line["caizhi"], "戒") !== false && $line["shoucun"] != "")
				{
					return "规格:".$line["shoucun"]."#";
				}
				// 手链,项链 规格 长度
				elseif(($line["cat_type"] == "手链" || $line["cat_type"] == "项链") && $line["changdu"] != "")
				{
					return "规格:".$line["changdu"];
				}
				// 银条 精品-> 工费,
				//elseif($line["kuanshi_type"] == "银条" && $line["ziyin"] == "精工")
				// 除了工艺品 都显示工费
				elseif($line["product_type"] != "投资黄金")
				{
					// 返回工费
					//return "工费:￥".number_format($line["mairugongfei"],"0", ".","");
					return "工费:￥".number_format($line['jijiagongfei']*3+$line["jinzhong"]*4,0,".","");
				}
				// 吊坠,耳饰 返回空
				else
				{
					return "";
				}
			}
			elseif(in_array($line["caizhi"], array("18K金")))
			{
				// 戒指  指圈号
				if(strpos($line["cat_type"], "戒") !== false && $line["shoucun"] != "")
				{
					return "规格:".$line["shoucun"]."#";
				}
				// 手链,项链  规格长度
				elseif(($line["cat_type"] == "手链" || $line["cat_type"] == "项链") && $line["changdu"] != "")
				{
					return "规格:".$line["changdu"];
				}
				// 吊坠,耳饰,手镯  空白
				else
				{
					return "";
				}
			}
		}
	}


	function get_h_col_value($line, $other_price)
	{
		$chanpinxian = $this->get_type_with_shipin_type($line["product_type"]);
		if(empty($chanpinxian)) return;	// 没有匹配的产品标签返回空

		// 珍珠 珍珠大小
		if($chanpinxian == "zhenzhu")
		{
			return $line["zhushiguige"];
		}
		// 彩宝 长度,空白
		elseif($chanpinxian == "caibao")
		{
			// 项链,手链,手镯  返回长度
			if(in_array($line["cat_type"], array("手链", "项链", "手镯")) && $line["changdu"] != "")
			{
				return "规格:".$line["changdu"];
			}
		}
		// 素金 空白,指圈号,长度
		elseif($chanpinxian == "sujin")
		{
			// pt戒指,指圈号
			if(in_array($line["caizhi"], array("铂Pt950", "铂Pt900", "铂Pt990")) && $line["shoucun"] != "")
			{
				return "规格:".$line["shoucun"]."#";
			}
			// pt项链,手链  长度
			elseif(in_array($line["caizhi"], array("铂Pt950", "铂Pt900", "铂Pt990")) && $line["changdu"] != "")
			{
				return "规格:".$line["changdu"];
			}
			// 黄金戒指 不是精品,3D  指圈号
			elseif(in_array($line["caizhi"], array("千足金")) && $line["shoucun"] >= 0 && strtoupper($line["ziyin"] != "3D") && $line["ziyin"] != "精工" && $line["kuanshi_type"] == "戒指" && $line["shoucun"]>0)
			{
				return "规格:".$line["shoucun"]."#";
			}
			// 黄金项链 不是精品,3D  规格长度
			elseif(in_array($line["caizhi"], array("千足金")) && $line["changdu"] != "" && strtoupper($line["ziyin"] != "3D") && $line["ziyin"] != "精工" && $line["shipin_type"] == "项链")
			{
				return "规格:".$line["changdu"];
			}
		}
		// 钻饰 净度颜色,定制价,空白
		elseif($chanpinxian == "zuanshi")
		{
			// 18K,PT托  定制价
			if(in_array($line["caizhi"], array("铂Pt950", "铂Pt900", "铂Pt990", "18K金")) && $line["tuo_type"] >1)
			{
				//return "￥".number_format($other_price, 0,".","");
				return "";
			}
			// 18K,PT戒指 钻石净度+钻石颜色
			elseif(in_array($line["caizhi"], array("铂Pt950", "铂Pt900", "铂Pt990", "18K金")) && in_array($line["cat_type"], array("戒指", "女戒", "吊坠", "耳饰")))
			{
				return $line["zhushijingdu"]."/".$line["zhushiyanse"];
			}

		}
	}
	function get_i_col_value($line)
	{
		$chanpinxian = $this->get_type_with_shipin_type($line["product_type"]);
		if(empty($chanpinxian)) return;	// 没有匹配的产品标签返回空

		// 彩宝  戒指指圈号,挂件主成色重
		if($chanpinxian == "caibao")
		{
			if(trim($line["cat_type"]) == "男戒" || trim($line["cat_type"]) == "女戒"|| trim($line["cat_type"]) == "情侣戒")
			{
				return $line["shoucun"]."#";
			}
			elseif($line["cat_type"] == "挂件")
			{
				return number_format($line["jinzhong"], 2 ,".","");
			}
		}
		// 珍珠  珍珠指圈号,项链手链链长,
		elseif($chanpinxian == "zhenzhu")
		{
			if($line["cat_type"] == "男戒" || $line["cat_type"] == "女戒" || $line["cat_type"] == "情侣戒")
			{
				return $line["shoucun"]."#";
			}
			elseif(($line["cat_type"] == "项链" || $line["cat_type"] == "手链" ) && $line["changdu"] !="")
			{
				return "长:".$line["changdu"];
			}
		}
		// 钻饰  戒指戒托指圈号,手链套链规格长度
		elseif($chanpinxian == "zuanshi")
		{
			if(strpos($line["cat_type"], "戒") !== false)
			{
				return $line["shoucun"]."#";
			}
			elseif(($line["cat_type"] == "项链" || $line["cat_type"] == "套链") && $line["changdu"]!= "" )
			{
				return "规格:长".$line["changdu"];
			}
		}
		// 素金 分材质
		elseif($chanpinxian == "sujin")
		{
			// 18K 主成色重
			if(in_array($line["caizhi"], array("18K金")))
			{
				return number_format($line["jinzhong"],2,".","")."g";
			}
			// 千足金 精品,3D,空白
			elseif($line["caizhi"] == "千足金")
			{
				if($line["ziyin"] == "精工")
				{
					return "精品";
				}
				elseif(strtoupper($line["ziyin"]) == "3D")
				{
					return "3D硬金";
				}
			}
			// 银 银条精品,3D,空白 其他主成色重
			elseif($line["caizhi"] == "银925")
			{
				if($line["ziyin"] == "精工")
				{
					return "精品";
				}
				elseif(strtoupper($line["ziyin"]) == "3d")
				{
					return "3D硬金";
				}
				else
				{
					return number_format($line["jinzhong"], 2,".","")."g";
				}
			}
			// PT 空白
		}
	}
	function get_hb_g_col_value($line)
	{
		$chanpinxian = $this->get_type_with_shipin_type($line["product_type"]);
		if(empty($chanpinxian)) return;	// 没有匹配的产品标签返回空
		$jindata = array('18K金','18K白金','18K玫瑰金','18K彩金','18K玫瑰白','18K白','18K黄','18K黄白','18K玫瑰黄');
		$ptdata = array('铂Pt950','PT950');
		
		//if($chanpinxian == "zuanshi"){
			//如果金托类型是成品并且款式分类是情侣戒，需要显示定制价这几个字
			if($line["tuo_type"] == 1)
			{
				if($line["cat_type"] == "情侣戒")	
				{
					//显示定制价
					if(in_array($line['caizhi'],$jindata))
					{
						return "铂PT950定制价:";
					}elseif(in_array($line['caizhi'],$ptdata))
					{
						return "金18K定制价:";
					}else{
						return "铂PT950定制价:";	
					}
				}else{
					return $line["zhengshuhao"];
				}
			}
			if($line["tuo_type"] == 2 || $line["tuo_type"] == 3)
			{
				//return $line["zhengshuhao"];
				//显示定制价
				if(in_array($line['caizhi'],$jindata))
				{
					return "铂PT950定制价:";
				}elseif(in_array($line['caizhi'],$ptdata))
				{
					return "金18K定制价:";
				}else{
					return "铂PT950定制价:";	
				}
			}
			
			/*原有逻辑
			if($line["tuo_type"] == 2 || $line["tuo_type"] == 3 || $line["cat_type"] == "情侣戒")	// 托
			{
				//return $other_price_string;
				if($line['caizhi'] == '18K金' || $line['caizhi'] == '18K白金')
				{
					return '';//"铂PT950定制价:";
				}
				elseif($line['caizhi'] == '铂Pt950' || $line['caizhi'] == 'PT950')
				{
					return '';//"金18K定制价:";
				}
			}
			else
			{
				return $line["zhengshuhao"];
			}
			*/
		//}
	}
	function get_hb_h_col_value($line, $other_price)
	{
		$chanpinxian = $this->get_type_with_shipin_type($line["product_type"]);
		if(empty($chanpinxian)) return;	// 没有匹配的产品标签返回空
		
		$dataarr = array("铂Pt950", "铂Pt900", "铂Pt990", "18K金","18K白金","PT950","18K玫瑰金","18K彩金",'18K玫瑰白');
	
		//if($chanpinxian == "zuanshi"){
			
			/*原来的
			// 18K,PT托  定制价
			if((in_array($line["caizhi"],$dataarr ) && $line["tuo_type"] >1) || $line["cat_type"] == "情侣戒")
			{
				if(in_array($line['caizhi'],array("18K金","18K白金"))){
					return "铂PT950定制价:"."￥".number_format($other_price, 0,".","");
				}elseif(in_array($line['caizhi'],array("铂Pt950", "铂Pt900", "铂Pt990","PT950"))){
					return "金18K定制价:"."￥".number_format($other_price, 0,".","");
				}
				return "￥".number_format($other_price, 0,".","");
			}else{
				return $line["zhushijingdu"]."/".$line["zhushiyanse"];
			}*/
			
			
			//如果金托类型是成品，且款式分类是情侣戒需要显示定制价
			if($line['tuo_type'] == 1)
			{
				//如果是情侣
				if($line["cat_type"] == "情侣戒")
				{
					return "￥".number_format($other_price, 0,".","");
				}else{
					return $line["zhushijingdu"]."/".$line["zhushiyanse"];
				}
			}elseif($line["tuo_type"] == 2 || $line["tuo_type"] == 3)
			{
				//return $line["zhushijingdu"]."/".$line["zhushiyanse"];
				return "￥".number_format($other_price, 0,".","");
			}
			
		//}
	}



	/*------------------------------------------------------ */
	//-- 判断条码类别
	//-- by zlj 
	/*------------------------------------------------------ */
	function get_type_with_shipin_type($shipin_type)
	{
		//黄金等投资产品,黄金饰品及工艺品,素金饰品,   钻石饰品,珍珠饰品,彩宝饰品,翡翠饰品, 其他饰品,非珠宝,配件及特殊包装
		$res = "";

		$shipin_type = trim($shipin_type);
		switch($shipin_type)
		{
			case "黄金等投资产品":
			case "黄金饰品及工艺品":
			case "素金饰品":
				$res = "sujin";
				break;
			case '钻石饰品':
				$res = "zuanshi";
				break;
			case '珍珠饰品':
				$res = "zhenzhu";
				break;
			case '彩宝饰品':
			case '翡翠饰品':
				$res = "caibao";
				break;
			case '其他':
			case '裸石':
				//后续是新系统后新加的产品线
			case "K金":
				$res = "sujin";
				break;
			case "PT":
				$res = "sujin";
				break;
			case "银饰":
				$res = "sujin";
				break;
			case "珍珠":
				$res = "zhenzhu";
				break;
			case "钻石":
				$res = "zuanshi";
				break;
			case "翡翠":
				$res = "caibao";
				break;
			case "彩钻":
				$res = "caizuan";
				break;
			case "宝石":
				$res = "baoshi";
				break;
			case "足金镶嵌";
				$res = "zjxqtype";
				break;
			case "普通黄金";
				$res = "pthjtype";
				break;
			case "投资黄金";
				$res = "tzhjtype";
				break;
			case "定价黄金";
				$res = "djhjtype";
				break;	
			default:
		}
		return $res;
	}


	//AJAX检测订单号是否合法
	public function CheckOrderSn($params){
		$result = array('success' => 0,'error' =>'');
		$order_sn = trim($params['order_sn']);
		$model = new WarehouseBillModel(21);
		$warehousemodel = new WarehouseModel(21);
		$salemodel = new SalesModel(27);
		$res = $salemodel->CheckOrderSn($order_sn);
		//检查订单是否合法
		if(!$res){
			$result['error'] = '未查询到此订单!';
			Util::jsonExit($result);
		}
		//获得门店信息
		$dis = $salemodel->getDistributionByOrderId($res['id']);
		if(isset($dis['distribution_type'])){
				if($dis['distribution_type'] ==1 && isset($dis['shop_name'])){
					$managemodel = new ManagementModel(1);
					//配送方式：门店(通过店铺名称获取店铺信息)
					$infos = $managemodel->GetShopInfoByShopName($dis['shop_name']);
					//通过公司ID获取该公司下的仓库
					$infos = $warehousemodel->getRepairLastWarehouse($infos['company_id']);
                    $info['warehouse_id'] =$infos['id'];
                    $info['code'] =$infos['code']; 
                    $info['warehouse_name'] =$infos['name'];

                }else{
                    //配送方式：总公司到客户
                    $info['warehouse_id'] =606;
                    $info['code']='SZWX';
                    $info['warehouse_name'] ='总公司维修库';
				}
			}
		$info['customer'] =$res['consignee'];	//顾客姓名
		//返回入库仓库信息
		$result['data'] = $info;
		$result['success'] = 1;
		Util::jsonExit($result);
	}

}?>