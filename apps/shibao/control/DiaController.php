<?php
/**
 *  -------------------------------------------------
 *   @file		: DiaController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-16 17:26:28
 *   @update	:
 *  -------------------------------------------------
 */
class DiaController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist  = array('download');
	protected $order_type = array(
			'MS' => '买石单',
			'SS' => '送石单',
			'HS' => '还石单',
			'TS' => '退石单',
			'YS' => '遗失单',
			'SY' => '损益单',
			'TH' => '退货单',
			'AS' => '调整单',
			'RK' => '其他入库单',
			'CK' => '其他出库单',
			'fenbaoru' => '分包入',
			'fenbaochu'=> '分包出'
	);

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('dia_search_form.html',array('bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		//var_dump($_REQUEST);exit;
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'shibao' => _Request::get("shibao"),
			'kucun_cnt' => _Request::get("kucun_cnt")


		);
		$page = _Request::getInt("page",1);
		$where = array(
				'shibao' => _Request::get("shibao"),
				'kucun_cnt' => _Request::get("kucun_cnt")
		);

		$model = new DiaModel(45);
		$data = $model->pageList($where,$page,10,false);
		//var_dump($data);exit;
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'dia_search_page';
		$this->render('dia_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('dia_info.html',array(
			'view'=>new DiaView(new DiaModel(45))
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
		$result['content'] = $this->fetch('dia_info.html',array(
			'view'=>new DiaView(new DiaModel($id,45)),
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
		$this->render('dia_show.html',array(
			'view'=>new DiaView(new DiaModel($id,45)),
			'bar'=>Auth::getViewBar()
		));
	}
	
	
	/**
	 *	shibao_edit,石包编辑页面
	 */
	public function shibao_edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id =  _Request::getInt("tab_id");

		$this->render('dia_show.html',array(
				'view'=>new DiaView(new DiaModel($id,45)),
				'bar'=>Auth::getViewBar(),
				'tab_id'   => $tab_id,
		));
	}
	
	/**
	 *	shibao_update,石包编辑
	 */
	public function shibao_update1 ($params)
	{
	
		//var_dump($_REQUEST['shibao']);exit;
		$id = intval($params["id"]);
		$newmodel =  new DiaModel($id,46);		
		$shibao_info = $newmodel->getDataObject();
		
		$shibaos =$_REQUEST['shibao'];
		$cnts =$_REQUEST['cnt'];
		$zhongs =$_REQUEST['zhong'];
		$xiaoshouchengbens =$_REQUEST['xiaoshouchengben'];
		//$zhong =$_REQUEST['zhong'];
		$c_num = count(shibaos);
		$zongzhong = $xiaoshouchengben =0;
		$sb_info =array();
		for($i = 0 ;$i < 5 ; $i++){
			
			if ($shibaos[$i] != '' && $cnts[$i] != '' && $zhongs[$i] != ''){

				$sb['shibao'] = $shibaos[$i];
				if (substr($sb['shibao'], 0 ,2) != 'KL'){
					$result['error'] = $sb['shibao'] . "BDD的石包号必须以KL开头";
					Util::jsonExit($result);
				}else{
					
					$DiaModel = new DiaModel(46);
					$ret = $DiaModel->shibao_exist($sb['shibao']);
					if($ret> 0)
					{
						$result['error'] = "石包号".$sb['shibao']."已经存在，请重新输入！";
						Util::jsonExit($result);
					}
				}
				// var_dump($i);
				$sb['cnt'] = $cnts[$i];
				if(!is_numeric(trim($sb['cnt']))){
					$result['error'] = "您输入的数量[".$sb['cnt']."]不合法，数量必须为数字！";
					Util::jsonExit($result);
				}
		
				$sb['zhong'] = $zhongs[$i];
				if(!is_numeric(trim($sb['zhong']))){
					$result['error'] = "您输入的总重量[".$sb['zhong']."]不合法，总重量必须为数字！";
					Util::jsonExit($result);
				}
		
				$sb['xiaoshouchengben'] = $_REQUEST['xiaoshouchengben'][$i];
				//var_dump($sb['xiaoshouchengben']);
				if(!is_numeric(trim($sb['xiaoshouchengben']))){
					$result['error'] = "您输入的每卡销售价格[".$sb['xiaoshouchengben']."]不合法，每卡销售价格必须为数字！";
					Util::jsonExit($result);
				}
				//var_dump($sb['cnt']);
				//echo "<pre>";print_r($shibao_info);exit;
				if($shibao_info['kucun_cnt'] == 0){
					$sb['caigouchengben'] = 0;
				}else{
					$sb['caigouchengben'] = $shibao_info['caigouchengben'] / $shibao_info['kucun_cnt'] * $sb['cnt'];
				}
				//$zongzhong='';
				//var_dump($sb['cnt'],88);
				$sb_info[] = $sb;
				//var_dump($sb['zhong'],$sb['cnt'],$sb['xiaoshouchengben']);
				$zongzhong += $sb['zhong'];
				$cnt = $sb['cnt'];
			
					
				//var_dump($cnt,99);	exit;
				$xiaoshouchengben += $sb['xiaoshouchengben'];
				//var_dump($zongzhong);
			}
		}
		//var_dump($zongzhong);
	//	exit;
		if ($cnt > $shibao_info['kucun_cnt']){
			$result['error'] = "分包数量超过原石包数量";
			Util::jsonExit($result);
		}
		if ($zongzhong > $shibao_info['kucun_zhong']){
			$result['error'] = "分包重量超过原石包重量";
			Util::jsonExit($result);
		}
		//echo 111;exit;
		//var_dump($sb_info);exit;
		foreach($sb_info as $sb){
			echo $sb['shibao'];
			if ($DiaModel->shibao_exist($sb['shibao'])){
				$DiaModel->opt_shibao($sb['shibao'], 'fenbaoru', $sb['cnt'], $sb['zhong']);
			}else{
				$DiaModel->fen_shibao($sb['shibao'], $sb['cnt'], $sb['zhong'], $shibao_info['caigouchengben'], $sb['xiaoshouchengben']);
			}
			$res = $DiaModel->opt_shibao($shibao_info['shibao'], 'fenbaochu', $sb['cnt'], $sb['zhong']);
		}
		
		//notice("石包编辑成功", "jxc_dia_order.php?act=fenbao&id=$id");
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '分包失败';
		}
		Util::jsonExit($result);
		
	
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

		$newmodel =  new DiaModel(46);
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

		$newmodel =  new DiaModel($id,46);

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
		$model = new DiaModel($id,46);
		$do = $model->getDataObject();
		if($do['kucun_cnt']>0){
			$result['error'] = "库存不为0不能删！！！！";
			Util::jsonExit($result);
		}
		$model->setValue('status',0);
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
	
	
	public function download($param) {
		//var_dump($_REQUEST);exit;
		$args = array(
				'shibao' => _Request::getString('shibao'),
				'kucun_cnt' => _Request::getString('kucun_cnt')
				
		);
		$model = new DiaModel(45);		
		$data = $model->pageList($args, 1, 1000000, false);
		//echo "<pre>";print_r($data['data']);exit;
		if ($data['data']) {
			$down = $data['data'];
			$xls_content = "序号,名称,每卡采购价格,每卡销售价格,库存数量,库存重量,买入,分包转入,送出,分包转出,还回,差额,退石,退货,遗失,损坏,其他入库,其他出库,添加时间\r\n";
			foreach ($down as $key => $val) {
				
				$xls_content .= $key+1 . ",";
				$xls_content .= $val['shibao'] . ",";
				$xls_content .= $val['caigouchengben'] . ",";
				$xls_content .= $val['xiaoshouchengben']. ",";
				$xls_content .= $val['kucun_cnt'] . ",";
				$xls_content .= $val['kucun_zhong'] . ",";
				if($val['MS_zhong']&&$val['MS_cnt']){
					$xls_content .= $val['MS_cnt'].'/'.$val['MS_zhong'] . ",";
				}else{
					$xls_content .= '' . ",";
				}
				
				if($val['fenbaoru_zhong']&&$val['fenbaoru_cnt']){
					$xls_content .= $val['fenbaoru_cnt'].'/'.$val['fenbaoru_zhong'] . ",";
				}else{
					$xls_content .= '' . ",";
				}
				if($val['SS_cnt']&&$val['SS_zhong']){
					$xls_content .= $val['SS_cnt'].'/'.$val['SS_zhong'] . ",";
				}else{
					$xls_content .= '' . ",";
				}
				if($val['fenbaochu_zhong']&&$val['fenbaochu_cnt']){
					$xls_content .= $val['fenbaochu_cnt'].'/'.$val['fenbaochu_zhong'] . ",";
				}else{
					$xls_content .= '' . ",";
				}
				
				if($val['HS_cnt']&&$val['HS_zhong']){
					$xls_content .= $val['HS_cnt'].'/'.$val['HS_zhong'] . ",";
				}else{
					$xls_content .= '' . ",";
				}

				if($val['cha_cnt']==0&&$val['cha_zhong']==0)
				{
					$xls_content .= '' . ",";
				}
				else
				{
					$xls_content .= $val['cha_cnt'].'/'.$val['cha_zhong'] . ",";
				}
				if($val['TS_cnt']&&$val['TS_zhong']){
					$xls_content .= $val['TS_cnt'].'/'.$val['TS_zhong'] . ",";
				}else{
					$xls_content .= '' . ",";
				}
				if($val['TH_cnt']&&$val['TH_zhong']){
					$xls_content .= $val['TH_cnt'].'/'.$val['TH_zhong'] . ",";
				}else{
					$xls_content .= '' . ",";
				}
			 	if($val['YS_cnt']&&$val['YS_zhong']){
					$xls_content .= $val['YS_cnt'].'/'.$val['YS_zhong'] . ",";
				}else{
					$xls_content .= '' . ",";
				}
				if($val['SY_cnt']&&$val['SY_zhong']){
					$xls_content .= $val['SY_cnt'].'/'.$val['SY_zhong'] . ",";
				}else{
					$xls_content .= '' . ",";
				}
				if($val['RK_cnt']&&$val['RK_zhong']){
					$xls_content .=$val['RK_cnt'].'/'.$val['RK_zhong'] . ",";
				}else{
					$xls_content .= '' . ",";
				}

				if($val['CK_cnt']&&$val['CK_zhong']){
					$xls_content .= $val['CK_cnt'].'/'.$val['CK_zhong'] . ",";
				}else{
					$xls_content .= '' . ",";
				} 
				$xls_content .= $val['addtime'] . "\n";
				
			
			}
		} else {
			$xls_content = '没有数据！';
		}
		header("Content-type: text/html; charset=gbk");
		header("Content-type:aplication/vnd.ms-excel");
		header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
		echo iconv("utf-8", "gbk", $xls_content);
	
	}
	
	/********************************************************************************************************
	 fun:rebulidShibao
	description:重新核算石包信息
	*********************************************************************************************************/
	function rebulidShibao ($params)
	{
		$id = intval($params['id']);
		$models = new DiaModel($id,46);
		$shibao = $models->getValue('shibao');
		$model   = new DiaOrderModel(46);
		$res     = $model->checkShibaoInfo($shibao,$this->order_type);
		if (!$res)
		{
			$result['error'] = "核算失败";
		}
		else
		{
			$result['success'] = 1;
		}
		Util::jsonExit($result);
	}

	/**
	 *	shibao_update,石包编辑
	 */
	public function shibao_update ($params)
	{
		$id					    = $params['id'];
		$shibao_arr			    = $params['shibao'];
		$cnt_arr				= $params['cnt'];
		$zhong_arr				= $params['zhong'];
		$xiaoshouchengben_arr   = $params['xiaoshouchengben'];
		$tab_id					= _Post::getInt('tab_id');
		$_cls = _Post::getInt('_cls');
	
		$model_dia				= new DiaModel($id,46);
		$sb_info				= array();
		#取得当前被分石包的信息
		$old_shibao_info       = $model_dia->getDataObject();
		if (!$old_shibao_info)
		{
			$result['error'] = "单据不存在";
			Util::jsonExit($result);
		}
		$emp = false;
		//数量 等等 总计
		$zongzhong = $xiaoshouchengben = $cnt = 0;

		for($i = 0; $i < 5; $i++)
		{
			$sb = array();
			if ($shibao_arr[$i] != '')
			{
				if ( empty($cnt_arr[$i]) || empty($zhong_arr[$i])  || empty($xiaoshouchengben_arr[$i]))
				{
					$result['error'] = $shibao_arr[$i] . "信息请补全";
					Util::jsonExit($result);
				}

				#验证石包
				$sb['shibao'] = $shibao_arr[$i];
				$shibao_info = $model_dia->getInfoByShibao($sb['shibao']);

				if (substr($sb['shibao'], 0 ,2) != 'KL')
				{
					$result['error'] = $sb['shibao'] . "BDD的石包号必须以KL开头";
					Util::jsonExit($result);
				}
				else
				{
                    if(count($shibao_info) > 0)
                    {
						$result['error'] ="石包号". $sb['shibao'] . "已经存在，请重新输入！";
						Util::jsonExit($result);
                    }
                }
				#验证数量
				$sb['cnt'] = $cnt_arr[$i];
                if(!is_numeric(trim($sb['cnt'])))
				{
					$result['error'] ="您输入的数量[". $sb['cnt'] . "]不合法，数量必须为数字！";
					Util::jsonExit($result);
                }
				#验证总重量
				$sb['zhong'] = $zhong_arr[$i];
                if(!is_numeric(trim($sb['zhong'])))
				{
					$result['error'] ="您输入的总重量[". $sb['zhong'] . "]不合法，数量必须为数字！";
					Util::jsonExit($result);
                }
				#验证销售价
				$sb['xiaoshouchengben'] = $xiaoshouchengben_arr[$i];
                if(!is_numeric(trim($sb['xiaoshouchengben'])))
				{
					$result['error'] ="您输入的每卡销售价格[". $sb['xiaoshouchengben'] . "]不合法，每卡销售价格必须为数字！";
					Util::jsonExit($result);
                }
				/*  赶脚用不着
                if($old_shibao_info['kucun_cnt'] == 0)
				{
                	$sb['caigouchengben'] = 0;
                }
				else
				{
					$sb['caigouchengben'] = @$shibao_info['caigouchengben'] / $shibao_info['kucun_cnt'] * $cnt; //源代码  不清楚计算规则
                }
				*/
				$sb_info[]			=	$sb;
				$zongzhong			+=	$sb['zhong'];
                $cnt				+=  $sb['cnt'];
				//$xiaoshouchengben	+=  $sb['xiaoshouchengben'];
				$emp = true;
			}
	
		}
		if ($emp == false)
		{
			$result['error'] = "数据不能为空";
			Util::jsonExit($result);
		}
		#验证石包分包条件
		if ($cnt > $old_shibao_info['kucun_cnt'])
		{
			$result['error'] = "分包数量超过原石包数量";
			Util::jsonExit($result);
		}
		if ($zongzhong > $old_shibao_info['kucun_zhong'])
		{
			$result['error'] = "分包重量超过原石包重量";
			Util::jsonExit($result);
		}
		foreach($sb_info as $sb)
		{
			/***** 以上判断条件证明 石包不会存在  源码
			if (shibao_exist($sb['shibao']))
			{
				opt_shibao($sb['shibao'], 'fenbaoru', $sb['cnt'], $sb['zhong']);
			}
			else
			{
				fen_shibao($sb['shibao'], $sb['cnt'], $sb['zhong'], $shibao_info['caigouchengben'], $sb['xiaoshouchengben']);
			}
			****/
			$model_dia->fen_shibao($sb['shibao'], $sb['cnt'], $sb['zhong'], $old_shibao_info['caigouchengben'], $sb['xiaoshouchengben']);
			
			$model_dia->opt_shibao($old_shibao_info['shibao'], 'fenbaochu', $sb['cnt'], $sb['zhong']);
		}
		$result['success'] = 1;
		$result['_cls'] = $id;
		$result['tab_id'] = $tab_id;	
		Util::jsonExit($result);
	}
}
?>