$import("public/js/select2/select2.min.js",function(){
	var attribute_id= '<%$view->get_id()%>';
    var status = '<%$view->get_status()%>';    //款式类别状态
    var name = '<%$view->get_name()%>';   //材质

	var Obj = function(){
		var initElements = function(){
			//初始化单选按钮组
			$('#app_xilie_info select[name="status"]').select2({
				placeholder: "全部",
				allowClear: true,

			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件

			
			
		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = attribute_id ? 'index.php?mod=style&con=appXilie&act=update' : 'index.php?mod=style&con=appXilie&act=insert';
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
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						alert(attribute_id ? "修改成功!": "添加成功!");
						if (attribute_id)
						{//刷新当前页
							util.page(util.getItem('url'));
						}
						else
						{//刷新首页
							app_xilie_search_page(util.getItem("orl"));
							//util.page('index.php?mod=management&con=application&act=search');
						}
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

			$('#app_xilie_info').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					xilie_name: {
						required: true
					},
					
				},
				messages: {
					xilie_name: {
						required: "系列名称不能为空."
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
					$("#app_xilie_info").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#app_xilie_info input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#app_xilie_info').validate().form()) {
						$('#app_xilie_info').submit();
					}
					else
					{
						return false;
					}
				}
			});
		};
		var initData = function(){
			//下拉组件重置
			$('#app_xilie_info :reset').on('click',function(){
				$('#app_xilie_info input[name="xilie_name"]').select2("val",name);
				$('#app_xilie_info select[name="status"]').select2("val",status);
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
	Obj.init();
});