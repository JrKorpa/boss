<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoHView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-21 21:31:17

单据价格编辑是都取明细表中的(原始采购成本取值没有影响，其他都可能会影响），不要查库存中的价格 会导致保存的数据不一致的！！！！！！！！！！！！！！！！！！！！！！！！！！！！！

 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoHView extends View
{
	protected $_id;
	protected $_bill_id;


	public function get_id(){return $this->_id;}
	public function get_bill_id(){return $this->_bill_id;}
	
	
	//调拨单表格属性 label取wahoue_goods表的字段
	//1、采购价就是货品的成本价
	//2、批发价等于销售单的批发价
	//3、实价默认和批发价相等 可修改
	//4、差异价 = 批发价--实价
	public $js_table = [
	'id'=>'#from_table_data_h',
	'title'=>['货号','款号','主成色重','主石重','证书号','采购价','实价','批发价','差异价','手寸','长度','主成色','主石','主石粒数','副石','副石粒数','副石重','总重','净度','颜色','名称'],
	'lable' =>['goods_id','goods_sn','jinzhong','zuanshidaxiao','zhengshuhao','yuanshichengbenjia','chengbenjia','chengbenjia','0','shoucun','changdu','caizhi','zhushi','zhushilishu','fushi','fushilishu','fushizhong','zongzhong','jingdu','yanse','goods_name'],
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
		
		    ]
		   ];

    public $js_table_shijia = [
    'id'=>'#from_table_data_h',
    'title'=>['货号','款号','主成色重','主石重','证书号','采购价','实价','批发价','差异价','手寸','长度','主成色','主石','主石粒数','副石','副石粒数','副石重','总重','净度','颜色','名称'],
    'lable' =>['goods_id','goods_sn','jinzhong','zuanshidaxiao','zhengshuhao','yuanshichengbenjia','chengbenjia','chengbenjia','0','shoucun','changdu','caizhi','zhushi','zhushilishu','fushi','fushilishu','fushizhong','zongzhong','jingdu','yanse','goods_name'],
    'columns'=>[
            ['type'=>'text','readOnly'=>false],
            ['type'=>'text','readOnly'=>'1'],
            ['type'=>'text','readOnly'=>'1'],
            ['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
            ['type'=>'text','readOnly'=>'1'],
            ['type'=>'text','readOnly'=>'1'],
            ['type'=>'text','readOnly'=>false],
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
        
            ]
           ];
	//采购价=原始采购价,实价=名义成本价,批发价=名义成本价 boss_1413
	public $js_table_ha = [
	    'id'=>'#from_table_data_ha',
	    'title'=>['货号','款号','主成色重','主石重','证书号','采购价','实价','批发价','差异价','手寸','长度','主成色','主石','主石粒数','副石','副石粒数','副石重','总重','净度','颜色','名称'],
	    'lable' =>['goods_id','goods_sn','jinzhong','zuanshidaxiao','zhengshuhao','yuanshichengbenjia','mingyichengben','mingyichengben','0','shoucun','changdu','caizhi','zhushi','zhushilishu','fushi','fushilishu','fushizhong','zongzhong','jingdu','yanse','goods_name'],
	    'columns'=>[
	        ['type'=>'text','readOnly'=>false],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>false],
			['type'=>'text','readOnly'=>false],
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
	
				]
				];
	public $js_table_edit = [
	'lable' =>['goods_id','goods_sn','jinzhong','zuanshidaxiao','zhengshuhao','sale_price','shijia','pifajia','chajia','shoucun','changdu','caizhi','zhushi','zhushilishu','fushi','fushilishu','fushizhong','zongzhong','jingdu','yanse','goods_name']
		   ];
		//查询表格数据
		public function getTableGoods($bill_id){
			$sql = "SELECT `b`.shijia,b.sale_price,b.pifajia,b.pifajia-b.shijia as 'chajia',w.`goods_id`,w.`shoucun`,w.`changdu`,w.`caizhi`,w.`zhushi`,w.`fushi`,w.`fushilishu`,w.`zongzhong`,w.`goods_name`,w.`goods_sn`,w.`put_in_type`,w.`jingdu`,w.`zhengshuhao`,w.`yanse`,w.`zuanshidaxiao`,w.`jinzhong`,w.`zhushilishu`,w.`fushizhong` FROM `warehouse_bill_goods` AS `b`,`warehouse_goods` AS `w` WHERE b.`goods_id` = w.`goods_id` AND b.`bill_id` = '".$bill_id."'";
			$res = $this->getModel()->db()->getAll($sql);
			foreach($res as $k => $v){
				//var_dump($v);exit;
				$tmp = array();
				foreach ($this->js_table_edit['lable'] as $lab) {
					$tmp[] = $v[$lab];
				}
				$data[] = $tmp;
			}
			//var_dump($data);exit;
			return $data;
		}
	

}
?>