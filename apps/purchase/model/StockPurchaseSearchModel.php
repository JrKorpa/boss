<?php
/**
 *  -------------------------------------------------
 *   @file		: StockPurchaseSearchModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-08-03 10:02:03
 *   @update	:
 *  -------------------------------------------------
 */
class StockPurchaseSearchModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'purchase_info';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"p_sn"=>"采购单单号",
"t_id"=>"采购单分类ID",
"is_tofactory"=>"是否去工厂生产",
"is_style"=>"是否有款采购",
"p_sum"=>"采购数量",
"purchase_fee"=>"采购申请费用",
"put_in_type"=>"采购方式（数据字典入库方式）",
"make_uname"=>"制单人姓名",
"make_time"=>"制单时间",
"check_uname"=>"审核人姓名",
"check_time"=>"审核时间",
"p_status"=>"采购单状态：1=新增，2=待审核，3=审核，4=作废",
"p_info"=>"采购单备注",
"prc_id"=>"工厂ID",
"prc_name"=>"工厂名称",
"to_factory_time"=>"采购单分配工厂时间",
"channel_ids"=>"采购——销售渠道",
"is_zhanyong"=>"是否占用备货");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url StockPurchaseSearchController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		//$sql = "SELECT * FROM `".$this->table()."`";
        $sql = "SELECT pi.id,pg.id as g_id,pi.p_sn,pi.check_time,pi.channel_ids,pg.style_sn,pr.bc_sn,pr.`status`,pg.num FROM `purchase_info` pi 
inner join purchase_goods pg on pi.id = pg.pinfo_id
inner join kela_supplier.product_info pr on pg.id = pr.p_id and from_type = 1";
		$str = 'pi.p_status = 3 and pi.is_zhanyong = 1
and pi.is_style = 1 and pi.is_tofactory = 1
and pg.num > 0 AND ';
		if(!empty($where['channel_id']))
		{
			$str .= "pi.`channel_ids` like '%,".addslashes($where['channel_id']).",%' AND ";
		}
        if(!empty($where['style_sn']))
        {
            $str .= "pg.`style_sn`='".$where['style_sn']."' AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY pi.`id` DESC";
        //echo $sql;
        //$data = $this->db()->getAll($sql);
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
        //var_dump($data);die;
        if(!empty($data['data'])){
            foreach ($data['data'] as $key => $value) {
                $g_id = $value['g_id'];
                $arrList = $this->getPurchaseGoodsAttr($g_id);
                //var_dump($arrList);die;
                $num = $this->getMayOccupationNum($g_id,$value['num']);
                if(!empty($arrList)){
                    $arrInfo = array();
                    foreach ($arrList as $k => $v) {
                        $arrInfo[$v['code']] = $v['value'];
                    }
                    $zuanshidaxiao_false = $yanse_false = $jingdu_false = $caizhi_false = $k_color_false = $zhiquan_false = true;
                    if(!empty($where['zuanshidaxiao']) && $arrInfo['zuanshidaxiao'] != $where['zuanshidaxiao']){
                        $zuanshidaxiao_false=false;
                    }
                    if(!empty($where['yanse']) && $arrInfo['yanse'] != $where['yanse']){
                        $yanse_false=false;
                    }
                    if(!empty($where['jingdu']) && $arrInfo['jingdu'] != $where['jingdu']){
                        $jingdu_false=false;
                    }
                    if(!empty($where['caizhi']) && $arrInfo['caizhi'] != $where['caizhi']){
                        $caizhi_false=false;
                    }
                    if(!empty($where['18k_color']) && $arrInfo['18k_color'] != $where['18k_color']){
                        $k_color_false=false;
                    }
                    if(!empty($where['zhiquan']) && $arrInfo['zhiquan'] != $where['zhiquan']){
                        $zhiquan_false=false;
                    }
                    //if($value['id'] = '1580') var_dump($arrInfo,$where);die;
                    if($zuanshidaxiao_false && $yanse_false && $jingdu_false && $caizhi_false && $k_color_false && $zhiquan_false && $num > 0){
                        //$arrInfo['zuanshidaxiao'] == $where['zuanshidaxiao'] && 
                        //$arrInfo['yanse'] == $where['yanse'] && 
                        //$arrInfo['jingdu'] == $where['jingdu'] && 
                        //$arrInfo['caizhi'] == $where['caizhi'] && 
                        //$arrInfo['18k_color'] == $where['18k_color'] && 
                        //$arrInfo['zhiquan'] == $where['zhiquan'] && 
                        
                        $data['data'][$key]['attrdata'] = $arrInfo;
                    }else{
                        unset($data['data'][$key]);
                        continue;
                    }
                    $data['data'][$key]['rel_num'] = $num;//可占用数量
                }
                
            }
        }
		return $data;
	}

    //获取采购货品的属性
    public function getPurchaseGoodsAttr($g_id){
        $sql = "select `id`,`code`,`name`,`value` from `purchase_goods_attr` where `g_id`=".$g_id;
        $result = $this->db()->getAll($sql);
        return $result;
    }

    //获取可占用数量
    public function getMayOccupationNum($id,$zongNum)
    {
        $zr_num = 0;
        $sql = "select bc_sn from kela_supplier.product_info where p_id = '".$id."' and from_type = 1";
        $bc_sn = $this->db()->getOne($sql);
        if(!empty($bc_sn)){
            $sql = "select count(*) from warehouse_shipping.warehouse_goods where buchan_sn = '".$bc_sn."' and is_on_sale <> 1";
            $zr_g_num = $this->db()->getOne($sql);//所有自然入库数量
            $sql = "select count(*) from `app_order`.`purchase_order_info` where (bd_goods_id <> '' or bd_goods_id is not null) and purchase_id = ".$id;
            $zr_do_num = $this->db()->getOne($sql);//备货绑定的且入库的
            //所有自然入库数量 - 备货绑定的且入库的 = 自然入库
            $zr_num = $zr_g_num - $zr_do_num;

        }
        //获取采购信息的已绑定信息
        $sql = "select count(*) from `app_order`.`purchase_order_info` where purchase_id = ".$id;
        $do_num = $this->db()->getOne($sql);
        $do_num = empty($do_num)?0:$do_num;
        //采购单剩余数量 = 总数量-自然入库-原已绑定数量
        $num = $zongNum - $zr_num - $do_num;
        return $num;
    }
}

?>