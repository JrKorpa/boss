<?php
/**
 *  -------------------------------------------------
 *   @file		: ForumLinksView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-17 15:54:24
 *   @update	:
 *  -------------------------------------------------
 */
class ForumLinksView extends View
{
	protected $_id;
	protected $_title;
	protected $_url_img;
	protected $_url_addr;
	protected $_display_order;


	public function get_id(){return $this->_id;}
	public function get_title(){return $this->_title;}
	public function get_url_img(){return $this->_url_img;}
	public function get_url_addr(){return $this->_url_addr;}
	public function get_display_order(){return $this->_display_order;}

}
?>