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
class DiamondGetJxcController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('downLoad');
    protected $jxcwh = array();
    protected $war = array();
    protected $code = array();
    protected $warehouse = array();

    public function __construct() {
        parent::__construct();

		$model = new DiamondInfoModel(19);
		$warehouse_arr = $model->get_warehouse_all();
		if($warehouse_arr['error']<=0){
			$this->jxcwh=$warehouse_arr['data'];
			$this->code['总公司']='COM';
			$this->warehouse['COM']='总公司';
			foreach($this->jxcwh as $k=>$v){
				$this->war[$v['id']]=$v['name'];
				$this->code[$v['name']]=$v['code'];
				$this->warehouse[$v['code']]=$v['name'];
				if($v['code']=='ZJLZ' || $v['code']=='TTPLZK' || $v['code']=='TBLZK' || $v['code']=='XA2LZ' || $v['code']=='ZZLZK' || $v['code']=='XABDJLZMDK' || $v['code']=='NJZJYHTYDLZ' || $v['code']=='CGBZZK' || $v['code']=='CDKJXTYDLZ' || $v['code']=='SZYWJD'){
					unset($this->jxcwh[$k]);
					unset($this->war[$v['id']]);
				}
			}
		}else{
		   $this->jxcwh=array();
		   $this->war=array();
		   $this->code=array();
		   $this->warehouse=array();
		}

		$this->assign("xianhuoway",$this->jxcwh);
		$this->assign("form",1);
    }

    /**
     * 	index，搜索框
     */
    public function index($params) {
        $this->render('diamond_get_jxc_search_form.html', array('view' => new DiamondGetJxcView(new DiamondGetJxcModel(19)), 'bar' => Auth::getBar()));
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
        
        $page = _Request::getInt("page", 1);
        $where = array(
          
        );
        
        $model = new DiamondGetJxcModel(19);
        $select = " `goods_id`,`goods_sn`,`goods_name`,`goods_number`,`market_price`,`chengben_jia`,`is_active`,`carat`,`clarity`,`cut`,`color`,`shape`,`symmetry`,`polish`,`fluorescence`,`cert`,`cert_id`,`status` ";
        $data = $model->pageList($where, $page, 10, false,$select);
        //var_dump($data);die;
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'diamond_get_jxc_search_page';
        $this->render('diamond_get_jxc_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }


    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('diamond_get_jxc_info.html', array(
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
        $result['content'] = $this->fetch('diamond_get_jxc_info.html', array(
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
        $this->render('diamond_get_jxc_show.html', array(
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
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
        exit;

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
		$where = array(
            'id'=> _Request::getString('id'),
            'page_size'=>'10000000',
            'page'=>'1',
		);
        if($where['id']!=''){
            if($where['id']=='all'){
               $where['id']=implode("','",array_flip($this->war));
            }

            $model = new DiamondGetJxcModel(19);
            $data = $model->get_warehouse_by_houseId($where);
            //print_r($data);exit;     
            if($data['error']>0){
                $datalists = array();
            }else{
                $datalist=$data['data']['data'];
                $datalists=array();
                if($datalist){
                foreach($datalist as $k=>$v){
                    if(!empty($v['order_goods_id']))
                        continue;
                    //特殊处理
                    if($v['zhushixingzhuang'] == '公主方'){
                        $v['zhushixingzhuang'] = '公主方形';
                    }
                    if($v['zhushixingzhuang'] == '梨形'){
                        $v['zhushixingzhuang'] = '水滴形';
                    }
                    
                    if($v['zhushixingzhuang'] == '垫形'){
                    	$v['zhushixingzhuang'] = '坐垫形';
                    }
                    
                    if($v['zhushixingzhuang'] == '祖母绿'){
                    	$v['zhushixingzhuang'] = '祖母绿形';
                    }
                    
                 

                    $datalists[$k]['goods_id']=$v['goods_id']?$v['goods_id']:'';
                    $datalists[$k]['warehouse_id']=isset($this->war[$v['warehouse_id']])?$this->war[$v['warehouse_id']]:'';
                    $datalists[$k]['zhushixingzhuang']=$v['zhushixingzhuang']?$v['zhushixingzhuang']:'';
                    $datalists[$k]['zuanshidaxiao']=$v['zuanshidaxiao']?$v['zuanshidaxiao']:'';
                    $datalists[$k]['zhushiyanse']=$v['zhushiyanse']?$v['zhushiyanse']:'';
                    $datalists[$k]['zhushijingdu']=$v['zhushijingdu']?$v['zhushijingdu']:'';
                    $datalists[$k]['qiegong']=$v['qiegong']?$v['qiegong']:'';
                    $datalists[$k]['paoguang']=$v['paoguang']?$v['paoguang']:'';
                    $datalists[$k]['yingguang']=$v['yingguang']?$v['yingguang']:'';
                    $datalists[$k]['duichen']=$v['duichen']?$v['duichen']:'';
                    $datalists[$k]['zhengshuleibie']=$v['zhengshuleibie']?$v['zhengshuleibie']:'';
                    $datalists[$k]['zhengshuhao']=$v['zhengshuhao']?$v['zhengshuhao']:'';
                    $datalists[$k]['chengbenjia']=$v['mingyichengben']?$v['mingyichengben']:'';//名义成本 
                    $datalists[$k]['gemx_zhengshu']=$v['gemx_zhengshu']?$v['gemx_zhengshu']:'';
                    $datalists[$k]['goods_sn']=$v['goods_sn']?$v['goods_sn']:'';
                    $datalists[$k]['mo_sn']=$v['mo_sn']?$v['mo_sn']:'';
                }
                }
            }
        }else{
            $datalists=array();
        }
        $title = array(
				'货号',
                '仓库',
                '主石形状',
                '钻石大小',
                '主石颜色',
                '主石净度',
                '切工',
                '抛光',
                '荧光',
                '对称',
                '证书类别',
                '证书号',
                '成本价',
				'星耀钻石',
				'款号',
                '模号');
            
            Util::downloadCsv("进销存取裸钻列表",$title,$datalists);
	}

   /**
     * 	upload_ins，批量上传裸钻
     */
    public function upload_ins() {
        ini_set('memory_limit','6000M');
        set_time_limit(0);
        $result = array('success' => 0, 'error' => '');
		$jiajialv=10;
		$res = false;
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
			$is_n[]=$data;
        }
        
        $is_n=array_splice($is_n,1);
        if(empty($is_n)){
            $result['error'] = '文件内容不能为空!';
            Util::jsonExit($result);             
        }
        $data_r_cert=array();
        $warehouse=array();
        foreach($data_r as $k=>$v){
            $data_r_cert[]=$v[10].$v[11];
            if($k!=0){
                if(isset($this->code[$v[1]]) && !empty($this->code[$v[1]])){
                    $warehouse[$k]=$this->code[$v[1]];
                }else{
                    $result['error'] = '第'.($k+1).'库房不存在！';
                    Util::jsonExit($result);                      
                }
            }
        }
       //error_reporting(E_ALL); 
        $warehouse=implode("','",array_unique($warehouse)); 
        $model = new DiamondInfoModel(20);
        $jiajialvModel = new DiamondJiajialvModel(20);
        $jiajialvList=$jiajialvModel->getAllList();
        //更改逻辑 刷钻前删除所有现货钻，不管哪个仓库(boss 包含总公司和直营店裸钻库 浩鹏包含个体店裸钻库)
        $warehouse=$model->get_warehouse_all();
        $warehouse=implode("','", array_unique(array_column($warehouse['data'],'code')));
        $Xh=$model->deleteXh($warehouse,1);

		//除去表头
		array_shift($data_r);

        $tong=array();
        foreach($data_r as $k=>$v){
			$shape = $v[2];
			$carat = $v[3];
			$cut = $v[6];
			$color = $v[4];
			$clarity = $v[5];
			$polish = $v[7];
			$fluorescence = $v[8];
			$symmetry = $v[9];
			$cert = $v[10];
			$chengbenjia = $v[12];

			//$model->deletebycert_id($v[11],$v[12]);//删除重复
			
			$jiajialv = $jiajialvModel->getJiajialv($jiajialvList,$v[10],$v[3],1,1,1);
			if(empty($jiajialv)){
						$jiajialv = 0;
//                    $result['error'] = '第'.$k.'行加价率不存在!';
//                    Util::jsonExit($result);
			}
			$v['jiajialv'] = $jiajialv;


			$v['shop_price'] = ceil($chengbenjia*$v['jiajialv']*(SYS_SCOPE == 'zhanting' ? 1.035 : 1));
			$v['market_price'] = ceil($v['shop_price']*1.5);
			$v['member_price'] = ceil($v['shop_price']*0.95);

			if(!in_array($v[1],$this->warehouse)){
//				    $result['error'] = '第'.$k.'行库房不存在';
//				    Util::jsonExit($result);            
			}
			if($v[3]<=0){
//                   $result['error'] = '第'.$k.'行钻石大小错误';
//                   Util::jsonExit($result);
			}
			if($v[11]==''){
//                    $result['error'] = '第'.$k.'行证书号为空';
//                    Util::jsonExit($result);
			}
			if($v[12]<=0){
//                    $result['error'] = '第'.$k.'行成本价错误';
//                    Util::jsonExit($result);
			}elseif($v[12]==''){
//                    $result['error'] = '第'.$k.'行成本价错误';
//                    Util::jsonExit($result);               
			}
			$error=0;
			//list($error,$error_msg) = $model->checkDiamond($shape,$cut,$color,$clarity,$polish,$fluorescence,$symmetry,$cert);
			if($error == 1){
				//$result['error'] = '第'.($k).'行'.implode(',',$error_msg)."不符合要求";
				//Util::jsonExit($result);
			}
			if($v[10]=='HRD-D' && $v[14]==''){
				$result['error'] = '第'.$k.'行不是天生一对';
				Util::jsonExit($result);
			}
			$v[2] = $model->getShapeId($v[2]);
			if(empty($v['2'])){
				$v['2'] = 0;
			}
			$tong[$k+2] = $v;
		}
        $newmodel = new DiamondGetJxcModel(20);
        if($tong){ //更新 

            $newdoList = array();
            foreach($tong as $k=>$v){

               
                
                $shape = $v[2];
                $cut = $v[6];
                $color = $v[4];
                $clarity = $v[5];
                $polish = $v[7];
                $fluorescence = $v[8];
                $symmetry = $v[9];
                $cert = $v[10];

                
                $olddo=array();
                
                $olddo = array();
                
                $newdo = array();
                //$newdo['goods_id'] = null;  不写 自增
                $newdo['goods_sn'] = $v[0];
                $newdo['warehouse'] = isset($this->code[$v[1]])?$this->code[$v[1]]:'COM';
                $newdo['goods_name'] = $v[3]."克拉/ct ".$v[5]."净度 ".$v[4]."颜色 ".$v[6]."切工";
                $newdo['goods_number'] = 1;
                $newdo['good_type'] = 1;
                $newdo['clarity'] = $v[5];
                $newdo['color'] = $v[4];
                $newdo['shape'] = $v[2];
				$newdo['cut'] = $v[6];
                $newdo['polish'] = $v[7];
                $newdo['symmetry'] = $v[9];
                $newdo['fluorescence'] = $v[8];
                $newdo['carat'] = $v[3];
                $newdo['from_ad'] = 1;
                $newdo['chengben_jia'] = $v[12];
                $newdo['market_price'] = $v['market_price'];
                $newdo['shop_price'] = $v['shop_price'];
                $newdo['member_price'] = $v['member_price'];
                $newdo['status'] = 1;
                $newdo['gemx_zhengshu'] = $v[13];
                $newdo['cert_id'] = $v[11];
                $newdo['cert'] = $v[10];
                $newdo['add_time'] = date("Y-m-d H:i:s");
                $newdo['is_active']=1;
                if($v[10]=='HRD-D'){
                    $newdo['kuan_sn']=$v[14];
                }else{
                    $newdo['kuan_sn']='';
                }
                $newdo['mo_sn']=isset($v[15])?$v[15]:'';

                $newdoList[] = $newdo;
            }
        }
        $newdoList = $this->change($newdoList,5000);
        //print_r($newdoList);exit;
        if($newdoList){
            foreach($newdoList as $key => $val){
                $res=$newmodel->insertByGroup($val);
            }
        }

		if ($res !== false) {
			$result['success'] = 1;
			//AsyncDelegate::dispatch('task', array('event' => 'dia_upserted'));
		} else {
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
    }

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
