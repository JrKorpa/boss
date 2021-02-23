<?php
/**
 *  -------------------------------------------------
 *   @file		: productCreationCodeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: liaoweixian 
 *   @date		: 2017-5-26
 *   @update	:
 *  -------------------------------------------------
 */
class ProductCreationCodeController extends CommonController{
    /**
     * {@inheritDoc}
     * @see Controller::index()
     */
    public function index($params)
    {
        if(Auth::$userType>2)
        {
            die('操作禁止');
        }
        $res = $this->ChannelListO();
        if ($res === true) {
            //获取全部的有效的销售渠道
            $SalesChannelsModel = new SalesChannelsModel(1);
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        } else {
            $channellist = $this->getchannelinfo($res);
        }
        $this->render('product_creation_list_form.html',array(
            'channelList'=>$channellist,
            'bar'=>Auth::getBar(),
        ));
        
    }
    
    public function search($params){
        $args = array(
            'mod'	=> _Request::get("mod"),
            'con'	=> substr(__CLASS__, 0, -10),
            'act'	=> __FUNCTION__,
            'department_id'=>_Request::get('department_id'),
            'create_user'=>_Request::get('create_user'),
            'order_sn'=>_Request::get('order_sn'),
            'department_id_from'=>_Request::get('department_id_from'),
            'id'=>_Request::get('id'),
            'sale_user'=>_Request::get('sale_user'),
            'status'=>_Request::get('status'),
        );
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
        $where =[];
        if($args['department_id']!=''){
            $where['department_id']=$args['department_id'];
        }
        if($args['create_user']!=''){
            $where['create_user']=$args['create_user'];
        }
        if($args['order_sn']!=''){
            $where['order_sn']=$args['order_sn'];
        }
        if($args['department_id_from']!=''){
            $where['department_id_from']=$args['department_id_from'];
        }  
        if($args['id']!=''){
            $where['id']=$args['id'];
        }  
        if($args['sale_user']!=''){
            $where['sale_user']=$args['sale_user'];
        }
        $data= new ProductCreationCodeModel(27);//27
        $listProduct=$data->pageList($where,$page,10,false);
        $pageData = $listProduct;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'product_creation_code_search_page';
        $this->render('product_creation_list_search_list.html',array(
            'page_list'=>$listProduct,
            'pa'=>Util::page($pageData)
        ));
    }
    /**
     *	add，渲染添加页面
     */
    public function add ()
    {
        $result = array('success' => 0,'error' => '');
        $res = $this->ChannelListO();
        if ($res === true) {
            //获取全部的有效的销售渠道
            $SalesChannelsModel = new SalesChannelsModel(1);
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        } else {
            $channellist = $this->getchannelinfo($res);
        }
        $result['content'] = $this->fetch('product_creation_list_info.html',array(
             'view'=>new ProductCreationCodeView(new ProductCreationCodeModel(27)),
            'channelList'=>$channellist,
        ));
        $result['title'] = '控制器-添加';
        Util::jsonExit($result);
    }
    
    /*
     * 添加成品定制码
     * */
    public function insert(){
       $args=array(
           'department_id'=>_Request::get('department_id'),
           'transaction_price'=>_Request::get('transaction_price'),
           'create_user'=>$_SESSION['userName'],
           'created_time'=>date('Y-m-d H:i:s'),
       );
       if(!empty($_SESSION['userName'])){
          $connection=new ProductCreationCodeModel(27);
          $msg=$connection->saveData($args,array());
          if($msg!=false){
              $result['success'] = '1';
          }else{
              $result['error'] = '添加失败';
          }
       }else{
           $result['error'] = '添加失败';
       }
       Util::jsonExit($result);
    }
}
