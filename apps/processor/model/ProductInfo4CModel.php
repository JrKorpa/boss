<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 14:58:58
 *   @update	:
 *  -------------------------------------------------
 */
class ProductInfo4CModel extends Model
{   
    //切工(Cut) ：完美 EX   非常好 VG   好 G   一般 Fair
    public static $cut_arr = array('EX', 'VG', 'G', 'Fair');
    //抛光(Polish)	 完美 EX   非常好 VG   好 G   一般 Fair
    public static $polish_arr = array('EX', 'VG', 'G', 'Fair');
    //对称(Symmetry)	 完美 EX   非常好 VG   好 G   一般 Fair
    public static $symmetry_arr = array('EX', 'VG', 'G', 'Fair');
    //荧光(Fluorescence): 无 N   轻微 F   中度 M   强烈 S
    public static $fluorescence_arr = array('N', 'F', 'M', 'S');
    //颜色(Color): D	完全无色   E 无色   F 几乎无色   G   H   I 接近无色   J
    public static $color_arr = array('D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'H', 'D-E', 'E-F', 'F-G', 'G-H', 'H-I', 'I-J', 'J-K');
    //净度(Clarity) FL 完全洁净  IF 内部洁净  VVS1 极微瑕  VVS2  VS1 微瑕  VS2  SI1 小瑕  SI2
    public static $clarity_arr = array('FL', 'IF', 'VVS1', 'VVS2', 'VS1', 'VS2', 'SI1', 'SI2');
    //形状(Shape): 圆形   公主方形   祖母绿形   橄榄形   椭圆形   水滴形   心形  坐垫形   辐射形   方形辐射形   方形祖母绿   三角形
    public static $shape_arr = array(1 => '圆形', 2 => '公主方形', 3 => '祖母绿形', 4 => '橄榄形', 5 => '椭圆形', 6 => '水滴形', 7 => '心形', 8 => '坐垫形', 9 => '辐射形', 10 => '方形辐射形', 11 => '方形祖母绿', 12 => '三角形',13=>'戒指托',14=>'异形',15=>'梨形',16=>'阿斯切',17 => '马眼', 18 => '长方形', 19 => '雷迪恩');
    //证书类型
    public static $cert_arr = array('HRD-D','GIA','HRD','IGI','DIA','AGL','EGL','NGTC','NGGC','HRD-S');
	function __construct ($id=NULL,$strConn="")
	{
            $this->_objName = 'product_info_4c';
            $this->_dataObject = array(
                "id"=>"布产ID",
                "order_sn"=>"订单编号",
                "zhengshuhao_org"=>"原证书号",
                "zhenshuhao"=>"新证书号",                
                "price_org"=>"原采购价格",
                "price"=>"新采购价格",
                "discount_org"=>"原采购折扣",
                "discount"=>"新采购折扣",
                "color"=>"颜色",
                "carat"=>"石重",
                "shape"=>"形状",
                "clarity"=>"净度",
                "peishi_status"=>"配石状态0未完成1已完成",
                "create_user"=>"操作人员",
                "create_time"=>"操作时间",
                
            );
		parent::__construct($id,$strConn);
	}
	
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
	    $sql=$this->getsql($where);
	    //echo $sql;
	    $data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
	    return $data;
	}
	function getList($where){
	   $sql = $this->getsql($where);
	   $data = $this->db()->getAll($sql);
	   return $data;
	}
	function getsql($where=array())
	{
		   
	    $sql = "SELECT c.*,p.bc_sn,p.`status` from ".$this->table()." c LEFT JOIN product_info p on c.id=p.id where 1=1";
	    if(isset($where['order_sn']) && !empty($where['order_sn'])){
	        $sql.= " AND c.`order_sn` = '".$where['order_sn']."'";
	    }
	    if(isset($where['bc_sn']) && !empty($where['bc_sn'])){
	        $sql.= " AND p.`bc_sn` = '".$where['bc_sn']."'";
	    }
	    if(isset($where['zhengshuhao']) && !empty($where['zhengshuhao'])){
	        $sql.= " AND (c.`zhengshuhao` = '".$where['zhengshuhao']."' or c.`zhengshuhao_org` = '".$where['zhengshuhao']."')";
	    }
	    if(isset($where['carat_min']) && !empty($where['carat_min'])){
	       $sql.= " AND c.`carat`>=".$where['carat_min'];
	    }
	    if(isset($where['carat_max']) && !empty($where['carat_max'])){
	        $sql.= " AND c.`carat`<=".$where['carat_max'];
	    }
	    if(isset($where['price_min']) && !empty($where['price_min'])){
	        $sql.= " AND c.`price`>=".$where['price_min'];
	    }
	    if(isset($where['price_max']) && !empty($where['price_max'])){
	        $sql.= " AND c.`price`<=".$where['price_max'];
	    }
	    if(isset($where['discount_min']) && !empty($where['discount_min'])){
	        $sql.= " AND (c.`discount`>=".$where['discount_min']." or c.`discount_org`>=".$where['discount_min'].")";
	    }
	    if(isset($where['discount_max']) && !empty($where['discount_max'])){
	        $sql.= " AND (c.`discount`<=".$where['discount_max']." or c.`discount_org`<=".$where['discount_max'].")";
	    }	    
	    if(isset($where['clarity']) && !empty($where['clarity'])){
            $clarity = implode("','",$where['clarity']);
	        $sql.= " AND c.`clarity` in ('".$clarity."')";
	    }    
	    if(isset($where['color']) && !empty($where['color'])){
            $color = implode("','",$where['color']);
            $sql.= " AND c.`color` in ('".$color."')";
	    }
	    if(isset($where['shape']) && !empty($where['shape'])){
            $shape = implode("','",$where['shape']);
            $sql.= " AND c.`shape` in ('".$shape."')";
	    }
	    if(isset($where['cut']) && !empty($where['cut'])){
            $cut = implode("','",$where['cut']);
            $sql.= " AND c.`cut` in ('".$cut."')";
	    }
	    $sql .=" order by c.id desc";
	    //echo $sql;
	    return $sql;
	}
	public function getAll($fields,$where){
	    if(empty($fields)){
	        $fields = '*';
	    }
	    $sql = "select {$fields} from ".$this->table()." where {$where}";
	    return $this->db()->getAll($sql);
	}
	public function getRow($fields,$where){
	    if(empty($fields)){
	        $fields = '*';
	    }
	    $sql = "select {$fields} from ".$this->table()." where {$where}";
	    return $this->db()->getRow($sql);
	}	
    /**
     * 通过自定义where条件修改当前Model表数据任意字段
     * @param unknown $data 字段值
     * @param unknown $where where条件（拼接字符串）
     * 使用案例：
     * $data = array("field1"=>'XXXX',"field2"=>'xxxx',....);
     * $where = "pk1='XXXXX' and pk2='XXXXX' and ...."
     * $model->update($data,$where);
     */  
	public function update($data,$where){
	    //过滤主键id值
	    if($this->pk() && isset($data[$this->pk()])){
	        unset($data[$this->pk()]);
	    }
	    //通过系统底层函数拼接sql，然后替换掉死板的where条件
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