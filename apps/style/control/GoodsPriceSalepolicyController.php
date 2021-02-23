<?php
/**
 * 按款定价商品管理（官网数据对接）
 *  -------------------------------------------------
 *   @file		: GoodsPriceByStyleController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *  -------------------------------------------------
 */
class GoodsPriceSalepolicyController extends CommonController {

    protected $smartyDebugEnabled = true;

    public $whitelist = array('getAttrHtml','getAttrPriceList','getAttrSalepolicyList');
    /**
     * 	index，搜索框
     */
    public function index($params) {
        
        $salesChannelModel = new SalesChannelsModel(1);
        $sales_channel = $salesChannelModel->getList("id,channel_name","is_deleted=0");
       
        $this->render('goodsprice_salepolicy_search_form.html', array(
			'bar' => Auth::getBar(),
            'sales_channel' => $sales_channel,      
        ));
        
    }
    /**
     * 搜索
     * @param unknown $params
     */
    public function search($params){

        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'style_sn' => _Request::getString('style_sn'),
            'channel_id' => _Request::getInt('channel_id')
        );
        $args['style_sn'] = str_replace(","," ",$args['style_sn']);
        $args['style_sn'] = preg_replace('/\s+/is'," ",$args['style_sn']);
        $style_sn_arr = $args['style_sn']?explode(' ',$args['style_sn']):array();
        
        $where = array(
            'style_sn'   => $style_sn_arr,
            'channel_id' => $args['channel_id'],
            'is_delete' => 0 //未删除
        );
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
        
        $model = new AppGoodsPriceSalepolicyModel(11);//52只读，11可写
        $salesChannelModel = new SalesChannelsModel(1);
        $data = $model->pageList($where, $page,$pageSize=20);
        if(!empty($data['data'])){            
            foreach ($data['data'] as $key=>$vo){
                $vo['channel_name'] = $salesChannelModel->getNameByid($vo['channel_id']);
                $data['data'][$key] = $vo;
            }
        }
        //print_r($data);        
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'goodsprice_salepolicy_search_page';
        $this->render('goodsprice_salepolicy_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }
    
    public function add($params){
        $result = array('success' => 0,'error' => '','title'=>'添加销售政策');
        
        $salesChannelModel = new SalesChannelsModel(1);
        $sales_channel = $salesChannelModel->getList("id,channel_name","is_deleted=0");
        
        $result['content'] = $this->fetch('goodsprice_salepolicy_add.html', array(
            'sales_channel' => $sales_channel,
        ));
        
        Util::jsonExit($result);
    }
    
    /**
     * 编辑销售政策
     * @param unknown $params
     */
    public function edit($params){
               
        $result = array('success' => 0,'error' => '','title'=>'编辑销售政策');
        $id = _Request::getInt('id');
        
        $salesChannelModel = new SalesChannelsModel(1);
        $model = new AppGoodsPriceSalepolicyModel($id,11);
        $data = $model->getDataObject();
        
        if(empty($data)){
            $result['error'] = "当前编辑记录不存在";
            Util::jsonExit($result);
        }else{
            $data['channel_name'] = $salesChannelModel->getNameByid($data['channel_id']); 
        }

        $result['content'] = $this->fetch('goodsprice_salepolicy_edit.html', array(
            'data' => $data,
        ));
        
        Util::jsonExit($result);
    }
    
    public function insert($params){
        $result = array('error'=>'','success'=>0);
        
        $channel_id = _Post::getInt('channel_id');
        $jiajialv = _Post::getString('jiajialv');
        $sta_value = _Post::getString('sta_value');
        $style_sn  = _Post::getString('style_sn');
        
        $style_sn = str_replace(","," ",$style_sn);
        $style_sn = preg_replace('/\s+/is'," ",$style_sn);
        $style_sn_arr = $style_sn?explode(' ',$style_sn):array();
        
        if(!empty($jiajialv) && !is_numeric($jiajialv)){
            $result['error'] = "加价率必须为数字类型";
            Util::jsonExit($result);
        }
        if(!empty($sta_value) && !is_numeric($sta_value)){
            $result['error'] = "固定值必须为数字类型";
            Util::jsonExit($result);
        }
        if(empty($style_sn_arr)){
            $result['error'] = "款号不能为空";
            Util::jsonExit($result);
        }else{
            //检查款号是否存在
        }
        $model = new AppGoodsPriceSalepolicyModel(11);
        $data = array();
        foreach($style_sn_arr as $style_sn){
            
            $exist = $model->checkExists($style_sn, $channel_id);
            if(!$exist){
                $data[] = array(
                    'style_sn'=>$style_sn,
                    'channel_id'=>$channel_id,
                    'jiajialv'  =>(float)$jiajialv,
                    'sta_value' =>(float)$sta_value,
                    'create_time'=>date('Y-m-d H:i:s'),
                    'update_time'=>date('Y-m-d H:i:s')
                );
            }else{
                $result['error'] = "款号【{$style_sn}】在当前销售渠道已被添加过！";
                Util::jsonExit($result);
            }
            
        }
        if(!empty($data)){
            try{
                $model->insertAll($data,"app_goodsprice_salepolicy");               
            }catch (Exception $e){
                $result['error'] = "添加失败,请重新尝试！";
                Util::jsonExit($result);
            }
       }
       
       $result['success'] = 1;
       Util::jsonExit($result);

    }
    /**
     * 更新销售政策
     * @param unknown $params
     */
    public function update($params){
        
        $result = array('error'=>'','success'=>0);
        
        $id = _Request::getInt('id');
        $jiajialv  = _Post::getString('jiajialv');
        $sta_value = _Post::getString("sta_value");
        
        if(!empty($jiajialv) && !is_numeric($jiajialv)){
            $result['error'] = "加价率必须为数字类型";
            Util::jsonExit($result);
        }
        if(!empty($sta_value) && !is_numeric($sta_value)){
            $result['error'] = "固定值必须为数字类型";
            Util::jsonExit($result);
        }
        
        $model = new AppGoodsPriceSalepolicyModel($id,11);
        $olddo = $model->getDataObject();
        
        if(empty($olddo)){
            $result['error'] = "当前编辑记录不存在";
            Util::jsonExit($result);
        }
        $newdo = array(
            'id'      =>$id,
            'jiajialv'=>(float)$jiajialv,
            'sta_value'=>(float)$sta_value,
            'update_time'=>date('Y-m-d H:i:s')            
        );
        try{
            $model->saveData($newdo, $olddo);
            $result['success'] = 1;
            Util::jsonExit($result);
        }catch (Exception $e){
            $result['error'] = "保存失败,请重新尝试！";
            Util::jsonExit($result);
        }
        
    }
    /**
     * 批量删除
     * @param unknown $params
     */
    public function delete($params){
        $result = array('error'=>'','success'=>0);
        
        $ids = _Post::getList('_ids');
        $model = new AppGoodsPriceSalepolicyModel(11);
        try{
            $res = $model->deleteById($ids);
            $result['success'] = 1;
            Util::jsonExit($result);
        }catch (Exception $e){
            $result['error'] = "删除失败";
            Util::jsonExit($result);
        }
    }
    
    public function deleteAll(){
        $result = array('error'=>'','success'=>0);
        
        $style_sn = _Post::getString('style_sn');
        $model = new AppGoodsPriceSalepolicyModel(11);
        try{
            $res = $model->deleteByStyleSn($style_sn);
            $result['success'] = 1;
            Util::jsonExit($result);
        }catch (Exception $e){
            $result['error'] = "删除失败";
            Util::jsonExit($result);
        } 
    }
}

