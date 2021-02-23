$import(function(){
	var ModifyPass = function(){
		var initElements = function(){};
		var handleForm = function(){
			var options = {
				//url
				url:'?mod=management&con=main&act=modifyPass',
				error:function ()
				{
					$('.modal-scrollable').trigger('click');
					bootbox.alert('请求超时，请检查链接');
				},
				//回调函数
				success: function(data) {
					if(data.success == 1 ){
						bootbox.alert("修改成功,下次登陆请使用新密码!");
						$('.modal-scrollable').trigger('click');
						$('.modal').hide();
					}else{
						bootbox.alert(data.error ? data.error : (data ? data :'程序异常'));
					}
				} 
			};
			$('.changemypass-form').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				ignore: "",
				rules: {
					newPass: {
						required: true,
						minlength:6
					},
					confrimPass: {
						required: true,
						equalTo:'.changemypass-form #newPass'
					}
				},

				messages: {
					newPass: {
						required: "密码不能为空.",
						minlength:'密码必须为6位以上'
					},
					confrimPass: {
						required: "密码不能为空.",
						equalTo:'两次密码不一致'
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
					$(".changemypass-form").ajaxSubmit(options);
				}
			});

			$('.changemypass-form input').keypress(function (e) {
				
				if (e.which == 13) {
					$('.changemypass-form').validate().form();
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
	ModifyPass.init();
});