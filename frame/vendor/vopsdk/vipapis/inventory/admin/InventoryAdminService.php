<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace vipapis\inventory\admin;
interface InventoryAdminServiceIf{
	
	
	public function delInvUpdateRetryRequest( $cooperationNo, $warehouse, $barcode);
	
	public function getWhiInvUpdateHealth();
	
	public function healthCheck();
	
	public function listInvUpdateRetryRequest( $startIndex, $endIndex);
	
	public function saveInvUpdateRetryRequest(\vipapis\inventory\admin\InvUpdateRetryRequest $retryRequest);
	
	public function saveWhiInvUpdateHealth( $health);
	
	public function updateStockRetry(\vipapis\inventory\admin\InvUpdateRetryRequest $retryRequest);
	
}

class _InventoryAdminServiceClient extends \Osp\Base\OspStub implements \vipapis\inventory\admin\InventoryAdminServiceIf{
	
	public function __construct(){
		
		parent::__construct("vipapis.inventory.admin.InventoryAdminService", "1.0.0");
	}
	
	
	public function delInvUpdateRetryRequest( $cooperationNo, $warehouse, $barcode){
		
		$this->send_delInvUpdateRetryRequest( $cooperationNo, $warehouse, $barcode);
		return $this->recv_delInvUpdateRetryRequest();
	}
	
	public function send_delInvUpdateRetryRequest( $cooperationNo, $warehouse, $barcode){
		
		$this->initInvocation("delInvUpdateRetryRequest");
		$args = new \vipapis\inventory\admin\InventoryAdminService_delInvUpdateRetryRequest_args();
		
		$args->cooperationNo = $cooperationNo;
		
		$args->warehouse = $warehouse;
		
		$args->barcode = $barcode;
		
		$this->send_base($args);
	}
	
	public function recv_delInvUpdateRetryRequest(){
		
		$result = new \vipapis\inventory\admin\InventoryAdminService_delInvUpdateRetryRequest_result();
		$this->receive_base($result);
		
	}
	
	
	public function getWhiInvUpdateHealth(){
		
		$this->send_getWhiInvUpdateHealth();
		return $this->recv_getWhiInvUpdateHealth();
	}
	
	public function send_getWhiInvUpdateHealth(){
		
		$this->initInvocation("getWhiInvUpdateHealth");
		$args = new \vipapis\inventory\admin\InventoryAdminService_getWhiInvUpdateHealth_args();
		
		$this->send_base($args);
	}
	
	public function recv_getWhiInvUpdateHealth(){
		
		$result = new \vipapis\inventory\admin\InventoryAdminService_getWhiInvUpdateHealth_result();
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
		$args = new \vipapis\inventory\admin\InventoryAdminService_healthCheck_args();
		
		$this->send_base($args);
	}
	
	public function recv_healthCheck(){
		
		$result = new \vipapis\inventory\admin\InventoryAdminService_healthCheck_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function listInvUpdateRetryRequest( $startIndex, $endIndex){
		
		$this->send_listInvUpdateRetryRequest( $startIndex, $endIndex);
		return $this->recv_listInvUpdateRetryRequest();
	}
	
	public function send_listInvUpdateRetryRequest( $startIndex, $endIndex){
		
		$this->initInvocation("listInvUpdateRetryRequest");
		$args = new \vipapis\inventory\admin\InventoryAdminService_listInvUpdateRetryRequest_args();
		
		$args->startIndex = $startIndex;
		
		$args->endIndex = $endIndex;
		
		$this->send_base($args);
	}
	
	public function recv_listInvUpdateRetryRequest(){
		
		$result = new \vipapis\inventory\admin\InventoryAdminService_listInvUpdateRetryRequest_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function saveInvUpdateRetryRequest(\vipapis\inventory\admin\InvUpdateRetryRequest $retryRequest){
		
		$this->send_saveInvUpdateRetryRequest( $retryRequest);
		return $this->recv_saveInvUpdateRetryRequest();
	}
	
	public function send_saveInvUpdateRetryRequest(\vipapis\inventory\admin\InvUpdateRetryRequest $retryRequest){
		
		$this->initInvocation("saveInvUpdateRetryRequest");
		$args = new \vipapis\inventory\admin\InventoryAdminService_saveInvUpdateRetryRequest_args();
		
		$args->retryRequest = $retryRequest;
		
		$this->send_base($args);
	}
	
	public function recv_saveInvUpdateRetryRequest(){
		
		$result = new \vipapis\inventory\admin\InventoryAdminService_saveInvUpdateRetryRequest_result();
		$this->receive_base($result);
		
	}
	
	
	public function saveWhiInvUpdateHealth( $health){
		
		$this->send_saveWhiInvUpdateHealth( $health);
		return $this->recv_saveWhiInvUpdateHealth();
	}
	
	public function send_saveWhiInvUpdateHealth( $health){
		
		$this->initInvocation("saveWhiInvUpdateHealth");
		$args = new \vipapis\inventory\admin\InventoryAdminService_saveWhiInvUpdateHealth_args();
		
		$args->health = $health;
		
		$this->send_base($args);
	}
	
	public function recv_saveWhiInvUpdateHealth(){
		
		$result = new \vipapis\inventory\admin\InventoryAdminService_saveWhiInvUpdateHealth_result();
		$this->receive_base($result);
		
	}
	
	
	public function updateStockRetry(\vipapis\inventory\admin\InvUpdateRetryRequest $retryRequest){
		
		$this->send_updateStockRetry( $retryRequest);
		return $this->recv_updateStockRetry();
	}
	
	public function send_updateStockRetry(\vipapis\inventory\admin\InvUpdateRetryRequest $retryRequest){
		
		$this->initInvocation("updateStockRetry");
		$args = new \vipapis\inventory\admin\InventoryAdminService_updateStockRetry_args();
		
		$args->retryRequest = $retryRequest;
		
		$this->send_base($args);
	}
	
	public function recv_updateStockRetry(){
		
		$result = new \vipapis\inventory\admin\InventoryAdminService_updateStockRetry_result();
		$this->receive_base($result);
		
	}
	
	
}




class InventoryAdminService_delInvUpdateRetryRequest_args {
	
	static $_TSPEC;
	public $cooperationNo = null;
	public $warehouse = null;
	public $barcode = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'cooperationNo'
			),
			2 => array(
			'var' => 'warehouse'
			),
			3 => array(
			'var' => 'barcode'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['cooperationNo'])){
				
				$this->cooperationNo = $vals['cooperationNo'];
			}
			
			
			if (isset($vals['warehouse'])){
				
				$this->warehouse = $vals['warehouse'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI32($this->cooperationNo); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->warehouse);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->barcode);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('cooperationNo');
		$xfer += $output->writeI32($this->cooperationNo);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->warehouse !== null) {
			
			$xfer += $output->writeFieldBegin('warehouse');
			$xfer += $output->writeString($this->warehouse);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcode !== null) {
			
			$xfer += $output->writeFieldBegin('barcode');
			$xfer += $output->writeString($this->barcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class InventoryAdminService_getWhiInvUpdateHealth_args {
	
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




class InventoryAdminService_healthCheck_args {
	
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




class InventoryAdminService_listInvUpdateRetryRequest_args {
	
	static $_TSPEC;
	public $startIndex = null;
	public $endIndex = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'startIndex'
			),
			2 => array(
			'var' => 'endIndex'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['startIndex'])){
				
				$this->startIndex = $vals['startIndex'];
			}
			
			
			if (isset($vals['endIndex'])){
				
				$this->endIndex = $vals['endIndex'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI32($this->startIndex); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->endIndex); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('startIndex');
		$xfer += $output->writeI32($this->startIndex);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('endIndex');
		$xfer += $output->writeI32($this->endIndex);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class InventoryAdminService_saveInvUpdateRetryRequest_args {
	
	static $_TSPEC;
	public $retryRequest = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'retryRequest'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['retryRequest'])){
				
				$this->retryRequest = $vals['retryRequest'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->retryRequest = new \vipapis\inventory\admin\InvUpdateRetryRequest();
			$this->retryRequest->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->retryRequest !== null) {
			
			$xfer += $output->writeFieldBegin('retryRequest');
			
			if (!is_object($this->retryRequest)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->retryRequest->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class InventoryAdminService_saveWhiInvUpdateHealth_args {
	
	static $_TSPEC;
	public $health = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'health'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['health'])){
				
				$this->health = $vals['health'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->health);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('health');
		$xfer += $output->writeString($this->health);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class InventoryAdminService_updateStockRetry_args {
	
	static $_TSPEC;
	public $retryRequest = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'retryRequest'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['retryRequest'])){
				
				$this->retryRequest = $vals['retryRequest'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->retryRequest = new \vipapis\inventory\admin\InvUpdateRetryRequest();
			$this->retryRequest->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->retryRequest !== null) {
			
			$xfer += $output->writeFieldBegin('retryRequest');
			
			if (!is_object($this->retryRequest)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->retryRequest->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class InventoryAdminService_delInvUpdateRetryRequest_result {
	
	static $_TSPEC;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
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




class InventoryAdminService_getWhiInvUpdateHealth_result {
	
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
			
			$input->readString($this->success);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			$xfer += $output->writeString($this->success);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class InventoryAdminService_healthCheck_result {
	
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




class InventoryAdminService_listInvUpdateRetryRequest_result {
	
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
			
			
			$this->success = new \vipapis\inventory\admin\ListInvRequestRetryRequestResult();
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




class InventoryAdminService_saveInvUpdateRetryRequest_result {
	
	static $_TSPEC;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
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




class InventoryAdminService_saveWhiInvUpdateHealth_result {
	
	static $_TSPEC;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
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




class InventoryAdminService_updateStockRetry_result {
	
	static $_TSPEC;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
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




?>