<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoBView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-18 17:09:51
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoBView extends View
{
	protected $_id;
	protected $_bill_id;
	protected $_kela_order_sn;
	protected $_pid;
	protected $_in_warehouse_type;


	public function get_id(){return $this->_id;}
	public function get_bill_id(){return $this->_bill_id;}
	public function get_kela_order_sn(){return $this->_kela_order_sn;}
	public function get_pid(){return $this->_pid;}
	public function get_in_warehouse_type(){return $this->_in_warehouse_type;}

	/** 根据 bill_id 获取in_warehouse_type (入库方式)**/
	public function getWarehouseTypeForBill_id($bill_id){
		 $model = new WarehouseBillInfoBModel(21);
		 $data = $model->getRowForBill_id($bill_id);
		 return $data['in_warehouse_type'];
	}
	/** 根据 bill_id 获取pid (加工商)**/
	public function getPidForBill_id($bill_id){
		 $model = new WarehouseBillInfoBModel(21);
		 $data = $model->getRowForBill_id($bill_id);
		 return $data['pid'];
	}
	public function  getPidForBill_Name($bill_id){
		$model = new WarehouseBillInfoBModel(21);
		$data = $model->getRowForBill_id($bill_id);
		return $data['prc_name'];
	}

	/** 根据 bill_id 获取kela_order_sn (BDD订单号/参考编号)**/
	public function getKelaOrderSnForBill_id($bill_id){
		 $model = new WarehouseBillInfoBModel(21);
		 $data = $model->getRowForBill_id($bill_id);
		 return $data['kela_order_sn'];
	}
	
//调拨单表格属性 label取wahoue_goods表的字段
	public $js_table = [
	'id'=>'#from_table_data_bill_b',
	'title'=>['货号','货品名称','款号','入库方式','主成色','主石颗粒','主石重','副石颗粒','副石重','颜色','净度','证书号','数量','是否结价','成本价','退货价','原始采购价'],
	'lable' =>['goods_id','goods_name','goods_sn','put_in_type','zhushiyanse','zhushilishu','zuanshidaxiao','fushilishu','fushizhong','yanse','jingdu','zhengshuhao','num','jiejia','yuanshichengbenjia','yuanshichengbenjia','yuanshichengbenjia'],
	'columns'=>[
			['type'=>'text','readOnly'=>false],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],						
			['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],			
			['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],

			]
	];
	
	//查询表格数据
	public function getTableGoods($bill_id){
		//$bill_lab = ['goods_id','goods_name','goods_sn','in_warehouse_type','jingdu','zhengshuhao','yanse','jinzhong','chengbenjia','xiaoshoujia','jinzhong','zhushilishu','fushizhong'];
		//$w_lable = array_diff($this->js_table['lable'],$bill_lab);
		//$w_sel = "`w`.`".implode('`,`w`.`',$w_lable)."`";
		//$b_sel = "`b`.`".implode('`,`b`.`',$bill_lab)."`";
		//$sql = "SELECT ".$b_sel.",".$w_sel." FROM `warehouse_bill_goods` AS `b`,`warehouse_goods` AS `w` WHERE b.`goods_id` = w.`goods_id` AND b.`bill_id` = '".$bill_id."'";
		//echo $sql;exit;
		$sql = "SELECT w.`goods_id`,w.`goods_name`,w.`goods_sn`,w.`put_in_type`,w.`jingdu`,w.`zhengshuhao`,w.`yanse`,w.`zuanshidaxiao`,w.`yuanshichengbenjia`,w.`mingyichengben`,w.`fushilishu`,w.`num`,w.`jiejia`,w.`xianzaixiaoshou`,w.`zhushiyanse`,w.`jinzhong`,w.`zhushilishu`,w.`fushizhong` FROM `warehouse_bill_goods` AS `b`,`warehouse_goods` AS `w` WHERE b.`goods_id` = w.`goods_id` AND b.`bill_id` = '".$bill_id."'";
		//echo $sql;exit;
		$res = $this->getModel()->db()->getAll($sql);
		foreach($res as $k => $v){
			//var_dump($v);exit;
			$tmp = array();
			foreach ($this->js_table['lable'] as $lab) {
				$tmp[] = $v[$lab];
			}
			$data[] = $tmp;
		}
		
		return $data;
	}


}
?>