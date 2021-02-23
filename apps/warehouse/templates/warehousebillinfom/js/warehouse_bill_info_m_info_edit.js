function closeBillM(obj) {
	$('body').modalmanager('loading');
	var url = $(obj).attr('data-url');
	var id = '<%$view->get_id()%>';
	var bill_no = '<%$view->get_bill_no()%>';

	//出库公司
	var company = '<%$view->get_from_company_name()%>';

	$.get('index.php?mod=warehouse&con=WarehouseBillInfoM&act=checkbing&bill_no='+bill_no,'',function(res){
		var massage = '确定取消单据吗?';
		if(company == '总公司'){
			if(res == 1){
				massage = "当前调拨单已经绑定了包裹单，确定取消单据吗?";
			}
		}

		bootbox.confirm(massage, function(result) {
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
	});

}
function print_info(obj) {
    var url =$(obj).attr('data-url') ;
    var id = '<%$view->get_id()%>';
    //js请求方法
    url = url+'&id='+id;
    window.location.href=url;
    
    
}
function printHunbohui(obj){
    var url =$(obj).attr('data-url');
    var id = '<%$view->get_id()%>';
     //js请求方法
    url = url+'&id='+id;
    window.open(url);      
    //window.location.href=url;
}
//打印条码
function printcode(){
	var down_info = 'down_info';
    var bill_id = $("#bill_id").val();
    var args = "&down_info="+down_info+"&bill_id="+bill_id;
    location.href = "index.php?mod=warehouse&con=WarehouseBill&act=printcode"+args;
}

//核对货品
function hedui_goods(obj){
    var url = $(obj).attr('data-url');
    var bill_no = '<%$view->get_bill_no()%>';
    var bill_id = '<%$view->get_id()%>';
    util._pop(url+'&bill_no='+bill_no+'&bill_id='+bill_id);
}

function closeBillY(obj) {
	$('body').modalmanager('loading');
	var url = $(obj).attr('data-url');
	var id = '<%$view->get_id()%>';
	var bill_no = '<%$view->get_bill_no()%>';

	//出库公司
	var company = '<%$view->get_from_company_name()%>';

	$.get('index.php?mod=warehouse&con=WarehouseBillInfoM&act=checkbing&bill_no='+bill_no,'',function(res){
		var massage = '确定取消单据吗?';
		if(company == '总公司'){
			if(res == 1){
				massage = "当前调拨单已经绑定了包裹单，确定取消单据吗?";
			}
		}

		bootbox.confirm(massage, function(result) {
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
	});

}
//审核单据
function checkBillM(obj) {
	$('body').modalmanager('loading');
	var url = $(obj).attr('data-url');
	var id = '<%$view->get_id()%>';
	var bill_no = '<%$view->get_bill_no()%>';

	bootbox.confirm("确定审核吗?", function(result){
		if(result == true){
			$.post(url, {id : id,bill_no : bill_no}, function(data){
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
				}else{
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


function add(obj){
	var url = $(obj).attr('data-url');
	var tab = $(obj).attr('data-id');
	var title = $(obj).attr('data-title');
    new_tab(tab,title,url);
}
$import(["public/js/select2/select2.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",
	"public/js/jquery-zero/ZeroClipboard.min.js"],function(){

	var info_id= '<%$view->get_id()%>';

	var obj = function(){
		var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
			$('#warehouse_bill_info_m_info select[name="to_warehouse_id"],#warehouse_bill_info_m_info select[name="from_company_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

			$('#warehouse_bill_info_m_info select[name="to_company_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function (e){
  				$(this).valid();
				var _t = $(this).val();
				if (_t) {
					$.post('index.php?mod=warehouse&con=WarehouseBillInfoM&act=getTowarehouseId', {'id': _t}, function (data) {
						$('#warehouse_bill_info_m_info select[name="to_warehouse_id"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
						$('#warehouse_bill_info_m_info select[name="to_warehouse_id"]').change();
					});
				}else{
					$('#warehouse_bill_info_y_info select[name="to_warehouse_id"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
				}
			});

			//关闭容易引起js冲突的页签
			var txt = $('#nva-tab li a');
			txt.each(function(i){
				if($.trim($(this).text()).indexOf('调拨单')  >= 0 ){
					$(this).parent().children('i').trigger('click');
				}
			});

		};

		//表单验证和提交
		var handleForm = function(){
			var url = 'index.php?mod=warehouse&con=WarehouseBillInfoM&act=insert';
			var options1 = {
				url: url,
				error:function ()
				{
					$('.modal-scrollable').trigger('click');
					bootbox.alert({
						message: "请求超时，请检查链接",
						buttons: {
								   ok: {
										label: '确定'
									}
								},
						animate: true,
						closeButton: false,
						title: "提示信息"
					});
					return;
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert({
							message: info_id ? "修改成功!": "添加成功!",
							buttons: {
									   ok: {
											label: '确定'
										}
									},
							animate: true,
							closeButton: false,
							title: "提示信息" ,
							callback:function(){
								if (data._cls)
								{
									util.retrieveReload();
									util.syncTab(data.tab_id);
								}
								else
								{//刷新首页
									warehouse_bill_info_m_search_page(util.getItem("orl"));
									//util.page('index.php?mod=management&con=application&act=search');
								}
							}
						});



					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						bootbox.alert({
							message: data.error ? data.error : (data ? data :'程序异常'),
							buttons: {
									   ok: {
											label: '确定'
										}
									},
							animate: true,
							closeButton: false,
							title: "提示信息"
						});
						return;
					}
				}
			};

			$('#warehouse_bill_info_m_info').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {

				},
				messages: {

				},

				highlight: function (element) { // hightlight error inputs
					$(element)
						.closest('.form-group').addClass('has-error'); // set error class to the control group
					//$(element).focus();
				},

				success: function (label) {
					label.closest('.form-group').removeClass('has-error');
					label.remove();
				},

				errorPlacement: function (error, element) {
					error.insertAfter(element.closest('.form-control'));
				},

				submitHandler: function (form) {
					$("#warehouse_bill_info_m_info").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#warehouse_bill_info_m_info input').keypress(function (e) {
				if (e.which == 13) {
					$('#warehouse_bill_info_m_info').validate().form()
				}
			});
		};
		var initData = function(){
			//批量复制货号
			util.batchCopyGoodsid('<%$view->get_id()%>','batch_copy_goodsid_m_e');
		};

		var from_table = function(){
			$.ajax({
				// url:"public/json/load.json",
				url:"index.php?mod=warehouse&con=WarehouseBillInfoM&act=mkJson",
				dataType:"json",
				type:"POST",
				data:{'id':info_id},
				success:function(res) {
					from_table_data_bill_m(res.id,res.data_bill_m,res.title,res.columns);
				}
			});
			//保存值
			$("body").find("#from_table_data_info_m_btn").click(function(){
				/** 获取表单数据 **/
				var to_warehouse_id = $('#warehouse_bill_info_m_info select[name="to_warehouse_id"]').val();
				var to_company_id = $('#warehouse_bill_info_m_info select[name="to_company_id"]').val();
				var from_company_id = $('#warehouse_bill_info_m_info select[name="from_company_id"]').val();
				var order_sn = $('#warehouse_bill_info_m_info input[name="order_sn"]').val();
				var ship_number = $('#warehouse_bill_info_m_info input[name="ship_number"]').val();
				var bill_note = $('#bill_note').val();
				var mingyijia = $('#mingyijia').val();
				var old_system = $('#old_system').val();

				if ($("#from_table_data_info_m_btn").find("td").hasClass("htInvalid") == true) {
					$("#from_table_data_info_m_btn").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
					return false;
				}else{
					// $("#from_table_data_info_m_btn").prev("p").addClass('text-success').text("保存成功O(∩_∩)O~~");
					// console.log($("#from_table_data_info_m_btn").handsontable('getData'));
					var save = {
						'data':$("#from_table_data_bill_m").handsontable('getData'),
						'to_warehouse_id':to_warehouse_id,
						'to_company_id':to_company_id,
						'from_company_id':from_company_id,
						'order_sn':order_sn,
						'ship_number':ship_number,
						'bill_note':bill_note,
						'id':info_id,
						'old_system':old_system,
					};
 					console.log(save);
				   $.ajax(
				   {
						url :'?mod=warehouse&con=WarehouseBillInfoM&act=amountMax',
						data : {to_warehouse_id:to_warehouse_id,to_company_id:to_company_id,from_company_id:from_company_id,mingyijia:mingyijia},
						dataType : "json",
						type : "POST",
						async: false,
						success : function(res) 
						{
							if (res.success == 1)
							{
								save_info(save,info_id);
								//直接保存
							}
							else
							{
								bootbox.confirm(res.error+"确定继续执行?", function(result) 
								{
									if (result == true) 
									{
										save_info(save,info_id);
									}
								});
							}
						}
				   });
					//e

					

				}
			});
		};


		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
				from_table();
			}
		}
	}();
	obj.init();
});
var save_info_submits = 0;
function save_info(save,info_id)
{   
	$.ajax({
		url:"index.php?mod=warehouse&con=WarehouseBillInfoM&act=update&submits="+save_info_submits,
		data:save,
		dataType:"json",
		type:"POST",
		success:function(res) {
			  if (res.success == "1") {
				util.xalert("保存成功",function(){
					save_info_submits = 0;						
					util.retrieveReload();					
			   });	   
			 } else {	
				save_info_submits = res.submits;
				util.xalert(res.error ? res.error : (res ? res :'程序异常'),function(){
					if(save_info_submits==1){														 
						 $('#from_table_data_info_m_btn').click();	
					}
				});
			 }
		}	
	
     });
}