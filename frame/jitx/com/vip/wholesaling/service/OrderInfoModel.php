<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\wholesaling\service;

class OrderInfoModel {
	
	static $_TSPEC;
	public $orderSnapshotId = null;
	public $orderId = null;
	public $status = null;
	public $wholesaleId = null;
	public $wholesaleName = null;
	public $ownerId = null;
	public $ownerName = null;
	public $merchantId = null;
	public $merchantName = null;
	public $warehouseCode = null;
	public $warehouseName = null;
	public $applyTime = null;
	public $appliableOps = null;
	public $appliedOps = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'orderSnapshotId'
			),
			2 => array(
			'var' => 'orderId'
			),
			3 => array(
			'var' => 'status'
			),
			4 => array(
			'var' => 'wholesaleId'
			),
			5 => array(
			'var' => 'wholesaleName'
			),
			6 => array(
			'var' => 'ownerId'
			),
			7 => array(
			'var' => 'ownerName'
			),
			8 => array(
			'var' => 'merchantId'
			),
			9 => array(
			'var' => 'merchantName'
			),
			10 => array(
			'var' => 'warehouseCode'
			),
			11 => array(
			'var' => 'warehouseName'
			),
			12 => array(
			'var' => 'applyTime'
			),
			13 => array(
			'var' => 'appliableOps'
			),
			14 => array(
			'var' => 'appliedOps'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['orderSnapshotId'])){
				
				$this->orderSnapshotId = $vals['orderSnapshotId'];
			}
			
			
			if (isset($vals['orderId'])){
				
				$this->orderId = $vals['orderId'];
			}
			
			
			if (isset($vals['status'])){
				
				$this->status = $vals['status'];
			}
			
			
			if (isset($vals['wholesaleId'])){
				
				$this->wholesaleId = $vals['wholesaleId'];
			}
			
			
			if (isset($vals['wholesaleName'])){
				
				$this->wholesaleName = $vals['wholesaleName'];
			}
			
			
			if (isset($vals['ownerId'])){
				
				$this->ownerId = $vals['ownerId'];
			}
			
			
			if (isset($vals['ownerName'])){
				
				$this->ownerName = $vals['ownerName'];
			}
			
			
			if (isset($vals['merchantId'])){
				
				$this->merchantId = $vals['merchantId'];
			}
			
			
			if (isset($vals['merchantName'])){
				
				$this->merchantName = $vals['merchantName'];
			}
			
			
			if (isset($vals['warehouseCode'])){
				
				$this->warehouseCode = $vals['warehouseCode'];
			}
			
			
			if (isset($vals['warehouseName'])){
				
				$this->warehouseName = $vals['warehouseName'];
			}
			
			
			if (isset($vals['applyTime'])){
				
				$this->applyTime = $vals['applyTime'];
			}
			
			
			if (isset($vals['appliableOps'])){
				
				$this->appliableOps = $vals['appliableOps'];
			}
			
			
			if (isset($vals['appliedOps'])){
				
				$this->appliedOps = $vals['appliedOps'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'OrderInfoModel';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("orderSnapshotId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->orderSnapshotId); 
				
			}
			
			
			
			
			if ("orderId" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->orderId);
				
			}
			
			
			
			
			if ("status" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->status); 
				
			}
			
			
			
			
			if ("wholesaleId" == $schemeField){
				
				$needSkip = false;
				$input->readByte($this->wholesaleId); 
				
			}
			
			
			
			
			if ("wholesaleName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->wholesaleName);
				
			}
			
			
			
			
			if ("ownerId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->ownerId); 
				
			}
			
			
			
			
			if ("ownerName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->ownerName);
				
			}
			
			
			
			
			if ("merchantId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->merchantId); 
				
			}
			
			
			
			
			if ("merchantName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->merchantName);
				
			}
			
			
			
			
			if ("warehouseCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->warehouseCode);
				
			}
			
			
			
			
			if ("warehouseName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->warehouseName);
				
			}
			
			
			
			
			if ("applyTime" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->applyTime);
				
			}
			
			
			
			
			if ("appliableOps" == $schemeField){
				
				$needSkip = false;
				
				$this->appliableOps = array();
				$_size1 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem1 = null;
						
						$elem1 = new \com\vip\wholesaling\service\OperateModel();
						$elem1->read($input);
						
						$this->appliableOps[$_size1++] = $elem1;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
			}
			
			
			
			
			if ("appliedOps" == $schemeField){
				
				$needSkip = false;
				
				$this->appliedOps = array();
				$_size2 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem2 = null;
						
						$elem2 = new \com\vip\wholesaling\service\OperateModel();
						$elem2->read($input);
						
						$this->appliedOps[$_size2++] = $elem2;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
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
		
		if($this->orderSnapshotId !== null) {
			
			$xfer += $output->writeFieldBegin('orderSnapshotId');
			$xfer += $output->writeI64($this->orderSnapshotId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->orderId !== null) {
			
			$xfer += $output->writeFieldBegin('orderId');
			$xfer += $output->writeString($this->orderId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->status !== null) {
			
			$xfer += $output->writeFieldBegin('status');
			$xfer += $output->writeI32($this->status);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->wholesaleId !== null) {
			
			$xfer += $output->writeFieldBegin('wholesaleId');
			$xfer += $output->writeByte($this->wholesaleId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->wholesaleName !== null) {
			
			$xfer += $output->writeFieldBegin('wholesaleName');
			$xfer += $output->writeString($this->wholesaleName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->ownerId !== null) {
			
			$xfer += $output->writeFieldBegin('ownerId');
			$xfer += $output->writeI64($this->ownerId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->ownerName !== null) {
			
			$xfer += $output->writeFieldBegin('ownerName');
			$xfer += $output->writeString($this->ownerName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->merchantId !== null) {
			
			$xfer += $output->writeFieldBegin('merchantId');
			$xfer += $output->writeI64($this->merchantId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->merchantName !== null) {
			
			$xfer += $output->writeFieldBegin('merchantName');
			$xfer += $output->writeString($this->merchantName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->warehouseCode !== null) {
			
			$xfer += $output->writeFieldBegin('warehouseCode');
			$xfer += $output->writeString($this->warehouseCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->warehouseName !== null) {
			
			$xfer += $output->writeFieldBegin('warehouseName');
			$xfer += $output->writeString($this->warehouseName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->applyTime !== null) {
			
			$xfer += $output->writeFieldBegin('applyTime');
			$xfer += $output->writeString($this->applyTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->appliableOps !== null) {
			
			$xfer += $output->writeFieldBegin('appliableOps');
			
			if (!is_array($this->appliableOps)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->appliableOps as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->appliedOps !== null) {
			
			$xfer += $output->writeFieldBegin('appliedOps');
			
			if (!is_array($this->appliedOps)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->appliedOps as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>