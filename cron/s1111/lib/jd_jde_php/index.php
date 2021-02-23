<?php

require_once(APP_ROOT.'/sales/modules/jd_jdk_php_2/index.php');
//京东新店
class jd_jde_php extends jd_jdk_php_2
{
	var $key = "985C9E37363A93F9D74724201AC0398B";
	var $secret = "f981f669e7804d379ecf6ac8aeac463f";
	//var $access_token = "2ad59103-3023-4608-bd84-4ad2de231d6e";
	var $access_token = "4970054d-840a-4382-bd77-2bb2b73f308a";
    //下面这个token可以延时
	//var $access_token = "1b6cfb26-7c4c-4602-97bd-b307611c9762";

	//new_jd true:新店 (默认false)
	var $new_jd = true;


}



?>