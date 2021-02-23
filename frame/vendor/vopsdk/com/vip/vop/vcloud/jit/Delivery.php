<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\jit;

class Delivery {
	
	static $_TSPEC;
	public $vendorId = null;
	public $vendorName = null;
	public $poNo = null;
	public $storageNo = null;
	public $deliveryNo = null;
	public $vipWarehouse = null;
	public $deliveryMethod = null;
	public $deliveryTime = null;
	public $carrierCode = null;
	public $carrierName = null;
	public $outTime = null;
	public $outFlag = null;
	public $createTime = null;
	public $arrivalTime = null;
	public $realArrivalTime = null;
	public $raceTime = null;
	public $realRaceTime = null;
	public $outFlagDesc = null;
	public $erpWarehouse = null;
	public $brandName = null;
	public $totalDeliveryNum = null;
	public $totalBox = null;
	public $deliveryMethodDesc = null;
	public $id = null;
	public $isSubmit = null;
	public $po = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'vendorName'
			),
			3 => array(
			'var' => 'poNo'
			),
			4 => array(
			'var' => 'storageNo'
			),
			5 => array(
			'var' => 'deliveryNo'
			),
			6 => array(
			'var' => 'vipWarehouse'
			),
			7 => array(
			'var' => 'deliveryMethod'
			),
			8 => array(
			'var' => 'deliveryTime'
			),
			9 => array(
			'var' => 'carrierCode'
			),
			10 => array(
			'var' => 'carrierName'
			),
			11 => array(
			'var' => 'outTime'
			),
			12 => array(
			'var' => 'outFlag'
			),
			13 => array(
			'var' => 'createTime'
			),
			14 => array(
			'var' => 'arrivalTime'
			),
			15 => array(
			'var' => 'realArrivalTime'
			),
			16 => array(
			'var' => 'raceTime'
			),
			17 => array(
			'var' => 'realRaceTime'
			),
			18 => array(
			'var' => 'outFlagDesc'
			),
			19 => array(
			'var' => 'erpWarehouse'
			),
			20 => array(
			'var' => 'brandName'
			),
			21 => array(
			'var' => 'totalDeliveryNum'
			),
			22 => array(
			'var' => 'totalBox'
			),
			23 => array(
			'var' => 'deliveryMethodDesc'
			),
			24 => array(
			'var' => 'id'
			),
			25 => array(
			'var' => 'isSubmit'
			),
			26 => array(
			'var' => 'po'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['vendorName'])){
				
				$this->vendorName = $vals['vendorName'];
			}
			
			
			if (isset($vals['poNo'])){
				
				$this->poNo = $vals['poNo'];
			}
			
			
			if (isset($vals['storageNo'])){
				
				$this->storageNo = $vals['storageNo'];
			}
			
			
			if (isset($vals['deliveryNo'])){
				
				$this->deliveryNo = $vals['deliveryNo'];
			}
			
			
			if (isset($vals['vipWarehouse'])){
				
				$this->vipWarehouse = $vals['vipWarehouse'];
			}
			
			
			if (isset($vals['deliveryMethod'])){
				
				$this->deliveryMethod = $vals['deliveryMethod'];
			}
			
			
			if (isset($vals['deliveryTime'])){
				
				$this->deliveryTime = $vals['deliveryTime'];
			}
			
			
			if (isset($vals['carrierCode'])){
				
				$this->carrierCode = $vals['carrierCode'];
			}
			
			
			if (isset($vals['carrierName'])){
				
				$this->carrierName = $vals['carrierName'];
			}
			
			
			if (isset($vals['outTime'])){
				
				$this->outTime = $vals['outTime'];
			}
			
			
			if (isset($vals['outFlag'])){
				
				$this->outFlag = $vals['outFlag'];
			}
			
			
			if (isset($vals['createTime'])){
				
				$this->createTime = $vals['createTime'];
			}
			
			
			if (isset($vals['arrivalTime'])){
				
				$this->arrivalTime = $vals['arrivalTime'];
			}
			
			
			if (isset($vals['realArrivalTime'])){
				
				$this->realArrivalTime = $vals['realArrivalTime'];
			}
			
			
			if (isset($vals['raceTime'])){
				
				$this->raceTime = $vals['raceTime'];
			}
			
			
			if (isset($vals['realRaceTime'])){
				
				$this->realRaceTime = $vals['realRaceTime'];
			}
			
			
			if (isset($vals['outFlagDesc'])){
				
				$this->outFlagDesc = $vals['outFlagDesc'];
			}
			
			
			if (isset($vals['erpWarehouse'])){
				
				$this->erpWarehouse = $vals['erpWarehouse'];
			}
			
			
			if (isset($vals['brandName'])){
				
				$this->brandName = $vals['brandName'];
			}
			
			
			if (isset($vals['totalDeliveryNum'])){
				
				$this->totalDeliveryNum = $vals['totalDeliveryNum'];
			}
			
			
			if (isset($vals['totalBox'])){
				
				$this->totalBox = $vals['totalBox'];
			}
			
			
			if (isset($vals['deliveryMethodDesc'])){
				
				$this->deliveryMethodDesc = $vals['deliveryMethodDesc'];
			}
			
			
			if (isset($vals['id'])){
				
				$this->id = $vals['id'];
			}
			
			
			if (isset($vals['isSubmit'])){
				
				$this->isSubmit = $vals['isSubmit'];
			}
			
			
			if (isset($vals['po'])){
				
				$this->po = $vals['po'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'Delivery';
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
			
			
			
			
			if ("vendorName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->vendorName);
				
			}
			
			
			
			
			if ("poNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->poNo);
				
			}
			
			
			
			
			if ("storageNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->storageNo);
				
			}
			
			
			
			
			if ("deliveryNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->deliveryNo);
				
			}
			
			
			
			
			if ("vipWarehouse" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->vipWarehouse);
				
			}
			
			
			
			
			if ("deliveryMethod" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->deliveryMethod); 
				
			}
			
			
			
			
			if ("deliveryTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->deliveryTime);
				
			}
			
			
			
			
			if ("carrierCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->carrierCode);
				
			}
			
			
			
			
			if ("carrierName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->carrierName);
				
			}
			
			
			
			
			if ("outTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->outTime);
				
			}
			
			
			
			
			if ("outFlag" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->outFlag); 
				
			}
			
			
			
			
			if ("createTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->createTime);
				
			}
			
			
			
			
			if ("arrivalTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->arrivalTime);
				
			}
			
			
			
			
			if ("realArrivalTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->realArrivalTime);
				
			}
			
			
			
			
			if ("raceTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->raceTime);
				
			}
			
			
			
			
			if ("realRaceTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->realRaceTime);
				
			}
			
			
			
			
			if ("outFlagDesc" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->outFlagDesc);
				
			}
			
			
			
			
			if ("erpWarehouse" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->erpWarehouse);
				
			}
			
			
			
			
			if ("brandName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->brandName);
				
			}
			
			
			
			
			if ("totalDeliveryNum" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->totalDeliveryNum); 
				
			}
			
			
			
			
			if ("totalBox" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->totalBox); 
				
			}
			
			
			
			
			if ("deliveryMethodDesc" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->deliveryMethodDesc);
				
			}
			
			
			
			
			if ("id" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->id); 
				
			}
			
			
			
			
			if ("isSubmit" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->isSubmit); 
				
			}
			
			
			
			
			if ("po" == $schemeField){
				
				$needSkip = false;
				
				$this->po = new \com\vip\vop\vcloud\jit\Po();
				$this->po->read($input);
				
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
		
		
		if($this->vendorName !== null) {
			
			$xfer += $output->writeFieldBegin('vendorName');
			$xfer += $output->writeString($this->vendorName);
			
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
		
		
		if($this->deliveryNo !== null) {
			
			$xfer += $output->writeFieldBegin('deliveryNo');
			$xfer += $output->writeString($this->deliveryNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->vipWarehouse !== null) {
			
			$xfer += $output->writeFieldBegin('vipWarehouse');
			$xfer += $output->writeString($this->vipWarehouse);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->deliveryMethod !== null) {
			
			$xfer += $output->writeFieldBegin('deliveryMethod');
			$xfer += $output->writeI32($this->deliveryMethod);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->deliveryTime !== null) {
			
			$xfer += $output->writeFieldBegin('deliveryTime');
			$xfer += $output->writeI64($this->deliveryTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->carrierCode !== null) {
			
			$xfer += $output->writeFieldBegin('carrierCode');
			$xfer += $output->writeString($this->carrierCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->carrierName !== null) {
			
			$xfer += $output->writeFieldBegin('carrierName');
			$xfer += $output->writeString($this->carrierName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->outTime !== null) {
			
			$xfer += $output->writeFieldBegin('outTime');
			$xfer += $output->writeI64($this->outTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->outFlag !== null) {
			
			$xfer += $output->writeFieldBegin('outFlag');
			$xfer += $output->writeI32($this->outFlag);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->createTime !== null) {
			
			$xfer += $output->writeFieldBegin('createTime');
			$xfer += $output->writeI64($this->createTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->arrivalTime !== null) {
			
			$xfer += $output->writeFieldBegin('arrivalTime');
			$xfer += $output->writeI64($this->arrivalTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->realArrivalTime !== null) {
			
			$xfer += $output->writeFieldBegin('realArrivalTime');
			$xfer += $output->writeI64($this->realArrivalTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->raceTime !== null) {
			
			$xfer += $output->writeFieldBegin('raceTime');
			$xfer += $output->writeI64($this->raceTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->realRaceTime !== null) {
			
			$xfer += $output->writeFieldBegin('realRaceTime');
			$xfer += $output->writeI64($this->realRaceTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->outFlagDesc !== null) {
			
			$xfer += $output->writeFieldBegin('outFlagDesc');
			$xfer += $output->writeString($this->outFlagDesc);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->erpWarehouse !== null) {
			
			$xfer += $output->writeFieldBegin('erpWarehouse');
			$xfer += $output->writeString($this->erpWarehouse);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandName !== null) {
			
			$xfer += $output->writeFieldBegin('brandName');
			$xfer += $output->writeString($this->brandName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->totalDeliveryNum !== null) {
			
			$xfer += $output->writeFieldBegin('totalDeliveryNum');
			$xfer += $output->writeI32($this->totalDeliveryNum);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->totalBox !== null) {
			
			$xfer += $output->writeFieldBegin('totalBox');
			$xfer += $output->writeI32($this->totalBox);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->deliveryMethodDesc !== null) {
			
			$xfer += $output->writeFieldBegin('deliveryMethodDesc');
			$xfer += $output->writeString($this->deliveryMethodDesc);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->id !== null) {
			
			$xfer += $output->writeFieldBegin('id');
			$xfer += $output->writeI64($this->id);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isSubmit !== null) {
			
			$xfer += $output->writeFieldBegin('isSubmit');
			$xfer += $output->writeI64($this->isSubmit);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->po !== null) {
			
			$xfer += $output->writeFieldBegin('po');
			
			if (!is_object($this->po)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->po->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>