<?php
/**
 *  -------------------------------------------------
 *   @file		: UpdateQudaoBumenController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 11:04:15
 *   @update	:
 *
 *  -------------------------------------------------
 */
class BatchCriterionXiLieController extends CommonController
{
    protected $smartyDebugEnabled = true;
    protected $whitelist = array('download_xiangkou_demo');

    /**
     *	index，搜索框
     */
    public function index($params)
    {
        $this->render('batch_criterion_xilie.html');
    }

    /**
     *	uploadfile
     */
    public function uploadfile($params)
    {
        $res = array('error' => 1, 'msg' => '');
        $xilie_arr = array(
            1 => '天鹅湖',
            2 => '天使之吻',
            3 => '怦然心动',
            4 => 'UNO',
            5 => '天使之翼',
            6 => '星耀',
            7 => '小黄鸭',
            8 => '天生一对',
            9 => '基本款',
            10 => 'O2O爆款',
            11 => '轻奢',
            12 => 'PINK EMILY',
            13 => 'Attractive',
            14 => '缤纷',
            15 => '挚爱',
            16 => '城堡');
        $upload_name = $_FILES['fileinfo_xilie'];
        $tmp_name = $upload_name['tmp_name'];
        if (!$tmp_name) {

            $res['msg'] = "文件不能为空";
            Util::jsonExit($res);
        }
        if (Upload::getExt($upload_name['name']) != 'csv') {

            $res['msg'] = "请上传csv格式文件！";
            Util::jsonExit($res);
        }
        $file = fopen($tmp_name, 'r');

        $style_data = array();

        while ($data = fgetcsv($file)) {
            $style_data[] = $data;
        }

        array_shift($style_data);

        if (count($style_data) < 1) {

            $res['msg'] = "请上传至少1条数据！";
            Util::jsonExit($res);
        }
        if (count($style_data) > 150) {

            $res['msg'] = "请上传少于150条款的信息！";
            Util::jsonExit($res);
        }


        $model = new RelStyleFactoryModel(12);
        $style_model = new AppFactoryApplyModel(12);
        $model_info = new BaseStyleInfoModel(12);
        $error['flag']=true;
        
        foreach ($style_data as $k => $v) {
           
            
            if (count($v) <= 2) {

                $res['msg'] = "请至少上传2列信息！";
                Util::jsonExit($res);
            }

            foreach ($v as $key => $val) {

                $v[$key] = trim(iconv('gbk', 'utf-8', $val));
            }

            $style_sn = trim($v[0]);
            $xilie = trim($v[1]);
            $xilie2 = trim($v[2]);
            $xilie3 = trim($v[3]);


            //var_dump($style_sn,$factory_name,$factory_sn,$xiangkou,$defaultMohao,$defaultJinz);die;


            $where_sn = array();
            $where_sn['style_sn'] = $style_sn;
            if(!empty($xilie)){
            $xilie_id = $model_info->getXiLieIdByName($xilie);
            }else{
                $xilie_id='nul';
            }
            if(!empty($xilie2)){
            $xilie_id2 = $model_info->getXiLieIdByName($xilie2);
            
            }else
            {
                $xilie_id2='nul';
            }
            if(!empty($xilie3)){
                $xilie_id3 = $model_info->getXiLieIdByName($xilie3);
            }
            else
            {
                $xilie_id3='nul';
            }
            $style_id = $model_info->getStyleByStyle_sn($where_sn);
            $style_status=$model_info->getStyleStatus($style_sn);
       
           
            if(!empty($style_status))
            {
                $error[ $k ][] = "{$style_sn} 款式“{$style_status}”，请上传正确的款式！</br>";
                $error['flag'] = FALSE;
            }
            if (empty($style_id)) {
                $error[ $k ][] = "没有此款“{$style_sn}”的信息，请上传正确的款式！</br>";
                $error['flag'] = FALSE;
            }
            if(empty($xilie))
            {
                $error[ $k ][] = "第一个系列不能为空</br>";
                $error['flag'] = FALSE;
            }
            $xilie_null = $model_info->getStyleXiLieBySn($style_sn);

            if (empty($xilie_null)) {
                if(empty($xilie_id2)){
                    $error[ $k ][] = "没有第二个“{$xilie2} ”的信息或者系列已被禁用，请上传正确的系列！</br>";
                    $error['flag'] = FALSE;
                } if(empty($xilie_id3)){
                    $error[ $k ][] = "没有第三个“{$xilie3} ”的信息或者系列已被禁用，请上传正确的系列！</br>";
                    $error['flag'] = FALSE;
                } 
                if (empty($xilie_id)) {
                    $error[ $k ][] = "没有第一个“{$xilie} ”的信息或者系列已被禁用，请上传正确的系列！</br>";
                    $error['flag'] = FALSE;
                }
                else {
                    $yanzheng = $model_info->getStyleXiLieByStyleSn($style_sn, $xilie_id);

                    if (!empty($yanzheng)) {
                        $error[ $k ][] = "款号“{$style_sn}”第一个系列在该款中已存在</br>";
                        $error['flag'] = FALSE;
                    }
                    if (!empty($xilie_id2)) {
                        $yanzheng2 = $model_info->getStyleXiLieByStyleSn($style_sn, $xilie_id2);
                        if (!empty($yanzheng2)) {
                            $error[ $k ][] = "款号“{$style_sn}”第二个系列在该款中已存在</br>";
                           $error['flag'] = FALSE;
                        }
                    }
                    if (!empty($xilie_id3)) {
                        $yanzheng3 = $model_info->getStyleXiLieByStyleSn($style_sn, $xilie_id3);
                        if (!empty($yanzheng3)) {
                            $error[ $k ][] = "款号“{$style_sn}”第三个系列在该款中已存在</br>";
                           $error['flag'] = FALSE;
                        }
                    }
                }
            } else {
                if(empty($xilie_id2)){
                    $error[ $k ][] = "没有第二个“{$xilie2} ”的信息或者系列已被禁用，请上传正确的系列！</br>";
                    $error['flag'] = FALSE;
                } if(empty($xilie_id3)){
                    $error[ $k ][] = "没有第三个“{$xilie3} ”的信息或者系列已被禁用，请上传正确的系列！</br>";
                    $error['flag'] = FALSE;
                }
                if (empty($xilie_id)) {
                    $error[ $k ][] = "没有第一个“{$xilie}”的信息或者系列已被禁用，请上传正确的系列！</br>";
                    $error['flag'] = FALSE;
                }  else {
                    $yanzheng = $model_info->getStyleXiLieByStyleSn($style_sn, $xilie_id);

                    if (!empty($yanzheng)) {
                        $error[ $k][] = "款号“{$style_sn}”第一个系列在该款中已存在</br>";
                        $error['flag'] = FALSE;
                    }
                    if (!empty($xilie_id2)) {
                        $yanzheng2 = $model_info->getStyleXiLieByStyleSn($style_sn, $xilie_id2);
                        if (!empty($yanzheng2)) {
                            $error[ $k][] = "款号“{$style_sn}”第二个系列在该款中已存在</br>";
                            $error['flag'] = FALSE;
                        }
                    }
                    if (!empty($xilie_id3)) {
                        $yanzheng3 = $model_info->getStyleXiLieByStyleSn($style_sn, $xilie_id3);
                        if (!empty($yanzheng3)) {
                            $error[ $k][] = "款号“{$style_sn}”第三个系列在该款中已存在</br>";
                            $error['flag'] = FALSE;
                        }
                    }
                    
                }
            }
            
        }
       if(!$error['flag']){
            //发生错误
            unset($error['flag']);
            $str = '';
            $ka=1;
            foreach($error as $k=>$v1){
                $s = implode(',',$v1);
                $ka=$k+1;
                $str.='第'.$ka.'行'.$s.'<br/>';
            }
            $res['msg'] = $str;
            Util::jsonExit($res);
        }

         //指针重新回到开始
        rewind($file);
        foreach ($style_data as $k => $v) {

            foreach ($v as $key => $val) {

                $v[$key] = trim(iconv('gbk', 'utf-8', $val));
            }

            $style_sn = trim($v[0]);
            $xilie = trim($v[1]);
            $xilie2 = trim($v[2]);
            $xilie3 = trim($v[3]);
            //var_dump($style_sn,$factory_name,$factory_sn,$xiangkou,$defaultMohao,$defaultJinz);die;
            $where_sn = array();
            $where_sn['style_sn'] = $style_sn;

            $xilie_id = $model_info->getXiLieIdByName($xilie);

            $xilie_id2 = $model_info->getXiLieIdByName($xilie2);
            $xilie_id3 = $model_info->getXiLieIdByName($xilie3);

            $style_id = $model_info->getStyleByStyle_sn($where_sn);
            $xilie_null = $model_info->getStyleXiLieBySn($style_sn);

            if (empty($xilie_null)) {
                $xilieid = "," . $xilie_id . ",";
                if (!empty($xilie_id2)) {
                    $xilieid = "," . $xilie_id . "," . $xilie_id2 . ",";
                }
                if (!empty($xilie_id3)) {
                    $xilieid = "," . $xilie_id . "," . $xilie_id2 . "," . $xilie_id3 . ",";
                }
                $where_sn['xilieid'] = $xilieid;
                $model_info->updateXiLieByStyleSn($where_sn);
            } else {
                $xilieid = "," . $xilie_id;
                if (!empty($xilie_id2)) {
                    $xilieid = "," . $xilie_id . "," . $xilie_id2;
                }
                if (!empty($xilie_id3)) {

                    $xilieid = "," . $xilie_id . "," . $xilie_id2 . "," . $xilie_id3;
                }
                $where_sn['xilieid'] = $xilieid;
                $model_info->updateXiLieByStyleSn($where_sn);
            }
        }
    }

    public function download_xiangkou_demo()
    {
        $title = array(
            '款号',
            '系列1',
            '系列2',
            '系列3');
        //$content = array(array("W240_001","华世伦","HSLB0043-20","0.2","60",),array("W240_002","华世伦","HSLB0043-20","0.2","60"));
        Util::downloadCsv("batch_import_style_xilie", $title, $content);
    }

    public function getProcessorList()
    {
        $apiProcessorModel = new ApiProcessorModel();
        $info = $apiProcessorModel->GetSupplierList();
        return $info;
    }
}
?>