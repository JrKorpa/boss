<?php
/**
 *  -------------------------------------------------
 *   @file		: AppStyleTsydPriceController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-10 00:02:23
 *   @update	:
 *  -------------------------------------------------
 */
class AppStyleTsydPriceController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_style_tsyd_price_search_form.html',array('bar'=>Auth::getBar()));
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
			//'参数' = _Request::get("参数");
            'style_sn'	=> _Request::getString("style_sn"),
            'style_name'	=> _Request::getString("style_name"),
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['style_sn'] = $args['style_sn'];
		$where['style_name'] = $args['style_name'];

		$model = new AppStyleTsydPriceModel(11);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_style_tsyd_price_search_page';
		$this->render('app_style_tsyd_price_search_list.html',array(
			'pa'=>Util::page($pageData),
            'dd'=>new DictView(new DictModel(1)),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_style_tsyd_price_info.html',array(
			'view'=>new AppStyleTsydPriceView(new AppStyleTsydPriceModel(11))
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
		$result['content'] = $this->fetch('app_style_tsyd_price_info.html',array(
			'view'=>new AppStyleTsydPriceView(new AppStyleTsydPriceModel($id,11)),
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
		$this->render('app_style_tsyd_price_show.html',array(
			'view'=>new AppStyleTsydPriceView(new AppStyleTsydPriceModel($id,11)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
        $style_sn = _Request::getString('style_sn');
        $style_name = _Request::getString('style_name');
        $work = _Request::getString('work');
        $carat = _Request::getString('carat');
        $xiangkou_min = _Request::getString('xiangkou_min');
        $xiangkou_max = _Request::getString('xiangkou_max');
        $k_weight = _Request::getString('k_weight');
        $pt_weight = _Request::getString('pt_weight');
        $k_price = _Request::getString('k_price');
        $pt_price = _Request::getString('pt_price');
        $jumpto = _Request::getInt('jumpto');

        if($style_sn==''){
			$result['error'] = '款号不能为空';
    		Util::jsonExit($result);            
        }
        if($style_name==''){
			$result['error'] = '名称不能为空';
    		Util::jsonExit($result);            
        }
        if($work==''){
			$result['error'] = '工艺不能为空';
    		Util::jsonExit($result);            
        }
        if($carat==''){
			$result['error'] = '石重不能为空';
    		Util::jsonExit($result);            
        }
        if($xiangkou_min==''){
			$result['error'] = '镶口最小不能为空';
    		Util::jsonExit($result);            
        }
        if($xiangkou_max==''){
			$result['error'] = '镶口最大不能为空';
    		Util::jsonExit($result);            
        }
        if($k_weight==''){
			$result['error'] = '约18K金重不能为空';
    		Util::jsonExit($result);            
        }
        if($pt_weight==''){
			$result['error'] = '约Pt950金重不能为空';
    		Util::jsonExit($result);            
        }
        if($k_price==''){
			$result['error'] = '18K一对托定制价不能为空';
    		Util::jsonExit($result);            
        }
        if($pt_price==''){
			$result['error'] = 'PT950一对定制托价不能为空';
    		Util::jsonExit($result);            
        }
        if($jumpto==''){
			$result['error'] = '跳转地址不能为空';
    		Util::jsonExit($result);            
        }

		$olddo = array();
		$newdo=array();
		$newdo['style_sn']=$style_sn;
		$newdo['style_name']=$style_name;
		$newdo['work']=$work;
		$newdo['carat']=$carat;
		$newdo['xiangkou_min']=$xiangkou_min;
		$newdo['xiangkou_max']=$xiangkou_max;
		$newdo['k_weight']=$k_weight;
		$newdo['pt_weight']=$pt_weight;
		$newdo['k_price']=$k_price;
		$newdo['pt_price']=$pt_price;
		$newdo['jumpto']=$jumpto;

        $img_ori = _Post::getString('img_ori');

        if(empty($_FILES)){
			$result['error'] = '上传图片不能为空';
    		Util::jsonExit($result);         
        }
        
        //上传文件类型列表  
        $uptypes=array(  
            'image/jpg',  
            'image/jpeg',  
            'image/png',  
            'image/pjpeg',  
            'image/gif',  
            'image/bmp',  
            'image/x-png'  
        );
        if(!in_array($_FILES["img_ori"]['type'],$uptypes)){
			$result['error'] = '图片类型不能对';
    		Util::jsonExit($result);           
        }else{
            $type=str_replace('image/','.',$_FILES["img_ori"]['type']);
        }

        $path = KELA_ROOT.'/public/upload/style/tsyd/';
        $flag=0;
        if($_FILES["img_ori"]["name"]) 
        { 
            $today=date("YmdHis"); //获取时间并赋值给变量 
            $file2 = $path.$today.$type; //图片的完整路径 
            $img = $today.$type; //图片名称 
            $flag=1; 
        } 
        if($flag){
            move_uploaded_file($_FILES["img_ori"]["tmp_name"],$file2); 
            $newdo['pic']=str_replace(KELA_ROOT,'',$file2);
        }else{
            $newdo['pic']='';
        }


		$newmodel =  new AppStyleTsydPriceModel(12);
		$res = $newmodel->saveData($newdo,$olddo);
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
        $style_sn = _Request::getString('style_sn');
        $style_name = _Request::getString('style_name');
        $work = _Request::getString('work');
        $carat = _Request::getString('carat');
        $xiangkou_min = _Request::getString('xiangkou_min');
        $xiangkou_max = _Request::getString('xiangkou_max');
        $k_weight = _Request::getString('k_weight');
        $pt_weight = _Request::getString('pt_weight');
        $k_price = _Request::getString('k_price');
        $pt_price = _Request::getString('pt_price');
        $img_url = _Request::getString('img_url');
        $jumpto = _Request::getInt('jumpto');

        if($style_sn==''){
			$result['error'] = '款号不能为空';
    		Util::jsonExit($result);            
        }
        if($style_name==''){
			$result['error'] = '名称不能为空';
    		Util::jsonExit($result);            
        }
        if($work==''){
			$result['error'] = '工艺不能为空';
    		Util::jsonExit($result);            
        }
        if($carat==''){
			$result['error'] = '石重不能为空';
    		Util::jsonExit($result);            
        }
        if($xiangkou_min==''){
			$result['error'] = '镶口最小不能为空';
    		Util::jsonExit($result);            
        }
        if($xiangkou_max==''){
			$result['error'] = '镶口最大不能为空';
    		Util::jsonExit($result);            
        }
        if($k_weight==''){
			$result['error'] = '约18K金重不能为空';
    		Util::jsonExit($result);            
        }
        if($pt_weight==''){
			$result['error'] = '约Pt950金重不能为空';
    		Util::jsonExit($result);            
        }
        if($k_price==''){
			$result['error'] = '18K一对托定制价不能为空';
    		Util::jsonExit($result);            
        }
        if($pt_price==''){
			$result['error'] = 'PT950一对定制托价不能为空';
    		Util::jsonExit($result);            
        }
        if($jumpto==''){
			$result['error'] = '跳转地址不能为空';
    		Util::jsonExit($result);            
        }

		$newmodel =  new AppStyleTsydPriceModel($id,12);
		$olddo = $newmodel->getDataObject();

		$newdo=array();
		$newdo['id']=$id;
		$newdo['style_sn']=$style_sn;
		$newdo['style_name']=$style_name;
		$newdo['work']=$work;
		$newdo['carat']=$carat;
		$newdo['xiangkou_min']=$xiangkou_min;
		$newdo['xiangkou_max']=$xiangkou_max;
		$newdo['k_weight']=$k_weight;
		$newdo['pt_weight']=$pt_weight;
		$newdo['k_price']=$k_price;
		$newdo['pt_price']=$pt_price;
		$newdo['jumpto']=$jumpto;


        if(empty($_FILES) && empty($img_url)){
			$result['error'] = '上传图片或地址不能为空';
    		Util::jsonExit($result);         
        }
        
        if(!empty($_FILES)){
            //上传文件类型列表  
            $uptypes=array(  
                'image/jpg',  
                'image/jpeg',  
                'image/png',  
                'image/pjpeg',  
                'image/gif',  
                'image/bmp',  
                'image/x-png'  
            );
            if(!in_array($_FILES["img_ori"]['type'],$uptypes)){
                $result['error'] = '图片类型不能对';
                Util::jsonExit($result);           
            }else{
                $type=str_replace('image/','.',$_FILES["img_ori"]['type']);
            }

            $path = KELA_ROOT.'/public/upload/style/tsyd/';
            $flag=0;
            if($_FILES["img_ori"]["name"]) 
            { 
                $today=date("YmdHis"); //获取时间并赋值给变量 
                $file2 = $path.$today.$type; //图片的完整路径 
                $img = $today.$type; //图片名称 
                $flag=1; 
            } 
            if($flag){
                move_uploaded_file($_FILES["img_ori"]["tmp_name"],$file2); 
                $newdo['pic']=str_replace(KELA_ROOT,'',$file2);
            }else{
                $newdo['pic']='';
            }
        }else{
            if(empty($img_url)){
                $result['error'] = '图片地址不能为空';
                Util::jsonExit($result);                
            }
            $newdo['pic']=$img_url;

        }

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
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppStyleTsydPriceModel($id,12);
		$do = $model->getDataObject();
		//联合删除？
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>