<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\product;
interface DieselProductSelfServiceIf{
	
	
	public function deleteEmailConfig( $partnerId, $email);
	
	public function healthCheck();
	
	public function pushPriceToVdgByIdList( $idList);
	
	public function pushProductToVdgBySkuIdList( $skuIdList);
	
	public function pushProductToVdgBySpuIdList( $spuIdList);
	
	public function saveEmailConfig(\com\vip\vop\vcloud\product\EmailConfig $config);
	
	public function updateProductSku( $skuId, $map);
	
	public function updateProductSkuAttr( $skuId, $map);
	
	public function updateProductSkuStatus(\com\vip\vop\vcloud\product\ProductSkuStatus $criteria,\com\vip\vop\vcloud\product\ProductSkuStatus $skuStatus);
	
	public function updateProductSpu( $spuId, $map);
	
	public function updateProductSpuAttr( $spuId, $map);
	
	public function updateSftpFileLog( $logIdList, $logType, $status);
	
}

class _DieselProductSelfServiceClient extends \Osp\Base\OspStub implements \com\vip\vop\vcloud\product\DieselProductSelfServiceIf{
	
	public function __construct(){
		
		parent::__construct("com.vip.vop.vcloud.product.DieselProductSelfService", "1.0.0");
	}
	
	
	public function deleteEmailConfig( $partnerId, $email){
		
		$this->send_deleteEmailConfig( $partnerId, $email);
		return $this->recv_deleteEmailConfig();
	}
	
	public function send_deleteEmailConfig( $partnerId, $email){
		
		$this->initInvocation("deleteEmailConfig");
		$args = new \com\vip\vop\vcloud\product\DieselProductSelfService_deleteEmailConfig_args();
		
		$args->partnerId = $partnerId;
		
		$args->email = $email;
		
		$this->send_base($args);
	}
	
	public function recv_deleteEmailConfig(){
		
		$result = new \com\vip\vop\vcloud\product\DieselProductSelfService_deleteEmailConfig_result();
		$this->receive_base($result);
		
	}
	
	
	public function healthCheck(){
		
		$this->send_healthCheck();
		return $this->recv_healthCheck();
	}
	
	public function send_healthCheck(){
		
		$this->initInvocation("healthCheck");
		$args = new \com\vip\vop\vcloud\product\DieselProductSelfService_healthCheck_args();
		
		$this->send_base($args);
	}
	
	public function recv_healthCheck(){
		
		$result = new \com\vip\vop\vcloud\product\DieselProductSelfService_healthCheck_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function pushPriceToVdgByIdList( $idList){
		
		$this->send_pushPriceToVdgByIdList( $idList);
		return $this->recv_pushPriceToVdgByIdList();
	}
	
	public function send_pushPriceToVdgByIdList( $idList){
		
		$this->initInvocation("pushPriceToVdgByIdList");
		$args = new \com\vip\vop\vcloud\product\DieselProductSelfService_pushPriceToVdgByIdList_args();
		
		$args->idList = $idList;
		
		$this->send_base($args);
	}
	
	public function recv_pushPriceToVdgByIdList(){
		
		$result = new \com\vip\vop\vcloud\product\DieselProductSelfService_pushPriceToVdgByIdList_result();
		$this->receive_base($result);
		
	}
	
	
	public function pushProductToVdgBySkuIdList( $skuIdList){
		
		$this->send_pushProductToVdgBySkuIdList( $skuIdList);
		return $this->recv_pushProductToVdgBySkuIdList();
	}
	
	public function send_pushProductToVdgBySkuIdList( $skuIdList){
		
		$this->initInvocation("pushProductToVdgBySkuIdList");
		$args = new \com\vip\vop\vcloud\product\DieselProductSelfService_pushProductToVdgBySkuIdList_args();
		
		$args->skuIdList = $skuIdList;
		
		$this->send_base($args);
	}
	
	public function recv_pushProductToVdgBySkuIdList(){
		
		$result = new \com\vip\vop\vcloud\product\DieselProductSelfService_pushProductToVdgBySkuIdList_result();
		$this->receive_base($result);
		
	}
	
	
	public function pushProductToVdgBySpuIdList( $spuIdList){
		
		$this->send_pushProductToVdgBySpuIdList( $spuIdList);
		return $this->recv_pushProductToVdgBySpuIdList();
	}
	
	public function send_pushProductToVdgBySpuIdList( $spuIdList){
		
		$this->initInvocation("pushProductToVdgBySpuIdList");
		$args = new \com\vip\vop\vcloud\product\DieselProductSelfService_pushProductToVdgBySpuIdList_args();
		
		$args->spuIdList = $spuIdList;
		
		$this->send_base($args);
	}
	
	public function recv_pushProductToVdgBySpuIdList(){
		
		$result = new \com\vip\vop\vcloud\product\DieselProductSelfService_pushProductToVdgBySpuIdList_result();
		$this->receive_base($result);
		
	}
	
	
	public function saveEmailConfig(\com\vip\vop\vcloud\product\EmailConfig $config){
		
		$this->send_saveEmailConfig( $config);
		return $this->recv_saveEmailConfig();
	}
	
	public function send_saveEmailConfig(\com\vip\vop\vcloud\product\EmailConfig $config){
		
		$this->initInvocation("saveEmailConfig");
		$args = new \com\vip\vop\vcloud\product\DieselProductSelfService_saveEmailConfig_args();
		
		$args->config = $config;
		
		$this->send_base($args);
	}
	
	public function recv_saveEmailConfig(){
		
		$result = new \com\vip\vop\vcloud\product\DieselProductSelfService_saveEmailConfig_result();
		$this->receive_base($result);
		
	}
	
	
	public function updateProductSku( $skuId, $map){
		
		$this->send_updateProductSku( $skuId, $map);
		return $this->recv_updateProductSku();
	}
	
	public function send_updateProductSku( $skuId, $map){
		
		$this->initInvocation("updateProductSku");
		$args = new \com\vip\vop\vcloud\product\DieselProductSelfService_updateProductSku_args();
		
		$args->skuId = $skuId;
		
		$args->map = $map;
		
		$this->send_base($args);
	}
	
	public function recv_updateProductSku(){
		
		$result = new \com\vip\vop\vcloud\product\DieselProductSelfService_updateProductSku_result();
		$this->receive_base($result);
		
	}
	
	
	public function updateProductSkuAttr( $skuId, $map){
		
		$this->send_updateProductSkuAttr( $skuId, $map);
		return $this->recv_updateProductSkuAttr();
	}
	
	public function send_updateProductSkuAttr( $skuId, $map){
		
		$this->initInvocation("updateProductSkuAttr");
		$args = new \com\vip\vop\vcloud\product\DieselProductSelfService_updateProductSkuAttr_args();
		
		$args->skuId = $skuId;
		
		$args->map = $map;
		
		$this->send_base($args);
	}
	
	public function recv_updateProductSkuAttr(){
		
		$result = new \com\vip\vop\vcloud\product\DieselProductSelfService_updateProductSkuAttr_result();
		$this->receive_base($result);
		
	}
	
	
	public function updateProductSkuStatus(\com\vip\vop\vcloud\product\ProductSkuStatus $criteria,\com\vip\vop\vcloud\product\ProductSkuStatus $skuStatus){
		
		$this->send_updateProductSkuStatus( $criteria, $skuStatus);
		return $this->recv_updateProductSkuStatus();
	}
	
	public function send_updateProductSkuStatus(\com\vip\vop\vcloud\product\ProductSkuStatus $criteria,\com\vip\vop\vcloud\product\ProductSkuStatus $skuStatus){
		
		$this->initInvocation("updateProductSkuStatus");
		$args = new \com\vip\vop\vcloud\product\DieselProductSelfService_updateProductSkuStatus_args();
		
		$args->criteria = $criteria;
		
		$args->skuStatus = $skuStatus;
		
		$this->send_base($args);
	}
	
	public function recv_updateProductSkuStatus(){
		
		$result = new \com\vip\vop\vcloud\product\DieselProductSelfService_updateProductSkuStatus_result();
		$this->receive_base($result);
		
	}
	
	
	public function updateProductSpu( $spuId, $map){
		
		$this->send_updateProductSpu( $spuId, $map);
		return $this->recv_updateProductSpu();
	}
	
	public function send_updateProductSpu( $spuId, $map){
		
		$this->initInvocation("updateProductSpu");
		$args = new \com\vip\vop\vcloud\product\DieselProductSelfService_updateProductSpu_args();
		
		$args->spuId = $spuId;
		
		$args->map = $map;
		
		$this->send_base($args);
	}
	
	public function recv_updateProductSpu(){
		
		$result = new \com\vip\vop\vcloud\product\DieselProductSelfService_updateProductSpu_result();
		$this->receive_base($result);
		
	}
	
	
	public function updateProductSpuAttr( $spuId, $map){
		
		$this->send_updateProductSpuAttr( $spuId, $map);
		return $this->recv_updateProductSpuAttr();
	}
	
	public function send_updateProductSpuAttr( $spuId, $map){
		
		$this->initInvocation("updateProductSpuAttr");
		$args = new \com\vip\vop\vcloud\product\DieselProductSelfService_updateProductSpuAttr_args();
		
		$args->spuId = $spuId;
		
		$args->map = $map;
		
		$this->send_base($args);
	}
	
	public function recv_updateProductSpuAttr(){
		
		$result = new \com\vip\vop\vcloud\product\DieselProductSelfService_updateProductSpuAttr_result();
		$this->receive_base($result);
		
	}
	
	
	public function updateSftpFileLog( $logIdList, $logType, $status){
		
		$this->send_updateSftpFileLog( $logIdList, $logType, $status);
		return $this->recv_updateSftpFileLog();
	}
	
	public function send_updateSftpFileLog( $logIdList, $logType, $status){
		
		$this->initInvocation("updateSftpFileLog");
		$args = new \com\vip\vop\vcloud\product\DieselProductSelfService_updateSftpFileLog_args();
		
		$args->logIdList = $logIdList;
		
		$args->logType = $logType;
		
		$args->status = $status;
		
		$this->send_base($args);
	}
	
	public function recv_updateSftpFileLog(){
		
		$result = new \com\vip\vop\vcloud\product\DieselProductSelfService_updateSftpFileLog_result();
		$this->receive_base($result);
		
	}
	
	
}




class DieselProductSelfService_deleteEmailConfig_args {
	
	static $_TSPEC;
	public $partnerId = null;
	public $email = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'partnerId'
			),
			2 => array(
			'var' => 'email'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['partnerId'])){
				
				$this->partnerId = $vals['partnerId'];
			}
			
			
			if (isset($vals['email'])){
				
				$this->email = $vals['email'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->partnerId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->email);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('partnerId');
		$xfer += $output->writeI64($this->partnerId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('email');
		$xfer += $output->writeString($this->email);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class DieselProductSelfService_healthCheck_args {
	
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




class DieselProductSelfService_pushPriceToVdgByIdList_args {
	
	static $_TSPEC;
	public $idList = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'idList'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['idList'])){
				
				$this->idList = $vals['idList'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->idList = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					$input->readI64($elem0); 
					
					$this->idList[$_size0++] = $elem0;
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
		
		$xfer += $output->writeFieldBegin('idList');
		
		if (!is_array($this->idList)){
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$output->writeListBegin();
		foreach ($this->idList as $iter0){
			
			$xfer += $output->writeI64($iter0);
			
		}
		
		$output->writeListEnd();
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class DieselProductSelfService_pushProductToVdgBySkuIdList_args {
	
	static $_TSPEC;
	public $skuIdList = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'skuIdList'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['skuIdList'])){
				
				$this->skuIdList = $vals['skuIdList'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->skuIdList = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					$input->readI64($elem1); 
					
					$this->skuIdList[$_size1++] = $elem1;
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
		
		$xfer += $output->writeFieldBegin('skuIdList');
		
		if (!is_array($this->skuIdList)){
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$output->writeListBegin();
		foreach ($this->skuIdList as $iter0){
			
			$xfer += $output->writeI64($iter0);
			
		}
		
		$output->writeListEnd();
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class DieselProductSelfService_pushProductToVdgBySpuIdList_args {
	
	static $_TSPEC;
	public $spuIdList = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'spuIdList'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['spuIdList'])){
				
				$this->spuIdList = $vals['spuIdList'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->spuIdList = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					$input->readI64($elem1); 
					
					$this->spuIdList[$_size1++] = $elem1;
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
		
		$xfer += $output->writeFieldBegin('spuIdList');
		
		if (!is_array($this->spuIdList)){
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$output->writeListBegin();
		foreach ($this->spuIdList as $iter0){
			
			$xfer += $output->writeI64($iter0);
			
		}
		
		$output->writeListEnd();
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class DieselProductSelfService_saveEmailConfig_args {
	
	static $_TSPEC;
	public $config = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'config'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['config'])){
				
				$this->config = $vals['config'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->config = new \com\vip\vop\vcloud\product\EmailConfig();
			$this->config->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('config');
		
		if (!is_object($this->config)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->config->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class DieselProductSelfService_updateProductSku_args {
	
	static $_TSPEC;
	public $skuId = null;
	public $map = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'skuId'
			),
			2 => array(
			'var' => 'map'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['skuId'])){
				
				$this->skuId = $vals['skuId'];
			}
			
			
			if (isset($vals['map'])){
				
				$this->map = $vals['map'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->skuId); 
			
		}
		
		
		
		
		if(true) {
			
			
			$this->map = array();
			$input->readMapBegin();
			while(true){
				
				try{
					
					$key0 = '';
					$input->readString($key0);
					
					$val0 = '';
					$input->readString($val0);
					
					$this->map[$key0] = $val0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readMapEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->skuId !== null) {
			
			$xfer += $output->writeFieldBegin('skuId');
			$xfer += $output->writeI64($this->skuId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('map');
		
		if (!is_array($this->map)){
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$output->writeMapBegin();
		foreach ($this->map as $kiter0 => $viter0){
			
			$xfer += $output->writeString($kiter0);
			
			$xfer += $output->writeString($viter0);
			
		}
		
		$output->writeMapEnd();
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class DieselProductSelfService_updateProductSkuAttr_args {
	
	static $_TSPEC;
	public $skuId = null;
	public $map = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'skuId'
			),
			2 => array(
			'var' => 'map'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['skuId'])){
				
				$this->skuId = $vals['skuId'];
			}
			
			
			if (isset($vals['map'])){
				
				$this->map = $vals['map'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->skuId); 
			
		}
		
		
		
		
		if(true) {
			
			
			$this->map = array();
			$input->readMapBegin();
			while(true){
				
				try{
					
					$key1 = '';
					$input->readString($key1);
					
					$val1 = '';
					$input->readString($val1);
					
					$this->map[$key1] = $val1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readMapEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->skuId !== null) {
			
			$xfer += $output->writeFieldBegin('skuId');
			$xfer += $output->writeI64($this->skuId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('map');
		
		if (!is_array($this->map)){
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$output->writeMapBegin();
		foreach ($this->map as $kiter0 => $viter0){
			
			$xfer += $output->writeString($kiter0);
			
			$xfer += $output->writeString($viter0);
			
		}
		
		$output->writeMapEnd();
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class DieselProductSelfService_updateProductSkuStatus_args {
	
	static $_TSPEC;
	public $criteria = null;
	public $skuStatus = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'criteria'
			),
			2 => array(
			'var' => 'skuStatus'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['criteria'])){
				
				$this->criteria = $vals['criteria'];
			}
			
			
			if (isset($vals['skuStatus'])){
				
				$this->skuStatus = $vals['skuStatus'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->criteria = new \com\vip\vop\vcloud\product\ProductSkuStatus();
			$this->criteria->read($input);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->skuStatus = new \com\vip\vop\vcloud\product\ProductSkuStatus();
			$this->skuStatus->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('criteria');
		
		if (!is_object($this->criteria)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->criteria->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('skuStatus');
		
		if (!is_object($this->skuStatus)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->skuStatus->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class DieselProductSelfService_updateProductSpu_args {
	
	static $_TSPEC;
	public $spuId = null;
	public $map = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'spuId'
			),
			2 => array(
			'var' => 'map'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['spuId'])){
				
				$this->spuId = $vals['spuId'];
			}
			
			
			if (isset($vals['map'])){
				
				$this->map = $vals['map'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->spuId); 
			
		}
		
		
		
		
		if(true) {
			
			
			$this->map = array();
			$input->readMapBegin();
			while(true){
				
				try{
					
					$key0 = '';
					$input->readString($key0);
					
					$val0 = '';
					$input->readString($val0);
					
					$this->map[$key0] = $val0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readMapEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->spuId !== null) {
			
			$xfer += $output->writeFieldBegin('spuId');
			$xfer += $output->writeI64($this->spuId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('map');
		
		if (!is_array($this->map)){
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$output->writeMapBegin();
		foreach ($this->map as $kiter0 => $viter0){
			
			$xfer += $output->writeString($kiter0);
			
			$xfer += $output->writeString($viter0);
			
		}
		
		$output->writeMapEnd();
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class DieselProductSelfService_updateProductSpuAttr_args {
	
	static $_TSPEC;
	public $spuId = null;
	public $map = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'spuId'
			),
			2 => array(
			'var' => 'map'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['spuId'])){
				
				$this->spuId = $vals['spuId'];
			}
			
			
			if (isset($vals['map'])){
				
				$this->map = $vals['map'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->spuId); 
			
		}
		
		
		
		
		if(true) {
			
			
			$this->map = array();
			$input->readMapBegin();
			while(true){
				
				try{
					
					$key1 = '';
					$input->readString($key1);
					
					$val1 = '';
					$input->readString($val1);
					
					$this->map[$key1] = $val1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readMapEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->spuId !== null) {
			
			$xfer += $output->writeFieldBegin('spuId');
			$xfer += $output->writeI64($this->spuId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('map');
		
		if (!is_array($this->map)){
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$output->writeMapBegin();
		foreach ($this->map as $kiter0 => $viter0){
			
			$xfer += $output->writeString($kiter0);
			
			$xfer += $output->writeString($viter0);
			
		}
		
		$output->writeMapEnd();
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class DieselProductSelfService_updateSftpFileLog_args {
	
	static $_TSPEC;
	public $logIdList = null;
	public $logType = null;
	public $status = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'logIdList'
			),
			2 => array(
			'var' => 'logType'
			),
			3 => array(
			'var' => 'status'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['logIdList'])){
				
				$this->logIdList = $vals['logIdList'];
			}
			
			
			if (isset($vals['logType'])){
				
				$this->logType = $vals['logType'];
			}
			
			
			if (isset($vals['status'])){
				
				$this->status = $vals['status'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->logIdList = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					$input->readI64($elem1); 
					
					$this->logIdList[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readByte($this->logType); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readByte($this->status); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->logIdList !== null) {
			
			$xfer += $output->writeFieldBegin('logIdList');
			
			if (!is_array($this->logIdList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->logIdList as $iter0){
				
				$xfer += $output->writeI64($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->logType !== null) {
			
			$xfer += $output->writeFieldBegin('logType');
			$xfer += $output->writeByte($this->logType);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->status !== null) {
			
			$xfer += $output->writeFieldBegin('status');
			$xfer += $output->writeByte($this->status);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class DieselProductSelfService_deleteEmailConfig_result {
	
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




class DieselProductSelfService_healthCheck_result {
	
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




class DieselProductSelfService_pushPriceToVdgByIdList_result {
	
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




class DieselProductSelfService_pushProductToVdgBySkuIdList_result {
	
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




class DieselProductSelfService_pushProductToVdgBySpuIdList_result {
	
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




class DieselProductSelfService_saveEmailConfig_result {
	
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




class DieselProductSelfService_updateProductSku_result {
	
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




class DieselProductSelfService_updateProductSkuAttr_result {
	
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




class DieselProductSelfService_updateProductSkuStatus_result {
	
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




class DieselProductSelfService_updateProductSpu_result {
	
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




class DieselProductSelfService_updateProductSpuAttr_result {
	
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




class DieselProductSelfService_updateSftpFileLog_result {
	
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