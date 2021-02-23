<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\common\config;

class ConfigItem {
	
	static $_TSPEC;
	public $configItemCode = null;
	public $configItemName = null;
	public $configItemValue = null;
	public $configItemOrdinal = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'configItemCode'
			),
			2 => array(
			'var' => 'configItemName'
			),
			3 => array(
			'var' => 'configItemValue'
			),
			4 => array(
			'var' => 'configItemOrdinal'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['configItemCode'])){
				
				$this->configItemCode = $vals['configItemCode'];
			}
			
			
			if (isset($vals['configItemName'])){
				
				$this->configItemName = $vals['configItemName'];
			}
			
			
			if (isset($vals['configItemValue'])){
				
				$this->configItemValue = $vals['configItemValue'];
			}
			
			
			if (isset($vals['configItemOrdinal'])){
				
				$this->configItemOrdinal = $vals['configItemOrdinal'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'ConfigItem';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("configItemCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->configItemCode);
				
			}
			
			
			
			
			if ("configItemName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->configItemName);
				
			}
			
			
			
			
			if ("configItemValue" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->configItemValue);
				
			}
			
			
			
			
			if ("configItemOrdinal" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->configItemOrdinal); 
				
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
		
		if($this->configItemCode !== null) {
			
			$xfer += $output->writeFieldBegin('configItemCode');
			$xfer += $output->writeString($this->configItemCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->configItemName !== null) {
			
			$xfer += $output->writeFieldBegin('configItemName');
			$xfer += $output->writeString($this->configItemName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->configItemValue !== null) {
			
			$xfer += $output->writeFieldBegin('configItemValue');
			$xfer += $output->writeString($this->configItemValue);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('configItemOrdinal');
		$xfer += $output->writeI32($this->configItemOrdinal);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>