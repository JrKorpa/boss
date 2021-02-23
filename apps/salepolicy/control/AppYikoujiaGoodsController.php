<?php
/**
 *  销售政策一口价商品控制器
 *  -------------------------------------------------
 *   @file		: AppYikoujiaGoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-01-17 18:36:47
 *   @update	:
 *  -------------------------------------------------
 */
class AppYikoujiaGoodsController extends CommonController
{
	protected $smartyDebugEnabled = true;
    protected $whitelist = array('zddow','batchgoods','error_csv');
	public $zhengshuleixing = array('NGDTC','GIA','IGI','NGTC','HRD','AGL','EGL','NGGC','NGSTC','HRD-D','HRD-S');
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_yikoujia_goods_search_form.html',array('bar'=>Auth::getBar()));
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
            'goods_id'=>_Request::get('goods_id'),
			'goods_sn'=>_Request::get('goods_sn'),
            'small'=>_Request::getFloat('small',0),
            'sbig'=>_Request::getFloat('sbig',100),
			'is_delete'=>_Request::getString('is_delete'),
		    'policy_id'=>_Request::getInt('_id'),
		);
		$page = _Request::getInt("page",1);
		$where = array(                
                'goods_id'=>$args['goods_id'],
				'goods_sn'=>$args['goods_sn'],
				'small' => $args['small'],
				'sbig' => $args['sbig'],
				'is_delete'=>$args['is_delete'],
		        'policy_id'=> $args['policy_id']
		);
		//$model = new AppYikoujiaGoodsModel(17);
		$model = new AppYikoujiaGoodsModel(15);//只读数据库
		$data = $model->pageList($where,$page,20,false);
		
		$goodsAttrModel = new GoodsAttributeModel(17);
		$caizhi_arr = $goodsAttrModel->getCaizhiList();
		foreach ($data['data'] as $k=>$v){
		    if(!empty($caizhi_arr[$v['caizhi']])){
		        $v['caizhi'] = $caizhi_arr[$v['caizhi']];
		        $data['data'][$k] = $v;
		    }
		}
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_yikoujia_goods_search_page';
		$this->render('app_yikoujia_goods_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
		));
	}
    /**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$dd = new DictView(new DictModel(1));
		$result = array('success' => 0,'error' => '');
 		$id=_Request::getInt('id');
        $tuo_type = array(1=>'成品', 3=>'空托');
 		$goodsAttrModel = new GoodsAttributeModel(17);
 		$caizhi_arr = $goodsAttrModel->getCaizhiList();
 		$cert_arr =  $goodsAttrModel->getCertList();
		$result['content'] = $this->fetch('app_yikoujia_goods_info.html',array(
			'policy_id'=>_Request::getInt('policy_id'),
		    'caizhi_arr'=>$caizhi_arr,
            'tuo_type' => $tuo_type,
			'cert'=>$cert_arr,
			'view'=>new AppYikoujiaGoodsView(new AppYikoujiaGoodsModel(17)),

		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}
	
	/*
	 * batchadd 批量添加
	 *
	*/
	public function batchadd()
	{
		$result = array('success' => 0,'error' => '');
 		$id=_Request::getInt('id');
 		
		$result['content'] = $this->fetch('app_yikoujia_goods_batchinfo.html',array(
			'policy_id'=>_Request::getInt('policy_id')
		));
		$result['title'] = '批量导入';
		Util::jsonExit($result);
	}
	
	
   	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
	    $result = array('success' => 0,'error' => '','title'=>"编辑");	     
		$id = _Request::getInt('id'); 
        $tuo_type = array(1=>'成品', 3=>'空托');
        $model =  new AppYikoujiaGoodsModel($id,18);
        $data = $model->getDataObject();
        if(empty($data)){
            $result['error'] = '信息记录不存在，可能已被删除';
        }
        
        $goodsAttrModel = new GoodsAttributeModel(17);
        $caizhi_arr = $goodsAttrModel->getCaizhiList();
        $cert_arr =  $goodsAttrModel->getCertList();
		$result['content'] = $this->fetch('app_yikoujia_goods_info.html',array(
			'data'=>$data,
		    'caizhi_arr'=>$caizhi_arr,
            'tuo_type' => $tuo_type,
		    'policy_id'=>_Request::getInt('policy_id'),
            'view' => new AppYikoujiaGoodsView(new AppYikoujiaGoodsModel(17)),
			'cert'=>$cert_arr,
		));
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{

		$result = array('success' => 0,'error' =>'');
       
		if(_Request::getString('goods_sn')==''){
			$result['error'] = '款号不能为空！';
			Util::jsonExit($result);
		}
		if(_Request::getString('caizhi')==''){
			$result['error'] = '材质不能为空！';
			Util::jsonExit($result);
		}
		if(_Request::getString('price')==''){
			$result['error'] = '销售价不能为空！';
			Util::jsonExit($result);
		}
		
		$newmodel =  new AppYikoujiaGoodsModel(18);
 
		$newdo=array(
				'goods_id'=>_Request::getString('goods_id'),
		        'goods_sn'=>_Request::getString('goods_sn'),
				'caizhi'=>_Request::getString('caizhi'),
				'small'=>_Request::getFloat('small',0),
				'sbig'=>_Request::getFloat('sbig',100),
				'price'=>_Request::getFloat('price',0),
				'policy_id'=>_Request::getInt('policy_id',0),
                'tuo_type'=>_Request::getInt('tuo_type'),
                'color'=>_Request::getString('color'),
                'clarity' =>_Request::getString('clarity'),
				'isXianhuo'=>_Request::getString('isXianhuo'),
				'tuo_type'=>_Request::getString('tuo_type'),
				'cert'=>_Request::getString('cert'),
				'add_time'=>date('Y-m-d H:i:s'),
				'add_user'=>$_SESSION['userName']
		);
		//update by lly 20170307 在同一个销售政策里的(有效的)同一个货号或者款号、材质、镶口范围相同信息添加时如果价格一样提示"***已存在"，如果价格不一样，新增一条记录，历史货号状态变成失效
		$w_check['goods_id'] = $newdo['goods_id'];
		$w_check['goods_sn'] = $newdo['goods_sn'];
		$w_check['caizhi'] = $newdo['caizhi'];
		$w_check['small'] = $newdo['small'];
		$w_check['sbig'] = $newdo['sbig'];
		$w_check['policy_id'] = $newdo['policy_id'];
		$w_check['isXianhuo'] = $newdo['isXianhuo'];
		$w_check['is_delete'] = 0;

		$c_data = $newmodel->getyikoujiainfo($w_check);
		if(!empty($c_data))
		{
			$c_data=$c_data[0];
			
			if($c_data['price'] == $newdo['price']){
				$result['error'] = '一口价的货品记录存在并且价格和上一次配置的价格一样,请勿重复配置！';
				Util::jsonExit($result);	
			}else{
				//否则将之前的失效即可
				$newmodel->deleteYikoujiaGoods($c_data['id']);	
			}
		}
		//如果存在的话,则修改为之前的无效,再添加新的
		$res = $newmodel->saveData($newdo,array());
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
	 *	update，更新信息 sale_price
	 */
	public function update ($params)
	{
	    $id = _Request::getInt('id');
	    $_cls = _Post::getInt('_cls');
	    $tab_id = _Post::getInt('tab_id');
	    
		$result = array('success' => 0,'error' =>'');

		$id = _Post::getInt('id');
       
		if(_Request::getString('goods_sn')==''){
			$result['error'] = '款号不能为空！';
			Util::jsonExit($result);
		}

		if(_Request::getString('caizhi')==''){
			$result['error'] = '材质不能为空！';
			Util::jsonExit($result);
		}
		if(_Request::getString('price')==''){
			$result['error'] = '别找死,价格不能为空！';
			Util::jsonExit($result);
		}
		
		$newmodel =  new AppYikoujiaGoodsModel($id,18);
		$olddo = $newmodel->getDataObject();
		$newdo=array(
				'id'=>$id,
				'goods_id'=>_Request::getString('goods_id'),
		        'goods_sn'=>_Request::getString('goods_sn'),
				'caizhi'=>_Request::getString('caizhi'),
				'small'=>_Request::getFloat('small',0),
				'sbig'=>_Request::getFloat('sbig',100),
				'price'=>_Request::getFloat('price',0),
				'policy_id'=>_Request::getInt('policy_id',0),
                'color'=>_Request::getString('color'),
                'clarity' =>_Request::getString('clarity'),
				'isXianhuo'=>_Request::getString('isXianhuo'),
				'tuo_type'=>_Request::getString('tuo_type'),
				'cert'=>_Request::getString('cert'),
				'is_delete'=>0
		);
		$c_data = $newmodel->getyikoujiainfo($newdo);
		if(!empty($c_data))
		{
			$c_data=$c_data[0];	
			if($c_data['price'] == $newdo['price']){
				$result['error'] = '一口价的货品记录存在并且价格和上一次配置的价格一样,请勿重复配置！';
				Util::jsonExit($result);	
			}else{
				//否则将之前的失效即可
				$newmodel->deleteYikoujiaGoods($c_data['id']);	
			}
		}
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;		
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	delMany 批量删除
	 */
	public function delMany ($params)
	{
		$result = array('success' => 0,'error' => '');
		$ids = _Request::getList('_ids');
		$id  = _Request::getInt('id');
		if(empty($ids) && !empty($id)){
		    $ids = array($id);
		}
		$model =  new AppYikoujiaGoodsModel(17);
		//$result['success'] = 1;
		//Util::jsonExit($result);
		$res = $model->deleteYikoujiaGoods($ids);		//不进行物理删除
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
	
	
	
	//下载上传一口价的模板
    public function zddow($value='')
    {
    	$type= _Request::get("type");
    	if($type==1){
	        $title = array(
	                '货号',
					'款号',
					'材质',
					'镶口最小值',
					'镶口最大值',
	                '金托类型(0:全部,1:成品,2:空托女戒,3:空托)',
					'价格',
					'证书类型',
					'货品类型(0:期货,1:现货)',
    	            '主石颜色(仅支持 成品定制)',
    	            '主石净度(仅支持 成品定制)'
	        );
	        $data[0]['goods_id']="28872219223";
	        $data[0]['goods_sn']="998";
	        $data[0]['caizhi']="18K";
			$data[0]['small']="0";
	        $data[0]['sbig']="100";
			$data[0]['tuo_type']="0";
	        $data[0]['price']="998";
			$data[0]['cert']="GIA";
			$data[0]['isXianhuo']="1";
			$data[0]['color']="D";
			$data[0]['clarity']="FL";
	        Util::downloadCsv("app_yikoujia_goods".time(),$title,$data);
    	}elseif($type==2){
    		$temexcel_file = 'apps/salepolicy/exceltemp/app_yikoujia.xls';
    		//$filedir = "apps/warehouse/exceltemp/";
    		$user_file = 'yikoujiagoods_' . time() . ".xls";
    		$file = fopen($temexcel_file, 'r');
    		
    		header('Content-type: application/octet-stream');
    		header("Accept-Ranges:bytes");
    		header("Accept-length:" . filesize($temexcel_file));
    		header('Content-Disposition: attachment;filename=' . $user_file);
    		ob_clean();
    		$a = fread($file, filesize($temexcel_file));
    		fclose($file);
    		echo $a;
    	}
    }
	
	//批量添加
	public function batchgoods()
	{
		$goodsAttrModel = new GoodsAttributeModel(17);
        $caizhi_arr = $goodsAttrModel->getCaizhiList();
		$caizhi_ids = array_flip($caizhi_arr);
		
		//获取销售政策id
		$policy_id = _Request::getInt('policy_id',0);
		ini_set("memory_limit","-1");
        set_time_limit(0);//设置上传允许超时提交（数据量大时有用）
        //标红提示；
        $error = '';
        $fileInfo = $_FILES['yikoujiagoods'];//读取文件信息；

        $tmp_name = $fileInfo['tmp_name'];
        //是否选择文件；
        if ($tmp_name == '') 
        {
            $error = "请选择上传文件！";
            $this->error_csv($error);
        }
        //是否csv文件；
        $file_name = $fileInfo['name'];
        $ext=Upload::getExt($file_name);
        if ($ext != 'xls' && $ext != 'csv') 
        {

            $error = "请上传.xls或.csv为后缀的文件！";
            $this->error_csv($error);
        }
		$errorinfo = '';
		$del_ids = '';
		$newmodel =  new AppYikoujiaGoodsModel(18);
		
		
        if($ext=='xlsx' || $ext=='xls'){        
           //上传.xlsx或者.xls文件
	        $path = '/frame/PHPExcel/PHPExcel.php';
	        $pathIo = '/frame/PHPExcel/PHPExcel/IOFactory.php';
	        $Excel5 = '/frame/PHPExcel/PHPExcel/Reader/Excel5.php';
	        
	        include_once(KELA_ROOT.$path);
	        include_once(KELA_ROOT.$pathIo);
	        include_once(KELA_ROOT.$Excel5);
			$tmptime = time();
	        $uploadfile=KELA_ROOT.'/frame/yikoujiagoods.'.$tmptime.$ext;
	        $result=move_uploaded_file($tmp_name,$uploadfile);
	        if($result){
				/*
	        	if($ext=='xlsx'){
		          $objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format
	        	}else{
	        		$objReader = PHPExcel_IOFactory::createReader('Excel5'); 
	        	}*/
				$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		        $objPHPExcel = $objReader->load($uploadfile);
		        
		        $objWorksheet = $objPHPExcel->getActiveSheet();
		        $highestRow = $objWorksheet->getHighestRow();

		        $highestColumn = $objWorksheet->getHighestColumn();
		        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);//总列数
		        $data=array();
		        for ($i = 2;$i <= $highestRow;$i++)
		        {
					$row=array();
					$goods_id= $objPHPExcel->getActiveSheet()->getCell("A{$i}")->getValue();	        	 
					$goods_sn= $objPHPExcel->getActiveSheet()->getCell("B{$i}")->getValue();
					$caizhi= $objPHPExcel->getActiveSheet()->getCell("C{$i}")->getValue();	        	 
					$small= $objPHPExcel->getActiveSheet()->getCell("D{$i}")->getValue();
					$sbig= $objPHPExcel->getActiveSheet()->getCell("E{$i}")->getValue();	        	 
					$tuotype= $objPHPExcel->getActiveSheet()->getCell("F{$i}")->getValue();
					$price= $objPHPExcel->getActiveSheet()->getCell("G{$i}")->getValue();
					$cert= $objPHPExcel->getActiveSheet()->getCell("H{$i}")->getValue();
					$isxianhuo= $objPHPExcel->getActiveSheet()->getCell("I{$i}")->getValue();
					$color = $objPHPExcel->getActiveSheet()->getCell("J{$i}")->getValue();
					$clarity = $objPHPExcel->getActiveSheet()->getCell("K{$i}")->getValue();
					if(!is_numeric($price)){
						$errorinfo .= " 第 $i 行的价格不是数字,请修改后在上传";
						continue;
					}
					if($price<0 ){
						$errorinfo .= " 第 $i 行的价格必须大于0,请修改后在上传";
						continue;
					}
					if($goods_id =='' && $goods_sn=='')
					{
						$errorinfo .= " 第 $i 行的货号，和款号不能同时为空,请修改后在上传";
						continue;
					}
					if($goods_id == '')
					{
						if($caizhi=='')
						{
							$errorinfo .= " 第 $i 行的材质不能为空,请修改后再上传";
							continue;
						}
					}
					
					//检查上传的商品是否存在
					$row['goods_id']= $goods_id;
					$row['goods_sn']= $goods_sn;
					$row['caizhi']= $caizhi_ids[strtoupper($caizhi)];
					$row['small']= trim($small) == '' ? 0 : trim($small);
					$row['sbig']= trim($sbig)=='' ? 100 : trim($sbig);
					$row['tuo_type'] = trim($tuotype)=='' ? 0 : trim($tuotype);
					$row['price']=round($price, 0);
					$row['isXianhuo']=$isxianhuo;	
					$row['color'] = trim($color)==""?"全部":$color;
					$row['clarity'] = trim($clarity)==""?"全部":$clarity;
					$c_data = $newmodel->getyikoujiainfo($row);
					if(!empty($c_data))
					{
						$c_data=$c_data[0];
						if($c_data['price'] == $row['price']){
							$errorinfo .= '一口价的货品记录存在并且价格和上一次配置的价格一样,请勿重复配置！';
							continue;
						}else{
							//否则将要失效的id存起来
							$del_ids[$row['goods_id']] = $c_data['id'];
						}
					}
					$data[]=$row;     
		        }
		     }else{
		        $error = "上传失败";
		        $this->error_csv($error);
		     }
         }elseif($ext=='csv'){
         	//上传csv格式
         	
         	//打开文件资源
         	$fileData = fopen($tmp_name, 'r');
         	while ($data = fgetcsv($fileData))
         	{
         	    foreach ($data as $_k=>$_v){
         	        $data[$_k] = iconv('GB2312', 'UTF-8',trim($_v));         	        
         	    }
         		$codeInfo[] = $data;
         	}
         	//是否填写数据
         	if (count($codeInfo) == 1)
         	{
         		$error = "未检测到数据，请填写后上传！";
         		$this->error_csv($error);
         	}
         	$hgt = 1;//行数；
         	array_shift($codeInfo);//去除首行文字；
         	foreach ($codeInfo as $key => $value) {
         		$hgt++;	
         		$fields = array('goods_id','goods_sn','caizhi','small','sbig','tuo_type','price','cert','isXianhuo','color','clarity');
         		$LineInfo=array();
         		//去除用户录入不规范的内容
         		for ($i=0; $i < 11 ; $i++)
         		{
         			$LineInfo[$fields[$i]] = trim($value[$i]);
         		}
         		if($LineInfo['goods_id'] == '' && $LineInfo['goods_sn'] =='')
         		{
         			$errorinfo .= "文件第".$hgt."行货号和款号不能同时为空！";
					continue;
         		}
				if($LineInfo['price'] == '')
				{
					$errorinfo .= "文件第".$hgt."行一口价不能为空！";
					continue;
				}
				if($LineInfo['price'] < 1 )
				{
					$errorinfo .= "文件第".$hgt."行一口价不能小于1！";
					continue;
				}
				if($LineInfo['goods_id'] == '')
				{
					if($LineInfo['caizhi']=='')
					{
						$errorinfo .= " 第 $i 行的材质不能为空,请修改后再上传";
						continue;
					}
				}
				if(trim($LineInfo['cert']) == '')
				{
					$errorinfo .= "文件第".$hgt."行证书类型不能为空！";
					continue;
				}	
				if(trim($LineInfo['color']) == '')
				{
					$errorinfo .= "文件第".$hgt."行颜色不能为空！";
					continue;
				}
				if(trim($LineInfo['clarity']) == '')
				{
					$errorinfo .= "文件第".$hgt."行净度不能为空！";
					continue;
				}											
				//检查上传的商品是否存在
				$caizhi = $LineInfo['caizhi'];
				$row = array();
				$row['goods_id']= $LineInfo['goods_id'];
				$row['goods_sn']= $LineInfo['goods_sn'];
				$row['caizhi']= $caizhi_ids[strtoupper($caizhi)];
				$row['small']= $LineInfo['small'] == '' ? 0 : $LineInfo['small'];
				$row['sbig']= $LineInfo['sbig']=='' ? 100 : $LineInfo['sbig'];
				$row['tuo_type'] = $LineInfo['tuo_type']=='' ? 0 : $LineInfo['tuo_type'];
				$row['price']=round($LineInfo['price'], 0);
				$row['cert']=strtoupper($LineInfo['cert']);
				$row['isXianhuo']= $LineInfo['isXianhuo'];
				$row['color'] = $LineInfo['color'];
				$row['clarity'] = $LineInfo['clarity'];
				
				$c_data = $newmodel->getyikoujiainfo($row);
				if(!empty($c_data))
				{
					$c_data=$c_data[0];
					if($c_data['price'] == $LineInfo['price']){
						$errorinfo .= "第 $hgt 行一口价的货品记录存在并且价格和上一次配置的价格一样,请勿重复配置！";
						continue;
					}else{
						//否则将要失效的id存起来
						$del_ids[$row['goods_id']] = $c_data['id'];
					}
				}
         		$data[] = $row;
         	}
         }else{
			$error = "上传文件格式错误";
			//$this->error_csv($error);
			$this->error_txt($error,'导入失败信息');
		}
		//print_r($data);
	    if($errorinfo !='')
		{
			//$this->error_csv($errorinfo,'err.csv');
			$this->error_txt($errorinfo,'导入失败信息');
		}
		if(empty($data)){
			$error = "上传文件中满足条件的记录为空";
			//$this->error_csv($error);
			$this->error_txt($error,'导入失败信息');
		}
		//如果没有任何错误 ,那我们就开始添加新的吧
		foreach($data as $key=>$value)
		{
			$gid = $value['goods_id'];
			$value['policy_id'] = $policy_id;
			$value['add_time'] = date('Y-m-d H:i:s');
			$value['add_user'] = $_SESSION['userName'];
			$res = $newmodel->saveData($value,array());
			if($res !== false)
			{
				if(isset($del_ids[$gid]))
				{
					$delid = $del_ids[$gid];
					$newmodel->deleteYikoujiaGoods($delid);
				}
			}
		}
		//$this->error_csv('文件上传成功');
		$this->error_txt('所有信息上传成功','文件上传成功');
		
	}
	
	/**
     *  错误输出
     */
    public function error_csv($content,$filename='')
    {
    	if($filename=='') $filename=$content;
        header("Content-type:text/csv;charset=gbk");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk","error:".$filename) . ".csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo iconv("utf-8", "gbk//IGNORE", $content);
        exit;
    }
	
	 public function error_txt($content,$filename='')
    {
    	if($filename=='') $filename=$content;
    	header("Content-type:text/txt;charset=gbk");
    	header("Content-Type: application/octet-stream");
    	Header( "Accept-Ranges:bytes ");
    	header('Content-Disposition: attachment; filename="' . iconv("utf-8", "gbk","error:".$filename) . '.txt"');
    	header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
    	header('Expires:0');
    	header('Pragma:public');
    	echo iconv("utf-8", "gbk//IGNORE", $content);
    	exit;
    }

}

