<?php
/**
 *  -------------------------------------------------
 *   @file		: AppSalepolicyGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-17 18:36:47
 *   @update	:
 *  -------------------------------------------------
 */
class AppSalepolicyGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_salepolicy_goods';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
			"policy_id"=>"销售策略id",
			"goods_id"=>"货号或款号",
			"chengben"=>"成本价",
			"sale_price"=>"销售价",
			"jiajia"=>"加价率",
			"sta_value"=>"固定值",
			"isXianhuo"=>"现货状态0是期货1是现货",
			"create_time"=>"创建时间",
			"create_user"=>"创建人",
			"check_time"=>"审核时间",
			"check_user"=>"审核",
			"status"=>"状态:1保存2申请3审核通过4未通过5取消",
			"is_delete"=>"删除 1未删除 2已删除");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageTogetherList，分页列表
	 *
	 *	@url AppSalepolicyGoodsController/searchother
	 */
	function pageTogetherList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT `atg`.*,`ast`.`together_name`,`ast`.`is_split` FROM `app_together_policey_related` as `atp`,`app_together_goods_related` as `atg`,`app_salepolicy_together_goods` as `ast` WHERE `atp`.`id`=`atg`.`together_id` AND `ast`.`id`=`atp`.`together_id` ";
        if(isset($where['policy_id'])&&!empty($where['policy_id'])){
            $sql.=" AND `atp`.`policy_id` = ".$where['policy_id']."";
        }
		$sql .= " ORDER BY `atg`.`id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
    
    /**
     * 获取一条销售政策、打包策略下的商品
     * @param type $where
     * @return type
     */
    function getTogetherList($where) {
        $sql = "SELECT `atg`.`goods_id` FROM `app_together_policey_related` as `atp`,`app_together_goods_related` as `atg`,`app_salepolicy_together_goods` as `ast` WHERE `atp`.`id`=`atg`.`together_id` AND `ast`.`id`=`atp`.`together_id` ";
        if(isset($where['policy_id'])&&!empty($where['policy_id'])){
            $sql.=" AND `atp`.`policy_id` = ".$where['policy_id']."";
        }
        if(isset($where['together_id'])&&!empty($where['together_id'])){
            $sql.=" AND `atp`.`together_id`= ".$where['together_id'];
        }
		$sql .= " ORDER BY `atg`.`id` DESC";
		$data = $this->db()->getAll($sql);
		return $data;
    }
    
	/**
	 *	pageList，分页列表
	 *
	 *	@url AppSalepolicyGoodsController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT ag.*,bg.is_valid FROM `".$this->table()."` as ag left join base_salepolicy_goods as bg on ag.goods_id=bg.goods_id 
       left join `warehouse_shipping`.`warehouse_goods` as c on ag.goods_id=c.goods_id
         WHERE ag.`is_delete`=1 ";
         // 
        if(isset($where['policy_id'])&&!empty($where['policy_id'])){
            $sql.=" AND ag.policy_id = ".$where['policy_id']."";
        }
        if(isset($where['goods_id'])&&!empty($where['goods_id'])){
            $sql.=" AND ag.goods_id LIKE \"%".addslashes($where['goods_id'])."%\"";
        }

        if(isset($where['max_p'])&&!empty($where['max_p'])){
            $sql.=" AND ag.sale_price <= ".$where['max_p']."";
        }

        if(isset($where['min_p'])&&!empty($where['min_p'])){
            $sql.=" AND ag.sale_price >= ".$where['min_p']."";
        }

         if(isset($where['xianhuo'])&&$where['xianhuo']!==''){
             $sql.=" AND ag.isXianhuo = ".$where['xianhuo']."";
         }

        if(isset($where['is_valid'])&&$where['is_valid']!==''){
            $sql.=" AND c.is_on_sale = ".$where['is_valid']."";
        }

        //echo $sql;
		$sql .= " ORDER BY ag.id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 *	getAllList，所有
	 *
	 *	@url AppSalepolicyGoodsController/getAllList
	 */
	function getAllList ($where,$start,$limit)
	{
		//$sql = "SELECT ag.*,bg.is_valid FROM `".$this->table()."` as ag left join base_salepolicy_goods as bg on ag.goods_id=bg.goods_id WHERE ag.`is_delete`=1 ";
	    $sql = "SELECT ag.*,bg.is_valid FROM `".$this->table()."` as ag left join base_salepolicy_goods as bg on ag.goods_id=bg.goods_id
       left join `warehouse_shipping`.`warehouse_goods` as c on ag.goods_id=c.goods_id WHERE ag.`is_delete`=1 ";
	    
	    if(isset($where['policy_id'])&&!empty($where['policy_id'])){
            $sql.=" AND ag.policy_id = ".$where['policy_id']."";
        }
        if(isset($where['goods_id'])&&!empty($where['goods_id'])){
            $sql.=" AND ag.goods_id LIKE \"%".addslashes($where['goods_id'])."%\"";
        }

        if(isset($where['max_p'])&&!empty($where['max_p'])){
            $sql.=" AND ag.sale_price <= ".$where['max_p']."";
        }

        if(isset($where['min_p'])&&!empty($where['min_p'])){
            $sql.=" AND ag.sale_price >= ".$where['min_p']."";
        }

         if(isset($where['xianhuo'])&&$where['xianhuo']!==''){
             $sql.=" AND ag.isXianhuo = ".$where['xianhuo']."";
         }
       
        if(isset($where['is_valid'])&&$where['is_valid']!==''){
            $sql.=" AND c.is_on_sale = ".$where['is_valid']."";
        }

        //echo $sql;
		$sql .= " ORDER BY ag.id DESC LIMIT $start,$limit";
		$data['data'] = $this->db()->getAll($sql);
		return $data;
	}

	/**
	 *	getWarehouseBygoods_id，取货
	 *
	 *	@url AppSalepolicyGoodsController/getWarehouseBygoods_id
	 */
    function getWarehouseBygoods_id($goods_id){

        $keys=array('goods_id');
        $vals=array($goods_id);

        $ret=ApiModel::warehouse_api($keys,$vals,'GetWarehouseGoodsByGoodsid');
        return $ret;
    }
    
    function GetBaoxianFei($xiangkou) {
      $xiangkou = $xiangkou * 10000;
      $baoxianfei = ApiModel::style_api('', '', "getAllbaoxianfee");
      $i = 0;
      $j = 0;
      $k = 0;
      foreach ($baoxianfei as $k => $v) {
          $max[$i] = $v['max'] * 10000;
          $min[$j] = $v['min'] * 10000;
          $fee[$k] = $v['price'];
          $i++;$j++;$k++;
      }
      $count = count($max);
      for($i = 0; $i <$count; $i ++) {
        if ( $xiangkou >= $min[$i] && $xiangkou <= $max[$i]){
                return $fee[$i];
        }
    }
  }
	/**
	 *	getxiankouBygoods_id，取保险费
	 *
	 *	@url AppSalepolicyGoodsController/getWarehouseBygoods_id
	 */
    function getxiankouBygoods_id($goods_id){
        $sql = "SELECT  `zuanshidaxiao`,`jietuoxiangkou` FROM  `warehouse_shipping`.`warehouse_goods`
         WHERE `goods_id` in ('{$goods_id}') AND `tuo_type` in (2,3) AND `product_type` in 
('钻石','珍珠','珍珠饰品','翡翠','翡翠饰品','宝石','宝石饰品','钻石饰品','宝石饰品','宝石') ";
        return $this->db()->getRow($sql);
    }

	/**
	 *	查询货品信息
	 *
	 *	@url AppSalepolicyGoodsController/getWaregoodisonsale
	 */
    function getWaregoodisonsale($goods_id){
        if(empty($goods_id)){
            return false;
        }
        $str = '';
        if (isset($goods_id) && !empty($goods_id)) {
                $str .= " AND `goods_id` in ('" . $goods_id . "') ";
        }
        $str = ltrim($str, " AND");
        $sql = "SELECT * FROM `warehouse_shipping`.`warehouse_goods` WHERE " . $str;
        return $this->db()->getAll($sql);
    }
	/**
	 *	查询货品信息
	 *
	 *	@url AppSalepolicyGoodsController/getWaregoodisonsale
	 */
    function getWaregoodisAgeonsale($goods_id){
        if(empty($goods_id)){
            return false;
        }
        $sql = "SELECT * FROM `warehouse_shipping`.`warehouse_goods_age` WHERE `goods_id` =  '" . $goods_id . "' limit 1 ;";
        return $this->db()->getRow($sql);
    }    
	/**
	 *	getStyleBystyle_sn，取款
	 *
	 *	@url AppSalepolicyGoodsController/getStyleBystyle_sn
	 */
    function getStyleBygoods_sn($style_sn){
        $keys=array('style_sn');
        $vals=array($style_sn);
        $ret=ApiModel::style_api($keys,$vals,'GetStyleInfo');
        return $ret;
    }

	/**
	 *	getStyleBystyle_sn，取款
	 *
	 *	@url AppSalepolicyGoodsController/getStyleBystyle_sn
	 */
    function getStyleBystyle_sn($style_sn){
        $where='';
		if(!empty($style_sn))
		{
			$where .= " `style_sn` = '".$style_sn."'";
        }else{
            return false;
        }
        $sql = "select * from `front`.`base_style_info` " ."where ".$where." ;";
        return $this->db()->getRow($sql);
    }

	/**
	 *	GetStyleGoods，查询款式商品信息
	 *
	 *	@url AppSalepolicyGoodsController/GetStyleGoods
	 */
    function GetStyleGoods($goods_sn){
        $where='';
		if(!empty($goods_sn))
		{
			$where .= " `goods_sn` in ('".$goods_sn."')";
        }
        $sql = "select * from `front`.`list_style_goods` " .
                   "where ".$where." ;";
        return $this->db()->getAll($sql);
    }

     function getChengbenBygoods_id($goods_id){
        $sql = "SELECT  `chengbenjia` FROM  `warehouse_shipping`.`warehouse_goods`
         WHERE `goods_id` in ('{$goods_id}') ";
        return $this->db()->getOne($sql);
    }    
    
    function get_goods_id_by_ids($ids,$policy_id) {
        if(empty($ids) || count($ids) < 1){
            return array();
        }
        $ids = implode("','", $ids);
        $sql = "SELECT `goods_id` FROM `".$this->table()."` WHERE `goods_id` in ('{$ids}') AND `policy_id`={$policy_id} AND is_delete=1";
        return $this->db()->getAll($sql);
    }
	/**
	 *	getGoodsBy，根据id获取商品信息
	 *
	 *	
	 */
	function getGoodsById ($id)
	{
		//根据id获取所有货号
//		$sql = "SELECT goods_id,sale_price FROM `".$this->table()."` WHERE policy_id = {$id} and goods_id regexp '^[0-9]+$' and is_delete=1";
        //不区分现货和期货
		$sql = "SELECT `goods_id`,`sale_price`,`jiajia` FROM `".$this->table()."` WHERE `policy_id` = {$id} and `is_delete`=1";
		$sql .= " ORDER BY id DESC";
		$res = $this->db()->getAll($sql);
		return $res;
	}
	
	/**
	 *	getGoodsBy，根据goodsid获取所有信息
	 *
	 *
	 */
	function getInfoByGoodsId ($where)
	{
		//根据goodsid获取所有信息
		$sql = "SELECT `goods_id`  FROM `".$this->table()."` WHERE  is_delete=1 ";
		if($where['goods_id'] != ""){
			$sql .= " AND goods_id ='".$where['goods_id']."'";
		}
		if($where['policy_id'] != ""){
			$sql .= " AND policy_id =".$where['policy_id'];
		}
		$sql .= " ORDER BY id DESC";
     
		$res = $this->db()->getAll($sql);
		return $res;
	}
	/**
	 *	getGoodsBy，根据goodsid获取所有信息
	 *
	 *
	 */
	function getDeleteInfoByGoodsId($policy_id,$goods_id)
	{
		//根据goodsid获取所有信息
        $sql = "SELECT `goods_id`  FROM `".$this->table()."` WHERE  is_delete=2 ";
        $sql .= " AND goods_id ='".$goods_id."'";
        $sql .= " AND policy_id = '".$policy_id."' ";
		$res = $this->db()->getRow($sql);
		return $res;
	}
    function getPolicyNameByGoodsId($id)
    {
        $sql="SELECT b.`policy_name` FROM `".$this->table()."`  a left join `base_salepolicy_info` b on a.`policy_id`=b.`policy_id` WHERE b.is_delete=0 AND a.goods_id='$id'";
        $res=$this->db()->getAll($sql);
        return $res;
    }
    function getInfoByid($id)
    {
        $sql="SELECT `policy_id`,`goods_id` FROM `".$this->table()."`  WHERE is_delete=1 AND id='$id'";
        $res=$this->db()->getRow($sql);
        return $res;
    }


    public function saveAllG($info){
    	return true;
        if(empty($info)){
            return false;
        }
        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
             foreach($info as $key=>$val){
                 $f = array_keys($val);
                 $v = array_values($val);
                 $f = implode('`,`',$f);
                 $v = implode("','",$v);
                 $sql = "INSERT INTO `app_salepolicy_goods` (`$f`) VALUE('$v')";
                 $pdo->query($sql);
             }
            //批量更改销售政策商品中的is_policy商品
            $where = implode("','",array_column($info,'goods_id'));
            $usql = "UPDATE `base_salepolicy_goods` SET is_policy=2 WHERE `goods_id` in ('$where')";
            $pdo->query($usql);
        }
        catch(Exception $e){//捕获异常
            echo '<pre>';
            print_r($e);
            echo '</pre>';
            exit;
            $error = var_export($e,true);
            file_put_contents('kexiaoshoushangpinE.txt',$error,FILE_APPEND);
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }
        //如果没有异常，就提交事务
        $pdo->commit();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return true;

    }

    public function updateAppSalepolicyGoodsById($id,$updatedata){
        foreach($updatedata as $key => $val){
            $setArr[] = "{$key}={$val}";
        }
        $setStr = implode(',',$setArr);
        $sql = "UPDATE `app_salepolicy_goods` set $setStr where id = '$id' limit 1;";
       
        $this->db()->query($sql);
    }

    public function getInfoByGoodsIdAndPolicyId($goods_id,$policy_id){
        $sql = "SELECT * FROM `app_salepolicy_goods` where goods_id = '$goods_id' and policy_id = $policy_id and is_delete=1 limit 1;";
        return $this->db()->getRow($sql);
    }

    public function getInfoByGoodsIdAndPolicyIs_delete($goods_id,$policy_id){
        $sql = "SELECT ag.*,bg.is_valid FROM `app_salepolicy_goods` as ag left join base_salepolicy_goods as bg on ag.goods_id=bg.goods_id WHERE ag.`is_delete`=1 AND ag.goods_id='$goods_id' AND ag.policy_id = $policy_id ORDER BY ag.id DESC";
        return $this->db()->getRow($sql);
    }

    public function saveAllGU($info){
        if(empty($info)){
            return false;
        }
        foreach($info as $key=>$val){
            $policy_id = $val['policy_id'];
            $goods_id = $val['goods_id'];

            $sql = "SELECT * FROM `app_salepolicy_goods` where goods_id = '$goods_id' and policy_id = $policy_id limit 1;";
            $oldRow = $this->db()->getRow($sql);

            if($val['sta_value']!=$oldRow['sta_value'] || $val['jiajia']!=$oldRow['jiajia'] || $val['chengben']!=$oldRow['chengben'] || $val['sale_price']!=$oldRow['sale_price']){

                $setArr = array();  
                $setArr[] = "sta_value=".$val['sta_value'];
                $setArr[] = "jiajia=".$val['jiajia'];
                $setArr[] = "chengben=".$val['chengben'];
                $setArr[] = "sale_price=".$val['sale_price'];
                $setStr = implode(',',$setArr);
                $sql = "UPDATE `app_salepolicy_goods` set $setStr where goods_id = '$goods_id' and policy_id = $policy_id limit 1;";
                $this->db()->query($sql);

                $contentArr = array();
                if($val['sta_value']!=$oldRow['sta_value']){
                    $contentArr[]="将固定值由{$oldRow['sta_value']}修改为{$val['sta_value']} ";
                }elseif($val['jiajia']!=$oldRow['jiajia']){
                    $contentArr[]="将固定值由{$oldRow['jiajia']}修改为{$val['jiajia']} ";
                }elseif($val['chengben']!=$oldRow['chengben']){
                    $contentArr[]="将固定值由{$oldRow['chengben']}修改为{$val['chengben']} ";
                }elseif($val['sale_price']!=$oldRow['sale_price']){
                    $contentArr[]="将固定值由{$oldRow['sale_price']}修改为{$val['sale_price']} ";
                }
                $logmodel =  new AppSalepolicyChannelLogModel(18);
                $bespokeActionLog=array();
                $bespokeActionLog['policy_id']=$newdo['policy_id'];
                $bespokeActionLog['create_user']=$_SESSION['userName'];
                $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
                $bespokeActionLog['IP']=Util::getClicentIp();
                $bespokeActionLog['status']=1;
                $bespokeActionLog['remark']=implode(',',$contentArr);
                $logmodel->saveData($bespokeActionLog,array());
            }
        }
        return true;

    }

    public function GoodsRev($goods_id){
    	return true;
        if(empty($goods_id)){
            return false;
        }
        $sql = "select policy_id from app_salepolicy_goods WHERE is_delete=1 AND `goods_id`='".$goods_id."'";
        $res =  $this->db()->getAll($sql);
        if(count($res)==0){
            $sql = "UPDATE `base_salepolicy_goods` SET is_policy=1 WHERE `goods_id`='$goods_id'";
           return  $this->db()->query($sql);
        }else{
            return true;
        }

    }

	 //批量删除
    public function delManyDelete ($ids)
    {
    	if(count($ids)==0)
    	{
    		return true;
    	}
    	$sql = "update " . $this->table() . " set is_delete =2 WHERE `id` IN (".implode(",",$ids).")";
    	return $this->db()->query($sql);
    }

    
    public function deleteGoods($goods_id)
    {
        if(empty($goods_id)){
            return false;
        }
        $sql="delete from ".$this->table()." where goods_id = '$goods_id' AND is_delete=2 ;";
        $this->db()->query($sql);
    }

}

