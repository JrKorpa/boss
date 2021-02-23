<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\order\common\pojo\order\vo;

class OrderBizExtAttrVO {
	
	static $_TSPEC;
	public $customBodyId = null;
	public $customId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'customBodyId'
			),
			2 => array(
			'var' => 'customId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['customBodyId'])){
				
				$this->customBodyId = $vals['customBodyId'];
			}
			
			
			if (isset($vals['customId'])){
				
				$this->customId = $vals['customId'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'OrderBizExtAttrVO';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("customBodyId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->customBodyId); 
				
			}
			
			
			
			
			if ("customId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->customId); 
				
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
		
		if($this->customBodyId !== null) {
			
			$xfer += $output->writeFieldBegin('customBodyId');
			$xfer += $output->writeI64($this->customBodyId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->customId !== null) {
			
			$xfer += $output->writeFieldBegin('customId');
			$xfer += $output->writeI64($this->customId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>