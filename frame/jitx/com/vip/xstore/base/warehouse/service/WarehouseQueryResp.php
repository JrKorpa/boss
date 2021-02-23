<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\xstore\base\warehouse\service;

class WarehouseQueryResp {
	
	static $_TSPEC;
	public $companyCode = null;
	public $xstoreWarehouseCode = null;
	public $xstoreWarehouseName = null;
	public $logisiticsName = null;
	public $logisiticsCustomerCode = null;
	public $logisiticsWarehouseCode = null;
	public $erpWarehouseCode = null;
	public $isWhale = null;
	public $provinceCode = null;
	public $province = null;
	public $cityCode = null;
	public $city = null;
	public $districtCode = null;
	public $district = null;
	public $streetCode = null;
	public $street = null;
	public $address = null;
	public $longitude = null;
	public $latitude = null;
	public $tel = null;
	public $contact = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'companyCode'
			),
			2 => array(
			'var' => 'xstoreWarehouseCode'
			),
			3 => array(
			'var' => 'xstoreWarehouseName'
			),
			4 => array(
			'var' => 'logisiticsName'
			),
			5 => array(
			'var' => 'logisiticsCustomerCode'
			),
			6 => array(
			'var' => 'logisiticsWarehouseCode'
			),
			7 => array(
			'var' => 'erpWarehouseCode'
			),
			8 => array(
			'var' => 'isWhale'
			),
			9 => array(
			'var' => 'provinceCode'
			),
			10 => array(
			'var' => 'province'
			),
			11 => array(
			'var' => 'cityCode'
			),
			12 => array(
			'var' => 'city'
			),
			13 => array(
			'var' => 'districtCode'
			),
			14 => array(
			'var' => 'district'
			),
			15 => array(
			'var' => 'streetCode'
			),
			16 => array(
			'var' => 'street'
			),
			17 => array(
			'var' => 'address'
			),
			18 => array(
			'var' => 'longitude'
			),
			19 => array(
			'var' => 'latitude'
			),
			20 => array(
			'var' => 'tel'
			),
			21 => array(
			'var' => 'contact'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['companyCode'])){
				
				$this->companyCode = $vals['companyCode'];
			}
			
			
			if (isset($vals['xstoreWarehouseCode'])){
				
				$this->xstoreWarehouseCode = $vals['xstoreWarehouseCode'];
			}
			
			
			if (isset($vals['xstoreWarehouseName'])){
				
				$this->xstoreWarehouseName = $vals['xstoreWarehouseName'];
			}
			
			
			if (isset($vals['logisiticsName'])){
				
				$this->logisiticsName = $vals['logisiticsName'];
			}
			
			
			if (isset($vals['logisiticsCustomerCode'])){
				
				$this->logisiticsCustomerCode = $vals['logisiticsCustomerCode'];
			}
			
			
			if (isset($vals['logisiticsWarehouseCode'])){
				
				$this->logisiticsWarehouseCode = $vals['logisiticsWarehouseCode'];
			}
			
			
			if (isset($vals['erpWarehouseCode'])){
				
				$this->erpWarehouseCode = $vals['erpWarehouseCode'];
			}
			
			
			if (isset($vals['isWhale'])){
				
				$this->isWhale = $vals['isWhale'];
			}
			
			
			if (isset($vals['provinceCode'])){
				
				$this->provinceCode = $vals['provinceCode'];
			}
			
			
			if (isset($vals['province'])){
				
				$this->province = $vals['province'];
			}
			
			
			if (isset($vals['cityCode'])){
				
				$this->cityCode = $vals['cityCode'];
			}
			
			
			if (isset($vals['city'])){
				
				$this->city = $vals['city'];
			}
			
			
			if (isset($vals['districtCode'])){
				
				$this->districtCode = $vals['districtCode'];
			}
			
			
			if (isset($vals['district'])){
				
				$this->district = $vals['district'];
			}
			
			
			if (isset($vals['streetCode'])){
				
				$this->streetCode = $vals['streetCode'];
			}
			
			
			if (isset($vals['street'])){
				
				$this->street = $vals['street'];
			}
			
			
			if (isset($vals['address'])){
				
				$this->address = $vals['address'];
			}
			
			
			if (isset($vals['longitude'])){
				
				$this->longitude = $vals['longitude'];
			}
			
			
			if (isset($vals['latitude'])){
				
				$this->latitude = $vals['latitude'];
			}
			
			
			if (isset($vals['tel'])){
				
				$this->tel = $vals['tel'];
			}
			
			
			if (isset($vals['contact'])){
				
				$this->contact = $vals['contact'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'WarehouseQueryResp';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("companyCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->companyCode);
				
			}
			
			
			
			
			if ("xstoreWarehouseCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->xstoreWarehouseCode);
				
			}
			
			
			
			
			if ("xstoreWarehouseName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->xstoreWarehouseName);
				
			}
			
			
			
			
			if ("logisiticsName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->logisiticsName);
				
			}
			
			
			
			
			if ("logisiticsCustomerCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->logisiticsCustomerCode);
				
			}
			
			
			
			
			if ("logisiticsWarehouseCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->logisiticsWarehouseCode);
				
			}
			
			
			
			
			if ("erpWarehouseCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->erpWarehouseCode);
				
			}
			
			
			
			
			if ("isWhale" == $schemeField){
				
				$needSkip = false;
				$input->readByte($this->isWhale); 
				
			}
			
			
			
			
			if ("provinceCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->provinceCode);
				
			}
			
			
			
			
			if ("province" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->province);
				
			}
			
			
			
			
			if ("cityCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->cityCode);
				
			}
			
			
			
			
			if ("city" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->city);
				
			}
			
			
			
			
			if ("districtCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->districtCode);
				
			}
			
			
			
			
			if ("district" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->district);
				
			}
			
			
			
			
			if ("streetCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->streetCode);
				
			}
			
			
			
			
			if ("street" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->street);
				
			}
			
			
			
			
			if ("address" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->address);
				
			}
			
			
			
			
			if ("longitude" == $schemeField){
				
				$needSkip = false;
				$input->readDouble($this->longitude);
				
			}
			
			
			
			
			if ("latitude" == $schemeField){
				
				$needSkip = false;
				$input->readDouble($this->latitude);
				
			}
			
			
			
			
			if ("tel" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->tel);
				
			}
			
			
			
			
			if ("contact" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->contact);
				
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
		
		if($this->companyCode !== null) {
			
			$xfer += $output->writeFieldBegin('companyCode');
			$xfer += $output->writeString($this->companyCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->xstoreWarehouseCode !== null) {
			
			$xfer += $output->writeFieldBegin('xstoreWarehouseCode');
			$xfer += $output->writeString($this->xstoreWarehouseCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->xstoreWarehouseName !== null) {
			
			$xfer += $output->writeFieldBegin('xstoreWarehouseName');
			$xfer += $output->writeString($this->xstoreWarehouseName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->logisiticsName !== null) {
			
			$xfer += $output->writeFieldBegin('logisiticsName');
			$xfer += $output->writeString($this->logisiticsName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->logisiticsCustomerCode !== null) {
			
			$xfer += $output->writeFieldBegin('logisiticsCustomerCode');
			$xfer += $output->writeString($this->logisiticsCustomerCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->logisiticsWarehouseCode !== null) {
			
			$xfer += $output->writeFieldBegin('logisiticsWarehouseCode');
			$xfer += $output->writeString($this->logisiticsWarehouseCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->erpWarehouseCode !== null) {
			
			$xfer += $output->writeFieldBegin('erpWarehouseCode');
			$xfer += $output->writeString($this->erpWarehouseCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isWhale !== null) {
			
			$xfer += $output->writeFieldBegin('isWhale');
			$xfer += $output->writeByte($this->isWhale);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->provinceCode !== null) {
			
			$xfer += $output->writeFieldBegin('provinceCode');
			$xfer += $output->writeString($this->provinceCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->province !== null) {
			
			$xfer += $output->writeFieldBegin('province');
			$xfer += $output->writeString($this->province);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->cityCode !== null) {
			
			$xfer += $output->writeFieldBegin('cityCode');
			$xfer += $output->writeString($this->cityCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->city !== null) {
			
			$xfer += $output->writeFieldBegin('city');
			$xfer += $output->writeString($this->city);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->districtCode !== null) {
			
			$xfer += $output->writeFieldBegin('districtCode');
			$xfer += $output->writeString($this->districtCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->district !== null) {
			
			$xfer += $output->writeFieldBegin('district');
			$xfer += $output->writeString($this->district);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->streetCode !== null) {
			
			$xfer += $output->writeFieldBegin('streetCode');
			$xfer += $output->writeString($this->streetCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->street !== null) {
			
			$xfer += $output->writeFieldBegin('street');
			$xfer += $output->writeString($this->street);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->address !== null) {
			
			$xfer += $output->writeFieldBegin('address');
			$xfer += $output->writeString($this->address);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->longitude !== null) {
			
			$xfer += $output->writeFieldBegin('longitude');
			$xfer += $output->writeDouble($this->longitude);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->latitude !== null) {
			
			$xfer += $output->writeFieldBegin('latitude');
			$xfer += $output->writeDouble($this->latitude);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->tel !== null) {
			
			$xfer += $output->writeFieldBegin('tel');
			$xfer += $output->writeString($this->tel);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->contact !== null) {
			
			$xfer += $output->writeFieldBegin('contact');
			$xfer += $output->writeString($this->contact);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>