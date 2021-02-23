$import("public/js/select2/select2.min.js",function(){
	var id = '<%$view->get_id()%>';
	//闭包
	var LogisticsInfoObj = function(){
		var initElements=function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			}
			$('#logistics_deli_uu select').select2({
					placeholder: "请选择",
					allowClear: true
				}).change(function(e){
					$(this).valid();
			});
		}
		//表单验证和提交
		var handleForm = function(){
			var url =id?'index.php?mod=shipping&con=DeliveryList&act=update':'index.php?mod=shipping&con=DeliveryList&act=insert';
			var options1 = {
				url: url,
				error:function ()
				{
					alert('请求超时，请检查链接');
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					//debugger;
					if(data.success == 1 ){
						bootbox.alert({   
							message: id?"修改成功":"添加成功!",
							buttons: {  
									   ok: {  
											label: '确定'  
										}  
									},
							animate: true, 
							closeButton: false,
							title: "提示信息",
							callback:function(){
								$('.modal-scrollable').trigger('click');//关闭遮罩
																	
							}
						});  
						util.page(util.getItem('url'));

					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						alert(data.error ? data.error : (data ? data :'程序异常'));
					}
				}, 
				error:function(){
					$('.modal-scrollable').trigger('click');
					alert("数据加载失败");  
				}
			};

			$('#logistics_deli_uu').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					freight_no: {
						required: true,
					},
					consignee: {
						required: true,
						maxlength:20,
					},
					express_id: {
						required: true,
					},
					cons_address: {
						required: true,
					},
					sender: {
						required: true,
						maxlength:20,
					},
					department: {
						required: true,
					},
					note: {
						maxlength:20,
					},
	
				},

				messages: {
					freight_no: {
						required: "请输入快递单号",
					},
					consignee: {
						required: "请输入收件人",
						maxlength:"收货人不大于20个字符",
					},
					express_id: {
						required: "请选择快递公司",
					},
					cons_address: {
						required: "请输入地址",
					},
				    sender: {
						required: "请输入发货人",
						maxlength:"发货人不大于20个字符",
					},
					department: {
						required: "请选择发货部门",
					},
					note: {
						required: "长度不大于20个字符",
					},

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
					$("#logistics_deli_uu").ajaxSubmit(options1);
				}
			});

		};
		var initData=function(){
		}

		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	LogisticsInfoObj.init();
});