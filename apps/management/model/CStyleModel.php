<?php
/**
 * 款式库跨模块Model
 * C开头的Model 为 跨模块Model 可被不同模块下的 SelfStyleModel 继承
 *  -------------------------------------------------
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2017-05-12 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class CStyleModel extends SelfModel
{
    protected $db;
	function __construct ($strConn="")
	{
		parent::__construct($strConn);
	}
	
    /**
     * 根据款号查询       主石，副石信息
     * @param unknown $style_sn 款号
     * @return Ambigous <multitype:, multitype:string Ambigous <string, unknown> Ambigous <string, unknown, mixed> unknown >
     */
	public function getStyleStoneByStyleSn($style_sn){

        $shape_arr = array("0"=>"无",1=>"垫形",2=>"公主方",3=>"祖母绿",4=>"心形",5=>"蛋形",6=>"椭圆形",7=>"橄榄形",8=>"三角形",9=>"水滴形",10=>"长方形",11=>"圆形",12=>"梨形",13=>"马眼形");
	    $stonecat_arr = array("0"=>"无","1"=>"圆钻","2"=>"异形钻","3"=>"珍珠","4"=>"翡翠","5"=>"红宝石","6"=>"蓝宝石","7"=>"和田玉","8"=>"水晶","9"=>"珍珠贝","10"=>"碧玺","11"=>"玛瑙","12"=>"月光石","13"=>"托帕石","14"=>"石榴石","15"=>"绿松石","16"=>"芙蓉石","17"=>"祖母绿","18"=>"贝壳","19"=>"橄榄石","20"=>"彩钻","21"=>"葡萄石","22"=>"海蓝宝","23"=>"坦桑石","24"=>"粉红宝","25"=>"沙佛莱","26"=>"粉红蓝宝石");
	    $color_arr = array("0"=>"无","1" =>"F","2" =>"G","3" =>"H","4" =>"I","8" =>"I-J","5" =>"J","6" =>"K","9" =>"K-L","7" =>"L","10" =>"白","11" =>"M","12" =>"<N","13" =>"N","14" =>"D","15" =>"E");
	    $clarity_arr = array("0"=>"无","1"=>"IF","2" => "VVS","3" => "VVS1","4" =>"VVS2","5" =>"VS","6" =>"VS1","7" =>"VS2","8" =>"SI","9" =>"SI1","10" =>"SI2","11" =>"I1","12" =>"I2","13" =>"VSN","14" =>"不分级");

	    $sql = "select a.stone_position,a.stone_cat,a.stone_attr from front.rel_style_stone a inner join front.base_style_info b on a.style_id=b.style_id where b.style_sn='{$style_sn}'";
	    $data = $this->db()->getAll($sql);

	    $stoneList = array();
	    foreach($data as $vo){
	        $stone = array();
	        //stone_position = 1主石 2 副石
	        $stone_postion = $vo['stone_position'];	        
	        //stone_cat=1 圆钻 圆形   stone_cat=2 异形钻 对应形状
	        $stoneAttr = unserialize($vo['stone_attr']);
	        if($vo['stone_cat']==1){
	            $shape_name = "圆形";	
	        }else if($vo['stone_cat']==0){
	            $stoneAttr = array();
	            $shape_name = "无";
	            continue;//石头类型为无 记录无效，忽略。
	        }else{
	            $shape_id = isset($stoneAttr['shape_fushi'])?$stoneAttr['shape_fushi']:'0';
	            $shape_id = isset($stoneAttr['shape_zhushi'])?$stoneAttr['shape_zhushi']:$shape_id;
	            $shape_name = isset($shape_arr[$shape_id])?$shape_arr[$shape_id]:$shape_id;
	        }
	        $color_id = isset($stoneAttr['color_fushi'])?$stoneAttr['color_fushi']:'0';
	        $color_id = isset($stoneAttr['color_zhushi'])?$stoneAttr['color_zhushi']:$color_id;
	        $color = isset($color_arr[$color_id])?$color_arr[$color_id]:$color_id;
	         
	        $clarity_id = isset($stoneAttr['clarity_fushi'])?$stoneAttr['clarity_fushi']:'0';
	        $clarity_id = isset($stoneAttr['clarity_zhushi'])?$stoneAttr['clarity_zhushi']:$clarity_id;
	        $clarity = isset($clarity_arr[$clarity_id])?$clarity_arr[$clarity_id]:$clarity_id;
    	    
	        $zhushi_num = isset($stoneAttr['number'])?$stoneAttr['number']:'0';
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
    	    $stone['zhushi_num'] = $zhushi_num;//主石粒数
    	    $stoneList[$stone_postion][] = $stone;
	    }
	    return $stoneList;
	    
	}
	/**
	 * 查询 款式信息   副石重，副石粒数 (返回结果中，副石重 是总重  未除以副石粒数)
	 * @param unknown $style_sn 款号
	 * @param unknown $stone 石重
	 * @param unknown $xiangkou 镶口
	 * @param unknown $zhiquan 指圈
	 * @return multitype:unknown
	 */
	function getStyleFushi($style_sn,$stone,$xiangkou,$zhiquan){
	    if($stone === '' && $xiangkou ===''){
	        return array();
	    }
	    $zhiquan_where = "";
        /*
        $zhiquan_where = " and finger in (
						select att_value_name from app_attribute_value where attribute_id=5 and att_value_id in (
							select SUBSTRING_INDEX(SUBSTRING_INDEX(r.attribute_value,',',n.id),',',-1) as val    from rel_style_attribute r  left join rel_style_attribute_node n on LENGTH(trim(TRAILING ',' from r.attribute_value))-LENGTH(  REPLACE(trim(TRAILING ',' from r.attribute_value),',',''))+1 >= n.id where style_sn='{$style_sn}'  and attribute_id=5
						)
                ) ";
        */        

	    $zhiquan = round($zhiquan);//四舍五入
	    $stone = trim($stone);
	    $xiangkou = trim($xiangkou);
	    $sql = "select finger as zhiquan,
            	     sec_stone_weight as fushi_zhong1,
            	     sec_stone_num  as fushi_num1,
            	     sec_stone_weight_other as fushi_zhong2,
            	     sec_stone_num_other as fushi_num2,
            	     sec_stone_weight3 as fushi_zhong3,
            	     sec_stone_num3 as fushi_num3 
	    from front.app_xiangkou where style_sn='{$style_sn}'";
	    if($stone!=='' && is_numeric($stone) && $stone>=0){
	        $sql .=" and round(stone*1-0.05,4) <= {$stone} and {$stone}<= round(stone*1+0.04,4) ". $zhiquan_where . " order by abs(stone-{$stone}) asc";
	    }else if($stone==='' && is_numeric($xiangkou) && $xiangkou>=0){
	        $sql .=" and stone= ".$xiangkou."".$zhiquan_where;
	    }else{
	        return array();
	    }
	   
	    //echo $sql;
	    $data = $this->db()->getAll($sql);
	    $fushiInfo = array();
	    foreach ($data as $vo){
	        $zhiquan_arr = explode('-',$vo['zhiquan']);
	        $len = count($zhiquan_arr);
	        if($len==2){
	            $zhiquan_min = $zhiquan_arr[0];
	            $zhiquan_max = $zhiquan_arr[1];
	        }else if($len==1){
	            $zhiquan_min = $zhiquan_arr[0];
	            $zhiquan_max = $zhiquan_arr[0];
	        }else {
	            continue;
	        }
	        
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
	
	/**
	 * 获取布产单主石，副石信息
	 * @param unknown $style_sn 款号
	 * @param unknown $attrlist 布产已有属性列表
	 * @return multitype:multitype:string unknown  multitype:string  multitype:string Ambigous <string, number, unknown>
	 */
	public function getStoneAttrList($style_sn,$attrlist){

	    $stoneAttrList = array();//主石，副石 属性
	
	    $stoneList = $this->getStyleStoneByStyleSn($style_sn);
	    $stoneAttrList[11] = array('code'=>'zhushi_cat','name'=>'主石类型','value'=>'');
	    $stoneAttrList[12] = array('code'=>'zhushi_shape','name'=>'主石形状','value'=>'');
	    $stoneAttrList[21] = array('code'=>'fushi_cat','name'=>'副石类型','value'=>'');
	    $stoneAttrList[22] = array('code'=>'fushi_shape','name'=>'副石形状','value'=>'');
	    $stoneAttrList[23] = array('code'=>'fushi_yanse','name'=>'副石颜色','value'=>'');
	    $stoneAttrList[24] = array('code'=>'fushi_jingdu','name'=>'副石净度','value'=>'');
	    $stoneAttrList[25] = array('code'=>'fushi_zhong_total1','name'=>'副石1总重','value'=>'');
	    $stoneAttrList[26] = array('code'=>'fushi_num1','name'=>'副石1粒数','value'=>'');
	    $stoneAttrList[27] = array('code'=>'fushi_zhong_total2','name'=>'副石2总重','value'=>'');
	    $stoneAttrList[28] = array('code'=>'fushi_num2','name'=>'副石2粒数','value'=>'');
	    $stoneAttrList[29] = array('code'=>'fushi_zhong_total3','name'=>'副石3总重','value'=>'');
	    $stoneAttrList[30] = array('code'=>'fushi_num3','name'=>'副石3粒数','value'=>'');
	     
	    foreach ($stoneList as $key=>$vo){
	        if($key==1){
	            $zhushi_shape_arr = array_unique(array_column($vo,'shape'));
	            $zhushi_shape = trim(implode('|',$zhushi_shape_arr),'|');
	            $zhushi_cat_arr = array_unique(array_column($vo,'stone_cat'));
	            $zhushi_cat = trim(implode('|',$zhushi_cat_arr),'|');
	            $stoneAttrList[11] = array('code'=>'zhushi_cat','name'=>'主石类型','value'=>$zhushi_cat);
	            $stoneAttrList[12] = array('code'=>'zhushi_shape','name'=>'主石形状','value'=>$zhushi_shape);
	        }else if($key==2){
	            $fushi_cat_arr = array_unique(array_column($vo,'stone_cat'));
	            $fushi_cat = trim(implode('|',$fushi_cat_arr),'|');
	            $fushi_shape_arr = array_unique(array_column($vo,'shape'));
	            $fushi_shape = trim(implode('|',$fushi_shape_arr),'|');
	            $fushi_yanse_arr = array_unique(array_column($vo,'color'));
	            $fushi_yanse = trim(implode('|',$fushi_yanse_arr),'|');
	
	            $fushi_jingdu_arr = array_unique(array_column($vo,'clarity'));
	            $fushi_jingdu = trim(implode('|',$fushi_jingdu_arr),'|');
	             
	            $stoneAttrList[21] = array('code'=>'fushi_cat','name'=>'副石类型','value'=>$fushi_cat);
	            $stoneAttrList[22] = array('code'=>'fushi_shape','name'=>'副石形状','value'=>$fushi_shape);
	            $stoneAttrList[23] = array('code'=>'fushi_yanse','name'=>'副石颜色','value'=>$fushi_yanse);
	            $stoneAttrList[24] = array('code'=>'fushi_jingdu','name'=>'副石净度','value'=>$fushi_jingdu);
	        }
	    }

	    $zhiquan = $carat = $xiangkou = "";
	    foreach ($attrlist as $vo){
	        if($vo['code']=="zhiquan"){
	            $zhiquan = trim($vo['value']);
	        }else if($vo['code']=="cart" || $vo['code']=="zuanshidaxiao"){
	            $carat = trim($vo['value']);
	        }else if($vo['code']=="xiangkou"){
	            $xiangkou = trim($vo['value']);
	        }
	    }	

        $fushiInfo = $this->getStyleFushi($style_sn, $carat,$xiangkou, $zhiquan);
        if(!empty($fushiInfo)){
            $stoneAttrList[25] = array('code'=>'fushi_zhong_total1','name'=>'副石1总重','value'=>$fushiInfo['fushi_zhong_total1']);
            $stoneAttrList[26] = array('code'=>'fushi_num1','name'=>'副石1粒数','value'=>$fushiInfo['fushi_num1']);

            $stoneAttrList[27] = array('code'=>'fushi_zhong_total2','name'=>'副石2总重','value'=>$fushiInfo['fushi_zhong_total2']);
            $stoneAttrList[28] = array('code'=>'fushi_num2','name'=>'副石2粒数','value'=>$fushiInfo['fushi_num2']);

            $stoneAttrList[29] = array('code'=>'fushi_zhong_total3','name'=>'副石3总重','value'=>$fushiInfo['fushi_zhong_total3']);
            $stoneAttrList[30] = array('code'=>'fushi_num3','name'=>'副石3粒数','value'=>$fushiInfo['fushi_num3']);
        }
        /* 
        else{
            $stoneAttrList[21]['value'] ='';
	        $stoneAttrList[22]['value'] ='';
	        $stoneAttrList[23]['value'] ='';
	        $stoneAttrList[24]['value'] ='';
        }*/

	    ksort($stoneAttrList);
	    return $stoneAttrList;
	
	}
}

?>