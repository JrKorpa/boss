<?php
class VopWrapper {
	private static $_instance = null;
	
	const APP_KEY = 'a876c4cc';//'0d3cda55';
	const APP_SECRET = '77780A5819EC3CFBE648436DB9F95492'; //BE0131A73E50E5E8F9D7AEA99A800DED';
	const APP_URL = 'http://sandbox.vipapis.com:80'; //'http://gw.vipapis.com';
	const VENDOR_ID = 550; //1337;
	const CO_MODE = 'jit'; //jit_4a;
	const LANGUAGE = 'zh'; //zh; 
	
	const MOD_DELIVERY = 'delivery';
	public static function getInstance($module = 'Delivery'){
	    $module = strtolower($module);
	    $service = null;
	    switch ($module){
	        case 'delivery':
	            {
	                require_once "vipapis/delivery/JitDeliveryServiceClient.php";
	                self::$_instance = \vipapis\delivery\JitDeliveryServiceClient::getService();
	                break;
	            }
	        default:
	            {
	                echo $module." is not supported!";
	            }
	             
	    }
	    $ctx=\Osp\Context\InvocationContextFactory::getInstance();
	    $ctx->setAppKey(self::APP_KEY);
	    $ctx->setAppSecret(self::APP_SECRET);
	    $ctx->setAppURL(self::APP_URL);
	    $ctx->setLanguage(self::LANGUAGE);
	    return self::$_instance;
	}	
	
}