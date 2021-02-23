<?php
/**
 *  -------------------------------------------------
 *   @file		: GoodsWarehouseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 10:55:30
 *   @update	:
 *  -------------------------------------------------
 */
class GoodsWarehouseModel extends Model
{
    function __construct ($id=NULL,$strConn="")
    {
        $this->_objName = 'goods_warehouse';
        $this->pk='id';
        $this->_prefix='';
        $this->_dataObject = array(
            "id"=>"主键",
            "good_id"=>"货号",
            "warehouse_id"=>"仓库ID 关联warehouse表ID",
            'box_id' => "柜位ID 关联warehouse_box表主键",
            "add_time"=>"入库时间 ",
            "create_time"=>"上架时间",
            'create_user'=>"上架操作人");
        parent::__construct($id,$strConn);
    }

    /**
     *	pageList，分页列表
     *
     *	@url GoodsWarehouseController/search
     */
    function pageList ($where,$page,$pageSize=10,$useCache=true)
    {
        $sql = "SELECT `m`.`id`,`m`.`good_id`,`g`.`goods_sn`,`m`.`warehouse_id`,`m`.`box_id`,`g`.`box_sn`,`m`.`add_time`,`m`.`create_time`,`m`.`create_user` FROM warehouse_goods AS g  join  `".$this->table()."` AS `m` on g.goods_id = m.good_id where g.`is_on_sale`=2  ";

        $str = "";
        if(isset($where['hidden']) && $where['hidden'] != ''){
            $str .= " g.hidden = ".$where['hidden']." AND ";
        }
        if($where['good_id'] != ''){
            $str .= "`m`.`good_id`  like \"%".addslashes($where['good_id'])."%\" AND ";
        }
        //if($where['warehouse_id'] != ''){$str .= "`m`.`warehouse_id` = {$where['warehouse_id']} AND ";}
        if(isset($where['warehouse_id[]']) and !empty($where['warehouse_id[]']))
        {
            $where['warehouse_id[]']=implode(',',$where['warehouse_id[]']);
            //$sql .= "  AND main.`buchan_fac_opra` in (".$where['warehouse_id[]'].")  ";
            $str .= "`g`.`warehouse_id` in ({$where['warehouse_id[]']}) AND ";
        }
        //exit($str);exit;
        if($where['box_sn'] != '') {
            $str .= "`g`.`box_sn` = '{$where['box_sn']}' AND ";
        }
        if($where['goods_sn'] != '') {
            $str .= "`g`.`goods_sn`  like \"%".addslashes($where['goods_sn'])."%\" AND ";
        }
        if($where['status'] != ''){
            if($where['status'] == '1'){
                $str .= "`g`.`box_sn` <> '0-00-0-0' AND ";
            }
            if($where['status'] == '2'){
                $str .= "`g`.`box_sn` = '0-00-0-0' AND ";
            }
        }
        if($where['start_time'] ){
            if($where['count_type']){//超三天未上架
                $start_time=strtotime($where['start_time']);
                $three_day_ago=date('Y-m-d H:i:s',$start_time-3600*24*3);
                $str .= "`g`.`addtime` <= '{$three_day_ago}' AND ";
            }
            else{
                $str .= "`g`.`addtime` <= '{$where['start_time']}' AND ";
            }
        }
        if(!empty($where['addtime'])){
            $str .= " `g`.`addtime` >= '{$where['addtime']}' AND ";
        }
        //不显示已销售的、已返厂的、店面的货品柜位信息  暂时不用了 默认显示的是库存的
        //if($where['close'] != ''){
        //	$str .= " `g`.`is_on_sale` NOT IN (3,9) AND `g`.`company_id` IN (58,445) ";
        //}
//		if($str)
//		{
//			$str = rtrim($str,"AND ");//这个空格很重要
//			$sql .=" AND `g`.`is_on_sale`=2 AND ".$str;
//		}
        if ($str){
            $str = rtrim($str,"AND ");//这个空格很重要
            $sql .= " AND ".$str;
        }
        $sql .= "  ORDER BY `m`.`id` DESC";
        // echo $sql;
        $data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
        return $data;
    }

    /**
     * 普通查询
     * @param $fields String 要查询的字段
     * @param $where String 要查询条件
     * @param $is_all Int 1/查询多条记录 2/查询一条记录 3/查询单个字段
     */
    public function select2($fields, $where, $is_all = 1 ){
        $sql = "SELECT {$fields} FROM `goods_warehouse` WHERE {$where} ORDER BY `id` DESC";
        if($is_all == 1){
            return $this->db()->getAll($sql);
        }else if($is_all == 2){
            return $this->db()->getRow($sql);
        }else if($is_all == 3){
            return $this->db()->getOne($sql);
        }
    }

    /**
     * 根据商品号获取所属的 仓库/柜位
     */
    public function getWarehouseInfo($good_id){
        $sql = "SELECT * FROM {$this->table()} WHERE good_id = '{$good_id}' ";
        return $this->db()->getRow($sql);
    }

    /**
     * 检测同一批货号是否是在同一个仓库
     * @param $strArr Array 货号数组
     * @param $warehouse Int 指定仓库ID
     */
    public function checkOnWarehouse($strArr, $warehouse){
        $result = array('type'=>1, 'error'=>'');
        if($strArr[0] == ''){
            $result = array('type'=>0, 'error'=>array('请输入货号!'));
            return $result;
        }
        $sql = "SELECT name FROM `warehouse` WHERE id = {$warehouse} ";
        $warehouse_name = $this->db()->getOne($sql);
        foreach ($strArr as $k => $v) {

            $sql = "SELECT warehouse_id FROM `{$this->table()}` WHERE good_id = '{$v}' ";
            $data = $this->db()->getOne($sql);
            if($data != $warehouse){
                $k++;
                $result['error'] .= "第{$k}个货号：<b style='color:red;'>{$v}</b> 不在 `{$warehouse_name}` 中<br/>";
                $result['type'] = 0;
            }
        }
        return $result;
    }

    /** 检测货号是否存在入库并且是库存状态，即数据库goods_warehouse 中没有数据 **/
    public function checkRepeat($strArr){
        $result = array('type'=>1, 'error'=>array());
        foreach ($strArr as $k => $v) {
            $k +=1;
            $sql = "SELECT `is_on_sale` FROM `warehouse_goods` WHERE `goods_id` = '{$v}' limit 1";
            $is_no_sale = $this->db()->getOne($sql);
            if($is_no_sale != 2){
                $result['error'] = "第{$k}个货号:`{$v}` 不是库存状态，无法上架.\r\n";	//记录不存在的记录
                $result['type'] = 0;
                break;
            }
            $sql = "SELECT `id` FROM `{$this->table()}` WHERE `good_id` = '{$v}' limit 1";
            if(empty($this->db()->getRow($sql)) ){
                $result['error'] = "第{$k}个货号:`{$v}` 没有入库，无法上架.\r\n";	//记录不存在的记录
                $result['type'] = 0;
                break;
            }
        }
        return $result;
    }

    /** 上架 **/
    public function shangjia($strArr, $box_id){
        $strArr = array_unique($strArr);
        //查询上架的柜位号
        $sql = "SELECT `box_sn` FROM `warehouse_box` WHERE `id` = {$box_id} LIMIT 1";
        $box_sn = $this->db()->getOne($sql);



        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            $logBoxArr=array();
            foreach($strArr AS $k=>$goods_id){
                $olddo = $this->getWarehouseInfo($goods_id);
                $newdo=array(
                    'id'=>$olddo['id'],
                    'box_id' =>$box_id,
                    'create_user' => $_SESSION['userName'],
                    'create_time' => date('Y-m-d H:i:s'),
                );
                $sql ="UPDATE `goods_warehouse` SET  `box_id` = {$newdo['box_id']}, `create_user` = '{$newdo['create_user']}', `create_time` = '{$newdo['create_time']}' WHERE `id` = {$newdo['id']}";
                $pdo->query($sql);

                //更新warehouse_goods 表的box_sn 字段
                $sql = "UPDATE `warehouse_goods` SET `box_sn` = '{$box_sn}' WHERE `goods_id` = '{$goods_id}'";
                $pdo->query($sql);

                //获取下架前的柜位
                $sql = "SELECT `box_sn` FROM `warehouse_box` WHERE `id` = {$olddo['box_id']} LIMIT 1";
                $box_sn1 = $this->db()->getOne($sql);
                $logBoxArr[$k]['goods_id']=$goods_id;
                if ($box_sn == '0-00-0-0'){
                    $logBoxArr[$k]['box_sn']=$box_sn1;
                }else{
                    $logBoxArr[$k]['box_sn']=$box_sn;
                }
            }
        }
        catch(Exception $e){//捕获异常
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        //添加日志
        $logmodel = new BoxGoodsLogModel(22);
        if ($box_sn == '0-00-0-0'){
            $logmodel->addLog1($logBoxArr,1);//下架
        }else {
            $logmodel->addLog1($logBoxArr,2);//上架
        }
        return true;
    }

    /**
     * 检测柜位是否禁用
     * @param $space_id Int 筐位ID
     */
    public function checkBoxToDelete($box_id){
        $sql = "SELECT `is_deleted` FROM `warehouse_box` WHERE `id` = {$box_id} ";
        return $this->db()->getOne($sql);
    }

    /**
     * 检测货品是否已经上架
     * @param $good_id_str 货号 格式：id1,id2,id3.....
     */
    public function checkIsShangJia($good_id_str){
        $is_shangjia = array('shangjia'=>0, 'error'=>'');
        $num = 0;
        $goods_id_arr = explode(',', $good_id_str);
        foreach ($goods_id_arr as $key => $value)
        {
            //已经上架的
            if( $this->getGoodShangjia($value) )
            {
                $num = ++$key;
                $is_shangjia['error'] .= "第 {$num} 个货号{$value} 已经上架,不能重复做上架处理<br/>";
            }
        }
        if($is_shangjia['error'] != ''){
            $is_shangjia['shangjia'] = 1;
        }
        return $is_shangjia;
    }

    /**
     * 根据货号，获取该货品的筐位(用于检测货品是否上架)
     */
    public function getGoodShangjia($good_id){
        $sql = "SELECT `box_id` FROM `{$this->table()}` WHERE `good_id` = '{$good_id}'";
        return $this->db()->getOne($sql);
    }

    /*通过货号，获取货品所在仓库的默认柜位ID*/
    public function getDefaultBoxIdBygoods($goods_id){
        $sql = "SELECT `warehouse_id` FROM `goods_warehouse` WHERE `good_id` = '$goods_id'";
        $w_id = $this->db()->getOne($sql);
        if(!$w_id){
            return false;
        }
        $sql = "SELECT `id` FROM `warehouse_box` WHERE `warehouse_id` = {$w_id} AND `box_sn` = '0-00-0-0' LIMIT 1";
        $default_box = $this->db()->getOne($sql);
        return $default_box;
    }

    //一键倒入柜位信息
    public function insertAdd($datas){

        $sql = "INSERT INTO `goods_warehouse` (`good_id` , `warehouse_id` , `box_id` , `add_time` , `create_time` , `create_user`) VALUES ( '{$datas['good_id']}' , {$datas['warehouse_id']} , {$datas['box_id']} , '{$datas['add_time']}' , '{$datas['create_time']}' , '{$datas['create_user']}')";
        $res = $this->db()->query($sql);
        if($res){
            return array('success' => 1 , 'error' => '操作成功');
        }else{
            return array('success' => 0 , 'error' => '报错SQL:'.$sql);
        }
    }

    //批量下架
    /**
     * @param $goods_id_arr
     * @return array
     */
    public function BatchUndercarriage($goods_id_arr){
        $num = 0;
        $passed_str = $pass_str = $no_pass_str = $no_exist =  '';
        $logModel = new BoxGoodsLogModel(22);
        $pan = array();		//用来放盘点过的货号 容器
        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            //分别获取每个货所在仓库的默认柜位
            $sth = $pdo->prepare("SELECT `a`.`warehouse_id`, `a`.`warehouse` ,`a`.`is_on_sale`, `b`.`id` AS `box_id` FROM `warehouse_goods` AS `a`, `warehouse_box` AS `b` WHERE `a`.`goods_id` = ? AND `a`.`warehouse_id` = `b`.`warehouse_id` AND `b`.`box_sn` = '0-00-0-0'");

            $xth = $pdo->prepare("UPDATE `goods_warehouse` SET `warehouse_id` = ? , `box_id` = ? WHERE good_id = ?");

            //获取货品当前所在的柜位
            foreach($goods_id_arr AS $val){
                $sth->execute(array($val));
                $row = $sth->fetch();
                if(!isset($row) || empty($row)){
                    $no_exist .= "<span style='color:blue;'>{$val} 仓库查不到该货</span><br/>";
                    continue;
                }
                if($row['is_on_sale'] != 2){
                    $no_pass_str .= "<span style='color:red;'>{$val} 不是库存状态的货</span><br/>";
                    continue;
                }
                //$sql = "SELECT `goods_id`,`type`,`create_time`,`create_user` FROM `box_goods_log` WHERE `goods_id` = '{$val}' AND `warehouse` = '{$row['warehouse']}' ORDER BY `id` DESC LIMIT 1";
                $sql = "SELECT `goods_id`,`type`,`create_time`,`create_user` FROM `box_goods_log` WHERE `goods_id` = '{$val}' ORDER BY `id` DESC LIMIT 1";
                if($d = $this->db()->getRow($sql))
                {
                    if($d['type'] == 1){
                        $pan[] = $val;
                        $passed_str .= "<span style='color:#AB62AC;'>{$val} 已经操作过下架。操作人：{$d['create_user']} / 操作时间:{$d['create_time']}</span><br/>";
                        continue;
                    }
                }/*else{
					//检测货品是否已经下架
					$sql = "SELECT `id` FROM `box_goods_log` WHERE `box_sn` = '0-00-0-0' AND `goods_id` = '{$val}' ORDER BY `id` desc LIMIT 1";
					if($this->db()->getOne($sql)){
						$passed_str .= "<span style='color:#AB62AC;'>{$val} 已经操作过下架</span><br/>";
						continue;
					}
				}*/

                //写入出入库记录
                $go = array_diff(array($val), $pan);
                if(!empty($go)){
                    $logModel->addLog(array($val), $type=1);
                }

                //更新warehouse_goods 表的box_sn 字段
                $sql = "UPDATE `warehouse_goods` SET `box_sn` = '0-00-0-0' WHERE `goods_id` = '{$val}'";
                $pdo->query($sql);

                $num ++;
                //将货放在默认柜位上
                $res = $xth->execute(array($row['warehouse_id'] , $row['box_id'] , $val));
                $pass_str .= $res ? "{$val} 下架成功<br/>" : "<span style='color:red;'>{$val}下架失败，请重新操作</span><br/>";

            }

        }
        catch(Exception $e){//捕获异常
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return array('success' => 0 , 'error'=>'事物执行失败!');
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交

        return array('success'=>1 , 'error' => $no_pass_str.$no_exist.$passed_str.$pass_str, 'success_num' =>$num);
    }

    /**一键倒入柜位信息 （插入默认柜位）**/
    public function addbox($warehouse_id){
        $box_sn = '0-00-0-0';
        $create_time = date('Y-m-d H:i:s');
        $create_name = 'SYSTEM';
        $info = '一键倒入柜位信息';
        $sql = "INSERT INTO `warehouse_box` (`warehouse_id`,`box_sn`,`create_time` , `create_name` , `info`) VALUES ({$warehouse_id}, '{$box_sn}' , '{$create_time}' , '{$create_name}' , '{$info}')";
        $res = $this->db()->query($sql);
        if($res){
            return $this->db()->insertId();
        }else{
            echo '创建柜位失败';die;
        }
    }

    public function SetGoodsOn($box_sn,$goods_id){
        $i = 1;
        $logModel = new BoxGoodsLogModel(22);
        $result = array('success' => 1, 'error' => 0);
        $html = '';
        $box_sn = trim($box_sn);

        if($box_sn == '0-00-0-0' || $box_sn == ''){
            $result = array('success' => 0, 'error' => 1,'info' => '不能上架0-00-0-0或者空的柜位！','html' => '');
            return $result;
        }

        /*此处不完善暂时注释
        //盘点中柜位其中的货品不能进行上架操作
        $re2=$this->checkGoodsSd($goods_id);
        if(!empty($re2)){
            $result = array('success' => 0, 'error' => 1,'info' => $goods_id.'正在周盘点单'.$re2['plan_id'].'中盘点中，不能进行上下架操作!','html' => '');
            return $result;
        }

        //非盘点中柜位的货号进行上下架操作时，不能选择盘点中的柜位，
        $re1=$this->checkGuiweiSd($box_sn);
        if(!empty($re1)){
            $result = array('success' => 0, 'error' => 1,'info' => $re1['lock_guiwei'].'在周盘点单'.$re1['plan_id'].'中盘点中，不能进行上下架操作!','html' => '');
            return $result;
        }*/


        //$result = $this->checkWarehousebyBox($goods_id,$box_sn);
        if($result['success'] == 1){//上架前判断。该柜位号是否属于该货品的仓库的柜位
            $box_id = $this->GetBoxid($box_sn);
            $sql = "update `goods_warehouse` set `box_id`=$box_id where `good_id`='$goods_id'";
            $this->db()->query($sql);
            $sql = "update `warehouse_goods` set `box_sn`='$box_sn' where `goods_id`=$goods_id";
            $rs  = $this->db()->query($sql);

            if ($rs){
                $sql = "select `goods_sn`,`warehouse` from `warehouse_goods` where `goods_id`=$goods_id";
                $data = $this->db()->getRow($sql);

                $html .= "<tr><td>$goods_id</td><td>".$data['goods_sn']."</td><td>".$data['warehouse']."</td><td>$box_sn</td></tr>";
                //$html .= "<td>$goods_id</td><td>".$data['goods_sn']."</td><td>".$data['warehouse']."</td><td>$box_sn</td>";
                //$html = $goods_id.",".$data['goods_sn'].",".$data['warehouse'].",".$box_sn;
                //$html .= " </table>";
                //$html .= "<div></div>";
                $html2 = "<tr id='add'></tr>";
                $result = array('success' => 1, 'error' => 0,'info' => '上架成功！','html' => $html,'html2' => $html2);
                $logModel->addLog(array('goods_id'=>$goods_id), $type=2);
            }else{
                $result = array('success' => 0, 'error' => 1,'info' => '上架失败！');
            }
        }
        //var_dump($result);exit;
        return $result;
    }
    /*
     * 批量上架
     */

    public function SetPiliangGoodsOn($goods_id_arr,$box_sn,$warehouse){
        //var_dump($goods_id_arr);exit;
        $result = array('success' => 0 , 'error' => '');
        $num = 0;
        $html = '';
        $time = date('Y-m-d H:i:s');
        $create_user = $_SESSION['userName'];
        $passed_str = $pass_str = $no_pass_str = $no_exist =  '';
        $logModel = new BoxGoodsLogModel(22);
        $flag = true;
        $i = 0;
        $temp_goods_arr = array();
        //exit($box_sn);
        $pan = array();		//用来放盘点过的货号 容器
        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            //分别获取每个货所在仓库的默认柜位
            //$sth = $pdo->prepare("SELECT `a`.`warehouse_id`, `a`.`warehouse`,`a`.`box_sn` as box_sn ,`a`.`is_on_sale`, `b`.`id` AS `box_id` FROM `warehouse_goods` AS `a`, `warehouse_box` AS `b` WHERE `a`.`goods_id` = ? AND `a`.`warehouse_id` = `b`.`warehouse_id`");
            $sth = $pdo->prepare("SELECT `a`.`warehouse_id`, `a`.`warehouse`,`a`.`box_sn` as box_sn ,`a`.`is_on_sale` FROM `warehouse_goods` AS `a` WHERE `a`.`goods_id` = ? ");




            foreach($goods_id_arr AS $val){
                $sth->execute(array($val));
                $row = $sth->fetch();
                // print_r($row);exit;
                if(!isset($row) || empty($row) || $row == false){
                    $no_exist .= "<span style='color:blue;'>{$val} 仓库查不到该货</span><br/>";
                    $flag = false;
                    continue;
                }
                if($row['is_on_sale'] != 2){
                    $no_pass_str .= "<span style='color:red;'>{$val} 不是库存状态的货</span><br/>";
                    $flag = false;
                    continue;
                }
                if ($box_sn == '0-00-0-0'){
                    $no_pass_str .= "<span style='color:red;'>{$val} 不能上架0-00-0-0</span><br/>";
                    $flag = false;
                    continue;
                }
                $box_id = $this->GetBoxid($box_sn);
//                                if ($box_id == false){
//                                    $no_pass_str .= "<span style='color:red;'>{$val} 不能上架0-00-0-0</span><br/>";
//                                    $flag = false;
//				    continue;
//                                }
                if ($row['warehouse_id'] != $warehouse){
                    $no_pass_str .= "<span style='color:red;'>{$val} 不属于所选择仓库</span><br/>";
                    $flag = false;
                    continue;
                }

                if ($row['box_sn'] !='' && $row['box_sn'] != '0-00-0-0'){
                    $no_pass_str .= "<span style='color:red;'>{$val} 已存在柜位，不需上架</span><br/>";
                    $flag = false;
                    continue;
                }
                //盘点中柜位其中的货品不能进行上架操作
                $re2=$this->checkGoodsSd($val);
                if(!empty($re2)){
                    $no_pass_str .= "<span style='color:red;'>{$val}正在周盘点单{$re2['plan_id']}中盘点中，不能进行上下架操作!'</span><br/>";
                    $flag = false;
                    continue;
                }

                //非盘点中柜位的货号进行上下架操作时，不能选择盘点中的柜位，
                $re1=$this->checkGuiweiSd($box_sn);
                if(!empty($re1)){
                    $no_pass_str .= "<span style='color:red;'>{$re1['lock_guiwei']}在周盘点单{$re1['plan_id']}中盘点中，不能进行上下架操作!'</span><br/>";
                    $flag = false;
                    continue;
                }

                $sql = "update `goods_warehouse` set `box_id`=".$box_id.",`create_user`='".$create_user."',`create_time`='".$time."' where `good_id`='$val'";

                $this->db()->query($sql);
                //更新warehouse_goods 表的box_sn 字段
                $sql = "UPDATE `warehouse_goods` SET `box_sn` = '$box_sn' WHERE `goods_id` = '{$val}'";
                $pdo->query($sql);
                //生成返回的html，成功上架
                $html .= $this->CreateJsonHtml(array($val));

                $num ++;
                $temp_goods_arr[]=$val;
                //添加日志
                //$logModel->addLog(array($val), 2,$box_sn);
                $i ++;

            }


        }
        catch(Exception $e){//捕获异常
            //echo $sql;die;
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return array('success' => 0 , 'error'=>'事务执行失败!');
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        //var_dump($temp_goods_arr);exit;
        //写入出入库记录
        if ($this->CheckIfExists($temp_goods_arr) ){
            $logModel->addLog($temp_goods_arr, 2);

        }
        if ($flag)
            $value = 1;
        else
            $value = 0;
        return array('success'=>$value , 'error' => $no_pass_str.$no_exist.$passed_str.$pass_str, 'success_num' =>$num,'info'=>'上架成功','html' => $html);
    }

    public function CreateJsonHtml($goods_arr){
        $html = '';
        foreach ($goods_arr as $v){
            $sql = "select `goods_sn`,`warehouse`,`box_sn` from `warehouse_goods` where `goods_id`=$v";
            //echo$sql;exit;
            $rs = $this->db()->getRow($sql);
            $goods_sn = $rs['goods_sn'];
            $warehouse= $rs['warehouse'];
            $box_sn   = $rs['box_sn'];
            $html .= "<tr><td>$v</td><td>".$goods_sn."</td><td>".$warehouse."</td><td>$box_sn</td></tr>";

        }

        return $html;

    }
    public function CheckIfExists($goods_arr){
        foreach ($goods_arr as $k) {
            $sql = "select count(*) from `warehouse_goods` where `goods_id`='$k'";
            $row = $this->db()->getOne($sql);
            if ($row >= 1)
                return true;
            else
                return false;
        }

    }
    public function getWarehouseAndBox($goods_id){
        $result = array('success' => 1, 'error' => 0);
        $sql = "select `is_on_sale`,`goods_sn`,`warehouse_id`,`warehouse`,`box_sn` from `warehouse_goods` where `goods_id`='$goods_id'";
        $data = $this->db()->getRow($sql);
//            if($data['is_on_sale'] != 2){
//                $result = array('success' => 0,'error'=>1,'info' => '下架失败');
//            }
        return $data;


    }
    public function getSameGoodsSnBoxInfo($goods_id) {
        $result = array('success' => 0,'error' =>'');
        $sql = "select `is_on_sale`,`goods_sn`,`warehouse_id`,`warehouse`,`box_sn` from `warehouse_goods` where `goods_id`='$goods_id'";
        $row = $this->db()->getRow($sql);
        if(empty($row)){
            $result = array('success' => 0,'error' =>1,'info' => '不存在该货品');
            return $result;
        }
        if($row['is_on_sale'] != 2){//如果非库存状态，提示不能上架
            $result = array('success' => 0,'error' =>1,'info' => '非库存状态，不能上架');
            return $result;
        }
        if ($row['box_sn'] != '' && $row['box_sn'] != '0-00-0-0'){//已经在柜位××上，不需上架

            $result = array('success' => 0,'error' =>1,'info' => '已经在柜位 '.$row['box_sn'].'上，不需上架。');

            return $result;
        }

        $goods_sn = $row['goods_sn'];
        if ($goods_sn) {
            $sql2 = "select distinct(`box_sn`),`goods_sn`,`warehouse_id`,`warehouse`,(select count(0) from warehouse_goods g2 where g2.warehouse_id=g.warehouse_id and g2.box_sn=g.box_sn and g2.goods_sn=g.goods_sn and g2.is_on_sale=2) as goods_totals from `warehouse_goods` g where `is_on_sale`=2 and `box_sn`!='0-00-0-0' and `goods_sn`='{$goods_sn}'";
            $data = $this->db()->getAll($sql2);
            //屏蔽显示柜位号和库相同的数据
            //$this->array_unique_fb($data);
            return $data;
        }else{
            return false;
        }

    }
    public function GetBoxSn($warehouse_id,$box_id=''){
        $html = '';
        if (!empty($warehouse_id)){
            $sql = "select `id`,`box_sn` from `warehouse_box` where `warehouse_id`={$warehouse_id}";
            $list = $this->db()->getAll($sql);
            $html .= "<option value=''></option>";
            foreach( $list as $k => $v){
                $html .= "<option value='".$v['id']."'>".$v['box_sn']."</option>";
            }
            return $html;
        }
        if (!empty($box_id)){
            $sql = "select `id`,`box_sn` from `warehouse_box` where `id`={$box_id}";
            $data = $this->db()->getRow($sql);
            return $data['box_sn'];
        }
        return false;
    }

    public function GetBoxid($box_sn) {
        if ($box_sn == '0-00-0-0'){//不能为0－00-0-0
            return false;
        }
        $sql = "select `id` from `warehouse_box` where `box_sn`='{$box_sn}' and `box_sn`!='0-00-0-0'";
        $data = $this->db()->getRow($sql);
        $box_id = $data['id'];
        return $box_id;
    }
    //二维数组去掉重复值
    public function array_unique_fb($array2D){
        foreach ($array2D as $v){
            $v = join(",",$v);  //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
            $temp[] = $v;
        }
        $temp = array_unique($temp);    //去掉重复的字符串,也就是重复的一维数组
        foreach ($temp as $k =>$v){
            $temp[$k] = explode(",",$v);   //再将拆开的数组重新组装
        }
        return $temp;
    }
    //检查所填写的柜位号是否属于该库
    public function checkWarehousebyBox($goods_id,$box_sn){
        $sql = "select `warehouse_id`  from `warehouse_goods` where `goods_id`=$goods_id";
        $data = $this->db()->getRow($sql);
        $warehouse_id = $data['warehouse_id'];
        $sql2 = "select `box_sn` from `warehouse_box` where `warehouse_id`=$warehouse_id";

        $box_sn_arr = $this->db()->getAll($sql2);

        foreach($box_sn_arr as $key => $val){
            if($val['box_sn'] == $box_sn){
                $result = array('success' => 1);
                return $result;
            }
        }

        $result = array('success' => 0, 'error'=>1, 'info' => '该柜位不是该货品所属仓库的柜位。');
        return $result;
//        $data = $this->db()->getRow($sql);
//        $nbox_sn = $data['box_sn'];
//        if ($box_sn != $nbox_sn){
//            $result = array('success' => 0, 'error'=>1, 'info' => '该柜位不是该货品所属的柜位。');
//            return $result;
//        }else{
//            return true;
//        }Array ( [0] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-2-C ) [1] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-20-2-B ) [2] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-20-2-A ) [3] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-30-2-D ) [4] => Array ( [0] => 仅售现货 [1] => 3 [2] => 深圳待拆库 [3] => F-18-3-C ) [5] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-20-3-D ) [6] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-20-3-C ) [7] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-20-5-B ) [8] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-19-3-D ) [9] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-19-2-B ) [10] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-19-4-D ) [16] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-19-5-D ) [20] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-19-4-C ) [27] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-20-4-D ) [28] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-20-4-C ) [29] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-20-3-A ) [31] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-19-4-B ) [32] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-19-2-D ) [33] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-19-2-C ) [35] => Array ( [0] => 仅售现货 [1] => 96 [2] => 总公司后库 [3] => F-30-2-A ) [37] => Array ( [0] => 仅售现货 [1] => 586 [2] => 广州天河分公司广晟大厦体验店后库 [3] => F-30-2-A ) [38] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-32-1-A ) [43] => Array ( [0] => 仅售现货 [1] => 531 [2] => 青岛分公司香港中路体验店柜面 [3] => F-07-3-A ) [56] => Array ( [0] => 仅售现货 [1] => 96 [2] => 总公司后库 [3] => F-31-3-D ) [61] => Array ( [0] => 仅售现货 [1] => 654 [2] => 网络待拆库 [3] => Y-01-2-B ) [62] => Array ( [0] => 仅售现货 [1] => 96 [2] => 总公司后库 [3] => F-30-2-D ) [63] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-29-1-B ) [65] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-19-1-A ) [66] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-19-1-C ) [67] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-19-1-B ) [71] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-19-4-A ) [75] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-20-5-D ) [78] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-20-5-C ) [79] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-30-2-A ) [81] => Array ( [0] => 仅售现货 [1] => 531 [2] => 青岛分公司香港中路体验店柜面 [3] => F-19-2-B ) [84] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-30-5-C ) [86] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-25-5-A ) [90] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-27-4-D ) [91] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-19-6-B ) [93] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-29-6-B ) [94] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-08-5-C ) [97] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-30-2-C ) [131] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-29-6-C ) [133] => Array ( [0] => 仅售现货 [1] => 96 [2] => 总公司后库 [3] => F-31-1-A ) [134] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-1-B ) [136] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-27-1-A ) [137] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-27-1-B ) [138] => Array ( [0] => 仅售现货 [1] => 546 [2] => 线上唯品会货品库 [3] => N-03-4-C ) [139] => Array ( [0] => 仅售现货 [1] => 546 [2] => 线上唯品会货品库 [3] => N-02-2-A ) [141] => Array ( [0] => 仅售现货 [1] => 531 [2] => 青岛分公司香港中路体验店柜面 [3] => F-27-1-B ) [144] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-29-1-A ) [145] => Array ( [0] => 仅售现货 [1] => 599 [2] => 深圳罗湖分公司地王大厦体验店柜面 [3] => F-10-4-A ) [149] => Array ( [0] => 仅售现货 [1] => 517 [2] => 批发借货 [3] => F-26-1-C ) [150] => Array ( [0] => 仅售现货 [1] => 531 [2] => 青岛分公司香港中路体验店柜面 [3] => F-26-1-A ) [151] => Array ( [0] => 仅售现货 [1] => 586 [2] => 广州天河分公司广晟大厦体验店后库 [3] => F-26-4-A ) [152] => Array ( [0] => 仅售现货 [1] => 586 [2] => 广州天河分公司广晟大厦体验店后库 [3] => F-30-2-B ) [160] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-29-6-D ) [164] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-2-D ) [166] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-3-B ) [170] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-1-C ) [176] => Array ( [0] => 仅售现货 [1] => 531 [2] => 青岛分公司香港中路体验店柜面 [3] => F-27-1-D ) [177] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-1-A ) [179] => Array ( [0] => 仅售现货 [1] => 96 [2] => 总公司后库 [3] => F-30-2-B ) [184] => Array ( [0] => 仅售现货 [1] => 3 [2] => 深圳待拆库 [3] => F-18-4-B ) [189] => Array ( [0] => 仅售现货 [1] => 531 [2] => 青岛分公司香港中路体验店柜面 [3] => F-26-1-C ) [190] => Array ( [0] => 仅售现货 [1] => 531 [2] => 青岛分公司香港中路体验店柜面 [3] => F-27-1-C ) [197] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-27-1-C ) [214] => Array ( [0] => 仅售现货 [1] => 2 [2] => 线上低值库 [3] => B-02-4-A ) [237] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-24-2-D ) [245] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-3-C ) [248] => Array ( [0] => 仅售现货 [1] => 243 [2] => 天津滨江道体验店柜面 [3] => F-26-2-C ) [249] => Array ( [0] => 仅售现货 [1] => 308 [2] => 总公司店面配货库 [3] => F-07-5-B ) [250] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-3-D ) [251] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-4-D ) [252] => Array ( [0] => 仅售现货 [1] => 96 [2] => 总公司后库 [3] => F-31-5-A ) [276] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-24-4-C ) [277] => Array ( [0] => 仅售现货 [1] => 308 [2] => 总公司店面配货库 [3] => F-01-1-A ) [280] => Array ( [0] => 仅售现货 [1] => 308 [2] => 总公司店面配货库 [3] => F-09-4-A ) [283] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-28-1-B ) [284] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-28-1-D ) [285] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-27-1-D ) [293] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-22-4-A ) [296] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-1-D ) [297] => Array ( [0] => 仅售现货 [1] => 3 [2] => 深圳待拆库 [3] => F-30-4-D ) [298] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-28-1-C ) [305] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-27-2-B ) [306] => Array ( [0] => 仅售现货 [1] => 3 [2] => 深圳待拆库 [3] => F-30-4-C ) [309] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-5-C ) [312] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-4-A ) [316] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-3-A ) [319] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-27-3-A ) [325] => Array ( [0] => 仅售现货 [1] => 243 [2] => 天津滨江道体验店柜面 [3] => F-26-3-C ) [329] => Array ( [0] => 仅售现货 [1] => 586 [2] => 广州天河分公司广晟大厦体验店后库 [3] => F-26-5-B ) [335] => Array ( [0] => 仅售现货 [1] => 243 [2] => 天津滨江道体验店柜面 [3] => F-26-3-D ) [355] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-23-5-A ) [357] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-24-5-D ) [360] => Array ( [0] => 仅售现货 [1] => 3 [2] => 深圳待拆库 [3] => F-18-1-A ) [395] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-19-3-C ) [401] => Array ( [0] => 仅售现货 [1] => 531 [2] => 青岛分公司香港中路体验店柜面 [3] => F-21-3-C ) [408] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-21-4-C ) [409] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-21-2-C ) [414] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-21-2-A ) [419] => Array ( [0] => 仅售现货 [1] => 585 [2] => 广州天河分公司广晟大厦体验店柜面 [3] => F-30-2-A ) [423] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-21-2-B ) [424] => Array ( [0] => 仅售现货 [1] => 3 [2] => 深圳待拆库 [3] => F-30-2-B ) [429] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-23-5-D ) [430] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-23-5-B ) [433] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-28-1-A ) [436] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-22-4-C ) [437] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-22-2-A ) [445] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-2-A ) [452] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-21-4-A ) [460] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-21-3-C ) [462] => Array ( [0] => 仅售现货 [1] => 3 [2] => 深圳待拆库 [3] => F-18-3-A ) [463] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-19-3-B ) [475] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-20-4-A ) [480] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-20-4-B ) [482] => Array ( [0] => 仅售现货 [1] => 531 [2] => 青岛分公司香港中路体验店柜面 [3] => F-28-1-A ) [499] => Array ( [0] => 仅售现货 [1] => 3 [2] => 深圳待拆库 [3] => F-18-3-B ) [505] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-19-2-A ) [514] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-30-5-C ) [515] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-20-3-C ) [519] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-07-1-C ) [521] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-23-3-C ) [531] => Array ( [0] => 仅售现货 [1] => 586 [2] => 广州天河分公司广晟大厦体验店后库 [3] => F-26-3-C ) [536] => Array ( [0] => 仅售现货 [1] => 586 [2] => 广州天河分公司广晟大厦体验店后库 [3] => F-26-3-D ) [538] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-4-B ) [562] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-20-5-A ) [567] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-22-3-A ) [575] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-24-2-C ) [609] => Array ( [0] => 仅售现货 [1] => 585 [2] => 广州天河分公司广晟大厦体验店柜面 [3] => F-10-2-C ) [616] => Array ( [0] => 仅售现货 [1] => 3 [2] => 深圳待拆库 [3] => F-30-4-A ) [617] => Array ( [0] => 仅售现货 [1] => 386 [2] => 彩宝库 [3] => C-04-2-B ) [659] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-22-5-D ) [660] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-21-2-D ) [661] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-21-5-A ) [682] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-4-C ) [689] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-26-5-B ) [716] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-27-3-C ) [718] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-32-1-A ) [742] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-27-4-B ) [750] => Array ( [0] => 仅售现货 [1] => 656 [2] => 展会活动库 [3] => F-26-2-D ) [753] => Array ( [0] => 仅售现货 [1] => 243 [2] => 天津滨江道体验店柜面 [3] => F-27-3-A ) [763] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-29-3-B ) [765] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-07-3-C ) [767] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-29-3-A ) [774] => Array ( [0] => 仅售现货 [1] => 656 [2] => 展会活动库 [3] => F-30-2-C ) [785] => Array ( [0] => 仅售现货 [1] => 656 [2] => 展会活动库 [3] => F-08-5-C ) [818] => Array ( [0] => 仅售现货 [1] => 169 [2] => 跟单维修库 [3] => F-30-4-D ) [837] => Array ( [0] => 仅售现货 [1] => 656 [2] => 展会活动库 [3] => F-32-1-A ) [838] => Array ( [0] => 仅售现货 [1] => 96 [2] => 总公司后库 [3] => F-32-1-A ) [840] => Array ( [0] => 仅售现货 [1] => 96 [2] => 总公司后库 [3] => F-26-1-C ) [854] => Array ( [0] => 仅售现货 [1] => 656 [2] => 展会活动库 [3] => F-26-1-B ) [889] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-27-3-D ) [940] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-23-1-C ) [942] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-24-1-B ) [946] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-24-1-A ) [949] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-24-1-C ) [957] => Array ( [0] => 仅售现货 [1] => 443 [2] => 婚博会备货库 [3] => F-31-5-C ) )

    }

    public function checkGuiweiSd($box_sn){
        $sql="select * from warehouse_pandian_action where lock_guiwei='{$box_sn}' AND is_delete=0";
        return $this->db()->getRow($sql);
    }

    public function checkGoodsSd($goods_id){
        $sql="select wpr.plan_id from warehouse_pandian_report as wpr,warehouse_pandian_action as wpa where wpr.guiwei = wpa.lock_guiwei and wpr.goods_id='{$goods_id}' and wpa.is_delete = 0";
        return $this->db()->getRow($sql);
    }



    /** 检测货号是否在盘点中柜位里，和不能选择的柜位是在盘点中 **/
    public function checkGoodsBox($strArr,$box_id){
        $result = array('type'=>1, 'error'=>array());
        foreach ($strArr as $k => $v) {
            $k +=1;
            $re1=$this->checkGoodsSd($v);
            if($re1){
                $result['error'] = "第{$k}个货号:`{$v}` 正在周盘点单{$re1['plan_id']}中盘点中，不能进行上下架操作.\r\n";	//记录不存在的记录
                $result['type'] = 0;
                break;
            }

        }
        $sql="select box_sn from warehouse_box where id= {$box_id}";
        $box_sn=$this->db()->getOne($sql);
        $re2=$this->checkGuiweiSd($box_sn);
        if($re2){
            $result['error'] = "{$box_sn}在周盘点单{$re2['plan_id']}中盘点中，不能进行上下架操作.\r\n";	//记录不存在的记录
            $result['type'] = 0;
        }
        return $result;
    }

} /** END MODEL **/
?>
