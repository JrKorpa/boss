$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	var info_form_id = 'user_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=User&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	var user_info_user_type= '<%$view->get_user_type()%>';
	var user_info_gender= '<%$view->get_gender()%>';
    var user_info_internship= '<%$view->get_internship()%>';
	var user_info_is_channel_keeper= parseInt('<%$view->get_is_channel_keeper()%>');
	var user_info_is_warehouse_keeper= parseInt('<%$view->get_is_warehouse_keeper()%>');
	var UserInfoOjb = function(){
		var initElements = function () {
			if (!jQuery().uniform) {
				return;
			}
			var test = $("#user_info input[name='gender']:not(.toggle, .star, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}

            var test = $("#user_info input[name='internship']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function () {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }

			var test = $("#user_info input[name='is_warehouse_keeper']:not(.toggle, .make-switch),#user_info input[name='is_channel_keeper']:not(.toggle, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}

			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
            $('#user_info select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			/*$('#user_info select[name="user_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});*/

		}

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
						//$('.modal button.close').click();
						$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
						util.xalert(
							info_id ? "修改成功!": "添加成功!",
							function(){
								if (info_id)
								{//刷新当前页
									util.page(util.getItem('url'));
								}
								else
								{//刷新首页
									user_search_page(util.getItem("orl"));
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
//					code:{
//						required:true,
//						maxlength:10,
//						checkCode:true
//					},
					account: {
						required: true,
						maxlength:20,
						minlength:2,
						stringCheck:true
					},
					real_name:{
						required: true,
						maxlength:20,
						checkName:true
					},
					user_type: {
						required: true
					},
					email:{
						required: true,
						email:true
					},
					mobile:{
						required: true,
						isMobile:true
					},
					icd:{
						maxlength:18
						}
				},

				messages: {
//					code:{
//						required:"编码必填",
//						maxlength:"输入最大程度是10"
//					},
					account: {
						required: "帐户不能为空.",
						minlength: "不能少于两个字符.",
						maxlength:"输入最大程度是20"
					},
					real_name: {
						required: "姓名不能为空.",
						maxlength:"输入最大程度是20"
					},
					user_type: {
						required: "请选择用户类型."
					},
					email:{
						required: "请填写邮箱.",
						email: "邮箱格式不正确."
					},
					mobile:{
						required: "手机号必填.",
						isMobile:"你这号码太牛了，都能打到火星去"
					},
					icd:{
						maxlength:'身份证号有这么长吗？'
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
					$("#"+info_form_id).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id).validate().form();
				}
			});	
		}
		var initData = function(){
			$('#user_info :reset').on('click',function(){
				$('#user_info select[name="user_type"]').select2("val",user_info_user_type);
				//单选按钮组重置
				$("#user_info input[name='gender'][value='"+user_info_gender+"']").attr('checked','checked');
				var test = $("#user_info input[name='gender']:not(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}

                //单选按钮组重置
                $("#user_info input[name='internship'][value='"+user_info_internship+"']").attr('checked','checked');
                var test = $("#user_info input[name='internship']:not(.toggle, .star, .make-switch)");
                if (test.size() > 0) {
                    test.each(function () {
                        if ($(this).parents(".checker").size() == 0) {
                            $(this).show();
                            $(this).uniform();
                        }
                    });
                }

				//复选按钮重置，真他妈逗逼
				if (user_info_is_warehouse_keeper)
				{
					$("#user_info input[name='is_warehouse_keeper']").attr('checked',true);
				}
				else
				{
					$("#user_info input[name='is_warehouse_keeper']").attr('checked',false);
				}
				if (user_info_is_channel_keeper)
				{
					$("#user_info input[name='is_channel_keeper']").attr('checked',true);
				}
				else
				{
					$("#user_info input[name='is_channel_keeper']").attr('checked',false);
				}

				var test = $("#user_info input[name='is_warehouse_keeper']:not(.toggle, .make-switch),#user_info input[name='is_channel_keeper']:not(.toggle, .make-switch)");
				if (test.size() > 0) {
					test.each(function () {
						if($(this).attr('checked')=='checked')
						{
							$(this).parent().addClass('checked');
						}
						else
						{
							$(this).parent().removeClass('checked');
						}
					});
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

	UserInfoOjb.init();
});