<?php

/*
 *  -------------------------------------------------
 *   @file		: sf.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2017-2024 kela Inc
 *   @author		: luochuanrong 
 *   @date		: 2017
 *   @update		:
 *  -------------------------------------------------
 */

class Express_api {
 
        public static function searchOrder($express_order_id,$express_id,$order_sn=null){
            switch($express_id){
                case 4 : //顺丰
                    $file_path = APP_ROOT."shipping/modules/express_api/SfExpress.php";

                    if(!file_exists($file_path))                {
                        //$result['error']='<div class="alert alert-info" style="display: block !important;width:100% !important;">';      
                        $result['result']=0;
                        $result['error'] = '很抱歉,顺丰速运接口文件不存在'.$file_path;
                        return $result;
                    }
                    require_once($file_path);
                    $result=SfExpress::searchOrder($express_order_id);
                    return $result;
                    break;
                case 12 : //圆通
                        $result['result']=0;
                        $result['error'] = '很抱歉,暂不支持该快递电子下单';
                        return $result;                
                    break;
                case 14 : //申通
                        $result['result']=0;
                        $result['error'] = '很抱歉,暂不支持该快递电子下单';
                        return $result; 
                    break;
                case 18 : //顺丰到付
                    $file_path = APP_ROOT."shipping/modules/express_api/SfExpressDF.php";

                    if(!file_exists($file_path))                {
                        //$result['error']='<div class="alert alert-info" style="display: block !important;width:100% !important;">';      
                        $result['result']=0;
                        $result['error'] = '很抱歉,顺丰速运接口文件不存在'.$file_path;
                        return $result;
                    }
                    require_once($file_path);
                    $result=SfExpressDF::searchOrder($express_order_id);
                    return $result;                
                    break;                           
                case 19 : //中通
                    $file_path = APP_ROOT."shipping/modules/express_api/ZtExpress_new.php";
                    if(!file_exists($file_path)){                         
                        $result['result']=0;
                        $result['error'] = '很抱歉,顺丰速运接口文件不存在'.$file_path;
                        return $result;
                    }
                    require_once($file_path);
                    $result=ZtExpress::searchOrder($express_order_id);
                    return $result;  
                        return $result;                
                    break;
                case 41 : //中通
                    /*
                    $file_path = APP_ROOT."shipping/modules/express_api/ZtExpress_new_weipinhui.php";
                    if(!file_exists($file_path)){                         
                        $result['result']=0;
                        $result['error'] = '很抱歉,唯品会指定中通速运接口文件不存在'.$file_path;
                        return $result;
                    }
                    require_once($file_path);
                    $result=ZtExpress::searchOrder($express_order_id);
                    return $result;  
                        return $result;
                    
                    
                    
                    if(empty($order_sn)){
                        $result['result']=0;
                        $result['error'] = '很抱歉,打印面单时不能获取唯品会外部订单号';
                        return $result;                        
                    }   
                    $order_sn = explode(',',$order_sn);        
                    $order_sn =  $order_sn[0];  
                                 
                    require_once KELA_PATH.'/jitx/vipapis/jitx/JitXServiceClient.php';
                    try {
                        $service=\vipapis\jitx\JitXServiceClient::getService();
                        $ctx=\Osp\Context\InvocationContextFactory::getInstance();
                   
                        //$params = json_decode($express_order_id);
                        //$ctx->setAppKey("a876c4cc");
                        //$ctx->setAppSecret("77780A5819EC3CFBE648436DB9F95492");
                        //$ctx->setAppURL("http://sandbox.vipapis.com");              
                        $ctx->setAppKey(VOP_APP_KEY);
                        $ctx->setAppSecret(VOP_APP_SECRET);
                        $ctx->setAppURL("https://gw.vipapis.com");

                        $PrintDetail = new \vipapis\jitx\PrintDetail(
                            array(
                            'order_sn' =>$order_sn,
                            'transport_no' => $express_order_id,
                            'box_no' =>1,
                            'carrier_code' =>'zhongtong',
                            'total_package' =>1,
                            'goods_info' => ['饰品*xx*1']
                            )
                        );
                        $req = new \vipapis\jitx\GetOrderLabelRequest(['vendor_id'=>VOP_VENDOR_ID,'print_details'=>[$PrintDetail]]);


                
                        //echo "<pre>";

                        //var_dump($service->getTransportNos($req));
                        //print_r($service->getTransportNos($req));
                        //$res = $service->getOrderLabel($req);
                        $res = $service->getPrintTemplate($req);
                        //print_r(json_decode($res[0]->order_label));
                        //print_r($res);
                        //echo $res[0]['order_label'];exit();
                        $res_arr = json_decode($res[0]->order_label);

                          //echo "<pre>";
                          //print_r($res_arr);
                          //exit();
                        $res = array();
                        foreach ($res_arr as $key => $v) {
                            $res[$v->fieldCode] = $v->fieldValue;
                        }
                        //print_r($res);
                        return array('result'=>1,'res'=>$res);

                    } catch(\Osp\Exception\OspException $e){
                        //var_dump($e);
                        //echo "<pre>";
                        //print_r($e);
                        //echo $e->getReturnMessage();
                        return array('result'=>0,'error'=>$e->getReturnMessage());
                    }
                    */
                    break;
                case 39 : //跨越速运
                        $result['result']=0;
                        $result['error'] = '很抱歉,暂不支持该快递电子下单';
                        return $result;                 
                    break;
                case 40 : //韵达快递
                        $result['result']=0;
                        $result['error'] = '很抱歉,暂不支持该快递电子下单';
                        return $result;                 
                    break;                                              
            }  
        }

        public static function makeOrder($express_order_id,$express_id,$data){
            if(empty($express_order_id) || empty($express_id) || empty($data)){
                    $result['result']=0;
                    $result['error'] = '传入下单参数异常';
                    return $result;                
            }
            switch($express_id){
                case 4 : //顺丰
                    $file_path = APP_ROOT."shipping/modules/express_api/SfExpress.php";

                    if(!file_exists($file_path))                {
                        //$result['error']='<div class="alert alert-info" style="display: block !important;width:100% !important;">';      
                        $result['result']=0;
                        $result['error'] = '很抱歉,顺丰速运接口文件不存在'.$file_path;
                        return $result;
                    }
                    require_once($file_path);
                    $result=SfExpress::makeOrder($express_order_id,$data);
                    return $result;
                    break;
                case 12 : //圆通
                        $result['result']=0;
                        $result['error'] = '很抱歉,暂不支持该快递电子下单';
                        return $result;                
                    break;
                case 14 : //申通
                        $result['result']=0;
                        $result['error'] = '很抱歉,暂不支持该快递电子下单';
                        return $result;
                    break;
                case 18 : //顺丰到付
                    $file_path = APP_ROOT."shipping/modules/express_api/SfExpressDF.php";

                    if(!file_exists($file_path))                {
                        //$result['error']='<div class="alert alert-info" style="display: block !important;width:100% !important;">';      
                        $result['result']=0;
                        $result['error'] = '很抱歉,顺丰速运接口文件不存在'.$file_path;
                        return $result;
                    }
                    require_once($file_path);
                    $result=SfExpressDF::makeOrder($express_order_id,$data);
                    return $result;                 
                    break;                           
                case 19 : //中通
                    $file_path = APP_ROOT."shipping/modules/express_api/ZtExpress_new.php";
                    if(!file_exists($file_path)){                         
                        $result['result']=0;
                        $result['error'] = '很抱歉,顺丰速运接口文件不存在'.$file_path;
                        return $result;
                    }
                    require_once($file_path);
                    $result=ZtExpress::makeOrder($express_order_id,$data);
                    return $result;                           
                    break;
                case 22 : //京东快递
                        $result['result']=0;
                        $result['error'] = '很抱歉,暂不支持该快递电子下单';
                        return $result;                
                    break;
                case 39 : //跨越速运
                        $result['result']=0;
                        $result['error'] = '很抱歉,暂不支持该快递电子下单';
                        return $result;                
                    break;
                case 41 : //唯品会指定中通
                   /*
                   $file_path = APP_ROOT."shipping/modules/express_api/ZtExpress_new_weipinhui.php";
                    if(!file_exists($file_path)){                         
                        $result['result']=0;
                        $result['error'] = '很抱歉,唯品会指定中通接口文件不存在'.$file_path;
                        return $result;
                    }
                    require_once($file_path);
                    $result=ZtExpress::makeOrder($express_order_id,$data);
                    return $result;
                    
                    if(empty($data['out_order_sn'])){
                        $result['result']=0;
                        $result['error'] = '很抱歉,电子面单下单时不能获取唯品会外部订单号';
                        return $result;                        
                    }   
                    $order_sn = explode(',',$data['out_order_sn']);        
                    $order_sn =  $order_sn[0];                     
                    require_once KELA_PATH.'/jitx/vipapis/jitx/JitXServiceClient.php';
                    try {
                        $service=\vipapis\jitx\JitXServiceClient::getService();
                        $ctx=\Osp\Context\InvocationContextFactory::getInstance();
                        
                        $ctx->setAppKey(VOP_APP_KEY);
                        $ctx->setAppSecret(VOP_APP_SECRET);
                        $ctx->setAppURL("https://gw.vipapis.com");    
                        
                        
                        //$ctx->setAppKey("a876c4cc");
                        //$ctx->setAppSecret("77780A5819EC3CFBE648436DB9F95492");
                        //$ctx->setAppURL("http://sandbox.vipapis.com");
                        
                        //$ctx->setAccessToken("83435BBB7E69CFAA10517BCE5B8C0A37FBC17DD1");  

                        //$ctx->setServiceName("vipapis.jitx.JitXService");   
                        //$ctx->setMethod("getTransportNos");     
                        //$ctx->setCallerVersion("1.0.0");
                        //$ctx->setTimeOut(30); 
                        //$ctx->setAccessToken("your accessToken if you need");
                        //$ctx->setLanguage("the language code if you need");
                        $req = new \vipapis\jitx\GetTransportNosRequest(['vendor_id'=>VOP_VENDOR_ID,'order_sn'=>$order_sn,'carrier_code'=>'zhongtong']);
                        //echo "<pre>";

                        //var_dump($service->getTransportNos($req));
                        //print_r($service->getTransportNos($req));
                        $res = $service->getTransportNos($req);
                        return array('result'=>1,'express_no'=>$res[0],'express_order_id'=>$res[0]);
                    } catch(\Osp\Exception\OspException $e){
                        //var_dump($e);
                        //echo "<pre>";
                        //print_r($e);
                        //echo $e->getReturnMessage();
                        return array('result'=>0,'error'=>$e->getReturnMessage());
                    }  
                    */
                    break;
                default:
                        $result['result']=0;
                        $result['error'] = '很抱歉,暂不支持该快递电子下单';
                        return $result;                
                break;                                                 
            }     

        }            
}

?>