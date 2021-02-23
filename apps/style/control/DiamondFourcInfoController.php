<?php
/**
 *  -------------------------------------------------
 *   @file		: DiamondFourcInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-02-27 15:27:55
 *   @update	:
 *  -------------------------------------------------
 */
class DiamondFourcInfoController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('getStoneAttrListHtml');
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('diamond_fourc_info_search_form.html',array('bar'=>Auth::getBar(),'view'=>new DiamondFourcInfoView(new DiamondFourcInfoModel(11))));
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
            'shape' => _Request::getInt('shape'),
            'carat_min' => _Request::getFloat('stone_min'),
            'carat_max' => _Request::getFloat('stone_max'),
            'color' => _Request::getString('color'),
            'clarity' => _Request::getString('clarity'),
            'cert' => _Request::getString('cert'),
            'status' => _Request::getInt('status')
		);
		$page = _Request::getInt("page",1);
		$where = array(
            'shape' => $args['shape'],
            'carat_min' => $args['carat_min'],
            'carat_max' => $args['carat_max'],
            'color' => $args['color'],
            'clarity' => $args['clarity'],
            'cert' => $args['cert'],
            'status' => $args['status']
        );

		$model = new DiamondFourcInfoModel(11);
		$data = $model->pageList($where,$page,10,false);
        $viewInfo = new DiamondFourcInfoView($model);
        $shape = $viewInfo->getShapeList();
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'diamond_fourc_info_search_page';
		$this->render('diamond_fourc_info_search_list.html',array(
            'shape' =>$shape,
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
		$result['content'] = $this->fetch('diamond_fourc_info_info.html',array(
			'view'=>new DiamondFourcInfoView(new DiamondFourcInfoModel(11))
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
		$result['content'] = $this->fetch('diamond_fourc_info_info.html',array(
			'view'=>new DiamondFourcInfoView(new DiamondFourcInfoModel($id,11)),
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
		$this->render('diamond_fourc_info_show.html',array(
			'view'=>new DiamondFourcInfoView(new DiamondFourcInfoModel($id,11)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

        $shape = _Post::getInt('shape');
        $stone_min = _Post::getFloat('stone_min');
        $stone_max = _Post::getFloat('stone_max');
        $color = _Post::getString('color');
        $clarity = _Post::getString('clarity');
        $cert = _Post::getString('cert');
        $price = _Post::getFloat('price');
        
        if(empty($shape)){
            $result['error'] = '主石形状不能为空';
            Util::jsonExit($result);
        }
        if(empty($color)){
            $result['error'] = '主石颜色不能为空';
            Util::jsonExit($result);
        }
        if(empty($clarity)){
            $result['error'] = '主石净度不能为空';
            Util::jsonExit($result);
        }
        if($stone_max<$stone_min){
            $result['error'] = '镶口最大值不能小于最小值';
            Util::jsonExit($result);
        }
        if(empty($cert)){
            $result['error'] = '证书类型不能为空';
            Util::jsonExit($result);
        }
        if($price<=0){
            $result['error'] = '价格必须大于0';
            Util::jsonExit($result);
        }
		$olddo = array();		
		$newdo=array('shape' => $shape,
            'carat_min' => $stone_min,
            'carat_max' => $stone_max,
            'color' => $color,
            'clarity' => $clarity,
            'cert' => $cert,
            'price' => $price,
            'status' => 1);
		$newmodel =  new DiamondFourcInfoModel(12);
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

        $shape = _Post::getInt('shape');
        $stone_min = _Post::getFloat('stone_min');
        $stone_max = _Post::getFloat('stone_max');
        $color = _Post::getString('color');
        $clarity = _Post::getString('clarity');
        $cert = _Post::getString('cert');
        $price = _Post::getFloat('price');

		$id = _Post::getInt('id');
		
		if(empty($shape)){
		    $result['error'] = '主石形状不能为空';
		    Util::jsonExit($result);
		}
		if(empty($color)){
		    $result['error'] = '主石颜色不能为空';
		    Util::jsonExit($result);
		}
		if(empty($clarity)){
		    $result['error'] = '主石净度不能为空';
		    Util::jsonExit($result);
		}
		if($stone_max<$stone_min){
		    $result['error'] = '镶口最大值不能小于最小值';
		    Util::jsonExit($result);
		}
		if(empty($cert)){
		    $result['error'] = '证书类型不能为空';
		    Util::jsonExit($result);
		}
		if($price<=0){
		    $result['error'] = '价格必须大于0';
		    Util::jsonExit($result);
		}
		$newmodel =  new DiamondFourcInfoModel($id,12);

		$olddo = $newmodel->getDataObject();
		$newdo=array('id' => $id,
            'shape' => $shape,
            'carat_min' => $stone_min,
            'carat_max' => $stone_max,
            'color' => $color,
            'clarity' => $clarity,
            'cert' => $cert,
            'price' => $price,
            'status' => 1);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '修改此处为想显示在页签上的字段';
			//修改日志记录
			$dataLog['pkdata'] = array('id'=>$id);
			$dataLog['newdata'] = $newdo;
			$dataLog['olddata'] = $olddo;
			$dataLog['fields']  = $newmodel->getFieldsDefine();
			$this->operationLog("update",$dataLog);
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	qiyong
	 */
	public function qiyong ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new DiamondFourcInfoModel($id,12);
		$do = $model->getDataObject();
		$valid = $do['status'];
		if($valid == 1)
		{
			$result['error'] = "当前记录已启用";
			Util::jsonExit($result);
		}
		$model->setValue('status',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
			//修改日志记录
			$dataLog['remark'] = "对ID=".$id."的情侣戒成品定制进行启用";
			$this->operationLog("update",$dataLog);
		}else{
			$result['error'] = "启用失败";
		}
		Util::jsonExit($result);
	}

    /**
     *  jinyong
     */
    public function jinyong ($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new DiamondFourcInfoModel($id,12);
        $do = $model->getDataObject();
        $valid = $do['status'];
        if($valid == 2)
        {
            $result['error'] = "当前记录已禁用";
            Util::jsonExit($result);
        }
        $model->setValue('status',2);
        $res = $model->save(true);
        if($res !== false){
            $result['success'] = 1;
            //修改日志记录
            $dataLog['remark'] = "对ID=".$id."的情侣戒成品定制进行禁用";
            $this->operationLog("update",$dataLog);
        }else{
            $result['error'] = "禁用失败";
        }
        Util::jsonExit($result);
    }
    
    public function getStoneAttrListHtml($params){
        $model = new DiamondFourcInfoModel(12);
        $colorArr = $model->getDistinctFields('color');
        $clarityArr = $model->getDistinctFields('clarity');
        
        $htmlArr = array(
            'color'=>'<option value=""></option>',
            'clarity'=>'<option value=""></option>',
        );
        $attrList = array(
            'color' =>$colorArr,
            'clarity' =>$clarityArr,
        );
        foreach ($attrList as $key=>$vo){            
            $optionArr = array();
            foreach ($vo as $v){
                $htmlArr[$key].="<option value=\"{$v}\">{$v}</option>";
            }
        }
        $result['success'] = 1;
        $result['data'] = $htmlArr;
        Util::jsonExit($result);
    }
}

?>