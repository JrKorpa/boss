<?php
/**
 * Created by PhpStorm.
 * User: liaoweixian
 * Date: 2018/6/11
 * Time: 15:00
 */

class PointCodeController extends CommonController
{
    public function index($params){
        $channellist = $this->accessChannels();
        $def_id = isset(current($channellist)['id']) ? current($channellist)['id'] : 0;
        $this->render('point_code_search_form.html',array('bar'=>Auth::getBar(),'sales_channels_idData' => $channellist,'def_id'=>$def_id));
    }

    public function search(){
        $args = array(
            'mod'	=> _Request::get("mod"),
            'con'	=> substr(__CLASS__, 0, -10),
            'act'	=> __FUNCTION__,
            'channel_code'=>_Request::get("channel_code"),
            'status'=> _Request::get("status"),
            'point_code'=> _Request::get("point_code"),
            'use_proportion'=> _Request::get("use_proportion"),
            'order_sn'=> _Request::get("order_sn"),
            'use_people_name'=> _Request::get("use_people_name"),
            'created_name'=> _Request::get("created_name"),           
        );
        if($_SESSION['userType'] <> 1){
            $args['channel_limit'] = array_column($this->accessChannels(),'id');
        }else
            $args['channel_limit'] = array();
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
        $where = array();
        $where['channel_id'] = $args['channel_code'];
        $where['status'] = $args['status'];
        $where['point_code'] = $args['point_code'];
        $where['use_proportion'] = $args['use_proportion'];
        $where['order_sn'] = $args['order_sn'];
        $where['use_people_name'] = $args['use_people_name'];
        $where['created_name'] = $args['created_name'];
        $where['channel_limit'] =$args['channel_limit'];

        $model = new PointCodeModel(2);
        $data = $model->pageList($where,$page,10,false);
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'point_code_search_page';
        $this->render('point_code_search_list.html',array(
            'pa'=>Util::page($pageData),
            'page_list'=>$data
        ));
    }

    public function insert(){
        $result = array('success' => 0, 'error' => '');
        $type = _Post::get('type');
        $channel_id = _Post::get('channels_id');
        if(empty($channel_id)){
            $result['error'] = "请选择渠道！";
            Util::jsonExit($result);
        }
        if(empty($type)){
            $result['error'] = "系统错误";
            Util::jsonExit($result);
        }
        $salesChannelsModel = new SalesChannelsModel(1);
        $channelInfo = $salesChannelsModel->getQuDaoInfo('*',['id'=>$channel_id]);
        if(empty($channelInfo)){
            $result['error'] = "渠道不存在或您没有权限操作该渠道！";
            Util::jsonExit($result);
        }
        $point_code = $this->returnCode();
        $params = array(
            'channel_id'=>$channelInfo[0]['id'],
            'channel_name'=>$channelInfo[0]['channel_name'],
            'point_code'=>$point_code,
            'use_proportion' => $type == 1 ? 20 : 50,
            'status'=> 0,            
            'created_name'=>$_SESSION['userName'],
            'create_time'=>date('Y-m-d H:i:s')
        );
        $newModel = new PointCodeModel(2);
        $olddo = array();
        $res = $newModel->saveData($params,$olddo);
        if ($res !== false) {
            $result['success'] = 1;
            $result['code'] = $point_code;
        } else {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

    public function update(){
       return false;
    }

    public function del(){
        return false;
    }

    public function add(){
       $result = array('success' => 0,'error' => '');
       $channellist = $this->accessChannels();
        $result['content'] = $this->fetch('point_code_info.html',array(
            'view'=>new PointCodeView(new PointCodeModel(2)),
            'channels'=>$channellist,
            'type'=>_Get::get('type')
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);


    }

    // 抽离渠道访问权限
    private function accessChannels() {
        if($_SESSION['userType'] == 1){
            // 超级用户查看所有线下体验店
            $salesChannelsModel = new SalesChannelsModel(1);
            $channellist = $salesChannelsModel->getSalesChannelsInfo('id,channel_name',array('channel_class'=>2,'channel_type'=>2));
            $channellist[] = array('id'=>163, 'channel_name'=>'总公司网销');
        }else{
            //当前用户所处渠道
            $saleChannelmodel = new UserChannelModel(1);
            $list = $saleChannelmodel->getChannels($_SESSION['userId'],0);
            // 所有销售渠道
            $channellist = array();
            foreach ($list as $item) {               
                    $channellist[] = $item;                
            }
        }
        return $channellist;
    }

    private function returnCode(){
        $coding = '';
        for ($i=0;$i<5;$i++){
            $coding .= chr(rand(65,90));
        }
        $model = new PointCodeModel(2);
        $count = $model->getCount(['point_code'=>$coding]);
        if($count > 0){
            $this->returnCode();
        }
        return $coding;
    }

}