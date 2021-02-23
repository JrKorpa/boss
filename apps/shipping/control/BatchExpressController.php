<?php
/**
 * 批量快递单操作:批量录入快递信息、打印快递单、批量登记快递单的功能
 *  -------------------------------------------------
 *   @file		: BatchExpressController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2015-09-28
 *   @update	:
 *  -------------------------------------------------
 */
class BatchExpressController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('downloadTpl','printExpress','exportExpress');
	
    /**
     * 默认页
     * @see CommonController::index()
     */
    public function index($params)
    {
        $expressModel = new ExpressModel(1);
        $expressComList = $expressModel->getAllExpress();
        $expressComList = array_column($expressComList,"exp_name",'id');
        
        $this->render('batch_express_search_form.html',
            array(
                'bar'=>Auth::getBar(),    
                'expressComList'=>$expressComList,
            )
        );
    }
    
    /**
     * 快递单文件搜索结果页
     */
    public function search()
    {
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
        $pageSize = isset($_REQUEST["pageSize"]) ? intval($_REQUEST["pageSize"]) : 15 ;
        
        $args = array(
            'mod'	=> _Request::get("mod"),
            'con'	=> substr(__CLASS__, 0, -10),
            'act'	=> __FUNCTION__,
            'page'=>  $page,
            'pageSize'=>  $pageSize,
            'express_id'=> _Request::getString('express_id'),
            'create_user'=> _Request::getString('create_user'),
            'is_print'=> _Request::getString('is_print'),            
            'is_register'=> _Request::getString('is_register'),
            'register_time_begin'=> _Request::getString('register_time_begin'),
            'register_time_end'=> _Request::getString('register_time_end'),
            'print_time_begin'=> _Request::getString('print_time_begin'),
            'print_time_end'=> _Request::getString('print_time_end'),
            'create_time_begin'=> _Request::getString('create_time_begin'),
            'create_time_end'=> _Request::getString('create_time_end'),
        );
        $where = array(
            'express_id'=> _Request::getString('express_id'),
            'create_user'=> _Request::getString('create_user'),
            'is_print'=> _Request::getString('is_print'),            
            'is_register'=> _Request::getString('is_register'),
            'register_time_begin'=> _Request::getString('register_time_begin'),
            'register_time_end'=> _Request::getString('register_time_end'),
            'print_time_begin'=> _Request::getString('print_time_begin'),
            'print_time_end'=> _Request::getString('print_time_end'),
            'create_time_begin'=> _Request::getString('create_time_begin'),
            'create_time_end'=> _Request::getString('create_time_end'),
        );
        $expressModel = new ExpressModel(1);
        $expressComList = $expressModel->getAllExpress();
        $expressComList = array_column($expressComList,"exp_name",'id');
                
        $model = new ExpressFileModel(43);
        $pageData = $model->pageList($where,$page,$pageSize);
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'batch_express_search_page';
        $this->render('batch_express_search_list.html',array(
            'pa'=>Util::page($pageData),
            'page_list'=>$pageData,
            'expressComList'=>$expressComList,//快递公司列表
        ));
    }
    
    /**
     * 上传快递单
     */
    public function uploadFile()
    {   
        $result['title'] = '快递单文件上传';
        
        $expressModel = new ExpressModel(1);
        $expressComList = $expressModel->getAllExpress();
        $expressComList = array_column($expressComList,"exp_name",'id');
        
        $result['content'] = $this->fetch('batch_express_upload_file.html',
            array(
              'expressComList'=>$expressComList,
            )
        );
        
        Util::jsonExit($result);
    }
    /**
     * 保存插入快递单文件
     */
    public function insertFile(){
        
        $result = array('error'=>'','success'=>'');
        
        $express_id = _Post::getInt('express_id');
        if(empty($express_id)){
            $result['error'] = "请选择快递公司";
            Util::jsonExit($result);
        }
        if(empty($_FILES['filename']['tmp_name'])){
           $result['error'] = "请上传数据文件"; 
           Util::jsonExit($result);
        }else if(!preg_match("/\.csv$/is",$_FILES['filename']['name'])){
            $result['error'] = "文件格式不对，请上传Excel(CSV)文件";
            Util::jsonExit($result);
        }
        $filename = $_FILES['filename']['name'];
        $tmp_name = $_FILES['filename']['tmp_name'];
        $fileModel = new ExpressFileModel(43);
        $fileDetailModel = new ExpressFileDetailModel(43);
        $depModel = new DepartmentModel(1);
        $pdo = $fileDetailModel->db()->db();
        
        //文件重复提交校验
        $file_md5 = md5_file($tmp_name);
        $existsFile = $fileModel->getRow("*","file_md5='{$file_md5}'");
        if(!empty($existsFile)){
            $result['error'] = "此文件已被用户【{$existsFile['create_user']}】在【{$existsFile['create_time']}】上传过。<hr style='margin:5px 0'/>文件单据号【{$existsFile['id']}】,请核实！";
            Util::jsonExit($result);
        }
        
        //开始事物
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
        $pdo->beginTransaction();//开启事务
        
        //添加文件记录
        $dataFile = array(
            'express_id'=>$express_id,//快递公司ID
            'filename'=>$filename,//文件名
            'file_md5'=>$file_md5,//文件md5唯一标识
            'is_print'=>0,//打印状态：未打印
            'register_time'=>0,//登记状态：未登记
            'create_user'=>Auth::$userName,
            'create_time'=>date('Y-m-d H:i:s')//上传时间
        );
        $file_id = $fileModel->saveData($dataFile,array());
        if(empty($file_id)){
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            
            $result['error'] = "操作失败，事物回滚！提示:文件记录写入失败，error:".__LINE__;
            Util::jsonExit($result);
        }
        
        //打开文件
        $file = fopen($tmp_name, 'r');
        //数据校验
        $header = array();
        $i = 0;//统计有多少行
        while ($datav = fgetcsv($file)) {
             $i ++;            
             /**********************数据验证 begin**************************/
             if(count($datav) != 6){                 
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                 
                $result['error'] = "文件数据格式不正确！请使用模板文件格式,第{$i}行";
                Util::jsonExit($result);
             }
             //批量转换字符gbk=>utf-8
             foreach($datav as $key=>$vo){
                $datav[$key] = iconv('gbk','utf-8',trim($vo));
             }
             if($i==1){
                 $header = $datav;
                 continue;
             }
         
             $sender       = $datav[0];//发货人
             $department   = $datav[1];//发货部门
             $remark       = $datav[2];//发货缘由
             $consignee    = $datav[3];//收货人
             $cons_address = preg_replace('/\s/is'," ",$datav[4]);//收货地址
             $cons_tel     = $datav[5];//收货人电话
             //验证必填项不能为空
             $error = array();
             foreach($datav as $key=>$vo){
                 if($vo==""){
                     $error[] = $header[$key]."不能为空";
                 }                 
             }
             if(!empty($error)){
                 $pdo->rollback();//事务回滚
                 $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                 $result['error'] = "第{$i}行,【".implode('】【',$error)."】";
                 Util::jsonExit($result);
             } 
             //验证发货部门是否在系统维护中存在
             $existsDep = $depModel->checkExistsByName($department);
             if(!$existsDep){
                 $pdo->rollback();//事务回滚
                 $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                 $result['error'] = "第{$i}行,发货部门【{$department}】在系统不存在。";
                 Util::jsonExit($result);
             }
             /**********************数据验证 END**************************/
             
             /**********************数据处理 BEGIN**************************/
             $detailData = array(
                 'file_id'      =>$file_id,//文件ID
                 'sender'       => $sender,//发货人
                 'department'   => $department,//发货部门
                 'remark'       => $remark,//发货缘由
                 'consignee'    => $consignee,//收货人
                 'cons_address' => $cons_address,//收货地址
                 'cons_tel'     => $cons_tel,//收货人电话
             );
             
             $detail_id = $fileDetailModel->saveData($detailData,array());
             if(empty($detail_id)){
                 $pdo->rollback();//事务回滚
                 $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                 
                 $result['error'] = "第{$i}行,数据写入数据库时失败！事物已回滚！";
                 Util::jsonExit($result);
             }
             /**********************数据处理 END**************************/
             
        }
        
        if($i<=1){
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
             
            $result['error'] = "文件数据格式不正确！请使用模板文件格式";
            Util::jsonExit($result);
        }
        //更新统计文件内 快递单数目
        $fileModel = new ExpressFileModel($file_id,43);
        $fileModel->setValue('detail_num',$i-1);
        $res = $fileModel->save();
        if(!$res){
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交             
            $result['error'] = "操作失败，事物回滚！提示:统计快递单数目失败，error:".__LINE__;;
            Util::jsonExit($result);
        }
        $pdo->commit();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        $result['success'] = 1;
        Util::jsonExit($result);
    }
    /**
     * 查看上传文件内容-快递单列表
     */
    public function viewFile($parmas)
    {
        $id = _Request::getInt('id');
        
        $fileModel = new ExpressFileModel($id,43);
        $data = $fileModel->getDataObject();
        if(empty($data)){
            echo "单据号为【 {$id}】的记录不存在";
            exit;
        }
        $expressModel = new ExpressModel(1);
        $data['company_name'] = $expressModel->getNameById($data['express_id']);
        $fileDetailModel = new ExpressFileDetailModel(43);
        $data['expresslist'] = $fileDetailModel->getList($id);
        $this->render('batch_express_view_file.html',
            array(
                'bar'=>Auth::getViewBar(),
                'id'=>$id,
                'data'=>$data,                           
            )
        );
    }
    /**
     * 批量删除快递单文件
     */
    public function deleteFile()
    {
        $result = array('error'=>'','success'=>'');
        
        $ids = _Request::getList('_ids');
        if(empty($ids)){
            $result['error'] = "缺少参数ids为空";
            Util::jsonExit($result);
        }
        
        $fileModel = new ExpressFileModel(43);
        $fileDetailModel = new ExpressFileDetailModel(43);
        
        //开启事物
        $pdo = $fileModel->db()->db();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
        $pdo->beginTransaction();
        
        
        foreach($ids as $id){
            $model = new ExpressFileModel($id,43);
            $data = $model->getDataObject();
            //begin delete
            if(!empty($data)){
                if($data['is_register']==1){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交                    
                    $result['error'] = "单据号为【{$id}】的文件已经登记过，不能删除！";
                    Util::jsonExit($result);
                }else{
                    //删除单据文件和物流单据详情
                    $res1 = $fileModel->deleteById($id);
                    $res2 = $fileDetailModel->deleteByFileId($id);
                    if(!$res1 || !$res2){
                        $pdo->rollback();//事务回滚
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                        $result['error'] = "删除单据失败，事物已回滚！";
                        Util::jsonExit($result);
                    }                    
                }
               
            }
            //end delete            
        }        
        $pdo->commit();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        $result['success'] = 1;
        Util::jsonExit($result);
    }
    /**
     * 更改打印状态
     */
    public function changePrintStatus(){
        
        $result = array('error'=>'','success'=>'');

        $id     = _Request::getInt('id');
        $status = _Request::getInt('status');
        $fileModel = new ExpressFileModel($id,43);
        $olddo = $fileModel->getDataObject();
        if(empty($olddo)){
            $result['error'] = "打印数据不存在";
            Util::jsonExit($result);
        }
        $newdo = array(
            'id'=>$id,
            'is_print'=>$status,
            'print_time'  =>date('Y-m-d H:i:s'),
        );
        $res = $fileModel->saveData($newdo, $olddo);
        if(!$res){
            $result['error'] = "修改打印状态失败";
            Util::jsonExit($result);
        }else{
            $result['success'] = 1;
            Util::jsonExit($result);
        }
        
    }
    /**
     * 登记快递单
     */
    public function registerExpress()
    {
       $result = array('error'=>'','success'=>'');
       
       $id = _Request::getInt('id');//文件单据标号（文件ID）
       $express_list = _Post::getList("freight_no");//快递单列表
       $fileModel = new ExpressFileModel($id,43);
       $fileOld = $fileModel->getDataObject();
       if(empty($fileOld)){
           $result['error'] = "操作失败，单据编号为【{$id}】的记录不存在，可能已被删除";
           Util::jsonExit($result);
       }
       $is_register = $fileOld['is_register'];//登记状态
       $express_id      = $fileOld['express_id'];//快递公司ID
       //判断单据是否登记过
       if($is_register==1){
           $result['error'] = "此单据已经登记过，请勿重复登记";
           Util::jsonExit($result);
       }
       $expressModel    = new ExpressModel(1);
       $fileDetailModel = new ExpressFileDetailModel(43);
       $shipFreightModel = new ShipFreightModel(43);
       
       $freight_rule = $expressModel->select2("freight_rule","id={$express_id}",3);
       //快递单号验证
       $freight_no_temp = array();
       $key_i = 0;
       foreach($express_list as $key=>$val){
           $key_i ++;
           //快递单号不能为空
           if(empty($val)){
               $result['error'] = "编号为【{$key_i}】的行出错：快递单号不能为空";
               Util::jsonExit($result);
           }
           //快递单号规则验证
           if(!empty($freight_rule) && !preg_match($freight_rule,$val)){
               $result['error'] = "编号为【{$key_i}】的行出错：快递单号【{$val}】格式不符合规则";
               Util::jsonExit($result);
           }
           //快递单号是否被使用过（快递单号是否在快递列表存在）
           if(!in_array($val,$freight_no_temp)){
               $freight_no_temp[] = $val;
           }else{
               $result['error'] = "编号为【{$key_i}】的行出错：快递单号【{$val}】重复";
               Util::jsonExit($result);
           }     
           $exists = $shipFreightModel->select2('count(*)',"freight_no='{$val}'",3);
           if($exists){
               $result['error'] = "编号为【{$key_i}】的行出错：快递单号【{$val}】已在快件列表存在";
               Util::jsonExit($result);
           } 
           
           
       }
       
       $pdo = $fileDetailModel->db()->db();
       $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);
       $pdo->beginTransaction();
       //数据处理
       foreach($express_list as $key=>$val){

           $fileDetailModel = new ExpressFileDetailModel($key,43);
           $fileDetailOld = $fileDetailModel->getDataObject();
           if(empty($fileDetailOld)){
               $pdo->rollback();//事务回滚
               $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
               $result['error'] = "编号为【{$key}】的行出错：快递单信息不存在，可能已被删除！";
               Util::jsonExit($result);
           }
           //保存快递单编号 begin
           $fileDetailNew = array(
               'id' =>$key,
               'freight_no'=>$val,
           );
           $res = $fileDetailModel->saveData($fileDetailNew,$fileDetailOld);
           if(!$res){
               $pdo->rollback();//事务回滚
               $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
               $result['error'] = "编号为【{$key}】的行出错：快递单信息推送快递列表失败，事物回滚！";
               Util::jsonExit($result);
           }
           $freightNew = array(
               'freight_no'=>$val,//快递单号
               'express_id'=>(int)$express_id,//快递公司ID
               'consignee' =>$fileDetailOld['consignee'],//收货人
               'cons_address' =>$fileDetailOld['cons_address'],//收货地址
               'cons_tel' =>$fileDetailOld['cons_tel'],//收货联系方式
               'remark'   =>$fileDetailOld['remark'],//发货缘由
               'is_print' =>(int)$fileOld['is_print']?1:2,//打印状态
               'print_date' =>$fileOld['print_time'],//打印时间
               'sender' =>$fileDetailOld['sender'],//发货人
               'department' =>$fileDetailOld['department'],
               'create_id' =>Auth::$userId,//操作人ID
               'create_name'=>Auth::$userName,//操作人
               'create_time'=>time(),//操作时间               
           );
           $res = $shipFreightModel->saveData($freightNew,array());
           if(!$res){
               $pdo->rollback();//事务回滚
               $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
               $result['error'] = "编号为【{$key}】的行出错：快递单信息推送快递列表失败，事物回滚！";
               Util::jsonExit($result);
           } 
       }
       //更新快递单登记状态 begin
       $fileNew = array(
           'id'         =>$id,
           'is_register'=>1,
           'register_time'=>date('Y-m-d H:i:s'),
       );
       $res = $fileModel->saveData($fileNew,$fileOld);
       if(!$res){
           $pdo->rollback();//事务回滚
           $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
           $result['error'] = "快递单保存登记状态失败，事物回滚！";
           Util::jsonExit($result);
       }

       $pdo->commit();
       $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
       $result['success'] = 1;
       Util::jsonExit($result); 
    }
    
    
    /* 打印快递单 */
    public function printExpress($params)
    {
        $id = _Request::get('id',_Request::get("_ids"));//快递单文件主键ID
        
        $fileModel = new ExpressFileModel($id,43);
        $data = $fileModel->getDataObject();
        if(empty($data)){
            echo "单据号为【 {$id}】信息不存在";
            exit;
        }
        $express_id      = $data['express_id'];
        
        $fileDetailModel = new ExpressFileDetailModel(43);
        $express_list = $fileDetailModel->getList($id);

          
        if(empty($express_id))
        {
            echo "该订单没有选择快递公司";exit;
        }
    
        //测试顺丰 不是货到付款  1是货到付款
        //$shipping_p = $order['express_id'] = 19;
        // $order['p'] = 2;
    
        $expressmodel = new ExpressModel($express_id,1);
        $express = $expressmodel->getDataObject();
        if(empty($express))
        {
            echo "此快递方式不存在";exit;
        }   
    
        if ($express['id'] == '10') {
            echo '此订单为上门取货，无需打印订单。';
            exit;
        }
        
        //快递单大小
        //$express['print_bg_size'] = array('width' => '575', 'height' => '320');
        $express['print_bg_size'] = array('width' => '1024', 'height' => '600');
        $change_addr_time = strtotime('2017-01-25');
        $express_time=time();
        $key=0;
        foreach ($express_list as $key=>$val){
            /* 标签信息 */
            $customer_province = '';
            $customer_city     = '';
            $lable_box = array();
            $lable_box['t_order_amount'] = '';
            $lable_box['t_shop_country'] = '中国'; //网店-国家
            $lable_box['t_shop_city'] = '深圳市'; //网店-城市
            $lable_box['t_shop_province'] = '广东省'; //网店-省份
            $lable_box['t_shop_name'] = ''; //网店-名称
            $lable_box['t_shop_district'] = ''; //网店-区/县
            $lable_box['t_shop_tel'] = '4008980188'; //网店-联系电话
            if (time() >= $change_addr_time) {
                $lable_box['t_shop_address'] = '广东省深圳市龙岗区南湾街道布澜路31号李朗国际珠宝园B1栋东3层'; //网店-地址
            } else {
                $lable_box['t_shop_address'] = '广东省深圳市龙岗区南湾街道布澜路31号李朗国际产业园B8栋10楼'; //网店-地址
            }
            $lable_box['t_customer_country'] = '中国'; //收件人-国家
            $lable_box['t_customer_province'] = $customer_province; //收件人-省份
            $lable_box['t_customer_city'] = "<b>".$customer_city."</b>"; //收件人-城市
            $lable_box['t_customer_city_big'] ="<b style=\"font-family: 微软雅黑;  font-size:30px\" >".$customer_city."</b>"; //收件人-城市
            $lable_box['t_customer_province_big'] ="<b style=\"font-family: 微软雅黑;  font-size:30px\">".$customer_province."</b>"; //收件人-省份
                  
            $lable_box['t_customer_tel'] = $val['cons_tel']; //收件人-电话
            $lable_box['t_customer_mobel'] = $val['cons_tel']; //收件人-手机
            $lable_box['t_customer_post'] = ''; //收件人-邮编
        
            $lable_box['t_customer_address'] = $val['cons_address']; //收件人-详细地址
            $lable_box['t_customer_name'] = $val['consignee']; //收件人-姓名
        
            $gmtime_utc_temp = time(); //获取 UTC 时间戳
            $lable_box['t_year'] = date('Y', $gmtime_utc_temp); //年-当日日期
            $lable_box['t_months'] = date('m', $gmtime_utc_temp); //月-当日日期
            $lable_box['t_day'] = date('d', $gmtime_utc_temp); //日-当日日期
        
            $lable_box['t_order_no'] = ""; //订单号-订单
            $lable_box['t_order_best_time'] = ''; //送货时间-订单
            $lable_box['t_pigeon'] = '√'; //√-对号
            $lable_box['t_duigou'] = '√'; //√-对号
            //邮政 并且 货到付款
            //$lable_box['t_chahao'] = ($order["express_id"] == 9 && $order["pay_id"] != 1) ? "" : '×'; //×-号
            $lable_box['t_chahao'] = '×'; //×-号
            $lable_box['t_ems_dagou'] = '×'; //×-号
            $lable_box['t_custom_content'] = '太白营销部';
            $lable_box['lanjian'] = '755026'; //自定义内容
        
            if($express_id == 4){
                $lable_box['t_remark'] = '转寄协议客户，必须本人签收！';
            }else{
                $lable_box['t_remark'] = '务必本人签收<br />  请当快递面拆件验货！';
            }
        
            $lable_box['t_sf_signature'] = '郭伟'; //顺风寄件人签署
            //$lable_box['t_zt_qz'] = '008'; //中通签章
			$lable_box['t_zt_qz'] = '郭伟';
            $lable_box['t_z_ems'] = "";
            $lable_box['t_z_zto'] = "<img src=http://order.kela.cn/images/receipt/z_zto.png />"; //ZTO签章
            //$lable_box['t_express_no'] = $order['invoice_no'];
            $lable_box['t_ems_bx'] = '0.1%'; //EMS保险费率
            
            $lable_box['t_pigeon'] = ''; //√-对号(不勾选货到付款)
            $lable_box['t_sf_card'] = ''; //顺风代收款卡号
            $t_order_amount = 0;
            //$cn_arr = $this->money_to_cn($t_order_amount);
            //var_dump($cn_arr);exit;
            $lable_box['t_goods_name'] = '工艺品';
            $lable_box['t_sf_work_code'] = "0 6 4 4 8 6";//员工编号
            $sf_card = "7556559853";
            $lable_box['t_send_company'] = "BDD";
            $lable_box['t_send_user'] = '郭伟';
            $lable_box['t_sf_c_code'] = $lable_box['t_sf_y_code'] = $sf_card;            
    
    
            $temp_config_lable = explode('||,||', $express['config_lable']);
            foreach ($temp_config_lable as $temp_key => $temp_lable)
            {  
                if(empty($temp_lable))
                {   
                    unset($temp_config_lable[$temp_key]);
                    continue;
                }
                $temp_info = explode(',',$temp_lable);
                if (is_array($temp_info))
                {
                    $temp_info[1] = isset($lable_box[$temp_info[0]])?$lable_box[$temp_info[0]]:'';
                }
                $temp_config_lable[$temp_key] = implode(',', $temp_info);
            }
            $express['list'][] = implode('||,||',  $temp_config_lable);

            if(in_array($express_id,array('4','18','19'))){
                    $exdata=array();
                    $exdata['id']=$val['id'];
                    $exdata['j_company'] =EXPRESS_J_COMPANY;
                    $exdata['j_contact']=EXPRESS_J_CONTACT;
                    $exdata['j_tel']=EXPRESS_J_TEL;
                    $exdata['j_address']=EXPRESS_J_ADDRESS;
                    $exdata['goods_name']=EXPRESS_GOODS_NAME;        
                    $exdata['d_company']=$lable_box['t_customer_name'];
                    $exdata['d_contact']=$lable_box['t_customer_name'];
                    $exdata['d_tel']=$lable_box['t_customer_tel'];
                    $exdata['d_address']=$lable_box['t_customer_address'];
                    

                    $file_path = APP_ROOT."shipping/modules/express_api/Express_api.php";
                    require_once($file_path);
                    $key++;
                    $expresslistmodel=new ExpressListModel(43);
                    if(!empty($val['express_order_id']))
                        $express_order_id=$val['express_order_id'];
                    else{
                        $olddo = array();
                        $newdo=array(
                            'address'=>$exdata['d_address'],
                            'd_tel'=>$exdata['d_tel'],
                            'd_contact'=>$exdata['d_contact'],
                            "express_id"=>$express_id,              
                            'create_time'=>date('Y-m-d H:i:s'),
                            'create_user'=>$_SESSION['userName'],           
                        );                                  
                        $express_order_id =$expresslistmodel->saveData($newdo,$olddo);
                    }
                    //$express_order_id= !empty($val['express_order_id']) ?  $val['express_order_id'] : ($express_time+$key) .rand(10,99);
                    $res=Express_api::makeOrder($express_order_id,$express_id,$exdata);
                    if($res['result']==1){
                        $exdata=array_merge($exdata,$res);
                        $express['data'][]=$exdata;                                           
                        $expresslistmodel->updateExpressNO($express_order_id,$res['express_no']);                      
                    }else{
                        exit($res['error']);
                    }                   
            }

        }

        $print_template='batch_express_print.html';
        if(isset($express['data']))
            $print_template='batch_express_print_api.html';
        $this->render($print_template,array(
            'id'  =>$id,
            'express'=>$express,
			'express_id'=>$express_id,
        ));
    }    
    
    /**
     * 修改快递公司（渲染页面）
     */
    public function editCompany()
    {
        $result['title'] = '修改快递公司';
        
        $id = _Post::getInt('id');
        $expressFileModel = new ExpressFileModel($id,43);
        $data = $expressFileModel->getDataObject();
        if(empty($data)){
            $result['content'] = "选中数据记录不存在，可能已经被删除！";
            Util::jsonExit($result);
        }else if($data['is_register']==1){
            $result['content'] = "该单据已经登记过，不能修改快递公司！";
            Util::jsonExit($result);
        }
        $expressModel = new ExpressModel(1);
        $expressComList = $expressModel->getAllExpress();
        $expressComList = array_column($expressComList,"exp_name",'id');
        $result['content'] = $this->fetch('batch_express_company_edit.html',
            array(
                'expressComList'=>$expressComList,
                'data'=>$data
            )
        );
       
        Util::jsonExit($result);
    }
    /**
     * 保存快递公司
     */
    public function updateCompany()
    {
        $result = array('error'=>'','success'=>'');
        $id = _Post::getInt('id');
        $express_id = _Post::getInt('express_id');
        if(empty($express_id)){
            $result['error'] = "请选择快递公司！";
            Util::jsonExit($result);
        }
        
        $expressFileModel = new ExpressFileModel($id,43);
        $olddo = $expressFileModel->getDataObject();
        if(empty($olddo)){
            $result['error'] = "修改失败：数据记录不存在，可能已经被删除！";
            Util::jsonExit($result);
        }
        $newdo = array(
            'id' =>$id,
            'express_id'=>$express_id,
        ); 
        $res = $expressFileModel->saveData($newdo, $olddo);
        if($res){
            $result['success'] = 1;
            Util::jsonExit($result);
        }else{
            $result['error'] = "修改失败！";
            Util::jsonExit($result);
        }
    }
    /**
     * 导出快递单
     */
    public function exportExpress()
    {
        $id = _Request::getInt('id',_Request::getInt("_ids"));
        
        $fileModel = new ExpressFileModel($id,43);
        $fileData = $fileModel->getDataObject();
        if(empty($fileData)){
            echo "单据号为【 {$id}】的记录不存在";
            exit;
        }
        $expressModel = new ExpressModel(1);
        $company_name = $expressModel->getNameById($fileData['express_id']);//快递公司名称
        
        $fileDetailModel = new ExpressFileDetailModel(43);
        $data = $fileDetailModel->getList($id);        
        
        $title = array(
            "单据编号",
            "编号",
            "快递单号",
            "寄件人",
            "寄件部门",
            "发件缘由",
            "收件人",
            "收件地址",
            "联系电话",
            "快递公司"
        );        
        $datalist=array();
        foreach($data as $k=>$v){
            $datalist[$k]['file_id'] = $fileData['id'];
            $datalist[$k]['id']      = $k+1;
            $datalist[$k]['freight_no'] = $v['freight_no'];
            $datalist[$k]['sender'] = $v['sender'];
            $datalist[$k]['department'] = $v['department'];
            $datalist[$k]['remark'] = $v['remark'];
            $datalist[$k]['consignee'] = $v['consignee'];
            $datalist[$k]['cons_address'] = $v['cons_address'];
            $datalist[$k]['cons_tel'] = $v['cons_tel'];
            $datalist[$k]['company_name'] = $company_name;
        }        
        Util::downloadCsv("快递单报表(单据编号{$fileData['id']})",$title,$datalist);
    }
    /**
     * 下载快递单模板
     */
    public function downloadTpl()
    {
        $title = array(
            "寄件人",
            "寄件部门",
            "发件缘由",
            "收件人",
            "收件地址",
            "联系电话"
        );
        Util::downloadCsv("快递单模板",$title,'');
    }
    
}
?>