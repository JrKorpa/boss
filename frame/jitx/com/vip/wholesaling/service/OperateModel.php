<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\wholesaling\service;

class OperateModel {
	
	static $_TSPEC;
	public $value = null;
	public $label = null;
	public $afterSaleSnapshotId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'value'
			),
			2 => array(
			'var' => 'label'
			),
			3 => array(
			'var' => 'afterSaleSnapshotId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['value'])){
				
				$this->value = $vals['value'];
			}
			
			
			if (isset($vals['label'])){
				
				$this->label = $vals['label'];
			}
			
			
			if (isset($vals['afterSaleSnapshotId'])){
				
				$this->afterSaleSnapshotId = $vals['afterSaleSnapshotId'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'OperateModel';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("value" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->value);
				
			}
			
			
			
			
			if ("label" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->label);
				
			}
			
			
			
			
			if ("afterSaleSnapshotId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->afterSaleSnapshotId); 
				
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
		
		if($this->value !== null) {
			
			$xfer += $output->writeFieldBegin('value');
			$xfer += $output->writeString($this->value);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->label !== null) {
			
			$xfer += $output->writeFieldBegin('label');
			$xfer += $output->writeString($this->label);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->afterSaleSnapshotId !== null) {
			
			$xfer += $output->writeFieldBegin('afterSaleSnapshotId');
			$xfer += $output->writeI64($this->afterSaleSnapshotId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>