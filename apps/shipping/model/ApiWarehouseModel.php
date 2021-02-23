<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiWarehouseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: JUAN
 *   @date		: 2015年1月21日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiWarehouseModel
{
	public static function CheckBillByBillSn($bill_no, $bill_type){
		$keys=array('bill_no', 'bill_type');
        $vals=array($bill_no, $bill_type);
		return ApiModel::warehouse_api($keys,$vals,'CheckBillByBillSn');
	}

	/** 根据仓储单号，获取单号的明细信息 **/
	public static function getDetailByBillSn($bill_no, $bill_type){
		$keys=array('bill_no', 'bill_type');
		$vals=array($bill_no, $bill_type);
		return ApiModel::warehouse_api($keys,$vals,'getDetailByBillSn');
	}

	/** 根据仓库ID，获取所属公司 **/
	public static function getCompanyByWarehouse($warehouse_id){
		$keys = array('warehouse_id');
		$vals = array($warehouse_id);
		return ApiModel::warehouse_api($keys , $vals , 'getCompanyByWarehouse');
	}

	/** 获取仓库列表 **/
	public static function getWarehouseList($warehouse_id = 0){
		if($warehouse_id){
			$keys = array('id');
			$vals = array($warehouse_id);
		}else{
			$keys = array('is_delete');
			$vals = array(1);
		}

		return ApiModel::warehouse_api($keys , $vals ,'GetWarehouseList');
	}

	/** 绑定展厅发货，回写调拨单的快递单号 **/
	public static function SetShipNumber( $filter = array('ship_number'=>'', 'bill_id'=>'' , 'bill_no'=> '') , $type = ''){
		if($type == ''){
			return false;
		}
		if($type == 'add'){
			if( $filter['ship_number'] != '' && $filter['bill_id'] != ''){
				$keys=array('ship_number', 'bill_id' , 'type');
				$vals=array($filter['ship_number'], $filter['bill_id'] , $type);
			}else{
				return false;
			}
		}
		if($type == 'del'){
			 if( $filter['bill_no'] != ''){
				$keys=array('bill_no', 'type');
				$vals=array($filter['bill_no'], $type);
			}else{
				return false;
			}
		}
		return ApiModel::warehouse_api($keys,$vals,'SetShipNumber');
	}

}?>