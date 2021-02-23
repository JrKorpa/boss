<?php

/**
 *  -------------------------------------------------
 *   @file		: ShopCfgController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-30 10:14:28
 *   @update	:
 *  -------------------------------------------------
 */
class ShopCfgController extends CommonController {

        protected $smartyDebugEnabled = true;
        protected $whitelist = array('search');

        /**
         * 	index，搜索框
         */
        public function index($params) {
                $this->render('shop_cfg_search_form.html', array('bar' => Auth::getBar()));
        }

        /**
         * 	search，列表
         */
        public function search($params) {
                $args = array(
                        'mod' => _Request::get("mod"),
                        'con' => substr(__CLASS__, 0, -10),
                        'act' => __FUNCTION__,
                        'shop_name' => _Request::get("shop_name"),
                        'shop_type' => _Request::getInt('shop_type'),
                        'shop_tel' => trim(_Request::get("shop_tel")),
                        'shop_address' => trim(_Request::get("shop_address")),
                        'is_delete' => 0,
                		'down_info'	=> _Request::get('down_info')?_Request::get('down_info'):'',
                );
                $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
                $where = array();
                $where['shop_name'] = $args['shop_name'];
                $where['is_delete'] = $args['is_delete'];
                $where['shop_type'] = $args['shop_type'];
                $where['shop_tel'] = $args['shop_tel'];
                $where['shop_address'] = $args['shop_address'];
                $model = new ShopCfgModel(1);
                /** 2015-12-30 zzm boss-1029 **/
        		if($args['down_info']=='down_info'){
					$data = $model->getUinfos();
					$this->download($data);
					exit;
        		}
                $data = $model->pageList($where, $page, 10, false);
                $pageData = $data;
                $pageData['filter'] = $args;
                $pageData['jsFuncs'] = 'shop_cfg_search_page';
                $this->render('shop_cfg_search_list.html', array(
                        'pa' => Util::page($pageData),
                        'page_list' => $data
                ));
        }

        /**
         * 	add，渲染添加页面
         */
        public function add() {
                $result = array('success' => 0, 'error' => '');
                $reginModel = new RegionModel(1);
                $countdata = $reginModel->getRegionType(0);
                $result['content'] = $this->fetch('shop_cfg_info.html', array(
                        'view' => new ShopCfgView(new ShopCfgModel(1)),
                        'count' => $countdata,
                ));
                $result['title'] = '添加';
                Util::jsonExit($result);
        }

        /**
         * 	edit，渲染修改页面
         */
        public function edit($params) {
                $id = intval($params["id"]);
                $result = array('success' => 0, 'error' => '');
                $reginModel = new RegionModel(1);
                $countdata = $reginModel->getRegionType(0);
                $result['content'] = $this->fetch('shop_cfg_info.html', array(
                        'view' => new ShopCfgView(new ShopCfgModel($id, 1)), 'count' => $countdata,
                ));
                $result['title'] = '编辑';
                Util::jsonExit($result);
        }

        /**
         * 	c_edit，渲染修改页面
         */
        public function c_edit($params) {
                $id = intval($params["id"]);
                $result = array('success' => 0, 'error' => '');
                $reginModel = new RegionModel(1);
                $countdata = $reginModel->getRegionType(0);
                $result['content'] = $this->fetch('shop_cfg_c_info.html', array(
                        'view' => new ShopCfgView(new ShopCfgModel($id, 1)), 'count' => $countdata,
                ));
                $result['title'] = '经销商信息';
                Util::jsonExit($result);
        }

        /**
         * 	show，渲染查看页面
         */
        public function show($params) {
                $id = intval($params["id"]);

                $model = new ShopCfgModel(1);
                $data = $model->getUinfo($id);

                $this->render('shop_cfg_show.html', array(
                        'd' => $data,
                ));
        }

        /**
         * 	insert，信息入库
         */
        public function insert($params) {
                $result = array('success' => 0, 'error' => '');

                $shop_name = _Post::getString('shop_name');
                $short_name = _Post::getString('short_name');
                $official_webiste_show = _Post::getString('official_webiste_show');
                $shop_type = _Post::getInt('shop_type');
                $shop_phone = _Post::getString('shop_phone');
                $country_id = _Post::getInt('country_id');
                $province_id = _Post::getInt('province_id');
                $city_id = _Post::getInt('city_id');
                $regional_id = _Post::getInt('regional_id');
                $shop_address = _Post::getString('shop_address');
                $shop_time = _Post::getString('shop_time');
                $second_url = _Post::getString('second_url');
                $shop_traffic = _Post::getString('shop_traffic');
                $shop_dec = _Post::getString('shop_dec');
                $area = _Post::getString('area');
                $shop_status = _Request::getString('shop_status');
                $start_shop_time = _Post::getString('start_shop_time');
                $baidu_maps = _Post::getString('baidu_maps');
                $shopowner = _Post::getString('shopowner');
                $shopowner_tel = _Post::getString('shopowner_tel');
                $shopowner_mail = _Post::getString('shopowner_mail');
                $order = time();
                $create_user = $_SESSION['userId'];
                $create_time = time();

                $olddo = array();
                $newdo = array();
                $newdo['shop_name'] = $shop_name;
                $newdo['shop_type'] = $shop_type;
                $newdo['short_name'] = $short_name;
                $newdo['official_webiste_show'] = $official_webiste_show;
                $newdo['shop_phone'] = $shop_phone;
                $newdo['country_id'] = $country_id;
                $newdo['province_id'] = $province_id;
                $newdo['city_id'] = $city_id;
                $newdo['regional_id'] = $regional_id;
                $newdo['shop_address'] = $shop_address;
                $newdo['shop_time'] = $shop_time;
                $newdo['shop_traffic'] = $shop_traffic;
                $newdo['shop_dec'] = $shop_dec;
                $newdo['second_url'] = $second_url;
                $newdo['order'] = $order;
                $newdo['create_user'] = $create_user;
                $newdo['create_time'] = $create_time;
                $newdo['area'] = $area;
                $newdo['shop_status'] = $shop_status;
                $newdo['start_shop_time'] = $start_shop_time;
                $newdo['baidu_maps'] = $baidu_maps;
                $newdo['shopowner'] = $shopowner;
                $newdo['shopowner_tel'] = $shopowner_tel;
                $newdo['shopowner_mail'] = $shopowner_mail;



                $newmodel = new ShopCfgModel(2);
                $res = $newmodel->saveData($newdo, $olddo);
                if ($res !== false) {
                        $result['success'] = 1;
                } else {
                        $result['error'] = '添加失败';
                }
                Util::jsonExit($result);
        }

        /**
         * 	update，更新信息
         */
        public function update($params) {
                $result = array('success' => 0, 'error' => '');
                $id = _Post::getInt('id');

                $shop_name = _Post::getString('shop_name');
                $short_name = _Post::getString('short_name');
                $official_webiste_show = _Post::getString('official_webiste_show');
                $shop_type = _Post::getInt('shop_type');
                $shop_phone = _Post::getString('shop_phone');
                $country_id = _Post::getInt('country_id');
                $province_id = _Post::getInt('province_id');
                $city_id = _Post::getInt('city_id');
                $regional_id = _Post::getInt('regional_id');
                $shop_address = _Post::getString('shop_address');
                $shop_time = _Post::getString('shop_time');
                $second_url = _Post::getString('second_url');
                $shop_traffic = _Post::getString('shop_traffic');
                $shop_dec = _Post::getString('shop_dec');
                $area = _Post::getString('area');
                $shop_status = _Request::getString('shop_status');
                $start_shop_time = _Post::getString('start_shop_time');
                $baidu_maps = _Post::getString('baidu_maps');
                $shopowner = _Post::getString('shopowner');
                $shopowner_tel = _Post::getString('shopowner_tel');
                $shopowner_mail = _Post::getString('shopowner_mail');

                $newmodel = new ShopCfgModel($id, 2);

                $olddo = $newmodel->getDataObject();
                $newdo = array();

                $newdo['id'] = $id;
                $newdo['shop_name'] = $shop_name;
                $newdo['shop_type'] = $shop_type;
                $newdo['short_name'] = $short_name;
                $newdo['official_webiste_show'] = $official_webiste_show;
                $newdo['shop_phone'] = $shop_phone;
                $newdo['country_id'] = $country_id;
                $newdo['province_id'] = $province_id;
                $newdo['city_id'] = $city_id;
                $newdo['regional_id'] = $regional_id;
                $newdo['shop_address'] = $shop_address;
                $newdo['shop_time'] = $shop_time;
                $newdo['shop_traffic'] = $shop_traffic;
                $newdo['shop_dec'] = $shop_dec;
                $newdo['second_url'] = $second_url;
                $newdo['area'] = $area;
                $newdo['shop_status'] = $shop_status;
                $newdo['start_shop_time'] = $start_shop_time;
                $newdo['baidu_maps'] = $baidu_maps;
                $newdo['shopowner'] = $shopowner;
                $newdo['shopowner_tel'] = $shopowner_tel;
                $newdo['shopowner_mail'] = $shopowner_mail;


                $res = $newmodel->saveData($newdo, $olddo);
                if ($res !== false) {
                        $result['success'] = 1;
                        
                        //修改日志记录
                        $dataLog['pkdata'] = array('id'=>$id);
                        $dataLog['newdata'] = $newdo;
                        $dataLog['olddata'] = $olddo;
                        $dataLog['fields']  = $newmodel->getFieldsDefine();
                        $this->operationLog("update",$dataLog);
                        
                        if ($olddo['shop_name'] != $newdo['shop_name'] || $olddo['shop_address'] != $newdo['shop_address']) {
                        	//AsyncDelegate::dispatch('order', array('event' => 'shop_addr_changed', 'old_name' => $olddo['shop_name'], 'new_name' => $newdo['shop_name'], 'old_addr' => $olddo['shop_address'], 'new_addr' => $newdo['shop_address']));
                        }
                        
                } else {
                        $result['error'] = '修改失败';
                }
                Util::jsonExit($result);
        }

        /**
         * 	c_update，经销商更新信息
         */
        public function c_update($params) {
                $result = array('success' => 0, 'error' => '');
                $id = _Post::getInt('id');

                $dealer_name = _Post::getString('dealer_name');
                $join_type = _Post::getString('join_type');
                $shop_responsible_name = _Post::getString('shop_responsible_name');
                $shop_responsible_tel = _Post::getInt('shop_responsible_tel');
                $shop_responsible_mail = _Post::getString('shop_responsible_mail');
                $contract_status = _Request::getString('contract_status');
                $contract_start_time = _Post::getString('contract_start_time');
                $contract_end_time = _Post::getString('contract_end_time');
                $trademark_use_fee = _Post::getString('trademark_use_fee');
                $credit_guarantee_fee = _Post::getString('credit_guarantee_fee');
                $security_user = _Post::getString('security_user');
                $diamond_gem_fee = _Post::getString('diamond_gem_fee');
                $su_jin_fee = _Request::getString('su_jin_fee');
                $gia_diamond_fee = _Post::getString('gia_diamond_fee');
                $other_diamond_fee = _Post::getString('other_diamond_fee');
                $stock_index = _Post::getString('stock_index');
                $development_index = _Post::getString('development_index');
                $start_shop_time = _Post::getString('start_shop_time');
                $regional_manager = _Post::getString('regional_manager');
                $remarks = _Post::getString('remarks');

                $newmodel = new ShopCfgModel($id, 2);

                $olddo = $newmodel->getDataObject();
                $newdo = array();

                $newdo['id'] = $id;
                $newdo['dealer_name'] = $dealer_name;
                $newdo['join_type'] = $join_type;
                $newdo['shop_responsible_name'] = $shop_responsible_name;
                $newdo['shop_responsible_tel'] = $shop_responsible_tel;
                $newdo['shop_responsible_mail'] = $shop_responsible_mail;
                $newdo['contract_status'] = $contract_status;
                $newdo['contract_start_time'] = $contract_start_time;
                $newdo['contract_end_time'] = $contract_end_time;
                $newdo['trademark_use_fee'] = $trademark_use_fee;
                $newdo['credit_guarantee_fee'] = $credit_guarantee_fee;
                $newdo['security_user'] = $security_user;
                $newdo['diamond_gem_fee'] = $diamond_gem_fee;
                $newdo['su_jin_fee'] = $su_jin_fee;
                $newdo['gia_diamond_fee'] = $gia_diamond_fee;
                $newdo['other_diamond_fee'] = $other_diamond_fee;
                $newdo['stock_index'] = $stock_index;
                $newdo['development_index'] = $development_index;
                $newdo['start_shop_time'] = $start_shop_time;
                $newdo['regional_manager'] = $regional_manager;
                $newdo['remarks'] = $remarks;


                $res = $newmodel->saveData($newdo, $olddo);
                if ($res !== false) {
                        $result['success'] = 1;
                        
                        //修改日志记录
                        $dataLog['pkdata'] = array('id'=>$id);
                        $dataLog['newdata'] = $newdo;
                        $dataLog['olddata'] = $olddo;
                        $dataLog['fields']  = $newmodel->getFieldsDefine();
                        $this->operationLog("update",$dataLog);
                        
                } else {
                        $result['error'] = '修改失败';
                }
                Util::jsonExit($result);
        }

        /**
         * 	delete，删除
         */
        public function delete($params) {
                $result = array('success' => 0, 'error' => '');
                $id = intval($params['id']);
                $model = new ShopCfgModel($id, 2);
                $do = $model->getDataObject();
                if ($do['is_delete'] == 1) {
                        $result['success'] = 1;
                        Util::jsonExit($result);
                }
                $model->setValue('is_delete', 1);
                $res = $model->save(true);
                if ($res !== false) {
                        $result['success'] = 1;
                        //日志记录
                        $dataLog['pkdata'] = array('id'=>$id);
                        $this->operationLog("delete",$dataLog);
                } else {
                        $result['error'] = "删除失败";
                }
                Util::jsonExit($result);
        }

        public function moveup() {
                $result = array('success' => 0, 'error' => '');
                $id = _Post::getInt('id');
                $model = new ShopCfgModel($id, 2);
                $res = $model->move($id);

                if ($res == 1) {
                        $result['success'] = 1;
                } else if ($res == 3) {
                        $result['error'] = "已经是第一个了";
                } else {
                        $result['error'] = "移动失败";
                }
                Util::jsonExit($result);
        }

        public function movedown() {
                $result = array('success' => 0, 'error' => '');
                $id = _Post::getInt('id');
                $model = new ShopCfgModel($id, 2);
                $res = $model->move($id, false);

                if ($res == 1) {
                        $result['success'] = 1;
                } else if ($res == 3) {
                        $result['error'] = "已经是最后一个了";
                } else {
                        $result['error'] = "移动失败";
                }
                Util::jsonExit($result);
        }

        public function getProvince() {
                $count_id = _Post::getInt('count');
                $reginModel = new RegionModel(1);
                $provincedata = $reginModel->getRegion($count_id);
                $res = $this->fetch('province_option.html', array('provincedata' => $provincedata));
                echo $res;
        }
        
        //导出 2015-12-30 zzm boss-1029
        public function download($data){
        	$dir = KELA_ROOT."/apps/warehouse/tmp/";
            $dh=opendir($dir);
            while ($file=readdir($dh)) {
                if($file!="." && $file!="..") {
                    $fullpath=$dir."/".$file;
                    if(!is_dir($fullpath)) {
                        unlink($fullpath);
                    }
                }
            }
            
			$down = $data;
            $path = '/frame/PHPExcel/PHPExcel.php';
            $pathIo = '/frame/PHPExcel/PHPExcel/IOFactory.php';
            include_once(KELA_ROOT.$path);
            include_once(KELA_ROOT.$pathIo);
		

            // 创建一个处理对象实例
            $objPHPExcel = new PHPExcel();
            // 创建文件格式写入对象实例, uncomment
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel); // 用于其他版本格式

            // 创建一个表
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('A1', '体验店名称');
            $objPHPExcel->getActiveSheet()->setCellValue('B1', '简称');
            $objPHPExcel->getActiveSheet()->setCellValue('C1', '体验店类别');
            $objPHPExcel->getActiveSheet()->setCellValue('D1', '体验店地址');
            $objPHPExcel->getActiveSheet()->setCellValue('E1', '店长');
            $objPHPExcel->getActiveSheet()->setCellValue('F1', '店长联系电话');
            $objPHPExcel->getActiveSheet()->setCellValue('G1', '店长邮箱');
            $objPHPExcel->getActiveSheet()->setCellValue('H1', '营业时间');
            $objPHPExcel->getActiveSheet()->setCellValue('I1', '快捷交通路线');
            $objPHPExcel->getActiveSheet()->setCellValue('J1', '体验店的二级域名');
            $objPHPExcel->getActiveSheet()->setCellValue('K1', '百度地图坐标');
            $objPHPExcel->getActiveSheet()->setCellValue('L1', '体验店介绍');
            $objPHPExcel->getActiveSheet()->setCellValue('M1', '经销商公司名称');
            $objPHPExcel->getActiveSheet()->setCellValue('N1', '开店日期');
            $objPHPExcel->getActiveSheet()->setCellValue('O1', '区域经理');
            $objPHPExcel->getActiveSheet()->setCellValue('P1', '加盟类型');
            $objPHPExcel->getActiveSheet()->setCellValue('Q1', '店铺负责人');
            $objPHPExcel->getActiveSheet()->setCellValue('R1', '负责人联系电话');
            $objPHPExcel->getActiveSheet()->setCellValue('S1', '负责人邮箱');
            $objPHPExcel->getActiveSheet()->setCellValue('T1', '合同状态');
            $objPHPExcel->getActiveSheet()->setCellValue('U1', '合同开始日期');
            $objPHPExcel->getActiveSheet()->setCellValue('V1', '合同结束日期');
            $objPHPExcel->getActiveSheet()->setCellValue('W1', '商标使用费');
            $objPHPExcel->getActiveSheet()->setCellValue('X1', '授信及担保额度');
            $objPHPExcel->getActiveSheet()->setCellValue('Y1', '担保人');
            $objPHPExcel->getActiveSheet()->setCellValue('Z1', '钻石及宝石管理费');
            $objPHPExcel->getActiveSheet()->setCellValue('AA1', '素金类管理费');
            $objPHPExcel->getActiveSheet()->setCellValue('AB1', 'GIA裸钻管理费');
            $objPHPExcel->getActiveSheet()->setCellValue('AC1', '其他裸钻管理费');
            $objPHPExcel->getActiveSheet()->setCellValue('AD1', '进货指标');
            $objPHPExcel->getActiveSheet()->setCellValue('AE1', '拓展指标');
            $objPHPExcel->getActiveSheet()->setCellValue('AF1', '备注');
            
            $dd =new DictModel(1);
            
            $i = 1;
            foreach ($down as $v) {

                $i=$i+1;

                // 设置高度
                $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(20);
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $v['shop_name']);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $v['short_name']);
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $dd->getEnum('shop.type',$v['shop_type']));
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $v['shop_address']);
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $v['shopowner']);
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $v['shopowner_tel']);
                $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $v['shopowner_mail']);
                $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $v['shop_time']);
                $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $v['shop_traffic']);
                $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $v['second_url']);
                $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $v['baidu_maps']);
                $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $v['shop_dec']);
                $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $v['dealer_name']);
                $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $v['start_shop_time']);
                $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $v['regional_manager']);
                $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $v['join_type']);
                $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $v['shop_responsible_name']);
                $objPHPExcel->getActiveSheet()->setCellValue('R'.$i, $v['shop_responsible_tel']);
                $objPHPExcel->getActiveSheet()->setCellValue('S'.$i, $v['shop_responsible_mail']);
                $objPHPExcel->getActiveSheet()->setCellValue('T'.$i, $v['contract_status']);
                $objPHPExcel->getActiveSheet()->setCellValue('U'.$i, $v['contract_start_time']);
                $objPHPExcel->getActiveSheet()->setCellValue('V'.$i, $v['contract_end_time']);
                $objPHPExcel->getActiveSheet()->setCellValue('W'.$i, $v['trademark_use_fee']);
                $objPHPExcel->getActiveSheet()->setCellValue('X'.$i, $v['credit_guarantee_fee']);
                $objPHPExcel->getActiveSheet()->setCellValue('Y'.$i, $v['security_user']);
                $objPHPExcel->getActiveSheet()->setCellValue('Z'.$i, $v['diamond_gem_fee']);
                $objPHPExcel->getActiveSheet()->setCellValue('AA'.$i, $v['su_jin_fee']);
                $objPHPExcel->getActiveSheet()->setCellValue('AB'.$i, $v['gia_diamond_fee']);
                $objPHPExcel->getActiveSheet()->setCellValue('AC'.$i, $v['other_diamond_fee']);
                $objPHPExcel->getActiveSheet()->setCellValue('AD'.$i, $v['stock_index']);
                $objPHPExcel->getActiveSheet()->setCellValue('AE'.$i, $v['development_index']);
                $objPHPExcel->getActiveSheet()->setCellValue('AF'.$i, $v['remarks']);
            }

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(17);

            // 设置工作表的第一项表
            $objPHPExcel->setActiveSheetIndex(0);

            // 重命名表
            $objPHPExcel->getActiveSheet()->setTitle("体验店信息");

            $ymd = date("Ymd_His", time()+8*60*60);
            include_once(KELA_ROOT.$pathIo);
            $outputFileName = $ymd.'.xls';
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header('Content-Disposition:inline;filename="'.$outputFileName.'"');
            header("Content-Transfer-Encoding: binary");
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: no-cache");
            $objWriter->save('php://output');
            exit;
        }
        

}

?>