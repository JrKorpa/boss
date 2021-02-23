<?php
class Page
{
	//计算总共的页数
	public function pages($pagesize=20,$allnum)
	{
		if(empty($pagesize) || empty($allnum))
		{
			return false;
		}
		if($pagesize == $allnum)
		{
			return 1;
		}
		
		$num = ( $allnum / $pagesize);
		$numt = (int)$num + 1;
		$page_num = ($allnum%$pagesize==0)? $num : $numt;
		return $page_num;
	}
	
	//计算偏移量
	public function offset($page=1,$pagesize=10000)
	{
		return ($page-1)*$pagesize;
	}
	
	
	public function pagelink($pages,$pagenow)
	{
		$selfphp = $_SERVER['REQUEST_URI'];
		$urlarr = parse_url($selfphp);
		//获取参数
		
		$parames = isset($urlarr['query']) ? $urlarr['query'] : 1;
		$arr = explode('&page=',$parames);
		$count = count($arr);
		if($count < 2)
		{
			//说明是首页或者是第一页
			$pagetogou = $selfphp;	
		}else{
			$pagetogou = $urlarr['path'].'?'.$arr[0];	
		}
		for($i=1;$i<$pages;$i++)
		{
			if($pagenow == $i)
			{
				echo <<< HTML
			<font class="red">$i</font>
HTML;
			}else{
				echo <<< HTML
			<a href="javascript:;" onclick="showpage('$i')"">$i</a>&nbsp;&nbsp;
HTML;
			}
		}
	}
}
?>