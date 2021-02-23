<?php
/**
 *  -------------------------------------------------
 *   @file		: AppXiangkouController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-22 23:00:30
 *   @update	:
 *  -------------------------------------------------
 */
class AppXiangkouController extends CommonController
{
	protected $whitelist = array('aa');
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_xiangkou_search_form.html',array('bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
        //获取镶口和指圈
        $id = $params['_id'];
        $baselstyle_view = new BaseStyleInfoView(new BaseStyleInfoModel($id, 11));
        $check_status = $baselstyle_view->get_check_status();
        $cat_type = $baselstyle_view->get_style_type();
        $CNF_SEC_STONE_RANGE = "";
        $CNF_FINGER_RANGE = "";
        
        //如果此款时戒指，那么必须要有指圈，镶口，材质，18K可做颜色
        if(true || $cat_type == 2 || $cat_type == 10 || $cat_type == 11){
            //$data = $this->getRingAttribute($id);
			/*
				为了最快满足业务需求			
			*/
            $data = $this->getRingAttributeTmp($id);
//            var_dump($data);die;
            if($data['error']==1){
		
                echo "<span style='color:red;'>".$data['message']."</span>";
                die;
            }else{
                $CNF_SEC_STONE_RANGE = $data['data']['xk'];
                $CNF_FINGER_RANGE = $data['data']['zq'];
            }
        }
        
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");
			'style_id'=> _Request::getInt('_id'),
			'style_sn'=> _Request::getString('style_sn')

		);
		$page = _Request::getInt("page",1);
		$where = array(
				'style_id'=>$args['style_id'],
				'style_sn'=>$args['style_sn']
			);
		$model = new BaseStyleInfoModel(11);

		$xiangkouModel = new AppXiangkouModel(11);
        $xiangkou = $xiangkouModel->getXiangKouByStyle_sn($where);

		//var_dump($xiangkou);
       
		$stone = array ();
		foreach ( $xiangkou as $val ) {
			$stone[$val['stone']][$val['finger']] = $val;
		}
		$data = $model->pageList($where,$page,10,false);
        
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_xiangkou_search_page';

		$this->render('app_xiangkou_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'cnf_sec_stone_range'=>$CNF_SEC_STONE_RANGE,
			'cnf_finger_range'=>$CNF_FINGER_RANGE,
			'xiangkou'=>$stone,
			'style_id'=>$args['style_id'],
            'check_status' => $check_status
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_xiangkou_info.html',array(
			'view'=>new AppXiangkouView(new AppXiangkouModel(11))
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
		$tab_id = intval($params["tab_id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_xiangkou_info.html',array(
			'view'=>new AppXiangkouView(new AppXiangkouModel($id,11)),
			'tab_id'=>$tab_id
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库，保存录入金重信息；
	 */
	public function insert ($params)
	{
        set_time_limit(0);
		$result = array('success' => 0,'error' =>'');
		$newdo=array();
        $newdo['style_id']= _Request::getInt('style_id');
        $newdo['style_sn'] = _Request::getString('style_sn');
 		$newdo['stone']= explode(',',_Request::getString('stone'));
 		$newdo['finger']= explode(',',_Request::getString('finger'));
 		$newdo['sec_stone_weight']= explode(',',_Request::getString('sec_stone_weight'));
		$newdo['sec_stone_num']= explode(',',_Request::getString('sec_stone_num'));
		$newdo['sec_stone_weight_other']= explode(',',_Request::getString('sec_stone_weight_other'));
		$newdo['sec_stone_num_other']= explode(',',_Request::getString('sec_stone_num_other'));
        $newdo['sec_stone_weight3']= explode(',',_Request::getString('sec_stone_weight3'));
        $newdo['sec_stone_num3']= explode(',',_Request::getString('sec_stone_num3'));
        //$newdo['sec_stone_price_other']=0;//暂时注释
		$newdo['g18_weight']= explode(',',_Request::getString('g18_weight'));
		$newdo['g18_weight_more']= explode(',',_Request::getString('g18_weight_more'));
		$newdo['g18_weight_more2']= explode(',',_Request::getString('g18_weight_more2'));
		$newdo['gpt_weight']= explode(',',_Request::getString('gpt_weight'));
		$newdo['gpt_weight_more']= explode(',',_Request::getString('gpt_weight_more'));
		$newdo['gpt_weight_more2']= explode(',',_Request::getString('gpt_weight_more2'));
		$newdo['gpt_weight_more2']= explode(',',_Request::getString('gpt_weight_more2'));
        $company_type1= explode(';',_Request::getString('company_type'));
        $company_type=array();  
        foreach ($company_type1 as $key => $v) {
        	$sub_company_type=explode(':',$v);
        	$company_type[$sub_company_type[0]]=isset($sub_company_type[1]) ? $sub_company_type[1] : '';
        }

		if($newdo['style_id']=='' || $newdo['style_sn']==''){

			$result['error'] = '款式编号或ID为空，无法提交！';
			Util::jsonExit($result);
		}

        //查询款式是否审核
        $model = new BaseStyleInfoModel(11);
        /*$ret = $model ->getStyleById($newdo['style_id']);

        if($ret['check_status'] != 3){
            
            $result['error'] = '此款还未通过审核，无法提交！';
            Util::jsonExit($result);
        }*/

        //查询改款是否有属性
        $attributeModel = new RelStyleAttributeModel(11);
        $ret = $attributeModel ->getInfoByStyle_sn($newdo['style_id']);
        if(empty($ret)){
            
            $result['error'] = '此款没有设置属性信息，无法提交！';
            Util::jsonExit($result);               
        } 
		
		$newmodel = new AppXiangkouModel(12);

        $sWeight = array();
        $sWeight = $newdo['sec_stone_weight'];
        $res = true;
        //根据主石信息来判断是否填写数据
		foreach($sWeight as $k => $v){

			$where=array(); 
            $where['style_id']=$newdo['style_id'];//款式id
            $where['style_sn']=$newdo['style_sn'];//款号
			$where['stone']=$newdo['stone'][$k];//镶口
			$where['finger']=$newdo['finger'][$k];//手寸
			$where['main_stone_weight']=0.000; //主石重 
			$where['main_stone_num']=0; //主石数 
			$where['sec_stone_weight']=$newdo['sec_stone_weight'][$k]; //副石1重 
			$where['sec_stone_num']=$newdo['sec_stone_num'][$k]; //副石1数量
			$where['sec_stone_weight_other']=$newdo['sec_stone_weight_other'][$k]; //副石2重
			$where['sec_stone_num_other']=$newdo['sec_stone_num_other'][$k];// 副石2数量
            $where['sec_stone_weight3']=$newdo['sec_stone_weight3'][$k]; //副石3重
            $where['sec_stone_num3']=$newdo['sec_stone_num3'][$k];// 副石3数量
			//$where['sec_stone_price_other']='';// 其他副石成本价
			$where['g18_weight']=$newdo['g18_weight'][$k]; //18K标准金重
			$where['g18_weight_more']=$newdo['g18_weight_more'][$k];//18K金重上公差 
			$where['g18_weight_more2']=$newdo['g18_weight_more2'][$k];// 18K金重下公差 
			$where['gpt_weight']=$newdo['gpt_weight'][$k];//PT950标准金重 
			$where['gpt_weight_more']=$newdo['gpt_weight_more'][$k];//PT950金重上公差 
			$where['gpt_weight_more2']=$newdo['gpt_weight_more2'][$k];//PT950金重下公差
			$where['company_type']=!empty($company_type[$where['stone']]) ? ','.$company_type[$where['stone']].',' : '';

            //如果基本信息没有，则跳过；
            if($where['style_sn'] == '' || $where['stone'] == '' || $where['finger'] == ''){

                continue;
            }

            //判断基本信息是否有填写；
			if($where['sec_stone_weight']=='' && $where['sec_stone_weight']=='' && $where['sec_stone_num']=='' && $where['sec_stone_weight_other']=='' && $where['sec_stone_num_other']==''  && $where['sec_stone_weight3']=='' && $where['sec_stone_num3']=='' && $where['g18_weight']=='' && $where['g18_weight_more']=='' && $where['g18_weight_more2']=='' && $where['gpt_weight']=='' && $where['gpt_weight_more']=='' && $where['gpt_weight_more2']==''){


                $del = array();
                $del['style_sn'] = $where['style_sn'];
                $del['stone']    = $where['stone'];
                $del['finger']   = $where['finger'];
                $newmodel->delStyleXiangkouFinger($del);//删除同款同手寸同镶口的数据；
				continue;
			}else{
				
				if($where['sec_stone_weight']==''){
					$result['error'] = '提交失败，镶口'.$where['stone'].'中手寸为'.$where['finger'].'的副石1重不可为空，若没有请补充0！';
					Util::jsonExit($result);
				}if($where['sec_stone_num']==''){
					$result['error'] = '提交失败，镶口'.$where['stone'].'中手寸为'.$where['finger'].'的副石1数量不可为空，若没有请补充0！';
					Util::jsonExit($result);
				}if($where['sec_stone_weight_other']==''){
					$result['error'] = '提交失败，镶口'.$where['stone'].'中手寸为'.$where['finger'].'的副石2重不可为空，若没有请补充0！';
					Util::jsonExit($result);
				}if($where['sec_stone_num_other']==''){
					$result['error'] = '提交失败，镶口'.$where['stone'].'中手寸为'.$where['finger'].'的副石2数量不可为空，若没有请补充0！';
					Util::jsonExit($result);
				}if($where['sec_stone_weight3']==''){
                    $result['error'] = '提交失败，镶口'.$where['stone'].'中手寸为'.$where['finger'].'的副石3重不可为空，若没有请补充0！';
                    Util::jsonExit($result);
                }if($where['sec_stone_num3']==''){
                    $result['error'] = '提交失败，镶口'.$where['stone'].'中手寸为'.$where['finger'].'的副石3数量不可为空，若没有请补充0！';
                    Util::jsonExit($result);
                }
                /*if($where['sec_stone_price_other']==''){
					$result['error'] ='镶口'.$where['stone'].'中手寸为'.$where['finger']. '的其他副石成本价不可为空！';
					Util::jsonExit($result);
				}*/
                if($where['g18_weight']==''){
					$result['error'] = '提交失败，镶口'.$where['stone'].'中手寸为'.$where['finger'].'的18K标准金重不可为空，若没有请补充0！';
					Util::jsonExit($result);
				}if($where['g18_weight_more']==''){
					$result['error'] = '提交失败，镶口'.$where['stone'].'中手寸为'.$where['finger'].'的18K金重上公差不可为空，若没有请补充0！';
					Util::jsonExit($result);
				}if($where['g18_weight_more2']==''){
					$result['error'] = '提交失败，镶口'.$where['stone'].'中手寸为'.$where['finger'].'的18K金重下公差不可为空，若没有请补充0！';
					Util::jsonExit($result);
				}if($where['gpt_weight']==''){
					$result['error'] = '提交失败，镶口'.$where['stone'].'中手寸为'.$where['finger'].'的PT950标准金重不可为空，若没有请补充0！';
					Util::jsonExit($result);
				}if($where['gpt_weight_more']==''){
					$result['error'] ='提交失败，镶口'.$where['stone'].'中手寸为'.$where['finger']. '的PT950金重上公差不可为空，若没有请补充0！';
					Util::jsonExit($result);
				}if($where['gpt_weight_more2']==''){
					$result['error'] ='提交失败，镶口'.$where['stone'].'中手寸为'.$where['finger']. '的PT950金重下公差不可为空，若没有请补充0！';
					Util::jsonExit($result);
				}
			}

            $del_all = array();
            $del_all['style_sn'] = $where['style_sn'];
            $del_all['stone']    = $where['stone'];
            $del_all['finger']   = $where['finger'];
            $newmodel->delStyleXiangkouFinger($del_all);//删除同款同手寸同镶口的数据；
            
            //插入金重信息
            $olddo = array();
			$res = $newmodel->saveData($where,$olddo);
		}
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '添加失败';
		}
		$model->addBaseStyleLog(array('style_id'=>$newdo['style_id'],'remark'=>'商品属性提交成功'));
		Util::jsonExit($result);
	}

    function aa(){
	$list = array(86014,86015,86016);

	foreach($list as $key => $id){
	$data = unserialize(file_get_contents("http://bossgate.kela.cn/getStyleXiangkou.php?id=".$id));
	if(empty($data)){
		continue;
	}
	$_REQUEST['style_id'] = $data['style_id'];
	$_REQUEST['style_sn'] = $data['style_sn'];
	$_REQUEST['stone_select'] = $data['stone'];
	$_REQUEST['finger'] = array($data['finger']);
	$_REQUEST['sec_stone_weight'] = array($data['sec_stone_weight']);
	$_REQUEST['sec_stone_num'] = array($data['sec_stone_num']);
	$_REQUEST['sec_stone_weight_other'] = array($data['sec_stone_weight_other']);
	$_REQUEST['sec_stone_num_other'] = array($data['sec_stone_num_other']);
	$_REQUEST['sec_stone_weight3'] = array($data['sec_stone_weight3']);
	$_REQUEST['sec_stone_num3'] = array($data['sec_stone_num3']);
	$_REQUEST['g18_weight'] = array($data['g18_weight']);
	$_REQUEST['g18_weight_more'] = array($data['g18_weight_more']);
	$_REQUEST['g18_weight_more2'] = array($data['g18_weight_more2']);
	$_REQUEST['gpt_weight'] = array($data['gpt_weight']);
	$_REQUEST['gpt_weight_more'] = array($data['gpt_weight_more']);
	$_REQUEST['gpt_weight_more2'] = array($data['gpt_weight_more2']);
	$this->createGoods($_REQUEST);
	}
    }
    
    function createGoods($param) {
    	set_time_limit(0);
        //$style_sn = _Request::getString('style_sn');
        $result = array('success' => 0,'error' =>'');
        $style_id = _Request::getInt('style_id');
        $where['stone'] = _Request::getString('stone_select');
        $where['finger'] = _Request::getList('finger');
        $where['sec_stone_weight'] = _Request::getList('sec_stone_weight');
        $where['sec_stone_num'] = _Request::getList('sec_stone_num');
        $where['sec_stone_weight_other'] = _Request::getList('sec_stone_weight_other');
        $where['sec_stone_num_other'] = _Request::getList('sec_stone_num_other');
        $where['sec_stone_weight3'] = _Request::getList('sec_stone_weight3');
        $where['sec_stone_num3'] = _Request::getList('sec_stone_num3');
        //$where['sec_stone_price_other'] = _Request::getList('sec_stone_price_other');
        $where['g18_weight'] = _Request::getList('g18_weight');
        $where['g18_weight_more'] = _Request::getList('g18_weight_more');
        $where['g18_weight_more2'] = _Request::getList('g18_weight_more2');
        $where['gpt_weight'] = _Request::getList('gpt_weight');
        $where['gpt_weight_more'] = _Request::getList('gpt_weight_more');
        $where['gpt_weight_more2'] = _Request::getList('gpt_weight_more2');

        //如果不是定制时不可以生成商品的
        $baseStyleModel = new BaseStyleInfoModel($style_id,11);
        $style_info = array();
        $style_info = $baseStyleModel->getDataObject();

        //通过审核
        if(!empty($style_info) && $style_info['check_status'] != 3){

            $result['error'] = $_REQUEST['style_sn'].'此款未通过审核！';
            echo $_REQUEST['style_sn'].'此款未通过审核！';
            return true;
            Util::jsonExit($result);
        }

        //定制商品
        if($style_info['is_made'] == 0){

            return false;
            $result['error'] = $_REQUEST['style_sn'].'此款不是定制商品，不可以生成商品！';
            Util::jsonExit($result);
        }

        //查询改款是否有属性
        $attributeModel = new RelStyleAttributeModel(11);
        $attrInfo = $attributeModel ->getInfoByStyle_sn($style_id);
        if(empty($attrInfo)){
            
            echo $_REQUEST['style_sn'].'请先添加属性信息！';
            Util::jsonExit($result);
        }
        
		$xiangkouModel = new AppXiangkouModel(11);
        $newInfo=array();
        foreach($where['finger'] as $k=>$v){ 
        	//var_dump($where['finger'][$k]);
            if($where['sec_stone_weight'][$k]){
                $newInfo['sec_stone_weight'][]=$where['sec_stone_weight'][$k];
                $newInfo['sec_stone_weight']['finger']=$v;
            }
            if($where['sec_stone_num'][$k]){
                $newInfo['sec_stone_num'][]=$where['sec_stone_num'][$k];
                $newInfo['sec_stone_num']['finger']=$v;
            }
            if($where['sec_stone_weight_other'][$k]){
                $newInfo['sec_stone_weight_other'][]=$where['sec_stone_weight_other'][$k];
                $newInfo['sec_stone_weight_other']['finger']=$v;
            }
            if($where['sec_stone_num_other'][$k]){
                $newInfo['sec_stone_num_other'][]=$where['sec_stone_num_other'][$k];
                $newInfo['sec_stone_num_other']['finger']=$v;
            }
            if($where['sec_stone_weight3'][$k]){
                $newInfo['sec_stone_weight3'][]=$where['sec_stone_weight3'][$k];
                $newInfo['sec_stone_weight3']['finger']=$v;
            }
            if($where['sec_stone_num3'][$k]){
                $newInfo['sec_stone_num3'][]=$where['sec_stone_num3'][$k];
                $newInfo['sec_stone_num3']['finger']=$v;
            }
//            if($where['sec_stone_price_other'][$k]){
//                $newInfo['sec_stone_price_other'][]=$where['sec_stone_price_other'][$k];
//                $newInfo['sec_stone_price_other']['finger']=$v;
//            }
            if($where['g18_weight'][$k]){
                $newInfo['g18_weight'][]=$where['g18_weight'][$k];
                $newInfo['g18_weight']['finger']=$v;
            }
            if($where['g18_weight_more'][$k]){
                $newInfo['g18_weight_more'][]=$where['g18_weight_more'][$k];
                $newInfo['g18_weight_more']['finger']=$v;
            }
            if($where['g18_weight_more2'][$k]){
                $newInfo['g18_weight_more2'][]=$where['g18_weight_more2'][$k];
                $newInfo['g18_weight_more2']['finger']=$v;
            }
            if($where['gpt_weight'][$k]){
                $newInfo['gpt_weight'][]=$where['gpt_weight'][$k];
                $newInfo['gpt_weight']['finger']=$v;
            }
            if($where['gpt_weight_more'][$k]){
                $newInfo['gpt_weight_more'][]=$where['gpt_weight_more'][$k];
                $newInfo['gpt_weight_more']['finger']=$v;
            }
            if($where['gpt_weight_more2'][$k]){
                $newInfo['gpt_weight_more2'][]=$where['gpt_weight_more2'][$k];
                $newInfo['gpt_weight_more2']['finger']=$v;
            }
        }

        //检查是否新录入数据
        /*if($newInfo){

            foreach($newInfo as $k => $v){

                $cNt=array();
                $stone = '';
                $finger = '';
                $stone = $where['stone'];
                $finger= $v['finger'];

                $cNt['style_id'] = $style_id;
                $cNt['stone']    = $stone;
                $cNt['finger']   = $finger;

                $xiangkou = $xiangkouModel->getXiangKouByStyle_Id($cNt);
                if(empty($xiangkou)){     

                    $result['error'] = '有录入新数据，请先提交信息！';
                    Util::jsonExit($result);               
                }
            }
        }*/

        $is_error = true;
        $error = array();
        $is_mark = 0;
        
        foreach ($where['finger'] as $k=>$val){

            if(trim($where['sec_stone_weight'][$k]) == ''){

                $is_mark++;
                continue;
            }

            if($where['sec_stone_num'][$k]==""){

                 $error[$val] = "副石1数量不能为空";
                 $is_error = false;
            }
        }

        //没有提交任何数据
        if(count($where['finger']) == $is_mark){
             $result['error'] = '没有提交任何数据，请填写数据！';
             Util::jsonExit($result);
        }
        
        //当副石没有数据
        if(!$is_error){

            $error_info = "";
            foreach ($error as $x_key => $k_val){

                $error_info .= "此镶口:".$where['stone'].",此手寸的:".$x_key.",".$k_val."!\n";
            }

            $result['error'] = $error_info;
            Util::jsonExit($result);
        }

        $cat_type = $style_info['style_type'];
        $style_xiangkou = array();
        $style_zhiquan = array();
        $style_caizhi = array();
        $style_yanse = array();

        //如果此款时戒指，那么必须要有指圈，镶口，材质，18K可做颜色
        if(true || $cat_type == 2 || $cat_type == 10 || $cat_type == 11 ){

            //$data = $this->getRingAttribute($style_id);
            $data = $this->getRingAttributeTmp($style_id);
            if($data['error']==1){

                $result['error'] = $data['message'];
                Util::jsonExit($result);
            }else{

                //取出都是款的属性对应的属性值的id，并不是描述，所以需要在转化成描述
                $style_xiangkou = $data['data']['xk'];
                $style_zhiquan = $data['data']['zq'];
                $style_caizhi = $data['data']['cz'];
                $style_yanse = $data['data']['ys'];
            }
        }

        //临时加的限制（目前只会生成18k和pt950的商品）
        /*if(!in_array('18K', $style_caizhi) && !in_array('PT950', $style_caizhi)){
            $result['error'] = '系统目前只支持18K、PT950两种材质生成商品，请在属性信息里设置！';
            Util::jsonExit($result);
        }*/

        //删除原来的数据
        //$listStyleModel = new ListStyleGoodsModel(11);
        //$listStyleModel->deleteStyleList(array('style_id'=>$style_id));

        $stone_key = array_flip($style_xiangkou);
        $_key = $stone_key[$where['stone']];
        $xiangkou_num = count($style_xiangkou);
        if($xiangkou_num == 0){
            $num = 0;
        }else {
            $num = count($where['sec_stone_weight'])/$xiangkou_num;
        }
        
        $_where = array();
        foreach ($where as $k=>$v){

            if($k=='stone'){

                $val = $v;
	    }else{

                $val = array_chunk($v, $num);
                $val = $val[$_key];
            }

            $_where[$k] = $val;
        }

        foreach ($_where['sec_stone_weight'] as $a => $b) {
            # code...
            if($b === ''){
                unset($_where['finger'][$a]);
            }
        }

        $_where['finger_old']=$_where['finger'][0];
        //把手寸数据6-8 转换成6，7，8
        $_where['finger'] = $this->cutFingerInfo($_where['finger']);
        //切割数据 end
        $attributeValueModel = new AppAttributeValueModel(11);
        //$caizhi_arr = $attributeValueModel->getCaizhi();
        $color_arr = $attributeValueModel->getColor();
        $color_value_arr = $attributeValueModel->getColorValue();

        $is_flag = false;
        $res1['num'] = 0; 
        $res2['num'] = 0;
        //18K
        if(in_array("18K", $style_caizhi)){
            $is_flag = true;

            $yanse_data = array();
            foreach ($style_yanse as $val){

                if(array_key_exists($val, $color_arr)){
                    $yanse_data[$color_value_arr[$val]] = $color_arr[$val];
                }
            }
            $caizhi = array('id'=>1,'name'=>"18K");
            $res1 = $this->create_goods_insert($style_info, $_where, $caizhi, $yanse_data);
        }
       
        //PT950
        if(in_array("PT950", $style_caizhi)){
            $is_flag = true;

            //只有一个颜色那就是白色
            $yanse_data_pt = array();
            $yanse_data_pt[$color_value_arr["白"]] = $color_arr["白"];
            $caizhi = array('id'=>2,'name'=>"PT950");
            $res2 = $this->create_goods_insert($style_info, $_where, $caizhi, $yanse_data_pt);
        }
        
        if($is_flag){

            $num = $res1['num'] + $res2['num'];
            $result['error'] = "操作成功,一共生成".$num."条SKU。";
            $result['success'] = 1;
        }else{

            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

    //全部生成商品信息
    public function createGoodsAll($param)
    {
        # code...
        set_time_limit(0);
        $result = array('success' => 0,'error' =>'');

        $goodsInfo=array();
        $style_id = _Request::getInt('style_id');
        $style_sn = _Request::getString('style_sn');
        $goodsInfo['stone']= explode(',',_Request::getString('stone'));
        $goodsInfo['finger']= explode(',',_Request::getString('finger'));
        $goodsInfo['sec_stone_weight']= explode(',',_Request::getString('sec_stone_weight'));
        $goodsInfo['sec_stone_num']= explode(',',_Request::getString('sec_stone_num'));
        $goodsInfo['sec_stone_weight_other']= explode(',',_Request::getString('sec_stone_weight_other'));
        $goodsInfo['sec_stone_num_other']= explode(',',_Request::getString('sec_stone_num_other'));
        $goodsInfo['sec_stone_weight3']= explode(',',_Request::getString('sec_stone_weight3'));
        $goodsInfo['sec_stone_num3']= explode(',',_Request::getString('sec_stone_num3'));
        $goodsInfo['g18_weight']= explode(',',_Request::getString('g18_weight'));
        $goodsInfo['g18_weight_more']= explode(',',_Request::getString('g18_weight_more'));
        $goodsInfo['g18_weight_more2']= explode(',',_Request::getString('g18_weight_more2'));
        $goodsInfo['gpt_weight']= explode(',',_Request::getString('gpt_weight'));
        $goodsInfo['gpt_weight_more']= explode(',',_Request::getString('gpt_weight_more'));
        $goodsInfo['gpt_weight_more2']= explode(',',_Request::getString('gpt_weight_more2'));

        //获取此款款式信息
        $baseStyleModel = new BaseStyleInfoModel($style_id,11);
        $dostyleinfo = $baseStyleModel->getDataObject();

        //查询该款是否审核通过，未通过不可以生成商品
        if($dostyleinfo['check_status'] != 3){
            $result['error'] = $style_sn.'，此款未通过审核，不可以生成商品！';
            Util::jsonExit($result);
        }

        //判断是否定制商品，不是定制商品不可生成商品
        if($dostyleinfo['is_made'] == 0){
            $result['error'] = $style_sn.'，此款不是定制商品，不可以生成商品！';
            Util::jsonExit($result);  
        }

        //判断该款是否有属性信息
        $attributeModel = new RelStyleAttributeModel(11);
        $goodsList = $attributeModel ->getInfoByStyle_sn($style_id);
        if(count($goodsList) == 0){
            $result['error'] = $style_sn.'此款没有属性信息，请先添加属性信息！';
            Util::jsonExit($result);  
        }

        //检测是否有新录入数据，先提交信息
        $data = array();
        $stoneAll = array();
        $stoneAll = $goodsInfo['stone'];
        if(!empty($stoneAll)){

            array_pop($stoneAll);
        }

        //改变结构检测
        foreach ($stoneAll as $k => $v) {
            # code...
            if($goodsInfo['sec_stone_weight'][$k]){
                $data['sec_stone_weight'][$k]['stone']=$v;
                $data['sec_stone_weight'][$k]['finger']=$goodsInfo['finger'][$k];
            }if($goodsInfo['sec_stone_num'][$k]){
                $data['sec_stone_num'][$k]['stone']=$v;
                $data['sec_stone_num'][$k]['finger']=$goodsInfo['finger'][$k];
            }if($goodsInfo['sec_stone_weight_other'][$k]){
                $data['sec_stone_weight_other'][$k]['stone']=$v;
                $data['sec_stone_weight_other'][$k]['finger']=$goodsInfo['finger'][$k];
            }if($goodsInfo['sec_stone_num_other'][$k]){
                $data['sec_stone_num_other'][$k]['stone']=$v;
                $data['sec_stone_num_other'][$k]['finger']=$goodsInfo['finger'][$k];
                $data['sec_stone_num_other'][$k][]=$goodsInfo['sec_stone_num_other'][$k];
            }if($goodsInfo['sec_stone_weight3'][$k]){
                $data['sec_stone_weight3'][$k]['stone']=$v;
                $data['sec_stone_weight3'][$k]['finger']=$goodsInfo['finger'][$k];
                $data['sec_stone_weight3'][$k][]=$goodsInfo['sec_stone_weight3'][$k];
            }if($goodsInfo['sec_stone_num3'][$k]){
                $data['sec_stone_num3'][$k]['stone']=$v;
                $data['sec_stone_num3'][$k]['finger']=$goodsInfo['finger'][$k];
            }if($goodsInfo['g18_weight'][$k]){
                $data['g18_weight'][$k]['stone']=$v;
                $data['g18_weight'][$k]['finger']=$goodsInfo['finger'][$k];
            }if($goodsInfo['g18_weight_more'][$k]){
                $data['g18_weight_more'][$k]['stone']=$v;
                $data['g18_weight_more'][$k]['finger']=$goodsInfo['finger'][$k];
            }if($goodsInfo['g18_weight_more2'][$k]){
                $data['g18_weight_more2'][$k]['stone']=$v;
                $data['g18_weight_more2'][$k]['finger']=$goodsInfo['finger'][$k];
            }if($goodsInfo['gpt_weight'][$k]){
                $data['gpt_weight'][$k]['stone']=$v;
                $data['gpt_weight'][$k]['finger']=$goodsInfo['finger'][$k];
            }if($goodsInfo['gpt_weight_more'][$k]){
                $data['gpt_weight_more'][$k]['stone']=$v;
                $data['gpt_weight_more'][$k]['finger']=$goodsInfo['finger'][$k];
            }if($goodsInfo['gpt_weight_more2'][$k]){
                $data['gpt_weight_more2'][$k]['stone']=$v;
                $data['gpt_weight_more2'][$k]['finger']=$goodsInfo['finger'][$k];
            }
        }

        //判断信息是否都为空
        if(empty($data)){

            $result['error'] = '未检测出数据，请填写信息！';
            Util::jsonExit($result); 
        }
        
        //查询是否信息都已提交
        $xiangkouModel = new AppXiangkouModel(11);
        foreach ($data as $key => $val) {
            # code...
            foreach ($val as $k => $v) {
                # code...
                $ls['style_id'] = $style_id;
                $ls['stone'] = $v['stone'];
                $ls['finger'] = $v['finger'];
                $xiangkou = $xiangkouModel->getXiangKouByStyle_Id($ls);
                if(empty($xiangkou)){
                    $result['error'] = '有录入新数据，请先提交信息！';
                    Util::jsonExit($result); 
                }
            }
        }

        $jinZongInfo = array();
        $styleId     = array();
        $styleId['style_id'] = $style_id;
        $jinZongInfo = $xiangkouModel->getXiangKouByStyle_Id($styleId);

        $style_xiangkou = array();
        $style_zhiquan = array();
        $style_caizhi = array();
        $style_yanse = array();
        $info = $this->getRingAttributeTmp($style_id);
        if($info['error'] == 1){
            $result['error'] = $info['message'];
            Util::jsonExit($result);
        }else{
            //取出都是款的属性对应的属性值的id，并不是描述，所以需要在转化成描述
            $style_xiangkou = $info['data']['xk'];
            $style_zhiquan = $info['data']['zq'];
            $style_caizhi = $info['data']['cz'];
            $style_yanse = $info['data']['ys'];
        }

        //取出所以的材质和颜色信息
        $attributeValueModel = new AppAttributeValueModel(11);
        $color_arr = $attributeValueModel->getColor();
        $color_value_arr = $attributeValueModel->getColorValue();
        $is_flag = false;
        $res1_num = 0;
        $res2_num = 0;
        array_filter($style_yanse);
        foreach ($jinZongInfo as $keys => $jinzong) {

            //18K
            if(in_array("18K", $style_caizhi) && $jinzong['g18_weight'] != '0'){
                $is_flag = true;
                foreach ($style_yanse as $vals){
                    if(array_key_exists($vals, $color_arr)){
                        $yanse_data[$color_value_arr[$vals]] = $color_arr[$vals];
                    }
                }
                $caizhi = array('id'=>1,'name'=>"18K");
                $res1_num += $this->create_goods_insert_all($dostyleinfo, $jinzong, $caizhi, $yanse_data);
            }
           
            //PT950
            if(in_array("PT950", $style_caizhi) && $jinzong['gpt_weight'] != '0'){
                $is_flag = true;
                //只有一个颜色那就是白色
                $yanse_data_pt[$color_value_arr["白"]] = $color_arr["白"];
                $caizhi = array('id'=>2,'name'=>"PT950");
                $res2_num += $this->create_goods_insert_all($dostyleinfo, $jinzong, $caizhi, $yanse_data_pt);
            }
        }

        if($is_flag){
            $num = $res1_num + $res2_num;
            $result['error'] = "操作成功,一共生成".$num."条SKU。";
           //添加日志
            $model = new BaseStyleInfoModel(11);
            $model->addBaseStyleLog(array('style_id'=>$style_id,'remark'=>'全部生成商品成功'));
        }else{
            $result['error'] = '添加失败';
        }
       // var_dump($result);exit;
        Util::jsonExit($result);
    }
    
    //生成除戒指的商品信息
    function createOtherGoods($param) {
    	//var_dump($_REQUEST);exit;
    	$style_id =_Request::get('style_id');
    	$jinzhong =_Request::get('jinzhong');
    	$chengben =_Request::get('chengben');
    	$type =strtoupper(_Request::get('type'));
    	
    	
    	if(empty($style_id)){
    		$result['error'] = '款号为空 ,不可以生成商品！';
    		Util::jsonExit($result);
    	}
    	//获取款式信息
    	$styleModel = new BaseStyleInfoModel(11);
    	$style_info = $styleModel->getStyleById($style_id);
        if($style_info['check_status'] != 3){
            $result['error'] = '款式未审核，不可以生成商品！';
            Util::jsonExit($result);
        }
        
         //如果不是定制时不可以生成商品的
        if($style_info['is_made']== 0){
            $result['error'] = '此款不是定制商品，不可以生成商品！';
            Util::jsonExit($result);   
        }
        
        //判断此款是否含有材质信息
        $attributeModel = new AppAttributeModel(11);
        $caizhi_data = $attributeModel->getAttributeInfoByName('材质');
        if(empty($caizhi_data)){
           $result['error'] = '请查看属性设置中是否存在：材质！';
           Util::jsonExit($result);
        }
        
        $relStyleModel = new RelStyleAttributeModel(11);
        $cz_id = $caizhi_data['attribute_id'];
        //材质
        $cz_data = $relStyleModel->getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$cz_id));
        if(empty($cz_data)){
            $result['error'] = '此款没有设置：材质属性!此款没有设置：材质属性!';
            Util::jsonExit($result);
        }
        if(empty($cz_data['attribute_value'])){
            $result['error'] = '此款没有选择：材质数据!';
            Util::jsonExit($result);
        }
	     $cz_info = explode(",",rtrim($cz_data['attribute_value'],","));
	    
        $attributeValueModel = new AppAttributeValueModel(11);
        //材质
        $style_caizhi = array();
        foreach ($cz_info as $val){
            $value = $attributeValueModel->getAttrNameByid(array('att_value_id'=>$val));
            $style_caizhi[]=$value['att_value_name'];
        }
       
        //判断K金，pt，足金
        $is_mark = false;
        foreach ($style_caizhi as $val){
            if(strpos($val, $type) !== FALSE){
                $is_mark = true;
            }
            if($type == "S" && $val=="千足银"){
                $is_mark = true;
            }
            if($type == "ZJ" && strpos($val, "足金") !== FALSE){
                $is_mark = true;
            }
        }
        
        if(!$is_mark){
            $type_name = $this->getTypeName($type);
            $str = "此款属性：材质没有选择".$type_name;
            $result['error'] = $str;
            Util::jsonExit($result);
        }
        
        $caizhi_arr = $this->getCaiZhiOther($type);
        $caizhi = $caizhi_arr['id'];
        $caizhi_name = $caizhi_arr['name'];
        $goods_sn = $style_info['style_sn']."-".$caizhi_name;
        //如果数据已经存在更新
        $listStyleModel = new ListStyleGoodsModel(11);
        $style_data = $listStyleModel->getListStyleGoodsByWhere(array('style_id'=>$style_id,'caizhi'=>$caizhi,'weight'=>$jinzhong));
        $model = new ListStyleGoodsModel(12);
        
        if($style_data){
            //更新数据
            $res = $model->UpdateListStyleGoodsInfo(array('style_id'=>$style_id,'caizhi'=>$caizhi,'weight'=>$jinzhong,'chengben'=>$chengben));
        }else{
            
            $olddo = array();
            $newdo = array();
            //$newdo['goods_id'] = $style_info['style_id'];
            $newdo['goods_sn'] = $goods_sn;
            $newdo['style_id'] = $style_info['style_id'];
            $newdo['style_sn'] = $style_info['style_sn'];
            $newdo['style_name'] = $style_info['style_name'];
            $newdo['product_type_id'] =$style_info['product_type'];
            $newdo['cat_type_id'] = $style_info['style_type'];
            $newdo['last_update'] = date("Y-m-d H:i:s");

            //开始 默认值 ==0
            $newdo['xiangkou'] =0;
            $newdo['shoucun'] =0;
            $newdo['caizhi'] =$caizhi;
            $newdo['yanse'] =0;
            $newdo['zhushizhong'] =0;
            $newdo['zhushi_num'] =0;
            $newdo['fushizhong1'] =0;
            $newdo['fushi_num1'] =0;
            $newdo['fushizhong2'] =0;
            $newdo['fushi_num2'] =0;
            $newdo['fushi_chengbenjia_other'] =0;
            $newdo['weight'] =$jinzhong;
            $newdo['jincha_shang'] =0;
            $newdo['jincha_xia'] =0;
            $newdo['dingzhichengben'] =$chengben;
            //结束
           
            $res = $model->saveData($newdo, $olddo);
        }
    	if($res){
    			$apiSalePolicyModel = new ApiSalePolicyModel();
    			$salepolicy_data = array('goods_id'=>$goods_sn,'goods_sn'=>$style_info['style_sn'],'goods_name'=>$style_info['style_name'],'chengbenjia'=>$chengben,'category'=>$style_info['style_type'],'product_type'=>$style_info['product_type'],'isXianhuo'=>0);
    			$apiSalePolicyModel->AddAppPayDetail(array('insert_data'=>$salepolicy_data));
    	}
    	if ($res !== false) {
    		$result['success'] = 1;
    	} else {
    		$result['error'] = '添加失败';
    	}
    	Util::jsonExit($result);
    	
    }

    public function create_goods_insert_all($style_info,$xiangkou,$caizhi_info,$color_arr)
    {
        # code...
        //echo '<pre>';
        //var_dump($style_info,$xiangkou,$caizhi_info,$color_arr);die;
        $newmodel = new ListStyleGoodsModel(12); 
        $apiSalePolicyModel = new ApiSalePolicyModel();

        $stone = $xiangkou['stone'];
        $finger = $xiangkou['finger'];
        $style_id = $style_info['style_id'];
        $style_sn = $style_info['style_sn'];
        $style_name = $style_info['style_name'];
        $product_type_id = $style_info['product_type'];
        $cat_type_id = $style_info['style_type'];
        $caizhi = $caizhi_info['id'];
        $caizhi_name = $caizhi_info['name'];

        $cut_finger = array();
        $cut_finger = $this->cutFingerInfoAll($finger);
        //echo '<pre>';
        //print_r($color_arr);die;
        $olddo = array();
        $num = 0;
        $insert_data=array();
        
        foreach ($color_arr as $ys_key => $ys_val) {
            # code...
            $color_name = $ys_val;
            $where['style_id']=$style_id;
            $where['style_sn']=$style_sn;
            $where['product_type_id']=$product_type_id;//产品线id
            $where['cat_type_id']=$cat_type_id;//分类id
            $where['style_name'] = $style_name;//款式名称
            $where['caizhi']=$caizhi;//材质
            $where['yanse']=$ys_key;//镶口
            $where['xiangkou'] = $stone;//镶口
            
            $where['zhushizhong']=$stone; //主石重 
            $where['zhushi_num']=0; //主石数 
            $where['fushizhong1']=$xiangkou['sec_stone_weight']; //副石1重 
            $where['fushi_num1']=$xiangkou['sec_stone_num']; //副石1数量
            $where['fushizhong2']=$xiangkou['sec_stone_weight_other']; //副石2重
            $where['fushi_num2']=$xiangkou['sec_stone_num_other'];// 副石2数量
            $where['fushizhong3']=$xiangkou['sec_stone_weight3']; //副石2重
            $where['fushi_num3']=$xiangkou['sec_stone_num3'];// 副石2数量
            //$where['fushi_chengbenjia_other']=$xiangkou['sec_stone_price_other'][$k];// 其他副石成本价
            $where['dingzhichengben']=601;// 定制成本
            if($caizhi == 1){
                $where['weight']=$xiangkou['g18_weight']; //18K标准金重
                $where['jincha_shang']=$xiangkou['g18_weight_more'];//18K金重上公差 
                $where['jincha_xia']=$xiangkou['g18_weight_more2'];// 18K金重下公差 
            }else{
                $where['weight']=$xiangkou['gpt_weight'];//PT950标准金重 
                $where['jincha_shang']=$xiangkou['gpt_weight_more'];//PT950金重上公差 
                $where['jincha_xia']=$xiangkou['gpt_weight_more2'];//PT950金重下公差
            }
           
            $where['last_update']=date("Y-m-d H:i:s");
            
            if($xiangkou['sec_stone_weight_other']==""){
                 $where['fushizhong2']=0; //副石2重
            }
            if($xiangkou['sec_stone_num_other']==""){
                 $where['fushi_num2']=0;// 副石2数量
            }
            if($xiangkou['sec_stone_weight3']==""){
                 $where['fushizhong3']=0; //副石3重
            }
            if($xiangkou['sec_stone_num3']==""){
                 $where['fushi_num3']=0;// 副石3数量
            }
            /*if($xiangkou['sec_stone_price_other'][$k]==""){
                 $where['fushi_chengbenjia_other'] =0;// 其他副石成本价
            }*/
            $where['fushi_chengbenjia_other'] =0;// 其他副石成本价
            if($caizhi == 1){
                if($xiangkou['g18_weight']==""){
                    $where['weight'] =0;// 18K标准金重
                }
                if($xiangkou['g18_weight_more']==""){
                    $where['jincha_shang'] =0;// 18K金重上公差 
                }
                if($xiangkou['g18_weight_more2']==""){
                    $where['jincha_xia'] =0;// 18K金重下公差
                }
            }else{
                if($xiangkou['gpt_weight']==""){
                    $where['weight'] =0;// PT950标准金重 
                }
                if($xiangkou['gpt_weight_more']==""){
                    $where['jincha_shang'] =0;// //PT950金重上公差
                }
                if($xiangkou['gpt_weight_more2']==""){
                    $where['jincha_xia'] =0;// //PT950金重下公差
                }  
            }

            
            $where['xiangkou_company_type']=$xiangkou['company_type']; //可销售公司类型 
            //循环指圈
            foreach ($cut_finger as $f_val){

                $shoucun = $f_val;
                $where['shoucun']=$shoucun;//手寸
                $stone_name = $stone * 100;
                $goods_sn = $style_sn."-".$caizhi_name."-".$color_name."-".$stone_name."-".$shoucun;
                $quickDiyGoods = $newmodel->getQuickDiyGoodsByGoodsSn($goods_sn);
                if(!empty($quickDiyGoods)){                
                  $newmodel->deletegoods_sninfo($goods_sn);
                }
                $where['is_quick_diy'] = $quickDiyGoods['is_quick_diy']==1?1:0;
                $where['goods_sn'] = $goods_sn;
                $num++;
                // $salepolicy_data = array('goods_id'=>$goods_sn,'goods_sn'=>$style_sn,'goods_name'=>$style_name,'chengbenjia'=>$where['dingzhichengben'],'category'=>$cat_type_id,'product_type'=>$product_type_id,'isXianhuo'=>0);
                //  $apiSalePolicyModel->AddAppPayDetail(array('insert_data'=>$salepolicy_data));
                //$res = $newmodel->saveData($where,$olddo); 
                $insert_data[]=$where; 
            }
        }
        $newmodel->insertAll($insert_data,'list_style_goods'); 
        $this->update_goods_price($style_id,$caizhi,$stone);
        /*
        $new2model = new ListStyleGoodsModel(11); 
        $goods_sn_arr = $new2model->getAllGoodsinfo($style_id,$caizhi,$stone);
        foreach($goods_sn_arr as $val){
            $salepolicy_data = array('goods_id'=>$val['goods_sn'],'goods_sn'=>$val['style_sn'],'goods_name'=>$val['style_name'],'chengbenjia'=>$val['dingzhichengben'],'category'=>$val['cat_type_id'],'product_type'=>$val['product_type_id'],'isXianhuo'=>0,'is_base_style'=>0,'xiangkou'=>$val['xiangkou'],'caizhi'=>$val['caizhi'],'yanse'=>$val['yanse']);
            $apiSalePolicyModel->AddAppPayDetail(array('insert_data'=>$salepolicy_data));
        }*/
        return $num;
    }
    
    
    public function create_goods_insert($style_info,$xiangkou,$caizhi_info,$color_arr){

        $newmodel = new ListStyleGoodsModel(12); 
        //$apiSalePolicyModel = new ApiSalePolicyModel();
        $xiangkouModel = new AppXiangkouModel(11);
        $caizhi = $caizhi_info['id'];
        $caizhi_name = $caizhi_info['name'];
        
        $stone = $xiangkou['stone'];
        $style_id = $style_info['style_id'];
        $style_sn = $style_info['style_sn'];
        $style_name = $style_info['style_name'];
        $product_type_id = $style_info['product_type'];
        $cat_type_id = $style_info['style_type'];
        
        $olddo = array();
        $num = 0;
      
        $insert_data=array();
        $xiangkou_company_type='';
	    $xiangkou_company_type_array=$xiangkouModel->getXiangKouByStyle_Id(array('style_id'=>$style_id,'stone'=>$stone,'finger'=>$xiangkou['finger_old']));
	    if($xiangkou_company_type_array){
	        $xiangkou_company_type_array=array_pop($xiangkou_company_type_array);  
            $xiangkou_company_type=$xiangkou_company_type_array['company_type'];
	    }    
         
        foreach ($color_arr as $c_key=>$c_val){

            foreach ($xiangkou['finger'] as $k=>$val){

                if(trim($xiangkou['sec_stone_weight'][$k])==''){
                    continue;
                }
                $where =array();                
                $color_name = $c_val;
                $where['style_id']=$style_id;
                $where['style_sn']=$style_sn;
                $where['product_type_id']=$product_type_id;//产品线id
                $where['cat_type_id']=$cat_type_id;//分类id
                $where['style_name'] = $style_name;//款式名称
                $where['caizhi']=$caizhi;//材质
                $where['yanse']=$c_key;//镶口
                $where['xiangkou'] = $stone;//镶口
                $where['zhushizhong']=$stone; //主石重 
                $where['zhushi_num']=0; //主石数 
                $where['fushizhong1']=$xiangkou['sec_stone_weight'][$k]; //副石1重 
                $where['fushi_num1']=$xiangkou['sec_stone_num'][$k]; //副石1数量
                $where['fushizhong2']=$xiangkou['sec_stone_weight_other'][$k]; //副石2重
                $where['fushi_num2']=$xiangkou['sec_stone_num_other'][$k];// 副石2数量
                $where['fushizhong3']=$xiangkou['sec_stone_weight3'][$k]; //副石2重
                $where['fushi_num3']=$xiangkou['sec_stone_num3'][$k];// 副石2数
                //$where['fushi_chengbenjia_other']=$xiangkou['sec_stone_price_other'][$k];// 其他副石成本价
                $where['dingzhichengben']=601;// 定制成本

                if($caizhi == 1){

                    //如果18k金重为0 ，则对应材质不生成商品；
                    if($xiangkou['g18_weight'][$k] == '0'){
                        continue;
                    }

                    $where['weight']=$xiangkou['g18_weight'][$k]; //18K标准金重
                    $where['jincha_shang']=$xiangkou['g18_weight_more'][$k];//18K金重上公差 
                    $where['jincha_xia']=$xiangkou['g18_weight_more2'][$k];// 18K金重下公差 
                }else{

                    //如果pt金重为0 ，则对应材质不生成商品；
                    if($xiangkou['gpt_weight'][$k] == '0'){
                        continue;
                    }

                    $where['weight']=$xiangkou['gpt_weight'][$k];//PT950标准金重 
                    $where['jincha_shang']=$xiangkou['gpt_weight_more'] [$k];//PT950金重上公差 
                    $where['jincha_xia']=$xiangkou['gpt_weight_more2'][$k];//PT950金重下公差
                }
               
                $where['last_update']=date("Y-m-d H:i:s");
                
                if($xiangkou['sec_stone_weight_other'][$k]==""){

                     $where['fushizhong2']=0; //副石2重
                }
                if($xiangkou['sec_stone_num_other'][$k]==""){

                     $where['fushi_num2']=0;// 副石2数量
                }
                if($xiangkou['sec_stone_weight3'][$k]==""){

                     $where['fushizhong3']=0; //副石3重
                }
                if($xiangkou['sec_stone_num3'][$k]==""){

                     $where['fushi_num3']=0;// 副石3数量
                }
                $where['xiangkou_company_type']=$xiangkou_company_type;


                if($caizhi == 1){
                        if($xiangkou['g18_weight'][$k]==""){
                            $where['weight'] =0;// 18K标准金重
                       }
                       if($xiangkou['g18_weight_more'][$k]==""){
                            $where['jincha_shang'] =0;// 18K金重上公差 
                       }
                       if($xiangkou['g18_weight_more2'][$k]==""){
                            $where['jincha_xia'] =0;// 18K金重下公差
                       }
                }else{
                     if($xiangkou['gpt_weight'][$k]==""){
                        $where['weight'] =0;// PT950标准金重 
                     }
                     if($xiangkou['gpt_weight_more'][$k]==""){
                        $where['jincha_shang'] =0;// //PT950金重上公差
                     }
                     if($xiangkou['gpt_weight_more2'][$k]==""){
                        $where['jincha_xia'] =0;// //PT950金重下公差
                    }  
                }

                //手寸是格式 数组array(6,7,8)
                //$new_figer_data = explode(",", $val);
        		if($val === '0'){
        			$val = array('0'=>0);
        		}              
               

                foreach ($val as $f_val){
                    $shoucun = $f_val;//手寸
                    $where['shoucun']=$shoucun;//手寸
                    $stone_name = $stone * 100;
                    $goods_sn = $style_sn."-".$caizhi_name."-".$color_name."-".$stone_name."-".$shoucun;
                    $quickDiyGoods = $newmodel->getQuickDiyGoodsByGoodsSn($goods_sn);
                    if(!empty($quickDiyGoods)){

                        $newmodel->deletegoods_sninfo($goods_sn);
                    }
                    $where['is_quick_diy'] = $quickDiyGoods['is_quick_diy']==1?1:0;
                    $where['goods_sn'] = $goods_sn;

                    $num++;
                  // $salepolicy_data = array('goods_id'=>$goods_sn,'goods_sn'=>$style_sn,'goods_name'=>$style_name,'chengbenjia'=>$where['dingzhichengben'],'category'=>$cat_type_id,'product_type'=>$product_type_id,'isXianhuo'=>0);
                  //  $apiSalePolicyModel->AddAppPayDetail(array('insert_data'=>$salepolicy_data));
                  //$res = $newmodel->saveData($where,$olddo);                      
                    $insert_data[]=$where;
                }
            }
        }

        $newmodel->insertAll($insert_data,'list_style_goods'); 
        if($style_id){
            //更新成本价
        	$ret = $this->update_goods_price($style_id,$caizhi,$stone);
        }
        /* 
		$new2model = new ListStyleGoodsModel(11); 
        $goods_sn_arr = $new2model->getAllGoodsinfo($style_id,$caizhi,$stone);
        foreach($goods_sn_arr as $val){
            //推送至可销售商品
        	$salepolicy_data = array('goods_id'=>$val['goods_sn'],'goods_sn'=>$val['style_sn'],'goods_name'=>$val['style_name'],'chengbenjia'=>$val['dingzhichengben'],'category'=>$val['cat_type_id'],'product_type'=>$val['product_type_id'],'isXianhuo'=>0,'is_base_style'=>0,'xiangkou'=>$val['xiangkou'],'caizhi'=>$val['caizhi'],'yanse'=>$val['yanse']);
        	$apiSalePolicyModel->AddAppPayDetail(array('insert_data'=>$salepolicy_data));
        }*/
        return array('flag'=>$ret,'num'=>$num);
    }
   
    
    /*------------------------------------------------------ */
    //-- 更新商品成本价格
    //-- BY linian
    /*------------------------------------------------------ */
    public function update_goods_price($style_id,$caizhi,$stone) {

    	$result = array('success' => 0, 'error' => '');
    	//$style_id = _Post::getInt('id');
    	$model = new ListStyleGoodsModel(11);
    	//1,获取商品表中所有商品
    	if($caizhi){
    		$data = $model->getAllGoodsinfo($style_id,$caizhi,$stone);
    	}
    	
    
    	//var_dump($data);
    	//echo "+++++++++++++++++++++++++";
    	//遍历所有商品数据 没遍历一条更新都更新商品成本价格
    	foreach($data as $key=>$val){
    		//var_dump($val);
    		//echo '=========================';
    		//2,每次获取一条基本数据
    		$goods_id = $val['goods_id'];
    		$style_id = $val['style_id'];
    		$style_sn = $val['style_sn'];
    		$yanse = $val['yanse'];
    		$fushi_1 = $val['fushizhong1'];
    		$fushi_num_1 = $val['fushi_num1'];
    		$fushi_2 = $val['fushizhong2'];
    		$fushi_num_2 = $val['fushi_num2'];
            $fushi_3 = $val['fushizhong3'];
            $fushi_num_3 = $val['fushi_num3'];
    		$caizhi = $val['caizhi'];
    		$weight = $val['weight'];
            $xiangkou = $val['xiangkou'];
    		$jincha_shang = $val['jincha_shang'];
            $product_type_id = $val['product_type_id'];
    		$goods_sn[]= $val['goods_sn'];
    		//print_r($goods_sn);
    
    			
    		//工费信息  基础工费 表面工艺费 超石费
    		$newmodel =  new AppStyleFeeModel(11);
    		if(!empty($style_id)){
    			//获取三种工费
    			$gongfei='';
    			$baomiangongyi_gongfei='';
    			$chaoshifee='';
                $baoxianfei = '';
    			$gongfei_data = $newmodel->getStyleFee($style_id);
    			foreach($gongfei_data as $val){
    				if($val['fee_type']==1 && $caizhi==1){
    					$gongfei = empty($val['price'])?'0':$val['price'];
    				}elseif($val['fee_type']==2){
    					$chaoshifee = empty($val['price'])?'0':$val['price'];
                        $chaoshifee = $chaoshifee * ($fushi_num_1+$fushi_num_2+$fushi_num_3);
    				}elseif($val['fee_type']==3){
    					$baomiangongyi_gongfei = empty($val['price'])?'0':$val['price'];
    				}elseif($val['fee_type']==4 && $caizhi==2){
                        $gongfei = empty($val['price'])?'0':$val['price'];
                    }
    			}
                
                $productTypeModel = new AppProductTypeModel(11);
                $parent_id = $productTypeModel->getParentIdById($product_type_id);

                if($parent_id == 3 && $xiangkou != ''){
                    $baoxianfeeModel = new AppStyleBaoxianfeeModel(11);
                    $baoxianfei = $baoxianfeeModel->getPriceByXiangkou($xiangkou);
                }

    		}
    			
    		//4,计算各种工费数据
    		$tal_gongfei = $gongfei+$baomiangongyi_gongfei+$chaoshifee+$baoxianfei;
    		//var_dump($tal_gongfei);exit;
    		//$gongfei = empty($val['gongfei'])?'':$val['gongfei'];
    		//$baomiangongyi_gongfei = empty($val['baomiangongyi_gongfei'])?'':$val['baomiangongyi_gongfei'];
    		//$fushixiangshifei = empty($val['fushixiangshifei'])?'':$val['fushixiangshifei'];
    	//var_dump('工费',$tal_gongfei);
    		//金损率:price_type:1男戒2女戒3情侣男戒4情侣女戒;
    		//3,判断款号是什么什么戒指，来获取对应的金损
    		$model = new AppJinsunModel(11);
    		if(!empty($caizhi)){
    			//材质
    			//if($caizhi==1){
    			//	$where['material_id']="18K";
    			//}else{
    			//	$where['material_id']="PT950";
    			//}
    			$where['material_id']=$caizhi;
    			
    			//2 女戒
    			$where['price_type']=2;
    			$jinsundata = $model->pageList($where,10);
    			if($jinsundata['data']){
    				$jinsunlv = $jinsundata['data'][0]['lv'];
    					
    			}
    		}
    	
    
    
    		//5,获取所有钻石规格单价数据
    		//(副石1重/副石1数量)的对应单价*副石1重+（副石2重/副石2数量）的对应单价*副石2重+（副石3重/副石3数量）的对应单价*副石3重
    		$newmodel =  new AppDiamondPriceModel(19);
    		if($fushi_num_1){
    			$where['guige'] = 100 * $fushi_1 / $fushi_num_1;
    			//获取副石1价格
    			$diamondprice = $newmodel->getDanPrice($where);
    			$fushi_price_1=$diamondprice['price']*$fushi_1;
    		}else{
    			$fushi_price_1='';
    		}
    		if($fushi_num_2){
    			$where['guige'] = 100 * $fushi_2 / $fushi_num_2;
    			//获取副石2价格
    			$diamondprice = $newmodel->getDanPrice($where);
    			$fushi_price_2=$diamondprice['price']*$fushi_2;
    		}else{
    			$fushi_price_2='';
    		}
            if($fushi_num_3){
                $where['guige'] = 100 * $fushi_3 / $fushi_num_3;
                //获取副石3价格
                $diamondprice = $newmodel->getDanPrice($where);
                $fushi_price_3=$diamondprice['price']*$fushi_3;
            }else{
                $fushi_price_3='';
            }
    		//var_dump($fushi_price_1,$fushi_price_2,$fushi_price_3);
    		//6,(材质金重+向上公差）*金损率* 对应材质单价
    		//材质单价:price_type :1=》18K；2=>PT950; price:价格; type = 2
    		$model = new AppMaterialInfoModel(11);
    		if(!empty($caizhi)){
    			if($caizhi ==1){
    				$material_name ='18K';
    			}elseif($caizhi ==2){
    				$material_name ='PT950';
    			}
    			//材质
    			$where['material_name']=$material_name;
    			$where['material_status']=1;
    			//获取对应的材质单价
    			$caizhidata = $model->pageList($where,10);
    			$caizhi_price = $caizhidata['data'][0]['price'];
                $shuidian = $caizhidata['data'][0]['tax_point'];
    		}
    
    
    		//7,金损率 等于1+金损率
    		$jinsun_price = $jinsunlv+1;
    		//var_dump('近损率',$jinsun_price);
    		//8,计算金损价格
    		//var_dump('金重',$weight);
    		//var_dump('上公差',$jincha_shang);
    		//var_dump('金损价',$jinsun_price);
    		//var_dump('材质价格',$caizhi_price);
    		$tal_jinsun = ($weight + $jincha_shang) * $jinsun_price * $caizhi_price;
    		//9,计算定制成本价格
    		$model = new ListStyleGoodsModel(12);
    		//$aa = array('副石1'=>$fushi_price_1,'副石2'=>$fushi_price_2,'金损率'=>$tal_jinsun,'工费'=>$tal_gongfei);
    		//var_dump($aa);
    		$dingzhichengben = ($fushi_price_1 + $fushi_price_2 + $fushi_price_3 + $tal_jinsun + $tal_gongfei) * (1 + $shuidian);
    		//var_dump('定制成本加个',$dingzhichengben);
    		$where['chengbenjia'] = round($dingzhichengben,2);
    		//var_dump($where['chengbenjia'],99);
    		$where['goods_id'] =$goods_id;
           
    		$res = $model->updateChengbenPrice($where);
  
    		$chenbenjia[] =$where['chengbenjia'];
    	}
        $salepolicyModel = new SalepolicyModel(18);
        //if(!empty($goods_sn) && !empty($chenbenjia)){ 
        //	$salepolicyModel->UpdateSalepolicyChengben($goods_sn,$chenbenjia);
        //}
    	if ($res !== false) {
    		$result['success'] = 1;
    	} else {
    		$result['error'] = '更新价格失败';
    	}
    	return array('flag'=>$res);
    
    }
   // }
    
    /*
     * 切割手寸
     * 转换数据 6-8 其实要变成 6,7,8
     */
    public function cutFingerInfo($data){
		if($data === 0){
			return array('0');
		}
        
        foreach ($data as $key=>$val){
            if(empty($val)){
                continue;
            }              
            $is_search = $this->checkString('-', $val);
            $new_arr = array();
            if($is_search){
                $tmp = explode('-', $val);
                $min = intval($tmp[0]);
                $max = intval($tmp[1]);
                if($min == $max) {
                     $new_arr[] = $min;
                }else{
                    for($i=$min;$i<=$max;$i++){
                        $new_arr[] = $i;
                    }
                }
            }else{
                 $new_arr[] = $val;
            }
            $data[$key]=$new_arr;
        }
       
        return $data;
    }


    /*
     * 切割手寸,针对批量生成商品
     * 转换数据 6-8 其实要变成 6,7,8
     */
    function cutFingerInfoAll($data){

        if($data === 0){
            return array('0');
        }

        $is_search = $this->checkString('-', $data);

        $new_arr = array();
        if($is_search){

            $tmp = explode('-', $data);

            $min = intval($tmp[0]);
            $max = intval($tmp[1]);

            if($min == $max) {

                 $new_arr[] = $min;
            }else{

                for($i=$min;$i<=$max;$i++){

                    $new_arr[] = $i;
                }
            }
        }else{

             $new_arr[] = $data;
        }
        $data=$new_arr;
        return $data;
    }
    
    //检查字符串是否存在
    public function checkString($search,$string) {
        $pos = strpos($string, $search);
        if($pos == FALSE){
            return FALSE;
        }else{
            return TRUE;
        }
    }
    
    //判断此款是否是基本款
    public function is_base_style($style_id) {
        
    }
    
    //获取基本款的属性
    public function getBaseStyle($attribute_name){
        $attributeModel = new AppAttributeModel(11);
        $data = $attributeModel->getAttributAndValue(array('attribute_name'=>'基本款'));
        $attribute_id = '';
        $attr_arr = array();
        foreach ($data as $val){
            $attribute_id = $val['attribute_id'];
            $att_value_id = $val['att_value_id'];
            $att_value_name = $val['att_value_name'];
            $attr_arr[$attribute_id][$att_value_name]=$att_value_id;
        }
        
        return array('attribute_id'=>$attribute_id,'attr_data'=>$attr_arr);
    }
 
	    //戒指的相关属性
    public function getRingAttributeTmp($style_id) {
            $error = 0;//默认没有错误
            $attributeModel = new AppAttributeModel(11);
            $xiangkou_data = $attributeModel->getAttributeInfoByName('镶口');
            $zhiquan_data = $attributeModel->getAttributeInfoByName('指圈');
            $caizhi_data = $attributeModel->getAttributeInfoByName('材质');
            $yanse_data = $attributeModel->getAttributeInfoByName('材质颜色');
            $xk_id = $xiangkou_data['attribute_id'];
            $zq_id = $zhiquan_data['attribute_id'];
            $cz_id = $caizhi_data['attribute_id'];
            $ys_id = $yanse_data['attribute_id'];
			//var_dump($xiangkou_data,$zhiquan_data,$caizhi_data,$yanse_data);
            $relStyleModel = new RelStyleAttributeModel(11);
            $xk_data = $relStyleModel->getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$xk_id));

/*array(3) {
  ["attribute_value"]=>
  string(3) "29,"
  ["product_type_id"]=>
  string(2) "16"
  ["cat_type_id"]=>
  string(2) "14"
}

			*/
			//var_dump($xk_data);
            //镶口
            if(empty($xk_data)){
                $error = 1;
                return array('error'=>$error,'message'=>'此款没有设置：镶口属性!');
            }
            if(empty($xk_data['attribute_value'])){
                $error = 1;
                return array('error'=>$error,'message'=>'此款没有选择：镶口数据!');
            }
            //指圈
            $zq_data = $relStyleModel->getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$zq_id));
			//var_dump($zq_data);
            if(empty($zq_data)){
				/*
                $error = 1;
                return array('error'=>$error,'message'=>'此款没有设置：指圈属性!');
				*/
				$zq_data= array('attribute_value'=>'888,','product_type_id'=>16,'cat_type_id'=>14);
            }
            if(empty($zq_data['attribute_value'])){
                $error = 1;
                return array('error'=>$error,'message'=>'此款没有选择：指圈数据!');
            }
            //材质
            $cz_data = $relStyleModel->getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$cz_id));
            if(empty($cz_data)){
                $error = 1;
                return array('error'=>$error,'message'=>'此款没有设置：材质属性!');
            }
            if(empty($cz_data['attribute_value'])){
                $error = 1;
                return array('error'=>$error,'message'=>'此款没有选择：材质数据!');
            }
            //可做颜色
            $ys_data = $relStyleModel->getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$ys_id));
            if(empty($ys_data)){
				/*
                $error = 1;
                return array('error'=>$error,'message'=>'此款没有设置：材质颜色属性!');
				*/
				$ys_data= array('attribute_value'=>'888,','product_type_id'=>16,'cat_type_id'=>14);
            }
            if(empty($ys_data['attribute_value'])){
                $error = 1;
                return array('error'=>$error,'message'=>'此款没有选择：材质颜色数据!');
            }
           
            $xk_info = explode(",",rtrim($xk_data['attribute_value'],","));
            $zq_info = explode(",",rtrim($zq_data['attribute_value'],","));
            $cz_info = explode(",",rtrim($cz_data['attribute_value'],","));
            $ys_info = explode(",",rtrim($ys_data['attribute_value'],","));
            
            //取出都是款的属性对应的属性值的id，并不是描述，所以需要在转化成描述
            $style_xiangkou = array();
            $style_zhiquan = array();
            $style_caizhi = array();
            $style_yanse = array();
            $attributeValueModel = new AppAttributeValueModel(11);
            //镶口
            foreach ($xk_info as $val){
                $value = $attributeValueModel->getAttrNameByid(array('att_value_id'=>$val));
                $style_xiangkou[]=$value['att_value_name'];
            }
            //指圈
            foreach ($zq_info as $val){
				if($val == 888){
					$style_zhiquan[]='0';
					continue;
				}
                $value = $attributeValueModel->getAttrNameByid(array('att_value_id'=>$val));
                $style_zhiquan[]=$value['att_value_name'];
            }
            //材质
            foreach ($cz_info as $val){
                $value = $attributeValueModel->getAttrNameByid(array('att_value_id'=>$val));
                $style_caizhi[]=$value['att_value_name'];
            }
            //材质颜色
            foreach ($ys_info as $val){
				if($val == 888){
					$style_yanse[]='白色';
					continue;
				}
                $value = $attributeValueModel->getAttrNameByid(array('att_value_id'=>$val));
                $style_yanse[]=$value['att_value_name'];
            }
            return array('error'=>$error,'data'=>array('xk'=>$style_xiangkou,'zq'=>$style_zhiquan,'cz'=>$style_caizhi,'ys'=>$style_yanse));
    }

    //戒指的相关属性
    public function getRingAttribute($style_id) {
            $error = 0;//默认没有错误
            $attributeModel = new AppAttributeModel(11);
            $xiangkou_data = $attributeModel->getAttributeInfoByName('镶口');
             
            if(empty($xiangkou_data)){
               $error = 1;
               return array('error'=>$error,'message'=>'请查看属性设置中是否存在：镶口！');
            }
            $zhiquan_data = $attributeModel->getAttributeInfoByName('指圈');
            if(empty($zhiquan_data)){
               $error = 1;
               return array('error'=>$error,'message'=>'请查看属性设置中是否存在：指圈！');
            }
            $caizhi_data = $attributeModel->getAttributeInfoByName('材质');
            if(empty($caizhi_data)){
               $error = 1;
               return array('error'=>$error,'message'=>'请查看属性设置中是否存在：材质!');
            }
            $yanse_data = $attributeModel->getAttributeInfoByName('材质颜色');
            if(empty($yanse_data)){
               $error = 1;
               return array('error'=>$error,'message'=>'请查看属性设置中是否存在：材质颜色!');
            }
            $xk_id = $xiangkou_data['attribute_id'];
            $zq_id = $zhiquan_data['attribute_id'];
            $cz_id = $caizhi_data['attribute_id'];
            $ys_id = $yanse_data['attribute_id'];
            $relStyleModel = new RelStyleAttributeModel(11);
            $xk_data = $relStyleModel->getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$xk_id));
            //镶口
            if(empty($xk_data)){
                $error = 1;
                return array('error'=>$error,'message'=>'此款没有设置：镶口属性!');
            }
            if(empty($xk_data['attribute_value'])){
                $error = 1;
                return array('error'=>$error,'message'=>'此款没有选择：镶口数据!');
            }
            //指圈
            $zq_data = $relStyleModel->getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$zq_id));
            if(empty($zq_data)){
                $error = 1;
                return array('error'=>$error,'message'=>'此款没有设置：指圈属性!');
            }
            if(empty($zq_data['attribute_value'])){
                $error = 1;
                return array('error'=>$error,'message'=>'此款没有选择：指圈数据!');
            }
            //材质
            $cz_data = $relStyleModel->getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$cz_id));
            if(empty($cz_data)){
                $error = 1;
                return array('error'=>$error,'message'=>'此款没有设置：材质属性!');
            }
            if(empty($cz_data['attribute_value'])){
                $error = 1;
                return array('error'=>$error,'message'=>'此款没有选择：材质数据!');
            }
            //可做颜色
            $ys_data = $relStyleModel->getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$ys_id));
            if(empty($ys_data)){
                $error = 1;
                return array('error'=>$error,'message'=>'此款没有设置：材质颜色属性!');
            }
            if(empty($ys_data['attribute_value'])){
                $error = 1;
                return array('error'=>$error,'message'=>'此款没有选择：材质颜色数据!');
            }
           
            $xk_info = explode(",",rtrim($xk_data['attribute_value'],","));
            $zq_info = explode(",",rtrim($zq_data['attribute_value'],","));
            $cz_info = explode(",",rtrim($cz_data['attribute_value'],","));
            $ys_info = explode(",",rtrim($ys_data['attribute_value'],","));
            
            //取出都是款的属性对应的属性值的id，并不是描述，所以需要在转化成描述
            $style_xiangkou = array();
            $style_zhiquan = array();
            $style_caizhi = array();
            $style_yanse = array();
            $attributeValueModel = new AppAttributeValueModel(11);
            //镶口
            foreach ($xk_info as $val){
                $value = $attributeValueModel->getAttrNameByid(array('att_value_id'=>$val));
                $style_xiangkou[]=$value['att_value_name'];
            }
            //指圈
            foreach ($zq_info as $val){
                $value = $attributeValueModel->getAttrNameByid(array('att_value_id'=>$val));
                $style_zhiquan[]=$value['att_value_name'];
            }
            //材质
            foreach ($cz_info as $val){
                $value = $attributeValueModel->getAttrNameByid(array('att_value_id'=>$val));
                $style_caizhi[]=$value['att_value_name'];
            }
            //材质颜色
            foreach ($ys_info as $val){
                $value = $attributeValueModel->getAttrNameByid(array('att_value_id'=>$val));
                $style_yanse[]=$value['att_value_name'];
            }
            
            return array('error'=>$error,'data'=>array('xk'=>$style_xiangkou,'zq'=>$style_zhiquan,'cz'=>$style_caizhi,'ys'=>$style_yanse));
    }
    
    function getTypeName($type){
        $type_name = "";
        if($type == 'ZJ'){
            $type_name = "足金";
        }else if($type=="S"){
            $type_name = $type."金";
        }else{
            $type_name = "银";
        }
        return $type_name;
    }
    
    //
    function getCaiZhiOther($type){
        $caizhi = "";
        $name = "";
        if($type == 'K'){
            $caizhi = 1;
            $name = "18K";
        }else if($type == "PT"){
            $caizhi = 2;
            $name = "PT950";
        }else if($type == "ZJ"){
            $caizhi = 3;
            $name ='ZJ';
        }else if($type == "S"){
            $caizhi = 4;
            $name ='S925';
        }
        return array('id'=>$caizhi,'name'=>$name);
    }
    
    
           
}

?>
