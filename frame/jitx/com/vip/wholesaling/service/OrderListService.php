<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\wholesaling\service;
interface OrderListServiceIf{
	
	
	public function analyseOrderFile( $url, $fileType, $wholesalingTypeId);
	
	public function healthCheck();
	
	public function queryOrderList(\com\vip\wholesaling\service\OrderListQueryCondition $condition);
	
}

class _OrderListServiceClient extends \Osp\Base\OspStub implements \com\vip\wholesaling\service\OrderListServiceIf{
	
	public function __construct(){
		
		parent::__construct("com.vip.wholesaling.service.OrderListService", "1.0.0");
	}
	
	
	public function analyseOrderFile( $url, $fileType, $wholesalingTypeId){
		
		$this->send_analyseOrderFile( $url, $fileType, $wholesalingTypeId);
		return $this->recv_analyseOrderFile();
	}
	
	public function send_analyseOrderFile( $url, $fileType, $wholesalingTypeId){
		
		$this->initInvocation("analyseOrderFile");
		$args = new \com\vip\wholesaling\service\OrderListService_analyseOrderFile_args();
		
		$args->url = $url;
		
		$args->fileType = $fileType;
		
		$args->wholesalingTypeId = $wholesalingTypeId;
		
		$this->send_base($args);
	}
	
	public function recv_analyseOrderFile(){
		
		$result = new \com\vip\wholesaling\service\OrderListService_analyseOrderFile_result();
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
		$args = new \com\vip\wholesaling\service\OrderListService_healthCheck_args();
		
		$this->send_base($args);
	}
	
	public function recv_healthCheck(){
		
		$result = new \com\vip\wholesaling\service\OrderListService_healthCheck_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function queryOrderList(\com\vip\wholesaling\service\OrderListQueryCondition $condition){
		
		$this->send_queryOrderList( $condition);
		return $this->recv_queryOrderList();
	}
	
	public function send_queryOrderList(\com\vip\wholesaling\service\OrderListQueryCondition $condition){
		
		$this->initInvocation("queryOrderList");
		$args = new \com\vip\wholesaling\service\OrderListService_queryOrderList_args();
		
		$args->condition = $condition;
		
		$this->send_base($args);
	}
	
	public function recv_queryOrderList(){
		
		$result = new \com\vip\wholesaling\service\OrderListService_queryOrderList_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
}




class OrderListService_analyseOrderFile_args {
	
	static $_TSPEC;
	public $url = null;
	public $fileType = null;
	public $wholesalingTypeId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'url'
			),
			2 => array(
			'var' => 'fileType'
			),
			3 => array(
			'var' => 'wholesalingTypeId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['url'])){
				
				$this->url = $vals['url'];
			}
			
			
			if (isset($vals['fileType'])){
				
				$this->fileType = $vals['fileType'];
			}
			
			
			if (isset($vals['wholesalingTypeId'])){
				
				$this->wholesalingTypeId = $vals['wholesalingTypeId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->url);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->fileType);
			
		}
		
		
		
		
		if(true) {
			
			$input->readI64($this->wholesalingTypeId); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('url');
		$xfer += $output->writeString($this->url);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('fileType');
		$xfer += $output->writeString($this->fileType);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('wholesalingTypeId');
		$xfer += $output->writeI64($this->wholesalingTypeId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class OrderListService_healthCheck_args {
	
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




class OrderListService_queryOrderList_args {
	
	static $_TSPEC;
	public $condition = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'condition'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['condition'])){
				
				$this->condition = $vals['condition'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->condition = new \com\vip\wholesaling\service\OrderListQueryCondition();
			$this->condition->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('condition');
		
		if (!is_object($this->condition)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->condition->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class OrderListService_analyseOrderFile_result {
	
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
			
			
			$this->success = new \com\vip\wholesaling\service\ImportOrderMsgModel();
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




class OrderListService_healthCheck_result {
	
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




class OrderListService_queryOrderList_result {
	
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
			
			
			$this->success = new \com\vip\wholesaling\service\OrderInfoListModel();
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




?>