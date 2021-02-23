<?php
/**
 *  -------------------------------------------------
 *   @file		: AppDiamondColorController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-02 15:07:13
 *   @update	:
 *  -------------------------------------------------
 */
class AppDiamondColorController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('downLoad','dow');
	 protected $warehouse = array('1'=>'kela','2'=>'leibish');
	 protected  $from_arr =array('1'=>'kela','2'=>'leibish');
	 protected  $ware_arr =array('1'=>'kela','2'=>'leibish');
	 
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
// 		$view = new AppDiamondColorView(new AppDiamondColorModel(19));
// 		var_dump($view->get_status());exit;
		$this->render('app_diamond_color_search_form.html',array('bar'=>Auth::getBar(),'view'=>new AppDiamondColorView(new AppDiamondColorModel(19))));
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
			//'参数' = _Request::get("参数");
            'goods_id'=>  _Request::getString('goods_id'),
            'carat_min'=>  _Request::getFloat('carat_min'),
            'carat_max'=>  _Request::getFloat('carat_max'),
			'color_grade'=> _Request::getString('color_grade'),
			'color'=> _Request::getString('color'),
			'shape'=> _Request::getString('shape'),
			'clarity'=> _Request::getString('clarity'),
			'cert'=> _Request::getString('cert'),
			'cert_id'=> _Request::getString('cert_id'),
            'price_min'=>  _Request::getFloat('price_min'),
            'price_max'=>  _Request::getFloat('price_max'),
			'from_ad'=> _Request::getString('from_ad'),
			'status'=> _Request::getString('status')
		);
		$page = _Request::getInt("page",1);
		$where = array(
            'goods_id'=>  $args['goods_id'],
            'carat_min'=>  $args['carat_min'],
            'carat_max'=>  $args['carat_max'],
			'color_grade'=> $args['color_grade'],
			'color'=> $args['color'],
			'shape'=> $args['shape'],
			'clarity'=> $args['clarity'],
			'cert'=> $args['cert'],
			'cert_id'=> $args['cert_id'],
            'price_min'=>  $args['price_min'],
            'price_max'=>  $args['price_max'],
			'from_ad'=> $args['from_ad'],
			'status'=> $args['status']
                   
        );
		
		$model = new AppDiamondColorModel(19);
		$data = $model->pageList($where,$page,10,false);
		
		$pageData = $data;
		$pageData['filter'] = $args;
		
		$pageData['jsFuncs'] = 'app_diamond_color_search_page';
		$from_ads = AppDiamondColorModel::$from_ad;
		$goods_type = AppDiamondColorModel::$goods_type;
		$status = AppDiamondColorModel::$status;
		
		if(!empty($data['data'])){
		foreach($data['data'] as $k=>$v){
			if($v['from_ad'] !== 1 ){
				$data['data'][$k]['from_ad'] = $from_ads[$v['from_ad']];
			}else{
				$data['data'][$k]['from_ad'] = 'kela';
			}
			if($v['status'] == 2){
				$data['data'][$k]['status'] ='下架';
			}else{
				$data['data'][$k]['status'] ='上架';
			}
		}
	}
	
			$this->render('app_diamond_color_search_list.html',array(
				'pa'=>Util::page($pageData),
				'page_list'=>$data
			));
		
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_diamond_color_info.html',array(
			'view'=>new AppDiamondColorView(new AppDiamondColorModel(19))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}
	/**
	 * 
	*检查产品编号
	*/
	public function check_goods_sn(){
		$goods_sn=trim($_GET['goods_sn']);
		$id = trim($_GET['id']);
		$diamond_model=new AppDiamondColorModel(19); 

		$data=$diamond_model->pageList(array('goods_sn'=> $goods_sn), 0);
		if (!isset($data['data']) || empty($data['data'])) {
			exit('true');
		} else {
			if (!empty($id)) {		
				$num = count($data['data']);
				if ($num > 1) {
					exit('false');
				}
				
				if ($num == 1) {
					exit($data['data'][0]['id'] == $id ? 'true' : 'false');
				}
			}
			
			exit('false');
		}
	}
		/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$model = new AppDiamondColorModel(20);
		$res = $model->getMeasures($id);			//获得尺寸长宽高的值
		$measures = explode('*',$res[0]['measurements']);
		list($long,$width,$height) = $measures;
		
		$result['content'] = $this->fetch('app_diamond_color_info.html',array(
			'view'=>new AppDiamondColorView(new AppDiamondColorModel($id,19)),
			'act'=>'edit',
			'tab_id'=>$tab_id,
			'long' => $long,
			'width'=>$width,
			'height'=>$height
			
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$view = new AppDiamondColorView(new AppDiamondColorModel($id,19));
		$id = intval($params["id"]);
		$this->render('app_diamond_color_show.html',array(
			'view'=>new AppDiamondColorView(new AppDiamondColorModel($id,19)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$goods_id = _Request::getString('goods_id');
		$color = _Request::getString('color');
		$shape = _Request::getString('shape');
		$clarity = _Request::getString('clarity');
		$carat = _Request::getString('carat');
		$cert = _Request::getString('cert');
		$cert_id = _Request::getString('cert_id');
		$from_ad = _Post::getInt('from_ad');
		$color_grade = _Request::getString('color_grade');
		$cost_price = _Request::getString('cost_price');
		$measurements = _Request::getList('measurements');
		$status = _Request::getInt('status');
		$symmetry = _Request::getString('symmetry');
		$polish = _Request::getString('polish');
		$fluorescence = _Request::getString('fluorescence');
		$quantity = _Request::getInt('quantity');
		$good_type= 1;		//现货
		
		date_default_timezone_set('PRC');
		
		$jiajialvModel = new AppDiamondJiajialvModel(20);
		
		$jiajialvList=$jiajialvModel->getAllList();
		
		$jiajialv=$jiajialvModel->getJiajialv($jiajialvList,$cert,$carat,$from_ad,$status,$good_type);
		
		if($jiajialv === false){
			$jiajialv =1;
		}
		$measure = explode('*', $measurements);
		$olddo = array();
		$newdo=array();
		$newdo['goods_id']=trim($goods_id);
		$newdo['shape']=$shape;
		$newdo['carat']=$carat;
		$newdo['color']=$color;
		$newdo['clarity']=$clarity;
		$newdo['polish']=empty($polish)?'':$polish;
		$newdo['symmetry']=empty($symmetry)?'':$symmetry;
		$newdo['fluorescence']=empty($fluorescence)?'':$fluorescence;
		$newdo['measurements']=count($measure)>0?implode('*',$measurements):'';
		$newdo['cert']=$cert;
		$newdo['cert_id']=trim($cert_id);
		$newdo['price']=$cost_price*$jiajialv;
		$newdo['from_ad']=empty($from_ad)?'kela':$from_ad;
		$newdo['add_time']=date('Y-m-d H:i:s',time());
		$newdo['color_grade']=$color_grade;
// 		$newdo['warehouse']=$warehouse;
		$newdo['cost_price']=$cost_price;
// 		$newdo['good_type']=$good_type;
// 		$newdo['mo_sn']=$mo_sn;
		$newdo['status']=empty($status)?1:$status;  //默认上架
		$newdo['quantity']=empty($quantity)?1:$quantity;  //默认数量为1
		
		$newmodel =  new AppDiamondColorModel(20);
		$getRowDiamondBygoods_sn=$newmodel->getRowBygoods_id($newdo['goods_id']);
		if($getRowDiamondBygoods_sn){
			$result['error'] = '该商品编码已存在！';
			Util::jsonExit($result);
		}
		$getRowDiamondBycert_id=$newmodel->getRowBycert_id($newdo['cert_id']);
		if($getRowDiamondBycert_id){
			$result['error'] = '该证书号已存在！';
			Util::jsonExit($result);
		}
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
		
		$goods_sn = _Request::getString('goods_sn');
		$color = _Request::getString('color');
		$shape = _Request::getString('shape');
		$clarity = _Request::getString('clarity');
		$carat = _Request::getString('carat');
		$cert = _Request::getString('cert');
		$cert_id = _Request::getString('cert_id');
		$from_ad = _Post::getInt('from_ad');
		$color_grade = _Request::getList('color_grade');
		$cost_price = _Request::getString('cost_price');
		$measurements = _Request::getList('measurements');
		$status = _Request::getInt('status');
		$symmetry = _Request::getString('symmetry');
		$polish = _Request::getString('polish');
		$fluorescence = _Request::getString('fluorescence');
		$quantity = _Request::getInt('quantity');
		$old_goods_sn = _Request::getString('old_goods_sn');
		$old_cert_id = _Request::getString('old_cert_id');
		
		$good_type= 1;		//现货
		
		$newmodel =  new AppDiamondColorModel($id,20);
        $olddo = array();
		$olddo = $newmodel->getDataObject();
		
		date_default_timezone_set('PRC');
		
		$jiajialvModel = new AppDiamondJiajialvModel(20);
		$jiajialvList=$jiajialvModel->getAllList();
		
		$jiajialv=$jiajialvModel->getJiajialv($jiajialvList,$cert,$carat,$from_ad,$status,$good_type);
		
		if($jiajialv === false){
			$jiajialv =1;
		}
		$newdo=array();
		$newdo['id']=$id;
		$newdo['goods_sn']=$goods_sn;
		$newdo['shape']=$shape;
		$newdo['carat']=$carat;
		$newdo['color']=$color;
		$newdo['clarity']=$clarity;
		$newdo['polish']=empty($polish)?'--':$polish;
		$newdo['symmetry']=empty($symmetry)?'--':$symmetry;
		$newdo['fluorescence']=empty($fluorescence)?'--':$fluorescence;
		$newdo['measurements']=count($measurements)>0?implode('*',$measurements):'--';
		$newdo['cert']=$cert;
		$newdo['cert_id']=$cert_id;
		$newdo['price']=$cost_price * $jiajialv;
		$newdo['from_ad']=empty($from_ad)?'kela':$from_ad;
		$newdo['add_time']=date('Y-m-d H:i:s',time());
		$newdo['color_grade']=$color_grade;
// 		$newdo['warehouse']=$warehouse;
		$newdo['cost_price']=$cost_price;
// 		$newdo['good_type']=$good_type;
// 		$newdo['mo_sn']=$mo_sn;
		$newdo['status']=empty($status)?'--':$status;
		$newdo['quantity']=empty($quantity)?'--':$quantity;
		
		$getRowDiamondBycert_id=$newmodel->getRowBycert_id($newdo['cert_id']);
		if($getRowDiamondBycert_id&&$getRowDiamondBycert_id['cert_id']!= $old_cert_id){
			$result['error'] = '该证书号已存在！';
			Util::jsonExit($result);
		}
		
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	   /**
     * 	delete，
     */
    public function delete($params) {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new AppDiamondColorModel($id, 20);
        $do = $model->getDataObject();
        if( $do['status']== 2){
            $result['error'] = "此记录状态为下架";
            Util::jsonExit($result); 
        }
        //$status = $do['status']==1?0:1;
        $model->setValue('status',2);
        $res = $model->save(true);
        //联合删除？
//         $res = $model->delete();
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "操作失败";
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
				'shape'=> _Request::getList('shape'),
				'carat_min'=>  _Request::getFloat('carat_min'),
				'carat_max'=>  _Request::getFloat('carat_max'),
				'color'=> _Request::getList('color'),
				'clarity'=> _Request::getList('clarity'),
				'polish'=> _Request::getList('polish'),
				'symmetry'=> _Request::getList('symmetry'),
				'fluorescence'=> _Request::getList('fluorescence'),
				'measurements'=>  _Request::getString('measurements'),
				'cert'=> _Request::getList('cert'),
				'cert_id'=> _Request::getInt('cert_id'),
				'price'=> _Request::getFloat('price'),
				'from_ad'=> _Request::getString('from_ad'),
				'color_grade'=> _Request::getString('color_grade'),
				'warehouse'=> _Request::getString('warehouse'),
				'cost_price'=> _Request::getFloat('cost_price'),
				'good_type'=> _Request::getInt('good_type'),
				'mo_sn'=> _Request::getString('mo_sn'),
				'status'=> _Request::getInt('status'),
				'quantity'=> _Request::getInt('quantity'),
            
		);
		
		$model = new AppDiamondColorModel(19);
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
                $datalists[$k]['carat']=$v['carat']?$v['carat']:'';
                $datalists[$k]['shape']=$v['shape']?$v['shape']:'';
                $datalists[$k]['color']=$v['color']?$v['color']:'';
                $datalists[$k]['color_grade']=$v['color_grade']?$v['color_grade']:'';
                $datalists[$k]['polish']=$v['polish']?$v['polish']:'';
                $datalists[$k]['symmetry']=$v['symmetry']?$v['symmetry']:'';
                $datalists[$k]['fluorescence']=$v['fluorescence']?$v['fluorescence']:'';
                $datalists[$k]['measurements']=$v['measurements']?$v['measurements']:'';
                $datalists[$k]['clarity']=$v['clarity']?$v['clarity']:'';
                $datalists[$k]['quantity']=$v['quantity']?$v['quantity']:'';
                $datalists[$k]['cert']=$v['cert']?$v['cert']:'';
                $datalists[$k]['cert_id']=$v['cert_id']?$v['cert_id']:'';
                $datalists[$k]['cost_price']=$v['cost_price']?$v['cost_price']:'';
                $datalists[$k]['good_type']=$v['good_type']?$v['good_type']:'';	
                $datalists[$k]['from_ad']=$v['from_ad']?$v['from_ad']:'';
                $datalists[$k]['warehouse']=$v['warehouse']?$v['warehouse']:'';
                $datalists[$k]['mo_sn']=$v['mo_sn']?$v['mo_sn']:'';
                $datalists[$k]['status']=$v['status']?$v['status']:'';
                
            }
        //}
      $title = array(
				'商品编号',
				'石重',
				'形状',
				'颜色',
				'颜色分级',
				'抛光',
				'对称性',
				'荧光',
				'测量值',
				'净度',
				'数量',
				'证书类型',
				'证书号',
				'成本价',
				'货品类型(1->现货,2->期货)',
				'供应商(1->BDD,2->比丽诗)',
				'库房',
				'模号',
				'状态(1->上架,0->下架)');
            
            Util::downloadCsv("彩钻列表",$title,$datalists);
	}
	
	/**
	 *	delMany，删除多个
	 */
	public function delMany ($params)
	{
		$result = array('success' => 0,'error' => '');
		$goods_id = _Request::getList('_ids');
		$model = new AppDiamondColorModel(20);
		$res = $model->delManyDelete($goods_id);
		if($res !== false){
			$result['success'] = 1;
		}else{
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
		$model = new AppDiamondColorModel($id, 20);
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
			$result['success'] = 1;
		} else {
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}
	
	/**
	 * 	upload，上传彩钻
	 */
	public function upload() {
		$result = array('success' => 0, 'error' => '');
		$result['content'] = $this->fetch('app_diamond_info_upload.html', array(
				'view' => new AppDiamondColorView(new AppDiamondColorModel(19))
		));
		$result['title'] = '批量上传';
		Util::jsonExit($result);
	}
	
	/**
	 * 	upload_ins，批量上传彩钻(入库)
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
		
		$model = new AppDiamondColorModel(20);
		$jiajialvModel = new AppDiamondJiajialvModel(20);
		$zuanshi_list = $model->getAlls("`goods_sn`,`cert`,`cert_id`");	//获得钻石唯一标示
		$get_dia_all=array();
		foreach($zuanshi_list as $k=>$v){
			$get_dia_all[]=$v['cert'].$v['cert_id'];
		}
	
		$header_target="商品编号,石重,形状,颜色,颜色分级,抛光,对称性,荧光,测量值,净度,数量,证书类型,证书号,成本价,货品类型(1->现货,2->期货),供应商,库房,模号,状态(1->上架,0->下架)";
		
		$header=trim(implode(',',array_shift($data_r)),',');
		
		if($header != $header_target){
			$result['error'] = '表头出错';
			Util::jsonExit($result);
		}
		//加价率
		$jiajialvList=$jiajialvModel->getAllList();
// 		$jiajialv=$jiajialvModel->getJiajialv($jiajialvList,$cert,$carat,$from_ad,$status,$good_type);

		foreach($data_r as $k=>$v){
			$v = array_map('trim',$v);
			if($v[0]==''){
				$result['error'] = '第'.($k+2).'行商品编号为空';
				Util::jsonExit($result);
			}elseif(strstr($v[0],'.')&&strstr($v[0],'E+')){
				$result['error'] = '第'.($k+2).'行商品编号格式不对，请重新填写！';
				Util::jsonExit($result);
			}
			$getRowDiamondBygoods_sn=$model->getRowBygoods_sn($v[0]);
			if($getRowDiamondBygoods_sn){
				$result['error'] = '第'.($k+2).'行商品编码已存在！';
				Util::jsonExit($result);
			}
			if($v[1]<=0){
				$result['error'] = '第'.($k+2).'行石重错误';
				Util::jsonExit($result);
			}
			if($v[2]==''){
				$result['error'] = '第'.($k+2).'行形状为空';
				Util::jsonExit($result);
			}
			if($v[3]==''){
				$result['error'] = '第'.($k+2).'行颜色为空';
				Util::jsonExit($result);
			}
			
			if($v[4]==''){
				$result['error'] = '第'.($k+2).'行颜色分级为空';
				Util::jsonExit($result);
			}
			
			if($v[5]==''){
				$result['error'] = '第'.($k+2).'行颜抛光为空';
				Util::jsonExit($result);
			}
			
			if($v[6]==''){
				$result['error'] = '第'.($k+2).'行对称性为空';
				Util::jsonExit($result);
			}
			
			if($v[7]==''){
				$result['error'] = '第'.($k+2).'行荧光为空';
				Util::jsonExit($result);
			}
			if($v[8]==''){
				$result['error'] = '第'.($k+2).'行测试值为空';
				Util::jsonExit($result);
			}
			if($v[9]==''){
				$result['error'] = '第'.($k+2).'行净度为空';
				Util::jsonExit($result);
			}
			
			if($v[9]==''){
				$result['error'] = '第'.($k+2).'行净度为空';
				Util::jsonExit($result);
			}
				
			if($v[10]==''){
				$result['error'] = '第'.($k+2).'行数量为空';
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
			
			if($v[13]==''){
				$result['error'] = '第'.($k+2).'行成本价为空';
				Util::jsonExit($result);
			}
				
			if($v[14]==''){
				$result['error'] = '第'.($k+2).'行货品类型为空';
				Util::jsonExit($result);
			}
			$from_adds = array_values($this->from_arr);
			if(!in_array($v[15],$from_adds)){
				$result['error'] = '第'.($k+2).'行来源不存在';
				Util::jsonExit($result);
			}
			$wares = array_values($this->ware_arr);
			if($v[16]==''){
				$result['error'] = '第'.($k+2).'行库房为空';
				Util::jsonExit($result);
			}elseif(!in_array($v[16],$wares)){

				$result['error'] = '第'.($k+2).'行库房不存在';
				Util::jsonExit($result);
			}
			if($v[18]==''){
				$result['error'] = '第'.($k+2).'行货品状态为空';
				Util::jsonExit($result);
			}
		
			$getRowDiamondBycert_id=$model->getRowBycert_id($v[12]);
			if($getRowDiamondBycert_id){
				$result['error'] = '第'.($k+2).'行证书号已存在！';
				Util::jsonExit($result);
			}
			$shape = $v[2];
			$color = $v[3];
			$clarity = $v[9];
			$polish = $v[5];
			$fluorescence = $v[7];
			$symmetry = $v[6];
			$cert = $v[11];
			$good_type = $v[14];
			list($error,$error_msg,$error_arr) = $model->checkDiamond($shape,$color,$clarity,$polish,$fluorescence,$symmetry,$cert);   //检查上传货品是否在数组中
			if($error == 1){
// 				AppDiamondColorModel::$Cert_arr;
				$result['error'] = '第'.($k+2).'行'.implode(',',$error_msg)."不符合要求.<br/>允许填入以下值：".implode(',',$error_arr);
				Util::jsonExit($result);
			}
			
			$from_ad  = $this->fromad_arrKey[$v[15]];
			$status = $v[18];
			$carat = $v[1];

			$jiajialv=$jiajialvModel->getJiajialv($jiajialvList,$cert,$carat,$from_ad,$status,$good_type);
			
			if(empty($jiajialv)){
				$jiajialv =1;
			}
			
// 			if(!$jiajialv){
// 				$result['error'] = '第'.($k+2).'行加价率不存在!';
// 				Util::jsonExit($result);
// 			}
			$data_r[$k]['jiajialv']=$jiajialv;
		}
		if(!$data_r){
			$result['error'] = '无信息上传!';
			Util::jsonExit($result);
		}
		
		$from_adds = array_flip($this->from_arr);
		foreach($data_r as $k=>$v){
			if(in_array($v[11].$v[12],$get_dia_all)){
				$model->deletebycert_id($v[11],$v[12]);//删除重复
			}
			$olddo=array();
			$newdo = array();
			$newdo['goods_sn'] = $v[0];
			$newdo['carat'] = $v[1];
			$newdo['shape'] = $v[2];
			$newdo['color'] = $v[3];
			$newdo['color_grade'] = $v[4];
			$newdo['polish'] = $v[5];
			$newdo['symmetry'] = $v[6];
			$newdo['fluorescence'] = $v[7];
			$newdo['measurements'] = $v[8];
			$newdo['clarity'] = $v[9];
			$newdo['quantity'] = $v[10];
			$newdo['cert'] = $v[11];
			$newdo['cert_id'] = $v[12];
			$newdo['cost_price'] = $v[13];
			$newdo['price'] = $v[13]*$v['jiajialv'];
			$newdo['good_type'] = $v[14];
			$newdo['from_ad'] = $from_adds[$v[15]];
			$newdo['warehouse'] = $v[16];
			$newdo['mo_sn'] = $v[17]?$v[17]:'--';
			$newdo['status'] = $v[18];
			
			$res = $model->saveData($newdo, $olddo);
		}
		if ($res !== false) {
			$result['success'] = 1;
		} else {
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}
	
	/**
	 *	dow，模板
	 */
	public function dow ()
	{
		$title = array(
				'商品编号',
				'石重',
				'形状',
				'颜色',
				'颜色分级',
				'抛光',
				'对称性',
				'荧光',
				'测量值',
				'净度',
				'数量',
				'证书类型',
				'证书号',
				'成本价',
				'货品类型(1->现货,2->期货)',
				'供应商',
				'库房',
				'模号',
				'状态(1->上架,0->下架)');

	            Util::downloadCsv("彩钻列表",$title,$newdo);
	    }
		
	    /**
	     *	del，删除一个
	     */
	    public function del ($params)
	    {
	    	$result = array('success' => 0,'error' => '');
	    	$id = intval($params['id']);
	    	$model = new AppDiamondColorModel($id,20);
	    	$do = $model->getDataObject();
	    	$res = $model->delete();
	    	if($res !== false){
	    		$result['success'] = 1;
	    	}else{
	    		$result['error'] = "删除失败";
	    	}
	    	Util::jsonExit($result);
	    }
	
	    /*
	     * 查看图片
	     */
	    public function view_Image($params){
	    	
	    	$id=_Request::get('id');
	    	$model = new AppDiamondColorModel(20);
	    	$images = $model->getImagesById($id);
// 	    	$gallery_data=$gallerymodel->getStyleGalleryList($style_sn);
	    	foreach($images[0] as $k=>$v){
	    		if(empty($images[0][$k])){
	    			unset($images[0][$k]);
	    		}
	    	}
	    	if(empty($images[0])){
	    		echo '该钻石没有图片';
	    		return '';
	    	}
	    	
	    	$this->render('show_image.html',array(
	    			'gallery_data'=>$images[0]
	    	));
	    }
	    
	    /*
	     * 编辑图片
	     */
	    public function edit_image(){
	    	
// 	    	echo '<pre>';
// 	    	print_r($_REQUEST);exit;	    	
	    	$id=_Request::get('id');
	    	$model = new AppDiamondColorModel(20);
	    	$images = $model->getImagesById($id);
// 	    	$gallery_data=$gallerymodel->getStyleGalleryList($style_sn);
	    	foreach($images[0] as $k=>$v){
	    		if(empty($images[0][$k])){
	    			unset($images[0][$k]);
	    		}
	    	}
	    	$this->render('edit_image.html',array(
	    			'gallery_data'=>$images[0]
	    	));
	    }
	
	    /**
	     * 	upload，上传图片
	     */
	    public function upload_image() {
	    	
	    	$result = array('success' => 0, 'error' => '');
	    	$result['content'] = $this->fetch('app_diamond_info_upload.html', array(
	    			'view' => new AppDiamondColorView(new AppDiamondColorModel(19))
	    	));
	    	$result['title'] = '批量上传';
	    	Util::jsonExit($result);
	    }
	    
	    /*
	     * 更改图片
	     */
// 	    public function update_image(){
// 	    	echo 777;
// 	    	echo "<pre>";
// 	    	print_r($_REQUEST);
	    	
// 	    }
	    
	    
}

?>