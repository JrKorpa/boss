$import("public/js/jquery.validate.extends.js",function(){
	var info_form_id = 'company_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=Company&act=';//基本提交路径
	var info_id ='<%$view->get_id()%>';
	var obj = function(){
		var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
			var test = $("#company_info input[type='radio']");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
			
			$('#company_info select').select2({
					placeholder: "请选择",
					allowClear: true
				}).change(function(e){
					$(this).valid();
			});
		};
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
									company_search_page(util.getItem("orl"));
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
					company_name: {
						required: true,
						maxlength:255,
						checkCN:true
					},
					company_sn: {
						required: true,
						checkFields:true,
						maxlength:255
					},
					contact:{
						required: true,
						maxlength:5
					},
					phone:{
						required: true,
						maxlength:100,
						isPhone:true
					},
					address:{
						required: true,
						maxlength:255
					},
					account:{
						required: true,
						maxlength:30,
						is_Num:true
					},

					bank_of_deposit:{
						required: true,
						maxlength:40
					},
					remark:{
						maxlength:100
					}

				},
				messages: {
					company_name: {
						required: "公司名称不能为空.",
						maxlength:"公司名称不能超过255个字"
					},
					company_sn: {
						required: "公司编号不能为空.",
						maxlength:"公司编号不能超过255"
					},
					contact:{
						required:"联系人不能为空",
						maxlength:"联系人输入过长"
					},
					phone:{
						required:"电话必须填写",
						maxlength:"电话输入过长"
					},
					address:{
						required: "公司地址必须填写",
						maxlength:"长度不能超过255"
					},
					account:{
						required:"开户账号必须填写",
						maxlength:"开户账号过长"
					},

					bank_of_deposit:{
						required: "开户银行地址必须填写",
						maxlength:"开户银行地址过长"
					},
					remark:{
						maxlength:"备注过长"
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
		};
		var initData = function(){
		
		};
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	obj.init();
});