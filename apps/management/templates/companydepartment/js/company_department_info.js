$import("public/js/select2/select2.min.js",function(){
	var obj = function(){
		var initElements = function(){
			$('#company_department_info select[name="company_id[]"]').select2({
				placeholder: "请选择公司",
				allowClear: true
/*				minimumInputLength: 2*/
			}).change(function(e){
				$(this).valid();
			});


		}
		var handleForm = function(){
			var url ='index.php?mod=management&con=CompanyDepartment&act=insert';
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout('company_department_info');
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock('company_department_info');
				},
				success: function(data) {
					$('#company_department_info :submit').removeAttr('disabled');//解锁
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
						util.xalert( "添加成功!",
							function(){
								company_department_search_page(util.getItem("url"));
							}
						);
					}
					else
					{
						util.error(data);//错误处理
					}
				}
			};

			$('#company_department_info').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					user_id:{
						required: true
					}
				},

				messages: {
					user_id: {
						required: "请选择用户."
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
					$("#company_department_info").ajaxSubmit(options1);
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

	obj.init();
});