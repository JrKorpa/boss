<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class GlobalProductWithSkus {
	
	static $_TSPEC;
	public $globalProduct = null;
	public $skuList = null;
	public $flagsMap = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'globalProduct'
			),
			2 => array(
			'var' => 'skuList'
			),
			3 => array(
			'var' => 'flagsMap'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['globalProduct'])){
				
				$this->globalProduct = $vals['globalProduct'];
			}
			
			
			if (isset($vals['skuList'])){
				
				$this->skuList = $vals['skuList'];
			}
			
			
			if (isset($vals['flagsMap'])){
				
				$this->flagsMap = $vals['flagsMap'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GlobalProductWithSkus';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("globalProduct" == $schemeField){
				
				$needSkip = false;
				
				$this->globalProduct = new \com\vip\product\gpdc\service\GlobalProduct();
				$this->globalProduct->read($input);
				
			}
			
			
			
			
			if ("skuList" == $schemeField){
				
				$needSkip = false;
				
				$this->skuList = array();
				$_size0 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem0 = null;
						
						$elem0 = new \com\vip\product\gpdc\service\GlobalSku();
						$elem0->read($input);
						
						$this->skuList[$_size0++] = $elem0;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
			}
			
			
			
			
			if ("flagsMap" == $schemeField){
				
				$needSkip = false;
				
				$this->flagsMap = array();
				$input->readMapBegin();
				while(true){
					
					try{
						
						$key1 = 0;
						$input->readI32($key1); 
						
						$val1 = 0;
						$input->readI32($val1); 
						
						$this->flagsMap[$key1] = $val1;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readMapEnd();
				
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
		
		if($this->globalProduct !== null) {
			
			$xfer += $output->writeFieldBegin('globalProduct');
			
			if (!is_object($this->globalProduct)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->globalProduct->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->skuList !== null) {
			
			$xfer += $output->writeFieldBegin('skuList');
			
			if (!is_array($this->skuList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->skuList as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->flagsMap !== null) {
			
			$xfer += $output->writeFieldBegin('flagsMap');
			
			if (!is_array($this->flagsMap)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeMapBegin();
			foreach ($this->flagsMap as $kiter0 => $viter0){
				
				$xfer += $output->writeI32($kiter0);
				
				$xfer += $output->writeI32($viter0);
				
			}
			
			$output->writeMapEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>