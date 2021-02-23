<?php
/**
 *  -------------------------------------------------
 *   @file		: ConfItemController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-29 18:03:38
 *   @update	:
 *  -------------------------------------------------
 */
class ConfItemController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('downLoad');

	/**
	 *	index，默认页
	 */
	public function index ($params)
	{
		if(APP_DEBUG && Auth::$userType=1)
		{
			$this->render('conf_item_search_form.html',array('bar'=>Auth::getBar()));
		}else{
			die('无权操作');
		}
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$model = new ConfItemModel();
		$this->render('conf_item_search_list.html',array(
			'param'=>$model->getParam()
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');

		$result['content'] = $this->fetch('conf_item_info.html',
			array('view'=>new ConfItemView())
		);

		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);

		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('conf_item_info.html',array(
			'view'=>new ConfItemView($id))
		);
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息写入
	 */
	public function insert ()
	{
		$result = array('success' => 0,'error' =>'');
		$model = new ConfItemModel();
		$id = count($model->getParam());
		if($this->save($id) !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ()
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');
		$res = $this->save($id);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	save 保存信息
	 */
	private function save($id=null)
	{
		$model = new ConfItemModel();
		$param = $model->getParam();

		$param[$id]['item'] = _Post::get('item');
		$param[$id]['db_type'] = 'mysql';
		$param[$id]['db_host'] = _Post::get('db_host');
		$param[$id]['db_port'] = _Post::get('db_port');
		$param[$id]['db_name'] = _Post::get('db_name');
		$param[$id]['db_user'] = _Post::get('db_user');
		$param[$id]['db_pwd'] = _Post::get('db_pwd');
		$param[$id]['note'] = _Post::get('note');

		//rules验证
		$vd = new Validator();
		$vd->set_rules('item', '配置项',  'require|isLegal');
		$vd->set_rules('db_host', '服务器',  'require|isLegal');
		$vd->set_rules('db_name', '数据库',  'require|isLegal');
		$vd->set_rules('db_port', '端口',  'require|isNumer');
		$vd->set_rules('db_user', '用户名',  'require|isUsername');
		$vd->set_rules('db_pwd', '密码',  'require|isLegal');

		if (!$vd->is_valid($_POST))
		{
			$result['error'] = $vd->get_errors();
			Util::jsonExit($result);
		}

		$res = $model->gen_doc($param);
		return $res;
	}
	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);

		if($id >2){  //默认第一条记录不允许删
			$model = new ConfItemModel();
			$param = $model->getParam();
			$param[$id] = null;
			$res = $model->gen_doc($param);
		}else{
			$res = false;
		}

		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/**
	 * getDoc	生成文档并提供下载地址
	 */
	public function getDoc()
	{
		$model = new ConfItemModel();
		$param = $model->getParam();
		foreach ($param as $k => $v) {
			if(isset($v['note'])){
				unset ($param[$k]['note']);
			}
		}
		$str = $model->arraytostr($param);

		$file_path = KELA_ROOT.'/apps/management/tmp/template_c/'.Util::random(40).'.html.php';
		$res = file_put_contents($file_path,$str,FILE_USE_INCLUDE_PATH);
		$url = Util::getDomain().'/index.php?mod=management&con=ConfItem&act=downLoad&filename='.$file_path;
		echo ($res)?$url:$res;
	}

	public function downLoad () 
	{
		$filename = _Get::get('filename');
		$filename = iconv('utf-8', 'gb2312', $filename);
		if(!is_file($filename))
		{
			die ("<br/>没有该文件!");	
		}

		$fp = fopen($filename, 'r');
		$file_size = filesize($filename);
		header("Content-type:application/octet-stream");
		header("Accept-Ranges:bytes");
		header("Accept-Length:".$file_size);
		header("Content-Disposition:attachment;filename=web.config");
		$buffer = 1024;
		$file_count = 0;

		while(!feof($fp) && $file_count<$file_size) {
			$file_con = fread($fp, $buffer);
			$file_count += $buffer;
			echo $file_con;
		}
		fclose($fp); 
		unlink($filename);
	}

	/**
	 * 生产脚本页面
	 */
	public function export(){
		$this->render('conf_item_data_export.html',[
			'bar'=>Auth::getViewBar()
		]);
	}

	public function getOldConf(){
		print_r($_POST);exit;
	}



}

?>