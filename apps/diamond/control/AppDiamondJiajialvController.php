<?php

/**
 *  -------------------------------------------------
 *   @file		: DiamondJiajialvController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-15 15:56:34
 *   @update	:
 *  -------------------------------------------------
 */
class AppDiamondJiajialvController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $diamondview = array();
    protected $fromad_arr = array();

    public function __construct() {
        parent::__construct();
        $model = new AppDiamondColorModel(19);
       
        $this->diamondview = new AppDiamondColorView(new AppDiamondColorModel(19));
        $this->assign('diamondview', $this->diamondview);
       
        $this->fromad_arr = $model->getForm_ad();
        $this->assign("from_ad",$this->fromad_arr);
        
    }

    /**
     * 	index，搜索框
     */
    public function index($params) {
    	
        $this->render('diamond_jiajialv_search_form.html', array('bar' => Auth::getBar()));
    }

    /**
     * 	search，列表 
     */
    public function search($params) {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'cost_min'=>  _Request::getFloat('cost_min'),
            'cost_max'=> _Request::getFloat('cost_max'),
            'good_type'=> _Request::getInt('good_type'),
            'from_ad'=> _Request::getInt('from_ad'),
            'cert'=> _Request::getString('cert'),
            'status'=> _Request::getString('status'),
        );
        $page = _Request::getInt("page", 1);
        $where = array(
            'cost_min'=>  _Request::getFloat('cost_min'),
            'cost_max'=> _Request::getFloat('cost_max'),
            'good_type'=> _Request::getInt('good_type'),
            'from_ad'=> _Request::getInt('from_ad'),
            'cert'=> _Request::getString('cert'),
            'status'=> _Request::getString('status'),
        );

        $model = new AppDiamondJiajialvModel(19);
        $data = $model->pageList($where, $page, 10, false);
        
        foreach($data['data'] as $k=>$v){
        	if(intval($data['data'][$k]['cost_max'])==0){
        		$data['data'][$k]['cost_max'] = '--';
        	}
        	if(intval($data['data'][$k]['cost_min'])==0){
        		$data['data'][$k]['cost_min'] = '--';
        	}
        }
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_diamond_jiajialv_search_page';
        
        $this->render('diamond_jiajialv_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data,
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('diamond_jiajialv_info.html', array(
            'view' => new AppDiamondJiajialvView(new AppDiamondJiajialvModel(19))
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }

    /**
     * 	edit，渲染修改页面
     */
    public function edit($params) {
        $id = intval($params["id"]);
        //$tab_id = intval($params["tab_id"]);
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('diamond_jiajialv_info.html', array(
            'view' => new AppDiamondJiajialvView(new AppDiamondJiajialvModel($id, 19)),
                //'tab_id'=>$tab_id
        ));
        $result['title'] = '编辑';
        Util::jsonExit($result);
    }

    /**
     * 	show，渲染查看页面
     */
    public function show($params) {
        $id = intval($params["id"]);
        $this->render('diamond_jiajialv_show.html', array(
            'view' => new DiamondJiajialvView(new DiamondJiajialvModel($id, 19)),
            'bar' => Auth::getViewBar()
        ));
    }

    /**
     * 	insert，信息入库
     */
    public function insert($params) {
    	
        $result = array('success' => 0, 'error' => '');
        $good_type = _Post::getInt('good_type');
        $from_ad = _Post::getInt('from_ad');
        $cert = _Post::getString('cert');
        $cost_min = _Post::getFloat('cost_min');
        $cost_max = _Post::getFloat('cost_max');
        $jiajialv = _Post::getFloat('jiajialv');
        if ($jiajialv < 0 && $jiajialv >= 100) {
            $result['error'] = '加价应该在0-100之间';
            Util::jsonExit($result);
        }

        if($cost_min <0){
            $result['error'] = '最低成本价不能小于0';
            Util::jsonExit($result);
        }
        
        if($cost_max<0){
            $result['error'] = '最大成本价不能小于0';
            Util::jsonExit($result);
        }
        
        if ($cost_min > $cost_max && !empty($cost_max) && !empty($cost_min)) {
            $result['error'] = '最高成本价不能低于最低成本价';
            Util::jsonExit($result);
        }
        if($cost_min >= pow(10,8)){
        	$result['error'] = '最低成本价要小于100,000,000';
        	Util::jsonExit($result);
        }
        if($cost_max >= pow(10,8)){
        	$result['error'] = '最高成本价要小于100,000,000';
        	Util::jsonExit($result);
        }
        
        $olddo = array();
        $newdo = array(
            'good_type' => $good_type,
            'from_ad' => $from_ad,
            'cert' => $cert,
            'cost_min' => $cost_min,
            'cost_max' => $cost_max,
            'jiajialv' => $jiajialv,
        	'status'=>1
        );
        
        $newmodel = new AppDiamondJiajialvModel(20);
        //获取所有的数据
        $where_list = array('cert' => $cert,'from_ad'=>$from_ad,'good_type' => $good_type);
        $all_data = $newmodel->getAllList("`id`,`cost_min`,`cost_max`", $where_list);
        if(!empty($all_data)){
        foreach ($all_data as $v) {
        	if (($v['cost_min'] <= $newdo['cost_min'] && $newdo['cost_min'] < $v['cost_max'])) {
        		$result['error'] = '最低成本价在已有成本价('.$v['cost_min'].'~'.$v['cost_max'].')范围内';
        		Util::jsonExit($result);
        		break;
        	}
        	 
        	if(($v['cost_min'] < $newdo['cost_max'] && $newdo['cost_max'] < $v['cost_max'])){
        		$result['error'] = '最高成本价在已有成本价('.$v['cost_min'].'~'.$v['cost_max'].')范围内';
        		Util::jsonExit($result);
        		break;
        	}
        	if ($newdo['cost_min'] <= $v['cost_min'] && $newdo['cost_max'] >= $v['cost_max']) {
        		$result['error'] = '最低成本价和最高成本价完全包含已有成本价('.$v['cost_min'].'~'.$v['cost_max'].')';
        		Util::jsonExit($result);
        		break;
        	}
        }
    }
        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $diamondLogModel = new AppDiamondLogModel(20);
            $datas = array(
                'from_ad' => $from_ad,
                'operation_type' => '1',
                'operation_content' => '添加操作',
                'create_time' => date("Y-m-d H:i:s"),
                'create_user' => $_SESSION['userName'],
            );
            $diamondLogModel->saveData($datas, array());
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
        $good_type = _Post::getInt('good_type');
        $from_ad = _Post::getInt('from_ad');
        $cert = _Post::getString('cert');
        $cost_min = _Post::getFloat('cost_min');
        $cost_max = _Post::getFloat('cost_max');
        $jiajialv = _Post::getFloat('jiajialv');
        if ($jiajialv < 0 || $jiajialv >= 100) {
            $result['error'] = '加价应该在0-100之间';
            Util::jsonExit($result);
        }

        if($cost_min <0){
            $result['error'] = '最小成本价不能小于0';
            Util::jsonExit($result);
        }
        
        if($cost_max<0){
            $result['error'] = '最高成本价不能小于0';
            Util::jsonExit($result);
        }
            
        if ($cost_min > $cost_max) {
            $result['error'] = '最低成本价不能大于最高成本价';
            Util::jsonExit($result);
        }
        $newmodel = new AppDiamondJiajialvModel($id, 20);
        $olddo = $newmodel->getDataObject();
        $newdo = array(
            'id' => $id,
            'good_type' => $good_type,
            'from_ad' => $from_ad,
            'cert' => $cert,
            'cost_min' => $cost_min,
            'cost_max' => $cost_max,
            'jiajialv' => $jiajialv,
        );

        //获取所有的数据
        $where_list = array('cert' => $cert,'from_ad'=>$from_ad,'good_type' => $good_type);
        $all_data = $newmodel->getAllList("`id`,`cost_min`,`cost_max`", $where_list);
        
        if(!empty($all_data)){
        foreach ($all_data as $v) {
        	if($v['id'] == $id){
        		continue;
        	}
        	if (($v['cost_min'] <= $newdo['cost_min'] && $newdo['cost_min'] < $v['cost_max'])) {
        		$result['error'] = '最低成本价在已有成本价('.$v['cost_min'].'~'.$v['cost_max'].')范围内';
        		Util::jsonExit($result);
        		break;
        	}
        	 
        	if(($v['cost_min'] < $newdo['cost_max'] && $newdo['cost_max'] < $v['cost_max'])){
        		$result['error'] = '最高成本价在已有成本价('.$v['cost_min'].'~'.$v['cost_max'].')范围内';
        		Util::jsonExit($result);
        		break;
        	}
        	if ($newdo['cost_min'] <= $v['cost_min'] && $newdo['cost_max'] >= $v['cost_max']) {
        		$result['error'] = '最低成本价和最高成本价完全包含已有成本价('.$v['cost_min'].'~'.$v['cost_max'].')';
        		Util::jsonExit($result);
        		break;
        	}
        }
    }
    
        $info = $newmodel->array_difficult($newdo, $olddo);
        //如果没有任何修改，不做任何操作
        if (empty($info)) {
            $result['success'] = 1;
            Util::jsonExit($result);
        }
        //记录所有修改数据
        $change_data = '';
        foreach ($info as $val) {
            $change_data.=$val['name'] . '由' . $val['old'] . "改成" . $val['new'] . ",";
        }
        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $datas = array(
                'from_ad' => $from_ad,
                'operation_type' => '2',
                'operation_content' => rtrim($change_data, ","),
                'create_time' => date("Y-m-d H:i:s"),
                'create_user' => $_SESSION['userName'],
            );
            $diamondLogModel = new AppDiamondLogModel(20);
            
            $diamondLogModel->saveData($datas, array());

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
    public function delete($params) {
    	
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new AppDiamondJiajialvModel($id, 20);
        $do = $model->getDataObject();
        if( $do['status']== 0){
            $result['error'] = "此记录状态为停用";
            Util::jsonExit($result); 
        }
        $model->setValue('status', 0 );
        $res = $model->save(true);
       
        //$res = $model->delete();
        if ($res !== false) {
			$datas = array(
                'from_ad' => $do['from_ad'],
                'operation_type' => '3',
                'operation_content' => '停用',
                'create_time' => date("Y-m-d H:i:s"),
                'create_user' => $_SESSION['userName'],
            );
			
            $diamondLogModel = new AppDiamondLogModel(20);
            $diamondLogModel->saveData($datas, array());	//saveData函数保存不了(数据库设计问题)
//             print_r($diamondLogModel->saveData($datas, array()));exit;
            $result['success'] = 1;
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
        $model = new AppDiamondJiajialvModel($id, 20);
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
			$datas = array(
                'from_ad' => $do['from_ad'],
                'operation_type' => '4',
                'operation_content' => '启用',
                'create_time' => date("Y-m-d H:i:s"),
                'create_user' => $_SESSION['userName'],
            );
            $diamondLogModel = new AppDiamondLogModel(20);
            $diamondLogModel->saveData($datas, array());
           // print_r($diamondLogModel->saveData($datas, array()));exit;
        } else {
            $result['error'] = "删除失败";
        }
        Util::jsonExit($result);
    }

}

?>