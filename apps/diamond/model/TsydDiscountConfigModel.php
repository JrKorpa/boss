<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseLzDiscountConfigModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-19 15:37:36
 *   @update	:
 *  -------------------------------------------------
 */
class TsydDiscountConfigModel extends Model
{
    
    function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'tsyd_discount_config';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"自增ID",
"chanel_id"=>"渠道ID[sales_channels表id]",
"type"=>"类型范围 0未设定 1.小于50分 2.小于1克拉 3.大于1克拉 4.成品 5.空托",
"zhekou"=>"折扣",
"enabled"=>"是否可用 1可用 0停用");
		parent::__construct($id,$strConn);
	}

		/**
	 *	pageList，分页列表
	 *
	 *	@url BaseLzDiscountConfigController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT `id`,`channel_id`,`type`,`zhekou`,`enabled` FROM `".$this->table()."`";
		$str = '';

        if(!empty($where['channel_id'])){
            $str .= "`channel_id` =".$where['channel_id']." AND ";
        }
        if(!empty($where['type'])){
            $str .= "`type` = ".$where['type']." AND ";
        }
        if($where['enabled']!=''){
            $str .= "`enabled` = ".$where['enabled']." AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
        //echo $sql;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	//返回分类折扣
	public function getDiscountType(){
		$discounttype = array('1'=>'裸钻50分以下','2'=>'裸钻50分(含)-1.5克拉','3'=>'裸钻1.5克拉(含)以上','4'=>'成品','5'=>'空托');
		return $discounttype;

	}

	//折扣是否存在
	public function getDiscountExists($channel_id,$type){
		$sql = "SELECT count(*) FROM ".$this->table()." WHERE channel_id=".$channel_id." AND type=".$type;
		return $this->db()->getOne($sql);

	}

	/*
	* 通过ID获取折扣信息
	*
	*/
	public function getDiscountById($id){
		$sql ="SELECT * FROM ".$this->table()." WHERE id=".$id;
		return $this->db()->getRow($sql);

	}

	/*
	* 通过渠道ID获取折扣信息
	*
	*/
	public function getDiscountByChannelId($channel_id){
		$sql ="SELECT * FROM ".$this->table()." WHERE channel_id=".$channel_id;
		return $this->db()->getAll($sql);

	}

	//折扣是否存在,存在并返回结果
	public function getDiscountResult($channel_id,$type){
		$sql = "SELECT * FROM ".$this->table()." WHERE channel_id=".$channel_id." AND type=".$type;
		return $this->db()->getRow($sql);

	}


}

?>