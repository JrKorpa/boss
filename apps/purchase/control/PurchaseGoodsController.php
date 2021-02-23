<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseGoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-09 12:35:39
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseGoodsController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array('downPurcaseCSV');
	protected $dd;
	public function __construct()
	{
		parent::__construct();
		$this->dd=new DictView(new DictModel(1));
		$this->assign('dd',$this->dd);
	}
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
	//	Util::M('purchase_goods_attr','purchase',23);	//生成模型后请注释该行
	//	Util::V('purchase_goods',23);	//生成视图后请注释该行
		$this->render('purchase_goods_search_form.html');
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
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();

		$model = new PurchaseGoodsModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'purchase_goods_search_page';
		$this->render('purchase_goods_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

     //获取镶口信息
    public function getXiangkouList($style_sn='')
    {
        $apiStyle = new ApiStyleModel();
        $attres = $apiStyle->GetStyleAttribute($style_sn);
        $xiangkou_arr = array();
        if(!empty($attres) && is_array($attres)){
            $attr_list = array();
            //格式化属性数组结构，让attribute_code作为键值
            foreach($attres as $key=>$vo){
                 $attrcode = $vo['attribute_code'];                       
                 $attr_list[$attrcode] = $vo;
            }
            //获取材质属性列表,如果不为空覆盖默认材质属性列表
            if(!empty($attr_list['xiangkou']['value'])){
                $xiangkou_arr = $attr_list['xiangkou']['value'];
            }       
        }
        return $xiangkou_arr;
    }
	
	public function getGoodsAttrList($params=array()){
	    $style_sn = isset($params['style_sn'])?$params['style_sn']:'';
	    $output = isset($params['output'])?$params['output']:'';
	    //$caizhi_arr=array('1'=>'默认','2'=>'无','3'=>'9K','4'=>'10K','5'=>'18K','6'=>'24K','7'=>'PT950','8'=>'PT900','9'=>'S925' );
	    //$jinse_arr=array('1'=>'默认','2'=>'无','3'=>'按图做','4'=>'玫瑰金','5'=>'白','6'=>'黄','7'=>'黄白','8'=>'彩金','9'=>'分色' );
	    //定义默认属性列表
	    $goodsAttrModel = new GoodsAttributeModel(17);
	    $caizhi_arr = $goodsAttrModel->getCaizhiList(false);//false 读取属性数据库维护值 ，true 读取 固定维护值	
	    $jinse_arr  = $goodsAttrModel->getJinseList(false);//false 读取属性数据库维护值 ，true 读取 固定维护值
	    $cert_arr  = $goodsAttrModel->getCertList(false);//false 读取属性数据库维护值 ，true 读取 固定维护值
        $xiangqian_arr = $goodsAttrModel->getXiangqianListNew(false);
        $facework_arr = $goodsAttrModel->getFaceworkList(false);//false 读取属性数据库维护值 ，true 读取 固定维护值
	    if($style_sn !=''){
	        $apiStyle = new ApiStyleModel();
	        $attres = $apiStyle->GetStyleAttribute($style_sn);
	        if(!empty($attres) && is_array($attres)){
	            
	            $attr_list = array();
	            //格式化属性数组结构，让attribute_code作为键值
                foreach($attres as $key=>$vo){
                     $attrcode = $vo['attribute_code'];                       
                     $attr_list[$attrcode] = $vo;
                }
	        
    	        //获取材质属性列表,如果不为空覆盖默认材质属性列表
    	        if(!empty($attr_list['caizhi']['value'])){
   	                $caizhi_arr = $attr_list['caizhi']['value'];
    	        }
    	        //获取材质颜色属性列表,如果不为空覆盖默认材质颜色属性列表
    	        if(!empty($attr_list['caizhiyanse']['value'])){
   	                $jinse_arr = $attr_list['caizhiyanse']['value'];
    	        }
    	        if(!empty($attr_list['zhengshu']['value'])){
    	            $cert_arr = $attr_list['zhengshu']['value'];
    	        }    	        
	        }
	    } 
	    $data = array(
	        'caizhi_arr'=>$caizhi_arr,
	        'jinse_arr' =>$jinse_arr,
	        'cert_arr' =>$cert_arr,
	        'xiangqian_arr'=>$xiangqian_arr,
	        'facework_arr'=>$facework_arr 
	    );
	    if($output=="json"){
	        $result['success'] = 1;
	        $result['content'] = $data;
	        Util::jsonExit($data);	        
	    }else{
	        return $data;
	    }
	}
	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$pinfo_id = _Request::get("pinfo_id");//采购单ID
		$is_style = _Request::get("is_style");//是否有款采购
		//根据单据是有款采购还是无款采购来调用不同的模板。
		$html_name = $is_style?'purchase_goods_style_info.html':'purchase_goods_info.html';

        $model = new PurchaseInfoModel($pinfo_id, 23);
        $do = $model->getDataObject();
		$styleApiModel = new ApiStyleModel();
		$product_type_list = $styleApiModel->getProductTypeInfo();
		$cat_type_list = $styleApiModel->getCatTypeInfo();
		$goodsAttrList = $this->getGoodsAttrList();	//获取默认属性列表	
        $is_djbh = $do['t_id'] == 9 ? true : false;//是否鼎捷经销商备货 ID = 9；
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch($html_name,array(
			'view'=>new PurchaseGoodsView(new PurchaseGoodsModel(23)),
			'pinfo_id' => $pinfo_id,//采购单ID
			'is_style' => $is_style,//是否有款采购
            'is_djbh' => $is_djbh,//是否鼎捷经销商备货
		    'jinse_arr' =>$goodsAttrList['jinse_arr'],
			'caizhi_arr'=>$goodsAttrList['caizhi_arr'],
		    'cert_arr'=>$goodsAttrList['cert_arr'],
		    'product_type_list' => $product_type_list,
			'cat_type_list' => $cat_type_list,
		));

		$result['title'] = '添加采购款式';
		Util::jsonExit($result);
	}
	//根据款式编号获取款式图库
    public function getStyleGallery(){

        $style_sn = _Request::get('style_sn');
        if(empty($style_sn)){
            exit;
        }
        $model = new ApiStyleModel();
        
        $style_gallery = $model->getStyleGallery(array('style_sn'=>$style_sn));
        $first_pic     = array();
        //将image_place=0的排在最后面
        if(!empty($style_gallery) && is_array(current($style_gallery))){
            foreach ($style_gallery as $key=>$vo){
                if($vo['image_place'] ==0){
                    unset($style_gallery[$key]);
                    $style_gallery[] = $vo;
                }
            }
            reset($style_gallery);//重置指针
            $first_pic = current($style_gallery);
        }else{
            exit;
        }
        
        $this->render('purchase_style_gallery.html',array(
            'res_pic_list' =>$style_gallery,
            'first_pic'    =>$first_pic
        ));
    }
	public function batch_add(){
		$pur_id = _Request::get("pur_id");//采购单ID
		$is_style = _Request::get("is_style");//1=有款采购
		$result = array('success' => 0,'error' => '');

		$result['content'] = $this->fetch('purchase_batch_add_goods.html',[
			'view'=>new PurchaseInfoView(new PurchaseInfoModel($pur_id,23)),
			'is_style' => $is_style,
		]);

		$result['title'] = '批量采购款式';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
	    $result = array('success' => 0,'error' => '');
		$is_style = _Request::get("is_style");//是否有款采购
        $pinfo_id = _Request::get("pinfo_id");//采购单ID
		//根据单据是有款采购还是无款采购来调用不同的模板。
		$html_name = $is_style?'purchase_goods_style_info.html':'purchase_goods_info.html';
        $model = new PurchaseInfoModel($pinfo_id, 23);
        $do = $model->getDataObject();
		$styleApiModel = new ApiStyleModel();
		$product_type_list = $styleApiModel->getProductTypeInfo();	//产品线
		$cat_type_list = $styleApiModel->getCatTypeInfo();			//款式分类
		$is_djbh = $do['t_id'] == 9 ? true : false;//是否鼎捷经销商备货 ID = 9；
		$id = _Request::getInt("id");
		$view = new PurchaseGoodsView(new PurchaseGoodsModel($id,23));
		$style_sn = $view->get_style_sn();
		$goodsAttrList = $this->getGoodsAttrList();	//获取默认属性列表
		$result['content'] = $this->fetch($html_name,array(
			'view'=>$view,
			'pinfo_id' => $pinfo_id,
			'is_style' => $is_style,
            'is_djbh' => $is_djbh,
			'product_type_list' => $product_type_list,
			'cat_type_list' => $cat_type_list,
		    'jinse_arr' =>$goodsAttrList['jinse_arr'],
		    'caizhi_arr'=>$goodsAttrList['caizhi_arr'],
		    'cert_arr'=>$goodsAttrList['cert_arr'],
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 * 渲染申请修改页面
	 */
	public function applyEdit($params){

		$result = array('success' => 0,'error' => '');
		$is_style = _Request::get("is_style");	//是否有款采购
		$pinfo_id = _Request::get("pinfo_id");	//采购单ID
		$id = intval($params["id"]);			//采购商品ID
                
		$model = new PurchaseGoodsModel($id,23);
		$view = new PurchaseGoodsView($model);
		$bc_status = $view->get_bc_status();	//数据字典 buchan_status
		$style_sn = $view->get_style_sn();
		if($bc_status > 4){
			$result['content'] = '该货品已生产';
			Util::jsonExit($result);
		}
		$purchaseType = new PurchaseTypeModel(23);
		$type_list = $purchaseType->getList(1);
        $attr_list = $this->getGoodsAttrList();
		$result['content'] = $this->fetch('purchase_goods_apply_info.html',array(
			'view'=>$view,
			'pinfo_id' => $pinfo_id,
		    'type_list'=>$type_list,
		    'caizhi_arr'=>$attr_list['caizhi_arr'],
		    'jinse_arr'=>$attr_list['jinse_arr'],
		    'cert_arr' =>$attr_list['cert_arr'],
			'is_style' => $is_style,
		));
		$result['title'] = '申请布产修改';
		Util::jsonExit($result);

	}

	//渲染审核页面
	public function showCheck($params){

		$is_style = _Request::get("is_style");	//是否有款采购
		$pinfo_id = _Request::get("pinfo_id");	//采购单ID
		$id = intval($params["id"]);			//采购商品ID

		$purchaseType = new PurchaseTypeModel(23);
		$type_list = $purchaseType->getList(1);

		$view = new PurchaseGoodsView(new PurchaseGoodsModel($id,23));
		$olddo = $view->get_all_attr();
		$newdo = $view->get_apply_attr();
        $olddo[9]['value'] = $this->replaceTsKezi($olddo[9]['value']);
        $newdo['kezi'] = $this->replaceTsKezi($newdo['kezi']);
		//print_r($newdo);exit;
		$newdo['note'] = $newdo['info'];
		$this->render('purchase_goods_check_info.html',array(
			'view'=>$view, 'pinfo_id' => $pinfo_id, 'is_style' => $is_style,
			'type_list'=>$type_list,'olddo'=>$olddo,'newdo'=>$newdo
		));

	}
	
	public function applycheck(){
		$result = array('success' => 0,'error' =>'');
		$res = Auth::getAuth('BUTTON10047');//有审核采购单 权限，就有这个审核权限
		if(!$res){
		    $result['error'] = '对不起,您无权操作';
		    Util::jsonExit($result);
		}
		
        $id = _Post::getInt('id');
        $pass = _Post::getInt('pass');
		$model = new PurchaseGoodsModel($id,23);
		$logmodel = new PurchaseLogModel(24);
		$peishiModel = new PeishiListModel(14);
		$view = new PurchaseGoodsView($model);
		$bc_info = $view->get_buchan_info($id);
		if(empty($bc_info)){
		    $result['error'] = '找不到布产单！';
		    Util::jsonExit($result);
		}
		$bc_status = $bc_info['status'];	//数据字典 buchan_status
		$bc_id = $bc_info['id'];
		$style_sn = $view->get_style_sn();        
        $rece_id = $view->get_pinfo_id();
        $newdo = $view->get_apply_attr();
        $olddo = $view->get_all_attr();
        $remark = '采购单审核通过,修改属性:';
        if($newdo['num']!=$view->get_num()){
            $remark .= "数量：——> ".$newdo['num'];
        }
        if ($newdo['g_name']!=$view->get_g_name()){
            $remark .= "名称：——>".$newdo['g_name'];
        }
        if ($newdo['xiangqian']!=$view->get_xiangqian()){
            $remark .= "镶嵌方式：——>".$newdo['xiangqian'];
        }
        foreach($olddo as $k => $v){
            if(isset($newdo[$v['code']]) && $newdo[$v['code']]!=$v['value']){
                $remark .= $v['name']."：——>".$newdo[$v['code']];
            }
        }			
		
		if($bc_status > 4){
			$result['error'] = '该货品已生产';
			Util::jsonExit($result);
		}
		if($pass == 1){//审核通过 == 修改采购商品信息 修改布产商品信息 清空申请状态
			$res = $model->checkApplyPass($id,$bc_id,$olddo);
			if(!$res){
			    $result['error'] = '操作失败。';
			    Util::jsonExit($result);
			}
			//$result['success'] = 1;
			//$result['pass'] = $pass;
			//Util::jsonExit($result);
            $logmodel->addLog($rece_id, 3, $remark); 
            //更新布产主石，副石信息
            $styleModel = new CStyleModel(11);
            $oldAttr2 = $model->getAllProductAttr($bc_id);//重新获取布产属性所有属性            
            $newAttr2 = $styleModel->getStoneAttrList($style_sn,$oldAttr2);
            $res = $model->saveProductAttrData($bc_id, $newAttr2);
            if($res['success']==0){
                $error = date('Y-m-d H:i:s')."--操作失败:同步主石，副石信息失败。".$res['error']."\r\n";
                file_put_contents('caigou_checkApplyPass.log',$error,FILE_APPEND);
                $result['error'] = "审核成功！但同步主石，副石信息失败。".$res['error']." ，请联系技术人员处理！";
                Util::jsonExit($result);
            }                                   
            $res = $peishiModel->createPeishiList($bc_id,'update','修改采购单');
            if($res['success']==0){
                $error = date('Y-m-d H:i:s')."--操作失败:同步布产单关联的配石单失败。".$res['error']."\r\n";
                file_put_contents('caigou_checkApplyPass.log',$error,FILE_APPEND);
                $result['error'] = "审核成功！但同步布产单关联的配石单失败。".$res['error']." ，请联系技术人员处理！";
                Util::jsonExit($result);
            }
			$result['success'] = 1;
		}else{//审核取消 == 清空申请信息，取消申请状态
			$res = $model->checkApplyOut($id);
            $logmodel->addLog($rece_id, 3, "采购单修改信息驳回");                        
			if($res){
				$result['success'] = 1;
			}else{
				$result['error'] = '操作失败';
			}			
		}
		$result['pass'] = $pass;
		Util::jsonExit($result);
		
	}

	//编辑有款的时候调用属性
	public function editAttr_Style_sn($params)
	{
		$style_sn = $params['style_sn'];
		$id = $params['id'];
		$model = new PurchaseGoodsAttrModel(24);
		$attrArr = $model->getGoodsAttr($id);
		$arr = array();
		foreach($attrArr as $key => $val)
		{
			$arr[$val['code']] = $val['value'];
		}

		$apimodel = new ApiStyleModel();
		$style_attr = $apimodel->GetStyleAttribute($style_sn);

		$result['content'] = $this->fetch("purchase_style_attribute_edit.html",array(
			'style_attr' => $style_attr,
			'attr' => $arr
		));
		Util::jsonExit($result);
	}

	//编辑无款的时候调用属性
	public function editAttr_product_cat($params)
	{
		$product_type_id = $params['product_type_id'];
		$cat_type_id = $params['cat_type_id'];
		$id = $params['id'];
		$model = new PurchaseGoodsAttrModel(24);
		$attrArr = $model->getGoodsAttr($id);
		$arr = array();
		foreach($attrArr as $key => $val)
		{
			$arr[$val['code']] = $val['value'];
		}

		$apimodel = new ApiStyleModel();
		$style_attr = $apimodel->GetCatAttribute($product_type_id,$cat_type_id);

		$result['content'] = $this->fetch("purchase_style_attribute_edit.html",array(
			'style_attr' => $style_attr,
			'attr' => $arr
		));
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$model = new PurchaseGoodsModel($id,23);
		$infoModel = new PurchaseInfoModel($model->getValue('pinfo_id'),23);
		$attrModel = new PurchaseGoodsAttrModel(23);

        $info = array();
        $info = $attrModel->getGoodsAttr($id);
        $kezi = $this->replaceTsKezi($info[9]['value']);
        $info[9]['value'] = $model->retWord($kezi);

		$result['content'] = $this->fetch('purchase_goods_show.html',array(
			'view'=>new PurchaseGoodsView($model),
			'is_style' => $infoModel->getValue('is_style'),
			'attrArr'  => $info
		));
		$result['title'] = '采购详情';
		Util::jsonExit($result);

	}

	/**
	 * 批量添加采购商品
	 */
	public function batch_insert($params){
		$result = array('success' => 0,'error' =>'','msg'=>'');
		$pur_id = $params['pur_id'];//采购单ID
		$model =  new PurchaseGoodsModel(24);
        $infoModel = new PurchaseInfoModel($pur_id, 23);
        $goodsAttrModel = new GoodsAttributeModel(17);
        $do = $infoModel->getDataObject();
        $is_djbh = $do['t_id'];
		if (!isset($_FILES['batch_goods']['error']) || $_FILES['batch_goods']['error'] != 0)
		{
			$result['error'] = '请选择上传文件';
		}else{
			$file_array = explode(".",$_FILES['batch_goods']['name']);
			$file_extension = strtolower(array_pop($file_array));
			if($file_extension != 'csv')
			{
				$result['error'] = "请上传csv格式的文件";
				Util::jsonExit($result);
			}
			$file = $_FILES['batch_goods']['tmp_name'];
			$data = Upload::getCSV($file);
			if(empty($data)){
			    $result['error'] = "批量导入内容为空";
			    Util::jsonExit($result);
			}

			//* 款号,* 名称,* 数量,* 镶嵌要求	,(*)表面工艺,* 材质,(K金*)K金可做颜色,(*戒指)指圈,(*)钻石大小,(*)镶口,(特定)证书号,颜色,净度,刻字内容,布产备注
			$label = array('style_sn','goods_name','g_num','consignee','xiangqian','face_work','caizhi','18k_color','zhiquan','zuanshidaxiao','zhushi_num','xiangkou','zhengshuhao','cert', 'yanse', 'jingdu','kezi','note');
            if($is_djbh == 9){//鼎捷字段
                $is_djbh_str = array('p_sn_out','ds_xiangci','pinhao');
                $label = array_merge($label, $is_djbh_str);
            }
			$all_style = $model->getAllStyleSN();
			$caizhi_list1 = array("9K","10K","14K","18K");
			$caizhi_list2 = array("PT900","PT950","PT990","PT999","S925","S990","裸石","其它","千足金","千足金银","千足银","足金","无");
            $errStyleSn = array();
			foreach ($data as $k=>$row) {
                if($is_djbh!=9){
                    if(isset($row[18])){
                        unset($row[18]);
                    }if(isset($row[19])){
                        unset($row[19]);
                    }if(isset($row[20])){
                        unset($row[20]);
                    }if(isset($row[21])){
                        unset($row[21]);
                    }if(isset($row[22])){
                        unset($row[22]);
                    }
                }
				$data[$k] = array_combine($label,$row);
				$data[$k] = array_map('trim',$data[$k]);//批量过滤空格
				$data[$k]['pinfo_id'] = $pur_id;
				if(empty($data[$k]['style_sn'])){
					$result['error'] = '第'.($k+2)."行,款号必填";
					Util::jsonExit($result);
				}
				if(!in_array(trim($data[$k]['style_sn']),$all_style)){
					$result['error'] = '第'.($k+2)."行,款号不存在或不是已审核状态";
					Util::jsonExit($result);
				}
				if(empty($data[$k]['goods_name'])){
					$result['error'] = '第'.($k+2)."行,商品名称必填";
					Util::jsonExit($result);
				}
				if(empty($data[$k]['g_num'])){
					$result['error'] = '第'.($k+2)."行,商品数量必填";
					Util::jsonExit($result);
				}
				if(!Util::isNum($data[$k]['g_num'])){
					$result['error'] = '第'.($k+2)."行,商品数量必须是数字";
					Util::jsonExit($result);
				}
				if(empty($data[$k]['xiangqian'])){
					$result['error'] = '第'.($k+2)."行,镶嵌要求必填";
					Util::jsonExit($result);
				}
				if(empty($data[$k]['caizhi'])){
					$result['error'] = '第'.($k+2)."行,材质必填";
					Util::jsonExit($result);
				}else{
				    $data[$k]['caizhi'] = strtoupper($data[$k]['caizhi']);
				}
                if(empty($data[$k]['p_sn_out']) && $is_djbh == '9'){
                    $result['error'] = '第'.($k+2)."行,外部单号必填";
                    Util::jsonExit($result);
                }
                if(empty($data[$k]['ds_xiangci']) && $is_djbh == '9'){
                    $result['error'] = '第'.($k+2)."行,单身-项次必填";
                    Util::jsonExit($result);
                }
                
                //检查特定属性字段，是否合法
				$res = $this->checkGoodsData($data[$k]);
				if($res['success']==0){
				     $result['error'] = '第'.($k+2)."行,".$res['error'];
				     Util::jsonExit($result);
				}else{
				     $data[$k] = $res['data'];//获取处理过的合法字段值
				}

                //获取巷口
                $xiangkouList = $this->getXiangkouList($data[$k]['style_sn']);
				//获取所有属性
				$attrlist = $this->getGoodsAttrList();				
				if(!in_array($data[$k]['caizhi'],$attrlist['caizhi_arr'])){
				    $result['error'] = '第'.($k+2)."行,材质【{$data[$k]['caizhi']}】在系统不存在";
				    Util::jsonExit($result);
				}
				if(in_array($data[$k]['caizhi'],$caizhi_list1)){
				    if(empty($data[$k]['18k_color']) || $data[$k]['18k_color']=="无"){
				        $result['error'] = '第'.($k+2)."行,材质为【{$data[$k]['caizhi']}】时，金色不能为空";
				        Util::jsonExit($result);
				    }
				}else if(in_array($data[$k]['caizhi'],$caizhi_list2)){
				    if(!empty($data[$k]['18k_color']) && $data[$k]['18k_color']!="无"){
				        $result['error'] = '第'.($k+2)."行,材质为【{$data[$k]['caizhi']}】时，金色必须为空";
				        Util::jsonExit($result);
				    }
				}
				if(!in_array($data[$k]['caizhi'],$caizhi_list2)){
    				if(!in_array($data[$k]['18k_color'],$attrlist['jinse_arr'])){
    				    $result['error'] = '第'.($k+2)."行,材质颜色【{$data[$k]['18k_color']}】在系统不存在";
    				    Util::jsonExit($result);
    				}
			    }

			    if(!in_array($data[$k]['caizhi'],$caizhi_list2)){
    				if(!in_array($data[$k]['18k_color'],$attrlist['jinse_arr'])){
    				    $result['error'] = '第'.($k+2)."行,材质颜色【{$data[$k]['18k_color']}】在系统不存在";
    				    Util::jsonExit($result);
    				}
			    }
			    //镶嵌方式 数据格式验证
			    if(!in_array($data[$k]['xiangqian'],$attrlist['xiangqian_arr'])){
		            $_xiangqian = '【'.implode("】【",$attrlist['xiangqian_arr']).'】';
			        $result['error'] = '第'.($k+2)."行,镶嵌方式【{$data[$k]['xiangqian']}】在系统不存在，请检文字是否有误。<br/><font style='color:red'>提示：镶嵌方式只能是:{$_xiangqian}</font>";
			        Util::jsonExit($result);
			    }
			    //表面工艺  数据格式验证
			    if(!in_array($data[$k]['face_work'],$attrlist['facework_arr'])){
			        $_facework = '【'.implode("】【",$attrlist['facework_arr']).'】';
			        $result['error'] = '第'.($k+2)."行,镶嵌方式【{$data[$k]['face_work']}】在系统不存在，请检文字是否有误。<br/><font style='color:red'>提示：表面工艺只能是:{$_facework}</font>";
			        Util::jsonExit($result);
			    }
			    //证书类型  数据格式验证
			    if(!empty($data[$k]['cert']) && !in_array($data[$k]['cert'],$attrlist['cert_arr'])){
			        $_facework = '【'.implode("】【",$attrlist['cert_arr']).'】';
			        $result['error'] = '第'.($k+2)."行,证书类型【{$data[$k]['cert']}】在系统不存在，请检文字是否有误。<br/><font style='color:red'>提示：证书类型只能是:{$_facework}</font>";
			        Util::jsonExit($result);
			    }
			    if(!empty($data[$k]['zhengshuhao']) && !empty($data[$k]['cert'])){
			        $res = $model->checkCertByCertId($data[$k]['zhengshuhao'],$data[$k]['cert']);
			        if($res === false){
			            $result['error'] = '第'.($k+2)."行,证书类型【{$data[$k]['cert']}】与证书号【{$data[$k]['zhengshuhao']}】对应的证书类型不匹配。";
			            Util::jsonExit($result);
			        }
			    }
                if(!empty($data[$k]['kezi'])){ //刻字验证
                    $apiStyle = new ApiStyleModel();
                    $styleAttrInfo = $apiStyle->GetStyleAttributeInfo(array('style_sn'=>$data[$k]['style_sn']));
                    $attrinfo = empty($styleAttrInfo) ? array() : $styleAttrInfo;
                    //刻字验证
                    $keziModel =  new PurchaseGoodsModel(24);
                    $allkezi = $keziModel->getKeziData();
                    //是否欧版戒 92
                    if(isset($attrinfo[92]['value']) && !empty($attrinfo[92]['value']) && trim($attrinfo[92]['value'] == '是')){
                        $str_count = $keziModel->pdKeziData($data[$k]['kezi'],$allkezi,1);
                        if($str_count['str_count']>=50){
                            $result['error'] = "第".($k+2)."行,欧版戒只能刻50位以内的任何字符！";
                            Util::jsonExit($result);
                        }
                        $data[$k]['kezi'] = $str_count['kezi'];
                    }else{
                        $str_count = $keziModel->pdKeziData($data[$k]['kezi'],$allkezi);
                        if($str_count['str_count']>6){
                            $result['error'] = "第".($k+2)."行,非欧版戒只能刻最多6位字符！";
                            Util::jsonExit($result);
                        }
                        if($str_count['err_bd'] != ''){
                            $result['error'] = "第".($k+2)."行,非欧版戒下列字符不可以刻：".$str_count['err_bd'];
                            Util::jsonExit($result);
                        }
                        $data[$k]['kezi'] = $str_count['kezi'];
                    }
                }
                if(!in_array($data[$k]['xiangkou'], $xiangkouList)){
                    $errStyleSn[$data[$k]['style_sn']][] = $data[$k]['xiangkou'];
                }
			}
            $err_str = '保存成功！！！<br/>';
            if(!empty($errStyleSn)){
                foreach ($errStyleSn as $k_sn => $xiangkou) {
                    $err_str.= "提示：款式：".$k_sn."没有维护如下镶口（".implode(",", array_unique($xiangkou))."）<br/>";
                }
                $err_str.="请联系款式库专员核对！";
            }
			$res = $model->batch_insert($data,$pur_id);
			if($res !== false){
				$result['success'] = 1;
                $result['msg'] = $err_str;
			}else{
				$result['error'] = "保存失败";
			}
		}
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$style_sn	= _Post::getString('style_sn');
		$product_type_id= _Post::getInt('product_type_id');
		$cat_type_id	= _Post::getInt('cat_type_id');
		$info		= _Post::getString('info');
		$num		= _Post::getInt('num');
		$consignee=_Post::getString('consignee');
		$is_urgent	= _Post::getInt('is_urgent');
		$pinfo_id	= _Post::getInt('pinfo_id');
		$xiangqian=_Post::getString('xiangqian');
		$attr_arr = array();
		//add by zhangruiying
		$params['diamond_size']=array(_Post::getString('diamond_size'),'大小',0);
		$params['color']=array(_Post::getString('color'),'颜色',0);
		$params['neatness']=array(_Post::getString('neatness'),'净度',0);
		$params['certificate_no']=array(_Post::getString('certificate_no'),'证书号',0);
		//add end
		foreach($params as $key => $val)
		{
			if(is_array($val))
			{
				if($val[2] == 1 && ($val[0] == "" || $val[0] == "无"))
				{
					$result['error'] = $val[1].'为必填项';
					Util::jsonExit($result);
				}
				$attr_arr[] = array(
					'code' => $key,
					'name' => $val[1],
					'value' => $val[0]
				);
			}
		}
		$olddo = array();
		$newdo=array(
			'style_sn' => $style_sn,
			'product_type_id' => $product_type_id,
			'cat_type_id' => $cat_type_id,
			'info'	=> $info,
			'num'	=> $num,
			'is_urgent'=> $is_urgent,
			'pinfo_id' => $pinfo_id,
			'xiangqian'=>$xiangqian,
			'consignee'=>$consignee
		);

		$newmodel =  new PurchaseGoodsModel(24);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			if(count($attr_arr))
			{
				//插入属性表
				$attrModel =  new PurchaseGoodsAttrModel(24);
				foreach($attr_arr as $key => $val)
				{
					$val['g_id'] = $res;
					$attrModel->saveData($val,array());
				}
			}

			$m = new PurchaseInfoModel($pinfo_id,24);
			$snum = $newmodel ->getSum_num($pinfo_id);
			$m->setValue('p_sum',$snum);
			$m->save();
			$result['success'] = 1;
			$result['s_num'] = $snum;
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id			= _Post::getInt('id');
		$style_sn	= _Post::getString('style_sn');
		$product_type_id = _Post::getInt('product_type_id');
		$cat_type_id	 = _Post::getInt('cat_type_id');
		$info		= _Post::getString('info');
		$num		= _Post::getInt('num');
		$consignee=_Post::getString('consignee');
		$is_urgent	= _Post::getInt('is_urgent');
		$pinfo_id	= _Post::getInt('pinfo_id');
		$xiangqian=_Post::getString('xiangqian');
		$params['diamond_size']=array(_Post::getString('diamond_size'),'大小',0);
		$params['color']=array(_Post::getString('color'),'颜色',0);
		$params['neatness']=array(_Post::getString('neatness'),'净度',0);
		$params['certificate_no']=array(_Post::getString('certificate_no'),'证书号',0);
		$attr_arr = array();
		foreach($params as $key => $val)
		{
			if(is_array($val))
			{
				if($val[2] == 1 && ($val[0] == "" || $val[0] == "无"))
				{
					$result['error'] = $val[1].'为必填项';
					Util::jsonExit($result);
				}
				$attr_arr[] = array(
					'code' => $key,
					'name' => $val[1],
					'value' => $val[0]
				);
			}
		}

		$newmodel =  new PurchaseGoodsModel($id,24);
		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'	=> $id,
			'style_sn' => $style_sn,
			'product_type_id' => $product_type_id,
			'cat_type_id' => $cat_type_id,
			'info'	=> $info,
			'num'	=> $num,
			'is_urgent'=>$is_urgent,
			'xiangqian'=>$xiangqian,
			'consignee'=>$consignee
		);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			//插入属性表
			$attrModel =  new PurchaseGoodsAttrModel(24);
			$attrModel->delGoodsAttr($id);//删除原来的数据，再新增一次。

			if(count($attr_arr))
			{
				//插入属性表
				foreach($attr_arr as $key => $val)
				{
					$val['g_id'] = $res;
					$attrModel->saveData($val,array());
				}
			}

			$m = new PurchaseInfoModel($pinfo_id,24);
			$snum = $newmodel ->getSum_num($pinfo_id);
			$m->setValue('p_sum',$snum);
			$m->save();
			$result['success'] = 1;
			$result['s_num'] = $snum;
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}
	/**
	 * 采购单的属性字段是否合法
	 * @param unknown $data
	 */
	protected function checkGoodsData($args){
	    $result = array('success' => 0,'error' => '');
	    $xiangqian = isset($args['xiangqian'])?$args['xiangqian']:'';
	    //主石单颗重验证	    
	    if(!empty($args['zuanshidaxiao']) && !is_numeric($args['zuanshidaxiao'])){
	        $result['error']="主石单颗重不合法，主石单颗重必须为数字!";
	        return $result;
	    }else if(isset($args['zuanshidaxiao'])){
	        $args['zuanshidaxiao'] = $args['zuanshidaxiao']/1;
	    }
	    //主石粒数验证
	    if(!empty($args['zhushi_num']) && !preg_match("/^\d+$/",$args['zhushi_num'])){
	        $result['error']="主石粒数不合法，主石粒数必须为正整数!";
	        return $result;
	    }else if(isset($args['zhushi_num'])){
	        $args['zhushi_num'] = $args['zhushi_num']/1;
	    }
	    if($xiangqian<>'不需工厂镶嵌'){
    	    if(isset($args['zuanshidaxiao']) && isset($args['zhushi_num'])){
    	        if(($args['zuanshidaxiao']==0 && $args['zhushi_num']>0) ||($args['zuanshidaxiao']>0 && $args['zhushi_num']==0)){
    	            $result['error']="主石单颗重和主石粒数不合要求，两者要么同时大于0，要么同时为空或0";
    	            return $result;
    	        }
    	    }
	    }
	    //镶口
	    if(!empty($args['xiangkou']) && !is_numeric($args['xiangkou'])){
	        $result['error']="镶口不合法，镶口必须为数字!";
	        return $result;
	    }else if(isset($args['xiangkou'])){
	        $args['xiangkou'] = $args['xiangkou']/1;
	        //镶口是否合法
	        if($xiangqian<>'不需工厂镶嵌'){
    	        if(!empty($args['xiangkou']) && isset($args['cart'])){
    	            if(!$this->GetStone((float)$args['xiangkou'],(float)$args['cart'])){
    	                $result['error'] = "镶口和石重不匹配";
    	                return $result;
    	            }
    	        }
	        }
	    }
	     
	    //金重
	    /*
	    if(!empty($args['jinzhong']) && !is_numeric($args['jinzhong'])){
	        $result['error']="金重不合法，金重必须为数字!";
	        return $result;
	    }else if(isset($args['jinzhong'])){
	        $args['jinzhong'] = $args['jinzhong']/1;
	    }*/
	    
	    //证书号
	    if(!empty($args['zhengshuhao']) && !preg_match("/^[\-|a-z|A-Z|0-9|\|]+$/is",$args['zhengshuhao'])){
	        $result['error']="证书号不合法，证书号只能包含【字母】【数字】【英文竖线】,英文竖线作为多个证书号分隔符。";
	        return $result;
	    }
	    //证书类型验证
	    if(!empty($args['zhengshuhao']) && isset($args['cert']) && ($args['cert']=="" ||$args['cert']=="无")){
	        $result['error']="证书类型不能为空或无，填写了证书号必须填写有效的证书类型";
	        return $result;	        
	    }
	    //指圈
	    if(!empty($args['zhiquan']) && !is_numeric($args['zhiquan'])){
	        $result['error']="指圈不合法，指圈必须为数字!";
	        return $result;
	    }else if(isset($args['zhiquan'])){
	        $args['zhiquan'] = $args['zhiquan']/1;
	    }
	    $result['success'] = 1;
	    $result['data'] = $args;
	    return $result;
	}
	public function applyInsert(){

		$result = array('success' => 0,'error' =>'');
		$bc_sn = _Post::getInt('bc_sn'); //布产状态
                $logmodel = new PurchaseLogModel(24);
                $model =  new PurchaseGoodsModel(24);
		if($bc_sn >= 4){
			$result['error'] = "该货品已生成,不能进行修改!";
			Util::jsonExit($result);
		}

		$pur['p_sn'] = _Post::getString('p_sn');		//采购单号
		$pur['t_id'] = _Post::getInt('t_id');			//采购分类
		$pur['p_info'] = _Post::getString('p_info');	//采购单备注
		$pur['pinfo_id'] = _Post::getInt('pinfo_id'); 	//采购ID
		$data['style_sn'] = _Post::getString('style_sn');
		$data['g_name'] = _Post::getString('g_name');
		$data['num'] = _Post::getInt('num');
		$data['xiangqian'] = _Post::getString('xiangqian');
		$data['face_work'] = _Post::getString('face_work');
		$data['caizhi'] = _Post::getString('caizhi');
		$data['18k_color'] = _Post::getString('18k_color');
		$data['zhiquan'] = _Post::get('zhiquan');
		$data['zuanshidaxiao'] = _Post::getString('zuanshidaxiao');
		$data['zhushi_num'] = _Post::get("zhushi_num");
		$data['xiangkou'] = _Post::getString('xiangkou');
		$data['zhengshuhao'] = _Post::getString('zhengshuhao');
		$data['cert'] = _Post::get('cert');
		$data['yanse'] = _Post::getString('yanse');
		$data['jingdu'] = _Post::getString('jingdu');
		$data['kezi'] = _Post::getString('kezi');
		$data['info'] = _Post::getString('info');
		$data['id'] = _Post::getInt('id');	
		
		$res = $this->checkGoodsData($data);
		if($res['success'] ==0){
		    $result['error'] = $res['error'];
		    Util::jsonExit($result);
		}else{
		    $data = $res['data']; 
		}
		$purModel = new PurchaseGoodsModel($data['id'],24);
                
		$view = new PurchaseGoodsView($purModel);
		$bc_status = $view->get_bc_status();	//数据字典 buchan_status
		if($bc_status > 4){
			$result['error'] = '该货品已生产,不能申请';
			Util::jsonExit($result);
		}

        if(!empty($data['kezi'])){ //刻字验证
            
            $apiStyle = new ApiStyleModel();
            $styleAttrInfo = $apiStyle->GetStyleAttributeInfo(array('style_sn'=>$data['style_sn']));
            $attrinfo = empty($styleAttrInfo) ? array() : $styleAttrInfo;
            //刻字验证
            $keziModel =  new PurchaseGoodsModel(24);
            $allkezi = $keziModel->getKeziData();
            //是否欧版戒 92
            if(isset($attrinfo[92]['value']) && !empty($attrinfo[92]['value']) && trim($attrinfo[92]['value'] == '是')){
                $str_count = $keziModel->pdKeziData($data['kezi'],$allkezi,1);
                if($str_count['str_count']>=50){
                    $result['error'] = "欧版戒只能刻50位以内的任何字符！";
                    Util::jsonExit($result);
                }
                $data['kezi'] = $str_count['kezi'];
            }else{
                $str_count = $keziModel->pdKeziData($data['kezi'],$allkezi);
                if($str_count['str_count']>6){
                    $result['error'] = "非欧版戒只能刻最多6位字符！（一个汉字为一个字符）";
                    Util::jsonExit($result);
                }
                if($str_count['err_bd'] != ''){
                    $result['error'] = "非欧版戒下列字符不可以刻：".$str_count['err_bd'];
                    Util::jsonExit($result);
                }
                $data['kezi'] = $str_count['kezi'];
            }
        }                 
              
		$purModel->editPurInfo($pur);//修改采购分类,备注。[无审核]
		$res = $purModel->applyEdit($data);//写入申请信息
                
                //把申请修改的信息写入日志
                $olddo = $purModel->getDataObject();
                if(isset($olddo['apply_info'])){
                    $olddo = unserialize($olddo['apply_info']);
                }
                                //print_r($olddo);exit;
                $remark = '款号：'.$olddo['style_sn'].'，申请修改采购信息：';
                $index_str  = array('款号','名称','数量','镶嵌','表面工艺','材质','k金可做颜色','指圈','主石单颗重','主石粒数','镶口','证书号','证书类型','颜色','净度','刻字内容','备注','ID');
                $i = 0;
                
                foreach ($data as $k => $v){
                    if($v != $olddo[$k]){
                        if(empty($data[$k])){
                            $data[$k] = '[空]';
                        }
                        $remark .= $index_str[$i].":".$olddo[$k]." 申请修改为:".$data[$k];
                        
                    }
                    $i ++;
                }
                //end log
		if($res != false){
			$result['success'] = 1;
                        
                        $logmodel->addLog($pur['pinfo_id'] , 3, $remark);
		}else{
                        
			$result['error'] = "申请失败";
                        $logmodel->addLog($pur['pinfo_id'], 3, "申请失败".$remark);
		}
                
                
		Util::jsonExit($result);
	}
	
	//有款采购
	public function insertHasStyle($params){
		$result = array('success' => 0,'error' =>'', 'msg'=>'');
		$data = $this->getSubmitDataHasStyle();
		foreach($data as $k=>$v){
			$data[$k] = trim($v);
		}
		$res = $this->checkGoodsData($data);
		if($res['success']==0){
		    $result['error'] = $res['error'];
		    Util::jsonExit($result);
		}else{
		    $data = $res['data'];
		}
		
        $infoModel = new PurchaseInfoModel($data['pinfo_id'], 23);
        $do = $infoModel->getDataObject();
		if(empty($data['style_sn'])){$result['error'] = '款号必填';Util::jsonExit($result);}
		if(empty($data['g_name'])){$result['error'] = '货品名称必填';Util::jsonExit($result);}
		if(empty($data['num'])){$result['error'] = '数量必填';Util::jsonExit($result);}
		if(!is_numeric($data['num'])){$result['error'] = '数量必须是数字';Util::jsonExit($result);}
		if(empty($data['xiangqian'])){$result['error'] = '镶嵌要求必填';Util::jsonExit($result);}
		if(!is_numeric($data['xiangkou'])){$result['error'] = '镶口必须是整数';Util::jsonExit($result);}
        if(empty($data['p_sn_out']) && $do['t_id'] == 9){$result['error'] = '外部单号必填';Util::jsonExit($result);}
        if(empty($data['ds_xiangci']) && $do['t_id'] == 9){$result['error'] = '单身-项次必填';Util::jsonExit($result);}
        if (preg_match("/[\x7f-\xff]/", $data['ds_xiangci'])) {$result['error'] = '单身-项次只能是数字+符号';Util::jsonExit($result);}
        if(!empty($data['kezi'])){ //刻字验证
            $data['kezi'] = $this->checkKeziStr($data['style_sn'],$data['kezi']);
        }
        //获取巷口
        $xiangkouList = $this->getXiangkouList($data['style_sn']);
        $err_str = '添加成功！！！<br/>';
        if(!in_array($data['xiangkou'], $xiangkouList)) $err_str.= "提示：款式：".$data['style_sn']."没有维护如下镶口（".$data['xiangkou']."）请联系款式库专员核对！";
        $model = new PurchaseGoodsModel(24);
		$res = $model->insertPurGoods($data);
		if($res != false){
			$result['success'] = 1;
            $result['msg'] = $err_str;
		}else{
			$result['error'] = "添加失败";
		}
		Util::jsonExit($result);

	}

	public function updateHasStyle(){
		$result = array('success' => 0,'error' =>'', 'msg'=>'');
		$data = $this->getSubmitDataHasStyle();
		$data['id'] = _Post::getInt('id');//必须
		
		$res = $this->checkGoodsData($data);
		if($res['success']==0){
		    $result['error'] = $res['error'];
		    Util::jsonExit($result);
		}else{
		    $data = $res['data'];
		}
		
		if(empty($data['id'])){$result['error'] = "程序异常,缺少参数";Util::jsonExit($result);}
		if(empty($data['style_sn'])){$result['error'] = '款号必填';Util::jsonExit($result);}
		if(empty($data['g_name'])){$result['error'] = '货品名称必填';Util::jsonExit($result);}
		if(empty($data['num'])){$result['error'] = '数量必填';Util::jsonExit($result);}
		if(!is_numeric($data['num'])){$result['error'] = '数量必须是数字';Util::jsonExit($result);}
		if(empty($data['xiangqian'])){$result['error'] = '镶嵌要求必填';Util::jsonExit($result);}
		if(!is_numeric($data['xiangkou'])){$result['error'] = '镶口必须是整数';Util::jsonExit($result);}
		$model =  new PurchaseGoodsModel(24);

        if(!empty($data['kezi'])){ //刻字验证

            $this->checkKeziStr($data['style_sn'],$data['kezi']);
        }
        //获取巷口
        $xiangkouList = $this->getXiangkouList($data['style_sn']);
        $err_str = '修改成功！！！<br/>';
        if(!empty($xiangkouList)){
            if(!in_array($data['xiangkou'], $xiangkouList)) $err_str.= "提示：款式：".$data['style_sn']."只能做如下镶口（".implode(",", $xiangkouList)."）<br/>";
        }else{
            $err_str.= "提示：款式：".$data['style_sn']."没有镶口<br/>";
        }

		$res = $model->updatePurGoods($data);

		if($res != false){
			$result['success'] = 1;
            $result['msg'] = $err_str;
		}else{
			$result['error'] = "修改失败";
		}
		Util::jsonExit($result);
	}

	public function getSubmitDataHasStyle(){
		$data = array();
		$data['style_sn'] = _Post::getString('style_sn');
		$data['g_name'] = _Post::getString('g_name');
		$data['num'] = _Post::getString('num');
		$data['consignee']= _Post::getString('consignee');
		$data['xiangqian'] = _Post::getString('xiangqian');
		$data['face_work'] = _Post::getString('face_work');
		$data['caizhi'] = _Post::getString('caizhi');
		$data['18k_color'] = _Post::getString('18k_color');
		$data['zhiquan'] = _Post::getString('zhiquan');
		$data['zuanshidaxiao'] = _Post::getString('zuanshidaxiao');
		$data['zhushi_num'] = _Post::getString('zhushi_num');
		$data['xiangkou'] = _Post::getString('xiangkou');
		$data['zhengshuhao'] = _Post::getString('zhengshuhao');
		$data['yanse'] = _Post::getString('yanse');
		$data['jingdu'] = _Post::getString('jingdu');
		$data['kezi'] = _Post::getString('kezi');
		$data['info'] = _Post::getString('info');
        $data['pinfo_id'] = _Post::getInt('pinfo_id');
		$data['p_sn_out'] = _Post::getString('p_sn_out');
        $data['ds_xiangci'] = _Post::getString('ds_xiangci');
        $data['pinhao'] = _Post::getString('pinhao');
        $data['cert'] = _Post::getString('cert');
		return $data;
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$pinfo_id	= $params['pinfo_id'];
		$model = new PurchaseGoodsModel($id,24);
		$res = $model->delete();
		if($res !== false){
			$attrModel =  new PurchaseGoodsAttrModel(24);
			$attrModel->delGoodsAttr($id);//删除属性数据
			$m = new PurchaseInfoModel($pinfo_id,24);
			$snum = $model ->getSum_num($pinfo_id);
			if(!$snum) $snum = 0;
			$m->setValue('p_sum',$snum);
			$m->save();
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}


	//根据款号验证是否BDD款
	public function checkStyleSn(){
		$result = array('success' => 0,'error' => '','return_msg' => '');
		if(empty($_POST['style_sn']))
		{
			$result['error'] = '款号不能为空';
			Util::jsonExit($result);
		}
		$style_sn = $_POST['style_sn'];
		$model = new CStyleModel(11);
		$styleInfo = $model->select("*","style_sn='{$style_sn}'",2,'base_style_info');
		if(empty($styleInfo)){
		    $result['error'] = '没有此款，请重新输入';
		    Util::jsonExit($result);
		}else if($styleInfo['check_status'] != 3){
			$result['error'] = '款式非已审核状态，不能进行采购。';
			Util::jsonExit($result);
		}else if($styleInfo['dismantle_status'] != 1){
			$result['error'] = '款式拆货状态非正常状态，不能进行采购。';
			Util::jsonExit($result);
		}
		$stoneList = $model->getStyleStoneByStyleSn($style_sn);
		$zhushi_num = 0;
		if(!empty($stoneList[1])){
		    $zhushiList = $stoneList[1];//主石列表
		    foreach ($zhushiList as $zhushi) {
		        $zhushi_num += $zhushi['zhushi_num'];
		    }
		}
		$data['zhushi_num'] = $zhushi_num;
		$result['success'] = 1;
		$result['content'] = $data;
		Util::jsonExit($result);
	}


	//根据产品线和款式分类查属性
	public function getAttr_product_cat($params)
	{
		$result = array('success' => 0,'error' => '','return_msg' => '');
		if($params['product_type_id'] == "" || $params['cat_type_id'] == "")
		{
			$result['error'] = '产品线和款式分类都必选';
			Util::jsonExit($result);
		}
		$model = new ApiStyleModel();
		$style_attr = $model->GetCatAttribute($params['product_type_id'],$params['cat_type_id']);
		$result['success'] = 1;
		$result['content'] = $this->fetch("purchase_style_attribute.html",array(
			'style_attr' => $style_attr,
		));
		Util::jsonExit($result);
	}

	public function downPurcaseCSV(){
        $pur_id = _Request::getInt('pur_id');
        $model = new PurchaseInfoModel($pur_id,24);
        $do = $model->getDataObject();
        $djbh_title = '';
        if($do['t_id'] == 9)
            $djbh_title = ",(*)外部单号（多行商品允许输入不同的外部单号）,(*)单身-项次（数字+符号，不能出现汉字）,品号";
		header("Content-Disposition: attachment;filename=purchase_goods_".date('Ymd').".csv");
		$title =  "(*)款号,(*)名称,(*)数量,客户名称,(*)镶嵌要求,(*)表面工艺,(*)材质,(K金*)K金可做颜色,(*戒指)指圈,(*)主石单颗重,(*)主石粒数,(*)镶口,(特定)证书号,(特定)证书类型,颜色,净度,刻字内容,布产备注".$djbh_title."\n";
		echo iconv("utf-8","gbk", $title);

	}

    /**
     *3、欧版戒刻字要求
     *50位以内的任何字符都可以刻
     *4、  非欧版戒刻字要求
     *（1）最多六位字符(一个汉字也当一个字符，)
     *（2）汉字，数字，字母（支持大小写），标点符号（中英文符号状态下都可以刻），页面显示的特殊符号
     * 标点符号包含：~ • ！@ # $ % ^ & * ( ) _ - + = { }【 】| 、 ： ；“ ”‘’ 《》 ， 。 ？ 、\  /  . < > 空格
     * @hxw
     */
    public function checkKeziStr($style_sn,$kezi)
    {
        $apiStyle = new ApiStyleModel();
        $styleAttrInfo = $apiStyle->GetStyleAttributeInfo(array('style_sn'=>$style_sn));
        $attrinfo = empty($styleAttrInfo) ? array() : $styleAttrInfo;
        //刻字验证
        $keziModel =  new PurchaseGoodsModel(24);
        $allkezi = $keziModel->getKeziData();
        //是否欧版戒 92
        if(isset($attrinfo[92]['value']) && !empty($attrinfo[92]['value']) && trim($attrinfo[92]['value'] == '是')){
            $str_count = $keziModel->pdKeziData($kezi,$allkezi,1);
            if($str_count['str_count']>=50){
                $result['error'] = "<span style='color:red';>欧版戒只能刻50位以内的任何字符！<span/>";
                Util::jsonExit($result);
            }
            $kezi = $str_count['kezi'];
        }else{
            $str_count = $keziModel->pdKeziData($kezi,$allkezi);
            if($str_count['str_count']>6){
                $result['error'] = "<span style='color:red';>非欧版戒只能刻最多6位字符！（一个汉字为一个字符）<span/>";
                Util::jsonExit($result);
            }
            if($str_count['err_bd'] != ''){
                $result['error'] = "<span style='color:red';>非欧版戒下列字符不可以刻：".$str_count['err_bd']."<span/>";
                Util::jsonExit($result);
            }
            $kezi = $str_count['kezi'];
        }
        return $kezi;
    }
}

?>
