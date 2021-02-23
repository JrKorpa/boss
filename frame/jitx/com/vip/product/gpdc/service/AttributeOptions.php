<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class AttributeOptions {
	
	static $_TSPEC;
	public $optionId = null;
	public $optionName = null;
	public $optionAliases = null;
	public $literal = null;
	public $flag = null;
	public $sort = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'optionId'
			),
			2 => array(
			'var' => 'optionName'
			),
			3 => array(
			'var' => 'optionAliases'
			),
			4 => array(
			'var' => 'literal'
			),
			5 => array(
			'var' => 'flag'
			),
			6 => array(
			'var' => 'sort'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['optionId'])){
				
				$this->optionId = $vals['optionId'];
			}
			
			
			if (isset($vals['optionName'])){
				
				$this->optionName = $vals['optionName'];
			}
			
			
			if (isset($vals['optionAliases'])){
				
				$this->optionAliases = $vals['optionAliases'];
			}
			
			
			if (isset($vals['literal'])){
				
				$this->literal = $vals['literal'];
			}
			
			
			if (isset($vals['flag'])){
				
				$this->flag = $vals['flag'];
			}
			
			
			if (isset($vals['sort'])){
				
				$this->sort = $vals['sort'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'AttributeOptions';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("optionId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->optionId); 
				
			}
			
			
			
			
			if ("optionName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->optionName);
				
			}
			
			
			
			
			if ("optionAliases" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->optionAliases);
				
			}
			
			
			
			
			if ("literal" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->literal);
				
			}
			
			
			
			
			if ("flag" == $schemeField){
				
				$needSkip = false;
				$input->readByte($this->flag); 
				
			}
			
			
			
			
			if ("sort" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->sort); 
				
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
		
		if($this->optionId !== null) {
			
			$xfer += $output->writeFieldBegin('optionId');
			$xfer += $output->writeI32($this->optionId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->optionName !== null) {
			
			$xfer += $output->writeFieldBegin('optionName');
			$xfer += $output->writeString($this->optionName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->optionAliases !== null) {
			
			$xfer += $output->writeFieldBegin('optionAliases');
			$xfer += $output->writeString($this->optionAliases);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->literal !== null) {
			
			$xfer += $output->writeFieldBegin('literal');
			$xfer += $output->writeString($this->literal);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('flag');
		$xfer += $output->writeByte($this->flag);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->sort !== null) {
			
			$xfer += $output->writeFieldBegin('sort');
			$xfer += $output->writeI32($this->sort);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>