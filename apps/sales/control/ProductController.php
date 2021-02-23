<?php
/**
 *  -------------------------------------------------
 *   @file		: DiamondListController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 21:06:55
 *   @update	:
 *  -------------------------------------------------
 */
class ProductController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }			
        //镶口
        $stone = array('0.000','0.100','0.200', '0.300', '0.400', '0.500', '0.600', '0.700', '0.800', '0.900', '1.000', '1.100', '1.200', '1.300', '1.400', '1.500');
        //手寸
        $finger = array('6','7','8','9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27');
        //材质
        $goodsAttrModel = new GoodsAttributeModel(17);
        $caizhi = $goodsAttrModel->getCaizhiList();
        $yanse  = $goodsAttrModel->getJinseList();

        $color_arr = $goodsAttrModel->getColorList();
        $clarity_arr = $goodsAttrModel->getClarityList();
        $shape_arr = $goodsAttrModel->getShapeList();
        $cert_arr = $goodsAttrModel->getCertList();
        
        $param['style_sn']=_Request::getString('style_sn');
        $res = $this->ChannelListO();
        if ($res === true) {
            //获取全部的有效的销售渠道
            $SalesChannelsModel = new SalesChannelsModel(1);
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        } else {
            $channellist = $this->getchannelinfo($res);
        }

		$this->render('product_form.html',array(
				'bar'=>Auth::getBar(),
				'channelList'=>$channellist,
                'Stone' => $stone,
                'Finger' => $finger,
                'Caizhi' => $caizhi,
                'Yanse' => $yanse,
                'param' => $param,
                'color_arr' => $color_arr,
                'clarity_arr' => $clarity_arr,
                'cert_arr' => $cert_arr,
			));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
        $goodsAttrModel = new GoodsAttributeModel(17);
        $caizhi = $goodsAttrModel->getCaizhiList();
        $yanse  = $goodsAttrModel->getJinseList();
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
            'goods_id'=>  _Request::getString('goods_id'),
			'goods_sn'=>  _Request::getString('goods_sn'),
			'isXianhuo'=>  _Request::getString('isXianhuo'),
			'finger'=>  _Request::getString('finger'),
			'caizhi'=>  _Request::getString('caizhi'),
			'yanse'=>  _Request::getString('yanse'),
            'xiangkou'=> _Request::getString('xiangkou'),
            'channel'=>_Request::get('channel'),
            'policy_name'=>_Request::getString('policy_name'),
            'tuo_type' =>_Request::getString('tuo_type'),
            'cert' =>_Request::getString('cert'),
            'color' =>_Request::getString('color'),
            'clarity' =>_Request::getString('clarity'),
		    'is_quick_diy'=>_Request::get('is_quick_diy'),
		);
		$args['goods_sn'] = empty($args['goods_id'])?$args['goods_sn']:$args['goods_id'];
		$where = array(
            'goods_id'=> $args['goods_sn'],
			'isXianhuo'=>$args['isXianhuo'],
			'channel'=> $args['channel'],
            'xiangkou'=>$args['xiangkou'],
            'finger'=>$args['finger'],
            'caizhi'=>$args['caizhi'],
            'yanse'=>$args['yanse'],
            'channel'=>$args['channel'],
            'policy_name'=>$args['policy_name'],
            'tuo_type' =>$args['tuo_type'],
            'cert' =>$args['cert'],
            'color' =>$args['color'],
            'clarity' =>$args['clarity'],
		    'is_quick_diy'=>$args['is_quick_diy'],
		    'is_more_line'=>1,//快速定制列表拆行
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$pageSize = isset($_REQUEST["pageSize"]) ? intval($_REQUEST["pageSize"]) : 15 ;
		$appSalepolicyGoodsModelR =new AppSalepolicyGoodsModel(15);
		
		$chengpindingzhi = $where['tuo_type']==1 && $where['isXianhuo']==0?1:0;
		//调试保险费
		//echo $appSalepolicyGoodsModelR->getbaoxianfei('宝石','0.6');die();

		//根据货品类型,去到对应的货品仓库找商品
		if($where['isXianhuo']==0)
		{
            //金托类型选择成品 ，巷口、款号必选
            if($where['tuo_type'] == 1 && $where['goods_id'] == ''){
                echo "<script>util.xalert('请输入款号或货号！提示：金拖类型选成品时 款号/货号 和镶口必填');</script>";
                exit;
            }
            if($where['tuo_type'] == 1 && $where['xiangkou'] == '' && strpos($where['goods_id'],'-') == false){
                echo "<script>util.xalert('请选择镶口！提示：金拖类型选成品时 款号/货号 和 镶口必填');</script>";
                exit;
            }
            if($where['tuo_type'] == 1){
                $pageSize = 200;//成品定制最多检索200个虚拟货号
            }
            //判断货号还是款号
            if(strpos($where['goods_id'],'-') !== false ){
                //所有的期货货号都带有-  符合,否则那就是款号
                $mast = explode('-', $where['goods_id']);
                $style_sn = $mast[0];
            }else{
                $style_sn = $where['goods_id'];
            }
            //根据款号查主石形状；
            $zhushipak = $appSalepolicyGoodsModelR->getStyleStone($style_sn);
            if(!empty($zhushipak)){
                //1、圆钻 2、异形钻
                $stone_cat = isset($zhushipak['stone_cat'])?$zhushipak['stone_cat']:'';
                $stone_attr = isset($zhushipak['stone_attr'])?$zhushipak['stone_attr']:'';
                // 钻石形状
                $_style_shape = array("垫形","公主方","祖母绿","心形","蛋形","椭圆形","橄榄形","三角形","水滴形","长方形","圆形","梨形","马眼形");
                switch ($stone_cat) {
                    case '1':
                        $shape = '圆钻';
                        break;
                    case '2':
                        $shape = '';
                        $attr_info = unserialize($stone_attr);
                        if(isset($attr_info['shape_zhushi']) && !empty($attr_info['shape_zhushi'])){
                            $shape = $_style_shape[$attr_info['shape_zhushi']-1];
                        }
                        break;
                    default:
                        $shape = '';
                        break;
                }
            }
            
			//找期货
			$data = $appSalepolicyGoodsModelR->pageQihuoList($where,$page,$pageSize);
            if(empty($data['data'])){
                $error = !empty($data['error'])?$data['error']:"搜索商品找不到销售政策！";
                echo "<script>util.xalert('{$error}');</script>";
                exit;
            }

            //这个判断不知道怎么判断是现货还是期货
            $company_type=$appSalepolicyGoodsModelR->userByCompany();
            if(!empty($data['data'][0]['company_type_id'])){
                $company_type_ids= explode(',',trim($data['data'][0]['company_type_id'],','));               
                if(!empty($company_type_ids) && !in_array($company_type, array_filter($company_type_ids))){
                    $error ="此款不支持定制，若有疑问请联系易霞核实";
                    echo "<script>util.xalert('{$error}');</script>";
                    exit;
                }                
            }

            
            if($data['pageCount']==1 && count($data['data'])<>$data['recordCount']){
                //结果页只有1页，组合商品记录与虚拟货号查询记录不相等时，以组合商品记录为准，用组合商品记录数 重置 总记录和每页显示数量
                $data['recordCount'] = count($data['data']);
                $data['pageSize'] = $data['recordCount']>$data['pageSize']?$data['recordCount']:$data['pageSize'];
            }

		}else{
			//找现货
			$dia_support_sapolicy= 0;
			//经销商的需要增加公司的过滤
			if( SYS_SCOPE == 'zhanting' )
			{
				//1、展厅系统如果登陆账号所属公司为总公司，
				//销售政策可以匹配总公司和所有门店的库存，如果所属公司为门店只能匹配自己门店的库存
				if($_SESSION['companyId'] != 58)
				{
					$is_company_check = Auth::user_is_from_base_company();
					if(!$is_company_check){
						$where['company_id_list'] = $_SESSION['companyId'];
					}
				}
					
				$companyModel = new CompanyModel(1);
				$company_type = $companyModel->select2("company_type","id={$_SESSION['companyId']}",3);
				if ($company_type == '3') {
					$dia_support_sapolicy = 1;
				}
				
			} 

			$data = $appSalepolicyGoodsModelR->pageXianhuoList($where,$page,$pageSize,$caizhi,$yanse,true,$dia_support_sapolicy);
			if(isset($data['error']) && $data['error'] == 1){
				//die($data['content']);	
				echo "<script>util.xalert('{$data['content']}');</script>";
				exit;
			}
		}
		
 		if(empty($data['data'][0]['sprice']))
		{
		    echo "<script>util.xalert('没有找到销售政策！');</script>";
		    exit;
		}  
		
		
		//
		
		$salesChannelsModel = new SalesChannelsModel(1);
		$getSalesChannelsInfo = $salesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
		//获取所有数据
		$allSalesChannelsData = array();
		foreach ($getSalesChannelsInfo as $val) {
		    $allSalesChannelsData[$val['id']] = $val['channel_name'];
		}
		
		$pageData = $data;
        $pageData['filter'] = $args;        
        
        
        if($where['xiangkou']!='' && !$chengpindingzhi){
            $pageData['jsFuncs'] = 'product_xiangkou_search_page';
            $html = 'product_xiangkou_search_list.html';
        }else{
            $pageData['jsFuncs'] = 'product_search_page';
            $html = 'product_search_list.html';
        }
        $pageData['jsFuncs'] = 'product_search_page';
        $is_ceshi = !in_array($_SERVER['HTTP_HOST'],array('boss.kela.cn','zhanting.kela.cn'))?1:0;
        $this->render($html,array(
            'pa'=>Util::page($pageData),
            'page_list'=>$pageData,
        	'channelList'=>$allSalesChannelsData,
            'Caizhi' => $caizhi,
            'Yanse' => $yanse,
            'chengpindingzhi'=>$chengpindingzhi,
            'xiangkou'=>$args['xiangkou'],
            'color'=>$args['color'],
            'clarity'=>$args['clarity'],
            'cert' =>$args['cert'],
            'tuo_type'=>$args['tuo_type'],
            'is_xianhuo'=>$args['isXianhuo'],
            'channel_id'=>$args['channel'],
            'is_ceshi' =>$is_ceshi
        ));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		//$e = new DiamondListView(new DiamondListModel(27));
		//var_dump($e);exit;
		$result['content'] = $this->fetch('diamond_list_info.html',array(
			'view'=>new DiamondListView(new DiamondListModel(19)),
			'_id'=>_Post::getInt('_id')

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
		$tab_id = _Post::getInt('tab_id');//主记录对应的列表页签id
		$result = array('success' => 0,'error' => '');
		//根据明细查主表id
		$model = new DiamondListModel($id,19);
		$_id = $model->getvalue('order_id');
		$result['content'] = $this->fetch('diamond_list_info.html',array(
			'view'=>new DiamondListView(new DiamondListModel($id,19)),
			'tab_id'=>$tab_id,
			'_id'=>$_id //主表id
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
		$model = new DiamondListModel(19);
		$data = $model->getRowById($id);
        if(empty($data['data'])){
            die('数据错误!');
        }
		$this->render('diamond_list_show.html',array(
			'd'=>$data['data'],
            'dd'=>new DictView(new DictModel(1)),
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		//echo '<pre>';
		//print_r ($_POST);
		//echo '</pre>';
		//exit;
		$goods_sn		=  _Post::get('goods_sn');
		$order_id		=  _Post::get('_id');
		$goods_name		=  _Post::get('goods_name');
		$goods_price	=  _Post::get('goods_price');
		$details_remark	=  _Post::get('details_remark');
		$date_time		= date('Y-m-d H:i:s',time());
		$olddo = array();
		$newdo=array(
			'order_id'=>$order_id,
			'goods_id'=>'123456',
			'goods_sn'=>$goods_sn,
			'goods_price'=>$goods_price,
			'details_remark'=>$details_remark,
			'create_time'=>$date_time,
			'create_user'=>$_SESSION['realName'],
			'modify_time'=>$date_time,
			'goods_name'=>$goods_name,
			'goods_count'=>1

			);

		$newmodel =  new DiamondListModel(20);
		$res = $newmodel->saveData($newdo,$olddo);
		//修改订单金额 查询货品总金额
		$all_money = $newmodel->goods_all_money($order_id);
		$order_model = new OrderModel($order_id,28);
		$order_model->setValue('order_price',$all_money);

		if(($res && $order_model->save())!== false)
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
		$id = _Post::getInt('id');
		//echo '<pre>';
		//print_r ($_POST);
		//echo '</pre>';
		//exit;
		$goods_sn		=  _Post::get('goods_sn');
		$goods_name		=  _Post::get('goods_name');
		$goods_price	=  _Post::get('goods_price');
		$details_remark	=  _Post::get('details_remark');
		$order_id		=  _Post::get('_id');
		$date_time		= date('Y-m-d H:i:s',time());
		$newmodel =  new DiamondListModel($id,20);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$id,
			'goods_id'=>'123456',
			'goods_sn'=>$goods_sn,
			'goods_price'=>$goods_price,
			'details_remark'=>$details_remark,
			'modify_time'=>$date_time,
			'goods_name'=>$goods_name,
		);

		$res = $newmodel->saveData($newdo,$olddo);

		$all_money = $newmodel->goods_all_money($order_id);
		$order_model = new OrderModel($order_id,28);
		$order_model->setValue('order_price',$all_money);

		if($res  && $order_model->save()!== false)
		{
			$result['success'] = 1;
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

		$model = new DiamondListModel($id,20);
		$order_id = $model->getValue('order_id');
		$do = $model->getDataObject();

		$model->setValue('details_status',3); //删除状态
		$res = $model->save(true);
		//修改订单金额 查询货品总金额
		$all_money = $model->goods_all_money($order_id);
		//var_dump($all_money);exit;
		$order_model = new OrderModel($order_id,28);
		if ($all_money == false)
		{
			$all_money = 0;
		}
		//var_dump($all_money);
		$order_model->setValue('order_price',$all_money);
		$order_model->save();
		if($res)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
    
    public function addCart(){
        $result = array('success' => 0,'error' => '');
        $ids_arr = _Request::getList('_ids');
         //获取渠道当前权限
        $res= $this->ChannelListO();

        $where['department_in'] = '';
        if($res===true){//管理员权限可以查所有渠道的销售政策
            $SalesChannelsModel = new SalesChannelsModel(1);
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        }else{
            $channellist = $this->getchannelinfo($res);
            if(!empty($channellist)){
                foreach ($channellist as $val){
                    $department_id[] = $val['id'];
                }
                $where['department_in'] = implode(",", $department_id); 
            }
        }
        
        $qudao = array();
        foreach ($channellist as $val){
            $qudao[$val['id']]= $val['channel_name'];
        }
        
        $xiangkou = "";
        $color = "";
        $clarity = "";
        $cert = "";
        $tuo_type = "";
        $is_xianhuo = "";
        $channel_id = "";
        foreach ($ids_arr as $ids){
           $arr = explode('|',$ids);
           $goods_ids[] = $arr[0];
           $xiangkou = !empty($arr[1])?$arr[1]:0;
           $color = !empty($arr[2])?$arr[2]:"";
           $clarity = !empty($arr[3])?$arr[3]:"";
           $cert = !empty($arr[4])?$arr[4]:"";
           $tuo_type = !empty($arr[5])?$arr[5]:"";
           $is_xianhuo = !empty($arr[6])?$arr[6]:"";
           $channel_id = !empty($arr[7])?$arr[7]:"";
        }
        
        $ids = implode(',', $ids_arr);
        $result['content'] = $this->fetch('add_cart.html',array(
            'ids'=>$ids,
            'qudao'=>$qudao,
            'channel_id'=>$channel_id
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);

    }

    public function getChannelprice(){
		$goodsAttrModel = new GoodsAttributeModel(17);
        $caizhi = $goodsAttrModel->getCaizhiList();
        $yanse  = $goodsAttrModel->getJinseList();
		
        $error =1;
        $ids_arr = explode(',',_Request::getString('ids'));
        $department = _Request::getInt('qudao');
        if(empty($ids_arr)){
            $content ="请选择商品";
            $this->render('compare_price.html',array('error'=>$error,'content'=>$content));
            die;
        }
        if(empty($department)){
            $content ="请选择渠道";
            $this->render('compare_price.html',array('error'=>$error,'content'=>$content));
            die;
        }
        $cart = 1;
        $appSalepolicyGoodsModelR =new AppSalepolicyGoodsModel(15);
        
        $goods_ids = array();
        $xiangkou = "";
        $color = "";
        $clarity = "";
        $cert = "";
        $tuo_type = "";
        $is_xianhuo = "";
        $channel_id = "";
        $goods_key = "";
        $is_cpdz = 0;
        foreach ($ids_arr as $ids){
           $where = array();
           $arr = explode('|',$ids);
           $goods_id = $arr[0];
           $goods_ids[] = $arr[0];
           $xiangkou = !empty($arr[1])?$arr[1]:0;
           $color = !empty($arr[2])?$arr[2]:"";
           $clarity = !empty($arr[3])?$arr[3]:"";
           $cert = !empty($arr[4])?$arr[4]:"";
           $tuo_type = !empty($arr[5])?$arr[5]:"";
           $is_xianhuo = !empty($arr[6])?$arr[6]:"";
           $channel_id = !empty($arr[7])?$arr[7]:"";
           $goods_key = !empty($arr[8])?$arr[8]:"";

           if($tuo_type==1 && $is_xianhuo==0){
               $is_cpdz = 1;
               $where['goods_id'] = $goods_id;
               $where['tuo_type'] = 1;
               $where['xiangkou'] = $xiangkou;
               $where['color'] = $color;
               $where['clarity'] = $clarity;
               $where['cert'] = $cert;
               $where['goods_key'] = $goods_key;
               $where['department'] = $department;
               $where['channel']= $department;            
               $data = $appSalepolicyGoodsModelR->getChenpingdingzhiList($where,1,20);
               if(!empty($data['data'][0])){
                   $goods_info['data'][] = $data['data'][0];
               }
           }
        }
		if($is_cpdz <> 1){
		    $where['goods_id_in'] = implode("','",$goods_ids);
		    $where['department'] = $department;
		    $where['channel']= $department;
    		if(strpos($where['goods_id_in'],'-') !== false)
    		{		    
    			$goods_info = $appSalepolicyGoodsModelR->pageQihuoList($where,1,20);
    		}else{
    			//经销商的需要增加公司的过滤
    			$dia_support_sapolicy = 0;
    			if( SYS_SCOPE == 'zhanting' )
    			{
    				$is_company_check = Auth::user_is_from_base_company();
    				if(!$is_company_check){
    					$where['company_id_list'] = $_SESSION['companyId'];
    				}
    				
    				$companyModel = new CompanyModel(1);
    				$company_type = $companyModel->select2("company_type","id={$_SESSION['companyId']}",3);
    				if ($company_type == '3') {
    					$dia_support_sapolicy = 1;
    				}
    			}
    			$goods_info = $appSalepolicyGoodsModelR->pageXianhuoList($where,1,20,$caizhi,$yanse, true, $dia_support_sapolicy);
    		}
		}
		if(empty($goods_info['data'])){
            $content ="销售政策中没有此商品或已经下架!";
            $this->render('compare_price.html',array('error'=>$error,'content'=>$content));
            die;
        }
        $this->render('compare_price.html',array(
            'dabao_data'=>array(),
            'channel_id'=>$channel_id,
            'is_cpdz'=>$is_cpdz,
            'putong_data'=>$goods_info['data'],
            'error'=>0
        )); 
    }


    public function DelCartGoods(){
        $result = array('success' => 1,'error' => '');
        $cartModel = new AppOrderCartModel(27);
        $id = _Request::getInt('id');
//        $is_falg = $cartModel->delete_cart_goods_by_policy_goods_id($policy_$idgoods_id,$cart_goods);
        $is_falg = $cartModel->delete_cart_goods_by_id($id);
        if($is_falg){
            $this->GetCartGoods();die;
        }else{
            $result['success'] =0;
            $result['error'] =1;
        }
        Util::jsonExit($result);
    }

    public function GetCartGoods(){
        $cartModel = new AppOrderCartModel(27);
        $all_data = $cartModel->get_cart_goods();
       // var_dump($all_data);die;
        $this->render('cart_goods_list.html',array('goods'=>$all_data));
    }
    
    //保存购物车
    public function SaveCartGoods(){
        $result = array('success' => 1,'error' => '');
        $department  = _Request::getInt('department');
		$goods_id  = _Request::getString('goodsid');
        $type = _Request::getInt('type');
        $keys_arr = explode("|",_Request::getString('keys'));
		//是否现货
		$isXianhuo = _Request::getInt('isxianhuo');
        $id = _Request::getString('id');
        $chengpindingzhi = _Request::getString('chengpindingzhi');
        //排除如果部门不相同则需要清空购物车
        $cartModel = new AppOrderCartModel(27);
        $cart_goods = $cartModel->get_cart_goods();
        $is_falg = $cartModel->is_department($department, $cart_goods);
        if(!$is_falg){
            $result['error'] = 1;
            $result['content'] = '您购物车中的商品的销售渠道和您当前选择的渠道部门不一致';
            Util::jsonExit($result);
        }else if(empty($keys_arr) || count($keys_arr)<9){
            $result['error'] = 'ids 参数错误';
            Util::jsonExit($result);
        }
        $goods_id = !empty($keys_arr[0])?$keys_arr[0]:"";
        $xiangkou = !empty($keys_arr[1])?$keys_arr[1]:0;
        $color = !empty($keys_arr[2])?$keys_arr[2]:"";
        $clarity = !empty($keys_arr[3])?$keys_arr[3]:"";
        $cert = !empty($keys_arr[4])?$keys_arr[4]:"";
        $tuo_type = !empty($keys_arr[5])?$keys_arr[5]:"";
        $is_xianhuo = !empty($keys_arr[6])?$keys_arr[6]:"";
        $channel_id = !empty($keys_arr[7])?$keys_arr[7]:"";
        $goods_key = !empty($keys_arr[8])?$keys_arr[8]:"";
        
        if(empty($goods_id)){
            $result['error'] = '货号为空！';
            Util::jsonExit($result);
        }
        
        $where = array(
            'goods_id'=>$goods_id,
            'department'=>$department,
            'channel'=>$department,
            'type'=>$type,
            'policy_id'=>$id            
        );
        //成品定制
        if($tuo_type==1 && $is_xianhuo==0){
            $where['is_more_line'] = 1;
            $where['tuo_type'] = 1;
            $where['xiangkou'] = $xiangkou;
            $where['color'] = $color;
            $where['clarity'] = $clarity;
            $where['cert'] = $cert;
        }

        $goodsAttrModel = new GoodsAttributeModel(17);
        $Caizhi = $goodsAttrModel->getCaizhiList();
        $Yanse = $goodsAttrModel->getJinseList();
		$appSalepolicyGoodsModelR =new AppSalepolicyGoodsModel(15);
		if($isXianhuo>0)
		{
			//经销商的需要增加公司的过滤
			$dia_support_sapolicy= 0;
			if( SYS_SCOPE == 'zhanting' )
			{
				$is_company_check = Auth::user_is_from_base_company();
				if(!$is_company_check){
					$where['company_id_list'] = $_SESSION['companyId'];
				}
				
				$companyModel = new CompanyModel(1);
				$company_type = $companyModel->select2("company_type","id={$_SESSION['companyId']}",3);
				if ($company_type == '3') {
					$dia_support_sapolicy = 1;
				}
			}
			$goods_info = $appSalepolicyGoodsModelR->pageXianhuoList($where,1,10,$Caizhi,$Yanse, true, $dia_support_sapolicy);
		}else{
			//经销商的需要增加公司的过滤
			if( SYS_SCOPE == 'zhanting' )
			{
				$is_company_check = Auth::user_is_from_base_company();
				if(!$is_company_check){
					$where['company_id_list'] = $_SESSION['companyId'];
				}
			}

			$goods_info = $appSalepolicyGoodsModelR->pageQihuoList($where,1,20,$Caizhi);
			if(empty($goods_info['data'])){
			    $goods_info['error'] = 1;
			    $goods_info['data'] ="虚拟货号不存在！";
			}else{
			    $goods_info['error'] = 0;
			}
		}
				
        if(!empty($goods_info['error'])){
            $result['error'] = 1;
            $result['content'] = $goods_info['data'];
            Util::jsonExit($result);
        }  
        foreach ($goods_info['data'] as $val){
            if(!empty($val['goods_key'])){
                $goods_key = $val['goods_key'];
            }else{
                $goods_key = $val['id'];
            }
            $is_have = $cartModel->check_cart_goods($goods_key);
            if($is_have){
                $result['error'] = 1;
                $result['content'] = "此商品已经加入购物车";
                Util::jsonExit($result);
            }
        }
              
       /*  $is_have = $cartModel->check_cart_goods($goods_key);
        if($is_have){
            $result['error'] = 1;
            $result['content'] = "此商品已经加入购物车";
            Util::jsonExit($result);
        }      
 */
        foreach ($goods_info['data'] as $val){    
            
            $parent = array();
            $parent["session_id"]=DBSessionHandler::getSessionId();
            $parent["goods_id"]=$val['goods_id'];
            $parent["goods_sn"]=$val['goods_sn'];
            $parent["goods_price"]=$val['sale_price'];
            $parent["is_stock_goods"]=$val['isXianhuo'];
            //$parent["is_stock_goods"]=$is_xianhuo;
            $parent["goods_type"]='style_goods';
            $parent["favorable_price"]=0;//优惠价格
            $parent["goods_count"]=1;
            $parent["goods_name"]=$val['goods_name'];
            $parent["create_time"]=  date("Y-m-d H:i:s");
            $parent["modify_time"]= date("Y-m-d H:i:s");
            $parent["create_user"]=$_SESSION['userName'];
            $parent["department_id"]=$val['channel'];
            $parent["policy_goods_id"]=$val['id'];
            $parent["type"]=1;  		   //默认用1 普通政策
            $parent["cart"]=empty($val['stone'])?$val['zuanshidaxiao']:$val['stone']; //没有
            $parent["xiangkou"]=$val['xiangkou'];
            $parent["product_type"]=$val['product_type'];
            $parent["cat_type"]=$val['category'];
            $parent["zhiquan"]=$val['finger'];
            $parent['tuo_type'] = $this->dd->getEnum('warehouse_goods.tuo_type',$tuo_type);            
            $parent["jinzhong"]=0;
            $parent["kezi"]='';
            $parent["face_work"]='';
            $parent["kuan_sn"]='';
            $parent["xiangqian"]='不需工厂镶嵌';
            $parent["goods_key"]=$goods_key;
			if( $val['isXianhuo'] == 1 ){
				$parent["jinse"]=$val['yanse'];
            	$parent["caizhi"]=$val['caizhi'];
				if(isset($val['tuo_type']) && $val['tuo_type']==1)
				{
					$parent["xiangqian"] = "工厂配钻，工厂镶嵌";
				}
				$parent['zhengshuhao']=$val['zhengshuhao'];
                $parent['jinzhong']=$val['jinzhong'];
				$parent['cart'] = $val['cart'];
				$parent['cut'] = $val['cut'];
				$parent['clarity']=$val['clarity'];
				$parent['color']=$val['color'];
			}else{
				$parent["jinse"]=$Yanse[$val['yanse']];
            	$parent["caizhi"]=$Caizhi[$val['caizhi']];	
			}
			//成品定制
			if($tuo_type ==1 && $val['isXianhuo']==0){
			    if($val['goods_key']==$goods_key){
			        $parent['goods_price'] = $val['sale_price'];
			        $parent['color'] = $val['color'];
			        $parent['cert'] = $val['cert'];
			        $parent['clarity']=$val['clarity'];
			        $parent["xiangqian"] = $val['xiangkou']>0?"工厂配钻，工厂镶嵌":"不需工厂镶嵌";
			    }	
			}
			//print_r($val);
			//print_r($parent);
            $res = $cartModel->add_cart($parent);
            if($res===false){
                $result['error'] = 1;
                $result['content'] = "添加失败";
                Util::jsonExit($result);
            } 
        }
        $this->GetCartGoods();
        die;        
    }
    /**
     * 讲库房数据格式化成标准数据
     * @param unknown $info
     * @return multitype:number unknown string
     */
    public function getWarehouseData($info) {
        $new_goods_info = array();
        $goodsAttrModel = new GoodsAttributeModel(17);
        $caizhi_arr = $goodsAttrModel->explodeZhuchengseToStr($info['caizhi']);
        $new_goods_info['caizhi'] = $caizhi_arr['caizhi'];
        $new_goods_info['jinse'] = $caizhi_arr['jinse'];
        $new_goods_info['goods_name'] = $info['goods_name'];
        $new_goods_info['goods_sn'] = $info['goods_sn'];
        $new_goods_info['cart'] = $info['zuanshidaxiao'];
        $new_goods_info['clarity'] = strtoupper($info['jingdu']);
        $new_goods_info['color'] = strtoupper($info['yanse']);
        $new_goods_info['zhengshuhao'] = $info['zhengshuhao'];        
        $new_goods_info['zuanshidaxiao'] = $info['zuanshidaxiao'];
        $new_goods_info['jinzhong'] = $info['jinzhong'];
        $new_goods_info['zhiquan'] = $info['shoucun'];
        $new_goods_info['xiangkou'] = $info['jietuoxiangkou'];
        $new_goods_info['favorable_price'] = 0;  
        $new_goods_info['tuo_type'] = $info['tuo_type'];
        
        return $new_goods_info;
    }
    /**
     * 根据货号，获取款式属性
     * @param unknown $goods_id
     */
    public function getStyleGoodsData($goods_id){
        $arr_jinse =array('W'=>'白','Y'=>'黄','R'=>'玫瑰金','C'=>'分色');
        $goods_id_arr = explode("-", $goods_id);
        $style_goods = array();
        if(count($goods_id_arr)==5){
             $style_goods['goods_sn'] = $goods_id_arr[0];             
             $style_goods['caizhi'] = $goods_id_arr[1];
             $style_goods['xiangkou'] = $goods_id_arr[3]/100;
             $style_goods['zhiquan'] = $goods_id_arr[4];
             $style_goods['cart'] = $style_goods['xiangkou'];
             if(isset($arr_jinse[$goods_id_arr[2]])){
                 $style_goods['jinse'] = $arr_jinse[$goods_id_arr[2]];
             }else{
                 $style_goods['jinse'] = '';
             }
        }        
        return $style_goods;
    }

    //解析主副石信息
    public function getStyleStoneInfo($style_sn,$style_stone,$style_stone_cat)
    {
        include(CONFIG_DIR."stone_config.php");
        # code...
        $data = array(
            '主石信息'=>'',
            '副石信息'=>''
            );
        //echo '<pre>';
        //print_r($_style_sec_stone_cat);die;
        $stoneInfo = isset($style_stone[$style_sn])?$style_stone[$style_sn]:'';
        if(!empty($stoneInfo)){
            $name = array();
            $name2 = array();
            $stone_str_z = '';//主石
            $stone_str_f = '';//副石
            foreach ($stoneInfo as $k => $v) {
                # code...
                if($k == 1){
                    foreach ($v as $key => $value) {
                        # code...
                        if($key != 0){
                            $stone_str_z = unserialize($value);
                        }
                        $name = array();
                        if(isset($stone_str_z['weight']) && !empty($stone_str_z['weight'])){
                            $name[] = $stone_str_z['weight'];
                        }else{
                            $name[] = '空';
                        }
                        if(isset($stone_str_z['number']) && !empty($stone_str_z['number'])){
                            $name[] = $stone_str_z['number'];
                        }else{
                            $name[] = '空';
                        }
                        if(isset($stone_str_z['xiangkou_start']) && isset($stone_str_z['xiangkou_end'])){
                            if(empty($stone_str_z['xiangkou_start']) && empty($stone_str_z['xiangkou_end'])){
                                $name[] = '空';
                            }else{
                                $name[] = $stone_str_z['xiangkou_start'].'-'.$stone_str_z['xiangkou_end'];
                            }
                        }else{
                            $name[] = '空';
                        }
                    }
                }if($k == 2){
                    foreach ($v as $key => $value) {
                        # code...
                        if($key != 0){
                            $stone_str_f = unserialize($value);
                            //print_r($stone_str_f);die;
                        }

                        $name2 = array();
                        if(isset($stone_str_f['weight']) && !empty($stone_str_f['weight'])){
                            $name2[] = $stone_str_f['weight'];
                        }else{
                            $name2[] = '空';
                        }
                        if(isset($stone_str_f['number']) && !empty($stone_str_f['number'])){
                            $name2[] = $stone_str_f['number'];
                        }else{
                            $name2[] = '空';
                        }
                        if(isset($stone_str_f['xiangkou_start']) && isset($stone_str_f['xiangkou_end'])){
                            if(empty($stone_str_f['xiangkou_start']) && empty($stone_str_f['xiangkou_end'])){
                                $name2[] = '空';
                            }else{
                                $name2[] = $stone_str_f['xiangkou_start'].'-'.$stone_str_f['xiangkou_end'];
                            }
                        }else{
                            $name2[] = '空';
                        }
                    }
                }
            }
            $main_name = isset($_style_main_stone_cat[$style_stone_cat[$style_sn][1]]['stone_name'])?$_style_main_stone_cat[$style_stone_cat[$style_sn][1]]['stone_name']:'无';
            $sec_name = isset($_style_sec_stone_cat[$style_stone_cat[$style_sn][2]]['stone_name'])?$_style_sec_stone_cat[$style_stone_cat[$style_sn][2]]['stone_name']:'无';
            $data['主石信息'] = $main_name.'|'.implode('|',$name);
            $data['副石信息'] = $sec_name.'|'.implode('|',$name2);
            return $data;
        }else{
            $data['主石信息'] = '空';
            $data['副石信息'] = '空';
            return $data;
        }
    }


}

?>