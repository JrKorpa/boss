<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\omni\store\model;

class VendorStoreReturnAddressDo {
	
	static $_TSPEC;
	public $vendorId = null;
	public $storeCode = null;
	public $storeName = null;
	public $province = null;
	public $provinceCode = null;
	public $city = null;
	public $cityCode = null;
	public $district = null;
	public $districtCode = null;
	public $street = null;
	public $streetCode = null;
	public $address = null;
	public $postalCode = null;
	public $contact = null;
	public $tel = null;
	public $lineNum = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'storeCode'
			),
			3 => array(
			'var' => 'storeName'
			),
			4 => array(
			'var' => 'province'
			),
			5 => array(
			'var' => 'provinceCode'
			),
			6 => array(
			'var' => 'city'
			),
			7 => array(
			'var' => 'cityCode'
			),
			8 => array(
			'var' => 'district'
			),
			9 => array(
			'var' => 'districtCode'
			),
			10 => array(
			'var' => 'street'
			),
			11 => array(
			'var' => 'streetCode'
			),
			12 => array(
			'var' => 'address'
			),
			13 => array(
			'var' => 'postalCode'
			),
			14 => array(
			'var' => 'contact'
			),
			15 => array(
			'var' => 'tel'
			),
			16 => array(
			'var' => 'lineNum'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['storeCode'])){
				
				$this->storeCode = $vals['storeCode'];
			}
			
			
			if (isset($vals['storeName'])){
				
				$this->storeName = $vals['storeName'];
			}
			
			
			if (isset($vals['province'])){
				
				$this->province = $vals['province'];
			}
			
			
			if (isset($vals['provinceCode'])){
				
				$this->provinceCode = $vals['provinceCode'];
			}
			
			
			if (isset($vals['city'])){
				
				$this->city = $vals['city'];
			}
			
			
			if (isset($vals['cityCode'])){
				
				$this->cityCode = $vals['cityCode'];
			}
			
			
			if (isset($vals['district'])){
				
				$this->district = $vals['district'];
			}
			
			
			if (isset($vals['districtCode'])){
				
				$this->districtCode = $vals['districtCode'];
			}
			
			
			if (isset($vals['street'])){
				
				$this->street = $vals['street'];
			}
			
			
			if (isset($vals['streetCode'])){
				
				$this->streetCode = $vals['streetCode'];
			}
			
			
			if (isset($vals['address'])){
				
				$this->address = $vals['address'];
			}
			
			
			if (isset($vals['postalCode'])){
				
				$this->postalCode = $vals['postalCode'];
			}
			
			
			if (isset($vals['contact'])){
				
				$this->contact = $vals['contact'];
			}
			
			
			if (isset($vals['tel'])){
				
				$this->tel = $vals['tel'];
			}
			
			
			if (isset($vals['lineNum'])){
				
				$this->lineNum = $vals['lineNum'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'VendorStoreReturnAddressDo';
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
			
			
			
			
			if ("storeCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->storeCode);
				
			}
			
			
			
			
			if ("storeName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->storeName);
				
			}
			
			
			
			
			if ("province" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->province);
				
			}
			
			
			
			
			if ("provinceCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->provinceCode);
				
			}
			
			
			
			
			if ("city" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->city);
				
			}
			
			
			
			
			if ("cityCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->cityCode);
				
			}
			
			
			
			
			if ("district" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->district);
				
			}
			
			
			
			
			if ("districtCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->districtCode);
				
			}
			
			
			
			
			if ("street" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->street);
				
			}
			
			
			
			
			if ("streetCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->streetCode);
				
			}
			
			
			
			
			if ("address" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->address);
				
			}
			
			
			
			
			if ("postalCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->postalCode);
				
			}
			
			
			
			
			if ("contact" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->contact);
				
			}
			
			
			
			
			if ("tel" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->tel);
				
			}
			
			
			
			
			if ("lineNum" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->lineNum); 
				
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
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI64($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->storeCode !== null) {
			
			$xfer += $output->writeFieldBegin('storeCode');
			$xfer += $output->writeString($this->storeCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->storeName !== null) {
			
			$xfer += $output->writeFieldBegin('storeName');
			$xfer += $output->writeString($this->storeName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('province');
		$xfer += $output->writeString($this->province);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->provinceCode !== null) {
			
			$xfer += $output->writeFieldBegin('provinceCode');
			$xfer += $output->writeString($this->provinceCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('city');
		$xfer += $output->writeString($this->city);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->cityCode !== null) {
			
			$xfer += $output->writeFieldBegin('cityCode');
			$xfer += $output->writeString($this->cityCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('district');
		$xfer += $output->writeString($this->district);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->districtCode !== null) {
			
			$xfer += $output->writeFieldBegin('districtCode');
			$xfer += $output->writeString($this->districtCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->street !== null) {
			
			$xfer += $output->writeFieldBegin('street');
			$xfer += $output->writeString($this->street);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->streetCode !== null) {
			
			$xfer += $output->writeFieldBegin('streetCode');
			$xfer += $output->writeString($this->streetCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('address');
		$xfer += $output->writeString($this->address);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->postalCode !== null) {
			
			$xfer += $output->writeFieldBegin('postalCode');
			$xfer += $output->writeString($this->postalCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('contact');
		$xfer += $output->writeString($this->contact);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('tel');
		$xfer += $output->writeString($this->tel);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->lineNum !== null) {
			
			$xfer += $output->writeFieldBegin('lineNum');
			$xfer += $output->writeI32($this->lineNum);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>