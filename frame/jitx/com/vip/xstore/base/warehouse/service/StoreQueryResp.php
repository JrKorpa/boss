<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\xstore\base\warehouse\service;

class StoreQueryResp {
	
	static $_TSPEC;
	public $companyCode = null;
	public $storeCode = null;
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
	public $storeName = null;
	public $storeCodeMP = null;
	public $weekdayOpenTime = null;
	public $weekdayCloseTime = null;
	public $weekendOpenTime = null;
	public $weekendCloseTime = null;
	public $warehouseCode = null;
	public $openDate = null;
	public $regionalCode = null;
	public $storeClass = null;
	public $storeLevelCode = null;
	public $countryCode = null;
	public $countryName = null;
	public $floorArea = null;
	public $shoppingMallName = null;
	public $mobie = null;
	public $zipCode = null;
	public $levelCode = null;
	public $firstTransferringDate = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'companyCode'
			),
			2 => array(
			'var' => 'storeCode'
			),
			3 => array(
			'var' => 'provinceCode'
			),
			4 => array(
			'var' => 'province'
			),
			5 => array(
			'var' => 'cityCode'
			),
			6 => array(
			'var' => 'city'
			),
			7 => array(
			'var' => 'districtCode'
			),
			8 => array(
			'var' => 'district'
			),
			9 => array(
			'var' => 'streetCode'
			),
			10 => array(
			'var' => 'street'
			),
			11 => array(
			'var' => 'address'
			),
			12 => array(
			'var' => 'longitude'
			),
			13 => array(
			'var' => 'latitude'
			),
			14 => array(
			'var' => 'tel'
			),
			15 => array(
			'var' => 'contact'
			),
			16 => array(
			'var' => 'storeName'
			),
			17 => array(
			'var' => 'storeCodeMP'
			),
			18 => array(
			'var' => 'weekdayOpenTime'
			),
			19 => array(
			'var' => 'weekdayCloseTime'
			),
			20 => array(
			'var' => 'weekendOpenTime'
			),
			21 => array(
			'var' => 'weekendCloseTime'
			),
			22 => array(
			'var' => 'warehouseCode'
			),
			23 => array(
			'var' => 'openDate'
			),
			24 => array(
			'var' => 'regionalCode'
			),
			25 => array(
			'var' => 'storeClass'
			),
			26 => array(
			'var' => 'storeLevelCode'
			),
			27 => array(
			'var' => 'countryCode'
			),
			28 => array(
			'var' => 'countryName'
			),
			29 => array(
			'var' => 'floorArea'
			),
			30 => array(
			'var' => 'shoppingMallName'
			),
			31 => array(
			'var' => 'mobie'
			),
			32 => array(
			'var' => 'zipCode'
			),
			33 => array(
			'var' => 'levelCode'
			),
			34 => array(
			'var' => 'firstTransferringDate'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['companyCode'])){
				
				$this->companyCode = $vals['companyCode'];
			}
			
			
			if (isset($vals['storeCode'])){
				
				$this->storeCode = $vals['storeCode'];
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
			
			
			if (isset($vals['storeName'])){
				
				$this->storeName = $vals['storeName'];
			}
			
			
			if (isset($vals['storeCodeMP'])){
				
				$this->storeCodeMP = $vals['storeCodeMP'];
			}
			
			
			if (isset($vals['weekdayOpenTime'])){
				
				$this->weekdayOpenTime = $vals['weekdayOpenTime'];
			}
			
			
			if (isset($vals['weekdayCloseTime'])){
				
				$this->weekdayCloseTime = $vals['weekdayCloseTime'];
			}
			
			
			if (isset($vals['weekendOpenTime'])){
				
				$this->weekendOpenTime = $vals['weekendOpenTime'];
			}
			
			
			if (isset($vals['weekendCloseTime'])){
				
				$this->weekendCloseTime = $vals['weekendCloseTime'];
			}
			
			
			if (isset($vals['warehouseCode'])){
				
				$this->warehouseCode = $vals['warehouseCode'];
			}
			
			
			if (isset($vals['openDate'])){
				
				$this->openDate = $vals['openDate'];
			}
			
			
			if (isset($vals['regionalCode'])){
				
				$this->regionalCode = $vals['regionalCode'];
			}
			
			
			if (isset($vals['storeClass'])){
				
				$this->storeClass = $vals['storeClass'];
			}
			
			
			if (isset($vals['storeLevelCode'])){
				
				$this->storeLevelCode = $vals['storeLevelCode'];
			}
			
			
			if (isset($vals['countryCode'])){
				
				$this->countryCode = $vals['countryCode'];
			}
			
			
			if (isset($vals['countryName'])){
				
				$this->countryName = $vals['countryName'];
			}
			
			
			if (isset($vals['floorArea'])){
				
				$this->floorArea = $vals['floorArea'];
			}
			
			
			if (isset($vals['shoppingMallName'])){
				
				$this->shoppingMallName = $vals['shoppingMallName'];
			}
			
			
			if (isset($vals['mobie'])){
				
				$this->mobie = $vals['mobie'];
			}
			
			
			if (isset($vals['zipCode'])){
				
				$this->zipCode = $vals['zipCode'];
			}
			
			
			if (isset($vals['levelCode'])){
				
				$this->levelCode = $vals['levelCode'];
			}
			
			
			if (isset($vals['firstTransferringDate'])){
				
				$this->firstTransferringDate = $vals['firstTransferringDate'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'StoreQueryResp';
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
			
			
			
			
			if ("storeCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->storeCode);
				
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
			
			
			
			
			if ("storeName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->storeName);
				
			}
			
			
			
			
			if ("storeCodeMP" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->storeCodeMP);
				
			}
			
			
			
			
			if ("weekdayOpenTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->weekdayOpenTime);
				
			}
			
			
			
			
			if ("weekdayCloseTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->weekdayCloseTime);
				
			}
			
			
			
			
			if ("weekendOpenTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->weekendOpenTime);
				
			}
			
			
			
			
			if ("weekendCloseTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->weekendCloseTime);
				
			}
			
			
			
			
			if ("warehouseCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->warehouseCode);
				
			}
			
			
			
			
			if ("openDate" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->openDate);
				
			}
			
			
			
			
			if ("regionalCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->regionalCode);
				
			}
			
			
			
			
			if ("storeClass" == $schemeField){
				
				$needSkip = false;
				$input->readByte($this->storeClass); 
				
			}
			
			
			
			
			if ("storeLevelCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->storeLevelCode);
				
			}
			
			
			
			
			if ("countryCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->countryCode);
				
			}
			
			
			
			
			if ("countryName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->countryName);
				
			}
			
			
			
			
			if ("floorArea" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->floorArea); 
				
			}
			
			
			
			
			if ("shoppingMallName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->shoppingMallName);
				
			}
			
			
			
			
			if ("mobie" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->mobie);
				
			}
			
			
			
			
			if ("zipCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->zipCode);
				
			}
			
			
			
			
			if ("levelCode" == $schemeField){
				
				$needSkip = false;
				$input->readByte($this->levelCode); 
				
			}
			
			
			
			
			if ("firstTransferringDate" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->firstTransferringDate);
				
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
		
		
		if($this->storeCode !== null) {
			
			$xfer += $output->writeFieldBegin('storeCode');
			$xfer += $output->writeString($this->storeCode);
			
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
		
		
		if($this->storeName !== null) {
			
			$xfer += $output->writeFieldBegin('storeName');
			$xfer += $output->writeString($this->storeName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->storeCodeMP !== null) {
			
			$xfer += $output->writeFieldBegin('storeCodeMP');
			$xfer += $output->writeString($this->storeCodeMP);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->weekdayOpenTime !== null) {
			
			$xfer += $output->writeFieldBegin('weekdayOpenTime');
			$xfer += $output->writeI64($this->weekdayOpenTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->weekdayCloseTime !== null) {
			
			$xfer += $output->writeFieldBegin('weekdayCloseTime');
			$xfer += $output->writeI64($this->weekdayCloseTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->weekendOpenTime !== null) {
			
			$xfer += $output->writeFieldBegin('weekendOpenTime');
			$xfer += $output->writeI64($this->weekendOpenTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->weekendCloseTime !== null) {
			
			$xfer += $output->writeFieldBegin('weekendCloseTime');
			$xfer += $output->writeI64($this->weekendCloseTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->warehouseCode !== null) {
			
			$xfer += $output->writeFieldBegin('warehouseCode');
			$xfer += $output->writeString($this->warehouseCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->openDate !== null) {
			
			$xfer += $output->writeFieldBegin('openDate');
			$xfer += $output->writeI64($this->openDate);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->regionalCode !== null) {
			
			$xfer += $output->writeFieldBegin('regionalCode');
			$xfer += $output->writeString($this->regionalCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->storeClass !== null) {
			
			$xfer += $output->writeFieldBegin('storeClass');
			$xfer += $output->writeByte($this->storeClass);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->storeLevelCode !== null) {
			
			$xfer += $output->writeFieldBegin('storeLevelCode');
			$xfer += $output->writeString($this->storeLevelCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->countryCode !== null) {
			
			$xfer += $output->writeFieldBegin('countryCode');
			$xfer += $output->writeString($this->countryCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->countryName !== null) {
			
			$xfer += $output->writeFieldBegin('countryName');
			$xfer += $output->writeString($this->countryName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->floorArea !== null) {
			
			$xfer += $output->writeFieldBegin('floorArea');
			$xfer += $output->writeI32($this->floorArea);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->shoppingMallName !== null) {
			
			$xfer += $output->writeFieldBegin('shoppingMallName');
			$xfer += $output->writeString($this->shoppingMallName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->mobie !== null) {
			
			$xfer += $output->writeFieldBegin('mobie');
			$xfer += $output->writeString($this->mobie);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->zipCode !== null) {
			
			$xfer += $output->writeFieldBegin('zipCode');
			$xfer += $output->writeString($this->zipCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->levelCode !== null) {
			
			$xfer += $output->writeFieldBegin('levelCode');
			$xfer += $output->writeByte($this->levelCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->firstTransferringDate !== null) {
			
			$xfer += $output->writeFieldBegin('firstTransferringDate');
			$xfer += $output->writeI64($this->firstTransferringDate);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>