<?php
/**
 *  -------------------------------------------------
 *   @file		: ListStyleGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-13 17:03:57
 *   @update	:
 *  -------------------------------------------------
 */
class ListStyleGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'list_style_goods';
		$this->pk='goods_id';
		$this->_prefix='';
        $this->_dataObject = array("goods_id"=>" ",
			"product_type_id"=>"产品线id",
			"cat_type_id"=>"款式分类id",
			"style_id"=>"款式id",
			"style_sn"=>"款式编号",
			"style_name"=>"款式名称",
			"goods_sn"=>"产品编号",
			"shoucun"=>"手寸",
			"xiangkou"=>"镶口",
			"caizhi"=>"材质",
			"yanse"=>"颜色",
			"zhushizhong"=>"主石重",
			"zhushi_num"=>"主石数",
			"fushizhong1"=>"副石1重",
			"fushi_num1"=>"副石1数",
			"fushizhong2"=>"副石2重",
			"fushi_num2"=>"副石2数",
			"fushi_chengbenjia_other"=>"其他副石副石成本价",
			"weight"=>"材质金重",
			"jincha_shang"=>"金重上公差",
			"jincha_xia"=>"金重下公差",
			"dingzhichengben"=>"定制成本",
			"is_ok"=>"是否上架;0为下架;1为上架",
			"last_update"=>"最后更新时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ListStyleGoodsController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` as `sg`,`base_style_info` as `si` WHERE `sg`.`style_id`=`si`.`style_id` ";
		if($where['style_sn'] != "")
		{
			$sql .= " AND `sg`.`style_sn` ='".$where['style_sn']."'";
		}
		if($where['caizhi'] != "")
		{
			$sql .= " AND `sg`.`caizhi` =".$where['caizhi'];
		}
        if($where['yanse'] != "")
        {
            $sql .= " AND `sg`.`yanse` =".$where['yanse'];
        }
		if($where['xiangkou1'] != "")
		{
			$sql .= " AND `sg`.`xiangkou` >=".$where['xiangkou1']."";
		}
		if($where['xiangkou2'] != "")
		{
			$sql .= " AND `sg`.`xiangkou` <=".$where['xiangkou2']."";
		}
		if($where['finger1'] != "")
		{
			$sql .= " AND `sg`.`shoucun` >=".$where['finger1']."";
		}
		if($where['finger2'] != "")
		{
			$sql .= " AND `sg`.`shoucun` <=".$where['finger2']."";
		}
		if($where['status'] != "")
		{
			$sql .= " AND `sg`.`is_ok` =".($where['status']==1?1:0);
		}
		if(isset($where['xilie']) && !empty($where['xilie'])){
            if(count($where['xilie'])==1){
                 $sql.= " AND `xilie` like '%,".$where['xilie'][0].",%'";
            }else{
                $str = "";
                foreach ($where['xilie'] as $val){
                     $str.=" `xilie` like '%,".$val.",%' or ";
                }
                $str = rtrim($str," or");
                $sql .= " AND (".$str.")";
               
            }
        }
        if(isset($where['is_quick_diy']) && $where['is_quick_diy']!=""){
            $sql .=" AND sg.is_quick_diy={$where['is_quick_diy']}";
        }
		$sql .= " ORDER BY `sg`.`goods_id` DESC";//echo $sql;exit;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
  
	/**
	 *	pageList，分页列表
	 *
	 *	@url ListStyleGoodsController/search
	 */
	function pageListAll ($where)
	{
		$sql = "SELECT * FROM `".$this->table()."` as `sg`,`base_style_info` as `si` WHERE `sg`.`style_id`=`si`.`style_id` ";
		if($where['style_sn'] != "")
		{
			$sql .= " AND `sg`.`style_sn` ='".$where['style_sn']."'";
		}
		if($where['caizhi'] != "")
		{
			$sql .= " AND `sg`.`caizhi` =".$where['caizhi'];
		}
		if($where['xiangkou1'] != "")
		{
			$sql .= " AND `sg`.`xiangkou` >=".$where['xiangkou1']."";
		}
		if($where['xiangkou2'] != "")
		{
			$sql .= " AND `sg`.`xiangkou` <=".$where['xiangkou2']."";
		}
		if($where['finger1'] != "")
		{
			$sql .= " AND `sg`.`shoucun` >=".$where['finger1']."";
		}
		if($where['finger2'] != "")
		{
			$sql .= " AND `sg`.`shoucun` <=".$where['finger2']."";
		}
		if($where['status'] != "")
		{
			$sql .= " AND `sg`.`is_ok` =".$where['status'];
		}
		if($where['xilie'] != "")
		{
			$sql .= " AND `si`.`xilie` in(".$where['xilie'].")";
		}
		$sql .= " ORDER BY `sg`.`goods_id` DESC";//echo $sql;exit;

		$data = $this->db()->getAll($sql);
		return $data;
	}        

        function insertListGoods($table_name,$data,$where){
            $style_id = $where['style_id'];
            $style_sn = $where['style_sn'];
            $row = $this->getStyleGoodsByStyleId($table_name,$style_id);
            //如果已经有记录了则删除原来的数据重新生成
            if($row){
               $sql =" DELETE FROM `".$table_name."`  WHERE style_id=".$style_id;
               $res = $this->db()->query($sql);
            }
              
            foreach ($data as $val){
                $arr=  explode(",", $val);
                $material = $arr[0];
                $finger = $arr[1];
                $xiangkou = $arr[2];
                $sql =" INSERT INTO `".$table_name."` SET  material='".$material."' , finger='".$finger."' ,xiangkou='".$xiangkou."',style_id='".$style_id."',style_sn='".$style_sn."' "; 
               
                $res = $this->db()->query($sql);
            }
            
            return $res;
        }
        
        public function getStyleGoodsByStyleId($table_name,$style_id){
             $sql =" SELECT `style_id` FROM `".$table_name."` where `style_id`= ".$style_id;
             return $res = $this->db()->getAll($sql);
        }
       
        public function deleteStyleList($where){
            $style_id = $where['style_id'];
            $sql = " DELETE FROM `".$this->table()."` WHERE `style_id`=".$style_id."";
            if(isset($where['xiangkou'])){
                $sql.="  AND `xiangkou`='".$where['xiangkou']."'";
            }
            return $this->db()->query($sql);
        }
         /*
         * 更新商品成本价格 BY linian
        */
        function updateChengbenPrice($where){
        	
        	if(isset($where['chengbenjia'])&&!empty($where['goods_id'])){
        		$chengben = $where['chengbenjia'];
        		$goods_id = $where['goods_id'];
        		$sql = "UPDATE  `".$this->table()."` SET `dingzhichengben`={$chengben} WHERE `goods_id`={$goods_id}";
                return $this->db()->query($sql);
        	}   
        	return 1;
        }
        
        public function getAllGoodsinfo($style_id,$caizhi,$stone){
        	$where = "WHERE 1 ";        	
        	if(isset($style_id)&&!empty($style_id)){
        		$where.=" and `style_id` = {$style_id}";
        	}
        	if(isset($caizhi)&&!empty($caizhi)){
        		$where.=" and `caizhi` = {$caizhi}";
        	}
        	if(isset($stone)&&!empty($stone)){
        		$where.=" and `xiangkou` = '{$stone}'";
        	}
        	
        	$sql ="SELECT * FROM `{$this->table()}` {$where}";
        	$res = $this->db()->getAll($sql);
			return $res;
        }


        public function getAllGoodsinfoByids($ids_str){
            
            $sql ="SELECT * FROM `{$this->table()}` WHERE `goods_id` in({$ids_str})";
            //echo $sql;exit;
            return $res = $this->db()->getAll($sql);
        }
        


        /**
         * 通过接口去批量修正成本价
         * @param array $goods_id
         * @param  array $chengbenjia
         * @return array 数组
         */
        public function UpdateSalepolicyChengben($goods_id,$chengbenjia){
             $res = array($goods_id,$chengbenjia);
            $res = ApiSalePolicyModel::UpdateSalepolicyChengben($res);
            //var_dump($res);exit;
          	if($res){
          		return $res;
         	 }

        }
        
        //根据id获取商品数据：有联表，为了获取
        public function getStyleGoods($where){
            $str = '';
            if(isset($where['id_in']) && !empty($where['id_in'])){
                $id_in = $where['id_in'];
        		$str.=" AND a.`goods_id` in ($id_in)";
        	}
            
            if(isset($where['is_ok'])){
                $is_ok = $where['is_ok'];
        		$str.=" AND a.`is_ok` = ".$is_ok;
        	}
            
            $sql = "SELECT  a.*,b.`check_status` FROM ".$this->table()." as a ,`base_style_info` as b WHERE a.`style_id`=b.`style_id` ".$str;
            return  $this->db()->getAll($sql);
        }
        
        function updateStyleGoodsStupdate($where){
            
            if(!isset($where['is_ok'])){
                    return false;
            }
         
            $is_ok = $where['is_ok'];
            if(empty($where['id_in'])){
                 return false;
            }
           
            $id_in = $where['id_in'];
            $sql = "UPDATE  `".$this->table()."` SET `is_ok`={$is_ok} WHERE `goods_id` in ({$id_in})";
            
            return $this->db()->query($sql);
        }
        
        /*
         * 更新此款的商品状态
         */
        public function UpdateListGoodsByStyleSn($where){
            if(!isset($where['style_sn'])){
                return false;
            }
            if(!isset($where['is_ok'])){
                return false;
            }
            $is_ok = $where['is_ok'];
            $goods_sn = $where['style_sn'];
            $sql = "UPDATE  `".$this->table()."` SET `is_ok`={$is_ok} WHERE `style_sn` ='".$goods_sn."'";
            
            return $this->db()->query($sql);
        }

    public function StyleFee($str){
        if($str==''){
            return false;
        }
        $sql = "SELECT `style_sn`,`fee_type`,`price` FROM `app_style_fee` WHERE `style_sn` IN ('$str')";
        return $this->db()->getAll($sql);
    }

    public function getlistgoodsbygoodssn($goods_sn_in)
    {
        # code...
        $sql = "SELECT * FROM `".$this->table()."` WHERE `goods_sn` = '{$goods_sn_in}'";
        return $this->db()->getRow($sql);
    }
    /**
     * 快速定制商品码查询
     * @param unknown $goods_sn
     */
    public function getQuickDiyGoodsByGoodsSn($goods_sn){
        $sql = "SELECT a.goods_sn,IFNULL(b.status,0) as is_quick_diy FROM `".$this->table()."` a left join app_style_quickdiy b on a.goods_sn=b.goods_sn WHERE a.`goods_sn` = '{$goods_sn}'";
        return $this->db()->getRow($sql);
    }
    public function deletegoods_sninfo($goods_sn_in)
    {
        # code...
        if(!$goods_sn_in){
            return false;
        }
        $sql = " DELETE FROM `".$this->table()."` WHERE `goods_sn` = '{$goods_sn_in}'";
        return $this->db()->query($sql);
    }
}

?>