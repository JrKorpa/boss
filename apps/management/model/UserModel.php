<?php
/**
 *  -------------------------------------------------
 *   @file		: UserModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-26 18:00:24
 *   @update	:
 *  -------------------------------------------------
 */
class UserModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'user';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"主键",
"account"=>"登录帐号",
"password"=>"登录密码",
"code"=>"员工编码",
"real_name"=>"姓名",
"is_on_work"=>"员工状态。1在职，0离职。离职后无法登录系统，但账户信息保留。",
"is_enabled"=>"账户状态。1启用，0停用。停用则无法登录系统。",
"gender"=>"性别",
"birthday"=>"生日",
"mobile"=>"手机",
"phone"=>"电话",
"qq"=>"QQ号码",
"email"=>"邮箱",
"address"=>"住址",
"join_date"=>"入职时间",
"user_type"=>"用户类型",
"up_pwd_date"=>"最后修改密码日期",
"uin"=>"微信账号",
"is_system"=>"系统内置",
"icd"=>"身份证号",
"is_warehouse_keeper"=>"库管",
"is_channel_keeper"=>"渠道操作员",
"is_deleted"=>"是否删除。",
"company_id"=>"当前所在公司",
"internship"=>"是否实习期"             
        );
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url UserController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT m.* FROM `".$this->table()."` AS m ";
		$str = '';
		if(isset($where['is_deleted']))
		{
			$str .="`m`.`is_deleted`='".$where['is_deleted']."' AND ";
		}
		if($where['user_type'])
		{
			$str .="`m`.`user_type`='".$where['user_type']."' AND ";
		}
		if(!empty($where['role_id']))
		{
			$str .="`m`.`role_id`='".$where['role_id']."' AND ";
		}
		if($where['is_on_work']==1)
		{
			$str .= "`m`.`is_on_work`='1' AND ";//离职
		}
		else if ($where['is_on_work']==0)
		{
			$str .= "`m`.`is_on_work`='0' AND ";//在职
		}
		if($where['code'] != "")
		{
			$str .= "`code`='".$where['code']."' AND ";
		}


		if($where['account'] != "")
		{
			$str .= "m.account like \"%".addslashes($where['account'])."%\" AND ";
		}


		if($where['real_name'] != "")
		{
			$str .= "`m`.`real_name` like \"%".addslashes($where['real_name'])."%\" AND ";
		}

		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}

		$sql .= " ORDER BY `m`.`id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	
	public function getRealId ($accecipt_man)
	{
		$sql = "SELECT id FROM `".$this->table()."` WHERE `real_name` like \"%".$accecipt_man."%\"";
		return $this->db()->getOne($sql);
	}
	
	public function hasAccount ($account)
	{
		$sql = "SELECT count(*) FROM `".$this->table()."` WHERE `account`='{$account}'";
		if($this->pk())
		{
			$sql .=" AND id<>".$this->pk();
		}
		return $this->db()->getOne($sql);
	}

	public function hasCode ($code)
	{
		$sql = "SELECT count(*) FROM `".$this->table()."` WHERE `code`='{$code}'";
		if($this->pk())
		{
			$sql .=" AND id<>".$this->pk();
		}
		return $this->db()->getOne($sql);
	}

	public function getByAccount ($account)
	{
		$sql = "SELECT group_concat(distinct `cs`.`channel_id`) as `qudao`,group_concat(distinct `o`.`dept_id`) as `bumen`,`u`.* FROM `user` as `u` LEFT JOIN `organization` as `o` ON `u`.`id`=`o`.`user_id` LEFT JOIN `user_channel` as `cs` ON `u`.`id`=`cs`.`user_id` WHERE `u`.`account`='{$account}' ";
		return $this->db()->getRow($sql);
	}

	public function getOrganization($user_id)
	{
		$sql = "SELECT `o`.`level`,`o`.`position`,`d`.`name` FROM `organization` AS `o` INNER JOIN `department` AS `d` ON `o`.`dept_id`=`d`.`id` WHERE `o`.`user_id`='".$user_id."' AND `d`.`is_deleted`='0' ";
		return $this->db()->getAll($sql);
	}

	public function getGroup($user_id)
	{
		$sql = "SELECT `g`.`name` from `group_user` AS `gu` INNER JOIN `group` AS `g` ON `gu`.`group_id` = `g`.`id` WHERE `gu`.`user_id`='".$user_id."' AND `g`.`is_deleted`='0'";
		return $this->db()->getAll($sql);
	}

	public function getRole($user_id){
		$sql = "SELECT `r`.`label` FROM `role_user` AS `ru` INNER JOIN `role` AS `r` ON `ru`.`role_id`=`r`.`id` WHERE `ru`.`user_id`='".$user_id."' AND `r`.`is_deleted`='0'";
		return $this->db()->getAll($sql);
	}

	public function getPermission ($user_id)
	{
		$data = array();
		//menu
		$sql1 = "SELECT `p`.`id`,'0' AS `parent_id`,`m`.`label`,'1' AS `type` FROM `user_menu_permission` AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` INNER JOIN `menu` AS `m` ON `m`.`id`=`p`.`resource_id` WHERE `u`.`user_id`='".$user_id."' AND `p`.`type`='1' AND `m`.`is_deleted`='0' AND `m`.`type`='1' ";
		//button
		$sql2 = "SELECT `p`.`id`,`u`.`parent_id`,`b`.`label`,'2' AS `type` FROM `user_button_permission` AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` INNER JOIN `button` AS `b` ON `b`.`id`=`p`.`resource_id` WHERE `u`.`user_id`='".$user_id."' AND `p`.`type`='2' AND `b`.`is_deleted`='0' ";
		//view button
		$sql3 = "SELECT `p`.`id`,`u`.`parent_id`,`b`.`label`,'3' AS `type` FROM `user_view_button_permission` AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` INNER JOIN `button` AS `b` ON `b`.`id`=`p`.`resource_id` WHERE `u`.`user_id`='".$user_id."' AND `p`.`type`='2' AND `b`.`is_deleted`='0' ";
		//operation
		$sql4 = "SELECT `p`.`id`,`u`.`parent_id`,`o`.`label`,'4' AS `type` FROM `user_operation_permission` AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` INNER JOIN `operation` AS `o` ON `o`.`id`=`p`.`resource_id` WHERE `u`.`user_id`='".$user_id."' AND `p`.`type`='3' AND `o`.`is_deleted`='0' ";
		//detail
		$sql5 = "SELECT `p`.`id`,`u`.`parent_id`,`c`.`label`,'5' AS `type` FROM `user_subdetail_permission` AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` INNER JOIN `control` AS `c` ON `c`.`id`=`p`.`resource_id` WHERE `u`.`user_id`='".$user_id."' AND `p`.`type`='4' AND `c`.`is_deleted`='0' ";
		//detai_button
		$sql6 = "SELECT `p`.`id`,`u`.`parent_id`,`b`.`label`,'6' AS `type` FROM `user_subdetail_button_permission` AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` INNER JOIN `button` AS `b` ON `b`.`id`=`p`.`resource_id` WHERE `u`.`user_id`='".$user_id."' AND `p`.`type`='2' AND `b`.`is_deleted`='0' ";
		//detai_operation
		$sql7 = "SELECT `p`.`id`,`u`.`parent_id`,`o`.`label`,'7' AS `type` FROM `user_subdetail_operation_permission` AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` INNER JOIN `operation` AS `o` ON `o`.`id`=`p`.`resource_id` WHERE `u`.`user_id`='".$user_id."' AND `p`.`type`='3' AND `o`.`is_deleted`='0' ";

		$sql = "SELECT (@i:=@i+1) as i,`t`.* FROM ((".$sql1.") UNION (".$sql2.") UNION (".$sql3.") UNION (".$sql4.") UNION (".$sql5.") UNION (".$sql6.") UNION (".$sql7.")) AS `t`,(select @i:=0) as it ORDER BY `t`.`type`,`t`.`id`";

		$data = $this->db()->getAll($sql);

		$keys = array_column($data,'i');
		$data = array_combine($keys,$data);
		$relation = array();
		$datas = array();
		foreach ($data as $key => $val )
		{
			$relation[$val['type']][$val['id']] = $key;
			if(in_array($val['type'],array(2,3,4,5)))
			{//主对象
				$data[$relation[1][$val['parent_id']]]['son'.$val['type']][] = &$data[$key];
			}
			else if (in_array($val['type'],array(6,7)))
			{
				$data[$relation[5][$val['parent_id']]]['son'.$val['type']][] = &$data[$key];
			}
			else
			{
				$datas[] = &$data[$key];
			}
		}

		return $datas;
	}

	/**
	 * 获取用户基本信息
	 * @author	: yangxiaotong
	 * @note	: 供应商模块[采购人，取货人]
	 */
	public function getUserInfo(){
		$sql = 'SELECT `id`,`account`,`code`,`real_name`,`icd`,`mobile` FROM `user` WHERE `is_deleted` = 0 AND `is_on_work` = 1';
		return DB::cn(1)->getAll($sql);
	}

	/**
	 * 获取用户基本信息
	 * @author	: tanghuzhen
	 * @note	: 销售顾问
	 */
	public function getUserList(){
		$sql = 'SELECT `id`,`account`,`code`,`real_name`,`icd`,`mobile` FROM `user` WHERE `is_deleted` = 0 AND `is_on_work` = 1 AND `is_enabled`=1';
		return DB::cn(1)->getAll($sql);
	}

	/**
	 * 获取用户所属部门
	 * @param $user_id
	 * @return array
	 * @author	: yangxiaotong
	 * @note	: 供应商模块[申请部门]
	 */
	public function getUserDept($user_id){
		$sql = 'SELECT `dept_id` FROM `organization` WHERE `user_id` = '.$user_id;
		$data = DB::cn(1)->getAll($sql);
		$dept = array();
		foreach ($data as $v) {
			$dept[] = $v['dept_id'];
		}
		return $dept;
	}


    /**
     * 退款系统接口，获取用户名
     * @param type $userId
     * @return type
     */
    public function getAccount($userId) {
        $sql = "select `account` from `{$this->table()}` where `id`=$userId";
        return $this->db()->getOne($sql);
    }

    /**
     * 获取用户ID
     * @param type $userName
     * @return type
     */
    public function getAccountId($username) {
        $sql = "select `id` from `{$this->table()}` where `account`='{$username}'";
        return $this->db()->getOne($sql);
    }

    /**
     * 根据用户名 获取所在公司
     * @param type $userName
     * @return type
     */
    public function getCompanyByUsername($username) {
        $sql = "select `company_id`,`internship`,`trun_date` from `{$this->table()}` where `account`='{$username}'";
        return $this->db()->getRow($sql);
    }

//	/**
//	 * 获取用户组成员
//	 */
//	public function getGroupUser($group_id)
//	{
//		$sql = "SELECT `id`,`user_id` FROM `group_user` WHERE `group_id` = ".$group_id;
//		$data = $this->db()->getAll($sql);
//		$users = array_column($data,'user_id','id');
//		return $users;
//	}

	/**
	 * 获取审核组人成员信息
	 * 用户ID 姓名 账户
	 */
	public function getGroupCheckUser($group_id){
		$sql = "SELECT g.`id`,g.`user_id`,u.`real_name`,u.`account` FROM `group_user` AS g LEFT JOIN `user` AS u ON g.`user_id` = u.`id` WHERE g.`group_id` = ".$group_id;
		$data = $this->db()->getAll($sql);
		return $data;
	}
        
        public function getChannel($id) 
        {
                $sql = "SELECT `m`.id,`m`.`power`,`c`.`channel_name` FROM `user_channel` AS `m` INNER JOIN `sales_channels` AS `c` ON `c`.`id`=`m`.`channel_id` where m.user_id='{$id}' ";
                return $this->db()->getAll($sql);
        }
        
        public function getHouse($id) 
        {
                $data = array();
                $sql = "SELECT id,house_id FROM `user_warehouse` where user_id='{$id}' ";
                $hs = $this->db()->getAll($sql);
                if($hs)
                {
                        $ids = array_column($hs,'house_id');
                        $jumpids = array_combine($ids,array_column($hs, 'id'));
                        $model = new ApiWarehouseModel();
                        $datas = $model->getList();
                        foreach ($datas as $value) {
                              if(in_array($value['id'],$ids)) 
                              {
                                      $value['rel_id'] = $jumpids[$value['id']];
                                      $data[] = $value;
                              }
                        }
                }
                return $data;         
        }

    public function getAutList($user_id='')
    {
        if($user_id == '') return false;

        $sql = "select DISTINCT A.app_name as '功能菜单', btn.btn_name as '权限', px.view_btn as '详情页权限' from (
        (
            SELECT
                concat(`a`.`label`, '/', `g`.`label`,'/', m.label) AS `app_name`,
                `m`.`application_id`,
                `m`.c_id,
                `m`.id as mid,
                `p`.`id` as pid
            FROM
                `user_menu_permission` AS `u`
            INNER JOIN `permission` AS `p` ON `u`.`permission_id` = `p`.`id`
            INNER JOIN `menu` AS `m` ON `p`.`resource_id` = `m`.`id` AND `p`.`is_deleted` = `m`.`is_deleted`
            INNER JOIN `menu_group` AS `g` ON `g`.`id` = `m`.`group_id` AND `g`.`is_deleted` = `m`.`is_deleted`
            INNER JOIN `application` AS `a` ON `a`.`id` = `m`.`application_id` AND `a`.`is_deleted` = `m`.`is_deleted`
            WHERE `u`.`user_id` = '$user_id' AND `m`.`is_deleted` = '0' AND `m`.`is_enabled` = '1' AND `a`.`is_enabled` = 1 AND `p`.`type` = '1'
        ) UNION ALL (
                SELECT
                    concat(`a`.`label`, '/', `g`.`label`,'/', m.label) AS `app_name`,
                    `m`.`application_id`,
                    `m`.c_id,
                    `m`.id as mid,
                  `p`.`id` as pid
                FROM
                    `user_extend_menu` AS `u`
                INNER JOIN `permission` AS `p` ON `u`.`permission_id` = `p`.`id`
                INNER JOIN `menu` AS `m` ON `p`.`resource_id` = `m`.`id` AND `p`.`is_deleted` = `m`.`is_deleted`
                INNER JOIN `menu_group` AS `g` ON `g`.`id` = `m`.`group_id` AND `g`.`is_deleted` = `m`.`is_deleted`
                INNER JOIN `application` AS `a` ON `a`.`id` = `m`.`application_id` AND `a`.`is_deleted` = `m`.`is_deleted`
                WHERE `u`.`user_id` = '$user_id' AND `m`.`is_deleted` = '0' AND `m`.`is_enabled` = '1' AND `a`.`is_enabled` = 1 AND `p`.`type` = '1'
        )) as A 
        left JOIN (
            SELECT u.parent_id, GROUP_CONCAT(REPLACE(p.`name`,'-按钮权限','') SEPARATOR ', ') as btn_name FROM (
                select DISTINCT g.* from (
                    SELECT parent_id, permission_id FROM `user_button_permission` WHERE `user_id`='$user_id'
                    UNION ALL (SELECT parent_id,permission_id FROM `user_subdetail_button_permission` WHERE `user_id`='$user_id') 
                    UNION ALL (SELECT parent_id,permission_id FROM `user_view_button_permission` WHERE `user_id`='$user_id') 
                    UNION ALL (SELECT parent_id,permission_id FROM `user_extend_list_button` WHERE `user_id`='$user_id' )
                    UNION ALL (SELECT parent_id,permission_id FROM `user_extend_view_button` WHERE `user_id`='$user_id' )
                    UNION ALL (SELECT parent_id,permission_id FROM `user_extend_subdetail_button` WHERE `user_id`='$user_id')
                ) as g
            ) AS u INNER JOIN `permission` AS p ON u.permission_id=p.id WHERE p.is_deleted='0'
            group by u.parent_id
        ) as btn on btn.parent_id = A.pid
        left join (
            SELECT GROUP_CONCAT(`b`.`label`) as view_btn, m.`c_id`, m.id as mid  FROM `button` AS `b` INNER JOIN `permission` AS `p` ON  `b`.`id`=`p`.`resource_id` AND `b`.`is_deleted`=`p`.`is_deleted` 
            inner join menu m on m.`c_id`=`b`.`c_id` AND `m`.`is_deleted`='0' AND `m`.`is_enabled`='1'
            inner JOIN (
                SELECT distinct `permission_id`, `parent_id` FROM `user_view_button_permission` WHERE `user_id`='$user_id'
            ) AS `uop` ON `p`.`id`=`uop`.`permission_id`
            where `p`.`type`=2 AND b.type='2' AND `b`.`is_deleted`='0'
            group by m.`c_id`, m.id
        ) as px on px.mid = A.mid and A.c_id = px.c_id
        order by A.application_id";
        $data = $this->db()->getAll($sql);
        return $data;
    }

    //获取各个店业绩排名冠军 个人新增-个人转退 （不含婚博会）
    public function get_sale_goods_amount_top($date_start,$date_end,$sys){
        if($sys=='boss'){
            $sql="
                select t2.* FROM
				(

				    select  t.department_id,max(goods_amount) as goods_amount from (
				    select o.department_id,s.channel_name,o.create_user,
				      sum(a.goods_amount-a.favorable_price)
				        -
				        ifnull((SELECT
				             sum(if(rg.return_by=1,if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price),rg.real_return_amount)) as return_amount
				        FROM
				            app_order.base_order_info as o2
				            left join app_order.app_return_goods rg on rg.order_sn = o2.order_sn
				            left join cuteframe.user u on rg.apply_user_id=u.id 
				            left join app_order.app_return_check rc on rc.return_id = rg.return_id             
				            left join app_order.app_order_details d on d.id = rg.order_goods_id          
				        WHERE
				            rc.deparment_finance_status=1 
				            and rg.return_by in (1,2) and o2.is_zp=0 and o2.referer<>'婚博会' 
				            and rc.deparment_finance_time>='{$date_start} 00:00:00'
				            and rc.deparment_finance_time<='{$date_end} 23:59:59'
				            and u.account=o.create_user),0)    
				        +      
				        ifnull((select 
				              sum(rg3.real_return_amount)
				        FROM
				            app_order.base_order_info as o3
				            left join app_order.app_return_goods rg3 on rg3.order_sn = o3.order_sn
				            left join cuteframe.user u3 on rg3.apply_user_id=u3.id 
				            left join app_order.app_return_check rc3 on rc3.return_id = rg3.return_id             
				            left join app_order.app_order_details d3 on d3.id = rg3.order_goods_id          
				        WHERE
				            rc3.deparment_finance_status=1 
				            and rg3.return_by =2 and o3.is_zp=0 and o3.referer<>'婚博会' 
				            
				            and rg3.order_goods_id in (
				               select DISTINCT rg4.order_goods_id
				               FROM
				               app_order.base_order_info as o4
				               left join app_order.app_return_goods rg4 on rg4.order_sn = o4.order_sn
				               left join cuteframe.user u4 on rg4.apply_user_id=u4.id 
				               left join app_order.app_return_check rc4 on rc4.return_id = rg4.return_id             
				               left join app_order.app_order_details d4 on d4.id = rg4.order_goods_id          
				               WHERE
				               rc4.deparment_finance_status=1 
				               and rg4.return_by =1 and o4.is_zp=0 and o4.referer<>'婚博会' 
				               and rc4.deparment_finance_time>='{$date_start} 00:00:00'
				               and rc4.deparment_finance_time<='{$date_end} 23:59:59'
				               and u4.account=o.create_user              
				            )
				            and u3.account=o.create_user
				            ),0)             
				            as goods_amount
				 
					from app_order.base_order_info o left join cuteframe.sales_channels s on o.department_id=s.id,app_order.app_order_account a where o.id=a.order_id 
					and o.is_zp=0
					and o.department_id in (4,42,47,56,68)
					and o.referer<>'婚博会'
					and o.pay_date>='{$date_start} 00:00:00' 
					and o.pay_date<='{$date_end} 23:59:59'
					group by s.channel_name,o.create_user) as t group by t.department_id

				) as t1

				left join (

 				    select o.department_id,s.channel_name,o.create_user,
				      sum(a.goods_amount-a.favorable_price)
				        -
				        ifnull((SELECT
				             sum(if(rg.return_by=1,if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price),rg.real_return_amount)) as return_amount
				        FROM
				            app_order.base_order_info as o2
				            left join app_order.app_return_goods rg on rg.order_sn = o2.order_sn
				            left join cuteframe.user u on rg.apply_user_id=u.id 
				            left join app_order.app_return_check rc on rc.return_id = rg.return_id             
				            left join app_order.app_order_details d on d.id = rg.order_goods_id          
				        WHERE
				            rc.deparment_finance_status=1 
				            and rg.return_by in (1,2) and o2.is_zp=0 and o2.referer<>'婚博会' 
				            and rc.deparment_finance_time>='{$date_start} 00:00:00'
				            and rc.deparment_finance_time<='{$date_end} 23:59:59'
				            and u.account=o.create_user),0)    
				        +      
				        ifnull((select 
				              sum(rg3.real_return_amount)
				        FROM
				            app_order.base_order_info as o3
				            left join app_order.app_return_goods rg3 on rg3.order_sn = o3.order_sn
				            left join cuteframe.user u3 on rg3.apply_user_id=u3.id 
				            left join app_order.app_return_check rc3 on rc3.return_id = rg3.return_id             
				            left join app_order.app_order_details d3 on d3.id = rg3.order_goods_id          
				        WHERE
				            rc3.deparment_finance_status=1 
				            and rg3.return_by =2 and o3.is_zp=0 and o3.referer<>'婚博会' 
				            
				            and rg3.order_goods_id in (
				               select DISTINCT rg4.order_goods_id
				               FROM
				               app_order.base_order_info as o4
				               left join app_order.app_return_goods rg4 on rg4.order_sn = o4.order_sn
				               left join cuteframe.user u4 on rg4.apply_user_id=u4.id 
				               left join app_order.app_return_check rc4 on rc4.return_id = rg4.return_id             
				               left join app_order.app_order_details d4 on d4.id = rg4.order_goods_id          
				               WHERE
				               rc4.deparment_finance_status=1 
				               and rg4.return_by =1 and o4.is_zp=0 and o4.referer<>'婚博会' 
				               and rc4.deparment_finance_time>='{$date_start} 00:00:00'
				               and rc4.deparment_finance_time<='{$date_end} 23:59:59'
				               and u4.account=o.create_user              
				            )
				            and u3.account=o.create_user
				            ),0)             
				            as goods_amount
				 
					from app_order.base_order_info o left join cuteframe.sales_channels s on o.department_id=s.id,app_order.app_order_account a where o.id=a.order_id 
					and o.is_zp=0
					and o.department_id in (4,42,47,56,68)
					and o.referer<>'婚博会'
					and o.pay_date>='{$date_start} 00:00:00' 
					and o.pay_date<='{$date_end} 23:59:59'
					group by s.channel_name,o.create_user
				)as t2 on t1.department_id=t2.department_id and t1.goods_amount=t2.goods_amount ";
        
        }

        if($sys=='zhanting'){
            $sql="
				select t2.* FROM
				(

				    select  t.department_id,max(goods_amount) as goods_amount from (
				    select o.department_id,s.channel_name,o.create_user,
				      sum(a.goods_amount-a.favorable_price)
				        -
				        ifnull((SELECT
				             sum(if(rg.return_by=1,if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price),rg.real_return_amount)) as return_amount
				        FROM
				            app_order.base_order_info as o2
				            left join app_order.app_return_goods rg on rg.order_sn = o2.order_sn
				            left join cuteframe.user u on rg.apply_user_id=u.id 
				            left join app_order.app_return_check rc on rc.return_id = rg.return_id             
				            left join app_order.app_order_details d on d.id = rg.order_goods_id          
				        WHERE
				            rc.deparment_finance_status=1 
				            and rg.return_by in (1,2) and o2.is_zp=0 and o2.referer<>'婚博会' 
				            and rc.deparment_finance_time>='{$date_start} 00:00:00'
				            and rc.deparment_finance_time<='{$date_end} 23:59:59'
				            and u.account=o.create_user),0)    
				        +      
				        ifnull((select 
				              sum(rg3.real_return_amount)
				        FROM
				            app_order.base_order_info as o3
				            left join app_order.app_return_goods rg3 on rg3.order_sn = o3.order_sn
				            left join cuteframe.user u3 on rg3.apply_user_id=u3.id 
				            left join app_order.app_return_check rc3 on rc3.return_id = rg3.return_id             
				            left join app_order.app_order_details d3 on d3.id = rg3.order_goods_id          
				        WHERE
				            rc3.deparment_finance_status=1 
				            and rg3.return_by =2 and o3.is_zp=0 and o3.referer<>'婚博会' 
				            
				            and rg3.order_goods_id in (
				               select DISTINCT rg4.order_goods_id
				               FROM
				               app_order.base_order_info as o4
				               left join app_order.app_return_goods rg4 on rg4.order_sn = o4.order_sn
				               left join cuteframe.user u4 on rg4.apply_user_id=u4.id 
				               left join app_order.app_return_check rc4 on rc4.return_id = rg4.return_id             
				               left join app_order.app_order_details d4 on d4.id = rg4.order_goods_id          
				               WHERE
				               rc4.deparment_finance_status=1 
				               and rg4.return_by =1 and o4.is_zp=0 and o4.referer<>'婚博会' 
				               and rc4.deparment_finance_time>='{$date_start} 00:00:00'
				               and rc4.deparment_finance_time<='{$date_end} 23:59:59'
				               and u4.account=o.create_user              
				            )
				            and u3.account=o.create_user
				            ),0)             
				            as goods_amount
				 
					from app_order.base_order_info o left join cuteframe.sales_channels s on o.department_id=s.id left join cuteframe.company c on s.company_id=c.id,app_order.app_order_account a where o.id=a.order_id 
					and o.is_zp=0
					and c.company_type=2
					and o.referer<>'婚博会'
					and o.pay_date>='{$date_start} 00:00:00' 
					and o.pay_date<='{$date_end} 23:59:59'
					group by s.channel_name,o.create_user) as t group by t.department_id

				) as t1

				left join (

 				    select o.department_id,s.channel_name,o.create_user,
				      sum(a.goods_amount-a.favorable_price)
				        -
				        ifnull((SELECT
				             sum(if(rg.return_by=1,if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price),rg.real_return_amount)) as return_amount
				        FROM
				            app_order.base_order_info as o2
				            left join app_order.app_return_goods rg on rg.order_sn = o2.order_sn
				            left join cuteframe.user u on rg.apply_user_id=u.id 
				            left join app_order.app_return_check rc on rc.return_id = rg.return_id             
				            left join app_order.app_order_details d on d.id = rg.order_goods_id          
				        WHERE
				            rc.deparment_finance_status=1 
				            and rg.return_by in (1,2) and o2.is_zp=0 and o2.referer<>'婚博会' 
				            and rc.deparment_finance_time>='{$date_start} 00:00:00'
				            and rc.deparment_finance_time<='{$date_end} 23:59:59'
				            and u.account=o.create_user),0)    
				        +      
				        ifnull((select 
				              sum(rg3.real_return_amount)
				        FROM
				            app_order.base_order_info as o3
				            left join app_order.app_return_goods rg3 on rg3.order_sn = o3.order_sn
				            left join cuteframe.user u3 on rg3.apply_user_id=u3.id 
				            left join app_order.app_return_check rc3 on rc3.return_id = rg3.return_id             
				            left join app_order.app_order_details d3 on d3.id = rg3.order_goods_id          
				        WHERE
				            rc3.deparment_finance_status=1 
				            and rg3.return_by =2 and o3.is_zp=0 and o3.referer<>'婚博会' 
				            
				            and rg3.order_goods_id in (
				               select DISTINCT rg4.order_goods_id
				               FROM
				               app_order.base_order_info as o4
				               left join app_order.app_return_goods rg4 on rg4.order_sn = o4.order_sn
				               left join cuteframe.user u4 on rg4.apply_user_id=u4.id 
				               left join app_order.app_return_check rc4 on rc4.return_id = rg4.return_id             
				               left join app_order.app_order_details d4 on d4.id = rg4.order_goods_id          
				               WHERE
				               rc4.deparment_finance_status=1 
				               and rg4.return_by =1 and o4.is_zp=0 and o4.referer<>'婚博会' 
				               and rc4.deparment_finance_time>='{$date_start} 00:00:00'
				               and rc4.deparment_finance_time<='{$date_end} 23:59:59'
				               and u4.account=o.create_user              
				            )
				            and u3.account=o.create_user
				            ),0)             
				            as goods_amount
				 
					from app_order.base_order_info o left join cuteframe.sales_channels s on o.department_id=s.id left join cuteframe.company c on s.company_id=c.id,app_order.app_order_account a where o.id=a.order_id 
					and o.is_zp=0
					and c.company_type=2
					and o.referer<>'婚博会'
					and o.pay_date>='{$date_start} 00:00:00' 
					and o.pay_date<='{$date_end} 23:59:59'
					group by s.channel_name,o.create_user
				)as t2 on t1.department_id=t2.department_id and t1.goods_amount=t2.goods_amount ";
        }

        return $this->db()->getAll($sql);
    }


    //获取每个销售顾问天生一对销售数量
    public function get_sale_goods_tsyd($date_start,$date_end,$sys){
    	if($sys=='boss'){
            $sql="select t1.*,t2.return_tsyd_num,t1.tsyd_num+ifnull(t2.return_tsyd_num,0) as allnum from 
					(
					select o.department_id,s.channel_name,o.create_user,
					sum(
					if(if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)='HRD-D' AND d.cart>0 and d.goods_type not in ('lz','zp'),1,0) 
					+
					if((if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie) is null or if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)<>'HRD-D') and t.style_sn is not null,1,0) 
					+
					if(d.goods_sn in (select style_sn from front.base_style_info where xilie like '%,8,%') and (d.cart<=0 or d.cart='' or d.cart is null) and t.style_sn is null,1,0)
					) as tsyd_num
					from app_order.base_order_info o left join cuteframe.sales_channels s on o.department_id=s.id
					left join cuteframe.company c on s.company_id=c.id,app_order.app_order_details d 
					left join front.app_tsyd_special t on d.goods_sn=t.style_sn
					left join warehouse_shipping.warehouse_goods g on d.goods_id=g.goods_id
					 where o.id=d.order_id and o.pay_date>='{$date_start} 00:00:00' and  o.pay_date<='{$date_end} 23:59:59'
					and c.company_type=1
					and o.referer<>'婚博会'

					and o.is_zp=0
					and o.order_status=2 
					AND o.order_pay_status IN ('2','3','4') 
					AND o.is_delete=0
					and 
					(
					if(if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)='HRD-D' AND d.cart>0 and d.goods_type not in ('lz','zp'),1,0) 
					 +
					if((if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie) is null or if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)<>'HRD-D') and t.style_sn is not null,1,0) 
					+
					if(d.goods_sn in (select style_sn from front.base_style_info where xilie like '%,8,%') and (d.cart<=0 or d.cart='' or d.cart is null) and t.style_sn is null,1,0)
					) >0
					group by  o.department_id,o.create_user
			) as t1
					 left join 
					(
							SELECT
					             u.account,sum(-1) as return_tsyd_num 
					        FROM
					            app_order.base_order_info as o
					            left join app_order.app_return_goods rg on rg.order_sn = o.order_sn
					            left join cuteframe.user u on rg.apply_user_id=u.id 
					            left join app_order.app_return_check rc on rc.return_id = rg.return_id             
					            left join app_order.app_order_details d on d.id = rg.order_goods_id          
					            left join warehouse_shipping.warehouse_goods g on d.goods_id=g.goods_id
					            left join front.app_tsyd_special t on d.goods_sn=t.style_sn
					        WHERE
					            rg.return_by =1 and o.is_zp=0 and o.referer<>'婚博会' 
					            and rc.deparment_finance_time>='{$date_start} 00:00:00'
					            and rc.deparment_finance_time<='{$date_end} 23:59:59'
					and 
					(
					if(if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)='HRD-D' AND d.cart>0 and d.goods_type not in ('lz','zp'),1,0) 
					 +
					if((if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie) is null or if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)<>'HRD-D') and t.style_sn is not null,1,0) 
					+
					if(d.goods_sn in (select style_sn from front.base_style_info where xilie like '%,8,%') and (d.cart<=0 or d.cart='' or d.cart is null) and t.style_sn is null,1,0)
					) >0
					group by u.account
				) as t2 
					on t1.create_user=t2.account
					order by t1.tsyd_num+ifnull(t2.return_tsyd_num,0) desc  limit 200";
		}

		if($sys=='zhanting'){
            $sql="select t1.*,t2.return_tsyd_num,t1.tsyd_num+ifnull(t2.return_tsyd_num,0) as allnum from 
					(
					select o.department_id,s.channel_name,o.create_user,
					sum(
					if(if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)='HRD-D' AND d.cart>0 and d.goods_type not in ('lz','zp'),1,0) 
					+
					if((if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie) is null or if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)<>'HRD-D') and t.style_sn is not null,1,0) 
					+
					if(d.goods_sn in (select style_sn from front.base_style_info where xilie like '%,8,%') and (d.cart<=0 or d.cart='' or d.cart is null) and t.style_sn is null,1,0)
					) as tsyd_num
					from app_order.base_order_info o left join cuteframe.sales_channels s on o.department_id=s.id
					left join cuteframe.company c on s.company_id=c.id,app_order.app_order_details d 
					left join front.app_tsyd_special t on d.goods_sn=t.style_sn
					left join warehouse_shipping.warehouse_goods g on d.goods_id=g.goods_id
					 where o.id=d.order_id and o.pay_date>='{$date_start} 00:00:00' and  o.pay_date<='{$date_end} 23:59:59'
					and c.company_type=2
					and o.referer<>'婚博会'

					and o.is_zp=0
					and o.order_status=2 
					AND o.order_pay_status IN ('2','3','4') 
					AND o.is_delete=0
					and 
					(
					if(if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)='HRD-D' AND d.cart>0 and d.goods_type not in ('lz','zp'),1,0) 
					 +
					if((if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie) is null or if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)<>'HRD-D') and t.style_sn is not null,1,0) 
					+
					if(d.goods_sn in (select style_sn from front.base_style_info where xilie like '%,8,%') and (d.cart<=0 or d.cart='' or d.cart is null) and t.style_sn is null,1,0)
					) >0
					group by  o.department_id,o.create_user
			) as t1
					 left join 
					(
							SELECT
					             u.account,sum(-1) as return_tsyd_num 
					        FROM
					            app_order.base_order_info as o
					            left join app_order.app_return_goods rg on rg.order_sn = o.order_sn
					            left join cuteframe.user u on rg.apply_user_id=u.id 
					            left join app_order.app_return_check rc on rc.return_id = rg.return_id             
					            left join app_order.app_order_details d on d.id = rg.order_goods_id          
					            left join warehouse_shipping.warehouse_goods g on d.goods_id=g.goods_id
					            left join front.app_tsyd_special t on d.goods_sn=t.style_sn
					        WHERE
					            rg.return_by =1 and o.is_zp=0 and o.referer<>'婚博会' 
					            and rc.deparment_finance_time>='{$date_start} 00:00:00'
					            and rc.deparment_finance_time<='{$date_end} 23:59:59'
					and 
					(
					if(if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)='HRD-D' AND d.cart>0 and d.goods_type not in ('lz','zp'),1,0) 
					 +
					if((if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie) is null or if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)<>'HRD-D') and t.style_sn is not null,1,0) 
					+
					if(d.goods_sn in (select style_sn from front.base_style_info where xilie like '%,8,%') and (d.cart<=0 or d.cart='' or d.cart is null) and t.style_sn is null,1,0)
					) >0
					group by u.account
				) as t2 
					on t1.create_user=t2.account
					order by t1.tsyd_num+ifnull(t2.return_tsyd_num,0) desc  limit 200";
		}
        //echo $sql;
		return $this->db()->getAll($sql);
    }

    //获取每个销售顾问星耀销售数量
    public function get_sale_goods_xy($date_start,$date_end,$sys){
    	if($sys=='boss'){
            $sql="select t1.*,t2.return_tsyd_num,t1.tsyd_num+ifnull(t2.return_tsyd_num,0) as allnum from 
					(
					select o.department_id,s.channel_name,o.create_user,
					sum(
					if(if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)='HRD-S' AND d.cart>0 and d.goods_type='lz',1,0)
					) as tsyd_num
					from app_order.base_order_info o left join cuteframe.sales_channels s on o.department_id=s.id
					left join cuteframe.company c on s.company_id=c.id,app_order.app_order_details d 
					left join warehouse_shipping.warehouse_goods g on d.goods_id=g.goods_id
					 where o.id=d.order_id and o.pay_date>='{$date_start} 00:00:00' and  o.pay_date<='{$date_end} 23:59:59'
					and c.company_type=1
					and o.referer<>'婚博会'

					and o.is_zp=0
					and o.order_status=2 
					AND o.order_pay_status IN ('2','3','4') 
					AND o.is_delete=0
					and 
					(
						if(if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)='HRD-S' AND d.cart>0 and d.goods_type='lz',1,0)
					) >0
					group by  o.department_id,o.create_user
			) as t1
					 left join 
					(
							SELECT
					             u.account,sum(-1) as return_tsyd_num 
					        FROM
					            app_order.base_order_info as o
					            left join app_order.app_return_goods rg on rg.order_sn = o.order_sn
					            left join cuteframe.user u on rg.apply_user_id=u.id 
					            left join app_order.app_return_check rc on rc.return_id = rg.return_id             
					            left join app_order.app_order_details d on d.id = rg.order_goods_id          
					            left join warehouse_shipping.warehouse_goods g on d.goods_id=g.goods_id
					        WHERE
					            rg.return_by =1 and o.is_zp=0 and o.referer<>'婚博会' 
					            and rc.deparment_finance_time>='{$date_start} 00:00:00'
					            and rc.deparment_finance_time<='{$date_end} 23:59:59'
							and 
							(
			                if(if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)='HRD-S' AND d.cart>0 and d.goods_type='lz',1,0)				
							) >0
					       group by u.account
				) as t2 
					on t1.create_user=t2.account
					order by t1.tsyd_num+ifnull(t2.return_tsyd_num,0) desc  limit 200";
		}

		if($sys=='zhanting'){
            $sql="select t1.*,t2.return_tsyd_num,t1.tsyd_num+ifnull(t2.return_tsyd_num,0) as allnum from 
					(
					select o.department_id,s.channel_name,o.create_user,
					sum(
					if(if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)='HRD-S' AND d.cart>0 and d.goods_type='lz',1,0)
					) as tsyd_num
					from app_order.base_order_info o left join cuteframe.sales_channels s on o.department_id=s.id
					left join cuteframe.company c on s.company_id=c.id,app_order.app_order_details d 
					left join warehouse_shipping.warehouse_goods g on d.goods_id=g.goods_id
					 where o.id=d.order_id and o.pay_date>='{$date_start} 00:00:00' and  o.pay_date<='{$date_end} 23:59:59'
					and c.company_type=2
					and o.referer<>'婚博会'

					and o.is_zp=0
					and o.order_status=2 
					AND o.order_pay_status IN ('2','3','4') 
					AND o.is_delete=0
					and 
					(
						if(if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)='HRD-S' AND d.cart>0 and d.goods_type='lz',1,0)
					) >0
					group by  o.department_id,o.create_user
			) as t1
					 left join 
					(
							SELECT
					             u.account,sum(-1) as return_tsyd_num 
					        FROM
					            app_order.base_order_info as o
					            left join app_order.app_return_goods rg on rg.order_sn = o.order_sn
					            left join cuteframe.user u on rg.apply_user_id=u.id 
					            left join app_order.app_return_check rc on rc.return_id = rg.return_id             
					            left join app_order.app_order_details d on d.id = rg.order_goods_id          
					            left join warehouse_shipping.warehouse_goods g on d.goods_id=g.goods_id
					        WHERE
					            rg.return_by =1 and o.is_zp=0 and o.referer<>'婚博会' 
					            and rc.deparment_finance_time>='{$date_start} 00:00:00'
					            and rc.deparment_finance_time<='{$date_end} 23:59:59'
							and 
							(
								if(if(LENGTH(d.cert) >0,d.cert,g.zhengshuleibie)='HRD-S' AND d.cart>0 and d.goods_type='lz',1,0)
							) >0
							group by u.account
				) as t2 
					on t1.create_user=t2.account
					order by t1.tsyd_num+ifnull(t2.return_tsyd_num,0) desc  limit 200";
		}

		return $this->db()->getAll($sql);
    }    

    //获取每个销售顾问香榭巴黎销售数量
    public function get_sale_goods_xxbl($date_start,$date_end,$sys){
    	if($sys=='boss'){
            $sql="
				select t1.*,t2.return_tsyd_num,t1.tsyd_num+ifnull(t2.return_tsyd_num,0) as allnum from 
				(
					select o.order_sn,o.department_id,s.channel_name,o.create_user,
					sum(
					if(s.style_sn is not null,1,0)
					) as tsyd_num 
					from app_order.base_order_info o left join cuteframe.sales_channels s on o.department_id=s.id 
					left join cuteframe.company c on s.company_id=c.id,app_order.app_order_details d 
					left join front.base_style_info s on d.goods_sn=s.style_sn and s.xilie like '%,24,%'
					 where o.id=d.order_id and o.pay_date>='{$date_start} 00:00:00' and  o.pay_date<='{$date_end} 23:59:59'
					and o.referer<>'婚博会'
					and o.is_zp=0
					and o.order_status=2 
					AND o.order_pay_status IN ('2','3','4') 
					AND o.is_delete=0
					and c.company_type=1
					and o.department_id in (4,42,47,56,68)
					and 
					(
					if(s.style_sn is not null,1,0)
					) >0
					group by  o.department_id,o.create_user
				) as t1
				 left join 
				(
						SELECT
				             u.account,sum(-1) as return_tsyd_num 
				        FROM
				            app_order.base_order_info as o
				            left join app_order.app_return_goods rg on rg.order_sn = o.order_sn
				            left join cuteframe.user u on rg.apply_user_id=u.id 
				            left join app_order.app_return_check rc on rc.return_id = rg.return_id             
				            left join app_order.app_order_details d on d.id = rg.order_goods_id              
				            left join front.base_style_info s on d.goods_sn=s.style_sn and s.xilie like '%,24,%'
				        WHERE
						    rc.deparment_finance_status=1  
							and rg.return_by =1 and o.is_zp=0 and o.referer<>'婚博会' 
				            and rc.deparment_finance_time>='{$date_start} 00:00:00'
				            and rc.deparment_finance_time<='{$date_end} 23:59:59'
						and 
						(
						if(s.style_sn is not null,1,0)
						) >0
						group by u.account
				) as t2 

				on t1.create_user=t2.account
				order by t1.tsyd_num+ifnull(t2.return_tsyd_num,0) desc  limit 200";
		}

		if($sys=='zhanting'){
            $sql="
				select t1.*,t2.return_tsyd_num,t1.tsyd_num+ifnull(t2.return_tsyd_num,0) as allnum from 
				(
					select o.order_sn,o.department_id,s.channel_name,o.create_user,
					sum(
					if(s.style_sn is not null,1,0)
					) as tsyd_num 
					from app_order.base_order_info o left join cuteframe.sales_channels s on o.department_id=s.id 
					left join cuteframe.company c on s.company_id=c.id,app_order.app_order_details d 
					left join front.base_style_info s on d.goods_sn=s.style_sn and s.xilie like '%,24,%'
					 where o.id=d.order_id and o.pay_date>='{$date_start} 00:00:00' and  o.pay_date<='{$date_end} 23:59:59'
					and o.referer<>'婚博会'
					and o.is_zp=0
					and o.order_status=2 
					AND o.order_pay_status IN ('2','3','4') 
					AND o.is_delete=0
					and c.company_type=2					
					and 
					(
					if(s.style_sn is not null,1,0)
					) >0
					group by  o.department_id,o.create_user
				) as t1
				 left join 
				(
						SELECT
				             u.account,sum(-1) as return_tsyd_num 
				        FROM
				            app_order.base_order_info as o
				            left join app_order.app_return_goods rg on rg.order_sn = o.order_sn
				            left join cuteframe.user u on rg.apply_user_id=u.id 
				            left join app_order.app_return_check rc on rc.return_id = rg.return_id             
				            left join app_order.app_order_details d on d.id = rg.order_goods_id              
				            left join front.base_style_info s on d.goods_sn=s.style_sn and s.xilie like '%,24,%'
				        WHERE
						    rc.deparment_finance_status=1  
							and rg.return_by =1 and o.is_zp=0 and o.referer<>'婚博会' 
				            and rc.deparment_finance_time>='{$date_start} 00:00:00'
				            and rc.deparment_finance_time<='{$date_end} 23:59:59'
						and 
						(
						if(s.style_sn is not null,1,0)
						) >0
						group by u.account
				) as t2 

				on t1.create_user=t2.account
				order by t1.tsyd_num+ifnull(t2.return_tsyd_num,0) desc  limit 200";
		}

		return $this->db()->getAll($sql);
    }    

    //获取各个店业每个销售顾问业绩 个人新增-个人转退 （不含婚博会）
    public function get_sale_goods_amount($date_start,$date_end,$sys){
        if($sys=='boss'){
            $sql="
             	    select o.department_id,s.channel_name,o.create_user,
				      sum(a.goods_amount-a.favorable_price)
				        -
				        ifnull((SELECT
				             sum(if(rg.return_by=1,if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price),rg.real_return_amount)) as return_amount
				        FROM
				            app_order.base_order_info as o2
				            left join app_order.app_return_goods rg on rg.order_sn = o2.order_sn
				            left join cuteframe.user u on rg.apply_user_id=u.id 
				            left join app_order.app_return_check rc on rc.return_id = rg.return_id             
				            left join app_order.app_order_details d on d.id = rg.order_goods_id          
				        WHERE
				            rc.deparment_finance_status=1 
				            and rg.return_by in (1,2) and o2.is_zp=0 and o2.referer<>'婚博会' 
				            and rc.deparment_finance_time>='{$date_start} 00:00:00'
				            and rc.deparment_finance_time<='{$date_end} 23:59:59'
				            and u.account=o.create_user),0)    
				        +      
				        ifnull((select 
				              sum(rg3.real_return_amount)
				        FROM
				            app_order.base_order_info as o3
				            left join app_order.app_return_goods rg3 on rg3.order_sn = o3.order_sn
				            left join cuteframe.user u3 on rg3.apply_user_id=u3.id 
				            left join app_order.app_return_check rc3 on rc3.return_id = rg3.return_id             
				            left join app_order.app_order_details d3 on d3.id = rg3.order_goods_id          
				        WHERE
				            rc3.deparment_finance_status=1 
				            and rg3.return_by =2 and o3.is_zp=0 and o3.referer<>'婚博会' 
				            
				            and rg3.order_goods_id in (
				               select DISTINCT rg4.order_goods_id
				               FROM
				               app_order.base_order_info as o4
				               left join app_order.app_return_goods rg4 on rg4.order_sn = o4.order_sn
				               left join cuteframe.user u4 on rg4.apply_user_id=u4.id 
				               left join app_order.app_return_check rc4 on rc4.return_id = rg4.return_id             
				               left join app_order.app_order_details d4 on d4.id = rg4.order_goods_id          
				               WHERE
				               rc4.deparment_finance_status=1 
				               and rg4.return_by =1 and o4.is_zp=0 and o4.referer<>'婚博会' 
				               and rc4.deparment_finance_time>='{$date_start} 00:00:00'
				               and rc4.deparment_finance_time<='{$date_end} 23:59:59'
				               and u4.account=o.create_user              
				            )
				            and u3.account=o.create_user
				            ),0)             
				            as goods_amount
				 
						from app_order.base_order_info o left join cuteframe.sales_channels s on o.department_id=s.id,app_order.app_order_account a where o.id=a.order_id 
						and o.is_zp=0
						and o.department_id in (4,42,47,56,68)
						and o.referer<>'婚博会'
						and o.pay_date>='{$date_start} 00:00:00' 
						and o.pay_date<='{$date_end} 23:59:59'
						group by s.channel_name,o.create_user";
        
        }
        if($sys=='zhanting'){
            $sql="
            	    select o.department_id,s.channel_name,o.create_user,
				      sum(a.goods_amount-a.favorable_price)
				        -
				        ifnull((SELECT
				             sum(if(rg.return_by=1,if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price),rg.real_return_amount)) as return_amount
				        FROM
				            app_order.base_order_info as o2
				            left join app_order.app_return_goods rg on rg.order_sn = o2.order_sn
				            left join cuteframe.user u on rg.apply_user_id=u.id 
				            left join app_order.app_return_check rc on rc.return_id = rg.return_id             
				            left join app_order.app_order_details d on d.id = rg.order_goods_id          
				        WHERE
				            rc.deparment_finance_status=1 
				            and rg.return_by in (1,2) and o2.is_zp=0 and o2.referer<>'婚博会' 
				            and rc.deparment_finance_time>='{$date_start} 00:00:00'
				            and rc.deparment_finance_time<='{$date_end} 23:59:59'
				            and u.account=o.create_user),0)    
				        +      
				        ifnull((select 
				              sum(rg3.real_return_amount)
				        FROM
				            app_order.base_order_info as o3
				            left join app_order.app_return_goods rg3 on rg3.order_sn = o3.order_sn
				            left join cuteframe.user u3 on rg3.apply_user_id=u3.id 
				            left join app_order.app_return_check rc3 on rc3.return_id = rg3.return_id             
				            left join app_order.app_order_details d3 on d3.id = rg3.order_goods_id          
				        WHERE
				            rc3.deparment_finance_status=1 
				            and rg3.return_by =2 and o3.is_zp=0 and o3.referer<>'婚博会' 
				            
				            and rg3.order_goods_id in (
				               select DISTINCT rg4.order_goods_id
				               FROM
				               app_order.base_order_info as o4
				               left join app_order.app_return_goods rg4 on rg4.order_sn = o4.order_sn
				               left join cuteframe.user u4 on rg4.apply_user_id=u4.id 
				               left join app_order.app_return_check rc4 on rc4.return_id = rg4.return_id             
				               left join app_order.app_order_details d4 on d4.id = rg4.order_goods_id          
				               WHERE
				               rc4.deparment_finance_status=1 
				               and rg4.return_by =1 and o4.is_zp=0 and o4.referer<>'婚博会' 
				               and rc4.deparment_finance_time>='{$date_start} 00:00:00'
				               and rc4.deparment_finance_time<='{$date_end} 23:59:59'
				               and u4.account=o.create_user              
				            )
				            and u3.account=o.create_user
				            ),0)             
				            as goods_amount
				 
						from app_order.base_order_info o left join cuteframe.sales_channels s on o.department_id=s.id left join cuteframe.company c on s.company_id=c.id,app_order.app_order_account a where o.id=a.order_id 
						and o.is_zp=0
						and c.company_type=2
						and o.referer<>'婚博会'
						and o.pay_date>='{$date_start} 00:00:00' 
						and o.pay_date<='{$date_end} 23:59:59'
						group by s.channel_name,o.create_user";
        }

        return $this->db()->getAll($sql);
    }

}

?>