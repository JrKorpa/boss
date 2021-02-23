<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoLModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 17:15:30
 *   @update	:
 *  -------------------------------------------------
 */
class StoneModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'stone';
	    $this->_dataObject = array();
		parent::__construct($id,$strConn);
	}

	function saveStoneBill($bill){
		$sql="insert into stone_bill (id,bill_no,bill_type,status,processors_id,processors_name,factory_id,factory_name,source,source_no,paper_no,create_user,create_time,remark) values
		 (null,null,'{$bill['bill_type']}','1','{$bill['processors_id']}','{$bill['processors_name']}','{$bill['factory_id']}','{$bill['factory_name']}','{$bill['source']}','{$bill['source_no']}','{$bill['paper_no']}','{$bill['create_user']}',now(),'{$bill['remark']}')";
        $res=$this->db()->query($sql);
        if($res==false){
        	return false;
        } 
        return $this->db()->insertId();		
	}
	function saveStoneBill_detail($data){
		$sql="insert into stone_bill_details (id,bill_id,dia_package,purchase_price,specification,color,neatness,cut,symmetry,polishing,fluorescence,num,weight,price) values 
		(0,'{$data['bill_id']}','{$data['dia_package']}','{$data['purchase_price']}','{$data['specification']}','{$data['color']}','{$data['neatness']}','{$data['cut']}','{$data['symmetry']}','{$data['polishing']}','{$data['fluorescence']}','{$data['num']}','{$data['weight']}','{$data['price']}')";
	    $res=$this->db()->query($sql);	    
	    return $res;
	}
	function updateStoneBill($data){
		$sql="update stone_bill set bill_no='{$data['bill_no']}',num='{$data['num']}',weight='{$data['weight']}',price_total='{$data['price_total']}' where id='{$data['id']}'";
		$res=$this->db()->query($sql);
		return $res;
	}

    function getStoneBill($bill_no){
        $sql="select * from stone_bill where source_no='$bill_no'"; 
        return $this->db()->getAll($sql); 
    }


}

