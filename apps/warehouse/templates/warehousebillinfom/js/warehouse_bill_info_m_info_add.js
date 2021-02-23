var save_warehouse_id ='';  //保存入库仓库ID
var save_company_id ='';  //保存入库公司ID


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

                var checkopen = function(_t){
                    $.post('index.php?mod=warehouse&con=WarehouseBillInfoM&act=getTowarehouseId', {'id': _t}, function (data) {
                            $('#warehouse_bill_info_m_info select[name="to_warehouse_id"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
                            //console.log(save_warehouse_id+'house_id');
                        });
                }
				var initElements1 = function(){
					if (!jQuery().uniform){
						return;
					}
					$('#warehouse_bill_info_m_info select[name="to_company_id"]').select2({
						placeholder: "请选择",
						allowClear: true,
					}).change(function (e){
		  				$(this).valid();
						var _t = $(this).val();
						if (_t) {
							checkopen(_t);
                            $('#warehouse_bill_info_m_info select[name="to_warehouse_id"]').change();
                            save_company_id = $('#warehouse_bill_info_m_info select[name="to_company_id"]').val();
                            save_warehouse_id = $('#warehouse_bill_info_m_info select[name="to_warehouse_id"]').val();
						}else{
							$('#warehouse_bill_info_m_info select[name="to_warehouse_id"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
						}
					});

					$('#warehouse_bill_info_m_info select[name="to_warehouse_id"], #warehouse_bill_info_m_info select[name="from_company_id"]').select2({
						placeholder : "请选择",
						allowClear : true
					}).change(function(e){
						$(this).valid();
					});

					//关闭容易引起js冲突的页签
						var txt = $('#nva-tab li a');
						txt.each(function(i){
							if($.trim($(this).text()).indexOf('编辑：M')  >= 0 ){
								$(this).parent().children('i').trigger('click');
							}
						});

				};

				var initElements = function(){
					//输入订单号后，input失去焦点触发事件
					$('#warehouse_bill_info_m_info input[name="order_sn"]').on('blur',function(){
						if($(this).val()){
							var order_sn = $(this).val();
							$('body').modalmanager('loading');//进度条和遮罩
//                            $('#warehouse_bill_info_m_info').attr('disabled','disabled');     
							$.post('index.php?mod=warehouse&con=WarehouseBillInfoM&act=orderSn_check',{order_sn:order_sn},function(data){
								console.log(data);
								if (data.success==1) {
									var consignee = data.info.consignee;	//客户姓名
									var company_name = data.info.company_name;	//入库公司名称
									save_company_id = data.info.to_company_id //入库公司的ID
									warehouse_name = data.info.warehouse_name;	//入库仓库ID
									save_warehouse_id = data.info.warehouse_id;	//入库仓库名称
                                    var save_from_company_id = data.info.from_company_id;//出库公司
                                    var customer_source_id = data.info.customer_source_id;//客户来源
									$('#warehouse_bill_info_m_info select[name="to_company_id"]').select2("val",save_company_id);
                                    if(customer_source_id == 2034){
                                        $('#warehouse_bill_info_m_info select[name="from_company_id"]').select2("val",save_from_company_id);
                                        checkopen(save_company_id);
                                        $('#warehouse_bill_info_m_info select[name="to_warehouse_id"]').select2("val",523);
                                    }else{
                                        $('#warehouse_bill_info_m_info select[name="to_warehouse_id"]').attr('disabled', false).empty().append('<option value="'+save_warehouse_id+'">'+warehouse_name+'</option>').change();
                                    }
									$('#warehouse_bill_info_m_info input[name="consignee"]').val(consignee);
									$('.modal-scrollable').trigger('click');// 关闭遮罩
								}else if(data.success == 2){
									//订单号是老系统的订单 , 在提交页面做上标记
									$('#old_system').val('1');
								}else{
									util.xalert(data.error);
									return false;
								}
							});
						}

					});
				};

				var from_table1 = function(){
					$.ajax({
						url : "index.php?mod=warehouse&con=WarehouseBillInfoM&act=mkJson",
						dataType : "json",
						type : "POST",
						data : {
							'id' : info_id
						},
						success : function(res){
							
							//$("#Statistics").html(str);
							from_table_data_bill_m(res.id,res.data_bill_m, res.title,res.columns);
							// from_table_data_bill_m(res.id, res.title,res.columns);
						}
					});
					// 保存值
					$("body").find("#from_table_data_info_m_btn").click(
						function() {
							/** 获取表单数据 * */
							if(!save_warehouse_id){
								var save_warehouse_id = $('#warehouse_bill_info_m_info select[name="to_warehouse_id"]').val();
							}
							var from_company_id = $('#warehouse_bill_info_m_info select[name="from_company_id"]').val();

							var order_sn = $('#warehouse_bill_info_m_info input[name="order_sn"]').val();
							var ship_number = $('#warehouse_bill_info_m_info input[name="ship_number"]').val();
							var bill_note = $('#bill_note').val();
							var mingyijia = $('#mingyijia').val();
							var old_system = $('#old_system').val();
							var consignee = $('#warehouse_bill_info_m_info input[name="consignee"]').val();
                            var label_price_total = $('#label_price_total').val();
							if (from_company_id == '')
							{
								bootbox.alert("请选择出库公司");
								return false;
							}
							if (save_company_id == '')
							{
								bootbox.alert("请选择入库公司");
								return false;
							}
							if (save_warehouse_id == '')
							{
								bootbox.alert("请选择入库仓库");
								return false;
							}

							if ($("#from_table_data_bill_m").find("td").hasClass("htInvalid") == true) {
								$("#from_table_data_bill_m").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
								return false;
							} else {
								console.log(save_company_id);
								var save = {
									'data' : $("#from_table_data_bill_m").handsontable('getData'),
									'to_warehouse_id' : save_warehouse_id,
									'to_company_id' : save_company_id,
									'from_company_id' : from_company_id,
									'order_sn' : order_sn,
									'ship_number' : ship_number,
									'bill_note' : bill_note,
									'id' : info_id,
									'old_system' : old_system,
									'consignee' : consignee,
                                    'label_price_total' :label_price_total
								};
								$('body').modalmanager('loading');//进度条和遮罩
                                $('#from_table_data_info_m_btn').attr('disabled','disabled');
							  //判断该价格是否超过限制
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
                                        $('#from_table_data_info_m_btn').removeAttr('disabled');
									}
							   });
							   //alert(555);return false;


							}
						});
				};

				//重置表单
				var initData = function(){
					$('#reset').on('click', function(){
						$('#warehouse_bill_info_m_info select[name="to_company_id"]').select2("val", to_company_id).change();
						$('#warehouse_bill_info_m_info select[name="from_company_id"]').select2("val", from_company_id).change();
					});
				};

				return {
					init : function() {
						initElements1();// 处理表单元素
						initElements();
						from_table1();
						initData();
					}
				}
			}();
			obj1.init();
		/*
			util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillInfoM&act=getGoodsInDetails&bill_id='+info_id);
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
			obj2.init();*/
		});

var save_info_submits = 0;
function save_info(save,info_id)
{
	//start
	$.ajax({
		url : info_id ? "index.php?mod=warehouse&con=WarehouseBillInfoM&act=update&submits="+save_info_submits: "index.php?mod=warehouse&con=WarehouseBillInfoM&act=insert&submits="+save_info_submits,
		data : save,
		dataType : "json",
		type : "POST",
		success : function(res) {
			if (res.success == "1") {
				util.xalert(info_id?"保存成功":"添加成功",function(){
					save_info_submits = 0;						
					if (info_id) {
						$('.modal-scrollable').trigger('click');// 关闭遮罩
					} else {
						//util.retrieveReload();
						var jump_url = 'index.php?mod=warehouse&con=WarehouseBillInfoM&act=edit';
						//x_id 新生成单据的主键ID  label 新生成单据的单号
						util.closeTab(res.x_id);
						util.buildEditTab(res.x_id,jump_url,res.tab_id,res.label);
					}					
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
		//end
}
