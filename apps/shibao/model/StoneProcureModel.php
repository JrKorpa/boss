<?php
/**
 *  -------------------------------------------------
 *   @file		: StoneProcureModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-28 15:47:27
 *   @update	:
 *  -------------------------------------------------
 */
class StoneProcureModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'stone_procure';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"pro_sn"=>"采购单号",
"pro_type"=>"采购方式",
"pro_ct"=>"采购重量",
"pro_total"=>"采购总金额",
"is_batch"=>"分批采购",
"check_status"=>"审核状态",
"create_id"=>"制单人ID",
"create_user"=>"制单人",
"create_time"=>"制单时间",
"note"=>"备注",
"refuse_cause"=>"驳回原因",
"check_plan"=>"已审批人数");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url StoneProcureController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."` WHERE `check_status` <> '3'";
		if(isset($where['pro_sn']) && $where['pro_sn'] != "")
		{
			$sql .= " AND `pro_sn` like \"%".addslashes($where['pro_sn'])."%\"";
		}
		if(isset($where['pro_type']) && $where['pro_type']!='0')
		{
			$sql .= " AND `pro_type`='".$where['pro_type']."'";
		}
		if(isset($where['check_status']))
		{
			$sql .= " AND `check_status`='".$where['check_status']."'";
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url StoneProcureController/search
	 */
	function passPageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE `check_status` = '3'";
		if(isset($where['pro_sn']) && $where['pro_sn'] != "")
		{
			$sql .= " AND `pro_sn` like \"%".addslashes($where['pro_sn'])."%\"";
		}
		if(isset($where['pro_type']) && $where['pro_type']!='0')
		{
			$sql .= " AND `pro_type`='".$where['pro_type']."'";
		}
		$sql .= "ORDER BY `id` DESC";
//		echo $sql;exit;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function getDetailList($where,$page,$pageSize=10,$useCache=true){
		$select = ['keep_ct','keep_num','pro_ct','ct_norms','color_norms','clarity_norms','pro_budget'];
		$sql = "SELECT ".implode(',',$select)." FROM `stone_procure_details` WHERE ";
		$sql .= "`pro_id` = '".$where['pro_id']."'";
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function mkJsonTable($label = false){
		$dict = new DictModel(1);
		$arr = [
			'id'=>'#stone_procure_detail',
			'title'=>[
				'0'=>'期初库存重量(ct)',
				'1'=>'期初库存数量(粒)',
				'2'=>'预计采购重量(ct)',
				'3'=>'重量规格',
				'4'=>'颜色规格',
				'5'=>'净度规格',
				'6'=>'采购预算金额(&yen;)',
			],
			'label'=>[
				'0'=>'keep_ct',
				'1'=>'keep_num',
				'2'=>'pro_ct',
				'3'=>'ct_norms',
				'4'=>'color_norms',
				'5'=>'clarity_norms',
				'6'=>'pro_budget',
			],
			'columns'=>[
				'0'=>['type'=>'numeric','format'=>'0,0.000'],
				'1'=>['type'=>'numeric','format'=>'0,0'],
				'2'=>['type'=>'numeric','format'=>'0,0.000'],
				'3'=>['editor'=>'select','selectOptions'=>array_merge(['0'=>'请选择'],$dict->getEnums('stone.ct_norms'))],
				'4'=>['editor'=>'select','selectOptions'=>array_merge(['0'=>'请选择'],$dict->getEnums('stone.color_norms'))],
				'5'=>['editor'=>'select','selectOptions'=>array_merge(['0'=>'请选择'],$dict->getEnums('stone.clarity_norms'))],
				'6'=>['type'=>'numeric','format'=>'0,0.00'],
			]
		];
		return (!$label)?$arr:$arr['label'];
	}

	/**
	 * 明细转为关联数组
	 */
	public function getAssocData($data){
		if(!is_array($data[0]) && empty($data)){
			return false;
		}
		$label = $this->mkJsonTable(true);
		foreach ($data as $k=>$v) {
			if(array_search('',$v)){return false;}
			$u_k = array_diff_key($label,$v);
			foreach ($u_k as $u=>$p) {
				unset($label[$u]);
			}
			$data[$k] = array_combine($label,$v);
		}
		return $data;
	}

	/**
	 * 生成采购单号
	 */
	public function createProSn(){
		$sql = 'SELECT `pro_sn` FROM `stone_procure` WHERE id = (SELECT max(id) FROM `stone_procure`)';
		$str = $this->db()->getOne($sql);
		$no = (substr($str,2,8) != date('Ymd',time()))?1:intval(substr($str,10))+1;
		return  'SQ'.date('Ymd',time()).str_pad($no,4,"0",STR_PAD_LEFT);
	}

	public function insert2($newdo,$detail,$table,$u_field){
		$olddo = array();
		$dict = new DictModel(1);
		$ct_norms = $dict->getEnumArray('stone.ct_norms');
		$color_norms = $dict->getEnumArray('stone.color_norms');
		$clarity_norms = $dict->getEnumArray('stone.clarity_norms');
		$ct_norms = array_column($ct_norms,'label','name');
		$color_norms = array_column($color_norms,'label','name');
		$clarity_norms = array_column($clarity_norms,'label','name');

		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			/*----------*/
			$lastID = $this->saveData($newdo,$olddo);
			foreach ($detail as $k=>$v) {
				$detail[$k][$u_field] = $lastID;
				$detail[$k]['ct_norms'] = array_search ($v['ct_norms'],$ct_norms);
				$detail[$k]['color_norms'] = array_search ($v['color_norms'],$color_norms);
				$detail[$k]['clarity_norms'] = array_search ($v['clarity_norms'],$clarity_norms);
			}
			foreach ($detail[0] as $k=>$v) {
				$param[$k] = ":".$k;
			}
			$sql = "INSERT INTO `".$table."` (".implode(',',array_flip($param)).") VALUES (".implode(',',$param).")";
			$_res = $pdo->prepare($sql);
			foreach ($detail as $row) {
				$_res->execute($row);
			}
			/*----------*/
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return $lastID;
	}

	public function update2($newdo,$olddo,$detail,$table,$u_field){
		$dict = new DictModel(1);
		$ct_norms = $dict->getEnumArray('stone.ct_norms');
		$color_norms = $dict->getEnumArray('stone.color_norms');
		$clarity_norms = $dict->getEnumArray('stone.clarity_norms');
		$ct_norms = array_column($ct_norms,'label','name');
		$color_norms = array_column($color_norms,'label','name');
		$clarity_norms = array_column($clarity_norms,'label','name');

		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			/*----------*/
			$this->saveData($newdo,$olddo);
			$sql = "DELETE FROM `".$table."` WHERE `".$u_field."` = ".$newdo['id'];
			$pdo->query($sql);
			foreach ($detail as $k=>$v) {
				$detail[$k][$u_field] = $newdo['id'];
				$detail[$k]['ct_norms'] = array_search($v['ct_norms'],$ct_norms);
				$detail[$k]['color_norms'] = array_search($v['color_norms'],$color_norms);
				$detail[$k]['clarity_norms'] = array_search($v['clarity_norms'],$clarity_norms);
			}
			foreach ($detail[0] as $k=>$v) {
				$param[$k] = ":".$k;
			}
			$sql = "INSERT INTO `".$table."` (".implode(',',array_flip($param)).") VALUES (".implode(',',$param).")";
			$_res = $pdo->prepare($sql);
			foreach ($detail as $row) {
				$_res->execute($row);
			}
			/*----------*/
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}

	/**
	 * 获取采购明细信息
	 */
	public function getDetail($pro_id,$select = array()){
		if(!empty($select)){
			$sql = "SELECT ".implode(',',$select)." FROM `stone_procure_details` WHERE `pro_id` = ".$pro_id;
		}else{
			$sql = "SELECT * FROM `stone_procure_details` WHERE `pro_id` = ".$pro_id;
		}
		$data = $this->db()->getAll($sql);
		return $data;
	}

	public function mkLog($do){
		$label = ['pro_id','check_id','check_user','check_time','check_info'];
		foreach ($do as $k=>$v) {
			if(!in_array($k,$label)){
				return false;
			}
		}
		$sql = "INSERT INTO `stone_check_log` (".implode(',',$label).") VALUES ('".$do['pro_id']."','".$do['check_id']."','".$do['check_user']."','".$do['check_time']."','".$do['check_info']."')";
		$res = $this->db()->query($sql);
		return ($res)?true:false;
	}

	public function checkPass($user_id,$pro_id){
		$res = $this->hasCheck($user_id,$pro_id);
		if($res){
			return 'hasCheck';
		}else{
			$label = ['pro_id','check_id','check_user','check_time','check_info'];
			$sql = "INSERT INTO `stone_check_log` (".implode(',',$label).",`is_check`) VALUES ('".$pro_id."','".$user_id."','".$_SESSION['realName']."','".date('Y-m-d H:i:s')."','审核通过','1')";
			$res = $this->db()->query($sql);
			return ($res)?true:false;
		}
	}

	public function checkOut($user_id,$pro_id,$refuse_cause){
		$res = $this->hasCheck($user_id,$pro_id);
		if($res){
			return 'hasCheck';
		}else{
			$label = ['pro_id','check_id','check_user','check_time','check_info'];
			$sql = "INSERT INTO `stone_check_log` (".implode(',',$label).",`is_check`) VALUES ('".$pro_id."','".$user_id."','".$_SESSION['realName']."','".date('Y-m-d H:i:s')."','[审核驳回]原因:".$refuse_cause."','1')";
			$res = $this->db()->query($sql);
			return ($res)?true:false;
		}
	}

	public function hasCheck($user_id,$pro_id){
		$sql = "SELECT count(*) FROM `stone_check_log` WHERE `pro_id` = '".$pro_id."' AND `check_id` = '".$user_id."' AND `is_check` = '1'";
		$res = $this->db()->getOne($sql);
		return ($res)?true:false;
	}

	public function getLogInfo($pro_id){
		$sql = "SELECT `check_user`,`check_time`,`check_info` FROM `stone_check_log` WHERE `pro_id` = ".$pro_id." ORDER BY `id` DESC";
		$data = $this->db()->getAll($sql);
		return $data;
	}

	public function clearCheck($pro_id){
		$sql = "UPDATE `stone_check_log` SET `is_check` = '0' WHERE `pro_id` = ".$pro_id;
		return $this->db()->query($sql);
	}

}

?>