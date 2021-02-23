<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\jit;

class DeliveryBox {
	
	static $_TSPEC;
	public $deliveryId = null;
	public $boxNo = null;
	public $boxedQuantity = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'deliveryId'
			),
			2 => array(
			'var' => 'boxNo'
			),
			3 => array(
			'var' => 'boxedQuantity'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['deliveryId'])){
				
				$this->deliveryId = $vals['deliveryId'];
			}
			
			
			if (isset($vals['boxNo'])){
				
				$this->boxNo = $vals['boxNo'];
			}
			
			
			if (isset($vals['boxedQuantity'])){
				
				$this->boxedQuantity = $vals['boxedQuantity'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'DeliveryBox';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("deliveryId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->deliveryId); 
				
			}
			
			
			
			
			if ("boxNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->boxNo);
				
			}
			
			
			
			
			if ("boxedQuantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->boxedQuantity); 
				
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
		
		if($this->deliveryId !== null) {
			
			$xfer += $output->writeFieldBegin('deliveryId');
			$xfer += $output->writeI64($this->deliveryId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->boxNo !== null) {
			
			$xfer += $output->writeFieldBegin('boxNo');
			$xfer += $output->writeString($this->boxNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->boxedQuantity !== null) {
			
			$xfer += $output->writeFieldBegin('boxedQuantity');
			$xfer += $output->writeI32($this->boxedQuantity);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>