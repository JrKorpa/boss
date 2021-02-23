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
	
	//SELECT * FROM app_order_account AS a INNER JOIN base_order_info AS b ON a.order_id=b.id WHERE a.order_amount != (a.money_paid+a.money_unpaid) AND b.order_pay_status=3 AND b.referer='双11抓单'
}
?>

