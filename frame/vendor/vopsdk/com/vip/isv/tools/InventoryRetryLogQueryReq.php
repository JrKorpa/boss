<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\isv\tools;

class InventoryRetryLogQueryReq {
	
	static $_TSPEC;
	public $vendorId = null;
	public $batchNo = null;
	public $barcode = null;
	public $timeFrom = null;
	public $timeTo = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'batchNo'
			),
			3 => array(
			'var' => 'barcode'
			),
			4 => array(
			'var' => 'timeFrom'
			),
			5 => array(
			'var' => 'timeTo'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['batchNo'])){
				
				$this->batchNo = $vals['batchNo'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
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
		
		return 'InventoryRetryLogQueryReq';
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
			
			
			
			
			if ("batchNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->batchNo);
				
			}
			
			
			
			
			if ("barcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->barcode);
				
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
		$xfer += $output->writeI32($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->batchNo !== null) {
			
			$xfer += $output->writeFieldBegin('batchNo');
			$xfer += $output->writeString($this->batchNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcode !== null) {
			
			$xfer += $output->writeFieldBegin('barcode');
			$xfer += $output->writeString($this->barcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('timeFrom');
		$xfer += $output->writeI64($this->timeFrom);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('timeTo');
		$xfer += $output->writeI64($this->timeTo);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>