<?php
/**
 *  -------------------------------------------------
 *   @file		: SalesChannelsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 15:25:16
 *   @update	:
 *  -------------------------------------------------
 */
class SalesChannelsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'sales_channels';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"channel_name"=>"渠道名称",
"channel_code"=>"渠道编码",
"channel_class"=>"1线上，2线下",
"channel_type"=>"1部门，2体验店，3公司",
"channel_own_id"=>"所属ID",
"channel_own"=>"渠道归属",
"addby_id"=>"创建人",
"addby_time"=>"创建时间",
"updateby_id"=>"更新人",
"update_time"=>"修改时间",
"channel_man"=>"渠道联系人",
"channel_email"=>"联系人邮箱",
"channel_phone"=>"联系人手机",
"is_deleted"=>"删除标识",
"qrcode"=>"二维码",
 'wholesale_id'=>'批发客户',
        );
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url SalesChannelsController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE is_deleted = ".$where['is_deleted'];
		if($where['channel_name'] != "")
		{
			$sql .= " AND channel_name like \"%".addslashes($where['channel_name'])."%\"";
		}
		if($where['channel_code'] != "")
		{
			$sql .= " AND channel_code like \"%".addslashes($where['channel_code'])."%\"";
		}
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 * getChannelOwns
	 */
	public function getChannelOwns($type){
		switch ($type) {
			case '1':
				$table = 'department';//部门表
				$name = 'name';
                $where='is_deleted';
				break;
			case '2':
				$table = 'shop_cfg';//体验店表
				$name = 'shop_name';
                $where='is_delete';
				break;
			case '3':
				$table = 'company';//公司表
				$name = 'company_name';
                $where='is_deleted';
				break;
			default:
				$type = false;
				break;
		}
		if($type){
			$sql = 'SELECT id,`'.$name.'` as label FROM `'.$table.'` WHERE `'.$where.'` =0 ORDER BY id DESC';
//             echo $sql;
            file_put_contents('e:/8.sql',$sql);
			$data = DB::cn(1)->getAll($sql);
			return $data;
		}else{
			return false;
		}
	}

    /*
     * 取渠道 channel_type
     */
    public function getSalesChannelsInfo($select="*",$where){
        $sql = "SELECT $select FROM `sales_channels` WHERE is_deleted=0 ";
        if(!empty($where['id'])){
           $sql.=" AND `id`=".$where['id'];
        }
        if(!empty($where['channel_class'])){
           $sql.=" AND `channel_class`=".$where['channel_class'];
        }
		 if(isset($where['is_deleted'])){
           $sql.=" AND `is_deleted`=".$where['is_deleted'];
        }
        if(!empty($where['channel_type'])){
           $sql.=" AND `channel_type`=".$where['channel_type'];
        }
        if(!empty($where['is_tsyd'])){
        	$sql.=" AND `is_tsyd`=".$where['is_tsyd'];
        }
        return $this->db()->getAll($sql);
    }
	
	/*
	 * 获取能在官网显示的体验
	*/
	public function getSalesChannelsInfofowebsite($select="a.*")
	{
		$sql = "select $select
			from `shop_cfg` as s
            LEFT JOIN `sales_channels` as c ON c.channel_own_id=s.id AND s.is_delete=0  
			WHERE c.is_deleted=0 AND ( ( c.channel_type = 2 AND s.official_webiste_show = 1 AND is_tsyd = 0) or c.id=1 )";
		return $this->db()->getAll($sql);
	}

    /*
     * 取有权限渠道
     */
    public function getQuDaoInfo($select="*",$where){

        if(empty($where['id'])){
            return false;
        }

        $sql = "SELECT $select FROM `sales_channels` WHERE 1";
        if(!empty($where['id'])){
           $sql.=" AND `id` IN ('".$where['id']."')";
        }

        return $this->db()->getAll($sql);
    }

    /**
     * 通过所给的渠道的id数组 返回所有的渠道
     *
     * @param $channerids
     * @return bool or arr
     */
    public function getSalesChannel($channerids){
        if(isset($channerids)&&!empty($channerids)){
            $channerids = implode(',',$channerids);
            $sql = "SELECT * FROM `".$this->table()."` WHERE `is_deleted`=0 AND `id` IN ($channerids)";
            return $this->db()->getAll($sql);
        }else{
            return false;
        }

    }


    /**
     *给予渠道id返回对应的实体信息
     * @param $channel_id
     * return array
     */
    public function getChannelOwnId($channel_id){
        $sql = "SELECT `channel_own_id`,`channel_type` FROM `".$this->table(). "` WHERE `id`=".$channel_id;
        $res = $this->db()->getRow($sql);
        switch ($res['channel_type']) {
            case '1':
                $table = 'department';//部门表
                $name = 'id,name';
                break;
            case '2':
                $table = 'shop_cfg';//体验店表
                $name = '`short_name`,`shop_address`,`country_id`,`province_id`,`city_id`,`regional_id`,`id`,`shop_name`,`shop_type`,`shop_phone`';
                break;
            case '3':
                $table = 'company';//公司表
                $name = 'id,company_name';
                break;
            default:
                return false;
        }

            $sql1 = "SELECT ".$name." FROM `".$table."` WHERE `id`=".$res['channel_own_id'];

      return $this->db()->getRow($sql1);

    }

    /**
     *给予渠道id返回对应的实体信息
     * @param $channel_id
     * return array
     */
    public function getChannelByOwnId($channel_id){
        $sql = "SELECT `channel_own_id`,`channel_type` FROM `".$this->table(). "` WHERE `id`=".$channel_id;
        $res = $this->db()->getRow($sql);
        
        $ret = array();
        if($res['channel_type'] == 2){
            $table = 'shop_cfg';//体验店表
            $name = '`short_name`,`shop_address`,`country_id`,`province_id`,`city_id`,`regional_id`,`id`,`shop_name`,`shop_type`,`shop_phone`';

            $sql1 = "SELECT ".$name." FROM `".$table."` WHERE `id`=".$res['channel_own_id'];
            $ret = $this->db()->getRow($sql1);
            if(!$ret){
                $list = array();
                $list['short_name']='';
                $list['shop_address']='';
                $list['country_id']=0;
                $list['province_id']=0;
                $list['city_id']=0;
                $list['regional_id']=0;
                $list['id']='';
                $list['shop_name']='';
                $list['shop_type']='';
                $list['shop_phone']='';
            return $list;  
            }else{
                return $ret;
            }
        }
        else{
            $list = array();
            $list['short_name']='';
            $list['shop_address']='';
            $list['country_id']=0;
            $list['province_id']=0;
            $list['city_id']=0;
            $list['regional_id']=0;
            $list['id']='';
            $list['shop_name']='';
            $list['shop_type']='';
            $list['shop_phone']='';
            return $list;
        }
    }

    public function getOwns($type,$own_id){
        $sql = "select `id`,`channel_own` from `".$this->table()."` where `channel_own_id`=$own_id and `channel_type`=$type";

        return $this->db()->getAll($sql);
    }

	public function hasName ($name)
	{
		$sql = "SELECT count(*) FROM `".$this->table()."` WHERE `channel_name`='{$name}'";
		if($this->pk())
		{
			$sql .=" AND id<>".$this->pk();
		}
		return $this->db()->getOne($sql);
	}



    /**
     * 通过渠道id获取实体名称
     * @param $id
     * @return bool
     */
    public function getChannelOwnNameById($id){
        if($id){
            $sql = "select `channel_own` from `".$this->table()."` where `id`=$id";
            return $this->db()->getOne($sql);
        }
        return false;

    }


    /**
     *给予渠道id返回渠道归属简称编码
     * @param $channel_id
     * return array
     */
    public function getChannelOwnCode($channel_id,$alias=0){
        $sql = "SELECT `channel_own_id`,`channel_type` FROM `".$this->table(). "` WHERE `id`=".$channel_id;
        $res = $this->db()->getRow($sql);
        switch ($res['channel_type']) {
            case '1':
                $table = 'department';//部门表
                $code = '`code`';
                break;
            case '2':
                $table = 'shop_cfg';//体验店表
                $code = '`short_name`';
                if($alias){
                    $code = "`short_name` as `code`";
                }
                break;
            case '3':
                $table = 'company';//公司表
                $code = '`company_sn`';
                if($alias){
                    $code = "`company_sn` as `code`";
                }
                break;
            default:
                return false;
                break;
        }
        $sql1 = "SELECT $code FROM `".$table."` WHERE `id`=".$res['channel_own_id'];

        return $this->db()->getOne($sql1);

    }

	/******
	取部门，体验店，公司渠道
	*******/
	public function getChannelByChannel_Name($channel_name)
	{
		$sql = "select * from `".$this->table()."` where `channel_type`=2 and `is_deleted`=0 and `channel_name` like '%".$channel_name."%'";
		return $this->db()->getAll($sql);
	}

	/******
	取部门，体验店，公司渠道
	*******/
	public function getChannelById($id)
	{
		$sql = "select * from `".$this->table()."` where `channel_type`=2 and `is_deleted`=0 and `id` not in($id)";
		return $this->db()->getAll($sql);
	}

	/******
	getNameByid
	*******/
	public function getNameByid($id)
	{
		$sql = "select channel_name from ".$this->table()." where id ={$id}";
		return $this->db()->getOne($sql);
	}

    /******
    //获取渠道对应公司名
    *******/
    public function getCompanyNameByid($id)
    {
        $sql = "select c.company_name from ".$this->table()." s left join company c on s.company_id=c.id  where s.id ={$id}";
        return $this->db()->getOne($sql);
    }
    /******
    //获取渠道对应公司信息
    *******/
    public function getCompanyByChannelid($id)
    {
        $sql = "select c.* from ".$this->table()." s left join company c on s.company_id=c.id  where s.id ={$id}";
        return $this->db()->getRow($sql);
    }
	/**
	* 取渠道的所有用户
	*/
	public function getUsers ()
	{
		$sql = "SELECT c.power,u.* FROM `user_channel` AS c,`user` AS u WHERE c.user_id=u.id AND c.`channel_id`='".$this->pk()."' AND u.is_deleted=0";
		return $this->db()->getAll($sql);
	}
    
    /**
     * 获取体验店是直营店的销售渠道
     * @param type $channel_own_ids
     * @return type
     */
    public function getSaleChannelList($channel_own_ids) {
        $sql = "SELECT `id`,`channel_name` FROM `{$this->table()}` WHERE `channel_own_id` IN ($channel_own_ids)";
        return $this->db()->getAll($sql);
    }

    public function getShopCid($id = ''){
        $sql = "select id,dp_leader_name,dp_people_name from sales_channels_person";
        if ($id != '') {
            $sql = $sql. " where id = {$id}";
        }
        return $this->db()->getAll($sql);

    }
	
	
	
	/*
	author : liulinyan
	date: 2015-08-12
	used: 根据类型，一级分类和二级分类找出所有的满足条件的渠道
	*/
	public function getallchannels($data)
	{
		if(empty($data) || !is_array($data))
		{
			return false;
		}
		$shoptype  = isset($data['shoptype']) ? $data['shoptype']: '';
		$onelev  = isset($data['onelev']) ? $data['onelev']: '';
		$twolev  = isset($data['twolev']) ? $data['twolev']: '';
		if($shoptype)
		{
			$sql = "select b.id,b.channel_name from sales_channels as b inner join shop_cfg as a on   a.id=b.channel_own_id where a.shop_type=$shoptype and  ";
		}else{
			$sql = " select id,channel_name from sales_channels where ";
		}
		if($onelev)
		{
			$sql .= " b.channel_class = $onelev and "; 
		}
		if($twolev)
		{	
			$sql .= " b.channel_type = $twolev and ";
		}
		$sql .= " b.is_deleted<1 ";
		$sql .= " ORDER BY b.id DESC";
		//$data = $this->db()->getPageList($sql,array(),1, 200,false);
        $data['data'] = $this->db()->getAll($sql);
		return $data;
	}

    public function getSaleChannelTyd(){
        $sql = "SELECT sc.id id,sc.channel_name shop_name FROM `sales_channels` sc inner join shop_cfg s on sc.channel_own_id = s.id and sc.channel_type =2 and s.is_delete=0";
        return $this->db()->getAll($sql);
    }

	public function a(){
		echo '1111111111111111111';
	}
    
    /*
     * 获取所有体验店的信息
     */
    public function getExpStore($channel_own){
    	$sql = "select count(*) from ".$this->table()." where channel_type=2 and channel_own_id = '".$channel_own."'";
    	return $this->db()->getOne($sql);
    }
    
    /*
     * 通过渠道id获取渠道所属
     */
    public function getChannelOwnIdById($id){
    	$sql = "select channel_own_id from ".$this->table()." where id='".$id."'";
    	return $this->db()->getOne($sql);
    	
    }
    public function getList($field="*",$where="1=1"){
        $sql = "select {$field} from ".$this->table()." where {$where}";
        return $this->db()->getAll($sql);
    }
    
    /*
     * 通过渠道id获取批发客户
     */
    public function getChannelIdById($id){
    	$sql = "select wholesale_id from ".$this->table()." where id='".$id."'";
    	return $this->db()->getRow($sql);
    	 
    }
    
    /*
    * 通过销售名称获取渠道ID
    *
    */
    public function getChannelIdByChannelName($channel_name){
        $sql = "select channel_id from ".$this->table()." where channel_name like '%".$channel_name."%'";
        return $this->db()->getOne($sql);
    }
    
     /*
    * 通过销售名称获取渠道ID
    *
    */
    public function getChannelNameByChannelId($channel_id){
        $sql = "select channel_name from ".$this->table()." where id=".$channel_id;
        return $this->db()->getOne($sql);
    }

    /*
    * 所有渠道
    *
    */
    public function getAllChannelInfo(){
        $sql = "select id,channel_name from ".$this->table();
        return $this->db()->getAll($sql);

    }

	
	//为新加的店面拷贝全国的sku商品信息
	public function copyskugoods($channelid,$copyid=1)
	{
		$sql = "insert into front.app_goodsprice_salepolicy
		(style_sn,style_id,channel_id,jiajialv,sta_value,create_time,update_time,is_delete) ";
		$sql .="select style_sn,style_id,$channelid,jiajialv,sta_value,create_time,update_time,is_delete 
from front.app_goodsprice_salepolicy where channel_id='{$copyid}' and is_delete=0 ";
		$this->db()->query($sql);
	}


}


?>