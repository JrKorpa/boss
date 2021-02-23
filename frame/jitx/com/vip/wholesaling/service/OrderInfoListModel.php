<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\wholesaling\service;

class OrderInfoListModel {
	
	static $_TSPEC;
	public $currentPage = null;
	public $pageSize = null;
	public $totalCount = null;
	public $list = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'currentPage'
			),
			2 => array(
			'var' => 'pageSize'
			),
			3 => array(
			'var' => 'totalCount'
			),
			4 => array(
			'var' => 'list'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['currentPage'])){
				
				$this->currentPage = $vals['currentPage'];
			}
			
			
			if (isset($vals['pageSize'])){
				
				$this->pageSize = $vals['pageSize'];
			}
			
			
			if (isset($vals['totalCount'])){
				
				$this->totalCount = $vals['totalCount'];
			}
			
			
			if (isset($vals['list'])){
				
				$this->list = $vals['list'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'OrderInfoListModel';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("currentPage" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->currentPage); 
				
			}
			
			
			
			
			if ("pageSize" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->pageSize); 
				
			}
			
			
			
			
			if ("totalCount" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->totalCount); 
				
			}
			
			
			
			
			if ("list" == $schemeField){
				
				$needSkip = false;
				
				$this->list = array();
				$_size0 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem0 = null;
						
						$elem0 = new \com\vip\wholesaling\service\OrderInfoModel();
						$elem0->read($input);
						
						$this->list[$_size0++] = $elem0;
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
		
		if($this->currentPage !== null) {
			
			$xfer += $output->writeFieldBegin('currentPage');
			$xfer += $output->writeI32($this->currentPage);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->pageSize !== null) {
			
			$xfer += $output->writeFieldBegin('pageSize');
			$xfer += $output->writeI32($this->pageSize);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->totalCount !== null) {
			
			$xfer += $output->writeFieldBegin('totalCount');
			$xfer += $output->writeI32($this->totalCount);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->list !== null) {
			
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
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>