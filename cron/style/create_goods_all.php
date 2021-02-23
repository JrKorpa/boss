<?php
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once('MysqlDB.class.php');
set_time_limit(0);
ini_set('memory_limit','2000M');
$doTest = 'localhost';
//$doTest = true;
if($doTest === 'localhost'){
    $new_conf = [
        'dsn'=>"mysql:host=localhost:3306;dbname=front",
        'user'=>"root",
        'password'=>"root",
        'charset' => 'utf8'
    ];
}elseif($doTest){
    $new_conf = [
        'dsn'=>"mysql:host=192.168.0.95;dbname=front",
        'user'=>"cuteman",
        'password'=>"QW@W#RSS33#E#",
        'charset' => 'utf8'
    ];
}else{
    $new_conf = [
        'dsn'=>"mysql:host=192.168.1.59;dbname=front",
        'user'=>"cuteman",
        'password'=>"QW@W#RSS33#E#",
        'charset' => 'utf8'
    ];
}

global $db;
global $caizhi_arr;
global $color_arr;
global $color_value_arr;
$caizhi_arr = array("18K","PT950");
$color_arr=array("白"=>"W","黄"=>"Y","玫瑰金"=>"R","分色"=>"C","彩金"=>"H","玫瑰黄"=>"RY","玫瑰白"=>"RW","黄白"=>"YW","白黄（黄为电分色）"=>"WY");
$color_value_arr=array("白"=>1,"黄"=>2,"玫瑰金"=>3,"分色"=>4,"彩金"=>5,"玫瑰黄"=>6,"玫瑰白"=>7,"黄白"=>8,"白黄（黄为电分色）"=>9);

$db = new MysqlDB($new_conf);
$sql= "select * from front.base_style_info where is_made=1 and check_status=3";
$style_list = $db->getAll($sql);
foreach ($style_list as $style){
    $style_id = $style['style_id'];
    $style_sn = $style['style_sn'];
    $goodsList = getInfoByStyle_sn($style_id);
    if(count($goodsList) == 0){
        $error = '此款没有属性信息，请先添加属性信息！';
        loginfo('fail',$style_sn.'---'.$error);
        continue;
    }

    $res = create_goods($style_id);
    if($res['code'] == 0){
        loginfo('fail',$style_sn.'---'.$res['error']);
        continue;
    }else{
        loginfo('succ',$style_sn.'---'.$res['error']);
    }
}




die("执行完成");




function create_goods($style_id){
    $sql = "SELECT * FROM `front`.`base_style_info` WHERE `style_id`='{$style_id}' ";
    $base_style_info =  $GLOBALS['db']->getRow($sql);
    $style_sn = $base_style_info['style_sn'];
    $cnf_sec_stone_range = array();
    $cnf_finger_range = array();
    $data = getRingAttributeTmp($style_id);
    if($data['error']==1){
        $result['code'] = 0;
        $result['error'] = $data['message'];
        return $result;
    }else{
            $cnf_sec_stone_range = $data['data']['xk'];
            $cnf_finger_range = $data['data']['zq'];
    }


    $xiangkou = getXiangKouByStyle_sn(array('style_id'=>$style_id));
    $stone_xiangkou = array ();
    foreach ( $xiangkou as $val ) {
        $stone_xiangkou[$val['stone']][$val['finger']] = $val;
    }

    $goodsInfo=array();
    $stone = array();
    $finger = array();
    $sec_stone_weight = array();
    $sec_stone_num = array();
    $sec_stone_weight_other = array();
    $sec_stone_num_other = array();
    $sec_stone_weight3 = array();
    $sec_stone_num3 = array();
    $g18_weight = array();
    $g18_weight_more = array();
    $g18_weight_more2 = array();
    $gpt_weight = array();
    $gpt_weight_more = array();
    $gpt_weight_more2 = array();

    if(!empty($cnf_sec_stone_range) && !empty($cnf_finger_range)){
        foreach ($cnf_sec_stone_range as $item_stone){
            foreach ($cnf_finger_range as $finger_item){
                $stone[] = $item_stone;
                $finger[] = $finger_item;
                if(isset($stone_xiangkou[$item_stone][$finger_item]['sec_stone_weight'])){
                    $sec_stone_weight[] = $stone_xiangkou[$item_stone][$finger_item]['sec_stone_weight'];
                }else{
                    $sec_stone_weight[] = '';
                }

                if(isset($stone_xiangkou[$item_stone][$finger_item]['sec_stone_num'])){
                    $sec_stone_num[] = $stone_xiangkou[$item_stone][$finger_item]['sec_stone_num'];
                }else{
                    $sec_stone_num[] = '';
                }

                if(isset($stone_xiangkou[$item_stone][$finger_item]['sec_stone_weight_other'])){
                    $sec_stone_weight_other[] = $stone_xiangkou[$item_stone][$finger_item]['sec_stone_weight_other'];
                }else{
                    $sec_stone_weight_other[] = '';
                }


                if(isset($stone_xiangkou[$item_stone][$finger_item]['sec_stone_num_other'])){
                    $sec_stone_num_other[] = $stone_xiangkou[$item_stone][$finger_item]['sec_stone_num_other'];
                }else{
                    $sec_stone_num_other[] = '';
                }


                if(isset($stone_xiangkou[$item_stone][$finger_item]['sec_stone_weight3'])){
                    $sec_stone_weight3[] = $stone_xiangkou[$item_stone][$finger_item]['sec_stone_weight3'];
                }else{
                    $sec_stone_weight3[] = '';
                }


                if(isset($stone_xiangkou[$item_stone][$finger_item]['sec_stone_num3'])){
                    $sec_stone_num3[] = $stone_xiangkou[$item_stone][$finger_item]['sec_stone_num3'];
                }else{
                    $sec_stone_num3[] = '';
                }


                if(isset($stone_xiangkou[$item_stone][$finger_item]['g18_weight'])){
                    $g18_weight[] = $stone_xiangkou[$item_stone][$finger_item]['g18_weight'];
                }else{
                    $g18_weight[] = '';
                }


                if(isset($stone_xiangkou[$item_stone][$finger_item]['g18_weight_more'])){
                    $g18_weight_more[] = $stone_xiangkou[$item_stone][$finger_item]['g18_weight_more'];
                }else{
                    $g18_weight_more[] = '';
                }

                if(isset($stone_xiangkou[$item_stone][$finger_item]['g18_weight_more2'])){
                    $g18_weight_more2[] = $stone_xiangkou[$item_stone][$finger_item]['g18_weight_more2'];
                }else{
                    $g18_weight_more2[] = '';
                }


                if(isset($stone_xiangkou[$item_stone][$finger_item]['gpt_weight'])){
                    $gpt_weight[] = $stone_xiangkou[$item_stone][$finger_item]['gpt_weight'];
                }else{
                    $gpt_weight[] = '';
                }


                if(isset($stone_xiangkou[$item_stone][$finger_item]['gpt_weight_more'])){
                    $gpt_weight_more[] = $stone_xiangkou[$item_stone][$finger_item]['gpt_weight_more'];
                }else{
                    $gpt_weight_more[] = '';
                }


                if(isset($stone_xiangkou[$item_stone][$finger_item]['gpt_weight_more2'])){
                    $gpt_weight_more2[] = $stone_xiangkou[$item_stone][$finger_item]['gpt_weight_more2'];
                }else{
                    $gpt_weight_more2[] = '';
                }

            }
        }
    }

    $goodsInfo['stone'] = $stone;
    $goodsInfo['finger'] = $finger;
    $goodsInfo['sec_stone_weight'] = $sec_stone_weight;
    $goodsInfo['sec_stone_num'] = $sec_stone_num;
    $goodsInfo['sec_stone_weight_other'] = $sec_stone_weight_other;
    $goodsInfo['sec_stone_num_other'] = $sec_stone_num_other;
    $goodsInfo['sec_stone_weight3'] = $sec_stone_weight3;
    $goodsInfo['sec_stone_num3'] = $sec_stone_num3;
    $goodsInfo['g18_weight'] = $g18_weight;
    $goodsInfo['g18_weight_more'] = $g18_weight_more;
    $goodsInfo['g18_weight_more2'] = $g18_weight_more2;
    $goodsInfo['gpt_weight'] = $gpt_weight;
    $goodsInfo['gpt_weight_more'] = $gpt_weight_more;
    $goodsInfo['gpt_weight_more2'] = $gpt_weight_more2;

    //检测是否有新录入数据，先提交信息
    $data = array();
    $stoneAll = $goodsInfo['stone'];
    if(!empty($stoneAll)){
        //array_pop($stoneAll);
    }
    //改变结构检测
    foreach ($stoneAll as $k => $v) {
        # code...
        if($goodsInfo['sec_stone_weight'][$k]){
            $data['sec_stone_weight'][$k]['stone']=$v;
            $data['sec_stone_weight'][$k]['finger']=$goodsInfo['finger'][$k];
        }if($goodsInfo['sec_stone_num'][$k]){
            $data['sec_stone_num'][$k]['stone']=$v;
            $data['sec_stone_num'][$k]['finger']=$goodsInfo['finger'][$k];
        }if($goodsInfo['sec_stone_weight_other'][$k]){
            $data['sec_stone_weight_other'][$k]['stone']=$v;
            $data['sec_stone_weight_other'][$k]['finger']=$goodsInfo['finger'][$k];
        }if($goodsInfo['sec_stone_num_other'][$k]){
            $data['sec_stone_num_other'][$k]['stone']=$v;
            $data['sec_stone_num_other'][$k]['finger']=$goodsInfo['finger'][$k];
            $data['sec_stone_num_other'][$k][]=$goodsInfo['sec_stone_num_other'][$k];
        }if($goodsInfo['sec_stone_weight3'][$k]){
            $data['sec_stone_weight3'][$k]['stone']=$v;
            $data['sec_stone_weight3'][$k]['finger']=$goodsInfo['finger'][$k];
            $data['sec_stone_weight3'][$k][]=$goodsInfo['sec_stone_weight3'][$k];
        }if($goodsInfo['sec_stone_num3'][$k]){
            $data['sec_stone_num3'][$k]['stone']=$v;
            $data['sec_stone_num3'][$k]['finger']=$goodsInfo['finger'][$k];
        }if($goodsInfo['g18_weight'][$k]){
            $data['g18_weight'][$k]['stone']=$v;
            $data['g18_weight'][$k]['finger']=$goodsInfo['finger'][$k];
        }if($goodsInfo['g18_weight_more'][$k]){
            $data['g18_weight_more'][$k]['stone']=$v;
            $data['g18_weight_more'][$k]['finger']=$goodsInfo['finger'][$k];
        }if($goodsInfo['g18_weight_more2'][$k]){
            $data['g18_weight_more2'][$k]['stone']=$v;
            $data['g18_weight_more2'][$k]['finger']=$goodsInfo['finger'][$k];
        }if($goodsInfo['gpt_weight'][$k]){
            $data['gpt_weight'][$k]['stone']=$v;
            $data['gpt_weight'][$k]['finger']=$goodsInfo['finger'][$k];
        }if($goodsInfo['gpt_weight_more'][$k]){
            $data['gpt_weight_more'][$k]['stone']=$v;
            $data['gpt_weight_more'][$k]['finger']=$goodsInfo['finger'][$k];
        }if($goodsInfo['gpt_weight_more2'][$k]){
            $data['gpt_weight_more2'][$k]['stone']=$v;
            $data['gpt_weight_more2'][$k]['finger']=$goodsInfo['finger'][$k];
        }
    }
    //判断信息是否都为空
    if(empty($data)){
        $result['code'] = 0;
        $result['error'] = '未检测出数据，请填写信息！';
        return $result;
    }

    foreach ($data as $key => $val) {
        # code...
        foreach ($val as $k => $v) {
            # code...
            $ls['style_id'] = $style_id;
            $ls['stone'] = $v['stone'];
            $ls['finger'] = $v['finger'];
            $xiangkou = getXiangKouByStyle_Id($ls);
            if(empty($xiangkou)){
                $result['code'] = 0;
                $result['error'] = '有录入新数据，请先提交信息！';
                return $result;
            }
        }
    }


    $jinZongInfo = getXiangKouByStyle_Id(array('style_id'=>$style_id));

    $style_xiangkou = array();
    $style_zhiquan = array();
    $style_caizhi = array();
    $style_yanse = array();
    $info = getRingAttributeTmp($style_id);
    if($info['error'] == 1){
        $result['code'] = 0;
        $result['error'] = $info['message'];
        return $result;
    }else{
        //取出都是款的属性对应的属性值的id，并不是描述，所以需要在转化成描述
        //$style_xiangkou = $info['data']['xk'];
        //$style_zhiquan = $info['data']['zq'];
        $style_caizhi = $info['data']['cz'];
        $style_yanse = $info['data']['ys'];
    }

    //取出所以的材质和颜色信息
    $color_arr = $GLOBALS['color_arr'];
    $color_value_arr = $GLOBALS['color_value_arr'];
    $is_flag = false;
    $res1_num = 0;
    $res2_num = 0;
    array_filter($style_yanse);
    foreach ($jinZongInfo as $keys => $jinzong) {
        //18K
        if(in_array("18K", $style_caizhi) && $jinzong['g18_weight'] != '0'){
            $is_flag = true;
            foreach ($style_yanse as $vals){
                if(array_key_exists($vals, $color_arr)){
                    $yanse_data[$color_value_arr[$vals]] = $color_arr[$vals];
                }
            }
            $caizhi = array('id'=>1,'name'=>"18K");
            $res1_num += create_goods_insert_all($base_style_info, $jinzong, $caizhi, $yanse_data);
        }

        //PT950
        if(in_array("PT950", $style_caizhi) && $jinzong['gpt_weight'] != '0'){
            $is_flag = true;
            //只有一个颜色那就是白色
            $yanse_data_pt[$color_value_arr["白"]] = $color_arr["白"];
            $caizhi = array('id'=>2,'name'=>"PT950");
            $res2_num += create_goods_insert_all($base_style_info, $jinzong, $caizhi, $yanse_data_pt);
        }
    }

    if($is_flag){
        $num = $res1_num + $res2_num;
        $result['code'] = 1;
        $result['error'] = $style_sn."生成成功,一共生成".$num."条SKU。";
    }else{
        $result['code'] = 0;
        $result['error'] = '添加失败';
    }
    return $result;








}



//戒指的相关属性
function getRingAttributeTmp($style_id) {
    $error = 0;//默认没有错误

    $xiangkou_data = getAttributeInfoByName('镶口');
    $zhiquan_data = getAttributeInfoByName('指圈');
    $caizhi_data = getAttributeInfoByName('材质');
    $yanse_data = getAttributeInfoByName('材质颜色');
    $xk_id = $xiangkou_data['attribute_id'];
    $zq_id = $zhiquan_data['attribute_id'];
    $cz_id = $caizhi_data['attribute_id'];
    $ys_id = $yanse_data['attribute_id'];
    //var_dump($xiangkou_data,$zhiquan_data,$caizhi_data,$yanse_data);

    $xk_data = getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$xk_id));

    //var_dump($xk_data);
    //镶口
    if(empty($xk_data)){
        $error = 1;
        return array('error'=>$error,'message'=>'此款没有设置：镶口属性!');
    }
    if(empty($xk_data['attribute_value'])){
        $error = 1;
        return array('error'=>$error,'message'=>'此款没有选择：镶口数据!');
    }
    //指圈
    $zq_data = getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$zq_id));
    //var_dump($zq_data);
    if(empty($zq_data)){
        /*
        return array('error'=>$error,'message'=>'此款没有设置：指圈属性!');
        */
        $zq_data= array('attribute_value'=>'888,','product_type_id'=>16,'cat_type_id'=>14);
    }
    if(empty($zq_data['attribute_value'])){
        $error = 1;
        return array('error'=>$error,'message'=>'此款没有选择：指圈数据!');
    }
    //材质
    $cz_data = getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$cz_id));
    if(empty($cz_data)){
        $error = 1;
        return array('error'=>$error,'message'=>'此款没有设置：材质属性!');
    }
    if(empty($cz_data['attribute_value'])){
        $error = 1;
        return array('error'=>$error,'message'=>'此款没有选择：材质数据!');
    }
    //可做颜色
    $ys_data = getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$ys_id));
    if(empty($ys_data)){
        /*
        $error = 1;
        return array('error'=>$error,'message'=>'此款没有设置：材质颜色属性!');
        */
        $ys_data= array('attribute_value'=>'888,','product_type_id'=>16,'cat_type_id'=>14);
    }
    if(empty($ys_data['attribute_value'])){
        $error = 1;
        return array('error'=>$error,'message'=>'此款没有选择：材质颜色数据!');
    }

    $xk_info = explode(",",rtrim($xk_data['attribute_value'],","));
    $zq_info = explode(",",rtrim($zq_data['attribute_value'],","));
    $cz_info = explode(",",rtrim($cz_data['attribute_value'],","));
    $ys_info = explode(",",rtrim($ys_data['attribute_value'],","));

    //取出都是款的属性对应的属性值的id，并不是描述，所以需要在转化成描述
    $style_xiangkou = array();
    $style_zhiquan = array();
    $style_caizhi = array();
    $style_yanse = array();

    //镶口
    foreach ($xk_info as $val){
        $value = getAttrNameByid(array('att_value_id'=>$val));
        $style_xiangkou[]=$value['att_value_name'];
    }
    //指圈
    foreach ($zq_info as $val){
        if($val == 888){
            $style_zhiquan[]='0';
            continue;
        }
        $value = getAttrNameByid(array('att_value_id'=>$val));
        $style_zhiquan[]=$value['att_value_name'];
    }
    //材质
    foreach ($cz_info as $val){
        $value = getAttrNameByid(array('att_value_id'=>$val));
        $style_caizhi[]=$value['att_value_name'];
    }
    //材质颜色
    foreach ($ys_info as $val){
        if($val == 888){
            $style_yanse[]='白色';
            continue;
        }
        $value = getAttrNameByid(array('att_value_id'=>$val));
        $style_yanse[]=$value['att_value_name'];
    }

    return array('error'=>$error,'data'=>array('xk'=>$style_xiangkou,'zq'=>$style_zhiquan,'cz'=>$style_caizhi,'ys'=>$style_yanse));
}



/*
      * 获取款式的属性
        */
 function getStyleAttributeByStyleId($where) {
    $str = "";
    if(isset($where['style_id'])){
        $str .=" AND `style_id` =".$where['style_id'];
    }

    if(isset($where['style_sn'])){
        $str .=" AND `style_sn` = '".$where['style_sn']."'";
    }

    if(isset($where['attribute_id'])){
        $str .=" AND `attribute_id` =".$where['attribute_id'];
    }

    $sql = "SELECT `attribute_value`,`product_type_id`,`cat_type_id` FROM `front`.`rel_style_attribute` WHERE 1 ".$str ;
    return $GLOBALS['db']->getRow($sql);
}

/*
     * 获取属性信息
     */
function getAttributeInfoByName($attribute_name){
    $sql = "SELECT `attribute_id`,`attribute_code` FROM `front`.`app_attribute` WHERE `attribute_name`='{$attribute_name}' ";
    return $GLOBALS['db']->getRow($sql);
}


/*
         * 获取属性的属性名
        */
function getAttrNameByid($where){
    $sql = "SELECT `att_value_name` FROM `front`.`app_attribute_value`";
    if($where['att_value_id']!=''){
        $sql .= " WHERE `att_value_id`='{$where['att_value_id']}'";
    }
    return $GLOBALS['db']->getRow($sql);
}


/**
 *	style_sn，取款
 *
 *	@url AppXiangkouController/get
 */
function getXiangKouByStyle_sn ($where=array())
{
    $sql = "SELECT * FROM front.`app_xiangkou` WHERE 1 ";
    if(isset($where['style_sn']) && !empty($where['style_sn'])){
        $sql .=" AND `style_sn` = '{$where['style_sn']}' ";
    }
    if(isset($where['style_id']) && !empty($where['style_id'])){
        $sql .=" AND `style_id` = '{$where['style_id']}' ";
    }
    $sql .= " ORDER BY x_id DESC";
    $data = $GLOBALS['db']->getAll($sql);
    return $data;
}

/*
        * 根据款号查询属性信息是否存在
       */
 function getInfoByStyle_sn($style_id){
    $sql = "SELECT `style_id` FROM front.`rel_style_attribute`";
    if($style_id!=''){
        $sql .= " WHERE `style_id`={$style_id}";
    }
    //echo $sql;exit;
    return $GLOBALS['db']->getAll($sql);
}

/**
 *	style_sn，取款
 *
 *	@url AppXiangkouController/get
 */
function getXiangKouByStyle_Id($where)
{
    $sql = "SELECT * FROM front.`app_xiangkou` WHERE 1 ";
    if(isset($where['style_id']) && !empty($where['style_id'])){
        $sql .=" AND `style_id` = '{$where['style_id']}' ";
    }
    if(isset($where['stone']) && !empty($where['stone'])){
        $sql .=" AND `stone` = '{$where['stone']}' ";
    }
    if(isset($where['finger']) && !empty($where['finger'])){
        $sql .=" AND `finger` = '{$where['finger']}' ";
    }
    $sql .= " ORDER BY x_id DESC";
    $data = $GLOBALS['db']->getAll($sql);
    return $data;
}




 function create_goods_insert_all($style_info,$xiangkou,$caizhi_info,$color_arr)
{

    $stone = $xiangkou['stone'];
    $finger = $xiangkou['finger'];
    $style_id = $style_info['style_id'];
    $style_sn = $style_info['style_sn'];
    $style_name = $style_info['style_name'];
    $product_type_id = $style_info['product_type'];
    $cat_type_id = $style_info['style_type'];
    $caizhi = $caizhi_info['id'];
    $caizhi_name = $caizhi_info['name'];


    $cut_finger = cutFingerInfoAll($finger);
    //echo '<pre>';
    //print_r($color_arr);die;
    $olddo = array();
    $num = 0;
    $insert_data=array();

    foreach ($color_arr as $ys_key => $ys_val) {
        # code...
        $color_name = $ys_val;
        $where['style_id']=$style_id;
        $where['style_sn']=$style_sn;
        $where['product_type_id']=$product_type_id;//产品线id
        $where['cat_type_id']=$cat_type_id;//分类id
        $where['style_name'] = $style_name;//款式名称
        $where['caizhi']=$caizhi;//材质
        $where['yanse']=$ys_key;//镶口
        $where['xiangkou'] = $stone;//镶口

        $where['zhushizhong']=$stone; //主石重
        $where['zhushi_num']=0; //主石数
        $where['fushizhong1']=$xiangkou['sec_stone_weight']; //副石1重
        $where['fushi_num1']=$xiangkou['sec_stone_num']; //副石1数量
        $where['fushizhong2']=$xiangkou['sec_stone_weight_other']; //副石2重
        $where['fushi_num2']=$xiangkou['sec_stone_num_other'];// 副石2数量
        $where['fushizhong3']=$xiangkou['sec_stone_weight3']; //副石2重
        $where['fushi_num3']=$xiangkou['sec_stone_num3'];// 副石2数量
        //$where['fushi_chengbenjia_other']=$xiangkou['sec_stone_price_other'][$k];// 其他副石成本价
        $where['dingzhichengben']=601;// 定制成本
        if($caizhi == 1){
            $where['weight']=$xiangkou['g18_weight']; //18K标准金重
            $where['jincha_shang']=$xiangkou['g18_weight_more'];//18K金重上公差
            $where['jincha_xia']=$xiangkou['g18_weight_more2'];// 18K金重下公差
        }else{
            $where['weight']=$xiangkou['gpt_weight'];//PT950标准金重
            $where['jincha_shang']=$xiangkou['gpt_weight_more'];//PT950金重上公差
            $where['jincha_xia']=$xiangkou['gpt_weight_more2'];//PT950金重下公差
        }

        $where['last_update']=date("Y-m-d H:i:s");

        if($xiangkou['sec_stone_weight_other']==""){
            $where['fushizhong2']=0; //副石2重
        }
        if($xiangkou['sec_stone_num_other']==""){
            $where['fushi_num2']=0;// 副石2数量
        }
        if($xiangkou['sec_stone_weight3']==""){
            $where['fushizhong3']=0; //副石3重
        }
        if($xiangkou['sec_stone_num3']==""){
            $where['fushi_num3']=0;// 副石3数量
        }
        /*if($xiangkou['sec_stone_price_other'][$k]==""){
             $where['fushi_chengbenjia_other'] =0;// 其他副石成本价
        }*/
        $where['fushi_chengbenjia_other'] =0;// 其他副石成本价
        if($caizhi == 1){
            if($xiangkou['g18_weight']==""){
                $where['weight'] =0;// 18K标准金重
            }
            if($xiangkou['g18_weight_more']==""){
                $where['jincha_shang'] =0;// 18K金重上公差
            }
            if($xiangkou['g18_weight_more2']==""){
                $where['jincha_xia'] =0;// 18K金重下公差
            }
        }else{
            if($xiangkou['gpt_weight']==""){
                $where['weight'] =0;// PT950标准金重
            }
            if($xiangkou['gpt_weight_more']==""){
                $where['jincha_shang'] =0;// //PT950金重上公差
            }
            if($xiangkou['gpt_weight_more2']==""){
                $where['jincha_xia'] =0;// //PT950金重下公差
            }
        }


        $where['xiangkou_company_type']=$xiangkou['company_type']; //可销售公司类型
        //循环指圈
        foreach ($cut_finger as $f_val){

            $shoucun = $f_val;
            $where['shoucun']=$shoucun;//手寸
            $stone_name = $stone * 100;
            $goods_sn = $style_sn."-".$caizhi_name."-".$color_name."-".$stone_name."-".$shoucun;
            $quickDiyGoods = getQuickDiyGoodsByGoodsSn($goods_sn);
            if(!empty($quickDiyGoods)){
                deletegoods_sninfo($goods_sn);
            }
            $where['is_quick_diy'] = $quickDiyGoods['is_quick_diy']==1?1:0;
            $where['goods_sn'] = $goods_sn;
            $num++;

            $insert_data[]=$where;
        }
    }
    $GLOBALS['db']->autoExecALL($insert_data,'list_style_goods');
    update_goods_price($style_id,$caizhi,$stone);

    return $num;
}


/*
     * 切割手寸,针对批量生成商品
     * 转换数据 6-8 其实要变成 6,7,8
     */
function cutFingerInfoAll($data){

    if($data === 0){
        return array('0');
    }

    $is_search = strpos($data,'-');

    $new_arr = array();
    if($is_search){

        $tmp = explode('-', $data);

        $min = intval($tmp[0]);
        $max = intval($tmp[1]);

        if($min == $max) {

            $new_arr[] = $min;
        }else{

            for($i=$min;$i<=$max;$i++){

                $new_arr[] = $i;
            }
        }
    }else{

        $new_arr[] = $data;
    }
    $data=$new_arr;
    return $data;
}


/**
 * 快速定制商品码查询
 * @param unknown $goods_sn
 */
 function getQuickDiyGoodsByGoodsSn($goods_sn){
    $sql = "SELECT a.goods_sn,IFNULL(b.status,0) as is_quick_diy FROM front.`list_style_goods` a left join app_style_quickdiy b on a.goods_sn=b.goods_sn WHERE a.`goods_sn` = '{$goods_sn}'";
    return $GLOBALS['db']->getRow($sql);
}

 function deletegoods_sninfo($goods_sn_in)
{
    # code...
    if(!$goods_sn_in){
        return false;
    }
    $sql = " DELETE FROM front.`list_style_goods` WHERE `goods_sn` = '{$goods_sn_in}'";
    return $GLOBALS['db']->query($sql);
}



/*------------------------------------------------------ */
//-- 更新商品成本价格
//-- BY linian
/*------------------------------------------------------ */
 function update_goods_price($style_id,$caizhi,$stone) {

    $result = array('success' => 0, 'error' => '');
    //$style_id = _Post::getInt('id');
    //1,获取商品表中所有商品
    if($caizhi){
        $data = getAllGoodsinfo($style_id,$caizhi,$stone);
    }

   $res = true;
    //var_dump($data);
    //echo "+++++++++++++++++++++++++";
    //遍历所有商品数据 没遍历一条更新都更新商品成本价格
    foreach($data as $key=>$val){
        //var_dump($val);
        //echo '=========================';
        //2,每次获取一条基本数据
        $goods_id = $val['goods_id'];
        $style_id = $val['style_id'];
        $style_sn = $val['style_sn'];
        $yanse = $val['yanse'];
        $fushi_1 = $val['fushizhong1'];
        $fushi_num_1 = $val['fushi_num1'];
        $fushi_2 = $val['fushizhong2'];
        $fushi_num_2 = $val['fushi_num2'];
        $fushi_3 = $val['fushizhong3'];
        $fushi_num_3 = $val['fushi_num3'];
        $caizhi = $val['caizhi'];
        $weight = $val['weight'];
        $xiangkou = $val['xiangkou'];
        $jincha_shang = $val['jincha_shang'];
        $product_type_id = $val['product_type_id'];
        $goods_sn[]= $val['goods_sn'];
        //print_r($goods_sn);


        //工费信息  基础工费 表面工艺费 超石费
        if(!empty($style_id)){
            //获取三种工费
            $gongfei='';
            $baomiangongyi_gongfei='';
            $chaoshifee='';
            $baoxianfei = '';
            $gongfei_data = getStyleFee($style_id);
            foreach($gongfei_data as $val){
                if($val['fee_type']==1 && $caizhi==1){
                    $gongfei = empty($val['price'])?'0':$val['price'];
                }elseif($val['fee_type']==2){
                    $chaoshifee = empty($val['price'])?'0':$val['price'];
                    $chaoshifee = $chaoshifee * ($fushi_num_1+$fushi_num_2+$fushi_num_3);
                }elseif($val['fee_type']==3){
                    $baomiangongyi_gongfei = empty($val['price'])?'0':$val['price'];
                }elseif($val['fee_type']==4 && $caizhi==2){
                    $gongfei = empty($val['price'])?'0':$val['price'];
                }
            }
           
           
            $parent_id = getParentIdById($product_type_id);

            if($parent_id == 3 && $xiangkou != ''){
                $baoxianfei = getPriceByXiangkou($xiangkou);
            }
            
        }

        //4,计算各种工费数据
        $tal_gongfei = $gongfei+$baomiangongyi_gongfei+$chaoshifee+$baoxianfei;
        //var_dump($tal_gongfei);exit;
        //$gongfei = empty($val['gongfei'])?'':$val['gongfei'];
        //$baomiangongyi_gongfei = empty($val['baomiangongyi_gongfei'])?'':$val['baomiangongyi_gongfei'];
        //$fushixiangshifei = empty($val['fushixiangshifei'])?'':$val['fushixiangshifei'];
        //var_dump('工费',$tal_gongfei);
        //金损率:price_type:1男戒2女戒3情侣男戒4情侣女戒;
        //3,判断款号是什么什么戒指，来获取对应的金损

        if(!empty($caizhi)){
            //材质
            //if($caizhi==1){
            //	$where['material_id']="18K";
            //}else{
            //	$where['material_id']="PT950";
            //}
            $where['material_id']=$caizhi;

            //2 女戒
            $where['price_type']=2;
            $jinsundata = getAppJinSun($where);
            if($jinsundata){
                $jinsunlv = $jinsundata[0]['lv'];

            }
        }



        //5,获取所有钻石规格单价数据
        //(副石1重/副石1数量)的对应单价*副石1重+（副石2重/副石2数量）的对应单价*副石2重+（副石3重/副石3数量）的对应单价*副石3重
        if($fushi_num_1){
            $where['guige'] = 100 * $fushi_1 / $fushi_num_1;
            //获取副石1价格
            $diamondprice = getDanPrice($where);
            $fushi_price_1=$diamondprice['price']*$fushi_1;
        }else{
            $fushi_price_1='';
        }
        if($fushi_num_2){
            $where['guige'] = 100 * $fushi_2 / $fushi_num_2;
            //获取副石2价格
            $diamondprice = getDanPrice($where);
            $fushi_price_2=$diamondprice['price']*$fushi_2;
        }else{
            $fushi_price_2='';
        }
        if($fushi_num_3){
            $where['guige'] = 100 * $fushi_3 / $fushi_num_3;
            //获取副石3价格
            $diamondprice = getDanPrice($where);
            $fushi_price_3=$diamondprice['price']*$fushi_3;
        }else{
            $fushi_price_3='';
        }
        //var_dump($fushi_price_1,$fushi_price_2,$fushi_price_3);
        //6,(材质金重+向上公差）*金损率* 对应材质单价
        //材质单价:price_type :1=》18K；2=>PT950; price:价格; type = 2

        if(!empty($caizhi)){
            if($caizhi ==1){
                $material_name ='18K';
            }elseif($caizhi ==2){
                $material_name ='PT950';
            }
            //材质
            $where['material_name']=$material_name;
            $where['material_status']=1;
            //获取对应的材质单价
            $caizhidata = getAppMaterialInfo($where);
            $caizhi_price = $caizhidata[0]['price'];
            $shuidian = $caizhidata[0]['tax_point'];
        }


        //7,金损率 等于1+金损率
        $jinsun_price = $jinsunlv+1;
        //var_dump('近损率',$jinsun_price);
        //8,计算金损价格
        //var_dump('金重',$weight);
        //var_dump('上公差',$jincha_shang);
        //var_dump('金损价',$jinsun_price);
        //var_dump('材质价格',$caizhi_price);
        $tal_jinsun = ($weight + $jincha_shang) * $jinsun_price * $caizhi_price;
        //9,计算定制成本价格

        //$aa = array('副石1'=>$fushi_price_1,'副石2'=>$fushi_price_2,'金损率'=>$tal_jinsun,'工费'=>$tal_gongfei);
        //var_dump($aa);
        $dingzhichengben = ($fushi_price_1 + $fushi_price_2 + $fushi_price_3 + $tal_jinsun + $tal_gongfei) * (1 + $shuidian);
        //var_dump('定制成本加个',$dingzhichengben);
        $where['chengbenjia'] = round($dingzhichengben,2);
        //var_dump($where['chengbenjia'],99);
        $where['goods_id'] =$goods_id;

        $res = updateChengbenPrice($where);

        $chenbenjia[] =$where['chengbenjia'];
    }

    if ($res !== false) {
        $result['success'] = 1;
    } else {
        $result['error'] = '更新价格失败';
        loginfo('fail',$style_sn.'----'.$result['error']);
    }
    return array('flag'=>$result);

}



 function getAllGoodsinfo($style_id,$caizhi,$stone){
    $where = "WHERE 1 ";
    if(isset($style_id)&&!empty($style_id)){
        $where.=" and `style_id` = {$style_id}";
    }
    if(isset($caizhi)&&!empty($caizhi)){
        $where.=" and `caizhi` = {$caizhi}";
    }
    if(isset($stone)&&!empty($stone)){
        $where.=" and `xiangkou` = '{$stone}'";
    }

    $sql ="SELECT * FROM front.`list_style_goods` {$where}";
    $res = $GLOBALS['db']->getAll($sql);
    return $res;
}

//查询款式的3种工费   表面工艺费   超时费  基础工费
function  getStyleFee($style_id){
    $sql = "SELECT * FROM front.`app_style_fee` WHERE  `style_id` ={$style_id}";
    return $res = $GLOBALS['db']->getAll($sql);
}

/**
 *
 *
 *	@url MessageController/search
 */
function getAppJinSun ($where)
{
    $sql = "SELECT * FROM front.`app_jinsun` WHERE 1 ";
    if(isset($where['price_type']) && !empty($where['price_type'])){
        $sql .=" AND price_type = '{$where['price_type']}' ";
    }
    if(isset($where['material_id']) && !empty($where['material_id'])){
        $sql .=" AND material_id = '{$where['material_id']}' ";
    }
    if(isset($where['jinsun_status']) && $where['jinsun_status']!=''){
        $sql .=" AND jinsun_status = '{$where['jinsun_status']}' ";
    }
    $sql .= " ORDER BY s_id DESC";
    return $res = $GLOBALS['db']->getAll($sql);
}


/**
 *	根据石头重量获取钻石规格单价
 *	@url AppDiamondPriceController/search
 */
function getDanPrice ($where)
{
    $sql = "SELECT `price` FROM front.`app_diamond_price` WHERE 1 ";
    if(isset($where['guige']) && !empty($where['guige'])){
        $sql .=" AND guige_a < {$where['guige']} AND guige_b >= {$where['guige']} ";
    }
    return $GLOBALS['db']->getRow($sql);
}


function getAppMaterialInfo ($where)
{
    $sql = "SELECT * FROM front.`app_material_info`  WHERE 1 ";

    if($where['material_name'] != "")
    {
        $sql .= " AND material_name like \"%".addslashes($where['material_name'])."%\"";
    }
    if($where['material_status'] != "")
    {
        $sql .= " AND material_status =".addslashes($where['material_status']);
    }

    $sql .= " ORDER BY material_id DESC";
    return $res = $GLOBALS['db']->getAll($sql);
}

/*
         * 更新商品成本价格 BY linian
        */
function updateChengbenPrice($where){

    if(isset($where['chengbenjia'])&&!empty($where['goods_id'])){
        $chengben = $where['chengbenjia'];
        $goods_id = $where['goods_id'];
        $sql = "UPDATE  front.`list_style_goods` SET `dingzhichengben`={$chengben} WHERE `goods_id`={$goods_id}";
        return $GLOBALS['db']->query($sql);
    }
    return 1;
}

function getParentIdById ($id)
    {
        $sql = "SELECT `parent_id` FROM front.`app_product_type` WHERE `product_type_id` = {$id}";
        return $GLOBALS['db']->getOne($sql);
    }


 /**
     *  镶口获取保险费｛只针对于产品线：镶嵌类｝
     *  @url AppStyleBaoxianfeeController/search
     */
    function getPriceByXiangkou ($xiangkou)
    {
        $sql = "SELECT `price` FROM front.`app_style_baoxianfee` WHERE {$xiangkou} >= min and {$xiangkou} <= max and `status` = 1";
        return $GLOBALS['db']->getOne($sql);
    }	

function loginfo($file_exi,$err) {
    file_put_contents(__DIR__.'/log/'.$file_exi.date('Y-m-d').'create_goods_all.err', json_encode($err, JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);
}