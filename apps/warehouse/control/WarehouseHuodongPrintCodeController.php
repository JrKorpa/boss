<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseHuodongPrintCodeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-06-04 16:42:28
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseHuodongPrintCodeController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist =array('printCode', 'uploadzdfile', 'downCode_zhubao', 'downCode_sujin', 'downCode_luoshi', 'error_csv', 'dow', 'printBzhCode', 'zddow','uploadbzhfile');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('warehouse_huodong_form.html',array('bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
    public function printCode($params) {
    	//var_dump($_REQUEST);exit;
        $args = array(
            'mod'	=> _Request::get("mod"),
            'con'	=> substr(__CLASS__, 0, -10),
            'act'	=> __FUNCTION__,
            'down_info' => _Request::get("down_info"),
            'type'  => _Request::get("type"),
            'jiajialv' => _Request::get("jiajialv"),
            'jiajianum' => _Request::get("jiajianum"),
            'bill_no' => _Request::get("bill_no"),
            'goods_id'=> _Request::get("goods_id"),
            'daying_type'=>_Request::get("daying_type"),
            'type_t'=>_Request::get("type_t"),
            'label_price'=>_Request::get("label_price"),
        );
        $model = new WarehouseGoodsModel(21);
        $info = array(
            'type' => $args['type'],
            'jiajialv' => $args['jiajialv'],
            'jiajianum' => $args['jiajianum'],
            'bill_no' => $args['bill_no'],
            'goods_id'=> $args['goods_id'],
            'down_info' => $args['down_info'],
            'daying_type'=> $args['daying_type'],
            'type_t'=> $args['type_t'],
            'label_price' => $args['label_price']
        );
        
        if ($info['down_info'] == "down_info") {
           
            $model->PrintHuodongCode($info);
            exit;
        }else {
            return false;
        }
    }

    public function printBzhCode($params) {
        //var_dump($_REQUEST);exit;
        $args = array(
            'mod'   => _Request::get("mod"),
            'con'   => substr(__CLASS__, 0, -10),
            'act'   => __FUNCTION__,
            'down_info' => _Request::get("down_info"),
            'goods_id'=> _Request::get("goods_id")
        );
        $model = new WarehouseGoodsModel(21);
        $info = array(
            'goods_id'=> $args['goods_id'],
            'down_info' => $args['down_info']
        );
        if ($info['down_info'] == "down_info_bzh") {
           
            $model->PrintHuodongBzhCode($info);
            exit;
        }else {
            return false;
        }
    }

    public function uploadzdfile()
    {
        ini_set("memory_limit","-1");
        set_time_limit(0);//设置上传允许超时提交（数据量大时有用）
        //标红提示；
        $error = '';
        //$result['error'] = "提示：批量上传成功，<span style='color:red;'>请核查！</span>";
        //Util::jsonExit($result);
        $fileInfo = $_FILES['zhidingcode'];//读取文件信息；

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
        if ($ext != 'xlsx' && $ext != 'xls' && $ext != 'csv') 
        {

            $error = "请上传.xls或.xls为后缀的文件！";
            $this->error_csv($error);
        }
        
        if($ext=='xlsx' || $ext=='xls'){        
           //上传.xlsx或者.xls文件
	        $path = '/frame/PHPExcel/PHPExcel.php';
	        $pathIo = '/frame/PHPExcel/PHPExcel/IOFactory.php';
	        $Excel5 = '/frame/PHPExcel/PHPExcel/Reader/Excel5.php';
	        
	        include_once(KELA_ROOT.$path);
	        include_once(KELA_ROOT.$pathIo);
	        include_once(KELA_ROOT.$Excel5);
	        $uploadfile=KELA_ROOT.'/frame/dabiao.'.$ext;
	        $result=move_uploaded_file($tmp_name,$uploadfile);
	        if($result){
	        	if($ext=='xlsx'){
		          $objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format
	        	}else{
	        		$objReader = PHPExcel_IOFactory::createReader('Excel5'); 
	        	}
		        $objPHPExcel = $objReader->load($uploadfile);
		        
		        $objWorksheet = $objPHPExcel->getActiveSheet();
		        $highestRow = $objWorksheet->getHighestRow();
		        
		        
		        $highestColumn = $objWorksheet->getHighestColumn();
		        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);//总列数
		        
		        $data=array();
		        for ($i = 2;$i <= $highestRow;$i++)
		        {
		        	 $row=array();
		        	 $goods_id= $objPHPExcel->getActiveSheet()->getCell("F{$i}")->getValue();	        	 
		        	 $price= $objPHPExcel->getActiveSheet()->getCell("BE{$i}")->getValue();  
		        	 
		        	  
		              if(!is_numeric($goods_id)) continue;
		              if( $price ===null) continue;
		              if(!is_numeric($price)){
		              	$error = "BE{$i}行‘价格’是用公式计算的价格，请改成数值。";
		              	$this->error_csv($error);
		              }
		              if($price<0 || $goods_id <= 0) continue;
		        	  $row['buyout_price']=round($price, 0);
		        	  $row['goods_id']=$goods_id;
		        	  $row['activity_price']=0;
		        	  $data[]=$row;	
		        	  //$error .=$goods_id.",".$price."\r\n"; 
		        	     
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
         		
         		$fields = array('goods_id','buyout_price','activity_price');
         		$LineInfo=array();
         		//去除用户录入不规范的内容
         		for ($i=0; $i < 3 ; $i++)
         		{
         		$LineInfo[$fields[$i]] = $this->trimall($value[$i]);
         		}
         	
         		if($LineInfo['goods_id'] == '')
         		{
         		$error = "文件第".$hgt."行货号不能为空！";
         				$this->error_csv($error);
         		}
         	
         			if($LineInfo['buyout_price'] == '')
         			{
         			$error = "文件第".$hgt."行指定价不能为空！";
         			$this->error_csv($error);
         	}
         	
         	
         	if($LineInfo['activity_price'] == '')
         	{
         	///$error = "文件第".$hgt."行活动价不能为空！";
         	//$this->error_csv($error);
         	$LineInfo['activity_price']='0';
         	}
         	
         	$data[] = $LineInfo;
         	}
         }else{
         	$error = "上传文件格式错误";
         	$this->error_csv($error);
         }  
         
	      if(empty($data)){
	       	  $error = "上传文件是空文件";
	       	  $this->error_csv($error);
	      }
	        $model = new WarehouseGoodsModel(21);
	        $model1 = new WarehouseGoodsModel(22);
	        $res = true;
	        $word="操作人：".$_SESSION['userName']."\t操作时间：".date('Y-m-d H:i:s',time())."\r\n\r\n";
	        $str='';
	        foreach ($data as $key => $value) {
	        	$re = $model->getBiaoqian($value['goods_id']);
	        	if(empty($re)){
	        		$r = $model1->saveBiaoQianData($value);
	        		if($r){
	        			$str.="货号".$value['goods_id']."插入价格：".$value['buyout_price']."\r\n";
	        		}
	        	}else{
	        		$bq_id=$re['id'];
	        		$buyout_price=$re['buyout_price'];	        		
	        		$r = $model1->updateBiaoQianData($bq_id,$value);
	        		if($r){
	        			$str.="货号".$value['goods_id']."更新价格：{$buyout_price}---->".$value['buyout_price']."\r\n";
	        		}
	        	}
	        	if($r == false) $res = false;
	        }
	        if($res == true){
	        	//echo ("<script>alert('".$str."');window.history.go(-1);</script>");
	        	
	        	$file=KELA_ROOT."/frame/dabiao_log.txt";
	        	if(!file_exists($file)){
	        		file_put_contents($file,'');
	        	}
	        	$fh = fopen($file, "a");
	        	$word .=$str."\r\n\r\n\r\n";
	        	fwrite($fh, $word);
	        	
	        	$this->error_txt($str,'导入成功信息');
	        }else{
	        	$error = "提交导入失败！";
	        	$this->error_csv($error);
	        }
   
	        
        
        
        /*
        //打开文件资源
        $fileData = fopen($tmp_name, 'r');
        while ($data = fgetcsv($fileData))
        {
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
            //是否为16列信息；
            
            if (count($value) != 3)
            {

                $error = "文件第".$hgt."行请上传3列信息！";
                $this->error_csv($error);
            }
            
            $fields = array('goods_id','buyout_price','activity_price');
            $LineInfo=array();
            //去除用户录入不规范的内容
            for ($i=0; $i < 3 ; $i++) 
            {
                $LineInfo[$fields[$i]] = $this->trimall($value[$i]);
            }

            if($LineInfo['goods_id'] == '')
            {
                $error = "文件第".$hgt."行货号不能为空！";
                $this->error_csv($error);
            }

            if($LineInfo['buyout_price'] == '')
            {
                $error = "文件第".$hgt."行指定价不能为空！";
                $this->error_csv($error);
            }
            
            
            if($LineInfo['activity_price'] == '')
            {
                ///$error = "文件第".$hgt."行活动价不能为空！";
                //$this->error_csv($error);
                 $LineInfo['activity_price']='0';
            }

            $data[] = $LineInfo;
        }

        $model = new WarehouseGoodsModel(21);
        $model1 = new WarehouseGoodsModel(22);
        $res = true;
        foreach ($data as $key => $value) {
            $bq_id = $model->getcheckBiaoqian($value['goods_id']);
            if(empty($bq_id)){
                $r = $model1->saveBiaoQianData($value);
            }else{
                $r = $model1->updateBiaoQianData($bq_id,$value);
            }
            if($r == false) $res = false;
        }
        if($res == true){
            $error = "提交导入成功！";
            $this->error_csv($error);
        }else{
            $error = "提交导入失败！";
            $this->error_csv($error);
        }
        */
    }

    public function uploadbzhfile(){
    	ini_set("memory_limit","-1");
    	set_time_limit(0);//设置上传允许超时提交（数据量大时有用）
    	//标红提示；
    	$error = '';
    	//$result['error'] = "提示：批量上传成功，<span style='color:red;'>请核查！</span>";
    	//Util::jsonExit($result);
    	$fileInfo = $_FILES['zhidingcode'];//读取文件信息；
    	 
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
    	if ($ext != 'xlsx' && $ext != 'xls')
    	{
    		 
    		$error = "请上传.xls或.xls为后缀的文件！";
    		$this->error_csv($error);
    	}
    	 
    	 
    	//上传.xlsx或者.xls文件
    	$path = '/frame/PHPExcel/PHPExcel.php';
    	$pathIo = '/frame/PHPExcel/PHPExcel/IOFactory.php';
    	$Excel5 = '/frame/PHPExcel/PHPExcel/Reader/Excel5.php';
    	 
    	include_once(KELA_ROOT.$path);
    	include_once(KELA_ROOT.$pathIo);
    	include_once(KELA_ROOT.$Excel5);
    	$uploadfile=KELA_ROOT.'/frame/baizhihui.'.$ext;
    	$result=move_uploaded_file($tmp_name,$uploadfile);
    	if($result){
    		 
    		if($ext=='xlsx'){
    			$objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format
    		}else{
    			$objReader = PHPExcel_IOFactory::createReader('Excel5');
    		}
    		$objPHPExcel = $objReader->load($uploadfile);
    		 
    		$objWorksheet = $objPHPExcel->getActiveSheet();
    		$highestRow = $objWorksheet->getHighestRow();
    		 
    		 
    		$highestColumn = $objWorksheet->getHighestColumn();
    		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);//总列数
    		 
    		$data=array();
    		for ($i = 2;$i <= $highestRow;$i++)
    		{
    		$row=array();
    		$row['goods_id']= $objPHPExcel->getActiveSheet()->getCell("F{$i}")->getValue();
    		$row['goods_sn']= $objPHPExcel->getActiveSheet()->getCell("G{$i}")->getValue();
    		$row['goods_name']= $objPHPExcel->getActiveSheet()->getCell("BK{$i}")->getValue();
    		$row['shoucun']= $objPHPExcel->getActiveSheet()->getCell("M{$i}")->getValue();
    		$row['zhengshuhao']= $objPHPExcel->getActiveSheet()->getCell("I{$i}")->getValue();
    		$row['chengbenjia']= $objPHPExcel->getActiveSheet()->getCell("BA{$i}")->getValue();
    		$row['jinzhong']= $objPHPExcel->getActiveSheet()->getCell("H{$i}")->getValue();
    				$row['zhushishu']= $objPHPExcel->getActiveSheet()->getCell("S{$i}")->getValue();
    						$row['zhushizhong']= $objPHPExcel->getActiveSheet()->getCell("R{$i}")->getValue();
    	    			$row['fushishu']= $objPHPExcel->getActiveSheet()->getCell("AL{$i}")->getValue();
    	    			$row['fushizhong']= $objPHPExcel->getActiveSheet()->getCell("AO{$i}")->getValue();
    	    			$row['xiangkou']= $objPHPExcel->getActiveSheet()->getCell("L{$i}")->getValue();
    	    			$row['xiaoshoujia']= $objPHPExcel->getActiveSheet()->getCell("BE{$i}")->getValue();
    	    					$row['zhuchengse']= $objPHPExcel->getActiveSheet()->getCell("N{$i}")->getValue();
    	    							$row['zhushijiebie']= $objPHPExcel->getActiveSheet()->getCell("T{$i}")->getValue();
    	    							$row['zhushijingdu']= $objPHPExcel->getActiveSheet()->getCell("T{$i}")->getValue();
    	    							$row['zhushisecai']= $objPHPExcel->getActiveSheet()->getCell("U{$i}")->getValue();
    	    							$row['zhushiyanse']= $objPHPExcel->getActiveSheet()->getCell("U{$i}")->getValue();
    	    							$row['ygoods_id']= $objPHPExcel->getActiveSheet()->getCell("BR{$i}")->getValue();
    
    
    
    
    	    							//if(empty($row['goods_id'])) continue;
    		if(empty($row['goods_sn'])) continue;
    		if(empty($row['goods_name'])) continue;
    		if(empty($row['chengbenjia']) && $row['chengbenjia'] != 0) continue;
    		if(empty($row['xiaoshoujia']) && $row['xiaoshoujia'] != 0) continue;
    		if(!is_numeric($row['chengbenjia'])){
    				$error = "BA{$i}行‘价格’是用公式计算的价格，请改成数值。";
    				$this->error_csv($error);
    		}
    						if(!is_numeric($row['xiaoshoujia'])){
    						$error = "BE{$i}行‘价格’是用公式计算的价格，请改成数值。";
    						$this->error_csv($error);
    		}
    		$data[]=$row;
    				//$error .=$goods_id.",".$price."\r\n";
    				 
    						}
    
    						}else{
    						$error = "上传失败";
    						$this->error_csv($error);
    }
    
     
     
    $model = new WarehouseGoodsModel(21);
    $model1 = new WarehouseGoodsModel(22);
    	$res = true;
    	$word="操作人：".$_SESSION['userName']."\t操作时间：".date('Y-m-d H:i:s',time())."\r\n\r\n";
    	$str='';
    	foreach ($data as $key => $value) {
		    	$re = $model->getBaizhihui($value['goods_id']);
		    	if(empty($re)){
		    			$r = $model1->insertTableData('warehouse_goods_baizhihui',$value);
		    			$id=$model1->db()->insertId();
		    			if(empty($value['goods_id'])){//货号为空时，id赋值给货号
		    			$d['goods_id']=$id;
		    	$model1->updateTableData('warehouse_goods_baizhihui',$d,'id='.$id);
		    			}
		    			if($r){
		    			   $str.="货号".$value['goods_id']."录入数据库成功\r\n";
		    	       }
		    	}else{
			    	$id=$re['id'];
			    			$goods_id=$value['goods_id'];
			    		unset($value['goods_id']);
			    		$r = $model1->updateTableData('warehouse_goods_baizhihui',$value,'id='.$id);
			    	if($r){
			    	$str.="货号".$goods_id."更新信息成功\r\n";
			    	}
		    	}
    		  if($r == false) $res = false;
    	}
    		if($res == true){
    	//echo ("<script>alert('".$str."');window.history.go(-1);</script>");
    	 
    	$file=KELA_ROOT."/frame/baizhihui_log.txt";
    		if(!file_exists($file)){
    	file_put_contents($file,'');
    	}
    	$fh = fopen($file, "a");
    	$word .=$str."\r\n\r\n\r\n";
    	fwrite($fh, $word);
    	 
    $this->error_txt($str,'导入成功信息');
    }else{
    $error = "提交导入失败！";
    	$this->error_csv($error);
    }
     
    }
    
    
    /**
     *  downCode_zhubao，下载标签
     */
    public function downCode_zhubao()
    {
        ini_set("memory_limit","-1");
        set_time_limit(0);//设置上传允许超时提交（数据量大时有用）
        //标红提示；
        $error = '';
        //$result['error'] = "提示：批量上传成功，<span style='color:red;'>请核查！</span>";
        //Util::jsonExit($result);
        $fileInfo = $_FILES['file_code'];//读取文件信息；

        $tmp_name = $fileInfo['tmp_name'];
        //是否选择文件；
        if ($tmp_name == '') 
        {
            $error = "请选择上传文件！";
            $this->error_csv($error);
        }

        //是否csv文件；
        $file_name = $fileInfo['name'];
        if (Upload::getExt($file_name) != 'csv') 
        {

            $error = "请上传.csv为后缀的文件！";
            $this->error_csv($error);
        }

        //打开文件资源
        $fileData = fopen($tmp_name, 'r');
        while ($data = fgetcsv($fileData))
        {
            $codeInfo[] = $data;
        }

        //是否填写数据
        if (count($codeInfo) == 1)
        {

            $error = "未检测到数据，请填写后上传！";
            $this->error_csv($error);
        }

        //限制上传数据量，限制行数为小于等于150行数据
        /*if (count($codeInfo) >= 151)
        {

            $error = "上传数据过大会导致提交超时，不能超过150行信息！";
            $this->error_csv($error);
        }*/
        $hgt = 1;//行数；
        array_shift($codeInfo);//去除首行文字；
        foreach ($codeInfo as $key => $value) {
            $hgt++;
            //是否为16列信息；
            if (count($value) != 54)
            {

                $error = "文件第".$hgt."行请上传13列信息！";
                $this->error_csv($error);
            }

            $fields = array('riqi','danhao','gongyingshang','gongyingshangname','goods_id','goods_sn','goods_name','shoucun','cat_type','shipin_type','zhengshuhao','shijichengben','zhengshufei','xiulifei','qibanfei','rukuchengbenjia','rukuxiaoshoujia','num','huozhong','jinzhong','peijianzhong','zhushi','zhushishu','zhushizhong','fushi','fushishu','fushizhong','gongyingshangtype','shizhong','xilie','xiangkou','xiaoshoujia','xingbie','xuhao','yanse','zhuchengse','zhushiduicheng','zhushiguige','zhushidanlizhong','zhushijibie','zhushijingdu','zhushimingcheng','zhushimohao','zhushipaoguang','zhushiquegong','zhushisecai','zhushiyanse','zhushizhonglei','zhushiquduanzhong','shuxing','beizhu','biaomian','cangku','changkuname');

            //去除用户录入不规范的内容
            for ($i=0; $i < 54 ; $i++) 
            {
                $LineInfo[$fields[$i]] = $this->trimall($value[$i]);
            }

            /*if($LineInfo['customer_name'] == '')
            {
                echo "文件第".$hgt."行请填写准客户姓名！";die;
            }

            if($LineInfo['tel'] == '')
            {
                echo "文件第".$hgt."行请填写电话号码！";die;
            }

            if(in_array($LineInfo['tel'], $telAll))
            {
                echo "文件第".$hgt."行电话号码在系统已存在！";die;
            }*/

            $data[] = $LineInfo;
        }
        $model = new WarehouseGoodsModel(21);
        $model->downCodeInfo($data);
        exit();
    }

    /**
     *  downCode_zhubao，下载标签
     */
    public function downCode_sujin()
    {
        ini_set("memory_limit","-1");
        set_time_limit(0);//设置上传允许超时提交（数据量大时有用）
        //标红提示；
        $error = '';
        //$result['error'] = "提示：批量上传成功，<span style='color:red;'>请核查！</span>";
        //Util::jsonExit($result);
        $fileInfo = $_FILES['file_code'];//读取文件信息；

        $tmp_name = $fileInfo['tmp_name'];
        //是否选择文件；
        if ($tmp_name == '') 
        {
            $error = "请选择上传文件！";
            $this->error_csv($error);
        }

        //是否csv文件；
        $file_name = $fileInfo['name'];
        if (Upload::getExt($file_name) != 'csv') 
        {

            $error = "请上传.csv为后缀的文件！";
            $this->error_csv($error);
        }

        //打开文件资源
        $fileData = fopen($tmp_name, 'r');
        while ($data = fgetcsv($fileData))
        {
            $codeInfo[] = $data;
        }

        //是否填写数据
        if (count($codeInfo) == 1)
        {

            $error = "未检测到数据，请填写后上传！";
            $this->error_csv($error);
        }

        //限制上传数据量，限制行数为小于等于150行数据
        /*if (count($codeInfo) >= 151)
        {

            $error = "上传数据过大会导致提交超时，不能超过150行信息！";
            $this->error_csv($error);
        }*/
        $hgt = 1;//行数；
        array_shift($codeInfo);//去除首行文字；
        foreach ($codeInfo as $key => $value) {
            $hgt++;
            //是否为16列信息；
            if (count($value) != 54)
            {

                $error = "文件第".$hgt."行请上传13列信息！";
                $this->error_csv($error);
            }

            $fields = array('riqi','danhao','gongyingshang','gongyingshangname','goods_id','goods_sn','goods_name','shoucun','cat_type','shipin_type','zhengshuhao','shijichengben','zhengshufei','xiulifei','qibanfei','rukuchengbenjia','rukuxiaoshoujia','num','huozhong','jinzhong','peijianzhong','zhushi','zhushishu','zhushizhong','fushi','fushishu','fushizhong','gongyingshangtype','shizhong','xilie','xiangkou','xiaoshoujia','xingbie','xuhao','yanse','zhuchengse','zhushiduicheng','zhushiguige','zhushidanlizhong','zhushijibie','zhushijingdu','zhushimingcheng','zhushimohao','zhushipaoguang','zhushiquegong','zhushisecai','zhushiyanse','zhushizhonglei','zhushiquduanzhong','shuxing','beizhu','biaomian','cangku','changkuname');

            //去除用户录入不规范的内容
            for ($i=0; $i < 54 ; $i++) 
            {
                $LineInfo[$fields[$i]] = $this->trimall($value[$i]);
            }

            /*if($LineInfo['customer_name'] == '')
            {
                echo "文件第".$hgt."行请填写准客户姓名！";die;
            }

            if($LineInfo['tel'] == '')
            {
                echo "文件第".$hgt."行请填写电话号码！";die;
            }

            if(in_array($LineInfo['tel'], $telAll))
            {
                echo "文件第".$hgt."行电话号码在系统已存在！";die;
            }*/

            $data[] = $LineInfo;
        }
        $model = new WarehouseGoodsModel(21);
        $model->downCodeInfo($data);
        exit();
    }

    /**
     *  downCode_zhubao，下载标签
     */
    public function downCode_luoshi()
    {
        ini_set("memory_limit","-1");
        set_time_limit(0);//设置上传允许超时提交（数据量大时有用）
        //标红提示；
        $error = '';
        //$result['error'] = "提示：批量上传成功，<span style='color:red;'>请核查！</span>";
        //Util::jsonExit($result);
        $fileInfo = $_FILES['file_code'];//读取文件信息；

        $tmp_name = $fileInfo['tmp_name'];
        //是否选择文件；
        if ($tmp_name == '') 
        {
            $error = "请选择上传文件！";
            $this->error_csv($error);
        }

        //是否csv文件；
        $file_name = $fileInfo['name'];
        if (Upload::getExt($file_name) != 'csv') 
        {

            $error = "请上传.csv为后缀的文件！";
            $this->error_csv($error);
        }

        //打开文件资源
        $fileData = fopen($tmp_name, 'r');
        while ($data = fgetcsv($fileData))
        {
            $codeInfo[] = $data;
        }

        //是否填写数据
        if (count($codeInfo) == 1)
        {

            $error = "未检测到数据，请填写后上传！";
            $this->error_csv($error);
        }

        //限制上传数据量，限制行数为小于等于150行数据
        /*if (count($codeInfo) >= 151)
        {

            $error = "上传数据过大会导致提交超时，不能超过150行信息！";
            $this->error_csv($error);
        }*/
        $hgt = 1;//行数；
        array_shift($codeInfo);//去除首行文字；
        foreach ($codeInfo as $key => $value) {
            $hgt++;
            //是否为16列信息；
            if (count($value) != 54)
            {

                $error = "文件第".$hgt."行请上传13列信息！";
                $this->error_csv($error);
            }

            $fields = array('riqi','danhao','gongyingshang','gongyingshangname','goods_id','goods_sn','goods_name','shoucun','cat_type','shipin_type','zhengshuhao','shijichengben','zhengshufei','xiulifei','qibanfei','rukuchengbenjia','rukuxiaoshoujia','num','huozhong','jinzhong','peijianzhong','zhushi','zhushishu','zhushizhong','fushi','fushishu','fushizhong','gongyingshangtype','shizhong','xilie','xiangkou','xiaoshoujia','xingbie','xuhao','yanse','zhuchengse','zhushiduicheng','zhushiguige','zhushidanlizhong','zhushijibie','zhushijingdu','zhushimingcheng','zhushimohao','zhushipaoguang','zhushiquegong','zhushisecai','zhushiyanse','zhushizhonglei','zhushiquduanzhong','shuxing','beizhu','biaomian','cangku','changkuname');

            //去除用户录入不规范的内容
            for ($i=0; $i < 54 ; $i++) 
            {
                $LineInfo[$fields[$i]] = $this->trimall($value[$i]);
            }

            /*if($LineInfo['customer_name'] == '')
            {
                echo "文件第".$hgt."行请填写准客户姓名！";die;
            }

            if($LineInfo['tel'] == '')
            {
                echo "文件第".$hgt."行请填写电话号码！";die;
            }

            if(in_array($LineInfo['tel'], $telAll))
            {
                echo "文件第".$hgt."行电话号码在系统已存在！";die;
            }*/

            $data[] = $LineInfo;
        }
        $model = new WarehouseGoodsModel(21);
        $model->downCodeInfo($data);
        exit();
    }
	
    //下载
    public function dow($value='')
    {
        $title = array(
                '准客户姓名',
                '状态',
                '项目',
                '来源类型',
                '来源渠道',
                '联系电话',
                '邮箱',
                '省',
                '市',
                '区',
                '意向开店数',
                '投资金额',
                '其他信息'
                );
        $data[0]['name']="张三";
        $data[0]['status']="待跟进";
        $data[0]['xiangmu']="kelan";
        $data[0]['laiyuan']="A";
        $data[0]['qudao']="中国加盟网";
        $data[0]['dianhua']="13888888882";
        $data[0]['eml']="123@kela.cn";
        $data[0]['sheng']="广东";
        $data[0]['shi']="深圳";
        $data[0]['qu']="龙岗";
        $data[0]['yix']="1";
        $data[0]['jine']="1000000万";
        $data[0]['qita']="备注";
            
        Util::downloadCsv("masterplate".time(),$title,$data);
    }

    //下载
    public function zddow($value='')
    {
    	$type= _Request::get("type");
    	if($type==1){
	        $title = array(
	                '货号',
	                '打标价',
	                '活动价'
	                );
	        $data[0]['name']="28872219223";
	        $data[0]['status']="998";
	        $data[0]['xiangmu']="998";
	            
	        Util::downloadCsv("zhiding_masterplate".time(),$title,$data);
    	}elseif($type==2){
    		$temexcel_file = 'apps/warehouse/exceltemp/dabiao.xls';
    		//$filedir = "apps/warehouse/exceltemp/";
    		$user_file = 'dabiao_' . time() . ".xls";
    		$file = fopen($temexcel_file, 'r');
    		
    		header('Content-type: application/octet-stream');
    		header("Accept-Ranges:bytes");
    		header("Accept-length:" . filesize($temexcel_file));
    		header('Content-Disposition: attachment;filename=' . $user_file);
    		ob_clean();
    		$a = fread($file, filesize($temexcel_file));
    		fclose($file);
    		echo $a;
    	}elseif($type==3){
    		
    		$temexcel_file = 'apps/warehouse/exceltemp/dabiao.xlsx';
    		//$filedir = "apps/warehouse/exceltemp/";
    		$user_file = 'dabiao_' . time() . ".xlsx";
    		$file = fopen($temexcel_file, 'r');
    		
    		header('Content-type: application/octet-stream');
    		header("Accept-Ranges:bytes");
    		header("Accept-length:" . filesize($temexcel_file));
    		header('Content-Disposition: attachment;filename=' . $user_file);
    		ob_clean();
    		$a = fread($file, filesize($temexcel_file));
    		fclose($file);
    		echo $a;
    	}elseif($type==4){
    	
    		$temexcel_file = 'apps/warehouse/exceltemp/baizhihui.xlsx';
    		//$filedir = "apps/warehouse/exceltemp/";
    		$user_file = 'baizhihui_' . time() . ".xlsx";
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


    /**
     *  trimall，删除空格
     */
    public function trimall($str)
    {

        //字符类型转换；
        $str = iconv('gbk','utf-8',$str);
        //数字不能为负数；
        if(is_numeric($str)){

            $str = abs($str);
        }
        //过滤字符串中用户不小心录入的的空格、换行、等特殊字符；
        $qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");

        return str_replace($qian,$hou,$str);
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

?>