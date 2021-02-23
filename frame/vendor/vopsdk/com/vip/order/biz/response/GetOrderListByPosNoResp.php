<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\order\biz\response;

class GetOrderListByPosNoResp {
	
	static $_TSPEC;
	public $result = null;
	public $orderPosMap = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'result'
			),
			2 => array(
			'var' => 'orderPosMap'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['result'])){
				
				$this->result = $vals['result'];
			}
			
			
			if (isset($vals['orderPosMap'])){
				
				$this->orderPosMap = $vals['orderPosMap'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GetOrderListByPosNoResp';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("result" == $schemeField){
				
				$needSkip = false;
				
				$this->result = new \com\vip\order\common\pojo\order\result\Result();
				$this->result->read($input);
				
			}
			
			
			
			
			if ("orderPosMap" == $schemeField){
				
				$needSkip = false;
				
				$this->orderPosMap = array();
				$input->readMapBegin();
				while(true){
					
					try{
						
						$key0 = '';
						$input->readString($key0);
						
						$val0 = null;
						
						$val0 = array();
						$_size1 = 0;
						$input->readListBegin();
						while(true){
							
							try{
								
								$elem1 = null;
								
								$elem1 = new \com\vip\order\common\pojo\order\vo\OrderByPosVO();
								$elem1->read($input);
								
								$val0[$_size1++] = $elem1;
							}
							catch(\Exception $e){
								
								break;
							}
						}
						
						$input->readListEnd();
						
						$this->orderPosMap[$key0] = $val0;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readMapEnd();
				
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
		
		if($this->result !== null) {
			
			$xfer += $output->writeFieldBegin('result');
			
			if (!is_object($this->result)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->result->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->orderPosMap !== null) {
			
			$xfer += $output->writeFieldBegin('orderPosMap');
			
			if (!is_array($this->orderPosMap)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeMapBegin();
			foreach ($this->orderPosMap as $kiter0 => $viter0){
				
				$xfer += $output->writeString($kiter0);
				
				
				if (!is_array($viter0)){
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$output->writeListBegin();
				foreach ($viter0 as $iter1){
					
					
					if (!is_object($iter1)) {
						
						throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
					}
					
					$xfer += $iter1->write($output);
					
				}
				
				$output->writeListEnd();
				
			}
			
			$output->writeMapEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>