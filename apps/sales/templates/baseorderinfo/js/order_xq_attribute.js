$import(function(){
	

	var obj = function(){
		var initElements = function(){
            $('#app_base_order_goods_info select').select2({
                placeholder: "请选择",
                allowClear: true,

            }).change(function(e){
                $(this).valid();
            });


		};
		
		//表单验证和提交
		var handleForm = function(){

			var goods_id = $('#app_base_order_goods_info input[name="sale_goods_id"]').val();
			var id = $('#app_base_order_goods_info input[name="_id"]').val();
			var department = $('#app_base_order_user_info select[name="department_id"]').val();
			var formdata = $("#app_order_detail_style_goods_form").serialize();
			var url = 'index.php?mod=sales&con=BaseOrderInfo&act=saveCartGoods&goods_id='+goods_id+"&id="+id+"&department="+department;
			var options1 = {
				url: url,
				data:formdata,
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
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert({   
							message: "添加成功!",
							buttons: {  
									   ok: {  
											label: '确定'  
										}  
									},
							animate: true, 
							closeButton: false,
							title: "提示信息" 
						});
						$("#reload_cart_button").trigger('click');
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
					}
				}
			};

			$('#app_order_detail_style_goods_form').validate({
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
					$("#app_order_detail_style_goods_form").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#app_order_detail_style_goods_form input').keypress(function (e) {
				if (e.which == 13) {
					$('#app_order_detail_style_goods_form').validate().form()
				}
			});
		};
		var initData = function(){
		
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
