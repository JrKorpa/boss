<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseQibanGoodsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-01 15:22:51
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseQibanGoodsView extends View
{
	protected $_id;
	protected $_info;
	protected $_price;
	protected $_addtime;
	protected $_order_sn;
	protected $_opt;
	protected $_customer;
	protected $_xiangkou;
	protected $_shoucun;
	protected $_specifi;
	protected $_fuzhu;
	protected $_qibanfei;
	protected $_jinliao;
	protected $_jinse;
	protected $_gongyi;
	protected $_is_shenhe;
	protected $_is_fukuan;
	protected $_gongchang;
	protected $_gongchang_id;
	protected $_kuanhao;
	protected $_zhengshu;
	protected $_xuqiu;
	protected $_pic;
	protected $_status;
    protected $_kuan_type;
    protected $_zhushi_num;
    protected $_cert;
    protected $_jinzhong_min;
    protected $_jinzhong_max;
    protected $_yanse;
    protected $_jingdu;

	public function get_id(){return $this->_id;}
	public function get_info(){return $this->_info;}
	public function get_price(){return $this->_price;}
	public function get_addtime(){return $this->_addtime;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_opt(){return $this->_opt;}
	public function get_customer(){return $this->_customer;}
	public function get_xiangkou(){return $this->_xiangkou;}
	public function get_shoucun(){return $this->_shoucun;}
	public function get_specifi(){return $this->_specifi;}
	public function get_fuzhu(){return $this->_fuzhu;}
	public function get_qibanfei(){return $this->_qibanfei;}
	public function get_jinliao(){return $this->_jinliao;}
	public function get_jinse(){return $this->_jinse;}
	public function get_gongyi(){return $this->_gongyi;}
	public function get_is_shenhe(){return $this->_is_shenhe;}
	public function get_is_fukuan(){return $this->_is_fukuan;}
	public function get_gongchang(){return $this->_gongchang;}
	public function get_gongchang_id(){return $this->_gongchang_id;}
	public function set_gongchang_id($id){$this->_gongchang_id = $id;}
	public function get_kuanhao(){return $this->_kuanhao;}
	public function get_zhengshu(){return $this->_zhengshu;}
	public function get_xuqiu(){return $this->_xuqiu;}
	public function get_pic(){return $this->_pic;}
	public function get_status(){return $this->_status;}
    public function get_kuan_type(){return $this->_kuan_type;}
    public function get_zhushi_num(){return $this->_zhushi_num;}
    public function get_cert(){return $this->_cert;}
    public function get_jinzhong_min(){return $this->_jinzhong_min;}
    public function get_jinzhong_max(){return $this->_jinzhong_max;}
    public function get_yanse(){return $this->_yanse;}
    public function get_jingdu(){return $this->_jingdu;}
    var $goodsAttrModel ;
    public function  __construct($obj){
        parent::__construct($obj);
        $this->goodsAttrModel = new GoodsAttributeModel(17);
    }
    public function getCaizhiList(){
        return $this->goodsAttrModel->getCaizhiList();
    }
    public function getJinseList(){
        return $this->goodsAttrModel->getJinseList();
    }
    public function getCertList(){
        return $this->goodsAttrModel->getCertList();
    }
    public function getFaceworkList(){
        return $this->goodsAttrModel->getFaceworkList();
    }
    public function getXiangqianList(){
        $data = $this->goodsAttrModel->getXiangqianList();
        foreach ($data as $key=>$vo){
            if(false !== strpos($vo,'镶嵌4C')){
                unset($data[$key]);
            }
        }
        return $data;
    }
    public function getSupplierList(){
        $apiProcessor = new ApiProcessorModel();
        return $apiProcessor->GetSupplierList();        
    }
    public function getColorList(){
        return $this->goodsAttrModel->getColorList();
    }

    public function getClarityList(){
        return $this->goodsAttrModel->getClarityList();
    }

}
?>