<?php
/**
 *  -------------------------------------------------
 *   @file		: StockPurchaseSearchController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-08-03 10:02:03
 *   @update	:
 *  -------------------------------------------------
 */
class StockPurchaseSearchController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $goodsAttrList = $this->getGoodsAttrList(); //获取默认属性列表  
        $channelModel = new UserChannelModel(1);//渠道
        $allshop = $channelModel->getAllChannels();
		$this->render('stock_purchase_search_search_form.html',array('bar'=>Auth::getBar(),'allshop'=>$allshop,
            'jinse_arr' =>$goodsAttrList['jinse_arr'],
            'caizhi_arr'=>$goodsAttrList['caizhi_arr']));
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
			'channel_id'    => _Request::get("channel_id"),
            'style_sn'   => _Request::get("style_sn"),
            'zuanshidaxiao'   => _Request::get("zuanshidaxiao"),
            'yanse'   => _Request::get("yanse"),
            'jingdu'   => _Request::get("jingdu"),
            'caizhi'   => _Request::get("caizhi"),
            '18k_color'   => _Request::get("18k_color"),
            'zhiquan'   => _Request::get("zhiquan")
		);
        if(empty($args['channel_id'])){
            exit('提示：请选择销售渠道');
        }
        if(empty($args['style_sn'])){
            exit('提示：请输入款号');
        }
        //if(empty($args['zuanshidaxiao'])){
        //    exit('提示：请输入主石单颗重');
        //}
        //if(empty($args['yanse'])){
        //    exit('提示：请输入钻石颜色');
        //}
        //if(empty($args['jingdu'])){
        //    exit('提示：请输入钻石净度');
        //}
        //if(empty($args['caizhi'])){
        //    exit('提示：请输入材质');
        //}
        //if(empty($args['18k_color'])){
        //    exit('提示：请输入18K可做色');
        //}
        //if(empty($args['zhiquan'])){
        //    exit('提示：请输入指圈');
        //}
		$page = _Request::getInt("page",1);
		$where = array(
            'channel_id'    => _Request::get("channel_id"),
            'style_sn'   => _Request::get("style_sn"),
            'zuanshidaxiao'   => _Request::get("zuanshidaxiao"),
            'yanse'   => _Request::get("yanse"),
            'jingdu'   => _Request::get("jingdu"),
            'caizhi'   => _Request::get("caizhi"),
            '18k_color'   => _Request::get("18k_color"),
            'zhiquan'   => _Request::get("zhiquan")
            );
		$model = new StockPurchaseSearchModel(23);
		$data = $model->pageList($where,$page,100000,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'stock_purchase_search_search_page';
		$this->render('stock_purchase_search_search_list.html',array(
            'view'=>new StockPurchaseSearchView(new StockPurchaseSearchModel(23)),
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
		$result['content'] = $this->fetch('stock_purchase_search_info.html',array(
			'view'=>new StockPurchaseSearchView(new StockPurchaseSearchModel(23))
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
		$result['content'] = $this->fetch('stock_purchase_search_info.html',array(
			'view'=>new StockPurchaseSearchView(new StockPurchaseSearchModel($id,23)),
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
		$this->render('stock_purchase_search_show.html',array(
			'view'=>new StockPurchaseSearchView(new StockPurchaseSearchModel($id,23)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;
		$olddo = array();
		$newdo=array();

		$newmodel =  new StockPurchaseSearchModel(24);
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
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new StockPurchaseSearchModel($id,24);

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
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new StockPurchaseSearchModel($id,24);
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

    public function getGoodsAttrList($style_sn = ''){
        //$caizhi_arr=array('1'=>'默认','2'=>'无','3'=>'9K','4'=>'10K','5'=>'18K','6'=>'24K','7'=>'PT950','8'=>'PT900','9'=>'S925' );
        //$jinse_arr=array('1'=>'默认','2'=>'无','3'=>'按图做','4'=>'玫瑰金','5'=>'白','6'=>'黄','7'=>'黄白','8'=>'彩金','9'=>'分色' );
        //定义默认属性列表
        $goodsAttrModel = new GoodsAttributeModel(17);
        $caizhi_arr = $goodsAttrModel->getCaizhiList(false);//false 读取属性数据库维护值 ，true 读取 固定维护值   
        $jinse_arr  = $goodsAttrModel->getJinseList(false);//false 读取属性数据库维护值 ，true 读取 固定维护值
        $cert_arr  = $goodsAttrModel->getCertList(false);//false 读取属性数据库维护值 ，true 读取 固定维护值
        $xiangqian_arr = $goodsAttrModel->getXiangqianListNew(false);
        $facework_arr = $goodsAttrModel->getFaceworkList(false);//false 读取属性数据库维护值 ，true 读取 固定维护值
        if($style_sn !=''){
            $apiStyle = new ApiStyleModel();
            $attres = $apiStyle->GetStyleAttribute($style_sn);
            if(!empty($attres) && is_array($attres)){
                
                $attr_list = array();
                //格式化属性数组结构，让attribute_code作为键值
                foreach($attres as $key=>$vo){
                     $attrcode = $vo['attribute_code'];                       
                     $attr_list[$attrcode] = $vo;
                }
            
                //获取材质属性列表,如果不为空覆盖默认材质属性列表
                if(!empty($attr_list['caizhi']['value'])){
                    $caizhi_arr = $attr_list['caizhi']['value'];
                }
                //获取材质颜色属性列表,如果不为空覆盖默认材质颜色属性列表
                if(!empty($attr_list['caizhiyanse']['value'])){
                    $jinse_arr = $attr_list['caizhiyanse']['value'];
                }
                if(!empty($attr_list['zhengshu']['value'])){
                    $cert_arr = $attr_list['zhengshu']['value'];
                }               
            }
        } 
        $data = array(
            'caizhi_arr'=>$caizhi_arr,
            'jinse_arr' =>$jinse_arr,
            'cert_arr' =>$cert_arr,
            'xiangqian_arr'=>$xiangqian_arr,
            'facework_arr'=>$facework_arr 
        );
        return $data;
    }
}

?>