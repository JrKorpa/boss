<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\isv\tools;

class CloudCooperationNoLogDo {
	
	static $_TSPEC;
	public $vendorId = null;
	public $vendorName = null;
	public $cooperationNo = null;
	public $forbidden = null;
	public $applyContent = null;
	public $effectMode = null;
	public $effectTime = null;
	public $expireTime = null;
	public $applyBy = null;
	public $applyTime = null;
	public $checkBy = null;
	public $checkTime = null;
	public $createTime = null;
	
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
			'var' => 'cooperationNo'
			),
			4 => array(
			'var' => 'forbidden'
			),
			5 => array(
			'var' => 'applyContent'
			),
			6 => array(
			'var' => 'effectMode'
			),
			7 => array(
			'var' => 'effectTime'
			),
			8 => array(
			'var' => 'expireTime'
			),
			9 => array(
			'var' => 'applyBy'
			),
			10 => array(
			'var' => 'applyTime'
			),
			11 => array(
			'var' => 'checkBy'
			),
			12 => array(
			'var' => 'checkTime'
			),
			13 => array(
			'var' => 'createTime'
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
			
			
			if (isset($vals['cooperationNo'])){
				
				$this->cooperationNo = $vals['cooperationNo'];
			}
			
			
			if (isset($vals['forbidden'])){
				
				$this->forbidden = $vals['forbidden'];
			}
			
			
			if (isset($vals['applyContent'])){
				
				$this->applyContent = $vals['applyContent'];
			}
			
			
			if (isset($vals['effectMode'])){
				
				$this->effectMode = $vals['effectMode'];
			}
			
			
			if (isset($vals['effectTime'])){
				
				$this->effectTime = $vals['effectTime'];
			}
			
			
			if (isset($vals['expireTime'])){
				
				$this->expireTime = $vals['expireTime'];
			}
			
			
			if (isset($vals['applyBy'])){
				
				$this->applyBy = $vals['applyBy'];
			}
			
			
			if (isset($vals['applyTime'])){
				
				$this->applyTime = $vals['applyTime'];
			}
			
			
			if (isset($vals['checkBy'])){
				
				$this->checkBy = $vals['checkBy'];
			}
			
			
			if (isset($vals['checkTime'])){
				
				$this->checkTime = $vals['checkTime'];
			}
			
			
			if (isset($vals['createTime'])){
				
				$this->createTime = $vals['createTime'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'CloudCooperationNoLogDo';
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
			
			
			
			
			if ("cooperationNo" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->cooperationNo); 
				
			}
			
			
			
			
			if ("forbidden" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->forbidden); 
				
			}
			
			
			
			
			if ("applyContent" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->applyContent); 
				
			}
			
			
			
			
			if ("effectMode" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->effectMode); 
				
			}
			
			
			
			
			if ("effectTime" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->effectTime);
				
			}
			
			
			
			
			if ("expireTime" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->expireTime);
				
			}
			
			
			
			
			if ("applyBy" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->applyBy);
				
			}
			
			
			
			
			if ("applyTime" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->applyTime);
				
			}
			
			
			
			
			if ("checkBy" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->checkBy);
				
			}
			
			
			
			
			if ("checkTime" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->checkTime);
				
			}
			
			
			
			
			if ("createTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->createTime);
				
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
		
		
		if($this->cooperationNo !== null) {
			
			$xfer += $output->writeFieldBegin('cooperationNo');
			$xfer += $output->writeI32($this->cooperationNo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->forbidden !== null) {
			
			$xfer += $output->writeFieldBegin('forbidden');
			$xfer += $output->writeI32($this->forbidden);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->applyContent !== null) {
			
			$xfer += $output->writeFieldBegin('applyContent');
			$xfer += $output->writeI32($this->applyContent);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->effectMode !== null) {
			
			$xfer += $output->writeFieldBegin('effectMode');
			$xfer += $output->writeI32($this->effectMode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->effectTime !== null) {
			
			$xfer += $output->writeFieldBegin('effectTime');
			$xfer += $output->writeString($this->effectTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->expireTime !== null) {
			
			$xfer += $output->writeFieldBegin('expireTime');
			$xfer += $output->writeString($this->expireTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->applyBy !== null) {
			
			$xfer += $output->writeFieldBegin('applyBy');
			$xfer += $output->writeString($this->applyBy);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->applyTime !== null) {
			
			$xfer += $output->writeFieldBegin('applyTime');
			$xfer += $output->writeString($this->applyTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->checkBy !== null) {
			
			$xfer += $output->writeFieldBegin('checkBy');
			$xfer += $output->writeString($this->checkBy);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->checkTime !== null) {
			
			$xfer += $output->writeFieldBegin('checkTime');
			$xfer += $output->writeString($this->checkTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->createTime !== null) {
			
			$xfer += $output->writeFieldBegin('createTime');
			$xfer += $output->writeI64($this->createTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>