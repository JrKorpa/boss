$import(function(){
	var info_form_id = 'app_order_action_info';//form表单id
	var info_form_base_url = 'index.php?mod=sales&con=BaseOrderInfo&act=action_';//基本提交路径
	var info_id= '<%$action_id%>';//记录主键

	var obj = function(){
		var initElements = function(){	
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
					$('.modal-scrollable').trigger('click');//关闭遮罩
					if(data.success == 1 )
					{
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
					}else if(res.success==0){
                        util.xalert(res.error);
					}
				}
			});
	}else{
		$("input[name=invoice_amount]").val('0.00');
	}
	
}