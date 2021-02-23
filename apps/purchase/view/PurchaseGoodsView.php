<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseGoodsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-09 17:54:49
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseGoodsView extends View
{
	protected $_id;
	protected $_pinfo_id;
	protected $_style_sn;
	protected $_g_name;
	protected $_product_type_id;
	protected $_cat_type_id;
	protected $_num;
	protected $_is_urgent;
	protected $_info;
	protected $_xiangqian;
	protected $_diamond_size;
	protected $_color;
	protected $_neatness;
	protected $_certificate_no;
	protected $_is_apply;
	protected $_apply_info;
	protected $_consignee;

	public function get_id(){return $this->_id;}
	public function get_pinfo_id(){return $this->_pinfo_id;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_g_name(){return $this->_g_name;}
	public function get_product_type_id(){return $this->_product_type_id;}
	public function get_cat_type_id(){return $this->_cat_type_id;}
	public function get_num(){return $this->_num;}
	public function get_is_urgent(){return $this->_is_urgent;}
	public function get_info(){return $this->_info;}
	public function get_xiangqian(){return $this->_xiangqian;}
	public function get_is_apply(){return $this->_is_apply;}
	public function get_apply_info(){return $this->_apply_info;}
	public function get_consignee(){return $this->_consignee;}


	//获取采购单信息
	public function get_purchase_info($field){
		$sql = "SELECT `".$field."` FROM `purchase_info` WHERE `id` = '".$this->_pinfo_id."'";
		$res = $this->getModel()->db()->getOne($sql);
		return ($res)?$res:'';
	}

	public function get_goods_attr($code){
		if($this->_id){
			return $this->replaceTsKezi($this->get_attr($code));
		}else{
			return '';
		}
	}

	//获取布产状态
	public function get_bc_status(){
		$ApiProModel = new ApiProcessorModel();
		//p_sn采购单号 style_sn款号
		$res = $ApiProModel->get_bc_status($this->get_purchase_info('p_sn'),$this->get_style_sn(),$this->get_id());
		return $res;
	}
    public function get_buchan_info($id){
        $sql ="select * from kela_supplier.product_info where p_id={$id} and from_type=1";
        return $this->getModel()->db()->getRow($sql);
    }
	public function get_color()
	{
		if($this->_id)
		{
		return $this->get_attr('color');
		}
	}
	public function get_diamond_size()
	{
		if($this->_id)
		{
		return $this->get_attr('diamond_size');
		}

	}
	public function get_neatness()
	{
		if($this->_id)
		{
		return $this->get_attr('neatness');
		}
	}
	public function get_certificate_no()
	{
		if($this->_id)
		{
		return $this->get_attr('certificate_no');
		}
	}
	public function get_attr($code)
	{
		$model = new PurchaseGoodsModel(23);

		$name = $model->getAttrInfoByCode($this->_id,$code);
		return $name;
	}
	public function get_product_name($product_id)
	{
		$model = new ApiStyleModel();
		$product_name = $model->getProductName($product_id);
		return $product_name;
	}

	public function get_cat_name($cat_id)
	{
		$model = new ApiStyleModel();
		$cat_name = $model->getCatName($cat_id);
		return $cat_name;
	}

	public function get_all_attr(){
		$data = $this->getModel()->get_all_attr($this->_id);
		return $data;
	}
	public function get_apply_attr(){
		$data = unserialize($this->get_apply_info());
		return $data;
	}

    /**
     * 替换刻字特殊字符串
     * @param type $kezi
     * @return string
     */
    public function replaceTsKezi($kezi='')
    {
        if($kezi!=''){

            //替换刻字特殊字符串
            $kezi = str_replace('a01','\\',$kezi);
            $kezi = str_replace('a02','\'',$kezi);
            $kezi = str_replace('a03','"',$kezi);
        }
        return $kezi;
    }


}
?>