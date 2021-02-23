<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class CategoryProps {
	
	static $_TSPEC;
	public $pid = null;
	public $vid = null;
	public $alias = null;
	public $operationModes = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'pid'
			),
			2 => array(
			'var' => 'vid'
			),
			3 => array(
			'var' => 'alias'
			),
			4 => array(
			'var' => 'operationModes'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['pid'])){
				
				$this->pid = $vals['pid'];
			}
			
			
			if (isset($vals['vid'])){
				
				$this->vid = $vals['vid'];
			}
			
			
			if (isset($vals['alias'])){
				
				$this->alias = $vals['alias'];
			}
			
			
			if (isset($vals['operationModes'])){
				
				$this->operationModes = $vals['operationModes'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'CategoryProps';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("pid" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->pid); 
				
			}
			
			
			
			
			if ("vid" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->vid); 
				
			}
			
			
			
			
			if ("alias" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->alias);
				
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
		
		if($this->pid !== null) {
			
			$xfer += $output->writeFieldBegin('pid');
			$xfer += $output->writeI32($this->pid);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->vid !== null) {
			
			$xfer += $output->writeFieldBegin('vid');
			$xfer += $output->writeI32($this->vid);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->alias !== null) {
			
			$xfer += $output->writeFieldBegin('alias');
			$xfer += $output->writeString($this->alias);
			
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