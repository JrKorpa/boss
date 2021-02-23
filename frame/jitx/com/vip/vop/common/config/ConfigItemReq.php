<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\common\config;

class ConfigItemReq {
	
	static $_TSPEC;
	public $domainName = null;
	public $moduleName = null;
	public $configItemCode = null;
	public $configItemValue = null;
	public $configItemName = null;
	public $ordinal = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'domainName'
			),
			2 => array(
			'var' => 'moduleName'
			),
			3 => array(
			'var' => 'configItemCode'
			),
			4 => array(
			'var' => 'configItemValue'
			),
			5 => array(
			'var' => 'configItemName'
			),
			6 => array(
			'var' => 'ordinal'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['domainName'])){
				
				$this->domainName = $vals['domainName'];
			}
			
			
			if (isset($vals['moduleName'])){
				
				$this->moduleName = $vals['moduleName'];
			}
			
			
			if (isset($vals['configItemCode'])){
				
				$this->configItemCode = $vals['configItemCode'];
			}
			
			
			if (isset($vals['configItemValue'])){
				
				$this->configItemValue = $vals['configItemValue'];
			}
			
			
			if (isset($vals['configItemName'])){
				
				$this->configItemName = $vals['configItemName'];
			}
			
			
			if (isset($vals['ordinal'])){
				
				$this->ordinal = $vals['ordinal'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'ConfigItemReq';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("domainName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->domainName);
				
			}
			
			
			
			
			if ("moduleName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->moduleName);
				
			}
			
			
			
			
			if ("configItemCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->configItemCode);
				
			}
			
			
			
			
			if ("configItemValue" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->configItemValue);
				
			}
			
			
			
			
			if ("configItemName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->configItemName);
				
			}
			
			
			
			
			if ("ordinal" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->ordinal); 
				
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
		
		$xfer += $output->writeFieldBegin('domainName');
		$xfer += $output->writeString($this->domainName);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('moduleName');
		$xfer += $output->writeString($this->moduleName);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('configItemCode');
		$xfer += $output->writeString($this->configItemCode);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('configItemValue');
		$xfer += $output->writeString($this->configItemValue);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('configItemName');
		$xfer += $output->writeString($this->configItemName);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('ordinal');
		$xfer += $output->writeI32($this->ordinal);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>