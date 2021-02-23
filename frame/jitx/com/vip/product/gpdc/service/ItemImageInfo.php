<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class ItemImageInfo {
	
	static $_TSPEC;
	public $itemId = null;
	public $description = null;
	public $imageUrl = null;
	public $imageType = null;
	public $imageSize = null;
	public $imageIndex = null;
	public $operationModes = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'itemId'
			),
			2 => array(
			'var' => 'description'
			),
			3 => array(
			'var' => 'imageUrl'
			),
			4 => array(
			'var' => 'imageType'
			),
			5 => array(
			'var' => 'imageSize'
			),
			6 => array(
			'var' => 'imageIndex'
			),
			7 => array(
			'var' => 'operationModes'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['itemId'])){
				
				$this->itemId = $vals['itemId'];
			}
			
			
			if (isset($vals['description'])){
				
				$this->description = $vals['description'];
			}
			
			
			if (isset($vals['imageUrl'])){
				
				$this->imageUrl = $vals['imageUrl'];
			}
			
			
			if (isset($vals['imageType'])){
				
				$this->imageType = $vals['imageType'];
			}
			
			
			if (isset($vals['imageSize'])){
				
				$this->imageSize = $vals['imageSize'];
			}
			
			
			if (isset($vals['imageIndex'])){
				
				$this->imageIndex = $vals['imageIndex'];
			}
			
			
			if (isset($vals['operationModes'])){
				
				$this->operationModes = $vals['operationModes'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'ItemImageInfo';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("itemId" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->itemId); 
				
			}
			
			
			
			
			if ("description" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->description);
				
			}
			
			
			
			
			if ("imageUrl" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->imageUrl);
				
			}
			
			
			
			
			if ("imageType" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->imageType);
				
			}
			
			
			
			
			if ("imageSize" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->imageSize);
				
			}
			
			
			
			
			if ("imageIndex" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->imageIndex); 
				
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
		
		if($this->itemId !== null) {
			
			$xfer += $output->writeFieldBegin('itemId');
			$xfer += $output->writeI64($this->itemId);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->description !== null) {
			
			$xfer += $output->writeFieldBegin('description');
			$xfer += $output->writeString($this->description);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->imageUrl !== null) {
			
			$xfer += $output->writeFieldBegin('imageUrl');
			$xfer += $output->writeString($this->imageUrl);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->imageType !== null) {
			
			$xfer += $output->writeFieldBegin('imageType');
			$xfer += $output->writeString($this->imageType);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->imageSize !== null) {
			
			$xfer += $output->writeFieldBegin('imageSize');
			$xfer += $output->writeString($this->imageSize);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->imageIndex !== null) {
			
			$xfer += $output->writeFieldBegin('imageIndex');
			$xfer += $output->writeI32($this->imageIndex);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->operationModes !== null) {
			
			$xfer += $output->writeFieldBegin('operationModes');
			
			$em = new \com\vip\product\gpdc\service\OperationModes; 
			$output->writeString($em::$__names[$this->operationModes]);  
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>