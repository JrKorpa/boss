<?php

/**
 *  -------------------------------------------------
 *   @file		: AppStyleGalleryModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 11:53:48
 *   @update	:
 *  -------------------------------------------------
 */
class AppStyleGalleryModel extends Model {

    public $_prefix = 'g';

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_style_gallery';
        $this->_dataObject = array("g_id" => "自增ID",
            "style_id" => "款式id",
            "image_place" => "图片位置，100=网络上架，6=表现工艺，5=证书图,1=正立45°图,2=正立图,3=爪头图,4=爪尾图,5=内臂图,7=质检专用图",
            "img_sort" => "图片排序",
            "img_ori" => "原图路径",
            "thumb_img" => "缩略图",
            "middle_img" => "中图",
            "big_img" => "大图");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url ApplicationController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true, $sort_by_place = false) {
        $sql = "SELECT a.*,b.style_sn FROM `" . $this->table() . "` a,base_style_info b  WHERE 1 and a.style_id=b.style_id";

        if (isset($where['style_id']) && $where['style_id'] > 0) {
            $sql .= " AND a.style_id = {$where['style_id']}";
        }

        if (!$sort_by_place) {
        	$sql .= " ORDER BY `a`.`img_sort` DESC,a.g_id DESC";
        } else {
        	$sql .= " ORDER BY (case when a.`image_place` is null or a.`image_place` = 0 then 999 else a.`image_place` end) ASC, a.`img_sort` ASC";
        }
        
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    public function getImagePlaceList() {
        $data = array(
            '100' => array('id' => '100', 'name' => '网络上架'),
            '6' => array('id' => '6', 'name' => '表现工艺'),
            '5' => array('id' => '5', 'name' => '证书图'),
            '1' => array('id' => '1', 'name' => '正立45°图'),
            '2' => array('id' => '2', 'name' => '正立图'),
            '3' => array('id' => '3', 'name' => '爪头图'),
            '4' => array('id' => '4', 'name' => '爪尾图'),
            '8' => array('id' => '8', 'name' => '内臂图'),
            '7' => array('id' => '7', 'name' => '质检专用图')
        );
        return $data;
    }

    //获取款式所缩略图
    public function getStyleGalleryInfo($con) {
        $sql = "SELECT `thumb_img`,`big_img` FROM `" . $this->table() . "` WHERE `style_id` = {$con['style_id']} AND `style_sn` = '{$con['style_sn']}' AND `image_place` = 1";
        //echo $sql;die;
        return $this->db()->getRow($sql);
    }
    //查询款式图库
    public function getStyleGalleryRow($style_sn,$image_place = 1){
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE `style_sn` = '{$style_sn}' AND `image_place` = {$image_place}";
        return $this->db()->getRow($sql);
    }

}

?>