<?php
/**
 *  -------------------------------------------------
 *   @file		: BoxGoodsLogController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 19:06:59
 *   @update	:
 *  -------------------------------------------------
 */
class BoxGoodsLogController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array("search");

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('box_goods_log_search_form.html',array(
			'bar'=>Auth::getBar(),
			'dd'=> new DictView(new DictModel(1))
		));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'				=> _Request::get("mod"),
			'con'				=> substr(__CLASS__, 0, -10),
			'act'				=> __FUNCTION__,
			'goods_id'			=> trim(_Request::get("goods_id")),
			'type'	=> _Request::getInt("type"),
			'time_start' => trim(_Request::get("time_start")),
			'time_end' => trim(_Request::get("time_end")),
			'create_user' => trim(_Request::get("create_user")),
			'__order' => _Request::get("__order"),
			'__desc_or_asc' => _Request::get("__desc_or_asc")
		);
		$where = array(
			'goods_id'		=> $args['goods_id'],
			'type'			=> $args['type'],
			'time_start'	=> $args['time_start'],
			'time_end'		=> $args['time_end'],
			'create_user'	=> $args['create_user'],
			'__order' 		=>$args['__order'],
			'__desc_or_asc' 		=>$args['__desc_or_asc']
		);
        
		$model = new BoxGoodsLogModel(21);

		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		if(isset($params['down']) && ($params['down'] == 1)){
			$content = array();
			$where['__order'] = 'create_time';
			$where['__desc_or_asc'] = 'ASC';
			$data = $model->pageList($where,$page,90000000,false);
			foreach ($data['data'] as $k => $val) {
				$content[]= array(
					'type' => ($val['type'] == 1) ? '下架' : '上架',
					'goods_id' => $val['goods_id'],
					'warehouse' => $val['warehouse'],
					'box_sn' => $val['box_sn'],
					'create_time' => $val['create_time'],
					'create_user' => $val['create_user']
				);
			}
			$name= "货品出入库记录".date('Y-m-d H:i:s');
			$title = array('操作类型','货号','仓库','柜位','时间（从小到大排序）','操作人');
			Util::downloadCsv($name, $title, $content);
			exit;
		}
		$data = $model->pageList($where,$page,10,false);

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'box_goods_log_search_page';
		$this->render('box_goods_log_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'dd'=> new DictView(new DictModel(1))
		));
	}

}

?>