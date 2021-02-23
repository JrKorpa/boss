<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\omni\store;

class BatchSaveOrUpdateResq {
	
	static $_TSPEC;
	public $success = null;
	public $effectiveCount = null;
	public $failures = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'success'
			),
			2 => array(
			'var' => 'effectiveCount'
			),
			3 => array(
			'var' => 'failures'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
			if (isset($vals['effectiveCount'])){
				
				$this->effectiveCount = $vals['effectiveCount'];
			}
			
			
			if (isset($vals['failures'])){
				
				$this->failures = $vals['failures'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'BatchSaveOrUpdateResq';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("success" == $schemeField){
				
				$needSkip = false;
				$input->readBool($this->success);
				
			}
			
			
			
			
			if ("effectiveCount" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->effectiveCount); 
				
			}
			
			
			
			
			if ("failures" == $schemeField){
				
				$needSkip = false;
				
				$this->failures = array();
				$_size0 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem0 = null;
						
						$elem0 = new \com\vip\vop\omni\store\StoreFailItem();
						$elem0->read($input);
						
						$this->failures[$_size0++] = $elem0;
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
		
		$xfer += $output->writeFieldBegin('success');
		$xfer += $output->writeBool($this->success);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('effectiveCount');
		$xfer += $output->writeI32($this->effectiveCount);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->failures !== null) {
			
			$xfer += $output->writeFieldBegin('failures');
			
			if (!is_array($this->failures)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->failures as $iter0){
				
				
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

?>