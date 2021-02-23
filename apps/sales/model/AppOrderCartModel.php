<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderCartModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 19:44:38
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderCartModel extends Model
{
		function __construct ($id=NULL,$strConn="")
	{
        
// 		$this->_objName = 'app_order_cart';
		$this->_objName = 'app_order_cart';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID",
        "session_id"=>"session的id",
        "goods_id"=>"货号",
        "goods_sn"=>"款号",
        "product_type"=>"产品线",
        "goods_type"=>"商品类型：lz裸钻",
        "goods_name"=>"名称",
        "goods_price"=>"原价格",
        "favorable_price"=>"优惠掉的金额",
        "goods_count"=>"数量",
        "is_stock_goods"=>"是否是现货：1现货 0期货",
        "cart"=>"石重",
        "cut"=>"切工",
        "clarity"=>"净度",
        "color"=>"颜色",
        "zhengshuhao"=>"证书号",
        "caizhi"=>"材质",
        "jinse"=>"金色",
        "jinzhong"=>"金重",
        "zhiquan"=>"指圈",
        "kezi"=>"刻字",
        "face_work"=>"表面工艺",
        "xiangqian"=>"镶嵌方式",
        "create_time"=>"添加时间",
        "modify_time"=>"修改时间",
        "create_uid"=>"创建人ID",
        "create_user"=>"添加人",
        "department_id"=>"渠道部门id",
        "policy_goods_id"=>"政策对商品的id",
        "type"=>"政策类型",
        "kuan_sn"=>"天生一对用",
		"is_4c"=>"是否为4c搜索",
		"filter_data"=>"4c搜索条件",
            );
		parent::__construct($id,$strConn);
	}
	/**
	 *	pageList，分页列表
	 *
	 *	@url AppOrderCartController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
    
    /**
	 * 取购物车列表, 第二个参数可判断商品是否存在购物车中
	 * @return mixed This is the return value description
	 */
	public function get_cart_goods($select='*', $goods_id=false){
		$sql='SELECT '.$select.' FROM '.$this->table().' WHERE session_id = \''.DBSessionHandler::getSessionId().'\' ';
        if ($goods_id) {
            $sql .= ' and goods_id='.$goods_id;
        }
        $sql .= ' ORDER BY id ASC';
		return $this->db()->getAll($sql);
	}
    
    /**
	 * 取购物车单个购物车
	 * @return mixed This is the return value description
	 */
	public function get_cart_goods_by_id($Id,$select='*'){
		$sql="SELECT ".$select." FROM ".$this->table()." WHERE id = '".$Id."'";
		return $this->db()->getRow($sql);
	}
    
    /**
	 * 添加购物车数据
	 * @param mixed $cartDate This is a description
	 * @return mixed This is the return value description
	 */
	public function add_cart($cartData){
        $res = $this->saveData($cartData, array());
        if($res){
            return $res;
        }else{
            return false;
        }
	}
    
    	/**
	 * 修改购物车数据
	 *
	 * @param mixed $cartDate This is a description
	 * @return mixed This is the return value description
	 *
	 */
	public function update_cart_by_id($newdo,$olddo){
        $res = $this->saveData($newdo, $olddo);
        if($res){
            return $res;
        }else{
            return false;
        }
	}
    
    /**
	 * 删除购物车
	 *
	 * @param mixed $Id 
	 * @return mixed This is the return value description
	 *
	 */
	public function delete_cart_goods_by_id($Id){
		$row=$this->get_cart_goods_by_id($Id);
		/* 取得商品id */
		if($row){
            $kuan_sn = $row['kuan_sn'];
            if(!empty($kuan_sn)){
                $sql="DELETE FROM ".$this->table()." WHERE `session_id` = '".DBSessionHandler::getSessionId()."' AND `kuan_sn` ='".$kuan_sn."'";
            }else{
                $sql="DELETE FROM ".$this->table()." WHERE `session_id` = '".DBSessionHandler::getSessionId()."' AND `id` ='$Id'";
            }
			
			return $this->db()->query($sql);
		}else{
			return false;
		}
	}

	/**
	 * 清空购物车
	 *
	 * @return mixed This is the return value description
	 *
	 */
	public function clear_cart(){
		if(DBSessionHandler::getSessionId()){
            $siss = DBSessionHandler::getSessionId();

			$sql='DELETE FROM '.$this->table().' WHERE session_id=\''.$siss.'\'';
			return $this->db()->query($sql);
		}
        return false;
	}
    
    /**
	 * 取购物车总价
	 *
	 * @param mixed $goodsCart This is a description
	 * @return mixed This is the return value description
	 */
	public function get_cart_total($goodsCart=array()){
		$total_price=0.00;
		if(empty($goodsCart)){
			return null;
		}
        $total_price = 0;
        $favorable_price = 0;
		foreach($goodsCart as $value){
			$total_price+=round((isset($value['goods_price'])?$value['goods_price']:$value['goods_price']),2);
			$favorable_price+=round((isset($value['favorable_price'])?$value['favorable_price']:$value['favorable_price']),2);
		}
        
		return array('total_price'=>$total_price,'favorable_price'=>$favorable_price) ;
	}
    
    /*
     * 检查是否是同一个商品
     */
    /**
     * 
     * @param type $id :普通政策是政策对商品的id，打包政策是政策对策略的id
     * @param type $cart_goods
     * @return false:没有相同的商品反之
     */
    public function check_cart_goods($id,$cart_goods=array()){
        $session_id = DBSessionHandler::getSessionId();
        if(strlen($id)>20){
           $sql = "select count(*) from ".$this->table()." where goods_key='{$id}' and session_id = '".$session_id."'";
        }else{
           $sql = "select count(*) from ".$this->table()." where policy_goods_id='{$id}' and session_id = '".$session_id."'";
        }
        return $this->db()->getOne($sql);
    }
   
    /**
     *  判断如果添加的购物车的商品是否是一个渠道部门
     * @param type $department
     * @param type $cart_goods
     * return true 是同一个部门;fales 不是同一个部门
     * ln
     */
    public function is_department($department,$cart_goods) {
        $is_falg = TRUE;
        foreach ($cart_goods as $val){
            if(in_array($val['goods_type'],array('lz','caizuan_goods','qiban','zp'))){
                continue;//裸钻时不分渠道的
            }
            if($val['department_id'] != $department){
                $is_falg = FALSE;
                break;
            }
        }
        return $is_falg;
    }
    
    //适应打包政策，删一个则捆绑商品一起删除
    public function delete_cart_goods_by_policy_goods_id($policy_goods_id,$cart_goods) {
        if(empty($policy_goods_id)){
            return FALSE;
        }
        if(empty($cart_goods)){
            return TRUE;
        }
		/* 取得商品id */
		if($cart_goods){
			$sql="DELETE FROM ".$this->table()." WHERE session_id = '".DBSessionHandler::getSessionId()."' AND policy_goods_id ='$policy_goods_id'";
            return $this->db()->query($sql);
		}else{
			return FALSE;
		}
    }
}

?>