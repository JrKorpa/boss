function addgoodsforbill(obj){
	var url = $(obj).attr('data-url');
	var listid = $(obj).attr('list-id');
	var params = util.parseUrl(url);
	var prefix = params['con'].toLowerCase()+"_xx";
	var title = $(obj).attr('data-title');
	if (!title)
	{
		title=params['con'];
	}
	//不能同时打开两个添加页
	var flag = false;
	$('#nva-tab li').each(function(){
		var href = $(this).children('a').attr('href');
		href = href.split('-');
		href.pop();
		href = href.join('_').substr(1);
		if (href==prefix)
		{
			flag=true;
			var that = this;
			bootbox.confirm({  
				buttons: {  
					confirm: {  
						label: '前往查看' 
					},  
					cancel: {  
						label: '点错了'  
					}  
				},
				closeButton: false,
				message: "发现同类数据的页签已经打开。",  
				callback: function(result) {  
					if (result == true) {
						$(that).children('a').trigger("click");
					}
				},  
				title: "提示信息", 
			});
			return false;
		}
	});
	if (!flag)
	{
		var id = prefix+"-0";
		new_tab(id,'添加:'+title,url+'&tab_id='+listid);
	}
}

// 取消单据
function closeBillD(obj) {
	$('body').modalmanager('loading');
	var url = $(obj).attr('data-url');
	var id = '<%$view->get_id()%>';
	var bill_no = '<%$view->get_bill_no()%>';

	bootbox.confirm('确定取消吗', function(result) {
		if (result == true) {
			$.post(url, {id : id,bill_no : bill_no}, function(data) {
				$('.modal-scrollable').trigger('click');
				if (data.success == 1) {
					bootbox.alert({
						message : data.error,
						buttons : {
							ok : {
								label : '确定'
							}
						},
						animate : true,
						closeButton : false,
						title : "提示信息",
					});
					util.retrieveReload();
				} else {
					bootbox.alert({
						message : data.error,
						buttons : {
							ok : {
								label : '确定'
							}
						},
						animate : true,
						closeButton : false,
						title : "提示信息",
					});
				}
			});
		}
	});
}

$import(
	["public/js/select2/select2.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",	//明细table插件
	],function() {
	var info_form_id = 'warehouse_bill_info_d';//form表单id
	var info_form_base_url = 'index.php?mod=warehouse&con=WarehouseBillInfoD&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	var obj = function(){
		var initElements = function(){
			//下拉美化 需要引入"public/js/select2/select2.min.js"
			$('#'+info_form_id+' select').select2({
				placeholder: "请选择",
				allowClear: true,
//				minimumInputLength: 2
			}).change(function(e){
				$(this).valid();
			});
		};

		var from_table1 = function(){
			$.ajax({
				url : "index.php?mod=warehouse&con=WarehouseBillInfoD&act=mkJson",
				dataType : "json",
				type : "POST",
				data : {
					'id' : info_id
				},
				success : function(res){
					from_table_data_d(res.id,res.data_bill_d, res.title,res.columns);
				}
			});
			// 保存值
			$("body").find("#from_table_data_info_d_btn").click(function() {
				//获取表单数据

				if ($("#from_table_data_bill_d").find("td").hasClass("htInvalid") == true) {
					$("#from_table_data_bill_d").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
					return false;
				} else {
					var save = {
						'data' : $("#from_table_data_bill_d").handsontable('getData'),
						'order_sn' : $("#warehouse_bill_info_d #order_sn").val(),
						'bill_note' : $("#warehouse_bill_info_d #bill_note").val(),
						'id' : info_id,
						'to_warehouse_id':$("#warehouse_bill_info_d #to_warehouse_id").val()
					};
					$('body').modalmanager('loading');//进度条和遮罩
					$.ajax({
						url : info_id ? "index.php?mod=warehouse&con=WarehouseBillInfoD&act=update" : "index.php?mod=warehouse&con=WarehouseBillInfoD&act=insert",
						data : save,
						dataType : "json",
						type : "POST",
						success : function(res) {
							if (res.success == "1") {
								bootbox.alert({
									message : info_id ? '修改销售退货单成功' : res.error,
									buttons : {
										ok : {
											label : '确定'
										}
									},
									animate : true,
									closeButton : false,
									title : "提示信息",
								});
								if (info_id) {
									$('.modal-scrollable').trigger('click');// 关闭遮罩
									util.retrieveReload();
								} else {
									var jump_url = 'index.php?mod=warehouse&con=WarehouseBillInfoD&act=edit';
									util.closeTab(res.x_id);
									util.buildEditTab(res.x_id,jump_url,res.tab_id,res.label);
								}
							} else {
								// alert(res.error);
								bootbox.alert({
									message : res.error,
									buttons : {
										ok : {
											label : '确定'
										}
									},
									animate : true,
									closeButton : false,
									title : "提示信息",
								});

							}
						}
					});
				}
			});
		};


		var initData = function(){
			$('#'+info_form_id+' :reset').on('click',function(){

			});
		};
		return {
			init:function(){
				initElements();//处理表单元素
				initData();//处理表单重置和其他特殊情况
				from_table1();
			}
		}
	}();
	obj.init();
});