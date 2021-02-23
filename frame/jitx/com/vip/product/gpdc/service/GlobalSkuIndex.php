<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class GlobalSkuIndex {
	
	static $_TSPEC;
	public $globalSkuId = null;
	public $globalProductId = null;
	public $owner = null;
	public $barcode = null;
	public $vendorId = null;
	public $brandId = null;
	public $categoryId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'globalSkuId'
			),
			2 => array(
			'var' => 'globalProductId'
			),
			3 => array(
			'var' => 'owner'
			),
			4 => array(
			'var' => 'barcode'
			),
			5 => array(
			'var' => 'vendorId'
			),
			6 => array(
			'var' => 'brandId'
			),
			7 => array(
			'var' => 'categoryId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['globalSkuId'])){
				
				$this->globalSkuId = $vals['globalSkuId'];
			}
			
			
			if (isset($vals['globalProductId'])){
				
				$this->globalProductId = $vals['globalProductId'];
			}
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['brandId'])){
				
				$this->brandId = $vals['brandId'];
			}
			
			
			if (isset($vals['categoryId'])){
				
				$this->categoryId = $vals['categoryId'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GlobalSkuIndex';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("globalSkuId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->globalSkuId); 
				
			}
			
			
			
			
			if ("globalProductId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->globalProductId); 
				
			}
			
			
			
			
			if ("owner" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->owner);
				
			}
			
			
			
			
			if ("barcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->barcode);
				
			}
			
			
			
			
			if ("vendorId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->vendorId); 
				
			}
			
			
			
			
			if ("brandId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->brandId); 
				
			}
			
			
			
			
			if ("categoryId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->categoryId); 
				
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
		
		if($this->globalSkuId !== null) {
			
			$xfer += $output->writeFieldBegin('globalSkuId');
			$xfer += $output->writeI64($this->globalSkuId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->globalProductId !== null) {
			
			$xfer += $output->writeFieldBegin('globalProductId');
			$xfer += $output->writeI64($this->globalProductId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcode !== null) {
			
			$xfer += $output->writeFieldBegin('barcode');
			$xfer += $output->writeString($this->barcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->vendorId !== null) {
			
			$xfer += $output->writeFieldBegin('vendorId');
			$xfer += $output->writeI32($this->vendorId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandId !== null) {
			
			$xfer += $output->writeFieldBegin('brandId');
			$xfer += $output->writeI32($this->brandId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->categoryId !== null) {
			
			$xfer += $output->writeFieldBegin('categoryId');
			$xfer += $output->writeI32($this->categoryId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>