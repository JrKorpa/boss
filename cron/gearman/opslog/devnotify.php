<?php


function on_devnotify($data, $db) {

	$content = $data['msg'];
	$sys_scope = $data['sys_scope'];
	
	if (empty($content)) return;
	
	mail("quanxiaoyun@kela.cn", "系统异常信息通知", $content, "from: {$sys_scope} <{$sys_scope}.kela.cn> ");
}


?>