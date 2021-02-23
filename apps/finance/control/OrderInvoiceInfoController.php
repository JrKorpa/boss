<?php
/**
 *  订单发票列表管理
 *  -------------------------------------------------
 *   @file		: ShipFreightController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class OrderInvoiceInfoController extends CommonController
{
    protected $smartyDebugEnabled = false;
    protected $whitelist = array('showFapiao');
    /**
     *	index，搜索框
     */
    public function index ($params)
    {
        $this->render('order_invoice_info_search_form.html',array('bar'=>Auth::getBar()));
    }

    /**
     *  search，列表
     */
    public function search ($params)
    {
        $args = array(
            'mod'   => _Request::get("mod"),
            'con'   => substr(__CLASS__, 0, -10),
            'act'   => __FUNCTION__,
            //'参数' = _Request::get("参数");
            'is_invoice'=>1,//需要开发票
            'invoice_type'=>_Request::getInt('invoice_type'),
            'order_sn'=>  _Request::getString('order_sn'),
            'invoice_num'=>  _Request::getString('invoice_num'),
            'invoice_title'=>  _Request::getString('invoice_title'), 
            'invoice_content'=> _Request::getString('invoice_content'),
            'invoice_status'=>  _Request::get('invoice_status'),
            'amount_min'=>  _Request::getfloat('amount_min'),
            'amount_max'=>  _Request::getfloat('amount_max'),
            'create_time_start'=>  _Request::getString('create_time_start'),
            'create_time_end'=>  _Request::getString('create_time_end'),
            'create_user'=>  _Request::getString('create_user'),            

        );
        $page = _Request::getInt("page",1);
        $where = $args;        
        $model = new AppOrderInvoiceModel(27);
        $data = $model->pageList($where, $page,10);
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'order_invoice_info_search_page';
        $this->render('order_invoice_info_search_list.html',array(
            'pa'=>Util::page($pageData),
            'page_list'=>$data
        ));
    }
    
    /**
     * 电子发票批量导入 渲染模板
     * @param unknown $parmams
     */
    public function importInvoice($parmams){
        $result = array('content'=>'','title'=>'Excel导入订单发票');
        $result['content'] = $this->fetch('order_invoice_info_import.html');
        Util::jsonExit($result);
    }
    
    /**
     * 电子发票批量导入 保存
     * @param unknown $parmams
     */
    public function importInvoiceSave($parmams){
        $result = array('success'=>0,'error'=>'');
        if(empty($_FILES['file']['tmp_name'])){
            $result['error'] = "请上传文件";
            Util::jsonExit($result);
        }
        $file = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];
        $file_ext  = Upload::getExt($file_name); 
        if (!in_array($file_ext,array("xls","xlsx"))) {
            $result['error'] = '文件格式错误！请上传.xlsx 或  .xls 后缀excel文件';
            Util::jsonExit($result);
        }         
        include_once(KELA_ROOT.'/frame/PHPExcel/PHPExcel.php');
        include_once(KELA_ROOT.'/frame/PHPExcel/PHPExcel/IOFactory.php');
        include_once(KELA_ROOT.'/frame/PHPExcel/PHPExcel/Reader/Excel5.php');
        if($file_ext =='xlsx'){
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format
        }else{
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
        }
        $objPHPExcel = $objReader->load($file);//读取文件
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();//总行数       
        
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);//总列数
        $model = new AppOrderInvoiceModel(27);
        $data=array();
        if($highestColumnIndex == 30){        
            for ($i = 2;$i <= $highestRow;$i++)
            {
                $row=array();
                $number = $objWorksheet->getCell("Z{$i}")->getValue();//发票数量
                if($number<1){
                    continue;
                }
                $row["open_sn"]= $objWorksheet->getCell("A{$i}")->getValue();//外部发票流水号
                $row["order_sn"] = $objWorksheet->getCell("C{$i}")->getValue();//订单编号
                $row["invoice_num"] = $objWorksheet->getCell("N{$i}")->getValue();//发票号
                $row["invoice_amount"] = $objWorksheet->getCell("AB{$i}")->getValue();//含税金额     
                if(empty($row["open_sn"]))  {
                    $result['error'] = "第{$i}行，A列,流水号不能为空";
                    Util::jsonExit($result);
                }
                
                if(empty($row["order_sn"]))  {
                    $result['error'] = "第{$i}行，C列，订单编号不能为空";
                    Util::jsonExit($result);
                } 
                if(empty($row["invoice_num"]))  {
                    $result['error'] = "第{$i}行，N列，发票编号不能为空";
                    Util::jsonExit($result);
                }                    
                $data[$i] = $row;
            }
        }else if($highestColumnIndex >=34){
            for ($i = 2;$i <= $highestRow;$i++)
            {
                $row=array();
                $number = $objWorksheet->getCell("AD{$i}")->getValue();//发票数量
                if($number<1){
                    continue;
                }
                $row["open_sn"]= $objWorksheet->getCell("A{$i}")->getValue();//外部发票流水号
                $row["order_sn"] = $objWorksheet->getCell("C{$i}")->getValue();//订单编号
                $row["invoice_num"] = $objWorksheet->getCell("R{$i}")->getValue();//发票号
                $row["invoice_amount"] = $objWorksheet->getCell("AF{$i}")->getValue();//含税金额
                if(empty($row["open_sn"]))  {
                    $result['error'] = "第{$i}行，A列,流水号不能为空";
                    Util::jsonExit($result);
                }
                if(empty($row["order_sn"]))  {
                    $result['error'] = "第{$i}行，C列，订单编号不能为空";
                    Util::jsonExit($result);
                }
                if(empty($row["invoice_num"]))  {
                    $result['error'] = "第{$i}行，R列，发票编号不能为空";
                    Util::jsonExit($result);
                }
                $data[$i] = $row;
            }
        }else{
            $result['error'] = "文件内容格式不对！ ".$highestColumnIndex;
            Util::jsonExit($result);
        }
        //基础校验
        foreach ($data as $i =>$row){
            $orderInvoiceList = $model->getOrderInvoice($row["order_sn"]);
            if(empty($orderInvoiceList)){
                $result['error'] = "第{$i}行，C列,订单号{$row['order_sn']}不存在";
                Util::jsonExit($result);
            }
            $order_amount = $orderInvoiceList[0]['order_amount'];
            if($row["invoice_amount"] > $order_amount){
                $result['error'] = "第{$i}行,发票金额【{$row["invoice_amount"]}】不能大于订单金额【{$order_amount}】。<br/>订单号{$row['order_sn']}";
                Util::jsonExit($result);
            }
            foreach ($orderInvoiceList as $vo){
                //需要开发票
                if($vo['open_sn']==$row["open_sn"]){
                    $row["id"] = $vo['id'];//发票流水ID
                    $row["order_id"] = $vo['order_id'];//订单ID
                    break;
                }else if($vo['is_invoice']==1 && $vo['invoice_status']!=3){
                    $row["id"] = $vo['id'];//发票流水ID
                    $row["order_id"] = $vo['order_id'];//订单ID
                }
            }
            if(empty($row["id"])){
                $result['error'] = "第{$i}行,C列,订单号{$row['order_sn']}不符合开票条件，订单可能不需要开票 或  订单没有发票记录";
                Util::jsonExit($result);
            }
            $data[$i] = $row;
        }
        //$result['error'] = var_export($row,true);
        //Util::jsonExit($result);
        //开启事物
        $pdolist[27] = $model->db()->db();
        try{
            //开启事物
            foreach ($pdolist as $pdo){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                $pdo->beginTransaction(); //开启事务
            }        
            //数据保存处理
            foreach ($data as $i =>$row){
                $res = $model->setInvoiceNum($row); 
                if($res['success']==0){
                    $error = "第{$i}行，导入发票失败! ".$res['error'];
                    Util::rollbackExit($error,$pdolist);
                }           
            }
            //提交事物
            foreach ($pdolist as $pdo){
                $pdo->commit();
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
            }
            $result['success'] = 1;
            Util::jsonExit($result);
        }catch (Exception $e){
            $result['error'] ="操作失败，事物回滚！".$e->getMessage();
            Util::jsonExit($result);
        }
        
    }    
    public function showFapiao(){
            $invoice_num = _Request::getString("invoice_num");
            $order_sn = _Request::getString("order_sn");
                    include_once(APP_ROOT."shipping/modules/invoice_api/invoice.php");
                    include_once(APP_ROOT."shipping/modules/invoice_api/DESDZFP.class.php");
                    if(!empty($invoice_num))
                       $inv_res = Invoice::searchInvoiceNum($invoice_num);
                    if(!empty($order_sn))
                       $inv_res = Invoice::searchOrder($order_sn);
                   
                    //echo '['.$invoice_num.']';
                    //echo "<pre>";
                    //print_r($inv_res);
                    if(is_array($inv_res) && $inv_res['result']=='success' && !empty($inv_res['list'][0]['c_url'])){
                        $result['content'] ="<iframe height='600' width='100%' src='".$inv_res['list'][0]['c_url']."' id='iframepage' scrolling='no' frameborder='0' onload'changeFrameHeight()'></iframe>";
                        $result['title'] = '电子发票文件';
                    }else{
                        $result['content']="未找到相关电子发票";
                        $result['title'] = '未找到相关电子文件';
                    }  

        Util::jsonExit($result);

    }    

}

?>