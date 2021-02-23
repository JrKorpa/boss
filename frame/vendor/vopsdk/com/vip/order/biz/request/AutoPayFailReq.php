<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\order\biz\request;

class AutoPayFailReq {
	
	static $_TSPEC;
	public $userId = null;
	public $orderSn = null;
	public $failCode = null;
	public $failMsg = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'userId'
			),
			2 => array(
			'var' => 'orderSn'
			),
			3 => array(
			'var' => 'failCode'
			),
			4 => array(
			'var' => 'failMsg'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['userId'])){
				
				$this->userId = $vals['userId'];
			}
			
			
			if (isset($vals['orderSn'])){
				
				$this->orderSn = $vals['orderSn'];
			}
			
			
			if (isset($vals['failCode'])){
				
				$this->failCode = $vals['failCode'];
			}
			
			
			if (isset($vals['failMsg'])){
				
				$this->failMsg = $vals['failMsg'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'AutoPayFailReq';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("userId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->userId); 
				
			}
			
			
			
			
			if ("orderSn" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->orderSn);
				
			}
			
			
			
			
			if ("failCode" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->failCode); 
				
			}
			
			
			
			
			if ("failMsg" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->failMsg);
				
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
		
		if($this->userId !== null) {
			
			$xfer += $output->writeFieldBegin('userId');
			$xfer += $output->writeI64($this->userId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->orderSn !== null) {
			
			$xfer += $output->writeFieldBegin('orderSn');
			$xfer += $output->writeString($this->orderSn);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->failCode !== null) {
			
			$xfer += $output->writeFieldBegin('failCode');
			$xfer += $output->writeI32($this->failCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->failMsg !== null) {
			
			$xfer += $output->writeFieldBegin('failMsg');
			$xfer += $output->writeString($this->failMsg);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>