<?php
/**
 * 裸钻模块新APiModel类(对purchase库的访问）
 *  -------------------------------------------------
 *   @file		: SelfPurchaseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: LRJ
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class SelfPurchaseModel extends SelfModel
{

    function __construct ($strConn="")
	{
		parent::__construct($strConn);
	}

	
	/*
	*通过款号查询起版列表是否存在
	*
	*/
	public function getQiBanInfosByGoodsId($goods_id){
		$sql = "SELECT id FROM purchase.purchase_qiban_goods WHERE addtime='".$goods_id."'";
		return $this->db()->getOne($sql);
	}

    //占用备货验证可用备货数量
    public function checkOutOrderIsGoods($where, $source_type, $alreadyZy)
    {
        //查询相同渠道，已审核的单据  只允许从 有款采购且需要布产 的采购单中占用名额 //增加是否可用占用备货
        $sql = "select * from purchase_info where channel_ids like '%,".$source_type.",%' and p_status = 3 and is_style = 1 and is_tofactory = 1 and is_zhanyong = 1";//and p_status = 3
        $info = $this->db()->getAll($sql);
        if(!empty($info)){
            foreach ($info as $row) {
                $sql = "select `id`,`num` from purchase_goods where num > 0 and pinfo_id =".$row['id']." and style_sn = '".$where['goods_sn']."'";//查询采购明细信息 相同款式，数量大于0
                $g_info = $this->db()->getAll($sql);
                if(!empty($g_info)){
                    foreach ($g_info as $val) {
                        $id = $val['id'];
                        //查询已经自然入库的数量
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
                        //获取此次已经占用的数量
                        $alrInfo = array();
                        if(!empty($alreadyZy)) $alrInfo = array_count_values($alreadyZy);
                        $check_num = isset($alrInfo[$id])?$alrInfo[$id]:0;
                        //未占有数量 = 总数量-自然入库-原已绑定数量-此次占用数量
                        $num = $val['num'] - $zr_num - $do_num - $check_num;
                        //如果有值 ，则去验证是否有相同属性
                        $sql = "select * from purchase_goods_attr where g_id = ".$id;
                        $attrinfo = $this->db()->getAll($sql);//抓取所有属性值
                        $attr = array();
                        foreach ($attrinfo as $v) {
                            $attr[$v['code']] = $v['value'];
                        }
                        //判断是否属性相同,并且未被占用；
                        if(round($where['cart'],2) == round($attr['zuanshidaxiao'],2) &&
                            $where['color'] == $attr['yanse'] &&
                            $where['clarity'] == $attr['jingdu'] &&
                            $where['caizhi'] == $attr['caizhi'] &&
                            $where['jinse'] == $attr['18k_color'] &&
                            $where['zhiquan'] == $attr['zhiquan']){
                            if($num >0){//!in_array($id, $alreadyZy) &&
                                if(($num-$where['num'])>=0){
                                    $ct = $where['num'];
                                }else{
                                    $ct = $num;
                                }
                                $ret = array();
                                for ($i=0; $i < $ct; $i++) { 
                                    $ret[] = $id;
                                }
                                return array('purchase_id'=>$id,'ret'=>$ret);//返回采购单的货品ID
                            }
                            //return array('purchase_id'=>'','ret'=>array());//需求数量不满足，但有数量则跳过。
                        }
                        continue;
                    }
                }
            }
        }
        return false;
    }
}

?>