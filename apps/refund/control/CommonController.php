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
	protected function init ($act,$c) 
	{
		if(!Util::isAjax())
		{
			if(!in_array($act,$this->whitelist))
			{
				Route::setAsAutoLaunch($_SERVER['REQUEST_URI'], _Request::getString("title", "退款管理"));
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
					die('没有操作权限');
				}
			}
		}
		$this->dd = new DictView(new DictModel(1));
		$this->assign('dd',$this->dd);//数据字典
	}
	public function index ($params){
		die('forbidden');
	}
    
    //获取所有有权限的仓库所对应的公司列表
	public function getCompanyList(){
		$company_list = array();
		$qxwarehouse_ids = $this->WarehouseListO();

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
    
    
    //返回这个操作权限的的仓库数组
    public function WarehouseListO(){
        //由于仓库单据添加功能和菜单组是绑定的
        if($_SESSION['userType']==1 || true){
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
    
    
}
?>