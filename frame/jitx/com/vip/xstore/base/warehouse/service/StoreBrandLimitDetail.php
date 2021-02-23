<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\xstore\base\warehouse\service;

class StoreBrandLimitDetail {
	
	static $_TSPEC;
	public $brandSn = null;
	public $brandCnName = null;
	public $brandEnName = null;
	public $limitedQuantity = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'brandSn'
			),
			2 => array(
			'var' => 'brandCnName'
			),
			3 => array(
			'var' => 'brandEnName'
			),
			4 => array(
			'var' => 'limitedQuantity'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['brandSn'])){
				
				$this->brandSn = $vals['brandSn'];
			}
			
			
			if (isset($vals['brandCnName'])){
				
				$this->brandCnName = $vals['brandCnName'];
			}
			
			
			if (isset($vals['brandEnName'])){
				
				$this->brandEnName = $vals['brandEnName'];
			}
			
			
			if (isset($vals['limitedQuantity'])){
				
				$this->limitedQuantity = $vals['limitedQuantity'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'StoreBrandLimitDetail';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("brandSn" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->brandSn);
				
			}
			
			
			
			
			if ("brandCnName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->brandCnName);
				
			}
			
			
			
			
			if ("brandEnName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->brandEnName);
				
			}
			
			
			
			
			if ("limitedQuantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->limitedQuantity); 
				
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
		
		if($this->brandSn !== null) {
			
			$xfer += $output->writeFieldBegin('brandSn');
			$xfer += $output->writeString($this->brandSn);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandCnName !== null) {
			
			$xfer += $output->writeFieldBegin('brandCnName');
			$xfer += $output->writeString($this->brandCnName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandEnName !== null) {
			
			$xfer += $output->writeFieldBegin('brandEnName');
			$xfer += $output->writeString($this->brandEnName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->limitedQuantity !== null) {
			
			$xfer += $output->writeFieldBegin('limitedQuantity');
			$xfer += $output->writeI32($this->limitedQuantity);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>