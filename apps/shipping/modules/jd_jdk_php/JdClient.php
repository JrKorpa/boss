<?php
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-5
 * Time: 下午6:01
 *  sdk 客户端
 */

class JdClient
{
    /**
     * @var 商家id
     */
    public $venderId;

    /**
     * @var 商家key
     */
    public $venderKey;

    /**
     * @var 请求的url
     */
    public $serverUrl;

    /**
     * api 版本
     * @var string
     */
    protected $version = "1.0";

    /**
     *  构造方法
     * @param  $venderId 商家id
     * @param  $venderKey 商家key
     * @param  $serverUrl 请求url
     * @return void
     */
    function JdClient($venderId, $venderKey, $serverUrl)
    {
        $this->serverUrl = $serverUrl;
        $this->venderId = $venderId;
        $this->venderKey = $venderKey;
    }

    /**
     *
     * @param  $request
     * @return void
     */
    public function execute($request)
    {
        //组装系统参数
        $sysParams["vender_id"] = $this->venderId; //商家id
        $sysParams["v"] = $this->version; //版本
        $sysParams["timestamp"] = date("Y-m-d H:i:s"); //时间戳
        $sysParams["method"] = $request->getApiMethod(); //方法名
        //获取业务json格式的参数
        $apiParams["360buy_param_json"] = $request->getAppJsonParams();

        $sysParams["sign"] = $this->generateSign(array_merge($sysParams,$apiParams));

        //生产请求的url,包括请求地址以及一些系统级参数信息
        $requestUrl=$this->buildUrl($sysParams);
        //发起HTTP请求

        try {
            $resp = $this->doExecute($requestUrl, $apiParams);
        } catch (Exception $e) {
            print_r($e->getMessage());
            return false;
        }
        return $resp;
    }

    /**
     * 执行HTTP请求。
     * @throws Exception
     * @param  $url
     * @param  $apiParams
     * @return mixed
     */
    public function  doExecute($url,$apiParams){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        /**
         * 首先判断是否含有页面参数如果有，则通过post方式请求
         */
        if (is_array($apiParams) && 0 < count($apiParams)) {
            $postBodyString = "";
            foreach ($apiParams as $k => $v)
            {
                $postBodyString .= "$k=" . urlencode($v) . "&";
            }
            unset($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
        }

        $reponse = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch), 0);
        }
        else
        {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new Exception($reponse, $httpStatusCode);
            }
        }
        curl_close($ch);
        return $reponse;
    }

    /**
     * 组装并生产url
     * @param  $params 系统级参数
     * @return void
     */
    public function buildUrl($params)
    {
        $requestUrl = $this->serverUrl . "?";
        foreach ($params as $sysParamKey => $sysParamValue)
		{
			$requestUrl .= "$sysParamKey=" . urlencode($sysParamValue) . "&";
		}
		$requestUrl = substr($requestUrl, 0, -1);
        return $requestUrl;
    }

    /**
     * 签名
     * @param  $params 业务参数
     * @return void
     */
    private function generateSign($params)
    {
        //所有请求参数按照字母先后顺序排序
        ksort($params);
        //定义字符串开始 结尾所包括的字符串
        $stringToBeSigned = $this->venderKey;
        //把所有参数名和参数值串在一起
        foreach ($params as $k => $v)
        {
            $stringToBeSigned .= "$k$v";
        }
        unset($k, $v);
        //把venderKey夹在字符串的两端
        $stringToBeSigned .= $this->venderKey;
        //使用MD5进行加密，再转化成大写
        return strtoupper(md5($stringToBeSigned));
    }
}
