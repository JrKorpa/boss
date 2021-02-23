<?php

/**
 *  -------------------------------------------------
 *   @file		: DiamondGetJxcController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462206282@qq.com>
 *   @date		: 2019-01-14 11:31:14
 *   @update	:
 *  -------------------------------------------------
 */
class AppDiamondGetJxcController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('downLoad');
    protected $jxcwh = array();
    protected $war = array();
    protected $code = array();
    protected $warehouse = array();

    public function __construct() {
        parent::__construct();

        $appdiamondcolor = new AppDiamondColorModel(19);
        $this->jxcwh = $appdiamondcolor->getWarehouse();
        
		$this->assign("xianhuoway",$this->jxcwh);   //$this->jxcwh是一个包括仓库信息的数组
		$this->assign("form",1);
    }

    /**
     * 	index，搜索框
     */
    public function index($params) {
    	$this->render('app_diamond_get_jxc_search_form.html', array('view' => new DiamondGetJxcView(new DiamondGetJxcModel(19)), 'bar' => Auth::getBar()));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
        );
        
        $page = _Request::getInt("page", 1);	//获得当前页
        $where = array(
          
        );
        
        $model = new DiamondGetJxcModel(19);
        $select = " `goods_id`,`goods_sn`,`goods_name`,`goods_number`,`market_price`,`chengben_jia`,`is_active`,`carat`,`clarity`,`cut`,`color`,`shape`,`symmetry`,`polish`,`fluorescence`,`cert`,`cert_id`,`status` ";
        $data = $model->pageList($where, $page, 10, false,$select);
        //var_dump($data);die;
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'diamond_get_jxc_search_page';
        $this->render('app_diamond_get_jxc_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }


    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('app_diamond_get_jxc_info.html', array(
            'view' => new DiamondGetJxcView(new DiamondGetJxcModel(19))
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
        $result['content'] = $this->fetch('app_diamond_get_jxc_info.html', array(
            'view' => new DiamondGetJxcView(new DiamondGetJxcModel($id, 19)),
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
        $this->render('app_diamond_get_jxc_show.html', array(
            'view' => new DiamondGetJxcView(new DiamondGetJxcModel($id, 19)),
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
        $newmodel = new DiamondGetJxcModel(20);
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
                $new_model = new DiamondGetJxcModel($lastId['id'],20);
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

        $newmodel = new DiamondGetJxcModel($id, 20);

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
        $model = new DiamondGetJxcModel($id, 20);
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
	 *	downLoad，下载
	 */
	public function downLoad ()
	{
// 		echo "<pre>";
// 		print_r($_REQUEST);exit;
		
		$where = array(
            'id'=> _Request::getString('id'),
            'page_size'=>'10000000',
            'page'=>'1',
		);
        if($where['id']!=''){
        	if($where['id'] == 'all'){
        		$type = 1;
        	}else{
        		$type = 0;
        	}
            $model = new AppDiamondGetJxcModel(19);
            $data = $model->get_warehouse_by_house($where,$type);
            $datalists=array();
            foreach($data as $k=>$v){
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
            
        }else{
            $datalists=array();
        }
//         echo "<pre>";
//         print_r($datalists);exit;
        
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
            
            Util::downloadCsv("进销存取彩钻列表",$title,$datalists);
	}

   /**
     * 	upload_ins，批量上传彩钻
     */
    public function upload_ins() {
    	
        ini_set('memory_limit','6000M');
        set_time_limit(0);
        $result = array('success' => 0, 'error' => '');
// 		$jiajialv=1;
		$res = false;
		$upload_name = $_FILES;
		
        if (!$upload_name) {
            $result['error'] = '文件不能为空';
            Util::jsonExit($result);
        }
        //upload::getExt  --获得上传文件的后缀名		
        if (Upload::getExt($upload_name['file_price']['name']) != 'csv') {
            $result['error'] = '请上传csv格式文件';
            Util::jsonExit($result);
        }
        
		$file = fopen($upload_name['file_price']['tmp_name'], 'r');
		//fgetcsv 读取csv文件的一行
        while ($data = fgetcsv($file)) {
			foreach($data as $k => $v){
				$data[$k] = iconv("GBK","UTF-8",$v);
			}
			$data_r[]=$data;		//$data获得上传文件信息的数组
			$is_n[]=$data;
        }
        
      	$is_n=array_splice($is_n,1);	//移除了表头属性字段
        if(empty($is_n)){
            $result['error'] = '文件内容不能为空!';
            Util::jsonExit($result);             
        }
        $data_r_cert=array();
        $warehouse=array();
        
//         foreach($is_n as $k=>$v){
//         	$warehouse[] = $v[20];
//         }
		
       //error_reporting(E_ALL); 
    	 $warehouse=implode("','",array_unique($warehouse)); 
       	 $model = new AppDiamondColorModel(20);		//diamondInfoModel  裸钻配置列表
//         
       	 $jiajialvModel = new AppDiamondJiajialvModel(20);		//DiamondJiajialvModel  裸钻加价率
        $jiajialvList=$jiajialvModel->getAllList();			// 从表diamond_jiajialv获取部分数据
//         	$Xh=$model->deleteXh($warehouse,1);		// 删除根据字段warehouse和good_type(现货1、期货2) = 1删除(表diamond_info)
       	array_shift($data_r);
        $tong=array();
        foreach($data_r as $k=>$v){
        	
//  		$model->deletebycert_id($v[11],$v[12]);//删除根据证书类型和证书号删除唯一屁屁额记录
			//表diamond_jiajialv取出部分字段
			//return  返回加价率 $jiajialvList, $cert, $carat, $from_ad, $status,$good_type
		
			$v['jiajialv'] = $jiajialv;
			if($v[1]<=0){
                  $result['error'] = '第'.$k.'行石重错误';
                  Util::jsonExit($result);
			}
			if($v[12]==''){
                   $result['error'] = '第'.$k.'行证书号为空';
                   Util::jsonExit($result);
			}
			if($v[13]<=0){
                   $result['error'] = '第'.$k.'行成本价错误';
                   Util::jsonExit($result);
			}elseif($v[13]==''){
                   $result['error'] = '第'.$k.'行成本价错误';
                   Util::jsonExit($result);               
			}
			$error=0;
			$shape = $v[2];
			$color = $v[3];
			$clarity =$v[9];
			$polish =$v[5];
			$fluorescence =$v[7];
			$symmetry = $v[6];
			$cert =$v[11];
			
			list($error,$error_msg) = $model->checkDiamond($shape,$color,$clarity,$polish,$fluorescence,$symmetry,$cert);
			if($error == 1){
				$result['error'] = '第'.($k+1).'行'.implode(',',$error_msg)."不符合要求";
				Util::jsonExit($result);
				break;
			}
        }
        if($is_n){ //更新 
            $newdoList = array();
            foreach($is_n as $k=>$v){
				if(!empty($v['mo_sn'])){
					$newdo['mo_sn'] = $v[17];
				}
			//$jiajialvList, $cert, $carat, $from_ad, $status,$good_type
			$jiajialv = $jiajialvModel->getJiajialv($jiajialvList,$v[12],$v[1],$v[15],1,1);
			if(empty($jiajialv)){
				$jiajialv = 1;
			}
// 			file_put_contents('e:/8.txt',$jiajialv);
                $newdo = array();
//                 $newdo['id'] = null;  不写 自增
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
                $newdo['good_type'] = 1;
                $newdo['from_ad'] = $v[15];
                $newdo['warehouse'] = $v[16];
                $newdo['status'] = $v[18]?$v[18]:'1';
                $newdo['add_time']=date('Y-m-d H:i:s',time());
                $newdo['price'] = $v[13]*$jiajialv;
                
                $newdoList[] = $newdo;
            }
        }
        $newdoList = $this->change($newdoList,100);		//一次100条数据插入，减缓数据库压力
        $newmodel = new AppDiamondGetJxcModel(20);
        if($newdoList){
            foreach($newdoList as $key => $val){
                $res=$newmodel->insertByGroup($val);		//把数据插入到数据表diamond_info
            }
        }
		
		if ($res !== false) {
			$result['success'] = 1;
		} else {
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
    }

    //  count($a) = 80;  $step = 100;
    
     public function change($a,$step=10){
        $i_leng= count($a);
        $x = array();
        for($i=0;$i<$i_leng;$i++){		
            $x = $i/$step;			
            $y = $i%$step;
            $new[$x][$y] = $a[$i];
        }
        return $new;
    }
}

?>
