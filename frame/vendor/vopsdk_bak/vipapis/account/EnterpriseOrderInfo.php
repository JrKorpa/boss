<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace vipapis\account;

class EnterpriseOrderInfo {
	
	static $_TSPEC;
	public $enterprise_code = null;
	public $enterprise_employee_no = null;
	public $oder_time = null;
	public $money = null;
	public $category = null;
	public $account_status = null;
	public $order_status = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'enterprise_code'
			),
			2 => array(
			'var' => 'enterprise_employee_no'
			),
			3 => array(
			'var' => 'oder_time'
			),
			4 => array(
			'var' => 'money'
			),
			5 => array(
			'var' => 'category'
			),
			6 => array(
			'var' => 'account_status'
			),
			7 => array(
			'var' => 'order_status'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['enterprise_code'])){
				
				$this->enterprise_code = $vals['enterprise_code'];
			}
			
			
			if (isset($vals['enterprise_employee_no'])){
				
				$this->enterprise_employee_no = $vals['enterprise_employee_no'];
			}
			
			
			if (isset($vals['oder_time'])){
				
				$this->oder_time = $vals['oder_time'];
			}
			
			
			if (isset($vals['money'])){
				
				$this->money = $vals['money'];
			}
			
			
			if (isset($vals['category'])){
				
				$this->category = $vals['category'];
			}
			
			
			if (isset($vals['account_status'])){
				
				$this->account_status = $vals['account_status'];
			}
			
			
			if (isset($vals['order_status'])){
				
				$this->order_status = $vals['order_status'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'EnterpriseOrderInfo';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("enterprise_code" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->enterprise_code);
				
			}
			
			
			
			
			if ("enterprise_employee_no" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->enterprise_employee_no);
				
			}
			
			
			
			
			if ("oder_time" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->oder_time);
				
			}
			
			
			
			
			if ("money" == $schemeField){
				
				$needSkip = false;
				$input->readDouble($this->money);
				
			}
			
			
			
			
			if ("category" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->category);
				
			}
			
			
			
			
			if ("account_status" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->account_status); 
				
			}
			
			
			
			
			if ("order_status" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->order_status); 
				
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
		
		if($this->enterprise_code !== null) {
			
			$xfer += $output->writeFieldBegin('enterprise_code');
			$xfer += $output->writeString($this->enterprise_code);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->enterprise_employee_no !== null) {
			
			$xfer += $output->writeFieldBegin('enterprise_employee_no');
			$xfer += $output->writeString($this->enterprise_employee_no);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->oder_time !== null) {
			
			$xfer += $output->writeFieldBegin('oder_time');
			$xfer += $output->writeString($this->oder_time);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->money !== null) {
			
			$xfer += $output->writeFieldBegin('money');
			$xfer += $output->writeDouble($this->money);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->category !== null) {
			
			$xfer += $output->writeFieldBegin('category');
			$xfer += $output->writeString($this->category);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->account_status !== null) {
			
			$xfer += $output->writeFieldBegin('account_status');
			$xfer += $output->writeI32($this->account_status);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->order_status !== null) {
			
			$xfer += $output->writeFieldBegin('order_status');
			$xfer += $output->writeI32($this->order_status);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>