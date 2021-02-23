<?php

class Template {
	
	public function exec($sql) {
		if (empty($sql)) {
			die('sql can not be null');
		}
		
		echo '======== begin ========'.PHP_EOL;
		set_time_limit(0);

		echo 'start to connect database'.PHP_EOL;
		
		try {
			$db = new PDO('mysql:host=192.168.1.59;port=3306;dbname=front', 'cuteman', 'QW@W#RSS33#E#', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$res = $db->prepare($sql);
		
			echo 'start to execute sql'.PHP_EOL;
			
			if($res->execute() === false) {
				echo 'execution failed.'.PHP_EOL;
			} else {
				echo 'finish execution'.PHP_EOL;
			}
		} catch(Exception $e) {
			echo 'an error'.PHP_EOL;;
			die(json_encode($e));
		};
			
		echo '======== end ========';
	}
}

?>