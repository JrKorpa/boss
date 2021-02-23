<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\jit;

class DeliveryDetail {
	
	static $_TSPEC;
	public $vendorType = null;
	public $barcode = null;
	public $boxNo = null;
	public $pickNo = null;
	public $amount = null;
	public $poNo = null;
	public $productName = null;
	public $subPickNo = null;
	public $imageUrl = null;
	public $color = null;
	public $colorName = null;
	public $sn = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorType'
			),
			2 => array(
			'var' => 'barcode'
			),
			3 => array(
			'var' => 'boxNo'
			),
			4 => array(
			'var' => 'pickNo'
			),
			5 => array(
			'var' => 'amount'
			),
			6 => array(
			'var' => 'poNo'
			),
			7 => array(
			'var' => 'productName'
			),
			8 => array(
			'var' => 'subPickNo'
			),
			9 => array(
			'var' => 'imageUrl'
			),
			10 => array(
			'var' => 'color'
			),
			11 => array(
			'var' => 'colorName'
			),
			12 => array(
			'var' => 'sn'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorType'])){
				
				$this->vendorType = $vals['vendorType'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
			if (isset($vals['boxNo'])){
				
				$this->boxNo = $vals['boxNo'];
			}
			
			
			if (isset($vals['pickNo'])){
				
				$this->pickNo = $vals['pickNo'];
			}
			
			
			if (isset($vals['amount'])){
				
				$this->amount = $vals['amount'];
			}
			
			
			if (isset($vals['poNo'])){
				
				$this->poNo = $vals['poNo'];
			}
			
			
			if (isset($vals['productName'])){
				
				$this->productName = $vals['productName'];
			}
			
			
			if (isset($vals['subPickNo'])){
				
				$this->subPickNo = $vals['subPickNo'];
			}
			
			
			if (isset($vals['imageUrl'])){
				
				$this->imageUrl = $vals['imageUrl'];
			}
			
			
			if (isset($vals['color'])){
				
				$this->color = $vals['color'];
			}
			
			
			if (isset($vals['colorName'])){
				
				$this->colorName = $vals['colorName'];
			}
			
			
			if (isset($vals['sn'])){
				
				$this->sn = $vals['sn'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'DeliveryDetail';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("vendorType" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->vendorType);
				
			}
			
			
			
			
			if ("barcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->barcode);
				
			}
			
			
			
			
			if ("boxNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->boxNo);
				
			}
			
			
			
			
			if ("pickNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->pickNo);
				
			}
			
			
			
			
			if ("amount" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->amount); 
				
			}
			
			
			
			
			if ("poNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->poNo);
				
			}
			
			
			
			
			if ("productName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->productName);
				
			}
			
			
			
			
			if ("subPickNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->subPickNo);
				
			}
			
			
			
			
			if ("imageUrl" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->imageUrl);
				
			}
			
			
			
			
			if ("color" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->color);
				
			}
			
			
			
			
			if ("colorName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->colorName);
				
			}
			
			
			
			
			if ("sn" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->sn);
				
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
		
		
		if($this->pickNo !== null) {
			
			$xfer += $output->writeFieldBegin('pickNo');
			$xfer += $output->writeString($this->pickNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->amount !== null) {
			
			$xfer += $output->writeFieldBegin('amount');
			$xfer += $output->writeI32($this->amount);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->poNo !== null) {
			
			$xfer += $output->writeFieldBegin('poNo');
			$xfer += $output->writeString($this->poNo);
			
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
		
		
		if($this->imageUrl !== null) {
			
			$xfer += $output->writeFieldBegin('imageUrl');
			$xfer += $output->writeString($this->imageUrl);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->color !== null) {
			
			$xfer += $output->writeFieldBegin('color');
			$xfer += $output->writeString($this->color);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->colorName !== null) {
			
			$xfer += $output->writeFieldBegin('colorName');
			$xfer += $output->writeString($this->colorName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->sn !== null) {
			
			$xfer += $output->writeFieldBegin('sn');
			$xfer += $output->writeString($this->sn);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>