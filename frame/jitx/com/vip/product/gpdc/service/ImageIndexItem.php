<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;

class ImageIndexItem {
	
	static $_TSPEC;
	public $index = null;
	public $imageUri = null;
	public $delFlag = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'index'
			),
			2 => array(
			'var' => 'imageUri'
			),
			3 => array(
			'var' => 'delFlag'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['index'])){
				
				$this->index = $vals['index'];
			}
			
			
			if (isset($vals['imageUri'])){
				
				$this->imageUri = $vals['imageUri'];
			}
			
			
			if (isset($vals['delFlag'])){
				
				$this->delFlag = $vals['delFlag'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'ImageIndexItem';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("index" == $schemeField){
				
				$needSkip = false;
				$input->readI32($this->index); 
				
			}
			
			
			
			
			if ("imageUri" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->imageUri);
				
			}
			
			
			
			
			if ("delFlag" == $schemeField){
				
				$needSkip = false;
				$input->readBool($this->delFlag);
				
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
		
		if($this->index !== null) {
			
			$xfer += $output->writeFieldBegin('index');
			$xfer += $output->writeI32($this->index);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->imageUri !== null) {
			
			$xfer += $output->writeFieldBegin('imageUri');
			$xfer += $output->writeString($this->imageUri);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->delFlag !== null) {
			
			$xfer += $output->writeFieldBegin('delFlag');
			$xfer += $output->writeBool($this->delFlag);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>