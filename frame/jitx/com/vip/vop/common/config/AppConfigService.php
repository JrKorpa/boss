<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\common\config;
interface AppConfigServiceIf{
	
	
	public function createAppConfig( $domainName, $moduleName, $remark);
	
	public function createAppConfigItem(\com\vip\vop\common\config\ConfigItemReq $configItemReq);
	
	public function healthCheck();
	
	public function queryConfigItemByItemCode( $domainName, $moduleName, $configItemCode);
	
	public function queryConfigItems( $domainName, $moduleName);
	
	public function updateAppConfigItem(\com\vip\vop\common\config\UpdateConfigItemReq $updateConfigItemReq);
	
	public function updateAppConfigItemValue( $domainName, $moduleName, $itemCode, $itemValue);
	
}

class _AppConfigServiceClient extends \Osp\Base\OspStub implements \com\vip\vop\common\config\AppConfigServiceIf{
	
	public function __construct(){
		
		parent::__construct("com.vip.vop.common.config.AppConfigService", "1.0.0");
	}
	
	
	public function createAppConfig( $domainName, $moduleName, $remark){
		
		$this->send_createAppConfig( $domainName, $moduleName, $remark);
		return $this->recv_createAppConfig();
	}
	
	public function send_createAppConfig( $domainName, $moduleName, $remark){
		
		$this->initInvocation("createAppConfig");
		$args = new \com\vip\vop\common\config\AppConfigService_createAppConfig_args();
		
		$args->domainName = $domainName;
		
		$args->moduleName = $moduleName;
		
		$args->remark = $remark;
		
		$this->send_base($args);
	}
	
	public function recv_createAppConfig(){
		
		$result = new \com\vip\vop\common\config\AppConfigService_createAppConfig_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function createAppConfigItem(\com\vip\vop\common\config\ConfigItemReq $configItemReq){
		
		$this->send_createAppConfigItem( $configItemReq);
		return $this->recv_createAppConfigItem();
	}
	
	public function send_createAppConfigItem(\com\vip\vop\common\config\ConfigItemReq $configItemReq){
		
		$this->initInvocation("createAppConfigItem");
		$args = new \com\vip\vop\common\config\AppConfigService_createAppConfigItem_args();
		
		$args->configItemReq = $configItemReq;
		
		$this->send_base($args);
	}
	
	public function recv_createAppConfigItem(){
		
		$result = new \com\vip\vop\common\config\AppConfigService_createAppConfigItem_result();
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
		$args = new \com\vip\vop\common\config\AppConfigService_healthCheck_args();
		
		$this->send_base($args);
	}
	
	public function recv_healthCheck(){
		
		$result = new \com\vip\vop\common\config\AppConfigService_healthCheck_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function queryConfigItemByItemCode( $domainName, $moduleName, $configItemCode){
		
		$this->send_queryConfigItemByItemCode( $domainName, $moduleName, $configItemCode);
		return $this->recv_queryConfigItemByItemCode();
	}
	
	public function send_queryConfigItemByItemCode( $domainName, $moduleName, $configItemCode){
		
		$this->initInvocation("queryConfigItemByItemCode");
		$args = new \com\vip\vop\common\config\AppConfigService_queryConfigItemByItemCode_args();
		
		$args->domainName = $domainName;
		
		$args->moduleName = $moduleName;
		
		$args->configItemCode = $configItemCode;
		
		$this->send_base($args);
	}
	
	public function recv_queryConfigItemByItemCode(){
		
		$result = new \com\vip\vop\common\config\AppConfigService_queryConfigItemByItemCode_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function queryConfigItems( $domainName, $moduleName){
		
		$this->send_queryConfigItems( $domainName, $moduleName);
		return $this->recv_queryConfigItems();
	}
	
	public function send_queryConfigItems( $domainName, $moduleName){
		
		$this->initInvocation("queryConfigItems");
		$args = new \com\vip\vop\common\config\AppConfigService_queryConfigItems_args();
		
		$args->domainName = $domainName;
		
		$args->moduleName = $moduleName;
		
		$this->send_base($args);
	}
	
	public function recv_queryConfigItems(){
		
		$result = new \com\vip\vop\common\config\AppConfigService_queryConfigItems_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function updateAppConfigItem(\com\vip\vop\common\config\UpdateConfigItemReq $updateConfigItemReq){
		
		$this->send_updateAppConfigItem( $updateConfigItemReq);
		return $this->recv_updateAppConfigItem();
	}
	
	public function send_updateAppConfigItem(\com\vip\vop\common\config\UpdateConfigItemReq $updateConfigItemReq){
		
		$this->initInvocation("updateAppConfigItem");
		$args = new \com\vip\vop\common\config\AppConfigService_updateAppConfigItem_args();
		
		$args->updateConfigItemReq = $updateConfigItemReq;
		
		$this->send_base($args);
	}
	
	public function recv_updateAppConfigItem(){
		
		$result = new \com\vip\vop\common\config\AppConfigService_updateAppConfigItem_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function updateAppConfigItemValue( $domainName, $moduleName, $itemCode, $itemValue){
		
		$this->send_updateAppConfigItemValue( $domainName, $moduleName, $itemCode, $itemValue);
		return $this->recv_updateAppConfigItemValue();
	}
	
	public function send_updateAppConfigItemValue( $domainName, $moduleName, $itemCode, $itemValue){
		
		$this->initInvocation("updateAppConfigItemValue");
		$args = new \com\vip\vop\common\config\AppConfigService_updateAppConfigItemValue_args();
		
		$args->domainName = $domainName;
		
		$args->moduleName = $moduleName;
		
		$args->itemCode = $itemCode;
		
		$args->itemValue = $itemValue;
		
		$this->send_base($args);
	}
	
	public function recv_updateAppConfigItemValue(){
		
		$result = new \com\vip\vop\common\config\AppConfigService_updateAppConfigItemValue_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
}




class AppConfigService_createAppConfig_args {
	
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




class AppConfigService_createAppConfigItem_args {
	
	static $_TSPEC;
	public $configItemReq = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'configItemReq'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['configItemReq'])){
				
				$this->configItemReq = $vals['configItemReq'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->configItemReq = new \com\vip\vop\common\config\ConfigItemReq();
			$this->configItemReq->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('configItemReq');
		
		if (!is_object($this->configItemReq)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->configItemReq->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppConfigService_healthCheck_args {
	
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




class AppConfigService_queryConfigItemByItemCode_args {
	
	static $_TSPEC;
	public $domainName = null;
	public $moduleName = null;
	public $configItemCode = null;
	
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
			'var' => 'configItemCode'
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
			
			
			if (isset($vals['configItemCode'])){
				
				$this->configItemCode = $vals['configItemCode'];
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
			
			$input->readString($this->configItemCode);
			
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
		
		$xfer += $output->writeFieldBegin('configItemCode');
		$xfer += $output->writeString($this->configItemCode);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppConfigService_queryConfigItems_args {
	
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




class AppConfigService_updateAppConfigItem_args {
	
	static $_TSPEC;
	public $updateConfigItemReq = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'updateConfigItemReq'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['updateConfigItemReq'])){
				
				$this->updateConfigItemReq = $vals['updateConfigItemReq'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->updateConfigItemReq = new \com\vip\vop\common\config\UpdateConfigItemReq();
			$this->updateConfigItemReq->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('updateConfigItemReq');
		
		if (!is_object($this->updateConfigItemReq)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->updateConfigItemReq->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppConfigService_updateAppConfigItemValue_args {
	
	static $_TSPEC;
	public $domainName = null;
	public $moduleName = null;
	public $itemCode = null;
	public $itemValue = null;
	
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
			'var' => 'itemCode'
			),
			4 => array(
			'var' => 'itemValue'
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
			
			
			if (isset($vals['itemCode'])){
				
				$this->itemCode = $vals['itemCode'];
			}
			
			
			if (isset($vals['itemValue'])){
				
				$this->itemValue = $vals['itemValue'];
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
			
			$input->readString($this->itemCode);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->itemValue);
			
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
		
		$xfer += $output->writeFieldBegin('itemCode');
		$xfer += $output->writeString($this->itemCode);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('itemValue');
		$xfer += $output->writeString($this->itemValue);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppConfigService_createAppConfig_result {
	
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
		
		$xfer += $output->writeFieldBegin('success');
		$xfer += $output->writeBool($this->success);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppConfigService_createAppConfigItem_result {
	
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
		
		$xfer += $output->writeFieldBegin('success');
		$xfer += $output->writeBool($this->success);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppConfigService_healthCheck_result {
	
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




class AppConfigService_queryConfigItemByItemCode_result {
	
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
			
			
			$this->success = new \com\vip\vop\common\config\ConfigItem();
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




class AppConfigService_queryConfigItems_result {
	
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
			
			
			$this->success = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					
					$elem0 = new \com\vip\vop\common\config\ConfigItem();
					$elem0->read($input);
					
					$this->success[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppConfigService_updateAppConfigItem_result {
	
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
		
		$xfer += $output->writeFieldBegin('success');
		$xfer += $output->writeBool($this->success);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class AppConfigService_updateAppConfigItemValue_result {
	
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
		
		$xfer += $output->writeFieldBegin('success');
		$xfer += $output->writeBool($this->success);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




?>