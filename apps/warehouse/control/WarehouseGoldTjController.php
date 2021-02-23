<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseGoldTjController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-07-17 01:39:49
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseGoldTjController extends CommonController
{
    protected $smartyDebugEnabled = true;
	protected $whitelist = array("search");

    /**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('warehouse_gold_tj_search_form.html',array(
			'bar'=>Auth::getBar(),
			'dd'=> new DictView(new DictModel(1))
		));
	}
	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'				=> _Request::get("mod"),
			'con'				=> substr(__CLASS__, 0, -10),
			'act'				=> __FUNCTION__,
                        'zdj'=> _Request::get("zdj"),
                        'down_info' => 	_Request::get('down_info')?_Request::get('down_info'):'',
                        'start_time' =>_Request::get("start_time"),
                        'end_time' =>_Request::get("end_time"),
		);
                $model = new WarehouseBillModel(21);
	        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
                  $where = array(
                       'start_time' =>$args['start_time'],
                       'end_time' =>$args['end_time']
                   );
                    
               if($args['zdj'] == 'real_all' && $args['down_info'] == 'down_info'){
                 $data = $model->getRealData($where,$page,90000000,false);; //实时总库存数据
                 $this->exportData($data);
               }   
                 if($args['zdj'] == 'real_all'){ 
                         $data = $model->getRealData($where,$page,90000000,false); //实时总库存数据 
						  
                         $show = 'warehouse_bill_gold_all_list.html';
                 }
	       if($args['zdj'] == 'sales' && $args['down_info'] == 'down_info'){
                  
                     $data = $model->getGoldlData($where,$page,90000000,false); //黄金销售数据
                      $this->exportGLData($data);
                  } 
		  if($args['zdj'] == 'sales'){
                       $data = $model->getGoldlData($where,$page,90000000,false); //黄金销售数据 
					   $data['start_time'] =$args['start_time']; 
					   $data['end_time'] = $args['end_time'];
                       $show = 'warehouse_bill_gold_gl_list.html';
                  }

                  if($args['zdj'] == 'from_data' && $args['down_info'] == 'down_info'){
                  
                     $data = $model->getGoldltData($where,$page,90000000,false); //黄金销售数据
                      $this->exportGLtData($data);
                  } 

                  if($args['zdj'] == 'from_data'){
                       $data = $model->getGoldltData($where,$page,90000000,false); //黄金销售数据 
                       $data['start_time'] =$args['start_time']; 
                       $data['end_time'] = $args['end_time'];
                       $show = 'warehouse_bill_gold_l_list.html';
                  }
                
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'gold_search_page';
		$this->render($show,array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'admin_name'=>$_SESSION['userName'],
			'dd'=> new DictView(new DictModel(1)) 
		));
	}
	public function exportData($data) {
	        $newdata = $data['data'];
		if ($newdata) { 
			$down = $newdata; 
                        $xls_content = "商品渠道,库存数量,总成本,总金重\r\n"; 
			foreach ($down as $key => $val) { 
					$xls_content .= $val['channel']. ",";
					$xls_content .= $val['sku']. ",";
					$xls_content .= $val['cost']. ",";  
					$xls_content .= $val['gold'] . "\n";
			}
		} else {
			$xls_content = '没有数据！';
		}
              
                header("Content-type:text/csv;charset=gbk");
                header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "黄金库存数据导出" . date("Y-m-d")) . ".csv");
                header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
                header('Expires:0');
                header('Pragma:public');
                echo iconv("utf-8", "gbk//IGNORE", $xls_content); 
                exit;
            
	}
	 
        public function exportGLData($data){
               $newdata = $data['data'];
		if ($newdata) { 
			$down = $newdata; 
                        $xls_content = "日期,库房,款号,数量,金重,成本,买入工费,销售价,是否结价\r\n"; 
			foreach ($down as $key => $val) { 
					$xls_content .= $val['check_time']. ",";
					$xls_content .= $val['warehouse']. ",";
					$xls_content .= $val['goods_sn']. ",";  
                                        $xls_content .= $val['num']. ",";  
                                        $xls_content .= $val['gold']. ",";  
                                        $xls_content .= $val['chengbenjia']. ",";   
                                        $xls_content .= $val['mairugongfei']. ","; 
                                        $xls_content .= $val['shijia']. ","; 
					$xls_content .= $val['is_settle'] . "\n";
			}
		} else {
			$xls_content = '没有数据！';
		}
              
                header("Content-type:text/csv;charset=gbk");
                header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "黄金销售数据导出" . date("Y-m-d")) . ".csv");
                header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
                header('Expires:0');
                header('Pragma:public');
                echo iconv("utf-8", "gbk//IGNORE", $xls_content); 
                exit;
        }

        public function exportGLtData($data){
               $newdata = $data['data'];
        if ($newdata) { 
            $down = $newdata; 
                        $xls_content = "日期,入库单号,供应商,件数,克重,金价,工费,入库仓\r\n"; 
            foreach ($down as $key => $val) { 
                    $xls_content .= $val['check_time']. ",";
                    $xls_content .= $val['bill_no']. ",";
                    $xls_content .= $val['pro_name']. ",";  
                                        $xls_content .= $val['num']. ",";  
                                        $xls_content .= $val['gold']. ",";  
                                        $xls_content .= $val['chengbenjia']. ",";   
                                        $xls_content .= $val['mairugongfei']. ","; 
                                        $xls_content .= $val['to_warehouse_name']. "\n";
            }
        } else {
            $xls_content = '没有数据！';
        }
              
                header("Content-type:text/csv;charset=gbk");
                header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "黄金入库数据导出" . date("Y-m-d")) . ".csv");
                header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
                header('Expires:0');
                header('Pragma:public');
                echo iconv("utf-8", "gbk//IGNORE", $xls_content); 
                exit;
        }
}

?>