<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class GlobalSku {
	
	static $_TSPEC;
	public $globalSkuId = null;
	public $globalProductId = null;
	public $owner = null;
	public $vendorId = null;
	public $version = null;
	public $status = null;
	public $barcode = null;
	public $residualGrade = null;
	public $marketPrice = null;
	public $currency = null;
	public $buyMin = null;
	public $buyMax = null;
	public $attrSaleProps = null;
	public $itemPropertiesList = null;
	public $itemDetailModuleList = null;
	public $itemImageList = null;
	public $itemDetailInfo = null;
	public $flatSaleProps = null;
	public $sizeDetailId = null;
	public $globalSkuGroupId = null;
	public $groupSn = null;
	public $imageGroupId = null;
	public $simpleSaleProps = null;
	public $updateOa = null;
	public $squareImageList = null;
	public $operationModes = null;
	public $oldBarcode = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'globalSkuId'
			),
			2 => array(
			'var' => 'globalProductId'
			),
			3 => array(
			'var' => 'owner'
			),
			4 => array(
			'var' => 'vendorId'
			),
			5 => array(
			'var' => 'version'
			),
			6 => array(
			'var' => 'status'
			),
			7 => array(
			'var' => 'barcode'
			),
			8 => array(
			'var' => 'residualGrade'
			),
			9 => array(
			'var' => 'marketPrice'
			),
			10 => array(
			'var' => 'currency'
			),
			11 => array(
			'var' => 'buyMin'
			),
			12 => array(
			'var' => 'buyMax'
			),
			13 => array(
			'var' => 'attrSaleProps'
			),
			14 => array(
			'var' => 'itemPropertiesList'
			),
			15 => array(
			'var' => 'itemDetailModuleList'
			),
			16 => array(
			'var' => 'itemImageList'
			),
			17 => array(
			'var' => 'itemDetailInfo'
			),
			18 => array(
			'var' => 'flatSaleProps'
			),
			19 => array(
			'var' => 'sizeDetailId'
			),
			20 => array(
			'var' => 'globalSkuGroupId'
			),
			21 => array(
			'var' => 'groupSn'
			),
			22 => array(
			'var' => 'imageGroupId'
			),
			23 => array(
			'var' => 'simpleSaleProps'
			),
			24 => array(
			'var' => 'updateOa'
			),
			25 => array(
			'var' => 'squareImageList'
			),
			26 => array(
			'var' => 'operationModes'
			),
			27 => array(
			'var' => 'oldBarcode'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['globalSkuId'])){
				
				$this->globalSkuId = $vals['globalSkuId'];
			}
			
			
			if (isset($vals['globalProductId'])){
				
				$this->globalProductId = $vals['globalProductId'];
			}
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['version'])){
				
				$this->version = $vals['version'];
			}
			
			
			if (isset($vals['status'])){
				
				$this->status = $vals['status'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
			if (isset($vals['residualGrade'])){
				
				$this->residualGrade = $vals['residualGrade'];
			}
			
			
			if (isset($vals['marketPrice'])){
				
				$this->marketPrice = $vals['marketPrice'];
			}
			
			
			if (isset($vals['currency'])){
				
				$this->currency = $vals['currency'];
			}
			
			
			if (isset($vals['buyMin'])){
				
				$this->buyMin = $vals['buyMin'];
			}
			
			
			if (isset($vals['buyMax'])){
				
				$this->buyMax = $vals['buyMax'];
			}
			
			
			if (isset($vals['attrSaleProps'])){
				
				$this->attrSaleProps = $vals['attrSaleProps'];
			}
			
			
			if (isset($vals['itemPropertiesList'])){
				
				$this->itemPropertiesList = $vals['itemPropertiesList'];
			}
			
			
			if (isset($vals['itemDetailModuleList'])){
				
				$this->itemDetailModuleList = $vals['itemDetailModuleList'];
			}
			
			
			if (isset($vals['itemImageList'])){
				
				$this->itemImageList = $vals['itemImageList'];
			}
			
			
			if (isset($vals['itemDetailInfo'])){
				
				$this->itemDetailInfo = $vals['itemDetailInfo'];
			}
			
			
			if (isset($vals['flatSaleProps'])){
				
				$this->flatSaleProps = $vals['flatSaleProps'];
			}
			
			
			if (isset($vals['sizeDetailId'])){
				
				$this->sizeDetailId = $vals['sizeDetailId'];
			}
			
			
			if (isset($vals['globalSkuGroupId'])){
				
				$this->globalSkuGroupId = $vals['globalSkuGroupId'];
			}
			
			
			if (isset($vals['groupSn'])){
				
				$this->groupSn = $vals['groupSn'];
			}
			
			
			if (isset($vals['imageGroupId'])){
				
				$this->imageGroupId = $vals['imageGroupId'];
			}
			
			
			if (isset($vals['simpleSaleProps'])){
				
				$this->simpleSaleProps = $vals['simpleSaleProps'];
			}
			
			
			if (isset($vals['updateOa'])){
				
				$this->updateOa = $vals['updateOa'];
			}
			
			
			if (isset($vals['squareImageList'])){
				
				$this->squareImageList = $vals['squareImageList'];
			}
			
			
			if (isset($vals['operationModes'])){
				
				$this->operationModes = $vals['operationModes'];
			}
			
			
			if (isset($vals['oldBarcode'])){
				
				$this->oldBarcode = $vals['oldBarcode'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GlobalSku';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("globalSkuId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->globalSkuId); 
				
			}
			
			
			
			
			if ("globalProductId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->globalProductId); 
				
			}
			
			
			
			
			if ("owner" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->owner);
				
			}
			
			
			
			
			if ("vendorId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->vendorId); 
				
			}
			
			
			
			
			if ("version" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->version); 
				
			}
			
			
			
			
			if ("status" == $schemeField){
				
				$needSkip = false;
				
				$names = \com\vip\product\gpdc\service\ProductStatus::$__names;
				$name = null;
				$input->readString($name);
				foreach ($names as $k => $v){
					
					if($name == $v){
						
						$this->status = $k;
						break;
					}
					
				}
				
				
			}
			
			
			
			
			if ("barcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->barcode);
				
			}
			
			
			
			
			if ("residualGrade" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->residualGrade);
				
			}
			
			
			
			
			if ("marketPrice" == $schemeField){
				
				$needSkip = false;
				$input->readDouble($this->marketPrice);
				
			}
			
			
			
			
			if ("currency" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->currency);
				
			}
			
			
			
			
			if ("buyMin" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->buyMin); 
				
			}
			
			
			
			
			if ("buyMax" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->buyMax); 
				
			}
			
			
			
			
			if ("attrSaleProps" == $schemeField){
				
				$needSkip = false;
				
				$this->attrSaleProps = array();
				$_size0 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem0 = null;
						
						$elem0 = new \com\vip\product\gpdc\service\Attributes();
						$elem0->read($input);
						
						$this->attrSaleProps[$_size0++] = $elem0;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
			}
			
			
			
			
			if ("itemPropertiesList" == $schemeField){
				
				$needSkip = false;
				
				$this->itemPropertiesList = array();
				$_size1 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem1 = null;
						
						$elem1 = new \com\vip\product\gpdc\service\ItemPropertiesInfo();
						$elem1->read($input);
						
						$this->itemPropertiesList[$_size1++] = $elem1;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
			}
			
			
			
			
			if ("itemDetailModuleList" == $schemeField){
				
				$needSkip = false;
				
				$this->itemDetailModuleList = array();
				$_size2 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem2 = null;
						
						$elem2 = new \com\vip\product\gpdc\service\ItemDetailModuleInfo();
						$elem2->read($input);
						
						$this->itemDetailModuleList[$_size2++] = $elem2;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
			}
			
			
			
			
			if ("itemImageList" == $schemeField){
				
				$needSkip = false;
				
				$this->itemImageList = array();
				$_size3 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem3 = null;
						
						$elem3 = new \com\vip\product\gpdc\service\ItemImageInfo();
						$elem3->read($input);
						
						$this->itemImageList[$_size3++] = $elem3;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
			}
			
			
			
			
			if ("itemDetailInfo" == $schemeField){
				
				$needSkip = false;
				
				$this->itemDetailInfo = new \com\vip\product\gpdc\service\ItemDetailInfo();
				$this->itemDetailInfo->read($input);
				
			}
			
			
			
			
			if ("flatSaleProps" == $schemeField){
				
				$needSkip = false;
				
				$this->flatSaleProps = array();
				$input->readMapBegin();
				while(true){
					
					try{
						
						$key4 = 0;
						$input->readI32($key4); 
						
						$val4 = '';
						$input->readString($val4);
						
						$this->flatSaleProps[$key4] = $val4;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readMapEnd();
				
			}
			
			
			
			
			if ("sizeDetailId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->sizeDetailId); 
				
			}
			
			
			
			
			if ("globalSkuGroupId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->globalSkuGroupId); 
				
			}
			
			
			
			
			if ("groupSn" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->groupSn);
				
			}
			
			
			
			
			if ("imageGroupId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->imageGroupId); 
				
			}
			
			
			
			
			if ("simpleSaleProps" == $schemeField){
				
				$needSkip = false;
				
				$this->simpleSaleProps = array();
				$_size5 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem5 = null;
						
						$elem5 = new \com\vip\product\gpdc\service\CategoryProps();
						$elem5->read($input);
						
						$this->simpleSaleProps[$_size5++] = $elem5;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
			}
			
			
			
			
			if ("updateOa" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->updateOa);
				
			}
			
			
			
			
			if ("squareImageList" == $schemeField){
				
				$needSkip = false;
				
				$this->squareImageList = array();
				$_size6 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem6 = null;
						
						$elem6 = new \com\vip\product\gpdc\service\ItemImageInfo();
						$elem6->read($input);
						
						$this->squareImageList[$_size6++] = $elem6;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
			}
			
			
			
			
			if ("operationModes" == $schemeField){
				
				$needSkip = false;
				
				$names = \com\vip\product\gpdc\service\OperationModes::$__names;
				$name = null;
				$input->readString($name);
				foreach ($names as $k => $v){
					
					if($name == $v){
						
						$this->operationModes = $k;
						break;
					}
					
				}
				
				
			}
			
			
			
			
			if ("oldBarcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->oldBarcode);
				
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
		
		if($this->globalSkuId !== null) {
			
			$xfer += $output->writeFieldBegin('globalSkuId');
			$xfer += $output->writeI64($this->globalSkuId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->globalProductId !== null) {
			
			$xfer += $output->writeFieldBegin('globalProductId');
			$xfer += $output->writeI64($this->globalProductId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->vendorId !== null) {
			
			$xfer += $output->writeFieldBegin('vendorId');
			$xfer += $output->writeI32($this->vendorId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->version !== null) {
			
			$xfer += $output->writeFieldBegin('version');
			$xfer += $output->writeI32($this->version);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->status !== null) {
			
			$xfer += $output->writeFieldBegin('status');
			
			$em = new \com\vip\product\gpdc\service\ProductStatus; 
			$output->writeString($em::$__names[$this->status]);  
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcode !== null) {
			
			$xfer += $output->writeFieldBegin('barcode');
			$xfer += $output->writeString($this->barcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->residualGrade !== null) {
			
			$xfer += $output->writeFieldBegin('residualGrade');
			$xfer += $output->writeString($this->residualGrade);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->marketPrice !== null) {
			
			$xfer += $output->writeFieldBegin('marketPrice');
			$xfer += $output->writeDouble($this->marketPrice);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->currency !== null) {
			
			$xfer += $output->writeFieldBegin('currency');
			$xfer += $output->writeString($this->currency);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->buyMin !== null) {
			
			$xfer += $output->writeFieldBegin('buyMin');
			$xfer += $output->writeI32($this->buyMin);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->buyMax !== null) {
			
			$xfer += $output->writeFieldBegin('buyMax');
			$xfer += $output->writeI32($this->buyMax);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->attrSaleProps !== null) {
			
			$xfer += $output->writeFieldBegin('attrSaleProps');
			
			if (!is_array($this->attrSaleProps)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->attrSaleProps as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->itemPropertiesList !== null) {
			
			$xfer += $output->writeFieldBegin('itemPropertiesList');
			
			if (!is_array($this->itemPropertiesList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->itemPropertiesList as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->itemDetailModuleList !== null) {
			
			$xfer += $output->writeFieldBegin('itemDetailModuleList');
			
			if (!is_array($this->itemDetailModuleList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->itemDetailModuleList as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->itemImageList !== null) {
			
			$xfer += $output->writeFieldBegin('itemImageList');
			
			if (!is_array($this->itemImageList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->itemImageList as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->itemDetailInfo !== null) {
			
			$xfer += $output->writeFieldBegin('itemDetailInfo');
			
			if (!is_object($this->itemDetailInfo)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->itemDetailInfo->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->flatSaleProps !== null) {
			
			$xfer += $output->writeFieldBegin('flatSaleProps');
			
			if (!is_array($this->flatSaleProps)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeMapBegin();
			foreach ($this->flatSaleProps as $kiter0 => $viter0){
				
				$xfer += $output->writeI32($kiter0);
				
				$xfer += $output->writeString($viter0);
				
			}
			
			$output->writeMapEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->sizeDetailId !== null) {
			
			$xfer += $output->writeFieldBegin('sizeDetailId');
			$xfer += $output->writeI64($this->sizeDetailId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->globalSkuGroupId !== null) {
			
			$xfer += $output->writeFieldBegin('globalSkuGroupId');
			$xfer += $output->writeI64($this->globalSkuGroupId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->groupSn !== null) {
			
			$xfer += $output->writeFieldBegin('groupSn');
			$xfer += $output->writeString($this->groupSn);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->imageGroupId !== null) {
			
			$xfer += $output->writeFieldBegin('imageGroupId');
			$xfer += $output->writeI64($this->imageGroupId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->simpleSaleProps !== null) {
			
			$xfer += $output->writeFieldBegin('simpleSaleProps');
			
			if (!is_array($this->simpleSaleProps)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->simpleSaleProps as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->updateOa !== null) {
			
			$xfer += $output->writeFieldBegin('updateOa');
			$xfer += $output->writeString($this->updateOa);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->squareImageList !== null) {
			
			$xfer += $output->writeFieldBegin('squareImageList');
			
			if (!is_array($this->squareImageList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->squareImageList as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->operationModes !== null) {
			
			$xfer += $output->writeFieldBegin('operationModes');
			
			$em = new \com\vip\product\gpdc\service\OperationModes; 
			$output->writeString($em::$__names[$this->operationModes]);  
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->oldBarcode !== null) {
			
			$xfer += $output->writeFieldBegin('oldBarcode');
			$xfer += $output->writeString($this->oldBarcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>