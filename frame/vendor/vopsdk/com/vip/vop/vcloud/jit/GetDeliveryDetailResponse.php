<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\vop\vcloud\jit;

class GetDeliveryDetailResponse {
	
	static $_TSPEC;
	public $delivery = null;
	public $deliveryDetail = null;
	public $deliveryTrace = null;
	public $pagination = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'delivery'
			),
			2 => array(
			'var' => 'deliveryDetail'
			),
			3 => array(
			'var' => 'deliveryTrace'
			),
			4 => array(
			'var' => 'pagination'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['delivery'])){
				
				$this->delivery = $vals['delivery'];
			}
			
			
			if (isset($vals['deliveryDetail'])){
				
				$this->deliveryDetail = $vals['deliveryDetail'];
			}
			
			
			if (isset($vals['deliveryTrace'])){
				
				$this->deliveryTrace = $vals['deliveryTrace'];
			}
			
			
			if (isset($vals['pagination'])){
				
				$this->pagination = $vals['pagination'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GetDeliveryDetailResponse';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("delivery" == $schemeField){
				
				$needSkip = false;
				
				$this->delivery = new \com\vip\vop\vcloud\jit\Delivery();
				$this->delivery->read($input);
				
			}
			
			
			
			
			if ("deliveryDetail" == $schemeField){
				
				$needSkip = false;
				
				$this->deliveryDetail = array();
				$_size1 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem1 = null;
						
						$elem1 = new \com\vip\vop\vcloud\jit\DeliveryDetail();
						$elem1->read($input);
						
						$this->deliveryDetail[$_size1++] = $elem1;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
			}
			
			
			
			
			if ("deliveryTrace" == $schemeField){
				
				$needSkip = false;
				
				$this->deliveryTrace = array();
				$_size2 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem2 = null;
						
						$elem2 = new \com\vip\vop\vcloud\jit\DeliveryTrace();
						$elem2->read($input);
						
						$this->deliveryTrace[$_size2++] = $elem2;
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
		
		if($this->delivery !== null) {
			
			$xfer += $output->writeFieldBegin('delivery');
			
			if (!is_object($this->delivery)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->delivery->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->deliveryDetail !== null) {
			
			$xfer += $output->writeFieldBegin('deliveryDetail');
			
			if (!is_array($this->deliveryDetail)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->deliveryDetail as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->deliveryTrace !== null) {
			
			$xfer += $output->writeFieldBegin('deliveryTrace');
			
			if (!is_array($this->deliveryTrace)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->deliveryTrace as $iter0){
				
				
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