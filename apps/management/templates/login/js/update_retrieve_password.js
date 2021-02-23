$import(function(){
	UIExtendedModals.init();
	var UpdateLoginPass = function(){
		var handleUpdate = function(){
			var options1 = {
				//url
				url:'index.php?mod=management&con=Login&act=postUpRetrievePassword',
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
                                        $('#DivLocker').css('display','none');
					if(data.success == 1 ){
						util.xalert("修改成功,请重新登陆!",function(){
							window.location= data.url;
						});
					}else{
						util.error(data);
					}
				} 
			};
			//
			$('#modifypass-form').validate({
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
						equalTo:'#newPass'
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
					$("#modifypass-form").ajaxSubmit(options1);
				}
			});

			$('#modifypass-form input').keypress(function (e) {
				if (e.which == 13) {
					$('#modifypass-form').validate().form();
				}
			});		
		
		}

		return {
			init:function(){
				handleUpdate();
			}
		}
	}();

	UpdateLoginPass.init();
        $('#DivLocker').css('display','none');
});