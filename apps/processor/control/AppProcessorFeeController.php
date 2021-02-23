<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorFeeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 21:17:58
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorFeeController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('app_processor_fee','front',13);	//生成模型后请注释该行
		//Util::V('app_processor_fee',13);	//生成视图后请注释该行
		$this->render('app_processor_fee_search_form.html',array('bar'=> Auth::getBar()));
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
                        'processor_id'=> _Request::getInt('_id')

		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
                $where['processor_id'] = $args['processor_id'];

		$model = new AppProcessorFeeModel(13);
		$data = $model->pageList($where,$page,10,false);
        if($data['data']){
            $processorInfoModel = new AppProcessorInfoModel(13);
            foreach ($data['data'] as &$val) {
                $val['processor_name'] = $processorInfoModel->getProcessorName($val['processor_id']);
            }
            unset($val);
        }
        $pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_processor_fee_search_page';
		$this->render('app_processor_fee_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
                $processor_id = _Request::getInt('_id');
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_processor_fee_info.html',array(
			'view'=>new AppProcessorFeeView(new AppProcessorFeeModel(13)),
                        'processor_id'=>$processor_id
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_processor_fee_info.html',array(
			'view'=>new AppProcessorFeeView(new AppProcessorFeeModel($id,13))
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		die('开发中');
		$id = intval($params["id"]);
		$this->render('app_processor_fee_show.html',array(
			'view'=>new AppProcessorFeeView(new AppProcessorFeeModel($id,1))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$olddo = array();
		$newdo=array();
		$newdo['processor_id']=_Request::getInt('processor_id');
		$newdo['fee_type']=_Request::getInt('fee_type');
		$newdo['price']=_Request::getFloat('price');
		$newdo['status']=_Request::getInt('status');
		$newdo['check_user']=$_SESSION['userName'];
		$newdo['check_time']=date("Y-m-d H:i:s");
        $comodel = new AppProcessorFeeModel(13);
        $oldres = $comodel->getProcessorOldInfo($newdo);
        if(count($oldres) > 0){

            $result['error'] = '错误，重复添加！';
            Util::jsonExit($result);
        }
		$newmodel =  new AppProcessorFeeModel(14);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');

		$newmodel =  new AppProcessorFeeModel($id,14);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
		);
		$newdo['id']=_Request::getString('id');
		$newdo['processor_id']=_Request::getString('processor_id');
		$newdo['fee_type']=_Request::getString('fee_type');
		$newdo['price']=_Request::getString('price');
		$newdo['status']=_Request::getInt('status');
		$newdo['check_user']=$_SESSION['userName'];

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
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppProcessorFeeModel($id,14);
                $do = $model->getDataObject();
                if($do['status'] == 2){
                    $result['error'] = "停用记录不可操作";
                    Util::jsonExit($result);
                }
		$model->setValue('status',2);
                $model->setValue('cancel_time', date("Y-m-d H:i:s"));
                $res = $model->save(TRUE);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>