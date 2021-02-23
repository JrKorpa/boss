$import(function(){
	var ModifyUserPassObj = function(){
		var initElements = function(){
	//		console.log('obj',$('#modify_user_pass'));
			$("#modify_pass_captcha").attr("onclick","this.src='index.php?mod=management&con=User&act=captcha&'+Math.random()");
			$("#modify_pass_captcha").click();
		}
		var handleForm = function(){
			var options1 = {
				url:'index.php?mod=management&con=User&act=setModify',
				error:function ()
				{
					util.timeout('modify_user_pass');
				},
				beforeSubmit:function(frm,jq,op){
					App.startPageLoading('操作中，请稍候……');
				},
				//回调函数
				success: function(data) {
					App.stopPageLoading();
					if(data.success == 1){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						util.xalert('设置成功');
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						util.error(data);
					}
				} 
			};

			$('#modify_user_pass').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					newPass: {
						required: true,
						minlength:6,
						maxlength:20
					},
					confirmPass: {
						required: true,
						equalTo:'#modify_user_pass input[name="newPass"]'
					},
					modify_captcha:{
						required: true
					}
				},

				messages: {
					newPass: {
						required: "密码不能为空.",
						minlength: "不能少于6个字符.",
						maxlength: "不能超过20个字符."
					},
					confirmPass: {
						required: "密码不能为空.",
						equalTo:'两次新密码不一致'
					},
					modify_captcha:{
						required: "验证码必填"
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
					$("#modify_user_pass").ajaxSubmit(options1);
				}
			});

			$('#modify_user_pass input').keypress(function (e) {
				if (e.which == 13) {
					$('#modify_user_pass').validate().form();
				}
			});	
		}
		var initData = function(){};

		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	ModifyUserPassObj.init();
});
