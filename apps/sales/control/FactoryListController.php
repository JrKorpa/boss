<?php
/**
 *  -------------------------------------------------
 *   @file		: FactoryListController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 21:06:55
 *   @update	:
 *  -------------------------------------------------
 */
class FactoryListController extends Controller
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{

		$this->render('factory_list_form.html',array(
				'bar'=>Auth::getBar(),
				'dd'=>new DictView(new DictModel(1)),
                'view'=>new FactoryListView(new FactoryListModel(27))
			));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
            $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
            $pageSize = isset($_REQUEST["pageSize"]) ? intval($_REQUEST["pageSize"]) : 15 ;

            $args = array(
                'mod'	=> _Request::get("mod"),
                'con'	=> substr(__CLASS__, 0, -10),
                'act'	=> __FUNCTION__,
                'page'=>  $page,
                'pageSize'=>  $pageSize,
                'style_sn'=>  _Request::getString('style_sn'),

            );
            $where = array(
                'pageSize'=>  $pageSize,
                'style_sn'=>  _Request::getString('style_sn'),

            );
            $model = new FactoryListModel(27);
            $data = $model->pageList($where);
            //var_dump($data);exit;
            $datalist = $data;
            $pageData['filter'] = $args;
            $pageData['jsFuncs'] = 'factory_list_search_page';
            $this->render('factory_list_search_list.html',array(
                    'pa'=>Util::page($pageData),
                    'dd'=>new DictView(new DictModel(1)),
                    'page_list'=>$datalist
                   
            ));
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$model = new FactoryListModel(27);
		$data = $model->getRowById($id);
        if(empty($data['data'])){
			die('数据错误!');
        }
		$this->render('factory_list_show.html',array(
			'd'=>$data['data'],
            'dd'=>new DictView(new DictModel(1)),
		));
	}

     //加入购物车
    public function addCart(){
        $id = _Request::getInt('id');
        $model = new FactoryListModel(27);
		$data = $model->getRowById($id);

        if(empty($data['data'])){
            $result['error'] = '数据错误!';
            Util::jsonExit($result);
        }

        $goods_id = $data['data']['goods_id'];
        $cartModel = new AppOrderCartModel(27);
        //裸钻只有一个所以去重
        $cartList = $cartModel->get_cart_goods();
        if($cartList){
            foreach ($cartList as $val){
                if($val['goods_id'] == $goods_id){
                     $result['error'] = '此商品已经添加!';
                     Util::jsonExit($result);
                }
            }
        }

        $parent["session_id"]=SESS_ID;
        $parent["goods_id"]=$goods_id;
        $parent["goods_sn"]=$data['data']['goods_sn'];
        $parent["goods_price"]=$data['data']['chengben_jia'];
        $parent["is_stock_goods"]=$data['data']['good_type'];
        $parent["sale_price"]=$data['data']['market_price'];
        $parent["goods_count"]=1;
        $parent["goods_name"]=$data['data']['goods_name'];
        $parent["create_time"]=  date("Y-m-d H:i:s");
        $parent["modify_time"]= date("Y-m-d H:i:s");
        $parent["create_uid"]=$_SESSION['userId'];
        $parent["create_user"]=$_SESSION['userName'];
        $cart_id=$cartModel->add_cart($parent);

        if($cart_id){
            $result['success'] = 1;
        }else{
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }
}

?>