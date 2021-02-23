<?php
/**
 *  -------------------------------------------------
 *   @file		: InventoryStreamController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-04-13 10:58:36
 *   @update	:
 *  -------------------------------------------------
 */
class InventoryStreamController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('search');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('inventory_stream_search_form.html',array('bar'=>Auth::getBar()));
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
            'dia_type'   => _Request::get("dia_type"),
            'down_infos'   => _Request::get("down_infos"),
			//'参数' = _Request::get("参数");
		);
		$page = _Request::getInt("page",1);
		$where = array(
            'dia_type'=>$args['dia_type']
            );
		$model = new InventoryStreamModel(21);
        $infoData = $model->pageList($where);
        $yanse = array_column($infoData, 'yanse');
        $jingdu = array_column($infoData, 'jingdu');
        //var_dump($jingdu);die;
        $saleData = $model->getSaleStoneData();
        $year = date("Y");
        $date = date("Y-m-d");
        $week_tilie = $this->get_week($year,$date);
        $week_array = array_slice($week_tilie, -6, 6,true);
        if(!empty($args['dia_type']))
        	$zhengshuleibie=array($args['dia_type']);
        else
            $zhengshuleibie=array('AGL','EGL','GIA','HRD-D','HRD-S');
        $zuanshidaxiao = array(0,0.05,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1,1.5);
        //$yanse = array('D','E','F','G','H','I','J');
        //$jingdu = array('VVS1','VVS2','VS1','VS2','SI1','SI2');
        $yanse = array_unique($yanse); 
        $jingdu = array_unique($jingdu);
        $sale_data = array();
        foreach ($saleData as $key => $val) {
            //if($val['zuanshidaxiao'] != '' && $val['yanse'] != '' && $val['jingdu'] != '' && $val['zhengshuleibie'] != ''){
                $val['zuanshidaxiao'] = sprintf("%.2f", $val['zuanshidaxiao']);
                $sale_data[$val['zhengshuleibie'].'|'.$val['zuanshidaxiao'].'|'.$val['yanse'].'|'.$val['jingdu']][$val['week']] = $val;
            //}
        }
        $saleData=null;

        $info_Data = array();
        foreach ($infoData as $key => $val) {
            //if($val['zuanshidaxiao'] != '' && $val['yanse'] != '' && $val['jingdu'] != '' && $val['zhengshuleibie'] != ''){
                $val['zuanshidaxiao'] = sprintf("%.2f", $val['zuanshidaxiao']);
                $_k = $val['zhengshuleibie'].'|'.$val['zuanshidaxiao'].'|'.$val['yanse'].'|'.$val['jingdu'];
                $info_Data[$_k]['all_kucun'] = $val['all_kucun'];
                $info_Data[$_k]['buy_kucun'] = $val['buy_kucun'];
            //}
        }
        $infoData=null;
        $data = array();
        $weekInfo = array();
        foreach ($zhengshuleibie as $zskey => $zsleibie) {
	        foreach ($zuanshidaxiao as $daxiao_k => $daxiao_val) {
	            foreach ($yanse as $yanse_k => $yanse_val) {
	                foreach ($jingdu as $jingdu_k => $jingdu_val) {
	                	$val=array();
	                    $daxiao_val = sprintf("%.2f", $daxiao_val);
	                    $_k = $zsleibie.'|'.$daxiao_val.'|'.$yanse_val.'|'.$jingdu_val;
	                    $val['zhengshuleibie'] = $zsleibie;
	                    $val['zuanshidaxiao'] = $daxiao_val;
	                    $val['yanse']   = $yanse_val;
	                    $val['jingdu']  = $jingdu_val;
	                    $val['all_kucun'] = isset($info_Data[$_k]['all_kucun'])?$info_Data[$_k]['all_kucun']:0;
	                    $val['buy_kucun'] = isset($info_Data[$_k]['buy_kucun'])?$info_Data[$_k]['buy_kucun']:0;
	                    $val['sale'] = isset($sale_data[$_k]) && !empty($sale_data[$_k])?$sale_data[$_k]:array();
	                    //$sale_num = isset($val['sale']['count']) && !empty($val['sale']['count'])?$val['sale']['count']:0;
	                    //$sale_num = !empty($val['sale'])?array_column($val['sale'], 'count'):0;
                        $sale_num = array();
                        foreach ($val['sale'] as $k => $v) {
                            $sale_num[$v['week']] = $v['count'];
                        }
	                    $a = 0;

	                    if(is_array($sale_num)){	                    	
	                        foreach ($sale_num as $k=> $v){
                                if(in_array($k, array_keys($week_array))){
                                    $a+=$v;
                                }
	                       	}	                       
	                    }   
                        if($val['all_kucun'] == 0 && $a == 0){
                            continue;
                        }
	                    if(!empty($val['sale'])){
	                        foreach ($val['sale'] as $v) {
	                            $t_v = $val['zhengshuleibie'].'|'.$val['zuanshidaxiao'].'|'.$val['yanse'].'|'.$val['jingdu'].'|'.$v['week'];
	                            $weekInfo[$t_v] = $v['count'];
	                        }
	                    }
	                   
	                    //$val['week_sale_num'] = round($a/intval(date('W',time())),2);
                        $val['week_sale_num'] = round($a/6,2);
	                    $val['stock_to_sales'] =round($val['buy_kucun']/$val['week_sale_num'],2);
	                    $data[$_k] = $val;
	                }
	            }
	        }
	    }    


        //echo '<pre>';
        //print_r($weekInfo);die;
        if($args['down_infos'] == 'downs'){
            $this->Download($data,$weekInfo,$week_tilie);exit();
        }
		$this->render('inventory_stream_search_list.html',array(
			'page_list'=>$data,
            'weekInfo'=>$weekInfo,
            'week_tilie'=>$week_tilie
		));
	}

    function get_week($year,$nowtime){ 
        $year_start = $year . "-01-01"; 
        $year_end = $nowtime; 
        $startday = strtotime($year_start); 
        if (intval(date('N', $startday)) != '1') { 
            $startday = strtotime("next monday", strtotime($year_start)); //获取年第一周的日期 
        } 
        $year_mondy = date("Y-m-d", $startday); //获取年第一周的日期 
        $endday = strtotime($year_end); 
        if (intval(date('W', $endday)) == '7') { 
            $endday = strtotime("last sunday", strtotime($year_end)); 
        } 
        $num = intval(date('W', $endday)); 
        for ($i = 1; $i <= $num; $i++) { 
            $j = $i -1; 
            $start_date = date("Y-m-d", strtotime("$year_mondy $j week ")); 
            $end_day = date("Y-m-d", strtotime("$start_date +6 day")); 
            $week_array[$i] =$start_date.'<br/>'.$end_day;
        } 
        //$week_array = array_slice($week_array, -6, 6,true);
        return $week_array; 
    } 

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('inventory_stream_info.html',array(
			'view'=>new InventoryStreamView(new InventoryStreamModel(21))
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
		$result['content'] = $this->fetch('inventory_stream_info.html',array(
			'view'=>new InventoryStreamView(new InventoryStreamModel($id,21)),
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
		$this->render('inventory_stream_show.html',array(
			'view'=>new InventoryStreamView(new InventoryStreamModel($id,21)),
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

		$newmodel =  new InventoryStreamModel(22);
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

		$newmodel =  new InventoryStreamModel($id,22);

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
		$model = new InventoryStreamModel($id,22);
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

    //导出
    public function Download($data,$weekInfo,$week_tilie){
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312','导出').time().".xls");
        $week_s = '';
        if(!empty($week_tilie)){
            foreach ($week_tilie as $k => $v) {
                $week_s .= "<td>第".$k."周<br/>".$v."</td>";
            }
        }
        $csv_header="<table><tr><td>证书类型</td><td>分数段</td><td>颜色</td><td>净度</td><td>库存</td><td>买断</td><td>存销比</td><td>周平均销售数据量</td>".$week_s."</tr>";
        $csv_body = '';
        if(!empty($data)){
            foreach ($data as $kv => $info) {
                $str = '';
                foreach ($week_tilie as $k => $v) {
                    $ct = $kv."|".$k;
                    $weeks = isset($weekInfo[$ct])?$weekInfo[$ct]:'0';
                    $str.="<td>".$weeks."</td>";
                }
                $csv_body.="<tr><td>".$info['zhengshuleibie']."</td><td>".$info['zuanshidaxiao']."</td><td>".$info['yanse']."</td><td>".$info['jingdu']."</td><td>".$info['all_kucun'].
                "</td><td>".$info['buy_kucun']."</td><td>".$info['stock_to_sales']."</td><td>".$info['week_sale_num']."</td>".$str."</tr>";
            }
        }
        $csv_footer="</table>";
        echo $csv_header.$csv_body.$csv_footer;
    }
}

?>