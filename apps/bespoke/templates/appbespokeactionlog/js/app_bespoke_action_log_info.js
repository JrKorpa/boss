
function app_bespoke_action_log_search_page(url){
	util.page(url);
}

$import(["public/js/select2/select2.min.js"],function(){
	var app_bespoke_action_log_info_id ='<%$view->get_action_id()%>';
	var baseMemberInfoInfogObj = function(){
		var initElements = function(){};
			if (!jQuery().uniform) {
				return;
			}
			$('#app_bespoke_action_log_info select[name="member_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

		var handleForm = function(){
			//表单验证和提交
			var url = app_bespoke_action_log_info_id ? 'index.php?mod=bespoke&con=AppBespokeActionLog&act=update' : 'index.php?mod=bespoke&con=AppBespokeActionLog&act=insert';
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
						alert(app_bespoke_action_log_info_id ? "修改成功!": "添加成功!");
						util.retrieveReload();
						if (data.tab_id)
						{
							util.syncTab(data.tab_id);
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

			$('#app_bespoke_action_log_info').validate({
				errorElement: 'span', //default input error app_bespoke_action_log container
				errorClass: 'help-block', // default input error app_bespoke_action_log class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
                    member_name: {
                        required: true,
                        checkName: true
                    },
                    member_type: {
                        required: true,
                    },
                    member_phone: {
                        required: true,
                        maxlength:20,
                        digits:true
                    },
				},
				messages: {
                    member_name: {
                        required: "会员名不能为空."
                    },
                    member_type: {
                        required: "会员类型不能为空."
                    },
                    member_phone: {
                        required: "会员电话不符合规则."
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
					$("#app_bespoke_action_log_info").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#app_bespoke_action_log_info input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#app_bespoke_action_log_info').validate().form()) {
						$('#app_bespoke_action_log_info').submit();
					}
					else
					{
						return false;
					}
				}
			});
		}
		var initData = function(){

		}
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