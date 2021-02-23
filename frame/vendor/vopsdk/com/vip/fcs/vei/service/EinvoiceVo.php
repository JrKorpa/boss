<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\fcs\vei\service;

class EinvoiceVo {
	
	static $_TSPEC;
	public $fpdm = null;
	public $fphm = null;
	public $pdfUrl = null;
	public $orderSn = null;
	public $taxRegisterNo = null;
	public $dataType = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'fpdm'
			),
			2 => array(
			'var' => 'fphm'
			),
			3 => array(
			'var' => 'pdfUrl'
			),
			4 => array(
			'var' => 'orderSn'
			),
			5 => array(
			'var' => 'taxRegisterNo'
			),
			6 => array(
			'var' => 'dataType'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['fpdm'])){
				
				$this->fpdm = $vals['fpdm'];
			}
			
			
			if (isset($vals['fphm'])){
				
				$this->fphm = $vals['fphm'];
			}
			
			
			if (isset($vals['pdfUrl'])){
				
				$this->pdfUrl = $vals['pdfUrl'];
			}
			
			
			if (isset($vals['orderSn'])){
				
				$this->orderSn = $vals['orderSn'];
			}
			
			
			if (isset($vals['taxRegisterNo'])){
				
				$this->taxRegisterNo = $vals['taxRegisterNo'];
			}
			
			
			if (isset($vals['dataType'])){
				
				$this->dataType = $vals['dataType'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'EinvoiceVo';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("fpdm" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->fpdm);
				
			}
			
			
			
			
			if ("fphm" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->fphm);
				
			}
			
			
			
			
			if ("pdfUrl" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->pdfUrl);
				
			}
			
			
			
			
			if ("orderSn" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->orderSn);
				
			}
			
			
			
			
			if ("taxRegisterNo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->taxRegisterNo);
				
			}
			
			
			
			
			if ("dataType" == $schemeField){
				
				$needSkip = false;
				$input->readByte($this->dataType); 
				
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
		
		
		if($this->pdfUrl !== null) {
			
			$xfer += $output->writeFieldBegin('pdfUrl');
			$xfer += $output->writeString($this->pdfUrl);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('orderSn');
		$xfer += $output->writeString($this->orderSn);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->taxRegisterNo !== null) {
			
			$xfer += $output->writeFieldBegin('taxRegisterNo');
			$xfer += $output->writeString($this->taxRegisterNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('dataType');
		$xfer += $output->writeByte($this->dataType);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>