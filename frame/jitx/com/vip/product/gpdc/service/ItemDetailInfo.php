<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class ItemDetailInfo {
	
	static $_TSPEC;
	public $itemId = null;
	public $videoUrl = null;
	public $accessoryInfo = null;
	public $saleService = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'itemId'
			),
			2 => array(
			'var' => 'videoUrl'
			),
			3 => array(
			'var' => 'accessoryInfo'
			),
			4 => array(
			'var' => 'saleService'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['itemId'])){
				
				$this->itemId = $vals['itemId'];
			}
			
			
			if (isset($vals['videoUrl'])){
				
				$this->videoUrl = $vals['videoUrl'];
			}
			
			
			if (isset($vals['accessoryInfo'])){
				
				$this->accessoryInfo = $vals['accessoryInfo'];
			}
			
			
			if (isset($vals['saleService'])){
				
				$this->saleService = $vals['saleService'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'ItemDetailInfo';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("itemId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->itemId); 
				
			}
			
			
			
			
			if ("videoUrl" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->videoUrl);
				
			}
			
			
			
			
			if ("accessoryInfo" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->accessoryInfo);
				
			}
			
			
			
			
			if ("saleService" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->saleService);
				
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
		
		if($this->itemId !== null) {
			
			$xfer += $output->writeFieldBegin('itemId');
			$xfer += $output->writeI64($this->itemId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->videoUrl !== null) {
			
			$xfer += $output->writeFieldBegin('videoUrl');
			$xfer += $output->writeString($this->videoUrl);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->accessoryInfo !== null) {
			
			$xfer += $output->writeFieldBegin('accessoryInfo');
			$xfer += $output->writeString($this->accessoryInfo);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->saleService !== null) {
			
			$xfer += $output->writeFieldBegin('saleService');
			$xfer += $output->writeString($this->saleService);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>