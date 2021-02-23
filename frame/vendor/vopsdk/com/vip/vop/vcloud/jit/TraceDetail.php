<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\jit;

class TraceDetail {
	
	static $_TSPEC;
	public $transportCode = null;
	public $transportDetail = null;
	public $transportTime = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'transportCode'
			),
			2 => array(
			'var' => 'transportDetail'
			),
			3 => array(
			'var' => 'transportTime'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['transportCode'])){
				
				$this->transportCode = $vals['transportCode'];
			}
			
			
			if (isset($vals['transportDetail'])){
				
				$this->transportDetail = $vals['transportDetail'];
			}
			
			
			if (isset($vals['transportTime'])){
				
				$this->transportTime = $vals['transportTime'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'TraceDetail';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("transportCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->transportCode);
				
			}
			
			
			
			
			if ("transportDetail" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->transportDetail);
				
			}
			
			
			
			
			if ("transportTime" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->transportTime);
				
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
		
		if($this->transportCode !== null) {
			
			$xfer += $output->writeFieldBegin('transportCode');
			$xfer += $output->writeString($this->transportCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->transportDetail !== null) {
			
			$xfer += $output->writeFieldBegin('transportDetail');
			$xfer += $output->writeString($this->transportDetail);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->transportTime !== null) {
			
			$xfer += $output->writeFieldBegin('transportTime');
			$xfer += $output->writeString($this->transportTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>