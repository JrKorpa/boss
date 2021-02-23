<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\order;

class OrderSequence {
	
	static $_TSPEC;
	public $nextId = null;
	public $name = null;
	public $lastUpdateTime = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'nextId'
			),
			2 => array(
			'var' => 'name'
			),
			3 => array(
			'var' => 'lastUpdateTime'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['nextId'])){
				
				$this->nextId = $vals['nextId'];
			}
			
			
			if (isset($vals['name'])){
				
				$this->name = $vals['name'];
			}
			
			
			if (isset($vals['lastUpdateTime'])){
				
				$this->lastUpdateTime = $vals['lastUpdateTime'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'OrderSequence';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("nextId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->nextId); 
				
			}
			
			
			
			
			if ("name" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->name);
				
			}
			
			
			
			
			if ("lastUpdateTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->lastUpdateTime);
				
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
		
		if($this->nextId !== null) {
			
			$xfer += $output->writeFieldBegin('nextId');
			$xfer += $output->writeI32($this->nextId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->name !== null) {
			
			$xfer += $output->writeFieldBegin('name');
			$xfer += $output->writeString($this->name);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->lastUpdateTime !== null) {
			
			$xfer += $output->writeFieldBegin('lastUpdateTime');
			$xfer += $output->writeI64($this->lastUpdateTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>