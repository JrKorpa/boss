<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\jit;

class DeliverySubPick {
	
	static $_TSPEC;
	public $vendorId = null;
	public $subPickNo = null;
	public $poNo = null;
	public $unboxedQuantity = null;
	public $leavingUnboxNum = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'subPickNo'
			),
			3 => array(
			'var' => 'poNo'
			),
			4 => array(
			'var' => 'unboxedQuantity'
			),
			5 => array(
			'var' => 'leavingUnboxNum'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['subPickNo'])){
				
				$this->subPickNo = $vals['subPickNo'];
			}
			
			
			if (isset($vals['poNo'])){
				
				$this->poNo = $vals['poNo'];
			}
			
			
			if (isset($vals['unboxedQuantity'])){
				
				$this->unboxedQuantity = $vals['unboxedQuantity'];
			}
			
			
			if (isset($vals['leavingUnboxNum'])){
				
				$this->leavingUnboxNum = $vals['leavingUnboxNum'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'DeliverySubPick';
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
			
			
			
			
			if ("subPickNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->subPickNo);
				
			}
			
			
			
			
			if ("poNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->poNo);
				
			}
			
			
			
			
			if ("unboxedQuantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->unboxedQuantity); 
				
			}
			
			
			
			
			if ("leavingUnboxNum" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->leavingUnboxNum); 
				
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
		
		
		if($this->subPickNo !== null) {
			
			$xfer += $output->writeFieldBegin('subPickNo');
			$xfer += $output->writeString($this->subPickNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->poNo !== null) {
			
			$xfer += $output->writeFieldBegin('poNo');
			$xfer += $output->writeString($this->poNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->unboxedQuantity !== null) {
			
			$xfer += $output->writeFieldBegin('unboxedQuantity');
			$xfer += $output->writeI32($this->unboxedQuantity);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->leavingUnboxNum !== null) {
			
			$xfer += $output->writeFieldBegin('leavingUnboxNum');
			$xfer += $output->writeI32($this->leavingUnboxNum);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>