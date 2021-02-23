$import(function(){
	var id = '';
	var obj = function(){
			var initElements = function(){
				//复选框按钮
				$('#purchase_receipt_info_add select[name="prc_id"]').select2({
					placeholder: "请选择",
					allowClear: true
				
				}).change(function(e){
					$(this).valid();
				});	
			}
			var initData = function(){}
			var handleForm = function(){
					var url = 'index.php?mod=purchase&con=PurchaseReceipt&act=insert';
					var options1 = {
						url: url,
						error:function ()
						{
							bootbox.alert('请求超时，请检查链接');
						},
						beforeSubmit:function(frm,jq,op){
							$('body').modalmanager('loading');//进度条和遮罩
						},
						success: function(data) {
							if(data.success == 1 ){
								$('.modal-scrollable').trigger('click');//关闭遮罩
								alert("添加成功!");
								var jump_url = 'index.php?mod=purchase&con=PurchaseReceipt&act=edit';
								util.buildEditTab(data.x_id,jump_url,data.tab_id);
								
							}else{
								$('body').modalmanager('removeLoading');//关闭进度条
								bootbox.alert(data.error ? data.error : (data ? data :'程序异常'));
							}
						}, 
						error:function(){
							$('.modal-scrollable').trigger('click');
							bootbox.alert("数据加载失败");  
						}
					};
					$('#purchase_receipt_info_add').validate({
						errorElement: 'span', //default input error message container
						errorClass: 'help-block', // default input error message class
						focusInvalid: false, // do not focus the last invalid input
						rules: {
							ship_num: {
								required: true
							},
							prc_id: {
								required: true
							}
						},
						messages: {
							ship_num: {
								required: "出货单号不能为空.",
							},
							prc_id: {
								required: "供应商不能为空.",
							}
						},
		
						highlight: function (element) { // hightlight error inputs
							$(element).closest('.form-group').addClass('has-error'); // set error class to the control group
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
							$("#purchase_receipt_info_add").ajaxSubmit(options1);
						}
					});
					
					//回车提交
					$('#purchase_receipt_info_add input').keypress(function (e) {
						if (e.which == 13) {
							if ($('#purchase_receipt_info_add').validate().form()) {
								$('#purchase_receipt_info_add').submit();
							}
							else
							{
								return false;
							}
						}
					});
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