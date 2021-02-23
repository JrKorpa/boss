<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\sync;
interface InventoryOccupiedOrderSyncServiceIf{
	
	
	public function delDeductOrderRedisKey( $intervalDays, $startIndex);
	
	public function delExpiredOccupiedOrdersFromRedis();
	
	public function healthCheck();
	
	public function syncAllDeductOrderToRedis();
	
	public function syncAllOccupiedOrderToRedis();
	
	public function syncIncDeductOrderToRedis( $startIndex);
	
	public function syncIncOccupiedOrderToRedis( $startIndex);
	
}

class _InventoryOccupiedOrderSyncServiceClient extends \Osp\Base\OspStub implements \com\vip\vop\sync\InventoryOccupiedOrderSyncServiceIf{
	
	public function __construct(){
		
		parent::__construct("com.vip.vop.sync.InventoryOccupiedOrderSyncService", "1.0.0");
	}
	
	
	public function delDeductOrderRedisKey( $intervalDays, $startIndex){
		
		$this->send_delDeductOrderRedisKey( $intervalDays, $startIndex);
		return $this->recv_delDeductOrderRedisKey();
	}
	
	public function send_delDeductOrderRedisKey( $intervalDays, $startIndex){
		
		$this->initInvocation("delDeductOrderRedisKey");
		$args = new \com\vip\vop\sync\InventoryOccupiedOrderSyncService_delDeductOrderRedisKey_args();
		
		$args->intervalDays = $intervalDays;
		
		$args->startIndex = $startIndex;
		
		$this->send_base($args);
	}
	
	public function recv_delDeductOrderRedisKey(){
		
		$result = new \com\vip\vop\sync\InventoryOccupiedOrderSyncService_delDeductOrderRedisKey_result();
		$this->receive_base($result);
		
	}
	
	
	public function delExpiredOccupiedOrdersFromRedis(){
		
		$this->send_delExpiredOccupiedOrdersFromRedis();
		return $this->recv_delExpiredOccupiedOrdersFromRedis();
	}
	
	public function send_delExpiredOccupiedOrdersFromRedis(){
		
		$this->initInvocation("delExpiredOccupiedOrdersFromRedis");
		$args = new \com\vip\vop\sync\InventoryOccupiedOrderSyncService_delExpiredOccupiedOrdersFromRedis_args();
		
		$this->send_base($args);
	}
	
	public function recv_delExpiredOccupiedOrdersFromRedis(){
		
		$result = new \com\vip\vop\sync\InventoryOccupiedOrderSyncService_delExpiredOccupiedOrdersFromRedis_result();
		$this->receive_base($result);
		
	}
	
	
	public function healthCheck(){
		
		$this->send_healthCheck();
		return $this->recv_healthCheck();
	}
	
	public function send_healthCheck(){
		
		$this->initInvocation("healthCheck");
		$args = new \com\vip\vop\sync\InventoryOccupiedOrderSyncService_healthCheck_args();
		
		$this->send_base($args);
	}
	
	public function recv_healthCheck(){
		
		$result = new \com\vip\vop\sync\InventoryOccupiedOrderSyncService_healthCheck_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function syncAllDeductOrderToRedis(){
		
		$this->send_syncAllDeductOrderToRedis();
		return $this->recv_syncAllDeductOrderToRedis();
	}
	
	public function send_syncAllDeductOrderToRedis(){
		
		$this->initInvocation("syncAllDeductOrderToRedis");
		$args = new \com\vip\vop\sync\InventoryOccupiedOrderSyncService_syncAllDeductOrderToRedis_args();
		
		$this->send_base($args);
	}
	
	public function recv_syncAllDeductOrderToRedis(){
		
		$result = new \com\vip\vop\sync\InventoryOccupiedOrderSyncService_syncAllDeductOrderToRedis_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncAllOccupiedOrderToRedis(){
		
		$this->send_syncAllOccupiedOrderToRedis();
		return $this->recv_syncAllOccupiedOrderToRedis();
	}
	
	public function send_syncAllOccupiedOrderToRedis(){
		
		$this->initInvocation("syncAllOccupiedOrderToRedis");
		$args = new \com\vip\vop\sync\InventoryOccupiedOrderSyncService_syncAllOccupiedOrderToRedis_args();
		
		$this->send_base($args);
	}
	
	public function recv_syncAllOccupiedOrderToRedis(){
		
		$result = new \com\vip\vop\sync\InventoryOccupiedOrderSyncService_syncAllOccupiedOrderToRedis_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncIncDeductOrderToRedis( $startIndex){
		
		$this->send_syncIncDeductOrderToRedis( $startIndex);
		return $this->recv_syncIncDeductOrderToRedis();
	}
	
	public function send_syncIncDeductOrderToRedis( $startIndex){
		
		$this->initInvocation("syncIncDeductOrderToRedis");
		$args = new \com\vip\vop\sync\InventoryOccupiedOrderSyncService_syncIncDeductOrderToRedis_args();
		
		$args->startIndex = $startIndex;
		
		$this->send_base($args);
	}
	
	public function recv_syncIncDeductOrderToRedis(){
		
		$result = new \com\vip\vop\sync\InventoryOccupiedOrderSyncService_syncIncDeductOrderToRedis_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncIncOccupiedOrderToRedis( $startIndex){
		
		$this->send_syncIncOccupiedOrderToRedis( $startIndex);
		return $this->recv_syncIncOccupiedOrderToRedis();
	}
	
	public function send_syncIncOccupiedOrderToRedis( $startIndex){
		
		$this->initInvocation("syncIncOccupiedOrderToRedis");
		$args = new \com\vip\vop\sync\InventoryOccupiedOrderSyncService_syncIncOccupiedOrderToRedis_args();
		
		$args->startIndex = $startIndex;
		
		$this->send_base($args);
	}
	
	public function recv_syncIncOccupiedOrderToRedis(){
		
		$result = new \com\vip\vop\sync\InventoryOccupiedOrderSyncService_syncIncOccupiedOrderToRedis_result();
		$this->receive_base($result);
		
	}
	
	
}




class InventoryOccupiedOrderSyncService_delDeductOrderRedisKey_args {
	
	static $_TSPEC;
	public $intervalDays = null;
	public $startIndex = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'intervalDays'
			),
			2 => array(
			'var' => 'startIndex'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['intervalDays'])){
				
				$this->intervalDays = $vals['intervalDays'];
			}
			
			
			if (isset($vals['startIndex'])){
				
				$this->startIndex = $vals['startIndex'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI32($this->intervalDays); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readI64($this->startIndex); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('intervalDays');
		$xfer += $output->writeI32($this->intervalDays);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('startIndex');
		$xfer += $output->writeI64($this->startIndex);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class InventoryOccupiedOrderSyncService_delExpiredOccupiedOrdersFromRedis_args {
	
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




class InventoryOccupiedOrderSyncService_healthCheck_args {
	
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




class InventoryOccupiedOrderSyncService_syncAllDeductOrderToRedis_args {
	
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




class InventoryOccupiedOrderSyncService_syncAllOccupiedOrderToRedis_args {
	
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




class InventoryOccupiedOrderSyncService_syncIncDeductOrderToRedis_args {
	
	static $_TSPEC;
	public $startIndex = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'startIndex'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['startIndex'])){
				
				$this->startIndex = $vals['startIndex'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->startIndex); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('startIndex');
		$xfer += $output->writeI64($this->startIndex);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class InventoryOccupiedOrderSyncService_syncIncOccupiedOrderToRedis_args {
	
	static $_TSPEC;
	public $startIndex = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'startIndex'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['startIndex'])){
				
				$this->startIndex = $vals['startIndex'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->startIndex); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('startIndex');
		$xfer += $output->writeI64($this->startIndex);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class InventoryOccupiedOrderSyncService_delDeductOrderRedisKey_result {
	
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




class InventoryOccupiedOrderSyncService_delExpiredOccupiedOrdersFromRedis_result {
	
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




class InventoryOccupiedOrderSyncService_healthCheck_result {
	
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




class InventoryOccupiedOrderSyncService_syncAllDeductOrderToRedis_result {
	
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




class InventoryOccupiedOrderSyncService_syncAllOccupiedOrderToRedis_result {
	
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




class InventoryOccupiedOrderSyncService_syncIncDeductOrderToRedis_result {
	
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




class InventoryOccupiedOrderSyncService_syncIncOccupiedOrderToRedis_result {
	
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