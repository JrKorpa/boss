<?php
/**
 *  -------------------------------------------------
 *   @file		: MonthlyExportModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-04-05 10:42:41
 *   @update	:
 *  -------------------------------------------------
 */
class MonthlyExportModel extends Model
{

    public $shield_time = '2018-10-01 00:00:00';

    //拼接sql
    public function getsql($where)
    {
        $sql_salse = '';
        if(!empty($where['salse'])){
            foreach ($where['salse'] as $key => $value) {
                $art = explode("_", $value);
                $dep_id = $art[0];
                $name = $art[1];
                $dep_arr = explode("|", rtrim($art[0],"|"));
                if(!empty($dep_arr) && $name != '' && $where['export_type'] != 'zuantui'){
                    $sql_salse.= "(`oi`.`department_id` IN (".implode(',', $dep_arr).") AND `oi`.`create_user` = '".$name."') or";
                }elseif(!empty($dep_arr) && $name != '' && $where['export_type'] == 'zuantui'){
                    $sql_salse.= "(`oi`.`department_id` IN (".implode(',', $dep_arr).") AND `u`.`account` = '".$name."') or";
                }
            }
        }
        //不要用*,修改为具体字段
        if($where['export_type'] == 'xinzeng'){
            $sql = "SELECT 
            `cs`.`source_name` as 'ad_name', `oi`.`create_user` as 'make_order',`oi`.`bespoke_id`, `oi`.`order_sn` as 'order_sn', `sc`.`channel_name` as 'dep_name', `oa`.`order_amount` as 'price', `oa`.`money_paid` as 'money_paid', `oa`.`money_unpaid` as 'order_amount', `oi`.`referer` as 'referer', `od`.`goods_sn`, `od`.`goods_name`, `od`.`goods_type`, `od`.`caizhi` as 'gold', `od`.`jinzhong` as 'gold_weight', `od`.`goods_price` as 'market_price', IF(`od`.`favorable_status` = 3,`od`.`goods_price`-`od`.`favorable_price`,`od`.`goods_price`) as 'goods_price',`od`.`kuan_sn`,`od`.`zhengshuhao`,`od`.`cert`,`od`.`cart`,`od`.`goods_id`,`oi`.`id` as order_id,`od`.`id` as detail_id,
             (case `oi`.`order_status` when 1 then '待审核' when 2 then '已审核' when 3 then '取消' else '关闭' end) as order_status,
             (case `oi`.`order_pay_status` when 1 then '未付款' when 2 then '支付定金' when 3 then '已付款' else '财务备案' end) as order_pay_status,`oi`.`pay_date`,`oi`.`shipfreight_time`,(case `od`.`send_good_status` when 1 then '未发货' when 2 then '已发货' when 3 then '收货确认' when 4 then '允许发货' else '已到店' end) as send_good_status,
         (case `od`.`buchan_status` when 1 then '初始化' when 2 then '待分配' when 3 then '已分配' when 4 then '生产中' when 5 then '质检中' when 6 then '质检完成' when 7 then '部分出厂' when 8 then '作废' when 9 then '已出厂' when 10 then '已取消' when 11 then '不需布产' else '未知' end) as buchan_status,wg.goods_id as bd_goods_id,`wg`.`order_goods_id` as bangding_goods_id,wg.yuanshichengbenjia,(wg.jingxiaoshangchengbenjia-wg.management_fee) as jingxiaoshangchengbenjia,`wg`.warehouse,`od`.is_return,if(`od`.`is_stock_goods`= 1,'现货','期货') as is_stock_goods,`si`.`style_type`,cc.style_channel,od.cpdzcode,`t`.`cat_type_name`,`p`.`product_type_name`, 
             IFNULL(`wg`.`cat_type1`,`t`.`cat_type_name`) AS 'cat_type1',IFNULL(`wg`.`product_type1`,`p`.product_type_name) AS 'product_type1',od.xiangqian,od.details_remark
            FROM  `app_order`.`app_order_account` AS `oa` 
            inner join  `app_order`.`base_order_info` AS `oi` on `oi`.`id` = `oa`.`order_id` 
            inner join `app_order`.`app_order_details` AS `od` on   `oi`.`id` = `od`.`order_id`
            inner join `cuteframe`.`sales_channels` AS `sc` on `sc`.`id` = `oi`.`department_id` 
            left join `cuteframe`.`customer_sources` AS `cs` on `oi`.`customer_source_id` = `cs`.`id`
            left join `front`.`base_cpdz_code` cc on cc.order_detail_id = od.id
            left join `front`.`base_style_info` si on si.style_sn = od.goods_sn
            left join `front`.`app_cat_type` `t` on `t`.`cat_type_id` = `si`.`style_type` 
            left join `front`.`app_product_type` `p` on `p`.`product_type_id` = `si`.`product_type`
            left join `cuteframe`.`shop_cfg` c on `c`.`id` = `sc`.`channel_own_id`
            left join `warehouse_shipping`.`warehouse_goods` as wg on wg.order_goods_id = convert(od.id,CHAR) and wg.order_goods_id>0 ";
            $str = '`oi`.`order_pay_status` in (2,3,4) AND `oi`.`order_status` = 2 AND ';
            if(!empty($where['export_time_start']))
            {
                if($where['export_time_start']<=$this->shield_time && SYS_SCOPE == 'zhanting'){
                    $str.="`oi`.`pay_date` >= '".$this->shield_time."' AND ";
                }else{
                    $str.="`oi`.`pay_date` >= '".$where['export_time_start']." 00:00:00' AND ";
                }
                
            }
            if(!empty($where['export_time_end']))
            {
                $str.="`oi`.`pay_date` <= '".$where['export_time_end']." 23:59:59' AND ";
            }
            if(!empty($where['dep_type'])){
                $str .= "`c`.`shop_type` = '".$where['dep_type']."' AND ";
            }
            if(!empty($where['dep']) && $where['dep'][0] != 'null'){
                $str .= "`oi`.`department_id` in(".implode(',', $where['dep']).") AND ";
            }
            /*if(!empty($where['salse']) && $where['salse'][0] != 'null'){
                $str .= "`oi`.`create_user` in('".implode("','", $where['salse'])."') AND ";
            }*/
            if($sql_salse != ''){
                $str .= "(".rtrim($sql_salse, 'or').") AND ";
            }
            if($str)
            {
                $str = rtrim($str,"AND ");//这个空格很重要
                $sql .=" WHERE ".$str;
            }
            $sql .= " ORDER BY `oi`.`pay_date` DESC";
            //echo $sql;die;
        }elseif($where['export_type'] == 'fahuo'){
            /*
            $sql = "SELECT `cs`.`source_name` as 'ad_name', `oi`.`create_user` as 'make_order',`oi`.`bespoke_id`, `oi`.`order_sn` as 'order_sn', `sc`.`channel_name` as 'dep_name', `oa`.`order_amount` as 'price', `oa`.`money_paid` as 'money_paid', `oa`.`money_unpaid` as 'order_amount', `oi`.`referer` as 'referer',`oi`.order_pay_type, `wbg`.`goods_id`, `od`.`goods_name`, `od`.`goods_type`, `od`.`caizhi` as 'gold', `od`.`jinzhong` as 'gold_weight', `od`.`goods_price` as 'market_price', IF(`od`.`favorable_status` = 3,`od`.`goods_price`-`od`.`favorable_price`,`od`.`goods_price`) as 'goods_price',`od`.`goods_sn`,`od`.`kuan_sn`,`od`.`zhengshuhao`,`od`.cart,`od`.cert ,`wbg`.`yuanshichengben` as chengbenjia ,`od`.`goods_count`,`oi`.`order_status`,`wbg`.`sale_price`,ROUND(IF(`od`.`favorable_status` = 3,`od`.`goods_price`-`od`.`favorable_price`,`od`.`goods_price`)/(`oa`.`goods_amount` - `oa`.`favorable_price`)*100,2) as percent,if(`od`.`is_stock_goods`= 1,'现货','期货') as is_stock_goods,`si`.`style_type`,cc.style_channel,od.cpdzcode,IFNULL(`wg`.`cat_type1`,`t`.`cat_type_name`) AS 'cat_type1',IFNULL(`wg`.`product_type1`,`p`.product_type_name) AS 'product_type1',`oi`.`pay_date`,`oi`.`shipfreight_time`,od.xiangqian,`od`.`id` as detail_id
            FROM 
            `warehouse_shipping`.`warehouse_bill` AS wb 
            inner join `app_order`.`base_order_info` AS `oi` on `wb`.`order_sn` = `oi`.`order_sn`
            inner join `app_order`.`app_order_account` AS `oa` on `oi`.`id` = `oa`.`order_id`
            inner join `app_order`.`app_order_details` AS `od` on `oi`.`id` = `od`.`order_id`
            inner join `cuteframe`.`sales_channels` AS `sc` on `sc`.`id` = `oi`.`department_id`
            inner join `warehouse_shipping`.`warehouse_bill_goods` AS wbg on `wbg`.`detail_id` = `od`.`id` and `wb`.`id`=`wbg`.`bill_id` 
            left join `front`.`base_cpdz_code` cc on cc.order_detail_id = od.id
            left join `front`.`base_style_info` si on si.style_sn = od.goods_sn
            left join `cuteframe`.`customer_sources` AS `cs` on `oi`.`customer_source_id` = `cs`.`id`
            left join `cuteframe`.`shop_cfg` AS c on `c`.`id` = `sc`.`channel_own_id`            
            left join `warehouse_shipping`.`warehouse_goods` as wg on wg.goods_id = wbg.goods_id
            left join `front`.`app_cat_type` `t` on `t`.`cat_type_id` = `si`.`style_type` 
            left join `front`.`app_product_type` `p` on `p`.`product_type_id` = `si`.`product_type`";
            $str = " `wb`.`bill_type` = 'S' AND `wb`.`bill_status` = 2 AND ";
            if(!empty($where['export_time_start']))
            {
                $str.="`wb`.`check_time` >= '".$where['export_time_start']." 00:00:00' AND";
            }
            if(!empty($where['export_time_end']))
            {
                $str.="`wb`.`check_time` <= '".$where['export_time_end']." 23:59:59' AND";
            }
            if(!empty($where['dep_type'])){
                $str .= "`c`.`shop_type` = '".$where['dep_type']."' AND ";
            }
            if(!empty($where['dep']) && $where['dep'][0] != 'null'){
                $str .= "`oi`.`department_id` in(".implode(',', $where['dep']).") AND ";
            }           
            if($sql_salse != ''){
                $str .= "(".rtrim($sql_salse, 'or').") AND ";
            }
            if($str)
            {
                $str = rtrim($str,"AND ");//这个空格很重要
                $sql .=" WHERE ".$str;
            }
            $sql .= " ORDER BY wb.`id` DESC";
            */
            $sql="SELECT cs.source_name  as 'ad_name', oi.`create_user` as 'make_order',oi.`bespoke_id`, oi.`order_sn` as 'order_sn', sc.`channel_name` as 'dep_name',
                 oa.`order_amount` as 'price', oa.`money_paid` as 'money_paid', oa.`money_unpaid` as 'order_amount', oi.`referer` as 'referer',oi.order_pay_type, bg.`goods_id`, 
                 if(d.goods_name is null,g.goods_name,d.goods_name) as goods_name, d.`goods_type`, if(d.`caizhi` is null,g.caizhi,d.caizhi) as 'gold', 
                    if(d.`jinzhong` is null,g.jinzhong,d.jinzhong) as 'gold_weight', if(d.`goods_price` is null,0,d.goods_price) as 'market_price', 
                        if(d.id is null,0,IF(d.`favorable_status` = 3,d.`goods_price`-d.`favorable_price`,d.`goods_price`)) as 'goods_price',if(d.id is null,g.goods_sn,d.`goods_sn`) as goods_sn,
                            d.`kuan_sn`,if(d.id is null,g.zhengshuhao,d.`zhengshuhao`) as zhengshuhao,if(d.id is null ,g.zuanshidaxiao,d.cart) as cart,d.cert ,bg.`yuanshichengben` as chengbenjia ,
                        if(d.id is null,1,d.`goods_count`) as goods_count,oi.`order_status`,bg.`sale_price`,if(d.id is null,0,ROUND(IF(d.`favorable_status` = 3,d.`goods_price`-d.`favorable_price`,d.`goods_price`)/(oa.`goods_amount` - oa.`favorable_price`)*100,2)) as percent,
                            if(d.`is_stock_goods`= 1,'现货','期货') as is_stock_goods,`si`.`style_type`,cc.style_channel,d.cpdzcode,
                IFNULL(g.`cat_type1`,t.`cat_type_name`) AS 'cat_type1',IFNULL(g.`product_type1`,`p`.product_type_name) AS 'product_type1',oi.`pay_date`,oi.`shipfreight_time`,d.xiangqian,d.`id` as detail_id,`sc`.`company_id`,d.is_return,d.favorable_status,if(d.id is null,0,d.favorable_price) as favorable_price ,
               if(d.id is null,1,d.goods_count) as count_num
                from warehouse_shipping.warehouse_bill_goods bg
                left join app_order.app_order_details d on bg.detail_id=d.id 
                left join front.base_style_info si on d.goods_sn=si.style_sn 
                left join front.base_cpdz_code cc on d.id=cc.order_detail_id 
                left join front.app_cat_type t on si.style_type=t.cat_type_id 
                left join front.app_product_type p on si.product_type=p.product_type_id
                ,warehouse_shipping.warehouse_goods g,warehouse_shipping.warehouse_bill b   
                left join app_order.base_order_info oi on b.order_sn=oi.order_sn
                left join app_order.app_order_account oa on oi.id=oa.order_id
                left join cuteframe.customer_sources cs on oi.customer_source_id=cs.id 
                left join cuteframe.sales_channels sc on oi.department_id=sc.id 
                left join cuteframe.shop_cfg c on sc.channel_own_id=c.id ";
               
            $str = " bg.bill_id=b.id and bg.goods_id=g.goods_id and b.bill_type='S' and b.bill_status=2  AND ";
            if(!empty($where['export_time_start']))
            {
                if($where['export_time_start']<=$this->shield_time && SYS_SCOPE == 'zhanting'){
                    $str.="b.`check_time` >= '".$this->shield_time."' AND ";
                }else{
                    $str.="b.`check_time` >= '".$where['export_time_start']." 00:00:00' AND ";
                }
                
            }
            if(!empty($where['export_time_end']))
            {
                $str.="b.`check_time` <= '".$where['export_time_end']." 23:59:59' AND ";
            }
            if(!empty($where['dep_type'])){
                $str .= "c.`shop_type` = '".$where['dep_type']."' AND ";
            }
            if(!empty($where['dep']) && $where['dep'][0] != 'null'){
                $str .= "oi.`department_id` in(".implode(',', $where['dep']).") AND ";
            }           
            if($sql_salse != ''){
                $str .= "(".rtrim($sql_salse, 'or').") AND ";
            }
            if($str)
            {
                $str = rtrim($str,"AND ");//这个空格很重要
                $sql .=" WHERE ".$str;
            }
            $sql .= " ORDER BY b.`id` DESC";

            //echo $sql;die;
        }elseif($where['export_type'] == 'zuantui'){
            $sql = "SELECT `oi`.`referer`,`oi`.`order_sn` order_sn,`cs`.`source_name` as ad_name,`oi`.`create_user` as make_order,`oi`.`bespoke_id`,`sc`.`channel_name` as 'dep_name',`oa`.`order_amount` as 'price',  `oa`.`real_return_price` as 'real_return_price',(`oa`.`goods_amount`-`oa`.`favorable_price`) as `goods_amount`, IF(`od`.`favorable_status` = 3,`od`.`goods_price`-`od`.`favorable_price`,`od`.`goods_price`) as 'goods_price', IF(`od`.`favorable_status` = 3,`od`.`goods_price`-`od`.`favorable_price`,`od`.`goods_price`) as 'goods_price1', should_return_amount,  confirm_price, `r`.`return_type`, `od`.`goods_type`, `od`.`kuan_sn`, `od`.`zhengshuhao`, `od`.`cert`,`od`.`cart`, `od`.`goods_id`, `od`.`goods_sn`, `od`.`caizhi`, `od`.`jinzhong`, `od`.`goods_name`, `od`.`goods_price` market_price,`oi`.`id` as order_id,`rc`.`deparment_finance_time`,(case `oi`.`order_status` when 1 then '待审核' when 2 then '已审核' when 3 then '取消' else '关闭' end) as order_status,(case `oi`.`order_pay_status` when 1 then '未付款' when 2 then '支付定金' when 3 then '已付款' else '财务备案' end) as order_pay_status,`u`.`account`,`r`.`apply_user_id`,if(`od`.`is_stock_goods`= 1,'现货','期货') as is_stock_goods,`si`.`style_type`,if(`r`.`return_by` = 1,'退商品','不退商品') as return_by,r.apply_return_amount,r.return_by as return_bys,cc.style_channel,od.cpdzcode,(case `r`.`return_type` when 1 then '转单' when 2 then '打卡' when 3 then '现金' else '--' end) as return_types,IFNULL(`wg`.`cat_type1`,`act`.`cat_type_name`) AS 'cat_type1',IFNULL(`wg`.`product_type1`,`p`.product_type_name) AS 'product_type1',od.xiangqian,`od`.`id` as detail_id,`r`.`order_goods_id`
            FROM `app_order`.`app_return_goods` AS r 
            inner join `app_order`.`app_return_check` AS rc on `rc`.`return_id` = `r`.`return_id`
            inner join `app_order`.`base_order_info` AS `oi` on `r`.`order_sn` = `oi`.`order_sn`
            inner join `app_order`.`app_order_account` AS `oa` on `oa`.`order_id` = `oi`.`id`
            inner join `cuteframe`.`sales_channels` AS `sc` on `sc`.`id` = `oi`.`department_id`
            left join `cuteframe`.`customer_sources` AS `cs` on `oi`.`customer_source_id` = `cs`.`id`
            left join `app_order`.`app_order_details` od on `od`.`id` = `r`.`order_goods_id`
            left join `warehouse_shipping`.`warehouse_goods` as wg on wg.order_goods_id = convert(od.id,CHAR) and wg.order_goods_id>0
            left join `front`.`base_cpdz_code` cc on cc.order_detail_id = od.id
            left join `front`.`base_style_info` si on si.style_sn = od.goods_sn
            left join `cuteframe`.`user` u on `u`.`id` =`r`.`apply_user_id`
            left join `cuteframe`.`shop_cfg` AS c on `c`.`id` = `sc`.`channel_own_id`
            left join front.`app_product_type` `p` on `p`.`product_type_id` = `si`.`product_type`
            left join front.`app_cat_type` `act` on `act`.`cat_type_id` = `si`.`style_type`";

            $str = '';
            if(!empty($where['export_time_start']))
            {
                if($where['export_time_start']<=$this->shield_time && SYS_SCOPE == 'zhanting'){
                    $str.="`rc`.`deparment_finance_time` >= '".$this->shield_time."' AND ";
                }else{
                    $str.="`rc`.`deparment_finance_time` >= '".$where['export_time_start']." 00:00:00' AND";
                }
            }
            if(!empty($where['export_time_end']))
            {
                $str.="`rc`.`deparment_finance_time` <= '".$where['export_time_end']." 23:59:59' AND";
            }
            if(!empty($where['dep_type'])){
                $str .= "`c`.`shop_type` = '".$where['dep_type']."' AND ";
            }
            if(!empty($where['dep']) && $where['dep'][0] != 'null'){
                $str .= "`oi`.`department_id` in(".implode(',', $where['dep']).") AND ";
            }
            /*if(!empty($where['salse']) && $where['salse'][0] != 'null'){
                $str .= "`oi`.`create_user` in('".implode("','", $where['salse'])."') AND ";
            }*/
            if($sql_salse != ''){
                $str .= "(".rtrim($sql_salse, 'or').") AND ";
            }
            if($str)
            {
                $str = rtrim($str,"AND ");//这个空格很重要
                $sql .=" WHERE ".$str;
            }
            $sql .= " ORDER BY `r`.`return_id` DESC";
        }
        //echo $sql;die;
        return $sql;
    }

	/**
	 *	pageList，分页列表
	 *
	 *	@url MonthlyExportController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = $this->getsql($where);
		$data = $this->getPageList($sql,array(),$page, $pageSize,$useCache);
        $dataArr = $this->getDataC($where);
        $returnData = $this->ExportSet($data,$where,$dataArr);
        unset($returnData['detailsid']);
        $data['data'] = $returnData;
		return $data;
	}

    public function get_fahuo_sql($where=array())
    {
        $sql = '';
        if($where['export_type'] == 'fahuo'){
            $sql = "SELECT `cs`.`source_name` as 'ad_name', `oi`.`create_user` as 'make_order',`oi`.`bespoke_id`, `oi`.`order_sn` as 'order_sn', `sc`.`channel_name` as 'dep_name', `oa`.`order_amount` as 'price', `oa`.`money_paid` as 'money_paid', `oa`.`money_unpaid` as 'order_amount', `oi`.`referer` as 'referer',`oi`.order_pay_type, `wbg`.`goods_id`, `od`.`goods_name`, `od`.`goods_type`, `od`.`caizhi` as 'gold', `od`.`jinzhong` as 'gold_weight', `od`.`goods_price` as 'market_price', IF(`od`.`favorable_status` = 3,`od`.`goods_price`-`od`.`favorable_price`,`od`.`goods_price`) as 'goods_price',`od`.`goods_sn`,`od`.`kuan_sn`,`od`.`zhengshuhao`,`od`.cart,`od`.cert ,`wbg`.`yuanshichengben` as chengbenjia ,`od`.`goods_count`,`oi`.`order_status`,`wbg`.`sale_price`,ROUND(IF(`od`.`favorable_status` = 3,`od`.`goods_price`-`od`.`favorable_price`,`od`.`goods_price`)/(`oa`.`goods_amount` - `oa`.`favorable_price`)*100,2) as percent,if(`od`.`is_stock_goods`= 1,'现货','期货') as is_stock_goods,`si`.`style_type`,cc.style_channel,od.cpdzcode,
              IFNULL(`wg`.`cat_type1`,`act`.`cat_type_name`) AS 'cat_type1',IFNULL(`wg`.`product_type1`,`p`.product_type_name) AS 'product_type1',`oi`.`pay_date`,`oi`.`shipfreight_time`,od.xiangqian,`od`.`id` as detail_id,`sc`.`company_id`,od.is_return,od.favorable_status,od.favorable_price,od.goods_count as count_num
        FROM 
            `warehouse_shipping`.`warehouse_bill` AS wb 
            inner join `app_order`.`base_order_info` AS `oi` on `wb`.`order_sn` = `oi`.`order_sn`
            inner join `app_order`.`app_order_account` AS `oa` on `oi`.`id` = `oa`.`order_id`
            inner join `app_order`.`app_order_details` AS `od` on `oi`.`id` = `od`.`order_id`
            inner join `cuteframe`.`sales_channels` AS `sc` on `sc`.`id` = `oi`.`department_id`
            inner join `warehouse_shipping`.`warehouse_bill_goods` AS wbg on `wbg`.`detail_id` = `od`.`id` and `wb`.`id`=`wbg`.`bill_id`
            left join `front`.`base_cpdz_code` cc on cc.order_detail_id = od.id
            left join `front`.`base_style_info` si on si.style_sn = od.goods_sn
            left join `cuteframe`.`customer_sources` AS `cs` on `oi`.`customer_source_id` = `cs`.`id`
            left join `cuteframe`.`shop_cfg` AS c on `c`.`id` = `sc`.`channel_own_id`
            left join `warehouse_shipping`.`warehouse_goods` AS wg on  `wbg`.`goods_id`=`wg`.`goods_id`
            left join front.`app_product_type` `p` on `p`.`product_type_id` = `si`.`product_type`
            left join front.`app_cat_type` `act` on `act`.`cat_type_id` = `si`.`style_type`";
            $str = " `wb`.`bill_type` = 'S' AND `wb`.`bill_status` = 2 AND oi.referer <> '婚博会' AND ";
            if(!empty($where['export_time_start']))
            {
                $str.="`wb`.`check_time` >= '".$where['export_time_start']." 00:00:00' AND";
            }
            if(!empty($where['export_time_end']))
            {
                $str.="`wb`.`check_time` <= '".$where['export_time_end']." 23:59:59' AND";
            }
            if(!empty($where['dep_type'])){
                $str .= "`c`.`shop_type` = '".$where['dep_type']."' AND ";
            }
            if(!empty($where['dep']) && $where['dep'][0] != 'null'){
                //$str .= "`oi`.`department_id` in(".implode(',', $where['dep']).") AND ";
                $str .= "`oi`.`department_id`  =".$where['dep'][0]." AND ";
            }
            if(!empty($where['salse']) && $where['salse'][0] != 'null'){
                if(count($where['salse'])>1){
                    $str .= "`oi`.`create_user` in('".implode("','", $where['salse'])."') AND ";
                }else{
                    $str .= "`oi`.`create_user` = '".$where['salse'][0]."' AND ";
                }
                
            }
            if($str)
            {
                $str = rtrim($str,"AND ");//这个空格很重要
                $sql .=" WHERE ".$str;
            }
            $sql .= " ORDER BY wb.`id` DESC";
        }elseif($where['export_type'] == 'zuantui'){
            $sql = "SELECT `oi`.`referer`,`oi`.`order_sn` order_sn,`cs`.`source_name` as ad_name,`oi`.`create_user` as make_order,`oi`.`bespoke_id`,`sc`.`channel_name` as 'dep_name',`oa`.`order_amount` as 'price',  `oa`.`real_return_price` as 'real_return_price',(`oa`.`goods_amount`-`oa`.`favorable_price`) as `goods_amount`, IF(`od`.`favorable_status` = 3,`od`.`goods_price`-`od`.`favorable_price`,`od`.`goods_price`) as 'goods_price',IF(`od`.`favorable_status` = 3,`od`.`goods_price`-`od`.`favorable_price`,`od`.`goods_price`) as 'goods_price1', should_return_amount,  confirm_price, `r`.`return_type`, `od`.`goods_type`, `od`.`kuan_sn`, `od`.`zhengshuhao`, `od`.`cert`,`od`.`cart`, `od`.`goods_id`, `od`.`goods_sn`, `od`.`caizhi`, `od`.`jinzhong`, `od`.`goods_name`, `od`.`goods_price` market_price,`oi`.`id` as order_id,`rc`.`deparment_finance_time`,(case `oi`.`order_status` when 1 then '待审核' when 2 then '已审核' when 3 then '取消' else '关闭' end) as order_status,(case `oi`.`order_pay_status` when 1 then '未付款' when 2 then '支付定金' when 3 then '已付款' else '财务备案' end) as order_pay_status,`u`.`account`,`r`.`apply_user_id`,if(`od`.`is_stock_goods`= 1,'现货','期货') as is_stock_goods,`si`.`style_type`,if(`r`.`return_by` = 1,'退商品','不退商品') as return_by,r.apply_return_amount,r.return_by as return_bys,cc.style_channel,od.cpdzcode,(case `r`.`return_type` when 1 then '转单' when 2 then '打卡' when 3 then '现金' else '--' end) as return_types, 
            IFNULL(`wg`.`cat_type1`,`act`.`cat_type_name`) AS 'cat_type1',IFNULL(`wg`.`product_type1`,`p`.product_type_name) AS 'product_type1',od.xiangqian,`od`.`id` as detail_id,`sc`.`company_id`,od.is_return,od.favorable_status,od.favorable_price,od.goods_count as count_num,`r`.`order_goods_id`
            FROM `app_order`.`app_return_goods` AS r 
            inner join `app_order`.`app_return_check` AS rc on `rc`.`return_id` = `r`.`return_id`
            inner join `app_order`.`base_order_info` AS `oi` on `r`.`order_sn` = `oi`.`order_sn`
            inner join `app_order`.`app_order_account` AS `oa` on `oa`.`order_id` = `oi`.`id`
            inner join `cuteframe`.`sales_channels` AS `sc` on `sc`.`id` = `oi`.`department_id`
            left join `cuteframe`.`customer_sources` AS `cs` on `oi`.`customer_source_id` = `cs`.`id`
            left join `app_order`.`app_order_details` od on `od`.`id` = `r`.`order_goods_id`
            left join `front`.`base_cpdz_code` cc on cc.order_detail_id = od.id
            left join `front`.`base_style_info` si on si.style_sn = od.goods_sn
            left join `cuteframe`.`user` u on `u`.`id` =`r`.`apply_user_id`
            left join `cuteframe`.`shop_cfg` AS c on `c`.`id` = `sc`.`channel_own_id`
            left join `warehouse_shipping`.`warehouse_goods` AS wg on `r`.`order_goods_id`=`wg`.`id`
            left join front.`app_product_type` `p` on `p`.`product_type_id` = `si`.`product_type`
            left join front.`app_cat_type` `act` on `act`.`cat_type_id` = `si`.`style_type`";
            $str = '';
            if(!empty($where['export_time_start']))
            {
                $str.="`rc`.`deparment_finance_time` >= '".$where['export_time_start']." 00:00:00' AND";
            }
            if(!empty($where['export_time_end']))
            {
                $str.="`rc`.`deparment_finance_time` <= '".$where['export_time_end']." 23:59:59' AND";
            }
            if(!empty($where['dep_type'])){
                $str .= "`c`.`shop_type` = '".$where['dep_type']."' AND ";
            }

            //if(!empty($where['dep']) && $where['dep'][0] != 'null'){
                //$str .= "`oi`.`department_id` in(".implode(',', $where['dep']).") AND ";
                //$str .= "`oi`.`department_id`  =".$where['dep'][0]." AND ";
            //}
            if(!empty($where['salse']) && $where['salse'][0] != 'null'){
                if(count($where['salse'])>1){
                    $str .= "`u`.`account` in('".implode("','", $where['salse'])."') AND ";
                }else{
                    $str .= "`u`.`account` = '".$where['salse'][0]."' AND ";
                }
            }
            //if($sql_salse != ''){
                //$str .= "(".rtrim($sql_salse, 'or').") AND ";
            //}
            if($str)
            {
                $str = rtrim($str,"AND ");//这个空格很重要
                $sql .=" WHERE ".$str;
            }
            $sql .= " ORDER BY `r`.`return_id` DESC";
            //echo $sql;die;
        }elseif($where['export_type'] == 'xinzeng'){
            $sql = "SELECT 
            `cs`.`source_name` as 'ad_name', `oi`.`create_user` as 'make_order',`oi`.`bespoke_id`, `oi`.`order_sn` as 'order_sn', `sc`.`channel_name` as 'dep_name', `oa`.`order_amount` as 'price', `oa`.`money_paid` as 'money_paid', `oa`.`money_unpaid` as 'order_amount', `oi`.`referer` as 'referer', `od`.`goods_sn`, `od`.`goods_name`, `od`.`goods_type`, `od`.`caizhi` as 'gold', `od`.`jinzhong` as 'gold_weight', `od`.`goods_price` as 'market_price', IF(`od`.`favorable_status` = 3,`od`.`goods_price`-`od`.`favorable_price`,`od`.`goods_price`) as 'goods_price',`od`.`kuan_sn`,`od`.`zhengshuhao`,`od`.`cert`,`od`.`cart`,`od`.`goods_id`,`oi`.`id` as order_id,`od`.`id` as detail_id,
             (case `oi`.`order_status` when 1 then '待审核' when 2 then '已审核' when 3 then '取消' else '关闭' end) as order_status,
             (case `oi`.`order_pay_status` when 1 then '未付款' when 2 then '支付定金' when 3 then '已付款' else '财务备案' end) as order_pay_status,`oi`.`pay_date`,`oi`.`shipfreight_time`,(case `od`.`send_good_status` when 1 then '未发货' when 2 then '已发货' when 3 then '收货确认' when 4 then '允许发货' else '已到店' end) as send_good_status,
         (case `od`.`buchan_status` when 1 then '初始化' when 2 then '待分配' when 3 then '已分配' when 4 then '生产中' when 5 then '质检中' when 6 then '质检完成' when 7 then '部分出厂' when 8 then '作废' when 9 then '已出厂' when 10 then '已取消' when 11 then '不需布产' else '未知' end) as buchan_status,wg.goods_id as bd_goods_id,`wg`.`order_goods_id` as bangding_goods_id,wg.yuanshichengbenjia,(wg.jingxiaoshangchengbenjia-wg.management_fee) as jingxiaoshangchengbenjia,`wg`.warehouse,`od`.is_return,if(`od`.`is_stock_goods`= 1,'现货','期货') as is_stock_goods,`si`.`style_type`,cc.style_channel,od.cpdzcode, 
            IFNULL(`wg`.`cat_type1`,`act`.`cat_type_name`) AS 'cat_type1',IFNULL(`wg`.`product_type1`,`p`.product_type_name) AS 'product_type1',od.xiangqian,od.details_remark,`sc`.`company_id`,od.is_return,od.favorable_status,od.favorable_price,od.goods_count as count_num
            FROM  `app_order`.`app_order_account` AS `oa` 
            inner join  `app_order`.`base_order_info` AS `oi` on `oi`.`id` = `oa`.`order_id` 
            inner join `app_order`.`app_order_details` AS `od` on   `oi`.`id` = `od`.`order_id`
            inner join `cuteframe`.`sales_channels` AS `sc` on `sc`.`id` = `oi`.`department_id` 
            left join `front`.`base_cpdz_code` cc on cc.order_detail_id = od.id
            left join `front`.`base_style_info` si on si.style_sn = od.goods_sn
            left join `cuteframe`.`customer_sources` AS `cs` on `oi`.`customer_source_id` = `cs`.`id`
            left join `cuteframe`.`shop_cfg` c on `c`.`id` = `sc`.`channel_own_id`
            left join `warehouse_shipping`.`warehouse_goods` as wg on wg.order_goods_id = convert(od.id,CHAR) and wg.order_goods_id>0 
            left join front.`app_product_type` `p` on `p`.`product_type_id` = `si`.`product_type`
            left join front.`app_cat_type` `act` on `act`.`cat_type_id` = `si`.`style_type`";
            $str = '`oi`.`order_pay_status` in (2,3,4) AND `oi`.`order_status` = 2 AND IF(`od`.`favorable_status` = 3,`od`.`goods_price`-`od`.`favorable_price`,`od`.`goods_price`) > 0 AND ';
            if(!empty($where['export_time_start']))
            {
                $str.="`oi`.`pay_date` >= '".$where['export_time_start']." 00:00:00' AND ";
            }
            if(!empty($where['export_time_end']))
            {
                $str.="`oi`.`pay_date` <= '".$where['export_time_end']." 23:59:59' AND ";
            }
            if(!empty($where['dep_type'])){
                $str .= "`c`.`shop_type` = '".$where['dep_type']."' AND ";
            }
            if(!empty($where['dep']) && $where['dep'][0] != 'null'){
                //$str .= "`oi`.`department_id` in(".implode(',', $where['dep']).") AND ";
                $str .= "`oi`.`department_id`  =".$where['dep'][0]." AND ";
            }
            if(!empty($where['salse']) && $where['salse'][0] != 'null'){
                if(count($where['salse'])>1){
                    $str .= "`oi`.`create_user` in('".implode("','", $where['salse'])."') AND ";
                }else{
                    $str .= "`oi`.`create_user` = '".$where['salse'][0]."' AND ";
                }
            }
            if($str)
            {
                $str = rtrim($str,"AND ");//这个空格很重要
                $sql .=" WHERE ".$str;
            }
            $sql .= " ORDER BY `oi`.`pay_date` DESC";
        }elseif($where['export_type'] == 'hbh'){//不包含所属渠道、当前时间段、销售顾问、婚博会订单
            $sql = "SELECT 
            `cs`.`source_name` as 'ad_name', `oi`.`create_user` as 'make_order',`oi`.`bespoke_id`, `oi`.`order_sn` as 'order_sn', `sc`.`channel_name` as 'dep_name', `oa`.`order_amount` as 'price', `oa`.`money_paid` as 'money_paid', `oa`.`money_unpaid` as 'order_amount', `oi`.`referer` as 'referer', `od`.`goods_sn`, `od`.`goods_name`, `od`.`goods_type`, `od`.`caizhi` as 'gold', `od`.`jinzhong` as 'gold_weight', `od`.`goods_price` as 'market_price', IF(`od`.`favorable_status` = 3,`od`.`goods_price`-`od`.`favorable_price`,`od`.`goods_price`) as 'goods_price',`od`.`kuan_sn`,`od`.`zhengshuhao`,`od`.`cert`,`od`.`cart`,`od`.`goods_id`,`oi`.`id` as order_id,`od`.`id` as detail_id,
             (case `oi`.`order_status` when 1 then '待审核' when 2 then '已审核' when 3 then '取消' else '关闭' end) as order_status,
             (case `oi`.`order_pay_status` when 1 then '未付款' when 2 then '支付定金' when 3 then '已付款' else '财务备案' end) as order_pay_status,`oi`.`pay_date`,`oi`.`shipfreight_time`,(case `od`.`send_good_status` when 1 then '未发货' when 2 then '已发货' when 3 then '收货确认' when 4 then '允许发货' else '已到店' end) as send_good_status,
         (case `od`.`buchan_status` when 1 then '初始化' when 2 then '待分配' when 3 then '已分配' when 4 then '生产中' when 5 then '质检中' when 6 then '质检完成' when 7 then '部分出厂' when 8 then '作废' when 9 then '已出厂' when 10 then '已取消' when 11 then '不需布产' else '未知' end) as buchan_status,wg.goods_id as bd_goods_id,`wg`.`order_goods_id` as bangding_goods_id,wg.yuanshichengbenjia,(wg.jingxiaoshangchengbenjia-wg.management_fee) as jingxiaoshangchengbenjia,`wg`.warehouse,`od`.is_return,if(`od`.`is_stock_goods`= 1,'现货','期货') as is_stock_goods,`si`.`style_type`,cc.style_channel,od.cpdzcode, 
            IFNULL(`wg`.`cat_type1`,`act`.`cat_type_name`) AS 'cat_type1',IFNULL(`wg`.`product_type1`,`p`.product_type_name) AS 'product_type1',od.xiangqian,od.details_remark,`sc`.`company_id`,od.is_return,od.favorable_status,od.favorable_price,od.goods_count as count_num
            FROM  `app_order`.`app_order_account` AS `oa` 
            inner join  `app_order`.`base_order_info` AS `oi` on `oi`.`id` = `oa`.`order_id` 
            inner join `app_order`.`app_order_details` AS `od` on   `oi`.`id` = `od`.`order_id`
            inner join `cuteframe`.`sales_channels` AS `sc` on `sc`.`id` = `oi`.`department_id` 
            left join `front`.`base_cpdz_code` cc on cc.order_detail_id = od.id
            left join `front`.`base_style_info` si on si.style_sn = od.goods_sn
            left join `cuteframe`.`customer_sources` AS `cs` on `oi`.`customer_source_id` = `cs`.`id`
            left join `cuteframe`.`shop_cfg` c on `c`.`id` = `sc`.`channel_own_id`
            left join `warehouse_shipping`.`warehouse_goods` as wg on wg.order_goods_id = convert(od.id,CHAR) and wg.order_goods_id>0
            left join front.`app_product_type` `p` on `p`.`product_type_id` = `si`.`product_type`
            left join front.`app_cat_type` `act` on `act`.`cat_type_id` = `si`.`style_type`";
            $str = "`oi`.`order_pay_status` in (2,3,4) AND `oi`.`referer` = '婚博会' AND `oi`.`order_status` = 2 AND ";
            if(!empty($where['export_time_start']))
            {
                $str.="`oi`.`pay_date` >= '".$where['export_time_start']." 00:00:00' AND ";
            }
            if(!empty($where['export_time_end']))
            {
                $str.="`oi`.`pay_date` <= '".$where['export_time_end']." 23:59:59' AND ";
            }
            //if(!empty($where['dep_type'])){
                //$str .= "`c`.`shop_type` = '".$where['dep_type']."' AND ";
            //}
            if(!empty($where['dep']) && $where['dep'][0] != 'null'){
                //$str .= "`oi`.`department_id` in(".implode(',', $where['dep']).") AND ";
                $str .= "`oi`.`department_id` <> ".$where['dep'][0]." AND ";
            }
            if(!empty($where['salse']) && $where['salse'][0] != 'null'){
                if(count($where['salse'])>1){
                    $str .= "`oi`.`create_user` in('".implode("','", $where['salse'])."') AND ";
                }else{
                    $str .= "`oi`.`create_user` = '".$where['salse'][0]."' AND ";
                }
            }
            if($str)
            {
                $str = rtrim($str,"AND ");//这个空格很重要
                $sql .=" WHERE ".$str;
            }
            $sql .= " ORDER BY `oi`.`pay_date` DESC";
            //echo $sql;
        }
        
        return $sql;
    }

    //提成调用
    public function pushMoneyInvoking($where,$dataArr)
    {
       $sql = $this->get_fahuo_sql($where);
       $data['data']= $this->db()->getAll($sql);
       //$dataArr = $this->getDataC($where);
       $returnData = $this->ExportSet($data,$where,$dataArr);
       unset($returnData['detailsid']);
       return $returnData;
    }

    //数据处理
    public function getDataC($where = array())
    {
        set_time_limit(0);
        ini_set('memory_limit','3500M');
        $sql = "SELECT `id`,`name` FROM `front`.`app_style_xilie` WHERE `status`='1'";
        $xilies = $this->db()->getAll($sql);
        foreach($xilies as $v){
            $xilie[$v['id']]=$v['name'];
        }
        $sql = "SELECT `zhengshuhao` FROM `warehouse_shipping`.`warehouse_goods` WHERE `gemx_zhengshu`!=''";
        $gemxres = $this->db()->getAll($sql);
        $gem=array();
        foreach($gemxres as $k=>$v){
            if($v['zhengshuhao']){
                $gem[$v['zhengshuhao']]='星耀';
            }
        }
        //以旧换新
        if(SYS_SCOPE == 'zhanting'){
            //272：以旧换新/转单 252：经销商渠道协作收款 321：跨渠道协作收款
            $huanxing = array('272','252','321');
        }else{
            //272:以旧换新/转单  252：经销商渠道协作收款 320：以旧换新-补差额
            $huanxing = array('272','252','320');
        }
        $orderPayAll = array();
        $orderPayZdAll = array();
        $orderDepositAll = array();
        if($where['export_type'] == 'fahuo'){
            $sql = "select order_sn,pay_type,`zhuandan_sn`,`deposit` from finance.app_order_pay_action where status<>4 and pay_type in(".implode(',', $huanxing).")";
            $orderPay = $this->db()->getAll($sql);
            foreach ($orderPay as $cy) {
                $orderPayAll[$cy['order_sn']][] = $cy['pay_type'];
                $orderPayZdAll[$cy['order_sn']][] = $cy['zhuandan_sn'];
                $orderDepositAll[$cy['order_sn']][] = $cy['deposit'];
            }
        }
        $style_info = $this->getTsydSpecial();
        $sql = "select cat_type_id, cat_type_name from front.app_cat_type";
        $res = $this->db()->getAll($sql);
        $style_cat = array();
        foreach ($res as $key => $val) {
            $style_cat[$val['cat_type_id']] = $val['cat_type_name'];
        }
        $data['orderPayAll'] = $orderPayAll;
        $data['orderPayZdAll'] = $orderPayZdAll;
        $data['orderDepositAll'] = $orderDepositAll;
        $data['gem'] = $gem;
        $data['xilie'] = $xilie;
        $data['style_cat'] = $style_cat;
        $data['style_info'] = $style_info;
        return $data;
    }

    //处理结果集
    public function ExportSet($data = array(),$where = array(),$dataArr = array(),$detailsid= array())
    {
        $returnData = array();
        if(!empty($data['data'])){
            $orderPayAll = $dataArr['orderPayAll'];
            $orderPayZdAll = $dataArr['orderPayZdAll'];
            $orderDepositAll = $dataArr['orderDepositAll'];
            $gem = $dataArr['gem'];
            $xilie = $dataArr['xilie'];
            $style_cat = $dataArr['style_cat'];
            $style_info = $dataArr['style_info'];
            //$detailsid = array();//容纳订单明细ID
            foreach ($data['data'] as $key => $val) {
                //款式系列
                $val['xl_name_str']='';
                if($val['goods_type']!='lz' && $val['goods_type']!='caizuan_goods'){
                    $sql = "SELECT `xilie` FROM `front`.`base_style_info` WHERE `style_sn` = '".$val['goods_sn']."'";
                    $goods_sn = $this->db()->getOne($sql);
                    $gs = array_filter(explode(',',$goods_sn));
                    $xl_name = array();
                    if($gs){
                        foreach($gs as $g => $s){
                            $xl_name[] = isset($xilie[$s])?$xilie[$s]:'';
                        }
                        $val['xl_name_str'] = implode(',',$xl_name);
                    }
                    $sql = "SELECT * FROM `warehouse_shipping`.`rel_hrd` WHERE `tuo_a` = '".$val['goods_id']."'";
                    $kuan_sn = $this->db()->getRow($sql);
                    if(!empty($kuan_sn)){
                        $val['kuan_sn']=true;
                    }else{
                        $val['kuan_sn']=false;
                    }
                }
                if($val['goods_type']=='lz'){
                    $val['zhengshuhao']=str_replace("GIA","",$val['zhengshuhao']);
                }
                //款式分类
                $val['style_type'] = isset($style_cat[$val['style_type']])?$style_cat[$val['style_type']]:'';
                //是否网销
                $val['bespoke_make_order']='';
                $val['wangxiao']="否";
                if($val['bespoke_id'] != ''){
                    $sql = "SELECT * FROM `front`.`app_bespoke_info` WHERE `bespoke_id` ='".$val['bespoke_id']."'";
                    $BespokereInfo = $this->db()->getRow($sql);
                    $bespoke_person = '';
                    if(!empty($BespokereInfo) && $BespokereInfo['make_order'] != ''){
                        $sql = "select * from cuteframe.sales_channels_person where concat(',',dp_is_netsale,',') like '%,".$BespokereInfo['make_order'].",%'";
                        $bespoke_person = $this->db()->getRow($sql);
                    }
                    if($bespoke_person){
                        $val['bespoke_make_order']=$BespokereInfo['make_order'];
                        $val['wangxiao']="是";
                    }
                }
                //证书类型
                $to_tsyd = isset($gem[$val['zhengshuhao']])?$gem[$val['zhengshuhao']]:'';
                $val['dia_type']=$val['kuan_sn']?"天生一对":$to_tsyd;
                $is_tsyd_special = '';
                if(in_array($val['goods_sn'],$style_info)){
                    $is_tsyd_special = '是';
                }
                //② 如果“天生一对特殊款”列为“是”，那么此款的镶嵌要求如果是“需工厂镶嵌”并且证书号列有值，
                //那么找到此钻石的销售记录，钻石的“天生一对特殊款”列也是“是”
                //if($is_tsyd_special == '是' && $val['xiangqian'] == '需工厂镶嵌' && !empty($val['zhengshuhao'])){
                    //$sql = "select id from app_order.app_order_details where zhengshuhao = '".$val['zhengshuhao']."' and id <>".$val['detail_id'];
                    //$zhengshuhaoList[] = $this->db()->getOne($sql);
                //}
                $val['is_tsyd_special'] = $is_tsyd_special;
                if($where['export_type'] == 'xinzeng'){
                    //证书号
                    if($val['cert']==""){
                        $sql ="select zhengshuleibie from warehouse_shipping.warehouse_goods where goods_id='".$val['goods_id']."' and zhengshuleibie is not null";
                        $val['cert'] = $this->db()->getOne($sql);
                    }
                    //是否退货
                    $sql = "select * from `app_order`.`app_return_goods` where order_goods_id = " .$val['detail_id']." order by `return_id` desc limit 1";
                    $datas = $this->db()->getRow($sql);
                    $val['is_return_srt'] = '否';
                    if(!empty($datas) && $datas['check_status']>=4 && $val['is_return'] == 1){
                        $val['is_return_srt'] = '是';
                    }
                    //成本价
                    $val['chengbenprice'] = $where['dep_type'] == 1?$val['yuanshichengbenjia']:$val['jingxiaoshangchengbenjia'];
                    $sql = "select ur.company_id from cuteframe.`user` u inner join cuteframe.user_extend_company ur on ur.user_id = u.id where u.id='".$_SESSION['userId']."'";
                    $is_zgs = $this->db()->getAll($sql);
                    //如果当前用户不是总公司这不能看到成本；
                    if(!empty($is_zgs)) {
                        $rsc = array_column($is_zgs, 'company_id');
                        if(!in_array('58', $rsc)){
                            $val['chengbenprice'] = '';
                        }
                    }else{
                        $val['chengbenprice'] = '';
                    }
                    //是否可取订单
                    $sql = "select wg.order_goods_id,sc.company_id,wg.company_id as w_id from app_order.base_order_info oi 
                    inner join app_order.app_order_details `od` on `oi`.id = od.order_id
                    inner join `cuteframe`.`sales_channels` AS `sc` on `sc`.`id` = `oi`.`department_id`
                    left join warehouse_shipping.warehouse_goods `wg` on wg.order_goods_id = convert(od.id,CHAR) where oi.id =".$val['order_id'];
                    $res = $this->db()->getAll($sql);
                    $is_kequ = true;
                    if(!empty($res)){
                        foreach ($res as $v_ts) {
                            if(!$v_ts['order_goods_id'] || $v_ts['company_id'] != $v_ts['w_id']){
                                $is_kequ = false;
                            } 
                        }
                    }else{
                        $is_kequ = false;
                    }
                    $val['is_kequstr'] = $is_kequ == true?'可取':'不可取';
                    //数量
                    $sql = "select sum(`goods_count`) from `app_order`.`app_order_details` where `order_id` = ".$val['order_id'];
                    $val['goods_count'] = $this->db()->getOne($sql);
                }elseif($where['export_type'] == 'fahuo'){
                    //证书类型
                    //$val['dia_type']=$val['kuan_sn']?"天生一对":isset($gem[$val['zhengshuhao']])?$gem[$val['zhengshuhao']]:'';
                    //是否特价钻
                    if($val['goods_type']=='lz'){
                        $sql = "SELECT `special_price` FROM `front`.`diamond_ssy_tejia20151111` WHERE `cert_id`='".$val['zhengshuhao']."'";
                        $val['special_price'] = $this->db()->getOne($sql);
                        $val['zhengshuhao']=str_replace("GIA","",$val['zhengshuhao']);
                    }else{
                        $val['special_price']='';
                    }
                    //以旧换新
                    if(SYS_SCOPE == 'zhanting'){
                        //272：以旧换新/转单 252：经销商渠道协作收款 321：跨渠道协作收款
                        $huanxing = array('272','252','321');
                    }else{
                        //272:以旧换新/转单  252：经销商渠道协作收款 320：以旧换新-补差额
                        $huanxing = array('272','252','320');
                    }
                    //是否以旧换新
                    $is_huanxin = '否';
                    $is_xiezuo_to = '否';//是否协作收款
                    $order_sn_ts =$val['order_sn'];
                    if(isset($orderPayAll[$order_sn_ts]) && !empty($orderPayAll[$order_sn_ts])){
                        $t_orderPay = $orderPayAll[$order_sn_ts];
                        if(is_array($t_orderPay)){
                            foreach ($t_orderPay as $v_type) {
                                if(in_array($v_type, $huanxing)){
                                    $is_huanxin = '是';
                                    if($v_type != '272'){
                                        $is_xiezuo_to = '是';
                                    }
                                }
                            }
                        }
                    }
                    //退款流水
                    $tuikuan_no = array();
                    $val['tuikuan_str'] = '';
                    //2、点款方式为以旧换新/转单时，如果流水号找不到对应的退款单，【点款流水号】为实际流水号，【是否以旧换新订单】为【是】，退货金额为0
                    $tuihuo_price = 0;//退货金额
                    if(isset($orderPayZdAll[$order_sn_ts]) && !empty($orderPayZdAll[$order_sn_ts])){
                        $tuihuoInfo = $orderPayZdAll[$order_sn_ts];
                        
                        if(!empty($tuihuoInfo)){
                           foreach ($tuihuoInfo as $tui) {
                                if($tui){
                                    $tuikuan_no[] = $tui;
                                }
                           }
                        }
                        $tuikuan_no =array_filter($tuikuan_no);
                    }
                    //1、点款方式为经销商渠道协作收款、跨渠道协作收款，【点款流水号】为空，【是否以旧换新订单】为【是】，退货商品金额就是点款金额
                    ////3、点款方式为以旧换新/转单时，如果流水号对应的退款单生成了销售退货单，【点款流水号】为实际流水号，【是否以旧换新订单】为【是】，退货商品金额=退货商品成交价
                    if($is_xiezuo_to == '是' && empty($tuikuan_no) && $is_huanxin == '是'){
                        $tuihuo_price = isset($orderDepositAll[$order_sn_ts])?array_sum($orderDepositAll[$order_sn_ts]):0;
                    }elseif(!empty($tuikuan_no) && $is_huanxin == '是'){
                        $val['tuikuan_str'] = implode('|', $tuikuan_no);
                        //根据退款号查老订单号
                        $tmp_price = 0;
                        foreach ($tuikuan_no as $ky => $id) {
                            $sql = "select order_sn from `app_order`.`app_return_goods` where return_id ='".$id."'";
                            $old_order_sn = $this->db()->getOne($sql);
                            $sql = "select count(*) from warehouse_shipping.warehouse_bill `wb` where wb.order_sn = '{$old_order_sn}' and wb.bill_type = 'D' and wb.bill_status = 2";
                            $is_D = $this->db()->getOne($sql);
                            //是否生成销售退货单
                            if($is_D>0){
                                $sql = "SELECT IF(`od`.`favorable_status` = 3,`od`.`goods_price`-`od`.`favorable_price`,`od`.`goods_price`) as price FROM app_order.`app_return_goods` rg inner join app_order.app_order_details od on od.id = rg.order_goods_id where rg.return_id ='".$id."'";
                                $tmp_price += $this->db()->getOne($sql);
                            }
                        }
                        $tuihuo_price = $tmp_price;
                    }
                    $tuihuo_price = round($tuihuo_price*($val['percent']/100));
                    //实际回款金额：
                    //如果点款方式非经销商渠道协作收款、跨渠道协作收款、旧换新/转单时，实际回款金额=成交价
                    //如果点款方式为经销商渠道协作收款、跨渠道协作收款、旧换新/转单时，实际回款金额=成交价-退货商品金额
                    $val['real_hk_price'] = empty($val['goods_price'])?0:$val['goods_price'];//实际回款金额
                    if($is_huanxin == '是'){
                        $val['real_hk_price'] = bcsub($val['real_hk_price'],$tuihuo_price,2);
                        if($val['real_hk_price'] < 0){
                            $val['real_hk_price'] = 0;
                        }
                    }
                    $val['is_huanxin'] = $is_huanxin;
                    $val['tuihuo_price'] = $tuihuo_price;
                }elseif($where['export_type'] == 'zuantui'){
                    if($val['return_type'] == 1){
                        $val['return_type']="转单";
                    }else{
                        $val['return_type']="退款";
                    }
                    //取出退款申请人所属渠道
                    $val['apply_channel'] = '';
                    if($val['apply_user_id'] != ''){
                        //$sql = "select sc.`channel_name` from cuteframe.`user_channel` uc left join `cuteframe`.`sales_channels` sc on sc.`id` = uc.channel_id where uc.user_id = ".$val['apply_user_id']." order by sc.id desc limit 1";
                        $sql = "select company_id from cuteframe.user where id = ".$val['apply_user_id'];
                        $companyId = $this->db()->getOne($sql);
                        $res_all = array();
                        $res = array();
                        if($companyId){
                            $sql = "select id from cuteframe.sales_channels where company_id =".$companyId;
                            $res_all = $this->db()->getAll($sql);
                            if(!empty($res_all)) $res_all = array_column($res_all,'id');
                        }
                        $sql = "SELECT c.id FROM cuteframe.`user_channel` AS `m` 
                        INNER JOIN cuteframe.`sales_channels` AS `c` ON `c`.`id`=`m`.`channel_id` 
                        where m.user_id='".$val['apply_user_id']."'";
                        $res = $this->db()->getAll($sql);
                        if(!empty($res)) $res = array_column($res,'id');
                        $R = array_intersect($res_all,$res);
                        if(!empty($R)){
                            $pos = array_search(max($R), $R);
                            $sql = "select channel_name,company_id from cuteframe.sales_channels where id =".$R[$pos];
                            $salesChannels = $this->db()->getRow($sql);
                            $val['apply_channel'] = isset($salesChannels['channel_name'])?$salesChannels['channel_name']:'';
                            $val['company_id'] = isset($salesChannels['company_id'])?$salesChannels['company_id']:'';
                        }
                    }
                    //证书号
                    if($val['cert']==""){
                        $sql ="select zhengshuleibie from warehouse_shipping.warehouse_goods where goods_id='".$val['goods_id']."' and zhengshuleibie is not null";
                        $val['cert'] = $this->db()->getOne($sql);
                    }
                    //数量
                    $sql = "select sum(`goods_count`) from `app_order`.`app_order_details` where `order_id` = ".$val['order_id'];
                    $val['goods_count'] = $this->db()->getOne($sql);
                    //退款核算金额
                    $return_account_price = 0;
                    $apply_return_amount = 0;
                    $return_by = $val['return_bys'];
                    $goods_price = $val['goods_price1'];
                    if($return_by == 2){
                        $return_account_price = $val['apply_return_amount'];
                    }else{
                        if(!empty($val['order_goods_id'])){
                            $sql = "select apply_return_amount,return_by from `app_order`.`app_return_goods` where order_goods_id = ".$val['order_goods_id']."";
                            $return = $this->db()->getAll($sql);
                            if(!empty($return)){
                                $is_return_by = false;
                                foreach ($return as $key => $value) {
                                    $apply_return_amount = bcadd($apply_return_amount, $value['apply_return_amount'], 2);
                                    if($value['return_by'] == 2){
                                        $is_return_by = true;
                                    }
                                }
                                if($is_return_by){
                                    $return_account_price = bcsub($goods_price, $apply_return_amount, 2); 
                                }else{
                                    $return_account_price = $goods_price; 
                                }
                                
                            }else{
                                $return_account_price = $goods_price; 
                            }
                        }
                        
                    }
                    $val['goods_price'] = $return_account_price;
                }
                //一件商品销了两件货品 ，其中一件货品的 原价 成交价 实际回款价格 置为0 2017年12月4日
                if(in_array($val['detail_id'],$detailsid)){
                    $val['market_price'] = 0;
                    $val['goods_price'] = 0;
                    $val['goods_price1'] = 0;
                    if($where['export_type'] == 'fahuo') $val['real_hk_price'] = 0;
                }
                array_push($detailsid, $val['detail_id']);
                $returnData[] = $val;
            }
            $returnData['detailsid'] = $detailsid;
            unset($orderPayAll,$orderPayZdAll,$orderDepositAll,$gem,$xilie);
        }
        return $returnData;
    }

    //分页
    public function getPageList($sql, $params = array(), $page = 1, $pageSize = 20, $useCache = false)
    {
        try {
            $countSql = "SELECT COUNT(*) as count FROM (" . $sql . ") AS b";
            $data['pageSize'] = (int)$pageSize < 1 ? 20 : (int)$pageSize;
            $data['recordCount'] = $this->db()->getOne($countSql, $params, $useCache);
            $data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
            $data['page'] = $data['pageCount'] == 0 ? 0 : ((int)$page < 1 ? 1 : (int)$page);
            $data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
            $data['isFirst'] = $data['page'] > 1 ? false : true;
            $data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
            $data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] +   1;
            $data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
            $data['data'] = $this->db()->query($data['sql'], $params, $useCache);
        }
        catch (exception $e) {
            throw $e;
        }
        return $data;
    }

    //获取所有的天生一对特殊款
    private function getTsydSpecial()
    {
        $stylelist = array();
        $sql = "select style_sn from front.app_tsyd_special";
        $stylelist = $this->db()->getAll($sql);
        if(!empty($stylelist)){
            return array_column($stylelist,"style_sn");
        }
        return $stylelist;
    }

    //抓取所有关联证书号的裸石记录
    public function getTsydInfo($where)
    {
        $style_info = $this->getTsydSpecial();

        $sql = $this->getsql($where);
        $info = $this->db()->getAll($sql);
        $zhengshuhaoList = array();
        if(!empty($info)){
            foreach ($info as $key => $val) {
                $is_tsyd_special = '';
                if(in_array($val['goods_sn'],$style_info)){
                    $is_tsyd_special = '是';
                }
                //② 如果“天生一对特殊款”列为“是”，那么此款的镶嵌要求如果是“需工厂镶嵌”并且证书号列有值，
                //那么找到此钻石的销售记录，钻石的“天生一对特殊款”列也是“是”
                if($is_tsyd_special == '是' && $val['xiangqian'] == '需工厂镶嵌' && !empty($val['zhengshuhao'])){
                    $sql = "select id from app_order.app_order_details where zhengshuhao = '".$val['zhengshuhao']."' and id <>".$val['detail_id'];
                    $zhengshuhaoList[] = $this->db()->getOne($sql);
                }
            }
        }
        return $zhengshuhaoList;
    }
}

?>