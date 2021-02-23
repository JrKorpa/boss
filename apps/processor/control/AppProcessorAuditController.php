<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorAuditController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-22 10:07:27
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorAuditController extends Controller
{
	protected $smartyDebugEnabled = false;

	public function index($params) {}

	/**
	 * 审核操作
	 * @author yangxiaotong
	 */
	public function checkPass(){
		$check_id = _Post::getInt('user_id');			//审核人
		if($check_id !=$_SESSION['userId']){
			echo "对不起!您无权操作!";
			exit;
		}
		$record_id = _Post::getInt('record_id');	//申请ID
		$process_id = _Post::getInt('process_id');	//流程ID
		$pass = _Post::getInt('pass');				//审核状态
		$user_sum = _Post::getInt('user_sum'); 		//审核人数
		$recordModel = new AppProcessorRecordModel($record_id,13);
		$view = new AppProcessorRecordView($recordModel);
		$user = $view->getCheckUser();			//获取审核人
		$user = $view->getAuditStatus($user,$record_id);	//获取审核状态
		$order=0;//是否是最后一个人审核
		foreach($user as $key=>$v)
		{
			if($_SESSION['userId']==$v['user_id'])
			{
				$order=$key+1;
				break;
			}
		}
		$olddo = array();
		$newdo=array(
			'record_id'=>$record_id,
			'process_id'=>$process_id,
			'user_id'=>$_SESSION['userId'],
			'audit_status'=>$pass,//1通过,2驳回
			'audit_time'=>time()
		);

		$newmodel =  new AppProcessorAuditModel(14);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false){
			if($newdo['audit_status'] == 1){//增加进度
				$recordModel = new AppProcessorRecordModel($record_id,14);
				$recordModel->setValue('check_status',3);//审批中

				$audit_plan = ceil((1/$user_sum)*100);
				$sql = "UPDATE `app_processor_record` SET `audit_plan`=if(`audit_plan`+".$audit_plan.">=100,100,`audit_plan`+".$audit_plan.") WHERE id =".$record_id;
				$res = DB::cn(14)->db()->query($sql);
				if($res){
					//如果是最后一个审核
					if(count($user)==$order){
						$recordModel->setValue('check_status',7);//审批通过
						$res = $recordModel->toInfo();
						if($res){
							/*-----更新审核人-----*/
							$recordModel->setValue('check_user','0');
							$recordModel->save(true);
							/*----更新审核人END------*/
							echo "1";
						}else{
							$sql = "DELETE FROM `app_processor_audit` WHERE `record_id` = '".$record_id."' AND `user_id` = '".$_SESSION['userId']."'";
							DB::cn(14)->db()->query($sql);
							echo "4";exit;
						}
					}else{
						/*-----更新审核人-----*/
						$new_user=isset($user[($order)]['user_id'])?$user[($order)]['user_id']:0; 
						$recordModel->setValue('check_user',$new_user);
						$recordModel->setValue('check_status',3);
						$recordModel->save(true);
						/*----更新审核人END------*/
						echo "1";
					}
				}
			}else{
				$recordModel->setValue('check_status',4);//审批驳回
				/*-----更新审核人-----*/
				$new_user=isset($user[($order+1)]['user_id'])?$user[($order+1)]['user_id']:0;
				$recordModel->setValue('check_user',$new_user);
				$recordModel->save(true);
				/*----更新审核人END------*/
				echo "2";//审批驳回
			}
			$recordModel->save(true);
		}else{
			echo "4";//操作失败
		}

	}







}

?>