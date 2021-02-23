<?php
/**
 *  -------------------------------------------------
 *   @file		: UpdateQudaoBumenController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 11:04:15
 *   @update	:
 *
 *  -------------------------------------------------
 */
class GoodsStatusBatchReportController extends Controller
{
	protected $smartyDebugEnabled = true;
    protected $whitelist = array('get_goods_batch');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('goods_status_batch_report.html');
	}
	
	/**
	 *	get_goods_batch
	 */
	public function get_goods_batch ($params)
	{
        ini_set('memory_limit','-1');
        set_time_limit(0);
        $res = array('error'=>1,'msg'=>'');
		$goods_sn = _Post::getString('goods_sn');
        $goods_status = array(1=>'收货中',2=>'库存',3=>'已销售',4=>'盘点中',5=>'调拨中',6=>'损益中',7=>'已报损',8=>'返厂中',9=>'已返厂',10=>'销售中',11=>'退货中',12=>'作废');

        $goods_sn_arr = array();
        $goods_sn = trim(strtoupper($goods_sn));
        $goods_sn = str_replace(' ',',',$goods_sn);
        $goods_sn = str_replace('，',',',$goods_sn);
        $goods_sn = str_replace(array("\r\n", "\r", "\n"),',',$goods_sn);
        $goods_sn_arr = explode(",", $goods_sn);

        $goods_sn_arr=implode("','",$goods_sn_arr);
        $goodsModel = new AppGoodsListModel(55);
        $affirm_goods_sn = $goodsModel->getAllGoodsInfoById($goods_sn_arr);

        $warehousemodel = new WarehouseModel(55);
        $data = array();
        $goods_ids = array();
        foreach ($affirm_goods_sn as $key => $goods_info) {
            if(in_array($goods_info['goods_id'], $goods_ids)){
                    continue;
            }
            array_push($goods_ids, $goods_info['goods_id']);
            # code...
     	    //获取最新采购成本
            // $caigou_chengbenjia = $warehousemodel->getNewestCaigouchengbenByGoodsId($goods_info['goods_id']);
            //获取最新的调拨单号
            $diaobo_bill = $warehousemodel->getNewestDiaoboBillByGoodsId($goods_info['goods_id']);

            $data[$key]['goods_sn'] = isset($goods_info['goods_sn']) && !empty($goods_info['goods_sn']) ? $goods_info['goods_sn'] : '';
            $data[$key]['goods_id'] = isset($goods_info['goods_id']) && !empty($goods_info['goods_id']) ? $goods_info['goods_id'] : '';
            $data[$key]['goods_name'] = isset($goods_info['goods_name']) && !empty($goods_info['goods_name']) ? $goods_info['goods_name'] : '';
            $data[$key]['prc_name'] = isset($goods_info['prc_name']) && !empty($goods_info['prc_name']) ? $goods_info['prc_name'] : '';
            $data[$key]['order_sn'] = isset($goods_info['order_sn']) && !empty($goods_info['order_sn']) ? $goods_info['order_sn'] : '';
            $data[$key]['warehouse'] = isset($goods_info['warehouse']) && !empty($goods_info['warehouse']) ? $goods_info['warehouse'] : '';
            $data[$key]['is_on_sale'] = isset($goods_info['is_on_sale']) && !empty($goods_info['is_on_sale']) ? $goods_info['is_on_sale'] : '';
            if($data[$key]['is_on_sale'] != ''){
                $data[$key]['is_on_sale'] = $goods_status[$data[$key]['is_on_sale']];
            }
            $data[$key]['mingyichengben'] = isset($goods_info['mingyichengben']) && !empty($goods_info['mingyichengben']) ? $goods_info['mingyichengben'] : '';
            $data[$key]['chengbenjia'] = isset($goods_info['chengbenjia']) && !empty($goods_info['chengbenjia']) ? $goods_info['chengbenjia'] : '';
            $data[$key]['goods_price'] = isset($goods_info['goods_price']) && !empty($goods_info['goods_price']) ? $goods_info['goods_price'] : '';
            $data[$key]['create_time'] = isset($goods_info['create_time']) && !empty($goods_info['create_time']) ? $goods_info['create_time'] : '';


            $data[$key]['caizhi'] = isset($goods_info['caizhi']) && !empty($goods_info['caizhi']) ? $goods_info['caizhi'] : '';
            $data[$key]['jinzhong'] = isset($goods_info['jinzhong']) && !empty($goods_info['jinzhong']) ? $goods_info['jinzhong'] : '';
            $data[$key]['shoucun'] = isset($goods_info['shoucun']) && !empty($goods_info['shoucun']) ? $goods_info['shoucun'] : '';
            $data[$key]['zhushi'] = isset($goods_info['zhushi']) && !empty($goods_info['zhushi']) ? $goods_info['zhushi'] : '';
            $data[$key]['zuanshidaxiao'] = isset($goods_info['zuanshidaxiao']) && !empty($goods_info['zuanshidaxiao']) ? $goods_info['zuanshidaxiao'] : '';
            $data[$key]['zhushiyanse'] = isset($goods_info['zhushiyanse']) && !empty($goods_info['zhushiyanse']) ? $goods_info['zhushiyanse'] : '';
            $data[$key]['zhushijingdu'] = isset($goods_info['zhushijingdu']) && !empty($goods_info['zhushijingdu']) ? $goods_info['zhushijingdu'] : '';
            $data[$key]['zhengshuhao'] = isset($goods_info['zhengshuhao']) && !empty($goods_info['zhengshuhao']) ? $goods_info['zhengshuhao'] : '';
            $data[$key]['zhengshuhao2'] = isset($goods_info['zhengshuhao2']) && !empty($goods_info['zhengshuhao2']) ? $goods_info['zhengshuhao2'] : '';
            $data[$key]['zhengshuleibie'] = isset($goods_info['zhengshuleibie']) && !empty($goods_info['zhengshuleibie']) ? $goods_info['zhengshuleibie'] : '';
            $data[$key]['box_sn'] = isset($goods_info['box_sn']) && !empty($goods_info['box_sn']) ? $goods_info['box_sn'] : '';
            $data[$key]['diaobo_bill'] = !empty($diaobo_bill) ? $diaobo_bill : '';
            $data[$key]['pinpai'] = isset($goods_info['pinpai']) && !empty($goods_info['pinpai']) ? $goods_info['pinpai'] : '';
            $data[$key]['supplier_code'] = isset($goods_info['supplier_code']) && !empty($goods_info['supplier_code']) ? $goods_info['supplier_code'] : '';

        }

        $title = array(
                '款号',
                '货号',
        		'名称',
                '供货商',
                '订单号',
                '仓库',
                '状态',
                '名义成本',
                '最新采购成本',
                '销售价',
                '销售时间',
                '材质',
                '金重',
                '手寸',
                '主石',
                '主石大小',
                '主石颜色',
                '主石净度',
                '证书号',
                '证书号2',
                '证书类型',
                '柜位',
                '调拨单号',
                '品牌',
                '供应商货号'
                );
        Util::downloadCsv("货品状态文档",$title,$data);
	}
}

?>
