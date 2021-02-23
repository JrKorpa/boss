
$import(function(){
	var obj = function(){
			var initElements = function(){
				//初始化单选按钮组
				if (!jQuery().uniform) {
					return;
				}
			}
			var initData = function(){}
			var handleForm = function(){
					$('#product_info_4c_edit').validate({
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
						$("#product_info_4c_edit").ajaxSubmit(opt);
					}
				});
				var url = 'index.php?mod=processor&con=ProductInfoFourC&act=update';
				var opt = {
					url: url,
					beforeSubmit:function(frm,jq,op){
						$('body').modalmanager('loading');//进度条和遮罩
					},
					success: function(data) {
						if(data.success == 1 ){
							$('.modal-scrollable').trigger('click');//关闭遮罩
							util.xalert("修改成功",function(){
								util.retrieveReload();
							});
															
						}else{
							$('body').modalmanager('removeLoading');//关闭进度条
							if(data.error){
								util.xalert(data.error,function(){ });
							}else{
							    util.xalert(data,function(){ });
							}
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