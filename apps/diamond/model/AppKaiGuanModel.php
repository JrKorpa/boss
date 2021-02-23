<?php

/**
 *  -------------------------------------------------
 *   @file		: AppShopConfigModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-18 14:56:43
 *   @update	:
 *  -------------------------------------------------
 */
class AppKaiGuanModel extends Model {


/*[#YYB+
-- 获取裸钻接口 配置
#]*/
public static $diaconf = array(
				/*
				 * edm
				 * */
				 "emd"=>array("server_url"	=>	"http://emd-online.com/api/",
							  "username"	=>	"apiUser",
							  "password"	=>	"0bc04da51ahdfbc21aadfs25djg8112af6adfdf9c8a367",
				 			  "source"		=>	"emd",
							  "name"		=>	"EMD",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>0,//0=开启 1=关闭
							  "isinform"	=>	0 // 1=显示在下载列表
							  ),
				 /* KGK(凯吉凯) */
				/*
				 * kgk 已经重新谈
				 *
				 * */
				 "kgk"=>array("server_url"	=>	"http://www.kgk.cc/websvckgkccclient/Service.asmx?WSDL",
							  "username"	=>	"lihai@china.com",
							  "password"	=>	"susan111",
				 			  "source"		=>	"kgk",
							  "name"		=>	"凯吉凯",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>0,//0=开启 1=关闭
							  "isinform"	=>	0 // 1=显示在下载列表
							  ),
				 /* diarough */
				/*
				 * */
				 "fiveonezuan"=>array("server_url"	=>	"",
							  "username"	=>	"lihai@china.com",
							  "password"	=>	"susan111",
				 			  "source"		=>	"fiveonezuan",
							  "name"		=>	"fiveonezuan",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>0,//0=开启 1=关闭
							  "isinform"	=>	0 // 1=显示在下载列表
							  ),
				/*
				 * */
				 "gd"=>array("server_url"	=>	"http://webservice.godhanigems.in/stock/stock.asmx/LiveStock",
							  "username"	=>	"pinank",
							  "password"	=>	"susan111",
				 			  "source"		=>	"gd",
							  "name"		=>	"gd",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>0,//0=开启 1=关闭
							  "isinform"	=>	0 // 1=显示在下载列表
							  ),
				 /* diarough */
				 /*
				  * 戴瑞富
				  */
				  "diarough"=>array("server_url"=>"http://94.200.156.148/DiaroughService/Diarough.svc/basic?wsdl",
							  "uid"	=>	"10608",
				 			  "source"		=>	"diarough",
							  "name"		=>	"diarough",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>0,//0=开启 1=关闭
							  "isinform"	=>	0 // 1=显示在下载列表
							  ),
				 /*  vir */
				 /*
				  * 虚拟货品
				  */
				  "vir"=>array("server_url"=>"",
				 			  "source"		=>	"vir",
							  "name"		=>	"virual",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>1,//0=开启 1=关闭
							  "isinform"	=>	1 // 1=显示在下载列表
							  ),
				/* dax(Diamdax) */
				 "dax"=>array("server_url"	=>	"https://diamdax.com/api/certificates.csv?auth_token=MGxzBdRbq3rqpq5cq2zy",
							  "username"	=>	"kela_api_test",
							  "password"	=>	"aiXaeDeehohz3Ki",
				 			  "source"		=>	"dax",
							  "name"		=>	"Diamdax",
							  "key"		    =>	"MGxzBdRbq3rqpq5cq2zy",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>1,
							  "isinform"	=>	0
							  ),
				 /* 长宁 KLXZ_admin lipengguang http://system.cheungning.com/login.aspx */
				 "cheungning"=>array(
							  "server_url"	=>	"http://gateway.cheungning.com/CN_InterFace/WebAPI.asmx?WSDL",
							  "username"	=>	"KLXZ_100166",
							  "password"	=>	"123456789",
				 			  "key"			=>	"48A283B56E0D58CF73D7D6BD997640DD",
							  "source"		=>	"cheungning",
							  "name"		=>  "长宁",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>1,
							  "isinform"	=>	0
							  ),
				  /*
				   * 英培斯
				   *
				   * */
				  "diamondbyhk"=>array(
								//"server_url"=>"http://5g.diamondbyhk.com/hkwebservice/packetlistservlet?usr=xukawang@gmail.com&pwd=Susan111",
								"server_url"=>"http://5g.diamondbyhk.com/hkwebservice/packetlistservlet?usr=shujuan@kela.cn&pwd=susan123",
								"source"		=>	"diamondbyhk",
								"name"		=>  "diamondbyhk",
								"price"		=>	array("70"=>"1.48","0"=>"1.48"),
								"lastDeletedia"=>0,
								"isinform"	=>	0
							  ),

				 /*dharamhk*/
				 "dharam"=>array(
							  "server_url"	=>	"http://serviceapi.dharamhk.com/stock_disp.asmx?WSDL",
							  "CompanyName"	=>	"Kela Diamond",
							  "password"	=>	"susan111",
							  "UniqID"	    =>	"8997",
				 			  "ActivationCode"	=>	"KelaDiam8897#Diam",
				 			  "username"	=>	"christywang11@gmail.com",
							  "source"		=>	"dharam",
							  "name"		=>  "dharamhk",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=> 0,
							  "isinform"	=>	0
							  ),

				 /*KAPU */
				 "kapu"=>array(
							  "server_url"	=>	"http://xml.kapugems.com:19090/xml_demo/get_xml",
							//  "server_url"	=>	"http://115.124.107.79:19090/xml_demo/get_xml",
							  "username"	=>	"kipad",
							  "password"	=>	"ipad",
							  "source"		=>	"kapu",
							  "name"		=>  "KAPU",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=> 0,
							  "isinform"	=>	0
							  ),
				   /*KIRAN */
				/*
				 *
				 * luofang (罗芳) 2013-12-03 17:36:10
				 * 大马家
				 * 标志:R
				 * R家
				 *	他家密码也是换了
				 *	我已经告诉王徐了
				 */
				 "kiran"=>array(
							  "server_url"	=>	"ftp://120.138.212.154/Report.csv",
							  "source"		=>	"kiran",
							  "name"		=>  "KIRAN",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=> 0,
							  "isinform"	=>	0
							  ),
				   /*LEO */
				 "leo"=>array(
							  "server_url"	=>	"http://feedcenter.net/Output/360/LSDCOHK.csv",
							  "source"		=>	"leo",
							  "name"		=>  "LEO",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=> 0,
							  "isinform"	=>	0
							  ),
				  /*HY */
				 "hy"=>array(
							  "server_url"  =>      "http://systems.srkexport.com/stk/conkey.asmx/GetDiamonds",
							  "username"	=>	"slk88",
							  "password"	=>	"kela123456",
							  "source"		=>	"hy",
							  "name"		=>  "HY",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=> 0,
							  "isinform"	=>	0
							  ),
				  /*JB*/
				  'jb'=>array(
							"server_url"	=>	"http://www.conkeydiamond.com/sozuan/inventory.php",
							  "username"	=>	"kela",
							  "password"	=>	"123123",
							  "source"		=>	"jb",
							  "name"		=>  "JB",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=> 0,
							  "isinform"	=>	0
							),
				  /*现货产品*/
				  "xianhuoall"=>array(
				                "source"		=>	"xianhuoall",
				                "name"		    =>	"xianhuoall",
								"lastDeletedia" => 0,
								"isinform"	    =>	0
								),
				 /* 总公司 */
				 "com"=>array(
							  "source"		=>	"com",
							  "name"		=>  "总公司",
							  "isinform"	=>	0
							  ),
				 /* 总公司 每日更新 */
				 "comdaily"=>array(
							  "source"		=>	"comdaily",
							  "name"		=>  "每日更新",
							  "isinform"	=>	1
							  ),
				 /* 总公司 定期更新 */
				 "comregular"=>array(
							  "source"		=>	"comregular",
							  "name"		=>  "定期更新",
							  "isinform"	=>	1
							  ),

				//其他(特殊)
				 "created"=>array(
							  "source"		=>	"created",
							  "name"		=>  "其他",
							  "isinform"	=>	0
							  ),
				"KIRAN"=>array(
							  "source"		=>	"kiran",
							  "name"		=>  "KIRAN",
							  "isinform"	=>	1
							  ),
				"LEO"=>array(
							  "source"		=>	"leo",
							  "name"		=>  "LEO",
							  "isinform"	=>	1
							  ),
				"dhl"=>array(
							  "source"		=>	"dhl",
							  "name"		=>  "dhl",
							  "isinform"	=>	1
							  ),
				"JB"=>array(
							  "source"		=>	"jb",
							  "name"		=>  "JB",
							  "isinform"	=>	1
							  ),
					/**
					 *  豪艺
					 */
/*
"HY"=>array(
							  "source"		=>	"hy",
							  "name"		=>  "HY",
							  "isinform"	=>	1
							  ),
*/				"Suashish"=>array(
							  "source"		=>	"S",
							  "name"		=>  "Suashish",
							  "isinform"	=>	1
							  ),
				"KGK"=>array(
							  "source"		=>	"kgk",
							  "name"		=>  "KGK",
							  "isinform"	=>	1
							  ),
				"Diamdax"=>array(
							  "source"		=>	"dax",
							  "name"		=>  "Diamdax",
							  "isinform"	=>	1
							  ),
					/**
					 * 卡普
					 */
				"KAPU"=>array(
							  "source"		=>	"kapu",
							  "name"		=>  "KAPU",
							  "isinform"	=>	1
							  ),
				"Diamondbyhk"=>array(
							  "source"		=>	"diamondbyhk",
							  "name"		=>  "diamondbyhk",
							  "isinform"	=>	1
							  ),
				/**
				 * HK
				 */
				 "Dharamhk"=>array(
							  "source"		=>	"dharam",
							  "name"		=>  "Dharamhk",
							  "isinform"	=>	1
							  ),
				  "A"=>array(
							  "source"		=>	"A",
							  "name"		=>  "A家",
							  "isinform"	=>	1
							  ),
				 "hongjian"=>array(
							  "source"		=>	"HJ",
							  "name"		=>  "hongjian",
							  "isinform"	=>	0
							  ),
				 //贵天钻石
				 "guitian"=>array(
							  "source"		=>	"GT",
							  "name"		=>  "guitian",
							  "isinform"	=>	1
							  ),
				 //KSP
				 "KSP"=>array(
							  "source"		=>	"KSP",
							  "name"		=>  "KSP",
							  "isinform"	=>	1
							  ),

                "Akarshexports"=>array(
                              "source"=>"akarsh",
                              "name"=>"Akarsh",
                              "isinform"=>1
                              ),
                "starrays"=>array(
                              "source"=>"starrays",
                              "name"=>"starrays",
                              "isinform"=>1
                              ),
                "Bhavani"=>array(
                              "source"=>"Bhavani",
                              "name"=>"Bhavani",
                              "isinform"=>1
                              ),
                "Ankit"=>array(
                              "source"=>"Ankit",
                              "name"=>"ANKITGEMS",
                              "isinform"=>1
                              ),
                "Laxmi"=>array(
                              "source"=>"laxmi",
                              "name"=>"Laxmi",
                              "isinform"=>1
                              ),
                "Venus"=>array(
                              "source"=>"venus",
                              "name"=>"venus",
                              "isinform"=>1
                              ),
    /**
     * 供应商WCF
     */
    'wcf'=>array(
        "server_url"=>"http://svc-api.kgirdharlal.com:8081/KGService.svc?wsdl",
        "username"	=>	"kela",
        "password"	=>	"123123",
        "source"		=>	"jb",
        "name"		=>  "JB",
        "price"		=>	array("70"=>"1.48","0"=>"1.48"),
        "lastDeletedia"=> 0,
        "isinform"	=>	0
    ),
                /**
                * fulong 福隆
                */
                "fulong"=>array(
                      "server_url"  =>  "http://www.forlongjewelry.com/APPAPI/Api.ashx?action=viplogin",
                      "username"    =>  "kela123",
                      "password"    =>  "goodluck666",
                      "source"      =>  "fulong",
                      "name"        =>  "福隆",
                      "price"       =>  array("70"=>"1.48","0"=>"1.48"),
                      "lastDeletedia"=>1,
                      "isinform"    =>  0
                      ),
				);

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'diamond_vendor';
        $this->_dataObject = array(
        	"vendor_id" => "供应商ID",
            "title" => "名称",
            "activate" => "是否激活",
            "show" => "后台是否显示开关",
            "created" => "创建时间",
            "updated" => "更新时间"
            );
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url AppShopConfigController/search
     */
    function getAllList() {
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE `show`=1 ORDER BY `vendor_id` DESC";
        $data = $this->db()->getAll($sql);
        return $data;
    }

    function hasConfig($name,$code){
        $sql = "select count(1) from `".$this->table()."` where title='".$name."' ";
        return  $this->db()->getOne($sql,array(),false);
                
    }

    //检查是否开启
    /*function hasKaiQi(){
        $sql="SELECT value FROM `".$this->table()."` WHERE code='dia_api_kai'";
        return  $this->db()->getOne($sql);        
    }*/

    //检查是否开启
    function UpdateKaiQi($kai,$guan){
    	$log = '';
        if(!empty($kai)){
            $sql="UPDATE `".$this->table()."` SET `activate`=1 WHERE `vendor_id` in (".implode(',',$kai).");";
            $this->db()->query($sql); 
            $log .= $sql;
        }
        if(!empty($guan)){
            $sql="UPDATE `".$this->table()."` SET `activate`=0 WHERE `vendor_id` in (".implode(',',$guan).");";
            $this->db()->query($sql);
            $log .= $sql;
        }
        $ret = array("success"=>'true', "log"=>$log);
        return $ret;
    }

    function KaiGuan(){

        return  self::$diaconf;
                
    }
}

?>