<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class Attributes {
	
	static $_TSPEC;
	public $attriuteId = null;
	public $attributeName = null;
	public $dataType = null;
	public $flag = null;
	public $values = null;
	public $sort = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'attriuteId'
			),
			2 => array(
			'var' => 'attributeName'
			),
			3 => array(
			'var' => 'dataType'
			),
			4 => array(
			'var' => 'flag'
			),
			5 => array(
			'var' => 'values'
			),
			6 => array(
			'var' => 'sort'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['attriuteId'])){
				
				$this->attriuteId = $vals['attriuteId'];
			}
			
			
			if (isset($vals['attributeName'])){
				
				$this->attributeName = $vals['attributeName'];
			}
			
			
			if (isset($vals['dataType'])){
				
				$this->dataType = $vals['dataType'];
			}
			
			
			if (isset($vals['flag'])){
				
				$this->flag = $vals['flag'];
			}
			
			
			if (isset($vals['values'])){
				
				$this->values = $vals['values'];
			}
			
			
			if (isset($vals['sort'])){
				
				$this->sort = $vals['sort'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'Attributes';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("attriuteId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->attriuteId); 
				
			}
			
			
			
			
			if ("attributeName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->attributeName);
				
			}
			
			
			
			
			if ("dataType" == $schemeField){
				
				$needSkip = false;
				
				$names = \com\vip\product\gpdc\service\DataTypes::$__names;
				$name = null;
				$input->readString($name);
				foreach ($names as $k => $v){
					
					if($name == $v){
						
						$this->dataType = $k;
						break;
					}
					
				}
				
				
			}
			
			
			
			
			if ("flag" == $schemeField){
				
				$needSkip = false;
				$input->readByte($this->flag); 
				
			}
			
			
			
			
			if ("values" == $schemeField){
				
				$needSkip = false;
				
				$this->values = array();
				$_size0 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem0 = null;
						
						$elem0 = new \com\vip\product\gpdc\service\AttributeOptions();
						$elem0->read($input);
						
						$this->values[$_size0++] = $elem0;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
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
		
		if($this->attriuteId !== null) {
			
			$xfer += $output->writeFieldBegin('attriuteId');
			$xfer += $output->writeI32($this->attriuteId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->attributeName !== null) {
			
			$xfer += $output->writeFieldBegin('attributeName');
			$xfer += $output->writeString($this->attributeName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->dataType !== null) {
			
			$xfer += $output->writeFieldBegin('dataType');
			
			$em = new \com\vip\product\gpdc\service\DataTypes; 
			$output->writeString($em::$__names[$this->dataType]);  
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('flag');
		$xfer += $output->writeByte($this->flag);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->values !== null) {
			
			$xfer += $output->writeFieldBegin('values');
			
			if (!is_array($this->values)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->values as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
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