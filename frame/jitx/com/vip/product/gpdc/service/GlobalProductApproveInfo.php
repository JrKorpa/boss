<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class GlobalProductApproveInfo {
	
	static $_TSPEC;
	public $globalProductId = null;
	public $masterStatus = null;
	public $imageStatus = null;
	public $catePropsStatus = null;
	public $errorMessage = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'globalProductId'
			),
			2 => array(
			'var' => 'masterStatus'
			),
			3 => array(
			'var' => 'imageStatus'
			),
			4 => array(
			'var' => 'catePropsStatus'
			),
			5 => array(
			'var' => 'errorMessage'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['globalProductId'])){
				
				$this->globalProductId = $vals['globalProductId'];
			}
			
			
			if (isset($vals['masterStatus'])){
				
				$this->masterStatus = $vals['masterStatus'];
			}
			
			
			if (isset($vals['imageStatus'])){
				
				$this->imageStatus = $vals['imageStatus'];
			}
			
			
			if (isset($vals['catePropsStatus'])){
				
				$this->catePropsStatus = $vals['catePropsStatus'];
			}
			
			
			if (isset($vals['errorMessage'])){
				
				$this->errorMessage = $vals['errorMessage'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GlobalProductApproveInfo';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("globalProductId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->globalProductId); 
				
			}
			
			
			
			
			if ("masterStatus" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->masterStatus); 
				
			}
			
			
			
			
			if ("imageStatus" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->imageStatus); 
				
			}
			
			
			
			
			if ("catePropsStatus" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->catePropsStatus); 
				
			}
			
			
			
			
			if ("errorMessage" == $schemeField){
				
				$needSkip = false;
				
				$this->errorMessage = new \com\vip\product\gpdc\service\ErrorMessage();
				$this->errorMessage->read($input);
				
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
		
		if($this->globalProductId !== null) {
			
			$xfer += $output->writeFieldBegin('globalProductId');
			$xfer += $output->writeI64($this->globalProductId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->masterStatus !== null) {
			
			$xfer += $output->writeFieldBegin('masterStatus');
			$xfer += $output->writeI32($this->masterStatus);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->imageStatus !== null) {
			
			$xfer += $output->writeFieldBegin('imageStatus');
			$xfer += $output->writeI32($this->imageStatus);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->catePropsStatus !== null) {
			
			$xfer += $output->writeFieldBegin('catePropsStatus');
			$xfer += $output->writeI32($this->catePropsStatus);
			
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
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>