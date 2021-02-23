<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\jit;

class GetDeliveryGoodsResponse {
	
	static $_TSPEC;
	public $deliveryGoodsList = null;
	public $pagination = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'deliveryGoodsList'
			),
			2 => array(
			'var' => 'pagination'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['deliveryGoodsList'])){
				
				$this->deliveryGoodsList = $vals['deliveryGoodsList'];
			}
			
			
			if (isset($vals['pagination'])){
				
				$this->pagination = $vals['pagination'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GetDeliveryGoodsResponse';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("deliveryGoodsList" == $schemeField){
				
				$needSkip = false;
				
				$this->deliveryGoodsList = array();
				$_size0 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem0 = null;
						
						$elem0 = new \com\vip\vop\vcloud\jit\DeliveryDetail();
						$elem0->read($input);
						
						$this->deliveryGoodsList[$_size0++] = $elem0;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
			}
			
			
			
			
			if ("pagination" == $schemeField){
				
				$needSkip = false;
				
				$this->pagination = new \com\vip\vop\vcloud\common\api\Pagination();
				$this->pagination->read($input);
				
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
		
		if($this->deliveryGoodsList !== null) {
			
			$xfer += $output->writeFieldBegin('deliveryGoodsList');
			
			if (!is_array($this->deliveryGoodsList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->deliveryGoodsList as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->pagination !== null) {
			
			$xfer += $output->writeFieldBegin('pagination');
			
			if (!is_object($this->pagination)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->pagination->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>