<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class VpdcToGpdcSyncInfo {
	
	static $_TSPEC;
	public $owner = null;
	public $barcode = null;
	public $syncResult = null;
	public $errorMessage = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'barcode'
			),
			3 => array(
			'var' => 'syncResult'
			),
			4 => array(
			'var' => 'errorMessage'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
			if (isset($vals['syncResult'])){
				
				$this->syncResult = $vals['syncResult'];
			}
			
			
			if (isset($vals['errorMessage'])){
				
				$this->errorMessage = $vals['errorMessage'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'VpdcToGpdcSyncInfo';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("owner" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->owner);
				
			}
			
			
			
			
			if ("barcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->barcode);
				
			}
			
			
			
			
			if ("syncResult" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->syncResult); 
				
			}
			
			
			
			
			if ("errorMessage" == $schemeField){
				
				$needSkip = false;
				
				$this->errorMessage = new \com\vip\product\gpdc\service\ErrorMessage();
				$this->errorMessage->read($input);
				
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
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcode !== null) {
			
			$xfer += $output->writeFieldBegin('barcode');
			$xfer += $output->writeString($this->barcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('syncResult');
		$xfer += $output->writeI32($this->syncResult);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->errorMessage !== null) {
			
			$xfer += $output->writeFieldBegin('errorMessage');
			
			if (!is_object($this->errorMessage)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->errorMessage->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>