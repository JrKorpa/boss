$import('public/js/select2/select2.min.js',function(){
	var bill_id= '<%$bill_id%>';
	var url = 'index.php?mod=warehouse&con=WarehouseBillInfoP&act=show&id='+bill_id;
	var obj = function(){

		var initElements = function(){

			$('#to_warehouse').select2({
				placeholder: "请选择",
				allowClear: true
			});

		};
		var handleForm = function(){
				$('#sign_p_bill_form').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					"to_warehouse": {
						required: true
					}
				},
				messages: {
					"to_warehouse": {
						required: "仓库为必填项."
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
					$("#sign_p_bill_form").ajaxSubmit(opt);
				}
			});
			var url = 'index.php?mod=warehouse&con=WarehouseBillInfoP&act=sign_p_bill&ops=postsign&bill_id='+bill_id;
			var opt = {
				url: url,
				dataType : "json",
				type : "POST",
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						util.xalert("签收成功!");
						util.retrieveReload();
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						util.xalert(data.error ? data.error : (data ? data :'程序异常'));
					}
				},
				error:function(){
					$('.modal-scrollable').trigger('click');
					util.xalert("数据加载失败");
				}
			}
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