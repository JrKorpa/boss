<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoPView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-23 21:35:24
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoPView extends View
{
	protected $_id;
	protected $_bill_id;


	public function get_id(){return $this->_id;}
	public function get_bill_id(){return $this->_bill_id;}
	
	//调拨单表格属性 label取wahoue_goods表的字段
	public $js_table = [
	'id'=>'#from_table_data_bill_p',
	'title'=>['货号','款号','主成色重','主石重','证书号','采购价','实价','批发价','差异价','手寸','长度','主成色','主石','主石粒数','副石','副石粒数','副石重','总重','净度','颜色','名称','订单号','品号','项次','外部订单号','表面工艺','门店结算状态','结算操作时间','管理费','展厅标签价'],
	'lable' =>['a.goods_id','a.goods_sn','a.jinzhong','a.zuanshidaxiao','a.zhengshuhao','a.yuanshichengbenjia','a.mingyichengben','a.mingyichengben','(a.mingyichengben-a.mingyichengben) as chajia','a.shoucun','a.changdu','a.caizhi','a.zhushi','a.zhushilishu','a.fushi','a.fushilishu','a.fushizhong','a.zongzhong','a.jingdu','a.yanse','a.goods_name',"b.order_sn","b.pinhao","b.xiangci","b.p_sn_out",'b.p_sn_out',"b.dep_settlement_type","b.settlement_time","b.management_fee","a.biaoqianjia"],
	'columns'=>[
	['type'=>'text','readOnly'=>false],
	['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
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
			['type'=>'text','readOnly'=>'1'],
			['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
			['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
    	    ['type'=>'text','readOnly'=>'1'],
    	    ['type'=>'text','readOnly'=>'1'],
            ['type'=>'text','readOnly'=>'1'],
            ['type'=>'text','readOnly'=>'1'],
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
		$sql = "SELECT w.`goods_id`,w.`shoucun`,w.`changdu`,w.`caizhi`,w.`zhushi`,w.`fushi`,w.`fushilishu`,w.`zongzhong`,w.`goods_name`,w.`goods_sn`,w.`put_in_type`,w.`jingdu`,w.`zhengshuhao`,w.`yanse`,w.`zuanshidaxiao`,w.`chengbenjia`,w.`xianzaixiaoshou`,w.`jinzhong`,w.`zhushilishu`,w.`fushizhong`,`b`.`pinhao`,`b`.`xiangci`,`b`.`order_sn`,`b`.`dep_settlement_type`,`b`.`settlement_time`,`b`.`label_price` FROM `warehouse_bill_goods` AS `b`,`warehouse_goods` AS `w` WHERE b.`goods_id` = w.`goods_id` AND b.`bill_id` = '".$bill_id."'";
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