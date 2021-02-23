<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoMView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 17:24:09
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoMView extends View
{
	//调拨单表格属性 
	public $js_table = [
		'id'=>'#from_table_data_bill_m',
		'title'=>['货号','款号','成本价','主成色重','主成色','主石粒数','副石粒数','副石重','颜色','净度','金耗','证书号','证书类型','货品名称'
		,'加价成本', '加价率(%)', '新款式分类' , '展厅标签价'],

		// jiajia_chengben 是数据库中没有的字段，需要计算合成的，只为实现表格的时候做个标记
		// lable 是界面表格需要显示的字段：（并决定先后顺序）
		'lable' =>['goods_id','goods_sn','yuanshichengbenjia','jinzhong','caizhi','zhushilishu','fushilishu','fushizhong','yanse','jingdu','jinhao','zhengshuhao','zhengshuleibie','goods_name', 'jiajia_chengben', 'jiajialv', 'cat_type1', 'biaoqianjia'],
		// warehouse_bill_goods fields
		'field_bill_goods' => ['jiajialv'],
		// warehouse_goods fields
		'field_goods' => ['goods_id','goods_sn','jinzhong','caizhi','yanse','jingdu','jinhao','zhengshuhao','zhengshuleibie', 'goods_name', 'yuanshichengbenjia', 'zhushilishu', 'fushilishu', 'fushizhong', 'cat_type1', 'biaoqianjia'],

		'map_field' => [
			'goods_sn'=>2,
			'yuanshichengbenjia'=>11,
			'jinzhong'=>3,
			'caizhi'=>4,
			'zhushilishu'=>12,
			'fushilishu'=>13,
			'fushizhong'=>14,
			'yanse'=>5,
			'jingdu'=>6,
			'jinhao'=>7,
			'zhengshuhao'=>8,
            'zhengshuleibie'=>9,
			'goods_name'=>10, 
			'jiajia_chengben'=>-1,  // special
			'jiajialv'=>0, 
			'cat_type'=>15,
            'biaoqianjia'=>16
		],
		
		'columns'=>[
			['type'=>'numeric','readOnly'=>false],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
			['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
			['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
			['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
            ['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
			['type'=>'text','readOnly'=>'1'],
            ['type'=>'numeric','format'=>'0,0.00','readOnly'=>'1'],
		]
	];

	//查询表格数据
	public function getTableGoods($bill_id)
	{
		$b_sel = "`b`.`".implode('`,`b`.`', $this->js_table['field_bill_goods'])."`";
		$w_sel = "`w`.`".implode('`,`w`.`', $this->js_table['field_goods'])."`";
		
		$sql = "SELECT ".$b_sel.",".$w_sel." ,jingxiaoshangchengbenjia FROM `warehouse_bill_goods` AS `b`,`warehouse_goods` AS `w` WHERE b.`goods_id` = w.`goods_id` AND b.`bill_id` = '".$bill_id."'";
		// print_r($sql);exit;
		$res = $this->getModel()->db()->getAll($sql);
		foreach($res as $k => $v)
		{
			$tmp = array();
			foreach ($this->js_table['lable'] as $lab) 
			{								
				// 加价成本：
				if ($lab == 'jiajia_chengben') {
					$tmp[] = !empty($v['jingxiaoshangchengbenjia']) ? $v['jingxiaoshangchengbenjia'] * ( 1 + (float)$v['jiajialv'] /100 ) : 0;
				} else {
				    if($lab == 'yuanshichengbenjia'){
						if(SYS_SCOPE=='zhanting' && $_SESSION['companyId']<>58)
							$tmp[]=!empty($v['jingxiaoshangchengbenjia']) ? $v['jingxiaoshangchengbenjia'] : 0;
	                    else
	                    	$tmp[] = $v[$lab];
	                }else    				
					    $tmp[] = $v[$lab];
					  
				} 
			}
			$data[] = $tmp;
		}
		return $data;
	}


}
?>