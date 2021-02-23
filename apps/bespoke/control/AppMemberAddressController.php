<?php
/**
 *  -------------------------------------------------
 *   @file		: AppMemberAddressController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 16:01:08
 *   @update	:
 *  -------------------------------------------------
 */
class AppMemberAddressController extends CommonController
{
	protected $smartyDebugEnabled = true;

        /**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('app_member_address','front',17);	//生成模型后请注释该行
		//Util::V('app_member_address',17 );	//生成视图后请注释该行
                $id = _Get::getInt('id');
		$this->render('app_member_address_search_form.html',array('view'=>new AppMemberAddressView(new AppMemberAddressModel(17)),'member_id'=>$id,'bar'=>Auth::getBar()));
		//$this->render('app_member_address_search_form.html',array('bar'=>Auth::getBar()));
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
                        'member_id' => _Request::getInt('_id')
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array(
                    'member_id'=>$args['member_id']
                );
	
		$model = new AppMemberAddressModel(17);
		$data = $model->pageList($where,$page,10,false);
        if(!empty($data['data'])){
            $region_ids=array();
            foreach($data['data'] as $k=>$v){
                $region_ids[]=$v['mem_country_id'];
                $region_ids[]=$v['mem_province_id'];
                $region_ids[]=$v['mem_city_id'];
                $region_ids[]=$v['mem_district_id'];
            }

        $region_ids=array_unique($region_ids);
        $region_ids=implode(',',$region_ids);
        $regionNameList=$model->getRegionOption($region_ids);
        $regionName=array();
        foreach($regionNameList as $k=>$v){
            $regionName[$v['region_id']]=$v['region_name'];
        }
        }else{
            $data['data']=array();
            $regionName=array();
        }
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_member_address_search_page';
		$this->render('app_member_address_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'regionName'=>$regionName,
		));
	}
    
    //省，市
    public function getCity()
    {
        $a_id = _Post::getInt('province');
        $model = new RegionModel(1);
        $data = $model->getRegion($a_id);
        $this->render('area_info_options1.html', array('data' => $data));
    }
    
    //区
    public function getDistrict()
    {
        $a_id = _Post::getInt('city');
        $model = new RegionModel(1);
        $data = $model->getRegion($a_id);
        $this->render('area_info_options2.html', array('data' => $data));
    }

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$member_id = _Request::getInt('member_id');
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_member_address_info.html',array(
			'view'=>new AppMemberAddressView(new AppMemberAddressModel(17)),'member_id'=>$member_id,'app_address'=>false
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
		$member_id = _Request::getInt('member_id');
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_member_address_info.html',array(
			'view'=>new AppMemberAddressView(new AppMemberAddressModel($id,17)),'member_id'=>$member_id,'app_address'=>true
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
		$this->render('app_member_address_show.html',array(
			'view'=>new AppMemberAddressView(new AppMemberAddressModel($id,17))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$olddo = array();
		$newdo=array(
				'customer'=>_Request::getString('customer'),
				'mobile'=>_Request::getString('mobile'),
				'member_id'=>_Request::getInt('member_id'),
				'mem_country_id'=>_Request::getInt('mem_country_id'),
				'mem_province_id'=>_Request::getInt('mem_province_id'),
				'mem_city_id'=>_Request::getInt('mem_city_id'),
				'mem_district_id'=>_Request::getInt('mem_district_id'),
				'mem_address'=>_Request::getString('mem_address'),
				'mem_is_def'=>_Request::getInt('mem_is_def'),
		);
        
		$newmodel =  new AppMemberAddressModel(18);
        if($newdo['mem_is_def']==1){
            $row=$newmodel->getRow($newdo['member_id']);
            if($row){
                $newmodel->updateMemberAddress($newdo['member_id']);
            }
        }

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
		$id = _Request::getInt('id');

		$newmodel =  new AppMemberAddressModel($id,18);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
				'mem_address_id'=>$id,
                'member_id'=>_Request::getInt('member_id'),
				'customer'=>_Request::getString('customer'),
				'mobile'=>_Request::getString('mobile'),
				'mem_country_id'=>_Request::getInt('mem_country_id'),
				'mem_province_id'=>_Request::getInt('mem_province_id'),
				'mem_city_id'=>_Request::getInt('mem_city_id'),
				'mem_district_id'=>_Request::getInt('mem_district_id'),
				'mem_address'=>_Request::getString('mem_address'),
				'mem_is_def'=>_Request::getInt('mem_is_def')
		);

        if($newdo['mem_is_def']==1){
            $newmodel->updateMemberAddress($newdo['member_id']);
        }

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
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
		$model = new AppMemberAddressModel($id,18);
		$do = $model->getDataObject();
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