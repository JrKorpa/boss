<?php
/**
 *  -------------------------------------------------
 *   @file		: DiamondInfoLogController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-17 18:23:12
 *   @update	:
 *  -------------------------------------------------
 */
class AppDownloadZhekouController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('downLoad');

    public function __construct() {
        parent::__construct();
    }
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
	    $path = '/cron/diamond/zhekoulist/';
	    $fullPath = KELA_ROOT.$path;
	    $myDirectory=opendir($fullPath);
	     
	    $cnt=0;
	    while ($filename=readdir($myDirectory) )
	    {
	        $filename =  utf8_encode($filename);
	        // drop (exclude) the entry if it includes index or starts with a dot
	        if (  preg_match("/xls/",strtolower($filename))
	            || preg_match("/xlsx/",strtolower($filename))
	            || preg_match("/csv/",strtolower($filename))
	             
	            )
	        {
	            $DirArray[$cnt]['fname']=$filename;
	            $DirArray[$cnt]['type']=strtoupper(filetype($fullPath.$filename));
	            $DirArray[$cnt]['size']=$this->formatFileSize(filesize($fullPath.$filename));
	            $DirArray[$cnt]['mtime']=date('Y-m-d H:i:s',filemtime($fullPath.$filename));
	            $cnt=$cnt+1;
	        }
	    }
	    if(empty($DirArray)){
	        echo '没有更新下载文件！';exit;
	    }
	    $DirArray = $this->multi_array_sort($DirArray,'mtime');
	    closedir($myDirectory);
	    
        $this->render('app_download_zhekou_index.html',array('DirArray'=>$DirArray,'path'=>$path));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
        
         $jiajialvview = new DiamondJiajialvView(new DiamondJiajialvModel(19));
        
        $this->assign('jiajialvview', $jiajialvview);
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,

		);
		$page = _Request::getInt("page",1);
		$where = array(

        );

		$model = new DiamondInfoLogModel(19);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_download_zhekou_search_page';
		$this->render('app_download_zhekou_search_list.html',array(
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
		$result['content'] = $this->fetch('app_download_zhekou_info.html',array(
			'view'=>new DiamondInfoLogView(new DiamondInfoLogModel(19))
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
		$tab_id = intval($params["tab_id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('diamond_info_log_info.html',array(
			'view'=>new DiamondInfoLogView(new DiamondInfoLogModel($id,19)),
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
		$this->render('diamond_info_log_show.html',array(
			'view'=>new DiamondInfoLogView(new DiamondInfoLogModel($id,19)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$olddo = array();
		$newdo=array(
                    'from_ad'=>$params['from_ad'],
                    'operation_type'=>$params['operation_type'],
                    'operation_content'=>$params['operation_content'],
                    'create_time'=>date("Y-m-d H:i:s"),
                    'create_user'=>$_SESSION['userName'],
                );

		$newmodel =  new DiamondInfoLogModel(20);
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

		$newmodel =  new DiamondInfoLogModel($id,20);

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
		$model = new DiamondInfoLogModel($id,20);
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


	/*
	新的下载
	*/
	public function downLoad()
	{
	    
	}

	/**
	 *	xiazai，下载
	 */
	public function downLoadold()
	{
        define("JIAJIALV",	1.043);		//税点

        define("MARKET_RATE", 	1.5);		//市场/BDD
        define("MEMBER_RATE", 	0.9);		//vip/BDD
        define("MEMBER_RATE_60", 	0.9);		//vip/BDD
        define("JXCCHENGBENTOSHOP",		1.44);	//进销存成本价转销售价  小于70分
        define("JXCCHENGBENTOSHOP_70",	1.44);	//进销存成本价转销售价 大于70分
        define("JXCCHENGBENTOSHOP_100",	1.44);	//进销存成本价转销售价 大于70分
        define("JXCCHENGBENTOSHOP_EGL",		2);	//EGL进销存成本价转销售价
        ini_set('memory_limit','6000M');
        set_time_limit(0);  
        
        //形状
        $model = new DiamondInfoModel(19);
        $xz_arr=$model->getShapeName();

        $form_ad=$model->getForm_ad();

        //加价率
        $dia_jiajialv=1;

        //加价率
        $huilv_model = new AppShopConfigModel(19);
        $dia_huilv=$huilv_model->gethuilvValue();

        //获取国际价格
        $guoji_model = new DiamondPriceModel(19);
        $guoji_priceLastId=$guoji_model->getLastId();
        $guoji_price=$guoji_model->getVersionList_new($guoji_priceLastId);

        /*$data_str="货号,证书类型,证书号,数量,形状,重量,重量段,重量段,颜色,净度,切工,对称,抛光,荧光,台宽,台深,国际价,折扣值,供应商折扣值,美元成本价,人民币成本价,人民币不含税成本,供应商,gemx证书号,天生一对,是否活动(0否.1是)||";*/
        $data_str='';

        $Zhekou_Model=new AppDownloadZhekouModel(19);
        $page=1;
        $limit=2000;
		
		//汇率转换值
		$dia_huilv = THE_EXCHANGE_RATE;
		
        while(true)
        {
            $start=($page-1)*$limit;
            //获取裸钻信息 
            $diamond_arr=$Zhekou_Model->getDiamond_all($start,$limit);
            if($diamond_arr==null)
            {
                break;
                exit();
            }
            $page++;
            $rescnt=count($diamond_arr);
            for($i=0;$i<$rescnt;$i++)
            {
                $xingzhuang="";
                if($diamond_arr[$i]['cat_id']=="1")
                {
                    $xingzhuang="BR";
                }
                else
                {
                    $xingzhuang="PS";
                }
                //如何是现货裸钻直接显示为现货（区分现货和期货产品）
                if(strstr($diamond_arr[$i]['source'],"LZ"))
                {
                    $diamond_arr[$i]['source']="现货";
                    //$dia_huilv=6.25;
                }
                //计算不含税成本
                $rmb_chengbin=round($diamond_arr[$i]['chengbenjia']/JIAJIALV,2);
				
				$yuangou = $diamond_arr[$i]['guojibaojia'];   //国际价
				$guoprice = $diamond_arr[$i]['us_price_source'];  //美元成本
				
                foreach($guoji_price as $k=>$v)
                {
                    $zhekou=0;
                    //$guoprice=0;    //国际价
                    //$yuangou=0;
					$usa_price=0;  
					//update by liulinyan 20151222 for boss_955
					//2.1 修改计算公式
					//国际价*重量*（1-供应折扣值）%=美元价
					//美元价*汇率*税点=人民币成本价
					//$zekouz = round((100-$diamond_arr[$i]['source_discount'])/100,2);
					//$usa_price = $yuangou*$diamond_arr[$i]['carat']*$zekouz;
					
					
                    if($diamond_arr[$i]['clarity']==$v['jingdu']&&$diamond_arr[$i]['color']==$v['yanse']&&$diamond_arr[$i]['carat']>=$v['min']&&$diamond_arr[$i]['carat']<=$v['max']&&$xingzhuang==$v['xingzhuang'])
                    {
                        $zhekou=-(1-round($rmb_chengbin/($v['price']*$diamond_arr[$i]['carat']*$dia_huilv),2))*100;
						//折扣值
						$zekouz = round((100-$diamond_arr[$i]['source_discount'])/100,4);
						$yuangou=$v['price'];
						//echo $zekouz.'<br/>';
						$usa_price = $yuangou *$diamond_arr[$i]['carat']*$zekouz;
						$jisuanchenj = $usa_price*$dia_huilv*JIAJIALV;
						//update for boss_1064 折扣清单】来源kela的"人民币成本价"取值【裸钻列表】的"成本价
						if($form_ad[$diamond_arr[$i]['source']] != 'kela')
						{
							$diamond_arr[$i]['chengbenjia'] = $usa_price*$dia_huilv*JIAJIALV;
							//$guoprice=round($rmb_chengbin/$dia_huilv,2);
						}
						
						
						$guoprice=$usa_price;
                        $min=$v['min'];
                        $max=$v['max'];
                        break;
                    }else{
                        $min="无";
                        $max="无";
                    }
                }
				
				if($diamond_arr[$i]['source']!=1)
				{
					$diamond_arr[$i]['chengbenjia'] = $jisuanchenj;
				}			
				//abs($zhekou).','.abs($diamond_arr[$i]['source_discount'])变成了$diamond_arr[$i]['source_discount']
                $data_str.=$diamond_arr[$i]['goods_sn'].",".$diamond_arr[$i]['cert'].",".$diamond_arr[$i]['cert_id'].",".$diamond_arr[$i]['goods_number'].",".$xz_arr[$diamond_arr[$i]['cat_id']].",".$diamond_arr[$i]['carat'].",".$min.",".$max.",".$diamond_arr[$i]['color'].",".$diamond_arr[$i]['clarity'].",".$diamond_arr[$i]['cut'].",".$diamond_arr[$i]['symmetry'].",".$diamond_arr[$i]['polish'].",".$diamond_arr[$i]['fluorescence'].",".$diamond_arr[$i]['table'].",".$diamond_arr[$i]['depth'].",".$yuangou.",".abs($diamond_arr[$i]['source_discount']).','.abs($diamond_arr[$i]['source_discount']).",".(abs($diamond_arr[$i]['source_discount'])==0?0.00:$guoprice).",".$diamond_arr[$i]['chengbenjia'].",".$form_ad[$diamond_arr[$i]['source']].",".$diamond_arr[$i]['gemx_zhengshu'].",".$diamond_arr[$i]['kuan_sn'].",".$diamond_arr[$i]['is_active']."||";

            }
        }
	
        /*$csvdir=ROOT_PATH."apps/diamond/templates/appdownloadzhekou/zhekou";
        if(is_dir($csvdir))
        {
            //清空目录
            //遍历临时文件，并写进数据库
            $handle=opendir($csvdir);
            while(false!==($file=readdir($handle)))
            {
                if($file!="."&&$file!=".."&&strpos($file,".log")===false)
                {
                    @unlink($csvdir."/".$file);
                    clearstatcache();
                }
            }
        }
        else
        {
            mkdir($csvdir);
        }
        ;
        $name="zhekoudiamond_".date('YmdHis').".csv";
        file_put_contents($csvdir."/".$name,iconv('UTF-8','GB2312',$data_str));*/
        
        $name="zhekoudiamond_".date('YmdHis');
        $title=array("货号",
                     "证书类型",
                     "证书号",
                     "数量",
                     "形状",
                     "重量",
                     "重量段",
                     "重量段",
                     "颜色",
                     "净度",
                     "切工",
                     "对称",
                     "抛光",
                     "荧光",
                     "台宽",
                     "台深",
                     "国际价",
                     "折扣值",
                     "供应商折扣值",
                     "美元成本价",
                     "人民币成本价",
                     //"人民币不含税成本",
                     "供应商",
                     "gemx证书号",
                     "天生一对",
                     "是否活动(1否.2是)");
        
        $data_str=explode('||',$data_str);
        $newdo=array();
        foreach($data_str as $k=>$val){
            $val=explode(',',$val);
                $newdo[$k]['goods_sn']=$val[0];
                $newdo[$k]['cert']=$val[1];
                $newdo[$k]['cert_id']=$val[2];
                $newdo[$k]['goods_number']=$val[3];
                $newdo[$k]['cat_id']=$val[4];
                $newdo[$k]['carat']=$val[5];
                $newdo[$k]['min']=$val[6];
                $newdo[$k]['max']=$val[7];
                $newdo[$k]['color']=$val[8];
                $newdo[$k]['clarity']=$val[9];
                $newdo[$k]['cut']=$val[10];
                $newdo[$k]['symmetry']=$val[11];
                $newdo[$k]['polish']=$val[12];
                $newdo[$k]['fluorescence']=$val[13];
                $newdo[$k]['table']=$val[14];
                $newdo[$k]['depth']=$val[15];
                $newdo[$k]['yuangou']=$val[16];
                $newdo[$k]['zhekou']=$val[18];
                $newdo[$k]['source_discount']=$val[18];
                $newdo[$k]['guoprice']=$val[19];
                $newdo[$k]['chengbenjia']=$val[20];
                //$newdo[$k]['rmb_chengbin']=$val[21];
                $newdo[$k]['source']=$val[21];
                $newdo[$k]['gemx_zhengshu']=$val[22];
                $newdo[$k]['kuan_sn']=$val[23];
                $newdo[$k]['is_active']=$val[24];
        }
        // Util::downloadCsv($name,$title,$newdo);
	}
	
	function formatFileSize($fileSize)
	{
	    $unit = array(' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');
	    $i = 0;
	    $inv = 1 / 1024;
	
	    while($fileSize >= 1024 && $i < 8)
	    {
	        $fileSize *= $inv;
	        ++$i;
	    }
	    $fileSizeTmp = sprintf("%.2f", $fileSize);
	    return ($fileSizeTmp - (int)$fileSizeTmp ? $fileSizeTmp : $fileSize) . $unit[$i];
	}
	
	function multi_array_sort($multi_array,$sort_key,$sort=SORT_DESC){
	
	    if(is_array($multi_array)){
	
	        foreach ($multi_array as $row_array){
	
	            if(is_array($row_array)){
	
	                $key_array[] = $row_array[$sort_key];
	            }else{
	
	                return false;
	            }
	        }
	    }else{
	
	        return false;
	    }
	    array_multisort($key_array,$sort,$multi_array);
	    return $multi_array;
	}
}

?>
