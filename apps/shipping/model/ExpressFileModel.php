<?php
/**
 * 批量快递单文件上传Model
 *  -------------------------------------------------
 *   @file		: ExpressFileModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2015-09-28 
 *   @update	:
 *  -------------------------------------------------
 */
class ExpressFileModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'express_file';
		$this->pk='id';
		$this->_prefix='';
		$this->_dataObject = array(
		    "id"=>"主键ID",
		    "express_id"=>"快递公司ID",
		    "filename"=>"文件名",
		    "is_print"=>"打印状态",
		    "print_time"=>"打印时间",
		    "is_register"=>"登记状态",
		    "register_time"=>"登记时间",
		    "create_user"=>"上传人",
		    "create_time"=>"上传时间",
		    'file_md5'=>'文件md5值'		    		    
		);
		parent::__construct($id,$strConn);
	}
	
	public function getRow($fields="*",$where){
	     $sql = "select {$fields} from ".$this->table()." where {$where}";
	     return $this->db()->getRow($sql);
	}
	public function deleteById($id){
	    $sql = "delete from ".$this->table()." where id = {$id}";
	    return $this->db()->query($sql);
	}
	public function pageList($where,$page,$pageSize=10,$useCache=true)
	{   
	    $sql = "select * from ".$this->table()." where 1=1";
	    //上传人模糊搜索
	    if(isset($where['create_user'])&& $where['create_user']!=''){
	        $sql.=" AND create_user like '%{$where['create_user']}%'";
	    }
	    //打印状态搜索
	    if(isset($where['is_print']) && $where['is_print']!=''){
	        $sql.=" AND is_print = {$where['is_print']}";
	    }
	    //登记状态搜索
	    if(isset($where['is_register']) && $where['is_register']!=''){
	        $sql.=" AND is_register = {$where['is_register']}";
	    }
	    //打印时间
	    if(isset($where['print_time_begin']) && $where['print_time_begin']!=''){
	        $sql.=" AND print_time >= '{$where['print_time_begin']}'";
	    }
	    if(isset($where['print_time_end']) && $where['print_time_end']!=''){
	        $sql.=" AND print_time <= '{$where['print_time_end']} 23:59:59'";
	    }
	    //登记时间
	    if(isset($where['register_time_begin']) && $where['register_time_begin']!=''){
	        $sql.=" AND register_time >= '{$where['register_time_begin']}'";
	    }
	    if(isset($where['register_time_end']) && $where['register_time_end']!=''){
	        $sql.=" AND register_time <= '{$where['register_time_end']} 23:59:59'";
	    }
	    //上传时间
	    if(isset($where['create_time_begin']) && $where['create_time_begin']!=''){
	        $sql.=" AND create_time >= '{$where['create_time_begin']}'";
	    }
	    if(isset($where['create_time_end']) && $where['create_time_end']!=''){
	        $sql.=" AND create_time <= '{$where['create_time_end']} 23:59:59'";
	    }
	    //快递公司搜索
	    if(isset($where['express_id'])&& $where['express_id']!=''){
	        $sql.=" AND express_id={$where['express_id']}";
	    }

	    $sql .= " order by create_time desc";
	    
	    //echo $sql;
	    return $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
	}
	
}

?>