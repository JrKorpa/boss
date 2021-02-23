<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\xstore\base\warehouse\service;

class StoreBrandLimitResultDTO {
	
	static $_TSPEC;
	public $code = null;
	public $errorMsg = null;
	public $storeCode = null;
	public $storeLevelCode = null;
	public $storeLevelName = null;
	public $storeBrandLimitDetails = null;
	public $storeLevelBrandLimitDetails = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'code'
			),
			2 => array(
			'var' => 'errorMsg'
			),
			3 => array(
			'var' => 'storeCode'
			),
			4 => array(
			'var' => 'storeLevelCode'
			),
			5 => array(
			'var' => 'storeLevelName'
			),
			6 => array(
			'var' => 'storeBrandLimitDetails'
			),
			7 => array(
			'var' => 'storeLevelBrandLimitDetails'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['code'])){
				
				$this->code = $vals['code'];
			}
			
			
			if (isset($vals['errorMsg'])){
				
				$this->errorMsg = $vals['errorMsg'];
			}
			
			
			if (isset($vals['storeCode'])){
				
				$this->storeCode = $vals['storeCode'];
			}
			
			
			if (isset($vals['storeLevelCode'])){
				
				$this->storeLevelCode = $vals['storeLevelCode'];
			}
			
			
			if (isset($vals['storeLevelName'])){
				
				$this->storeLevelName = $vals['storeLevelName'];
			}
			
			
			if (isset($vals['storeBrandLimitDetails'])){
				
				$this->storeBrandLimitDetails = $vals['storeBrandLimitDetails'];
			}
			
			
			if (isset($vals['storeLevelBrandLimitDetails'])){
				
				$this->storeLevelBrandLimitDetails = $vals['storeLevelBrandLimitDetails'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'StoreBrandLimitResultDTO';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("code" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->code); 
				
			}
			
			
			
			
			if ("errorMsg" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->errorMsg);
				
			}
			
			
			
			
			if ("storeCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->storeCode);
				
			}
			
			
			
			
			if ("storeLevelCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->storeLevelCode);
				
			}
			
			
			
			
			if ("storeLevelName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->storeLevelName);
				
			}
			
			
			
			
			if ("storeBrandLimitDetails" == $schemeField){
				
				$needSkip = false;
				
				$this->storeBrandLimitDetails = array();
				$_size0 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem0 = null;
						
						$elem0 = new \com\vip\xstore\base\warehouse\service\StoreBrandLimitDetail();
						$elem0->read($input);
						
						$this->storeBrandLimitDetails[$_size0++] = $elem0;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
			}
			
			
			
			
			if ("storeLevelBrandLimitDetails" == $schemeField){
				
				$needSkip = false;
				
				$this->storeLevelBrandLimitDetails = array();
				$_size1 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem1 = null;
						
						$elem1 = new \com\vip\xstore\base\warehouse\service\StoreBrandLimitDetail();
						$elem1->read($input);
						
						$this->storeLevelBrandLimitDetails[$_size1++] = $elem1;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
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
		
		if($this->code !== null) {
			
			$xfer += $output->writeFieldBegin('code');
			$xfer += $output->writeI32($this->code);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->errorMsg !== null) {
			
			$xfer += $output->writeFieldBegin('errorMsg');
			$xfer += $output->writeString($this->errorMsg);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->storeCode !== null) {
			
			$xfer += $output->writeFieldBegin('storeCode');
			$xfer += $output->writeString($this->storeCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->storeLevelCode !== null) {
			
			$xfer += $output->writeFieldBegin('storeLevelCode');
			$xfer += $output->writeString($this->storeLevelCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->storeLevelName !== null) {
			
			$xfer += $output->writeFieldBegin('storeLevelName');
			$xfer += $output->writeString($this->storeLevelName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->storeBrandLimitDetails !== null) {
			
			$xfer += $output->writeFieldBegin('storeBrandLimitDetails');
			
			if (!is_array($this->storeBrandLimitDetails)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->storeBrandLimitDetails as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->storeLevelBrandLimitDetails !== null) {
			
			$xfer += $output->writeFieldBegin('storeLevelBrandLimitDetails');
			
			if (!is_array($this->storeLevelBrandLimitDetails)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->storeLevelBrandLimitDetails as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>