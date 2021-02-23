<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class GlobalSkuOwnerExp {
	
	static $_TSPEC;
	public $barcodeUppercase = null;
	public $madeIn = null;
	public $expirationDate = null;
	public $isExpManagent = null;
	public $isProductionDate = null;
	public $isInvalid = null;
	public $insuranceDate = null;
	public $acceptanceDeadline = null;
	public $saleDeadline = null;
	public $errorMessage = null;
	public $operationModes = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'barcodeUppercase'
			),
			2 => array(
			'var' => 'madeIn'
			),
			3 => array(
			'var' => 'expirationDate'
			),
			4 => array(
			'var' => 'isExpManagent'
			),
			5 => array(
			'var' => 'isProductionDate'
			),
			6 => array(
			'var' => 'isInvalid'
			),
			7 => array(
			'var' => 'insuranceDate'
			),
			8 => array(
			'var' => 'acceptanceDeadline'
			),
			9 => array(
			'var' => 'saleDeadline'
			),
			10 => array(
			'var' => 'errorMessage'
			),
			11 => array(
			'var' => 'operationModes'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['barcodeUppercase'])){
				
				$this->barcodeUppercase = $vals['barcodeUppercase'];
			}
			
			
			if (isset($vals['madeIn'])){
				
				$this->madeIn = $vals['madeIn'];
			}
			
			
			if (isset($vals['expirationDate'])){
				
				$this->expirationDate = $vals['expirationDate'];
			}
			
			
			if (isset($vals['isExpManagent'])){
				
				$this->isExpManagent = $vals['isExpManagent'];
			}
			
			
			if (isset($vals['isProductionDate'])){
				
				$this->isProductionDate = $vals['isProductionDate'];
			}
			
			
			if (isset($vals['isInvalid'])){
				
				$this->isInvalid = $vals['isInvalid'];
			}
			
			
			if (isset($vals['insuranceDate'])){
				
				$this->insuranceDate = $vals['insuranceDate'];
			}
			
			
			if (isset($vals['acceptanceDeadline'])){
				
				$this->acceptanceDeadline = $vals['acceptanceDeadline'];
			}
			
			
			if (isset($vals['saleDeadline'])){
				
				$this->saleDeadline = $vals['saleDeadline'];
			}
			
			
			if (isset($vals['errorMessage'])){
				
				$this->errorMessage = $vals['errorMessage'];
			}
			
			
			if (isset($vals['operationModes'])){
				
				$this->operationModes = $vals['operationModes'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GlobalSkuOwnerExp';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("barcodeUppercase" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->barcodeUppercase);
				
			}
			
			
			
			
			if ("madeIn" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->madeIn); 
				
			}
			
			
			
			
			if ("expirationDate" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->expirationDate); 
				
			}
			
			
			
			
			if ("isExpManagent" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->isExpManagent);
				
			}
			
			
			
			
			if ("isProductionDate" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->isProductionDate);
				
			}
			
			
			
			
			if ("isInvalid" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->isInvalid);
				
			}
			
			
			
			
			if ("insuranceDate" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->insuranceDate); 
				
			}
			
			
			
			
			if ("acceptanceDeadline" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->acceptanceDeadline); 
				
			}
			
			
			
			
			if ("saleDeadline" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->saleDeadline); 
				
			}
			
			
			
			
			if ("errorMessage" == $schemeField){
				
				$needSkip = false;
				
				$this->errorMessage = new \com\vip\product\gpdc\service\ErrorMessage();
				$this->errorMessage->read($input);
				
			}
			
			
			
			
			if ("operationModes" == $schemeField){
				
				$needSkip = false;
				
				$names = \com\vip\product\gpdc\service\OperationModes::$__names;
				$name = null;
				$input->readString($name);
				foreach ($names as $k => $v){
					
					if($name == $v){
						
						$this->operationModes = $k;
						break;
					}
					
				}
				
				
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
		
		if($this->barcodeUppercase !== null) {
			
			$xfer += $output->writeFieldBegin('barcodeUppercase');
			$xfer += $output->writeString($this->barcodeUppercase);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->madeIn !== null) {
			
			$xfer += $output->writeFieldBegin('madeIn');
			$xfer += $output->writeI32($this->madeIn);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->expirationDate !== null) {
			
			$xfer += $output->writeFieldBegin('expirationDate');
			$xfer += $output->writeI32($this->expirationDate);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isExpManagent !== null) {
			
			$xfer += $output->writeFieldBegin('isExpManagent');
			$xfer += $output->writeString($this->isExpManagent);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isProductionDate !== null) {
			
			$xfer += $output->writeFieldBegin('isProductionDate');
			$xfer += $output->writeString($this->isProductionDate);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isInvalid !== null) {
			
			$xfer += $output->writeFieldBegin('isInvalid');
			$xfer += $output->writeString($this->isInvalid);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->insuranceDate !== null) {
			
			$xfer += $output->writeFieldBegin('insuranceDate');
			$xfer += $output->writeI32($this->insuranceDate);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->acceptanceDeadline !== null) {
			
			$xfer += $output->writeFieldBegin('acceptanceDeadline');
			$xfer += $output->writeI32($this->acceptanceDeadline);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->saleDeadline !== null) {
			
			$xfer += $output->writeFieldBegin('saleDeadline');
			$xfer += $output->writeI32($this->saleDeadline);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->errorMessage !== null) {
			
			$xfer += $output->writeFieldBegin('errorMessage');
			
			if (!is_object($this->errorMessage)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->errorMessage->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->operationModes !== null) {
			
			$xfer += $output->writeFieldBegin('operationModes');
			
			$em = new \com\vip\product\gpdc\service\OperationModes; 
			$output->writeString($em::$__names[$this->operationModes]);  
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>