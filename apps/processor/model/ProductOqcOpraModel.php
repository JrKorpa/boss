<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductOqcOpraModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-12 12:05:59
 *   @update	:
 *  -------------------------------------------------
 */
class ProductOqcOpraModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'product_oqc_opra';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"bc_id"=>"布产号",
"oqc_result"=>"OQC结果",
"oqc_reason"=>"OQC未过原因",
"oqc_info"=>"操作备注",
"opra_uid"=>"操作人ID",
"opra_uname"=>"操作人",
"opra_time"=>"操作时间");
		parent::__construct($id,$strConn);
	}

	function pageList ($where)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";

		if($where['bc_id'] !== "")
		{
			$sql .= " AND bc_id = ".$where['bc_id'];
		}

		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getAll($sql);
		return $data;
	}


	/**
	* 添加OQC质检
	* @param bc_id_arr Array 布产单ID
	* @param  oqc_data Array 质检结果集
	*/
	public function AddOqcAction($bc_id_arr , $oqc_data){
		$dd = new DictModel(1);
		$pro_model = new ProductInfoModel(14);	//实例化布产单对象
		$model_pw = new AppProcessorWorktimeModel(13);
		$logModel = new ProductOpraLogModel(14);
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
            $oqc_num = $oqc_data['oqc_num'];
            $oqc_bf_num = $oqc_data['oqc_bf_num'];
            $oqc_no_num = $oqc_data['oqc_no_num'];
            $reason_scrapped = $oqc_data['reason_scrapped'];
			//OQC质检操作
			foreach($bc_id_arr AS $bc_id){
				#质检未通过 如果是订单加时则需修改标准出厂时间，在当前时间小于标准出厂时间 4订单问题
				//OQC未过，订单问题。在未超期的情况下加时(订单问题加时)。如果已经超期了，就不加时了。
				$data = $pro_model->Select2($fields=' `esmt_time`,`prc_id`,`buchan_times`,`num`,`from_type` ' , $where= " `id`={$bc_id} " , $type = 'row');	//获取布产标准出厂时间
                $bc_num = $data['num'];
                $from_type = $data['from_type'];

                //出库数量+报废数量 =总数量
                $other_num=$shenyu_num=$oqc_no_num_all=0;
                $oqc_num_all=$oqc_bf_num_all=0;
                $sql="select sum(oqc_num) as num,sum(oqc_bf_num) as bf_num,sum(oqc_no_num) as oqc_no_num from product_oqc_opra where bc_id='$bc_id'";
                foreach ($this->db()->query($sql) as $row2){
                    //if( $oqc_num+$oqc_bf_num+$oqc_no_num > $bc_num-$row2['oqc_num']-$row2['oqc_bf_num']-$row2['oqc_no_num'] )
                    //{
                        //throw new Exception("出厂数量/报废数量/质检未过数量和$oqc_num + $oqc_bf_num + $oqc_no_num 超过未出厂数量". ($bc_num-$row2['oqc_num']-$row2['oqc_bf_num']) .",不允许操作。");
                    //}
                    $oqc_num_all =  $oqc_num_all + $row2['num'];
                    $oqc_bf_num_all = $oqc_bf_num_all + $row2['bf_num'];
                    //$other_num=$bc_num-$row2['num']-$row2['bf_num'];    
                    $oqc_no_num_all =$oqc_no_num_all+$row2['oqc_no_num'];  

                } 
                //$other_num= $other_num-$oqc_num-$oqc_bf_num;
                //$shenyu_num = $bc_num-$other_num;
                $oqc_num_all =  $oqc_num_all + $oqc_num ;
                $oqc_bf_num_all = $oqc_bf_num_all + $oqc_bf_num ;
                $oqc_no_num_all =$oqc_no_num_all+$oqc_no_num;
                $shenyu_num = $bc_num - $oqc_num_all - $oqc_bf_num_all ;
                //var_dump($other_num, $shenyu_num, $oqc_no_num_all);die;
                if($shenyu_num > 0    && $shenyu_num < $bc_num){
                    $oqc_result = '部分质检通过';
                    $buchan_fac_opra = 17;
                }elseif($shenyu_num > 0 && $shenyu_num == $bc_num){
                    $oqc_result = "质检未过";
                    $buchan_fac_opra = 5;
                }elseif($shenyu_num == 0){
                    $oqc_result = "质检通过";
                    $buchan_fac_opra = 4;
                }

                if($from_type == 2 && $oqc_data['oqc_result']==1){
                    $oqc_result = "质检通过";
                    $buchan_fac_opra = 4;
                }
				if ($oqc_data['oqc_result'] !=1 &&  $oqc_data['oqc_reason'] == 4 && date('Y-m-d') <=  $data['esmt_time'])
				{
					//1：问题订单(计算方式 标准出厂时间加上订单加时时间)（计算工作日）5
					//2、等钻加时 当前时间加上等钻加时时间 （不计算工作日）25 ；
					//3、等钻后加时 当前时间加上等钻后加时时间（计算工作日）5
					//4、开始生产设置标准出厂时间，根据默认设置标准出厂时间（7+1天23:59:59）或者提示设置 date("Y-m-d 23:59:59",time()+24*3600*8); 有则按工作日计算天数
					#工厂加时信息获取
					$order_problem = 0;
					$is_rest       = 1;//不休
					$row   = $model_pw->getInfoById($data['prc_id']);
					if ($row)
					{
						$order_problem = $row['order_problem'];
						$is_rest	   = $row['is_rest'];
					}
					$str_time = strtotime($data['esmt_time']);
					$time = $model_pw->js_normal_time($order_problem , $is_rest , $str_time);
					
					$remark = "OQC操作：".$oqc_result." ".$dd->getEnum('OQC_reason',$oqc_data['oqc_reason'])."，备注：".$oqc_data['oqc_info'];
					//add by zhangruiying
					$edit_time = date('Y-m-d H:i:s');
					$sql = "UPDATE `product_info` SET `esmt_time` = '{$time}' , `edit_time` = '{$edit_time}' ,  `remark` = '{$remark}' , `buchan_fac_opra` = {$buchan_fac_opra}  WHERE `id` = {$bc_id}";
					//add end
					$pdo->query($sql);
				}else{
					/*if($oqc_data['oqc_result'] == 1){
						$oqc_result = '质检通过';
						$buchan_fac_opra = 4;
					}else{
						$oqc_result = "质检未过";
						$buchan_fac_opra = 5;
					}*/
					$remark = "OQC操作：".$oqc_result." ".$dd->getEnum('OQC_reason',$oqc_data['oqc_reason'])."，备注：".$oqc_data['oqc_info'];
					$edit_time = date('Y-m-d H:i:s');
                   	$sql = "UPDATE `product_info` SET `edit_time` = '{$edit_time}' ,  `remark` = '{$remark}' , `buchan_fac_opra` = {$buchan_fac_opra} WHERE `id` = {$bc_id}";
					$pdo->query($sql);
					//add end
				}

				//写OQC操作日志
				$oqc_reason = $oqc_data['oqc_result'] == 1 ? 0 : $oqc_data['oqc_reason'];
				$opra_time = date("Y-m-d H:i:s");
				if(!isset($data['buchan_times']) || $data['buchan_times']=="")
					$data['buchan_times']=1;
				$sql = "INSERT INTO `product_oqc_opra` (`bc_id` , `oqc_num` , `oqc_bf_num` , `oqc_no_num` , `reason_scrapped` , `oqc_result` , `oqc_reason` , `oqc_info` , `opra_uid` , `opra_uname` , `opra_time`,`buchan_times`) VALUES ( {$bc_id} , '{$oqc_num}' , '{$oqc_bf_num}' , '{$oqc_no_num}' , '{$reason_scrapped}' , {$oqc_data['oqc_result']} , {$oqc_reason} , '{$oqc_data['oqc_info']}' , {$_SESSION['userId']} , '{$_SESSION['userName']}' , '{$opra_time}','{$data['buchan_times']}')";
				//file_put_contents('d://lyh.txt',$sql,FILE_APPEND);
				//echo $sql;
				$pdo->query($sql);

				//记录布产日志+ 回写订单操作日志
				$oqc_result = $oqc_data['oqc_result'] ? "质检通过" : "质检未过";
				$oqc_info = $oqc_data['oqc_info'] ? $oqc_data['oqc_info'] : '无';
                                $oqc_reason = $this->getCatName($oqc_reason);
                                $oqc_problem = $this->getCatName($oqc_data['oqc_problem']);
                                if (empty($oqc_data['oqc_info'])) {
                                  //  $logModel->addLog($bc_id , 4 , "OQC操作：".$oqc_result." ".$oqc_reason."&nbsp;".$oqc_problem);
                                     $logModel->addLog($bc_id , "布产单OQC操作：".$oqc_result." ".$oqc_reason."&nbsp;".$oqc_problem);                                   
                                    //$pro_model->Writeback($bc_id, "OQC操作：".$oqc_result." ".$oqc_reason."&nbsp;".$oqc_problem);	//回写订单操作日志 BY hulichao
                                }else {
                                   // $logModel->addLog($bc_id , 4 , "OQC操作：".$oqc_result." ".$oqc_reason."&nbsp;".$oqc_problem."，备注处理：".$oqc_info);
                                    $logModel->addLog($bc_id ,  "布产单OQC操作：".$oqc_result." ".$oqc_reason."&nbsp;".$oqc_problem."，备注处理：".$oqc_info);
                                    //$pro_model->Writeback($bc_id, "OQC操作：".$oqc_result." ".$dd->getEnum('OQC_reason',$oqc_reason)."，备注：".$oqc_info);	//回写订单操作日志 BY hulichao
                                  //  $pro_model->Writeback($bc_id, "OQC操作：".$oqc_result." ".$oqc_reason."&nbsp;".$oqc_problem."，备注处理：".$oqc_info);	//回写订单操作日志 BY hulichao
                                }
			}
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error' => '事物执行不成功，操作失败'.$e->getMessage());
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return  array('success' => 1 , 'error' => '操作成功');
	}
        public function getCatName($id) {
            $sql = "select `cat_name` from `product_fqc_conf` where `id`='{$id}' and `is_deleted`=0";
            $data = $this->db()->getRow($sql);
            return $data['cat_name'];
        }
        

}?>