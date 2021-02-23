<?php

/**
 *  -------------------------------------------------
 *   @file		: AppFactoryApplyController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 10:37:58
 *   @update	:
 *  -------------------------------------------------
 */
class AppFactoryApplyController extends CommonController {

    protected $smartyDebugEnabled = false;

    /**
     * 	index，搜索框
     */
    public function index($params) {
    	$processorList = $this->getProcessorList();
    	$allProcessorInfo = array();
    	if (!empty($processorList['data'])) {
    		$allProcessorInfo = $processorList['data'];
    	}

        $this->render('app_factory_apply_search_form.html', array(
        	'bar' => Auth::getBar(),
        	'factories' => $allProcessorInfo
        ));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'style_sn' => _Request::getString('style_sn'),
			'factory_id' => _Request::getInt('factory_id'),
        	'applier' => _Request::getString('applier'),
        	'apply_type0' => _Request::getInt('apply_type0'),
        	'apply_type1' => _Request::getInt('apply_type1'),
        	'apply_type2' => _Request::getInt('apply_type2'),
            'apply_type2' => _Request::getInt('apply_type3'),
        	'apply_status' => _Request::getInt('apply_status'),
        	'apply_start_time' => _Request::getString('apply_start_time'),
        	'apply_end_time' => _Request::getString('apply_end_time'),
        	'check_name' => _Request::getString('check_name'),
        	'check_start_time' => _Request::getString('check_start_time'),
        	'check_end_time' => _Request::getString('check_end_time'),
            'PageSize' => _Request::get('PageSize', 10)
        );
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
        
        $where = $args;
        if (!empty($args['apply_type0'])) $where['apply_type'][] = $args['apply_type0'];
        if (!empty($args['apply_type1'])) $where['apply_type'][] = $args['apply_type1'];
        if (!empty($args['apply_type2'])) $where['apply_type'][] = $args['apply_type2'];
        if (!empty($args['apply_type3'])) $where['apply_type'][] = $args['apply_type3'];
        
        $model = new AppFactoryApplyModel(11);
        $data = $model->pageList($where, $page, $args['PageSize'], false);
        if ($data) {
        	$gallery_model = new AppStyleGalleryModel(11);
        	$rel_style_fact_model = new RelStyleFactoryModel(11);
        	$api_model = new ApiProcessorModel();
            foreach ($data['data'] as &$val) {
                $val['status_name'] = $model->getStatusVal($val['status']);
				
	            $img = $gallery_model->getStyleGalleryInfo($val);
                if(!empty($img)){
                	$val['thumb_img'] = $img['thumb_img'];//45°图片
                	$val['big_img'] = $img['big_img'];
                } else {
                	$val['thumb_img'] = "";
                	$val['big_img'] = "";
                }
                
                // overide factory name;
                $val_factory_name = $api_model->GetSupplierListName($val['factory_id']);
                if (!empty($val_factory_name) && isset($val_factory_name['data'])) $val['factory_name'] = $val_factory_name['data'];
                
                // 获取当前的工厂信息 
               	$val['factory_info'] = '';
                $res = $rel_style_fact_model->getStyleFactoryInfo(array('style_id' => $val['style_id'], 'style_sn' => $val['style_sn']));
                foreach ($res as $key => $value) {
                	$factory_name = '';
                	$factory_id = isset($value['factory_id'])?$value['factory_id']:'';
                	if($factory_id != ''){
                		$name = $api_model->GetSupplierListName($value['factory_id']);
                		$factory_name = isset($name['data'])?$name['data']:'';
                	}
                	$factory_sn = isset($value['factory_sn'])?$value['factory_sn']:'';
                	$xiangkou = isset($value['xiangkou'])?$value['xiangkou']:'';
                	$factory_fee = isset($value['factory_fee'])?$value['factory_fee']:'';
                	$val['factory_info'] .= $factory_name.':'.$factory_sn.':'.$xiangkou.':'.$factory_fee.';<br />';//工厂模号信息
                }

                if($val['factory_info']){
                    $val['factory_info']=explode("<br />",$val['factory_info']);
                    $c='';
                    foreach($val['factory_info'] as $k=>$v){
                        if(strstr($v,$val['factory_name'])){
                            $c=$v;break;
                        }
                    }
                    foreach($val['factory_info'] as $k=>$v){
                        if(strstr($v,$val['factory_name'])){
                            unset($val['factory_info'][$k]);
                        }
                    }
                    $val['factory_info'][]=$c;
                    $val['factory_info']=implode("<br />",array_filter($val['factory_info']));
                }
                
            }
            unset($val);
        }
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_factory_apply_search_page';
        $this->render('app_factory_apply_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('app_factory_apply_info.html', array(
            'view' => new AppFactoryApplyView(new AppFactoryApplyModel(11))
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }

    /**
     * 	operationStatus，操作工厂状态
     */
    public function operationStatus($params) {
        $id = intval($params["id"]);
        $result = array('success' => 0, 'error' => '');
        $model = new AppFactoryApplyModel($id, 11);
        if($model->getValue('status')!=1){
            $result['content']="该工厂已操作";
            Util::jsonExit($result);
        }
        $result['content'] = $this->fetch('app_factory_apply_info.html', array(
            'view' => new AppFactoryApplyView($model)
        ));
        $result['title'] = '操作工厂状态';
        Util::jsonExit($result);
    }

    /**
     * 申请工厂审核通过
     * @param type $params
     */
    public function factoryCheck($params) {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $status = _Post::getInt('status');
        
        $model = new AppFactoryApplyModel($id, 12);
        $do = $model->getDataObject();
        $info = _Post::getString('info');
        if(empty($info)){
            $result['error'] = "操作备注不能为空";
            Util::jsonExit($result);
        }

       if($do['type']==1){
            if ($status == 2) {
                $pdo = $model->db()->db();
                $pdo->beginTransaction();
                
                $resp = $this->createRelStyleFactory($model, $do);
                if (!empty($resp['error'])) {
                	$pdo->rollBack();
                	$result['error'] = $resp['error'];
                	Util::jsonExit($result);
                }
            }
        }elseif($do['type']==3){
            if ($status == 2 )//只在状态发生改变时
            {
                $resp = $this->Set_RelStyleFactory($_model, $do);
                if (!empty($resp['error'])) {
                    $pdo->rollBack();
                    $result['error'] = $resp['error'];
                    Util::jsonExit($result);
                }
            }               
        }else{
            if ($status == 2) {
                $pdo = $model->db()->db();
                $pdo->beginTransaction();
                
                $f_id = $model->getValue('f_id');
                $_model = new RelStyleFactoryModel($f_id,12);
                $_model->delete();
            }
        }
                
        $model->setValue('status', $status);
        $model->setValue('check_time', date("Y-m-d H:i:s"));
        $model->setValue('check_name', $_SESSION['userName']);
        $model->setValue('info', $info);
        $res = $model->save(true);
        
        if ($res !== false) {
            $result['success'] = 1;
            if (isset($pdo)) $pdo->commit();
        } else {
            $result['error'] = $error;
            if (isset($pdo)) $pdo->rollBack();
        }
        Util::jsonExit($result);
    }

    /**
     * 申请工厂审核未通过
     * @param type $params
     */
    public function factoryCheckFalse($params) {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new AppFactoryApplyModel($id, 12);
        $do = $model->getDataObject();
        $status = $do['status'];
        if ($status != '1') {
            $result['error'] = "该条记录状态异常";
            Util::jsonExit($result);
        }
        $model->setValue('status', 3);
        $res = $model->save(true);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "审核未通过失败";
        }
        Util::jsonExit($result);
    }

    /**
     * 取消申请工厂
     * @param type $params
     */
    public function cancelApply($params) {
    	die();
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);

    	$model = new AppFactoryApplyModel($id, 12);
        $do = $model->getDataObject();
        $status = $do['status'];
        if ($status != '1') {
            $result['error'] = "该条记录状态异常";
            return $result;
        }
        
        $model->setValue('status', 4);
        $res = $model->save(true);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "取消申请失败";
        }
        
        Util::jsonExit($result);
    }

    /**
     * 	show，渲染查看页面
     */
    public function show($params) {
        die('开发中');
        $id = intval($params["id"]);
        $this->render('app_factory_apply_show.html', array(
            'view' => new AppFactoryApplyView(new AppFactoryApplyModel($id, 11))
        ));
    }

    /**
     * 	insert，信息入库
     */
    public function insert($params) {
        $result = array('success' => 0, 'error' => '');
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
        exit;
        $olddo = array();
        $newdo = array();

        $newmodel = new AppFactoryApplyModel(12);
        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	update，更新信息
     */
    public function update($params) {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('id');
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
        exit;

        $newmodel = new AppFactoryApplyModel($id, 12);

        $olddo = $newmodel->getDataObject();
        $newdo = array(
        );

        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	delete，删除
     */
    public function delete($params) {
        $id = intval($params['id']);
        $result = $this->inner_delete($id);
       
        Util::jsonExit($result);
    }
    
    /**
     * 批量审核
     * @param unknown $params
     */
    public function batchAudit($params) {
    	$result = array('success' => 0, 'error' => '');
		if (!isset($params['status']) || !isset($params['_ids']) || empty($params['_ids'])) {
			$result['error'] = '参数异常';
			Util::jsonExit($result);
		}
		
		$status = intval($params['status']);
		if ($status != 2 && $status != 3) {
			$result['error'] = '参数异常';
			Util::jsonExit($result);
		}
		
		$model = new AppFactoryApplyModel(12);
		if ($status == 2) {
		    $pdo = $model->db()->db();
		    $pdo->beginTransaction();
		    
		    foreach ($params['_ids'] as $id) {
		        $_model = new AppFactoryApplyModel($id, 12);
		        $do = $_model->getDataObject();
		        if ($do['type']==1) {
					if ($do['status']  != $status )//只在状态发生改变时
					{
						$resp = $this->createRelStyleFactory($_model, $do);
						if (!empty($resp['error'])) {
							$pdo->rollBack();
							$result['error'] = $resp['error'];
							Util::jsonExit($result);
						}
					}
		        } elseif($do['type']==3){
 					if ($do['status']  != $status )//只在状态发生改变时
					{
						$resp = $this->Set_RelStyleFactory($_model, $do);
						if (!empty($resp['error'])) {
							$pdo->rollBack();
							$result['error'] = $resp['error'];
							Util::jsonExit($result);
						}
					}               
                } elseif($do['type']==4){
                    if ($do['status'] != $status)
                    {
                        $f_id = $do['f_id'];
                        $rel_style_fact_model = new RelStyleFactoryModel($f_id,12);
                        $olddo = $rel_style_fact_model->getDataObject();
                        //print_r($olddo);die;
                        $newdo = array();
                        $newdo = array(
                            'f_id' => $f_id,
                            'factory_id' => $do['factory_id'],
                            'factory_sn' => $do['factory_sn'],
                            'factory_fee' => $do['factory_fee'],
                            'xiangkou' => $do['xiangkou']
                            );
                        $ckeck_moren = $rel_style_fact_model->getAllMoRenFactory($do['style_id'],$do['factory_id']);
                        if(!empty($ckeck_moren)){
                            $newdo['is_factory'] = 1;
                        }else{
                            $newdo['is_factory'] = 0;
                        }
                        $resp = $rel_style_fact_model->saveData($newdo, $olddo);
                        if(!$resp){
                            $pdo->rollBack();
                            $result['error'] = "审核失败！";
                            Util::jsonExit($result);
                        }
                    }
                }else {
		        	$f_id = $_model->getValue('f_id');
		        	$rel_style_fact_model = new RelStyleFactoryModel($f_id,12);
		        	$rel_style_fact_model->delete();
		        }
		        
		        $flag = $model->auditFactoryApply(array($id), $status, $_SESSION['userName']);
		        if ($flag === false) {
		            $pdo->rollBack();
		            break;
		        }
		    }
		    
		    if ($flag !== false) $pdo->commit();
		} else {
		    $flag = $model->auditFactoryApply($params['_ids'], $status, $_SESSION['userName']);
		}
		
		if ($flag === false) {
			$result['error'] = "操作失败";
		} else {
			$result['success'] = 1;
		}
		
		Util::jsonExit($result);
    }
    
    /**
     * 批量删除申请
     * @param unknown $params
     */
    public function batchDelete($params) {
        $result = array('success' => 0, 'error' => '');
        if (!isset($params['_ids']) || empty($params['_ids'])) {
            $result['error'] = '参数异常';
            Util::jsonExit($result);
        }
        
        $model = new AppFactoryApplyModel(12);
        $pdo = $model->db()->db();
        $pdo->beginTransaction();
        
        foreach ($params['_ids'] as $id) {
            $resp = $this->inner_delete($id, false);
            if (!empty($resp['error'])) {
                $pdo->rollBack();
                
                $result['error'] = $resp['error']; 
                Util::jsonExit($result);
                break;
            }
        }
        
        if (empty($resp['error'])) {
            $pdo->commit();
            
            $result['success'] = 1;
            Util::jsonExit($result);
        }
    }
    
    /**
     * 创建rel_style_factory记录;
     * @param AppFactoryApplyModel $model
     * @param unknown $factory_apply_dataObject
     */
    private function createRelStyleFactory(AppFactoryApplyModel $model, $factory_apply_dataObject) {
    	$result = array('error'=>'', 'success' => 0);
    	$_model = new RelStyleFactoryModel(12);
    	
    	$check_result = $_model->checkFactoryInfoIsUnique($factory_apply_dataObject['factory_id'], trim($factory_apply_dataObject['factory_sn']), $factory_apply_dataObject['style_sn']);
        if (!empty($check_result['error'])) {
        	$result['error'] = $check_result['error'];
        	return $result;
        }
    		
        $olddo = array();
        $newdo = array();
        $newdo['style_id'] = $factory_apply_dataObject['style_id'];
        $newdo['style_sn'] = $factory_apply_dataObject['style_sn'];
        $newdo['factory_id'] = $factory_apply_dataObject['factory_id'];
        $newdo['factory_sn'] = $factory_apply_dataObject['factory_sn'];
        $newdo['factory_fee'] = $factory_apply_dataObject['factory_fee'];
        $newdo['xiangkou'] = $factory_apply_dataObject['xiangkou'];

        $default_factory = $_model->getIsFactory($factory_apply_dataObject['style_id']);
        
        if (!$default_factory) {
            $newdo['is_def'] = 1;
            $newdo['is_factory'] = 1;
        } else {
            $newdo['is_factory'] = 0;
            if($default_factory['factory_id'] == $factory_apply_dataObject['factory_id']) {
                $newdo['is_factory'] = 1;
            }
        }
        $f_id = $_model->saveData($newdo, $olddo);
        $model->setValue('f_id', $f_id);
        $model->save(true);
        
        $result['success'] = 1;
        return $result;
    }
    
    /**
     * 更改rel_style_factory记录;
     * @param AppFactoryApplyModel $model
     * @param unknown $factory_apply_dataObject
     */
    private function Set_RelStyleFactory(AppFactoryApplyModel $model, $factory_apply_dataObject) {
    	$result = array('error'=>'', 'success' => 0);
    	$_model = new RelStyleFactoryModel(12);
    	
    	$check_result = $_model->checkFactoryInfoIsUnique($factory_apply_dataObject['factory_id'], trim($factory_apply_dataObject['factory_sn']), $factory_apply_dataObject['style_sn']);
        if (!empty($check_result['error'])) {
        	$result['error'] = $check_result['error'];
        	return $result;
        }
    		
        $apply_dataObject_model = new RelStyleFactoryModel($factory_apply_dataObject['f_id'],12);
        $olddo = $apply_dataObject_model->getDataObject();

        $newdo = array();

        $default_factory = $_model->getAllIsFactory($factory_apply_dataObject['style_id']);
        
        //$newdo['f_id'] = $factory_apply_dataObject['f_id'];
        if (!$default_factory) {
            //$newdo['is_def'] = 1;
            //$newdo['is_factory'] = 1;
            $factory=$_model->updateAllIIsFactory($factory_apply_dataObject['style_id'],$factory_apply_dataObject['factory_id']);
            if($factory){
                $_model->updateIsDef($factory_apply_dataObject['f_id']);
            }
        } else {
            /*$is_factory_model = new RelStyleFactoryModel($default_factory['f_id'],12);
            $is_factory_model->setValue('is_factory',0);
            $is_factory_model->setValue('is_def',0);
            if($is_factory_model->save(true)){
                $newdo['is_def'] = 1;
                $newdo['is_factory'] = 1;
            }*/
            $_model->updateIsFactory($factory_apply_dataObject['f_id'],$factory_apply_dataObject['style_id'],$factory_apply_dataObject['factory_id']);
        }
        //$f_id = $apply_dataObject_model->saveData($newdo, $olddo);
        
        $result['success'] = 1;
        return $result;
    }
    

    /**
     * 删除申请
     * @param unknown $id
     * @return string
     */
    private function inner_delete($id, $forced = true) {
    	$result = array('success' => 0, 'error' => '');
    	$model = new AppFactoryApplyModel($id, 12);
    	$do = $model->getDataObject();
    	
    	if (!$forced && intval($do['status']) > 1) {
    		$result['error'] = "删除失败";
    		return $result;
    	}

    	$res = $model->delete();
    	if ($res !== false) {
    		$result['success'] = 1;
    	} else {
    		$result['error'] = "删除失败";
    	}
    	
        return $result;
    }

    private function getProcessorList() {
    	$apiProcessorModel = new ApiProcessorModel();
    	$info = $apiProcessorModel->GetSupplierList();
    	return $info;
    }
}

?>