<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class GlobalProductWithSkuReturn {
	
	static $_TSPEC;
	public $globalProductId = null;
	public $globalSkuId = null;
	public $sn = null;
	public $originalGoodsNum = null;
	public $barcode = null;
	public $oldBarcode = null;
	public $vendorId = null;
	public $brandId = null;
	public $groupSn = null;
	public $errorMessage = null;
	public $residualGrade = null;
	public $groupSnExists = null;
	public $colorName = null;
	public $colorNameExists = null;
	public $extraOperation = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'globalProductId'
			),
			2 => array(
			'var' => 'globalSkuId'
			),
			3 => array(
			'var' => 'sn'
			),
			4 => array(
			'var' => 'originalGoodsNum'
			),
			5 => array(
			'var' => 'barcode'
			),
			6 => array(
			'var' => 'oldBarcode'
			),
			7 => array(
			'var' => 'vendorId'
			),
			8 => array(
			'var' => 'brandId'
			),
			9 => array(
			'var' => 'groupSn'
			),
			10 => array(
			'var' => 'errorMessage'
			),
			11 => array(
			'var' => 'residualGrade'
			),
			12 => array(
			'var' => 'groupSnExists'
			),
			13 => array(
			'var' => 'colorName'
			),
			14 => array(
			'var' => 'colorNameExists'
			),
			15 => array(
			'var' => 'extraOperation'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['globalProductId'])){
				
				$this->globalProductId = $vals['globalProductId'];
			}
			
			
			if (isset($vals['globalSkuId'])){
				
				$this->globalSkuId = $vals['globalSkuId'];
			}
			
			
			if (isset($vals['sn'])){
				
				$this->sn = $vals['sn'];
			}
			
			
			if (isset($vals['originalGoodsNum'])){
				
				$this->originalGoodsNum = $vals['originalGoodsNum'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
			if (isset($vals['oldBarcode'])){
				
				$this->oldBarcode = $vals['oldBarcode'];
			}
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['brandId'])){
				
				$this->brandId = $vals['brandId'];
			}
			
			
			if (isset($vals['groupSn'])){
				
				$this->groupSn = $vals['groupSn'];
			}
			
			
			if (isset($vals['errorMessage'])){
				
				$this->errorMessage = $vals['errorMessage'];
			}
			
			
			if (isset($vals['residualGrade'])){
				
				$this->residualGrade = $vals['residualGrade'];
			}
			
			
			if (isset($vals['groupSnExists'])){
				
				$this->groupSnExists = $vals['groupSnExists'];
			}
			
			
			if (isset($vals['colorName'])){
				
				$this->colorName = $vals['colorName'];
			}
			
			
			if (isset($vals['colorNameExists'])){
				
				$this->colorNameExists = $vals['colorNameExists'];
			}
			
			
			if (isset($vals['extraOperation'])){
				
				$this->extraOperation = $vals['extraOperation'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GlobalProductWithSkuReturn';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("globalProductId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->globalProductId); 
				
			}
			
			
			
			
			if ("globalSkuId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->globalSkuId); 
				
			}
			
			
			
			
			if ("sn" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->sn);
				
			}
			
			
			
			
			if ("originalGoodsNum" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->originalGoodsNum);
				
			}
			
			
			
			
			if ("barcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->barcode);
				
			}
			
			
			
			
			if ("oldBarcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->oldBarcode);
				
			}
			
			
			
			
			if ("vendorId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->vendorId); 
				
			}
			
			
			
			
			if ("brandId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->brandId); 
				
			}
			
			
			
			
			if ("groupSn" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->groupSn);
				
			}
			
			
			
			
			if ("errorMessage" == $schemeField){
				
				$needSkip = false;
				
				$this->errorMessage = new \com\vip\product\gpdc\service\ErrorMessage();
				$this->errorMessage->read($input);
				
			}
			
			
			
			
			if ("residualGrade" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->residualGrade);
				
			}
			
			
			
			
			if ("groupSnExists" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->groupSnExists);
				
			}
			
			
			
			
			if ("colorName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->colorName);
				
			}
			
			
			
			
			if ("colorNameExists" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->colorNameExists);
				
			}
			
			
			
			
			if ("extraOperation" == $schemeField){
				
				$needSkip = false;
				
				$names = \com\vip\product\gpdc\service\ExtraOperation::$__names;
				$name = null;
				$input->readString($name);
				foreach ($names as $k => $v){
					
					if($name == $v){
						
						$this->extraOperation = $k;
						break;
					}
					
				}
				
				
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
		
		if($this->globalProductId !== null) {
			
			$xfer += $output->writeFieldBegin('globalProductId');
			$xfer += $output->writeI64($this->globalProductId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->globalSkuId !== null) {
			
			$xfer += $output->writeFieldBegin('globalSkuId');
			$xfer += $output->writeI64($this->globalSkuId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->sn !== null) {
			
			$xfer += $output->writeFieldBegin('sn');
			$xfer += $output->writeString($this->sn);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->originalGoodsNum !== null) {
			
			$xfer += $output->writeFieldBegin('originalGoodsNum');
			$xfer += $output->writeString($this->originalGoodsNum);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcode !== null) {
			
			$xfer += $output->writeFieldBegin('barcode');
			$xfer += $output->writeString($this->barcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->oldBarcode !== null) {
			
			$xfer += $output->writeFieldBegin('oldBarcode');
			$xfer += $output->writeString($this->oldBarcode);
			
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
		
		
		if($this->groupSn !== null) {
			
			$xfer += $output->writeFieldBegin('groupSn');
			$xfer += $output->writeString($this->groupSn);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->errorMessage !== null) {
			
			$xfer += $output->writeFieldBegin('errorMessage');
			
			if (!is_object($this->errorMessage)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->errorMessage->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->residualGrade !== null) {
			
			$xfer += $output->writeFieldBegin('residualGrade');
			$xfer += $output->writeString($this->residualGrade);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->groupSnExists !== null) {
			
			$xfer += $output->writeFieldBegin('groupSnExists');
			$xfer += $output->writeString($this->groupSnExists);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->colorName !== null) {
			
			$xfer += $output->writeFieldBegin('colorName');
			$xfer += $output->writeString($this->colorName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->colorNameExists !== null) {
			
			$xfer += $output->writeFieldBegin('colorNameExists');
			$xfer += $output->writeString($this->colorNameExists);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->extraOperation !== null) {
			
			$xfer += $output->writeFieldBegin('extraOperation');
			
			$em = new \com\vip\product\gpdc\service\ExtraOperation; 
			$output->writeString($em::$__names[$this->extraOperation]);  
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>