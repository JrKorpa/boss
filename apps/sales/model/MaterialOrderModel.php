<?php
/**
 *  -------------------------------------------------
 *   @file		: MaterialBillModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2018-01-18 14:00:47
 *   @update	:
 *  -------------------------------------------------
 */
class MaterialOrderModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'material_order';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"bill_no"=>"单据编号",
"bill_status"=>"数据字典：仓储单据状态（warehouse_in_status）/ 盘点单状态（warehouse.pandian_plan）",
"department_id"=>"销售渠道",
"bill_note"=>"备注",
"create_user"=>"制单人",
"create_time"=>"制单时间",
"check_user"=>"审核人",
"check_time"=>"审核时间");
		parent::__construct($id,$strConn);
	}
    public static function createBillNo($bill_id,$bill_type){
        $bill_id = substr($bill_id, -4);
        $bill_no = $bill_type . date('Ymd', time()) . rand(100, 999) . str_pad($bill_id, 4,
            "0", STR_PAD_LEFT);
        return $bill_no;
    }
	/**
	 *	pageList，分页列表
	 *
	 *	@url MaterialBillController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true,$dow_info = null)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT DISTINCT b.*,c.channel_name,p.company_name,p.company_type,'/' as goods_sn,'/' as goods_name,'/' as goods_allnum,'/' as goods_allcost,'/' as goods_allshijia from material_order b 
			LEFT JOIN material_order_goods bg on b.id=bg.bill_id 
			LEFT JOIN material_goods g on bg.goods_sn=g.goods_sn 			
			LEFT JOIN cuteframe.sales_channels c on b.department_id=c.id 
			LEFT JOIN cuteframe.company p on c.company_id=p.id ";
	    /*		
		if(isset($dow_info) && $dow_info =="dow_info"){
			$sql = "SELECT b.bill_no,b.bill_type,b.bill_status,b.department_id,b.create_time,b.check_time,b.create_user,b.check_user,b.bill_note,
				bg.goods_sn,bg.supplier_id,bg.inventory_id,bg.in_warehouse_id,bg.out_warehouse_id,bg.num,bg.cost,bg.shijia,bg.batch_sn,
				g.style_sn,g.style_name,g.goods_name,g.goods_spec,g.catetory1,g.catetory2,g.catetory3,g.unit ,g.cost as chenbenjia,
                 s.name as sub_supplier_name,c.channel_name,p.company_name,p.company_type,w1.name as in_warehouse_name,w2.name as out_warehouse_name 
				 from material_bill b 
				LEFT JOIN material_bill_goods bg on b.id=bg.bill_id 
				LEFT JOIN material_goods g on bg.goods_sn=g.goods_sn 
				LEFT JOIN kela_supplier.app_processor_info s on bg.supplier_id=s.id 
				LEFT JOIN cuteframe.sales_channels c on b.department_id=c.id 
				LEFT JOIN cuteframe.company p on c.company_id=p.id 
				LEFT JOIN warehouse w1 on bg.in_warehouse_id=w1.id 
				LEFT JOIN warehouse w2 on bg.out_warehouse_id=w2.id
				";
			$page = 1;
			$pageSize = 9999;
		}*/
		$str = '';
		$where_old = $where;
		if(!empty($where['bill_no'])){   
		    if(is_array($where['bill_no'])){
		        $where['bill_no'] = "'".implode("','",$where['bill_no'])."'";
		        $str .= "b.`bill_no` in ({$where['bill_no']}) AND ";
		    }else{
			    $str .= "b.`bill_no` = '{$where['bill_no']}' AND ";
		    }
		}	
		
		if(!empty($where['bill_status'])){
		    $str .= "b.`bill_status` = {$where['bill_status']} AND ";
		}
		
		if(!empty($where['goods_sn'])){
		    $str .="g.goods_sn='{$where['goods_sn']}' AND ";
		}		
		if(!empty($where['style_sn'])){
		    $str .="g.style_sn='{$where['style_sn']}' AND ";
		}
		if(!empty($where['style_name'])){
		    $str .="g.style_name like '%{$where['style_name']}%' AND ";
		}
		if(!empty($where['goods_name'])){
		    $str .="g.goods_name like '%{$where['goods_name']}%' AND ";
		}
		if(!empty($where['catetory1'])){
		    $str .="g.catetory1 = '{$where['catetory1']}' AND ";
		}
		if(!empty($where['catetory2'])){
		    $str .="g.catetory2 = '{$where['catetory2']}' AND ";
		}
		if(!empty($where['goods_spec'])){
		    $str .="g.goods_spec like '%{$where['goods_spec']}%' AND ";
		}
		if(!empty($where['create_user'])){
		    $str .="b.create_user = '{$where['create_user']}' AND ";
		}		
        if (!empty($where['time_start'] !== "")) {
            $str .= " b.create_time>='{$where['time_start']} 00:00:00' AND ";
        }
        if (!empty($where['time_end'])) {
            $str .= " b.create_time <= '{$where['time_end']} 23:59:59' AND ";
        }
        if (!empty($where['check_time_start'])) {
            $str .= " b.check_time>='{$where['check_time_start']} 00:00:00' AND ";
        }
        if (!empty($where['check_time_end'])) {
            $str .= " b.check_time <= '{$where['check_time_end']} 23:59:59' AND ";
        }	
		if(!empty($where['department_id'])){
		    $str .="b.department_id='{$where['department_id']}' AND ";
		}
        $companyId = $_SESSION['companyId'];
        if($companyId<>58){
        	if(!empty($_SESSION['qudao']))
        	    $str .=" b.department_id in ({$_SESSION['qudao']}) AND ";
        	else
        		$str .=" b.department_id ='0' AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY b.`id` DESC";
		
		//echo $sql; 
		//$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
		//下载
		if(isset($dow_info) && $dow_info =="dow_info"){
			$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
			$this->download($data['data']);
		}
		if(!empty($where['show_detail'])){
            $data = $this->pageList_detail($where_old,$page,$pageSize=10,$useCache=true);
		}else
		    $data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);

		foreach ($data['data'] as $key => $row) {
			$res1= array('allnum' =>0 ,'allcost' =>0,'allshihia' =>0 );
            $res2=array();  
			$res2=$this->getTotal($row['id']);
			if($res2)
				$res1=$res2;
			$data['data'][$key]=array_merge($data['data'][$key],$res1);
		}
		return $data;
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url MaterialBillController/search
	 */
	 function pageList_detail ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT b.*,g.goods_sn,g.goods_name,g.goods_spec,g.style_sn,g.style_name,g.catetory1,g.catetory2,g.catetory3,g.goods_sale_price,g.unit,c.channel_name,p.company_name,p.company_type,bg.goods_num,bg.goods_price from material_order b 
			LEFT JOIN material_order_goods bg on b.id=bg.bill_id 
			LEFT JOIN material_goods g on bg.goods_sn=g.goods_sn 			
			LEFT JOIN cuteframe.sales_channels c on b.department_id=c.id 
			LEFT JOIN cuteframe.company p on c.company_id=p.id ";
			
		
		$str = '';
		if(!empty($where['bill_no'])){   
		    if(is_array($where['bill_no'])){
		        $where['bill_no'] = "'".implode("','",$where['bill_no'])."'";
		        $str .= "b.`bill_no` in ({$where['bill_no']}) AND ";
		    }else{
			    $str .= "b.`bill_no` = '{$where['bill_no']}' AND ";
		    }
		}	
		if(!empty($where['bill_type'])){
		    $str .= "b.`bill_type` = '{$where['bill_type']}' AND ";
		}
		if(!empty($where['bill_status'])){
		    $str .= "b.`bill_status` = {$where['bill_status']} AND ";
		}
		if(!empty($where['in_warehouse_id'])){
		    $str .="bg.in_warehouse_id={$where['in_warehouse_id']} AND ";
		}
		if(!empty($where['out_warehouse_id'])){
		    $str .="bg.out_warehouse_id={$where['out_warehouse_id']} AND ";
		}
		if(!empty($where['supplier_id'])){
		    $str .="bg.supplier_id={$where['supplier_id']} AND ";
		}
		if(!empty($where['goods_sn'])){
		    $str .="g.goods_sn='{$where['goods_sn']}' AND ";
		}		
		if(!empty($where['style_sn'])){
		    $str .="g.style_sn='{$where['style_sn']}' AND ";
		}
		if(!empty($where['style_name'])){
		    $str .="g.style_name like '%{$where['style_name']}%' AND ";
		}
		if(!empty($where['goods_name'])){
		    $str .="g.goods_name like '%{$where['goods_name']}%' AND ";
		}
		if(!empty($where['catetory1'])){
		    $str .="g.catetory1 = '{$where['catetory1']}' AND ";
		}
		if(!empty($where['catetory2'])){
		    $str .="g.catetory2 = '{$where['catetory2']}' AND ";
		}
		if(!empty($where['goods_spec'])){
		    $str .="g.goods_spec like '%{$where['goods_spec']}%' AND ";
		}
		if(!empty($where['create_user'])){
		    $str .="b.create_user = '{$where['create_user']}' AND ";
		}			
        if (!empty($where['time_start'] !== "")) {
            $str .= " b.create_time>='{$where['time_start']} 00:00:00' AND ";
        }
        if (!empty($where['time_end'])) {
            $str .= " b.create_time <= '{$where['time_end']} 23:59:59' AND ";
        }
        if (!empty($where['check_time_start'])) {
            $str .= " b.check_time>='{$where['check_time_start']} 00:00:00' AND ";
        }
        if (!empty($where['check_time_end'])) {
            $str .= " b.check_time <= '{$where['check_time_end']} 23:59:59' AND ";
        }	
		if(!empty($where['department_id'])){
		    $str .="b.department_id='{$where['department_id']}' AND ";
		}
		$companyId = $_SESSION['companyId'];
        if($companyId<>58){
        	$str .="c.company_id='{$companyId}' AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " group by b.bill_no,bg.goods_sn";

		//echo $sql;
		//exit;
		$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
				
		return $data;
	}
	
	
	function download($data){
		
		if(!empty($data)){
			$dd =new DictModel(1);
			//$status_list = $dd->getEnumArray("material.bill_status");
			//$salesChannelsModel = new SalesChannelsModel(1);
			$status_list = $dd->getEnumArray("material.bill_status");
			$status_name=array_column($status_list,'name');
			$status_label=array_column($status_list,'label');
			$status_list=array();
			foreach ($status_name as $key => $v) {
				$status_list[$v] = $status_label[$key];
			}		
			$company_list = $dd->getEnumArray("company.company_type");
			$company_name=array_column($company_list,'name');
			$company_label=array_column($company_list,'label');
			$company_list=array();
			foreach ($company_name as $key => $v) {
				$company_list[$v] = $company_label[$key];
			}	

			$xls_ontent = "单据编号,单据类型,单据状态,制单时间,审核时间,制单人,审核人,出库公司,公司类型,销售渠道,供应商,入库仓库,出库仓库,批次号,出入库数量,成本单价,成本总价,销售单价,销售总价,款号,款式名称,货品编号,货品名称,货品规格,分类1,分类2,分类3,单位,单据备注\r\n";	
			foreach($data as $key => $value){
				$xls_ontent .= $value['bill_no'].",";
				$xls_ontent .= $value['bill_type'].",";
				$xls_ontent .= $status_list[$value['bill_status']].",";//$dd->getEnum("material.bill_status",$value['bill_status']).",";
				$xls_ontent .= $value['create_time'].",";
				$xls_ontent .= $value['check_time'].",";
				$xls_ontent .= $value['create_user'].",";
				$xls_ontent .= $value['check_user'].",";
				$xls_ontent .= $value['company_name'].","; //销售公司
				$xls_ontent .= !empty($value['company_type']) ? $company_list[$value['company_type']]."," : ","; //$dd->getEnum("company.company_type",$value['company_type']).",";
				$xls_ontent .= $value['channel_name'].","; //销售渠道
				$xls_ontent .= $value['sub_supplier_name'].","; 
				$xls_ontent .= $value['in_warehouse_name'].",";
				$xls_ontent .= $value['out_warehouse_name'].",";
			
				$xls_ontent .= $value['batch_sn'].",";
				$xls_ontent .= $value['num'].",";
				$xls_ontent .= $value['cost'].",";
				$xls_ontent .= ($value['cost']*$value['num'] ).",";
				$xls_ontent .= $value['shijia'].",";
				$xls_ontent .= ($value['shijia']*$value['num']).","; //销售单价
		
				$xls_ontent .= $value['style_sn'].",";
				$xls_ontent .= $value['style_name'].",";
				$xls_ontent .= $value['goods_sn'].",";
				$xls_ontent .= $value['goods_name'].",";
				$xls_ontent .= $value['goods_spec'].",";
				$xls_ontent .= $value['catetory1'].",";
				$xls_ontent .= $value['catetory2'].",";
				$xls_ontent .= $value['catetory3'].",";
				$xls_ontent .= $value['unit'].",";
				$xls_ontent .= $value['bill_note'].",\r\n";

			}		
		}else{
			$xls_ontent = "没有数据\r\n";	
		}
		
		header("Content-type: text/html; charset=gbk");
		header("Content-type:aplication/vnd.ms-excel");
		header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "物控导出" . date("Y-m-d")) . ".csv");
		echo iconv("utf-8", "GB18030", $xls_ontent);	
		exit();
	}

	/**
	 * 单据明细查询
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 */
	function billGoodsPageList ($where,$page,$pageSize=10,$useCache=true){
		$sql = "SELECT g.goods_sn,g.goods_name,g.unit,bg.id,bg.goods_num,bg.goods_price,g.style_sn,g.goods_type,g.remark  
			FROM material_order_goods bg 
			LEFT JOIN material_goods g on bg.goods_sn=g.goods_sn ";
		
	    $str = '';
	    if(!empty($where['bill_id'])){
            $str .= "bg.`bill_id` = {$where['bill_id']} AND ";
	    }
	    if(!empty($where['goods_status'])){
	        $str .= "g.`goods_status` = {$where['goods_status']} AND ";
	    }
	    if(!empty($where['goods_type'])){
	        if(is_array($where['goods_type'])){
	            $str .= "g.`goods_type` in (".implode(",",$where['goods_type']).") AND ";
	        }else{
	            $str .= "g.`goods_type` =".$where['goods_type']." AND ";
	        }
	    }	    
	    if($str)
	    {
	        $str = rtrim($str,"AND ");//这个空格很重要
	        $sql .=" WHERE ".$str;
	    }
		
		//$sql .= " ORDER BY bg.`goods_sn` ";
		$sql .= " ORDER BY g.goods_type asc,g.goods_sn asc";
		//echo $sql ;
		// exit;
	    $data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
	    return $data;
	}

	/**
	 * 货号汇总单据明细查询
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 */
	/*
	function billGoodsSumPageList ($where,$page,$pageSize=10,$useCache=true){
		$sql = "SELECT sum(bg.num) as num,sum(round(bg.num*bg.cost,2)) as allcost,sum(round(bg.num*bg.shijia,2)) as sales_price ,g.* 
			FROM material_bill_goods bg 
			LEFT join material_bill b  on bg.bill_id=b.id 
			LEFT JOIN material_goods g on bg.goods_sn=g.goods_sn ";
		
	    $str = '';
	    if(!empty($where['bill_id'])){
            $str .= "bg.`bill_id` = {$where['bill_id']} AND ";
	    }	    
	    if($str)
	    {
	        $str = rtrim($str,"AND ");//这个空格很重要
	        $sql .=" WHERE ".$str;
	    }
		
		
		$sql .= " group BY bg.`goods_sn`";
		
		//echo $sql ; exit;
	    $data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
	    return $data;
	}*/


	/**
	 *	pageList，商品分页列表
	 *
	 *	@url MaterialGoodsController/search
	 */
	function goodspageList ($where,$page,$pageSize=10,$useCache=true,$bill_id=null)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT mg.id,mg.style_sn,mg.style_name,mg.goods_sn,mg.goods_name,mg.goods_spec,mg.catetory1,mg.catetory2,mg.catetory3,mg.cost,mg.min_qty,mg.pack_qty,mg.goods_status,mg.goods_type,mg.remark,mg.unit,mg.caizhi,mg.goods_jiajialv,if(mg.goods_sale_price>0,mg.goods_sale_price,if(mg.goods_jiajialv>0,round(mg.cost*mg.goods_jiajialv,2),round(mg.cost*bsi.jiajialv,2))) as goods_sale_price,bsi.jiajialv,bg.goods_num,(select sum(v.inventory_qty) from material_inventory v where v.goods_sn=mg.goods_sn and v.inventory_qty>0) as inventory_qty FROM material_goods as mg left join front.base_style_info bsi on mg.style_sn=bsi.style_sn left join material_order_goods bg on mg.goods_sn=bg.goods_sn and bg.bill_id='{$bill_id}' ";
		$str = '';
        if(!empty($where['style_sn'])){
            $str .=" mg.style_sn = '{$where['style_sn']}'  AND ";
        }
        if(!empty($where['goods_sn'])){
            $str .=" mg.goods_sn = '{$where['goods_sn']}'  AND ";
        }
        if(!empty($where['goods_status'])){
            $str .=" mg.goods_status = '{$where['goods_status']}'  AND ";
        } 
        if(!empty($where['goods_type'])){
            $str .=" mg.goods_type = '{$where['goods_type']}'  AND ";
        }
        if(!empty($where['style_name'])){
            $str .=" mg.style_name like '%{$where['style_name']}%'  AND ";
        }

        if(!empty($where['goods_name'])){
            $str .=" mg.goods_name like '%{$where['goods_name']}%'  AND ";
        }

        if(!empty($where['goods_spec'])){
            $str .=" mg.goods_spec like '%{$where['goods_spec']}%'  AND ";
        }

        if(!empty($where['catetory1'])){
            $str .=" mg.catetory1 = '%{$where['catetory1']}'  AND ";
        }


        if(!empty($where['catetory2'])){
            $str .=" catetory2 = '{$where['catetory2']}'  AND ";
        }

        if(!empty($where['catetory3'])){
            $str .=" catetory3 = '{$where['catetory3']}'  AND ";
        }

        if(!empty($where['cost'])){
            $str .=" cost = '{$where['cost']}' AND ";
        }
       
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
        if(!empty($where['order_by_field']))
            $sql .= " ORDER BY mg.{$where['order_by_field']} ";
        else        
		    $sql .= " ORDER BY `id` DESC";
		//echo $sql;
		$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

   

	/**
	 * 
	 * @param unknown $bill_id
	 * @param string $transMode
	 * @throws Exception
	 * @return multitype:number string NULL
	 */
	function checkOrderPass($bill_id,$transMode = true){
	    $result = array('success'=>0,'error'=>'');	    
	    try{
	        	        
	        $pdo = $this->db()->db();
	        if($transMode == true){
	            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
	            $pdo->beginTransaction();//开启事务
	        }
	        
	        $user = $_SESSION['userName'];
	        $time = date("Y-m-d H:i:s");
	        
    	    $sql = "select * from material_order where id={$bill_id}";
    	    $billInfo = $this->db()->getRow($sql);
    	    if($billInfo['bill_status']!=1){
    	        throw new Exception("单据不是已保存状态，不能审核！");
    	    }
    	   
    	        	    
    	    $sql = "select * from material_order_goods where bill_id={$bill_id}";
    	    $billGoodsList = $this->db()->getAll($sql);
    	    if(empty($billGoodsList)){
    	        throw new Exception("单据还没有添加明细！");
    	    } 
    	    
    	    $sql = "update material_order set bill_status=2,check_user='{$user}',check_time='{$time}' where id={$bill_id}";
    	    $pdo->query($sql);
    	    if($transMode==true){
    	        $pdo->commit();//如果没有异常，就提交事务
    	        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
    	    }
    	    $result['success'] = 1;    	    
	    }catch (Exception $e){
	        if($transMode==true){
	            $pdo->rollback();//事务回滚
	            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	        }
	        $result['success'] = 0;
	        $result['error'] = $e->getMessage();
	    }
	    return $result;	    
	}

    /****获取单据总数量 总成本 总出入库金额***/
	function getTotal($bill_id){
           $sql="select sum(goods_num) as allnum,sum(goods_price) as allprice 
           from material_order_goods where bill_id='$bill_id'";
           $res=$this->db()->getRow($sql);
           return $res;
	}
	/********获取物控有需要的渠道(非关闭的门店)*********************************/
	function getAllChannelInfo(){
		   $companyId = $_SESSION['companyId'];
		    if($companyId<>58){
	            $sql="select *from cuteframe.sales_channels   where channel_class<>2 and company_id='{$companyId}' 
				union all 
				select s.* from cuteframe.sales_channels  s  where s.channel_class=2 and s.channel_type<>2 and s.company_id='{$companyId}' 
				union all
				select s.* from cuteframe.sales_channels  s,cuteframe.shop_cfg p   where s.channel_own_id=p.id and s.channel_class=2 and s.channel_type=2 and p.shop_status<>2 and s.company_id='{$companyId}' ";
		    }else{
	            $sql="select *from cuteframe.sales_channels   where channel_class<>2  
				union all 
				select s.* from cuteframe.sales_channels  s  where s.channel_class=2 and s.channel_type<>2 
				union all
				select s.* from cuteframe.sales_channels  s,cuteframe.shop_cfg p   where s.channel_own_id=p.id and s.channel_class=2 and s.channel_type=2 and p.shop_status<>2 ";
		    }
			$data=$this->db()->getAll($sql);
	        $department = array();
	        foreach ($data as $k=>$vo){
	            $department[] = array('id'=>$vo['id'],'name'=>$vo['channel_name']);
	        }
		    return $department;         
	}

    function getStyle_Img($style_sn){
    	$sql="select si.style_id,si.style_sn,sg.thumb_img from front.base_style_info as si,front.app_style_gallery as sg  where  si.style_id=sg.style_id
 and si.style_sn='{$style_sn}' and sg.image_place=1";
        return $this->db()->getRow($sql);
    }

    function sendGoods($bill_id,$status){
    	if(!in_array($status,array(4,5)))
    		return "参数异常";
		$pdo = $this->db()->db();//pdo对象		
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			$row = $this->db()->getRow("select * from warehouse_shipping.material_order where id='{$bill_id}' for update");
		    if($row){
		    	//赠品发货
		    	if($status==4){
				    if(!in_array($row['bill_status'],array(2,5))){
				        throw new Exception("单据不是已审核状态或者物料已发货状态，不能发赠品！");				       
				    }
                    $update_status = $row['bill_status'];
				    if($row['bill_status']==5)
                        $update_status = 6;
                    else
                        $update_status =4;     
				    $this->db()->query("update warehouse_shipping.material_order set bill_status='{$update_status}' where id='{$bill_id}'");
			        $pdo->commit();//如果没有异常，就提交事务
		            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				    return true;
				} 
				//物料发货
		    	if($status==5){
				    if(!in_array($row['bill_status'],array(2,4))){
				        throw new Exception("单据不是已审核状态或者赠品已发货状态，不能发物料！");				       
				    }
                    $update_status = $row['bill_status'];
				    if($row['bill_status']==4)
                        $update_status = 6;
                    else
                        $update_status =5;     
				    $this->db()->query("update warehouse_shipping.material_order set bill_status='{$update_status}' where id='{$bill_id}'");
			        $pdo->commit();//如果没有异常，就提交事务
		            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				    return true;
				} 					   
			}else{
				throw new Exception("单据不存在!");
			}    

        }catch(Exception $e){
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return $e->getMessage();        	
        }
    } 
}

?>