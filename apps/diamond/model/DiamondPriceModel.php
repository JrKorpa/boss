<?php

/**
 *  -------------------------------------------------
 *   @file		: DiamondPriceModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 11:31:14
 *   @update	:
 *  -------------------------------------------------
 */
class DiamondPriceModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'diamond_price';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => " ",
            "shape" => " ",
            "clarity" => " ",
            "color" => " ",
            "min" => " ",
            "max" => " ",
            "price" => " ",
            "version" => "上传版本",
            "addtime" => " ");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url DiamondPriceController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE 1 ";
        if(isset($where['version']) && $where['version'] && $where['version']!='00'){
            $sql .= " and `version` = {$where['version']}";
        }
        if(isset($where['shape']) && $where['shape']){
            if($where['shape']=="BR"){
                $sql .= " and `shape`='BR'";
            }else{
                $sql .= " and `shape`!='BR'";
            }
        }
        if(isset($where['clarity']) && $where['clarity']){
            $sql .= " and `clarity`='{$where['clarity']}'";
        }
        if(isset($where['color']) && $where['color']){
            $sql .= " and `color`='{$where['color']}'";
        }
        if(isset($where['min']) && $where['min']){
            $sql .= " and `min`>={$where['min']}";
        }
        if(isset($where['max']) && $where['max']){
            $sql .= " and `max`<={$where['max']}";
        }
        if(isset($where['price_start']) && $where['price_start']){
            $sql .= " and `price`>={$where['price_start']}";
        }
        if(isset($where['price_end']) && $where['price_end']){
            $sql .= " and `price`<={$where['price_end']}";
        }
        $sql .= " ORDER BY `id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }
    
    
    public function getInfoList($where) {
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE 1 ";
        if(isset($where['version']) && $where['version'] && $where['version']!='00'){
            $sql .= " and `version` = {$where['version']}";
        }
        if(isset($where['shape']) && $where['shape']){
            if($where['shape']=="BR"){
                $sql .= " and `shape`='BR'";
            }else{
                $sql .= " and `shape`!='BR'";
            }
        }
        if(isset($where['clarity']) && $where['clarity']){
            $sql .= " and `clarity`='{$where['clarity']}'";
        }
        if(isset($where['color']) && $where['color']){
            $sql .= " and `color`='{$where['color']}'";
        }
        if(isset($where['min']) && $where['min']){
            $sql .= " and `min`>={$where['min']}";
        }
        if(isset($where['max']) && $where['max']){
            $sql .= " and `max`<={$where['max']}";
        }
        if(isset($where['price_start']) && $where['price_start']){
            $sql .= " and `price`>={$where['price_start']}";
        }
        if(isset($where['price_end']) && $where['price_end']){
            $sql .= " and `price`<={$where['price_end']}";
        }
        $sql .= " ORDER BY `id` DESC";
        $data = $this->db()->getAll($sql);
        return $data;
    }
    
    /**
     * 获取最后一条数据id
     * @return type
     */
    public function getLastId($param=0) {
        $sql = "SELECT `id`,`version` FROM `" . $this->table() . "` ";
        if($param > 0){
            $sql .= "where `version` = $param";
        }
        $sql .= " order by `id` desc limit 1";
        return $this->db()->getRow($sql);
    }
    
    /**
     * 获取版本列表
     * @return type
     */
    public function getVersionList() {
        $sql = "SELECT `id`,`version` FROM `" . $this->table() . "` order by `id` desc limit 1";
        $row = $this->db()->getRow($sql);
        if($row){
            $versionList = array();
            for($j=1;$j<=$row['version'];$j++){
                $versionList[$j]['id'] = $j;
                $versionList[$j]['name'] = '版本'.$j;
            }
            return $versionList;
        }
        return array();
    }

    /**
     * 所有
     * @return type
     */
    public function getVersionList_all() {
        $sql = "select `shape` as `xingzhuang`,`clarity` as `jingdu`,`color` as `yanse`,`min`,`max`,`price` from `diamond_price`";
        $row = $this->db()->getAll($sql);
        return $row;
    }

    /**
     * 获取最新的
     * @return type
     */
    public function getVersionList_new($where) {
        $sql = "select `shape` as `xingzhuang`,`clarity` as `jingdu`,`color` as `yanse`,`min`,`max`,`price` from `diamond_price` where `version`=".$where['version'];
        $row = $this->db()->getAll($sql);
        return $row;
    }

    /**
     * 获取净度列表
     * @return array
     */
    function getClarityList() {
        $data = array(
            array('name' => 'I1'),
            array('name' => 'I2'),
            array('name' => 'I3'),
            array('name' => 'IF'),
            array('name' => 'SI1'),
            array('name' => 'SI2'),
            array('name' => 'SI3'),
            array('name' => 'VS1'),
            array('name' => 'VS2'),
            array('name' => 'VVS1'),
            array('name' => 'VVS2')
        );
        return $data;
    }
    
    /**
     * 获取颜色列表
     * @return array
     */
    function getColorList() {
        $data = array(
            array('name' => 'D'),
            array('name' => 'E'),
            array('name' => 'F'),
            array('name' => 'G'),
            array('name' => 'H'),
            array('name' => 'I'),
            array('name' => 'J'),
            array('name' => 'K'),
            array('name' => 'L'),
            array('name' => 'M'),
            array('name' => 'N')
        );
        return $data;
    }
	
	
	//获取最新的报价版本
	function getNewversion()
	{
		$sql = " select version from ". $this->table() ." where 1 order by id desc limit 1" ; 
        $datainfo = $this->db()->getRow($sql);
		return $datainfo['version'];
	}
	//根据裸钻的信息获取最新的国际报价
	function getNewPrice($where)
	{
		if(empty($where) || !is_array($where))
		{
			return 0;
		}
		$sql = "select price,min,max from diamond_price where version='".$where['version']."' ";
		if(isset($where['color']) && !empty($where['color']))
		{
			$sql .=" and color='".$where['color']."'";
		}
		if(isset($where['clarity']) && !empty($where['clarity']))
		{
			$sql .=" and clarity='".$where['clarity']."'";
		}
		if(isset($where['shape']) && !empty($where['shape']))
		{
			$sql .=" and shape='".$where['shape']."'";
		}
		if(isset($where['carat']) && !empty($where['carat']))
		{
			$sql .=" and min<='".$where['carat']."' and max >= '".$where['carat']."' ";
		}
		return $this->db()->getRow($sql);
	}

}

?>