<?php

function on_job_plan_confirmed($data, $db) {
	$apply_id = $data['id'];
	
	// 1. 查出job相关信息
	$job_info = $db->getRow("SELECT d.*, p.applicant, p.recipient_list, p.apply_time, p.job_status from job_plan p inner join job_definition d on d.id = p.job_id  where p.id = {$apply_id} and `enabled` = 1 and `job_status` = 2;");
	if (empty($job_info)) {
		echo  'this job was confirmed or unvalid!'.PHP_EOL;
		return;
	}
	
	//2. 任务业务主管已确认，判断是否需要系统(技术人员)进行确认
	if ($job_info['automated'] == '0') {
		$sys_name = $data['sys_scope'] == 'boss' ? "boss系统" : "浩鹏展厅系统";
		$site = $data['sys_scope'] == 'boss' ? "boss.kela.cn" : "zhanting.kela.cn";
	
		$cmd = 
"sendmail {$job_info['supervisor']} << EOF
subject: 有一份提数需求待您确认
from: {$site} <{$site}>

{$job_info['applicant']} 于 {$job_info['apply_time']} 在 {$sys_name} 提交了一份关于 {$job_info['name']} 的提数请求，请前去确认。
EOF";
		exec($cmd);
		return;
	}
	
	// 4. 如果任务不需要系统(技术)确认，调整状态为3。
	$db->exec("update job_plan set job_status = 3 where id = {$apply_id};");
}

?>