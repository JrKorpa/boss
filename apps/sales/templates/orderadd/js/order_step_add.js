function step_last()
{
	var step_num_last = $("#step_num_last").val();
	$.post('?mod=sales&con=OrderAdd&act=add_step_last',{step_num_last:step_num_last},function(res){
		//将获取内容添加到表单内
		$("#order_step_add").html(res.content);
	})
}
$import("public/js/select2/select2.min.js",function(){
	//闭包
	var OrderAddObj = function(){
		var initElements=function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			}
		}
		//表单验证和提交
		var handleForm = function(){
			var url = 'index.php?mod=sales&con=OrderAdd&act=add_step';
			var options1 = 
			{
				url: url,
				error:function ()
				{
					alert('请求超时，请检查链接');
				},
				beforeSubmit:function(frm,jq,op)
				{
				},
				success: function(data)
				{
					if(data.success == 1 )
					{
						//将获取内容添加到表单内
						$("#order_step_add").html(data.content);
					}
				}, 
				error:function()
				{
					alert("数据加载失败");  
				}
			};
			//下一步
			$('#order_step_add').validate
			({
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
					$("#order_step_add").ajaxSubmit(options1);
				}
			});

		};
		var initData=function(){
			//默认加载基础信息
			$.post('?mod=sales&con=OrderAdd&act=add_step_last',{step_num_last:1},function(res){
			//将获取内容添加到表单内
			$("#order_step_add").html(res.content);
			})
		}
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	OrderAddObj.init();
});