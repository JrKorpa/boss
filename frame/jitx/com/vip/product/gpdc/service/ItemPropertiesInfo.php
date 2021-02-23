<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class ItemPropertiesInfo {
	
	static $_TSPEC;
	public $itemId = null;
	public $name = null;
	public $value = null;
	public $propIndex = null;
	public $operationModes = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'itemId'
			),
			2 => array(
			'var' => 'name'
			),
			3 => array(
			'var' => 'value'
			),
			4 => array(
			'var' => 'propIndex'
			),
			5 => array(
			'var' => 'operationModes'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['itemId'])){
				
				$this->itemId = $vals['itemId'];
			}
			
			
			if (isset($vals['name'])){
				
				$this->name = $vals['name'];
			}
			
			
			if (isset($vals['value'])){
				
				$this->value = $vals['value'];
			}
			
			
			if (isset($vals['propIndex'])){
				
				$this->propIndex = $vals['propIndex'];
			}
			
			
			if (isset($vals['operationModes'])){
				
				$this->operationModes = $vals['operationModes'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'ItemPropertiesInfo';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("itemId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->itemId); 
				
			}
			
			
			
			
			if ("name" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->name);
				
			}
			
			
			
			
			if ("value" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->value);
				
			}
			
			
			
			
			if ("propIndex" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->propIndex); 
				
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
		
		if($this->itemId !== null) {
			
			$xfer += $output->writeFieldBegin('itemId');
			$xfer += $output->writeI64($this->itemId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->name !== null) {
			
			$xfer += $output->writeFieldBegin('name');
			$xfer += $output->writeString($this->name);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->value !== null) {
			
			$xfer += $output->writeFieldBegin('value');
			$xfer += $output->writeString($this->value);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->propIndex !== null) {
			
			$xfer += $output->writeFieldBegin('propIndex');
			$xfer += $output->writeI32($this->propIndex);
			
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