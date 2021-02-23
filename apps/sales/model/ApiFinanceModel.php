<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiOrderModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Zhangyuanyuan <1041522879@qq.com>
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiFinanceModel
{
    public static function getInvoiceInfo($order_sn){
        $keys = array('order_sn');
        $vals = array($order_sn);
        $ret=ApiModel::finance_api('GetInvoiceInfo',$keys,$vals);
		if($ret['error']==0){
			return $ret['data'];
		}else{
			return false;
		}
    }

	public static function updateInvoiceInfoStatusByIds($ids,$status){
        $keys = array('ids','status');
        $vals = array($ids,$status);
        $ret=ApiModel::finance_api('updateInvoiceInfoStatusByIds',$keys,$vals);
        return $ret;
	}

    public static function createInvoiceInfo($insertdata){
        $keys = array('insertdata');
        $vals = array($insertdata);
        $ret=ApiModel::finance_api('createInvoiceInfo',$keys,$vals);
        return $ret;
    }

    public static function getInvoiceInfoByInvoiceNum($invoice_num){
        $keys = array('invoice_num');
        $vals = array($invoice_num);
        $ret=ApiModel::finance_api('getInvoiceInfoByInvoiceNum',$keys,$vals);
		if(!$ret['data']){
			return array();
		}else{
			return $ret['data'];
		}
    }

	public static function updateInvoiceInfoByInvoiceNum($invoice_num,$updatedata){
        $keys = array('invoice_num','updatedata');
        $vals = array($invoice_num,$updatedata);
        $ret=ApiModel::finance_api('updateInvoiceInfoByInvoiceNum',$keys,$vals);
        return $ret;
	}

	public static function deleteInvoiceInfoByInvoiceNum($invoice_num){
        $keys = array('invoice_num');
        $vals = array($invoice_num);
        $ret=ApiModel::finance_api('deleteInvoiceInfoByInvoiceNum',$keys,$vals);
        return $ret;
	}

   static public function getPaySnExt($attach_sn){
        $keys = array('attach_sn');
        $vals = array($attach_sn);
        $ret=ApiModel::finance_api('getPaySnExt',$keys,$vals);
//var_dump($attach_sn);
        return $ret;
    }

    static public function cerateOrderPayAction($info){
        $keys = array('insertdata');
        $vals = array($info);
        $ret=ApiModel::finance_api('CerateOrderPayAction',$keys,$vals);
        return $ret;
    }

}

?>
