<?php
/**
 *  -------------------------------------------------
 *   @file		: AppBespokeActionLogController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 18:13:27
 *   @update	:
 *  -------------------------------------------------
 */
class AppBespokeOldController extends CommonController
{
	protected $smartyDebugEnabled = true;
    protected $whitelist = array('downLoad');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('app_bespoke_action_log','front',17);	//生成模型后请注释该行
		//Util::V('app_bespoke_action_log',17);	//生成视图后请注释该行

        //左老 右新
        $dep_id_mapping = array(144=>'1',
        2=>'2',
        3=>'3',
        4=>'4',
        118=>'5',
        5=>'6',
        6=>'7',
        99=>'10',
        7=>'9',
        100=>'10',
        8=>'11',
        9=>'12',
        10=>'13',
        11=>'14',
        14=>'15',
        15=>'16',
        16=>'17',
        17=>'18',
        18=>'19',
        19=>'20',
        20=>'21',
        21=>'22',
        22=>'23',
        23=>'24',
        26=>'25',
        27=>'26',
        28=>'27',
        29=>'28',
        30=>'89',
        31=>'30',
        33=>'31',
        34=>'32',
        35=>'34',
        36=>'34',
        37=>'118',
        38=>'36',
        41=>'113',
        40=>'38',
        39=>'39',
        42=>'40',
        43=>'41',
        44=>'42',
        45=>'43',
        46=>'44',
        47=>'45',
        49=>'46',
        50=>'47',
        51=>'48',
        52=>'49',
        53=>'51',
        54=>'51',
        55=>'52',
        56=>'53',
        57=>'54',
        58=>'55',
        59=>'56',
        60=>'57',
        61=>'58',
        63=>'59',
        62=>'60',
        64=>'61',
        65=>'62',
        66=>'63',
        68=>'64',
        67=>'65',
        69=>'66',
        70=>'67',
        71=>'68',
        72=>'69',
        73=>'70',
        81=>'71',
        74=>'110',
        75=>'73',
        76=>'74',
        77=>'75',
        78=>'76',
        79=>'77',
        80=>'78',
        82=>'79',
        83=>'103',
        84=>'82',
        85=>'82',
        86=>'83',
        87=>'84',
        88=>'85',
        89=>'86',
        90=>'102',
        92=>'88',
        91=>'89',
        93=>'100',
        94=>'91',
        95=>'105',
        96=>'93',
        97=>'94',
        98=>'95',
        101=>'96',
        102=>'97',
        112=>'98',
        132=>'99',
        104=>'100',
        105=>'101',
        107=>'102',
        109=>'103',
        110=>'104',
        111=>'105',
        127=>'106',
        116=>'108',
        117=>'108',
        119=>'109',
        120=>'110',
        121=>'111',
        122=>'112',
        123=>'113',
        124=>'114',
        125=>'115',
        126=>'116',
        128=>'117',
        129=>'118',
        130=>'119',
        131=>'120',
        133=>'121',
        134=>'124',
        135=>'125',
        136=>'126',
        137=>'127',
        138=>'128',
        139=>'129',
        140=>'130',
        141=>'131',
        142=>132,
        143=>133,
        144=>135,
        145=>134,
        146=>136,
        147=>137,
148=>138,
149=>141,   
150=>142,   
151=>143 
);
        //$department=array();
        //foreach($dep_id_mapping as $k=>$v){
            //$department[$v]=$k;
        //}
        //print_r($_SESSION['qudao']);exit;
        $qudao_arr=explode(",",$_SESSION['qudao']);
        $qudaos=array();
        foreach($dep_id_mapping as $k=>$v){
            if(in_array($v,$qudao_arr)){
                $qudaos[]=$k;
            }
        }
        
        $dc_id='1';
		$model = new AppBespokeOldModel(17);
		$data = $model->getQudaoList($dc_id); 
        if($data['data']){
            $qudao=array();
            foreach($data['data'] as $k=>$v){
                if(in_array($v['dc_id'],$qudaos)){
                    $qudao[$v['dc_id']]=$v['dep_name'];
                }
            }
        }else{
            $qudao=array();
        }
        
		$this->render('app_bespoke_old_search_form.html',array('bar'=>Auth::getBar(),'qudao'=>$qudao));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
        //左老 右新
        $dep_id_mapping = array(144=>'1',
        2=>'2',
        3=>'3',
        4=>'4',
        118=>'5',
        5=>'6',
        6=>'7',
        99=>'10',
        7=>'9',
        100=>'10',
        8=>'11',
        9=>'12',
        10=>'13',
        11=>'14',
        14=>'15',
        15=>'16',
        16=>'17',
        17=>'18',
        18=>'19',
        19=>'20',
        20=>'21',
        21=>'22',
        22=>'23',
        23=>'24',
        26=>'25',
        27=>'26',
        28=>'27',
        29=>'28',
        30=>'89',
        31=>'30',
        33=>'31',
        34=>'32',
        35=>'34',
        36=>'34',
        37=>'118',
        38=>'36',
        41=>'113',
        40=>'38',
        39=>'39',
        42=>'40',
        43=>'41',
        44=>'42',
        45=>'43',
        46=>'44',
        47=>'45',
        49=>'46',
        50=>'47',
        51=>'48',
        52=>'49',
        53=>'51',
        54=>'51',
        55=>'52',
        56=>'53',
        57=>'54',
        58=>'55',
        59=>'56',
        60=>'57',
        61=>'58',
        63=>'59',
        62=>'60',
        64=>'61',
        65=>'62',
        66=>'63',
        68=>'64',
        67=>'65',
        69=>'66',
        70=>'67',
        71=>'68',
        72=>'69',
        73=>'70',
        81=>'71',
        74=>'110',
        75=>'73',
        76=>'74',
        77=>'75',
        78=>'76',
        79=>'77',
        80=>'78',
        82=>'79',
        83=>'103',
        84=>'82',
        85=>'82',
        86=>'83',
        87=>'84',
        88=>'85',
        89=>'86',
        90=>'102',
        92=>'88',
        91=>'89',
        93=>'100',
        94=>'91',
        95=>'105',
        96=>'93',
        97=>'94',
        98=>'95',
        101=>'96',
        102=>'97',
        112=>'98',
        132=>'99',
        104=>'100',
        105=>'101',
        107=>'102',
        109=>'103',
        110=>'104',
        111=>'105',
        127=>'106',
        116=>'108',
        117=>'108',
        119=>'109',
        120=>'110',
        121=>'111',
        122=>'112',
        123=>'113',
        124=>'114',
        125=>'115',
        126=>'116',
        128=>'117',
        129=>'118',
        130=>'119',
        131=>'120',
        133=>'121',
        134=>'124',
        135=>'125',
        136=>'126',
        137=>'127',
        138=>'128',
        139=>'129',
        140=>'130',
        141=>'131',
        142=>'132',
        143=>'133',
        142=>132,
        143=>133,
        144=>135,
        145=>134,
        146=>136,
        147=>137,
148=>138,
149=>141,   
150=>142,   
151=>143         
        );
        /*$department=array();
        foreach($dep_id_mapping as $k=>$v){
            $department[$v]=$k;
        }
        $qudao_arr=explode(",",$_SESSION['qudao']);
        $qudao=array();
        foreach($qudao_arr as $k=>$v){
            if(in_array($v,$department)){
                $qudao[]=$v;
            }
        }
        $qudao=implode(",",$qudao);*/
        $qudao_arr=explode(",",$_SESSION['qudao']);
        $qudao=array();
        foreach($dep_id_mapping as $k=>$v){
            if(in_array($v,$qudao_arr)){
                $qudao[]=$k;
            }
        }
        //$qudao=implode(",",$qudao);
        $department_id = _Request::getList('department');
        if(isset($department_id)&&!empty($department_id)){
            //$qudao=implode(",",$department_id);
            $qudao=$department_id;
        }

		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
            'bespoke_sn' => _Request::getString('bespoke_sn'),
            'bespoke_man' => _Request::getString('bespoke_man'),
            'mobile' => _Request::getString('mobile'),
            'department[]' => implode(",",$qudao),
            'start_add_time' => _Request::getString('start_add_time'),
            'end_add_time' => _Request::getString('end_add_time'),
		);

		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['bespoke_sn'] = $args['bespoke_sn'];
		$where['bespoke_man'] = $args['bespoke_man'];
		$where['mobile'] = $args['mobile'];
		$where['department'] = $args['department[]'];
		$where['start_add_time'] = $args['start_add_time'];
		$where['end_add_time'] = $args['end_add_time'];

        //if($where['bespoke_sn']=='' && $where['mobile']==''){
            //die("必须输入预约号或手机号码查询");
        //}

		$model = new AppBespokeOldModel(17);
		$data = $model->pageList($where,$page,10,false);

        $pageData = $data['data'];
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_bespoke_old_search_page';
		//var_dump($pageData);die;
        $this->render('app_bespoke_old_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $pageData,
		));
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
        /* 预约状态 */
        $bespoke_status=array(0=>'初始化',1=>'成交',2=>'到店未成交',3=>'未到店',4=>'赠品');

        $result = array('success' => 0,'error' => '');
        $where=array();
		$where['bespoke_sn'] = intval($params["id"]);
        $model = new AppBespokeOldModel(17);
        $data = $model->getBespokeByBespoke_sn($where);
        
        $this->render('app_bespoke_old_show.html',array('bar'=>Auth::getBar(),'data'=>$data['data'],'bespoke_status'=>$bespoke_status));
	}

	/**
	 *	downLoad，导出
	 */
	public function downLoad ()
	{
        ini_set('memory_limit','6000M');
        set_time_limit(0);
        /* 预约状态 */
        $bespoke_status=array(0=>'初始化',1=>'成交',2=>'到店未成交',3=>'未到店',4=>'赠品');
        //左老 右新
        $dep_id_mapping = array(144=>'1',
        2=>'2',
        3=>'3',
        4=>'4',
        118=>'5',
        5=>'6',
        6=>'7',
        99=>'10',
        7=>'9',
        100=>'10',
        8=>'11',
        9=>'12',
        10=>'13',
        11=>'14',
        14=>'15',
        15=>'16',
        16=>'17',
        17=>'18',
        18=>'19',
        19=>'20',
        20=>'21',
        21=>'22',
        22=>'23',
        23=>'24',
        26=>'25',
        27=>'26',
        28=>'27',
        29=>'28',
        30=>'89',
        31=>'30',
        33=>'31',
        34=>'32',
        35=>'34',
        36=>'34',
        37=>'118',
        38=>'36',
        41=>'113',
        40=>'38',
        39=>'39',
        42=>'40',
        43=>'41',
        44=>'42',
        45=>'43',
        46=>'44',
        47=>'45',
        49=>'46',
        50=>'47',
        51=>'48',
        52=>'49',
        53=>'51',
        54=>'51',
        55=>'52',
        56=>'53',
        57=>'54',
        58=>'55',
        59=>'56',
        60=>'57',
        61=>'58',
        63=>'59',
        62=>'60',
        64=>'61',
        65=>'62',
        66=>'63',
        68=>'64',
        67=>'65',
        69=>'66',
        70=>'67',
        71=>'68',
        72=>'69',
        73=>'70',
        81=>'71',
        74=>'110',
        75=>'73',
        76=>'74',
        77=>'75',
        78=>'76',
        79=>'77',
        80=>'78',
        82=>'79',
        83=>'103',
        84=>'82',
        85=>'82',
        86=>'83',
        87=>'84',
        88=>'85',
        89=>'86',
        90=>'102',
        92=>'88',
        91=>'89',
        93=>'100',
        94=>'91',
        95=>'105',
        96=>'93',
        97=>'94',
        98=>'95',
        101=>'96',
        102=>'97',
        112=>'98',
        132=>'99',
        104=>'100',
        105=>'101',
        107=>'102',
        109=>'103',
        110=>'104',
        111=>'105',
        127=>'106',
        116=>'108',
        117=>'108',
        119=>'109',
        120=>'110',
        121=>'111',
        122=>'112',
        123=>'113',
        124=>'114',
        125=>'115',
        126=>'116',
        128=>'117',
        129=>'118',
        130=>'119',
        131=>'120',
        133=>'121',
        134=>'124',
        135=>'125',
        136=>'126',
        137=>'127',
        138=>'128',
        139=>'129',
        140=>'130',
        141=>'131',
        142=>'132',
        143=>'133',
        142=>132,
        143=>133,
        144=>135,
        145=>134,
        146=>136,
        147=>137,
148=>138,
149=>141,   
150=>142,   
151=>143             
        );
        $qudao_arr=explode(",",$_SESSION['qudao']);
        $qudao=array();
        foreach($dep_id_mapping as $k=>$v){
            if(in_array($v,$qudao_arr)){
                $qudao[]=$k;
            }
        }
        $qudao=implode(",",$qudao);
        $department_id = _Request::getList('department_id');
        if(isset($department_id)&&!empty($department_id)){
            $qudao=implode(",",$department_id);
        }

		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
            'bespoke_sn' => _Request::getString('bespoke_sn'),
            'bespoke_man' => _Request::getString('bespoke_man'),
            'mobile' => _Request::getString('mobile'),
            'department' => $qudao,
            'start_add_time' => _Request::getString('start_add_time'),
            'end_add_time' => _Request::getString('end_add_time'),
		);

		$where = array();
		$where['bespoke_sn'] = $args['bespoke_sn'];
		$where['bespoke_man'] = $args['bespoke_man'];
		$where['mobile'] = $args['mobile'];
		$where['department'] = $args['department'];
		$where['start_add_time'] = $args['start_add_time'];
		$where['end_add_time'] = $args['end_add_time'];

		$model = new AppBespokeOldModel(17);
		$data = $model->getAllList($where);
        
        if(isset($data['data'])&&!empty($data['data'])){
            foreach($data['data'] as $k=>$v){
                $arr[$k]['bespoke_sn']=$v['bespoke_sn'];
                $arr[$k]['bespoke_man']=$v['bespoke_man'];
                $arr[$k]['mobile']=$v['mobile'];
                $arr[$k]['bespoke_server']=$v['bespoke_server'];
                $arr[$k]['bespok_status']=$bespoke_status[$v['bespok_status']];
                $arr[$k]['make_order']=$v['make_order'];
                $arr[$k]['email']=$v['email'];
                $arr[$k]['from_ad']=$v['from_ad'];
                $arr[$k]['department']=$v['department'];
                if($v['withuserdo']==3){
                    $arr[$k]['withuserdo']='作废';
                }elseif($v['withuserdo']==1){
                    $arr[$k]['withuserdo']='需电话回访';
                }elseif($v['queue_status']==0){
                    $arr[$k]['withuserdo']='未到店';
                }elseif($v['bespok_status']==1){
                    $arr[$k]['withuserdo']='到店成交';
                }elseif($v['bespok_status']==2){
                    $arr[$k]['withuserdo']='到店未成交';
                }elseif($v['bespok_status']==4){
                    $arr[$k]['withuserdo']='赠品已领';
                }else{
                    $arr[$k]['withuserdo']='';
                }
                $arr[$k]['shop_time']=$v['shop_time'];
                $arr[$k]['add_time']=$v['add_time'];
                $arr[$k]['inshop_time']=$v['inshop_time'];
                $arr[$k]['start_time']=$v['start_time'];
                $arr[$k]['end_time']=$v['end_time'];
                $arr[$k]['bespok_remark']=$v['bespok_remark'];
            }
        }else{
            $arr=array();
        }

        $title = array(
				'预约号',
                '客户姓名',
                '手机号',
                '制单人',
                '预约状态',
                '接待人',
                'email',
                '来源',
                '部门',
                '状态',
                '到店时间',
                '添加时间',
                '顾客前台报到时间',
				'开始服务时间',
				'结束服务时间',
                '备注');
            
            Util::downloadCsv("预约列表",$title,$arr);
	}
}

?>
