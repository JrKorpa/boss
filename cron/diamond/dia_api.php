<?php
error_reporting(E_ALL&~E_NOTICE);
header('Content-Type: text/html; charset=utf-8');
/**
 *
 * 获取裸钻接口
 */
class autodiamond extends diamondModel{
	private $errormsg; //错误信息
	private $runtime=array("start"=>"","end"=>""); //运行时间
	private $action; //接口方法名
	private $conf; //配置 连接地址、用户名、密码等
	private $diaCat; //取裸钻形状
	private $download=0;
	private $f='';

	function __construct($from_ad, $download='', $f='')
	{   
		ini_set("soap.wsdl_cache_enabled","0");
		ini_set('memory_limit','-1');
		set_time_limit(0);
		$this->createFolder(ROOT_PATH."data/".$this->action);
		
		$this->runtime['start']=date("Y-m-d H:i:s"); //开始运行时间
        $this->conf=$GLOBALS['diaconf'][$from_ad]; //当前执行接口的配置
        $this->diaCat=$GLOBALS['diaCat']; //取裸钻形状
        $this->diaShapeVal=$GLOBALS['diaShapeVal']; //取裸钻形状
        $this->symmetry_arr=$GLOBALS['symmetry_arr']; //取切工、抛光、对称

        $from_ad_name = $GLOBALS['from_ad_name'];
        $this->action=$from_ad_name;

        parent::$from_ad = $this->conf['source'];
		$this->writeLog("Start...");
		$this->writeLog("USD to RMB = ".DOLLARTORMB);

		//记寻状态 (开始)
		$this->runstatus(1);

		if($download) $this->download=$download; //是否需要下载通过接口获取的数据(csv格式，下载方式则不会自动数据不会入库)

		$themethod="m_".$from_ad_name; //每个接口方法函数名全部由"m_"开始
		if(method_exists($this,$themethod)){ //检查是否有该方法
			$this->$themethod(); //调用
		}else{
			die();
        }
		
	}

	function __destruct()
	{
    	$this->writeLog("Done.");
    	$this->runlog(); //记录执行LOG
		$this->runstatus(2);
	}

	/**
	 *
	 * 记录运行
	 */
	function runlog(){
		$this->runtime['end']=date("Y-m-d H:i:s"); //结束运行时间
		$string="[@".$this->action."@][#开始:".$this->runtime['start']."#]";
		//$string.= "";
		$string.="[#结束:".$this->runtime['end']."#]\n";
		$tp=fopen(ROOT_PATH."data/access.log","a");
		fwrite($tp,$string);
		fclose($tp);
	}

	/**
	 *
	 * 记录ERROR
	 * @param unknown_type $file
	 * @param unknown_type $line
	 */
	function runerror($file,$line){
		$string="[@".$this->action."@][#开始:".$this->runtime['start']."#]";
		$string.="[FILE:'".$file."'&&LINE:'".$line."']".$this->errormsg;
		$string.="[#结束:".$this->runtime['end']."#]\n";
		$tp=fopen(ROOT_PATH."data/error.log","a");
		fwrite($tp,$string);
		fclose($tp);
	}
	function createFolder($path)
	{
	    if (!file_exists($path))
	    {
	        $this->createFolder(dirname($path));
	        mkdir($path, 0777);
	    }
	    return true;
	}
	/**
	 *
	 * 运行状态
	 * @param unknown_type $s
	 */
	function runstatus($s=1){
	    $this->createFolder(ROOT_PATH."data/".$this->action);
	    $filename=ROOT_PATH."data/".$this->action."/runstatus.log";
        $dir=str_replace("runstatus.log","",$filename);
		if($s==1){
			$string="正在下载(后台执行)...";
			file_put_contents($filename,$string);
		}else{
			$string="已全部完成";
			file_put_contents($filename,$string);
		}
	}

	/**
	 *
	 * 将需要同步的数据价 生成以goods_sn为key,shop_price为val的一维数组
	 */
	function activeOpen(){
		if(DIAMOND_ACTIVE_OPEN){
			$opts=array('http'=>array('method'=>'GET','timeout'=>600)); //设置超时，单位是秒，可以试0.1之类的float类型数字
			$context=stream_context_create($opts);
			$contents=file_get_contents("http://diaactive.kela.cn/do_data.php?act=dia_list",false,$context);
			$diaarrlist=simplexml_load_string($contents); //转成数组
			$resultSet=array();
			foreach($diaarrlist as $key=>$val){
				$val=(array)$val;
                $val['goods_sn']=trim($val['goods_sn']);
				$resultSet[$val['goods_sn']]=$val['shop_price'];
			}
			return $resultSet;
		}
		return false;
	}
	/**
	 *
	 * 51接口
	 */
	function m_fiveonezuan(){
        if(true){
            error_reporting(E_ALL);
            $bbss = "bbzuan.kela.cn";
            //$bbss = "bbss.kela.cn";
            $user = "kela";
            $passwd = "1";
            try {
			    //输入用户名和密码登陆取令牌
	            $client = new SoapClient("http://".$bbss."/webservice/service.php?ws=Sys&WSDL",array('trace'=> true,'exceptions'=>true,'cache_wsdl'=>WSDL_CACHE_NONE));
	            //// 避免乱码
	            $client->soap_defencoding =  'UTF-8' ;
	            $client->decode_utf8 =  false ;
	            $client->xml_encoding =  'UTF-8' ;
	            //var_dump ($client);
	            //var_dump($client->__getFunctions());

	            //$sess = $client->__soapCall("login",array("yangfuyou","123456"));
	            $sess = $client->__soapCall("login",array($user ,$passwd));
	            $access = json_decode($sess);
	            if($access->success==true)
	            {
	                $accessKey=$access->result;
	            }
	            else
	            {
	            	$this->writeLog($access->result);
	                die();
	            }
	            //var_dump($access);die;

	            //带令牌的请求头
	            $soapheader = new SoapHeader ("http://".$bbss, "PHPSESSID", $accessKey );
	            $parameters = array('userId'=>76,'fields'=>'user_name,id');



	            $client = new SoapClient("http://".$bbss."/webservice/service.php?ws=Diamond&WSDL",array('trace'=> true,'exceptions'=>true,'cache_wsdl'=>WSDL_CACHE_NONE,'login' => $user, 'password' => $passwd));
	            // 避免乱码
	            $client->soap_defencoding =  'UTF-8' ;
	            $client->decode_utf8 =  false ;
	            $client->xml_encoding =  'UTF-8' ;
	            $parameters=array("supplier_id"=>81,"opr"=>"");
	            $jsondata = $client->__soapCall ("getDataList", $parameters, NULL,$soapheader);
	            $data = json_decode($jsondata);
	            $csv_file=$source = $data->result;
			} catch(SoapFault $e) {
			    $this->writeLog("An error was caught executing your request: {$e->getMessage()}");
			}
            
        }

		//先建表
		global $db_dia;
		create_data($this->conf['source']);
        	$activeOpenResult=$this->activeOpen();
        	//验证允许的证书类型
        	$certAllowList=$this->certAllow();
		$csvdir=ROOT_PATH."data/".$this->conf['source'];
		if(is_dir($csvdir)){
			//清空目录
			cleardirfile($csvdir);
		}else{
			mkdir($csvdir);
		}

		//净度列表
		$Clarity_V=array("kela");
		$Clarity_allow=array("IF","VVS1","VVS2","VS1","VS2","SI1");

		//define('ROOT_PATH',str_replace('include/dia_api.php','',str_replace('\\','/',__FILE__)));

		//生成标准文件
        $csvfie1=$csvdir."/".$this->conf['source'].".csv";
        $csvfie=$csvdir."/".$this->conf['source'].".data.csv";
        $url = $csv_file;
        $content = file_get_contents($csv_file);
        file_put_contents($csvfie,$content);
        //exec('wget "'.$url.'"  -O "'.$csvfie1.'"');
        //file_put_contents($csvfie1,file_get_contents($this->conf['server_url']));
        if(!is_file($csvfie)){
            $this->runerror(__FILE__,__LINE__.'file doesn\'t exists'); //写进错误LOG
            $this->writeLog("没有查到数据");
            exit();
        }
        //$csvfie1 = ROOT_PATH."shell/hy.1.data";
		//$fiveonezuan=@explode(',',file_get_contents("http://bbzuan.kela.cn/admin/cnt.php?s=hy"));
        $handle=fopen($csvfie1,"w");
		//获取数据
		$data_list=fopen($csvfie,'r');
        $tmp=array();
		$i=0;
		while(($row=fgetcsv($data_list))==true){
                $row=(array)$row; //xml对象转数组
                $goods_sn = $row[0];
                $shape=$row[1];
                $carat=$row[2];
                $color = $row[3];
                $clarity=$row[4];
                $cut=$row[5];
                $symmetry=$row[6];
                $polish=$row[7];
                $fluorescence=$row[8];
                $measurement=$row[9];
                $depth=$row[10];
                $table=$row[11];
                $cert=$row[12];
                $cert_id=$row[13];
                $sc_us_source_price=$row[14];
                $discount_source=$row[15];
                //||in_array(strtoupper($row['LUSTER']),array('M1','M2','M3') 51钻处理了
                /*
				if(strpos($row['SRK_COMMENT'],'MILKY')>0){
					continue;
				}

				if($row['BROWN_INCLUSION']=='BROWN'){
					continue;
				}
                                if($row['BROWN_INCLUSION']=='MIX TINGE'){
                                        continue;
                                }
				if(strpos($row['LUSTER'],'MILKY')>0){
                			continue;
            			}

                */

                if($cert_id==""||$clarity=='SI2'){
					//continue;
				}
				if(!in_array($clarity,$Clarity_allow)){
					continue;
				}

				if($fluorescence=='NONE'){
                    $fluorescence='N';
                }elseif($fluorescence=='FAINT'){
                    $fluorescence='F';
                }elseif($fluorescence=='MEDIUM'){
                    $fluorescence='M';
                }elseif($fluorescence=='STRONG'){
                    $fluorescence='S';
                }
				if($symmetry=='EXCELLENT'){
                    $symmetry='EX';
                }elseif($symmetry=='GOOD'){
                    $symmetry='G';
                }elseif($symmetry=='VERY GOOD'){
                    $symmetry='VG';
                }
                
                if($this->diaCat[$shape]['cat_id']==1){
                    if(!$this->diafilter($carat,$color,$clarity,$cut,$polish,$symmetry,$fluorescence,$cert,$cert_id)){
                        continue;
                    }
                }else{
                    if(!$this->diafilter_yixing($carat,$color,$clarity,$polish,$symmetry,$fluorescence,$cert,$cert_id)){
                        continue;
                    }                    
                }

                if($certAllowList){
                    if(!in_array($cert_id,$certAllowList)){
                        //continue;
                    }
                }

                if($fiveonezuan&&!empty($fiveonezuan)&&!empty($fiveonezuan[0])){
                    if(!in_array($goods_sn,$fiveonezuan)){
                        //continue;
                    }
                }
                $row1=array();
				$row1["goods_sn"]=$goods_sn; //$row["stone"];//货号
				$row1["warehouse"]="COM"; //库房  SHCOM
				$row1["source"]=$this->conf['source']; //货品来源
				$row1["shape"]=$shape; //"ROUNDS";/形状
				$row1["carat"]=$carat; //石重:Weight
				$row1["color"]=$color; //颜色:colorName
				$row1["clarity"]=$clarity; //净度:clarityName
				$row1["cut"]=$cut; //切工:cutName
				$row1["po"]=$polish; //抛光:polName
				$row1["sym"]=$symmetry; //对称:symName
				$row1["fl"]=updat_fluor($fluorescence); //荧光:fluoName
				$row1["diam"]=$measurement; //尺寸:LxWxD
				$row1["depth"]=$depth; //全深比:Depth1
				$row1["table"]=$table; //台宽比:Table1
				$row1["cert"]=$cert; //证书:labName
				$row1["cert_id"]=$cert_id; //证书号:certNo
				$row1["shop_price"]=$sc_us_source_price*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
				$row1["member_price"]=""; //$data["Amount"] * * MEMBER_RATE	//会员价计算出
				$row1["chengbenjia"]=$sc_us_source_price*DOLLARTORMB*JIAJIALV;
				$row1["xianggangjia"]='';
				$row1["gemx_zhengshu"]='';
				$row1["source_discount"]=$discount_source;
				$row1["mo_sn"]=''; //模号
				//修改活动价
				if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
					$row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
					$row1['is_active']=1;
				}
				echo $row1["goods_sn"]."\r\n";//货号

				if($this->download==0){
					try{ //待更新到diamond表的goods_id
						//如果队列中含有相同钻，则不添加
                        $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                        if($getAddDiamond){
                            $tmp[]=$getAddDiamond;
                        }
					}catch(Exception $e){
						$this->errormsg=$e->getMessage();
						$this->runerror(__FILE__,__LINE__); //写进错误LOG
						return false;
					}
				}
				//编号	库房	形状	重量	颜色	净度	切工	对称	抛光	荧光	尺寸	全深比	台宽比	证书	证书号	售价	会员价
				unset($row1['source']);
				fputcsv($handle,$row1);
			}
			$this->writeLog("tmp ttl: ".count($tmp));
        if($tmp){
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
                $this->adddiaDiamond($val,$this->conf['source']); 
            }
                          
            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
    }

        /**
	 *
	 * dharamhk x
	 * @return boolean
	 */
	function m_dharam(){
		$online = true;
        $is_getfiveone = false;
        $limit_time = 3;
        $start_time = 1;
		//获取文件信息
		while(1){
			if($online){
				
				try{
					$client_count=new soapclient($this->conf['server_url']); //新建soap连接
					$result_count=$client_count->GetDiamonDataCommaDelimeted(array('UniqID'=>$this->conf['UniqID'],'CompanyName'=>$this->conf['CompanyName'],'ActivationCode'=>$this->conf['ActivationCode'],"columns"=>'*',"finder"=>'1=1',"sort"=>'CertNo',"index"=>1,"count"=>1));
				}catch(SoapFault $e){
					$this->errormsg=$e->getMessage();
					$this->writeLog($e->getMessage());
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}
				                file_put_contents(ROOT_PATH."dharam.xml",$result_count);
				$returnResult_count=$result_count->GetDiamonDataCommaDelimetedResult;
				$xml_count=simplexml_load_string($returnResult_count);//print_r($xml_count);exit;
				$tota_records=$xml_count->Table1->Column1;
				if($tota_records<2){
					$this->writeLog("没有查到数据");
					exit();
				}
                var_dump($start_time);
                    
                if($start_time >= $limit_time){
                	$this->writeLog("已经操作过3次");
                    die();
                }
                $start_time++;
                var_dump($start_time);
			}else{
				$this->writeLog("不支持线下");
                die();
            }
			//初始化表
			if(true){
				global $db_dia;
				create_data($this->conf['source']);
				break;
			}
		}
        
		//信息对象数组化
		$per_page=500;
		$get_index=ceil($tota_records/$per_page);
        
		$csvdir=ROOT_PATH."data/".$this->conf['source'];
		if(is_dir($csvdir)){
			//清空目录
			cleardirfile($csvdir);
		}else{
			mkdir($csvdir);
		}

		//获取活动钻信息 一维 goods_sn=>shop_price
		$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        // $certAllowList=$this->certAllow();
		//生成标准文件
		$csvfie=$csvdir."/".$this->conf['source'].".csv";
		$handle=fopen($csvfie,"w");
		//$fiveonezuan=@explode(',',file_get_contents("http://bbzuan.kela.cn/admin/cnt.php?s=dharam"));
        $fiveonezuan=array();

		$gid=array();
		//csv头信息
		$diaCsvTitle=array();
		foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
			$diaCsvTitle[$key]=iconv("UTF-8","GBK",$value);
		}
		fputcsv($handle,$diaCsvTitle);
        $tmp=array();
		for($i=0;$i<=$get_index;$i++){
			try {
			   	$client=new SoapClient($this->conf['server_url']);
				$result=$client->GetDiamonDataCommaDelimeted(array('UniqID'=>$this->conf['UniqID'],'CompanyName'=>$this->conf['CompanyName'],'ActivationCode'=>$this->conf['ActivationCode'],"columns"=>'*',"finder"=>'1=1',"sort"=>'CertNo',"index"=>$i,"count"=>$per_page));
				$returnResult=$result->GetDiamonDataCommaDelimetedResult;
			} catch(SoapFault $e) {
			    $this->writeLog("An error was caught executing your request: {$e->getMessage()}");
			}
			
			$diaarrlist=simplexml_load_string($returnResult);//print_r($diaarrlist);exit;
            $diaarrlists=array();
            foreach($diaarrlist->Table as $row){
                $row=(array)$row;
                $diaarrlists[$row['ReportNo']]=$row;
            }
			foreach($diaarrlists as $row){
				//$row=(array)$row;

                if($this->diaCommonFilter($row['Size'], $row['Color'], $row['Clarity'], $row['Cert'], $row['ReportNo'], true) == 0) 
                {
                	continue;
                }

				$row1=array();
				$row1["goods_sn"]="HK".str_replace(array("SH"," "),array("",""),$row["ReportNo"]);
                
                // 重复货号移除：
                if($fiveonezuan&&!empty($fiveonezuan)&&!empty($fiveonezuan[0])){
                    if(!in_array($row1['goods_sn'],$fiveonezuan)){
                        continue;
                    }
                }

				$row1["warehouse"]="COM"; //库房
				$row1["source"]=$this->conf['source']; //货品来源
				$row1["shape"]=$row["Shape"]; //形状
				$row1["carat"]=$row["Size"]; //石重
				$row1["color"]=$row["Color"]; //颜色
				$row1["clarity"]=$row["Clarity"]; //净度
				$row1["cut"]=$row["Cut"]; //切工
				$row1["sym"]=$row["Sym"]; //对称
				$row1["po"]=$row["Polish"]; //抛光
				$row1["fl"]=updat_fluor($row['Flour']); //荧光
				$row1["diam"]=$row["M1"]."-".$row['M2']."*".$row['M3']; //尺寸
				$row1["depth"]=$row["Depth"]; //全深比
				$row1["table"]=$row["Table"]; //台宽比
				$row1["cert"]=$row["Cert"]; //证书
				$row1["cert_id"]=$row["ReportNo"]; //证书号
				$row1["shop_price"]=$row["Rate"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
				$row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE	//会员价计算出
				$row1["chengbenjia"]=$row["Rate"]*DOLLARTORMB*JIAJIALV;
				$row1["cts"]=$row["Rate"]/$row["Size"]; // 每克拉价
				$row1["guojibaojia"]=$row["RapRate"]; //国际报价
                $row1["us_price_source"]=$row["Rate"]; //美元价 
				$row1["xianggangjia"]='';
				$row1["gemx_zhengshu"]='';
				$row1["source_discount"]=$row["Disc"]; // 源折扣
                $row1["mo_sn"]=''; //模号

				//修改活动价
				if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
					$row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
					$row1['is_active']=1;
				}
                /*if($certAllowList){
                    if(!in_array($row1["cert"],$certAllowList)){
                        continue;
                    }
                }*/
				echo $row1['goods_sn']."\r\n";

				if($this->download==0){
					try{ //待更新到diamond表的goods_id
                        $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                        if($getAddDiamond){
                            $tmp[]=$getAddDiamond;
                        }
					}catch(Exception $e){
                        //print_r($e);exit;
						$this->errormsg=$e->getMessage();
						$this->runerror(__FILE__,__LINE__); //写进错误LOG
						return false;
					}
				}

				//编号	库房	形状	重量	颜色	净度	切工	对称	抛光	荧光	尺寸	全深比	台宽比	证书	证书号	售价	会员价 
				unset($row1['source']);
				fputcsv($handle,$row1);
			}
		}

        if($tmp){
        	$this->writeLog("tmp ttl: ".count($tmp));
            foreach($tmp as $j=>$l){
                $t[$l['cert_id']]=$l;
            } 
            $strr=$this->getSelect($t,5000);
            foreach($strr as $val){
            	//修改净度为VS1的期货钻成本价 先注释
            	// foreach($val as $k1=>$v1){
            	// 	if($v1['clarity'] =='VS1' && $v1['good_type']==2){
            	// 		$val[$k1]['chengben_jia'] = round($v1['chengben_jia']*TIAOJIALV,2);
            	// 		$val[$k1]['shop_price'] = round($v1['shop_price']*TIAOJIALV);
            	// 	}
            	// }
                $this->adddiaDiamond($val,$this->conf['source']); 
            }
                          
            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
	}

	/**
	 *
	 * diamondbyhk接口
	 */
	function m_diamondbyhk()
	{
		$online=true;//true;
		//获取文件信息
		while(1){
			if($online){
				try{
					$content=file_get_contents($this->conf['server_url']);
                    //$content=file_get_contents("/diamondbyhk.data.xml",$content);
					$content=iconv("UTF-8","UTF-8//IGNORE",$content);
					$this->writeLog($this->conf['server_url']);

		            /*$ch = curl_init();
		            curl_setopt ($ch,CURLOPT_URL,$this->conf['server_url']);
		            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		            curl_setopt($ch,CURLOPT_VERBOSE,true);
		            curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");
		            $content = curl_exec($ch);
		            var_dump($content);die;
		            curl_close ($ch);*/

					if(empty($content)){
						$this->writeLog('no file');
						exit;
					}
					$diaxmllist="<?xml version='1.0' encoding='utf-8' ?>".$content; //从节点中取xml格式数据
				    file_put_contents(ROOT_PATH."diamondbyhk.xml",$diaxmllist);
				}catch(Exception $e){
					$this->errormsg=$e->getMessage();
					$this->writeLog($e->getMessage());
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}
			}else{
				if(true){
					$diaxmllist=file_get_contents('http://bbzuan.kela.cn/jxc/data/diamondbyhk/diamondbyhk.data.xml');
				}else{
					$diaxmllist=file_get_contents('diamondbyhk.xml');
				}
			}
			//初始化表
			if($diaxmllist!=''){
				global $db_dia;
				create_data($this->conf['source']);
				break;
			}
		}
		//信息对象数组化
		$xml_count=simplexml_load_string($diaxmllist);
		$diaarrlist=$xml_count->pkt;
		global $diaCat;
		$csvdir=ROOT_PATH."data/".$this->conf['source'];
		if(is_dir($csvdir)){
			//清空目录
			cleardirfile($csvdir);
		}else{
			mkdir($csvdir);
		}
		@file_put_contents($csvdir."/".$this->conf['source'].".data");
		//获取活动钻信息 一维 goods_sn=>shop_price
		$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        $certAllowList=$this->certAllow();

		//生成标准文件
		$csvfie=$csvdir."/".$this->conf['source'].".csv";
		$handle=fopen($csvfie,"w");
		//$fiveonezuan=@explode(',',file_get_contents("http://bbzuan.kela.cn/admin/cnt.php?s=diamondbyhk"));

		$gid=array();
		//csv头信息
		$diaCsvTitle=array();
		foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
			$diaCsvTitle[$key]=iconv("UTF-8","CP936",$value);
		}
		fputcsv($handle,$diaCsvTitle);
        $diaarrlists=array();
        foreach($diaarrlist as $row){
            $row=(array)$row;
            $diaarrlists["".$row['CERT_NO']]=$row;
        }
        $tmp=array();
		$i=0;
		$j=0;
        
		foreach($diaarrlists as $row){
			//$row=(array)$row; //xml对象转数组
			if(!isset($diaCat[$row["SHAPE"]])){
				continue;
			}
			if($row["rte"]<0){
				continue;
			}
            if(!in_array($row['COL-SHADE'],array('WH'))){
				continue;
            }
            if(!in_array($row['MILK'],array('M0'))){
				continue;
            }

			$modfluo=array("ST"=>"S");
			if(isset($row['FLOURESENCE'])&&array_key_exists($row['FLOURESENCE'],$modfluo)){
				$row['FLOURESENCE']=$modfluo[$row['FLOURESENCE']];
			}

			if($this->diaCommonFilter($row['CRTWT'], $row['COL'], $row['CLR'], $row['LAB'], $row['CERT_NO'], true) == 0) 
            {
            	continue;
            }

			$row1=array();
			$row1["goods_sn"]="D".str_replace(array("-"),array(""),$row["pktCode"]); //$row["stone"];		//货号
			if($fiveonezuan&&!empty($fiveonezuan)&&!empty($fiveonezuan[0])){
				if(!in_array($row1['goods_sn'],$fiveonezuan)){
					continue;
				}
			}
			$row1["warehouse"]="COM"; //库房  SHCOM
			$row1["source"]=$this->conf['source']; //货品来源
			$row1["shape"]=$row["SHAPE"]; //"ROUNDS";				//形状
			$row1["carat"]=$row["CRTWT"]; //石重:Weight
			$row1["color"]=$row["COL"]; //颜色:colorName
			$row1["clarity"]=$row["CLR"]; //净度:clarityName
			$row1["cut"]=$row["CUT"]; //切工:cutName
			$row1["sym"]=$row["SYM"]; //对称:symName
			$row1["po"]=$row["POL"]; //抛光:polName
			$row1["fl"]=updat_fluor($row["FLOURESENCE"]); //荧光:fluoName
			$row1["diam"]=$row["Length"]."-".$row['Width']."*".$row['Height']; //尺寸:LxWxD
			$row1["depth"]=$row["DP"]; //全深比:Depth1
			$row1["table"]=$row["TBL"]; //台宽比:Table1
			$row1["cert"]=$row["LAB"]; //证书:labName
			$row1["cert_id"]=$row["CERT_NO"]; //证书号:certNo
			$row1["shop_price"]=$row["rte"]*$row["CRTWT"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
			$row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE	//会员价计算出
			$row1["chengbenjia"]=$row["rte"]*$row["CRTWT"]*DOLLARTORMB*JIAJIALV;
            $row1["cts"]=$row["rte"]; // 每克拉价
            $row1["guojibaojia"]=$row["RAP_RTE"]; //国际报价
            $row1["us_price_source"]=$row["rte"]*$row["CRTWT"]; //美元价 
			$row1["xianggangjia"]='';
			$row1["source_discount"]=0;
			$row1["xianggangjia"]='';
			$row1["gemx_zhengshu"]='';
			$row1["source_discount"]=$row["dis"];
            $row1["mo_sn"]=''; //模号

			//修改活动价
			if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
				$row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
				$row1['is_active']=1;
			}
            /*if($certAllowList){
                if(!in_array($row1["cert"],$certAllowList)){
                    continue;
                }
            }*/
            echo $row1['goods_sn']."\t";
			if($this->download==0){
				try{ //待更新到diamond表的goods_id
                    $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
				}catch(Exception $e){
					$this->errormsg=$e->getMessage();
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}
			}
			//编号	库房	形状	重量	颜色	净度	切工	对称	抛光	荧光	尺寸	全深比	台宽比	证书	证书号	售价	会员价
			unset($row1['source']);
			//修改活动价
			if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
				unset($row1['active_shop_price']);
				unset($row1['is_active']);
			}
			fputcsv($handle,$row1);
		}
        if($tmp){
        	$this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
        	 	//修改净度为VS1的期货钻成本价 先注释，后面启用
            	// foreach($val as $k1=>$v1){
            	// 	if($v1['clarity'] =='VS1' && $v1['good_type']==2){
            	// 		$val[$k1]['chengben_jia'] = round($v1['chengben_jia']*TIAOJIALV,2);
            	// 		$val[$k1]['shop_price'] = round($v1['shop_price']*TIAOJIALV);
            	// 	}
            	// }
                $this->adddiaDiamond($val,$this->conf['source']); 
            }
                          
            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
	}
	/**
	 *
	 * diarough接口
	 */
	function m_diarough()
	{
		$online=1;
    	$fiveone_way=1;

    	ini_set('default_socket_timeout', 150);

		while(1){
			if($online){
               /* try
                {
                	$this->writeLog($this->conf['server_url']);
                    $wcfClient = new SoapClient('http://94.200.156.148/DiaroughService/Diarough.svc/basic?wsdl',array("trace" => true, "connection_timeout" => 200));
                    $returnValue = $wcfClient->DiaroughAvailableStock(array('UserId'=>10608));
				    file_put_contents(ROOT_PATH."diarough.xml",$returnValue);
                    $diaxmllist=array();
                    foreach($returnValue as $k=>$v)
                    {
                        $diaxmllist[]=(array)$v;
                    }

				    
				}  
                catch (SoapFault $sf)
                {
					$this->errormsg=$sf->getMessage();
					$this->writeLog($sf->getMessage());
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}*/
                
                try
                {
                    $curl = curl_init(); 
                    $url = 'ftp://diarough.com/kela/DRFStock.csv';//完整路径
                    $this->writeLog($url);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_VERBOSE, 1);
                    curl_setopt($curl, CURLOPT_FTP_USE_EPSV, 0);
                    curl_setopt($curl, CURLOPT_TIMEOUT, 300); // times out after 300s
                    curl_setopt($curl, CURLOPT_USERPWD, "user_kela:Ke1aup10ad!");//FTP用户名：密码
                    $info = curl_exec($curl);

                    if($info === false)
					{
					    $this->writeLog('Curl error: ' . curl_error($curl));
					}

                    file_put_contents(ROOT_PATH."diarough.xml",$info);
                     $arr = explode("\n",$info);
                     $k_val = explode(",",$arr[0]);
                     array_shift($arr);
                     $diaxmllist=array();
                     $v_val=array();
                     foreach($arr as $k=>$v){
                        $v=explode(",",$v);
                        array_pop($v);
                        foreach($v as $key=>$val){
                            $key=trim($k_val[$key]);
                            $v_val[$key]=$val;
                        }
                        $diaxmllist[]=$v_val;
                     }
                   }
                catch (Exception $sf)
                {
					$this->errormsg=$sf->getMessage();
					$this->writeLog($sf->getMessage());
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}
                 
                    
                
			}else{
                if($fiveone_way){
    				$diaxmllist=file_get_contents('http://bbzuan.kela.cn/jxc/data/diarough/diarough.data.xml');
                }else{
				    $diaxmllist=file_get_contents('http://dia.kela.cn/shell/diarough'.date('Ymd'));
                }
			}

			//初始化表
			if($diaxmllist!=''){
				global $db_dia;
				create_data($this->conf['source']);
				break;
			}else{
				$this->writeLog('no file');
				die();
				return false;
			}
		}
        $parcelStatus=array();
        $parcelException=array();
        //信息对象数组化
        //file_put_contents('diarough'.date('Ymd'),$diaxmllist);
        //$diaarrlist=simplexml_load_string($diaxmllist);
        $diaarrlist=$diaxmllist;

        if(is_null($diaarrlist)){
            $this->writeLog('no file');
            die();
        }
		$csvdir=ROOT_PATH."data/".$this->conf['source'];
		if(is_dir($csvdir)==''){
			//清空目录
			mkdir($csvdir);
		}else{
            cleardirfile($csvdir);
		}

		//获取活动钻信息 一维 goods_sn=>shop_price
		$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        $certAllowList=$this->certAllow();
		//生成标准文件
		$csvfie=$csvdir."/".$this->conf['source'].".csv";
		$handle=fopen($csvfie,"w");
		//$fiveonezuan=@explode(',',file_get_contents("http://bbzuan.kela.cn/admin/cnt.php?s=diarough"));

		$gid=array();
		//csv头信息
		$diaCsvTitle=array();
		foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
			$diaCsvTitle[$key]=iconv("UTF-8","GBK",$value);
		}
		fputcsv($handle,$diaCsvTitle);
        //$i=0;
        $diaarrlists=array();
        foreach($diaarrlist as $row){
            $diaarrlists[$row['CERTIFICATE_NO']]=$row;
        }

        $tmp=array();
		foreach($diaarrlists as $key => $row){
            	if($i==0){
                //file_put_contents('diarough.error.csv',$i.',0,POLISH_CARATS,COLOR_DESCR,CLARITY_01,CUT_CODE,POLISH,SYMMETRY,FLUORESCENCE,CERTIFIED_BY,CERTIFICATE_NO'."\r\n",FILE_APPEND);
            	}
            	//$i++;
			//$row=(array)$row; //xml对象转数组

			// 罗芳提供：
			$inclusion_v = array("CB", "BR", "SB", "ML");
           	if( in_array($row['INCLUSION'], $inclusion_v)) 
	          {
	              continue;
	          }
	          if($row['FLUORESCENCE']=='NON'){
				$row['FLUORESCENCE']='N';
			}
			if($row['FLUORESCENCE']=='MED'){
				$row['FLUORESCENCE']='M';
			}
			if($row['FLUORESCENCE']=='FNT'){
				$row['FLUORESCENCE']='F';
			}

			if($this->diaCommonFilter($row['POLISH_CARATS'], $row['COLOR_DESCR'], $row['CLARITY_01'], $row['CERTIFIED_BY'], $row['CERTIFICATE_NO'], true) == 0) 
            {
            	continue;
            }

			$warehouse='COM';

			$row1=array();
			//$row1["goods_id"]=$i; //$row["stone"],		//货号
			$row1["goods_sn"]='DRF'.$row['CERTIFICATE_NO'];//$row["PARCEL_ID"]; //$row["stone"],		//货号
			if($fiveonezuan&&!empty($fiveonezuan)&&!empty($fiveonezuan[0])){
				if(!in_array($row1['goods_sn'],$fiveonezuan)){
					continue;
				}
			}

			$row1["warehouse"]=$warehouse; //库房  SHCOM
			$row1["source"]=$this->conf['source']; //货品来源
			$row1["shape"]=$row["SHAPE_CODE"]; //"ROUNDS";				//形状
			$row1["carat"]=$row["POLISH_CARATS"]; //石重:Weight
			$row1["color"]=$row["COLOR_DESCR"]; //颜色:colorName
			$row1["clarity"]=$row["CLARITY_01"]; //净度:clarityName
			$row1["cut"]=$row["CUT_CODE"]; //切工:cutName
			$row1["sym"]=$row["SYMMETRY"]; //对称:symName
			$row1["po"]=$row["POLISH"]; //抛光:polName
			$row1["fl"]=$row["FLUORESCENCE"]; //荧光:fluoName
			$row1["diam"]=$row["MEASUREMENT_LENGTH"].'x'.$row["MEASUREMENT_WIDTH"].'x'.$row["MEASUREMENT_DEPTH"]; //尺寸:LxWxD
			$row1["depth"]=$row["DEPTH_PRCNTG"]; //全深比:Depth1
			$row1["table"]=$row["TABLE_PRCNTG"]; //台宽比:Table1
			$row1["cert"]=$row["CERTIFIED_BY"]; //证书:labName
			$row1["cert_id"]=$row["CERTIFICATE_NO"]; //证书号:certNo
			$row1["shop_price"]=$row["Total USD"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
			$row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE	//会员价计算出
			$row1["chengbenjia"]=$row["Total USD"]*DOLLARTORMB*JIAJIALV;
            		$row1["cts"]=$row["RATE"]; // 每克拉价
            		$row1["guojibaojia"]=$row["RAP_PRICE"]; // 国际报价
            		$row1["us_price_source"]=$row["Total USD"]; // 美元价 
			$row1["xianggangjia"]='';
			$row1["gemx_zhengshu"]='';
			$row1["source_discount"]=abs($row["RAP_DISC"]);
            $row1["mo_sn"]=''; //模号
			//修改活动价
			if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
				$row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
				$row1['is_active']=1;
			}
            /*if($certAllowList){
                if(!in_array($row1["cert"],$certAllowList)){
                    continue;
                }
            }*/
            echo $row1['goods_sn']."\t";
			if($this->download==0){
				try{ //待更新到diamond表的goods_id
                    $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
				}catch(Exception $e){
					$this->errormsg=$e->getMessage();
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}
			}
			//编号	库房	形状	重量	颜色	净度	切工	对称	抛光	荧光	尺寸	全深比	台宽比	证书	证书号	售价	会员价
			unset($row1['source']);
			fputcsv($handle,$row1);
		}
        if($tmp){
        	$this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
           		//修改净度为VS1的期货钻成本价
            	// foreach($val as $k1=>$v1){
            	// 	if($v1['clarity'] =='VS1' && $v1['good_type']==2){
            	// 		$val[$k1]['chengben_jia'] = round($v1['chengben_jia']*TIAOJIALV,2);
            	// 		$val[$k1]['shop_price'] = round($v1['shop_price']*TIAOJIALV);
            	// 	}
            	// }
                $this->adddiaDiamond($val,$this->conf['source']); 
            }
                          
            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
	}


	/**
	 *
	 * emd接口
	 * emd
	 */
	function m_emd(){
        $online=1;
        $doTryTime = 3;
        $tryTime = 0;
        if($online){
        while(1){
            $tryTime++;
			try{
                $post = array();
                $post['username'] = $this->conf['username'];
                $post['password'] = $this->conf['password'];
                $post['view'] = "xml";
                $this->writeLog($this->conf['server_url']);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->conf['server_url']);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
                curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                if(!empty($post)){
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                }
                $diaxmllist = curl_exec($ch);
                if($diaxmllist === false)
				{
				    $this->writeLog('Curl error: ' . curl_error($ch));
				}
            //file_put_contents("/data/www/cuteframe_boss/cron/diamond/emd.xml",$diaxmllist);
            file_put_contents(ROOT_PATH."emd.xml",$diaxmllist);
			}catch(Exception $e){
				$this->errormsg=$e->getMessage();
				$this->writeLog($e->getMessage());
				$this->runerror(__FILE__,__LINE__); //写进错误LOG
				return false;
			}
            if($doTryTime == $tryTime){
                exit();
            }
			//初始化表
			if($diaxmllist!=''){
				global $db_dia;
				create_data($this->conf['source']);
				break;
			}
		}
		//信息对象数组化
        }else{
            //$diaxmllist=file_get_contents("http://bbzuan.kela.cn/jxc/kgk.data.xml");
            $diaxmllist=file_get_contents(ROOT_PATH."/emd.xml");
            if($diaxmllist!=''){
                global $db_dia;
                create_data($this->conf['source']);
            }
        }
        $diaarrlist = simplexml_load_string($diaxmllist);

		$csvdir=ROOT_PATH."data/".$this->conf['source'];
		if(is_dir($csvdir)){
			//清空目录
            cleardirfile($csvdir);
		}else{
			mkdir($csvdir);
		}
		//获取活动钻信息 一维 goods_sn=>shop_price
		$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        // $certAllowList=$this->certAllow();
		//生成标准文件
		$csvfie=$csvdir."/".$this->conf['source'].".csv";
		$handle=fopen($csvfie,"w");
		//$fiveonezuan=@explode(',',file_get_contents("http://bbzuan.kela.cn/admin/cnt.php?s=kgk"));

		$gid=array();
		//csv头信息
		$diaCsvTitle=array();
		foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
			$diaCsvTitle[$key]=iconv("UTF-8","GBK",$value);
		}
		fputcsv($handle,$diaCsvTitle);
        $diaarrlists=array();
        foreach($diaarrlist as $row){
            $row=(array)$row;
            $diaarrlists[$row['Cert']]=$row;
        }
        $tmp=array();
		foreach($diaarrlists as $row){
			//$row=(array)$row; //xml对象转数组
            //var_dump($row);die;
            $warehouse="COM";
            $ShapeList[] = $row['Shape'];

            if($this->diaCommonFilter($row['Carat'], $row['Color'], $row['Clarity'], "GIA", $row['Cert'], true) == 0) 
            {
            	continue;
            }

			$row1=array();
			$row1["goods_sn"]="EMD".$row["StoneID"]; //$row["stone"],		//货号
			if($fiveonezuan&&!empty($fiveonezuan)&&!empty($fiveonezuan[0])){
				if(!in_array($row1['goods_sn'],$fiveonezuan)){
                    echo $row1['goods_sn']."\r\n";
					continue;
				}
			}
			$row1["warehouse"]=$warehouse; //库房  SHCOM
			$row1["source"]=$this->conf['source']; //货品来源
			$row1["shape"]=$row["Shape"]; //"ROUNDS";				//形状
			$row1["carat"]=$row["Carat"]; //石重:Weight
			$row1["color"]=$row["Color"]; //颜色:colorName
			$row1["clarity"]=$row["Clarity"]; //净度:clarityName
			$row1["cut"]=$row["Cut"]; //切工:cutName
			$row1["sym"]=$row["Symmetry"]; //对称:symName
			$row1["po"]=$row["Polish"]; //抛光:polName
			$row1["fl"]=$row["Fluorescence"]; //荧光:fluoName
			$row1["diam"]=$row["Measurement"]; //尺寸:LxWxD
			$row1["depth"]=$row["DepthLv"]; //全深比:Depth1
			$row1["table"]=$row["TableLv"]; //台宽比:Table1
			$row1["cert"]="GIA"; //证书:labName
			$row1["cert_id"]=$row["Cert"]; //证书号:certNo
			$row1["shop_price"]=$row["Total"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
			$row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE	//会员价计算出
			$row1["chengbenjia"]=$row["Total"]*DOLLARTORMB*JIAJIALV;
            $row1["cts"]=$row["Total"]/$row["Carat"]; // 每克拉价
            $row1["guojibaojia"]=$row["Rap"]; // 国际报价
            $row1["us_price_source"]=$row["Total"]; // 美元价 
			$row1["xianggangjia"]='';
			$row1["gemx_zhengshu"]='';
			$row1["source_discount"]=floatval($row["Discount"]);
            $row1["mo_sn"]=''; //模号

            //$row1["source_discount"]=$row1["source_discount"]?$row1["source_discount"][0]:0.0000;

			//修改活动价
			if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
				$row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
				$row1['is_active']=1;
			}
            /*if($certAllowList){
                if(!in_array($row1["cert"],$certAllowList)){
                    continue;
                }
            }*/
			echo $row1['goods_sn']."\r\n";
			if($this->download==0){
				try{ //待更新到diamond表的goods_id
					$getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
				}catch(Exception $e){
					$this->errormsg=$e->getMessage();
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}
			}

			//编号	库房	形状	重量	颜色	净度	切工	对称	抛光	荧光	尺寸	全深比	台宽比	证书	证书号	售价	会员价
			unset($row1['source']);
			fputcsv($handle,$row1);
		}

        if($tmp){
        	$this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
        		//修改净度为VS1的期货钻成本价
            	// foreach($val as $k1=>$v1){
            	// 	if($v1['clarity'] =='VS1' && $v1['good_type']==2){
            	// 		$val[$k1]['chengben_jia'] = round($v1['chengben_jia']*TIAOJIALV,2);
            	// 		$val[$k1]['shop_price'] = round($v1['shop_price']*TIAOJIALV);
            	// 	}
            	// }
                $this->adddiaDiamond($val,$this->conf['source']); 
            }
                          
            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this,"",$warehouse);
        }
	}

	/**
	 *
	 * gd接口
	 */
	function m_gd(){
		$online=1;
		$list=array();
        $fiveonezuan=true;
		$data=array("UserName"=>$this->conf['username'],"PassWord"=>$this->conf['password']);
		$post_array_string="";
		foreach($data as $key=>$value)
		{
			$post_array_string .= $key.'='.$value.'&';
		}
		$post_array_string = rtrim($post_array_string,'&');
		$post_url = $this->conf['server_url']. '?'. $post_array_string;
		// var_dump($post_url);
        //获取文件信息
		//while(1){
			if($online){
				try{
					$this->writeLog($post_url);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL,$post_url);
					curl_setopt($ch, CURLOPT_HEADER, 0);// 不带http header
					curl_setopt($ch, CURLOPT_VERBOSE, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不直接输出，返回到变量
					curl_setopt ($ch, CURLOPT_TIMEOUT, 30000);
                    $dialist=curl_exec($ch);
                    if($dialist === false)
					{
					    $this->writeLog('Curl error: ' . curl_error($ch));
					}
                    curl_close($ch);
                    file_put_contents(ROOT_PATH."gd.xml",$dialist);
                    $diaxmllist = $dialist;
                }catch(Exception $e){
					$this->errormsg=$e->getMessage();
					$this->writeLog($e->getMessage());
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}
			}else{
                if($fiveonezuan){
                    $diaxmllist=file_get_contents('http://bbzuan.kela.cn/jxc/data/gd/gd.data.xml');
                }else{
				    $diaxmllist=file_get_contents('gd.xml');
                }
			}
			//初始化表
			if($diaxmllist!=''){
				global $db_dia;
				create_data($this->conf['source']);
			}
		//}
		//信息对象数组化
		$diaarrlist = simplexml_load_string($diaxmllist);

		global $diaCat;
		$csvdir=ROOT_PATH."data/".$this->conf['source'];
		if(is_dir($csvdir)){
			//清空目录
			cleardirfile($csvdir);
		}else{
			mkdir($csvdir);
		}
		@file_put_contents($csvdir."/".$this->conf['source'].".data");
		//获取活动钻信息 一维 goods_sn=>shop_price
		$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        // $certAllowList=$this->certAllow();

		//生成标准文件
		$csvfie=$csvdir."/".$this->conf['source'].".csv";
		$handle=fopen($csvfie,"w");
		//$fiveonezuan=@explode(',',file_get_contents("http://bbzuan.kela.cn/admin/cnt.php?s=diamondbyhk"));

		$gid=array();
		//csv头信息
		$diaCsvTitle=array();
		foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
			$diaCsvTitle[$key]=iconv("UTF-8","CP936",$value);
		}
		fputcsv($handle,$diaCsvTitle);
        $diaarrlists=array();
        foreach($diaarrlist->ROW as $row){
            $row=(array)$row;
            $diaarrlists[$row['CertNo']]=$row;
        }
        $tmp=array();
		$i=0;
		$j=0;
        
		foreach($diaarrlists as $row){
        	//$row=(array)$row; //xml对象转数组
			$row1["goods_sn"]="GD".$row["PId"]; //货号
			if($fiveonezuan&&!empty($fiveonezuan)&&!empty($fiveonezuan[0])){
				if(!in_array($row1['goods_sn'],$fiveonezuan)){
					continue;
				}
			}

            /*if(!in_array($row['Milky_Name'],array('N'))){
				continue;
            }*/
            
            $xingzhuang = $row["S_Name"];
            $carat = $row["Carat"];
            $color=$row["GC_Name"];
            $clarity=$row["GQ_Name"];
            $cut=$row["GCT_Name"];
            $sym=$row["GSY_Name"];
            $pol=$row["GPO_Name"];
            $fl=$row["GFL_Name"];
            $cert=$row["CR_Name"];
            $cert_no=$row["CertNo"];

            $GD_XINGZHUANG = array(
                'RO'=>'ROUND',
                'P'=>'PRINCESS',
                'CU' => 'CUSHION',
                'RA' => 'SQ Radiant',
                'EM' => 'EMERALD',
                'PE' => 'PEAR',
                'M' => 'MARQUISE',
                'OV' => 'OVAL',
                'H' => 'HEART'
            );
            /*if(array_key_exists($xingzhuang,$GD_XINGZHUANG)){
                $xingzhuang=$GD_XINGZHUANG[$xingzhuang];
            }else{
                // continue;
            }*/

			$row1["warehouse"]="COM"; //库房  SHCOM
			$row1["source"]=$this->conf['source']; //货品来源
			$row1["shape"]=$xingzhuang; //"ROUNDS";				//形状
			$row1["carat"]=$carat; //石重:Weight
			$row1["color"]=$color; //颜色:colorName
			$row1["clarity"]=$clarity; //净度:clarityName
			$row1["cut"]=$cut; //切工:cutName
			$row1["sym"]=$sym; //对称:symName
			$row1["po"]=$pol; //抛光:polName
			$row1["fl"]=updat_fluor($fl); //荧光:fluoName
			$row1["diam"]=""; //尺寸:LxWxD
			$row1["depth"]=$row["TotDepth"]; //全深比:Depth1
			$row1["table"]=$row["Table1"]; //台宽比:Table1
			$row1["cert"]=$cert; //证书:labName
			$row1["cert_id"]=$cert_no; //证书号:certNo
            $row1["mo_sn"]=''; //模号
            //$row1["cts"]=$row["Total"]; // 每克拉价
            //$row1["guojibaojia"]=$row["Rap"]; // 国际报价
            //$row1["us_price_source"]=$row["Total"]*$row["Carat"]; // 美元价
            if($this->diaCommonFilter($row1['carat'], $row1['color'], $row1['clarity'], $row1['cert'], $row1['cert_id'], true) == 0) 
            {
            	continue;
            }
            
			$row1["shop_price"]=$row["GRate"]*$carat*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
			$row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE	//会员价计算出
			$row1["chengbenjia"]=$row["GRate"]*$carat*DOLLARTORMB*JIAJIALV;
			$row1["xianggangjia"]='';
			$row1["source_discount"]=0;
			$row1["xianggangjia"]='';
			$row1["gemx_zhengshu"]='';
			$row1["source_discount"]=$row["GPer"];

			//修改活动价
			if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
				$row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
				$row1['is_active']=1;
			}
            /*if($certAllowList){
                if(!in_array($row1["cert"],$certAllowList)){
                    continue;
                }
            }*/
		$list[]=$row1['goods_sn'];
            echo $row1['goods_sn']."\r\n";
			if($this->download==0){
				try{ //待更新到diamond表的goods_id
                    $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
				}catch(Exception $e){
					$this->errormsg=$e->getMessage();
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}
			}
			//编号	库房	形状	重量	颜色	净度	切工	对称	抛光	荧光	尺寸	全深比	台宽比	证书	证书号	售价	会员价
			unset($row1['source']);
			//修改活动价
			if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
				unset($row1['active_shop_price']);
				unset($row1['is_active']);
			}
			fputcsv($handle,$row1);
		}

		echo "have ".count($list);


        if($tmp){
        	$this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
            	// foreach($val as $k1=>$v1){
            	// 	if($v1['clarity'] =='VS1' && $v1['good_type']==2){
            	// 		$val[$k1]['chengben_jia'] = round($v1['chengben_jia']*TIAOJIALV,2);
            	// 		$val[$k1]['shop_price'] = round($v1['shop_price']*TIAOJIALV);
            	// 	}
            	// }
            	// 
        
                $this->adddiaDiamond($val,$this->conf['source']); 
    
            }
                          
            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
	}


	/**
	 *
	 * jb接口
	 * JB数据获取
	 */
	function m_jb()
	{
		//先建表
        global $db_dia;
		$online=0;
		$this->writeLog("Create temp data.");
		create_data($this->conf['source']);
		//裸钻形状
		$shape_arr=array('CUS'=>'CUSHION','EM'=>'EMERALD','MQ'=>'MARQUISE','PE'=>'PEAR','BR'=>'ROUNDS','PR'=>'PRINCESS');
		$diaList = array();

		if($online){
			//$diaxmllist=file_get_contents(ROOT_PATH."data/".'jb/jb.data.xml');
			$diaxmllist = file_get_contents(Util::getDomain().'/cron/diamond/jb2.data.xml');
			//信息对象数组化
			$diaarrlist = @simplexml_load_string($diaxmllist); //转成数组
			if($diaarrlist->LOGINMESSAGE!="0:"){
				exit('超时或没有数据');
			}
			$diaList = $diaarrlist->INVENTORY->ITEM;
		}
		else
		{
			// $post="shape=1,2,3,4,5,6,7,8,9,10,11&color=1,2,3,4,5,6,7,8&clarity=1,2,3,4,5,6,7&fromcarat=0.3&tocarat=10&loginName=".$this->conf['username']."&loginPassword=".$this->conf['password'];
			$aryPostBase = array(
				'shape' => array(1,2,3,4,5,6,7,8,9,10,11),
				'color' => array(1),
				'clarity' => array(1),
				'fromcarat' => 0.3,
				'tocarat' => 10,
				'loginName' => $this->conf['username'],
				'loginPassword' => $this->conf['password']
			);
			// 把抓取分成几种组合情况提交，避免一次提交过慢甚至没结果:
			// Written by Huangzhenyi
			$aryPostColor = array(
				array(1,2,3,4),
				array(5,6,7)
			);
			$aryPostClarity = array(
				array(1,2,3,4),
				array(5,6,7,8)
			);

			$aryDia = array();
			foreach ($aryPostColor as $key => $vColor) {
				foreach ($aryPostClarity as $k2 => $vClarity) {
					$aryPost = $aryPostBase;
					$aryPost['color'] = $vColor;
					$aryPost['clarity'] = $vClarity;

					$result = $this->postJB($this->conf['server_url'], $aryPost);
					if ($result->LOGINMESSAGE == "0:") {
						$aryResult = array();
						foreach($result->INVENTORY->ITEM as $row) {
				            $row = (array) $row;
				            $aryResult[$row['CER_NO']] = $row;
				        }
						if (is_array($aryResult)) {
							$this->writeLog('this post ttl:'. count($aryResult));
							$aryDia = array_merge($aryDia, $aryResult);
						}
					}else{
					    $this->writeLog($result->LOGINMESSAGE);
					    break;
					}
					$this->writeLog("sleep 10 seconds...");
					sleep(10);
					
				}
			}
	
			$diaList = $aryDia;
			// var_dump($diaList);die();
			if(count($diaList)<=0){
				$this->writeLog('Time out or no datas');
				exit();
			}
		}


		$csvdir=ROOT_PATH."data/".$this->conf['source'];
		if(is_dir($csvdir)){
			//清空目录
			// cleardirfile($csvdir);
		}else{
			mkdir($csvdir);
		}

		//获取活动钻信息 一维 goods_sn=>shop_price
		$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        $certAllowList=$this->certAllow();
		//生成标准文件
		$csvfie=$csvdir."/".$this->conf['source'].date("YmdHis").".csv";
		$handle=fopen($csvfie,"w");

		//$fiveonezuan=@explode(',',file_get_contents("http://bbzuan.kela.cn/admin/cnt.php?s=jb"));

		$gid=array();
		//csv头信息
		$diaCsvTitle=array();
		foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
			$diaCsvTitle[$key]=iconv("UTF-8","CP936",$value);
		}
		fputcsv($handle,$diaCsvTitle);
        $diaarrlists=array();
        foreach($diaList as $row){
            $row=(array)$row;
            $diaarrlists[$row['CER_NO']]=$row;
        }
        
        $tmp=array();
		foreach($diaarrlists as $row){
						
			if($this->diaCommonFilter($row['CARATS'], $row['COLOR'], $row['CLARITY'], $row['CERT'], $row['CER_NO'], true) == 0) 
	            {
	            	continue;
	            }

			$row1=array();
			$row1["goods_sn"]="J".$row["NO"]; //货号
			if($fiveonezuan&&!empty($fiveonezuan)&&!empty($fiveonezuan[0])){
				if(!in_array($row1['goods_sn'],$fiveonezuan)){
					continue;
				}
			}
			/*if(preg_match('/^J\d{8}$/',$row1["goods_sn"])){
				//continue;
			}*/



			$row1["warehouse"]='COM'; //库房
			$row1["source"]=$this->conf['source']; //货品来源
			$row1["shape"]=$shape_arr[$row["SHAPE"]]; //形状
			$row1["carat"]=$row["CARATS"]; //石重
			$row1["color"]=$row["COLOR"]; //颜色
			/*if(in_array($row1['color'],array('D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'))){

			}*/
			$row1["clarity"]=$row["CLARITY"]; //净度
			$row1["cut"]=$row["CUT"]; //切工
			$row1["sym"]=$row["SYMMETRY"]; //对称
			$row1["po"]=$row["POLISH"]; //抛光
			$row1["fl"]=updat_fluor($row["FL_INTENSITY"]); //荧光
			$row1["diam"]=''; //尺寸
			$row1["depth"]=$row["DEPTH"]; //全深比
			$row1["table"]=$row["WIDTH"]; //台宽比
			$row1["cert"]=$row["CERT"]; //证书
			$row1["cert_id"]=str_replace('*',"",$row["CER_NO"]); //证书号
			$guojibaojia = $row['INTL_PRICE']; //国际报价
			$us_price_per_carat = $guojibaojia*(100-$row['DISC'])/100;

			$row1["shop_price"]=$us_price_per_carat*$row["CARATS"]*DOLLARTORMB*JIAJIALV; //每颗价格
			$row1["member_price"]=''; //会员价计算出
			$row1["chengbenjia"]=$us_price_per_carat*$row["CARATS"]*DOLLARTORMB*JIAJIALV;
            $row1["cts"]=$us_price_per_carat; // 每克拉价
            $row1["guojibaojia"]=$guojibaojia; // 国际报价
            $row1["us_price_source"]=$us_price_per_carat*$row["CARATS"]; // 美元价 
			$row1["xianggangjia"]='';
			$row1["gemx_zhengshu"]='';
			$row1["source_discount"]=$row["DISC"];
            $row1["mo_sn"]=''; //模号

			//修改活动价
			if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
				$row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
				$row1['is_active']=1;
			}
            /*if($certAllowList){
                if(!in_array($row1["cert"],$certAllowList)){
                    continue;
                }
            }*/
			echo $row1['goods_sn']."\t ";
			if($this->download==0){
				try{ //待更新到diamond表的goods_id
					$getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
				}catch(Exception $e){
					$this->errormsg=$e->getMessage();
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}
			}

			//编号	库房	形状	重量	颜色	净度	切工	对称	抛光	荧光	尺寸	全深比	台宽比	证书	证书号	售价	会员价
			unset($row1['source']);
			fputcsv($handle,$row1);
		}

 		$this->writeLog("Fetch total:". count($tmp));
        if($tmp){
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
            	//修改净度为VS1的期货钻成本价
            	// foreach($val as $k1=>$v1){
            	// 	if($v1['clarity'] =='VS1' && $v1['good_type']==2){
            	// 		$val[$k1]['chengben_jia'] = round($v1['chengben_jia']*TIAOJIALV,2);
            	// 		$val[$k1]['shop_price'] = round($v1['shop_price']*TIAOJIALV);
            	// 	}
            	// }
                $this->adddiaDiamond($val,$this->conf['source']);   
            }            

            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }

	}

	public function postJB($url, $params)
	{
		$diaxmllist = "";
		try{						
			$ch = curl_init();
			$this->writeLog("POST ".$url);
			$this->writeLog("color:" .implode("," ,$params['color']));
			$this->writeLog("clarity:" .implode("," ,$params['clarity']));
			$this->writeLog("Loading...");
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_HEADER, false);
			// curl_setopt($ch, CURLOPT_VERBOSE, 1);
			curl_setopt($ch, CURLOPT_NOPROGRESS, 0);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0');
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($params));
			curl_setopt($ch,CURLOPT_TIMEOUT, 1000);
			$diaxmllist=curl_exec($ch);
			if($diaxmllist === false)
			{
			    $this->writeLog('Curl error: ' . curl_error($ch));
			}
			curl_close($ch);
			file_put_contents(ROOT_PATH."jb.xml",$diaxmllist);
		}catch(Exception $e){
			$this->errormsg=$e->getMessage();
			$this->runerror(__FILE__,__LINE__); //写进错误LOG
			return false;
		}
		//信息对象数组化
		$ary = @simplexml_load_string($diaxmllist); //转成数组
		return $ary;
	}

	/**
	 *
	 * kapu接口
	 * kapu数据抓取
	 */
	function m_kapu(){
		
		//取出条数
		$online=1;
		//获取文件信息
		while(1){
			if($online){
				try{
					$this->writeLog($this->conf['server_url']);
					$ch=curl_init();
					curl_setopt($ch,CURLOPT_URL,$this->conf['server_url']);
					curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
					curl_setopt($ch,CURLOPT_HEADER,0); // 不要http header 加快效率
					curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
					curl_setopt($ch,CURLOPT_USERPWD,$this->conf['username'].':'.$this->conf['password']);
					curl_setopt($ch,CURLOPT_TIMEOUT,1000);
					$output=curl_exec($ch);
					if($output === false){
					    $this->writeLog('Curl error: ' . curl_error($ch));
					    curl_close($ch);exit;
					}else if(preg_match("/401 Unauthorized/is",$output)){
					    $this->writeLog('Curl error: 401 Unauthorized');
					    curl_close($ch);exit;
					}
					curl_close($ch);
					$xml_arr=explode("\n",$output);
					//file_put_contents("/data/www/cuteframe_boss/cron/diamond/kapu.data.xml",$output);
                    file_put_contents(ROOT_PATH."kapu.xml",$output);
					$start=false;
					$end=false;
					foreach($xml_arr as $k=>$v){
						if(!$k){
							continue;
						}
						if(!$start){
							$start=preg_match('/<DocumentElement(.*?)/',$xml_arr[$k]);
							if(!$start){
								unset($xml_arr[$k]);
							}
						}
						if($end){
							unset($xml_arr[$k]);
						}
						if($start&&!$end){
							$end=preg_match('/<\/DocumentElement(.*?)/',$xml_arr[$k]);
						}
					}
				}catch(Exception $e){
					$this->errormsg=$e->getMessage();
					$this->writeLog($e->getMessage());
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}
				$xml_str=implode("\n",$xml_arr);
				$diaarrlist=@simplexml_load_string($xml_str);
				if($diaarrlist==""){
					exit("没有查到数据");
				}
			}else{
				try{
					$this->writeLog("http://bbzuan.kela.cn/jxc/data/kapu/kapu.data.xml");
				        $ch=curl_init();
                                        curl_setopt($ch,CURLOPT_URL,"http://bbzuan.kela.cn/jxc/data/kapu/kapu.data.xml");
                                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                        curl_setopt($ch,CURLOPT_HEADER,0); // 不要http header 加快效率
                                        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
                                        curl_setopt($ch,CURLOPT_TIMEOUT,1000);
                                        $output=curl_exec($ch);
                                        if($output === false)
										{
										    $this->writeLog('Curl error: ' . curl_error($ch));
										}
                                        curl_close($ch);
                                        $xml_arr=explode("\n",$output);
					                    //file_put_contents("/data/www/cuteframe_boss/cron/diamond/kapu.data.xml",$output);
                                        file_put_contents(ROOT_PATH."kapu.xml",$output);
                                        $start=false;
                                        $end=false;
                                        foreach($xml_arr as $k=>$v){
                                                if(!$k){
                                                        continue;
                                                }
                                                if(!$start){
                                                        $start=preg_match('/<DocumentElement(.*?)/',$xml_arr[$k]);
                                                        if(!$start){
                                                                unset($xml_arr[$k]);
                                                        }
                                                }
                                                if($end){
                                                        unset($xml_arr[$k]);
                                                }
                                                if($start&&!$end){
                                                        $end=preg_match('/<\/DocumentElement(.*?)/',$xml_arr[$k]);
                                                }
                                        }
                                }catch(Exception $e){
                                        $this->errormsg=$e->getMessage();
                                        $this->writeLog($e->getMessage());
                                        $this->runerror(__FILE__,__LINE__); //写进错误LOG
                                        return false;
                                }
                                $xml_str=implode("\n",$xml_arr);
                                $diaarrlist=@simplexml_load_string($xml_str);
                                if($diaarrlist==""){
                                        exit("没有查到数据");
                                }
			}
			//初始化表
			if(true){
				global $db_dia;
				create_data($this->conf['source']);
				break;
			}
		}

		//信息对象数组化
		$csvdir=ROOT_PATH."data/".$this->conf['source'];
		if(is_dir($csvdir)){
			//清空目录
			cleardirfile($csvdir);
		}else{
			mkdir($csvdir);
		}

		//获取活动钻信息 一维 goods_sn=>shop_price
		$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        $certAllowList=$this->certAllow();
		//生成标准文件
		$csvfie=$csvdir."/".$this->conf['source'].".csv";
		$handle=fopen($csvfie,"w");

		$gid=array();
		//csv头信息
		$diaCsvTitle=array();
		foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
			$diaCsvTitle[$key]=iconv("UTF-8","CP936",$value);
		}
		fputcsv($handle,$diaCsvTitle);
		//$fiveonezuan=@explode(',',file_get_contents("http://bbzuan.kela.cn/admin/cnt.php?s=kapu"));

	        //file_put_contents('kapu.data',serialize($diaarrlist));exit;
        $diaarrlists=array();
        foreach($diaarrlist as $key=>$row){
            $row=(array)$row;
            $diaarrlists[$row['CERTNO']]=$row;
        }
        $tmp=array();
        $cert_id=array();
		foreach($diaarrlists as $row){
			//$row=(array)$row;
			if(in_array($row["CERTNO"],array('2186486482','2176869554','5186173425','2196455094'))){
				continue;
  			}
			/*if($row["CT"]=='NA'){
				continue;
			}*/
			if ($row['TYPEIIA'] == 'HPHT') {
				continue;
			}
			if ($row['INSCRIPTION'] == '' || $row['INSCRIPTION'] == 'NA') {
				continue;
			}
			if (!($row['MILKY'] == 'NA' || $row['MILKY'] == '')) {
				continue;
			}
			if (!($row['TINGE'] == 'NA' || $row['TINGE'] == '' || $row['TINGE'] == 'VB')) {
				continue;
			}
			if (!($row['IT'] == 'NA' || $row['IT'] == '' || $row['IT'] == 'EC1')) {
				continue;
			}

			if($this->diaCommonFilter($row['CRTWT'], $row['CO'], $row['PU'], $row['LAB'], $row['CERTNO'], true) == 0) 
	        {
            	continue;
	        }

			$modfluo=array("FNT"=>"F","MED"=>"M","STG"=>"S","NON"=>"N");
			if(@array_key_exists($row['fl'],$modfluo)){
				$row['fl']=@$modfluo[$row['fl']];
			}
			/*if($row["PU"]=='SI2'||$row["PU"]=='SI2+'){
				continue;
			}*/
			$row1=array();
			$row1["goods_sn"]="K".str_replace(array("SH"," "),array("",""),$row["CERTNO"]);
			if($fiveonezuan&&!empty($fiveonezuan)&&!empty($fiveonezuan[0])){
				if(!in_array($row1['goods_sn'],$fiveonezuan)){
					continue;
				}
			}
			$row1["warehouse"]="COM"; //库房
			$row1["source"]=$this->conf['source']; //货品来源 
			$row1["shape"]=$row["SH"]; //形状
			$row1["carat"]=$row["CRTWT"]; //石重
			$row1["color"]=$row["CO"]; //颜色
			$row1["clarity"]=$row["PU"]; //净度
			$row1["cut"]=$row["CT"]; //切工
			$row1["sym"]=$row["SY"]; //对称
			$row1["po"]=$row["PO"]; //抛光
			$row1["fl"]=updat_fluor($row['FL']); //荧光
			$row1["diam"]=''; //尺寸
			$row1["depth"]=$row["DP"]; //台深比
			$row1["table"]=$row["TBL"]; //台宽比
			$row1["cert"]=$row["LAB"]; //证书
			$row1["cert_id"]=$row["CERTNO"]; //证书号
            $row["PricePerCarat"] = $row["RapRte"]*(1+$row["RapBk"]*0.01);
			$row1["shop_price"]=$row["PricePerCarat"]*$row["CRTWT"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
			$row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE	//会员价计算出
			$row1["chengbenjia"]=$row["PricePerCarat"]*$row["CRTWT"]*DOLLARTORMB*JIAJIALV;
            $row1["cts"]=$row["PricePerCarat"]; // 每克拉价
            $row1["guojibaojia"]=$row["RapRte"]; // 国际报价
            $row1["us_price_source"]=$row["PricePerCarat"]*$row["CRTWT"]; // 美元价 
			$row1["xianggangjia"]='';
			$row1["gemx_zhengshu"]='';
			$row1["source_discount"]=$row["RapBk"];
            $row1["mo_sn"]=''; //模号
			echo $row1['goods_sn']."\r\n";
			//修改活动价
			if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
				$row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
				$row1['is_active']=1;
			}
            /*if($certAllowList){
                if(!in_array($row1["cert"],$certAllowList)){
                    continue;
                }
            }*/
			if($this->download==0){
				try{ //待更新到diamond表的goods_id
					$getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
				}catch(Exception $e){
					$this->errormsg=$e->getMessage();
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}
			}

			//编号	库房	形状	重量	颜色	净度	切工	对称	抛光	荧光	尺寸	全深比	台宽比	证书	证书号	售价	会员价
			unset($row1['source']);
			fputcsv($handle,$row1);
		}
        if($tmp){
        	$this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
            	//修改净度为VS1的期货钻成本价
            	// foreach($val as $k1=>$v1){
            	// 	if($v1['clarity'] =='VS1' && $v1['good_type']==2){
            	// 		$val[$k1]['chengben_jia'] = round($v1['chengben_jia']*TIAOJIALV,2);
            	// 		$val[$k1]['shop_price'] = round($v1['shop_price']*TIAOJIALV);
            	// 	}
            	// }
                $this->adddiaDiamond($val,$this->conf['source']);   
            }            

            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
	}



	/**
	 *
	 * kgk接口
	 * KGK(凯吉凯)
	 * 2016-1-6 改进抓取策略
	 */
	function m_kgk_bak()
	{
        $isQihuo = false;

        $online=true;
        if($online) {

        	$ary_operation =  array(
        		array(2, 'GetStockGlobalXML', 'GetStockGlobalXMLResult'),   //期货
        	    array(1, 'GetStockLocalXML',  'GetStockLocalXMLResult')     //现货
        	    // StockPHYXMLNoBM,StockPHYXMLNoBMResult
    	 	);

        	$this->writeLog($this->conf['server_url']);
        	try {
			    $client = new soapclient($this->conf['server_url']); //新建soap连接
			} catch(SoapFault $e) {
			    $this->writeLog("An error was caught executing your request: {$e->getMessage()}");
			}
        	
        	$diaarrlists= array();

        	foreach ($ary_operation as $k => $v) {
        		$good_type  = $v[0];
        		$operation  = $v[1];
        		$result_act = $v[2];
        		$this->writeLog("good type:". $good_type);
        		$this->writeLog("operation:". $operation);
        		$this->writeLog("result_act:". $result_act);
        		try{
                    $result = $client->$operation(array('strEmail'=>$this->conf['username'], 'strPassword'=>$this->conf['password'])); //获取数据
                    $diaxmllist = $result->$result_act->any; //从节点中取xml格式数据
                    file_put_contents(ROOT_PATH."kgk". $good_type.".xml", $diaxmllist);
           			$ary_one = simplexml_load_string($diaxmllist);
           			foreach($ary_one as $key=>$row) {
			            $row = (array) $row;
			            $row['good_type'] = $good_type;
			            $diaarrlists[$row['certNo']] = $row;
			        }
	            }catch(Exception $e){
	                $this->errormsg=$e->getMessage();
	                $this->writeLog($e->getMessage());
	                $this->runerror(__FILE__,__LINE__); //写进错误LOG
	                return false;
	            }
        	}
            
            //初始化表
            if($diaxmllist!=''){
                global $db_dia;
                create_data($this->conf['source']);
            }

        }else{
            $diaxmllist=file_get_contents(ROOT_PATH."51.kgk.data.xml");
            //$diaxmllist=file_get_contents("http://bbzuan.kela.cn/jxc/kgk.data.xml");
            if($diaxmllist!=''){
                global $db_dia;
                create_data($this->conf['source']);
            }
            file_put_contents(ROOT_PATH."kgk.xml",$diaxmllist);
            $diaarrlist=simplexml_load_string($diaxmllist);
            foreach($diaarrlist as $key=>$row){
	            $row = (array) $row;
	            $diaarrlists[$row['certNo']]=$row;
	        }
        }
		
		
		$csvdir=ROOT_PATH."data/".$this->conf['source'];
		if(is_dir($csvdir)){
			//清空目录
			cleardirfile($csvdir);
		}else{
			mkdir($csvdir);
		}
		//获取活动钻信息 一维 goods_sn=>shop_price
		$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        $certAllowList=$this->certAllow();
		//生成标准文件
		$csvfie=$csvdir."/".$this->conf['source'].".csv";
		$handle=fopen($csvfie,"w");
		//$fiveonezuan=@explode(',',file_get_contents("http://bbzuan.kela.cn/admin/cnt.php?s=kgk"));

		$gid=array();
		//csv头信息
		$diaCsvTitle=array();
		foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
			$diaCsvTitle[$key]=iconv("UTF-8","GBK",$value);
		}
		fputcsv($handle,$diaCsvTitle);
       
        $tmp=array();

		foreach($diaarrlists as $row){

			// 地址为空的不要：
			if (trim($row['location'])=='' || empty($row['location'])) {
				// echo '<br/><font color="red">Location NULL</font><br/>';
				continue;
			}

			if (!($row['location']=='SH' || $row['location']=='CS'))
			{
				continue;
			}
			
			// SH 表示上海,CS表示深圳
			if ($row['location']=='SH' || $row['location']=='CS') {
				$isQihuo = false;
			}
			if ($row['location']=='HK') {
				$isQihuo = true;
			}

			//$row=(array)$row; //xml对象转数组
			$modfluo=array("FNT"=>"F","MED"=>"M","STG"=>"S","NON"=>"N");
			if(@array_key_exists($row['fluoName'],$modfluo)){
				$row['fluoName']=@$modfluo[$row['fluoName']];
			}

			if($this->diaCommonFilter($row['Weight'], $row['colorName'], $row['clarityName'], $row['labName'], $row['certNo'], $isQihuo) == 0) 
              {
                	continue;
              }
           
            if($isQihuo){
    			$warehouse='COM';
                
            }else{
            	
                if(preg_match('/^SH/',$row["stone"]) || preg_match('/^CS/',$row["stone"])){
                }else{
                    continue;
                }
    			$warehouse='SHCOM';
            }

            /*$header2=substr($row["stone"],0,2);
            if(in_array($header2,array('ST','SP','SR'))){
                continue;
            }*/
			$row1=array();
			// $row1["goods_sn"] = str_replace(array("SH","SZ","SG","SC"," "),array("","","","",""),$row["stone"]);
			$row1["goods_sn"] = $row["stone"];
			//$row["stone"],		//货号
			if($fiveonezuan&&!empty($fiveonezuan)&&!empty($fiveonezuan[0])){
				if(!in_array($row1['goods_sn'],$fiveonezuan)){
					continue;
				}
			}
			$row1["warehouse"]=$warehouse; //库房  SHCOM
			$row1["source"]=$this->conf['source']; //货品来源
			$row1["shape"]=$row["shape"]; //"ROUNDS";				//形状
			$row1["carat"]=$row["Weight"]; //石重:Weight
			$row1["color"]=$row["colorName"]; //颜色:colorName
			$row1["clarity"]=$row["clarityName"]; //净度:clarityName
			$row1["cut"]=$row["cutName"]; //切工:cutName
			$row1["sym"]=$row["symName"]; //对称:symName
			$row1["po"]=$row["polName"]; //抛光:polName
			$row1["fl"]=$row["fluoName"]; //荧光:fluoName
			$row1["diam"]=$row["LxWxD"]; //尺寸:LxWxD
			$row1["depth"]=$row["Depth1"]; //全深比:Depth1
			$row1["table"]=$row["Table1"]; //台宽比:Table1
			$row1["cert"]=$row["labName"]; //证书:labName
			$row1["cert_id"]=$row["certNo"]; //证书号:certNo 
			$row1["shop_price"]=$row["Amount"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
			$row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE	//会员价计算出
			$row1["chengbenjia"]=$row["Amount"]*DOLLARTORMB*JIAJIALV;
            $row1["cts"]=$row["Rate"]; // 每克拉价
            $row1["guojibaojia"]=$row["intRap"]; // 国际报价
            $row1["us_price_source"]=$row["Rate"]*$row["Weight"]; // 美元价 
			$row1["xianggangjia"]='';
			$row1["gemx_zhengshu"]='';
			$row1["source_discount"]=$row["discount"];
			$row1["good_type"]= (($isQihuo)?2:1); //现货 期货 or $row['good_type'];
            $row1["mo_sn"]=''; //模号

			//修改活动价
			if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
				$row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
				$row1['is_active']=1;
			}
           
			echo $row1['goods_sn']."\r\n";

			if($this->download==0){
				try{ //待更新到diamond表的goods_id
					$getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
				}catch(Exception $e){
					$this->errormsg=$e->getMessage();
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}
			}

			//编号	库房	形状	重量	颜色	净度	切工	对称	抛光	荧光	尺寸	全深比	台宽比	证书	证书号	售价	会员价
			unset($row1['source']);
			fputcsv($handle,$row1);
			
		}

        if($tmp){
        	$this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
            	//修改净度为VS1的期货钻成本价
            	// foreach($val as $k1=>$v1){
            	// 	if($v1['clarity'] =='VS1' && $v1['good_type']==2){
            	// 		$val[$k1]['chengben_jia'] = round($v1['chengben_jia']*TIAOJIALV,2);
            	// 		$val[$k1]['shop_price'] = round($v1['shop_price']*TIAOJIALV);
            	// 	}
            	// }
                $this->adddiaDiamond($val,$this->conf['source']);   
            }            

            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this,"",$warehouse);
        }
	}

        /**
     *
     * kgk接口
     * KGK(凯吉凯)
     * 2016-1-6 改进抓取策略
     */
    function m_kgk()
    {
        $isQihuo = false;

        $online=true;
        if($online) {

            try{
                //$this->writeLog("http://kgk.cc/WCFKGKccClientService/Service1.svc/GetStockXML/lihai@china.com/kela123");
                //$diaxmllist=file_get_contents("http://kgk.cc/WCFKGKccClientService/Service1.svc/GetStockXML/lihai@china.com/kela123");
                $this->writeLog("http://kgk.cc/client_lots?username=lihai@china.com&password=12345kela");
                $diaxmllist=file_get_contents("http://kgk.cc/client_lots?username=lihai@china.com&password=12345kela");
  
            }catch(Exception $e){
                $this->errormsg=$e->getMessage();
                $this->writeLog($e->getMessage());
                $this->runerror(__FILE__,__LINE__); //写进错误LOG
                return false;
            }
            //var_dump($diaxmllist);die;
            //初始化表
            if($diaxmllist!=''){
                global $db_dia;
                create_data($this->conf['source']);
                //break;
            }

            file_put_contents(ROOT_PATH.'kgk.xml',$diaxmllist);
            //$diaarrlist=simplexml_load_string($diaxmllist);
            //echo "<pre>";
            $res=json_decode($diaxmllist,true);
            //print_r($res);
            if(is_array($res) && !empty($res['data']['inventories'])){
               $diaarrlist =$res['data']['inventories'];
            }else
               $diaarrlist =array();
        }else{
            $diaxmllist=file_get_contents(ROOT_PATH."51.kgk.data.xml");
            //$diaxmllist=file_get_contents("http://bbzuan.kela.cn/jxc/kgk.data.xml");
            if($diaxmllist!=''){
                global $db_dia;
                create_data($this->conf['source']);
            }
            file_put_contents(ROOT_PATH."kgk.xml",$diaxmllist);
            $diaarrlist=simplexml_load_string($diaxmllist);
        }

        foreach($diaarrlist as $key=>$row){
            $row = (array) $row;
            $diaarrlists[$row['certno']]=$row;
        }
        //var_dump($diaarrlists);die;
        $csvdir=ROOT_PATH."data/".$this->conf['source'];
        if(is_dir($csvdir)){
            //清空目录
            cleardirfile($csvdir);
        }else{
            mkdir($csvdir);
        }
        //获取活动钻信息 一维 goods_sn=>shop_price
        $activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        $certAllowList=$this->certAllow();
        //生成标准文件
        $csvfie=$csvdir."/".$this->conf['source'].".csv";
        $handle=fopen($csvfie,"w");
        //$fiveonezuan=@explode(',',file_get_contents("http://bbzuan.kela.cn/admin/cnt.php?s=kgk"));

        $gid=array();
        //csv头信息
        $diaCsvTitle=array();
        foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
            $diaCsvTitle[$key]=iconv("UTF-8","GBK",$value);
        }
        fputcsv($handle,$diaCsvTitle);
       
        $tmp=array();

        foreach($diaarrlists as $row){
        	

            // 地址为空的不要：
            if (trim($row['location'])=='' || empty($row['location'])) {
                // echo '<br/><font color="red">Location NULL</font><br/>';
                continue;
            }

            if ($row['location']<>'SH')
            {
                continue;
            }
            if($row['flor']=='VST'){
            	continue;
            }

            // SH 表示上海,CS表示深圳
            /*if ($row['location']=='SH' || $row['location']=='CS') {
                $isQihuo = true;
            }
            if ($row['location']=='HK') {
                $isQihuo = true;
            }*/
            $isQihuo = true;//都过滤掉|只要SI2的都过滤掉 by罗芳
            //$row=(array)$row; //xml对象转数组
            $modfluo=array("FNT"=>"F","MED"=>"M","STG"=>"S","NON"=>"N");
            if(@array_key_exists($row['flor'],$modfluo)){
                $row['flor']=@$modfluo[$row['flor']];
            }

            if($this->diaCommonFilter($row['size'], $row['color'], $row['clarity'], $row['labs'], $row['certno'], $isQihuo) == 0) 
              {
                    continue;
              }
           
            if($isQihuo){
                $warehouse='COM';
                
            }else{
                
                //if(preg_match('/^SH/',$row["stone"]) || preg_match('/^CS/',$row["stone"])){
                //}else{
                    //continue;
                //}
                $warehouse='SHCOM';
            }

            /*$header2=substr($row["stone"],0,2);
            if(in_array($header2,array('ST','SP','SR'))){
                continue;
            }*/
            $row1=array();
            // $row1["goods_sn"] = str_replace(array("SH","SZ","SG","SC"," "),array("","","","",""),$row["stone"]);
            $row1["goods_sn"] = $row["lotno"];
            //$row["stone"],        //货号
            /*
            if($fiveonezuan&&!empty($fiveonezuan)&&!empty($fiveonezuan[0])){
                if(!in_array($row1['goods_sn'],$fiveonezuan)){
                    continue;
                }
            }*/

                if($row["labs"] != 'GIA')
                {
                    continue;
                }
                if(!in_array($row["color"], array('D', 'E', 'F', 'G', 'H', 'I', 'J', 'K')))
                {
                    continue;
                }
                if(!in_array($row["clarity"], array('FL', 'IF', 'VVS1', 'VVS2', 'SI1', 'VS1', 'VS2')))
                {
                    continue;
                }
                if($row["size"] < 0.3)
                {
                    continue;
                }

            $row1["warehouse"]=$warehouse; //库房  SHCOM
            $row1["source"]=$this->conf['source']; //货品来源
            $row1["shape"]=$row["shape"]; //"ROUNDS";               //形状
            $row1["carat"]=$row["size"]; //石重:Weight
            $row1["color"]=$row["color"]; //颜色:colorName
            $row1["clarity"]=$row["clarity"]; //净度:clarityName
            $row1["cut"]=$row["culet"]; //切工:cutName
            $row1["sym"]=$row["symn"]; //对称:symName
            $row1["po"]=$row["poli"]; //抛光:polName
            $row1["fl"]=$row["flor"]; //荧光:fluoName
            $row1["diam"]=$row["measurementOriginal"]; //尺寸:LxWxD
            $row1["depth"]=$row["depth"]; //全深比:Depth1
            $row1["table"]=$row["table_name"]; //台宽比:Table1
            $row1["cert"]=$row["labs"]; //证书:labName
            $row1["cert_id"]=$row["certno"]; //证书号:certNo 
            $row1["shop_price"]=$row["amount"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
            $row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE  //会员价计算出
            $row1["chengbenjia"]=$row["amount"]*DOLLARTORMB*JIAJIALV;
            $row1["cts"]=$row["raparate"]; // 每克拉价
            $row1["guojibaojia"]=$row["raparate"]; // 国际报价
            $row1["us_price_source"]=round($row["raparate"]*$row["size"]*(100+$row["backrate"])/100,2); // 美元价 
            $row1["xianggangjia"]='';
            $row1["gemx_zhengshu"]='';
            $row1["source_discount"]=$row["backrate"];
            $row1["good_type"]=1; //现货 期货 or $row['good_type'];
            $row1["mo_sn"]=''; //模号

            //修改活动价
            if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
                $row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
                $row1['is_active']=1;
            }
            //echo "<pre>";
            //print_r($row);
            echo $row1['goods_sn']."\r\n";

            if($this->download==0){
                try{ //待更新到diamond表的goods_id
                    $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
                }catch(Exception $e){
                    $this->errormsg=$e->getMessage();
                    $this->runerror(__FILE__,__LINE__); //写进错误LOG
                    return false;
                }
            }

            //编号    库房  形状  重量  颜色  净度  切工  对称  抛光  荧光  尺寸  全深比 台宽比 证书  证书号 售价  会员价
            unset($row1['source']);
            fputcsv($handle,$row1);
            
        }
        if($tmp){
            $this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
                //修改净度为VS1的期货钻成本价
                // foreach($val as $k1=>$v1){
                //  if($v1['clarity'] =='VS1' && $v1['good_type']==2){
                //      $val[$k1]['chengben_jia'] = round($v1['chengben_jia']*TIAOJIALV,2);
                //      $val[$k1]['shop_price'] = round($v1['shop_price']*TIAOJIALV);
                //  }
                // }
                $this->adddiaDiamond($val,$this->conf['source']);   
            }            

            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this,"",$warehouse);
        }
    }


	/**
	 *
	 * karp接口
	 * karp(karp)
	 * 数据量相对较少，单文件处理
	 */
	function m_karp(){
        $isQihuo=true;
        $online=1;
        if($online){
            while(1){
                try{
                	$this->writeLog("http://www.karpgroup.com/ftp/KELA_CN.xml");
                    $diaxmllist=file_get_contents("http://www.karpgroup.com/ftp/KELA_CN.xml");
                }catch(Exception $e){
                    $this->errormsg=$e->getMessage();
                    $this->writeLog($e->getMessage());
                    $this->runerror(__FILE__,__LINE__); //写进错误LOG
                    return false;
                }
                //初始化表
                if($diaxmllist!=''){
                    global $db_dia;
                    create_data($this->conf['source']);
                    break;
                }
            }
            //信息对象数组化
        }else{
        	$this->writeLog(ROOT_PATH."51.kgk.data.xml");
            $diaxmllist=file_get_contents(ROOT_PATH."51.kgk.data.xml");
            //$diaxmllist=file_get_contents(ROOT_PATH."KELA_CN.xml");
                if($diaxmllist!=''){
                                    global $db_dia;
                                    create_data($this->conf['source']);
                            }
        }
        file_put_contents(ROOT_PATH.'karp.xml',$diaxmllist);
		$diaarrlist=simplexml_load_string($diaxmllist);
		$csvdir=ROOT_PATH."data/".$this->conf['source'];
		if(is_dir($csvdir)){
			//清空目录
			cleardirfile($csvdir);
		}else{
			mkdir($csvdir);
		}
		//获取活动钻信息 一维 goods_sn=>shop_price
		$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        $certAllowList=$this->certAllow();
		//生成标准文件
		$csvfie=$csvdir."/".$this->conf['source'].".csv";
		$handle=fopen($csvfie,"w");
		//$fiveonezuan=@explode(',',file_get_contents("http://bbzuan.kela.cn/admin/cnt.php?s=kgk"));

		$gid=array();
		//csv头信息
		$diaCsvTitle=array();
		foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
			$diaCsvTitle[$key]=iconv("UTF-8","GBK",$value);
		}

		fputcsv($handle,$diaCsvTitle);
        $diaarrlists=array();
        foreach($diaarrlist as $key=>$row){
            $row=(array)$row;
            $diaarrlists[$row['certNo']]=$row;
        }
        $tmp=array();
		foreach($diaarrlists as $row){
			//$row=(array)$row; //xml对象转数组
			/*$modfluo=array("FNT"=>"F","MED"=>"M","STG"=>"S","NON"=>"N");
			if(@array_key_exists($row['fluoName'],$modfluo)){
				$row['fluoName']=@$modfluo[$row['fluoName']];
			}*/

			if($this->diaCommonFilter($row['Weight'], $row['colorName'], $row['clarityName'], $row['labName'], $row['certNo'], $isQihuo) == 0) 
              {
                	continue;
              }
			

            if($isQihuo){
    			$warehouse='COM';
            }else{
                /*if(preg_match('/^SH/',$row["stone"])){
                }else{ 
                    //continue;
                }*/
    			$warehouse='SHCOM';
            }
     
            //$header2=substr($row["stone"],0,2);
            //if(in_array($header2,array('ST','SP','SR'))){
            //    continue;
            //}

			$row1=array();
			//$row1["goods_sn"]="X".str_replace(array("SH","SZ","SG","SC"," "),array("","","","",""),$row["stone"]); //$row["stone"],		//货号
			$row1["goods_sn"]="X".$row["stone"]; //货号		
			if($fiveonezuan&&!empty($fiveonezuan)&&!empty($fiveonezuan[0])){
				if(!in_array($row1['goods_sn'],$fiveonezuan)){
					continue;
				}
			}
			$row1["warehouse"]=$warehouse; //库房  SHCOM
			$row1["source"]=$this->conf['source']; //货品来源
			$row1["shape"]=$row["shape"]; //"ROUNDS";				//形状
			$row1["carat"]=$row["Weight"]; //石重:Weight
			$row1["color"]=$row["colorName"]; //颜色:colorName
			$row1["clarity"]=$row["clarityName"]; //净度:clarityName
			$row1["cut"]=$row["cutName"]; //切工:cutName
			$row1["sym"]=$row["symName"]; //对称:symName
			$row1["po"]=$row["polName"]; //抛光:polName
			$row1["fl"]=$row["fluoName"]; //荧光:fluoName
			$row1["diam"]=$row["LxWxD"]; //尺寸:LxWxD
			$row1["depth"]=$row["Depth1"]; //全深比:Depth1
			$row1["table"]=$row["Table1"]; //台宽比:Table1
			$row1["cert"]=$row["labName"]; //证书:labName
			$row1["cert_id"]=$row["certNo"]; //证书号:certNo
			$row1["shop_price"]=$row["Amount"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
			$row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE	//会员价计算出
			$row1["chengbenjia"]=$row["Amount"]*DOLLARTORMB*JIAJIALV;

            $row1["cts"]=$row["Rate"]; // 每克拉价
            $row1["guojibaojia"]=$row["intRap"]; // 国际报价 
            $row1["us_price_source"]=$row["Amount"]; // 美元价 

			$row1["xianggangjia"]='';
			$row1["gemx_zhengshu"]='';
			$row1["source_discount"]=$row["discount"];
            $row1["mo_sn"]=''; //模号

			//修改活动价
			if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
				//$row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
				//$row1['is_active']=1;
			}
            /*if($certAllowList){
                if(!in_array($row1["cert"],$certAllowList)){
                    //continue;
                }
            }*/
			echo $row1['goods_sn']."\r\n";
			if($this->download==0){
				try{ //待更新到diamond表的goods_id
					$getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
				}catch(Exception $e){
					$this->errormsg=$e->getMessage();
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}
			}

			//编号	库房	形状	重量	颜色	净度	切工	对称	抛光	荧光	尺寸	全深比	台宽比	证书	证书号	售价	会员价
			unset($row1['source']);
			fputcsv($handle,$row1);
		}
        if($tmp){
        	$this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
            	//修改净度为VS1的期货钻成本价
            	// foreach($val as $k1=>$v1){
            	// 	if($v1['clarity'] =='VS1' && $v1['good_type']==2){
            	// 		$val[$k1]['chengben_jia'] = round($v1['chengben_jia']*TIAOJIALV,2);
            	// 		$val[$k1]['shop_price'] = round($v1['shop_price']*TIAOJIALV);
            	// 	}
            	// }
                $this->adddiaDiamond($val,$this->conf['source']);   
            }            

            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this,"",$warehouse);
        }
	}
	/**
	 *
	 * hy接口
	 * hy数据获取
	 */
	function m_hy(){
		//先建表
		global $db_dia;
		create_data($this->conf['source']);
        $activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        $certAllowList=$this->certAllow();
		$csvdir=ROOT_PATH."data/".$this->conf['source'];
		if(is_dir($csvdir)){
			//清空目录
			cleardirfile($csvdir);
		}else{
			mkdir($csvdir);
		}

		//净度列表
		$Clarity_V=array("kela");
		$Clarity_allow=array("IF","VVS1","VVS2","VS1","VS2","SI1");

		define('ROOT_PATH',str_replace('include/dia_api.php','',str_replace('\\','/',__FILE__)));

		//生成标准文件
        $csvfie1=$csvdir."/../".$this->conf['source'].".csv";
        $csvfie=$csvdir."/".$this->conf['source'].".csv";

	$url = "http://systems.srkexport.com/inventory/myinventoryJSON.asmx/GetSRKLiveStock?ShapeList=&ColorList=D,E,F,G,H,I,J&ClarityList=FL,IF,VVS1,VVS2,VS1,VS2,SI1&FromCarat=0.30&ToCarat=20.00&StoneID=&LoginName=slk88&PassWord=kela123456";
        exec('wget "'.$url.'"  -O "'.$csvfie1.'"');
        $this->writeLog($url);
        //file_put_contents($csvfie1,file_get_contents($url));
        file_put_contents(ROOT_PATH.'hy.xml',file_get_contents($url));
        if(!is_file($csvfie1)){
            $this->runerror(__FILE__,__LINE__.'file doesn\'t exists'); //写进错误LOG
            $this->writeLog("没有查到数据");
            exit();
        }
        //$csvfie1 = ROOT_PATH."shell/hy.1.data";
		$c=file_get_contents($csvfie1);
		//$c=file_get_contents($url);
		//$diaarrlist1=simplexml_load_string($c);
		$diaarrlist1=json_decode($c);

		foreach($diaarrlist1->Result as $key => $val){
		    $diaarrlist[]=objectToArray($val);
		}
		//$fiveonezuan=@explode(',',file_get_contents("http://bbzuan.kela.cn/admin/cnt.php?s=hy"));
        $handle=fopen($csvfie,"w");
            $diaarrlists=array();
            foreach($diaarrlist as $key=>$row){
                $row=(array)$row;
                $diaarrlists[$row['CERTIFICATENO']]=$row;
            }
            $tmp=array();
			foreach($diaarrlists as $row){
				//$row=(array)$row; //xml对象转数组 
				//
				if($this->diaCommonFilter($row['WEIGHT'], $row['COLOR'], $row['CLARITY'], $row['LAB'], $row['CERTIFICATENO'], true) == 0) 
	            	{
	            		continue;
	            	}

				if(in_array(strtoupper($row['LUSTER']),array('M1','M2','M3','MILKY'))){
					continue;
				}
				
				if(isset($row['SRK_COMMENT']) && !is_array($row['SRK_COMMENT']) && strpos($row['SRK_COMMENT'],'MILKY')>0){
					continue;
				}
	              if (strpos($row['SRK_COMMENT'], 'LOCATION  NEWYORK') !== false) {
                    	continue;
	              }

				if($row['BROWN_INCLUSION']=='BROWN' 
					|| $row['BROWN_INCLUSION']=='MIX TINGE'){
					continue;
				}
           
           		if($row['FLUORESCENCE']=='NONE'){
                        $row['FLUORESCENCE']='N';
                   }elseif($row['FLUORESCENCE']=='FAINT'){
                        $row['FLUORESCENCE']='F';
                   }elseif($row['FLUORESCENCE']=='MEDIUM'){
                        $row['FLUORESCENCE']='M';
                   }elseif($row['FLUORESCENCE']=='STRONG'){
                        $row['FLUORESCENCE']='S';
                   }

				$row1=array();
				$row1["goods_sn"]="H".str_replace(array("SH"," "),array("",""),$row['STONEID']); //$row["stone"];//货号
				if($fiveonezuan&&!empty($fiveonezuan)&&!empty($fiveonezuan[0])){
					if(!in_array($row1['goods_sn'],$fiveonezuan)){
						continue;
					}
				}
				$row1["warehouse"]="COM"; //库房  SHCOM
				$row1["source"]=$this->conf['source']; //货品来源
				$row1["shape"]=$row['SHAPE']; //"ROUNDS";/形状
				$row1["carat"]=$row['WEIGHT']; //石重:Weight
				$row1["color"]=$row['COLOR']; //颜色:colorName
				$row1["clarity"]=$row['CLARITY']; //净度:clarityName
				$row1["cut"]=$row['CUT']; //切工:cutName
				$row1["po"]=$row['POLISH']; //抛光:polName
				$row1["sym"]=$row['SYMMETRY']; //对称:symName
				$row1["fl"]=updat_fluor($row['FLUORESCENCE']); //荧光:fluoName
				$row1["diam"]=$row['MEASUREMENT']; //尺寸:LxWxD
				$row1["depth"]=$row['TOTDEPTH']; //全深比:Depth1
				$row1["table"]=$row['TABLE1']; //台宽比:Table1
				$row1["cert"]=$row['LAB']; //证书:labName
				$row1["cert_id"]=str_replace("L","",$row['CERTIFICATENO']); //证书号:certNo
				$row1["shop_price"]=$row['PRATE']*$row['WEIGHT']*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
				$row1["member_price"]=""; //$data["Amount"] * * MEMBER_RATE	//会员价计算出
				$row1["chengbenjia"]=$row['PRATE']*$row['WEIGHT']*DOLLARTORMB*JIAJIALV;
                	$row1["cts"]=$row["PRATE"]; // 每克拉价
                	$row1["guojibaojia"]=$row["PRRATE"]; // 国际报价
                	$row1["us_price_source"]=$row["PRATE"]*$row["WEIGHT"]; // 美元价 
				$row1["xianggangjia"]='';
				$row1["gemx_zhengshu"]='';
				$row1["source_discount"]=$row['PPER'];
           		$row1["mo_sn"]=''; //模号

				//修改活动价
				if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
					$row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
					$row1['is_active']=1;
				}
                		/*if($certAllowList){
                		    if(!in_array($row1["cert"],$certAllowList)){
                    			    continue;
                    			}
                		}*/
				/*if($row['CUT']==''){
					continue;
				}*/
				echo $row1["goods_sn"]."\r\n";//货号
			
				if($this->download==0){
					try{ //待更新到diamond表的goods_id
						//如果队列中含有相同钻，则不添加
                        $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                        if($getAddDiamond){
                            $tmp[]=$getAddDiamond;
                        }
					}catch(Exception $e){
						$this->errormsg=$e->getMessage();
						$this->runerror(__FILE__,__LINE__); //写进错误LOG
						return false;
					}
				}
				//编号	库房	形状	重量	颜色	净度	切工	对称	抛光	荧光	尺寸	全深比	台宽比	证书	证书号	售价	会员价
				unset($row1['source']);
				fputcsv($handle,$row1);
			}
        if($tmp){
        	$this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
            	//修改净度为VS1的期货钻成本价
            	// foreach($val as $k1=>$v1){
            	// 	if($v1['clarity'] =='VS1' && $v1['good_type']==2){
            	// 		$val[$k1]['chengben_jia'] = round($v1['chengben_jia']*TIAOJIALV,2);
            	// 		$val[$k1]['shop_price'] = round($v1['shop_price']*TIAOJIALV);
            	// 	}
            	// }
                $this->adddiaDiamond($val,$this->conf['source']);   
            }            

            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
	}

	/**
	 *
	 * 深圳一加一
	 * 
	 */
	function m_enjoy(){
        $online=1;
        $doTryTime = 3;
        $tryTime = 0;
        $i=1;
        $newstr='';
        if($online){
          
			try{
                //建立接口链接
                $client = new SoapClient($this->conf['server_url']);
                $this->writeLog($this->conf['server_url']);
                //设置字符编码
                $client->soap_defencoding = 'utf-8';
                $client->xml_encoding = 'utf-8';  


                //下面调用对应WebService上的函数"login"
                //PHP手册中推荐使用__soapCall调用webservice函数

                //方法：
                //$ukeyobj = $client->login(array('userKey'=>"用户名",'password'=>"密码"));

                //读取用户KEY
                $ukeyobj = $client->login(array('userKey'=>"BDD数据对接",'password'=>"111111"));
                $ukey = $ukeyobj->loginResult;

                //调用钻石数据返回XML
                //先获取getDiamonds函数，getDiamonds(array('key'=>'用户KEY', 'find'=>'筛选条件', 'pages'=>'第几页', 'pageSize'=>'一页几条', 'pageLength'=>'总数据', 'sort'=>'按什么排序', 'webRate'=>'钻石系数'))->getDiamondsResult;
                //查看到返回的XML值数组为[any];
            while(1){
                //$diamondsxmlstr = $client->getDiamonds(array('key'=>$ukey, 'find'=>"areaState=深圳,香港(深圳)&certificate=GIA&color=D,E,F,G,H,I,J,K&clarity=FL,IF,VVS1,VVS2,SI1,VS1,VS2", 'pages'=>$i, 'pageSize'=>'100', 'pageLength'=>'1000', 'sort'=>'carat', 'webRate'=>'1'))->getDiamondsResult->any;
                $diamondsxmlstr = $client->getDiamonds(array('key'=>$ukey, 'find'=>"areaState=深圳&certificate=GIA&color=D,E,F,G,H,I,J,K&clarity=FL,IF,VVS1,VVS2,SI1,VS1,VS2", 'pages'=>$i, 'pageSize'=>'100', 'pageLength'=>'1000', 'sort'=>'carat', 'webRate'=>'1'))->getDiamondsResult->any;
                //echo (string)$diamondsxmlstr;

                if(empty($diamondsxmlstr)){
                    break;
                }
                //转换$diamondsxmlstr值为标准XML　----------开始
                $newstr .= $diamondsxmlstr;
                $i++;
            }
            
                $newxml = '<?xml version="1.0" encoding="utf-8"?><diamonds>';

                $index = strpos($newstr, '<t_diamonds');
                while($index != ""){
                    $newxml = $newxml.'<t_diamonds>';
                    $newstr = substr($newstr, $index);
                    $newstr = substr($newstr, strpos($newstr, '>')+1);
                    $newxml = $newxml.substr($newstr, 0, strpos($newstr, '</t_diamonds>'));
                    $newxml = $newxml.'</t_diamonds>';
                    $index = strpos($newstr, '<t_diamonds');
                }

                $diaxmllist = $newxml.'</diamonds>';
                //转换$diamondsxmlstr值为标准XML　----------结束
                //file_put_contents(ROOT_PATH."emd.xml",$diaxmllist);
			}catch(SoapFault $e){
				$this->errormsg=$e->getMessage();
				$this->writeLog($e->getMessage());
				$this->runerror(__FILE__,__LINE__); //写进错误LOG
				return false;
			}
            
         
			//初始化表
			if($diaxmllist!=''){
				global $db_dia;
				create_data($this->conf['source']);
			}
		//信息对象数组化
        }else{
            //$diaxmllist=file_get_contents("http://bbzuan.kela.cn/jxc/kgk.data.xml");
            $diaxmllist=file_get_contents(ROOT_PATH."/enjoy.xml");
            if($diaxmllist!=''){
                global $db_dia;
                create_data($this->conf['source']);
            }
        }
        file_put_contents(ROOT_PATH.'enjoy.xml',$diaxmllist);
        $diaarrlist = simplexml_load_string($diaxmllist);
        
		$csvdir=ROOT_PATH."data/".$this->conf['source'];
		if(is_dir($csvdir)){
			//清空目录
            cleardirfile($csvdir);
		}else{
			mkdir($csvdir);
		}
		//获取活动钻信息 一维 goods_sn=>shop_price
		$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        $certAllowList=$this->certAllow();
		//生成标准文件
		$csvfie=$csvdir."/".$this->conf['source'].".csv";
		$handle=fopen($csvfie,"w");
		//$fiveonezuan=@explode(',',file_get_contents("http://bbzuan.kela.cn/admin/cnt.php?s=kgk"));

		$gid=array();
		//csv头信息
		$diaCsvTitle=array();
		foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
			$diaCsvTitle[$key]=iconv("UTF-8","GBK",$value);
		}
		fputcsv($handle,$diaCsvTitle);
        $diaarrlists=array();
        foreach($diaarrlist as $row){
            $row=(array)$row;
            $diaarrlists[$row['certificateNo']]=$row;
        }
        $tmp=array();
		foreach($diaarrlists as $row){
			//$row=(array)$row; //xml对象转数组
            //$ShapeList[] = $row['Shape'];

            /*if($row['Shape']=='ROUND'){
                if(!$this->diafilter($row["Carat"],$row['Color'],$row['Clarity'],$row['Cut'],$row['Polish'],$row['Symmetry'],$row['Fluorescence'],"GIA",$row['Cert'])){
                    continue;
                    echo $row1['goods_sn']." --- failed \r\n";
                }
            }else{
                if(!$this->diafilter_yixing($row["Carat"],$row['Color'],$row['Clarity'],$row['Polish'],$row['Symmetry'],$row['Fluorescence'],"GIA",$row['Cert'])){
                    echo $row1['goods_sn']." --- failed \r\n";
                    continue;
                }
            }*/
            if($row["milky"]&&$row["browness"]){ //无奶无咖
                //continue;
            }

			$row1=array();
			$row1["goods_sn"]=$row["serial"]; //$row["stone"],		//货号
			$row1["warehouse"]="COM"; //库房  SZCOM
			$row1["source"]=$this->conf['source']; //货品来源
			$row1["shape"]=$this->diaShapeVal[$row["shape"]]; //"ROUNDS";				//形状
			$row1["carat"]=$row["carat"]; //石重:Weight
			$row1["color"]=$row["color"]; //颜色:colorName
			$row1["clarity"]=$row["clarity"]; //净度:clarityName
			$row1["cut"]=$row["cut"]; //切工:cutName
			$row1["sym"]=$row["symmetry"]; //对称:symName
			$row1["po"]=$row["polish"]; //抛光:polName
			$row1["fl"]=$row["fluorescence"]; //荧光:fluoName
			$row1["diam"]=$row["measurement"]; //尺寸:LxWxD
			$row1["depth"]=$row["depthProportion"]; //全深比:Depth1
			$row1["table"]=$row["tableProportion"]; //台宽比:Table1
			$row1["cert"]=$row["certificate"]; //证书:labName
			$row1["cert_id"]=$row["certificateNo"]; //证书号:certNo
			$row1["good_type"]=1; //证书号:certNo
            $row1["cts"]=(1-abs(floatval($row["descountRangeOff"]))/100)*$row["originalPrice"]; // 每克拉价
            $row1["guojibaojia"]=$row["originalPrice"]; // 国际报价
            $row1["us_price_source"]=$row1["guojibaojia"]*(1-abs(floatval($row["descountRangeOff"]))/100)*$row1["carat"]; // 美元价 
			$row1["shop_price"]=$row1["us_price_source"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
			$row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE	//会员价计算出
			$row1["chengbenjia"]=$row1["us_price_source"]*DOLLARTORMB*JIAJIALV;
			$row1["xianggangjia"]='';
			$row1["gemx_zhengshu"]='';
			$row1["source_discount"]=floatval($row["descountRangeOff"]);
            $row1["mo_sn"]=''; //模号

            //$row1["source_discount"]=$row1["source_discount"]?$row1["source_discount"][0]:0.0000;

			//修改活动价
			if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
				//$row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
				//$row1['is_active']=1;
			}
            if($certAllowList){
                if(!in_array($row1["cert"],$certAllowList)){
                    //continue;
                }
            }
			echo $row1['goods_sn']."\r\n";
			if($this->download==0){
				try{ //待更新到diamond表的goods_id
					$getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
				}catch(Exception $e){
					$this->errormsg=$e->getMessage();
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}
			}

			//编号	库房	形状	重量	颜色	净度	切工	对称	抛光	荧光	尺寸	全深比	台宽比	证书	证书号	售价	会员价
			unset($row1['source']);
			fputcsv($handle,$row1);
		}
        if($tmp){
        	$this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
            	//修改净度为VS1的期货钻成本价
            	// foreach($val as $k1=>$v1){
            	// 	if($v1['clarity'] =='VS1' && $v1['good_type']==2){
            	// 		$val[$k1]['chengben_jia'] = round($v1['chengben_jia']*TIAOJIALV,2);
            	// 		$val[$k1]['shop_price'] = round($v1['shop_price']*TIAOJIALV);
            	// 	}
            	// }
                $this->adddiaDiamond($val,$this->conf['source']); 
            }
                          
            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边 
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this,"",$warehouse);
        }
	}
	/**
	 * KG  by 2016-02-25
	 * @return boolean
	 */
    function m_kg(){
        try{
            
            $this->writeLog($this->conf['server_url']);
            
            //$client = new SoapClient($this->conf['server_url'],array('soap_version' => SOAP_1_2,'cache_wsdl' => 0,'trace' => TRUE,'exceptions' => true));
            //$d = $client->__soapCall("LiveStock",array($this->conf['username'],$this->conf['password']));
            $url = "http://svc-api.kgirdharlal.com:8081/XML/{$this->conf['username']}_file.xml";
            //$url = ROOT_PATH."kg.xml";
            $diaxmllist = file_get_contents($url); 
            if(strlen($diaxmllist)>100){
                file_put_contents(ROOT_PATH.$this->conf['source'].".xml",$diaxmllist);
            }
            $diaarrlist = simplexml_load_string($diaxmllist);
        }catch(SoapFault $e){
            $this->errormsg = $e->getMessage();
            $this->writeLog($this->errormsg);
            $this->runerror(__FILE__,__LINE__); //写进错误LOG
            return false;
        }
        
        //初始化表
        global $db_dia;
        create_data($this->conf['source']);
        
        //信息对象数组化
        $csvdir=ROOT_PATH."data/".$this->conf['source'];
        if(is_dir($csvdir)){
            //清空目录
            cleardirfile($csvdir);
        }else{
            mkdir($csvdir);
        }
        
        //获取活动钻信息 一维 goods_sn=>shop_price
        $activeOpenResult=$this->activeOpen();
        
        //生成标准文件
        $csvfie=$csvdir."/".$this->conf['source'].".csv";
        $handle=fopen($csvfie,"w");
        
        $gid=array();
        //csv头信息
         $diaCsvTitle=array();        
        $GLOBALS['diaCsvTitle'] = array("编号", "库房", "形状", "重量", "颜色", "净度", "切工", "对称", "抛光", "荧光", "尺寸", "全深比", "台宽比", "证书", "证书号", "售价", "会员价","采购成本","每克拉价","国际报价","美元价","香港价","星耀证书号","货源折扣","款号");
        foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
            $diaCsvTitle[$key]=iconv("UTF-8","CP936",$value);
        }
        fputcsv($handle,$diaCsvTitle);
        $diaarrlists=array();

        foreach($diaarrlist->ThirdPartyData as $key=>$row)
        {
            $row = (array) $row;
            $diaarrlists[] = $row;
        }
        unset($diaarrlist);
        //print_r($diaarrlists);exit;
        $tmp=array();
        $cert_id=array();
        foreach($diaarrlists as $row)
        {
            // KB有过滤条件:
            // tinge=空白 miky=空白 cert NO=不抓空白
            if($this->diaCommonFilter($row['CARATS'], $row['COLOR'], $row['CLARITY'], $row['LAB'], $row['CERTINO'], true) == 0)
            {
                continue;
            }
            /* if ( !( trim($row["SHADE"])=="" || strtolower($row["SHADE"])=="none") ) {
                continue;
            }  */
        
            if ( !( trim($row["MILKY"])=="" || strtolower($row["MILKY"])=="none") ) {
                continue;
            }
            
            $row1=array();
            $row1["goods_sn"] = "KG". $row["CERTINO"];
            if($fiveonezuan&&!empty($fiveonezuan)&&!empty($fiveonezuan[0])){
                if(!in_array($row1['goods_sn'],$fiveonezuan)){
                    continue;
                }
            }
            
            $row1["warehouse"]       = "COM"; //库房
            $row1["source"]          = $this->conf['source']; //货品来源
            $row1["shape"]           = $row["SHAPE"]; //形状
            $row1["carat"]           = $row["CARATS"]; //石重
            $row1["color"]           = $row["COLOR"]; //颜色
            $row1["clarity"]         = $row["CLARITY"]; //净度
            $row1["cut"]             = $row["CUT"]; //切工
            $row1["sym"]             = $row["SYMMETRY"]; //对称
            $row1["po"]              = $row["POLISH"]; //抛光
            $row1["fl"]              = updat_fluor($row['FLOUR']); //荧光
            $row1["diam"]            = $row['Measurement']; //尺寸
            $row1["depth"]           = $row["depth"]; //台深比
            $row1["table"]           = $row["TABLE_x0025_"]; //台宽比
            $row1["cert"]            = $row["LAB"]; //证书
            $row1["cert_id"]         = $row["CERTINO"]; //证书号
        
            $PricePerCarat    = $row["SALES_PRICE"] ;// 每克拉价
            
            $row1["shop_price"]      = $PricePerCarat*$row["CARATS"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
            $row1["member_price"]    = ""; //$row["Amount"] * * MEMBER_RATE	//会员价计算出
            $row1["chengbenjia"]     = $PricePerCarat*$row["CARATS"]*DOLLARTORMB*JIAJIALV;
            $row1["cts"]             = $PricePerCarat; // 每克拉价
            $row1["guojibaojia"]     = $PricePerCarat*(1+$row["BACK"])/100; // 国际报价(国际每克拉报价)
            $row1["us_price_source"] = $row["AMOUNT"]; // 美元价
            $row1["xianggangjia"]    = '';
            $row1["gemx_zhengshu"]   = '';
            $row1["source_discount"] = $row["BACK"];
            $row1["mo_sn"]           = ''; //模号
            echo $row1['goods_sn']."\r\n";
            	
            //修改活动价
            if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
                $row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
                $row1['is_active']=1;
            }
        
            if($this->download==0){
                try{ //待更新到diamond表的goods_id
                    $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
                }catch(Exception $e){
                    $this->errormsg=$e->getMessage();
                    $this->runerror(__FILE__,__LINE__); //写进错误LOG
                    return false;
                }
            }
            //编号	库房	形状	重量	颜色	净度	切工	对称	抛光	荧光	尺寸	全深比	台宽比	证书	证书号	售价	会员价
            unset($row1['source']);
            fputcsv($handle,$row1);
        }
        if($tmp){
            $this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
                $this->adddiaDiamond($val,$this->conf['source']);
            }
        
            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
    }
    /**
     * bluestar  by 2016-02-25
     * @return boolean
     */
    function m_bluestar(){
        
        try{
        
            $url = $this->conf['server_url'];            
            $login_url  = $url."/ClientLogin";//登录api
            $this->writeLog($login_url);
            $login_data = array(
                'Email'=>$this->conf['username'],
                'Password' =>$this->conf['password'],
            );            
            $login_data = http_build_query($login_data);         
            $login_xml = $this->curl_post($login_url,$login_data);
            $login_xml = simplexml_load_string($login_xml);
            if(!is_object($login_xml) || empty($login_xml->ROW->Token)){
                $this->errormsg = "登录验证失败";
                $this->writeLog($this->errormsg);
                $this->runerror(__FILE__,__LINE__); //写进错误LOG
                return false;
            }
            
            $search_url = $url."/SearchStone";//查询裸钻api
            $this->writeLog($search_url);
            $token    = (String)$login_xml->ROW->Token;
            $location = "HK";
            $search_data = array(
                'UserId' => $this->conf['username'],
                'Token'  => $token,
                'Location'=> $location,
                'SliceNo' => "1"
            );
            $search_data = http_build_query($search_data);
            $diaxmllist = $this->curl_post($search_url,$search_data);
            if(strlen($diaxmllist)>100){
                file_put_contents(ROOT_PATH.$this->conf['source'].".xml",$diaxmllist);
            }
            $diaarrlist = simplexml_load_string($diaxmllist);
            if(empty($diaarrlist->ROW)){
                $this->errormsg = "抓取钻石信息为空";
                $this->writeLog($this->errormsg);
                $this->runerror(__FILE__,__LINE__); //写进错误LOG
                return false;
            }
        }catch(SoapFault $e){
            $this->errormsg = $e->getMessage();
            $this->writeLog($this->errormsg);
            $this->runerror(__FILE__,__LINE__); //写进错误LOG
            return false;
        }
    
        //初始化表
        global $db_dia;
        create_data($this->conf['source']);
    
        //信息对象数组化
        $csvdir=ROOT_PATH."data/".$this->conf['source'];
        if(is_dir($csvdir)){
            //清空目录
            cleardirfile($csvdir);
        }else{
            mkdir($csvdir);
        }
    
        //获取活动钻信息 一维 goods_sn=>shop_price
        $activeOpenResult=$this->activeOpen();
    
        //生成标准文件
        $csvfie=$csvdir."/".$this->conf['source'].".csv";
        $handle=fopen($csvfie,"w");
    
        $gid=array();
        //csv头信息
        $diaCsvTitle=array();
        $GLOBALS['diaCsvTitle'] = array("编号", "库房", "形状", "重量", "颜色", "净度", "切工", "对称", "抛光", "荧光", "尺寸", "全深比", "台宽比", "证书", "证书号", "售价", "会员价","采购成本","每克拉价","国际报价","美元价","香港价","星耀证书号","货源折扣","款号");
        foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
            $diaCsvTitle[$key]=iconv("UTF-8","CP936",$value);
        }
        fputcsv($handle,$diaCsvTitle);
        $diaarrlists=array();
    
        foreach($diaarrlist->ROW as $key=>$row)
        {
            $row = (array) $row;
            $diaarrlists[] = $row;
        }
        unset($diaarrlist);
        //print_r($diaarrlists);exit;
        $tmp=array();
        $cert_id=array();
        foreach($diaarrlists as $row)
        {
            // KB有过滤条件:
            // tinge=空白 miky=空白 cert NO=不抓空白
            if($this->diaCommonFilter($row['CARAT'], $row['COLOR_CODE'], $row['CLARITY_CODE'], $row['LAB'], $row['REPORTNO'], true) == 0)
            {
                continue;
            } 
            if(!in_array($row['TINGE'],array('NONE','')))
            {
                continue;
            } 
            if(!in_array($row['LUSTER'],array('EX','NL','GL')))
            {
                continue;
            }  
            $row1=array();
            $row1["goods_sn"] = "BS". $row["REPORTNO"];
            $row1["warehouse"]       = "COM"; //库房
            $row1["source"]          = $this->conf['source']; //货品来源
            $row1["shape"]           = $row["SHAPE_CODE"]; //形状
            $row1["carat"]           = $row["CARAT"]; //石重
            $row1["color"]           = $row["COLOR_CODE"]; //颜色
            $row1["clarity"]         = $row["CLARITY_CODE"]; //净度
            $row1["cut"]             = $row["CUT_CODE"]; //切工
            $row1["sym"]             = $row["SYMMETRY_CODE"]; //对称
            $row1["po"]              = $row["POLISH_CODE"]; //抛光
            $row1["fl"]              = updat_fluor($row['FLUORESCENCE_CODE']); //荧光
            $row1["diam"]            = $row['Measurement']; //尺寸
            $row1["depth"]           = $row["HEIGHT"]; //台深比
            $row1["table"]           = $row["WIDTH"]; //台宽比
            $row1["cert"]            = $row["LAB"]; //证书
            $row1["cert_id"]         = $row["REPORTNO"]; //证书号
    
            $PricePerCarat    = $row["PRICE_PER_CRT"] ;// 每克拉价
    
            $row1["shop_price"]      = $PricePerCarat*$row["CARAT"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
            $row1["member_price"]    = ""; //$row["Amount"] * * MEMBER_RATE	//会员价计算出
            $row1["chengbenjia"]     = $PricePerCarat*$row["CARAT"]*DOLLARTORMB*JIAJIALV;
            $row1["cts"]             = $PricePerCarat; // 每克拉价
            $row1["guojibaojia"]     = $row["RAP_PRICE"]; // 国际报价
            $row1["us_price_source"] = $row["AMOUNT"]; // 美元价
            $row1["xianggangjia"]    = '';
            $row1["gemx_zhengshu"]   = '';
            $row1["source_discount"] = $row['DISCOUNT'];
            $row1["mo_sn"]           = ''; //模号
            echo $row1['goods_sn']."\r\n";
             
            //修改活动价
            if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
                $row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
                $row1['is_active']=1;
            }
    
            if($this->download==0){
                try{ //待更新到diamond表的goods_id
                    $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
                }catch(Exception $e){
                    $this->errormsg=$e->getMessage();
                    $this->runerror(__FILE__,__LINE__); //写进错误LOG
                    return false;
                }
            }
            //编号	库房	形状	重量	颜色	净度	切工	对称	抛光	荧光	尺寸	全深比	台宽比	证书	证书号	售价	会员价
            unset($row1['source']);
            fputcsv($handle,$row1);
        }
        if($tmp){
            $this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
                $this->adddiaDiamond($val,$this->conf['source']);
            }
    
            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
    }
	/**
	 * 长宁 
	 */
	function m_changning()
	{
        $online=1;
        if($online){
			try{
                $client = new SoapClient($this->conf['server_url']);
                $this->writeLog($this->conf['server_url']);
                $rel=$client->GetAllProducts();
                file_put_contents(ROOT_PATH."changning.xml",$rel->GetAllProductsResult);
                $diaxmllist=file_get_contents(ROOT_PATH."changning.xml");
			}catch(SoapFault $e){
				$this->errormsg = $e->getMessage();
				$this->writeLog($this->errormsg);
				$this->runerror(__FILE__,__LINE__); //写进错误LOG
				return false;
			}
            
         
			//初始化表
			if($diaxmllist!=''){
				global $db_dia;
				create_data($this->conf['source']);
			}
		//信息对象数组化
        }else{
            //$diaxmllist=file_get_contents("http://bbzuan.kela.cn/jxc/kgk.data.xml");
            $diaxmllist=file_get_contents(ROOT_PATH."changning.xml");
            if($diaxmllist!=''){
                global $db_dia;
                create_data($this->conf['source']);
            }
        }
        $diaarrlist = simplexml_load_string($diaxmllist);
        
		$csvdir=ROOT_PATH."data/".$this->conf['source'];
		if(is_dir($csvdir)){
			//清空目录
            cleardirfile($csvdir);
		}else{
			mkdir($csvdir);
		}
		//获取活动钻信息 一维 goods_sn=>shop_price
		$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        $certAllowList=$this->certAllow();
		//生成标准文件
		$csvfie=$csvdir."/".$this->conf['source'].".csv";
		$handle=fopen($csvfie,"w");
		//$fiveonezuan=@explode(',',file_get_contents("http://bbzuan.kela.cn/admin/cnt.php?s=kgk"));

		$gid=array();
		//csv头信息
		$diaCsvTitle=array();
		foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
			$diaCsvTitle[$key]=iconv("UTF-8","GBK",$value);
		}
		fputcsv($handle,$diaCsvTitle);
        $diaarrlist=(array)$diaarrlist->Product;
        $diaarrlists=array();
        foreach($diaarrlist as $key=>$val){
          foreach($val as $k=>$row){
            $row=(array)$row;
            if(empty($row)){
                continue;
            }

            if($this->diaCommonFilter($row['Carat'], $row['Color'], $row['Clarity'], $row['Lab'], $row['CertNo'], true) == 0) 
            {
            		continue;
            }

            if(!in_array($row['Location'],array("深圳","上海","香港","印度","比利时"))){
                //continue;
            }
            if(!in_array($row['Location'],array("香港","印度","比利时"))&&!in_array($row['Clarity'],array("FL","IF","VVS1","VVS2","SI1"))){
                continue;
            }
            if(!in_array($row['Location'],array("深圳","上海"))&&!in_array($row['Clarity'],array("FL","IF","VVS1","VVS2","SI1","SI2"))){
                continue; 
            }
            if($row['Milk']!='无奶油'&&$row['Milk']!=''){
                continue;
            }
            if($row['Brown']!='无偏色'&&$row['Brown']!=''){
                continue;
            }
            if($row['Ha']!='不见黑'&&$row['Ha']!=''){
                continue;
            }
            if(!preg_match('/^\d*$/',$row['CertNo'])){
                continue;
            }
            $diaarrlists[$row['CertNo']]=$row;
          }
        }
        $tmp=array();
		foreach($diaarrlists as $row){

			$row1=array();
            if($this->selectExists($row['CertNo'])){
                continue;
            }
            if(in_array($row['Location'],array("深圳","上海"))){
                $row['good_type']=1;
            }else{
                $row['good_type']=2;
            }
			$row1["goods_sn"]=$row['ProductId']; //$row["stone"],		//货号
			$row1["warehouse"]="COM"; //库房  SZCOM
			$row1["source"]=$this->conf['source']; //货品来源 
			//$row1["shape"]=$this->diaShapeVal[$row[27]]; //"ROUNDS";				//形状
			//$row1["shape"]=$row['Shape']=='PEAR'?'CN_LIXING':$row['Shape']; //"ROUNDS";				//形状
			$row1["shape"]=$row['Shape']; //"ROUNDS";				//形状
			$row1["carat"]=$row['Carat']; //石重:Weight
			$row1["color"]=$row['Color']; //颜色:colorName
			$row1["clarity"]=$row['Clarity']; //净度:clarityName
			$row1["cut"]=$row['Cut']; //切工:cutName
			$row1["sym"]=$row['Symmetry']; //对称:symName
			$row1["po"]=$row['Polish']; //抛光:polName
			$row1["fl"]=$row['Fluo']; //荧光:fluoName
			$row1["diam"]=$row['Diameter']; //尺寸:LxWxD
			$row1["depth"]=$row['Depth']; //全深比:Depth1
			$row1["table"]=$row['TablePer']; //台宽比:Table1
			$row1["cert"]=$row['Lab']; //证书:labName
			$row1["cert_id"]=$row['CertNo']; //证书号:certNo
            $row1["guojibaojia"]=$row['RapPrice']; // 国际报价
            $row1["cts"]=$row['Price']; // 每克拉价
            $row1["us_price_source"]=$row['Price']*$row['Carat']; // 美元价 
			$row1["shop_price"]=$row1["us_price_source"]*DOLLARTORMB; //每颗价格:Amount
			$row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE	//会员价计算出
			$row1["chengbenjia"]=$row1["us_price_source"]*DOLLARTORMB;
			$row1["xianggangjia"]='';
			$row1["gemx_zhengshu"]='';
			$row1["source_discount"]=floatval($row["Back"]);// 折扣
            //$row1["cts"]=(1-abs(floatval($row1["source_discount"]))/100)*$row['RapPrice']; // 每克拉价
            $row1["mo_sn"]=''; //模号

			echo $row1['goods_sn']."\r\n";
			if($this->download==0){
				try{ //待更新到diamond表的goods_id
					$getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
				}catch(Exception $e){
					$this->errormsg=$e->getMessage();
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}
			}

			//编号	库房	形状	重量	颜色	净度	切工	对称	抛光	荧光	尺寸	全深比	台宽比	证书	证书号	售价	会员价
			unset($row1['source']);
			fputcsv($handle,$row1);
		}
        if($tmp){
        	$this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
            	//修改净度为VS1的期货钻成本价
            	// foreach($val as $k1=>$v1){
            	// 	if($v1['clarity'] =='VS1' && $v1['good_type']==2){
            	// 		$val[$k1]['chengben_jia'] = round($v1['chengben_jia']*TIAOJIALV,2);
            	// 		$val[$k1]['shop_price'] = round($v1['shop_price']*TIAOJIALV);
            	// 	}
            	// }
                $this->adddiaDiamond($val,$this->conf['source']); 
            }
                          
            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边 
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this,"",$warehouse);
        }
	}

	/**
	 * KB 接口
	 */
	function m_kb()
	{	
		$xmlContent = '';
		try {
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,$this->conf['server_url']);
			$this->writeLog($this->conf['server_url']);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_HEADER,0); // 不要http header 加快效率
			curl_setopt($ch,CURLOPT_USERPWD,$this->conf['username'].':'.$this->conf['password']);
			curl_setopt($ch,CURLOPT_TIMEOUT,1000);
			$xmlContent = curl_exec($ch);
			if($xmlContent === false)
			{
			    $this->writeLog('Curl error: ' . curl_error($ch));
			}
			curl_close($ch);
            file_put_contents(ROOT_PATH."kb.xml", $xmlContent);

		} catch(Exception $e) {
			$this->errormsg=$e->getMessage();
			$this->writeLog($e->getMessage());
			$this->runerror(__FILE__,__LINE__); //写进错误LOG
			return false;
		}
		
		$diaarrlist = @simplexml_load_string($xmlContent);
		if (count($diaarrlist)==0) {
			$this->writeLog("没有查到数据");
			exit();
		}

		// var_dump($diaarrlist);
		
		//初始化表
		global $db_dia;
		create_data($this->conf['source']);
			

		//信息对象数组化
		$csvdir=ROOT_PATH."data/".$this->conf['source'];
		if(is_dir($csvdir)){
			//清空目录
			cleardirfile($csvdir);
		}else{
			mkdir($csvdir);
		}

		//获取活动钻信息 一维 goods_sn=>shop_price
		$activeOpenResult=$this->activeOpen();
 
		//生成标准文件
		$csvfie=$csvdir."/".$this->conf['source'].".csv";
		$handle=fopen($csvfie,"w");

		$gid=array();
		//csv头信息
		$diaCsvTitle=array();
		foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
			$diaCsvTitle[$key]=iconv("UTF-8","CP936",$value);
		}
		fputcsv($handle,$diaCsvTitle);
		
        $diaarrlists=array();
        foreach($diaarrlist->StoneDetails as $key=>$row)
        {
            $row = (array) $row;
            $diaarrlists[$row['Cert_x0020_No.']] = $row;
        }
        $tmp=array();
        $cert_id=array();
		foreach($diaarrlists as $row)
		{
			// KB有过滤条件:
			// tinge=空白 miky=空白 cert NO=不抓空白
			if($this->diaCommonFilter($row['Carat'], $row['Color'], $row['Clarity'], $row['Lab'], $row['Cert_x0020_No.'], true) == 0) 
	        {
            	continue;
	        }

	        if ( !( trim($row["Tinge"])=="" || strtolower($row["Tinge"])=="none") ) {
	        	continue;
	        }

	        if ( !( trim($row["Milky"])=="" || strtolower($row["Milky"])=="none") ) {
	        	continue;
	        }

			$row1=array();
			$row1["goods_sn"] = "KB". $row["Cert_x0020_No."];
			if($fiveonezuan&&!empty($fiveonezuan)&&!empty($fiveonezuan[0])){
				if(!in_array($row1['goods_sn'],$fiveonezuan)){
					continue;
				}
			}
			$row1["warehouse"]       = "COM"; //库房
			$row1["source"]          = $this->conf['source']; //货品来源 
			$row1["shape"]           = $row["Shape"]; //形状
			$row1["carat"]           = $row["Carat"]; //石重
			$row1["color"]           = $row["Color"]; //颜色
			$row1["clarity"]         = $row["Clarity"]; //净度
			$row1["cut"]             = $row["Cut"]; //切工
			$row1["sym"]             = $row["Sym"]; //对称
			$row1["po"]              = $row["Pol"]; //抛光
			$row1["fl"]              = updat_fluor($row['FL']); //荧光
			$row1["diam"]            = $row['Measurement']; //尺寸
			$row1["depth"]           = $row["Depth_x0020__x0025_"]; //台深比
			$row1["table"]           = $row["Table_x0020__x0025_"]; //台宽比
			$row1["cert"]            = $row["Lab"]; //证书
			$row1["cert_id"]         = $row["Cert_x0020_No."]; //证书号

			$row["PricePerCarat"]    = $row["Rap_x0020_Price"] * (1+$row["Disc_x0020__x0025_"]*0.01);
			$row1["shop_price"]      = $row["PricePerCarat"]*$row["Carat"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
			$row1["member_price"]    = ""; //$row["Amount"] * * MEMBER_RATE	//会员价计算出
			$row1["chengbenjia"]     = $row["PricePerCarat"]*$row["Carat"]*DOLLARTORMB*JIAJIALV;
			$row1["cts"]             = $row["PricePerCarat"]; // 每克拉价
			$row1["guojibaojia"]     = $row["Rap_x0020_Price"]; // 国际报价
			$row1["us_price_source"] = $row["PricePerCarat"]*$row["Carat"]; // 美元价 
			$row1["xianggangjia"]    = '';
			$row1["gemx_zhengshu"]   = '';
			$row1["source_discount"] = $row["Disc_x0020__x0025_"];
			$row1["mo_sn"]           = ''; //模号
			echo $row1['goods_sn']."\r\n";
			
			//修改活动价
			if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
				$row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
				$row1['is_active']=1;
			}
            
			if($this->download==0){
				try{ //待更新到diamond表的goods_id
					$getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
				}catch(Exception $e){
					$this->errormsg=$e->getMessage();
					$this->runerror(__FILE__,__LINE__); //写进错误LOG
					return false;
				}
			}

			//编号	库房	形状	重量	颜色	净度	切工	对称	抛光	荧光	尺寸	全深比	台宽比	证书	证书号	售价	会员价
			unset($row1['source']);
			fputcsv($handle,$row1);
		}
        if($tmp){
        	$this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
            	//修改净度为VS1的期货钻成本价
            	// foreach($val as $k1=>$v1){
            	// 	if($v1['clarity'] =='VS1' && $v1['good_type']==2){
            	// 		$val[$k1]['chengben_jia'] = round($v1['chengben_jia']*TIAOJIALV,2);
            	// 		$val[$k1]['shop_price'] = round($v1['shop_price']*TIAOJIALV);
            	// 	}
            	// }
                $this->adddiaDiamond($val,$this->conf['source']);   
            }            

            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
	}

    /**
     * FL 接口
     */
    function m_fulong (){

        try{
            $url = $this->conf['server_url'];   
            $login_url = $url."?action=viplogin&";//登录api
            $this->writeLog($login_url);
            $login_data = array(
                'vipid'  => $this->conf['username'],
                'vippsd' => $this->conf['password'],
            );
            $login_data = http_build_query($login_data);
            $login_json = $this->curl_post($login_url,$login_data);
            $login_obj = json_decode($login_json);
            if(!is_object($login_obj) || !isset($login_obj->msgdata->token) || empty($login_obj->msgdata->token)){
                $this->errormsg = "登录验证失败";
                $this->writeLog($this->errormsg);
                $this->runerror(__FILE__,__LINE__); //写进错误LOG
                return false;
            }
            
            $feature_url = $url."?action=vipsuplist&";//获取特征值api
            $this->writeLog($feature_url);
            $token = (String)$login_obj->msgdata->token;
            $feature_data = array(
                'token' => $token
            );
            $feature_data = http_build_query($feature_data);
            $featurejsonList = $this->curl_post($feature_url,$feature_data);
            $featureobj = json_decode($featurejsonList);
            if(!is_object($featureobj) || !isset($featureobj->msgdata) || empty($login_obj->msgdata)){
                $this->errormsg = "获取特征值失败";
                $this->writeLog($this->errormsg);
                $this->runerror(__FILE__,__LINE__); //写进错误LOG
                return false;
            }

            $dateList = array();
            foreach ($featureobj->msgdata as $feature_val) {
                $sup = $feature_val;
                $query_url = $url."?action=vipquerystone&";//抓取裸钻api
                $this->writeLog($query_url);
                $query_data = array(
                    'token' => $token,
                    'sup'   => $sup
                );
                $query_data = http_build_query($query_data);
                $queryjsonData = $this->curl_post($query_url,$query_data);
                $queryobjData  = json_decode($queryjsonData);
                $queryArrData  = objectToArray($queryobjData);
                $dateList[] = $queryArrData;
                //break;
            }
            /*$data_arr_list = array();
            if(!empty($dateList)){
                foreach ($dateList as $key => $value) {
                    foreach ($value['msgdata']['tab'] as $k => $v) {
                        $data_arr_list[] = $v;
                    }
                }
            }
            $queryinfo_xml = '';
            $queryinfo_xml = $this->arrtoxml($data_arr_list);
            if(strlen($queryinfo_xml)>100){
                file_put_contents(ROOT_PATH.$this->conf['source'].".xml",$queryinfo_xml);
            }*/
            //$queryinfolist = simplexml_load_string($queryinfo_xml);
            if(empty($dateList)){
                $this->errormsg = "抓取钻石信息为空";
                $this->writeLog($this->errormsg);
                $this->runerror(__FILE__,__LINE__); //写进错误LOG
                return false;
            }
        }catch(SoapFault $e){
            $this->errormsg = $e->getMessage();
            $this->writeLog($this->errormsg);
            $this->runerror(__FILE__,__LINE__); //写进错误LOG
            return false;
        }
    
        //初始化表
        global $db_dia;
        create_data($this->conf['source']);
    
        //信息对象数组化
        $csvdir=ROOT_PATH."data/".$this->conf['source'];
        if(is_dir($csvdir)){
            //清空目录
            cleardirfile($csvdir);
        }else{
            mkdir($csvdir);
        }
    
        //获取活动钻信息 一维 goods_sn=>shop_price
        $activeOpenResult=$this->activeOpen();
    
        //生成标准文件
        $csvfie=$csvdir."/".$this->conf['source'].".csv";
        $handle=fopen($csvfie,"w");
    
        $gid=array();
        //csv头信息
        $diaCsvTitle=array();
        $GLOBALS['diaCsvTitle'] = array("编号", "库房", "形状", "重量", "颜色", "净度", "切工", "对称", "抛光", "荧光", "尺寸", "全深比", "台宽比", "证书", "证书号", "售价", "会员价","采购成本","每克拉价","国际报价","美元价","香港价","星耀证书号","货源折扣","款号");
        foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
            $diaCsvTitle[$key]=iconv("UTF-8","CP936",$value);
        }
        fputcsv($handle,$diaCsvTitle);
        $diaarrlists=array();
        foreach($queryobjData->msgdata->tab as $key=>$row)
        {
            $row = (array) $row;
            $diaarrlists[] = $row;
        }
        unset($queryobjData);
        $tmp=array();
        $cert_id=array();
        foreach ($dateList as $key => $diaarrlists) 
        {
            if(!isset($diaarrlists['msgdata']['tab']) 
                && empty($diaarrlists['msgdata']['tab'])) continue;

            foreach($diaarrlists['msgdata']['tab'] as $row)
            {

                //证书类型：GIA
                //颜色：D E F G H I J K
                //净度：FL IF VVS1 VVS2 SI1 VS1 VS2
                //石重：≥0.3
                //colsh  :只抓取“无咖”
                //milky  :只抓取“无奶”
                //green  :只抓取“无绿”
                if($row["report"] != 'GIA')
                {
                    continue;
                }
                if(!in_array($row["color"], array('D', 'E', 'F', 'G', 'H', 'I', 'J', 'K')))
                {
                    continue;
                }
                if(!in_array($row["clarity"], array('FL', 'IF', 'VVS1', 'VVS2', 'SI1', 'VS1', 'VS2')))
                {
                    continue;
                }
                if($row["carat"] < 0.3)
                {
                    continue;
                }
                if($row["colsh"] != '无咖')
                {
                    continue;
                }
                if($row["green"] != '无绿')
                {
                    continue;
                }
                if($row["milky"] != '无奶')
                {
                    continue;
                }
                $row1=array();
                $row1["goods_sn"] = "FL". $row["stoneid"];
                $row1["warehouse"]       = 'COM'; //库房
                $row1["source"]          = $this->conf['source']; //货品来源
                $row1["shape"]           = $this->diaShapeVal[$row["shape"]]; //形状
                $row1["carat"]           = $row["carat"]; //石重
                $row1["color"]           = $row["color"]; //颜色
                $row1["clarity"]         = $row["clarity"]; //净度
                $row1["cut"]             = isset($this->symmetry_arr[$row["cut"]])?$this->symmetry_arr[$row["cut"]]:$row["cut"]; //切工
                $row1["sym"]             = isset($this->symmetry_arr[$row["symmetry"]])?$this->symmetry_arr[$row["symmetry"]]:$row["symmetry"]; //对称
                $row1["po"]              = isset($this->symmetry_arr[$row["polish"]])?$this->symmetry_arr[$row["polish"]]:$row["polish"]; //抛光
                $row1["fl"]              = $row['fluorescence']; //荧光
                $row1["diam"]            = $row['measurement']; //尺寸
                $row1["depth"]           = ''; //台深比
                $row1["table"]           = ''; //台宽比
                $row1["cert"]            = $row["report"]; //证书
                $row1["cert_id"]         = $row["reportno"]; //证书号
            
                //美元每克拉价=（100+退点）*0.01*国际报价
                $PricePerCarat    = (100+$row['saleback'])*0.01*$row["onlineprice"];// 每克拉价
                $row1["shop_price"]      = $PricePerCarat*$row["carat"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
                $row1["member_price"]    = ""; //$row["Amount"] * * MEMBER_RATE //会员价计算出
                $row1["chengbenjia"]     = $PricePerCarat*$row["carat"]*DOLLARTORMB*JIAJIALV;
                $row1["cts"]             = $PricePerCarat; // 每克拉价
                $row1["guojibaojia"]     = $row["onlineprice"]; // 国际报价
                $row1["us_price_source"] = $PricePerCarat*$row["carat"]; // 美元价
                $row1["xianggangjia"]    = '';
                $row1["gemx_zhengshu"]   = '';
                $row1["source_discount"] = $row['saleback'] != ''?abs($row['saleback']):'';
                $row1["mo_sn"]           = ''; //模号
                $row1["img"]             = $row['imgurl']; //图片链接
                //echo "<pre>";
                //print_r($row);
                //echo $row1['goods_sn']."\r\n";
                 
                //修改活动价
                /*if($activeOpenResult&&array_key_exists($row1['goods_sn'],$activeOpenResult)){
                    $row1['active_shop_price']=$activeOpenResult[$row1['goods_sn']];
                    $row1['is_active']=1;
                }*/
                if($this->download==0){
                    try{ //待更新到diamond表的goods_id
                        $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                        if($getAddDiamond){
                            $tmp[]=$getAddDiamond;
                        }
                    }catch(Exception $e){
                        $this->errormsg=$e->getMessage();
                        $this->runerror(__FILE__,__LINE__); //写进错误LOG
                        return false;
                    }
                }
                //编号    库房  形状  重量  颜色  净度  切工  对称  抛光  荧光  尺寸  全深比 台宽比 证书  证书号 售价  会员价
                unset($row1['source']);
                fputcsv($handle,$row1);
            }
        }
        if($tmp){
            $this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,5000);
            foreach($strr as $val){
                $this->adddiaDiamond($val,$this->conf['source']);
            }
    
            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
    }

    /**
     *
     * KBGems接口 no23
     */
    function m_kbgems()
    {
        $online=1;
        $fiveone_way=1;

        ini_set('default_socket_timeout', 150);

        while(1){
            if($online){
                try
                {
                    $curl = curl_init(); 
                    $url = 'ftp://96.125.172.174:32/inventory.csv';//完整路径
                    $this->writeLog($url);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_VERBOSE, 1);
                    curl_setopt($curl, CURLOPT_FTP_USE_EPSV, 0);
                    curl_setopt($curl, CURLOPT_TIMEOUT, 300); // times out after 300s
                    curl_setopt($curl, CURLOPT_USERPWD, "cust1:c@530210");//FTP用户名：密码
                    $info = curl_exec($curl);

                    if($info === false)
                    {
                        $this->writeLog('Curl error: ' . curl_error($curl));
                    }
                    //file_put_contents(ROOT_PATH."kbgems.xml",$info);
                    $arr = explode("\n",$info);
                    $k_val = explode(",",$arr[0]);
                    array_shift($arr);
                    $diaxmllist=array();
                    $v_val=array();
                    foreach($arr as $k=>$v){
                        $v=explode(",",$v);
                        array_pop($v);
                        foreach($v as $key=>$val){
                            $key=trim($k_val[$key]);
                            $v_val[$key]=$val;
                        }
                        $diaxmllist[]=$v_val;
                    }
                }
                catch (Exception $sf)
                {
                    $this->errormsg=$sf->getMessage();
                    $this->writeLog($sf->getMessage());
                    $this->runerror(__FILE__,__LINE__); //写进错误LOG
                    return false;
                }
            }else{
            }
            //初始化表
            if($diaxmllist!=''){
                global $db_dia;
                create_data($this->conf['source']);
                break;
            }else{
                $this->writeLog('no file');
                die();
                return false;
            }
        }
        $parcelStatus=array();
        $parcelException=array();
        //信息对象数组化
        //file_put_contents('diarough'.date('Ymd'),$diaxmllist);
        //$diaarrlist=simplexml_load_string($diaxmllist);
        $diaarrlist=$diaxmllist;

        if(is_null($diaarrlist)){
            $this->writeLog('no file');
            die();
        }
        $csvdir=ROOT_PATH."data/".$this->conf['source'];
        if(is_dir($csvdir)==''){
            //清空目录
            mkdir($csvdir);
        }else{
            cleardirfile($csvdir);
        }

        //获取活动钻信息 一维 goods_sn=>shop_price
        //$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        //$certAllowList=$this->certAllow();
        //生成标准文件
        $csvfie=$csvdir."/".$this->conf['source'].".csv";
        $handle=fopen($csvfie,"w");

        $gid=array();
        //csv头信息
        $diaCsvTitle=array();
        foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
            $diaCsvTitle[$key]=iconv("UTF-8","GBK",$value);
        }
        fputcsv($handle,$diaCsvTitle);
        /*$diaarrlists=array();
        foreach($diaarrlist as $row){
            $diaarrlists[$row['CERTIFICATE_NO']]=$row;
        }*/
        $tmp=array();
        foreach($diaarrlist as $key => $row){
            $warehouse='COM';
            $row1=array();
            //$row1["goods_id"]=$i; //$row["stone"],        //货号
            $row1["goods_sn"]=$row['VendorStockNumber'];//$row["PARCEL_ID"]; //$row["stone"],        //货号
            /*if($fiveonezuan&&!empty($fiveonezuan)&&!empty($fiveonezuan[0])){
                if(!in_array($row1['goods_sn'],$fiveonezuan)){
                    continue;
                }
            }*/
            $R = array_keys($row);
            if($row["Weight"]<0.3){
                continue;
            }
   
            $row1["warehouse"]=$warehouse; //库房  SHCOM
            $row1["source"]=$this->conf['source']; //货品来源
            $row1["shape"]=$row[$R[0]]; //"ROUNDS";              //形状
            $row1["carat"]=$row["Weight"]; //石重:Weight
            $row1["color"]=$row["Color"]; //颜色:colorName
            $row1["clarity"]=$row["Clarity"]; //净度:clarityName
            $row1["cut"]=$row["CutGrade"]; //切工:cutName
            $row1["sym"]=$row["Symmetry"]; //对称:symName
            $row1["po"]=$row["Polish"]; //抛光:polName
            $row1["fl"]=$row["FluorescenceIntensity"]; //荧光:fluoName
            $row1["diam"]=$row["Measurement"]; //尺寸:LxWxD
            $row1["depth"]=$row["Depth"]; //全深比:Depth1
            $row1["table"]=$row["Table"]; //台宽比:Table1
            $row1["cert"]=$row["Lab"]; //证书:labName
            $row1["cert_id"]=$row["Certificate"]; //证书号:certNo
            $row1["shop_price"]=$row["Amount"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
            $row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE  //会员价计算出
            $row1["chengbenjia"]=round($row["Amount"]*DOLLARTORMB*JIAJIALV,2);
            $row1["cts"]=$row["Price"]; // 每克拉价
            $row1["guojibaojia"]=$row["Rapprice"]; // 国际报价
            $row1["us_price_source"]=$row["Amount"]; // 美元价 
            $row1["xianggangjia"]='';
            $row1["gemx_zhengshu"]='';
            $row1["source_discount"]=abs($row["Disc"]);
            $row1["mo_sn"]=''; //模号
            if($row["Disc"]>0)
            	continue;



            if(!in_array($row1["color"], array('D', 'E', 'F', 'G', 'H', 'I', 'J', 'K')))
                continue;
            if(!in_array($row1["clarity"], array('FL', 'IF', 'VVS1', 'VVS2', 'SI1', 'VS1', 'VS2')))
                continue;
            if($row1["carat"] < 0.3)
                continue;
            if(!in_array($row1["cert"], array('GIA')))
                continue;
            if(!in_array($row1["cut"],array('VG','EX','G'))) //切工:cutName
                continue;
            if( in_array($row1['fl'],array('VST')))
                continue;
            if(empty($row1["shape"]))
            	continue;
                               

           
            echo $row1['goods_sn']."\t";
            if($this->download==0){
                try{ //待更新到diamond表的goods_id
                    $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
                }catch(Exception $e){
                    $this->errormsg=$e->getMessage();
                    $this->runerror(__FILE__,__LINE__); //写进错误LOG
                    return false;
                }
            }
            //编号    库房  形状  重量  颜色  净度  切工  对称  抛光  荧光  尺寸  全深比 台宽比 证书  证书号 售价  会员价
            unset($row1['source']);
            fputcsv($handle,$row1);
        }
        if($tmp){
            $this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,10);
            foreach($strr as $val){
                $this->adddiaDiamond($val,$this->conf['source']); 
            }
                          
            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
    }

    /**
     *
     * sheelgems接口
     */
    function m_sheelgems()
    {
        $online=1;
        $fiveone_way=1;

        ini_set('default_socket_timeout', 150);

        while(1){
            if($online){
                try
                {
                    $curl = curl_init(); 
                    $url = 'ftp://118.140.149.78/KELA.csv';//完整路径
                    $this->writeLog($url);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_VERBOSE, 1);
                    curl_setopt($curl, CURLOPT_FTP_USE_EPSV, 0);
                    curl_setopt($curl, CURLOPT_TIMEOUT, 300); // times out after 300s
                    curl_setopt($curl, CURLOPT_USERPWD, "Kela:kela001");//FTP用户名：密码
                    $info = curl_exec($curl);

                    if($info === false)
                    {
                        $this->writeLog('Curl error: ' . curl_error($curl));
                    }
                    file_put_contents(ROOT_PATH."sheelgems.xml",$info);
                    $arr = explode("\n",$info);
                    $k_val = explode(",",$arr[0]);
                    

                    array_shift($arr);
                    $diaxmllist=array();
                    //$v_val=array();
                    foreach($arr as $k=>$v){
                    	$v_val=array();
                        $v=explode(",",$v);
                        array_pop($v);
                        foreach($v as $key=>$val){
                            $key=trim($k_val[$key]);
                            $v_val[$key]=$val;
                        }
                        $diaxmllist[]=$v_val;
                    }
                }
                catch (Exception $sf)
                {
                    $this->errormsg=$sf->getMessage();
                    $this->writeLog($sf->getMessage());
                    $this->runerror(__FILE__,__LINE__); //写进错误LOG
                    return false;
                }
            }else{
            }
            //初始化表
            if($diaxmllist!=''){
                global $db_dia;
                create_data($this->conf['source']);
                break;
            }else{
                $this->writeLog('no file');
                die();
                return false;
            }
        }
        $parcelStatus=array();
        $parcelException=array();
        //信息对象数组化
        //file_put_contents('diarough'.date('Ymd'),$diaxmllist);
        //$diaarrlist=simplexml_load_string($diaxmllist);
        $diaarrlist=$diaxmllist;

        if(is_null($diaarrlist)){
            $this->writeLog('no file');
            die();
        }
        $csvdir=ROOT_PATH."data/".$this->conf['source'];
        if(is_dir($csvdir)==''){
            //清空目录
            mkdir($csvdir);
        }else{
            cleardirfile($csvdir);
        }

        //获取活动钻信息 一维 goods_sn=>shop_price
        //$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        //$certAllowList=$this->certAllow();
        //生成标准文件
        $csvfie=$csvdir."/".$this->conf['source'].".csv";
        $handle=fopen($csvfie,"w");

        $gid=array();
        //csv头信息
        $diaCsvTitle=array();
        foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
            $diaCsvTitle[$key]=iconv("UTF-8","GBK",$value);
        }
        fputcsv($handle,$diaCsvTitle);
        /*$diaarrlists=array();
        foreach($diaarrlist as $row){
            $diaarrlists[$row['CERTIFICATE_NO']]=$row;
        }*/
        $tmp=array();
        foreach($diaarrlist as $key => $row){
            $warehouse='COM';
            $row1=array();
            //$row1["goods_id"]=$i; //$row["stone"],        //货号
            $row1["goods_sn"]=$row['Stock'];//$row["PARCEL_ID"]; //$row["stone"],    //货号
            $row1["warehouse"]=$warehouse; //库房  SHCOM
            $row1["source"]=$this->conf['source']; //货品来源
            $row1["shape"]=$row["Shape"]; //"ROUNDS";              //形状
            $row1["carat"]=$row["Weight"]; //石重:Weight
            $row1["color"]=$row["Color"]; //颜色:colorName
            $row1["clarity"]=$row["Clarity"]; //净度:clarityName
            $row1["cut"]=$row["Cut Grade"]; //切工:cutName
            $row1["sym"]=$row["Symmetry"]; //对称:symName
            $row1["po"]=$row["Polish"]; //抛光:polName
            $row1["fl"]=$row["Fluorescence Intensity"]; //荧光:fluoName
            $row1["diam"]=$row["Measurements"]; //尺寸:LxWxD
            $row1["depth"]=$row["Depth"]; //全深比:Depth1
            $row1["table"]=$row["Table"]; //台宽比:Table1
            $row1["cert"]=$row["Lab"]; //证书:labName
            $row1["cert_id"]=$row["Certificate"]; //证书号:certNo
            $row1["shop_price"]=round($row["Rap Price"]*(100+$row['Discount'])/100*$row['Weight']*DOLLARTORMB*JIAJIALV,2); //每颗价格:Amount
            $row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE  //会员价计算出
            $row1["chengbenjia"]=round($row["Rap Price"]*(100+$row['Discount'])/100*$row['Weight']*DOLLARTORMB*JIAJIALV,2);
            $row1["cts"]=round($row["Rap Price"]*(100+$row['Discount'])/100,2); // 每克拉价
            $row1["guojibaojia"]=$row["Rap Price"]; // 国际报价
            $row1["us_price_source"]=round($row["Rap Price"]*(100+$row['Discount'])/100*$row['Weight'],2); // 美元价 
            $row1["xianggangjia"]='';
            $row1["gemx_zhengshu"]='';
            $row1["source_discount"]=abs($row["Discount"]);
            $row1["mo_sn"]=''; //模号

            if($row["Discount"]>0)
            	continue;
            if(!in_array($row1["shape"],array('RBC','Pear','Heart','Princess','Cushion',' Emerald'))){
                continue;
            }
            if($row1["shape"]=='RBC') 
                $row1["shape"]='ROUNDS';

            if(!in_array($row1["color"], array('D', 'E', 'F', 'G', 'H', 'I', 'J', 'K')))
                continue;
            if(!in_array($row1["clarity"], array('FL', 'IF', 'VVS1', 'VVS2', 'SI1', 'VS1', 'VS2')))
                continue;
            if($row1["carat"] < 0.3)
                continue;
            if(!in_array($row1["cert"], array('GIA')))
                continue;    
            if(!in_array($row1["cut"],array('VG','EX'))) //切工:cutName
                continue;
            if(!in_array($row1["po"], array('VG','EX'))) //抛光:polName
                continue;
            if(!in_array($row1["sym"],array('VG','EX'))) //对称:symName
                continue;  
            if( in_array($row1['fl'],array('FIP','VS')))   
            	continue;
            //$row1['fl']=substr($row1['fl'], 0,1);

            echo $row1['goods_sn']."\t";
            if($this->download==0){
                try{ //待更新到diamond表的goods_id
                    $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
                }catch(Exception $e){
                    $this->errormsg=$e->getMessage();
                    $this->runerror(__FILE__,__LINE__); //写进错误LOG
                    return false;
                }
            }
            //编号    库房  形状  重量  颜色  净度  切工  对称  抛光  荧光  尺寸  全深比 台宽比 证书  证书号 售价  会员价
            unset($row1['source']);
            fputcsv($handle,$row1);
        }
        
        if($tmp){
            $this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,10);
            foreach($strr as $val){
                $this->adddiaDiamond($val,$this->conf['source']); 
            }
                          
            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
    }


    /**
     *
     * cdinesh接口 no25
     */
    function m_cdinesh()
    {
        $online=1;
        $fiveone_way=1;

        ini_set('default_socket_timeout', 150);

        while(1){
            if($online){
                try
                {
                    $url = "http://service.cdinesh.in/fullstockapi.asmx?wsdl";
			        $client = new SoapClient($url);
			        $uid = "fcbbab12-2e87-41c9-a924-2f3678e3c08e";
			        $abc = $client->GetStockJsonSP(array("Uid" => $uid));

			        $response = $abc->GetStockJsonSPResult;
			        file_put_contents(ROOT_PATH."cdinesh.xml",$response);
			        $string = preg_replace('/\s+/', '', $response);
			        $new_res = explode('},', $string);
                     
                    $diaxmllist=array(); 
			        foreach($new_res as $res_n){
				        $one = $res_n.'}';
				        $little = json_decode($one,true);
				        //echo '<pre>';
				        //print_r($little);
				        $diaxmllist[]=$little;
			        }                    
                }
                catch (Exception $sf)
                {
                    $this->errormsg=$sf->getMessage();
                    $this->writeLog($sf->getMessage());
                    $this->runerror(__FILE__,__LINE__); //写进错误LOG
                    return false;
                }
            }else{
            }
            //初始化表
            if($diaxmllist!=''){
                global $db_dia;
                create_data($this->conf['source']);
                break;
            }else{
                $this->writeLog('no file');
                die();
                return false;
            }
        }
        $parcelStatus=array();
        $parcelException=array();
        //信息对象数组化
        //file_put_contents('diarough'.date('Ymd'),$diaxmllist);
        //$diaarrlist=simplexml_load_string($diaxmllist);
        $diaarrlist=$diaxmllist;

        if(is_null($diaarrlist)){
            $this->writeLog('no file');
            die();
        }
        $csvdir=ROOT_PATH."data/".$this->conf['source'];
        if(is_dir($csvdir)==''){
            //清空目录
            mkdir($csvdir);
        }else{
            cleardirfile($csvdir);
        }

        //获取活动钻信息 一维 goods_sn=>shop_price
        //$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        //$certAllowList=$this->certAllow();
        //生成标准文件
        $csvfie=$csvdir."/".$this->conf['source'].".csv";
        $handle=fopen($csvfie,"w");

        $gid=array();
        //csv头信息
        $diaCsvTitle=array();
        foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
            $diaCsvTitle[$key]=iconv("UTF-8","GBK",$value);
        }
        fputcsv($handle,$diaCsvTitle);
        /*$diaarrlists=array();
        foreach($diaarrlist as $row){
            $diaarrlists[$row['CERTIFICATE_NO']]=$row;
        }*/
        $tmp=array();
        foreach($diaarrlist as $key => $row){
            $warehouse='COM';
            $row1=array();
            //$row1["goods_id"]=$i; //$row["stone"],        //货号
            $row1["goods_sn"]=$row['PacketNo'];//$row["PARCEL_ID"]; //$row["stone"],        //货号
            $row1["warehouse"]=$warehouse; //库房  SHCOM
            $row1["source"]=$this->conf['source']; //货品来源
            $row1["shape"]=strtoupper($row["Shape"]); //"ROUNDS";              //形状
            $row1["carat"]=$row["Wt"]; //石重:Weight
            $row1["color"]=$row["Colour"]; //颜色:colorName
            $row1["clarity"]=$row["Clarity"]; //净度:clarityName
            $row1["cut"]=$row["Cut"]; //切工:cutName
            $row1["sym"]=$row["Symmetry"]; //对称:symName
            $row1["po"]=$row["Polish"]; //抛光:polName
            $row1["fl"]=$row["Flourence"]; //荧光:fluoName
            $row1["diam"]=$row["Measurements"]; //尺寸:LxWxD
            $row1["depth"]=$row["DepthPer"]; //全深比:Depth1
            $row1["table"]=$row["TablePer"]; //台宽比:Table1
            $row1["cert"]=$row["Certificate"]; //证书:labName
            $row1["cert_id"]=$row["CertificateNo"]; //证书号:certNo
            $row1["shop_price"]=round($row["rapprice"]*(100+$row['Back'])/100*$row['Wt']*DOLLARTORMB*JIAJIALV,2); //每颗价格:Amount
            $row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE  //会员价计算出
            $row1["chengbenjia"]=round($row["rapprice"]*(100+$row['Back'])/100*$row['Wt']*DOLLARTORMB*JIAJIALV,2);
            $row1["cts"]=round($row["rapprice"]*(100+$row['Back'])/100,2); // 每克拉价
            $row1["guojibaojia"]=$row["rapprice"]; // 国际报价
            $row1["us_price_source"]=round($row["rapprice"]*(100+$row['Back'])/100*$row['Wt'],2); // 美元价 
            $row1["xianggangjia"]='';
            $row1["gemx_zhengshu"]='';
            $row1["source_discount"]=abs($row["Back"]);
            $row1["mo_sn"]=''; //模号

            if($row["Back"]>0)
            	continue;
            /*
            if(!in_array($row1["shape"],array('RBC','Pear','Heart','Princess','Cushion',' Emerald'))){
                continue;
            }*/


            if(!in_array($row1["color"], array('D', 'E', 'F', 'G', 'H', 'I', 'J', 'K')))
                continue;            
            if(!in_array($row1["clarity"], array('FL', 'IF', 'VVS1', 'VVS2', 'SI1', 'VS1', 'VS2')))
                continue;
            if($row1["carat"] < 0.3)
                continue;               
            if(!in_array($row1["cert"], array('GIA')))
                continue;    
            if(!in_array($row1["cut"],array('VG','EX','G'))) //切工:cutName EX VG G
                continue;
            if( in_array($row1['fl'],array('VST')))   
            	continue;
            if(!in_array($row["Milky"], array('Non')))
                continue;    
            if(!in_array($row["Shade"], array('White')))
                continue;  
            if(!in_array($row["status"], array('A')))
                continue;                                           
            //$row1['fl']=substr($row1['fl'], 0,1);

            
            echo $row1['goods_sn']."\t";
            if($this->download==0){
                try{ //待更新到diamond表的goods_id
                    $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
                }catch(Exception $e){
                    $this->errormsg=$e->getMessage();
                    $this->runerror(__FILE__,__LINE__); //写进错误LOG
                    return false;
                }
            }
            //编号    库房  形状  重量  颜色  净度  切工  对称  抛光  荧光  尺寸  全深比 台宽比 证书  证书号 售价  会员价
            unset($row1['source']);
            fputcsv($handle,$row1);
        }
        
        if($tmp){
            $this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,10);
            foreach($strr as $val){
                $this->adddiaDiamond($val,$this->conf['source']); 
            }
                          
            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
    }

    /**
     *
     * slk接口 no29
     */
    function m_slk()
    {
        $online=1;
        $fiveone_way=1;

        ini_set('default_socket_timeout', 150);

        while(1){
            if($online){
                try
                {



                    $diaxmllist=array();
		            $flag   = TRUE;
		            $page   = 1; //页数，默认第一页
		            $url    = 'http://www.slkdiamond.com/api/stocks/get_stocks_all/3A6776B85F1F1E8F87B7774A9680F6F6/';

		            while ($flag) {
		                $query_url  = $url.$page;
		                $ch         = curl_init();
		                curl_setopt($ch, CURLOPT_URL, $query_url);
		                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		                curl_setopt($ch, CURLOPT_HEADER, 0);
		                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		                $response   = curl_exec($ch);

		                if (FALSE !== $response) {
		                    $data   = json_decode($response, TRUE);
		                    if ($data['status']) {
		                        $total  = $data['page_total'];
		                        $results= $data['results'];
		                        unset($data);

		                        if(!empty($results)){
		                            //钻石数据操作
		                            //echo $page."<br>";
		                            //echo "<pre>";
		                            //print_r($results);
		                            foreach ($results as $key => $v) {
		                            	$diaxmllist[]=$v;
		                            }		                            
		                        }

		                        $flag   = ($page >= $total) ? FALSE : TRUE;
		                        $page++;
		                    } else {
		                        $flag   =   FALSE;
		                        //$data['message']错误信息
		                    }
		                } else {
		                    //接口获取失败
		                    echo curl_error($ch);
		                    exit;
		                }
		                curl_close($ch);
		            }















                  
                }
                catch (Exception $sf)
                {
                    $this->errormsg=$sf->getMessage();
                    $this->writeLog($sf->getMessage());
                    $this->runerror(__FILE__,__LINE__); //写进错误LOG
                    return false;
                }
            }else{
            }
            //初始化表
            if($diaxmllist!=''){
                global $db_dia;
                create_data($this->conf['source']);
                break;
            }else{
                $this->writeLog('no file');
                die();
                return false;
            }
        }

 


        $parcelStatus=array();
        $parcelException=array();
        //信息对象数组化
        //file_put_contents('diarough'.date('Ymd'),$diaxmllist);
        //$diaarrlist=simplexml_load_string($diaxmllist);
        $diaarrlist=$diaxmllist;

        if(is_null($diaarrlist)){
            $this->writeLog('no file');
            die();
        }
        $csvdir=ROOT_PATH."data/".$this->conf['source'];
        if(is_dir($csvdir)==''){
            //清空目录
            mkdir($csvdir);
        }else{
            cleardirfile($csvdir);
        }

        //获取活动钻信息 一维 goods_sn=>shop_price
        //$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        //$certAllowList=$this->certAllow();
        //生成标准文件
        $csvfie=$csvdir."/".$this->conf['source'].".csv";
        $handle=fopen($csvfie,"w");

        $gid=array();
        //csv头信息
        $diaCsvTitle=array();
        foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
            $diaCsvTitle[$key]=iconv("UTF-8","GBK",$value);
        }
        fputcsv($handle,$diaCsvTitle);
        /*$diaarrlists=array();
        foreach($diaarrlist as $row){
            $diaarrlists[$row['CERTIFICATE_NO']]=$row;
        }*/
        $tmp=array();
        foreach($diaarrlist as $key => $row){
            $isQihuo = false;
            if(in_array($row['location'], array('香港','深圳'))){
                $isQihuo = true;
            }
            $warehouse='COM';
            $row1=array();
            //$row1["goods_id"]=$i; //$row["stone"],        //货号
            $row1["goods_sn"]=$row['no'];//$row["PARCEL_ID"]; //$row["stone"],        //货号
            $row1["warehouse"]=$warehouse; //库房  SHCOM
            $row1["source"]=$this->conf['source']; //货品来源
            $row1["shape"]=$row["shape"]; //"ROUNDS";              //形状
            $row1["carat"]=$row["size"]; //石重:Weight
            $row1["color"]=$row["color"]; //颜色:colorName
            $row1["clarity"]=$row["clarity"]; //净度:clarityName
            $row1["cut"]=$row["cut"]; //切工:cutName
            $row1["sym"]=$row["symmetry"]; //对称:symName
            $row1["po"]=$row["polish"]; //抛光:polName
            $row1["fl"]=$row["fluor"]; //荧光:fluoName
            $row1["diam"]=$row["measure"]; //尺寸:LxWxD
            $row1["depth"]=$row["depth"]; //全深比:Depth1
            $row1["table"]=$row["table"]; //台宽比:Table1
            $row1["cert"]=$row["cert"]; //证书:labName
            $row1["cert_id"]=$row["cert_no"]; //证书号:certNo
            $row1["shop_price"]=round($row["rap_price"]*(100+$row['discount'])/100*$row['size']*DOLLARTORMB*JIAJIALV,2); //每颗价格:Amount
            $row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE  //会员价计算出
            $row1["chengbenjia"]=round($row["rap_price"]*(100+$row['discount'])/100*$row['size']*DOLLARTORMB*JIAJIALV,2);
            $row1["cts"]=round($row["rap_price"]*(100+$row['discount'])/100,2); // 每克拉价
            $row1["guojibaojia"]=$row["rap_price"]; // 国际报价
            $row1["us_price_source"]=round($row["rap_price"]*(100+$row['discount'])/100*$row['size'],2); // 美元价 
            $row1["xianggangjia"]='';
            $row1["gemx_zhengshu"]='';
            $row1["source_discount"]=abs($row["discount"]);
            $row1["mo_sn"]=''; //模号
            $row1["img"]=$row['img']; //图片
            $row1["good_type"]= (($isQihuo)?1:2); //现货 期货 or $row['good_type'];

            if($row["discount"]>0)
            	continue;
    
            global $diaShapeVal;
            //if(in_array($row1["shape"],array("枕形")))
            	//continue;
            $row1["shape"]=$diaShapeVal[$row1["shape"]];
            if(!in_array($row1["shape"],array('RBC','Pear','Heart','Princess','Cushion',' Emerald'))){
                //continue;
            }
            if($row1["shape"]=='RBC') 
                $row1["shape"]='ROUNDS';

            if($row1["carat"] < 0.3)
                continue;
            
            if(!in_array($row1["color"], array('D', 'E', 'F', 'G', 'H', 'I', 'J', 'K')))
               continue;
            //if(!in_array($row1["clarity"], array('FL', 'IF', 'VVS1', 'VVS2', 'SI1', 'VS1', 'VS2')))
            //    continue;               
            if(!in_array($row1["cert"], array('GIA')))
                continue;    
            if(in_array($row1["cut"],array('FAIR'))) //切工:cutName
                $row1['cut']='Fair';
            if(in_array($row1["po"], array('GD'))) //抛光:polName
                $row1["po"]='G';
            if(in_array($row1["sym"],array('GD'))) //对称:symName
                $row1["sym"]='G'; 
            if(in_array($row1["sym"],array('F'))) //对称:symName
                $row1["sym"]='Fair';                   
            if(in_array($row1["fl"],array('VSTG','V')))
                $row1['fl']="VS";    
            
           //echo "<pre>";
           //print_r($row);
            //if(in_array($row1["color"], array('D', 'E', 'F', 'G', 'H', 'I', 'J', 'K')))
            echo $row1['goods_sn']."\t";
            if($this->download==0){
                try{ //待更新到diamond表的goods_id
                    $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
                }catch(Exception $e){
                    $this->errormsg=$e->getMessage();
                    $this->runerror(__FILE__,__LINE__); //写进错误LOG
                    return false;
                }
            }
            //编号    库房  形状  重量  颜色  净度  切工  对称  抛光  荧光  尺寸  全深比 台宽比 证书  证书号 售价  会员价
            unset($row1['source']);
            fputcsv($handle,$row1);
        }
        
        if($tmp){
            $this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,10);
            foreach($strr as $val){
                $this->adddiaDiamond($val,$this->conf['source']); 
            }
                          
            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
    }




    /**
 *
 * starrays接口 no31
 */
    function m_starrays()
    {
        $online=true;//true;
        //获取文件信息
        while(1){
            set_time_limit(0);
            $url = "http://starrays.com/DataService/KC/StockDown.aspx?uname=kelachina&pwd=Li7747";
            $data = file_get_contents($url);

            if(!empty($data)){
                $data = '['.$data.']';
                $diaxmllist = json_decode($data);

            }


            //初始化表
            if($diaxmllist!=''){
                global $db_dia;
                create_data($this->conf['source']);
                break;
            }
        }

        global $diaCat;
        $csvdir=ROOT_PATH."data/".$this->conf['source'];
        if(is_dir($csvdir)){
            //清空目录
            cleardirfile($csvdir);
        }else{
            mkdir($csvdir);
        }
        @file_put_contents($csvdir."/".$this->conf['source'].".data");
        //获取活动钻信息 一维 goods_sn=>shop_price
        //$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        //$certAllowList=$this->certAllow();

        //生成标准文件
        $csvfie=$csvdir."/".$this->conf['source'].".csv";
        $handle=fopen($csvfie,"w");

        $gid=array();
        //csv头信息
        $diaCsvTitle=array();
        foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
            $diaCsvTitle[$key]=iconv("UTF-8","CP936",$value);
        }
        fputcsv($handle,$diaCsvTitle);
        $diaarrlists=array();
        foreach($diaxmllist as $row){
            $row=(array)$row;
            $diaarrlists["".$row['CertNo']]=$row;
        }

        $tmp=array();
        $i=0;
        $j=0;


        foreach($diaarrlists as $row){
            //$row=(array)$row; //xml对象转数组
            if(!isset($diaCat[strtoupper($row["Shape"])])){
                continue;
            }
            if($row["Rate"]<0){
                continue;
            }





            $row1=array();
            $row1["goods_sn"]=str_replace(array("."),array(""),$row["Ref.No"]); //$row["stone"];		//货号

            $row1["warehouse"]="COM"; //库房  SHCOM
            $row1["source"]=$this->conf['source']; //货品来源
            $row1["shape"]=strtoupper($row["Shape"]); //"ROUNDS";				//形状
            $row1["carat"]=$row["Cts."]; //石重:Weight
            $row1["color"]=$row["Color"]; //颜色:colorName
            $row1["clarity"]=$row["Clarity"]; //净度:clarityName
            $row1["cut"]=$row["Cut"]; //切工:cutName
            $row1["sym"]=$row["Sym."]; //对称:symName
            $row1["po"]=$row["Pol."]; //抛光:polName
            $row1["fl"]=updat_fluor($row["FL"]); //荧光:fluoName
            $row1["diam"]=$row["Measurement"]; //尺寸:LxWxD
            $row1["depth"]=$row["Depth"]; //全深比:Depth1
            $row1["table"]=$row["Table"]; //台宽比:Table1
            $row1["cert"]=$row["Cert."]; //证书:labName
            $row1["cert_id"]=$row["CertNo"]; //证书号:certNo
            $row1["shop_price"]=$row["Amount"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
            $row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE	//会员价计算出
            $row1["chengbenjia"]=$row["Amount"]*DOLLARTORMB*JIAJIALV;
            $row1["cts"]=$row["Rate"]; // 每克拉价
            $row1["guojibaojia"]=$row["Rap"]; //国际报价
            $row1["us_price_source"]=$row["Amount"]; //美元价
            $row1["xianggangjia"]='';
            $row1["source_discount"]=0;
            $row1["xianggangjia"]='';
            $row1["gemx_zhengshu"]='';
            $row1["source_discount"]=abs($row["Rap.%"]);
            $row1["mo_sn"]=''; //模号
            $row1["img"]=$row['Image']; //图片
            $row1['good_type'] = 2;

            if($row["Rap.%"]>0)
                continue;
            if(!in_array($row1["color"], array('D', 'E', 'F', 'G', 'H', 'I', 'J', 'K')))
                continue;
            if(!in_array($row1["clarity"], array('FL', 'IF', 'VVS1', 'VVS2', 'SI1', 'VS1', 'VS2')))
                continue;
            if($row1["carat"] < 0.3)
                continue;
            if(!in_array($row1["cert"], array('GIA')))
                continue;
            if(!in_array($row1["cut"],array('VG','EX','G'))) //切工:cutName
                continue;
            if( in_array($row1['fl'],array('VST')))
                continue;
            if(empty($row1["shape"]))
            	continue;
            if(!in_array($row["Lus"], array('EX')))
                continue;
            if($row["Col.Shade"]<>'')
                continue;
            if(!in_array($row["Status"], array('S')))
                continue;


           // echo $row1['goods_sn']."\t";
            if($this->download==0){
                try{ //待更新到diamond表的goods_id
                    $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
                }catch(Exception $e){
                    $this->errormsg=$e->getMessage();
                    $this->runerror(__FILE__,__LINE__); //写进错误LOG
                    return false;
                }
            }
            //编号    库房  形状  重量  颜色  净度  切工  对称  抛光  荧光  尺寸  全深比 台宽比 证书  证书号 售价  会员价
            unset($row1['source']);
            fputcsv($handle,$row1);


        }



        if($tmp){
            $this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,10);
            foreach($strr as $val){
                $this->adddiaDiamond($val,$this->conf['source']);
            }

            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }}


    /**
     *
     * starrays接口 no32
     */
    function m_shawn()
    {
        $online=true;//true;
  

        while(1){
            set_time_limit(0);
            //$url = "https://sheetalstock.sheetalgroup.com/stock.asmx/FullStock?%20HTTP/1.1";
            //$xml = file_get_contents($url);
            //$url = "http://smcstock.sheetalgroup.com/stock.asmx?UserName=Shawn&Password=shawn123";
            //$xml = file_get_contents($url);
            //libxml_disable_entity_loader(true);
            //$xml_string = simplexml_load_string($xml,'SimpleXMLElement', LIBXML_NOCDATA);
            //echo $xml_string;
            //$diaxmllist = json_decode(json_encode($xml_string),true);
            //获取文件信息
            $url = "http://smcstock.sheetalgroup.com/stock.asmx/ClientLogin?UserName=Shawn&Password=12345kela";
            $xml = file_get_contents($url);
            $obj = simplexml_load_string($xml);
            $token = $obj->Table->Token;
            if(empty($token))
            	break;
            $token = current($token);
            $PartyId = $obj->Table->PartyId;
            $PartyId = current($PartyId);
            $url = "http://smcstock.sheetalgroup.com/stock.asmx/FullStock?Token={$token}&PartyId={$PartyId}";
            $xml = file_get_contents($url);
            //$obj = simplexml_load_string($xml);
            $obj = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            if(empty($obj['Table']))
                break; 
            //echo "<pre>";          
            //print_r($obj);
            $diaxmllist = $obj['Table'];
            //break; 
            //print_r($diaarrlist) ;exit();
            //初始化表
            if($diaxmllist!=''){
                global $db_dia;
                create_data($this->conf['source']);
                break;
            }
        }

        global $diaCat;
        $csvdir=ROOT_PATH."data/".$this->conf['source'];
        if(is_dir($csvdir)){
            //清空目录
            cleardirfile($csvdir);
        }else{
            mkdir($csvdir);
        }
        @file_put_contents($csvdir."/".$this->conf['source'].".data");
        //获取活动钻信息 一维 goods_sn=>shop_price
        //$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        //$certAllowList=$this->certAllow();

        //生成标准文件
        $csvfie=$csvdir."/".$this->conf['source'].".csv";
        $handle=fopen($csvfie,"w");

        $gid=array();
        //csv头信息
        $diaCsvTitle=array();
        foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
            $diaCsvTitle[$key]=iconv("UTF-8","CP936",$value);
        }
        fputcsv($handle,$diaCsvTitle);
        $diaarrlists=$diaxmllist;
        //foreach($diaxmllist as $row){
           //$row=(array)$row;
           // $diaarrlists["".$row['CERTNO']]=$row;
        //}
        //print_r($diaarrlists);exit;
        $tmp=array();
        $i=0;
        $j=0;
        foreach($diaarrlists as $row){
            //$row=(array)$row; //xml对象转数组
            if(!isset($diaCat[strtoupper($row["SHAPE_CODE"])])){
                continue;
            }
            if($row["AMOUNT"]<0){
                continue;
            }




            $row1=array();
            $row1["goods_sn"]=str_replace(array("."),array(""),$row["STONE_NO"]); //$row["stone"];		//货号

            $row1["warehouse"]="COM"; //库房  SHCOM
            $row1["source"]=$this->conf['source']; //货品来源
            $row1["shape"]=strtoupper($row["SHAPE_CODE"]); //"ROUNDS";				//形状
            $row1["carat"]=$row["CARAT"]; //石重:Weight
            $row1["color"]=$row["COLOR_CODE"]; //颜色:colorName
            $row1["clarity"]=$row["CLARITY_CODE"]; //净度:clarityName
            $row1["cut"]=$row["CUT_CODE"]; //切工:cutName
            $row1["sym"]=$row["SYMMETRY_CODE"]; //对称:symName
            $row1["po"]=$row["POLISH_CODE"]; //抛光:polName
            $row1["fl"]=updat_fluor($row["FLUORESCENCE_CODE"]); //荧光:fluoName
            $row1["diam"]=$row["LWD"]; //尺寸:LxWxD
            $row1["depth"]=$row["TOTALDEPTHPER"]; //全深比:Depth1
            $row1["table"]=$row["TABLEDIAMETERPER"]; //台宽比:Table1
            $row1["cert"]=$row["LAB"]; //证书:labName
            $row1["cert_id"]=$row["CERTNO"]; //证书号:certNo
            $row1["shop_price"]=$row["AMOUNT"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
            $row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE	//会员价计算出
            $row1["chengbenjia"]=$row["AMOUNT"]*DOLLARTORMB*JIAJIALV;
            $row1["cts"]=$row["PRICEPERCRT"]; // 每克拉价
            $row1["guojibaojia"]=$row["RAPPRICE"]; //国际报价
            $row1["us_price_source"]=$row["AMOUNT"]; //美元价
            $row1["xianggangjia"]='';
            $row1["source_discount"]=0;
            $row1["xianggangjia"]='';
            $row1["gemx_zhengshu"]='';
            $row1["source_discount"]=abs($row["DISCOUNT"]);
            $row1["mo_sn"]=''; //模号
            $row1["img"]=$row['SHARE_DETAIL_URL']; //图片
            $row1['good_type'] = 2;


            if($row["DISCOUNT"]>0)
                continue;


            if(!in_array($row1["color"], array('D', 'E', 'F', 'G', 'H', 'I', 'J', 'K')))
                continue;
            if(!in_array($row1["clarity"], array('FL', 'IF', 'VVS1', 'VVS2', 'SI1', 'VS1', 'VS2')))
                continue;
            if($row1["carat"] < 0.3)
                continue;
            if(!in_array($row1["cert"], array('GIA')))
                continue;
            if(!in_array($row1["cut"],array('VG','EX','G'))) //切工:cutName
                continue;
            if( in_array($row1['fl'],array('VST')))
                continue;
            if(empty($row1["shape"]))
            	continue;
            if(!in_array($row["HUE"], array('WH')))
                continue;
            if($row["LOCATION"]=='USA')
                continue;
            if(!in_array($row["WEBSTATUS"], array('AVAILABLE')))
                continue;
            if(!in_array($row["EYECLEAN"], array('Yes')))
                continue;
            if(!in_array($row["MILKY"], array('NO')))
                continue;                            
            if(!in_array($row["LOCATION"], array('INDIA')))
                continue;


            echo $row1['goods_sn']."\t";
            if($this->download==0){
                try{ //待更新到diamond表的goods_id
                    $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
                }catch(Exception $e){
                    $this->errormsg=$e->getMessage();
                    $this->runerror(__FILE__,__LINE__); //写进错误LOG
                    return false;
                }
            }
            //编号    库房  形状  重量  颜色  净度  切工  对称  抛光  荧光  尺寸  全深比 台宽比 证书  证书号 售价  会员价
            unset($row1['source']);
            fputcsv($handle,$row1);



        }




        if($tmp){
            $this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,10);
            foreach($strr as $val){
                $this->adddiaDiamond($val,$this->conf['source']);
            }

            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
    }



    /**
     *
     * kiran接口 no14
     */
    function m_kiran()
    {
        $online=true;//true;
  
        try{
        
            $url = "http://diamonds.kirangems.com/GemKOnline/jsnsearch/jsndetail/query?username=lisalisa&password=12345kela&view=slkdiamond";            
            
            $post_data = array(
                'username'=>'lisalisa',
                'password' =>'12345kela',
                'view'  => 'slkdiamond',
            );            
            $post_data = http_build_query($post_data);         
            $data = $this->curl_post($url,$post_data);
            //echo $xml;
            $diaxmllist = json_decode($data);
            //echo "<pre>";
            //print_r($diaxmllist->StoneDetails);
            if(empty($data) ){
                $this->errormsg = "获取数据失败";
                $this->writeLog($this->errormsg);
                $this->runerror(__FILE__,__LINE__); //写进错误LOG
                return false;
            }
            $diaarrlist = $diaxmllist->StoneDetails;
            
            //$diaarrlist = simplexml_load_string($diaxmllist);
            /*
            if(empty($diaarrlist->ROW)){
                $this->errormsg = "抓取钻石信息为空";
                $this->writeLog($this->errormsg);
                $this->runerror(__FILE__,__LINE__); //写进错误LOG
                return false;
            }*/
        }catch(SoapFault $e){
            $this->errormsg = $e->getMessage();
            $this->writeLog($this->errormsg);
            $this->runerror(__FILE__,__LINE__); //写进错误LOG
            return false;
        }

        if(!empty($diaarrlist)){
                global $db_dia;
                create_data($this->conf['source']);
              
        }

        global $diaCat;
        $csvdir=ROOT_PATH."data/".$this->conf['source'];
        if(is_dir($csvdir)){
            //清空目录
            cleardirfile($csvdir);
        }else{
            mkdir($csvdir);
        }
        @file_put_contents($csvdir."/".$this->conf['source'].".data");
        //获取活动钻信息 一维 goods_sn=>shop_price
        //$activeOpenResult=$this->activeOpen();
        //验证允许的证书类型
        //$certAllowList=$this->certAllow();

        //生成标准文件
        $csvfie=$csvdir."/".$this->conf['source'].".csv";
        $handle=fopen($csvfie,"w");

        $gid=array();
        //csv头信息
        $diaCsvTitle=array();
        foreach($GLOBALS['diaCsvTitle'] as $key=>$value){
            $diaCsvTitle[$key]=iconv("UTF-8","CP936",$value);
        }
        fputcsv($handle,$diaCsvTitle);
        $diaarrlists=$diaarrlist;
        //foreach($diaxmllist as $row){
           //$row=(array)$row;
           // $diaarrlists["".$row['CERTNO']]=$row;
        //}
        //print_r($diaarrlists);exit;
        $tmp=array();
        $i=0;
        $j=0;
        foreach($diaarrlists as $row){
        	//$row = $this->objectToArray($row);
            $row=(array)$row; //xml对象转数组
            if($row['Shp']=='RD')
            	$row['Shp']='RAD';

            if(!isset($diaCat[strtoupper($row["Shp"])])){
                continue;
            }
            if($row["Amt"]<0){
                continue;
            }




            $row1=array();
            $row1["goods_sn"]=$row["StoneNo"]; //$row["stone"];		//货号

            $row1["warehouse"]="COM"; //库房  SHCOM
            $row1["source"]=$this->conf['source']; //货品来源
            $row1["shape"]=strtoupper($row["Shp"]); //"ROUNDS";				//形状
            $row1["carat"]=$row["Cts"]; //石重:Weight
            $row1["color"]=$row["Col"]; //颜色:colorName
            $row1["clarity"]=$row["Clr"]; //净度:clarityName
            $row1["cut"]=$row["Cut"]; //切工:cutName
            $row1["sym"]=$row["Sym"]; //对称:symName
            $row1["po"]=$row["Pol"]; //抛光:polName
            $row1["fl"]=updat_fluor($row["Flr"]); //荧光:fluoName
            $row1["diam"]=$row["LWD"]; //尺寸:LxWxD
            $row1["depth"]=$row["TD"]; //全深比:Depth1
            $row1["table"]=$row["Tbl"]; //台宽比:Table1
            $row1["cert"]=$row["Lab"]; //证书:labName
            $row1["cert_id"]=$row["RepNo"]; //证书号:certNo
            $row1["shop_price"]=$row["Amt"]*DOLLARTORMB*JIAJIALV; //每颗价格:Amount
            $row1["member_price"]=""; //$row["Amount"] * * MEMBER_RATE	//会员价计算出
            $row1["chengbenjia"]=$row["Amt"]*DOLLARTORMB*JIAJIALV;
            $row1["cts"]=$row["PRICEPERCRT"]; // 每克拉价
            $row1["guojibaojia"]=$row["Rap"]; //国际报价
            $row1["us_price_source"]=$row["Amt"]; //美元价
            $row1["xianggangjia"]='';
            $row1["source_discount"]=0;
            $row1["xianggangjia"]='';
            $row1["gemx_zhengshu"]='';
            $row1["source_discount"]=abs($row["Disc"]);
            $row1["mo_sn"]=''; //模号
            $row1["img"]=$row['video']; //图片
            $row1['good_type'] = 2;


            if($row["Disc"]<0)
                continue;


            if(!in_array($row1["color"], array('D', 'E', 'F', 'G', 'H', 'I', 'J', 'K')))
                continue;
            if(!in_array($row1["clarity"], array('FL', 'IF', 'VVS1', 'VVS2', 'SI1', 'VS1', 'VS2')))
                continue;
            if($row1["carat"] < 0.3)
                continue;
            if(!in_array($row1["cert"], array('GIA')))
                continue;
            //if(!in_array($row1["cut"],array('VG','EX','G'))) //切工:cutName
            //    continue;
            //if( in_array($row1['fl'],array('VST')))
            //    continue;
            if(empty($row1["shape"]))
            	continue;
            //if(!in_array($row["HUE"], array('WH')))
            //    continue;
            if(!in_array($row["Loc"], array('HK','INDIA')))
                continue;
            if(!in_array($row["Status"], array('Available')))
                continue;
            if(!empty($row['ColTinge']))
                continue;
            if(!empty($row['Luster']))
                continue;            
            if(!empty($row['Remark']))
                continue;
            if(!in_array($row["LI"], array('Yes')))
                continue;            


            echo $row1['goods_sn']."\t";
            if($this->download==0){
                try{ //待更新到diamond表的goods_id
                    $getAddDiamond=$this->get_adddia($row1,$this->conf['source']);
                    if($getAddDiamond){
                        $tmp[]=$getAddDiamond;
                    }
                }catch(Exception $e){
                    $this->errormsg=$e->getMessage();
                    $this->runerror(__FILE__,__LINE__); //写进错误LOG
                    return false;
                }
            }
            //编号    库房  形状  重量  颜色  净度  切工  对称  抛光  荧光  尺寸  全深比 台宽比 证书  证书号 售价  会员价
            unset($row1['source']);
            fputcsv($handle,$row1);



        }




        if($tmp){
            $this->writeLog("tmp ttl: ".count($tmp));
            $strr=$this->getSelect($tmp,10);
            foreach($strr as $val){
                $this->adddiaDiamond($val,$this->conf['source']);
            }

            if($this->download==1){
                return false;
            }
            //把抓取过来的数据之间写入到ecs_diamond表里边
            $datafile=$csvdir."/".$this->conf['source'].".sql";
            update_data($this->conf['source'],$datafile,$this);
        }
    }




	function curl_post($url,$data,$header=false,$post=true,$timeout=300){
		
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_HEADER,$header);
		curl_setopt($ch,CURLOPT_POST,$post);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_TIMEOUT,$timeout);		
		$result=curl_exec($ch);

		if($result === false)
		{
		    $this->writeLog('Curl error: ' . curl_error($ch));
		}
		curl_close($ch);
		return $result;
	}

    function arrtoxml($arr,$dom=0,$item=0)
    {
        if (!$dom){
            $dom = new DOMDocument("1.0");
        }
        if(!$item){
            $item = $dom->createElement("NewDataSet"); 
            $dom->appendChild($item);
        }
        foreach ($arr as $key=>$val){
            $itemx = $dom->createElement(is_string($key)?$key:"KELA_CN");
            $item->appendChild($itemx);
            if (!is_array($val)){
                $text = $dom->createTextNode($val);
                $itemx->appendChild($text);
                
            }else {
                $this->arrtoxml($val,$dom,$itemx);
            }
        }
        return $dom->saveXML();
    }

	function objectToArray($obj)
	{
	    $arr = is_object($obj) ? get_object_vars($obj) : $obj;
	    if(is_array($arr))
	    {
	        return array_map(__FUNCTION__, $arr);
	    }
	    else
	    {
	        return $arr;
	    }
	}


}


