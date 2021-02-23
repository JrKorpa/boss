<?php
error_reporting(E_ALL & ~E_NOTICE);
header('Content-Type: text/html; charset=utf-8');

class autoTransfer extends transferModel {
	private $errormsg; //错误信息
	private $runtime  = array("start" => "", "end" => ""); //运行时间
	private $run_type = 0;
	private $pageMax  = 200; //定义每一单所含货品数量的最大值
	private $aryDone  = array();

	function __construct($run_type) {
		ini_set('memory_limit', -1);
		set_time_limit(0);

		$this->run_type = $run_type;
		$this->runtime['start'] = date("Y-m-d H:i:s"); //开始运行时间
		//记寻状态 (开始)
		$this->runstatus(1);

		$this->main();

		$this->runlog(); //记录执行LOG
		$this->runstatus(2);
	}

	function main() {
		$this->aryDone = $this->readAlreadyDone();
		// print_r($this->aryDone);
		echo count($this->aryDone) . " goods already done.\n";

		$this->writeLog('Start:');

		$aryData = $this->getAllData();
		// var_dump($aryData);exit;
		

		$num_bill = 0; // 总制单数
		$num_goods = 0; // 总影响的货品数
		foreach ($aryData as $mkey => $mv) 
		{
			$item = $mv;
			$warehouse_id = $item[0]['warehouse_id'];
			$warehouse    = $item[0]['warehouse'];
			$company_id   = $item[0]['company_id'];
			$company      = $item[0]['company'];
			$create_time  = $item[0]['check_time'];
			$goods = $item;

			// 过滤已经做过的：
			$goods2 = array();
			foreach ($goods as $key => $value) {
				if (in_array($value['goods_id'], $this->aryDone)) {
					//echo "\n duplicate: ".$value['goods_id'];
					continue;
				}
				$goods2[] = $value;
			}

			$goods_page = array_chunk($goods2, $this->pageMax);
			foreach ($goods_page as $key => $value) {
				$a_goods_page = $value;
				$num_bill++;
				$page_size = count($a_goods_page);
				$num_goods += $page_size;

				$this->writeLog( "Now C=". $company_id." W=".$warehouse_id." Source:".$value[0]['bill_no']." ---- ".count($a_goods_page)." goods");
				
				$this->makeBill($company, $company_id, $warehouse, $warehouse_id, $a_goods_page, $create_time);
				// var_dump($a_goods_page);
				
				// var_dump($page_size);
			}	
		}
		
		$str_r = ($this->run_type==0)?"Completed!":"Tested!";
		$this->writeLog($num_bill*2 . ' bills, ' . $num_goods . ' goods '. $str_r);
	}

	public function makeBill($company, $company_id, $warehouse, $warehouse_id, $goods, $create_time) {
		$this->writeLog($company." ". $company_id." ". $warehouse." ". $warehouse_id." ". count($goods)." ". $create_time);
		// test run mode:
		if ($this->run_type==1) {
			return;
		}

		// 制作M单：
		$bill_m_no = '';
		$num_m = 0;
		$amount_m = 0;
		$ret = $this->createBillM($company, $company_id, $warehouse, $warehouse_id, $goods, $create_time);
		if (isset($ret['success'])) {
			if ($ret['success'] == 0) {
				echo $ret['error'];
				return;
			} else {
				$bill_m_no = $ret['bill_no'];
				$num_m = $ret['num'];
				$amount_m = $ret['amount'];
			}
		} else {
			echo 'unkown error';
			return;
		}

		// 马上制作新的M单（方法沿用Y单），发回原仓库：
		$bill_y_no = '';
		$num_y = 0;
		$amount_y = 0;
		$create_time = date("Y-m-d H:i:s", strtotime($create_time) + 60);
		$ret2 = $this->createBillY($company, $company_id, $warehouse, $warehouse_id, $goods, $create_time);
		if (isset($ret2['success'])) {
			if ($ret2['success'] == 0) {
				echo $ret2['error'];
				return;
			} else {
				$bill_y_no = $ret2['bill_no'];
				$num_y = $ret2['num'];
				$amount_y = $ret2['amount'];
			}
		} else {
			echo 'unkown error';
			return;
		}

		// Log the changes:
		$this->writeLog("{$bill_m_no}(num:{$num_m}, amount:{$amount_m}) to {$bill_y_no}(num:{$num_y}, amount:{$amount_y})");
	}

	/**
	 *
	 * 记录运行
	 */
	function runlog() {
		$this->runtime['end'] = date("Y-m-d H:i:s"); //结束运行时间
		$string = "[@transferBillingMtoY@][#Start:" . $this->runtime['start'] . "#]";
		//$string.= "";
		$string .= "[#End:" . $this->runtime['end'] . "#]\n";
		$tp = fopen(ROOT_PATH . "data/access.log", "a");
		fwrite($tp, $string);
		fclose($tp);
	}

	/**
	 *
	 * 记录ERROR
	 * @param unknown_type $file
	 * @param unknown_type $line
	 */
	function runerror($file, $line) {
		$string = "[@transferBillingMtoY@][#Start:" . $this->runtime['start'] . "#]";
		$string .= "[FILE:'" . $file . "'&&LINE:'" . $line . "']" . $this->errormsg;
		$string .= "[#End:" . $this->runtime['end'] . "#]\n";
		$tp = fopen(ROOT_PATH . "data/error.log", "a");
		fwrite($tp, $string);
		fclose($tp);
	}

	/**
	 *
	 * 运行状态
	 * @param unknown_type $s
	 */
	function runstatus($s = 1) {
		$filename = ROOT_PATH . "data/runstatus.log";
		$dir = str_replace("runstatus.log", "", $filename);
		if (!is_dir(ROOT_PATH . 'data/')) {
			mkdir(ROOT_PATH . 'data/');
			mkdir($dir);
		} else {
			if (!is_dir($dir)) {
				mkdir($dir);
			}
		}
		if ($s == 1) {
			$string = "transfering (background) ...";
			file_put_contents($filename, $string);
		} else {
			$string = "transfer all completed.";
			file_put_contents($filename, $string);
		}
	}

}
