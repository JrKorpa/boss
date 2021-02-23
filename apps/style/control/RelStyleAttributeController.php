<?php
/**
 *  -------------------------------------------------
 *   @file		: RelStyleAttributeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 19:34:35
 *   @update	:
 *  -------------------------------------------------
 */
class RelStyleAttributeController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('insert','downloadCSV');
    
    
        public function __construct() {
               parent::__construct();

               //产品线
               $new_product_data= array();
               $productModel = new AppProductTypeModel(11);
               $product_data = $productModel->getCtlList();
               foreach ($product_data as $val){
                   $new_product_data[$val['product_type_id']]=$val['product_type_name'];
               }
               
               //获取分类名称
                $new_cat_data= array();
                $appCatModel = new AppCatTypeModel(11);
                $cat_data = $appCatModel->getCtlList();
                foreach ($cat_data as $val){
                    $new_cat_data[$val['cat_type_id']]=$val['cat_type_name'];
                }
                
                //获取所有的属性
                $new_attr_data = array();
                $attributeModel = new AppAttributeModel(11);
                $attribute_arr = $attributeModel->getCtlList();
                foreach ($attribute_arr as $val){
                    $new_attr_data[$val['attribute_id']] = $val['attribute_name'];
                }
                
                $this->assign('cat_data',$new_cat_data);//数据字典
                $this->assign('attr_data',$new_attr_data);//数据字典
                $this->assign('product_data',$new_product_data);//数据字典
        }
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('rel_style_attribute_search_form.html',array('bar'=>Auth::getBar()));
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
                'cat_type_id' => _Request::getInt('cat_type_id')==1?'':_Request::getInt('cat_type_id'),
                'product_type_id' =>_Request::getInt('product_type_id'),
                'attribute_id' => _Request::getInt('attribute_id'),
                'style_sn' => _Request::getString('style_sn'),
                'style_id' => _Request::getInt('_id'),
            );
            $page = _Request::getInt("page",1);
            $where = array(
                'cat_type_id' => _Request::getInt('cat_type_id')==1?'':_Request::getInt('cat_type_id'),
                'product_type_id' => _Request::getInt('product_type_id')==1?'':_Request::getInt('product_type_id'),
                'attribute_id' => _Request::getInt('attribute_id'),
                'style_sn' => _Request::getString('style_sn'),
                'style_id' => _Request::getInt('_id'),
            );
        
            $model = new RelStyleAttributeModel(11);
            $data = $model->pageList($where,$page,10,false);
            
            //获取所有属性对应的属性值
            $relCatAttributeModel = new RelCatAttributeModel(11);
            $all_attribute = $relCatAttributeModel->getList($where);
            $attribute_data = array();
            foreach ($all_attribute as $val){
                $attribute_id = $val['attribute_id'];
                $attribute_name = $val['attribute_name'];
                //文本框没有对应属性值
                $attribute_data['info'][$attribute_id] = $attribute_name;
                if($val['show_type'] !=1){
                    $attr = $relCatAttributeModel->getAttr($where,$attribute_id);  
                    foreach($attr as $k => $v){
                        $attribute_data[$v['att_value_id']]=$v['att_value_name'];
                    }
                }
            }

            //把属性和属性值拼到查出的数据中
            foreach ($data['data'] as $key=>$val){
                $att_id = $val['attribute_id'];
                $value_id = $val['attribute_value'];
                
                if(!array_key_exists($att_id, $attribute_data['info'])){
                    continue;
                }
                $data['data'][$key]['attribute_name'] = $attribute_data['info'][$att_id];
                //show_type:1文本框，2单选，3多选，4下拉
                if($val['show_type'] == 1){
                    //unset($data['data'][$key]);
                    //continue;
                    $data['data'][$key]['att_value_name'] = $val['attribute_value']; 
                }elseif($val['show_type'] == 2 || $val['show_type'] == 4){
                    if(!array_key_exists($value_id, $attribute_data)){
                        continue;//9
                    }
                    $data['data'][$key]['att_value_name'] = $attribute_data[$value_id];
                }elseif($val['show_type'] == 3  ){
                    //把属性值解析出来
                    $att_value_arr = array_filter(explode(",", $value_id));
                    $num = count($att_value_arr);
                    if($num == 0){
                        $data['data'][$key]['att_value_name'] = '';
                    }else{
                        $arr_attr_value = array();
                        foreach ($att_value_arr as $v_val){
                            if(!array_key_exists($v_val, $attribute_data)){
                                continue;
                            }
                            $arr_attr_value[]= $attribute_data[$v_val];
                        }
                        $data['data'][$key]['att_value_name'] = implode(',',$arr_attr_value);
                    }
                }
            }
            
            $pageData = $data;
            $pageData['filter'] = $args;
            $pageData['jsFuncs'] = 'rel_style_attribute_search_page';
            $this->render('rel_style_attribute_search_list.html',array(
                    'pa'=>Util::page($pageData),
                    'page_list'=>$data,
                    'showType'=>array(1=>'文本框',2=>'单选',3=>'多选',4=>'下拉')
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
        $style_id = _Request::getInt('_id');
		$result = array('success' => 0,'error' => '');
        $product_type_id = _Request::getInt('product_type_id');
        $cat_type_id = _Request::getInt('cat_type_id');
        //$style_id = _Request::getInt('style_id');
        $style_sn = _Request::getString('style_sn');
        
        //-----------new-------------------
        //根据产品线和款式分类，获取所有父级
        //-----------end-------------------
        
        $is_edit = false; //默认是添加
        //修改：获取
        //获取是否已经存在款和属性的数据
        $where = array('product_type_id' => $product_type_id, 'cat_type_id' => $cat_type_id, 'style_id' => $style_id);
        $styleAttributeModel = new RelStyleAttributeModel(11);
        $new_style_data = $styleAttributeModel->getList($where);
       
        $select_style_arr = array();
        if ($new_style_data) {
            $is_edit = true;
            foreach ($new_style_data as $val) {
                $style_attribute_id = $val['attribute_id'];
                $style_attribute_value = $val['attribute_value'];
                if ($val['show_type'] == 3) {
                    if(!empty($style_attribute_value)){
                    $select_style_arr[$style_attribute_id] = "";
                       $select_style_arr[$style_attribute_id] = explode(",", $style_attribute_value);
                        // $select_style_arr[$style_attribute_id] =  $style_attribute_value;
                    }
                } else {
                    $select_style_arr[$style_attribute_id] = $style_attribute_value;
                }
            }
        }
        
        //获取产品线和分类的设置的属性
        $relCatAttributeModel = new RelCatAttributeModel(11);
        $where = array('product_type_id' => $product_type_id, 'cat_type_id' => $cat_type_id);
        $all_cat_attribute_data = $relCatAttributeModel->getAttributeList($where);
        //echo '<pre>';
        //print_r($all_cat_attribute_data);die;
        if(empty($all_cat_attribute_data)){
            $result['content'] = '此款的产品线和分类没有添加属性！';
            Util::jsonExit($result);
        }
        $all_cat_attribute_data_arry = array();
        $i=0;
        foreach ($all_cat_attribute_data as $key => $value) {
            # code...
            $res_tt = array();
            $fa_cto['attribute_id'] = $value['attribute_id'];
            $res_tt = $relCatAttributeModel->getAttributeValuesList($fa_cto);
            if(empty($res_tt)){           	
                $all_cat_attribute_data_arry[$i]['attribute_id'] = $value['attribute_id'];
                $all_cat_attribute_data_arry[$i]['is_show'] = $value['is_show'];
                $all_cat_attribute_data_arry[$i]['is_default'] = $value['is_default'];
                $all_cat_attribute_data_arry[$i]['is_require'] = $value['is_require'];
                $all_cat_attribute_data_arry[$i]['show_type'] = $value['show_type'];
                $all_cat_attribute_data_arry[$i]['attribute_name'] = $value['attribute_name'];
                $all_cat_attribute_data_arry[$i]['att_value_id'] = $res_tt['att_value_id'];
                $all_cat_attribute_data_arry[$i]['att_value_name'] = $res_tt['att_value_name'];
                $i++;
            }else{
                foreach ($res_tt as $k => $v) {
                    # code...
                    $all_cat_attribute_data_arry[$i]['attribute_id'] = $value['attribute_id'];
                    $all_cat_attribute_data_arry[$i]['is_show'] = $value['is_show'];
                    $all_cat_attribute_data_arry[$i]['is_default'] = $value['is_default'];
                    $all_cat_attribute_data_arry[$i]['is_require'] = $value['is_require'];
                    $all_cat_attribute_data_arry[$i]['show_type'] = $value['show_type'];
                    $all_cat_attribute_data_arry[$i]['attribute_name'] = $value['attribute_name'];
                    $all_cat_attribute_data_arry[$i]['att_value_id'] = $v['att_value_id'];
                    $all_cat_attribute_data_arry[$i]['att_value_name'] = $v['att_value_name'];
                    $i++;
                }
            }
        }
    
        $all_cat_attribute_data_arry = array_values($all_cat_attribute_data_arry);
       
        $new_attribute_data = array();
        foreach ($all_cat_attribute_data_arry as $val) {
            $attribute_id = $val['attribute_id'];
            $att_value_id = $val['att_value_id'];
            $att_value_name = $val['att_value_name'];
            $show_type = $val['show_type'];
            $new_attribute_data[$attribute_id]['info']['attribute_id'] = $val['attribute_id'];
            $new_attribute_data[$attribute_id]['info']['attribute_name'] = $val['attribute_name'];
            $new_attribute_data[$attribute_id]['info']['show_type'] = $val['show_type'];
            $new_attribute_data[$attribute_id]['value'][$att_value_id]['att_value_name'] = $att_value_name; 
            switch ($show_type){
                case 1:
                    $new_attribute_data[$attribute_id]['info']['attribute_value'] = '';
                    if ($is_edit) {
                        $new_attribute_data[$attribute_id]['info']['attribute_value'] = $select_style_arr[$attribute_id];
                    }
                    break;
                case 2:
                case 4:
                    $new_attribute_data[$attribute_id]['value'][$att_value_id]['checked'] = '';
                    if ($is_edit) {
                       // var_dump($select_style_arr,$attribute_id);
                       //if ($select_style_arr && array_key_exists($att_value_id, $select_style_arr[$attribute_id])) { 
                       if ($select_style_arr && array_key_exists($attribute_id, $select_style_arr)) { 
                            //1=>2,3,4  1=arary(2,3,4)
                            $new_attribute_data[$attribute_id]['value'][$att_value_id]['checked'] = $select_style_arr[$attribute_id];
                        }else{
                            $new_attribute_data[$attribute_id]['value'][$att_value_id]['checked'] = '';
                        }
                    }
                    break;
                case 3:
                    $new_attribute_data[$attribute_id]['value'][$att_value_id]['checked'] = '';
                    if ($is_edit) {
                        if (isset($select_style_arr[$attribute_id])) {
                            if(in_array($att_value_id, $select_style_arr[$attribute_id])){
                                $new_attribute_data[$attribute_id]['value'][$att_value_id]['checked'] = $att_value_id;
                            }
                            
                        }
                    }
                    break;
            }
            
        }
        

		if($new_attribute_data[$attribute_id]['info']['attribute_name'] == '镶口'){
			$e = $new_attribute_data[$attribute_id]['value'];
			$p = array();
			foreach($e as $key => $val){
				$p[$key] = $val['att_value_name'];
			}
			$d = array_flip($p);
			ksort($d,SORT_NUMERIC);
			$d = array_flip($d);
			foreach($d as $k => $v){
				$newdo[$k] = $e[$k];
			}
			$new_attribute_data[$attribute_id]['value'] = $newdo;
		}
        
        
      
		$result['content'] = $this->fetch('rel_style_attribute_info.html',array(
			//'view'=>new RelStyleAttributeView(new RelStyleAttributeModel(11)),
            'style_id' => $style_id,
            '_id' => _Post::getInt('_id'),
            'new_attribute_data' => $new_attribute_data,
            'product_type_id' => $product_type_id,
            'cat_type_id' => $cat_type_id,
            'style_id' => $style_id,
            'style_sn' => $style_sn,
            'title'=>'添加',
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
		$tab_id = intval($params["tab_id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('rel_style_attribute_info.html',array(
			'view'=>new RelStyleAttributeView(new RelStyleAttributeModel($id,11)),
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
		die('开发中');
		$id = intval($params["id"]);
		$this->render('rel_style_attribute_show.html',array(
			'view'=>new RelStyleAttributeView(new RelStyleAttributeModel($id,11)),
			'bar'=>Auth::getViewBar($id)
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
        $product_type_id = _Request::getInt('product_type_id');
        $cat_type_id = _Request::getInt('cat_type_id');
        $style_id = _Request::getInt('style_id');
        $style_sn = _Request::getString('style_sn');
        
        
        $result = array('success' => 0,'error' =>'');
        $other_args_arr = array('mod','con','act','style_sn','cat_type_id','product_type_id','style_id','_cls','tab_id');
        //所有的属性
        $all_attr_arr = array();
        foreach ($_REQUEST as $key=>$val){
            if(in_array($key, $other_args_arr)){
                continue;
            }
            $all_attr_arr[$key] = $val;
        }
        if(empty($all_attr_arr)){
            $result['error'] = '请填写属性';
            Util::jsonExit($result);
        }
        
        //判断类型必填属性
        $relCatAttributeModel = new RelCatAttributeModel(11);
        $where_style = array('product_type_id' => $product_type_id, 'cat_type_id' => $cat_type_id);
        $all_cat_attribute_data = $relCatAttributeModel->getList($where_style);
        
        $require_arr = array();
        $cat_attribute_list = array();
        foreach ($all_cat_attribute_data as $val){
            $a_id = $val['attribute_id'];
            $attribute_name = $val['attribute_name'];
            $is_require = $val['is_require'];
            if($is_require==1){
                $require_arr[$a_id]['attribute_name'] = $attribute_name;
            }
            $cat_attribute_list[$a_id] = $a_id;
        }
        
        //处理接受的属性和属性值
        $hand_data = $this->handleAttrbuteAndValue($all_attr_arr);
        //合并后的属性和属性值
        $all_str_data = $hand_data['data'];
        $is_error = true;
        $error = "";
        
        foreach($require_arr as $key => $val){
            if(!array_key_exists($key, $all_str_data) || $all_str_data[$key]==""){
                $is_error = false;
                $error .= $val['attribute_name'].",是必填项\n";
            }
        }
        
        if (!$is_error){
            $result['error'] = $error;
            Util::jsonExit($result);
        }
        //各个属性对应的显示类型
        $all_show_type = $hand_data['show_type'];
        
        //判断数据是否存在
        $styleAttributeModel = new RelStyleAttributeModel(12);
        
        foreach ($all_str_data as $key => $val) {
            if (!$key) {
                continue;
            }
            
            $where = array('style_id' => $style_id, 'attribute_id' => $key);
            //查看此款的此属性是否已经存在
            $style_data = $styleAttributeModel->getList($where);
            $olddo= array();
            if ($style_data) {
                $olddo = $style_data[0];
                $newdo = array(
                    'rel_id'=>$olddo['rel_id'],
                    'attribute_id' => $key,
                    'attribute_value' => $val,
                    'create_time' => date("Y-m-d H:i:s"),
                    'create_user' => $_SESSION['userName'],
                    'show_type' => $all_show_type[$key],
                    'product_type_id' => $product_type_id,
                    'style_sn' => $style_sn,
                    'cat_type_id' => $cat_type_id,
                );

                $styleAttributeModel = new  RelStyleAttributeModel($olddo['rel_id'],12);
                $res = $styleAttributeModel->saveData($newdo, $olddo);
                if($key=='5' && $newdo['attribute_value']<>$olddo['attribute_value']){
                    $styleAttributeModel->deleteXiangkou($style_sn,$olddo['attribute_value']);
                }
            } else {
                $newdo = array(
                    'style_id' => $style_id,
                    'attribute_id' => $key,
                    'attribute_value' => $val,
                    'product_type_id' => $product_type_id,
                    'style_sn' => $style_sn,
                    'cat_type_id' => $cat_type_id,
                    'create_time' => date("Y-m-d H:i:s"),
                    'create_user' => $_SESSION['userName'],
                    'show_type' => $all_show_type[$key],
                );
            }
            
            $res = $styleAttributeModel->saveData($newdo, $olddo,true);
        }
        
        //判断本次的属性哪些没有则，删掉
        foreach ($cat_attribute_list as $val){
            if(!array_key_exists($val, $all_str_data)){
                $where = array('style_id' => $style_id, 'attribute_id' => $val);
                //查看此款的此属性是否已经存在
                $style_data = $styleAttributeModel->getList($where);
                if(!empty($style_data)){ 
                    $rel_id = $style_data[0]['rel_id'];
                    $styleAttributeModel->deleteStyleAttribute($rel_id);
                }
            }
        }
        if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = _Request::get('_cls');
			$result['tab_id'] =  _Request::get('tab_id');;	
			$result['title'] = '操作成功';
			
			//AsyncDelegate::dispatch('opslog', array('event' => 'style_attr_changed', 'style_id' => $style_id, 'style_sn' => $style_sn));
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
		$model = new BaseStyleInfoModel($id, 12);
        $model->addBaseStyleLog(array('style_id'=>$style_id ,'remark'=>'属性添加成功'));
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new RelStyleAttributeModel($id,12);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
        
       
        /*
         * 把所有的属性和值处理
         */
        function  handleAttrbuteAndValue($all_data){
            //show_type:1文本框,2单选,3多选,4下拉列表
            //文本框：分开：展示方式-属性id=属性值id
            //其他：分开：展示方式-属性id_属性值id=属性值id
            
            /*array(3) { ["1-3"]=> string(2) "88" 
                         ["3-2_10"]=> string(2) "10" 
                        ["3-2_14"]=> string(2) "14" } 
             */
            $attribute_list_arr = array();
            foreach ($all_data as $key=>$val) {
            
                $all_attr_arr = explode("-", $key);
                //展示方式
                $show_type = $all_attr_arr[0];
                //属性值
                $attr_value = $val;
                //文本框没有对应的属性
                switch ($show_type){
                    case 1:
                        //文本框
                        $attr_id = $all_attr_arr[1];
                        $all_str_data[$attr_id] = $attr_value;
                        $all_show_type[$attr_id] = $show_type;
                        break;
                    case 2:
                        //2单选
                        $other_attr_str = $all_attr_arr[1];
                        $other_attr_arr = explode("_", $other_attr_str);
                        $attr_id = $other_attr_arr[0];
                        
                        $all_str_data[$attr_id] = $attr_value;
                        $all_show_type[$attr_id] = $show_type;
                        break;
                    case 3:
                        //3多选
                        // ["3-2_10"]=> string(2) "10" ["3-2_14"]=> string(2) "14" 
                        $other_attr_str = $all_attr_arr[1];
                        $other_attr_arr = explode("_", $other_attr_str);
                        $attr_id = $other_attr_arr[0];
                        
                        if (in_array($attr_id, $attribute_list_arr)) {
                            $all_str_data[$attr_id].= $attr_value . ',';
                        } else {
                            $attribute_list_arr[] = $attr_id;
                            $all_str_data[$attr_id] = $attr_value . ',';
                        }
                        
                        $all_show_type[$attr_id] = $show_type;
                        break;
                    case 4:
                        //下拉列表                        
                        $other_attr_str = $all_attr_arr[1];
                        $other_attr_arr = explode("_", $other_attr_str);
                        $attr_id = $other_attr_arr[0];
                        
                        $all_str_data[$attr_id] = $attr_value;
                        $all_show_type[$attr_id] = $show_type;
                        break;
                }
            }
            return array('data'=>$all_str_data,'show_type'=>$all_show_type);
        }
        
    public function downloadCSV(){
        $data = array("款号","金重");
        $fileName = "款式金重属性导入模板.csv";
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $fileName);
        //1.输出字段标题
        $str = "";
        foreach ($data as $v){
            $v = @iconv("UTF-8","GBK",$v);
            $str .= $v.",";
        }
        $str = trim($str,",")."\r\n";
        echo $str;
    }    
    public function importIndex(){
        $result = array("title"=>"款式属性导入-金重导入");
        $result['content'] = $this->fetch("rel_style_attribute_import.html");        
        Util::jsonExit($result);
    }
    public function importCSV($params){
        $result = array("error"=>"","success"=>"");
        if(empty($_FILES['file']['tmp_name'])){
            $result['error'] = "请上传文件";
            Util::jsonExit($result);
        }else if(Upload::getExt($_FILES['file']['name']) != 'csv'){
            $result['error'] = "请上传csv格式的文件";
            Util::jsonExit($result);
        }
        $tmp_name = $_FILES['file']['tmp_name'];
        $file = fopen($tmp_name, 'r');
        $datalist = array();        
        $i = 0;
        $relCatAttrModel = new RelCatAttributeModel(11);//52只读，11可读可写
        $baseStyleInfoModel = new BaseStyleInfoModel(11);//52只读，11可读可写
        $datalist = array();
        while ($datav = fgetcsv($file)) {
            $i++;
            foreach ($datav as $k=>$v){
                $datav[$k] = @iconv("GBK","UTF-8",$v);
            }            
            if($i >= 2){
                //过滤空行 begin
                $is_empty_line = true;
                foreach ($datav as $k=>$v){
                    if(trim($v)!=""){
                        $is_empty_line = false;
                    }
                }
                if($is_empty_line){
                    continue;
                }//过滤空行 end
                
                $style_sn   = $datav[0];
                $attribute_value     = $datav[1];
                $where = array("style_sn"=>$style_sn);
                $baseStyleInfo = $baseStyleInfoModel->getStyleByStyle_sn($where);
                if(empty($baseStyleInfo[0])){
                    $result['error'] = "第{$i}行，款号{$style_sn}不存在！";
                    Util::jsonExit($result);
                }
                $baseStyleInfo = $baseStyleInfo[0];
                $style_id     = $baseStyleInfo['style_id'];
                $product_type = $baseStyleInfo['product_type'];//产品线
                $style_type   = $baseStyleInfo['style_type']; //款式分类 
                $attribute_id = 77;//金重属性ID
                $where = array('cat_type_id'=>$style_type,'product_type_id'=>$product_type,"attribute_id"=>$attribute_id);
                $relCatAttribute = $relCatAttrModel->getCatAttrInfo($where); 
                if(empty($relCatAttribute[0])) {
                    $result['error'] = "第{$i}行，款号{$style_sn}没有金重属性，请添加！";
                    Util::jsonExit($result);
                }
                $relCatAttribute = $relCatAttribute[0];
                $datalist[] = array(
                    'style_sn' =>$style_sn,
                    'attribute_id'=>$attribute_id,
                    'product_type_id'=>$product_type,
                    'show_type' =>$relCatAttribute["show_type"],//显示方式
                    'cat_type_id'=>$style_type,
                    'attribute_value' =>$attribute_value,
                    'style_id' =>$style_id,
                    'create_user'=>"admin",
                    'create_time'=>date("Y-m-d H:i:s")                    
                );                
            }            
        }
        fclose($file);

        $model = new RelStyleAttributeModel(11);//11可写可读        
        $pdo = $model->db()->db();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
        $pdo->beginTransaction();//开启事务
        $i = 0;
        try{   
            foreach ($datalist as $data){   
                $i++;
                $sql = "select rel_id from rel_style_attribute where style_sn='{$data['style_sn']}' and attribute_id={$data['attribute_id']}";
                $rel_id = $model->db()->getOne($sql);                
                if($rel_id){
                     $model->update($data,"rel_id={$rel_id}");
                }else{
                     $sql = $model->insertSql($data,"rel_style_attribute");
                     $pdo->query($sql);
                }
            }  
            $pdo->commit();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1); 
            $result['success'] = 1;  
            Util::jsonExit($result);
        }catch(PDOException $e){
            $error = "第{$i}行保存时异常,error:".$e->getMessage();
            Util::rollbackExit($error,array($pdo));
        } 
    }
}

?>