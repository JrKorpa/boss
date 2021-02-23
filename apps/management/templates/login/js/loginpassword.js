//加载js
$import(function(){
	var LoginPassword = function(){
		var handleLoginpass = function(){
			var options1 = {
				//url
				url:'index.php?mod=management&con=login&act=userModifyPassPost',
				error:function ()
				{
                                        util.xalert('请求超时，请检查链接',function(){
						$('#DivLocker1').css('display','none');
					});
				},
                                beforeSubmit:function(frm,jq,op){
					$('#DivLocker1 .main span').html('操作中，请稍候……');
					$('#DivLocker1').css('display','block');
				},
				//回调函数
				success: function(data) {
                                        $('#DivLocker1').css('display','none');
					if(data.success == 1 ){
						util.xalert("修改成功!",function(){
							window.location.href= data.url;
						});
					}else{
						util.error(data);
					}
				} 
			};

			$('#login-loginpassword-form').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					oldPass: {
						required: true,
						minlength:6
					},
					newPass: {
						required: true,
						minlength:6
					},
					confrimPass: {
						required: true,
						equalTo:'#login-loginpassword-form #newPass'
					}
				},

				messages: {
					oldPass: {
						required: "密码不能为空.",
						minlength:'密码必须为6位以上'
					},
					newPass: {
						required: "密码不能为空.",
						minlength:'密码必须为6位以上'
					},
					confrimPass: {
						required: "密码不能为空.",
						equalTo:'两次新密码不一致'
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
					$("#login-loginpassword-form").ajaxSubmit(options1);
				}
			});

			$('#login-loginpassword-form input').keypress(function (e) {
				if (e.which == 13) {
					$('#login-loginpassword-form').validate().form();
				}
			});
		}
		return {
			init:function(){
				handleLoginpass();
			}
		}
	}();
	UIExtendedModals.init();
	LoginPassword.init();
        $('#DivLocker1').css('display','none');
});
