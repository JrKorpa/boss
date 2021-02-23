<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\jit;

class GetDeliveryGoodsRequest {
	
	static $_TSPEC;
	public $vendorId = null;
	public $storageNo = null;
	public $pagination = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'storageNo'
			),
			3 => array(
			'var' => 'pagination'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['storageNo'])){
				
				$this->storageNo = $vals['storageNo'];
			}
			
			
			if (isset($vals['pagination'])){
				
				$this->pagination = $vals['pagination'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GetDeliveryGoodsRequest';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("vendorId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->vendorId); 
				
			}
			
			
			
			
			if ("storageNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->storageNo);
				
			}
			
			
			
			
			if ("pagination" == $schemeField){
				
				$needSkip = false;
				
				$this->pagination = new \com\vip\vop\vcloud\common\api\Pagination();
				$this->pagination->read($input);
				
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
		
		if($this->vendorId !== null) {
			
			$xfer += $output->writeFieldBegin('vendorId');
			$xfer += $output->writeI32($this->vendorId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->storageNo !== null) {
			
			$xfer += $output->writeFieldBegin('storageNo');
			$xfer += $output->writeString($this->storageNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->pagination !== null) {
			
			$xfer += $output->writeFieldBegin('pagination');
			
			if (!is_object($this->pagination)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->pagination->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>