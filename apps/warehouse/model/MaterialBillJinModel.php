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
class MaterialBillJinModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'material_bill_jin';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"bill_no"=>"单据编号",
"bill_type"=>"单据类型",
"bill_status"=>"数据字典：仓储单据状态（warehouse_in_status）/ 盘点单状态（warehouse.pandian_plan）",
"warehouse_id"=>"仓库",
"supplier_id"=>"供应商",
"department_id"=>"销售渠道",
"bill_note"=>"备注",
"create_user"=>"制单人",
"create_time"=>"制单时间",
"check_user"=>"审核人",
"check_time"=>"审核时间",
"batch_sn"=>"销售单号");
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
		$sql = "SELECT DISTINCT b.*,s.name as main_supplier_name,c.channel_name,if(p2.company_name is not null,p2.company_name,p.company_name) as company_name,p.company_type,'/' as goods_sn,'/' as goods_name,w.name as main_warehouse_name,'/' as goods_allnum,'/' as goods_allcost,'/' as goods_allshijia from material_bill_jin b 
			LEFT JOIN material_bill_goods_jin bg on b.id=bg.bill_id 
			LEFT JOIN material_goods_jin g on bg.goods_sn=g.goods_sn 
			LEFT JOIN kela_supplier.app_processor_info s on b.supplier_id=s.id 
			LEFT JOIN cuteframe.sales_channels c on b.department_id=c.id 
			LEFT JOIN cuteframe.company p on c.company_id=p.id 
			LEFT JOIN cuteframe.company p2 on p.sd_company_id=p2.id
			LEFT JOIN warehouse w on b.warehouse_id=w.id ";
		if(isset($dow_info) && $dow_info =="dow_info"){
			$sql = "SELECT b.bill_no,b.bill_type,b.bill_status,b.department_id,b.create_time,b.check_time,b.create_user,b.check_user,b.bill_note,
				bg.goods_sn,bg.supplier_id,bg.inventory_id,bg.in_warehouse_id,bg.out_warehouse_id,bg.num,bg.cost,bg.shijia,bg.batch_sn,
				g.style_sn,g.style_name,g.goods_name,g.goods_spec,g.catetory1,g.catetory2,g.catetory3,g.unit ,g.cost as chenbenjia,
                 s.name as sub_supplier_name,s1.name as sub_supplier_name1,c.channel_name,if(p2.company_name is not null,p2.company_name,p.company_name) as company_name,p.company_type,w1.name as in_warehouse_name,w2.name as out_warehouse_name 
				 from material_bill_jin b 
				LEFT JOIN material_bill_goods_jin bg on b.id=bg.bill_id 
				LEFT JOIN material_goods_jin g on bg.goods_sn=g.goods_sn 
				LEFT JOIN kela_supplier.app_processor_info s on bg.supplier_id=s.id 
				LEFT JOIN kela_supplier.app_processor_info s1 on b.supplier_id=s1.id 
				LEFT JOIN cuteframe.sales_channels c on b.department_id=c.id 
				LEFT JOIN cuteframe.company p on c.company_id=p.id 
				LEFT JOIN cuteframe.company p2 on p.sd_company_id=p2.id
				LEFT JOIN warehouse w1 on bg.in_warehouse_id=w1.id 
				LEFT JOIN warehouse w2 on bg.out_warehouse_id=w2.id
				";
			$page = 1;
			$pageSize = 9999;
		}
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

		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY b.`id` DESC";
		
		//echo $sql; exit;
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
		$sql = "SELECT b.*,g.goods_sn,g.goods_name,g.goods_spec,g.style_sn,g.style_name,g.catetory1,g.catetory2,g.catetory3,g.goods_sale_price,g.unit,s.name as main_supplier_name,c.channel_name,p.company_name,p.company_type,w.name as main_warehouse_name,sum(bg.num) as goods_allnum,sum(round(bg.num*bg.cost,2)) as goods_allcost,sum(round(bg.num*bg.shijia,2)) as goods_allshijia from material_bill_jin b 
			LEFT JOIN material_bill_goods_jin bg on b.id=bg.bill_id 
			LEFT JOIN material_goods_jin g on bg.goods_sn=g.goods_sn 
			LEFT JOIN kela_supplier.app_processor_info s on b.supplier_id=s.id 
			LEFT JOIN cuteframe.sales_channels c on b.department_id=c.id 
			LEFT JOIN cuteframe.company p on c.company_id=p.id 
			LEFT JOIN warehouse w on b.warehouse_id=w.id ";
		
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

			$xls_ontent = "单据编号,单据类型,单据状态,制单时间,审核时间,制单人,审核人,出库公司,公司类型,销售渠道,供应商(单头),供应商（明细）,入库仓库,出库仓库,批次号,出入库数量,成本单价,成本总价,销售单价,销售总价,款号,款式名称,货品编号,货品名称,货品规格,分类1,分类2,分类3,单位,单据备注\r\n";
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
				$xls_ontent .= $value['sub_supplier_name1'].",";
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
		$sql = "SELECT bg.*,round(bg.num*bg.cost,2) as allcost,round(bg.num*bg.shijia,2) as allshijia ,g.goods_name ,g.`style_sn`,s.name as supplier_name,w1.name as in_warehouse_name,w2.name as out_warehouse_name
			FROM material_bill_goods_jin bg 
			LEFT JOIN material_goods_jin g on bg.goods_sn=g.goods_sn 
			LEFT JOIN kela_supplier.app_processor_info s on bg.supplier_id=s.id 
			LEFT JOIN warehouse w1 on bg.in_warehouse_id=w1.id 
			LEFT JOIN warehouse w2 on bg.out_warehouse_id=w2.id
			";
		/*	
		if(isset($extends)  && $extends['sel'] === "sel"){
			$sql = "SELECT (sum(bg.shijia) * sum(bg.num)) as sales_price,bg.num,bg.goods_sn,g.style_sn,g.style_name,g.goods_name,g.catetory1,g.catetory2,g.catetory3,g.unit 
				FROM material_bill_goods_jin bg 
				LEFT JOIN material_goods_jin g on bg.goods_sn=g.goods_sn 
				LEFT JOIN kela_supplier.app_processor_info s on bg.supplier_id=s.id 
				LEFT JOIN warehouse w1 on bg.in_warehouse_id=w1.id 
				LEFT JOIN warehouse w2 on bg.out_warehouse_id=w2.id
				";
		}*/	
	    $str = '';
	    if(!empty($where['bill_id'])){
            $str .= "bg.`bill_id` = {$where['bill_id']} AND ";
	    }	    
	    if($str)
	    {
	        $str = rtrim($str,"AND ");//这个空格很重要
	        $sql .=" WHERE ".$str;
	    }
		
		//$sql .= " ORDER BY bg.`goods_sn` ";
		$sql .= " ORDER BY bg.id ";
		//echo $sql ; exit;
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
	function billGoodsSumPageList ($where,$page,$pageSize=10,$useCache=true){
		$sql = "SELECT sum(bg.num) as num,sum(round(bg.num*bg.cost,2)) as allcost,sum(round(bg.num*bg.shijia,2)) as sales_price ,bg.shijia,g.* 
			FROM material_bill_goods_jin bg 
			LEFT join material_bill_jin b  on bg.bill_id=b.id 
			LEFT JOIN material_goods_jin g on bg.goods_sn=g.goods_sn ";
		
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
	}

    /**
     * 获取单据类别，1是入库单 2是出库单    0未知
     * @param unknown $bill_type
     * @return number
     */
	function getBillCat($bill_type){
	    if(in_array($bill_type,array('WL','WT','WY','WH'))){
	        return 1;
	    }else if(in_array($bill_type,array('WP','WB','WC','WK'))){
	        return 2;
	    }else{
	        return 0;
	    }
	}

	/**
	 * 
	 * @param unknown $bill_id
	 * @param string $transMode
	 * @throws Exception
	 * @return multitype:number string NULL
	 */
	function checkBillPass($bill_id,$transMode = true){
	    $result = array('success'=>0,'error'=>'');	    
	    try{
	        	        
	        $pdo = $this->db()->db();
	        if($transMode == true){
	            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
	            $pdo->beginTransaction();//开启事务
	        }
	        
	        $user = $_SESSION['userName'];
	        $time = date("Y-m-d H:i:s");
	        
    	    $sql = "select * from material_bill_jin where id={$bill_id}";
    	    $billInfo = $this->db()->getRow($sql);
    	    if($billInfo['bill_status']!=1){
    	        throw new Exception("单据不是已保存状态，不能审核！");
    	    }
    	    $bill_type = $billInfo['bill_type'];    	    
    	    $bill_cat = $this->getBillCat($bill_type);
    	    if(!in_array($bill_cat,array(1,2))){
    	        throw new Exception("暂不支持单据类型【{$bill_type}】");    	        
    	    }
    	        	    
    	    $sql = "select * from material_bill_goods_jin where bill_id={$bill_id}";
    	    $billGoodsList = $this->db()->getAll($sql);
    	    if(empty($billGoodsList)){
    	        throw new Exception("单据还没有添加明细！");
    	    } 
    	    
    	    if($bill_cat==1){
    	        //入库单处理
    	        foreach ($billGoodsList as $goods){ 
      	       
        	        $inventoryData = array(
                        "goods_sn"=>$goods['goods_sn'],
                        "supplier_id"=>$goods['supplier_id'],
                        "warehouse_id"=>$goods['in_warehouse_id'],
                        "batch_sn"=>$goods['batch_sn'],
                        "inventory_qty"=>$goods['num'],
                        "cost"=>$goods['cost'],    	           
        	        ); 
        	        if($bill_type=='WH' && !empty($billInfo['from_bill_no'])){
		        	        $sql ="select *from material_inventory_jin where goods_sn='{$goods['goods_sn']}' and supplier_id='{$goods['supplier_id']}' and warehouse_id='{$goods['in_warehouse_id']}' and batch_sn='{$goods['batch_sn']}'";
		        	        $exist_inventory = $this->db()->getRow($sql);
		        	        if(!empty($exist_inventory)){
		        	        	$sql = "select *from material_inventory_jin where id='{$exist_inventory['id']}' for update";
                                $pdo->query($sql);
                                $sql = "update material_inventory_jin set inventory_qty=inventory_qty+{$goods['num']} where id='{$exist_inventory['id']}'";
		        	            $pdo->query($sql);
		        	        }else{
			        	        $sql = $this->insertSqlNew($inventoryData,"material_inventory_jin");
			        	        $pdo->query($sql);
                            }
	        	    }else{
		        	        $sql = $this->insertSqlNew($inventoryData,"material_inventory_jin");
		        	        $pdo->query($sql);
		        	        //更新货品成本价
		        	        $sql = "update material_goods_jin set cost={$goods['cost']} where goods_sn='{$goods['goods_sn']}'";
		        	        $pdo->query($sql);	        	    	
	        	    }    
    	        }
    	    }else if($bill_cat==2){
    	        //出库单处理
    	        foreach ($billGoodsList as $goods){
    	            $inventory_id = $goods['inventory_id'];
                    $inventory_qty = $goods['num'];
                    if($inventory_id<=0){
                        throw new Exception("单据明细【货号{$goods['goods_sn']}+批次号{$goods['batch_sn']}】,关联的库存ID为空！");
                    }
                    if($inventory_qty<=0){
                        throw new Exception("单据明细【货号{$goods['goods_sn']}+批次号{$goods['batch_sn']}】,出库数量不合法！");
                    }
                    $sql = "select * from material_inventory_jin where id={$inventory_id}";
                    $inventoryInfo = $this->db()->getRow($sql);                    
                    if($inventoryInfo['inventory_qty']<$inventory_qty){
                        throw new Exception("单据明细【货号{$goods['goods_sn']}+批次号{$goods['batch_sn']}】,出库数量不能大于剩余库存数量！");
                    }
                    $sql = "update material_inventory_jin set inventory_qty=inventory_qty-{$inventory_qty} where id={$inventory_id} and version='{$inventoryInfo['version']}'";
    	            $num = $pdo->exec($sql);
                    if($num==0){
                        throw new Exception("更新库存失败！");
                    }
    	        } 
    	        
    	    }
    	    $sql = "update material_bill_jin set bill_status=2,check_user='{$user}',check_time='{$time}' where id={$bill_id}";
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
           $sql="select sum(num) as allnum,sum(round(num*cost,2)) as allcost,sum(round(num*shijia,2)) as allshijia from material_bill_goods_jin where bill_id='$bill_id'";
           $res=$this->db()->getRow($sql);
           return $res;
	}


	/********获取物控有需要的渠道(非关闭的门店)*********************************/
	function getAllChannelInfo(){
           $sql="select *from cuteframe.sales_channels   where channel_class<>2
			union all 
			select s.* from cuteframe.sales_channels  s  where s.channel_class=2 and s.channel_type<>2
			union all
			select s.* from cuteframe.sales_channels  s,cuteframe.shop_cfg p   where s.channel_own_id=p.id and s.channel_class=2 and s.channel_type=2 and p.shop_status<>2 ";
			$data=$this->db()->getAll($sql);
	        $department = array();
	        foreach ($data as $k=>$vo){
	            $department[] = array('id'=>$vo['id'],'name'=>$vo['channel_name']);
	        }
		    return $department;         
	}	

    /*
    *   获取单据明细
    */
    public function getBill_detail($bill_no){
    	$sql = "select g.goods_name,g.unit,bg.* from material_bill_goods_jin  bg left join material_goods_jin g on bg.goods_sn=g.goods_sn,material_bill_jin b where bg.bill_id=b.id and b.bill_no='{$bill_no}'";
        return $this->db()->getAll($sql);
    }

    /*
    *   获取单据信息
    */
    public function getBill_Info($bill_no){
    	$sql = "select b.* from material_bill_jin b where b.bill_no='{$bill_no}'";
        return $this->db()->getRow($sql);
    }    

}

?>