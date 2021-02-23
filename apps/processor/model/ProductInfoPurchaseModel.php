<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangruiying
 *   @date		: 2015/5/8
 *   @update	:
 *  -------------------------------------------------
 */
class ProductInfoPurchaseModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
        $this->_objName = 'product_info';
		$this->pk='id';
		$this->_prefix='';
		$this->_dataObject = array();
		parent::__construct($id,$strConn);
	}
	//根据查询条件生成SQL语句
	public function getListSql($where)
	{
		$sql = "SELECT main.p_sn,main.bc_sn,main.opra_uname,main.id,main.p_id,main.style_sn,main.status,main.esmt_time,main.order_time,main.edit_time,r.oqc_result,r.oqc_reason,cc.num,r.oqc_info,r.opra_uname as o_uname,r.opra_time,main.info,main.consignee,main.add_time,main.buchan_fac_opra,rc.cat_name,main.remark FROM `".$this->table()."` as main LEFT JOIN (SELECT t.bc_id,t.oqc_result,t.oqc_reason,t.oqc_info,t.opra_time,t.opra_uname FROM product_oqc_opra t INNER JOIN (SELECT bc_id, MAX(opra_time) AS MaxDate FROM product_oqc_opra GROUP BY bc_id ) tm ON t.bc_id = tm.bc_id AND t.opra_time=tm.MaxDate) as r ON r.bc_id=main.id LEFT JOIN product_fqc_conf as rc ON rc.id=r.oqc_reason LEFT JOIN (SELECT c.bc_id,COUNT(c.bc_id) as num FROM product_oqc_opra as c where c.oqc_result=0 group by c.bc_id) AS cc ON cc.bc_id=main.id";
		$str="";
		if(isset($where['p_sn']) and !empty($where['p_sn']))
		{
			$str.=" main.p_sn='{$where['p_sn']}' and";
		}
		if(isset($where['p_sn_ids']) and !empty($where['p_sn_ids']))
		{
			$str.=" main.p_sn in ({$where['p_sn_ids']}) and";
		}
		if(!empty($str))
		{
			$sql.=" where{$str}";
			$sql=rtrim($sql,'and');
		}
		return $sql;

	}
	public function GetListByPurchaseId($where,$page,$pageSize=10,$useCache=true)
	{
		$sql=$this->getListSql($where);
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,false);
		return $data;

	}
	public function GetAllByPurchaseId($where)
	{
		$sql="select main.p_sn,main.bc_sn,main.opra_uname,main.id,main.p_id,main.style_sn,main.status,main.esmt_time,main.order_time,main.edit_time,main.info,main.consignee,main.add_time,main.buchan_fac_opra,main.num,main.rece_time,main.xiangqian,main.remark,main.goods_name,main.prc_id,main.from_type from product_info as main";
		$str='';
		if(isset($where['p_sn']) and !empty($where['p_sn']))
		{
			$str.=" main.p_sn='{$where['p_sn']}' and";
		}
		if(isset($where['p_sn_ids']) and !empty($where['p_sn_ids']))
		{
			$str.=" main.p_sn in ({$where['p_sn_ids']}) and";
		}
		if(!empty($str))
		{
			$sql.=" where{$str}";
			$sql=rtrim($sql,'and');
		}
		$data = $this->db()->getAll($sql);
		return $data;
	}
	public function mutiUpdateProductionStatus($ids=array(),$prc_ids=array())
	{
		if($ids)
		{
			$pdo = $this->db()->db();
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			$edit_time=date('Y-m-d H:i:s');
			$sql="update product_info set buchan_fac_opra=2,status=4,order_time='{$edit_time}',edit_time='{$edit_time}' where id in(".implode(',',$ids).")";
			$res=$this->db()->query($sql);
			$insert_sql="insert into product_opra_log(`bc_id`,`status`,`remark`,`uid`,`uname`,`time`) values";
			foreach($ids as $id)
			{
					$insert_sql.=" ({$id},4,'采购单布产单开始生产',{$_SESSION['userId']},'{$_SESSION['userName']}','".date('Y-m-d H:i:s')."') ,";
			}
			$insert_sql=rtrim($insert_sql,',');

			$res1=$this->db()->query($insert_sql);
			$res2=true;
			if(!empty($prc_ids))
			{
				$model_pw = new AppProcessorWorktimeModel(13);
				foreach($prc_ids as $key=>$prc_id){
					$gendan_info = $model_pw->getInfoById($prc_id);
					if(empty($gendan_info))
					{
						$normal_time = time()+(3600*24);
						$time = date("Y-m-d",$normal_time);
					}
					else
					{
						$time = $model_pw->js_normal_time($gendan_info['normal_day'],$gendan_info['is_rest']);
					}
					$sql="update product_info set esmt_time='{$time}' where id in(".implode(',',$ids).") and prc_id={$prc_id}";
					$res2=$this->db()->query($sql);
				}

			}
			if($res!=false and $res1!=false and $res2!=false)
			{
				$pdo->commit();
				return true;
			}
			else
			{
				$pdo->rollback();
				return false;
			}
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
		}
		else
		{
			return false;
		}

	}
	//统计采购单下布产单的布产状态的个数
	public function getPurchaseProductInfo($ids)
	{
		if(!empty($ids))
		{
			$ids="'".implode("','",$ids)."'";
			$sql="select sum(case when `status`=1 then 1 else 0 end) as status1,sum(case when `status`=2 then 1 else 0 end) as status2,sum(case when `status`=3 then 1 else 0 end) as status3,sum(case when `status`=4 then 1 else 0 end) as status4,sum(case when `status`=5 then 1 else 0 end) as status5,sum(case when `status`=6 then 1 else 0 end) as status6,sum(case when `status`=7 then 1 else 0 end) as status7,sum(case when `status`=8 then 1 else 0 end) as status9,sum(case when `status`=9 then 1 else 0 end) as status9,sum(case when `status`=10 then 1 else 0 end) as status10,sum(case when `status`=11 then 1 else 0 end) as status11,p_sn from product_info where p_sn in($ids) group by p_sn order by status";
			$row=$this->db()->getAll($sql);
			$arr=array();
			if($row)
			{
				foreach($row as $key=>$v)
				{
					$arr[$v['p_sn']]=$v;
					unset($arr[$v['p_sn']]['p_sn']);
				}
				unset($row);
			}
			return $arr;
		}
		return false;
	}
	//统计采购单下布产单生产状态
	public function getPurchaseBuchanFacOpra($ids)
	{
		if(!empty($ids))
		{
			$ids="'".implode("','",$ids)."'";
			$sql="select sum(case when `buchan_fac_opra`=1 then 1 else 0 end) as status1,sum(case when `buchan_fac_opra`=2 then 1 else 0 end) as status2,sum(case when `buchan_fac_opra`=3 then 1 else 0 end) as status3,sum(case when `buchan_fac_opra`=4 then 1 else 0 end) as status4,sum(case when `buchan_fac_opra`=5 then 1 else 0 end) as status5,sum(case when `buchan_fac_opra`=6 then 1 else 0 end) as status6 from product_info where p_sn in($ids) order by status";
			$row=$this->db()->getAll($sql);
			$arr=array();
			if($row)
			{
				foreach($row as $key=>$v)
				{
					$arr[$v['p_sn']]=$v;
					unset($arr[$v['p_sn']]['p_sn']);
				}
				unset($row);
			}
			return $arr;
		}
		return false;
	}
	public function getPurchaseOpraUname($ids)
	{
		if(!empty($ids))
		{
			$ids="'".implode("','",$ids)."'";
			$sql="select distinct opra_uname,p_sn from product_info where p_sn in($ids)";
			$row=$this->db()->getAll($sql);
			$arr=array();
			if($row)
			{
				foreach($row as $key=>$v)
				{
					if(!empty($v['opra_uname']))
					{
						if(isset($arr[$v['p_sn']]))
						{
							$arr[$v['p_sn']].=','.$v['opra_uname'];
						}
						else
						{
							$arr[$v['p_sn']]=$v['opra_uname'];
						}
					}
				}
				unset($row);
			}
			return $arr;
		}
		return false;

	}
	
	public function getPurchaseList($ids)
	{
	    if (empty($ids)) return false;
	    
	    $sql = "SELECT `id`,`p_sn`,`t_id`,`p_sum`,`purchase_fee`,`put_in_type`,`make_uname`,`make_time`,`check_uname`,`check_time`,`p_status`,`p_info` FROM purchase.`purchase_info` WHERE is_style=1 and is_tofactory=1";
	    $sql.=" and id in({$ids})";

	    return $this->db()->getAll($sql);
	}
	
	public function getPurchaseType()
	{
	    $sql = "SELECT id,t_name FROM purchase.`purchase_type`";
	    $row=$this->db()->getAll($sql);
	    if($row)
	    {
	        $arr=array();
	        foreach($row as $key=>$v)
	        {
	            $arr[$v['id']]=$v['t_name'];
	        }
	        return $arr;
	    }
	    
	    return false;
	}
	
	public function getStyleXiangKouByWhere($filter) {	    
	    $where = " WHERE 1";
	    if(isset($filter['style_sn']) && !empty($filter['style_sn'])){
	        $style_sn=trim($filter['style_sn']);
	        $where .= " AND `style_sn` = '".$style_sn."'";
	    }else{
	        return false;
	    }
	    $xiangkou = '';
	    if(isset($filter['xiangkou']) && !empty($filter['xiangkou'])){
	        $xiangkou = trim($filter['xiangkou']);
	    }
	    
	    $res_data = array();
	    //查询商品详情
	    $sql = "select * from front.`rel_style_factory` " .$where." ;";
	    $row = $this->db()->getAll($sql);
	    if(empty($row)){
	        return false;
	    }
	    $is_mark = false;
	    //找对应镶口的
	    foreach ($row as $val){
	        if($val['xiangkou'] == $xiangkou){
	            $res_data = $val;
	            $is_mark = true;
	            break;
	        }
	    }
	    
	    //没有对应镶口的找默认工厂的
	    if(!$is_mark){
	        foreach ($row as $val){
	            if($val['is_def'] ==1){
	                $res_data = $val;
	                $is_mark = true;
	                break;
	            }
	        }
	    }
	    
       //返回信息
       return $res_data;
	}
	
		/* 根据石重 查找 最差值最近的镶口，返回对应的工厂信息
		$style_sn 款式编号
		$weight 石重
		$factory_id 指定的工厂id。不输入则返回默认工厂的最近接近镶口
	*/
	public function GetFactoryStyleFromXiangKou($style_sn,$weight,$factory_id = null){
		$factory_style = null;
		//$weight = 1.35;
		$styleApi = new ApiStyleModel();
		$fac_list =$styleApi->getFactryInfo($style_sn);
		$xiangkous = array(); //镶口数组
		$default_xiangkou = array();//默认数组

		foreach($fac_list as $fac){
			if ($factory_id){
				//指定工厂 那么我只算指定的工厂的镶口
				if ($fac['factory_id'] == $factory_id){
					//如果和指定的工厂一致直接储存镶口信息
					$xiangkous[$fac['f_id']] = $fac['xiangkou'];
				}else{
					//如果不是指定的工厂，那么去找关联的工厂镶口
					$appProcessorInfoModel = new AppProcessorInfoModel(13);
					$group_factories = $appProcessorInfoModel->getGroupProList($factory_id);
					if ($group_factories){
						foreach($group_factories as $key=>$val){
							$factories[] = $key;
						}
						if (in_array($fac['factory_id'], $factories))
						{
							//找到关联工厂镶口并储存下来
							$xiangkous[$fac['f_id']] = $fac['xiangkou'];
						}
					}
				}
			}else{
				//指定工厂 那么 算默认工厂的镶口
				if ($fac['is_factory'] == 1){
					//储存镶口信息
					$xiangkous[$fac['f_id']] = $fac['xiangkou'];
				}
			}
			if ($fac['is_factory'] == 1 && $fac['is_def'] == 1){
				//得到默认镶口信息
				$default_xiangkou = $fac;
			}
		}
		//倒序排序一下，最早的镶口在下面
		krsort($xiangkous);
		if (empty($weight)){
			//石重为空或者为0，
			if (!$factory_id){
				//没有分配工厂，则带出默认工厂的默认镶口的模号
				$factory_style = $default_xiangkou;
			}else{
				//取得分配工厂的最早一个镶口
				$last_key = "";
				foreach ($xiangkous as $key=>$xiangkou){
					$last_key = $key;
				}
				foreach($fac_list as $fac){
					if($fac['f_id'] == $last_key){
						$factory_style = $fac;
					}
				}
				
			}
		}else{
			$diffs = array();//差值
			foreach ($xiangkous as $key=>$xiangkou){
				$diffs[$key] = abs($weight-$xiangkou);//计算石重与每个镶口的差值
			}
			$min = empty($diffs)?array():min($diffs);
			$factoryIds = array();//目标工厂
			foreach ($diffs as $key=>$diff){
				if (strval($min) == strval($diff)){
					//算出最小差距镶口，得到工厂信息
					$factoryIds[] = $key;
				}
			}
			if (count($factoryIds) < 2){
				//最小差距镶口只有一个
				foreach($fac_list as $fac){
					if($fac['f_id'] == current($factoryIds)){
						$factory_style = $fac;
					}
				}
			}else{
			
				/*
				算出来有两个镶口都符合条件时
				根据上四下五规则（+0.04/-0.05）进行二次判断，
				选择离石重最近的一个镶口
				*/
				$new_xiangkous =  array();
				foreach($factoryIds as $factoryId){
					$new_xiangkous[strval($xiangkous[$factoryId] + 0.04)] = $factoryId;
					$new_xiangkous[strval($xiangkous[$factoryId] - 0.05)] = $factoryId;
				}
				$diffs = array();//差值
				foreach ($new_xiangkous as $key=>$new_xiangkou){
					$diffs[$key] = abs($weight-$key);//计算石重与每个镶口的差值
				}
				$min = min($diffs);//求最小差值

				foreach ($diffs as $key=>$diff){
					if ($min == $diff){
						$new_factory_xiangkou = $key;
					}
				}
				//注释太啰嗦了， 总之算出来两个中比较接近的一个
				foreach ($new_xiangkous as $key=>$new_xiangkou){
					if ($key == $new_factory_xiangkou){
						foreach($fac_list as $fac){
							if($fac['f_id'] == $new_xiangkou){
								$factory_style = $fac;
							}
						}
					}
				}
			}
		}
		return $factory_style;
	
	}
	
	public function getStyleGalleryInfo($filter)
	{
	    $sql = "select `sg`.`g_id`, `sg`.`style_id`, `sg`.`image_place`, `sg`.`img_sort`, `sg`.`img_ori`, `sg`.`thumb_img`, `sg`.`middle_img`, `sg`.`big_img` from front.`base_style_info` as `si`,front.`app_style_gallery` as `sg`" .
	        " where `si`.`style_id`=`sg`.`style_id`";
	
	    if(!empty($filter['style_sn'])){
	        $sql .= " and `si`.`style_sn` = '".$filter['style_sn']."'";
	    }else{
	        return false;
	    }
	    if(isset($filter['image_place'])){
	        $image_place = (int)$filter['image_place']?(int)$filter['image_place']:1;
	        $sql .= " and `sg`.`image_place`={$filter['image_place']}";
	    }
	    $sql .= " order by `sg`.`image_place` asc";
	    $data = $this->db()->getAll($sql);
	
	    return $data;
	}
	
	public function getStyleNameListByStyleSn($filter)
	{
	    $ids=isset($filter['ids'])?trim($filter['ids']):'';
	    $where='';
	    if($ids)
	    {
	        $ids="'".implode("','",$ids);
	        $where.=" style_sn in($ids) and";
	    }
	    $sql = "select style_sn,style_name from front.base_style_info";
	    if($where)
	    {
	        $where=rtrim($where,'and');
	        $sql.=$where;
	    }
	    $row = $this->db()->getAll($sql);
	    return $row;	
	}

}
?>