<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseIqcOpraController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-18 18:31:46
 *   @update	:
 *	IQC质检操作
 *  -------------------------------------------------
 */
class PurchaseIqcOpraController extends Controller
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('purchase_receipt','purchase',23);	//生成模型后请注释该行
		//Util::V('purchase_receipt',23);	//生成视图后请注释该行
		//$this->render('purchase_iqc_opra_form.html',array('bar'=>Auth::getBar()));
	}
	/**
	 *	add，渲染添加页面
	 */
	public function add ($params)
	{
		$id = $params['id'];
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('purchase_iqc_opra_info.html',array(
			//'view'=>new PurchaseIqcOpraView(new PurchaseIqcOpraModel(1))
			'dd'  => new DictView(new DictModel(1)),
			'rece_detail_id' => $id
		));
		$result['title'] = '质检操作';
		Util::jsonExit($result);
	}


	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$opra_code	= _Post::getInt('opra_code');
		$opra_info	= _Post::get('opra_info');
		$rece_detail_id = _Post::get('rece_detail_id');

		if(!$opra_code)
		{
			
			$result['error'] = '质检操作必选';
			Util::jsonExit($result);
		}

		$olddo = array();
		$newdo=array(
			'rece_detail_id'	=> $rece_detail_id,
			'opra_code'	=> $opra_code,
			'opra_info'	=> $opra_info,
			'opra_uname'=> $_SESSION['userName'],
			'opra_time' => date('Y-m-d H:i:s')
		);

		$newmodel =  new PurchaseIqcOpraModel(24);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			//质检操作id => 对应的货品状态
			$s = array(
				'1' => '4',
				'2' => '2',
				'3' => '5'	
			);
			$model = new PurchaseReceiptDetailModel($rece_detail_id,24);
			$model->setValue('status',$s[$opra_code]);
			if($model->save())
			{
				$result['success'] = 1;
				//记录流水日志
				$logModel = new PurchaseLogModel(24);
				$dd = new DictModel(1);
				$remark = $opra_info?$opra_info:"无";
				$opra_act = $dd->getEnum('iqc_opra',$opra_code);
				$logModel->addLog($rece_detail_id,$s[$opra_code],"IQC质检操作：".$opra_act."，备注：".$remark);
			}
		}
		else
		{
			$result['error'] = '操作失败';
		}
		Util::jsonExit($result);
	}

}

?>