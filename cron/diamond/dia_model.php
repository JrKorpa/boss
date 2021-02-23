<?php
error_reporting(E_ALL&~E_NOTICE);
/*[#YYB+
-- 裸钻数据模型类，添加更新操作
#]*/
$pfj_config = array();
class diamondModel
{
    public static $from_ad;

    function __construct()
    {

    }

    /**
     * 写操作日志
     * William Huang
     */
    public function writeLog($line) {

        $string = "[" . date("Y-m-d H:i:s") . "] ";
        $string .= $line;
        if ($_SERVER['HTTP_USER_AGENT'] != NULL) {
            echo $string . "<br/>\n";
        } else {
            echo $string . "\n";
        }
        $string .= "\n";
        $tp = fopen(ROOT_PATH."data/".self::$from_ad. "/work.log", "a");
        fwrite($tp, $string);
        fclose($tp);
    }

    /**
     * 更新最后抓取时间
     */
    public function updateFetchTime()
    {
        $sql = "UPDATE `diamond_vendor` SET `updated`='". date("Y-m-d H:i:s"). "' WHERE `title`='". self::$from_ad."';";
        $GLOBALS['db']->query($sql);
    }

    /**
     *
     * 开始增加数据
     * @param unknown_type $dia
     * @param unknown_type $table
     */
    function get_adddia($dia,$table="diamond")
    {
        global $diaCat,$diaDefault,$db;

        //对应裸钻的供应商 
        //$fromad_arr = array(1=>'kela',2=>'fiveonezuan',3=>'venus',4=>'dharam',5=>'diamondbyhk',6=>'diarough',7=>'emd',8=>'gd',9=>'jb',10=>'kapu',11=>'kgk',12=>'hy',13=>'leo',14=>'kiran',15=>'vir',16=>'karp',17=>'enjoy',18=>'changning',19=>"kb",20=>'kg');
        //$from_ad_ids = array_flip($fromad_arr);
        
        $from_ad = $db->getOne("select vendor_id from `diamond_vendor` WHERE `title` = '{$table}'"); 
        //标准数组
        $diamond=array_merge($diaDefault,$dia);
    	//print_r($diamond);exit;	
    	$diamond["goods_sn"]=trim($diamond["goods_sn"]);
    	$diamond["goods_sn"]=str_replace(array("\r","\n"),array('',''),$diamond['goods_sn'])?str_replace(array("\r","\n"),array('',''),$diamond['goods_sn']):'';
        $diamond["cat_id"]=strtoupper($this->strfilter($diamond["shape"]))?strtoupper($this->strfilter($diamond["shape"])):0;

        $diamond["shape"]=$diaCat[$diamond["cat_id"]]["cat_id"]?$diaCat[$diamond["cat_id"]]["cat_id"]:0;

        $diamond["original_img"]=$diamond["goods_img"]=$diamond["goods_thumb"]=$diaCat[$diamond["cat_id"]]["img"]?$diaCat[$diamond["cat_id"]]["img"]:''; //图片

        unset($diamond["cat_id"]); //不插入数据表

        $diamond["goods_weight"]=number_format(floatval($this->strfilter($diamond["carat"])),2,'.','')?number_format(floatval($this->strfilter($diamond["carat"])),2,'.',''):0.000; //钻石分数--重量

        $diamond["carat"]=$this->strfilter($diamond["carat"])?$this->strfilter($diamond["carat"]):0.00; //钻石重量

        $diamond["cert"]=$this->strfilter2($diamond["cert"]); //证书
        $diamond["cert"]=trim($diamond["cert"])?trim($diamond["cert"]):'';

        $diamond["depth_lv"]=$this->strfilter($diamond["depth"])?$this->strfilter($diamond["depth"]):''; //台深百分比

        $diamond["table_lv"]=$this->strfilter($diamond["table"])?$this->strfilter($diamond["table"]):''; //台面百分比

        $diamond["symmetry"]=$this->strfilter($diamond["sym"])?$this->strfilter($diamond["sym"]):''; //对称性 

        $diamond["cert_id"]=$this->strfilter($diamond["cert_id"])?$this->strfilter($diamond["cert_id"]):''; //证书编号 	

        $diamond["color"]=str_replace(array('+'),array(''),$diamond["color"])?str_replace(array('+'),array(''),$diamond["color"]):''; //颜色  去掉+

        if($diamond["shape"]==1){
            $diamond["cut"]=$this->strfilter($diamond["cut"])?$this->strfilter($diamond["cut"]):''; //切工  去掉+
            if(strtoupper($diamond["cut"])=='F' || strtoupper($diamond["cut"])=='FR' || strtoupper($diamond["cut"])=='FAIR')
                return false;
        }else{
            $diamond["cut"]=""; //  异形钻去掉切工
        }

        $diamond["polish"]=$this->strfilter($diamond["po"])?$this->strfilter($diamond["po"]):''; //抛光度

        $diamond["clarity"]=$this->strfilter($diamond["clarity"])?$this->strfilter($diamond["clarity"]):''; //净度  去掉+

        $diamond["fluorescence"]=$this->strfilter($diamond["fl"])?$this->strfilter($diamond["fl"]):''; //荧光度

        $diamond["girdle"]=$this->strfilter($diamond["diam"])?$this->strfilter($diamond["diam"]):'';

        $diamond["culet"]=$this->strfilter($diamond["diam"])?$this->strfilter($diamond["diam"]):'';

        $diamond["chengben_jia"]=$this->strfilter($diamond["chengbenjia"])?$this->strfilter($diamond["chengbenjia"]):0.00;

        $diamond["xianggangjia"]=$this->strfilter(@$diamond["xianggangjia"])?$this->strfilter(@$diamond["xianggangjia"]):0.00;

        $diamond["guojibaojia"]=$this->strfilter(@$diamond["guojibaojia"])?$this->strfilter(@$diamond["guojibaojia"]):0.00; //国际报价

        $diamond["cts"]=$this->strfilter(@$diamond["cts"])?$this->strfilter(@$diamond["cts"]):0.00; //每克拉价

        $diamond["us_price_source"]=$this->strfilter(@$diamond["us_price_source"])?$this->strfilter(@$diamond["us_price_source"]):0.00; //美元价

        $diamond["source_discount"]=$this->strfilter(@$diamond["source_discount"])?$this->strfilter(@$diamond["source_discount"]):0.00; //源折扣

        unset($diamond["diam"]); //不插入数据表

        $diamond["warehouse"]=strtoupper($diamond["warehouse"])?strtoupper($diamond["warehouse"]):''; //库房

        $diamond["gemx_zhengshu"]=$diamond["gemx_zhengshu"]?$diamond["gemx_zhengshu"]:''; //gemx证书

        $diamond["booking_man"]=0;  //预售人id

        $diamond["booking_time"]='00-00-00 00:00:00';  //预售时间

        $diamond["add_time"]=date("Y-m-d H:i:s");  //添加时间

        $diamond["kuan_sn"]=(isset($diamond["kuan_sn"]) && !empty($diamond["kuan_sn"]))?$diamond["kuan_sn"]:'';  //天生一对

        $diamond["from_ad"] = $from_ad;  //来源 

        $diamond["status"]='1';  //1上架，2下架

        $diamond["good_type"]=isset($diamond["good_type"])?$diamond["good_type"]:'2';  //1现货，2期货

        $diamond["mo_sn"]=isset($diamond["mo_sn"])?$diamond["mo_sn"]:'';  //模号

        $diamond["goods_name"]=$diamond["carat"]."克拉/ct ".$diamond["clarity"]."净度 ".$diamond["color"]."颜色 ".$diamond["cut"]."切工"?$diamond["carat"]."克拉/ct ".$diamond["clarity"]."净度 ".$diamond["color"]."颜色 ".$diamond["cut"]."切工":''; //名称

        //荧光数值替换
        $diamond["fluorescence"]=updat_fluor($diamond["fluorescence"])?updat_fluor($diamond["fluorescence"]):'';

        //获取分段加价率 
        $zhekou=get_zhekou($diamond["carat"],"COM",$diamond["cert"],$from_ad,$diamond["good_type"]);
        if($diamond["chengbenjia"]>0)
        {
            /*默认售价=成本价*加价率（期货1.5现货1.44）*/
            $diamond["shop_price"]=round($diamond["chengbenjia"]*$zhekou)?round($diamond["chengbenjia"]*$zhekou):0.00;

            $diamond["market_price"]=round($diamond["shop_price"]*MARKET_RATE)?round($diamond["shop_price"]*MARKET_RATE):0.00; //市场价格 carat

            if($diamond["carat"]>=0.6)
            {
                $diamond["member_price"]=round($diamond["shop_price"]*MEMBER_RATE_60)?round($diamond["shop_price"]*MEMBER_RATE_60):0.00;
            }
            else
            {
                $diamond["member_price"]=round($diamond["shop_price"]*MEMBER_RATE)?round($diamond["shop_price"]*MEMBER_RATE):0.00; //商店价格
            }

            $diamond["chengben"]=$diamond["chengbenjia"]?$diamond["chengbenjia"]:0.00;
            $diamond['is_active']=(isset($diamond['is_active'])&&empty($diamond['is_active']))?1:1;
            if($diamond['is_active']==2)
            {
                $diamond["shop_price"]=$diamond["active_shop_price"]?$diamond["active_shop_price"]:0.00;
            }

            //插入goods表  插入goods_attr表 
            $diamond_key="'".implode("','",array_keys($diamond))."'";
            $diamond_val="'".implode("','",array_values($diamond))."'";
            $keys=array('goods_id', 'goods_sn', 'goods_name', 'goods_number', 'from_ad', 'good_type', 'market_price', 'shop_price', 'member_price', 'chengben_jia', 'carat', 'clarity', 'cut', 'color', 'shape', 'depth_lv', 'table_lv', 'symmetry', 'polish', 'fluorescence', 'warehouse', 'guojibaojia', 'cts', 'us_price_source', 'source_discount', 'cert', 'cert_id', 'gemx_zhengshu', 'status', 'add_time', 'is_active', 'kuan_sn', 'mo_sn','img');
            $d=array();
            foreach($diamond as $k=>$v){
                if(in_array($k,$keys)){
                       $d[$k]=$v;
                }
            }        
            return $d;
        }
        else
        {
            return false;
        }
    }

    //分批导出
    function getSelect($arr,$drop=5){
        
        $d=array();
        $i=0;
        foreach($arr as $k=>$v){
            $a=$i/$drop;
            $b=$i%$drop;
            $d[$a][$b]=$v;
            $i++;
        }
        return $d;
    }

    //分批插入
    function adddiaDiamond($arr,$table){
        foreach($arr as $j=>$l){
            $key=implode("`,`",array_keys($l));
            $val[]='("'.implode('","',$l).'")';
        }
        $sql="REPLACE INTO `app_diamond_".$table."` (`".$key."`) VALUES ".implode(',',$val)."";
        try {
            $GLOBALS['db']->query($sql);
        } catch (Exception $e) {
           $this->writeLog($e->getMessage());
        }
        return;
    }

    /*[#YYB+ 如裸钻表中有重复GIA号	#]*/
    function selectExists($cert_id)
    {
        $sql="SELECT `cert_id` FROM `diamond_info` WHERE `cert_id`='$cert_id'";
        return $GLOBALS['db_dia']->getOne($sql);
    }

    /*[#YYB+ 如裸钻表中有重复GIA号	#]*/
    function selectExistsTable($cert_id,$table)
    {
        $sql="SELECT `cert_id` FROM `app_diamond_".$table."` WHERE `cert_id`='$cert_id'";
        return $GLOBALS['db']->getOne($sql);
    }

    /*[#YYB+ 删除现有钻 #]*/
    function deletedia($source="",$warehouse="",$goodsId=array(),$diacat="all")
    {
        // 将裸钻状态修改为 回收站
        $diarr=array(
                        "roundness"=>"73", //圆形cat
                        "polygon"=>"74,75,76,77,78,79,84,131,132,133,134,135", //多边形cat
                        "all"=>"0,73,74,75,76,77,78,79,84,131,132,133,134,135");
        $where="shape IN(".$diarr[$diacat].")";
        if(!empty($source))
            $where.=" AND from_ad='20'";
        if(!empty($warehouse))
            $where.=" AND warehouse='$warehouse'";
        if(is_array($goodsId)&&count($goodsId)>0)
        {
            $where.=" AND goods_id IN(".implode(",",$goodsId).")";
        }
        
        $sql="UPDATE diamond_info SET is_delete=1 WHERE $where";
        $GLOBALS['db']->query($sql);
        //获得回收站的裸钻id
        while(1)
        {
            $sql="SELECT goods_id FROM diamond_info WHERE $where AND is_delete=1 limit 200";
            $goodsId=$GLOBALS['db']->getAll($sql);
            if($goodsId==null)
            {
                break;
            }
            if(is_array($goodsId)&&count($goodsId)>0)
            {
                $gid=implode(",",$goodsId);
                delete_goods1($gid);
            }
        }
        
        return $goodsId;
    }
    /*按来源删除期货钻石*/
    function deletedia1($source,$warehouse='')
    {
        if(empty($source))
            return false;
        if(is_array($source))
        {
            $source=implode("','",$source);
        }
        $where="";
        if($warehouse){
            $where .=" AND warehouse = '{$warehouse}'";
        }
        $sql="DELETE FROM diamond_info WHERE from_ad in('".$source."')".$where;
        return $GLOBALS['db']->query($sql);
    }
    /*[#YYB+ 重新整理数据 #]*/
    function updatedia($gid)
    {
        if(is_array($gid))
        {
            $sql="SELECT COUNT(*) as count FROM diamond_info WHERE goods_id IN(".implode(",",$gid).") AND is_delete=0";
            $count=$GLOBALS['db']->getOne($sql);
            $step=10;
            for($i=0;$i<=$count;$i+=$step)
            {
                //echo $i.","; exit;
                rebuild_dia1($i,$step,$gid);
            }
        }
    }
    /*[#YYB+ 字条串过滤 #]*/
    function strfilter($str)
    {
        $str=str_replace('+',"",$str);
        $str=str_replace('-',"",$str);
        $str=str_replace('"',"",$str);
        $str=str_replace(' ',"",$str);
        $str=iconv('GB2312','UTF-8',$str);
        return $str;
    }
    /*[#YYB+ 字条串过滤 #]*/
    function strfilter2($str)
    {
        $str=str_replace('+',"",$str);
        $str=str_replace('"',"",$str);
        $str=str_replace(' ',"",$str);
        $str=iconv('GB2312','UTF-8',$str);
        return $str;
    }

    /**
     * 新版通用过滤规则
     * 2015/12/15 BOSS-942
     * @param  [type] $carat    [克重]
     * @param  [type] $color    [颜色]
     * @param  [type] $clarity  [净度]
     * @param  [type] $cert     [证书类型]
     * @param  [type] $cert_id  [证书号]
     * @param  [boolean] $is_futures [是否期货]
     * @return [int]           [1 、0]
     */
    function diaCommonFilter($carat, $color, $clarity, $cert, $cert_id, $is_futures)
    {
        //允许的值
        $Color_V=array("D", "E", "F", "G", "H", "I", "J", "K");
        $Clarity_V=array("FL", 
                        "IF",
                        "VVS1",
                        "VVS2",
                        "VS1",
                        "VS2",
                        "SI1"
                        );
        // 期货无SI2
        if (!$is_futures) {
            $Clarity_V[] = "SI2";
        }
        if($carat>=0.3 
            && in_array($color,$Color_V) 
            && in_array($clarity,$Clarity_V) 
            && $cert=="GIA" 
            && strlen($cert_id)>0)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    /*[#YYB+ 过滤钻 #]*/
    function diafilter($carat,$color,$clarity,$cut,$polish,$symmetry,$fluo,$cert,$cert_id)
    {
        //允许的值
        $Color_V=array(
                        "D",
                        "E",
                        "F",
                        "G",
                        "H",
                        "I",
                        "J",
                        );
        $Clarity_V=array(
                        "IF",
                        "VVS1",
                        "VVS2",
                        "VS1",
                        "VS2",
                        "SI1",
                        "SI2");
        $Cut_V=array(
                        "EX",
                        "VG",
                        "G");
        $Polish_V=array(
                        "EX",
                        "VG",
                        "G");
        $Symmetry_V=array(
                        "EX",
                        "VG",
                        "G");
        $Fluo_V=array(
                        "N",
                        "F",
                        "M",
                        "S");
//		if(!in_array($color,$Color_V)){
//			echo $cert_id."<br>";
//		}
        if($carat>=0.3&&in_array($color,$Color_V)&&in_array($clarity,$Clarity_V)&&in_array($cut,$Cut_V)&&in_array($polish,$Polish_V)&&in_array($symmetry,$Symmetry_V)&&in_array($fluo,$Fluo_V)&&$cert=="GIA"&&strlen($cert_id)>4)
        {
            return 1;
        }
        else
            return 0;
    }
    /*[#YYB+ 过滤钻 除去异形 切工 #]*/
    function diafilter_yixing($carat,$color,$clarity,$polish,$symmetry,$fluo,$cert,$cert_id)
    {
        //允许的值
        $Color_V=array(
                        "D",
                        "E",
                        "F",
                        "G",
                        "H",
                        "I",
                        "J",
                        );
        $Clarity_V=array(
                        "IF",
                        "VVS1",
                        "VVS2",
                        "VS1",
                        "VS2",
                        "SI1",
                        "SI2");
        $Polish_V=array(
                        "EX",
                        "VG",
                        "G");
        $Symmetry_V=array(
                        "EX",
                        "VG",
                        "G");
        $Fluo_V=array(
                        "N",
                        "F",
                        "M",
                        "S");
//		if(!in_array($color,$Color_V)){
//			echo $cert_id."<br>";
//		}
        if($carat>=0.3&&in_array($color,$Color_V)&&in_array($clarity,$Clarity_V)&&in_array($polish,$Polish_V)&&in_array($symmetry,$Symmetry_V)&&in_array($fluo,$Fluo_V)&&$cert=="GIA"&&strlen($cert_id)>4)
        {
            return 1;
        }
        else
            return 0;
    }
    /*[#YYB+ 过滤钻 #]*/
    function _diafilter($carat,$color,$clarity,$cut,$polish,$symmetry,$fluo,$cert,$cert_id)
    {
        //允许的值
        $Color_V=array(
                        "D",
                        "E",
                        "F",
                        "G",
                        "H",
                        "I",
                        "J",
                        "K");
        $Clarity_V=array(
                        "IF",
                        "VVS1",
                        "VVS2",
                        "VS1",
                        "VS2",
                        "SI1");
        $Cut_V=array(
                        "EX",
                        "VG",
                        "G");
        $Polish_V=array(
                        "EX",
                        "VG",
                        "G");
        $Symmetry_V=array(
                        "EX",
                        "VG",
                        "G");
        $Fluo_V=array(
                        "N",
                        "F",
                        "M",
                        "S");
        $level=1;
        if($carat>=0.3){
            $level++;
        }else{
            return $level;//1
        }
        if(in_array($color,$Color_V)){
            $level++;
        }else{
            return $level;//2
        }
        if(in_array($clarity,$Clarity_V)){
            $level++;
        }else{
            return $level;//3
        }
        if(in_array($cut,$Cut_V)){
            $level++;
        }else{
            return $level;//4
        }
        if(in_array($polish,$Polish_V)){
            $level++;
        }else{
            return $level;//5
        }
        if(in_array($symmetry,$Symmetry_V)){
            $level++;
        }else{
            return $level;//6
        }
        if(in_array($fluo,$Fluo_V)){
            $level++;
        }else{
            return $level;//7
        }
        if($cert=="GIA"){
            $level++;
        }else{
            return $level;//8
        }
        if(strlen($cert_id)>4){
            $level++;
        }else{
            return $level;//9
        }
        return $level;
    }
    /*[#YYB+ 删钻过滤掉的钻
			5分一个重量段 相同重量段 颜色 净度
			最低折扣的要删除
	#]*/
    function lastDeletedia($source)
    {
        if(empty($source))
            return false;
             //取重量最大的值
        $sql="SELECT carat FROM diamond_info WHERE from_ad='$source' ORDER BY carat DESC LIMIT 1";
        $maxCarat=$GLOBALS['db']->getOne($sql);
        $maxCarat=$maxCarat*100;
        $i=35;
        while($i<=$maxCarat)
        {
            sleep(1);
            $sql="SELECT goods_id,COUNT(*) FROM diamond_info WHERE from_ad='$source' AND carat>".(($i-5)/100)." AND carat<=".($i/100)." GROUP BY color,clarity HAVING COUNT(*)>1 ORDER BY source_discount ASC";
            $res=$GLOBALS['db']->getAll($sql);
            if($res)
            {
                foreach($res as $row)
                {
                    $goodsId[]=$row['goods_id'];
                }
                $this->deletedia($source,"",$goodsId);
                $goodsId=array();
            }
            if($i==$maxCarat)
                break;
            $i=($i+5)>$maxCarat?$maxCarat:$i+5;
        }
        return;
    }
    /*[#YYB+ 如裸钻表中有重复GIA号，将其删除	#]*/
    function deleteExists($cert_id,$type,$cert='GIA',$table="diamond")
    {
        if($type==0)
        {
            $sql="SELECT cert_id FROM ecs_".$table." WHERE cert='$cert' AND cert_id='$cert_id'";
            return $GLOBALS['db_dia']->getOne($sql);
        }
        else
        {
            $sql="SELECT goods_id FROM diamond_info WHERE cert='$cert' AND cert_id='$cert_id'";
            $goods_id=$GLOBALS['db']->getOne($sql);
            if($goods_id)
                $this->deletedia("","",array(
                                $goods_id));
        }
    }
	/**
	 *
	 * 只接收证书类型可识别的钻
	 */
	function certAllow(){
        //$sql="SELECT cert From diamond_info_certtype where enable=1;";
        //$res=$GLOBALS['db']->getCol($sql);
        $res=array("AGL","EGL","GIA","IGI","NGTC","HRD-D");
        if(empty($res)){
            return array('kela');
        }else{
            return $res;
        }
	}
}

function rebuild_dia1($from,$step,$gid)
{
    if(is_array($gid))
    {
        $sql="SELECT * FROM diamond_info WHERE goods_id IN(".implode(",",$gid).") AND is_delete=0 limit $from, $step";
        $goods_list=$GLOBALS['db']->getAll($sql);
        /*======================= 附加属性映射表 ==============================*/
        $field_map=array(
                        173=>'carat',
                        174=>'clarity',
                        175=>'color',
                        176=>'cut',
                        177=>'depth',
                        178=>'table',
                        179=>'symmetry',
                        180=>'polish',
                        181=>'girdle',
                        182=>'culet',
                        183=>'fluorescence',
                        184=>'cert',
                        185=>'cert_id',
                        343=>'chengbenjia');
        /*======================= 附加属性映射表 ==============================*/
        foreach($goods_list as $i=>$goods)
        {
            // 去掉没用的 值
            unset($goods_list[$i]['goods_link_key']);
            unset($goods_list[$i]['is_gift_love']);
            unset($goods_list[$i]['is_gift_friend']);
            unset($goods_list[$i]['is_gift_family']);
            unset($goods_list[$i]['is_gift_bussiness']);
            unset($goods_list[$i]['is_week']);
            unset($goods_list[$i]['is_booking']);
            unset($goods_list[$i]['present']);
            unset($goods_list[$i]['processing_fee']);
            unset($goods_list[$i]['gold_weight']);
            unset($goods_list[$i]['booking_time']);
            unset($goods_list[$i]['comment_count']);
            unset($goods_list[$i]['ori_goods_sn']);
            unset($goods_list[$i]['is_batch']);
            unset($goods_list[$i]['']);
            unset($goods_list[$i]['ocomment_count']);
            unset($goods_list[$i]['comment_count']);
            unset($goods_list[$i]['purpose']);
            $sql="INSERT INTO diamond_info(`".implode("`,`",array_keys($goods_list[$i]))."`) VALUES ('".implode("','",array_values($goods_list[$i]))."')";
            $GLOBALS['db']->query($sql);
            unset($goods_list[$i]);
        }
    }
}
/**
 * 从回收站删除多个商品
 * @param   mix     $goods_id   商品id列表：可以逗号格开，也可以是数组
 * @return  void
 */
function delete_goods1($goods_id)
{
    if(empty($goods_id))
    {
        return false;
    }
    $ingoodsid=db_create_in1($goods_id);
    /* 取得有效商品id */
    $sql="SELECT DISTINCT goods_id FROM diamond_info"." WHERE goods_id ".$ingoodsid." AND is_delete = 1";
    $goods_id=$GLOBALS['db']->getAll($sql);
    if(empty($goods_id))
    {
        return;
    }
    $sql="DELETE FROM diamond_info"." WHERE goods_id ".$ingoodsid;
    $GLOBALS['db']->query($sql);
    /* 清除缓存 */
    clear_tpl_files1();
}
/**
 * 清除指定后缀的模板缓存或编译文件
 *
 * @access  public
 * @param  bool       $is_cache  是否清除缓存还是清出编译文件
 * @param  string     $ext       文件后缀
 *
 * @return int        返回清除的文件个数
 */
function clear_tpl_files1($is_cache=true,$ext='')
{
    $dirs=array();
    if($is_cache)
    {
        $dirs[]=ROOT_PATH.'templates/caches/';
    }
    else
    {
        $dirs[]=ROOT_PATH.'templates/compiled/';
        $dirs[]=ROOT_PATH.'templates/compiled/admin/';
    }
    $str_len=strlen($ext);
    $count=0;
    foreach($dirs as $dir)
    {
        $folder=@opendir($dir);
        if($folder==false)
        {
            continue;
        }
        while($file=readdir($folder))
        {
            if($file=='.'||$file=='..'||$file=='index.htm'||$file=='index.html')
            {
                continue;
            }
            if(is_file($dir.$file))
            {
                /* 如果有后缀判断后缀是否匹配 */
                if($str_len>0)
                {
                    $ext_str=substr($file,-$str_len);
                    if($ext_str==$ext)
                    {
                        if(@unlink($dir.$file))
                        {
                            $count++;
                        }
                    }
                }
                else
                {
                    if(@unlink($dir.$file))
                    {
                        $count++;
                    }
                }
            }
        }
        closedir($folder);
    }
    return $count;
}
/**
 * 创建像这样的查询: "IN('a','b')";
 *
 * @access   public
 * @param    mix      $item_list      列表数组或字符串
 * @param    string   $field_name     字段名称
 * @author   Xuan Yan
 *
 * @return   void
 */
function db_create_in1($item_list,$field_name='')
{
    if(empty($item_list))
    {
        return $field_name." IN ('') ";
    }
    else
    {
        if(!is_array($item_list))
        {
            $item_list=explode(',',$item_list);
        }
        $item_list=array_unique($item_list);
        $item_list_tmp='';
        foreach($item_list as $item)
        {
            if($item!=='')
            {
                $item_list_tmp.=$item_list_tmp?",'$item'":"'$item'";
            }
        }
        if(empty($item_list_tmp))
        {
            return $field_name." IN ('') ";
        }
        else
        {
            return $field_name.' IN ('.$item_list_tmp.') ';
        }
    }
}
/*
 * 清空目录
 */
function cleardirfile($dir)
{
    return;

    //遍历临时文件，并写进数据库
    $handle=opendir($dir);
    while(false!==($file=readdir($handle)))
    {
        if($file!="."&&$file!=".."&&strpos($file,".log")===false)
        {
            //rename($dir."/".$file,$dir."/".$file.'.bat');
            @unlink($dir."/".$file);
            clearstatcache();
        }
    }
}
//获取加价折扣价格
function get_zhekou($carat,$warehouse,$cert,$from_ad,$good_type)
{
    global $db;
    if($good_type==''){
        $good_type='2';
    }
    $sql_jia="SELECT carat_min,carat_max,jiajialv FROM diamond_jiajialv where good_type={$good_type} and status=1 and from_ad = '".$from_ad."' and cert='".$cert."';";
    $jia=$db->getAll($sql_jia);
    $jiajialv = 0;
    if($jia){
        foreach($jia as $k => $v){
            if($carat >= $v['carat_min'] && $carat < $v['carat_max']){
                $jiajialv = $v['jiajialv'];
            }
        }
    }
    return $jiajialv;
}

//荧光数值替换
function updat_fluor($fluor)
{
    global $fluor_arr;
    foreach($fluor_arr as $key=>$val)
    {
        if(in_array(strtoupper($fluor),$val))
        {
            $fluor=$key;
            break;
        }
    }
    return $fluor;
}
//删除所有已售钻石
function delete_sale()
{
    $sql="DELETE FROM diamond_info WHERE is_on_sale=0";
    $GLOBALS['db']->query($sql);
}
//删除相同的现货钻石
function delete_goods2($cert_id)
{
    if(empty($cert_id))
    {
        return false;
    }
    $ingoodsid=db_create_in1($cert_id);
    $sql="DELETE FROM diamond_info"." WHERE cert_id ".$ingoodsid;
    $GLOBALS['db']->query($sql);
    /* 清除缓存 */
    clear_tpl_files1();
}
//创建上传的临时表格
function create_data($table)
{
    global $db_dia;
    //创建之前先删除表格  
    $sql="DROP TABLE IF EXISTS app_diamond_".$table.';';
    $db_dia->query($sql);
/*     $sql="CREATE TABLE IF NOT EXISTS app_diamond_".$table." (`goods_id` varchar(20) DEFAULT NULL,
`goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
`goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
`goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
`from_ad` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1=51钻，2=BDD',
`good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型:1=现货，2=期货',
`market_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '市场价',
`shop_price` DECIMAL( 10, 2 ) NULL COMMENT  '销售价',
`member_price` DECIMAL( 10, 2 ) NULL COMMENT  '会员价',
`chengben_jia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本价',
`carat` double NOT NULL DEFAULT '0.00' COMMENT '石重',
`clarity` varchar(40) NOT NULL DEFAULT '' COMMENT '净度',
`cut` varchar(10) NOT NULL DEFAULT '' COMMENT '切工',
`color` varchar(10) NOT NULL DEFAULT '' COMMENT '颜色',
`shape` tinyint(3) DEFAULT NULL COMMENT '形状',
`depth_lv` varchar(10) NOT NULL DEFAULT '0' COMMENT '台深',
`table_lv` varchar(10) NOT NULL DEFAULT '0' COMMENT '台宽',
`symmetry` varchar(10) NOT NULL DEFAULT '' COMMENT '对称',
`polish` varchar(10) NOT NULL DEFAULT ''  COMMENT '抛光',
`fluorescence` varchar(10) NOT NULL DEFAULT '' COMMENT '荧光',
`warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
`guojibaojia` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' COMMENT '国际报价',
`cts` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' COMMENT '每克拉价',
`us_price_source` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' COMMENT '美元价',
`source_discount` DECIMAL( 10, 4 ) NOT NULL DEFAULT '0.0000' COMMENT '源折扣',
`cert` varchar(10) NOT NULL DEFAULT '' COMMENT '证书号类型',
`cert_id` varchar(30) NOT NULL DEFAULT '' COMMENT '证书号',
`gemx_zhengshu` varchar(10) DEFAULT NULL COMMENT 'gemx证书号',
`status` tinyint(1) unsigned NOT NULL DEFAULT '1',
`add_time` datetime DEFAULT NULL COMMENT '添加时间',
`is_active` tinyint(1) NULL DEFAULT '1',
`kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
`mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
 `tran_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '鼎捷同步时间',
 UNIQUE KEY `cert` (`cert`,`cert_id`))
ENGINE=MyISAM DEFAULT CHARSET=utf8"; */
    
    $sql = "CREATE TABLE app_diamond_".$table." as select * from diamond_info where 1=0";
    $db_dia->query($sql);
}
//数据导入
function update_data($source,$file,$obj,$table="",$warehouse="")
{
    return import_data($source, $table, $warehouse, $obj);
    
    /*  Will Remove these codes when everything is OK
    global $db_dia,$from_ad_ids;

    if($table=="")
    {
        $table=$source;
    }
    //删除cat_id=0的钻石
    $db_dia->query('DELETE FROM app_diamond_'.$table.' WHERE shape=0');
    //把抓取过来的数据之间写入到diamond_info表里边
    exec('/usr/local/mysql/bin/mysqldump --set-gtid-purged=OFF -ucuteman -p"QW@W#RSS33#E#" --default-character-set=utf8 -h192.168.1.59 -P 3306 -t --compact front app_diamond_'.$table." > ".$file);
    if(@file_exists($file))
    {
        $content=file_get_contents($file);
        $content=str_replace("app_diamond_".$table,"diamond_info",$content);
        $fp=fopen($file,"w");
        fwrite($fp,$content);
        fclose($fp);
        //删钻
        $obj->deletedia1($from_ad_ids[$source],$warehouse);
        exec('/usr/local/mysql/bin/mysql -ucuteman -p"QW@W#RSS33#E#" --default-character-set=utf8 -h192.168.1.59 -P 3306 front < '.$file);
        //删除数据
        $sql="DROP TABLE app_diamond_".$table;
        $db_dia->query($sql);
        //删除导出来的sql
        @unlink($file);
        $db_dia->query("REPLACE into `diamond_info_all`(SELECT * from `diamond_info`);");
        //update_jd();
    }*/
}

/**
 * [import_data]
 * @param  [type] $source    
 * @param  string $table     
 * @param  string $warehouse 
 */
function import_data($source, $table="", $warehouse="", $obj)
{
    global $db_dia, $from_ad_ids;

    if($table=="")
    {
        $table = $source;
    }

    //删除cat_id=0的钻石
    $db_dia->query('DELETE FROM app_diamond_'.$table.' WHERE shape=0');

    $c_sql = "SELECT count(1) AS ttl FROM app_diamond_".$table;
    $table_ttl = $db_dia->getOne($c_sql);
    $obj->writeLog("table ". $table. " ttl:". $table_ttl);
    
    if (empty($table_ttl) || $table_ttl <= 0) {
        $obj->writeLog("WARNNING!!! No valid data, exit.");
        return;
    }

    //旧数据
    $old_dia_list = array();
    $old_dia_list = indexArray($db_dia->getAll("SELECT * FROM diamond_info WHERE from_ad = '".$from_ad_ids[$source]."'"), 'cert_id');
    $adding_list = [];
    $updating_list = [];

    //读取新数据
    $new_dia_list = $db_dia->getAll("SELECT * FROM app_diamond_".$table);
    foreach ($new_dia_list as $new_row) {
        //荧光 
        if(!empty($new_row['fluorescence']) && in_array($new_row['fluorescence'],array('V','VS','V.STRONG'))){
            $new_row['fluorescence'] = 'S';
        }   
        // 已有数据
        $old_item = array();
        $old_exists = false;
        if (array_key_exists($new_row['cert_id'], $old_dia_list)) {   
            $old_item = $old_dia_list[$new_row['cert_id']];
            $old_exists = true;
        } else {
            $old_item = $db_dia->getRow("select * from diamond_info where cert_id='".$new_row['cert_id']."'");
            //只允许其他供应商覆盖福隆的，不允许福隆覆盖其他供应商的信息
            if(!empty($old_item) && $table == 'fulong') continue;
        }
        
        if (!empty($old_item)) {
            // 如果这个钻是在指定的from_ad列表中，则不做更新
            if (!in_array($old_item['from_ad'], array('CS','JP','RA','KI', 'SUNRISE','OT','Sunrise','FD'))) {
                // 排除add_time,modifyDate,mode,status,tran_time
                //$stacks = array($old_item['goods_id'],$old_item['add_time'], $old_item['tran_time'] );
                $stacks = array($old_item['goods_id'],$old_item['add_time']);
                
                unset($old_item['goods_id']);
                unset($old_item['add_time']);
                //unset($old_item['tran_time']);
                //unset($old_item['mode']);
                //unset($old_item['modifyDate']);
                
                unset($new_row['goods_id']);
                unset($new_row['add_time']);
                //unset($new_row['tran_time']);

                // 如果数据有发生变化
                $all_fileds = array_keys($new_row);
                $changed = false;
                foreach($all_fileds as $f) {
                    if ($new_row[$f] != $old_item[$f]) {
                        $changed = true;
                        break;
                    } 
                }
                if ($changed) {               
                    $new_row['goods_id'] = $stacks[0];
                    $new_row['add_time'] = $stacks[1];
                    //$new_row['tran_time'] = $stacks[2];
                    $updating_list[] = $new_row;
                }
            }
            // 从待删除列表中去除    
            if ($old_exists) unset($old_dia_list[$new_row['cert_id']]); 
        } else {
            $adding_list[] = $new_row;
        }
    }
    $now = date('Y-m-d H:i:s',time());
    //echo '<pre>';
    //print_r($adding_list);die;
    //TODO: 将 adding_list 新增到库【同时设置modifyDate为当前时间, mode = 1】
    adding_list($adding_list, $now);
    //TODO: 将 updating_list 更新到库【同时设置modifyDate为当前时间, mode = 2】
    updating_list($updating_list);
    //TODO: 将 old_dia_list 更新 status=2(即下架) 到库【同时设置modifyDate为当前时间, mode = 3】
    old_dia_list($old_dia_list);

    //删钻
    //$obj->deletedia1($from_ad_ids[$source], $warehouse);
    //$obj->writeLog("log: delete diamonds from diamond_info.");

    //把抓取过来的数据之间写入到diamond_info表里边
    //echo 'REPLACE INTO diamond_info SELECT * FROM app_diamond_'.$table;
    //$db_dia->query('REPLACE INTO diamond_info SELECT * FROM app_diamond_'.$table);
    //$obj->writeLog("log: replace into diamond_info");
    
    //删除表格
    // $db_dia->query("DROP TABLE app_diamond_".$table);

    //同步到diamond_info_all
    //$db_dia->query("REPLACE INTO `diamond_info_all`(SELECT * FROM `diamond_info`);");
	
    $obj->updateFetchTime();
	
	notify($from_ad_ids[$source]);	
}

function notify($from_ad) {
	$job_server_list = array(
        ['host'=> '192.168.1.58', 'port' => 4730],
        //['host'=> '192.168.1.61', 'port' => 4730]
	);

	$gearmanc = new GearmanClient();
	foreach ($job_server_list as $serv) {
			$gearmanc->addServer($serv['host'], $serv['port']);
	}

	$gearmanc->doBackground('task', json_encode(array('event'=>'dia_upserted', 'from_ad' => $from_ad, 'sys_scope'=>'boss', 'msgId' => time(), 'timestamp'=>time())));
}

function check_xml_by_xmlReader($url)
{
    $arr=array();
    $reader=new XMLReader();
    $reader->open($url);
    $i=0;
    while($reader->read())
    {
        if($reader->nodeType==XMLReader::TEXT)
        {
            $arr[$i][$name]=$reader->value;
        }
        elseif($reader->nodeType==XMLReader::ELEMENT)
        {
            $name=$reader->name;
        }
        if(($reader->nodeType==XMLReader::END_ELEMENT)&&($reader->name=='STONEDETAILS'))
        {
            $i++;
            mysql_ping();
        }
    }
    $reader->close();
    return $arr;
}

function indexArray($array, $key, $groups = []) {
    $result = [];
    $groups = (array)$groups;
    foreach ($array as $element) {
        $lastArray = &$result;
        foreach ($groups as $group) {
            $value = getValue($element, $group);
            if (!array_key_exists($value, $lastArray)) {
                $lastArray[$value] = [];
            }
            $lastArray = &$lastArray[$value];
        }
        if ($key === null) {
            if (!empty($groups)) {
                $lastArray[] = $element;
            }
        } else {
            $value = getValue($element, $key);
            if ($value !== null) {
                if (is_float($value)) {
                    $value = (string) $value;
                }
                $lastArray[$value] = $element;
            }
        }
        unset($lastArray);
    }
    return $result;
}

function array_equal($a1, $a2) {
    return !array_diff($a1, $a2) && !array_diff($a2, $a1);
}

function getValue($array, $key, $default = null) {
    if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array)) ) {
        return $array[$key];
    }
    
    return $default;
}

function adding_list($adding_list=array(), $now) {
    if(!empty($adding_list)) {
        $sql_list = array();
        foreach ($adding_list as $k => $val) {
            $adding_list[$k]['add_time'] = $now;
            if($adding_list[$k]['cert_id'] != ''){
                $sql_list[] = "INSERT INTO diamond_info(`".implode("`,`",array_keys($adding_list[$k]))."`) VALUES ('".implode("','",array_values($adding_list[$k]))."')";

                if (count($sql_list) >= 10) {
                    try{ 
                       $GLOBALS['db']->query(implode(' ;', $sql_list));
                       $sql_list = array();
                    }catch(PDOException $e){
                       //$err_str =json_encode($e).implode(' ;', $sql_list);
                    }
                }
            }
        }
        if (!empty($sql_list)) {
            try{ 
                $GLOBALS['db']->query(implode(' ;', $sql_list));
            }catch(PDOException $e){
                       //$err_str =json_encode($e).implode(' ;', $sql_list);
            }                
        }   
        unset($adding_list);
    }
}

function updating_list($updating_list=array())
{
    if(!empty($updating_list)){
        $sql_list = array();
        foreach ($updating_list as $k => $val) { 
            if(!isset($val['img']))
                $val['img']='';       
            if($val['cert_id'] != '') {
                $sql_list[]="UPDATE `diamond_info` `dia` SET 
            `dia`.`goods_sn`='".$val['goods_sn']."',`dia`.`goods_name`='".$val['goods_name']."',`dia`.`goods_number`='".$val['goods_number']."',`dia`.`from_ad`='".$val['from_ad']."',`dia`.`good_type`='".$val['good_type']."',`dia`.`market_price`='".$val['market_price']."',`dia`.`shop_price`='".$val['shop_price']."',`dia`.`member_price`='".$val['member_price']."',`dia`.`chengben_jia`='".$val['chengben_jia']."',`dia`.`carat`='".$val['carat']."',`dia`.`clarity`='".$val['clarity']."',`dia`.`cut`='".$val['cut']."',`dia`.`color`='".$val['color']."',`dia`.`shape`='".$val['shape']."',`dia`.`depth_lv`='".$val['depth_lv']."',`dia`.`table_lv`='".$val['table_lv']."',`dia`.`symmetry`='".$val['symmetry']."',`dia`.`polish`='".$val['polish']."',`dia`.`fluorescence`='".$val['fluorescence']."',`dia`.`warehouse`='".$val['warehouse']."',`dia`.`guojibaojia`='".$val['guojibaojia']."',`dia`.`cts`='".$val['cts']."',`dia`.`us_price_source`='".$val['us_price_source']."',`dia`.`source_discount`='".$val['source_discount']."',`dia`.`cert`='".$val['cert']."',`dia`.`gemx_zhengshu`='".$val['gemx_zhengshu']."',`dia`.`status`=1,`dia`.`is_active`='".$val['is_active']."',`dia`.`kuan_sn`='".$val['kuan_sn']."',`dia`.`mo_sn`='".$val['mo_sn']."', `dia`.`pifajia` ='".$val['pifajia']."', `dia`.`pifajia_mode`='".$val['pifajia_mode']."',`dia`.`img`='".addslashes($val['img'])."' WHERE `dia`.`cert_id`='".$val['cert_id']."'";

                if (count($sql_list) >= 10) {
                    $GLOBALS['db']->query(implode(' ;', $sql_list));
                    $sql_list = array();
                }
            }            
        }
        if (!empty($sql_list)) {
            $GLOBALS['db']->query(implode(' ;', $sql_list));
        }
        unset($updating_list);
    }
}

function old_dia_list($old_dia_list=array())
{
   if(!empty($old_dia_list)){
        $sql_list = array();
        foreach ($old_dia_list as $k => $val) {
            //if($val['mode'] == 3) continue;  // 之前已经是删除状态，不需后续操作
            if($val['cert_id'] != '') {
                //$sql_list[]="UPDATE `diamond_info` `dia` SET 
           //`dia`.`status`=2,`dia`.`mode`=3,`dia`.`modifyDate`='".$now."' WHERE `dia`.`cert_id`='".$val['cert_id']."'";
                $sql_list[]="DELETE FROM `diamond_info` WHERE `cert_id`='".$val['cert_id']."'";
                if (count($sql_list) >= 10) {
                    $GLOBALS['db']->query(implode(' ;', $sql_list));
                    $sql_list = array();
                }
            }
        }

        if (!empty($sql_list)) {
            $GLOBALS['db']->query(implode(' ;', $sql_list));
        }
        unset($old_dia_list);
    }
}


//同步到diamond_info_all
function update_jd()
{
    $sql="UPDATE `diamond_info_all` AS `dia`,`diamond_info` AS `d` SET 
    `dia`.`goods_sn`=`d`.`goods_sn`,`dia`.`goods_name`=`d`.`goods_name`,`dia`.`goods_number`=`d`.`goods_number`,`dia`.`from_ad`=`d`.`from_ad`,`dia`.`good_type`=`d`.`good_type`,`dia`.`market_price`=`d`.`market_price`,`dia`.`shop_price`=`d`.`shop_price`,`dia`.`member_price`=`d`.`member_price`,`dia`.`chengben_jia`=`d`.`chengben_jia`,`dia`.`carat`=`d`.`carat`,`dia`.`clarity`=`d`.`clarity`,`dia`.`cut`=`d`.`cut`,`dia`.`color`=`d`.`color`,`dia`.`shape`=`d`.`shape`,`dia`.`depth_lv`=`d`.`depth_lv`,`dia`.`table_lv`=`d`.`table_lv`,`dia`.`symmetry`=`d`.`symmetry`,`dia`.`polish`=`d`.`polish`,`dia`.`fluorescence`=`d`.`fluorescence`,`dia`.`warehouse`=`d`.`warehouse`,`dia`.`guojibaojia`=`d`.`guojibaojia`,`dia`.`cts`=`d`.`cts`,`dia`.`us_price_source`=`d`.`us_price_source`,`dia`.`source_discount`=`d`.`source_discount`,`dia`.`cert`=`d`.`cert`,`dia`.`cert_id`=`d`.`cert_id`,`dia`.`gemx_zhengshu`=`d`.`gemx_zhengshu`,`dia`.`status`=`d`.`status`,`dia`.`add_time`=`d`.`add_time`,`dia`.`is_active`=`d`.`is_active`,`dia`.`kuan_sn`=`d`.`kuan_sn`,`dia`.`mo_sn`=`d`.`mo_sn` WHERE `dia`.`cert_id`=`d`.`cert_id`";
    $res=$GLOBALS['db']->query($sql);
    if($res){
        $sql="INSERT INTO `diamond_info_all`(`goods_sn`, `goods_name`, 
        `goods_number`, `from_ad`, `good_type`, `market_price`, `shop_price`, `member_price`,`chengben_jia`,`carat`,`clarity`,`cut`,`color`,`shape`,`depth_lv`,`table_lv`,`symmetry`,`polish`,`fluorescence`,`warehouse`, `guojibaojia`, `cts`, `us_price_source`, `source_discount`, `cert`, `cert_id`, `gemx_zhengshu`, `status`, `add_time`, `is_active`, `kuan_sn`, `mo_sn`) SELECT `d`.`goods_sn`, `d`.`goods_name`, 
        `d`.`goods_number`, `d`.`from_ad`, `d`.`good_type`, `d`.`market_price`, `d`.`shop_price`, 
        `d`.`member_price`, `d`.`chengben_jia`, `d`.`carat`, `d`.`clarity`, `d`.`cut`, `d`.`color`, `d`.`shape`, 
        `d`.`depth_lv`, `d`.`table_lv`, `d`.`symmetry`, `d`.`polish`, `d`.`fluorescence`, `d`.`warehouse`, 
        `d`.`guojibaojia`, `d`.`cts`, `d`.`us_price_source`, `d`.`source_discount`, `d`.`cert`, `d`.`cert_id`, 
        `d`.`gemx_zhengshu`, `d`.`status`, `d`.`add_time`, `d`.`is_active`, `d`.`kuan_sn`, `d`.`mo_sn` FROM 
        `diamond_info` AS `d` WHERE `d`.`cert_id` NOT IN(SELECT `dia`.`cert_id` FROM `diamond_info_all` as `dia`)";  
        $GLOBALS['db']->query($sql);
    }    
}
