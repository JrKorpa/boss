//跳转到添加快递页面
function add_delivery()
{
	var order_sn = $("#order_sn").val();
	var url ="index.php?mod=shipping&con=LogisticsDelivery&act=delivery_info";
	$.post(url,{order_sn:order_sn},function(data){
		if (data.success == true)
		{
			$("#logistics_deli_uu").html(data.content);
		}
	})
	
	//alert(order_sn);
}

$import("public/js/select2/select2.min.js",function(){
	//闭包
	var LogisticsInfoObj = function(){
		var initElements=function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			}
		}
		//表单验证和提交
		var handleForm = function(){
			var url ='index.php?mod=shipping&con=LogisticsDelivery&act=insert';
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
							message: "添加成功!",
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

			$('#logistics_deli_qq').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					delivery_sn: {
						required: true,
					},
				},

				messages: {
					delivery_sn: {
						required: "快递单号不能为空",
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
					$("#logistics_deli_qq").ajaxSubmit(options1);
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