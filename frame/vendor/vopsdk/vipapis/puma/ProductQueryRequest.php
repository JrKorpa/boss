<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace vipapis\puma;

class ProductQueryRequest {
	
	static $_TSPEC;
	public $pagination = null;
	public $product_ids = null;
	public $brand_sns = null;
	public $third_level_category_ids = null;
	public $query_types = null;
	public $is_on_sale = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'pagination'
			),
			2 => array(
			'var' => 'product_ids'
			),
			3 => array(
			'var' => 'brand_sns'
			),
			4 => array(
			'var' => 'third_level_category_ids'
			),
			5 => array(
			'var' => 'query_types'
			),
			6 => array(
			'var' => 'is_on_sale'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['pagination'])){
				
				$this->pagination = $vals['pagination'];
			}
			
			
			if (isset($vals['product_ids'])){
				
				$this->product_ids = $vals['product_ids'];
			}
			
			
			if (isset($vals['brand_sns'])){
				
				$this->brand_sns = $vals['brand_sns'];
			}
			
			
			if (isset($vals['third_level_category_ids'])){
				
				$this->third_level_category_ids = $vals['third_level_category_ids'];
			}
			
			
			if (isset($vals['query_types'])){
				
				$this->query_types = $vals['query_types'];
			}
			
			
			if (isset($vals['is_on_sale'])){
				
				$this->is_on_sale = $vals['is_on_sale'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'ProductQueryRequest';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("pagination" == $schemeField){
				
				$needSkip = false;
				
				$this->pagination = new \vipapis\puma\Pagination();
				$this->pagination->read($input);
				
			}
			
			
			
			
			if ("product_ids" == $schemeField){
				
				$needSkip = false;
				
				$this->product_ids = array();
				$_size0 = 0;
				$input->readSetBegin();
				while(true){
					
					try{
						
						$elem0 = null;
						$input->readI64($elem0); 
						
						$this->product_ids[$_size0++] = $elem0;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readSetEnd();
				
			}
			
			
			
			
			if ("brand_sns" == $schemeField){
				
				$needSkip = false;
				
				$this->brand_sns = array();
				$_size1 = 0;
				$input->readSetBegin();
				while(true){
					
					try{
						
						$elem1 = null;
						$input->readString($elem1);
						
						$this->brand_sns[$_size1++] = $elem1;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readSetEnd();
				
			}
			
			
			
			
			if ("third_level_category_ids" == $schemeField){
				
				$needSkip = false;
				
				$this->third_level_category_ids = array();
				$_size2 = 0;
				$input->readSetBegin();
				while(true){
					
					try{
						
						$elem2 = null;
						$input->readI32($elem2); 
						
						$this->third_level_category_ids[$_size2++] = $elem2;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readSetEnd();
				
			}
			
			
			
			
			if ("query_types" == $schemeField){
				
				$needSkip = false;
				
				$this->query_types = array();
				$_size3 = 0;
				$input->readSetBegin();
				while(true){
					
					try{
						
						$elem3 = null;
						$input->readI32($elem3); 
						
						$this->query_types[$_size3++] = $elem3;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readSetEnd();
				
			}
			
			
			
			
			if ("is_on_sale" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->is_on_sale); 
				
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
		
		$xfer += $output->writeFieldBegin('pagination');
		
		if (!is_object($this->pagination)) {
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$xfer += $this->pagination->write($output);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->product_ids !== null) {
			
			$xfer += $output->writeFieldBegin('product_ids');
			
			if (!is_array($this->product_ids)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeSetBegin();
			foreach ($this->product_ids as $iter0){
				
				$xfer += $output->writeI64($iter0);
				
			}
			
			$output->writeSetEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brand_sns !== null) {
			
			$xfer += $output->writeFieldBegin('brand_sns');
			
			if (!is_array($this->brand_sns)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeSetBegin();
			foreach ($this->brand_sns as $iter0){
				
				$xfer += $output->writeString($iter0);
				
			}
			
			$output->writeSetEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->third_level_category_ids !== null) {
			
			$xfer += $output->writeFieldBegin('third_level_category_ids');
			
			if (!is_array($this->third_level_category_ids)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeSetBegin();
			foreach ($this->third_level_category_ids as $iter0){
				
				$xfer += $output->writeI32($iter0);
				
			}
			
			$output->writeSetEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('query_types');
		
		if (!is_array($this->query_types)){
			
			throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
		}
		
		$output->writeSetBegin();
		foreach ($this->query_types as $iter0){
			
			$xfer += $output->writeI32($iter0);
			
		}
		
		$output->writeSetEnd();
		
		$xfer += $output->writeFieldEnd();
		
		if($this->is_on_sale !== null) {
			
			$xfer += $output->writeFieldBegin('is_on_sale');
			$xfer += $output->writeI32($this->is_on_sale);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>