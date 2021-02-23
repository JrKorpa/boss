<?php
/**
 * 裸石供料配石
 *  -------------------------------------------------
 *   @file		: StoneFeedConfigController
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2017-06-12
 *   @update	:
 *  -------------------------------------------------
 */
class StoneFeedConfigController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array();

	/**
	 *	index，搜索框
	 */
	public function index ($params)	
	{       
        $this->render('stone_feed_config_search_form.html',array(
            'bar'=>Auth::getBar(),
            'view'=>new StoneFeedConfigView(new StoneFeedConfigModel(13)),
        ));
	}
	public function search($params){
	    $args = array(
	        'mod'	=> _Request::get("mod"),
	        'con'	=> substr(__CLASS__, 0, -10),
	        'act'	=> __FUNCTION__,
	        'factory_id'=>_Request::getInt('factory_id'),
	        'is_enable'=>_Request::get('is_enable'),
	        'shape'=> _Request::get('shape'),
	        'cert'=> _Request::get('cert'),
	        'feed_type'=> _Request::get('feed_type'),
	        'color'=> _Request::get('color'),
	        'clarity'=> _Request::get('clarity'),
	        'carat_min'=> _Request::getfloat('carat_min'),
	        'carat_max'=> _Request::getfloat('carat_max'),	        
	        'create_time_begin' =>_Request::get('create_time_begin'),
	        'create_time_end' =>_Request::get('create_time_end'),	       
	    );
	    $page = _Request::getInt("page",1);
	    $where = $args;
	    
	    $model = new StoneFeedConfigModel(13);
	    $data = $model->pageList($where,$page,20,false);
	    $pageData = $data;
	    
	    $pageData['filter'] = $args;
	    $pageData['jsFuncs'] = 'stone_feed_config_search_page';
	    $this->render('stone_feed_config_search_list.html',array(
	        'pa'=>Util::page($pageData),
	        'page_list'=>$data
	    ));
	}
	public function add($params){
	    $result = array('content'=>'','title'=>'添加');
	    $result['content'] = $this->fetch(
	        'stone_feed_config_info.html',array(
	        'view'=>new StoneFeedConfigView(new StoneFeedConfigModel(13)),
	    ));
	    Util::jsonExit($result);
	}
	/**
	 * 数据校验
	 * @param unknown $params
	 */
	protected function checkData($params){
        $result = array('success'=>0,'error'=>''); 
        
        $model = new StoneFeedConfigModel(13);
        
        $id = 0;
        if(!empty($params['id'])){
            $id = $params['id'];
        }
        if(empty($params['factory_id'])){
            $result['error'] = "请选择工厂！";
            return $result;
        }
        if(empty($params['prority_sort'])){
            $result['error'] = "请填写优先级！";
            return $result;
        }else if(!Util::isNum($params['prority_sort'])){
            $result['error'] = "优先级排序必须为数字！";
            return $result;
        }
        $res = $model->checkProritySort($params['factory_id'],$params['prority_sort'],$id); 
        if($res){
            $result['error'] = "优先级排序值重复！";
            return $result;
        }
	    if(!isset($params['carat_min'])){
	        $result['error'] = "石重下限不能为空！";
	        return $result;
	    }else if(!is_numeric($params['carat_min'])){
            $result['error'] = "石重下限必须为数字！";
            return $result;
	    }
	    if(!isset($params['carat_max'])){
	        $result['error'] = "石重上限不能为空！";
	        return $result;
	    }else if(!is_numeric($params['carat_max'])){
	        $result['error'] = "石重上限必须为数字！";
	        return $result;
	    }    

        if($params['carat_min']>$params['carat_max']){
            $result['error'] = "石重下限不能大于上限！";
            return $result;
        }

	    $result['success'] = 1;
	    return $result;	    
	}
	
	/**
	 * 新增
	 * @param unknown $params
	 */
	public function insert($params){
	    $result = array('success'=>0,'error'=>'');
	    $newdo = array(
	        'color'=>_Post::get('color'),
	        'clarity'=>_Post::get('clarity'),
	        'cert'=>_Post::get('cert'),
	        'carat_min'=>_Post::get('carat_min'),
	        'carat_max'=>_Post::get('carat_max'),
	        'factory_id'=>_Post::get('factory_id'),
	        'factory_name'=>_Post::get('factory_name'),
	        'feed_type'=>_Post::get('feed_type'),
	        'prority_sort'=>_Post::get('prority_sort'),
	        'create_user'=> $_SESSION['userName'],
	        'create_time'=> date('Y-m-d H:i:s'),
	        'is_enable'=>_Post::get('is_enable',1),
	    );
	    $checkRes = $this->checkData($newdo);
	    if($checkRes['success']==0){
	        $result['error'] = $checkRes['error'];
	        Util::jsonExit($result);
	    }
	    $prodModel = new AppProcessorInfoModel(13);	 
	    $model = new StoneFeedConfigModel(14);   
	    $newdo['factory_name'] = $prodModel->getProcessorName($newdo['factory_id']);	     
	    $pdolist[14] = $model->db()->db();
	    try{
    	    foreach ($pdolist as $pdo){
    	        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
    	        $pdo->beginTransaction();//开启事务
    	    } 
    	    
    	   $id = $model->saveData($newdo,array());
    	   foreach ($pdolist as $pdo){
    	       $pdo->commit();
               $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
    	   }
    	   $result['success'] = 1;
    	   Util::jsonExit($result);
	    }catch (Exception $e){
	        $error = "添加失败！".$e->getMessage();
	        Util::rollbackExit($error,$pdolist);
	    }
	    	    
	}
	/**
	 * 编辑
	 * @param unknown $params
	 */
	public function edit($params){
	   $result = array('content'=>'','title'=>'编辑');
	    $id = _Request::getInt('id');
	    if(empty($id)){
	        $result['content'] = "id is empty！";
	        Util::jsonExit($result);
	    }
	    
	    $model = new StoneFeedConfigModel($id,14);
	    $view = new StoneFeedConfigView($model);
	    if(!$view->get_id()){
	        $result['content'] = "编辑对象不存在，请重新尝试！";
	        Util::jsonExit($result);
	    }
	    
	    $result['content'] = $this->fetch(
	        'stone_feed_config_info.html',array(
	            'view'=>$view,
	        ));
	    Util::jsonExit($result);
	    
	}
	/**
	 * 更新
	 * @param unknown $params
	 */
	public function update($params){
	    $result = array('success'=>0,'error'=>'');
	    $id = _Request::getInt('id');
	    if(empty($id)){
	        $result['error'] = "id is empty!";
	        Util::jsonExit($result);
	    }
	    
	    $model = new StoneFeedConfigModel($id,14);
	     
	    $olddo = $model->getDataObject();
	    if(empty($olddo)){
	        $result['error'] = "编辑对象不存在，请重新尝试!";
	        Util::jsonExit($result);
	    }
	    $newdo = array(
	        'id' =>$id,
	        'color'=>_Post::get('color'),
	        'clarity'=>_Post::get('clarity'),
	        'cert'=>_Post::get('cert'),
	        'carat_min'=>_Post::get('carat_min'),
	        'carat_max'=>_Post::get('carat_max'),
	        'factory_id'=>_Post::get('factory_id'),
	        'factory_name'=>_Post::get('factory_name'),
	        'feed_type'=>_Post::get('feed_type'),
	        'prority_sort'=>_Post::get('prority_sort'),
	        'is_enable'=>_Post::get('is_enable',1),
	    );
	    $checkRes = $this->checkData($newdo);
	    if($checkRes['success']==0){
	        $result['error'] = $checkRes['error'];
	        Util::jsonExit($result);
	    }
	    
	    $prodModel = new AppProcessorInfoModel(13);
	    $newdo['factory_name'] = $prodModel->getProcessorName($newdo['factory_id']);	    
	    $pdolist[14] = $model->db()->db();
	    try{
	        //开启事物
	        foreach ($pdolist as $pdo){
	            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
	            $pdo->beginTransaction();//开启事务
	        }
	        	
	        $model->saveData($newdo,$olddo);	        
	        //提交事物
	        foreach ($pdolist as $pdo){
	            $pdo->commit();
	            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	        }
	        
	        //日志记录（异步执行）
	        $dataLog['pkdata'] = array('id'=>$id);
	        $dataLog['newdata'] = $newdo;
	        $dataLog['olddata'] = $olddo;
	        $dataLog['fields']  = $model->getFieldsDefine();	        
	        $this->operationLog("update",$dataLog);
	        
	        $result['success'] = 1;
	        Util::jsonExit($result);
	    }catch (Exception $e){
	        $error = "添加失败！".$e->getMessage();
	        Util::rollbackExit($error,$pdolist);
	    }
	}
	/**
	 * 批量启用
	 * @param unknown $params
	 */
	public function setEnable($params){
	    $result = array('success'=>0,'error'=>'');
	    
	    $ids = _Request::getList('_ids');
	    $model = new StoneFeedConfigModel(14);
	    try{
    	    $res = $model->updateIsEnable($ids,1);
    	    if($res === false){
    	        throw new Exception("批量启用失败");
    	    }
   	    
    	    $dataLog['remark']  = "对ID=".implode(",",$ids)."的记录进行启用";
    	    $this->operationLog("update",$dataLog);
    	    
    	    $result['success'] = 1;
    	    Util::jsonExit($result);
	    }catch (Exception $e){
	        $result['error'] = "操作失败！".$e->getMessage();
	        Util::jsonExit($result);
	    }
	}
	/**
	 * 批量禁用
	 * @param unknown $params
	 */
	public function setUnable($params){
	    $result = array('success'=>0,'error'=>'');
	     
	    $ids = _Request::getList('_ids');
	    $model = new StoneFeedConfigModel(14); 
	    
	    try{
    	    $res = $model->updateIsEnable($ids,0);
    	    if($res === false){
    	        throw new Exception("批量禁用失败");
    	    }
    	    
    	    $dataLog['remark']  = "对ID=".implode(",",$ids)."的记录进行禁用";
    	    $this->operationLog("update",$dataLog);
    	    
    	    $result['success'] = 1;
    	    Util::jsonExit($result);
	    }catch (Exception $e){
	        $result['error'] = "操作失败！".$e->getMessage();
	        Util::jsonExit($result);
	    }
	}
}

?>