<?php
/**
 *  -------------------------------------------------
 *   @file		: CommonController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-03
 *   @update	:
 *  -------------------------------------------------
 */
class CommonController extends Controller
{
	protected $whitelist = array();
	protected $gifts = array(1=>'赠送珍珠耳钉',2=>'赠送S925银链',3=>'赠送黑皮绳',4=>'赠送红玛瑙手链',5=>'赠送白色手提袋',6=>'赠送情人节礼盒',7=>'赠送红绳',8=>'赠送手绳',9=>'赠送砗磲手链',10=>'赠送粉晶手链',11=>'赠送金条红包0.02g',12=>'赠送首饰盒',13=>'耳堵');
    public $tihCzInfo = array('无' => '','其它' => '','其他' => '','24K' =>'足金','千足金银'    =>'足金银','S990'    =>'足银','千足银' =>'足银','千足金' =>'足金','PT900'   =>'铂900','PT999'   =>'铂999','PT990'   =>'铂990','PT950'   =>'铂950','18K玫瑰黄'=>'18K金','18K玫瑰白'=>'18K金','18K玫瑰金'=>'18K金','18K黄金'=>'18K金','18K白金'=>'18K金','18K黑金'=>'18K金','18K彩金'=>'18K金','18K红'=>'18K金','18K黄白'=>'18K金','18K分色'=>'18K金','18K黄'=>'18K金','18K白'=>'18K金','9K玫瑰黄'=>'9K金','9K玫瑰白'=>'9K金','9K玫瑰金'=>'9K金','9K黄金'=>'9K金','9K白金'=>'9K金','9K黑金'=>'9K金','9K彩金'=>'9K金','9K红'=>'9K金','9K黄白'=>'9K金','9K分色'=>'9K金','9K黄'=>'9K金','9K白'=>'9K金','10K玫瑰黄'=>'10K金','10K玫瑰白'=>'10K金','10K玫瑰金'=>'10K金','10K黄金'=>'10K金','10K白金'=>'10K金','10K黑金'=>'10K金','10K彩金'=>'10K金','10K红'=>'10K金','10K黄白'=>'10K金','10K分色'=>'10K金','10K黄'=>'10K金','10K白'=>'10K金', '14K玫瑰黄'=>'14K金','14K玫瑰白'=>'14K金','14K玫瑰金'=>'14K金','14K黄金'=>'14K金','14K白金'=>'14K金','14K黑金'=>'14K金','14K彩金'=>'14K金','14K红'=>'14K金','14K黄白'=>'14K金','14K分色'=>'14K金','14K黄'=>'14K金','14K白'=>'14K金','19K黄'=>'19K金','19K白'=>'19K金','19K玫瑰黄'=>'19K金','19K玫瑰白'=>'19K金','19K玫瑰金'=>'19K金','19K黄金'=>'19K金','19K白金'=>'19K金','19K黑金'=>'19K金','19K彩金'=>'19K金','19K红'=>'19K金','19K黄白'=>'19K金','19K分色'=>'19K金','20K黄'=>'20K金','20K白'=>'20K金','20K玫瑰黄'=>'20K金','20K玫瑰白'=>'20K金','20K玫瑰金'=>'20K金','20K黄金'=>'20K金','20K白金'=>'20K金','20K黑金'=>'20K金','20K彩金'=>'20K金','20K红'=>'20K金','20K黄白'=>'20K金','20K分色'=>'20K金','21K黄'=>'21K金','21K白'=>'21K金','21K玫瑰黄'=>'21K金','21K玫瑰白'=>'21K金','21K玫瑰金'=>'21K金','21K黄金'=>'21K金','21K白金'=>'21K金','21K黑金'=>'21K金','21K彩金'=>'21K金','21K红'=>'21K金','21K黄白'=>'21K金','21K分色'=>'21K金','22K黄'=>'22K金','22K白'=>'22K金','22K玫瑰黄'=>'22K金','22K玫瑰白'=>'22K金','22K玫瑰金'=>'22K金','22K黄金'=>'22K金','22K白金'=>'22K金','22K黑金'=>'22K金','22K彩金'=>'22K金','22K红'=>'22K金','22K黄白'=>'22K金','22K分色'=>'22K金','23K黄'=>'23K金','23K白'=>'23K金','23K玫瑰黄'=>'23K金','23K玫瑰白'=>'23K金','23K玫瑰金'=>'23K金','23K黄金'=>'23K金','23K白金'=>'23K金','23K黑金'=>'23K金','23K彩金'=>'23K金','23K红'=>'23K金','23K黄白'=>'23K金','23K分色'=>'23K金','S925黄'=>'S925','S925白'=>'S925','S925玫瑰黄'=>'S925','S925玫瑰白'=>'S925','S925玫瑰金'=>'S925','S925黄金'=>'S925','S925白金'=>'S925','S925黑金'=>'S925','S925彩金'=>'S925','S925红'=>'S925','S925黄白'=>'S925','S925分色'=>'S925','S925'    =>'银925');
    public $tihCtInfo = array('耳环'=>'耳饰','吊坠'=>'吊坠','裸石（镶嵌物）'=>'裸石（镶嵌物）','女戒'=>'戒指','套装'=>'饰品','纪念币'=>'纪念币','素料类'=>'素料类','彩宝'=>'彩宝','彩钻'=>'彩钻','男戒'=>'戒指','赠品'=>'饰品','胸针'=>'饰品','脚链'=>'脚链','情侣戒'=>'戒指','金条'=>'金条','其它'=>'饰品','摆件'=>'摆件','项链'=>'项链','多功能款'=>'饰品','手链'=>'手链','耳钩'=>'耳饰','手表'=>'手表','固定资产'=>'固定资产','长链'=>'饰品','裸石（统包货）'=>'裸石（统包货）','裸石（珍珠）'=>'裸石（珍珠）','套戒'=>'戒指','领带夹'=>'领带夹','手镯'=>'手镯','原材料'=>'原材料','袖口钮'=>'饰品','耳钉'=>'耳饰','物料'=>'物料','其他'=>'饰品','耳饰'=>'耳饰');
    public $stoneInfo = array('红玛瑙'=>'玛瑙','和田玉'=>'和田玉','星光石'=>'星光石','莹石'=>'莹石','捷克陨石'=>'捷克陨石','绿松石'=>'绿松石','欧泊'=>'欧泊','砗磲'=>'砗磲','芙蓉石'=>'芙蓉石','坦桑石'=>'坦桑石','南洋白珠'=>'珍珠','大溪地珍珠'=>'珍珠','南洋金珠'=>'珍珠','无'=>'','黑玛瑙'=>'玛瑙','托帕石'=>'托帕石','橄榄石'=>'橄榄石','红纹石'=>'红纹石','蓝宝石'=>'蓝宝石','祖母绿'=>'祖母绿','黄水晶'=>'水晶','玉髓'=>'玉髓','异形钻'=>'钻石','粉红宝'=>'粉红宝','彩钻'=>'钻石','尖晶石'=>'尖晶石','石榴石'=>'石榴石','贝壳'=>'贝壳','珍珠贝'=>'贝壳','圆钻'=>'钻石','碧玺'=>'碧玺','葡萄石'=>'葡萄石','拉长石（月光石）'=>'拉长石（月光石）','舒俱来石'=>'舒俱来石','琥珀'=>'琥珀','黑钻'=>'钻石','混搭珍珠'=>'珍珠','碧玉'=>'','紫龙晶'=>'紫龙晶','玛瑙'=>'玛瑙','青金石'=>'青金石','虎睛石（木变石）'=>'虎睛石（木变石）','黑曜石'=>'黑曜石','珍珠'=>'珍珠','红宝石'=>'红宝石','其它'=>'','海蓝宝'=>'海蓝宝石','水晶'=>'水晶','翡翠'=>'翡翠','孔雀石'=>'孔雀石','东陵玉'=>'东陵玉','锂辉石'=>'锂辉石','珊瑚'=>'珊瑚','海水香槟珠'=>'珍珠','淡水白珠'=>'珍珠','锆石'=>'合成立方氧化锆','月光石'=>'月光石');
	protected function init ($act,$c)
	{
		if(!Util::isAjax())
		{
			if(!in_array($act,$this->whitelist))
			{
				Route::setAsAutoLaunch($_SERVER['REQUEST_URI'], _Request::getString("title", "仓储管理"));
				header('Location:/index.php');
				die();
			}
		}
		else
		{
			if($c!='Main')
			{
				if(!Auth::getMenuAuth($c)){
					die('没有菜单权限');
				}
				if(!Auth::getOperationAuth($c,$act))
				{					
					Util::jsonExit("Limited permissions 没有操作权限");					
					exit();
					//die('没有操作权限');
				}
			}
		}

		if( (Auth::getValFromEnv('isHouseKeeper', '-1') == '-1')&&(!isset($_GET['kela'])))
		{
			die('不是库管不能访问');exit;
		}

		$this->dd = new DictView(new DictModel(1));
		$this->assign('dd',$this->dd);//数据字典
	}
	public function index ($params){
		die('forbidden');
	}

    //通过仓库id 来获取挂靠的公司信息和仓库基本信息
    public function getCompanyInfo($warehouseid){
        $wmodel = new WarehouseBillInfoLModel(21);
        return  $wmodel->getCompanyInfo($warehouseid);
    }

	//获取所有有权限的仓库所对应的公司列表
	public function getCompanyList(){
		$company_list = array();
		//$qxwarehouse_ids = $this->WarehouseListO();
	    if($_SESSION['userType']==1){
            $qxwarehouse_ids = true;
        } else  {
            $sql  = "select house_id from cuteframe.user_warehouse uw inner join warehouse_shipping.warehouse w on w.id = uw.house_id and w.is_delete = 1
where uw.user_id = {$_SESSION['userId']} ";    
           $house_ids = DB::cn(1)->getAll($sql);
           $qxwarehouse_ids = array_column($house_ids, 'house_id');
        }
		
		//$sql = "SELECT distinct(concat(`wr`.`company_id`,'|',`wr`.`company_name`)) as w,`wr`.`company_id`,`wr`.`company_name` FROM `warehouse_rel` as `wr` LEFT JOIN `warehouse` as `w` ON `w`.`id`=`wr`.`warehouse_id` where w.is_delete = 1 ";
		$sql = "SELECT distinct(`wr`.`company_id`)   FROM `warehouse_rel` as `wr` LEFT JOIN `warehouse` as `w` ON `w`.`id`=`wr`.`warehouse_id` where w.is_delete = 1 ";
		if($qxwarehouse_ids === true)//如果是超级管理员，取全部的公司
		{
			$sql = "select concat(`id`,'|',`company_name`) as w,id,company_sn,company_name from company where is_deleted = 0";
			//$company_list = DB::cn(21)->getAll($sql);
			$company_list = DB::cn(1)->getAll($sql);

		}elseif(count($qxwarehouse_ids))//不是超级管理员 并且有仓库权限，返回仓库对应的公司列表
		{
			$warehouse_ids_str = implode(',',$qxwarehouse_ids);
			$sql .= " AND `w`.id in(".$warehouse_ids_str.")";
			//$company_list = DB::cn(21)->getAll($sql);
			$company_arr = DB::cn(21)->getAll($sql);
			$company_arr = array_column($company_arr,'company_id');
			$company_str = implode(',',$company_arr);
			$sql = "select concat(`id`,'|',`company_name`) as w,id,company_sn,company_name from company where is_deleted = 0 and id in(".$company_str.")";
			$company_list = DB::cn(1)->getAll($sql);
		}
		return $company_list;
    }

//校验不同仓库的不同权限的方法  给出当前仓库id 校验是否有操作权限
//有权限返回true 没有权限返回权限名称
    public function checkSession($warehouseid){
       // print_r($_SESSION['__operation_p']);
        if($_SESSION['userType']==1){
            return true;
        }
        $pre = '/([A-Z]{1})/';
        $res =preg_replace($pre,'_$1',$_GET['con']);
        $con =substr($res,1);
        $act = $_GET['act'];
        $act =preg_replace($pre,'_$1',$act);
        $pricheck =strtoupper($con.'_'.$act.'_O');
        $sql = "SELECT `name` FROM permission WHERE code='".$pricheck."'";
        //这里会有找不到权限的情况 所以当你要鉴定权限的时候请先保证这个操作是管控的
        $res = DB::cn(1)->getOne($sql);

		//判断所传仓库是否在本人有权限的仓库内存在。没有，则提示
		$warehouselist = $this->WarehouseListO();
		if(!in_array($warehouseid,$warehouselist))
		{
			return $res;
		}
		//判断是否有权限
        $pris = array_flip($_SESSION['__operation_p'][2][$warehouseid]);
        if(in_array($pricheck,$pris)){
            return true;
        }
        return $res;
    }

    //返回这个操作权限的的仓库数组
    public function WarehouseListO(){
        //由于仓库单据添加功能和菜单组是绑定的
        if($_SESSION['userType']==1){
            return true;
        }
        $pre = '/([A-Z]{1})/';
        $res =preg_replace($pre,'_$1',$_GET['con']);
        $con =substr($res,1);
        $act = $_GET['act'];
        $act =preg_replace($pre,'_$1',$act);
        $pricheck =strtoupper($con.'_'.$act.'_O');

        $pris = $_SESSION['__operation_p'][2];

        $warehousearr=array();
        foreach($pris as $key=>$val){
            if(array_key_exists($pricheck,$val)){
                $warehousearr[]=$key;
            }
        }
        return $warehousearr;

    }

	public function getPremissionCode(){
		$pre = '/([A-Z]{1})/';
		$res =preg_replace($pre,'_$1',$_GET['con']);
		$con =substr($res,1);
		$act = $_GET['act'];
		$act =preg_replace($pre,'_$1',$act);
		$pricheck =strtoupper($con.'_'.$act.'_O');
		return $pricheck;
	}

	public function checkHosePremission($warehouse_id){
		if($_SESSION['userType']==1){return true;}

		$pre_code = $this->getPremissionCode();
		$sql = "SELECT `name` FROM permission WHERE code='".$pre_code."' AND `is_deleted` = 0";
		$pre_name = DB::cn(1)->getOne($sql);
		if(!$pre_name){
			return true;
		}

		if(!isset($_SESSION['__operation_p'][2][$warehouse_id])){
			$w_view = new WarehouseView(new WarehouseModel($warehouse_id,21));
			$w_name = $w_view->get_name();
			return "您没有".$w_name."操作权限";
		}

		if(!array_key_exists($pre_code,$_SESSION['__operation_p'][2][$warehouse_id])){
			return "您没有".$pre_name;
		}else{
			return true;
		}

	}

	/**
	 * 获取当前用户拥有权限的仓库Ids
	 */
	public function getPremissHouse(){
		$houses = array();
		if($_SESSION['userType']==1){
			$houseModel = new WarehouseModel(21);
			$houses =$houseModel->getAllhouse();

		}
		if(isset($_SESSION['__operation_p'][2]) && !empty($_SESSION['__operation_p'][2])){
			$i = 0;
			foreach ($_SESSION['__operation_p'][2] as $k=>$v) {
				$houses[$i]['id'] = $k;
				//$w_view = new WarehouseView(new WarehouseModel($k,21));
				//$w_name = $w_view->get_name();

				$w_model = new WarehouseModel(21);
				$winfo = $w_model->select2('name','is_delete = 1 and id ='.$k,'row');
				if(count())
				{
					$houses[$i]['name'] = $winfo['name'];
				}else{
					unset($houses[$i]);
				}
				$i++;
			}
		}
		return $houses;
	}

	public function getOnlyMasterCompanyWarehouse()
	{
		$houseModel = new WarehouseModel(21);
		$master_company_id = 58;
        $warehouse = $houseModel->getMasterWarehouse($master_company_id);
        return $warehouse;
	}

	//返回这个操作权限的的去渠道数组
	public function ChannelListO(){
		if($_SESSION['userType']==1){
			return true;
		}
		/*
		$pre = '/([A-Z]{1})/';
		$res =preg_replace($pre,'_$1',$_GET['con']);
		$con =substr($res,1);
		$act = $_GET['act'];
		$act =preg_replace($pre,'_$1',$act);
		$pricheck =strtoupper($con.'_'.$act.'_O');

		$pris = $_SESSION['__operation_p'][3];

		$channelarr=array();
		foreach($pris as $key=>$val){
			if(array_key_exists($pricheck,$val)){
				$channelarr[]=$key;
			}
		}
		return $channelarr;*/
		$sql  = "select channel_id from cuteframe.user_channel where user_id = {$_SESSION['userId']} ";
        $channel_ids = DB::cn(1)->getAll($sql);
        return array_column($channel_ids, 'channel_id');

	}

	function getchannelinfo(array $channerarray){
		$channelModel = new SalesChannelsModel(1);
		return $channelModel->getSalesChannel($channerarray);
	}
   
	public function isViewChengbenjia(){
		if(SYS_SCOPE=='zhanting' &&  Auth::user_is_from_base_company()==false){
			return false;
		}else{
			
			return true;
		}
	}

    public function get_detail_view_bar_new($model='') {
          
          //如果不需要进行签收，则移除签收按钮
          $bar = Auth::build_view_bar(array('view_p_pifajia' =>true, 'show_p_mingyichengben' => true,'show_caigou_price'=>true,'check_h_shijia'=>true), 'WAREHOUSE_BILL_INFO_P_M');
          $show_pifajia = SYS_SCOPE == "boss";
          $show_p_mingyichenggben = SYS_SCOPE == "boss";
          $show_caigou_price = SYS_SCOPE == "boss";
          $check_h_shijia = false;
          if (is_array($bar)) {
            $show_pifajia = in_array('view_p_pifajia', $bar[1]);
            $show_p_mingyichenggben = in_array('show_p_mingyichengben', $bar[1]);
            $show_caigou_price = in_array('show_caigou_price', $bar[1]);
            $check_h_shijia = in_array('check_h_shijia', $bar[1]);
          }
          return array($show_pifajia, $show_p_mingyichenggben, $show_caigou_price, $check_h_shijia);
      }

    //验证是否可以查看采购价
    public function checkBillHCaiGouJia($id)
    {
        $WarehouseBillModel = new WarehouseBillModel($id,21);
        $company_model = new CompanyModel(1);
        $is_company = Auth::user_is_from_base_company();
        $do = $WarehouseBillModel->getDataObject();
        $is_show_caigoujia = true;//是否可以查看采购价
        $to_is_shengdai = false;
        $from_is_shengdai = false;
        $to_company_id   = $do['to_company_id'];
        $from_company_id = $do['from_company_id'];
        $companyId = $_SESSION['companyId'];//当前所在公司
        //经销商，个体店，直营店隐藏列表的采购价，单头的成本总计；
        $is_shengdai = $company_model->select2(' count(*) ' , " is_deleted = 0 and id = '{$companyId}' and is_shengdai =1" , $type = '3');
        //如果是总部批发给省代的单据，省代查看时隐藏采购价（不受权限管控，就是不显示）
        //入库公司是否总公司
        if(in_array($to_company_id,array('58','445', '515'))){
            $to_is_shengdai = true;
        }
        //出库公司是否省代
        if($from_company_id){
            $res = $company_model->select2(' `is_shengdai` ' , " is_deleted = 0 and id = '{$from_company_id}'" , $type = '3');
            if($res == '1') $from_is_shengdai = true;
        }
        if($from_is_shengdai == true && $to_is_shengdai == true && $is_shengdai != false) $is_show_caigoujia = false;
        //非总公司、非省代的  采购成本和名义成本不能看且不受权限管控，批发价受权限管控；
        if(!$is_company && $is_shengdai == false) $is_show_caigoujia = false;
        return $is_show_caigoujia;
    }

    //验证当前用户是那种级别用户
    //1.总公司 、2.经销商，个体，直营 、3.省代  
    public function verifyUserLevel()
    {
        $res = array('level' => 2,'dataCompInfo' => array());
        $companyId = $_SESSION['companyId'];
        if(!empty($companyId)){
            $is_company = Auth::is_base_company($companyId);
            if($is_company){
                $res['level'] = 1;
                return $res;
            }
            $company_model = new CompanyModel(1);
            $checkshengdai = $company_model->select2(' `id` ' , " is_shengdai = '1' and id ='{$companyId}' " , $type = '2');
            if(!empty($checkshengdai)){
                //如果是省代则取出改省代公司下的公司
                $rCop = $company_model->select2(' `id` ' , " sd_company_id ='{$companyId}' " , $type = '1');
                $rCop = array_column($rCop,'id');
                array_push($rCop, $companyId);
                $res['level'] = 3;
                $res['dataCompInfo'] = $rCop;
                return $res;//省代
            }
        }
        $res['dataCompInfo'] = array($companyId);
        return $res;
    }
}
?>