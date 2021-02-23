<?php
/*[#YYB+
-- 获取裸钻接口 配置
#]*/
$diaconf = array(
				 /* fiveonezuan */
				/*
				 * */
				 "2"=>array("server_url"	=>	"",
							  "username"	=>	"lihai@china.com",
							  "password"	=>	"susan111",
				 			  "source"		=>	"fiveonezuan",
							  "name"		=>	"fiveonezuan",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>0,//0=开启 1=关闭
							  "isinform"	=>	0 // 1=显示在下载列表
							  ),
                /*Venus*/
                "3"=>array(
                              "source"=>"venus",
                              "name"=>"venus",
                              "isinform"=>1
                              ),
				 /*dharam*/
				 "4"=>array(
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
				  /*
				   * 英培斯
				   * diamondbyhk
				   * */
				  "5"=>array(
								//"server_url"=>"http://5g.diamondbyhk.com/hkwebservice/packetlistservlet?usr=xukawang@gmail.com&pwd=Susan111",
								// "server_url"=>"http://5g.diamondbyhk.com/hkwebservice/packetlistservlet?usr=shujuan@kela.cn&pwd=susan123",
								"server_url"=>"http://5g.diamondbyhk.com/hkwebservice/packetlistservlet?usr=shujuan@kela.cn&pwd=kela123",
								"source"		=>	"diamondbyhk",
								"name"		=>  "kela123",//"diamondbyhk"
								"price"		=>	array("70"=>"1.48","0"=>"1.48"),
								"lastDeletedia"=>0,
								"isinform"	=>	0
							  ),
				 /* diarough */
				 /*
				  * 戴瑞富
				  */
				  "6"=>array("server_url"=>"http://94.200.156.148/DiaroughService/Diarough.svc/basic?wsdl",
							  "uid"	=>	"10608",
				 			  "source"		=>	"diarough",
							  "name"		=>	"kela123456",//diarough
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>0,//0=开启 1=关闭
							  "isinform"	=>	0 // 1=显示在下载列表
							  ),
                  /*emd*/
				 "7"=>array("server_url"	=>	"http://emd-online.com/api/",
							  "username"	=>	"apiUser",
							  "password"	=>	"0bc04da51ahdfbc21aadfs25djg8112af6adfdf9c8a367",
				 			  "source"		=>	"emd",
							  "name"		=>	"EMD",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>0,//0=开启 1=关闭
							  "isinform"	=>	0 // 1=显示在下载列表
							  ),
				/*gd
				 * */
				 "8"=>array("server_url"	=>	"http://webservice.godhanigems.in/stock/stock.asmx/LiveStock",
							  "username"	=>	"pinank",
							  "password"	=>	"susan111",
				 			  "source"		=>	"gd",
							  "name"		=>	"gd",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>0,//0=开启 1=关闭
							  "isinform"	=>	0 // 1=显示在下载列表
							  ),
				  /*JB*/
				  '9'=>array(
							"server_url"	=>	"http://www.conkeydiamond.com/sozuan/inventory.php",
							  "username"	=>	"kela",
							  "password"	=>	"123123",
							  "source"		=>	"jb",
							  "name"		=>  "JB",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=> 0,
							  "isinform"	=>	0
							),
				 /*KAPU */
				 "10"=>array(
							  "server_url"	=>	"http://xml.kapugems.com:19090/xml_demo/get_xml",
							//  "server_url"	=>	"http://115.124.107.79:19090/xml_demo/get_xml",
							  "username"	=>	"kipad",
							  "password"	=>	"ipad",//"ipad"
							  "source"		=>	"kapu",
							  "name"		=>  "KAPU",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=> 0,
							  "isinform"	=>	0
							  ),
				/*
				 * kgk 已经重新谈
				 *
				 * */
				 "11"=>array("server_url"	=>	"http://www.kgk.cc/websvckgkccclient/Service.asmx?WSDL",
							  "username"	=>	"lihai@china.com",
							  "password"	=>	"kela123",//"susan111"
				 			  "source"		=>	"kgk",
							  "name"		=>	"凯吉凯",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>0,//0=开启 1=关闭
							  "isinform"	=>	0 // 1=显示在下载列表
							  ),
				  /*HY */
				 "12"=>array(
							  "server_url"  =>      "http://systems.srkexport.com/stk/conkey.asmx/GetDiamonds",
							  "username"	=>	"slk88",
							  "password"	=>	"kela123456",
							  "source"		=>	"hy",
							  "name"		=>  "HY",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=> 0,
							  "isinform"	=>	0
							  ),
				   /*LEO */
				 "13"=>array(
							  "server_url"	=>	"http://feedcenter.net/Output/360/LSDCOHK.csv",
							  "source"		=>	"leo",
							  "name"		=>  "LEO",
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
				 "14"=>array(
							  "server_url"	=>	"ftp://120.138.212.154/Report.csv",
							  "source"		=>	"kiran",
							  "name"		=>  "KIRAN",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=> 0,
							  "isinform"	=>	0
							  ),
				 /*  vir */
				 /*
				  * 虚拟货品
				  */
				  "15"=>array("server_url"=>"",
				 			  "source"		=>	"vir",
							  "name"		=>	"virual",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>1,//0=开启 1=关闭
							  "isinform"	=>	1 // 1=显示在下载列表
							  ),

				 /*  karp */
				  "16"=>array("server_url"=>"",
				 			  "source"		=>	"karp",
							  "name"		=>	"kela123",//karp
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>0,//0=开启 1=关闭
							  "isinform"	=>	0 // 1=显示在下载列表
							  ),

				 /*  enjoy 深圳一加一 */
				  "17"=>array("server_url"=>"http://www.yjyzb.com/service/webDiamonds.asmx?WSDL",
				 			  "source"		=>	"enjoy",
							  "name"		=>	"kela123",//"enjoy"
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>0,//0=开启 1=关闭
							  "isinform"	=>	0 // 1=显示在下载列表
							  ),


				 /*  长宁 */
				  "18"=>array("server_url"=>"http://gateway.cheungning.com/CN_InterFace/WebAPI.asmx?wsdl",
				 			  "source"		=>	"changning",
							  "name"		=>	"changning",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>0,//0=开启 1=关闭
							  "isinform"	=>	0 // 1=显示在下载列表
							  ),
				 /*  KB */
				  "19"=>array("server_url"=>"http://kbdiamondz.com/Resources/FTP/KELA/KELA_StockDetails.xml",
				  	 		  "username"	=>	"KELA",
							  "password"	=>	"kela123",//"KELA123456"
				 			  "source"		=>	"kb",
							  "name"		=>	"kb",
							  "price"		=>	array("70"=>"1.48","0"=>"1.48"),
							  "lastDeletedia"=>0,//0=开启 1=关闭
							  "isinform"	=>	0 // 1=显示在下载列表
							  ),
    /*  KB */
    "20"=>array("server_url"=>"http://svc-api.kgirdharlal.com:8081/KGService.svc?wsdl",
        "username"	=>	"BLH-KG",
        "password"	=>	"BLH-KG$%",
        "source"	=>	"kg",
        "name"		=>	"kg",
        "price"		=>	array("70"=>"1.48","0"=>"1.48"),
        "lastDeletedia"=>0,//0=开启 1=关闭
        "isinform"	=>	0 // 1=显示在下载列表
    ),
    /* bluestar */
    "21"=>array("server_url"=>"http://appstock.bluestardiamonds.in/BlueStarWebService.asmx",
        "username"	=>	"dinzuanbu@kela.cn",
        "password"	=>	"kela123",//"susan111"
        "source"	=>	"bluestar",
        "name"		=>	"bluestar",
        "price"		=>	array("70"=>"1.48","0"=>"1.48"),
        "lastDeletedia"=>0,//0=开启 1=关闭
        "isinform"	=>	0 // 1=显示在下载列表
    ),
    /* fulong */
    "22"=>array("server_url"=>"http://www.forlongjewelry.com/APPAPI/Api.ashx",
        "username"  =>  "kela123",
        "password"  =>  "goodluck666",//"goodluck666"
        "source"    =>  "fulong",
        "name"      =>  "福隆",
        "price"     =>  array("70"=>"1.48","0"=>"1.48"),
        "lastDeletedia"=>0,//0=开启 1=关闭
        "isinform"  =>  0 // 1=显示在下载列表
    ),
    /* KBGems */
    "23"=>array("server_url"=>"ftp://192.163.253.228:2002/inventory.csv",
        "username"  =>  "ftpuser1",
        "password"  =>  "goodluck666",//"goodluck666"
        "source"    =>  "kbgems",
        "name"      =>  "KBGems",
        "price"     =>  array("70"=>"1.48","0"=>"1.48"),
        "lastDeletedia"=>0,//0=开启 1=关闭
        "isinform"  =>  0 // 1=显示在下载列表
    ),
    /* SheelGems */
    "24"=>array("server_url"=>"ftp://118.140.149.78/KELA.csv",
        "username"  =>  "Kela",
        "password"  =>  "kela001",//"goodluck666"
        "source"    =>  "sheelgems",
        "name"      =>  "sheelgems",
        "price"     =>  array("70"=>"1.48","0"=>"1.48"),
        "lastDeletedia"=>0,//0=开启 1=关闭
        "isinform"  =>  0 // 1=显示在下载列表
    ),       
    /* cdinesh */
    "25"=>array("server_url"=>"ftp://118.140.149.78/KELA.csv",
        "username"  =>  "Kela",
        "password"  =>  "kela001",//"goodluck666"
        "source"    =>  "cdinesh",
        "name"      =>  "cdinesh",
        "price"     =>  array("70"=>"1.48","0"=>"1.48"),
        "lastDeletedia"=>0,//0=开启 1=关闭
        "isinform"  =>  0 // 1=显示在下载列表
    ),
    /* cdinesh */
    "29"=>array("server_url"=>"http://www.slkdiamond.com/api/stocks/get_stocks_all/ADC70E0DAB9FFB87D200B4E138A032BA",
        "username"  =>  "Kela",
        "password"  =>  "kela001",//"goodluck666"
        "source"    =>  "slk",
        "name"      =>  "slk",
        "price"     =>  array("70"=>"1.48","0"=>"1.48"),
        "lastDeletedia"=>0,//0=开启 1=关闭
        "isinform"  =>  0 // 1=显示在下载列表
    ),
    /* starrays */
    "31"=>array("server_url"=>"http://starrays.com/DataService/KC/StockDown.aspx?uname=kelachina&pwd=Li7747",
        "username"  =>  "Kela",
        "password"  =>  "kela001",//"goodluck666"
        "source"    =>  "starrays",
        "name"      =>  "starrays",
        "price"     =>  array("70"=>"1.48","0"=>"1.48"),
        "lastDeletedia"=>0,//0=开启 1=关闭
        "isinform"  =>  0 // 1=显示在下载列表
    ),

    /* shawn */
    "32"=>array("server_url"=>"https://sheetalstock.sheetalgroup.com/stock.asmx/FullStock?%20HTTP/1.1",
        "username"  =>  "Kela",
        "password"  =>  "kela001",//"goodluck666"
        "source"    =>  "shawn",
        "name"      =>  "shawn",
        "price"     =>  array("70"=>"1.48","0"=>"1.48"),
        "lastDeletedia"=>0,//0=开启 1=关闭
        "isinform"  =>  0 // 1=显示在下载列表
    ),
);


//cat_id 在 ecs_category 表里 
$diaCat = array(
				"PRINCESS"		=>	array("cat_id"=>"2","img"=>"gongzhufang.jpg"),
                            //公主方形
				"PR"		=>	array("cat_id"=>"2","img"=>"gongzhufang.jpg"), //公主方形
				"PRI"		=>	array("cat_id"=>"2","img"=>"gongzhufang.jpg"), //公主方形
				"HEART"			=>	array("cat_id"=>"7","img"=>"xin.jpg"),				//心形
				"HT"			=>	array("cat_id"=>"7","img"=>"xin.jpg"),				//心形
				"HS"			=>	array("cat_id"=>"7","img"=>"xin.jpg"),				//心形
				"HE"			=>	array("cat_id"=>"7","img"=>"xin.jpg"),				//心形
				"MARQUISE"		=>	array("cat_id"=>"4","img"=>"ganlan.jpg"),			//橄榄形
				"OVAL"			=>	array("cat_id"=>"5","img"=>"73.jpg"),				//椭圆形
				"OV"			=>	array("cat_id"=>"5","img"=>"73.jpg"),				//椭圆形
				"OL"			=>	array("cat_id"=>"5","img"=>"73.jpg"),				//椭圆形
				"EMERALD"		=>	array("cat_id"=>"3","img"=>"zumulv.jpg"),			//祖母绿
				"EM"		=>	array("cat_id"=>"3","img"=>"zumulv.jpg"),			//祖母绿   PEAR
				"EMRALD"		=>	array("cat_id"=>"3","img"=>"zumulv.jpg"),			//祖母绿
				"EMR"		=>	array("cat_id"=>"3","img"=>"zumulv.jpg"),			//祖母绿
				"CUSHION"		=>	array("cat_id"=>"8","img"=>"dian.jpg"),			//垫形
				"CU"		=>	array("cat_id"=>"8","img"=>"dian.jpg"),			//垫形
				"CUSHION 1"		=>	array("cat_id"=>"8","img"=>"dian.jpg"),			//垫形
				"CUSHIONBRILLIANT"	=>	array("cat_id"=>"8","img"=>"dian.jpg"),		//垫形 //'"CUSHION BRILLIANT"' 去掉双引号
				"CUSHIONMODIFIEDBRILLIANT" =>	array("cat_id"=>"8","img"=>"dian.jpg"),//垫形	//'"Cushion Modified Brilliant"' 去掉双引号
				"CMB" =>	array("cat_id"=>"8","img"=>"dian.jpg"),//垫形
				"TRILLIANT"		=>	array("cat_id"=>"12","img"=>"sanjiao.jpg"),		//三角形
				"PEAR"			=>	array("cat_id"=>"6","img"=>"shuidi.jpg"),			//水滴形
				"PE"			=>	array("cat_id"=>"6","img"=>"shuidi.jpg"),			//水滴形
				"PS"			=>	array("cat_id"=>"6","img"=>"shuidi.jpg"),			//水滴形
				"RADIANT"		=>	array("cat_id"=>"9","img"=>"fushe.jpg"),			//辐射形				
				"SQUARERADIANT" =>	array("cat_id"=>"10","img"=>"fushe.jpg"),			//方形辐射形		//"SQUARE RADIANT"
				"SQUAREEMERALD" =>	array("cat_id"=>"11","img"=>"zumulv.jpg"),			//方形祖母绿		//"SQUARE EMERALD"
				"SQ" =>	array("cat_id"=>"11","img"=>"zumulv.jpg"),
                        //方形祖母绿		//"SQUARE EMERALD"
				"SQ EMERALD" =>	array("cat_id"=>"11","img"=>"zumulv.jpg"),
                        //方形祖母绿		//"SQUARE EMERALD"
				"SQEM" =>	array("cat_id"=>"11","img"=>"zumulv.jpg"),
                        //方形祖母绿		//"SQUARE EMERALD"
                "RB"			=>	array("cat_id"=>"1","img"=>"73.jpg"),				//圆形
                "BR"			=>	array("cat_id"=>"1","img"=>"73.jpg"),				//圆形
				"RBC"			=>	array("cat_id"=>"1","img"=>"73.jpg"),				//圆形
				"ROUND"			=>	array("cat_id"=>"1","img"=>"73.jpg"),				//圆形
				"ROUNDS"		=>	array("cat_id"=>"1","img"=>"73.jpg"),				//圆形
				"RD"			=>	array("cat_id"=>"1","img"=>"73.jpg"),				//圆形
				"YIXING"			=>	array("cat_id"=>"1","img"=>"gongzhufang.jpg"),		//圆形
				"LIXING"			=>	array("cat_id"=>"15","img"=>"gongzhufang.jpg"),		//梨形
				"CN_LIXING"			=>	array("cat_id"=>"15","img"=>"gongzhufang.jpg"),		//梨形
				//"PE"			=>	array("cat_id"=>"15","img"=>"gongzhufang.jpg"),		//梨形
				"ASIQIE"			=>	array("cat_id"=>"16","img"=>"gongzhufang.jpg"),		//阿斯切
				"AS"			=>	array("cat_id"=>"16","img"=>"gongzhufang.jpg"),		//阿斯切
				"ASH"			=>	array("cat_id"=>"16","img"=>"gongzhufang.jpg"),		//阿斯切
				"MQ"			=>	array("cat_id"=>"17","img"=>"mayan.jpg"),		//马眼形
				"MARQUISE"			=>	array("cat_id"=>"17","img"=>"mayan.jpg"),		//马眼形
				"OCTA"			=>	array("cat_id"=>"18","img"=>"yixing.jpg"),		//异形
				"FXS"			=>	array("cat_id"=>"18","img"=>"yixing.jpg"),		//异形
				"BAG"			=>	array("cat_id"=>"18","img"=>"yixing.jpg"),		//异形
				"TRA"			=>	array("cat_id"=>"18","img"=>"yixing.jpg"),		//异形
				"TBAG"			=>	array("cat_id"=>"18","img"=>"yixing.jpg"),		//异形
				"RA"			=>	array("cat_id"=>"19","img"=>"gongzhufang.jpg"),		//雷地恩形
				"RAD"			=>	array("cat_id"=>"19","img"=>"gongzhufang.jpg"),		//雷地恩形
				"Square"			=>	array("cat_id"=>"20","img"=>"gongzhufang.jpg"),		//正方形
				"SQRA"			=>	array("cat_id"=>"20","img"=>"gongzhufang.jpg"),		//正方形
				"Triangular"			=>	array("cat_id"=>"20","img"=>"gongzhufang.jpg"),		//三角形
				"TRI"			=>	array("cat_id"=>"20","img"=>"gongzhufang.jpg"),		//三角形
				);

$diaShapeKey = array( 
	            "1"	=>	"ROUNDS",		//圆形
				"2"	=>	"PRINCESS",		//公主方形
				"3"	=>	"EMERALD",		//祖母绿				
				"4"	=>	"MARQUISE",		//橄榄形
				"5"	=>	"OVAL",			//椭圆形
				"6"	=>	"PEAR",			//水滴形
				"7"	=>	"HEART",		//心形
				"8"	=>	"CUSHION",		//垫形
				"9"	=>	"RADIANT",		//辐射形				
				"10" 	=>	"SQUARERADIANT",//方形辐射形		//"SQUARE RADIANT"
				"11" 	=>	"SQUAREEMERALD",//方形祖母绿		//"SQUARE EMERALD"
				"12"	=>	"TRILLIANT",	//三角形
		        "15" => "PEAR", //水滴形 梨形
		        "17" => "MARQUISE", //马眼形
		        "18" => "OCTA",  //异形
		        "19"  => "RAD",  ////雷地恩形
		        "20"  => "TRILLIANT", //三角形
				);

$diaShapeVal = array( //enjoy
				"公主方"	=>	"PRINCESS",		//公主方形
				"心形"	=>	"HEART",		//心形
				"橄榄形"	=>	"MARQUISE",		//橄榄形
				"椭圆形"	=>	"OVAL",			//椭圆形
				"椭圆"	=>	"OVAL",			//椭圆形
				"祖母绿"	=>	"EMERALD",		//祖母绿
				"靠垫形"	=>	"CUSHION",		//垫形
				"靠垫形"	=>	"CUSHIONBRILLIANT",		//垫形
				"靠垫形"	=>	"CUSHIONMODIFIEDBRILLIANT",		//垫形
				"三角形"	=>	"TRILLIANT",	//三角形
				"水滴形"	=>	"PEAR",			//水滴形
				"辐射形"	=>	"RADIANT",		//辐射形
				"方形辐射形" 	=>	"SQUARERADIANT",//方形辐射形		//"SQUARE RADIANT"
				"方形祖母绿" 	=>	"SQUAREEMERALD",//方形祖母绿		//"SQUARE EMERALD"
				"圆形"	=>	"ROUNDS",		//圆形
				"圆形"	=>	"ROUND",		//圆形
				"圆形"	=>	"RD",		//圆形
				"圆形"	=>	"YIXING", //圆形
				"梨形"	=>	"LIXING", //梨形
				"阿斯切"	=>	"ASIQIE", //阿斯切
				"马眼形"        =>"MQ",
				"马眼"        =>"MQ",
				"雷地恩"      =>"RAD",	
                "枕形"   =>  "CUSHION",      //垫形			
				);

//抓完数据要生成的csv文件头部
$diaCsvTitle = array("编号", "库房", "形状", "重量", "颜色", "净度", "切工", "对称", "抛光", "荧光", "尺寸", "全深比", "台宽比", "证书", "证书号", "售价", "会员价","采购成本","香港价","星耀证书号","货源折扣","款号");

//抓完数据要生成的csv列说明
$diaCsv = array(
			"goods_sn"	=>"",
			"warehouse"	=>"",		//库房
			"shape"		=>"ROUNDS",	//形状(默认)
			"carat"		=>"",		//石重:
			"color"		=>"",		//颜色:
			"clarity"	=>"",		//净度:
			"cut"		=>"",		//切工:
			"sym"		=>"",		//对称:
			"po"		=>"",		//抛光:
			"fl"		=>"",		//荧光:
			"diam"		=>"",		//尺寸:
			"depth"		=>"",		//全深比:
			"table"		=>"",		//台宽比:
			"cert"		=>"GIA",	//证书:
			"cert_id"	=>"",		//证书号:
			"shop_price"=>"",		//每颗价格
			"member_price"=>"",		//会员价计算出
			"chengbenjia"=>"",		//采购成本
			"xianggangjia"=>"",	    //香港价
			"gemx_zhengshu"=>"",	//星耀证书号
			"source_discount"=>"",	//货源折扣
			"kuan_sn"=>""		//用来标识是否是天生一对裸钻的孪生钻
		 );


//往diamond表里插数据的默认值
$diaDefault = array(
					"cat_id" 		=> "1",
					"add_time" 		=> time(),			//添加时间
					"last_update"	=> time(),			//最近修改时间
					"goods_name"	=> "",				//名称
					"goods_sn"		=> "",				//商品号
					"brand_name" 	=> "",				//品牌(无用)
					"market_price"  => "",				//市场价格
					"shop_price" 	=> "",				//商店价格
					"member_price" 	=> "",				//商店价格
					"integral" 		=> "0",				//积分
					"original_img"	=> "",				//图
					"goods_img"     => "",				//图
					"goods_thumb" 	=> "",				//图
					"keywords"	 	=> "钻石、裸钻、GIA", //关键字描述
					"goods_brief" 	=> "钻石、裸钻、GIA", //关键字描述
					"goods_desc" 	=> "钻石、裸钻、GIA",	//关键字描述
					"goods_weight"  => "",				//重量
					"goods_number"  => "1",				//商品数量
					"warn_number" 	=> "1",				//库存警告数
					"is_best"	 	=> "0",
					"is_new"	 	=> "0",
					"is_hot"	 	=> "0",
					"can_handsel"	=> "0",
					"is_on_sale" 	=> "1",				//是否可售
					"is_alone_sale" => "1",				//是否可售
					"is_real" 		=> "1",				//是否可售
					"goods_type" 	=> "9",				//商品类型
					"carat" 		=> "",				//钻石重量
					"cert"			=> "",				//证书
					"depth" 		=> "",				//台深百分比
					"table" 		=> "",				//台面百分比
					"sym" 			=> "",				//对称性
					"cert_id" 		=> "",				//证书编号
					"color" 		=> "",				//颜色
					"cut" 			=> "",	 	 		//切工
					"po" 			=> "",				//抛光度
					"clarity" 		=> "",				//净度
					"fl" 			=> "",				//荧光度
					"girdle"		=> "",
					"culet" 		=> "",
					"warehouse" 	=> "",				//库房
					"source"		=> "",				//货品来源
					"xianggangjia"	=> "",				//香港价
					"gemx_zhengshu"	=> "",				//星耀证书号
					);

//荧光对应数组
$fluor_arr = array(
    'F'=>array('F','FA','FAINT','FAINT','FAINTBLUE','FAINTYELL','FNT'),
    'M'=>array('M','MB','MED','MEDBLUE','MEDIUM','MEDIUM','MEDIUMBLU','MOD','MODERATEB'),
    'N'=>array('N','NN','NON','NONE','NONE'),
    'S'=>array('S','SB','SLIGHT','SLIGHTBLU','SLIGHTYEL','ST','STG','STGBLUE','STRONGBLU','STRONG'),
    'V'=>array('V.SLTY','VB','VERYSTRONG','VERYSLIGH','VS','VST','VSTRONG','VERYSLIGHT'),

);

//切工 、对称、抛光
$symmetry_arr = array('GD'=>'G');


