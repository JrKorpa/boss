<?php
/**
 *  -------------------------------------------------
 *   @file		: ShipFreightController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class OrderDownloadInfoController extends CommonController
{
    protected $smartyDebugEnabled = false;
    protected $whitelist = array("orderSearch");
    /**
     *	index，搜索框
     */
    public function index ($params)
    {
        $this->render('order_download_info_search_form.html',array('bar'=>Auth::getBar()));
    }

    /**
     * 查询订单信息
     */
    public function orderSearch($params){
        $where=array(
            'order_sn'=>_Request::get('order_no'),
            );
        $item=null;
        if($where['order_sn'] != "")
        {
            //add by zhangruiying 去除用户不小心输入或粘贴的空白字符和中文,号替换
            $where['order_sn']=preg_replace("/[sv]+/",'',$where['order_sn']);
            $where['order_sn']=str_replace(" ",',',$where['order_sn']);
            $where['order_sn']=str_replace("，",',',$where['order_sn']);
            //add end
            $item =explode(",",$where['order_sn']);
            $goodsid = "";
            foreach($item as $key => $val) {
                if ($val != '') {
                    if($goodsid){
                        $goodsid .= ",'".trim($val)."'";
                    }else{
                        $goodsid .= "'".trim($val)."'";
                    }
                }
            }
            $where['order_sn'] = $goodsid;
            $where['order_sn']= " AND  o.order_sn in (".$where['order_sn'].") ";
        }

        $invoiceModel = new BaseInvoiceInfoModel(29);
        $invoiceOrderArray=$invoiceModel->getdownloadElecGoods($where);
        $date=[];


        foreach ($item as $key => $value) {
           $orderAmount=0;
            $index=0;
           $state=array('index'=>0,'amount'=>0);
           foreach ($invoiceOrderArray as $k => $v) {
               if($value==$v['order_sn']){
                  if($v['favorable_status']==3){
                      $v['goods_price']=sprintf("%1.2f",$v['goods_price']-$v['favorable_price']);//商品实际金额
                  }else{
                      $v['goods_price']=sprintf("%1.2f",$v['goods_price']);
                  }
                  $orderAmount+= $v['goods_price'];
                  //业务需要  这里算的是发票金额
                  $date[$value][]=$v;
                  if($v['goods_price']>$state['amount']){
                    $state['index']=$index;
                    $state['amount']=$v['goods_price'];
                  }
                  $index++;
               }
           };
           if(!($orderAmount==$date[$value][0]['invoice_amount'])){
                $differ=$orderAmount-$date[$value][0]['invoice_amount'];
                $date[$value][$state['index']]['goods_price']= $date[$value][$state['index']]['goods_price']-$differ;
           }
        }
        $this->orderDownloadGoods($date);
    }
    public function orderDownloadGoods(array $invoiceOrderArray){
        $path = '/frame/PHPExcel/PHPExcel.php';
        $pathIo = '/frame/PHPExcel/PHPExcel/IOFactory.php';
        include_once(KELA_ROOT.$path);
        include_once(KELA_ROOT.$pathIo);
        // 创建一个处理对象实例
        $objPhpExcel = new PHPExcel();
        // 创建文件格式写入对象实例, uncomment
        $objWriter = new PHPExcel_Writer_Excel2007($objPhpExcel); // 用于其他版本格式
        // 设置一个当前活动页
        $objPhpExcel->setActiveSheetIndex(0);
        //获取活动页
        $objSheet=$objPhpExcel->getActiveSheet();
        $objSheet->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objSheet->getStyle('B')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objSheet->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objSheet->getStyle('T')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objSheet->getStyle('M')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objSheet->getStyle('U')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objSheet->getStyle('Z')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objSheet->getStyle('AA')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objSheet->getStyle('AB')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objSheet->getStyle('AC')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objSheet->getStyle('AD')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $title=array(
            array('销方税号','订单号','订单时间','开票类型','客户类型','客户名称','客户税号','客户地址','客户电话','客户手机','客户银行、账号','客户邮箱','备注','原发票代码','原发票号码','商品编号','数量','单价','金额(含税)','税额','折扣金额','商品名称','税率','规格型号','计量单位','税务编码','税务编码版本号','是否享受税收优惠政策','税收优惠政策内容','零税率标识')
        );
        //用于把款式分类转换成票据类型
        $styleInfo=array(
            '彩钻'=>array('成品钻','1060509020100000000','克拉'),
            '裸石(统包货)'=>array('成品钻','1060509020100000000','克拉'),
            '裸石'=>array('成品钻','1060509020100000000','克拉'),
            '裸石(镶嵌物)'=>array('成品钻','1060509020100000000','克拉'),
            '男戒'=>array('男戒','1060509010000000000','枚'),
            '女戒'=>array('女戒','1060509010000000000','枚'),
            '情侣戒'=>array('情侣戒','1060509010000000000','枚'),
            '戒指'=>array('戒指','1060509010000000000','枚'),
            '吊坠'=>array('吊坠','1060509010000000000','个'),
            '耳钉'=>array('耳饰','1060509010000000000','对'),
            '耳钩'=>array('耳饰','1060509010000000000','对'),
            '耳饰'=>array('耳饰','1060509010000000000','对'),
            '金条'=>array('金条','1020401990000000000','克'),
            '手链'=>array('手链','1020401990000000000','件'),
            '手镯'=>array('手镯','1060509010000000000','个'),
            '项链'=>array('项链','1060509010000000000','条'),
            '套装'=>array('套装','1020401990000000000','件'),
            '其它'=>array('饰品','1060509010000000000','件'),
            '其他'=>array('饰品','1060509010000000000','件'),
            '摆件'=>array('摆件','1020401990000000000','件'),
            //'脚链'=>array('脚链',),
            );
        $objSheet->fromArray($title);
        $invoiceModel = new BaseInvoiceInfoModel(29);

        $key=1;
        foreach ($invoiceOrderArray as $k => $v) {
            $orderAmount=0;
            foreach ($v as $index => $value) {
                $key=$key+1;
                $goods_price=0;
                $unit_price=0;
                $style=null;
                $cat_type="cat_type";
                if(empty($value['cat_type'])){
                    $cat_type="cat_type_name";
                }

                $unit_price=sprintf("%1.2f",empty($value['num'])?0:($value['goods_price']/$value['num']));
                if(!empty($value['cat_type_name'])){
                    $style=$styleInfo[$value['cat_type_name']];
                }else if(!empty($value['cat_type'])){
                    $style=$styleInfo[$value['cat_type']];
                }else if(!empty($value['goods_sn'])&&$value['goods_sn']=='DIA'){//款式号
                      $style=$styleInfo['裸石']; 
                }else{
                     $style=array(' ',' ',' ');
                }
              /*  $objSheet->getStyle('A'.$key)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $objSheet->getStyle('B'.$key)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);*/
                $objSheet->setCellValueExplicit('A'.$key,trim("913101146778859129"),PHPExcel_Cell_DataType::TYPE_STRING);
                //$objSheet->setCellValue('A'.$key,trim("'913101146778859129"));
                $objSheet->setCellValue('B'.$key,$value['order_sn']);
                $objSheet->setCellValue('C'.$key,date('Y/m/d g:i:s'));
                $objSheet->setCellValue('D'.$key,'1');
                $objSheet->setCellValue('E'.$key,$value['invoice_title']=='个人'?'1':'2');
                $objSheet->setCellValue('F'.$key,$value['invoice_title']);//发票抬头
                $objSheet->setCellValue('G'.$key,$value['taxpayer_sn']);
                $objSheet->setCellValue('H'.$key,'');//客户地址
                $objSheet->setCellValue('I'.$key,'');//客户电话
                $objSheet->setCellValue('J'.$key,'');//客户手机
                $objSheet->setCellValue('K'.$key,'');//客户银行、账号
                $objSheet->setCellValue('L'.$key,$value['invoice_email']);
                $objSheet->setCellValue('M'.$key,$value['order_sn']);//备注
                $objSheet->setCellValue('N'.$key,'');//原发票代码
                $objSheet->setCellValue('O'.$key,'');//原发票号码
                $objSheet->setCellValue('P'.$key,'');//商品编号
                $objSheet->setCellValue('Q'.$key,$value['num']);
                $objSheet->setCellValue('R'.$key,$unit_price);//单价
                $objSheet->setCellValue('S'.$key,$value['goods_price']);//货品含税价格
                $objSheet->setCellValue('T'.$key,'');//税额
                $objSheet->setCellValue('U'.$key,'');//折扣金额
                $objSheet->setCellValue('V'.$key,$style[0]);//商品名称
                $objSheet->setCellValue('W'.$key,'0.16');//税率
                $objSheet->setCellValue('X'.$key,'');//规格型号
                $objSheet->setCellValue('Y'.$key,$style[2]);//计量单位
                $objSheet->setCellValue('Z'.$key,$style[1]);//税务编码
                $objSheet->setCellValue('AA'.$key,'13.0');
                $objSheet->setCellValue('AB'.$key,'0');
                $objSheet->setCellValue('AC'.$key,'');
                $objSheet->setCellValue('AD'.$key,'');
            } 

        }
        /*----------------------*/
        $ymd = date("Ymd_His", time()+8*60*60);
        include_once(KELA_ROOT.$pathIo);
        $outputFileName = $ymd.'.xlsx';
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$outputFileName.'"');
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
        exit;
    }
}

?>