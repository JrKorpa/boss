<?php
/**
 *  -------------------------------------------------
 *   @file		: UpdateWGDiaGoodsSnController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: JUAN <82739364@qq.com>
 *   @date		: 2015-01-14 11:04:15
 *   @update	:
 *  仓储管理-商品管理-批量修改裸石款号
 *  -------------------------------------------------
 */


class UpdateWGDiaGoodsSnController extends CommonController
{
	protected $whitelist = array('upload','dow');
	public function index($params)
	{
            $this->render('updatewgdiagoodssn_search_form.html');exit;
            //$this->main();
	}
	//上传附件
	public function upload($params)
	{
            ini_set('memory_limit','-1');
            set_time_limit(0);
            $result = array('success' => 0,'error' =>'');
            
	    $model = new UpdatePiliangModel(21);
             $model->uploadWGDExcel();
            
	}

	//模板
	public function dow($params)
	{
        $title = array(
				'货号',
                '款号',
                'GEMX证书号'
                );
        $data[0]['goods_id']="1010000106";
        $data[0]['goods_sn']="DIA";
        $data[0]['goods_gemx']="G000000";
        $data[1]['goods_id']="150702660122";
        $data[1]['goods_sn']="LS000040";
        $data[1]['goods_gemx']="G000000";
            
        Util::downloadCsv("批量修改裸石款号",$title,$data);
            
	}

    
}
?>