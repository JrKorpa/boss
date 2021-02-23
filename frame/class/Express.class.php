<?php
/*
 * -------------------------------------------------
 * 快递信息查询类
 * @file        : Express.class.php
 * @author      : yangxt <yangxiaotong@163.com>
 * @date        : 2015-05-08 02:23:18
 * @version     : 1.0
 * -------------------------------------------------
*/
class Express
{
	private static $instance = NULL;
	private $link = NULL;
	public $exp_info = array();
	protected $link_key = 'aHR0cCUzQSUyRiUyRnd3dy5rdWFpZGkxMDAuY29tJTJG';

	protected $express = [
		'shunfeng'=>'顺丰速递',
		'yuantong'=>'圆通速递',
		'shentong'=>'申通速递',
		'zhongtong'=>'中通快递',
		'ems'=>'EMS',
		'yunda'=>'韵达速递',
		'youzhengguonei'=>'邮政包裹',
		'tiantian'=>'天天快递',
		'zhaijisong'=>'宅急送',
		'quanfenguaidi'=>'全峰快递',
		'huitongkuaidi'=>'百世汇通',
	];

	final private function __construct()
	{
		$this->link = $this->decrypt($this->link_key,'KELA');
	}

	public static function getIns($value='')
	{
		if(self::$instance === NULL){
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function decrypt($str, $key) {
		$str=urldecode(base64_decode($str));
		for($i=0;$i<strlen($str);$i++){
			$str[$i]=chr(ord($str[$i])-$key);
		}
		return $str;
	}

	private function autoNumber($exp_no)
	{
		$url = $this->link."/autonumber/autoComNum?text=".$exp_no."&temp=0.".mt_rand(1000000000000000,9999999999999999);
		$info = file_get_contents($url);
		$auto = json_decode($info)->auto;
		if(empty($auto)){
			$this->exp_info = array();
			return false;
		}
		$type = $auto[0]->comCode;
		return $type;
	}

	public function getExpName($exp_no)
	{
		$type = $this->autoNumber($exp_no);
		if($type){
			$name = '';
			if(array_key_exists($type, $this->express)){
				$name = $this->express[$type];
			}
			return $name;
		}
		return false;
	}

	public function getExpInfo($exp_no)
	{
		$info = $this->getExpAll($exp_no);
		return ((isset($info['data'])))?$info['data']:$info['message'];
	}

	public function getExpAll($exp_no)
	{
		$type = $this->autoNumber($exp_no);
		$url = $this->link.'query?type='.$type.'&postid='.$exp_no;
		$info = file_get_contents($url);
		$info = json_decode($info);

		$info = $this->object_array($info);
		return $info;
	}

	public function object_array($array) {
		if(is_object($array)) {
			$array = (array)$array;
		}
		if(is_array($array)) {
			foreach($array as $key=>$value) {

				$array[$key] = $this->object_array($value);
			}

		}
		return $array;
	}
}

?>