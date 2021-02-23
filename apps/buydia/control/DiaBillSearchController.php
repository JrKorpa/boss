<?php
/**
 *  -------------------------------------------------
 *   @file		: DiaBillController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-03-22 15:40:08
 *   @update	:
 *  -------------------------------------------------
 */
class DiaBillSearchController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('dowTemplate');
    protected $bill_type = array('YSD'=>'用石单','TZD'=>'价格调整单','SLD'=>'石包录入单');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $model_p = new ApiProcessorModel();
        $pro_list = $model_p->GetSupplierList();//调用加工商接口
		$this->render('dia_bill_search_form.html',array('bar'=>Auth::getBar(),'bill_type'=>$this->bill_type,'pro_list'=>$pro_list));
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
            'dia_package' => _Request::get("dia_package"),
            'bill_no' => _Request::get("bill_no"),
            'paper_no' => _Request::get("paper_no"),
            'create_user' => _Request::get("create_user"),
            'create_time_start' => _Request::get("create_time_start"),
            'create_time_end' => _Request::get("create_time_end"),
            'check_user' => _Request::get("check_user"),
            'check_time_start' => _Request::get("check_time_start"),
            'check_time_end' => _Request::get("check_time_end"),
            'status' => _Request::get("status"),
            'bill_type' => _Request::get("bill_type"),
            'processors_id' => _Request::get("processors_id"),
            'factory_id' => _Request::get("factory_id"),
            'remark' => _Request::get("remark")
			//'参数' = _Request::get("参数");


		);
        if($args['dia_package']){
            $dia_package = explode(' ', $args['dia_package']);
        }
        if($args['bill_no']){
            $bill_no = explode(' ', $args['bill_no']);
        }
        if($args['paper_no']){
            $paper_no = explode(' ', $args['paper_no']);
        }
        if($args['create_user']){
            $create_user = explode(' ', $args['create_user']);
        }
        if($args['check_user']){
            $check_user = explode(' ', $args['check_user']);
        }
        $processors_id = '';
        if($args['processors_id'] != ''){
            $proarr = explode('|', $args['processors_id']);
            $processors_id = $proarr[0];
        }
        $factory_id = '';
        if($args['factory_id'] != ''){
            $factoryarr = explode('|', $args['factory_id']);
            $factory_id = $factoryarr[0];
        }

		$page = _Request::getInt("page",1);
		$where = array(
            'dia_package' => $dia_package,
            'bill_no' => $bill_no,
            'paper_no' => $paper_no,
            'create_user' => $create_user,
            'create_time_start' => $args['create_time_start'],
            'create_time_end' => $args['create_time_end'],
            'check_user' => $check_user,
            'check_time_start' => $args['check_time_start'],
            'check_time_end' => $args['check_time_end'],
            'status' => $args['status'],
            'bill_type' => $args['bill_type'],
            'processors_id' => $processors_id,
            'factory_id' => $factory_id,
            'remark' => $args['remark']
            );

		$model = new DiaBillSearchModel(45);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'dia_bill_search_page';
		$this->render('dia_bill_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function StoneBagEnter ()
	{
        $model_p = new ApiProcessorModel();
        $pro_list = $model_p->GetSupplierList();//调用加工商接口
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('dia_bill_info.html',array(
			'view'=>new DiaBillSearchView(new DiaBillSearchModel(45)),
            'pro_list' =>$pro_list,
            'type'=>'SLD'
		));
		$result['title'] = '石包录入';
		Util::jsonExit($result);
	}

    /**
     *  add，渲染添加页面
     */
    public function useStoneBill ()
    {
        $model_p = new ApiProcessorModel();
        $pro_list = $model_p->GetSupplierList();//调用加工商接口
        $result = array('success' => 0,'error' => '');
        $result['content'] = $this->fetch('use_stone_bill.html',array(
            'view'=>new DiaBillSearchView(new DiaBillSearchModel(45)),
            'pro_list' =>$pro_list,
            'type'=>'YSD'
        ));
        $result['title'] = '用石单';
        Util::jsonExit($result);
    }

    /**
     *  价格调整
     */
    public function adjustmentPrice ()
    {
        $model_p = new ApiProcessorModel();
        $pro_list = $model_p->GetSupplierList();//调用加工商接口
        $result = array('success' => 0,'error' => '');
        $result['content'] = $this->fetch('adjustment_price.html',array(
            'view'=>new DiaBillSearchView(new DiaBillSearchModel(45)),
            'pro_list' =>$pro_list,
            'type'=>'TZD'
        ));
        $result['title'] = '价格调整';
        Util::jsonExit($result);
    }

    /**
     *  价格调整单
     */
    public function insertadjustmentPrice ()
    {
        $result = array('success' => 0,'error' =>'');
        $create_user = _Request::get("create_user");
        $create_time = _Request::get("create_time");
        $remark = _Request::get("remark");

        if($_FILES['file']['name'] == '')
        {
            $result['error'] = "请上传石包明细";
            Util::jsonExit($result);
        }
        if(empty($_FILES['file']['tmp_name']))
        {
            $result['error'] = "上传文件不能为空";
            Util::jsonExit($result);
        }
        $file_array = explode(".",$_FILES['file']['name']);
        $file_extension = strtolower(array_pop($file_array));
        if($file_extension != 'csv'){
            $result['error'] = "请上传CSV格式的文件";
            Util::jsonExit($result);
        }

        $newmodel =  new DiaBillSearchModel(46);
        $f = fopen($_FILES['file']['tmp_name'],"r");
        $i = 0;
        while(! feof($f)){
            $con = fgetcsv($f);
            if ($i > 0){
                if (trim($con[0]) == '' && trim($con[1]) == '' ){
                    if($i == 1){
                        $result['error'] = "上传文件数据不能为空";
                        Util::jsonExit($result);
                    }
                }else{
                    $dia_package = strtoupper(trim($con[0])); 
                    $purchase_price = strtoupper(trim($con[1])); 
                    
                    if(empty($dia_package) || empty($purchase_price)){
                        $result['error'] = "石包号和价格不能为空！";
                        Util::jsonExit($result);
                    }
                    //根据石包号查出信息；
                    $stoneInfo = $newmodel->check_dia_package($dia_package);
                    if(empty($stoneInfo)){
                        $result['error'] = "石包号无效或不存在".$dia_package;
                        Util::jsonExit($result);
                    }
                    if($purchase_price <=0){
                        $result['error'] = "石包号调整后价格必须大于0".$dia_package;
                        Util::jsonExit($result);
                    }
                    $processors_id = $stoneInfo['sup_id'];
                    $processors_name = $stoneInfo['sup_name'];
                    $val['dia_package'] = $dia_package;
                    $val['purchase_price'] = $purchase_price;
                    $val['specification'] = $stoneInfo['specification'];
                    $val['color'] = $stoneInfo['color'];
                    $val['neatness'] = $stoneInfo['neatness'];
                    $val['cut'] = $stoneInfo['cut'];
                    $val['symmetry'] = $stoneInfo['symmetry'];
                    $val['polishing'] = $stoneInfo['polishing'];
                    $val['fluorescence'] = $stoneInfo['fluorescence']; 
                    $data[] = $val;
                }
            }
            $i++;
        }
        $bill_info = array(
            'bill_type'=>'TZD',
            'status'=>1,
            'num'=>count($data),
            'processors_id'=>$processors_id,
            'processors_name' => $processors_name,
            'create_user'=>$_SESSION['userName'],
            'create_time'=>$create_time,
            'price_total'=>0,
            'remark'=>$remark//调整原因
            );
        $res = $newmodel->saveBillTZDData($bill_info,$data);
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
	 *	edit，渲染修改页面 单据详情
	 */
	public function edit ($params)
	{
        $result = array('success' => 0,'error' => '');
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
        $newmodel =  new DiaBillSearchModel($id,46);
        $olddo = $newmodel->getDataObject();
        $model_p = new ApiProcessorModel();
        $pro_list = $model_p->GetSupplierList();//调用加工商接口
        if($olddo['bill_type']  == 'YSD' && $olddo['source']  == 1){
            exit('收货单生成的用石单不可编辑');
        }
        if($olddo['status'] != 1){
            exit('只有保存状态的可以编辑');
        }
        if($olddo['bill_type'] == 'YSD'){
            $result['content'] = $this->fetch('use_stone_bill.html',array(
                'view'=>new DiaBillSearchView($newmodel),
                'tab_id'=>$tab_id,
                'type'=>$olddo['bill_type'],
                'pro_list'=>$pro_list
            ));
            $result['title'] = '编辑';
            Util::jsonExit($result);
        }
		$result['content'] = $this->fetch('dia_bill_info.html',array(
			'view'=>new DiaBillSearchView($newmodel),
			'tab_id'=>$tab_id,
            'type'=>$olddo['bill_type'],
            'pro_list'=>$pro_list
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
        $newmodel =  new DiaBillSearchModel($id,46);
        $olddo = $newmodel->getDataObject();
		$this->render('dia_bill_show.html',array(
			'view'=>new DiaBillSearchView(new DiaBillSearchModel($id,45)),
			'bar'=>Auth::getViewBar(),
            'billInfo'=>$olddo,
            'bill_type'=>$this->bill_type
		));
	}

	/**
	 *	insert，信息入库  石包录入单新增
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
        $processors_id = _Request::get("processors_id");
        $create_user = _Request::get("create_user");
        $create_time = _Request::get("create_time");
        $remark = _Request::get("remark");

        if($processors_id == ''){
            $result['error'] = "供应商必填";
            Util::jsonExit($result);
        }

        if($_FILES['file']['name'] == '')
        {
            $result['error'] = "请上传石包明细";
            Util::jsonExit($result);
        }
        if(empty($_FILES['file']['tmp_name']))
        {
            $result['error'] = "上传文件不能为空";
            Util::jsonExit($result);
        }
        $file_array = explode(".",$_FILES['file']['name']);
        $file_extension = strtolower(array_pop($file_array));
        if($file_extension != 'csv'){
            $result['error'] = "请上传CSV格式的文件";
            Util::jsonExit($result);
        }

        $newmodel =  new DiaBillSearchModel(46);
        $f = fopen($_FILES['file']['tmp_name'],"r");
        $i = 0;
        while(! feof($f)){
            $con = fgetcsv($f);
            if ($i > 0){
                if (trim($con[0]) == '' && trim($con[1]) == '' ){
                    if($i == 1){
                        $result['error'] = "上传文件数据不能为空";
                        Util::jsonExit($result);
                    }
                }else{
                    $dia_package = strtoupper(trim($con[0])); 
                    $purchase_price = strtoupper(trim($con[1])); 
                    $specification = strtoupper(trim($con[2])); 
                    $color = strtoupper(trim($con[3])); 
                    $neatness = strtoupper(trim($con[4])); 
                    $cut = strtoupper(trim($con[5])); 
                    $symmetry = strtoupper(trim($con[6])); 
                    $polishing = strtoupper(trim($con[7])); 
                    $fluorescence = strtoupper(trim($con[8])); 
                    if(empty($dia_package) || empty($purchase_price)){
                        $result['error'] = "石包号和价格不能为空！";
                        Util::jsonExit($result);
                    }
                    //确认是否存在有效石包号
                    $checkDia = $newmodel->check_dia_package($dia_package);
                    if(!empty($checkDia)){
                        $result['error'] = "存在有效石包号".$dia_package;
                        Util::jsonExit($result);
                    }
                    $val['dia_package'] = $dia_package;
                    $val['purchase_price'] = $purchase_price;
                    $val['specification'] = $specification;
                    $val['color'] = $color;
                    $val['neatness'] = $neatness;
                    $val['cut'] = $cut;
                    $val['symmetry'] = $symmetry;
                    $val['polishing'] = $polishing;
                    $val['fluorescence'] = $fluorescence;
                    $data[] = $val;
                }
            }
            $i++;
        }
        $processors_arr = explode('|', $processors_id);
        $processors_idt = $processors_arr[0];
        $processors_name = $processors_arr[1];
        $bill_info = array(
            'bill_type'=>'SLD',
            'status'=>1,
            'processors_id'=>$processors_idt,
            'processors_name'=>$processors_name,
            'create_user'=>$_SESSION['userName'],
            'create_time'=>$create_time,
            'source'=>2,
            'num'=>count($data),
            'weight'=>0,
            'price_total'=>0,
            'remark'=>$remark
            );
		$res = $newmodel->saveBillData($bill_info,$data);
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
     *  insertStoneBill 用石单新增
     */
    public function insertStoneBill ($params)
    {
        $result = array('success' => 0,'error' =>'');
        $processors_id = _Request::get("processors_id");
        $factory_id = _Request::get("factory_id");
        $create_user = _Request::get("create_user");
        $create_time = _Request::get("create_time");
        $paper_no = _Request::get("paper_no");
        $remark = _Request::get("remark");
        if($factory_id == ''){
            $result['error'] = "工厂必填";
            Util::jsonExit($result);
        }
        if($processors_id == ''){
            $result['error'] = "供应商必填";
            Util::jsonExit($result);
        }
        if($_FILES['file']['name'] == '')
        {
            $result['error'] = "请上传石包明细";
            Util::jsonExit($result);
        }
        if(empty($_FILES['file']['tmp_name']))
        {
            $result['error'] = "上传文件不能为空";
            Util::jsonExit($result);
        }
        $file_array = explode(".",$_FILES['file']['name']);
        $file_extension = strtolower(array_pop($file_array));
        if($file_extension != 'csv'){
            $result['error'] = "请上传CSV格式的文件";
            Util::jsonExit($result);
        }

        $newmodel =  new DiaBillSearchModel(46);
        $price_total = 0;
        $f = fopen($_FILES['file']['tmp_name'],"r");
        $i = 0;
        while(! feof($f)){
            $con = fgetcsv($f);
            if ($i > 0){
                if (trim($con[0]) == '' && trim($con[1]) == '' ){
                    if($i == 1){
                        $result['error'] = "上传文件数据不能为空";
                        Util::jsonExit($result);
                    }
                }else{
                    $dia_package = strtoupper(trim($con[0])); 
                    $num = strtoupper(trim($con[1]));
                    $weight = strtoupper(trim($con[2]));
                    if(empty($dia_package) || empty($num) || empty($weight)){
                        $result['error'] = "石包、总数量、总粒数不能为空！";
                        Util::jsonExit($result);
                    }

                    //确认石包号是否已经存在
                    $stoneInfo = $newmodel->check_dia_package($dia_package);
                    if(empty($stoneInfo)){
                        $result['error'] = "石包号无效或不存在".$dia_package;
                        Util::jsonExit($result);
                    }
                    $val['dia_package'] = $dia_package;
                    $val['num'] = $num;
                    $val['weight'] = $weight;
                    $val['purchase_price'] = $stoneInfo['purchase_price'];
                    $val['price'] = bcmul($stoneInfo['purchase_price'], $weight, 2);
                    $val['specification'] = $stoneInfo['specification'];
                    $val['color'] = $stoneInfo['color'];
                    $val['neatness'] = $stoneInfo['neatness'];
                    $val['cut'] = $stoneInfo['cut'];
                    $val['symmetry'] = $stoneInfo['symmetry'];
                    $val['polishing'] = $stoneInfo['polishing'];
                    $val['fluorescence'] = $stoneInfo['fluorescence']; 
                    $data[] = $val;
                    $price_total+=$val['price'];
                    $num_total+=$num;
                    $weight_total+=$weight;
                }
            }
            $i++;
        }

        $processors_idt = '';
        $processors_name = '';
        if(!empty($processors_id)){
            $processors_arr = explode('|', $processors_id);
            $processors_idt = $processors_arr[0];
            $processors_name = $processors_arr[1];
        }

        $factory_idt = '';
        $factory_name = '';
        if(!empty($factory_id)){
            $factory_arr = explode('|', $factory_id);
            $factory_idt = $factory_arr[0];
            $factory_name = $factory_arr[1];
        }

        $bill_info = array(
            'bill_type'=>'YSD',
            'status'=>1,
            'paper_no'=>$paper_no,
            'processors_id'=>$processors_idt,
            'processors_name'=>$processors_name,
            'factory_id' => $factory_idt,
            'factory_name' => $factory_name,
            'create_user'=>$_SESSION['userName'],
            'create_time'=>$create_time,
            'source'=>2,
            'price_total'=>$price_total,
            'remark'=>$remark,
            'weight'=>$weight_total,
            'num'=>$num_total
            );
        $res = $newmodel->saveBillYSDData($bill_info,$data);
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
        $_cls = _Post::getInt('_cls');
        $tab_id = _Post::getInt('tab_id');
        $processors_id = _Request::get("processors_id");
        $create_user = _Request::get("create_user");
        $create_time = _Request::get("create_time");
        $remark = _Request::get("remark");
        $type = _Request::get("type");
        if($processors_id == ''){
            $result['error'] = "供应商必填";
            Util::jsonExit($result);
        }
        if($_FILES['file']['name'] == '')
        {
            $result['error'] = "请上传石包明细";
            Util::jsonExit($result);
        }
        if(empty($_FILES['file']['tmp_name']))
        {
            $result['error'] = "上传文件不能为空";
            Util::jsonExit($result);
        }
        $file_array = explode(".",$_FILES['file']['name']);
        $file_extension = strtolower(array_pop($file_array));
        if($file_extension != 'csv'){
            $result['error'] = "请上传CSV格式的文件";
            Util::jsonExit($result);
        }

        $id = _Post::getInt('id');
        $newmodel =  new DiaBillSearchModel($id,46);
        $olddo = $newmodel->getDataObject();
        $f = fopen($_FILES['file']['tmp_name'],"r");
        $i = 0;
        while(! feof($f)){
            $con = fgetcsv($f);
            if ($i > 0){
                if (trim($con[0]) == '' && trim($con[1]) == '' ){
                    if($i == 1){
                        $result['error'] = "上传文件数据不能为空";
                        Util::jsonExit($result);
                    }
                }else{
                    if($type == 'SLD'){
                        $dia_package = strtoupper(trim($con[0])); 
                        $purchase_price = strtoupper(trim($con[1])); 
                        $specification = strtoupper(trim($con[2])); 
                        $color = strtoupper(trim($con[3])); 
                        $neatness = strtoupper(trim($con[4])); 
                        $cut = strtoupper(trim($con[5])); 
                        $symmetry = strtoupper(trim($con[6])); 
                        $polishing = strtoupper(trim($con[7])); 
                        $fluorescence = strtoupper(trim($con[8])); 
                        if(empty($dia_package) || empty($purchase_price)){
                            $result['error'] = "石包号和价格数据不能为空！";
                            Util::jsonExit($result);
                        }
                        //确认是否存在有效石包号；
                        $checkDia = $newmodel->check_dia_package($dia_package);
                        if(!empty($checkDia)){
                            $result['error'] = "存在有效石包号".$dia_package;
                            Util::jsonExit($result);
                        }
                        $val['dia_package'] = $dia_package;
                        $val['purchase_price'] = $purchase_price;
                        $val['specification'] = $specification;
                        $val['color'] = $color;
                        $val['neatness'] = $neatness;
                        $val['cut'] = $cut;
                        $val['symmetry'] = $symmetry;
                        $val['polishing'] = $polishing;
                        $val['fluorescence'] = $fluorescence;
                        $val['num'] = 1;
                        $val['weight'] = 0;
                        $data[] = $val;
                    }else if($type == 'TZD'){
                        $dia_package = strtoupper(trim($con[0])); 
                        $purchase_price = strtoupper(trim($con[1]));
                        if(empty($dia_package) || empty($purchase_price)){
                            $result['error'] = "石包号和价格不能为空！";
                            Util::jsonExit($result);
                        }
                        //根据石包号查出信息；
                        $stoneInfo = $newmodel->check_dia_package($dia_package);
                        if(empty($stoneInfo)){
                            $result['error'] = "石包号无效或不存在".$dia_package;
                            Util::jsonExit($result);
                        }
                        $val['dia_package'] = $dia_package;
                        $val['purchase_price'] = $purchase_price;
                        $val['specification'] = $stoneInfo['specification'];
                        $val['color'] = $stoneInfo['color'];
                        $val['neatness'] = $stoneInfo['neatness'];
                        $val['cut'] = $stoneInfo['cut'];
                        $val['symmetry'] = $stoneInfo['symmetry'];
                        $val['polishing'] = $stoneInfo['polishing'];
                        $val['fluorescence'] = $stoneInfo['fluorescence'];
                        $val['num'] = 1;
                        $val['weight'] = 0; 
                        $data[] = $val;
                    }
                    
                }
            }
            $i++;
        }
        $processors_arr = explode('|', $processors_id);
        $processors_idt = $processors_arr[0];
        $processors_name = $processors_arr[1];
        $bill_info = array(
            'id' => $id,
            'bill_type'=>$type,
            'status'=>1,
            'processors_id'=>$processors_idt,
            'processors_name'=>$processors_name,
            'create_user'=>$_SESSION['userName'],
            'create_time'=>$create_time,
            'source'=>2,
            'num'=>count($data),
            'weight'=>0,
            'price_total'=>0,
            'remark'=>$remark
            );

        $res = $newmodel->updateBillData($bill_info,$data);
        if($res !== false)
        {
            $result['success'] = 1;
            $result['_cls'] = $_cls;
            $result['tab_id'] = $tab_id;    
            $result['title'] = '修改此处为想显示在页签上的字段';
        }
        else
        {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
	}


    /**
     *  更新用石单
     */
    public function updateYsd ($params)
    {
        $result = array('success' => 0,'error' =>'');
        $_cls = _Post::getInt('_cls');
        $tab_id = _Post::getInt('tab_id');
        $processors_id = _Request::get("processors_id");
        $factory_id = _Request::get("factory_id");
        $create_user = _Request::get("create_user");
        $create_time = _Request::get("create_time");
        $paper_no = _Request::get("paper_no");
        $remark = _Request::get("remark");
        if($factory_id == ''){
            $result['error'] = "工厂必填";
            Util::jsonExit($result);
        }
        if($processors_id == ''){
            $result['error'] = "供应商必填";
            Util::jsonExit($result);
        }
        if($_FILES['file']['name'] == '')
        {
            $result['error'] = "请上传石包明细";
            Util::jsonExit($result);
        }
        if(empty($_FILES['file']['tmp_name']))
        {
            $result['error'] = "上传文件不能为空";
            Util::jsonExit($result);
        }
        $file_array = explode(".",$_FILES['file']['name']);
        $file_extension = strtolower(array_pop($file_array));
        if($file_extension != 'csv'){
            $result['error'] = "请上传CSV格式的文件";
            Util::jsonExit($result);
        }

        $id = _Post::getInt('id');
        $newmodel =  new DiaBillSearchModel($id,46);
        $olddo = $newmodel->getDataObject();
        $price_total = 0;
        $num_total = 0;
        $weight_total = 0;
        $f = fopen($_FILES['file']['tmp_name'],"r");
        $i = 0;
        while(! feof($f)){
            $con = fgetcsv($f);
            if ($i > 0){
                if (trim($con[0]) == '' && trim($con[1]) == '' ){
                    if($i == 1){
                        $result['error'] = "上传文件数据不能为空";
                        Util::jsonExit($result);
                    }
                }else{
                    $dia_package = strtoupper(trim($con[0])); 
                    $num = strtoupper(trim($con[1]));
                    $weight = strtoupper(trim($con[2]));
                    if(empty($dia_package) || empty($num) || empty($weight)){
                        $result['error'] = "石包、总数量、总粒数不能为空！";
                        Util::jsonExit($result);
                    }

                    //确认石包号是否已经存在
                    $stoneInfo = $newmodel->check_dia_package($dia_package);
                    if(empty($stoneInfo)){
                        $result['error'] = "石包号无效或不存在".$dia_package;
                        Util::jsonExit($result);
                    }
                    $val['dia_package'] = $dia_package;
                    $val['num'] = $num;
                    $val['weight'] = $weight;
                    $val['purchase_price'] = $stoneInfo['purchase_price'];
                    $val['price'] = bcmul($stoneInfo['purchase_price'], $weight, 2);
                    $val['specification'] = $stoneInfo['specification'];
                    $val['color'] = $stoneInfo['color'];
                    $val['neatness'] = $stoneInfo['neatness'];
                    $val['cut'] = $stoneInfo['cut'];
                    $val['symmetry'] = $stoneInfo['symmetry'];
                    $val['polishing'] = $stoneInfo['polishing'];
                    $val['fluorescence'] = $stoneInfo['fluorescence']; 
                    $data[] = $val;
                    $price_total+=$val['price'];
                    $num_total+=$num;
                    $weight_total+=$weight;
                }
            }
            $i++;
        }

        $processors_idt = '';
        $processors_name = '';
        if(!empty($processors_id)){
            $processors_arr = explode('|', $processors_id);
            $processors_idt = $processors_arr[0];
            $processors_name = $processors_arr[1];
        }

        $factory_idt = '';
        $factory_name = '';
        if(!empty($factory_id)){
            $factory_arr = explode('|', $factory_id);
            $factory_idt = $factory_arr[0];
            $factory_name = $factory_arr[1];
        }

        $bill_info = array(
            'id'=>$id,
            'bill_type'=>'YSD',
            'status'=>1,
            'paper_no'=>$paper_no,
            'processors_id'=>$processors_idt,
            'processors_name'=>$processors_name,
            'factory_id' => $factory_idt,
            'factory_name' => $factory_name,
            'create_user'=>$_SESSION['userName'],
            'create_time'=>$create_time,
            'source'=>2,
            'price_total'=>$price_total,
            'remark'=>$remark,
            'weight'=>$weight_total,
            'num'=>$num_total
            );
        $res = $newmodel->updateBillYsdData($bill_info,$data);
        if($res !== false)
        {
            $result['success'] = 1;
            $result['_cls'] = $_cls;
            $result['tab_id'] = $tab_id;    
            $result['title'] = '修改此处为想显示在页签上的字段';
        }
        else
        {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

    /**
     *  单据审核
     */
    public function check ($params)
    {
        $result = array('success' => 0,'error' =>'');
        $_cls = _Post::getInt('_cls');
        $tab_id = _Post::getInt('tab_id');

        $id = _Post::getInt('id');
        $newmodel =  new DiaBillSearchModel($id,46);

        $olddo = $newmodel->getDataObject();
        if($olddo['status'] != 1){
            $result['error'] = '只有保存状态的才可以审核';
            Util::jsonExit($result);
        }
        $res = $newmodel->checkBillStatus($id,$olddo);
        if($res !== false)
        {
            $result['success'] = 1;
            $result['_cls'] = $_cls;
            $result['tab_id'] = $tab_id;
            $result['title'] = '修改此处为想显示在页签上的字段';
        }
        else
        {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);
    }

    /**
     *  单据驳回
     */
    public function reject($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new DiaBillSearchModel($id,46);
        $do = $model->getDataObject();
        if($do['status'] != 1){
            $result['error'] = '只有保存状态的才可以驳回';
            Util::jsonExit($result);
        }
        $model->setValue('status',3);
        $res = $model->save(true);
        if($res !== false){
            $result['success'] = 1;
        }else{
            $result['error'] = "驳回失败";
        }
        Util::jsonExit($result);
        /*$result = array('success' => 0,'error' =>'');
        $_cls = _Post::getInt('_cls');
        $tab_id = _Post::getInt('tab_id');

        $id = _Post::getInt('id');
        $newmodel =  new DiaBillSearchModel($id,46);

        $olddo = $newmodel->getDataObject();
        if($olddo['status'] != 2){
            $result['error'] = '只有审核状态的才可以驳回';
            Util::jsonExit($result);
        }
        $res = $newmodel->rejectBillStatus($id,$olddo);
        if($res !== false)
        {
            $result['success'] = 1;
            $result['_cls'] = $_cls;
            $result['tab_id'] = $tab_id;
            $result['title'] = '修改此处为想显示在页签上的字段';
        }
        else
        {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);*/
    }

    /**
     *  取消
     */
    public function countermand ($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new DiaBillSearchModel($id,46);
        $do = $model->getDataObject();
        if($do['status'] != 1){
            $result['error'] = '只有保存状态的才可以取消';
            Util::jsonExit($result);
        }
        $model->setValue('status',3);
        $model->setValue('check_user',$_SESSION['userName']);
        $model->setValue('check_time',date('Y-m-d H:i:s'));
        $res = $model->save(true);
        if($res !== false){
            $result['success'] = 1;
        }else{
            $result['error'] = "取消失败";
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
		$model = new DiaBillSearchModel($id,46);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}


    /*******************************************************************************************************
    fun:getTemplate array('YSD'=>'用石单','TZD'=>'价格调整单','SLD'=>'石包录入单');
    decription:下载模板
    ********************************************************************************************************/
    public function dowTemplate($params)
    {
        $type= $params['type'];
        header("Content-Disposition: attachment;filename=".$type.date("Ymd").".csv");
        if($type == 'SLD'){
            $str = "石包,每卡采购价格(元),规格,颜色,净度,切工,对称,抛光,荧光\n";
        }else if($type== 'TZD'){
            $str = "石包,每卡采购价格(元)\n";
        }else if($type == 'YSD'){
            $str = "石包,总数量/粒,总重量/ct,每卡采购价格(元),金额,规格,颜色,净度,切工,对称,抛光,荧光\n";
        }
        //取得明细信息
        if (isset($params['id']))
        {
            $id   = $params['id'];
            $model = new DiaBillSearchModel(45);
            $res = $model->getDetailByOrderId($id);
            if($type == 'SLD'){
                $str = "石包,每卡采购价格(元),规格,颜色,净度,切工,对称,抛光,荧光\n";
            }else if($type== 'TZD'){
                $str = "石包,每卡采购价格(元)\n";
            }else if($type == 'YSD'){
                $str = "石包,总数量/粒,总重量/ct,每卡采购价格(元),金额,规格,颜色,净度,切工,对称,抛光,荧光\n";
            }
            if ($res)
            { 
                foreach ($res as $val)
                {
                    if($type == 'SLD'){
                         $str .= '"'.$val['dia_package'].'",'.
                            '"'.$val['purchase_price'].'",'.
                            '"'.$val['specification'].'",'.
                            '"'.$val['color'].'",'.
                            '"'.$val['neatness'].'",'.
                            '"'.$val['cut'].'",'.
                            '"'.$val['symmetry'].'",'.
                            '"'.$val['polishing'].'",'.
                            '"'.$val['fluorescence'].'" '."\n";
                        }else if($type == 'TZD'){
                            $str .= '"'.$val['dia_package'].'",'.
                            '"'.$val['purchase_price'].'" '."\n";
                        }else if($type == 'YSD'){
                            $str .= '"'.$val['dia_package'].'",'.
                            '"'.$val['num'].'",'.
                            '"'.$val['weight'].'",'.
                            '"'.$val['purchase_price'].'",'.
                            '"'.$val['specification'].'",'.
                            '"'.$val['color'].'",'.
                            '"'.$val['neatness'].'",'.
                            '"'.$val['cut'].'",'.
                            '"'.$val['symmetry'].'",'.
                            '"'.$val['polishing'].'",'.
                            '"'.$val['fluorescence'].'" '."\n";
                        }
                }

            }
        }
        echo iconv("utf-8","gbk", $str);
    }
}

?>