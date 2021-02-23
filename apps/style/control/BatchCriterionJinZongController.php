<?php
/**
 * @Author: H丶xw
 * @Date:   2015-09-14 19:19:09
 * @Last Modified by:   anchen
 * @Last Modified time: 2017-03-01 15:28:15
 */

class BatchCriterionJinZongController extends CommonController
{

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('download_demo');

    /**
     *  index，搜索框
     */
    public function index ($params)
    {
        $this->render('batch_criterion_jin_zong.html');
    }

    /**
     *  uploadJinZongFile，提交上传
     */
    public function uploadJinZongFile($params)
    {
        # code...
        # 
        set_time_limit(0);//设置上传允许超时提交（数据量大时有用）
        $res = array('success' => 0,'error' => '');

        //标红提示；
        $red_err_str = "提示：<span style='color:red';>";
        $html_end = "</span> <br />";
        //$res['error'] = "提示：批量上传成功，<span style='color:red;'>请核查！</span>";
        //Util::jsonExit($res);
        $fileInfo = $_FILES['fileinfo'];//读取文件信息；

        $tmp_name = $fileInfo['tmp_name'];
        //是否选择文件；
        if ($tmp_name == '') 
        {

            $res['error'] = $red_err_str."请选择上传文件！（请下载模版务必按照表头填写后上传）。".$html_end;
            Util::jsonExit($res);
        }

        //是否csv文件；
        $file_name = $fileInfo['name'];
        if (Upload::getExt($file_name) != 'csv') 
        {

            $res['error'] = $red_err_str."请上传.csv为后缀的文件！".$html_end;
            Util::jsonExit($res);
        }

        //打开文件资源
        $fileData = fopen($tmp_name, 'r');
        while ($data = fgetcsv($fileData))
        {
            $styleInfo[] = $data;
        }

        //是否填写数据
        if (count($styleInfo) == 1)
        {

            $res['error'] = $red_err_str."未检测到数据，请填写后上传！".$html_end;
            Util::jsonExit($res);
        }

        //限制上传数据量，限制行数为小于等于150行数据
        if (count($styleInfo) >= 151)
        {

            $res['error'] = $red_err_str."上传数据过大会导致提交超时，不能超过150行信息！".$html_end;
            Util::jsonExit($res);
        }

        $stylemodel = new BaseStyleInfoModel(11);
        $attrmodel = new RelStyleAttributeModel(11);
        $attrvalmodel = new AppAttributeValueModel(11);
        $hgt = 1;//行数；
        array_shift($styleInfo);//去除首行文字；
        //对文档数据基本检测；
        foreach ($styleInfo as $key => $val) 
        {
            # code...
            # 
            $hgt++;
            //是否为16列信息；
            if (count($val) != 16)
            {

                $res['error'] = $red_err_str."文件第".$hgt."行请上传16列信息！（按照模版要求填写）".$html_end;
                Util::jsonExit($res);
            }

            $fields = array('style_sn','stone','finger','sec_stone_weight','sec_stone_num','sec_stone_weight_other','sec_stone_num_other','sec_stone_weight3','sec_stone_num3','sec_stone_price_other','g18_weight','g18_weight_more','g18_weight_more2','gpt_weight','gpt_weight_more','gpt_weight_more2');

            //去除用户录入不规范的内容
            for ($i=0; $i < 16; $i++) 
            { 
                # code...
                # 
                if($val[$i] === ''){

                    $val[$i] = '0';//将用户填为空的默认写为0；
                }

                $LineInfo[$fields[$i]] = $this->trimall($val[$i]);
            }

            $LineInfo['stone'] = number_format($LineInfo['stone'], 2, '.', '');//镶口保留两位小数；
            $LineInfo['main_stone_weight'] = 0;
            $LineInfo['main_stone_num'] = 0;

            $styleData = $stylemodel->getStyleStyleByStyle_sn($LineInfo);

            //是否是BDD款号；
            if (empty($styleData) || !isset($styleData[0]))
            {

                $res['error'] = $red_err_str."文件第".$hgt."行录入的款号在款式库查不到！（请按照要求填写）".$html_end;
                Util::jsonExit($res);
            }

            $styleData = $styleData[0];
            $LineInfo['style_id'] = $styleData['style_id'];
            //款式是否审核；tby（保存、申请审核、审核状态的款都需要导入金重信息，但保存、申请审核状态的不需要生成商品）；
            /*if ($styleData['check_status'] != 3)
            {

                $error_info.= $red_err_str."文件第".$hgt."行录入的款号不是已审核状态！（请按照要求填写）".$html_end;
            }*/
            $check_stone = array();
            $check_stone['style_sn'] = $styleData['style_sn'];
            $check_stone['attribute_id'] = 1;//镶口
            //是否维护镶口信息；
            $styleStone = $attrmodel->getStyleAttributeByStyleId($check_stone);
            if (empty($styleStone) || $styleStone['attribute_value'] == '')
            {

                $res['error'] = $red_err_str."文件第".$hgt."行款号".$styleData['style_sn']."未设置镶口属性！（请核实后操作）".$html_end;
                Util::jsonExit($res);
            }

            $styleStone_id = array();
            $styleStone_id['att_value_id'] = trim($styleStone['attribute_value'],',');
            $attrValList = $attrvalmodel->getAttributeValue($styleStone_id);
            foreach ($attrValList as $vallsit) 
            {
                # code...
                $styleStoneInfo[] = $vallsit['att_value_name'];
            }

            if(!in_array($LineInfo['stone'], $styleStoneInfo))
            {

                $res['error'] = $red_err_str."款号".$styleData['style_sn']."录入的镶口未在镶口属性信息里！（请核实后操作）".$html_end."目前已有镶口（".implode(",", $styleStoneInfo)."）;";
                Util::jsonExit($res);
            }

            $is_huokou = array();
            $is_huokou['style_sn'] = $styleData['style_sn'];
            $is_huokou['attribute_id'] = 75;//活口
            $styleHuokou = $attrmodel->getStyleAttributeByStyleId($is_huokou);
            $cat_type = $styleData['style_type'];
            if($cat_type == 2 || $cat_type == 10 || $cat_type == 11)
            {
                $check_finger = array();
                $check_finger['style_sn'] = $styleData['style_sn'];
                $check_finger['attribute_id'] = 5;//指圈
                //是否维护镶口信息；
                $styleFinger = $attrmodel->getStyleAttributeByStyleId($check_finger);

                if (empty($styleFinger) || $styleFinger['attribute_value'] == '')
                {

                    $res['error'] = $red_err_str."文件第".$hgt."行款号".$styleData['style_sn']."未设置指圈属性！（请核实后操作）".$html_end;
                    Util::jsonExit($res);
                }

                $styleFinger_id = array();
                $styleFinger_id['att_value_id'] = trim($styleFinger['attribute_value'],',');
                //是否维护指圈信息；
                $attrValListFinger = $attrvalmodel->getAttributeValue($styleFinger_id);
                foreach ($attrValListFinger as $vallsitf) 
                {
                    # code...
                    $styleFingerInfo[] = $vallsitf['att_value_name'];
                }

                if(!in_array($LineInfo['finger'], $styleFingerInfo))
                {

                    $res['error'] = $red_err_str."款号".$styleData['style_sn']."录入的手寸未在镶口属性信息里！（请核实后操作）".$html_end."目前已有手寸（".implode(",", $styleFingerInfo)."）;";
                    Util::jsonExit($res);
                }
            }
            //整合数据；
            $styleConInfo[] = $LineInfo;
        }

        $model = new AppXiangkouModel(12);
        //$pdo = $model->db()->db();
        //$pdo->beginTransaction();//开启事务；
        foreach ($styleConInfo as $ins_info) {
            # code...
            # 
            $check_info = $model->getXiangKouByStyle_Id($ins_info);
            if(empty($check_info) || !isset($check_info[0])){

                $result = $model->saveData($ins_info,array());
            }else{

                $ins_info['x_id'] = $x_id = $check_info[0]['x_id'];
                $model = new AppXiangkouModel($x_id, 12);
                $oldInfo = $model->getDataObject();
                $result = $model->saveData($ins_info,$oldInfo);
            }

            if($result == true){

                //$pdo->commit();
                //生成商品；（只有已审核状态的款和是否定制为是的款才能生成商品和销售商品）
                $styleDataIn = $stylemodel->getStyleStyleByStyle_sn($ins_info);
                $styleDataIn = $styleDataIn[0];
                if($styleDataIn['check_status'] == '3' && $styleDataIn['is_made'] == '1')
                {
                    $check_res = $this->createGoods($ins_info, $styleDataIn);
                }
            }else{
                //$pdo->rollBack();
                $result['error'] = "导入标准金重事物操作失败，事物回滚请重新导入！";
                Util::jsonExit($result);
            }
        }

        if($result){
            $res['error'] ="提示：批量上传成功，<span style='color:red;'>请核查！</span>";
            Util::jsonExit($res);   
        }
        $res['error'] = "批量上传失败，<span style='color:red;'>提交超时！</span>";
        Util::jsonExit($res);

    }

    /**
     *  trimall，删除空格
     */
    public function trimall($str)
    {

        //字符类型转换；
        $str = iconv('gbk','utf-8',$str);
        //数字不能为负数；
        if(is_numeric($str)){

            $str = abs($str);
        }
        //过滤字符串中用户不小心录入的的空格、换行、等特殊字符；
        $qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");

        return str_replace($qian,$hou,$str);
    }

    /**
     * cutFingerInfo，切割手寸
     */
    public function cutFingerInfo($finger)
    {

        $is_search = $this->checkString('-', $finger);
        $new_arr = array();
        if($is_search)
        {
            $tmp = explode('-', $finger);
            $min = intval($tmp[0]);
            $max = intval($tmp[1]);

            if($min == $max)
            {
                 $new_arr[] = $min;
            }
            else
            {
                for( $i=$min; $i<=$max; $i++)
                {
                    $new_arr[] = $i;
                }
            }
        }
        else
        {
             $new_arr[] = $data;
        }
        return $new_arr;
    }

    /**
     * checkString，查找字符串
     */
    public function checkString($search,$string) 
    {

        $pos = strpos($string, $search);
        if($pos == false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     *  createGoods，生成商品
     */
    public function createGoods($data, $styleinfo)
    {
        # code...
        # 
        $res = array('success' => 0,'error' => '');
        //取出改款的可做材质和可做颜色；
        $style_caizhi = array();
        $style_yanse = array();
        $attrInfo = $this->getRingAttribute($styleinfo['style_id'],$styleinfo['style_sn']);
        if($attrInfo['error']==1){

            $res['error'] = $attrInfo['message'];
            Util::jsonExit($res);
        }else{

            $style_caizhi = array_filter($attrInfo['data']['cz']);
            $style_yanse  = array_filter($attrInfo['data']['ys']);
        }

        $attributeValueModel = new AppAttributeValueModel(11);
        $color_arr = $attributeValueModel->getColor();
        $color_value_arr = $attributeValueModel->getColorValue();

        //生成商品
        //18K
        if(in_array("18K", $style_caizhi)){

            foreach ($style_yanse as $val){
                if(array_key_exists($val, $color_arr)){
                    $yanse_data[$color_value_arr[$val]] = $color_arr[$val];
                }
            }
            $caizhi = array('id'=>1,'name'=>"18K");
            $this->create_goods_insert($styleinfo, $data, $caizhi,$yanse_data);
        }
        
        //PT950
        if(in_array("PT950", $style_caizhi)){

            //只有一个颜色那就是白色
            $yanse_data_pt[$color_value_arr["白"]] = $color_arr["白"];
            $caizhi = array('id'=>2,'name'=>"PT950");
            $this->create_goods_insert($styleinfo, $data, $caizhi,$yanse_data_pt);
        }
    }

    /**
     *  getRingAttribute，产品线为戒指的属性
     */
    //戒指的相关属性
    public function getRingAttribute($style_id,$style_sn) {

        $error = 0;//默认没有错误
        $attributeModel = new AppAttributeModel(11);
        $xiangkou_data = $attributeModel->getAttributeInfoByName('镶口');
        $zhiquan_data = $attributeModel->getAttributeInfoByName('指圈');
        $caizhi_data = $attributeModel->getAttributeInfoByName('材质');
        $yanse_data = $attributeModel->getAttributeInfoByName('材质颜色');
        $xk_id = $xiangkou_data['attribute_id'];
        $zq_id = $zhiquan_data['attribute_id'];
        $cz_id = $caizhi_data['attribute_id'];
        $ys_id = $yanse_data['attribute_id'];
        $relStyleModel = new RelStyleAttributeModel(11);
        $xk_data = $relStyleModel->getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$xk_id));
        if(empty($xk_data)){
            $error = 1;
            return array('error'=>$error,'message'=>'此款'.$style_sn.'没有设置：镶口属性!');
        }
        if(empty($xk_data['attribute_value'])){
            $error = 1;
            return array('error'=>$error,'message'=>'此款'.$style_sn.'没有选择：镶口数据!');
        }
        //指圈
        $zq_data = $relStyleModel->getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$zq_id));
        //var_dump($zq_data);
        if(empty($zq_data)){
            $zq_data= array('attribute_value'=>'888,','product_type_id'=>16,'cat_type_id'=>14);
        }
        if(empty($zq_data['attribute_value'])){
            $error = 1;
            return array('error'=>$error,'message'=>'此款'.$style_sn.'没有选择：指圈数据!');
        }
        //材质
        $cz_data = $relStyleModel->getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$cz_id));
        if(empty($cz_data)){
            $error = 1;
            return array('error'=>$error,'message'=>'此款'.$style_sn.'没有设置：材质属性!');
        }
        if(empty($cz_data['attribute_value'])){
            $error = 1;
            return array('error'=>$error,'message'=>'此款'.$style_sn.'没有选择：材质数据!');
        }
        //可做颜色
        $ys_data = $relStyleModel->getStyleAttributeByStyleId(array('style_id'=>$style_id,'attribute_id'=>$ys_id));
        if(empty($ys_data)){
            $ys_data= array('attribute_value'=>'888,','product_type_id'=>16,'cat_type_id'=>14);
        }
        if(empty($ys_data['attribute_value'])){
            $error = 1;
            return array('error'=>$error,'message'=>'此款'.$style_sn.'没有选择：材质颜色数据!');
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
        $attributeValueModel = new AppAttributeValueModel(11);
        //镶口
        foreach ($xk_info as $val){

            $value = $attributeValueModel->getAttrNameByid(array('att_value_id'=>$val));
            $style_xiangkou[] = isset($value['att_value_name'])?$value['att_value_name']:'';
        }
        //指圈
        foreach ($zq_info as $val){

            if($val == 888){

                $style_zhiquan[]='0';
                continue;
            }
            $value = $attributeValueModel->getAttrNameByid(array('att_value_id'=>$val));
            $style_zhiquan[] = isset($value['att_value_name'])?$value['att_value_name']:'';
        }
        //材质
        foreach ($cz_info as $val){

            $value = $attributeValueModel->getAttrNameByid(array('att_value_id'=>$val));
            $style_caizhi[] = isset($value['att_value_name'])?$value['att_value_name']:'';
        }
        //材质颜色
        foreach ($ys_info as $val){
            if($val == 888){

                $style_yanse[] = '白色';
                continue;
            }

            $value = $attributeValueModel->getAttrNameByid(array('att_value_id'=>$val));
            $style_yanse[] = isset($value['att_value_name'])?$value['att_value_name']:'';
        }
        
        return array('error'=>$error,'data'=>array('xk'=>$style_xiangkou,'zq'=>$style_zhiquan,'cz'=>$style_caizhi,'ys'=>$style_yanse));
    }

    public function create_goods_insert($style_info,$xiangkou,$caizhi_info,$color_arr){
        /*print_r($style_info);
        print_r($xiangkou);
        print_r($caizhi_info);
        print_r($color_arr);die;*/
        $newmodel = new ListStyleGoodsModel(12);
        $apiSalePolicyModel = new ApiSalePolicyModel();
        $caizhi = $caizhi_info['id'];
        $caizhi_name = $caizhi_info['name'];
        $stone = $xiangkou['stone'];
        $finger = $xiangkou['finger'];
        $style_id = $style_info['style_id'];
        $style_sn = $style_info['style_sn'];
        $style_name = $style_info['style_name'];
        $product_type_id = $style_info['product_type'];
        $cat_type_id = $style_info['style_type'];

        $num = 0;
        $olddo = array();
        $fingerInfo = array();
        if($finger == 0){
            $fingerInfo[0] = 0;
        }else{
            $fingerInfo = $this->cutFingerInfo($finger);
        }
        foreach ($color_arr as $c_key=>$c_val){

            $color_name = $c_val;
            $where['style_id']=$style_id;
            $where['style_sn']=$style_sn;
            $where['product_type_id']=$product_type_id;//产品线id
            $where['cat_type_id']=$cat_type_id;//分类id
            $where['style_name'] = $style_name;//款式名称
            $where['caizhi']=$caizhi;//材质
            $where['yanse']=$c_key;//镶口
            $where['xiangkou'] = $stone;//镶口
            
            $where['zhushizhong']=$xiangkou['main_stone_weight']; //主石重 
            $where['zhushi_num']=$xiangkou['main_stone_num']; //主石数 
            //print_r($xiangkou);die;
            $where['fushizhong1']=$xiangkou['sec_stone_weight']; //副石1重 
            $where['fushi_num1']=$xiangkou['sec_stone_num']; //副石1数量
            $where['fushizhong2']=$xiangkou['sec_stone_weight_other']; //副石2重
            $where['fushi_num2']=$xiangkou['sec_stone_num_other'];// 副石2数量
            $where['fushizhong3']=$xiangkou['sec_stone_weight3']; //副石2重
            $where['fushi_num3']=$xiangkou['sec_stone_num3'];// 副石2数量
//                $where['fushi_chengbenjia_other']=$xiangkou['sec_stone_price_other'];// 其他副石成本价
            $where['dingzhichengben']=0;// 定制成本
            if($caizhi == 1){

                if($xiangkou['g18_weight'] == '0'){
                    continue;
                }

                $where['weight']=$xiangkou['g18_weight']; //18K标准金重
                $where['jincha_shang']=$xiangkou['g18_weight_more'];//18K金重上公差 
                $where['jincha_xia']=$xiangkou['g18_weight_more2'];// 18K金重下公差 
            }else{

                if ($xiangkou['gpt_weight'] == '0') {
                    continue;
                }
                $where['weight']=$xiangkou['gpt_weight'];//PT950标准金重 
                $where['jincha_shang']=$xiangkou['gpt_weight_more'];//PT950金重上公差 
                $where['jincha_xia']=$xiangkou['gpt_weight_more2'];//PT950金重下公差
            }
           
            $where['last_update']=date("Y-m-d H:i:s");
            //print_r($xiangkou['sec_stone_weight_other']);die;
            //print_r($xiangkou['sec_stone_weight_other']);die;
            if($xiangkou['sec_stone_weight_other']==""){
                 $where['fushizhong2']=0; //副石2重
            }
            if($xiangkou['sec_stone_num_other']==""){
                 $where['fushi_num2']=0;// 副石2数量
            }
            if($xiangkou['sec_stone_weight3']==""){
                 $where['fushizhong3']=0; //副石2重
            }
            if($xiangkou['sec_stone_num3']==""){
                 $where['fushi_num3']=0;// 副石2数量
            }
//                if($xiangkou['sec_stone_price_other']==""){
//                     $where['fushi_chengbenjia_other'] =0;// 其他副石成本价
//                }
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
            foreach ($fingerInfo as $k=>$val){
               $shoucun = $val;//手寸
               $where['shoucun']=$shoucun;//手寸
               $stone_name = $stone * 100;
               $goods_sn = $style_sn."-".$caizhi_name."-".$color_name."-".$stone_name."-".$shoucun;
 
               $quickDiyGoods = $newmodel->getQuickDiyGoodsByGoodsSn($goods_sn);
               if(!empty($quickDiyGoods)){                
                  $newmodel->deletegoods_sninfo($goods_sn);
               }
               $where['is_quick_diy'] = $quickDiyGoods['is_quick_diy']==1?1:0;
               $where['goods_sn'] = $goods_sn;
               $num++;
               $res = $newmodel->saveData($where,$olddo);                       
            }
        }
        
        if($style_id){
            $ret = $this->update_goods_price($style_id,$caizhi,$stone);
        }
        $goods_sn_arr = $newmodel->getAllGoodsinfo($style_id,$caizhi,$stone);
        foreach($goods_sn_arr as $val){
            $salepolicy_data = array('goods_id'=>$val['goods_sn'],'goods_sn'=>$val['style_sn'],'goods_name'=>$val['style_name'],'chengbenjia'=>$val['dingzhichengben'],'category'=>$val['cat_type_id'],'product_type'=>$val['product_type_id'],'isXianhuo'=>0,'is_base_style'=>0,'stone'=>$val['xiangkou'],'caizhi'=>$val['caizhi'],'yanse'=>$val['yanse'],'finger'=>$val['shoucun']);
            $apiSalePolicyModel->AddAppPayDetail(array('insert_data'=>$salepolicy_data));
        }
    }

    /*------------------------------------------------------ */
    //-- 更新商品成本价格
    //-- BY linian
    /*------------------------------------------------------ */
    public function update_goods_price($style_id,$caizhi,$stone) {

        $result = array('success' => 0, 'error' => '');
        //$style_id = _Post::getInt('id');
        $model = new ListStyleGoodsModel(11);
        //1,获取商品表中所有商品
        if($caizhi){
            $data = $model->getAllGoodsinfo($style_id,$caizhi,$stone);
        }
        
    
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
            $newmodel =  new AppStyleFeeModel(11);
            if(!empty($style_id)){
                //获取三种工费
                $gongfei='';
                $baomiangongyi_gongfei='';
                $chaoshifee='';
                $baoxianfei = '';
                $gongfei_data = $newmodel->getStyleFee($style_id);
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

                $productTypeModel = new AppProductTypeModel(11);
                $parent_id = $productTypeModel->getParentIdById($product_type_id);
                if($parent_id == 3 && $xiangkou != ''){
                    $baoxianfeeModel = new AppStyleBaoxianfeeModel(11);
                    $baoxianfei = $baoxianfeeModel->getPriceByXiangkou($xiangkou);
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
            $model = new AppJinsunModel(11);
            if(!empty($caizhi)){
                //材质
                //if($caizhi==1){
                //  $where['material_id']="18K";
                //}else{
                //  $where['material_id']="PT950";
                //}
                $where['material_id']=$caizhi;
                
                //2 女戒
                $where['price_type']=2;
                $jinsundata = $model->pageList($where,10);
                if($jinsundata['data']){
                    $jinsunlv = $jinsundata['data'][0]['lv'];
                }
            }
        
    
            //5,获取所有钻石规格单价数据
            //(副石1重/副石1数量)的对应单价*副石1重+（副石2重/副石2数量）的对应单价*副石2重
            $newmodel =  new AppDiamondPriceModel(19);
            if($fushi_num_1){
                $where['guige'] = 100 * $fushi_1 / $fushi_num_1;
                //获取副石1价格
                $diamondprice = $newmodel->getDanPrice($where);
                $fushi_price_1=$diamondprice['price']*$fushi_1;
            }else{
                $fushi_price_1='';
            }
            if($fushi_num_2){
                $where['guige'] = 100 * $fushi_2 / $fushi_num_2;
                //获取副石2价格
                $diamondprice = $newmodel->getDanPrice($where);
                $fushi_price_2=$diamondprice['price']*$fushi_2;
            }else{
                $fushi_price_2='';
            }
            if($fushi_num_3){
                $where['guige'] = 100 * $fushi_3 / $fushi_num_3;
                //获取副石3价格
                $diamondprice = $newmodel->getDanPrice($where);
                $fushi_price_3=$diamondprice['price']*$fushi_3;
            }else{
                $fushi_price_3='';
            }
            //var_dump($fushi_price_1,$fushi_price_2);
            //6,(材质金重+向上公差）*金损率* 对应材质单价
            //材质单价:price_type :1=》18K；2=>PT950; price:价格; type = 2
            $model = new AppMaterialInfoModel(11);
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
                $caizhidata = $model->pageList($where,10);
                $caizhi_price = $caizhidata['data'][0]['price'];
                $shuidian = $caizhidata['data'][0]['tax_point'];
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
            //var_dump($weight,$jincha_shang,$jinsun_price,$caizhi_price,$tal_jinsun);die;
            //9,计算定制成本价格
            $model = new ListStyleGoodsModel(12);
            //$aa = array('副石1'=>$fushi_price_1,'副石2'=>$fushi_price_2,'金损率'=>$tal_jinsun,'工费'=>$tal_gongfei);
            //var_dump($aa);
            $dingzhichengben = ($fushi_price_1 + $fushi_price_2 + $fushi_price_3 + $tal_jinsun + $tal_gongfei) * (1 + $shuidian);
            //var_dump('定制成本加个',$dingzhichengben);
            $where['chengbenjia'] = round($dingzhichengben,2);
            //var_dump($where['chengbenjia'],99);
            $where['goods_id'] =$goods_id;
           
            $res = $model->updateChengbenPrice($where);
  
            $chenbenjia[] =$where['chengbenjia'];
        }
        $salepolicyModel = new SalepolicyModel(18);
        if(!empty($goods_sn) && !empty($chenbenjia)){ 
            $salepolicyModel->UpdateSalepolicyChengben($goods_sn,$chenbenjia);
        }
        //var_dump($res);exit;
        //$ret = $model->UpdateSalepolicyChengben(array($goods_sn),array($chenbenjia));
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '更新价格失败';
        }
        return array('flag'=>$res);
    
    }

    public function download_demo() {
        $title = array('款号','镶口','手寸','副石1重','副石1数量','副石2重','副石2数量','副石3重','副石3数量','其他副石成本价','18K标准金重','18K金重上公差','18K金重下公差','PT950标准金重','PT950金重上公差','PT950金重下公差');
        $content = array(array("W240_001","0.2","11-13","0","0","0","0","0","0","0","2.48","0.25","0.25","3.29","0.35","0.35"),array("W240_001","0.2","11-13","0","0","0","0","0","0","0","2.48","0.25","0.25","3.29","0.35","0.35"));
        Util::downloadCsv("batch_import_jinzhong",$title,$content);
    }
}