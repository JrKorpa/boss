<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangruiying
 *   @date		: 2015-04-07 15:49:45
 *   @update	:
 *  -------------------------------------------------
 */
class ClothProductionTrackingModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'product_info';
		$this->pk='id';
		$this->_prefix='';
                $this->_dataObject = array("id"=>"ID",
                "bc_sn"=>"布产号",
                "p_id"=>"采购单明细ID/订单商品ID",
                "p_sn"=>"采购单号/订单号",
                "style_sn"=>"款号",
                "status"=>"状态:buchan_status",
                "buchan_fac_opra"=>"生产状态：见数据字典buchan_fac_opra ",
                "num"=>"数量",
                "prc_id"=>"工厂ID",
                "prc_name"=>"工厂名称",
                "opra_uname"=>"跟单人",
                "add_time"=>"单据添加时间",
                "esmt_time"=>"标准出厂时间",
                "rece_time"=>"工厂交货时间",
                "info"=>"备注",
                "channel_id"=>"渠道ID",
                "customer_source_id"=>"来源ID",
                "from_type"=>"来源类型：1=>采购单 2=>订单");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 * @param string $power 标识符（标示 是查询当前登录人的记录 还是 所有的记录）
	 *	@url ProductInfoController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true,$power = '')
	{
		$where = $this->checkOpra_uname($where , $power);
		$sql=$this->getsql($where);
		$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
		foreach($data['data'] as $k=>$v)
		{
			$data['data'][$k]['class']='';
			if(date('Y-m-d')>$v['esmt_time'])
			{
				$data['data'][$k]['class']='color:red';
			}elseif(date('Y-m-d',time()+86400*2)>=$v['esmt_time'])
			{
				$data['data'][$k]['class']='color:green';
			}
		}
		return $data;
	}
	function getsql($where=array())
	{
		 //质检状态取最新的一条状态为依据
		$sql = "SELECT main.p_sn,main.bc_sn,main.opra_uname,main.id,main.p_id,main.style_sn,main.status,main.esmt_time,
                main.order_time,main.edit_time,r.oqc_result,r.oqc_reason,cc.num,r.oqc_info,r.opra_uname as o_uname,main.channel_id,main.customer_source_id,
                r.opra_time,main.info,main.consignee,main.add_time,main.buchan_fac_opra,rc.cat_name,main.remark
                FROM `".$this->table()."` as main LEFT JOIN (SELECT t.bc_id,t.oqc_result,t.oqc_reason,t.oqc_info,t.opra_time,t.opra_uname FROM product_oqc_opra t INNER JOIN (SELECT bc_id, MAX(opra_time) AS MaxDate FROM product_oqc_opra GROUP BY bc_id ) tm
                ON t.bc_id = tm.bc_id AND t.opra_time=tm.MaxDate) as r ON r.bc_id=main.id LEFT JOIN product_fqc_conf as rc ON rc.id=r.oqc_reason LEFT JOIN (SELECT c.bc_id,COUNT(c.bc_id) as num FROM product_oqc_opra as c where c.oqc_result=0 group by c.bc_id) AS cc ON cc.bc_id=main.id";
		$str = '';
		if(!empty($where['p_sn']))
		{
			//$str .= "main.`p_sn`='".$where['p_sn']."' AND ";
			$p_sn="'".str_replace(' ',"','",$where['p_sn'])."'";
			$str .= "main.`p_sn` in ({$p_sn}) AND ";
		}
        if(!empty($where['bc_sn'])) {$str .= "main.`bc_sn`='".$where['bc_sn']."' AND ";}
        if(!empty($where['channel_id'])) {$str .= "main.`channel_id`='".$where['channel_id']."' AND ";}
        if(!empty($where['customer_source_id'])) {$str .= "main.`customer_source_id`='".$where['customer_source_id']."' AND ";}
		if(!empty($where['status'])) {
			$str .= "main.`status`=".$where['status']." AND ";
		}
                $str.="(main.`status`=4 or main.`status`=7) AND";
				//$buchan_fac_opra=explode(',',$where['buchan_fac_opra']);
         if(!empty($where['buchan_fac_opra[]']))
		{
			$where['buchan_fac_opra[]']=implode(',',$where['buchan_fac_opra[]']);
			$str .= " main.`buchan_fac_opra` in (".$where['buchan_fac_opra[]'].") AND ";
		}
                //用LIKE 'A%'不造成全表扫描
                if($where['opra_uname'] != "")
		{
			$str .= " main.`opra_uname` like \"".addslashes($where['opra_uname'])."%\" AND ";
		}
                if($where['style_sn'] != "")
		{
			$str .= " main.`style_sn` like \"".addslashes($where['style_sn'])."%\" AND ";
		}

                //0为全部4为3次以上
                if($where['oqc_num'] !=0 and $where['oqc_num']<4)
		{
			$str .= " cc.`num`=".$where['oqc_num']." AND ";
		}
                else if($where['oqc_num']>3)
                {
                    $str .= " cc.`num`>".($where['oqc_num']-1)." AND ";
                }
				elseif($where['oqc_num']=='have')
				{
					 $str .= " cc.`num`>0 AND ";
				}

                if($where['is_extended'] !== "")
                {
                    if($where['is_extended']==1)
                    {
                        $str .= " main.`esmt_time`<'".date('Y-m-d')."' AND ";
                    }
                    else
                    {
                        $str .= " (date_format(main.`esmt_time`,'%Y-%m-%d') BETWEEN '".date('Y-m-d')."' AND '".date('Y-m-d',time()+2*86400)."') AND ";
                    }
                }
                if(!empty($where['start_time']) and empty($where['end_time']))
                {
                    $str .= " main.`esmt_time`>='".$where['start_time']."' AND ";
                }
                elseif(empty($where['start_time']) and !empty($where['end_time']))
                {
                    $str .= " date_format(main.`esmt_time`,'%Y-%m-%d')<='".$where['end_time']."' AND ";
                }
                elseif(!empty($where['start_time']) and !empty($where['end_time']))
                {
                    $str .= " (date_format(main.`esmt_time`,'%Y-%m-%d') BETWEEN '".$where['start_time']."' AND '".$where['end_time']."') AND ";
                }
                if(!empty($where['order_start_time']) and empty($where['order_end_time']))
                {
                    $str .= " main.`order_time`>='".$where['order_start_time']."' AND ";
                }
                elseif(empty($where['order_start_time']) and !empty($where['order_end_time']))
                {
                    $str .= " date_format(main.`order_time`,'%Y-%m-%d')<='".$where['order_end_time']."' AND ";
                }
                elseif(!empty($where['order_start_time']) and !empty($where['order_end_time']))
                {
                    $str .= " (date_format(main.`order_time`,'%Y-%m-%d') BETWEEN '".$where['order_start_time']."' AND '".$where['order_end_time']."') AND ";
                }
                if(!empty($where['edit_start_time']) and empty($where['edit_end_time']))
                {
                    $str .= " main.`edit_time`>='".$where['edit_start_time']."' AND ";
                }
                elseif(empty($where['edit_start_time']) and !empty($where['edit_end_time']))
                {
                    $str .= " date_format(main.`edit_time`,'%Y-%m-%d')<='".$where['edit_end_time']."' AND ";
                }
                elseif(!empty($where['edit_start_time']) and !empty($where['edit_end_time']))
                {
                    $str .= " (date_format(main.`edit_time`,'%Y-%m-%d') BETWEEN '".$where['edit_start_time']."' AND '".$where['edit_end_time']."') AND ";
                }
                if(!empty($where['question_type']))
                {
                   $str .= " (rc.`tree_path` like \"".'0-'.$where['question_type']."%\" or rc.`id`={$where['question_type']}) AND ";
                }
				if(isset($where['oqc_result']) and $where['oqc_result']!=='')
				{
					$str .= " r.`oqc_result`={$where['oqc_result']} AND ";
				}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		if(isset($where['orderby']) and $where['orderby'] !== "" and isset($where['desc_or_asc']) and $where['desc_or_asc'] !== "")
		{
			$sql.= " ORDER BY {$where['orderby']} {$where['desc_or_asc']}";
		}
		else if(isset($where['orderby']) and $where['orderby'] !== "" )
		{
			$sql.= " ORDER BY {$where['orderby']}";
		}
		else
		{
			$sql .= " ORDER BY main.id DESC";
		}
		//echo $sql;//exit;
		return $sql;

	}
	function getdownload($where)
	{
		$sql=$this->getsql($where);
		return $this->db()->getAll($sql);
	}

	public function checkOpra_uname($where = array() , $power = '')
	{
		if(!empty($power) && isset($where['opra_uname'])){
			$where['opra_uname'] = $_SESSION['userName'];
		}
		return $where;
	}

}

?>