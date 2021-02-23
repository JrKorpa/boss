<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\isv\tools;

class VendorSalesQueryReq {
	
	static $_TSPEC;
	public $vendorId = null;
	public $salesNo = null;
	public $timeFrom = null;
	public $timeTo = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'salesNo'
			),
			3 => array(
			'var' => 'timeFrom'
			),
			4 => array(
			'var' => 'timeTo'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['salesNo'])){
				
				$this->salesNo = $vals['salesNo'];
			}
			
			
			if (isset($vals['timeFrom'])){
				
				$this->timeFrom = $vals['timeFrom'];
			}
			
			
			if (isset($vals['timeTo'])){
				
				$this->timeTo = $vals['timeTo'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'VendorSalesQueryReq';
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
			
			
			
			
			if ("salesNo" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->salesNo); 
				
			}
			
			
			
			
			if ("timeFrom" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->timeFrom);
				
			}
			
			
			
			
			if ("timeTo" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->timeTo);
				
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
		
		if($this->salesNo !== null) {
			
			$xfer += $output->writeFieldBegin('salesNo');
			$xfer += $output->writeI64($this->salesNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->timeFrom !== null) {
			
			$xfer += $output->writeFieldBegin('timeFrom');
			$xfer += $output->writeI64($this->timeFrom);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->timeTo !== null) {
			
			$xfer += $output->writeFieldBegin('timeTo');
			$xfer += $output->writeI64($this->timeTo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>