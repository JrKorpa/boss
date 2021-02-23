<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\jit;

class GetDeliveryListRequest {
	
	static $_TSPEC;
	public $vendorId = null;
	public $poNo = null;
	public $storageNo = null;
	public $vipWarehouse = null;
	public $outFlag = null;
	public $outTimeFrom = null;
	public $outTimeTo = null;
	public $arrivalTimeFrom = null;
	public $arrivalTimeTo = null;
	public $realArrivalTimeFrom = null;
	public $realArrivalTimeTo = null;
	public $erpWarehouse = null;
	public $pagination = null;
	public $deliveryTimeFrom = null;
	public $deliveryTimeTo = null;
	public $needPush = null;
	public $userId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'poNo'
			),
			3 => array(
			'var' => 'storageNo'
			),
			4 => array(
			'var' => 'vipWarehouse'
			),
			5 => array(
			'var' => 'outFlag'
			),
			6 => array(
			'var' => 'outTimeFrom'
			),
			7 => array(
			'var' => 'outTimeTo'
			),
			8 => array(
			'var' => 'arrivalTimeFrom'
			),
			9 => array(
			'var' => 'arrivalTimeTo'
			),
			10 => array(
			'var' => 'realArrivalTimeFrom'
			),
			11 => array(
			'var' => 'realArrivalTimeTo'
			),
			12 => array(
			'var' => 'erpWarehouse'
			),
			13 => array(
			'var' => 'pagination'
			),
			14 => array(
			'var' => 'deliveryTimeFrom'
			),
			15 => array(
			'var' => 'deliveryTimeTo'
			),
			16 => array(
			'var' => 'needPush'
			),
			17 => array(
			'var' => 'userId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['poNo'])){
				
				$this->poNo = $vals['poNo'];
			}
			
			
			if (isset($vals['storageNo'])){
				
				$this->storageNo = $vals['storageNo'];
			}
			
			
			if (isset($vals['vipWarehouse'])){
				
				$this->vipWarehouse = $vals['vipWarehouse'];
			}
			
			
			if (isset($vals['outFlag'])){
				
				$this->outFlag = $vals['outFlag'];
			}
			
			
			if (isset($vals['outTimeFrom'])){
				
				$this->outTimeFrom = $vals['outTimeFrom'];
			}
			
			
			if (isset($vals['outTimeTo'])){
				
				$this->outTimeTo = $vals['outTimeTo'];
			}
			
			
			if (isset($vals['arrivalTimeFrom'])){
				
				$this->arrivalTimeFrom = $vals['arrivalTimeFrom'];
			}
			
			
			if (isset($vals['arrivalTimeTo'])){
				
				$this->arrivalTimeTo = $vals['arrivalTimeTo'];
			}
			
			
			if (isset($vals['realArrivalTimeFrom'])){
				
				$this->realArrivalTimeFrom = $vals['realArrivalTimeFrom'];
			}
			
			
			if (isset($vals['realArrivalTimeTo'])){
				
				$this->realArrivalTimeTo = $vals['realArrivalTimeTo'];
			}
			
			
			if (isset($vals['erpWarehouse'])){
				
				$this->erpWarehouse = $vals['erpWarehouse'];
			}
			
			
			if (isset($vals['pagination'])){
				
				$this->pagination = $vals['pagination'];
			}
			
			
			if (isset($vals['deliveryTimeFrom'])){
				
				$this->deliveryTimeFrom = $vals['deliveryTimeFrom'];
			}
			
			
			if (isset($vals['deliveryTimeTo'])){
				
				$this->deliveryTimeTo = $vals['deliveryTimeTo'];
			}
			
			
			if (isset($vals['needPush'])){
				
				$this->needPush = $vals['needPush'];
			}
			
			
			if (isset($vals['userId'])){
				
				$this->userId = $vals['userId'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GetDeliveryListRequest';
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
			
			
			
			
			if ("poNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->poNo);
				
			}
			
			
			
			
			if ("storageNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->storageNo);
				
			}
			
			
			
			
			if ("vipWarehouse" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->vipWarehouse);
				
			}
			
			
			
			
			if ("outFlag" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->outFlag); 
				
			}
			
			
			
			
			if ("outTimeFrom" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->outTimeFrom);
				
			}
			
			
			
			
			if ("outTimeTo" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->outTimeTo);
				
			}
			
			
			
			
			if ("arrivalTimeFrom" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->arrivalTimeFrom);
				
			}
			
			
			
			
			if ("arrivalTimeTo" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->arrivalTimeTo);
				
			}
			
			
			
			
			if ("realArrivalTimeFrom" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->realArrivalTimeFrom);
				
			}
			
			
			
			
			if ("realArrivalTimeTo" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->realArrivalTimeTo);
				
			}
			
			
			
			
			if ("erpWarehouse" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->erpWarehouse);
				
			}
			
			
			
			
			if ("pagination" == $schemeField){
				
				$needSkip = false;
				
				$this->pagination = new \com\vip\vop\vcloud\common\api\Pagination();
				$this->pagination->read($input);
				
			}
			
			
			
			
			if ("deliveryTimeFrom" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->deliveryTimeFrom);
				
			}
			
			
			
			
			if ("deliveryTimeTo" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->deliveryTimeTo);
				
			}
			
			
			
			
			if ("needPush" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->needPush); 
				
			}
			
			
			
			
			if ("userId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->userId); 
				
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
		
		
		if($this->poNo !== null) {
			
			$xfer += $output->writeFieldBegin('poNo');
			$xfer += $output->writeString($this->poNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->storageNo !== null) {
			
			$xfer += $output->writeFieldBegin('storageNo');
			$xfer += $output->writeString($this->storageNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->vipWarehouse !== null) {
			
			$xfer += $output->writeFieldBegin('vipWarehouse');
			$xfer += $output->writeString($this->vipWarehouse);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->outFlag !== null) {
			
			$xfer += $output->writeFieldBegin('outFlag');
			$xfer += $output->writeI32($this->outFlag);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->outTimeFrom !== null) {
			
			$xfer += $output->writeFieldBegin('outTimeFrom');
			$xfer += $output->writeI64($this->outTimeFrom);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->outTimeTo !== null) {
			
			$xfer += $output->writeFieldBegin('outTimeTo');
			$xfer += $output->writeI64($this->outTimeTo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->arrivalTimeFrom !== null) {
			
			$xfer += $output->writeFieldBegin('arrivalTimeFrom');
			$xfer += $output->writeI64($this->arrivalTimeFrom);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->arrivalTimeTo !== null) {
			
			$xfer += $output->writeFieldBegin('arrivalTimeTo');
			$xfer += $output->writeI64($this->arrivalTimeTo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->realArrivalTimeFrom !== null) {
			
			$xfer += $output->writeFieldBegin('realArrivalTimeFrom');
			$xfer += $output->writeI64($this->realArrivalTimeFrom);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->realArrivalTimeTo !== null) {
			
			$xfer += $output->writeFieldBegin('realArrivalTimeTo');
			$xfer += $output->writeI64($this->realArrivalTimeTo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->erpWarehouse !== null) {
			
			$xfer += $output->writeFieldBegin('erpWarehouse');
			$xfer += $output->writeString($this->erpWarehouse);
			
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
		
		
		if($this->deliveryTimeFrom !== null) {
			
			$xfer += $output->writeFieldBegin('deliveryTimeFrom');
			$xfer += $output->writeI64($this->deliveryTimeFrom);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->deliveryTimeTo !== null) {
			
			$xfer += $output->writeFieldBegin('deliveryTimeTo');
			$xfer += $output->writeI64($this->deliveryTimeTo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->needPush !== null) {
			
			$xfer += $output->writeFieldBegin('needPush');
			$xfer += $output->writeI32($this->needPush);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('userId');
		$xfer += $output->writeI64($this->userId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>