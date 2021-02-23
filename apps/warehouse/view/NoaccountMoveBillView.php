<?php
/**
 *  -------------------------------------------------
 *   @file		: NoaccountMoveBillView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-04 10:17:22
 *   @update	:
 *  -------------------------------------------------
 */
class NoaccountMoveBillView extends View
{
	protected $_id;
	protected $_bill_id;
	protected $_ship_number;


	public function get_id(){return $this->_id;}
	public function get_bill_id(){return $this->_bill_id;}
	public function get_ship_number(){return $this->_ship_number;}
	
	//调拨单表格属性 label取wahoue_goods表的字段
	public $js_table = [
	'id'=>'#from_table_data_bill_wf_return',//对应xxxhtml id
	'title'=>['无帐修退流水号','业务类型','货号','款号','主成色','订单号','指圈','证书号','主石重','主石粒数','零售价','金托类型'],
	'lable' =>['virtual_id','business_type','goods_id','style_sn','ingredient_color','order_sn','finger_circle','credential_num','main_stone_weight','main_stone_num','resale_price','torr_type'],
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
			]
	];
	
	//查询表格数据
	public function getTableGoods($bill_id){
		//echo 1111;exit;
		//$bill_lab = ['goods_id','goods_name','goods_sn','in_warehouse_type','jingdu','zhengshuhao','yanse','jinzhong','chengbenjia','xiaoshoujia','jinzhong','zhushilishu','fushizhong'];
		//$w_lable = array_diff($this->js_table['lable'],$bill_lab);
		//$w_sel = "`w`.`".implode('`,`w`.`',$w_lable)."`";
		//$b_sel = "`b`.`".implode('`,`b`.`',$bill_lab)."`";
		//$sql = "SELECT ".$b_sel.",".$w_sel." FROM `warehouse_bill_goods` AS `b`,`warehouse_goods` AS `w` WHERE b.`goods_id` = w.`goods_id` AND b.`bill_id` = '".$bill_id."'";
		//echo $sql;exit;
		$sql = "SELECT w.`goods_id`,w.`goods_name`,w.`goods_sn`,w.`put_in_type`,w.`jingdu`,w.`zhengshuhao`,w.`yanse`,w.`zuanshidaxiao`,w.`chengbenjia`,w.`xianzaixiaoshou`,w.`jinzhong`,w.`zhushilishu`,w.`fushizhong`,w.`fushilishu` FROM `warehouse_bill_goods` AS `b`,`warehouse_goods` AS `w` WHERE b.`goods_id` = w.`goods_id` AND b.`bill_id` = '".$bill_id."'";
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
		//var_dump($data);exit;
		return $data;
	}

}
?>