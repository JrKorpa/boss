<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\common\switcher;
interface AppSwitcherServiceIf{
	
	
	public function closeSwitcher( $domainName, $moduleName);
	
	public function createSwitcher( $domainName, $moduleName, $remark);
	
	public function healthCheck();
	
	public function isOpenSwither( $domainName, $moduleName);
	
	public function openSwitcher( $domainName, $moduleName);
	
}

class _AppSwitcherServiceClient extends \Osp\Base\OspStub implements \com\vip\vop\common\switcher\AppSwitcherServiceIf{
	
	public function __construct(){
		
		parent::__construct("com.vip.vop.common.switcher.AppSwitcherService", "1.0.0");
	}
	
	
	public function closeSwitcher( $domainName, $moduleName){
		
		$this->send_closeSwitcher( $domainName, $moduleName);
		return $this->recv_closeSwitcher();
	}
	
	public function send_closeSwitcher( $domainName, $moduleName){
		
		$this->initInvocation("closeSwitcher");
		$args = new \com\vip\vop\common\switcher\AppSwitcherService_closeSwitcher_args();
		
		$args->domainName = $domainName;
		
		$args->moduleName = $moduleName;
		
		$this->send_base($args);
	}
	
	public function recv_closeSwitcher(){
		
		$result = new \com\vip\vop\common\switcher\AppSwitcherService_closeSwitcher_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function createSwitcher( $domainName, $moduleName, $remark){
		
		$this->send_createSwitcher( $domainName, $moduleName, $remark);
		return $this->recv_createSwitcher();
	}
	
	public function send_createSwitcher( $domainName, $moduleName, $remark){
		
		$this->initInvocation("createSwitcher");
		$args = new \com\vip\vop\common\switcher\AppSwitcherService_createSwitcher_args();
		
		$args->domainName = $domainName;
		
		$args->moduleName = $moduleName;
		
		$args->remark = $remark;
		
		$this->send_base($args);
	}
	
	public function recv_createSwitcher(){
		
		$result = new \com\vip\vop\common\switcher\AppSwitcherService_createSwitcher_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function healthCheck(){
		
		$this->send_healthCheck();
		return $this->recv_healthCheck();
	}
	
	public function send_healthCheck(){
		
		$this->initInvocation("healthCheck");
		$args = new \com\vip\vop\common\switcher\AppSwitcherService_healthCheck_args();
		
		$this->send_base($args);
	}
	
	public function recv_healthCheck(){
		
		$result = new \com\vip\vop\common\switcher\AppSwitcherService_healthCheck_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function isOpenSwither( $domainName, $moduleName){
		
		$this->send_isOpenSwither( $domainName, $moduleName);
		return $this->recv_isOpenSwither();
	}
	
	public function send_isOpenSwither( $domainName, $moduleName){
		
		$this->initInvocation("isOpenSwither");
		$args = new \com\vip\vop\common\switcher\AppSwitcherService_isOpenSwither_args();
		
		$args->domainName = $domainName;
		
		$args->moduleName = $moduleName;
		
		$this->send_base($args);
	}
	
	public function recv_isOpenSwither(){
		
		$result = new \com\vip\vop\common\switcher\AppSwitcherService_isOpenSwither_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function openSwitcher( $domainName, $moduleName){
		
		$this->send_openSwitcher( $domainName, $moduleName);
		return $this->recv_openSwitcher();
	}
	
	public function send_openSwitcher( $domainName, $moduleName){
		
		$this->initInvocation("openSwitcher");
		$args = new \com\vip\vop\common\switcher\AppSwitcherService_openSwitcher_args();
		
		$args->domainName = $domainName;
		
		$args->moduleName = $moduleName;
		
		$this->send_base($args);
	}
	
	public function recv_openSwitcher(){
		
		$result = new \com\vip\vop\common\switcher\AppSwitcherService_openSwitcher_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
}




class AppSwitcherService_closeSwitcher_args {
	
	static $_TSPEC;
	public $domainName = null;
	public $moduleName = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'domainName'
			),
			2 => array(
			'var' => 'moduleName'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['domainName'])){
				
				$this->domainName = $vals['domainName'];
			}
			
			
			if (isset($vals['moduleName'])){
				
				$this->moduleName = $vals['moduleName'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->domainName);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->moduleName);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('domainName');
		$xfer += $output->writeString($this->domainName);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('moduleName');
		$xfer += $output->writeString($this->moduleName);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppSwitcherService_createSwitcher_args {
	
	static $_TSPEC;
	public $domainName = null;
	public $moduleName = null;
	public $remark = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'domainName'
			),
			2 => array(
			'var' => 'moduleName'
			),
			3 => array(
			'var' => 'remark'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['domainName'])){
				
				$this->domainName = $vals['domainName'];
			}
			
			
			if (isset($vals['moduleName'])){
				
				$this->moduleName = $vals['moduleName'];
			}
			
			
			if (isset($vals['remark'])){
				
				$this->remark = $vals['remark'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->domainName);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->moduleName);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->remark);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('domainName');
		$xfer += $output->writeString($this->domainName);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('moduleName');
		$xfer += $output->writeString($this->moduleName);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->remark !== null) {
			
			$xfer += $output->writeFieldBegin('remark');
			$xfer += $output->writeString($this->remark);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppSwitcherService_healthCheck_args {
	
	static $_TSPEC;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			
			);
			
		}
		
		if (is_array($vals)){
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppSwitcherService_isOpenSwither_args {
	
	static $_TSPEC;
	public $domainName = null;
	public $moduleName = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'domainName'
			),
			2 => array(
			'var' => 'moduleName'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['domainName'])){
				
				$this->domainName = $vals['domainName'];
			}
			
			
			if (isset($vals['moduleName'])){
				
				$this->moduleName = $vals['moduleName'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->domainName);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->moduleName);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('domainName');
		$xfer += $output->writeString($this->domainName);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('moduleName');
		$xfer += $output->writeString($this->moduleName);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppSwitcherService_openSwitcher_args {
	
	static $_TSPEC;
	public $domainName = null;
	public $moduleName = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'domainName'
			),
			2 => array(
			'var' => 'moduleName'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['domainName'])){
				
				$this->domainName = $vals['domainName'];
			}
			
			
			if (isset($vals['moduleName'])){
				
				$this->moduleName = $vals['moduleName'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->domainName);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->moduleName);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('domainName');
		$xfer += $output->writeString($this->domainName);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('moduleName');
		$xfer += $output->writeString($this->moduleName);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppSwitcherService_closeSwitcher_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readBool($this->success);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			$xfer += $output->writeBool($this->success);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppSwitcherService_createSwitcher_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readBool($this->success);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			$xfer += $output->writeBool($this->success);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppSwitcherService_healthCheck_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = new \com\vip\hermes\core\health\CheckResult();
			$this->success->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_object($this->success)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->success->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppSwitcherService_isOpenSwither_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readBool($this->success);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			$xfer += $output->writeBool($this->success);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppSwitcherService_openSwitcher_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readBool($this->success);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			$xfer += $output->writeBool($this->success);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




?>