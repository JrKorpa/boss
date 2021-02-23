<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\haitao\base\osp\service\record;

class GoodRecordResponse {
	
	static $_TSPEC;
	public $respCode = null;
	public $msg = null;
	public $goodRecordList = null;
	public $sizeSnList = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'respCode'
			),
			2 => array(
			'var' => 'msg'
			),
			3 => array(
			'var' => 'goodRecordList'
			),
			4 => array(
			'var' => 'sizeSnList'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['respCode'])){
				
				$this->respCode = $vals['respCode'];
			}
			
			
			if (isset($vals['msg'])){
				
				$this->msg = $vals['msg'];
			}
			
			
			if (isset($vals['goodRecordList'])){
				
				$this->goodRecordList = $vals['goodRecordList'];
			}
			
			
			if (isset($vals['sizeSnList'])){
				
				$this->sizeSnList = $vals['sizeSnList'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GoodRecordResponse';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("respCode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->respCode);
				
			}
			
			
			
			
			if ("msg" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->msg);
				
			}
			
			
			
			
			if ("goodRecordList" == $schemeField){
				
				$needSkip = false;
				
				$this->goodRecordList = array();
				$_size0 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem0 = null;
						
						$elem0 = new \com\vip\haitao\base\osp\service\record\HtGoodRecordModel();
						$elem0->read($input);
						
						$this->goodRecordList[$_size0++] = $elem0;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
			}
			
			
			
			
			if ("sizeSnList" == $schemeField){
				
				$needSkip = false;
				
				$this->sizeSnList = array();
				$_size1 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem1 = null;
						$input->readString($elem1);
						
						$this->sizeSnList[$_size1++] = $elem1;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
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
		
		if($this->respCode !== null) {
			
			$xfer += $output->writeFieldBegin('respCode');
			$xfer += $output->writeString($this->respCode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->msg !== null) {
			
			$xfer += $output->writeFieldBegin('msg');
			$xfer += $output->writeString($this->msg);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->goodRecordList !== null) {
			
			$xfer += $output->writeFieldBegin('goodRecordList');
			
			if (!is_array($this->goodRecordList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->goodRecordList as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->sizeSnList !== null) {
			
			$xfer += $output->writeFieldBegin('sizeSnList');
			
			if (!is_array($this->sizeSnList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->sizeSnList as $iter0){
				
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

?>