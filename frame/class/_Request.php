<?php
/**
 * 客户端端请求的全局变量的类。
 * 此文件中包含5个静态类，分别是 _Get, _Post,_Request,
 *
 */
class _Request
{
    /**
     * 获取变量值，存在则返回原值，否则返回默认值。
     *
     * @param string $var
     * @param mixed $default
     * @return mixed
     */
    public static function get($var, $default=null)
    {	
    	$value = isset($_REQUEST[$var]) ? $_REQUEST[$var] : $default;
        return trim($value);
    }

    /**
     * 获取变量值，存在则返回将原值转换成浮点型并返回整数部分，
     * 不存在则将默认值转换成浮点型并整数返回。
     *
     * @param string $var
     * @param mixed $default
     * @return float
     */
    public static function getInt($var, $default=null)
    {
		$value = isset($_REQUEST[$var]) ? $_REQUEST[$var] : $default;
        return floor((float)$value);
    }

    /**
     * 获取变量值，存丰则返回将原值转换成浮点型返回，
     * 不存在则将默认值转换成浮点型返回。
     *
     * @param string $var
     * @param mixed $default
     * @return float
     */
    public static function getFloat($var, $default=null)
    {
		$value = isset($_REQUEST[$var]) ? $_REQUEST[$var] : $default;
        return (float)$value;
    }

    /**
     * 获取变量值，
     * 存在则返回将原值转换成字符串并trim后返回，
     * 不存在则将默认值转换成字符串并trim后返回。
     *
     * @param string $var
     * @param string $default
     * @return string
     */
    public static function getString($var, $default=null)
    {
		$value = isset($_REQUEST[$var]) ? $_REQUEST[$var] : $default;
        return _Request::transInvalidChar(trim((string)$value));
    }

    /**
     * 获取变量值，有则返回将原值转换成数组返回，没有设置则将默认值转换成数组返回。
     *
     * @param string $var
     * @param array $default
     * @return array
     */
    public static function getList($var, array $default=array())
    {
		$value = isset($_REQUEST[$var]) ? $_REQUEST[$var] : $default;
        return (array)$value;
    }

    // 转意非法字符，目前单引号和双引号已转义
    public static function transInvalidChar($str) {
        // 转义配对双引号 为中文
        $str = preg_replace('/"([^"]*)"/', '“${1}”', $str);
        // 转义单个双引号 为中文
        $str = str_replace('"', '”', $str);
        // 转义配对单引号 为中文
        $str = preg_replace("/'([^']*)'/", '‘${1}’', $str);
        // 单个单引号替换
        $str = str_replace("'", "’", $str);
        return $str;
    }
}

class _Post
{
	/**
	 * 获取变量值，存在则返回原值，否则返回默认值。
	 *
	 * @param string $var
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get($var, $default=null)
	{
		$value = isset($_POST[$var]) ? $_POST[$var] : $default;
		return trim($value);
	}

	/**
	 * 获取变量值，存在则返回将原值转换成浮点型并返回整数部分，
	 * 不存在则将默认值转换成浮点型并整数返回。
	 *
	 * @param string $var
	 * @param mixed $default
	 * @return float
	 */
	public static function getInt($var, $default=null)
	{
		$value = isset($_POST[$var]) ? $_POST[$var] : $default;
		return floor((float)$value);
	}

	/**
	 * 获取变量值，存丰则返回将原值转换成浮点型返回，
	 * 不存在则将默认值转换成浮点型返回。
	 *
	 * @param string $var
	 * @param mixed $default
	 * @return float
	 */
	public static function getFloat($var, $default=null)
	{
		$value = isset($_POST[$var]) ? $_POST[$var] : $default;
		return (float)$value;
	}

	/**
	 * 获取变量值，
	 * 存在则返回将原值转换成字符串并trim后返回，
	 * 不存在则将默认值转换成字符串并trim后返回。
	 *
	 * @param string $var
	 * @param string $default
	 * @return string
	 */
	public static function getString($var, $default=null)
	{
		$value = (isset($_POST[$var]) && !empty($_POST[$var])) ? $_POST[$var] : $default;
        return _Request::transInvalidChar(trim((string)$value));
	}

	/**
	 * 获取变量值，有则返回将原值转换成数组返回，没有设置则将默认值转换成数组返回。
	 *
	 * @param string $var
	 * @param array $default
	 * @return array
	 */
	public static function getList($var, array $default=array())
	{
		$value = isset($_POST[$var]) ? $_POST[$var] : $default;
		return (array)$value;
	}
}

class _Get
{
	/**
	 * 获取变量值，存在则返回原值，否则返回默认值。
	 *
	 * @param string $var
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get($var, $default=null)
	{
		$value = isset($_GET[$var]) ? $_GET[$var] : $default;
		return trim($value);
	}

	/**
	 * 获取变量值，存在则返回将原值转换成浮点型并返回整数部分，
	 * 不存在则将默认值转换成浮点型并整数返回。
	 *
	 * @param string $var
	 * @param mixed $default
	 * @return float
	 */
	public static function getInt($var, $default=null)
	{
		$value = isset($_GET[$var]) ? $_GET[$var] : $default;
		return floor((float)$value);
	}

	/**
	 * 获取变量值，存丰则返回将原值转换成浮点型返回，
	 * 不存在则将默认值转换成浮点型返回。
	 *
	 * @param string $var
	 * @param mixed $default
	 * @return float
	 */
	public static function getFloat($var, $default=null)
	{
		$value = isset($_GET[$var]) ? $_GET[$var] : $default;
		return (float)$value;
	}

	/**
	 * 获取变量值，
	 * 存在则返回将原值转换成字符串并trim后返回，
	 * 不存在则将默认值转换成字符串并trim后返回。
	 *
	 * @param string $var
	 * @param string $default
	 * @return string
	 */
	public static function getString($var, $default=null)
	{
		$value = isset($_GET[$var]) ? $_GET[$var] : $default;
        return _Request::transInvalidChar(trim((string)$value));
	}

	/**
	 * 获取变量值，有则返回将原值转换成数组返回，没有设置则将默认值转换成数组返回。
	 *
	 * @param string $var
	 * @param array $default
	 * @return array
	 */
	public static function getList($var, array $default=array())
	{
		$value = isset($_GET[$var]) ? $_GET[$var] : $default;
		return (array)$value;
	}
}