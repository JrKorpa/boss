<?php
/**
 *  -------------------------------------------------
 *   @file		: AppStyleForController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-19 10:41:11
 *   @update	:
 *  -------------------------------------------------
 */
class DiyXiangkouConfigController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array();
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{

		$this->render('diy_xiangkou_config_search_form.html', array(
		    'bar'=>Auth::getBar(),
	         
		    )
		);
	}
	public function getAttrHtmlAjax($params){

	    $style_sn = _Request::getString('style_sn');
	    $styleModel = new BaseStyleInfoModel(11);
	    $model = new DiyXiangkouConfigModel(11);
	    $styleInfo = $styleModel->getStyleBySn($style_sn);
	    if(empty($styleInfo)){
	        $result['success'] = 0;
	        $result['data'] = "款号{$style_sn}不存在";
	        Util::jsonExit($result);
	    }else if($styleInfo['check_status']!=3){
	        $result['success'] = 0;
	        $result['data'] = "款号{$style_sn}当前为 非审核状态";
	        Util::jsonExit($result);
	    }
	    $htmlArr = array(
	        'xiangkou'=>'<option value=""></option>',
	    );
	    $attribute_code_arr = array('xiangkou');	     
	    $styleAttrList = $this->getAttrListBySN($style_sn,$attribute_code_arr);	  
        if(!$styleAttrList){
            $result['success'] = 0;
            $result['data'] = "款号不存在";
            Util::jsonExit($result);
        }	    
	    foreach ($styleAttrList as $key=>$vo){
	        $optionArr = array();            
            foreach ($vo as $v){
                $htmlArr[$key].="<option value=\"{$v}\">{$v}</option>";
            }            
	        
	    }
	    $result['success'] = 1;
	    $result['data'] = $htmlArr;
	    Util::jsonExit($result);
	}
	public function getAttrListBySN($style_sn,$attribute_code_arr){
	    $model = new GoodsAttributeModel(11);//11只读 ，11可写可读
	    $attrList = $model->getAttrListByStyleSn($style_sn,$attribute_code_arr);
	    $attr_list = array();
	    foreach ($attribute_code_arr as $vo){
	        $attr_list[$vo] = array();
	    }
	    foreach($attrList as $key1=>$vo1){
	        $attrValIds = trim($vo1['attribute_value'],',');
	        if($vo1['show_type'] ==1){
	            $_attrVal = explode(',',$attrValIds);
	            $attrVal = array();
	            foreach($_attrVal as $key2=>$vo2){
	               $attrVal[] = $vo2['att_value_name'];
	            }
	        }else{
	            if(empty($attrValIds)){
	                continue;
	            }
	            $attrVal = $model->getAttrValByIds($attrValIds);
	            if(empty($attrVal)){
	                continue;
	            }
	            $attrVal = array_column($attrVal,'att_value_name','att_value_id');
	        }
	        $attribute_code = $vo1['attribute_code'];
	        $attr_list[$attribute_code] = $attrVal;
	    }
	    
	    return $attr_list;
	}
	
	
	public function search ($params)
	{   
	    $args = array(
	        'mod'	=> _Request::get("mod"),
	        'con'	=> substr(__CLASS__, 0, -10),
	        'act'	=> __FUNCTION__,
	        'style_sn'	=> _Request::getString("style_sn"),	 
            'xiangkou_lower_limit' => _Request::getFloat("xiangkou_lower_limit"),
	        'xiangkou_upper_limit' => _Request::getFloat("xiangkou_upper_limit"),
	        'carat_lower_limit' => _Request::getFloat("carat_lower_limit"),
	        'carat_upper_limit' => _Request::getFloat("carat_upper_limit"),
	    );
	    $page = _Request::getInt("page",1);
	    $where = $args;	   
	    
	    $model = new DiyXiangkouConfigModel(11);//11只读 ，11可写可读
	    $data = $model->pageList($where,$page,10,false);
	    $pageData = $data;
	    $pageData['filter'] = $args;
	    $pageData['jsFuncs'] = 'diy_xiangkou_config_search_page';
	    $this->render('diy_xiangkou_config_search_list.html', array(
	        'pa'=>Util::page($pageData),
			'page_list'=>$data                
	     ));
	}
	
	public function add ($params)
	{   
	    $result = array('title'=>"快速定制添加",'content'=>'');
	    $result['content']=$this->fetch('diy_xiangkou_config_info.html',
	        array(
	             'view' => new DiyXiangkouConfigView(new DiyXiangkouConfigModel(11)),
	        )
	    );
	    
	    Util::jsonExit($result);
	}
	/**
	 * 新增 程序处理
	 * @param unknown $params
	 */
	public function insert ($params)
	{
	    $result = array('success'=>0,'error'=>'');

	    $newdo = array(
	        'style_sn'=>_Post::get('style_sn'),
	        'xiangkou'=>_Post::get('xiangkou'),
	        'carat_lower_limit'=>_Post::getFloat('carat_lower_limit'),
	        'carat_upper_limit'=>_Post::getFloat('carat_upper_limit')	        
	    );
	    $checkResult = $this->checkData($newdo);
	    if($checkResult['error']){
	        $result['error'] = $checkResult['error'];
	        Util::jsonExit($result);
	    }
	    $model = new DiyXiangkouConfigModel(11);
        
	    $res = $model->saveData($newdo, array());
	    if($res !==false){
	        $result['success'] = 1;
	        Util::jsonExit($result);
	    }else{
	        $result['error'] = "添加失败";
	        Util::jsonExit($result);
	    }
	    
	}
	//数据验证
    public function checkData($data){
    	    
    	    $result = array('success'=>0,'error'=>'');
    	    if($data['style_sn']==""){
    	        $result['error'] = "款号不能为空";
    	        return $result;
    	    }
    	    if($data['xiangkou']==""){
    	        $result['error'] = "镶口不能为空";
    	        return $result;
    	    }
    	    if($data['carat_lower_limit']==""){
    	        $result['error'] = "石重下限不能为空";
    	        return $result;
    	    }
    	    if($data['carat_upper_limit']==""){
    	        $result['error'] = "石重上限不能为空";
    	        return $result;
    	    }
    	    if($data['carat_lower_limit']>$data['carat_upper_limit']){
    	        $result['error'] = "石重下限不能大于石重上限";
    	        return $result;
    	    }	
    	    $style_sn = $data['style_sn'];
    	    $xiangkou = $data['xiangkou'];
    	    $carat_lower_limit = $data['carat_lower_limit'];
    	    $carat_upper_limit = $data['carat_upper_limit'];
    	    
    	    $model = new DiyXiangkouConfigModel(11);    	    
    	    //$where = "style_sn='{$style_sn}' and xiangkou={$xiangkou} and (carat_lower_limit={$carat_lower_limit} or carat_lower_limit={$carat_upper_limit})";
    	    $where = "style_sn='{$style_sn}' and xiangkou={$xiangkou}";
    	    if(!empty($data['id'])){
    	        $where .=" and id <>{$data['id']}";
    	    }    	    	
    	    $xiangkou_check = $model->getDiyXiangkouConfig("count(*) as c",$where);
    	    if($xiangkou_check['c']>0){
    	        $result['error'] = "镶口重复，同款已经存在相同镶口";
    	        return $result;
    	    }
    	    $where = "style_sn='{$style_sn}' and ((carat_lower_limit<={$carat_upper_limit} and carat_upper_limit>={$carat_upper_limit}) or (carat_lower_limit<={$carat_lower_limit} and carat_upper_limit>={$carat_lower_limit}))";
    	    if(!empty($data['id'])){
    	        $where .=" and id <>{$data['id']}";
    	    }
    	    
    	    $carat_check = $model->getDiyXiangkouConfig("*",$where);
    	    if(!empty($carat_check)){
    	        
    	        $result['error'] = "主石上下限冲突，同款不同镶口的主石范围出现交叉！<br/>冲突款号【{$style_sn}】，镶口【{$carat_check['xiangkou']}】,下限【{$carat_check['carat_lower_limit']}】,上限【{$carat_check['carat_upper_limit']}】";
    	        
    	        return $result;
    	    }
    	    $result['success'] = 1;    
    	    return $result;
	    
	}
	/**
	 * 编辑 渲染页面
	 * @param unknown $params
	 */
	public function edit ($params)
	{
	    $result = array('title'=>"快速定制编辑",'content'=>'');
	    
	    $id = _Post::getInt('id');
	    $model = new DiyXiangkouConfigModel($id,11);
	    $view = new DiyXiangkouConfigView($model);
	    if(!$view->get_id()){
	        $result['content'] = "编辑对象不存在!";
	        Util::jsonExit($result);
	    }
	    $result['content']=$this->fetch('diy_xiangkou_config_info.html',
	        array(
	            'view' => $view,
	        )
	    );	     
	    Util::jsonExit($result);
	}
	/**
	 * 编辑 保存
	 * @param unknown $params
	 */
	public function update ($params)
	{
	    $result = array('success'=>0,'error'=>'');
	    $id = _Post::getInt('id');
	    $model = new DiyXiangkouConfigModel($id,11);
	    $olddo = $model->getDataObject();
	    if(empty($olddo)){
	        $result['error'] = "编辑对象不存在！";
	        Util::jsonExit($result);
	    }
	    $newdo = array(
	        'id'=>$id,
	        'style_sn'=>_Post::get('style_sn'),
	        'xiangkou'=>_Post::get('xiangkou'),
	        'carat_lower_limit'=>_Post::getFloat('carat_lower_limit'),
	        'carat_upper_limit'=>_Post::getFloat('carat_upper_limit')	        
	    );
	    $checkResult = $this->checkData($newdo);
	    if($checkResult['error']){
	        $result['error'] = $checkResult['error'];
	        Util::jsonExit($result);
	    }
	    $res = $model->saveData($newdo, $olddo);
	    if($res !==false){
	       $result['success'] = 1;
	       
	       $dataLog['pkdata'] = array('id'=>$id);
	       $dataLog['newdata'] = $newdo;
	       $dataLog['olddata'] = $olddo;
	       $dataLog['fields']  = $model->getFieldsDefine();
	       $this->operationLog("update",$dataLog);
	    }else{
	       $result['error'] = "修改失败！"; 
	    }
	    Util::jsonExit($result);
	}
	/**
	 * 批量删除
	 * @param unknown $params
	 */
	public function delete ($params)
	{
	    $result = array('success'=>0,'error'=>'');
	    
	    $ids = _Request::getList('_ids');
	    if(empty($ids)){
	        $result['error'] = "未选中任何记录";
	        Util::jsonExit($result);
	    }
	    $model = new DiyXiangkouConfigModel(11);
	    $res = $model->multi_delete($ids);
	    if($res !== false){
	       $result['success'] = 1;
	       
	       $dataLog['remark'] = "对ID=".implode(',',$ids)."的镶口石重配置信息进行删除";
	       $this->operationLog("delete",$dataLog);
	    }else{
	       $result['error'] = "删除失败";
	    }
	    Util::jsonExit($result);
	}

}

?>