
// 取消单据
function closeBillp(obj) {
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
//确认发货
function confirmP(obj) {
	$('body').modalmanager('loading');
	var url = $(obj).attr('data-url');
	var id = '<%$view->get_id()%>';
	var bill_no = '<%$view->get_bill_no()%>';

	bootbox.confirm("确定要改变确认发货吗?", function(result){
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
$import(
	["public/js/select2/select2.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",	//明细table插件
	],function() {
	var info_form_id = 'warehouse_bill_info_p_add';//form表单id
	var info_form_base_url = 'index.php?mod=warehouse&con=WarehouseBillInfoP&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';
	
	var obj = function(){
		var initElements = function(){
			//下拉美化 需要引入"public/js/select2/select2.min.js"
			$('#'+info_form_id+' select').select2({
				placeholder: "请选择",
				allowClear: true,
//				minimumInputLength: 2
			});		
			
			$('#'+info_form_id+' select[name="wholesale_user"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){	
				$(this).valid();
 				var _t = $(this).val();
 			    var opts = _t.split('|');
 			    if (opts.length == 2) {
 			    	if (parseInt(opts[1]) > 0) {
 			    		$('#sign_rel_div').show();
			    		$.ajax({
							url : "index.php?mod=warehouse&con=WarehouseBillInfoP&act=getToCompanyHtml",
							dataType : "text",
							type : "POST",
							data : {
								'company_id' :opts[1]
							},
							success : function(res){
								$('#to_company').html(res).change();
							}
				      });  		
 			    	} else {
 			    		$('#sign_rel_div').hide();
 			    	}
 			    }
			});
			
			$('#'+info_form_id+' input[name="wholesale_user_val"]').blur(function(){	
 				var _t = $(this).val();
 			    var opts = _t.split('|');
 			    //if (opts.length == 1) {
			    		$.ajax({
							url : "index.php?mod=warehouse&con=WarehouseBillInfoP&act=getWholesaleUser&w_sn="+opts[0],
							type : "GET",
							dataType : "json",
							success : function(res){
								if (res.success == 1) {
									$('#'+info_form_id+' input[name="wholesale_user_val"]').val(opts[0]+'|'+res.wholesale_name);
									$('#'+info_form_id+' input[name="wholesale_user"]').val(res.wholesale_user);
								} else {
									$('#'+info_form_id+' input[name="wholesale_user_val"]').val(opts[0]);
									$('#'+info_form_id+' input[name="wholesale_user"]').val('');
									bootbox.alert("找不到该客户，请确认您的客户编号");
								}
							}
				      }); 
 			    //}
			});
           
			$('#'+info_form_id+' select[name="from_company"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$.ajax({
					url : "index.php?mod=warehouse&con=WarehouseBillInfoP&act=getWhoListHtml",
					dataType : "text",
					type : "POST",
					data : {
						'sd_company_id' : $(this).val().split('|')[0]
					},
					success : function(res){
						$('#'+info_form_id+' select[name="wholesale_user"]').html(res);
						$('#'+info_form_id+' select[name="wholesale_user"]').select2("val","").change();
						$('#sign_rel_div').hide();
					}
				});
			});
			$('#wholesale_user').change();
		};

		var from_table1 = function(){
			$.ajax({
				url : "index.php?mod=warehouse&con=WarehouseBillInfoP&act=mkJson",
				dataType : "json",
				type : "POST",
				data : {
					'id' : info_id,
					'from_company':$('#'+info_form_id+' select[name="from_company"]').val().split('|')[0],
				},
				success : function(res){
					from_table_data_p(res.id,res.data_bill_p, res.title,res.columns);
				}
			});
			// 保存值
			var save_info_submits = 0;
			$("body").find("#from_table_data_info_p_btn").click(function() {
				//获取表单数据
                var bill_note = $('#bill_note').val();
				var wholesale_user = $('#warehouse_bill_info_p_add select[name="wholesale_user"]').val();
				if (typeof(wholesale_user) == 'undefined') {
					wholesale_user = $('#warehouse_bill_info_p_add input[name="wholesale_user"]').val();
				}

                var to_company = $('#warehouse_bill_info_p_add select[name="to_company"]').val();
                var from_company = $('#warehouse_bill_info_p_add select[name="from_company"]').val();
                var out_warehouse_type = $('#warehouse_bill_info_p_add select[name="out_warehouse_type"]').val();
                var p_type = $('#warehouse_bill_info_p_add select[name="p_type"]').val();
				var is_invoice = $('#warehouse_bill_info_p_add select[name="is_invoice"]').val();
                if (wholesale_user == '')
                {
                    bootbox.alert("请选择批发客户");
                    return false;
                }
				if (from_company == '')
				{
					bootbox.alert("请选择出库公司");
					return false;
				}
                if (out_warehouse_type == '')
                {
                    bootbox.alert("请选择出库类型");
                    return false;
                }
				if (is_invoice == '')
                {
                    bootbox.alert("请选择是否开票");
                    return false;
                }
				
				var to_company_id = parseInt( wholesale_user.split('|')[1]);
				
				if ($("#from_table_data_bill_p").find("td").hasClass("htInvalid") == true) {
					$("#from_table_data_bill_p").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
					return false;
				} else {
					var save = {
						'data' : $("#from_table_data_bill_p").handsontable('getData'),
						'wholesale_user' : wholesale_user,
						'bill_note' : bill_note,
						'id' : info_id,
						'from_company':from_company,
						'to_company' : to_company_id > 0 ? (to_company ? to_company :  to_company_id +'|'+''):'',
                        'out_warehouse_type' : out_warehouse_type,
                        'p_type' : p_type,
						'is_invoice':is_invoice
						//'to_warehouse' : to_company_id > 0 ? $('#warehouse_bill_info_p_add select[name="to_warehouse"]').val() : ''
					};
					$('body').modalmanager('loading');//进度条和遮罩
                    $('#from_table_data_info_p_btn').attr('disabled','disabled');
					$.ajax({
						url : info_id ? "index.php?mod=warehouse&con=WarehouseBillInfoP&act=update&submits="+save_info_submits : "index.php?mod=warehouse&con=WarehouseBillInfoP&act=insert&submits="+save_info_submits,
						data : save,
						dataType : "json",
						type : "POST",
						success : function(res) {
							if (res.success == "1") {
                                							
								util.xalert(info_id ? '保存成功' :'添加成功',function(){
									save_info_submits = 0;						
									if (info_id) {
										$('.modal-scrollable').trigger('click');// 关闭遮罩
										util.retrieveReload();										
									} else {
										//util.retrieveReload();
										var jump_url = 'index.php?mod=warehouse&con=WarehouseBillInfoP&act=edit';
										//x_id 新生成单据的主键ID  label 新生成单据的单号
										util.closeTab(res.x_id);
										util.buildEditTab(res.x_id,jump_url,res.tab_id,res.label);
									}					
							   });								
								
							} else {
								save_info_submits = res.submits;								
                                $('#from_table_data_info_p_btn').removeAttr('disabled');
								util.xalert(res.error ? res.error : (res ? res :'程序异常'),function(){
									if(save_info_submits==1){														 
										 $('#from_table_data_info_p_btn').click();	
									}
							   });
								

							}
						}
					});
				}
			});
		};


		var initData = function(){
			$('#'+info_form_id+' :reset').on('click',function(){
				//下拉置空
//				$('#'+info_form_id+' select[name="xxxx"]').select2('val','').change();//single
//				$('#'+info_form_id+' select[name="xxxx"]').select2('val',[]).change();//multiple
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
