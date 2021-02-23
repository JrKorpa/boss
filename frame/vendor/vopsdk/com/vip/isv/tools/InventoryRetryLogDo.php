<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\isv\tools;

class InventoryRetryLogDo {
	
	static $_TSPEC;
	public $vendorId = null;
	public $batchNo = null;
	public $cooperationNo = null;
	public $warehouse = null;
	public $barcode = null;
	public $vendorRetryQuantity = null;
	public $realQuantity = null;
	public $cartHold = null;
	public $sellableQuantity = null;
	public $retryTimes = null;
	public $errorMsg = null;
	public $createTime = null;
	
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
			'var' => 'cooperationNo'
			),
			4 => array(
			'var' => 'warehouse'
			),
			5 => array(
			'var' => 'barcode'
			),
			6 => array(
			'var' => 'vendorRetryQuantity'
			),
			7 => array(
			'var' => 'realQuantity'
			),
			8 => array(
			'var' => 'cartHold'
			),
			9 => array(
			'var' => 'sellableQuantity'
			),
			10 => array(
			'var' => 'retryTimes'
			),
			11 => array(
			'var' => 'errorMsg'
			),
			12 => array(
			'var' => 'createTime'
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
			
			
			if (isset($vals['cooperationNo'])){
				
				$this->cooperationNo = $vals['cooperationNo'];
			}
			
			
			if (isset($vals['warehouse'])){
				
				$this->warehouse = $vals['warehouse'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
			if (isset($vals['vendorRetryQuantity'])){
				
				$this->vendorRetryQuantity = $vals['vendorRetryQuantity'];
			}
			
			
			if (isset($vals['realQuantity'])){
				
				$this->realQuantity = $vals['realQuantity'];
			}
			
			
			if (isset($vals['cartHold'])){
				
				$this->cartHold = $vals['cartHold'];
			}
			
			
			if (isset($vals['sellableQuantity'])){
				
				$this->sellableQuantity = $vals['sellableQuantity'];
			}
			
			
			if (isset($vals['retryTimes'])){
				
				$this->retryTimes = $vals['retryTimes'];
			}
			
			
			if (isset($vals['errorMsg'])){
				
				$this->errorMsg = $vals['errorMsg'];
			}
			
			
			if (isset($vals['createTime'])){
				
				$this->createTime = $vals['createTime'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'InventoryRetryLogDo';
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
			
			
			
			
			if ("cooperationNo" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->cooperationNo); 
				
			}
			
			
			
			
			if ("warehouse" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->warehouse);
				
			}
			
			
			
			
			if ("barcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->barcode);
				
			}
			
			
			
			
			if ("vendorRetryQuantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->vendorRetryQuantity); 
				
			}
			
			
			
			
			if ("realQuantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->realQuantity); 
				
			}
			
			
			
			
			if ("cartHold" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->cartHold); 
				
			}
			
			
			
			
			if ("sellableQuantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->sellableQuantity); 
				
			}
			
			
			
			
			if ("retryTimes" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->retryTimes); 
				
			}
			
			
			
			
			if ("errorMsg" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->errorMsg);
				
			}
			
			
			
			
			if ("createTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->createTime);
				
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
		
		
		if($this->cooperationNo !== null) {
			
			$xfer += $output->writeFieldBegin('cooperationNo');
			$xfer += $output->writeI32($this->cooperationNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->warehouse !== null) {
			
			$xfer += $output->writeFieldBegin('warehouse');
			$xfer += $output->writeString($this->warehouse);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcode !== null) {
			
			$xfer += $output->writeFieldBegin('barcode');
			$xfer += $output->writeString($this->barcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->vendorRetryQuantity !== null) {
			
			$xfer += $output->writeFieldBegin('vendorRetryQuantity');
			$xfer += $output->writeI32($this->vendorRetryQuantity);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->realQuantity !== null) {
			
			$xfer += $output->writeFieldBegin('realQuantity');
			$xfer += $output->writeI32($this->realQuantity);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->cartHold !== null) {
			
			$xfer += $output->writeFieldBegin('cartHold');
			$xfer += $output->writeI32($this->cartHold);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->sellableQuantity !== null) {
			
			$xfer += $output->writeFieldBegin('sellableQuantity');
			$xfer += $output->writeI32($this->sellableQuantity);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->retryTimes !== null) {
			
			$xfer += $output->writeFieldBegin('retryTimes');
			$xfer += $output->writeI32($this->retryTimes);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->errorMsg !== null) {
			
			$xfer += $output->writeFieldBegin('errorMsg');
			$xfer += $output->writeString($this->errorMsg);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->createTime !== null) {
			
			$xfer += $output->writeFieldBegin('createTime');
			$xfer += $output->writeI64($this->createTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>