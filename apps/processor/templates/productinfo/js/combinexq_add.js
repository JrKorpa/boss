$import('public/js/select2/select2.min.js',function(){
	var buchan_id= '<%$id%>';
	var obj = function(){

		var initElements = function(){

			$('#combinexq_add_form select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

		};
		var handleForm = function(){
				$('#combinexq_add_form').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					factory: {
						required: true
					}
				},
				messages: {
					factory: {
						required: "工厂为必填项."
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
					$("#combinexq_add_form").ajaxSubmit(opt);
				}
			});

               var opt = {
				url: 'index.php?mod=processor&con=ProductInfo&act=combineXQSave',
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						util.xalert("保存成功!");
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

			//回车提交
			$('#combinexq_add_form input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#combinexq_add_form').validate().form()) {
						$('#combinexq_add_form').submit();
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