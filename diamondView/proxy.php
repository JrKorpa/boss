<?php
echo post("https://diamonds.kirangems.com:8080/imaged".$_SERVER['PATH_INFO'],'');

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