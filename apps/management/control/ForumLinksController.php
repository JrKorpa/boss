<?php
/**
 *  -------------------------------------------------
 *   @file		: ForumLinksController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-17 15:50:18
 *   @update	:
 *  -------------------------------------------------
 */
class ForumLinksController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('forum_links_search_form.html',array('bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'title'=>_Request::get("title"),
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['title'] = $args['title'];

		$model = new ForumLinksModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'forum_links_search_page';
		$this->render('forum_links_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('forum_links_info.html',array(
			'view'=>new ForumLinksView(new ForumLinksModel(1))
		));
		$result['title'] = '友情链接-添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('forum_links_info.html',array(
			'view'=>new ForumLinksView(new ForumLinksModel($id,1))
		));
		$result['title'] = '友情链接-编辑';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$title = _Post::get('title');
		$url_addr = _Post::get('url_addr');

		$olddo = array();
		$newdo=array(
			'title'=>$title,
			'url_addr'=>$url_addr,
		);

		if(isset($_FILES['userfile']))
		{
			$type = Upload::getExt($_FILES['userfile']['name']);
			$upload = new Upload();

			if(!in_array($type,$upload->img)){
				$result['error'] = "文件不符合类型！";
				Util::jsonExit($result);
			}

			$res = $upload->toUP($_FILES['userfile']);

			if(is_array($res)){
				$newdo['url_img'] = $res['url'];
			}else{
				$result['error'] = $res;
				Util::jsonExit($result);
			}
		}

		$newmodel =  new ForumLinksModel(2);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
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
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');
		$title = _Post::get('title');
		$url_addr = _Post::get('url_addr');

		$newmodel =  new ForumLinksModel($id,2);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=> $id,
			'title'=>$title,
			'url_addr'=>$url_addr,
		);

		if(isset($_FILES['userfile']))
		{
			$upload = new Upload();
			$type = $upload->getExt($_FILES['userfile']['name']);
			if(!in_array($type,$upload->img)){
				$result['error'] = "文件不符合类型！";
				Util::jsonExit($result);
			}
			$res = $upload->toUP($_FILES['userfile']);

			if(is_array($res)){
				$newdo['url_img'] = $res['url'];
			}else{
				$result['error'] = $res;
				Util::jsonExit($result);
			}
		}

		$res = $newmodel->saveData($newdo,$olddo);
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
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new ForumLinksModel($id,2);
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	listAll，排序页面
	 */
	public function listAll ()
	{
		$result = array('success' => 0,'error' => '');
		$menu_id = _Post::getInt('id');
		$model = new ForumLinksModel($menu_id,1);
		$data = $model->getList();
		$result['content'] = $this->fetch('links_sort.html',array('data'=>$data));
		$result['title'] = '友情链接-排序';
		Util::jsonExit($result);
	}

	/**
	 *	saveSort,排序保存
	 */
	public function saveSort ()
	{
		$result = array('success' => 0,'error' => '');
		$ids = _Post::getList('linksArray');
		krsort($ids);
		$ids = array_values($ids);
		$model = new ForumLinksModel(2);
		$res = $model->sortLink($ids);
		if($res)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}
}

?>