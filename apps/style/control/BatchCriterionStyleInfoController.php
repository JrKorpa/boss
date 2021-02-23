<?php
/**
 * @Author: H丶xw
 * @Date:   2015-09-14 19:19:09
 * @Last Modified by:   anchen
 * @Last Modified time: 2016-06-24 17:11:44
 */

class BatchCriterionStyleInfoController extends CommonController
{

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('dow');

    /**
     *  index，搜索框
     */
    public function index ($params)
    {
        $this->render('batch_criterion_style_info.html');
    }

    /**
     *  uploadStyleInfoFile
     */
    public function uploadStyleInfoFile($params)
    {
        set_time_limit(0);
        $res = array('success' => 0,'error' => '');
        $red_err_str = "提示：<span style='color:red';>";
        $html_end = "</span> <br />";
        $fileInfo = $_FILES['style_file'];
        $tmp_name = $fileInfo['tmp_name'];
        if ($tmp_name == '') 
        {
            $res['error'] = $red_err_str."请选择上传文件！（请下载模版务必按照表头填写后上传）。".$html_end;
            Util::jsonExit($res);
        }
        $file_name = $fileInfo['name'];
        if (Upload::getExt($file_name) != 'csv') 
        {
            $res['error'] = $red_err_str."请上传.csv为后缀的文件！".$html_end;
            Util::jsonExit($res);
        }
        $fileData = fopen($tmp_name, 'r');
        while ($data = fgetcsv($fileData))
        {
            $styleInfo[] = $data;
        }
        if (count($styleInfo) <= 1)
        {
            $res['error'] = $red_err_str."未检测到数据，请填写后上传！".$html_end;
            Util::jsonExit($res);
        }
        /*if (count($styleInfo) >= 151)
        {
            $res['error'] = $red_err_str."上传数据过大会导致提交超时，不能超过150行信息！".$html_end;
            Util::jsonExit($res);
        }*/
        $model      = new BaseStyleInfoModel(11);
        $ptmodel    = new AppProductTypeModel(11);
        $catmodel   = new AppCatTypeModel(11);
        $ctInfo     = $catmodel->getCtlListon();
        $catList    = array_column($ctInfo, 'cat_type_name');
        $snCat      = array_combine(array_column($ctInfo, 'cat_type_name'), array_column($ctInfo, 'cat_type_id'));
        $ptInfo     = $ptmodel->getCtlListonPt();
        $ptList     = array_column($ptInfo, 'product_type_name');
        $snPt       = array_combine(array_column($ptInfo, 'product_type_name'), array_column($ptInfo, 'product_type_id'));
        /*$xiliemodel = new AppXilieModel(11);
        $xilieInfo  = array_combine(array_column($xiliemodel->getAllXilieName(), 'name'), array_column($xiliemodel->getAllXilieName(), 'id'));*/
        
        $fields = array('style_name','product_type','is_made','style_type','sell_type','check_status','dismantle_status','dapei_goods_sn','changbei_sn','is_zp','style_sex','bang_type','is_sales','market_xifen','style_remark','is_xz','zp_price','sale_way','is_allow_favorable','is_gold','is_support_style');
        if(isset($styleInfo[0]) && !empty($styleInfo[0])){
            $coldata = $styleInfo[0];
            foreach ($fields as $key => $value) {
                if(!in_array($value, $coldata)){
                    $res['error'] = $red_err_str.$value."表头错误！（请下载模版）".$html_end;
                    Util::jsonExit($res);
                }
            }
        }
        array_shift($styleInfo);
        array_shift($styleInfo);
        $error = '';
        $hgt = 2;
        $intfield = array('is_made', 'is_sales', 'dismantle_status', 'changbei_sn', 'style_sex', 'is_zp', 'sell_type', 'bang_type', 'sale_way', 'is_xz', 'is_allow_favorable', 'is_gold', 'is_support_style');
        foreach ($styleInfo as $key => $val) 
        {
            $hgt++;
            if (count($val) != 21)
            {
                $res['error'] = $red_err_str."文件第".$hgt."行请上传21列信息！（按照模版要求填写）".$html_end;
                Util::jsonExit($res);
            }

            $newInfo = array_combine($coldata, $val);
            foreach ($newInfo as $field => $value) {

                $LineInfo[$field] = $valSn = $this->trimall($value);
                if(in_array($field, $intfield) && is_numeric($field)){
                    $error.= $red_err_str."文件第".$hgt."行".$field."必须是数字！".$html_end;
                }
                if($field == 'style_name' && empty($valSn)){
                    $error.= $red_err_str."文件第".$hgt."行款式名称不能为空！".$html_end;
                }
                if($field == 'style_type' && empty($valSn)){
                    $error.= $red_err_str."文件第".$hgt."行款式分类不能为空！".$html_end;
                }
                if($field == 'product_type' && empty($valSn)){
                    $error.= $red_err_str."文件第".$hgt."行产品线不能为空！".$html_end;
                }
                if($field == 'is_allow_favorable' && $valSn === ''){
                    $error.= $red_err_str."文件第".$hgt."行是否允许改价不能为空！".$html_end;
                }
                if($field == 'is_gold' && $valSn === ''){
                    $error.= $red_err_str."文件第".$hgt."行是否黄金不能为空！".$html_end;
                }
                if($field == 'style_name' && strlen($valSn) >= 60){
                    $error.= $red_err_str."文件第".$hgt."行款式名称不要大于60个字符！".$html_end;
                }
                if($field == 'product_type' && !in_array($valSn, $ptList)){
                    $error.= $red_err_str."文件第".$hgt."行产品线不存在！".$html_end;
                }
                if($field == 'style_type' && !in_array($valSn, $catList)){
                    $error.= $red_err_str."文件第".$hgt."行款式分类不存在！".$html_end;
                }
                if($field == 'is_made' && !in_array($valSn, array(0, 1))){
                    $error.= $red_err_str."文件第".$hgt."行是否定制错误！（0否1是）".$html_end;
                }
                if($field == 'sell_type' && $valSn == ''){
                    $error.= $red_err_str."文件第".$hgt."行畅销度错误！（0否;1:是）".$html_end;
                }
                if($field == 'check_status' && !in_array($valSn, array(1, 2, 3))){
                    $error.= $red_err_str."文件第".$hgt."行审核状态错误！（1保存2提交申请3审核）".$html_end;
                }
                if($field == 'dismantle_status' && !in_array($valSn, array(1, 2, 3))){
                    $error.= $red_err_str."文件第".$hgt."行是否拆货错误！（1正常2允许拆货3已拆货）".$html_end;
                }
                if($field == 'dapei_goods_sn' && strlen($valSn) >= 60){
                    $error.= $red_err_str."文件第".$hgt."行搭配套系名称不要大于60个字符！".$html_end;
                }
                if($field == 'changbei_sn' && !in_array($valSn, array(1, 2))){
                    $error.= $red_err_str."文件第".$hgt."行是否常备款错误！（1是2否）".$html_end;
                }
                if($field == 'is_zp' && !in_array($valSn, array(1, 2))){
                    $error.= $red_err_str."文件第".$hgt."行是否赠品错误！（1否2是）".$html_end;
                }
                if($field == 'style_sex' && !in_array($valSn, array(1, 2, 3))){
                    $error.= $red_err_str."文件第".$hgt."行款式性别错误！（1男2女3中性）".$html_end;
                }
                if($field == 'bang_type' && !in_array($valSn, array(1, 2))){
                    $error.= $red_err_str."文件第".$hgt."行绑定错误！（1需要绑定2不需要绑定）".$html_end;
                }
                if($field == 'is_sales' && !in_array($valSn, array(0, 1))){
                    $error.= $red_err_str."文件第".$hgt."行是否销售错误！（0否1是）".$html_end;
                }
                /*if($field == 'xilie' && !in_array($valSn, $xilieInfo)){
                    $error.= $red_err_str."文件第".$hgt."行系列名称错误！请参考系列及款式归属信息。".$html_end;
                }*/
                if($field == 'market_xifen' && strlen($valSn) >= 50){
                    $error.= $red_err_str."文件第".$hgt."行市场细分不要大于50个字符！".$html_end;
                }
                if($field == 'is_xz' && !in_array($valSn, array(1, 2))){
                    $error.= $red_err_str."文件第".$hgt."行是否销帐错误！（2是1否）".$html_end;
                }
                if($field == 'sale_way' && !in_array($valSn, array(1, 2))){
                    $error.= $red_err_str."文件第".$hgt."行可销售渠道错误！（1线上2线下）".$html_end;
                }
                if($field == 'is_allow_favorable' && !in_array($valSn, array(1))){
                    $error.= $red_err_str."文件第".$hgt."行是否允许改价错误！（0否, 1是）".$html_end;
                }
                if($field == 'is_gold' && !in_array($valSn, array(0, 1, 2, 3, 4, 5, 6, 7, 8))){
                    $error.= $red_err_str."文件第".$hgt."行是否是黄金错误！（0、非黄金、1、瑞金2、3D  3、一口价、4.普通金条、5.PT990 、6.PT950 、7.刚泰金条、8.刚泰其他投资黄金）".$html_end;
                }
                if($field == 'is_support_style' && !in_array($valSn, array(0, 1))){
                    $error.= $red_err_str."文件第".$hgt."行是否支持按款销售错误！（0否, 1是）".$html_end;
                }
            }
            $styleConInfo[] = $LineInfo;
        }
        if($error != ''){
            $res['error'] = $error;
            Util::jsonExit($res);
        }
        $basemodel      = new BaseStyleInfoModel(12);
        $pdo = $basemodel->db()->db();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
        $pdo->beginTransaction();//开启事务
        try{
            foreach ($styleConInfo as $lt) {
                
                $lt['product_type'] = $snPt[$lt['product_type']];
                $lt['style_type']   = $snCat[$lt['style_type']];
                $lt['create_time']  = date('Y-m-d H:i:s', time());
                $lt['modify_time']  = date('Y-m-d H:i:s', time());
                if($lt['check_status'] == 3){
                    $lt['check_time']  = date('Y-m-d H:i:s', time());
                }

                $style_sn = $this->getStyleNumber(array('style_sex'=>$lt['style_sex'],'style_type'=>$lt['style_type']));
                $lt['style_sn']     = $style_sn;
                $last_id = $basemodel->saveData($lt, array());
                if($last_id !== false){
                    $id_len = strlen($last_id)*-1;
                    $newStyleSn = substr($style_sn,0,$id_len).$last_id;
                    $basemodel->updateStylesn($last_id,$newStyleSn); 
                    $basemodel->addBaseStyleLog(array('style_id'=>$last_id,'remark'=>'批量导入款式'));
                }else{
                    $pdo->rollback();
                    $res['error'] = '提交失败！';
                    Util::jsonExit($res);
                }
            }
        }catch (Exception $e){
            $pdo->rollback();
            $res['error'] =$red_err_str."操作失败，事物回滚！".$html_end."提示：系统批量提交事物时发生异常！error code:".__LINE__;
            Util::jsonExit($res);
        }

        $pdo->commit();
        $res['error'] = '提交成功！';
        Util::jsonExit($res);
    }

    public function trimall($str)
    {
        $str = iconv('gbk','utf-8',$str);
        $str = is_numeric($str)?abs($str):$str;
        $rpe = array(" ","　","\t","\n","\r");
        $hou = array("","","","","");
        return str_replace($rpe,$hou,$str);
    }

    public function getStyleNumber($data) {

        $catModel   = new AppCatTypeModel(11);
        $styleModel = new BaseStyleInfoModel(11);
        $style_sex_arr  = array("1"=>"M","2"=>"W","3"=>"X");
        $cat_code       = $catModel->getCatCode($data['style_type']);
        $style_sex      = $style_sex_arr[$data['style_sex']];
        $style_sn_prefix = 'KL'.$cat_code.$style_sex;
        $data['style_sn_prefix'] = $style_sn_prefix;

        $res = $styleModel->getLatestStyleSnByWhere($data);
        if(!empty($res)){                      
            $nextStyleNo = substr($res['style_sn'], -6)  + 1;
        }else{
            $nextStyleNo = 1;
        }
        return $style_sn_prefix.str_pad($nextStyleNo, 6,'0',STR_PAD_LEFT);
    }
    
    public function dow() {
        $title = array('style_name','product_type','is_made','style_type','sell_type','check_status','dismantle_status','dapei_goods_sn','changbei_sn','is_zp','style_sex','bang_type','is_sales','market_xifen','style_remark','is_xz','zp_price','sale_way','is_allow_favorable','is_gold','is_support_style');
        $content = array(array("*款式名称","产品线（***）","是否定制(0:否;1:是)（***)","款式分类(***)","畅销度(***)默认值","审核状态(1保存2提交申请3审核4未通过5作废 )(***)全为3","是否拆货(1:正常;2:允许拆货;3:已拆货)(***)","搭配套系名称","是否常备款;1,是；2,否(***)","是否是赠品；1否，2是( ***)","款式性别;1:男；2：女；3：中性 (***) ","绑定1：需要绑定，2：不需要绑定(***)","是否销售(0:否;1:是)(***)","市场细分"," 记录备注"," 是否销账,2.是.1否(***)","赠品售价(***)","可销售渠道. 1线上，2线下(***)","是否允许改价","是否是黄金 0:非黄金，1:瑞金 2:3D  3:一口价","是否支持按款销售"),array("XX","宝石","0","吊坠","1","3","1","1","1","1","2","1","1","1","1","2","0","1","1","3","1"));
        Util::downloadCsv("up_style_info".time(),$title,$content);
    }
}