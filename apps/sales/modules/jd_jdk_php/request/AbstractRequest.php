<?php
/**
 * Created by JetBrains PhpStorm.
 * User: YangLin
 * Date: 11-8-8
 * Time: 下午3:52
 * 
 */
 
abstract class AbstractRequest {

    /**
     * @var array 业务参数集合
     */
      protected $apiParams = array();

    /**
     * @abstract
     * @return void 将业务参数转换成json字符串
     */
    public abstract function getAppJsonParams();

    /**
     * @abstract
     * @return void 定义 访问接口的方法名称
     */
    public abstract  function getApiMethod();
}
