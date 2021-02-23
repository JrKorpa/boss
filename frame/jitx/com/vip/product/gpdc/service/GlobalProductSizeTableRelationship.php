<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class GlobalProductSizeTableRelationship {
	
	static $_TSPEC;
	public $globalProductId = null;
	public $sizeTableId = null;
	public $skuSizeDetailIdMappings = null;
	public $errorMessage = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'globalProductId'
			),
			2 => array(
			'var' => 'sizeTableId'
			),
			3 => array(
			'var' => 'skuSizeDetailIdMappings'
			),
			4 => array(
			'var' => 'errorMessage'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['globalProductId'])){
				
				$this->globalProductId = $vals['globalProductId'];
			}
			
			
			if (isset($vals['sizeTableId'])){
				
				$this->sizeTableId = $vals['sizeTableId'];
			}
			
			
			if (isset($vals['skuSizeDetailIdMappings'])){
				
				$this->skuSizeDetailIdMappings = $vals['skuSizeDetailIdMappings'];
			}
			
			
			if (isset($vals['errorMessage'])){
				
				$this->errorMessage = $vals['errorMessage'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GlobalProductSizeTableRelationship';
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
			
			
			
			
			if ("sizeTableId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->sizeTableId); 
				
			}
			
			
			
			
			if ("skuSizeDetailIdMappings" == $schemeField){
				
				$needSkip = false;
				
				$this->skuSizeDetailIdMappings = array();
				$input->readMapBegin();
				while(true){
					
					try{
						
						$key0 = 0;
						$input->readI64($key0); 
						
						$val0 = 0;
						$input->readI64($val0); 
						
						$this->skuSizeDetailIdMappings[$key0] = $val0;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readMapEnd();
				
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
		
		
		if($this->sizeTableId !== null) {
			
			$xfer += $output->writeFieldBegin('sizeTableId');
			$xfer += $output->writeI64($this->sizeTableId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->skuSizeDetailIdMappings !== null) {
			
			$xfer += $output->writeFieldBegin('skuSizeDetailIdMappings');
			
			if (!is_array($this->skuSizeDetailIdMappings)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeMapBegin();
			foreach ($this->skuSizeDetailIdMappings as $kiter0 => $viter0){
				
				$xfer += $output->writeI64($kiter0);
				
				$xfer += $output->writeI64($viter0);
				
			}
			
			$output->writeMapEnd();
			
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