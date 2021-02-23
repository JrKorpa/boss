<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace vipapis\inventory\admin;

class InvUpdateRetryRequest {
	
	static $_TSPEC;
	public $transId = null;
	public $batchNo = null;
	public $vendorId = null;
	public $cooperationNo = null;
	public $warehouse = null;
	public $barcode = null;
	public $vendorQuantity = null;
	public $cartQuantity = null;
	public $unpaidQuantity = null;
	public $sellableQuantity = null;
	public $isPos = null;
	public $createTime = null;
	public $retryTimes = null;
	public $areaWarehouseId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'transId'
			),
			2 => array(
			'var' => 'batchNo'
			),
			3 => array(
			'var' => 'vendorId'
			),
			4 => array(
			'var' => 'cooperationNo'
			),
			5 => array(
			'var' => 'warehouse'
			),
			6 => array(
			'var' => 'barcode'
			),
			7 => array(
			'var' => 'vendorQuantity'
			),
			8 => array(
			'var' => 'cartQuantity'
			),
			9 => array(
			'var' => 'unpaidQuantity'
			),
			10 => array(
			'var' => 'sellableQuantity'
			),
			11 => array(
			'var' => 'isPos'
			),
			12 => array(
			'var' => 'createTime'
			),
			13 => array(
			'var' => 'retryTimes'
			),
			14 => array(
			'var' => 'areaWarehouseId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['transId'])){
				
				$this->transId = $vals['transId'];
			}
			
			
			if (isset($vals['batchNo'])){
				
				$this->batchNo = $vals['batchNo'];
			}
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['cooperationNo'])){
				
				$this->cooperationNo = $vals['cooperationNo'];
			}
			
			
			if (isset($vals['warehouse'])){
				
				$this->warehouse = $vals['warehouse'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
			if (isset($vals['vendorQuantity'])){
				
				$this->vendorQuantity = $vals['vendorQuantity'];
			}
			
			
			if (isset($vals['cartQuantity'])){
				
				$this->cartQuantity = $vals['cartQuantity'];
			}
			
			
			if (isset($vals['unpaidQuantity'])){
				
				$this->unpaidQuantity = $vals['unpaidQuantity'];
			}
			
			
			if (isset($vals['sellableQuantity'])){
				
				$this->sellableQuantity = $vals['sellableQuantity'];
			}
			
			
			if (isset($vals['isPos'])){
				
				$this->isPos = $vals['isPos'];
			}
			
			
			if (isset($vals['createTime'])){
				
				$this->createTime = $vals['createTime'];
			}
			
			
			if (isset($vals['retryTimes'])){
				
				$this->retryTimes = $vals['retryTimes'];
			}
			
			
			if (isset($vals['areaWarehouseId'])){
				
				$this->areaWarehouseId = $vals['areaWarehouseId'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'InvUpdateRetryRequest';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("transId" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->transId);
				
			}
			
			
			
			
			if ("batchNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->batchNo);
				
			}
			
			
			
			
			if ("vendorId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->vendorId); 
				
			}
			
			
			
			
			if ("cooperationNo" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->cooperationNo); 
				
			}
			
			
			
			
			if ("warehouse" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->warehouse);
				
			}
			
			
			
			
			if ("barcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->barcode);
				
			}
			
			
			
			
			if ("vendorQuantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->vendorQuantity); 
				
			}
			
			
			
			
			if ("cartQuantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->cartQuantity); 
				
			}
			
			
			
			
			if ("unpaidQuantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->unpaidQuantity); 
				
			}
			
			
			
			
			if ("sellableQuantity" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->sellableQuantity); 
				
			}
			
			
			
			
			if ("isPos" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->isPos); 
				
			}
			
			
			
			
			if ("createTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->createTime); 
				
			}
			
			
			
			
			if ("retryTimes" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->retryTimes); 
				
			}
			
			
			
			
			if ("areaWarehouseId" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->areaWarehouseId);
				
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
		
		$xfer += $output->writeFieldBegin('transId');
		$xfer += $output->writeString($this->transId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('batchNo');
		$xfer += $output->writeString($this->batchNo);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI32($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('cooperationNo');
		$xfer += $output->writeI32($this->cooperationNo);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('warehouse');
		$xfer += $output->writeString($this->warehouse);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('barcode');
		$xfer += $output->writeString($this->barcode);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('vendorQuantity');
		$xfer += $output->writeI32($this->vendorQuantity);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('cartQuantity');
		$xfer += $output->writeI32($this->cartQuantity);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('unpaidQuantity');
		$xfer += $output->writeI32($this->unpaidQuantity);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('sellableQuantity');
		$xfer += $output->writeI32($this->sellableQuantity);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('isPos');
		$xfer += $output->writeI32($this->isPos);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('createTime');
		$xfer += $output->writeI64($this->createTime);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('retryTimes');
		$xfer += $output->writeI32($this->retryTimes);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->areaWarehouseId !== null) {
			
			$xfer += $output->writeFieldBegin('areaWarehouseId');
			$xfer += $output->writeString($this->areaWarehouseId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>