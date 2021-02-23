
$import("public/js/select2/select2.min.js",function(){
	var id= '<%$view->get_id()%>';
	//闭包
	var InfoObj = function(){

		var initElements=function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			}
			var test = $("#order_info_invoice input[name='invoice_need']:not(.toggle, .star, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
		}
		//表单验证和提交
		var handleForm = function(){
			var url = 'index.php?mod=sales&con=Order&act=update_invoice';
			var options1 = {
				url: url,
				error:function ()
				{
					alert('请求超时，请检查链接');
				},
				beforeSubmit:function(frm,jq,op){
					//console.log(frm);return false;
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					//debugger;
					if(data.success == 1 ){
						bootbox.alert({   
							message: id ? "修改成功!": "添加成功!",
							buttons: {  
									   ok: {  
											label: '确定'  
										}  
									},
							animate: true, 
							closeButton: false,
							title: "提示信息",
							callback:function(){
								//{locale:"zh_CN",backdrop:!0,animate:!0,className:null,closeButton:!0,show:!0}
								$('.modal-scrollable').trigger('click');//关闭遮罩
							
								if (data._cls)
								{								
									util.retrieveReload();
								}
								else
								{//刷新首页
									order_search_page(util.getItem("orl"));
								}									
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
			$('#order_info_invoice').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					invoice_amount:{
						number:true,
					}

				},

				messages: {
					invoice_amount:{
						number:"请输入正确的格式"
					}

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
					$("#order_info_invoice").ajaxSubmit(options1);
				}
			});

			$('#order_info_invoice input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#order_info_invoice').validate().form()) {
						$('#order_info_invoice').submit();
					}
					else
					{
						return false;
					}
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
	InfoObj.init();
});