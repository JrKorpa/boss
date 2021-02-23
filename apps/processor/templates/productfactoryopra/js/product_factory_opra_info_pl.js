$import('public/js/select2/select2.min.js',function(){
	var ids   ="<%$ids%>";
	var obj = function(){

		var initElements = function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			}
			var test = $("#factory_opra input[name='fac_opra']:not(.toggle, .star, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
		};
		var handleForm = function(){
				$('#factory_opra').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input

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
					$("#factory_opra").ajaxSubmit(opt);
				}
			});
			var url = 'index.php?mod=processor&con=ProductFactoryOpra&act=insert_pl';
			var opt = {
				url: url,
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						// alert("操作成功!");
						bootbox.alert({
							message: data.error,
							buttons: {
							   ok: {
									label: '确定'
								}
							},
							animate: true,
							closeButton: false,
							title: "提示信息"
						});
						//util.refresh("productinfo-"+buchan_id,data.title,'index.php?mod=processor&con=ProductInfo&act=show&id='+buchan_id);
						util.retrieveReload();
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						// alert(data.error ? data.error : (data ? data :'程序异常'));
						bootbox.alert({
							message: data.error ? data.error : (data ? data :'程序异常'),
							buttons: {
							   ok: {
									label: '确定'
								}
							},
							animate: true,
							closeButton: false,
							title: "提示信息"
						});
					}
				},
				error:function(){
					$('.modal-scrollable').trigger('click');
					alert("数据加载失败");
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