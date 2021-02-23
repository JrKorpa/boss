<?php
	header("Content-type:text/html;charset=utf-8");
	date_default_timezone_set('Asia/Shanghai');
	require_once('MysqlDB.class.php');
	set_time_limit(0);
	ini_set('memory_limit','2000M');

	$conf = [
		/*'dsn'=>"mysql:host=127.0.0.1;dbname=warehouse_shipping",
		'user'=>"root",
		'password'=>"root",
		'charset' => 'utf8'*/
		'dsn'     => "mysql:host=192.168.1.93;dbname=warehouse_shipping;",
		'user'    => "cuteman",
		'password'=> "QW@W#RSS33#E#",
		'charset' => 'utf8'
	];

	$db = new MysqlDB($conf);

	echo 'GAMES START GO!GO!GO! >>>>>>>>>>>>>>';

		$goods_sn_arr = array('KLSX027779','KLSX027904','KLSX028149','KLSX028148','KLRM024770','KLPX015846','KLBW027168 ','KLZW027205','KLSX027479','KLSX027242','KLSX027219','KLSX027218','KLSX027217','KLSX027216','KLSX027215','KLSX027214','KLSX027213','KLSX027212','KLSX027211','KLSX027209','KLSX027208','KLSX026460','KLSX026348','KLSW027812','KLSW027477','KLSW027476','KLRX027202','KLRX027198','KLRX027070','KLRX019446','KLRW027009','KLRW013229','KLQX028256','KLQX028255','KLQX028248','KLQX028247','KLQX027609','KLQX027608','KLQX026367','KLQX026016','KLQX022815','KLQX022133','KLQX020641','KLQX016680','KLQX016666','KLPX027533','KLPX027324','KLPX026971','KLPX026494','KLPW027326','KLPW027222','KLPW027072','KLPW027071','KLPW027041','KLPW026975','KLPW026973','KLPW026970','KLPW026074','KLPW025968','KLPW021459','KLPW021456','KLPM027040','KLPM026974','KLPM026972','KLPM026969','KLNW027963','KLNW027962','KLNW027360','KLNW027164','KLNM027525','KLNM027519','KLNM027371','KLDW020917','KLBX028094','KLBX028093','KLBW027473','KLBW027168','KLBW027166','KLBW027163','KLBW027022','KLBW027010','KLBW024241');

		$sql = "SELECT `a`.`goods_sn` , `c`.`box_sn` FROM `warehouse_goods` AS `a` INNER JOIN `goods_warehouse` AS `b` ON `a`.`goods_id` = `b`.`good_id` LEFT JOIN `warehouse_box` AS `c` ON `b`.`box_id` = `c`.`id` WHERE `a`.`is_on_sale` = 2 AND `a`.`company_id` = 58 AND `a`.`goods_sn` = ?";

		$dbth = $db->prepare($sql);
		foreach($goods_sn_arr AS $goods_sn){
			$error = $goods_sn;
			$dbth->execute(array($goods_sn));
			$data = $dbth->fetchAll(PDO::FETCH_ASSOC);
			$res = array_unique(array_column($data, 'box_sn'));
			foreach($res AS $val){
				$error .= "	{$val}";
			}
			echo $error .= "\r\n";
			file_put_contents(__DIR__."/xxx.log", $error , FILE_APPEND);
		}

	echo 'GAMES OVER GO!GO!GO! >>>>>>>>>>>>>>';
?>