$import(function(){
	var obj = function(){
			var initElements = function(){
				$('#product_info_edit select').select2({
					placeholder: "请选择",
					allowClear: true
				
				}).change(function(e){
					$(this).valid();
				});	
				//初始化单选按钮组
				if (!jQuery().uniform) {
					return;
				}
				var test = $(".radio-inline input:not(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function (){
						if ($(this).parents(".checker").size() == 0){
							$(this).show();
							$(this).uniform();
						}
					});
				}
			}
			var initData = function(){}
			var handleForm = function(){
					$('#product_info_edit').validate({
					errorElement: 'span', //default input error message container
					errorClass: 'help-block', // default input error message class
					focusInvalid: false, // do not focus the last invalid input
					rules: {
						num: {
							required: true,
							number:true
						}
					},
					messages: {
						num:{
							required: "数量不能为空",
							number: "数量只能是数字"
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
						$("#product_info_edit").ajaxSubmit(opt);
					}
				});
				var url = 'index.php?mod=processor&con=ProductInfo&act=update';
				var opt = {
					url: url,
					beforeSubmit:function(frm,jq,op){
						$('body').modalmanager('loading');//进度条和遮罩
					},
					success: function(data) {
						if(data.success == 1 ){
							$('.modal-scrollable').trigger('click');//关闭遮罩
							alert("修改成功!");
							util.retrieveReload();								
						}else{
							$('body').modalmanager('removeLoading');//关闭进度条
							alert(data.error ? data.error : (data ? data :'程序异常'));
						}
					}, 
					error:function(){
						$('.modal-scrollable').trigger('click');
						alert("数据加载失败");  
					}
				}
			}
		
			return {
					init:function(){
						initElements();	
						handleForm();
						initData();
						}
			}
		
	}();
	obj.init();
				 
});