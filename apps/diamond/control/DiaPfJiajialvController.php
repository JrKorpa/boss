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
class DiaPfJiajialvController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $diamondview = array();
    protected $fromad_arr = array();

    public function __construct() {
        parent::__construct();
        $model = new DiamondInfoModel(19);
        $this->diamondview = new DiamondInfoView(new DiamondInfoModel(19));
        $this->assign('diamondview', $this->diamondview);

        $this->fromad_arr = $model->getForm_ad();
        $this->assign("from_ad",$this->fromad_arr);
    }

    /**
     * 	index，搜索框
     */
    public function index($params) {
        $this->render('pf_jiajialv_search_form.html', array('bar' => Auth::getBar()));
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
            'from_ad'=> _Request::getString('from_ad'),
            'cert'=> _Request::getString('cert'),
            'status'=> _Request::getString('status'),
        );
        $page = _Request::getInt("page", 1);
        $where = array(
            'carat_min'=>  _Request::getFloat('carat_min'),
            'carat_max'=> _Request::getFloat('carat_max'),
            'good_type'=> _Request::getInt('good_type'),
        	'from_ad'=> _Request::getString('from_ad'),
            'cert'=> _Request::getString('cert'),
            'status'=> _Request::getString('status'),
        );

        $model = new DiaPfJiajialvModel(19);
        $data = $model->pageList($where, $page, 50, false);
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'pf_jiajialv_search_page';
        $this->render('pf_jiajialv_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data,
            'from_ad' => $this->diamondview->getFromAdList(),
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
        	$result['content'] = $this->fetch('pf_jiajialv_info.html', array(
        			'view' => new DiaPfJiajialvView(new DiaPfJiajialvModel(19))
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
	        $result['content'] = $this->fetch('pf_jiajialv_info.html', array(
	            'view' => new DiaPfJiajialvView(new DiaPfJiajialvModel($id, 19)),
	                //'tab_id'=>$tab_id
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
            'view' => new DiamondJiajialvView(new DiamondJiajialvModel($id, 19)),
            'bar' => Auth::getViewBar()
        ));
    }

    /**
     * 	insert，信息入库
     */
    private function insert($params) {
        $result = array('success' => 0, 'error' => '');

        $good_type = _Post::getInt('good_type');
        $from_ad = _Post::getString('from_ad');
        $cert = _Post::getString('cert');
        $carat_min = _Post::getFloat('carat_min');
        $carat_max = _Post::getFloat('carat_max');
        $jiajialv = _Post::getFloat('jiajialv');
        $color = _Post::getString('color');
        $clarity = _Post::getString('clarity');
        if ($jiajialv < 0 && $jiajialv >= 100) {
            $result['error'] = '加价应该在0-100之间';
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
            'from_ad' => $from_ad,
            'cert' => $cert,
            'carat_min' => $carat_min,
            'carat_max' => $carat_max,
            'jiajialv' => $jiajialv,
        	'color' => $color,
        	'clarity' => $clarity
        );
        $newmodel = new DiaPfJiajialvModel(20);
        //获取所有的数据
        $where_list = array('cert' => $cert,'from_ad'=>$from_ad,'good_type' => $good_type);
        $all_data = $newmodel->getAllList("`carat_min`,`carat_max`, ifnull(`color`,'') as color, ifnull(`clarity`, '') as clarity, `good_type`, ifnull(cert, '') as cert, ifnull(from_ad, '') as from_ad ", $where_list);
        $do = true;
        foreach ($all_data as $v) {
        	if ( $v['from_ad'] != $newdo['from_ad'] || $v['color'] != $newdo['color'] || $v['clarity'] != $newdo['clarity'] || $v['good_type'] != $newdo['good_type'] || $v['cert'] != $newdo['cert']) {
        		continue;
        	}
        	
        	if ($v['carat_min'] <= $newdo['carat_min'] && $newdo['carat_min'] < $v['carat_max']) {
        		$do = FALSE;
        		break;
        	}
        	
        	if($v['carat_min'] < $newdo['carat_max'] && $newdo['carat_max'] < $v['carat_max']){
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
        $from_ad = _Post::getString('from_ad');
        $cert = _Post::getString('cert');
        $carat_min = _Post::getFloat('carat_min');
        $carat_max = _Post::getFloat('carat_max');
        $jiajialv = _Post::getFloat('jiajialv');
        $color = _Post::getString('color');
        $clarity = _Post::getString('clarity');
        if ($jiajialv < 0 || $jiajialv >= 100) {
            $result['error'] = '加价应该在0-100之间';
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
        $newmodel = new DiaPfJiajialvModel($id, 20);
        $olddo = $newmodel->getDataObject();
        $newdo = array(
        		'id' => $id,
        		'good_type' => $good_type,
        		'from_ad' => $from_ad,
        		'cert' => $cert,
        		'carat_min' => $carat_min,
        		'carat_max' => $carat_max,
        		'jiajialv' => $jiajialv,
        		'color' => $color,
        		'clarity' => $clarity
        );

        //获取所有的数据
        $where_list = array('cert' => $cert,'from_ad'=>$from_ad,'good_type' => $good_type);
        $all_data = $newmodel->getAllList("id, `carat_min`,`carat_max`, ifnull(`color`,'') as color, ifnull(`clarity`, '') as clarity, `good_type`, ifnull(`cert`, '') as cert, ifnull(from_ad, '') as from_ad ", $where_list);
        $do = true;
        foreach ($all_data as $v) {
        	if($v['id'] == $id){
        		continue;
        	}
        	
        	if ( $v['from_ad'] != $newdo['from_ad'] || $v['color'] != $newdo['color'] || $v['clarity'] != $newdo['clarity'] || $v['good_type'] != $newdo['good_type'] || $v['cert'] != $newdo['cert']) {
        		continue;
        	}
        	
        	if ($v['carat_min'] <= $newdo['carat_min'] && $newdo['carat_min'] < $v['carat_max']) {
                $do = FALSE;
                break;
            }
           
            if($v['carat_min'] < $newdo['carat_max'] && $newdo['carat_max'] < $v['carat_max']){
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
            $result['_cls'] = $_cls;
            $result['tab_id'] = $tab_id;
            $result['title'] = '修改裸钻批发加价率';
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
        $model = new DiaPfJiajialvModel($id, 20);
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
        $model = new DiaPfJiajialvModel($id, 20);
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
        } else {
            $result['error'] = "删除失败";
        }
        Util::jsonExit($result);
    }
}

?>