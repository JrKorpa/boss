$import('public/js/select2/select2.min.js',function(){
	var buchan_id= '<%$ids%>';
	var obj = function(){

		var initElements = function(){

			$('#sel_factory_pl select[name="prc"]').select2({
				placeholder: "请选择",
				allowClear: true

			}).change(function(e){
				$(this).valid();
			});

		};
		var handleForm = function(){
				$('#sel_factory_pl').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					prc: {
						required: true
					}
				},
				messages: {
					prc: {
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
					$("#sel_factory_pl").ajaxSubmit(opt);
				}
			});
			var url = 'index.php?mod=processor&con=ProductInfo&act=sel_factory_pl_save';
			var opt = {
				url: url,
				beforeSubmit:function(frm,jq,op){
					//需要提示是否需要配石配石
					$('body').modalmanager('loading');//进度条和遮罩	
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						util.xalert("分配成功!");
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
			$('#sel_factory_pl input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#sel_factory_pl').validate().form()) {
						$('#sel_factory_pl').submit();
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