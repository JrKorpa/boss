<?php
/**
 *  -------------------------------------------------
 *   @file		: DiaBillModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-03-22 15:40:08
 *   @update	:
 *  -------------------------------------------------
 */
class DiaBillSearchModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'stone_bill';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID",
"bill_no"=>"单号",
"processors"=>"加工商",
"factory"=>" ",
"price_total"=>"价格总计",
"dia_package"=>"石包号",
"create_user"=>"制单人",
"num"=>"总数量",
"weight"=>"总重量",
"paper_no"=>"纸质单号",
"create_time"=>"制单时间",
"check_time"=>"审核时间",
"status"=>"状态");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url DiaBillController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT b.* FROM `".$this->table()."` b INNER JOIN `stone_bill_details` `bd` ON `bd`.`bill_id` = `b`.`id`";
		$str = '';
		if($where['remark'] != "")
		{
			$str .= "`b`.`remark` like \"".addslashes($where['remark'])."%\" AND ";
		}
		if(!empty($where['bill_no']))
		{
			if(count($where['bill_no']) > 1){
                $str .= "`b`.`bill_no` in('".implode("','", $where['bill_no'])."') AND ";
            }else{
                $str .= "`b`.`bill_no` like \"".addslashes($where['bill_no'][0])."%\" AND ";
            }
		}
        if(!empty($where['dia_package']))
        {
            if(count($where['dia_package']) > 1){
                $str .= "`bd`.`dia_package` in('".implode("','", $where['dia_package'])."') AND ";
            }else{
                $str .= "`bd`.`dia_package` like \"".addslashes($where['dia_package'][0])."%\" AND ";
            }
        }
        if(!empty($where['paper_no']))
        {
            if(count($where['paper_no']) > 1){
                $str .= "`b`.`paper_no` in('".implode("','", $where['paper_no'])."') AND ";
            }else{
                $str .= "`b`.`paper_no` like \"".addslashes($where['paper_no'][0])."%\" AND ";
            }
        }
        if(!empty($where['create_user']))
        {
            if(count($where['create_user']) > 1){
                $str .= "`b`.`create_user` in('".implode("','", $where['create_user'])."') AND ";
            }else{
                $str .= "`b`.`create_user` like \"".addslashes($where['create_user'][0])."%\" AND ";
            }
        }
        if(!empty($where['check_user']))
        {
            if(count($where['check_user']) > 1){
                $str .= "`b`.`check_user` in('".implode("','", $where['check_user'])."') AND ";
            }else{
                $str .= "`b`.`check_user` like \"".addslashes($where['check_user'][0])."%\" AND ";
            }
        }
        if(!empty($where['status']))
        {
            $str .= "`b`.`status`='".$where['status']."' AND ";
        }
        if(!empty($where['bill_type']))
        {
            $str .= "`b`.`bill_type`='".$where['bill_type']."' AND ";
        }
        if(!empty($where['processors_id']))
        {
            $str .= "`b`.`processors_id`='".$where['processors_id']."' AND ";
        }
        if(!empty($where['factory_id']))
        {
            $str .= "`b`.`factory_id`='".$where['factory_id']."' AND ";
        }

        if($where['create_time_start'] != '')
        {
            $str .= "`b`.`create_time` >= '".$where['create_time_start']." 00:00:00' AND ";
        }
        if($where['create_time_end'] != '')
        {
            $str .= "`b`.`create_time` <= '".$where['create_time_end']." 23:59:59' AND ";
        }
        if($where['check_time_start'] != '')
        {
            $str .= "`b`.`check_time` >= '".$where['check_time_start']." 00:00:00' AND ";
        }
        if($where['check_time_end'] != '')
        {
            $str .= "`b`.`check_time` <= '".$where['check_time_end']." 23:59:59' AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " GROUP BY `b`.`id` ORDER BY `b`.`id` DESC";
		$data = $this->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

    public function getPageList($sql, $params = array(), $page = 1, $pageSize = 20, $useCache = false)
    {
        try {
            $countSql = "SELECT COUNT(*) as count FROM (" . $sql . ") AS b";
            //$countSql = "SELECT COUNT(0) as rows,SUM(wb.goods_num) as total_num,SUM(wb.total_chengben) as total_price,SUM(wb.shijia) as total_shijia FROM (" . $sql . ") AS wb";

            $total = $this->db()->getRow($countSql);
            //$data['total_num'] = $total['total_num']?$total['total_num']:0;
            //$data['total_price'] = $total['total_price']?$total['total_price']:0.00;
            //$data['total_shijia'] = $total['total_shijia']?$total['total_shijia']:0.00;
            
            $data['pageSize'] = (int)$pageSize < 1 ? 20 : (int)$pageSize;
            $data['recordCount'] = $this->db()->getOne($countSql, $params, $useCache);
            //$data['recordCount']=$total['rows']?$total['rows']:0;
            $data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
            $data['page'] = $data['pageCount'] == 0 ? 0 : ((int)$page < 1 ? 1 : (int)$page);
            $data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
            $data['isFirst'] = $data['page'] > 1 ? false : true;
            $data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
            $data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] +   1;
            $data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
            $data['data'] = $this->db()->query($data['sql'], $params, $useCache);
        }
        catch (exception $e) {
            throw $e;
        }
        return $data;
    }

    /***************************************************************************
    fun:getDetailByOrderId
    description:根据单据号获取明细信息
    ****************************************************************************/
    public function getDetailByOrderId($id)
    {
        $sql = "select sbd.dia_package,sbd.purchase_price,sbd.specification,sbd.color,sbd.neatness,sbd.cut,sbd.symmetry,sbd.polishing,sbd.fluorescence,sb.num,sb.weight from `stone_bill_details` sbd inner join `stone_bill` sb on sbd.bill_id = sb.id  where sbd.bill_id='{$id}' order by sbd.id asc";
        return $this->db()->getAll($sql);
    }

    /**
    *保存单据
    */
    public function saveBillData($billInfo,$goods_info)
    {
        
        $pdo = $this->db()->db();//pdo对象
        try {
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            $bill_no = $this->create_bill_no('SLD');
            //写入单头
            $sql = "INSERT INTO `stone_bill` (`id`,`bill_no`,`bill_type`,`status`,`processors_id`,`processors_name`,`source`,`price_total`,`create_user`,`num`,`weight`,`create_time`,`remark`) VALUES (NULL,'{$bill_no}','{$billInfo['bill_type']}','{$billInfo['status']}','{$billInfo['processors_id']}','{$billInfo['processors_name']}','{$billInfo['source']}','{$billInfo['price_total']}','{$billInfo['create_user']}','{$billInfo['num']}','{$billInfo['weight']}','{$billInfo['create_time']}','{$billInfo['remark']}')";

            $pdo->query($sql);
            $id = $pdo->lastInsertId();

            $bill_no= $this->create_bill_no('SLD',$id);
            $sql = "UPDATE `stone_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$id}";
            $pdo->query($sql);

            //插入单据明细
            foreach ($goods_info as $key => $val) {

                //$sql = "INSERT INTO `stone` (`id`,`dia_package`,`purchase_price`,`status`,`sup_id`,`sup_name`,`specification`,`color`,`neatness`,`cut`,`symmetry`,`polishing`,`fluorescence`) VALUES (NULL,'{$val['dia_package']}','{$val['purchase_price']}',3,'{$billInfo['processors_id']}','{$billInfo['processors_name']}','{$val['specification']}','{$val['color']}','{$val['neatness']}','{$val['cut']}','{$val['symmetry']}','{$val['polishing']}','{$val['fluorescence']}')";
                //$pdo->query($sql);

                $sql="INSERT INTO `stone_bill_details` (`id`,`bill_id`,`dia_package`,`purchase_price`,`specification`,`color`,`neatness`,`cut`,`symmetry`,`polishing`,`fluorescence`,`num`,`weight`) VALUES (NULL,{$id},'{$val['dia_package']}','{$val['purchase_price']}','{$val['specification']}','{$val['color']}','{$val['neatness']}','{$val['cut']}','{$val['symmetry']}','{$val['polishing']}','{$val['fluorescence']}',1,0)";
                $pdo->query($sql);
            }
            
        } catch (Exception $e) {
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return array('id'=>$id, 'bill_no'=>$bill_no);
    }

    /**
    *保存单据
    */
    public function updateBillData($billInfo,$goods_info)
    {
        
        $pdo = $this->db()->db();//pdo对象
        try {
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            $id = $billInfo['id'];
            //更新单头
            $sql = "UPDATE `stone_bill` SET processors_id = '{$billInfo['processors_id']}',processors_name = '{$billInfo['processors_name']}', price_total = '{$billInfo['price_total']}',num = '{$billInfo['num']}',weight = '{$billInfo['weight']}',remark = '{$billInfo['remark']}' WHERE id = '{$id}'";
            $pdo->query($sql);
            //删除之前明细
            $sql = "DELETE FROM `stone_bill_details` WHERE `bill_id` = '{$id}'";
            $pdo->query($sql);
            //更新单据明细
            foreach ($goods_info as $key => $val) {
                $sql="INSERT INTO `stone_bill_details` (`id`,`bill_id`,`dia_package`,`purchase_price`,`specification`,`color`,`neatness`,`cut`,`symmetry`,`polishing`,`fluorescence`,`num`,`weight`) VALUES (NULL,{$id},'{$val['dia_package']}','{$val['purchase_price']}','{$val['specification']}','{$val['color']}','{$val['neatness']}','{$val['cut']}','{$val['symmetry']}','{$val['polishing']}','{$val['fluorescence']}','{$val['num']}','{$val['weight']}')";
                $pdo->query($sql);
            }
            
        } catch (Exception $e) {
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return true;
    }

    /**
    *用石单更新单据
    */
    public function updateBillYsdData($billInfo,$goods_info)
    {
        
        $pdo = $this->db()->db();//pdo对象
        try {
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            $id = $billInfo['id'];
            //更新单头
            $sql = "UPDATE `stone_bill` SET processors_id = '{$billInfo['processors_id']}',processors_name = '{$billInfo['processors_name']}',factory_id = '{$billInfo['factory_id']}',factory_name = '{$billInfo['factory_name']}', price_total = '{$billInfo['price_total']}',num = '{$billInfo['num']}',weight = '{$billInfo['weight']}',remark = '{$billInfo['remark']}',price_total = '{$billInfo['price_total']}',source = '{$billInfo['source']}',paper_no = '{$billInfo['paper_no']}' WHERE id = '{$id}'";
            $pdo->query($sql);
            //删除之前明细
            $sql = "DELETE FROM `stone_bill_details` WHERE `bill_id` = '{$id}'";
            $pdo->query($sql);
            //更新单据明细
            foreach ($goods_info as $key => $val) {
                $sql="INSERT INTO `stone_bill_details` (`id`,`bill_id`,`dia_package`,`purchase_price`,`specification`,`color`,`neatness`,`cut`,`symmetry`,`polishing`,`fluorescence`,`num`,`weight`,`price`) VALUES (NULL,'{$id}','{$val['dia_package']}','{$val['purchase_price']}','{$val['specification']}','{$val['color']}','{$val['neatness']}','{$val['cut']}','{$val['symmetry']}','{$val['polishing']}','{$val['fluorescence']}','{$val['num']}','{$val['weight']}','{$val['price']}')";
                $pdo->query($sql);
            }
        } catch (Exception $e) {
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return true;
    }

    /**
    *保存单据
    */
    public function saveBillTZDData($billInfo,$goods_info)
    {
        
        $pdo = $this->db()->db();//pdo对象
        try {
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            $bill_no = $this->create_bill_no('TZD');
            //写入单头
            $sql = "INSERT INTO `stone_bill` (`id`,`bill_no`,`bill_type`,`status`,`processors_id`,`processors_name`,`create_user`,`num`,`create_time`,`remark`,`price_total`) VALUES (NULL,'{$bill_no}','{$billInfo['bill_type']}','{$billInfo['status']}','{$billInfo['processors_id']}','{$billInfo['processors_name']}','{$billInfo['create_user']}','{$billInfo['num']}','{$billInfo['create_time']}','{$billInfo['remark']}','{$billInfo['price_total']}')";

            $pdo->query($sql);
            $id = $pdo->lastInsertId();

            $bill_no= $this->create_bill_no('TZD',$id);
            $sql = "UPDATE `stone_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$id}";
            $pdo->query($sql);

            //插入单据明细
            foreach ($goods_info as $key => $val) {

                $sql="INSERT INTO `stone_bill_details` (`id`,`bill_id`,`dia_package`,`purchase_price`,`specification`,`color`,`neatness`,`cut`,`symmetry`,`polishing`,`fluorescence`,`num`,`weight`) VALUES (NULL,{$id},'{$val['dia_package']}','{$val['purchase_price']}','{$val['specification']}','{$val['color']}','{$val['neatness']}','{$val['cut']}','{$val['symmetry']}','{$val['polishing']}','{$val['fluorescence']}',1,0)";
                $pdo->query($sql);
            }
            
        } catch (Exception $e) {
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return true;
    }

    /**
    *YSD保存单据
    */
    public function saveBillYSDData($billInfo,$goods_info)
    {
        
        $pdo = $this->db()->db();//pdo对象
        try {
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            $bill_no = $this->create_bill_no('YSD');
            //写入单头
            $sql = "INSERT INTO `stone_bill` (`id`,`bill_no`,`bill_type`,`status`,`factory_id`,`factory_name`,`processors_id`,`processors_name`,`source`,`price_total`,`create_user`,`num`,`weight`,`create_time`,`remark`,`paper_no`) VALUES (NULL,'{$bill_no}','{$billInfo['bill_type']}','{$billInfo['status']}','{$billInfo['factory_id']}','{$billInfo['factory_name']}','{$billInfo['processors_id']}','{$billInfo['processors_name']}','{$billInfo['source']}','{$billInfo['price_total']}','{$billInfo['create_user']}','{$billInfo['num']}','{$billInfo['weight']}','{$billInfo['create_time']}','{$billInfo['remark']}','{$billInfo['paper_no']}')";

            $pdo->query($sql);
            $id = $pdo->lastInsertId();

            $bill_no= $this->create_bill_no('YSD',$id);
            $sql = "UPDATE `stone_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$id}";
            $pdo->query($sql);

            //插入单据明细
            foreach ($goods_info as $key => $val) {
                $sql="INSERT INTO `stone_bill_details` (`id`,`bill_id`,`dia_package`,`purchase_price`,`specification`,`color`,`neatness`,`cut`,`symmetry`,`polishing`,`fluorescence`,`num`,`weight`,`price`) VALUES (NULL,{$id},'{$val['dia_package']}','{$val['purchase_price']}','{$val['specification']}','{$val['color']}','{$val['neatness']}','{$val['cut']}','{$val['symmetry']}','{$val['polishing']}','{$val['fluorescence']}','{$val['num']}','{$val['weight']}','{$val['price']}')";
                $pdo->query($sql);
            }
        } catch (Exception $e) {
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return array('id'=>$id, 'bill_no'=>$bill_no);
    }

    /**
    *单据审核
    */
    public function checkBillStatus($id, $bill_info)
    {
        $pdo = $this->db()->db();//pdo对象
        $time = date('Y-m-d H:i:s');
        $user = $_SESSION['userName'];
        try {
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务

            //单据状态改为已审核;
            $sql = "UPDATE `stone_bill` SET `status` = 2,`check_user` = '{$user}',`check_time` = '{$time}' WHERE `id` = {$id}";
            $pdo->query($sql);

            $billDetails = $this->getDetailsInfo($id);
            //审核石包录入单
            if($bill_info['bill_type'] == 'SLD'){
                //单据明细写入石包列表;
                if(!empty($billDetails)){
                    foreach ($billDetails as $key => $val) {
                        $dia_package = $val['dia_package'];
                        $checkI = $this->check_dia_package($dia_package);
                        if(!empty($checkI)){
                            $pdo->rollback();//事务回滚
                            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                            exit('石包号存在有效记录'.$dia_package);
                        }
                        $sql = "INSERT INTO `stone` (`id`,`dia_package`,`purchase_price`,`status`,`sup_id`,`sup_name`,`specification`,`color`,`neatness`,`cut`,`symmetry`,`polishing`,`fluorescence`) VALUES (NULL,'{$dia_package}','{$val['purchase_price']}',1,'{$bill_info['processors_id']}','{$bill_info['processors_name']}','{$val['specification']}','{$val['color']}','{$val['neatness']}','{$val['cut']}','{$val['symmetry']}','{$val['polishing']}','{$val['fluorescence']}')";
                        $pdo->query($sql);
                    }
                }
            }
            //审核调整单，则把石包价格更改
            if($bill_info['bill_type'] == 'TZD'){
                if(!empty($billDetails)){
                    foreach ($billDetails as $key => $value) {
                        $dia_package = $value['dia_package'];
                        $checkI = $this->check_dia_package($dia_package);
                        if(empty($checkI)){
                            $pdo->rollback();//事务回滚
                            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                            exit('石包号无效或不存在'.$dia_package);
                        }
                        $sql = "UPDATE `stone` SET `purchase_price` = '".$value['purchase_price']."' WHERE dia_package = '".$dia_package."' AND `status` = 1";
                        $pdo->query($sql);
                    }
                }
            }
        } catch (Exception $e) {
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return true;
    }

    /**
    *单据驳回
    */
    public function rejectBillStatus($id, $bill_info)
    {
        $pdo = $this->db()->db();//pdo对象
        $time = date('Y-m-d H:i:s');
        $user = $_SESSION['userName'];
        try {
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务

            //吧单据状态改为已保存;
            $sql = "UPDATE `stone_bill` SET `status` = 1 WHERE `id` = {$id}";
            $pdo->query($sql);

            //把石包状态改为保存状态 排除YSD
            if($bill_info['bill_type'] != 'YSD'){
                $sql = "UPDATE `stone` SET `status` = 3 WHERE `dia_package` IN(SELECT `dia_package` FROM `stone_bill_details` WHERE bill_id = {$id})";
                $pdo->query($sql);
            }
        } catch (Exception $e) {
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return true;
    }

    /**
     * create_bill_no() 生成单据号
     */
    public function create_bill_no($type, $bill_id = '1')
    {
        $bill_id = substr($bill_id, -4);
        $bill_no = $type . date('Ymd', time()) . str_pad($bill_id, 4,
            "0", STR_PAD_LEFT);
        return $bill_no;
    }

    /**
     * 确认是否存在有效石包号
     */
    public function check_dia_package($dia_package)
    {
        $sql = "select * from `stone` where dia_package = '{$dia_package}' and `status` = 1";
        return $this->db()->getRow($sql);
    }

    /**
     * 根据单据号取明细
     */
    public function getDetailsInfo($id)
    {
        $sql = "select * from `stone_bill_details` where bill_id = '{$id}'";
        return $this->db()->getAll($sql);
    }
}

?>