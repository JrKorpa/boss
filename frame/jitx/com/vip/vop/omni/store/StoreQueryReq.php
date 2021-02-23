<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\omni\store;

class StoreQueryReq {
	
	static $_TSPEC;
	public $vendorId = null;
	public $storeCodes = null;
	public $provinceCode = null;
	public $cityCode = null;
	public $districtCode = null;
	public $streetCode = null;
	public $contact = null;
	public $tel = null;
	public $areaWarehouseCode = null;
	public $hasReturnAddress = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'storeCodes'
			),
			3 => array(
			'var' => 'provinceCode'
			),
			4 => array(
			'var' => 'cityCode'
			),
			5 => array(
			'var' => 'districtCode'
			),
			6 => array(
			'var' => 'streetCode'
			),
			7 => array(
			'var' => 'contact'
			),
			8 => array(
			'var' => 'tel'
			),
			9 => array(
			'var' => 'areaWarehouseCode'
			),
			10 => array(
			'var' => 'hasReturnAddress'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['storeCodes'])){
				
				$this->storeCodes = $vals['storeCodes'];
			}
			
			
			if (isset($vals['provinceCode'])){
				
				$this->provinceCode = $vals['provinceCode'];
			}
			
			
			if (isset($vals['cityCode'])){
				
				$this->cityCode = $vals['cityCode'];
			}
			
			
			if (isset($vals['districtCode'])){
				
				$this->districtCode = $vals['districtCode'];
			}
			
			
			if (isset($vals['streetCode'])){
				
				$this->streetCode = $vals['streetCode'];
			}
			
			
			if (isset($vals['contact'])){
				
				$this->contact = $vals['contact'];
			}
			
			
			if (isset($vals['tel'])){
				
				$this->tel = $vals['tel'];
			}
			
			
			if (isset($vals['areaWarehouseCode'])){
				
				$this->areaWarehouseCode = $vals['areaWarehouseCode'];
			}
			
			
			if (isset($vals['hasReturnAddress'])){
				
				$this->hasReturnAddress = $vals['hasReturnAddress'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'StoreQueryReq';
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
			
			
			
			
			if ("storeCodes" == $schemeField){
				
				$needSkip = false;
				
				$this->storeCodes = array();
				$_size0 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem0 = null;
						$input->readString($elem0);
						
						$this->storeCodes[$_size0++] = $elem0;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
			}
			
			
			
			
			if ("provinceCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->provinceCode);
				
			}
			
			
			
			
			if ("cityCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->cityCode);
				
			}
			
			
			
			
			if ("districtCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->districtCode);
				
			}
			
			
			
			
			if ("streetCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->streetCode);
				
			}
			
			
			
			
			if ("contact" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->contact);
				
			}
			
			
			
			
			if ("tel" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->tel);
				
			}
			
			
			
			
			if ("areaWarehouseCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->areaWarehouseCode);
				
			}
			
			
			
			
			if ("hasReturnAddress" == $schemeField){
				
				$needSkip = false;
				$input->readBool($this->hasReturnAddress);
				
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
		
		if($this->storeCodes !== null) {
			
			$xfer += $output->writeFieldBegin('storeCodes');
			
			if (!is_array($this->storeCodes)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->storeCodes as $iter0){
				
				$xfer += $output->writeString($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->provinceCode !== null) {
			
			$xfer += $output->writeFieldBegin('provinceCode');
			$xfer += $output->writeString($this->provinceCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->cityCode !== null) {
			
			$xfer += $output->writeFieldBegin('cityCode');
			$xfer += $output->writeString($this->cityCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->districtCode !== null) {
			
			$xfer += $output->writeFieldBegin('districtCode');
			$xfer += $output->writeString($this->districtCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->streetCode !== null) {
			
			$xfer += $output->writeFieldBegin('streetCode');
			$xfer += $output->writeString($this->streetCode);
			
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
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>