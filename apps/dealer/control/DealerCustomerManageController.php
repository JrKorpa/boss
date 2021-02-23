<?php
/**
 *  -------------------------------------------------
 *   @file		: DealerCustomerManageController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangzhimin
 *   @date		: 2015-12-15 11:51:21
 *   @update	:
 *  -------------------------------------------------
 */
class DealerCustomerManageController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('search', 'dow');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $user_model = new RoleUserModel(1);
		$this->render('dealer_customer_manage_search_form.html',array(
            'bar'=>Auth::getBar(),'source'=>$this->getHistorySource(),
            'source_channel'=>$this->getHistorySourceChannel(),
            'spread'=>$user_model->getRoleUserList(33),
            'text_item'=>$this->getTextItem()
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
			'customer_name' => _Request::getString("customer_name"),
			'tel' => _Request::getString("tel"),
			'email' => _Request::getString("email"),
			'district' => _Request::getString("district"),
			'follower' => _Request::getString("follower"),
			'source_channel' => _Request::getList("source_channel"),
			'status' => _Request::getString("status"),
			'source' => _Request::getList("source"),
            'spread_id' => _Request::getList("spread_id"),
			'start_time' => _Request::getString("start_time"),
			'end_time' => _Request::getString("end_time"),
            'down_infos' => _Request::getString("down_infos"),
            'province' => _Request::getString("province"),
            'city' => _Request::getString("city"),
            'text_item' => _Request::getList("text_item")
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['customer_name'] = $args['customer_name'];
		$where['tel'] = $args['tel'];
		$where['email'] = $args['email'];
		$where['district'] = $args['district'];
        $where['province'] = $args['province'];
        $where['city'] = $args['city'];
		$where['follower'] = $args['follower'];
		$where['source_channel'] = $args['source_channel'];
		$where['status'] = $args['status'];
		$where['source'] = $args['source'];
        $where['spread_id'] = $args['spread_id'];
		$where['start_time'] = $args['start_time'];
		$where['end_time'] = $args['end_time'];
        $where['text_item'] = $args['text_item'];
        $model = new DealerCustomerManageModel(15);
        if($args['down_infos'] == 'downs'){
            $where['source_channel'] = _Request::getString("source_channel") != 'null'?explode(",", _Request::getString("source_channel")) : '';
            $where['source'] = _Request::getString("source") != 'null'?explode(",", _Request::getString("source")):'';
            $where['spread_id'] = _Request::getString("spread_id") != 'null'?explode(",", _Request::getString("spread_id")):'';
            $where['text_item'] = _Request::getString("text_item") != 'null'?explode(",", _Request::getString("text_item")):'';
            $data = $model->pageList($where,$page,999999999,false);
            //echo '<pre>';
            //print_r($data);die;
        }else{
            $data = $model->pageList($where,$page,50,false);
        }
		$user_model = new RoleUserModel(1);
		$userData = $user_model->getRoleUserList(31);
		$array_column = array_column($userData,'account','uid');
        $userDatas = $user_model->getRoleUserList(33);
        $array_columns = array_column($userDatas,'account','uid');
		foreach($data['data'] as $k=>$r){
			$ex = explode(",",$r['follow_upper_id']);
            $ex_d = explode(",",$r['spread_id']);
			$str = '';
            $str_d = '';
			foreach($ex as $rr){
				$str .= $array_column[$rr].",";
			}
            foreach($ex_d as $v) {
                $str_d .= $array_columns[$v].",";
            }
			$data['data'][$k]['follow_upper'] = trim($str,",");
            $data['data'][$k]['spread_name'] = trim($str_d,",");
            $data['data'][$k]['source'] = trim($r['source'],",");
            $data['data'][$k]['source_channel'] = trim($r['source_channel'],",");
            $data['data'][$k]['text_item'] = trim($r['text_item'],",");
            $arac = $model->selectFollow($r['id'],true);
            $strrr = '';
            if(!empty($arac)){
                $strrr = substr($arac['created_time'],0,10)." ".$arac['follow_name']." : ".$arac['content'];
            }
			$data['data'][$k]['newest_follow_info'] = $strrr;
			$data['data'][$k]['follow_info'] = $model->selectFollow($r['id']);
		}
        if($args['down_infos'] == 'downs'){
            $this->downloads($data['data']);exit();
        }
		/*echo '<pre>';
		print_r ($data);
		echo '</pre>';
		exit;*/
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'dealer_customer_manage_search_page';
		$this->render('dealer_customer_manage_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$user_model = new RoleUserModel(1);
		$operator = array_column($user_model->getRoleUserList(32),'uid');
		$result['content'] = $this->fetch('dealer_customer_manage_info.html',array(
			'view'=>new DealerCustomerManageView(new DealerCustomerManageModel(15)),
			'role_data'=>$user_model->getRoleUserList(31),
			'operator'=>$operator,
            'spread'=>$user_model->getRoleUserList(33),
            //'source'=>$this->getHistorySource(),
            //'source_channel'=>$this->getHistorySourceChannel()
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
		$user_model = new RoleUserModel(1);
		$operator = array_column($user_model->getRoleUserList(32),'uid');
		$model = new DealerCustomerManageModel($id,15);
		$view = new DealerCustomerManageView($model);
		$result['content'] = $this->fetch('dealer_customer_manage_info.html',array(
			'view'=>$view,
			'tab_id'=>$tab_id,
			'follower_arr'=>explode(",",$view->get_follow_upper_id()),
            'spread_arr'=>explode(",",$view->get_spread_id()),
			'role_data'=>$user_model->getRoleUserList(31),
			'id'=>$id,
			'follow_data'=>$model->selectFollow($id),
			'operator'=>$operator,
            'spread'=>$user_model->getRoleUserList(33),
            'source'=>$this->getHistorySource(),
            'source_edit'=>trim($view->get_source(),","),
            'source_channel_edit'=>trim($view->get_source_channel(),","),
            //'source_channel'=>$this->getHistorySourceChannel(),
            'source_all'=>explode(",",$view->get_source()),
            //'source_channel_all'=>explode(",",$view->get_source_channel())
            'text_item'=>trim($view->get_text_item(),","),
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
		$this->render('dealer_customer_manage_show.html',array(
			'view'=>new DealerCustomerManageView(new DealerCustomerManageModel($id,15)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		/*echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;*/
		$newmodel =  new DealerCustomerManageModel(16);
		if(!_Post::getString('customer_name'))
		{
			$result['error'] = "请填写准客户姓名";
			Util::jsonExit($result);
		}
		if(_Post::getString('tel'))
		{
			$ifrepeat = $newmodel->checkTel(_Post::getString('tel'));
			if($ifrepeat){
				$result['error'] = "此手机号重复,请重新填写";
				Util::jsonExit($result);
			}
		}
		$olddo = array();
		$follow_upper_id = !empty(_Post::getList('follow_upper_id'))?",".implode(",",_Post::getList('follow_upper_id')).",":'';
        $spread_id = !empty(_Post::getList('spread_id'))?",".implode(",",_Post::getList('spread_id')).",":'';
        $source = !empty(_Post::getList('source'))?",".str_replace(array("，"),",",implode(",",_Post::getList('source'))).",":'';
        $source_channel = !empty(_Post::getList('source_channel'))?",".str_replace(array("，"),",",implode(",",_Post::getList('source_channel'))).",":'';
        $text_item = !empty(_Post::getList('text_item'))?",".str_replace(array("，"),",",implode(",",_Post::getList('text_item'))).",":'';
		$newdo=array(
			'customer_name'=>_Post::getString('customer_name'),
			'status'=>_Post::getString('status'),
			'source'=>$source,
			'source_channel'=>$source_channel,
			'tel'=>_Post::getString('tel'),
			'email'=>_Post::getString('email'),
			'province'=>_Post::getString('province'),
			'city'=>_Post::getString('city'),
			'district'=>_Post::getString('district'),
			'shop_nums'=>_Post::getString('shop_nums'),
			'investment_amount'=>_Post::getString('investment_amount'),
			'info'=>_Post::getString('info'),
			'follow_upper_id'=>$follow_upper_id,
            'spread_id'=>$spread_id,
			'created_time'=>date("Y-m-d H:i:s"),
			'modified_time'=>date("Y-m-d H:i:s"),
            'text_item'=>$text_item
		);
		
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
		/*echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;*/
		$user_model = new RoleUserModel(1);
		$operator = array_column($user_model->getRoleUserList(32),'uid');
		
		if($_SESSION['userType'] == 1 || in_array($_SESSION['userId'],$operator)){
			if(!_Post::getString('customer_name'))
			{
				$result['error'] = "请填写准客户姓名";
				Util::jsonExit($result);
			}
		}
		$olddo = array();
		$newmodel =  new DealerCustomerManageModel($id,16);
		$olddo = $newmodel->getDataObject();
		
		if($_SESSION['userType'] == 1 || in_array($_SESSION['userId'],$operator)){
			$follow_upper_id=!empty(_Post::getList('follow_upper_id'))?",".implode(",",_Post::getList('follow_upper_id')).",":'';
            $spread_id = !empty(_Post::getList('spread_id'))?",".implode(",",_Post::getList('spread_id')).",":'';
            $source = !empty(_Post::getList('source'))?",".str_replace(array("，"),",",implode(",",_Post::getList('source'))).",":'';
            $source_channel = !empty(_Post::getList('source_channel'))?",".str_replace(array("，"),",",implode(",",_Post::getList('source_channel'))).",":'';
            $text_item = !empty(_Post::getList('text_item'))?",".str_replace(array("，"),",",implode(",",_Post::getList('text_item'))).",":'';
			$newdo=array(
				'id'=>$id,
				'customer_name'=>_Post::getString('customer_name'),
				'status'=>_Post::getString('status'),
				'source'=>$source,
				'source_channel'=>$source_channel,
				'tel'=>_Post::getString('tel'),
				'email'=>_Post::getString('email'),
				'province'=>_Post::getString('province'),
				'city'=>_Post::getString('city'),
				'district'=>_Post::getString('district'),
				'shop_nums'=>_Post::getString('shop_nums'),
				'investment_amount'=>_Post::getString('investment_amount'),
				'info'=>_Post::getString('info'),
				'follow_upper_id'=>$follow_upper_id,
                'spread_id'=>$spread_id,
				'modified_time'=>date("Y-m-d H:i:s"),
                'text_item'=>$text_item
			);
		}else{
			$newdo=array(
				'id'=>$id,
				'status'=>_Post::getString('status'),
			    'modified_time'=>date("Y-m-d H:i:s")
			);
		}

		$res = $newmodel->saveData($newdo,$olddo);
		/** 编辑跟进情况 **/
		$content = _Post::getList('content');
		$fid = _Post::getList('fid');
		if(!empty($fid)){
			foreach($content as $k=>$r){
				$newmodel->updateFollow($r,$fid[$k]);
			}
		}
		/** end **/
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
		
		$model = new DealerCustomerManageModel($id,16);
		//联合删除？
		$res = $model->delete();
		if($res !== false){
			$model->deleteFollow($id);
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
	
	/**
	 *	add_follow，渲染添加跟进情况页面
	 */
	public function add_follow ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('dealer_customer_manage_add_follow.html',array(
			'view'=>new DealerCustomerManageView(new DealerCustomerManageModel(15)),
			'id'=>intval($_REQUEST['id']),
            //'source'=>$this->getHistorySource()
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}
	
	/**
	 *	insert，添加跟进情况
	 */
	public function insert_follow ($params)
	{
		$result = array('success' => 0,'error' =>'','compare' => 0);
		/*echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;*/
		$id = _Post::getInt('id');
		if(!_Post::getString('content'))
		{
			$result['error'] = "请填写跟进情况";
			Util::jsonExit($result);
		}
		$olddo = array();
		$anothermodel =  new DealerCustomerManageModel($id,16);
		$olddo = $anothermodel->getDataObject();
		if($olddo['status'] == '待跟进' && $_GET['compare'] == 1){
			$result['error'] = "目前跟进状态为待跟进,如需修改,请按确认,无需修改请点取消";
			$result['compare'] = 1;
		}
		$newdo=array(
			'did'=>$id,
			'content'=>_Post::getString('content'),
			'created_time'=>date("Y-m-d H:i:s"),
			'modified_time'=>date("Y-m-d H:i:s"),
            'follow_name'=>$_SESSION['userName']
		);

		$newmodel =  new DealerCustomerManageModel(16);
		$res = $newmodel->saveFollow($newdo);
        //$source = !empty(_Post::getList('source'))?",".implode(",",_Post::getList('source')).",":'';
        $status = _Post::getString('status');
		if($status){
			$anotherdo=array(
				'id'=>$id,
				'status'=>$status,
				'modified_time'=>date("Y-m-d H:i:s")
			);
			$anothermodel->saveData($anotherdo,$olddo);
		}
		/*if($olddo['status'] == '待跟进'){
			$statusdo=array(
				'id'=>$id,
				'status'=>'跟进中',
				'modified_time'=>date("Y-m-d H:i:s")
			);
			$anothermodel->saveData($statusdo,$olddo);
		}*/
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

    //取出历史来源类型
    public function getHistorySource()
    {
        $model = new DealerCustomerManageModel(15);
        $data = array_filter(array_column($model->getHistorySource(), 'source'));
        if($data){
            foreach ($data as &$v) {
                $v = trim($v, ",");
            }
        }
        return $data;
    }

    //取出历史来源类型
    public function getHistorySourceChannel()
    {
        $model = new DealerCustomerManageModel(15);
        $data = array_filter(array_column($model->getHistorySourceChannel(), 'source_channel'));
        if($data){
            foreach ($data as &$v) {
                $v = trim($v, ",");
            }
        }
        return $data;
    }

    //取出历史项目
    public function getTextItem()
    {
        $model = new DealerCustomerManageModel(15);
        $data = array_filter(array_column($model->getTextItem(), 'text_item'));
        if($data){
            foreach ($data as &$v) {
                $v = trim($v, ",");
            }
        }
        return $data;
    }

    /**
     * 下载
     */
    public function downloads($data) {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=" . iconv('utf-8', 'gb2312', "准客户跟进情况导出") . ".xls");

        if ($data) {
            $csv_body = '<table border="1"><tr>
                <td style="text-align: center;">录入时间</td>
                <td style="text-align: center;">准客户姓名</td>
                <td style="text-align: center;">状态</td>
                <td style="text-align: center;">项目</td>
                <td style="text-align: center;">来源类型</td>
                <td style="text-align: center;">来源渠道</td>
                <td style="text-align: center;">联系电话</td>
                <td style="text-align: center;">联系邮箱</td>
                <td style="text-align: center;">省</td>
                <td style="text-align: center;">市</td>
                <td style="text-align: center;">区</td>
                <td style="text-align: center;">意向开店数</td>
                <td style="text-align: center;">投资金额</td>
                <td style="text-align: center;">招商经理</td>
                <td style="text-align: center;">推广专员</td>
                <td style="text-align: center;">跟进情况</td>
                <td style="text-align: center;">其他信息</td>
            </tr>';
            foreach ($data as $key => $val) {
                $csv_body .= "<tr>";
                $csv_body .= "<td>" . $val['created_time'] . "</td><td>" . $val['customer_name'] . "</td>";
                $csv_body .= "<td>" . $val['status'] . "</td><td>" . $val['text_item'] . "</td><td>" . $val['source'] . "</td>";
                $csv_body .= "<td>" . $val['source_channel'] . "</td>";
                $csv_body .= "<td>" . $val['tel'] . "</td><td>" . $val['email'] . "</td>";
                $csv_body .= "<td>" . $val['province'] . "</td><td>" . $val['city'] . "</td>";
                $csv_body .= "<td>" . $val['district'] . "</td><td>" . $val['shop_nums'] . "</td>";
                $csv_body .= "<td>" . $val['investment_amount'] . "</td><td>" . $val['follow_upper'] . "</td>";
                $csv_body .= "<td>" . $val['spread_name'] . "</td><td>" . $val['newest_follow_info'] . "</td>";
                $csv_body .= "<td>" . $val['info']."</td></tr>";
            }
            $csv_body .= "</table>";
            echo $csv_body;
        } else {
            echo '没有数据！';
        }
        exit;
    }

    /**
     *  pl_Fenpei
     */
    public function pl_Fenpei ($params)
    {
        $result = array('success' => 0,'error' => '');
        $user_model = new RoleUserModel(1);
        $operator = array_column($user_model->getRoleUserList(32),'uid');
        $result['content'] = $this->fetch('dealer_customer_manage_info_pl.html',array(
            'view'=>new DealerCustomerManageView(new DealerCustomerManageModel(15)),
            'role_data'=>$user_model->getRoleUserList(31),
            'operator'=>$operator,
            'ids'=>implode(',', $params['_ids']),
            'spread'=>$user_model->getRoleUserList(33),
            'text_item'=>$this->getTextItem()
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }

    /**
     *  insert，信息入库
     */
    public function pl_insert ($params)
    {
        $result = array('success' => 0,'error' =>'');
        if(!isset($params['_ids']) && $params['_ids'] == ''){
            $result['error'] = "参数错误！";
            Util::jsonExit($result);
        }
        $ids = explode(",", $params['_ids']);
        $follow_upper_id = !empty(_Post::getList('follow_upper_id'))?",".implode(",",_Post::getList('follow_upper_id')).",":'';
        $spread_id = !empty(_Post::getList('spread_id'))?",".implode(",",_Post::getList('spread_id')).",":'';
        $text_item = !empty(_Post::getList('text_item'))?",".implode(",",_Post::getList('text_item')).",":'';
        $newdo=array(
            'follow_upper_id'=>$follow_upper_id,
            'spread_id'=>$spread_id,
            'text_item'=>$text_item
        );

        $trck = true;
        foreach ($ids as $id) {
            $newmodel =  new DealerCustomerManageModel($id,16);
            $olddo = $newmodel->getDataObject();
            $newdo['id'] = $id;
            if($newdo['follow_upper_id'] == ''){
                $newdo['follow_upper_id'] = $olddo['follow_upper_id'];
            }
            if($newdo['spread_id'] == ''){
                $newdo['spread_id'] = $olddo['spread_id'];
            }
            if($newdo['text_item'] == ''){
                $newdo['text_item'] = $olddo['text_item'];
            }
            $res = $newmodel->saveData($newdo,$olddo);
            if($res == false){
                $trck = false;
            }
        }
        if($trck !== false)
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
     *  批量导入
     */
    public function upload_info ()
    {
        $result = array('success' => 0,'error' => '');
        $result['content'] = $this->fetch('dealer_customer_manage_info_upload.html',array(
            'view'=>new DealerCustomerManageView(new DealerCustomerManageModel(15))
        ));
        $result['title'] = '导入准客户信息';
        Util::jsonExit($result);
    }

    /**
     *  导入信息
     */
    public function upload_ins($params)
    {
        set_time_limit(0);//设置上传允许超时提交（数据量大时有用）
        $result = array('success' => 0,'error' => '');

        //标红提示；
        $red_err_str = "提示：<span style='color:red';>";
        $html_end = "</span> <br />";
        //$result['error'] = "提示：批量上传成功，<span style='color:red;'>请核查！</span>";
        //Util::jsonExit($result);
        $fileInfo = $_FILES['file_dealer'];//读取文件信息；

        $tmp_name = $fileInfo['tmp_name'];
        //是否选择文件；
        if ($tmp_name == '') 
        {

            $result['error'] = $red_err_str."请选择上传文件！（请下载模版务必按照表头填写后上传）。".$html_end;
            Util::jsonExit($result);
        }

        //是否csv文件；
        $file_name = $fileInfo['name'];
        if (Upload::getExt($file_name) != 'csv') 
        {

            $result['error'] = $red_err_str."请上传.csv为后缀的文件！".$html_end;
            Util::jsonExit($result);
        }

        //打开文件资源
        $fileData = fopen($tmp_name, 'r');
        while ($data = fgetcsv($fileData))
        {
            $dealerInfo[] = $data;
        }

        //是否填写数据
        if (count($dealerInfo) == 1)
        {

            $result['error'] = $red_err_str."未检测到数据，请填写后上传！".$html_end;
            Util::jsonExit($result);
        }

        //限制上传数据量，限制行数为小于等于150行数据
        if (count($dealerInfo) >= 151)
        {

            $result['error'] = $red_err_str."上传数据过大会导致提交超时，不能超过150行信息！".$html_end;
            Util::jsonExit($result);
        }

        $hgt = 1;//行数；
        array_shift($dealerInfo);//去除首行文字；
        $model = new DealerCustomerManageModel(16);
        $pdo = $model->db()->db();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
        $pdo->beginTransaction();//开启事务
        $telAll = array_filter(array_column($model->getTelAll(), 'tel'));
        foreach ($dealerInfo as $key => $value) {
            $hgt++;
            //是否为16列信息；
            if (count($value) != 13)
            {

                $result['error'] = $red_err_str."文件第".$hgt."行请上传13列信息！（按照模版要求填写）".$html_end;
                Util::jsonExit($result);
            }

            $fields = array('customer_name','status','text_item','source','source_channel','tel','email','province','city','district','shop_nums','investment_amount','info');

            //去除用户录入不规范的内容
            for ($i=0; $i < 13; $i++) 
            {
                $LineInfo[$fields[$i]] = $this->trimall($value[$i]);
            }

            if($LineInfo['customer_name'] == '')
            {
                $result['error'] = $red_err_str."文件第".$hgt."行请填写准客户姓名！".$html_end;
                Util::jsonExit($result);
            }

            if($LineInfo['tel'] == '')
            {
                $result['error'] = $red_err_str."文件第".$hgt."行请填写电话号码！".$html_end;
                Util::jsonExit($result);
            }

            if($LineInfo['source'] == '')
            {
                $result['error'] = $red_err_str."文件第".$hgt."行请填写来源类型！".$html_end;
                Util::jsonExit($result);
            }

            if($LineInfo['source_channel'] == '')
            {
                $result['error'] = $red_err_str."文件第".$hgt."行请填写来源渠道！".$html_end;
                Util::jsonExit($result);
            }

            if($LineInfo['text_item'] == '')
            {
                $result['error'] = $red_err_str."文件第".$hgt."行请填写项目！".$html_end;
                Util::jsonExit($result);
            }

            if(in_array($LineInfo['tel'], $telAll)){
                $result['error'] = $red_err_str."文件第".$hgt."行电话号码在系统已存在！".$html_end;
                Util::jsonExit($result);
            }

            $LineInfo['follow_upper_id'] = '';
            $LineInfo['source'] = ','.trim($LineInfo['source']).',';
            $LineInfo['source_channel'] = ','.trim($LineInfo['source_channel']).',';
            $LineInfo['text_item'] = ','.trim($LineInfo['text_item']).',';
            $LineInfo['created_time'] = date('Y-m-d H:i:s',time());
            $LineInfo['modified_time'] = date('Y-m-d H:i:s',time());

            $data[] = $LineInfo;
        }

        foreach ($data as $key => $value) {

            $r = $model->saveData($value,array());

            if($r == false){
                $pdo->rollback();
                $result['error'] = '提交失败！';
                Util::jsonExit($result);
            }
        }
        $pdo->commit();
        $result['success'] = 1;
        Util::jsonExit($result);
    }

    /**
     *  trimall，删除空格
     */
    public function trimall($str)
    {

        //字符类型转换；
        $str = iconv('gbk','utf-8',$str);
        //数字不能为负数；
        if(is_numeric($str)){

            $str = abs($str);
        }
        //过滤字符串中用户不小心录入的的空格、换行、等特殊字符；
        $qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");

        return str_replace($qian,$hou,$str);
    }

    //模板
    public function dow($params)
    {
        $title = array(
                '准客户姓名',
                '状态',
                '项目',
                '来源类型',
                '来源渠道',
                '联系电话',
                '邮箱',
                '省',
                '市',
                '区',
                '意向开店数',
                '投资金额',
                '其他信息'
                );
        $data[0]['name']="张三";
        $data[0]['status']="待跟进";
        $data[0]['xiangmu']="kelan";
        $data[0]['laiyuan']="A";
        $data[0]['qudao']="中国加盟网";
        $data[0]['dianhua']="13888888882";
        $data[0]['eml']="123@kela.cn";
        $data[0]['sheng']="广东";
        $data[0]['shi']="深圳";
        $data[0]['qu']="龙岗";
        $data[0]['yix']="1";
        $data[0]['jine']="1000000万";
        $data[0]['qita']="备注";
            
        Util::downloadCsv("masterplate".time(),$title,$data);
            
    }
}

?>