<?php

/**
 *  -------------------------------------------------
 *   @file		: AppStyleGalleryController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 11:31:36
 *   @update	:
 *  -------------------------------------------------
 */
class AppStyleGalleryController extends CommonController {

    protected $smartyDebugEnabled = true;

    /**
     * 	index，搜索框
     */
    public function index($params) {
//		Util::M('app_style_gallery','front',11);	//生成模型后请注释该行
//		Util::V('app_style_gallery',11);	//生成视图后请注释该行
        $this->render('app_style_gallery_search_form.html', array('bar' => Auth::getBar()));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'style_id' => _Request::getInt('_id')
        );
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
        $where = array();
        $where['style_id'] = $args['style_id'];


        $model = new AppStyleGalleryModel(11);
        $data = $model->pageList($where, $page, 20, false, true);
        $imagePlace = $model->getImagePlaceList();
        $this->assign('imagePlace', $imagePlace);
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_style_gallery_search_page';
        $this->render('app_style_gallery_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $style_id = _Request::getInt('_id');
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('app_style_gallery_info.html', array(
            'view' => new AppStyleGalleryView(new AppStyleGalleryModel(11)),
            'style_id' => $style_id
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }

    /**
     * 	edit，渲染修改页面
     */
    public function edit($params) {
        $id = intval($params["id"]);
        $tab_id = _Post::getInt('tab_id'); //主记录对应的列表页签id
        $result = array('success' => 0, 'error' => '');
        $v = new AppStyleGalleryView(new AppStyleGalleryModel($id, 11));
        $result['content'] = $this->fetch('app_style_gallery_info.html', array(
            'view' => $v,
            'tab_id' => $tab_id,
            '_id' => $v->get_style_id()
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
        $this->render('app_style_gallery_show.html', array(
            'view' => new AppStyleGalleryView(new AppStyleGalleryModel($id, 11))
        ));
    }

    /**
     * 	insert，信息入库
     */
    public function insert($params) {
        $result = array('success' => 0, 'error' => '');
		//var_dump($_FILES);exit;
        $style_id = _Post::get('style_id');
       // $img_ori = $_FILES['img_ori']['name'];
        //$tmp_name = $_FILES['img_ori']['tmp_name'];
        
//        if(isset($_FILES['img_ori']))
//        {
//        	$type = UploadImage::getExt($_FILES['img_ori']['name']);
//        	$upload = new Upload();
//        
//        	if(!in_array($type,$upload->img)){
//        		$result['error'] = "文件不符合类型！";
//        		Util::jsonExit($result);
//        	}
//        
//        	$res = $upload->toUP($_FILES['img_ori']);
//        	$img_ori=$res['url'];
//        }
        
        //$img_ori = _Post::get('img_ori');
        $image_place = _Post::get('image_place');
        $img_sort = _Post::get('img_sort');
        $img_ori = _Post::getString('img_ori');

        if($img_ori==''){
            $result['error'] = '上传图片不能为空';
            Util::jsonExit($result);
        }
        if($image_place==''){
            $result['error'] = '请选择图片位置';
            Util::jsonExit($result);
        }
        if($img_sort==''){
            $result['error'] = '请填写图片排序';
            Util::jsonExit($result);
        }

		
        $olddo = array();
        $newdo = array();
        if($img_ori){
        	$newdo['img_ori'] = $img_ori;
        	$newdo['thumb_img'] = $img_ori;
        	$newdo['middle_img'] = $img_ori;
        	$newdo['big_img'] = $img_ori;
        }else{
        	$result['error'] = '图片上传出错！';
        	Util::jsonExit($result);
        }
        $newdo['style_id'] = $style_id;
       
        $newdo['image_place'] = $image_place;
        $newdo['img_sort'] = $img_sort;

        $newmodel = new AppStyleGalleryModel(12);
        //var_dump($newdo);exit;
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
        $style_id = _Post::get('style_id');
        $img_ori = _Post::get('img_ori');
        $image_place = _Post::get('image_place');
        $img_sort = _Post::get('img_sort');

        $newmodel = new AppStyleGalleryModel($id, 12);

        $olddo = $newmodel->getDataObject();
        $newdo = array();
        $newdo['g_id'] = $id;
        $newdo['style_id'] = $style_id;
        $newdo['img_ori'] = $img_ori;
        $newdo['thumb_img'] = $img_ori;
        $newdo['middle_img'] = $img_ori;
        $newdo['big_img'] = $img_ori;
        $newdo['image_place'] = $image_place;
        $newdo['img_sort'] = $img_sort;

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
        $model = new AppStyleGalleryModel($id, 12);
        $do = $model->getDataObject();
        $valid = $do['is_system'];
        if ($valid) {
            $result['error'] = "当前记录为系统内置，禁止删除";
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

}

?>