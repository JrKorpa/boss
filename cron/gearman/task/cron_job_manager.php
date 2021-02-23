<?php
 
 /*
 获取排队中的job，按优先级从高到低进行依次执行，为避免数据库压力，需设置执行间隔时间5分钟
 当次任务状态：待确认1，等待系统确认2，排队中3，执行中4，中断5，已完成6
 */
require_once(__DIR__ . '/../MysqlDB.class.php');

// 设置最多并行job数量
define('MAX_JOBS_IN_PARALLEL', 2);

$db_conf = [
	'boss' => [
		'dsn'=>"mysql:host=192.168.1.59;dbname=cuteframe",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
		],
	'zhanting' => [
		'dsn'=>"mysql:host=192.168.1.132;dbname=cuteframe",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
	]	
];

$sys_scope = '';
if (php_sapi_name() === 'cli' && isset($argv[1])) {
	$sys_scope = $argv[1];
}

if (empty($sys_scope) || !in_array($sys_scope, array_keys($db_conf))) {
	echo 'Sorry, we can not understand what you want to fuck and will exit after 3 seconds...'.PHP_EOL;
	sleep(3);
	exit;
}

$db = new MysqlDB($db_conf[$sys_scope]);

/* TODO:
1. check the number of running jobs;
2. check the running jobs status if multiple jobs are running;
*/
$running_jobs = $db->getAll("select * from job_plan p inner join job_definition d on d.id = p.job_id where job_status = 4 order by start_time asc;");
$running_jobs_num = count($running_jobs);
if ($running_jobs_num >= MAX_JOBS_IN_PARALLEL) {
	//TODO: check healthy of the running jobs if multiple jobs are running.
	check_running_jobs($running_jobs);
	exit;
}

//TODO: it is the time to run the hightest priority job at present.
$waiting_job = $db->getRow("SELECT * from job_plan p inner join job_definition d on d.id = p.job_id where p.job_status = 3 order BY p.priority desc limit 1;");
if (!empty($waiting_job)) {
	exec_job($waiting_job);
	exit;
}

//TODO: we should check the running jobs since there is no waiting job.
if ($running_jobs > 0) {
	check_running_jobs($running_jobs);
}

function check_running_jobs($jobs) {
	$running_pids = list_running_pids(array_column($jobs, 'exec_pid'));
	$tofix_jobs = array();
	foreach($jobs as $jb) {
		$is_running = false;
		foreach($running_pids as $k=>$pid) {
			if ($pid == $jb['exec_pid']) {
				$is_running = true;
				unset($running_pids[$k]);
				break;
			}
		}
		
		if (!$is_running) {
			//TODO: try to update job_status for next check
			$tofix_jobs[] = $jb['id'];
		}
	}
	
	if (!empty($tofix_jobs)) {
		global $db, $sys_scope;
		//TODO: job_status = 4, but no running process exist, we forcly update the status, and notify system manager
		$ids = implode(',', array_values($tofix_jobs));
		$db->exec("update job_plan set job_status = 5 where id in ({$ids}) and job_status = 4;");
		
		//TODO: supervisors are the same actually, so that we notify one time
		$cmd = 
"sendmail {$jobs[0]['supervisor']} << EOF
subject: job执行异常待您确认
from: {$sys_scope} <{$sys_scope}.kela.cn>

系统检测有{}项job执行可能出现异常，请前去确认。
EOF";
		exec($cmd);
	}
}

function list_running_pids(Array $pids) {
	$pids_str = implode(' ', $pids);
	exec("ps -o pid= -p $pids_str", $output);
	return $output;
}

function exec_job($job) {
	global $db, $sys_scope;
	
	$cmdline = $job['exec_command']. ' '. $sys_scope . ' '.$job['id'];
	$output_arr = array();
	exec(escapeshellcmd($cmdline) .' >/dev/null 2>&1 & echo $! ', $output_arr);
	$pid = empty($output_arr) ? -1 : $output_arr[0];
	$db->exec("update job_plan set job_status = 4, start_time = NOW(), exec_pid ={$pid} where id = {$job['id']};");
}

 
?>