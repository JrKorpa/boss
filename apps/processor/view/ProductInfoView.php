<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 14:58:58
 *   @update	:
 *  -------------------------------------------------
 */
class ProductInfoView extends View
{
	protected $_id;
	protected $_bc_sn;
	protected $_p_id;
	protected $_p_sn;
	protected $_style_sn;
	protected $_status;
	protected $_weixiu_status;
	protected $_buchan_fac_opra;
	protected $_factory_opra_status;
	protected $_num;
	protected $_prc_id;
	protected $_prc_name;
	protected $_opra_uname;
	protected $_add_time;
	protected $_esmt_time;
	protected $_rece_time;
	protected $_info;
	protected $_from_type;
	protected $_xiangqian;
	protected $_bc_style;
	protected $_caigou_info;
	protected $_buchan_times;
	protected $_is_4c;//是否是4C销售
	protected $_purchase_cost;//采购成本
	protected $_purchase_discount;//采购折扣
	protected $_zhengshuhao_org;//原始证书号
	protected $_is_alone;//售卖方式
	protected $_qiban_type;//起版类型
	protected $_diamond_type;//售卖方式
	protected $_origin_dia_type;//原钻石类型
	protected $_to_factory_time;//分配工厂时间
	protected $_wait_dia_starttime;//等钻开始时间
	protected $_wait_dia_endtime;//实际等钻结束时间
	protected $_wait_dia_finishtime;//预计等钻完成时间
	protected $_oqc_pass_time;//OQC质检通过时间
    protected $_peishi_goods_id;//配石货号
    protected $_is_quick_diy;//是否快速定制
    protected $_is_combine;//是否组合镶嵌
    protected $_combine_goods_id;//组合镶嵌现货托货号
    protected $_biaozhun_jinzhong_min;
    protected $_biaozhun_jinzhong_max;
    protected $_lishi_jinzhong_min;
    protected $_lishi_jinzhong_max;
    
	public function get_id(){return $this->_id;}
	public function get_is_alone(){return $this->_is_alone;}
	public function get_bc_sn(){return $this->_bc_sn;}
	public function get_p_id(){return $this->_p_id;}
	public function get_p_sn(){return $this->_p_sn;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_status(){return $this->_status;}
	public function get_weixiu_status(){return $this->_weixiu_status;}
	public function get_buchan_fac_opra(){return $this->_buchan_fac_opra;}
	public function get_factory_opra_status(){return $this->_factory_opra_status;}
	public function get_num(){return $this->_num;}
	public function get_prc_id(){return $this->_prc_id;}
	public function get_prc_name(){return $this->_prc_name;}
	public function get_opra_uname(){return $this->_opra_uname;}
	public function get_add_time(){return $this->_add_time;}
	public function get_esmt_time(){return $this->_esmt_time;}
	public function get_rece_time(){return $this->_rece_time;}
	public function get_info(){return $this->_info;}
	public function get_from_type(){return $this->_from_type;}
	public function get_bc_style(){return $this->_bc_style;}
	public function get_xiangqian(){return $this->_xiangqian;}
	public function get_caigou_info(){return $this->_caigou_info;}
	public function get_buchan_times(){return $this->_buchan_times;}
	public function get_is_4c(){return $this->_is_4c;}
    public function get_purchase_cost(){return $this->_purchase_cost;}
    public function get_purchase_discount(){return $this->_purchase_discount;}
    public function get_zhengshuhao_org(){return $this->_zhengshuhao_org;}
    public function get_qiban_type(){return $this->_qiban_type;}
    public function get_diamond_type(){return $this->_diamond_type;}
    public function get_origin_dia_type(){return $this->_origin_dia_type;}
 	public function get_to_factory_time(){return $this->_to_factory_time;}
    public function get_wait_dia_starttime(){return $this->_wait_dia_starttime;}
    public function get_wait_dia_endtime(){return $this->_wait_dia_endtime;}
    public function get_wait_dia_finishtime(){return $this->_wait_dia_finishtime;}
    public function get_oqc_pass_time(){return $this->_oqc_pass_time;}
    public function get_peishi_goods_id(){return $this->_peishi_goods_id;}
    public function get_is_quick_diy(){ return $this->_is_quick_diy;}
    public function get_is_combine(){ return $this->_is_combine;}
    public function get_combine_goods_id(){ return $this->_combine_goods_id;}


    
	public function get_attr($id=0)
	{  
		$attrModel = new ProductInfoAttrModel(13);
		if(!empty($id)){
		    $attr = $attrModel->getGoodsAttr($id);
		}else{
		    $attr = $attrModel->getGoodsAttr($this->_id);
		}
		$kezi=new Kezi();
		foreach($attr as $k=>$v)
		{
			if(in_array($v['code'],array('kezi','work_con')))
			{
				$attr[$k]['value']=$kezi->retWord($v['value']);
			}
		}
		return $attr;
	}
	public function get_channel_name($id)
	{
		$SalesChannelsModel = new SalesChannelsModel(1);
		if($id == 0)
		{
			return '';
		}
        $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", array('id'=>$id));
		if($channellist)
		{
			return $channellist[0]['channel_name'];
		}
	}
	public function get_customer_name($id)
	{
		$CustomerSourcesModel = new CustomerSourcesModel(1);
		if($id == 0)
		{
			return '';
		}
        $CustomerSourcesList = $CustomerSourcesModel->getCustomerSourcesList("`id`,`source_name`",array('id'=>$id));
		if($CustomerSourcesList)
		{
			return $CustomerSourcesList[0]['source_name'];
		}
	}

	/**
	 * 获取渠道分类
	 */
	public function get_channel_class($channel_id){
		$sql = "SELECT `channel_class` FROM `sales_channels` WHERE `id` = '".$channel_id."'";
		$res = DB::cn(1)->getOne($sql);
		return ($res)?$res:'';
	}
	//add by zhangruiying配置列表显示字段及允许排序字段
	function getFC()
	{
		return array(
			array(
				'field'=>'bc_sn',
				'sort'=>'main.id',
				'is_sort'=>1,
				'title'=>'布产单号'
			),
			array(
				'field'=>'from_type',
				'sort'=>'main.from_type',
				'is_sort'=>1,
				'title'=>'布产来源'
			),
			array(
				'field'=>'style_sn',
				'sort'=>'main.style_sn',
				'is_sort'=>1,
				'title'=>'款号'
			),
			array(
				'field'=>'num',
				'sort'=>'main.num',
				'is_sort'=>1,
				'title'=>'数量'
			),
			array(
				'field'=>'consignee',
				'sort'=>'main.consignee',
				'is_sort'=>1,
				'title'=>'客户姓名'
			),
			array(
				'field'=>'',//opra_uname
				'sort'=>'',//main.opra_uname
				'is_sort'=>1,
				'title'=>'跟单人'
			),
			array(
						'field'=>'production_manager_name',
						'sort'=>'main.production_manager_name',
						'is_sort'=>1,
						'title'=>'生产经理'
				),
			array(
				'field'=>'prc_id',//prc_name
				'sort'=>'main.prc_id',
				'is_sort'=>1,
				'title'=>'工厂名称'
			),
			array(
				'field'=>'to_factory_time',
				'sort'=>'main.to_factory_time',
				'is_sort'=>1,
				'title'=>'分配工厂时间'
			),
			array(
				'field'=>'order_time',
				'sort'=>'main.order_time',
				'is_sort'=>1,
				'title'=>'工厂接单时间'
			),
			array(
				'field'=>'esmt_time',
				'sort'=>'main.esmt_time',
				'is_sort'=>1,
				'title'=>'标准出厂时间'
			),
			array(
				'field'=>'rece_time',
				'sort'=>'main.rece_time',
				'is_sort'=>1,
				'title'=>'工厂交货时间'
			),
			array(
				'field'=>'status',
				'sort'=>'main.status',
				'is_sort'=>1,
				'title'=>'布产状态'
			),
			array(
				'field'=>'buchan_fac_opra',
				'sort'=>'main.buchan_fac_opra',
				'is_sort'=>1,
				'title'=>'生产状态'
			),
			array(
				'field'=>'bc_style',
				'sort'=>'main.bc_style',
				'is_sort'=>1,
				'title'=>'布产类型'
			),
			array(
				'field'=>'xiangqian',
				'sort'=>'main.xiangqian',
				'is_sort'=>1,
				'title'=>'镶嵌要求'
			),
			array(
				'field'=>'caigou_info',
				'sort'=>'main.caigou_info',
				'is_sort'=>1,
				'title'=>'采购/订单备注'
			),
				array(
				'field'=>'channel_id',
				'sort'=>'main.channel_id',
				'is_sort'=>1,
				'title'=>'销售渠道'
			),
			array(
				'field'=>'customer_source_id',
				'sort'=>'main.customer_source_id',
				'is_sort'=>1,
				'title'=>'客户来源'
			),

			array(
				'field'=>'online',
				'sort'=>'main.channel_id',
				'is_sort'=>0,
				'title'=>'线上/线下'
			),
			array(
				'field'=>'create_user',
				'sort'=>'main.create_user',
				'is_sort'=>1,
				'title'=>'制单人'
			),
			array(
				'field'=>'time',
				'sort'=>'r.time',
				'is_sort'=>1,
				'title'=>'最后操作时间'
			),
			array(
				'field'=>'opra_remark',
				'sort'=>'r.remark',
				'is_sort'=>1,
				'title'=>'操作备注'
			),
			array(
				'field'=>'qiban_type',
				'sort'=>'qiban_type',
				'is_sort'=>1,
				'title'=>'起版类型'
			),
			array(
				'field'=>'diamond_type',
				'sort'=>'diamond_type',
				'is_sort'=>1,
				'title'=>'钻石类型'
			),
			array(
				'field'=>'wait_dia_starttime',
				'sort'=>'wait_dia_starttime',
				'is_sort'=>1,
				'title'=>'等钻时间'
			),
			array(
				'field'=>'wait_dia_finishtime',
				'sort'=>'wait_dia_finishtime',
				'is_sort'=>1,
				'title'=>'预计等钻完成时间'
			),
			array(
				'field'=>'wait_dia_endtime',
				'sort'=>'wait_dia_endtime',
				'is_sort'=>1,
				'title'=>'实际等钻结束时间'
			),
			array(
				'field'=>'oqc_pass_time',
				'sort'=>'oqc_pass_time',
				'is_sort'=>1,
				'title'=>'OQC质检通过时间'
			),
			array(
				'field'=>'referer',
				'sort'=>'referer',
				'is_sort'=>1,
				'title'=>'订单录单来源'
			),
            array(
                'field'=>'p_sn_out',
                'sort'=>'p_sn_out',
                'is_sort'=>1,
                'title'=>'外部单号'
            ),
		    array(
		        'field'=>'is_combine',
		        'sort'=>'main.is_combine',
		        'is_sort'=>1,
		        'title'=>'是否组合镶嵌'
		    ),
		    array(
		        'field'=>'combine_goods_id',
		        'sort'=>'main.combine_goods_id',
		        'is_sort'=>1,
		        'title'=>'组合镶嵌现货托'
		    )

		);
	}

    public function get_biaozhun_jinzhong_min() {
    	if(in_array($this->_qiban_type, array('0','1'))){
    	    $sql="select q.jinzhong_min from kela_supplier.product_info p,kela_supplier.product_goods_rel r,app_order.app_order_details d,purchase.purchase_qiban_goods q 
where p.id=r.bc_id and r.goods_id=d.id and d.ext_goods_sn=q.addtime and p.id='{$this->_id}'";
    	    $res=DB::cn(1)->getOne($sql); 
    	    return $res;
    	}else
    	    return $this->_biaozhun_jinzhong_min;
    }

    public function get_biaozhun_jinzhong_max() { 
    	if(in_array($this->_qiban_type, array('0','1'))){
    	    $sql="select q.jinzhong_max from kela_supplier.product_info p,kela_supplier.product_goods_rel r,app_order.app_order_details d,purchase.purchase_qiban_goods q 
where p.id=r.bc_id and r.goods_id=d.id and d.ext_goods_sn=q.addtime and p.id='{$this->_id}'";
    	    $res=DB::cn(1)->getOne($sql); 
    	    return $res;    	
        }else    	
    	return $this->_biaozhun_jinzhong_max;

    }

    public function get_lishi_jinzhong_min() { 
    	if(in_array($this->_qiban_type, array('0','1'))){
    	    return '';
    	}else
    	    return $this->_lishi_jinzhong_min;    	
    }
    public function get_lishi_jinzhong_max() {
    	if(in_array($this->_qiban_type, array('0','1'))){    	   
    	    return '';    	
        }else
            return $this->_lishi_jinzhong_max;

    }

}
?>