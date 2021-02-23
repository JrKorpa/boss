<?php
/**
 *  -------------------------------------------------
 *   @file		: StyleSaleReportController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	:luochuanrong
 *   @date		: 2018-03-05 10:26:59
 *   @update	:
 *  -------------------------------------------------
 */
class StyleSaleReportController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('style_sale_report_search_form.html',array('bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
     	$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'date_start' => _Request::get("date_start"),
			'date_end' => _Request::get("date_end"),
			'style_sn' => _Request::get("style_sn"),
		);
        if(!empty($args['style_sn'])){
			$args['style_sn']=preg_replace("/[sv]+/",'',$args['style_sn']);
			$args['style_sn']=str_replace(" ",',',$args['style_sn']);
			$args['style_sn']=str_replace("，",',',$args['style_sn']);
			//add end
			$item =explode(",",$args['style_sn']);
			$style_sn = "";
			foreach($item as $key => $val) {
				if ($val != '') {
					if($style_sn){
						$style_sn .= ",'".trim($val)."'";
					}else{
						$style_sn .= "'".trim($val)."'";
					}
				}
			}
			$args['style_sn'] = $style_sn;
		}	
		//print_r($args);
		$model = new StyleSaleReportModel(21);
		$data = $model->pageList($args);
		//echo "<pre>";
        //print_r($data);
       
        if(_Request::get("down_infos")=='downs'){
            $this->dow($data);
        	exit();
        }

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'style_sale_report_search_page';
		$this->render('style_sale_report_search_list.html',array(
			'pa'=>Util::page($pageData),
			'data'=>$data
		));

	}

	
	

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('style_sale_report_show.html',array(
			'view'=>new StyleSaleReportView(new StyleSaleReportModel($id,21)),
			'bar'=>Auth::getViewBar()
		));
	}


    //导出
    public function dow($params){
        set_time_limit(0);
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312','导出').time().".xls");
        $day_str="";      
        /*
        set_time_limit(0);
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312','导出').time().".xls");
        $day_str="";
       
        if(!empty($data['dates'])){
	        foreach ($data['dates'] as $key => $date) {
	        	$day_str .="<td>".$date."</td>";
	        }
	    }    
        $csv_header="<table><tr><td>款号</td><td>渠道</td>".$day_str."</tr>";
        
        $csv_body="";
        if(!empty($data['style_sns']) && !empty($data['qudaos']) && !empty($data['dates']) && !empty($data['data'])){
	        foreach ($data['style_sns'] as $key1 => $style_sn) {
	        	foreach ($data['qudaos'] as $key2 => $qudao){
	        		$csv_body .="<tr><td>{$style_sn}</td><td>{$qudao}</td>";
	        		foreach ($data['dates'] as $key3 => $day){
                        $csv_body .="<td>{$data['data'][$style_sn][$qudao][$day]['sale_num']}</td>";
	        		}
	        		$csv_body .="</tr>";
	        	}
	        }
	    }    
        $csv_footer="</table>";
        echo $csv_header.$csv_body.$csv_footer;       
        */           
    }	

}

?>