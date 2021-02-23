<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductIqcOpraController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-12 15:13:13
 *   @update	:
 *  -------------------------------------------------
 */
class ProductIqcOpraController extends Controller
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('product_iqc_opra','front',13);	//生成模型后请注释该行
		//Util::V('product_iqc_opra',13);	//生成视图后请注释该行
		//$this->render('product_iqc_opra_search_form.html');
	}


	/**
	 *	add，渲染添加页面
	 */
	public function add ($params)
	{
		$id = $params['id'];//出货单ID
		$shmtModel = new ProductShipmentModel($id,13);
		$shipment_number = $shmtModel->getValue('shipment_number');
		$shmt_num = $shmtModel->getValue('num');
		if($shmtModel->getValue('iqc_status'))
		{
			$result['title'] = "IQC质检";
			$result['content'] = "此出货单已经质检完成，请检查后再操作。";
			Util::jsonExit($result);
		}

		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('product_iqc_opra_info.html',array(
			'id'=>$id
		));
		$result['title'] = 'IQC质检（出货单号：'.$shipment_number.'&nbsp;&nbsp;出货数量：'.$shmt_num.'）';
		Util::jsonExit($result);
	}

	
	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		die('开发中');
		$id = intval($params["id"]);
		$this->render('product_iqc_opra_show.html',array(
			'view'=>new ProductIqcOpraView(new ProductIqcOpraModel($id,1))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$id			= intval($params["id"]);//出货单ID
		$sj_num		= intval($params['sj_num']);
		$bf_num		= intval($params['bf_num']);
		$iqc_num	= intval($params['iqc_num']);
		$info		= $params['info'];
		
		
		$shmt_model = new ProductShipmentModel($id,14);
		$shmt_num = $shmt_model->getValue('num');//出货单数量
		if($shmt_model->getValue('iqc_status'))
		{
			$result['error'] = "此出货单已经质检完成，请检查后再操作。";
			Util::jsonExit($result);
		}

		//实际交货数量 + 报废数量 + iqc未过数量 等于 IQC的出货单总数量	
		$sum_num = $sj_num + $bf_num + $iqc_num;
		if($sum_num != $shmt_num)
		{
			$result['error'] = "质检数量和出货单数量不符";
			Util::jsonExit($result);
		}

		$model = new ProductIqcOpraModel(14);
		$olddo = array();
		$newdo=array(
			"shipment_id"	=> $id,
			"sj_num"		=> $sj_num,
			"bf_num"		=> $bf_num,
			"iqc_num"		=> $iqc_num,
			"info"			=> $info,
			"opra_uid"		=> $_SESSION['userId'],
			"opra_uname"	=> $_SESSION['userName'],
			"opra_time"		=> date("Y-m-d H:i:s")
		);
		$res = $model->saveData($newdo,$olddo);

		if($res !== false){
			//出货单的IQC质检状态为已质检

			$shmt_model->setValue('iqc_status',1);
			$shmt_model->save();
			
			$bc_id = $shmt_model->getValue('bc_id');//布产单ID

			//IQC质检的布产单的各种总数量
			$sum = $model->getSumNumOfBcid($bc_id);

			//实际出厂数量 = 实际接货数量 + 报废数量
			$z_sum = intval($sum['sj_num']) + intval($sum['bf_num']);

			$infoModel = new ProductInfoModel($bc_id,14);
			$info_num = $infoModel->getValue('num');
			if((0 < $z_sum) && ($z_sum < intval($info_num)))
			{
				//如果布产单的实际出厂数量 大于 0  小于 布产单总数量  布产单状态为 部分出厂
				$infoModel->setValue('status',7);
				$infoModel->save();
			}elseif($z_sum == intval($info_num)){
				//实际出厂数量和布产总数相等  布产单状态为 已出厂
				$infoModel->setValue('status',9);
				$infoModel->setValue('rece_time',date("Y-m-d H:i:s"));
				$infoModel->save();
			}else{
				$result['error'] = "实际交货数量有异常，请联系技术人员";
				Util::jsonExit($result);
			}

			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}

}

?>