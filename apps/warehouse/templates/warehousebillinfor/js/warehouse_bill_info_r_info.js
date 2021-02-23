//审核单据
function checkBillR(obj) {
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



/** 塞进页面 **/
function warehouse_bill_goods_show_page(url){
	util.page(url);
}

$import(
	["public/js/select2/select2.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",	//明细table插件
	],function() {
			var info_id = '<%$view->get_id()%>';
			var to_company_id = '<%$view->get_to_company_id()%>';
			var to_warehouse_id = '<%$view->get_to_warehouse_id()%>';
			var from_company_id = '<%$view->get_from_company_id()%>';

			var obj1 = function() {
				var initElements1 = function(){
					if (!jQuery().uniform){
						return;
					}
					$('#warehouse_bill_info_r_show select[name="to_company_id"]').select2({
						placeholder: "请选择",
						allowClear: true,
					}).change(function (e){
		  				$(this).valid();
						var _t = $(this).val();
						if (_t) {
							$.post('index.php?mod=warehouse&con=WarehouseBillInfoR&act=getTowarehouseId', {'id': _t}, function (data) {
								$('#warehouse_bill_info_r_show select[name="to_warehouse_id"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
								$('#warehouse_bill_info_r_show select[name="to_warehouse_id"]').change();
							});
						}else{
							$('#warehouse_bill_info_r_show select[name="to_warehouse_id"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
						}
					});

					$('#warehouse_bill_info_r_show select[name="to_warehouse_id"], #warehouse_bill_info_r_show select[name="from_company_id"]').select2({
						placeholder : "请选择",
						allowClear : true
					}).change(function(e){
						$(this).valid();
					});

					//关闭容易引起js冲突的页签
					if (info_id) {
						var txt = $('#nva-tab li a');
						txt.each(function(i){
							if($.trim($(this).text()) == '调拨单'){
								$(this).parent().children('i').trigger('click');
							}
						});
					}
				};

				var from_table1 = function(){
					$.ajax({
						url : "index.php?mod=warehouse&con=WarehouseBillInfoR&act=mkJson",
						dataType : "json",
						type : "POST",
						data : {
							'id' : info_id
						},
						success : function(res){
							//from_table_data_bill_m(res.id,res.data_bill_m, res.title,res.columns);
							// from_table_data_bill_m(res.id, res.title,res.columns);
						}
					});
					// 保存值
					$("body").find("#from_table_data_info_m_btn").click(
						function() {
							/** 获取表单数据 * */
							var to_warehouse_id = $('#warehouse_bill_info_r_show select[name="to_warehouse_id"]').val();
							var to_company_id = $('#warehouse_bill_info_r_show select[name="to_company_id"]').val();
							var from_company_id = $('#warehouse_bill_info_r_show select[name="from_company_id"]').val();

							var order_sn = $('#warehouse_bill_info_r_show input[name="order_sn"]').val();
							var ship_number = $('#warehouse_bill_info_r_show input[name="ship_number"]').val();
							var bill_note = $('#bill_note').val();

							if (from_company_id == '')
							{
								bootbox.alert("请选择出库公司");
								return false;
							}
							
							if ($("#from_table_data_bill_m").find("td").hasClass("htInvalid") == true) {
								$("#from_table_data_bill_m").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
								return false;
							} else {
								var save = {
									'data' : $("#from_table_data_bill_m").handsontable('getData'),
									'to_warehouse_id' : to_warehouse_id,
									'to_company_id' : to_company_id,
									'from_company_id' : from_company_id,
									'order_sn' : order_sn,
									'ship_number' : ship_number,
									'bill_note' : bill_note,
									'id' : info_id,
								};
								$('body').modalmanager('loading');//进度条和遮罩
								$.ajax({
									url : info_id ? "index.php?mod=warehouse&con=WarehouseBillInfoM&act=update" : "index.php?mod=warehouse&con=WarehouseBillInfoM&act=insert",
									data : save,
									dataType : "json",
									type : "POST",
									success : function(res) {
										if (res.success == "1") {
											bootbox.alert({
												message : info_id ? '修改调拨单成功' : res.error,
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
												util.retrieveReload();
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

				//重置表单
				var initData = function(){
					/*$('#reset').on('click', function(){
						$('#warehouse_bill_info_r_show select[name="to_company_id"]').select2("val", to_company_id).change();
						$('#warehouse_bill_info_r_show select[name="from_company_id"]').select2("val", from_company_id).change();
					});*/
				};

				return {
					init : function() {
						initElements1();// 处理表单元素
						from_table1();
						initData();
					}
				}
			}();
			obj1.init();

			util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillInfoR&act=getGoodsInDetails&bill_id='+info_id);
			util.setItem('listDIV',"warehouse_bill_goods_list");
			var obj2 = function(){
				var initElements2 = function(){
					warehouse_bill_goods_show_page(util.getItem("orl"));
				}

				return {
					init : function(){
						initElements2();// 处理表单元素
					}
				}
			}();
			obj2.init();
		});