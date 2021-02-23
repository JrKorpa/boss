<?php
use vipapis\delivery\Delivery;
/**
 *  -------------------------------------------------
 *   @file		: VipDeliveryDetailsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @date		: 2016-06-26
 *   @update	:
 *  -------------------------------------------------
 */
class VipDeliveryDetailsModel extends Model
{
    protected $deliveryService = null;
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'vip_delivery_details';
      	$this->pk='pick_no';
		$this->_prefix='';
	    $this->_dataObject = array(
	               
	    );        
		parent::__construct($id,$strConn);
		require_once KELA_PATH.'/vendor/vopsdk/VopWrapper.class.php';		
		require_once KELA_PATH.'/vendor/vopsdk/ApiDeliveryService.class.php';
		$this->deliveryService = new ApiDeliveryService();
	}
}