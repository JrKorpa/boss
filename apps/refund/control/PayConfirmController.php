<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReturnGoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-13 11:05:03
 *   @update	:
 *  -------------------------------------------------
 */
class PayConfirmController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('createGoodsSn','createCheckGoodsSn');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('pay_confirm_search_form.html',array('view'=>new AppReturnGoodsView(new AppReturnGoodsModel(31)),'bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		
		if($_SESSION['userType']==1){
            $department = _Request::getInt('department')?_Request::getInt('department'):0;
        }else{
            if(isset($_REQUEST['department'])){
                $department = _Request::getInt('department')?_Request::getInt('department'):($_SESSION['qudao']?$_SESSION['qudao']:-1);
            }else{
                $department = _Request::getInt('department')?_Request::getInt('department'):($_SESSION['qudao']?current(explode(',', $_SESSION['qudao'])):-1);
            }
        }
		$args = array(
            'mod'   => _Request::get("mod"),
            'con'   => substr(__CLASS__, 0, -10),
            'act'   => __FUNCTION__,
			'return_id'	=> _Request::getInt("return_id"),
			'order_sn'	=> _Request::getString("order_sn"),
			'return_type'	=> _Request::getInt("return_type"),
			'start_time'	=> _Request::getString("start_time"),
			'end_time'	=> _Request::getString("end_time"),
			'department'	=> $department,
		);
		$page = _Request::getInt("page",1);
		$where = array(
            'check_status'=>6,
            'return_id'=>$args['return_id'],
            'order_sn'=>$args['order_sn'],
            'return_type'=>$args['return_type'],
            'start_time'=>$args['start_time'],
            'end_time'=>$args['end_time'],
            'department'=>$args['department'],
			
		);

                if($args['return_id']){
                        $where =array();
                        $where['return_id']=$args['return_id'];
                }
		$model = new AppReturnGoodsModel(31);


		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;


		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'pay_confirm_search_page';
		$this->render('pay_confirm_search_list.html',array(
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
		$result['content'] = $this->fetch('department_check_info.html',array(
			'view'=>new AppReturnGoodsView(new AppReturnGoodsModel(31))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function editer ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
        $_model = new AppReturnGoodsModel($id,31);
        $do = $_model->getDataObject();
        /*$model = new AppReturnCheckModel(31);
        $is_check = $model->getCheckId($id);*/
        $a=new AppReturnCheckView(new AppReturnCheckModel(31));
        //这里要对权限进行控制  具体细节  不明
        if($do['check_status']!=5){
            $result['content'] = "该条退款记录财务还未操作";
        }
            $result['content'] = $this->fetch('pay_confirm_info.html',
            		array('id'=>$id ));
			

		$result['title'] = '实际退款';
		Util::jsonExit($result);
	}
	

	
	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		/*接参*/
        $pay_res = _Post::getstring('pay_res');
		//var_dump($check_status);exit;
        if(empty($pay_res)){
            $result['error'] = '凭证备注不能为空！';
            Util::jsonExit($result);
        }

        $pay_attach = '';
        if(isset($_FILES['pay_attach'])){
            $uploadObj = new Upload();
            $path = KELA_ROOT.'/public/upload/refund/';
            $uploadObj->base_path=$path;
            $res = $uploadObj->toUP($_FILES['pay_attach']);
            if(!is_array($res)){
                $result['error'] = '上传凭证失败请重试';
                $result['error'] = $res;
                Util::jsonExit($result);
            }else{
                $pay_attach = $res['url'];
            }
        }
        /*else{
            $result['error'] = '凭证必需上传';
            Util::jsonExit($result);
        }*/

		$return_id = _Post::getInt('return_id');


		$user_id= $_SESSION['userId'];

		$check_user=$_SESSION['userName'];

		$check_time=date("Y-m-d H:i:s");

		/*实例化*/
		$newmodel =  new AppReturnGoodsModel($return_id,32);

        $do = $newmodel->getDataObject();
        if(count($do)<1){
            $result['error'] = '退款申请不存在！';
            Util::jsonExit($result);
        }
		$checkmodel =  new AppReturnCheckModel(32);
		$logmodel =  new AppReturnLogModel(32);
        
		
        /*操作app_return_check表*/
		$checkid = $checkmodel->getCheckId($return_id);	
		/*取id*/

		if(!empty($checkid)){
			$checkmodel =  new AppReturnCheckModel($checkid,32);
			$olddo = $checkmodel->getDataObject();

			$checkdo=array(
				'id'=>$checkid,
				'pay_id'=>$_SESSION['userId'],
				'pay_res'=>$pay_res,
				'pay_status'=>1,
				'pay_attach'=>$pay_attach,
			);
			
            
			//var_dump($checkdo);die;
			//echo $check;die;
			$res = $checkmodel->saveData($checkdo,$olddo);
		}else{
            $savepath =  KELA_ROOT.'/public/upload/refund/';
            Upload::removeAbsoluteFile($savepath.$pay_attach);
			$result['error'] = '修改失败';
            Util::jsonExit($result);
		}
   
        /*操作app_return_goods表*/

			$newdo=array(
					'check_status'=>6,
					'return_id'=>$return_id,
			);

		$res = $newmodel->saveData($newdo,$do);
        /*操作app_return_log表*/
		$logdo=array(
			'return_id'=>$return_id,
			'even_time'=>$check_time,
			'even_user'=>$check_user,
			'even_content'=>'退款凭证备注'.$pay_res,
		);
		$res = $logmodel->saveData($logdo,array());
		if($res !== false)
		{
			$result['success'] = 1;
            
            $order_info = $newmodel->get_order_info_by_order_sn($do['order_sn']);
            //订单日志
            $insert_action = array ();
            $insert_action ['order_id'] = $order_info ['order_id'];
            $insert_action ['order_status'] = $order_info ['order_status'];
            $insert_action ['shipping_status'] = $order_info ['send_good_status'];
            $insert_action ['pay_status'] = $order_info ['order_pay_status'];
            $insert_action ['remark'] = '退款/退货单:实际退款已经审核';
            $insert_action ['create_user'] = $_SESSION ['userName'];
            $insert_action ['create_time'] = date ( 'Y-m-d H:i:s' );
            $apiModel = new ApiRefundModel();
            $apiModel->AddOrderActionInfo($insert_action);
		}
		else
		{
			$result['error'] = '修改失败';
            Upload::removeAbsoluteFile($savepath.$pay_attach);
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
		$model = new AppReturnGoodsModel($id,32);
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
    
    
    
    public function createGoodsSn($param) {
        
        if($_SESSION['userName']=='admin'){
            $id = intval($param["return_id"]);
            $warehouse_id = intval($param['wh']);
            $goodsModel = new AppReturnGoodsModel($id,31);
            $do = $goodsModel->getDataObject();

            //生成退货销售单 把返回的单号回写到jxc_order字段中
            $jxc_order = $this->createSaleBackGoods($do['order_sn'],$do['return_id'],$warehouse_id,array(array('detail_id'=>$do['order_goods_id'],'return_price'=>$do['real_return_amount'])));
            if($jxc_order['error']>0){
                $result['error'] = $jxc_order['data'];
                Util::jsonExit($result);
            }
            $returnGoodsModel = new AppReturnGoodsModel($id,32);
            $returnGoodsModel->setValue('jxc_order', $jxc_order['data']);
            $returnGoodsModel->save();

            $returngoodsmodel = new AppReturnGoodsModel($id,31);
            $warehouseModel = new ApiWarehouseModel();
            $warehouseModel->OprationBillD(array('order_sn'=>$returngoodsmodel->getValue('order_sn'),'bill_no'=>$returngoodsmodel->getValue('jxc_order'),'type'=>1));
            echo 'ok';
        }

    }
    
    public function createCheckGoodsSn($param) {
        
        if($_SESSION['userName']=='admin'){
            $id = intval($param["return_id"]);
//            
//            $goodsModel = new AppReturnGoodsModel($id,31);
//            $do = $goodsModel->getDataObject();
//
//            //生成退货销售单 把返回的单号回写到jxc_order字段中
//            $jxc_order = $this->createSaleBackGoods($do['order_sn'],$do['return_id'],array(array('detail_id'=>$do['order_goods_id'],'return_price'=>$do['real_return_amount'])));
//            if($jxc_order['error']>0){
//                $result['error'] = $jxc_order['data'];
//                Util::jsonExit($result);
//            }
//            $returnGoodsModel = new AppReturnGoodsModel($id,32);
//            $returnGoodsModel->setValue('jxc_order', $jxc_order['data']);
//            $returnGoodsModel->save();

            $returngoodsmodel = new AppReturnGoodsModel($id,31);
            $warehouseModel = new ApiWarehouseModel();
            $where = array('order_sn'=>$returngoodsmodel->getValue('order_sn'),'opra_uname'=>  Auth::$userName,'bill_no'=>$returngoodsmodel->getValue('jxc_order'),'type'=>1);
            echo "<pre>";
            print_r($where);
            echo "</pre>";
            $a = $warehouseModel->OprationBillD($where);
            echo "<pre>";
            print_r($a);
            echo "</pre>";
            echo 'ok';
        }

    }
    
    //ln 生成销售退货单
   public function createSaleBackGoods($order_sn,$return_id,$warehouse_id,$order_goods){
      // createReturnGoodsBill
       $warehouseModel = new ApiWarehouseModel();
       $jxc_order = $warehouseModel->createReturnGoodsBill(array('order_sn'=>$order_sn,'return_id'=>$return_id,'warehouse_id'=>$warehouse_id,'order_goods'=>$order_goods));
       return $jxc_order;
   }
    
    
}

?>
