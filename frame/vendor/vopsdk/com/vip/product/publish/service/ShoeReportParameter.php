<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\publish\service;

class ShoeReportParameter {
	
	static $_TSPEC;
	public $vendorId = null;
	public $barcode = null;
	public $sizeRelationshipJson = null;
	public $shoeTags = null;
	public $result = null;
	public $reportJson = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'barcode'
			),
			3 => array(
			'var' => 'sizeRelationshipJson'
			),
			4 => array(
			'var' => 'shoeTags'
			),
			5 => array(
			'var' => 'result'
			),
			6 => array(
			'var' => 'reportJson'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
			if (isset($vals['sizeRelationshipJson'])){
				
				$this->sizeRelationshipJson = $vals['sizeRelationshipJson'];
			}
			
			
			if (isset($vals['shoeTags'])){
				
				$this->shoeTags = $vals['shoeTags'];
			}
			
			
			if (isset($vals['result'])){
				
				$this->result = $vals['result'];
			}
			
			
			if (isset($vals['reportJson'])){
				
				$this->reportJson = $vals['reportJson'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'ShoeReportParameter';
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
			
			
			
			
			if ("barcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->barcode);
				
			}
			
			
			
			
			if ("sizeRelationshipJson" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->sizeRelationshipJson);
				
			}
			
			
			
			
			if ("shoeTags" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->shoeTags);
				
			}
			
			
			
			
			if ("result" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->result);
				
			}
			
			
			
			
			if ("reportJson" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->reportJson);
				
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
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI32($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('barcode');
		$xfer += $output->writeString($this->barcode);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('sizeRelationshipJson');
		$xfer += $output->writeString($this->sizeRelationshipJson);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('shoeTags');
		$xfer += $output->writeString($this->shoeTags);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('result');
		$xfer += $output->writeString($this->result);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('reportJson');
		$xfer += $output->writeString($this->reportJson);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>