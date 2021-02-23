<?php

/**
 *  -------------------------------------------------
 *   @file		: DiamondInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 16:13:09
 *   @update	:
 *  -------------------------------------------------
 */
class DiamondInfoController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('downLoad','dow','hbdown','changeStatus','bidump','checkAvailable', 'download_qh_templ');
    protected $code = array();
    protected $fromad_arr = array();
    protected $warehouse = array();

    public function __construct() {
        parent::__construct();
  
        $model = new DiamondInfoModel(19);
        $warehouse_arr = $model->get_warehouse_all(); 
        if($warehouse_arr['error']<=0){
            $this->code['总公司']='COM';
            $this->warehouse['COM']='总公司';
            foreach($warehouse_arr['data'] as $k=>$v){
                $this->code[$v['name']]=$v['code'];
                $this->warehouse[$v['code']]=$v['name'];
            }
        }else{
           $this->code=array();
           $this->warehouse=array();
        }
        $this->assign("warehouse",$this->code);

        $this->fromad_arr = $model->getForm_ad_only();
       
        foreach($this->fromad_arr as $k=>$v){
            $this->fromad_arrKey[$v]=$k;
        }
        $this->assign("from_ad",$this->fromad_arr);
    }

    /**
     * 	index，搜索框
     */
    public function index($params) {
        $this->render('diamond_info_search_form.html', array('bar' => Auth::getBar(),'view'=>new DiamondInfoView(new DiamondInfoModel(19))));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
       // var_dump(_REQUEST::getList('color'));die; from_ad
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'goods_sn'=>  _Request::getString('goods_sn'),
            'carat_min'=>  _Request::getFloat('carat_min'),
            'carat_max'=>  _Request::getFloat('carat_max'),
            'clarity[]'=> _Request::getList('clarity'),
            'color[]'=> _Request::getList('color'),
            'shape[]'=> _Request::getList('shape'),
            'cut[]'=> _Request::getList('cut'),
            'polish[]'=> _Request::getList('polish'),
            'symmetry[]'=> _Request::getList('symmetry'),
            'fluorescence[]'=> _Request::getList('fluorescence'),
            'cert[]'=> _Request::getList('cert'),
            'cert_id'=> _Request::getString('cert_id'),
            'good_type'=> _Request::getInt('good_type'),
			's_carats_tsyd1'=> _Request::getFloat('s_carats_tsyd1'),
            'e_carats_tsyd1'=> _Request::getFloat('e_carats_tsyd1'),
            's_carats_tsyd2'=> _Request::getFloat('s_carats_tsyd2'),
            'e_carats_tsyd2'=> _Request::getFloat('e_carats_tsyd2'),
            'kelan_price_min'=> _Request::getFloat('kelan_price_min'),
            'kelan_price_max'=> _Request::getFloat('kelan_price_max'),
            'gemx_zhengshu'=> _Request::getString('gemx_zhengshu'),
            'is_active'=> _Request::getInt('is_active'),
            'status'=> _Request::getInt('status'),
            'gm'=> _Request::getInt('gm'),
            'ysyd'=> _Request::getInt('ysyd'),
            'warehouse'=> _Request::getString('warehouse'),
            'from_ad'=> _Request::getString('from_ad'),
        );
      
        $page = _Request::getInt("page", 1);
        $where = array(
            'goods_sn'=>  _Request::getString('goods_sn'),
            'carat_min'=>  _Request::getFloat('carat_min'),
            'carat_max'=>  _Request::getFloat('carat_max'),
            'clarity'=> _Request::getList('clarity'),
            'color'=> _Request::getList('color'),
            'shape'=> _Request::getList('shape'),
            'cut'=> _Request::getList('cut'),
            'polish'=> _Request::getList('polish'),
            'symmetry'=> _Request::getList('symmetry'),
            'fluorescence'=> _Request::getList('fluorescence'),
            'cert'=> _Request::getList('cert'),
            'cert_id'=> _Request::getString('cert_id'),
            'good_type'=> _Request::getInt('good_type'),
			's_carats_tsyd1'=> _Request::getFloat('s_carats_tsyd1'),
            'e_carats_tsyd1'=> _Request::getFloat('e_carats_tsyd1'),
            's_carats_tsyd2'=> _Request::getFloat('s_carats_tsyd2'),
            'e_carats_tsyd2'=> _Request::getFloat('e_carats_tsyd2'),
            'kelan_price_min'=> _Request::getFloat('kelan_price_min'),
            'kelan_price_max'=> _Request::getFloat('kelan_price_max'),
            'gemx_zhengshu'=> _Request::getString('gemx_zhengshu'),
            'gm'=> _Request::getInt('gm'),
            'is_active'=> _Request::getInt('is_active'),
            'status'=> _Request::getInt('status'),              
            'ysyd'=> _Request::getInt('ysyd'), 
            'warehouse'=> _Request::getString('warehouse'),
            'from_ad'=> _Request::getString('from_ad'),
        );
       
        if($_SESSION['userType']!=1){
            $bumenArr = explode(",",$_SESSION['bumen']);
            if(!in_array(35,$bumenArr)){
                die("非订钻部人员不能观看裸钻列表，请邮件订钻部领导申请开通此权限，审批通过后再联系技术处理 ！");
            }            
        }
    /* if(in_array($_SESSION['userName'],array("李斌华","苏凤辉","罗芳","陈晓丹","刘梦伊","黎珊","邹燕华","叶启新","李月园","曾杰","黎海","admin","袁娟")) || $_SESSION['userType']==1){
	}else{
            die("非订钻部人员不能观看裸钻列表，请邮件订钻部领导申请开通此权限，审批通过后再联系技术处理 ！");
    } */
        $model = new DiamondInfoModel(19);
        $Shape_arr = $model->getShapeName();

        $select = "  d.`goods_id`,d.`goods_sn`,d.`mo_sn`,d.`goods_name`,d.`goods_number`,d.`good_type`,d.`market_price`,d.`shop_price`,d.`member_price`,d.`chengben_jia`,d.`is_active`,d.`carat`,d.`clarity`,d.`cut`,d.`color`,d.`shape`,d.`symmetry`,d.`polish`,d.`depth_lv`,d.`table_lv`,d.`add_time`,d.`fluorescence`,d.`from_ad`,d.`warehouse`,d.`cert`,d.`cert_id`,d.`status`,d.`gemx_zhengshu`,d.`kuan_sn`,d.`guojibaojia`,d.`cts`,d.`us_price_source`,d.`source_discount`, d.pifajia,d.img ";
        $data = $model->pageList($where, $page, 10, false,$select,1);

		$goods_list=$data['data'];
		$_goods_list=array();
		foreach($goods_list as $key => $val){
			if($val['cert']=='HRD-D' && $val['kuan_sn']!=''){
				$dia_kuan=array();
				$dia_kuan=$model->get_diamond_by_kuan_sn($val['kuan_sn']);
				if($dia_kuan){
					foreach($dia_kuan as $k => $v){
						if($v['goods_sn']!=$val['goods_sn']){
                            $val['add']=$v;
                            break;
						}
					}
				}
			}
            $_goods_list[]=$val;
		}
        
		$data['data']=$_goods_list;

        $kuan_sn=array();
        foreach($data['data'] as $key=>$val)
        {
            if($val['kuan_sn']!=''){
                if(!in_array($val['kuan_sn'],$kuan_sn)){
                    $kuan_sn[]=$val['kuan_sn'];
                }else{
                    unset($data['data'][$key]);
                }
            }
        }

        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'diamond_info_search_page';
        $this->render('diamond_info_search_list.html', array(
            'pa' => Util::page($pageData),
            'Shape_arr' => $Shape_arr,
            'page_list' => $data,
            'warehouse_arrs' => $this->warehouse,
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('diamond_info_info.html', array(
            'view' => new DiamondInfoView(new DiamondInfoModel(19))
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }

    /**
     * 	edit，渲染修改页面
     */
    public function edit($params) {
        $id = intval($params["id"]);
        $tab_id = _Post::getInt("tab_id");
        $result = array('success' => 0, 'error' => '');
        //var_dump(new DiamondInfoView(new DiamondInfoModel($id, 19)));
        $result['content'] = $this->fetch('diamond_info_info.html', array(
            'view' => new DiamondInfoView(new DiamondInfoModel($id, 19)),
            'tab_id' => $tab_id
        ));
        $result['title'] = '编辑';
        Util::jsonExit($result);
    }

    /**
     * 	show，渲染查看页面
     */
    public function show($params) {
        $id = intval($params["id"]);
        $this->render('diamond_info_show.html', array(
            'view' => new DiamondInfoView(new DiamondInfoModel($id, 19)),
            'bar' => Auth::getViewBar()
        ));
    }

    /**
     * 	insert，信息入库
     */
    public function insert($params) {
        $result = array('success' => 0, 'error' => '');
        $jiajialvModel = new DiamondJiajialvModel(20);
        $jiajialvList=$jiajialvModel->getAllList();        

        $goods_sn = _Request::getString('goods_sn');
        $warehouse = _Request::getString('warehouse');
        $goods_number = _Request::getInt('goods_number');
        $good_type = _Request::getFloat('good_type');
        $clarity = _Request::getString('clarity');
        $color = _Request::getString('color');
        $shape = _Request::getInt('shape');
        $cut = _Request::get('cut');
        $polish = _Request::get('polish');
        $symmetry = _Request::get('symmetry');
        $fluorescence = _Request::get('fluorescence');
        $carat = _Request::getFloat('carat');
        $table_lv = _Request::getFloat('table_lv');
        $depth_lv = _Request::getFloat('depth_lv');
        $from_ad = _Request::getInt('from_ad');
        $chengben_jia = _Request::getFloat('chengben_jia');
        $market_price = _Request::getFloat('market_price');
        $source_discount = _Request::getFloat('source_discount');
        $us_price_source = _Request::getFloat('us_price_source');
        $guojibaojia = _Request::getFloat('guojibaojia');
        $cts = _Request::getFloat('cts');
        $is_active = _Request::getInt('is_active');
        $status = _Request::getInt('status');
        $gemx_zhengshu = _Request::get('gemx_zhengshu');
        $cert_id = _Request::get('cert_id');
        $cert = _Request::get('cert');
        $kuan_sn = _Request::get('kuan_sn');

        $olddo = array();
        $newdo = array();
        $newdo['goods_sn'] = $goods_sn;
        $newdo['warehouse'] = $warehouse;
        $newdo['goods_number'] = $goods_number;
        $newdo['good_type'] = $good_type;
        $newdo['clarity'] = $clarity;
        $newdo['color'] = $color;
        $newdo['shape'] = $shape;
        $newdo['cut'] = $cut;
        $newdo['polish'] = $polish;
        $newdo['symmetry'] = $symmetry;
        $newdo['fluorescence'] = $fluorescence;
        $newdo['carat'] = $carat;
        $newdo['table_lv'] = $table_lv;
        $newdo['depth_lv'] = $depth_lv;
        $newdo['from_ad'] = $from_ad;
        $newdo['chengben_jia'] = $chengben_jia;
        $newdo['is_active'] = $is_active;
        $newdo['status'] = $status;
        $newdo['gemx_zhengshu'] = $gemx_zhengshu;
        $newdo['cert_id'] = $cert_id;
        $newdo['cert'] = $cert;
        $newdo['market_price'] = $market_price;
        $newdo['shop_price'] = $market_price;
        $newdo['member_price'] = $market_price;
        $newdo['source_discount'] = $source_discount;
        $newdo['us_price_source'] = $us_price_source;
        $newdo['guojibaojia'] = $guojibaojia;
        $newdo['cts'] = $cts;
        $newdo['add_time'] = date("Y-m-d H:i:s");
		$newdo['kuan_sn'] = $kuan_sn;
        $newdo['goods_name'] = $newdo["carat"]."克拉/ct ".$newdo["clarity"]."净度 ".$newdo["color"]."颜色 ".$newdo["cut"]."切工";

        $newmodel = new DiamondInfoModel(20);
        
        $getRowDiamondBygoods_sn=$newmodel->getRowBygoods_sn($newdo['goods_sn']);
        if($getRowDiamondBygoods_sn){
            $result['error'] = '该商品编码已存在！';
            Util::jsonExit($result);
        }
        $getRowDiamondBycert_id=$newmodel->getRowBycert_id($newdo['cert_id']);
        if($getRowDiamondBycert_id){
            $result['error'] = '该证书号已存在！';
            Util::jsonExit($result);
        }

        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
            //AsyncDelegate::dispatch('task', array('event' => 'dia_upserted', 'cert_id' => $cert_id));
        } else {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	update，更新信息
     */
    public function update($params) {
        $result = array('success' => 0, 'error' => '');
        $jiajialvModel = new DiamondJiajialvModel(20);
        $jiajialvList=$jiajialvModel->getAllList();

        $_cls = _Post::getInt('_cls');
        $tab_id = _Post::getInt('tab_id');
        $id = _Post::getInt('goods_id');
        $goods_sn = _Request::getString('goods_sn');
        $warehouse = _Request::getString('warehouse');
        $goods_number = _Request::getInt('goods_number');
        $good_type = _Request::getFloat('good_type');
        $clarity = _Request::getString('clarity');
        $color = _Request::getString('color');
        $shape = _Request::getInt('shape');
        $cut = _Request::get('cut');
        $polish = _Request::get('polish');
        $symmetry = _Request::get('symmetry');
        $fluorescence = _Request::get('fluorescence');
        $carat = _Request::get('carat');
        $table_lv = _Request::get('table_lv');
        $depth_lv = _Request::get('depth_lv');
        $from_ad = _Request::get('from_ad');
        $chengben_jia = _Request::get('chengben_jia');
        $market_price = _Request::get('market_price');
        $source_discount = _Request::getFloat('source_discount');
        $us_price_source = _Request::getFloat('us_price_source');
        $guojibaojia = _Request::getFloat('guojibaojia');
        $cts = _Request::getFloat('cts');
        $is_active = _Request::getInt('is_active');
        $status = _Request::getInt('status');
        $gemx_zhengshu = _Request::get('gemx_zhengshu');
        $cert_id = _Request::get('cert_id');
        $cert = _Request::get('cert');
        $kuan_sn = _Request::get('kuan_sn');
        $old_goods_sn = _Request::get('old_goods_sn');
        $old_cert_id = _Request::get('old_cert_id');


        $newmodel = new DiamondInfoModel($id, 20);

        $olddo = $newmodel->getDataObject();
        $newdo = array();
        $newdo['goods_id'] = $id;
        $newdo['goods_sn'] = $goods_sn;
        $newdo['warehouse'] = $warehouse;
        $newdo['goods_number'] = $goods_number;
        $newdo['good_type'] = $good_type;
        $newdo['clarity'] = $clarity;
        $newdo['color'] = $color;
        $newdo['shape'] = $shape;
        $newdo['cut'] = $cut;
        $newdo['polish'] = $polish;
        $newdo['symmetry'] = $symmetry;
        $newdo['fluorescence'] = $fluorescence;
        $newdo['carat'] = $carat;
        $newdo['table_lv'] = $table_lv;
        $newdo['depth_lv'] = $depth_lv;
        $newdo['from_ad'] = $from_ad;
        $newdo['chengben_jia'] = $chengben_jia;
        $newdo['market_price'] = $market_price;
        $newdo['shop_price'] = $market_price;
        $newdo['member_price'] = $market_price;
        $newdo['source_discount'] = $source_discount;
        $newdo['us_price_source'] = $us_price_source;
        $newdo['guojibaojia'] = $guojibaojia;
        $newdo['cts'] = $cts;
        $newdo['is_active'] = $is_active;
        $newdo['status'] = $status;
        $newdo['gemx_zhengshu'] = $gemx_zhengshu;
        $newdo['cert_id'] = $cert_id;
        $newdo['cert'] = $cert;
		$newdo['kuan_sn'] = $kuan_sn;
		$newdo['goods_name'] = $newdo["carat"]."克拉/ct ".$newdo["clarity"]."净度 ".$newdo["color"]."颜色 ".$newdo["cut"]."切工";

        $getRowDiamondBygoods_sn=$newmodel->getRowBygoods_sn($newdo['goods_sn']);
        if($getRowDiamondBygoods_sn&&$getRowDiamondBygoods_sn['goods_sn']!=$old_goods_sn){
            $result['error'] = '该商品编码已存在！';
            Util::jsonExit($result);
        }
        $getRowDiamondBycert_id=$newmodel->getRowBycert_id($newdo['cert_id']);
        if($getRowDiamondBycert_id&&$getRowDiamondBycert_id['cert_id']!=$old_cert_id){
            $result['error'] = '该证书号已存在！';
            Util::jsonExit($result);
        }        

        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
            $result['_cls'] = $_cls;
            $result['tab_id'] = $tab_id;
            $result['title'] = '修改此处为想显示在页签上的字段';
            //AsyncDelegate::dispatch('task', array('event' => 'dia_upserted', 'cert_id' => $cert_id, 'create_user' => $_SESSION['userName']));
        } else {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	delete，删除
     */
    public function delete($params) {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new DiamondInfoModel($id, 20);
        $do = $model->getDataObject();
        if( $do['status']== 2){
            $result['error'] = "此记录状态为下架";
            Util::jsonExit($result); 
        }
        //$status = $do['status']==1?0:1;
        $model->setValue('status', 2 );
        $res = $model->save(true);
        //联合删除？
        //$res = $model->delete();
        if ($res !== false) {

            $diamondLogModel = new DiamondInfoLogModel(20);
            $datas = array(
                'from_ad' => 2,
                'cert_id'=>$do['cert_id'],
                'operation_type' => '6',
                'operation_content' => '下架操作',
                'create_time' => date("Y-m-d H:i:s"),
                'create_user' => $_SESSION['userName'],
            );

            $diamondLogModel->saveData($datas, array());
            $result['success'] = 1;
        } else {
            $result['error'] = "删除失败";
        }
        Util::jsonExit($result);
    }
    
   /**
     * 	enable，启用
     */
    public function enable($params) {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new DiamondInfoModel($id, 20);
        $do = $model->getDataObject();
        if( $do['status']== 1){
            $result['error'] = "此记录状态为上架";
            Util::jsonExit($result); 
        }
        //$status = $do['status']==1?0:1;
        $model->setValue('status', 1);
        $res = $model->save(true);
        
        //$res = $model->delete();
        if ($res !== false) {
            $diamondLogModel = new DiamondInfoLogModel(20);
            $datas = array(
                'from_ad' => 2,
                'cert_id' => $do['cert_id'],
                'operation_type' => '5',
                'operation_content' => '上架操作',
                'create_time' => date("Y-m-d H:i:s"),
                'create_user' => $_SESSION['userName'],
            );

            $diamondLogModel->saveData($datas, array());
            $result['success'] = 1;
        } else {
            $result['error'] = "删除失败";
        }
        Util::jsonExit($result);
    }

    /**
     * 	upload，批量上传裸钻
     */
    public function upload() {
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('diamond_info_upload.html', array(
            'view' => new DiamondInfoView(new DiamondInfoModel(19))
        ));
        $result['title'] = '批量上传';
        Util::jsonExit($result);
    }

   /**
     * 	upload_ins，批量上传裸钻
     */
    public function upload_ins() {
        ini_set('memory_limit','6000M');
        set_time_limit(0);
        $result = array('success' => 0, 'error' => '');
		$upload_name = $_FILES;
        if (!$upload_name) {
            $result['error'] = '文件不能为空';
            Util::jsonExit($result);
        }		
        if (Upload::getExt($upload_name['file_price']['name']) != 'csv') {
            $result['error'] = '请上传csv格式文件';
            Util::jsonExit($result);
        }

		$file = fopen($upload_name['file_price']['tmp_name'], 'r');
        while ($data = fgetcsv($file)) {
			foreach($data as $k => $v){
				$data[$k] = iconv("GBK","UTF-8",$v);
			}
			$data_r[]=$data;
        }

        $model = new DiamondInfoModel(20);
        $jiajialvModel = new DiamondJiajialvModel(20);
        $zuanshi_list = $model->getAlls("`goods_sn`,`cert`,`cert_id`");	
		$get_dia_all=array();
        $get_goods_sn_all=array();
		foreach($zuanshi_list as $k=>$v){
			$get_dia_all[]=$v['cert'].$v['cert_id'];
            $get_goods_sn_all[]=$v['goods_sn'];
		}
		$header_target="商品编码,仓库,商品数量,石重,净度,颜色,形状,切工,抛光,对称,荧光,证书类型,证书号,gemx证书号,台宽,台深,成本价,货品类型(1=》现货，2=》期货),来源,状态(1=》上架，2=》下架),是否为活动（双11等活动）(1=>正常，2=》活动),天生一对,源折扣,美元价,国际报价";
		$header=implode(',',array_shift($data_r));

		if($header != $header_target){
			$result['error'] = '表头出错';
			Util::jsonExit($result);
		}

        $jiajialvList=$jiajialvModel->getAllList();

		foreach($data_r as $k=>$v){
            $v = array_map('trim',$v);
			if($v[0]==''){
				$result['error'] = '第'.($k+2).'行商品编号为空';
				Util::jsonExit($result);
			}elseif(strstr($v[0],'.')&&strstr($v[0],'E+')){
				$result['error'] = '第'.($k+2).'行商品编号格式不对，请重新填写！';
				Util::jsonExit($result);                
            }
			if($v[1]==''){
				$result['error'] = '第'.($k+2).'行库房为空';
				Util::jsonExit($result);
			}elseif(!in_array($v[1],$this->warehouse)){
				$result['error'] = '第'.($k+2).'行库房不存在';
				Util::jsonExit($result);            
            }
			if($v[11]==''){
				$result['error'] = '第'.($k+2).'行证书类型为空';
				Util::jsonExit($result);
			}
			if($v[12]==''){
				$result['error'] = '第'.($k+2).'行证书号为空';
				Util::jsonExit($result);
			}elseif(strstr($v[12],'.')&&strstr($v[12],'E+')){
				$result['error'] = '第'.($k+2).'行证书号格式不对，请重新填写！';
				Util::jsonExit($result);                
            }
			if(!in_array($v[18],$this->fromad_arr)){
				$result['error'] = '第'.($k+2).'行来源不存在';
				Util::jsonExit($result);
			}
			if($v[3]<=0){
				$result['error'] = '第'.($k+2).'行石重错误';
				Util::jsonExit($result);
			}
			if($v[16]<=0){
				$result['error'] = '第'.($k+2).'行成本价错误';
				Util::jsonExit($result);
			}
			if(!in_array($v[17],array('1','2'))){
				$result['error'] = '第'.($k+2).'行货品类型错误';
				Util::jsonExit($result);
			}
			if(!in_array($v[19],array('1','2'))){
				$result['error'] = '第'.($k+2).'行状态错误';
				Util::jsonExit($result);
			}
			if(!in_array($v[20],array('1','2'))){
				$result['error'] = '第'.($k+2).'行是否为活动错误';
				Util::jsonExit($result);
			}
			if($v[11]=='HRD-D' && $v[21]==''){
				$result['error'] = '第'.($k+2).'行不是天生一对';
				Util::jsonExit($result);
            }
			$shape = $v[6];
			$cut = $v[7];
			$color = $v[5];
			$clarity = $v[4];
			$polish = $v[8];
			$fluorescence = $v[10];
			$symmetry = $v[9];
			$cert = $v[11];
			$good_type = $v[17];

            //$getRowDiamondBygoods_sn=$model->getRowBygoods_sn($v[0]);
            //if($getRowDiamondBygoods_sn){
                //$result['error'] = '第'.($k+2).'行商品编码已存在！';
                //Util::jsonExit($result);
            //}
            //$getRowDiamondBycert_id=$model->getRowBycert_id($v[12]);
            //if($getRowDiamondBycert_id){
                //$result['error'] = '第'.($k+2).'行证书号已存在！';
                //Util::jsonExit($result);
            //}

            list($error,$error_msg) = $model->checkDiamond($shape,$cut,$color,$clarity,$polish,$fluorescence,$symmetry,$cert);
            if($error == 1){
				$result['error'] = '第'.($k+2).'行'.implode(',',$error_msg)."不符合要求";
				Util::jsonExit($result);
            }
            $carat = $v[3];
			$from_ad  = $this->fromad_arrKey[$v[18]];
			$status = $v[19];

            $jiajialv=$jiajialvModel->getJiajialv($jiajialvList,$cert,$carat,$from_ad,$status,$good_type);
            if(!$jiajialv){
				$result['error'] = '第'.($k+2).'行加价率不存在!';
				Util::jsonExit($result);
            }
            $data_r[$k]['jiajialv']=$jiajialv;
		}
        if(!$data_r){
            $result['error'] = '无信息上传!';
            Util::jsonExit($result);
        }

		foreach($data_r as $k=>$v){
			if(in_array($v[11].$v[12],$get_dia_all)){
				$model->deletebycert_id($v[11],$v[12]);//删除重复	
			}
            if(in_array($v[0],$get_goods_sn_all)){
                $model->deletebygoods_sn($v[0]);//删除重复   
            }
			$olddo=array();
			$newdo = array();
			$newdo['goods_sn'] = $v[0];
			$newdo['warehouse'] = $this->code[$v[1]];
			$newdo['goods_name'] = $v[3]."克拉/ct ".$v[4]."净度 ".$v[5]."颜色 ".$v[7]."切工";
			$newdo['goods_number'] = $v[2];
			$newdo['good_type'] = $v[17];
			$newdo['clarity'] = $v[4];
			$newdo['color'] = $v[5];
			$newdo['shape'] = $model->getShapeId($v[6]);
			$newdo['cut'] = $v[7];
			$newdo['polish'] = $v[8];
			$newdo['symmetry'] = $v[9];
			$newdo['fluorescence'] = $v[10];
			$newdo['carat'] = $v[3];
			$newdo['table_lv'] = $v[14];
			$newdo['depth_lv'] = $v[15];
			$newdo['from_ad'] = $this->fromad_arrKey[$v[18]];
			$newdo['chengben_jia'] = $v[16];
			$newdo['market_price'] = round($v[16]*$v['jiajialv']);
			$newdo['shop_price'] = round($v[16]*$v['jiajialv']);
			$newdo['member_price'] = round($v[16]*$v['jiajialv']);
			$newdo['is_active'] = $v[20];
			$newdo['status'] = $v[19];
			$newdo['gemx_zhengshu'] = $v[13];
			$newdo['cert_id'] = $v[12];
			$newdo['cert'] = $v[11];
			$newdo['add_time'] = date("Y-m-d H:i:s");
            $newdo['is_active']=$v[20]==1?1:2;
            $newdo['kuan_sn']=$v[21];
            $newdo['source_discount']=$v[22];
            $newdo['us_price_source']=$v[23];
            $newdo['guojibaojia']=$v[24];

			$res = $model->saveData($newdo, $olddo);
		}
		if ($res !== false) {
			$result['success'] = 1;
			//AsyncDelegate::dispatch('task', array('event' => 'dia_upserted'));
		} else {
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
    }

	/**
	 *	downLoad，下载
	 */
	public function downLoad ()
	{
        set_time_limit(0);
        ini_set('memory_limit','2000M');
		$where = array(
            'goods_sn'=>  _Request::getString('goods_sn'),
            'carat_min'=>  _Request::getFloat('carat_min'),
            'carat_max'=>  _Request::getFloat('carat_max'),
            'clarity'=> _Request::getList('clarity'),
            'color'=> _Request::getList('color'),
            'shape'=> _Request::getList('shape'),
            'cut'=> _Request::getList('cut'),
            'polish'=> _Request::getList('polish'),
            'symmetry'=> _Request::getList('symmetry'),
            'fluorescence'=> _Request::getList('fluorescence'),
            'cert'=> _Request::getList('cert'),
            'cert_id'=> _Request::getInt('cert_id'),
            'good_type'=> _Request::getInt('good_type'),
			's_carats_tsyd1'=> _Request::getFloat('s_carats_tsyd1'),
            'e_carats_tsyd1'=> _Request::getFloat('e_carats_tsyd1'),
            's_carats_tsyd2'=> _Request::getFloat('s_carats_tsyd2'),
            'e_carats_tsyd2'=> _Request::getFloat('e_carats_tsyd2'),
            'kelan_price_min'=> _Request::getFloat('kelan_price_min'),
            'kelan_price_max'=> _Request::getFloat('kelan_price_max'),
            'gemx_zhengshu'=> _Request::getString('gemx_zhengshu'),
            'gm'=> _Request::getInt('gm'),
            'is_active'=> _Request::getInt('is_active'),
            'status'=> _Request::getInt('status'),              
            'ysyd'=> _Request::getInt('ysyd'), 
            'warehouse'=> _Request::getString('warehouse'),
            'from_ad'=> _Request::getString('from_ad'),
		);

		$model = new DiamondInfoModel(19);
        $getShapeName=$model->getShapeName();

            $pageSize = 5000;
            $page=1;
            $datalist=array();
            while(true){
                $start=($page-1)*$pageSize;
                $tmp=$model->getDiamond_all($where, $start, $pageSize);
                if(isset($tmp)&&empty($tmp)){
                    break;
                    exit;
                }
                $page++;
                foreach($tmp as $k=>$v){
                    $datalist[]=$v; 
                }
            }

            $datalists=array();
            foreach($datalist as $k=>$v){
                $datalists[$k]['goods_sn']=$v['goods_sn']?$v['goods_sn']:'';
                $datalists[$k]['goods_name']=$v["carat"]."克拉/ct ".$v["clarity"]."净度 ".$v["color"]."颜色 ".$v["cut"]."切工";
                $datalists[$k]['goods_number']=$v['goods_number']?$v['goods_number']:'';
                $datalists[$k]['shape']=isset($getShapeName[$v['shape']])?$getShapeName[$v['shape']]:'';
                $datalists[$k]['carat']=$v['carat']?$v['carat']:'';
                $datalists[$k]['color']=$v['color']?$v['color']:'';
                $datalists[$k]['clarity']=$v['clarity']?$v['clarity']:'';
                $datalists[$k]['cut']=$v['cut']?$v['cut']:'';
                $datalists[$k]['symmetry']=$v['symmetry']?$v['symmetry']:'';
                $datalists[$k]['polish']=$v['polish']?$v['polish']:'';
                $datalists[$k]['fluorescence']=$v['fluorescence']?$v['fluorescence']:'';
                $datalists[$k]['market_price']=$v['market_price']?$v['market_price']:'';
                $datalists[$k]['shop_price']=$v['shop_price']?$v['shop_price']:'';
                $datalists[$k]['chengben_jia']=$v['chengben_jia']?$v['chengben_jia']:'';
                $datalists[$k]['pifajia']=$v['pifajia']?$v['pifajia']:'';
                $datalists[$k]['source_discount']=$v['source_discount']?$v['source_discount']:'';
                $datalists[$k]['from_ad']=isset($this->fromad_arr[$v['from_ad']])?$this->fromad_arr[$v['from_ad']]:'';
                $datalists[$k]['warehouse']=isset($v['warehouse'])?$v['warehouse']:'';
                $datalists[$k]['cert']=$v['cert']?$v['cert']:'';
                $datalists[$k]['cert_id']=$v['cert_id']?$v['cert_id']:'';
                $datalists[$k]['is_active']=$v['is_active']==1?"正常":"活动";
                $datalists[$k]['status']=$v['status']==1?"上架":"下架";
                $datalists[$k]['kuan_sn']=$v['kuan_sn']?$v['kuan_sn']:'';
                $datalists[$k]['mo_sn']=$v['mo_sn']?$v['mo_sn']:'';
                $datalists[$k]['gemx_zhengshu']=$v['gemx_zhengshu']?$v['gemx_zhengshu']:'';
            }
        //}
        $title = array(
				'商品编码',
				'商品名称',
				'商品数量',
				'形状',
				'石重',
				'颜色',
				'净度',
				'切工',
				'对称',
				'抛光',
				'荧光',
				'市场价',
				'BDD价',
				'成本价',
                '展厅批发价',
                '源折扣',
				'来源',
				'库房',
				'证书号类型',
				'证书号',
				'是否活动(1正常,2活动)',
				'状态',
				'天生一对',
				'模号',
                '星耀证书号');
            
            Util::downloadCsv("裸钻列表",$title,$datalists);
	}

	/**
	 *	dow，模板
	 */
	public function dow ()
	{
        $title = array(
				'商品编码',
				'仓库',
				'商品数量',
				'石重',
				'净度',
				'颜色',
				'形状',
				'切工',
				'抛光',
				'对称',
				'荧光',
				'证书类型',
				'证书号',
				'gemx证书号',
				'台宽',
                '台深',
                '成本价',
                '货品类型(1=》现货，2=》期货)',
                '来源',
                '状态(1=》上架，2=》下架)',
                '是否为活动（双11等活动）(1=>正常，2=》活动)',
                '天生一对',
                '源折扣',
                '美元价',
                '国际报价');
			$newdo = array();
			$newdo[0]['goods_sn'] = '1211044277';
			$newdo[0]['warehouse'] = '总公司后库';
			$newdo[0]['goods_number'] = 1;
			$newdo[0]['good_type'] = 0.23;
			$newdo[0]['clarity'] = 'VVS2';
            $newdo[0]['cert_id'] = 'H';
            $newdo[0]['active']='圆形';
			$newdo[0]['color'] = 'EX';
			$newdo[0]['shape'] = 'EX';
			$newdo[0]['cut'] ='EX';
			$newdo[0]['polish'] = 'N';
			$newdo[0]['symmetry'] = 'GIA';
			$newdo[0]['fluorescence'] = '6145280696';
			$newdo[0]['carat'] = '';
			$newdo[0]['table_lv'] = '';
			$newdo[0]['depth_lv'] = '';
			$newdo[0]['from_ad'] = '57932';
			$newdo[0]['chengben_jia'] = 1;
			$newdo[0]['is_active'] = "BDD";
			$newdo[0]['status'] = 1;
			$newdo[0]['gemx'] = 1;
			$newdo[0]['kuan_sn'] = 'DB001';
            $newdo[0]['source_discount'] = '32';
            $newdo[0]['us_price_source'] = '10000';
            $newdo[0]['guojibaojia'] = '10000';
            Util::downloadCsv("裸钻列表",$title,$newdo);
    }

	/**
	 *	del，删除一个
	 */
	public function del ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new DiamondInfoModel($id,20);
		$do = $model->getDataObject();
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	delMany，删除多个
	 */
	public function delMany ($params)
	{
		$result = array('success' => 0,'error' => '');
		$goods_id = _Request::getList('_ids');
		$model = new DiamondInfoModel(20);
		$res = $model->delManyDelete($goods_id);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
	
	/*婚博会专用裸钻导出*/
	public function hbdown()
	{
		set_time_limit(0);
        ini_set('memory_limit','2000M');
		$where = array(
            'goods_sn'=>  _Request::getString('goods_sn'),
            'carat_min'=>  _Request::getFloat('carat_min'),
            'carat_max'=>  _Request::getFloat('carat_max'),
            'clarity'=> _Request::getList('clarity'),
            'color'=> _Request::getList('color'),
            'shape'=> _Request::getList('shape'),
            'cut'=> _Request::getList('cut'),
            'polish'=> _Request::getList('polish'),
            'symmetry'=> _Request::getList('symmetry'),
            'fluorescence'=> _Request::getList('fluorescence'),
            'cert'=> _Request::getList('cert'),
            'cert_id'=> _Request::getInt('cert_id'),
            'good_type'=> _Request::getInt('good_type'),
			's_carats_tsyd1'=> _Request::getFloat('s_carats_tsyd1'),
            'e_carats_tsyd1'=> _Request::getFloat('e_carats_tsyd1'),
            's_carats_tsyd2'=> _Request::getFloat('s_carats_tsyd2'),
            'e_carats_tsyd2'=> _Request::getFloat('e_carats_tsyd2'),
            'kelan_price_min'=> _Request::getFloat('kelan_price_min'),
            'kelan_price_max'=> _Request::getFloat('kelan_price_max'),
            'gemx_zhengshu'=> _Request::getString('gemx_zhengshu'),
            'gm'=> _Request::getInt('gm'),
            'is_active'=> _Request::getInt('is_active'),
            'status'=> _Request::getInt('status'),              
            'ysyd'=> _Request::getInt('ysyd'), 
            'warehouse'=> _Request::getString('warehouse'),
            'from_ad'=> _Request::getString('from_ad'),
		);
		$model = new DiamondInfoModel(19);
        $getShapeName=$model->getShapeName();
        $pageSize = 5000;
        $page=1;
		$datalist=array();
		while(true)
		{
			$start=($page-1)*$pageSize;
			$tmp=$model->getDiamond_all($where, $start, $pageSize);
			if(isset($tmp)&&empty($tmp)){
				break;
				exit;
			}
			$page++;
			foreach($tmp as $k=>$v){
				$tmparr = array();
				$tmparr['goods_sn'] = $v['goods_sn']?$v['goods_sn']:'';
				$tmparr['warehouse'] = isset($v['warehouse'])?$v['warehouse']:'';
				$tmparr['shape']= isset($getShapeName[$v['shape']]) ? $getShapeName[$v['shape']]:'';
				$tmparr['carat']=$v['carat']?$v['carat']:'';
                $tmparr['color']=$v['color']?$v['color']:'';
                $tmparr['clarity']=$v['clarity']?$v['clarity']:'';
                $tmparr['cut']= $v['cut']?str_replace("好","G",$v['cut']):'';
				$tmparr['symmetry']=$v['symmetry']?$v['symmetry']:'';
                $tmparr['polish']=$v['polish']?$v['polish']:'';
                $tmparr['fluorescence']=$v['fluorescence']?$v['fluorescence']:'';
				$tmparr['cert']=$v['cert']?$v['cert']:'';
                $tmparr['cert_id']=$v['cert_id']?$v['cert_id']:'';
				$tmparr['chengben_jia']=$v['chengben_jia']?$v['chengben_jia']:'';
				$tmparr['gemx_zhengshu']=$v['gemx_zhengshu']?$v['gemx_zhengshu']:'';
				$tmparr['kuan_sn']=$v['kuan_sn']?$v['kuan_sn']:'';
				array_push($datalist,$tmparr);
			}
		}
		$title = array(
				'货号','库房','形状','石重','颜色','净度','切工','对称','抛光','荧光',
				'证书','证书号','成本价格','星耀证书号','款号'
		);
		Util::downloadCsv("婚博会裸钻列表",$title,$datalist);
	}

    /*
    下载裸钻数据
    */
    public function bidump() {
        if (!$this->signCallAuth('_duMp-4_bIdiAm__')) {
            Util::jsonExit(array('error' => 'deny!', 'success'=>0,'code'=>1));
        }

        $date = date('Y-m-d-H');
        $file = KELA_ROOT.'/cron/diamond/dump/dia_'.$date.'.sql';
        if(!file_exists($file_name)){
            if(!is_dir(dirname($file))){
                mkdir(dirname($file));
            }
            $sql_tmpl = "mysqldump -u %s -h %s --password=%s --skip-add-locks --add-drop-table -B %s --tables %s --where=%s > %s";
            $cmd = sprintf($sql_tmpl, 'cuteman', '192.168.1.59', 'QW@W#RSS33#E#',
                'front',
                'diamond_info',
                'from_ad!=1',
                $file
            );
            file_put_contents(dirname($file).'/dia.log', date("Y-m-d H:i:s").' - dump...'.PHP_EOL, FILE_APPEND);
            exec($cmd);
            
            $sql_tmpl = "mysqldump -u %s -h %s --password=%s --skip-add-locks --add-drop-table -B %s --tables %s >> %s";
            $cmd = sprintf($sql_tmpl, 'cuteman', '192.168.1.59', 'QW@W#RSS33#E#',
                'front',
                'diamond_price',
                $file
            );
            
            exec($cmd);
            
            file_put_contents(dirname($file).'/dia.log', date("Y-m-d H:i:s").' - done!!!'.PHP_EOL, FILE_APPEND);
        }
        
        if (file_exists($file)) {
            header('Content-Description: File Transfer');  
            header('Content-Type: application/octet-stream');  
            header('Content-Disposition: attachment; filename='.basename($file));  
            header('Content-Transfer-Encoding: utf8');  
            header('Expires: 0');  
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');  
            header('Pragma: public');  
            ob_clean();  
            flush();  
            readfile($file);  
            exit;  
        } else{
            Util::jsonExit(array('error' => "dia_".$date.".sql not find!", 'success'=>0,'code'=>2));
        }
    }

    /*
    修改裸钻上下架状态
    */
    public function changeStatus() {
        $result = array (
            'success' => 0,
            'error' => '',
            'result' => array(),
        );

        if (!$this->signCallAuth('_chSt2aUs-bY_bIdiAm__')) {
            $result['error'] = '非法的调用';
            Util::jsonExit($result);
        }

        $type = _Request::getString('type','');
        if($type != 1 && $type != 2){
            $result['error'] = '上下架类型不可用!';
            Util::jsonExit($result);
        }

        $certNo = _Request::getString('certNo','');
        if(empty($certNo)){
            $result['error'] = '证书号不能为空!';
            Util::jsonExit($result);
        }
        $certList = explode(',',$certNo);
        $diamondModel = new DiamondInfoModel(19);
        $result['result'] = $diamondModel->offlineDiamond($type,$certList);
        $result['success'] = 1;
        Util::jsonExit($result);
    }

    /*
    检查指定证书号钻石上下架是否可用
    */
    public function checkAvailable() {
        $result = array (
            'success' => 0,
            'error' => '',
            'result' => array(),
        );
        
        if (!$this->signCallAuth('_chkA3vIlblE-bY_bIdiAm__')) {
            $result['error'] = '非法的调用';
            Util::jsonExit($result);
        }

        $type = _Request::getString('type','');
        $certNo = _Request::getString('certNo','');
        if($type != 1 && $type != 2){
            $result['error'] = '检查类型不可用!';
            Util::jsonExit($result);
        }
        if(empty($certNo)){
            $result['error'] = '证书号不能为空!';
            Util::jsonExit($result);
        }
        $certList = explode(',',$certNo);

        $diamondModel = new DiamondInfoModel(19);
        $diamondInfo = $diamondModel->getDiamondInfo($certList);
        if(empty($diamondInfo)){
            $result['error'] = '证书号不存在!';
            Util::jsonExit($result);
        }

        $diamondResult = [];
        foreach($diamondInfo as $diamondItem){
            $diamondResult[$diamondItem['cert_id']] = $diamondItem;
        }

        $checkResult = [];
        foreach($certList as $certItem){
            if(!isset($diamondResult[$certItem]) || empty($diamondResult[$certItem])){
                $checkResult[$certItem] = 0;
                continue;
            }
            $diamondItemInfo = $diamondResult[$certItem];
            $status = 0;
            //status 1上架 2下架
            if(($type == 1 && $diamondItemInfo['status'] == 2) || ($type == 2 && $diamondItemInfo['status'] == 1)){
                $status = 1;
            }
            $checkResult[$certItem] = $status;
        }
        $result['success'] = 1;
        $result['result'] = $checkResult;
        Util::jsonExit($result);
    }

    private function signCallAuth($private_key) {
        $api_key = _Request::getString('api_key');
        if (empty($api_key)) {
	        return false;
        }       

        $date = date('Y-m-d');
        $hash_key = md5(strrev($date.$private_key));
        if (strcmp($hash_key, $api_key) !== 0) {
	        return false;
        }

        return true;           
    }

    public function download_qh_templ() {
        $title = array(
            '货号',
            '形状',
            '石重',
            '颜色',
            '净度',
            '切工',
            '抛光',
            '对称',
            '荧光',
            '证书类型',
            '证书号',
            '国际报价',
            '折扣率',
            '美元价',
            '名义成本',
            '零售价',
            'gemx证书号');
        $newdo = array();
        Util::downloadCsv("qihuo_diamond",$title,$newdo);
    }
    
    public function manual_upload_qh() {
        $result = array('success' => 0, 'error' => '');
        if ( _Request::getString("mtype") == '') {
            $result['content'] = $this->fetch('diamond_info_upload_qh.html', array(
                'view' => new DiamondInfoView(new DiamondInfoModel(19))
            ));
            $result['title'] = '上传期货';
            Util::jsonExit($result);
        }
      
        $from_ad = _Request::getString('from_ad');
        if (!in_array($from_ad, array('CS', 'JP', 'JB', 'RA','KI', 'Sunrise', 'FD','HA','Venus'))) {
            $result['error'] = '该供应商暂不支持';
            Util::jsonExit($result);
        }
                
        ini_set('memory_limit','6000M');
        set_time_limit(0);

        $upload_name = $_FILES;
        if (!$upload_name) {
            $result['error'] = '文件不能为空';
            Util::jsonExit($result);
        }
    
        if (Upload::getExt($upload_name['qihuo_file']['name']) != 'csv') {
            $result['error'] = '请上传csv格式文件';
            Util::jsonExit($result);
        }
    
        $file = fopen($upload_name['qihuo_file']['tmp_name'], 'r');
        while ($data = fgetcsv($file)) {
            foreach($data as $k => $v){
                $val = iconv("GBK","UTF-8",$v);
                $data[$k] = $this->resolve_qi_fields($val, $k);
            }

            $data_r[]=$data;
        }

        if (!isset($data_r) || count($data_r) <= 1) {
            $result['error'] = '数据为空';
            Util::jsonExit($result);
        } else {
            $model = new DiamondInfoModel(20);
            $resp = $model->transfer_qihuo($data_r, $from_ad);
            if ($resp === true) {
                //AsyncDelegate::dispatch("task", array('event' => 'dia_upserted'));
                $result['success'] = 1;
            } else {
                $result['error'] = $resp;
            }
            
            Util::jsonExit($result);
        }
    }
    
    private function resolve_qi_fields($col, $idx) {
        $col = strtoupper($col);
        if ($idx == 1) {
            //shape
            if (in_array(trim($col), array('ROUND','RB','RD','BR', '圆形'))) {
                return 1;
            } else if (in_array(trim($col), array('PRINCESS','PR'))) {
                return 2;
            } else if (in_array(trim($col), array('EM','ASH'))) {
                return 3;
            } else if (in_array(trim($col), array('OL','OV'))) {
                return 5;
            } else if (in_array(trim($col), array('PEAR','PE','PS'))) {
                return 6;
            } else if (in_array(trim($col), array('HT','HEART','HRT'))) {
                return 7;
            } else if (in_array(trim($col), array('CU','CUSHION','CUMBR'))) {
                return 8;
            } else if (in_array(trim($col), array('SQEM', 'SQ EMERALD','SQEMERALD'))) {
                return 11;
            } else if (in_array(trim($col), array('TR'))) {
                return 12;
            } else if (in_array(trim($col), array('MQ', 'MARQUISE'))) {
                return 17;
            } else if (in_array(trim($col), array('SQRT'))) {
                return 19;
            } else if (in_array(trim($col), array('RT'))) {
                return 21;
            } else {
                return $col;
            }
        } else if ($idx == 7) {
            //sym
            if (trim($col) == '') return 'OT';
            else return $col;
        } else if ($idx == 8) {
            if (in_array($col, array('STG'))) {
                return 'S';
            } else if (in_array($col, array('NON','NONE'))) {
                return  'N';
            } else if (in_array($col, array('FNT'))) {
                return 'F';
            } else if (in_array($col, array('MEDBL','MEDIUM','MB'))) {
                return 'M';
            } else if (in_array($col, array('VSTG', 'VERY STRONG'))) {
                return 'V';
            } else {
                return $col;
            }
        } else if ($idx >= 11) {
            return str_replace(',', '', $col);
        } else {
            return $col;
        }
    }


    public function get_source_discount(){
        $cert_id = _Request::getString("cert_id");
        $model = new DiamondInfoModel(20);
        $result = $model->get_diamond_info_all_row($cert_id);
        if(!empty($result)){
            Util::jsonExit(array('error'=>0,'source_discount'=>$result['source_discount']));
        }else{
            Util::jsonExit(array('error'=>'1'));
        }
    }


    //订单日志
    public function showLogs() {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'cert_id' => _Request::get("cert_id"),

        );

        $page = _Request::getInt("page", 1);
        $where = array();
        $where['cert_id'] = $args['cert_id'];

        $model = new DiamondInfoLogModel(20);
        //$haveold = 0;
        $data = $model->pageList($where, $page, 25, false);



        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_order_action_search_page';
        $this->render('diamond_info_log_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data,
        ));
    }

}
?>
