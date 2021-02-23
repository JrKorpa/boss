<?php
/**
 *  -------------------------------------------------
 *   @file		: ShopcountReportController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Liulinyan <939942478@qq.com>
 *   @date		: 2015-09-01 10:15:23
 *   @update	:
 *  -------------------------------------------------
 */
class ShopDealReportController extends CommonController
{
	protected $smartyDebugEnabled = false;
    public $limit_time = '2019-01-01';
	
	/**
	*	index，搜索框
	*/
    public function index($params) {
        //获取体验店的信息
        $data = $this->getMyDepartments();
        $types=array(0=>"全部",-1=>"其他",1=>"异业联盟",2=>"社区",3=>"BDD相关",4=>"团购",5=>"老顾客",6=>"数据",7=>"网络来源");
        $this->render('shopdeal_report_form.html',
            array(
                'bar' => Auth::getBar(),
                'allshop'=>$data,
                'types'=>$types
            )
        );
    }

    // 各店成交率统计表
    public function search($params) {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'shop_type' => _Request::get("shop_type"),
            'department_id' => _Request::get("shop_id"),
            'orderenter' => _Request::getString("orderenter"),
            'fenlei' => _Request::get("fenlei"),
            'begintime' => _Request::get("begintime"),
            'endtime'   => _Request::get("endtime"),
            'is_delete' => 0
        );

        if(empty($args['begintime']) || empty($args['endtime'])) {
            echo '请选择时间';exit;
        }
        if($args['begintime'] > $args['endtime']) {
            echo '结束时间必须大于开始时间';exit;
        }

        if($args['begintime']<$this->limit_time && SYS_SCOPE == 'zhanting'){
            $args['begintime'] = $this->limit_time;
        }
        
        $days=$this->getDatePeriod($args['begintime'],$args['endtime']);
        if($days>100) {
            die('请查询100天范围内的信息!');
        }

		if(!empty($args['department_id']))
		{
			$tydid = array($args['department_id']);
		}else{
            $shop_type = $args['shop_type'];
            
            //获取体验店的信息
            $model = new ShopCfgChannelModel(59);
            $data = $model->getallshop_name();
            $ret = array();
            foreach($data as $key => $val){
                if($shop_type == 1 && $val['shop_type'] == 1){
                    $ret[$val['id']] = $val['shop_name'];
                }
                if($shop_type == 2 && $val['shop_type'] == 2){
                    $ret[$val['id']] = $val['shop_name'];
                }
                if($shop_type == 0){
                    $ret[$val['id']] = $val['shop_name'];
                }
            }

            $userChannelmodel = new UserChannelModel(59);
            $data_chennel = $userChannelmodel->getChannels($_SESSION['userId'],0);
            $myChannel=array();
            foreach($data_chennel as $key => $val){
                if(!empty($ret) && array_key_exists($val['id'],$ret)){
                    $myChannel[]= $val['id'];
                }
            }
            $tydid = $myChannel;
		}

        $args['department_id'] = $tydid;

        //体验店的名称
        $alltyd = $this->getMyDepartments();
        $shops = array_column($alltyd, 'shop_name', 'id');

        //开始拿数据
        $Model = new TydReportModel(59);
        //第一步统计门店、分类的已审核预约数
        $where = array();
        $where['create_time_start'] = $args['begintime'];
        $where['create_time_end'] = $args['endtime'];
        $where['bespoke_status'] = 2;  //已经审核的
        if(!empty($args['department_id'])){
            $where['department_id'] = $args['department_id'];
        }
        if(!empty($args['fenlei'])){
            $where['fenlei'] = $args['fenlei'];
        }
        $checked_data = $Model->getShopsBespokeCount($where);

        //第二步根据来源分组 实际到店数
        $where = array();
        $where['real_inshop_time_start'] = $args['begintime'];
        $where['real_inshop_time_end'] = $args['endtime'];
        $where['bespoke_status'] = 2;
        if(!empty($args['department_id'])){
            $where['department_id'] = $args['department_id'];
        }
        if(!empty($args['fenlei'])){
            $where['fenlei'] = $args['fenlei'];
        }
        $inshop_data = $Model->getShopsBespokeCount($where);

        //第三步拿取所有的订单信息
        $order_data = $Model->getShopsOrderCount($args);

        $types = array(1=>"yiye", 7=>"site"); // other, all

        $sourcedata = array();
        //全部预约, 来源分组转化为 分类统计
        foreach($checked_data as $obj) {
            $department_id = $obj['department_id'];
            $fenlei_id = $obj['fenlei'];
            $count = $obj['count'];
            if (empty($shops[$department_id])) continue;

            if (isset($types[$fenlei_id])) {
                $typename = $types[$fenlei_id];
                $sourcedata[$department_id][$typename]['bespokenum'] = $count;
            } else {
                // 异业和网络以外的
                $sourcedata[$department_id]['other']['bespokenum'] = isset($sourcedata[$department_id]['other']['bespokenum']) ?
                    $sourcedata[$department_id]['other']['bespokenum'] + $count : $count;
            }
            // 全部
            $sourcedata[$department_id]['all']['bespokenum'] = isset($sourcedata[$department_id]['all']['bespokenum']) ?
                $sourcedata[$department_id]['all']['bespokenum'] + $count : $count;
        }
        //实际到店预约
        foreach($inshop_data as $obj) {
            $department_id = $obj['department_id'];
            $fenlei_id = $obj['fenlei'];
            $count = $obj['count'];
            if (empty($shops[$department_id])) continue;

            if (isset($types[$fenlei_id])) {
                $typename = $types[$fenlei_id];
                $sourcedata[$department_id][$typename]['realbokenum'] = $count;
            } else {
                // 异业和网络以外的
                $sourcedata[$department_id]['other']['realbokenum'] = isset($sourcedata[$department_id]['other']['realbokenum']) ?
                    $sourcedata[$department_id]['other']['realbokenum'] + $count : $count;
            }
            // 全部
            $sourcedata[$department_id]['all']['realbokenum'] = isset($sourcedata[$department_id]['all']['realbokenum']) ?
                $sourcedata[$department_id]['all']['realbokenum'] + $count : $count;
        }
        //订单的
        foreach($order_data as $obj) {
            $department_id = $obj['department_id'];
            $fenlei_id = $obj['fenlei'];
            $count = $obj['ordernum']; // ordernum
            if (empty($shops[$department_id])) continue;
            
            if (isset($types[$fenlei_id])) {
                $typename = $types[$fenlei_id];
                $sourcedata[$department_id][$typename]['ordernum'] = $count;
            } else {
                // 异业和网络以外的
                $sourcedata[$department_id]['other']['ordernum'] = isset($sourcedata[$department_id]['other']['ordernum']) ?
                    $sourcedata[$department_id]['other']['ordernum'] + $count : $count;
            }
            // 全部
            $sourcedata[$department_id]['all']['ordernum'] = isset($sourcedata[$department_id]['all']['ordernum']) ?
                $sourcedata[$department_id]['all']['ordernum'] + $count : $count;
        }
        ksort($sourcedata);
        $this->render('shopdeal_report_list.html',
            array(
                'data'=>$sourcedata,
                'rows'=>count($sourcedata),
                'shops'=>$shops
            )
        );
    }

}?>