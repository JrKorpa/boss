<?php
/*
auto: liulinyan
date: 2015-09-21
file: PayClassModel.php
used: 淘宝用户清洗类
*/
class UserClassModel extends PdoModel
{
	
	function __construct()
	{
		parent::__construct();
	}
	function __distruct()
	{
		//
	}
	
	//根据手机号码查找
	public function getinfobymobile($tel)
	{
		$sql = "select member_id from front.base_member_info where member_phone = '".$tel."' ";
		$sql .= " order by member_id desc limit 1";
		$result = $this->mysqli->query($sql);
		$res = array();
		if($result)
		{
			$rows = $result->num_rows;
			
			if($rows > 0)
			{
				$res = $result->fetch_assoc();
			}
		}
		return $res;
	}
	
	//获取BDD订单的信息
	//获取所有淘宝订单没有填写用户id的信息
	function getorderinfo()
	{
		$sql ="select id,user_id,consignee,mobile,order_remark from app_order.base_order_info where ";
		$sql .=" user_id=0 and customer_source_id=544 and department_id=2 and referer='双11抓单' ";
		$result = $this->mysqli->query($sql);
		$res = array();
		if($result)
		{
			if($result->num_rows>0)
			{
				while($obj = $result->fetch_assoc())
				{
					array_push($res,$obj);
				}
			}
		}
		return $res;
	}
	
	
	//创建一个用户
	function adduser($data)
	{
		if(empty($data))
		{
			return false;
		}
		//$data = array_filter($data);
		$key = implode(',',array_keys($data));
		$value = implode("','",array_values($data));
		$sql = "insert into front.base_member_info($key) value('$value')";
		echo $sql.'\r\n';
		$this->mysqli->query($sql);
		return $this->mysqli->insert_id;
	}
	
	//更改淘宝订单中的user_id
	function updateuserid($id,$userid)
	{
		if(empty($id) || empty($userid)){
			return false;
		}
		$sql = "update app_order.base_order_info set user_id=$userid where id='".$id."'";
		echo $sql.'******************\r\n';
		$this->mysqli->query($sql);
	}
}
?>