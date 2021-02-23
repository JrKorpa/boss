<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\haitao\base\osp\service\record;

class GoodResultParam {
	
	static $_TSPEC;
	public $goodResultAfterRecord = null;
	public $htGoodRecordModel = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'goodResultAfterRecord'
			),
			2 => array(
			'var' => 'htGoodRecordModel'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['goodResultAfterRecord'])){
				
				$this->goodResultAfterRecord = $vals['goodResultAfterRecord'];
			}
			
			
			if (isset($vals['htGoodRecordModel'])){
				
				$this->htGoodRecordModel = $vals['htGoodRecordModel'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GoodResultParam';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("goodResultAfterRecord" == $schemeField){
				
				$needSkip = false;
				
				$this->goodResultAfterRecord = new \com\vip\haitao\base\osp\service\record\GoodResultAfterRecord();
				$this->goodResultAfterRecord->read($input);
				
			}
			
			
			
			
			if ("htGoodRecordModel" == $schemeField){
				
				$needSkip = false;
				
				$this->htGoodRecordModel = new \com\vip\haitao\base\osp\service\record\HtGoodRecordModel();
				$this->htGoodRecordModel->read($input);
				
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
		
		if($this->goodResultAfterRecord !== null) {
			
			$xfer += $output->writeFieldBegin('goodResultAfterRecord');
			
			if (!is_object($this->goodResultAfterRecord)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->goodResultAfterRecord->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->htGoodRecordModel !== null) {
			
			$xfer += $output->writeFieldBegin('htGoodRecordModel');
			
			if (!is_object($this->htGoodRecordModel)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->htGoodRecordModel->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>