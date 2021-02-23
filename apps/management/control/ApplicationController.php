<?php

/**
 *  -------------------------------------------------
 *   @file		: ApplicationController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-19
 *   @update	:
 *  -------------------------------------------------
 */
class ApplicationController extends CommonController {

        protected $smartyDebugEnabled = false;

        /**
         * 	index，搜索框
         */
        public function index($params) {
                if (Auth::$userType > 2) {
                        die('操作禁止');
                }
                $this->render('application_search_form.html', array('bar' => Auth::getBar()));
        }

        /**
         * 	search，项目列表
         */
        public function search($params) {
                $args = array(
                        'mod' => _Request::get("mod"),
                        'con' => substr(__CLASS__, 0, -10),
                        'act' => __FUNCTION__,
                        'code' => _Request::get('code'),
                        'label' => _Request::get('label')
                );
                $page = _Request::getInt("page", 1);
                $where = array();
                $where['label'] = $args['label'];
                $where['code'] = $args['code'];
                $where['is_deleted'] = 0;

                $model = new ApplicationModel(1);
                $data = $model->pageList($where, $page, 10, false);
                $pageData = $data;
                $pageData['filter'] = $args;
                $pageData['jsFuncs'] = 'application_search_page';
                $this->render('application_search_list.html', array(
                        'pa' => Util::page($pageData),
                        'page_list' => $data
                ));
        }

        /**
         * 	add，渲染添加页面
         */
        public function add() {
                $result = array('success' => 0, 'error' => '');
                $result['content'] = $this->fetch('application_info.html', array(
                        'view' => new ApplicationView(new ApplicationModel(1))
                ));


                $result['title'] = '项目-添加';
                Util::jsonExit($result);
        }

        /**
         * 	edit，渲染修改页面
         */
        public function edit($params) {
                $id = intval($params["id"]);
                $result = array('success' => 0, 'error' => '');
                $result['content'] = $this->fetch('application_info.html', array(
                        'view' => new ApplicationView(new ApplicationModel($id, 1))
                ));
                $result['title'] = '项目-编辑';
                Util::jsonExit($result);
        }

        /**
         * 	show，渲染查看页面
         */
        public function show($params) {
                $id = intval($params["id"]);
                $this->render('application_show.html', array(
                        'view' => new ApplicationView(new ApplicationModel($id, 1)),
                        'bar' => Auth::getViewBar(),
                        'bar1' => Auth::getDetailBar('menu_group')
                ));
        }

        /**
         * 	insert，信息入库
         */
        public function insert($params) {
                $result = array('success' => 0, 'error' => '');
                $label = _Post::get('label');
                $code = strtolower(_Post::get('code'));
                $icon = _Post::getInt('icon');
                $is_enabled = _Post::getInt('is_enabled');
                if ($label == '') {
                        $result['error'] = "项目名称不能为空！";
                        Util::jsonExit($result);
                }

                if (!Util::isChinese($label)) {
                        $result['error'] = "项目名称只能是汉字！";
                        Util::jsonExit($result);
                }
                if (mb_strlen($label) > 10) {
                        $result['error'] = "项目名称不能超过10个汉字！";
                        Util::jsonExit($result);
                }
                if ($code == '') {
                        $result['error'] = "项目文件夹不能为空！";
                        Util::jsonExit($result);
                }
                if (!Util::isEnglish($code)) {
                        $result['error'] = "项目文件夹只能填小写字母！";
                        Util::jsonExit($result);
                }
                if (mb_strlen($code) > 10) {
                        $result['error'] = "项目文件夹不能超过40个字母！";
                        Util::jsonExit($result);
                }
                $olddo = array();
                $newdo = array(
                        "label" => $label,
                        "code" => $code,
                        "icon" => $icon,
                        "is_enabled" => $is_enabled,
                        "display_order" => 0
                );

                $newmodel = new ApplicationModel(2);
                if ($newmodel->hasLabel($label)) {
                        $result['error'] = "项目名称重复！";
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
        public function update($params) {
                $result = array('success' => 0, 'error' => '');
                $id = _Post::getInt('id');
                $label = _Post::get('label');
                $code = strtolower(_Post::get('code'));
                $icon = _Post::getInt('icon');
                $is_enabled = _Post::getInt('is_enabled');
                if ($label == '') {
                        $result['error'] = "项目名称不能为空！";
                        Util::jsonExit($result);
                }

                if (!Util::isChinese($label)) {
                        $result['error'] = "项目名称只能是汉字！";
                        Util::jsonExit($result);
                }
                if (mb_strlen($label) > 10) {
                        $result['error'] = "项目名称不能超过10个汉字！";
                        Util::jsonExit($result);
                }
                if ($code == '') {
                        $result['error'] = "项目文件夹不能为空！";
                        Util::jsonExit($result);
                }
                if (!Util::isEnglish($code)) {
                        $result['error'] = "项目文件夹只能填小写字母！";
                        Util::jsonExit($result);
                }
                if (mb_strlen($code) > 10) {
                        $result['error'] = "项目文件夹不能超过40个字母！";
                        Util::jsonExit($result);
                }


                $newmodel = new ApplicationModel($id, 2);
                if ($newmodel->hasLabel($label)) {
                        $result['error'] = "项目名称重复！";
                        Util::jsonExit($result);
                }
                $olddo = $newmodel->getDataObject();
                $newdo = array(
                        "id" => $id,
                        "label" => $label,
                        "code" => $code,
                        "icon" => $icon,
                        "is_enabled" => $is_enabled
                );

                $res = $newmodel->saveData($newdo, $olddo);
                if ($res !== false) {
                        $result['success'] = 1;
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
                $model = new ApplicationModel($id, 2);
                $do = $model->getDataObject();
                if ($do['is_system']) {
                        $result['error'] = "当前记录为系统内置，禁止删除";
                        Util::jsonExit($result);
                }
                if ($model->hasRelData($id)) {
                        $result['error'] = "存在关联数据，禁止删除";
                        Util::jsonExit($result);
                }
                $model->setValue('is_deleted', 1);
                $res = $model->save(true);
                if ($res !== false) {
                        $result['success'] = 1;
                } else {
                        $result['error'] = "删除失败";
                }
                Util::jsonExit($result);
        }

        /**
         * 	排序页面
         */
        public function listAll() {
                $result = array('success' => 0, 'error' => '');
                $model = new ApplicationModel(1);
                $data = $model->getAppList();
                $result['content'] = $this->fetch('application_sort.html', array('data' => $data));
                $result['title'] = '项目-排序';
                Util::jsonExit($result);
        }

        /**
         * 	saveSort,排序保存
         */
        public function saveSort() {
                $result = array('success' => 0, 'error' => '');
                $datas = _Post::getList('ApplicationArray');
                krsort($datas);
                $datas = array_values($datas);
                $model = new ApplicationModel(1);
                $res = $model->saveSort($datas);
                if ($res) {
                        $result['success'] = 1;
                } else {
                        $result['error'] = "操作失败";
                }
                Util::jsonExit($result);
        }

}

?>