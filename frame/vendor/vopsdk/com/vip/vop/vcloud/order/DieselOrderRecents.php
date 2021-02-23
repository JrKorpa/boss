<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\order;

class DieselOrderRecents {
	
	static $_TSPEC;
	public $type = null;
	public $incrementId = null;
	public $orderIncrementId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'type'
			),
			2 => array(
			'var' => 'incrementId'
			),
			3 => array(
			'var' => 'orderIncrementId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['type'])){
				
				$this->type = $vals['type'];
			}
			
			
			if (isset($vals['incrementId'])){
				
				$this->incrementId = $vals['incrementId'];
			}
			
			
			if (isset($vals['orderIncrementId'])){
				
				$this->orderIncrementId = $vals['orderIncrementId'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'DieselOrderRecents';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("type" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->type);
				
			}
			
			
			
			
			if ("incrementId" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->incrementId);
				
			}
			
			
			
			
			if ("orderIncrementId" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->orderIncrementId);
				
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
		
		$xfer += $output->writeFieldBegin('type');
		$xfer += $output->writeString($this->type);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('incrementId');
		$xfer += $output->writeString($this->incrementId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('orderIncrementId');
		$xfer += $output->writeString($this->orderIncrementId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>