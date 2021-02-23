<?php
/**
 *  -------------------------------------------------
 *   @file		: BoxGoodsLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 19:06:59
 *   @update	:
 *  -------------------------------------------------
 */
class BoxGoodsLogModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'box_goods_log';
      	$this->pk='id';
		$this->_prefix='';
	    $this->_dataObject = array(
		"id"=>"主键",
		"good_id"=>"货号",
		"type"=>"类型 （上架、下架）",
		'create_time' => "操作时间",
		"create_user"=>"操作人",
		"warehouse"=>"仓库",
		'box_sn'=>"柜位");

		parent::__construct($id,$strConn);
	}

    function pageList ($where,$page,$pageSize=10,$useCache=true)
    {
        $sql = "SELECT * FROM `box_goods_log`";
        $str = '';
        if(isset($where['goods_id']) && $where['goods_id'] != "")
        {
            $str .= "`goods_id` = '".$where['goods_id'].'\' AND ';
        }
        if(isset($where['type']) && $where['type'] != "")
        {
            $str .= "`type` = ".$where['type'].' AND ';
        }
        if(isset($where['create_user']) && $where['create_user'] != "")
        {
            $str .= "`create_user` = '".$where['create_user'].'\' AND ';
        }
        if($where['time_start'] !== ""){
            $str .= "`create_time`>='{$where['time_start']} 00:00:00' AND ";
        }
        if ($where['time_end'] !== ""){
            $str .= "`create_time` <= '{$where['time_end']} 23:59:59' AND ";
        }
        if($str)
        {
            $str = rtrim($str,"AND ");//这个空格很重要
            $sql .=" WHERE ".$str;
        }
        if($where['__order'] && $where['__desc_or_asc']){
            $sql .= " ORDER BY `{$where['__order']}` {$where['__desc_or_asc']}";
        }else{
            $sql .= " ORDER BY `id` DESC";
        }
        // echo $sql;
        $data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
        return $data;
    }
	/**
	 * 添加货品柜位变动记录
	 * @param $type int 1(下架)/2(上架)
	 */
	public function addLog($goods_id_arr, $type=1){
		$info = array();
		$time = date('Y-m-d H:i:s');
		$user = (isset($_SESSION['userName']) && !empty($_SESSION['userName'])) ? $_SESSION['userName'] :'系统自动下架';
	
		foreach ($goods_id_arr as $val){
			$arr = $this->GetBoxSnByGoods($val);
			if(empty($arr)){
			    $arr['box_sn'] = '';
			    $arr['warehouse'] = '';
			}
			$arr['goods_id'] = $val;
			$arr['type'] = $type;
			$info[] = $arr;
		}
		$sql = "INSERT INTO `box_goods_log` (`goods_id`,`type`,`create_time`,`create_user`,`warehouse`,`box_sn`) VALUES ";
		$str = '';
		foreach($info AS $val)
		{
			$str .= "('{$val['goods_id']}', {$val['type']}, '{$time}', '{$user}', '{$val['warehouse']}', '{$val['box_sn']}'),";
				
			// 2015-10-30
			// 在warehouse_goods_age增加一个最后上架时间的字段last_onshelf_dt，datetime类型， 默认值为"0000-00-00 00:00:00"。程序需要改成写入最后上架时间
	
			$goods_id = $val['goods_id'];
			$this->update_last_onshelf_dt($goods_id);
		}
		$str = rtrim($str, ',');
	
	
		if(strlen($str)){
			$sql = $sql.$str;
			$this->db()->query($sql);
		}
		 
	
	
	
	}
	/**
	 * 添加货品柜位变动记录
	 * @param $type int 1(下架)/2(上架)
	 */
	public function addLog1($goods_id_arr, $type=1){
		$info = array();
		$time = date('Y-m-d H:i:s');
		$user = (isset($_SESSION['userName']) && !empty($_SESSION['userName'])) ? $_SESSION['userName'] :'系统自动下架';

		foreach ($goods_id_arr as $val){
			$arr = $this->GetBoxSnByGoods($val['goods_id']);
			if(empty($arr)){
			    $arr['box_sn'] = '';
			    $arr['warehouse'] = '';
			}
			$arr['goods_id'] = $val['goods_id'];
			$arr['type'] = $type;
			$arr['box_sn'] = $val['box_sn'];
			$info[] = $arr;
		}
		
		
		$sql = "INSERT INTO `box_goods_log` (`goods_id`,`type`,`create_time`,`create_user`,`warehouse`,`box_sn`) VALUES ";
		$str = '';
		foreach($info AS $val)
		{
			$str .= "('{$val['goods_id']}', {$val['type']}, '{$time}', '{$user}', '{$val['warehouse']}', '{$val['box_sn']}'),";
		 
		 // 2015-10-30
		// 在warehouse_goods_age增加一个最后上架时间的字段last_onshelf_dt，datetime类型， 默认值为"0000-00-00 00:00:00"。程序需要改成写入最后上架时间

			$goods_id = $val['goods_id'];
			$this->update_last_onshelf_dt($goods_id);
		}
		$str = rtrim($str, ',');
		
		
		if(strlen($str)){
			$sql = $sql.$str;
			$this->db()->query($sql);
		}
		   
		
		
		
	}
	
	public function update_last_onshelf_dt($goods_id){
		$sql = "SELECT id FROM `warehouse_goods_age` WHERE `goods_id` = '{$goods_id}'";
		$ret = $this->db()->getRow($sql);
		$time = date('Y-m-d H:i:s');
		if(empty($ret))
		{
			$sqllos = "INSERT INTO `warehouse_goods_age`(`warehouse_id`,`goods_id`,`last_onshelf_dt`) VALUES ('0','{$goods_id}','{$time}')";
		}
		else
		{
			$sqllos = "update `warehouse_goods_age` set `last_onshelf_dt` = '{$time}' where `goods_id` = '{$goods_id}'";
		}
		$re = $this->db()->query($sqllos);
		 
	}



	//根据货号，获取该货当前所在的柜位号
	public function GetBoxSnByGoods($goods_id){
		$sql = "SELECT `b`.`box_sn`,`c`.`name` AS `warehouse` FROM `goods_warehouse` AS `a` LEFT JOIN `warehouse_box` AS `b` ON `a`.`box_id` = `b`.`id`  INNER JOIN `warehouse` AS `c` ON `b`.`warehouse_id` = `c`.`id` WHERE `a`.`good_id` = '{$goods_id}'";
		return $this->db()->getRow($sql);
	}



}
?>
