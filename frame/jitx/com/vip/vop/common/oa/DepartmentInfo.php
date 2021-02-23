<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\common\oa;

class DepartmentInfo {
	
	static $_TSPEC;
	public $deptCode = null;
	public $deptName = null;
	public $level = null;
	public $parentDeptCode = null;
	public $firstDeptCode = null;
	public $firstDeptName = null;
	public $secondDeptCode = null;
	public $secondDeptName = null;
	public $thirdDeptCode = null;
	public $thirdDeptName = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'deptCode'
			),
			2 => array(
			'var' => 'deptName'
			),
			3 => array(
			'var' => 'level'
			),
			4 => array(
			'var' => 'parentDeptCode'
			),
			5 => array(
			'var' => 'firstDeptCode'
			),
			6 => array(
			'var' => 'firstDeptName'
			),
			7 => array(
			'var' => 'secondDeptCode'
			),
			8 => array(
			'var' => 'secondDeptName'
			),
			9 => array(
			'var' => 'thirdDeptCode'
			),
			10 => array(
			'var' => 'thirdDeptName'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['deptCode'])){
				
				$this->deptCode = $vals['deptCode'];
			}
			
			
			if (isset($vals['deptName'])){
				
				$this->deptName = $vals['deptName'];
			}
			
			
			if (isset($vals['level'])){
				
				$this->level = $vals['level'];
			}
			
			
			if (isset($vals['parentDeptCode'])){
				
				$this->parentDeptCode = $vals['parentDeptCode'];
			}
			
			
			if (isset($vals['firstDeptCode'])){
				
				$this->firstDeptCode = $vals['firstDeptCode'];
			}
			
			
			if (isset($vals['firstDeptName'])){
				
				$this->firstDeptName = $vals['firstDeptName'];
			}
			
			
			if (isset($vals['secondDeptCode'])){
				
				$this->secondDeptCode = $vals['secondDeptCode'];
			}
			
			
			if (isset($vals['secondDeptName'])){
				
				$this->secondDeptName = $vals['secondDeptName'];
			}
			
			
			if (isset($vals['thirdDeptCode'])){
				
				$this->thirdDeptCode = $vals['thirdDeptCode'];
			}
			
			
			if (isset($vals['thirdDeptName'])){
				
				$this->thirdDeptName = $vals['thirdDeptName'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'DepartmentInfo';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("deptCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->deptCode);
				
			}
			
			
			
			
			if ("deptName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->deptName);
				
			}
			
			
			
			
			if ("level" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->level); 
				
			}
			
			
			
			
			if ("parentDeptCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->parentDeptCode);
				
			}
			
			
			
			
			if ("firstDeptCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->firstDeptCode);
				
			}
			
			
			
			
			if ("firstDeptName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->firstDeptName);
				
			}
			
			
			
			
			if ("secondDeptCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->secondDeptCode);
				
			}
			
			
			
			
			if ("secondDeptName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->secondDeptName);
				
			}
			
			
			
			
			if ("thirdDeptCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->thirdDeptCode);
				
			}
			
			
			
			
			if ("thirdDeptName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->thirdDeptName);
				
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
		
		
		if($this->level !== null) {
			
			$xfer += $output->writeFieldBegin('level');
			$xfer += $output->writeI32($this->level);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->parentDeptCode !== null) {
			
			$xfer += $output->writeFieldBegin('parentDeptCode');
			$xfer += $output->writeString($this->parentDeptCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->firstDeptCode !== null) {
			
			$xfer += $output->writeFieldBegin('firstDeptCode');
			$xfer += $output->writeString($this->firstDeptCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->firstDeptName !== null) {
			
			$xfer += $output->writeFieldBegin('firstDeptName');
			$xfer += $output->writeString($this->firstDeptName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->secondDeptCode !== null) {
			
			$xfer += $output->writeFieldBegin('secondDeptCode');
			$xfer += $output->writeString($this->secondDeptCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->secondDeptName !== null) {
			
			$xfer += $output->writeFieldBegin('secondDeptName');
			$xfer += $output->writeString($this->secondDeptName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->thirdDeptCode !== null) {
			
			$xfer += $output->writeFieldBegin('thirdDeptCode');
			$xfer += $output->writeString($this->thirdDeptCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->thirdDeptName !== null) {
			
			$xfer += $output->writeFieldBegin('thirdDeptName');
			$xfer += $output->writeString($this->thirdDeptName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>