<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\order\common\pojo\order\vo;

class OrderCookieVO {
	
	static $_TSPEC;
	public $phpsessionId = null;
	public $marsCid = null;
	public $cid = null;
	public $mid = null;
	public $did = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'phpsessionId'
			),
			2 => array(
			'var' => 'marsCid'
			),
			3 => array(
			'var' => 'cid'
			),
			4 => array(
			'var' => 'mid'
			),
			5 => array(
			'var' => 'did'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['phpsessionId'])){
				
				$this->phpsessionId = $vals['phpsessionId'];
			}
			
			
			if (isset($vals['marsCid'])){
				
				$this->marsCid = $vals['marsCid'];
			}
			
			
			if (isset($vals['cid'])){
				
				$this->cid = $vals['cid'];
			}
			
			
			if (isset($vals['mid'])){
				
				$this->mid = $vals['mid'];
			}
			
			
			if (isset($vals['did'])){
				
				$this->did = $vals['did'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'OrderCookieVO';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("phpsessionId" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->phpsessionId);
				
			}
			
			
			
			
			if ("marsCid" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->marsCid);
				
			}
			
			
			
			
			if ("cid" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->cid);
				
			}
			
			
			
			
			if ("mid" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->mid);
				
			}
			
			
			
			
			if ("did" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->did);
				
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
		
		if($this->phpsessionId !== null) {
			
			$xfer += $output->writeFieldBegin('phpsessionId');
			$xfer += $output->writeString($this->phpsessionId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->marsCid !== null) {
			
			$xfer += $output->writeFieldBegin('marsCid');
			$xfer += $output->writeString($this->marsCid);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->cid !== null) {
			
			$xfer += $output->writeFieldBegin('cid');
			$xfer += $output->writeString($this->cid);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->mid !== null) {
			
			$xfer += $output->writeFieldBegin('mid');
			$xfer += $output->writeString($this->mid);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->did !== null) {
			
			$xfer += $output->writeFieldBegin('did');
			$xfer += $output->writeString($this->did);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>