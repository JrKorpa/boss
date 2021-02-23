<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;
interface ProductGpdcServiceIf{
	
	
	public function batchGeneralNewBarcode( $owner, $barcodeMappingList, $operator, $appId);
	
	public function batchSaveOrUpdatedGlobalSkuOwnerExpList( $owner, $globalSkuOwnerExpList, $operator, $appId);
	
	public function countGlobalProductIdsByBrandIdAndCategoryId( $owner, $brandId, $categoryIds, $appId);
	
	public function countGlobalProductIdsByCondition( $owner, $snList, $isNewBarcode, $barcodeList, $brandId, $categoryIds, $appId);
	
	public function countGlobalSkuIdsByBrandIdAndCategoryId( $owner, $globalSkuIds, $brandId, $categoryIds, $appId);
	
	public function countGlobalSkuIdsByCondition( $owner, $snList, $isNewBarcode, $barcodeList, $brandId, $appId);
	
	public function getGlobalProduct( $owner, $globalProductId, $optionList, $appId);
	
	public function getGlobalProductIdsByBrandIdAndCategoryId( $owner, $brandId, $categoryIds, $curPage, $pageSize, $appId);
	
	public function getGlobalProductIdsByCondition( $owner, $snList, $isNewBarcode, $barcodeList, $brandId, $categoryIds, $curPage, $pageSize, $appId);
	
	public function getGlobalProductInfoByBarcode( $owner, $barcodeList, $appId);
	
	public function getGlobalProductListByIds( $owner, $globalProductIds, $optionList, $appId);
	
	public function getGlobalSkuForEBSAndDelSkuSyncRecord( $globalSkuForEbsParams, $apiKey, $appId);
	
	public function getGlobalSkuIdsByBrandIdAndCategoryId( $owner, $globalSkuIds, $brandId, $categoryIds, $curPage, $pageSize, $appId);
	
	public function getGlobalSkuIdsByCondition( $owner, $snList, $isNewBarcode, $barcodeList, $brandId, $curPage, $pageSize, $appId);
	
	public function getGlobalSkuIndexList( $owner, $barcodeAndVendorIdList, $appId);
	
	public function getGlobalSkuListByIds( $owner, $globalSkuIds, $optionList, $appId);
	
	public function getGlobalSkuListByProductId( $owner, $globalProductId, $optionList, $appId);
	
	public function getGlobalSkuOwnerExpListByBarcode( $owner, $barcodeList, $appId);
	
	public function healthCheck();
	
	public function sentVmsToXstore( $owner, $globalSkuId, $globalProductId, $appId, $operateType, $barcode);
	
}

class _ProductGpdcServiceClient extends \Osp\Base\OspStub implements \com\vip\product\gpdc\service\ProductGpdcServiceIf{
	
	public function __construct(){
		
		parent::__construct("com.vip.product.gpdc.service.ProductGpdcService", "1.0.0");
	}
	
	
	public function batchGeneralNewBarcode( $owner, $barcodeMappingList, $operator, $appId){
		
		$this->send_batchGeneralNewBarcode( $owner, $barcodeMappingList, $operator, $appId);
		return $this->recv_batchGeneralNewBarcode();
	}
	
	public function send_batchGeneralNewBarcode( $owner, $barcodeMappingList, $operator, $appId){
		
		$this->initInvocation("batchGeneralNewBarcode");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_batchGeneralNewBarcode_args();
		
		$args->owner = $owner;
		
		$args->barcodeMappingList = $barcodeMappingList;
		
		$args->operator = $operator;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_batchGeneralNewBarcode(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_batchGeneralNewBarcode_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function batchSaveOrUpdatedGlobalSkuOwnerExpList( $owner, $globalSkuOwnerExpList, $operator, $appId){
		
		$this->send_batchSaveOrUpdatedGlobalSkuOwnerExpList( $owner, $globalSkuOwnerExpList, $operator, $appId);
		return $this->recv_batchSaveOrUpdatedGlobalSkuOwnerExpList();
	}
	
	public function send_batchSaveOrUpdatedGlobalSkuOwnerExpList( $owner, $globalSkuOwnerExpList, $operator, $appId){
		
		$this->initInvocation("batchSaveOrUpdatedGlobalSkuOwnerExpList");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_batchSaveOrUpdatedGlobalSkuOwnerExpList_args();
		
		$args->owner = $owner;
		
		$args->globalSkuOwnerExpList = $globalSkuOwnerExpList;
		
		$args->operator = $operator;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_batchSaveOrUpdatedGlobalSkuOwnerExpList(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_batchSaveOrUpdatedGlobalSkuOwnerExpList_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function countGlobalProductIdsByBrandIdAndCategoryId( $owner, $brandId, $categoryIds, $appId){
		
		$this->send_countGlobalProductIdsByBrandIdAndCategoryId( $owner, $brandId, $categoryIds, $appId);
		return $this->recv_countGlobalProductIdsByBrandIdAndCategoryId();
	}
	
	public function send_countGlobalProductIdsByBrandIdAndCategoryId( $owner, $brandId, $categoryIds, $appId){
		
		$this->initInvocation("countGlobalProductIdsByBrandIdAndCategoryId");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_countGlobalProductIdsByBrandIdAndCategoryId_args();
		
		$args->owner = $owner;
		
		$args->brandId = $brandId;
		
		$args->categoryIds = $categoryIds;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_countGlobalProductIdsByBrandIdAndCategoryId(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_countGlobalProductIdsByBrandIdAndCategoryId_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function countGlobalProductIdsByCondition( $owner, $snList, $isNewBarcode, $barcodeList, $brandId, $categoryIds, $appId){
		
		$this->send_countGlobalProductIdsByCondition( $owner, $snList, $isNewBarcode, $barcodeList, $brandId, $categoryIds, $appId);
		return $this->recv_countGlobalProductIdsByCondition();
	}
	
	public function send_countGlobalProductIdsByCondition( $owner, $snList, $isNewBarcode, $barcodeList, $brandId, $categoryIds, $appId){
		
		$this->initInvocation("countGlobalProductIdsByCondition");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_countGlobalProductIdsByCondition_args();
		
		$args->owner = $owner;
		
		$args->snList = $snList;
		
		$args->isNewBarcode = $isNewBarcode;
		
		$args->barcodeList = $barcodeList;
		
		$args->brandId = $brandId;
		
		$args->categoryIds = $categoryIds;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_countGlobalProductIdsByCondition(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_countGlobalProductIdsByCondition_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function countGlobalSkuIdsByBrandIdAndCategoryId( $owner, $globalSkuIds, $brandId, $categoryIds, $appId){
		
		$this->send_countGlobalSkuIdsByBrandIdAndCategoryId( $owner, $globalSkuIds, $brandId, $categoryIds, $appId);
		return $this->recv_countGlobalSkuIdsByBrandIdAndCategoryId();
	}
	
	public function send_countGlobalSkuIdsByBrandIdAndCategoryId( $owner, $globalSkuIds, $brandId, $categoryIds, $appId){
		
		$this->initInvocation("countGlobalSkuIdsByBrandIdAndCategoryId");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_countGlobalSkuIdsByBrandIdAndCategoryId_args();
		
		$args->owner = $owner;
		
		$args->globalSkuIds = $globalSkuIds;
		
		$args->brandId = $brandId;
		
		$args->categoryIds = $categoryIds;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_countGlobalSkuIdsByBrandIdAndCategoryId(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_countGlobalSkuIdsByBrandIdAndCategoryId_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function countGlobalSkuIdsByCondition( $owner, $snList, $isNewBarcode, $barcodeList, $brandId, $appId){
		
		$this->send_countGlobalSkuIdsByCondition( $owner, $snList, $isNewBarcode, $barcodeList, $brandId, $appId);
		return $this->recv_countGlobalSkuIdsByCondition();
	}
	
	public function send_countGlobalSkuIdsByCondition( $owner, $snList, $isNewBarcode, $barcodeList, $brandId, $appId){
		
		$this->initInvocation("countGlobalSkuIdsByCondition");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_countGlobalSkuIdsByCondition_args();
		
		$args->owner = $owner;
		
		$args->snList = $snList;
		
		$args->isNewBarcode = $isNewBarcode;
		
		$args->barcodeList = $barcodeList;
		
		$args->brandId = $brandId;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_countGlobalSkuIdsByCondition(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_countGlobalSkuIdsByCondition_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getGlobalProduct( $owner, $globalProductId, $optionList, $appId){
		
		$this->send_getGlobalProduct( $owner, $globalProductId, $optionList, $appId);
		return $this->recv_getGlobalProduct();
	}
	
	public function send_getGlobalProduct( $owner, $globalProductId, $optionList, $appId){
		
		$this->initInvocation("getGlobalProduct");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalProduct_args();
		
		$args->owner = $owner;
		
		$args->globalProductId = $globalProductId;
		
		$args->optionList = $optionList;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_getGlobalProduct(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalProduct_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getGlobalProductIdsByBrandIdAndCategoryId( $owner, $brandId, $categoryIds, $curPage, $pageSize, $appId){
		
		$this->send_getGlobalProductIdsByBrandIdAndCategoryId( $owner, $brandId, $categoryIds, $curPage, $pageSize, $appId);
		return $this->recv_getGlobalProductIdsByBrandIdAndCategoryId();
	}
	
	public function send_getGlobalProductIdsByBrandIdAndCategoryId( $owner, $brandId, $categoryIds, $curPage, $pageSize, $appId){
		
		$this->initInvocation("getGlobalProductIdsByBrandIdAndCategoryId");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalProductIdsByBrandIdAndCategoryId_args();
		
		$args->owner = $owner;
		
		$args->brandId = $brandId;
		
		$args->categoryIds = $categoryIds;
		
		$args->curPage = $curPage;
		
		$args->pageSize = $pageSize;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_getGlobalProductIdsByBrandIdAndCategoryId(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalProductIdsByBrandIdAndCategoryId_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getGlobalProductIdsByCondition( $owner, $snList, $isNewBarcode, $barcodeList, $brandId, $categoryIds, $curPage, $pageSize, $appId){
		
		$this->send_getGlobalProductIdsByCondition( $owner, $snList, $isNewBarcode, $barcodeList, $brandId, $categoryIds, $curPage, $pageSize, $appId);
		return $this->recv_getGlobalProductIdsByCondition();
	}
	
	public function send_getGlobalProductIdsByCondition( $owner, $snList, $isNewBarcode, $barcodeList, $brandId, $categoryIds, $curPage, $pageSize, $appId){
		
		$this->initInvocation("getGlobalProductIdsByCondition");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalProductIdsByCondition_args();
		
		$args->owner = $owner;
		
		$args->snList = $snList;
		
		$args->isNewBarcode = $isNewBarcode;
		
		$args->barcodeList = $barcodeList;
		
		$args->brandId = $brandId;
		
		$args->categoryIds = $categoryIds;
		
		$args->curPage = $curPage;
		
		$args->pageSize = $pageSize;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_getGlobalProductIdsByCondition(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalProductIdsByCondition_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getGlobalProductInfoByBarcode( $owner, $barcodeList, $appId){
		
		$this->send_getGlobalProductInfoByBarcode( $owner, $barcodeList, $appId);
		return $this->recv_getGlobalProductInfoByBarcode();
	}
	
	public function send_getGlobalProductInfoByBarcode( $owner, $barcodeList, $appId){
		
		$this->initInvocation("getGlobalProductInfoByBarcode");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalProductInfoByBarcode_args();
		
		$args->owner = $owner;
		
		$args->barcodeList = $barcodeList;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_getGlobalProductInfoByBarcode(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalProductInfoByBarcode_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getGlobalProductListByIds( $owner, $globalProductIds, $optionList, $appId){
		
		$this->send_getGlobalProductListByIds( $owner, $globalProductIds, $optionList, $appId);
		return $this->recv_getGlobalProductListByIds();
	}
	
	public function send_getGlobalProductListByIds( $owner, $globalProductIds, $optionList, $appId){
		
		$this->initInvocation("getGlobalProductListByIds");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalProductListByIds_args();
		
		$args->owner = $owner;
		
		$args->globalProductIds = $globalProductIds;
		
		$args->optionList = $optionList;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_getGlobalProductListByIds(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalProductListByIds_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getGlobalSkuForEBSAndDelSkuSyncRecord( $globalSkuForEbsParams, $apiKey, $appId){
		
		$this->send_getGlobalSkuForEBSAndDelSkuSyncRecord( $globalSkuForEbsParams, $apiKey, $appId);
		return $this->recv_getGlobalSkuForEBSAndDelSkuSyncRecord();
	}
	
	public function send_getGlobalSkuForEBSAndDelSkuSyncRecord( $globalSkuForEbsParams, $apiKey, $appId){
		
		$this->initInvocation("getGlobalSkuForEBSAndDelSkuSyncRecord");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalSkuForEBSAndDelSkuSyncRecord_args();
		
		$args->globalSkuForEbsParams = $globalSkuForEbsParams;
		
		$args->apiKey = $apiKey;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_getGlobalSkuForEBSAndDelSkuSyncRecord(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalSkuForEBSAndDelSkuSyncRecord_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getGlobalSkuIdsByBrandIdAndCategoryId( $owner, $globalSkuIds, $brandId, $categoryIds, $curPage, $pageSize, $appId){
		
		$this->send_getGlobalSkuIdsByBrandIdAndCategoryId( $owner, $globalSkuIds, $brandId, $categoryIds, $curPage, $pageSize, $appId);
		return $this->recv_getGlobalSkuIdsByBrandIdAndCategoryId();
	}
	
	public function send_getGlobalSkuIdsByBrandIdAndCategoryId( $owner, $globalSkuIds, $brandId, $categoryIds, $curPage, $pageSize, $appId){
		
		$this->initInvocation("getGlobalSkuIdsByBrandIdAndCategoryId");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalSkuIdsByBrandIdAndCategoryId_args();
		
		$args->owner = $owner;
		
		$args->globalSkuIds = $globalSkuIds;
		
		$args->brandId = $brandId;
		
		$args->categoryIds = $categoryIds;
		
		$args->curPage = $curPage;
		
		$args->pageSize = $pageSize;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_getGlobalSkuIdsByBrandIdAndCategoryId(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalSkuIdsByBrandIdAndCategoryId_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getGlobalSkuIdsByCondition( $owner, $snList, $isNewBarcode, $barcodeList, $brandId, $curPage, $pageSize, $appId){
		
		$this->send_getGlobalSkuIdsByCondition( $owner, $snList, $isNewBarcode, $barcodeList, $brandId, $curPage, $pageSize, $appId);
		return $this->recv_getGlobalSkuIdsByCondition();
	}
	
	public function send_getGlobalSkuIdsByCondition( $owner, $snList, $isNewBarcode, $barcodeList, $brandId, $curPage, $pageSize, $appId){
		
		$this->initInvocation("getGlobalSkuIdsByCondition");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalSkuIdsByCondition_args();
		
		$args->owner = $owner;
		
		$args->snList = $snList;
		
		$args->isNewBarcode = $isNewBarcode;
		
		$args->barcodeList = $barcodeList;
		
		$args->brandId = $brandId;
		
		$args->curPage = $curPage;
		
		$args->pageSize = $pageSize;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_getGlobalSkuIdsByCondition(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalSkuIdsByCondition_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getGlobalSkuIndexList( $owner, $barcodeAndVendorIdList, $appId){
		
		$this->send_getGlobalSkuIndexList( $owner, $barcodeAndVendorIdList, $appId);
		return $this->recv_getGlobalSkuIndexList();
	}
	
	public function send_getGlobalSkuIndexList( $owner, $barcodeAndVendorIdList, $appId){
		
		$this->initInvocation("getGlobalSkuIndexList");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalSkuIndexList_args();
		
		$args->owner = $owner;
		
		$args->barcodeAndVendorIdList = $barcodeAndVendorIdList;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_getGlobalSkuIndexList(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalSkuIndexList_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getGlobalSkuListByIds( $owner, $globalSkuIds, $optionList, $appId){
		
		$this->send_getGlobalSkuListByIds( $owner, $globalSkuIds, $optionList, $appId);
		return $this->recv_getGlobalSkuListByIds();
	}
	
	public function send_getGlobalSkuListByIds( $owner, $globalSkuIds, $optionList, $appId){
		
		$this->initInvocation("getGlobalSkuListByIds");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalSkuListByIds_args();
		
		$args->owner = $owner;
		
		$args->globalSkuIds = $globalSkuIds;
		
		$args->optionList = $optionList;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_getGlobalSkuListByIds(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalSkuListByIds_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getGlobalSkuListByProductId( $owner, $globalProductId, $optionList, $appId){
		
		$this->send_getGlobalSkuListByProductId( $owner, $globalProductId, $optionList, $appId);
		return $this->recv_getGlobalSkuListByProductId();
	}
	
	public function send_getGlobalSkuListByProductId( $owner, $globalProductId, $optionList, $appId){
		
		$this->initInvocation("getGlobalSkuListByProductId");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalSkuListByProductId_args();
		
		$args->owner = $owner;
		
		$args->globalProductId = $globalProductId;
		
		$args->optionList = $optionList;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_getGlobalSkuListByProductId(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalSkuListByProductId_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function getGlobalSkuOwnerExpListByBarcode( $owner, $barcodeList, $appId){
		
		$this->send_getGlobalSkuOwnerExpListByBarcode( $owner, $barcodeList, $appId);
		return $this->recv_getGlobalSkuOwnerExpListByBarcode();
	}
	
	public function send_getGlobalSkuOwnerExpListByBarcode( $owner, $barcodeList, $appId){
		
		$this->initInvocation("getGlobalSkuOwnerExpListByBarcode");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalSkuOwnerExpListByBarcode_args();
		
		$args->owner = $owner;
		
		$args->barcodeList = $barcodeList;
		
		$args->appId = $appId;
		
		$this->send_base($args);
	}
	
	public function recv_getGlobalSkuOwnerExpListByBarcode(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_getGlobalSkuOwnerExpListByBarcode_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function healthCheck(){
		
		$this->send_healthCheck();
		return $this->recv_healthCheck();
	}
	
	public function send_healthCheck(){
		
		$this->initInvocation("healthCheck");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_healthCheck_args();
		
		$this->send_base($args);
	}
	
	public function recv_healthCheck(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_healthCheck_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
	public function sentVmsToXstore( $owner, $globalSkuId, $globalProductId, $appId, $operateType, $barcode){
		
		$this->send_sentVmsToXstore( $owner, $globalSkuId, $globalProductId, $appId, $operateType, $barcode);
		return $this->recv_sentVmsToXstore();
	}
	
	public function send_sentVmsToXstore( $owner, $globalSkuId, $globalProductId, $appId, $operateType, $barcode){
		
		$this->initInvocation("sentVmsToXstore");
		$args = new \com\vip\product\gpdc\service\ProductGpdcService_sentVmsToXstore_args();
		
		$args->owner = $owner;
		
		$args->globalSkuId = $globalSkuId;
		
		$args->globalProductId = $globalProductId;
		
		$args->appId = $appId;
		
		$args->operateType = $operateType;
		
		$args->barcode = $barcode;
		
		$this->send_base($args);
	}
	
	public function recv_sentVmsToXstore(){
		
		$result = new \com\vip\product\gpdc\service\ProductGpdcService_sentVmsToXstore_result();
		$this->receive_base($result);
		if ($result->success !== null){
			
			return $result->success;
		}
		
	}
	
	
}




class ProductGpdcService_batchGeneralNewBarcode_args {
	
	static $_TSPEC;
	public $owner = null;
	public $barcodeMappingList = null;
	public $operator = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'barcodeMappingList'
			),
			3 => array(
			'var' => 'operator'
			),
			4 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['barcodeMappingList'])){
				
				$this->barcodeMappingList = $vals['barcodeMappingList'];
			}
			
			
			if (isset($vals['operator'])){
				
				$this->operator = $vals['operator'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->barcodeMappingList = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					
					$elem0 = new \com\vip\product\gpdc\service\BarcodeMapping();
					$elem0->read($input);
					
					$this->barcodeMappingList[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->operator);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcodeMappingList !== null) {
			
			$xfer += $output->writeFieldBegin('barcodeMappingList');
			
			if (!is_array($this->barcodeMappingList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->barcodeMappingList as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->operator !== null) {
			
			$xfer += $output->writeFieldBegin('operator');
			$xfer += $output->writeString($this->operator);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_batchSaveOrUpdatedGlobalSkuOwnerExpList_args {
	
	static $_TSPEC;
	public $owner = null;
	public $globalSkuOwnerExpList = null;
	public $operator = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'globalSkuOwnerExpList'
			),
			3 => array(
			'var' => 'operator'
			),
			4 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['globalSkuOwnerExpList'])){
				
				$this->globalSkuOwnerExpList = $vals['globalSkuOwnerExpList'];
			}
			
			
			if (isset($vals['operator'])){
				
				$this->operator = $vals['operator'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->globalSkuOwnerExpList = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					
					$elem0 = new \com\vip\product\gpdc\service\GlobalSkuOwnerExp();
					$elem0->read($input);
					
					$this->globalSkuOwnerExpList[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->operator);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->globalSkuOwnerExpList !== null) {
			
			$xfer += $output->writeFieldBegin('globalSkuOwnerExpList');
			
			if (!is_array($this->globalSkuOwnerExpList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->globalSkuOwnerExpList as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->operator !== null) {
			
			$xfer += $output->writeFieldBegin('operator');
			$xfer += $output->writeString($this->operator);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_countGlobalProductIdsByBrandIdAndCategoryId_args {
	
	static $_TSPEC;
	public $owner = null;
	public $brandId = null;
	public $categoryIds = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'brandId'
			),
			3 => array(
			'var' => 'categoryIds'
			),
			4 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['brandId'])){
				
				$this->brandId = $vals['brandId'];
			}
			
			
			if (isset($vals['categoryIds'])){
				
				$this->categoryIds = $vals['categoryIds'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->brandId); 
			
		}
		
		
		
		
		if(true) {
			
			
			$this->categoryIds = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					$input->readI32($elem0); 
					
					$this->categoryIds[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandId !== null) {
			
			$xfer += $output->writeFieldBegin('brandId');
			$xfer += $output->writeI32($this->brandId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->categoryIds !== null) {
			
			$xfer += $output->writeFieldBegin('categoryIds');
			
			if (!is_array($this->categoryIds)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->categoryIds as $iter0){
				
				$xfer += $output->writeI32($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_countGlobalProductIdsByCondition_args {
	
	static $_TSPEC;
	public $owner = null;
	public $snList = null;
	public $isNewBarcode = null;
	public $barcodeList = null;
	public $brandId = null;
	public $categoryIds = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'snList'
			),
			3 => array(
			'var' => 'isNewBarcode'
			),
			4 => array(
			'var' => 'barcodeList'
			),
			5 => array(
			'var' => 'brandId'
			),
			6 => array(
			'var' => 'categoryIds'
			),
			7 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['snList'])){
				
				$this->snList = $vals['snList'];
			}
			
			
			if (isset($vals['isNewBarcode'])){
				
				$this->isNewBarcode = $vals['isNewBarcode'];
			}
			
			
			if (isset($vals['barcodeList'])){
				
				$this->barcodeList = $vals['barcodeList'];
			}
			
			
			if (isset($vals['brandId'])){
				
				$this->brandId = $vals['brandId'];
			}
			
			
			if (isset($vals['categoryIds'])){
				
				$this->categoryIds = $vals['categoryIds'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->snList = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					$input->readString($elem0);
					
					$this->snList[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readBool($this->isNewBarcode);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->barcodeList = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					$input->readString($elem1);
					
					$this->barcodeList[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->brandId); 
			
		}
		
		
		
		
		if(true) {
			
			
			$this->categoryIds = array();
			$_size2 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem2 = null;
					$input->readI32($elem2); 
					
					$this->categoryIds[$_size2++] = $elem2;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->snList !== null) {
			
			$xfer += $output->writeFieldBegin('snList');
			
			if (!is_array($this->snList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->snList as $iter0){
				
				$xfer += $output->writeString($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isNewBarcode !== null) {
			
			$xfer += $output->writeFieldBegin('isNewBarcode');
			$xfer += $output->writeBool($this->isNewBarcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcodeList !== null) {
			
			$xfer += $output->writeFieldBegin('barcodeList');
			
			if (!is_array($this->barcodeList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->barcodeList as $iter0){
				
				$xfer += $output->writeString($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandId !== null) {
			
			$xfer += $output->writeFieldBegin('brandId');
			$xfer += $output->writeI32($this->brandId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->categoryIds !== null) {
			
			$xfer += $output->writeFieldBegin('categoryIds');
			
			if (!is_array($this->categoryIds)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->categoryIds as $iter0){
				
				$xfer += $output->writeI32($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_countGlobalSkuIdsByBrandIdAndCategoryId_args {
	
	static $_TSPEC;
	public $owner = null;
	public $globalSkuIds = null;
	public $brandId = null;
	public $categoryIds = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'globalSkuIds'
			),
			3 => array(
			'var' => 'brandId'
			),
			4 => array(
			'var' => 'categoryIds'
			),
			5 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['globalSkuIds'])){
				
				$this->globalSkuIds = $vals['globalSkuIds'];
			}
			
			
			if (isset($vals['brandId'])){
				
				$this->brandId = $vals['brandId'];
			}
			
			
			if (isset($vals['categoryIds'])){
				
				$this->categoryIds = $vals['categoryIds'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->globalSkuIds = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					$input->readI64($elem0); 
					
					$this->globalSkuIds[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->brandId); 
			
		}
		
		
		
		
		if(true) {
			
			
			$this->categoryIds = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					$input->readI32($elem1); 
					
					$this->categoryIds[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->globalSkuIds !== null) {
			
			$xfer += $output->writeFieldBegin('globalSkuIds');
			
			if (!is_array($this->globalSkuIds)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->globalSkuIds as $iter0){
				
				$xfer += $output->writeI64($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandId !== null) {
			
			$xfer += $output->writeFieldBegin('brandId');
			$xfer += $output->writeI32($this->brandId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->categoryIds !== null) {
			
			$xfer += $output->writeFieldBegin('categoryIds');
			
			if (!is_array($this->categoryIds)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->categoryIds as $iter0){
				
				$xfer += $output->writeI32($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_countGlobalSkuIdsByCondition_args {
	
	static $_TSPEC;
	public $owner = null;
	public $snList = null;
	public $isNewBarcode = null;
	public $barcodeList = null;
	public $brandId = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'snList'
			),
			3 => array(
			'var' => 'isNewBarcode'
			),
			4 => array(
			'var' => 'barcodeList'
			),
			5 => array(
			'var' => 'brandId'
			),
			6 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['snList'])){
				
				$this->snList = $vals['snList'];
			}
			
			
			if (isset($vals['isNewBarcode'])){
				
				$this->isNewBarcode = $vals['isNewBarcode'];
			}
			
			
			if (isset($vals['barcodeList'])){
				
				$this->barcodeList = $vals['barcodeList'];
			}
			
			
			if (isset($vals['brandId'])){
				
				$this->brandId = $vals['brandId'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->snList = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					$input->readString($elem0);
					
					$this->snList[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readBool($this->isNewBarcode);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->barcodeList = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					$input->readString($elem1);
					
					$this->barcodeList[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->brandId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->snList !== null) {
			
			$xfer += $output->writeFieldBegin('snList');
			
			if (!is_array($this->snList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->snList as $iter0){
				
				$xfer += $output->writeString($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isNewBarcode !== null) {
			
			$xfer += $output->writeFieldBegin('isNewBarcode');
			$xfer += $output->writeBool($this->isNewBarcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcodeList !== null) {
			
			$xfer += $output->writeFieldBegin('barcodeList');
			
			if (!is_array($this->barcodeList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->barcodeList as $iter0){
				
				$xfer += $output->writeString($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandId !== null) {
			
			$xfer += $output->writeFieldBegin('brandId');
			$xfer += $output->writeI32($this->brandId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalProduct_args {
	
	static $_TSPEC;
	public $owner = null;
	public $globalProductId = null;
	public $optionList = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'globalProductId'
			),
			3 => array(
			'var' => 'optionList'
			),
			4 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['globalProductId'])){
				
				$this->globalProductId = $vals['globalProductId'];
			}
			
			
			if (isset($vals['optionList'])){
				
				$this->optionList = $vals['optionList'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			$input->readI64($this->globalProductId); 
			
		}
		
		
		
		
		if(true) {
			
			
			$this->optionList = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					
					$names = \com\vip\product\gpdc\service\ResultOptions::$__names;
					$name = null;
					$input->readString($name);
					foreach ($names as $k => $v){
						
						if($name == $v){
							
							$elem0 = $k;
							break;
						}
						
					}
					
					
					$this->optionList[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->globalProductId !== null) {
			
			$xfer += $output->writeFieldBegin('globalProductId');
			$xfer += $output->writeI64($this->globalProductId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->optionList !== null) {
			
			$xfer += $output->writeFieldBegin('optionList');
			
			if (!is_array($this->optionList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->optionList as $iter0){
				
				
				$em = new \com\vip\product\gpdc\service\ResultOptions; 
				$output->writeString($em::$__names[$iter0]);  
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalProductIdsByBrandIdAndCategoryId_args {
	
	static $_TSPEC;
	public $owner = null;
	public $brandId = null;
	public $categoryIds = null;
	public $curPage = null;
	public $pageSize = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'brandId'
			),
			3 => array(
			'var' => 'categoryIds'
			),
			4 => array(
			'var' => 'curPage'
			),
			5 => array(
			'var' => 'pageSize'
			),
			6 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['brandId'])){
				
				$this->brandId = $vals['brandId'];
			}
			
			
			if (isset($vals['categoryIds'])){
				
				$this->categoryIds = $vals['categoryIds'];
			}
			
			
			if (isset($vals['curPage'])){
				
				$this->curPage = $vals['curPage'];
			}
			
			
			if (isset($vals['pageSize'])){
				
				$this->pageSize = $vals['pageSize'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->brandId); 
			
		}
		
		
		
		
		if(true) {
			
			
			$this->categoryIds = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					$input->readI32($elem0); 
					
					$this->categoryIds[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->curPage); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->pageSize); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandId !== null) {
			
			$xfer += $output->writeFieldBegin('brandId');
			$xfer += $output->writeI32($this->brandId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->categoryIds !== null) {
			
			$xfer += $output->writeFieldBegin('categoryIds');
			
			if (!is_array($this->categoryIds)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->categoryIds as $iter0){
				
				$xfer += $output->writeI32($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('curPage');
		$xfer += $output->writeI32($this->curPage);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('pageSize');
		$xfer += $output->writeI32($this->pageSize);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalProductIdsByCondition_args {
	
	static $_TSPEC;
	public $owner = null;
	public $snList = null;
	public $isNewBarcode = null;
	public $barcodeList = null;
	public $brandId = null;
	public $categoryIds = null;
	public $curPage = null;
	public $pageSize = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'snList'
			),
			3 => array(
			'var' => 'isNewBarcode'
			),
			4 => array(
			'var' => 'barcodeList'
			),
			5 => array(
			'var' => 'brandId'
			),
			6 => array(
			'var' => 'categoryIds'
			),
			7 => array(
			'var' => 'curPage'
			),
			8 => array(
			'var' => 'pageSize'
			),
			9 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['snList'])){
				
				$this->snList = $vals['snList'];
			}
			
			
			if (isset($vals['isNewBarcode'])){
				
				$this->isNewBarcode = $vals['isNewBarcode'];
			}
			
			
			if (isset($vals['barcodeList'])){
				
				$this->barcodeList = $vals['barcodeList'];
			}
			
			
			if (isset($vals['brandId'])){
				
				$this->brandId = $vals['brandId'];
			}
			
			
			if (isset($vals['categoryIds'])){
				
				$this->categoryIds = $vals['categoryIds'];
			}
			
			
			if (isset($vals['curPage'])){
				
				$this->curPage = $vals['curPage'];
			}
			
			
			if (isset($vals['pageSize'])){
				
				$this->pageSize = $vals['pageSize'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->snList = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					$input->readString($elem0);
					
					$this->snList[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readBool($this->isNewBarcode);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->barcodeList = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					$input->readString($elem1);
					
					$this->barcodeList[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->brandId); 
			
		}
		
		
		
		
		if(true) {
			
			
			$this->categoryIds = array();
			$_size2 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem2 = null;
					$input->readI32($elem2); 
					
					$this->categoryIds[$_size2++] = $elem2;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->curPage); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->pageSize); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->snList !== null) {
			
			$xfer += $output->writeFieldBegin('snList');
			
			if (!is_array($this->snList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->snList as $iter0){
				
				$xfer += $output->writeString($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isNewBarcode !== null) {
			
			$xfer += $output->writeFieldBegin('isNewBarcode');
			$xfer += $output->writeBool($this->isNewBarcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcodeList !== null) {
			
			$xfer += $output->writeFieldBegin('barcodeList');
			
			if (!is_array($this->barcodeList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->barcodeList as $iter0){
				
				$xfer += $output->writeString($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandId !== null) {
			
			$xfer += $output->writeFieldBegin('brandId');
			$xfer += $output->writeI32($this->brandId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->categoryIds !== null) {
			
			$xfer += $output->writeFieldBegin('categoryIds');
			
			if (!is_array($this->categoryIds)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->categoryIds as $iter0){
				
				$xfer += $output->writeI32($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('curPage');
		$xfer += $output->writeI32($this->curPage);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('pageSize');
		$xfer += $output->writeI32($this->pageSize);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalProductInfoByBarcode_args {
	
	static $_TSPEC;
	public $owner = null;
	public $barcodeList = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'barcodeList'
			),
			3 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['barcodeList'])){
				
				$this->barcodeList = $vals['barcodeList'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->barcodeList = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					$input->readString($elem0);
					
					$this->barcodeList[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcodeList !== null) {
			
			$xfer += $output->writeFieldBegin('barcodeList');
			
			if (!is_array($this->barcodeList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->barcodeList as $iter0){
				
				$xfer += $output->writeString($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalProductListByIds_args {
	
	static $_TSPEC;
	public $owner = null;
	public $globalProductIds = null;
	public $optionList = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'globalProductIds'
			),
			3 => array(
			'var' => 'optionList'
			),
			4 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['globalProductIds'])){
				
				$this->globalProductIds = $vals['globalProductIds'];
			}
			
			
			if (isset($vals['optionList'])){
				
				$this->optionList = $vals['optionList'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->globalProductIds = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					$input->readI64($elem0); 
					
					$this->globalProductIds[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			
			$this->optionList = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					
					$names = \com\vip\product\gpdc\service\ResultOptions::$__names;
					$name = null;
					$input->readString($name);
					foreach ($names as $k => $v){
						
						if($name == $v){
							
							$elem1 = $k;
							break;
						}
						
					}
					
					
					$this->optionList[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->globalProductIds !== null) {
			
			$xfer += $output->writeFieldBegin('globalProductIds');
			
			if (!is_array($this->globalProductIds)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->globalProductIds as $iter0){
				
				$xfer += $output->writeI64($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->optionList !== null) {
			
			$xfer += $output->writeFieldBegin('optionList');
			
			if (!is_array($this->optionList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->optionList as $iter0){
				
				
				$em = new \com\vip\product\gpdc\service\ResultOptions; 
				$output->writeString($em::$__names[$iter0]);  
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalSkuForEBSAndDelSkuSyncRecord_args {
	
	static $_TSPEC;
	public $globalSkuForEbsParams = null;
	public $apiKey = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'globalSkuForEbsParams'
			),
			2 => array(
			'var' => 'apiKey'
			),
			3 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['globalSkuForEbsParams'])){
				
				$this->globalSkuForEbsParams = $vals['globalSkuForEbsParams'];
			}
			
			
			if (isset($vals['apiKey'])){
				
				$this->apiKey = $vals['apiKey'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->globalSkuForEbsParams = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					
					$elem0 = new \com\vip\product\gpdc\service\GlobalSkuForEbsParam();
					$elem0->read($input);
					
					$this->globalSkuForEbsParams[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->apiKey);
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->globalSkuForEbsParams !== null) {
			
			$xfer += $output->writeFieldBegin('globalSkuForEbsParams');
			
			if (!is_array($this->globalSkuForEbsParams)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->globalSkuForEbsParams as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->apiKey !== null) {
			
			$xfer += $output->writeFieldBegin('apiKey');
			$xfer += $output->writeString($this->apiKey);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalSkuIdsByBrandIdAndCategoryId_args {
	
	static $_TSPEC;
	public $owner = null;
	public $globalSkuIds = null;
	public $brandId = null;
	public $categoryIds = null;
	public $curPage = null;
	public $pageSize = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'globalSkuIds'
			),
			3 => array(
			'var' => 'brandId'
			),
			4 => array(
			'var' => 'categoryIds'
			),
			5 => array(
			'var' => 'curPage'
			),
			6 => array(
			'var' => 'pageSize'
			),
			7 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['globalSkuIds'])){
				
				$this->globalSkuIds = $vals['globalSkuIds'];
			}
			
			
			if (isset($vals['brandId'])){
				
				$this->brandId = $vals['brandId'];
			}
			
			
			if (isset($vals['categoryIds'])){
				
				$this->categoryIds = $vals['categoryIds'];
			}
			
			
			if (isset($vals['curPage'])){
				
				$this->curPage = $vals['curPage'];
			}
			
			
			if (isset($vals['pageSize'])){
				
				$this->pageSize = $vals['pageSize'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->globalSkuIds = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					$input->readI64($elem0); 
					
					$this->globalSkuIds[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->brandId); 
			
		}
		
		
		
		
		if(true) {
			
			
			$this->categoryIds = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					$input->readI32($elem1); 
					
					$this->categoryIds[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->curPage); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->pageSize); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->globalSkuIds !== null) {
			
			$xfer += $output->writeFieldBegin('globalSkuIds');
			
			if (!is_array($this->globalSkuIds)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->globalSkuIds as $iter0){
				
				$xfer += $output->writeI64($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandId !== null) {
			
			$xfer += $output->writeFieldBegin('brandId');
			$xfer += $output->writeI32($this->brandId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->categoryIds !== null) {
			
			$xfer += $output->writeFieldBegin('categoryIds');
			
			if (!is_array($this->categoryIds)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->categoryIds as $iter0){
				
				$xfer += $output->writeI32($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('curPage');
		$xfer += $output->writeI32($this->curPage);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('pageSize');
		$xfer += $output->writeI32($this->pageSize);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalSkuIdsByCondition_args {
	
	static $_TSPEC;
	public $owner = null;
	public $snList = null;
	public $isNewBarcode = null;
	public $barcodeList = null;
	public $brandId = null;
	public $curPage = null;
	public $pageSize = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'snList'
			),
			3 => array(
			'var' => 'isNewBarcode'
			),
			4 => array(
			'var' => 'barcodeList'
			),
			5 => array(
			'var' => 'brandId'
			),
			6 => array(
			'var' => 'curPage'
			),
			7 => array(
			'var' => 'pageSize'
			),
			8 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['snList'])){
				
				$this->snList = $vals['snList'];
			}
			
			
			if (isset($vals['isNewBarcode'])){
				
				$this->isNewBarcode = $vals['isNewBarcode'];
			}
			
			
			if (isset($vals['barcodeList'])){
				
				$this->barcodeList = $vals['barcodeList'];
			}
			
			
			if (isset($vals['brandId'])){
				
				$this->brandId = $vals['brandId'];
			}
			
			
			if (isset($vals['curPage'])){
				
				$this->curPage = $vals['curPage'];
			}
			
			
			if (isset($vals['pageSize'])){
				
				$this->pageSize = $vals['pageSize'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->snList = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					$input->readString($elem0);
					
					$this->snList[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readBool($this->isNewBarcode);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->barcodeList = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					$input->readString($elem1);
					
					$this->barcodeList[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->brandId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->curPage); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->pageSize); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->snList !== null) {
			
			$xfer += $output->writeFieldBegin('snList');
			
			if (!is_array($this->snList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->snList as $iter0){
				
				$xfer += $output->writeString($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->isNewBarcode !== null) {
			
			$xfer += $output->writeFieldBegin('isNewBarcode');
			$xfer += $output->writeBool($this->isNewBarcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcodeList !== null) {
			
			$xfer += $output->writeFieldBegin('barcodeList');
			
			if (!is_array($this->barcodeList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->barcodeList as $iter0){
				
				$xfer += $output->writeString($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->brandId !== null) {
			
			$xfer += $output->writeFieldBegin('brandId');
			$xfer += $output->writeI32($this->brandId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('curPage');
		$xfer += $output->writeI32($this->curPage);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('pageSize');
		$xfer += $output->writeI32($this->pageSize);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalSkuIndexList_args {
	
	static $_TSPEC;
	public $owner = null;
	public $barcodeAndVendorIdList = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'barcodeAndVendorIdList'
			),
			3 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['barcodeAndVendorIdList'])){
				
				$this->barcodeAndVendorIdList = $vals['barcodeAndVendorIdList'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->barcodeAndVendorIdList = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					
					$elem0 = new \com\vip\product\gpdc\service\BarcodeAndVendorId();
					$elem0->read($input);
					
					$this->barcodeAndVendorIdList[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcodeAndVendorIdList !== null) {
			
			$xfer += $output->writeFieldBegin('barcodeAndVendorIdList');
			
			if (!is_array($this->barcodeAndVendorIdList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->barcodeAndVendorIdList as $iter0){
				
				
				if (!is_object($iter0)) {
					
					throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
				}
				
				$xfer += $iter0->write($output);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalSkuListByIds_args {
	
	static $_TSPEC;
	public $owner = null;
	public $globalSkuIds = null;
	public $optionList = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'globalSkuIds'
			),
			3 => array(
			'var' => 'optionList'
			),
			4 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['globalSkuIds'])){
				
				$this->globalSkuIds = $vals['globalSkuIds'];
			}
			
			
			if (isset($vals['optionList'])){
				
				$this->optionList = $vals['optionList'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->globalSkuIds = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					$input->readI64($elem0); 
					
					$this->globalSkuIds[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			
			$this->optionList = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					
					$names = \com\vip\product\gpdc\service\ResultOptions::$__names;
					$name = null;
					$input->readString($name);
					foreach ($names as $k => $v){
						
						if($name == $v){
							
							$elem1 = $k;
							break;
						}
						
					}
					
					
					$this->optionList[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->globalSkuIds !== null) {
			
			$xfer += $output->writeFieldBegin('globalSkuIds');
			
			if (!is_array($this->globalSkuIds)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->globalSkuIds as $iter0){
				
				$xfer += $output->writeI64($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->optionList !== null) {
			
			$xfer += $output->writeFieldBegin('optionList');
			
			if (!is_array($this->optionList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->optionList as $iter0){
				
				
				$em = new \com\vip\product\gpdc\service\ResultOptions; 
				$output->writeString($em::$__names[$iter0]);  
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalSkuListByProductId_args {
	
	static $_TSPEC;
	public $owner = null;
	public $globalProductId = null;
	public $optionList = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'globalProductId'
			),
			3 => array(
			'var' => 'optionList'
			),
			4 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['globalProductId'])){
				
				$this->globalProductId = $vals['globalProductId'];
			}
			
			
			if (isset($vals['optionList'])){
				
				$this->optionList = $vals['optionList'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			$input->readI64($this->globalProductId); 
			
		}
		
		
		
		
		if(true) {
			
			
			$this->optionList = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					
					$names = \com\vip\product\gpdc\service\ResultOptions::$__names;
					$name = null;
					$input->readString($name);
					foreach ($names as $k => $v){
						
						if($name == $v){
							
							$elem0 = $k;
							break;
						}
						
					}
					
					
					$this->optionList[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->globalProductId !== null) {
			
			$xfer += $output->writeFieldBegin('globalProductId');
			$xfer += $output->writeI64($this->globalProductId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->optionList !== null) {
			
			$xfer += $output->writeFieldBegin('optionList');
			
			if (!is_array($this->optionList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->optionList as $iter0){
				
				
				$em = new \com\vip\product\gpdc\service\ResultOptions; 
				$output->writeString($em::$__names[$iter0]);  
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalSkuOwnerExpListByBarcode_args {
	
	static $_TSPEC;
	public $owner = null;
	public $barcodeList = null;
	public $appId = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'barcodeList'
			),
			3 => array(
			'var' => 'appId'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['barcodeList'])){
				
				$this->barcodeList = $vals['barcodeList'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			
			$this->barcodeList = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					$input->readString($elem0);
					
					$this->barcodeList[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcodeList !== null) {
			
			$xfer += $output->writeFieldBegin('barcodeList');
			
			if (!is_array($this->barcodeList)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->barcodeList as $iter0){
				
				$xfer += $output->writeString($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_healthCheck_args {
	
	static $_TSPEC;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			
			);
			
		}
		
		if (is_array($vals)){
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_sentVmsToXstore_args {
	
	static $_TSPEC;
	public $owner = null;
	public $globalSkuId = null;
	public $globalProductId = null;
	public $appId = null;
	public $operateType = null;
	public $barcode = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'owner'
			),
			2 => array(
			'var' => 'globalSkuId'
			),
			3 => array(
			'var' => 'globalProductId'
			),
			4 => array(
			'var' => 'appId'
			),
			5 => array(
			'var' => 'operateType'
			),
			6 => array(
			'var' => 'barcode'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['owner'])){
				
				$this->owner = $vals['owner'];
			}
			
			
			if (isset($vals['globalSkuId'])){
				
				$this->globalSkuId = $vals['globalSkuId'];
			}
			
			
			if (isset($vals['globalProductId'])){
				
				$this->globalProductId = $vals['globalProductId'];
			}
			
			
			if (isset($vals['appId'])){
				
				$this->appId = $vals['appId'];
			}
			
			
			if (isset($vals['operateType'])){
				
				$this->operateType = $vals['operateType'];
			}
			
			
			if (isset($vals['barcode'])){
				
				$this->barcode = $vals['barcode'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readString($this->owner);
			
		}
		
		
		
		
		if(true) {
			
			$input->readI64($this->globalSkuId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readI64($this->globalProductId); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->appId);
			
		}
		
		
		
		
		if(true) {
			
			$input->readI32($this->operateType); 
			
		}
		
		
		
		
		if(true) {
			
			$input->readString($this->barcode);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->owner !== null) {
			
			$xfer += $output->writeFieldBegin('owner');
			$xfer += $output->writeString($this->owner);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldBegin('globalSkuId');
		$xfer += $output->writeI64($this->globalSkuId);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('globalProductId');
		$xfer += $output->writeI64($this->globalProductId);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->appId !== null) {
			
			$xfer += $output->writeFieldBegin('appId');
			$xfer += $output->writeString($this->appId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->operateType !== null) {
			
			$xfer += $output->writeFieldBegin('operateType');
			$xfer += $output->writeI32($this->operateType);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->barcode !== null) {
			
			$xfer += $output->writeFieldBegin('barcode');
			$xfer += $output->writeString($this->barcode);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_batchGeneralNewBarcode_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					
					$elem0 = new \com\vip\product\gpdc\service\BarcodeMapping();
					$elem0->read($input);
					
					$this->success[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				
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




class ProductGpdcService_batchSaveOrUpdatedGlobalSkuOwnerExpList_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					
					$elem1 = new \com\vip\product\gpdc\service\GlobalSkuOwnerExp();
					$elem1->read($input);
					
					$this->success[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				
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




class ProductGpdcService_countGlobalProductIdsByBrandIdAndCategoryId_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->success); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			$xfer += $output->writeI64($this->success);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_countGlobalProductIdsByCondition_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->success); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			$xfer += $output->writeI64($this->success);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_countGlobalSkuIdsByBrandIdAndCategoryId_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->success); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('success');
		$xfer += $output->writeI64($this->success);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_countGlobalSkuIdsByCondition_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readI64($this->success); 
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('success');
		$xfer += $output->writeI64($this->success);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalProduct_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = new \com\vip\product\gpdc\service\GlobalProduct();
			$this->success->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_object($this->success)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->success->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalProductIdsByBrandIdAndCategoryId_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size0 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem0 = null;
					$input->readI64($elem0); 
					
					$this->success[$_size0++] = $elem0;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				$xfer += $output->writeI64($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalProductIdsByCondition_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					$input->readI64($elem1); 
					
					$this->success[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				$xfer += $output->writeI64($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalProductInfoByBarcode_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					
					$elem1 = new \com\vip\product\gpdc\service\BriefGlobalProductAndSku();
					$elem1->read($input);
					
					$this->success[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				
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




class ProductGpdcService_getGlobalProductListByIds_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					
					$elem1 = new \com\vip\product\gpdc\service\GlobalProduct();
					$elem1->read($input);
					
					$this->success[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				
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




class ProductGpdcService_getGlobalSkuForEBSAndDelSkuSyncRecord_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					
					$elem1 = new \com\vip\product\gpdc\service\GlobalSkuForEBS();
					$elem1->read($input);
					
					$this->success[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				
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




class ProductGpdcService_getGlobalSkuIdsByBrandIdAndCategoryId_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					$input->readI64($elem1); 
					
					$this->success[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				$xfer += $output->writeI64($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalSkuIdsByCondition_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					$input->readI64($elem1); 
					
					$this->success[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				$xfer += $output->writeI64($iter0);
				
			}
			
			$output->writeListEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_getGlobalSkuIndexList_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					
					$elem1 = new \com\vip\product\gpdc\service\GlobalSkuIndex();
					$elem1->read($input);
					
					$this->success[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				
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




class ProductGpdcService_getGlobalSkuListByIds_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					
					$elem1 = new \com\vip\product\gpdc\service\GlobalSku();
					$elem1->read($input);
					
					$this->success[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				
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




class ProductGpdcService_getGlobalSkuListByProductId_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					
					$elem1 = new \com\vip\product\gpdc\service\GlobalSku();
					$elem1->read($input);
					
					$this->success[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				
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




class ProductGpdcService_getGlobalSkuOwnerExpListByBarcode_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = array();
			$_size1 = 0;
			$input->readListBegin();
			while(true){
				
				try{
					
					$elem1 = null;
					
					$elem1 = new \com\vip\product\gpdc\service\GlobalSkuOwnerExp();
					$elem1->read($input);
					
					$this->success[$_size1++] = $elem1;
				}
				catch(\Exception $e){
					
					break;
				}
			}
			
			$input->readListEnd();
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_array($this->success)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeListBegin();
			foreach ($this->success as $iter0){
				
				
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




class ProductGpdcService_healthCheck_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			
			$this->success = new \com\vip\hermes\core\health\CheckResult();
			$this->success->read($input);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		if($this->success !== null) {
			
			$xfer += $output->writeFieldBegin('success');
			
			if (!is_object($this->success)) {
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$xfer += $this->success->write($output);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




class ProductGpdcService_sentVmsToXstore_result {
	
	static $_TSPEC;
	public $success = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			0 => array(
			'var' => 'success'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['success'])){
				
				$this->success = $vals['success'];
			}
			
			
		}
		
	}
	
	
	public function read($input){
		
		
		
		
		if(true) {
			
			$input->readBool($this->success);
			
		}
		
		
		
		
		
		
	}
	
	public function write($output){
		
		$xfer = 0;
		$xfer += $output->writeStructBegin();
		
		$xfer += $output->writeFieldBegin('success');
		$xfer += $output->writeBool($this->success);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}




?>