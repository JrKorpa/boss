<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseQibanGoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-01 15:22:51
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseQibanGoodsController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('search');

	//起版款号固定写死   （黄文銮亲自答应，涉案人员 刘日 + 曹操）
	private $qian_style_sn =  'QIBAN';

	/**
	* 获取供应商列表
	*/
	public function GetGongyingshang(){
		$obj = new ApiProcessorModel();
		return $obj->GetSupplierList();
	}
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('purchase_qiban_goods_search_form.html',array('bar'=>Auth::getBar(),'gys'=>$this->GetGongyingshang(),'dd'=> new DictView(new DictModel(1))));
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
			'addtime' => _Request::get("addtime"),
			'order_sn' => _Request::get("order_sn"),
			'customer' => _Request::get("customer"),
			'status' => _Request::get("status"),
			'price_min' => _Request::getString("price_min"),
			'price_max' => _Request::getString("price_max"),
			'xiangkou_min' => _Request::getString("xiangkou_min"),
			'xiangkou_max' => _Request::getString("xiangkou_max"),
			'shoucun_min' => _Request::getString("shoucun_min"),
			'shoucun_max' => _Request::getString("shoucun_max"),
			'fuzhu' => _Request::getString("fuzhu"),
			'gongchang' => _Request::getString("gongchang"),
			'kuanhao' => _Request::getString("kuanhao"),
			'zhengshu' => _Request::getString("zhengshu"),
			'xuqiu' => _Request::getString("xuqiu"),
			'jinliao' => _Request::getString("jinliao"),
			'jinse' => _Request::getString("jinse"),
			'gongyi' => _Request::getString("gongyi"),
			'opt' => _Request::getString("opt"),
			'info' => _Request::getString("info"),
            'kuan_type' => _Request::getInt("kuan_type"),
            'qiban_type' => _Request::getString("qiban_type"),
            'start_time' => _Request::getString("start_time"),
            'end_time' => _Request::getString("end_time"),
            'qiban_download' => _Request::get('qiban_download')?_Request::get('qiban_download'):''

		);  
		$page = _Request::getInt("page",1);
		$where = array(
			'addtime'=>$args['addtime'],
			'order_sn'=>$args['order_sn'],
			'customer'=>$args['customer'],
			'status'=>$args['status'],
			'price_min'=>$args['price_min'],
			'price_max'=>$args['price_max'],
			'xiangkou_min'=>$args['xiangkou_min'],
			'xiangkou_max'=>$args['xiangkou_max'],
			'shoucun_min'=>$args['shoucun_min'],
			'shoucun_max'=>$args['shoucun_max'],
			'fuzhu'=>$args['fuzhu'],
			'gongchang'=>$args['gongchang'],
			'kuanhao'=>$args['kuanhao'],
			'zhengshu'=>$args['zhengshu'],
			'xuqiu'=>$args['xuqiu'],
			'jinliao'=>$args['jinliao'],
			'jinse'=>$args['jinse'],
			'gongyi'=>$args['gongyi'],
			'opt'=>$args['opt'],
			'info'=>$args['info'],
            'kuan_type'=>$args['kuan_type'],
            'qiban_type'=>$args['qiban_type'],
            'start_time'=>$args['start_time'],
            'end_time'=>$args['end_time']
		);

        if(SYS_SCOPE == 'zhanting'){
            $where['hidden'] = '0';
        }

		$model = new PurchaseQibanGoodsModel(23);
        //导出功能
        if($args['qiban_download']=='qiban_download'){
            $data = $model->pageList($where,$page,90000000,false);
            $this->qiban_download($data);
            exit;
        }
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'purchase_qiban_goods_search_page';
		$this->render('purchase_qiban_goods_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'dd'=> new DictView(new DictModel(1)),
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
	    $result = array('success' => 0,'content' => '','title'=>'添加');	    		
		$result['content'] = $this->fetch('purchase_qiban_goods_info.html',array(
			'view'=>new PurchaseQibanGoodsView(new PurchaseQibanGoodsModel(23)),
		));
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
	    $result = array('success'=> 0,'content' =>'','title'=>'编辑');
		
	    $id = _Request::getInt("id");
		$tab_id = _Request::getInt("tab_id");

		$result['content'] = $this->fetch('purchase_qiban_goods_info.html',array(
			'view'=>new PurchaseQibanGoodsView(new PurchaseQibanGoodsModel($id,23)),
			'tab_id'=>$tab_id,
		));
		Util::jsonExit($result);
		
	}


	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		if(empty($params['gongchang_info'])){
			$result['error'] = '请选择工厂';
			Util::jsonExit($result);
		}
		if (!isset($params['kuanhao']) || empty($params['kuanhao'])) {
			$result['error'] = '请输入QIBAN或者款号';
			Util::jsonExit($result);
		}
        //增加字段【起版类型】有款起版/无款起版，如果输入款号QIBAN，为无款起版，非QIBAN为有款起版
        $qiban_type = '有款起版';
        if($params['kuanhao'] == 'QIBAN'){
            
            $qiban_type = '无款起版';
        }
		
		$gongchang_info = explode('|', $params['gongchang_info']);
		$olddo = array();
		$newdo=array(
			'price'=>trim($params['price']),
			'xiangkou'=>$params['xiangkou'],
			'shoucun'=>$params['shoucun'],
			'specifi'=>$params['specifi'],
			'fuzhu'=>trim($params['fuzhu']),
			'gongchang_id'=> $gongchang_info[0],
			'gongchang'=> $gongchang_info[1],
			'kuanhao'=>trim($params['kuanhao']),
			'zhengshu'=>$params['zhengshu'],
			'qibanfei'=>$params['qibanfei'],
			'xuqiu'=> intval($params['xuqiu']),
			'jinliao'=>intval($params['jinliao']),
			'jinse'=>intval($params['jinse']),
			'gongyi'=> intval($params['gongyi']),
			'order_sn'=> trim($params['order_sn']),
			'info'=> trim($params['info']),
            'kuan_type'=> trim($params['kuan_type']),
            'qiban_type'=> $qiban_type,
			'addtime' => time(),
		    'zhushi_num'=>$params['zhushi_num'],
		    'cert'=>$params['cert'],
            'yanse'=>$params['yanse'],
            'jingdu'=>$params['jingdu'],
		    'jinzhong_min'=>empty($params['jinzhong_min']) ? 0 : $params['jinzhong_min'],
		    'jinzhong_max'=>empty($params['jinzhong_max']) ? 0 : $params['jinzhong_max']
		);
		//校验个别属性字段
		$res = $this->checkQibanData($newdo);
		if($res['success']==0){
		    $result['error'] = $res['error'];
		    Util::jsonExit($result);
		}else{
		    $newdo = $res['data'];
		}
		$this->checkStyleAndFactory($newdo['kuanhao'], $newdo['gongchang_id'], $newdo['fuzhu'], $newdo['xiangkou']);

		$newmodel = new PurchaseQibanGoodsModel(24);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['error'] = '添加成功';
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}
	/**
	 * 检查起版个别属性值是否合法
	 * @param unknown $args
	 * @return multitype:number string
	 */
    protected function checkQibanData($args){
        $result = array('success' => 0,'error' => '');
        $xiangqian = isset($args['xuqiu'])?$args['xuqiu']:'';
        //主石单颗重验证
        if(!empty($args['specifi']) && !is_numeric($args['specifi'])){
            $result['error']="主石单颗重不合法，主石单颗重必须为数字!";
            return $result;
        }else if(isset($args['specifi'])){            
            $args['specifi'] = $args['specifi']/1;
        }
        //主石粒数验证
        if(!empty($args['zhushi_num']) && !preg_match("/^\d+$/",$args['zhushi_num'])){
            $result['error']="主石粒数不合法，主石粒数必须为正整数!";
            return $result;
        }else if(isset($args['zhushi_num'])){
            $args['zhushi_num'] = $args['zhushi_num']/1;
        }
        //$result['error']=$args['zhushi_num'];
        //return $result;
        if($xiangqian<>'不需工厂镶嵌'){
            if(isset($args['specifi']) && isset($args['zhushi_num'])){
                if(($args['specifi']==0 && $args['zhushi_num']>0) ||($args['specifi']>0 && $args['zhushi_num']==0)){
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
        //指圈
        if(!empty($args['zhiquan']) && !is_numeric($args['zhiquan'])){
            $result['error']="指圈不合法，指圈必须为数字!";
            return $result;
        }else if(isset($args['zhiquan'])){
            $args['zhiquan'] = $args['zhiquan']/1;
        }
        //证书号
        if(!empty($args['zhengshu']) && !preg_match("/^[a-z|A-Z|0-9|\|]+$/is",$args['zhengshu'])){
            $result['error']="证书号不合法，证书号只能包含【字母】【数字】【英文竖线】,英文竖线作为多个证书号分隔符。";
            return $result;
        }
        //证书类型验证
        if(!empty($args['zhengshu']) && ($args['cert']=="" ||$args['cert']=="无")){
            $result['error']="证书类型不能为空或无，填写了证书号必须填写有效的证书类型";
            return $result;
        }
        $result['success'] = 1;
        $result['data'] = $args;
        return $result;
    }
	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
	    
		$result = array('success' => 0,'error' =>'');
		if(empty($params['gongchang_info'])){
			$result['error'] = '请选择工厂';
			Util::jsonExit($result);
		}
		if (!isset($params['kuanhao']) || empty($params['kuanhao'])) {
			$result['error'] = '请输入QIBAN或者款号';
			Util::jsonExit($result);
		}

        //增加字段【起版类型】有款起版/无款起版，如果输入款号QIBAN，为无款起版，非QIBAN为有款起版
        $qiban_type = '有款起版';
        if($params['kuanhao'] == 'QIBAN'){
            
            $qiban_type = '无款起版';
        }
		
		$gongchang_info = explode('|', $params['gongchang_info']);
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');

		$newmodel =  new PurchaseQibanGoodsModel($id,24);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$params['id'],
			'price'=>$params['price'],
			'xiangkou'=>$params['xiangkou'],
			'shoucun'=>$params['shoucun'],
			'specifi'=>$params['specifi'],
			'fuzhu'=>$params['fuzhu'],
			'gongchang_id'=> $gongchang_info[0],
			'gongchang'=> $gongchang_info[1],
			'kuanhao'=>trim($params['kuanhao']),
			'zhengshu'=>$params['zhengshu'],
		    'jinliao'=>intval($params['jinliao']),
		    'jinse'=>intval($params['jinse']),
			'qibanfei'=>$params['qibanfei'],
			'xuqiu'=> intval($params['xuqiu']),
			'jinliao'=>intval($params['jinliao']),
			'jinse'=>intval($params['jinse']),
			'gongyi'=> intval($params['gongyi']),
			'order_sn'=> trim($params['order_sn']),
			'info'=> trim($params['info']),
            'kuan_type'=> trim($params['kuan_type']),
            'qiban_type'=> $qiban_type,
		    'zhushi_num'=>$params['zhushi_num'],
		    'cert'=>$params['cert'],
            'yanse'=>$params['yanse'],
            'jingdu'=>$params['jingdu'],
		    'jinzhong_min'=>empty($params['jinzhong_min']) ? 0 : $params['jinzhong_min'],
		    'jinzhong_max'=>empty($params['jinzhong_max']) ? 0 : $params['jinzhong_max']
			//'addtime' => time(), 老大 谁拿addtime去做起版号id的， 更新的时候还改动 以前的起版怎么关联
		);
		//校验个别属性字段
		$res = $this->checkQibanData($newdo);
		if($res['success']==0){
		    $result['error'] = $res['error'];
		    Util::jsonExit($result);
		}else{
		    $newdo = $res['data'];
		}
		$this->checkStyleAndFactory($newdo['kuanhao'], $newdo['gongchang_id'], $newdo['fuzhu'], $newdo['xiangkou']);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['error'] = '修改成功';
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
	 *	禁用
	 */
	public function stop ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new PurchaseQibanGoodsModel($id,24);
		$do = $model->getValue('status');
		if($do == 2)
		{
			$result['error'] = "当前记录已经停用，不能重复操作";
			Util::jsonExit($result);
		}
		$do = $model->getValue('order_sn');
		if($do != '')
		{
			$result['error'] = "当前记录已经绑定了订单，不能停用";
			Util::jsonExit($result);
		}
		$model->setValue('status',2);
		$res = $model->save(true);

		if($res !== false){
			$result['success'] = 1;
			$result['error'] = "停用成功";
		}else{
			$result['error'] = "停用失败";
		}
		Util::jsonExit($result);
	}

	/**
	 * 启用
	 */
	public function start($params){
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new PurchaseQibanGoodsModel($id,24);
		$do = $model->getValue('status');
		if($do == 1)
		{
			$result['error'] = "当前记录已经启用，不能重复操作";
			Util::jsonExit($result);
		}
		$do = $model->getValue('order_sn');
		if($do != '')
		{
			$result['error'] = "当前记录已经绑定了订单，不能启用";
			Util::jsonExit($result);
		}
		$model->setValue('status',1);
		$res = $model->save(true);

		if($res !== false){
			$result['success'] = 1;
			$result['error'] = "启用成功";
		}else{
			$result['error'] = "启用失败";
		}
		Util::jsonExit($result);
	}

	/**
	 * 删除（物理删除）
	 */
	public function delete($params){
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new PurchaseQibanGoodsModel($id,24);
		$do = $model->getValue('order_sn');
		if($do != '')
		{
			$result['error'] = "当前记录已经绑定了订单，不能删除";
			Util::jsonExit($result);
		}
		$res = $model->delete();

		if($res !== false){
			$result['success'] = 1;
			$result['error'] = "删除成功";
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/**
	 * 获取款号信息
	 * @param unknown $params
	 */
	public function retrieveKuan($params) {
		$result = array('error' => '');
		if (!isset($params['style_sn'])) {
			$result['error'] = '参数异常';
			Util::jsonExit($result);
		}
		
		$apiModel = new ApiProcessorModel();
		$resp = $apiModel->getStyleAndFactories($params['style_sn']);
		if (empty($resp)) {
			$result['error'] = '该款号不存在或者未审核，请确认';
		} else {
			if (isset($params['internal'])) {
				foreach ($resp as $r) {
					if (!empty($r['factory_id'])) $result['factories'][] = $r;
				}
			} else {				
				$result['factory_id'] = $resp[0]['factory_id'];
				$result['factory_sn'] = $resp[0]['factory_sn'];
			}

			if (isset($result['factory_id'])) {
				$result['factory_name'] = $apiModel->GetProcessorName($result['factory_id']);
			}
		}
		
		if (isset($params['internal'])) {
			return $result;
		} else {
			Util::jsonExit($result);
		}
	}
	
	private function checkStyleAndFactory($style_sn, $factory_id, $factory_sn, $xiangkou) {
		// 1. style_sn为自定义输入时, 验证style_sn；
		if ($style_sn != $this->qian_style_sn) {
			$style_factory = $this->retrieveKuan(array('style_sn'=> $style_sn, 'internal' => ''));
			if (!empty($style_factory['error'])) {
				$result['error'] = $style_factory['error'];
				Util::jsonExit($result);
			} else {
				// 3.1 验证当前款是否有工厂列表
				if (!isset($style_factory['factories'])) {
					$result['error'] = '此款没有工厂列表，请确认';
					Util::jsonExit($result);
				}
				// 3. 验证用户选择的工厂是否在其工厂列表中
				$factory_id_list = array_column($style_factory['factories'], 'factory_id');
				
				$apiModel = new ApiProcessorModel();
				//获取默认工厂
				$factory = $apiModel->GetProcessorName($style_factory['factories'][0]['factory_id']);
				if (!in_array($factory_id, $factory_id_list)) {
					$result['error'] = '此款不能在所选工厂生产，此款的默认工厂为'.'"'.implode(" ", $factory).'"';
					Util::jsonExit($result);
				}
				if ($factory['id'] != $factory_id && empty($factory_sn)) {
					$result['error'] = '选择非默认工厂时，模号请必填。';
					Util::jsonExit($result);
				}
			}
		}
		
		// 2. 验证工厂+模号是否唯一
		if (!empty($factory_sn)) {
			/*
			 * 申请工厂验证工厂+模号唯一时，全部是X的情况下不做判断，这个不管是一个还是多个；模号里面有2个或者2个以上XX不做判断，不管是否连续；模号位数大于1，且只有一个x，是需要判断的
			 */
			$mt = array();
			if (preg_match_all('/X/i', $factory_sn, $mt)) {
				$num = count($mt[0]);
				if ($num >= 2 || $num == strlen($factory_sn)) return;
			}
			
			$style_api_model = new ApiProcessorModel();
			$factories = $style_api_model->getValidStyleFactoryList($factory_id, $factory_sn);
			if (!empty($factories)) {
				$repeat_data = null;
				foreach ($factories as $f) {
					if ($f['style_sn'] == $style_sn) {
						if ($f['xiangkou'] == $xiangkou) {
							$result['error'] = '此款工厂镶口对应模号已存在，不用起版，请确认';
							Util::jsonExit($result);
						}
					} else {
						$repeat_data = $f;
						// 不要break，确保前面一个判断都执行到
					}
				}
				if (!empty($repeat_data)) {
					$result['error'] = '此工厂的模号在款式库已存在，款号为'.$repeat_data['style_sn'].'，请核实！';
					Util::jsonExit($result);
				}
			}
		}
	}

    //导出
    public function qiban_download($data) {

        set_time_limit(0);
        //ini_set('memory_limit', '3500M');
        $dd =new DictModel(1);
        //$salemodel = new SalesModel(51);
        //$view = new WarehouseBillModel(21);

        if ($data['data']) {
            $down = $data['data'];
            $xls_content = "款式类型,起版类型,添加时间,起版号,价格,镶口,手寸,规格,模号,起版费,工厂,款号,证书号,产品需求,金料,金色 ,表面工艺,状态,订单,顾客姓名,录单人,备注\r\n";
            foreach ($down as $key => $val) {
                
                $xls_content .= $dd->getEnum('purchase.kuan_type',$val['kuan_type']) . ",";
                $xls_content .= $val['qiban_type'] . ",";
                $xls_content .= date('Y-m-d H:i:s', $val['addtime']) . ",";
                $xls_content .= $val['addtime'] . ",";
                $xls_content .= $val['price'] . ",";
                $xls_content .= $val['xiangkou'] . ",";
                $xls_content .= $val['shoucun'] . ",";
                $xls_content .= $val['specifi']. ",";
                $xls_content .= $val['fuzhu'] . ",";
                $xls_content .= $val['qibanfei'] . ",";
                $xls_content .= $val['gongchang'] . ",";
                $xls_content .= $val['kuanhao'] . ",";
                $xls_content .= $val['zhengshu'] . ",";
                $xls_content .= $dd->getEnum('purchase.qiban_xuqiu',$val['xuqiu']) . ",";
                $xls_content .= $dd->getEnum('purchase.qiban_jinliao',$val['jinliao']) . ",";
                $xls_content .= $dd->getEnum('purchase.qiban_jinse',$val['jinse']) . ",";
                $xls_content .= $dd->getEnum('purchase.qiban_gongyi',$val['gongyi']) . ",";
                $xls_content .= $dd->getEnum('purchase.qiban_status',$val['status']) . ",";
                $xls_content .= $val['order_sn'] . ",";
                $xls_content .= $val['customer'] . ",";
                $xls_content .= $val['opt'] . ",";
                $xls_content .= $val['info'] . "\n";

            }
        } else {
            $xls_content = '没有数据！';
        }

        header("Content-type: text/html; charset=gbk");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=" . iconv("utf-8", "GBK//IGNORE", "导出" . date("Y-m-d")) . ".csv");
        echo iconv("utf-8", "GBK//IGNORE", $xls_content);

    }

}?>