<?php
/**
 * This contains the Retrieval API .
 *
 */
class api {

    private $db = null;
    private $error_msg = '';
    private $return_msg = '';
    private $return_sql = '';
    private $filter = array();

    public function __construct($_filter)
    {
        global $config;
        $this->db= new KELA_API_DB($config);
		$this->filter = $_filter;
    }

    /**
     * 订单系统推送新增采购信息
	 * 'insert_data'=>
	 *  array(
			'info' => array(
				//订单主信息
				'order_num' => 订单总数量
				'order_fee' => 订单总金额
			),
			'goods' => array(
				goods1 => array(
					'style_sn' => '商品款号',
					'product_type_id' => '产品线ID'
					'cat_type_id' => '款式分类ID'
					'num'	=> '商品数量'
					'info'  => '备注'
					'attr'	=> array(
						 '属性信息'
					)
				)
				goods2 => array(
					'style_sn' => '商品款号',
					'product_type_id' => '产品线ID'
					'cat_type_id' => '款式分类ID'
					'num'	=> '商品数量'
					'info'  => '备注'
					'attr'	=> array(
						 '属性信息'
					)
				)
			)
		)
	 *
	 *
     */
    public function AddPurchaseOrder() {
		$error = 0;
		$content = "";
        $_id = 0;

        if (!isset($this->filter['insert_data'])) {
			$error = 1;
			$content = "缺少参数insert_data";
		}
		$data = $this->filter['insert_data'];
		if(!count($data))
		{
			$error = 1;
			$content = "insert_data是个空数组";
		}
		if($error)
		{
			$this->error = 1;
            $this->return_sql = '';
            $this->error_msg = $content;
            $this->return_msg = 0;
            $this->display();
        }
		$time = date('Y-m-d H:i:s');
		//事务添加数据，如果处理错误则返回false
		//require_once('/frame/init.php');
		define("root_path",dirname(dirname(dirname(dirname(__FILE__)))));
		require_once(root_path.'/frame/init.php');
		$pdo = DB::cn(24)->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			$info = $data['info'];

			//添加采购单主表信息 `purchase_info`
			$sql = "INSERT INTO `purchase_info`(`p_sn`, `t_id`, `is_tofactory`, `is_style`, `p_sum`, `purchase_fee`, `put_in_type`,`make_uname`, `make_time`, `check_uname`, `check_time`, `p_status`, `p_info`) VALUES ('',1,1,1,{$info['order_num']},{$info['order_fee']},1,'SYSTEM','SYSTEM','$time','SYSTEM','$time',3,'系统自动生成')";
			$pdo->query($sql);
			$_id = $pdo->lastInsertId();
			$p_sn = CGD_PREFIX.$_id;
			$sql = "UPDATE `purchase_info` SET `p_sn` = '".$p_sn."' WHERE id = ".$_id;
			$pdo->query($sql);

			//添加采购单明细信息 `purchase_goods`
			foreach($data['goods'] as $key => $val)
			{
				$sql = "INSERT INTO `purchase_goods`(`pinfo_id`, `style_sn`, `product_type_id`, `cat_type_id`, `num`, `info`) VALUES ($_id,'".$val['style_sn']."',".$val['product_type_id'].",".$val['cat_type_id'].",".$val['num'].",'".$val['info']."')";
				$pdo->query($sql);
				$g_id = $pdo->lastInsertId();

				//添加采购明细的属性表 `purchase_goods_attr`
				foreach($val['attr'] as $k => $v)
				{
					$sql = "INSERT INTO `purchase_goods_attr`( `g_id`, `code`, `name`, `value`) VALUES (".$g_id.",'".$v['code']."','".$v['name']."','".$v['value']."')";
					$pdo->query($sql);
				}

			}

		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			$this->error = 1;
			$this->return_sql = '';
			$this->error_msg = "数据异常，推送信息失败。";
			$this->return_msg = 0;
			$this->display();
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交


        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
		$this->error = 0;
		$this->return_sql = "";
		$this->return_msg = "成功";
		$this->display();
    }

    /*
     * 写入采购日志，不产列表推送
     */
    public function AddPurchaseLog() {
        $s_time = microtime();
        $error = 0;
        $time = date('Y-m-d H:i:s');
        $msg = '';
       
        if (isset($this->filter['rece_id']) && !empty($this->filter['rece_id'])){
            $rece_id = $this->filter['rece_id'];
        }else{
            $error = 1;
            $msg .= "没有关联的ID";
        }
        
        if (isset($this->filter['remark']) && !empty($this->filter['remark'])){
            $remark = $this->filter['remark'];
        }else{
            $error = 1;
            $msg .= '备注为空';
        }
        if (isset($this->filter['uid']) && !empty($this->filter['uid'])){
            $uid = $this->filter['uid'];
        }
        if (isset($this->filter['uname']) && !empty($this->filter['uname'])){
            $uname = $this->filter['uname'];
        }
        if($error){
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = $msg;
            $this->return_msg = 0;
            $this->display();
        }
        
        $sql = "insert into purchase_log (rece_detail_id,status,remark,uid,uname,time) values ('".$rece_id."','3','".$remark."',".$uid.",'".$uname."','".$time."');";
        //file_put_contents('./a.txt', $sql);
        $rs = $this->db->query($sql);
        if ($rs){
            $this->error = 0;
            $this->return_sql = $sql;
            $this->error_msg = "添加成功";
            $this->return_msg = 0;
            $this->display(); 
        }
        // 记录日志
    	$reponse_time = microtime() - $s_time;
    	$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        
    }
    
    /**
     * 根据起版号，获取起版信息
     * @param addtime 起版号
     */
    public function GetQiban(){
    	$s_time = microtime();
    	$error = 0;
    	if ( isset($this->filter['addtime']) && !empty($this->filter['addtime']) ) {
    		$sql = "SELECT `id`, `status`, `order_sn`, `customer` FROM `purchase_qiban_goods` WHERE `addtime` = '{$this->filter['addtime']}' LIMIT 1";
    		$row = $this->db->getRow($sql);
    		if(empty($row)){
    			$error = 1;
    			$content = "不存在起版号{$this->filter['addtime']}!!";
    		}
                else
                {
                        if($row['status'] == 2){
                                $error = 1;
                                $content = "起版号{$this->filter['addtime']}是无效状态，不能使用!!";
                        }
                        if($row['order_sn'] != '' && $row['customer'] != ''){
                                $error = 1;
                                $content = "起版号{$this->filter['addtime']}是已经绑定其他订单，不能重复使用!!";
                        }
                }

    	}else{
    		$error = 1;
    		$content = "起版号不能为空!!";
    	}

    	if($error)
    	{
    		$this->error = 1;
    		$this->return_sql = '';
    		$this->error_msg = $content;
    		$this->return_msg = 0;
    		$this->display();
    	}

    	$sql = "SELECT `info`, `price`, `addtime`, `xiangkou`, `shoucun` , `specifi`, `fuzhu` , `qibanfei` , `jinliao` , `jinse` , `gongyi` , `gongchang` , `kuanhao` , `zhengshu` , `xuqiu` , `status`,zhushi_num,cert,yanse,jingdu FROM `purchase_qiban_goods` WHERE `addtime` = '{$this->filter['addtime']}'";
    	$data = $this->db->getRow($sql);
    	// 记录日志
    	$reponse_time = microtime() - $s_time;
    	$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

    	//返回信息
    	if (!$data) {
    		$this->error = 1;
    		$this->return_sql = $sql;
    		$this->error_msg = "接口异常，获取数据失败";
    		$this->return_msg = array();
    		$this->display();
    	} else {
    		$this->error = 0;
    		$this->return_sql = $sql;
    		$this->return_msg = $data;
    		$this->display();
    	}
    }
    
    public function getQibanInfo(){
        $filter = $this->filter;
        if(empty($filter['addtime'])){
            $this->error = 1;
            $this->error_msg = "起版号不能为空";
            $this->return_msg = array();
            $this->display();
        }
        $sql = "SELECT * FROM `purchase_qiban_goods` WHERE `addtime` = '{$filter['addtime']}'";
        $row = $this->db->getRow($sql);
        if(empty($row)){
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = "起版号不存在!";
            $this->return_msg = array();
            $this->display();
        }else if($row['status'] == 2){
            $this->error = 1;
            $this->error_msg = "起版号{$filter['addtime']}是无效状态，不能使用!!";
            $this->display();
        }else if($row['order_sn'] != '' && $row['customer'] != ''){
            $this->error = 1;
            $this->error_msg = "起版号{$filter['addtime']}是已经绑定其他订单，不能重复使用!!";
            $this->display();
        }
        if(empty($row['kuanhao']) || $row['kuanhao']=="QIBAN"){
            $row['goods_name'] = '无款起版';
            $row['goods_image'] = '';
        }else{
            $row['goods_name'] = '有款起版';
            $sql = "select middle_img from front.app_style_gallery where style_sn='{$row['kuanhao']}'";
            $row['goods_image'] = $this->db->getOne($sql);
        }
        $dd = new DictView(new DictModel(1));
        
        $row['caizhi'] = $dd->getEnum('purchase.qiban_jinliao',$row['jinliao']);
        $row['yanse'] = $dd->getEnum('purchase.qiban_jinse',$row['jinse']);
        $row['facework'] = $dd->getEnum('purchase.qiban_gongyi',$row['gongyi']);
        $row['xiangqian'] = $dd->getEnum('purchase.qiban_xuqiu',$row['xuqiu']);
        $row['carat'] = $row['specifi'];
        $this->error = 0;
        $this->return_sql = $sql;
        $this->return_msg = $row;
        $this->display();
       
    }

    /**
     * 回写起版 订单信息
     * @param order_sn 订单号
     * @param opt 录单人
     * @param customer 客户姓名
     * @param addtime 起版号
     * 1/验证起版号是否存在，并且是启用状态的
     * 2/是否被其他订单使用
     */
    public function SetQiban() {
    	$s_time = microtime();
		$error = 0;
    	if ( isset($this->filter['addtime']) && !empty($this->filter['addtime']) ) {
    		$sql = "SELECT `id`, `status`, `order_sn`, `customer` FROM `purchase_qiban_goods` WHERE `addtime` = '{$this->filter['addtime']}' LIMIT 1";
    		$row = $this->db->getRow($sql);
    		if(empty($row)){
    			$error = 1;
    			$content = "不存在起版号{$this->filter['addtime']}!!";
    		}
    		if($row['status'] == 2){
    			$error = 1;
    			$content = "起版号{$this->filter['addtime']}是无效状态，不能使用!!";
    		}
    		if($this->filter['order_sn']!=''){
                if($row['customer'] != ''){
                    $error = 1;
                    $content = "起版号{$this->filter['addtime']}是已经绑定其他订单，不能重复使用!!";
                }
            }

    	}else{
    		$error = 1;
    		$content = "起版号不能为空!!";
    	}

    	if(!isset($this->filter['order_sn']) && !isset($this->filter['opt']) && !isset($this->filter['customer'])){
    		$error = 1;
    		$content = "参数不完整!!!";
    	}

    	if($error)
    	{
    		$this->error = 1;
    		$this->return_sql = '';
    		$this->error_msg = $content;
    		$this->return_msg = 0;
    		$this->display();
    	}

    	$sql = "UPDATE `purchase_qiban_goods` SET `order_sn` = '{$this->filter['order_sn']}' , `opt` = '{$this->filter['opt']}' , `customer` = '{$this->filter['customer']}' WHERE `addtime` = '{$this->filter['addtime']}'";
    	$data= $this->db->query($sql);
    	// 记录日志
    	$reponse_time = microtime() - $s_time;
    	$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

    	//返回信息
    	if (!$data) {
    		$this->error = 1;
    		$this->return_sql = $sql;
    		$this->error_msg = "接口异常，操作失败";
    		$this->return_msg = array();
    		$this->display();
    	} else {
			$this->error = 0;
    		$this->return_sql = $sql;
    		$this->return_msg = $data;
    		$this->display();
    	}
    }
	//获取有款采购并且去工厂生产的采购单列表add by zhangruiying
    public function GetProductInfoPurchaseList()
	{
		$page=isset($this->filter['page'])?intval($this->filter['page']):1;
		$pageSize=isset($this->filter['pageSize'])?intval($this->filter['pageSize']):10;
		$start=($page - 1) * $pageSize;

		$count_sql="select count(id) as num from `purchase_info` WHERE <%where%>";
		$sql = "SELECT `id`,`p_sn`,`t_id`,`p_sum`,`purchase_fee`,`put_in_type`,`make_uname`,`make_time`,`check_uname`,`check_time`,`p_status`,`p_info` FROM `purchase_info` WHERE <%where%>";
	    $where='is_style=1 and is_tofactory=1 and p_status=3';

        if(isset($this->filter['p_sn']) and !empty($this->filter['p_sn']))
		{

            $where .= " and p_sn in({$this->filter['p_sn']})";
        }
		$count_sql=str_replace('<%where%>',$where,$count_sql);

		$recordCount=$this->db->getOne($count_sql);
		//file_put_contents('./ruir.txt',$count_sql."\n",FILE_APPEND);
		$where .= " ORDER BY `id` DESC LIMIT {$start},{$pageSize}";
		$sql=str_replace('<%where%>',$where,$sql);
		//file_put_contents('./ruir.txt',$sql."\n",FILE_APPEND);
		$list=$this->db->getAll($sql);
		$data=array(
			'pageSize'=>$pageSize,
			'recordCount'=>$recordCount,//总记录
			'pageCount'=>ceil($recordCount/$pageSize),//共多少页
			'page'=>$page,
			'isFirst'=>$page>1?false:true,
			'isLast'=>$page==$recordCount?true:false,
			'start'=>$start+1,
			'data'=>$list
		);
		$this->return_sql = $sql."<br />".$count_sql;
        if (!$data['data'])
		{
            $this->error = 1;
            $this->return_msg = array();
        }
		else
		{
            $this->error = 0;
            $this->return_msg = $data;
        }
		$this->display();
    }
	//add by zhangruiying
	function GetPurchaseInfo()
	{
		if(isset($this->filter['id']) and !empty($this->filter['id']))
		{
			$sql="SELECT `id`,`p_sn`,`t_id`,`p_sum`,`purchase_fee`,`put_in_type`,`make_uname`,`make_time`,`check_uname`,`check_time`,`p_status`,`p_info` FROM `purchase_info` where id=".$this->filter['id']." and is_style=1 and is_tofactory=1";
			$row=$this->db->getRow($sql);
			if($row)
			{
				$this->error = 0;
				$this->return_msg = $row;
			}
			else
			{
				$this->error = 1;
				$this->return_sql=$sql;
				$this->return_msg =array();
				$this->error_msg ='查询失败';
			}
        }
		else
		{
			$this->error = 1;
            $this->error_msg ='ID为空';
		}
		$this->display();
	}
	//根据多个采购单号获取采取单信息
	function GetPurchaseList()
	{
		$sql = "SELECT `id`,`p_sn`,`t_id`,`p_sum`,`purchase_fee`,`put_in_type`,`make_uname`,`make_time`,`check_uname`,`check_time`,`p_status`,`p_info` FROM `purchase_info` WHERE is_style=1 and is_tofactory=1";
		if(isset($this->filter['ids']) and !empty($this->filter['ids']))
		{
			$sql.=" and id in({$this->filter['ids']})";
		}
		$row=$this->db->getAll($sql);
		if($row)
		{
				$this->error = 0;
				$this->return_sql=$sql;
				$this->return_msg = $row;
		}
		else
		{
				$this->error = 1;
				$this->return_sql=$sql;
				$this->return_msg =array();
				$this->error_msg ='查询失败';
		}
		$this->display();

	}
	function GetPurchaseType()
	{
		$sql = "SELECT id,t_name FROM `purchase_type`";
		$row=$this->db->getAll($sql);
		if($row)
		{
				$arr=array();
				foreach($row as $key=>$v)
				{
					$arr[$v['id']]=$v['t_name'];
				}
				$this->error = 0;
				$this->return_sql=$sql;
				$this->return_msg = $arr;
		}
		else
		{
				$this->error = 1;
				$this->return_sql=$sql;
				$this->return_msg =array();
				$this->error_msg ='查询失败';
		}
		$this->display();

	}
    /* ------------------------------------------------------ */

    //-- 返回内容
    //-- by col
    /* ------------------------------------------------------ */
    public function display() {
        $res = array("error" => intval($this->error), "error_msg" => $this->error_msg, "return_msg" => $this->return_msg, "return_sql" => $this->return_sql);
        die(json_encode($res));
    }

    /* ------------------------------------------------------ */

    //-- 记录日志信息
    //-- by haibo
    /* ------------------------------------------------------ */
    public function recordLog($api, $response_time, $str) {
        define('ROOT_LOG_PATH', str_replace('api/api.php', '', str_replace('\\', '/', __FILE__)));
        if (!file_exists(ROOT_LOG_PATH . 'logs/api_logs')) {
            mkdir(ROOT_LOG_PATH . 'logs/api_logs', 0777);
            chmod(ROOT_LOG_PATH . 'logs/api_logs', 0777);
        }
        $content = $api . "||" . $response_time . "||" . $str . "||" . date('Y-m-d H:i:s') . "\n";
        $file_path = ROOT_LOG_PATH . 'logs/api_logs/' . date('Y') . "_" . date('m') . "_" . date('d') . "_api_log.txt";
        file_put_contents($file_path, $content, FILE_APPEND);
    }
    /**
     * 根据起版Id查询起版记录
     * @author gaopeng
     */
    public function GetQiBianGoodsByQBId(){

        if(!empty($this->filter['qb_id'])){
            $sql = "SELECT * FROM `purchase_qiban_goods` WHERE addtime='{$this->filter['qb_id']}'";
            $res = $this->db->getRow($sql);
            if(!empty($res)){
                $this->error = 0;
                $this->return_sql = $sql;
                $this->error_msg = "起版记录查询成功";
                $this->return_msg = $res;
                $this->display();
            }else{
                $this->error = 1;
                $this->return_sql = $sql;
                $this->error_msg = "未查询到起版记录";
                $this->return_msg = array();
                $this->display();
            }
        
        }else{
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = "参数错误";
        }     

    }

}

?>
