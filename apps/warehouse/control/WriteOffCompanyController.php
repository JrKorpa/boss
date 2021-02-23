<?php
/**
 *  -------------------------------------------------
 *   @file		: WriteOffCompanyController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-06-23 16:39:11
 *   @update	:
 *  -------------------------------------------------
 */
class WriteOffCompanyController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
                $payMentModel = new PaymentModel(1);
                $pay_type = array_column($payMentModel->getAll(),'pay_name','id');
                //var_dump($pay_type);exit;
		$this->render('write_off_company_search_form.html',array(
                    'bar'=>Auth::getBar(),
                    'pay_type'=>$pay_type
                ));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
                        'pay_type_id' => _Request::get("pay_type"),
                        'company_id'  => _Request::get("company_id")
			//'参数' = _Request::get("参数");


		);
		$page = _Request::getInt("page",1);
		$model = new WriteOffCompanyModel(21);
                
		$data = $model->pageList($args,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'write_off_company_search_page';
		$this->render('write_off_company_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{       
		$result = array('success' => 0,'error' =>'');
                //var_dump($params);exit;
		$where = array(
                    //'id' => $params['id'],
                    'pay_type_id' => $params['type'],
                    'pay_type'=> $params['type_name'],
                    'company_id' => $params['company_id'],
                    'company' => $params['company']
                );
		$newmodel =  new WriteOffCompanyModel(22);
		$res = $newmodel->SavePayTypeData($where);
		if($res !== false)
		{
			$result['success'] = 1;
                        $result['info'] = "添加成功！";
		}
		else
		{
			$result['error'] = '不能重复添加，添加失败';
		}
               
		Util::jsonExit($result);
	}

	

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new WriteOffCompanyModel($id,22);
		$res = $model->delPayTypeData($id);
		
		
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>