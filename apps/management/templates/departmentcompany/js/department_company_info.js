$import("public/js/select2/select2.min.js",function(){
	var obj = function(){
		var initElements = function(){
			$('#department_company_info select[name="department_id[]"]').select2({
				placeholder: "请选择公司",
				allowClear: true
				/*				minimumInputLength: 2*/
			}).change(function(e){
				$(this).valid();
			});


		}
		var handleForm = function(){
			var url ='index.php?mod=management&con=DepartmentCompany&act=insert';
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
						alert("添加成功!");
						$('.modal-scrollable').trigger('click');//关闭遮罩
						department_company_search_page(util.getItem("url"));
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

			$('#department_company_info').validate({
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
					$("#department_company_info").ajaxSubmit(options1);
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