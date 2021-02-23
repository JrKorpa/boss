<?php
/*[#YYB+
-- 裸钻自动获取
#]*/
error_reporting(E_ALL ^ E_DEPRECATED);
date_default_timezone_set("Asia/Shanghai");
header("Content-type:text/html;charset=utf8;");

define('IN_ECS',true);
define('ROOT_PATH', str_replace('get_3d_html.php', '', str_replace('\\', '/', __FILE__)));


define('KELA_ROOT', str_replace('\\','/',realpath(rtrim(ROOT_PATH,'/').'/../../')));//定义网站根目录

set_time_limit(0);
require(ROOT_PATH."app_mysql.php"); //数据对象

$config = array();
$config['db_type'] = 'mysql';
$config['db_port'] = 3306;
$config['db_name'] = 'front';

// release:
$config['db_host'] = '192.168.1.192';
$config['db_user'] = 'cuteman';
$config['db_pwd'] = 'QW@W#RSS33#E#';

// debug:
/*$config['db_host'] = '192.168.0.91';
$config['db_user'] = 'root';
$config['db_pwd'] = '123456';*/
/*
$config['db_host'] = '192.168.0.95';
$config['db_user'] = 'cuteman';
$config['db_pwd'] = 'QW@W#RSS33#E#';
*/
$db=new KELA_API_DB($config);


$from = 0;
if($argv[1]){
   $from = $argv[1];
}

$sql="select distinct left(img,40) from diamond_info where length(img)>40";
//$res = $db->getAll($sql);
//echo "<pre>";
//print_r($res);

    $sql ="select d.goods_id,d.goods_sn,d.cert_id,d.img,d.from_ad,left(d.img,40) as img from diamond_info d left join diamond_info_3d p on d.cert_id=p.cert_id where (d.img like 'https://diamonds.kirangems.com/GemKOnline/DiaSearch/appVideo.jsp?idv=%')
     and p.id is null order by d.goods_id desc limit {$from},1000";
    echo $sql.PHP_EOL;
	$res = $db->getAll($sql);
	if($res){		
		$json_array = ['0.json','1.json','2.json','3.json','4.json','5.json','sm.json'];
		foreach ($res as $key => $item) {
			$cert_id = $item['cert_id'];
			$from_ad = $item['from_ad'];
            $img = $item['img'];
            $goods_sn = $item['goods_sn'];

			switch($img){
				case "https://diamonds.kirangems.com/GemKOnlin" :
					$downloadfilename_path_dir = KELA_ROOT."/diamondView/imaged/imaged/".$cert_id;
					if (!is_dir($downloadfilename_path_dir)) {				
						@mkdir($downloadfilename_path_dir, 0777, true);
					}
					$i=0;
					foreach ($json_array as $f_key => $file) {
						if(file_exists($downloadfilename_path_dir."/".$file)){
							continue;
						}
						try{
							$file_content = post("https://diamonds.kirangems.com:8080/imaged/imaged/{$cert_id}/".$file,"");
							//echo "https://diamonds.kirangems.com:8080/imaged/imaged/{$cert_id}/".$file.PHP_EOL;

							if($file_content){
								file_put_contents($downloadfilename_path_dir."/".$file,$file_content);
								$i++;
							}else{
		                        echo $cert_id." error".PHP_EOL;
							}					
						}catch(Exception $ex){
							echo "ERROR:".json_encode($ex).PHP_EOL;
							continue;
						}				
					}
					
					if($i==7){
						try{
	                        $db->query("insert into diamond_info_3d values (0,'{$cert_id}','')");
						    echo $cert_id.PHP_EOL;
					    }catch(Exceptin $ex){
                            echo "ERROR:".json_encode($ex).PHP_EOL; 
					    }
					}
					sleep(1);
					break;
				case "https://www.sheetalgroup.com/Details/Sto" :
                    $url = "https://d21g74glrkyyov.cloudfront.net/{$goods_sn}/{$goods_sn}.html"; 
                    try{
	                    $file_content = post($url,'');
	                    if(!empty($file_content)){
	                        $downloadfilename_path_dir = KELA_ROOT."/diamondView/imaged/html/";
	                        file_put_contents($downloadfilename_path_dir.$cert_id.".html",$file_content);                    	
	                        $db->query("insert into diamond_info_3d values (0,'{$cert_id}','')");
						    echo $cert_id.PHP_EOL;
	                    }
                    }catch(Exception $ex){
                        echo "ERROR:".json_encode($ex).PHP_EOL;
                    }   				
				    break;
				case "https://starrays.com/aspxpages/FrmStoneD"	:

				   break;
				default:
				   echo "none".PHP_EOL;

			}


			
		}
	}






    function post($url,$body) 
        { 
             $curlObj = curl_init();
             curl_setopt($curlObj, CURLOPT_URL, $url); // 设置访问的url
             curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1); //curl_exec将结果返回,而不是执行
             curl_setopt($curlObj, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded;charset=UTF-8"));
             curl_setopt($curlObj, CURLOPT_URL, $url);
             curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, FALSE);
             curl_setopt($curlObj, CURLOPT_SSL_VERIFYHOST, FALSE);
             curl_setopt($curlObj, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
            
             curl_setopt($curlObj, CURLOPT_CUSTOMREQUEST, 'GET');      
            
             curl_setopt($curlObj, CURLOPT_POST, true);
             curl_setopt($curlObj, CURLOPT_POSTFIELDS, $body);       
             curl_setopt($curlObj, CURLOPT_ENCODING, 'gzip');

             $res = @curl_exec($curlObj);
             //var_dump($res);
             curl_close($curlObj);

            return $res;
        }

?>