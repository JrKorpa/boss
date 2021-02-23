<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace com\vip\product\gpdc\service;
final class ProductStatus{
	
	
	const DEFAULT_STATUS = 0;
	
	const DRAFT = 11;
	
	const PENDING = 12;
	
	const PASS = 13;
	
	const REJECT = 14;
	
	const HIDDEN = 15;
	
	const DELETED = -1;
	
	const OUTSALE = 3;
	
	static public $__names = array(
	
	0 => 'DEFAULT_STATUS',
	
	11 => 'DRAFT',
	
	12 => 'PENDING',
	
	13 => 'PASS',
	
	14 => 'REJECT',
	
	15 => 'HIDDEN',
	
	-1 => 'DELETED',
	
	3 => 'OUTSALE',
	
	);
}

?>