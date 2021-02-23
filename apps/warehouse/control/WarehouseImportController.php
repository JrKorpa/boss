<?php
/**
 *  仓库数据导入
 *  -------------------------------------------------
 *   @file		: WarehouseImportController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng <gaopeng@kela.cn>
 *   @date		: 2015-09-12 11:04:15
 *   @update	:
 *  -------------------------------------------------
 */

class WarehouseImportController extends CommonController
{
	protected $whitelist = array();
	
	/**
	 * 老系统商品批量导入新系统,html页面
	 * @param unknown $params
	 */
	public function goodsImport($params)
	{
		$this->render('warehouse_goods_import.html',array());
	}
	/**
	 * 老系统商品批量导入新系统,批量导入处理
	 * @param unknown $params
	 */
	public function doGoodsImport($params){
	    set_time_limit(3600);
	    $old_conf = array(
	        'dsn'=>"mysql:host=192.168.1.61;dbname=jxc",
	        'user'=>"kela_jxc",
	        'password'=>"kela$%jxc",
	    ); 
/*	    $old_conf = array(
	        'dsn'=>"mysql:host=192.168.10.23;dbname=warehouse_shipping",
	        'user'=>"root",
	        'password'=>"123456",
	    );	*/    
	    $oldPdo = new PDO($old_conf['dsn'], $old_conf['user'], $old_conf['password'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
	    
	    $result['content'] = '';
	    /************检查批量商品货号合法性*************/ 
	    if(empty($params['goods_ids'])){
	        $result['error'] = "批量商品货号为空";
	        Util::jsonExit($result);
	    }	
	    $goods_id_split = explode("\n",$params['goods_ids']);	     
	    //获取最终合法订单编号
	    $goods_id_list = array();
	    foreach($goods_id_split as $vo){
	        //获取不为空和不重复的订单id
	        if(trim($vo)!='' && !in_array($vo,$goods_id_list)){
	            $goods_id_list[]=trim($vo);
	        }
	    }
	    if(empty($goods_id_list)){
	        $result['error'] = "批量合法商品货号为空";
	        Util::jsonExit($result);
	    } 
	    
	   
	    /****************检查老系统货号可用性*****************/
	    $errors = array();
	    $data   = array();
	    $warehouses= Util::eexplode(',', $_SESSION['userWareList']);

	    foreach($goods_id_list as $goods_id){
	        $sql = "SELECT * FROM `jxc_goods` WHERE `goods_id` = '".$goods_id."' ";
	        $obj = $oldPdo->query($sql);
	        $row = $obj->fetch(PDO::FETCH_ASSOC);
	        if(empty($row)){
	            $errors[$goods_id][] = "老系统不存在此货号";
	            continue;
	        }
	        
	        if($row['is_on_sale'] !=1){
	            $errors[$goods_id][] = "老系统商品不是库存状态";
	            continue;
	        }
	        if($_SESSION['userType']!=1 && !in_array($row['warehouse'],$warehouses)){
	            $errors[$goods_id][] = "不是你公司库房的商品";
	            continue;	        	
	        }
	        $data[$goods_id] = $row;
	    }	    
	    
	    if(!empty($errors)){
	        $result['content'] = "<b>批量导入验证失败！</b><hr/>";
	        foreach($errors as $key=>$vo1){
                 $result['content'].="{$key}：";
                 foreach ($vo1 as $vo2){
                     $result['content'] .='【'.$vo2.'】,';
                 }
                 $result['content'] =trim($result['content'],',')."<hr/>";
             }
	        $result['content'] =trim($result['content'],',');
	        $result['error'] = "老系统商品导入验证失败,共有【".count($errors)."】个不符条件的货号";
	        Util::jsonExit($result);
	    }else{
	        
	        $model = new WarehouseImportModel(21);
	        $result = $model->addGoodsData($data,$oldPdo);
	        
	        if($result == false){
	        	echo "导入失败！";exit();
	            $result['error'] = "批量导入失败，事物已回滚！";
	            Util::jsonExit($result);
	            exit();
	        }else{
	        	echo "导入成功！";exit();
    	        $result['content'] = "<b>批量导入成功！总计:【".count($data).'】个</b><hr/>';
    	        foreach($data as $key=>$vo){
    	            $result['content']="导入成功！<hr/>";
    	        }
    	        $result['success'] = 1;
    	        Util::jsonExit($result);
    	        
	        }
	    }
	    
	}
	
}
?>