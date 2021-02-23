<?php
/**
 *  -------------------------------------------------
 *   @file		: AppCouponTypeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-11 16:52:55
 *   @update	:
 *  -------------------------------------------------
 */
class SeriesGoodsReportController extends Controller
{
	protected $smartyDebugEnabled = false;
    public $limit_time = '2019-01-01';

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $goods_status = array(1=>'收货中',2=>'库存',3=>'已销售',4=>'盘点中',5=>'调拨中',6=>'损益中',7=>'已报损',8=>'返厂中',9=>'已返厂',10=>'销售中',11=>'退货中',12=>'作废');
		$this->render('series_goods_report_search_form.html',array('bar'=>Auth::getBar(),'goods_status'=>$goods_status));
	}

	/**
     *  search，列表
     */
    public function search ($params)
    {
        $args = array(
            'mod'   => _Request::get("mod"),
            'con'   => substr(__CLASS__, 0, -10),
            'act'   => __FUNCTION__,
            'style_sn' => _Request::getString("style_sn"),
            'goods_status' => _Request::getString("goods_status"),
            'time_start' => _Request::getString("time_start"),
            'time_end' => _Request::getString("time_end")
        );

        if(!empty($args['time_start']) && $args['time_start']<$this->limit_time && SYS_SCOPE == 'zhanting'){
            $args['time_start'] = $this->limit_time;
        }
        
        $user_name = $_SESSION['userName'];
        $model = new SeriesGoodsReportModel(15);
        $xilie_sc = $model->getVisitByUserNmae($user_name);

        if(!$xilie_sc){
            echo '无权限，请配置系列权限！';exit;
        }
        $xilie_sc = trim($xilie_sc,",");
        $xilie_arr = array();
        $xilie_arr = explode(',',$xilie_sc);
        $page = _Request::getInt("page",1);
        $where = array(
            'goods_sn' => $args['style_sn'],
            'goods_status' => $args['goods_status'],
            'xilie' => $xilie_arr,
            'time_start' => $args['time_start'],
            'time_end' => $args['time_end']
        );
        $data = $model->pageList($where,$page,10,false);
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'series_goods_report_search_page';
        $this->render('series_goods_report_search_list.html',array(
            'pa'=>Util::page($pageData),
            'page_list'=>$data
        ));
    }
}
?>