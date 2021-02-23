<?php
    /**
     *  -------------------------------------------------
     *   @file		: GoodsController.php
     *   @link		:  www.kela.cn
     *   @copyright	: 2014-2024 kela Inc
     *   @author	: Laipiyang <462166282@qq.com>
     *   @date		: 2015-03-08 15:15:41
     *   @update	:
     *  -------------------------------------------------
     */
    class ProcessorInAccountController extends CommonController
    {
        protected $smartyDebugEnabled = true;
        protected $whitelist = array('downCsv');

        /**
         *	index，搜索框
         */
        public function index ($params)
        {
            $processorModel = new ApiProcessorModel();
            $process_list = $processorModel->GetSupplierList();//modify minitues later
           // $model = new ProcessorInAccountModel(29);
           // $companys = $model->getJiagongshangCompany();
            $this->render(  'processor_in_account_search_form.html',array(
                            'bar'=>Auth::getBar(),
                            'dd' => new DictView(new DictModel(1)),
                            'process_list' => $process_list,
                           // 'companys' => $companys
                            )
                          );
        }
        /**
         *	search，列表
         */
        public function search ($params)
        {

            $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
            $args = array(
                'mod'	=> _Post::get("mod"),
                'con'	=> substr(__CLASS__, 0, -10),
                'act'	=> __FUNCTION__,
                'company' => _Request::get('company'),
                'pro_id'  => _Request::get('pro_name'),
                'pay_channel'	=> _Request::get('pay_channel'),//渠道
                'fin_status'=> _Request::get('fin_status'),
                'account_type' => _Request::get('account_type'),
                'fin_check_time_start' => _Request::get('finance_check_time_start'),
                'fin_check_time_end'   => _Request::get('finance_check_time_end'),
                'make_time_start' => _Request::get('make_time_start'),
                'make_time_end' => _Request::get('make_time_end'),
                'check_time_start' => _Request::get('check_time_start'),
                'check_time_end' => _Request::get('check_time_end'),
                'put_in_type' => _Request::get('put_in_type'),
                'page' => $page

                );
			//var_dump( $args);exit;

            $model = new ProcessorInAccountModel(29);
            $data  = $model->GetProcessorInfo($args);
	
			//var_dump($data);exit;
            $pageData = $data['data'];

            $newdata = $pageData['data'];
			$list =array(); 
            foreach ($newdata as $key => $val) {
                $bill_no = $val['bill_no'];
                $list[$bill_no]['bill_no'] = $val['bill_no'];
                $list[$bill_no]['chengbenjia'] = $val['goods_total'];
                $list[$bill_no]['amount'] = $val['amount'];
                $list[$bill_no]['pro_name'] = $val['pro_name'];
                $list[$bill_no]['pay_method'] = $val['pay_method'];
                $list[$bill_no]['create_time'] = $val['create_time'];
                $list[$bill_no]['fin_check_status'] = $val['fin_check_status'];
                $list[$bill_no]['bill_type'] = $val['bill_type'];
                $list[$bill_no]['id'] = $val['id'];
                $list[$bill_no]['put_in_type'] = $val['put_in_type'];
                $list[$bill_no]['bill'][] = $val;
            }

           if($data['error'] != 1) {
                //$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
                //$pageData = $data['data'];
                $pageData['filter'] = $args;
                $pageData['jsFuncs'] = 'processor_in_account_search_page';
                $this->render('processor_in_account_search_list.html',array(
                            'pa'=>Util::page($pageData),
                            'page_list'=>$list,
                            'dd'=>new DictView(new DictModel(1)),
                            ));
           }
        }

        /*
         * 财务审核
         */
        function checkTrue($params){
                       
            $ids = $params['_ids'];
            $result = array('success' => 1,'error' => '');
            $model = new ProcessorInAccountModel(29);
            $bill_no=  _Post::get('id');
            foreach($ids as $k => $v) {
                $bill_no = $v;
                $ret = $model->checkStatus($bill_no);
                if($ret['error'] == 1) {
                    $result['error'] = "该单号:{$bill_no}财务已经审核过，请核实！";
                    Util::jsonExit($result);
                }
                $res = ApiModel::warehouse_api(array('bill_no'), array($bill_no), 'UpdateFinCheck');
                if($res == false) {
                    $result['error'] == "该单号：{$bill_no}审核失败";
                }
                
            }
            Util::jsonExit($result);
        }

        //
        function downCsv()
        {
            
            $args = array(
                'mod'	=> _Post::get("mod"),
                'con'	=> substr(__CLASS__, 0, -10),
                'act'	=> __FUNCTION__,
                'company' => _Request::get('company'),
                'pro_id'  => _Request::get('pro_name'),
                'pay_channel'	=> _Request::get('pay_channel'),//渠道
                'fin_status'=> _Request::get('fin_status'),
                //'bill_status'=>  _Request::get('bill_status'),//业务审核状态
                'account_type' => _Request::get('account_type'),
                'fin_check_time_start' => _Request::get('fin_check_time_start'),
                'fin_check_time_end'   => _Request::get('fin_check_time_end'),
                'make_time_start' => _Request::get('make_time_start'),
                'make_time_end' => _Request::get('make_time_end'),
                'check_time_start' => _Request::get('check_time_start'),
                'check_time_end' => _Request::get('check_time_end'),
                'put_in_type' => _Request::get('put_in_type'),
                'page' => '',
                );

            $model = new ProcessorInAccountModel(29);
            $dictModel = new DictModel(1);
            $data = $model->GetProcessorInfo($args);
// 			var_dump($data);exit;

            $newdata = $data['data'];
            $title = array('单号','供货商','入库方式','制单时间','业务审核时间','出库成本','实际支付总额','金重','送货单号','备注');
            if (is_array($newdata)){
                $cnt = count($newdata);
                $size  = ceil($cnt/100);
                for($i=0;$i<$size;$i++){
                    $tmp = array_slice($newdata,$i*100,100);
                    $bill_ids = $bill_nos = array();
                    foreach($tmp as $k=>$v){
                        $bill_ids[] = $v['id'];
                        $bill_nos[] = $v['bill_no'];
                    }            
                    $data = ApiModel::warehouse_api(array("bill_no"), array(implode(",",array_unique($bill_nos))), "getJinZhong");
                    $data2 = ApiModel::warehouse_api(array('bill_id'), array(implode(",",$bill_ids)), "getSendGoodsSn");
                    
                    foreach ($tmp as $k=>$v){
                        $jinzhong = $data['data'][$v['bill_no']];
                        if (isset($data2['data'][$v['id']])) {
                            $send_goods_sn = $data2['data'][$v['id']];
                        }else { 
                            $send_goods_sn = '';
                        }
                        $bill_id = $v['id'];
                        $val = array($v['bill_no'],$v['pro_name'],$dictModel->getEnum('warehouse.put_in_type',$v['put_in_type']),$$v['create_time'],$v['check_time'],$v['goods_total'],$v['amount'],"$jinzhong","$send_goods_sn",$v['bill_note']);
                        
                        //$val = iconv('utf-8','gbk',var_export($val,true));
                        $val = eval('return '.iconv('utf-8','GB18030',var_export($val,true).';')) ;
                        $content[] = $val;
//                         var_dump($val);exit;
                    }
                    //var_dump($content);exit;
                }
            }
            $model = new GoodsModel(29);
            $model->detail_csv('加工商入库结算',$title,$content);
        }

    }

    ?>