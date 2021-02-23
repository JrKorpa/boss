<?php
class DiaChannelJiajialvController extends CommonController {
	
	protected $smartyDebugEnabled = false;
	protected $diamondview = array();
	protected $fromad_arr = array();
	
	public function __construct() {
		parent::__construct();
		$model = new DiamondInfoModel(19);
		$this->diamondview = new DiamondInfoView(new DiamondInfoModel(19));
		$this->assign('diamondview', $this->diamondview);
	}
	
	/**
	 * 	index，搜索框
	 */
	public function index($params) {
		$res = $this->get_channel_list();
		$this->render('diachannel_jiajialv_search_form.html', array('bar' => Auth::getBar(), 'channels' => $res));
	}
	
	/**
	 * 	search，列表
	 */
	public function search($params) {
		$args = array(
				'mod' => _Request::get("mod"),
				'con' => substr(__CLASS__, 0, -10),
				'act' => __FUNCTION__,
				'carat_min'=>  _Request::getFloat('carat_min'),
				'carat_max'=> _Request::getFloat('carat_max'),
				'good_type'=> _Request::getInt('good_type'),
				'status'=> _Request::getString('status'),
				'channel_id' => _Request::getInt('channel_id')
		);
		$page = _Request::getInt("page", 1);
		$where = array(
				'carat_min'=>  _Request::getFloat('carat_min'),
				'carat_max'=> _Request::getFloat('carat_max'),
				'good_type'=> _Request::getInt('good_type'),
				'status'=> _Request::getString('status'),
				'channel_id' => _Request::getInt('channel_id')
		);
		
		$user_channels = $this->get_channel_list();
		if ($where['channel_id'] == 0) {
			if (count($user_channels) == 1) {
				$where['channel_id'] = $user_channels[0]['channel_id'];
			} else if (!Auth::user_is_from_base_company()) {
				$where['channel_id'] = implode(',', array_column($user_channels, 'channel_id'));
			}
		}
		
		$model = new DiaChannelJiajialvModel(99);
		$data = $model->pageList($where, $page, 10, false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'diachannel_jiajialv_search_page';
		$this->render('diachannel_jiajialv_search_list.html', array(
				'pa' => Util::page($pageData),
				'channels' => Util::indexArray($user_channels, 'channel_id'),
				'certs' => DiamondInfoModel::$cert_arr,
				'page_list' => $data,
		));
	}
	
	/**
	 * 	add，渲染添加页面
	 */
	public function add($params) {
		if (isset($_GET['ispost']) && $_GET['ispost'] == '1') {
			$this->insert($params);
		} else {
			$result = array('success' => 0, 'error' => '');
			$result['content'] = $this->fetch('diachannel_jiajialv_info.html', array(
				'view' => new DiaChannelJiajialvView(new DiaChannelJiajialvModel(99)),
				'channels' => $this->get_channel_list()
			));
			$result['title'] = '添加';
			Util::jsonExit($result);
		}
	}
	
	/**
	 * 	edit，渲染修改页面
	 */
	public function edit($params) {
		if (isset($_GET['ispost']) && $_GET['ispost'] == '1') {
			$this->update($params);
		} else {
			$id = intval($params["id"]);
			//$tab_id = intval($params["tab_id"]);
			$result = array('success' => 0, 'error' => '');
			$result['content'] = $this->fetch('diachannel_jiajialv_info.html', array(
					'view' => new DiaChannelJiajialvView(new DiaChannelJiajialvModel($id, 99)),
					'channels' => $this->get_channel_list()
			));
			$result['title'] = '编辑';
			Util::jsonExit($result);
		}
	}
	
	/**
	 * 	show，渲染查看页面
	 */
	public function show($params) {
		$id = intval($params["id"]);
		$this->render('diamond_jiajialv_show.html', array(
				'view' => new DiaChannelJiajialvView(new DiaChannelJiajialvModel($id, 99)),
				'bar' => Auth::getViewBar()
		));
	}
	
	/**
	 * 	insert，信息入库
	 */
	private function insert($params) {
		$result = array('success' => 0, 'error' => '');
		
		$good_type = _Post::getInt('good_type');
		$channel_id = _Post::getInt('channel_id');
		$carat_min = _Post::getFloat('carat_min');
		$carat_max = _Post::getFloat('carat_max');
		$jiajialv = _Post::getFloat('jiajialv');
		$cert = _Post::getString('cert');
		if ($jiajialv < 1.2 || $jiajialv > 3) {
			$result['error'] = '加价应该在1.2 - 3之间';
			Util::jsonExit($result);
		}
		
		if($carat_min <0){
			$result['error'] = '最小钻重不能小于0';
			Util::jsonExit($result);
		}
		
		if($carat_max<0){
			$result['error'] = '最大钻重不能小于0';
			Util::jsonExit($result);
		}
		
		if ($carat_min > $carat_max) {
			$result['error'] = '最小钻重不能大于最大钻重';
			Util::jsonExit($result);
		}
		$olddo = array();
		$newdo = array(
				'good_type' => $good_type,
				'channel_id' => $channel_id,
				'carat_min' => $carat_min,
				'carat_max' => $carat_max,
				'jiajialv' => $jiajialv,
				'cert' => $cert
		);
		$newmodel = new DiaChannelJiajialvModel(99);
		//获取所有的数据
		$where_list = array('channel_id'=>$channel_id,'good_type' => $good_type,'cert' => $cert);
		$all_data = $newmodel->getAllList("`carat_min`,`carat_max`", $where_list);
		$do = true;
		foreach ($all_data as $v) {
			if (($v['carat_min'] <= $newdo['carat_min'] && $newdo['carat_min'] < $v['carat_max'])) {
				$do = FALSE;
				break;
			}
			
			if(($v['carat_min'] < $newdo['carat_max'] && $newdo['carat_max'] < $v['carat_max'])){
				$do = FALSE;
				break;
			}
			if ($newdo['carat_min'] <= $v['carat_min'] && $newdo['carat_max'] >= $v['carat_max']) {
				$do = FALSE;
				break;
			}
		}
		if (!$do) {
			$result['error'] = '范围出错';
			Util::jsonExit($result);
		}
		
		$res = $newmodel->saveData($newdo, $olddo);
		if ($res !== false) {
			$result['success'] = 1;
			$this->operationLog("insert", $newdo);
		} else {
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}
	
	/**
	 * 	update，更新信息
	 */
	private function update($params) {
		$result = array('success' => 0, 'error' => '');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');
		
		$id = _Post::getInt('id');
		$good_type = _Post::getInt('good_type');
		$channel_id = _Post::getInt('channel_id');
		$carat_min = _Post::getFloat('carat_min');
		$carat_max = _Post::getFloat('carat_max');
		$jiajialv = _Post::getFloat('jiajialv');
		$cert = _Post::getString('cert');
		if ($jiajialv < 1.2 || $jiajialv > 3) {
			$result['error'] = '加价应该在1.2 - 3 之间';
			Util::jsonExit($result);
		}
		
		if($carat_min <0){
			$result['error'] = '最小钻重不能小于0';
			Util::jsonExit($result);
		}
		
		if($carat_max<0){
			$result['error'] = '最大钻重不能小于0';
			Util::jsonExit($result);
		}
		
		if ($carat_min > $carat_max) {
			$result['error'] = '最小钻重不能大于最大钻重';
			Util::jsonExit($result);
		}
		$newmodel = new DiaChannelJiajialvModel($id, 99);
		$olddo = $newmodel->getDataObject();
		$newdo = array(
				'id' => $id,
				'good_type' => $good_type,
				'channel_id' => $channel_id,
				'carat_min' => $carat_min,
				'carat_max' => $carat_max,
				'jiajialv' => $jiajialv,
				'cert' => $cert
		);
		
		//获取所有的数据
		$where_list = array('channel_id' => $channel_id,'good_type' => $good_type, 'cert' => $cert);
		$all_data = $newmodel->getAllList("`id`,`carat_min`,`carat_max`", $where_list);
		$do = true;
		foreach ($all_data as $v) {
			if($v['id'] == $id){
				continue;
			}
			
			if (($v['carat_min'] <= $newdo['carat_min'] && $newdo['carat_min'] < $v['carat_max'])) {
				$do = FALSE;
				break;
			}
			
			if(($v['carat_min'] < $newdo['carat_max'] && $newdo['carat_max'] < $v['carat_max'])){
				$do = FALSE;
				break;
			}
			if ($newdo['carat_min'] <= $v['carat_min'] && $newdo['carat_max'] >= $v['carat_max']) {
				$do = FALSE;
				break;
			}
		}
		if (!$do) {
			$result['error'] = '范围出错';
			Util::jsonExit($result);
		}
		
		$equals = !array_diff($newdo, $olddo) && !array_diff($olddo, $newdo);
		//如果没有任何修改，不做任何操作
		if ($equals) {
			$result['success'] = 1;
			Util::jsonExit($result);
		}
		
		$res = $newmodel->saveData($newdo, $olddo);
		if ($res !== false) {
			
			$this->operationLog("update", array('olddata' => $olddo, 'newdata' => $newdo, 'pkdata' => array('id' => $id)));
			
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;
			$result['title'] = '修改裸钻加价率';
		} else {
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}
	
	/**
	 * 	delete,停用
	 */
	public function disable($params) {
		$result = array('success' => 0, 'error' => '');
		$id = intval($params['id']);
		$model = new DiaChannelJiajialvModel($id, 99);
		$do = $model->getDataObject();
		if( $do['status']== 0){
			$result['error'] = "此记录状态为停用";
			Util::jsonExit($result);
		}
		//$status = $do['status']==1?0:1;
		$model->setValue('status', 0 );
		$res = $model->save(true);
		
		//$res = $model->delete();
		if ($res !== false) {
			$result['success'] = 1;
			$new_data = $do;
			$new_data['status'] = 0;
			$this->operationLog("update", array('olddata'=> $do, 'newdata' => $new_data, "pkdata" =>  array("id" => $id)));
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
		$model = new DiaChannelJiajialvModel($id, 99);
		$do = $model->getDataObject();
		if( $do['status']== 1){
			$result['error'] = "此记录状态为启用";
			Util::jsonExit($result);
		}
		//$status = $do['status']==1?0:1;
		$model->setValue('status', 1);
		$res = $model->save(true);
		
		//$res = $model->delete();
		if ($res !== false) {
			$result['success'] = 1;
			$new_data = $do;
			$new_data['status'] = 1;
			$this->operationLog("update", array('olddata'=> $do, 'newdata' => $new_data, "pkdata" =>  array("id" => $id)));
		} else {
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}
	
	public function get_channel_list($company_type = 3) {
		
		if(Auth::user_is_from_base_company()) {
			$sql = "select s.id as channel_id, s.channel_name from cuteframe.sales_channels s
			inner join cuteframe.company c on s.company_id = c.id
			where c.company_type = {$company_type};";
		} else {
			$sql = "select channel_id, s.channel_name from cuteframe.user_channel uc
			inner join cuteframe.sales_channels s on s.id = uc.channel_id
			inner join cuteframe.company c on s.company_id = c.id
			where user_id = {$_SESSION['userId']} and c.company_type = {$company_type} and c.id = {$_SESSION['companyId']}";
		}
		return DB::cn(1)->getAll($sql);
	}
	
}

?>