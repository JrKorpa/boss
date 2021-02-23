<?php
global $lib_path;
require_once($lib_path.'/MysqlDB.class.php');

function on_user_upserted($data, $db) {

	$uid = $data['user_id'];
	$old_user_name = isset( $data['old_account']) ?  $data['old_account'] : '' ;
	
	$user_info = $db->getRow("SELECT account, password, is_enabled, is_on_work, is_deleted from `user` where id = {$uid}; ");
	if (empty($user_info)) {
		return;
	}
	
	global $db_conf;
	$sso_db = new MysqlDB($db_conf['sso']);
	
	$account = empty($old_user_name) ? $user_info['account'] : $old_user_name;
	
	if ($user_info['is_enabled'] == '1' && $user_info['is_deleted'] == 0) {
		// 1. if user exist in SSO DB, then only update password and enabled
		$sso_user = $sso_db->getRow("select * from oauth2_user where account='{$account}';");
		$sso_user_new = empty($old_user_name) ? false : $sso_db->getRow("select * from oauth2_user where account='{$user_info['account']}';");
		if (!empty($sso_user) || !empty($sso_user_new)) {
			$uuid = !empty($sso_user) ? $sso_user['uuid'] : $sso_user_new['uuid'];
			$sso_db->exec(
			"update oauth2_user set password = '{$user_info['password']}', `enabled` = 1, account='{$user_info['account']}' where uuid = {$uuid};
			 insert into oauth2_user_client(client_id, user_id, access_token_exp) values('{$data['sys_scope']}', {$uuid}, 86400) on DUPLICATE KEY update access_token_exp = 86400;");
		} else {
			// 2. if user doestn't exist, then we should create user and grant user access
			$sso_db->exec(
			"insert into oauth2_user(account, password, enabled) values('{$user_info['account']}','{$user_info['password']}',1);
			 insert into oauth2_user_client(client_id, user_id, access_token_exp) select '{$data['sys_scope']}', uuid, 86400 from oauth2_user where account = '{$user_info['account']}';");			
		}			
	} else {
		// 1. only disable user in SSO DB
		$sso_db->exec("update oauth2_user set password = '{$user_info['password']}', `enabled` = 0 where account='{$user_info['account']}';");
	}
	
	echo "user {$uid} processed.".PHP_EOL;
}


?>
