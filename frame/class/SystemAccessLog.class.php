<?php
/**
 *  -------------------------------------------------
 *   @file		: SystemAccessLog.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		:
 *   @update	:
 *  -------------------------------------------------
 */
class SystemAccessLog extends Model
{
	private static $_instance;//实例

	/**
	 *	__construct，构造函数
	 *
	 *	@param Integer $id 记录号
	 *	@param String $strConn 数据库连接配置
	 *
	 */
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'system_access_log';
        $this->_dataObject = array("id"=>"主键",
			"user_id"=>'操作员id',
			"status"=>"登录状态",
			"data"=>"数据",
			"create_time"=>"操作时间",
			"ip"=>"操作者ip"
		);

        $this->_prefix= "";
		parent::__construct($id,$strConn);
	}

	/**
	 *	getInstance，单例返回实例
	 *
	 *	@param Integer $id 记录号
	 *	@param String $strConn 数据库连接配置
	 *
	 */
	public static function getInstance($id=NULL,$strConn="")
	{
        if(!(self::$_instance instanceof self))
		{
			self::$_instance = new static($id,$strConn);
        }
        return self::$_instance;
    }

	/**
	 *	log，记录操作行为
	 *
	 *	@param String $user_id	操作者，可能为空
	 *	@param String $status	登录状态
	 *	@param String $strConn 数据库连接配置
	 *
	 *	@return $id 日志记录号
	 *
	 */
	public static function log ($user_id,$status,$data,$strConn="2")
	{
		$model = self::getInstance($strConn);
		$model->setValue("user_id",$user_id);
		$model->setValue("status",$status);
		$model->setValue("data",$data);
		$model->setValue("ip", Util::getClicentIp());
		$model->setValue("create_time",time());
		$log_id = $model->create();
		return $log_id;
	}
	/**
	 *	getLogs，获取日志列表
	 *
	 *	@param Aeeay $where
		Int user_id 哪个用户
		String start_date 开始时间
		String $end_date 结束时间
	 *	@param String $strConn 数据库连接配置
	 *	@param int $page 当前页
	 *	@param int $pageSize 每页数量
	 *
	 *	@return Array
	 */
	public static function getLogs ($where,$page,$pageSize=10,$strConn="1")
	{
		$str = '';
		if(!empty($where['user_id']))
		{
			$str .= "l.`user_id`=".$where['user_id']." AND ";
		}
		if(empty($where['start_date']))
		{
			$start_date = date('Y-m-d',0);
		}
		else
		{
			$start_date= $where['start_date'];
		}
		if(empty($where['end_date']))
		{
			$end_date = date('Y-m-d')." 23:59:59";
		}
		else
		{
			$end_date =$where['end_date']." 23:59:59";
		}
		$str .= "l.`create_time` BETWEEN ".strtotime($start_date)." AND ".strtotime($end_date)." AND ";

		$sql = "select l.*,u.account,u.real_name from `system_access_log` AS l LEFT JOIN user AS u ON l.user_id=u.id";
		if(!empty($str))
		{
			$str = " where ".rtrim($str,"AND ");
			$sql .=$str;
		}
		$sql .=" order by l.id desc";
		if(!$pageSize) $pageSize=10;
		$data = self::getInstance(null,$strConn)->db()->getPageList($sql,array(),$page, $pageSize,false);
		return $data;
	}
}
?>