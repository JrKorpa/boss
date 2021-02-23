$import(["public/js/select2/select2.min.js"],function(){
	var info_form_id = 'change_company_form';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=main&act=';//基本提交路径

	var company_id= '<%$user.company_id%>';
	var UserInfoOjb = function(){
		var initElements = function () {
			if (!jQuery().uniform) {
				return;
			}
            $('#'+info_form_id+' select').select2({
				placeholder: "请选择",
				allowClear: false
			}).change(function(e){
				$(this).valid();
			});

		}

		var handleForm = function(){
			var url = info_form_base_url+'changeCompany';
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
							"修改成功!",
							function(){
								
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
					company_id: {
						required: true
					}
					
				},

				messages: {
					company_id: {
						required: "所在公司不能为空",
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

		}
		var initData = function(){
			$('#'+info_form_id+' :reset').on('click',function(){
				$('#'+info_form_id+' select[name="company_id"]').select2("val",company_id).change();
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