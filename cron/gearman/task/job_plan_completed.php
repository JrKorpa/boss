<?php

function on_job_plan_completed($data, $db) {
	$apply_id = $data['id'];
	
	// 1. 查出job相关信息
	$job_info = $db->getRow("SELECT d.*, p.applicant, p.recipient_list, p.apply_time, p.job_status from job_plan p inner join job_definition d on d.id = p.job_id  where p.id = {$apply_id} and `enabled` = 1 and `job_status` = 4;");
	if (empty($job_info)) {
		echo  'this job was confirmed or unvalid!'.PHP_EOL;
		return;
	}
	
	// 2. 任务完成，调整状态为6。
	$db->exec("update job_plan set job_status = 6, end_time = NOW() where id = {$apply_id};");
	
	// 3. 任务完成，通知任务结果接收人
	if (!empty($job_info['recipient_list'])) {
		$sys_name = $data['sys_scope'] == 'boss' ? "boss系统" : "浩鹏展厅系统";
		$site = $data['sys_scope'] == 'boss' ? "boss.kela.cn" : "zhanting.kela.cn";
	
		$cmd = 
"sendmail {$job_info['recipient_list']} << EOF
subject: 您的提数需求已完成
from: {$site} <{$site}>

{$job_info['applicant']} 于 {$job_info['apply_time']} 在 {$sys_name} 提交了一份关于 {$job_info['name']} 的提数请求，系统已处理完毕，请前去确认取数。
EOF";
		exec($cmd);
		return;
	}
}

?>