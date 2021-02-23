<?php
/**
 *  -------------------------------------------------
 *   @file		: DeveloperController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 12:27:57
 *   @update	:
 *  -------------------------------------------------
 */
class DeveloperController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('downLoad');

	/**
	 *	index，默认页
	 */
	public function index ($params)
	{
		if(Auth::$userType==1)
		{
			$xx = new IniFile();
			$xx->Load(KELA_PATH.'/common/web.config');
			$data = array();
			foreach ($xx->_Settings as $key => $val )
			{
				$k = substr($key,8);
				if($k%2==1)
				{
					$data[]=array('key'=>$k,'label'=>$key,'db_name'=>$val['db_name']);
				}
			}

			$dir = KELA_ROOT.'/apps';
			$dh = opendir($dir);
			$apps = array();
			while ($row = readdir($dh) )
			{
				if(is_dir($dir.'/'.$row) && $row<>'.' && $row<>'..' && $row<>'.svn')
				{
					$apps[] = $row;
				}
			}
			closedir($dh);
			$this->render('developer.html',array('cfg'=>$data,'apps'=>$apps));
		}
		else
		{
			die('无权操作！');
		}
	}

	/**
	 *	getTables，获取指定数据库的数据表
	 */
	public function getTables ()
	{
		$conn = _Post::getInt('conn_flag');
		$data = DB::cn($conn)->getTables();
		$this->render('developer_options.html',array('data'=>$data));
	}

	public function getFileds ()
	{
		$conn = _Post::getInt('conn');
		$table_name = _Post::get('table_name');
		$data =  DB::cn($conn)->getFields($table_name);
		$this->render('developer_options1.html',array('data'=>$data));
	}

	public function getPk ()
	{
		$conn = _Post::getInt('conn_flag');
		$table_name=_Post::get('table_name');
		$db_name=_Post::get('db_name');
		$sql = "SELECT column_name FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='".$table_name."' AND table_schema='".$db_name."' AND constraint_name='primary'";
		echo DB::cn($conn)->getOne($sql);
	}


	/**
	 *	gen，生成文件
	 */
	public function gen ($params)
	{
		$result = array('success' => 0,'error' =>'');
		if(Auth::$userType!=1)
		{
			$result['error']='无权操作！';
			Util::jsonExit($result);
		}

		$args['mod_name'] = _Post::get('mod_name');
		$args['conn_flag'] = _Post::getInt('conn_flag');
		$args['table_name'] = _Post::get('table_name');
		$args['db_name'] = _Post::get('db_name');
		$args['pri_key'] = _Post::get('pri_key');
		$args['con_name'] = _Post::get('con_name');
		$args['con_type'] = _Post::getInt('con_type');
		$args['detail_name'] = _Post::getList('detail_name');
		$args['foreign_key'] = _Post::get('foreign_key');

		if($args['mod_name']=='')
		{
			$result['error'] ="项目名称不能为空！";
			Util::jsonExit($result);
		}

		if(!$args['conn_flag'])
		{
			$result['error'] ="数据库连接不能为空！";
			Util::jsonExit($result);
		}

		if($args['table_name']=='')
		{
			$result['error'] ="数据表名不能为空！";
			Util::jsonExit($result);
		}

		if($args['db_name']=='')
		{
			$result['error'] ="数据库名称不能为空！";
			Util::jsonExit($result);
		}

		if($args['pri_key']=='')
		{
			$result['error'] ="数据表主键不能为空！";
			Util::jsonExit($result);
		}

		if($args['con_name']=='')
		{
			$result['error'] ="文件名不能为空！";
			Util::jsonExit($result);
		}

		if(!$args['con_type'])
		{
			$result['error'] ="请选择对象类型！";
			Util::jsonExit($result);
		}

		if($args['con_type']==2 && count($args['detail_name'])==0)
		{
			$result['error'] ="请选择明细对象表！";
			Util::jsonExit($result);
		}

		if($args['con_type']==3 && $args['foreign_key']=='')
		{
			$result['error'] ="请选择明细表外键！";
			Util::jsonExit($result);
		}

		if($args['con_type']==2 && in_array($args['table_name'],$args['detail_name']))
		{
			$result['error'] ="明细不能与主表相同！";
			Util::jsonExit($result);
		}

		$dir = $this->buildDir($args);

		if($args['con_type']==1)
		{
			$this->genAlone($args,$dir);
		}
		else if ($args['con_type']==2)
		{
			$this->genMain($args,$dir);
		}
		else if ($args['con_type']==3)
		{
			$this->genDetail($args,$dir);
		}

		$zip = new PclZip($args['table_name'].'.zip');

		$v_list = $zip->create($dir ,PCLZIP_OPT_REMOVE_PATH,$dir);
		Util::rrmdir($dir,false);
		$result = array('success' => 0,'error' =>'');
		if($v_list == 0){
			$result['error'] ='异常：'.$zip->errorInfo(true);
			Util::jsonExit($result);
		}
		else {
			$result['success'] = 1;
			$result['url'] = Util::getDomain().'/index.php?mod=management&con=Developer&act=downLoad&file='.$args['table_name'].'.zip';
			Util::jsonExit($result);
		}
	}

	private function buildDir ($args)
	{
		$dir = 'apps/management/data/'.time();
		mkdir($dir);
		$tmp_name = Util::parseStr3($args['con_name']);
		$mod_name = $args['mod_name'];
		$dirs = array(
			$dir.'/'.'apps',
			$dir.'/'.'apps/'.$mod_name,
			$dir.'/'.'apps/'.$mod_name.'/control',
			$dir.'/'.'apps/'.$mod_name.'/data',
			$dir.'/'.'apps/'.$mod_name.'/logs',
			$dir.'/'.'apps/'.$mod_name.'/model',
			$dir.'/'.'apps/'.$mod_name.'/templates',
			$dir.'/'.'apps/'.$mod_name.'/templates/'.$tmp_name,
			$dir.'/'.'apps/'.$mod_name.'/templates/'.$tmp_name.'/js',
			$dir.'/'.'apps/'.$mod_name.'/view',
			$dir.'/'.'apps/'.$mod_name.'/tmp',
			$dir.'/'.'apps/'.$mod_name.'/tmp/cache',
			$dir.'/'.'apps/'.$mod_name.'/tmp/template_c'
		);
		Util::xmkdir($dirs);
		return $dir;
	}

	private function genAlone ($args,$dir)
	{
		//控制器
		$fileTpl = KELA_PATH.'/template/Controller.tool.php';
		$content = file_get_contents ($fileTpl);
		$keys = array('{CONTROLLER}','{DATE}','{TMPL_PREFIX}','{CONN}','{CONN2}');
		$values = array($args['con_name'],date('Y-m-d H:i:s'),$args['table_name'],$args['conn_flag'],$args['conn_flag']+1);
		$content = str_replace ($keys,$values,$content);
		file_put_contents($dir.'/'.'apps/'.$args['mod_name'].'/control/'.$args['con_name'].'Controller.php',$content);

		//模型
		$sql ="SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$args['table_name']."' AND TABLE_SCHEMA='".$args['db_name']."'";
		$data = DB::cn($args['conn_flag'])->getAll($sql);
		$str = 'array(';
		foreach ($data as $val )
		{
			$str .='"'.$val["COLUMN_NAME"].'"=>'.($val["COLUMN_COMMENT"] ? '"'.$val["COLUMN_COMMENT"].'"' : '" "').",".PHP_EOL;
		}
		$str = rtrim($str,",\r\n");
		$str .=")";

		$fileTpl = KELA_PATH.'/template/Model.tool.php';
		$content = file_get_contents ($fileTpl);
		$keys = array('{MODEL}','{DATE}','{TABLE}','{PK}','{DATA}');
		$values = array($args['con_name'],date('Y-m-d H:i:s'),$args['table_name'],$args['pri_key'],$str);
		$content = str_replace ($keys,$values,$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/model/'.$args['con_name'].'Model.php',$content);

		//view
		$fields = DB::cn($args['conn_flag'])->getFields($args['table_name']);
		$protypeStr = '';
		$methodStr = '';
		foreach ($fields as $val )
		{
			$protypeStr .="\t"."protected \$_".$val['Field'].";".PHP_EOL;
			$methodStr .="\t"."public function get_".$val['Field']."(){return \$this->_".$val['Field'].";}".PHP_EOL;
		}

		$fileTpl = KELA_PATH.'/template/View.tool.php';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{DATE}','{VIEW}','{FIELDS}','{FUNCTIONS}'),array(date('Y-m-d H:i:s'),$args['con_name'],$protypeStr,$methodStr),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/view/'.$args['con_name'].'View.php',$content);

		//tpl
		$tmp_name = Util::parseStr3($args['con_name']);//目录名
		$fileTpl = KELA_PATH.'/template/search_form.html';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{TMPL_PREFIX}'),array($args['table_name']),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/'.$args['table_name'].'_search_form.html',$content);

		$fileTpl = KELA_PATH.'/template/search_list.html';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{PK}','{TMPL_PREFIX}'),array($args['pri_key'],$args['table_name']),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/'.$args['table_name'].'_search_list.html',$content);

		$fileTpl = KELA_PATH.'/template/info.html';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{TMPL_PREFIX}','{PK}'),array($args['table_name'],$args['pri_key']),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/'.$args['table_name'].'_info.html',$content);

		$fileTpl = KELA_PATH.'/template/show.html';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{TMPL_PREFIX}','{DETAIL}'),array($args['table_name'],''),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/'.$args['table_name'].'_show.html',$content);

		//js
		$fileTpl = KELA_PATH.'/template/list.js';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{TMPL_PREFIX}','{TMPL_MOD}','{TMPL_CTL}'),array($args['table_name'],$args['mod_name'],$args['con_name']),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/js/'.$args['table_name'].'_list.js',$content);

		$fileTpl = KELA_PATH.'/template/info.js';
		$content = file_get_contents ($fileTpl);
		$keys = array('{PK}','{TMPL_MOD}','{TMPL_CTL}','{TMPL_PREFIX}');
		$values = array($args['pri_key'],$args['mod_name'],$args['con_name'],$args['table_name']);

		$content = str_replace ($keys,$values,$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/js/'.$args['table_name'].'_info.js',$content);

		$fileTpl = KELA_PATH.'/template/show0.js';
		$content = file_get_contents ($fileTpl);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/js/'.$args['table_name'].'_show.js',$content);
	}

	private function genMain ($args,$dir)
	{
		//控制器
		$fileTpl = KELA_PATH.'/template/Controller.tool.php';
		$content = file_get_contents ($fileTpl);
		$keys = array('{CONTROLLER}','{DATE}','{TMPL_PREFIX}','{CONN}','{CONN2}');
		$values = array($args['con_name'],date('Y-m-d H:i:s'),$args['table_name'],$args['conn_flag'],$args['conn_flag']+1);
		$content = str_replace ($keys,$values,$content);
		file_put_contents($dir.'/'.'apps/'.$args['mod_name'].'/control/'.$args['con_name'].'Controller.php',$content);

		//模型
		$sql ="SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$args['table_name']."' AND TABLE_SCHEMA='".$args['db_name']."'";
		$data = DB::cn($args['conn_flag'])->getAll($sql);
		$str = 'array(';
		foreach ($data as $val )
		{
			$str .='"'.$val["COLUMN_NAME"].'"=>'.($val["COLUMN_COMMENT"] ? '"'.$val["COLUMN_COMMENT"].'"' : '" "').",".PHP_EOL;
		}
		$str = rtrim($str,",\r\n");
		$str .=")";

		$fileTpl = KELA_PATH.'/template/Model.tool.php';
		$content = file_get_contents ($fileTpl);
		$keys = array('{MODEL}','{DATE}','{TABLE}','{PK}','{DATA}');
		$values = array($args['con_name'],date('Y-m-d H:i:s'),$args['table_name'],$args['pri_key'],$str);
		$content = str_replace ($keys,$values,$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/model/'.$args['con_name'].'Model.php',$content);

		//view
		$fields = DB::cn($args['conn_flag'])->getFields($args['table_name']);
		$protypeStr = '';
		$methodStr = '';
		foreach ($fields as $val )
		{
			$protypeStr .="\t"."protected \$_".$val['Field'].";".PHP_EOL;
			$methodStr .="\t"."public function get_".$val['Field']."(){return \$this->_".$val['Field'].";}".PHP_EOL;
		}

		$fileTpl = KELA_PATH.'/template/View.tool.php';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{DATE}','{VIEW}','{FIELDS}','{FUNCTIONS}'),array(date('Y-m-d H:i:s'),$args['con_name'],$protypeStr,$methodStr),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/view/'.$args['con_name'].'View.php',$content);

		//tpl
		$tmp_name = Util::parseStr3($args['con_name']);//目录名
		$fileTpl = KELA_PATH.'/template/search_form.html';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{TMPL_PREFIX}'),array($args['table_name']),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/'.$args['table_name'].'_search_form.html',$content);

		$fileTpl = KELA_PATH.'/template/search_list.html';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{PK}','{TMPL_PREFIX}'),array($args['pri_key'],$args['table_name']),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/'.$args['table_name'].'_search_list.html',$content);

		$fileTpl = KELA_PATH.'/template/info.html';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{TMPL_PREFIX}','{PK}'),array($args['table_name'],$args['pri_key']),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/'.$args['table_name'].'_info.html',$content);

		//主对象查看页面模板处理
		$detailStr = '';
		$fileTpl = KELA_PATH.'/template/rel_search_form.html';
		$content = file_get_contents ($fileTpl);
		foreach ($args['detail_name'] as $key => $val )
		{
			$detailStr .= str_replace (array('{DETAIL_ITEM}','{KK}'),array($val,$key+1),$content).PHP_EOL;
		}

		$fileTpl = KELA_PATH.'/template/show.html';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{TMPL_PREFIX}','{DETAIL}'),array($args['table_name'],$detailStr),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/'.$args['table_name'].'_show.html',$content);

		//js
		$fileTpl = KELA_PATH.'/template/list.js';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{TMPL_PREFIX}','{TMPL_MOD}','{TMPL_CTL}'),array($args['table_name'],$args['mod_name'],$args['con_name']),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/js/'.$args['table_name'].'_list.js',$content);

		$fileTpl = KELA_PATH.'/template/info.js';
		$content = file_get_contents ($fileTpl);
		$keys = array('{PK}','{TMPL_MOD}','{TMPL_CTL}','{TMPL_PREFIX}');
		$values = array($args['pri_key'],$args['mod_name'],$args['con_name'],$args['table_name']);

		$content = str_replace ($keys,$values,$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/js/'.$args['table_name'].'_info.js',$content);

		//show.js
		$fileTpl = KELA_PATH.'/template/show.js';

		$funcStr = '';
		$objStr = '';
		$fileTpl = KELA_PATH.'/template/show.func.js';
		$content = file_get_contents ($fileTpl);
		$fileTpl = KELA_PATH.'/template/show.obj.js';
		$content1 = file_get_contents ($fileTpl);
		foreach ($args['detail_name'] as $key => $val )
		{
			$funcStr .= str_replace (array('{DETAIL_ITEM}','{ITEM_KEY}'),array($val,$key+1),$content).PHP_EOL;
			$objStr .= str_replace (array('{ITEM_KEY}','{TMPL_MOD}','{TMPL_CTL}','{DETAIL_ITEM}'),array($key+1,$args['mod_name'],Util::parseStr2($val),$val),$content1).PHP_EOL.PHP_EOL.PHP_EOL."\t";
		}

		$fileTpl = KELA_PATH.'/template/show.js';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{SERACH_FUNC}','{SHOW_OBJ}'),array($funcStr,$objStr),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/js/'.$args['table_name'].'_show.js',$content);
	}

	private function genDetail ($args,$dir)
	{
		//控制器
		$fileTpl = KELA_PATH.'/template/Controller.tool2.php';
		$content = file_get_contents ($fileTpl);
		$keys = array('{CONTROLLER}','{DATE}','{TMPL_PREFIX}','{CONN}','{CONN2}','{MAIN_PK}');
		$values = array($args['con_name'],date('Y-m-d H:i:s'),$args['table_name'],$args['conn_flag'],$args['conn_flag']+1,$args['foreign_key']);
		$content = str_replace ($keys,$values,$content);
		file_put_contents($dir.'/'.'apps/'.$args['mod_name'].'/control/'.$args['con_name'].'Controller.php',$content);

		//模型
		$sql ="SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$args['table_name']."' AND TABLE_SCHEMA='".$args['db_name']."'";
		$data = DB::cn($args['conn_flag'])->getAll($sql);
		$str = 'array(';
		foreach ($data as $val )
		{
			$str .='"'.$val["COLUMN_NAME"].'"=>'.($val["COLUMN_COMMENT"] ? '"'.$val["COLUMN_COMMENT"].'"' : '" "').",".PHP_EOL;
		}
		$str = rtrim($str,",\r\n");
		$str .=")";

		$fileTpl = KELA_PATH.'/template/Model.tool2.php';
		$content = file_get_contents ($fileTpl);
		$keys = array('{MODEL}','{DATE}','{TABLE}','{PK}','{DATA}','{MAIN_PK}');
		$values = array($args['con_name'],date('Y-m-d H:i:s'),$args['table_name'],$args['pri_key'],$str,$args['foreign_key']);
		$content = str_replace ($keys,$values,$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/model/'.$args['con_name'].'Model.php',$content);

		//view
		$fields = DB::cn($args['conn_flag'])->getFields($args['table_name']);
		$protypeStr = '';
		$methodStr = '';
		foreach ($fields as $val )
		{
			$protypeStr .="\t"."protected \$_".$val['Field'].";".PHP_EOL;
			$methodStr .="\t"."public function get_".$val['Field']."(){return \$this->_".$val['Field'].";}".PHP_EOL;
		}

		$fileTpl = KELA_PATH.'/template/View.tool.php';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{DATE}','{VIEW}','{FIELDS}','{FUNCTIONS}'),array(date('Y-m-d H:i:s'),$args['con_name'],$protypeStr,$methodStr),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/view/'.$args['con_name'].'View.php',$content);

		//tpl
		$tmp_name = Util::parseStr3($args['con_name']);//目录名

		$fileTpl = KELA_PATH.'/template/search_list.html';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{PK}','{TMPL_PREFIX}'),array($args['pri_key'],$args['table_name']),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/'.$args['table_name'].'_search_list.html',$content);

		$fileTpl = KELA_PATH.'/template/info2.html';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{TMPL_PREFIX}','{PK}'),array($args['table_name'],$args['pri_key']),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/'.$args['table_name'].'_info.html',$content);

		$fileTpl = KELA_PATH.'/template/show.html';
		$content = file_get_contents ($fileTpl);
		$content = str_replace (array('{TMPL_PREFIX}','{DETAIL}'),array($args['table_name'],''),$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/'.$args['table_name'].'_show.html',$content);

		//js
		$fileTpl = KELA_PATH.'/template/info2.js';
		$content = file_get_contents ($fileTpl);
		$keys = array('{PK}','{TMPL_MOD}','{TMPL_CTL}','{TMPL_PREFIX}');
		$values = array($args['pri_key'],$args['mod_name'],$args['con_name'],$args['table_name']);
		$content = str_replace ($keys,$values,$content);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/js/'.$args['table_name'].'_info.js',$content);

		$fileTpl = KELA_PATH.'/template/show0.js';
		$content = file_get_contents ($fileTpl);
		file_put_contents ($dir.'/'.'apps/'.$args['mod_name'].'/templates/'.$tmp_name.'/js/'.$args['table_name'].'_show.js',$content);
	}

	public function downLoad ()
	{
		$file = _Get::get('file');
		if(!is_file($file))
		{
			die ("<br/>没有该文件!");
		}

		$fp = fopen($file, 'r');
		$file_size = filesize($file);
		header("Content-type:application/zip");
		header("Accept-Ranges:bytes");
		header("Accept-Length:".$file_size);
		header("Content-Disposition:attachment;filename=".$file);
		$buffer = 1024;
		$file_count = 0;

		while(!feof($fp) && $file_count<$file_size) {
			$file_con = fread($fp, $buffer);
			$file_count += $buffer;
			echo $file_con;
		}
		fclose($fp);
		unlink($file);
	}

}

?>