$import('public/js/select2/select2.min.js',function(){
	var obj = function(){
			var initElements = function(){
			$('#purchase_info_send_to_factory select').select2({
				placeholder: "请选择",
				allowClear: true
			});
			}
			var handleForm = function(){
					var url ='index.php?mod=purchase&con=PurchaseInfo&act=updateRelFactory';
					var options1 = {
						url: url,
						error:function ()
						{
							util.xalert('请求超时，请检查链接');
						},
						beforeSubmit:function(frm,jq,op){
							$('body').modalmanager('loading');//进度条和遮罩
						},
						success: function(data) {
							if(data.success == 1 ){
								$('.modal-scrollable').trigger('click');//关闭遮罩
								util.xalert('分配成功',function(){
									util.retrieveReload();
								})																
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
					//回车提交
					$('#purchase_info_send_to_factory').keypress(function (e) {
						if (e.which == 13) {
							if ($('#purchase_info_send_to_factory').validate().form()) {
								$('#purchase_info_send_to_factory').submit();
							}
							else
							{
								return false;
							}
						}
					});
					$('#purchase_info_send_to_factory').validate({
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
							$("#purchase_info_send_to_factory").ajaxSubmit(options1);
						}
					});
				
				}
			return {
					init:function(){
						initElements();	
						handleForm();
						}
			}
	}();
	obj.init();
				 
});