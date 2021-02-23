<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\isv\tools;

class UpdateStockToWhiResponse {
	
	static $_TSPEC;
	public $vendorId = null;
	public $cooperationNo = null;
	public $barcode = null;
	public $quantity = null;
	public $updateTime = null;
	public $isSuccess = null;
	public $message = null;
	public $warehouse = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'cooperationNo'
			),
			3 => array(
			'var' => 'barcode'
			),
			4 => array(
			'var' => 'quantity'
			),
			5 => array(
			'var' => 'updateTime'
			),
			6 => array(
			'var' => 'isSuccess'
			),
			7 => array(
			'var' => 'message'
			),
			8 => array(
			'var' => 'warehouse'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['cooperationNo'])){
				
				$this->cooperationNo = $vals['cooperationNo'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
			if (isset($vals['quantity'])){
				
				$this->quantity = $vals['quantity'];
			}
			
			
			if (isset($vals['updateTime'])){
				
				$this->updateTime = $vals['updateTime'];
			}
			
			
			if (isset($vals['isSuccess'])){
				
				$this->isSuccess = $vals['isSuccess'];
			}
			
			
			if (isset($vals['message'])){
				
				$this->message = $vals['message'];
			}
			
			
			if (isset($vals['warehouse'])){
				
				$this->warehouse = $vals['warehouse'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'UpdateStockToWhiResponse';
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
			
			
			
			
			if ("cooperationNo" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->cooperationNo); 
				
			}
			
			
			
			
			if ("barcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->barcode);
				
			}
			
			
			
			
			if ("quantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->quantity); 
				
			}
			
			
			
			
			if ("updateTime" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->updateTime);
				
			}
			
			
			
			
			if ("isSuccess" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->isSuccess); 
				
			}
			
			
			
			
			if ("message" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->message);
				
			}
			
			
			
			
			if ("warehouse" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->warehouse);
				
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
		
		
		if($this->cooperationNo !== null) {
			
			$xfer += $output->writeFieldBegin('cooperationNo');
			$xfer += $output->writeI32($this->cooperationNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcode !== null) {
			
			$xfer += $output->writeFieldBegin('barcode');
			$xfer += $output->writeString($this->barcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->quantity !== null) {
			
			$xfer += $output->writeFieldBegin('quantity');
			$xfer += $output->writeI32($this->quantity);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->updateTime !== null) {
			
			$xfer += $output->writeFieldBegin('updateTime');
			$xfer += $output->writeString($this->updateTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isSuccess !== null) {
			
			$xfer += $output->writeFieldBegin('isSuccess');
			$xfer += $output->writeI32($this->isSuccess);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->message !== null) {
			
			$xfer += $output->writeFieldBegin('message');
			$xfer += $output->writeString($this->message);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->warehouse !== null) {
			
			$xfer += $output->writeFieldBegin('warehouse');
			$xfer += $output->writeString($this->warehouse);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>