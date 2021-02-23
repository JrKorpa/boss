<?php
/**
 *  -------------------------------------------------
 *   @file		: GiftGoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-12 18:37:15
 *   @update	:
 *  -------------------------------------------------
 */
class GiftGoodsController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('gift_goods_search_form.html',array('bar'=>Auth::getBar()));
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
			'name' => _Request::get("name"),
			'goods_number' => _Request::get("goods_number"),
			//'sell_sprice' => _Request::get("sell_sprice"),
			'sell_type' => _Request::get("sell_type"),
			'status' => _Request::get("status"),
			'start_time' =>_Request::get("start_time"),
			'end_time' => _Request::get("end_time"),
			'is_xz' => _Request::get("is_xz"),
			'sale_way1' => _Request::get('sale_way1'),
            'sale_way2' => _Request::get('sale_way2'),
			'is_check' => _Request::get("is_check")
		);
       
		$page = _Request::getInt("page",1);
		$where = array(
            'name' =>$args['name'],
            'goods_number' => $args['goods_number'],
            //'sell_sprice' => $args['sell_sprice'],
            'sell_type' => $args['sell_type'],
            'status' =>$args['status'],
            'start_time' =>$args['start_time']?strtotime($args['start_time'].'00:00:00'):'',
            'end_time' =>$args['end_time']?strtotime($args['end_time'].'23:59:59'):'',
			'is_xz' => $args["is_xz"],
			'sale_way1' =>$args['sale_way1'],
            'sale_way2' =>$args['sale_way2'],
			'is_check' => $args["is_check"]
				
        );
		$args ='';		
        
// 		echo 88;
// 		echo "<pre>";
// 		print_r($where);exit;
		
		$model = new GiftGoodsModel(27);
		$data = $model->pageList($where,$page,40,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'gift_goods_search_page';
		$this->render('gift_goods_search_list.html',array(
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
		$result['content'] = $this->fetch('gift_goods_info.html',array(
			'view'=>new GiftGoodsView(new GiftGoodsModel(27))
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
        $model=new GiftGoodsModel($id,27);
        $is_xz_status=$model->getgiftorderstatus($id);
        
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('gift_goods_info.html',array(
			'view'=>new GiftGoodsView(new GiftGoodsModel($id,27)),
			'tab_id'=>$tab_id,
            'is_xz_status'=>$is_xz_status
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
		$this->render('gift_goods_show.html',array(
			'view'=>new GiftGoodsView(new GiftGoodsModel($id,27)),
			'bar'=>Auth::getViewBar()
		));
	}
    	/**
	 *	show，渲染查看日志页面
	 */
	public function showlog ($params)
	{
		$goods_number = _Request::get('goods_number');
		$args ='';		
		$model = new GiftGoodsModel(27);
		$data = $model->pageListlog($goods_number,$page,40,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'base_action_info_search_list';
		$this->render('gift_goods_showlog.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
        
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
        $name=_Request::get('name');
        $is_online1=_Request::get('is_online1');
        $is_online2=_Request::get('is_online2');
        $sale_way=$is_online1.$is_online2;
        if(empty($name)){
            $result['error']="赠品名称不能为空";
        }
        //校验商品名称
        $model = new GiftGoodsModel(28);
        $res = $model->CheckName($name);
        if($res){
            $result['error']="赠品名称已经有了禁止添加";
            Util::jsonExit($result);
        }
        $goods_number=_Request::get('goods_number');

        if(empty($goods_number)){
            $result['error']="赠品货号不能为空";
            Util::jsonExit($result);
        }else{
            $val = $model->TestCode($goods_number);
            if($val){
                $result['error']="款号重复禁止添加";
                Util::jsonExit($result);
            }
            //接口查这个款是否存在
            $apiStyle = new ApiStyleModel();
            $res = $apiStyle->getStyleInfo($goods_number);
        
            if($res['check_status']>3)
            {
                  $result['error']="款式已作废";
                Util::jsonExit($result);
            }
            if(empty($res)){
                $result['error']="款式库并无此款";
                Util::jsonExit($result);
            }
        }


        $sell_sprice=_Request::get('sell_sprice');
        $is_xz=_Request::get('is_xz');
        $is_zp=_Request::get('is_zp');
        
        if(empty($sell_sprice)){
            $result['error']="赠品售价不能为空";
            Util::jsonExit($result);
        }
        if(!Util::isNum($sell_sprice)){
            $result['error']="赠品售价只能是数字";
            Util::jsonExit($result);
        }
        //$sell_type=_Request::get('sell_type');
        //if(empty($sell_type)){
        //    $result['error']="销售类型不能为空";
        //    Util::jsonExit($result);
        //}
        $add_time=time();
		$olddo = array();
		$newdo=array(
           'num'=>0,
           'name'=>$name,
           'goods_number'=>$goods_number,
           'sell_sprice'=>$sell_sprice,
           //'sell_type'=>$sell_type,
           'add_time'=>$add_time,
           'is_xz'=>$is_xz,
           'is_zp'=>$is_zp,
           'sale_way'=>$sale_way,
        );

		$res = $model->saveData($newdo,$olddo);
		if($res !== false)
		{
		   $log=$model->setgiftgoodslog($goods_number,"添加赠品",$_SESSION['userName'],date("Y-m-d H:i:s"));
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
        $name=_Request::get('name');
        $is_xz=_Request::get('is_xz');
        $is_zp=_Request::get('is_zp');
        $is_online1=_Request::get('is_online1');
        $is_online2=_Request::get('is_online2');
        $sale_way=$is_online1.$is_online2;

        if(empty($name)){
            $result['error']="赠品名称不能为空";
        }
        $model = new GiftGoodsModel($id,28);
        $goods_number=_Request::get('goods_number');
        if(empty($goods_number)){
            $result['error']="赠品货号不能为空";
        }
        $sell_sprice=_Request::get('sell_sprice');
        if(empty($sell_sprice)){
            $result['error']="赠品售价不能为空";
        }
        if(!Util::isNum($sell_sprice)){
            $result['error']="赠品售价只能是数字";
            Util::jsonExit($result);
        }
        //$sell_type=_Request::get('sell_type');
        //if(empty($sell_type)){
        //    $result['error']="销售类型不能为空";
        //}
        $update_time=time();
		$olddo = $model->getDataObject();
        if($olddo['name']!=$name){
            $res = $model->CheckName($name,$id);
            if($res){
                $result['error']="赠品名称已经有了禁止添加";
                Util::jsonExit($result);
            }
        }
        if($olddo['goods_number']==$goods_number){
            
            //接口查这个款是否存在
            $apiStyle = new ApiStyleModel();
            $res = $apiStyle->getStyleInfo($goods_number);
             
            
            if(empty($res)){
                $result['error']="款式库并无此款";
                Util::jsonExit($result);
            }
        }
        else
        {
            $apiStyle = new ApiStyleModel();
            $res = $apiStyle->getStyleInfo($goods_number);
             
            if($res['check_status']>3)
            {
                  $result['error']="款式已作废";
                Util::jsonExit($result);
            }
        }
       
        $newdo=array(
            'id'=>$id,
            'num'=>0,
            'name'=>$name,
            'goods_number'=>$goods_number,
            'sell_sprice'=>$sell_sprice,
            //'sell_type'=>$sell_type,
            'update_time'=>$update_time,
            'is_xz'=> $is_xz,
           'is_zp'=>$is_zp,
           'sale_way'=>$sale_way,
        );
		
		if (!isset($is_xz) || empty($is_xz)) unset($newdo['is_xz']);
		$res = $model->saveData($newdo,$olddo);
        
		if($res !== false)
		{
		   $log=$model->setgiftgoodslog($goods_number,"修改赠品",$_SESSION['userName'],date("Y-m-d H:i:s"));
			$result['success'] = 1;

			$result['title'] = '修改成功';
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
		$model = new GiftGoodsModel($id,28);
		$do = $model->getDataObject();
		$valid = $do['status'];
        if($valid==-1){
            $result['error'] = "该赠品已经被禁用";
            Util::jsonExit($result);
        }
		$model->setValue('status',-1);
		$res = $model->save(true);
        
		if($res !== false){
		  $log=$model->setgiftgoodslog($do['goods_number'],"禁用赠品",$_SESSION['userName'],date("Y-m-d H:i:s"));
			$result['success'] = 1;
		}else{
			$result['error'] = "禁用失败";
		}
		Util::jsonExit($result);
	}

    public function open ($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new GiftGoodsModel($id,28);
        $do = $model->getDataObject();
        $valid = $do['status'];
        if($valid==1){
            $result['error'] = "该赠品已经被启用";
            Util::jsonExit($result);
        }
        $apiStyle = new ApiStyleModel();
            $res = $apiStyle->getStyleInfo($do['goods_number']);
             
            if($res['check_status']>3)
            {
                  $result['error']="款式已作废";
                Util::jsonExit($result);
            }
            
        $model->setValue('status',1);
        $res = $model->save(true);
        
        if($res !== false){
            $log=$model->setgiftgoodslog($do['goods_number'],"启用赠品",$_SESSION['userName'],date("Y-m-d H:i:s"));
            $result['success'] = 1;
        }else{
            $result['error'] = "启用失败";
        }
        Util::jsonExit($result);
    }
    
    
    /*
     * 删除赠品
     */
    
    public function del(){
    	
    	$id =intval(_Post::getInt('id'));
    	//删除赠品需要判断是否绑定订单
    		$newmodel =new GiftGoodsModel($id,28);
    		$styles = $newmodel->getStyleById($id);
    		if(!empty($styles)){
    			$order = $newmodel->getXzInfo($styles['goods_number']);
              
    			if(!empty($order)){
    			 	foreach ($order as $k=>$v){
                 
    					if($v['order_status'] < 4){
    						//订单关闭
                            
    						$result['error'] = '订单未关闭，不能进行删除';
                            
    						Util::jsonExit($result);
    					}
    					if($v['send_good_status'] ==1){
    						//订单已发货
    						$result['error'] = '订单未发货，不能进行删除';
    						Util::jsonExit($result);
    					}
    				}
    			}
    		}
             
    	    
    		$res = $newmodel->delZpById($id);
    		
    		if($res !== false){
    		  $log=$newmodel->setgiftgoodslog($styles['goods_number'],"删除赠品",$_SESSION['userName'],date("Y-m-d H:i:s"));
    			$result['success'] = 1;
    		}else{
    			$result['error'] = "删除失败";
    		}
    		Util::jsonExit($result);
    	
    }
    
    
    
    
    
}

?>