<?php

/**
 *  -------------------------------------------------
 *   @file		: UserWarehouseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 10:30:05
 *   @update	:
 *  -------------------------------------------------
 */
class UserCompanyModel extends Model {

        function __construct($id = NULL, $strConn = "") {
                $this->_objName = 'user_extend_company';
                $this->pk = 'id';
                $this->_prefix = '';
                $this->_dataObject = array("id" => "主键id",
                        "user_id" => "用户id",
                        "company_id" => "公司id");
                parent::__construct($id, $strConn);
        }
        
        public function getUserCompanyList($where){
            $sql ="select * from ".$this->table()." where 1=1";
            if(isset($where['user_id'])){
                $sql .=" AND user_id=".(int)$where['user_id'];
            }            
            return $this->db()->getAll($sql);
        }

       public function getUserCompanyList2($where){
            if(isset($where['user_id']) && $where['user_id']==1){
                 $sql ="select id as company_id from cuteframe.company";
                 return $this->db()->getAll($sql);
            }
            $sql ="select * from ".$this->table()." where 1=1";
            if(isset($where['user_id'])){
                $sql .=" AND user_id=".(int)$where['user_id'];
            }            
            return $this->db()->getAll($sql);
        }



        /**
         * 批量插入用户公司关联数据
         * @param unknown $datas
         */
        public function insertCompanyAll($datas){
            $this->insertAll($datas);
        }    
        /**
         * 根据用户id和公司id删除会员公司关联表记录
         * @param array $company
         * @param unknown $user_id
         * @return boolean
         */
        public function deleteCompanyByUser($company_ids,$user_id){

            if(empty($user_id)){
                return false;
            }
            
            $sql ='delete from '.$this->table().' where user_id='.(int)$user_id;
            if(!empty($company_ids)){
                if(is_array($company_ids)){
                    $company_ids = trim(implode($company_ids, ','),',');
                    $sql .=" AND company_id in(".$company_ids.")";
                }else{
                    $sql .=" AND company_id =".(int)$company_ids;
                }
            } 
            return $this->db()->query($sql);
        }    
        
        

}
?>