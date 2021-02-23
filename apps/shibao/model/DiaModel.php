<?php
/**
 *  -------------------------------------------------
 *   @file		: DiaModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-16 17:26:29
 *   @update	:
 *  -------------------------------------------------
 */
class DiaModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'dia';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array(
				"id"=>" ",
				"shibao"=>" ",
				"addtime"=>" ",
				"status"=>" ",
				"kucun_cnt"=>"库存数量",
				"MS_cnt"=>"买入数量",
				"fenbaoru_cnt"=>"分包转入数量",
				"SS_cnt"=>"送出数量",
				"fenbaochu_cnt"=>"分包转出数量",
				"HS_cnt"=>"还回数量-镶嵌",
				"TS_cnt"=>"退石数量",
				"YS_cnt"=>"遗失数量",
				"SY_cnt"=>"损坏数量",
				"TH_cnt"=>"退货数",
				"RK_cnt"=>"其他入库数量",
				"CK_cnt"=>"其他出库数量",
				"kucun_zhong"=>" ",
				"MS_zhong"=>" ",
				"fenbaoru_zhong"=>"分包转入重量",
				"SS_zhong"=>" ",
				"fenbaochu_zhong"=>"分包转出重量",
				"HS_zhong"=>" ",
				"TS_zhong"=>" ",
				"YS_zhong"=>" ",
				"SY_zhong"=>" ",
				"TH_zhong"=>"退货重",
				"RK_zhong"=>" ",
				"CK_zhong"=>" ",
				"yuanshicaigouchengben"=>"原始采购成本",
				"caigouchengben"=>"每卡采购价格",
				"xiaoshouchengben"=>"每卡销售价格"
		);
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url DiaController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT *,(SS_cnt-HS_cnt-TS_cnt) as cha_cnt,(SS_zhong-HS_zhong-TS_zhong) as cha_zhong FROM `".$this->table()."`";
		$str = ' WHERE status = 1 ';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
		if(!empty($where['shibao']))
			{
				$str .= "and `shibao` like \"%".addslashes($where['shibao'])."%\"";
			}
		if(!empty($where['kucun_cnt']))
		{
			$str .= "and `kucun_cnt` > 0 ";
		}
		/* if($where['select_kc']==2)
			{
				$str .= "`kucun_cnt`>0 ";
			} */
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=$str;
		}
		$sql .= " ORDER BY `id` DESC";
		//echo $sql;exit;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	/******************************************************
	fun:getInfoByShibao
	description:根据石包号获取石包信息
	*******************************************************/
	public function getInfoByShibao($shibao)
	{
		//暂时*
		$sql = "select * from ".$this->table()." where shibao ='{$shibao}'";
		return $this->db()->getRow($sql);
	}
	/******************************************************
	fun:shibao_exist
	description:检查石包号是否存在 
	return:1:存在 0 :不存在
	*******************************************************/
	public function shibao_exist($shibao)
	{
		$sql = "select count(1) from ".$this->table()." where shibao ='{$shibao}'";
		return $this->db()->getOne($sql);
	}
	
	/******************************************************
	fun:opt_shibao
	description:重新核算对应单据数量重量，并且更新库存数量和重量
	*******************************************************/
	function opt_shibao($shibao, $type, $num, $zongzhong)
	{
		$opt_cnt	= $type . '_cnt';
		$opt_zhong	= $type . '_zhong';
		$sql2 ="update dia set `$opt_cnt` = `$opt_cnt` + $num , `$opt_zhong` = `$opt_zhong` + $zongzhong where shibao = '$shibao'";
		$this->db()->query($sql2);
		$sql = "update dia set `kucun_cnt` =  `MS_cnt` + `fenbaoru_cnt` - `SS_cnt` - `fenbaochu_cnt` + `TS_cnt` - `YS_cnt` - `SY_cnt` - `TH_cnt` + `RK_cnt` - `CK_cnt`, `kucun_zhong` =  `MS_zhong` + `fenbaoru_zhong` - `SS_zhong` - `fenbaochu_zhong` + `TS_zhong` - `YS_zhong` - `SY_zhong` - `TH_zhong`+ `RK_zhong` - `CK_zhong` where shibao = '$shibao'";
		return $res = $this->db()->query($sql);
	}

	/***************************************************************************************
	fun:opt_shibao_one   暂时不用
	description：重新核算对应单据数量重量，并且更新库存数量和重量，更新销售价
	para:@shibao石包号, @type单据类型, @num 数量, @zongzhong 总重
	*****************************************************************************************/
	function opt_shibao_one($shibao, $type, $num, $zongzhong,$xiaoshouchengben)
	{
		$opt_cnt	 = $type . '_cnt';
		$opt_zhong	 = $type . '_zhong';
		$shibao_info = $this->getInfoByShibao($shibao);

		$sql ="update dia set `$opt_cnt` = `$opt_cnt` + $num , `$opt_zhong` = `$opt_zhong` + $zongzhong, `kucun_cnt` =  `MS_cnt` + `fenbaoru_cnt` - `SS_cnt` - `fenbaochu_cnt` + `TS_cnt` - `YS_cnt` - `SY_cnt` - `TH_cnt` + `RK_cnt` - `CK_cnt`, `kucun_zhong` =  `MS_zhong` + `fenbaoru_zhong` - `SS_zhong` - `fenbaochu_zhong` + `TS_zhong` - `YS_zhong` - `SY_zhong` - `TH_zhong`+ `RK_zhong` - `CK_zhong` where shibao = '$shibao'";
		return $res = $this->db()->query($sql);
	}	


	/******************************************************
	fun:fen_shibao
	description:分包
	*******************************************************/
	function fen_shibao($shibao, $num, $zongzhong , $caigouchengben, $xiaoshouchengben)
	{
		$addtime = date("Y-m-d H:i:s");
		$sql ="INSERT INTO `dia` (`shibao`, `addtime`,`kucun_cnt`, `fenbaoru_cnt`,`kucun_zhong`, `fenbaoru_zhong`, `caigouchengben`, `xiaoshouchengben` ) VALUES ('$shibao', '$addtime', '$num','$num', '$zongzhong', '$zongzhong', '$caigouchengben','$xiaoshouchengben')";
		return $res = $this->db()->query($sql);
	}
}

?>
