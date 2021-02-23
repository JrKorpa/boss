<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class BarcodeMapping {
	
	static $_TSPEC;
	public $vendorId = null;
	public $oldBarcode = null;
	public $residualGrade = null;
	public $newBarcode = null;
	public $errorCode = null;
	public $errorMessage = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'oldBarcode'
			),
			3 => array(
			'var' => 'residualGrade'
			),
			4 => array(
			'var' => 'newBarcode'
			),
			5 => array(
			'var' => 'errorCode'
			),
			6 => array(
			'var' => 'errorMessage'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['oldBarcode'])){
				
				$this->oldBarcode = $vals['oldBarcode'];
			}
			
			
			if (isset($vals['residualGrade'])){
				
				$this->residualGrade = $vals['residualGrade'];
			}
			
			
			if (isset($vals['newBarcode'])){
				
				$this->newBarcode = $vals['newBarcode'];
			}
			
			
			if (isset($vals['errorCode'])){
				
				$this->errorCode = $vals['errorCode'];
			}
			
			
			if (isset($vals['errorMessage'])){
				
				$this->errorMessage = $vals['errorMessage'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'BarcodeMapping';
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
			
			
			
			
			if ("oldBarcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->oldBarcode);
				
			}
			
			
			
			
			if ("residualGrade" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->residualGrade);
				
			}
			
			
			
			
			if ("newBarcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->newBarcode);
				
			}
			
			
			
			
			if ("errorCode" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->errorCode); 
				
			}
			
			
			
			
			if ("errorMessage" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->errorMessage);
				
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
		
		
		if($this->oldBarcode !== null) {
			
			$xfer += $output->writeFieldBegin('oldBarcode');
			$xfer += $output->writeString($this->oldBarcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->residualGrade !== null) {
			
			$xfer += $output->writeFieldBegin('residualGrade');
			$xfer += $output->writeString($this->residualGrade);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->newBarcode !== null) {
			
			$xfer += $output->writeFieldBegin('newBarcode');
			$xfer += $output->writeString($this->newBarcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->errorCode !== null) {
			
			$xfer += $output->writeFieldBegin('errorCode');
			$xfer += $output->writeI32($this->errorCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->errorMessage !== null) {
			
			$xfer += $output->writeFieldBegin('errorMessage');
			$xfer += $output->writeString($this->errorMessage);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>