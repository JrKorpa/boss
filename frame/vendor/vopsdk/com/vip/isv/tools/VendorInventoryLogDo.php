<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\isv\tools;

class VendorInventoryLogDo {
	
	static $_TSPEC;
	public $vendorId = null;
	public $batchNo = null;
	public $barcode = null;
	public $warehouse = null;
	public $quantity = null;
	public $cartQuantity = null;
	public $sellableQuantity = null;
	public $status = null;
	public $message = null;
	public $createTime = null;
	public $updateTime = null;
	public $isIncremental = null;
	public $realQuantity = null;
	public $cooperationNo = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'batchNo'
			),
			3 => array(
			'var' => 'barcode'
			),
			4 => array(
			'var' => 'warehouse'
			),
			5 => array(
			'var' => 'quantity'
			),
			6 => array(
			'var' => 'cartQuantity'
			),
			7 => array(
			'var' => 'sellableQuantity'
			),
			8 => array(
			'var' => 'status'
			),
			9 => array(
			'var' => 'message'
			),
			10 => array(
			'var' => 'createTime'
			),
			11 => array(
			'var' => 'updateTime'
			),
			12 => array(
			'var' => 'isIncremental'
			),
			13 => array(
			'var' => 'realQuantity'
			),
			14 => array(
			'var' => 'cooperationNo'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['batchNo'])){
				
				$this->batchNo = $vals['batchNo'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
			if (isset($vals['warehouse'])){
				
				$this->warehouse = $vals['warehouse'];
			}
			
			
			if (isset($vals['quantity'])){
				
				$this->quantity = $vals['quantity'];
			}
			
			
			if (isset($vals['cartQuantity'])){
				
				$this->cartQuantity = $vals['cartQuantity'];
			}
			
			
			if (isset($vals['sellableQuantity'])){
				
				$this->sellableQuantity = $vals['sellableQuantity'];
			}
			
			
			if (isset($vals['status'])){
				
				$this->status = $vals['status'];
			}
			
			
			if (isset($vals['message'])){
				
				$this->message = $vals['message'];
			}
			
			
			if (isset($vals['createTime'])){
				
				$this->createTime = $vals['createTime'];
			}
			
			
			if (isset($vals['updateTime'])){
				
				$this->updateTime = $vals['updateTime'];
			}
			
			
			if (isset($vals['isIncremental'])){
				
				$this->isIncremental = $vals['isIncremental'];
			}
			
			
			if (isset($vals['realQuantity'])){
				
				$this->realQuantity = $vals['realQuantity'];
			}
			
			
			if (isset($vals['cooperationNo'])){
				
				$this->cooperationNo = $vals['cooperationNo'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'VendorInventoryLogDo';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("vendorId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->vendorId); 
				
			}
			
			
			
			
			if ("batchNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->batchNo);
				
			}
			
			
			
			
			if ("barcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->barcode);
				
			}
			
			
			
			
			if ("warehouse" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->warehouse);
				
			}
			
			
			
			
			if ("quantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->quantity); 
				
			}
			
			
			
			
			if ("cartQuantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->cartQuantity); 
				
			}
			
			
			
			
			if ("sellableQuantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->sellableQuantity); 
				
			}
			
			
			
			
			if ("status" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->status); 
				
			}
			
			
			
			
			if ("message" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->message);
				
			}
			
			
			
			
			if ("createTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->createTime);
				
			}
			
			
			
			
			if ("updateTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->updateTime);
				
			}
			
			
			
			
			if ("isIncremental" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->isIncremental); 
				
			}
			
			
			
			
			if ("realQuantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->realQuantity); 
				
			}
			
			
			
			
			if ("cooperationNo" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->cooperationNo); 
				
			}
			
			
			
			if($needSkip){
				
				\Osp\Protocol\ProtocolUtil::skip($input);
			}
			
			$input->readFieldEnd();
		}
		
		$input->readStructEnd();
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->vendorId !== null) {
			
			$xfer += $output->writeFieldBegin('vendorId');
			$xfer += $output->writeI32($this->vendorId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->batchNo !== null) {
			
			$xfer += $output->writeFieldBegin('batchNo');
			$xfer += $output->writeString($this->batchNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcode !== null) {
			
			$xfer += $output->writeFieldBegin('barcode');
			$xfer += $output->writeString($this->barcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->warehouse !== null) {
			
			$xfer += $output->writeFieldBegin('warehouse');
			$xfer += $output->writeString($this->warehouse);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->quantity !== null) {
			
			$xfer += $output->writeFieldBegin('quantity');
			$xfer += $output->writeI32($this->quantity);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->cartQuantity !== null) {
			
			$xfer += $output->writeFieldBegin('cartQuantity');
			$xfer += $output->writeI32($this->cartQuantity);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->sellableQuantity !== null) {
			
			$xfer += $output->writeFieldBegin('sellableQuantity');
			$xfer += $output->writeI32($this->sellableQuantity);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->status !== null) {
			
			$xfer += $output->writeFieldBegin('status');
			$xfer += $output->writeI32($this->status);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->message !== null) {
			
			$xfer += $output->writeFieldBegin('message');
			$xfer += $output->writeString($this->message);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->createTime !== null) {
			
			$xfer += $output->writeFieldBegin('createTime');
			$xfer += $output->writeI64($this->createTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->updateTime !== null) {
			
			$xfer += $output->writeFieldBegin('updateTime');
			$xfer += $output->writeI64($this->updateTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isIncremental !== null) {
			
			$xfer += $output->writeFieldBegin('isIncremental');
			$xfer += $output->writeI32($this->isIncremental);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->realQuantity !== null) {
			
			$xfer += $output->writeFieldBegin('realQuantity');
			$xfer += $output->writeI32($this->realQuantity);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->cooperationNo !== null) {
			
			$xfer += $output->writeFieldBegin('cooperationNo');
			$xfer += $output->writeI32($this->cooperationNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>