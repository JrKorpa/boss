<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class BriefGlobalProductAndSku {
	
	static $_TSPEC;
	public $barcode = null;
	public $osn = null;
	public $brandSn = null;
	public $brandName = null;
	public $topCategoryId = null;
	public $topCategoryName = null;
	public $subCategoryId = null;
	public $subCategoryName = null;
	public $categoryId = null;
	public $categoryName = null;
	public $size = null;
	public $color = null;
	public $productName = null;
	public $mainImageUrl = null;
	public $marketPrice = null;
	public $gender = null;
	public $season = null;
	public $disabilityLevel = null;
	public $skuId = null;
	public $globalProductId = null;
	public $oldBarcode = null;
	public $groupSn = null;
	public $hierarchyTopCategoryId = null;
	public $hierarchyTopCateName = null;
	public $hierarchySubCateoryId = null;
	public $hierarchySubCateName = null;
	public $hierarchyLeafCateoryId = null;
	public $hierarchyLeafCateName = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'barcode'
			),
			2 => array(
			'var' => 'osn'
			),
			3 => array(
			'var' => 'brandSn'
			),
			4 => array(
			'var' => 'brandName'
			),
			5 => array(
			'var' => 'topCategoryId'
			),
			6 => array(
			'var' => 'topCategoryName'
			),
			7 => array(
			'var' => 'subCategoryId'
			),
			8 => array(
			'var' => 'subCategoryName'
			),
			9 => array(
			'var' => 'categoryId'
			),
			10 => array(
			'var' => 'categoryName'
			),
			11 => array(
			'var' => 'size'
			),
			12 => array(
			'var' => 'color'
			),
			13 => array(
			'var' => 'productName'
			),
			14 => array(
			'var' => 'mainImageUrl'
			),
			15 => array(
			'var' => 'marketPrice'
			),
			16 => array(
			'var' => 'gender'
			),
			17 => array(
			'var' => 'season'
			),
			18 => array(
			'var' => 'disabilityLevel'
			),
			19 => array(
			'var' => 'skuId'
			),
			20 => array(
			'var' => 'globalProductId'
			),
			21 => array(
			'var' => 'oldBarcode'
			),
			22 => array(
			'var' => 'groupSn'
			),
			23 => array(
			'var' => 'hierarchyTopCategoryId'
			),
			24 => array(
			'var' => 'hierarchyTopCateName'
			),
			25 => array(
			'var' => 'hierarchySubCateoryId'
			),
			26 => array(
			'var' => 'hierarchySubCateName'
			),
			27 => array(
			'var' => 'hierarchyLeafCateoryId'
			),
			28 => array(
			'var' => 'hierarchyLeafCateName'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
			if (isset($vals['osn'])){
				
				$this->osn = $vals['osn'];
			}
			
			
			if (isset($vals['brandSn'])){
				
				$this->brandSn = $vals['brandSn'];
			}
			
			
			if (isset($vals['brandName'])){
				
				$this->brandName = $vals['brandName'];
			}
			
			
			if (isset($vals['topCategoryId'])){
				
				$this->topCategoryId = $vals['topCategoryId'];
			}
			
			
			if (isset($vals['topCategoryName'])){
				
				$this->topCategoryName = $vals['topCategoryName'];
			}
			
			
			if (isset($vals['subCategoryId'])){
				
				$this->subCategoryId = $vals['subCategoryId'];
			}
			
			
			if (isset($vals['subCategoryName'])){
				
				$this->subCategoryName = $vals['subCategoryName'];
			}
			
			
			if (isset($vals['categoryId'])){
				
				$this->categoryId = $vals['categoryId'];
			}
			
			
			if (isset($vals['categoryName'])){
				
				$this->categoryName = $vals['categoryName'];
			}
			
			
			if (isset($vals['size'])){
				
				$this->size = $vals['size'];
			}
			
			
			if (isset($vals['color'])){
				
				$this->color = $vals['color'];
			}
			
			
			if (isset($vals['productName'])){
				
				$this->productName = $vals['productName'];
			}
			
			
			if (isset($vals['mainImageUrl'])){
				
				$this->mainImageUrl = $vals['mainImageUrl'];
			}
			
			
			if (isset($vals['marketPrice'])){
				
				$this->marketPrice = $vals['marketPrice'];
			}
			
			
			if (isset($vals['gender'])){
				
				$this->gender = $vals['gender'];
			}
			
			
			if (isset($vals['season'])){
				
				$this->season = $vals['season'];
			}
			
			
			if (isset($vals['disabilityLevel'])){
				
				$this->disabilityLevel = $vals['disabilityLevel'];
			}
			
			
			if (isset($vals['skuId'])){
				
				$this->skuId = $vals['skuId'];
			}
			
			
			if (isset($vals['globalProductId'])){
				
				$this->globalProductId = $vals['globalProductId'];
			}
			
			
			if (isset($vals['oldBarcode'])){
				
				$this->oldBarcode = $vals['oldBarcode'];
			}
			
			
			if (isset($vals['groupSn'])){
				
				$this->groupSn = $vals['groupSn'];
			}
			
			
			if (isset($vals['hierarchyTopCategoryId'])){
				
				$this->hierarchyTopCategoryId = $vals['hierarchyTopCategoryId'];
			}
			
			
			if (isset($vals['hierarchyTopCateName'])){
				
				$this->hierarchyTopCateName = $vals['hierarchyTopCateName'];
			}
			
			
			if (isset($vals['hierarchySubCateoryId'])){
				
				$this->hierarchySubCateoryId = $vals['hierarchySubCateoryId'];
			}
			
			
			if (isset($vals['hierarchySubCateName'])){
				
				$this->hierarchySubCateName = $vals['hierarchySubCateName'];
			}
			
			
			if (isset($vals['hierarchyLeafCateoryId'])){
				
				$this->hierarchyLeafCateoryId = $vals['hierarchyLeafCateoryId'];
			}
			
			
			if (isset($vals['hierarchyLeafCateName'])){
				
				$this->hierarchyLeafCateName = $vals['hierarchyLeafCateName'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'BriefGlobalProductAndSku';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("barcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->barcode);
				
			}
			
			
			
			
			if ("osn" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->osn);
				
			}
			
			
			
			
			if ("brandSn" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->brandSn); 
				
			}
			
			
			
			
			if ("brandName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->brandName);
				
			}
			
			
			
			
			if ("topCategoryId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->topCategoryId); 
				
			}
			
			
			
			
			if ("topCategoryName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->topCategoryName);
				
			}
			
			
			
			
			if ("subCategoryId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->subCategoryId); 
				
			}
			
			
			
			
			if ("subCategoryName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->subCategoryName);
				
			}
			
			
			
			
			if ("categoryId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->categoryId); 
				
			}
			
			
			
			
			if ("categoryName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->categoryName);
				
			}
			
			
			
			
			if ("size" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->size);
				
			}
			
			
			
			
			if ("color" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->color);
				
			}
			
			
			
			
			if ("productName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->productName);
				
			}
			
			
			
			
			if ("mainImageUrl" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->mainImageUrl);
				
			}
			
			
			
			
			if ("marketPrice" == $schemeField){
				
				$needSkip = false;
				$input->readDouble($this->marketPrice);
				
			}
			
			
			
			
			if ("gender" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->gender);
				
			}
			
			
			
			
			if ("season" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->season);
				
			}
			
			
			
			
			if ("disabilityLevel" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->disabilityLevel);
				
			}
			
			
			
			
			if ("skuId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->skuId); 
				
			}
			
			
			
			
			if ("globalProductId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->globalProductId); 
				
			}
			
			
			
			
			if ("oldBarcode" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->oldBarcode);
				
			}
			
			
			
			
			if ("groupSn" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->groupSn);
				
			}
			
			
			
			
			if ("hierarchyTopCategoryId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->hierarchyTopCategoryId); 
				
			}
			
			
			
			
			if ("hierarchyTopCateName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->hierarchyTopCateName);
				
			}
			
			
			
			
			if ("hierarchySubCateoryId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->hierarchySubCateoryId); 
				
			}
			
			
			
			
			if ("hierarchySubCateName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->hierarchySubCateName);
				
			}
			
			
			
			
			if ("hierarchyLeafCateoryId" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->hierarchyLeafCateoryId); 
				
			}
			
			
			
			
			if ("hierarchyLeafCateName" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->hierarchyLeafCateName);
				
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
		
		if($this->barcode !== null) {
			
			$xfer += $output->writeFieldBegin('barcode');
			$xfer += $output->writeString($this->barcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->osn !== null) {
			
			$xfer += $output->writeFieldBegin('osn');
			$xfer += $output->writeString($this->osn);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandSn !== null) {
			
			$xfer += $output->writeFieldBegin('brandSn');
			$xfer += $output->writeI32($this->brandSn);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandName !== null) {
			
			$xfer += $output->writeFieldBegin('brandName');
			$xfer += $output->writeString($this->brandName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->topCategoryId !== null) {
			
			$xfer += $output->writeFieldBegin('topCategoryId');
			$xfer += $output->writeI32($this->topCategoryId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->topCategoryName !== null) {
			
			$xfer += $output->writeFieldBegin('topCategoryName');
			$xfer += $output->writeString($this->topCategoryName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->subCategoryId !== null) {
			
			$xfer += $output->writeFieldBegin('subCategoryId');
			$xfer += $output->writeI32($this->subCategoryId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->subCategoryName !== null) {
			
			$xfer += $output->writeFieldBegin('subCategoryName');
			$xfer += $output->writeString($this->subCategoryName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->categoryId !== null) {
			
			$xfer += $output->writeFieldBegin('categoryId');
			$xfer += $output->writeI32($this->categoryId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->categoryName !== null) {
			
			$xfer += $output->writeFieldBegin('categoryName');
			$xfer += $output->writeString($this->categoryName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->size !== null) {
			
			$xfer += $output->writeFieldBegin('size');
			$xfer += $output->writeString($this->size);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->color !== null) {
			
			$xfer += $output->writeFieldBegin('color');
			$xfer += $output->writeString($this->color);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->productName !== null) {
			
			$xfer += $output->writeFieldBegin('productName');
			$xfer += $output->writeString($this->productName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->mainImageUrl !== null) {
			
			$xfer += $output->writeFieldBegin('mainImageUrl');
			$xfer += $output->writeString($this->mainImageUrl);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->marketPrice !== null) {
			
			$xfer += $output->writeFieldBegin('marketPrice');
			$xfer += $output->writeDouble($this->marketPrice);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->gender !== null) {
			
			$xfer += $output->writeFieldBegin('gender');
			$xfer += $output->writeString($this->gender);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->season !== null) {
			
			$xfer += $output->writeFieldBegin('season');
			$xfer += $output->writeString($this->season);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->disabilityLevel !== null) {
			
			$xfer += $output->writeFieldBegin('disabilityLevel');
			$xfer += $output->writeString($this->disabilityLevel);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->skuId !== null) {
			
			$xfer += $output->writeFieldBegin('skuId');
			$xfer += $output->writeI64($this->skuId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->globalProductId !== null) {
			
			$xfer += $output->writeFieldBegin('globalProductId');
			$xfer += $output->writeI64($this->globalProductId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->oldBarcode !== null) {
			
			$xfer += $output->writeFieldBegin('oldBarcode');
			$xfer += $output->writeString($this->oldBarcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->groupSn !== null) {
			
			$xfer += $output->writeFieldBegin('groupSn');
			$xfer += $output->writeString($this->groupSn);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->hierarchyTopCategoryId !== null) {
			
			$xfer += $output->writeFieldBegin('hierarchyTopCategoryId');
			$xfer += $output->writeI32($this->hierarchyTopCategoryId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->hierarchyTopCateName !== null) {
			
			$xfer += $output->writeFieldBegin('hierarchyTopCateName');
			$xfer += $output->writeString($this->hierarchyTopCateName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->hierarchySubCateoryId !== null) {
			
			$xfer += $output->writeFieldBegin('hierarchySubCateoryId');
			$xfer += $output->writeI32($this->hierarchySubCateoryId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->hierarchySubCateName !== null) {
			
			$xfer += $output->writeFieldBegin('hierarchySubCateName');
			$xfer += $output->writeString($this->hierarchySubCateName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->hierarchyLeafCateoryId !== null) {
			
			$xfer += $output->writeFieldBegin('hierarchyLeafCateoryId');
			$xfer += $output->writeI32($this->hierarchyLeafCateoryId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->hierarchyLeafCateName !== null) {
			
			$xfer += $output->writeFieldBegin('hierarchyLeafCateName');
			$xfer += $output->writeString($this->hierarchyLeafCateName);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>