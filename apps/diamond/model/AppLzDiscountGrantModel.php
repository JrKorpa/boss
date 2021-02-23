<?php
/**
 *  -------------------------------------------------
 *   @file		: AppLzDiscountGrantModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-19 18:32:50
 *   @update	:
 *  -------------------------------------------------
 */
class AppLzDiscountGrantModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_lz_discount_grant';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"自增id",
            "user_id"=>"用户授权[user 表id]",
            "type"=>"类型范围 0未设定 1.小于50分 2.小于1克拉 3.大于1克拉",
            "zhekou"=>"折扣",
            "mima"=>"密码",
            "create_user_id"=>"创建用户id",
            "create_user"=>"创建用户",
            "createtime"=>"生成时间",
            "endtime"=>"结束时间",
            "use_user_id"=>"使用人id",
            "use_user"=>"使用人",
            "usetime"=>"使用时间",
            "status"=>"是否可用 1可用 2使用 3过期 4作废");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppLzDiscountGrantController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//读的裸钻折扣管理的用户数据
		$sql = "SELECT *  FROM `".$this->table()."`";
		$str = '';
        if(!empty($where['user_id'])){
            $str .= "`user_id` = ".$where['user_id']." AND ";
        }
        
        if(!empty($where['type'])){
            $str .= "`type` = ".$where['type']." AND ";
        }

		if(!empty($where['createtime']))
		{
			$str .= "`createtime` >= '".$where['createtime']." 00:00:00' AND ";
		}

        if(!empty($where['endtime']))
        {
            $str .= "`endtime` <= '".$where['endtime']." 23:59:59' AND ";
        }

        if(!empty($where['start_time']))
        {
            $str .= "`usetime` >= '".$where['start_time']." 00:00:00' AND ";
        }

        if(!empty($where['end_time']))
        {
            $str .= "`usetime` <= '".$where['end_time']." 23:59:59' AND ";
        }

		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		
        $sql .= " ORDER BY `id` DESC";
        //echo $sql;die;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
    
    //产生一个随机密码
    public function createCode(){
        $code = rand(1000, 9999);
        return $code;
    }
    
    //验证随机密码是否重复
    public function checkCode($where){
        $sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
        if(!empty($where['user_id'])){
            $str .= " `user_id` =".$where['user_id']." AND ";
        }
        
        if(!empty($where['type'])){
            $str .= " `type` = ".$where['type']." AND ";
        }

        if(!empty($where['zhekou'])){
            $str .= " `zhekou` = ".$where['zhekou']." AND ";
        }
       
        if(!empty($where['status'])){
            $str .= " `status` = ".$where['status']." AND ";
        }
        
        if(!empty($where['time'])){
            $str .= " `createtime` <= '".$where['time']."' AND `endtime` >='".$where['time']."' AND ";
        }
        
        if(!empty($where['mima'])){
            $str .= " `mima` = '".$where['mima']."' AND ";
        }
        if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
        return $this->db()->getAll($sql);
    }
}

?>