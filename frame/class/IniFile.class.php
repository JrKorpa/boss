<?php
/**
 *  -------------------------------------------------
 *   @file		: IniFile.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-09
 *   @update	:
 *  -------------------------------------------------
 */
/**
 * 读写Ini文件的类
 */
class IniFile
{
	public $_Settings;
	private $_File;
	public function Load($file)
	{
		$this->_Settings = parse_ini_file($file,true);
		$this->_File = $file;
	}
	private function ToStr($arr)
	{
		$ok   =   "";
		$s   =   "";
		foreach($arr as  $k=>$v)   {
			if(is_array($v))   {
				if($k   !=   $ok)   {
					$s   .=   PHP_EOL."[$k]".PHP_EOL;
					$ok   =   $k;
				}
				$s   .=   $this->ToStr($v,"");
			}else   {
				if(trim($v)   !=   $v   ||   strstr($v,"["))
				$v   =   "\"$v\"";
				$s   .=   "$k   =   $v".PHP_EOL;
			}
		}
		return $s;
	}
	/**
	 * 把设置信息保存到文件中
	 * @param $filename	文件名，可以为空，为空时保存到
	 * @return unknown_type
	 */
	public function Save($filename='')
	{
		if (empty($filename)) $filename = $this->_File;
		$str = $this->ToStr($this->_Settings);
		$fp   =   fopen($filename,"w");
		fwrite($fp,$str);
		fclose($fp);
	}
	/**
	 *为INI文件的配置项赋值
	 * @param $key
	 * @param $val
	 * @return unknown_type
	 */
	public function Set($key,$val)
	{
		foreach ($this->_Settings as $k=>$v)
		{
			if (is_array($v))
			{
				foreach($v as $k1=>$v1)
				{
					if ($k1==$key)
					{
						$this->_Settings[$k][$k1]=$val;
						return;
					}
				}
			}
			else
			{
				if ($k == $key)
				{
					$this->_Settings[$k] = $val;
					return;
				}
			}
		}
		$this->_Settings[$key] = $val;
	}
	/**
	 *读取INI的配置项的值
	 * @param $key 配置项的名称
	 * @param $default 默认值
	 * @return unknown_type
	 */
	public function Get($key,$default='')
	{
		$val='';
		foreach ($this->_Settings as $k=>$v)
		{
			if (is_array($v))
			{
				foreach($v as $k1=>$v1)
				{
					if ($k1==$key)
					{
						$val= $this->_Settings[$k][$k1];
						break;
					}
				}
			}
			else
			{
				if ($k == $key)
				{
					$val = $this->_Settings[$k];
					break;
				}
			}
		}
		if (empty($val)) $val = $default;
		return $val;
	}
	/**
	 * 读取一个配置段的所有配置项
	 * @param $Name 配置段的名称
	 * @return 返回一个数组
	 */
	public function GetSection($Name)
	{
		foreach ($this->_Settings as $k=>$v)
		{
			if (is_array($v) && $k==$Name)
			{
				return $v;
			}
		}
		return array();
	}
}
?>