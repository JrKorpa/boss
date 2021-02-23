<?php
/**
 *  -------------------------------------------------
 *   @file		: EverydayWithStoneController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-04-27 13:48:41
 *   @update	:
 *  -------------------------------------------------
 */
class EverydayWithStoneController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('everyday_with_stone_search_form.html',array('bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
        $now_time = date('Y-m-d');
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'start_time' => _Request::get('start',$now_time),
            'end_time' => _Request::get('end',$now_time)
		);
		$page = _Request::getInt("page",1);
		$where = array(
            'start_time' => $args['start_time'],
            'end_time' => $args['end_time']
        );
		$model = new EverydayWithStoneModel(13);
		$data = $model->pageList($where);
        $ToDayInfo = $data['ToDayInfo'];
        $UnfinishedBill = array_column($data['UnfinishedBill'],'diff_time');
        if($ToDayInfo){
            foreach ($ToDayInfo as $val) {
                $toInfo[$val['days']] = $val['count'];
            }
        }
        //统计未完成订单
        $unInfo[$now_time]['0days'] = 0;
        $unInfo[$now_time]['1days'] = 0;
        $unInfo[$now_time]['2days'] = 0;
        $unInfo[$now_time]['2tdays'] = 0;
        if($UnfinishedBill){
            foreach ($UnfinishedBill as $val) {
                if($val=='0'){
                    $unInfo[$now_time]['0days']++;
                }elseif($val=='1'){
                    $unInfo[$now_time]['1days']++;
                }elseif($val=='2'){
                    $unInfo[$now_time]['2days']++;
                }elseif($val >'2'){
                    $unInfo[$now_time]['2tdays']++;
                }
                $unInfo[$now_time]['total']++;
            }
        }
        if(!empty($data['EveryDayInfo'])){
            $EveryDayInfo = array();
            foreach ($data['EveryDayInfo'] as $val) {
                $EveryDayInfo[$val['days']] = $val['count'];
            }
            $dateList = $this->ExtDate($where['start_time'],$where['end_time']);
            if($dateList){
                foreach ($dateList as $time) {
                    $datato[$time]['xinzeng'] = isset($EveryDayInfo[$time])?$EveryDayInfo[$time]:'0';
                    $datato[$time]['wancheng'] = isset($toInfo[$time])?$toInfo[$time]:'0';
                    $datato[$time]['0days'] = $unInfo[$time]['0days'];
                    $datato[$time]['1days'] = $unInfo[$time]['1days'];
                    $datato[$time]['2days'] = $unInfo[$time]['2days'];
                    $datato[$time]['2tdays']= $unInfo[$time]['2tdays'];
                    $datato[$time]['total']= $unInfo[$time]['total'];
                }
            }
        }
		$this->render('everyday_with_stone_search_list.html',array(
			'page_list'=>$datato
		));
	}

    //取出两个时间段内所有日期
    public function ExtDate($lt_start,$lt_end)
    {
        $dt_start   = strtotime($lt_start);
        $dt_end = strtotime($lt_end);
        do {
           $dateList[] = date('Y-m-d', $dt_start);
        } while (($dt_start += 86400) <= $dt_end);
        krsort($dateList);
        return $dateList;
    }

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('everyday_with_stone_info.html',array(
			'view'=>new EverydayWithStoneView(new EverydayWithStoneModel(13))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('everyday_with_stone_info.html',array(
			'view'=>new EverydayWithStoneView(new EverydayWithStoneModel($id,13)),
			'tab_id'=>$tab_id
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('everyday_with_stone_show.html',array(
			'view'=>new EverydayWithStoneView(new EverydayWithStoneModel($id,13)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;
		$olddo = array();
		$newdo=array();

		$newmodel =  new EverydayWithStoneModel(14);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new EverydayWithStoneModel($id,14);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '修改此处为想显示在页签上的字段';
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new EverydayWithStoneModel($id,14);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>