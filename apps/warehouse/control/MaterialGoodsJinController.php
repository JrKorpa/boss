<?php
/**
 *  -------------------------------------------------
 *   @file		: MaterialGoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2018-01-18 11:08:37
 *   @update	:
 *  -------------------------------------------------
 */
class MaterialGoodsJinController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render(
		    'material_goods_search_form.html',
            array('bar'=>Auth::getBar())
        );
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
            'style_sn' => _Request::get('style_sn'),
            'goods_sn'  => _Request::get('goods_sn'),
            'style_name' => _Request::get('style_name'),
            'goods_name' => _Request::get('goods_name'),
            'goods_spec' => _Request::get('goods_spec'),
            'catetory1' => _Request::get('catetory1'),
            'catetory2' => _Request::get('catetory2'),
            'catetory3' => _Request::get('catetory3'),
            'style_name' => _Request::get('style_name'),
            'cost' => _Request::get('cost'),
            'goods_status' => _Request::get('goods_status'),
		);

		$page = _Request::getInt("page",1);
		$where = $args;

		$model = new MaterialGoodsJinModel(21);
		$data = $model->pageList($where,$page,30,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'material_goods_search_page';
		$this->render('material_goods_search_list.html',array(
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
		$result['content'] = $this->fetch('material_goods_info.html',array(
			'view'=>new MaterialGoodsJinView(new MaterialGoodsJinModel(21))
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
		$result['content'] = $this->fetch('material_goods_info.html',array(
			'view'=>new MaterialGoodsJinView(new MaterialGoodsJinModel($id,21)),
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
		$this->render('material_goods_show.html',array(
			'view'=>new MaterialGoodsJinView(new MaterialGoodsJinModel($id,21)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
        $args = array(
            'mod'	=> _Request::get("mod"),
            'con'	=> substr(__CLASS__, 0, -10),
            'act'	=> __FUNCTION__,
            //'参数' = _Request::get("参数");
            'style_sn' =>  _Request::get('style_sn'),
            //'style_name' => _Request::get('style_name'),
            //'goods_sn'   =>  _Request::get('goods_sn'),
            'goods_name' =>  _Request::get('goods_name'),
            'goods_spec' =>  _Request::get('goods_spec'),
            'catetory1'  =>  _Request::get('catetory1'),
            'catetory2'  =>  _Request::get('catetory2'),
            'catetory3'  =>  _Request::get('catetory3'),
            //'cost'       =>  _Request::get('cost'),
            'unit'       =>  _Request::get('unit'),
            'goods_sale_price'       =>  _Request::getFloat('goods_sale_price',0.00),
            'goods_jiajialv' =>  _Request::getFloat('goods_jiajialv',0.00),
            'goods_type' =>  _Request::get('goods_type'),
            'min_qty'=> _Post::getInt('min_qty'),
            'pack_qty'=> _Post::getInt('pack_qty'),
            'caizhi'=> _Post::getString('caizhi'),
            'remark'=> _Post::getString('remark'),
        );
        $style_info_model = new BaseStyleInfoModel(12);
        $res = $style_info_model->getStyleInfo('style_name',array('style_sn'=>$args['style_sn']));
        if(!$res){
            $result['error'] = '添加失败';
        }
		$olddo = array();
        $newdo=array(
            'style_sn'   =>  $args['style_sn'],
            'style_name' =>  $res['style_name'],
            'goods_sn'   =>  '',
            'goods_name' =>  $args['goods_name'],
            'goods_spec' =>  $args['goods_spec'],
            'catetory1'  =>  $args['catetory1'],
            'catetory2'  =>  $args['catetory2'],
            'catetory3'  =>  $args['catetory3'],
            //'cost'       =>  $args['cost'],
            'unit'       =>   $args['unit'],
            'create_user'=>  $_SESSION['userName'],
            'create_time'=>  date('Y-m-d H:i:s'),
            'goods_sale_price' => $args['goods_sale_price'],
            'goods_jiajialv' => $args['goods_jiajialv'],
            'goods_type' => $args['goods_type'],
            'min_qty'=> $args['min_qty'],
            'pack_qty'=>$args['pack_qty'],
            'caizhi'=> $args['caizhi'],
            'remark'=> $args['remark'],
        );

		$newmodel =  new MaterialGoodsJinModel(22);
		$pdo = $newmodel->db()->db();
		try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            $res = $newmodel->saveData($newdo,$olddo);
            if(!$res){
                throw new Exception("添加失败");
            }
            $goods_sn = $this->getGoodsSn($newdo['style_sn'],$res,$newdo['catetory1'],$newdo['catetory2']);
            $newdo = array(
                'goods_sn' => $goods_sn
            );
            $res = $newmodel->update($newdo,"id={$res}");
            if(!$res){
                throw new Exception("添加失败");
            }
            $pdo->commit();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//关闭sql语句自动提交
            $result['success'] = 1;
        }catch(Exception $e){
            $pdo->rollback(); //事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            $result['error'] = $e->getMessage();
        }
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		/*$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');*/
        $args = array(
            'mod'	=> _Request::get("mod"),
            'con'	=> substr(__CLASS__, 0, -10),
            'act'	=> __FUNCTION__,
            //'参数' = _Request::get("参数");
            'style_sn'   =>  _Request::get('style_sn'),
            'style_name' =>  _Request::get('style_name'),
            //'goods_sn'   =>  _Request::get('goods_sn'),
            'goods_name' =>  _Request::get('goods_name'),
            'goods_spec' =>  _Request::get('goods_spec'),
            'catetory1'  =>  _Request::get('catetory1'),
            'catetory2'  =>  _Request::get('catetory2'),
            'catetory3'  =>  _Request::get('catetory3'),
            //'cost'   =>  _Request::get('cost'),
            'unit'   =>  _Request::get('unit'),
            'tab_id'     =>  _Request::getInt('tab_id'),
            'cls'        =>  _Request::getInt('_cls'),
            'goods_sale_price' =>  _Request::getFloat('goods_sale_price',0.00),
            'goods_jiajialv' =>  _Request::getFloat('goods_jiajialv',0.00),
            'goods_type' => _Request::get('goods_type'),
            'min_qty'=> _Post::getInt('min_qty'),
            'pack_qty'=> _Post::getInt('pack_qty'),
            'caizhi'=> _Post::getString('caizhi'),
            'remark'=> _Post::getString('remark'),
        );
		$id = _Post::getInt('id');
		$newmodel =  new MaterialGoodsJinModel($id,22);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
            //'goods_sn'   =>  $args['goods_sn'],
            'goods_name' =>  $args['goods_name'],
            'goods_spec' =>  $args['goods_spec'],
            'catetory1'  =>  $args['catetory1'],
            'catetory2'  =>  $args['catetory2'],
            'catetory3'  =>  $args['catetory3'],
            'cost'       =>  0.00,
            'unit'       =>  $args['unit'],
            'update_user'=>  $_SESSION['userName'],
            'update_time'=>  date('Y-m-d H:i:s'),
            'id'         =>  $id,
            'goods_sale_price' => $args['goods_sale_price'],
            'goods_jiajialv' => $args['goods_jiajialv'],
		    'goods_type' => $args['goods_type'],
		    'min_qty'=> $args['min_qty'],
		    'pack_qty'=>$args['pack_qty'],
		    'caizhi'=> $args['caizhi'],
		    'remark'=> $args['remark'],
		);
	/*	echo '<pre>';
    var_dump($newdo); exit;*/
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $args['cls'];
			$result['tab_id'] = $args['tab_id'];
			$result['title'] = '修改此处为想显示在页签上的字段';
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}	

	public function getGoodsSn($style_sn,$id,$catetory1 = null,$catetory2 = null){
        $goods_sn =$style_sn;
        $len = 6;
        $and = "-";
        if(trim($catetory1)==''){
            $goods_sn .=$and."00";
        }else{
            $goods_sn .=$and.$catetory1;
        }
        if(trim($catetory2)==''){
            $goods_sn .=$and."00";
        }else{
            $goods_sn .=$and.$catetory2;
        }
        //$goods_sn .= $and.str_pad($id,6,'0',STR_PAD_LEFT);
        /*
        $count = $len - count($id);
        switch ($count){
            case 1 : $goods_sn .=$and."0".$id; break;
            case 2 : $goods_sn .=$and."00".$id; break;
            case 3 : $goods_sn .=$and."000".$id; break;
            case 4 : $goods_sn .=$and."0000".$id; break;
            case 5 : $goods_sn .=$and."00000".$id; break;
            default : $goods_sn.$and.$id;
        }*/
        return preg_replace('# #', '',$goods_sn);
    }

	public function getStyleName(){
	    $result = array('success' => "0",'error' => "",'data' => "");
	    $style_sn = $_GET['style_sn'];
	    if(empty($style_sn)){
            Util::jsonExit($result);
        }

        $style_info_model = new BaseStyleInfoModel(12);
        $res = $style_info_model->getStyleInfo('style_name',array('style_sn'=>$style_sn));
        if($res)
            $result['success'] = 1;
        else
        	$result['success'] = 0; 
        $result['data'] = $res;
        Util::jsonExit($result);
    }
    
/**
	 * 批量上架
	 * @param unknown $params
	 */
	public function onShelf($params){
	    $result = array('success'=>0,'error'=>'');
	    $ids = _Request::getList('_ids');
	    if(empty($ids)){
	        $result['error'] = "未选中任何记录";
	        Util::jsonExit($result);
	    }
	    $model = new MaterialGoodsJinModel(22);
	    $res = $model->editGoodsStatus($ids,1);
	    if($res != false){
	        $result['success'] = 1;
	        //修改日志记录
	        //$dataLog['remark'] = "对ID=".implode(',',$ids)."的配件货号进行上架";
	        //$this->operationLog("update",$dataLog);
	    }else{
	        $result['error'] = "操作失败!";
	    }
	    Util::jsonExit($result);
	}
	
	/**
	 * 批量下架
	 * @param unknown $params
	 */
	public function offShelf($params){
	    $result = array('success'=>0,'error'=>'');
	    $ids = _Request::getList('_ids');
	    if(empty($ids)){
	        $result['error'] = "未选中任何记录";
	        Util::jsonExit($result);
	    }
	    $model = new MaterialGoodsJinModel(22);
	    $res = $model->editGoodsStatus($ids,2);
	    if($res != false){
	        $result['success'] = 1;
	        //修改日志记录
	        //$dataLog['remark'] = "对ID=".implode(',',$ids)."的配件货号进行上架";
	        //$this->operationLog("update",$dataLog);
	    }else{
	        $result['error'] = "操作失败!";
	    }
	    Util::jsonExit($result);
	}
}

?>