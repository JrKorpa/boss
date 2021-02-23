$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'button_function_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=ButtonFunction&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';
	
	var buttion_function_type = '<%$view->get_type()%>';
	var ButtonFunctionInfoObj = function(){
		var initElements = function(){
			$('#'+info_form_id+' select[name="type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
		};
		var handleForm=function(){
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
							info_id ? "修改成功!": "添加成功!",
							function(){
								util.page(util.getItem('url'));
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
					name: {
						required: true,
						maxlength:40,
						checkField:true
						
					},
					label: {
						required: true,
						maxlength:10,
						checkCN:true
					},
					type:{
						required:true
					},
					tips:{
						maxlength:200
					}
				},

				messages: {
					name: {
						required: "方法名不能为空.",
						maxlength:"输入长度最多是40"
					},
					label: {
						required: "显示值不能为空.",
						maxlength:"输入长度最多是10"
					},
					type:{
						required:"方法的类型不能为空."
					},
					tips:{
						maxlength:'输入长度最多是200'
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
			$('#'+info_form_id+' select[name="type"]').select2('val',buttion_function_type);
			$('#'+info_form_id+' :reset').on('click',function(){
				$('#'+info_form_id+' select[name="type"]').select2("val",buttion_function_type);

			})
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	ButtonFunctionInfoObj.init();
});
