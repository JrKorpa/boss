<?php
/**
 *  -------------------------------------------------
 *   @file		: MonthlyExportController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-04-05 10:42:41
 *   @update	:
 *  -------------------------------------------------
 */
class MonthlyExportController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('search');
    public $limit_time = '2019-01-01 00:00:00';

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('monthly_export_search_form.html',array('bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{

        $dep = _Request::getList('dep');
        //$sale = _Request::getList('salse');
        //if(count($sale) > 20){
            //echo '销售顾问不能超过20个！';exit();
        //}
        //$qudaoarr = array();
        //$saleUser = array();
        //if(empty($dep) || empty($sale) || $dep[0] == null || $sale[0] == null){
        if(empty($dep) || $dep[0] == null || $dep[0]=='null'){
            if($_SESSION['qudao']){
                $dep = explode(',',$_SESSION['qudao']);
            }
            //if(!empty($qudaoarr)){
                //$saleUser = $this->getCreateuser($qudaoarr,1);
            //}
        }
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
            'export_type'   => _Request::get("export_type"),
            'export_time_start' => _Request::get("export_time_start"),
            'export_time_end' => _Request::get("export_time_end"),
            'dep_type' => _Request::get('dep_type'),
            'dep' => $dep,
            'salse' => _Request::getList('salse'),
            //'salse' => _Request::getList('salse',$saleUser),
            'down_infos' => _Request::get('down_infos')
		);

        if(!empty($args['export_time_start']) && $args['export_time_start']<$this->limit_time && SYS_SCOPE == 'zhanting'){
            $args['export_time_start'] = $this->limit_time;
        }
        //if(!empty($args['dep']) && strstr($args['dep'][0], ',')){
        //    $args['dep'] = explode(",", $args['dep'][0]);
        //}
        //if(!empty($args['salse']) && strstr($args['salse'][0], ',')){
        //    $args['salse'] = explode(",", $args['salse'][0]);
        //}
        if(!empty($args['salse'][0]) && $args['salse'][0]=='null')
            $args['salse']=array();
		$page = _Request::getInt("page",1);
		$where = array(
            'export_type'=>$args['export_type'],
            'export_time_start'=>$args['export_time_start'],
            'export_time_end'=>$args['export_time_end'],
            'dep_type'=>$args['dep_type'],
            'dep'=>$args['dep'],
            'salse'=>$args['salse'],
            );
		$model = new MonthlyExportModel(29);
        
        if(!empty($args['dep'])) $args['dep'] = implode(",", $args['dep']);
        if(!empty($args['salse'])) $args['salse'] = implode(",", $args['salse']);
        if(empty($dep)){
            $args['dep'] = array();
        }
        if(empty($sale)){
            $args['salse'] = array();
        }
        if($args['down_infos'] == 'downs'){
            $this->DownloadCSV($where);exit();
        }
        $tsyd_list = $model->getTsydInfo($where);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'monthly_export_search_page';
		$this->render('monthly_export_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
            'tsyd_list'=>$tsyd_list,
            'export_type'=>$args['export_type']
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('monthly_export_info.html',array(
			'view'=>new MonthlyExportView(new MonthlyExportModel(29))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('monthly_export_info.html',array(
			'view'=>new MonthlyExportView(new MonthlyExportModel($id,29)),
			'tab_id'=>$tab_id
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
		$this->render('monthly_export_show.html',array(
			'view'=>new MonthlyExportView(new MonthlyExportModel($id,29)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;
		$olddo = array();
		$newdo=array();

		$newmodel =  new MonthlyExportModel(30);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
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
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new MonthlyExportModel($id,30);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '修改此处为想显示在页签上的字段';
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new MonthlyExportModel($id,30);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

    public function getShops(){
        $shop_type = _Post::getInt('dep_type');
        //获取体验店的信息
        $model = new ShopCfgChannelModel(59);
        $data = $model->getallshopqita();
        $ret = array();
        foreach($data as $key => $val){
            if($shop_type == 0){
                $ret[$val['id']] = $val['shop_name'];
            }
            if($shop_type == 1 && $val['shop_type'] == 1){
                $ret[$val['id']] = $val['shop_name'];
            }
            if($shop_type == 2 && $val['shop_type'] == 2){
                $ret[$val['id']] = $val['shop_name'];
            }
            if($shop_type == 3 && $val['shop_type'] == 3){
                $ret[$val['id']] = $val['shop_name'];
            }
        }
        $userChannelmodel = new UserChannelModel(59);
        $data_chennel = $userChannelmodel->getChannels($_SESSION['userId'],0);
        $myChannel="<option value=''></option>";
        foreach($data_chennel as $key => $val){
            if(!empty($ret) && array_key_exists($val['id'],$ret)){
                $myChannel .= "<option value='".$val['id']."'>".$val['channel_name']."</option>";
            }
        }
        $result = $myChannel;
        Util::jsonExit($result);
    }

    //取销售顾问
    public function getCreateuser($dep =array(),$type = 0){
        $result = array('success' => 0,'error' => '');
        $department= _Request::getList('department',$dep);
        $model = new UserChannelModel(1);
        if(!empty($department)){
            $dp_people_name = array();
            foreach ($department as $key => $value) {
                $data = $model->get_channels_person_by_channel_id($value);
                if($data['dp_people_name']=='' || $data['dp_leader_name']==''){
                    $data = $model->get_user_channel_by_channel_id($value);
                }else{
                    $dp_people_name = explode(",",$data['dp_people_name']);
                    $dp_people_name = array_filter($dp_people_name);
                    $dp_leader_name = explode(",",$data['dp_leader_name']);
                    $dp_leader_name = array_filter($dp_leader_name);
                    $data=array();
                    $dp_people_me = in_array($_SESSION['userName'],$dp_people_name);
            
                    if(in_array($_SESSION['userName'],$dp_leader_name))
                    {
                        $alluser = array_merge($dp_people_name,$dp_leader_name);
                        foreach($alluser as $k=>$v)
                        {
                            $data[]['account']=$v;
                        }
                    }elseif($dp_people_me){
                        $data[0]['account']=$_SESSION['userName'];  
                    }
            }
                $dataI[$value] =$data;
            }
        }
        if(!empty($dataI)){
            $dataC = array();
            foreach ($dataI as $dep => $name) {
                if(!empty($name)){
                    foreach ($name as $k => $v) {
                        $dataC[$v['account']][] = $dep;
                    }
                }
            }
            $dataP = array();
            foreach ($dataC as $key => $value) {
                $dep_str = '';
                foreach ($value as $k => $v) {
                    $dep_str.= $v.'|';
                  $dataP[$key] = $dep_str.'_'.$key;
                }
            }
            $content='';
            if(!empty($dataP)){
                foreach($dataP as $k => $v){
                    $content.='<option value="'.$v.'">'.$k.'</option>"';
                }
            }
            $result['content']=$content;
        }
        if(!$type){
            Util::jsonExit($result);
        }else{
            return $dataP;
        }
        
    }

    public function DownloadCSV($where){

        set_time_limit(0);
        ini_set('memory_limit', -1);// 临时解除内存限制：
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=monthlyexport.csv");
        header('Cache-Control: max-age=0');
        $model = new MonthlyExportModel(29);
        $export_type = $where['export_type'];
        if($export_type == 'xinzeng'){
            $title=array('订单号','来源','销售渠道','顾问','类型','商品类型','系列','证书类型','彩钻','钻石大小','总金额','原价','成交价','已付金额','未付金额','是否网销订单','网销名','款号','货品名称','黄金材质','金重','商品数量','订单状态','付款状态','第一次点款时间','证书号','款式分类','产品线',',期货/现货','发货状态','布产状态','绑定货号','成本','货品所在仓库','是否退货','可取货状态','款式来源渠道','成品定制码','天生一对特殊款','商品信息备注','仓储新产品线','仓储新款式分类','款式库产品线','款式库款式分类'
            );
        }elseif($export_type == 'fahuo'){
            $title=array('来源','顾问','柜面','订单号','销售渠道','总金额','已付金额','未付金额','类型','款号','货品名称','商品类型','金料','金重','原价','成交价','成本价','系列','证书类型','彩钻','货号','双十一特价钻','是否网校订单','网销名','石重','点款流水号','是否以旧换新订单','退货商品金额','实际回款金额','证书号','款式分类','产品线','期货/现货','款式来源渠道','成品定制码','天生一对特殊款','仓储新产品线','仓储新款式分类','款式库产品线','款式库款式分类','发货时间','第一次付款时间'
            );
        }elseif($export_type == 'zuantui'){
            $title=array('订单号','来源','销售渠道','顾问','退款申请人','录入类型','商品类型','系列','证书类型','彩钻','钻石大小','总金额','标价','成交价','应退金额','实退金额','退款核算金额','是否网销订单','网销名','款号','货号名称','黄金材质','金重','商品数量','订单状态','付款状态','退货类型','审核金额','订单商品总金额','会计审核时间','证书号','退款申请人所属渠道','款式分类','产品线','期货/现货','退款方式','款式来源渠道','成品定制码','天生一对特殊款','仓储新产品线','仓储新款式分类','款式库产品线','款式库款式分类'
            );
        }
        foreach ($title as $k => $v) {
            $title[$k]=iconv('utf-8', 'GB18030', $v);
        }
        echo "\"".implode("\",\"",$title)."\"\r\n";
        $page = 1;
        $pageSize=30;
        $pageCount=1;
        $recordCount = 0;
        $list_sql=$model->getsql($where);
        $dataArr = $model->getDataC($where);
        $tsyd_list= $model->getTsydInfo($where);
        $detailsid = array();
        while($page <= $pageCount){
            $data = $model->db()->getPageListForExport($list_sql,array(),$page,$pageSize,false,$recordCount);
            $returnData= $model->ExportSet($data,$where,$dataArr,$detailsid);
            $detailsid = $returnData['detailsid'];
            unset($returnData['detailsid']);
            $data['data'] =$returnData;
            $page ++;
            if(!empty($data['data'])){
                $recordCount = $data['recordCount'];
                $pageCount = $data['pageCount'];
                $data = $data['data'];
                if(!is_array($data) || empty($data)){
                    continue;
                }
                //wg.product_type1,wg.cat_type1,`t`.`cat_type_name`,`p`.`product_type_name`
                foreach($data as $d){

                    $temp=array();
                    if($export_type == 'xinzeng'){
                        $temp['order_sn']="'".$d['order_sn'];
                        $temp['ad_name']=$d['ad_name'];
                        $temp['dep_name']=$d['dep_name'];
                        $temp['make_order']=$d['make_order'];
                        $temp['referer']=$d['referer'];
                        $temp['goods_type']=$d['goods_type'];
                        $temp['xl_name_str']=$d['xl_name_str'];
                        $temp['cert']=$d['cert'];
                        $temp['is_caizuan']=$d['goods_type']=='caizuan_goods'?'是':'否';
                        $temp['cart']=$d['cart'];
                        $temp['price']=$d['price'];
                        $temp['market_price']=$d['market_price'];
                        $temp['goods_price']=$d['goods_price'];
                        $temp['money_paid']=$d['money_paid'];
                        $temp['order_amount']=$d['order_amount'];
                        $temp['wangxiao']=$d['wangxiao'];
                        $temp['bespoke_make_order']=$d['bespoke_make_order'];
                        $temp['goods_sn']=$d['goods_sn'];
                        $temp['goods_name']=$d['goods_name'];
                        $temp['gold']=$d['gold'];
                        $temp['gold_weight']=$d['gold_weight'];
                        $temp['goods_count']=$d['goods_count'];
                        $temp['order_status']=$d['order_status'];
                        $temp['order_pay_status']=$d['order_pay_status'];
                        $temp['pay_date']=$d['pay_date'];
                        $temp['zhengshuhao']=$d['zhengshuhao'];
                        $temp['style_type']=$d['cat_type1'];
                        $temp['product_type']=$d['product_type1'];
                        $temp['is_stock_goods']=$d['is_stock_goods'];
                        $temp['send_good_status']=$d['send_good_status'];
                        $temp['buchan_status']=$d['buchan_status'];
                        $temp['bd_goods_id']=$d['bd_goods_id'];
                        $temp['chengbenprice']=$d['chengbenprice'];
                        $temp['warehouse']=$d['warehouse'];
                        $temp['is_return_srt']=$d['is_return_srt'];
                        $temp['is_kequstr']=$d['is_kequstr'];
                        $temp['style_channel']=$d['style_channel'];
                        $temp['cpdzcode']=$d['cpdzcode'];
                        if(!empty($d['is_tsyd_special'])){
                            $temp['is_tsyd_special']=$d['is_tsyd_special'];
                        }else{
                            if(in_array($d['detail_id'],$tsyd_list)){
                                $temp['is_tsyd_special'] = '是';
                            }else{
                                $temp['is_tsyd_special'] = '';
                            }
                        }
                        $temp['details_remark']=$d['details_remark'];
                        //wg.product_type1,wg.cat_type1,`t`.`cat_type_name`,`p`.`product_type_name` 
                        $temp['product_type1']=$d['product_type1'];
                        $temp['cat_type1']=$d['cat_type1'];
                        $temp['product_type_name']=$d['product_type_name'];
                        $temp['cat_type_name']=$d['cat_type_name'];
                    }elseif($export_type == 'fahuo'){
                        $temp['ad_name']=$d['ad_name'];
                        $temp['make_order']=$d['make_order'];
                        $temp['id']=$d['id'];
                        $temp['order_sn']="'".$d['order_sn'];
                        $temp['dep_name']=$d['dep_name'];
                        $temp['price']=$d['price'];
                        $temp['money_paid']=$d['money_paid'];
                        $temp['order_amount']=$d['order_amount'];
                        $temp['referer']=$d['referer'];
                        $temp['goods_sn']=$d['goods_sn'];
                        $temp['goods_name']=$d['goods_name'];
                        $temp['goods_type']=$d['goods_type'];
                        $temp['gold']=$d['gold'];
                        $temp['gold_weight']=$d['gold_weight'];
                        $temp['market_price']=$d['market_price'];
                        $temp['goods_price']=$d['goods_price'];
                        $temp['sale_price']=$d['sale_price'];
                        $temp['xl_name_str']=$d['xl_name_str'];
                        $temp['cert']=$d['cert'];
                        $temp['is_caizuan']=$d['goods_type']=='caizuan_goods'?'是':'否';
                        $temp['goods_id']=$d['goods_id'];
                        $temp['special_price']=$d['special_price']!=''?'是':'否';;
                        $temp['wangxiao']=$d['wangxiao'];
                        $temp['bespoke_make_order']=$d['bespoke_make_order'];
                        $temp['cart']=$d['cart'];
                        $temp['tuikuan_str']=$d['tuikuan_str'];
                        $temp['is_huanxin']=$d['is_huanxin'];
                        $temp['tuihuo_price']=$d['tuihuo_price'];
                        $temp['real_hk_price']=$d['real_hk_price'];
                        $temp['zhengshuhao']=$d['zhengshuhao'];
                        $temp['style_type']=$d['cat_type1'];
                        $temp['product_type']=$d['product_type1'];
                        $temp['is_stock_goods']=$d['is_stock_goods'];
                        $temp['style_channel']=$d['style_channel'];
                        $temp['cpdzcode']=$d['cpdzcode'];
                        if(!empty($d['is_tsyd_special'])){
                            $temp['is_tsyd_special']=$d['is_tsyd_special'];
                        }else{
                            if(in_array($d['detail_id'],$tsyd_list)){
                                $temp['is_tsyd_special'] = '是';
                            }else{
                                $temp['is_tsyd_special'] = '';
                            }
                        }
                        $temp['product_type1']=$d['product_type1'];
                        $temp['cat_type1']=$d['cat_type1'];
                        $temp['product_type_name']=$d['product_type_name'];
                        $temp['cat_type_name']=$d['cat_type_name'];
                        $temp['shipfreight_time']=$d['shipfreight_time'];
                        $temp['pay_date']=$d['pay_date'];
                    }elseif($export_type == 'zuantui'){
                        $temp['order_sn']="'".$d['order_sn'];
                        $temp['ad_name']=$d['ad_name'];
                        $temp['dep_name']=$d['dep_name'];
                        $temp['make_order']=$d['make_order'];
                        $temp['account']=$d['account'];
                        $temp['referer']=$d['referer'];
                        $temp['goods_type']=$d['goods_type'];
                        $temp['xl_name_str']=$d['xl_name_str'];
                        $temp['cert']=$d['cert'];
                        $temp['is_caizuan']=$d['goods_type']=='caizuan_goods'?'是':'否';
                        $temp['cart']=$d['cart'];
                        $temp['price']=$d['price'];
                        $temp['market_price']=$d['market_price'];
                        $temp['goods_price1']=$d['goods_price1'];
                        $temp['should_return_amount']=$d['should_return_amount'];
                        $temp['real_return_price']=$d['real_return_price'];
                        $temp['goods_price']=$d['goods_price'];
                        $temp['wangxiao']=$d['wangxiao'];
                        $temp['bespoke_make_order']=$d['bespoke_make_order'];
                        $temp['goods_sn']=$d['goods_sn'];
                        $temp['goods_name']=$d['goods_name'];
                        $temp['caizhi']=$d['caizhi'];
                        $temp['jinzhong']=$d['jinzhong'];
                        $temp['goods_count']=$d['goods_count'];
                        $temp['order_status']=$d['order_status'];
                        $temp['order_pay_status']=$d['order_pay_status'];
                        $temp['return_types']=$d['return_types'];
                        $temp['confirm_price']=$d['confirm_price'];
                        $temp['goods_amount']=$d['goods_amount'];
                        $temp['deparment_finance_time']=$d['deparment_finance_time'];
                        $temp['zhengshuhao']=$d['zhengshuhao'];
                        $temp['apply_channel']=$d['apply_channel'];
                        $temp['style_type']=$d['cat_type1'];
                        $temp['product_type']=$d['product_type1'];
                        $temp['is_stock_goods']=$d['is_stock_goods'];
                        $temp['return_by']=$d['return_by'];
                        $temp['style_channel']=$d['style_channel'];
                        $temp['cpdzcode']=$d['cpdzcode'];
                        if(!empty($d['is_tsyd_special'])){
                            $temp['is_tsyd_special']=$d['is_tsyd_special'];
                        }else{
                            if(in_array($d['detail_id'],$tsyd_list)){
                                $temp['is_tsyd_special'] = '是';
                            }else{
                                $temp['is_tsyd_special'] = '';
                            }
                        }
                        $temp['product_type1']=$d['product_type1'];
                        $temp['cat_type1']=$d['cat_type1'];
                        $temp['product_type_name']=$d['product_type_name'];
                        $temp['cat_type_name']=$d['cat_type_name'];
                    }
                    foreach ($temp as $k => $v) {
                        $temp[$k] = iconv('utf-8', 'GB18030', $v);
                    }
                    echo "\"".implode("\",\"",$temp)."\"\r\n";
                }
            }
        }
    }
}

?>