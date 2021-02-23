<?php
/**
 *  -------------------------------------------------
 *   @file		: DiaQueryController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-03-22 11:54:36
 *   @update	:
 *  -------------------------------------------------
 */
class DiaQueryController extends CommonController
{
    protected $whitelist = array('search');
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $model_p = new ApiProcessorModel();
        $pro_list = $model_p->GetSupplierList();//调用加工商接口
		$this->render('dia_query_search_form.html',array('bar'=>Auth::getBar(),'pro_list'=>$pro_list));
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
            'dia_package' => _Request::get("dia_package"),
            'status' => _Request::get("status"),
            'sup_id' => _Request::get("processors_id"),
            'down_info' => _Request::get("down_info")
			//'参数' = _Request::get("参数");


		);
        if($args['dia_package']){
            $dia_package = explode(' ', $args['dia_package']);
        }
        $sup_id = '';
        if($args['sup_id'] != ''){
            $suparr = explode('|', $args['sup_id']);
            $sup_id = $suparr[0];
        }
		$page = _Request::getInt("page",1);
		$where = array(
            'dia_package' => $dia_package,
            'status' => $args['status'],
            'sup_id' => $sup_id
            );
		$model = new DiaQueryModel(45);
        if($args['down_info'] == 'down_info'){
            $data = $model->pageList($where,$page,90000000,false);
            $this->download($data);
            exit;
        }
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'dia_query_search_page';
		$this->render('dia_query_search_list.html',array(
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
		$result['content'] = $this->fetch('dia_query_info.html',array(
			'view'=>new DiaQueryView(new DiaQueryModel(45))
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
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('dia_query_info.html',array(
			'view'=>new DiaQueryView(new DiaQueryModel($id,45)),
			'tab_id'=>$tab_id
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('dia_query_show.html',array(
			'view'=>new DiaQueryView(new DiaQueryModel($id,45)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;
		$olddo = array();
		$newdo=array();

		$newmodel =  new DiaQueryModel(46);
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
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new DiaQueryModel($id,46);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '修改此处为想显示在页签上的字段';
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
		$model = new DiaQueryModel($id,46);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

    /**
     *  loseEfficacy 失效
     */
    public function loseEfficacy ($params)
    {
        $id = intval($params["id"]);
        $tab_id = _Request::getInt("tab_id");
        $result = array('success' => 0,'error' => '');
        $result['content'] = $this->fetch('lose_efficacy.html',array(
            'view'=>new DiaQueryView(new DiaQueryModel($id,45)),
            'tab_id'=>$tab_id
        ));
        $result['title'] = '提示信息';
        Util::jsonExit($result);
    }

    /**
     *  loseEfficacyExecute 失效
     */
    public function loseEfficacyExecute ($params)
    {
        $result = array('success' => 0,'error' =>'');
        $_cls = _Post::getInt('_cls');
        $tab_id = _Post::getInt('tab_id');
        $lose_efficacy = _Post::get('lose_efficacy');

        $id = _Post::getInt('id');
        //echo '<pre>';
        //print_r ($_POST);
        //echo '</pre>';
        //exit;

        $newmodel =  new DiaQueryModel($id,46);

        $olddo = $newmodel->getDataObject();

        if($olddo['status'] == 2){
            $result['error'] = "石包已失效";
            Util::jsonExit($result);
        }
        $newdo=array(
            'id'=>$id,
            'lose_efficacy_cause'=>$lose_efficacy,
            'lose_efficacy_time' =>date("Y-m-d H:i:s"),
            'lose_efficacy_user' =>$_SESSION['userName'],
            'status' =>2
        );

        $res = $newmodel->saveData($newdo,$olddo);
        if($res !== false)
        {
            $result['success'] = 1;
            $result['_cls'] = $_cls;
            $result['tab_id'] = $tab_id;    
            $result['title'] = '修改此处为想显示在页签上的字段';
        }
        else
        {
            $result['error'] = '失效失败';
        }
        Util::jsonExit($result);
    }

    //导出
    public function download($data) {
        if ($data['data']) {
            $down = $data['data'];
            $xls_content = "序号,石包,每卡采购价格（元）,供应商,规格,颜色,净度,切工,对称,抛光,荧光,状态,失效日期,失效原因,失效操作人\r\n";
            foreach ($down as $key => $val) {
                $xls_content .= $val['id'] . ",";
                $xls_content .= $val['dia_package'] . ",";
                $xls_content .= $val['purchase_price'] . ",";
                $xls_content .= $val['sup_name'] . ",";
                $xls_content .= $val['specification'] . ",";
                $xls_content .= $val['color'] . ",";
                $xls_content .= $val['neatness'] . ",";
                $xls_content .= $val['cut'] . ",";
                $xls_content .= $val['symmetry']. ",";
                $xls_content .= $val['polishing'] . ",";
                $xls_content .= $val['fluorescence'] . ",";
                $xls_content .= $val['status'] == 1 ? '有效':'无效' . ",";
                $xls_content .= $val['lose_efficacy_time'] . ",";
                $xls_content .= $val['lose_efficacy_cause'] . ",";
                $xls_content .= $val['lose_efficacy_user'] . "\n";
            }
        } else {
            $xls_content = '没有数据！';
        }

        header("Content-type: text/html; charset=gbk");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
        echo iconv("utf-8", "GB18030", $xls_content);
    }
}

?>