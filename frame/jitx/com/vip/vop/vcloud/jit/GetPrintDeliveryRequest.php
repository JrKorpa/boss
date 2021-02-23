<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\jit;

class GetPrintDeliveryRequest {
	
	static $_TSPEC;
	public $vendorId = null;
	public $storageNo = null;
	public $poNo = null;
	public $boxNo = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'storageNo'
			),
			3 => array(
			'var' => 'poNo'
			),
			4 => array(
			'var' => 'boxNo'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['storageNo'])){
				
				$this->storageNo = $vals['storageNo'];
			}
			
			
			if (isset($vals['poNo'])){
				
				$this->poNo = $vals['poNo'];
			}
			
			
			if (isset($vals['boxNo'])){
				
				$this->boxNo = $vals['boxNo'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GetPrintDeliveryRequest';
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
			
			
			
			
			if ("storageNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->storageNo);
				
			}
			
			
			
			
			if ("poNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->poNo);
				
			}
			
			
			
			
			if ("boxNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->boxNo);
				
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
		
		$xfer += $output->writeFieldBegin('storageNo');
		$xfer += $output->writeString($this->storageNo);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('poNo');
		$xfer += $output->writeString($this->poNo);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->boxNo !== null) {
			
			$xfer += $output->writeFieldBegin('boxNo');
			$xfer += $output->writeString($this->boxNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>