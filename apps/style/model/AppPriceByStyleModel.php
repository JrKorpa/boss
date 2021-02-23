<?php

/**
 *  -------------------------------------------------
 *   @file		: AppPriceByStyleModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *  -------------------------------------------------
 */
class AppPriceByStyleModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_price_by_style';
        $this->_dataObject = array(
            "id" => "自增ID",
            "style_id" => "款式ID",
            "stone_position" => "石头位置",
            "stone_cat" => '石头类型',
            "tuo_type" => '托类型',
            "zuan_min" => '钻重',
            "zuan_max" => '钻重',
            "zuan_yanse_min" => '颜色范围',
            "zuan_yanse_max" => '颜色范围',
            "zuan_jindu_min" => '净度范围',
            "zuan_jindu_max" => '净度范围',
            "cert" => '证书类型',
            "zuan_shape" => '钻石形状',
            "price" => '定价',
            "is_delete" => '删除标志'
        );
        parent::__construct($id, $strConn);
    }

    public function getListByStyleid($style_id)
    {
        $sql = "SELECT * FROM `".$this->table()."` WHERE style_id = '$style_id';";;
        return $this->db()->getAll($sql);
    }

    public function updateWarehouseGoodsAgeDelete($id)
    {
        if(empty($id)){
            return false;
        }

        $sql = "UPDATE warehouse_shipping.warehouse_goods_age wga
            inner join front.app_salepolicy_goods asg on asg.goods_id = wga.goods_id
            inner join front.base_salepolicy_goods bsg on bsg.goods_id = wga.goods_id
        set 
            asg.is_delete = 2,
            wga.style_kuanprice_id=0,
            wga.is_kuanprice=0,
            wga.kuanprice=0,
            bsg.is_policy=1
        where 
            1
            AND bsg.isXianhuo =1
            AND asg.isXianhuo =1
            AND wga.style_kuanprice_id = $id";
        $this->db()->query($sql);
        $sql = "UPDATE warehouse_shipping.warehouse_goods_age wga
            inner join front.base_salepolicy_goods bsg on bsg.goods_id = wga.goods_id
        set 
            wga.style_kuanprice_id=0,
            wga.is_kuanprice=0,
            wga.kuanprice=0,
            bsg.is_policy=1
        where 
            1
            AND bsg.isXianhuo =1
            AND wga.style_kuanprice_id = $id";
        $this->db()->query($sql);
        $sql = "UPDATE warehouse_shipping.warehouse_goods_age wga
        set 
            wga.style_kuanprice_id=0,
            wga.is_kuanprice=0,
            wga.kuanprice=0
        where 
            1
            AND wga.style_kuanprice_id = $id";
        $this->db()->query($sql);
    }

    public function updateWarehouseGoodsAgeRecover($info)
    {
		$id = $info['id'];
        $price = $info['price'];
        $tuo_type = $info['tuo_type'];
        $caizhi = $info['caizhi'];
        if($caizhi == 1){
            $caizhi = '18K';
        }elseif($caizhi == 2 ){
            $caizhi = 'PT950';
        }

        $style_id = $info['style_id'];
        $start = isset($info['zuan_min'])?$info['zuan_min']:'';
        $end   = isset($info['zuan_max'])?$info['zuan_max']:'';
        $xz = isset($info['zuan_shape'])?$info['zuan_shape']:'';
        $zs = isset($info['cert']) ? $info['cert'] : '';
		if(empty($info['zuan_yanse_min']) || empty($info['zuan_yanse_max']))
		{
			$yanse = '';
		}else{
        	$yanse = implode("','",$this->getYanseList($info['zuan_yanse_min'],$info['zuan_yanse_max']));
		}
		if(empty($info['zuan_jindu_min']) || empty($info['zuan_jindu_max']))
		{
        	$jingdu = '';
		}else{
			$jingdu = implode("','",$this->getJingduList($info['zuan_jindu_min'],$info['zuan_jindu_max']));
		}
		//是否胥国凯说非钻石的 去掉那些颜色，范围过过滤
		$stonposion = $info['stone_position'];
		$zs_sql = '';
        if($zs == '全部'){
            $zs_sql = '';
        }else{
            if($zs == '空值'){
                $zs = '';
            }
			if(empty($zs))
			{
				$zs_sql = '';
			}else{	
            	$zs_sql = "AND wg.luozuanzhengshu = '{$zs}'";
			}
        }
        $sql = "
            update 
                warehouse_shipping.warehouse_goods_age wga
                inner join warehouse_shipping.warehouse_goods wg on wga.goods_id = wg.goods_id
                inner join front.app_salepolicy_goods asg on asg.goods_id = convert(wga.goods_id,char)
                inner join front.base_salepolicy_goods bsg on bsg.goods_id = convert(wga.goods_id,char)
                inner join front.base_salepolicy_info bsi on bsi.policy_id = asg.policy_id
                inner join front.base_style_info si on si.style_sn = wg.goods_sn
            set 
                wga.is_kuanprice=1,
                wga.kuanprice = $price,
                wga.style_kuanprice_id = $id,
                asg.is_delete=1,
                bsg.is_policy=2,
                asg.sale_price=$price,
                asg.chengben=wg.mingyichengben
            where 
                wg.tuo_type = $tuo_type
                AND wg.caizhi like '{$caizhi}%' ";
			if($stonposion==1)
			{
				$sql .= " AND wg.zhushi = '钻石' ";
			}
			
			if(!empty($start))
			{
				$sql .=" AND wg.zuanshidaxiao >= $start ";
			}
			if(!empty($end))
			{
				$sql .=" AND wg.zuanshidaxiao <=  $end ";
			}
			if(!empty($xz))
			{
				$sql .=" AND wg.zhushixingzhuang = '{$xz}' ";
			}
			$sql .=$zs_sql;
			if(!empty($yanse))
			{
				$sql .=" AND wg.zhushiyanse in ('$yanse') ";
			}
			if(!empty($jingdu))
			{
				$sql .=" AND wg.zhushijingdu in ('$jingdu') ";
			}
			$sql .= "
                AND wg.is_on_sale = 2
                AND bsi.is_kuanprice=1
                AND bsg.isXianhuo =1
                AND asg.isXianhuo =1
                AND si.style_id = $style_id
        
        ";
        $this->db()->query($sql); 
        $sql = "
        
            update 
                warehouse_shipping.warehouse_goods_age wga
                inner join warehouse_shipping.warehouse_goods wg on wga.goods_id = wg.goods_id
                inner join front.base_salepolicy_goods bsg on bsg.goods_id = convert(wga.goods_id,char)
                inner join front.base_style_info si on si.style_sn = wg.goods_sn
            set 
                wga.is_kuanprice=1,
                wga.kuanprice = $price,
                wga.style_kuanprice_id = $id,
                bsg.is_policy=2
            where 
                wg.tuo_type = $tuo_type
                AND wg.caizhi like '{$caizhi}%' " ;
				
				if($stonposion==1)
				{
					$sql .= " AND wg.zhushi = '钻石' ";
				}
				
				if(!empty($start))
				{
					$sql .=" AND wg.zuanshidaxiao >= $start ";
				}
				if(!empty($end))
				{
					$sql .=" AND wg.zuanshidaxiao <=  $end ";
				}
				if(!empty($xz))
				{
					$sql .=" AND wg.zhushixingzhuang = '{$xz}' ";
				}
				$sql .= $zs_sql;
				if(!empty($yanse))
				{
					$sql .=" AND wg.zhushiyanse in ('$yanse') ";
				}
				if(!empty($jingdu))
				{
					$sql .=" AND wg.zhushijingdu in ('$jingdu') ";
				}
				$sql .="
                AND wg.is_on_sale = 2

                AND bsg.isXianhuo =1
                AND si.style_id = $style_id
        
        ";
        $this->db()->query($sql);
        $sql = "
        
            update 
                warehouse_shipping.warehouse_goods_age wga
                inner join warehouse_shipping.warehouse_goods wg on wga.goods_id = wg.goods_id
                inner join front.base_style_info si on si.style_sn = wg.goods_sn
            set 
                wga.is_kuanprice=1,
                wga.kuanprice = $price,
                wga.style_kuanprice_id = $id
            where 
                wg.tuo_type = $tuo_type 
                AND wg.caizhi like '{$caizhi}%' ";
				
				if($stonposion==1)
				{
					$sql .= " AND wg.zhushi = '钻石' ";
				}
				
				if(!empty($start))
				{
					$sql .=" AND wg.zuanshidaxiao >= $start ";
				}
				if(!empty($end))
				{
					$sql .=" AND wg.zuanshidaxiao <=  $end ";
				}
				if(!empty($xz))
				{
					$sql .=" AND wg.zhushixingzhuang = '{$xz}' ";
				}
				$sql .= $zs_sql;
				if(!empty($yanse))
				{
					$sql .=" AND wg.zhushiyanse in ('$yanse') ";
				}
				if(!empty($jingdu))
				{
					$sql .=" AND wg.zhushijingdu in ('$jingdu') ";
				}
				$sql .="
                AND wg.is_on_sale = 2
                AND si.style_id = $style_id
        ";
        $this->db()->query($sql);
    }

    public function getYanseAll()
    {
        $zuan_yanse = array(
            1=>'D',
            2=>'D-E',
            3=>'E',
            4=>'F',
            5=>'F-G',
            6=>'G',
            7=>'H',
            8=>'I',
            9=>'I-J',
            10=>'J',
            11=>'K',
            12=>'K-L',
            13=>'L',
            14=>'M',
            15=>'M-N',
            16=>'N',
            17=>'白色',
            18=>'不分级',
            19=>'空值'
        );
        return $zuan_yanse;
    }

    public function getJingduAll()
    {
        $zuan_jingdu = array(
            1=>'FL',
            2=>'IF',
            3=>'VVS1',
            4=>'VVS2',
            5=>'VVS',
            6=>'VS1',
            7=>'VS2',
            8=>'VS',
            9=>'SI1',
            10=>'SI2',
            11=>'SI',
            12=>'P1',
            13=>'P2',
            14=>'P',
            15=>'不分级',
            16=>'空值'
        ); 
        return $zuan_jingdu;
    }

    public function getYanseList($s,$e)
    {
        $zuan_yanse = $this->getYanseAll();
        $j = array_flip($zuan_yanse);
        $s = $j[$s];
        $e = $j[$e];

        $ret = array();
        foreach($zuan_yanse as $key =>$val){
            if($key >=$s && $key <= $e){
                if($val == '空值'){
                    $val = '';
                }
                $ret[] = $val;
            }
        }
        return $ret;
    }


    public function getJingduList($s,$e)
    {
        $zuan_jingdu = $this->getJingduAll();         
        $j = array_flip($zuan_jingdu);
        $s = $j[$s];
        $e = $j[$e];
        
        $ret = array();
        foreach($zuan_jingdu as $key =>$val){
            if($key >=$s && $key <= $e){
                if($val == '空值'){
                    $val = '';
                }
                $ret[] = $val;
            }
        }
        return $ret;
    }

    /**
     * 主石形状
     * @return array
     */
    public function getShapeList() {
        $_style_shape = array(
            "1" => array("item_name" => "垫形"),
            "2" => array("item_name" => "公主方"),
            "3" => array("item_name" => "祖母绿"),
            "4" => array("item_name" => "心形"),
            "5" => array("item_name" => "蛋形"),
            "6" => array("item_name" => "椭圆形"),
            "7" => array("item_name" => "橄榄形"),
            "8" => array("item_name" => "三角形"),
            "9" => array("item_name" => "水滴形"),
            "10" => array("item_name" => "长方形"),
            "11" => array("item_name" => "圆形"),
            "12" => array("item_name" => "梨形")
        );
        return $_style_shape;
    }

    public function getXingzhuang($zhushixingzhuang)
    {
        $x_list = $this->getShapeList();
        foreach($x_list as $key => $v){
            if($v['item_name'] == $zhushixingzhuang){
                return $key;
            }
        }
        return 0;
    }
}
