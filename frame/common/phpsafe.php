<?php
/**
 *  -------------------------------------------------
 *   @file		: phpsafe.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-10-29
 *   @update	:
 *  -------------------------------------------------
 */
//function customError($errno, $errstr, $errfile, $errline)
//{
//	die( "<b>Error number:</b> [$errno],error on line $errline in $errfile<br />");
//}
//set_error_handler("customError",E_ERROR);
$getfilter="'|(and|or)\\b.+?(>|<|=|in|like)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
$postfilter="\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|\\bUNION\\b.+?\\bSELECT\\b|\\bUPDATE\\b.+?\\bSET\\b|\\bINSERT\\b\\s+\\bINTO\\b.+?\\bVALUES\\b|(\\bSELECT\\b|\\bDELETE\\b).+?\\bFROM\\b|(\\bCREATE\\b|\\bALTER\\b|\\bDROP\\b|\\bTRUNCATE\\b)\\s+(\\bTABLE\\b|\\bDATABASE\\b)";
$cookiefilter="\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";

function StopAttack($StrFiltKey,$StrFiltValue,$ArrFiltReq)
{
	if(is_array($StrFiltValue))
	{
		foreach($StrFiltValue AS $k =>$val){
			if(is_array($val)){
				StopAttack($StrFiltKey,$val,$ArrFiltReq);
			}
			else
			{
				if (preg_match("/".$ArrFiltReq."/is",$val)==1)
				{
					die( "提示:操作非法!");
				}
			}
		}
	}
	else
	{
		if (preg_match("/".$ArrFiltReq."/is",$StrFiltValue)==1)
		{
			die( "提示:操作非法!");
		}
	}
}

foreach($_GET as $key=>$value)
{
	StopAttack($key,$value,$getfilter);
}
foreach($_POST as $key=>$value)
{
	StopAttack($key,$value,$postfilter);
}
foreach($_COOKIE as $key=>$value)
{
	StopAttack($key,$value,$cookiefilter);
}
?>