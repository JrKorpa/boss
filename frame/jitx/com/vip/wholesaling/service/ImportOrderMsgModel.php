<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\wholesaling\service;

class ImportOrderMsgModel {
	
	static $_TSPEC;
	public $errorMsg = null;
	public $success = null;
	public $msg = null;
	public $orderSnapshotId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'errorMsg'
			),
			2 => array(
			'var' => 'success'
			),
			3 => array(
			'var' => 'msg'
			),
			4 => array(
			'var' => 'orderSnapshotId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['errorMsg'])){
				
				$this->errorMsg = $vals['errorMsg'];
			}
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
			if (isset($vals['msg'])){
				
				$this->msg = $vals['msg'];
			}
			
			
			if (isset($vals['orderSnapshotId'])){
				
				$this->orderSnapshotId = $vals['orderSnapshotId'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'ImportOrderMsgModel';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("errorMsg" == $schemeField){
				
				$needSkip = false;
				
				$this->errorMsg = array();
				$_size0 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem0 = null;
						$input->readString($elem0);
						
						$this->errorMsg[$_size0++] = $elem0;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
			}
			
			
			
			
			if ("success" == $schemeField){
				
				$needSkip = false;
				$input->readBool($this->success);
				
			}
			
			
			
			
			if ("msg" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->msg);
				
			}
			
			
			
			
			if ("orderSnapshotId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->orderSnapshotId); 
				
			}
			
			
			
			if($needSkip){
				
				\Osp\Protocol\ProtocolUtil::skip($input);
			}
			
			$input->readFieldEnd();
		}
		
		$input->readStructEnd();
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->errorMsg !== null) {
			
			$xfer += $output->writeFieldBegin('errorMsg');
			
			if (!is_array($this->errorMsg)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->errorMsg as $iter0){
				
				$xfer += $output->writeString($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			$xfer += $output->writeBool($this->success);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->msg !== null) {
			
			$xfer += $output->writeFieldBegin('msg');
			$xfer += $output->writeString($this->msg);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->orderSnapshotId !== null) {
			
			$xfer += $output->writeFieldBegin('orderSnapshotId');
			$xfer += $output->writeI64($this->orderSnapshotId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>