<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class GlobalProduct {
	
	static $_TSPEC;
	public $globalProductId = null;
	public $owner = null;
	public $vendorId = null;
	public $sn = null;
	public $version = null;
	public $title = null;
	public $subTitle = null;
	public $brandId = null;
	public $categoryId = null;
	public $marketPrice = null;
	public $currency = null;
	public $unit = null;
	public $productType = null;
	public $status = null;
	public $attrSpecProps = null;
	public $flags = null;
	public $weight = null;
	public $length = null;
	public $width = null;
	public $height = null;
	public $itemDetailInfo = null;
	public $itemDetailModuleList = null;
	public $itemPropertiesList = null;
	public $itemImageList = null;
	public $originalGoodsNum = null;
	public $taxRate = null;
	public $areaOutput = null;
	public $flatSpecProps = null;
	public $sizeTableId = null;
	public $packLanguage = null;
	public $brandCnName = null;
	public $brandEnName = null;
	public $brandShowName = null;
	public $grossWeight = null;
	public $simpleSpecProps = null;
	public $updateOa = null;
	public $squareImageList = null;
	public $updateTime = null;
	public $unitName = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'globalProductId'
			),
			2 => array(
			'var' => 'owner'
			),
			3 => array(
			'var' => 'vendorId'
			),
			4 => array(
			'var' => 'sn'
			),
			5 => array(
			'var' => 'version'
			),
			6 => array(
			'var' => 'title'
			),
			7 => array(
			'var' => 'subTitle'
			),
			8 => array(
			'var' => 'brandId'
			),
			9 => array(
			'var' => 'categoryId'
			),
			10 => array(
			'var' => 'marketPrice'
			),
			11 => array(
			'var' => 'currency'
			),
			12 => array(
			'var' => 'unit'
			),
			13 => array(
			'var' => 'productType'
			),
			14 => array(
			'var' => 'status'
			),
			15 => array(
			'var' => 'attrSpecProps'
			),
			16 => array(
			'var' => 'flags'
			),
			17 => array(
			'var' => 'weight'
			),
			18 => array(
			'var' => 'length'
			),
			19 => array(
			'var' => 'width'
			),
			20 => array(
			'var' => 'height'
			),
			21 => array(
			'var' => 'itemDetailInfo'
			),
			22 => array(
			'var' => 'itemDetailModuleList'
			),
			23 => array(
			'var' => 'itemPropertiesList'
			),
			24 => array(
			'var' => 'itemImageList'
			),
			25 => array(
			'var' => 'originalGoodsNum'
			),
			26 => array(
			'var' => 'taxRate'
			),
			27 => array(
			'var' => 'areaOutput'
			),
			28 => array(
			'var' => 'flatSpecProps'
			),
			29 => array(
			'var' => 'sizeTableId'
			),
			30 => array(
			'var' => 'packLanguage'
			),
			31 => array(
			'var' => 'brandCnName'
			),
			32 => array(
			'var' => 'brandEnName'
			),
			33 => array(
			'var' => 'brandShowName'
			),
			34 => array(
			'var' => 'grossWeight'
			),
			35 => array(
			'var' => 'simpleSpecProps'
			),
			36 => array(
			'var' => 'updateOa'
			),
			37 => array(
			'var' => 'squareImageList'
			),
			38 => array(
			'var' => 'updateTime'
			),
			39 => array(
			'var' => 'unitName'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['globalProductId'])){
				
				$this->globalProductId = $vals['globalProductId'];
			}
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['vendorId'])){
				
				$this->vendorId = $vals['vendorId'];
			}
			
			
			if (isset($vals['sn'])){
				
				$this->sn = $vals['sn'];
			}
			
			
			if (isset($vals['version'])){
				
				$this->version = $vals['version'];
			}
			
			
			if (isset($vals['title'])){
				
				$this->title = $vals['title'];
			}
			
			
			if (isset($vals['subTitle'])){
				
				$this->subTitle = $vals['subTitle'];
			}
			
			
			if (isset($vals['brandId'])){
				
				$this->brandId = $vals['brandId'];
			}
			
			
			if (isset($vals['categoryId'])){
				
				$this->categoryId = $vals['categoryId'];
			}
			
			
			if (isset($vals['marketPrice'])){
				
				$this->marketPrice = $vals['marketPrice'];
			}
			
			
			if (isset($vals['currency'])){
				
				$this->currency = $vals['currency'];
			}
			
			
			if (isset($vals['unit'])){
				
				$this->unit = $vals['unit'];
			}
			
			
			if (isset($vals['productType'])){
				
				$this->productType = $vals['productType'];
			}
			
			
			if (isset($vals['status'])){
				
				$this->status = $vals['status'];
			}
			
			
			if (isset($vals['attrSpecProps'])){
				
				$this->attrSpecProps = $vals['attrSpecProps'];
			}
			
			
			if (isset($vals['flags'])){
				
				$this->flags = $vals['flags'];
			}
			
			
			if (isset($vals['weight'])){
				
				$this->weight = $vals['weight'];
			}
			
			
			if (isset($vals['length'])){
				
				$this->length = $vals['length'];
			}
			
			
			if (isset($vals['width'])){
				
				$this->width = $vals['width'];
			}
			
			
			if (isset($vals['height'])){
				
				$this->height = $vals['height'];
			}
			
			
			if (isset($vals['itemDetailInfo'])){
				
				$this->itemDetailInfo = $vals['itemDetailInfo'];
			}
			
			
			if (isset($vals['itemDetailModuleList'])){
				
				$this->itemDetailModuleList = $vals['itemDetailModuleList'];
			}
			
			
			if (isset($vals['itemPropertiesList'])){
				
				$this->itemPropertiesList = $vals['itemPropertiesList'];
			}
			
			
			if (isset($vals['itemImageList'])){
				
				$this->itemImageList = $vals['itemImageList'];
			}
			
			
			if (isset($vals['originalGoodsNum'])){
				
				$this->originalGoodsNum = $vals['originalGoodsNum'];
			}
			
			
			if (isset($vals['taxRate'])){
				
				$this->taxRate = $vals['taxRate'];
			}
			
			
			if (isset($vals['areaOutput'])){
				
				$this->areaOutput = $vals['areaOutput'];
			}
			
			
			if (isset($vals['flatSpecProps'])){
				
				$this->flatSpecProps = $vals['flatSpecProps'];
			}
			
			
			if (isset($vals['sizeTableId'])){
				
				$this->sizeTableId = $vals['sizeTableId'];
			}
			
			
			if (isset($vals['packLanguage'])){
				
				$this->packLanguage = $vals['packLanguage'];
			}
			
			
			if (isset($vals['brandCnName'])){
				
				$this->brandCnName = $vals['brandCnName'];
			}
			
			
			if (isset($vals['brandEnName'])){
				
				$this->brandEnName = $vals['brandEnName'];
			}
			
			
			if (isset($vals['brandShowName'])){
				
				$this->brandShowName = $vals['brandShowName'];
			}
			
			
			if (isset($vals['grossWeight'])){
				
				$this->grossWeight = $vals['grossWeight'];
			}
			
			
			if (isset($vals['simpleSpecProps'])){
				
				$this->simpleSpecProps = $vals['simpleSpecProps'];
			}
			
			
			if (isset($vals['updateOa'])){
				
				$this->updateOa = $vals['updateOa'];
			}
			
			
			if (isset($vals['squareImageList'])){
				
				$this->squareImageList = $vals['squareImageList'];
			}
			
			
			if (isset($vals['updateTime'])){
				
				$this->updateTime = $vals['updateTime'];
			}
			
			
			if (isset($vals['unitName'])){
				
				$this->unitName = $vals['unitName'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'GlobalProduct';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
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
			
			
			
			
			if ("sn" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->sn);
				
			}
			
			
			
			
			if ("version" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->version); 
				
			}
			
			
			
			
			if ("title" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->title);
				
			}
			
			
			
			
			if ("subTitle" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->subTitle);
				
			}
			
			
			
			
			if ("brandId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->brandId); 
				
			}
			
			
			
			
			if ("categoryId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->categoryId); 
				
			}
			
			
			
			
			if ("marketPrice" == $schemeField){
				
				$needSkip = false;
				$input->readDouble($this->marketPrice);
				
			}
			
			
			
			
			if ("currency" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->currency);
				
			}
			
			
			
			
			if ("unit" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->unit); 
				
			}
			
			
			
			
			if ("productType" == $schemeField){
				
				$needSkip = false;
				
				$names = \com\vip\product\gpdc\service\ProductTypes::$__names;
				$name = null;
				$input->readString($name);
				foreach ($names as $k => $v){
					
					if($name == $v){
						
						$this->productType = $k;
						break;
					}
					
				}
				
				
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
			
			
			
			
			if ("attrSpecProps" == $schemeField){
				
				$needSkip = false;
				
				$this->attrSpecProps = array();
				$_size0 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem0 = null;
						
						$elem0 = new \com\vip\product\gpdc\service\Attributes();
						$elem0->read($input);
						
						$this->attrSpecProps[$_size0++] = $elem0;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readListEnd();
				
			}
			
			
			
			
			if ("flags" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->flags); 
				
			}
			
			
			
			
			if ("weight" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->weight); 
				
			}
			
			
			
			
			if ("length" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->length); 
				
			}
			
			
			
			
			if ("width" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->width); 
				
			}
			
			
			
			
			if ("height" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->height); 
				
			}
			
			
			
			
			if ("itemDetailInfo" == $schemeField){
				
				$needSkip = false;
				
				$this->itemDetailInfo = new \com\vip\product\gpdc\service\ItemDetailInfo();
				$this->itemDetailInfo->read($input);
				
			}
			
			
			
			
			if ("itemDetailModuleList" == $schemeField){
				
				$needSkip = false;
				
				$this->itemDetailModuleList = array();
				$_size1 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem1 = null;
						
						$elem1 = new \com\vip\product\gpdc\service\ItemDetailModuleInfo();
						$elem1->read($input);
						
						$this->itemDetailModuleList[$_size1++] = $elem1;
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
				$_size2 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem2 = null;
						
						$elem2 = new \com\vip\product\gpdc\service\ItemPropertiesInfo();
						$elem2->read($input);
						
						$this->itemPropertiesList[$_size2++] = $elem2;
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
			
			
			
			
			if ("originalGoodsNum" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->originalGoodsNum);
				
			}
			
			
			
			
			if ("taxRate" == $schemeField){
				
				$needSkip = false;
				$input->readDouble($this->taxRate);
				
			}
			
			
			
			
			if ("areaOutput" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->areaOutput);
				
			}
			
			
			
			
			if ("flatSpecProps" == $schemeField){
				
				$needSkip = false;
				
				$this->flatSpecProps = array();
				$input->readMapBegin();
				while(true){
					
					try{
						
						$key4 = '';
						$input->readString($key4);
						
						$val4 = '';
						$input->readString($val4);
						
						$this->flatSpecProps[$key4] = $val4;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readMapEnd();
				
			}
			
			
			
			
			if ("sizeTableId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->sizeTableId); 
				
			}
			
			
			
			
			if ("packLanguage" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->packLanguage);
				
			}
			
			
			
			
			if ("brandCnName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->brandCnName);
				
			}
			
			
			
			
			if ("brandEnName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->brandEnName);
				
			}
			
			
			
			
			if ("brandShowName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->brandShowName);
				
			}
			
			
			
			
			if ("grossWeight" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->grossWeight); 
				
			}
			
			
			
			
			if ("simpleSpecProps" == $schemeField){
				
				$needSkip = false;
				
				$this->simpleSpecProps = array();
				$_size5 = 0;
				$input->readListBegin();
				while(true){
					
					try{
						
						$elem5 = null;
						
						$elem5 = new \com\vip\product\gpdc\service\CategoryProps();
						$elem5->read($input);
						
						$this->simpleSpecProps[$_size5++] = $elem5;
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
			
			
			
			
			if ("updateTime" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->updateTime); 
				
			}
			
			
			
			
			if ("unitName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->unitName);
				
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
		
		
		if($this->sn !== null) {
			
			$xfer += $output->writeFieldBegin('sn');
			$xfer += $output->writeString($this->sn);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->version !== null) {
			
			$xfer += $output->writeFieldBegin('version');
			$xfer += $output->writeI32($this->version);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->title !== null) {
			
			$xfer += $output->writeFieldBegin('title');
			$xfer += $output->writeString($this->title);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->subTitle !== null) {
			
			$xfer += $output->writeFieldBegin('subTitle');
			$xfer += $output->writeString($this->subTitle);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandId !== null) {
			
			$xfer += $output->writeFieldBegin('brandId');
			$xfer += $output->writeI32($this->brandId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->categoryId !== null) {
			
			$xfer += $output->writeFieldBegin('categoryId');
			$xfer += $output->writeI32($this->categoryId);
			
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
		
		
		if($this->unit !== null) {
			
			$xfer += $output->writeFieldBegin('unit');
			$xfer += $output->writeI32($this->unit);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->productType !== null) {
			
			$xfer += $output->writeFieldBegin('productType');
			
			$em = new \com\vip\product\gpdc\service\ProductTypes; 
			$output->writeString($em::$__names[$this->productType]);  
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->status !== null) {
			
			$xfer += $output->writeFieldBegin('status');
			
			$em = new \com\vip\product\gpdc\service\ProductStatus; 
			$output->writeString($em::$__names[$this->status]);  
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->attrSpecProps !== null) {
			
			$xfer += $output->writeFieldBegin('attrSpecProps');
			
			if (!is_array($this->attrSpecProps)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->attrSpecProps as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->flags !== null) {
			
			$xfer += $output->writeFieldBegin('flags');
			$xfer += $output->writeI64($this->flags);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->weight !== null) {
			
			$xfer += $output->writeFieldBegin('weight');
			$xfer += $output->writeI32($this->weight);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->length !== null) {
			
			$xfer += $output->writeFieldBegin('length');
			$xfer += $output->writeI32($this->length);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->width !== null) {
			
			$xfer += $output->writeFieldBegin('width');
			$xfer += $output->writeI32($this->width);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->height !== null) {
			
			$xfer += $output->writeFieldBegin('height');
			$xfer += $output->writeI32($this->height);
			
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
		
		
		if($this->originalGoodsNum !== null) {
			
			$xfer += $output->writeFieldBegin('originalGoodsNum');
			$xfer += $output->writeString($this->originalGoodsNum);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->taxRate !== null) {
			
			$xfer += $output->writeFieldBegin('taxRate');
			$xfer += $output->writeDouble($this->taxRate);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->areaOutput !== null) {
			
			$xfer += $output->writeFieldBegin('areaOutput');
			$xfer += $output->writeString($this->areaOutput);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->flatSpecProps !== null) {
			
			$xfer += $output->writeFieldBegin('flatSpecProps');
			
			if (!is_array($this->flatSpecProps)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeMapBegin();
			foreach ($this->flatSpecProps as $kiter0 => $viter0){
				
				$xfer += $output->writeString($kiter0);
				
				$xfer += $output->writeString($viter0);
				
			}
			
			$output->writeMapEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->sizeTableId !== null) {
			
			$xfer += $output->writeFieldBegin('sizeTableId');
			$xfer += $output->writeI64($this->sizeTableId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->packLanguage !== null) {
			
			$xfer += $output->writeFieldBegin('packLanguage');
			$xfer += $output->writeString($this->packLanguage);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandCnName !== null) {
			
			$xfer += $output->writeFieldBegin('brandCnName');
			$xfer += $output->writeString($this->brandCnName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandEnName !== null) {
			
			$xfer += $output->writeFieldBegin('brandEnName');
			$xfer += $output->writeString($this->brandEnName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandShowName !== null) {
			
			$xfer += $output->writeFieldBegin('brandShowName');
			$xfer += $output->writeString($this->brandShowName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->grossWeight !== null) {
			
			$xfer += $output->writeFieldBegin('grossWeight');
			$xfer += $output->writeI32($this->grossWeight);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->simpleSpecProps !== null) {
			
			$xfer += $output->writeFieldBegin('simpleSpecProps');
			
			if (!is_array($this->simpleSpecProps)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->simpleSpecProps as $iter0){
				
				
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
		
		
		if($this->updateTime !== null) {
			
			$xfer += $output->writeFieldBegin('updateTime');
			$xfer += $output->writeI64($this->updateTime);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->unitName !== null) {
			
			$xfer += $output->writeFieldBegin('unitName');
			$xfer += $output->writeString($this->unitName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>