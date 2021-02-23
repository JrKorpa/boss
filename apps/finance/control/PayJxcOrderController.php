<?php
/**
 *  -------------------------------------------------
 *   @file		: PayJxcOrderController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-26 16:08:05
 *   @update	:
 *  -------------------------------------------------
 */
class PayJxcOrderController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('download');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$model = new CustomerSourcesModel(1);
        $source_list = $model->getSourcesPay();
		$this->render('pay_jxc_order_search_form.html',array('source_list'=>$source_list,'bar'=>Auth::getBar()));
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
            'jxc_order'=> _Request::getString('jxc_order'),
            'type'=> _Request::getString('type'),
            'is_return'=> _Request::getInt('is_return'),
            'status'=> _Request::getInt('status'),
            'kela_sn'=> _Request::getString('kela_sn'),
            'from_ad'=> _Request::getString('from_ad'),
            'addtime_start'=> _Request::getString('addtime_start'),
            'addtime_end'=> _Request::getString('addtime_end'),
            'checktime_start'=> _Request::getString('checktime_start'),
            'checktime_end'=> _Request::getString('checktime_end'),
            'hexiaotime_start'=> _Request::getString('hexiaotime_start'),
            'hexiaotime_end'=> _Request::getString('hexiaotime_end'),
			//'参数' = _Request::get("参数");


		);
		$page = _Request::getInt("page",1);
		$where = array();
        $where['jxc_order'] = $args['jxc_order'];
        $where['type'] = $args['type'];
        $where['is_return'] = $args['is_return'];
        $where['kela_sn'] = $args['kela_sn'];
        $where['status'] = $args['status'];
        $where['from_ad'] = $args['from_ad'];
        $where['addtime_start'] = $args['addtime_start'];
        $where['addtime_end'] = $args['addtime_end'];
        $where['checktime_start'] = $args['checktime_start'];
        $where['checktime_end'] = $args['checktime_end'];
        $where['hexiaotime_start'] = $args['hexiaotime_start'];
        $where['hexiaotime_end'] = $args['hexiaotime_end'];

        $model = new PayJxcOrderModel(29);
		$data = $model->pageList($where,$page,10,false);
		$sourceModel = new CustomerSourcesModel(1);
        if($data['data']){
            $hexiaoModel = new PayHexiaoModel(29);
            foreach ($data['data'] as &$val){
                $val['type'] = $val['type'] == 'S'?'公司销售单':'销售退货单';
                $val['ad_name'] = $sourceModel->getSourceNameById($val['from_ad']);
            }
        }
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'pay_jxc_order_search_page';
		$this->render('pay_jxc_order_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}



    public function download() {
        $where = array();
        $where['jxc_order'] = _Request::getString('jxc_order');
        $where['type'] = _Request::get('type');
        $where['is_return'] = _Request::get('is_return');
        $where['kela_sn'] = _Request::get('kela_sn');
        $where['status'] = _Request::get('status');
        $where['from_ad'] = _Request::get('from_ad');
        $where['addtime_start'] = _Request::get('addtime_start');
        $where['addtime_end'] = _Request::get('addtime_end');
        $where['checktime_start'] = _Request::get('checktime_start');
        $where['checktime_end'] = _Request::get('checktime_end');
        $where['hexiaotime_start'] = _Request::get('hexiaotime_start');
        $where['hexiaotime_end'] = _Request::get('hexiaotime_end');

        $model = new PayJxcOrderModel(29);
		$data = $model->getInfoList($where);
        $title = array('单号','订单来源','单据类型','审核时间','货品数量','成本价','销售价','BDD订单号','核销状态','核销单号','核销时间','核销周期','是否回款','回款周期');
        if (is_array($data)) {
            $hexiaoModel = new PayHexiaoModel(29);
            foreach ($data as $k => $v) {
                $v['type'] = $v['type'] == 'S'?'公司销售单':'销售退货单';
                $v['is_return'] = $v['is_return'] == '0'?'否':'是';
                $v['status'] = $hexiaoModel->getStatusList($v['status']);
                $val = array($v['jxc_order'],$v['ad_name'],$v['type'],$v['checktime'],$v['goods_num'],$v['chengben'],$v['shijia'],$v['kela_sn']."\t ",$v['status'],$v['hexiao_number'],$v['hexiaotime'],$v['hexiao_zq'],$v['is_return'],$v['return_zq']);
				$val = eval('return '.iconv('utf-8','gbk',var_export($val,true).';')) ;
                $content[] = $val;
            }
        }
        $ymd = date("Ymd_His", time() + 8 * 60 * 60);
        header("Content-Disposition: attachment;filename=" . iconv('utf-8', 'gbk', '销售出入库列表') . $ymd . ".csv");
        $fp = fopen('php://output', 'w');
        $title = eval('return ' . iconv('utf-8', 'gbk', var_export($title, true) . ';'));
        fputcsv($fp, $title);
        foreach ($content as $k => $v) {
            fputcsv($fp, $v);
        }
        fclose($fp);
        exit;
    }

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('pay_jxc_order_info.html',array(
			'view'=>new PayJxcOrderView(new PayJxcOrderModel(29))
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
		$result['content'] = $this->fetch('pay_jxc_order_info.html',array(
			'view'=>new PayJxcOrderView(new PayJxcOrderModel($id,29)),
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
		$this->render('pay_jxc_order_show.html',array(
			'view'=>new PayJxcOrderView(new PayJxcOrderModel($id,29)),
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

		$newmodel =  new PayJxcOrderModel(30);
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

		$newmodel =  new PayJxcOrderModel($id,30);

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
		$model = new PayJxcOrderModel($id,30);
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
}

?>