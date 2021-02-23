<?php

/**
 *  -------------------------------------------------
 *   @file		: DiamondListModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 16:13:09
 *   @update	:
 *  -------------------------------------------------
 */
class DiamondListModel extends Model {

    //切工(Cut) ：完美 EX   非常好 VG   好 G   一般 Fair
    public static $Cut_arr = array('EX', 'VG', 'G', 'Fair');
    //抛光(Polish)	 完美 EX   非常好 VG   好 G   一般 Fair
    public static $Polish_arr = array('EX', 'VG', 'G', 'Fair');
    //对称(Symmetry)	 完美 EX   非常好 VG   好 G   一般 Fair
    public static $Symmetry_arr = array('EX', 'VG', 'G', 'Fair');
    //荧光(Fluorescence): 无 N   轻微 F   中度 M   强烈 S
    public static $Fluorescence_arr = array('N', 'F', 'M', 'S');
    //颜色(Color): D	完全无色   E 无色   F 几乎无色   G   H   I 接近无色   J
    public static $Color_arr = array('D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', /*'H',*/ 'D-E', 'E-F', 'F-G', 'G-H', 'H-I', 'I-J', 'J-K');
    //净度(Clarity) FL 完全洁净  IF 内部洁净  VVS1 极微瑕  VVS2  VS1 微瑕  VS2  SI1 小瑕  SI2
    public static $Clarity_arr = array('FL', 'IF', 'VVS1', 'VVS2', 'VS1', 'VS2', 'SI1', 'SI2');
    //形状(Shape): 圆形   公主方形   祖母绿形   橄榄形   椭圆形   水滴形   心形  坐垫形   辐射形   方形辐射形   方形祖母绿   三角形
    public static $Shape_arr = array(1 => '圆形', 2 => '公主方形', 3 => '祖母绿形', 4 => '橄榄形', 5 => '椭圆形', 6 => '水滴形', 7 => '心形', 8 => '坐垫形', 9 => '辐射形', 10 => '方形辐射形', 11 => '方形祖母绿', 12 => '三角形',13=>'戒指托',14=>'异形',15=>'梨形',16=>'阿斯切',17 => '马眼', 18 => '长方形', 19 => '雷迪恩');
    //证书类型
    public static $Cert_arr = array('HRD-D','GIA','HRD','IGI','DIA','AGL','EGL','NGTC','NGGC','HRD-S');
    
    function __construct($id = NULL, $strConn = "") {
       
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url DiamondListController/search
     */
    function pageList($where) {
    
        if(isset($where['page'])){
            $keys[]='page';
            $vals[]=$where['page'];
        }
        if(isset($where['pageSize'])){
            $keys[]='pageSize';
            $vals[]=$where['pageSize'];
        }
        if(isset($where['goods_sn'])){
            $keys[]='goods_sn';
            $vals[]=$where['goods_sn'];
        }
        if(isset($where['carat_min'])){
            $keys[]='carat_min';
            $vals[]=$where['carat_min'];
        }
        if(isset($where['carat_max'])){
            $keys[]='carat_max';
            $vals[]=$where['carat_max'];
        }
        if(isset($where['price_min'])){
            $keys[]='price_min';
            $vals[]=$where['price_min'];
        }
        if(isset($where['price_max'])){
            $keys[]='price_max';
            $vals[]=$where['price_max'];
        }
        if(isset($where['clarity'])){
            $keys[]='clarity';
            $vals[]=$where['clarity'];
        }
    
        if(isset($where['color'])){
            $keys[]='color';
            $vals[]=$where['color'];
        }
        if(isset($where['shape'])){
            $keys[]='shape';
            $vals[]=$where['shape'];
        }
    
        if(isset($where['cut'])){
            $keys[]='cut';
            $vals[]=$where['cut'];
        }
        if(isset($where['polish'])){
            $keys[]='polish';
            $vals[]=$where['polish'];
        }
        if(isset($where['symmetry'])){
            $keys[]='symmetry';
            $vals[]=$where['symmetry'];
        }
        if(isset($where['fluorescence'])){
            $keys[]='fluorescence';
            $vals[]=$where['fluorescence'];
        }
    
        if(isset($where['cert'])){
            $keys[]='cert';
            $vals[]=$where['cert'];
        }
        if(isset($where['goods_name'])){
            $keys[]='goods_name';
            $vals[]=$where['goods_name'];
        }
        if(isset($where['from_ad'])){
            $keys[]='from_ad';
            $vals[]=$where['from_ad'];
        }     
        if(isset($where['s_carats_tsyd1'])){
            $keys[]='s_carats_tsyd1';
            $vals[]=$where['s_carats_tsyd1'];
        }
        if(isset($where['e_carats_tsyd1'])){
            $keys[]='e_carats_tsyd1';
            $vals[]=$where['e_carats_tsyd1'];
        }
        if(isset($where['s_carats_tsyd2'])){
            $keys[]='s_carats_tsyd2';
            $vals[]=$where['s_carats_tsyd2'];
        }
        if(isset($where['e_carats_tsyd2'])){
            $keys[]='e_carats_tsyd2';
            $vals[]=$where['e_carats_tsyd2'];
        }
        if(isset($where['good_type'])){
            $keys[]='good_type';
            $vals[]=$where['good_type'];
        }
        if(isset($where['warehouse'])){
            $keys[]='warehouse';
            $vals[]= $where['warehouse'];
        }
        /*
         if(isset($where['hrd_s_warehouse'])){
            $keys[]='hrd_s_warehouse';
            $vals[]= $where['hrd_s_warehouse'];
        }*/       
        if(isset($where['gm'])){
            $keys[]='gm';
            $vals[]=$where['gm'];
        }
        if(isset($where['ysyd'])){
            $keys[]='kuan_sn';
            $vals[]=$where['ysyd'];
        }
        if(isset($where['gemx_zhengshu'])){
            $keys[]='gemx_zhengshu';
            $vals[]=$where['gemx_zhengshu'];
        }
        if(isset($where['cert_id'])){
            $keys[]='cert_id';
            $vals[]=$where['cert_id'];
        }
        //是否有活动
        if(isset($where['is_active'])){
            $keys[]='is_active';
            $vals[]=$where['is_active'];
        }
        //状态
        if(isset($where['status'])){
            $keys[]='status';
            $vals[]=$where['status'];
        }
        if(isset($where['zdj'])){
            $keys[]='zdj';
            $vals[]=$where['zdj'];
        }
        if(isset($where['stonesort'])){
            $keys[]='stonesort';
            $vals[]=$where['stonesort'];
        }
        if(isset($where['yansesort'])){
            $keys[]='yansesort';
            $vals[]=$where['yansesort'];
        }
        if(isset($where['jdsort'])){
            $keys[]='jdsort';
            $vals[]=$where['jdsort'];
        }
        if(isset($where['no_goods_id'])){
            $keys[]='no_goods_id';
            $vals[]=$where['no_goods_id'];
        }
        //双十一活动
        if(isset($where['ssy_active'])){
            $keys[]='ssy_active';
            $vals[]=$where['ssy_active'];
        }
        if(isset($where['pf_price_min'])){
        	$keys[]='pf_price_min';
        	$vals[]=$where['pf_price_min'];
        }
        if(isset($where['pf_price_max'])){
        	$keys[]='pf_price_max';
        	$vals[]=$where['pf_price_max'];
        }
        if(isset($where['include_img'])){
            $keys[]='include_img';
            $vals[]=$where['include_img'];
        }

        $where['is_4c'] = isset($where['is_4c'])?$where['is_4c']:0;
        if($where['is_4c']==2){
            //$where['is_4c']==2 表示 快捷4C搜索
            if (!isset($where['price_min'])){
                $keys[]='price_min';
                $vals[]='0.01';//最低价格必须大于1分钱
            }
            $keys[]='kuan_sn';//非天生一对
            $vals[]="no_tsyd";
    
            $keys[]='zdj';
            $vals[]='asc';//最低价格排序
            //来源非kela
            //$keys[]='not_from_ad';
            //$vals[]=1;
            //来源非kela
            $keys[]='not_from_ad';
            if(!empty($where['not_from_ad'])){
                if(is_array($where['not_from_ad']))
                    $vals[]=array_merge(array('1'),$where['not_from_ad']);
                else
                    $vals[]=array('1',$where['not_from_ad']);
            }else
                $vals[]=1;

            //$keys[]='pageSize';
            //$vals[]='1';//只选一条
            $ret = ApiModel::diamond_api($keys,$vals,'GetDiamondList');
            if (!$ret['error']){     
                foreach($ret['data']['data'] as $key=>$val){
                    $_SESSION['cart_filter_data2'][$val["goods_id"]] = array("keys" => $keys, "vals"=> $vals,'is_4c'=>2);
                }
            }            
        }else if($where['is_4c']==3){
            if (!isset($where['price_min'])){
                $keys[]='price_min';
                $vals[]='0.01';//最低价格必须大于1分钱
            }
            $keys[]='kuan_sn';//非天生一对
            $vals[]="no_tsyd";
            
            $keys[]='zdj';
            $vals[]='asc';//最低价格排序
            
            //来源非kela
            //$keys[]='not_from_ad';
            //$vals[]=1;
            //来源非kela
            $keys[]='not_from_ad';
            if(!empty($where['not_from_ad'])){
                if(is_array($where['not_from_ad']))
                    $vals[]=array_merge(array('1'),$where['not_from_ad']);
                else
                    $vals[]=array('1',$where['not_from_ad']);
            }else
                $vals[]=1;

            $ret = ApiModel::diamond_api($keys,$vals,'GetDiamondList');
            $data = $where['data'];
            if (!$ret['error']){
                foreach($ret['data']['data'] as $key=>$val){
                    $_SESSION['cart_filter_data3'][$val["goods_id"]] = array("keys" => $keys, "vals"=> $vals,'is_4c'=>3,'data'=>$data);
                    break;
                }
            }
            //print_r($_SESSION['cart_filter_data3']);
        }else{
            if(isset($where['not_from_ad'])){
                $keys[]='not_from_ad';
                $vals[]=$where['not_from_ad'];
            }  
            $ret=ApiModel::diamond_api($keys,$vals,'GetDiamondList');
            //print_r($ret);
        }
        if($ret['error']==1){
            $ret['data'] = array('page'=>1, 'pageSize'=>1, 'recordCount'=>0, 'pageCount'=>0, 'data'=>array());
        }
        return $ret;
    }
    function _pageList($where) {
        
        if(isset($where['page'])){
            $keys[]='page';
            $vals[]=$where['page'];
        }
        if(isset($where['pageSize'])){
            $keys[]='pageSize';
            $vals[]=$where['pageSize'];
        }
        if(isset($where['goods_sn'])){
            $keys[]='goods_sn';
            $vals[]=$where['goods_sn'];
        }
        if(isset($where['carat_min'])){
            $keys[]='carat_min';
            $vals[]=$where['carat_min'];
        }
        if(isset($where['carat_max'])){
            $keys[]='carat_max';
            $vals[]=$where['carat_max'];
        }
        if(isset($where['price_min'])){
            $keys[]='price_min';
            $vals[]=$where['price_min'];
        }
        if(isset($where['price_max'])){
            $keys[]='price_max';
            $vals[]=$where['price_max'];
        }
        if(isset($where['clarity'])){
                $keys[]='clarity';
                $vals[]=$where['clarity'];
        }
        
        if(isset($where['color'])){
                $keys[]='color';
                $vals[]=$where['color'];
        }
        if(isset($where['shape'])){
                $keys[]='shape';
                $vals[]=$where['shape'];
        }
        
        if(isset($where['cut'])){
                $keys[]='cut';
                $vals[]=$where['cut'];
        } 
        if(isset($where['polish'])){
                $keys[]='polish';
                $vals[]=$where['polish'];
        }
        if(isset($where['symmetry'])){
                $keys[]='symmetry';
                $vals[]=$where['symmetry'];
        }
        if(isset($where['fluorescence'])){
                $keys[]='fluorescence';
                $vals[]=$where['fluorescence'];
        }
        
        if(isset($where['cert'])){
                $keys[]='cert';
                $vals[]=$where['cert'];
        }
        if(isset($where['goods_name'])){
                $keys[]='goods_name';
                $vals[]=$where['goods_name'];
        }
        if(isset($where['from_ad'])){
                $keys[]='from_ad';
                $vals[]=$where['from_ad'];
        }
		if(isset($where['s_carats_tsyd1'])){
                $keys[]='s_carats_tsyd1';
                $vals[]=$where['s_carats_tsyd1'];
        }
		if(isset($where['e_carats_tsyd1'])){
                $keys[]='e_carats_tsyd1';
                $vals[]=$where['e_carats_tsyd1'];
        }
		if(isset($where['s_carats_tsyd2'])){
                $keys[]='s_carats_tsyd2';
                $vals[]=$where['s_carats_tsyd2'];
        }
		if(isset($where['e_carats_tsyd2'])){
                $keys[]='e_carats_tsyd2';
                $vals[]=$where['e_carats_tsyd2'];
		}
        if(isset($where['good_type'])){
                $keys[]='good_type';
                $vals[]=$where['good_type'];
        }
        if(isset($where['warehouse'])){
                $keys[]='warehouse';
                $vals[]= $where['warehouse'];
        }
        if(isset($where['gm'])){
                $keys[]='gm';
                $vals[]=$where['gm'];
        }
        if(isset($where['ysyd'])){
                $keys[]='kuan_sn';
                $vals[]=$where['ysyd'];
        }
        if(isset($where['gemx_zhengshu'])){
                $keys[]='gemx_zhengshu';
                $vals[]=$where['gemx_zhengshu'];
        }
        if(isset($where['cert_id'])){
                $keys[]='cert_id';
                $vals[]=$where['cert_id'];
        }
        //是否有活动
        if(isset($where['is_active'])){
                $keys[]='is_active';
                $vals[]=$where['is_active'];
        }
        //状态
        if(isset($where['status'])){
                $keys[]='status';
                $vals[]=$where['status'];
        }
		if(isset($where['zdj'])){
				$keys[]='zdj';
				$vals[]=$where['zdj'];
		}
		if(isset($where['stonesort'])){
				$keys[]='stonesort';
				$vals[]=$where['stonesort'];
		}
		if(isset($where['yansesort'])){
				$keys[]='yansesort';
				$vals[]=$where['yansesort'];
		}
		if(isset($where['jdsort'])){
				$keys[]='jdsort';
				$vals[]=$where['jdsort'];
		}
		if(isset($where['no_goods_id'])){
				$keys[]='no_goods_id';
				$vals[]=$where['no_goods_id'];
		}
		if(isset($_SESSION['cart_filter_data'])){
            unset($_SESSION['cart_filter_data']);
		}
        
		if (empty($where['is_4c'])){
			$ret=ApiModel::diamond_api($keys,$vals,'GetDiamondList');
		} else if($where['is_4c']==2){
		    //$where['is_4c']==2 表示 快捷4C搜索
		    if (!isset($where['price_min'])){
		        $keys[]='price_min';
		        $vals[]='0.01';//最低价格必须大于1分钱
		    }
		    $keys[]='kuan_sn';//非天生一对
			$vals[]="no_tsyd";
		    
		    $keys[]='zdj';
		    $vals[]='asc';//最低价格排序
		    	
		    //$keys[]='pageSize';
		    //$vals[]='1';//只选一条
		    $ret = ApiModel::diamond_api($keys,$vals,'GetDiamondList');
		    if (!$ret['error']){
		        foreach($ret['data']['data'] as $key=>$val){
		            $_SESSION['cart_filter_data'][$val["goods_id"]] = array("keys" => $keys, "vals"=> $vals,'is_4c'=>2);
		        }
		    }
		    if($ret['error']==1){
		       $ret['data'] = array('page'=>1, 'pageSize'=>1, 'recordCount'=>0, 'pageCount'=>0, 'data'=>array());
		    }
		} else {   
			//4c搜索 分别搜索3条数据			
			if (!isset($where['price_min'])){
				$keys[]='price_min';
				$vals[]='0.01';//最低价格必须大于1分钱
			}
			$keys[]='kuan_sn';//非天生一对
			$vals[]="no_tsyd";
			
			$keys[]='page';
            $vals[]='1';
            
            //$keys[]='no_tsyd';
            //$vals[]='1';
            
			$keys[]='zdj';
			$vals[]='asc';//最低价格排序
			
            $keys[]='pageSize';
            $vals[]='1';//只选一条
			
			$data = array();
			
			$_SESSION['cart_filter_data'] = array();
			/*begin-------------第一条现货--------------begin*/
            if ((empty($where['good_type']) || $where['good_type'] == "1") && (empty($where['warehouse']) || array_intersect($where['warehouse'], $where['user_warehouse']))){
				$keys[]='good_type';
				$vals[]='1';//现货
				
				$keys[]='warehouse';
				$vals[]= $where['user_warehouse'];//自己所在仓库
					
				$result=ApiModel::diamond_api($keys,$vals,'GetDiamondList');
				
				if (!$result['error']){
					foreach($result['data']['data'] as $key=>$val){
						$result['data']['data'][$key]['delivery_time'] = '现货';
						$_SESSION['cart_filter_data'][$val["goods_id"]] = array("keys" => $keys, "vals"=> $vals,'is_4c'=>1);
					}
				}
				$data = $result['error'] ? $data : array_merge($data,$result['data']['data']);


			}
	
			/*end-------------第一条现货--------------end*/
			
			/*begin-----------第二条7天取货--------------begin*/
			if ((empty($where['good_type']) || $where['good_type'] == "1") && (  empty($where['warehouse']) || !empty(array_diff($where['warehouse'], $where['user_warehouse'])))){

				$keys[]='good_type';
				$vals[]='1';//现货
				
				if (!empty($where['warehouse'])){
					$keys[]='warehouse';
					$vals[]= array_diff($where['warehouse'], $where['user_warehouse']);
				}else{
					$keys[]='warehouse';
					$vals[]= $where['warehouse'];
					
					$keys[]='no_warehouse';
					$vals[]= $where['user_warehouse'];
					
				}
				
				$result=ApiModel::diamond_api($keys,$vals,'GetDiamondList');
				if (!$result['error']){
					foreach($result['data']['data'] as $key=>$val){
						$result['data']['data'][$key]['delivery_time'] = '7天';
						$_SESSION['cart_filter_data'][$val["goods_id"]] = array("keys" => $keys, "vals"=> $vals,'is_4c'=>1);
					}
				}
				

				$data = $result['error'] ? $data : array_merge($data,$result['data']['data']);
			}
			/*end-------------第二条7天取货--------------end*/
			
			/*begin-----------第三条30天取货--------------begin*/
			if ((empty($where['good_type']) || $where['good_type'] == "2")){
				$keys[]='good_type';
				$vals[]='2';//期货
				
				$keys[]='warehouse';
				$vals[]= $where['warehouse'];
				
				$result=ApiModel::diamond_api($keys,$vals,'GetDiamondList');
				//print_r($result);
				//exit;
				if (!$result['error']){
					foreach($result['data']['data'] as $key=>$val){
						$result['data']['data'][$key]['delivery_time'] = '30天';
						$_SESSION['cart_filter_data'][$val["goods_id"]] = array("keys" => $keys, "vals"=> $vals ,'is_4c'=>1);
					}
				}
				$data = $result['error'] ? $data : array_merge($data,$result['data']['data']);
			}

			/*end-------------第三条30天取货--------------end*/

			$ret['error'] = 0;
			$ret['data'] = array('page'=>1, 'pageSize'=>3, 'recordCount'=>count($data), 'pageCount'=>count($data), 'data'=>$data);
			//print_r($_SESSION['cart_filter_data']);
			
		}
        return $ret;              
    }
    

    /**
     * 	getRowById，取一行
     *
     * 	@url DiamondListController/getAllList
     */
    function getRowById($goods_id) {
        $keys=array('goods_id');
        $vals=array($goods_id);
        $ret=ApiModel::diamond_api($keys,$vals,'GetDiamondByiId');
        return $ret; 
    }

    /*
     * 通过id获取裸钻数据
     */
    
    function getColorRowById($id) {
    	$keys=array('id');
    	$vals=array($id);
    	$ret=ApiModel::diamond_api($keys,$vals,'GetColorDiamondByiId');
    	return $ret;
    }
    

    /**
     * 	getRowByGoodSn，取一行
     *
     * 	@url DiamondListController/getRowByGoodSn
     */
    function getRowByGoodSn($goods_sn) {
        $keys=array('goods_sn');
        $vals=array($goods_sn);
        $ret=ApiModel::diamond_api($keys,$vals,'GetDiamondByGoods_sn');
        if ($ret['error'] == 1) {
            $dia = $this->resolveDIA($goods_sn);
            if ($dia !== false) {
                 return $dia;
            }
        }
        return $ret; 
    }

    /**
     * 	getRowByGoodSn，取一行
     *
     * 	@url DiamondListController/getRowByGoodSnOrCertId
     */
    function getRowByGoodSnOrCertId($sn) {
        $keys=array('goods_sn_or_certid');
        $vals=array($sn);
        $ret=ApiModel::diamond_api($keys,$vals,'GetDiamondByGoods_snOrCertid');
        if ($ret['error'] == 1) {
        	$dia = $this->resolveDIA($sn);
            if ($dia !== false) {
                 return $dia;
            }
        }
        return $ret; 
    }

    function resolveDIA($goods_or_cert_id) {
    	if (SYS_SCOPE != 'zhanting') return false;
    	
    	$companyModel = new CompanyModel(1);
    	$company_type = $companyModel->select2("company_type","id={$_SESSION['companyId']}",3);
    	if ($company_type != '3') return false;
   	
    	// 对于经销商，去商品列表再查一次
    	$goods_sn_arr = ApiDiamondModel::getDiamondFromWarehouse($goods_or_cert_id, $_SESSION['companyId']);
    	//var_dump($goods_sn_arr);die();
    	if($goods_sn_arr['error'] == 1) return false;
    	
    	$data = array();
    	foreach($goods_sn_arr['data'] as $k=>$v){
    		if (!empty($v['order_goods_id'])) {
    			return array('error' => 1, 'content' => '货号 ' .$v['goods_id']. '，该裸钻 已绑定了订单，请去商品列表确认或解绑');
    		}
    		//特殊处理
    		if($v['zhushixingzhuang'] == '公主方'){
    			$v['zhushixingzhuang'] = '公主方形';
    		}
    		if($v['zhushixingzhuang'] == '梨形'){
    			$v['zhushixingzhuang'] = '水滴形';
    		}
    		
    		if($v['zhushixingzhuang'] == '垫形'){
    			$v['zhushixingzhuang'] = '坐垫形';
    		}
    		
    		if($v['zhushixingzhuang'] == '祖母绿'){
    			$v['zhushixingzhuang'] = '祖母绿形';
    		}
    		
    		$dia = array();
    		$dia['goods_id'] = $v['goods_id'];
    		$dia['goods_sn'] = $v['goods_id'];
    		$dia['shape'] = $this->getShapeId($v['zhushixingzhuang']);
    		$dia['carat'] = $v['zuanshidaxiao'];
    		$dia['color'] = $v['zhushiyanse'];
    		$dia['cut'] = $v['qiegong'];
    		$dia['clarity'] = $v['zhushijingdu'];
    		$dia['polish'] = $v['paoguang'];
    		$dia['fluorescence'] = $v['yingguang'];
    		$dia['symmetry'] = $v['duichen'];
    		$dia['cert'] = $v['zhengshuleibie'];
    		$dia['cert_id'] = $v['zhengshuhao'];
    		$dia['chengben_jia'] = empty($v['jingxiaoshangchengbenjia']) ? $v['mingyichengben'] : $v['jingxiaoshangchengbenjia'];
    		$dia['good_type'] = 1;
    		$dia['shop_price'] = $dia['chengben_jia'];
    		$dia['pifajia'] = $dia['chengben_jia'];
    		$dia['goods_name'] = $dia['carat']."克拉/ct ".$dia['clarity']."净度 ".$dia['color']."颜色 ".$dia['cut']."切工";
    		$dia['goods_number'] = 1;
    		$dia['gemx_zhengshu'] = $v['gemx_zhengshu'];
    		$dia['kuan_sn'] = $v['goods_sn'];
    		$dia['mo_sn'] = $v['mo_sn'];
    		$dia['status'] = 1;
    		
    		$data[] = $dia;
    	}
    	return array('error' => 0, 'data' => $data);
    }
    
    private function getShapeId($shape)
    {
    	$shape_arr = array(1 => '圆形', 2 => '公主方形', 3 => '祖母绿形', 4 => '橄榄形', 5 => '椭圆形', 6 => '水滴形', 7 => '心形', 8 => '坐垫形', 9 => '辐射形', 10 => '方形辐射形', 11 => '方形祖母绿', 12 => '三角形',13=>'戒指托',14=>'异形',15=>'梨形',16=>'阿斯切',17 => '马眼', 18 => '长方形', 19 => '雷迪恩');
    	foreach($shape_arr as $key => $val)
    	{
    		if($val == $shape){
    			return $key;
    		}
    	}
    	return false;
    }

    /**
     * 	getAllList，取所有
     *
     * 	@url DiamondListController/getAllList
     */
    function getAllList($select="*") {
        $sql = "SELECT $select FROM `" . $this->table() . "`";
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /**
     * 	deletebycert_id，删除
     *
     * 	@url DiamondListController/deletebycert_id
     */
    function deletebycert_id($cert,$cert_id) {
        $sql = "DELETE FROM `".$this->table()."` WHERE `cert` = '".$cert."' AND `cert_id`='".$cert_id."'";
		return $this->db()->query($sql);
    }

    //取所有形状
    public static function getShapeName()
    {
           $Shape_arr=self::$Shape_arr;
           return $Shape_arr;
    }
    
    public function checkMimaVaild($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        return ApiModel::diamond_api($keys,$vals,'checkDiscountMima');
    }

    public function checkMimaVaildPrice($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        return ApiModel::diamond_api($keys,$vals,'checkDiscountMimaPrice');
    }
    
    //取打折密码通过货号
    public function getDiscountByGoods_id($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        return ApiModel::diamond_api($keys,$vals,'checkDiscountid');
    }

    public function get_diamond_by_kuan_sn($kuan_sn){
        if(isset($kuan_sn)){
            $keys[] ='kuan_sn';
            $vals[] =$kuan_sn;             
        }
        return ApiModel::diamond_api($keys,$vals,'GetDiamondByKuan_sn');
    }
    
    public function updateDiscountMima($where) {
        $keys = array('log_data','grant_data');
        $vals = array($where['insert_data'],$where['update_data']);

        return ApiModel::diamond_api($keys,$vals,'updateDiscountMima');
    }
    
    //取消优惠
    public function updateDiscountMimas($where) {
        $keys = array('log_data','grant_data');
        $vals = array($where['insert_data'],$where['update_data']);

        return ApiModel::diamond_api($keys,$vals,'updateDiscountMimas');
    }

    /**
     *
     * 获取库房
     */
    public function get_warehouse_all($type=1)
    {
        $keys[]='pppppp';
        $vals[]='';
        $keys[]='diamond_warehouse';
        $vals[]=$type;
        $ret=ApiModel::warehouse_api($keys,$vals,'GetWarehouseList');
        
        return $ret;  
    }
	    /* 根据钻石大小反推镶口
     * 镶口：不用验证 钻石大小>0 钻石大小<0.10
     * 镶口：0.10 钻石大小>=0.10 钻石大小<=0.15
     * 镶口：0.20 钻石大小>0.15 钻石大小<=0.25
     * 镶口：0.30 钻石大小>0.25 钻石大小<=0.35
     * luna
     */
    public function getXiangKou ($stone){
        $xiangkou = '0';
		if($stone>0 && $stone<0.1){
			return '';
		}
		if($stone>=0.10 && $stone <=0.15){
			$xiangkou = 0.1;
		}
		
		if($stone >0.15 && $stone <1){
			$xiangkou = floatval(substr($stone,0,3));
		}
		if($stone>=1){
			$temp_arr = explode('.',$stone);
			if(count($temp_arr)>=2){
	                        //整数部分
				$zhengshu = floor($stone);;
	                        //小数第一位
				$xiaoshu_part = ($stone-$zhengshu)*10;
				$xiaoshu_part = floor($xiaoshu_part);	
	          		$xiaoshu = $xiaoshu_part*0.1;

            			//小数其他位数
				$xiaoshu2 = floatval($stone) - floatval($zhengshu + $xiaoshu) ; 

				//判断小数其他位数是否小于0.051
				if(floatval($xiaoshu2)<0.051){
					 $xiangkou= $zhengshu + $xiaoshu;
					 return $xiangkou;
				}else{
					$xiangkou = $zhengshu + $xiaoshu+0.1;
					return $xiangkou;
	
				}
			}else{//整数的话直接返回石重即可
				return $xiangkou= $stone;
			}
		}
		return $xiangkou;
    }
    public function getCaiZuaninfo($goods_sn){
        $keys = array('goods_sn');
        $vals = array($goods_sn);
        return ApiModel::diamond_api($keys,$vals,'getCaiZuanInfo');
    }

	/**
     * 	getRowByKeysAndVals 根据条件搜索
     *
     * 	@url DiamondListController/getRowByKeysAndVals
     */
    function getRowByKeysAndVals($keys, $vals) {
        $ret= ApiModel::diamond_api($keys,$vals,'GetDiamondList');
		if (!$ret['error']){
			$return['error'] = 0;
			$return['data'] = $ret['data']['data'];
		}else{
			$return = $ret;
		}
		return $return;
        
    }

    /*
    *通过证书号获取裸钻信息
    *
    */
    public function getGoodsTypeByCertId($cert_id,$cert_id2=''){
        if(!empty($cert_id2)){
            $sql ="select good_type from front.diamond_info_all where cert_id='".$cert_id."' OR cert_id ='".$cert_id2."'";
        }else{
            $sql ="select good_type from front.diamond_info_all where cert_id='".$cert_id."'";
        }
        return $this->db()->getOne($sql);

     }



	}
?>