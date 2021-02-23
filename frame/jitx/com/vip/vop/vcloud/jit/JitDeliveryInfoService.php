<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\jit;
interface JitDeliveryInfoServiceIf{
	
	
	public function addbox( $storageNo, $vendorId);
	
	public function boxItem( $vendorId, $poNo, $storageNo, $vendorType, $barcode, $boxNo, $amount, $productName, $subPickNo);
	
	public function confirmDelivery(\com\vip\vop\vcloud\jit\ConfirmDeliveryRequest $request);
	
	public function createDelivery(\com\vip\vop\vcloud\jit\CreateDeliveryRequest $request);
	
	public function deleteBoxedItem( $boxNo, $barcode, $storageNo, $vendorId);
	
	public function deleteDeliveryDetail(\com\vip\vop\vcloud\jit\DeleteDeliveryDetailRequest $request);
	
	public function editDelivery(\com\vip\vop\vcloud\jit\EditDeliveryRequest $request);
	
	public function getBox( $storageNo, $vendorId);
	
	public function getBoxItem( $deliveryId, $boxNo);
	
	public function getDeliveryDetail(\com\vip\vop\vcloud\jit\GetDeliveryDetailRequest $request);
	
	public function getDeliveryGoods(\com\vip\vop\vcloud\jit\GetDeliveryGoodsRequest $request);
	
	public function getDeliveryList(\com\vip\vop\vcloud\jit\GetDeliveryListRequest $request);
	
	public function getDeliverySubPick( $storageNo, $vendorId);
	
	public function getDeliveryTrace(\com\vip\vop\vcloud\jit\GetDeliveryTraceRequest $request);
	
	public function healthCheck();
	
	public function importDeliveryDetail(\com\vip\vop\vcloud\jit\ImportDeliveryDetailRequest $request);
	
	public function submitDelivery( $vendorId, $storageNo);
	
	public function syncDeliveryInfo();
	
}

class _JitDeliveryInfoServiceClient extends \Osp\Base\OspStub implements \com\vip\vop\vcloud\jit\JitDeliveryInfoServiceIf{
	
	public function __construct(){
		
		parent::__construct("com.vip.vop.vcloud.jit.JitDeliveryInfoService", "1.0.0");
	}
	
	
	public function addbox( $storageNo, $vendorId){
		
		$this->send_addbox( $storageNo, $vendorId);
		return $this->recv_addbox();
	}
	
	public function send_addbox( $storageNo, $vendorId){
		
		$this->initInvocation("addbox");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_addbox_args();
		
		$args->storageNo = $storageNo;
		
		$args->vendorId = $vendorId;
		
		$this->send_base($args);
	}
	
	public function recv_addbox(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_addbox_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function boxItem( $vendorId, $poNo, $storageNo, $vendorType, $barcode, $boxNo, $amount, $productName, $subPickNo){
		
		$this->send_boxItem( $vendorId, $poNo, $storageNo, $vendorType, $barcode, $boxNo, $amount, $productName, $subPickNo);
		return $this->recv_boxItem();
	}
	
	public function send_boxItem( $vendorId, $poNo, $storageNo, $vendorType, $barcode, $boxNo, $amount, $productName, $subPickNo){
		
		$this->initInvocation("boxItem");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_boxItem_args();
		
		$args->vendorId = $vendorId;
		
		$args->poNo = $poNo;
		
		$args->storageNo = $storageNo;
		
		$args->vendorType = $vendorType;
		
		$args->barcode = $barcode;
		
		$args->boxNo = $boxNo;
		
		$args->amount = $amount;
		
		$args->productName = $productName;
		
		$args->subPickNo = $subPickNo;
		
		$this->send_base($args);
	}
	
	public function recv_boxItem(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_boxItem_result();
		$this->receive_base($result);
		
	}
	
	
	public function confirmDelivery(\com\vip\vop\vcloud\jit\ConfirmDeliveryRequest $request){
		
		$this->send_confirmDelivery( $request);
		return $this->recv_confirmDelivery();
	}
	
	public function send_confirmDelivery(\com\vip\vop\vcloud\jit\ConfirmDeliveryRequest $request){
		
		$this->initInvocation("confirmDelivery");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_confirmDelivery_args();
		
		$args->request = $request;
		
		$this->send_base($args);
	}
	
	public function recv_confirmDelivery(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_confirmDelivery_result();
		$this->receive_base($result);
		
	}
	
	
	public function createDelivery(\com\vip\vop\vcloud\jit\CreateDeliveryRequest $request){
		
		$this->send_createDelivery( $request);
		return $this->recv_createDelivery();
	}
	
	public function send_createDelivery(\com\vip\vop\vcloud\jit\CreateDeliveryRequest $request){
		
		$this->initInvocation("createDelivery");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_createDelivery_args();
		
		$args->request = $request;
		
		$this->send_base($args);
	}
	
	public function recv_createDelivery(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_createDelivery_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function deleteBoxedItem( $boxNo, $barcode, $storageNo, $vendorId){
		
		$this->send_deleteBoxedItem( $boxNo, $barcode, $storageNo, $vendorId);
		return $this->recv_deleteBoxedItem();
	}
	
	public function send_deleteBoxedItem( $boxNo, $barcode, $storageNo, $vendorId){
		
		$this->initInvocation("deleteBoxedItem");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_deleteBoxedItem_args();
		
		$args->boxNo = $boxNo;
		
		$args->barcode = $barcode;
		
		$args->storageNo = $storageNo;
		
		$args->vendorId = $vendorId;
		
		$this->send_base($args);
	}
	
	public function recv_deleteBoxedItem(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_deleteBoxedItem_result();
		$this->receive_base($result);
		
	}
	
	
	public function deleteDeliveryDetail(\com\vip\vop\vcloud\jit\DeleteDeliveryDetailRequest $request){
		
		$this->send_deleteDeliveryDetail( $request);
		return $this->recv_deleteDeliveryDetail();
	}
	
	public function send_deleteDeliveryDetail(\com\vip\vop\vcloud\jit\DeleteDeliveryDetailRequest $request){
		
		$this->initInvocation("deleteDeliveryDetail");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_deleteDeliveryDetail_args();
		
		$args->request = $request;
		
		$this->send_base($args);
	}
	
	public function recv_deleteDeliveryDetail(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_deleteDeliveryDetail_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function editDelivery(\com\vip\vop\vcloud\jit\EditDeliveryRequest $request){
		
		$this->send_editDelivery( $request);
		return $this->recv_editDelivery();
	}
	
	public function send_editDelivery(\com\vip\vop\vcloud\jit\EditDeliveryRequest $request){
		
		$this->initInvocation("editDelivery");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_editDelivery_args();
		
		$args->request = $request;
		
		$this->send_base($args);
	}
	
	public function recv_editDelivery(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_editDelivery_result();
		$this->receive_base($result);
		
	}
	
	
	public function getBox( $storageNo, $vendorId){
		
		$this->send_getBox( $storageNo, $vendorId);
		return $this->recv_getBox();
	}
	
	public function send_getBox( $storageNo, $vendorId){
		
		$this->initInvocation("getBox");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_getBox_args();
		
		$args->storageNo = $storageNo;
		
		$args->vendorId = $vendorId;
		
		$this->send_base($args);
	}
	
	public function recv_getBox(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_getBox_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getBoxItem( $deliveryId, $boxNo){
		
		$this->send_getBoxItem( $deliveryId, $boxNo);
		return $this->recv_getBoxItem();
	}
	
	public function send_getBoxItem( $deliveryId, $boxNo){
		
		$this->initInvocation("getBoxItem");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_getBoxItem_args();
		
		$args->deliveryId = $deliveryId;
		
		$args->boxNo = $boxNo;
		
		$this->send_base($args);
	}
	
	public function recv_getBoxItem(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_getBoxItem_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getDeliveryDetail(\com\vip\vop\vcloud\jit\GetDeliveryDetailRequest $request){
		
		$this->send_getDeliveryDetail( $request);
		return $this->recv_getDeliveryDetail();
	}
	
	public function send_getDeliveryDetail(\com\vip\vop\vcloud\jit\GetDeliveryDetailRequest $request){
		
		$this->initInvocation("getDeliveryDetail");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_getDeliveryDetail_args();
		
		$args->request = $request;
		
		$this->send_base($args);
	}
	
	public function recv_getDeliveryDetail(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_getDeliveryDetail_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getDeliveryGoods(\com\vip\vop\vcloud\jit\GetDeliveryGoodsRequest $request){
		
		$this->send_getDeliveryGoods( $request);
		return $this->recv_getDeliveryGoods();
	}
	
	public function send_getDeliveryGoods(\com\vip\vop\vcloud\jit\GetDeliveryGoodsRequest $request){
		
		$this->initInvocation("getDeliveryGoods");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_getDeliveryGoods_args();
		
		$args->request = $request;
		
		$this->send_base($args);
	}
	
	public function recv_getDeliveryGoods(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_getDeliveryGoods_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getDeliveryList(\com\vip\vop\vcloud\jit\GetDeliveryListRequest $request){
		
		$this->send_getDeliveryList( $request);
		return $this->recv_getDeliveryList();
	}
	
	public function send_getDeliveryList(\com\vip\vop\vcloud\jit\GetDeliveryListRequest $request){
		
		$this->initInvocation("getDeliveryList");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_getDeliveryList_args();
		
		$args->request = $request;
		
		$this->send_base($args);
	}
	
	public function recv_getDeliveryList(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_getDeliveryList_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getDeliverySubPick( $storageNo, $vendorId){
		
		$this->send_getDeliverySubPick( $storageNo, $vendorId);
		return $this->recv_getDeliverySubPick();
	}
	
	public function send_getDeliverySubPick( $storageNo, $vendorId){
		
		$this->initInvocation("getDeliverySubPick");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_getDeliverySubPick_args();
		
		$args->storageNo = $storageNo;
		
		$args->vendorId = $vendorId;
		
		$this->send_base($args);
	}
	
	public function recv_getDeliverySubPick(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_getDeliverySubPick_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getDeliveryTrace(\com\vip\vop\vcloud\jit\GetDeliveryTraceRequest $request){
		
		$this->send_getDeliveryTrace( $request);
		return $this->recv_getDeliveryTrace();
	}
	
	public function send_getDeliveryTrace(\com\vip\vop\vcloud\jit\GetDeliveryTraceRequest $request){
		
		$this->initInvocation("getDeliveryTrace");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_getDeliveryTrace_args();
		
		$args->request = $request;
		
		$this->send_base($args);
	}
	
	public function recv_getDeliveryTrace(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_getDeliveryTrace_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function healthCheck(){
		
		$this->send_healthCheck();
		return $this->recv_healthCheck();
	}
	
	public function send_healthCheck(){
		
		$this->initInvocation("healthCheck");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_healthCheck_args();
		
		$this->send_base($args);
	}
	
	public function recv_healthCheck(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_healthCheck_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function importDeliveryDetail(\com\vip\vop\vcloud\jit\ImportDeliveryDetailRequest $request){
		
		$this->send_importDeliveryDetail( $request);
		return $this->recv_importDeliveryDetail();
	}
	
	public function send_importDeliveryDetail(\com\vip\vop\vcloud\jit\ImportDeliveryDetailRequest $request){
		
		$this->initInvocation("importDeliveryDetail");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_importDeliveryDetail_args();
		
		$args->request = $request;
		
		$this->send_base($args);
	}
	
	public function recv_importDeliveryDetail(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_importDeliveryDetail_result();
		$this->receive_base($result);
		
	}
	
	
	public function submitDelivery( $vendorId, $storageNo){
		
		$this->send_submitDelivery( $vendorId, $storageNo);
		return $this->recv_submitDelivery();
	}
	
	public function send_submitDelivery( $vendorId, $storageNo){
		
		$this->initInvocation("submitDelivery");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_submitDelivery_args();
		
		$args->vendorId = $vendorId;
		
		$args->storageNo = $storageNo;
		
		$this->send_base($args);
	}
	
	public function recv_submitDelivery(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_submitDelivery_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function syncDeliveryInfo(){
		
		$this->send_syncDeliveryInfo();
		return $this->recv_syncDeliveryInfo();
	}
	
	public function send_syncDeliveryInfo(){
		
		$this->initInvocation("syncDeliveryInfo");
		$args = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_syncDeliveryInfo_args();
		
		$this->send_base($args);
	}
	
	public function recv_syncDeliveryInfo(){
		
		$result = new \com\vip\vop\vcloud\jit\JitDeliveryInfoService_syncDeliveryInfo_result();
		$this->receive_base($result);
		
	}
	
	
}




class JitDeliveryInfoService_addbox_args {
	
	static $_TSPEC;
	public $storageNo = null;
	public $vendorId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'storageNo'
			),
			2 => array(
			'var' => 'vendorId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['storageNo'])){
				
				$this->storageNo = $vals['storageNo'];
			}
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->storageNo);
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->vendorId); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->storageNo !== null) {
			
			$xfer += $output->writeFieldBegin('storageNo');
			$xfer += $output->writeString($this->storageNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI32($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_boxItem_args {
	
	static $_TSPEC;
	public $vendorId = null;
	public $poNo = null;
	public $storageNo = null;
	public $vendorType = null;
	public $barcode = null;
	public $boxNo = null;
	public $amount = null;
	public $productName = null;
	public $subPickNo = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'poNo'
			),
			3 => array(
			'var' => 'storageNo'
			),
			4 => array(
			'var' => 'vendorType'
			),
			5 => array(
			'var' => 'barcode'
			),
			6 => array(
			'var' => 'boxNo'
			),
			7 => array(
			'var' => 'amount'
			),
			8 => array(
			'var' => 'productName'
			),
			9 => array(
			'var' => 'subPickNo'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['poNo'])){
				
				$this->poNo = $vals['poNo'];
			}
			
			
			if (isset($vals['storageNo'])){
				
				$this->storageNo = $vals['storageNo'];
			}
			
			
			if (isset($vals['vendorType'])){
				
				$this->vendorType = $vals['vendorType'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
			if (isset($vals['boxNo'])){
				
				$this->boxNo = $vals['boxNo'];
			}
			
			
			if (isset($vals['amount'])){
				
				$this->amount = $vals['amount'];
			}
			
			
			if (isset($vals['productName'])){
				
				$this->productName = $vals['productName'];
			}
			
			
			if (isset($vals['subPickNo'])){
				
				$this->subPickNo = $vals['subPickNo'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI32($this->vendorId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->poNo);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->storageNo);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->vendorType);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->barcode);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->boxNo);
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->amount); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->productName);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->subPickNo);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI32($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->poNo !== null) {
			
			$xfer += $output->writeFieldBegin('poNo');
			$xfer += $output->writeString($this->poNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->storageNo !== null) {
			
			$xfer += $output->writeFieldBegin('storageNo');
			$xfer += $output->writeString($this->storageNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->vendorType !== null) {
			
			$xfer += $output->writeFieldBegin('vendorType');
			$xfer += $output->writeString($this->vendorType);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcode !== null) {
			
			$xfer += $output->writeFieldBegin('barcode');
			$xfer += $output->writeString($this->barcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->boxNo !== null) {
			
			$xfer += $output->writeFieldBegin('boxNo');
			$xfer += $output->writeString($this->boxNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->amount !== null) {
			
			$xfer += $output->writeFieldBegin('amount');
			$xfer += $output->writeI32($this->amount);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->productName !== null) {
			
			$xfer += $output->writeFieldBegin('productName');
			$xfer += $output->writeString($this->productName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->subPickNo !== null) {
			
			$xfer += $output->writeFieldBegin('subPickNo');
			$xfer += $output->writeString($this->subPickNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_confirmDelivery_args {
	
	static $_TSPEC;
	public $request = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'request'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['request'])){
				
				$this->request = $vals['request'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->request = new \com\vip\vop\vcloud\jit\ConfirmDeliveryRequest();
			$this->request->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('request');
		
		if (!is_object($this->request)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->request->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_createDelivery_args {
	
	static $_TSPEC;
	public $request = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'request'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['request'])){
				
				$this->request = $vals['request'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->request = new \com\vip\vop\vcloud\jit\CreateDeliveryRequest();
			$this->request->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('request');
		
		if (!is_object($this->request)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->request->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_deleteBoxedItem_args {
	
	static $_TSPEC;
	public $boxNo = null;
	public $barcode = null;
	public $storageNo = null;
	public $vendorId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'boxNo'
			),
			2 => array(
			'var' => 'barcode'
			),
			3 => array(
			'var' => 'storageNo'
			),
			4 => array(
			'var' => 'vendorId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['boxNo'])){
				
				$this->boxNo = $vals['boxNo'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
			if (isset($vals['storageNo'])){
				
				$this->storageNo = $vals['storageNo'];
			}
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->boxNo);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->barcode);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->storageNo);
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->vendorId); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->boxNo !== null) {
			
			$xfer += $output->writeFieldBegin('boxNo');
			$xfer += $output->writeString($this->boxNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcode !== null) {
			
			$xfer += $output->writeFieldBegin('barcode');
			$xfer += $output->writeString($this->barcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->storageNo !== null) {
			
			$xfer += $output->writeFieldBegin('storageNo');
			$xfer += $output->writeString($this->storageNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI32($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_deleteDeliveryDetail_args {
	
	static $_TSPEC;
	public $request = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'request'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['request'])){
				
				$this->request = $vals['request'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->request = new \com\vip\vop\vcloud\jit\DeleteDeliveryDetailRequest();
			$this->request->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('request');
		
		if (!is_object($this->request)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->request->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_editDelivery_args {
	
	static $_TSPEC;
	public $request = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'request'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['request'])){
				
				$this->request = $vals['request'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->request = new \com\vip\vop\vcloud\jit\EditDeliveryRequest();
			$this->request->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('request');
		
		if (!is_object($this->request)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->request->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_getBox_args {
	
	static $_TSPEC;
	public $storageNo = null;
	public $vendorId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'storageNo'
			),
			2 => array(
			'var' => 'vendorId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['storageNo'])){
				
				$this->storageNo = $vals['storageNo'];
			}
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->storageNo);
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->vendorId); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->storageNo !== null) {
			
			$xfer += $output->writeFieldBegin('storageNo');
			$xfer += $output->writeString($this->storageNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI32($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_getBoxItem_args {
	
	static $_TSPEC;
	public $deliveryId = null;
	public $boxNo = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'deliveryId'
			),
			2 => array(
			'var' => 'boxNo'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['deliveryId'])){
				
				$this->deliveryId = $vals['deliveryId'];
			}
			
			
			if (isset($vals['boxNo'])){
				
				$this->boxNo = $vals['boxNo'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->deliveryId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->boxNo);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->deliveryId !== null) {
			
			$xfer += $output->writeFieldBegin('deliveryId');
			$xfer += $output->writeI64($this->deliveryId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->boxNo !== null) {
			
			$xfer += $output->writeFieldBegin('boxNo');
			$xfer += $output->writeString($this->boxNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_getDeliveryDetail_args {
	
	static $_TSPEC;
	public $request = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'request'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['request'])){
				
				$this->request = $vals['request'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->request = new \com\vip\vop\vcloud\jit\GetDeliveryDetailRequest();
			$this->request->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('request');
		
		if (!is_object($this->request)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->request->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_getDeliveryGoods_args {
	
	static $_TSPEC;
	public $request = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'request'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['request'])){
				
				$this->request = $vals['request'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->request = new \com\vip\vop\vcloud\jit\GetDeliveryGoodsRequest();
			$this->request->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('request');
		
		if (!is_object($this->request)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->request->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_getDeliveryList_args {
	
	static $_TSPEC;
	public $request = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'request'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['request'])){
				
				$this->request = $vals['request'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->request = new \com\vip\vop\vcloud\jit\GetDeliveryListRequest();
			$this->request->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('request');
		
		if (!is_object($this->request)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->request->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_getDeliverySubPick_args {
	
	static $_TSPEC;
	public $storageNo = null;
	public $vendorId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'storageNo'
			),
			2 => array(
			'var' => 'vendorId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['storageNo'])){
				
				$this->storageNo = $vals['storageNo'];
			}
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->storageNo);
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->vendorId); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->storageNo !== null) {
			
			$xfer += $output->writeFieldBegin('storageNo');
			$xfer += $output->writeString($this->storageNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI32($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_getDeliveryTrace_args {
	
	static $_TSPEC;
	public $request = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'request'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['request'])){
				
				$this->request = $vals['request'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->request = new \com\vip\vop\vcloud\jit\GetDeliveryTraceRequest();
			$this->request->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('request');
		
		if (!is_object($this->request)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->request->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_healthCheck_args {
	
	static $_TSPEC;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			
			);
			
		}
		
		if (is_array($vals)){
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_importDeliveryDetail_args {
	
	static $_TSPEC;
	public $request = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'request'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['request'])){
				
				$this->request = $vals['request'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->request = new \com\vip\vop\vcloud\jit\ImportDeliveryDetailRequest();
			$this->request->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('request');
		
		if (!is_object($this->request)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->request->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_submitDelivery_args {
	
	static $_TSPEC;
	public $vendorId = null;
	public $storageNo = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'storageNo'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['storageNo'])){
				
				$this->storageNo = $vals['storageNo'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI32($this->vendorId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->storageNo);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI32($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->storageNo !== null) {
			
			$xfer += $output->writeFieldBegin('storageNo');
			$xfer += $output->writeString($this->storageNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_syncDeliveryInfo_args {
	
	static $_TSPEC;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			
			);
			
		}
		
		if (is_array($vals)){
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_addbox_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->success);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			$xfer += $output->writeString($this->success);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_boxItem_result {
	
	static $_TSPEC;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_confirmDelivery_result {
	
	static $_TSPEC;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_createDelivery_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = new \com\vip\vop\vcloud\jit\CreateDeliveryResponse();
			$this->success->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_object($this->success)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->success->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_deleteBoxedItem_result {
	
	static $_TSPEC;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_deleteDeliveryDetail_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					
					$elem0 = new \com\vip\vop\vcloud\jit\DeletedDeliveryDetail();
					$elem0->read($input);
					
					$this->success[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_editDelivery_result {
	
	static $_TSPEC;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_getBox_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					
					$elem1 = new \com\vip\vop\vcloud\jit\DeliveryBox();
					$elem1->read($input);
					
					$this->success[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_getBoxItem_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					
					$elem1 = new \com\vip\vop\vcloud\jit\DeliveryDetail();
					$elem1->read($input);
					
					$this->success[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_getDeliveryDetail_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = new \com\vip\vop\vcloud\jit\GetDeliveryDetailResponse();
			$this->success->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_object($this->success)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->success->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_getDeliveryGoods_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = new \com\vip\vop\vcloud\jit\GetDeliveryGoodsResponse();
			$this->success->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_object($this->success)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->success->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_getDeliveryList_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = new \com\vip\vop\vcloud\jit\GetDeliveryListResponse();
			$this->success->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_object($this->success)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->success->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_getDeliverySubPick_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					
					$elem0 = new \com\vip\vop\vcloud\jit\DeliverySubPick();
					$elem0->read($input);
					
					$this->success[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_getDeliveryTrace_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					
					$elem1 = new \com\vip\vop\vcloud\jit\DeliveryTrace();
					$elem1->read($input);
					
					$this->success[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_healthCheck_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = new \com\vip\hermes\core\health\CheckResult();
			$this->success->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_object($this->success)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->success->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_importDeliveryDetail_result {
	
	static $_TSPEC;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_submitDelivery_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = new \com\vip\vop\vcloud\jit\SubmitDeliveryResponse();
			$this->success->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_object($this->success)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->success->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class JitDeliveryInfoService_syncDeliveryInfo_result {
	
	static $_TSPEC;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




?>