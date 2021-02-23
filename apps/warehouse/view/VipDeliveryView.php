<?php
/**
 *  -------------------------------------------------
 *   @file		: VipDeliveryView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @date		: 2017-06-26 21:17:07
 *   @update	:
 *  -------------------------------------------------
 */
class VipDeliveryView extends View
{
    public function __construct($obj){
        parent::__construct($obj);       
    }
    
    //获取仓库列表
    public static function getWarehouseList(){
        $warehouse = array(
            'VIP_NH'=>array('value'=>1,'name'=>'南海仓'),
            'VIP_SH'=>array('value'=>2,'name'=>'上海仓'),
            'VIP_CD'=>array('value'=>3,'name'=>'成都仓'),
            'VIP_BJ'=>array('value'=>4,'name'=>'北京仓'),
            'VIP_HZ'=>array('value'=>5,'name'=>'鄂州仓'),
            'VIP_HH'=>array('value'=>7,'name'=>'花海仓'),
            'VIP_ZZ'=>array('value'=>8,'name'=>'郑州'),
            'VIP_SE'=>array('value'=>9,'name'=>'首尔'),
            'VIP_JC'=>array('value'=>10,'name'=>'白云'),
            'VIP_DA'=>array('value'=>11,'name'=>'唯品团'),
            'VIP_MRC'=>array('value'=>12,'name'=>'唯品卡'),
            'VIP_ZZKG'=>array('value'=>13,'name'=>'郑州空港'),
            'VIP_GZNS'=>array('value'=>14,'name'=>'广州南沙'),
            'VIP_CQKG'=>array('value'=>15,'name'=>'重庆空港'),
            'VIP_SZGY'=>array('value'=>16,'name'=>'苏州工业'),
            'VIP_FZPT'=>array('value'=>17,'name'=>'福州平潭'),
            'VIP_QDHD'=>array('value'=>18,'name'=>'青岛黄岛'),
            'HT_GZZY'=>array('value'=>19,'name'=>'广州中远'),
            'HT_GZFLXY'=>array('value'=>20,'name'=>'富力心怡仓'),
            'VIP_NBJCBS'=>array('value'=>21,'name'=>'机场保税仓'),
            'HT_NBYC'=>array('value'=>22,'name'=>'云仓代运营'),
            'HT_HZHD'=>array('value'=>23,'name'=>'杭州航都仓'),
            'HT_JPRT'=>array('value'=>24,'name'=>'日本日通仓'),
            'HT_AUXNXY'=>array('value'=>25,'name'=>'悉尼心怡仓'),
            'HT_USALATM'=>array('value'=>26,'name'=>'洛杉矶天马仓'),
            'HT_USANYTM'=>array('value'=>27,'name'=>'纽约天马仓'),
            'HT_SZQHBH'=>array('value'=>28,'name'=>'前海保宏仓'),
            'FJFZ'=>array('value'=>29,'name'=>'福建福州仓'),
            'PJ_ZJHZ'=>array('value'=>30,'name'=>'杭州仓'),
            'HNZZ'=>array('value'=>31,'name'=>'郑州小仓'),
            'SXXA'=>array('value'=>32,'name'=>'西安小仓'),
            'LNSY'=>array('value'=>33,'name'=>'沈阳小仓'),
            'YNKM'=>array('value'=>34,'name'=>'昆明小仓'),
            'GZGY'=>array('value'=>35,'name'=>'贵阳前置仓'),
            'NMGHHHT'=>array('value'=>36,'name'=>'内蒙古前置仓'),
            'SDJN'=>array('value'=>37,'name'=>'济南前置仓'),
            'XJWLMQ'=>array('value'=>38,'name'=>'新疆前置仓'),
            'HLJHEB'=>array('value'=>39,'name'=>'黑龙江哈尔滨前置仓'),
            'GXNN'=>array('value'=>40,'name'=>'广西南宁前置仓'),
            'SXTY'=>array('value'=>41,'name'=>'山西太原前置仓'),
            'AHHF'=>array('value'=>42,'name'=>'安徽合肥前置仓'),
            'VIP_HK'=>array('value'=>43,'name'=>'香港自营仓'),
            'VIP_TYO'=>array('value'=>44,'name'=>'日本东京自营仓'),
            'VIP_NYC'=>array('value'=>45,'name'=>'美国纽约自营仓'),
            'VIP_PAR'=>array('value'=>46,'name'=>'法国巴黎自营仓'),
            'VIP_SEL'=>array('value'=>47,'name'=>'韩国首尔自营仓'),
            'VIP_SYD'=>array('value'=>48,'name'=>'澳大利亚悉尼运自营仓'),
            'VIP_LON'=>array('value'=>49,'name'=>'英国伦敦自营仓'),
            'VIP_FRA'=>array('value'=>50,'name'=>'德国法兰克福自营仓'),
            'VIP_MIL'=>array('value'=>51,'name'=>'意大利米兰自营仓'),
            'VIP_SY'=>array('value'=>52,'name'=>'东北仓'),
            'VIP_KTNH'=>array('value'=>53,'name'=>'华南客退RDC仓'),
            'VIP_XA'=>array('value'=>54,'name'=>'西北仓'),
            
        );
        foreach ($warehouse as $key=>$vo){
            $vo['code'] = $key;
            $warehouse[$key] = $vo;
        }
        return $warehouse;
    } 
    
    public static function getWarehouseInfo($code){
        $warehouse = self::getWarehouseList();
        if(isset($warehouse[$code])){
            return $warehouse[$code];
        }else{
            return array();
        }
    }   
    //获取仓库名称
    public static function getWarehouseName($code){
        $warehouse = self::getWarehouseList();
        if(isset($warehouse[$code])){
            return $warehouse[$code]['name'];
        }else{
            return $code;
        }
    }
    public static function getWarehouseValue($code){
        $warehouse = self::getWarehouseList();
        if(isset($warehouse[$code])){
            return $warehouse[$code]['value'];
        }else{
            return $code;
        }
    }

    public static function getDeliveryMethodList(){
        return array(
            '2'=>array(
                'name'=>'空运',
                'deliveryHours'=>array('12:00:00','15:00:00','16:00:00','20:00:00','22:00:00'),
                'arrivalHours'=>array('09:00:00','16:00:00','18:00:00','23:59:00')
            ),
            '1'=>array(
                'name'=>'汽运',
                'deliveryHours'=>array('12:00:00','15:00:00','20:00:00','22:00:00'),
                'arrivalHours'=>array('12:00:00','15:00:00','20:00:00','22:00:00','23:59:00'),
            )            
        );
    }
    public static function getDeliveryMethodName($code){
       $deliveryMethodList = self::getDeliveryMethodList();
       if(isset($deliveryMethodList[$code])){
           return $deliveryMethodList[$code]['name'];
       }else{
           return $code;
       }
   }
   public static function getDeliveryStatusList(){
       return array('0'=>'未出仓','1'=>'已出仓');
   }
   public static function getDeliveryStatusName($code){
       $code = empty($code)?0:$code;
       $deliveryStatusList = self::getDeliveryStatusList();
       if(isset($deliveryStatusList[$code])){
           return $deliveryStatusList[$code];
       }else{
           return $code;
       }
   }
   public function getCarrierList(){
       return $this->_model->apiGetCarrierList();
   }
   
   public  static  function getDeliveryTimeList(){       
       $datetimes = array();      
       $dates = array(
           date('Y-m-d'),
           date("Y-m-d",strtotime("+1 day"))           
       );
       $deliveryMethodList = self::getDeliveryMethodList();
       foreach ($dates as $date){
           foreach ($deliveryMethodList as $i=>$method){
               foreach ($method['deliveryHours'] as $hour){
                  $datetimes[$i][] = $date." ".$hour;
               }
           }
       }
       return $datetimes;       
   }
   public static  function getArrivalTimeList(){
        
       $datetimes = array();
       $dates = array(
           date('Y-m-d'),
           date("Y-m-d",strtotime("+1 day"))
       );
       $deliveryMethodList = self::getDeliveryMethodList();
       foreach ($dates as $date){
           foreach ($deliveryMethodList as $i=>$method){
               foreach ($method['arrivalHours'] as $hour){
                   $datetime = $date." ".$hour;
                   if(date("Y-m-d H:i")>=$datetime) {
                       continue;
                   }
                   $datetimes[$i][] = $datetime;
               }
           }
       }
       return $datetimes;
        
   }
   
   //唯品会发货地址
   public static function getAddressList(){
       $address = array();
       $address['华北仓']=array(
           'region'=>'华北仓',           
           'consignee_id'=>'0',           
           'consignee'=>'华北入库部',
           'express_id'=>39,
           'express_name'=>'跨越速运',
           'country_id'=>1,
           'province_id'=>27,
           'city_id'=>343,
           'regional_id'=>2925,
           'address'=>'天津市武清区崔黄口镇宏光路宏达道20号唯品会华北物流中心',
           'tel' =>'022-82209110',
           'warehouses'=>array(
               'VIP_BJ'
           ),
       );
       $address['华东仓']=array(
           'region'=>'华东仓',
           'consignee_id'=>'3314571',
           'consignee'=>'华东仓收货部',
           'express_id'=>39,
           'express_name'=>'跨越速运',
           'country_id'=>1,
           'province_id'=>0,//江苏
           'city_id'=>0,//苏州
           'regional_id'=>0,//昆山
           'address'=>'浙江省湖州市吴兴区杨家埠镇街道八字桥北唯品会华东运营总部',
           'tel' =>'0512-36827574',
           'warehouses'=>array(
               'VIP_SH'
           ),
       );
       $address['西北仓'] = array(
           'region'=>'西北仓',
           'consignee_id'=>'33142620',           
           'consignee'=>'王彦',
           'express_id'=>39,
           'express_name'=>'跨越速运',
           'country_id'=>1,
           'province_id'=>24,//陕西省
           'city_id'=>311,//西安
           'regional_id'=>2608,//高陵县
           'address'=>'陕西省西安市高陵区唯品会西北物流中心',
           'tel' =>'13519814560',
           'warehouses'=>array(
               'VIP_XA'
           ),
       );
       $address['西南仓'] = array(
           'region'=>'西南仓',
           'consignee_id'=>'3314262',
           'consignee'=>'西南物流中心收货组',//马炳阳
           'express_id'=>39,
           'express_name'=>'跨越速运',
           'country_id'=>1,
           'province_id'=>26,//四川
           'city_id'=>340,//资阳
           'regional_id'=>2896,//简阳市
           'address'=>'四川省成都市简阳市石桥镇皂角村成简快速通道C段西侧唯品会',
           'tel' =>'028-27985531/18980383156',//028-27985500/18190380113/
           'warehouses'=>array(
               'VIP_CD'
           ),
       );
       $address['华南仓'] = array(
           'region'=>'华南仓',
           'consignee_id'=>'3314231',           
           'consignee'=>'华南仓收货部',
           //'express_id'=>4,
           //'express_name'=>'顺丰速运',           
           'express_id'=>39,
           'express_name'=>'跨越速运',
           'country_id'=>1,
           'province_id'=>6,//广东
           'city_id'=>94,//肇庆
           'regional_id'=>837,//肇庆市
           'address'=>'广东省肇庆市高新开发区(大旺)北江大道与亚铝大街交汇处唯品会华南物流中心',
           'tel' =>'0758-8992531',
           'warehouses'=>array(
               'VIP_GZ','VIP_NH'
           ),
       );
       $address['华中仓'] =array(
           'region'=>'华中仓',
           'consignee_id'=>'3314244',
           'consignee'=>'华中仓收货部',
           'express_id'=>39,
           'express_name'=>'跨越速运',
           'country_id'=>1,
           'province_id'=>13,//湖北
           'city_id'=>182,//鄂州
           'regional_id'=>1560,//华容区
           'address'=>'湖北省鄂州市葛店经济开发区人民路唯品会',           
           'tel' =>'0711-3819618',
           'warehouses'=>array(
               'VIP_HZ','VIP_WH','VIP_HB'
           ),
       );
       $address['花海仓'] = array(
           'region'=>'花海仓',
           'consignee_id'=>'3270924',
           'consignee'=>'张伟',
           'express_id'=>39,
           'express_name'=>'跨越速运',
           'country_id'=>1,
           'province_id'=>6,//广东省
           'city_id'=>94,//肇庆
           'regional_id'=>837,//肇庆市
           'address'=>'广东省肇庆市大旺高新区亚铝大街(亚铝二号门)唯品会13栋4号门',
           'tel' =>'13025574107',
           'warehouses'=>array(
               'VIP_HH'
           ),
       );
       $address['东北仓'] = array(
           'region'=>'东北仓',
           'consignee_id'=>'3270924',
           'consignee'=>'东北RDC预约室',
           'express_id'=>39,
           'express_name'=>'跨越速运',
           'country_id'=>1,
           'province_id'=>18,//辽宁
           'city_id'=>244,//沈阳
           'regional_id'=>2068,//于洪区
           'address'=>'辽宁省沈阳市于洪区G102与四环交叉口A1库A2库A3库',
           'tel' =>'024-25993389',
           'warehouses'=>array(
               'VIP_SY'
           ),
       );
       return $address;
   
   }
   //获取数组格式 配送地址
   public static function getAddressInfo($region){
       $addressList = self::getAddressList();
       if(isset($addressList[$region])){
           return $addressList[$region];
       }else{
           return $region;
       }
   }
   //获取 字符串格式 配送地址
   public static function getAddressName($region){
       $address = self::getAddressInfo($region);
       if(!empty($address)){
           return $region."：".$address['address']."   ".$address['consignee']."  ".$address['tel'];
       }else{
           return $region;
       }
   }
   /**
    * 根据仓库编号，获取发货地址信息
    * @param unknown $warehouse
    * @return multitype:string multitype:string
    */
   public static function getAddressInfoByWarehoue($warehouse){
       $addressList = self::getAddressList();
       foreach ($addressList as $vo){
           if(in_array($warehouse,$vo['warehouses'])){
               return $vo;
               break;
           }
       }
   } 
   
    
}
?>