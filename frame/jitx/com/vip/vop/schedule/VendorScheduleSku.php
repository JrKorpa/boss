<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\schedule;

class VendorScheduleSku {
	
	static $_TSPEC;
	public $vendorId = null;
	public $scheduleId = null;
	public $barcode = null;
	public $vendorSkuId = null;
	public $stockPushStatus = null;
	
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
			'var' => 'barcode'
			),
			4 => array(
			'var' => 'vendorSkuId'
			),
			5 => array(
			'var' => 'stockPushStatus'
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
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
			if (isset($vals['vendorSkuId'])){
				
				$this->vendorSkuId = $vals['vendorSkuId'];
			}
			
			
			if (isset($vals['stockPushStatus'])){
				
				$this->stockPushStatus = $vals['stockPushStatus'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'VendorScheduleSku';
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
			
			
			
			
			if ("barcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->barcode);
				
			}
			
			
			
			
			if ("vendorSkuId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->vendorSkuId); 
				
			}
			
			
			
			
			if ("stockPushStatus" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->stockPushStatus); 
				
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
		
		$xfer += $output->writeFieldBegin('barcode');
		$xfer += $output->writeString($this->barcode);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('vendorSkuId');
		$xfer += $output->writeI64($this->vendorSkuId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('stockPushStatus');
		$xfer += $output->writeI32($this->stockPushStatus);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>