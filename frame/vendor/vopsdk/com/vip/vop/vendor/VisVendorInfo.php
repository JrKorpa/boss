<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vendor;

class VisVendorInfo {
	
	static $_TSPEC;
	public $vendorId = null;
	public $vendorName = null;
	public $ebsId = null;
	public $vendorCode = null;
	public $taxReference = null;
	public $vendorType = null;
	public $vendorAddr = null;
	public $auditStatus = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'vendorName'
			),
			3 => array(
			'var' => 'ebsId'
			),
			4 => array(
			'var' => 'vendorCode'
			),
			5 => array(
			'var' => 'taxReference'
			),
			6 => array(
			'var' => 'vendorType'
			),
			7 => array(
			'var' => 'vendorAddr'
			),
			8 => array(
			'var' => 'auditStatus'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['vendorName'])){
				
				$this->vendorName = $vals['vendorName'];
			}
			
			
			if (isset($vals['ebsId'])){
				
				$this->ebsId = $vals['ebsId'];
			}
			
			
			if (isset($vals['vendorCode'])){
				
				$this->vendorCode = $vals['vendorCode'];
			}
			
			
			if (isset($vals['taxReference'])){
				
				$this->taxReference = $vals['taxReference'];
			}
			
			
			if (isset($vals['vendorType'])){
				
				$this->vendorType = $vals['vendorType'];
			}
			
			
			if (isset($vals['vendorAddr'])){
				
				$this->vendorAddr = $vals['vendorAddr'];
			}
			
			
			if (isset($vals['auditStatus'])){
				
				$this->auditStatus = $vals['auditStatus'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'VisVendorInfo';
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
			
			
			
			
			if ("vendorName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->vendorName);
				
			}
			
			
			
			
			if ("ebsId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->ebsId); 
				
			}
			
			
			
			
			if ("vendorCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->vendorCode);
				
			}
			
			
			
			
			if ("taxReference" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->taxReference);
				
			}
			
			
			
			
			if ("vendorType" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->vendorType); 
				
			}
			
			
			
			
			if ("vendorAddr" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->vendorAddr);
				
			}
			
			
			
			
			if ("auditStatus" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->auditStatus); 
				
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
		
		
		if($this->vendorName !== null) {
			
			$xfer += $output->writeFieldBegin('vendorName');
			$xfer += $output->writeString($this->vendorName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->ebsId !== null) {
			
			$xfer += $output->writeFieldBegin('ebsId');
			$xfer += $output->writeI32($this->ebsId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->vendorCode !== null) {
			
			$xfer += $output->writeFieldBegin('vendorCode');
			$xfer += $output->writeString($this->vendorCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->taxReference !== null) {
			
			$xfer += $output->writeFieldBegin('taxReference');
			$xfer += $output->writeString($this->taxReference);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->vendorType !== null) {
			
			$xfer += $output->writeFieldBegin('vendorType');
			$xfer += $output->writeI32($this->vendorType);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->vendorAddr !== null) {
			
			$xfer += $output->writeFieldBegin('vendorAddr');
			$xfer += $output->writeString($this->vendorAddr);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->auditStatus !== null) {
			
			$xfer += $output->writeFieldBegin('auditStatus');
			$xfer += $output->writeI32($this->auditStatus);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>