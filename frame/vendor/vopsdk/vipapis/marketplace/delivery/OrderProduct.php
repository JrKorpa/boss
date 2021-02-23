<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace vipapis\marketplace\delivery;

class OrderProduct {
	
	static $_TSPEC;
	public $sku_id = null;
	public $spu_id = null;
	public $outer_spu_id = null;
	public $outer_sku_id = null;
	public $num = null;
	public $title = null;
	public $size = null;
	public $price = null;
	public $customization = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'sku_id'
			),
			2 => array(
			'var' => 'spu_id'
			),
			3 => array(
			'var' => 'outer_spu_id'
			),
			4 => array(
			'var' => 'outer_sku_id'
			),
			5 => array(
			'var' => 'num'
			),
			6 => array(
			'var' => 'title'
			),
			7 => array(
			'var' => 'size'
			),
			8 => array(
			'var' => 'price'
			),
			9 => array(
			'var' => 'customization'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['sku_id'])){
				
				$this->sku_id = $vals['sku_id'];
			}
			
			
			if (isset($vals['spu_id'])){
				
				$this->spu_id = $vals['spu_id'];
			}
			
			
			if (isset($vals['outer_spu_id'])){
				
				$this->outer_spu_id = $vals['outer_spu_id'];
			}
			
			
			if (isset($vals['outer_sku_id'])){
				
				$this->outer_sku_id = $vals['outer_sku_id'];
			}
			
			
			if (isset($vals['num'])){
				
				$this->num = $vals['num'];
			}
			
			
			if (isset($vals['title'])){
				
				$this->title = $vals['title'];
			}
			
			
			if (isset($vals['size'])){
				
				$this->size = $vals['size'];
			}
			
			
			if (isset($vals['price'])){
				
				$this->price = $vals['price'];
			}
			
			
			if (isset($vals['customization'])){
				
				$this->customization = $vals['customization'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'OrderProduct';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("sku_id" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->sku_id);
				
			}
			
			
			
			
			if ("spu_id" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->spu_id);
				
			}
			
			
			
			
			if ("outer_spu_id" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->outer_spu_id);
				
			}
			
			
			
			
			if ("outer_sku_id" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->outer_sku_id);
				
			}
			
			
			
			
			if ("num" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->num);
				
			}
			
			
			
			
			if ("title" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->title);
				
			}
			
			
			
			
			if ("size" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->size);
				
			}
			
			
			
			
			if ("price" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->price);
				
			}
			
			
			
			
			if ("customization" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->customization);
				
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
		
		if($this->sku_id !== null) {
			
			$xfer += $output->writeFieldBegin('sku_id');
			$xfer += $output->writeString($this->sku_id);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->spu_id !== null) {
			
			$xfer += $output->writeFieldBegin('spu_id');
			$xfer += $output->writeString($this->spu_id);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->outer_spu_id !== null) {
			
			$xfer += $output->writeFieldBegin('outer_spu_id');
			$xfer += $output->writeString($this->outer_spu_id);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->outer_sku_id !== null) {
			
			$xfer += $output->writeFieldBegin('outer_sku_id');
			$xfer += $output->writeString($this->outer_sku_id);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->num !== null) {
			
			$xfer += $output->writeFieldBegin('num');
			$xfer += $output->writeString($this->num);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->title !== null) {
			
			$xfer += $output->writeFieldBegin('title');
			$xfer += $output->writeString($this->title);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->size !== null) {
			
			$xfer += $output->writeFieldBegin('size');
			$xfer += $output->writeString($this->size);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->price !== null) {
			
			$xfer += $output->writeFieldBegin('price');
			$xfer += $output->writeString($this->price);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->customization !== null) {
			
			$xfer += $output->writeFieldBegin('customization');
			$xfer += $output->writeString($this->customization);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>