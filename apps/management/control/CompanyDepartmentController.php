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
class CompanyDepartmentController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{

		//var_dump(Auth::getBar('COMPANY_DEPARTMENT_M'));
		$this->render('company_department_search_form.html',array('bar'=>Auth::getBar()));
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
			'dept_id'=>_Request::get("department_id"),
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['dept_id']=$args['dept_id'];
		$where['company_id'] = '';
		$model = new CompanyDepartmentModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'company_department_search_page';
		$this->render('company_department_search_list.html',array(
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
		$dept_id = _Post::get('department_id');

		$where['dept_id']=$dept_id;
		$where['company_id']='';
		$model = new CompanyDepartmentModel(1);
		$inGather = $model->idGather($where);

		$model = new CompanyModel(1);
		$tree = $model->getCompanyTree();

		foreach($tree as $key=>$val){
			if(strpos($inGather,$val['id'])===false){
				$tree[$key]['disable']=0;
			}else{
				//$tree[$key]['disable']=1;
				unset($tree[$key]);
			}
		}

		$result['content'] = $this->fetch('company_department_info.html',array('dept_id'=>$dept_id,'ctree'=>$tree));
		$result['title'] = '公司添加到部门';
		Util::jsonExit($result);
	}




	/**
	 *	insert，信息入库
	 */

	/*这里缺少对非正常提交的校验*/
	public function insert ($params)
	{

		$result = array('success' => 0,'error' =>'');
		$company_arr = $_POST['company_id'];
		$dept_id =_Post::get('department_id');
		$newmodel =  new DepartmentModel(2);
		$res = $newmodel->getDepartmentexist($dept_id);
		if($res===false){
			$result['error']="没有选择一个合理的部门";
			Util::jsonExit($result);
		}

		if(!$company_arr)
		{
			$result['error'] = "请选择公司";
			Util::jsonExit($result);
		}
		$arr = array();
		$newmodeld= new CompanyModel(2);
		foreach ($company_arr as $key => $val )
		{
			$arr[$key]['dep_id'] = $dept_id;
			/*非正常提交的校验 待修改*/
/*			$resu = $newmodeld->getCompanyExist($val,$dept_id);
			if($resu===false){
				$result['error'] = '该公司已在该部门或者该公司不存在';
				Util::jsonExit($result);
			}*/
			$arr[$key]['company_id'] = $val;
		}

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
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new CompanyDepartmentModel($id,2);
/*		print_r($id);
		exit;*/
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

	public function  departmentList(){
		$model = new DepartmentModel(1);
		$data = $model->getList();
		Util::jsonExit($data);
	}

}

?>