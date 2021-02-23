<?php
class ApiModel
{
    public static function salepolicy_api($method,$keys,$vals){
        $ret=Util::sendRequest('salepolicy', $method, $keys, $vals);
        return $ret;
    }
}

?>