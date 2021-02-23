<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\omni\store;

class VendorStoreInfoDo {
	
	static $_TSPEC;
	public $id = null;
	public $vendorId = null;
	public $vendorCode = null;
	public $storeCode = null;
	public $storeName = null;
	public $storeType = null;
	public $country = null;
	public $countryCode = null;
	public $province = null;
	public $provinceCode = null;
	public $city = null;
	public $cityCode = null;
	public $district = null;
	public $districtCode = null;
	public $street = null;
	public $streetCode = null;
	public $address = null;
	public $contact = null;
	public $tel = null;
	public $warehouse = null;
	public $priority = null;
	public $zipcode = null;
	public $isDeleted = null;
	public $createTime = null;
	public $updateTime = null;
	public $version = null;
	public $lineNum = null;
	public $carrier = null;
	public $carrierCard = null;
	public $operator = null;
	public $areaWarehouseCode = null;
	public $hasReturnAddress = null;
	public $storeReturnAddressDo = null;
	public $geocodeStatus = null;
	public $latitude = null;
	public $longitude = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'id'
			),
			2 => array(
			'var' => 'vendorId'
			),
			3 => array(
			'var' => 'vendorCode'
			),
			4 => array(
			'var' => 'storeCode'
			),
			5 => array(
			'var' => 'storeName'
			),
			6 => array(
			'var' => 'storeType'
			),
			7 => array(
			'var' => 'country'
			),
			8 => array(
			'var' => 'countryCode'
			),
			9 => array(
			'var' => 'province'
			),
			10 => array(
			'var' => 'provinceCode'
			),
			11 => array(
			'var' => 'city'
			),
			12 => array(
			'var' => 'cityCode'
			),
			13 => array(
			'var' => 'district'
			),
			14 => array(
			'var' => 'districtCode'
			),
			15 => array(
			'var' => 'street'
			),
			16 => array(
			'var' => 'streetCode'
			),
			17 => array(
			'var' => 'address'
			),
			18 => array(
			'var' => 'contact'
			),
			19 => array(
			'var' => 'tel'
			),
			20 => array(
			'var' => 'warehouse'
			),
			21 => array(
			'var' => 'priority'
			),
			22 => array(
			'var' => 'zipcode'
			),
			23 => array(
			'var' => 'isDeleted'
			),
			24 => array(
			'var' => 'createTime'
			),
			25 => array(
			'var' => 'updateTime'
			),
			26 => array(
			'var' => 'version'
			),
			27 => array(
			'var' => 'lineNum'
			),
			28 => array(
			'var' => 'carrier'
			),
			29 => array(
			'var' => 'carrierCard'
			),
			30 => array(
			'var' => 'operator'
			),
			31 => array(
			'var' => 'areaWarehouseCode'
			),
			32 => array(
			'var' => 'hasReturnAddress'
			),
			33 => array(
			'var' => 'storeReturnAddressDo'
			),
			34 => array(
			'var' => 'geocodeStatus'
			),
			35 => array(
			'var' => 'latitude'
			),
			36 => array(
			'var' => 'longitude'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['id'])){
				
				$this->id = $vals['id'];
			}
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['vendorCode'])){
				
				$this->vendorCode = $vals['vendorCode'];
			}
			
			
			if (isset($vals['storeCode'])){
				
				$this->storeCode = $vals['storeCode'];
			}
			
			
			if (isset($vals['storeName'])){
				
				$this->storeName = $vals['storeName'];
			}
			
			
			if (isset($vals['storeType'])){
				
				$this->storeType = $vals['storeType'];
			}
			
			
			if (isset($vals['country'])){
				
				$this->country = $vals['country'];
			}
			
			
			if (isset($vals['countryCode'])){
				
				$this->countryCode = $vals['countryCode'];
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
			
			
			if (isset($vals['contact'])){
				
				$this->contact = $vals['contact'];
			}
			
			
			if (isset($vals['tel'])){
				
				$this->tel = $vals['tel'];
			}
			
			
			if (isset($vals['warehouse'])){
				
				$this->warehouse = $vals['warehouse'];
			}
			
			
			if (isset($vals['priority'])){
				
				$this->priority = $vals['priority'];
			}
			
			
			if (isset($vals['zipcode'])){
				
				$this->zipcode = $vals['zipcode'];
			}
			
			
			if (isset($vals['isDeleted'])){
				
				$this->isDeleted = $vals['isDeleted'];
			}
			
			
			if (isset($vals['createTime'])){
				
				$this->createTime = $vals['createTime'];
			}
			
			
			if (isset($vals['updateTime'])){
				
				$this->updateTime = $vals['updateTime'];
			}
			
			
			if (isset($vals['version'])){
				
				$this->version = $vals['version'];
			}
			
			
			if (isset($vals['lineNum'])){
				
				$this->lineNum = $vals['lineNum'];
			}
			
			
			if (isset($vals['carrier'])){
				
				$this->carrier = $vals['carrier'];
			}
			
			
			if (isset($vals['carrierCard'])){
				
				$this->carrierCard = $vals['carrierCard'];
			}
			
			
			if (isset($vals['operator'])){
				
				$this->operator = $vals['operator'];
			}
			
			
			if (isset($vals['areaWarehouseCode'])){
				
				$this->areaWarehouseCode = $vals['areaWarehouseCode'];
			}
			
			
			if (isset($vals['hasReturnAddress'])){
				
				$this->hasReturnAddress = $vals['hasReturnAddress'];
			}
			
			
			if (isset($vals['storeReturnAddressDo'])){
				
				$this->storeReturnAddressDo = $vals['storeReturnAddressDo'];
			}
			
			
			if (isset($vals['geocodeStatus'])){
				
				$this->geocodeStatus = $vals['geocodeStatus'];
			}
			
			
			if (isset($vals['latitude'])){
				
				$this->latitude = $vals['latitude'];
			}
			
			
			if (isset($vals['longitude'])){
				
				$this->longitude = $vals['longitude'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'VendorStoreInfoDo';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("id" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->id); 
				
			}
			
			
			
			
			if ("vendorId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->vendorId); 
				
			}
			
			
			
			
			if ("vendorCode" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->vendorCode); 
				
			}
			
			
			
			
			if ("storeCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->storeCode);
				
			}
			
			
			
			
			if ("storeName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->storeName);
				
			}
			
			
			
			
			if ("storeType" == $schemeField){
				
				$needSkip = false;
				$input->readByte($this->storeType); 
				
			}
			
			
			
			
			if ("country" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->country);
				
			}
			
			
			
			
			if ("countryCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->countryCode);
				
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
			
			
			
			
			if ("contact" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->contact);
				
			}
			
			
			
			
			if ("tel" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->tel);
				
			}
			
			
			
			
			if ("warehouse" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->warehouse);
				
			}
			
			
			
			
			if ("priority" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->priority);
				
			}
			
			
			
			
			if ("zipcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->zipcode);
				
			}
			
			
			
			
			if ("isDeleted" == $schemeField){
				
				$needSkip = false;
				$input->readByte($this->isDeleted); 
				
			}
			
			
			
			
			if ("createTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->createTime);
				
			}
			
			
			
			
			if ("updateTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->updateTime);
				
			}
			
			
			
			
			if ("version" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->version); 
				
			}
			
			
			
			
			if ("lineNum" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->lineNum); 
				
			}
			
			
			
			
			if ("carrier" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->carrier);
				
			}
			
			
			
			
			if ("carrierCard" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->carrierCard);
				
			}
			
			
			
			
			if ("operator" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->operator);
				
			}
			
			
			
			
			if ("areaWarehouseCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->areaWarehouseCode);
				
			}
			
			
			
			
			if ("hasReturnAddress" == $schemeField){
				
				$needSkip = false;
				$input->readBool($this->hasReturnAddress);
				
			}
			
			
			
			
			if ("storeReturnAddressDo" == $schemeField){
				
				$needSkip = false;
				
				$this->storeReturnAddressDo = new \com\vip\vop\omni\store\model\VendorStoreReturnAddressDo();
				$this->storeReturnAddressDo->read($input);
				
			}
			
			
			
			
			if ("geocodeStatus" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->geocodeStatus); 
				
			}
			
			
			
			
			if ("latitude" == $schemeField){
				
				$needSkip = false;
				$input->readDouble($this->latitude);
				
			}
			
			
			
			
			if ("longitude" == $schemeField){
				
				$needSkip = false;
				$input->readDouble($this->longitude);
				
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
		
		if($this->id !== null) {
			
			$xfer += $output->writeFieldBegin('id');
			$xfer += $output->writeI64($this->id);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->vendorId !== null) {
			
			$xfer += $output->writeFieldBegin('vendorId');
			$xfer += $output->writeI64($this->vendorId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->vendorCode !== null) {
			
			$xfer += $output->writeFieldBegin('vendorCode');
			$xfer += $output->writeI64($this->vendorCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
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
		
		
		if($this->storeType !== null) {
			
			$xfer += $output->writeFieldBegin('storeType');
			$xfer += $output->writeByte($this->storeType);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->country !== null) {
			
			$xfer += $output->writeFieldBegin('country');
			$xfer += $output->writeString($this->country);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->countryCode !== null) {
			
			$xfer += $output->writeFieldBegin('countryCode');
			$xfer += $output->writeString($this->countryCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->province !== null) {
			
			$xfer += $output->writeFieldBegin('province');
			$xfer += $output->writeString($this->province);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->provinceCode !== null) {
			
			$xfer += $output->writeFieldBegin('provinceCode');
			$xfer += $output->writeString($this->provinceCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->city !== null) {
			
			$xfer += $output->writeFieldBegin('city');
			$xfer += $output->writeString($this->city);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->cityCode !== null) {
			
			$xfer += $output->writeFieldBegin('cityCode');
			$xfer += $output->writeString($this->cityCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->district !== null) {
			
			$xfer += $output->writeFieldBegin('district');
			$xfer += $output->writeString($this->district);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
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
		
		
		if($this->address !== null) {
			
			$xfer += $output->writeFieldBegin('address');
			$xfer += $output->writeString($this->address);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->contact !== null) {
			
			$xfer += $output->writeFieldBegin('contact');
			$xfer += $output->writeString($this->contact);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->tel !== null) {
			
			$xfer += $output->writeFieldBegin('tel');
			$xfer += $output->writeString($this->tel);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->warehouse !== null) {
			
			$xfer += $output->writeFieldBegin('warehouse');
			$xfer += $output->writeString($this->warehouse);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->priority !== null) {
			
			$xfer += $output->writeFieldBegin('priority');
			$xfer += $output->writeString($this->priority);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->zipcode !== null) {
			
			$xfer += $output->writeFieldBegin('zipcode');
			$xfer += $output->writeString($this->zipcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isDeleted !== null) {
			
			$xfer += $output->writeFieldBegin('isDeleted');
			$xfer += $output->writeByte($this->isDeleted);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->createTime !== null) {
			
			$xfer += $output->writeFieldBegin('createTime');
			$xfer += $output->writeI64($this->createTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->updateTime !== null) {
			
			$xfer += $output->writeFieldBegin('updateTime');
			$xfer += $output->writeI64($this->updateTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->version !== null) {
			
			$xfer += $output->writeFieldBegin('version');
			$xfer += $output->writeI32($this->version);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->lineNum !== null) {
			
			$xfer += $output->writeFieldBegin('lineNum');
			$xfer += $output->writeI32($this->lineNum);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->carrier !== null) {
			
			$xfer += $output->writeFieldBegin('carrier');
			$xfer += $output->writeString($this->carrier);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->carrierCard !== null) {
			
			$xfer += $output->writeFieldBegin('carrierCard');
			$xfer += $output->writeString($this->carrierCard);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->operator !== null) {
			
			$xfer += $output->writeFieldBegin('operator');
			$xfer += $output->writeString($this->operator);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->areaWarehouseCode !== null) {
			
			$xfer += $output->writeFieldBegin('areaWarehouseCode');
			$xfer += $output->writeString($this->areaWarehouseCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->hasReturnAddress !== null) {
			
			$xfer += $output->writeFieldBegin('hasReturnAddress');
			$xfer += $output->writeBool($this->hasReturnAddress);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->storeReturnAddressDo !== null) {
			
			$xfer += $output->writeFieldBegin('storeReturnAddressDo');
			
			if (!is_object($this->storeReturnAddressDo)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->storeReturnAddressDo->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->geocodeStatus !== null) {
			
			$xfer += $output->writeFieldBegin('geocodeStatus');
			$xfer += $output->writeI32($this->geocodeStatus);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->latitude !== null) {
			
			$xfer += $output->writeFieldBegin('latitude');
			$xfer += $output->writeDouble($this->latitude);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->longitude !== null) {
			
			$xfer += $output->writeFieldBegin('longitude');
			$xfer += $output->writeDouble($this->longitude);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>