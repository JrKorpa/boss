<?php
/**
 *  -------------------------------------------------
 *   @file		: MaterialInventoryModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2018-01-18 14:01:12
 *   @update	:
 *  -------------------------------------------------
 */
class MaterialInventoryJinModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'material_inventory_jin';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"goods_sn"=>"货品编号",
"supplier_id"=>"供应商ID",
"warehouse_id"=>"库存仓库ID",
"batch_sn"=>"批次号",
"inventory_qty"=>"库存数量",
"cost_price"=>"成本单价");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url MaterialInventoryController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true,$dow_info=null)
	{	    
		//不要用*,修改为具体字段
		$mg_fields= "mg.style_sn,mg.style_name,mg.goods_name,mg.goods_spec,mg.catetory1,mg.catetory2,mg.catetory3,kr.name as supplier_name,wa.name as warehouse_name,mg.unit,mg.cost as goods_cost,mg.goods_sale_price,mg.goods_jiajialv ";
		if(empty($where['group_by'])){
			$sql = "SELECT inv.*,{$mg_fields} FROM `".$this->table()."` as inv inner join material_goods_jin mg on inv.goods_sn=mg.goods_sn left join warehouse as wa on wa.id=inv.warehouse_id left join kela_supplier.app_processor_info as kr on kr.id = inv.supplier_id ";
	        $sqltj="select sum(inv.inventory_qty) as allnum,sum(round(inv.cost*inv.inventory_qty,2)) as allcost FROM `".$this->table()."` as inv inner join material_goods_jin mg on inv.goods_sn=mg.goods_sn left join warehouse as wa on wa.id=inv.warehouse_id left join kela_supplier.app_processor_info as kr on kr.id = inv.supplier_id ";
		}else{
            $sql = "SELECT inv.goods_sn,inv.warehouse_id,inv.cost,sum(inv.inventory_qty) as inventory_qty,wa.name as warehouse_name,mg.style_sn,mg.style_name,mg.goods_name,mg.goods_spec,mg.catetory1,mg.catetory2,mg.catetory3,mg.unit,mg.cost as goods_cost  FROM `".$this->table()."` as inv inner join material_goods_jin mg on inv.goods_sn=mg.goods_sn left join warehouse as wa on wa.id=inv.warehouse_id left join kela_supplier.app_processor_info as kr on kr.id = inv.supplier_id ";
	        $sqltj="select sum(inv.inventory_qty) as allnum,sum(round(inv.cost*inv.inventory_qty,2)) as allcost FROM `".$this->table()."` as inv inner join material_goods_jin mg on inv.goods_sn=mg.goods_sn left join warehouse as wa on wa.id=inv.warehouse_id left join kela_supplier.app_processor_info as kr on kr.id = inv.supplier_id ";

	    }$str = '';

		if(!empty($where['supplier_id'])){
            $str .= "inv.`supplier_id` = '{$where['supplier_id']}' AND ";
        }
        if(!empty($where['warehouse_id'])){
            $str .= "inv.`warehouse_id` = '{$where['warehouse_id']}' AND ";
        }
        if(isset($where['number_index']) && $where['number_index']=='2'){
            $str .= "inv.`inventory_qty` >= 0 AND ";
        }else
            $str .= "inv.`inventory_qty` > 0 AND ";

        if(!empty($where['cost'])){
            $str .= "inv.`cost` = '{$where['cost']}' AND ";
        }
        if(!empty($where['style_sn'])){
            $str .= "mg.`style_sn` = '{$where['style_sn']}' AND ";
        }
        if(!empty($where['style_name'])){
            $str .= "mg.`style_name` like '%{$where['style_name']}%' AND ";
        }
        if(!empty($where['goods_spec'])){
            $str .= "mg.`goods_spec` = '{$where['goods_spec']}' AND ";
        }
        if(!empty($where['goods_name'])){
            $str .= "mg.`goods_name` like '%{$where['goods_name']}%' AND ";
        }
        if(!empty($where['catetory1'])){
            $str .= "mg.`catetory1` = '{$where['catetory1']}' AND ";
        }
        if(!empty($where['catetory2'])){
            $str .= "mg.`catetory2` = '{$where['catetory2']}' AND ";
        }
        if(!empty($where['catetory3'])){
            $str .= "mg.`catetory3` = '{$where['catetory3']}' AND ";
        }
	    if(!empty($where['goods_sn'])){   
		    if(is_array($where['goods_sn'])){
		        $where['goods_sn'] = "'".implode("','",$where['goods_sn'])."'";
		        $str .= "inv.`goods_sn` in ({$where['goods_sn']}) AND ";
		    }else{
			    $str .= "inv.`goods_sn` = '{$where['goods_sn']}' AND ";
		    }
		}

		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
			$sqltj .=" WHERE ".$str;
		}

		if(!empty($where['group_by'])){
            $sql .=" group by inv.goods_sn,inv.warehouse_id,inv.cost order by inv.warehouse_id,inv.goods_sn";

		}else{ 
			if(!empty($where['order_by_field']))
	            $sql .= " ORDER BY {$where['order_by_field']} ";
	        else  
			    $sql .= " ORDER BY inv.`id` DESC";
        } 
		if(isset($dow_info) && $dow_info == 'dow_info'){
			$data = $this->db()->getPageList($sql,array(),1, 8000,$useCache);
			$this->download($data['data']);
			exit();
		}		
		$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
		$data['tongji']=$this->db()->getRow($sqltj);
		
		return $data;
	}
	/**
	 * 库存导出
	 */
	function  download($data){
        if(!empty($data)) {
            $xls_ontent = "款式编号,款式名称,货品编号,货品名称,供应商,仓库,数量,成本价,金额,单位,分类1,分类2,分类3,批次,\r\n";	
            foreach($data as $key => $value){ 
                $xls_ontent .= $value['style_sn'].",";
                $xls_ontent .= $value['style_name'].",";
                $xls_ontent .= $value['goods_sn'].",";
                $xls_ontent .= $value['goods_name'].",";
                $xls_ontent .= !empty($value['supplier_name']) ? $value['supplier_name']."," : ",";
                $xls_ontent .= $value['warehouse_name'].",";
                $xls_ontent .= $value['inventory_qty'].",";
                $xls_ontent .= $value['cost'].",";
                $xls_ontent .= round($value['inventory_qty']*$value['cost'],2).",";
                $xls_ontent .= $value['unit'].",";
                $xls_ontent .= $value['catetory1'].",";
                $xls_ontent .= $value['catetory2'].",";
                $xls_ontent .= $value['catetory3'].",";
                $xls_ontent .= !empty($value['batch_sn']) ? $value['batch_sn'].",\r\n" : ",\r\n";
            }
        } else {
            $xls_ontent = "没有数据\r\n";	
        }
        header("Content-type: text/html; charset=gbk");
		header("Content-type:aplication/vnd.ms-excel");
		header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "物控库存" . date("Y-m-d")) . ".csv");
		echo iconv("utf-8", "GB18030", $xls_ontent);	
		exit();
	}
	
	/**
	 * 查询库存记录
	 * @param unknown $where
	 * @param string $fields
	 */
	function getInventoryInfo($fields="*",$where){
	    $sql = "select {$fields} from ".$this->table()." where {$where}";
	    return $this->db()->getRow($sql);
	}
	/**
	 * 计算商品加价价格
	 * @param unknown $data
	 * @return unknown
	 */
	function calcGoodsJiajiaPriceList($data,$company_type=3){
		//print_r($data);
		//echo $company_type;
	    if(!empty($data) && is_array($data)){
	        foreach ($data as &$vo){
	        	if($company_type=='1' || $company_type=='2'){
		            $jiajialv = 1;
		            $cost = $vo['cost'];
		            $vo['sale_price'] = $cost*$jiajialv;
		            $vo['jiajialv']=$jiajialv;
	        	}else{
	        		if($vo['goods_sale_price']>0){
			            $vo['sale_price'] = $vo['goods_sale_price'];
			            $vo['jiajialv']=0;
			        }elseif($vo['goods_jiajialv']>0){
			            $jiajialv = $vo['goods_jiajialv'];
			            $cost = $vo['goods_cost'];
			            $vo['sale_price'] = $cost*$jiajialv;
			            $vo['jiajialv']=$jiajialv;                        
			        }else{    	        			
			            $sql = "select jiajialv from front.base_style_info where style_sn='{$vo['style_sn']}'";
			            //echo $sql;
			            $jiajialv = $this->db()->getOne($sql);
			            $jiajialv = $jiajialv>0?$jiajialv:1;
			            $cost = $vo['goods_cost'];
			            $vo['sale_price'] = $cost*$jiajialv;
			            $vo['jiajialv']=$jiajialv;
		            }
		        }    
	        }
	    }
	    //print_r($data);
	    return $data;
	}
}

?>