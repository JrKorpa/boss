$import('public/js/select2/select2.min.js',function(){
	var obj = function(){
	
		var initElements = function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			}
			var test = $("#purchase_iqc_opra input[name='opra_code']:not(.toggle, .star, .make-switch)");
			if (test.size() > 0) {
				test.each(function (){
					if ($(this).parents(".checker").size() == 0){
						$(this).show();
						$(this).uniform();
					}
				});
			}
		};
		var handleForm = function(){
				$('#purchase_iqc_opra').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
				},
				messages: {
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
					$("#purchase_iqc_opra").ajaxSubmit(opt);
				}
			});
			var url = 'index.php?mod=purchase&con=PurchaseReceiptDetail&act=insertIqc';
			var opt = {
				url: url,
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						alert("操作成功!");
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

			//回车提交
			$('#purchase_iqc_opra input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#purchase_iqc_opra').validate().form()) {
						$('#purchase_iqc_opra').submit();
					}
					else
					{
						return false;
					}
				}
			});	
		};

		var initData = function(){};
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