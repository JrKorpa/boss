<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\common\oa;

class GetEmployeeInfoReq {
	
	static $_TSPEC;
	public $employeeId = null;
	public $oaName = null;
	public $realName = null;
	public $deptCode = null;
	public $status = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'employeeId'
			),
			2 => array(
			'var' => 'oaName'
			),
			3 => array(
			'var' => 'realName'
			),
			4 => array(
			'var' => 'deptCode'
			),
			5 => array(
			'var' => 'status'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['employeeId'])){
				
				$this->employeeId = $vals['employeeId'];
			}
			
			
			if (isset($vals['oaName'])){
				
				$this->oaName = $vals['oaName'];
			}
			
			
			if (isset($vals['realName'])){
				
				$this->realName = $vals['realName'];
			}
			
			
			if (isset($vals['deptCode'])){
				
				$this->deptCode = $vals['deptCode'];
			}
			
			
			if (isset($vals['status'])){
				
				$this->status = $vals['status'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GetEmployeeInfoReq';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("employeeId" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->employeeId);
				
			}
			
			
			
			
			if ("oaName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->oaName);
				
			}
			
			
			
			
			if ("realName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->realName);
				
			}
			
			
			
			
			if ("deptCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->deptCode);
				
			}
			
			
			
			
			if ("status" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->status); 
				
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
		
		if($this->employeeId !== null) {
			
			$xfer += $output->writeFieldBegin('employeeId');
			$xfer += $output->writeString($this->employeeId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->oaName !== null) {
			
			$xfer += $output->writeFieldBegin('oaName');
			$xfer += $output->writeString($this->oaName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->realName !== null) {
			
			$xfer += $output->writeFieldBegin('realName');
			$xfer += $output->writeString($this->realName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->deptCode !== null) {
			
			$xfer += $output->writeFieldBegin('deptCode');
			$xfer += $output->writeString($this->deptCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->status !== null) {
			
			$xfer += $output->writeFieldBegin('status');
			$xfer += $output->writeI32($this->status);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>