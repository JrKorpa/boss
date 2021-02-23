<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseSalepolicyInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 10:54:58
 *   @update	:
 *  -------------------------------------------------
 */
class AppSalepolicyChannelModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_salepolicy_channel';
        $this->_dataObject = array("id"=>" ",
"policy_id"=>"销售策略id",
"channel"=>"渠道id",
"channel_level"=>"等级",
"create_time"=>"创建时间",
"create_user"=>"创建人",
"check_time"=>"审核时间",
"check_user"=>"审核",
"status"=>"记录状态 1保存,2申请,3审核通过,4未通过,5取消",
"is_delete"=>"取消 0否,1取消");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppSalepolicyChannelController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
        if(isset($where['id'])&&!empty($where['id'])){
            $sql.=" AND id like '".addslashes($where['id'])."%'";
        }
        if(isset($where['channel'])&&!empty($where['channel'])){
            $sql.=" AND channel like '".addslashes($where['channel'])."%'";
        }
        if(isset($where['policy_id'])&&!empty($where['policy_id'])){
            $sql.=" AND policy_id = '".$where['policy_id']."'";
        }
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 *	getSalepolicyChannelByPolicyId
	 *
	 *	@url BaseSalepolicyInfoController/show
	 */
    public function getSalepolicyChannelByPolicyId($policy_id)
    {
		$sql = "SELECT * FROM `".$this->table()."` WHERE policy_id = '{$policy_id}' ORDER BY id DESC";
		$data = $this->db()->getAll($sql);
		return $data;
    }
    
    /**
     *	根据渠道id 查出所有
     *
     *	@url BaseSalepolicyInfoController/show
     */
    public function getSalepolicyChannelByChannel($channel)
    {
    	$sql = "SELECT `policy_id` FROM `".$this->table()."` WHERE `channel` = '{$channel}' and `is_delete`<>2 GROUP BY `policy_id` ORDER BY `id` DESC";
    	$data = $this->db()->getAll($sql);
    	return $data;
    }

    public function saveAllC($data){
        if(!is_array($data['channel'])){
            return false;
        }
        $f = array_keys($data);
        $f = implode('`,`',$f);
        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            foreach($data['channel'] as $key=>$val){
                $sql = "select count(*) from ".$this->table()." where policy_id={$data['policy_id']} and channel={$val}";
                $ifExists = $this->db()->getOne($sql);
                if(!$ifExists){
                   $sql = "insert into ".$this->table()." (`$f`) value($data[policy_id],$val,$data[channel_level],'$data[create_time]','$data[create_user]',$data[status],$data[is_delete])";
                   $pdo->query($sql);
                }
            }

        }catch(Exception $e){//捕获异常
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                return false;
            }
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;

    }


    /**
     *	根据渠道id 查出所有
     *
     *	@url BaseSalepolicyInfoController/show
     */
    public function getSalepolicyByChannel($channel_id)
    {
        $sql = "SELECT bsi.`policy_id`,bsi.policy_name FROM `app_salepolicy_channel` as sc INNER JOIN base_salepolicy_info as bsi ON  bsi.policy_id = sc.policy_id WHERE sc.`channel` = '{$channel_id}' and sc.`is_delete`<>2 AND  bsi.is_delete=0 AND bsi.bsi_status=3 ORDER BY bsi.`policy_id` DESC";
        $data = $this->db()->getAll($sql);
        return $data;
    }

}
?>