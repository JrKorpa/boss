<?php
/**
 *  -------------------------------------------------
 *   @file		: MaterialGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2018-01-18 11:08:37
 *   @update	:
 *  -------------------------------------------------
 */
class MaterialGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'material_goods';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"style_sn"=>"款号",
"style_name"=>"款式名称",
"goods_sn"=>"货品编号",
"goods_name"=>"货品名称",
"goods_spec"=>"货品规格",
"catetory1"=>"分类1",
"catetory2"=>"分类2",
"catetory3"=>"分类3",
"create_user"=>"添加人",
"create_time"=>"添加时间",
"update_user"=>"修改人",
"update_time"=>"修改时间",
"goods_sale_price"=>"指定销售价",
"goods_jiajialv"=>"货品加价率",
"goods_type"=>"货品类型",
"min_qty"=>"起订量", 
"pack_qty"=>"装箱量", 
"caizhi"=>"材质", 
"remark"=>"备注"
        );
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url MaterialGoodsController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT mg.*,bsi.jiajialv FROM `".$this->table()."` as mg left join front.base_style_info bsi on mg.style_sn=bsi.style_sn";
		$str = '';
        if(!empty($where['style_sn'])){
            $str .=" mg.style_sn = '{$where['style_sn']}'  AND ";
        }

        if(!empty($where['goods_sn'])){
            $str .=" mg.goods_sn = '{$where['goods_sn']}'  AND ";
        }

        if(!empty($where['style_name'])){
            $str .=" mg.style_name like '%{$where['style_name']}%'  AND ";
        }

        if(!empty($where['goods_name'])){
            $str .=" mg.goods_name like '%{$where['goods_name']}%'  AND ";
        }

        if(!empty($where['goods_spec'])){
            $str .=" mg.goods_spec like '%{$where['goods_spec']}%'  AND ";
        }

        if(!empty($where['catetory1'])){
            $str .=" mg.catetory1 = '%{$where['catetory1']}'  AND ";
        }


        if(!empty($where['catetory2'])){
            $str .=" catetory2 = '{$where['catetory2']}'  AND ";
        }

        if(!empty($where['catetory3'])){
            $str .=" catetory3 = '{$where['catetory3']}'  AND ";
        }

        if(!empty($where['cost'])){
            $str .=" cost = '{$where['cost']}' AND ";
        }
        
        if(!empty($where['goods_status'])){
            $str .=" goods_status = '{$where['goods_status']}' AND ";
        }       
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
        if(!empty($where['order_by_field']))
            $sql .= " ORDER BY mg.{$where['order_by_field']} ";
        else        
		    $sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	
	/**
	 * 修改货品状态
	 * @param unknown $id
	 * @param unknown $status
	 */
	public function editGoodsStatus($id,$status){
	    if(is_array($id)){
	        $sql = "update ".$this->table()." set goods_status={$status} where id in(".implode(',',$id).")";
	    }else{
	        $sql = "update ".$this->table()." set goods_status={$status} where id ={$id}";
	    }
	    return $this->db()->query($sql);
	}
}

?>