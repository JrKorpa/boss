<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\xstore\base\warehouse\service;

class TransferResp {
	
	static $_TSPEC;
	public $companyCode = null;
	public $erpWarehouseCode = null;
	public $xstoreWarehouseCode = null;
	public $logisiticsWarehouseCode = null;
	public $logisiticsName = null;
	public $logisiticsCustomerCode = null;
	public $isWhale = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'companyCode'
			),
			2 => array(
			'var' => 'erpWarehouseCode'
			),
			3 => array(
			'var' => 'xstoreWarehouseCode'
			),
			4 => array(
			'var' => 'logisiticsWarehouseCode'
			),
			5 => array(
			'var' => 'logisiticsName'
			),
			6 => array(
			'var' => 'logisiticsCustomerCode'
			),
			7 => array(
			'var' => 'isWhale'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['companyCode'])){
				
				$this->companyCode = $vals['companyCode'];
			}
			
			
			if (isset($vals['erpWarehouseCode'])){
				
				$this->erpWarehouseCode = $vals['erpWarehouseCode'];
			}
			
			
			if (isset($vals['xstoreWarehouseCode'])){
				
				$this->xstoreWarehouseCode = $vals['xstoreWarehouseCode'];
			}
			
			
			if (isset($vals['logisiticsWarehouseCode'])){
				
				$this->logisiticsWarehouseCode = $vals['logisiticsWarehouseCode'];
			}
			
			
			if (isset($vals['logisiticsName'])){
				
				$this->logisiticsName = $vals['logisiticsName'];
			}
			
			
			if (isset($vals['logisiticsCustomerCode'])){
				
				$this->logisiticsCustomerCode = $vals['logisiticsCustomerCode'];
			}
			
			
			if (isset($vals['isWhale'])){
				
				$this->isWhale = $vals['isWhale'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'TransferResp';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("companyCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->companyCode);
				
			}
			
			
			
			
			if ("erpWarehouseCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->erpWarehouseCode);
				
			}
			
			
			
			
			if ("xstoreWarehouseCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->xstoreWarehouseCode);
				
			}
			
			
			
			
			if ("logisiticsWarehouseCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->logisiticsWarehouseCode);
				
			}
			
			
			
			
			if ("logisiticsName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->logisiticsName);
				
			}
			
			
			
			
			if ("logisiticsCustomerCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->logisiticsCustomerCode);
				
			}
			
			
			
			
			if ("isWhale" == $schemeField){
				
				$needSkip = false;
				$input->readByte($this->isWhale); 
				
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
		
		if($this->companyCode !== null) {
			
			$xfer += $output->writeFieldBegin('companyCode');
			$xfer += $output->writeString($this->companyCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->erpWarehouseCode !== null) {
			
			$xfer += $output->writeFieldBegin('erpWarehouseCode');
			$xfer += $output->writeString($this->erpWarehouseCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->xstoreWarehouseCode !== null) {
			
			$xfer += $output->writeFieldBegin('xstoreWarehouseCode');
			$xfer += $output->writeString($this->xstoreWarehouseCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->logisiticsWarehouseCode !== null) {
			
			$xfer += $output->writeFieldBegin('logisiticsWarehouseCode');
			$xfer += $output->writeString($this->logisiticsWarehouseCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->logisiticsName !== null) {
			
			$xfer += $output->writeFieldBegin('logisiticsName');
			$xfer += $output->writeString($this->logisiticsName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->logisiticsCustomerCode !== null) {
			
			$xfer += $output->writeFieldBegin('logisiticsCustomerCode');
			$xfer += $output->writeString($this->logisiticsCustomerCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isWhale !== null) {
			
			$xfer += $output->writeFieldBegin('isWhale');
			$xfer += $output->writeByte($this->isWhale);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>