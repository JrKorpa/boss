<?php
/**
 *  -------------------------------------------------
 *   @file		: AppDiamondTongjiController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: liangrongjun <843349919@qq.com>
 *   @date		: 2015-07-26 11:31:14
 *   @update	:
 *  -------------------------------------------------
 */

	class AppDiamondTongjiController extends CommonController{
		
		protected $smartyDebugEnabled = false;
		protected $diamondview = array();
		protected $code = array();
		protected $warehouse = array();
		protected $from_ad =array('1'=>'kela','2'=>'leibish');
		
		public function __construct() {
			parent::__construct();
			$this->appdiamondview = new AppDiamondColorView(new AppDiamondColorModel(19));
			$this->assign('diamondview', $this->appdiamondview);
			//库房
		}
		
		/**
		 * 	index，搜索框
		 */
		public function index($params) {
			$this->render('app_diamond_tongji_search_form.html', array('view' => new DiamondTongjiView(new DiamondTongjiModel(19)), 'bar' => Auth::getBar()));
		}
		
		/**
		 * 	search，列表
		 */
		public function search($params) {
			// var_dump(_REQUEST::getList('color'));die;
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
					'cert_id'=> _Request::getInt('cert_id'),
					'is_active'=> _Request::getInt('is_active'),
					'status'=> _Request::getInt('status'),
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
					'cert_id'=> _Request::getInt('cert_id'),
					'is_active'=> _Request::getInt('is_active'),
					'status'=> _Request::getInt('status'),
			);
		
			$model = new DiamondTongjiModel(19);
			$select = " `goods_id`,`goods_sn`,`goods_name`,`goods_number`,`market_price`,`chengben_jia`,`is_active`,`carat`,`clarity`,`cut`,`color`,`shape`,`symmetry`,`polish`,`fluorescence`,`cert`,`cert_id`,`status` ";
			$data = $model->pageList($where, $page, 10, false,$select);
			//var_dump($data);die;
			$pageData = $data;
			$pageData['filter'] = $args;
			$pageData['jsFuncs'] = 'diamond_info_search_page';
			$this->render('diamond_info_search_list.html', array(
					'pa' => Util::page($pageData),
					'page_list' => $data
			));
		}
		
		
		/**
		 * 	add，渲染添加页面
		 */
		public function add() {
			$result = array('success' => 0, 'error' => '');
			$result['content'] = $this->fetch('diamond_tongji_info.html', array(
					'view' => new DiamondTongjiView(new DiamondTongjiModel(19))
			));
			$result['title'] = '添加';
			Util::jsonExit($result);
		}
		
		/**
		 * 	edit，渲染修改页面
		 */
		public function edit($params) {
			$id = intval($params["id"]);
			$tab_id = intval($params["tab_id"]);
			$result = array('success' => 0, 'error' => '');
			$result['content'] = $this->fetch('diamond_tongji_info.html', array(
					'view' => new DiamondTongjiView(new DiamondTongjiModel($id, 19)),
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
			$this->render('diamond_tongji_show.html', array(
					'view' => new DiamondTongjiView(new DiamondTongjiModel($id, 19)),
					'bar' => Auth::getViewBar()
			));
		}
		
		/**
		 * 	insert，信息入库
		 */
		public function insert($params) {
			$result = array('success' => 0, 'error' => '');
			$upload_name = $_FILES['file_price'];
			if (Upload::getExt($upload_name['name']) != 'csv') {
				$result['error'] = '请上传csv格式文件';
				Util::jsonExit($result);
			}
			$tmp_name = $upload_name['tmp_name'];
			if (!$tmp_name) {
				$result['error'] = '文件不能为空';
				Util::jsonExit($result);
			}
			$newmodel = new DiamondTongjiModel(20);
			$versionValue = 1;
			$tmp_value = $newmodel->getLastId();
			if($tmp_value){
				$versionValue = $tmp_value['version'] + 1;
			}
			$newdo = array();
			$file = fopen($tmp_name, 'r');
			$addtime = date("Y-m-d H:i:s");
			$j = 0;
			while ($data = fgetcsv($file)) {
				$newdo[$j]['shape'] = $data[0];
				$newdo[$j]['clarity'] = $data[1];
				$newdo[$j]['color'] = $data[2];
				$newdo[$j]['min'] = $data[3];
				$newdo[$j]['max'] = $data[4];
				$newdo[$j]['price'] = $data[5];
				$newdo[$j]['addtime'] = $addtime;
				$j++;
			}
		
			$res = $newmodel->insertAll($newdo);
			if ($res !== false) {
				if($lastId = $newmodel->getLastId()){
					$new_model = new DiamondTongjiModel($lastId['id'],20);
					$new_model->setValue('version', $versionValue);
					$new_model->save(true);
				}
				$result['success'] = 1;
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
			$_cls = _Post::getInt('_cls');
			$tab_id = _Post::getInt('tab_id');
		
			$id = _Post::getInt('id');
		
			$newmodel = new DiamondTongjiModel($id, 20);
		
			$olddo = $newmodel->getDataObject();
			$newdo = array(
			);
		
			$res = $newmodel->saveData($newdo, $olddo);
			if ($res !== false) {
				$result['success'] = 1;
				$result['_cls'] = $_cls;
				$result['tab_id'] = $tab_id;
				$result['title'] = '修改此处为想显示在页签上的字段';
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
			$model = new DiamondTongjiModel($id, 20);
			$do = $model->getDataObject();
			$valid = $do['is_system'];
			if ($valid) {
				$result['error'] = "当前记录为系统内置，禁止删除";
				Util::jsonExit($result);
			}
			$model->setValue('is_deleted', 1);
			$res = $model->save(true);
			//联合删除？
			//$res = $model->delete();
			if ($res !== false) {
				$result['success'] = 1;
			} else {
				$result['error'] = "删除失败";
			}
			Util::jsonExit($result);
		}
		
		/**
		 * 	search，列表
		 */
		public function tongji($params) {
			
			$args = array();
			$page = _Request::getInt("page", 1);	//获得当前页
			$where = array();
		
			$model = new AppDiamondTongjiModel(19);
			$Cert = $model->getCert();		//按证书类型统计
			$From_ad = $model->getFrom_ad();	//按来源统计
			$Shape = $model->getShape();		//按形状统计
			$Warehouse = $model->getWarehouse();	//按库房统计
			$appdiamondcolor = new AppDiamondColorModel(19);
			$ware = $appdiamondcolor->getWarehouse();
			
			$addrs = $this->from_ad;
			foreach($From_ad as $k=>$v){
				$From_ad[$k]['from_ad']=$addrs[$v['from_ad']];
			}
			$pageData['jsFuncs'] = 'diamond_tongji_search_page';
			$this->render('app_diamond_tongji_search_list.html', array(
 					'page_list' => $Cert,		//证书
					'From_ad' => $From_ad,		//来源
 					'Shape' => $Shape,			//形状
 					'Warehouse' => $Warehouse,	//库房
 					'Shape_arr' => $appdiamondcolor->getShapeList(),	//返回钻石所有形状数组  array(1 => '圆形', 2 => '公主方形', 3 => '祖母绿形', 4 => '橄榄形', 5 => '椭圆形', 6 => '水滴形', 7 => '心形', 8 => '坐垫形', 9 => '辐射形', 10 => '方形辐射形', 11 => '方形祖母绿', 12 => '三角形',13=>'戒指托',14=>'异形',15=>'梨形',16=>'阿斯切');
			));
		}
		
		/**
		 * 	del，按类型删除
		 */
		public function del($params) {
			
			$result = array('success' => 0, 'error' => '');
			$cert = _Request::getString('cert');
			$from_ad = _Request::getString('from_ad');
			$from_ads = AppDiamondColorModel::$from_ad;
			$from_ads = array_flip($from_ads);
			$shape = _Request::getString('shape');
			$warehouse = _Request::getString('warehouse');
			if(!empty($cert)){
				$where=" AND `cert` IN('".$cert."')";
			}elseif(!empty($from_ad)){
				$where=" AND `from_ad` IN('".$from_ads[$from_ad]."')";
			}elseif(!empty($shape)){
				$where=" AND `shape` IN('".$shape."')";
			}elseif(!empty($warehouse)){
				$where=" AND `warehouse` IN('".$warehouse."')";
			}else{
				$result['error'] = "删除失败";
				Util::jsonExit($result);
			}
		
			$model = new AppDiamondTongjiModel(19);
			$res = $model->del($where);
		
			if ($res !== false) {
				$result['success'] = 1;
			} else {
				$result['error'] = "删除失败";
			}
			Util::jsonExit($result);
		}
		
	}



