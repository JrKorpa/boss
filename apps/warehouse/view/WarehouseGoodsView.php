<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseGoodsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-28 18:06:10
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseGoodsView extends View
{
	protected $_id;
	protected $_goods_id;
	protected $_goods_sn;
	protected $_buchan_sn;
	protected $_order_goods_id;
	protected $_product_type;
	protected $_cat_type;
	protected $_is_on_sale;
	protected $_prc_id;
	protected $_prc_name;
	protected $_mo_sn;
	protected $_put_in_type;
	protected $_goods_name;
	protected $_company;
	protected $_warehouse;
	protected $_company_id;
	protected $_warehouse_id;
	protected $_caizhi;
	protected $_jinzhong;
	protected $_jinhao;
	protected $_zhushi;
	protected $_zhuchengsezhongjijia;
	protected $_zhuchengsemairudanjia;
	protected $_zhuchengsemairuchengben;
	protected $_zhuchengsejijiadanjia;
	protected $_zhushilishu;
	protected $_zuanshidaxiao;
	protected $_zhushizhongjijia;
	protected $_zhushiyanse;
	protected $_zhushijingdu;
	protected $_zhushimairudanjia;
	protected $_zhushimairuchengben;
	protected $_zhushijijiadanjia;
	protected $_zhushiqiegong;
	protected $_zhushixingzhuang;
	protected $_zhushibaohao;
	protected $_zhushiguige;
	protected $_fushi;
	protected $_fushilishu;
	protected $_fushizhong;
	protected $_fushizhongjijia;
	protected $_fushiyanse;
	protected $_fushijingdu;
	protected $_fushimairuchengben;
	protected $_fushimairudanjia;
	protected $_fushijijiadanjia;
	protected $_fushixingzhuang;
	protected $_fushibaohao;
	protected $_fushiguige;
	protected $_zongzhong;
	protected $_mairugongfeidanjia;
	protected $_mairugongfei;
	protected $_jijiagongfei;
	protected $_shoucun;
	protected $_ziyin;
	protected $_danjianchengben;
	protected $_peijianchengben;
	protected $_qitachengben;
	protected $_yuanshichengbenjia;
	protected $_chengbenjia;
	protected $_jijiachengben;
	protected $_jiajialv;
	protected $_kela_order_sn;
	protected $_zuixinlingshoujia;
	protected $_pinpai;
	protected $_changdu;
	protected $_zhengshuhao;
	protected $_zhengshuhao2;
	protected $_yanse;
	protected $_jingdu;
	protected $_peijianshuliang;
	protected $_guojizhengshu;
	protected $_zhengshuleibie;
	protected $_gemx_zhengshu;
	protected $_num;
	protected $_addtime;
	protected $_shi2;
	protected $_shi2lishu;
	protected $_shi2zhong;
	protected $_shi2zhongjijia;
	protected $_shi2mairudanjia;
	protected $_shi2mairuchengben;
	protected $_shi2jijiadanjia;
	protected $_qiegong;
	protected $_paoguang;
	protected $_duichen;
	protected $_yingguang;
	protected $_mingyichengben;
	protected $_xianzaixiaoshou;
	protected $_zuanshizhekou;
	protected $_guojibaojia;
	protected $_gongchangchengben;
	protected $_account;
	protected $_account_time;
	protected $_tuo_type;
	protected $_att1;
	protected $_att2;
	protected $_huopin_type;
	protected $_dia_sn;
	protected $_zhushipipeichengben;
	protected $_biaoqianjia;
	protected $_jietuoxiangkou;
	protected $_caigou_chengbenjia;
	protected $_box_sn;
	protected $_oldsys_id;
	protected $_old_set_w;
	protected $_weixiu_status;


	public function get_id(){return $this->_id;}
	public function get_goods_id(){return $this->_goods_id;}
	public function get_goods_sn(){return $this->_goods_sn;}
	public function get_buchan_sn(){return $this->_buchan_sn;}
	public function get_order_goods_id(){return $this->_order_goods_id;}
	public function get_product_type(){return $this->_product_type;}
	public function get_cat_type(){return $this->_cat_type;}
	public function get_is_on_sale(){return $this->_is_on_sale;}
	public function get_prc_id(){return $this->_prc_id;}
	public function get_prc_name(){return $this->_prc_name;}
	public function get_mo_sn(){return $this->_mo_sn;}
	public function get_put_in_type(){return $this->_put_in_type;}
	public function get_goods_name(){return $this->_goods_name;}
	public function get_company(){return $this->_company;}
	public function get_warehouse(){return $this->_warehouse;}
	public function get_company_id(){return $this->_company_id;}
	public function get_warehouse_id(){return $this->_warehouse_id;}
	public function get_caizhi(){return $this->_caizhi;}
	public function get_jinzhong(){return $this->_jinzhong;}
	public function get_jinhao(){return $this->_jinhao;}
	public function get_zhushi(){return $this->_zhushi;}
	public function get_zhuchengsezhongjijia(){return $this->_zhuchengsezhongjijia;}
	public function get_zhuchengsemairudanjia(){return $this->_zhuchengsemairudanjia;}
	public function get_zhuchengsemairuchengben(){return $this->_zhuchengsemairuchengben;}
	public function get_zhuchengsejijiadanjia(){return $this->_zhuchengsejijiadanjia;}
	public function get_zhushilishu(){return $this->_zhushilishu;}
	public function get_zuanshidaxiao(){return $this->_zuanshidaxiao;}
	public function get_zhushizhongjijia(){return $this->_zhushizhongjijia;}
	public function get_zhushiyanse(){return $this->_zhushiyanse;}
	public function get_zhushijingdu(){return $this->_zhushijingdu;}
	public function get_zhushimairudanjia(){return $this->_zhushimairudanjia;}
	public function get_zhushimairuchengben(){return $this->_zhushimairuchengben;}
	public function get_zhushijijiadanjia(){return $this->_zhushijijiadanjia;}
	public function get_zhushiqiegong(){return $this->_zhushiqiegong;}
	public function get_zhushixingzhuang(){return $this->_zhushixingzhuang;}
	public function get_zhushibaohao(){return $this->_zhushibaohao;}
	public function get_zhushiguige(){return $this->_zhushiguige;}
	public function get_fushi(){return $this->_fushi;}
	public function get_fushilishu(){return $this->_fushilishu;}
	public function get_fushizhong(){return $this->_fushizhong;}
	public function get_fushizhongjijia(){return $this->_fushizhongjijia;}
	public function get_fushiyanse(){return $this->_fushiyanse;}
	public function get_fushijingdu(){return $this->_fushijingdu;}
	public function get_fushimairuchengben(){return $this->_fushimairuchengben;}
	public function get_fushimairudanjia(){return $this->_fushimairudanjia;}
	public function get_fushijijiadanjia(){return $this->_fushijijiadanjia;}
	public function get_fushixingzhuang(){return $this->_fushixingzhuang;}
	public function get_fushibaohao(){return $this->_fushibaohao;}
	public function get_fushiguige(){return $this->_fushiguige;}
	public function get_zongzhong(){return $this->_zongzhong;}
	public function get_mairugongfeidanjia(){return $this->_mairugongfeidanjia;}
	public function get_mairugongfei(){return $this->_mairugongfei;}
	public function get_jijiagongfei(){return $this->_jijiagongfei;}
	public function get_shoucun(){return $this->_shoucun;}
	public function get_ziyin(){return $this->_ziyin;}
	public function get_danjianchengben(){return $this->_danjianchengben;}
	public function get_peijianchengben(){return $this->_peijianchengben;}
	public function get_qitachengben(){return $this->_qitachengben;}
	public function get_yuanshichengbenjia(){return $this->_yuanshichengbenjia;}
	public function get_chengbenjia(){return $this->_chengbenjia;}
	public function get_jijiachengben(){return $this->_jijiachengben;}
	public function get_jiajialv(){return $this->_jiajialv;}
	public function get_kela_order_sn(){return $this->_kela_order_sn;}
	public function get_zuixinlingshoujia(){return $this->_zuixinlingshoujia;}
	public function get_pinpai(){return $this->_pinpai;}
	public function get_changdu(){return $this->_changdu;}
	public function get_zhengshuhao(){return $this->_zhengshuhao;}
	public function get_zhengshuhao2(){return $this->_zhengshuhao2;}
	public function get_yanse(){return $this->_yanse;}
	public function get_jingdu(){return $this->_jingdu;}
	public function get_peijianshuliang(){return $this->_peijianshuliang;}
	public function get_guojizhengshu(){return $this->_guojizhengshu;}
	public function get_zhengshuleibie(){return $this->_zhengshuleibie;}
	public function get_gemx_zhengshu(){return $this->_gemx_zhengshu;}
	public function get_num(){return $this->_num;}
	public function get_addtime(){return $this->_addtime;}
	public function get_shi2(){return $this->_shi2;}
	public function get_shi2lishu(){return $this->_shi2lishu;}
	public function get_shi2zhong(){return $this->_shi2zhong;}
	public function get_shi2zhongjijia(){return $this->_shi2zhongjijia;}
	public function get_shi2mairudanjia(){return $this->_shi2mairudanjia;}
	public function get_shi2mairuchengben(){return $this->_shi2mairuchengben;}
	public function get_shi2jijiadanjia(){return $this->_shi2jijiadanjia;}
	public function get_qiegong(){return $this->_qiegong;}
	public function get_paoguang(){return $this->_paoguang;}
	public function get_duichen(){return $this->_duichen;}
	public function get_yingguang(){return $this->_yingguang;}
	public function get_mingyichengben(){return $this->_mingyichengben;}
	public function get_xianzaixiaoshou(){return $this->_xianzaixiaoshou;}
	public function get_zuanshizhekou(){return $this->_zuanshizhekou;}
	public function get_guojibaojia(){return $this->_guojibaojia;}
	public function get_gongchangchengben(){return $this->_gongchangchengben;}
	public function get_account(){return $this->_account;}
	public function get_account_time(){return $this->_account_time;}
	public function get_tuo_type(){return $this->_tuo_type;}
	public function get_att1(){return $this->_att1;}
	public function get_att2(){return $this->_att2;}
	public function get_huopin_type(){return $this->_huopin_type;}
	public function get_dia_sn(){return $this->_dia_sn;}
	public function get_zhushipipeichengben(){return $this->_zhushipipeichengben;}
	public function get_biaoqianjia(){return $this->_biaoqianjia;}
	public function get_jietuoxiangkou(){return $this->_jietuoxiangkou;}
	public function get_caigou_chengbenjia(){return $this->_caigou_chengbenjia;}
	public function get_box_sn(){return $this->_box_sn;}
	public function get_oldsys_id(){return $this->_oldsys_id;}
	public function get_old_set_w(){return $this->_old_set_w;}
	public function get_weixiu_status(){return $this->_weixiu_status;}

}
?>