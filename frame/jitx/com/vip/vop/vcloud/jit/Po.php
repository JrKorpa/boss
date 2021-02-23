<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\jit;

class Po {
	
	static $_TSPEC;
	public $vendorId = null;
	public $poNo = null;
	public $scheduleId = null;
	public $coMode = null;
	public $brandName = null;
	public $scheduleName = null;
	public $sellTimeStart = null;
	public $sellTimeEnd = null;
	public $poStartTime = null;
	public $quantity = null;
	public $saleQuantity = null;
	public $unpickQuantity = null;
	public $warehouse = null;
	public $partnerId = null;
	public $cooperationNo = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'poNo'
			),
			3 => array(
			'var' => 'scheduleId'
			),
			4 => array(
			'var' => 'coMode'
			),
			5 => array(
			'var' => 'brandName'
			),
			6 => array(
			'var' => 'scheduleName'
			),
			7 => array(
			'var' => 'sellTimeStart'
			),
			8 => array(
			'var' => 'sellTimeEnd'
			),
			9 => array(
			'var' => 'poStartTime'
			),
			10 => array(
			'var' => 'quantity'
			),
			11 => array(
			'var' => 'saleQuantity'
			),
			12 => array(
			'var' => 'unpickQuantity'
			),
			13 => array(
			'var' => 'warehouse'
			),
			14 => array(
			'var' => 'partnerId'
			),
			15 => array(
			'var' => 'cooperationNo'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['poNo'])){
				
				$this->poNo = $vals['poNo'];
			}
			
			
			if (isset($vals['scheduleId'])){
				
				$this->scheduleId = $vals['scheduleId'];
			}
			
			
			if (isset($vals['coMode'])){
				
				$this->coMode = $vals['coMode'];
			}
			
			
			if (isset($vals['brandName'])){
				
				$this->brandName = $vals['brandName'];
			}
			
			
			if (isset($vals['scheduleName'])){
				
				$this->scheduleName = $vals['scheduleName'];
			}
			
			
			if (isset($vals['sellTimeStart'])){
				
				$this->sellTimeStart = $vals['sellTimeStart'];
			}
			
			
			if (isset($vals['sellTimeEnd'])){
				
				$this->sellTimeEnd = $vals['sellTimeEnd'];
			}
			
			
			if (isset($vals['poStartTime'])){
				
				$this->poStartTime = $vals['poStartTime'];
			}
			
			
			if (isset($vals['quantity'])){
				
				$this->quantity = $vals['quantity'];
			}
			
			
			if (isset($vals['saleQuantity'])){
				
				$this->saleQuantity = $vals['saleQuantity'];
			}
			
			
			if (isset($vals['unpickQuantity'])){
				
				$this->unpickQuantity = $vals['unpickQuantity'];
			}
			
			
			if (isset($vals['warehouse'])){
				
				$this->warehouse = $vals['warehouse'];
			}
			
			
			if (isset($vals['partnerId'])){
				
				$this->partnerId = $vals['partnerId'];
			}
			
			
			if (isset($vals['cooperationNo'])){
				
				$this->cooperationNo = $vals['cooperationNo'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'Po';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("vendorId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->vendorId); 
				
			}
			
			
			
			
			if ("poNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->poNo);
				
			}
			
			
			
			
			if ("scheduleId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->scheduleId); 
				
			}
			
			
			
			
			if ("coMode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->coMode);
				
			}
			
			
			
			
			if ("brandName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->brandName);
				
			}
			
			
			
			
			if ("scheduleName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->scheduleName);
				
			}
			
			
			
			
			if ("sellTimeStart" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->sellTimeStart);
				
			}
			
			
			
			
			if ("sellTimeEnd" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->sellTimeEnd);
				
			}
			
			
			
			
			if ("poStartTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->poStartTime);
				
			}
			
			
			
			
			if ("quantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->quantity); 
				
			}
			
			
			
			
			if ("saleQuantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->saleQuantity); 
				
			}
			
			
			
			
			if ("unpickQuantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->unpickQuantity); 
				
			}
			
			
			
			
			if ("warehouse" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->warehouse);
				
			}
			
			
			
			
			if ("partnerId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->partnerId); 
				
			}
			
			
			
			
			if ("cooperationNo" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->cooperationNo); 
				
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
		$xfer += $output->writeI32($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->poNo !== null) {
			
			$xfer += $output->writeFieldBegin('poNo');
			$xfer += $output->writeString($this->poNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->scheduleId !== null) {
			
			$xfer += $output->writeFieldBegin('scheduleId');
			$xfer += $output->writeI64($this->scheduleId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->coMode !== null) {
			
			$xfer += $output->writeFieldBegin('coMode');
			$xfer += $output->writeString($this->coMode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandName !== null) {
			
			$xfer += $output->writeFieldBegin('brandName');
			$xfer += $output->writeString($this->brandName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->scheduleName !== null) {
			
			$xfer += $output->writeFieldBegin('scheduleName');
			$xfer += $output->writeString($this->scheduleName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->sellTimeStart !== null) {
			
			$xfer += $output->writeFieldBegin('sellTimeStart');
			$xfer += $output->writeI64($this->sellTimeStart);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->sellTimeEnd !== null) {
			
			$xfer += $output->writeFieldBegin('sellTimeEnd');
			$xfer += $output->writeI64($this->sellTimeEnd);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->poStartTime !== null) {
			
			$xfer += $output->writeFieldBegin('poStartTime');
			$xfer += $output->writeI64($this->poStartTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->quantity !== null) {
			
			$xfer += $output->writeFieldBegin('quantity');
			$xfer += $output->writeI32($this->quantity);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->saleQuantity !== null) {
			
			$xfer += $output->writeFieldBegin('saleQuantity');
			$xfer += $output->writeI32($this->saleQuantity);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->unpickQuantity !== null) {
			
			$xfer += $output->writeFieldBegin('unpickQuantity');
			$xfer += $output->writeI32($this->unpickQuantity);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->warehouse !== null) {
			
			$xfer += $output->writeFieldBegin('warehouse');
			$xfer += $output->writeString($this->warehouse);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->partnerId !== null) {
			
			$xfer += $output->writeFieldBegin('partnerId');
			$xfer += $output->writeI64($this->partnerId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->cooperationNo !== null) {
			
			$xfer += $output->writeFieldBegin('cooperationNo');
			$xfer += $output->writeI64($this->cooperationNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>