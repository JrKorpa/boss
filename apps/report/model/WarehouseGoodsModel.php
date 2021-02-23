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
			"goods_name"=>"商品名称",
			"addtime"=>"添加时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ApplicationController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true , $is_down = false)
	{
		$time = date('Y-m-d H:i:s');
		$sql = "SELECT *,TIMESTAMPDIFF(DAY,change_time,if(chuku_time,chuku_time,'$time')) as 'thisage',TIMESTAMPDIFF(DAY, addtime,if(chuku_time,chuku_time,'$time'))  as 'allage' FROM `".$this->table()."`";
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
			$str .= " goods_id in (".$where['goods_id'].") AND ";
		}
		if($where['style_sn'] != "")
		{
                        $str .= $this->formatString($where['style_sn'],'goods_sn');

			//$str .= " goods_sn = '".$where['style_sn']."' AND ";
		}
		if($where['put_in_type'] !== "")
		{
			$str .= "put_in_type = '".$where['put_in_type']."' AND ";
		}
		if($where['weixiu_status'] !== "")
		{
			$str .= "weixiu_status = '".$where['weixiu_status']."' AND ";
		}
		if($where['is_on_sale'] !== "")
		{
			$str .= "is_on_sale = ".$where['is_on_sale']." AND ";
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
			$str .= "company_id = '".$where['company_id']."' AND ";
		}
		if($where['cat_type'] !== "" && $where['cat_type'] != "全部")
		{
			$str .= "cat_type = '".$where['cat_type']."' AND ";
		}
		if($where['cat_type1'] !== "" && $where['cat_type1'] != "全部")
		{
			$str .= "cat_type1 = '".$where['cat_type1']."' AND ";
		}
		if($where['warehouse_id'] !== "")
		{
			$str .= "warehouse_id = '".$where['warehouse_id']."' AND ";
		}
		//new add
		if($where['zhengshuhao'] !== "")
		{
			$str .= "zhengshuhao = '".$where['zhengshuhao']."' AND ";

		}
		if ($where['order_goods_ids'] !== '')
		{
			if($where['order_goods_ids'] == '1'){
				$str .= " order_goods_id <> 0 AND ";//已经绑定
			}else{
				$str .= " (order_goods_id = '0' or order_goods_id='') AND ";//未绑定
			}
		}
		if($where['shoucun'] !== '')
		{
			$str .= " shoucun = '".$where['shoucun']."' AND ";
		}
                if($where['pinpai'] !== '')
                {
                    $str .= " pinpai = '".$where['pinpai']."' AND ";
                }
                
                 if($where['luozuanzhengshu'] !== '')
                {
                    $str .= " luozuanzhengshu = '".$where['luozuanzhengshu']."' AND ";
                }
                
                if($where['xilie_name']){
                     
                    $str .= " goods_sn IN  (".$where['xilie_name'].") AND ";
                  
                }
                
		if ($where['kucunstart'] !== '')
		{
			$str .= " addtime >= '".$where['kucunstart']." 00:00:00' AND ";
		}
		if ($where['kucunend'] !== '')
		{
			$str .= " addtime <= '".$where['kucunend']." 23:59:59' AND ";
		}
		if($where['processor'] !== '')
		{
			$str .= " prc_id='".$where['processor']."' AND ";
		}
		if ($where['buchan'] !== '')
		{
			$str .= " buchan_sn='".$where['buchan']."' AND ";
		}
		if ($where['mohao'] !== '')
		{
			$str .= "mo_sn='".$where['mohao']."' AND ";
		}
		if ($where['zhushi'] !== '')
		{
			$str .= " zhushi='".$where['zhushi']."' AND ";
		}
		if ($where['zhengshuleibie'] !== '')
		{
			$str .= " zhengshuleibie='".$where['zhengshuleibie']."' AND ";
		}
		//差一个主成色重
		if($where['jinzhong_begin'] !== "") {
			$str .= " jinzhong >=".$where['jinzhong_begin']." AND ";
		}
		if($where['jinzhong_end'] !== "") {
			$str .= " jinzhong <=".$where['jinzhong_end']." AND ";
		}
		if($where['zs_color'] !== "")
		{
			$str .= " zhushiyanse = '".$where['zs_color']."' AND ";
		}
		if ($where['zs_clarity'] !== "")
		{
			$str .= " zhushijingdu = '".$where['zs_clarity']."' AND ";
		}
		if ($where['jinshi_type'] !== "") {
			$str .= " ziyin='".$where['jinshi_type']."' AND ";
		}
		if ($where['jintuo_type'] !== "")
		{
			$str .= " tuo_type='".$where['jintuo_type']."' AND ";
		}
		if ($where['jiejia'] !== "")
		{
			$str .= " jiejia = '".$where['jiejia']."' AND ";
		}
		if($where['guiwei'] == '1')
		{
			$str .= " ( box_sn = '' OR box_sn = '0-00-0-0') AND ";
		}elseif($where['guiwei'] == '2')
		{
			$str .= " ( box_sn != '' AND box_sn != '0-00-0-0') AND ";
		}
		if ($where['chanpinxian'] !== "")
		{
			$str .= "product_type='".$where['chanpinxian']."' AND ";
		}
		if ($where['chanpinxian1'] !== "" && $where['chanpinxian1'] != "全部")
		{
			$str .= "product_type1='".$where['chanpinxian1']."' AND ";
		}
		if ($where['zhushi_begin'] !== "") {
			$str .= "zuanshidaxiao >= '".$where['zhushi_begin']."' AND ";
		}
		if ($where['zhushi_end'] !== "") {
			$str .= "zuanshidaxiao <= '".$where['zhushi_end']."' AND ";
		}
		if($where['weixiu_company_id'] !== "")
		{
			$str .= "weixiu_company_id = '".$where['weixiu_company_id']."' AND ";
		}
		if($where['weixiu_warehouse_id'] !== "")
		{
			$str .= "weixiu_warehouse_id = '".$where['weixiu_warehouse_id']."' AND ";
		}
		if(isset($where['warehouse_ids_string'])&& $where['warehouse_ids_string']){
			$warehouse_ids_string=trim($where['warehouse_ids_string'],',');
			$str.="warehouse_id in ({$warehouse_ids_string})  AND ";
		}
		if(isset($where['company_ids_string'])&& $where['company_ids_string']){
			$company_ids_string=trim($where['company_ids_string'],',');
			$str.=" company_id in ({$company_ids_string}) AND ";
		}
		//此判断不要轻易动，有问题找张丽娟。。。。
		if(!in_array($_SESSION['userName'],array('董小华','张敏敏','admin')))
		{
			$str .= " goods_id not in(150409528627,150409528626,150409528625,150409528624,150409528623,150409528622,150409528621,150409528620,150409528619,150409528618,150409528617,150409528616,150409528615,150416530793,150416530794,150416530795,150416530796,150416530797,150416530798,150416530799,150416530800,150416530801,150416530802,150416530803,150416530804,150416530805,150416530806,150416530807,150416530808,150416530809,150429540286,150429540287,150429540288,150429540289,150429540290,150429540291,150429540292,150429540293,150429540294,150429540295,150429540296,150429540297,150429540298,150429540299,150429540300,150429540301,150429540302,150429540303,150429540304,150429540305,150429540306,150429540307,150429540308,150429540309)";
		}

		$sqltj = "SELECT count(*) as zong_num,sum(`mingyichengben`) as zong_chengbenjia FROM `".$this->table()."`";
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
			$sql .=" AND `is_on_sale` <> '100'";
			$sqltj .=" WHERE ".$str." and `is_on_sale` <> '100'";
		}else{
			$sql .= " WHERE `is_on_sale` <> '100'";
			$sqltj .=" WHERE `is_on_sale` <> '100'";
		}
		$sql .= " ORDER BY id DESC";
		//echo $sql;die;
		$tongji = $this->db()->getRow($sqltj);
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
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

        function GetSupplierList($arr = array())
	{
		$ret = ApiModel::pro_api('GetSupplierList',$arr);
		return $ret['return_msg']['data'];
	}

	function _getGoodsId(){
		//$goods_id = date("yms").rand(10,99).rand(10,99).rand(10,99);
		//return $goods_id;
		//预防并发时写入货号重复添加写入锁
		$sql="select max(CAST(goods_id AS UNSIGNED)) as num from `warehouse_goods`";
		$num=$this->db()->getOne($sql);
		return $num;
	}

	/*
	 * 根据货号 查询货品信息
	 * @param 货号
	 **/
	public function getGoodsByGoods_id($goods_id){
		$sql = "SELECT * FROM `warehouse_goods` WHERE `goods_id`='{$goods_id}'";
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
	public function table_GetGoodsInfo($gids,$label,$is_on_sale = 2,$bill_id,$weixiu_status =0,$warehouse_id=0,$order_goods_id=0,$company=0,$put_in_type=0){
		$error = ['1'=>'查无此货','2'=>'不是库存状态','3'=>'货品没有制 维修退货单 或 制了维修退货单没有审核','4'=>'货品不是批发借货库、深圳业务借贷库的货品，不能批量销售单','5'=>'货品已经绑定订单，不能制批量销售单','6'=>'货品出货公司不正确，不能制单','7'=>'单据所选入库方式不同，不能制退货返厂单'];
		array_shift($label);$goods_ids = array();
		//如果有bill_id,则是编辑单据
		if($bill_id){
			$sql = "SELECT `id`,`goods_id` FROM `warehouse_bill_goods` WHERE `bill_id` = '".$bill_id."'";
			$goods_ids = $this->db()->getAll($sql);
			$goods_ids = array_column($goods_ids,'goods_id','id');
		}
		$sql = "SELECT ".implode(',',$label).",`put_in_type`,`company`,`order_goods_id`,`warehouse_id`,`weixiu_status`,`is_on_sale` FROM `warehouse_goods` WHERE `goods_id` = ?";
		//echo $sql;
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
			//var_dump($new_order_goods_id);exit;
			if(!empty($res)){
				if(end($res) != $is_on_sale && !in_array($g,$goods_ids)){
					$data['error'][] = $g.' : '.$error[$is_on_sale];
					//var_dump($data);exit;
				}elseif($weixiu_status && $new_weixiu != $weixiu_status){
					$data['error'][] = $g.' : '.$error[$weixiu_status];
				}elseif($warehouse_id && !(in_array($new_warehouse_id,$warehouse_id))){
					$data['error'][] = $g.' : '.$error[4];
				}elseif($order_goods_id && $new_order_goods_id){
					$data['error'][] = $g.' : '.$error[5];
				}elseif($company && $new_company!=$company){
					$data['error'][] = $g.' : '.$error[6];
				}elseif($put_in_type && $new_put_in_type!=$put_in_type){
					$data['error'][] = $g.' : '.$error[7];
				}else{
					array_pop($res);
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
                    $goods_id[] = trim($k);
                }
               // var_dump($goods_id);exit;
            }

            if($type){
                $goods_info_list = $this->make_print_code_csv($goods_id,$jiajialv,$jiajianum,'',2,$type);
            }else{
                $goods_info_list = $this->make_print_code_csv($goods_id,$jiajialv,$jiajianum,'',2,$type);
            }

            exit;
        }
    public function make_print_code_csv($codes, $jiajialv=1, $jiajianum=0,$company_info = array(),$type =0,$type2){

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
      $content = '';
      if ($type2){
          $content .= "货号,款号,基因码,手寸,长度,主石粒数,主石重,副石粒数,副石重,加工商编号,总重,净度,颜色,证书号,国际证书,主石切工,标签备注,主石,副石,主成色,饰品分类,款式分类,名称,石3副石,石3粒数,石3重,石4副石,石4粒数,石4重,石5副石,石5粒数,石5重,主成色重,副成色,副成色重,买入工费,计价工费,加价率,最新零售价,模号,品牌,证书数量,配件数量,时尚款,系列,属性,类别,成本价,入库日期,加价率代码,主石粒重,副石粒重,标签手寸,字印,货币符号零售价,新成本价,新零售价,一口价,标价,定制价,A,B,C,D,E,F,G,H,I,HB_G,HB_H,原价\r\n";
          $price2 = '';
      }else {
        $content = "货号,款号,基因码,手寸,长度,主石粒数,主石重,副石粒数,副石重,加工商编号,总重,净度,颜色,证书号,国际证书,主石切工,标签备注,主石,副石,主成色,饰品分类,款式分类,名称,石3副石,石3粒数,石3重,石4副石,石4粒数,石4重,石5副石,石5粒数,石5重,主成色重,副成色,副成色重,买入工费,计价工费,加价率,最新零售价,模号,品牌,证书数量,配件数量,时尚款,系列,属性,类别,成本价,入库日期,加价率代码,主石粒重,副石粒重,标签手寸,字印,货币符号零售价,新成本价,新零售价,一口价,标价,定制价,A,B,C,D,E,F,G,H,I,HB_G,HB_H\r\n";
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
              echo "没有查到该货号：".$goods_id."信息,请核实后再做打印";exit;

          }

          $xilie = [];
          $base_style = 8; //基本款
          if($line['goods_sn']){
              $style_sql = "SELECT xilie FROM `base_style_info` WHERE style_sn = '{$line['goods_sn']}' AND check_status = 3";
              $xilie = explode(',',DB::cn(12)->db()->query($style_sql)->fetchColumn());
          }

          $zhuchengse = $line['caizhi'];
          $kuanshi_type = $line['kuanshi_type'];

          if($type ==2){//打印活动标签
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
          if($line['xianzaixiaoshou'] != 'err_AB' && $line['xianzaixiaoshou'] != 'err_WM' ){
               //如果商品产品线镶嵌类，金托类型是空托：标签价=（名义成本+保险费）*加价率+系数
              if (in_array($line['product_type'], $xiangqian_product_type) && ($line['tuo_type'] == 3) || $line['tuo_type'] == 2){

                  $price = round(($line['mingyichengben']+$baoxianfei)*trim($jiajialv) + trim($jiajianum));
                  //file_put_contents('c:/a.txt', 'baoxianfee:'.$baoxianfei.'jiajialv:'.$jiajialv.'num:'.$jiajianum);
              }else{
                  $price = round($line['mingyichengben']*trim($jiajialv) + trim($jiajianum));
              }
              if ($line['caizhi'] == '18K金' || $line['caizhi'] == '18K白金' || $line['caizhi'] == '18K玫瑰金' || $line['caizhi'] == '18K彩金'){
                  $other_price = $line['jinzhong'] * 400 + $price + 500 ;
                  $other_price_string = "PT定制价￥" . ceil($other_price);
              }elseif ($line['caizhi'] == '铂Pt950' || $line['caizhi'] == 'PT950'){
                  $other_price = $price - $line['jinzhong'] * 250;
                  $other_price_string = "18K金定制价￥" . ceil($other_price);
              }else{
                  $other_price_string = "";
              }
              if ($type2) {
                  $price2 = $price;
                  $price = substr($price,0,-2).'99';
              }else{
                  $price2 = '';
              }
          }else{
              $price = '待核实';
          }
          
          if($jiajialv <=1){
              $msg =  '货号：'.$goods_id.' price:'.$price.' mingyichengben:'.$line['mingyichengben'].' baoxianfee:'.$baoxianfei.' jiajialv:'.$jiajialv.' num:'.$jiajianum."\n";
              echo "出现售价不大于成本价异常：".$msg." ,请重试或联系技术部！";exit;
          }
          
          //售价不大于名义成本  直接die
          if(bccomp($left=$price,$right=$line['mingyichengben']) < 1){
              //file_put_contents('c:/a.txt', 'baoxianfee:'.$baoxianfei.'jiajialv:'.$jiajialv.'num:'.$jiajianum);
              $msg =  '货号：'.$goods_id.' price:'.$price.' mingyichengben:'.$line['mingyichengben'].' baoxianfee:'.$baoxianfei.' jiajialv:'.$jiajialv.' num:'.$jiajianum."\n";
              echo "出现售价不大于成本价异常：".$msg." ,请重试或联系技术部！";exit;
          }
          
          $line['goods_sn'] = preg_replace('/^[(\xc2\xa0)|\s]+/', '', $line['goods_sn']); //去掉中文空格
          $line['fuchengse'] = '';
          $line['fuchengsezhong'] = '';
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
          "\"" . $line['cat_type'] . "\"," .
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
          "\"".$price2."\"\r\n".	// yuanjia
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
 
 
}

