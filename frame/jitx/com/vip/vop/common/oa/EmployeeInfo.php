<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\common\oa;

class EmployeeInfo {
	
	static $_TSPEC;
	public $employeeId = null;
	public $oaName = null;
	public $realName = null;
	public $email = null;
	public $deptCode = null;
	public $deptName = null;
	public $deptFullName = null;
	public $positionCode = null;
	public $positionName = null;
	public $mobile = null;
	public $gender = null;
	public $birthday = null;
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
			'var' => 'email'
			),
			5 => array(
			'var' => 'deptCode'
			),
			6 => array(
			'var' => 'deptName'
			),
			7 => array(
			'var' => 'deptFullName'
			),
			8 => array(
			'var' => 'positionCode'
			),
			9 => array(
			'var' => 'positionName'
			),
			10 => array(
			'var' => 'mobile'
			),
			11 => array(
			'var' => 'gender'
			),
			12 => array(
			'var' => 'birthday'
			),
			13 => array(
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
			
			
			if (isset($vals['email'])){
				
				$this->email = $vals['email'];
			}
			
			
			if (isset($vals['deptCode'])){
				
				$this->deptCode = $vals['deptCode'];
			}
			
			
			if (isset($vals['deptName'])){
				
				$this->deptName = $vals['deptName'];
			}
			
			
			if (isset($vals['deptFullName'])){
				
				$this->deptFullName = $vals['deptFullName'];
			}
			
			
			if (isset($vals['positionCode'])){
				
				$this->positionCode = $vals['positionCode'];
			}
			
			
			if (isset($vals['positionName'])){
				
				$this->positionName = $vals['positionName'];
			}
			
			
			if (isset($vals['mobile'])){
				
				$this->mobile = $vals['mobile'];
			}
			
			
			if (isset($vals['gender'])){
				
				$this->gender = $vals['gender'];
			}
			
			
			if (isset($vals['birthday'])){
				
				$this->birthday = $vals['birthday'];
			}
			
			
			if (isset($vals['status'])){
				
				$this->status = $vals['status'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'EmployeeInfo';
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
			
			
			
			
			if ("email" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->email);
				
			}
			
			
			
			
			if ("deptCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->deptCode);
				
			}
			
			
			
			
			if ("deptName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->deptName);
				
			}
			
			
			
			
			if ("deptFullName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->deptFullName);
				
			}
			
			
			
			
			if ("positionCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->positionCode);
				
			}
			
			
			
			
			if ("positionName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->positionName);
				
			}
			
			
			
			
			if ("mobile" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->mobile);
				
			}
			
			
			
			
			if ("gender" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->gender); 
				
			}
			
			
			
			
			if ("birthday" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->birthday);
				
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
		
		
		if($this->email !== null) {
			
			$xfer += $output->writeFieldBegin('email');
			$xfer += $output->writeString($this->email);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->deptCode !== null) {
			
			$xfer += $output->writeFieldBegin('deptCode');
			$xfer += $output->writeString($this->deptCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->deptName !== null) {
			
			$xfer += $output->writeFieldBegin('deptName');
			$xfer += $output->writeString($this->deptName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->deptFullName !== null) {
			
			$xfer += $output->writeFieldBegin('deptFullName');
			$xfer += $output->writeString($this->deptFullName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->positionCode !== null) {
			
			$xfer += $output->writeFieldBegin('positionCode');
			$xfer += $output->writeString($this->positionCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->positionName !== null) {
			
			$xfer += $output->writeFieldBegin('positionName');
			$xfer += $output->writeString($this->positionName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->mobile !== null) {
			
			$xfer += $output->writeFieldBegin('mobile');
			$xfer += $output->writeString($this->mobile);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->gender !== null) {
			
			$xfer += $output->writeFieldBegin('gender');
			$xfer += $output->writeI32($this->gender);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->birthday !== null) {
			
			$xfer += $output->writeFieldBegin('birthday');
			$xfer += $output->writeI64($this->birthday);
			
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