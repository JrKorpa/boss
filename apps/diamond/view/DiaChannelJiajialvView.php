<?php
/**
 *  -------------------------------------------------
 *   @file		: DiamondJiajialvView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-15 15:56:34
 *   @update	:
 *  -------------------------------------------------
 */
class DiaChannelJiajialvView extends View
{
	public function getCertList() {
		return DiamondInfoModel::$cert_arr;
	}
}
?>