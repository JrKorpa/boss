<?php

/**
 *  -------------------------------------------------
 *   @file		: OutorderToklOrderController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com
 *   @date		: 2015-01-28 12:36:56
 *   @update	:
 *  -------------------------------------------------
 */
class OutorderToklOrderController extends CommonController {

    protected $smartyDebugEnabled = true;
    protected $whitelist = array("matchingOrder");

    /**
     * 	index
     */
    public function index($params) {

        $this->render('outorder_tokl_order_info.html', array('bar' => Auth::getBar()));
    }

    /**
     * 	导出数据
     */
    public function matchingOrder($params) {

        ini_set('memory_limit','-1');
        set_time_limit(0);
        $res = array('error'=>1,'msg'=>'');
        $error_str = '';
		$out_order_sn = _Post::getString('out_order_sn');
        if(empty($out_order_sn)){

            $error_str.= '订单号为空，请重新输入！';
		}
        $orderModel= new BaseOrderInfoModel(27);
        $order_sn_list = '';
        $error_out_order = '';
        if($out_order_sn){
            //若订单号中间存在空格、汉字、逗号替换为英文模式逗号；
            $out_order_sn = str_replace(' ',',',$out_order_sn);
            $out_order_sn = str_replace('，',',',$out_order_sn);
			$out_order_sn = str_replace(array("\r\n", "\r", "\n"),',',$out_order_sn);
            $outData = explode(",", $out_order_sn);
            foreach($outData as $val){
				if($val == ''){

                    continue;
                }
                $order_info_ct = $orderModel->checkOrderByWhere($val);
                if(empty($order_info_ct)){

                    $error_out_order.= $val.",";
                }
                $order_sn_list .= "'$val',";
            }

            if($error_out_order != ''){

                $error_str.= "系统没有以下： “".rtrim($val,",")."” 外部订单号，请核实后重新输入！";
            }
        }
        $data = array();
        $title = array();
        $xls_content = '';
        if($error_str != ''){

            echo $error_str;
        }else{

            $where = array();
            $where['out_order_sn'] = rtrim($order_sn_list,',');
            $data = $orderModel->selectKelaOrderByOutOrder($where);
            $xls_content = "BDD订单号,外部订单号\r\n";
        }
        
        foreach ($data as $key => $value) {
            # code...
            $a = iconv('utf-8','gb2312',htmlspecialchars($value['order_sn']));
            $b = iconv('utf-8','gb2312',htmlspecialchars($value['out_order_sn']));
            $xls_content .= $a.",".$b."\t\n";
        }
        $filename = '匹配外部订单号'.date('Ymd His').'.csv'; //设置文件名 
        $this->export_csv($filename,$xls_content); //导出 
	}

    public function export_csv($filename,$data)
    {
        # code...
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $data;
    }
}