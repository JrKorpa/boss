<?php

/**
 *  -------------------------------------------------
 *   @file		: AppOrderPayActionController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-19 18:16:49
 *   @update	:
 *  -------------------------------------------------
 */
class OrderZpController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('downLoad');

    /**
     * 	index，搜索框
     */
    public function index($params) {
        $SalesChannelsModel = new SalesChannelsModel(1);
        $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        $this->render('app_order_details_zp_search_form.html', array('bar' => Auth::getBar(),'channellist'=>$channellist));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'start_time' => _Request::get('start_time'),
            'end_time' => _Request::get('end_time'),
            'channel_id' => _Request::get('channel_id'),
            'goods_sn' => _Request::get('goods_sn'),
        );
        $page = _Request::getInt("page",1);
        if(empty($args['start_time'])&&!empty($args['end_time'])){
            $args['start_time']=date("Y-m-d H:i:s",strtotime($args['end_time'])-100*24*3600);
        }
        if(empty($args['end_time'])&&!empty($args['start_time'])){
            $args['end_time']=date("Y-m-d H:i:s",strtotime($args['end_time'])+100*24*3600);
        }
        //都不填默认 显示  从今天到30天前的
        if(empty($args['start_time'])&&empty($args['end_time'])){
            $args['end_time']=date('Y-m-d');
            $args['start_time']=date('Y-m-d',strtotime($args['end_time'])-100*24*3600);
        }
        
        
        if(strtotime($args['start_time'])<(strtotime($args['end_time'])-100*24*3600)){
            $info='开始时间和结束时间只能相差100天';
            $this->render('app_order_gift_search_list.html', array(
                'info'=>$info,
                'page_list' =>array(),
            ));
            exit;
        }

        $where = array();
        $where['start_time'] = $args['start_time']." 00:00:00";
        $where['end_time'] = $args['end_time']." 23:59:59";
        $where['channel_id'] = $args['channel_id'];
        $where['goods_sn'] = $args['goods_sn'];
        $where['page']=$page;
        /*原有的api
        $model = new ApiSalesModel();
        $data = $model->getDetailsInfo($where);
        */
        
        $SalesModel=new SalesModel(27);
        $data=$SalesModel->SearchGoodsZp($where);
		$tongji = array('type'=>0,'total'=>0);
		if(!empty($data)){
			$tongji['type'] = count($data);
            $tongji['total']=0;
			foreach($data as $k => $v){
				$tongji['total'] += $v['xuqiu'];
			}
		}
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_order_gift_search_page_zp';
        $this->render('app_order_gift_search_list.html', array(
            'page_list' =>$data,
            'info'=>'',
            'tongji'=>$tongji,
        ));
    }

    /**
     * 	downLoad，下载
     */
    public function downLoad($params) {
        set_time_limit(0);
        ini_set('memory_limit','2000M');
        $this->dd = new DictView(new DictModel(1));
        $payMentModel = new PaymentModel(1);
        $allPay = array_column($payMentModel->getAll(),'pay_name','id');//订购类型
        $buchan_status = array('1'=>'未操作','2'=>'已布产','3'=>'生产中','4'=>'已出厂','5'=>'不需布产');//布产状态
        //获取全部的有效的销售渠道
        $SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        //获取所有数据
        $allSalesChannelsData = array();
        foreach ($getSalesChannelsInfo as $val) {
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        }
        //来源
        $customer_source_model = new CustomerSourcesModel(1);
        $customer_source_name = $customer_source_model->getSources();
        $source=array();
        foreach($customer_source_name as $k=>$v){
            $source[$v['id']]=$v['source_name'];
        }

        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'start_time' => _Request::get('start_time'),
            'end_time' => _Request::get('end_time'),
            'channel_id' => _Request::get('channel_id'),
            'goods_sn' => _Request::get('goods_sn'),
        );

        if(empty($args['start_time'])&&!empty($args['end_time'])){
            $args['start_time']=date("Y-m-d H:i:s",strtotime($args['end_time'])-100*24*3600);
        }
        if(empty($args['end_time'])&&!empty($args['start_time'])){
            $args['end_time']=date("Y-m-d H:i:s",strtotime($args['end_time'])+100*24*3600);
        }
        //都不填默认 显示  从今天到30天前的
        if(empty($args['start_time'])&&empty($args['end_time'])){
            $args['end_time']=date('Y-m-d');
            $args['start_time']=date('Y-m-d',strtotime($args['end_time'])-100*24*3600);
        }
        
        
        if(strtotime($args['start_time'])<(strtotime($args['end_time'])-100*24*3600)){
            $info='开始时间和结束时间只能相差100天';
            $this->render('app_order_gift_search_list.html', array(
                'info'=>$info,
                'page_list' =>array(),
            ));
            exit;
        }

        $where = array();
        $where['start_time'] = $args['start_time']." 00:00:00";
        $where['end_time'] = $args['end_time']." 23:59:59";
        $where['channel_id'] = $args['channel_id'];
        $where['goods_sn'] = $args['goods_sn'];
        $where['page']=$page;
        //$model = new ApiSalesModel();
        //$data = $model->getOrderdownLoad($where);
        $SalesModel=new SalesModel(27);
        $data=$SalesModel->SearchOrderdownLoad($where);
        
        $datalists=array();
        if($data){
			$k=0;
            foreach($data as $m=>$v){
            	for($i=0;$i<$v['goods_count'];$i++,$k++){
                $datalists[$k]['order_sn']="'".$v['order_sn'];
                $datalists[$k]['consignee']=$v['consignee'];
                $datalists[$k]['mobile']=$v['mobile'];
                $datalists[$k]['create_user']=$v['create_user'];
                $datalists[$k]['order_status']=$this->dd->getEnum('order.order_status',$v['order_status']);
                $datalists[$k]['order_pay_status']=$this->dd->getEnum('order.order_pay_status',$v['order_pay_status']);
                $datalists[$k]['order_pay_type']=$allPay[$v['order_pay_type']];
                $datalists[$k]['buchan_status']=$buchan_status[$v['buchan_status']];
                $datalists[$k]['delivery_status']=$this->dd->getEnum('sales.delivery_status',$v['delivery_status']);
                $datalists[$k]['send_good_status']=$this->dd->getEnum('order.send_good_status',$v['send_good_status']);
                $datalists[$k]['apply_return']=$v['apply_return']==1?"未操作":"正在退款";
                if($v['is_xianhuo']==1){
                    $datalists[$k]['is_xianhuo']="未操作";
                }elseif($v['is_xianhuo']==1){
                    $datalists[$k]['is_xianhuo']="未选商品";
                }else{
                    $datalists[$k]['is_xianhuo']="期货";
                }
                if($v['order_status']==4){
                    $datalists[$k]['apply_close']="已关闭";
                }elseif($v['apply_close']==1){
                    $datalists[$k]['apply_close']="已申请";
                }else{
                    $datalists[$k]['apply_close']="未申请";
                }
                $datalists[$k]['department_id']=$allSalesChannelsData[$v['department_id']];
                $datalists[$k]['customer_source_id']=$source[$v['customer_source_id']];
                $datalists[$k]['referer']=$v['referer'];
                $datalists[$k]['goods_sn']=$v['goods_sn'];
                $datalists[$k]['goods_name']=$v['goods_name'];
                $datalists[$k]['zhiquan']=$v['zhiquan'];
				}
            }
        }
        $title = array(
				'订单号',
                '客户名称',
                '电话号码',
                '制单人',
                '订单状态',
                '支付状态',
                '订购类型',
                '布产状态',
                '配货状态',
                '发货状态',
                '退款状态',
                '订单类型',
                '申请关闭',
				'销售渠道',
				'客户来源',
                '录单来源',
                '款号',
                '名称',
                '指圈');
            
            Util::downloadCsv("订单取赠品",$title,$datalists);
    }

}

?>
