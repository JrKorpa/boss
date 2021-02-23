$import(function(){
	var id = '<%$view->get_id()%>';
	var obj = function(){
			var initElements = function(){
				//初始化单选按钮组
				if (!jQuery().uniform) {
					return;
				}
				var test = $("#purchase_type_info input[name='is_auto']:not(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function (){
						if ($(this).parents(".checker").size() == 0){
							$(this).show();
							$(this).uniform();
						}
					});
				}
				
				var test1 = $("#purchase_type_info input[name='is_enabled']:not(.toggle, .star, .make-switch)");
				if (test1.size() > 0) {
					test1.each(function (){
						if ($(this).parents(".checker").size() == 0){
							$(this).show();
							$(this).uniform();
						}
					});
				}
				
				var test1 = $("#purchase_type_info input[name='is_system']:not(.toggle, .star, .make-switch)");
				if (test1.size() > 0) {
					test1.each(function (){
						if ($(this).parents(".checker").size() == 0){
							$(this).show();
							$(this).uniform();
						}
					});
				}
			}
			var initData = function(){}
			var handleForm = function(){
					var url = id ? 'index.php?mod=purchase&con=PurchaseType&act=update' : 'index.php?mod=purchase&con=PurchaseType&act=insert';
			
					
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
								util.xalert(id ? "修改成功!": "添加成功!");
								if (id)
								{//刷新当前页
									util.page(util.getItem('url'));
								}
								else
								{//刷新首页
									purchase_type_search_page(util.getItem("orl"));
									util.page('index.php?mod=purchase&con=PurchaseType&act=search');
								}
							}else{
								$('body').modalmanager('removeLoading');//关闭进度条
								util.xalert(data.error ? data.error : (data ? data :'程序异常'));
							}
						}, 
						error:function(){
							$('.modal-scrollable').trigger('click');
							util.xalert("数据加载失败");
						}
					};
		
					$('#purchase_type_info').validate({
						errorElement: 'span', //default input error message container
						errorClass: 'help-block', // default input error message class
						focusInvalid: false, // do not focus the last invalid input
						rules: {
							t_name: {
								required: true
							}
						},
						messages: {
							t_name: {
								required: "采购分类名称不能为空."
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
							$("#purchase_type_info").ajaxSubmit(options1);
						}
					});
					//回车提交
					$('#purchase_type_info input').keypress(function (e) {
						if (e.which == 13) {
							if ($('#purchase_type_info').validate().form()) {
								$('#purchase_type_info').submit();
							}
							else
							{
								return false;
							}
						}
					});
				
				}
		
		     ////
			return {
					init:function(){
						initElements();	
						handleForm();
						initData();
						}
			}
			////
		
	}();
	obj.init();
				 
});