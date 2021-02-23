<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\omni\store;
interface VendorStoreEditServiceIf{
	
	
	public function batchSaveOrUpdate( $vendorId, $stores, $operator);
	
	public function batchUpdateSupportMaxSpeed( $vendorId, $updateStoreSns, $cancelStoreSns, $maxSpeedCarrier);
	
	public function cancelSupportMaxSpeed( $vendorId, $storeSns);
	
	public function delete( $vendorId, $storeCode);
	
	public function get( $storeCode);
	
	public function getByVendorId( $vendorId);
	
	public function getNeedPushStores( $vendorId, $lastId);
	
	public function getNeedSyncGeocodeStoreList( $vendorId, $lastId);
	
	public function getSupportMaxSpeedStores( $vendorId, $maxSpeedCarrier);
	
	public function healthCheck();
	
	public function pushStoreToTps(\com\vip\vop\omni\store\VendorStoreInfoDo $store);
	
	public function query(\com\vip\vop\omni\store\StoreQueryReq $storeQueryReq, $page, $limit);
	
	public function save(\com\vip\vop\omni\store\VendorStoreInfoDo $store);
	
	public function searchVendorStoreList(\com\vip\vop\omni\store\SearchVendorStoreListRequest $request);
	
	public function syncAddressLibrary();
	
	public function syncStoreGeocode(\com\vip\vop\omni\store\VendorStoreInfoDo $store);
	
	public function update(\com\vip\vop\omni\store\VendorStoreInfoDo $store);
	
}

class _VendorStoreEditServiceClient extends \Osp\Base\OspStub implements \com\vip\vop\omni\store\VendorStoreEditServiceIf{
	
	public function __construct(){
		
		parent::__construct("com.vip.vop.omni.store.VendorStoreEditService", "1.0.0");
	}
	
	
	public function batchSaveOrUpdate( $vendorId, $stores, $operator){
		
		$this->send_batchSaveOrUpdate( $vendorId, $stores, $operator);
		return $this->recv_batchSaveOrUpdate();
	}
	
	public function send_batchSaveOrUpdate( $vendorId, $stores, $operator){
		
		$this->initInvocation("batchSaveOrUpdate");
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_batchSaveOrUpdate_args();
		
		$args->vendorId = $vendorId;
		
		$args->stores = $stores;
		
		$args->operator = $operator;
		
		$this->send_base($args);
	}
	
	public function recv_batchSaveOrUpdate(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_batchSaveOrUpdate_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function batchUpdateSupportMaxSpeed( $vendorId, $updateStoreSns, $cancelStoreSns, $maxSpeedCarrier){
		
		$this->send_batchUpdateSupportMaxSpeed( $vendorId, $updateStoreSns, $cancelStoreSns, $maxSpeedCarrier);
		return $this->recv_batchUpdateSupportMaxSpeed();
	}
	
	public function send_batchUpdateSupportMaxSpeed( $vendorId, $updateStoreSns, $cancelStoreSns, $maxSpeedCarrier){
		
		$this->initInvocation("batchUpdateSupportMaxSpeed");
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_batchUpdateSupportMaxSpeed_args();
		
		$args->vendorId = $vendorId;
		
		$args->updateStoreSns = $updateStoreSns;
		
		$args->cancelStoreSns = $cancelStoreSns;
		
		$args->maxSpeedCarrier = $maxSpeedCarrier;
		
		$this->send_base($args);
	}
	
	public function recv_batchUpdateSupportMaxSpeed(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_batchUpdateSupportMaxSpeed_result();
		$this->receive_base($result);
		
	}
	
	
	public function cancelSupportMaxSpeed( $vendorId, $storeSns){
		
		$this->send_cancelSupportMaxSpeed( $vendorId, $storeSns);
		return $this->recv_cancelSupportMaxSpeed();
	}
	
	public function send_cancelSupportMaxSpeed( $vendorId, $storeSns){
		
		$this->initInvocation("cancelSupportMaxSpeed");
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_cancelSupportMaxSpeed_args();
		
		$args->vendorId = $vendorId;
		
		$args->storeSns = $storeSns;
		
		$this->send_base($args);
	}
	
	public function recv_cancelSupportMaxSpeed(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_cancelSupportMaxSpeed_result();
		$this->receive_base($result);
		
	}
	
	
	public function delete( $vendorId, $storeCode){
		
		$this->send_delete( $vendorId, $storeCode);
		return $this->recv_delete();
	}
	
	public function send_delete( $vendorId, $storeCode){
		
		$this->initInvocation("delete");
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_delete_args();
		
		$args->vendorId = $vendorId;
		
		$args->storeCode = $storeCode;
		
		$this->send_base($args);
	}
	
	public function recv_delete(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_delete_result();
		$this->receive_base($result);
		
	}
	
	
	public function get( $storeCode){
		
		$this->send_get( $storeCode);
		return $this->recv_get();
	}
	
	public function send_get( $storeCode){
		
		$this->initInvocation("get");
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_get_args();
		
		$args->storeCode = $storeCode;
		
		$this->send_base($args);
	}
	
	public function recv_get(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_get_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getByVendorId( $vendorId){
		
		$this->send_getByVendorId( $vendorId);
		return $this->recv_getByVendorId();
	}
	
	public function send_getByVendorId( $vendorId){
		
		$this->initInvocation("getByVendorId");
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_getByVendorId_args();
		
		$args->vendorId = $vendorId;
		
		$this->send_base($args);
	}
	
	public function recv_getByVendorId(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_getByVendorId_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getNeedPushStores( $vendorId, $lastId){
		
		$this->send_getNeedPushStores( $vendorId, $lastId);
		return $this->recv_getNeedPushStores();
	}
	
	public function send_getNeedPushStores( $vendorId, $lastId){
		
		$this->initInvocation("getNeedPushStores");
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_getNeedPushStores_args();
		
		$args->vendorId = $vendorId;
		
		$args->lastId = $lastId;
		
		$this->send_base($args);
	}
	
	public function recv_getNeedPushStores(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_getNeedPushStores_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getNeedSyncGeocodeStoreList( $vendorId, $lastId){
		
		$this->send_getNeedSyncGeocodeStoreList( $vendorId, $lastId);
		return $this->recv_getNeedSyncGeocodeStoreList();
	}
	
	public function send_getNeedSyncGeocodeStoreList( $vendorId, $lastId){
		
		$this->initInvocation("getNeedSyncGeocodeStoreList");
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_getNeedSyncGeocodeStoreList_args();
		
		$args->vendorId = $vendorId;
		
		$args->lastId = $lastId;
		
		$this->send_base($args);
	}
	
	public function recv_getNeedSyncGeocodeStoreList(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_getNeedSyncGeocodeStoreList_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getSupportMaxSpeedStores( $vendorId, $maxSpeedCarrier){
		
		$this->send_getSupportMaxSpeedStores( $vendorId, $maxSpeedCarrier);
		return $this->recv_getSupportMaxSpeedStores();
	}
	
	public function send_getSupportMaxSpeedStores( $vendorId, $maxSpeedCarrier){
		
		$this->initInvocation("getSupportMaxSpeedStores");
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_getSupportMaxSpeedStores_args();
		
		$args->vendorId = $vendorId;
		
		$args->maxSpeedCarrier = $maxSpeedCarrier;
		
		$this->send_base($args);
	}
	
	public function recv_getSupportMaxSpeedStores(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_getSupportMaxSpeedStores_result();
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
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_healthCheck_args();
		
		$this->send_base($args);
	}
	
	public function recv_healthCheck(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_healthCheck_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function pushStoreToTps(\com\vip\vop\omni\store\VendorStoreInfoDo $store){
		
		$this->send_pushStoreToTps( $store);
		return $this->recv_pushStoreToTps();
	}
	
	public function send_pushStoreToTps(\com\vip\vop\omni\store\VendorStoreInfoDo $store){
		
		$this->initInvocation("pushStoreToTps");
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_pushStoreToTps_args();
		
		$args->store = $store;
		
		$this->send_base($args);
	}
	
	public function recv_pushStoreToTps(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_pushStoreToTps_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function query(\com\vip\vop\omni\store\StoreQueryReq $storeQueryReq, $page, $limit){
		
		$this->send_query( $storeQueryReq, $page, $limit);
		return $this->recv_query();
	}
	
	public function send_query(\com\vip\vop\omni\store\StoreQueryReq $storeQueryReq, $page, $limit){
		
		$this->initInvocation("query");
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_query_args();
		
		$args->storeQueryReq = $storeQueryReq;
		
		$args->page = $page;
		
		$args->limit = $limit;
		
		$this->send_base($args);
	}
	
	public function recv_query(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_query_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function save(\com\vip\vop\omni\store\VendorStoreInfoDo $store){
		
		$this->send_save( $store);
		return $this->recv_save();
	}
	
	public function send_save(\com\vip\vop\omni\store\VendorStoreInfoDo $store){
		
		$this->initInvocation("save");
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_save_args();
		
		$args->store = $store;
		
		$this->send_base($args);
	}
	
	public function recv_save(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_save_result();
		$this->receive_base($result);
		
	}
	
	
	public function searchVendorStoreList(\com\vip\vop\omni\store\SearchVendorStoreListRequest $request){
		
		$this->send_searchVendorStoreList( $request);
		return $this->recv_searchVendorStoreList();
	}
	
	public function send_searchVendorStoreList(\com\vip\vop\omni\store\SearchVendorStoreListRequest $request){
		
		$this->initInvocation("searchVendorStoreList");
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_searchVendorStoreList_args();
		
		$args->request = $request;
		
		$this->send_base($args);
	}
	
	public function recv_searchVendorStoreList(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_searchVendorStoreList_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function syncAddressLibrary(){
		
		$this->send_syncAddressLibrary();
		return $this->recv_syncAddressLibrary();
	}
	
	public function send_syncAddressLibrary(){
		
		$this->initInvocation("syncAddressLibrary");
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_syncAddressLibrary_args();
		
		$this->send_base($args);
	}
	
	public function recv_syncAddressLibrary(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_syncAddressLibrary_result();
		$this->receive_base($result);
		
	}
	
	
	public function syncStoreGeocode(\com\vip\vop\omni\store\VendorStoreInfoDo $store){
		
		$this->send_syncStoreGeocode( $store);
		return $this->recv_syncStoreGeocode();
	}
	
	public function send_syncStoreGeocode(\com\vip\vop\omni\store\VendorStoreInfoDo $store){
		
		$this->initInvocation("syncStoreGeocode");
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_syncStoreGeocode_args();
		
		$args->store = $store;
		
		$this->send_base($args);
	}
	
	public function recv_syncStoreGeocode(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_syncStoreGeocode_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function update(\com\vip\vop\omni\store\VendorStoreInfoDo $store){
		
		$this->send_update( $store);
		return $this->recv_update();
	}
	
	public function send_update(\com\vip\vop\omni\store\VendorStoreInfoDo $store){
		
		$this->initInvocation("update");
		$args = new \com\vip\vop\omni\store\VendorStoreEditService_update_args();
		
		$args->store = $store;
		
		$this->send_base($args);
	}
	
	public function recv_update(){
		
		$result = new \com\vip\vop\omni\store\VendorStoreEditService_update_result();
		$this->receive_base($result);
		
	}
	
	
}




class VendorStoreEditService_batchSaveOrUpdate_args {
	
	static $_TSPEC;
	public $vendorId = null;
	public $stores = null;
	public $operator = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'stores'
			),
			3 => array(
			'var' => 'operator'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['stores'])){
				
				$this->stores = $vals['stores'];
			}
			
			
			if (isset($vals['operator'])){
				
				$this->operator = $vals['operator'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->vendorId); 
			
		}
		
		
		
		
		if(true) {
			
			
			$this->stores = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					
					$elem0 = new \com\vip\vop\omni\store\VendorStoreInfoDo();
					$elem0->read($input);
					
					$this->stores[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->operator);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI64($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->stores !== null) {
			
			$xfer += $output->writeFieldBegin('stores');
			
			if (!is_array($this->stores)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->stores as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->operator !== null) {
			
			$xfer += $output->writeFieldBegin('operator');
			$xfer += $output->writeString($this->operator);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class VendorStoreEditService_batchUpdateSupportMaxSpeed_args {
	
	static $_TSPEC;
	public $vendorId = null;
	public $updateStoreSns = null;
	public $cancelStoreSns = null;
	public $maxSpeedCarrier = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'updateStoreSns'
			),
			3 => array(
			'var' => 'cancelStoreSns'
			),
			4 => array(
			'var' => 'maxSpeedCarrier'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['updateStoreSns'])){
				
				$this->updateStoreSns = $vals['updateStoreSns'];
			}
			
			
			if (isset($vals['cancelStoreSns'])){
				
				$this->cancelStoreSns = $vals['cancelStoreSns'];
			}
			
			
			if (isset($vals['maxSpeedCarrier'])){
				
				$this->maxSpeedCarrier = $vals['maxSpeedCarrier'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->vendorId); 
			
		}
		
		
		
		
		if(true) {
			
			
			$this->updateStoreSns = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					$input->readString($elem0);
					
					$this->updateStoreSns[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			
			$this->cancelStoreSns = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					$input->readString($elem1);
					
					$this->cancelStoreSns[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->maxSpeedCarrier);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->vendorId !== null) {
			
			$xfer += $output->writeFieldBegin('vendorId');
			$xfer += $output->writeI64($this->vendorId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->updateStoreSns !== null) {
			
			$xfer += $output->writeFieldBegin('updateStoreSns');
			
			if (!is_array($this->updateStoreSns)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->updateStoreSns as $iter0){
				
				$xfer += $output->writeString($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->cancelStoreSns !== null) {
			
			$xfer += $output->writeFieldBegin('cancelStoreSns');
			
			if (!is_array($this->cancelStoreSns)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->cancelStoreSns as $iter0){
				
				$xfer += $output->writeString($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->maxSpeedCarrier !== null) {
			
			$xfer += $output->writeFieldBegin('maxSpeedCarrier');
			$xfer += $output->writeString($this->maxSpeedCarrier);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class VendorStoreEditService_cancelSupportMaxSpeed_args {
	
	static $_TSPEC;
	public $vendorId = null;
	public $storeSns = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'storeSns'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['storeSns'])){
				
				$this->storeSns = $vals['storeSns'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->vendorId); 
			
		}
		
		
		
		
		if(true) {
			
			
			$this->storeSns = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					$input->readString($elem0);
					
					$this->storeSns[$_size0++] = $elem0;
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
		
		if($this->vendorId !== null) {
			
			$xfer += $output->writeFieldBegin('vendorId');
			$xfer += $output->writeI64($this->vendorId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->storeSns !== null) {
			
			$xfer += $output->writeFieldBegin('storeSns');
			
			if (!is_array($this->storeSns)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->storeSns as $iter0){
				
				$xfer += $output->writeString($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class VendorStoreEditService_delete_args {
	
	static $_TSPEC;
	public $vendorId = null;
	public $storeCode = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'storeCode'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['storeCode'])){
				
				$this->storeCode = $vals['storeCode'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->vendorId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->storeCode);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI64($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->storeCode !== null) {
			
			$xfer += $output->writeFieldBegin('storeCode');
			$xfer += $output->writeString($this->storeCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class VendorStoreEditService_get_args {
	
	static $_TSPEC;
	public $storeCode = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'storeCode'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['storeCode'])){
				
				$this->storeCode = $vals['storeCode'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->storeCode);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->storeCode !== null) {
			
			$xfer += $output->writeFieldBegin('storeCode');
			$xfer += $output->writeString($this->storeCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class VendorStoreEditService_getByVendorId_args {
	
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




class VendorStoreEditService_getNeedPushStores_args {
	
	static $_TSPEC;
	public $vendorId = null;
	public $lastId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'lastId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['lastId'])){
				
				$this->lastId = $vals['lastId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->vendorId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readI64($this->lastId); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI64($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('lastId');
		$xfer += $output->writeI64($this->lastId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class VendorStoreEditService_getNeedSyncGeocodeStoreList_args {
	
	static $_TSPEC;
	public $vendorId = null;
	public $lastId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'lastId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['lastId'])){
				
				$this->lastId = $vals['lastId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->vendorId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readI64($this->lastId); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('vendorId');
		$xfer += $output->writeI64($this->vendorId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('lastId');
		$xfer += $output->writeI64($this->lastId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class VendorStoreEditService_getSupportMaxSpeedStores_args {
	
	static $_TSPEC;
	public $vendorId = null;
	public $maxSpeedCarrier = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'vendorId'
			),
			2 => array(
			'var' => 'maxSpeedCarrier'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['maxSpeedCarrier'])){
				
				$this->maxSpeedCarrier = $vals['maxSpeedCarrier'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->vendorId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->maxSpeedCarrier);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->vendorId !== null) {
			
			$xfer += $output->writeFieldBegin('vendorId');
			$xfer += $output->writeI64($this->vendorId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->maxSpeedCarrier !== null) {
			
			$xfer += $output->writeFieldBegin('maxSpeedCarrier');
			$xfer += $output->writeString($this->maxSpeedCarrier);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class VendorStoreEditService_healthCheck_args {
	
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




class VendorStoreEditService_pushStoreToTps_args {
	
	static $_TSPEC;
	public $store = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'store'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['store'])){
				
				$this->store = $vals['store'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->store = new \com\vip\vop\omni\store\VendorStoreInfoDo();
			$this->store->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->store !== null) {
			
			$xfer += $output->writeFieldBegin('store');
			
			if (!is_object($this->store)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->store->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class VendorStoreEditService_query_args {
	
	static $_TSPEC;
	public $storeQueryReq = null;
	public $page = null;
	public $limit = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'storeQueryReq'
			),
			2 => array(
			'var' => 'page'
			),
			3 => array(
			'var' => 'limit'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['storeQueryReq'])){
				
				$this->storeQueryReq = $vals['storeQueryReq'];
			}
			
			
			if (isset($vals['page'])){
				
				$this->page = $vals['page'];
			}
			
			
			if (isset($vals['limit'])){
				
				$this->limit = $vals['limit'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->storeQueryReq = new \com\vip\vop\omni\store\StoreQueryReq();
			$this->storeQueryReq->read($input);
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->page); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->limit); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('storeQueryReq');
		
		if (!is_object($this->storeQueryReq)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->storeQueryReq->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->page !== null) {
			
			$xfer += $output->writeFieldBegin('page');
			$xfer += $output->writeI32($this->page);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->limit !== null) {
			
			$xfer += $output->writeFieldBegin('limit');
			$xfer += $output->writeI32($this->limit);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class VendorStoreEditService_save_args {
	
	static $_TSPEC;
	public $store = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'store'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['store'])){
				
				$this->store = $vals['store'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->store = new \com\vip\vop\omni\store\VendorStoreInfoDo();
			$this->store->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->store !== null) {
			
			$xfer += $output->writeFieldBegin('store');
			
			if (!is_object($this->store)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->store->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class VendorStoreEditService_searchVendorStoreList_args {
	
	static $_TSPEC;
	public $request = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'request'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['request'])){
				
				$this->request = $vals['request'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->request = new \com\vip\vop\omni\store\SearchVendorStoreListRequest();
			$this->request->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('request');
		
		if (!is_object($this->request)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->request->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class VendorStoreEditService_syncAddressLibrary_args {
	
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




class VendorStoreEditService_syncStoreGeocode_args {
	
	static $_TSPEC;
	public $store = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'store'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['store'])){
				
				$this->store = $vals['store'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->store = new \com\vip\vop\omni\store\VendorStoreInfoDo();
			$this->store->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->store !== null) {
			
			$xfer += $output->writeFieldBegin('store');
			
			if (!is_object($this->store)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->store->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class VendorStoreEditService_update_args {
	
	static $_TSPEC;
	public $store = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'store'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['store'])){
				
				$this->store = $vals['store'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->store = new \com\vip\vop\omni\store\VendorStoreInfoDo();
			$this->store->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->store !== null) {
			
			$xfer += $output->writeFieldBegin('store');
			
			if (!is_object($this->store)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->store->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class VendorStoreEditService_batchSaveOrUpdate_result {
	
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
					
					$elem0 = new \com\vip\vop\omni\store\StoreFailItem();
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




class VendorStoreEditService_batchUpdateSupportMaxSpeed_result {
	
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




class VendorStoreEditService_cancelSupportMaxSpeed_result {
	
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




class VendorStoreEditService_delete_result {
	
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




class VendorStoreEditService_get_result {
	
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
			
			
			$this->success = new \com\vip\vop\omni\store\VendorStoreInfoDo();
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




class VendorStoreEditService_getByVendorId_result {
	
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
					
					$elem0 = new \com\vip\vop\omni\store\StoreItemInfo();
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




class VendorStoreEditService_getNeedPushStores_result {
	
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
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					
					$elem1 = new \com\vip\vop\omni\store\VendorStoreInfoDo();
					$elem1->read($input);
					
					$this->success[$_size1++] = $elem1;
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




class VendorStoreEditService_getNeedSyncGeocodeStoreList_result {
	
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
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					
					$elem1 = new \com\vip\vop\omni\store\VendorStoreInfoDo();
					$elem1->read($input);
					
					$this->success[$_size1++] = $elem1;
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




class VendorStoreEditService_getSupportMaxSpeedStores_result {
	
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
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					
					$elem1 = new \com\vip\vop\omni\store\StoreItemInfo();
					$elem1->read($input);
					
					$this->success[$_size1++] = $elem1;
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




class VendorStoreEditService_healthCheck_result {
	
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




class VendorStoreEditService_pushStoreToTps_result {
	
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




class VendorStoreEditService_query_result {
	
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
			
			
			$this->success = new \com\vip\vop\omni\store\StoreQueryResp();
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




class VendorStoreEditService_save_result {
	
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




class VendorStoreEditService_searchVendorStoreList_result {
	
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
					
					$elem0 = new \com\vip\vop\omni\store\VendorStoreInfoDo();
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




class VendorStoreEditService_syncAddressLibrary_result {
	
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




class VendorStoreEditService_syncStoreGeocode_result {
	
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




class VendorStoreEditService_update_result {
	
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