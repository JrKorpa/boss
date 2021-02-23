<?php
/**
 *  -------------------------------------------------
 *   @file		: RelStyleStoneView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 16:50:57
 *   @update	:
 *  -------------------------------------------------
 */
class RelStyleStoneView extends View
{
	protected $_id;
	protected $_style_id;
	protected $_stone_position;
	protected $_stone_cat;
	protected $_stone_attr;


	public function get_id(){return $this->_id;}
	public function get_style_id(){return $this->_style_id;}
	public function get_stone_position(){return $this->_stone_position;}
	public function get_stone_cat(){return $this->_stone_cat;}
	public function get_stone_attr(){return $this->_stone_attr;}
        
        
        public function getStoneCatList() {
                $model = new RelStyleStoneModel(11);
                return $model->getStoneCatList();
        }
        
        public function functionName($param) {
                $model = new RelStyleStoneModel(11);
                return $model->getStyleIdRes($style_id);
        }

}
?>