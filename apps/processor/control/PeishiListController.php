<?php
/**
 *  -------------------------------------------------
 *   @file		: PeishiListController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-14 21:00:22
 *   @update	:
 *  -------------------------------------------------
 */
class PeishiListController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('export','updateOldBCAndPeishi','downPeishiTpl','printPeishi');

	/**
	 *	index，搜索框
	 */
	public function index ($params)	
	{
	        $getChannelArr = $this->getChannelArr();
            $this->render('peishi_list_search_form.html',array(
                'bar'=>Auth::getBar(),
                'view'=>new PeishiListView(new PeishiListModel(13)),
                'getChannelArr'=>$getChannelArr
            ));
	}
	/**
	 * 批量同步历史 布产单属性 和 配石单属性
	 * @param unknown $params
	 */
	public function updateOldBCAndPeishi($params){
	    set_time_limit(0);
	    $last_time = _Request::get('last_time','');
	    $type = _Request::get('type');
	    if(empty($last_time)){
	        exit("last_time is empty!");
	    }
	    if(empty($type)){
	        exit("type is empty! 1 or 2");
	    }
	    
	    $peishiModel = new PeishiListModel(14);
	    $total = 100;
	    if($type==1){
	        $sql = "SELECT DISTINCT rec_id FROM	product_info_attr a INNER JOIN peishi_list b ON a.g_id = b.rec_id WHERE
	(`code` = 'cart' OR `code` = 'zuanshidaxiao') AND `value` LIKE '%ct' and b.last_time<='{$last_time}' order by b.rec_id desc limit 100";
	         
	    } else{
	        $sql = "select rec_id from peishi_list where last_time<='{$last_time}' order by id desc limit 100";	         
	    }
	    while($total ==100){
    	    $bc_ids = $peishiModel->db()->getAll($sql);
    	    $total = count($bc_ids);
    	    foreach ($bc_ids as $vo){
    	        $bc_id = $vo['rec_id'];	  
    	        $res = $peishiModel->updateOldBCAndPeishi($bc_id,"清洗配石单"); 
    	        if($res['success']==0){
    	            @file_put_contents('peishiqingxi.log',date('Y-m-d H:i:s')."--".var_export($res,true)."\r\n",FILE_APPEND);
    	        } else{
    	            echo "{$bc_id}更新成功<br/>\r\n";
    	        }    
    	    }
	    }
	    echo "Finish!";
	}
	/**
	 *	search，列表
	 */
	public function search ($params)
	{
	    /*  $styleModel = new CStyleModel(11);
	    $style_sn ='W240_001';
	    $stone = '0';
	    $xiangkou = '0.5'; 
	    $zhiquan ='11';
	    $res = $styleModel->getStyleFushi($style_sn, $stone, $xiangkou, $zhiquan);
	    echo '<pre>';
	    print_r($res);exit;  */
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
            'bc_sn' => _Request::get("bc_sn"),
            'department_id' => _Request::get("department_id"),
            'peishi_status'=> _Request::get('peishi_status'),
            'channel_class'=> _Request::get('channel_class'),
			'is_quick_diy'=>_Request::get('is_quick_diy'),
			'stone_cat'=> _Request::get('stone_cat'),
			'shape'=> _Request::get('shape'),
		    'cert'=> _Request::get('cert'),
		    'color'=> _Request::get('color'),
		    'clarity'=> _Request::get('clarity'),
		    'carat_min'=> _Request::getfloat('carat_min'),
		    'carat_max'=> _Request::getfloat('carat_max'),
		    'bc_type'=> _Request::get('bc_type'),
		    'from_type'=> _Request::get('from_type'),
		    'bc_status'=> _Request::get('bc_status'),
		    'goods_id' => _Request::get('goods_id'),
            'add_time_begin' =>_Request::get('add_time_begin'),
		    'add_time_end' =>_Request::get('add_time_end'),
		    'peishi_time_begin' =>_Request::get('peishi_time_begin'),
		    'peish_time_end' =>_Request::get('peishi_time_end'),
		    'songshi_time_begin' =>_Request::get('songshi_time_begin'),
		    'songshi_time_end' =>_Request::get('songshi_time_end'),
		    'caigou_time_begin' =>_Request::get('caigou_time_begin'),
		    'caigou_time_end' =>_Request::get('caigou_time_end'),
		    'caigou_user' =>_Request::get('caigou_user'),
		    'peishi_user' =>_Request::get('peishi_user'),
		    'songshi_user' =>_Request::get('songshi_user'),
		);
		$page = _Request::getInt("page",1);
		$where = $args;    
		if(!empty($where['bc_sn'])){
		    $where['bc_sn'] = str_replace(',', ' ',$where['bc_sn']);
		    $where['bc_sn'] = preg_replace('/\s+/is',' ',$where['bc_sn']);
		    $where['bc_sn'] = explode(' ',$where['bc_sn']);
		}
		if(!empty($where['goods_id'])){
		    $where['goods_id'] = str_replace(',', ' ',$where['goods_id']);
		    $where['goods_id'] = preg_replace('/\s+/is',' ',$where['goods_id']);
		    $where['goods_id'] = explode(' ',$where['goods_id']);
		}   
		$model = new PeishiListModel(13);       
		$data = $model->pageListSum($where,$page,30,false);        
		$pageData = $data;
		
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'peishi_list_search_page';
		$this->render('peishi_list_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}	
    /**
     * 详情渲染页面，批量特殊配石操作   mutiPeishiUpdate方法为对应保存方法
     * @param unknown $params
     */
    public function show($params){      
        $ids = _Request::getString('id');
        $group_ids = explode('N',$ids);    
        
        if(empty($group_ids[0]) ){
            exit('id 参数错误');
        }
        $id = $group_ids[0];
        $model = new PeishiListModel(13);
        $where['peishi_ids'] = $group_ids;
        $data = $model->pageList($where,1,100,true,"orderby2");
        
        $peishi_status1= array(array ("name"=>0,"label" => "未操作 "));
        $peishi_status2 = $this->dd->getEnumArray("peishi_status");        
        $peishi_status_arr = array_merge($peishi_status1,$peishi_status2);
        
        foreach ($data['data'] as $key=>$vo){
            $peishi_status_default = "";
            $peishi_status_default2 = "";
            $peishi_status_edit = "0";
            /*
                                      采购中5,[已送生产部]4,[厂配石]3,配石中2,[不需配石]1            
             a如果当前状态为未操作，【更新状态】默认为“配石中”，允许编辑，但只允许选择配石中/不需配石/厂配石
             --b如果当前状态为已送生产部/不需配石/厂配石，【更新状态】默认为“当前状态”，不允许编辑，单个和批量都不允许更改
             --c如果当前状态为配石中，【更新状态】默认为“采购中”，允许编辑，   但只允许选择采购中/已送生产部，
             当输入条码后，【更新状态】自动更新为“已送生产部”，且不允许编辑
             d如果当前状态为采购中，【更新状态】默认为“采购中”，允许编辑，但只允许选择“已送生产部”，也支持输入条码，自动更新为已送生产部
             * */
            if(empty($vo['peishi_status'])){
                $peishi_status_default = 2;
                $peishi_status_edit = 1;
            }else if($vo['peishi_status'] == 2){
                $peishi_status_default = 5;
                $peishi_status_default2 = 4;
                $peishi_status_edit = 0;
            }else if($vo['peishi_status'] == 5){
                $peishi_status_default = 5;
                $peishi_status_default2 = 4;
                $peishi_status_edit = 1;
            }
            $vo['peishi_status_default'] = $peishi_status_default;
            $vo['peishi_status_default2'] = $peishi_status_default2;
            $vo['peishi_status_edit'] = $peishi_status_edit;

            $data['data'][$key] = $vo;
        }
        $this->render('peishi_list_show.html',array(
            'bar'=>Auth::getViewBar(),  
            'id' => $id,
            'peishi_status_arr'=>$peishi_status_arr,
            'data'=>$data['data']
        ));
    }
	/**
	 * 配石单详情   （仅用于内嵌页面）
	 * @param unknown $params
	 */
	public function getPeishiInfo($params){
	    $id = _Request::getInt('id');
	    if(!$id) {
	        exit('ID is empty!');
	    }
	    
	    $model = new PeishiListModel(13);
	    $where['peishi_ids'] = $id;
	    $data = $model->pageList($where,1,1,false,"orderby2");
	    if(!empty($data['data'])){
	        $info = $data['data'][0];
	    }else{
	        exit("流水号{$id}配石信息不存在");
	    }
	    
	    $style_sn = $info['style_sn'];
	    $bc_id = $info['bc_id'];

	    $galleryList = $this->getStyleAllImages($style_sn,$bc_id);	 
	    //print_r($galleryList);  
	   // print_r($galleryList); 
	    $this->render('get_peishi_info.html',array(
	        'info'=>$info,
	        'galleryList'=>$galleryList
	    ));
	}
	/**
	 * 查询指定款的图片列表信息
	 * @param unknown $style_sn
	 * @param number $id
	 * @return Ambigous <unknown, multitype:number unknown , multitype:unknown , mixed>
	 */
    public function getStyleAllImages($style_sn,$id=0) {
		$gallerymodel = new ApiStyleModel();
		$row=$gallerymodel->getStyleGalleryList($style_sn);
		if(empty($row))
		{
			$imgModel=new ProductInfoImgModel(13);
			$temp=$imgModel->getImgList($id);
			if(!empty($temp))
			{
				$row[0]=$temp;
			}
		}
		return $row;
	}
	/**
     * 批量配石操作 页面（正常流程配石） 渲染页面，保存操作为：peishi_option 方法
     */
	public function add ($params)
	{   
	    //未操作——配石中——备用钻\已送生产部\采购中\不需配石\厂配钻；
        //采购中——已送生产部\不需配石\备用钻\配石中
		$ids = _Request::getList("_ids");
	    $tab_id = _Request::getInt("tab_id");
	    $result = array('success' => 0,'error' => '','title'=>'配石操作');
	    
	    $newmodel =  new PeishiListModel(14);
	    //验证状态是否一致
	    $res = $newmodel->checkPeishiStatusEqual($ids);
	    if($res == false){
	        $result['content'] = "批量选择的配石状态不一致,不可以同时操作！";
	        Util::jsonExit($result);
	    };

	    $newmodel =  new PeishiListModel($ids[0],14);	    
	    $peishi_status = $newmodel->getValue('peishi_status');
	    $peishi_status_arr = $this->dd->getEnumArray('peishi_status');
	    //print_r($peishi_status_arr);
	    //根据现在状态 拼接数组
	    if($peishi_status==0){
	        unset($peishi_status_arr[2]);
	        unset($peishi_status_arr[3]);
	        unset($peishi_status_arr[5]);
	    }elseif($peishi_status==1){
	        $result['content'] = "不需配石状态不可以再操作！";
	        Util::jsonExit($result);
	    }elseif($peishi_status==2){
	        //配石中: 备用钻\已送生产部\采购中\不需布产\厂配钻
	        unset($peishi_status_arr[1]);
	    }elseif($peishi_status==3){
	        $result['content'] = "厂配石状态不可以再操作！";
	        Util::jsonExit($result);
	    }elseif($peishi_status==4){
	        $result['content'] = "已送钻状态不可以再操作！";
	        Util::jsonExit($result);
	    }elseif($peishi_status==6){
	        $result['content'] = "备用钻状态不可以再操作！";
	        Util::jsonExit($result);
	    }else if($peishi_status==5){
	        //当前状态 采购中，批量配石中选项应该是 已送生产部\不需配石\备用钻\配石中
	        unset($peishi_status_arr[2]);
	        unset($peishi_status_arr[4]);
	    }
	    $result['content'] = $this->fetch('peishi_list_info.html',array(
	        'view'=>new PeishiListView(new PeishiListModel(13)),
	        'ids'=>$ids,
	        'peishi_status_arr'=>$peishi_status_arr,
	        'tab_id'=>$tab_id
	    ));
	    Util::jsonExit($result);
	}

    /**
     * 批量配石操作 保存（正常流程配石）
     * 渲染页面方法：add
     */
	public function peishi_option ()
	{
	    $result = array('success' => 0,'error' =>'');
	    $ids = _Request::getList("_ids");
	
	    $_cls = _Post::getInt('_cls');
	    $tab_id = _Request::getInt("tab_id");	    
	    $peishi_status=_Request::get('peishi_status');	    
	    $peishi_status_name = $this->dd->getEnum('peishi_status',$peishi_status);
	    $peishi_status_name = $peishi_status_name?$peishi_status_name:'未操作';
	    $logModel = new ProductOpraLogModel(14);
	    //处理 配石中，送石，采购最后操作时间，操作人 数组
	    $actionArr = array(
	        2=>'peishi',4=>'songshi',5=>'caigou'
	    );
	    foreach($ids as $id){
	        $id = intval($id);
	        $newmodel =  new PeishiListModel($id,14);
	        $old_peishi_status = $newmodel->getValue('peishi_status');
	        $bc_id = $newmodel->getValue('rec_id');
	        
	        if($peishi_status==5 && !empty($old_peishi_status) && $old_peishi_status<>2){
	            $result['error'] = '只有未操作或配石中状态的才可以修改为 采购中';
	            Util::jsonExit($result);
	        }
	        $peishi_remark = "配石操作:".$peishi_status_name;
	        $newmodel->setValue('peishi_status',$peishi_status);
	        $newmodel->setValue('peishi_remark',$peishi_remark);
	        //更新 【配石中】，【送石】，【采购中】 最后操作时间，操作人
	        if(!empty($actionArr[$peishi_status])){
	            $actionName = $actionArr[$peishi_status];
	            $field_time = $actionName.'_time';
	            $field_user = $actionName.'_user';
	            $newmodel->setValue($field_time,date('Y-m-d H:i:s'));
	            if($old_peishi_status==5 && $peishi_status==2){
	                //采购中 更改为 配石中 ，配石人不变
	            }else{
	               $newmodel->setValue($field_user,$_SESSION['userName']);
	            }
	        }
	        $res = $newmodel->save();
	        if($res !== false)
	        {
	            //生产配石单日志
	            $remark = "配石操作:".$peishi_status_name;
	            $newmodel->addLog($id,$remark);
	
	            // 生成日志 到布产详情页
	            $remark = "布产单配石单{$id},配石操作:".$peishi_status_name;
	            $res = $logModel->addLog($bc_id,$remark);
	            	
	            $result['success'] = 1;
	            $result['_cls'] = $_cls;
	            $result['tab_id'] = $tab_id;
	            $result['title'] = '修改此处为想显示在页签上的字段';
	        }
	        else
	        {
	            $result['error'] = '操作失败！';
	        }
	    }
	
	    Util::jsonExit($result);
	
	}
	/**
	 *	重新配石 页面
	 *  不需配石|厂配石|已送生产部，这三种状态才允许点击重新配石
	 */
	public function rePeishi($params)
	{
		$ids = _Request::getList('_ids');
		
		$result = array('success' => 0,'error' => '','title'=>'重新配石');
	    foreach ($ids as $id){
	        $model =  new PeishiListModel($id,14);
	        $peishi_status = $model->getValue('peishi_status');	        
	        if(!in_array($peishi_status,array(1,3,4,6))){
	            $peishi_status = $this->dd->getEnum('peishi_status',$peishi_status);
	            $peishi_status = $peishi_status?$peishi_status:'未操作';
	            $result['content'] = "配石单{$id}【{$peishi_status}】不允许重新配石！提示：只有【不需配石】【厂配石】【已送生产部】【备用钻】 状态的配石单才允许重新配石!";
	            Util::jsonExit($result);
	        }   
	    }
	    
	    $result['content'] = $this->fetch('re_peishi_list_info.html',array(
	        'ids'=>$ids,
	    ));
	    Util::jsonExit($result);
	}
	/**
	 *	重新配石 保存
	 */
	public function rePeishiSave($params){
	    $result = array('success'=>0,'error'=>'');
	    $ids    = _Post::getList('_ids');
	    $reasonRemark = _Post::getString('remark');
	    if(empty($ids)){
	        $result['error'] = "ids is empty!";
	        Util::jsonExit($result);
	    }
	    if(empty($reasonRemark)){
	        $result['error'] = "操作原因不能为空!";
	        Util::jsonExit($result);
	    }
	    
	    //处理 配石中，送石，采购最后操作时间，操作人 数组
	    $actionArr = array(
	        2=>'peishi',4=>'songshi',5=>'caigou'
	    );
	    
	    $logModel = new ProductOpraLogModel(14);
	    $pdolist[14] = $logModel->db()->db();
	    try{
	        //开启事物
	        foreach ($pdolist as $pdo){
	            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
	            $pdo->beginTransaction(); //开启事务
	        }
    	    foreach ($ids as $id){
    	        $model =  new PeishiListModel($id,14);
    	        $peishi_status = $model->getValue('peishi_status');
    	        $bc_id = $model->getValue('rec_id');
    	        $old_peishi_status = $this->dd->getEnum('peishi_status',$peishi_status);
    	        $old_peishi_status = $old_peishi_status?$old_peishi_status:'未操作';
    	        if(!in_array($peishi_status,array(1,3,4,6))){
    	            $error = "配石单{$id}【{$old_peishi_status}】不允许重新配石！<br/>提示：只有【不需配石】【厂配石】【已送生产部】【备用钻】 状态的配石单才允许重新配石";
    	            Util::rollbackExit($error,$pdolist);
    	        }    	        
    	        $model->setValue('peishi_status',0);
    	        $model->setValue('peishi_remark',"配石操作:未操作");

	            $model->setValue('peishi_time','0000-00-00 00:00:00');
	            $model->setValue('songshi_time','0000-00-00 00:00:00');
	            $model->setValue('caigou_time','0000-00-00 00:00:00');
	            
	            $model->setValue('peishi_user','');
	            $model->setValue('songshi_user','');
	            $model->setValue('caigou_user','');
    	        $model->save();
    	        $model->deletePeishiGoods($id);
    	        //生产配石单日志
    	        $remark = "重新配石：配石单{$id}配石状态从【{$old_peishi_status}】变更为【未操作】，原因:{$reasonRemark}";
    	        $model->addLog($id,$remark);
    	        
    	        // 生成日志 到布产详情页 
    	        $remark = "重新配石：配石单{$id}配石状态从【{$old_peishi_status}】变更为【未操作】，原因:{$reasonRemark}";
    	        $res = $logModel->addLog($bc_id,$remark);
    	    }
    	    
    	    //$error = "success";
    	   // Util::rollbackExit($error,$pdolist);
    	    //批量提交事物
    	    foreach ($pdolist as $pdo){
    	        $pdo->commit();
    	        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
    	    }
    	    $result['success'] = 1;
    	    Util::jsonExit($result);
	    }catch (Exception $e){
	        $error = "重新配石操作失败！".$e->getMessage();
	        Util::rollbackExit($error,$pdolist);
	    }

	    
	}

	/**
	 *	批量自动配石 保存
	 *  show 方法为对应渲染页面
	 */
	public function mutiPeishiUpdate ($params)
	{
		$result = array('success' => 0,'error' =>'');
        
        $ids = _Request::getList('id');
        $goods_id_list = _Request::getList("goods_id");
        $peishi_status_list = _Request::getList("peishi_status");
        $peishi_remark_list = _Request::getList("peishi_remark");


        foreach ($goods_id_list as $k =>$v) {
            if(trim($v)==""){
                unset($goods_id_list[$k]);
                continue;
            }
            $goods_arr = explode("\n", $v);
            $goods_id_list[$k] =array_filter($goods_arr);
        }
        
        $datalist = array();
        $model =  new PeishiListModel(14);
        foreach ($ids as $id){
            
             $error = "流水号{$id}配石单更改失败！";
             $peishiInfo = $model->select2("*",'id='.$id,'row');
             if(empty($peishiInfo)){
                 $error .= "提示：流水号{$id}配石单不存在！";
                 $result['error'] = $error;
                 Util::jsonExit($result);
             }
             $old_peishi_status = (int)$peishiInfo['peishi_status'];
             $data = array('id'=>$id,'old_peishi_status'=>$old_peishi_status);
             //石头条码验证
             if(!empty($goods_id_list[$id])){     
           
                 $goods_ids = $goods_id_list[$id];
                 //只有当前状态为“配石中/采购中”才允许输入条码
                 if(!in_array($old_peishi_status,array(2,5))){
                     $error .= "提示：不允许更改条码,只有当前状态为【配石中】【采购中】才允许更改条码！";
                     $result['error'] = $error;
                     Util::jsonExit($result);
                 }
                 $existsGoodsId = $model->getExistsGoodsId($goods_ids);
                 $notExistsGoodsId = array_diff($goods_ids,$existsGoodsId);
                 if(!empty($notExistsGoodsId)){
                     $notExistsGoodsId = '【'.implode("】【",$notExistsGoodsId).'】';
                     $error .= "提示：钻石条码{$notExistsGoodsId}不存在！";
                     $result['error'] = $error;
                     Util::jsonExit($result);
                 }
                 //条码不为空时
                 if(in_array($old_peishi_status,array(2,5))){
                     $data['peishi_status'] = 4;
                 }
                 
                 $data['goods_ids'] = $goods_ids;
             }
             //配石状态验证
             if(isset($peishi_status_list[$id]) && $peishi_status_list[$id]<>''){
                 $new_peishi_status = $peishi_status_list[$id];               
                 /*
                                                            采购中5,已送生产部4,厂配石3,配石中2,不需配石1
                    A当前状态为已送生产部4/不需配石1/厂配石3 的配石单不允许再批量或单个更新状态，如果流程要回溯，后续有变更功能
                    b当前状态为未操作只能批量或单个更新状态为——配石中2/不需配石1/厂配石3
                    c当前状态为配石中只能批量或单个更新状态为——采购中5/已送生产部4
                    d当前状态为采购中只能批量或单个更新状态为——已送生产部4/不需配石1                    

a如果当前状态为未操作，【更新状态】默认为“配石中”，允许编辑，但只允许选择配石中/不需配石/厂配石
b如果当前状态为已送生产部/不需配石/厂配石，【更新状态】默认为“当前状态”，不允许编辑，单个和批量都不允许更改
c如果当前状态为配石中，【更新状态】默认为“采购中”，允许编辑，但只允许选择采购中/已送生产部，当输入条码后，【更新状态】自动更新为“已送生产部”，且不允许编辑
d如果当前状态为采购中，【更新状态】默认为“采购中”，允许编辑，但只允许选择“已送生产部”，也支持输入条码，自动更新为已送生产部

                  * */
                 if(in_array($old_peishi_status,array(1,3,4)) && $new_peishi_status<>$old_peishi_status){
                     $error .= "提示：当前状态为【已送生产部】【不需配石】【厂配石】 的配石单不允许再更新状态";
                     $result['error'] = $error;
                     Util::jsonExit($result);
                 }else if($old_peishi_status==0 && !in_array($new_peishi_status,array(2,1,3,0))){
                     $error .= "当前状态为【未操作】只能更新状态为【配石中】【不需配石】【厂配石】";
                     $result['error'] = $error;
                     Util::jsonExit($result);
                 }else if($old_peishi_status==2 && !in_array($new_peishi_status,array(5,4,2))){
                     $error .= "当前状态为【配石中】只能更新状态为【采购中】【已送生产部】";
                     $result['error'] = $error;
                     Util::jsonExit($result);
                 }else if($old_peishi_status==5 && !in_array($new_peishi_status,array(4,1,5))){
                     $error .= "当前状态为【采购中】只能更新状态为【已送生产部】【不需配石】";
                     $result['error'] = $error;
                     Util::jsonExit($result);
                 }
                 
                 $data['peishi_status'] = $new_peishi_status;                 
             }
             //备注
             if(isset($peishi_remark_list[$id])){
                 $data['peishi_remark'] = trim($peishi_remark_list[$id]) ;                  
             }
             $datalist[] = $data;            
        }

        //批量保存
        if(empty($datalist)){
            $result['error'] = '没有要保存更新的内容！';
            Util::jsonExit($result);
        }

        $res = $model->mutiPeishiUpdate($datalist);
        if($res['success']==1)
        {           
            $result['success'] = 1;
            Util::jsonExit($result);
        }
        else
        {
            $result['error'] = $res['error'];
            Util::jsonExit($result);
        } 
	}
	

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new PeishiListModel($id,14);
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
     * 配石日志查看
     * @param unknown $params
     */
	public function show_log($params)
	{
	    $result = array('success' => 0,'error' => '','title'=>'配石日志查看');	    
		$id = _Request::getInt('id');
		if(empty($id)){
		    $result['content'] = "参数错误: id is empty!";
		    Util::jsonExit($result);
		}
	    $model = new PeishiListModel(13);
	    
	    $page = 1;
	    $pageSize = 100;
	    $where['peishi_id'] = $id;
		$data = $model->pagePeishiLogList($where,$page,$pageSize);
		
		$result['content'] = $this->fetch('peishi_list_show_log.html',array(
			'data'=>$data['data']
		));
		Util::jsonExit($result);
	}
    //导出搜索结果
    public function export($data){
        set_time_limit(0);
        $where = array(
            'bc_sn' => _Request::get("bc_sn"),
            'department_id' => _Request::get("department_id"),
            'peishi_status'=> _Request::get('peishi_status'),
            'channel_class'=> _Request::get('channel_class'),
            'is_quick_diy'=>_Request::get('is_quick_diy'),
            'stone_cat'=> _Request::get('stone_cat'),
            'shape'=> _Request::get('shape'),
            'cert'=> _Request::get('cert'),
            'color'=> _Request::get('color'),
            'clarity'=> _Request::get('clarity'),
            'carat_min'=> _Request::getfloat('carat_min'),
            'carat_max'=> _Request::getfloat('carat_max'),
            'bc_type'=> _Request::get('bc_type'),
            'from_type'=> _Request::get('from_type'),
            'bc_status'=> _Request::get('bc_status'),
            'goods_id' => _Request::get('goods_id'),
            'add_time_begin' =>_Request::get('add_time_begin'),
            'add_time_end' =>_Request::get('add_time_end'),
            'peishi_time_begin' =>_Request::get('peishi_time_begin'),
            'peish_time_end' =>_Request::get('peishi_time_end'),
            'songshi_time_begin' =>_Request::get('songshi_time_begin'),
            'songshi_time_end' =>_Request::get('songshi_time_end'),
            'caigou_time_begin' =>_Request::get('caigou_time_begin'),
            'caigou_time_end' =>_Request::get('caigou_time_end'),
            'caigou_user' =>_Request::get('caigou_user'),
            'peishi_user' =>_Request::get('peishi_user'),
            'songshi_user' =>_Request::get('songshi_user'),
        );
        if(!empty($where['bc_sn'])){
            $where['bc_sn'] = str_replace(',', ' ',$where['bc_sn']);
            $where['bc_sn'] = preg_replace('/\s+/is',' ',$where['bc_sn']);
            $where['bc_sn'] = explode(' ',$where['bc_sn']);
        }
        if(!empty($where['goods_id'])){
            $where['goods_id'] = str_replace(',', ' ',$where['goods_id']);
            $where['goods_id'] = preg_replace('/\s+/is',' ',$where['goods_id']);
            $where['goods_id'] = explode(' ',$where['goods_id']);
        }
        $model = new PeishiListModel(13);
        $data = $model->pageList($where,1,20000,false);
        $dd = $this->dd;
        
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312','导出').time().".xls");
        $csv_header="<table><tr><td>流水号</td><td>订单号</td><td>销售渠道</td><td>布产号</td><td>是否快速定制</td><td>客户名</td><td>跟单人</td><td>线上线下</td><td>钻石大小</td><td>石头颜色</td><td>石头净度</td><td>石头形状</td><td>证书类型</td><td>数量</td><td>石头类型</td><td>证书号</td><td>配石状态</td><td>布产状态</td><td>布产类型</td><td>布产分类</td><td>工厂名称</td><td>货号</td>
			<td>添加时间</td>
            <td>配石中时间</td>
            <td>已送工厂时间</td>
            <td>采购时间</td>
            <td>配石人</td>
            <td>送石人</td>
            <td>采购人</td>
            <td>布产备注</td>
            </tr>";
        $csv_body = '';
        if(!empty($data['data'])){
            foreach ($data['data'] as $kv => $info) {
                $info['order_sn'] = preg_match("/\d+$/is",$info['order_sn'])?"'".$info['order_sn']:'';
                $info['peishi_status'] = $dd->getEnum('peishi_status',$info['peishi_status']);
                $info['peishi_status'] = $info['peishi_status']?$info['peishi_status']:'未操作';
                $info['bc_status'] = $dd->getEnum('buchan_status',$info['bc_status']);
                $info['is_quick_diy'] = $info['is_quick_diy'] == 1 ?'是':'否';
                $info['goods_id'] = preg_match("/\d+$/is",$info['goods_id'])?"'".$info['goods_id']:'';
                
                $csv_body.="<tr><td>{$info['id']}</td>
                <td>{$info['order_sn']}</td>
                <td>{$info['channel_name']}</td>
                <td>{$info['bc_sn']}</td>
                <td>{$info['is_quick_diy']}</td>
                <td>{$info['consignee']}</td>
                <td>{$info['opra_uname']}</td>
                <td>{$info['channel_class']}</td>
                <td>{$info['carat']}</td>
                <td>{$info['color']}</td>
                <td>{$info['clarity']}</td>
                <td>{$info['shape']}</td>
                <td>{$info['cert']}</td>
                <td>{$info['num']}</td>
                <td>{$info['stone_cat']}</td>
                <td>{$info['zhengshuhao']}</td>
                <td>{$info['peishi_status']}</td>
                <td>{$info['bc_status']}</td>
                <td>{$info['bc_type']}</td>
                <td>{$info['from_type']}</td>
                <td>{$info['prc_name']}</td>
                <td>{$info['goods_id']}</td>
                <td>{$info['add_time']}</td>
				<td>{$info['peishi_time']}</td>
				<td>{$info['songshi_time']}</td>
				<td>{$info['caigou_time']}</td>
				<td>{$info['peishi_user']}</td>
				<td>{$info['songshi_user']}</td>
				<td>{$info['caigou_user']}</td>
                <td>{$info['bc_remark']}</td></tr>";
            }
        }
        $csv_footer="</table>";
        echo $csv_header.$csv_body.$csv_footer;
    }
    /**
     * 下载批量配石excel模板
     * @param unknown $parmas
     */
    public function downPeishiTpl($parmas){
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312','Excel批量配石导入模板').".csv");
        $titles = array('(*)流水号','(*)布产号','钻石条码(多个请用 空格 隔开)','备注');
        foreach ($titles as $key=>$vo){
            $titles[$key] = iconv('utf-8','gb2312','"'.$vo.'"');
        }
        echo implode(",",$titles);
        exit;
    }
    /**
     * 批量excel导入配石  渲染页面
     * @param unknown $params
     */
    public function importPeishi($params){
         $result = array('content'=>'','title'=>'Excel批量配石操作');
         $result['content'] = $this->fetch('import_peishi.html');
         Util::jsonExit($result);
    }
    /**
     * 批量excel导入配石 保存
     * @param unknown $params
     */
    public function importPeishiSave($params){
        
        $result = array('success'=>0,'error'=>'');
                
        if(empty($_FILES['file']['tmp_name'])){
            $result['error'] = "请上传文件";
            Util::jsonExit($result); 
        }
        $file = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];
        if (Upload::getExt($file_name) != 'csv') {
            $result['error'] = '请上传csv格式的excel文件';
            Util::jsonExit($result);
        }
        $model = new PeishiListModel(14);        
        $line = 0;
        $datalist = array(); 
        $file = fopen($file,"r");
        while ($datav = fgetcsv($file)) {
            $line ++;            
            $is_empty_line = true;
            foreach ($datav as $key=>$vo){
                if(trim($vo)!=""){
                    $is_empty_line = false;
                }
                $datav[$key] = iconv('gbk','utf-8',$vo);
            }
            //$result['error'] = var_export($datav,true);
            //Util::jsonExit($result);
            if($line == 1 || $is_empty_line==true){
                continue;
            }
            $id = trim($datav[0]);
            $bc_sn = trim($datav[1]);
            $goods_ids = trim($datav[2]);
            $remark = trim($datav[3]);
            $error = "第{$line}行：";
            if(empty($id) || !Util::isNum($id)){
                $error .= "流水号【{$peishi_id}】不合法！";
                $result['error'] = $error;
                Util::jsonExit($result);
            }
            $peishiInfo = $model->getPeishiInfo($id);
            if(empty($peishiInfo)){
                $error .= "流水号【{$id}】不存在！";
                $result['error'] = $error;
                Util::jsonExit($result);
            }else if($peishiInfo['bc_sn']!= $bc_sn){
                $error .= "布产号【{$bc_sn}】与流水号对应的布产号【{$peishiInfo['bc_sn']}】不匹配！";
                $result['error'] = $error;
                Util::jsonExit($result);
            }
            $old_peishi_status = $peishiInfo['peishi_status'];
            if($old_peishi_status!=2){
                $error .= "流水号【{$id}】配石单不是配石中状态！提示配石状态必须为【配石中】状态才可以操作！";
                $result['error'] = $error;
                Util::jsonExit($result);
            }
            $data = array(
                'id'=>$id,
                'old_peishi_status'=>$old_peishi_status,
                'peishi_status'=>4,//新配石状态 ：已送生产部
                'peishi_remark' => $remark
            );
            if(!empty($goods_ids)){  
                $goods_ids = preg_replace("/\s+|\|/is"," ",$goods_ids);
                $goods_ids = explode(" ",$goods_ids);
                //只有当前状态为“配石中/采购中”才允许输入条码
                /* if(!in_array($old_peishi_status,array(2,5))){
                    $error .= "不允许更改条码,只有当前状态为【配石中】【采购中】才允许更改条码！";
                    $result['error'] = $error;
                    Util::jsonExit($result);
                }*/
                $existsGoodsId = $model->getExistsGoodsId($goods_ids);
                $notExistsGoodsId = array_diff($goods_ids,$existsGoodsId);
                if(!empty($notExistsGoodsId)){
                    $notExistsGoodsId = '【'.implode("】【",$notExistsGoodsId).'】';
                    $error .= "钻石条码{$notExistsGoodsId}不存在！";
                    $result['error'] = $error;
                    Util::jsonExit($result);
                }                 
                $data['goods_ids'] = $goods_ids;
            }
            
            if(empty($data['goods_ids']) && empty($data['peishi_remark'])){
                $error .= "备注与钻石条码二选一，必须填其中一个";
                $result['error'] = $error;
                Util::jsonExit($result);
            }             
            
            $datalist[] = $data;
        }
        fclose($file);
        
        if(empty($datalist)){
            $result['error'] = "提交数据为空";
            Util::jsonExit($result);
        }
               
        $res = $model->mutiPeishiUpdate($datalist);
        //print_r($res);
       
        if($res['success']==1){
            $result['success'] = 1;
            Util::jsonExit($result);
        }else{
            $result['error'] = $res['error'];
            Util::jsonExit($result);
        }
        
    }
    
    /**
     * 打印配石单 标签 支持批量打印
     * @param unknown $params
     */
    public function printPeishi($params){
        $ids = _Request::getList("_ids");
        $where['peishi_ids'] = $ids;        
        $model = new PeishiListModel(13); 
        $view = new PeishiListView($model);       
        $data = $model->pageList($where,1,100,true,"orderby2");
        if(empty($data['data'])){
            exit("NO Data");
        }
        $this->render('print_peishi.html',array(
            'datalist'=>$data['data'],
            'view'=>$view
          )
        );
    }
}

?>