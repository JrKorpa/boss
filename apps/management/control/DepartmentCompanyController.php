<?php
/**
 *  -------------------------------------------------
 *   @file		: CompanydepartmentController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 10:52:00
 *   @update	:
 *  -------------------------------------------------
 */
class DepartmentCompanyController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{

		//var_dump(Auth::getBar('COMPANY_DEPARTMENT_M'));
		$this->render('department_company_search_form.html',array('bar'=>Auth::getBar()));
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
			'company_id'=>_Request::get("company_id"),
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['dept_id']='';
		$where['company_id']=$args['company_id'];
		$model = new CompanyDepartmentModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'companyde_partment_search_page';
		$this->render('department_company_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$company_id = _Post::get('company_id');

		$where['dept_id']='';
		$where['company_id']=$company_id;
		$model = new CompanyDepartmentModel(1);
		$inGather = $model->idGather($where);


		$model = new DepartmentModel(1);
		$tree = $model->getList();

		foreach($tree as $key=>$val){
			if(strpos($inGather,$val['id'])===false){
				$tree[$key]['disable']=0;
			}else{
				//$tree[$key]['disable']=1;
				unset($tree[$key]);
			}
		}
		$result['content'] = $this->fetch('department_company_info.html',array('company_id'=>$company_id,'ctree'=>$tree));
		$result['title'] = '部门添加到公司';
		Util::jsonExit($result);
	}




	/**
	 *	insert，信息入库
	 */
	/*这里没有对非正常提交的过滤 待修该*/
	public function insert ($params)
	{

		$result = array('success' => 0,'error' =>'');
		$department_arr = $_POST['department_id'];
		$company_id =_Post::get('company_id');

		$newmodel =  new CompanyModel(2);
		$res = $newmodel->getCompanyexists($company_id);

		if($res===false){
			$result['error']="没有选择一个合理的公司";
			Util::jsonExit($result);
		}

		if(!$department_arr)
		{
			$result['error'] = "请选择部门";
			Util::jsonExit($result);
		}
		$arr = array();
		/*在这里对非正常提交的过滤*/
/*		$newmodeld= new DepartmentModel(2);*/
		foreach ($department_arr as $key => $val )
		{
			$arr[$key]['company_id'] = $company_id;
/*			$resu = $newmodeld->getDepartmentExists($val,$company_id);
			if($resu===false){
				$result['error'] = '部门司已在公司门或者部门司不存在';
				Util::jsonExit($result);
			}*/
			$arr[$key]['dep_id'] = $val;
		};


		try{
			$newmodel->insertAll($arr,'company_department');
		}
		catch(Exception $e)
		{
			$result['error'] = '添加失败';
			Util::jsonExit($result);
		}
		$result['success'] = 1;
		Util::jsonExit($result);

	}

	/**
	 *	update，更新信息
	 */
	/*	public function update ($params)
        {
            $result = array('success' => 0,'error' =>'');
            $id = _Post::getInt('id');
            echo '<pre>';
            print_r ($_POST);
            echo '</pre>';
            exit;

            $newmodel =  new CompanyDepartmentModel($id,2);

            $olddo = $newmodel->getDataObject();
            $newdo=array(
            );

            $res = $newmodel->saveData($newdo,$olddo);
            if($res !== false)
            {
                $result['success'] = 1;
            }
            else
            {
                $result['error'] = '修改失败';
            }
            Util::jsonExit($result);
        }*/

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);

		$model = new CompanyDepartmentModel($id,2);

		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
			
			//日志记录
			$dataLog['pkdata'] = array('id'=>$id);
			$this->operationLog("delete",$dataLog);
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	public function  companyList(){
		$model = new CompanyModel(1);
		$data = $model->getList();
		Util::jsonExit($data);
	}

}

?>