<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiProModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	:
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ImportDataModel extends Model
{
	function __construct ($id,$strConn="")
	{
		parent::__construct($id,$strConn);
	}
	function addGoodsData($goods_list)
	{
                Util::L($goods_list,'zhangruiying.txt');
		//入库方式 旧 => 新
		$put_in_type = array('1','2','3','4');
		$time = date('Y-m-d H:i:s');
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			foreach($goods_list as $key => $value)
			{
				$sql = "select count(1) from `warehouse_goods` where goods_id = ".$value['goods_id'];
				$c = $this->db()->getOne($sql);
				if($c)//如果已经导入了，重复的，就跳过。
				{
					continue;
				}
				$comModel = new CompanyModel(1);
				$company_name = $comModel->getCompanyName($value['company']);

				$sql = "select name from `warehouse` where id = ".$value['warehouse'];
				$warehouse_name = $this->db()->getOne($sql);
				$sql_arr=array(
					'goods_id'=>$value['goods_id'],
					'goods_sn'=>$value['goods_sn'],
					'buchan_sn'=>'',
					'order_goods_id'=>0,
					'product_type'=>$value['shipin_type'],
					'cat_type'=>$value['kuanshi_type'],
					'is_on_sale'=>$value['is_on_sale'] == 100?1:100,
					'prc_id'=>$value['prc_id']?$value['prc_id']:0,
					'prc_name'=>$value['prc_name'],
					'mo_sn'=>$value['mo_sn'],
					'put_in_type'=>isset($put_in_type[$value['storage_mode']])?$put_in_type[$value['storage_mode']]:0,
					'goods_name'=>$value['goods_name'],
					'company'=>$company_name,
					'warehouse'=>$warehouse_name,
					'company_id'=>$value['company']?$value['company']:0,
					'warehouse_id'=>$value['warehouse']?$value['warehouse']:0,
					'caizhi'=>$value['zhuchengse'],
					'jinzhong'=>$value['zhuchengsezhong']?$value['zhuchengsezhong']:'0.00',
					'jinhao'=>$value['jinhao'],
					'zhushi'=>$value['zhushi'],
					'zhuchengsezhongjijia'=>$value['zhuchengsezhongjijia']?$value['zhuchengsezhongjijia']:'0.00',
					'zhuchengsemairudanjia'=>$value['zhuchengsemairudanjia']?$value['zhuchengsemairudanjia']:'0.00',
					'zhuchengsemairuchengben'=>$value['zhuchengsemairuchengben']?$value['zhuchengsemairuchengben']:'0.00',
					'zhuchengsejijiadanjia'=>$value['zhuchengsejijiadanjia']?$value['zhuchengsejijiadanjia']:'0.00',
					'zhushilishu'=>$value['zhushilishu'],
					'zuanshidaxiao'=>$value['zhushizhong']?$value['zhushizhong']:'0.00',
					'zhushizhongjijia'=>$value['zhushizhongjijia'],
					'zhushiyanse'=>$value['zhushiyanse'],
					'zhushijingdu'=>$value['zhushijingdu'],
					'zhushimairudanjia'=>$value['zhushimairudanjia']?$value['zhushimairudanjia']:'0.00',
					'zhushimairuchengben'=>$value['zhushimairuchengben']?$value['zhushimairuchengben']:'0.00',
					'zhushijijiadanjia'=>$value['zhushijijiadanjia']?$value['zhushijijiadanjia']:'0.00',
					'zhushiqiegong'=>$value['zhushiqiegong'],
					'zhushixingzhuang'=>$value['zhushixingzhuang'],
					'zhushibaohao'=>$value['zhushibaohao'],
					'zhushiguige'=>$value['zhushiguige'],
					'fushi'=>$value['fushi'],
					'fushilishu'=>$value['fushilishu'],
					'fushizhong'=>$value['fushizhong']?$value['fushizhong']:'0.00',
					'fushizhongjijia'=>$value['fushizhongjijia'],
					'fushiyanse'=>$value['fushiyanse'],
					'fushijingdu'=>$value['fushijingdu'],
					'fushimairuchengben'=>$value['fushimairuchengben']?$value['fushimairuchengben']:'0.00',
					'fushimairudanjia'=>$value['fushimairudanjia']?$value['fushimairudanjia']:'0.00',
					'fushijijiadanjia'=>$value['fushijijiadanjia']?$value['fushijijiadanjia']:'0.00',
					'fushixingzhuang'=>$value['fushixingzhuang'],
					'fushibaohao'=>$value['fushibaohao'],
					'fushiguige'=>$value['fushiguige'],
					'zongzhong'=>$value['zongzhong'],
					'mairugongfeidanjia'=>$value['mairugongfeidanjia']?$value['mairugongfeidanjia']:'0.00',
					'mairugongfei'=>$value['mairugongfei']?$value['mairugongfei']:'0.00',
					'jijiagongfei'=>$value['jijiagongfei']?$value['jijiagongfei']:'0.00',
					'shoucun'=>$value['shoucun']?$value['shoucun']:0,
					'ziyin'=>$value['ziyin'],
					'danjianchengben'=>$value['danjianchengben']?$value['danjianchengben']:'0.00',
					'peijianchengben'=>$value['peijianchengben']?$value['peijianchengben']:'0.00',
					'qitachengben'=>$value['qitachengben']?$value['qitachengben']:'0.00',
					'yuanshichengbenjia'=>$value['yuanshichengbenjia']?$value['yuanshichengbenjia']:'0.00',
					'chengbenjia'=>$value['chengbenjia']?$value['chengbenjia']:'0.00',
					'jijiachengben'=>$value['jijiachengben'],
					'jiajialv'=>$value['jiajialv']?$value['jiajialv']:'0.00',
					'kela_order_sn'=>$value['kela_order_sn'],
					'zuixinlingshoujia'=>$value['zuixinlingshoujia']?$value['zuixinlingshoujia']:'0.00',
					'pinpai'=>$value['pinpai'],
					'changdu'=>$value['changdu'],
					'zhengshuhao'=>$value['zhengshuhao'],
					'zhengshuhao2'=>$value['zhengshuhao2'],
					'yanse'=>$value['zhushiyanse'],
					'jingdu'=>$value['zhushijingdu'],
					'peijianshuliang'=>$value['peijianshuliang'],
					'guojizhengshu'=>$value['guojizhengshu'],
					'zhengshuleibie'=>$value['zhengshuleibie'],
					'gemx_zhengshu'=>$value['gemx_zhengshu'],
					'num'=>1,
					'addtime'=>$time,
					'shi2'=>$value['shi2'],
					'shi2lishu'=>$value['shi2lishu'],
					'shi2zhong'=>$value['shi2zhong']?$value['shi2zhong']:'0.00',
					'shi2zhongjijia'=>$value['shi2zhongjijia'],
					'shi2mairudanjia'=>$value['shi2mairudanjia']?$value['shi2mairudanjia']:'0.00',
					'shi2mairuchengben'=>$value['shi2mairuchengben']?$value['shi2mairuchengben']:'0.00',
					'shi2jijiadanjia'=>$value['shi2jijiadanjia']?$value['shi2jijiadanjia']:'0.00',
					'qiegong'=>$value['qiegong'],
					'paoguang'=>$value['paoguang'],
					'duichen'=>$value['duichen'],
					'yingguang'=>$value['yingguang'],
					'mingyichengben'=>$value['xianzaichengben']?$value['xianzaichengben']:'0.00',
					'xianzaixiaoshou'=>$value['xianzaixiaoshou']?$value['xianzaixiaoshou']:'0.00',
					'zuanshizhekou'=>$value['zuanshizhekou'],
					'guojibaojia'=>$value['guojibaojia'],
					'gongchangchengben'=>$value['gongchangchengben'],
					'account'=>$value['account']?$value['account']:0,
					'account_time'=>$value['account_time'],
					'tuo_type'=>$value['tuo_type']?$value['tuo_type']:0,
					'att1'=>$value['att1'],
					'att2'=>$value['att2'],
					'huopin_type'=>$value['huopin_type']?$value['huopin_type']:0,
					'dia_sn'=>$value['dia_sn'],
					'zhushipipeichengben'=>$value['zhushipipeichengben']?$value['zhushipipeichengben']:'0.00',
					'biaoqianjia'=>$value['biaoqianjia']?$value['biaoqianjia']:'0.00',
					'jietuoxiangkou'=>$value['jietuoxiangkou']?$value['jietuoxiangkou']:'0.000',
					'caigou_chengbenjia'=>$value['caigou_chengbenjia']?$value['caigou_chengbenjia']:'0.00',
					'box_sn'=>$value['tmp_sn']?$value['tmp_sn']:'0-00-0-0',
					'pass_sale'=>1,
					'old_set_w'=>1,
					'weixiu_status'=>$value['weixiu_status']?$value['weixiu_status']:0,
					'jiejia'=>$value['account']?$value['account']:0,
					'oldsys_id'=>$value['id']?$value['id']:0
					);
				$sql = "INSERT INTO `warehouse_goods` ("."`".implode('`,`',array_keys($sql_arr))."`".") VALUES ("."'".implode("','",array_values($sql_arr))."'".");";
				//file_put_contents('c:/sql.txt',$sql."\r\n",FILE_APPEND);
				$pdo->query($sql);
				$box_id = 0;//柜位默认为0
				//如果旧系统货品有柜位,查询现在系统是否有这个柜位,没有则创建并取ID，新系统有这个柜位则取出来ID
				if($sql_arr['box_sn'] != "")
				{
					//取柜位ID
					$sql = "SELECT id,is_deleted from warehouse_box where box_sn = '".$sql_arr['box_sn']."' and warehouse_id={$sql_arr['warehouse_id']}";
					//file_put_contents('c:/sql.txt',$sql."\r\n",FILE_APPEND);
					$row= $this->db()->getRow($sql);
					if(!$row)//没有此柜位创建柜位
					{
						$sql = "INSERT INTO `warehouse_box` (`warehouse_id`, `box_sn`, `create_time`, `create_name`, `is_deleted`, `info`) VALUES ({$sql_arr['warehouse_id']},'{$sql_arr['box_sn']}','{$time}','SYSTEM',1,'系统生成')";
						//file_put_contents('c:/sql.txt',$sql."\r\n",FILE_APPEND);
						$pdo->query($sql);
						$box_id = $pdo->lastInsertId();
					}
					else
					{
						$box_id=$row['id'];
						//如果导入的产品所在库位是禁用就启用该库位
						if($row['is_deleted']==2)
						{
							$sql="update warehouse_box set is_deleted=1 where id={$row['id']}";
							//file_put_contents('c:/sql.txt',$sql."\r\n",FILE_APPEND);
							$pdo->query($sql);
						}
					}
				}
				//有柜位ID 然后建立柜位和货品的关系，没有柜位则为0，只建立和仓库的关系
				$sql = "INSERT INTO `goods_warehouse` (`good_id`, `warehouse_id`, `box_id`, `add_time`, `create_time`, `create_user`) VALUES ('{$sql_arr['goods_id']}',{$sql_arr['warehouse_id']},{$box_id},'{$time}','{$time}','SYSTEM')";
				//file_put_contents('c:/sql.txt',$sql."\r\n",FILE_APPEND);
				$pdo->query($sql);
			}
		}
		catch(Exception $e){//捕获异常
                        Util::L($e,'zhangruiying.txt');
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}

	function addCompanyData($list)
	{
		$pdo = $this->db()->db();//pdo对象
		$time = time();

		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			foreach($list as $key => $value)
			{
				$sql = "select count(1) from `company` where `id` = ".$value['p_id'];
				$c = $this->db()->getOne($sql);
				if($c)//如果已经导入了，重复的，就跳过。
				{
					continue;
				}

				$is_deleted = $value['status']?0:1;
				$sql = "INSERT INTO `company`(`id`, `company_sn`, `company_name`, `parent_id`, `contact`, `phone`, `address`, `bank_of_deposit`, `account`, `receipt`, `is_sign`, `remark`, `create_user`, `create_time`, `is_deleted`, `is_system`) VALUES ({$value['p_id']},'{$value['p_sn']}','{$value['p_name']}',1,'无','无','无','无','无',0,1,NULL,1,'{$time}',{$is_deleted},0)";
				$pdo->query($sql);
			}
		}
		catch(Exception $e){//捕获异常

			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}

	function addWarehouseData($list)
	{
		$pdo = $this->db()->db();//pdo对象
		$time = date('Y-m-d H:i:s');

		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			$comModel = new CompanyModel(1);

			foreach($list as $key => $value)
			{
				$sql = "select count(1) from `warehouse` where `id` = ".$value['wh_id'];
				$c = $this->db()->getOne($sql);
				if($c)//如果已经导入了，重复的，就跳过。
				{
					continue;
				}
				$sql = "INSERT INTO `warehouse`(`id`, `name`, `code`, `remark`, `create_time`, `create_user`, `is_delete`, `lock`) VALUES ({$value['wh_id']},'{$value['wh_name']}','{$value['wh_sn']}','无','{$time}','SYSTEM',1,0)";
				$pdo->query($sql);

				$company_name = $comModel->getCompanyName($value['p_id']);
				$sql = "INSERT INTO `warehouse_rel`(`id`, `warehouse_id`, `company_id`, `create_time`, `company_name`) VALUES (NULL,{$value['wh_id']},{$value['p_id']},'{$time}','{$company_name}')";
				$pdo->query($sql);
			}
		}
		catch(Exception $e){//捕获异常

			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}
}

?>