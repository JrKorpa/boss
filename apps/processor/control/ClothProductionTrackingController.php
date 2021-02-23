<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangruiying
 *   @date		: 2015-04-07 15:49:45
 *   @update	:
 *  -------------------------------------------------
 */
class ClothProductionTrackingController extends CommonController
{
	protected $smartyDebugEnabled = true;
        protected $whitelist = array('exportOQC','export');
	//获取传过来的变量
	public function getData()
	{
        $self = _Request::get('self') == true ? true : false;
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'p_sn'=>_Request::get("p_sn"),
            'bc_sn'=>_Request::get("bc_sn"),
            'opra_uname'=>_Request::get("opra_uname"),
            'style_sn'=>_Request::get("style_sn"),
            'oqc_num'=>_Request::get("oqc_num"),
            'is_extended'=>_Request::get("is_extended"),
            'start_time'=>_Request::get("start_time"),
            'end_time'=>_Request::get("end_time"),
            'order_start_time'=>_Request::get("order_start_time"),
            'order_end_time'=>_Request::get("order_end_time"),
            'edit_start_time'=>_Request::get("edit_start_time"),
            'edit_end_time'=>_Request::get("edit_end_time"),
            'question_type'=>_Request::get("question_type"),
            'buchan_fac_opra[]'=>_Request::getList("buchan_fac_opra"),
            'status'=>_Request::get("status"),
            'channel_id'=>_Request::get("channel_id"),
            'customer_source_id'=>_Request::get("customer_source_id"),
			'orderby'=>_Request::get("__order"),
			'desc_or_asc'=>_Request::get("__desc_or_asc"),
            'self' => $self,
		);
		$args['p_sn']=str_replace('，',' ',$args['p_sn']);
		$args['p_sn']=trim(preg_replace('/(\s+|,+)/',' ',$args['p_sn']));
		return $args;

	}

    /**
     * 跟单人只能查看自己名下的订单
     */
    public function index_x($params){
        $params['self'] = true;
        $this->index ($params);
    }

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $ocq_list=array('--全部--','1次','2次','3次','更多');
        $is_extended=array('距出厂不足两天','超期未出厂');
        $newmodel = new ProductFqcConfModel(13);
        $question_list = $newmodel->get_top_menu();
        $buchan_fac_opra=$this->dd->getEnumArray('buchan_fac_opra');
        //获取跟单人
        $gendanModel = new ProductFactoryOprauserModel(13);
        $gen_list = $gendanModel->select2($fields = ' distinct(`opra_uname`)' , $where = ' 1 ' , $type = 'all');

        $this->render('product_info_search_form.html',array(
            'bar'=>Auth::getBar(),'ocq_list'=>$ocq_list,'view'=>new ClothProductionTrackingView(new ClothProductionTrackingModel(13)),
            'is_extended'=>$is_extended,'question_list'=>$question_list,
            'buchan_fac_opra'=>$buchan_fac_opra,'user_list'=>$gen_list,
            'self' => (isset($params['self']) && ($params['self'] == true)) ? true : false,
            )
        );
	}
	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args=$this->getData();
		$page = _Request::getInt("page",1);
		$model = new ClothProductionTrackingModel(13);
        $page_num = (_Post::getInt('page_num'))?_Post::getInt('page_num'):10;

        //获取标示， 跟单人只能查看自己名下的订单 还是 查看全部监控
        $power = (isset($params['self']) && ($params['self'] == true)) ? $params['self'] : '';

		$data = $model->pageList($args,$page,$page_num,false,$power);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'ClothProductionTracking_search_page';
		$this->render('product_info_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,'view'=>new ClothProductionTrackingView($model)

		));
	}
        public function exportOQC()
        {
			$where=$this->getData();
			unset($where['orderby'],$where['desc_or_asc']);
            $model = new ClothProductionTrackingModel(13);
            $where = $model->checkOpra_uname($where, $where['self']);
            $where['oqc_result']=0;
			$data = $model->getdownload($where);
            $util=new Util();
            $title=array('订单号','布产号','款号','收货人','跟单人','接单时间','标准出厂时间','问题类型','问题原因','质检人','操作时间','备注');
             $arr=array();
            foreach($data as $key=>$v)
            {
                $temp=array();
                $temp['p_sn']=$v['p_sn'];
                $temp['bc_sn']=$v['bc_sn'];
                $temp['style_sn']=$v['style_sn'];
                $temp['consignee']=$v['consignee'];
                $temp['opra_uname']=$v['opra_uname'];
                $temp['order_time']=$v['order_time'];
                $temp['esmt_time']=$v['esmt_time'];
                $temp['oqc_reason']=$v['cat_name'];
                $temp['oqc_info']=$v['oqc_info'];
                $temp['o_uname']=$v['o_uname'];
                $temp['opra_time']=$v['opra_time'];
                $temp['info']=$v['info'];
                $arr[]=$temp;
            }
            $util->downloadCsv('质检未过明细',$title,$arr);
        }
        public function export()
        {
            $where=$this->getData();

			unset($where['orderby'],$where['desc_or_asc']);
            $model = new ClothProductionTrackingModel(13);
            $where = $model->checkOpra_uname($where, $where['self']);
			$data = $model->getdownload($where);
            $util=new Util();
            $title=array('订单号','布产号','款号','收货人','生产状态','跟单人','接单时间','标准出厂时间','问题类型','添加时间','最后操作时间','质检状态','OQC未过次数','备注');
            $arr=array();
            $oqr_status=array(0=>'未过',1=>'通过',2=>'未质检');
            foreach($data as $key=>$v)
            {
                $temp=array();
                $temp['p_sn']=$v['p_sn'];
                $temp['bc_sn']=$v['bc_sn'];
                $temp['style_sn']=$v['style_sn'];
                $temp['consignee']=$v['consignee'];
                $temp['buchan_fac_opra']=$this->dd->getEnum('buchan_fac_opra',$v['buchan_fac_opra']);
                $temp['opra_uname']=$v['opra_uname'];
                $temp['order_time']=$v['order_time'];
                $temp['esmt_time']=$v['esmt_time'];
                $temp['oqc_reason']=$v['cat_name'];
                $temp['add_time']=$v['add_time'];
                $temp['edit_time']=$v['edit_time'];
                $v['oqc_result']=$v['oqc_result']!==null?$v['oqc_result']:2;
                $temp['oqc_result']=$oqr_status[$v['oqc_result']];
                $temp['num']=$v['num'];
                $temp['info']=$v['info'];
                $arr[]=$temp;
            }
            $util->downloadCsv('布产监控明细',$title,$arr);
        }
}

?>