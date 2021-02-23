<?php
/**
 *  -------------------------------------------------
 *   @file		: OrderFqcController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-08 18:17:23
 *   @update	:
 *  -------------------------------------------------
 */
class OrderFqcInfoController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$model = new OrderFqcInfoModel(21);
                $newmodel = new OrderFqcConfModel(21);
                $top_menu = $newmodel->get_top_menu();
                $op2 = $model->get_op2();
                $this->render('order_fqc_info_search_form.html',array(
                    'bar'=>Auth::getBar(),
                    'problem_type' => $top_menu,
                    'op2'=> $op2
                ));
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
                        'time_start' => _Request::getString("time_start"),
                        'time_end'   => _Request::getString("time_end"),
                        'search_type'=> _Request::getInt("search_type"),
                        'is_pass'    => _Request::getInt("is_pass"),
                        'qc_type'    => _Request::getInt("qc_type"),
                        'qc2'        => _Request::getInt("qc2"),
                        'operator'   => _Request::getString("operator"),
                        'consignee'  => _Request::getString("consignee"),
                        'bc_sn'      => _Request::getString("bc_sn"),
                        'order_sn'   => _Request::getString("order_sn")
			//'参数' = _Request::get("参数");


		);
                
		$page = _Request::getInt("page",1);
		$where = array();
                if(!empty($args['time_start'])) {
                        $where['time_start'] = $args['time_start'];
                }
                if(!empty($args['time_end'])) {
                        $where['time_end'] = $args['time_end'];
                }
                if(!empty($args['search_type'])){
			$where['search_type'] = $args['search_type'];
		}
		if($args['is_pass'] != ''){
			$where['is_pass'] = $args['is_pass'];
		}

                if(!empty($args['qc_type'])){
                        $where['qc_type'] = $args['qc_type'];
                }
                
                if(!empty($args['qc2'])) {
                        $where['qc2'] = $args['qc2'];
                }
                if(!empty($args['operator'])) {
                        $where['operator'] = $args['operator'];
                }
                if (!empty($args['bc_sn'])) {
                        $where['bc_sn'] = $args['bc_sn'];
                }
                if (!empty($args['consignee'])) {
                        $where['consignee'] = $args['consignee'];
                }
                if (!empty($args['order_sn'])) {
                        $where['order_sn'] = $args['order_sn'];
                } 
                //var_dump($where);exit;
        if(SYS_SCOPE == 'zhanting'){
            $where['hidden'] = '0';
        }
        $model = new OrderFqcInfoModel(21);
		$data = $model->pageList($where, $page);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'order_fqc_info_search_page';
		$this->render('order_fqc_info_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
                       
		));
	}
        
        //获取
	public function get_protype ()
	{
		$where= $_REQUEST['id'];
		/** 获取对应二级导航列表**/
		$model = new OrderFqcConfModel(21);
		$second_menu = $model->get_second_menu($where);
                
                //Util::jsonExit("aaa".$second_menu);
                //var_dump($second_menu);exit;
		$html ="";
		foreach ($second_menu as $key=>$val)
		{
			$html .= "<option value='{$val['id']}'>{$val['cat_name']}</option>";
		}
		Util::jsonExit($html);
	}

	
}

?>