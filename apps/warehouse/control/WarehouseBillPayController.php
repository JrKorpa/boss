<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillPayController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 17:59:53
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillPayController extends Controller
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('warehouse_bill_pay_search_form.html');
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


		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();

		$model = new WarehouseBillPayModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_bill_pay_search_page';
		$this->render('warehouse_bill_pay_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ($params)
	{
        $id = $params['bill_id'];
		$result = array('success' => 0,'error' => '');
		$model = new ApiProModel();
		$pro_list = $model->GetSupplierList(array('status'=>1));//调用加工商接口
		
		$bill_pay_model = new WarehouseBillPayModel(21);
        
		$result['content'] = $this->fetch('warehouse_bill_pay_info.html',array(
			'view'=>new WarehouseBillPayView(new WarehouseBillPayModel(21)),
            'view_bill' => new WarehouseBillView(new WarehouseBillModel($id, 21)),
			'dd'	=> new DictView(new DictModel(1)),
			'pro_list'	=> $pro_list,
			'bill_id'	=> $params['bill_id'],
			'zhifujia_prc'	   => $bill_pay_model->getAmount($id)
		));
		$result['title'] = '新增结算商';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
	    $bill_id = intval($params["bill_id"]);
		if (empty($id))
		{
			Util::jsonExit("入库尾差不能编辑");
		}
		$result = array('success' => 0,'error' => '');
		$model = new ApiProModel();
		
		$bill_pay_model = new WarehouseBillPayModel(21);
		
		//print_r(new WarehouseBillPayView(new WarehouseBillPayModel($id,21)));exit;
		$pro_list = $model->GetSupplierList(array('status'=>1));//调用加工商接口
		$result['content'] = $this->fetch('warehouse_bill_pay_info.html',array(
			'view'=>new WarehouseBillPayView(new WarehouseBillPayModel($id,21)),
			'view_bill' => new WarehouseBillView(new WarehouseBillModel($bill_id,21)),
			'dd'	=> new DictView(new DictModel(1)),
			'pro_list'	=> $pro_list,
			'bill_id'	=> $params['bill_id'],
		    'zhifujia_prc'	   => $bill_pay_model->getAmount($bill_id)
		));
		$result['title'] = '编辑结算商';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$bill_id = intval($params["bill_id"]);
		$model = new WarehouseBillPayModel(21);
		$payList = $model->getList(array('bill_id'=>$bill_id));
		
		$billModel = new WarehouseBillModel($bill_id,21);
		$bill_status  = $billModel->getValue('bill_status');
                
		if ($bill_status !=3 )
		{
			$chengbenjia_goods = $billModel->getValue('goods_total');
			$zhifujia_prc	   = $model->getAmount($bill_id);
                        $cha = round((($chengbenjia_goods*100) - ($zhifujia_prc*100))/100 ,2);
                        $pro_name = '入库成本尾差';
                        if (isset($params['bill_type']) && $params['bill_type'] == 'B'){
                            //出库成本尾差=成本总计-退款总计
                            //$cha = '';
                            $pro_name = '出库成本尾差';
                        }
			
			if ($cha !=0)
			{
				//入库尾差需要补
				$arr_cha = array(
				  'id' => '',
				  'bill_id' => $bill_id,
				  'pro_id' => 366,
				  'pro_name' => $pro_name,
				  'pay_content' =>6,// $cha<0?6:7 ,
				  'pay_method' => 1,
				  'tax' => 2,
				  'amount' => $cha
					);
				$payList[] =  $arr_cha;
			}
		}
		
		$result['content'] = $this->fetch('warehouse_bill_pay_show.html',array(
			'payList'	=> $payList,
			'amountTotal' => $cha,
			'dd'	=> new DictView(new DictModel(1)),
		));
		$result['success'] = 1;
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$ar = explode("|",$params['pro_id']);
		$pro_id		= $ar[0];
		$pro_name	= $ar[1];
		$bill_id	= $params['bill_id'];
		$pay_content= $params['pay_content'];
		$pay_method = $params['pay_method'];
		$tax		= $params['tax'];
		$amount		= $params['amount'];

		$olddo = array();
		$newdo=array(
			'bill_id'	=> $bill_id,
			'pro_id'	=> $pro_id,
			'pro_name'	=> $pro_name,
			'pay_content'=> $pay_content,
			'pay_method'=> $pay_method,
			'tax'		=> $tax,
			'amount'	=> $amount
		);
 
		$newmodel =  new WarehouseBillPayModel(22);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$model = new WarehouseBillPayModel(21);
			$payList = $model->getList(array('bill_id'=>$bill_id));
			$result['content'] = $this->fetch('warehouse_bill_pay_show.html',array(
				'payList'	=> $payList,
				'dd'	=> new DictView(new DictModel(1)),
			));
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
		$id = $params['id'];
		$ar = explode("|",$params['pro_id']);
		$pro_id		= $ar[0];
		$pro_name	= $ar[1];
		$bill_id	= $params['bill_id'];
		$pay_content= $params['pay_content'];
		$pay_method = $params['pay_method'];
		$tax		= $params['tax'];
		$amount		= $params['amount'];

		// 取B单中货号信息 
		//由于经常出现金额大于成本价的情况，无法向下执行，
		// 测试数据中成本价为0，shijia却不为0.为了减少影响
		// 所以，取消这段代码--2015-10-29
		// $goods_info = new WarehouseBillPayModel(21);		
		// $chengbenjia = $goods_info->getGoodsAmount($bill_id);
		// $BillType = $goods_info->getBillType($bill_id);
		// if($BillType == "B")
		// {
			
			// if($amount > $chengbenjia)
			// {
				// $result['error'] = $bill_id.'输入金额大于列表总金额之和'.$chengbenjia;
				// Util::jsonExit($result);
			// }
		// }
		
		$newmodel =  new WarehouseBillPayModel($id,22);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'		=> $id,
			'pro_id'	=> $pro_id,
			'pro_name'	=> $pro_name,
			'pay_content'=> $pay_content,
			'pay_method'=> $pay_method,
			'tax'		=> $tax,
			'amount'	=> $amount
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$payList = $newmodel->getList(array('bill_id'=>$bill_id));
			$result['content'] = $this->fetch('warehouse_bill_pay_show.html',array(
				'payList'	=> $payList,
				'dd'	=> new DictView(new DictModel(1)),
			));
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
		$model = new WarehouseBillPayModel($id,22);
		$bill_id = $model->getValue('bill_id');
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
	
	
	
	/**
	 *	add，渲染添加页面
	 */
	public function addBillPay ($params)
	{
		
		$result = array('success' => 0,'error' => '');
		$model = new ApiProModel();
		$where_level = array('status' => 1);
		$level = $this->verifyUserLevel();//3、省代
		//1、浩鹏系统，省代做收货单以及退货返厂单，加工商默认 经销商自采供应商		
        if($level['level'] == '3')
             $where_level['p_id'] = '597';//经销商自采供应商
		$pro_list = $model->GetSupplierList($where_level);//调用加工商接口

		if (SYS_SCOPE == 'zhanting' && isset($_SESSION['companyId']) && !empty($_SESSION['companyId'])) {
			$company = new CompanyModel(1);
			$processor_id = $company->select2('processor_id', ' id='.$_SESSION['companyId'], 3);
			if (!empty($processor_id)) {
				
				foreach ($pro_list as $k => $v) {
					if ($v['id'] == $processor_id) {
						$pro_list = array($v);
						break;
					}
				}
			}
		}
	
		$result['content'] = $this->fetch('warehouse_add_bill_pay_info.html',array(
				'view'=>new WarehouseBillPayView(new WarehouseBillPayModel(21)),
				
				'dd'	=> new DictView(new DictModel(1)),
				'pro_list'	=> $pro_list,
				
		));
		$result['title'] = '新增结算商';
		Util::jsonExit($result);
	}
	
	
	
	
	/**
	 *	结算商session模拟入库
	 */
	public function insertBillPay ($params)
	{
		$result = array('success' => 0,'error' =>'');
	
		$ar = explode("|",$params['pro_id']);
		$pro_id		= $ar[0];
		$pro_name	= $ar[1];		
		$pay_content= $params['pay_content'];
		$pay_method = $params['pay_method'];
		$tax		= $params['tax'];
		$amount		= $params['amount'];
		
		//$olddo = isset($_SESSION['bill_pay'])?$_SESSION['bill_pay']:array();
		$olddo_str = isset($_COOKIE['bill_pay'])?$_COOKIE['bill_pay']:'';
		if(isset($_COOKIE['bill_pay'])){
			$olddo_str=$_COOKIE['bill_pay'];
			$olddo=unserialize($olddo_str);
			$bill_id=count($olddo);
		}else{
			$bill_id=0;
			$olddo=array();
		}
		
		
		$newdo=array(
				'id'	=> $bill_id,
				'pro_id'	=> $pro_id,
				'pro_name'	=> $pro_name,
				'pay_content'=> $pay_content,
				'pay_method'=> $pay_method,
				'pay_tax'		=> $tax,
				'amount'	=> $amount
		);
		$olddo[]=$newdo;
		$olddo_str=serialize($olddo);
		setcookie('bill_pay', $olddo_str, time()+3600);
		//$_SESSION['bill_pay']=$olddo;		
		if(!empty($olddo))
		{
			$result['success'] = 1;
			$result['content'] = $this->fetch('warehouse_add_bill_pay_show.html',array(
					'payList'	=> $olddo,
					'dd'	=> new DictView(new DictModel(1)),
			));
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}
	
	
	
	
	/**
	 *	edit，渲染修改页面
	 */
	public function editBillPay ($params)
	{
		$id = intval($params["id"]);
		if (empty($id) && $id !=0)
		{
			Util::jsonExit("入库尾差不能编辑");
		}
		//print_r($_SESSION['bill_pay']);exit();
		//$view=$_SESSION['bill_pay'][$id];
		$olddo_str = $_COOKIE['bill_pay'];
		$olddo=unserialize($olddo_str);
		$view=$olddo[$id];
		$result = array('success' => 0,'error' => '');
		$model = new ApiProModel();
		$pro_list = $model->GetSupplierList(array('status'=>1));//调用加工商接口
		
		if (SYS_SCOPE == 'zhanting' && isset($_SESSION['companyId']) && !empty($_SESSION['companyId'])) {
			$company = new CompanyModel(1);
			$processor_id = $company->select2('processor_id', ' id='.$_SESSION['companyId'], 3);
			if (!empty($processor_id)) {
				
				foreach ($pro_list as $k => $v) {
					if ($v['id'] == $processor_id) {
						$pro_list = array($v);
						break;
					}
				}
			}
		}
		
		//print_r($view);exit;
		$result['content'] = $this->fetch('warehouse_edit_bill_pay_info.html',array(
				'view' => $view,
				'view_bill' => new WarehouseBillView(new WarehouseBillModel(21)),
				'dd'	=> new DictView(new DictModel(1)),
				'pro_list'	=> $pro_list,
				
		));
		$result['title'] = '编辑结算商';
		Util::jsonExit($result);
	}
	
	
	
	
	
	/**
	 *	update，更新信息
	 */
	public function updateBillPay ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = $params['id'];
		$ar = explode("|",$params['pro_id']);
		$pro_id		= $ar[0];
		$pro_name	= $ar[1];
		$pay_content= $params['pay_content'];
		$pay_method = $params['pay_method'];
		$tax		= $params['tax'];
		$amount		= $params['amount'];
	
		
		
		$newdo=array(
				'id'	=> $id,
				'pro_id'	=> $pro_id,
				'pro_name'	=> $pro_name,
				'pay_content'=> $pay_content,
				'pay_method'=> $pay_method,
				'pay_tax'		=> $tax,
				'amount'	=> $amount
		);
		//$olddo=$_SESSION['bill_pay'];
		$olddo_str = $_COOKIE['bill_pay'];
		$olddo=unserialize($olddo_str);
		if(isset($olddo[$id])){
			$olddo[$id]=$newdo;
		}else{
			$result['error'] = '没有这个结算商';
			Util::jsonExit($result);
		}
		$payList=$olddo;
		$olddo_str=serialize($olddo);
		setcookie('bill_pay', $olddo_str, time()+3600);
		//$_SESSION['bill_pay']=$olddo;
		//print_r($olddo);exit;
		if(!empty($olddo))
		{
			$result['success'] = 1;
			$result['content'] = $this->fetch('warehouse_add_bill_pay_show.html',array(
					'payList'	=> $payList,
					'dd'	=> new DictView(new DictModel(1)),
			));
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
	public function deleteBillPay ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		//$olddo=$_SESSION['bill_pay'];
		$olddo_str = $_COOKIE['bill_pay'];
		$olddo=unserialize($olddo_str);
		if(isset($olddo[$id])){
			unset($olddo[$id]);
		}
		
		
		if(isset($olddo[$id])){
			$result['error'] = "删除失败";
		}else{
			//函数删除数组后重建索引
			$olddo=array_values($olddo);
			foreach ($olddo as $k=>$v){
				$olddo[$k]['id']=$k;
			}
			//$_SESSION['bill_pay']=$olddo;
			$olddo_str=serialize($olddo);
		   setcookie('bill_pay', $olddo_str, time()+3600);
			$result['success'] = 1;
		}
		
		Util::jsonExit($result);
	}
	
	/**
	 *	ajax验证尾差
	 */
	public function validate_cha($params)
	{
		$result = array('success' => 0,'error' => '');
		$amount = $params['amount'];
		$zfj = $params['zfj'];
		$total = $params['total'];
		$sum = $zfj + $amount;
		if($total - $sum < -100 || $total - $sum > 100){
			$result['success'] = 0;
		}else{
			$result['success'] = 1;
		} 
		Util::jsonExit($result);
	}
    //验证当前用户是那种级别用户
    //1.总公司 、2.经销商，个体，直营 、3.省代  
    public function verifyUserLevel()
    {
        $res = array('level' => 2,'dataCompInfo' => array());
        $companyId = $_SESSION['companyId'];
        if(!empty($companyId)){
            $is_company = Auth::is_base_company($companyId);
            if($is_company){
                $res['level'] = 1;
                return $res;
            }
            $company_model = new CompanyModel(1);
            $checkshengdai = $company_model->select2(' `id` ' , " is_shengdai = '1' and id ='{$companyId}' " , $type = '2');
            if(!empty($checkshengdai)){
                //如果是省代则取出改省代公司下的公司
                $rCop = $company_model->select2(' `id` ' , " sd_company_id ='{$companyId}' " , $type = '1');
                $rCop = array_column($rCop,'id');
                array_push($rCop, $companyId);
                $res['level'] = 3;
                $res['dataCompInfo'] = $rCop;
                return $res;//省代
            }
        }
        $res['dataCompInfo'] = array($companyId);
        return $res;
    }	
}

?>