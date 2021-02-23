<?php
/**
 *  -------------------------------------------------
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class StyleModel extends SelfModel
{
    protected $db;
	function __construct ($strConn="")
	{
		parent::__construct($strConn);
	}	
	
	//获取款式库商品图片
	public function getAppStyleGalleryRow($where=""){
	    $sql ="select * from front.app_style_gallery where {$where} order by image_place asc";
	    return $this->db()->getRow($sql);
	}
	//根据款号获取商品名称列表
	function getStyleNameListByStyleSn($ids)
	{   
	    $sql = "select style_sn,style_name from front.base_style_info ";
	    if(is_array($ids)){
            $sql.="where style_sn in('".implode("','",$ids)."')";
	    }else{
	        $sql.="where style_sn ='".$ids."'";
	    }	        
	    return $this->db()->getAll($sql);
	}
	//根据款号获取商品名称
	function getStyleNameByStyleSn($style_sn){
	    $sql = "select style_name from front.base_style_info where style_sn='{$style_sn}'";
	    return $this->db()->getOne($sql);
	}

	/*
	*查找款号是否在款式库存在
	*
	*/
	public function getChengPinByStyle_sn($style_sn){
		$sql = "select style_id from front.base_style_info where style_sn='".$style_sn."' limit 1;";
		return	$this->db()->getOne($sql);
	}

    /**
     * 根据款号查询       主石，副石信息
     * @param unknown $style_sn 款号
     * @return Ambigous <multitype:, multitype:string Ambigous <string, unknown> Ambigous <string, unknown, mixed> unknown >
     */
	public function getStyleStoneByStyleSn($style_sn){

        $shape_arr = array(1=>"垫形",2=>"公主方",3=>"祖母绿",4=>"心形",5=>"蛋形",6=>"椭圆形",7=>"橄榄形",8=>"三角形",9=>"水滴形",10=>"长方形",11=>"圆形",12=>"梨形",13=>"马眼形");
	    $stonecat_arr = array("0"=>"无","1"=>"圆钻","2"=>"异形钻","3"=>"珍珠","4"=>"翡翠","5"=>"红宝石","6"=>"蓝宝石","7"=>"和田玉","8"=>"水晶","9"=>"珍珠贝","10"=>"碧玺","11"=>"玛瑙","12"=>"月光石","13"=>"托帕石","14"=>"石榴石","15"=>"绿松石","16"=>"芙蓉石","17"=>"祖母绿","18"=>"贝壳","19"=>"橄榄石","20"=>"彩钻","21"=>"葡萄石","22"=>"海蓝宝","23"=>"坦桑石","24"=>"粉红宝","25"=>"沙佛莱","26"=>"粉红蓝宝石");
	    $color_arr = array("1" =>"F","2" =>"G","3" =>"H","4" =>"I","8" =>"I-J","5" =>"J","6" =>"K","9" =>"K-L","7" =>"L","10" =>"白","11" =>"M","12" =>"<N","13" =>"N","14" =>"D","15" =>"E");
	    $clarity_arr = array("1"=>"IF","2" => "VVS","3" => "VVS1","4" =>"VVS2","5" =>"VS","6" =>"VS1","7" =>"VS2","8" =>"SI","9" =>"SI1","10" =>"SI2","11" =>"I1","12" =>"I2","13" =>"VSN","14" =>"不分级");

	    $sql = "select a.stone_position,a.stone_cat,a.stone_attr from front.rel_style_stone a inner join front.base_style_info b on a.style_id=b.style_id where b.style_sn='{$style_sn}'";
	    $data = $this->db()->getAll($sql);

	    $stoneList = array();
	    foreach($data as $vo){
	        $stone = array();
	        $stone_postion = $vo['stone_position'];	        
	        $keyName = $vo['stone_cat']==2?'zhushi':'fushi';
	        //stone_cat=1 圆钻 圆形   stone_cat=2 异形钻 对应形状
	        $stoneAttr = unserialize($vo['stone_attr']);
	        if($vo['stone_cat']==1){
	            $shape_name = "圆形";	
	        }else{
	            $shape_id = isset($stoneAttr['shape_fushi'])?$stoneAttr['shape_fushi']:'';
	            $shape_id = isset($stoneAttr['shape_zhushi'])?$stoneAttr['shape_zhushi']:$shape_id;
	            $shape_name = isset($shape_arr[$shape_id])?$shape_arr[$shape_id]:$shape_id;
	        }
	        $color_id = isset($stoneAttr['color_fushi'])?$stoneAttr['color_fushi']:'';
	        $color_id = isset($stoneAttr['color_zhushi'])?$stoneAttr['color_zhushi']:$color_id;
	        $color = isset($color_arr[$color_id])?$color_arr[$color_id]:$color_id;
	         
	        $clarity_id = isset($stoneAttr['clarity_fushi'])?$stoneAttr['clarity_fushi']:'';
	        $clarity_id = isset($stoneAttr['clarity_zhushi'])?$stoneAttr['clarity_zhushi']:$clarity_id;
	        $clarity = isset($clarity_arr[$clarity_id])?$clarity_arr[$clarity_id]:$clarity_id;
    	    
    	    if(isset($stonecat_arr[$vo['stone_cat']])){
    	        $stone_cat = $stonecat_arr[$vo['stone_cat']];
    	    }else{
    	        $stone_cat = '无';
    	    }
    	    $stone['stone_postion'] = $stone_postion;//石头位置
    	    $stone['stone_cat'] = $stone_cat;//石头类型
    	    $stone['shape'] = $shape_name;//石头形状
    	    $stone['color'] = $color;//石头形状
    	    $stone['clarity'] = $clarity;//石头形状
    	    $stoneList[$stone_postion][] = $stone;
	    }
	    return $stoneList;
	    
	}
	/**
	 * 查询 款式信息   副石重，副石粒数 (返回结果中，副石重 是总重  未除以副石粒数)
	 * @param unknown $style_sn 款号
	 * @param unknown $xiangkou 镶口
	 * @param unknown $zhiquan 指圈
	 * @return multitype:unknown
	 */
	function getStyleFushi($style_sn,$xiangkou,$zhiquan){

	    $sql = "select finger as zhiquan,
            	     sec_stone_weight as fushi_zhong1,
            	     sec_stone_num  as fushi_num1,
            	     sec_stone_weight_other as fushi_zhong2,
            	     sec_stone_num_other as fushi_num2,
            	     sec_stone_weight3 as fushi_zhong3,
            	     sec_stone_num3 as fushi_num3 
	    from front.app_xiangkou where style_sn='{$style_sn}' and stone={$xiangkou} limit 100";
	    
	    $data = $this->db()->getAll($sql);
	    $fushiInfo = array();
	    foreach ($data as $vo){
	        $zhiquan_arr = explode('-',$vo['zhiquan']);
	        if(empty($zhiquan_arr) || count($zhiquan_arr)!=2){
	            continue;
	        }
	        $zhiquan_min = $zhiquan_arr[0];
	        $zhiquan_max = $zhiquan_arr[1];
	        if($zhiquan>=$zhiquan_min && $zhiquan<=$zhiquan_max){
	            $fushiInfo['fushi1'] = '';
	            $fushiInfo['fushi_num1'] = $vo['fushi_num1'];
	            $fushiInfo['fushi_zhong_total1'] = $vo['fushi_zhong1']/1;
	            if($vo['fushi_num1']>0 && $vo['fushi_zhong1']>0){
	                $fushiInfo['fushi_zhong1'] = sprintf("%.4f",$vo['fushi_zhong1']/$vo['fushi_num1'])/1;
	                $fushiInfo['fushi1'] = $fushiInfo['fushi_zhong_total1'].'ct/'.$vo['fushi_num1'].'p';
	            }else{
	                $fushiInfo['fushi_zhong1']=0;
	            }
	            
	            $fushiInfo['fushi2'] = '';
	            $fushiInfo['fushi_num2'] = $vo['fushi_num2'];
	            $fushiInfo['fushi_zhong_total2'] = $vo['fushi_zhong2']/1;
	            if($vo['fushi_num2']>0 && $vo['fushi_zhong2']>0){
	                $fushiInfo['fushi_zhong2'] = sprintf("%.4f",$vo['fushi_zhong2']/$vo['fushi_num2'])/1;
	                $fushiInfo['fushi2'] = $fushiInfo['fushi_zhong_total2'].'ct/'.$vo['fushi_num2'].'p';
	            }else{
	                $fushiInfo['fushi_zhong2']=0;
	            }
	            
	            $fushiInfo['fushi3'] = $vo['fushi_num3'];
	            $fushiInfo['fushi_num3'] = $vo['fushi_num3'];
	            $fushiInfo['fushi_zhong_total3'] = $vo['fushi_zhong3']/1;
	            if($vo['fushi_num3']>0 && $vo['fushi_zhong3']>0){
	  	                $fushiInfo['fushi_zhong3'] = sprintf("%.4f",$vo['fushi_zhong3']/$vo['fushi_num3'])/1;
	                $fushiInfo['fushi3'] = $fushiInfo['fushi_zhong_total3'].'ct/'.$vo['fushi_num3'].'p';
	            }else{
	                $fushiInfo['fushi_zhong3']=0;
	            }
	            
	            break;
	        }
	    }
	    return $fushiInfo;	
	}

	//根据款号取款式属性信息
	public function getAttrByStyleSn($style_sn, $attr_name)
	{
		$return_str = "";
		$sql = "SELECT rs.attribute_value FROM front.`rel_style_attribute` rs LEFT JOIN front.app_attribute aa 
		on aa.attribute_id = rs.attribute_id 
		where aa.attribute_name = '".$attr_name."' and rs.style_sn = '".$style_sn."';";
		$attr_value = $this->db()->getOne($sql);
		if(!empty($attr_value)){
			$sql = "select att_value_name from app_attribute_value where att_value_id in(".$attr_value.");";
			$res = $this->db()->getAll($sql);
			if(!empty($res)) {
				$rest = array_column($res, 'att_value_name');
				$return_str = implode(",", $rest);
			}
		}
		return $return_str;
	}
}

?>