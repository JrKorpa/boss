<?php
/*
*  --------------彩钻接口-----------------------
*   Date   :2014/6/10 星期二
*  -------------------------------------------------
*/
//http://vm.newsite.com/api/diamond_color_dia_leibish.php
define('IN_ECS', true);
set_time_limit(0);
define('ROOT_PATH', str_replace('cron/diamond_color/diamond_color_dia_leibish.php', '', str_replace('\\', '/', __FILE__)));
//require(ROOT_PATH . "data/config.php");               //数据库配置
//require(ROOT_PATH . "includes/cls_mysql1.php");       //数据对象
date_default_timezone_set('Asia/Shanghai');//设置时区
 

// $url = "http://www.leibish.com/api/stock_export/kela/?user=kela&pwd=yaniv2014&limit=10";
 //$url = "http://www.leibish.com/api/stock_export/kela/hourly/15?user=kela&pwd=yaniv2014";
// $url = "http://www.leibish.com/api/stock_export/kela/?user=kela&pwd=yaniv2014";
   $url =  "http://www.leibish.com/api/stock_export/kela/?user=kela&pwd=yaniv2014";
   // $url = "leibish20150827.xml";
    error_reporting(E_ALL);
//指定目录
        /*$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);
        // 4. 释放curl句柄
        curl_close($ch);*/
        $output = file_get_contents($url);
       // var_dump($output);die;

    //读取数据
    //$conn=mysqli_connect('192.168.0.95','cuteman','QW@W#RSS33#E#','front') or die("数据库连接失败！") ;
        $conn=mysqli_connect('192.168.1.59','cuteman','QW@W#RSS33#E#','front') or die("数据库连接失败！") ;
    //      $conn=mysqli_connect('192.168.10.222','root','123456','front') or die("数据库连接失败！") ;
        //获取数据

        $data_list_obj = simplexml_load_string($output);
        if($data_list_obj == false) exit('xml载入对象失败！');
//var_dump($data_list_obj);die;
        foreach($data_list_obj->STOCK->PRODUCT as $key => $val){
               $data_list[] = objectToArray($val);
               // break;
         }

  	//删除数据的时候只能删除来自leibish的数据from_ad ='leibish',不能用truncate.所有彩钻数据都是存储在一张表中
        $sql = "delete from `app_diamond_color` where from_ad = '2'";
        if(!mysqli_query($conn,$sql)){
        	echo 'leibish数据删除失败';exit;
        }

    foreach($data_list as $key=>$val)
    {
       //丽比诗ID
    	 $goods_sn = "";
        if(!empty($val['ID'])){
            if(strpos($val['ID'],'diamond') === 0){
                    //过滤成品
                $goods_sn =  $val['ID'];
            }else{
                continue;
            }
        }
        //丽比诗编号
         $goods_id = "";
        if(!empty($val['SKU_NUMBER'])){

                $goods_id =  $val['SKU_NUMBER'];
        }

        
        $price_status = "";
        if(!empty($val['PRICE_STATUS'])){

                $price_status =  $val['PRICE_STATUS'];
        }
    

        $secondary_hue = "";
        if(!empty($val['SECONDARY_HUE'])){

                $secondary_hue =  $val['SECONDARY_HUE'];
        }

        $white_color = "";
        if(!empty($val['WHITE_COLOR'])){

                $white_color =  $val['WHITE_COLOR'];
        }

        $argyle_color = "";
        if(!empty($val['ARGYLE_COLOR'])){

                $argyle_color =  $val['ARGYLE_COLOR'];
        }

        $pair = "";
        if(!empty($val['PAIR'])){

                $pair =  $val['PAIR'];
        }

        $jewerly_type = "";
        if(!empty($val['jewerly_type'])){

                $jewerly_type =  $val['JEWERLY_TYPE'];
        }

        $jewerly_sub_type = "";
        if(!empty($val['JEWERLY_SUB_TYPE'])){

                $jewerly_sub_type =  $val['JEWERLY_SUB_TYPE'];
        }

        $metal = "";
        if(!empty($val['METAL'])){

                $metal =  $val['METAL'];
        }
        $metal_color = "";
        if(!empty($val['METAL_COLOR'])){

                $metal_color =  $val['METAL_COLOR'];
        }

        $metal_weight = "";
        if(!empty($val['METAL_WEIGHT'])){

                $metal_weight =  $val['METAL_WEIGHT'];
        }
        $entery_type = "";
        if(!empty($val['ENTERY_TYPE'])){

                $entery_type =  $val['ENTERY_TYPE'];
        }


        $earback_type = "";
        if(!empty($val['EARBACK_TYPE'])){

                $earback_type =  $val['EARBACK_TYPE'];
        }

        $ring_size = "";
        if(!empty($val['RING_SIZE'])){

                $ring_size =  $val['RING_SIZE'];
        }


        $side_weight = "";
        if(!empty($val['SIDE_WEIGHT'])){

                $side_weight =  $val['SIDE_WEIGHT'];
        }


        $jewelry_width = "";
        if(!empty($val['JEWELRY_WIDTH'])){

                $jewelry_width =  $val['JEWELRY_WIDTH'];
        }


        $chain_length = "";
        if(!empty($val['CHAIN_LENGTH'])){

                $chain_length =  $val['CHAIN_LENGTH'];
        }


        $length = "";
        if(!empty($val['LENGTH'])){

                $length =  $val['LENGTH'];
        }



        $jewelry_stones_details = "";
        if(!empty($val['JEWELRY_STONES_DETAILS'])){

                $jewelry_stones_details =  $val['JEWELRY_STONES_DETAILS'];
        }



        $price_per_karat = "";
        if(!empty($val['PRICE_PER_KARAT'])){

                $price_per_karat =  $val['PRICE_PER_KARAT'];
        }

    	$shape = '';
    	if(!empty($val['SHAPE'])){
    		$shape = $val['SHAPE'];
    	}
    
    	$carat=0;
    	if(!empty($val['WEIGHT'])){
    		$carat = $val['WEIGHT'];
    	}
    	//颜色
    	$color='';
    	if(!empty($val['MAIN_COLOR'])){
    		$color=$val['MAIN_COLOR'];
    	}
    
    	//处理净度
    	$clarity='';
    	if(!empty($val['CLARITY']))
    	{
    		$clarity = $val['CLARITY'];
    		$clarity =str_replace('(','',$clarity);
    		$clarity =str_replace(')','',$clarity);
    		
    	}
    
    	//处理抛光
    	$polish = '';
    	if(!empty($val['POLISH']))
    	{
    		$polish = $val['POLISH'];
    	}
    
    	//处理对称
    	$symmetry = '';
    	if(!empty($val['SYMMETRY']))
    	{
    		$symmetry = $val['SYMMETRY'];
    	}
    	//处理荧光
    	$fluorescence = '';
    	if(!empty($val['FLUORESCENCE']))
    	{
    		$fluorescence = $val['FLUORESCENCE'];
    	}
    	//处理高度
    	$height='';
    	if(!empty($val['HEIGHT']))
    	{
    		$height = $val['HEIGHT'];
    	}
    	//处理宽度
    	$width = '';
    	if(!empty($val['WIDTH']))
    	{
    		$width = $val['WIDTH'];
    	}
    	//处理深度
    	$deep = '';
    	if(!empty($val['DEEP']))
    	{
    		$deep = $val['DEEP'];
    	}
    	//处理尺寸
    	if($height != '' && $width != '' && $deep !='')
    	{
    		$measurements = $height.'*'.$width.'*'.$deep;
    	}
    	else
    	{
    		$measurements = '';
    	}
    	//处理证书类型
    	$cert='';
        $cert2='';
        $cert3='';
    	if(!empty($val['REPORT_1']))
    	{
    		$cert = $val['REPORT_1'];
    	}

        if(!empty($val['REPORT_2']))
        {
            $cert2 = $val['REPORT_2'];
        }

        if(!empty($val['REPORT_3']))
        {
            $cert3 = $val['REPORT_3'];
        }

    	//处理证书号
    	$cert_id='';
        $cert_id2='';
        $cert_id3='';
    	if(!empty($val['CERTIFICATE_NUMBER_1']))
    	{
    		$cert_id = $val['CERTIFICATE_NUMBER_1'];
    	}
        if(!empty($val['CERTIFICATE_NUMBER_2']))
        {
            $cert_id2 = $val['CERTIFICATE_NUMBER_2'];
        }

        if(!empty($val['CERTIFICATE_NUMBER_3']))
        {
            $cert_id3 = $val['CERTIFICATE_NUMBER_3'];
        }

        $report_1='';
        $report_2='';
        $report_3='';
        if(!empty($val['CERTIFICATE_1']))
        {
            $report_1 = $val['CERTIFICATE_1'];
        }
        if(!empty($val['CERTIFICATE_2']))
        {
            $report_2 = $val['CERTIFICATE_2'];
        }

        if(!empty($val['CERTIFICATE_3']))
        {
            $report_3 = $val['CERTIFICATE_3'];
        }

    	//价格为空，可以 标注为 电话报价。 6.3*1.43*价格
    	//2014-9-15 王志凤确认 使用的是 RMB价格。
    	$cost_price = 0;
    	if(!empty($val['PRICE'])){
    		$cost_price = $val['PRICE'];
    	}
    	//处理image 1-5;
    	$image1 = '';
    	$image2 = '';
    	$image3 = '';
    	$image4 = '';
    	$image5 = '';
        $image6 = '';
        $image7 = '';
        $image8 = '';
    	if(!empty($val['IMAGE1']))
    	{
    		$image1 = $val['IMAGE1'];
    	}
    	if(!empty($val['IMAGE2']))
    	{
    		$image2 = $val['IMAGE2'];
    	}
    	if(!empty($val['IMAGE3']))
    	{
    		$image3 = $val['IMAGE3'];
    	}
    	if(!empty($val['IMAGE4']))
    	{
    		$image4 = $val['IMAGE4'];
    	}
    	if(!empty($val['IMAGE5']))
    	{
    		$image5 = $val['IMAGE5'];
    	}

        if(!empty($val['IMAGE6']))
        {
            $image3 = $val['IMAGE6'];
        }
        if(!empty($val['IMAGE7']))
        {
            $image4 = $val['IMAGE7'];
        }
        if(!empty($val['IMAGE8']))
        {
            $image5 = $val['IMAGE8'];
        }

    	//货品数量
    	$color_grade='';
    	if(!empty($val['INTENSITY']))
    	{
    		$color_grade = $val['INTENSITY'];
    	}
    	//货品数量
    	$quantity='';
    	if(!empty($val['QUANTITY']))
    	{
    		$quantity = $val['QUANTITY'];
    	}
    
    	$jiajialv=1;
    	$price = $cost_price*$jiajialv;
    	$good_type ='2';  //默认为现货
    	$mo_sn = '';	//模号
    	$status =1; 	//货品默认上架

    	
		$row = array(
				'goods_sn'=>$goods_sn,	//货号
				'shape'=>$shape,	//形状
				'carat'=>$carat,//克拉
				'color'=>$color,	//颜色
				'clarity'=>$clarity,	//净度
				'polish'=>$polish,//抛光
				'symmetry'=>$symmetry,//对称
				'fluorescence'=>$fluorescence,//荧光
				'measurements'=>$measurements,	//尺寸
				'cert'=>$cert,	//证书类型
                'cert2'=>$cert2,  //证书类型2
                'cert3'=>$cert3,  //证书类型3
				'cert_id'=>$cert_id,  //证书号
                'cert_id2'=>$cert_id2,  //证书号
                'cert_id3'=>$cert_id3,  //证书号
                'price'=> $price,  //价格
				'image1'=>$image1,	//图片1
				'image2'=>$image2,	//图片2
				'image3'=>$image3,	//图片3
				'image4'=>$image4,	//图片4
				'image5'=>$image5,	//图片5
                'image6'=>$image6,  //图片6
                'image7'=>$image7,  //图片7
                'image8'=>$image8,  //图片8
				'from_ad'=>'2',	//货品来源  leibish
				'add_time'=>date("Y-m-d H:i:s"),	//添加时间
				'color_grade'=>$color_grade,	//颜色等级
				'warehouse'=>'leibish',	//库房
				'cost_price'=>$cost_price,	//
				'good_type'=>$good_type,	//尺寸
				'mo_sn'=>$mo_sn,	//尺寸
				'status'=>$status,	//尺寸
				'quantity'=>$quantity,	
                'goods_id'=>$goods_id,  
                'price_status'=>$price_status,  
                'secondary_hue'=>$secondary_hue,  
                'white_color'=>$white_color,  
                'argyle_color'=>$argyle_color,  
                'pair'=>$pair,  
                'jewerly_type'=>$jewerly_type,  
                'jewerly_sub_type'=>$jewerly_sub_type,  
                'metal'=>$metal,  
                'metal_color'=>$metal_color,  
                'metal_weight'=>$metal_weight,  
                'entery_type'=>$entery_type,  
                'earback_type'=>$earback_type,  
                'ring_size'=>$ring_size,  
                'side_weight'=>$side_weight,  
                'jewelry_width'=>$jewelry_width,  
                'chain_length'=>$chain_length,  
                'length'=>$length,  
                'jewelry_stones_details'=>$jewelry_stones_details,  
                'price_per_karat'=>$price_per_karat,  
                'quantity'=>$quantity,  
                'report_1'=>$report_1,  
                'report_2'=>$report_2, 
                'report_3'=>$report_3

			);

        //$re = $db->autoExecute('ecs_color_dia_leibish', $row, 'INSERT');
        $key=implode("`,`",array_keys($row));
        $val=implode("','",array_values($row));
        $result=true;
    	$sql="INSERT INTO `app_diamond_color`(`".$key."`) VALUES ('".$val."')";
    	if(!mysqli_query($conn,$sql)){
    		$result = false;
    		}
    }
	
	if($result){
		echo "数据抓取成功".rand(1,1000000000);
	}else{
		echo "数据抓取失败"."<br><br>";
	}

/**将对象转换成数组**/
function objectToArray($obj)
{
    $arr = is_object($obj) ? get_object_vars($obj) : $obj;
    if(is_array($arr))
	{
        return array_map(__FUNCTION__, $arr);
    }
	else
	{
        return $arr;
    }
}

function objectToArray2($obj){
    $_arr = is_object($obj)? get_object_vars($obj) :$obj;
    foreach ($_arr as $key => $val){
        $val=(is_array($val)) || is_object($val) ? objectToArray($val) :$val;
        $_arr[$key] = $val;
    }
    return $_arr;

}
function arrtoxml($arr,$dom=0,$item=0){
    if (!$dom){
        $dom = new DOMDocument("1.0");
    }
    if(!$item){
        $item = $dom->createElement("root");
        $dom->appendChild($item);
    }
    foreach ($arr as $key=>$val){
        $itemx = $dom->createElement(is_string($key)?$key:"item");
        $item->appendChild($itemx);
        if (!is_array($val)){
            $text = $dom->createTextNode($val);
            $itemx->appendChild($text);

        }else {
            arrtoxml($val,$dom,$itemx);
        }
    }
    return $dom->saveXML();
}

