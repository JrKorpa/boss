<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 22:25:34
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_goods';
        $this->_dataObject = array("id"=>" ",
			"goods_id"=>"货号",
			"goods_sn"=>"款号",
			"is_on_sale"=>"货品状态（见数据字典，货品状态）",
			"num"=>"",
			"warehouse"=>"所在仓库",
			"company"=>"所在公司",
			"company_id"=>"所在公司ID",
			"storage_mode"=>"入库方式",
			"product_type"=>"产品线",
			"cat_type"=>"款式分类",
			"caizhi"=>"主成色(材质)",
			"jinzhong"=>"主成色重（金重）",
			"shoucun" => "手寸",
			"jinhao"=>"金耗",
			"chengbenjia"=>"成本价",
			"mingyichengben"=>"名义成本价",
			"yuanshichengbenjia"=>"原始成本价",
			"goods_name"=>"商品名称",
			"addtime"=>"添加时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ApplicationController/search
	 */
	
	public function commonwhere($box_id="")
	{
		//柜位号--如果搜索条件【仓库】有值，搜索出来的货号要同时符合仓库和柜位号
		//box_sn 这个是柜位号，而不是 box_id
		$str = "";
		if($box_id !== "")
		{
			$str .= "box_sn='".$box_id."' AND ";
		}
		return $str;
	}
	

    function getWhere($where){       
		$str = "";
		if($where['goods_id'] != "")
		{

			/*
			//add by zhangruiying 去除用户不小心输入或粘贴的空白字符和中文,号替换
			$where['goods_id']=preg_replace("/[sv]+/",'',$where['goods_id']);
			$where['goods_id']=str_replace(" ",',',$where['goods_id']);
			$where['goods_id']=str_replace("，",',',$where['goods_id']);
			//add end
			$item =explode(",",$where['goods_id']);
			$goodsid = "";
			foreach($item as $key => $val) {
				if ($val != '') {
					if($goodsid){
						$goodsid .= ",'".trim($val)."'";
					}else{
						$goodsid .= "'".trim($val)."'";
					}
				}
			}
			*/

            $goodsid = $this->setFilter($where['goods_id']);
			$where['goods_id'] = $goodsid;
			//$str .= " goods_id like \"%".addslashes($where['goods_id'])."%\"  AND ";
			$str .= " g.goods_id in (".$where['goods_id'].") AND ";
		}
		if($where['style_sn'] != "")
		{
                        $str .= $this->formatString($where['style_sn'],'g.goods_sn');

			//$str .= " goods_sn = '".$where['style_sn']."' AND ";
		}
		if($where['put_in_type'] !== "")
		{
			$str .= "g.put_in_type = '".$where['put_in_type']."' AND ";
		}
		if($where['weixiu_status'] !== "")
		{
			$str .= "g.weixiu_status = '".$where['weixiu_status']."' AND ";
		}
		if($where['is_on_sale'] !== "")
		{
			$str .= "g.is_on_sale = ".$where['is_on_sale']." AND ";
		}
	    
	    if($where['caizhi'] !="" && $where['jinse'] !== ""){
		    $zhuchengse = $where['caizhi'].str_replace("无","",$where['jinse']);
		    $str .= "g.caizhi = '".$zhuchengse."' AND ";
		}else if($where['caizhi'] !== "" && $where['jinse'] == ""){		    
			$str .= "g.caizhi like '".$where['caizhi']."%' AND ";
		}else if($where['caizhi'] =="" && $where['jinse'] !== ""){
		    $str .= "g.caizhi like '%".$where['jinse']."' AND ";
		}
        
		if($where['company_id_list'] != ''){
            $str .= "g.company_id = '".$where['company_id_list']."' AND ";
        }else{
            if($where['company_id'] !== "")
            {
                $str .= "g.company_id = '".$where['company_id']."' AND ";
            }
        }
		if($where['cat_type'] !== "" && $where['cat_type'] != "全部")
		{
			$str .= "g.cat_type = '".$where['cat_type']."' AND ";
		}
		if($where['cat_type1'] !== "" && $where['cat_type1'] != "全部")
		{
			$str .= "g.cat_type1 = '".$where['cat_type1']."' AND ";
		}
		
		if($where['warehouse_id'] !== "")
		{
			$str .= "g.warehouse_id = '".$where['warehouse_id']."' AND ";
		}
		if($_SESSION['userName']=='lily'){
			$str .= "g.warehouse_id in (653,695) AND ";
		}
		//柜位号--如果搜索条件【仓库】有值，搜索出来的货号要同时符合仓库和柜位号
		//box_sn 这个是柜位号，而不是 box_id
		$str .= $this -> commonwhere($where['box_id']);
		//new add
		if($where['zhengshuhao'] !== "")
		{

            $zhengshuhao = $this->setFilter($where['zhengshuhao']);
            $where['zhengshuhao'] = $zhengshuhao;
            //$str .= " goods_id like \"%".addslashes($where['goods_id'])."%\"  AND ";
            $str .= " g.zhengshuhao in (".$where['zhengshuhao'].") AND ";
			//$str .= "g.zhengshuhao = '".$where['zhengshuhao']."' AND ";

		}
		if($where['zhengshuhao2'] !== "")
		{
			$str .= "g.zhengshuhao2 = '".$where['zhengshuhao2']."' AND ";
		}

		if ($where['order_goods_ids'] !== '')
		{
			if($where['order_goods_ids'] == '1'){
				$str .= " g.order_goods_id <> 0 AND ";//已经绑定
			}else{
				$str .= " (g.order_goods_id = '0' or g.order_goods_id='') AND ";//未绑定
			}
		}
		if($where['shoucun'] !== '')
		{
			$str .= " g.shoucun = '".$where['shoucun']."' AND ";
		}
		if($where['pinpai'] !== '')
		{
            $pinpai = $this->setFilter($where['pinpai']);
            $where['pinpai'] = $pinpai;
            //$str .= " goods_id like \"%".addslashes($where['goods_id'])."%\"  AND ";
            $str .= " g.pinpai in (".$where['pinpai'].") AND ";
			//$str .= " g.pinpai = '".$where['pinpai']."' AND ";
		}

		 if($where['luozuanzhengshu'] !== '')
		{
			$str .= " g.luozuanzhengshu = '".$where['luozuanzhengshu']."' AND ";
		}

		if($where['xilie_name']){

			$str .= " g.goods_sn IN  (".$where['xilie_name'].") AND ";

		}
                
		if ($where['kucunstart'] !== '')
		{
			$str .= " g.addtime >= '".$where['kucunstart']." 00:00:00' AND ";
		}
		if ($where['kucunend'] !== '')
		{
			$str .= " g.addtime <= '".$where['kucunend']." 23:59:59' AND ";
		}
		if($where['processor'] !== '')
		{
			$str .= " g.prc_id='".$where['processor']."' AND ";
		}
		if ($where['buchan'] !== '')
		{
			$str .= " g.buchan_sn='".$where['buchan']."' AND ";
		}
		if ($where['mohao'] !== '')
		{
			$str .= "g.mo_sn='".$where['mohao']."' AND ";
		}
		if ($where['zhushi'] !== '')
		{
			$str .= " g.zhushi='".$where['zhushi']."' AND ";
		}
		if ($where['zhengshuleibie'] !== '')
		{
			$str .= " g.zhengshuleibie='".$where['zhengshuleibie']."' AND ";
		}
		//差一个主成色重
		if($where['jinzhong_begin'] !== "") {
			$str .= " g.jinzhong >=".$where['jinzhong_begin']." AND ";
		}
		if($where['jinzhong_end'] !== "") {
			$str .= " g.jinzhong <=".$where['jinzhong_end']." AND ";
		}
        //3、商品列表搜索也增加 总金重 搜索
        if($where['total_jinzhong'] !== "")
        {
            $str .= " (g.jinzhong+g.peijianjinchong) = '".$where['total_jinzhong']."' AND ";
        }
		if($where['zs_color'] !== "")
		{
			$str .= " g.zhushiyanse = '".$where['zs_color']."' AND ";
		}
		if ($where['zs_clarity'] !== "")
		{
			$str .= " g.zhushijingdu = '".$where['zs_clarity']."' AND ";
		}
		if ($where['jinshi_type'] !== "") {
			$str .= " g.ziyin='".$where['jinshi_type']."' AND ";
		}
		if ($where['jintuo_type'] !== "")
		{
			$str .= " g.tuo_type='".$where['jintuo_type']."' AND ";
		}
		if ($where['jiejia'] !== "")
		{
			$str .= " g.jiejia = '".$where['jiejia']."' AND ";
		}
		if($where['guiwei'] == '1')
		{
			$str .= " ( g.box_sn = '' OR g.box_sn = '0-00-0-0') AND ";
		}elseif($where['guiwei'] == '2')
		{
			$str .= " ( g.box_sn != '' AND g.box_sn != '0-00-0-0') AND ";
		}
		if ($where['chanpinxian'] !== "")
		{
			$str .= "g.product_type='".$where['chanpinxian']."' AND ";
		}
		if ($where['chanpinxian1'] !== "" && $where['chanpinxian1'] != "全部")
		{
			$str .= "g.product_type1='".$where['chanpinxian1']."' AND ";
		}
		if ($where['zhushi_begin'] !== "") {
			$str .= "g.zuanshidaxiao >= '".$where['zhushi_begin']."' AND ";
		}
		if ($where['zhushi_end'] !== "") {
			$str .= "g.zuanshidaxiao <= '".$where['zhushi_end']."' AND ";
		}
		if($where['weixiu_company_id'] !== "")
		{
			$str .= "g.weixiu_company_id = '".$where['weixiu_company_id']."' AND ";
		}
		if($where['weixiu_warehouse_id'] !== "")
		{
			$str .= "g.weixiu_warehouse_id = '".$where['weixiu_warehouse_id']."' AND ";
		}
		
		if($where['xinyaozhanshi'] !== "")
		{
			if($where['xinyaozhanshi']==2){
				$str .= "(g.xinyaozhanshi = '".$where['xinyaozhanshi']."' or g.zhengshuleibie='HRD-S')  AND ";
			}else{
				$str .= "g.xinyaozhanshi = '".$where['xinyaozhanshi']."' AND ";
			}
			
		}

        if(isset($where['hidden']) && $where['hidden'] != ''){
            $str .= " g.hidden = ".$where['hidden']." AND " ;
        }

        return $str;
    }



	function pageList ($where,$page,$pageSize=10,$useCache=true , $is_down = false)
	{
		$time = date('Y-m-d H:i:s');
		// $sql = "SELECT *,TIMESTAMPDIFF(DAY,change_time,if(chuku_time,chuku_time,'$time')) as 'thisage',TIMESTAMPDIFF(DAY, addtime,if(chuku_time,chuku_time,'$time'))  as 'allage' FROM `".$this->table()."`";
		// 2015-11-04改成读总库龄 /本库库龄 改成 warehous_goods_age 这个表的值
		$sql = "SELECT `g`.* FROM `".$this->table()."` As `g` ";
		
        $str=$this->getWhere($where);

		//此判断不要轻易动，有问题找张丽娟。。。。
		if(!in_array($_SESSION['userName'],array('董小华','张敏敏','admin')))
		{
			$str .= " g.goods_id not in(150409528627,150409528626,150409528625,150409528624,150409528623,150409528622,150409528621,150409528620,150409528619,150409528618,150409528617,150409528616,150409528615,150416530793,150416530794,150416530795,150416530796,150416530797,150416530798,150416530799,150416530800,150416530801,150416530802,150416530803,150416530804,150416530805,150416530806,150416530807,150416530808,150416530809,150429540286,150429540287,150429540288,150429540289,150429540290,150429540291,150429540292,150429540293,150429540294,150429540295,150429540296,150429540297,150429540298,150429540299,150429540300,150429540301,150429540302,150429540303,150429540304,150429540305,150429540306,150429540307,150429540308,150429540309)";
		}

		$sqltj = "SELECT count(*) as zong_num,sum(`mingyichengben`) as zong_chengbenjia FROM `".$this->table()."` as g";
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
			$sqltj .=" WHERE ".$str;
		}
		if(isset($where['orderby']) and $where['orderby'] !== "" and isset($where['desc_or_asc']) and $where['desc_or_asc'] !== "")
		{
			$sql.= " ORDER BY {$where['orderby']} {$where['desc_or_asc']}";
		}
		else if(isset($where['orderby']) and $where['orderby'] !== "" )
		{
			$sql.= " ORDER BY {$where['orderby']}";
		}
		else
		{
			$SelfUserWarehouseModel=new SelfUserWarehouseModel(1);
			$DeptUserArr=$SelfUserWarehouseModel->getDeptUser();
			if(in_array($_SESSION['userName'], $DeptUserArr)){
				$sql .= " ORDER BY g.id DESC";
			}else{
			    $sql .= " ORDER BY g.addtime ASC";
			}
		}
		//$sql .= " ORDER BY id DESC";
		//echo $sql;die;
		$tongji = $this->db()->getRow($sqltj);
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
        $goods_list = array_column($data['data'], 'goods_id');
        if (!empty($goods_list)) {
            //TODO: 计算本库库龄
            $goods_ids_str = implode(",", $goods_list);
            $sql = 
"SELECT g.goods_id, (select in_time from goods_io i where i.goods_id = g.goods_id and i.warehouse_id = g.warehouse_id order by in_time desc limit 1) as thisage,`a`.`last_onshelf_dt`,`a`.`is_kuanprice`,`a`.`kuanprice`,`a`.`style_kuanprice_id`, s.xilie from warehouse_goods g
left join warehouse_goods_age a on a.goods_id = g.goods_id
left join front.base_style_info s on s.style_sn = g.goods_sn
where g.goods_id in ({$goods_ids_str});";
            $this_ages = $this->db()->getAll($sql);
            $now = time();
            foreach ($data['data'] as $rk => $row) {
                foreach ($this_ages as $k => $g) {
                    if ($g['goods_id'] == $row['goods_id']) {
                        $data['data'][$rk]['allage'] = ceil(($now-strtotime($data['data'][$rk]['addtime']))/(3600*24));
                        $data['data'][$rk]['thisage'] = $g['thisage'] == 0 || empty($g['thisage']) ? 0 : ceil(($now-strtotime($g['thisage']))/(3600*24));
                        $data['data'][$rk]['last_onshelf_dt'] = $g['last_onshelf_dt'];
                        $data['data'][$rk]['is_kuanprice'] = $g['is_kuanprice'];
                        $data['data'][$rk]['kuanprice'] = $g['kuanprice'];
                        $data['data'][$rk]['style_kuanprice_id'] = $g['style_kuanprice_id'];
                        $data['data'][$rk]['xilie'] = $g['xilie'];
                        unset($this_ages[$k]);
                        break;
                    }
                }
            }
        }
		$data['tongji']=$tongji;
		return $data;
	}

    function getDownloadDataCount($where){
    	$sql="select count(goods_id) from warehouse_goods as g where 1=1 AND ";
    	$sql.=$this->getWhere($where);
    	$sql = rtrim($sql,"AND ");
    	
    	$count=$this->db()->getOne($sql ,array());
    	return $count;
    }

    function getDownloadData($where,$page=1,$pageSize=5000){
 		$sql = "SELECT `g`.*,
			ceil((UNIX_TIMESTAMP( NOW( ) ) - UNIX_TIMESTAMP( g.`addtime` ) ) / ( 24 *3600 )) AS `allage`,
			case when i.in_time is null then 0 else ceil((UNIX_TIMESTAMP( NOW( ) ) - UNIX_TIMESTAMP( i.in_time ) ) / ( 24 *3600 )) end as thisage,
			`a`.`last_onshelf_dt`,`a`.`is_kuanprice`,`a`.`kuanprice`,`a`.`style_kuanprice_id`, s.xilie FROM `".$this->table()."` As `g` 
            LEFT JOIN `warehouse_goods_age` AS `a` ON `g`.`goods_id`=`a`.`goods_id`
			left join (
				select goods_id, warehouse_id, max(in_time) in_time from goods_io where in_time is not null group by goods_id, warehouse_id 
			) i on i.goods_id = g.goods_id and i.warehouse_id = g.warehouse_id
		    left join front.base_style_info s on s.style_sn = g.goods_sn where 1=1 AND ";		
 		//$sql = "SELECT `g`.goods_id,`g`.goods_sn FROM `".$this->table()."` As `g` LEFT JOIN `warehouse_goods_age` AS `a` ON `g`.`goods_id`=`a`.`goods_id`
		//   left join front.base_style_info s on s.style_sn = g.goods_sn where ";	
        $sql.=$this->getWhere($where);
        $sql = rtrim($sql,"AND ");

        if($page<=0)
        	$page=1;
        $start= ($page-1)*$pageSize +1;
        $sql.= " limit ". ($page-1)*$pageSize .",$pageSize";	 
        //echo $sql;
        $data = $this->db()->getAll($sql,array());
        return $data;
    }


    function GetSupplierList($arr = array())
	{
		$ret = ApiModel::pro_api('GetSupplierList',$arr);
		return $ret['return_msg']['data'];
	}
	
	function GetGoodsbyGoodid($goodid)
	{
		$sql = "SELECT * FROM `warehouse_goods` WHERE goods_id = '$goodid' ";
		$ret = $this->db()->getRow($sql);
        
		return $ret;
	}

	function _getGoodsId(){
		//$goods_id = date("yms").rand(10,99).rand(10,99).rand(10,99);
		//return $goods_id;
		//预防并发时写入货号重复添加写入锁
		/*
		$sql="select max(CAST(goods_id AS UNSIGNED)) as num from `warehouse_goods`";
		$num=$this->db()->getOne($sql);
		return $num;
		*/
	    switch (SYS_SCOPE) {
	        case 'boss':
	            if(defined('IS_ZHOUSHAN_SYS') && IS_ZHOUSHAN_SYS=='YES')
                    return  '6'.date('ymdHi', time()).str_pad (rand(0, 999), 4, '0', STR_PAD_LEFT);
	            else  
	                return  date('ymdHi', time()).str_pad (rand(0, 999), 4, '0', STR_PAD_LEFT);
	        case 'zhanting':
	            return '9'.date('ymdHi', time()).str_pad (rand(0, 999), 4, '0', STR_PAD_LEFT);
	        default:
	            die();
	    }
	}

	/*
	 * 根据货号 查询货品信息
	 * @param 货号
	 **/
	public function getGoodsByGoods_id($goods_id){
		$sql = "SELECT * FROM `warehouse_goods` WHERE `goods_id`='{$goods_id}'";
		return $this->db()->getRow($sql);
	}

	/*
	 * 根据货号 查询货品信息
	 * @param 货号
	 **/
	public function getGoodsAgeByGoods_id($goods_id){
		$sql = "SELECT * FROM `warehouse_goods_age` WHERE `goods_id`='{$goods_id}'";
		return $this->db()->getRow($sql);
	}

	/**
	 * 查询货物信息（带上加价率的，适用于Y单）
	 * @param  int $goods_id 商品id
	 * @return ary           数据
	 */
	public function getGoodsWithRate($goods_id){
		$sql = "SELECT `a`.*, `b`.`jiajialv` as jiajialv_y FROM `warehouse_goods` a, `warehouse_bill_goods` b WHERE 
				a.goods_id = b.goods_id AND 
				`a`.`goods_id`='{$goods_id}'";
		return $this->db()->getRow($sql);
	}

	/** 根据货号，查询warehouse_goods 仓库里的货品信息
	所有单据输入货号调取单据信息都用此方法。
	**/
	public function getGoodsInfoByGoodsID($goods_id){
		$sql = "SELECT `goods_id` , `put_in_type` , `jiejia` , `goods_sn` , `jinzhong` , `zhushilishu` , `zuanshidaxiao` ,`yuanshichengbenjia`, `chengbenjia` , `fushilishu` , `fushizhong` , `yanse` , `zhengshuhao` , `goods_name` , `jingdu` , `is_on_sale`,`company_id`, `order_goods_id`,`zhushimairuchengben`,`mingyichengben`,`changdu`,`shoucun`,`caizhi`,`zhushi`,`fushi`,`zongzhong`,`weixiu_status` FROM `warehouse_goods` WHERE `goods_id`='{$goods_id}' LIMIT 1";

		return $this->db()->getRow($sql);
	}

	/** 写入日志  **/
	public function InsertLog($goods_id,$info){
		$user = $_SESSION['userName'];
		$time = date('Y-m-d H:i:s');
		$sql = "INSERT INTO `warehouse_goods_update_log` (`info`,`update_user`,`update_time`,`goods_id`) VALUES ('{$info}','{$user}','{$time}','{$goods_id}')";
		$this->db()->query($sql);
	}

	function pageListByLog ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `warehouse_goods_update_log` WHERE `goods_id` = '{$where['goods_id']}'  ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 *根据仓库id， 获取该仓库下所有的货品
	 * @param $warehouse_id Int 仓库ID
	 * @param $goods_status Int 货品状态 默认0:所有状态   其他状态参考数字字典[货品状态-仓储管理]
	 **/
	public function getGoodsByWarehouse($warehouse_id, $goods_status = 0 ){
		$sql = "SELECT `goods_id`,`goods_sn`,`goods_name`,`num` FROM `warehouse_goods` WHERE `warehouse_id` = {$warehouse_id}";
		if($goods_status){
			$sql .= " AND `is_on_sale` = {$goods_status}";
		}
		return $this->db()->getAll($sql);
	}

	/** 获取货品的库存状态 **/
	public function getKuCunStatus($goods_id){
		$sql = "SELECT `is_on_sale` FROM `warehouse_goods` WHERE `goods_id`= '{$goods_id}'";
		return $this->db()->getOne($sql);
	}

	/** 获取货品的 order_goods_id (该字段是判断货品是否被下单，下单之后，订单的明细里会锁定仓库的的货，将订单明细的对应id，存入该字段) **/
	public function getOrderGoodsId($goods_id){
		$sql = "SELECT `order_goods_id` FROM `warehouse_goods` WHERE `goods_id` = '{$goods_id}'";
		return $this->db()->getOne($sql);
	}

	/** 根据货号，解绑货品 **/
	public function jiebang($goods_id){
		$sql = "UPDATE `warehouse_goods` SET `order_goods_id` = 0 WHERE `goods_id` = '{$goods_id}' ";
		return $this->db()->query($sql);
	}

	/** 根据订单明细ID 解绑期货货号，绑定另外一个货号 **/
	public function build_goods($detail_id,$goods_id){
		$sql1 = "UPDATE `warehouse_goods` SET `order_goods_id` = 0 WHERE `order_goods_id` = '{$detail_id}' ";
		$this->db()->query($sql1);
		$sql = "UPDATE `warehouse_goods` SET `order_goods_id` = '{$detail_id}' WHERE `goods_id` = '{$goods_id}' ";
		return $this->db()->query($sql);
	}
        /**
         * 批量的时候整合批量输入的字符串
         */
        public function formatString($str,$ziduan=''){

            $str = preg_replace("/[sv]+/",'',$str);
            $str = str_replace(" ",',',$str);
            $str = str_replace("，",',',$str);
            //add end
            $item =explode(",",$str);
            $goodsid = "";
            foreach($item as $key => $val) {
                if ($val != '') {
                        if($goodsid){
                                $goodsid .= ",'".trim($val)."'";
                        }else{
                                $goodsid .= "'".trim($val)."'";
                        }
                }
            }
            //echo $str;exit;
            $str = $goodsid;
            //$str .= " goods_id like \"%".addslashes($where['goods_id'])."%\"  AND ";
            $str = " $ziduan in (".$str.") AND ";

            //exit($str);
            return $str;
        }

    /**
     * 批量的时候整合批量输入的字符串（针对分组导出）
     */
    public function formatStringFenzu($str,$ziduan=''){

        $str = preg_replace("/[sv]+/",'',$str);
        $str = str_replace(" ",',',$str);
        $str = str_replace("，",',',$str);
        //add end
        $item =explode(",",$str);
        $goodsid = "";
        foreach($item as $key => $val) {
            if ($val != '') {
                    if($goodsid){
                            $goodsid .= ",'".trim($val)."'";
                    }else{
                            $goodsid .= "'".trim($val)."'";
                    }
            }
        }
        //echo $str;exit;
        $str = $goodsid;
        //$str .= " goods_id like \"%".addslashes($where['goods_id'])."%\"  AND ";
        $str = " `wg`.$ziduan in (".$str.") AND ";

        //exit($str);
        return $str;
    }

	/**
	* 普通查询
	* @param $fields string 要查询的字段
	* @param $where string 要查询的条件
	* @param $is_all Int 1取单个值 2/取一条记录 3/多条记录
	**/
	public function select2($fields, $where , $is_all = 1){
		$sql = "SELECT {$fields} FROM `warehouse_goods` WHERE {$where} ORDER BY `id` DESC";
		//if($_SESSION['userName'] == 'admin'){echo $sql;exit;}
		if($is_all == 1){
			return $this->db()->getOne($sql);
		}else if($is_all == 2){
			return $this->db()->getRow($sql);
		}else if($is_all == 3){
			return $this->db()->getAll($sql);
		}
	}

	public function getIdBySN($goods_id){
		$sql = "SELECT `id` FROM `".$this->table()."` WHERE `goods_id` = ".$goods_id;
		return $this->db()->getOne($sql);
	}

	public function filterGids($goods_sns){
		$goods_sns =array_filter($goods_sns);

		$sql = "SELECT `id` FROM `warehouse_goods` WHERE `goods_id` = ? AND `is_on_sale` = '2'";
		$stmt = $this->db()->db()->prepare($sql);
		$data = ['success'=>array(),'error'=>array()];
		foreach ($goods_sns as $g) {
			if($stmt->execute([$g])){
				$res = $stmt->fetch(PDO::FETCH_NUM);
				if(!empty($res)){
					$data['success'][] = $res[0];
				}else{
					$data['error'][] = $g;
				}
			}
		}
		return $data;
	}

	public function goBackOldSYS($ids){

		$old_conf = [
			'dsn'=>"mysql:host=192.168.1.61;dbname=jxc",
			'user'=>"mark",
			'password'=>"zaq1xsw2",
		];
		$db_pdo = new PDO($old_conf['dsn'], $old_conf['user'], $old_conf['password'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));

		$data = ['success'=>array(),'error'=>array()];

		foreach ($ids as $id) {
			$model = new WarehouseGoodsModel($id,21);
			$goods_id = $model->getValue('goods_id');
			$company = $model->getValue('company_id');
			$warehouse = $model->getValue('warehouse_id');

			$sql = "SELECT `goods_id`,`is_on_sale`,`order_goods_id` FROM `jxc_goods` WHERE `goods_id` = '".$goods_id."'";
			$obj = $db_pdo->query($sql);
			$row = $obj->fetch(PDO::FETCH_ASSOC);

			if(empty($row)){
				$newdo = $model->getDataObject();

				$newdo['storage_mode'] = ($newdo['put_in_type'] -1);unset($newdo['put_in_type']);
				$newdo['shipin_type'] = $newdo['product_type'];unset($newdo['product_type']);
				$newdo['kuanshi_type'] = $newdo['cat_type'];unset($newdo['cat_type']);
				$newdo['warehouse'] = $newdo['warehouse_id'];unset($newdo['warehouse_id']);
				$newdo['company'] = $newdo['company_id'];unset($newdo['company_id']);
				$newdo['tmp_sn'] = $newdo['box_sn'];unset($newdo['box_sn']);
				$newdo['zhuchengsezhong'] = $newdo['jinzhong'];unset($newdo['jinzhong']);
				$newdo['zhuchengse'] = $newdo['caizhi'];unset($newdo['caizhi']);
				$newdo['zhushizhong'] = $newdo['zuanshidaxiao'];unset($newdo['zuanshidaxiao']);
				$newdo['xianzaichengben'] = $newdo['mingyichengben'];unset($newdo['mingyichengben']);
				$newdo['pass_status']= '1';
				$newdo['is_on_sale'] = '1';
				$newdo['status_old'] = '';
				$newdo['order_id_old'] = '';
				$newdo['sale_time'] = '';
				$newdo['company_time'] = '';
				$newdo['change_time'] = '';
				$newdo['gene_sn'] = '';
				$newdo['zhushiyanse'] = $newdo['yanse'];
				unset($newdo['id'],$newdo['oldsys_id'],$newdo['pass_sale'],$newdo['old_set_w'],$newdo['buchan_sn'],$newdo['jiejia'],$newdo['yuanshichengbenjia'],$newdo['caigou_chengbenjia'],$newdo['weixiu_status']);

				foreach ($newdo as $k=>$v) {
					$newdo[$k] = "'".$v."'";
				}
				$res = $db_pdo->exec($sql);
				if(!$res){
					$data['error'][] = $goods_id;
				}else{
					$data['success'][] = $goods_id;
				}
			}else{
				$sql = "UPDATE `jxc_goods` SET `is_on_sale`  = '1',`warehouse` = '{$warehouse}',`company` = '{$company}' WHERE `goods_id` = '".$goods_id."'";
				$res2 = $db_pdo->exec($sql);
				if($res2){
					$model->setValue('is_on_sale',100);
					$res1 = $model->save(true);
				}
				if($res1 && $res2){
					$data['success'][] = $goods_id;
				}else{
					$result['error'][] = $goods_id;
				}

			}

		}

		return $data;

	}

	/**
	 * 批量获取商品信息
	 */
	public function table_GetGoodsInfo($gids,$label,$is_on_sale = 2,$bill_id,$weixiu_status =0,$warehouse_id=0,$order_goods_id=0,$company=0,$put_in_type=0,$h_args=array()){
	    $error = ['1'=>'查无此货','2'=>'不是库存状态','3'=>'货品没有制 维修退货单 或 制了维修退货单没有审核','4'=>'货品不是批发借货库、深圳业务借贷库、批发展厅库、批发展厅待取库的货品，不能批量销售单','5'=>'货品已经绑定订单，不能制批量销售单','6'=>'货品出货公司不正确，不能制单','7'=>'单据所选入库方式不同，不能制退货返厂单'];
		array_shift($label);$goods_ids = array();
		//如果有bill_id,则是编辑单据
		if($bill_id){
			$sql = "SELECT `id`,`goods_id`,order_sn,pinhao,xiangci FROM `warehouse_bill_goods` WHERE `bill_id` = '".$bill_id."'";
			$goods_ids = $this->db()->getAll($sql);
			$goods_ids = array_column($goods_ids,'goods_id','id');
		}
		if(is_array($label))
			for($ii=0;$ii<count($label);$ii++){
                if(strstr($label[$ii],'.')===false && $label[$ii]!="0")
                	$label[$ii]="a." . $label[$ii];
			}
		$sql = "SELECT ".implode(',',$label).",a.`put_in_type`,a.`company`,a.`order_goods_id`,a.`warehouse_id`,a.`weixiu_status`,a.`is_on_sale` FROM `warehouse_goods` a left join `warehouse_bill_goods` b on a.goods_id=b.goods_id WHERE a.`goods_id` = ?";

		$stmt = $this->db()->db()->prepare($sql);

		$data = ['success'=>array(),'error'=>array()];

		foreach ($gids as $g) {
			$stmt->execute([$g]);
			$res = $stmt->fetch(PDO::FETCH_NUM);
			//var_dump($res);exit;//$res['0']; //!= $weixiu_status &&
			$c=count($res);
			if($c>2){
				$new_weixiu = $res[$c-2];
				$new_warehouse_id = $res[$c-3];
				$new_order_goods_id=$res[$c-4];
				$new_company=$res[$c-5];
				$new_put_in_type=$res[$c-6];
			}
			//货品如果绑定的订单来源是“鼎捷经销商订单”或未绑定订单，允许做单
			$source_name='';
			if($new_order_goods_id){
				$sql="select c.source_name from app_order.base_order_info as b left join app_order.app_order_details as d on b.id=d.order_id left join cuteframe.customer_sources as c on b.customer_source_id=c.id where d.id={$new_order_goods_id}";
				$source_name=$this->db()->getOne($sql);
				if(!$source_name) $source_name='';
			}
			
			//var_dump($new_order_goods_id);exit;
			if(!empty($res)){
			    // $h_args, 为批发退货单的使用
				$skip_check_onsale = false;
			    if (!empty($h_args) && SYS_SCOPE =="zhanting") {
			        
			        // TODO: 货品符合做批发销退单的前提有：
			        // 1. 如果退货的来源客户批发单是需要签收的，则要求货品是库存状态
			        // 2. 否则，货品状态必须是已销售

			        $to_customer_id = $h_args['to_customer_id'];
			        $sign_req = $this->db()->getOne("select ifnull(sign_required,0) as sign_required from jxc_wholesale where wholesale_id = '{$to_customer_id}' ");
			        if ($sign_req) {
			            if (end($res) != 2) {
			                $data['error'][] = $g.' : 不是库存状态';
			                continue;
			            } else {
			                $skip_check_onsale = true;
			            }
			        }
			    }
			    
			    if(!$skip_check_onsale && end($res) != $is_on_sale && !in_array($g,$goods_ids)){
					$data['error'][] = $g.' : '.$error[$is_on_sale];
					//var_dump($data);exit;
				}elseif($weixiu_status && $new_weixiu != $weixiu_status){
					$data['error'][] = $g.' : '.$error[$weixiu_status];
				}elseif($warehouse_id && !(in_array($new_warehouse_id,$warehouse_id))){
					$data['error'][] = $g.' : '.$error[4];
				}elseif($order_goods_id && $new_order_goods_id && $source_name !='鼎捷经销商订单'){
					$data['error'][] = $g.' : '.$error[5];
				}elseif($company && $new_company!=$company){
					$data['error'][] = $g.' : '.$error[6];
				}elseif($put_in_type && $new_put_in_type!=$put_in_type){
					$data['error'][] = $g.' : '.$error[7];
				}else{
					array_pop($res);
					$sql="select bill_type from warehouse_bill where id=$bill_id";
					$bill_type=$this->db()->getOne($sql);
					if($bill_type=='P'){
						$ecRow=$this->getEcDetailByGoodsId($g);
						if(!empty($ecRow)){
							$res[21]=$ecRow['pinhao'];
							$res[22]=$ecRow['ds_xiangci'];
							$res[23]=$ecRow['p_sn_out'];
						}else{
					 	   $res[21]='';
					 	   $res[22]='';
					 	   $res[23]='';
					    }
					}
					$data['success'][] = $res;
				}
				//var_dump($data);exit;
			}else{
				$data['error'][1] = $g.' : '.$error[1];
			}
		}
		//有错误,不反回正确信息
		if(!empty($data['error'])){
			$data['success'] = array();
		}
		return $data;
	}

	/**
	 * 获取商品信息2
	 * bill type : 0= 从总公司到分公司（需要加价，查实时加价率）
	 * 1 = 从分公司到总公司（查原Y单加价率）
	 */
	public function fetchGoodsInfo($gids, $field_bill_goods, $field_goods, $map_field, $is_on_sale = 2, $bill_id, $jiajia_type=0)
	{
		$error = ['1'=>'查无此货','2'=>'不是库存状态','3'=>'货品没有制 维修退货单 或 制了维修退货单没有审核','4'=>'货品不是批发借货库、深圳业务借贷库的货品，不能批量销售单','5'=>'货品已经绑定订单，不能制批量销售单','6'=>'货品出货公司不正确，不能制单','7'=>'单据所选入库方式不同，不能制退货返厂单'];
		
		
		$goods_ids = array();
		//如果有bill_id,则是编辑单据
		if($bill_id) {
			$sql = "SELECT `id`, `goods_id` FROM `warehouse_bill_goods` WHERE `bill_id` = '".$bill_id."'";
			$goods_ids = $this->db()->getAll($sql);
			$goods_ids = array_column($goods_ids, 'goods_id', 'id');
		}

		$w_sel = "`w`.`".implode('`,`w`.`', $field_goods)."`";
		
		$sql = "SELECT goods_id, ".$w_sel
				.", `w`.`put_in_type`, `w`.`company`, `w`.`order_goods_id`, `w`.`warehouse_id`, `w`.`weixiu_status`, `w`.`is_on_sale` FROM  `warehouse_goods` AS `w`
				 WHERE `w`.`goods_id` = ? ";
		// echo $sql;
		$stmt = $this->db()->db()->prepare($sql);
		$data = ['success'=>array(),'error'=>array()];

		foreach ($gids as $g) 
		{
			$stmt->execute([$g]);
			$res = $stmt->fetch(PDO::FETCH_NUM);
			//var_dump($res);exit;//$res['0']; //!= $weixiu_status &&
			$c = count($res);
			if($c>2)
			{
				$new_weixiu         = $res[$c-2];
				$new_warehouse_id   = $res[$c-3];
				$new_order_goods_id = $res[$c-4];
				$new_company        = $res[$c-5];
				$new_put_in_type    = $res[$c-6];
			}
			//var_dump($new_order_goods_id);exit;
			if(!empty($res)){
				if(end($res) != $is_on_sale && !in_array($g, $goods_ids)){
					$data['error'][] = $g.' : '.$error[$is_on_sale];
					//var_dump($data);exit;
				}elseif(!empty($weixiu_status) && $new_weixiu != $weixiu_status){
					$data['error'][] = $g.' : '.$error[$weixiu_status];
				}elseif(!empty($warehouse_id) && !(in_array($new_warehouse_id,$warehouse_id))){
					$data['error'][] = $g.' : '.$error[4];
				}elseif(!empty($order_goods_id) && $new_order_goods_id){
					$data['error'][] = $g.' : '.$error[5];
				}elseif(!empty($company) && $new_company!=$company){
					$data['error'][] = $g.' : '.$error[6];
				}elseif(!empty($put_in_type) && $new_put_in_type!=$put_in_type){
					$data['error'][] = $g.' : '.$error[7];
				}else{

					array_pop($res);
					$result = array();
					$price = 0;
					$style_type = $res[$map_field['cat_type']];
					$rate = $this->getBillJiajiaInfo($style_type, $jiajia_type, $g,$bill_id);

					foreach ($map_field as $key => $value) {
						
						if ($key == 'yuanshichengbenjia') {
							$price = $res[$value];
						}

						if ($key == 'jiajialv') {
							$result[] = $rate;
						} else if ($key == 'jiajia_chengben') {
							$result[] = $price * (1 + $rate/100);
						} else {
							$result[] = $res[$value];
						}
					}
					$data['success'][] = $result;
				}
				//var_dump($data);exit;
			}else{
				$data['error'][1] = $g.' : '.$error[1];
			}
		}
		//有错误,不返回正确信息
		if(!empty($data['error'])){
			$data['success'] = array();
		}
		$data['jiajia_type'] = $jiajia_type; // debug
		return $data;
	}


	/**
	 * 获取商品信息M单
	 * bill type : 0= 从总公司到分公司（需要加价，查实时加价率）
	 * 1 = 从分公司到总公司（查原Y单加价率）
	 */
	public function fetchGoodsInfo_M($gids, $field_bill_goods, $field_goods, $map_field, $is_on_sale = 2, $bill_id, $jiajia_type=0)
	{
		$error = ['1'=>'查无此货','2'=>'不是库存状态','3'=>'货品没有制 维修退货单 或 制了维修退货单没有审核','4'=>'货品不是批发借货库、深圳业务借贷库的货品，不能批量销售单','5'=>'货品已经绑定订单，不能制批量销售单','6'=>'货品出货公司不正确，不能制单','7'=>'单据所选入库方式不同，不能制退货返厂单'];
		
		
		$goods_ids = array();
		//如果有bill_id,则是编辑单据
		if($bill_id) {
			$sql = "SELECT `id`, `goods_id` FROM `warehouse_bill_goods` WHERE `bill_id` = '".$bill_id."'";
			$goods_ids = $this->db()->getAll($sql);
			$goods_ids = array_column($goods_ids, 'goods_id', 'id');
		}

		$w_sel = "`w`.`".implode('`,`w`.`', $field_goods)."`";
		
		$sql = "SELECT goods_id, ".$w_sel
				.", `w`.`jingxiaoshangchengbenjia`,`w`.`put_in_type`, `w`.`company`, `w`.`order_goods_id`, `w`.`warehouse_id`, `w`.`weixiu_status`, `w`.`is_on_sale` FROM  `warehouse_goods` AS `w`
				 WHERE `w`.`goods_id` = ? ";
		// echo $sql;
		$stmt = $this->db()->db()->prepare($sql);
		$data = ['success'=>array(),'error'=>array()];

		foreach ($gids as $g) 
		{
			$stmt->execute([$g]);
			$res = $stmt->fetch(PDO::FETCH_NUM);
			//var_dump($res);exit;//$res['0']; //!= $weixiu_status &&
			$c = count($res);
			if($c>2)
			{
				$new_weixiu         = $res[$c-2];
				$new_warehouse_id   = $res[$c-3];
				$new_order_goods_id = $res[$c-4];
				$new_company        = $res[$c-5];
				$new_put_in_type    = $res[$c-6];
			}
			//var_dump($new_order_goods_id);exit;
			if(!empty($res)){
				if(end($res) != $is_on_sale && !in_array($g, $goods_ids)){
					$data['error'][] = $g.' : '.$error[$is_on_sale];
					//var_dump($data);exit;
				}elseif(!empty($weixiu_status) && $new_weixiu != $weixiu_status){
					$data['error'][] = $g.' : '.$error[$weixiu_status];
				}elseif(!empty($warehouse_id) && !(in_array($new_warehouse_id,$warehouse_id))){
					$data['error'][] = $g.' : '.$error[4];
				}elseif(!empty($order_goods_id) && $new_order_goods_id){
					$data['error'][] = $g.' : '.$error[5];
				}elseif(!empty($company) && $new_company!=$company){
					$data['error'][] = $g.' : '.$error[6];
				}elseif(!empty($put_in_type) && $new_put_in_type!=$put_in_type){
					$data['error'][] = $g.' : '.$error[7];
				}else{

					
					$result = array();
					$price = 0;
					$style_type = $res[$map_field['cat_type']];
					$rate = $this->getBillJiajiaInfo($style_type, $jiajia_type, $g,$bill_id);

					foreach ($map_field as $key => $value) {
						
						if ($key == 'yuanshichengbenjia') {
							//exit($_SESSION['companyId']);							
							if(SYS_SCOPE=='zhanting' && $_SESSION['companyId']<>58)
								$res[$value]=!empty($res[$c-7]) ? $res[$c-7] : 0;
							$price = $res[$value];
						}
                         
						if ($key == 'jiajialv') {
							$result[] = $rate;
						} else if ($key == 'jiajia_chengben') {
							$result[] = $price * (1 + $rate/100);
						} else {
							$result[] = $res[$value];
						}
					}
					$data['success'][] = $result;
				}
				//var_dump($data);exit;
			}else{
				$data['error'][1] = $g.' : '.$error[1];
			}
		}
		//有错误,不返回正确信息
		if(!empty($data['error'])){
			$data['success'] = array();
		}
		$data['jiajia_type'] = $jiajia_type; // debug
		return $data;
	}




	public function table_GetGoodsInfoP($gids,$label,$is_on_sale = 2,$bill_id,$weixiu_status =0,$warehouse_id=0,$order_goods_id=0,$company_id=0,$put_in_type=0){
		$error = ['1'=>'查无此货','2'=>'不是库存状态','3'=>'货品没有制 维修退货单 或 制了维修退货单没有审核','4'=>'货品不是批发借货库、深圳业务借贷库、批发展厅库、批发展厅待取库的货品，不能批量销售单','5'=>'货品已经绑定订单，不能制批量销售单','6'=>'货品出货公司不正确，不能制单','7'=>'单据所选入库方式不同，不能制退货返厂单'];
		array_shift($label);$goods_ids = array();
		//如果有bill_id,则是编辑单据
		if($bill_id){
			$sql = "SELECT `id`,`goods_id`,order_sn,pinhao,xiangci FROM `warehouse_bill_goods` WHERE `bill_id` = '".$bill_id."'";
			$goods_ids = $this->db()->getAll($sql);
			$goods_ids = array_column($goods_ids,'goods_id','id');
		}
		if(is_array($label))
			for($ii=0;$ii<count($label);$ii++){
			if(strstr($label[$ii],'.')===false && $label[$ii]!="0")
				$label[$ii]="a." . $label[$ii];
		}
		$sql = "SELECT ".implode(',',$label).",a.`put_in_type`,a.`company_id`,a.`order_goods_id`,a.`warehouse_id`,a.`weixiu_status`,a.`is_on_sale`,a.yuanshichengbenjia,a.jingxiaoshangchengbenjia FROM `warehouse_goods` a left join `warehouse_bill_goods` b on a.goods_id=b.goods_id WHERE a.`goods_id` = ?";
	
		$stmt = $this->db()->db()->prepare($sql);
	
		$data = ['success'=>array(),'error'=>array()];
		if ($put_in_type) {
			$dd = new DictModel(1);
			$put_in_type_str = $dd->getEnum('warehouse.put_in_type', $put_in_type);
		}
		foreach ($gids as $g) {
			$stmt->execute([$g]);
			$res = $stmt->fetch(PDO::FETCH_NUM);
			//var_dump($res);exit;//$res['0']; //!= $weixiu_status &&
			$c=count($res);
			$new_order_goods_id='';
			$new_weixiu = '';
			$new_warehouse_id = '';
			//$new_order_goods_id='';
			$new_company_id='';
			$new_put_in_type='';
			$new_jingxiaoshangchengbenjia='';
			$new_yuanshichengbenjia='';			
			
			if($c>2){
				$new_weixiu = $res[$c-4];
				$new_warehouse_id = $res[$c-5];
				$new_order_goods_id=$res[$c-6];
				$new_company_id=$res[$c-7];
				$new_put_in_type=$res[$c-8];
				$new_jingxiaoshangchengbenjia=$res[$c-1];
				$new_yuanshichengbenjia=$res[$c-2];				
			}
			//货品如果绑定的订单来源是“鼎捷经销商订单”或未绑定订单，允许做单
			$source_name='';
			if($new_order_goods_id){
				$sql="select c.source_name from app_order.base_order_info as b left join app_order.app_order_details as d on b.id=d.order_id left join cuteframe.customer_sources as c on b.customer_source_id=c.id where d.id={$new_order_goods_id}";
				$source_name=$this->db()->getOne($sql);
				if(!$source_name) $source_name='';
			}
			//$res[4] 采购价  a.yuanshichengben
			//$res[5] 名义价  a.mingyichengben
			//$res[6] 批发价  a.mingyichengben
			//var_dump($new_order_goods_id);exit;
			if(!empty($res)){
			    if(SYS_SCOPE == 'zhanting'){    			    
    			    if(!in_array($company_id,array(58,515)) && $new_put_in_type<>5){
    			        $res[4] = $new_jingxiaoshangchengbenjia;
    			    }else {
    			        $res[4] = $new_yuanshichengbenjia;
    			    }
    			    if(!in_array($company_id,array(58,515))){
    			        $res[5] = $res[4];
    			        $res[6] = 0;
    			    }
     		    }
			    
				if($res[$c-3] != $is_on_sale && !in_array($g,$goods_ids)){
					$data['error'][] = $g.' : '.$error[$is_on_sale];
					//var_dump($data);exit;
				}elseif($weixiu_status && $new_weixiu != $weixiu_status){
					$data['error'][] = $g.' : '.$error[$weixiu_status];
				}/*elseif($warehouse_id && !(in_array($new_warehouse_id,$warehouse_id))){
				 	BOSS和浩鹏 批发销售单取消仓库制单限制，所有仓库都允许做单 BOSS-1665
					$data['error'][] = $g.' : '.$error[4];
				}elseif(SYS_SCOPE <> 'zhanting' && $order_goods_id && $new_order_goods_id && $source_name !='鼎捷经销商订单'){
					$data['error'][] = $g.' : '.$error[5];
				}*/elseif( $new_company_id!=$company_id){
					$data['error'][] = $g.' : '.$error[6];
				}elseif($put_in_type && $new_put_in_type!=$put_in_type){
					$data['error'][] = $g.' : 货品的入库方式与单据的入库方式‘'.$put_in_type_str .'’不同 ';
				}else{
					array_pop($res);
						
						
					$ecRow=$this->getEcDetailByGoodsId($g);
					if(!empty($ecRow)){
						$res[21]=$ecRow['pinhao'];
						$res[22]=$ecRow['ds_xiangci'];
						$res[23]=$ecRow['p_sn_out'];
					}else{
						$res[21]='';
						$res[22]='';
						$res[23]='';
					}
						
					$data['success'][] = $res;
				}
				//var_dump($data);exit;
			}else{
				$data['error'][1] = $g.' : '.$error[1];
			}
		}
		//有错误,不反回正确信息
		if(!empty($data['error'])){
			$data['success'] = array();
		}
		return $data;
	}
	/**
	 * 从已有的单中找对应货品的加价率，没有就取系统配置的（财务管理-->加价率调整）。
	 * @param  string $value [description]
	 * @return [type]        [description]
	 * // 0 - 加价，1 - 查M单历史纪录加价，2 - 不加价
	 */
	public function getBillJiajiaInfo($style_type, $jiajia_type, $goods_id,$bill_id)
	{
		if ($jiajia_type == 0) {
			return $this->getRisingRate($style_type);
		} else if ($jiajia_type == 1) {
			// 改为当初从总公司调到分公司时用的加价率
			$wheretime="";
			if(!empty($bill_id))
                $wheretime=" and b.check_time<(select ifnull(b2.check_time,b2.create_time) from warehouse_bill b2 where b2.id='{$bill_id}') "; 
			$sql = "SELECT bg.`jiajialv` FROM `warehouse_bill_goods` bg join warehouse_bill b on bg.bill_id=b.id
				WHERE bg.`bill_type`='M' AND bg.`goods_id`={$goods_id} and from_company_id=58 and (b.from_bill_id=0 or b.from_bill_id is null)  $wheretime ORDER BY `bill_id` DESC limit 1";
			$jiajialv  = $this->db()->getOne($sql);
			return number_format($jiajialv, 2);
		}
		return 0;
	}

	/**
	 * 获取单个加价率
	 * @param  [char] $style_type [新款式类型]
	 * @return [float]             [加价率]
	 */
	public function getRisingRate($style_type)
	{
		if (trim($style_type) == '') {
			$style_type = '其他';
		}
		$model = new WarehouseBillInfoYJiajialvModel(22);
		return $model->getJiajialvByStyleTypeName($style_type);
	}

    /*
     * 更改货品状态以及货品位置信息
     */
    public function modifyGoodsInfo($where) {
        $result = array('success' => 0,'error' =>'');
        $sql = "UPDATE `warehouse_goods` set ";
        $where2 = " where 1=1 ";
        $str = "";
        if($where['goods_id'] != "")
        {
            //add by zhangruiying 去除用户不小心输入或粘贴的空白字符和中文,号替换
            $where['goods_id']=preg_replace("/[sv]+/",'',$where['goods_id']);
            $where['goods_id']=str_replace(" ",',',$where['goods_id']);
            $where['goods_id']=str_replace("，",',',$where['goods_id']);
            //add end
            $item =explode(",",$where['goods_id']);
            $goodsid = "";
            foreach($item as $key => $val) {
                    if ($val != '') {
                            if($goodsid){
                                    $goodsid .= ",'".trim($val)."'";
                            }else{
                                    $goodsid .= "'".trim($val)."'";
                            }
                    }
            }
            $where['goods_id'] = $goodsid;
            $where2 .= " and `goods_id` in (".$where['goods_id'].") ";
        }
        $dot = '';
         if (isset($where['is_on_sale']) && !empty($where['is_on_sale'])) {
            $is_on_sale = $where['is_on_sale'];
            $str .= " `is_on_sale`=".$where['is_on_sale'];
            $dot = ',';
        }
        if (isset($where['company_id']) && !empty($where['company_id']) ){
            $str .= " {$dot}`company_id`=".$where['company_id'];
        }
        if (isset($where['warehouse_id']) && !empty($where['warehouse_id'])) {
            $warehouse_id = $where['warehouse_id'];
            $str .= ",`warehouse_id`=".$warehouse_id;


        }
        if (isset($where['company']) && !empty($where['company'])){
            $str .= ",`company`='".$where['company']."'";
        }
        if (isset($where['warehouse']) && !empty($where['warehouse'])){
            $str .= ",`warehouse`='".$where['warehouse']."'";
        }

        $sql .= $str.$where2;
        //echo $sql;exit;
        $res = $this->db()->query($sql);
        file_put_contents('update_goods_stock_status.log', '[user:'.$_SESSION['userName'].'  time:'.date('Y-m-d h:i:s').'] '.$sql .PHP_EOL,FILE_APPEND);
        if (!$res) {
            return false;
        }
        if (!empty($warehouse_id)) {
            $box_id = $this->getBoxId($warehouse_id);
            $sql2 = "update `goods_warehouse` set `box_id`={$box_id} ,`warehouse_id`=".$warehouse_id." where  `good_id` in (".$where['goods_id'].") ";
            //echo $sql2;exit;
            $rs = $this->db()->query($sql2);
            if (!$rs) {
                return false;
            }
        }

        return true;
    }

    public function getBoxId($warehouse_id) {
        $sql = "select `box_id` from `goods_warehouse` as gw,`warehouse_box` as wb where gw.`box_id`=wb.`id` and wb.box_sn='0-00-0-0' and gw.`warehouse_id`={$warehouse_id}";
        //echo $sql;exit;
        $data = $this->db()->getRow($sql);
        $box_id = $data['box_id'];
        return $box_id;
    }

    //added by Linphie
    public function PrintHuodongCode($info) {
        //var_dump($info);exit;
        $jiajialv = (isset($info["jiajialv"]) && $info["jiajialv"] != '') ? $info["jiajialv"] : 1 ;
        $jiajianum = (isset($info["jiajianum"]) && $info["jiajianum"] != '') ? $info["jiajianum"] : 0 ;
        $type = (isset($info["type"]) && $info["type"] != '') ? $info["type"] : 0 ;
        $type_t = (isset($info["type_t"]) && $info["type_t"] != '') ? $info["type_t"] : 0 ;
        $label_price = (isset($info["label_price"]) && $info["label_price"] != '' && $info["label_price"] != 'undefined') ? $info["label_price"] : 0 ;
        $daying_type = $info['daying_type'];
        if (isset($info['bill_no']) && $info['bill_no'] != ''){
            $sql = "select `goods_id` from `warehouse_bill_goods` where `bill_no`='{$info['bill_no']}'";
            $goods_info = $this->db()->getAll($sql);
            $goods_id = array();
            foreach($goods_info as $k => $v){
                $goods_id[] = trim($v['goods_id']);
            }

        }
        if (isset($info['goods_id']) && !empty($info['goods_id'])){
            $info['goods_id']=preg_replace("/[sv]+/",'',trim($info['goods_id']));
            $info['goods_id']=str_replace(" ",',',trim($info['goods_id']));
            $info['goods_id']=str_replace("，",',',trim($info['goods_id']));

            $item =explode(",",$info['goods_id']);
            $goods_id = array();
            foreach ($item as $k){
                if($k){//去除空的goods_id
                    $goods_id[] = trim($k);
                }
            }
           // var_dump($goods_id);exit;
        }
        if($label_price == 1 && SYS_SCOPE == 'zhanting'){
            $goods_chek = '';
            foreach ($goods_id as $god_id) {
                $god_id = trim($god_id);
                if ($god_id == ""){
                  continue;
                }
                $sql = "select biaoqianjia from warehouse_goods where goods_id ='$god_id'";
                $check_biaoqianjia = $this->db()->getOne($sql);
                if($check_biaoqianjia == 0){
                    $goods_chek.= $god_id."|";
                }
            }

            if($goods_chek != ''){//货号“*** 没有展厅标签价无法打印”
                $error = "货号：".$goods_chek."没有展厅标签价无法打印";
                $this->error_csv($error);
            }
        }

        if($type){
            $goods_info_list = $this->make_print_code_csv($goods_id,$jiajialv,$jiajianum,'',2,$type,$daying_type,$type_t,$label_price);
        }else{
            $goods_info_list = $this->make_print_code_csv($goods_id,$jiajialv,$jiajianum,'',2,$type,$daying_type,$type_t,$label_price);
        }

        exit;
    }

    public function make_print_code_csv($codes, $jiajialv=1, $jiajianum=0,$company_info = array(),$type =0,$type2,$daying_type=1,$type_t,$label_price){

      $deal_num = new WarehouseBillController(21);
      $newmodel = new WarehouseBillModel(21);
      $goods_file = "";
      $xiangqian_product_type = array('钻石','珍珠','珍珠饰品','翡翠','翡翠饰品','宝石','宝石饰品','钻石饰品','宝石饰品' );//镶嵌类的产品线
      $zhuchengse_list = array(
              '18K白金'=>'18K金',
              '18K玫瑰金'=>'18K金',
              '18K黄金'=>'18K金',
              '18K彩金'=>'18K金',
              'PT950'=>'铂Pt950',
              'PT900'=>'铂Pt900',
              'PT990'=>'铂Pt990',
              '9K白金'=>'9K金',
              '9K玫瑰金'=>'9K金',
              '9K黄金'=>'9K金',
              '9K彩金'=>'9K金',
              '14K金'=>'14K金',

              '10K白金'=>'10K金',
              '10K玫瑰金'=>'10K金',
              '10K黄金'=>'10K金',
              '10K彩金'=>'10K金',
              '14K白金'=>'14K金',
              '14K玫瑰金'=>'14K金',
              '14K黄金'=>'14K金',
              '14K彩金'=>'14K金',
              '18K玫瑰白'=>'18K彩金',
              '18K黄白' => '18K彩金',
              'Pd950'=>'钯Pd950',
              'S925'=>'银925',
              '足金'=>'足金',
              '千足金'=>'千足金',
              '千足银'=>'千足银',
              '无'=>'',
              '其他' =>''
      );
      $rongko_list=array(
      		'W122_011'=>'0.3-0.5',
            'W111_003'=>'0.5-1',
            'W111_005'=>'0.3-1',
            'W300_005'=>'0.5-1',
            'W240_008'=>'0.2-0.7',
            'W500_001'=>'1.5-2',
      		'W500_002'=>'1.5-2',
      		'W240_006'=>'0.3-1',
      		'W122_012'=>'0.3-1',
      		'W122_013'=>'0.3-1',
      		'W230_006'=>'0.3-0.7',
      		'W150_006'=>'0.2-0.5',
      		'W170_003'=>'0.3-1',
      		'W170_004'=>'0.3-1',
      		'W210_005'=>'0.3-0.7',
      		'W220_005'=>'0.3-0.5',
      		'W230_002'=>'0.3-0.8',
      		'W122_007'=>'0.3-1',
      		'W240_011'=>'0.3-0.5',
      		'W111_002'=>'0.5-1,1.5',
      		'W111_012'=>'0.3-1',
      		'W113_005'=>'0.5-0.8',
      		'W122_005'=>'0.3-1',
      		'W112_001'=>'0.3-1',
      		'W121_010'=>'0.3-1',
      		'W121_006'=>'0.3-1',
      		'W170_002'=>'0.3-1',
      		'W111_010'=>'0.3-1.5',
      		'W300_007'=>'0.3-0.7',
      		'W240_010'=>'0.3-0.5',
      		'W122_003'=>'0.3-1',
      		'W150_004'=>'0.2-0.3',
      		'W300_006'=>'0.3-0.6,1',
      		'W113_022'=>'0.5-1',
      		'W113_024'=>'0.3-1',
      		'W210_011'=>'0.2-0.7',
      		'W111_001'=>'0.3-1',
      		'W113_014'=>'0.2-0.8',
      		'W122_009'=>'0.1-0.5',
      		'W220_004'=>'0.3-0.5',
      		'W113_016'=>'0.5-0.7',
      		'W122_006'=>'0.3-0.8',
      		'W121_009'=>'0.3-1',
      		'W230_009'=>'0.3-0.7',
      		'W300_004'=>'0.1-0.7',
      		'W300_008'=>'0.3-0.5',
      		'W122_010'=>'0.3-0.5',
      		'W133_003'=>'0.5-1',
      		'W160_001'=>'0.3-0.5',
      		'W121_007'=>'0.3-1',
      		'W150_005'=>'0.1-0.3',
      		'W111_009'=>'0.2-0.6,1,1.5',
      		'W121_008'=>'0.3-1.5',
      		'W210_006'=>'0.3-0.7',
      		'W113_019'=>'0.3-1',
      		'W210_010'=>'0.3-0.4',
      		'W150_003'=>'0.1-0.3',
      		'W122_002'=>'0.3-1',
      		'W150_001'=>'0.2-0.5',
      		'W122_014'=>'0.3-1',
      		'W150_002'=>'0.2-0.5',
      		'W111_014'=>'0.3-1',
      		'W140_002'=>'0.2-0.5',
      		'W210_004'=>'0.3-0.8',
      		'W210_002'=>'0.3-1',
      		'W210_003'=>'0.3-1',
      		'W210_007'=>'0.3-0.5',
      		'W111_013'=>'0.3-0.7',
      		'W240_009'=>'0.2-0.5',
      		'W240_007'=>'0.2-0.5',
      		'W113_012'=>'0.2-0.7',
      		'W113_010'=>'0.3-1',
      		'W113_009'=>'0.2-1',
      		'W500_003'=>'1.5-2',
      		'W240_001'=>'0.2-0.5',
      		'W113_023'=>'0.3-1',
      		'W300_003'=>'0.5-1',
      		'W113_007'=>'0.5-1',
      		'W112_003'=>'0.3-0.5',
      		'W210_008'=>'0.3-1',
      		'W160_002'=>'0.3-0.5',
      		'W220_006'=>'0.2-0.5',
      		'W210_009'=>'0.3-0.7',
      		'W500_004'=>'1.5-2',
      		'W210_012'=>'0.3-0.5',
      		'W111_006'=>'0.3-1',
      		'W133_001'=>'0.5-1',
      		'W240_004'=>'0.2-0.7',
      		'W240_003'=>'0.2-0.5',
      		'W121_005'=>'0.3-1',
      		'W113_011'=>'0.5-1',
      		'W400_002'=>'0.6-1.5',
      		'W113_015'=>'0.2-0.7',
      		'W113_018'=>'0.2-0.5',
      		'W140_001'=>'0.3-0.5',
      		'W121_002'=>'0.3-1.5',
      		'W111_008'=>'0.3-1',
      		'W111_004'=>'0.3-1',
      		'W121_004'=>'0.3-1',
      		'W121_003'=>'0.3-1',
      		'W122_008'=>'0.3-0.7',
      		'W230_005'=>'0.2-0.7',
      		'W170_001'=>'0.3-0.6',
      		'W111_007'=>'0.4-1',
      		'W113_020'=>'0.5-0.7',
      		'W134_001'=>'0.3-1',
      		'W113_002'=>'0.5-0.7',
      		'W220_002'=>'0.3-0.5',
      		'W300_001'=>'0.5-1',
      		'W220_001'=>'0.3-1',
      		'W122_001'=>'0.3-0.7',
      		'W220_003'=>'0.3-0.5',
      		'W400_001'=>'0.6-1.5',
      		'W230_001'=>'0.3-0.7',
      		'W112_002'=>'0.3-0.7',
      		'W133_002'=>'0.2-1',
      		'W240_012'=>'0.3-0.5',
      		'KLRW022614'=>'0.2-1',
      		'W111_011'=>'0.3-1',
      		'W113_021'=>'0.5-1',
      		'W240_002'=>'0.3-0.7',
      		'W122_004'=>'0.3-1',
      		'W240_005'=>'0.3-0.7',
      		'W210_001'=>'0.3-0.5',
      		'W230_010'=>'0.3-0.5',
      		'W112_004'=>'0.3-0.5',
      		'W230_004'=>'0.3-0.7',
      		'W300_002'=>'0.3-0.5',
      		'W230_007'=>'0.5-1',
      		'W113_008'=>'0.5-1',
      		'W113_013'=>'0.5-1',
      		'KLRW022271'=>'0.2-1',
      		'KLRW006426'=>'0.2-1',
      		'KLRW021064'=>'0.2-1',
      		'W5154'=>'0.2-1',
      		'W2770'=>'0.2-1',
      		'KLRW006275'=>'0.2-1',
      		'KLRW026900'=>'0.2-0.8,1,1.2,1.5',
      		'W3663'=>'0.2-1,1.2,1.5',
      		'W1978'=>'0.2-1',
      		'W2815'=>'0.2-1',
      		'W5966'=>'0.3-1,1.2',
      		'W9489'=>'0.3-1',
      		'W230_003'=>'0.3-0.7',
      		'W113_006'=>'0.5-0.9',
      		'W121_001'=>'0.3-1',
      		'W210_013'=>'0.2-0.5',
      		'W113_017'=>'0.5-1',
      		'W113_004'=>'0.3-0.7',
      		'W4950'=>'0.2-0.5,0.7-0.9,1.2-1.3,1.5,1.9',
      		'W230_008'=>'0.3-0.7',
      		'W113_003'=>'0.5-1',
      		'W113_001'=>'0.3-1',
      		'KLRW030271'=>'0.2-0.3, 0.5',
      		'KLRW030288'=>' 0.2-0.3, 0.5-0.6',
      		'KLRW030287'=>'0.2-0.3, 0.5, 1.0',
      		'KLRW030272 '=>'0.2-0.3, 0.5'
      		
      );
       if($type_t==0){
     	$mairugongfeidanjia_title="买入工费单价";
       }else{
       	$mairugongfeidanjia_title="";
       }
        if($daying_type == 2){
            ini_set('memory_limit', '-1');
            set_time_limit(0);
            header("Content-Type: text/html; charset=gb2312");
            header("Content-type:aplication/vnd.ms-excel");
            header("Content-Disposition:filename=" . iconv('utf-8', 'gb2312', "打印条码".time()) . ".xls");
            $csv_body = '<table border="1"><tr>
                <td style="text-align: center;">货号</td>
                <td style="text-align: center;">款号</td>
                <td style="text-align: center;">基因码</td>
                <td style="text-align: center;">手寸</td>
                <td style="text-align: center;">长度</td>
                <td style="text-align: center;">主石粒数</td>
                <td style="text-align: center;">主石重</td>
                <td style="text-align: center;">副石粒数</td>
                <td style="text-align: center;">副石重</td>
                <td style="text-align: center;">加工商编号</td>
                <td style="text-align: center;">总重</td>
                <td style="text-align: center;">净度</td>
                <td style="text-align: center;">颜色</td>
                <td style="text-align: center;">证书号</td>
                <td style="text-align: center;">国际证书</td>
                <td style="text-align: center;">主石切工</td>
                <td style="text-align: center;">标签备注</td>
                <td style="text-align: center;">主石</td>
                <td style="text-align: center;">副石</td>
                <td style="text-align: center;">主成色</td>
                <td style="text-align: center;">饰品分类</td>
                <td style="text-align: center;">款式分类</td>
                <td style="text-align: center;">名称</td>
                <td style="text-align: center;">石3副石</td>
                <td style="text-align: center;">石3粒数</td>
                <td style="text-align: center;">石3重</td>
                <td style="text-align: center;">石4副石</td>
                <td style="text-align: center;">石4粒数</td>
                <td style="text-align: center;">石4重</td>
                <td style="text-align: center;">石5副石</td>
                <td style="text-align: center;">石5粒数</td>
                <td style="text-align: center;">石5重</td>
                <td style="text-align: center;">主成色重</td>
                <td style="text-align: center;">副成色</td>
                <td style="text-align: center;">副成色重</td>
                <td style="text-align: center;">买入工费</td>
                <td style="text-align: center;">计价工费</td>
                <td style="text-align: center;">加价率</td>
                <td style="text-align: center;">最新零售价</td>
                <td style="text-align: center;">模号</td>
                <td style="text-align: center;">品牌</td>
                <td style="text-align: center;">证书数量</td>
                <td style="text-align: center;">配件数量</td>
                <td style="text-align: center;">时尚款</td>
                <td style="text-align: center;">系列</td>
                <td style="text-align: center;">属性</td>
                <td style="text-align: center;">类别</td>
                <td style="text-align: center;">成本价</td>
                <td style="text-align: center;">入库日期</td>
                <td style="text-align: center;">加价率代码</td>
                <td style="text-align: center;">主石粒重</td>
                <td style="text-align: center;">副石粒重</td>
                <td style="text-align: center;">标签手寸</td>
                <td style="text-align: center;">字印</td>
                <td style="text-align: center;">货币符号零售价</td>
                <td style="text-align: center;">新成本价</td>
                <td style="text-align: center;">新零售价</td>
                <td style="text-align: center;">一口价</td>
                <td style="text-align: center;">标价</td>
                <td style="text-align: center;">定制价</td>
                <td style="text-align: center;">A</td>
                <td style="text-align: center;">B</td>
                <td style="text-align: center;">C</td>
                <td style="text-align: center;">D</td>
                <td style="text-align: center;">E</td>
                <td style="text-align: center;">F</td>
                <td style="text-align: center;">G</td>
                <td style="text-align: center;">H</td>
                <td style="text-align: center;">I</td>
                <td style="text-align: center;">HB_G</td>
                <td style="text-align: center;">HB_H</td>
                <td style="text-align: center;">样板可做镶口范围</td>
                <td style="text-align: center;">活动价</td>
                <td style="text-align: center;">原价</td>
            	<td style="text-align: center;">镶口</td>	
            	<td style="text-align: center;">'.$mairugongfeidanjia_title.'</td>
                <td style="text-align: center;">直营店钻石颜色</td>
                <td style="text-align: center;">直营店钻石净度</td>
                <td style="text-align: center;">总重</td>
                <td style="text-align: center;">展厅标签价</td>
                <td style="text-align: center;">证书类型</td>
                <td style="text-align: center;">是否支持定制</td></tr>';
        }else{
            $content = '';
            $content .= "货号,款号,基因码,手寸,长度,主石粒数,主石重,副石粒数,副石重,加工商编号,总重,净度,颜色,证书号,国际证书,主石切工,标签备注,主石,副石,主成色,饰品分类,款式分类,名称,石3副石,石3粒数,石3重,石4副石,石4粒数,石4重,石5副石,石5粒数,石5重,主成色重,副成色,副成色重,买入工费,计价工费,加价率,最新零售价,模号,品牌,证书数量,配件数量,时尚款,系列,属性,类别,成本价,入库日期,加价率代码,主石粒重,副石粒重,标签手寸,字印,货币符号零售价,新成本价,新零售价,一口价,标价,定制价,A,B,C,D,E,F,G,H,I,HB_G,HB_H,样板可做镶口范围,活动价,原价,镶口,{$mairugongfeidanjia_title},直营店钻石颜色,直营店钻石净度,总重,展厅标签价,证书类型,是否支持定制\r\n";
            $price2 = '';
           
        }
        //$goods_file = iconv("utf-8", "gbk", $title) . "\n";
        

      foreach ($codes as $k => $goods_id){
          $goods_id = trim($goods_id);
          if ($goods_id == ""){
              break;
          }
          $sql = "select *, att2 as kuanshi_type from warehouse_goods as g where g.goods_id = '$goods_id' limit 0, 1";
//echo $sql;exit;
          $line = $this->db()->getRow($sql);
         // var_dump($line);exit;
          if (empty($line)){
              //echo "没有查到该货号：".$goods_id."信息,请核实后再做打印";exit;
            $error = "没有查到该货号：".$goods_id."信息，请核实后再做打印！";
            $this->error_csv($error);

          }
          if($line['caizhi'] != '' && $line['caizhi'] != "无" && $line['caizhi'] != "其它" && $line['caizhi'] != "裸石	"){
          	 if(strstr($line['goods_name'], $line['caizhi']) == false){
          	 	$error = "该货号：".$goods_id."商品名称没有包含该商品材质".$line['caizhi'];
          	 	$this->error_csv($error);
          	 }
          }
          
          if($line['zhushi']=='钻石' && $line['cat_type1']!='裸石' && strstr($line['goods_name'],$line['zhushi'])==false && $line['tuo_type']=='1'){
          	$error = '该货号：'.$goods_id.'商品名称必须包含"'.$line['zhushi'].'"';
          	$this->error_csv($error);
          }
          if($line['zhushi']=='' && $line['fushi']=='钻石' && $line['cat_type1']!='裸石' && strstr($line['goods_name'],$line['fushi'])==false && $line['tuo_type']=='1'){
          	$error = '该货号：'.$goods_id.'商品名称必须包含"'.$line['fushi'].'"';
          	$this->error_csv($error);
          }

          
          $xilie = [];
          $base_style = 8; //基本款
          if($line['goods_sn']){
              $style_sql = "SELECT xilie FROM `base_style_info` WHERE style_sn = '{$line['goods_sn']}' AND check_status = 3";
              $xilie = explode(',',DB::cn(12)->db()->query($style_sql)->fetchColumn());
          }

          $zhuchengse = $line['caizhi'];
          $kuanshi_type = $line['kuanshi_type'];
          
          $mairugongfeidanjia='';
          if($type_t ==0){
          	if($line['mairugongfeidanjia']<20){
          		$mairugongfeidanjia = round($line['mairugongfeidanjia']+8,1);
          	}else{
          		$mairugongfeidanjia = round($line['mairugongfeidanjia']+10,1);
          	}
          	
          }

          if($type_t ==2){//打印活动标签
              $line['xianzaixiaoshou'] = $line['mingyichengben'];
          }


          if($line['xianzaixiaoshou'] != 'err_AB' && $line['xianzaixiaoshou'] != 'err_WM' ){
              if ($zhuchengse == 'PT950' || $zhuchengse == 'PT990' || $zhuchengse == 'PT900'){
                  $line['yikoujia'] = round($line['xianzaixiaoshou']*1.9 + 1000);
                  $line['biaojia'] = round($line['yikoujia']*1.5);
              }else{
                  $line['yikoujia'] = round($line['xianzaixiaoshou']*1.9 + 500);
                  $line['biaojia'] = round($line['yikoujia']*1.5);
              }
          }
          $line['zhuchengse'] = $zhuchengse_list[$zhuchengse];
          $line['goods_name'] = str_replace($zhuchengse, $line['caizhi'], $line['goods_name']);
          $line['goods_name'] = str_replace(array('女戒','情侣戒','CNC情侣戒','男戒','戒托'), array('戒指','戒指','戒指','戒指','戒指'), $line['goods_name']);
          $line['goods_name'] = str_replace(array('海水海水','淡水白珠','淡水圆珠','淡水',"大溪地", "南洋金珠"), array('海水','珍珠','珍珠','',"海水","海水珍珠"), $line['goods_name']);

          if ($line['fushizhong'] > 0 && $line['fushi'] != $line['zhushi'] ){
              //$line['goods_name'] .= '配' . $line['fushi'];
          }
          if ($line['shi2zhong'] > 0 && $line['shi2'] != $line['zhushi'] && $line['shi2'] != $line['fushi']){
              //$line['goods_name'] .= '、' . $line['shi2'];
          }
          //获取保险费
		  
		  //update by liulinyan 2015-08-12 根据金托类型获取保险费
		  $tuotype = $line['tuo_type'];
		  if($tuotype>1)
		  {
			//托类型
			//获取镶口
			$xiankou = $line['jietuoxiangkou'];
			if(!empty($xiankou) && $xiankou > 0)
			{
				$getbxf_data = $xiankou;
			}else{
				$getbxf_data = $line['zuanshidaxiao'];
			}
			$baoxianfei = $newmodel->GetBaoxianFei($getbxf_data);
		  }else{
			  //如果是成品 那么保险费就是0
			  $baoxianfei = 0;
		  }
		 
          
          $price2 = '';
          $other_price = '';
          $price='';
          $price_y = '';
          
          if($type_t==2){
          	    $sql="select buyout_price,activity_price from warehouse_biaoqian where goods_id=".$goods_id;
          	   	$biaoqian_arr=$this->db()->getRow($sql);
          	   	if(!empty($biaoqian_arr)){
          	   		$price=$biaoqian_arr['buyout_price'];
          	   		if($price){
	          	   		if (substr($line['caizhi'],0,3) == '18K'){
	          	   			$other_price = $line['jinzhong'] * 400 + $price + 500 ;
	          	   			$other_price_string = "PT定制价￥" . ceil($other_price);
	          	   		}elseif ($line['caizhi'] == '铂Pt950' || $line['caizhi'] == 'PT950'){
	          	   			$other_price = $price - $line['jinzhong'] * 250;
	          	   			$other_price_string = "18K金定制价￥" . ceil($other_price);
	          	   		}else{
	          	   			$other_price_string = "";
	          	   		}
          	   		}
          	   		
          	   		$price_y=$biaoqian_arr['activity_price'];
                    if ($type2==1) {
                            $price2 = $price;
                          $price = substr($price,0,-2).'99';
                      }
          	   	}else{
                    $error = "没有查到该货号：".$goods_id."的指定价格信息，请核实后再做打印！";
                    $this->error_csv($error);
                }
                if(bccomp($left=$price,$right=$line['mingyichengben']) < 1){
	                $error = "指定打标价低于成本价 货号:$goods_id 指定打标价: $price  名义成本:{$line['mingyichengben']}  加价率: $jiajialv 加价系数: $jiajianum   重新上传价格或联系技术部！";	             
                    $this->error_csv($error);	 
                  
                }
          	
          }else{
           if($line['xianzaixiaoshou'] != 'err_AB' && $line['xianzaixiaoshou'] != 'err_WM' ){
               //如果商品产品线镶嵌类，金托类型是空托：标签价=（名义成本+保险费）*加价率+系数
              if (in_array($line['product_type'], $xiangqian_product_type) && ($line['tuo_type'] == 3) || $line['tuo_type'] == 2){

                  $price = round(($line['mingyichengben']+$baoxianfei)*trim($jiajialv) + trim($jiajianum));
                  //file_put_contents('c:/a.txt', 'baoxianfee:'.$baoxianfei.'jiajialv:'.$jiajialv.'num:'.$jiajianum);
              }else{
                  $price = round($line['mingyichengben']*trim($jiajialv) + trim($jiajianum));
              }
              if(!empty($goods_id) && $type_t == 1){
                $sql = "select `bg`.`shijia` from `warehouse_bill` `wb` inner join `warehouse_bill_goods` `bg` on `wb`.`id` = `bg`.`bill_id` where `wb`.`bill_type` = 'P' and `bg`.`goods_id` = '{$goods_id}' and (`wb`.from_bill_id=0 or `wb`.from_bill_id is null) order by `wb`.`id` desc  limit 0,1";
                $pifajia_price = $this->db()->getOne($sql);
                if($pifajia_price){
                    $price = round($pifajia_price*trim($jiajialv) + trim($jiajianum));
                }
              }
               if (substr($line['caizhi'],0,3) == '18K'){
                  $other_price = $line['jinzhong'] * 400 + $price + 500 ;
                  $other_price_string = "PT定制价￥" . ceil($other_price);
              }elseif ($line['caizhi'] == '铂Pt950' || $line['caizhi'] == 'PT950'){
                  $other_price = $price - $line['jinzhong'] * 250;
                  $other_price_string = "18K金定制价￥" . ceil($other_price);
              }else{
                  $other_price_string = "";
              }
              if ($type2==1) {
                  $price2 = $price;
                  $price = substr($price,0,-2).'99';
              }else{
                  $price2 = '';
              }
          }else{
              $price = '待核实';
          }
          
        

	          file_put_contents('/data/www/cuteframe_boss/logs/daobiao.log', date("Y-m-d H:i:s")."\t".$msg."\n");
	          if($jiajialv <=1   || bccomp($left=$price,$right=$line['mingyichengben']) < 1){
	              $error = "指定打标价低于成本价$goods_id price: $price  名义成本:{$line['mingyichengben']} baoxianfee: $baoxianfei jiajialv: $jiajialv num: $jiajianum   重试或联系技术部！";	             
                  $this->error_csv($error);	              
	          }
         }  
        if($line['zhushi']=='锆石' && $line['goods_sn'] != ''){
            $sql = "select TRIM(BOTH ',' FROM `attribute_value`)
            from `front`.`rel_style_attribute` 
            where `attribute_id` = 1 
            and `attribute_value` <> ''
            and `style_id` in(select `style_id` from `front`.`base_style_info` where `check_status` = 3 and `style_sn` = '".$line['goods_sn']."')";
            $xiangkou_id = $this->db()->getOne($sql);
            if($xiangkou_id != ''){
                $sql = "select `att_value_name` 
                from `front`.`app_attribute_value`  
                where att_value_id in(".$xiangkou_id.")";
                $xiangkouArr = $this->db()->getAll($sql);
                $rongko = implode(",", array_column($xiangkouArr, 'att_value_name'));
            }else{
                $rongko = isset($rongko_list[$line['goods_sn']])?$rongko_list[$line['goods_sn']]:'';
            }
        }else{
        	$rongko='';
        }

        //直营店钻石颜色、净度抓取规则、
        if(!in_array($line['cat_type1'], array('裸石','彩钻'))){
            $zhiyingdian_yanse = array('香槟'=>'',
                                        '变色'=>'',
                                        '黄'=>'',
                                        'D'=>'D-E',
                                        'D-E'=>'D-E',
                                        'G'=>'F-G',
                                        'H+'=>'H',
                                        'H-I'=>'H',
                                        'L'=>'K-L',
                                        'SI2'=>'<N',
                                        '红'=>'',
                                        '橘色'=>'OR',
                                        '蓝色'=>'BLU',
                                        '棕色'=>'BRO',
                                        '白色'=>'白色',
                                        '粉色'=>'PI',
                                        'F-G'=>'F-G',
                                        '绿色'=>'GR',
                                        'N'=>'M-N',
                                        '紫色'=>'PU',
                                        'E-F'=>'D-E',
                                        'G-H'=>'F-G',
                                        'H'=>'H',
                                        'M'=>'M-N',
                                        '红色'=>'RE',
                                        '黄色'=>'YE',
                                        '黑色'=>'BLA',
                                        '蓝'=>'',
                                        '绿'=>'',
                                        '格雷恩'=>'',
                                        '粉'=>'',
                                        '紫'=>'',
                                        'E'=>'D-E',
                                        'I-J'=>'I-J',
                                        'J-K'=>'I-J',
                                        'K'=>'K-L',
                                        'M-N'=>'M-N',
                                        'S-T'=>'<N',
                                        '无'=>'无',
                                        '蓝紫色'=>'',
                                        '黑'=>'',
                                        '金色'=>'',
                                        '混色'=>'',
                                        '橙'=>'',
                                        'F'=>'F-G',
                                        'I'=>'I-J',
                                        'J'=>'I-J',
                                        'K-L'=>'K-L',
                                        'Q-R'=>'<N',
                                        '白'=>'白',
                                        '不分级'=>'');
            $zhiyingdian_jingdu = array('I'=>'',
                                        'IF'=>'IF',
                                        'P'=>'',
                                        'P2'=>'',
                                        'SI2'=>'SI',
                                        'SI7'=>'SI',
                                        'I2'=>'',
                                        'I3'=>'',
                                        'SI8'=>'SI',
                                        'SI'=>'SI',
                                        'VVS'=>'VVS',
                                        'VVS1'=>'VVS1',
                                        'P3'=>'',
                                        'VS'=>'VS',
                                        'VVS2'=>'VVS2',
                                        '无'=>'',
                                        'VS2'=>'VS2',
                                        'I1'=>'',
                                        'SI3'=>'SI',
                                        'VS1'=>'VS1',
                                        'FL'=>'FL',
                                        'SI4'=>'SI',
                                        '完美无瑕'=>'LC',
                                        '不分级'=>'',
                                        'P1'=>'',
                                        'SI1'=>'SI');

            $line['zhiying_yanse'] = isset($zhiyingdian_yanse[$line['zhushiyanse']])?$zhiyingdian_yanse[$line['zhushiyanse']]:'';
            $line['zhiying_jingdu']= isset($zhiyingdian_jingdu[$line['zhushijingdu']])?$zhiyingdian_jingdu[$line['zhushijingdu']]:'';
        }else{
            $line['zhiying_yanse'] = $line['zhushiyanse'];
            $line['zhiying_jingdu'] = $line['zhushijingdu'];
        }
          
          $line['goods_sn'] = preg_replace('/^[(\xc2\xa0)|\s]+/', '', $line['goods_sn']); //去掉中文空格
          $line['fuchengse'] = '';
          $line['fuchengsezhong'] = '';

          $tihCz = array(
            '无' => '',
            '其它' => '',
            '其他' => '',
            '24K' =>'足金',
            '千足金银'    =>'足金银',
            'S990'    =>'足银',
            '千足银' =>'足银',            
            '千足金' =>'足金',
            'PT900'   =>'铂900',
            'PT999'   =>'铂999',
            'PT990'   =>'铂990',
            'PT950'   =>'铂950',
            '18K玫瑰黄'=>'18K金',
            '18K玫瑰白'=>'18K金',
            '18K玫瑰金'=>'18K金',
            '18K黄金'=>'18K金',
            '18K白金'=>'18K金',
            '18K黑金'=>'18K金',
            '18K彩金'=>'18K金',
            '18K红'=>'18K金',
            '18K黄白'=>'18K金',
            '18K分色'=>'18K金',
            '18K黄'=>'18K金',
            '18K白'=>'18K金',
            '9K玫瑰黄'=>'9K金',
            '9K玫瑰白'=>'9K金',
            '9K玫瑰金'=>'9K金',
            '9K黄金'=>'9K金',
            '9K白金'=>'9K金',
            '9K黑金'=>'9K金',
            '9K彩金'=>'9K金',
            '9K红'=>'9K金',
            '9K黄白'=>'9K金',
            '9K分色'=>'9K金',
            '9K黄'=>'9K金',
            '9K白'=>'9K金',
            '10K玫瑰黄'=>'10K金',
            '10K玫瑰白'=>'10K金',
            '10K玫瑰金'=>'10K金',
            '10K黄金'=>'10K金',
            '10K白金'=>'10K金',
            '10K黑金'=>'10K金',
            '10K彩金'=>'10K金',
            '10K红'=>'10K金',
            '10K黄白'=>'10K金',
            '10K分色'=>'10K金',
            '10K黄'=>'10K金',
            '10K白'=>'10K金', 
            '14K玫瑰黄'=>'14K金',
            '14K玫瑰白'=>'14K金',
            '14K玫瑰金'=>'14K金',
            '14K黄金'=>'14K金',
            '14K白金'=>'14K金',
            '14K黑金'=>'14K金',
            '14K彩金'=>'14K金',
            '14K红'=>'14K金',
            '14K黄白'=>'14K金',
            '14K分色'=>'14K金',
            '14K黄'=>'14K金',
            '14K白'=>'14K金',
            '19K黄'=>'19K金',
            '19K白'=>'19K金',
            '19K玫瑰黄'=>'19K金',
            '19K玫瑰白'=>'19K金',
            '19K玫瑰金'=>'19K金',
            '19K黄金'=>'19K金',
            '19K白金'=>'19K金',
            '19K黑金'=>'19K金',
            '19K彩金'=>'19K金',
            '19K红'=>'19K金',
            '19K黄白'=>'19K金',
            '19K分色'=>'19K金',
            '20K黄'=>'20K金',
            '20K白'=>'20K金',
            '20K玫瑰黄'=>'20K金',
            '20K玫瑰白'=>'20K金',
            '20K玫瑰金'=>'20K金',
            '20K黄金'=>'20K金',
            '20K白金'=>'20K金',
            '20K黑金'=>'20K金',
            '20K彩金'=>'20K金',
            '20K红'=>'20K金',
            '20K黄白'=>'20K金',
            '20K分色'=>'20K金',
            '21K黄'=>'21K金',
            '21K白'=>'21K金',
            '21K玫瑰黄'=>'21K金',
            '21K玫瑰白'=>'21K金',
            '21K玫瑰金'=>'21K金',
            '21K黄金'=>'21K金',
            '21K白金'=>'21K金',
            '21K黑金'=>'21K金',
            '21K彩金'=>'21K金',
            '21K红'=>'21K金',
            '21K黄白'=>'21K金',
            '21K分色'=>'21K金',
            '22K黄'=>'22K金',
            '22K白'=>'22K金',
            '22K玫瑰黄'=>'22K金',
            '22K玫瑰白'=>'22K金',
            '22K玫瑰金'=>'22K金',
            '22K黄金'=>'22K金',
            '22K白金'=>'22K金',
            '22K黑金'=>'22K金',
            '22K彩金'=>'22K金',
            '22K红'=>'22K金',
            '22K黄白'=>'22K金',
            '22K分色'=>'22K金',
            '23K黄'=>'23K金',
            '23K白'=>'23K金',
            '23K玫瑰黄'=>'23K金',
            '23K玫瑰白'=>'23K金',
            '23K玫瑰金'=>'23K金',
            '23K黄金'=>'23K金',
            '23K白金'=>'23K金',
            '23K黑金'=>'23K金',
            '23K彩金'=>'23K金',
            '23K红'=>'23K金',
            '23K黄白'=>'23K金',
            '23K分色'=>'23K金',
            'S925黄'=>'S925',
            'S925白'=>'S925',
            'S925玫瑰黄'=>'S925',
            'S925玫瑰白'=>'S925',
            'S925玫瑰金'=>'S925',
            'S925黄金'=>'S925',
            'S925白金'=>'S925',
            'S925黑金'=>'S925',
            'S925彩金'=>'S925',
            'S925红'=>'S925',
            'S925黄白'=>'S925',
            'S925分色'=>'S925',
            'S925'    =>'银925'
            );

        $stone_arr = array('红宝'=>'红宝石',
            '珍珠贝'=>'贝壳',
            '白水晶'=>'水晶',
            '粉晶'=>'水晶',
            '茶晶'=>'水晶',
            '紫晶'=>'水晶',
            '紫水晶'=>'水晶',
            '黄水晶'=>'水晶',
            '彩兰宝'=>'蓝宝石',
            '彩色蓝宝'=>'蓝宝石',
            '蓝晶'=>'水晶',
            '黄晶'=>'水晶',
            '柠檬晶'=>'水晶',
            '红玛瑙'=>'玛瑙',
            '黑玛瑙'=>'玛瑙',
            '奥泊'=>'宝石',
            '黑钻'=>'钻石',
            '琥铂'=>'琥铂',
            '虎晴石'=>'宝石',
            '大溪地珍珠'=>'珍珠',
            '大溪地黑珍珠'=>'珍珠',
            '淡水白珠'=>'珍珠',
            '淡水珍珠'=>'珍珠',
            '南洋白珠'=>'珍珠',
            '南洋金珠'=>'珍珠',
            '海水香槟珠'=>'珍珠',
            '混搭珍珠'=>'珍珠',
            '蓝宝'=>'蓝宝石',
        	'宝石石'=>'宝石',
            '黄钻'=>'钻石');

        $stone_arr_s = array('红玛瑙'=>'玛瑙',
                    '和田玉'=>'和田玉',
                    '星光石'=>'星光石',
                    '莹石'=>'莹石',
                    '捷克陨石'=>'捷克陨石',
                    '绿松石'=>'绿松石',
                    '欧泊'=>'欧泊',
                    '砗磲'=>'砗磲',
                    '芙蓉石'=>'芙蓉石',
                    '坦桑石'=>'坦桑石',
                    '南洋白珠'=>'珍珠',
                    '大溪地珍珠'=>'珍珠',
                    '南洋金珠'=>'珍珠',
                    '无'=>'',
                    '黑玛瑙'=>'玛瑙',
                    '托帕石'=>'托帕石',
                    '橄榄石'=>'橄榄石',
                    '红纹石'=>'红纹石',
                    '蓝宝石'=>'蓝宝石',
                    '祖母绿'=>'祖母绿',
                    '黄水晶'=>'水晶',
                    '玉髓'=>'玉髓',
                    '异形钻'=>'钻石',
                    '粉红宝'=>'粉红宝',
                    '彩钻'=>'钻石',
                    '尖晶石'=>'尖晶石',
                    '石榴石'=>'石榴石',
                    '贝壳'=>'贝壳',
                    '珍珠贝'=>'贝壳',
                    '圆钻'=>'钻石',
                    '碧玺'=>'碧玺',
                    '葡萄石'=>'葡萄石',
                    '拉长石（月光石）'=>'拉长石（月光石）',
                    '舒俱来石'=>'舒俱来石',
                    '琥珀'=>'琥珀',
                    '黑钻'=>'钻石',
                    '混搭珍珠'=>'珍珠',
                    '碧玉'=>'',
                    '紫龙晶'=>'紫龙晶',
                    '玛瑙'=>'玛瑙',
                    '青金石'=>'青金石',
                    '虎睛石（木变石）'=>'虎睛石（木变石）',
                    '黑曜石'=>'黑曜石',
                    '珍珠'=>'珍珠',
                    '红宝石'=>'红宝石',
                    '其它'=>'',
                    '海蓝宝'=>'海蓝宝石',
                    '水晶'=>'水晶',
                    '翡翠'=>'翡翠',
                    '孔雀石'=>'孔雀石',
                    '东陵玉'=>'东陵玉',
                    '锂辉石'=>'锂辉石',
                    '珊瑚'=>'珊瑚',
                    '海水香槟珠'=>'珍珠',
                    '淡水白珠'=>'珍珠',
                    '锆石'=>'合成立方氧化锆',
                    '月光石'=>'月光石');

$tihCt = array('耳环'=>'耳饰',
                    '吊坠'=>'吊坠',
                    '裸石（镶嵌物）'=>'裸石（镶嵌物）',
                    '女戒'=>'戒指',
                    '套装'=>'饰品',
                    '纪念币'=>'纪念币',
                    '素料类'=>'素料类',
                    '彩宝'=>'彩宝',
                    '彩钻'=>'彩钻',
                    '男戒'=>'戒指',
                    '赠品'=>'饰品',
                    '胸针'=>'饰品',
                    '脚链'=>'脚链',
                    '情侣戒'=>'戒指',
                    '金条'=>'金条',
                    '其它'=>'饰品',
                    '摆件'=>'摆件',
                    '项链'=>'项链',
                    '多功能款'=>'饰品',
                    '手链'=>'手链',
                    '耳钩'=>'耳饰',
                    '手表'=>'手表',
                    '固定资产'=>'固定资产',
                    '长链'=>'饰品',
                    '裸石（统包货）'=>'裸石（统包货）',
                    '裸石（珍珠）'=>'裸石（珍珠）',
                    '套戒'=>'戒指',
                    '领带夹'=>'领带夹',
                    '手镯'=>'手镯',
                    '原材料'=>'原材料',
                    '袖口钮'=>'饰品',
                    '耳钉'=>'耳饰',
                    '物料'=>'物料',
                    '其他'=>'饰品',
                    '耳饰'=>'耳饰');

          //$tihCt = array(
            //'男戒'=>'戒指',
            //'女戒'=>'戒指',
            //'情侣戒'=>'戒指'
           // );

          if($line['caizhi'] != ''){
            $line['caizhi'] = str_replace(array_keys($tihCz), array_values($tihCz), $line['caizhi']);
          }
          if($line['cat_type1'] != ''){
            $line['cat_type1'] = str_replace(array_keys($tihCt), array_values($tihCt), $line['cat_type1']);
          }
          if($line['goods_name'] != ''){
            //$line['goods_name'] = str_replace("千", "", $line['goods_name']);
            $line['goods_name'] = str_replace('锆石','合成立方氧化锆',$line['goods_name']);
            $line['goods_name'] = str_replace(array_keys($tihCz), array_values($tihCz), $line['goods_name']);
            $line['goods_name'] = str_replace(array_keys($tihCt), array_values($tihCt), $line['goods_name']);
            $line['goods_name'] = str_replace(array_keys($stone_arr_s), array_values($stone_arr_s), $line['goods_name']);
          }

          //if($line['zhushi'] != ''){
            //$line['zhushi'] = str_replace('锆石','合成立方氧化锆',$line['zhushi']);
          //}

          if($line['zhushi'] != ''){
                    $line['zhushi'] = str_replace(array_keys($stone_arr_s), array_values($stone_arr_s),$line['zhushi']);
                }
                if($line['fushi'] != ''){
                    $line['fushi'] = str_replace(array_keys($stone_arr_s), array_values($stone_arr_s),$line['fushi']);
                }

                if($line['zhushi'] != ''){
                    $shitou = $line['zhushi'];
                }else{
                    $shitou = $line['fushi'];
                }
                
                $line['goods_name'] = $line['caizhi'].$shitou.$line['cat_type1'];
                
                $line['zongzhong'] = !empty($line['zongzhong'])?$line['zongzhong'].'g':'';

                $biaoqianjia = SYS_SCOPE == 'zhanting' && $label_price == 1 ? $line['biaoqianjia']:'--';

		  //查询款号是否定制
          $sql = "select is_made from front.base_style_info where style_sn='{$line['goods_sn']}'";
          $is_made = $this->db()->getOne($sql);
          $is_made = $is_made ? '定':'';
          if($daying_type == 2){
                $csv_body .= "<tr>";
                $csv_body .="<td>" . $line['goods_id'] . "</td>";
                $csv_body .="<td>" . $line['goods_sn'] . "</td>";
                $csv_body .="<td>" . $line['gemx_zhengshu'] . "</td>";
                $csv_body .="<td>" . $line['shoucun'] . "</td>";
                $csv_body .="<td>" . $line['changdu'] . "</td>";
                $csv_body .="<td>" . $line['zhushilishu'] . "</td>";
                $csv_body .="<td>" . $line['zuanshidaxiao'] . "</td>";
                $csv_body .="<td>" . $line['fushilishu'] . "</td>";
                $csv_body .="<td>" . $line['fushizhong'] . "</td>";
                $csv_body .="<td>" . "</td>";       //加工商编号
                $csv_body .="<td>" . $line['zongzhong'] . "</td>";
                $csv_body .="<td>" . $line['jingdu'] . "</td>";
                $csv_body .="<td>" . $line['yanse'] . "</td>";
                $csv_body .="<td>" .  $line['zhengshuhao'] . "</td>";
                $csv_body .="<td>" . $line['guojizhengshu'] . "</td>";
                $csv_body .="<td>" . $line['zhushiqiegong'] . "</td>";
                $csv_body .="<td>" . "</td>";                //标签备注
                $csv_body .="<td>" . $line['zhushi'] . "</td>";
                $csv_body .="<td>" . $line['fushi'] . "</td>";
                $csv_body .="<td>" . $line['caizhi']. "</td>";
                $csv_body .="<td>" . $line['product_type'] . "</td>";
                $csv_body .="<td>" . $line['cat_type1'] . "</td>";
                $csv_body .="<td>" . $line['goods_name'] . "</td>";
                $csv_body .="<td>" . $line['shi2'] . "</td>";            //石3
                $csv_body .="<td>" . $line['shi2lishu']. "</td>";            //石3粒数
                $csv_body .="<td>" . $line['shi2zhong']. "</td>";            //石3重
                $csv_body .="<td>" . "</td>";           //石4
                $csv_body .="<td>" . "</td>";           //石4粒数
                $csv_body .="<td>" . "</td>";               //石4重
                $csv_body .="<td>" . "</td>";       //石5
                $csv_body .="<td>" . "</td>";           //石5粒数
                $csv_body .="<td>" . "</td>";           //石5重
                $csv_body .="<td>" . $line['jinzhong'] . "</td>";
                $csv_body .="<td>" . $line['fuchengse'] . "</td>";
                $csv_body .="<td>" . $line['fuchengsezhong']. "</td>";
                $csv_body .="<td>" . $line['mairugongfei'] . "</td>";
                $csv_body .="<td>" . $line['jijiagongfei']. "</td>";
                $csv_body .="<td>" . $jiajialv. "</td>";
                //$csv_body .="<td>" . $line['jiajialv']. "</td>";
                $csv_body .="<td>" . $line['zuixinlingshoujia'] . "</td>";
                $csv_body .="<td>" . $line['mo_sn']. "</td>";
                $csv_body .="<td>" . $line['pinpai'] . "</td>";
                $csv_body .="<td>" . "</td>";           //证书数量
                $csv_body .="<td>" . $line['peijianshuliang'] . "</td>";
                $csv_body .="<td>" . "</td>";               //时尚款
                $csv_body .="<td>" .(in_array($base_style,$xilie)?"基":"") . "</td>";           //系列
                $csv_body .="<td>" . "</td>";           //属性
                $csv_body .="<td>" . "</td>";           //类别
                $csv_body .="<td>" . $line['chengbenjia'] . "</td>";
                $csv_body .="<td>" . $line['addtime'] . "</td>";
                $csv_body .="<td>" . "</td>";           //加价率代码
                $csv_body .="<td>" . "</td>";           //主石粒重
                $csv_body .="<td>" . "</td>";           //副石粒重
                $csv_body .="<td>" . "</td>";           //标签手寸
                $csv_body .="<td>" . $line['ziyin']. "</td>";                //ziyin
                $csv_body .="<td>" . '￥'.$line['zuixinlingshoujia'] . "</td>";
                $csv_body .="<td>" . $line['mingyichengben'] . "</td>";
                $csv_body .="<td>" . $price . "</td>";
                $csv_body .="<td>" . $line['yikoujia'] . "</td>";
                $csv_body .="<td>" . $line['biaojia'] . "</td>";
                $csv_body .="<td>" . $other_price_string . "</td>";
                $csv_body .="<td>" .""."</td>";               // A
                $csv_body .="<td>" .$line['goods_name']. "</td>";   // B
                $csv_body .="<td>" .$deal_num->get_c_col_value($line, $price). "</td>"; // C
                $csv_body .="<td>" .$deal_num->get_d_col_value($line). "</td>"; // d
                $csv_body .="<td>" .$deal_num->get_e_col_value($line). "</td>"; // e
                $csv_body .="<td>" .$deal_num->get_f_col_value($line). "</td>"; // f
                $csv_body .="<td>" .$deal_num->get_g_col_value($line). "</td>"; // f
                $csv_body .="<td>" .$deal_num->get_h_col_value($line, $other_price). "</td>";   // h
                $csv_body .="<td>" .$deal_num->get_i_col_value($line). "</td>"; // i
                $csv_body .="<td>" .$deal_num->get_hb_g_col_value($line). "</td>";  // hb_f
                $csv_body .="<td>" .$deal_num->get_hb_h_col_value($line, $other_price). "</td>";    // hb_g
                $csv_body .="<td>" .$rongko. "</td>";   //可做镶口范围
                $csv_body .="<td>" .$price_y. "</td>";    //活动价
                $csv_body .="<td>" .$price2. "</td>";//原价
                $csv_body .="<td>" .($line['jietuoxiangkou']*100). "</td>";//镶口
                $csv_body .="<td>" .$mairugongfeidanjia."</td>"; //买入工费单价(黄金工费)
                $csv_body .="<td>" . $line['zhiying_yanse'] ."</td>";//直营店钻石颜色
                $csv_body .="<td>" . $line['zhiying_jingdu'] ."</td>";//直营店钻石净度
                $csv_body .="<td>" . $line['zongzhong'] ."</td>";//总重g
                $csv_body .="<td>" .$biaoqianjia."</td>";//展厅标签价
                $csv_body .="<td>" .$line['zhengshuleibie']."</td>";//证书类型
                $csv_body .="<td>" .$is_made."</td></tr>";//是否支持定制
          }else{
              $content .=
              "\"" . $line['goods_id'] . "\"," .
              "\"" . $line['goods_sn'] . "\"," .
              "\"" . $line['gemx_zhengshu'] . "\"," .
              "\"" . $line['shoucun'] . "\"," .
              "\"" . $line['changdu'] . "\"," .
              "\"" . $line['zhushilishu'] . "\"," .
              "\"" . $line['zuanshidaxiao'] . "\"," .
              "\"" . $line['fushilishu'] . "\"," .
              "\"" . $line['fushizhong'] . "\"," .
              "\"" .  "\"," . 		//加工商编号
              "\"" . $line['zongzhong'] . "\"," .
              "\"" . $line['jingdu'] . "\"," .
              "\"" . $line['yanse'] . "\"," .
              "\"" .  $line['zhengshuhao'] . '"' . "," .
              "\"" . $line['guojizhengshu'] . "\"," .
              "\"" . $line['zhushiqiegong'] . "\"," .
              "\"" . "\"," .  				//标签备注
              "\"" . $line['zhushi'] . "\"," .
              "\"" . $line['fushi'] . "\"," .
              "\"" . $line['caizhi']. "\"," .
              "\"" . $line['product_type'] . "\"," .
              "\"" . $line['cat_type1'] . "\"," .
              "\"" . $line['goods_name'] . "\"," .
              "\"" . $line['shi2'] . "\"," . 			//石3
              "\"" . $line['shi2lishu']. "\"," . 			//石3粒数
              "\"" . $line['shi2zhong']. "\"," .  			//石3重
              "\"" .  "\"," . 			//石4
              "\"" .  "\"," . 			//石4粒数
              "\"" .  "\"," . 				//石4重
              "\"" .  "\"," . 		//石5
              "\"" .  "\"," . 			//石5粒数
              "\"" .  "\"," . 			//石5重
              "\"" . $line['jinzhong'] . "\"," .
              "\"" . $line['fuchengse'] . "\"," .
              "\"" . $line['fuchengsezhong']. "\"," .
              "\"" . $line['mairugongfei'] . "\"," .
              "\"" . $line['jijiagongfei']. "\"," .
              "\"" . $jiajialv. "\"," .
              //"\"" . $line['jiajialv']. "\"," .
              "\"" . $line['zuixinlingshoujia'] . "\"," .
              "\"" . $line['mo_sn']. "\"," .
              "\"" . $line['pinpai'] . "\"," .
              "\"" .  "\"," . 			//证书数量
              "\"" . $line['peijianshuliang'] . "\"," .
              "\"" .  "\"," . 				//时尚款
              "\"".(in_array($base_style,$xilie)?"基":"") . "\",".			//系列
              "\"" .  "\"," . 			//属性
              "\"" .  "\"," . 			//类别
              "\"" . $line['chengbenjia'] . "\"," .
              "\"" . $line['addtime'] . "\"," .
              "\"" .  "\"," . 			//加价率代码
              "\"" .  "\"," . 			//主石粒重
              "\"" .  "\"," . 			//副石粒重
              "\"" .  "\"," . 			//标签手寸
              "\"" . $line['ziyin']. "\"," . 				//ziyin
              "\"" . '￥'.$line['zuixinlingshoujia'] . "\"," .
              "\"" . $line['mingyichengben'] . "\"," .
              "\"" . $price . "\"," .
              "\"" . $line['yikoujia'] . "\"," .
              "\"" . $line['biaojia'] . "\"," .
              "\"" . $other_price_string . "\",".
              ",".					// A
              "\"".$line['goods_name']."\",".	// B
              "\"".$deal_num->get_c_col_value($line, $price)."\",".	// C
              "\"".$deal_num->get_d_col_value($line)."\",".	// d
              "\"".$deal_num->get_e_col_value($line)."\",".	// e
              "\"".$deal_num->get_f_col_value($line)."\",".	// f
              "\"".$deal_num->get_g_col_value($line)."\",".	// f
              "\"".$deal_num->get_h_col_value($line, $other_price)."\",".	// h
              "\"".$deal_num->get_i_col_value($line)."\",".	// i
              "\"".$deal_num->get_hb_g_col_value($line)."\",".	// hb_f
              "\"".$deal_num->get_hb_h_col_value($line, $other_price)."\",".	// hb_g
              "\"".$rongko."\",".
              "\"".$price_y."\",".	//活动价
              "\"".$price2."\",".	//原价
              "\"".($line['jietuoxiangkou']*100)."\",".	//镶口
              "\"".$mairugongfeidanjia."\",".   //买入工费单价(黄金工费)
              "\"".$line['zhiying_yanse']."\",".    //直营店颜色
              "\"".$line['zhiying_jingdu']."\",".    //直营店净度
              "\"".$line['zongzhong']."\",".    //总重g、
              "\"".$biaoqianjia."\",".    //展厅标签价
              "\"".$line['zhengshuleibie']."\",".    //证书类型
              "\"".$is_made."\"\r\n";    //是否支持定制
        }
    }
    if($daying_type == 2){
        $csv_body .= "</table>";
        echo $csv_body;exit;
    }else{
        header("Content-type:text/csv;charset=gbk");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . "tiaoma.csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo iconv("utf-8", "gbk//IGNORE", $content);
        exit;
    }
}

/*婚博会专用导出*/
function hbhpageList ($where,$page,$pageSize=10,$useCache=true , $is_down = false)
		{
		$sql = "SELECT `g`.*,`b`.`bill_no`,`b`.`bill_note`,`b`.`check_time` FROM
		`".$this->table()."` AS g LEFT JOIN warehouse_shipping.warehouse_bill_goods bg 
		ON g.goods_id = bg.goods_id 
		LEFT JOIN warehouse_shipping.warehouse_bill b 
		ON bg.bill_id = b.id AND b.bill_status =2
		AND b.bill_type =  'M' ";
		$str = "";
		if($where['goods_id'] != "")
		{
			//add by zhangruiying 去除用户不小心输入或粘贴的空白字符和中文,号替换
			$where['goods_id']=preg_replace("/[sv]+/",'',$where['goods_id']);
			$where['goods_id']=str_replace(" ",',',$where['goods_id']);
			$where['goods_id']=str_replace("，",',',$where['goods_id']);
			//add end
			$item =explode(",",$where['goods_id']);
			$goodsid = "";
			foreach($item as $key => $val) {
				if ($val != '') {
					if($goodsid){
						$goodsid .= ",'".trim($val)."'";
					}else{
						$goodsid .= "'".trim($val)."'";
					}
				}
			}
			$where['goods_id'] = $goodsid;
			//$str .= " goods_id like \"%".addslashes($where['goods_id'])."%\"  AND ";
			$str .= " g.goods_id in (".$where['goods_id'].") AND ";
		}
		if($where['style_sn'] != "")
		{
                        $str .= $this->formatString($where['style_sn'],g.'goods_sn');

			//$str .= " goods_sn = '".$where['style_sn']."' AND ";
		}
		if($where['put_in_type'] !== "")
		{
			$str .= "g.put_in_type = '".$where['put_in_type']."' AND ";
		}
		if($where['weixiu_status'] !== "")
		{
			$str .= "g.weixiu_status = '".$where['weixiu_status']."' AND ";
		}
		if($where['is_on_sale'] !== "")
		{
			$str .= "g.is_on_sale = ".$where['is_on_sale']." AND ";
		}

		if($where['caizhi'] !="" && $where['jinse'] !== ""){
		    $zhuchengse = $where['caizhi'].str_replace("无","",$where['jinse']);
		    $str .= "caizhi = '".$zhuchengse."' AND ";
		}else if($where['caizhi'] !== "" && $where['jinse'] == ""){		    
			$str .= "caizhi like '".$where['caizhi']."%' AND ";
		}else if($where['caizhi'] =="" && $where['jinse'] !== ""){
		    $str .= "caizhi like '%".$where['jinse']."' AND ";
		}
		
		if($where['company_id'] !== "")
		{
			$str .= "g.company_id = '".$where['company_id']."' AND ";
		}
		if($where['cat_type'] !== "" && $where['cat_type'] != "全部")
		{
			$str .= "g.cat_type = '".$where['cat_type']."' AND ";
		}
		if($where['cat_type1'] !== "" && $where['cat_type1'] != "全部")
		{
			$str .= "g.cat_type1 = '".$where['cat_type1']."' AND ";
		}
		if($where['warehouse_id'] !=="" )
		{
			$str .= "g.`warehouse_id` = '".$where['warehouse_id']."' AND ";// g.warehouse like '%婚博会柜面%'
		}
		//new add
		if($where['zhengshuhao'] !== "")
		{
			$str .= "g.zhengshuhao = '".$where['zhengshuhao']."' AND ";
		}
		if ($where['order_goods_ids'] !== '')
		{
			if($where['order_goods_ids'] == '1'){
				$str .= " g.order_goods_id <> 0 AND ";//已经绑定
			}else{
				$str .= " (g.order_goods_id = '0' or g.order_goods_id='') AND ";//未绑定
			}
		}
		if($where['shoucun'] !== '')
		{
			$str .= " g.shoucun = '".$where['shoucun']."' AND ";
		}
		if ($where['kucunstart'] !== '')
		{
			$str .= " g.addtime >= '".$where['kucunstart']." 00:00:00' AND ";
		}
		if ($where['kucunend'] !== '')
		{
			$str .= " g.addtime <= '".$where['kucunend']." 23:59:59' AND ";
		}
		if($where['processor'] !== '')
		{
			$str .= " g.prc_id='".$where['processor']."' AND ";
		}
		if ($where['buchan'] !== '')
		{
			$str .= " g.buchan_sn='".$where['buchan']."' AND ";
		}
		if ($where['mohao'] !== '')
		{
			$str .= "g.mo_sn='".$where['mohao']."' AND ";
		}
		if ($where['zhushi'] !== '')
		{
			$str .= " g.zhushi='".$where['zhushi']."' AND ";
		}
		if ($where['zhengshuleibie'] !== '')
		{
			$str .= " g.zhengshuleibie='".$where['zhengshuleibie']."' AND ";
		}
		//差一个主成色重
		if($where['jinzhong_begin'] !== "") {
			$str .= " g.jinzhong >=".$where['jinzhong_begin']." AND ";
		}
		if($where['jinzhong_end'] !== "") {
			$str .= " g.jinzhong <=".$where['jinzhong_end']." AND ";
		}
		if($where['zs_color'] !== "")
		{
			$str .= "g.zhushiyanse = '".$where['zs_color']."' AND ";
		}
		if ($where['zs_clarity'] !== "")
		{
			$str .= "g.zhushijingdu = '".$where['zs_clarity']."' AND ";
		}
		if ($where['jinshi_type'] !== "") {
			$str .= " g.ziyin='".$where['jinshi_type']."' AND ";
		}
		if ($where['jintuo_type'] !== "")
		{
			$str .= " g.tuo_type='".$where['jintuo_type']."' AND ";
		}
		if ($where['jiejia'] !== "")
		{
			$str .= " g.jiejia = '".$where['jiejia']."' AND ";
		}
		if($where['guiwei'] == '1')
		{
			$str .= " ( g.box_sn = '' OR g.box_sn = '0-00-0-0') AND ";
		}elseif($where['guiwei'] == '2')
		{
			$str .= " ( g.box_sn != '' AND g.box_sn != '0-00-0-0') AND ";
		}
		if ($where['chanpinxian'] !== "")
		{
			$str .= "g.product_type='".$where['chanpinxian']."' AND ";
		}
		if ($where['chanpinxian1'] !== "")
		{
			$str .= "g.product_type1='".$where['chanpinxian1']."' AND ";
		}
		if ($where['zhushi_begin'] !== "") {
			$str .= "zuanshidaxiao >= '".$where['zhushi_begin']."' AND ";
		}
		if ($where['zhushi_end'] !== "") {
			$str .= "g.zuanshidaxiao <= '".$where['zhushi_end']."' AND ";
		}
		if($where['weixiu_company_id'] !== "")
		{
			$str .= "g.weixiu_company_id = '".$where['weixiu_company_id']."' AND ";
		}
		if($where['weixiu_warehouse_id'] !== "")
		{
			$str .= "g.weixiu_warehouse_id = '".$where['weixiu_warehouse_id']."' AND ";
		}

		//此判断不要轻易动，有问题找张丽娟。。。。
		/*if(!in_array($_SESSION['userName'],array('董小华','张敏敏','admin')))
		{
			$str .= " g.goods_id not in(150409528627,150409528626,150409528625,150409528624,150409528623,150409528622,150409528621,150409528620,150409528619vv额e,150409528618,150409528617,150409528616,150409528615,150416530793,150416530794,150416530795,150416530796,150416530797,150416530798,150416530799,150416530800,150416530801,150416530802,150416530803,150416530804,150416530805,150416530806,150416530807,150416530808,150416530809,150429540286,150429540287,150429540288,150429540289,150429540290,150429540291,150429540292,150429540293,150429540294,150429540295,150429540296,150429540297,150429540298,150429540299,150429540300,150429540301,150429540302,150429540303,150429540304,150429540305,150429540306,150429540307,150429540308,150429540309)";
		}*/
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str."";
		}else{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE 1";
		}
		$sql .= " ORDER BY g.goods_id DESC";
		//echo $sql;die;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}




	//婚博会专用导出
	public function hbhexport($where)
	{
		$sql = "SELECT * FROM (
			SELECT `g`.`goods_id`,`g`.`goods_sn`,`g`.`cat_type`,`g`.`cat_type1`,
				   `g`.`product_type`,`g`.`product_type1`,`g`.`goods_name`,
				   `g`.`caizhi`,`g`.`jinzhong`,`g`.`shoucun`,
				   `g`.`zhushixingzhuang`,`g`.`zuanshidaxiao`,`g`.`zhushijingdu`,
				   `g`.`zhushiyanse`,`g`.`zhushiqiegong`,`g`.`duichen`,`g`.`paoguang`,
				   `g`.`zhushiguige`,`g`.`yingguang`,`g`.`zhengshuhao`,
	               `g`.`mingyichengben`,`g`.`jijiachengben`,
				   `g`.`tuo_type`,`g`.`box_sn`,`b`.`bill_no`,`b`.`bill_note` 
			FROM   `".$this->table()."` AS g 
			LEFT  JOIN warehouse_shipping.warehouse_bill_goods bg ON g.goods_id = bg.goods_id 
		    LEFT  JOIN warehouse_shipping.warehouse_bill b ON bg.bill_id = b.id AND b.bill_status =2 AND b.bill_type =  'M' ";
		$str = "";
		if(isset($where['goods_id']) && $where['goods_id'] != "")
		{
			//去除用户不小心输入或粘贴的空白字符和中文,号替换
			$where['goods_id']=preg_replace("/[sv]+/",'',$where['goods_id']);
			$where['goods_id']=str_replace(" ",',',$where['goods_id']);
			$where['goods_id']=str_replace("，",',',$where['goods_id']);
			$item = explode(",",$where['goods_id']);
			if(!empty($item))
			{
				$goodsid = implode(",",$item);
				$str .= " g.goods_id in (".$goodsid.") AND ";
			}
		}
		if(isset($where['style_sn']) && $where['style_sn'] != "")
		{
           $str .= $this->formatString($where['style_sn'],g.'goods_sn');
		}
		if(isset($where['put_in_type']) && $where['put_in_type'] !== "")
		{
			$str .= "g.put_in_type = '".$where['put_in_type']."' AND ";
		}
		if(isset($where['weixiu_status']) && $where['weixiu_status'] !== "")
		{
			$str .= "g.weixiu_status = '".$where['weixiu_status']."' AND ";
		}
		if(isset($where['is_on_sale']) && $where['is_on_sale'] !== "")
		{
			$str .= "g.is_on_sale = ".$where['is_on_sale']." AND ";
		}
		if(isset($where['caizhi']) && $where['caizhi'] !="" && isset($where['jinse']) && $where['jinse'] !== "")
		{
		    $zhuchengse = $where['caizhi'].str_replace("无","",$where['jinse']);
		    $str .= "caizhi = '".$zhuchengse."' AND ";
		}else if(isset($where['caizhi']) && $where['caizhi']!== "" && isset($where['jinse']) && $where['jinse']=="")
		{		    
			$str .= "caizhi like '".$where['caizhi']."%' AND ";
		}else if(isset($where['caizhi']) && $where['caizhi'] =="" && isset($where['jinse']) && $where['jinse']!=="")
		{
		    $str .= "caizhi like '%".$where['jinse']."' AND ";
		}
		
		if(isset($where['company_id']) && $where['company_id'] !== "")
		{
			$str .= "g.company_id = '".$where['company_id']."' AND ";
		}
		if(isset($where['cat_type']) && $where['cat_type'] !== "" && isset($where['cat_type']) && $where['cat_type'] != "全部")
		{
			$str .= "g.cat_type = '".$where['cat_type']."' AND ";
		}
		if(isset($where['cat_type1']) && $where['cat_type1'] !== "" && isset($where['cat_type1']) && $where['cat_type1'] != "全部")
		{
			$str .= "g.cat_type1 = '".$where['cat_type1']."' AND ";
		}
		
		if(isset($where['warehouse_id']) && $where['warehouse_id'] !=="" )
		{
			$str .= "g.`warehouse_id` = '".$where['warehouse_id']."' AND ";// g.warehouse like '%婚博会柜面%'
		}
		if($_SESSION['userName']=='lily'){
			$str .= "warehouse_id in (653,695) AND ";  //这个人是谁？
		}
		
		if(isset($where['box_id']) &&  $where['box_id']  !=="" )
		{
			$str .= "g.`box_sn` = '".$where['box_id']."' AND ";
		}
		//new add
		if(isset($where['zhengshuhao']) && $where['zhengshuhao'] !== "")
		{
			$str .= "g.zhengshuhao = '".$where['zhengshuhao']."' AND ";
		}
		if (isset($where['order_goods_ids']) && $where['order_goods_ids'] !== '')
		{
			if(isset($where['order_goods_ids']) && $where['order_goods_ids'] == '1'){
				$str .= " g.order_goods_id <> 0 AND ";//已经绑定
			}else{
				$str .= " (g.order_goods_id = '0' or g.order_goods_id='') AND ";//未绑定
			}
		}
		if(isset($where['shoucun']) && $where['shoucun'] !== '')
		{
			$str .= " g.shoucun = '".$where['shoucun']."' AND ";
		}
		if (isset($where['kucunstart']) && $where['kucunstart'] !== '')
		{
			$str .= " g.addtime >= '".$where['kucunstart']." 00:00:00' AND ";
		}
		if (isset($where['kucunend']) && $where['kucunend'] !== '')
		{
			$str .= " g.addtime <= '".$where['kucunend']." 23:59:59' AND ";
		}
		if(isset($where['processor']) && $where['processor'] !== '')
		{
			$str .= " g.prc_id='".$where['processor']."' AND ";
		}
		if ($where['buchan'] !== '')
		{
			$str .= " g.buchan_sn='".$where['buchan']."' AND ";
		}
		if ($where['mohao'] !== '')
		{
			$str .= "g.mo_sn='".$where['mohao']."' AND ";
		}
		if ($where['zhushi'] !== '')
		{
			$str .= " g.zhushi='".$where['zhushi']."' AND ";
		}
		if ($where['zhengshuleibie'] !== '')
		{
			$str .= " g.zhengshuleibie='".$where['zhengshuleibie']."' AND ";
		}
		//差一个主成色重
		if($where['jinzhong_begin'] !== "") {
			$str .= " g.jinzhong >=".$where['jinzhong_begin']." AND ";
		}
		if($where['jinzhong_end'] !== "") {
			$str .= " g.jinzhong <=".$where['jinzhong_end']." AND ";
		}
		if($where['zs_color'] !== "")
		{
			$str .= "g.zhushiyanse = '".$where['zs_color']."' AND ";
		}
		if ($where['zs_clarity'] !== "")
		{
			$str .= "g.zhushijingdu = '".$where['zs_clarity']."' AND ";
		}
		if ($where['jinshi_type'] !== "") {
			$str .= " g.ziyin='".$where['jinshi_type']."' AND ";
		}
		if ($where['jintuo_type'] !== "")
		{
			$str .= " g.tuo_type='".$where['jintuo_type']."' AND ";
		}
		if ($where['jiejia'] !== "")
		{
			$str .= " g.jiejia = '".$where['jiejia']."' AND ";
		}
		if($where['guiwei'] == '1')
		{
			$str .= " ( g.box_sn = '' OR g.box_sn = '0-00-0-0') AND ";
		}elseif($where['guiwei'] == '2')
		{
			$str .= " ( g.box_sn != '' AND g.box_sn != '0-00-0-0') AND ";
		}
		if ($where['chanpinxian'] !== "")
		{
			$str .= "g.product_type='".$where['chanpinxian']."' AND ";
		}
		if ($where['chanpinxian1'] !== "")
		{
            if($where['chanpinxian1']=='全部'){
               
            }else{
               $str .= "g.product_type1='".$where['chanpinxian1']."' AND "; 
            }
		}
		if ($where['zhushi_begin'] !== "") {
			$str .= "zuanshidaxiao >= '".$where['zhushi_begin']."' AND ";
		}
		if ($where['zhushi_end'] !== "") {
			$str .= "g.zuanshidaxiao <= '".$where['zhushi_end']."' AND ";
		}
		if($where['weixiu_company_id'] !== "")
		{
			$str .= "g.weixiu_company_id = '".$where['weixiu_company_id']."' AND ";
		}
		if($where['weixiu_warehouse_id'] !== "")
		{
			$str .= "g.weixiu_warehouse_id = '".$where['weixiu_warehouse_id']."' AND ";
		}
		
		if($where['xinyaozhanshi'] !== "")
		{
			$str .= "g.xinyaozhanshi = '".$where['xinyaozhanshi']."' AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str."";
		}else{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE 1";
		}
		$sql .= " ORDER BY `b`.`check_time` DESC) AS `r` GROUP BY `r`.`goods_id`";
		$data['data'] = $this->db()->getAll($sql);
		return $data;
		
	}
	

/*婚博会专用导出*/
		function hbhpageListAll ($where)
		{
		$sql = "SELECT * FROM (SELECT `g`.`goods_id`,`g`.`goods_sn`,`g`.`cat_type`,`g`.`cat_type1`,`g`.`product_type`,`g`.`product_type1`,`g`.`goods_name`,`g`.`caizhi`,`g`.`jinzhong`,`g`.`shoucun`,`g`.`zhushixingzhuang`,`g`.`zuanshidaxiao`,`g`.`zhushijingdu`,`g`.`zhushiyanse`,`g`.`zhushiqiegong`,`g`.`duichen`,`g`.`paoguang`,`g`.`zhushiguige`,`g`.`yingguang`,`g`.`zhengshuhao`,`g`.`mingyichengben`,IF(`g`.`tuo_type`=1,'成品',IF(`g`.`tuo_type`=2,'空托女戒',IF(`g`.`tuo_type`=3,'空托',''))),`g`.`box_sn`,`b`.`bill_no`,`b`.`bill_note` FROM
		`".$this->table()."` AS g LEFT JOIN warehouse_shipping.warehouse_bill_goods bg 
		ON g.goods_id = bg.goods_id 
		LEFT JOIN warehouse_shipping.warehouse_bill b 
		ON bg.bill_id = b.id AND b.bill_status =2
		AND b.bill_type =  'M' ";
		$str = "";
		if($where['goods_id'] != "")
		{
			//add by zhangruiying 去除用户不小心输入或粘贴的空白字符和中文,号替换
			$where['goods_id']=preg_replace("/[sv]+/",'',$where['goods_id']);
			$where['goods_id']=str_replace(" ",',',$where['goods_id']);
			$where['goods_id']=str_replace("，",',',$where['goods_id']);
			//add end
			$item =explode(",",$where['goods_id']);
			$goodsid = "";
			foreach($item as $key => $val) {
				if ($val != '') {
					if($goodsid){
						$goodsid .= ",'".trim($val)."'";
					}else{
						$goodsid .= "'".trim($val)."'";
					}
				}
			}
			$where['goods_id'] = $goodsid;
			//$str .= " goods_id like \"%".addslashes($where['goods_id'])."%\"  AND ";
			$str .= " g.goods_id in (".$where['goods_id'].") AND ";
		}
		if($where['style_sn'] != "")
		{
                        $str .= $this->formatString($where['style_sn'],g.'goods_sn');

			//$str .= " goods_sn = '".$where['style_sn']."' AND ";
		}
		if($where['put_in_type'] !== "")
		{
			$str .= "g.put_in_type = '".$where['put_in_type']."' AND ";
		}
		if($where['weixiu_status'] !== "")
		{
			$str .= "g.weixiu_status = '".$where['weixiu_status']."' AND ";
		}
		if($where['is_on_sale'] !== "")
		{
			$str .= "g.is_on_sale = ".$where['is_on_sale']." AND ";
		}
    
		if($where['caizhi'] !="" && $where['jinse'] !== ""){
		    $zhuchengse = $where['caizhi'].str_replace("无","",$where['jinse']);
		    $str .= "caizhi = '".$zhuchengse."' AND ";
		}else if($where['caizhi'] !== "" && $where['jinse'] == ""){		    
			$str .= "caizhi like '".$where['caizhi']."%' AND ";
		}else if($where['caizhi'] =="" && $where['jinse'] !== ""){
		    $str .= "caizhi like '%".$where['jinse']."' AND ";
		}
		
		if($where['company_id_list'] != ''){
            $str .= "g.company_id = '".$where['company_id_list']."' AND ";
        }else{
            if($where['company_id'] !== "")
            {
                $str .= "g.company_id = '".$where['company_id']."' AND ";
            }
        }
		if($where['cat_type'] !== "" && $where['cat_type'] != "全部")
		{
			$str .= "g.cat_type = '".$where['cat_type']."' AND ";
		}
		if($where['cat_type1'] !== "" && $where['cat_type1'] != "全部")
		{
			$str .= "g.cat_type1 = '".$where['cat_type1']."' AND ";
		}
		
		if($where['warehouse_id'] !=="" )
		{
			$str .= "g.`warehouse_id` = '".$where['warehouse_id']."' AND ";// g.warehouse like '%婚博会柜面%'
		}
		if($_SESSION['userName']=='lily'){
			$str .= "warehouse_id in (653,695) AND ";
		}
		
		if($where['box_id'] !=="" )
		{
			$str .= "g.`box_sn` = '".$where['box_id']."' AND ";
		}
		//new add
		if($where['zhengshuhao'] !== "")
		{
			$str .= "g.zhengshuhao = '".$where['zhengshuhao']."' AND ";
		}
		if ($where['order_goods_ids'] !== '')
		{
			if($where['order_goods_ids'] == '1'){
				$str .= " g.order_goods_id <> 0 AND ";//已经绑定
			}else{
				$str .= " (g.order_goods_id = '0' or g.order_goods_id='') AND ";//未绑定
			}
		}
		if($where['shoucun'] !== '')
		{
			$str .= " g.shoucun = '".$where['shoucun']."' AND ";
		}
		if ($where['kucunstart'] !== '')
		{
			$str .= " g.addtime >= '".$where['kucunstart']." 00:00:00' AND ";
		}
		if ($where['kucunend'] !== '')
		{
			$str .= " g.addtime <= '".$where['kucunend']." 23:59:59' AND ";
		}
		if($where['processor'] !== '')
		{
			$str .= " g.prc_id='".$where['processor']."' AND ";
		}
		if ($where['buchan'] !== '')
		{
			$str .= " g.buchan_sn='".$where['buchan']."' AND ";
		}
		if ($where['mohao'] !== '')
		{
			$str .= "g.mo_sn='".$where['mohao']."' AND ";
		}
		if ($where['zhushi'] !== '')
		{
			$str .= " g.zhushi='".$where['zhushi']."' AND ";
		}
		if ($where['zhengshuleibie'] !== '')
		{
			$str .= " g.zhengshuleibie='".$where['zhengshuleibie']."' AND ";
		}
		//差一个主成色重
		if($where['jinzhong_begin'] !== "") {
			$str .= " g.jinzhong >=".$where['jinzhong_begin']." AND ";
		}
		if($where['jinzhong_end'] !== "") {
			$str .= " g.jinzhong <=".$where['jinzhong_end']." AND ";
		}
		if($where['zs_color'] !== "")
		{
			$str .= "g.zhushiyanse = '".$where['zs_color']."' AND ";
		}
		if ($where['zs_clarity'] !== "")
		{
			$str .= "g.zhushijingdu = '".$where['zs_clarity']."' AND ";
		}
		if ($where['jinshi_type'] !== "") {
			$str .= " g.ziyin='".$where['jinshi_type']."' AND ";
		}
		if ($where['jintuo_type'] !== "")
		{
			$str .= " g.tuo_type='".$where['jintuo_type']."' AND ";
		}
		if ($where['jiejia'] !== "")
		{
			$str .= " g.jiejia = '".$where['jiejia']."' AND ";
		}
		if($where['guiwei'] == '1')
		{
			$str .= " ( g.box_sn = '' OR g.box_sn = '0-00-0-0') AND ";
		}elseif($where['guiwei'] == '2')
		{
			$str .= " ( g.box_sn != '' AND g.box_sn != '0-00-0-0') AND ";
		}
		if ($where['chanpinxian'] !== "")
		{
			$str .= "g.product_type='".$where['chanpinxian']."' AND ";
		}
		if ($where['chanpinxian1'] !== "")
		{
            if($where['chanpinxian1']=='全部'){
               
            }else{
               $str .= "g.product_type1='".$where['chanpinxian1']."' AND "; 
            }
		}
		if ($where['zhushi_begin'] !== "") {
			$str .= "zuanshidaxiao >= '".$where['zhushi_begin']."' AND ";
		}
		if ($where['zhushi_end'] !== "") {
			$str .= "g.zuanshidaxiao <= '".$where['zhushi_end']."' AND ";
		}
		if($where['weixiu_company_id'] !== "")
		{
			$str .= "g.weixiu_company_id = '".$where['weixiu_company_id']."' AND ";
		}
		if($where['weixiu_warehouse_id'] !== "")
		{
			$str .= "g.weixiu_warehouse_id = '".$where['weixiu_warehouse_id']."' AND ";
		}
		
		if($where['xinyaozhanshi'] !== "")
		{
		    if($where['xinyaozhanshi']==2){
				$str .= "(g.xinyaozhanshi = '".$where['xinyaozhanshi']."' or g.zhengshuleibie='HRD-S')  AND ";
			}else{
				$str .= "g.xinyaozhanshi = '".$where['xinyaozhanshi']."' AND ";
			}
		}

		//此判断不要轻易动，有问题找张丽娟。。。。
		/*if(!in_array($_SESSION['userName'],array('董小华','张敏敏','admin')))
		{
			$str .= " g.goods_id not in(150409528627,150409528626,150409528625,150409528624,150409528623,150409528622,150409528621,150409528620,150409528619vv额e,150409528618,150409528617,150409528616,150409528615,150416530793,150416530794,150416530795,150416530796,150416530797,150416530798,150416530799,150416530800,150416530801,150416530802,150416530803,150416530804,150416530805,150416530806,150416530807,150416530808,150416530809,150429540286,150429540287,150429540288,150429540289,150429540290,150429540291,150429540292,150429540293,150429540294,150429540295,150429540296,150429540297,150429540298,150429540299,150429540300,150429540301,150429540302,150429540303,150429540304,150429540305,150429540306,150429540307,150429540308,150429540309)";
		}*/

		if(isset($where['hidden']) && $where['hidden'] != ''){
            $str .= " g.hidden = ".$where['hidden']." AND " ;
        }

		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str."";
		}else{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE 1";
		}
		$sql .= " ORDER BY `b`.`check_time` DESC) AS `r` GROUP BY `r`.`goods_id`";
		//echo $sql;die;
		$data['data'] = $this->db()->getAll($sql);
		return $data;
	}
	
	
	//查询款式基本信息
	function getStyleByStyle_sn($where) {
		$sql = "SELECT * FROM  `front`.`base_style_info`   WHERE 1 ";
	
		if ($where['style_sn'] != "") {
			$sql .= " AND `style_sn` = '" . addslashes($where['style_sn']) . "'";
		}				
		$sql .= " ORDER BY `style_id` DESC LIMIT 0,1 ";		 
		$data = $this->db()->getRow($sql);
		return $data;
	}
	
	//根据ID查询新产品线
	function getProducts_typeById($where) {
		$sql = "SELECT `product_type_name` FROM  `front`.`app_product_type`   WHERE 1 ";
	
		if ($where['product_type'] != "") {
			$sql .= " AND `product_type_id` = " .$where['product_type'];
		}
		$sql .= " ORDER BY `product_type_id` DESC LIMIT 0,1 ";
		$data='';
		$data = $this->db()->getOne($sql);
		
		return $data;
	
	}
	
	//查询新产品线
	function getProducts_type() {
		$sql = "SELECT `product_type_name` FROM  `front`.`app_product_type`  ORDER BY `product_type_id` asc ";	
		$data = $this->db()->getAll($sql);	
		return $data;
	
	}
	
	
	//根据ID查询新款式分类
	function getCat_typeById($where) {
		$sql = "SELECT `cat_type_name` FROM  `front`.`app_cat_type`   WHERE 1 ";
	
		if ($where['style_type'] != "") {
			$sql .= " AND `cat_type_id` = " .$where['style_type'];
		}
		$sql .= " ORDER BY `cat_type_id` DESC LIMIT 0,1 ";
		$data='';
		$data = $this->db()->getOne($sql);
		return $data;
	}
	
	//查询新款式分类
	function getCat_type() {
		$sql = "SELECT `cat_type_name` FROM  `front`.`app_cat_type`  ORDER BY `cat_type_id` asc";
		$data = $this->db()->getAll($sql);
		return $data;
	}
	
	public function getSalesPersons(Array $depts) {
	    $ids = implode(',', $depts);
	    $sql = "select * from cuteframe.sales_channels_person where id in ({$ids})";
	    return $this->db()->getAll($sql);	    
	}

		/**
	 *	pageGroupList，分组搜索分页列表
	 *
	 *	@url ApplicationController/search
	 */
	function pageGroupList ($where,$page,$pageSize=10,$useCache=true , $is_down = false)
	{
		$time = date('Y-m-d H:i:s');
		$sql = "SELECT `wg`.id, `wg`.goods_id, SUM(`wg`.mingyichengben) AS mingyichengben, SUM(`wg`.yuanshichengbenjia) AS yuanshichengbenjia, SUM(`wg`.chengbenjia) AS chengbenjia, `wg`.goods_sn, `sg`.`thumb_img`, `wg`.mo_sn, `wg`.goods_name, `wg`.caizhi, `wg`.warehouse_id, count(*) AS counts FROM `warehouse_shipping`.`".$this->table()."` `wg` 
        LEFT JOIN front.app_style_gallery AS `sg` ON `sg` .`style_sn` = `wg`.`goods_sn` 
        AND sg.`image_place` = 1";
		$str = "";
		if($where['goods_id'] != "")
		{
			//add by zhangruiying 去除用户不小心输入或粘贴的空白字符和中文,号替换
			$where['goods_id']=preg_replace("/[sv]+/",'',$where['goods_id']);
			$where['goods_id']=str_replace(" ",',',$where['goods_id']);
			$where['goods_id']=str_replace("，",',',$where['goods_id']);
			//add end
			$item =explode(",",$where['goods_id']);
			$goodsid = "";
			foreach($item as $key => $val) {
				if ($val != '') {
					if($goodsid){
						$goodsid .= ",'".trim($val)."'";
					}else{
						$goodsid .= "'".trim($val)."'";
					}
				}
			}
			$where['goods_id'] = $goodsid;
			//$str .= " goods_id like \"%".addslashes($where['goods_id'])."%\"  AND ";
			$str .= " `wg`.goods_id in (".$where['goods_id'].") AND ";
		}
		if($where['style_sn'] != "")
		{
                        $str .= $this->formatStringFenzu($where['style_sn'],'goods_sn');

			//$str .= " goods_sn = '".$where['style_sn']."' AND ";
		}
		if($where['put_in_type'] !== "")
		{
			$str .= "`wg`.put_in_type = '".$where['put_in_type']."' AND ";
		}
		if($where['weixiu_status'] !== "")
		{
			$str .= "`wg`.weixiu_status = '".$where['weixiu_status']."' AND ";
		}
		if($where['is_on_sale'] !== "")
		{
			$str .= "`wg`.is_on_sale = ".$where['is_on_sale']." AND ";
		}
	    
	    if($where['caizhi'] !="" && $where['jinse'] !== ""){
		    $zhuchengse = $where['caizhi'].str_replace("无","",$where['jinse']);
		    $str .= "`wg`.caizhi = '".$zhuchengse."' AND ";
		}else if($where['caizhi'] !== "" && $where['jinse'] == ""){		    
			$str .= "`wg`.caizhi like '".$where['caizhi']."%' AND ";
		}else if($where['caizhi'] =="" && $where['jinse'] !== ""){
		    $str .= "`wg`.caizhi like '%".$where['jinse']."' AND ";
		}
		
		if($where['company_id_list'] != ''){
            $str .= "wg.company_id = '".$where['company_id_list']."' AND ";
        }else{
            if($where['company_id'] !== "")
            {
                $str .= "wg.company_id = '".$where['company_id']."' AND ";
            }
        }
		if($where['cat_type'] !== "" && $where['cat_type'] != "全部")
		{
			$str .= "`wg`.cat_type = '".$where['cat_type']."' AND ";
		}
		if($where['cat_type1'] !== "" && $where['cat_type1'] != "全部")
		{
			$str .= "`wg`.cat_type1 = '".$where['cat_type1']."' AND ";
		}
		
		if($where['warehouse_id'] !== "")
		{
			$str .= "`wg`.warehouse_id = '".$where['warehouse_id']."' AND ";
		}
		if($_SESSION['userName']=='lily'){
			$str .= "warehouse_id in (653,695) AND ";
		}
		//柜位号--如果搜索条件【仓库】有值，搜索出来的货号要同时符合仓库和柜位号
		//box_sn 这个是柜位号，而不是 box_id
		$str .= $this -> commonwhere($where['box_id']);
		//new add
		if($where['zhengshuhao'] !== "")
		{
			$str .= "`wg`.zhengshuhao = '".$where['zhengshuhao']."' AND ";

		}
		if ($where['order_goods_ids'] !== '')
		{
			if($where['order_goods_ids'] == '1'){
				$str .= " `wg`.order_goods_id <> 0 AND ";//已经绑定
			}else{
				$str .= " (`wg`.order_goods_id = '0' or `wg`.order_goods_id='') AND ";//未绑定
			}
		}
		if($where['shoucun'] !== '')
		{
			$str .= " `wg`.shoucun = '".$where['shoucun']."' AND ";
		}
                if($where['pinpai'] !== '')
                {
                    $str .= " `wg`.pinpai = '".$where['pinpai']."' AND ";
                }
                
                 if($where['luozuanzhengshu'] !== '')
                {
                    $str .= " `wg`.luozuanzhengshu = '".$where['luozuanzhengshu']."' AND ";
                }
                
                if($where['xilie_name']){
                     
                    $str .= " `wg`.goods_sn IN  (".$where['xilie_name'].") AND ";
                  
                }
                
		if ($where['kucunstart'] !== '')
		{
			$str .= " `wg`.addtime >= '".$where['kucunstart']." 00:00:00' AND ";
		}
		if ($where['kucunend'] !== '')
		{
			$str .= " `wg`.addtime <= '".$where['kucunend']." 23:59:59' AND ";
		}
		if($where['processor'] !== '')
		{
			$str .= " `wg`.prc_id='".$where['processor']."' AND ";
		}
		if ($where['buchan'] !== '')
		{
			$str .= " `wg`.buchan_sn='".$where['buchan']."' AND ";
		}
		if ($where['mohao'] !== '')
		{
			$str .= "`wg`.mo_sn='".$where['mohao']."' AND ";
		}
		if ($where['zhushi'] !== '')
		{
			$str .= " `wg`.zhushi='".$where['zhushi']."' AND ";
		}
		if ($where['zhengshuleibie'] !== '')
		{
			$str .= " `wg`.zhengshuleibie='".$where['zhengshuleibie']."' AND ";
		}
		//差一个主成色重
		if($where['jinzhong_begin'] !== "") {
			$str .= " `wg`.jinzhong >=".$where['jinzhong_begin']." AND ";
		}
		if($where['jinzhong_end'] !== "") {
			$str .= " `wg`.jinzhong <=".$where['jinzhong_end']." AND ";
		}
		if($where['zs_color'] !== "")
		{
			$str .= " `wg`.zhushiyanse = '".$where['zs_color']."' AND ";
		}
		if ($where['zs_clarity'] !== "")
		{
			$str .= " `wg`.zhushijingdu = '".$where['zs_clarity']."' AND ";
		}
		if ($where['jinshi_type'] !== "") {
			$str .= " `wg`.ziyin='".$where['jinshi_type']."' AND ";
		}
		if ($where['jintuo_type'] !== "")
		{
			$str .= " `wg`.tuo_type='".$where['jintuo_type']."' AND ";
		}
		if ($where['jiejia'] !== "")
		{
			$str .= " `wg`.jiejia = '".$where['jiejia']."' AND ";
		}
		if($where['guiwei'] == '1')
		{
			$str .= " ( `wg`.box_sn = '' OR box_sn = '0-00-0-0') AND ";
		}elseif($where['guiwei'] == '2')
		{
			$str .= " ( `wg`.box_sn != '' AND box_sn != '0-00-0-0') AND ";
		}
		
		
		if ($where['chanpinxian'] !== "")
		{
			$str .= "`wg`.product_type='".$where['chanpinxian']."' AND ";
		}
		if ($where['chanpinxian1'] !== "" && $where['chanpinxian1'] != "全部")
		{
			$str .= "`wg`.product_type1='".$where['chanpinxian1']."' AND ";
		}
		if ($where['zhushi_begin'] !== "") {
			$str .= "`wg`.zuanshidaxiao >= '".$where['zhushi_begin']."' AND ";
		}
		if ($where['zhushi_end'] !== "") {
			$str .= "`wg`.zuanshidaxiao <= '".$where['zhushi_end']."' AND ";
		}
		if($where['weixiu_company_id'] !== "")
		{
			$str .= "`wg`.weixiu_company_id = '".$where['weixiu_company_id']."' AND ";
		}
		if($where['weixiu_warehouse_id'] !== "")
		{
			$str .= "`wg`.weixiu_warehouse_id = '".$where['weixiu_warehouse_id']."' AND ";
		}
		if($where['xinyaozhanshi'] !== "")
		{
			if($where['xinyaozhanshi']==2){
				$str .= "(wg.xinyaozhanshi = '".$where['xinyaozhanshi']."' or wg.zhengshuleibie='HRD-S')  AND ";
			}else{
				$str .= "wg.xinyaozhanshi = '".$where['xinyaozhanshi']."' AND ";
			}
		}
		

		//此判断不要轻易动，有问题找张丽娟。。。。
		if(!in_array($_SESSION['userName'],array('董小华','张敏敏','admin')))
		{
			$str .= " goods_id not in(150409528627,150409528626,150409528625,150409528624,150409528623,150409528622,150409528621,150409528620,150409528619,150409528618,150409528617,150409528616,150409528615,150416530793,150416530794,150416530795,150416530796,150416530797,150416530798,150416530799,150416530800,150416530801,150416530802,150416530803,150416530804,150416530805,150416530806,150416530807,150416530808,150416530809,150429540286,150429540287,150429540288,150429540289,150429540290,150429540291,150429540292,150429540293,150429540294,150429540295,150429540296,150429540297,150429540298,150429540299,150429540300,150429540301,150429540302,150429540303,150429540304,150429540305,150429540306,150429540307,150429540308,150429540309)";
		}

        if(isset($where['hidden']) && $where['hidden'] != ''){
            $str .= " wg.hidden = ".$where['hidden']." AND " ;
        }


		$sqltj = "SELECT count(*) as zong_num,sum(`wg`.`mingyichengben`) as zong_chengbenjia FROM `".$this->table()."` as `wg`";
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
			$sql .=" AND `wg`.`is_on_sale` <> '100'";
			$sqltj .=" WHERE ".$str." and `wg`.`is_on_sale` <> '100'";
		}else{
			$sql .= " WHERE `wg`.`is_on_sale` <> '100'";
			$sqltj .=" WHERE `wg`.`is_on_sale` <> '100'";
		}
		$sql .= " GROUP BY `wg`.goods_sn, `wg`.mo_sn, `wg`.caizhi ORDER BY count(*) DESC";
        //echo $sql;die;
		$tongji = $this->db()->getRow($sqltj);
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		$countSql = "SELECT COUNT(*) FROM ($sql) A";
		$data['recordCount'] = $this->db()->getOne($countSql);
		$data['pageCount'] = ceil($data['recordCount']/$pageSize);

		/*
		if(!empty($data['data']))
		{
			foreach ($data['data'] as $key=>$v){
				//根据商品款号获取款式基本信息
				
				  $baseArr=$this->getStyleByStyle_sn(Array('style_sn'=>$v['goods_sn']));
				  if(!empty($baseArr)){
				    $data['data'][$key]['product_type_name']=$this->getProducts_typeById(Array('product_type'=>$baseArr['product_type']));
				    $data['data'][$key]['cat_type_name']=$this->getCat_typeById(Array('style_type'=>$baseArr['style_type']));
				  }else{
				  	$data['data'][$key]['product_type_name']='';
				  	$data['data'][$key]['cat_type_name']='';
				  }
				
			}
			
		}*/
		//echo '<pre>';print_r($data);die;
		$data['tongji']=$tongji;
		return $data;
	}
	//修改warehouse表字段
    public function update($data,$where){
	    //过滤主键id值
	    if($this->pk() && isset($data[$this->pk()])){
	        unset($data[$this->pk()]);
	    }
	    //通过系统底层函数拼接sql，然后替换掉死板的where条件
	    $sql = $this->updateSql($data);
	    if(preg_match('/ WHERE /is',$sql)){
	        $sql = preg_replace('/ WHERE .*/is',' WHERE '.$where, $sql);
	        return $this->db()->query($sql);
	    }else{
	        return false;
	    }
	}

    // 打印提货单 查询订单明细对应的货号和柜位
	function getOrderGoodsAndBox($order_id){
        $sql="select g.goods_id,g.warehouse,b.box_sn from warehouse_goods g left join goods_warehouse w on (g.goods_id=w.good_id) left join warehouse_box b on w.box_id=b.id where g.order_goods_id='{$order_id}' order by g.goods_id desc";
        return $this->db()->getRow($sql);
    }

    public function getDzKuanPrice($val){
        $price = false;

        $id = $val['id'];
        $goods_id = $val['goods_id'];
        $goods_sn = $val['goods_sn'];
        $caizhi = $val['caizhi'];
        $tuo_type = $val['tuo_type'];
        $luozuanzhengshu = $val['luozuanzhengshu'];
        $zhushixingzhuang = $val['zhushixingzhuang'];
        $zuanshidaxiao = $val['zuanshidaxiao'];

        $zhushi = $val['zhushi'];
        $zhushiyanse = $val['zhushiyanse'];
        $zhushijingdu = $val['zhushijingdu'];

        $zhushixingzhuang_num = $this->getXingzhuang($zhushixingzhuang);
        $caizhi_enum = 0;
        if(strpos(strtoupper($caizhi),'18K') !== false ){
            $caizhi_enum = 2;
        }elseif($caizhi == 'PT950'){
            $caizhi_enum = 1;
        }

        // 1 18K  2 PT950

        $check = true;
        //var_dump($val);
        if($zhushi != '钻石'){
            $check = false;
            $e = 1;
        }elseif($caizhi_enum == 0){
            $check = false;
            $e = 2;
        }elseif($zhushixingzhuang_num == 0 ){
            $check = false;
            $e = 3;
        }elseif($zhushiyanse == '' ){
            $check = false;
            $e = 4;
        }elseif($zhushijingdu == '' ){
            $check = false;
            $e = 5;
        }

        $sel_sql = "select id,zuan_yanse_min,zuan_yanse_max,zuan_jindu_min,zuan_jindu_max,price
            from 
                front.base_style_info bsi 
                inner join front.app_price_by_style apbs on bsi.style_id = apbs.style_id
            where
                bsi.style_sn = '$goods_sn'
                AND caizhi = $caizhi_enum
                AND stone_position = 1
                AND tuo_type = $tuo_type
                AND zuan_min <= $zuanshidaxiao  
                AND zuan_max >= $zuanshidaxiao 
                AND cert = '$luozuanzhengshu'
                AND zuan_shape = '$zhushixingzhuang'
                AND is_delete = 0
        ";
        //echo $sel_sql;
        $priceList = $this->db()->getAll($sel_sql);
        foreach($priceList as $k => $v){
            $yanseIn = $this->YanseIn($v['zuan_yanse_min'],$v['zuan_yanse_max'],$zhushiyanse);
            $jingduIn = $this->JinduIn($v['zuan_jindu_min'],$v['zuan_jindu_max'],$zhushijingdu);
            if($yanseIn && $jingduIn){
                $price = $v['price'];
                break;
            }
        }
        return $price;
    }

     function YanseIn($oldS,$oldE,$new)
    {
        if($new == ''){
            $new = '空值';
        }
        $appPriceByStyleModel = new AppPriceByStyleModel(17);
        $zuan_yanse = $appPriceByStyleModel->getYanseAll();
        
        $y = array_flip($zuan_yanse);
        $oldS = $y[$oldS];
        $oldE = $y[$oldE];
        $new = $y[$new];

        if($oldS <= $new && $new <= $oldE){
            return true;
        }else{
            return false;
        }
    }

    function JinduIn($oldS,$oldE,$new)
    {
        if($new == ''){
            $new = '空值';
        }

        $appPriceByStyleModel = new AppPriceByStyleModel(17);
        $zuan_jingdu = $appPriceByStyleModel->getJingduAll();

        $j = array_flip($zuan_jingdu);
        $oldS = $j[$oldS];
        $oldE = $j[$oldE];
        $new = $j[$new];
        
        if($oldS <= $new && $new <= $oldE){
            return true;
        }else{
            return false;
        }
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
    
    //根据货号(可多个)获取商品的总名义成本价 2015-12-26 zzm boss-1015
    public function getTotalMingyichengben($goods_str){
    	if($goods_str){
	    	$sql = "SELECT SUM(mingyichengben) FROM ".$this->table()." WHERE goods_id IN(".$goods_str.")";
	    	return $this->db()->getOne($sql);
    	}else{
    		return 0;
    	}
    }
    
    
    
    //根据GEMX证书号和品牌确定是否是星耀钻石
    public function getXingYao($gemx_zhengshu='',$pinpai=''){
    	if($gemx_zhengshu!=''){
    		return 1;
    	}
    	if($pinpai!=''){
    		$pinpai=preg_replace('/([\x80-\xff]*)/i','',$pinpai);
    		$pinpai=preg_replace('~[^\p{Han}]~u', '',$pinpai);
    		$pinpai=preg_replace('/[GIA]+/i','',$pinpai);
    		$pinpai=preg_replace('/[EGL]+/i','',$pinpai);
    		$pinpai=preg_replace('/[AGL]+/i','',$pinpai);
    	}
    	
    }

	// 取直营店所属公司id,同时 排出总公司和上海南京东路体验店
	public function getTydCompanyIds() {
		$sql="select company_id from cuteframe.sales_channels a join cuteframe.shop_cfg b on a.channel_own_id=b.id
			where shop_type=1 and a.is_deleted=0 and b.is_delete=0";
		$res = $this->db()->getAll($sql);
		$company_ids = array_flip(array_unique(array_column($res, 'company_id')));
		// 排出：58总公司,223上海南京东路体验店,500广州天河分公司广晟大厦体验店, 501广州越秀分公司吉邦大厦体验店
		unset($company_ids['58'], $company_ids['223'], $company_ids['500'], $company_ids['501']);
		// 加上：445 柯兰深圳分公司仓库
		$company_ids['445'] = 1;
		return $company_ids;
	}
	// 取调拨单商品最新一次加价率
	public function getMbillJiajialv($goods_id, $company_id) {
		$sql="select jiajialv from warehouse_shipping.warehouse_bill_goods g join warehouse_shipping.warehouse_bill b on g.bill_id=b.id
				where b.bill_type='M' and goods_id={$goods_id} and to_company_id={$company_id} order by g.id desc limit 1";
		return $this->db()->getOne($sql);
	}



    function getWholesaleIdByName($name){
    	$sql="select wholesale_id from jxc_wholesale where wholesale_name='{$name}'";
    	$wholesale_id=$this->db()->getOne($sql);
    	if(!empty($wholesale_id)){
    		return $wholesale_id;
    	}else{
    		return 0;
    	}
    }
    
    
    //获取批发客户
    public function getWholesaleArr($wholesale_id){
    	$sql ="select wholesale_name from warehouse_shipping.jxc_wholesale where wholesale_id='".$wholesale_id."'";
    	return $this->db()->getOne($sql);
    }

    
    function getEcDetailByGoodsId($goods_id){
    	$sql="SELECT p.id,p.p_sn,p.p_id,p.from_type FROM warehouse_shipping.warehouse_goods AS g,kela_supplier.product_info AS p WHERE g.goods_id='{$goods_id}' AND g.buchan_sn=p.bc_sn";
    	$bc_arr=$this->db()->getRow($sql);   	
    	if(empty($bc_arr)){
    		return false;
    	}
    	$from_type=$bc_arr['from_type'];
    	if($from_type==1){
    		$sql="SELECT code,value FROM purchase.purchase_goods_attr WHERE g_id= {$bc_arr['p_id']} AND code IN ('p_sn_out','ds_xiangci','pinhao')";
    		$rows=$this->db()->getAll($sql);
    		$row=array();
    		foreach ($rows as $v){
    			$row[$v['code']]=$v['value'];
    		}    				
    	}else{
    		$sql="SELECT d.ds_xiangci,d.pinhao,r.out_order_sn AS p_sn_out FROM app_order.app_order_details AS d LEFT JOIN app_order.base_order_info AS o ON d.order_id=o.id LEFT JOIN app_order.rel_out_order AS r ON o.id=r.order_id WHERE d.id={$bc_arr['p_id']}";
    	    $row=$this->db()->getRow($sql);
    	    if(empty($row)){
    	    	$sql="SELECT d.ds_xiangci,d.pinhao,r.out_order_sn AS p_sn_out FROM app_order.app_order_details AS d LEFT JOIN app_order.base_order_info AS o ON d.order_id=o.id LEFT JOIN app_order.rel_out_order AS r ON o.id=r.order_id WHERE d.bc_id={$bc_arr['id']}";
    	    	$row=$this->db()->getRow($sql);
    	    }
    	    
    	}
    	return $row;
     }


     //打印条码导出
     public function downCodeInfo($data)
     {
        $content .= "货号,款号,基因码,手寸,长度,主石粒数,主石重,副石粒数,副石重,加工商编号,总重,净度,颜色,证书号,国际证书,主石切工,标签备注,主石,副石,主成色,饰品分类,款式分类,名称,石3副石,石3粒数,石3重,石4副石,石4粒数,石4重,石5副石,石5粒数,石5重,主成色重,副成色,副成色重,买入工费,计价工费,加价率,最新零售价,模号,品牌,证书数量,配件数量,时尚款,系列,属性,类别,成本价,入库日期,加价率代码,主石粒重,副石粒重,标签手寸,字印,货币符号零售价,新成本价,新零售价,一口价,标价,定制价,A,B,C,D,E,F,G,H,I,HB_G,HB_H,样板可做镶口范围,原价\r\n";
        foreach ($data as $key => $line) {
            $tihCz = array(
            '24K' =>'足金',
            '千足金银'    =>'足金银',
            'S990'    =>'足银',
            '千足银' =>'足银',            
            '千足金' =>'足金',
            'PT900'   =>'铂900',
            'PT999'   =>'铂999',
            'PT950'   =>'铂950',
            '18K玫瑰黄'=>'18K金',
            '18K玫瑰白'=>'18K金',
            '18K玫瑰金'=>'18K金',
            '18K黄金'=>'18K金',
            '18K白金'=>'18K金',
            '18K黑金'=>'18K金',
            '18K彩金'=>'18K金',
            '18K红'=>'18K金',
            '18K黄白'=>'18K金',
            '18K分色'=>'18K金',
            '18K黄'=>'18K金',
            '18K白'=>'18K金',
            '9K玫瑰黄'=>'9K金',
            '9K玫瑰白'=>'9K金',
            '9K玫瑰金'=>'9K金',
            '9K黄金'=>'9K金',
            '9K白金'=>'9K金',
            '9K黑金'=>'9K金',
            '9K彩金'=>'9K金',
            '9K红'=>'9K金',
            '9K黄白'=>'9K金',
            '9K分色'=>'9K金',
            '9K黄'=>'9K金',
            '9K白'=>'9K金',
            '10K玫瑰黄'=>'10K金',
            '10K玫瑰白'=>'10K金',
            '10K玫瑰金'=>'10K金',
            '10K黄金'=>'10K金',
            '10K白金'=>'10K金',
            '10K黑金'=>'10K金',
            '10K彩金'=>'10K金',
            '10K红'=>'10K金',
            '10K黄白'=>'10K金',
            '10K分色'=>'10K金',
            '10K黄'=>'10K金',
            '10K白'=>'10K金', 
            '14K玫瑰黄'=>'14K金',
            '14K玫瑰白'=>'14K金',
            '14K玫瑰金'=>'14K金',
            '14K黄金'=>'14K金',
            '14K白金'=>'14K金',
            '14K黑金'=>'14K金',
            '14K彩金'=>'14K金',
            '14K红'=>'14K金',
            '14K黄白'=>'14K金',
            '14K分色'=>'14K金',
            '14K黄'=>'14K金',
            '14K白'=>'14K金',
            '19K黄'=>'19K金',
            '19K白'=>'19K金',
            '19K玫瑰黄'=>'19K金',
            '19K玫瑰白'=>'19K金',
            '19K玫瑰金'=>'19K金',
            '19K黄金'=>'19K金',
            '19K白金'=>'19K金',
            '19K黑金'=>'19K金',
            '19K彩金'=>'19K金',
            '19K红'=>'19K金',
            '19K黄白'=>'19K金',
            '19K分色'=>'19K金',
            '20K黄'=>'20K金',
            '20K白'=>'20K金',
            '20K玫瑰黄'=>'20K金',
            '20K玫瑰白'=>'20K金',
            '20K玫瑰金'=>'20K金',
            '20K黄金'=>'20K金',
            '20K白金'=>'20K金',
            '20K黑金'=>'20K金',
            '20K彩金'=>'20K金',
            '20K红'=>'20K金',
            '20K黄白'=>'20K金',
            '20K分色'=>'20K金',
            '21K黄'=>'21K金',
            '21K白'=>'21K金',
            '21K玫瑰黄'=>'21K金',
            '21K玫瑰白'=>'21K金',
            '21K玫瑰金'=>'21K金',
            '21K黄金'=>'21K金',
            '21K白金'=>'21K金',
            '21K黑金'=>'21K金',
            '21K彩金'=>'21K金',
            '21K红'=>'21K金',
            '21K黄白'=>'21K金',
            '21K分色'=>'21K金',
            '22K黄'=>'22K金',
            '22K白'=>'22K金',
            '22K玫瑰黄'=>'22K金',
            '22K玫瑰白'=>'22K金',
            '22K玫瑰金'=>'22K金',
            '22K黄金'=>'22K金',
            '22K白金'=>'22K金',
            '22K黑金'=>'22K金',
            '22K彩金'=>'22K金',
            '22K红'=>'22K金',
            '22K黄白'=>'22K金',
            '22K分色'=>'22K金',
            '23K黄'=>'23K金',
            '23K白'=>'23K金',
            '23K玫瑰黄'=>'23K金',
            '23K玫瑰白'=>'23K金',
            '23K玫瑰金'=>'23K金',
            '23K黄金'=>'23K金',
            '23K白金'=>'23K金',
            '23K黑金'=>'23K金',
            '23K彩金'=>'23K金',
            '23K红'=>'23K金',
            '23K黄白'=>'23K金',
            '23K分色'=>'23K金',
            'S925黄'=>'S925',
            'S925白'=>'S925',
            'S925玫瑰黄'=>'S925',
            'S925玫瑰白'=>'S925',
            'S925玫瑰金'=>'S925',
            'S925黄金'=>'S925',
            'S925白金'=>'S925',
            'S925黑金'=>'S925',
            'S925彩金'=>'S925',
            'S925红'=>'S925',
            'S925黄白'=>'S925',
            'S925分色'=>'S925',
            'S925'    =>'银925'
            );

        $stone_arr = array('红宝'=>'红宝石',
            '珍珠贝'=>'贝壳',
            '白水晶'=>'水晶',
            '粉晶'=>'水晶',
            '茶晶'=>'水晶',
            '紫晶'=>'水晶',
            '紫水晶'=>'水晶',
            '黄水晶'=>'水晶',
            '彩兰宝'=>'蓝宝石',
            '彩色蓝宝'=>'蓝宝石',
            '蓝晶'=>'水晶',
            '黄晶'=>'水晶',
            '柠檬晶'=>'水晶',
            '红玛瑙'=>'玛瑙',
            '黑玛瑙'=>'玛瑙',
            '奥泊'=>'宝石',
            '黑钻'=>'钻石',
            '琥铂'=>'琥铂',
            '虎晴石'=>'宝石',
            '大溪地珍珠'=>'珍珠',
            '大溪地黑珍珠'=>'珍珠',
            '淡水白珠'=>'珍珠',
            '淡水珍珠'=>'珍珠',
            '南洋白珠'=>'珍珠',
            '南洋金珠'=>'珍珠',
            '海水香槟珠'=>'珍珠',
            '混搭珍珠'=>'珍珠',
            '蓝宝'=>'蓝宝石',
        	'宝石石'=>'宝石',
            '黄钻'=>'钻石');

          $tihCt = array(
            '男戒'=>'戒指',
            '女戒'=>'戒指',
            '情侣戒'=>'戒指'
            );
          if($line['goods_name'] != ''){
            //$line['goods_name'] = str_replace("千", "", $line['goods_name']);
            $line['goods_name'] = str_replace('锆石','合成立方氧化锆',$line['goods_name']);
            $line['goods_name'] = str_replace(array_keys($tihCz), array_values($tihCz), $line['goods_name']);
            $line['goods_name'] = str_replace(array_keys($stone_arr), array_values($stone_arr), $line['goods_name']);            
            $line['goods_name'] = str_replace(array_keys($tihCt), array_values($tihCt), $line['goods_name']);
          }

          if($line['zhushi'] != ''){
            $line['zhushi'] = str_replace('锆石','合成立方氧化锆',$line['zhushi']);
          }

            $fushishu = !empty($line['fushishu'])?$line['fushishu']."P":'';
            $fushizhong = !empty($line['fushizhong'])?$line['fushizhong']."CT/".$fushishu:'';
            $shoucun = !empty($line['shoucun'])?"#".$line['shoucun']:'';
            $tiemp = '';
            if($line['zhushijingdu'] != '' && $line['zhushiyanse'] != ''){
                $tiemp = $line['zhushijingdu']."/".$line['zhushiyanse'];
            }
            $content .=
              "\"" . $line['goods_id'] . "\"," .//货号
              "\"" . $line['goods_sn'] . "\"," .//款号
              "\"" . '' . "\"," .//基因吗
              "\"" . $line['shoucun'] . "\"," .//手寸
              "\"" . '' . "\"," .//长度
              "\"" . $line['zhushishu'] . "\"," .//主石粒数
              "\"" . $line['zhushizhong'] . "\"," .//主石重
              "\"" . $line['fushishu'] . "\"," .//副石数
              "\"" . $line['fushizhong'] . "\"," .//副石重
              "\"" .  "\"," .       //加工商编号
              "\"" . $line['huozhong'] . "\"," .//总重
              "\"" . $line['zhushijingdu'] . "\"," .//净度
              "\"" . $line['yanse'] . "\"," .//颜色
              "\"" . $line['zhengshuhao'] . '"' . "," .//证书号
              "\"" . '' . "\"," .//国际证书
              "\"" . $line['zhushiquegong'] . "\"," .//主石切工
              "\"" . $line['beizhu'] . "\"," .                //标签备注
              "\"" . $line['zhushi'] . "\"," .//主石
              "\"" . $line['fushi'] . "\"," .//副石
              "\"" . $line['zhuchengse'] . "\"," .//主成色
              "\"" . $line['shipin_type'] . "\"," .//饰品分类
              "\"" . $line['cat_type'] . "\"," .//款式分类
              "\"" . $line['goods_name'] . "\"," .//名称
              "\"" . '' . "\"," .            //石3副石
              "\"" . '' . "\"," .            //石3粒数
              "\"" . '' . "\"," .            //石3重
              "\"" .  "\"," .           //石4副石
              "\"" .  "\"," .           //石4粒数
              "\"" .  "\"," .               //石4重
              "\"" .  "\"," .       //石5副石
              "\"" .  "\"," .           //石5粒数
              "\"" .  "\"," .           //石5重
              "\"" . $line['jinzhong'] . "\"," .
              "\"" . '' . "\"," .//副成色
              "\"" . '' . "\"," .//副成色重
              "\"" . '' . "\"," .//买入工费 
              "\"" . '' . "\"," .//计价工费
              "\"" . $line['jiajialv'] . "\"," .//加价率
              //"\"" . $line['jiajialv']. "\"," .
              "\"" . '' . "\"," .//最新零售价
              "\"" . $line['mohao'] . "\"," .//模号
              "\"" . '' . "\"," .//品牌
              "\"" . '' . "\"," .           //证书数量
              "\"" . '' . "\"," . //配件数量
              "\"" .  "\"," .               //时尚款
              "\"" . $line['xilie'] . "\",".           //系列
              "\"" .  "\"," .           //属性
              "\"" .  "\"," .           //类别
              "\"" . $line['shijichengben'] . "\"," .//成本价
              "\"" . $line['riqi'] . "\"," .//入库时间
              "\"" .  "\"," .           //加价率代码
              "\"" .  "\"," .           //主石粒重
              "\"" .  "\"," .           //副石粒重
              "\"" .  "\"," .           //标签手寸
              "\"" . '' . "\"," .                //ziyin
              "\"" . '' . "\"," .//最新零售价
              "\"" . $line['rukuchengbenjia'] . "\"," .//买入成本
              "\"" . $line['xiaoshoujia'] . "\"," .//新零售价
              "\"" . '' . "\"," .//一口价
              "\"" . '' . "\"," .//标价
              "\"" . '' . "\",".//定制价
              ",".                  // A
              "\"" .$line['goods_name']."\",".   // B
              "\"" .'￥'.round($line['xiaoshoujia'])."\",". // C
              "\"" .$line['zhushizhong']."\",". // d
              "\"" .$fushizhong."\",". // e
              "\"" .$line['jinzhong']."g"."\",". // f
              "\"" .$line['zhengshuhao']."\",". // g
              "\"" .$tiemp."\",".   // h
              "\"" .$shoucun."\",". // i
              "\"" . '' ."\",".  // hb_f
              "\"" . '' ."\",".    // hb_g
              "\"" . '' ."\",".   // 样板可做镶口范围
              "\"" . '' ."\"\r\n".    // 原价
              "";
        }

        header("Content-type:text/csv;charset=gbk");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . "tiaoma.csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo iconv("utf-8", "gbk//IGNORE", $content);
        exit;
     }

     //打印条码导出
     public function downCodeInfo_sujin($data)
     {
        $content .= "货号,款号,基因码,手寸,长度,主石粒数,主石重,副石粒数,副石重,加工商编号,总重,净度,颜色,证书号,国际证书,主石切工,标签备注,主石,副石,主成色,饰品分类,款式分类,名称,石3副石,石3粒数,石3重,石4副石,石4粒数,石4重,石5副石,石5粒数,石5重,主成色重,副成色,副成色重,买入工费,计价工费,加价率,最新零售价,模号,品牌,证书数量,配件数量,时尚款,系列,属性,类别,成本价,入库日期,加价率代码,主石粒重,副石粒重,标签手寸,字印,货币符号零售价,新成本价,新零售价,一口价,标价,定制价,A,B,C,D,E,F,G,H,I,HB_G,HB_H,样板可做镶口范围,原价\r\n";
        foreach ($data as $key => $line) {

            $fushishu = !empty($line['fushishu'])?$line['fushishu']."P":'';
            $content .=
              "\"" . $line['goods_id'] . "\"," .//货号
              "\"" . $line['goods_sn'] . "\"," .//款号
              "\"" . '' . "\"," .//基因吗
              "\"" . $line['shoucun'] . "\"," .//手寸
              "\"" . '' . "\"," .//长度
              "\"" . $line['zhushishu'] . "\"," .//主石粒数
              "\"" . $line['zhushizhong'] . "\"," .//主石重
              "\"" . $line['fushishu'] . "\"," .//副石数
              "\"" . $line['fushizhong'] . "\"," .//副石重
              "\"" .  "\"," .       //加工商编号
              "\"" . $line['huozhong'] . "\"," .//总重
              "\"" . $line['zhushijingdu'] . "\"," .//净度
              "\"" . $line['yanse'] . "\"," .//颜色
              "\"" . $line['zhengshuhao'] . '"' . "," .//证书号
              "\"" . '' . "\"," .//国际证书
              "\"" . $line['zhushiquegong'] . "\"," .//主石切工
              "\"" . $line['beizhu'] . "\"," .                //标签备注
              "\"" . $line['zhushi'] . "\"," .//主石
              "\"" . $line['fushi'] . "\"," .//副石
              "\"" . $line['zhuchengse'] . "\"," .//主成色
              "\"" . $line['shipin_type'] . "\"," .//饰品分类
              "\"" . $line['cat_type'] . "\"," .//款式分类
              "\"" . $line['goods_name'] . "\"," .//名称
              "\"" . '' . "\"," .            //石3副石
              "\"" . '' . "\"," .            //石3粒数
              "\"" . '' . "\"," .            //石3重
              "\"" .  "\"," .           //石4副石
              "\"" .  "\"," .           //石4粒数
              "\"" .  "\"," .               //石4重
              "\"" .  "\"," .       //石5副石
              "\"" .  "\"," .           //石5粒数
              "\"" .  "\"," .           //石5重
              "\"" . $line['jinzhong'] . "\"," .
              "\"" . '' . "\"," .//副成色
              "\"" . '' . "\"," .//副成色重
              "\"" . '' . "\"," .//买入工费 
              "\"" . '' . "\"," .//计价工费
              "\"" . $line['jiajialv'] . "\"," .//加价率
              //"\"" . $line['jiajialv']. "\"," .
              "\"" . '' . "\"," .//最新零售价
              "\"" . $line['mohao'] . "\"," .//模号
              "\"" . '' . "\"," .//品牌
              "\"" . '' . "\"," .           //证书数量
              "\"" . '' . "\"," . //配件数量
              "\"" .  "\"," .               //时尚款
              "\"" . $line['xilie'] . "\",".           //系列
              "\"" .  "\"," .           //属性
              "\"" .  "\"," .           //类别
              "\"" . $line['shijichengben'] . "\"," .//成本价
              "\"" . $line['riqi'] . "\"," .//入库时间
              "\"" .  "\"," .           //加价率代码
              "\"" .  "\"," .           //主石粒重
              "\"" .  "\"," .           //副石粒重
              "\"" .  "\"," .           //标签手寸
              "\"" . '' . "\"," .                //ziyin
              "\"" . '￥'. '' . "\"," .//最新零售价
              "\"" . $line['rukuchengbenjia'] . "\"," .//买入成本
              "\"" . $line['xiaoshoujia'] . "\"," .//新零售价
              "\"" . '' . "\"," .//一口价
              "\"" . '' . "\"," .//标价
              "\"" . '' . "\",".//定制价
              ",".                  // A
              "\"" .$line['goods_name']."\",".   // B
              "\"" .$line['xiaoshoujia']."\",". // C
              "\"" .$line['zhushizhong']."\",". // d
              "\"" .!empty($line['fushizhong'])?$line['fushizhong']."CT/".$fushishu:''."\",". // e
              "\"" . '' ."\",". // f
              "\"" . '' ."\",". // g
              "\"" . '' ."\",".   // h
              "\"" . !empty($line['shoucun'])?"#".$line['shoucun']:$line['shoucun'] ."\",". // i
              "\"" . '' ."\",".  // hb_f
              "\"" . '' ."\",".    // hb_g
              "\"" . '' ."\",".   // 样板可做镶口范围
              "\"" . '' ."\"\r\n".    // 原价
              "";
        }

        header("Content-type:text/csv;charset=gbk");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . "tiaoma.csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo iconv("utf-8", "gbk//IGNORE", $content);
        exit;
     }

     //打印条码导出
     public function downCodeInfo_luoshi($data)
     {
        $content .= "货号,款号,基因码,手寸,长度,主石粒数,主石重,副石粒数,副石重,加工商编号,总重,净度,颜色,证书号,国际证书,主石切工,标签备注,主石,副石,主成色,饰品分类,款式分类,名称,石3副石,石3粒数,石3重,石4副石,石4粒数,石4重,石5副石,石5粒数,石5重,主成色重,副成色,副成色重,买入工费,计价工费,加价率,最新零售价,模号,品牌,证书数量,配件数量,时尚款,系列,属性,类别,成本价,入库日期,加价率代码,主石粒重,副石粒重,标签手寸,字印,货币符号零售价,新成本价,新零售价,一口价,标价,定制价,A,B,C,D,E,F,G,H,I,HB_G,HB_H,样板可做镶口范围,原价\r\n";
        foreach ($data as $key => $line) {

            $fushishu = !empty($line['fushishu'])?$line['fushishu']."P":'';
            $content .=
              "\"" . $line['goods_id'] . "\"," .//货号
              "\"" . $line['goods_sn'] . "\"," .//款号
              "\"" . '' . "\"," .//基因吗
              "\"" . $line['shoucun'] . "\"," .//手寸
              "\"" . '' . "\"," .//长度
              "\"" . $line['zhushishu'] . "\"," .//主石粒数
              "\"" . $line['zhushizhong'] . "\"," .//主石重
              "\"" . $line['fushishu'] . "\"," .//副石数
              "\"" . $line['fushizhong'] . "\"," .//副石重
              "\"" .  "\"," .       //加工商编号
              "\"" . $line['huozhong'] . "\"," .//总重
              "\"" . $line['zhushijingdu'] . "\"," .//净度
              "\"" . $line['yanse'] . "\"," .//颜色
              "\"" . $line['zhengshuhao'] . '"' . "," .//证书号
              "\"" . '' . "\"," .//国际证书
              "\"" . $line['zhushiquegong'] . "\"," .//主石切工
              "\"" . $line['beizhu'] . "\"," .                //标签备注
              "\"" . $line['zhushi'] . "\"," .//主石
              "\"" . $line['fushi'] . "\"," .//副石
              "\"" . $line['zhuchengse'] . "\"," .//主成色
              "\"" . $line['shipin_type'] . "\"," .//饰品分类
              "\"" . $line['cat_type'] . "\"," .//款式分类
              "\"" . $line['goods_name'] . "\"," .//名称
              "\"" . '' . "\"," .            //石3副石
              "\"" . '' . "\"," .            //石3粒数
              "\"" . '' . "\"," .            //石3重
              "\"" .  "\"," .           //石4副石
              "\"" .  "\"," .           //石4粒数
              "\"" .  "\"," .               //石4重
              "\"" .  "\"," .       //石5副石
              "\"" .  "\"," .           //石5粒数
              "\"" .  "\"," .           //石5重
              "\"" . $line['jinzhong'] . "\"," .
              "\"" . '' . "\"," .//副成色
              "\"" . '' . "\"," .//副成色重
              "\"" . '' . "\"," .//买入工费 
              "\"" . '' . "\"," .//计价工费
              "\"" . $line['jiajialv'] . "\"," .//加价率
              //"\"" . $line['jiajialv']. "\"," .
              "\"" . '' . "\"," .//最新零售价
              "\"" . $line['mohao'] . "\"," .//模号
              "\"" . '' . "\"," .//品牌
              "\"" . '' . "\"," .           //证书数量
              "\"" . '' . "\"," . //配件数量
              "\"" .  "\"," .               //时尚款
              "\"" . $line['xilie'] . "\",".           //系列
              "\"" .  "\"," .           //属性
              "\"" .  "\"," .           //类别
              "\"" . $line['shijichengben'] . "\"," .//成本价
              "\"" . $line['riqi'] . "\"," .//入库时间
              "\"" .  "\"," .           //加价率代码
              "\"" .  "\"," .           //主石粒重
              "\"" .  "\"," .           //副石粒重
              "\"" .  "\"," .           //标签手寸
              "\"" . '' . "\"," .                //ziyin
              "\"" . '￥'. '' . "\"," .//最新零售价
              "\"" . $line['rukuchengbenjia'] . "\"," .//买入成本
              "\"" . $line['xiaoshoujia'] . "\"," .//新零售价
              "\"" . '' . "\"," .//一口价
              "\"" . '' . "\"," .//标价
              "\"" . '' . "\",".//定制价
              ",".                  // A
              "\"" .$line['goods_name']."\",".   // B
              "\"" .$line['xiaoshoujia']."\",". // C
              "\"" .$line['zhushizhong']."\",". // d
              "\"" .!empty($line['fushizhong'])?$line['fushizhong']."CT/".$fushishu:''."\",". // e
              "\"" . '' ."\",". // f
              "\"" . '' ."\",". // g
              "\"" . '' ."\",".   // h
              "\"" . !empty($line['shoucun'])?"#".$line['shoucun']:$line['shoucun'] ."\",". // i
              "\"" . '' ."\",".  // hb_f
              "\"" . '' ."\",".    // hb_g
              "\"" . '' ."\",".   // 样板可做镶口范围
              "\"" . '' ."\"\r\n".    // 原价
              "";
        }

        header("Content-type:text/csv;charset=gbk");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . "tiaoma.csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo iconv("utf-8", "gbk//IGNORE", $content);
        exit;
     }


     public function PrintHuodongBzhCode($info)
     {
        if (isset($info['goods_id']) && !empty($info['goods_id'])){
            $info['goods_id']=preg_replace("/[sv]+/",'',trim($info['goods_id']));
            $info['goods_id']=str_replace(" ",',',trim($info['goods_id']));
            $info['goods_id']=str_replace("，",',',trim($info['goods_id']));

            $item = explode(",",$info['goods_id']);
            $goods_ids = array();
            foreach ($item as $k){
                if($k){//去除空的goods_id
                    $goods_ids[] = trim($k);
                }
            }
        }
        foreach ($goods_ids as $key => $goods_id) {
            $sql = "SELECT * FROM `warehouse_goods_baizhihui` WHERE goods_id = '$goods_id'";
            $res = $this->db()->getRow($sql);
            if(empty($res)){
                $error = "没有查到该货号：".$goods_id."信息，请核实后再做打印！";
                $this->error_csv($error);
            }
            $data[] = $res;
        }

        $content .= "货号,款号,基因码,手寸,长度,主石粒数,主石重,副石粒数,副石重,加工商编号,总重,净度,颜色,证书号,国际证书,主石切工,标签备注,主石,副石,主成色,饰品分类,款式分类,名称,石3副石,石3粒数,石3重,石4副石,石4粒数,石4重,石5副石,石5粒数,石5重,主成色重,副成色,副成色重,买入工费,计价工费,加价率,最新零售价,模号,品牌,证书数量,配件数量,时尚款,系列,属性,类别,成本价,入库日期,加价率代码,主石粒重,副石粒重,标签手寸,字印,货币符号零售价,新成本价,新零售价,一口价,标价,定制价,A,B,C,D,E,F,G,H,I,HB_G,HB_H,样板可做镶口范围,原价,镶口,原始货号\r\n";
        foreach ($data as $key => $line) {
            $goods_id = $line['goods_id'];
            

            $tihCz = array(
            '24K' =>'足金',
            '千足金银'    =>'足金银',
            'S990'    =>'足银',
            '千足银' =>'足银',            
            '千足金' =>'足金',
            'PT900'   =>'铂900',
            'PT999'   =>'铂999',
            'PT950'   =>'铂950',
            '18K玫瑰黄'=>'18K金',
            '18K玫瑰白'=>'18K金',
            '18K玫瑰金'=>'18K金',
            '18K黄金'=>'18K金',
            '18K白金'=>'18K金',
            '18K黑金'=>'18K金',
            '18K彩金'=>'18K金',
            '18K红'=>'18K金',
            '18K黄白'=>'18K金',
            '18K分色'=>'18K金',
            '18K黄'=>'18K金',
            '18K白'=>'18K金',
            '9K玫瑰黄'=>'9K金',
            '9K玫瑰白'=>'9K金',
            '9K玫瑰金'=>'9K金',
            '9K黄金'=>'9K金',
            '9K白金'=>'9K金',
            '9K黑金'=>'9K金',
            '9K彩金'=>'9K金',
            '9K红'=>'9K金',
            '9K黄白'=>'9K金',
            '9K分色'=>'9K金',
            '9K黄'=>'9K金',
            '9K白'=>'9K金',
            '10K玫瑰黄'=>'10K金',
            '10K玫瑰白'=>'10K金',
            '10K玫瑰金'=>'10K金',
            '10K黄金'=>'10K金',
            '10K白金'=>'10K金',
            '10K黑金'=>'10K金',
            '10K彩金'=>'10K金',
            '10K红'=>'10K金',
            '10K黄白'=>'10K金',
            '10K分色'=>'10K金',
            '10K黄'=>'10K金',
            '10K白'=>'10K金', 
            '14K玫瑰黄'=>'14K金',
            '14K玫瑰白'=>'14K金',
            '14K玫瑰金'=>'14K金',
            '14K黄金'=>'14K金',
            '14K白金'=>'14K金',
            '14K黑金'=>'14K金',
            '14K彩金'=>'14K金',
            '14K红'=>'14K金',
            '14K黄白'=>'14K金',
            '14K分色'=>'14K金',
            '14K黄'=>'14K金',
            '14K白'=>'14K金',
            '19K黄'=>'19K金',
            '19K白'=>'19K金',
            '19K玫瑰黄'=>'19K金',
            '19K玫瑰白'=>'19K金',
            '19K玫瑰金'=>'19K金',
            '19K黄金'=>'19K金',
            '19K白金'=>'19K金',
            '19K黑金'=>'19K金',
            '19K彩金'=>'19K金',
            '19K红'=>'19K金',
            '19K黄白'=>'19K金',
            '19K分色'=>'19K金',
            '20K黄'=>'20K金',
            '20K白'=>'20K金',
            '20K玫瑰黄'=>'20K金',
            '20K玫瑰白'=>'20K金',
            '20K玫瑰金'=>'20K金',
            '20K黄金'=>'20K金',
            '20K白金'=>'20K金',
            '20K黑金'=>'20K金',
            '20K彩金'=>'20K金',
            '20K红'=>'20K金',
            '20K黄白'=>'20K金',
            '20K分色'=>'20K金',
            '21K黄'=>'21K金',
            '21K白'=>'21K金',
            '21K玫瑰黄'=>'21K金',
            '21K玫瑰白'=>'21K金',
            '21K玫瑰金'=>'21K金',
            '21K黄金'=>'21K金',
            '21K白金'=>'21K金',
            '21K黑金'=>'21K金',
            '21K彩金'=>'21K金',
            '21K红'=>'21K金',
            '21K黄白'=>'21K金',
            '21K分色'=>'21K金',
            '22K黄'=>'22K金',
            '22K白'=>'22K金',
            '22K玫瑰黄'=>'22K金',
            '22K玫瑰白'=>'22K金',
            '22K玫瑰金'=>'22K金',
            '22K黄金'=>'22K金',
            '22K白金'=>'22K金',
            '22K黑金'=>'22K金',
            '22K彩金'=>'22K金',
            '22K红'=>'22K金',
            '22K黄白'=>'22K金',
            '22K分色'=>'22K金',
            '23K黄'=>'23K金',
            '23K白'=>'23K金',
            '23K玫瑰黄'=>'23K金',
            '23K玫瑰白'=>'23K金',
            '23K玫瑰金'=>'23K金',
            '23K黄金'=>'23K金',
            '23K白金'=>'23K金',
            '23K黑金'=>'23K金',
            '23K彩金'=>'23K金',
            '23K红'=>'23K金',
            '23K黄白'=>'23K金',
            '23K分色'=>'23K金',
            'S925黄'=>'S925',
            'S925白'=>'S925',
            'S925玫瑰黄'=>'S925',
            'S925玫瑰白'=>'S925',
            'S925玫瑰金'=>'S925',
            'S925黄金'=>'S925',
            'S925白金'=>'S925',
            'S925黑金'=>'S925',
            'S925彩金'=>'S925',
            'S925红'=>'S925',
            'S925黄白'=>'S925',
            'S925分色'=>'S925',
            'S925'    =>'银925'
            );

        $stone_arr = array('红宝'=>'红宝石',
            '珍珠贝'=>'贝壳',
            '白水晶'=>'水晶',
            '粉晶'=>'水晶',
            '茶晶'=>'水晶',
            '紫晶'=>'水晶',
            '紫水晶'=>'水晶',
            '黄水晶'=>'水晶',
            '彩兰宝'=>'蓝宝石',
            '彩色蓝宝'=>'蓝宝石',
            '蓝晶'=>'水晶',
            '黄晶'=>'水晶',
            '柠檬晶'=>'水晶',
            '红玛瑙'=>'玛瑙',
            '黑玛瑙'=>'玛瑙',
            '奥泊'=>'宝石',
            '黑钻'=>'钻石',
            '琥铂'=>'琥铂',
            '虎晴石'=>'宝石',
            '大溪地珍珠'=>'珍珠',
            '大溪地黑珍珠'=>'珍珠',
            '淡水白珠'=>'珍珠',
            '淡水珍珠'=>'珍珠',
            '南洋白珠'=>'珍珠',
            '南洋金珠'=>'珍珠',
            '海水香槟珠'=>'珍珠',
            '混搭珍珠'=>'珍珠',
            '蓝宝'=>'蓝宝石',
        	'宝石石'=>'宝石',
            '黄钻'=>'钻石');

           $tihCt = array(
            '男戒'=>'戒指',
            '女戒'=>'戒指',
            '情侣戒'=>'戒指',
            '对戒'=>'戒指'
            );
          if($line['goods_name'] != ''){
            //$line['goods_name'] = str_replace("千", "", $line['goods_name']);
            $line['goods_name'] = str_replace('锆石','合成立方氧化锆',$line['goods_name']);
            $line['goods_name'] = str_replace(array_keys($tihCz), array_values($tihCz), $line['goods_name']);
            $line['goods_name'] = str_replace(array_keys($stone_arr), array_values($stone_arr), $line['goods_name']);            
            $line['goods_name'] = str_replace(array_keys($tihCt), array_values($tihCt), $line['goods_name']);
          }

          if($line['zhushi'] != ''){
            $line['zhushi'] = str_replace('锆石','合成立方氧化锆',$line['zhushi']);
          }

            $fushishu = !empty($line['fushishu'])?$line['fushishu']."P":'';
            $fushizhong = !empty($line['fushizhong'])?$line['fushizhong']."CT/".$fushishu:'';
            $shoucun = !empty($line['shoucun'])?$line['shoucun']."#":'';
            $shuzhishu=$line['zhushishu']>0 ? "/".$line['zhushishu']."P" : "" ;

            $zhushizhong = !empty($line['zhushizhong']) ? $line['zhushizhong']."CT".$shuzhishu : '';
            $jinzhong = !empty($line['jinzhong'])?round($line['jinzhong'], 2)."g":'';
            $tiemp = '';
            if(trim($line['zhushijingdu']) != '' && trim($line['zhushijingdu']) != '0'){
                $tiemp = $line['zhushijingdu'];
            }
            if(trim($line['zhushiyanse']) != '' && trim($line['zhushiyanse']) != '0'){
                $tiemp.="/".$line['zhushiyanse'];
            }
              
            $content .=
              "\"" . $line['goods_id'] . "\"," .//货号
              "\"" . $line['goods_sn'] . "\"," .//款号
              "\"" . '' . "\"," .//基因吗
              "\"" . $line['shoucun'] . "\"," .//手寸
              "\"" . '' . "\"," .//长度
              "\"" . $line['zhushishu'] . "\"," .//主石粒数
              "\"" . $line['zhushizhong'] . "\"," .//主石重
              "\"" . '' . "\"," .//副石数
              "\"" . '' . "\"," .//副石重
              "\"" .  "\"," .       //加工商编号
              "\"" . $line['jinzhong'] . "\"," .//总重
              "\"" . $line['zhushijingdu'] . "\"," .//净度
              "\"" . $line['zhushiyanse'] . "\"," .//颜色
              "\"" . $line['zhengshuhao'] . '"' . "," .//证书号
              "\"" . '' . "\"," .//国际证书
              "\"" . $line['zhushiqiegong'] . "\"," .//主石切工
              "\"" . '' . "\"," .                //标签备注
              "\"" . '' . "\"," .//主石
              "\"" . '' . "\"," .//副石
              "\"" . $line['zhuchengse'] . "\"," .//主成色
              "\"" . '' . "\"," .//饰品分类
              "\"" . '' . "\"," .//款式分类
              "\"" . $line['goods_name'] . "\"," .//名称
              "\"" . '' . "\"," .            //石3副石
              "\"" . '' . "\"," .            //石3粒数
              "\"" . '' . "\"," .            //石3重
              "\"" .  "\"," .           //石4副石
              "\"" .  "\"," .           //石4粒数
              "\"" .  "\"," .               //石4重
              "\"" .  "\"," .       //石5副石
              "\"" .  "\"," .           //石5粒数
              "\"" .  "\"," .           //石5重
              "\"" . $line['jinzhong'] . "\"," .
              "\"" . '' . "\"," .//副成色
              "\"" . '' . "\"," .//副成色重
              "\"" . '' . "\"," .//买入工费 
              "\"" . '' . "\"," .//计价工费
              "\"" . '' . "\"," .//加价率
              //"\"" . $line['jiajialv']. "\"," .
              "\"" . '' . "\"," .//最新零售价
              "\"" . '' . "\"," .//模号
              "\"" . '' . "\"," .//品牌
              "\"" . '' . "\"," .           //证书数量
              "\"" . '' . "\"," . //配件数量
              "\"" .  "\"," .               //时尚款
              "\"" . '' . "\",".           //系列
              "\"" .  "\"," .           //属性
              "\"" .  "\"," .           //类别
              "\"" . $line['chengbenjia'] . "\"," .//成本价
              "\"" . '' . "\"," .//入库时间
              "\"" .  "\"," .           //加价率代码
              "\"" .  "\"," .           //主石粒重
              "\"" .  "\"," .           //副石粒重
              "\"" .  "\"," .           //标签手寸
              "\"" . '' . "\"," .                //ziyin
              "\"" . '' . "\"," .//最新零售价
              "\"" . '' . "\"," .//买入成本
              "\"" . $line['xiaoshoujia'] . "\"," .//新零售价
              "\"" . '' . "\"," .//一口价
              "\"" . '' . "\"," .//标价
              "\"" . '' . "\",".//定制价
              ",".                  // A
              "\"" .$line['goods_name']."\",".   // B
              "\"" .'￥'.round($line['xiaoshoujia'])."\",". // C
              "\"" .$zhushizhong."\",". // d
              "\"" .$fushizhong."\",". // e
              "\"" .$jinzhong."\",". // f
              "\"" .$line['zhengshuhao']."\",". // g
              "\"" .$tiemp."\",".   // h
              "\"" .$shoucun."\",". // i
              "\"" . '' ."\",".  // hb_f
              "\"" . '' ."\",".    // hb_g
              "\"" . '' ."\",".   // 样板可做镶口范围
              "\"" . '' ."\",".    // 原价
              "\"" . $line['xiangkou'] ."\",".   // 镶口
              "\"" . $line['ygoods_id'] ."\"\r\n".    // 原始货号
              "";
        }

        header("Content-type:text/csv;charset=gbk");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . "tiaoma.csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo iconv("utf-8", "gbk//IGNORE", $content);
        exit;
     }

     /**
     *  保存指定标签数据
     */
     public function saveBiaoQianData($value='')
     {
        if($value == ''){
            return false;
        }
        $sql="insert into warehouse_biaoqian (".implode(',',array_keys($value)).") values ('".implode("','",array_values($value))."')";
        return $this->db()->query($sql);
     }

     public function updateBiaoQianData($id, $value='')
     {
         if($value == '' || $id == ''){
            return false;
        }

        $sql = "UPDATE `warehouse_biaoqian` SET `buyout_price` = '".$value['buyout_price']."', `activity_price` = '".$value['activity_price']."' WHERE `id` = '{$id}'";
        return $this->db()->query($sql);
     }
  
     public function updateBiaoQianDataSet($id, $set)
     {
     	if($set == '' || $id == ''){
     		return false;
     	}
     
     	$sql = "UPDATE `warehouse_biaoqian` SET $set WHERE `id` = '{$id}'";
     	return $this->db()->query($sql);
     }
     public function getcheckBiaoqian($id)
     {
         if($id == ''){
            return false;
        }

        $sql = "select `id` from `warehouse_biaoqian` where `goods_id` = '{$id}'";
        return $this->db()->getOne($sql);
     }
     public function getBiaoqian($id)
     {
     	if($id == ''){
     		return false;
     	}
     
     	$sql = "select `id`,`buyout_price` from `warehouse_biaoqian` where `goods_id` = '{$id}'";
     	return $this->db()->getRow($sql);
     }

     /**
     *  错误输出
     */
    public function error_csv($content)
    {
        header("Content-type:text/csv;charset=gbk");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk","error:".$content) . ".csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo iconv("utf-8", "gbk//IGNORE", $content);
        exit;
    }
    
    //查询百智慧
    public function getBaizhihui($id)
    {
    	if($id == ''){
    		return false;
    	}
    
    	$sql = "select `id` from `warehouse_goods_baizhihui` where `goods_id` = '{$id}'";
    	return $this->db()->getRow($sql);
    }
    
    /**
     *  保存百智慧标签数据
     */
    public function insertTableData($table,$value='')
    {
    	if($value == '' || $table==''){
    		return false;
    	}
    	$sql="insert into {$table} (".implode(',',array_keys($value)).") values ('".implode("','",array_values($value))."')";
    	return $this->db()->query($sql);
    }
    
    
    
    public function updateTableData($table, $value='',$where)
    {
    	if($value == '' || $where == '' || $table ==''){
    		return false;
    	}
    	foreach ($value as  $k => $v){
    		if(is_numeric($v))
    		{
    			$fields[] = $k . '=' . $v;
    		}
    		else
    		{
    			$fields[] = $k . '="' . $v.'"';
    		}
    	}
    	$field = implode(',', $fields);
    	$sql = "UPDATE {$table} SET {$field} WHERE {$where}";
    	return $this->db()->query($sql);
    }


    //根据单据号查询单据明细
    public function getBillInfobyNo($bill_no='')
    {
        if($bill_no == ''){
            return array();
        }

        $sql = "select goods_id from `warehouse_bill_goods` where bill_no = '{$bill_no}'";
        return $this->db()->getAll($sql);
        # code...
    }

    //更改单据门店门店结算方式
    public function upSettlementStatus($bill_no, $goods_ids, $status)
    {
        $time = date('Y-m-d H:i:s');
        # code...
        $sql = "update warehouse_bill_goods set dep_settlement_type = {$status},settlement_time = '{$time}' where bill_no = '{$bill_no}' and goods_id in(".$goods_ids.")";
        return $this->db()->query($sql);
    }

    //更改单据门店门店退货方式
    public function upRefundStatus($bill_no, $goods_ids, $status)
    {
        $sql = "update warehouse_bill_goods set dep_settlement_type = {$status} where bill_no = '{$bill_no}' and goods_id in(".$goods_ids.")";
        return $this->db()->query($sql);
    }

    //验证下此货号是否有【已审核】的【批发退货单】，有才会更新状态已退货，更新批发销售单结算操作时间。
    public function checkisH($goods_id)
    {
        $sql = "select * from warehouse_bill wb,warehouse_bill_goods bg where wb.id = bg.bill_id and wb.bill_status = 2 and wb.bill_type = 'H' and bg.goods_id = '{$goods_id}'";
        return $this->db()->getRow($sql);
        # code...
    }
	
	
	
	
	/*
	 * used : 根据搜索条件获取货品信息
	 * parames  where   搜索的条件数组
	 * parames: field   需要的字段名称
	 * return : array
	 * add  : lly
	 * date : 2017-02-18
	*/
	public function getprintgoodsinfo($where=array(),$field='goods_id')
	{
		$data = array();
		if(empty($where))
		{
			return $data;
		}
		//$field = 'goods_id,warehouse mingyichengben,jingxiaoshangchengbenjia from warehouse_goods';
		$sql = "select $field from warehouse_goods where 1 ";
		if(isset($where['goods_id']))
		{
			if(is_array($where['goods_id']))
			{
				$sql .= " and goods_id in('" . implode("','",$where['goods_id'])."')";
			}else{
				$sql .= " and goods_id='{$where['goods_id']}' ";					
			}
		}
		//是否是婚博会备货库
		if(isset($where['ishbhwarehouse']))
		{
			if($where['ishbhwarehouse']>0){
				$sql .=" and warehouse ='婚博会备货库' ";
			}else{
				$sql .=" and warehouse !='婚博会备货库' ";	
			}
		}
		//echo $sql;
		//die();
		return $this->db()->getAll($sql);
		
	}
	
	
	
	/*
	 * 指定价打标
	*/
	public function getzdjdbgoodsinfo($where=array(),$field='goods_id,buyout_price,activity_price')
	{
		$sql = "select $field from warehouse_biaoqian where 1 ";	
		if(isset($where['goods_id']))
		{
			if(is_array($where['goods_id']))
			{
				$sql .= " and goods_id in('" . implode("','",$where['goods_id'])."')";
			}else{
				$sql .= " and goods_id='{$where['goods_id']}' ";					
			}
		}
		$data = $this->db()->getAll($sql);
		if(!empty($data))
		{
			$result = array();
			foreach($data as $value)	
			{
				$gid = $value['goods_id'];
				$result[$gid] = $value;
			}
			return $result;
		}
	}


	/**
    *批量修改展厅标签价；
    **/
    public function upBqjData($data=array())
    {
        $result = array('error'=>"","success"=>0);
        $pdo = $this->db()->db();//pdo对象
        try {
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            if(!empty($data)){
                foreach ($data as $key => $value) {
                    $sql = "update warehouse_goods set biaoqianjia = ".$value['biaoqianjia']." where goods_id = ".$value['goods_id'];
                    $pdo->query($sql);
                }
            }
            $pdo->commit();//如果没有异常，就提交事务
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
            $result['success'] = 1;
            return  $result;
        } catch (Exception $e) {
            $pdo->rollback();//错误 ，事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
            $result['error'] = "事物执行失败，请联系技术人员处理:error:".$sql;
            return $result;
        }
    }
    /**
     * 商品列表（单表）查询搜索
     * @param unknown $where
     * @param number $page
     * @param number $pageSize
     * @param string $useCache
     */
    public function searchGoodsList($where,$page=1,$pageSize=30,$useCache=true){
        $sql = "SELECT * FROM `warehouse_goods`";
        $str = "";
        if(!empty($where['goods_id'])){
            $str .= " goods_id = '".$where['goods_id']."' AND ";
        }
        if(!empty($where['style_sn']))
        {
            $str .= " goods_sn = '".$where['style_sn']."' AND ";
        }
        if(!empty($where['is_on_sale']))
        {            
            if($where['is_on_sale']==2){
                $str .= "is_on_sale = '2'  AND (order_goods_id='' or order_goods_id='0') AND ";                
            }else{
                $str .= "is_on_sale = ".$where['is_on_sale']." AND ";
            }
        }
        if(!empty($where['zhuchengse']))
        {
            $str .= "caizhi = '".$where['zhuchengse']."' AND ";
        }
        if(!empty($where['company_id']))
        {
            if(is_array($where['company_id'])){
                $where['company_id'] = implode(',', $where['company_id']);
                $str .= "company_id in( ".$where['company_id'].") AND ";                
            }else{
                $str .= "company_id =".$where['company_id']." AND ";
            }
        }
        //new add
        if(!empty($where['zhengshuhao']))
        {
            $str .= "zhengshuhao = '".$where['zhengshuhao']."' AND ";
        }

        if(isset($where['zhiquan_min']) && $where['zhiquan_min'] != '')
        {
            $str .= " shoucun >= ".$where['zhiquan_min']." AND ";
        }
        if(isset($where['zhiquan_max']) && $where['zhiquan_max'] != '')
        {
            $str .= " shoucun <= ".$where['zhiquan_max']." AND ";
        }
        if (isset($where['stone']) && $where['stone'] != '')
        {
            $str .= " zhushi='".$where['stone']."' AND ";
        }
        //差一个主成色重
        if(isset($where['jinzhong_min']) && $where['jinzhong_min'] != "") {
            $str .= " jinzhong >=".$where['jinzhong_min']." AND ";
        }
        if(isset($where['jinzhong_max']) && $where['jinzhong_max'] != "") {
            $str .= " jinzhong <=".$where['jinzhong_max']." AND ";
        }
        if(isset($where['carat_min']) && $where['carat_min'] != "") {
            $str .= " zuanshidaxiao >=".$where['carat_min']." AND ";
        }
        if(isset($where['carat_max']) && $where['carat_max'] != "") {
            $str .= " zuanshidaxiao <=".$where['carat_max']." AND ";
        }
        if(isset($where['color']) && $where['color'] != "")
        {
            $str .= " zhushiyanse = '".$where['color']."' AND ";
        }
        if (isset($where['clarity']) && $where['clarity'] != "")
        {
            $str .= " zhushijingdu = '".$where['clarity']."' AND ";
        }
        if (isset($where['caizhi']) && $where['caizhi'] != "")
        {
            $str .= " caizhi like '".$where['caizhi']."%' AND ";
        }
        if (isset($where['jinse']) && $where['jinse'] != "")
        {
            $str .= " caizhi like '%".$where['jinse']."' AND ";
        }
        if($str)
        {
            $str = rtrim($str,"AND ");
            $sql .=" WHERE ".$str;
        }
        $sql .= " ORDER BY id  ASC ";
        return  $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);        
    }







    /**
     * 批量查询，  数据处理
     * 去除用户不小心输入或粘贴的空白字符和中文,号替换
     */
    public function setFilter($str){
        //add by zhangruiying 去除用户不小心输入或粘贴的空白字符和中文,号替换
        $str=preg_replace("/[sv]+/",'',$str);
        $str=str_replace(" ",',',$str);
        $str=str_replace("，",',',$str);
        //add end
        $item =explode(",",$str);
        $rstr = "";
        foreach($item as $key => $val) {
            if ($val != '') {
                if($rstr != ''){
                    $rstr .= ",'".trim($val)."'";
                }else{
                    $rstr .= "'".trim($val)."'";
                }
            }
        }
        return $rstr;
    }
    /**
     * 计算展厅上级批发给下级的批发价
     * @param unknown $goods_list
     * @param unknown $wholesale_company_id
     * @param unknown $is_invoice
     */
    public function calcPifajia(&$goods_list,$wholesale_company_id,$is_invoice = false){
        if(SYS_SCOPE!='zhanting'){
            return ;
        }
        $companyModel = new CompanyModel(1);
        if($wholesale_company_id>0){
            $wholesale_company_type = $companyModel->select2("company_type","id={$wholesale_company_id}",3);
        }else{
            $wholesale_company_type = 3;
        }
        $tax_rate = $is_invoice?1.043:1;

        foreach ($goods_list as $key=>&$ginfo){
            //展厅自动计算批发价 begin
            $pifajia = 0;    
            $mingyichengben = (float)$ginfo['mingyichengben'];//名义成本
            $label_price = (float)$ginfo['biaoqianjia'];//标签价
            if($wholesale_company_type == 3){
                //如果批发客户为经销商：商品如果有展厅标签价
                //批发价=展厅标签价*0.25*税点    
                if($label_price>0){
                    $pifajia = sprintf("%.2f",$label_price*$tax_rate*0.25);
                }else{
                    $sql = "select count(*) from front.app_style_jxs where style_sn='{$ginfo['goods_sn']}'";
                    $is_jxs_style = $this->db()->getOne($sql)?true:false;
                    //$pifa_jiajialv = $is_jxs_style?1.21:1.17;
                    if($is_jxs_style){
                        $pifa_jiajialv = 1.21;
                    }else{
                        if($ginfo['product_type1']=='K金'){
                            $pifa_jiajialv = 1.1;
                        }else{
                            $pifa_jiajialv = 1.17;
                        }
                    }

                    if($ginfo['with_fee']==0)  {
                        if($ginfo['cat_type1']=='裸石' && $ginfo['zhengshuhao']!=""){
                            $gendan_fee = 0;
                        }else{
                            $gendan_fee = 20;
                        }
                    }else{
                        $gendan_fee = $ginfo['with_fee'];
                    }
                    if($ginfo['goods_sn'] == "QIBAN"){
                        $qiban_fee = 300;
                    }else{
                        $qiban_fee = 0;
                    }
                    /**
                                                                     非代销、非自采的并且商品产品线为：镶嵌类款式分类：非裸石或者款式分类为裸石证书号列为空时
                                                                     批发价=（名义成本*1.1X+跟单费+起版费）*税点
                     */
                    if(!in_array($ginfo['put_in_type'],array(5))){
                        //if(in_array($ginfo['product_type1'],array('钻石','珍珠','翡翠','宝石','彩钻','K金','PT','银饰','素金类')) && ($ginfo['cat_type1']<>'裸石'|| ($ginfo['cat_type']=='裸石' && $ginfo['zhengshuhao']==""))){
                        if(($ginfo['cat_type1']<>'裸石'|| ($ginfo['cat_type']=='裸石' && $ginfo['zhengshuhao']==""))){
                            $pifajia = ($mingyichengben*$pifa_jiajialv+$gendan_fee+$qiban_fee)*$tax_rate;
                            //echo $pifa_jiajialv."-".$gendan_fee."-".$qiban_fee."-".$tax_rate;
                            $pifajia = sprintf("%.2f",$pifajia);
                        }
                    }

                }
            }	else if($wholesale_company_type == 2){
                $pifajia = $mingyichengben;
            }
            $ginfo['pifajia'] = $pifajia;
        }
    }


    public function getFinBillNo($goods_id,$bill_type){
    	$sql = "select b.bill_no,b.check_time,b.from_company_id from warehouse_bill_goods bg,warehouse_bill b where bg.bill_id=b.id and b.bill_status=2 and b.to_company_id=58 and b.from_company_id<>58 and bg.goods_id='{$goods_id}' and b.bill_type='{$bill_type}' order by bg.id desc limit 1";
        return $this->db()->getRow($sql);
    }

}

?>
