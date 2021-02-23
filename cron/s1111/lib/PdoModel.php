<?php
class PdoModel
{
	public $mysqli;
	public function __construct()
	{
		//$this->mysqli = new mysqli('192.168.1.59','cuteman','QW@W#RSS33#E#','app_order');
		$this->mysqli = new mysqli('192.168.0.95','cuteman','QW@W#RSS33#E#','app_order');
		$this->mysqli->set_charset("utf8");
	}
	public function __distuct()
	{
		$this->mysqli->close();
	}
}
?>