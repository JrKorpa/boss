<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoCView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-18 21:54:49
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoCView extends View
{
	protected $_id;
	protected $_bill_id;
	protected $_pro_id;
	protected $_chuku_type;


	public function get_id(){return $this->_id;}
	public function get_bill_id(){return $this->_bill_id;}
	public function get_pro_id(){return $this->_pro_id;}
	public function get_chuku_type(){return $this->_chuku_type;}
	
	//调拨单表格属性 label取wahoue_goods表的字段
	public $js_table =array();  
	
    function __construct ($obj)
    {
        if(SYS_SCOPE=='boss'){
        	$this->js_table=
			 array(
			'id'=>'#from_table_data_c',
			'title'=>['货号','货品名称','款号','入库方式','净度','证书号','证书类型','颜色','主石重','成本价','销售成本价','主成色重','主石粒数','副石重'],
			'lable' =>['goods_id','goods_name','goods_sn','put_in_type','jingdu','zhengshuhao','zhengshuleibie','yanse','zuanshidaxiao','yuanshichengbenjia','mingyichengben','jinzhong','zhushilishu','fushizhong'],
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
					['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
					['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
					['type'=>'text','readOnly'=>'1'],
					['type'=>'text','readOnly'=>'1'],
					['type'=>'text','readOnly'=>'1'],
					]
			);        	
        }
        if(SYS_SCOPE=='zhanting'){
        	$this->js_table=
			 array(
			'id'=>'#from_table_data_c',
			'title'=>['货号','货品名称','款号','入库方式','净度','证书号','证书类型','颜色','主石重','成本价','销售成本价','主成色重','主石粒数','副石重'],
			'lable' =>['goods_id','goods_name','goods_sn','put_in_type','jingdu','zhengshuhao','zhengshuleibie','yanse','zuanshidaxiao','yuanshichengbenjia','mingyichengben','jinzhong','zhushilishu','fushizhong'],
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
					['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
					['type'=>'text','readOnly'=>'1'],
					['type'=>'text','readOnly'=>'1'],
					['type'=>'text','readOnly'=>'1'],
					]
			);        	
        }
        parent::__construct($obj);
    }	 
	//查询表格数据
	public function getTableGoods($bill_id){
		//$bill_lab = ['goods_id','goods_name','goods_sn','in_warehouse_type','jingdu','zhengshuhao','yanse','jinzhong','chengbenjia','xiaoshoujia','jinzhong','zhushilishu','fushizhong'];
		//$w_lable = array_diff($this->js_table['lable'],$bill_lab);
		//$w_sel = "`w`.`".implode('`,`w`.`',$w_lable)."`";
		//$b_sel = "`b`.`".implode('`,`b`.`',$bill_lab)."`";
		//$sql = "SELECT ".$b_sel.",".$w_sel." FROM `warehouse_bill_goods` AS `b`,`warehouse_goods` AS `w` WHERE b.`goods_id` = w.`goods_id` AND b.`bill_id` = '".$bill_id."'";
		//echo $sql;exit;
		$sql = "SELECT w.`goods_id`,w.`goods_name`,w.`goods_sn`,w.`put_in_type`,w.`jingdu`,w.`zhengshuhao`,w.`zhengshuleibie`,w.`yanse`,w.`zuanshidaxiao`,w.`yuanshichengbenjia`,w.`mingyichengben`,w.`jinzhong`,w.`zhushilishu`,w.`fushizhong` FROM `warehouse_bill_goods` AS `b`,`warehouse_goods` AS `w` WHERE b.`goods_id` = w.`goods_id` AND b.`bill_id` = '".$bill_id."'";
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