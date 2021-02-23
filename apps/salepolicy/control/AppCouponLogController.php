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
class AppCouponLogController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_coupon_log_search_form.html',array('bar'=>Auth::getBar()));
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
            'exchange_name' => _Request::get("exchange_name"),
            'time_start' => _Request::getString("time_start"),
            'time_end' => _Request::getString("time_end")
        );

        $page = _Request::getInt("page",1);
        $where = array(
            'exchange_name' => $args['exchange_name'],
            'time_start' => $args['time_start'],
            'time_end' => $args['time_end']
        );
        $model = new AppCouponLogModel(17);
        $data = $model->pageList($where,$page,10,false);
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_coupon_log_search_page';
        $this->render('app_coupon_log_search_list.html',array(
            'pa'=>Util::page($pageData),
            'page_list'=>$data
        ));
    }
}

?>