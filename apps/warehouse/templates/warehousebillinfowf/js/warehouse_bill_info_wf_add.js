function closeBill(obj){
	bootbox.confirm("确定取消吗?", function(result) {
		if (result == true) {
			var url = obj.getAttribute('data-url');
			var bill_id = '<%$view->get_id()%>';
			$.get(url+'&bill_id='+bill_id, '' , function(res){
				if(res.success == 1){
					bootbox.alert(res.error);
					util.retrieveReload();
				}else{
					bootbox.alert(res.error);
				}
			});
		}
	});
}

function check_order(obj){
	var order_sn = $(obj).val();
	if(order_sn != ''){
		var url = "index.php?mod=warehouse&con=WarehouseBill&act=CheckOrderSn&order_sn="+order_sn;
		$.get(url , '' , function(res){
			if(res.success != 1){
				util.xalert("系统找不到该订单号:"+order_sn);
				return false;
			}else{
				var warehouse_sp = res.data.warehouse_id+'|'+res.data.warehouse_name;
                //var warehouse_cp = res.data.code+'|'+res.data.warehouse_name;
                //$('#warehouse_bill_info_wf_add select[name="to_warehouse_id"]').attr('disabled', false).append('<option value="'+warehouse_sp+'">'+warehouse_cp+'</option>');
                $('#warehouse_bill_info_wf_add select[name="to_warehouse_id"]').select2("val",warehouse_sp);
				$('#customer').attr('value',res.data.customer);

			}
		});
	}
}


//打印条码
function printcode(){
	var down_info = 'down_info';
    var bill_id = '<%$view->get_id()%>';
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


//匿名回调
$import(
	["public/js/select2/select2.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",	//明细table插件
	"public/js/jquery-zero/ZeroClipboard.min.js",
	],function(){
	var info_id = '<%$view->get_id()%>';

	//匿名函数+闭包
	var obj = function(){

		var initElements = function(){
			$('#warehouse_bill_info_wf_add select[name="to_warehouse_id"], #warehouse_bill_info_wf_add select[name="from_company_id"] , #warehouse_bill_info_wf_add select[name="to_customer_id"]').select2({
				placeholder : "请选择",
				allowClear : true
			}).change(function(e){
				$(this).valid();
			});
		};

		var from_data = function(){
			$.ajax({
				url : "index.php?mod=warehouse&con=WarehouseBillInfoWf&act=mkJson",
				dataType : "json",
				type : "POST",
				data : {
					'id' : info_id
				},
				success : function(res){
					from_table_data_bill_wf(res.id,res.data_bill_wf, res.title,res.columns);
				}
			});
			// 保存值
			$("body").find("#from_table_data_info_wf_btn").click(
				function() {
					/** 获取表单数据 * */
					var to_warehouse_id = $('#warehouse_bill_info_wf_add select[name="to_warehouse_id"]').val();
					var from_company_id = $('#warehouse_bill_info_wf_add select[name="from_company_id"]').val();
					var to_customer_id = $('#warehouse_bill_info_wf_add select[name="to_customer_id"]').val();

					var order_sn = $('#warehouse_bill_info_wf_add input[name="order_sn"]').val();
					var ship_number = $('#warehouse_bill_info_wf_add input[name="ship_number"]').val();
					var bill_note = $('#bill_notes').val();

					if (from_company_id == '')
					{
						bootbox.alert("请选择发货方");
						return false;
					}
					if (to_warehouse_id == '')
					{
						bootbox.alert("请选择收货方");
						return false;
					}

                    $('#from_table_data_info_wf_btn').attr('disabled','disabled');      //提交后锁定submit按钮

					if ($("#warehouse_bill_info_wf_add").find("td").hasClass("htInvalid") == true) {
						$("#warehouse_bill_info_wf_add").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
						return false;
					} else {
						var save = {
							'data' : $("#from_table_data_bill_wf").handsontable('getData'),
							'to_warehouse_id' : to_warehouse_id,
							'from_company_id' : from_company_id,
							'order_sn' : order_sn,
							'ship_number' : ship_number,
							'bill_note' : bill_note,
							'to_customer_id' : to_customer_id,
							'id' : info_id,
						};
						$('body').modalmanager('loading');//进度条和遮罩
						$.ajax({
							url : info_id ? "index.php?mod=warehouse&con=WarehouseBillInfoWf&act=update" : "index.php?mod=warehouse&con=WarehouseBillInfoWf&act=insert",
							data : save,
							dataType : "json",
							type : "POST",
							success : function(res) {
								if (res.success == "1") {
									bootbox.alert({
										message : info_id ? '修改维修调拨单成功' : res.error,
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
									} else {
										//util.retrieveReload();
										var jump_url = 'index.php?mod=warehouse&con=WarehouseBillInfoWf&act=edit';
										//x_id 新生成单据的主键ID  label 新生成单据的单号
										util.closeTab(res.x_id);
										util.buildEditTab(res.x_id,jump_url,res.tab_id,res.label);
									}
								} else {
                                    $('#from_table_data_info_wf_btn').removeAttr('disabled');       //解锁submit按钮
									bootbox.alert({
										message : res.error ? res.error : (res ? res : '程序返回异常'),										
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
		}

		var initData = function(){
			//批量复制货号
			if(info_id){
				util.batchCopyGoodsid('<%$view->get_id()%>','batch_copy_goodsid_wf_e');
			}
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				initData();//处理默认数据
				from_data();
			}
		}
	}();

	obj.init();
});
