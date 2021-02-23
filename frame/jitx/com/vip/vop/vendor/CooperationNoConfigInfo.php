<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vendor;

class CooperationNoConfigInfo {
	
	static $_TSPEC;
	public $vendorId = null;
	public $cooperationNo = null;
	public $forbidden = null;
	public $stage = null;
	public $waringQuantity = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'cooperationNo'
			),
			3 => array(
			'var' => 'forbidden'
			),
			4 => array(
			'var' => 'stage'
			),
			5 => array(
			'var' => 'waringQuantity'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['cooperationNo'])){
				
				$this->cooperationNo = $vals['cooperationNo'];
			}
			
			
			if (isset($vals['forbidden'])){
				
				$this->forbidden = $vals['forbidden'];
			}
			
			
			if (isset($vals['stage'])){
				
				$this->stage = $vals['stage'];
			}
			
			
			if (isset($vals['waringQuantity'])){
				
				$this->waringQuantity = $vals['waringQuantity'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'CooperationNoConfigInfo';
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
			
			
			
			
			if ("cooperationNo" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->cooperationNo); 
				
			}
			
			
			
			
			if ("forbidden" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->forbidden); 
				
			}
			
			
			
			
			if ("stage" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->stage); 
				
			}
			
			
			
			
			if ("waringQuantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->waringQuantity); 
				
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
		
		if($this->vendorId !== null) {
			
			$xfer += $output->writeFieldBegin('vendorId');
			$xfer += $output->writeI32($this->vendorId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->cooperationNo !== null) {
			
			$xfer += $output->writeFieldBegin('cooperationNo');
			$xfer += $output->writeI32($this->cooperationNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->forbidden !== null) {
			
			$xfer += $output->writeFieldBegin('forbidden');
			$xfer += $output->writeI32($this->forbidden);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->stage !== null) {
			
			$xfer += $output->writeFieldBegin('stage');
			$xfer += $output->writeI32($this->stage);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->waringQuantity !== null) {
			
			$xfer += $output->writeFieldBegin('waringQuantity');
			$xfer += $output->writeI32($this->waringQuantity);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>