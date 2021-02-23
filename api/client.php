<?php
/**
 *  -------------------------------------------------
 *   @file		: .php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangruiying
 *   @date		:
 *   @update	:
 *  -------------------------------------------------
 */
header("Content-Type:text/html;charset=UTF-8");
ini_set("soap.wsdl_cache_enabled", "0"); //清空原有的wsdl缓存

$client = new SoapClient("http://boss.kela.cn/api/server.php?wsdl",array('trace'=> true,'exceptions'=>true,'cache_wsdl'=>WSDL_CACHE_NONE));
$client->soap_defencoding = 'utf-8';
$client->xml_encoding = 'utf-8';
try{
	 $auth = array(
        'sign'=>md5('BC23011BC2301'),//加密串
	    'fid'=>1,//工厂ID
		'factory_order_sn'=>'BC230',
		'product_info'=>"<?xml version=\"1.0\" encoding=\"utf-8\"?>
<goods_details>
<goods_info>
<xuhao>1111111</xuhao>
<kehuming>客户名</kehuming>
<buchanhao>BC123</buchanhao>
<kuanhao>KL12333</kuanhao>
<mohao>233444</mohao>
<kuanshifenlei>2222</kuanshifenlei>
<shoucun>34</shoucun>
<zhuchengse>RR</zhuchengse>
<maozhong>33</maozhong>
<zhuchengsezhong>333</zhuchengsezhong>
<jinhao>13</jinhao>
<zhuchengsemairudanjia>22</zhuchengsemairudanjia>
<zhushi>22</zhushi>
<zhushilishu>22</zhushilishu>
<zhushizhong>44</zhushizhong>
<zhushiyanse>66</zhushiyanse>
<zhushijingdu>89</zhushijingdu>
<zhushimairudanjia>77</zhushimairudanjia>
<fushi>66</fushi>
<fushilishu>67</fushilishu>
<fushizhong>66</fushizhong>
<fushimairudanjia>66</fushimairudanjia>
<gongfei>66</gongfei>
<chaoshifei>66</chaoshifei>
<qitagongfei>66</qitagongfei>
<peijianchengben>6</peijianchengben>
<shuifei>87</shuifei>
<kelaishixinxi>45</kelaishixinxi>
<zhengshuhao>4544</zhengshuhao>
<shi2>54</shi2>
<shi2lishu>56</shi2lishu>
<shi2zhong>55</shi2zhong>
<shi2mairudanjia>44</shi2mairudanjia>
</goods_info>
<goods_info>
<xuhao>1111111</xuhao>
<kehuming>客户名</kehuming>
<buchanhao>BC123</buchanhao>
<kuanhao>KL12333</kuanhao>
<mohao>233444</mohao>
<kuanshifenlei>2222</kuanshifenlei>
<shoucun>34</shoucun>
<zhuchengse>RR</zhuchengse>
<maozhong>33</maozhong>
<zhuchengsezhong>333</zhuchengsezhong>
<jinhao>13</jinhao>
<zhuchengsemairudanjia>22</zhuchengsemairudanjia>
<zhushi>22</zhushi>
<zhushilishu>22</zhushilishu>
<zhushizhong>44</zhushizhong>
<zhushiyanse>66</zhushiyanse>
<zhushijingdu>89</zhushijingdu>
<zhushimairudanjia>77</zhushimairudanjia>
<fushi>66</fushi>
<fushilishu>67</fushilishu>
<fushizhong>66</fushizhong>
<fushimairudanjia>66</fushimairudanjia>
<gongfei>66</gongfei>
<chaoshifei>66</chaoshifei>
<qitagongfei>66</qitagongfei>
<peijianchengben>6</peijianchengben>
<shuifei>87</shuifei>
<kelaishixinxi>45</kelaishixinxi>
<zhengshuhao>4544</zhengshuhao>
<shi2>54</shi2>
<shi2lishu>56</shi2lishu>
<shi2zhong>55</shi2zhong>
<shi2mairudanjia>44</shi2mairudanjia>
</goods_info>
</goods_details>
"
        );
	$result=$client->addDeliveryOrder($auth);
	var_dump($result);
}catch(Exception $e) {
	var_dump ($e);
}
?>