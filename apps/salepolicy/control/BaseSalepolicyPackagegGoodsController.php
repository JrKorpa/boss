<?php

/**
 *  -------------------------------------------------
 *   @file		: BaseSalepolicyGoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-03 18:25:10
 *   @update	:
 *  -------------------------------------------------
 */
class BaseSalepolicyPackagegGoodsController extends CommonController {

    protected $smartyDebugEnabled = false;

    /**
     * 	index，搜索框
     */
    public function index($params) {
        $this->render('base_salepolicy_packageg_goods_search_form.html', array('bar' => Auth::getBar()));
    }

    /**
     * 	search，列表
     */
    public function search($params) {

        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'goods_id' => _Request::getString('goods_id'),
            'goods_sn' => _Request::getString('goods_sn'),
            'price_start' => _Request::getString('price_start'),
            'price_end' => _Request::getString('price_end'),
            'isXianhuo' => _Request::get('isXianhuo'),
        	'is_sale' => _Request::get('is_sale'),
            'type'=>2,
        );

        $page = _Request::getInt("page", 1);
        $where = array();
        $where['goods_sn'] = $args['goods_sn'];
        $where['goods_id'] = $args['goods_id'];
        $where['price_start'] = $args['price_start'];
        $where['price_end'] = $args['price_end'];
        $where['isXianhuo'] = $args['isXianhuo'];
        $where['is_sale'] = $args['is_sale'];
        $where['type']=$args['type'];
        $model = new BaseSalepolicyGoodsModel(17);
        $data = $model->pageList($where, $page, 10, false);
        if($data['data']){
            foreach ($data['data'] as &$val){
                $val['product_type_name'] = $model->getProductTypeName($val['product_type']);
                $val['cat_type_name'] = $model->getCatTypeName($val['category']);
            }
        }
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'base_salepolicy_packageg_goods_search_page';
        $this->render('base_salepolicy_packageg_goods_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }
    
    
    public function batchPackageg($param) {
        $result = array('success' => 0, 'error' => '');
        $ids = _Post::getList('_ids');
        if(count($ids)<2){
            $result['content'] = "绑定打包商品至少要两个货品";
            Util::jsonExit($result);
        }
        $model = new BaseSalepolicyInfoModel(17);
        $policyList = $model->getPolicyList(0,2);
        $togetherModel = new AppSalepolicyTogetherGoodsModel(17);
        $togetherList = $togetherModel->getTogetherList();
        $result['content'] = $this->fetch('base_salepolicy_packageed_goods_info.html', array(
            'ids' => implode(',', $ids),
            'policy_list' => $policyList,
            'together_list'=>$togetherList
        ));
        $result['title'] = '批量添加销售政策商品';
        Util::jsonExit($result);
    }
    
    
    public function batchHandle($param) {
        $ids = _Request::getString('_ids');
        $ids_arr = explode(",",$ids);
        //var_dump($ids_arr);exit;
        foreach($ids_arr as $id){
        	$newmodel = new BaseSalepolicyGoodsModel($id,18);
        	$is_sale = $newmodel->getValue("is_sale");       	
        	$goods_id[] = $newmodel->getValue("goods_id");
        	if($is_sale==0){
        		$result['error'] = '下架的货品不可以添加销售政策';
        		Util::jsonExit($result);
        	}
        }
        
        $policy_id = _Request::getInt('policy_id');
        $together_policy = _Request::getInt('together_policy');
        
        $_model = new AppSalepolicyGoodsModel(17);
        $_list = $_model->getTogetherList(array('policy_id'=>$policy_id,'together_id'=>$together_policy));
        if(count($_list)){
            $result['error'] = '该条销售政策、打包策略下已有商品';
        	Util::jsonExit($result);
        }
        //$_list = array_column($_list,'goods_id');
        
        //销售策略和打包策略绑定
        $AppTogetherPolicyModel = new AppTogetherPolicyRelatedModel(18);
        $AppTogetherPolicyNewdo = array('policy_id'=>$policy_id,'together_id'=>$together_policy);
        $id = $AppTogetherPolicyModel->saveData($AppTogetherPolicyNewdo, array());
        
        //打包策略和商品绑定
        $model = new BaseSalepolicyGoodsModel(17);
        $policyModel = new BaseSalepolicyInfoModel($policy_id,17);
        $arrList = $model->getListByIds($ids);
        $newdo = array();
        foreach ($arrList as $val) {
            //计算销售价格
            $sale_price = $val['chengbenjia'] * $policyModel->getValue('jiajia') + $policyModel->getValue('sta_value') ;
            $newdo[$val['goods_id']] = array(
                'together_id' => $id,
                'goods_id' => $val['goods_id'],
                'isXianhuo' => $val['isXianhuo'],
                'chengben' => $val['chengbenjia'],
                'sale_price' => $sale_price,
                'create_time' => date("Y-m-d H:i:s"),
                'create_user' => $_SESSION['userName'],
            );
        }
        
        $res = false;
        $AppTogetherGoodsRelatedModel = new AppTogetherGoodsRelatedModel(18);
        if(count($newdo)>0){
            $newdo = array_values($newdo);
            $res = $AppTogetherGoodsRelatedModel->insertAll($newdo);
        }
        if ($res !== false) {
            $result['success'] = 1;
            $result['title'] = '批量添加成功<br/>';
        } else {
            $result['error'] = '批量添加失败<br/>';
        }
        Util::jsonExit($result);
        
    }
    

}

?>