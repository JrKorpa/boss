<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-09 12:40:07
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'purchase_goods';
        $this->_dataObject = array("id"=>"采购商品ID",
"pinfo_id"=>"采购单ID",
"style_sn"=>"款号",
"product_type_id"=>"产品线ID",
"cat_type_id"=>"款式分类ID",
"num"=>"数量",
'xiangqian'=>'镶嵌方式',
"is_urgent"=>"是否加急",
"info"=>"备注");
		parent::__construct($id,$strConn);
	}

	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";

		if($where['pinfo_id'] !== "")
		{
			$sql .= " AND pinfo_id = ".$where['pinfo_id'];
		}
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
                //根据获取到的款号，获取相关属性信息
                $proApi = new ApiProcessorModel();
                $styleApi = new ApiStyleModel();

                foreach ($data['data'] as $k => $v){
                    $g_id = $v['id'];
                    $image = $styleApi->getStyleGallery(array('style_sn' => $v['style_sn'],'image_place' => 1));

                    if (isset($image[0]['thumb_img'])){
                        $data['data'][$k]['image_url'] = $image[0]['thumb_img'];
                    }
                    $result = $this->getPurchaseGoodsAttr($g_id);

                    //$result = $proApi->GetAttrByStylesn(array('style_sn'), array($style_sn));
                    $sql ="select * from kela_supplier.product_info where p_id={$v['id']} and from_type=1";
                    $bc_info=$this->db()->getRow($sql);
                    $data['data'][$k]['bc_sn']='';
                    $data['data'][$k]['bc_status']='';
                    if(!empty($bc_info)){
                        $data['data'][$k]['bc_sn'] = $bc_info['bc_sn'];
                        $data['data'][$k]['bc_status'] = $bc_info['status'];
                    }
                    foreach ($result as $k1 =>$v1 ){
                        if(isset($v1['code']) && $v1['code']!= ''){
                            $code = $v1['code'];
                            $value = $v1['value'];
                            $data['data'][$k]["$code"] = $value; 
                        }
                        
                    }
                    
                    
                }

		return $data;
	}

         //获取采购货品的属性
        public function getPurchaseGoodsAttr($g_id){
            $sql = "select `id`,`code`,`name`,`value` from `purchase_goods_attr` where `g_id`=".$g_id;
            $result = $this->db()->getAll($sql);
            return $result;
        }
	//根据采购单ID取明细个数
	function getCountForPid($id)
	{
		$sql = "SELECT count(*) from ".$this->table()." WHERE pinfo_id = ".$id;
		$c = $this->db()->getOne($sql);
		return $c;
	}

	//根据采购单ID 取总数量
	function getSum_num($id)
	{
		$sql = "SELECT sum(num) FROM ".$this->table()." WHERE pinfo_id = ".$id;
		return $this->db()->getOne($sql);
	}

	/*根据采购单ID获取采购明细列表*/
	function get_data_goods ($id)
	{
		$sql = "SELECT `id`, `pinfo_id`, `style_sn`, `product_type_id`, `cat_type_id`, `num`, `is_urgent`, `info`,`xiangqian`,`consignee`  FROM `".$this->table()."` WHERE pinfo_id = ".$id;
		$data = $this->db()->getAll($sql);
		return $data;
	}
	function getAttrInfoByCode($id,$code)
	{   
	    $sql = "SELECT `value` FROM purchase_goods_attr where g_id ={$id} and code='{$code}'";
		$data = $this->db()->getOne($sql);
		return $data;
	}

	public function insertPurGoods($data){

		$pdo = $this->db()->db();
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			//写入 purchase_goods 表   [pinfo_id,style_sn,num,xiangqian]
			$good['pinfo_id'] = $pdo->quote($data['pinfo_id']);
			$good['style_sn'] = $pdo->quote($data['style_sn']);
			$good['g_name'] = $pdo->quote($data['g_name']);
			$good['num'] = $pdo->quote($data['num']);
			$good['info'] = $pdo->quote($data['info']);
			$good['xiangqian'] = $pdo->quote($data['xiangqian']);
			$good['consignee']=$pdo->quote($data['consignee']);
			$sql = "INSERT INTO `purchase_goods` (".implode(',',array_keys($good)).") VALUES (".implode(',',$good).")";
			$pdo->exec($sql);

			$last_id = $pdo->lastInsertId();
			//写入 purchase_goods_attr 表
			$sql = "INSERT INTO `purchase_goods_attr` (g_id,code,name,value) VALUES (:g_id,:code,:name,:value)";
			$stmt = $pdo->prepare($sql);
			//$code = '';$name='';$value='';
			$stmt->bindParam(':g_id', $g_id);
			$stmt->bindParam(':code', $code);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':value', $value);
			$g_id = $last_id;
			if(isset($data['caizhi'])){
				$code = 'caizhi';$name = '材质';$value = $data['caizhi'];$stmt->execute();
			}
			if(isset($data['zuanshidaxiao'])){
				$code = 'zuanshidaxiao';$name = '主石单颗重';$value = $data['zuanshidaxiao'];$stmt->execute();
			}
			if(isset($data['zhushi_num'])){
			    $code = 'zhushi_num';$name = '主石粒数';$value = $data['zhushi_num'];$stmt->execute();
			}
			if(isset($data['xiangkou'])){
				$code = 'xiangkou';$name = '镶口';$value = $data['xiangkou'];$stmt->execute();
			}
			if(isset($data['face_work'])){
				$code = 'face_work';$name = '表面工艺';$value = $data['face_work'];$stmt->execute();
			}
			if(isset($data['zhengshuhao'])){
				$code = 'zhengshuhao';$name = '证书号';$value = $data['zhengshuhao'];$stmt->execute();
			}
			if(isset($data['cert'])){
			    $code = 'cert';$name = '证书类型';$value = $data['cert'];$stmt->execute();
			}
			if(isset($data['18k_color'])){
				$code = '18k_color';$name = '18K可做色';$value = $data['18k_color'];$stmt->execute();
			}
			if(isset($data['yanse'])){
				$code = 'yanse';$name = '颜色';$value = $data['yanse'];$stmt->execute();
			}
			if(isset($data['jingdu'])){
				$code = 'jingdu';$name = '净度';$value = $data['jingdu'];$stmt->execute();
			}
			if(isset($data['zhiquan'])){
				$code = 'zhiquan';$name = '指圈';$value = $data['zhiquan'];$stmt->execute();
			}
			if(isset($data['kezi'])){
				$code = 'kezi';$name = '刻字';$value = $data['kezi'];$stmt->execute();
			}
			if(isset($data['note'])){
				$code = 'note';$name = '备注';$value = $data['note'];$stmt->execute();
			}
            if(isset($data['p_sn_out'])){
                $code = 'p_sn_out';$name = '外部单号';$value = $data['p_sn_out'];$stmt->execute();
            }
            if(isset($data['ds_xiangci'])){
                $code = 'ds_xiangci';$name = '单身-项次';$value = $data['ds_xiangci'];$stmt->execute();
            }
            if(isset($data['pinhao'])){
                $code = 'pinhao';$name = '品号';$value = $data['pinhao'];$stmt->execute();
            }

			$sql = "SELECT SUM(`num`) FROM `purchase_goods` WHERE `pinfo_id` = '".$data['pinfo_id']."'";
			$obj = $pdo->query($sql);
			$p_num = $obj->fetch(PDO::FETCH_NUM);
			$p_num = $p_num[0];

			$sql = "UPDATE `purchase_info` SET `p_sum` = '".$p_num."' WHERE `id` = '".$data['pinfo_id']."'";
			$pdo->exec($sql);

		}catch (Exception $e){
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;

	}

	public function updatePurGoods($data){
		$pdo = $this->db()->db();
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			//写入 purchase_goods 表   [pinfo_id,style_sn,num,xiangqian]
			$good['pinfo_id'] = $pdo->quote($data['pinfo_id']);
			$good['style_sn'] = $pdo->quote($data['style_sn']);
			$good['g_name'] = $pdo->quote($data['g_name']);
			$good['num'] = $pdo->quote($data['num']);
			$good['info'] = $pdo->quote($data['info']);
			$good['xiangqian'] = $pdo->quote($data['xiangqian']);
			$good['consignee']=$pdo->quote($data['consignee']);
			$set_str = '';
			foreach ($good as $k => $v) {
				$set_str .= "`".$k."` = ".$v.",";
			}
			$set_str = rtrim($set_str,',');
			$sql = "UPDATE `purchase_goods` SET ".$set_str." WHERE `id` = '".$data['id']."'";
			$pdo->exec($sql);
			$last_id = $data['id'];
			$sql = "DELETE FROM `purchase_goods_attr` WHERE `g_id` = '".$data['id']."'";
			$pdo->exec($sql);
			//写入 purchase_goods_attr 表
			$sql = "INSERT INTO `purchase_goods_attr` (g_id,code,name,value) VALUES (:g_id,:code,:name,:value)";
			$stmt = $pdo->prepare($sql);
			//$code = '';$name='';$value='';
			$stmt->bindParam(':g_id', $g_id);
			$stmt->bindParam(':code', $code);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':value', $value);
			$g_id = $last_id;
			if(isset($data['caizhi'])){
				$code = 'caizhi';$name = '材质';$value = $data['caizhi'];$stmt->execute();
			}
			if(isset($data['zuanshidaxiao'])){
				$code = 'zuanshidaxiao';$name = '主石单颗重';$value = $data['zuanshidaxiao'];$stmt->execute();
			}
			if(isset($data['zhushi_num'])){
			    $code = 'zhushi_num';$name = '主石粒数';$value = $data['zhushi_num'];$stmt->execute();
			}
			if(isset($data['xiangkou'])){
				$code = 'xiangkou';$name = '镶口';$value = $data['xiangkou'];$stmt->execute();
			}
			if(isset($data['face_work'])){
				$code = 'face_work';$name = '表面工艺';$value = $data['face_work'];$stmt->execute();
			}
			if(isset($data['zhengshuhao'])){
				$code = 'zhengshuhao';$name = '证书号';$value = $data['zhengshuhao'];$stmt->execute();
			}
			if(isset($data['cert'])){
			    $code = 'cert';$name = '证书类型';$value = $data['cert'];$stmt->execute();
			}
			if(isset($data['18k_color'])){
				$code = '18k_color';$name = '18K可做色';$value = $data['18k_color'];$stmt->execute();
			}
			if(isset($data['yanse'])){
				$code = 'yanse';$name = '颜色';$value = $data['yanse'];$stmt->execute();
			}
			if(isset($data['jingdu'])){
				$code = 'jingdu';$name = '净度';$value = $data['jingdu'];$stmt->execute();
			}
			if(isset($data['zhiquan'])){
				$code = 'zhiquan';$name = '指圈';$value = $data['zhiquan'];$stmt->execute();
			}
			if(isset($data['kezi'])){
				$code = 'kezi';$name = '刻字';$value = $data['kezi'];$stmt->execute();
			}
			if(isset($data['note'])){
				$code = 'note';$name = '备注';$value = $data['note'];$stmt->execute();
			}
            if(isset($data['p_sn_out'])){
                $code = 'p_sn_out';$name = '外部单号';$value = $data['p_sn_out'];$stmt->execute();
            }
            if(isset($data['ds_xiangci'])){
                $code = 'ds_xiangci';$name = '单身-项次';$value = $data['ds_xiangci'];$stmt->execute();
            }
            if(isset($data['pinhao'])){
                $code = 'pinhao';$name = '品号';$value = $data['pinhao'];$stmt->execute();
            }

			$sql = "SELECT SUM(`num`) FROM `purchase_goods` WHERE `pinfo_id` = '".$data['pinfo_id']."'";
			$obj = $pdo->query($sql);
			$p_num = $obj->fetch(PDO::FETCH_NUM);
			$p_num = $p_num[0];

			$sql = "UPDATE `purchase_info` SET `p_sum` = '".$p_num."' WHERE `id` = '".$data['pinfo_id']."'";
			$pdo->exec($sql);

		}catch (Exception $e){
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			//return $e;
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}

	/**
	 * 批量添加采购商品
	 */
	public function batch_insert($data,$pur_id){
		$pdo = $this->db()->db();
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			foreach ($data as $k=>$row) {
				//写入 purchase_goods 表   [pinfo_id,style_sn,num,xiangqian]
				$good[$k]['pinfo_id'] = $pdo->quote($row['pinfo_id']);
				$good[$k]['style_sn'] = $pdo->quote($row['style_sn']);
				$good[$k]['g_name'] = $pdo->quote($row['goods_name']);
				$good[$k]['num'] = $pdo->quote($row['g_num']);
				$good[$k]['info'] = $pdo->quote($row['note']);
				$good[$k]['xiangqian'] = $pdo->quote($row['xiangqian']);
				$good[$k]['consignee']=$pdo->quote($row['consignee']);
				$sql = "INSERT INTO `purchase_goods` (".implode(',',array_keys($good[$k])).") VALUES (".implode(',',$good[$k]).")";
				$pdo->exec($sql);

				$last_id = $pdo->lastInsertId();
				//写入 purchase_goods_attr 表
				$sql = "INSERT INTO `purchase_goods_attr` (g_id,code,name,value) VALUES (:g_id,:code,:name,:value)";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':g_id', $g_id);
				$stmt->bindParam(':code', $code);
				$stmt->bindParam(':name', $name);
				$stmt->bindParam(':value', $value);
				$g_id = $last_id;
				if(isset($row['caizhi'])){
					$code = 'caizhi';$name = '材质';$value = $row['caizhi'];$stmt->execute();
				}
				if(isset($row['zuanshidaxiao'])){
					$code = 'zuanshidaxiao';$name = '主石单颗重';$value = $row['zuanshidaxiao'];$stmt->execute();
				}
				if(isset($row['zhushi_num'])){
				    $code = 'zhushi_num';$name = '主石粒数';$value = $row['zhushi_num'];$stmt->execute();
				}
				if(isset($row['xiangkou'])){
					$code = 'xiangkou';$name = '镶口';$value = $row['xiangkou'];$stmt->execute();
				}
				if(isset($row['face_work'])){
					$code = 'face_work';$name = '表面工艺';$value = $row['face_work'];$stmt->execute();
				}
				if(isset($row['zhengshuhao'])){
					$code = 'zhengshuhao';$name = '证书号';$value = $row['zhengshuhao'];$stmt->execute();
				}
				if(isset($row['cert'])){
				    $code = 'cert';$name = '证书类型';$value = $row['cert'];$stmt->execute();
				}
				if(isset($row['18k_color'])){
					$code = '18k_color';$name = '18K可做色';$value = $row['18k_color'];$stmt->execute();
				}
				if(isset($row['yanse'])){
					$code = 'yanse';$name = '颜色';$value = $row['yanse'];$stmt->execute();
				}
				if(isset($row['jingdu'])){
					$code = 'jingdu';$name = '净度';$value = $row['jingdu'];$stmt->execute();
				}
				if(isset($row['zhiquan'])){
					$code = 'zhiquan';$name = '指圈';$value = $row['zhiquan'];$stmt->execute();
				}
				if(isset($row['kezi'])){
					$code = 'kezi';$name = '刻字';$value = $row['kezi'];$stmt->execute();
				}
				if(isset($row['note'])){
					$code = 'note';$name = '备注';$value = $row['note'];$stmt->execute();
				}
                if(isset($row['p_sn_out'])){
                    $code = 'p_sn_out';$name = '外部单号';$value = $row['p_sn_out'];$stmt->execute();
                }
                if(isset($row['ds_xiangci'])){
                    $code = 'ds_xiangci';$name = '单身-项次';$value = $row['ds_xiangci'];$stmt->execute();
                }
                if(isset($row['pinhao'])){
                    $code = 'pinhao';$name = '品号';$value = $row['pinhao'];$stmt->execute();
                }
			}

			$sql = "SELECT SUM(`num`) FROM `purchase_goods` WHERE `pinfo_id` = '".$pur_id."'";
			$obj = $pdo->query($sql);
			$p_num = $obj->fetch(PDO::FETCH_NUM);
			$p_num = $p_num[0];

			$sql = "UPDATE `purchase_info` SET `p_sum` = '".$p_num."' WHERE `id` = '".$pur_id."'";
			$pdo->exec($sql);

		}catch (Exception $e){
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}

	/**
	 * 获取所有款号
	 */
	public function getAllStyleSN()
	{
		$styleModel = new ApiStyleModel();
		$style = $styleModel->getAllStyleSN();
		return $style;
	}

	//修改采购分类,采购备注
	public function editPurInfo($pur){
		$sql = "UPDATE `purchase_info` SET `t_id` = '".$pur['t_id']."',`p_info` = '".$pur['p_info']."' WHERE `id` = '".$pur['pinfo_id']."'";
		//file_put_contents('./purchase_error.txt',print_r($sql),FILE_APPEND);
		$res = $this->db()->query($sql);
		return $res;
	}

	//申请修改采购商品
	public function applyEdit($data){
		$applyInfo = serialize($data);
		$sql = "UPDATE `purchase_goods` SET `is_apply` = '1',`apply_info` = '".$applyInfo."' WHERE `id` = '".$data['id']."'";
		//file_put_contents('./purchase_error.txt',print_r($sql),FILE_APPEND);
		$res = $this->db()->query($sql);
		return $res;
	}

	public function get_all_attr($g_id){
		$sql = "SELECT `name`,`code`,`value` FROM `purchase_goods_attr` WHERE `g_id` = '".$g_id."'";
		$data = $this->db()->getAll($sql);
		return $data;
	}

	//获取PurchaseInfo一个字段值
	public function getPurchaseInfo($field,$id){
		$sql = "SELECT `".$field."` FROM `purchase_info` WHERE `id` = '".$id."'";
		$res = $this->db()->getOne($sql);
		return $res;
	}
	public function getAllProductAttr($bc_id){
	    $sql = "select * from kela_supplier.`product_info_attr` where g_id={$bc_id}";
	    return $this->db()->getAll($sql);
	}
	/**
	 * 更新布产单信息
	 * @param unknown $bc_id
	 * @param unknown $newdo
	 * @param string $olddo
	 * @return multitype:number string multitype: multitype:boolean Ambigous <boolean, string>
	 */
	public function saveProductAttrData($bc_id,$newdo,$olddo=false){
	
	    $result = array('success'=>0,'error'=>'','data'=>array());
	    if(empty($olddo)){
    	    $sql = "select * from kela_supplier.`product_info_attr` where g_id={$bc_id}";
    	    $olddo = $this->db()->getAll($sql);
	    }
	    $olddo = array_column($olddo,'value','code');
	     
	    $bc_zhengshuhao_changed = false;
	    $bc_diy_dependency_changed = false;
	    $zhengshuhao = '';
	    try{
	        //布产类型横列属性限制
	        $fitler_x = array('goods_name','style_sn','num','bc_style','xiangqian','is_peishi','info');//横列属性
            //需要转换的字段
	        $fitler_replace = array('18k_color'=>'jinse','yanse'=>'color','zuanshidaxiao'=>'cart','g_name'=>'goods_name','bc_type'=>'bc_style');
	        //file_put_contents('5678.txt',var_export($newdo,true));
	        foreach ($newdo as $v) {
	            $code = $v['code'];
	            $new_code = isset($fitler_replace[$code])?$fitler_replace[$code]:$code;
	            //file_put_contents('5678.txt',var_export($v,true),FILE_APPEND);
	            
	            if(in_array($new_code,$fitler_x)){
	                $sql = "UPDATE kela_supplier.`product_info` SET `".$new_code."` = '".$v['value']."' WHERE `id` = ".$bc_id;
	                $this->db()->query($sql);
	            }else{
	                if($code <> $new_code){    
	                    $sql = "UPDATE kela_supplier.`product_info_attr` SET `value`='".$v['value']."',`code`='".$new_code."',`name`='".$v['name']."' WHERE `g_id`='".$bc_id."' AND `code` in('{$code}','{$new_code}')";
	                    $this->db()->query($sql);
	                }else{
    	                if(array_key_exists($v['code'],$olddo)){	            
        	                $sql = "UPDATE kela_supplier.`product_info_attr` SET `value`='".$v['value']."',`name`='".$v['name']."' WHERE `g_id`='".$bc_id."' AND `code` = '".$code."'";
        	                $this->db()->query($sql);
        	            }else{
        	                $sql = "INSERT INTO kela_supplier.`product_info_attr` (`g_id`,`code`,`name`,`value`) VALUES ('".$bc_id."','".$code."','".$v['name']."','".$v['value']."')";
        	                $this->db()->query($sql);
        	            }
	                }
	                //file_put_contents('5678.txt',$sql."\r\n",FILE_APPEND);
	            }
	
	            if ($v['code'] == 'zhengshuhao') {
	                $bc_zhengshuhao_changed = true;
	                $zhengshuhao = $v['value'];
	            } else {
	                $bc_diy_dependency_changed = true;
	            }
	        }
	        /*--------------*/
	        $result['success'] = 1;
	        $result['data'] = array(
	            'bc_zhengshuhao_changed'=>$bc_zhengshuhao_changed? $zhengshuhao: false,
	            'bc_diy_dependency_changed'=>$bc_diy_dependency_changed
	        );
	        return $result;
	    }
	    catch(Exception $e){
	        $result['error'] = "更新失败:".$e->getMessage();
	        //file_put_contents('5678.txt',var_export($result['error'],true),FILE_APPEND);
	        return $result;
	    }
	}
	
    
	//审核通过
	public function checkApplyPass($id,$bc_id,$olddo=array()){
		//修改采购商品信息 修改布产商品信息 清空申请状态
		//新属性值
		$newdo = unserialize($this->getValue('apply_info'));
		//旧属性值
		if(empty($olddo)){
		    $olddo = $this->get_all_attr($id);
		}
		$olddo = array_column($olddo,'value','code');
		//print_r($newdo);exit;
		//采购单 横列 属性
		$CGFields_x = $this->getDataObject();
		//采购单纵列
		$CGFields_y =array(
			'g_name'=>'货品名称','xiangqian'=>'镶嵌方式','face_work'=>'表面工艺', 'caizhi'=>'材质', '18k_color'=>'金色', 'zhiquan'=>'指圈', 'zuanshidaxiao'=>'主石单颗重','zhushi_num'=>'主石粒数', 'xiangkou'=>'镶口', 'cert'=>'证书类型','zhengshuhao'=>'证书号', 'yanse'=>'主石颜色', 'jingdu'=>'主石净度', 'kezi'=>'刻字', 'info'=>'布产备注'
		);
		//布产单更新字段
		$BCFields = array(
		    'style_sn'=>'款号', 'g_name'=>'货品名称', 'num'=>'数量', 'xiangqian'=>'镶嵌方式', 'face_work'=>'表面工艺', 'caizhi'=>'材质', '18k_color'=>'金色', 'zhiquan'=>'指圈', 'zuanshidaxiao'=>'主石单颗重','zhushi_num'=>'主石粒数', 'xiangkou'=>'镶口', 'cert'=>'证书类型','zhengshuhao'=>'证书号', 'yanse'=>'主石颜色', 'jingdu'=>'主石净度', 'kezi'=>'刻字', 'info'=>'布产备注'
		);
		//purchase_goods字段
		$base['style_sn'] = $this->getValue('style_sn');
		$base['cg_sn'] = $this->getPurchaseInfo('p_sn',$this->getValue('pinfo_id'));
		$base['p_id']=$id;
		$pdo = $this->db()->db();
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//修改采购属性表
			foreach ($newdo as $k=>$v) {
				if(array_key_exists($k,$CGFields_x)){
					$sql = "UPDATE `purchase_goods` SET `{$k}` = '{$v}' WHERE `id` = {$id}";
					$pdo->exec($sql);
				}else if(array_key_exists($k,$CGFields_y)){
					if(array_key_exists($k,$olddo)){
						$sql = "UPDATE `purchase_goods_attr` SET `value` = '".$v."',`name`='".$CGFields_y[$k]."' WHERE `code` = '".$k."' AND `g_id` = '".$id."'";
					}else{
						$sql = "INSERT INTO `purchase_goods_attr` (`g_id`,`name`,`code`,`value`) VALUES ({$id},'{$CGFields_y[$k]}','{$k}','{$v}')";
					}
					$pdo->exec($sql);
				}
			}
			//重新计算采购单总数
			$sql = "SELECT SUM(`num`) FROM `purchase_goods` WHERE `pinfo_id` = '".$this->getValue('pinfo_id')."'";
			$p_num = $this->db()->getOne($sql);
			$sql = "UPDATE `purchase_info` SET `p_sum` = '".$p_num."' WHERE `id` = '".$this->getValue('pinfo_id')."'";
			$pdo->exec($sql);

			$sql = "UPDATE `purchase_goods` SET `is_apply` = '0' WHERE `id` = '".$id."'";
			$pdo->exec($sql);
			

			if($bc_id>0){//有布产
			    $BCAttrs = array();
			    foreach ($BCFields  as $code=>$name){
			        $BCAttrs[] = array('code'=>$code,'name'=>$name,'value'=>$newdo[$code]);
			    }
			    $res = $this->saveProductAttrData($bc_id,$BCAttrs);
			    if($res['success'] == 0){
			        return false;
			    }
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

	//审核取消
	public function checkApplyOut($id){
		$sql = "UPDATE `purchase_goods` SET `is_apply` = '0',`apply_info` = '' WHERE `id` = '".$id."'";
		$res = $this->db()->query($sql);
		return $res;
	}

    /**
     *
     *  显示刻字
     *
     */
    public function retWord($word)
    {
        $rep = $this->getKeziData();
        foreach($rep as $key => $val)
        {
            $ret=stripos($word,$key);

            if($ret>0){
                return str_replace($key,"<img src='".$val."' width='24'/>",$word);
            }
        }
        return $word;
    }

    //刻字内容
	public function getKeziData(){
		$rep=array(
            '[&符号]'=>'/public/sales/face/1.png',
            '[间隔号]'=>'/public/sales/face/2.png',
            '[空心]'=>'/public/sales/face/3.png',
            '[实心]'=>'/public/sales/face/4.png',
            '[小数点]'=>'/public/sales/face/5.png',
            '[心心相印]'=>'/public/sales/face/6.png',
            '[一箭穿心]'=>'/public/sales/face/7.png'
        );

		return $rep;
	}

    //刻字长度
	public function pdKeziData($kezi,$allkezi,$is_oubanjie=0){
         $str = $kezi;
        //[一箭穿心]代表一个字符
        $allkezi_str='';
        foreach ($allkezi as $k=>$val){
            if($val){
                $k=str_replace(array('[',']','&'),array('\[','\]','\&'),$k);
            }
            $allkezi_str.=$k.'|';
        }
        $allkezi_str=rtrim($allkezi_str,'|');
        preg_match_all("/".$allkezi_str."+/u",$str,$allkezi_key);
        if($allkezi_key[0]){
            $allkezi_count=count($allkezi_key[0]);
            foreach($allkezi_key[0] as $k=>$v){
                $str=str_replace($v,'',$str);
            }
        }else{
            $allkezi_count=0;
        }
        //var_dump($str);exit;
        preg_match_all("/[0-9]{1}/i",$str,$arrNum);//数字
        preg_match_all("/[a-zA-Z]{1}/i",$str,$arrAl);//字母
        preg_match_all("/[\x{4e00}-\x{9fa5}]{1}/u",$str,$arrCh); //中文
        preg_match_all("/[^\x{4e00}-\x{9fa5}\w]{1}/u",$str,$punct); //其它字符
        //var_dump($arrCh[0]);exit;
        //1、欧版戒，0、非欧版戒
        $err_bd = '';
        if(!$is_oubanjie){
            //非欧版戒，只能刻以下标点符号
            $is_bdfh = array('`','~','•','！','@','#','$','%','^','&','*','(',')','_','-','+','=','{','}','【','】','|','、','：','；','“','”','‘','’','《','》','，','。','？','\ ','.','<','>',' ','·','!','￥','…','（','）','—','｛','｝','[',']',';',',',':','?','/','"','\'','\\');
            //var_dump($punct[0]);die;
            if(!empty($punct[0])){
                foreach ($punct[0] as $key => $value) {
                    if(!in_array($value,$is_bdfh)){
                        $err_bd .= $value;
                    }
                }
            }
        }
        
        $data = array('err_bd'=>'','str_count'=>'','kezi'=>'');
        $data['err_bd'] = $err_bd;
        $data['str_count'] = count($arrNum[0])+count($arrAl[0])+count($arrCh[0])+count($punct[0])+$allkezi_count;
        //将特殊字符用数字编码代替存入数据库；
        $kezi = str_replace('\\','a01',$kezi);
        $kezi = str_replace('\'','a02',$kezi);
        $kezi = str_replace('"','a03',$kezi);
        $data['kezi'] = $kezi;
        return $data;
	}
       
	/*
	* 获取采购单证书号
	*
	*/
	public function getDamindTypeById($id){
		$sql ="SELECT pga.value from purchase_goods pg left join purchase_goods_attr pga on pg.id=pga.g_id where pga.code='zhengshuhao' and pga.g_id=".$id;
		return $this->db()->getOne($sql);

	}

   public function CheckIsInFactory($id,$factory_id){
   	 //根据采购单id查询款号
     $sql="SELECT style_sn FROM ".$this->table()." WHERE pinfo_id = {$id}";
   	 $style_list=$this->db()->getAll($sql);
   	 //$SelfDiamondModel=new SelfDiamondModel(20);
   	 $SelfProcessorModel=new SelfProcessorModel(13);
   	 $factory_name=$SelfProcessorModel->getFactoryName($factory_id);
   	 //循环款号，根据款号查询默认工厂和与它关联的工厂，再判断$factory_id是否在这些工厂里
   	 $style_str='';
   	 $styleArr=array();
   	 foreach ($style_list as $v){
   	 	$styleArr[]=trim($v['style_sn']," ");
   	 }
   	 $styleArr=array_unique($styleArr);
   	 
   	 foreach ($styleArr as $style_sn){
   	 	
   	 	//查询默认工厂
   	    $factoryId=$SelfProcessorModel->getFactoryIdByStyle($style_sn);
   	    if(empty($factoryId)){
   	    	$style_str.=$style_sn;
   	    	continue;
   	    } 	    
   	 	$factory_arr=$SelfProcessorModel->getFactoryArr($factoryId);
   	 	if(!in_array($factory_id, $factory_arr)){
	       $style_str.=$style_sn;
   	 	}	
   	 	
   	 }
   	 if($style_str!='')
   	 {
   	 	$result['success']=0;
   	 	$result['error']="工厂{$factory_name}，不是款号{$style_str}的默认工厂，不允许分配";
   	 }
   	 else
   	 {
   	 	$result['success']=1;
   	 }
   	 
   	 return $result;
   }
   
   	/*
	*通过采购单号在采购列表确定有款无款
	*
	*/
	public function getStyleInfoByCgd($p_sn){
		$sql = "select is_style from purchase.purchase_info where p_sn='".$p_sn."'";
		return $this->db()->getOne($sql);
	}
	/**
     * 检查证书号与证书类型是否匹配,证书号有效 ，判断是否
     * @param unknown $zhengshuhao
     * @param unknown $cert
     */
    public function checkCertByCertId($zhengshuhao,$cert){
        $sql="select cert from front.diamond_info where cert_id = '{$zhengshuhao}'  
              union
              select cert from front.app_diamond_color where cert_id = '{$zhengshuhao}'";
        $row = $this->db()->getRow($sql);
        if(!empty($row)){
            if($row['cert']!=trim($cert)){
                return false;
            }
        }else{        
            $sql="select zhengshuleibie from warehouse_shipping.warehouse_goods where zhengshuhao = '{$zhengshuhao}'";
            $row = $this->db()->getRow($sql);
            if(!empty($row)){
                if($row['zhengshuleibie']<>$cert){
                    return false;
                }
            }
        }
        return true;
    }
	
    //根据明细ID取绑定备货信息
    public function getOutOrderInfo($purchase_id)
    {
        $sql = "select * from `app_order`.`purchase_order_info` where purchase_id = ".$purchase_id;
        return $this->db()->getAll($sql);
    }

    //获取款号默认工厂
    public function getDefaultFactory($style_sn,$xiangkou)
    {
        $sql = "select r.*,f.name as factory_name from front.rel_style_factory r left join kela_supplier.app_processor_info f on r.factory_id=f.id where r.is_factory=1 and r.style_sn ='{$style_sn}' ORDER BY abs({$xiangkou}-r.xiangkou)";
        return $this->db()->getRow($sql);
    }



	/**
	 * 批量添加采购商品
	 */
	public function batch_insert_notransaction($data,$pur_id){
		$pdo = $this->db()->db();
		try{
			//$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			//$pdo->beginTransaction();//开启事务
			foreach ($data as $k=>$row) {
				//写入 purchase_goods 表   [pinfo_id,style_sn,num,xiangqian]
				$good[$k]['pinfo_id'] = $pdo->quote($row['pinfo_id']);
				$good[$k]['style_sn'] = $pdo->quote($row['style_sn']);
				$good[$k]['g_name'] = $pdo->quote($row['goods_name']);
				$good[$k]['num'] = $pdo->quote($row['g_num']);
				$good[$k]['info'] = $pdo->quote($row['note']);
				$good[$k]['xiangqian'] = $pdo->quote($row['xiangqian']);
				$good[$k]['consignee']=$pdo->quote($row['consignee']);
				$sql = "INSERT INTO `purchase_goods` (".implode(',',array_keys($good[$k])).") VALUES (".implode(',',$good[$k]).")";
				$pdo->exec($sql);

				$last_id = $pdo->lastInsertId();
				//写入 purchase_goods_attr 表
				$sql = "INSERT INTO `purchase_goods_attr` (g_id,code,name,value) VALUES (:g_id,:code,:name,:value)";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':g_id', $g_id);
				$stmt->bindParam(':code', $code);
				$stmt->bindParam(':name', $name);
				$stmt->bindParam(':value', $value);
				$g_id = $last_id;
				if(isset($row['caizhi'])){
					$code = 'caizhi';$name = '材质';$value = $row['caizhi'];$stmt->execute();
				}
				if(isset($row['zuanshidaxiao'])){
					$code = 'zuanshidaxiao';$name = '主石单颗重';$value = $row['zuanshidaxiao'];$stmt->execute();
				}
				if(isset($row['zhushi_num'])){
				    $code = 'zhushi_num';$name = '主石粒数';$value = $row['zhushi_num'];$stmt->execute();
				}
				if(isset($row['xiangkou'])){
					$code = 'xiangkou';$name = '镶口';$value = $row['xiangkou'];$stmt->execute();
				}
				if(isset($row['face_work'])){
					$code = 'face_work';$name = '表面工艺';$value = $row['face_work'];$stmt->execute();
				}
				if(isset($row['zhengshuhao'])){
					$code = 'zhengshuhao';$name = '证书号';$value = $row['zhengshuhao'];$stmt->execute();
				}
				if(isset($row['cert'])){
				    $code = 'cert';$name = '证书类型';$value = $row['cert'];$stmt->execute();
				}
				if(isset($row['18k_color'])){
					$code = '18k_color';$name = '18K可做色';$value = $row['18k_color'];$stmt->execute();
				}
				if(isset($row['yanse'])){
					$code = 'yanse';$name = '颜色';$value = $row['yanse'];$stmt->execute();
				}
				if(isset($row['jingdu'])){
					$code = 'jingdu';$name = '净度';$value = $row['jingdu'];$stmt->execute();
				}
				if(isset($row['zhiquan'])){
					$code = 'zhiquan';$name = '指圈';$value = $row['zhiquan'];$stmt->execute();
				}
				if(isset($row['kezi'])){
					$code = 'kezi';$name = '刻字';$value = $row['kezi'];$stmt->execute();
				}
				if(isset($row['note'])){
					$code = 'note';$name = '备注';$value = $row['note'];$stmt->execute();
				}
                if(isset($row['p_sn_out'])){
                    $code = 'p_sn_out';$name = '外部单号';$value = $row['p_sn_out'];$stmt->execute();
                }
                if(isset($row['ds_xiangci'])){
                    $code = 'ds_xiangci';$name = '单身-项次';$value = $row['ds_xiangci'];$stmt->execute();
                }
                if(isset($row['pinhao'])){
                    $code = 'pinhao';$name = '品号';$value = $row['pinhao'];$stmt->execute();
                }
			}

			$sql = "SELECT SUM(`num`) FROM `purchase_goods` WHERE `pinfo_id` = '".$pur_id."'";
			$obj = $pdo->query($sql);
			$p_num = $obj->fetch(PDO::FETCH_NUM);
			$p_num = $p_num[0];

			$sql = "UPDATE `purchase_info` SET `p_sum` = '".$p_num."' WHERE `id` = '".$pur_id."'";
			$pdo->exec($sql);

		}catch (Exception $e){
			throw $e;
			//$pdo->rollback();//事务回滚
			//$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		//$pdo->commit();//如果没有异常，就提交事务
		//$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}


}

?>