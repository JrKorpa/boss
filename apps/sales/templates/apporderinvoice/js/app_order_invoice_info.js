$import(function(){
	var info_form_id = 'app_order_invoice_info';//form表单id
	var info_form_base_url = 'index.php?mod=sales&con=AppOrderInvoice&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';//记录主键

	var obj = function(){
		var initElements = function(){
			 $('#'+info_form_id+' select').select2({
				 placeholder: "请选择",
				 allowClear: true
             }).change(function(e) {
				 $(this).valid();
             });
	
		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+(info_id ? 'update' : 'insert');
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout(info_form_id);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(info_form_id);
				},
				success: function(data) {
					$('#'+info_form_id+' :submit').removeAttr('disabled');
					
					if(data.success == 1 )
					{   
					    $('.modal-scrollable').trigger('click');//关闭遮罩
						util.xalert(
							info_id ? "修改成功!": "添加成功!",
							function(){
								util.retrieveReload();
								if (data.tab_id)
								{
									util.syncTab(data.tab_id);
								}
							}
						);
 
					}
					else
					{
						util.error(data);
					}
				}
			};

			$('#'+info_form_id).validate({
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
					$('#'+info_form_id).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id).validate().form()
				}
			});
		};
		var initData = function(){
			 $('#'+info_form_id+' :reset').on('click',function(){
				$('#'+info_form_id+' select[name="is_invoice"]').select2("val",is_invoice);
             })
			 
			 $('#'+info_form_id+' select[name="title_type"]').change(function(){
			       var title_type = $(this).val();		
				   var invoice_title = $('#'+info_form_id+' input[name="invoice_title"]').val();
				   if(title_type==1){
					    
						$('#'+info_form_id+' input[name="invoice_title"]').val("<%$view->get_invoice_title()|default:"个人"%>");
						$('#'+info_form_id+' input[name="taxpayer_sn"]').attr("readonly",true).val("");
				   }else{
					   if(invoice_title=="个人"){
					      $('#'+info_form_id+' input[name="invoice_title"]').val("");
					   }
					   $('#'+info_form_id+' input[name="taxpayer_sn"]').attr("readonly",false);
				   }
			 });
			 $('#'+info_form_id+' select[name="title_type"]').change();
		
		};
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	obj.init();
});

var getOrderPrice=function(order_id){
	var is_invoice=$("#is_invoice").val();
	if(is_invoice==1){
		$.ajax({
				type:"POST",
				url: 'index.php?mod=sales&con=AppOrderInvoice&act=getOrderPrice',
				data:{'order_id':order_id},
				dataType: "json",
				async:false,
				success: function(res){
					if(res.success==1){
						$("input[name=invoice_amount]").val(res.error);
						$("select[name=invoice_status]").val(2);
						$("input[name=invoice_num]").focus();
					}else if(res.success==0){
                        util.xalert(res.error);
					}
				}
			});
	}else{
		$("input[name=invoice_amount]").val('0.00');
		$("select[name=invoice_status]").val(1);
		$("input[name=invoice_num]").val();
	}
	
}