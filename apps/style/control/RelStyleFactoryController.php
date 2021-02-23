<?php

/**
 *  -------------------------------------------------
 *   @file		: RelStyleFactoryController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 10:34:21
 *   @update	:
 *  -------------------------------------------------
 */
class RelStyleFactoryController extends CommonController {

    protected $smartyDebugEnabled = false;

    /**
     * 	index，搜索框
     */
    public function index($params) {
        $this->render('rel_style_factory_search_form.html', array('bar' => Auth::getBar()));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'style_sn' => _Post::getString('style_sn'),
            'style_id' => _Request::getInt('_id')
        );
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
        $model = new RelStyleFactoryModel(11);
        $data = $model->pageList($args, $page, 10, false);
        //$data = $model->getAllList($args);
        if(!empty($data['data'])){
			$arrData = array('否', '是');
             $_newModel = new ApiProcessorModel();
            foreach ($data['data'] as $key=>$val){
                $factory_name = $_newModel->GetSupplierListName($val['factory_id']);
				$data['data'][$key]['factory_name']=isset($factory_name['data'])?$factory_name['data']:'';
				$data['data'][$key]['is_def']=isset($arrData[$val['is_def']])?$arrData[$val['is_def']]:'';
				$data['data'][$key]['is_factory']=isset($arrData[$val['is_factory']])?$arrData[$val['is_factory']]:'';
            }
        }
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'rel_style_factory_search_page';
        $this->render('rel_style_factory_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $processorList = $this->getProcessorList();
        $allProcessorInfo = array();
        if (!empty($processorList['data'])) {
            $allProcessorInfo = $processorList['data'];
        }

        $style_id = _Request::getInt('_id');
        if ($style_id) {
            $_model = new BaseStyleInfoModel($style_id, 11);
            $do = $_model->getDataObject();
        }
        $style_sn = $do['style_sn'];
        $is_made = $do['is_made'];
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('rel_style_factory_info.html', array(
            'view' => new RelStyleFactoryView(new RelStyleFactoryModel(11)),
            'style_id' => $style_id,
            'style_sn' => $style_sn,
        	'is_made' => $is_made,
            'processorList' => $allProcessorInfo,
            '_id' => _Post::getInt('_id')
        ));
        $result['title'] = '申请添加工厂信息';
        Util::jsonExit($result);
    }

    /**
     *      cancel,作废工厂
     * @param type $param
     */
    public function cancel($param) {

        $result = array('success' => 0, 'error' => '');
        $id = intval($param['id']);

        $relModel = new RelStyleFactoryModel($id,11);
        $rel_do = $relModel->getDataObject();

        if($rel_do['is_factory'] == '1' && $rel_do['is_def'] == '1'){

            $result['error'] = '此款此工厂为：<span style="color:red">默认工厂、默认镶口，不许作废！<span/>';
            Util::jsonExit($result);
        }

        $model = new AppFactoryApplyModel(12);
        $do = $model->getResByFid($id);
        if(!empty($do)){

            if ($do['type'] == 2 && $do['status'] == 1) {
                $result['error'] = '此款此工厂已有：<span style="color:red">申请作废，请审核！<span/>';
                Util::jsonExit($result);
            }
        }

        if(!empty($do)){
            unset($do['apply_id']);
            $do['type'] = 2;
            $do['status'] = 1;
            $do['make_name'] = $_SESSION['userName'];
            $do['crete_time'] = date("Y-m-d H:i:s");
            $do['info'] = '申请作废';
        }else{
            $name = '';
            $_newModel = new ApiProcessorModel();

            $factory_name = $_newModel->GetSupplierListName($rel_do['factory_id']);

            if($factory_name['data']){
                $name = $factory_name['data'];
            }
            $do['style_id'] = $rel_do['style_id'];
            $do['style_sn'] = $rel_do['style_sn'];
            $do['f_id'] = $rel_do['f_id'];
            $do['factory_id'] = $rel_do['factory_id'];
            $do['factory_sn'] = $rel_do['factory_sn'];
            $do['factory_name'] = $name;
            $do['factory_fee'] = $rel_do['factory_fee'];
            $do['xiangkou'] = $rel_do['xiangkou'];
            $do['type'] = 2;
            $do['status'] = 1;
            $do['make_name'] = $_SESSION['userName'];
            $do['crete_time'] = date("Y-m-d H:i:s");
            $do['info'] = '申请作废';
        }
        $apply_num = $model->getApplyNum(array('style_sn' => $do['style_sn'], 'type' => 2));
        $do['apply_num'] = $apply_num === false ? 1 : $apply_num + 1;
        
        $res = $model->saveData($do, array());

        //$res = $model->delete();
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "申请作废工厂失败";
        }
		$baseStyleInfoModel = new BaseStyleInfoModel(11);
		$baseStyleInfoModel->addBaseStyleLog(array('style_id'=>$do['style_id'],'remark'=>'作废工厂成功'));
        Util::jsonExit($result);
    }

    /**
     * 申请修改工厂
     * @param type $param
     */
    public function applyUpdateFactory($param)
    {
        # code...
        $result = array('success' => 0, 'error' => '');
        $f_id = $param['id'];

        //取得全部工厂信息
        $processorList = $this->getProcessorList();
        $allProcessorInfo = array();
        if (!empty($processorList['data'])) {

            $allProcessorInfo = $processorList['data'];
        }
        
        $model = new RelStyleFactoryModel($f_id,11);
        $selfInfo = $model->getDataObject();

        $style_id = $selfInfo['style_id'];
        $style_sn = $selfInfo['style_sn'];
        $baseModel = new BaseStyleInfoModel($style_id, 11);
        $styleInfo = $baseModel->getDataObject();

        $is_made = $styleInfo['is_made'];
        $result['content'] = $this->fetch('rel_style_factory_info.html', array(
            'view' => new RelStyleFactoryView($model),
            'style_id' => $style_id,
            'style_sn' => $style_sn,
            'is_made' => $is_made,
            'processorList' => $allProcessorInfo,
            '_id' => $f_id,
        ));
        $result['title'] = '申请修改工厂信息';
        Util::jsonExit($result);
    }

    /**
     * 申请默认工厂
     * @param type $param
     */
    public function defaultFactory($param) {

        $result = array('success' => 0, 'error' => '');
        $id = intval($param['id']);

        $model = new RelStyleFactoryModel($id, 12);
        $do = $model->getDataObject();
        /*if ($do['is_def'] == 1 && $do['is_factory'] == 1) {
            $result['success'] = 1;
            Util::jsonExit($result);
        }*/

        $IsFactoryByf_id = $model->getIsFactoryByf_id($id);
        //查找当前是否是默认工厂
        if($IsFactoryByf_id){

            $result['error'] = '此款工厂已是：<span style="color:red">默认镶口、默认工厂！<span/>';
            Util::jsonExit($result);
        }

        //$res = $model->updateIsFactory($id, $do['style_id'], $do['factory_id']);
        $newmodel = new AppFactoryApplyModel(12);

        $where=array();
        $where['style_id']=$do['style_id'];
        $where['type']=3;
        $where['status']=1;
        $applyStatus = $newmodel->getResByStatus($where);
        //工厂状态
        if($applyStatus){

            $result['error'] = '此款工厂已有：<span style="color:red">待审核状态的申请，请审核！<span/>';
            Util::jsonExit($result);            
        }

        $IsFactory = $model->getIsFactory($do['style_id']);

        //查找默认工厂
        $apiProcessorModel = new ApiProcessorModel();
        if($IsFactory){
            //取供应商名称
            $factory_name_old = $apiProcessorModel->GetSupplierListName($IsFactory['factory_id']);
            if($factory_name_old['data']==''){
                $factory_name_old['data']=='';
            }
        }else{
            $factory_name_old['data']='';
        }
        $factory_name_new = $apiProcessorModel->GetSupplierListName($do['factory_id']);
        if($factory_name_new['data']==''){
            $factory_name_new['data']=='';
        }
        

        $where=array();
        $where['style_sn']=$do['style_sn'];
        $where['type']=3;
        $apply_num = $newmodel->getApplyNum($where);//申请次数

        $newdo=array();
        $newdo['style_id']=$do['style_id'];
        $newdo['style_sn']=$do['style_sn'];
        $newdo['f_id']=$do['f_id'];
        $newdo['factory_id']=$do['factory_id'];
        $newdo['factory_name']=$factory_name_new['data'];
        $newdo['factory_sn']=$do['factory_sn'];
        $newdo['xiangkou']=$do['xiangkou'];
        $newdo['factory_fee']=$do['factory_fee'];
        $newdo['type']=3;
        $newdo['status']=1;
        $newdo['apply_num']=$apply_num+1;
        $newdo['make_name']=$_SESSION['userName'];
        $newdo['crete_time']=date("Y-m-d H:i:s");
        $newdo['info']="申请默认工厂，原默认工厂为“".$factory_name_old['data']."”";
        $res = $newmodel->saveData($newdo, array());

        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "申请默认工厂失败";
        }
		$baseStyleInfoModel = new BaseStyleInfoModel(11);
		$baseStyleInfoModel->addBaseStyleLog(array('style_id'=>$do['style_id'],'remark'=>'申请默认工厂成功'));
        Util::jsonExit($result);
    }

    /**
     * 	edit，渲染修改页面
     */
    public function edit($params) {
        $id = intval($params["id"]);
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('rel_style_factory_info.html', array(
            'view' => new RelStyleFactoryView(new RelStyleFactoryModel($id, 11)),
            'tab_id' => $tab_id,
        ));
        $result['title'] = '编辑';
        Util::jsonExit($result);
    }

    /**
     * 	show，渲染查看页面
     */
    public function show($params) {
        die('开发中');
        $id = intval($params["id"]);
        $this->render('rel_style_factory_show.html', array(
            'view' => new RelStyleFactoryView(new RelStyleFactoryModel($id, 11))
        ));
    }

    /**
     * 	insert，信息入库
     */
    public function insert($params) {
        $result = array('success' => 0, 'error' => '');
        $style_id = _Post::getInt('style_id');
        $baseStyleInfoModel = new BaseStyleInfoModel(11);
        $style_info = $baseStyleInfoModel->getStyleById($style_id);
        if (!$style_info) {
            $result['error'] = '不是有效的款';
            Util::jsonExit($result);
        }
        
        $is_made = $style_info['is_made'];
        $factory_sn = trim(_Post::getString('factory_sn'));
        // Fix NEW-2068
        if (intval($is_made) == 1) {
        	/*
        	 *定制款，模号必填，只有以下情况有 x时能够保存：模号位数大于1，且只有一个x
        	 */
        	if (empty($factory_sn)) {
        		$result['error'] = "定制的款，工厂模号不能为空";
        		Util::jsonExit($result);
        	}
        	
        	$mt = array();
        	if (preg_match_all('/X/i', $factory_sn, $mt)) {
        		if (!(strlen($factory_sn) > 1 && count($mt[0]) == 1)) {
        			$result['error'] = "定制的款，如果工厂模号存在X，当且仅当模号位数大于1并且只有1个X时才有效";
        			Util::jsonExit($result);
        		}
        	}
        }
        
        $factory_id = _Post::getInt('factory_id');
        
        $rel_model = new RelStyleFactoryModel(11);
        $check_result = $rel_model->checkFactoryInfoIsUnique($factory_id, $factory_sn, $style_info['style_sn']);
        if (!empty($check_result['error'])) {
        	$result['error'] = $check_result['error'];
	        Util::jsonExit($result);
        }
        
        $factory_name = _Post::getString('factory_name');
        $style_sn = _Post::getstring('style_sn');
        $xiangkou = _Post::get('xiangkou');
        $factory_fee = _Post::get('factory_fee');

        $_model = new AppFactoryApplyModel(11);
        $isAllow = $_model->isAllowAdd($style_id, $factory_id, $factory_sn, $xiangkou);
        //print_r($isAllow);die;
        $falg = true;
        if (!empty($isAllow)) {

            $falg = false;

            if($isAllow['type'] == 2 && $isAllow['status'] == 2){
                $falg = true;
            }

            if($isAllow['type'] == 1 && $isAllow['status'] == 3){
                $falg = true;
            }
        }
        //var_dump($falg);die;
       // if(!$falg){    2015-10-12 修改boss-304 款式库理，维护工厂信息时，模号允许输入“无“
        if(!$falg && $factory_sn != '无'){

            $result['error'] = '不能重复申请！';
            Util::jsonExit($result);
        }
        
        $olddo = array();
        $newdo = array();
        $newdo['style_id'] = $style_id;
        $newdo['style_sn'] = $style_sn;
        $newdo['factory_id'] = $factory_id; //供应商id
        $newdo['factory_name'] = $factory_name; //供应商name
        $newdo['factory_sn'] = $factory_sn;
//                $relStyleFacModel = new RelStyleFactoryModel(11);
//                $isFactory = $relStyleFacModel->getIsFactory($style_id);
//                $is_factory = 0;
//                if($isFactory){
//                    $is_factory = 1;
//                }
//                $newdo['is_factory'] = $is_factory;
        $newdo['factory_fee'] = $factory_fee;
        $newdo['xiangkou'] = $xiangkou;
        $newdo['type'] = 1;
        $newdo['crete_time'] = date("Y-m-d H:i:s");
        $newdo['make_name'] = $_SESSION['userName'];
        
        $apply_num = $_model->getApplyNum(array('style_sn'=>$style_sn, 'type' => 1));
        $newdo['apply_num'] = $apply_num === false ? 1 : intval($apply_num) + 1;

        $newmodel = new AppFactoryApplyModel(12);
        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '添加失败';
        }
		$baseStyleInfoModel->addBaseStyleLog(array('style_id'=>$style_id,'remark'=>'申请添加工厂成功'));
        Util::jsonExit($result);
    }

    /**
     * 	update，更新信息（申请修改工厂）
     */
    public function update($params) {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('id');
        /*echo '<pre>';
        print_r($_POST);
        echo '</pre>';
        exit;*/

        $style_id = _Post::getInt('style_id');
        $style_sn = trim(_Post::getString('style_sn'));
        $is_made = _Post::getInt('is_made');
        $factory_id = _Post::getString('factory_id');
        $factory_sn = trim(_Post::getString('factory_sn'));
        $xiangkou = _Post::getFloat('xiangkou');
        $factory_fee = _Post::getFloat('factory_fee');

        $model = new RelStyleFactoryModel($id,11);
        $do = $model->getDataObject();

        //默认工厂默认镶口，工厂信息不可更改。其他信息可更改
        if($do['is_factory'] == '1' && $do['is_def'] == '1'){

            $factory_id = $do['factory_id'];
        }

        if($factory_id == $do['factory_id'] && $factory_sn == $do['factory_sn'] && $xiangkou == $do['xiangkou'] && $factory_fee == $do['factory_fee']){

            $result['error'] = "您未做任何修改，无法提交申请！";
            Util::jsonExit($result);
        }

        if ($is_made == 1) {
            /*
             *定制款，模号必填，只有以下情况有 x时能够保存：模号位数大于1，且只有一个x
             */
            if ($factory_sn == '') {

                $result['error'] = "此款为定制款，模号必填！";
                Util::jsonExit($result);
            }
            
            $mt = array();
            if (preg_match_all('/X/i', $factory_sn, $mt)) {

                if (!(strlen($factory_sn) > 1 && count($mt[0]) == 1)) {

                    $result['error'] = "此款为定制款，如果工厂模号存在X，当且仅当模号位数大于1并且只有1个X时才有效！";
                    Util::jsonExit($result);
                }
            }
        }

        $rel_model = new RelStyleFactoryModel(11);
        $check_result = $rel_model->checkFactoryInfoIsUnique($factory_id, $factory_sn, $style_sn);
        if (!empty($check_result['error'])) {
            $result['error'] = $check_result['error'];
            Util::jsonExit($result);
        }

        $check_mark = $rel_model->checkFactoryIsCun($factory_id, $factory_sn, $style_sn, $xiangkou);
        if(!empty($check_mark) && $factory_sn != '无' && $check_mark['f_id'] != $id){

            $result['error'] = '此款的工厂、模号、镶口已在本款存在，不可重复！';
            Util::jsonExit($result);
        }

        $_model = new AppFactoryApplyModel(11);
        $isAllow = $_model->checkUpdatFactory($style_id, $factory_id, $factory_sn);
        //print_r($isAllow);die;
        if(!empty($isAllow)){
            $result['error'] = '此款此工厂已有：未审核修改申请，请审核！';
            Util::jsonExit($result);
        }

        //取得全部工厂信息
        $processorList = $this->getProcessorList();
        $allProcessorInfo = array();
        if (!empty($processorList['data'])) {
            foreach ($processorList['data'] as $key => $value) {
                # code...
                $allProcessorInfo[$value['id']] = $value['name'];
            }
        }

        $olddo = array();
        $newdo = array();
        $newdo['style_id'] = $style_id;
        $newdo['style_sn'] = $style_sn;
        $newdo['f_id'] = $id;
        $newdo['factory_id'] = $factory_id; //供应商id
        $newdo['factory_name'] = $allProcessorInfo[$factory_id]; //供应商name
        $newdo['factory_sn'] = $factory_sn;
        $newdo['factory_fee'] = $factory_fee;
        $newdo['xiangkou'] = $xiangkou;
        $newdo['type'] = 4; //申请类型4修改；
        $newdo['crete_time'] = date("Y-m-d H:i:s");
        $newdo['make_name'] = $_SESSION['userName'];

        $diff_new = array();
        $diff_old = array();
        $diff_new['factory_id'] = $factory_id;
        $diff_new['factory_sn'] = $factory_sn;
        $diff_new['xiangkou'] = $xiangkou;
        $diff_new['factory_fee'] = $factory_fee;

        $diff_old['factory_id'] = $do['factory_id'];
        $diff_old['factory_sn'] = $do['factory_sn'];
        $diff_old['xiangkou'] = $do['xiangkou'];
        $diff_old['factory_fee'] = $do['factory_fee'];

        $info_s = array(
            'factory_id' => '供应商',
            'factory_sn' => '供应商模号',
            'xiangkou' => '镶口',
            'factory_fee' => '工费'
            );


        $diffdata = array();
        foreach ($diff_new as $key => $value) {
            # code...
            foreach ($diff_old as $k => $v) {
                # code...
                if($key == $k && $value != $v){
                    $diffdata[$key]['new'] = $value;
                    $diffdata[$key]['old'] = $v;
                }
            }
        }

        $str_log = '';
        if(!empty($diffdata)){

            foreach ($diffdata as $key => $value) {
                # code...
                if($key == 'factory_id'){
                    $str_log .= $info_s[$key].":".$allProcessorInfo[$value['old']]."=>".$allProcessorInfo[$value['new']]."<br />";
                }else{
                    $str_log .= $info_s[$key].":".$value['old']."=>".$value['new']."<br />";
                }
            }
        }

        $newdo['info'] = $str_log;
        $apply_num = $_model->getApplyNum(array('style_sn'=>$style_sn, 'type' => 4));
        $newdo['apply_num'] = $apply_num === false ? 1 : intval($apply_num) + 1;

        $newmodel = new AppFactoryApplyModel(12);
        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            //记录款式日志
            $baseModel = new BaseStyleInfoModel(12);
            $baseModel->addBaseStyleLog(array('style_id'=>$style_id,'remark'=>'申请修改工厂'));
            $result['success'] = 1;
            $result['error'] = '申请修改工厂成功，请等待审核！';
        } else {
            $result['error'] = '申请修改失败！';
        }
        Util::jsonExit($result);
    }

    /**
     * 	delete，删除
     */
    public function delete($params) {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new RelStyleFactoryModel($id, 12);
        $do = $model->getDataObject();
        $valid = $do['is_system'];
        if ($valid) {
            $result['error'] = "当前记录为系统内置，禁止删除";
            Util::jsonExit($result);
        }
        $model->setValue('is_deleted', 1);
        $res = $model->save(true);
        //$res = $model->delete();
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "删除失败";
        }
        Util::jsonExit($result);
    }

    public function getProcessorList() {
        $apiProcessorModel = new ApiProcessorModel();
        $info = $apiProcessorModel->GetSupplierList();
        return $info;
    }

}

?>