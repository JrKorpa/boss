<?php
/**
 *  -------------------------------------------------
 *   @file		: AppWsdExchangeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-12-25 17:49:45
 *   @update	:
 *  -------------------------------------------------
 */
class AppWsdExchangeModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_wsd_exchange';
		$this->pk='wsd_id';
		$this->_prefix='';
        $this->_dataObject = array("wsd_id"=>" ",
"wsd_code"=>" ",
"wsd_name"=>" ",
"wsd_mobile"=>" ",
"wsd_user"=>" ",
"wsd_department"=>" ",
"wsd_department_name"=>" ",
"wsd_is_bespoke"=>" ",
"wsd_time"=>"兑换时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppWsdExchangeController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
		if(!empty($where['wsd_code']))
		{
			$str .= "`wsd_code` in(".$where['wsd_code'].") AND ";
		}
        if(!empty($where['wsd_name']))
        {
            $str .= "`wsd_name`='".$where['wsd_name']."' AND ";
        }
        if(!empty($where['wsd_user']))
        {
            $str .= "`wsd_user`='".$where['wsd_user']."' AND ";
        }
        if(!empty($where['wsd_is_bespoke']))
        {
            $str .= "`wsd_is_bespoke`='".$where['wsd_is_bespoke']."' AND ";
        }
        if(!empty($where['wsd_mobile']))
        {
            $str .= "`wsd_mobile`='".$where['wsd_mobile']."' AND ";
        }
        if(!empty($where['wsd_department']))
        {
            $str .= "`wsd_department` in(".$where['wsd_department'].") AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
        //echo $sql;die;
		$sql .= " ORDER BY `wsd_id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

    /**
     *  checkWsdCode，验证是否已经有兑换
     *
     *  @url AppWsdExchangeController/search
     */
    public function checkWsdCode($wsd_code)
    {
        # code...
        $sql = "SELECT * FROM `".$this->table()."` WHERE `wsd_code` = '$wsd_code'";
        return $this->db()->getRow($sql);
    }

    /**
     * 生成预约号
     */
    public function create_besp_sn(){
        return date('ym').str_pad(mt_rand(1,99999),5,'0',STR_PAD_LEFT);
    }

    /**
     * 查询预约号是否存在 
     */
    public function get_bespoke_by_besp_sn($besp_sn,$select='*'){
        $sql="select ".$select." from `front`.`app_bespoke_info` where `bespoke_sn` = '".$besp_sn."'";
        return $this->db()->getRow($sql);
    }

    /**
     * 推送预约列表
     */
    public function saveMemberInfo($memberInfo){
        # code...
        if($memberInfo){
            $tmp = '';
            foreach ( $memberInfo as $k => $v ){

                $tmp .= '`' . $k . '` = \'' . $v . '\',';
            }

            $tmp = rtrim($tmp,',');
            $sql = "INSERT INTO `front`.`base_member_info` SET {$tmp}";
            $this->db()->query($sql);
            return $this->db()->insertId();
        }
        return false;
    }

    /**
     * 推送预约列表
     */
    public function saveBespokeInfo($bespokeInfo){
        # code...
        if($bespokeInfo){
            $tmp = '';
            foreach ( $bespokeInfo as $k => $v ){

                $tmp .= '`' . $k . '` = \'' . $v . '\',';
            }

            $tmp = rtrim($tmp,',');
            $sql = "INSERT INTO `front`.`app_bespoke_info` SET {$tmp}";
            return $this->_db->query($sql);
        }
        return false;
    }

    /**
     * 推送预约列表
     */
    public function checkSearchInfo($where){
        # code...
        # 
        $sql = "select `wsd_code` from `".$this->table()."` where `".$where['finds']."` = '".$where['values']."'";

        return $this->db()->getOne($sql);
    }
}

?>