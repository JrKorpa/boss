<?php

/**
 *  -------------------------------------------------
 *   @file		: AppProcessorInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 17:38:20
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorAInfoController extends CommonController {

    protected $smartyDebugEnabled = true;

    /**
     * 	index，搜索框
     */
    public function index($params) {
        $view = new AppProcessorAInfoView(new AppProcessorAInfoModel(13));
        $this->render('app_processor_info_search_form.html',
            array('view' =>$view, 'bar' => Auth::getBar()));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            //'name' => _Request::getInt('name'),
        	'name' => _Request::getString('name'),
            'business_scope'=> _Request::getString('business_scope'),
            'start_time'=> _Request::getString('start_time'),
            'end_time'=> _Request::getString('end_time'),
            'status'=> _Request::getInt('status'),
            'pro_contact'=> _Request::getString('pro_contact'),
            'opra_uname' => _Request::get('opra_uname')
        );
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
        $where = array();
        $where['name'] = $args['name'];
   //     $where['id'] = $args['name'];
        $where['business_scope'] = $args['business_scope'];
        $where['start_time'] = $args['start_time'];
        $where['end_time'] = $args['end_time'];
        $where['status'] = $args['status'];
        $where['pro_contact'] = $args['pro_contact'];
        $where['opra_uname'] = $args['opra_uname'];
        $model = new AppProcessorAInfoModel(13);
        $data = $model->pageList($where, $page, null, false);
        if ($data['data']) {
            foreach ($data['data'] as $key => &$value) {
                $value['status'] = $model->getStatusList($value['status']);
            }
            unset($value);
        }
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_processor_info_search_page';
        $this->render('app_processor_info_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }

    /**
     * 	grounp，渲染关联供应商页面
     */
    public function grounp($params) {
        $id = intval($params["id"]);
        $result = array('success' => 0, 'error' => '');
        $model = new AppProcessorAInfoModel($id,14);
        $view = new AppProcessorAInfoView($model);
        $res = $model->hasGroup($id);
        $suppliers = array();
        if($res){
            $suppliers = $view->get_group($res);
            unset($suppliers[$id]);
        }

        $result['content'] = $this->fetch('app_processor_info_group.html', array(
            'view' =>$view,'supplier_sum'=>$suppliers
        ));
        $result['title'] = '管理关联供应商';
        Util::jsonExit($result);
    }

    /**
     * saveGroup，保存关联供应商
     */
    public function saveGroup()
    {
        $result = array('success' => 0,'error' =>'');
        $id = _Post::getInt('id');
        $now = _Post::getList('data');
        $model = new AppProcessorAInfoModel(14);
        $res = $model->hasGroup($id);
        $group_id = $res?$res:$model->mkGroupId();

        $old = array();
        $tmp = $model->getGroup($group_id);
        if(!empty($tmp)){
            foreach ($tmp as $v) {
                $old[]= $v['supplier_id'];
            }
        }

        $del = array_diff($old,$now);		//删除供应商
        $new = array_diff($now,$old);		//新增供应商

        //新增关联供应商
        if(!empty($new)){
            foreach ($new as $v) {
                $res = $model->intoGroup($v,$group_id);
            }
        }
        //删除关联供应商
        if(!empty($del)){
            foreach ($del as $v) {
                $res = $model->delGroup($v,$group_id);
            }
        }

        if($res !== false)
        {
            $result['success'] = 1;
        }
        else
        {
            $result['error'] = '操作失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	show，渲染查看页面
     */
    public function show($params) {
        $id = intval($params["id"]);
        $row = new AppProcessorAInfoView(new AppProcessorAInfoModel($id, 13));
        $business_license_region = $row->get_business_license_region();
        //营业执照地址
        $business_region_data = array('pro_province_name'=>'','pro_area_name'=>'','pro_city_name'=>'');
        if($business_license_region){
            $business_region_arr = explode(",", $business_license_region);
            $regionModel = new RegionModel(1);

            $province_id = $business_region_arr[0];
            $area_id = $business_region_arr[1];
            $city_id = $business_region_arr[2];

            $province_name = $regionModel->getReginName($province_id);
            $area_name = $regionModel->getReginName($area_id);
            $city_name = $regionModel->getReginName($city_id);
            $business_region_data = array('province_name'=>$province_name,'area_name'=>$area_name,'city_name'=>$city_name);
        }

        //取货地址:不是必填项可能为空
        $pro_region = $row->get_pro_region();
        $pro_region_data = array('pro_province_name'=>'','pro_area_name'=>'','pro_city_name'=>'');
        if($pro_region){
            $pro_region_arr = explode(",", $pro_region);
            $pro_province_id = $pro_region_arr[0];
            $pro_area_id = $pro_region_arr[1];
            $pro_city_id = $pro_region_arr[2];
            $pro_province_name = $regionModel->getReginName($pro_province_id);
            $pro_area_name = $regionModel->getReginName($pro_area_id);
            $pro_city_name = $regionModel->getReginName($pro_city_id);
            $pro_region_data = array('pro_province_name'=>$pro_province_name,'pro_area_name'=>$pro_area_name,'pro_city_name'=>$pro_city_name);
        }
        $this->render('app_processor_info_show.html', array(
            'view' => $row,'dict'=>new DictView(new DictModel(1)),
            'business_region_data'=>$business_region_data,
            'pro_region_data'=>$pro_region_data,
            'bar' => Auth::getViewBar(),
            'bar1'=>  Auth::getDetailBar('app_processor_worktime')
        ));
    }

    /**
     * 查看关联供应商
     */
    public function showgroup($params){
        $id = intval($params["id"]);
        $result = array('success' => 0, 'error' => '');
        $model = new AppProcessorAInfoModel(13);
        $view = new AppProcessorAInfoView($model);
    $group_id = $model->hasGroup($id);
        if($group_id){
            $suppliers = $view->get_group($group_id);
            $result['content'] = $this->fetch('app_processor_show_group.html',['supplier'=>$suppliers]);
        }else{
            $result['content'] = $this->fetch('app_processor_show_group.html');
        }
        $result['title'] = '查看关联供应商';
        Util::jsonExit($result);

    }

    /**
     * 	enabled启用
     */
    public function enabled($params) {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new AppProcessorAInfoModel($id, 14);

        $model->setValue('status', 1);
        $res = $model->save(true);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "操作失败";
        }
        Util::jsonExit($result);
    }

    /**
     * 	disabel禁用
     */
    public function disabel($params) {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new AppProcessorAInfoModel($id, 14);

        $model->setValue('status', 2);
        $res = $model->save(true);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "操作失败";
        }
        Util::jsonExit($result);
    }

    /**
     * supplierList,供应商列表
     */
    public function supplierList ()
    {
        $model = new AppProcessorAInfoModel(14);
        $data = $model->getList();
        Util::jsonExit($data);
    }


    /**
    * 给工厂绑定跟单人 渲染添加页面
    * @author hulichao
    * @date 2015-4-21
    */
    public function BingOpraUser($params){
        $id = intval($params["id"]);        //工厂ID
        //获取用户列表
        $user = new UserModel(2);
        $userlist = $user->getUserInfo();

        //获取供应商名称
        $proModel = new AppProcessorAInfoModel($id , 13);
        $proName = $proModel->getValue('name');

        //获取当前供应商绑定的跟单人信息
        $model = new ProductFactoryOprauserModel(13);
        $user_select = $model->select2('`opra_user_id`,`opra_uname`,`production_manager_id`,`production_manager_name`' , "`prc_id`={$id}" , 'row');

        $this->render('product_factory_oprauser_info.html', array(
            'userlist'=>$userlist,
            'proName'=> $proName,
            'pro_id' => $id,
            'user_select' => $user_select,
        ));
    }

    /**
    * 绑定跟单人
    */
    public function BingOpraUserAction($params){
        $result = array('success' => 0 , 'error' => '');
        $kela_user = intval($params["kela_user"]);          //选中绑定的跟单人ID
        $pro_id = intval($params["pro_id"]);      //工厂ID
        $production_manager_id = intval($params["production_manager_id"]);//生产经理ID
        $model = new ProductFactoryOprauserModel(14);
        $res = $model->BingGendanMan($pro_id , $kela_user,$production_manager_id);
        if($res){
            $result['success'] = 1;
            $result['error'] = '操作成功';
        }else{
            $result['error'] = '操作失败';
        }
         Util::jsonExit($result);
    }


    /*
    *批量维护工作日
    *显示页面
    */
    public function BatAlterWorkday($params){

            $ids = _Post::getList('_ids');
            $appmodel = new AppProcessorWorktimeModel(14);
            $v =new AppProcessorWorktimeView(new AppProcessorWorktimeModel(14));
            $suppList= $appmodel->getSupplierInfoByIds($ids);
            // $suppList = array_unique($suppList);
            //获取所选供应商的名称    
            $this->render('app_processor_info_edit.html', array(
            'ids'=>$ids,
            'suppList'=>$suppList,
            'view'=>$v,
            ));

    }


    /*
    *批量更新上班时间和放假时间
    *
    */

    public function bat_save(){

        //获取所选供应商的ID
        $args =array(
            'ids'=> _Request::getList('pros'),
            'order_type'=> _Request::getString('order_type'),
            'is_rest'=> _Request::getInt('is_rest'),
            'is_work'=> _Request::getString('is_works'),
            'holiday_time'=> _Request::getString('holiday_times'),
            );
        if(count($args['ids'])==0){
              $result['error'] = '渠道名称不能为空!';
              Util::jsonExit($result);
        }
        
        $appmodel = new AppProcessorWorktimeModel(14);

         // 批量更新多个供应商的ID更新
        $promodel = new ProductInfoModel(14);

        $pdo14 = $appmodel->db()->db();
        // $pdo14 = $promodel->db()->db();

        $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
        $pdo14->beginTransaction();  //开启事务

        try{
                 if($args['is_rest'] ==4){
                    //不更新周末休息时间
                    if($args['order_type'] =='all'){
                        for($i=1;$i<3;$i++){
                            $args['order_type']=$i;
                            $res = $appmodel->updateProcessorWorktimeById($args,2,$i);
                        }
                    }else{
                            $res = $appmodel->updateProcessorWorktimeById($args,2,$args['order_type']);
                    }
                    
                }else{
                      if($args['order_type'] =='all'){
                        for($i=1;$i<3;$i++){
                            $args['order_type']=$i;
                            $res = $appmodel->updateProcessorWorktimeById($args,1,$i);
                        }
                    }else{
                            $res = $appmodel->updateProcessorWorktimeById($args,1,$args['order_type']);
                    }
                    // $res = $appmodel->updateProcessorWorktimeById($args,1);
                }
           
            //获取之前的周末休息、周末上班、周末放假
            
            if(!$res){
                throw new Exception('批量更新供应商信息失败!');
            }

         //接收到更新的需要新增的时间 出厂时间=当天+放假日期-周末上班+周末休息+（有/DIA）无款起版周期
            foreach($args['ids'] as $k=>$v){
                if($args['order_type'] =='all'){
                    for($i=1;$i<3;$i++){
                        $res = $this->updateEsmttimeByPrc_id($v,$i);
                    }
                }else{
                    $res = $this->updateEsmttimeByPrc_id($v,$args['order_type']);

                }

                if(!$res){
                    throw new Exception('出厂时间更新失败!');
                }
            }
        }catch(Exception $e){
            $pdo14->rollback(); //事务回滚
            $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            $result['error'] = '批量更新失败!';
            Util::jsonExit($result);
        }
        
         $pdo14->commit(); //事务提交
         $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
        // $res1 = $promodel->UpateMoreEsmttimeByPrc_id($args['ids'],$up_time,2);
        $result['success'] = 1;
        Util::jsonExit($result);
    }

    /*
    *格式化上班日期和放假日期
    *
    */

    public function formatData(){
        $times = _Request::getString("times");
        $time_arr = explode(';', $times);
        $time_arr=array_filter(array_unique($time_arr));
        Util::jsonExit(implode(';', $time_arr));

    }


   /*
    *更加供应商ID更新出厂时间
    *
    */
    public function updateEsmttimeByPrc_id($prc_id,$order_type=1){
        
        $newmodel = new AppProcessorWorktimeModel(14);
        $productModel = new ProductInfoModel(14);
        $stylemodel = new StyleModel(11);
        $purchasemodel = new PurchaseModel(23);
        $from_type = $order_type==1?2:1;
        $proInfos = $productModel->getBuChanInfoByPrc_id($prc_id,$from_type);
        $now = date('Y-m-d',time());
        foreach($proInfos as $k=>$v){
            $is_fj = false;//标准出厂时间是否小于一个供应商放假日期、周末上班日期boss_1324
            //更新出厂时间:未出厂 && 出厂时间大于当前时间
            //if(in_array($v['status'], array('1','2','3','4','5','6')) && $v['esmt_time'] >= $now){
            $infos = $newmodel->getProcessorInfoByTypeAndId($prc_id,$order_type);
            $esmt_time = $v['esmt_time'];
            if($infos['holiday_time']){
                $fangjia = explode(';', $infos['holiday_time']);
            }
            if($infos['is_work']){
                $zmfangjia = explode(';', $infos['is_work']);
            }
            $fjData = array();
            if(empty($fangjia)){
                $fjData = $zmfangjia;
            }
            if(empty($zmfangjia)){
                $fjData = $fangjia;
            }
            if(!empty($fangjia) && !empty($zmfangjia)){
                $fjData = array_merge($fangjia, $zmfangjia);
            }
            foreach ($fjData as $key => $value) {
                if($esmt_time >= $value){
                    $is_fj = true;//有大于等于一个供应商放假日期、周末上班日期
                }
            }
            if(in_array($v['status'], array('4','7')) && $is_fj == true){//4、生成中，7部分出厂
                
                if($order_type==1){
                    //查找客订单是否有起版号
                    $qiban_exists = $purchasemodel->getQiBanInfosByStyle_Sn($v['style_sn'],$v['p_sn']);
                    if($v['style_sn'] =='QIBAN' && $qiban_exists){
                        //无款起版
                        $cycle = $infos['wkqbzq'];
                    }else{
                        //成品:款式库存在,起版列表没有
                        //起版列表信息
                        if($v['style_sn'] !='QIBAN'){
                            if(empty($qiban_exists)){
                                //成品(更新)
                                $cycle = $infos['normal_day'];
                            }else{
                                //有款起版(更新)
                                $cycle = $infos['ykqbzq'];
                            }
                        }
                    }
                }else{
                    $is_style = $purchasemodel->getStyleInfoByCgd($v['p_sn']);
                    if($is_style ==1){
                        //采购列表  --有款采购
                        $cycle = $infos['ykqbzq'];
                    }elseif($is_style ==0){
                        //采购列表  --无款采购
                        $cycle = $infos['wkqbzq'];
                    }else{
                        //采购列表  --标准采购
                        $cycle = $infos['normal_day'];
                    }
                }
                // $add_days = strtotime($v['order_time']) +intval($cycle)*3600*24;
                $order_time = strtotime($v['order_time']);
                for($i=0;$i<=$cycle;$i++){
                    $day = date('Y-m-d',strtotime('+'.$i.' day',$order_time));
                        //放假日期
                        if(strpos($infos['holiday_time'],$day) !== false){
                                // $add_days +=3600*24;
                                $cycle = $cycle+1;
                                continue;
                        }
                        //暂时只能获得周末休息天数(默认周天休息)
                        switch ($infos['is_rest']) {
                            case '1':
                                break;
                            case '2':
                                //单休有周末就后延后一天
                                if(date('w',strtotime($day))== 0){
                                    // $add_days +=3600*24;
                                    $cycle = $cycle+1;
                                }
                                break;
                            default:
                                //双休遇到周末就延后两天
                                if(date('w',strtotime($day))== 6 || date('w',strtotime($day))== 0){
                                    // $add_days +=3600*24;
                                    $cycle = $cycle+1;
                                }
                                break;
                        }
                    //周末上班
                    if(strpos($infos['is_work'],$day) !== false && strpos('60',date('w',strtotime($day))) !== false){
                            // $add_days =$add_days-3600*24;
                            $cycle = $cycle-1;
                    }
            
                }
                    // $esmt_time =date('Y-m-d',$add_days);
                    $res = $productModel->updateEsmttime($v['id'],$day,1);
                    if(!$res){
                        return false;
                    }
            }
            
        }
        return true;
    }



}?>