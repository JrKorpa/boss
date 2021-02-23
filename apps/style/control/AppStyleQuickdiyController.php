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
class AppStyleQuickdiyController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('download_tpl','getAttrHtmlAjax');
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
	    //$goodsAttrModel = new GoodsAttributeModel(11);
	    //$caizhiArr = $goodsAttrModel->getCaizhiList(false);
	    //$caizhiyanseArr = $goodsAttrModel->getJinseList(false);
		$this->render('app_style_quickdiy_search_form.html', array(
		    'bar'=>Auth::getBar(),
	         
		    )
		);
	}
	public function getAttrHtmlAjax($params){
	    $htmlArr = array(
	        'caizhi'=>'<option value=""></option>',
	        'caizhiyanse'=>'<option value=""></option>',	        
	        'xiangkou'=>'<option value=""></option>',
	        'style_name'=>'',
	        'zhiquan'=>''
	    );
	    $style_sn = _Request::getString('style_sn');
	    $styleModel = new BaseStyleInfoModel(11);
	    $model = new AppStyleQuickdiyModel(11);
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
	    $htmlArr['style_name'] = $styleInfo['style_name'];
	    $attribute_code_arr = array('caizhi','caizhiyanse','xiangkou',"zhiquan");	     
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
	        'caizhi'	=> _Request::getString("caizhi"),
	        'caizhiyanse'	=> _Request::getString("caizhiyanse"),
 	        'xiangkou_min'=>_Request::getString("xiangkou_min"),
	        'xiangkou_max'=>_Request::getString("xiangkou_max"),
	        'zhiquan_min'=>_Request::getString("zhiquan_min"),
	        'zhiquan_max'=>_Request::getString("zhiquan_max"),
	        'create_time_min'=>_Request::getString("create_time_min"),
	        'create_time_max'=>_Request::getString("create_time_max"),
	        'create_user'=>_Request::getString("create_user"),
	        'status'=>_Request::getString("status"),
	    );
	    $page = _Request::getInt("page",1);
	    $where = $args;	   
	    
	    $model = new AppStyleQuickdiyModel(11);//11只读 ，11可写可读
	    $data = $model->pageList($where,$page,10,false);
	    $pageData = $data;
	    $pageData['filter'] = $args;
	    $pageData['jsFuncs'] = 'app_style_quickdiy_search_page';
	    $this->render('app_style_quickdiy_search_list.html', array(
	        'pa'=>Util::page($pageData),
			'page_list'=>$data                
	     ));
	}
	
	public function add ($params)
	{   
	    $result = array('title'=>"快速定制添加",'content'=>'');
	    $result['content']=$this->fetch('app_style_quickdiy_info.html',
	        array(
	             'view' => new AppStyleQuickdiyView(new AppStyleQuickdiyModel(11)),
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
	        'goods_sn'=>'',
	        'style_sn'=>_Post::getString('style_sn'),
	        'style_name'=>_Post::getString('style_name'),
	        'caizhi'=>_Post::getString('caizhi'),
	        'caizhiyanse'=>_Post::getString('caizhiyanse'),
	        'xiangkou'=>_Post::get('xiangkou'),
	        'zhiquan'=>_Post::get('zhiquan'),
	        'status'=>_Post::getInt('status'),
	        'create_user'=>$_SESSION['userName'],
	        'create_time'=>date('Y-m-d H:i:s')
	    );
	    if($newdo['style_sn']==""){
	        $result['error'] = "款号不能为空";
	        Util::jsonExit($result);
	    }
	    if($newdo['caizhi']==""){
	        $result['error'] = "材质不能为空";
	        Util::jsonExit($result);
	    }
	    if($newdo['caizhiyanse']==""){
	        $result['error'] = "材质颜色不能为空";
	        Util::jsonExit($result);
	    }
	    if($newdo['xiangkou']==""){
	        $result['error'] = "镶口不能为空";
	        Util::jsonExit($result);
	    }
	    if($newdo['zhiquan']==""){
	        $result['error'] = "指圈不能为空".$newdo['zhiquan'];
	        Util::jsonExit($result);
	    } 
	    $model = new AppStyleQuickdiyModel(11);
	    $goods_sn_res = $this->createGoodsSN($newdo); 
	    if($goods_sn_res['success']==false){
	        $result['error'] = $goods_sn_res['data'];
	        Util::jsonExit($result);
	    }else{
	        $newdo['goods_sn'] = $goods_sn_res['data'];
	        $count = $model->getAppStyleQuickdiy("count(*) as c","goods_sn='{$newdo['goods_sn']}'");
	        if($count['c']>0){
	            $result['error'] = "快速定制码{$newdo['goods_sn']}已存在";
	            Util::jsonExit($result);
	        }
	    }	   
	    $res = $model->insert($newdo);

	    if($res['success'] ===true){
	        $result['success'] = 1;
	        Util::jsonExit($result);
	    }else{
	        $result['error'] = $res['msg'];
	        Util::jsonExit($result);
	    }
	    
	}
	/**
	 * 创建快速定制编码
	 * @param unknown $data
	 * @return multitype:boolean string
	 */
	public function createGoodsSN($data){
	    
	    $result = array('success'=>true,'msg'=>"");
	    //$caizhi_arr =  AppStyleQuickdiyModel::$caizhi_arr;;
	    //$caizhiyanse_arr =  AppStyleQuickdiyModel::$caizhiyanse_arr;
	    $caizhi_arr = $this->dd->getEnumArray('style.caizhi');
	    $caizhi_arr = array_column($caizhi_arr,'note','label');
	    $caizhiyanse_arr = $this->dd->getEnumArray('style.color');
	    $caizhiyanse_arr = array_column($caizhiyanse_arr,'note','label');
	    
	    if(empty($data['style_sn'])){
	        $result['success'] = false;
	        $result['data'] = "创建快速定制码时，款号不能为空";
	        return $result;
	    }
	    if(empty($data['caizhi'])){
	        $result['success'] = false;
	        $result['data'] = "创建快速定制码时，材质不能为空";
	        return $result;
	    }else if(empty($caizhi_arr[$data['caizhi']])){
	        $result['success'] = false;
	        $result['data'] = "创建快速定制码时，材质{$data['caizhi']}转换英文编码失败，请在字典【style.caizhi】维护转换码";
	        return $result;
	    }
	    if(empty($data['caizhiyanse'])){
	        $result['success'] = false;
	        $result['data'] = "创建快速定制码时，材质颜色不能为空";
	        return $result;
	    }else if(empty($caizhiyanse_arr[$data['caizhiyanse']])){
	        $result['success'] = false;
	        $result['data'] = "创建快速定制码时，材质颜色【{$data['caizhiyanse']}】转换英文编码失败，请在字典【style.color】维护转换码";
	        return $result;
	    }
	    
	    if($data['xiangkou']==""){
	        $result['success'] = false;
	        $result['data'] = "创建快速定制码时，镶口不能为空";
	        return $result;
	    } 
	    if($data['zhiquan']==""){
	        $result['success'] = false;
	        $result['data'] = "创建快速定制码时，指圈不能为空";
	        return $result;
	    } 
	    
	    $attribute_code_arr = array('caizhi','caizhiyanse','xiangkou',"zhiquan");
	    $styleAttrList = $this->getAttrListBySN($data['style_sn'],$attribute_code_arr);
	    foreach ($styleAttrList as $attCode =>$attValArr){
	        //材质是否在款号的材质范围内
	        if($attCode=="caizhi"){
	            if(!in_array($data['caizhi'],$attValArr)){
	                $exp_str = implode(',',$attValArr);
    	            $result['success'] = false;
    	            $result['data'] = "材质【{$data['caizhi']}】不在款号【{$data['style_sn']}】的材质范围内!<br/>参考材质范围：【".trim($exp_str,",")."】";
    	            return $result;
	            }
	        }	        

	        //材质颜色是否在款号的材质颜色范围内
	        if($attCode=="caizhiyanse"){
	            if(!in_array($data['caizhiyanse'],$attValArr)){
	                $exp_str = implode(',',$attValArr);
    	            $result['success'] = false;
    	            $result['data'] = "材质颜色【{$data['caizhiyanse']}】不在款号【{$data['style_sn']}】的材质颜色范围内!<br/>参考材质颜色范围：【".trim($exp_str,",")."】";
    	            return $result;
	            }            
	            
	        }
	        //镶口是否在款号的镶口范围内
	        if($attCode=="xiangkou"){
	            $flag = false;
	            $exp_str="";
	            foreach ($attValArr as $kk=>$vv){
	                if($data['xiangkou'] == $vv){
	                    $flag = true;	                   
	                }
	                $exp_str .=$vv.",";
	            }	
	            if($flag === false){
	                $result['success'] = false;
	                $result['data'] = "镶口【{$data['xiangkou']}】不在款号【{$data['style_sn']}】的镶口范围内!<br/>参考镶口范围：【".trim($exp_str,",")."】";
	                return $result;
	            }            
	        }
	        if($attCode=="zhiquan"){
	            $flag = false;
	            $exp_str="0";
	            if(empty($attValArr) && $data['zhiquan']==0){
	                $flag = true;
	            }else{	                
    	            foreach ($attValArr as $kk=>$vv){
    	                if($vv=="活口"){
    	                    $vv = "0";
    	                }
    	                $arr = explode("-",$vv);
    	                if(count($arr)==1) {
    	                    $arr[1] = $arr[0];
    	                }
    	                if($data['zhiquan']>=(float)$arr[0] && $data['zhiquan']<=(float)$arr[1]){
    	                    $flag = true;	                   
    	                }
    	                $exp_str .=$vv.",";
    	            }
	            }
	            if($flag === false){
	                $result['success'] = false;
	                $result['data'] = "指圈【{$data['zhiquan']}】不在款号【{$data['style_sn']}】的指圈范围内!<br/>参考指圈范围：【".trim($exp_str,",")."】";
	                return $result;
	            }
	        }	        
	         
	    }
	    
	    $style_sn = $data['style_sn'];
	    $caizhi = $caizhi_arr[$data['caizhi']];
	    $caizhiyanse = $caizhiyanse_arr[$data['caizhiyanse']];
	    $xiangkou = $data['xiangkou']*100;
	    $zhiquan = $data['zhiquan']*1;	    
	    $goods_sn = $style_sn.'-'.$caizhi.'-'.$caizhiyanse.'-'.$xiangkou.'-'.$zhiquan;
	    
	    $result['success'] = true;
	    $result['data'] = $goods_sn;
	    return $result;
	}
    /**
     * 批量启用
     * @param unknown $params
     */
	public function set_enable(){
	    
	    $result = array('success'=>0,'error'=>'');	    
	    $ids = _Request::getList('_ids');
	    if(empty($ids)){
	        $result['error'] = "未选中任何记录";
	        Util::jsonExit($result);
	    }
	    $model = new AppStyleQuickdiyModel(11);
	    $res = $model->setStatus(1,$ids);
	    if($res['success']===true){
	        $result['success'] = 1;
	        
	        //修改日志记录
	        $dataLog['remark'] = "对ID=".implode(',',$ids)."的快速定制编码进行启用";
	        $this->operationLog("update",$dataLog);
	    }else{
	        $result['error'] = $res['msg'];
	    }
	    Util::jsonExit($result);
	}
	/**
	 * 批量禁用
	 * @param unknown $params
	 */
	public function set_unable ($params)
	{
	    $result = array('success'=>0,'error'=>'');
	    
	    $ids = _Request::getList('_ids');
	    if(empty($ids)){
	        $result['error'] = "未选中任何记录";
	        Util::jsonExit($result);
	    }
	    $model = new AppStyleQuickdiyModel(11);
	    $res = $model->setStatus(0,$ids);
	    if($res['success']===true){
	        $result['success'] = 1;
	        //修改日志记录
	        $dataLog['remark'] = "对ID=".implode(',',$ids)."的快速定制编码进行禁用";
	        $this->operationLog("update",$dataLog);
	    }else{
	        $result['error'] = $res['msg'];
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
	    $model = new AppStyleQuickdiyModel(11);
	    $res = $model->multi_delete($ids);
	    if($res['success'] === true){
	       $result['success'] = 1;
	       
	       $dataLog['remark'] = "对ID=".implode(',',$ids)."的快速定制编码进行删除";
	       $this->operationLog("delete",$dataLog);
	    }else{
	       $result['error'] = $res['msg'];
	    }
	    Util::jsonExit($result);
	}
	/**
	 * 按款定制 导入渲染页面
	 * @param unknown $params
	 */
	public function import ($params)
	{
	    $result = array('title'=>'批量上传快速定制','content'=>'');
	    $result['content'] = $this->fetch('app_style_quickdiy_import.html',
	        array(
	             
	        )
	    );
	    Util::jsonExit($result);
	}
	/**
	 * 按款定制 导入
	 * @param unknown $params
	 */
	public function import_file($params)
	{
	    $result = array('success'=>0,'error' =>'');
	    if(empty($_FILES['quickdiy_file']['tmp_name'])){
	        $result['error'] = "未上传文件";
	        Util::jsonExit($result);
	    }
	    $tmp_file = $_FILES['quickdiy_file']['tmp_name'];
	    $file = fopen($tmp_file,'r');
	    $rowIndex = 0;
	    $styleInfoList = array();

	    $styleModel = new BaseStyleInfoModel(11);
	    $model = new AppStyleQuickdiyModel(11);
	    $pdo = $model->db()->db();
	    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
	    $pdo->beginTransaction();//开启事务
	    
	    //检查款号是否合法
	    while($row = fgetcsv($file)){
	        $rowIndex++;
	        if($rowIndex==1 || $row[0]==""){
	            continue;
	        }
	        foreach ($row as $k=>$v){
	            $row[$k] = iconv("GB2312", "UTF-8", $v);
	        }
	        $style_sn = $row[0];
	        if(isset($styleInfoList[$style_sn])){
	            $styleInfo = $styleInfoList[$style_sn];
	        }else{
	            $styleInfo = $styleModel->getStyleBySn($style_sn);
	            if(empty($styleInfo)){
	                $error = "款号{$style_sn}不存在";
	                Util::rollbackExit($error,array($pdo));
	            }else if($styleInfo['check_status']!=3){
	                $error = "款号{$style_sn}当前为 非审核状态";
	                Util::rollbackExit($error,array($pdo));
	            }
	            $styleInfoList[$style_sn] = $styleInfo;
	            if(count($styleInfoList)>1000){
	                unset($styleInfoList);
	            }
	        }    	    
	        
	        $newdo = array(
	            'goods_sn'=>'',
	            'style_name'=>$styleInfo['style_name'],
	            'style_sn'=>$row[0],
	            'xiangkou'=>$row[1],
	            'caizhi'=>$row[2],
	            'caizhiyanse'=>$row[3],
	            'zhiquan'=>$row[4],
	            'status'=>1,
	            'create_user'=>$_SESSION['userName'],
	            'create_time'=>date('Y-m-d H:i:s'),
	        );
            $goods_sn_arr = $this->createGoodsSN($newdo);
            if($goods_sn_arr['success']===true){
                $newdo['goods_sn'] = $goods_sn_arr['data'];
            }else{
                $error = "第".($rowIndex+1)."行,".$goods_sn_arr['data'];
                Util::rollbackExit($error,array($pdo));
            }  
            //检查是否已经添加过
            $quicdiyExists = $model->getAppStyleQuickdiy("count(*) as c","goods_sn='{$newdo['goods_sn']}'");
            if($quicdiyExists['c']>0){
                continue;
            }
	        try{
	            $tip = "快速定制编码保存";
                $sql = $model->insertSql($newdo);
                $model->db()->query($sql);
                
                $tip = "同步款式商品快速定制状态";
                $sql = "update list_style_goods set is_quick_diy=1 where goods_sn='{$newdo['goods_sn']}'";
                $pdo->query($sql);              
                
	        }catch (Exception $e){        
	            $error = "第".($rowIndex+1)."行,{$tip}失败,sql:{$sql}";
	            Util::rollbackExit($error,array($pdo));
	        }
	        
	    }
	    $pdo->commit();//事务提交
	    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	    $result['success'] = 1;
	    Util::jsonExit($result);	   
	}
	/**
	 * 下载模板
	 * @param unknown $params
	 */
	public function download_tpl ($params)
	{
	   
	   header('Content-Type: application/vnd.ms-excel');
	   header("Content-Disposition: attachment;filename=按款快速定制导入模板.csv");
	   header('Cache-Control: max-age=0');
	   
	   $title = array('款号','镶口','材质','材质颜色','指圈');
	   foreach ($title as $k => $v) {
	       $title[$k]=iconv('utf-8', 'GB2312', $v);
	   }
	   echo "\"".implode("\",\"",$title)."\"\r\n";
	}
	
	/**
	 * 获取指定款号的属性select下来列表
	 * @param unknown $params
	 */
	public function getStyleAttrHtmlForOrder($params){
	    //zuanshijingdu
	    $attribute_code_arr = array(
	        'yanse',/*钻石颜色*/	        
	         'zuanshijingdu'/*钻石净度*/	        
	    );
	    $htmlArr = array(
	        'color'=>'<option value=""></option>',
	        'clarity'=>'<option value=""></option>',
	    );
	    $style_sn = _Request::getString('style_sn');
	    $styleModel = new BaseStyleInfoModel(11);
	    $model = new AppStyleQuickdiyModel(11);
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
	    $htmlArr['style_name'] = $styleInfo['style_name'];	    
	    $styleAttrList = $this->getAttrListBySN($style_sn,$attribute_code_arr);
	    if(!$styleAttrList){
	        $result['success'] = 0;
	        $result['data'] = "款号不存在";
	        Util::jsonExit($result);
	    }
	    foreach ($styleAttrList as $key=>$vo){
	        if($key=="yanse"){
	            $key ="color";
	        }else if($key=="zuanshijingdu"){
	            $key ="clarity";
	        }
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