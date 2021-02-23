<?php
/**
 *  -------------------------------------------------
 *   @file		: AppSalepolicyGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-5-26 15:41
 --  @update	:
 *  -------------------------------------------------
 */
class ProductCreationCodeModel extends Model{
    function __construct($id=NULL,$strConn=""){
        $this->_objName='product_creation_code';
        $this->pk='id';
        $this->_prefix='';
        $this->_dataObject = array(
            "id"=>"产品定制码",
            "department_id"=>"销售渠道",
            "create_user"=>"创建人",
            "order_sn"=>"订单号",
            "department_id_from"=>"款式来源渠道",
            "sale_user"=>"销售顾问",
            "STATUS"=>"0.未使用 1.已使用",
            "style_sn"=>"款号",
            "transaction_price"=>"成交价",
            "created_time"=>"创建时间",
            "udpated_time"=>"更新时间",
        );
        parent::__construct($id,$strConn);
    }
    public function pageList($where,$page=1,$pageSize=10,$useCache=true){
        //$sql="select id,(select channel_name from  cuteframe.sales_channels where id=department_id) as department_id,create_user,order_sn,(select channel_name from  cuteframe.sales_channels where id=department_id_from) as department_id_from,sale_user,status,style_sn,transaction_price from ".$this->table();
        $sql="select a.id,b.channel_name as department_id,a.create_user,a.order_sn,c.channel_name as department_id_from,a.sale_user,a.status,a.style_sn,a.transaction_price from ".$this->table()." a";
        $sql.=" left join cuteframe.sales_channels b on b.id=a.department_id  ";
        $sql.=" left join cuteframe.sales_channels c on c.id=a.department_id_from ";
        $sql.=" where 1=1";
        if(!empty($where['department_id'])){
            $sql.=" AND a.department_id=".$where['department_id'];
        }
        if(!empty( $where['create_user'])){
            $sql .= " AND a.create_user  like \"%".addslashes($where['create_user'])."%\"";
        }
        if(!empty($args['order_sn'])){
            $sql .= " AND a.order_sn in ('".$where['order_sn']."')";
        }
        if(!empty($args['department_id_from'])){
            $sql.=" AND a.department_id_from=".$where['department_id_from'];
        }  
        if(!empty($where['id'])){
            $sql.=" AND a.id=".$where['id'];
        }
        if(!empty($args['sale_user'])){
            $sql .= " AND a.create_user  like \"%".addslashes($where['sale_user'])."%\"";
        }
        $sql.=" ORDER BY  a.id DESC "; 
        
        return $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache) ;
    }
}