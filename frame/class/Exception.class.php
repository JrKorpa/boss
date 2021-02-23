<?php
/**
 *  -------------------------------------------------
 *   @file		: Exception.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-05-12
 *   @update	:
 *  -------------------------------------------------
 */
 /**
  *	TException，自定义异常基类
  *
  */
class TException extends Exception
{
	/**
	 *	构造函数
	 *
	 *	@param String $msg 异常内容
	 *	@param Int $code 异常代码
	 *
	 */
	public function __construct($message,$code=0)
	{
		parent::__construct($message,$code);
	}
}
 /**
  *  -------------------------------------------------
  *		DBException，数据库连接异常类定义
  *
  *  -------------------------------------------------
  */
class DBException extends TException
{
	/**
	 *	构造函数
	 *
	 *	@param String $msg 异常内容
	 *	@param Int $code 异常代码
	 *
	 */
	public function __construct($message,$code=0)
	{
		$errmsg = "数据库连接异常：".$message;
		parent::__construct($errmsg, $code);
	}
}
/**
 *	FileException，文件操作异常
 *
 */
class FileException extends TException
{

}
/**
 *	MethodException，方法不存在
 *
 */
class MethodException extends TException
{

}
/**
 *	ObjectException，对象属性异常
 *
 */
class ObjectException extends TException
{
	/**
	 *	构造函数
	 *
	 *	@param String $msg 异常内容
	 *	@param Int $code 异常代码
	 *
	 */
	public function __construct($message,$code=0)
	{
		$errmsg = "数据表不存在该属性：".$message;
		parent::__construct($errmsg, $code);
	}
}
?>