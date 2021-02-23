<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class GlobalSkuForEBS {
	
	static $_TSPEC;
	public $barcode = null;
	public $vendorId = null;
	public $sn = null;
	public $skuId = null;
	public $name = null;
	public $brandId = null;
	public $unit = null;
	public $sizeName = null;
	public $corlor = null;
	public $categoryId = null;
	public $isAirForbit = null;
	public $isEasyBreak = null;
	public $isLarge = null;
	public $isValuable = null;
	public $taxRate = null;
	public $length = null;
	public $width = null;
	public $height = null;
	public $weight = null;
	public $temperature = null;
	public $isConsumptionTax = null;
	public $vendorName = null;
	public $internationalTaxRates = null;
	public $isSerialNumManage = null;
	public $oldBarcode = null;
	public $taxCode = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'barcode'
			),
			2 => array(
			'var' => 'vendorId'
			),
			3 => array(
			'var' => 'sn'
			),
			4 => array(
			'var' => 'skuId'
			),
			5 => array(
			'var' => 'name'
			),
			6 => array(
			'var' => 'brandId'
			),
			7 => array(
			'var' => 'unit'
			),
			8 => array(
			'var' => 'sizeName'
			),
			9 => array(
			'var' => 'corlor'
			),
			10 => array(
			'var' => 'categoryId'
			),
			11 => array(
			'var' => 'isAirForbit'
			),
			12 => array(
			'var' => 'isEasyBreak'
			),
			13 => array(
			'var' => 'isLarge'
			),
			14 => array(
			'var' => 'isValuable'
			),
			15 => array(
			'var' => 'taxRate'
			),
			16 => array(
			'var' => 'length'
			),
			17 => array(
			'var' => 'width'
			),
			18 => array(
			'var' => 'height'
			),
			19 => array(
			'var' => 'weight'
			),
			20 => array(
			'var' => 'temperature'
			),
			21 => array(
			'var' => 'isConsumptionTax'
			),
			22 => array(
			'var' => 'vendorName'
			),
			23 => array(
			'var' => 'internationalTaxRates'
			),
			24 => array(
			'var' => 'isSerialNumManage'
			),
			25 => array(
			'var' => 'oldBarcode'
			),
			26 => array(
			'var' => 'taxCode'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['sn'])){
				
				$this->sn = $vals['sn'];
			}
			
			
			if (isset($vals['skuId'])){
				
				$this->skuId = $vals['skuId'];
			}
			
			
			if (isset($vals['name'])){
				
				$this->name = $vals['name'];
			}
			
			
			if (isset($vals['brandId'])){
				
				$this->brandId = $vals['brandId'];
			}
			
			
			if (isset($vals['unit'])){
				
				$this->unit = $vals['unit'];
			}
			
			
			if (isset($vals['sizeName'])){
				
				$this->sizeName = $vals['sizeName'];
			}
			
			
			if (isset($vals['corlor'])){
				
				$this->corlor = $vals['corlor'];
			}
			
			
			if (isset($vals['categoryId'])){
				
				$this->categoryId = $vals['categoryId'];
			}
			
			
			if (isset($vals['isAirForbit'])){
				
				$this->isAirForbit = $vals['isAirForbit'];
			}
			
			
			if (isset($vals['isEasyBreak'])){
				
				$this->isEasyBreak = $vals['isEasyBreak'];
			}
			
			
			if (isset($vals['isLarge'])){
				
				$this->isLarge = $vals['isLarge'];
			}
			
			
			if (isset($vals['isValuable'])){
				
				$this->isValuable = $vals['isValuable'];
			}
			
			
			if (isset($vals['taxRate'])){
				
				$this->taxRate = $vals['taxRate'];
			}
			
			
			if (isset($vals['length'])){
				
				$this->length = $vals['length'];
			}
			
			
			if (isset($vals['width'])){
				
				$this->width = $vals['width'];
			}
			
			
			if (isset($vals['height'])){
				
				$this->height = $vals['height'];
			}
			
			
			if (isset($vals['weight'])){
				
				$this->weight = $vals['weight'];
			}
			
			
			if (isset($vals['temperature'])){
				
				$this->temperature = $vals['temperature'];
			}
			
			
			if (isset($vals['isConsumptionTax'])){
				
				$this->isConsumptionTax = $vals['isConsumptionTax'];
			}
			
			
			if (isset($vals['vendorName'])){
				
				$this->vendorName = $vals['vendorName'];
			}
			
			
			if (isset($vals['internationalTaxRates'])){
				
				$this->internationalTaxRates = $vals['internationalTaxRates'];
			}
			
			
			if (isset($vals['isSerialNumManage'])){
				
				$this->isSerialNumManage = $vals['isSerialNumManage'];
			}
			
			
			if (isset($vals['oldBarcode'])){
				
				$this->oldBarcode = $vals['oldBarcode'];
			}
			
			
			if (isset($vals['taxCode'])){
				
				$this->taxCode = $vals['taxCode'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GlobalSkuForEBS';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("barcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->barcode);
				
			}
			
			
			
			
			if ("vendorId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->vendorId); 
				
			}
			
			
			
			
			if ("sn" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->sn);
				
			}
			
			
			
			
			if ("skuId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->skuId); 
				
			}
			
			
			
			
			if ("name" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->name);
				
			}
			
			
			
			
			if ("brandId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->brandId); 
				
			}
			
			
			
			
			if ("unit" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->unit);
				
			}
			
			
			
			
			if ("sizeName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->sizeName);
				
			}
			
			
			
			
			if ("corlor" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->corlor);
				
			}
			
			
			
			
			if ("categoryId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->categoryId); 
				
			}
			
			
			
			
			if ("isAirForbit" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->isAirForbit);
				
			}
			
			
			
			
			if ("isEasyBreak" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->isEasyBreak);
				
			}
			
			
			
			
			if ("isLarge" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->isLarge);
				
			}
			
			
			
			
			if ("isValuable" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->isValuable);
				
			}
			
			
			
			
			if ("taxRate" == $schemeField){
				
				$needSkip = false;
				$input->readDouble($this->taxRate);
				
			}
			
			
			
			
			if ("length" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->length); 
				
			}
			
			
			
			
			if ("width" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->width); 
				
			}
			
			
			
			
			if ("height" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->height); 
				
			}
			
			
			
			
			if ("weight" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->weight); 
				
			}
			
			
			
			
			if ("temperature" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->temperature);
				
			}
			
			
			
			
			if ("isConsumptionTax" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->isConsumptionTax);
				
			}
			
			
			
			
			if ("vendorName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->vendorName);
				
			}
			
			
			
			
			if ("internationalTaxRates" == $schemeField){
				
				$needSkip = false;
				
				$this->internationalTaxRates = array();
				$_size1 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem1 = null;
						
						$elem1 = new \com\vip\product\gpdc\service\InternationalTaxRate();
						$elem1->read($input);
						
						$this->internationalTaxRates[$_size1++] = $elem1;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
			}
			
			
			
			
			if ("isSerialNumManage" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->isSerialNumManage);
				
			}
			
			
			
			
			if ("oldBarcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->oldBarcode);
				
			}
			
			
			
			
			if ("taxCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->taxCode);
				
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
		
		
		if($this->sn !== null) {
			
			$xfer += $output->writeFieldBegin('sn');
			$xfer += $output->writeString($this->sn);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->skuId !== null) {
			
			$xfer += $output->writeFieldBegin('skuId');
			$xfer += $output->writeI64($this->skuId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->name !== null) {
			
			$xfer += $output->writeFieldBegin('name');
			$xfer += $output->writeString($this->name);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandId !== null) {
			
			$xfer += $output->writeFieldBegin('brandId');
			$xfer += $output->writeI32($this->brandId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->unit !== null) {
			
			$xfer += $output->writeFieldBegin('unit');
			$xfer += $output->writeString($this->unit);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->sizeName !== null) {
			
			$xfer += $output->writeFieldBegin('sizeName');
			$xfer += $output->writeString($this->sizeName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->corlor !== null) {
			
			$xfer += $output->writeFieldBegin('corlor');
			$xfer += $output->writeString($this->corlor);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->categoryId !== null) {
			
			$xfer += $output->writeFieldBegin('categoryId');
			$xfer += $output->writeI32($this->categoryId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isAirForbit !== null) {
			
			$xfer += $output->writeFieldBegin('isAirForbit');
			$xfer += $output->writeString($this->isAirForbit);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isEasyBreak !== null) {
			
			$xfer += $output->writeFieldBegin('isEasyBreak');
			$xfer += $output->writeString($this->isEasyBreak);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isLarge !== null) {
			
			$xfer += $output->writeFieldBegin('isLarge');
			$xfer += $output->writeString($this->isLarge);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isValuable !== null) {
			
			$xfer += $output->writeFieldBegin('isValuable');
			$xfer += $output->writeString($this->isValuable);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->taxRate !== null) {
			
			$xfer += $output->writeFieldBegin('taxRate');
			$xfer += $output->writeDouble($this->taxRate);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->length !== null) {
			
			$xfer += $output->writeFieldBegin('length');
			$xfer += $output->writeI32($this->length);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->width !== null) {
			
			$xfer += $output->writeFieldBegin('width');
			$xfer += $output->writeI32($this->width);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->height !== null) {
			
			$xfer += $output->writeFieldBegin('height');
			$xfer += $output->writeI32($this->height);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->weight !== null) {
			
			$xfer += $output->writeFieldBegin('weight');
			$xfer += $output->writeI32($this->weight);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->temperature !== null) {
			
			$xfer += $output->writeFieldBegin('temperature');
			$xfer += $output->writeString($this->temperature);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isConsumptionTax !== null) {
			
			$xfer += $output->writeFieldBegin('isConsumptionTax');
			$xfer += $output->writeString($this->isConsumptionTax);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->vendorName !== null) {
			
			$xfer += $output->writeFieldBegin('vendorName');
			$xfer += $output->writeString($this->vendorName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->internationalTaxRates !== null) {
			
			$xfer += $output->writeFieldBegin('internationalTaxRates');
			
			if (!is_array($this->internationalTaxRates)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->internationalTaxRates as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isSerialNumManage !== null) {
			
			$xfer += $output->writeFieldBegin('isSerialNumManage');
			$xfer += $output->writeString($this->isSerialNumManage);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->oldBarcode !== null) {
			
			$xfer += $output->writeFieldBegin('oldBarcode');
			$xfer += $output->writeString($this->oldBarcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->taxCode !== null) {
			
			$xfer += $output->writeFieldBegin('taxCode');
			$xfer += $output->writeString($this->taxCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>