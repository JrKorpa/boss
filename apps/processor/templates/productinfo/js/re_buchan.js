$import('public/js/select2/select2.min.js',function(){
	var obj = function(){

		var initElements = function(){

		};
		var handleForm = function(){
				$('#re_buchan_info').validate({
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
					$("#re_buchan_info").ajaxSubmit(opt);
				}
			});
			var url = 'index.php?mod=processor&con=ProductInfo&act=re_buchan_save';
			var opt = {
				url: url,
				beforeSubmit:function(frm,jq,op){

				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						util.xalert("操作成功!");
						//util.refresh("productinfo-"+buchan_id,data.title,'index.php?mod=processor&con=ProductInfo&act=show&id='+buchan_id);
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
			$('#re_buchan_info input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#re_buchan_info').validate().form()) {
						$('#re_buchan_info').submit();
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