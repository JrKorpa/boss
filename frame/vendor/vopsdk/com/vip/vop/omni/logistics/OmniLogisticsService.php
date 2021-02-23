<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\omni\logistics;
interface OmniLogisticsServiceIf{
	
	
	public function getOrderLogisticsTrack(\com\vip\vop\omni\logistics\LogisticsTrackRequest $trackRequest);
	
	public function healthCheck();
	
	public function receiveRouteInfo(\com\vip\vop\omni\logistics\WaybillRoute $waybillRoute);
	
}

class _OmniLogisticsServiceClient extends \Osp\Base\OspStub implements \com\vip\vop\omni\logistics\OmniLogisticsServiceIf{
	
	public function __construct(){
		
		parent::__construct("com.vip.vop.omni.logistics.OmniLogisticsService", "1.0.0");
	}
	
	
	public function getOrderLogisticsTrack(\com\vip\vop\omni\logistics\LogisticsTrackRequest $trackRequest){
		
		$this->send_getOrderLogisticsTrack( $trackRequest);
		return $this->recv_getOrderLogisticsTrack();
	}
	
	public function send_getOrderLogisticsTrack(\com\vip\vop\omni\logistics\LogisticsTrackRequest $trackRequest){
		
		$this->initInvocation("getOrderLogisticsTrack");
		$args = new \com\vip\vop\omni\logistics\OmniLogisticsService_getOrderLogisticsTrack_args();
		
		$args->trackRequest = $trackRequest;
		
		$this->send_base($args);
	}
	
	public function recv_getOrderLogisticsTrack(){
		
		$result = new \com\vip\vop\omni\logistics\OmniLogisticsService_getOrderLogisticsTrack_result();
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
		$args = new \com\vip\vop\omni\logistics\OmniLogisticsService_healthCheck_args();
		
		$this->send_base($args);
	}
	
	public function recv_healthCheck(){
		
		$result = new \com\vip\vop\omni\logistics\OmniLogisticsService_healthCheck_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function receiveRouteInfo(\com\vip\vop\omni\logistics\WaybillRoute $waybillRoute){
		
		$this->send_receiveRouteInfo( $waybillRoute);
		return $this->recv_receiveRouteInfo();
	}
	
	public function send_receiveRouteInfo(\com\vip\vop\omni\logistics\WaybillRoute $waybillRoute){
		
		$this->initInvocation("receiveRouteInfo");
		$args = new \com\vip\vop\omni\logistics\OmniLogisticsService_receiveRouteInfo_args();
		
		$args->waybillRoute = $waybillRoute;
		
		$this->send_base($args);
	}
	
	public function recv_receiveRouteInfo(){
		
		$result = new \com\vip\vop\omni\logistics\OmniLogisticsService_receiveRouteInfo_result();
		$this->receive_base($result);
		
	}
	
	
}




class OmniLogisticsService_getOrderLogisticsTrack_args {
	
	static $_TSPEC;
	public $trackRequest = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'trackRequest'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['trackRequest'])){
				
				$this->trackRequest = $vals['trackRequest'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->trackRequest = new \com\vip\vop\omni\logistics\LogisticsTrackRequest();
			$this->trackRequest->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('trackRequest');
		
		if (!is_object($this->trackRequest)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->trackRequest->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class OmniLogisticsService_healthCheck_args {
	
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




class OmniLogisticsService_receiveRouteInfo_args {
	
	static $_TSPEC;
	public $waybillRoute = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'waybillRoute'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['waybillRoute'])){
				
				$this->waybillRoute = $vals['waybillRoute'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->waybillRoute = new \com\vip\vop\omni\logistics\WaybillRoute();
			$this->waybillRoute->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('waybillRoute');
		
		if (!is_object($this->waybillRoute)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->waybillRoute->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class OmniLogisticsService_getOrderLogisticsTrack_result {
	
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
			
			
			$this->success = new \com\vip\vop\omni\logistics\LogisticsTrackResponse();
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




class OmniLogisticsService_healthCheck_result {
	
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




class OmniLogisticsService_receiveRouteInfo_result {
	
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