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
         * 查询仓储货品信息
         * @return json
         */
        public function getWarehouseGoodsInfo() {

            $filter = $this->filter;
            $sql = "SELECT * FROM `warehouse_goods` WHERE 1"; //暂时用＊号          

            if (!empty($filter['goods_id'])) {
                $sql .= " and `goods_id` = '{$filter['goods_id']}'";
            }else{
                $this->error = 1;
                $this->error_msg = "参数错误，goods_id不能为空";
                $this->return_msg = array();
                $this->display();
            }
            if (!empty($filter['is_on_sale'])) {
                $sql .= " and `is_on_sale` = {$filter['is_on_sale']}";
            }
            if (!empty($filter['company_id'])) {
                $sql .= " and `company_id` = {$filter['company_id']}";
            }
            $data = $this->db->getRow($sql);            
            if (!$data) {
                $this->error = 1;
                $this->return_sql = $sql;
                $this->error_msg = "未查询到此商品";
                $this->return_msg = array();
                $this->display();
            }
            if(!empty($filter['extends'])){
                 $style_sn = $data['style_sn'];
                 $style_id = $data['style_id'];
                 if(in_array("goods_image",$filter['extends'])){
                     $image_sql = "select middle_img from front.app_style_gallery where style_sn='{$style_sn}'";
                     $goods_image = $this->db->getOne($image_sql);
                     $data['goods_image'] = $goods_image;
                 }
                 //商品价格计算
                 if(in_array("goods_price",$filter['extends'])){
                     
                     if(empty($filter['channel_id'])){
                         $this->error = 1;
                         $this->error_msg = "参数错误，channel_id不能为空";
                         $this->return_msg = array();
                         $this->display();
                     } 
                     $goodsAttrModel = new GoodsAttributeModel(17);
                     $caizhiarr = $goodsAttrModel->getCaizhiList();
                     $yansearr  = $goodsAttrModel->getJinseList();
                     
                     $model = Util::get_model('sales\AppSalepolicyGoodsModel', [15]);
                     $s_where = array(
                         'goods_id'=>$filter['goods_id'],
                         'channel'=>$filter['channel_id']                         
                     );
                     $sdata = $model->pageXianhuoList($s_where,1,1,$caizhiarr,$yansearr,true);
                     
                     if(isset($sdata['error']) && $sdata['error']== 1 )
                     {
                         $this->error = 1;
                         $this->error_msg = $sdata['content'];
                         $this->return_msg = array();
                         $this->display();
                     }
                     if(empty($sdata['data'][0]['sprice']))
                     {
                         $this->error = 1;
                         $this->error_msg = "您输入的货品没有找到销售政策";
                         $this->return_msg = array();
                         $this->display();
                     }
                     $goods_price_list = array();
                     foreach ($sdata['data'][0]['sprice'] as $vo){
                         $goods_price_list[$vo['sale_price']] = $vo;
                     }
                     ksort($goods_price_list);
                     $goods_price_info = current($goods_price_list);
                     $data['goods_price'] = $goods_price_info['sale_price'];
                     $data['goods_image'] = $sdata['data'][0]['thumb_img'];                     
                     $data['extend_goods_price'] = $sdata['data'][0];
                 }
            } 
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();

        }
        /**
         * 查询仓储货品信息
         * @return json
         */
        public function GetWarehouseGoodsByGoodsid() {
                $s_time = microtime();
                $where = '';
                $sql = "SELECT * FROM `warehouse_goods` WHERE 1"; //暂时用＊号
                $filter_param_is_null = 1;

                if (!empty($this->filter['goods_id'])) {
                        $sql .= " and `goods_id` = '{$this->filter['goods_id']}'";
                        $filter_param_is_null = 0;
                }
                if (isset($this->filter['warehouse_id']) && !empty($this->filter['warehouse_id'])) {
                        $sql .= " and `warehouse_id` in ({$this->filter['warehouse_id']})";
                        $filter_param_is_null = 0;
                }
                if (isset($this->filter['is_on_sale']) && !empty($this->filter['is_on_sale'])) {
                        $sql .= " and `is_on_sale` = {$this->filter['is_on_sale']}";
                        $filter_param_is_null = 0;
                }
                if (isset($this->filter['company_id']) && !empty($this->filter['company_id'])) {
                        $sql .= " and `company_id` = {$this->filter['company_id']}";
                        $filter_param_is_null = 0;
                }
                if (isset($this->filter['goods_name']) && !empty($this->filter['goods_name'])) {
                        $sql .= " and `goods_name` = '{$this->filter['goods_name']}'";
                        $filter_param_is_null = 0;
                }
                if (isset($this->filter['zhengshuhao']) && !empty($this->filter['zhengshuhao'])) {
                        $sql .= " and `zhengshuhao` = '{$this->filter['zhengshuhao']}'";
                        $filter_param_is_null = 0;
                }
                if (isset($this->filter['tuo_type']) && !empty($this->filter['tuo_type'])) {
                        $sql .= " and `tuo_type` = {$this->filter['tuo_type']}";
                        $filter_param_is_null = 0;
                }
                //必须传入查询参数避免全表查询托S服务器
                if($filter_param_is_null!=0){
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "未传入任何参数";
                        $this->return_msg = array();
                        $this->display();                    
                }

                $data['data'] = $this->db->getRow($sql);

                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (!$data['data']) {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = "未查询到此商品";
                        $this->return_msg = array();
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $data;
                        $this->display();
                }
        }

        /**
         * 查询绑定订单的仓储货品信息
         * @return json
         */
        public function GetWarehouseGoodsByOrderGoodsid() {
                $s_time = microtime();
                $where = '';
                $sql = "SELECT * FROM `warehouse_goods` WHERE 1"; //暂时用＊号

                if (!empty($this->filter['order_goods_id'])) {
                        $sql .= " and `order_goods_id` = '{$this->filter['order_goods_id']}'";
                } else {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = "货号不能为空!!";
                        $this->return_msg = array();
                        $this->display();
                }
                $data['data'] = $this->db->getRow($sql);

                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (!$data['data']) {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = "未查询到此商品";
                        $this->return_msg = array();
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $data;
                        $this->display();
                }
        }

    public function SearchGoods(){
        if(empty($this->filter['where'])){
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "参数错误";
            $this->return_msg = array();
            $this->display();
        }else{
        $sql = "SELECT * FROM `warehouse_goods`";
        $str = "";
        $where=$this->filter['where'];
        if(!empty($where['goods_id'])){
            $str .= " goods_id = '".$where['goods_id']."' AND ";
        }
        if(!empty($where['style_sn']))
        {
            $str .= " goods_sn = '".$where['style_sn']."' AND ";
        }
        if(!empty($where['is_on_sale']))
        {
            $str .= "is_on_sale = ".$where['is_on_sale']." AND ";
        }
        if(!empty($where['zhuchengse']))
        {
            $str .= "caizhi = '".$where['zhuchengse']."' AND ";
        }
        if(!empty($where['w_id']))
        {
            $str .= "warehouse_id in( ".$where['w_id'].") AND ";
        }
        //new add
        if(!empty($where['zhengshuhao']))
        {
            $str .= "zhengshuhao = '".$where['zhengshuhao']."' AND ";
        }

        if(isset($where['finger']) && $where['finger'] != '')
        {
            $str .= " shoucun = '".$where['finger']."' AND ";
        }
        if (isset($where['stone']) && $where['stone'] != '')
        {
            $str .= " zhushi='".$where['stone']."' AND ";
        }
        //差一个主成色重
        if(isset($where['jinzhong_begin']) && $where['jinzhong_begin'] != "") {
            $str .= " jinzhong >=".$where['jinzhong_begin']." AND ";
        }
        if(isset($where['jinzhong_end']) && $where['jinzhong_end'] != "") {
            $str .= " jinzhong <=".$where['jinzhong_end']." AND ";
        }
        if(isset($where['zhushi_begin']) && $where['zhushi_begin'] != "") {
            $str .= " zuanshidaxiao >=".$where['zhushi_begin']." AND ";
        }
        if(isset($where['zhushi_end']) && $where['zhushi_end'] != "") {
            $str .= " zuanshidaxiao <=".$where['zhushi_end']." AND ";
        }
        if(isset($where['stone_color']) && $where['stone_color'] != "")
        {
            $str .= " zhushiyanse = '".$where['stone_color']."' AND ";
        }
        if (isset($where['zs_clarity']) && $where['zs_clarity'] != "")
        {
            $str .= " zhushijingdu = '".$where['zs_clarity']."' AND ";
        }
        if (isset($where['caizhi']) && $where['caizhi'] != "")
        {
            $str .= " caizhi like '".$where['caizhi']."%' AND ";
        }
        if (isset($where['zhushi_begin']) && $where['zhushi_begin'] !="") {
            $str .= "zuanshidaxiao >= '".$where['zhushi_begin']."' AND ";
        }
        if (isset($where['stone_clear']) && $where['stone_clear'] !="") {
            $str .= "zhushijingdu = '".$where['stone_clear']."' AND ";
        }
        $str .= "is_on_sale = '2'  AND (order_goods_id='' or order_goods_id='0')";
        if($str)
        {
            $str = rtrim($str,"AND ");
            $sql .=" WHERE ".$str;
        }
        $sql .= " ORDER BY id  ASC ";
            if($where['page']==''){
                $where['page']=1;
            }
       // $data = $this->db->getAll($sql);
        $data = $this->db->getPageList($sql, array(), $where['page'], 10);
        }
        if(empty($data)){
            $this->error = 0;
            $this->return_sql = $sql;
            $this->error_msg = "无数据返回";
            $this->return_msg = array();
            $this->display();
        }else{
            $this->error = 0;
            $this->return_sql = $sql;
            $this->error_msg = "成功";
            $this->return_msg = $data;
            $this->display();
        }
    }

        /**
         * 查询货品信息
         * @param goods_id | goods_sn | is_on_sale  三个参数 可以单个传，也可多个组合传
         * @return json
         */
        public function GetGoodsInfoByGoods() {
                $s_time = microtime();
                $where = '';
                $str = '';
                if (isset($this->filter['goods_id']) && !empty($this->filter['goods_id'])) {
                        $str .= " AND `goods_id` in ('" . $this->filter['goods_id'] . "') ";
                }

                if (isset($this->filter['goods_sn']) && !empty($this->filter['goods_sn'])) {
                        $str .= " AND `goods_sn` = '{$this->filter['goods_sn']}' ";
                }

                if (isset($this->filter['buchan_sn']) && !empty($this->filter['buchan_sn'])) {
                        $str .= " AND `buchan_sn` = '{$this->filter['buchan_sn']}' ";
                }

                if (isset($this->filter['is_on_sale']) && !empty($this->filter['is_on_sale'])) {
                        $str .= " AND `is_on_sale` = {$this->filter['is_on_sale']} ";
                }

                if ($str == '') {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = "参数不全或不合法！";
                        $this->return_msg = array();
                        $this->display();
                } else {
                        $str = ltrim($str, " AND"); //这个空格很重要
                }

                $sql = "SELECT * FROM `warehouse_goods` WHERE " . $str; //暂时用＊号
                $data['data'] = $this->db->getAll($sql);

                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (!$data['data']) {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = "未查询到此商品";
                        $this->return_msg = array();
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $data['data'];
                        $this->display();
                }
        }

        /**
         * 通过货号绑定\解绑货品
         * @return json
         */
        public function BindGoodsInfoByGoodsId() {
                $s_time = microtime();
                $set = '';
                $where = '';
                if ($this->filter['bind_type'] == 1) {//绑定
                        if ($this->filter['order_goods_id']) {
                                $set .= " `order_goods_id` = '" . $this->filter['order_goods_id'] . "' ";
                        } else {
                                $this->error = 1;
                                $this->return_sql = $sql;
                                $this->error_msg = "绑定编号不能为空!!";
                                $this->return_msg = array();
                                $this->display();
                        }

                        if (!empty($this->filter['goods_id'])) {
                                $where .= " `goods_id` = '" . $this->filter['goods_id'] . "' ";
                        } else {
                                $this->error = 1;
                                $this->return_sql = "";
                                $this->error_msg = "货号不能为空!!";
                                $this->return_msg = array();
                                $this->display();
                        }
                } elseif ($this->filter['bind_type'] == 2) {//解绑
                        $set .= " `order_goods_id` = '' ";
                        if ($this->filter['order_goods_id']) {
                                $where .= " `order_goods_id` = '" . $this->filter['order_goods_id'] . "' ";
                        } else {
                                $this->error = 1;
                                $this->return_sql = "";
                                $this->error_msg = "绑定编号不能为空!!";
                                $this->return_msg = array();
                                $this->display();
                        }
                } else {
                        $this->error = 1;
                        $this->return_sql = "";
                        $this->error_msg = "类型不能为空!!";
                        $this->return_msg = array();
                        $this->display();
                }


                if ($this->filter['bind_type'] == 1) {//绑定
                        $sql = "update `warehouse_goods` set " . $set . "  WHERE " . $where . " and is_on_sale = 2 ";
                } else {
                        $sql = "update `warehouse_goods` set " . $set . "  WHERE " . $where;
                }

                $data = $this->db->query($sql);

                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (!$data) {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = "未查询到此商品";
                        $this->return_msg = array();
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $data;
                        $this->display();
                }
        }

        /**
         * 查询仓储商品列表
         * @return json
         */
        public function GetWarehouseGoodsList() {
                $s_time = microtime();
                $where = '';
                $where['page'] = intval($this->filter["page"]) <= 0 ? 1 : intval($this->filter["page"]);
                $where['pageSize'] = intval($this->filter["pageSize"]) > 0 ? intval($this->filter["pageSize"]) : 10;

                $sql = "SELECT * FROM `warehouse_goods` WHERE 1"; //暂用＊号

                if (!empty($this->filter['is_on_sale'])) {
                        $sql .= " and `is_on_sale` = {$this->filter['is_on_sale']} ";
                } else {
                        $sql .= " and `is_on_sale` = 2 ";
                }
                $sql .= " ORDER BY `id` DESC LIMIT " . ($where['page'] - 1) * $where['pageSize'] . "," . $where['pageSize'];
                $data['page'] = $where['page'];
                $data['pageSize'] = $where['pageSize'];
                $data['recordCount'] = $this->db->getOne($sql);
                $data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
                $data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
                $data['isFirst'] = $data['page'] > 1 ? false : true;
                $data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
                $data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
                $data['data'] = $this->db->getAll($sql);

                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (!$data['data']) {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->error_msg = "未查询到此商品";
                        $this->return_msg = array();
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $data;
                        $this->display();
                }
        }

        /**
         * 	GetWarehouseList
         *  1,获取有效仓库   BY linian
         *  2,传公司id则根据入库公司获取相应仓库
         */
        public function GetWarehouseList() {
                $s_time = microtime();
                $where = ' 1 ';
                // $where = ' WHERE 1';
                //若 传公司id 获取该公司所有所属的仓库id
                if (isset($this->filter['company_id']) && !empty($this->filter['company_id'])) {
                        $company_id = $this->filter['company_id'];

                        $sql = "SELECT `warehouse_id`  FROM `warehouse_rel`  where `company_id` IN (".$company_id.")";
                        $row = $this->db->getAll($sql);
                        //若 公司ID有效  拼接查出的W_id到where条件
                        if ($row) {
                                $warehouse_id="";
                                foreach ($row as $val) {
                                        $warehouse_id .= $val['warehouse_id'] . ",";
                                }
                                $warehouse_id = rtrim($warehouse_id, ',');
                                $where.=" AND id in ($warehouse_id)";
                        } else {
                                $this->error = 1;
                                $this->return_sql = $sql;
                                $this->error_msg = "公司id不存在！";
                                $this->return_msg = array();
                                $this->display();
                        }
                } else if (isset($this->filter['zy']) && $this->filter['zy'] == '1') {
                	$where .= " AND id in (SELECT `warehouse_id`  FROM `warehouse_rel` where `company_id` IN (SELECT id from cuteframe.company where is_deleted = 0 and company_type = 2 and id not in (58, 445, 515, 526)))";	
                }
                
                $sql = "SELECT `id`,`name`,`code` FROM `warehouse` WHERE " . $where . " AND `is_delete`=1 "; //暂时用＊号
                if (isset($this->filter['id'])) {
                        $sql .= " and `id` = {$this->filter['id']}";
                }
                if (isset($this->filter['diamond_warehouse'])) {
                        $sql .= " and `diamond_warehouse` = {$this->filter['diamond_warehouse']}";
                }
                $data['data'] = $this->db->getAll($sql);


                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
                //返回信息
                if (!$data['data']) {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = "查询仓库信息失败";
                        $this->return_msg = array();
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $data['data'];
                        $this->display();
                }
        }


        /**
         * 	getCompanyList
         *  获取有效公司   BY liuri
         */
        public function getCompanyList() {
            $s_time = microtime();
            $sql = "SELECT `whr`.`company_id`,`whr`.`company_name` FROM `warehouse` as `wh`,`warehouse_rel` as `whr` WHERE `wh`.`id`=`whr`.`warehouse_id` AND `wh`.`diamond_warehouse`=1 AND `wh`.`is_delete`=1 GROUP BY `whr`.`company_id`";
            $arr = $this->db->getAll($sql);
            // 记录日志
            $reponse_time = microtime() - $s_time;
            $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
            //返回信息
            if (!$arr) {
                $this->error = 1;
                $this->return_sql = $sql;
                $this->error_msg = "查询公司信息失败";
                $this->return_msg = array();
                $this->display();
            } else {
                $this->error = 0;
                $this->return_sql = $sql;
                $this->return_msg = $arr;
                $this->display();
            }
        }


        /**
         * 	getCompanyName
         *  获取有效公司   BY liuri
         */
        public function getCompanyName() {
            $s_time = microtime();
            $where = '';
            if($this->filter['code']!=''){
                $where .= " and `wh`.`code`='{$this->filter['code']}'";
            }else{
                $this->error = 1;
                $this->return_sql = '';
                $this->error_msg = "参数不全";
                $this->return_msg = array();
                $this->display();
            }
            $sql = "SELECT `whr`.`company_name` FROM `warehouse` as `wh`,`warehouse_rel` as `whr` WHERE `wh`.`id`=`whr`.`warehouse_id` AND `wh`.`is_delete`=1 AND `wh`.`diamond_warehouse`=1 $where";
            $arr = $this->db->getOne($sql);
            // 记录日志
            $reponse_time = microtime() - $s_time;
            $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
            //返回信息
            if (!$arr) {
                $this->error = 1;
                $this->return_sql = $sql;
                $this->error_msg = "查询公司信息失败";
                $this->return_msg = array();
                $this->display();
            } else {
                $this->error = 0;
                $this->return_sql = $sql;
                $this->return_msg = $arr;
                $this->display();
            }
        }


        /**
         * 通过订单明细ID获取商品号
         */
        public function GetGoodsInfobyDetailId() {
                $s_time = microtime();

                if (isset($this->filter['detail_arr']) && !empty($this->filter["detail_arr"])) {   //销售订单需是保存状态
                        $detail_arr = $this->filter['detail_arr'];
                } else {
                        $this->error = 0;
                        $this->return_sql = "";
                        $this->error_msg = "未查到订单明细";
                        $this->return_msg = false;
                        $this->display();
                }
                $goods = array();
                foreach ($detail_arr as $v) {
                        $sql = "SELECT `goods_id`,`goods_sn`,`goods_name`,`num`,`is_on_sale` FROM `warehouse_goods` WHERE `order_goods_id` = '" . $v . "' and is_on_sale='10'";
                        $goods[] = $this->db->getRow($sql);
                }

                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (empty($goods)) {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->error_msg = "未查询到此订单商品";
                        $this->return_msg = array();
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $goods;
                        $this->display();
                }
        }

        /**
         * 获取仓储调拨单单据信息
         * @param bill_no 仓储单号
         * @param bill_type 仓储单类型
         * @author hlc
         */
        public function CheckBillByBillSn() {
                $s_time = microtime();
                $where = ' WHERE ';
                $str = '';
                if (isset($this->filter['bill_no']) && !empty($this->filter['bill_no'])) {
                        $bill_no = $this->filter['bill_no'];
                        $str .= " `a`.`bill_no` = '{$bill_no}' AND ";
                }
                if (isset($this->filter['bill_type']) && !empty($this->filter['bill_type'])) {
                        $str .= " `a`.`bill_type` = '{$this->filter['bill_type']}' AND ";
                }
                if (empty($this->filter) || $str == '') {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "参数不全或不合法！";
                        $this->return_msg = '';
                        $this->display();
                }
                if ($str != '') {
                        $where .= rtrim($str, ' AND ');
                }
				if($this->filter['bill_type']=="M"){
					$sql = "SELECT `a`.*, `b`.`ship_number` FROM `warehouse_bill` AS `a` INNER JOIN `warehouse_bill_info_m` AS `b` ON `a`.`id` = `b`.`bill_id` " . $where." ORDER BY a.`id` DESC";
				}else{
					$sql = "SELECT `a`.*, `b`.`ship_number` FROM `warehouse_bill` AS `a` INNER JOIN `warehouse_bill_info_wf` AS `b` ON `a`.`id` = `b`.`bill_id` " . $where." ORDER BY a.`id` DESC";
				}
                $row = $this->db->getRow($sql);
                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (!$row) {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->error_msg = "未查询到此订单";
                        $this->return_msg = array();
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $row;
                        $this->display();
                }
        }

        /**
         * 根据仓储单号，获取该单号下的明细
         * @param bill_no 仓储单号
         * @param bill_type 仓储单类型
         * @author hlc
         */
        public function getDetailByBillSn() {
                $s_time = microtime();
                $where = ' WHERE ';
                $str = '';
                if (isset($this->filter['bill_no']) && !empty($this->filter['bill_no'])) {
                        $bill_no = $this->filter['bill_no'];
                        $str .= " `bill_no` = '{$bill_no}' AND ";
                }
                if (isset($this->filter['bill_type']) && !empty($this->filter['bill_type'])) {
                        $str .= " `bill_type` = '{$this->filter['bill_type']}' AND ";
                }
                if (empty($this->filter) || $str == '') {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "参数不全或不合法！";
                        $this->return_msg = '';
                        $this->display();
                }
                if ($str != '') {
                        $where .= rtrim($str, ' AND ');
                }

                $sql = "SELECT `bill_id`, `bill_no`, `bill_type`, `goods_id`, `goods_sn`, `goods_name`, `num`,`sale_price`,  `warehouse_id`, `guiwei` FROM `warehouse_bill_goods` " . $where;

                $row = $this->db->getAll($sql);
                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (!$row) {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->error_msg = "未查询到此订单";
                        $this->return_msg = array();
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $row;
                        $this->display();
                }
        }

        /**
         * 调拨单 绑定/解除 展厅发货，回写快递单号
         * @param bill_id 调拨单ID
         * @param ship_number 包裹单的快递单号
         * @param type 执行类别 add 绑定回写 / del 解除回写
         * @author hlc
         */
        public function SetShipNumber() {
                $s_time = microtime();
                if (!isset($this->filter['type']) && empty($this->filter['type'])) {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "参数不全或不合法！";
                        $this->return_msg = '';
                        $this->display();
                }

                if ($this->filter['type'] == 'add') {     //绑定包裹单
                        if (isset($this->filter['ship_number'])) {
                                $ship_number = '\'' . $this->filter['ship_number'] . '\'';
                        }
                        $where = '';
                        if (isset($this->filter['bill_id']) && !empty($this->filter['bill_id'])) {
                                $where = " WHERE `bill_id` = {$this->filter['bill_id']}";
                        } else {
                                $this->error = 1;
                                $this->return_sql = '';
                                $this->error_msg = "参数不全或不合法！";
                                $this->return_msg = '';
                                $this->display();
                        }

                        $sql = "UPDATE `warehouse_bill_info_m` SET  `ship_number` = {$ship_number} " . $where;
                } else if ($this->filter['type'] == 'del') {   //解除包裹单绑定
                        if (isset($this->filter['bill_no']) && !empty($this->filter['bill_no'])) {
                                $bill_no = $this->filter['bill_no'];
                        } else {
                                $this->error = 1;
                                $this->return_sql = '';
                                $this->error_msg = "参数不全或不合法！";
                                $this->return_msg = '';
                                $this->display();
                        }
                        // 根据调拨单号获取调拨单ID,再确定具体清空哪一个
                        $sql = "SELECT `b`.`id` FROM `warehouse_bill` AS `a` INNER JOIN `warehouse_bill_info_m` AS `b` ON `a`.`id` = `b`.`bill_id` WHERE `a`.`bill_no` = '{$bill_no}'";
                        $id = $this->db->getOne($sql);
                        $sql = "UPDATE `warehouse_bill_info_m` SET `ship_number` = '' WHERE `id` = {$id}";
                }
                $rs = $this->db->query($sql);

                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (!$rs) {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = "回写订单号接口失败";
                        $this->return_msg = array();
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $rs;
                        $this->display();
                }
        }

        /**
         * 获取仓库所属的公司
         * @author hlc
         */
        public function getCompanyByWarehouse() {
                $s_time = microtime();
                $where = ' WHERE ';
                if (isset($this->filter['warehouse_id']) && !empty($this->filter['warehouse_id'])) {
                        $warehouse_id = $this->filter['warehouse_id'];
                        $str .= " `warehouse_id` = {$warehouse_id} AND ";
                }
                if (empty($this->filter) || $str == '') {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "参数不全或不合法！";
                        $this->return_msg = '';
                        $this->display();
                }
                if ($str != '') {
                        $where .= rtrim($str, ' AND ');
                }
                $sql = "SELECT `company_name` , `id` , `company_id` , `warehouse_id` FROM `warehouse_rel` " . $where;
                ;

                $row = $this->db->getOne($sql);
                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (!$row) {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->error_msg = "未查询到此订单";
                        $this->return_msg = array();
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $row;
                        $this->display();
                }
        }

        /**
         * 生成销售退货单  JUAN
         * order_sn：退货订单号
         * return_id 退货单号
         * order_goods二维数组 detail_id：明细ID，return_price：退款金额。
         */
        public function createReturnGoodsBill() {

                $s_time = microtime();
                define("root_path", dirname(dirname(dirname(dirname(__FILE__)))));
                require_once(root_path . '/frame/init.php');
                $pdo = DB::cn(22)->db(); //pdo对象
                //退货订单号
                if (isset($this->filter['order_sn']) && !empty($this->filter['order_sn'])) {
                        $order_sn = $this->filter['order_sn'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数order_sn或者为空";
                        $this->return_msg = '';
                        $this->display();
                }
                //退货流水ID
                if (isset($this->filter['return_id']) && !empty($this->filter['return_id'])) {
                        $return_id = $this->filter['return_id'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数return_id或者为空";
                        $this->return_msg = '';
                        $this->display();
                }

                //制单人
		if (isset($this->filter['create_user']) && !empty($this->filter['create_user'])) {
			$create_user = $this->filter['create_user'];
		} else {
			$create_user = 'SYSTEM';
		}

                //退货仓库ID
                if (isset($this->filter['warehouse_id']) && !empty($this->filter['warehouse_id'])) {
                        $warehouse_id = $this->filter['warehouse_id'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数warehouse_id或者为空";
                        $this->return_msg = '';
                        $this->display();
                }

                $sql = "SELECT `a`.`company_name` , `a`.`company_id` , `b`.`name` FROM `warehouse_rel` AS `a` LEFT JOIN `warehouse` AS `b` ON `a`.`warehouse_id` = `b`.`id` WHERE `b`.`id` = {$warehouse_id}";
                $warehouse_info = $this->db->getRow($sql);

                //退货商品
                if (isset($this->filter['order_goods']) && count($this->filter['order_goods'])) {
                        $order_goods = $this->filter['order_goods'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数order_goods或者为空";
                        $this->return_msg = '';
                        $this->display();
                }
                //查看此订单是否有存在的有效的销售单
                $sql = "SELECT `id` FROM warehouse_bill where `order_sn` = '" . $order_sn . "' AND `bill_status` = 2 AND `bill_type` = 'S'";
                $bill_id = $this->db->getOne($sql);
                if (!$bill_id) {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = "此订单号没有有效的销售单，请检查并联系相关人员";
                        $this->return_msg = '';
                        $this->display();
                }
               
                $chengbenjia = 0; //总成本价
                $mingyijia = 0; //总名义成本价
                $tuihuojia = 0; //总退货价
                foreach ($order_goods as $key => $val) {
                        $sql = "select g.goods_id from warehouse_goods as g,warehouse_bill_goods as bg where bg.goods_id = g.goods_id and order_goods_id = " . $val['detail_id'] . " and bg.bill_id = " . $bill_id;                	
                        $goods_id = $this->db->getOne($sql);
                        $sql1 = "select g.goods_id from warehouse_goods as g,warehouse_bill_goods as bg where bg.goods_id = g.goods_id and g.goods_id = " . $val['goods_id'] . " and bg.bill_id = " . $bill_id;
                        $goods_id1 = $this->db->getOne($sql1);
                        if (!$goods_id && !$goods_id1) {//没有找到关联的货号
                                $this->error = 1;
                                $this->return_sql = $sql;
                                $this->error_msg = "不是销售单中的货品";
                                $this->return_msg = '';
                                $this->display();
                        } else {
                                $sql = "select goods_id,goods_sn,goods_name,caizhi,jinzhong,zhushiyanse,zuanshidaxiao,put_in_type,is_on_sale,yuanshichengbenjia,chengbenjia,mingyichengben from warehouse_goods where goods_id = " . $goods_id;
                                $arr = $this->db->getRow($sql); //取出货品的其他信息
                                if ($arr['is_on_sale'] != '3') {//货品不是已销售状态不能做退货
                                        $this->error = 1;
                                        $this->return_sql = $sql;
                                        $this->error_msg = "货品不是已销售状态，不能退货。";
                                        $this->return_msg = '';
                                        $this->display();
                                }
                                $order_goods[$key] = array_merge($val, $arr);
                                $chengbenjia += $arr['yuanshichengbenjia'];
                                $mingyijia += $arr['mingyichengben'];
                                $tuihuojia += $val['return_price'];
                        }
                }
                //写事务，生成销售退货单
                try {
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                        $pdo->beginTransaction(); //开启事务

                        $time = date('Y-m-d H:i:s');
                        //生成单号
                        $sql = 'SELECT `bill_no` FROM `warehouse_bill` WHERE `id` = (SELECT max(id) from warehouse_bill)';
                        $str = $this->db->getOne($sql);
                        $no = (substr($str, 1, 8) != date('Ymd', time())) ? 1 : intval(substr($str, 9)) + 1;
                       // $bill_no = 'D' . date('Ymd', time()) . str_pad($no, 5, "0", STR_PAD_LEFT);

                    /* $to_warehouse_id = 96;
                    $to_warehouse_name = '总公司后库';
                    $to_company_id = 58;
                    $to_company_name = '总公司';*/
                    $to_warehouse_id = $warehouse_id;
                    $to_warehouse_name = $warehouse_info['name'];
                    $to_company_id = $warehouse_info['company_id'];
                    $to_company_name = $warehouse_info['company_name'];

                        $sql = "INSERT INTO `warehouse_bill`(`bill_no`, `bill_type`, `bill_status`, `order_sn`, `goods_num`, `to_warehouse_id`, `to_warehouse_name`, `to_company_id`, `to_company_name`, `from_company_id`, `from_company_name`, `bill_note`, `yuanshichengben`, `goods_total`, `shijia`, `check_user`, `check_time`, `create_user`, `create_time`) VALUES ('" . ''. "','D',1,'" . $order_sn . "'," . count($order_goods) . "," . $to_warehouse_id . ",'" . $to_warehouse_name . "'," . $to_company_id . ",'" . $to_company_name . "',0,null,'退款流水：" . $return_id . "'," . $chengbenjia . "," . $mingyijia . "," . $tuihuojia . ",null,'0000-00-00 00:00:00','". $create_user ."','" . $time . "')";
                        $pdo->query($sql);
                        $_id = $pdo->lastInsertId();

						$bill_id = substr($_id,-4);
						$bill_no = 'D'.date('Ymd',time()).rand(100,999).str_pad($bill_id,4,"0",STR_PAD_LEFT);

						$sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$_id}";
						$pdo->query($sql);

                        $sql = "INSERT INTO `warehouse_bill_info_d`(`bill_id`, `return_sn`) VALUES (" . $_id . "," . $return_id . ")";
                        $pdo->query($sql);

                        $sql = "INSERT INTO `warehouse_bill_status`(`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES (" . $_id . ",'" . $bill_no . "',1,'" . $time . "','SYSTEM','" . Util::getClicentIp() . "')";
                        $pdo->query($sql);

                        foreach ($order_goods as $key => $val) {

                                $sql = "INSERT INTO `warehouse_bill_goods`(`bill_id`, `bill_no`, `bill_type`, `goods_id`, `goods_sn`, `goods_name`, `num`, `warehouse_id`, `caizhi`, `jinzhong`, `yanse`, `zuanshidaxiao`, `yuanshichengben`, `sale_price`, `shijia`, `in_warehouse_type`, `account`, `addtime`, `pandian_status`, `guiwei`) VALUES (" . $_id . ",'" . $bill_no . "','D'," . $val['goods_id'] . ",'" . $val['goods_sn'] . "','" . $val['goods_name'] . "',1," . $to_warehouse_id . ",'" . $val['caizhi'] . "'," . $val['jinzhong'] . ",'" . $val['zhushiyanse'] . "'," . $val['zuanshidaxiao'] . "," . $val['yuanshichengbenjia'] . "," . $val['mingyichengben'] . "," . $val['return_price'] . "," . $val['put_in_type'] . ",0,'" . $time . "',0,null)";

                                $pdo->query($sql);

                                //改变货品状态为退货中
                                $sql = "UPDATE `warehouse_goods` SET `is_on_sale`= 11  WHERE goods_id = " . $val['goods_id'];
                                $pdo->query($sql);

								$goods_id = $val['goods_id'];
                        }
                } catch (Exception $e) {//捕获异常
                        //print_r($e);exit;
                        util::L(var_export($e,true), 'chaochao.txt');
                        $pdo->rollback(); //事务回滚
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = "创建销售退货单失败";
                        $this->return_msg = 0;
                        $this->display();
                }
                $pdo->commit(); //如果没有异常，就提交事务
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                $this->error = 0;
                $this->return_sql = $sql;
                $this->return_msg = array('bill_no'=>$bill_no,'goods_id'=>$goods_id);
                $this->display();
        }

        /**
         * 操作销售退货单  审核、取消  --- JUAN
         * order_sn:订单号
         * bill_no:销售退货单单号
         * type:操作 1\审核通过  2\取消
         * */
        public function OprationBillD() {

                //订单号
                if (isset($this->filter['order_sn']) && !empty($this->filter['order_sn'])) {
                        $order_sn = $this->filter['order_sn'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数order_sn或者为空";
                        $this->return_msg = '';
                        $this->display();
                }
                if (isset($this->filter['bill_no']) && !empty($this->filter['bill_no'])) {
                        $bill_no = $this->filter['bill_no'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数bill_no或者为空";
                        $this->return_msg = '';
                        $this->display();
                }
				if (isset($this->filter['opra_uname']) && !empty($this->filter['opra_uname'])) {
                        $opra_uname = $this->filter['opra_uname'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数opra_uname或者为空";
                        $this->return_msg = '';
                        $this->display();
                }

                if (isset($this->filter['type']) && !empty($this->filter['type'])) {
                        $type = $this->filter['type'];
                        if ($type != 1 && $type != 2) {
                                $this->error = 1;
                                $this->return_sql = '';
                                $this->error_msg = "参数type不正确";
                                $this->return_msg = '';
                                $this->display();
                        }
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数type或者为空";
                        $this->return_msg = '';
                        $this->display();
                }

                $sql = "SELECT `id`, `bill_status`, `to_warehouse_id` , `to_warehouse_name` , `to_company_id` , `to_company_name` FROM `warehouse_bill` WHERE `bill_no` = '" . $bill_no . "' AND `order_sn` = '" . $order_sn . "'";
                $bill_info = $this->db->getRow($sql);


                //传入的订单号和销售退货单号不对应或者错误造成不存在
                if (!count($bill_info)) {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "订单" . $order_sn . "相关的销售退货单".$bill_no."不存在，请检查。";
                        $this->return_msg = '';
                        $this->display();
                }
                //单据只有在保存状态下才能进行审核或者取消
                if ($bill_info['bill_status'] != 1) {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "单据" . $bill_no . "不是已保存状态，不允许操作。";
                        $this->return_msg = '';
                        $this->display();
                }

				//盘点中的仓库不能退货2015/6/27 星期六
				if (!empty($bill_info['to_warehouse_id']))
				{
					if ($type == 1)
					{
						$sql = "select `lock` from warehouse where id = '{$bill_info['to_warehouse_id']}'";
						$lock  = $this->db->getOne($sql);
						if ($lock==1)
						{
							$this->error = 1;
							$this->return_sql = $sql;
							$this->error_msg = "退款仓库正在盘点中，不允许审核！";
							$this->return_msg = '';
							$this->display();
						}
					}
				}
                define("root_path", dirname(dirname(dirname(dirname(__FILE__)))));
                require_once(root_path . '/frame/init.php');
                $pdo = DB::cn(22)->db(); //pdo对象
                //写事务，对各种状态进行改变。
                $bill_status = 1;
                try {
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                        $pdo->beginTransaction(); //开启事务

                        $sql = "SELECT goods_id FROM `warehouse_bill_goods` WHERE bill_id = '" . $bill_info['id'] . "'";
                        $goods_id_arr = $this->db->getAll($sql);

                        //审核通过操作
                        //1、修改单据状态为已审核
                        //2、修改货品状态为库存
                        //3、解绑和订单的关系(退货了就不需要和这个订单有绑定关系了)
                        $time = date('Y-m-d H:i:s');
                        if ($type == 1) {
                                $bill_status = 2; //已审核

                                //2、修改货品状态为库存
                                //3、解绑和订单的关系(退货了就不需要和这个订单有绑定关系了)
                                //4、变更货品的所在地
								//5、变更货品的柜位信息
								$sql = "SELECT `id` FROM `warehouse_box` WHERE `warehouse_id` = {$bill_info['to_warehouse_id']} AND `box_sn` = '0-00-0-0' LIMIT 1";
								$box_id = $this->db->getOne($sql);

                                foreach ($goods_id_arr as $key => $val) {
                                        $sql = "UPDATE `warehouse_goods` set `is_on_sale` = 2 , `change_time` = '{$time}',`order_goods_id` = 0 , `company` = '{$bill_info['to_company_name']}' , `company_id` = {$bill_info['to_company_id']} , `warehouse` = '{$bill_info['to_warehouse_name']}' , `warehouse_id` = {$bill_info['to_warehouse_id']}, `box_sn` = '0-00-0-0' WHERE `goods_id` = " . $val['goods_id'];
                                        $pdo->query($sql);
										$sql = "UPDATE `goods_warehouse` SET `warehouse_id` = {$bill_info['to_warehouse_id']}, box_id = {$box_id} WHERE `good_id` = '{$val['goods_id']}'";
										$pdo->query($sql);
                                }
                        }
                        //取消操作
                        //1、修改单据状态为已取消
                        //2、修改货品状态为已销售
                        if ($type == 2) {
                                $bill_status = 3; //已取消
                                //修改货品状态为已销售
                                foreach ($goods_id_arr as $key => $val) {
                                        $sql = "UPDATE `warehouse_goods` set is_on_sale = 3 where goods_id = " . $val['goods_id'];
                                        $pdo->query($sql);
                                }
                        }

                        //修改单据状态
                        $sql = "UPDATE `warehouse_bill` SET `bill_status`= " . $bill_status . ",`check_user` = '".$opra_uname."',`check_time` = '". $time ."'  WHERE id = " . $bill_info['id'];
                        $pdo->query($sql);

                        $sql = "INSERT INTO `warehouse_bill_status`(`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES (" . $bill_info['id'] . ",'" . $bill_no . "'," . $bill_status . ",'" . $time . "','SYSTEM','" . Util::getClicentIp() . "')";
                        $pdo->query($sql);
                } catch (Exception $e) {//捕获异常
                        //print_r($e);exit;
                        $pdo->rollback(); //事务回滚
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = "操作失败，请联系技术人员";
                        $this->return_msg = 0;
                        $this->display();
                }
                $pdo->commit(); //如果没有异常，就提交事务
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
                
                if ($bill_status == 2) {
                    //AsyncDelegate::dispatch('warehouse', array('event' => 'bill_D_checked', 'bill_id' => $bill_info['id']));
                }

                $this->error = 0;
                $this->return_sql = '';
                $this->return_msg = "操作成功";
                $this->display();
        }

        /**
         * 取消销售单  --- JUAN
         * order_sn:订单号
         * detail_id 订单明细的detail_id
         * （仅支持订单退货调用，没事儿别动哦~ 因为取消销售单后有订单和货品解绑动作，一般用不到）
         * */
        public function CancelBillS() {
                $s_time = microtime();
                if (isset($this->filter['order_sn']) && !empty($this->filter['order_sn'])) {
                        $order_sn = $this->filter['order_sn'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数order_sn或者为空";
                        $this->return_msg = '';
                        $this->display();
                }

                if (isset($this->filter['detail_id']) && !empty($this->filter['detail_id'])) {
                        $detail_id = $this->filter['detail_id'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数detail_id或者为空";
                        $this->return_msg = '';
                        $this->display();
                }

                $sql = "SELECT COUNT(1) FROM `warehouse_goods` WHERE `order_goods_id` = " . $detail_id;
                if (!$this->db->getOne($sql)) {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = $detail_id . " 没有绑定的货品，请检查。";
                        $this->return_msg = '';
                        $this->display();
                }
                define("root_path", dirname(dirname(dirname(dirname(__FILE__)))));
                require_once(root_path . '/frame/init.php');
                $pdo = DB::cn(22)->db(); //pdo对象
                //找到对应的销售单
                $sql = "SELECT id FROM `warehouse_bill` WHERE `order_sn` = '" . $order_sn . "' AND `bill_status` = 1 AND `bill_type` = 'S'";
                $bill_id = $this->db->getOne($sql);

                //写事务，对各种状态进行改变。
                try {
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                        $pdo->beginTransaction(); //开启事务
                        //在此不判断是否有有效的销售单，因为有可能一个订单两件货都在申请，第一个申请通过的时候就取消了销售单了。
                        //把已保存的相关联订单的销售单置为取消
                        $sql = "UPDATE `warehouse_bill` SET `bill_status` = 3 WHERE `id` = " . $bill_id;
                        $pdo->query($sql);

                        //把取消的销售单中的货品置为库存状态
                        $sql = "UPDATE `warehouse_goods` as wg,`warehouse_bill_goods` as wbg SET wg.`is_on_sale` = 2 WHERE wg.goods_id = wbg.goods_id and wbg.`bill_id` = " . $bill_id;
                        $pdo->query($sql);

                        //把所传的detail_id绑定的货品解绑
                        $sql = "UPDATE `warehouse_goods` SET `order_goods_id` = 0 WHERE `order_goods_id` = " . $detail_id;
                        $pdo->query($sql);
                } catch (Exception $e) {//捕获异常
                        //print_r($e);exit;
                        $pdo->rollback(); //事务回滚
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = "操作失败，请联系技术人员";
                        $this->return_msg = 0;
                        $this->display();
                }
                $pdo->commit(); //如果没有异常，就提交事务
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                $this->error = 0;
                $this->return_sql = '';
                $this->return_msg = "操作成功";
                $this->display();
        }

        // 审核销售单接口 根据order_sn
        public function checkXiaoshou() {
                 $s_time = microtime();
                if (isset($this->filter['order_sn']) && !empty($this->filter["order_sn"])) {
                        $order_sn = $this->filter['order_sn'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->return_msg = '订单号不能为空';
                        $this->display();
                }
                if (isset($this->filter['goods_ids']) && !empty($this->filter["goods_ids"])) {
                        $goods_ids = $this->filter['goods_ids'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->return_msg = '货号不能为空';
                        $this->display();
                }
                if (isset($this->filter['user']) && !empty($this->filter["user"])) {
                        $update_user = $this->filter['user'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->return_msg = '用户名不能为空';
                        $this->display();
                }
                if (isset($this->filter['ip']) && !empty($this->filter["ip"])) {
                        $update_ip = $this->filter['ip'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->return_msg = 'ip错误';
                        $this->display();
                }

			    if (isset($this->filter['time']) && !empty($this->filter["time"])) {
                        $time = $this->filter['time'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->return_msg = '时间';
                        $this->display();
                }
                $sql = "select id,bill_no from `warehouse_bill`  where order_sn ='{$order_sn}' and bill_type='S' and bill_status=1 ";
                $row = $this->db->getRow($sql);
                define("root_path", dirname(dirname(dirname(dirname(__FILE__)))));
                require_once(root_path . '/frame/init.php');
                $pdo = DB::cn(22)->db(); //pdo对象
                //$pdo = $this->db;
                try {
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                        $pdo->beginTransaction(); //开启事务
                        #1变更仓储货品状态，变为 货品状态未变为已销售
                        $sql = "UPDATE `warehouse_goods`  SET `is_on_sale` =3,`chuku_time`= '{$time}' WHERE goods_id in('" . $goods_ids . "')  and is_on_sale =10 ";
                        $pdo->query($sql);
                        #2自动审核销售单
                        $sql = "update `warehouse_bill` set bill_status=2,check_time= '{$time}',check_user='{$update_user}'  where order_sn ='{$order_sn}' and bill_type='S' and bill_status=1 ";
                        $pdo->query($sql);
                        #3仓储货品下架
                        $sql = "UPDATE `goods_warehouse` SET `box_id` = '0', `create_time` = '0000-00-00 00:00:00', `create_user` = '' WHERE `good_id` IN ('{$goods_ids}')";
                        $pdo->query($sql);
                        //写入日志
                        $date = date("Y-m-d H:i:s");
                        $sql = "INSERT INTO `warehouse_bill_status` (`id`, `bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES (NULL, '{$row['id']}', '{$row['bill_no']}', '2', '{$date}', '{$update_user}', '{$update_ip}');";
                        $pdo->query($sql);
                } catch (Exception $e) {//捕获异常
                        //print_r($e);exit;
                        $pdo->rollback(); //事务回滚
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = "数据异常，审核销售单失败";
                        $this->return_msg = 0;
                        $this->display();
                }
                $pdo->commit(); //如果没有异常，就提交事务
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
                //AsyncDelegate::dispatch('warehouse', array('event'=>'bill_S_checked', 'order_sn' =>$this->filter['order_sn'] ));
                //返回信息
                $this->error = 0;
                $this->return_sql = $sql;
                $this->return_msg = 1;
                $this->display();
        }

        #检测货品状态是否为销售中  lyh

        public function check_goods_status() {
                $s_time = microtime();
                if (isset($this->filter['goods_ids']) && !empty($this->filter['goods_ids'])) {
                        $goods_ids = $this->filter['goods_ids'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "参数传入错误";
                        $this->return_msg = array();
                        $this->display();
                }
                //判断货品是否是销售中10
                $sql = "select count(1) from `warehouse_goods` where goods_id in ('" . $goods_ids . "') and is_on_sale = 10";
                $row = $this->db->getOne($sql);
                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (!$row) {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = 0;
                        $this->return_msg = 0;
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $row;
                        $this->display();
                }
        }

        #查询该订单号对应的销售单（已保存状态）的所有货号  lyh

        public function GetGoodsIdsByOrderSN() {
                $s_time = microtime();
                if (isset($this->filter['order_sn']) && !empty($this->filter['order_sn'])) {
                        $order_sn = $this->filter['order_sn'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "参数传入错误";
                        $this->return_msg = array();
                        $this->display();
                }

                $sql = "select bg.goods_id from `warehouse_bill` as b,`warehouse_bill_goods` as bg  where b.id=bg.bill_id and b.order_sn = '{$order_sn}' and b.bill_type='S' and b.bill_status =1";
                $row = $this->db->getAll($sql);
                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (!$row) {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = 0;
                        $this->return_msg = 0;
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $row;
                        $this->display();
                }
        }

        /**
         * 获取裸钻数据（分页）
         * @param warehouse_id=仓库ID,page=当前页码,page_size=每页数量
         */
        public function getDiamondDataByHouseId() {
                $s_time = microtime();

                $where = "cat_type = '裸石'";

                if (isset($this->filter['warehouse_id']) && !empty($this->filter['warehouse_id'])) {
                        $where .= " AND `warehouse_id` in ('" . $this->filter['warehouse_id'] . "')";
                }

                if(isset($this->filter['is_on_sale'])){
                        $where .= " AND `is_on_sale` in ('" . $this->filter['is_on_sale'] . "')";
                }

                if (isset($this->filter['page_size']) && !empty($this->filter['page_size'])) {
                        $page_size = $this->filter['page_size'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数`page_size`";
                        $this->return_msg = 0;
                        $this->display();
                }
                if (isset($this->filter['page']) && !empty($this->filter['page'])) {
                        $page = $this->filter['page'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数`page`";
                        $this->return_msg = 0;
                        $this->display();
                }

                $select = ['goods_id', 'goods_sn','mo_sn', 'warehouse_id', 'order_goods_id','zhushixingzhuang', 'zhushiyanse',
                        'zhushijingdu', 'zhengshuleibie', 'zhengshuhao', 'gemx_zhengshu', 'qiegong', 'paoguang',
                        'duichen', 'yingguang', 'zuanshidaxiao', 'chengbenjia','mingyichengben','guojibaojia'
                ];
                $sql = "SELECT " . implode(',', $select) . " FROM `warehouse_goods` WHERE " . $where;
                $sql .= " ORDER BY `id` DESC";
                $data = $this->db->getPageList($sql, array(), $page, $page_size);

                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (!$data) {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = '查询失败';
                        $this->return_msg = $data;
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $data;
                        $this->display();
                }
        }

        /**
         * 获取所有启用仓库
         */
        public function getAllWarehouse() {
                $s_time = microtime();

                if (isset($this->filter['all_warehouse']) && !empty($this->filter['all_warehouse'])) {
                        $falg = $this->filter['all_warehouse'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数";
                        $this->return_msg = 0;
                        $this->display();
                }
                $data = array();
                if ($falg) {
                        $sql = "SELECT `id`,`name` FROM `warehouse` WHERE `is_delete` = '1'"; //有效仓库
                        $data = $this->db->getAll($sql);
                }

                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (!$data || empty($data)) {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = '查询失败';
                        $this->return_msg = $data;
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $data;
                        $this->display();
                }
        }

        /**
         * 获取商品属性
         */
        public function getGoodsAttrs() {
                $s_time = microtime();
                if (isset($this->filter['goods_attr']) && !empty($this->filter['goods_attr'])) {
                        $goods_attr = trim($this->filter['goods_attr']);
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数";
                        $this->return_msg = 0;
                        $this->display();
                }

                $sql = "SELECT `id`,`" . $goods_attr . "` FROM `warehouse_goods` GROUP BY `" . $goods_attr . "`";
                $data = $this->db->getAll($sql);

                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (!$data || empty($data)) {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = '查询失败';
                        $this->return_msg = $data;
                        $this->display();
                } else {
                        $data = array_column($data, $goods_attr, 'id');
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $data;
                        $this->display();
                }
        }

        /**
         * 通过商品属性查商品信息
         */
        public function getGoodsInfoByAttr() {
                $s_time = microtime();
                $_fields = $this->db->getFields('warehouse_goods');
                $_fields = array_column($_fields, 'Field');
                if (isset($this->filter['where']) && !empty($this->filter['where'])) {
                        $where = $this->filter['where'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数";
                        $this->return_msg = 0;
                        $this->display();
                }
                $_where = '';
                //主成色重 == 金重
                if (isset($where['zhuchengse_1']) && $where['zhuchengse_1'] != '') {
                        $_where .= "`jinzhong` >= " . "'" . $where['zhuchengse_1'] . "' AND ";
                        unset($where['zhuchengse_1']);
                }
                if (isset($where['zhuchengse_2']) && $where['zhuchengse_2'] != '') {
                        $_where .= "`jinzhong` <= " . "'" . $where['zhuchengse_2'] . "' AND ";
                        unset($where['zhuchengse_1']);
                }
                //主石大小 == 钻石大小
                if (isset($where['zuanshidaxiao_1']) && $where['zuanshidaxiao_1'] != '') {
                        $_where .= "`zuanshidaxiao` >= " . "'" . $where['zuanshidaxiao_1'] . "' AND ";
                        unset($where['zuanshidaxiao_1']);
                }
                if (isset($where['zuanshidaxiao_2']) && $where['zuanshidaxiao_2'] != '') {
                        $_where .= "`zuanshidaxiao` <= " . "'" . $where['zuanshidaxiao_2'] . "' AND ";
                        unset($where['zuanshidaxiao_2']);
                }

                foreach ($where as $k => $v) {
                        if (in_array($k, $_fields)) {
                                $_where .= "`" . $k . "` = '" . $v . "' AND ";
                        }
                }
                $_where = rtrim($_where, ' AND ');
                if ($_where == '') {
                        $this->error = 1;
                        $this->return_sql = $_where;
                        $this->error_msg = "查询条件有误";
                        $this->return_msg = false;
                        $this->display();
                }
                //库存状态并且未绑定
                $_where_1 = " AND `is_on_sale` = '2' AND (`order_goods_id` = '0' OR `order_goods_id` = '')";
                $sql = "SELECT `id`,`goods_id`,`goods_sn`,`goods_name`,`shoucun`,`caizhi`,`yanse`,`jingdu`,`jinzhong`,`zuanshidaxiao` FROM `warehouse_goods` WHERE " . $_where;
                $sql .= $_where_1;
                $data = $this->db->getAll($sql);

                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (!$data) {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = array();
                        $this->return_msg = $data;
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $data;
                        $this->display();
                }
        }

        /* -------------------------------lyh------------------------------- */
        /* ------------------------------------------------------ */

        /*
         * 加工商结算
         */

	public function GetProcessorInAccount()
	{

		$type = $this->filter['type'];
		$where = " where 1 AND wb.from_bill_id is null ";
		if (isset($this->filter['company']) && !empty($this->filter['company']))
		{
			$where .= " and wb.to_company_id=" . $this->filter['company'];
		}

		if (isset($this->filter['fin_status']) && !empty($this->filter['fin_status']))
		{
			$where .= " and wb.fin_check_status=" . $this->filter['fin_status'];
		}
		if (isset($this->filter['bill_status']) && !empty($this->filter['bill_status']))
		{
			$where .= " and wb.bill_status=" . $this->filter['bill_status'];
		}

        if (isset($this->filter['put_in_type']) && !empty($this->filter['put_in_type']))
        {
            $where .= " and wb.put_in_type=" . $this->filter['put_in_type'];
        }

        
		 //加工商出库结算定位搜索条件 附表b或c
		if (isset($this->filter['bill_type_info']) && !empty($this->filter['bill_type_info']))
		{
			$where .= " and wb.bill_type='" . $this->filter['bill_type_info']."'";
		}
		if (isset($this->filter['pay_channel']) && !empty($this->filter['pay_channel']))
		{
			$where .= " and wb.pro_id=" . $this->filter['pay_channel'];
		}
		if ($type == "in")
		{
            //判断是否结价
			if (isset($this->filter['account_type']) && in_array($this->filter['account_type'], array('0', '1'),true))
			{
				$where .= " and wb.jiejia=" . $this->filter['account_type'];
			}
		}
		if (isset($this->filter['make_time_start']) && !empty($this->filter['make_time_start']))
		{
			$where .= " and wb.create_time>='" . $this->filter['make_time_start'] . " 00:00:00'";
		}
		if (isset($this->filter['make_time_end']) && !empty($this->filter['make_time_end']))
		{
			$where .= " and wb.create_time<='" . $this->filter['make_time_end'] . " 23:59:59' ";
		}
		if (isset($this->filter['check_time_start']) && !empty($this->filter['check_time_start']))
		{
			$where .= " and wb.check_time>='" . $this->filter['check_time_start'] . " 00:00:00'";
		}
		if (isset($this->filter['check_time_end']) && !empty($this->filter['check_time_end']))
		{
			$where .= " and wb.check_time<='" . $this->filter['check_time_end'] . " 23:59:59' ";
		}
		if (isset($this->filter['fin_check_time_start']) && !empty($this->filter['fin_check_time_start']))
		{
			$where .= " and wb.fin_check_time>='" . $this->filter['fin_check_time_start'] . " 00:00:00' ";
		}
		if (isset($this->filter['fin_check_time_end']) && !empty($this->filter['fin_check_time_end']))
		{
			$where .= " and wb.fin_check_time<='" . $this->filter['fin_check_time_end'] . " 23:59:59' ";
		}

		$page = isset($this->filter["page"]) ? intval($this->filter["page"]) : 1;

		if ($type == "in")
		{

			$sql = "select wb.id,wb.put_in_type,wb.fin_check_status,wb.bill_type,wb.bill_note,wb.bill_no,wb.goods_num,wb.goods_total,wb.shijia,wb.check_time,wb.create_time , wb.fin_check_time,wp.pro_name,wp.pay_method,wp.amount,wp.pro_id from warehouse_bill as wb,warehouse_bill_pay as wp
			" . $where . " and wb.id=wp.bill_id and wb.bill_type in('L','T') and wb.bill_status=2 order by wb.create_time DESC";

		}
		else
		{
			$sql = "select wb.id,wb.bill_type,wb.put_in_type,wb.fin_check_status,wb.bill_note,wb.bill_no,wb.goods_num,wb.goods_total,wb.shijia,wb.check_time,wb.create_time , wb.fin_check_time, wp.pro_name,wp.pay_method,wp.amount,wp.pro_id from warehouse_bill as wb left join warehouse_bill_pay as wp on wb.id=wp.bill_id
              " . $where . "  and  wb.bill_type in ('B','C') and wb.bill_status=2  order by wb.create_time DESC";
		}
		if (empty($page))
		{
			//file_put_contents("D:\u223.txt",$sql."\r\n",FILE_APPEND );
			$res = $this->db->getAll($sql);
		}
		else
		{
			$res = $this->db->getPageList($sql, array(), $page, 20);
		}
		if (!$res)
		{
				$this->error = 1;
				$this->return_sql = $sql;
				$this->error_msg = "没有查到相应信息";
				$this->return_msg = array();
				$this->display();
		}
		else
		{
				$this->error = 0;
				$this->return_sql = $sql;
				$this->return_msg = $res;
				$this->display();
		}
	}

	//财务审核，更改状态
	public function UpdateFinCheck() {
//        $this->return_msg="hahahahaha";
//         $this->display();
			$fin_check_time = date("Y-m-d H:i:s", time());
			$where = " where 1 ";
			if (isset($this->filter['bill_no']) && $this->filter['bill_no'] != '') {
					$where .= " and bill_no='" . $this->filter['bill_no'] . "'";
			}

			$sql = "update warehouse_bill set fin_check_status=2,fin_check_time='" . $fin_check_time . "'" . $where;
			//$ct = $this->db->getOne($sql);
			$rs = $this->db->query($sql);

			if (!$rs) {
					$this->error = 1;
					$this->return_sql = $sql;
					$this->error_msg = 0;
					$this->return_msg = "没有更新状态，审核失败";
					$this->display();
			} else {
					$this->error = 0;
					$this->return_sql = $sql;
					$this->error_msg = 0;
					$this->return_msg = $rs;
					$this->display();
			}
	}

	//是否已经审核
	public function checkFinCheckStatus() {
			$bill_no = $this->filter['bill_no'];
			$sql = "select count(1) from warehouse_bill where fin_check_status=2 and bill_no='" . $bill_no . "'";
			$ret = $this->db->getOne($sql);
			if ($ret) {
					$this->error = 1;
					$this->return_sql = "$sql";
					$this->error_msg = "该状态已经审核";
					$this->return_msg = $ret;
					$this->display();
			} else {
					$this->error = 0;
					$this->return_sql = "$sql";
					$this->error_msg = "待审核";
					$this->return_msg = $ret;
					$this->display();
			}
	}

        //获取金重
        public function getJinZhong() {
                $bill_no = $this->filter['bill_no'];
                $ret = array();
                if(strpos($bill_no,",") !== false){
                    $bill_no = str_replace(',',"','",$bill_no);
                    $sql = "select bill_no,sum(jinzhong) as jinzhong from warehouse_bill_goods where bill_no IN ('" . $bill_no . "') GROUP BY `bill_no` ";
                    //echo $sql;exit;
                    $tmp = $this->db->getAll($sql);
                    if($tmp){
                        foreach ($tmp as $k=>$v){
                            $ret[$v['bill_no']] = $v['jinzhong'];
                        }
                    }
                }else{
                    $sql = "select sum(jinzhong) as jinzhong from warehouse_bill_goods where bill_no='" . $bill_no . "'";
                    $ret = $this->db->getRow($sql);
                }
                
//                 $sql = "select sum(jinzhong) as jinzhong from warehouse_bill_goods where bill_no='" . $bill_no . "'";
//                 $ret = $this->db->getRow($sql);
                if ($ret) {
                        $this->error = 0;
                        $this->return_sql = "$sql";
                        $this->return_msg = $ret;
                        $this->display();
                } else {
                        $this->error = 1;
                        $this->return_sql = "$sql";
                        $this->return_msg = "no data";
                        $this->display();
                }
        }

        //get send_goods_sn
        public function getSendGoodsSn() {
                $bill_id = $this->filter['bill_id'];
                if(strpos($bill_id,",") !== false){
                    $sql = "select id,send_goods_sn from warehouse_bill where id IN (" . $bill_id . ")";
                    $tmp = $this->db->getAll($sql);
                    if($tmp){
                        foreach ($tmp as $k=>$v){
                            $ret[$v['id']] = $v['send_goods_sn'];
                        }
                    }
                }else{
                    $sql = "select send_goods_sn from warehouse_bill where id='" . $bill_id . "'";
                    $ret = $this->db->getRow($sql);
                }
                if ($ret) {
                        $this->error = 0;
                        $this->return_sql = "$sql";
                        $this->return_msg = $ret;
                        $this->display();
                } else {
                        $this->error = 1;
                        $this->return_sql = "$sql";
                        $this->return_msg = "no data";
                        $this->display();
                }
        }


        /**
         * 获取销账公司
         */
        public function getWriteOffCompany() {
            $s_time = microtime();

            if (!empty($this->filter['pay_type'])) {
                    $sql = "select * from `write_off_company` where `pay_type_id`={$this->filter['pay_type']}";
            } else {
                    $this->error = 1;
                    $this->return_sql = '';
                    $this->error_msg = "订购类型不能为空!!";
                    $this->return_msg = array();
                    $this->display();
            }
            $data = $this->db->getRow($sql);
            // 记录日志
            $reponse_time = microtime() - $s_time;
            $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

            //返回信息
            if (!$data) {
                    $this->error = 1;
                    $this->return_sql = $sql;
                    $this->error_msg = "未查询到此商品";
                    $this->return_msg = array();
                    $this->display();
            } else {
                    $this->error = 0;
                    $this->return_sql = $sql;
                    $this->return_msg = $data;
                    $this->display();
            }
        }


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
         * 老系统货号更新到新系统
		 * 如果所有需要修改的货号都符合条件（只修改新系统中status是100的状态）
		 * 修改新系统中货品状态为2（库存），更新公司和仓库
         * @return json
         */
        public function OldsysToNewSys()
		{
			$s_time = microtime();
			$where = '';
			$model     = new CompanyModel(1);

			$sql = "SELECT goods_id,is_on_sale FROM `warehouse_goods` WHERE 1"; //暂时用＊号

			if (!empty($this->filter['goods_id_str']))
			{
					$sql .= " and `goods_id` in ('{$this->filter['goods_id_str']}') and is_on_sale = 100 ";
			}
			else
			{
					$this->error = 1;
					$this->return_sql = $sql;
					$this->error_msg = "货号不能为空!!";
					$this->return_msg = $this->filter['goods_id_str'];
					$this->display();
			}

			if (!(isset($this->filter['update_data']) && !empty($this->filter['update_data'])))
			{
					$this->error = 1;
					$this->return_sql = $sql;
					$this->error_msg = "货号信息不能为空!!";
					$this->return_msg = array();
					$this->display();
			}
			$update_data = $this->filter['update_data'];//老系统传入数据
			$data = $this->db->getAll($sql);//新系统查询数据

			//1、如果查出符合条件的数量不等于传过来的数量，则提示
			if(count($data)!=count($update_data))
			{
					$this->error = 1;
					$this->return_sql = $sql;
					$this->error_msg = "请检查新系统数据货品状态是否100或货品是否存在，新系统查询数量".count($data)."|||"."老系统需修改数量".count($this->filter['update_data']);
					$this->return_msg = "新系统查询数量".count($data)."|||"."老系统需修改数量".count($this->filter['update_data']);
					$this->display();
			}

			//2、所有条件通过则wangxiazou，需要修改新系统中的货品状态和仓库
			//保证数据一致
			define("root_path", dirname(dirname(dirname(dirname(__FILE__)))));
			require_once(root_path . '/frame/init.php');
			$pdo = DB::cn(22)->db(); //pdo对象
			try
			{
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
				$pdo->beginTransaction(); //开启事务
				foreach ($update_data as $key=>$val)
				{
					#循环修改货品状态和库存状态
					//根据公司id查询公司名称，仓储id查询仓储名称
					$sql = "select name from warehouse where id={$val['warehouse']}";
					$warehouse =$this->db->getOne($sql);
					$company = $model->getCompanyName($val['company']);
					$sql = "update warehouse_goods set is_on_sale = 2,warehouse_id='{$val['warehouse']}',warehouse='{$warehouse}',company_id='{$val['company']}',company='{$company}' where goods_id = '{$val['goods_id']}' and is_on_sale = 100 ";
					$pdo->query($sql);
				}
			}
			catch (Exception $e)
			{
					$pdo->rollback(); //事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
					$this->error = 1;
					$this->return_sql = $sql;
					$this->error_msg = "新系统数据修改失败--接口".$sql;
					$this->return_msg = 0;
					$this->display();
			}
			$pdo->commit(); //如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交


            // 记录日志
			$reponse_time = microtime() - $s_time;
			$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

			$this->error = 0;
			$this->return_sql = $sql;
			$this->return_msg = '修改成功';
			$this->display();
        }

		 /**
         *  老系统转仓审核时，转仓货品/货品位置/公司都需要更新到新系统
		 *  新系统 柜位下架 并重新上架
		 *     货品不限制任何条件
         */
        public function ZCToNewSys()
		{
			$s_time = microtime();
			if (!(isset($this->filter['update_data']) && !empty($this->filter['update_data'])))
			{
					$this->error = 1;
					$this->return_sql = '';
					$this->error_msg = "货号信息不能为空!!";
					$this->return_msg = array();
					$this->display();
			}
			$update_data = $this->filter['update_data'];//老系统传入数据

			//保证数据一致
			define("root_path", dirname(dirname(dirname(dirname(__FILE__)))));
			require_once(root_path . '/frame/init.php');
			$pdo = DB::cn(22)->db(); //pdo对象
			try
			{
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
				$pdo->beginTransaction(); //开启事务
				foreach ($update_data as $key=>$val)
				{
					#1、循环修改 位置、公司 (转仓审核后是库存)
					//根据仓储id查询公司id/名称，仓储id查询仓储名称
					$sql = "select name from warehouse where id={$val['warehouse']}";
					$warehouse =$this->db->getOne($sql);
					$sql = "select company_id,company_name from  warehouse_rel where warehouse_id={$val['warehouse']}";
					$company_info =$this->db->getRow($sql);
					$company  = $company_info['company_name'];
					$company_id = $company_info['company_id'];

					$sql = "update warehouse_goods set warehouse_id='{$val['warehouse']}',warehouse='{$warehouse}',company_id='{$company_id}',company='{$company}' where goods_id = '{$val['goods_id']}'";
					//file_put_contents('d:/lyh.txt',$sql."\r\n",FILE_APPEND);
					$pdo->query($sql);
					#2、货品下架 重新上架
					$default_box = $this->db->getOne("select id from warehouse_box where `warehouse_id`='{$val['warehouse']}'  and `box_sn` = '0-00-0-0' AND `is_deleted` = 1 ");
					$sql = "UPDATE `goods_warehouse` SET `box_id` = '{$default_box}' , `warehouse_id` = {$val['warehouse']}, `create_user` = '' , `create_time` = '0000-00-00 00:00:00' WHERE `good_id` = '{$val['goods_id']}' ";
					//file_put_contents('d:/lyh.txt',$sql."\r\n",FILE_APPEND);
					$pdo->query($sql);

				}
			}
			catch (Exception $e)
			{
					$pdo->rollback(); //事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
					$this->error = 1;
					$this->return_sql = $sql;
					$this->error_msg = "新系统数据修改失败--接口";
					$this->return_msg = 0;
					$this->display();
			}
			$pdo->commit(); //如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交


            // 记录日志
			$reponse_time = microtime() - $s_time;
			$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

			$this->error = 0;
			$this->return_sql = $sql;
			$this->return_msg = '修改成功';
			$this->display();
        }

        /**
        * 删除公司，禁用该公司底下的仓库   hulichao
        * @param $company_id 被删除的公司ID
        */
        public function PAISI_Warehouse()
        {
            $s_time = microtime();
            if (!(isset($this->filter['company_id']) && !empty($this->filter['company_id'])))
            {
                $this->error = 1;
                $this->return_sql = '';
                $this->error_msg = "请传递一个公司ID!!";
                $this->return_msg = array();
                $this->display();
            }
            $company_id = $this->filter['company_id'];

            //检测要删除的公司底下有没有货
            $sql = "SELECT `id` FROM `warehouse_goods` WHERE `company_id` = {$company_id} LIMIT 1";
            $extsis = $this->db->getOne($sql);
            if($extsis){
                $this->error = 1;
                $this->return_sql = '';
                $this->error_msg = "该公司底下有货品，禁止一切删除行为";
                $this->return_msg = array();
                $this->display();
            }

            //获取公司下所有的仓库
            $sql = "SELECT `warehouse_id` FROM `warehouse_rel` WHERE `company_id` = {$company_id}";
            $data = $this->db->getAll($sql);
            $warehouse_ids = "";
            foreach ($data as $key => $value) {
                $warehouse_ids .= ",'{$value['warehouse_id']}'";
            }
            $warehouse_ids = trim($warehouse_ids , ",");

            if(!strlen($warehouse_ids)){
                 $this->error = 0;
                $this->return_sql = $sql;
                $this->return_msg = '删除公司，公司底下没有柜位要禁用';
                $this->display();
            }
            //保证数据一致
            define("root_path", dirname(dirname(dirname(dirname(__FILE__)))));
            require_once(root_path . '/frame/init.php');
            $pdo = DB::cn(22)->db(); //pdo对象
            try
            {
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                $pdo->beginTransaction(); //开启事务
                //禁用仓库
                $sql = "UPDATE `warehouse` SET `is_delete` = 0 WHERE `id` IN ($warehouse_ids)";
                $pdo->query($sql);
                //禁用柜位
                $sql = "UPDATE `warehouse_box` SET `is_deleted`= 0 WHERE `warehouse_id` IN ($warehouse_ids)";   //禁用柜位
                $pdo->query($sql);
            }
            catch (Exception $e)
            {
                $pdo->rollback(); //事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
                $this->error = 1;
                $this->return_sql = $sql;
                $this->error_msg = "删除公司时，禁用公司下的仓库柜位失败";
                $this->return_msg = 0;
                $this->display();
            }
            $pdo->commit(); //如果没有异常，就提交事务
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交

            // 记录日志
            $reponse_time = microtime() - $s_time;
            $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = '删除公司，禁用公司下的仓库成功';
            $this->display();
        }

	//布产单列表页面打印提货单，获取提货单绑定的货以及柜位
	public function GetOrderDetailBing(){
		$s_time = microtime();
		if (!(isset($this->filter['order_goods_id']) && !empty($this->filter['order_goods_id'])))
		{
			$this->error = 1;
			$this->return_sql = '';
			$this->error_msg = "请传递订单明细的ID";
			$this->return_msg = array();
			$this->display();
		}
		//获取订单明细ID
		$order_goods_id = $this->filter['order_goods_id'];

		$sql = "SELECT `a`.`goods_id`, `c`.`box_sn` FROM `warehouse_goods` AS `a` LEFT JOIN `goods_warehouse` AS `b` ON `a`.`goods_id` = `b`.`good_id` INNER JOIN `warehouse_box` AS `c` ON `b`.`box_id` = `c`.`id` WHERE `a`.`order_goods_id` = {$order_goods_id}";
		$row = $this->db->getRow($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		$this->error = 0;
		$this->return_sql = $sql;
		$this->return_msg = $row;
		$this->display();
	}
	/*解绑上架*/
	 public function JiebasjiaGoodsInfoByGoodsId() {
                $s_time = microtime();
                $set = '';
                $where = '';
			if ($this->filter['bind_type'] == 2) {//解绑
                        $set .= " `order_goods_id` = '' ,`is_on_sale` = '2' ";
                        if ($this->filter['order_goods_id']) {
                                $where .= " `order_goods_id` = '" . $this->filter['order_goods_id'] . "' ";
                        } else {
                                $this->error = 1;
                                $this->return_sql = "";
                                $this->error_msg = "绑定编号不能为空!!";
                                $this->return_msg = array();
                                $this->display();
                        }
                } else {
                        $this->error = 1;
                        $this->return_sql = "";
                        $this->error_msg = "类型不能为空!!";
                        $this->return_msg = array();
                        $this->display();
                }


                if ($this->filter['bind_type'] == 2) {//绑定
                        $sql = "update `warehouse_goods` set " . $set . "  WHERE " . $where ;
                }

                $data = $this->db->query($sql);

                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                //返回信息
                if (!$data) {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = "未查询到此商品";
                        $this->return_msg = array();
                        $this->display();
                } else {
                        $this->error = 0;
                        $this->return_sql = $sql;
                        $this->return_msg = $data;
                        $this->display();
                }
        }
	public function jiebang() 
	{
		$s_time = microtime();
		if (isset($this->filter['order_goods_id'])) 
		{
			$order_goods_id = $this->filter['order_goods_id'];
			$sql = "select goods_id from warehouse_goods where order_goods_id = $order_goods_id";
					//	file_put_contents('d://lyh.txt',$sql.'\n',FILE_APPEND);

			$goods_id  = $this->db->getOne($sql);
		}
		else 
		{
			$this->error = 1;
			$this->return_sql = "";
			$this->error_msg = "";
			$this->return_msg = '';
			$this->display();
		 }

		if (!empty($goods_id))
		{
			$sql = "update warehouse_goods set order_goods_id = 0 where goods_id =$goods_id";
					//	file_put_contents('d://lyh.txt',$sql.'\n',FILE_APPEND);

			$this->db->query($sql);
			$this->error = 0;
			$this->return_sql = $sql;
			$this->return_msg =array($goods_id);
			$this->display();

		}
		else
		{
				$this->error = 1;
				$this->return_sql = $sql;
				$this->error_msg = "";
				$this->return_msg = '';
				$this->display();
		}

     }

        /**
         * 	GetWarehouseBillPay
         *  1,获取仓库订单结算商列表   BY zhouliang
         *  2,传订单id获取相应结果
         */
        public function GetWarehouseBillPayList() {
            $s_time = microtime();
			
            $sql = "SELECT p.* FROM warehouse_bill AS b LEFT JOIN warehouse_bill_pay AS p ON b.id=p.bill_id WHERE 1" ; //暂时用＊号

            if (isset($this->filter['bill_no'])) {
                $sql .= " AND b.`bill_no` = '{$this->filter['bill_no']}'";
            }
			if (isset($this->filter['pro_id'])) {
                $sql .= " AND p.`pro_id` = '{$this->filter['pro_id']}'";
            }
            $data['data'] = $this->db->getAll($sql);


            // 记录日志
            $reponse_time = microtime() - $s_time;
            $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
            //返回信息
            if (!$data['data']) {
                $this->error = 1;
                $this->return_sql = $sql;
                $this->error_msg = "查询结算商列表信息失败";
                $this->return_msg = array();
                $this->display();
            } else {
                $this->error = 0;
                $this->return_sql = $sql;
                $this->return_msg = $data['data'];
                $this->display();
            }
        }
		
		/**
         * 更新结价状态
         * @return json
         */
        public function UpdateJiejia() {
			if (!empty($this->filter['goods_id'])){
				if (is_array($this->filter['goods_id'])){
					$sql = "update `warehouse_goods` set jiejia = 1 WHERE goods_id in ('" . implode("','", $this->filter['goods_id']) . "')";
				}else{
					$sql = "update `warehouse_goods` set jiejia = 1 WHERE goods_id = '{$this->filter['goods_id']}'";
				}
				$data = $this->db->query($sql);
				
				//返回信息
				if (!$data) {
						$this->error = 1;
						$this->return_sql = $sql;
						$this->error_msg = "无法更新结价状态";
						$this->return_msg = array();
						$this->display();
				} else {
						$this->error = 0;
						$this->return_sql = $sql;
						$this->return_msg = $data;
						$this->display();
				}
			
			}else{
				$this->error = 1;
				$this->return_sql = '';
				$this->error_msg = "无法更新结价状态";
				$this->return_msg = array();
				$this->display();
			}
		}
		
		
		/**
         * 	GetWarehouseBillGoods
         *  获取仓库订单商品列表   BY zhouliang
         */
        public function GetWarehouseBillGoods() {
            $s_time = microtime();
			
            $sql = "SELECT * FROM warehouse_bill_goods warehouse_bill_goods WHERE 1" ; //暂时用＊号

            if (isset($this->filter['bill_no'])) {
                $sql .= " AND bill_no = '{$this->filter['bill_no']}'";
            }
			if (isset($this->filter['bill_type'])) {
                $sql .= " AND bill_type = '{$this->filter['bill_type']}'";
            }
            $data['data'] = $this->db->getAll($sql);


            // 记录日志
            $reponse_time = microtime() - $s_time;
            $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
            //返回信息
            if (!$data['data']) {
                $this->error = 1;
                $this->return_sql = $sql;
                $this->error_msg = "查询仓库订单商品列表失败";
                $this->return_msg = array();
                $this->display();
            } else {
                $this->error = 0;
                $this->return_sql = $sql;
                $this->return_msg = $data['data'];
                $this->display();
            }
        }
        
        public function GetDiaByCertIdOrGoodsId() {        	
        	if (!isset($this->filter['cert_id']) || empty($this->filter['cert_id']) 
        		|| !isset($this->filter['company_id']) || empty($this->filter['company_id'])) {
        		$this->error = 1;
        		$this->return_sql = $sql;
        		$this->error_msg = "查询仓库订单商品列表失败";
        		$this->return_msg = array();
        		$this->display();
        	}
        	
        	$cert_id = $this->filter['cert_id'];
        	$company_id= $this->filter['company_id'];
        	
        	$sql = "SELECT * FROM warehouse_goods WHERE is_on_sale = 2 and company_id = {$company_id} and (zhengshuhao = '{$cert_id}' or goods_id = '{$cert_id}') and (( product_type1 ='钻石' and cat_type1  in('裸石','彩钻')) or ( product_type1 ='彩钻' and cat_type1 in('钻石','彩钻','裸石')))"; 
        	$data = $this->db->getRow($sql);
        	
        	//返回信息
        	if (!$data) {
        		$this->error = 1;
        		$this->return_sql = $sql;
        		$this->error_msg = "查询不到该钻";
        		$this->return_msg = array();
        		$this->display();
        	} else {
        		
        		if ($data['zhengshuleibie'] == 'HRD-D' && strpos($data['goods_sn'],  'DB') === 0) {
        			//识别天生一对
        			$sql = "SELECT * FROM warehouse_goods WHERE is_on_sale = 2 and company_id = {$company_id} and goods_sn = '{$data['goods_sn']}' and (( product_type1 ='钻石' and cat_type1  in('裸石','彩钻')) or ( product_type1 ='彩钻' and cat_type1 in('钻石','彩钻','裸石')))";
        			$data = $this->db->getAll($sql);
        		} else {
        			$data = array($data);
        		}
        		
        		$this->error = 0;
        		$this->return_sql = $sql;
        		$this->return_msg = $data;
        		$this->display();
        	}
        }

    /**
     * 查询仓储列表分页信息
     * @param *
     * @return json
     */
    public function getwitwarehousegoodslist()
    {
       
        $where=" 1=1 ";
        $filter=$this->filter;
        if(empty($filter['channel_id'])){
                    $this -> error = 1;
                    $this -> error_msg = "参数 channel_id不能为空";
                    $this->display();
        }         
        if(!empty($filter['goods_id'])){
            if(is_array($filter['goods_id'])){
                $where .= " AND g.`goods_id` in('".implode("','",$filter['goods_id'])."')";
            }else{
                $where .= " AND g.`goods_id`='".$filter['goods_id']."'";
            }
        }
        if(!empty($filter['company_id'])) {
            $where .= " AND g.`company_id`='".$filter['company_id']."'";
        }
        if(!empty($filter['goods_name'])) {
            $where .= "and (g.goods_id='{$filter['goods_name']}' or g.goods_sn='{$filter['goods_name']}' or g.goods_name like '%{$filter['goods_name']}%' or g.zhengshuhao='{$filter['goods_name']}')";
        }
        if(!empty($filter['goods_sn'])){
            if(is_array($filter['goods_sn'])){
                $where .= " AND g.`goods_sn` in('".implode("','",$filter['goods_sn'])."')";
            }else{
                $where .= " AND g.`goods_sn`='".$filter['goods_sn']."'";
            }
        }
        if(!empty($filter['put_in_type'])){
            if(is_array($filter['put_in_type'])){
                $where .= " AND g.`put_in_type` in(".implode(",",$filter['put_in_type']).")";
            }else{
                $where .= " AND g.`put_in_type`='".$filter['put_in_type']."'";
            }
        }

        //仅售现货
       if(!empty($filter['goods_sn_not'])){
            if(is_array($filter['goods_sn_not'])){
                $where .= " AND g.`goods_sn` not in ('".implode("','",$filter['goods_sn_not'])."')";
            }else{
                $where .= " AND g.`goods_sn`!='".$filter['goods_sn_not']."'";
            }
        }
        if(!empty($filter['tuo_type'])){
            if(is_array($filter['tuo_type'])){
                $where .= " AND g.`tuo_type` in('".implode("','",$filter['tuo_type'])."')";
            }else{
                $where .= " AND g.`tuo_type`='".$filter['tuo_type']."'";
            }
        }
        if(!empty($filter['is_on_sale'])){
            if(is_array($filter['is_on_sale'])){
                $where .= " AND g.`is_on_sale` in('".implode("','",$filter['is_on_sale'])."')";
            }else{
                $where .= " AND g.`is_on_sale`='".$filter['is_on_sale']."'";
                //查询库存商品条件
                if($filter['is_on_sale']==2){
                    $where .= " AND (g.`jijiachengben`>0 and g.order_goods_id=0) ";
                }
            }
        }
        if(!empty($filter['warehouse_id'])){
            if(is_array($filter['warehouse_id'])){
                $where .= " AND g.`warehouse_id` in(".implode(",",$filter['warehouse_id']).")";
            }else{
                $where .= " AND g.`warehouse_id`=".$filter['warehouse_id'];
            }
        }
        if(!empty($filter['box_sn'])){
            if(is_array($filter['box_sn'])){
                $where .= " AND g.`box_sn` in('".implode("','",$filter['box_sn'])."')";
            }else{
                $where .= " AND g.`box_sn`='".$filter['box_sn']."'";
            }
        }
        if(isset($filter['carat_min']) && $filter['carat_min']!==""){
            $where .= " AND g.zuanshidaxiao >={$filter['carat_min']}";
        }
        if(isset($filter['carat_max']) && $filter['carat_max']!==""){
            if($filter['carat_max']==0){
                $where .= " AND (g.zuanshidaxiao=0 or g.zuanshidaxiao is null)";

            }else{
                $where .=" AND g.zuanshidaxiao <{$filter['carat_max']}";
            }
        }
        if(isset($filter['price_min']) && $filter['price_min']!==""){
            //$filter['price_min'] = round($filter['price_min']/1.8);
            $where .=" AND gs.sale_price >={$filter['price_min']}";
        }
        if(isset($filter['price_max']) && $filter['price_max']!==""){
            //$filter['price_max'] = round($filter['price_max']/1.8);
            $where .=" AND gs.sale_price <={$filter['price_max']}";
        }

        if(!empty($filter['caizhi'])){
            $caizhiArr = $this->getCaizhiList();
            if(is_array($filter['caizhi'])){
                foreach ($filter['caizhi'] as $k=>$v){
                    if(is_numeric($v)){
                        $filter['caizhi'][$k] = $caizhiArr[$v];
                    }
                }
                $where .= " AND g.`caizhi` in('".implode("','",$filter['caizhi'])."')";
            }else{
                if(is_numeric($filter['caizhi'])){
                    $filter['caizhi'] = $caizhiArr[$filter['caizhi']];
                }
                $where .= " AND g.`caizhi` like '%".$filter['caizhi']."%'";
            }
        }
        if(!empty($filter['product_type'])) {
            if(empty($productTypeArr)){
                $productTypeArr = $this->getProductTypeList();
            }
            if(is_array($filter['product_type'])){
                foreach ($filter['product_type'] as $k=>$v){
                    if(is_numeric($v)){
                        $filter['product_type'][$k] = $productTypeArr[$v];
                    }
                }
                $where .= " AND g.`product_type1` in('".implode("','",$filter['product_type'])."')";
            }else{
                if(is_numeric($filter['product_type'])){
                    $filter['product_type'] = $productTypeArr[$filter['product_type']];
                }
                $where .= " AND g.`product_type1`='".$filter['product_type']."'";
            }
        }
        if(!empty($filter['product_type_not'])) {
            if(is_array($filter['product_type_not'])){
                $where .= " AND g.`product_type1` not in('".implode("','",$filter['product_type'])."')";
            }else{
                $where .= " AND g.`product_type1`!='".$filter['product_type']."'";
            }
        }
        if(!empty($filter['cat_type'])) {
            if(empty($catTypeArr)){
                $catTypeArr = $this->getCatTypeList();
            }
            if(is_array($filter['cat_type'])){
                foreach ($filter['cat_type'] as $k=>$v){
                    if(is_numeric($v)){
                        $filter['cat_type'][$k] = $catTypeArr[$v];
                    }
                }
                $where .= " AND g.`cat_type1` in('".implode("','",$filter['cat_type'])."')";
            }else{
                if(is_numeric($filter['cat_type'])){
                    $filter['cat_type'] = $catTypeArr[$filter['cat_type']];
                }
                $where .= " AND g.`cat_type1`='".$filter['cat_type']."'";
            }
        }

        //镶口
        if(!empty($filter['xiangkou'])) {
            if(is_array($filter['xiangkou'])){
                $where .= " AND g.`jietuoxiangkou` in('".implode("','",$filter['xiangkou'])."')";
            }else{
                $where .= " AND g.`jietuoxiangkou`='".$filter['xiangkou']."'";
            }
        }
        if(isset($filter['xiangkou_min']) && $filter['xiangkou_min'] !== '') {
            $where .= " AND g.jietuoxiangkou>={$filter['xiangkou_min']}";
        }
        if(isset($filter['xiangkou_max']) && $filter['xiangkou_max'] !== '') {
            $where .= " AND g.jietuoxiangkou<={$filter['xiangkou_max']}";
        }
        //手寸
        if(!empty($filter['shoucun'])) {
            if(is_array($filter['shoucun'])){
                $where .= " AND g.`shoucun` in('".implode("','",$filter['shoucun'])."')";
            }else{
                $where .= " AND g.`shoucun`='".$filter['shoucun']."'";
            }
        }
        if(!empty($filter['shoucun_min'])) {
            $where .= " AND g.shoucun>='{$filter['shoucun_min']}'";
        }
        if(!empty($filter['shoucun_max'])) {
            $where .= " AND g.shoucun<='{$filter['shoucun_max']}'";
        }   
        //推荐
        if(isset($filter['is_recommend']) && !empty($filter['is_recommend'])){
            $where .= " AND bg.is_recommend = {$filter['is_recommend']}"; 
        }     
        if(!empty($filter['xilie'])){
            $where .= " and g.goods_sn in (select style_sn from front.base_style_info where check_status=3 and xilie like '%,{$filter['xilie']},%')";
        }
        
        $orderby_list = array(
            '1|1'=>"",
            '1|2'=>"",
            '2|1'=>"bg.goods_click asc",
            '2|2'=>"bg.goods_click desc",
            '3|1'=>"g.goods_id asc",
            '3|2'=>"g.goods_id desc",
            '4|1'=>"bg.goods_salenum asc",
            '4|2'=>"bg.goods_salenum desc",
            '5|1'=>"g.jijiachengben asc",
            '5|2'=>"g.jijiachengben desc",
        );
        $order_by = "";
        if(!empty($filter['order_by']) && !empty($orderby_list[$filter['order_by']])){
            $order_by = $orderby_list[$filter['order_by']];
        }
        $page = !empty($filter['page'])?$filter['page']:1;
        $pageSize = !empty($filter['pageSize'])?$filter['pageSize']:50;
        $useCache = !empty($this ->filter['useCache'])?$this->filter['useCache']:false;
        $recordCount = !empty($this ->filter['recordCount'])?$this->filter['recordCount']:false;
        $sql = "select g.*,gs.pifajia,gs.sale_price from warehouse_shipping.warehouse_goods g inner join `warehouse_shipping`.`warehouse` w on g.warehouse_id=w.id and w.is_delete=1 inner join warehouse_shipping.warehouse_goods_ishop_price gs on g.goods_id=gs.goods_id and gs.channel_id='{$filter['channel_id']}' and w.is_default=0 left join front.base_style_info bg on g.goods_sn=bg.style_sn where ".$where;
        if($order_by) $sql=$sql." order by ".$order_by;
        //$this->recordLog($sql);
//file_put_contents('warehouse.log', $sql);
        $data = $this->db->getPageListNew($sql, array(), $page, $pageSize, $useCache,$recordCount);
        
        if(!empty($filter['extends'])){
            //上级批发给下级的批发价计算
            if(in_array('pifajia',$filter['extends']) && !empty($data['data'])){
                //批发客户公司ID
                if(empty($filter['wholesale_company_id'])){
                    $this -> error = 1;
                    $this -> error_msg = "参数 wholesale_company_id不能为空";
                    $this->display();
                }
                $warehouseGoodsModel = Util::get_model('warehouse\WarehouseGoodsModel', [21]);
                $warehouseGoodsModel->calcPifajia($data['data'], $filter['wholesale_company_id']);
            }
        }
        
        //记录日志
        $s_time = microtime();
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        if(!empty($data))
        {
            $this -> error = 0;
            $this -> error_msg = "查询成功";
        }
        else
        {
            $this -> error = 1;
            $this -> error_msg = "查询失败";
        }
        $this -> return_sql = $sql;
        $this -> return_msg = $data;
        $this->display();
    }
    
    private function getCatTypeList(){
        $sql = "SELECT cat_type_id,cat_type_name from front.app_cat_type";
        $data = $this->db->getAll($sql);
        $data = array_column($data,"cat_type_name",'cat_type_id');
        return $data;
    }

    private function getProductTypeList(){
        $sql = "SELECT product_type_id,product_type_name from front.app_product_type";
        $data =$this->db->getAll($sql);
        $data = array_column($data,"product_type_name",'product_type_id');
        return $data;
    }

    /**
     * 获取所有材质列表
     */
    private function getCaizhiList()
    {
        $data= array(
            '10'=>'9K',
            '13'=>'10K',
            '9'=>'14K',
            '1'=>'18K',
            '11'=>'PT900',
            '2'=>'PT950',
            '17'=>'PT990',
            '12'=>'PT999',
            '3'=>'18K&PT950',
            '4'=>'S990',
            '6'=>'S925',
            '8'=>'足金',
            '5'=>'千足银',
            '7'=>'千足金',
            '14'=>'千足金银',
            '15'=>'裸石',
            '16'=>'无',
            '0'=>'其它',
            '18'=>'S999'
        );
        return $data;
    }
    /**
     * 批量更新货品信息（根据货号）
     * @param goods_id
     * @return json
     */
    public function updateWarehouseGoodsById()
    {
        $s_time = microtime();
        if(!is_array($this->filter['data'])){
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "数据不能为空！";
            $this->display();
        }
        $data = $this->filter['data'];
        foreach($data as $goods_id=>$field_values){
            $where = "goods_id='{$goods_id}'";
            $res = $this->db->autoExecute('warehouse_goods', $field_values, 'UPDATE', $where);
        }
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($field_values));
    
        //返回信息
        if($res===false){
            $this -> error = 1;
            $this -> return_sql = $res;
            $this -> error_msg = "操作失败";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $res;
            $this -> return_msg = "操作成功";
            $this->display();
        }
    }

    /**智慧门店生成销售单
    * 1/生成销售单
    * 2/改变货品仓储状态
    * 3/配货单状态变更 [数据库app_order 中的 base_order_info表中 delivery_status改为 5 已配货]
    * @param $user_name String 用户名称
    * @param $order_sn String 订单编号
    * @param $from_company_id  出库公司ID
    * @param $from_company_name  出库公司名称
    */
    public function createBillInfoS()
    {
        $filter = isset($this->filter['data']) && !empty($this->filter['data']) ?$this->filter['data']:array();
        if(empty($filter)){
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "参数不能为空";
            $this -> return_msg = array();
            $this->display();
        }
        $order_sn = isset($filter['order_sn']) && !empty($filter['order_sn'])?$filter['order_sn']:'';
        $from_company_id=isset($filter['from_company_id']) && !empty($filter['from_company_id'])?$filter['from_company_id']:'';
        $from_company_name = isset($filter['from_company_name']) && !empty($filter['from_company_name'])?$filter['from_company_name']:'';
        $user_name = isset($filter['user_name']) && !empty($filter['user_name'])?$filter['user_name']:'';
        if(empty($order_sn)|| empty($from_company_id)|| empty($from_company_name) || empty($user_name)){
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "用户名，订单号，出库公司，不能为空！";
            $this -> return_msg = array();
            $this->display();
        }
        $model = Util::get_model("warehouse\WarehouseBillInfoSModel", [21]);
        $res = $model->createBillInfoSFromZhmd($order_sn, $from_company_id, $from_company_name, $user_name);
        //返回信息
        if($res['success']!=1){
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "操作失败！".$res['error'];
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $res;
            $this -> return_msg = "操作成功";
            $this->display();
        }
    }

    /**智慧门店生成销售退货单
    * 1/生成销售单
    * 2/改变货品仓储状态
    * 3/配货单状态变更 [数据库app_order 中的 base_order_info表中 delivery_status改为 5 已配货]
    * @param $create_user String 用户名称
    * @param $order_sn String 订单编号
    * @param $return_id ID 退款流水号
    * @param $company_id  退货公司ID
    * @param $order_goods  退货的商品
    */
    public function createBillInfoD()
    {
        //制单人
        if (isset($this->filter['create_user']) && !empty($this->filter['create_user'])) {
            $create_user = $this->filter['create_user'];
        } else {
            $create_user = 'SYSTEM';
        }
        //退货订单号
        if (isset($this->filter['order_sn']) && !empty($this->filter['order_sn'])) {
            $order_sn = $this->filter['order_sn'];
        } else {
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数order_sn或者为空";
            $this->return_msg = '';
            $this->display();
        }
        //退货流水ID
        if (isset($this->filter['return_id']) && !empty($this->filter['return_id'])) {
            $return_id = $this->filter['return_id'];
        } else {
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数return_id或者为空";
            $this->return_msg = '';
            $this->display();
        }
        //退货仓库ID
        if (isset($this->filter['company_id']) && !empty($this->filter['company_id'])) {
            $company_id = $this->filter['company_id'];
        } else {
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数warehouse_id或者为空";
            $this->return_msg = '';
            $this->display();
        }
        //退货商品
        if (isset($this->filter['order_goods']) && count($this->filter['order_goods'])) {
            $order_goods = $this->filter['order_goods'];
        } else {
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数order_goods或者为空";
            $this->return_msg = '';
            $this->display();
        }
        
        $this->createReturnGoodsBillD_Zhmd();
    }

    /**
         * 生成销售退货单  JUAN
         * order_sn：退货订单号
         * return_id 退货单号
         * order_goods二维数组 detail_id：明细ID，return_price：退款金额。
         */
        public function createReturnGoodsBillD_Zhmd() {

                $s_time = microtime();
                define("root_path", dirname(dirname(dirname(dirname(__FILE__)))));
                require_once(root_path . '/frame/init.php');
                $pdo = DB::cn(22)->db(); //pdo对象
                //退货订单号
                if (isset($this->filter['order_sn']) && !empty($this->filter['order_sn'])) {
                        $order_sn = $this->filter['order_sn'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数order_sn或者为空";
                        $this->return_msg = '';
                        $this->display();
                }
                //退货流水ID
                if (isset($this->filter['return_id']) && !empty($this->filter['return_id'])) {
                        $return_id = $this->filter['return_id'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数return_id或者为空";
                        $this->return_msg = '';
                        $this->display();
                }

                //制单人
        if (isset($this->filter['create_user']) && !empty($this->filter['create_user'])) {
            $create_user = $this->filter['create_user'];
        } else {
            $create_user = 'SYSTEM';
        }

                //退货仓库ID
                if (isset($this->filter['company_id']) && !empty($this->filter['company_id'])) {
                        $company_id = $this->filter['company_id'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数warehouse_id或者为空";
                        $this->return_msg = '';
                        $this->display();
                }

                $sql = "SELECT
                            `b`.id,
                            `a`.`company_name`,
                            `a`.`company_id`,
                            `b`.`name`
                        FROM
                            `warehouse_rel` AS `a`
                        LEFT JOIN `warehouse` AS `b` ON `a`.`warehouse_id` = `b`.`id`
                        WHERE
                            `a`.`company_id` = '".$company_id."'
                            and `b`.type = 9";
                $warehouse_info = $this->db->getRow($sql);

                if(empty($warehouse_info)){
                    $this->error = 1;
                    $this->return_sql = '';
                    $this->error_msg = "公司缺少退货库";
                    $this->return_msg = '';
                    $this->display();
                }

                //退货商品
                if (isset($this->filter['order_goods']) && count($this->filter['order_goods'])) {
                        $order_goods = $this->filter['order_goods'];
                } else {
                        $this->error = 1;
                        $this->return_sql = '';
                        $this->error_msg = "缺少参数order_goods或者为空";
                        $this->return_msg = '';
                        $this->display();
                }
                //查看此订单是否有存在的有效的销售单
                $sql = "SELECT `id` FROM warehouse_bill where `order_sn` = '" . $order_sn . "' AND `bill_status` = 2 AND `bill_type` = 'S'";
                $bill_id = $this->db->getOne($sql);
                if (!$bill_id) {
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = "此订单号没有有效的销售单，请检查并联系相关人员";
                        $this->return_msg = '';
                        $this->display();
                }
               
                $chengbenjia = 0; //总成本价
                $mingyijia = 0; //总名义成本价
                $tuihuojia = 0; //总退货价
                foreach ($order_goods as $key => $val) {

                    $goods_id = $val['goods_id'];
                    //util::L(var_export($val['goods_pay_price'],true), 'chaochao.txt');die;
                        /*$sql = "select g.goods_id from warehouse_goods as g,warehouse_bill_goods as bg where bg.goods_id = g.goods_id and order_goods_id = " . $val['detail_id'] . " and bg.bill_id = " . $bill_id;                 
                        $goods_id = $this->db->getOne($sql);
                        $sql1 = "select g.goods_id from warehouse_goods as g,warehouse_bill_goods as bg where bg.goods_id = g.goods_id and g.goods_id = " . $val['goods_id'] . " and bg.bill_id = " . $bill_id;
                        $goods_id1 = $this->db->getOne($sql1);
                        if (!$goods_id && !$goods_id1) {//没有找到关联的货号
                                $this->error = 1;
                                $this->return_sql = $sql;
                                $this->error_msg = "不是销售单中的货品";
                                $this->return_msg = '';
                                $this->display();
                        } else {*/
                                $sql = "select goods_id,goods_sn,goods_name,caizhi,jinzhong,zhushiyanse,zuanshidaxiao,put_in_type,is_on_sale,yuanshichengbenjia,chengbenjia,mingyichengben from warehouse_goods where goods_id = " . $goods_id;
                                $arr = $this->db->getRow($sql); //取出货品的其他信息
                                if ($arr['is_on_sale'] != '3') {//货品不是已销售状态不能做退货
                                        $this->error = 1;
                                        $this->return_sql = $sql;
                                        $this->error_msg = "货品不是已销售状态，不能退货。";
                                        $this->return_msg = '';
                                        $this->display();
                                }
                                $order_goods[$key] = array_merge($val, $arr);
                                $chengbenjia += $arr['yuanshichengbenjia'];
                                $mingyijia += $arr['mingyichengben'];
                                $tuihuojia += $val['goods_pay_price'];
                        //}
                }
                //util::L(var_export($tuihuojia,true), 'chaochao.txt');die;
                //写事务，生成销售退货单
                try {
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                        $pdo->beginTransaction(); //开启事务

                        $time = date('Y-m-d H:i:s');
                        //生成单号
                        $sql = 'SELECT `bill_no` FROM `warehouse_bill` WHERE `id` = (SELECT max(id) from warehouse_bill)';
                        $str = $this->db->getOne($sql);
                        $no = (substr($str, 1, 8) != date('Ymd', time())) ? 1 : intval(substr($str, 9)) + 1;
                       // $bill_no = 'D' . date('Ymd', time()) . str_pad($no, 5, "0", STR_PAD_LEFT);

                    /* $to_warehouse_id = 96;
                    $to_warehouse_name = '总公司后库';
                    $to_company_id = 58;
                    $to_company_name = '总公司';*/
                    $to_warehouse_id = $warehouse_info['id'];
                    $to_warehouse_name = $warehouse_info['name'];
                    $to_company_id = $warehouse_info['company_id'];
                    $to_company_name = $warehouse_info['company_name'];

                        $sql = "INSERT INTO `warehouse_bill`(`bill_no`, `bill_type`, `bill_status`, `order_sn`, `goods_num`, `to_warehouse_id`, `to_warehouse_name`, `to_company_id`, `to_company_name`, `from_company_id`, `from_company_name`, `bill_note`, `yuanshichengben`, `goods_total`, `shijia`, `check_user`, `check_time`, `create_user`, `create_time`) VALUES ('" . ''. "','D',2,'" . $order_sn . "'," . count($order_goods) . "," . $to_warehouse_id . ",'" . $to_warehouse_name . "'," . $to_company_id . ",'" . $to_company_name . "',0,null,'退款流水：" . $return_id . "'," . $chengbenjia . "," . $mingyijia . "," . $tuihuojia . ",'".$create_user."','".$time."','". $create_user ."','" . $time . "')";
                        $pdo->query($sql);
                        $_id = $pdo->lastInsertId();

                        $bill_id = substr($_id,-4);
                        $bill_no = 'D'.date('Ymd',time()).rand(100,999).str_pad($bill_id,4,"0",STR_PAD_LEFT);

                        $sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$_id}";
                        $pdo->query($sql);

                        $sql = "INSERT INTO `warehouse_bill_info_d`(`bill_id`, `return_sn`) VALUES (" . $_id . "," . $return_id . ")";
                        $pdo->query($sql);

                        $sql = "INSERT INTO `warehouse_bill_status`(`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES (" . $_id . ",'" . $bill_no . "',1,'" . $time . "','SYSTEM','" . Util::getClicentIp() . "')";
                        $pdo->query($sql);

                        foreach ($order_goods as $key => $val) {

                                $sql = "INSERT INTO `warehouse_bill_goods`(`bill_id`, `bill_no`, `bill_type`, `goods_id`, `goods_sn`, `goods_name`, `num`, `warehouse_id`, `caizhi`, `jinzhong`, `yanse`, `zuanshidaxiao`, `yuanshichengben`, `sale_price`, `shijia`, `in_warehouse_type`, `account`, `addtime`, `pandian_status`, `guiwei`) VALUES (" . $_id . ",'" . $bill_no . "','D'," . $val['goods_id'] . ",'" . $val['goods_sn'] . "','" . $val['goods_name'] . "',1," . $to_warehouse_id . ",'" . $val['caizhi'] . "'," . $val['jinzhong'] . ",'" . $val['zhushiyanse'] . "'," . $val['zuanshidaxiao'] . "," . $val['yuanshichengbenjia'] . "," . $val['mingyichengben'] . "," . $val['goods_pay_price'] . "," . $val['put_in_type'] . ",0,'" . $time . "',0,null)";

                                $pdo->query($sql);

                                //改变货品状态为库存
                                $sql = "UPDATE `warehouse_goods` SET `is_on_sale`= 2  WHERE goods_id = " . $val['goods_id'];
                                $pdo->query($sql);

                                $goods_id = $val['goods_id'];
                        }
                } catch (Exception $e) {//捕获异常
                        //print_r($e);exit;
                        util::L(var_export($e,true), 'chaochao.txt');
                        $pdo->rollback(); //事务回滚
                        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
                        $this->error = 1;
                        $this->return_sql = $sql;
                        $this->error_msg = "创建销售退货单失败";
                        $this->return_msg = 0;
                        $this->display();
                }
                $pdo->commit(); //如果没有异常，就提交事务
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
                // 记录日志
                $reponse_time = microtime() - $s_time;
                $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

                $this->error = 0;
                $this->return_sql = $sql;
                $this->return_msg = array('bill_no'=>$bill_no,'goods_id'=>$goods_id);
                $this->display();
        }

}/** END class **/
?>
