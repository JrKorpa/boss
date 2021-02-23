<?php
/**
 * 裸钻4C快捷搜索
 *  -------------------------------------------------
 *   @file		: DiamondListFourCController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2015-09-09
 *   @update	:
 *  -------------------------------------------------
 */
class DiamondListFourCController extends CommonController
{
	protected $smartyDebugEnabled = true;
	
	/**
	 * 4C快捷搜索FORM表单
	 */
	public function index($params){
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }        
        $kefu_type=3;       
        if($_SESSION['userType']==1 || Auth::user_is_from_base_company())
            $kefu_type=1;
        else{
            $company_model = new CompanyModel(1);
            $companyInfo = $company_model->select2(" `id`,`company_name`,`is_shengdai`,`sd_company_id`, `company_type`" , " id ='".$_SESSION['companyId']."'" , $type = '1');
            if($companyInfo){
                if($companyInfo[0]['company_type']=="1" || $companyInfo[0]['company_type']=="2")
                    $kefu_type=2;
                else
                    $kefu_type=3; 
            }
        }
         if(SYS_SCOPE=='boss'){
            if(empty($_SESSION['companyId'])){
                exit("<br>没有总公司或者直营店公司权限。。。。。。");
            }
            if(!in_array($_SESSION['companyId'],array(58,690,687,553,665))){
                $company_model = new CompanyModel(1);
                $company_type = $company_model->select2("company_type","id='{$_SESSION['companyId']}'","3");
                //echo $company_type;
                if($company_type<>1){
                    exit("<br>非直营店员工请移步浩鹏系统查钻。。。。。。");
                }
            }
         }
	    $this->render('diamond_list_4c_form.html',array(
	        'bar'=>Auth::getBar(),
	        'view'=>new DiamondListView(new DiamondListModel(19)),
            'kefu_type'=>$kefu_type
	    ));
	}
	
	/**
	 * 4C快捷搜索
	 */
    public function search($params){
        
        $_SESSION['cart_filter_data2'] = array();
        
        $no_goods_id = array();
        $cartModel = new AppOrderCartModel(27);
        $cart_goods = $cartModel->get_cart_goods();
        foreach($cart_goods as $good){
            $no_goods_id[] = $good['goods_id'];            	
        } 
        $_hrd_s_warehouse=array();
        $_warehouse = array();
        $not_from_ad = '';
        $companyId = $_SESSION['companyId'];
        /*
        //if(SYS_SCOPE=="zhanting"){
            $companyId = $_SESSION['companyId'];
            $companyModel = new CompanyModel(1);
            $comInfo = $companyModel->select2("*","id={$companyId}",2);
            //company_type<>1 ：个体店和经销商 只可以看 （自己门店 +总公司）
            if($comInfo['company_type']<>1){
                $companyList = $companyModel->select2("id","company_type=1",1);
                $companyList = array_column($companyList,'id');
                $companyList[] = 58;   
                $companyList[ ]= $companyId;              
                //$companyList = array($companyId,58);
                $_hrd_s_warehouse=$companyModel->getWarehouse(1,1,58);
                $_hrd_s_warehouse=array_column($_hrd_s_warehouse,'code');
            }else{
                //直营店店的可以看（所有直营店 +总公司）
                $companyList = $companyModel->select2("id","company_type=1",1);
                $companyList = array_column($companyList,'id');
                $companyList[] = 58;
            }
            $wareshou_model = new ApiWarehouseModel();
	        $warehouse = $wareshou_model->get_warehouse_all(1,implode(',', $companyList));
	        if($warehouse['error']==0){
	            $_warehouse = array_column($warehouse['data'],'code');
	        }
	        
            array_push($_warehouse, 'COM');
        //}  
        */  
        
        $companyModel = new CompanyModel(1);
        if(SYS_SCOPE=='boss'){
                //直营店店的可以看（所有直营店 +总公司）的(期货+现货)
                $_companyList = $companyModel->select2("id","company_type=1",1);
                $_companyList = array_column($_companyList,'id');
                $_companyList[] = 58; 
                $where = array('diamond_warehouse' =>1 , 'company_id' =>implode(',', $_companyList));
                $warehouse = $companyModel->getWarehouse_Where($where);                 
                if(!empty($warehouse)){
                    $_warehouse = array_column($warehouse,'code');
                }
                array_push($_warehouse, 'COM');                  
                              
        }

        if(SYS_SCOPE=='zhanting')       {
                //经销商可以看  所有直营店期货(kgk+enjoy除外) + 自己门店现货 +浩鹏公司现货
                $_companyList[] = 58; 
                $_companyList[] = $companyId;
                $where = array('diamond_warehouse' =>1 , 'company_id' =>implode(',', $_companyList));
                $warehouse = $companyModel->getWarehouse_Where($where);                 
                if(!empty($warehouse)){
                    $_warehouse = array_column($warehouse,'code');
                }
                array_push($_warehouse, 'COM');  //直营店期货
                $not_from_ad = array('11','17');    //直营店期货(kgk+enjoy除外)               
                
        }

        $args = array(
            'mod'	=> _Request::get("mod"),
            'con'	=> substr(__CLASS__, 0, -10),
            'act'	=> __FUNCTION__,
            'carat_max'=>  _Request::getFloat('carat_max'), 
            'carat_min'=>  _Request::getFloat('carat_min'),
            'clarity[]'=> _Request::getList('clarity'),
            'color[]'=> _Request::getList('color'),
            'shape[]'=> _Request::getList('shape'),
            'cut[]'=> _Request::getList('cut'),            
            'cert[]'=> _Request::getList('cert'),
            'fluorescence[]'=>_Request::getList('fluorescence'), 
            'good_type'=> _Request::getInt('good_type'),
            'not_from_ad'   => $not_from_ad,
        );
        $where = array(
            'page'=>1,
            'pageSize'=>10, 
            'clarity'=> _Request::getList('clarity'),
            'color'=> _Request::getList('color'),
            'shape'=> _Request::getList('shape'),
            'cut'=> _Request::getList('cut'),         
            'cert'=> _Request::getList('cert'),
            'fluorescence'=>_Request::getList('fluorescence'),
            'good_type'=> _Request::getInt('good_type'),
            'status'=> 1,           
            'no_goods_id'=> $no_goods_id,
            'not_from_ad'=> $not_from_ad,
            'is_4c' => 2,
            'warehouse'=>$_warehouse,
            //'hrd_s_warehouse' =>$_hrd_s_warehouse
            'not_from_ad'   => $not_from_ad,
        );
        if(!empty($args['carat_min'])){
            $where['carat_min'] = $args['carat_min'];
        }
        if(!empty($args['carat_max'])){
            $where['carat_max'] = $args['carat_max'];
        }
        //cut->3EX代表 切工、抛光、对称皆为EX
        if(is_array($args['cut[]']) && in_array("3EX",$args['cut[]'])){
            $where['cut'][]='EX';
            $where['polish'][]='EX';
            $where['symmetry'][]='EX';
            //删除$where['cut']数组中3EX值
            unset($where['cut'][array_search('3EX',$where['cut'])]);
        }
        $model = new DiamondListModel(19);
        $where['pageSize'] = 20;//临时用20，可调整
        $data = $model->pageList($where);
        $pageData = $data['data'];        
        if(!empty($pageData)){ 
            //查询20条最低记录，用于计算倒数第二低价格 begin
            $p = $pageData['data'][0]['shop_price'];
            foreach ($pageData['data'] as $data){
                $where['price_min'] = $data['shop_price']+0.001;
                if($data['shop_price']>$p){
                    break;
                }
            }//查询20条最低记录，用于计算倒数第二低价格 end
            
            //查询倒数第二低价格，前10条记录
            $where['pageSize'] = 10;
            $data = $model->pageList($where);
            if(!empty($data['data'])){
                $pageData = $data['data'];
                $this->calc_dia_channel_price($pageData['data']);
            }
            unset($data);
        }      
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'diamond_list_4c_search_page';
        $this->render('diamond_list_4c_search_list.html',array(
            'pa'=>Util::page($pageData),
            'page_list'=>$pageData,
        ));
        
    } 

    //加入购物车
    public function addCart(){
        $id = _Request::getInt('id');
        $model = new DiamondListModel(27);
        $data = $model->getRowById($id);
        if($data['error']==1){
            $result['error'] = '裸钻可能已经下架或售出，请重新搜索!';
            Util::jsonExit($result);
        }    
        $info = $data['data'];         
        $select_goods_id = array_column($info, 'goods_sn');

        //裸钻只有一个所以去重
        $cartModel = new AppOrderCartModel(27);
        $cartList = $cartModel->get_cart_goods();
        if($cartList){
            foreach ($cartList as $val){
                if(in_array($val['goods_id'], $select_goods_id)){
                    $result['error'] = '此商品已经添加,请勿重复添加!';
                    Util::jsonExit($result);
                }
            }
        }
    	$this->calc_dia_channel_price($info);
        foreach ($info as $val){
            $parent["session_id"]=DBSessionHandler::getSessionId();
            $parent["goods_id"]=$val['goods_sn'];
            $parent["goods_sn"]='DIA';
            $parent["goods_price"]=$val['shop_price'];
            $parent["is_stock_goods"]=$val['good_type'];
            $parent["goods_count"]=1;
            $parent["create_time"]=  date("Y-m-d H:i:s");
            $parent["modify_time"]= date("Y-m-d H:i:s");
            $parent["create_user"]=$_SESSION['userName'];
            $parent["cart"]=$val['carat'];
            $parent["cut"]=$val['cut'];
            $parent["clarity"]=$val['clarity'];
            $parent["color"]=$val['color'];
            $parent["tuo_type"]='成品';
            $parent["cert"]=$val["cert"];
            $parent["goods_type"]='lz';
            $parent["kuan_sn"]=$val['kuan_sn'];
            $parent["product_type"]=0;
            $parent["cat_type"]=0;
            $parent["zhengshuhao"]=$val['cert_id'];
            $parent["goods_name"]=$val["carat"]."克拉/ct ".$val["clarity"]."净度 ".$val["color"]."颜色 ".$val["cut"]."切工";
            $parent["is_4c"] = 2;
            //file_put_contents('4c.txt',var_export($_SESSION['cart_filter_data2'],true));
            if(empty($_SESSION['cart_filter_data2'][$id])){
                $result['error'] = '页面表单提交超时，请重新搜索！';
                Util::jsonExit($result);
            }else{
                $parent["filter_data"]= json_encode($_SESSION['cart_filter_data2'][$id]);
            }
            $cart_id=$cartModel->add_cart($parent);            
            break;
        }
    
        if($cart_id){            
            $result['success'] = 1;
        }else{
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }
}

?>