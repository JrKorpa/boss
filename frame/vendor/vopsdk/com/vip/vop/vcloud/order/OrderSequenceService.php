<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\order;
interface OrderSequenceServiceIf{
	
	
	public function getLastUpdateTime( $name);
	
	public function getNextId( $name);
	
	public function getNextPid( $name);
	
	public function healthCheck();
	
	public function insertDieselOrderRecents( $list);
	
	public function updateLastUpdateTime(\com\vip\vop\vcloud\order\OrderSequence $sequence);
	
	public function updateSequence(\com\vip\vop\vcloud\order\OrderSequence $sequence);
	
}

class _OrderSequenceServiceClient extends \Osp\Base\OspStub implements \com\vip\vop\vcloud\order\OrderSequenceServiceIf{
	
	public function __construct(){
		
		parent::__construct("com.vip.vop.vcloud.order.OrderSequenceService", "1.0.0");
	}
	
	
	public function getLastUpdateTime( $name){
		
		$this->send_getLastUpdateTime( $name);
		return $this->recv_getLastUpdateTime();
	}
	
	public function send_getLastUpdateTime( $name){
		
		$this->initInvocation("getLastUpdateTime");
		$args = new \com\vip\vop\vcloud\order\OrderSequenceService_getLastUpdateTime_args();
		
		$args->name = $name;
		
		$this->send_base($args);
	}
	
	public function recv_getLastUpdateTime(){
		
		$result = new \com\vip\vop\vcloud\order\OrderSequenceService_getLastUpdateTime_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getNextId( $name){
		
		$this->send_getNextId( $name);
		return $this->recv_getNextId();
	}
	
	public function send_getNextId( $name){
		
		$this->initInvocation("getNextId");
		$args = new \com\vip\vop\vcloud\order\OrderSequenceService_getNextId_args();
		
		$args->name = $name;
		
		$this->send_base($args);
	}
	
	public function recv_getNextId(){
		
		$result = new \com\vip\vop\vcloud\order\OrderSequenceService_getNextId_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getNextPid( $name){
		
		$this->send_getNextPid( $name);
		return $this->recv_getNextPid();
	}
	
	public function send_getNextPid( $name){
		
		$this->initInvocation("getNextPid");
		$args = new \com\vip\vop\vcloud\order\OrderSequenceService_getNextPid_args();
		
		$args->name = $name;
		
		$this->send_base($args);
	}
	
	public function recv_getNextPid(){
		
		$result = new \com\vip\vop\vcloud\order\OrderSequenceService_getNextPid_result();
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
		$args = new \com\vip\vop\vcloud\order\OrderSequenceService_healthCheck_args();
		
		$this->send_base($args);
	}
	
	public function recv_healthCheck(){
		
		$result = new \com\vip\vop\vcloud\order\OrderSequenceService_healthCheck_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function insertDieselOrderRecents( $list){
		
		$this->send_insertDieselOrderRecents( $list);
		return $this->recv_insertDieselOrderRecents();
	}
	
	public function send_insertDieselOrderRecents( $list){
		
		$this->initInvocation("insertDieselOrderRecents");
		$args = new \com\vip\vop\vcloud\order\OrderSequenceService_insertDieselOrderRecents_args();
		
		$args->list = $list;
		
		$this->send_base($args);
	}
	
	public function recv_insertDieselOrderRecents(){
		
		$result = new \com\vip\vop\vcloud\order\OrderSequenceService_insertDieselOrderRecents_result();
		$this->receive_base($result);
		
	}
	
	
	public function updateLastUpdateTime(\com\vip\vop\vcloud\order\OrderSequence $sequence){
		
		$this->send_updateLastUpdateTime( $sequence);
		return $this->recv_updateLastUpdateTime();
	}
	
	public function send_updateLastUpdateTime(\com\vip\vop\vcloud\order\OrderSequence $sequence){
		
		$this->initInvocation("updateLastUpdateTime");
		$args = new \com\vip\vop\vcloud\order\OrderSequenceService_updateLastUpdateTime_args();
		
		$args->sequence = $sequence;
		
		$this->send_base($args);
	}
	
	public function recv_updateLastUpdateTime(){
		
		$result = new \com\vip\vop\vcloud\order\OrderSequenceService_updateLastUpdateTime_result();
		$this->receive_base($result);
		
	}
	
	
	public function updateSequence(\com\vip\vop\vcloud\order\OrderSequence $sequence){
		
		$this->send_updateSequence( $sequence);
		return $this->recv_updateSequence();
	}
	
	public function send_updateSequence(\com\vip\vop\vcloud\order\OrderSequence $sequence){
		
		$this->initInvocation("updateSequence");
		$args = new \com\vip\vop\vcloud\order\OrderSequenceService_updateSequence_args();
		
		$args->sequence = $sequence;
		
		$this->send_base($args);
	}
	
	public function recv_updateSequence(){
		
		$result = new \com\vip\vop\vcloud\order\OrderSequenceService_updateSequence_result();
		$this->receive_base($result);
		
	}
	
	
}




class OrderSequenceService_getLastUpdateTime_args {
	
	static $_TSPEC;
	public $name = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'name'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['name'])){
				
				$this->name = $vals['name'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->name);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('name');
		$xfer += $output->writeString($this->name);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class OrderSequenceService_getNextId_args {
	
	static $_TSPEC;
	public $name = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'name'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['name'])){
				
				$this->name = $vals['name'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->name);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('name');
		$xfer += $output->writeString($this->name);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class OrderSequenceService_getNextPid_args {
	
	static $_TSPEC;
	public $name = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'name'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['name'])){
				
				$this->name = $vals['name'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->name);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('name');
		$xfer += $output->writeString($this->name);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class OrderSequenceService_healthCheck_args {
	
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




class OrderSequenceService_insertDieselOrderRecents_args {
	
	static $_TSPEC;
	public $list = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'list'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['list'])){
				
				$this->list = $vals['list'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->list = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					
					$elem0 = new \com\vip\vop\vcloud\order\DieselOrderRecents();
					$elem0->read($input);
					
					$this->list[$_size0++] = $elem0;
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
		
		$xfer += $output->writeFieldBegin('list');
		
		if (!is_array($this->list)){
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$output->writeListBegin();
		foreach ($this->list as $iter0){
			
			
			if (!is_object($iter0)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $iter0->write($output);
			
		}
		
		$output->writeListEnd();
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class OrderSequenceService_updateLastUpdateTime_args {
	
	static $_TSPEC;
	public $sequence = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'sequence'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['sequence'])){
				
				$this->sequence = $vals['sequence'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->sequence = new \com\vip\vop\vcloud\order\OrderSequence();
			$this->sequence->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->sequence !== null) {
			
			$xfer += $output->writeFieldBegin('sequence');
			
			if (!is_object($this->sequence)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->sequence->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class OrderSequenceService_updateSequence_args {
	
	static $_TSPEC;
	public $sequence = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'sequence'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['sequence'])){
				
				$this->sequence = $vals['sequence'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->sequence = new \com\vip\vop\vcloud\order\OrderSequence();
			$this->sequence->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->sequence !== null) {
			
			$xfer += $output->writeFieldBegin('sequence');
			
			if (!is_object($this->sequence)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->sequence->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class OrderSequenceService_getLastUpdateTime_result {
	
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
			
			
			$this->success = new \com\vip\vop\vcloud\order\OrderSequence();
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




class OrderSequenceService_getNextId_result {
	
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
			
			
			$this->success = new \com\vip\vop\vcloud\order\OrderSequence();
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




class OrderSequenceService_getNextPid_result {
	
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
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			$xfer += $output->writeI32($this->success);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class OrderSequenceService_healthCheck_result {
	
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




class OrderSequenceService_insertDieselOrderRecents_result {
	
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




class OrderSequenceService_updateLastUpdateTime_result {
	
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




class OrderSequenceService_updateSequence_result {
	
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