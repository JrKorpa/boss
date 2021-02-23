<?php
/**
 *  -------------------------------------------------
 *   @file		: AppJinsunModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 16:42:07
 *   @update	:
 *  -------------------------------------------------
 */
class AppStyleQuickdiyModel extends Model
{
    public static $caizhi_arr = array('18K'=>'18K','PT950'=>'PT950');
    public static $caizhiyanse_arr = array("白"=>"W","黄"=>"Y","玫瑰金"=>"R","分色"=>"C","彩金"=>"H","玫瑰黄"=>"RY","玫瑰白"=>"RW","黄白"=>"YW");
    
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_style_quickdiy';
		$this->_dataObject = array("id"=>"主键",
		    "goods_sn"=>"定制编码",
		    "style_sn"=>"款号编码",
		    "style_name"=>"款式名称",
		    "caizhi"=>"材质",
		    "caizhiyanse"=>"材质颜色",
		    "xiangkou"=>"镶口",
		    "zhiquan"=>"指圈",
		    "status"=>"状态",
		    "create_user"=>"添加人",
		    "create_time"=>"添加时间",		    
		    );
        
		parent::__construct($id,$strConn);
	}
	
	public function getCaiZhiArr(){
	    $dd = new DictView(new DictModel(1));
	    $caizhiArr = $dd->getEnumArray('style.caizhi');
	    print_r($caizhiArr);
	    
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url MessageController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM ".$this->table()." WHERE 1=1 ";
      
        if(isset($where['style_sn']) && !empty($where['style_sn'])){
            $sql .=" AND style_sn = '{$where['style_sn']}' ";
        }
		if(isset($where['caizhi']) && $where['caizhi']!=''){
            $sql .=" AND caizhi = '{$where['caizhi']}' ";
        }
        if(isset($where['caizhiyanse']) && $where['caizhiyanse']!=''){
            $sql .=" AND caizhiyanse = '{$where['caizhiyanse']}' ";
        }
        if(isset($where['xiangkou_min']) && $where['xiangkou_min']!=''){
            $sql .=" AND xiangkou >= {$where['xiangkou_min']}";
        }
        if(isset($where['xiangkou_max']) && $where['xiangkou_max']!=''){
            $sql .=" AND xiangkou <= {$where['xiangkou_max']}";
        }
        if(isset($where['zhiquan_min']) && $where['zhiquan_min']!=''){
            $sql .=" AND zhiquan >= {$where['zhiquan_min']}";
        }
        if(isset($where['zhiquan_max']) && $where['zhiquan_max']!=''){
            $sql .=" AND zhiquan <= {$where['zhiquan_max']}";
        }
        if(isset($where['create_user']) && $where['create_user']!=''){
            $sql .=" AND create_user = '{$where['create_user']}'";
        }
        if(isset($where['create_time_min']) && $where['create_time_min']!=''){
            $sql .=" AND create_time >= '{$where['create_time_min']}'";
        }
        if(isset($where['create_time_max']) && $where['create_time_max']!=''){
            $sql .=" AND create_time <= '{$where['create_time_max']} 23:59:59' ";
        }
        if(isset($where['status']) && $where['status']!=''){
            $sql .=" AND `status`= {$where['status']} ";
        }
		$sql .= " ORDER BY id desc";
		//echo $sql;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

   
   public function getAppStyleQuickdiy($fields="*",$where=""){
       $sql = "select {$fields} from ".$this->table()." where {$where}";
       return $this->db()->getRow($sql);
   }
   //批量删除
   public function multi_delete($ids){
       $result = array('success'=>true,'msg'=>"更新成功");
       
       if(empty($ids) || !is_array($ids)){
          $result = array('success'=>false,'msg'=>"参数ID不合法");
          return $result;
       }
       try{
           $pdo = $this->db()->db();
           $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
           $pdo->beginTransaction();//开启事务
           $ids = implode(',',$ids);
           
           $tip = "取消款式商品快速定制状态";
           $sql = "update list_style_goods a inner join ".$this->table()." b on a.goods_sn=b.goods_sn set a.is_quick_diy=0 where b.id in($ids)";
           $pdo->query($sql);
           
           $tip = "删除快速定制码";
           $sql = "delete from ".$this->table()." where id in({$ids})";
           $pdo->query($sql); 
           
           $tip = "提交事物";          
           $pdo->commit();//事务回滚
           $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
       }catch (Exception $e){
           $pdo->rollback();//事务回滚
           $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
           $resuslt = array('success'=>false,'msg'=>$tip."失败,sql:".$sql);
       }
       return $result;
   }
   	//批量更新状态
	public function setStatus($status,$ids){
	    $result = array('success'=>true,'msg'=>"更新成功");
	    
	    $status = $status==1?1:0;
	    if(empty($ids) || !is_array($ids)){
          $result = array('success'=>false,'msg'=>"参数ID不合法");
          return $result;
        }
        $ids = implode(',',$ids);
	    try{
	        $tip = "初始化事物";
    	    $pdo = $this->db()->db();
    	    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
    	    $pdo->beginTransaction();//开启事务    	      
    	    
    	    $tip = "更新快速定制状态";
    	    $sql = "update ".$this->table()." set `status`={$status} where `id` in($ids)";    	    
    	    $pdo->query($sql);
    	    
    	    $tip = "同步款式商品快速定制状态";
    	    $sql = "update list_style_goods a inner join ".$this->table()." b on a.goods_sn=b.goods_sn set a.is_quick_diy={$status} where b.id in($ids)";
    	    $pdo->query($sql);

    	    $tip = "提交事物";
    	    $pdo->commit();//事务回滚
    	    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	    }catch (Exception $e){
	        $pdo->rollback();//事务回滚
	        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交	  
	        $result = array('success'=>false,'msg'=>$tip."失败,sql:".$sql);   
	    }
	    return $result;
	}
	
    public function insert($data){
        $result = array('success'=>true,'msg'=>"添加成功");
        
        if(empty($data) || empty($data['goods_sn'])){
            $result = array('success'=>false,'msg'=>"参数不合法");
            return $result;
        }
                
        try{
            $tip = "初始化事物";
            $pdo = $this->db()->db();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            
            $tip = "添加快速定制码";
            $sql = $this->insertSql($data);
            $pdo->query($sql);
            $is_quick_diy = empty($data['status'])?0:1;
            $tip = "同步款式商品快速定制状态";
            $sql = "update list_style_goods set is_quick_diy={$is_quick_diy} where goods_sn='{$data['goods_sn']}'";
            $pdo->query($sql);
            
            $tip = "提交事物";
            $pdo->commit();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        }catch (Exception $e){
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            $result = array('success'=>false,'msg'=>$tip."失败,sql:".$sql);
        }
        return $result;
    }
	//update
	public function update($data,$where){
	    //过滤主键id值
	    if($this->pk() && isset($data[$this->pk()])){
	        unset($data[$this->pk()]);
	    }
	    $sql = $this->updateSql($data);
	    if(preg_match('/ WHERE /is',$sql)){
	        $sql = preg_replace('/ WHERE .*/is',' WHERE '.$where, $sql);
	        return $this->db()->query($sql);
	    }else{
	        return false;
	    }
	}
}

?>