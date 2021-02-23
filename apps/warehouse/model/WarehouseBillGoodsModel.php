<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-31 10:58:10
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_bill_goods';
        $this->_dataObject = array(
				"id"=>" ",
				"bill_id"=>"单据id",
				"bill_no"=>"单据编号",
				"bill_type"=>"单据类型",
				"goods_id"=>"货号",
				"goods_sn"=>"款号",
				"goods_name"=>"商品名称",
				"num"=>"数量",
				"warehouse_id"=>"所属仓库ID(如果是盘点单的明细，则表示：盘点时,盘盈的货品需要记录)",
				"caizhi"=>"材质",
				"jinzhong"=>"金重",
				"jingdu"=>"净度",
				"jinhao"=>"金耗",
				"yanse"=>"颜色",
				"zhengshuhao"=>"证书号",
				"zuanshidaxiao"=>"钻石大小",
				"in_warehouse_type"=>"入库方式 0、默认无。1.购买。2、委托加工。3、代销。4、借入",
				"account"=>"是否结价0、默认无。1、未结价。2、已结价",
				"addtime"=>"添加时间",
				"pandian_status"=>"盘点状态 参考数字字典",
				"guiwei"=>"货品所在柜位号",
				"detail_id"=>"销售单和退后单存订单的detail_id所用",
				"pandian_guiwei"=>"盘点柜位",
				"pandian_user"=>"盘点人",
				"sale_price"=>" ",
				"shijia"=>"实际价格",
				"yuanshichengben"=>"原始采购成本");
		parent::__construct($id,$strConn);
	}


	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{

		$sql = "SELECT bg.*,bg.shijia-bg.sale_price as chajia,bg.pifajia,(bg.shijia-bg.pifajia)  as pf_chajia,g.yuanshichengbenjia,g.zhushilishu,g.fushilishu,g.fushizhong,g.changdu,g.zhushi,g.fushi,g.zhushiyanse,g.zhushijingdu,g.shoucun,g.mairugongfei,g.zongzhong,g.put_in_type,g.jiejia,g.zuanshidaxiao,g.xianzaixiaoshou,g.mingyichengben,g.zhushimairuchengben,g.chengbenjia,g.zuixinlingshoujia, g.cat_type, g.cat_type1,g.buchan_sn,g.zhengshuleibie,(
        CASE `bg`.`dep_settlement_type`
        WHEN 1 THEN
            '未结算'
        WHEN 2 THEN
            '已结算'
        WHEN 3 THEN
            '已退货'
        ELSE
            ''
        END
    ) dep_settlement_type,bg.settlement_time
			 FROM `warehouse_bill_goods` as bg LEFT JOIN warehouse_goods as g ON bg.goods_id = g.goods_id ";

		$str = '';
		if(isset($where['bill_id']) && $where['bill_id'] != "")
		{
			$str .= "`bill_id` = ".$where['bill_id'].' AND ';
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY bg.id asc";
		//echo $sql;exit;
		$sql1 = "SELECT count(*) FROM `warehouse_bill_goods` as bg LEFT JOIN warehouse_goods as g ON bg.goods_id = g.goods_id ";
		$sql1=$sql1." WHERE ".$str;
		$count=$this->db()->getOne($sql1);
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		$data['recordCount']=$count;
		return $data;
	}

	/**
	* 普通查询
	* @param $is_all INT 1/2/3
	*/
	public function select2($fields, $where , $is_all = 1){
		$sql = "SELECT {$fields} FROM `warehouse_bill_goods` WHERE $where ORDER BY `id` DESC";
		if($is_all == 1){
			return $this->db()->getOne($sql);
		}else if($is_all == 2){
			return $this->db()->getRow($sql);
		}else if($is_all == 3){
			return $this->db()->getAll($sql);
		}
	}

	public function getIniLabel($arr,$all = true){
		if(!isset($arr['label'])){
			return false;
		}else{
			$arr = $arr['label'];
			$sql = array();
			$sql['select'] = '';
			if($all === false ){
				$sql['where'] = array_shift($arr);
			}
			foreach ($arr as $v) {
				$sql['select'] .= "`".$v."`,";
			}
			$sql['select'] = substr($sql['select'],0,-1);
			return $sql;
		}
	}

	//根据单据ID获取单据商品信息tab专用
	public function getGoodsInfoByBillID($arr,$bill_id,$bill_type){
		$sql = $this->getIniLabel($arr);
		$sql = "SELECT ".$sql['select']." FROM ".$this->table();
		$sql .= " WHERE `bill_id` = ".$bill_id." AND `bill_type` = ".$bill_type;
		return $this->db()->getAll($sql);
	}

	//根据商品ID获取商品信息table插件（仓储单据明细货号带出货品信息）专用
	public function getGoodsInfoByGoodsID($filelds,$goods_id){
		$sql = "SELECT ".implode(',',$filelds)." FROM `warehouse_goods`";
		$sql .= " WHERE `goods_id` = '".$goods_id."'";
		return $this->db()->getRow($sql);
	}

	/**************
	function:get_bill_data
	decription:根据单据id获取数据明细
	***************/
	public function get_bill_data($bill_id)
	{
		$sql = "SELECT `wg`.`id`, `g`.`goods_id` , `wg`.`in_warehouse_type` , `wg`.`account` ,`wg`.sale_price, `wg`.`shijia` ,`g`.`goods_name` ,`g`.`goods_sn` ,`g`.`chengbenjia` ,`g`.`zuixinlingshoujia`, `g`.`jinzhong` , `g`.`zhushi`,`g`.`zhushilishu` , `g`.`zuanshidaxiao` , `g`.`mairugongfei`,`g`.`shoucun`,`g`.`fushi`,`g`.`fushilishu` ,`g`.`fushizhong` , `g`.`zhushiyanse`, `g`.`yanse` ,`g`.`zhushijingdu`, `g`.`jingdu` ,`g`.`changdu`,`g`.`caizhi`,`g`.`zhushiyanse` , `g`.`zhushijingdu` ,`g`.`zongzhong`,  `g`.`zhengshuhao`,`g`.`company_id`,`g`.`warehouse_id`,`g`.`warehouse` FROM `warehouse_bill_goods` AS `wg`  LEFT JOIN  `warehouse_goods` AS `g`  ON `wg`.`goods_id`=`g`.`goods_id`  WHERE `bill_id` = $bill_id ORDER BY `g`.`id` ASC" ;
		return $this->db()->getAll($sql);
	}
	
	//根据单据号获取单据商品的总名义成本价总实价 2015-12-25 zzm boss-1015
	public function getBillPrice($bill_no){
		$sql = "SELECT IF(SUM(a.mingyichengben), SUM(a.mingyichengben),0) mingyichengben,IF(SUM(b.shijia), SUM(b.shijia),0) shijia FROM warehouse_goods a JOIN warehouse_bill_goods b ON a.goods_id = b.goods_id WHERE b.bill_no = '".$bill_no."'";
		return $this->db()->getRow($sql);
	}

    //获取最后一个已审核的批发销售单的出库类型获取
    public function getOutWarehouseTypeByGoodsId($goods_id)
    {
        $sql = "select wb.out_warehouse_type from warehouse_bill wb inner join warehouse_bill_goods bg on bg.bill_id = wb.id where bg.goods_id = '".$goods_id."' and wb.bill_type = 'P' and wb.bill_status in(2,4) order by wb.id desc limit 1";
        return $this->db()->getOne($sql);
    }

}

?>