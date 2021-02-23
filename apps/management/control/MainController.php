<?php
/**
 *  -------------------------------------------------
 *   @file		: MainController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		:
 *   @update	:
 *  -------------------------------------------------
 */
class MainController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist=array('index');

	/**
	 *	管理首页
	 */
	function index ($params)
	{
		$this->render("index.html");
	}

	/*
	*	getMenu,获取菜单
	*/
	public function getMenu ()
	{
		$this->render("index_left.html",array('menu_data'=>Auth::getLeftTree(Auth::$userId)));
	}

	/*
	*	dashboard,缺省展示页
	*/
	public function dashboard ()
	{       
		$first_login_show_sale_data=0;
		if(empty($_COOKIE['first_login_time'])){
			setcookie('first_login_time',time(),strtotime(date('Y-m-d').' 23:59:59'));
			if(!empty($_SESSION['companyId'])){
				$company_view=new CompanyView( new CompanyModel($_SESSION['companyId'],1));
				$company_type=$company_view->get_company_type();
				if($_SESSION['companyId']!=58 && ($company_type==1 || $company_type==2))
					$first_login_show_sale_data=1;
			}			
		}
		$file_list = @scandir(KELA_ROOT."/public/table_file/");
		foreach ($file_list as $key => $f) {
			if($f=='.' || $f=='..')
				unset($file_list[$key]);
			else
			    $file_list[$key] = $f;//iconv("gbk","utf-8",$f);
		}
		
		$this->render("index_right.html",array('first_login_show_sale_data'=>$first_login_show_sale_data,'file_list' => $file_list));
	}

	/**
	 *	showInfo，个人信息查看界面
	 */
	public function showInfo ()
	{
		$result = array('success' => 0,'error' => '');
		$id = Auth::$userId;
		$view = new UserView(new UserModel($id,1));
		$company_id= $view->get_company_id();
		
		//获取所在公司
		$apiWareHourse = new ApiWarehouseModel();
		$company_all = $apiWareHourse->getCompanyAll();
		
		$company_all = array_column($company_all,'company_name','company_id');
		
		//获取当前所在公司
		if(!empty($company_id) && !empty($company_all[$company_id])){
		    $data['company_name'] = $company_all[$company_id];
		}else{
		    $data['company_name'] = '';
		}
		
		//获取所在公司列表
		$userCompanyModel = new UserCompanyModel(1);
		$has_company = $userCompanyModel->getUserCompanyList(array('user_id'=>$id));
		$has_company = array_column($has_company,'company_id');
		$str = '';
		foreach ($has_company as $vo){
		    if(isset($company_all[$vo])){
		        $str .=$company_all[$vo].',';
		    }
		}
		$data['has_company'] = trim($str,',');
		
		
		$result['content'] = $this->fetch('personal.html',array(
			'view'=>$view,
		    'data'=>$data
		));
		$result['title']='查看用户信息';
		Util::jsonExit($result);
	}


        public  function  help(){
                $this->render("help.html");
        }
		
		public  function  HelpAction(){
                $this->render("help.html");
        }

	public function changePass ()
	{
		$result = array('success' => 0,'error' => '');
		$result['title'] = '修改密码';
		$result['content'] = $this->fetch('password.html');
		Util::jsonExit($result);
	}

	/**
	 *	修改个人密码
	 */
	public function modifyPass ()
	{
		$result = array('success' => 0,'error' => '');
		$oldPass = _Post::get('oldPass');
		$newPass = _Post::get('newPass');
		$confirmPass = _Post::get('confirmPass');
		if($oldPass=='')
		{
			$result['error'] ="请正确填写原密码！";
			Util::jsonExit($result);
		}
		if($newPass=='')
		{
			$result['error'] ="请设置密码！";
			Util::jsonExit($result);
		}
		if(mb_strlen($newPass)<6)
		{
			$result['error'] ="密码太短！";
			Util::jsonExit($result);
		}
		if(mb_strlen($newPass)>20)
		{
			$result['error'] ="密码太长！";
			Util::jsonExit($result);
		}
		if($newPass!=$confirmPass)
		{
			$result['error'] ="密码不一致！";
			Util::jsonExit($result);
		}
		if($newPass==$oldPass)
		{
			$result['error'] ="新密码与原密码不能相同！";
			Util::jsonExit($result);
		}
		$model = new UserModel($_SESSION['userId'],2);
		$do = $model->getByAccount($_SESSION['userName']);
		if($do['password']!=Util::xmd5($oldPass))
		{
			//$result['error'] ="原密码不正确！";
			//Util::jsonExit($result);
		}
		$model->setValue('password',Util::xmd5($newPass));
		$res = $model->save(true);
		if($res !== false)
		{
			$result['success'] = 1;
			//AsyncDelegate::dispatch('opslog', array('event' => 'user_upserted', 'user_id' => $_SESSION['userId']));
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	public function getChart1 ()
	{
		$data = array(
			array('name'=>'夜店','data'=>array(2, 8, 57, 113, 170, 220, 248, 241, 201, 141, 86, 25)),
			array('name'=>'小卖店','data'=>array(23, 42, 57, 85, 119, 152, 170, 166, 142, 103, 66, 48)),
			array('name'=>'黑店','data'=>array(9, 6, 35, 84, 135, 170, 186, 179, 143, 90, 39, 10))
		);
		Util::jsonExit($data);
	}


	public function getChart2 ()
	{
		$data = array(
			array('name'=>'笨蛋','data'=>array(2, 8, 57, 113, 170, 220, 248, 241, 201, 141, 86, 25)),
			array('name'=>'毛蛋','data'=>array(23, 42, 57, 85, 119, 152, 170, 166, 142, 103, 66, 48)),
			array('name'=>'混蛋','data'=>array(9, 6, 35, 84, 135, 170, 186, 179, 143, 90, 39, 10))
		);
		Util::jsonExit($data);
	}

	public function getChart3 ()
	{
		$data = array(
			array('北京西单店',23.7),
			array('上海明珠店',16.1),
			array('深圳地王大厦店',14.2),
			array('新疆乌鲁木齐店',14.0),
			array('黑龙江齐齐哈尔店',12.5),
			array('内蒙古呼伦贝尔盟店',13.7),
			array('广西拉布拉多店',33.7),
			array('日本东京坑你没商量店',43.7),
			array('美国底特律店',22.7),
			array('加拿大堪培拉店',28.7),
			array('火星你不敢来店',53.7),
			array('水星乌托邦店',3.7),
			array('马航没有你想不到的店',63.7)
		);
		Util::jsonExit($data);
	}

	public function getChart4 ()
	{
		$data = Util::statControlNo();
		$datas = array();
		foreach ($data as $key => $val )
		{
			if($val['label']=='management')
			{
				$obj =new stdClass();
				$obj->name=$val['label'];
				$obj->y=$val['data'];
				$obj->sliced=true;
				$obj->selected=true;
				$datas[$key] = $obj;
			}
			else
			{
				$datas[$key] = array($val['label'],$val['data']);
			}
		}
		Util::jsonExit($datas);
	}

	public function getChart5 ()
	{
		$data = Util::statControlSize();
		$datas = array();
		foreach ($data as $key => $val )
		{
			if($val['label']=='management')
			{
				$obj =new stdClass();
				$obj->name=$val['label'];
				$obj->y=$val['data'];
				$obj->sliced=true;
				$obj->selected=true;
				$datas[$key] = $obj;
			}
			else
			{
				$datas[$key] = array($val['label'],$val['data']);
			}
		}
		Util::jsonExit($datas);
	}

	public function getChart6 ()
	{
		$obj1 = new stdClass();
		$obj1->enabled=false;
		$obj = new stdClass();
		$obj->name='其他';
		$obj->y=1;
		$obj->dataLabels=$obj1;

		$data = array(
			array('实习生',2),
			array('初级程序员',13),
			array('中级程序员',13),
			array('高级程序员',1),
			array('架构师',1),
			$obj
		);

		Util::jsonExit($data);
	}

	public function getChart7 ()
	{
		$obj =new stdClass();
		$obj->name='架构组';
		$obj->y=3;
		$obj->sliced=true;
		$obj->selected=true;

		$datas = array();
		$datas[]=$obj;
		$datas[]=array('前端组',7);
		$datas[]=array('后端组',5);
		$datas[]=array('闲人',1);
		Util::jsonExit($datas);
	}

	/*
	*	统计一个月内访问量形成折线图
	*/
	public function getChart8 ()
	{
		$date = getdate ();
		$start = mktime (0,0,0,$date['mon'],1, $date['year']);
		$sql = "SELECT create_time FROM `system_access_log` WHERE `create_time` BETWEEN '".$start."' AND '".time()."'";
		$data = DB::cn(1)->getAll($sql);

		$end = mktime (0,0,0,$date['mon']+1,1, $date['year'])-1;
		$datas = array_fill_keys (array_values(range(1,date('j',$end))),0);

		foreach ($data as $val )
		{
			$datas[date('j',$val['create_time'])]++;
		}
		$data = array();
		$data['name'] = '访问量';
		$obj =new stdClass();
		$obj->symbol='diamond';
		$data['marker'] = $obj;
		$data['data'] = array();

		foreach ($datas as $key => $val )
		{
			$data['data'][] = $val;
		}
		Util::jsonExit(array($data));
	}
	
	//切换所在公司
	public function changeCompany(){

	    
	    $user_id = Auth::$userId;
	    $model = new UserModel($user_id,1);
	    $user = $model->getDataObject();
	    //提交submit表单
	    if (isset($_POST['company_id'])){
	        $olddo = $user;
	        $newdo = array(
	            'id' => $user_id,
	            'company_id'=>_Post::get('company_id')
	        );
	         
	        if(empty($newdo['company_id'])){
	            $result['error'] ='所在公司不能为空';
	            Util::jsonExit($result);
	        }
	         
	        $res = $model->saveData($newdo,$olddo);
	        if($res !== false){	            
	            Auth::reLogin();	            	            
	            $result['success'] = 1;
	            Util::jsonExit($result);
	        }else{
	            $result['error'] ='修改失败';
	            Util::jsonExit($result);
	        }
	    }else {
	
	        //获取所在公司
	       
	        $companyModel     = new CompanyModel(1);
		    $company_all   = $companyModel->getCompanyTree();//公司列表
	        	
	        $company_all = array_column($company_all,'company_name','id');
          
	        //获取所在公司列表
			
	        $userCompanyModel = new UserCompanyModel(1);
	        $data = $userCompanyModel->getUserCompanyList(array('user_id'=>$user_id));
	        $has_company = array();
            if(!empty($data)){
    	        $has_company_ids = array_column($data,'company_id');
	        
    	        foreach($has_company_ids as $vo){
    	            if(isset($company_all[$vo])){
    	                $has_company[$vo] = $company_all[$vo];
    	            }
    	        }
            } 
            //if($_SESSION['userType']!=1)
            	$company_all=$has_company;
			

	        $content = $this->fetch('change_company.html',array(
	            'user'=>$user,
	            'has_company'=>$company_all
	        ));
	        
	        $result['title']   = '切换所在公司';
	        $result['content'] = $content;
	        Util::jsonExit($result);
	    }
	}

	public function first_login_show_sale_data(){
        $userModel1 = new UserModel(1);
        $userModel111 = new UserModel(111);
        $date_start = date('Y-m').'-01';
        //$date_start ='2017-12-01';
        $date_end   = date('Y-m-d');
        //天生一对有销售记录名单
        $tsyd_saler_list=array();
        $xy_saler_list=array();
        $xxbl_saler_list=array();
        $sys1 = '';
        $sys111='';
        if(SYS_SCOPE=='boss'){
	        $sys1 = 'boss';
	        $sys111='zhanting';             
        }
        if(SYS_SCOPE=='zhanting'){
	        $sys1 = 'zhanting';
	        $sys111='boss';             
        }        

        //获取各店销售业绩第一名
        $res1 = $userModel1->get_sale_goods_amount_top($date_start,$date_end,$sys1);
        $res111 = $userModel111->get_sale_goods_amount_top($date_start,$date_end,$sys111);
        $goods_amount_list=array_merge($res1,$res111);
        $key_list=array();
        //echo "<pre>";print_r($res1);
        foreach ($goods_amount_list as $key => $v) {
        	$key_list[$key] = $v['goods_amount'];
        }
        array_multisort($key_list,SORT_DESC,$goods_amount_list);

        //统计天生一对排行
        $res1   = $userModel1->get_sale_goods_tsyd($date_start,$date_end,$sys1);
        $res111 = $userModel111->get_sale_goods_tsyd($date_start,$date_end,$sys111);
        $res_list = array_merge($res1,$res111);
        $res_list = array_filter($res_list, function($item){ 
                 return $item['allnum'] >= 2; 
            });
        $key_list = array();
        //echo "<pre>";print_r($res1);
        $limit_list=array();
        $tsyd_saler_list=array();
        if(!empty($res_list)){
	        foreach ($res_list as $key => $v) {
	        	$key_list[$key] = $v['allnum'];
	        }
	        array_multisort($key_list,SORT_DESC,$res_list);
	        $limit_list = array();
	        for($i=0;$i<count($res_list);$i++) {
	        	if($i<9){
	        		$limit_list[] = $res_list[$i];
	        	}else{
	        		if($i==9){
	        			$limit_list[] = $res_list[$i];
	        			$tmp = $res_list[$i]['allnum'];
	        		}else{        			
	                    if($res_list[$i]['allnum']==$tmp)
	                    	$limit_list[] = $res_list[$i];
	        		}
	        	}
	        	if($res_list[$i]['allnum']>0)
	        		$tsyd_saler_list[]=$res_list[$i]['create_user'];
	        }
        }  
        $tsyd_list = $limit_list;
 

        //统计星耀排行
        $res1   = $userModel1->get_sale_goods_xy($date_start,$date_end,$sys1);
        $res111 = $userModel111->get_sale_goods_xy($date_start,$date_end,$sys111);
        $res_list = array_merge($res1,$res111);
        $res_list = array_filter($res_list, function($item){ 
                 return $item['allnum'] >= 2; 
            });        
        $key_list = array();
        //echo "<pre>";print_r($res1);
        $limit_list = array();
        $xy_saler_list=array();
        if(!empty($res_list)){
	        foreach ($res_list as $key => $v) {
	        	$key_list[$key] = $v['allnum'];
	        }
	        array_multisort($key_list,SORT_DESC,$res_list);
	        $limit_list = array();
	        for($i=0;$i<count($res_list);$i++) {
	        	if($i<9){
	        		$limit_list[] = $res_list[$i];
	        	}else{
	        		if($i==9){
	        			$limit_list[] = $res_list[$i];
	        			$tmp = $res_list[$i]['allnum'];
	        		}else{
	                    if($res_list[$i]['allnum']==$tmp)
	                    	$limit_list[] = $res_list[$i];
	        		}
	        	}
	        	if($res_list[$i]['allnum']>0)
	        		$xy_saler_list[]=$res_list[$i]['create_user'];        	
	        }
	    }    
        $xy_list = $limit_list;        


        //统计香榭巴黎排行
        $res1   = $userModel1->get_sale_goods_xxbl($date_start,$date_end,$sys1);
        $res111 = $userModel111->get_sale_goods_xxbl($date_start,$date_end,$sys111);
        $res_list = array_merge($res1,$res111);
        $res_list = array_filter($res_list, function($item){ 
                 return $item['allnum'] >= 2; 
            });        
        $key_list = array();
        //echo "<pre>";print_r($res1);
        $limit_list = array();
        $xybl_saler_list=array();
        if(!empty($res_list)){
	        foreach ($res_list as $key => $v) {
	        	$key_list[$key] = $v['allnum'];
	        }
	        array_multisort($key_list,SORT_DESC,$res_list);	        
	        for($i=0;$i<count($res_list);$i++) {
	        	if($i<9){
	        		$limit_list[] = $res_list[$i];
	        	}else{
	        		if($i==9){
	        			$limit_list[] = $res_list[$i];
	        			$tmp = $res_list[$i]['allnum'];
	        		}else{
	                    if($res_list[$i]['allnum']==$tmp)
	                    	$limit_list[] = $res_list[$i];
	        		}
	        	}
	        	if($res_list[$i]['allnum']>0)
	        		$xybl_saler_list[]=$res_list[$i]['create_user'];        	
	        }
	    }    
        $xxbl_list = $limit_list;


        //统计销售黑榜数据
        $res1 = $userModel1->get_sale_goods_amount($date_start,$date_end,$sys1);
        $res111 = $userModel111->get_sale_goods_amount($date_start,$date_end,$sys111);
        $amount_list = array_merge($res1,$res111);
        $amount_saler_num = count($amount_list);
        $amount_sum = array_sum(array_column($amount_list,'goods_amount')); 
        $amount_avg=0;
        if($amount_saler_num>0)
            $amount_avg = round($amount_sum/$amount_saler_num,2);
        $lower_avg_saler=array();
        //echo $amount_avg;
        //print_r($amount_list);
        if(!empty($amount_list)){
	        foreach ($amount_list as $key => $v) {
	        	if($v['goods_amount'] < $amount_avg && !in_array($v['create_user'],$tsyd_saler_list) && !in_array($v['create_user'],$xy_saler_list) && !in_array($v['create_user'],$xxbl_saler_list)){
	        		$v['amount_avg']=$amount_avg;
	        		$lower_avg_saler[]=$v;
	        	}
	        }
        }

        if(!empty($lower_avg_saler)){
	        $key_list = array();
	        foreach ($lower_avg_saler as $key => $v) {
	        	$key_list[$key] = $v['goods_amount'];
	        }
	        array_multisort($key_list,SORT_ASC,$lower_avg_saler);        
        } 

        $content='';
        if($res1){
        	$content=$this->fetch('first_login_show_sale_data.html',array('goods_amount_list' => $goods_amount_list,'tsyd_list'=>$tsyd_list,'xy_list'=>$xy_list,'xxbl_list'=>$xxbl_list,'lower_avg_saler'=>$lower_avg_saler));        	
        }
	    $result['title']   = '';
	    $result['content'] = $content;
	    Util::jsonExit($result);
	} 
}

?>