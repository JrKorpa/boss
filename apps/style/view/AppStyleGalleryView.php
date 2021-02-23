<?php
/**
 *  -------------------------------------------------
 *   @file		: AppStyleGalleryView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 11:53:48
 *   @update	:
 *  -------------------------------------------------
 */
class AppStyleGalleryView extends View
{
	protected $_g_id;
	protected $_style_id;
	protected $_image_place;
	protected $_img_sort;
	protected $_img_ori;
	protected $_thumb_img;
	protected $_middle_img;
	protected $_big_img;


	public function get_g_id(){return $this->_g_id;}
	public function get_style_id(){return $this->_style_id;}
	public function get_image_place(){return $this->_image_place;}
	public function get_img_sort(){return $this->_img_sort;}
	public function get_img_ori(){return $this->_img_ori;}
	public function get_thumb_img(){return $this->_thumb_img;}
	public function get_middle_img(){return $this->_middle_img;}
	public function get_big_img(){return $this->_big_img;}
        
        
        public function getImagePlaceList() {
            $model = new AppStyleGalleryModel(11);
            return $model->getImagePlaceList();
        }

}
?>