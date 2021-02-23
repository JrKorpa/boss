<?php

/*
 *  -------------------------------------------------
 *   @file		: sf.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2018-2094 kela Inc
 *   @author		: luochuanrong 
 *   @date		: 2018
 *   @update		:
 *  -------------------------------------------------
 */

class Invoice { 
        /*
        const  _SALETAXNUM ='339901999999142';     // 销方税号 测试： '339901999999142'
        const _IDENTITY ='93363DCC6064869708F1F3C72A0CE72A713A9D425CD50CDE'; //身份认证
        const _MAKEORDERURL = 'http://nnfpdev.jss.com.cn/shop/buyer/allow/cxfKp/cxfServerKpOrderSync.action';
        const _SEARCH_ORDERE_URL ='http://nnfpdev.jss.com.cn/shop/buyer/allow/ecOd/queryElectricKp.action';
        const _SEARCH_INVOICE_NUM_URL = 'http://nnfpdev.jss.com.cn/shop/buyer/allow/ecOd/queryElectricKp.action';
        const _TAX_RATE = 0.16;
        */

        const  _SALETAXNUM = '91440300080763832U';     // 销方税号 测试： '339901999999142'
        const _IDENTITY = 'A0138BA45284F9C8AF0BD4B1191B2B864F1FC40BBDA54489'; //身份认证
        const _MAKEORDERURL =           'http://nnfp.jss.com.cn/shop/buyer/allow/cxfKp/cxfServerKpOrderSync.action';
        const _SEARCH_ORDERE_URL =      'http://nnfp.jss.com.cn/shop/buyer/allow/ecOd/queryElectricKp.action';
        const _SEARCH_INVOICE_NUM_URL = 'http://nnfp.jss.com.cn/shop/buyer/allow/ecOd/queryElectricKp.action';
        const _TAX_RATE = 0.16;
       
        /**
         *  构造函数
         *
         */
        function __construct() {
               
        }

        public static function makeOrder($order){
	            $param=array(
			                'identity' =>self::_IDENTITY, 
			                'order' => array(
			                           'buyername' => $order['invoice_title'], //购方名称
			                           'taxnum'    => $order['taxpayer_sn'], //购方税号 企业要填，个人可为空
			                           'phone'     => $order['mobile'], // 购方手机(开票成功会短信提醒购方)
			                           //'address'   => $order['invoice_address'], //购方地址 企业要填，个人可为空
			                           'account'   => '', //购方银行账号 企业要填，个人可为空
			                           'orderno'   => $order['order_sn'], //订单号
			                           'invoicedate'=>$order['invoicedate'], //开票时间
			                           'clerk'      => '许恩妮', //开票人
                                       'payee'      => '徐飞燕',  //收款人
                                       'checker'    => '张敏敏',  //复核人
 			                           'salephone'  =>'0755-25503254' ,//销方电话
			                           'saleaddress' =>'深圳市龙岗区南湾街道布澜路31号中盈珠宝工业厂区B1栋3层' ,//销方地址
			                           'saletaxnum' => self::_SALETAXNUM,//销方税号
                                       'saleaccount' => '交通银行深圳布吉支行443066412018010108262', //销方银行及账号
			                           'kptype'     => '1',  //  
			                )  
	            );

	            if(!empty($order['detail'])){
	                foreach ($order['detail'] as $key => $d) {
                        $spbm = '106050901';//珠宝首饰
                        if($d['goods_name']=='金条')
                            $spbm = '10803110102';
                        $goods_name = $d['goods_name'];
                        if(strlen($d['goods_name'])>90){
                            $names = explode(',',$d['goods_name']);
                            $goods_name = $names[0];
                        }
                        $spec = $d['spec'];
                        if(strlen($d['spec'])>40){
                            $names = explode(',',$d['spec']);
                            $spec = $names[0];
                        }                        
	                    $param['order']['detail'][] = 
			                                    array(
			                                  	  'goodsname' => $goods_name,//商品名称
			                                  	  'num'       => $d['goods_count'], //数量
			                                  	  'price'     => round($d['goods_price']/$d['goods_count'],5), //单价
                                                  'taxamt'    => $d['goods_price'],
                                                  'taxfreeamt'=> round($d['goods_price']/(1+(time()>strtotime('2019-04-01 00:00:01') ? 0.13 : 0.16)),2),        //round($d['goods_price']/(1+self::_TAX_RATE),2),
                                                  'tax'       => $d['goods_price'] - round($d['goods_price']/(1+(time()>strtotime('2019-04-01 00:00:01') ? 0.13 : 0.16)),2), //$d['goods_price'] - round($d['goods_price']/(1+self::_TAX_RATE),2),                                                  
                                                  'unit'      => $d['unit'] , //单位
                                                  'spec'      => $spec,  //型号
			                                      'hsbz'      =>'1',//单价含税标志，0:不含税,1:含税
			                                      'taxrate'   => (time()>strtotime('2019-04-01 00:00:01') ? 0.13 : 0.16),  //self::_TAX_RATE, //税率
			                                      'spbm'      => $spbm, //商品编码
			                                      'fphxz'     => '0', //发票行性质:0, 正常行;1,折扣行;2,被折扣行
			                                  	); 

	                }
	            }
                //print_r($param);

	            $des = new DESDZFP();
                $body=$des->encrypt(json_encode($param));
                //echo $body;
                $body=array('order' => $body);
                $res = self::post(self::_MAKEORDERURL,$body);                  
                $res=json_decode($res,true);
                //echo "<pre>";
                //print_r($res);  
                return $res;          
        }


        public static function makeOrder2($order,$invoice_sn,$invoice_num,$pre_date=''){
                $_tax_rate = 0.13;
                if(!empty($pre_date) && $pre_date < strtotime('2019-04-01 00:00:00')*1000)
                    $_tax_rate = 0.16;
   
                $param=array(
                            'identity' =>self::_IDENTITY, 
                            'order' => array(
                                       'buyername' => $order['invoice_title'], //购方名称
                                       'taxnum'    => $order['taxpayer_sn'], //购方税号 企业要填，个人可为空
                                       'phone'     => '0', // 购方手机(开票成功会短信提醒购方)
                                       //'address'   => $order['invoice_address'], //购方地址 企业要填，个人可为空
                                       'account'   => '', //购方银行账号 企业要填，个人可为空
                                       'orderno'   => $order['order_sn'], //订单号
                                       'invoicedate'=>$order['invoicedate'], //开票时间
                                       'clerk'      => '许恩妮', //开票人
                                       'payee'      => '徐飞燕',  //收款人
                                       'checker'    => '张敏敏',  //复核人
                                       'salephone'  =>'0755-25503254' ,//销方电话
                                       'saleaddress' =>'深圳市龙岗区南湾街道布澜路31号中盈珠宝工业厂区B1栋3层' ,//销方地址
                                       'saletaxnum' => self::_SALETAXNUM,//销方税号
                                       'saleaccount' => '交通银行深圳布吉支行443066412018010108262', //销方银行及账号
                                       'kptype'     => '2',  //  
                                       'fpdm'       =>str_pad($invoice_sn,12,'0',STR_PAD_LEFT),
                                       'fphm'       =>str_pad($invoice_num,8,'0',STR_PAD_LEFT),
                            )  
                );

                if(!empty($order['detail'])){
                    foreach ($order['detail'] as $key => $d) {
                        $spbm = '106050901';//珠宝首饰
                        if($d['goods_name']=='金条')
                            $spbm = '10803110102';
                        $goods_name = $d['goods_name'];
                        if(strlen($d['goods_name'])>90){
                            $names = explode(',',$d['goods_name']);
                            $goods_name = $names[0];
                        }
                        $spec = $d['spec'];
                        if(strlen($d['spec'])>90){
                            $names = explode(',',$d['spec']);
                            $spec = $names[0];
                        }                        
                        $param['order']['detail'][] = 
                                                array(
                                                  'goodsname' => $goods_name,//商品名称
                                                  'num'       => $d['goods_count']*-1, //数量
                                                  'price'     => round($d['goods_price']/$d['goods_count'],5), //单价
                                                  'taxamt'    => $d['goods_price']*-1,
                                                  'taxfreeamt'=> round($d['goods_price']/(1+$_tax_rate),2)*-1, //round($d['goods_price']/(1+self::_TAX_RATE),2)*-1,
                                                  'tax'       => ($d['goods_price'] - round($d['goods_price']/(1+$_tax_rate),2))*-1,     //($d['goods_price'] - round($d['goods_price']/(1+self::_TAX_RATE),2))*-1,                                                  
                                                  'unit'      => $d['unit'] , //单位
                                                  'spec'      => $spec,  //型号
                                                  'hsbz'      =>'1',//单价含税标志，0:不含税,1:含税
                                                  'taxrate'   => $_tax_rate,  //self::_TAX_RATE, //税率
                                                  'spbm'      => $spbm, //商品编码
                                                  'fphxz'     => '0', //发票行性质:0, 正常行;1,折扣行;2,被折扣行
                                                ); 

                    }
                }
                //print_r($param);

                $des = new DESDZFP();
                $body=$des->encrypt(json_encode($param));
                //echo $body;
                $body=array('order' => $body);
                $res = self::post(self::_MAKEORDERURL,$body);                  
                $res=json_decode($res,true);
                //echo "<pre>";
                //print_r($res);  
                return $res;          
        }

        public static function searchOrder($order_sn){
	            $param=array(
			                'identity' =>self::_IDENTITY, 
			                'orderno' => array($order_sn)   
	            );

	            

	            $des = new DESDZFP();
                $body=$des->encrypt(json_encode($param));
                //echo $body;
                $body=array('order' => $body);
                $res = self::post(self::_SEARCH_ORDERE_URL,$body);                  
                $res=json_decode($res,true);
                //echo "<pre>";
                //print_r($res);  
                return $res;          
        }
        public static function searchInvoiceNum($invoice_num){
                $param=array(
                            'identity' =>self::_IDENTITY, 
                            'fpqqlsh' => array($invoice_num),  
                );                

                $des = new DESDZFP();
                $body=$des->encrypt(json_encode($param));
                //echo $body;
                $body=array('order' => $body);
                $res = self::post(self::_SEARCH_INVOICE_NUM_URL,$body);                  
                $res=json_decode($res,true);
                //echo "<pre>";
                //print_r($res);  
                return $res;          
        }

        public static function post($url,$body) 
        { 
            
        $postUrl = $url;
        $curlPost = $body;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        //curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded;charset=UTF-8"));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        //print_r($data);
        return $data;

        }

}          

?>