<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderComplaintController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2016-02-27 14:29:07
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderComplaintController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('search');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $model = new AppOrderComplaintModel(27);
        $feedbackInfo = $model->getFeedbackInfo();
		$this->render('app_order_complaint_search_form.html',array('bar'=>Auth::getBar(),'feedbackInfo'=>$feedbackInfo));
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
            'order_sn'   => _Request::get("order_sn"),
            'cl_user'   => _Request::get("cl_user"),
            'cl_feedback_id'   => _Request::get("cl_feedback_id"),
            'cl_time_start'   => _Request::get("cl_time_start"),
            'cl_time_end'   => _Request::get("cl_time_end"),
            'cl_other'   => _Request::get("cl_other"),
            'down_infos'   => _Request::get("down_infos")
		);
		$page = _Request::getInt("page",1);
		$where = array(
            'order_sn' => $args['order_sn'],
            'cl_user' => $args['cl_user'],
            'cl_feedback_id' => $args['cl_feedback_id'],
            'cl_time_start' => $args['cl_time_start'],
            'cl_time_end' => $args['cl_time_end'],
            'cl_other' => $args['cl_other'],
            );

		$model = new AppOrderComplaintModel(27);

        // 数据导出
        if($args['down_infos'] == 'downs')
        {

            $data = $model->pageList_down($where,$page,90000000,false);

            $productModel = new ProductInfoModel(53);
            $detailsModel = new AppOrderDetailsModel(27);
            if(!empty($data)){

                $bc_info = array(2, 3, 4);//已布产的订单才查询
                foreach ($data['data'] as $key => $value) {
                    # code... 取出布产信息
                    $data['data'][$key]['bc_sn'] = '';
                    $data['data'][$key]['bc_style'] = '';
                    $data['data'][$key]['prc_name'] = '';
                    $data['data'][$key]['order_time'] = '';
                    $data['data'][$key]['esmt_time'] = '';
                    if(in_array($value['buchan_status'], $bc_info)){

                        //布产明细
                        $bcInfos = $productModel->getBcSnByOrderSn($value['order_sn']);
                        if(!empty($bcInfos)){

                            $data['data'][$key]['bc_sn'] = implode('|', array_column($bcInfos, 'bc_sn'));
                            $data['data'][$key]['bc_style'] = implode('|', array_column($bcInfos, 'bc_style'));
                            $data['data'][$key]['prc_name'] = implode('|', array_column($bcInfos, 'prc_name'));
                            $data['data'][$key]['order_time'] = implode('|', array_column($bcInfos, 'order_time'));
                            $data['data'][$key]['esmt_time'] = implode('|', array_column($bcInfos, 'esmt_time'));
                        }
                    }

                    //订单明细
                    $goodsInfo = $detailsModel->getDetailsInfoByOrderSn($value['id']);

                    $data['data'][$key]['style_sn'] = '';
                    $data['data'][$key]['num'] = '0';

                    if(!empty($goodsInfo)){

                        $data['data'][$key]['style_sn'] = implode('|', array_column($goodsInfo['info'], 'goods_sn'));
                        $data['data'][$key]['num'] = $goodsInfo['num'];
                    }
                }
            }

            $this->downComplaints($data);
            exit;
        }

		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_order_complaint_search_page';
		$this->render('app_order_complaint_search_list.html',array(
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
		$result['content'] = $this->fetch('app_order_complaint_info.html',array(
			'view'=>new AppOrderComplaintView(new AppOrderComplaintModel(27))
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
		$result['content'] = $this->fetch('app_order_complaint_info.html',array(
			'view'=>new AppOrderComplaintView(new AppOrderComplaintModel($id,27)),
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
		$this->render('app_order_complaint_show.html',array(
			'view'=>new AppOrderComplaintView(new AppOrderComplaintModel($id,27)),
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

		$newmodel =  new AppOrderComplaintModel(28);
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

		$newmodel =  new AppOrderComplaintModel($id,28);

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
		$model = new AppOrderComplaintModel($id,28);
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
     *  downComplaints，导出
     */
    public function downComplaints($data='')
    {
        # code...
        if(!empty($data['data'])){
//订单号   布产单号    布产类型    客户姓名    款号  销售渠道    客户来源    线上/线下   客诉原因    客诉备注    客诉图片    客诉操作人   客诉操作时间  数量  跟单人 工厂名称    工厂接单时间  标准出厂时间  制单人
            $xls_content = "订单号,布产单号,布产类型,客户姓名,款号,销售渠道,客户来源,线上/线下,客诉原因,客诉备注,客诉操作人,客诉操作时间,数量,跟单人,工厂名称,工厂接单时间,标准出厂时间,制单人\r\n";
            foreach ($data['data'] as $key => $val) {
                # code...
                $xls_content .= $val['order_sn']. ",";
                $xls_content .= $val['bc_sn']. ",";
                $xls_content .= $val['bc_style']. ",";
                $xls_content .= $val['consignee']. ",";
                $xls_content .= $val['style_sn']. ",";
                $xls_content .= $val['channel_name']. ",";
                $xls_content .= $val['source_name']. ",";
                $xls_content .= $val['source_class']. ",";
                $xls_content .= $val['ks_option']. ",";
                $xls_content .= $val['cl_other']. ",";
                $xls_content .= $val['cl_user']. ",";
                $xls_content .= $val['cl_time']. ",";
                $xls_content .= $val['num']. ",";
                $xls_content .= $val['genzong']. ",";
                $xls_content .= $val['prc_name']. ",";
                $xls_content .= $val['order_time']. ",";
                $xls_content .= $val['esmt_time']. ",";
                $xls_content .= $val['create_user']. "\n";
            }
        }else{
            $xis_content = "没有数据！";
        }
        header("Content-type:text/csv;charset=gbk");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo iconv("utf-8", "gbk//IGNORE", $xls_content);
        exit;
    }
}

?>