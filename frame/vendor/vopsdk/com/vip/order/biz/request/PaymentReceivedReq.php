<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\order\biz\request;

class PaymentReceivedReq {
	
	static $_TSPEC;
	public $userId = null;
	public $orderId = null;
	public $isPaySuccess = null;
	public $hasPriv = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'userId'
			),
			2 => array(
			'var' => 'orderId'
			),
			3 => array(
			'var' => 'isPaySuccess'
			),
			4 => array(
			'var' => 'hasPriv'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['userId'])){
				
				$this->userId = $vals['userId'];
			}
			
			
			if (isset($vals['orderId'])){
				
				$this->orderId = $vals['orderId'];
			}
			
			
			if (isset($vals['isPaySuccess'])){
				
				$this->isPaySuccess = $vals['isPaySuccess'];
			}
			
			
			if (isset($vals['hasPriv'])){
				
				$this->hasPriv = $vals['hasPriv'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'PaymentReceivedReq';
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
			
			
			
			
			if ("orderId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->orderId); 
				
			}
			
			
			
			
			if ("isPaySuccess" == $schemeField){
				
				$needSkip = false;
				$input->readBool($this->isPaySuccess);
				
			}
			
			
			
			
			if ("hasPriv" == $schemeField){
				
				$needSkip = false;
				$input->readBool($this->hasPriv);
				
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
		
		
		if($this->orderId !== null) {
			
			$xfer += $output->writeFieldBegin('orderId');
			$xfer += $output->writeI64($this->orderId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isPaySuccess !== null) {
			
			$xfer += $output->writeFieldBegin('isPaySuccess');
			$xfer += $output->writeBool($this->isPaySuccess);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->hasPriv !== null) {
			
			$xfer += $output->writeFieldBegin('hasPriv');
			$xfer += $output->writeBool($this->hasPriv);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>