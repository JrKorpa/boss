<?php

/*
 *  -------------------------------------------------
 *   @file      : sf.class.php
 *   @link      :  www.kela.cn
 *   @copyright : 2017-2024 kela Inc
 *   @author        : luochuanrong 
 *   @date      : 2017
 *   @update        :
 *  -------------------------------------------------
 */
include ("zt/ZopClient.php");
include ("zt/ZopProperties.php");
include ("zt/ZopRequest.php");

use zop\ZopClient;
use zop\ZopProperties;
use zop\ZopRequest;

class ZtExpress {
        
        const  _COMPANY_ID ='00ea5a6992f241d2bbdd0eef1af7bb0a';     //测试： 'ea8c719489de4ad0bf475477bad43dc6';//合作商编码
        const _KEY ='9cfab89cd703'; //合作商key        
        const _PARTNER = '00ea5a6992f241d2bbdd0eef1af7bb0a';//商家ID  测试test
        const _VERIFY = 'O41EMCVQ';
        const _URL = 'http://japi.zto.cn/submitOrderCode';
        const _DATOUBI   = 'http://japi.zto.cn/bagAddrMarkGetmark';
        const _MONEY_URL = ' http://japi.zto.cn/queryAvailableBalance';
        /*
        const  _COMPANY_ID ='kfpttestCode'; 
        const _KEY ='kfpttestkey=='; //合作商key
        const _PARTNER = 'test';
        const _VERIFY = 'ZTO123';
        const _URL = 'http://58.40.16.120:9001/submitOrderCode'; //'http://58.40.16.125:9001/gateway.do';//快递类服务接口url
        */
        const _J_CITY ='广东省,深圳市';
        
        /**
         *  构造函数
         *
         */
        function __construct() {
               
        }


        public static function searchOrder($order_id){
                $expresslistmodel=new ExpressListModel($order_id,43);
                $express=$expresslistmodel->getRow($order_id);
                //var_dump($expresslistmodel);
                $data=array();
                $data['j_contact']= EXPRESS_J_CONTACT;        
                $data['j_tel']= EXPRESS_J_TEL;
                $data['j_address']= EXPRESS_J_ADDRESS;        

                $data['d_contact']= $express['d_contact'];
                $data['d_tel']= $express['d_tel'];
                $data['province']=$express['province'];
                $data['city']=$express['city'];
                $data['district']=$express['district'];
                $data['d_address']= $express['address'];
                //print_r($data);exit();
                return self::makeOrder($order_id,$data);
             
        }

        public static function makeOrder($order_id,$param){             
                $key=self::_KEY;
                $content=array();                
                $content['id']=  $order_id;
                $content['typeid']=1;  
    
                $content['sender']['name']= $param['j_contact'];        
                $content['sender']['mobile']= $param['j_tel'];
                $content['sender']['phone']=  $param['j_tel'];        
                $content['sender']['city']= self::_J_CITY;
                $content['sender']['address']= $param['j_address'];
                $content['receiver']['name']= $param['d_contact'];
                $content['receiver']['mobile']= $param['d_tel'];
                $content['receiver']['phone']= $param['d_tel'];
                $city = '';
                if(!empty($param['province']))
                    $city = $city . $param['province'];
                if(!empty($param['city']))
                    $city = $city .','. $param['city'];
                if(!empty($param['district']))
                    $city = $city .','. $param['district']; 

                $content['receiver']['city']= !empty($city) ? $city : mb_substr($param['d_address'],0,6);
                $content['receiver']['address']= $param['d_address'];


                $province=isset($param['province']) ? $param['province'] : '';
                $city=isset($param['city']) ? $param['city'] : '';
                $district=isset($param['district']) ? $param['district'] : '';
                $d_address=isset($param['d_address']) ? $param['d_address'] : '';
                $datoubi=self::getDaTouBi($order_id,$province,$city,$district,$d_address);               
                if(!is_array($datoubi)){
                    return array('result'=>0,'error'=>$datoubi);
                }
                $tocode ='';
                if(is_array($datoubi) && $datoubi['status']==true && $datoubi['message']=='成功' && is_array($datoubi['result']) && !empty($datoubi['result']['mark'])){
                    $tocode=$datoubi['result']['mark']; 
                }else{
                    return array('result'=>0,'error'=>'大头笔接口返回数据异常,可能地址不能识别');
                }

                //exit();
                $data = array();
                $data['partner'] = self::_PARTNER;
                $data['datetime'] = date('Y-m-d H:i:s',time());
                $data['verify'] = self::_VERIFY;
                $data['content'] = $content;
                $data = json_encode($data);
                 
                $properties = new ZopProperties(self::_COMPANY_ID, self::_KEY);
                $client = new ZopClient($properties);
                $request = new ZopRequest();
                $request->setUrl(self::_URL);
                $request->addParam("data", $data);

                $res = $client->execute($request); 
                //print_r($res);               
                $res =json_decode($res,true);
                //echo "<pre>";
                //print_r($res);
                //exit();     


                if($res['message']=='TRUE' && isset($res['data']) && is_array($res['data'])  && in_array($res['data']['message'],array("操作成功","单号获取成功")))
                        return array('result'=>1,'express_no'=>$res['data']['billCode'],'express_order_id'=>$res['data']['orderId'],'destcode'=>$res['data']['siteName'],'datoubi'=>$tocode); 
                elseif($res['message']==='FALSE' && isset($res['data']) && is_array($res['data']))
                        return array('result'=>0,'error'=>$res['data']['message']);
                elseif(!empty($res['message']))
                        return array('result'=>0,'error'=>$res['message']);
                else     
                        return array('result'=>0,'error'=>'接口异常');

             
        }


        public static function post($url,$body) 
        { 
             $curlObj = curl_init();
             curl_setopt($curlObj, CURLOPT_URL, $url); // 设置访问的url
             curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1); //curl_exec将结果返回,而不是执行
             curl_setopt($curlObj, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded;charset=UTF-8"));
             curl_setopt($curlObj, CURLOPT_URL, $url);
             curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, FALSE);
             curl_setopt($curlObj, CURLOPT_SSL_VERIFYHOST, FALSE);
             curl_setopt($curlObj, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
            
             curl_setopt($curlObj, CURLOPT_CUSTOMREQUEST, 'POST');      
            
             curl_setopt($curlObj, CURLOPT_POST, true);
             curl_setopt($curlObj, CURLOPT_POSTFIELDS, http_build_query($body));       
             curl_setopt($curlObj, CURLOPT_ENCODING, 'gzip');

             $res = curl_exec($curlObj);
             //var_dump($res);
             curl_close($curlObj);

             if ($res === false) {
                   $errno = curl_errno($curlObj);
                   if ($errno == CURLE_OPERATION_TIMEOUTED) {
                       $msg = "Request Timeout:   seconds exceeded";
                   } else {
                       $msg = curl_error($curlObj);
                   }
                   echo $msg;
                   $e = new XN_TimeoutException($msg);           
                   throw $e;
               } 
            return $res;
        }

        public static function getDaTouBi($order_id,$province,$city,$district,$address){
            //echo $address;   
                $data=array();
                $data['unionCode']=$order_id;
                $data['send_province']="广东省";//self::_J_CITY;
                $data['send_city']='深圳市';
                $data['send_district']="龙岗区";
                $data['send_address']=EXPRESS_J_ADDRESS;           
                if(empty($province) || empty($city) || empty($district)){ 
                    $data['receive_province']= !empty($province) ? $province : $address;
                    $data['receive_city']= !empty($city) ? $city : $address;
                    $data['receive_district']= !empty($district) ? $district : $address;                
                    /*      
                    $sheng='{
                            "message": "",
                            "result": [{
                                "code": 150000,
                                "fullName": "内蒙古",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 210000,
                                "fullName": "辽宁",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 320000,
                                "fullName": "江苏",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 330000,
                                "fullName": "浙江",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 340000,
                                "fullName": "安徽",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 220000,
                                "fullName": "吉林",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 230000,
                                "fullName": "黑龙江",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 130000,
                                "fullName": "河北",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 140000,
                                "fullName": "山西",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 460000,
                                "fullName": "海南",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 510000,
                                "fullName": "四川",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 350000,
                                "fullName": "福建",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 360000,
                                "fullName": "江西",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 370000,
                                "fullName": "山东",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 410000,
                                "fullName": "河南",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 420000,
                                "fullName": "湖北",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 430000,
                                "fullName": "湖南",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 440000,
                                "fullName": "广东",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 450000,
                                "fullName": "广西",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 540000,
                                "fullName": "西藏",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 610000,
                                "fullName": "陕西",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 620000,
                                "fullName": "甘肃",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 630000,
                                "fullName": "青海",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 640000,
                                "fullName": "宁夏",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 650000,
                                "fullName": "新疆",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 520000,
                                "fullName": "贵州",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 530000,
                                "fullName": "云南",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 830000,
                                "fullName": "台湾",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 810000,
                                "fullName": "香港特别行政区",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 820000,
                                "fullName": "澳门特别行政区",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 120000,
                                "fullName": "天津",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 110000,
                                "fullName": "北京",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },
                            {
                                "code": 310000,
                                "fullName": "上海",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            },   
                            {
                                "code": 500000,
                                "fullName": "重庆",
                                "mark": "",
                                "outofrange": 0,
                                "printMark": ""
                            }],
                            "status": true,
                            "statusCode": "R5"
                        }';

                        $r_sheng=array();
                        $r_city=array();
                        $r_xian=array();
                        $sheng=json_decode($sheng,true);
                        //print_r($sheng['result']);
                        foreach ($sheng['result'] as $k => $v) {                    
                            if(strpos(mb_substr($address,0,6,'utf-8'),$v['fullName'])!==false){
                                $r_sheng=$v;
                                $url="http://japi.zto.cn/zto/api_utf8/baseArea?msg_type=GET_AREA&data=".$v['code'];
                                //echo $url;
                                $citys=self::post($url,array());
                                $citys=json_decode($citys,true);
                                //print_r($citys);
                                break;
                            }else{
                                
                                //return false;
                            }
                        }
                        if(empty($r_sheng)){
                            return "地址:".$address." 省份名称不规范找不到省名.";
                        }
                        foreach ($citys['result'] as $k => $v) {                    
                            if(strpos($address,$v['fullName'])!==false){
                                $r_city=$v;
                                $url="http://japi.zto.cn/zto/api_utf8/baseArea?msg_type=GET_AREA&data=".$v['code'];
                                //echo $url;
                                $xians=self::post($url,array());
                                $xians=json_decode($xians,true);
                                //print_r($citys);
                                break;
                            }else{
                                
                                //return false;
                            }
                        }                
                        if(empty($r_city)){
                            return "地址:".$address ."[".$r_sheng['fullName']."->]城市名称不规范找不到城市名.";
                        }
                        foreach ($xians['result'] as $k => $v) {                    
                            if(strpos($address,$v['fullName'])!==false){
                                $r_xian=$v;                        
                                break;
                            }else{
                                
                                //return false;
                            }
                        }  
                        if(empty($r_xian)){
                            //return "地址:".$address." 县区名称不规范找不到县区名.";
                            $r_xian['fullName']='';
                        }
                        $data['receive_province']=$r_sheng['fullName'];
                        $data['receive_city']=$r_city['fullName'];
                        $data['receive_district']=$r_xian['fullName']; 
                        */                       
                }else{

                        $data['receive_province']=$province;
                        $data['receive_city']=$city;
                        $data['receive_district']=$district;

                }


                $data['receive_address']=$address;
                $properties = new ZopProperties(self::_COMPANY_ID, self::_KEY);
                $client = new ZopClient($properties);
                $request = new ZopRequest();
                $request->setUrl(self::_DATOUBI);
                $data = json_encode($data);
                $request->addParam("company_id", self::_COMPANY_ID);
                $request->addParam("msg_type", 'GETMARK');
                $request->addParam("data", $data);

                $res = $client->execute($request); 
    
                //print_r($res);               
                $res=json_decode($res,true);
                                
                return $res;     

        }

        public static function getMoneys(){
                $key=self::_KEY;
                $content = array();
                $content['lastNo']=''; 
                $content['typeId']='';
                $data = array();
                $data['partner'] = self::_PARTNER;
                $data['datetime'] = date('Y-m-d H:i:s',time());
                $data['verify'] = self::_VERIFY;
                $data['content'] = $content;
                $data = json_encode($data);                
                $properties = new ZopProperties(self::_COMPANY_ID, self::_KEY);
                $client = new ZopClient($properties);
                $request = new ZopRequest();
                $request->setUrl(self::_MONEY_URL);
                $request->addParam("data", $data);

                $res = $client->execute($request); 
                //print_r($res);               
                $res =json_decode($res,true); 
                if(is_array($res) && $res['data']['available']<30000){
                     $email=new PHPMailer();
                     $content='中通快递目前余额为:'.$res['data']['available'];
                     $email->send_mail('郭伟','guowei@kela.cn','中通快递余额提醒',$content);
                }    
                
        }

      
}

?>