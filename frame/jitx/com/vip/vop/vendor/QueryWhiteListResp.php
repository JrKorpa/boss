<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vendor;

class QueryWhiteListResp {
	
	static $_TSPEC;
	public $updateIfPreallcationCancelled = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'updateIfPreallcationCancelled'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['updateIfPreallcationCancelled'])){
				
				$this->updateIfPreallcationCancelled = $vals['updateIfPreallcationCancelled'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'QueryWhiteListResp';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("updateIfPreallcationCancelled" == $schemeField){
				
				$needSkip = false;
				$input->readBool($this->updateIfPreallcationCancelled);
				
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
		
		$xfer += $output->writeFieldBegin('updateIfPreallcationCancelled');
		$xfer += $output->writeBool($this->updateIfPreallcationCancelled);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>