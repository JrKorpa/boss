<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\schedule;

class VendorSchedule {
	
	static $_TSPEC;
	public $vendorId = null;
	public $scheduleId = null;
	public $cooperationNo = null;
	public $warehouse = null;
	public $businessType = null;
	public $isAreaSales = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'scheduleId'
			),
			3 => array(
			'var' => 'cooperationNo'
			),
			4 => array(
			'var' => 'warehouse'
			),
			5 => array(
			'var' => 'businessType'
			),
			6 => array(
			'var' => 'isAreaSales'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['scheduleId'])){
				
				$this->scheduleId = $vals['scheduleId'];
			}
			
			
			if (isset($vals['cooperationNo'])){
				
				$this->cooperationNo = $vals['cooperationNo'];
			}
			
			
			if (isset($vals['warehouse'])){
				
				$this->warehouse = $vals['warehouse'];
			}
			
			
			if (isset($vals['businessType'])){
				
				$this->businessType = $vals['businessType'];
			}
			
			
			if (isset($vals['isAreaSales'])){
				
				$this->isAreaSales = $vals['isAreaSales'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'VendorSchedule';
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
			
			
			
			
			if ("scheduleId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->scheduleId); 
				
			}
			
			
			
			
			if ("cooperationNo" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->cooperationNo); 
				
			}
			
			
			
			
			if ("warehouse" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->warehouse);
				
			}
			
			
			
			
			if ("businessType" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->businessType); 
				
			}
			
			
			
			
			if ("isAreaSales" == $schemeField){
				
				$needSkip = false;
				$input->readBool($this->isAreaSales);
				
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
		
		$xfer += $output->writeFieldBegin('scheduleId');
		$xfer += $output->writeI64($this->scheduleId);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->cooperationNo !== null) {
			
			$xfer += $output->writeFieldBegin('cooperationNo');
			$xfer += $output->writeI64($this->cooperationNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('warehouse');
		$xfer += $output->writeString($this->warehouse);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('businessType');
		$xfer += $output->writeI32($this->businessType);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->isAreaSales !== null) {
			
			$xfer += $output->writeFieldBegin('isAreaSales');
			$xfer += $output->writeBool($this->isAreaSales);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>