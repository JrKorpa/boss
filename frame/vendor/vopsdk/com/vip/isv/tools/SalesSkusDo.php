<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\isv\tools;

class SalesSkusDo {
	
	static $_TSPEC;
	public $vendorId = null;
	public $salesNo = null;
	public $cooperationNo = null;
	public $barcode = null;
	public $warehouse = null;
	public $merItemNo = null;
	public $isDeleted = null;
	public $skuState = null;
	public $createTime = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'salesNo'
			),
			3 => array(
			'var' => 'cooperationNo'
			),
			4 => array(
			'var' => 'barcode'
			),
			5 => array(
			'var' => 'warehouse'
			),
			6 => array(
			'var' => 'merItemNo'
			),
			7 => array(
			'var' => 'isDeleted'
			),
			8 => array(
			'var' => 'skuState'
			),
			9 => array(
			'var' => 'createTime'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['salesNo'])){
				
				$this->salesNo = $vals['salesNo'];
			}
			
			
			if (isset($vals['cooperationNo'])){
				
				$this->cooperationNo = $vals['cooperationNo'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
			if (isset($vals['warehouse'])){
				
				$this->warehouse = $vals['warehouse'];
			}
			
			
			if (isset($vals['merItemNo'])){
				
				$this->merItemNo = $vals['merItemNo'];
			}
			
			
			if (isset($vals['isDeleted'])){
				
				$this->isDeleted = $vals['isDeleted'];
			}
			
			
			if (isset($vals['skuState'])){
				
				$this->skuState = $vals['skuState'];
			}
			
			
			if (isset($vals['createTime'])){
				
				$this->createTime = $vals['createTime'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'SalesSkusDo';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("vendorId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->vendorId); 
				
			}
			
			
			
			
			if ("salesNo" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->salesNo); 
				
			}
			
			
			
			
			if ("cooperationNo" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->cooperationNo); 
				
			}
			
			
			
			
			if ("barcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->barcode);
				
			}
			
			
			
			
			if ("warehouse" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->warehouse);
				
			}
			
			
			
			
			if ("merItemNo" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->merItemNo); 
				
			}
			
			
			
			
			if ("isDeleted" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->isDeleted); 
				
			}
			
			
			
			
			if ("skuState" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->skuState); 
				
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
			$xfer += $output->writeI64($this->vendorId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->salesNo !== null) {
			
			$xfer += $output->writeFieldBegin('salesNo');
			$xfer += $output->writeI64($this->salesNo);
			
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
		
		
		if($this->warehouse !== null) {
			
			$xfer += $output->writeFieldBegin('warehouse');
			$xfer += $output->writeString($this->warehouse);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->merItemNo !== null) {
			
			$xfer += $output->writeFieldBegin('merItemNo');
			$xfer += $output->writeI64($this->merItemNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isDeleted !== null) {
			
			$xfer += $output->writeFieldBegin('isDeleted');
			$xfer += $output->writeI32($this->isDeleted);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->skuState !== null) {
			
			$xfer += $output->writeFieldBegin('skuState');
			$xfer += $output->writeI32($this->skuState);
			
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