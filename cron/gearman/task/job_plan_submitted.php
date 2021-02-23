<?php

function on_job_plan_submitted($data, $db) {
/*	
#当次任务状态：待确认1，等待系统确认2，排队中3，执行中4，中断5，已完成6
# 待确认：任务一旦提交，状态置为1，即进入待业务确认状态(发送job_plan_submitted), 业务确认后，状态置为2，发送job_plan_confirmed
# 待系统确认：任务需要系统确认(automate  = 0)，系统确认后，状态置为3
# 排队中：业务和系统都已确认，job处于排队状态
# 执行中：任务进入执行阶段
# 中  断：任务中断
# 已完成：任务执行完成
*/
	$apply_id = $data['id'];
	
	// 1. 查出job相关信息
	$job_info = $db->getRow("SELECT d.*, p.applicant, p.recipient_list, p.apply_time, p.job_status from job_plan p inner join job_definition d on d.id = p.job_id  where p.id = {$apply_id} and `enabled` = 1 and `job_status` = 1;");
	if (empty($job_info)) {
		echo  'this job was confirmed or unvalid!'.PHP_EOL;
		return;
	}
	
	$sys_name = $data['sys_scope'] == 'boss' ? "boss系统" : "浩鹏展厅系统";
	$site = $data['sys_scope'] == 'boss' ? "boss.kela.cn" : "zhanting.kela.cn";
	
	//2. 任务一旦提交，即进入待业务主管确认状态
	//TODO: 判断是否需要业务主管进行确认
    if (!empty($job_info['job_keeper'])) {
		$cmd = 
"sendmail {$job_info['job_keeper']} << EOF
subject: 有一份提数需求待您确认
from: {$site} <{$site}>

{$job_info['applicant']} 于 {$job_info['apply_time']} 在 {$sys_name} 提交了一份关于 {$job_info['name']} 的提数请求，请前去确认。
EOF";
		exec($cmd);
		return;
	}
	
	//3. 如果任务定义业务主管不需要确认，即进入系统待确认阶段
	//TODO: 是否需要系统(技术人员)进行确认
	if ($job_info['automated'] == '0') {
		$cmd = 
"sendmail {$job_info['supervisor']} << EOF
subject: 有一份提数需求待您确认
from: {$site} <{$site}>

{$job_info['applicant']} 于 {$job_info['apply_time']} 在 {$sys_name} 提交了一份关于 {$job_info['name']} 的提数请求，请前去确认。
EOF";
		exec($cmd);
		return;
	}
	
	// 4. 任务两阶段均不需要确认，调整状态为3
	$db->exec("update job_plan set job_status = 3 where id = {$apply_id};");
}

?>