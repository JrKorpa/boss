<?php

class T100Class {
    
    private $entId;
    private $compId;
    private $ipAddr;
	private $wsUrl;
    
    function __construct() {
        $this->entId = 10;
        $this->compId = 1000;
        $this->ipAddr = '192.168.1.192';
		$this->wsUrl= 'http://'.($this->ipAddr).'/wtopprd/ws/r/awsp900?wsdl';
    }
    
    function getBCInfo($fid, $bc_id){
		file_put_contents('/data/www/cuteframe_boss/api/logs/t100_getinfo.log', json_encode(array('fid' => $fid, 'bc_id' => $bc_id)).PHP_EOL, FILE_APPEND);
        $ori_fid = $fid;
        $fid = $this->adaptFidForT100($fid);
        $xml = '<request type="sync" key="84ebf957f7e3e7a064ffb74a6db9feb9"><host prod="EC_B2B" ver="5.0.0.0" ip="192.168.1.196" id="DS123456" lang="zh_CN" timezone="+8" timestamp="20160612122001602" acct="eait100"/><service prod="T100" name="getInfoById" srvver="1.0" ip="'.($this->ipAddr).'" id="topprd"/><datakey type="FOM"><key name="EntId">'.($this->entId).'</key><key name="GUID">69d471fa-882a-4ce4-a730-59e70c5015aa</key><key name="CompanyId">'.($this->compId).'</key></datakey>'
       .'<payload><param key="data" type="XML"><![CDATA[<Request><RequestContent><Parameter><Record><Field name="FID" value="'.$fid.'"/><Field name="BC_ID" value="'.$bc_id.'"/></Record></Parameter></RequestContent></Request>]]></param></payload></request>';  
        
       $resp = $this->invokeT100Svc($xml);
        // convert
        if (!empty($resp)) {
            $xo = $this->extractCData($resp);
            if ($xo) {
                $list = $this->xml2array($xo);
                $array = array('prc_id' => $ori_fid);
                foreach($list['ResponseContent']['Document']['RecordSet']['Master']['Record']['Field'] as $v) {
                    $array[$this->xml_attribute($v, 'name')] = $this->xml_attribute($v, 'value');
	            }
	            return $array;
            }
            return false;
        } else {
            return false;
        }
    }
    
    function changeBCStatus($fid, $bc_id, $status) {
		file_put_contents('/data/www/cuteframe_boss/api/logs/t100_cs.log', json_encode(array('fid'=>$fid, 'bc_id'=>$bc_id, 'status' =>$status)).PHP_EOL, FILE_APPEND);
        //TODO: status经确认，T100内部做了转换
        $fid = $this->adaptFidForT100($fid);
        $xml = '<request type="sync" key="84ebf957f7e3e7a064ffb74a6db9feb9"><host prod="EC_B2B" ver="5.0.0.0" ip="192.168.1.196" id="DS123456" lang="zh_CN" timezone="+8" timestamp="20160612122001602" acct="eait100"/><service prod="T100" name="changeStatus" srvver="1.0" ip="'.($this->ipAddr).'" id="topprd"/><datakey type="FOM"><key name="EntId">'.($this->entId).'</key><key name="GUID">69d471fa-882a-4ce4-a730-59e70c5015aa</key><key name="CompanyId">'.($this->compId).'</key></datakey>'
       .'<payload><param key="data" type="XML"><![CDATA[<Request><RequestContent><Parameter><Record><Field name="FID" value="'.$fid.'"/><Field name="STATUS" value="'.$status.'"/><Field name="BC_ID" value="'.$bc_id.'"/></Record></Parameter></RequestContent></Request>]]></param></payload></request>';
        
       $resp = $this->invokeT100Svc($xml);
        // convert
        if (!empty($resp)) {
            $xo = $this->extractCData($resp);
            if ($xo) {
                $list = $this->xml2array($xo);
                foreach($list['ResponseContent']['Document']['RecordSet']['Master']['Record']['Field'] as $v) {
                    return $this->xml_attribute($v, 'name') =='flag' && intval($this->xml_attribute($v, 'value')) == 1;
                }
            }
        }
        return false;
    }
    
    function getBCList($fid, $page, $pagesize) {
        file_put_contents('/data/www/cuteframe_boss/api/logs/t100_getlist.log', json_encode(array('fid'=>$fid, 'page'=>$page, 'size'=>$pagesize)).PHP_EOL, FILE_APPEND);
	    $fid = $this->adaptFidForT100($fid);
        $xml='<request type="sync" key="84ebf957f7e3e7a064ffb74a6db9feb9"><host prod="EC_B2B" ver="5.0.0.0" ip="192.168.1.196" id="DS123456" lang="zh_CN" timezone="+8" timestamp="20160612122001602" acct="eait100"/><service prod="T100" name="getListByPrcNo" srvver="1.0" ip="'.($this->ipAddr).'" id="topprd"/><datakey type="FOM"><key name="EntId">'.($this->entId).'</key><key name="GUID">69d471fa-882a-4ce4-a730-59e70c5015aa</key><key name="CompanyId">'.($this->compId).'</key></datakey>'
       .'<payload><param key="data" type="XML"><![CDATA[<Request><RequestContent><Parameter><Record><Field name="PAGE" value="'.$page.'"/><Field name="PAGESIZE" value="'.$pagesize.'"/><Field name="FID" value="'.$fid.'"/></Record></Parameter></RequestContent></Request>]]></param></payload></request>';  

        $resp = $this->invokeT100Svc($xml);

        // convert
        if (!empty($resp)) {
            $xo = $this->extractCData($resp);
            if ($xo) {
                $list = $this->xml2array($xo);
                $array['total_row'] = intval($this->xml_attribute($list['ResponseContent']['Document']['RecordSet']['Master']['Record']['Field']['@attributes'] , 'value'));
                $array['list'] = array();
                foreach($list['ResponseContent']['Document']['RecordSet']['Master']['Record']['Detail']['Record'] as $v) {
					$suba = array();
					foreach($v as $p) {
						$suba[$this->xml_attribute($p,'name')] = $this->xml_attribute($p, 'value'); 
					}
					$array['list'][] = $suba;
	            }
                return $array;
            }
        } else {
            return false;
        }
    }
    
    private function adaptFidForT100($fid) {
        switch ($fid) {
            case '416': 
                return 'GYS00033';
            case '452': 
                return 'GYS00041';
            case '5': 
                return 'GYS00006';
            default:
                return $fid;
        }
    }
    
    private function adaptFidForBoss($fid) {
        switch ($fid) {
            case 'GYS00033':
                return '416';
            case 'GYS00041':
                return '452';
            case 'GYS00006':
                return '5';
            default:
                return $fid;
        }
    }
    
    private function extractCData($xml) {
        if (empty($xml)) return false;

        $xml = str_replace('&lt;', '<', $xml);
        $xml = str_replace('&gt;', '>', $xml);
        $resp = preg_match('/<payload>\s*<param[^>]+>([\s\S]*)<\/param>\s*<\/payload>/', $xml, $matches);
        if ($resp) {
            $xml_source = trim($matches[1]);
			//var_dump($xml_source);
            $xml_source = substr($xml_source, 10, strlen($xml_source) - 13);
			//var_dump($xml_source);
            $xml_obj = simplexml_load_string($xml_source);
            if ($xml_obj) {
                return $xml_obj;
            }
        }
        return false;
    }
    
    private function xml2array($xmlObject, $out = array()) {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? $this->xml2array ( $node ) : $node;
    
        return $out;
    }
    
    private function xml_attribute($object, $attribute) {
        if(isset($object[$attribute])) return (string) $object[$attribute];
    }
    
    private function invokeT100Svc($xml) {
        $client = new SoapClient($this->wsUrl,array('trace'=> 1,'exceptions'=>true,'cache_wsdl'=>WSDL_CACHE_NONE));
        $client->soap_defencoding = 'utf-8';
        $client->xml_encoding = 'utf-8';
        try{
            $client->invokeSrv($xml);
            $result = $client->__getLastResponse();
			file_put_contents('/data/www/cuteframe_boss/api/logs/t100_resp.log', json_encode($result).PHP_EOL, FILE_APPEND);
            return $result;
        } catch (Exception $e) {
			file_put_contents('/data/www/cuteframe_boss/api/logs/t100_e.log', json_encode($e).PHP_EOL, FILE_APPEND);
            var_dump($e);
        }
    }
}

?>
