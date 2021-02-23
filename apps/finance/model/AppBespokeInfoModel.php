<?php
/**
 *  -------------------------------------------------
 *   @file		: AppbespokeinfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 16:51:42
 *   @update	:
 *  -------------------------------------------------
 */
class AppBespokeInfoModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_bespoke_info';
		$this->_prefix ='bespoke';
        $this->_dataObject = array(
"bespoke_id"=>"预约ID",
 "bespoke_sn"=>"预约号",
"department_id"=>"体验店ID",
"mem_id"=>"顾客id",
"customer_source_id"=>"客户来源",
"customer"=>"客户姓名",
"customer_mobile"=>"客户手机",
"customer_email"=>"客户email",
"customer_address"=>"客户地址",
"create_time"=>"预约时间",
"bespoke_inshop_time"=>"预约到店时间",
"real_inshop_time"=>"实际到店时间",
"make_order"=>"制单人",
"accecipt_man"=>"接待人",
"queue_status"=>"预约队列，0首先，1其次，2最后",
"salesstage"=>"销售阶段:[1,了解;2,对比;3,决定]",
"brandimage"=>"品牌印象[1,弱;2,中;3,强]",
"bespoke_status"=>"预约状态 1保存2已经审核3作废",
"re_status"=>"到店状态 1到店 2未到店",
"withuserdo"=>"回访状态 0未回访 1已回访",
"is_delete"=>"取消预约 0未取消 1已取消",
"remark"=>"预约备注");
		parent::__construct($id,$strConn);
	}
 
 
    /**
    * 更新预约信息
    * @param
    * @return json
    */
	public function updateBespokeDealStatus($bespoke_id, $data=array())
	{
		$s_time = microtime();
        $bespoke_id = trim($bespoke_id);
		if(empty($bespoke_id) || empty($data)){
			 return false;
		}
        $fields = '';
        foreach ($data as $k=>$v) {
            if (is_numeric($v)) {
                $fields .= $k.'='.$v.',';
            } else {
                $fields .= "$k='$v',"; // 字符窜加引号
            }
        }
        $fields = rtrim($fields, ',');

        //更新信息
		$sql = "UPDATE  front.`app_bespoke_info` SET $fields WHERE `bespoke_id`='{$bespoke_id}'";
		$row =$this->db()->query($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
	//	$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if($row==false){
		 return false;
		}else{
			return true;
		}
	}
    // 判断用户是否网销
    public function checkUserIsNetSaler($username) {
        $sql = "select * from cuteframe.sales_channels_person where concat(',',dp_is_netsale,',') like '%,{$username},%'";
        return $this->db()->getRow($sql);
    }

}

?>