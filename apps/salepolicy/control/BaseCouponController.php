<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseCouponController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-11 10:39:22
 *   @update	:
 *  -------------------------------------------------
 */
class BaseCouponController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $model = new AppCouponPolicyModel(17);
        $CouponPolicydata = $model->getCouponPolicyList();
		$this->render('base_coupon_search_form.html',array('couponPolicyData'=>$CouponPolicydata,'bar'=>Auth::getBar()));
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
			'coupon_code'	=> _Request::getString('coupon_code'),
			'coupon_status'	=> _Request::getString('coupon_status'),
			'price_start'	=> _Request::getString('price_start'),
			'price_end'	=> _Request::getString('price_end'),
			'coupon_policy'	=> _Request::getInt('coupon_policy'),
			//'参数' = _Request::get("参数");


		);
		$page = _Request::getInt("page",1);
		$where = array();
        $where['coupon_code'] = $args['coupon_code'];
        $where['coupon_status'] = $args['coupon_status'];
        $where['price_start'] = $args['price_start'];
        $where['price_end'] = $args['price_end'];
        $where['coupon_policy'] = $args['coupon_policy'];

		$model = new BaseCouponModel(17);
		$data = $model->pageList($where,$page,10,false);
        if($data['data']){
            $_model = new AppCouponPolicyModel(17);
            $_newmodel = new AppCouponTypeModel(17);
            foreach ($data['data'] as &$val){
                $tmp = $_model->getCouponPolicy($val['coupon_policy']);
                $val['coupon_policy'] = $tmp[0]['policy_name'];
                $temp = $_newmodel->getCouponTypeList($val['coupon_type']);
                if($temp){
                    $val['coupon_type'] = $temp[0]['type_name'];
                }else{
                    $val['coupon_type'] = '';
                }                
            }
            unset($val);
        }
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'base_coupon_search_page';
		$this->render('base_coupon_search_list.html',array(
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
        $model = new AppCouponPolicyModel(17);
        $CouponPolicydata = $model->getCouponPolicy(0);
        foreach($CouponPolicydata as $key=>$val){
            if($val['policy_status']!=4){
                unset($CouponPolicydata[$key]);
            }
        }
		$result['content'] = $this->fetch('base_coupon_info.html',array(
			'view'=>new BaseCouponView(new BaseCouponModel(17)),'CouponPolicydata'=>$CouponPolicydata
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
		$result['content'] = $this->fetch('base_coupon_info.html',array(
			'view'=>new BaseCouponView(new BaseCouponModel($id,17)),
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
		$this->render('base_coupon_show.html',array(
			'view'=>new BaseCouponView(new BaseCouponModel($id,17)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$coupon_policy = _Post::getInt('coupon_policy');
		$coupon_number = _Post::getInt('coupon_number');
        
        if($coupon_number < 1){
            $result['error'] = '兑换码个数不能少于1！';
            Util::jsonExit($result);
        }
        if(empty($coupon_policy)){
            $result['error'] = '优惠券政策必需填写！';
            Util::jsonExit($result);
        }
        $_model = new AppCouponPolicyModel($coupon_policy,17);
        $do = $_model->getDataObject();
        $array_data = array();
        for($j=0;$j<$coupon_number;$j++){
            $array_data[$j]['coupon_code'] = $this->createCouponCode();
            $array_data[$j]['coupon_price'] = $do['policy_price'];
            $array_data[$j]['coupon_policy'] = $coupon_policy;
            $array_data[$j]['coupon_type'] = $do['policy_type'];
            $array_data[$j]['create_time'] = date("Y-m-d H:i:s");
            $array_data[$j]['create_user'] = $_SESSION['userName'];
        }
        
		$newmodel =  new BaseCouponModel(18);
        
		$res = $newmodel->insertAll($array_data);
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

		$newmodel =  new BaseCouponModel($id,18);

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
     * 
     * @param type $param
     */
    public function cancel($param) {
        $result = array('success' => 0,'error' => '');
		$ids = _Request::getList('_ids');
        if(count($ids) < 1){
            $result['error'] = "至少勾选中一条记录";
            Util::jsonExit($result);
        }
        
		$model = new BaseCouponModel(18);
        $dataInfo = $model->getBatchCoupon($ids);
        foreach ($dataInfo as $k=>$val){
            if($val['coupon_status']!=2){
                unset($dataInfo[$k]);
            }
        }
        $error_ids = array_column($dataInfo,'id');
        if(count($error_ids)){
            $result['error'] = "序号".implode(',', $error_ids).'不能作废';
            Util::jsonExit($result);
        }
		$res = $model->batchModfiyStatus($ids);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "作废失败";
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
		$model = new BaseCouponModel($id,18);
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
     * 生成优惠券码
     * @param type $size
     * @return type
     */
    private function createCouponCode($size=15) {
        $str = ['1','2','3','4','5','6','7','8','9','0','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
        $code = '';
        for($j=0;$j<$size;$j++){
            $k = array_rand($str);
            $code .= $str[$k];
        }
        $_code = strtoupper($code);
        $model = new BaseCouponModel(17);
        $is_not = $model->checkCode($_code);
        if($is_not){
            $this->createCouponCode();
        }
        return $_code;
    }
    
}

?>