<?php
/**
 * 成品定制码管理
 *  -------------------------------------------------
 *   @file		: BaseCpdzCodeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-02-27 15:27:55
 *   @update	:
 *  -------------------------------------------------
 */
class BaseCpdzCodeController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('exportSearch');
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
	    $model = new BaseCpdzCodeModel(11);
	    $view = new BaseCpdzCodeView($model);
		$this->render('base_cpdz_code_search_form.html',
		    array('bar'=>Auth::getBar(),
		        'view'=>$view,
		    ));
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
            'style_channel_id' => _Request::get('style_channel_id'),//款式来源渠道
            'sales_channel_id' => _Request::get('sales_channel_id'),//订单销售渠道
            'code' => _Request::get('code'),  //定制码          
            'order_sn' => _Request::get('order_sn'),//订单号   
            'create_user' => _Request::get('create_user'),//创建人
            'use_status' => _Request::getInt('use_status'),//使用状态 1未使用 2使用中 3已使用
		    'sales_user' => _Request::get('use_user'),//使用人  （销售顾问）
		);
		$page = _Request::getInt("page",1);
		$where = $args;

		$model = new BaseCpdzCodeModel(11);
		$view = new BaseCpdzCodeView($model);
		$data = $model->pageList($where,$page,10,false);
		
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'base_cpdz_code_search_page';
		$this->render('base_cpdz_code_search_list.html',array(            
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
		    'view'=>$view,
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
	    $model = new BaseCpdzCodeModel(11);
	    $view = new BaseCpdzCodeView($model);
	    
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('base_cpdz_code_info.html',array(
			'view'=>$view
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
	    $result = array('content' =>"",'title'=>'编辑');
	    $id = _Request::get('id');
	    if(empty($id)){
	        $result['content'] = '参数错误：id 为空！';
	        Util::jsonExit($result);
	    }
	    
	    $model = new BaseCpdzCodeModel($id,11);
	    $view = new BaseCpdzCodeView($model);
	    
	    $olddo = $model->getDataObject();	    
	    if(empty($olddo)){
	        $result['content'] = '编辑对象不存在，可能已被删除！';
	        Util::jsonExit($result);
	    }else if($olddo['use_status']!=1){
	        $result['content'] = '定制码已被使用，不能编辑！';
	        Util::jsonExit($result);
	    }
	    		
		$result['content'] = $this->fetch('base_cpdz_code_info.html',array(
			'view'=>$view
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}	
	/**
	 * 定制码生成
	 * @return string
	 */
    protected function createCpdzCode(){
        return rand(10000,99999);
    }
	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
        //款式来源渠道  ID|NAME 组合参数  
		$style_channel = _Post::get('style_channel');
		if(empty($style_channel)){
		    $result['error'] = '款式来源渠道不能为空！';
		    Util::jsonExit($result);
		}else{
		    $arr = explode("|", $style_channel);
		    if(count($arr)<>2){
    		    $result['error'] = '参数错误：款式来源渠道不合法！';
    		    Util::jsonExit($result);
		    }
		    $style_channel_id = $arr[0];
		    $style_channel_name = $arr[1];
		}
		
        $newdo = array(
             "price"=>_Post::get("price"),
             "style_channel_id"=>$style_channel_id,
             "style_channel"=>$style_channel_name,
             "order_detail_id"=>null,
             "create_user"=>$_SESSION['userName'],
             "create_time"=>date("Y-m-d H:i:s"),            
             "use_status"=>1,
        );
        if(empty($newdo['price']) || !is_numeric($newdo['price'])){
            $result['error'] = '成交价不合法，必须为大于0的数字！';
            Util::jsonExit($result);
        }			
		
		$model = new BaseCpdzCodeModel(11);
		$id = $model->saveData($newdo,array());
		if($id !== false)
		{		    
		    $model->update(array("code"=>$id), "id={$id}");
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
	    $id = _Request::get('id');
	    
	    if(empty($id)){
	        $result['error'] = '参数错误：id 为空！';
	        Util::jsonExit($result);
	    }	    
	    $model = new BaseCpdzCodeModel($id,11);
	    $olddo = $model->getDataObject();
	    if(empty($olddo)){
	        $result['error'] = '编辑对象不存在，可能已被删除！';
	        Util::jsonExit($result);
	    }else if($olddo['use_status']!=1){
	        $result['error'] = '定制嘛已被使用，不能编辑！';
	        Util::jsonExit($result);
	    }
	    //款式来源渠道  ID|NAME 组合参数
	    $style_channel = _Post::get('style_channel');
	    if(empty($style_channel)){
	        $result['error'] = '款式来源渠道不能为空！';
	        Util::jsonExit($result);
	    }else{
	        $arr = explode("|", $style_channel);
	        if(count($arr)<>2){
	            $result['error'] = '参数错误：款式来源渠道不合法！';
	            Util::jsonExit($result);
	        }
	        $style_channel_id = $arr[0];
	        $style_channel_name = $arr[1];
	    }
	    
	    $newdo = array(
	        "id"=>$id,
	        "price"=>_Post::get("price"),
	        "style_channel_id"=>$style_channel_id,
	        "style_channel"=>$style_channel_name,	        
	    );
	    if(empty($newdo['price']) || !is_numeric($newdo['price'])){
	        $result['error'] = '成交价不合法，必须为大于0的数字！';
	        Util::jsonExit($result);
	    }
	    
	    $model = new BaseCpdzCodeModel($id,11);
	    $res = $model->saveData($newdo,$olddo);
	    if($res !== false)
	    {
	        $result['success'] = 1;
	    }
	    else
	    {
	        $result['error'] = '修改失败'.var_export($newdo,true);
	    }
	    Util::jsonExit($result);
	}

	/**
	 *	删除，单个删除
	 */
	public function delete($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = _Request::get('id');
		 
		if(empty($id)){
		    $result['error'] = '参数错误：id 为空！';
		    Util::jsonExit($result);
		}
		$model = new BaseCpdzCodeModel($id,11);
		$olddo = $model->getDataObject();
		if(empty($olddo)){
		    $result['error'] = '删除成功！';
		    Util::jsonExit($result);
		}else if($olddo['use_status']!=1){
		    $result['error'] = '定制码已被使用，不能删除！';
		    Util::jsonExit($result);
		}
		$res = $model->delete();
	    if($res !== false)
	    {
	        $result['success'] = 1;
	    }
	    else
	    {
	        $result['error'] = '删除失败';
	    }
		Util::jsonExit($result);
	}
	
	public function exportSearch($params){
	    $args = array(
	        'mod'	=> _Request::get("mod"),
	        'con'	=> substr(__CLASS__, 0, -10),
	        'act'	=> __FUNCTION__,
	        'style_channel_id' => _Request::get('style_channel_id'),//款式来源渠道
	        'sales_channel_id' => _Request::get('sales_channel_id'),//订单销售渠道
	        'code' => _Request::get('code'),  //定制码
	        'order_sn' => _Request::get('order_sn'),//订单号
	        'create_user' => _Request::get('create_user'),//创建人
	        'use_status' => _Request::getInt('use_status'),//使用状态 1未使用 2使用中 3已使用
	        'sales_user' => _Request::get('use_user'),//使用人  （销售顾问）
	    );
	    $page = _Request::getInt("page",1);
	    $where = $args;	    
	    $model = new BaseCpdzCodeModel(11);
	    $view = new BaseCpdzCodeView($model);
	    $data = $model->pageList($where,1,20000);
	    
	    header("Content-Type: text/html; charset=gb2312");
	    header("Content-type:aplication/vnd.ms-excel");
	    header("Content-Disposition:filename=".iconv('utf-8','gb2312','成品定制码').".xls");
	    
	    $csv_header="<table>
            <tr>
            <td>成品定制码</td>
            <td>成交价</td>
            <td>款式来源渠道</td>
            <td>使用状态</td>
            <td>创建人</td>
            <td>创建时间</td>
            <td>订单号</td>
            <td>款号</td>
            <td>销售顾问</td>
            <td>销售渠道</td>
            <td>使用时间</td>
            </tr>";
	    $csv_body = '';
	    if(!empty($data['data'])){
	        foreach ($data['data'] as $kv => $info) {
	            $info['use_status'] = $this->dd->getEnum('cpdzcode.use_status',$info['use_status']);
   
	            $csv_body.="<tr>
	            <td>{$info['code']}</td>
	            <td>{$info['price']}</td>
	                <td>{$info['style_channel']}</td>
	                <td>{$info['use_status']}</td>
	                <td>{$info['create_user']}</td>
	                <td>{$info['create_time']}</td>
	                <td>{$info['order_sn']}</td>
	                <td>{$info['style_sn']}</td>
	                <td>{$info['sales_user']}</td>
	                <td>{$info['sales_channel']}</td>
	                <td>{$info['use_time']}</td>                
	             </tr>";
	           }
	    }
	    $csv_footer="</table>";
	    echo $csv_header.$csv_body.$csv_footer;
	}
    
}

?>