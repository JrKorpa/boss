<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseOrderInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-06-01 19:31:40
 *   @update	:
 *  -------------------------------------------------
 */
class ModfiyOrderInfoController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('downLoad');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $hbhModel = new BaseHunbohuiInfoModel(1);
        $department = 0;
        if($_SESSION['userType']!=1){
            $department = $_SESSION['qudao'];
        }
        $salesChannel = $hbhModel->getDepartmentInfoqc($department);
		$this->render('modfiy_order_info_search_form.html',array('bar'=>Auth::getBar(),'salesChannel'=>$salesChannel));
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
			//'参数' = _Request::get("参数");
            'order_department'=>  _Request::getInt('order_department'),
            'order_sn'=>  _Request::getString('order_sn'),
            'create_user'=>  _Request::getString('create_user'),
            'genzong'=>  _Request::getString('genzong'),
            'hunbohui'=>  _Request::getInt('hunbohui'),
            'type'=>  _Request::getInt('type'),
            'goods_type'=>  _Request::getInt('goods_type'),

		);
        if($args['order_department']=='' || $args['hunbohui']==''){
            exit('验店部门和婚博会搜索条件不能为空！');
        }
		$page = _Request::getInt("page",1);
        if($args['hunbohui']){
            $arr = array();
            $hbhModel = new BaseHunbohuiInfoModel(1);
            $arr = $hbhModel->getHbhInfo($args['hunbohui']);
        }
        
		$where = array(
            'department'=>  $args['order_department'],
            'order_sn'=>  $args['order_sn'],
            'type'=>  $args['type'],
            'goods_type'=>  $args['goods_type'],
            'create_user'=>  $args['create_user'],
            'genzong'=>  $args['genzong']
        );
        if(!empty($arr)){
            $where['start_time'] = $arr['active_start_time'];
            $where['end_time'] = $arr['active_end_time'];
        }
		$model = new BaseOrderInfoModel(27);
		$data = $model->getHbhOrderList($where,$page,10,false);
        
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'modfiy_order_info_search_page';
		$this->render('modfiy_order_info_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}
   
	/**
	 *	downLoad，导出
	 */
	public function downLoad ()
	{
        set_time_limit(0);
        ini_set('memory_limit','2000M');
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");
            'department'=>  _Request::getInt('order_department'),
            'order_sn'=>  _Request::getString('order_sn'),
            'hunbohui'=>  _Request::getInt('hunbohui'),
            'type'=>  _Request::getInt('type'),
            'goods_type'=>  _Request::getInt('goods_type'),

		);
        if($args['department']=='' || $args['hunbohui']==''){
            exit('验店部门和婚博会搜索条件不能为空！');
        }
        if($args['hunbohui']){
            $arr = array();
            $hbhModel = new BaseHunbohuiInfoModel(1);
            $arr = $hbhModel->getHbhInfo($args['hunbohui']);
        }
        
		$where = array(
            'department'=>  $args['department'],
            'order_sn'=>  $args['order_sn'],
            'type'=>  $args['type'],
            'goods_type'=>  $args['goods_type'],
        );
        if(!empty($arr)){
            $where['start_time'] = $arr['active_start_time'];
            $where['end_time'] = $arr['active_end_time'];
        }
		$model = new BaseOrderInfoModel(27);
		$data = $model->getHbhOrderListdownLoad($where);

        //}
        $title = array(
				'订单号',
				'制单人',
				'跟单人');
            
        $this->groupdownload($data);
	}    

    /**
     * 批量分配跟单人
     * @param type $param
     */
    function batch_gendan($param) {
        $result = array('success' => 0,'error' => '');
        $ids = _Post::getList('_ids');
        $_ids = implode(',', $ids);
        $orderModel = new BaseOrderInfoModel(current($ids),27);
        $department_id = $orderModel->getValue('department_id');
        $model = new UserChannelModel(1);
        $make_order = $model->get_channels_person_by_channel_id($department_id);
        if($make_order['dp_people']=='' || $make_order['dp_people_name']==''){
            //die(1);
            $make_order = $model->get_user_channel_by_channel_id($olddo['department_id']);
        }else{
            //$dp_people = explode(",",$make_order['dp_people']);
            $dp_people_name = explode(",",$make_order['dp_people_name']);
            $dp_people_name = array_filter($dp_people_name);
            $dp_leader_name = explode(",",$make_order['dp_leader_name']);
            $dp_leader_name = array_filter($dp_leader_name);
            $make_order=array();
            foreach($dp_people_name as $k=>$v){
                $make_order[]['account']=$v;
            }
            foreach($dp_leader_name as $k=>$v){
                $make_order[]['account']=$v;
            }
        }
        $result['content'] = $this->fetch('modfiy_order_info_info.html',array(
			'make_order'=>$make_order,'batch_ids'=>$_ids
		));
        Util::jsonExit($result);
    }
    
    
    /**
     * 处理批量分配跟单人
     * @param type $param
     */
    function batch_update($param){
        $result = array('success' => 0,'error' =>'');
		$_ids = _Request::getString('batch_ids');
		$make_order = _Request::getString('make_order');
        if(empty($make_order)){
            $result['error'] = '请选择跟单人';
            Util::jsonExit($result);
        }
        $ids = explode(',', $_ids);
        if(!empty($ids)){
            foreach($ids as $val){
                $newdo=array();
                $newdo['id']=$val;
                $newdo['genzong']=$make_order;
                $newModel = new BaseOrderInfoModel($val,28);
                $olddo = $newModel->getDataObject();
                if($olddo){
                    $res = $newModel->saveData($newdo,$olddo);
                    //添加日志
                    $insert_action = array();
                    $insert_action ['order_id'] = $olddo ['id'];
                    $insert_action ['order_status'] = $olddo ['order_status'];
                    $insert_action ['shipping_status'] = $olddo ['send_good_status'];
                    $insert_action ['pay_status'] = $olddo ['order_pay_status'];
                    $logs_content = $olddo['order_sn'].'分配跟单人为：'.$make_order;
                    $insert_action ['remark'] = '<font color="red">'.$logs_content.'</font>';
                    $insert_action ['create_user'] = $_SESSION ['userName'];
                    $insert_action ['create_time'] = date ( 'Y-m-d H:i:s' );
                    $orderModel= new BaseOrderInfoModel(28);
                    $res = $orderModel->addOrderAction($insert_action);
                }
            }
        }		

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
     * 页面联动数据获取
     * @param type $param
     */
	function getHbhInfo($param) {
        $department = intval($param['department']);
        $model = new BaseHunbohuiInfoModel(1);
        $data = $model->getHbhInfoList($department);
        $str = '<option></opiont>';
        if(!empty($data)){
            foreach ($data as $val){
                $str .= "<option value='".$val['id']."'>".$val['name']."(".$val['start_time']." ".$val['end_time'].")</option>";
            }
        }
        echo $str;
    }

	//导出
	function groupdownload($data) {

		
		if ($data['data']) {
			$down = $data['data'];
			$xls_content = "订单号,制单人,跟单人\r\n";
			foreach ($down as $key => $val) {
				$xls_content .= $val['order_sn']. ",";
				$xls_content .= $val['create_user']. ",";
				$xls_content .= $val['genzong']. ",";
				$xls_content .= "\n";
			}
		} else {
			$xls_content = '没有数据！';
		}
                header("Content-type:text/csv;charset=gbk");
                header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "婚博会订单" . date("Y-m-d")) . ".csv");
                header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
                header('Expires:0');
                header('Pragma:public');
                echo $xls_content;

                exit;

	}
}

?>