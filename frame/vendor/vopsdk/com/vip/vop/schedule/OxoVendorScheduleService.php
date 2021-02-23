<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\schedule;
interface OxoVendorScheduleServiceIf{
	
	
	public function getScheduleListByCooperationNo( $vendorId, $cooperationNo, $warehouse);
	
	public function getSkuByScheduleIdAndBarcode( $scheduleId, $barcode);
	
	public function getSkuInventoryPushStatus( $vendorId, $cooperationNo, $warehouse, $barcode);
	
	public function healthCheck();
	
	public function setSkuInventoryPushStatusImported( $vendorId, $scheduleId, $barcode);
	
	public function syncVendorScheduleSkus();
	
	public function syncVendorScheduleSkusToCache();
	
	public function syncVendorSchedules();
	
}

class _OxoVendorScheduleServiceClient extends \Osp\Base\OspStub implements \com\vip\vop\schedule\OxoVendorScheduleServiceIf{
	
	public function __construct(){
		
		parent::__construct("com.vip.vop.schedule.OxoVendorScheduleService", "1.0.0");
	}
	
	
	public function getScheduleListByCooperationNo( $vendorId, $cooperationNo, $warehouse){
		
		$this->send_getScheduleListByCooperationNo( $vendorId, $cooperationNo, $warehouse);
		return $this->recv_getScheduleListByCooperationNo();
	}
	
	public function send_getScheduleListByCooperationNo( $vendorId, $cooperationNo, $warehouse){
		
		$this->initInvocation("getScheduleListByCooperationNo");
		$args = new \com\vip\vop\schedule\OxoVendorScheduleService_getScheduleListByCooperationNo_args();
		
		$args->vendorId = $vendorId;
		
		$args->cooperationNo = $cooperationNo;
		
		$args->warehouse = $warehouse;
		
		$this->send_base($args);
	}
	
	public function recv_getScheduleListByCooperationNo(){
		
		$result = new \com\vip\vop\schedule\OxoVendorScheduleService_getScheduleListByCooperationNo_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getSkuByScheduleIdAndBarcode( $scheduleId, $barcode){
		
		$this->send_getSkuByScheduleIdAndBarcode( $scheduleId, $barcode);
		return $this->recv_getSkuByScheduleIdAndBarcode();
	}
	
	public function send_getSkuByScheduleIdAndBarcode( $scheduleId, $barcode){
		
		$this->initInvocation("getSkuByScheduleIdAndBarcode");
		$args = new \com\vip\vop\schedule\OxoVendorScheduleService_getSkuByScheduleIdAndBarcode_args();
		
		$args->scheduleId = $scheduleId;
		
		$args->barcode = $barcode;
		
		$this->send_base($args);
	}
	
	public function recv_getSkuByScheduleIdAndBarcode(){
		
		$result = new \com\vip\vop\schedule\OxoVendorScheduleService_getSkuByScheduleIdAndBarcode_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getSkuInventoryPushStatus( $vendorId, $cooperationNo, $warehouse, $barcode){
		
		$this->send_getSkuInventoryPushStatus( $vendorId, $cooperationNo, $warehouse, $barcode);
		return $this->recv_getSkuInventoryPushStatus();
	}
	
	public function send_getSkuInventoryPushStatus( $vendorId, $cooperationNo, $warehouse, $barcode){
		
		$this->initInvocation("getSkuInventoryPushStatus");
		$args = new \com\vip\vop\schedule\OxoVendorScheduleService_getSkuInventoryPushStatus_args();
		
		$args->vendorId = $vendorId;
		
		$args->cooperationNo = $cooperationNo;
		
		$args->warehouse = $warehouse;
		
		$args->barcode = $barcode;
		
		$this->send_base($args);
	}
	
	public function recv_getSkuInventoryPushStatus(){
		
		$result = new \com\vip\vop\schedule\OxoVendorScheduleService_getSkuInventoryPushStatus_result();
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
		$args = new \com\vip\vop\schedule\OxoVendorScheduleService_healthCheck_args();
		
		$this->send_base($args);
	}
	
	public function recv_healthCheck(){
		
		$result = new \com\vip\vop\schedule\OxoVendorScheduleService_healthCheck_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function setSkuInventoryPushStatusImported( $vendorId, $scheduleId, $barcode){
		
		$this->send_setSkuInventoryPushStatusImported( $vendorId, $scheduleId, $barcode);
		return $this->recv_setSkuInventoryPushStatusImported();
	}
	
	public function send_setSkuInventoryPushStatusImported( $vendorId, $scheduleId, $barcode){
		
		$this->initInvocation("setSkuInventoryPushStatusImported");
		$args = new \com\vip\vop\schedule\OxoVendorScheduleService_setSkuInventoryPushStatusImported_args();
		
		$args->vendorId = $vendorId;
		
		$args->scheduleId = $scheduleId;
		
		$args->barcode = $barcode;
		
		$this->send_base($args);
	}
	
	public function recv_setSkuInventoryPushStatusImported(){
		
		$result = new \com\vip\vop\schedule\OxoVendorScheduleService_setSkuInventoryPushStatusImported_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncVendorScheduleSkus(){
		
		$this->send_syncVendorScheduleSkus();
		return $this->recv_syncVendorScheduleSkus();
	}
	
	public function send_syncVendorScheduleSkus(){
		
		$this->initInvocation("syncVendorScheduleSkus");
		$args = new \com\vip\vop\schedule\OxoVendorScheduleService_syncVendorScheduleSkus_args();
		
		$this->send_base($args);
	}
	
	public function recv_syncVendorScheduleSkus(){
		
		$result = new \com\vip\vop\schedule\OxoVendorScheduleService_syncVendorScheduleSkus_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncVendorScheduleSkusToCache(){
		
		$this->send_syncVendorScheduleSkusToCache();
		return $this->recv_syncVendorScheduleSkusToCache();
	}
	
	public function send_syncVendorScheduleSkusToCache(){
		
		$this->initInvocation("syncVendorScheduleSkusToCache");
		$args = new \com\vip\vop\schedule\OxoVendorScheduleService_syncVendorScheduleSkusToCache_args();
		
		$this->send_base($args);
	}
	
	public function recv_syncVendorScheduleSkusToCache(){
		
		$result = new \com\vip\vop\schedule\OxoVendorScheduleService_syncVendorScheduleSkusToCache_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncVendorSchedules(){
		
		$this->send_syncVendorSchedules();
		return $this->recv_syncVendorSchedules();
	}
	
	public function send_syncVendorSchedules(){
		
		$this->initInvocation("syncVendorSchedules");
		$args = new \com\vip\vop\schedule\OxoVendorScheduleService_syncVendorSchedules_args();
		
		$this->send_base($args);
	}
	
	public function recv_syncVendorSchedules(){
		
		$result = new \com\vip\vop\schedule\OxoVendorScheduleService_syncVendorSchedules_result();
		$this->receive_base($result);
		
	}
	
	
}




class OxoVendorScheduleService_getScheduleListByCooperationNo_args {
	
	static $_TSPEC;
	public $vendorId = null;
	public $cooperationNo = null;
	public $warehouse = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'cooperationNo'
			),
			3 => array(
			'var' => 'warehouse'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['cooperationNo'])){
				
				$this->cooperationNo = $vals['cooperationNo'];
			}
			
			
			if (isset($vals['warehouse'])){
				
				$this->warehouse = $vals['warehouse'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->vendorId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readI64($this->cooperationNo); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->warehouse);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI64($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('cooperationNo');
		$xfer += $output->writeI64($this->cooperationNo);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->warehouse !== null) {
			
			$xfer += $output->writeFieldBegin('warehouse');
			$xfer += $output->writeString($this->warehouse);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class OxoVendorScheduleService_getSkuByScheduleIdAndBarcode_args {
	
	static $_TSPEC;
	public $scheduleId = null;
	public $barcode = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'scheduleId'
			),
			2 => array(
			'var' => 'barcode'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['scheduleId'])){
				
				$this->scheduleId = $vals['scheduleId'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->scheduleId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->barcode);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('scheduleId');
		$xfer += $output->writeI64($this->scheduleId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('barcode');
		$xfer += $output->writeString($this->barcode);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class OxoVendorScheduleService_getSkuInventoryPushStatus_args {
	
	static $_TSPEC;
	public $vendorId = null;
	public $cooperationNo = null;
	public $warehouse = null;
	public $barcode = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'cooperationNo'
			),
			3 => array(
			'var' => 'warehouse'
			),
			4 => array(
			'var' => 'barcode'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
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
			
			$input->readI64($this->vendorId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readI64($this->cooperationNo); 
			
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
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI64($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('cooperationNo');
		$xfer += $output->writeI64($this->cooperationNo);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('warehouse');
		$xfer += $output->writeString($this->warehouse);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('barcode');
		$xfer += $output->writeString($this->barcode);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class OxoVendorScheduleService_healthCheck_args {
	
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




class OxoVendorScheduleService_setSkuInventoryPushStatusImported_args {
	
	static $_TSPEC;
	public $vendorId = null;
	public $scheduleId = null;
	public $barcode = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'scheduleId'
			),
			3 => array(
			'var' => 'barcode'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['scheduleId'])){
				
				$this->scheduleId = $vals['scheduleId'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->vendorId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readI64($this->scheduleId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->barcode);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI64($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('scheduleId');
		$xfer += $output->writeI64($this->scheduleId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('barcode');
		$xfer += $output->writeString($this->barcode);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class OxoVendorScheduleService_syncVendorScheduleSkus_args {
	
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




class OxoVendorScheduleService_syncVendorScheduleSkusToCache_args {
	
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




class OxoVendorScheduleService_syncVendorSchedules_args {
	
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




class OxoVendorScheduleService_getScheduleListByCooperationNo_result {
	
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
					
					$elem0 = new \com\vip\vop\schedule\VendorSchedule();
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




class OxoVendorScheduleService_getSkuByScheduleIdAndBarcode_result {
	
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
			
			
			$this->success = new \com\vip\vop\schedule\VendorScheduleSku();
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




class OxoVendorScheduleService_getSkuInventoryPushStatus_result {
	
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
			
			$input->readI32($this->success); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('success');
		$xfer += $output->writeI32($this->success);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class OxoVendorScheduleService_healthCheck_result {
	
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




class OxoVendorScheduleService_setSkuInventoryPushStatusImported_result {
	
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




class OxoVendorScheduleService_syncVendorScheduleSkus_result {
	
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




class OxoVendorScheduleService_syncVendorScheduleSkusToCache_result {
	
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




class OxoVendorScheduleService_syncVendorSchedules_result {
	
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