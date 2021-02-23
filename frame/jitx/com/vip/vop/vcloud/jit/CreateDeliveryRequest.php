<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\jit;

class CreateDeliveryRequest {
	
	static $_TSPEC;
	public $vendorId = null;
	public $poNo = null;
	public $deliveryNo = null;
	public $vipWarehouse = null;
	public $deliveryMethod = null;
	public $deliveryTime = null;
	public $arrivalTime = null;
	public $carrierCode = null;
	public $carrierName = null;
	public $erpWarehouse = null;
	public $subPickNos = null;
	
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
			'var' => 'deliveryNo'
			),
			4 => array(
			'var' => 'vipWarehouse'
			),
			5 => array(
			'var' => 'deliveryMethod'
			),
			6 => array(
			'var' => 'deliveryTime'
			),
			7 => array(
			'var' => 'arrivalTime'
			),
			8 => array(
			'var' => 'carrierCode'
			),
			9 => array(
			'var' => 'carrierName'
			),
			10 => array(
			'var' => 'erpWarehouse'
			),
			11 => array(
			'var' => 'subPickNos'
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
			
			
			if (isset($vals['arrivalTime'])){
				
				$this->arrivalTime = $vals['arrivalTime'];
			}
			
			
			if (isset($vals['carrierCode'])){
				
				$this->carrierCode = $vals['carrierCode'];
			}
			
			
			if (isset($vals['carrierName'])){
				
				$this->carrierName = $vals['carrierName'];
			}
			
			
			if (isset($vals['erpWarehouse'])){
				
				$this->erpWarehouse = $vals['erpWarehouse'];
			}
			
			
			if (isset($vals['subPickNos'])){
				
				$this->subPickNos = $vals['subPickNos'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'CreateDeliveryRequest';
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
			
			
			
			
			if ("arrivalTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->arrivalTime);
				
			}
			
			
			
			
			if ("carrierCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->carrierCode);
				
			}
			
			
			
			
			if ("carrierName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->carrierName);
				
			}
			
			
			
			
			if ("erpWarehouse" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->erpWarehouse);
				
			}
			
			
			
			
			if ("subPickNos" == $schemeField){
				
				$needSkip = false;
				
				$this->subPickNos = array();
				$_size0 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem0 = null;
						$input->readString($elem0);
						
						$this->subPickNos[$_size0++] = $elem0;
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
		
		
		if($this->arrivalTime !== null) {
			
			$xfer += $output->writeFieldBegin('arrivalTime');
			$xfer += $output->writeI64($this->arrivalTime);
			
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
		
		
		if($this->erpWarehouse !== null) {
			
			$xfer += $output->writeFieldBegin('erpWarehouse');
			$xfer += $output->writeString($this->erpWarehouse);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->subPickNos !== null) {
			
			$xfer += $output->writeFieldBegin('subPickNos');
			
			if (!is_array($this->subPickNos)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->subPickNos as $iter0){
				
				$xfer += $output->writeString($iter0);
				
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