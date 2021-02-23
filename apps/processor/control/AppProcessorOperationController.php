<?php

/**
 *  -------------------------------------------------
 *   @file		: AppProcessorOperationController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-15 17:12:26
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorOperationController extends CommonController {

    protected $smartyDebugEnabled = false;

    /**
     * 	index，搜索框
     */
    public function index($params) {
        $this->render('app_processor_operation_search_form.html', array('view' => new AppProcessorOperationView(new AppProcessorOperationModel(13)), 'bar' => Auth::getBar()));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'name'=> _Request::getString('name'),
            'type'=> _Request::getString('type'),
            'create_user'=> _Request::getString('create_user'),
            'start_time'=> _Request::getString('start_time'),
            'end_time'=> _Request::getString('end_time'),
                //'参数' = _Request::get("参数");
        );
        $page = _Request::getInt("page", 1);
        $where = array();
        $where['name'] = $args['name'];
        $where['type'] = $args['type'];
        $where['create_user'] = $args['create_user'];
        $where['start_time'] = $args['start_time'];
        $where['end_time'] = $args['end_time'];

        $model = new AppProcessorOperationModel(13);
        $data = $model->pageList($where, $page, 10, false);
        if ($data['data']) {
            foreach ($data['data'] as $key => &$value) {
                $value['status'] = $model->getTypeList($value['operation_type']);
            }
            unset($value);
        }
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_processor_operation_search_page';
        $this->render('app_processor_operation_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('app_processor_operation_info.html', array(
            'view' => new AppProcessorOperationView(new AppProcessorOperationModel(13))
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
        $result['content'] = $this->fetch('app_processor_operation_info.html', array(
            'view' => new AppProcessorOperationView(new AppProcessorOperationModel($id, 13)),
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
        $this->render('app_processor_operation_show.html', array(
            'view' => new AppProcessorOperationView(new AppProcessorOperationModel($id, 13)),
            'bar' => Auth::getViewBar()
        ));
    }

    /**
     * 	insert，信息入库
     */
    public function insert($params) {
        $result = array('success' => 0, 'error' => '');
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
        exit;
        $olddo = array();
        $newdo = array();

        $newmodel = new AppProcessorOperationModel(14);
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
    public function update($params) {
        $result = array('success' => 0, 'error' => '');
        $_cls = _Post::getInt('_cls');
        $tab_id = _Post::getInt('tab_id');

        $id = _Post::getInt('id');
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
        exit;

        $newmodel = new AppProcessorOperationModel($id, 14);

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
        $model = new AppProcessorOperationModel($id, 14);
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

}

?>