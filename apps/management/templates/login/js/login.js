if( top.location !== self.location )
{
	top.location=self.location;
}
//加载js
$import(function(){
//闭包: jquery.validator校验和jquery.form提交
	var Login = function(){
		var handleLogin=function(){
			var options1 = {
				//url
				url:'index.php?mod=management&con=Login&act=login',
				error:function ()
				{
					util.xalert('请求超时，请检查链接',function(){
						$('#DivLocker').css('display','none');
					});
				},
				beforeSubmit:function(frm,jq,op){
					$('#DivLocker .main span').html('登录中，请稍候……');
					$('#DivLocker').css('display','block');
				},
				//回调函数
				success: function(data) {
					if(data.success == 0){
						$('#DivLocker').css('display','none');
						util.xalert(data.msg,function(){
							if (data.type==1 || data.type==2)
							{
								$("input[name='account']").val('');
								$("input[name='pass']").val('');
								$("input[name='account']").focus();
							}
							else
							{
								$("input[name='pass']").val('');
								$("input[name='pass']").focus();
							}						
						});

						return;
					}else if(data.success == 1){
						window.location.href = "/index.php";
						return;
					}else if(data.success == 2){
						window.location.href = "/index.php?mod=management&con=Login&act=UsermodifyPass";
						return;
					}else{
						util.xalert(data.error ? data.error : (data ? data :'程序异常'),function(){
							$('#DivLocker').css('display','none');
						});
					}
				} 
			};

			$('#login_form').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					account: {
						required: true,
						minlength:2,
						maxlength:20,
						stringCheck:true
					},
					pass: {
						required: true,
						minlength:6,
						maxlength:50
					}
				},

				messages: {
					account: {
						required: "用户名不能为空.",
						minlength: "不能少于两个字符.",
						maxlength: "不能超过20个字符."
					},
					pass: {
						required: "密码不能为空.",
						minlength:'密码必须为6位以上',
						maxlength: "不能超过50个字符."
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
					error.insertAfter(element.closest('.input-icon'));
				},

				submitHandler: function (form) {
					$("#login_form").ajaxSubmit(options1);
				}
			});

			$('#login_form input').keypress(function (e) {
				if (e.which == 13) {
					$('#login_form').validate().form();
				}
			});	
		};

		var handleForgetPassword = function(){
			var options2 = {
					//url
					url:'index.php?mod=management&con=Login&act=postRetrievePwd',
					error:function ()
					{
						util.xalert('请求超时，请检查链接',function(){
							$('#DivLocker').css('display','none');
						});
					},
					beforeSubmit:function(frm,jq,op){
						$('#DivLocker .main span').html('操作中，请稍候……');
						$('#DivLocker').css('display','block');
					},
					//回调函数
					success: function(data) {
						if(data.success == 1 ){
							$.post("index.php?mod=management&con=Login&act=retrievePwdEmail", {user_name:data.user_name,email:data.email,url:data.url},function (res, textStatus){
								$('#DivLocker').css('display','none');
								if (res.success==1)
								{
									util.xalert("邮件已发出,请查收邮件修改密码",function(){
										window.parent.location = data.url;
									});
								}
								else
								{
									util.xalert('邮件发送失败，请知会管理员！');
									return;
								}
							}, "json");
						}else{
                                                        $('#DivLocker').css('display','none');
							util.error(data);
						}
					} 
				};

				$('#login-forget-form').validate({
					errorElement: 'span', //default input error message container
					errorClass: 'help-block', // default input error message class
					focusInvalid: false, // do not focus the last invalid input
					ignore: "",
					rules: {
						user_name: {
							required: true,
							minlength:2,
							maxlength:20,
							stringCheck:true
						},
						email: {
							required: true,
							email: true,
							maxlength:60
						}
					},

					messages: {
						user_name: {
							required: "用户名不能为空.",
							minlength: "不能少于两个字符.",
							maxlength: "不能超过20个字符."
						},
						email: {
							required: "Email不能为空.",
							email:"请输入有效的邮箱地址",
							maxlength: "不能超过60个字符."
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
						error.insertAfter(element.closest('.input-icon'));
					},

					submitHandler: function (form) {
						$("#login-forget-form").ajaxSubmit(options2);
					}
				});

				$('#login-forget-form input').keypress(function (e) {
					if (e.which == 13) {
						$('#login-forget-form').validate().form();
					}
				});

				$('#forget-password').click(function () {
					$('.login-form').hide();
					$('.forget-form').show();
				});

				$('#back-btn').click(function () {
					$('.login-form').show();
					$('.forget-form').hide();
				});	
		}

		return {
			//main function to initiate the module
			init: function () {
				handleLogin();
				handleForgetPassword();
				/*$.backstretch([
					"public/img/bg/1.jpg",
					"public/img/bg/2.jpg",
					"public/img/bg/3.jpg",
					"public/img/bg/4.jpg"
					], {
					  fade: 1000,
					  duration: 8000
				});*/
			}
		}
	}();

	UIExtendedModals.init();
	Login.init();
	$('#DivLocker').css('display','none');
});