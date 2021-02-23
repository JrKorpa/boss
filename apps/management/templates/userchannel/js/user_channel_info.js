function user_channel_info_check_all_user(){
	var ids = [];
	$('#user_channel_info select[name="user_id[]"] option').each(function(){
		if ($(this).val())
		{
			ids.push($(this).val());
		}
	});
	$('#user_channel_info select[name="user_id[]"]').select2('val',ids).change();
}
function user_channel_info_check_all_channel(){
	var ids = [];
	$('#user_channel_info select[name="channel_id[]"] option').each(function(){
		if ($(this).val())
		{
			ids.push($(this).val());
		}
	});
	$('#user_channel_info select[name="channel_id[]"]').select2('val',ids).change();
}

$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'user_channel_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=UserChannel&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	var obj = function(){
		var initElements = function(){
			$('#'+info_form_id+' select').select2({
                        placeholder: "请选择",
                        allowClear: true
                    });
                    var test = $('#'+info_form_id+' input[name="auth_check"]:not(.toggle, .make-switch)');
                    if (test.size() > 0) {
                            test.each(function () {
                                    if ($(this).parents(".checker").size() == 0) {
                                            $(this).show();
                                            $(this).uniform();
                                    }
                            });
                    }
		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+(info_id ? 'update' : 'insert');
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout(info_form_id);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(info_form_id);
				},
				success: function(data) {
					$('#'+info_form_id+' :submit').removeAttr('disabled');//解锁
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
						util.xalert(
							data.data=='' ? "添加成功!": "数据量较大，请命令行导入!"+data.data,
							function(){
                                                                if (info_id)
                                                                {//刷新当前页
                                                                        util.page(util.getItem('url'));
                                                                }
                                                                else
                                                                {//刷新首页
                                                                        user_channel_search_page(util.getItem("orl"));
                                                                }
							}
						);
					}
					else
					{
						util.error(data);//错误处理
					}
				}
			};

			$('#'+info_form_id).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
                                    'user_id[]':{required:true},
                                    'channel_id[]':{required:true}
				},
				messages: {
				    'user_id[]':{required:'请选择渠道操作员'},	
				    'channel_id[]':{required:'请选择渠道'}	
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
					$("#"+info_form_id).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id).validate().form();
				}
			});
		};
		var initData = function(){
			$('#'+info_form_id+' :reset').on('click',function(){
				$('#'+info_form_id+' select[name="channel_id[]"]').val([]).change();
				$('#'+info_form_id+' select[name="user_id[]"]').val([]).change();
                                $('#'+info_form_id+' input[name="auth_check"]').attr('checked',false);
			});
		};
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	obj.init();
});