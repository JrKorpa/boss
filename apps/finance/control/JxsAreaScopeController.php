<?php
/**
 *  -------------------------------------------------
 *   @file		: JxsAreaScopeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-07-17 01:51:49
 *   @update	:
 *  -------------------------------------------------
 */
class JxsAreaScopeController extends CommonController
{
	protected $smartyDebugEnabled = false;

    public function checkJxs($jxsList,$jxs_id)
    {
        if($jxsList)
        {
            foreach($jxsList as $jxs){
                if($jxs['jxs_id'] == $jxs_id){
                    return false;
                }
            }
        }
        return true;
    }

    public function jxsLevel($jxs)
    {
        if(isset($jxs['country_id']) && isset($jxs['province_id']) && isset($jxs['city_id']) && isset($jxs['region_id'])){
            if($jxs['country_id'] == 0){
                return 1;
            }
            if($jxs['province_id'] == 0){
                return 2;
            }
            if($jxs['city_id'] == 0){
                return 3;
            }
            if($jxs['region_id'] == 0){
                return 4;
            }else{
                return 5;
            }
        }else{
            return false;
        }
    }

    public function getShopName($jxs_id){
        $api = new ApiManagementModel();
        $jxs_list = $api->getJxslist();
        foreach($jxs_list as $key => $val){
            if($val['id'] == $jxs_id){
                return $val['shop_name'];
            }
        }
        return '名称未知';
    }

    function checkAreaScope($jxsList,$newjxs,$level){
        $ret = true;
        if($level == 1 || $level == 2){
            if(!empty($jxsList)){
                return array('do'=>false,'jxs_id'=>$jxsList[0]['jxs_id']);
            }else{
                return array('do'=>true);
            }
        }
        if($level == 3){
            foreach($jxsList as $jxs){
                if($jxs['country_id'] == $newjxs['country_id'] && $jxs['province_id'] == $newjxs['province_id']){
                    return array('do'=>false,'jxs_id'=>$jxs['jxs_id']);
                }
            }
        }
        if($level == 4){
            foreach($jxsList as $jxs){
                if($jxs['country_id'] == $newjxs['country_id'] && $jxs['province_id'] == $newjxs['province_id'] && $jxs['city_id'] == $newjxs['city_id']){
                    return array('do'=>false,'jxs_id'=>$jxs['jxs_id']);
                }
            }
        }
        if($level == 5){
            foreach($jxsList as $jxs){
                if($jxs['country_id'] == $newjxs['country_id'] && $jxs['province_id'] == $newjxs['province_id'] && $jxs['city_id'] == $newjxs['city_id'] && $jxs['region_id'] == $newjxs['region_id']){
                    return array('do'=>false,'jxs_id'=>$jxs['jxs_id']);
                }
            }
        }
        return array('do'=>true);
    }

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $api = new ApiManagementModel();
        $jxs_list = $api->getJxslist();
		$this->render('jxs_area_scope_search_form.html',array('bar'=>Auth::getBar(), 'jxs_list'=>$jxs_list));
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
			'jxs_id' => _Request::get("jxs_id"),
            'province_id' => _Request::get("province_id"),

		);
		$page = _Request::getInt("page",1);
		$where = array(
            'jxs_id' => $args['jxs_id'],
            );

        $api = new ApiManagementModel();
        $jxs_list = $api->getJxslist();
        $jxs_list_info = array();
        foreach ($jxs_list as $key => $value) {
            # code...
            $jxs_list_info[$value['id']] = $value['shop_name'];
        }
		$model = new JxsAreaScopeModel(29);
		$data = $model->pageList($where,$page,10,false);
        if(!empty($data['data'])){
            $region_ids=array();
            foreach($data['data'] as $k=>$v){
                $region_ids[]=$v['country_id'];
                $region_ids[]=$v['province_id'];
                $region_ids[]=$v['city_id'];
                $region_ids[]=$v['region_id'];
                $data['data'][$k]['jxs_id'] = $jxs_list_info[$v['jxs_id']];
            }

            $region_ids=array_unique($region_ids);
            $region_ids=implode(',',$region_ids);
            $regionNameList=$model->getRegionOption($region_ids);
            $regionName=array();
            foreach($regionNameList as $k=>$v){
                $regionName[0]='全部';
                $regionName[$v['region_id']]=$v['region_name'];
            }
        }else{
            $data['data']=array();
            $regionName=array();
        }
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'jxs_area_scope_search_page';
		$this->render('jxs_area_scope_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
            'regionName'=>$regionName
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
        $api = new ApiManagementModel();
        $jxs_list = $api->getJxslist();
		$result['content'] = $this->fetch('jxs_area_scope_info.html',array(
			'view'=>new JxsAreaScopeView(new JxsAreaScopeModel(29)), 'jxs_list'=>$jxs_list,'app_scope'=>false
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
        $api = new ApiManagementModel();
        $jxs_list = $api->getJxslist();
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('jxs_area_scope_info.html',array(
			'view'=>new JxsAreaScopeView(new JxsAreaScopeModel($id,29)),
			'tab_id'=>$tab_id,
            'jxs_list'=>$jxs_list,
            'app_scope'=>true
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
		$this->render('jxs_area_scope_show.html',array(
			'view'=>new JxsAreaScopeView(new JxsAreaScopeModel($id,29)),
			'bar'=>Auth::getViewBar()
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
		$olddo = array();
		$newdo = array(
            'jxs_id'=>_Request::getInt('jxs_id'),
            'country_id'=>1,
            'province_id'=>_Request::getInt('mem_province_id'),
            'city_id'=>_Request::getInt('mem_city_id'),
            'region_id'=>_Request::getInt('mem_district_id'),
            'create_time'=>date("Y-m-d H:i:s",time()),
            'create_user'=>$_SESSION['userName']
        );
        if($newdo['jxs_id'] == ''){
            $result['error'] = '请选择体验店！';
            Util::jsonExit($result);
        }
        if($newdo['province_id'] == ''){
            $result['error'] = '请选择管辖区域！';
            Util::jsonExit($result);
        }
		$Jxsnewmodel =  new JxsAreaScopeModel(30);
        $jxsList =  $Jxsnewmodel->getAllJxs();
        $jxs_exists = $this->checkJxs($jxsList,$newdo['jxs_id']);
        if(!$jxs_exists){
            $result['error'] = '该体验店已有此区域，不可重复添加！';
            Util::jsonExit($result);
        }

        $jxs = array();
        $jxs['country_id'] = $newdo['country_id'];
        $jxs['province_id'] = $newdo['province_id'];
        $jxs['city_id'] = $newdo['city_id'];
        $jxs['region_id'] = $newdo['region_id'];

        $level = $this->jxsLevel($jxs);
        if($level === false){
            die("数据不完整");
        }

        $fak = $this->checkAreaScope($jxsList,$jxs,$level);
        if(!$fak['do']){
            $shopName = $this->getShopName($fak['jxs_id']);
            $result['error'] = '该区域已有体验店'.$shopName.'，不可重复添加！';
            Util::jsonExit($result);
        }

		$res = $Jxsnewmodel->saveData($newdo,$olddo);
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
		//echo '<pre>';
		//print_r ($_POST);
		//echo '</pre>';
		//exit; 

		$newmodel =  new JxsAreaScopeModel($id,30);

		$olddo = $newmodel->getDataObject();
		$newdo = array(
            'id'=>$id,
            'jxs_id'=>_Request::getInt('jxs_id'),
            'country_id'=>1,
            'province_id'=>_Request::getInt('mem_province_id'),
            'city_id'=>_Request::getInt('mem_city_id'),
            'region_id'=>_Request::getInt('mem_district_id'),
            'create_time'=>date("Y-m-d H:i:s",time()),
            'create_user'=>$_SESSION['userName']
        );

        if($newdo['jxs_id'] == ''){
            $result['error'] = '请选择体验店！';
            Util::jsonExit($result);
        }
        if($newdo['province_id'] == ''){
            $result['error'] = '请选择管辖区域！';
            Util::jsonExit($result);
        }
		$Jxsnewmodel =  new JxsAreaScopeModel(30);
        $jxsList =  $Jxsnewmodel->getAllJxs();
        foreach($jxsList as $key => $val){
            if($val['jxs_id'] == $newdo['jxs_id']){
                unset($jxsList[$key]);
            }
        }
        $jxs_exists = $this->checkJxs($jxsList,$newdo['jxs_id']);
        if(!$jxs_exists){
            $result['error'] = '该体验店已有此区域，不可重复添加！';
            Util::jsonExit($result);
        }

        $jxs = array();
        $jxs['country_id'] = $newdo['country_id'];
        $jxs['province_id'] = $newdo['province_id'];
        $jxs['city_id'] = $newdo['city_id'];
        $jxs['region_id'] = $newdo['region_id'];

        $level = $this->jxsLevel($jxs);
        if($level === false){
            die("数据不完整");
        }

        $fak = $this->checkAreaScope($jxsList,$jxs,$level);
        if(!$fak['do']){
            $shopName = $this->getShopName($fak['jxs_id']);
            $result['error'] = '该区域已有体验店'.$shopName.'，不可重复添加！';
            Util::jsonExit($result);
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
		$Jxsnewmodel = new JxsAreaScopeModel($id,30);
		$do = $Jxsnewmodel->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$Jxsnewmodel->setValue('is_deleted',1);
		$res = $Jxsnewmodel->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

    //省，市
    public function getCity()
    {
        $a_id = _Post::getInt('province');
        $model = new RegionModel(1);
        $data = $model->getRegion($a_id);
        $this->render('jxs_area_scope1.html', array('data' => $data));
    }
    
    //区
    public function getDistrict()
    {
        $a_id = _Post::getInt('city');
        $model = new RegionModel(1);
        $data = $model->getRegion($a_id);
        $this->render('jxs_area_scope2.html', array('data' => $data));
    }
}

?>