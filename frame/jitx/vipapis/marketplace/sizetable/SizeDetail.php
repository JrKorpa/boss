<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace vipapis\marketplace\sizetable;

class SizeDetail {
	
	static $_TSPEC;
	public $size_table_id = null;
	public $size_detail_id = null;
	public $size_detail_name = null;
	public $size_detail_properties = null;
	public $del_flag = null;
	
	public function __construct($vals=null){
		
		if (!isset(self::$_TSPEC)){
			
			self::$_TSPEC = array(
			1 => array(
			'var' => 'size_table_id'
			),
			2 => array(
			'var' => 'size_detail_id'
			),
			3 => array(
			'var' => 'size_detail_name'
			),
			4 => array(
			'var' => 'size_detail_properties'
			),
			5 => array(
			'var' => 'del_flag'
			),
			
			);
			
		}
		
		if (is_array($vals)){
			
			
			if (isset($vals['size_table_id'])){
				
				$this->size_table_id = $vals['size_table_id'];
			}
			
			
			if (isset($vals['size_detail_id'])){
				
				$this->size_detail_id = $vals['size_detail_id'];
			}
			
			
			if (isset($vals['size_detail_name'])){
				
				$this->size_detail_name = $vals['size_detail_name'];
			}
			
			
			if (isset($vals['size_detail_properties'])){
				
				$this->size_detail_properties = $vals['size_detail_properties'];
			}
			
			
			if (isset($vals['del_flag'])){
				
				$this->del_flag = $vals['del_flag'];
			}
			
			
		}
		
	}
	
	
	public function getName(){
		
		return 'SizeDetail';
	}
	
	public function read($input){
		
		$input->readStructBegin();
		while(true){
			
			$schemeField = $input->readFieldBegin();
			if ($schemeField == null) break;
			$needSkip = true;
			
			
			if ("size_table_id" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->size_table_id); 
				
			}
			
			
			
			
			if ("size_detail_id" == $schemeField){
				
				$needSkip = false;
				$input->readI64($this->size_detail_id); 
				
			}
			
			
			
			
			if ("size_detail_name" == $schemeField){
				
				$needSkip = false;
				$input->readString($this->size_detail_name);
				
			}
			
			
			
			
			if ("size_detail_properties" == $schemeField){
				
				$needSkip = false;
				
				$this->size_detail_properties = array();
				$input->readMapBegin();
				while(true){
					
					try{
						
						$key1 = '';
						$input->readString($key1);
						
						$val1 = '';
						$input->readString($val1);
						
						$this->size_detail_properties[$key1] = $val1;
					}
					catch(\Exception $e){
						
						break;
					}
				}
				
				$input->readMapEnd();
				
			}
			
			
			
			
			if ("del_flag" == $schemeField){
				
				$needSkip = false;
				$input->readI16($this->del_flag); 
				
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
		
		$xfer += $output->writeFieldBegin('size_table_id');
		$xfer += $output->writeI64($this->size_table_id);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('size_detail_id');
		$xfer += $output->writeI64($this->size_detail_id);
		
		$xfer += $output->writeFieldEnd();
		
		$xfer += $output->writeFieldBegin('size_detail_name');
		$xfer += $output->writeString($this->size_detail_name);
		
		$xfer += $output->writeFieldEnd();
		
		if($this->size_detail_properties !== null) {
			
			$xfer += $output->writeFieldBegin('size_detail_properties');
			
			if (!is_array($this->size_detail_properties)){
				
				throw new \Osp\Exception\OspException('Bad type in structure.', \Osp\Exception\OspException::INVALID_DATA);
			}
			
			$output->writeMapBegin();
			foreach ($this->size_detail_properties as $kiter0 => $viter0){
				
				$xfer += $output->writeString($kiter0);
				
				$xfer += $output->writeString($viter0);
				
			}
			
			$output->writeMapEnd();
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		if($this->del_flag !== null) {
			
			$xfer += $output->writeFieldBegin('del_flag');
			$xfer += $output->writeI16($this->del_flag);
			
			$xfer += $output->writeFieldEnd();
		}
		
		
		$xfer += $output->writeFieldStop();
		$xfer += $output->writeStructEnd();
		return $xfer;
	}
	
}

?>