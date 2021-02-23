<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\sync;
interface ScheduleSalesSyncServiceIf{
	
	
	public function handleExpiredSchedulesAndSku();
	
	public function handleSellingJitSchedules();
	
	public function healthCheck();
	
	public function releaseSalesStock();
	
	public function syncInventoryTask();
	
	public function syncLockSkusToCache();
	
	public function syncMasterSalesSkus();
	
	public function syncMasterSalesSkusBySharding( $shardingTotalCount, $shardItem);
	
	public function syncSalesToRedis();
	
	public function syncScheduleSalesToRedis();
	
	public function syncScheduleSkuByVendorId( $vendorId);
	
	public function syncScheduleSkusToRedis();
	
	public function syncSkuByVendorIdAndScheduleId( $vendorId, $scheduleId, $warehouse);
	
	public function syncSpecialSales();
	
	public function syncSpecialSalesSku();
	
	public function syncVendorSchedule();
	
}

class _ScheduleSalesSyncServiceClient extends \Osp\Base\OspStub implements \com\vip\vop\sync\ScheduleSalesSyncServiceIf{
	
	public function __construct(){
		
		parent::__construct("com.vip.vop.sync.ScheduleSalesSyncService", "1.0.0");
	}
	
	
	public function handleExpiredSchedulesAndSku(){
		
		$this->send_handleExpiredSchedulesAndSku();
		return $this->recv_handleExpiredSchedulesAndSku();
	}
	
	public function send_handleExpiredSchedulesAndSku(){
		
		$this->initInvocation("handleExpiredSchedulesAndSku");
		$args = new \com\vip\vop\sync\ScheduleSalesSyncService_handleExpiredSchedulesAndSku_args();
		
		$this->send_base($args);
	}
	
	public function recv_handleExpiredSchedulesAndSku(){
		
		$result = new \com\vip\vop\sync\ScheduleSalesSyncService_handleExpiredSchedulesAndSku_result();
		$this->receive_base($result);
		
	}
	
	
	public function handleSellingJitSchedules(){
		
		$this->send_handleSellingJitSchedules();
		return $this->recv_handleSellingJitSchedules();
	}
	
	public function send_handleSellingJitSchedules(){
		
		$this->initInvocation("handleSellingJitSchedules");
		$args = new \com\vip\vop\sync\ScheduleSalesSyncService_handleSellingJitSchedules_args();
		
		$this->send_base($args);
	}
	
	public function recv_handleSellingJitSchedules(){
		
		$result = new \com\vip\vop\sync\ScheduleSalesSyncService_handleSellingJitSchedules_result();
		$this->receive_base($result);
		
	}
	
	
	public function healthCheck(){
		
		$this->send_healthCheck();
		return $this->recv_healthCheck();
	}
	
	public function send_healthCheck(){
		
		$this->initInvocation("healthCheck");
		$args = new \com\vip\vop\sync\ScheduleSalesSyncService_healthCheck_args();
		
		$this->send_base($args);
	}
	
	public function recv_healthCheck(){
		
		$result = new \com\vip\vop\sync\ScheduleSalesSyncService_healthCheck_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function releaseSalesStock(){
		
		$this->send_releaseSalesStock();
		return $this->recv_releaseSalesStock();
	}
	
	public function send_releaseSalesStock(){
		
		$this->initInvocation("releaseSalesStock");
		$args = new \com\vip\vop\sync\ScheduleSalesSyncService_releaseSalesStock_args();
		
		$this->send_base($args);
	}
	
	public function recv_releaseSalesStock(){
		
		$result = new \com\vip\vop\sync\ScheduleSalesSyncService_releaseSalesStock_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncInventoryTask(){
		
		$this->send_syncInventoryTask();
		return $this->recv_syncInventoryTask();
	}
	
	public function send_syncInventoryTask(){
		
		$this->initInvocation("syncInventoryTask");
		$args = new \com\vip\vop\sync\ScheduleSalesSyncService_syncInventoryTask_args();
		
		$this->send_base($args);
	}
	
	public function recv_syncInventoryTask(){
		
		$result = new \com\vip\vop\sync\ScheduleSalesSyncService_syncInventoryTask_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncLockSkusToCache(){
		
		$this->send_syncLockSkusToCache();
		return $this->recv_syncLockSkusToCache();
	}
	
	public function send_syncLockSkusToCache(){
		
		$this->initInvocation("syncLockSkusToCache");
		$args = new \com\vip\vop\sync\ScheduleSalesSyncService_syncLockSkusToCache_args();
		
		$this->send_base($args);
	}
	
	public function recv_syncLockSkusToCache(){
		
		$result = new \com\vip\vop\sync\ScheduleSalesSyncService_syncLockSkusToCache_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncMasterSalesSkus(){
		
		$this->send_syncMasterSalesSkus();
		return $this->recv_syncMasterSalesSkus();
	}
	
	public function send_syncMasterSalesSkus(){
		
		$this->initInvocation("syncMasterSalesSkus");
		$args = new \com\vip\vop\sync\ScheduleSalesSyncService_syncMasterSalesSkus_args();
		
		$this->send_base($args);
	}
	
	public function recv_syncMasterSalesSkus(){
		
		$result = new \com\vip\vop\sync\ScheduleSalesSyncService_syncMasterSalesSkus_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncMasterSalesSkusBySharding( $shardingTotalCount, $shardItem){
		
		$this->send_syncMasterSalesSkusBySharding( $shardingTotalCount, $shardItem);
		return $this->recv_syncMasterSalesSkusBySharding();
	}
	
	public function send_syncMasterSalesSkusBySharding( $shardingTotalCount, $shardItem){
		
		$this->initInvocation("syncMasterSalesSkusBySharding");
		$args = new \com\vip\vop\sync\ScheduleSalesSyncService_syncMasterSalesSkusBySharding_args();
		
		$args->shardingTotalCount = $shardingTotalCount;
		
		$args->shardItem = $shardItem;
		
		$this->send_base($args);
	}
	
	public function recv_syncMasterSalesSkusBySharding(){
		
		$result = new \com\vip\vop\sync\ScheduleSalesSyncService_syncMasterSalesSkusBySharding_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncSalesToRedis(){
		
		$this->send_syncSalesToRedis();
		return $this->recv_syncSalesToRedis();
	}
	
	public function send_syncSalesToRedis(){
		
		$this->initInvocation("syncSalesToRedis");
		$args = new \com\vip\vop\sync\ScheduleSalesSyncService_syncSalesToRedis_args();
		
		$this->send_base($args);
	}
	
	public function recv_syncSalesToRedis(){
		
		$result = new \com\vip\vop\sync\ScheduleSalesSyncService_syncSalesToRedis_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncScheduleSalesToRedis(){
		
		$this->send_syncScheduleSalesToRedis();
		return $this->recv_syncScheduleSalesToRedis();
	}
	
	public function send_syncScheduleSalesToRedis(){
		
		$this->initInvocation("syncScheduleSalesToRedis");
		$args = new \com\vip\vop\sync\ScheduleSalesSyncService_syncScheduleSalesToRedis_args();
		
		$this->send_base($args);
	}
	
	public function recv_syncScheduleSalesToRedis(){
		
		$result = new \com\vip\vop\sync\ScheduleSalesSyncService_syncScheduleSalesToRedis_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncScheduleSkuByVendorId( $vendorId){
		
		$this->send_syncScheduleSkuByVendorId( $vendorId);
		return $this->recv_syncScheduleSkuByVendorId();
	}
	
	public function send_syncScheduleSkuByVendorId( $vendorId){
		
		$this->initInvocation("syncScheduleSkuByVendorId");
		$args = new \com\vip\vop\sync\ScheduleSalesSyncService_syncScheduleSkuByVendorId_args();
		
		$args->vendorId = $vendorId;
		
		$this->send_base($args);
	}
	
	public function recv_syncScheduleSkuByVendorId(){
		
		$result = new \com\vip\vop\sync\ScheduleSalesSyncService_syncScheduleSkuByVendorId_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncScheduleSkusToRedis(){
		
		$this->send_syncScheduleSkusToRedis();
		return $this->recv_syncScheduleSkusToRedis();
	}
	
	public function send_syncScheduleSkusToRedis(){
		
		$this->initInvocation("syncScheduleSkusToRedis");
		$args = new \com\vip\vop\sync\ScheduleSalesSyncService_syncScheduleSkusToRedis_args();
		
		$this->send_base($args);
	}
	
	public function recv_syncScheduleSkusToRedis(){
		
		$result = new \com\vip\vop\sync\ScheduleSalesSyncService_syncScheduleSkusToRedis_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncSkuByVendorIdAndScheduleId( $vendorId, $scheduleId, $warehouse){
		
		$this->send_syncSkuByVendorIdAndScheduleId( $vendorId, $scheduleId, $warehouse);
		return $this->recv_syncSkuByVendorIdAndScheduleId();
	}
	
	public function send_syncSkuByVendorIdAndScheduleId( $vendorId, $scheduleId, $warehouse){
		
		$this->initInvocation("syncSkuByVendorIdAndScheduleId");
		$args = new \com\vip\vop\sync\ScheduleSalesSyncService_syncSkuByVendorIdAndScheduleId_args();
		
		$args->vendorId = $vendorId;
		
		$args->scheduleId = $scheduleId;
		
		$args->warehouse = $warehouse;
		
		$this->send_base($args);
	}
	
	public function recv_syncSkuByVendorIdAndScheduleId(){
		
		$result = new \com\vip\vop\sync\ScheduleSalesSyncService_syncSkuByVendorIdAndScheduleId_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncSpecialSales(){
		
		$this->send_syncSpecialSales();
		return $this->recv_syncSpecialSales();
	}
	
	public function send_syncSpecialSales(){
		
		$this->initInvocation("syncSpecialSales");
		$args = new \com\vip\vop\sync\ScheduleSalesSyncService_syncSpecialSales_args();
		
		$this->send_base($args);
	}
	
	public function recv_syncSpecialSales(){
		
		$result = new \com\vip\vop\sync\ScheduleSalesSyncService_syncSpecialSales_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncSpecialSalesSku(){
		
		$this->send_syncSpecialSalesSku();
		return $this->recv_syncSpecialSalesSku();
	}
	
	public function send_syncSpecialSalesSku(){
		
		$this->initInvocation("syncSpecialSalesSku");
		$args = new \com\vip\vop\sync\ScheduleSalesSyncService_syncSpecialSalesSku_args();
		
		$this->send_base($args);
	}
	
	public function recv_syncSpecialSalesSku(){
		
		$result = new \com\vip\vop\sync\ScheduleSalesSyncService_syncSpecialSalesSku_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncVendorSchedule(){
		
		$this->send_syncVendorSchedule();
		return $this->recv_syncVendorSchedule();
	}
	
	public function send_syncVendorSchedule(){
		
		$this->initInvocation("syncVendorSchedule");
		$args = new \com\vip\vop\sync\ScheduleSalesSyncService_syncVendorSchedule_args();
		
		$this->send_base($args);
	}
	
	public function recv_syncVendorSchedule(){
		
		$result = new \com\vip\vop\sync\ScheduleSalesSyncService_syncVendorSchedule_result();
		$this->receive_base($result);
		
	}
	
	
}




class ScheduleSalesSyncService_handleExpiredSchedulesAndSku_args {
	
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




class ScheduleSalesSyncService_handleSellingJitSchedules_args {
	
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




class ScheduleSalesSyncService_healthCheck_args {
	
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




class ScheduleSalesSyncService_releaseSalesStock_args {
	
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




class ScheduleSalesSyncService_syncInventoryTask_args {
	
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




class ScheduleSalesSyncService_syncLockSkusToCache_args {
	
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




class ScheduleSalesSyncService_syncMasterSalesSkus_args {
	
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




class ScheduleSalesSyncService_syncMasterSalesSkusBySharding_args {
	
	static $_TSPEC;
	public $shardingTotalCount = null;
	public $shardItem = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'shardingTotalCount'
			),
			2 => array(
			'var' => 'shardItem'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['shardingTotalCount'])){
				
				$this->shardingTotalCount = $vals['shardingTotalCount'];
			}
			
			
			if (isset($vals['shardItem'])){
				
				$this->shardItem = $vals['shardItem'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI32($this->shardingTotalCount); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->shardItem); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('shardingTotalCount');
		$xfer += $output->writeI32($this->shardingTotalCount);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->shardItem !== null) {
			
			$xfer += $output->writeFieldBegin('shardItem');
			$xfer += $output->writeI32($this->shardItem);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ScheduleSalesSyncService_syncSalesToRedis_args {
	
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




class ScheduleSalesSyncService_syncScheduleSalesToRedis_args {
	
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




class ScheduleSalesSyncService_syncScheduleSkuByVendorId_args {
	
	static $_TSPEC;
	public $vendorId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->vendorId); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI64($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ScheduleSalesSyncService_syncScheduleSkusToRedis_args {
	
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




class ScheduleSalesSyncService_syncSkuByVendorIdAndScheduleId_args {
	
	static $_TSPEC;
	public $vendorId = null;
	public $scheduleId = null;
	public $warehouse = null;
	
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
			'var' => 'warehouse'
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
			
			
			if (isset($vals['warehouse'])){
				
				$this->warehouse = $vals['warehouse'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI32($this->vendorId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->scheduleId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->warehouse);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI32($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('scheduleId');
		$xfer += $output->writeI32($this->scheduleId);
		
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




class ScheduleSalesSyncService_syncSpecialSales_args {
	
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




class ScheduleSalesSyncService_syncSpecialSalesSku_args {
	
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




class ScheduleSalesSyncService_syncVendorSchedule_args {
	
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




class ScheduleSalesSyncService_handleExpiredSchedulesAndSku_result {
	
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




class ScheduleSalesSyncService_handleSellingJitSchedules_result {
	
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




class ScheduleSalesSyncService_healthCheck_result {
	
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




class ScheduleSalesSyncService_releaseSalesStock_result {
	
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




class ScheduleSalesSyncService_syncInventoryTask_result {
	
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




class ScheduleSalesSyncService_syncLockSkusToCache_result {
	
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




class ScheduleSalesSyncService_syncMasterSalesSkus_result {
	
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




class ScheduleSalesSyncService_syncMasterSalesSkusBySharding_result {
	
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




class ScheduleSalesSyncService_syncSalesToRedis_result {
	
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




class ScheduleSalesSyncService_syncScheduleSalesToRedis_result {
	
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




class ScheduleSalesSyncService_syncScheduleSkuByVendorId_result {
	
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




class ScheduleSalesSyncService_syncScheduleSkusToRedis_result {
	
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




class ScheduleSalesSyncService_syncSkuByVendorIdAndScheduleId_result {
	
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




class ScheduleSalesSyncService_syncSpecialSales_result {
	
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




class ScheduleSalesSyncService_syncSpecialSalesSku_result {
	
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




class ScheduleSalesSyncService_syncVendorSchedule_result {
	
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