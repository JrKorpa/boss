<?php
/**
 * Created by JetBrains PhpStorm.
 * User: denniszhu
 * Date: 12-8-13
 * Time: 下午4:26
 * To change this template use File | Settings | File Templates.
 */

require_once 'includes/modules/paipaiOrder/lib/HttpClient.class.php';
require_once 'includes/init.php';

class PaiPaiOpenApiOauth
{

    private $uin;
    private $appOAuthID;
    private $appOAuthkey;
    private $accessToken;
    private $hostName="api.paipai.com";
    private $format="xml";
    private $charset="gbk";
    private $method = "get";	//POST or GET
    private $params ;           //{String, Object}

    private  $apiPath;
    private  $debugOn; //是否打开调试模式


    public function __construct($appOAuthID,$appOAuthkey,$accessToken,$uin){
        $this->appOAuthID = $appOAuthID;
        $this->appOAuthkey = $appOAuthkey;
        $this->accessToken = $accessToken;
        $this->uin = $uin;
        $this->PaiPaiOpenApiOauth();
    }


    private function PaiPaiOpenApiOauth(){
        $this->params = array();
        $this->params["randomValue"] = (rand() * 100000+11229);
        $this->params["timeStamp"] = $this->getMillisecond();
    }


    private function getMillisecond (){
        list($s1, $s2) = explode(' ', microtime());
        $ret =  (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000) . "";
        return $ret;
    }


    private function invokeOpenApi(){

        $this->params["appOAuthID"] = $this->appOAuthID;
        $this->params["accessToken"] = $this->accessToken;
        $this->params["uin"] = $this->uin;
        $this->params["format"] = $this->format;
        $this->params["charset"] = $this->charset;

        $protocol = "http";                 // "http" | "https"

        // 第二步： 构造密钥。得到密钥的方式：在应用的appOAuthkey末尾加上一个字节的“&”，即appOAuthkey&
        $secret = $this->appOAuthkey . "&";

        //生成签名，使用HMAC-SHA1加密算法，将Step1中的到的源串以及Step2中得到的密钥进行加密。然后将加密后的字符串经过Base64编码。
        $sign = $this->makeSign($this->method, $secret);
        $this->params["sign"] = $sign;

        //echo "@@@@:invokeOpenApi: sign :" ;var_dump( $sign);echo "<br/>";
        //echo "@@@@:invokeOpenApi: encodeUrl(sign) :" ;var_dump($this->encodeUrl($sign));echo "<br/>";
        $url = $protocol . "://" . $this->hostName . $this->apiPath . '?charset=' . $this->params["charset"] .'&';

        unset($this->params["charset"]);

        $cookies = null;
        $resp = null;

        if(!strcmp("POST", strtoupper($this->method))){
            $resp = $this->postRequest($url, $cookies, $protocol);
        }else if(!strcmp("GET", strtoupper($this->method))){
            $resp = $this->getRequest($url, $cookies, $protocol);
        }else{
            $resp = "";//error
        }
        return $resp;
    }


    public function invoke(){
        $res = $this->invokeOpenApi();
        if(strcmp("xml", $this->format)){

        }else if(strcmp("json", $this->format)){

        }else{
            throw new Exception();
        }
        return $res;
    }


    /**
     * 第三步：生成签名值。
     * 1. 使用HMAC-SHA1加密算法，将Step1中的到的源串以及Step2中得到的密钥进行加密。
     * 2. 然后将加密后的字符串经过Base64编码。
     * 3. 得到的签名值结果如下：
     *
     * more,to see: http://php.net/manual/en/function.hash-hmac.php
     * @param $method
     * @param $secret
     */
    public function makeSign($method, $secret){
        $sig = "";
        try{
            //echo "@@@@:makeSign: (secret)  :" ;var_dump($secret);echo "<br/>";
            //获取需要加密的原串
            $mk = $this->makeSource($method, $this->apiPath);
            //echo "@@@@:makeSign: makeSign(mk) Src String :" ;var_dump($mk);echo "<br/>";
            //使用sha1 加密算法加密
            //注意：这里必须设置为true： When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits.
            $hash = hash_hmac("sha1", $mk, $secret, true);

            //将加密后的字符串用base64方式编码
            $sig = base64_encode($hash);
        }catch (Exception $e){
            throw new Exception();
        }
        return $sig;
    }


    /**
     * 构造原串
     * 源串是由3部分内容用“&”拼接起来的：   HTTP请求方式 & urlencode(uri) & urlencode(a=x&b=y&...)
     * @param $method  get | post
     * @param $urlPath if our url is http://api.paipai.com/deal/sellerSearchDealList.xhtml,then
     * $urlPath=/deal/sellerSearchDealList.xhtml
     */
    public function makeSource($method, $urlPath){
        $keys = $this->params;
        ksort($keys);//按照关键码从小到大排序
        //先拼装  HTTP请求方式 & urlencode(uri) &
        $buffer = "" . strtoupper($method) . "&" . $this->encodeUrl($urlPath) . "&";
        //拼装 参数部分
        $buffer2 = "";
        foreach($keys as $key => $value){
            $buffer2 .= $key . "=" . $value . "&";
        }
        $buffer2 = substr_replace($buffer2, '', -1, 1 );
        //组装成预期的“原串”
        $buffer .= $this->encodeUrl($buffer2);

        return $buffer;
    }


    private function getRequest($url, $cookies, $protocol){
        $httpClient = new HttpClient($this->hostName);
        $httpClient->setDebug($this->getDebugOn());//是否打开debug模式
        try{
            $httpClient->setUserAgent("PaiPai API Invoker/PHP " . PHP_VERSION);
            if (!$httpClient->get($url, $this->getParams())) {
                return '<p>Request failed!</p>';
            } else {
                return $httpClient->getContent();
            }
        }catch (Exception $e){

        }
    }


    private function postRequest($url, $cookies, $protocol){
        $httpClient = new HttpClient($this->hostName);
        $httpClient->setDebug(true);
        try{
            $httpClient->setUserAgent("PaiPai API Invoker/PHP " . PHP_VERSION);
            if(!$httpClient->post($url,$this->getParams())){
                return '<p>Request failed!</p>';
            }else{
                return $httpClient->getContent();
            };
        }catch (Exception $e){

        }
    }


    private function encodeUrl($input) {
        try{
            $tmpUrl = urlencode($input);
            $tmpUrl = str_replace("+", "%20",$tmpUrl);
            $tmpUrl = str_replace("*", "%2A",$tmpUrl);
            return $tmpUrl;
        }catch (Exception $e){
            throw new Exception($e->getMessage(),$e->getCode());
        }
    }


    /**
     * the follows are getters and setters
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function setApiPath($apiPath)
    {
        $this->apiPath = $apiPath;
    }

    public function getApiPath()
    {
        return $this->apiPath;
    }

    public function setAppOAuthID($appOAuthID)
    {
        $this->appOAuthID = $appOAuthID;
    }

    public function getAppOAuthID()
    {
        return $this->appOAuthID;
    }

    public function setAppOAuthkey($appOAuthkey)
    {
        $this->appOAuthkey = $appOAuthkey;
    }

    public function getAppOAuthkey()
    {
        return $this->appOAuthkey;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    public function getCharset()
    {
        return $this->charset;
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setHostName($hostName)
    {
        $this->hostName = $hostName;
    }

    public function getHostName()
    {
        return $this->hostName;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public static function setOauth($oauth)
    {
        self::$oauth = $oauth;
    }

    public static function getOauth()
    {
        return self::$oauth;
    }

    public function setUin($uin)
    {
        $this->uin = $uin;
    }

    public function getUin()
    {
        return $this->uin;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return mixed this is a reference!!!!
     */
    public function &getParams()
    {
        return $this->params;
    }

    public function setDebugOn($debugOn)
    {
        $this->debugOn = $debugOn;
    }

    public function getDebugOn()
    {
        return $this->debugOn;
    }
    
    /**
	 * 用外部单号支付订单
	 * by col
	 */
	public function outer_order_pay($order_sn, $taobao_order_sn, $price=false)
	{
		require_once(ROOT_PATH . "includes/cls_yeji.php");  //add buy lulu
		// 用户使用的提交数据的方法。post 和 get均可；以及字符集
	    $this->setMethod("get");//post
	    $this->setCharset("utf-8");//gbk
	    // 以下部分用于设置用户在调用相关接口时url中"?"之后的各个参数，如上述描述中的a=1&b=2&c=3
	    $params = &PaiPaiOpenApiOauth::getParams();//注意，这里使用的是引用，故可以直接使用
	    $params["sellerUin"] = $this->uin;
	    $params["zhongwen"] = "cn";
	    $params["pageSize"] = "10";
	    $params["tms_op"] = "admin@855000017";
	    $params["tms_opuin"] = $this->uin;
	    $params["tms_skey"] = "@WXOgdqq16";
		$params["dealCode"] = $taobao_order_sn;
		$params['listItem'] =1;//显示订单商品
		//var_dump($params);exit;
		$this->setApiPath("/deal/getDealDetail.xhtml");
		 $xml = $this->invoke();
	    $xml = simplexml_load_string($xml);
	    //print_r($xml);
	  	//exit;
		
		
		
		// 错误代码
		if(trim($xml -> errorCode) !='0') {return false;}
		// 拍拍订单状态错误
		if(trim($xml->dealState) != "DS_WAIT_SELLER_DELIVERY") return false;
		$real_payment = trim($xml -> dealPayFeeTotal)/100;

		// 支付金额不等于实际支付金额
		if($price !== false && abs($real_payment - $price) > 1) return false;
		//echo "abc";exit;
		// 支付记录排重
		
		$cnt = $GLOBALS["db"] -> getOne("SELECT COUNT(*) FROM ecs_order_pay_action  WHERE attach_sn='".trim($xml->tenpayCode)."'");
		//echo $xml->tenpayCode;exit;
		if($cnt) return false;
		// 取订单信息
		$order_info = $GLOBALS["db"] -> getRow("SELECT order_id, order_sn, order_time, order_amount, money_paid,consignee FROM ecs_order_info WHERE order_sn = '".$order_sn."'");
		// 增加支付记录
		$pay_action = array(
			"order_id" => $order_info["order_id"],
			"order_sn" => $order_sn,
			"order_time" => date("Y-m-d H:i:s"),
			"deposit" => $real_payment,
			"order_amount" => $order_info["order_amount"],
			"balance" => $order_info["order_amount"]-$real_payment,
			"pay_time" => date("Y-m-d H:i:s"),
			"pay_type" => "腾讯拍拍店",
			"order_consignee" => $order_info["consignee"],
			"attach_sn" => trim($xml->tenpayCode),
			"leader" => $_SESSION["admin_name"],
			"leader_check" => date("Y-m-d H:i:s"),
			"opter_name" => $_SESSION["admin_name"],
			"status" => "1",
			"department" => "2",
			"system_flg" => "2"
		);
		$GLOBALS["db"]->autoExecute('ecs_order_pay_action', $pay_action, 'INSERT');
		// 支付订单
		$sql = "UPDATE ecs_order_info SET money_paid=money_paid+$real_payment, order_amount = order_amount - $real_payment, pay_time='".time()."' WHERE order_id = '".$order_info["order_id"]."'";
		$GLOBALS["db"] -> query($sql);
		//add buy lulu  添加付款业绩
		$yeji_log = new Yeji($GLOBALS['db']);
		$yeji_log->doAdd($order_info["order_id"],1);
		/* 记录log */
		order_action($order_info['order_sn'], OS_CONFIRMED, $order['shipping_status'], $order['pay_status'], "快速付款－－拍拍订单号:".$taobao_order_sn.",财付通交易号:".trim($xml->tenpayCode));

		// 检查支付金额,如果完成付款,修改订单付款状态
		$sql = "update `ecs_order_info` set pay_status=if((`order_amount`=0), 2, 4) WHERE order_sn = '".$order_info["order_sn"]."'";
		$GLOBALS["db"] -> query($sql);
		
		return true;
	}


}
