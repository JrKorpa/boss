$import(["public/js/select2/select2.min.js",
"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	var app_user_bespoke_log_info_id ='<%$view->get_log_id()%>';
	var baseMemberInfoInfogObj = function(){
		var initElements = function(){};
			if (!jQuery().uniform) {
				return;
			}
			$('#app_user_bespoke_log_info select[name="member_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

			$('#app_user_bespoke_log_info select[name="re_status"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
		var handleForm = function(){
			//表单验证和提交
			var url = app_user_bespoke_log_info_id ? 'index.php?mod=bespoke&con=AppUserBespokeLog&act=update' : 'index.php?mod=bespoke&con=AppUserBespokeLog&act=insert';
			var options1 = {
				url: url,
				error:function ()
				{
					alert('请求超时，请检查链接');
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						alert(app_user_bespoke_log_info_id ? "修改成功!": "添加成功!");
						if (app_user_bespoke_log_info_id)
						{//刷新当前页
							util.page(util.getItem('url'));
						}
						else
						{//刷新首页
							app_user_bespoke_log_search_page(util.getItem("orl"));
						}
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						alert(data.error ? data.error : (data ? data :'程序异常'));
					}
				},
				error:function(){
					$('.modal-scrollable').trigger('click');
					alert("数据加载失败");
				}
			};

			$('#app_user_bespoke_log_info').validate({
				errorElement: 'span', //default input error app_user_bespoke_log container
				errorClass: 'help-block', // default input error app_user_bespoke_log class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
                    remark: {
                        required: true,
						maxlength:200
                    },
				},
				messages: {
                    remark: {
                        required: "备注不能为空且不能超过200个字符."
                    },
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
					$("#app_user_bespoke_log_info").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#app_user_bespoke_log_info input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#app_user_bespoke_log_info').validate().form()) {
						$('#app_user_bespoke_log_info').submit();
					}
					else
					{
						return false;
					}
				}
			});
		}
		var initData = function(){}
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	baseMemberInfoInfogObj.init();
});