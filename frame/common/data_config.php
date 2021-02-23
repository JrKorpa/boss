<?php
/**
 *  -------------------------------------------------
 *   @file		: Inc_configs.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: wuhongchen <346332451@qq.com>
 *   @date		:
 *   @update	:
 *  -------------------------------------------------
 */
define('API_AUTH_KEYS', json_encode(array(
    'bespoke' => 'BespOkE',
    'diamond' => 'DIAmoNd',
    'finance' => 'fiNAncE',
    'giftman' => 'GIFtman',
    'management' => 'mNgMt',
    'processor' => 'prOces2Or',
    'purchase' => 'puRChAse',
    'refund' => 'refUnD',
    'repairorder' => 'repairORdEr',
    'salepolicy' => 'saLePOlIcy',
    'sales' => 'SaleS',
    'shibao' => 'shiBaO',
    'shipping' => 'shiP2ing',
    'style' => 'stYLe',
    'warehouse' => 'WAReHoUSe'
)));

define('BCD_PREFIX', 'BC'); //布产单前缀

define('CGD_PREFIX', 'CGD'); //采购单前缀

define('SYS_NAME','运营管理系统'); // page title

define('SYS_SCOPE', 'boss');  // boss or zhanting

define('THE_EXCHANGE_RATE', '7.7');  // 国际汇率