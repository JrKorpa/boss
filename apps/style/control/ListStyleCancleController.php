<?php
/**
 *  -------------------------------------------------
 *   @file		: ListStyleCancleController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-02 14:35:42
 *   @update	:
 *  -------------------------------------------------
 */
class ListStyleCancleController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        //产品线
        $new_product_data= array();
        $productModel = new AppProductTypeModel(11);
        $product_data = $productModel->getCtlList();
        foreach ($product_data as $val){
            $new_product_data[$val['product_type_id']]=$val['product_type_name'];
        }
        //var_dump($new_product_data);die;
        //获取分类名称
         $new_cat_data= array();
         $appCatModel = new AppCatTypeModel(11);
         $cat_data = $appCatModel->getCtlListon();
         foreach ($cat_data as $val){
             $new_cat_data[$val['cat_type_id']]=$val['cat_type_name'];
         }
         $this->assign('cat_data',$new_cat_data);//数据字典
         $this->assign('product_data',$new_product_data);//数据字典
		$this->render('list_style_cancle_search_form.html',array('bar'=>Auth::getBar()));
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
			'style_sn' => _Request::getString('style_sn'),
            'product_type_id' => _Request::getInt('product_type_id'),
            'cat_type_id' => _Request::getInt('cat_type_id'),
		);
		$page = _Request::getInt("page",1);

        $product_type = '';
        $app_type = new AppProductTypeModel(11);
        if($args['product_type_id']!=''){
            $res = $app_type->get_Product_type_id(array('product_type_id'=>$args['product_type_id']));
            foreach($res as $k=>$val){   
                $ret[$k]=$val['parent_id'];
            }
            $parent =implode(',',$ret);
            if($parent==1){
                $res = $app_type->get_Product_type_id(array('parent_id'=>$args['product_type_id']));
                foreach($res as $k=>$val){   
                    $ret[$k]=$val['product_type_id'];
                }
                $product_type = implode(',',$ret).','.$args['product_type_id'];
            }else{
                $product_type = $args['product_type_id'];
            }

        }
        
		$where['check_status'] = 7;//作废
        $where['style_sn'] = $args['style_sn'];
        $where['product_type_id'] = $product_type;
        $where['cat_type_id'] = $args['cat_type_id'];
		$model = new BaseStyleInfoModel(11);
		$data = $model->zuofei_pageList($where,$page,10,false);
        if ($data) {
            foreach ($data['data'] as &$val) {
                $style_type = $model->getStyleTypeList($val['style_type']);
                if($style_type){
                    $val['style_type'] = isset($style_type['cat_type_name']) ?$style_type['cat_type_name']:"";
                }else{
                    $val['style_type'] = '';
                }
                $product_type = $model->getProductTypeList($val['product_type']);
                if($product_type){
                    $val['product_type'] = isset($product_type['product_type_name'])?$product_type['product_type_name']:'';
                }else{
                    $val['product_type'] = '';
                }
            }
        }
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'list_style_cancle_search_page';
		$this->render('list_style_cancle_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}
    
     /*
     * 作废款恢复 
     */
    public function cancle_recover() {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('id');
        $model = new BaseStyleInfoModel($id, 12);
        $do = $model->getDataObject();
        
        $model->setValue('check_status', 3);
        $res = $model->save(true);
        $model->addBaseStyleLog(array('style_id'=>$id,'remark'=>'作废已恢复成已审核的'));
        
        /*//作废后款商品上架，
        $style_sn = $do['style_sn'];
        $ListGoodsModel = new ListStyleGoodsModel(11); 
        $style_where = array('style_sn'=>$style_sn,'is_ok'=>1);
        $ListGoodsModel->UpdateListGoodsByStyleSn($style_where);
        //销售政策商品上架
        $apiSalePolicyModel = new ApiSalePolicyModel();
        $salepolicy_data = array(array('goods_sn'=>$style_sn,'is_sale'=>'1','is_valid'=>1,'type'=>1));
        $apiSalePolicyModel->UpdateSalepolicygoodIsSale(array('update_data'=>$salepolicy_data));
        */
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '操作失败';
        }
        Util::jsonExit($result);
    }
}

?>