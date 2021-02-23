<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\fcs\vei\service;

class ExternalInvoiceHandleState {
	
	static $_TSPEC;
	public $state = null;
	public $fpdm = null;
	public $fphm = null;
	public $orderSn = null;
	public $retMsg = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'state'
			),
			2 => array(
			'var' => 'fpdm'
			),
			3 => array(
			'var' => 'fphm'
			),
			4 => array(
			'var' => 'orderSn'
			),
			5 => array(
			'var' => 'retMsg'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['state'])){
				
				$this->state = $vals['state'];
			}
			
			
			if (isset($vals['fpdm'])){
				
				$this->fpdm = $vals['fpdm'];
			}
			
			
			if (isset($vals['fphm'])){
				
				$this->fphm = $vals['fphm'];
			}
			
			
			if (isset($vals['orderSn'])){
				
				$this->orderSn = $vals['orderSn'];
			}
			
			
			if (isset($vals['retMsg'])){
				
				$this->retMsg = $vals['retMsg'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'ExternalInvoiceHandleState';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("state" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->state);
				
			}
			
			
			
			
			if ("fpdm" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->fpdm);
				
			}
			
			
			
			
			if ("fphm" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->fphm);
				
			}
			
			
			
			
			if ("orderSn" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->orderSn);
				
			}
			
			
			
			
			if ("retMsg" == $schemeField){
				
				$needSkip = false;
				
				$this->retMsg = new \com\vip\fcs\vei\service\BaseRetMsg();
				$this->retMsg->read($input);
				
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
		
		$xfer += $output->writeFieldBegin('state');
		$xfer += $output->writeString($this->state);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->fpdm !== null) {
			
			$xfer += $output->writeFieldBegin('fpdm');
			$xfer += $output->writeString($this->fpdm);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->fphm !== null) {
			
			$xfer += $output->writeFieldBegin('fphm');
			$xfer += $output->writeString($this->fphm);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('orderSn');
		$xfer += $output->writeString($this->orderSn);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('retMsg');
		
		if (!is_object($this->retMsg)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->retMsg->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>