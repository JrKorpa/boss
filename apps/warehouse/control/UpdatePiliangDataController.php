<?php
/**
 *  -------------------------------------------------
 *   @file		: TestController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: JUAN <82739364@qq.com>
 *   @date		: 2015-01-14 11:04:15
 *   @update	:
 *  仓储管理-仓储单据-单据查询
 *  -------------------------------------------------
 */


class UpdatePiliangDataController extends CommonController
{
	protected $whitelist = array('upload','downcsv');
	public function index($params)
	{
            $this->render('update_piliang.html');exit;
            //$this->main();
	}
	//上传附件
	public function upload($params)
	{
            $result = array('success' => 0,'error' =>'');
            
	    $model = new UpdatePiliangModel(21);
             $model->uploadExcel();
            
	}


	function main(){
	
	echo <<<HTML
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<form action="index.php?mod=warehouse&con=UpdatePiliangData&act=index" method="post" enctype="multipart/form-data">
上传文件<input type="file" name="file"><a href="index.php?mod=warehouse&con=UpdatePiliangData&act=downcsv">下载模版</a>
<br/>
<input type="hidden" name="act" value="upload">
<input type="submit" value="上传并修改">
</form>
</body>
</html>
HTML;
	exit;
}

public function downcsv(){
        $title = array(
            '货号',
            '模号',
        );

        Util::downloadCsv("update_mo_sn",$title,'');
    }
}
?>