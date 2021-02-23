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
class BatchCriterionMoHaoController extends CommonController
{
	protected $smartyDebugEnabled = true;
    protected $whitelist = array('download_xiangkou_demo');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('batch_criterion_mohao.html');
	}
	
	/**
	 *	uploadfile
	 */
	public function uploadfile ($params)
	{
        $res = array('error'=>1,'msg'=>'');

        $upload_name = $_FILES['fileinfo_mohao'];
        $tmp_name = $upload_name['tmp_name'];
        if (!$tmp_name) {

            $res['msg'] ="文件不能为空！";
            Util::jsonExit($res);
        }
        if (Upload::getExt($upload_name['name']) != 'csv') {

            $res['msg'] ="请上传csv格式文件！";
            Util::jsonExit($res);
        }
        $file = fopen($tmp_name,'r');

        $style_data = array();
        while ($data = fgetcsv($file))
        {
            $style_data[] = $data;
        }

        array_shift($style_data);

        if(count($style_data) < 1){

            $res['msg'] ="请上传至少1条数据！";
            Util::jsonExit($res);
        }
        if(count($style_data) > 150){

            $res['msg'] ="请上传少于150条款的信息！";
            Util::jsonExit($res);
        }

        $is_have_arr = array();
        $real_style_v3xiangkou=array();
        
        $allProcessorInfo = array();
        

        $processorList = $this->getProcessorList();
        if (!empty($processorList['data'])) {
            $allProcessorInfo = $processorList['data'];
        }
        $model = new RelStyleFactoryModel(11);
        $style_model = new AppFactoryApplyModel(12);
        $model_info = new BaseStyleInfoModel(11);
        $pdo = $style_model->db()->db();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
        $pdo->beginTransaction();//开启事务
        foreach ($style_data as $k=>$v){

            $app_facroty_apply = array();
            $app_facroty_have = array();
            if(count($v) != 5){

                $res['msg'] ="请上传5列信息！";
                Util::jsonExit($res);
            }

            foreach($v as $key => $val){

                $v[$key]=trim(iconv('gbk','utf-8',$val));
            }

            if($v[3] == ''){
                $v[3] = 0;
            }

            $style_sn = trim($v[0]);
            $factory_name = trim($v[1]);
            $factory_sn = trim($v[2]);
            $xiangkou = trim($v[3]);
            $gongfei = trim($v[4]);
            $defaultMohao = trim($v[5]);
            $defaultJinz = trim($v[6]);

            if(preg_match("/[\x7f-\xff]/", $v[4])){

                $res['msg'] ="工费只能输入金额！";
                Util::jsonExit($res);
            }

            //var_dump($style_sn,$factory_name,$factory_sn,$xiangkou,$defaultMohao,$defaultJinz);die;
            $factory_id = '';
            //是否工厂
            foreach ($allProcessorInfo as $ks => $vs) {
                if($vs['name'] == $factory_name){
                    $factory_id = $vs['id'];
                }
            }
            if(empty($factory_id)){

                $res['msg'] ="没有“{$factory_name}”工厂，或工厂没启用！";
                Util::jsonExit($res);
            }

            $where_sn = array();
            $where_sn['style_sn'] = $style_sn;
            $style_id = $model_info->getStyleByStyle_sn($where_sn);
            if(empty($style_id)){

                $res['msg'] ="没有此款“{$style_sn}”的信息，请上传正确的款式！";
                Util::jsonExit($res);
            }

            $style_where['factory_id'] = $factory_id;
            $style_where['xiangkou'] = $xiangkou;
            $style_where['factory_sn'] = $factory_sn;
            $is_exist = $model->getStyleIdByFactoryInfo($style_where);
            if(!empty($is_exist)){

                $style_info = $model_info->getStyleById($is_exist['style_id']);
                $v['style_sn'] = $style_info['style_sn'];
                $real_style_v3xiangkou[] = $v;
            }

            $is_factoryapply = $style_model->getStyleFactoryList($style_where);
            if(!empty($is_factoryapply)){
                $style_info = $model_info->getStyleById($is_factoryapply[0]['style_id']);
                $v['style_sn'] = $style_info['style_sn'];
                $app_facroty_apply[] = $v;
            }
            $where['style_id'] = $style_id[0]['style_id'];
            $where['xiangkou'] = $xiangkou;
            $where['factory_id'] = $factory_id;
            $is_have = $model->getStyleIdByFactoryInfo($where);
            if($is_have){

                $is_have_arr[] = $v;
            }

            $is_apply_have = $style_model->getStyleFactoryList($where);
            if(!empty($is_apply_have)){

                $app_facroty_have[] = $v;
            }
            if($app_facroty_apply || $app_facroty_have){
                $app_ly = array();
                $app_ly['factory_fee'] = $v[4];
                if(!empty($app_facroty_apply)){
                    $app_ly['apply_id'] = $is_factoryapply[0]['apply_id'];
                }
                if(!empty($app_facroty_have)){
                    $app_ly['apply_id'] = $is_apply_have[0]['apply_id'];
                }
                $r = $style_model->updateStyleFactoryFee($app_ly);
                if($r == false){
                    $pdo->rollback();
                    $res['msg'] = '更新工费信息失败！';
                    Util::jsonExit($res);
                }
            }
        }
        if($real_style_v3xiangkou || $is_have_arr || $app_facroty_apply || $app_facroty_have){
            $msg = '同一工厂，模号，镶口如果有重复的，导入不成功<hr>';
            if($real_style_v3xiangkou){
                foreach ($real_style_v3xiangkou as $v){
                    $msg .= '该条数据'.$v[0]." ".$v[1]." ".$v[2]." ".$v[3]."此工厂模号镶口在".$v['style_sn']."款存在<hr>";
                }
            }
            if($app_facroty_apply){
                foreach ($app_facroty_apply as $v){
                    $msg .= '该条数据'.$v[0]." ".$v[1]." ".$v[2]." ".$v[3]."此工厂模号镶口在".$v['style_sn']."款已申请<hr>";
                }
            }
            if($is_have_arr){
                $msg .= "同一款，同一工厂，同一镶口，如果模号不同，导入不成功<hr>";
                foreach ($is_have_arr as $val){
                    $msg .= '该条数据'.$val[0]." ".$val[1]." ".$val[2]." ".$val[3]."此工厂镶口已存在其他模号"."<hr>";
                }
            }
            if($app_facroty_have){
                $msg .= "同一款，同一工厂，同一镶口，如果模号不同，导入不成功<hr>";
                foreach ($app_facroty_have as $val){
                    $msg .= '该条数据'.$val[0]." ".$val[1]." ".$val[2]." ".$val[3]."此工厂镶口已申请其他模号"."<hr>";
                }
            }

            $res['msg'] =$msg;
            Util::jsonExit($res);
        }

        try{
            foreach ($style_data as $k=>$v){

                $style_sn_in = '';
                $style_sn_in = trim($v[0]);

                $tInfo = array();
                $tInfo['style_sn'] = $style_sn_in;

                $styleInfo = array();
                $styleInfo = $model_info->getStyleByStyle_sn($tInfo);

                foreach($v as $key => $val){
                    $v[$key]=trim(iconv('gbk','utf-8',$val));
                }

                $factory_id = '';
                foreach ($allProcessorInfo as $x=> $y) {
                    if($y['name'] == $v[1]){

                        $factory_id = $y['id'];
                    }
                }

                if($v[3] == ''){
                    $v[3] = 0;
                }

                $data = array();
                $data['style_id'] = $styleInfo[0]['style_id'];
                $data['style_sn'] = trim($v[0]);
                $data['xiangkou'] = trim($v[3]);
                $data['factory_sn'] = trim($v[2]);
                $data['factory_id'] = trim($factory_id);
                $data['factory_name'] = trim($v[1]);
                $data['factory_fee'] = trim($v[4]);
                $data['type'] = 1;
                $data['make_name'] = $_SESSION['userName'];
                $data['crete_time'] = date('Y-m-d H:i:s',time());
                 $r = $style_model->saveData($data);
                if($r == false){
                    $pdo->rollback();
                    $res['msg'] = "导入失败！";
                    Util::jsonExit($res);
                }
            }
        }catch (Exception $e){
            $pdo->rollback();
            $res['error'] =$red_err_str."操作失败，事物回滚！".$html_end."提示：系统批量提交事物时发生异常！error code:".__LINE__;
            Util::jsonExit($res);
        }
        $pdo->commit();
        $res['error'] = 0;
        $res['msg'] = "导入成功！";
        Util::jsonExit($res);
    }

    public function download_xiangkou_demo() {
        $title = array('款号','工厂','模号','镶口','工费');
        $content = array(array("W240_001","华世伦","HSLB0043-20","0.2","60",),array("W240_002","华世伦","HSLB0043-20","0.2","60"));
        Util::downloadCsv("batch_import_factory_mohao",$title,$content);
    }

    public function getProcessorList() {
        $apiProcessorModel = new ApiProcessorModel();
        $info = $apiProcessorModel->GetSupplierList();
        return $info;
    }
}
?>