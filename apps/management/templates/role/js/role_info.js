$import("public/js/jquery.validate.extends.js",function(){
	var info_form_id = 'role_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=Role&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	var obj = function(){
		var initElements = function(){};
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
									role_search_page(util.getItem("orl"));
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
					label: {
						required: true,
						checkName:true,
						maxlength:40
					},
					code: {
						required: true,
						checkField:true,
						maxlength:40
					},
					note:{
						maxlength:250
					}
				},
				messages: {
					label: {
						required: "角色名不能为空.",
						maxlength:"输入的最大长度是40个汉字"
					},
					code: {
						required: "编号不能为空.",
						maxlength:"输入的最大长度是40个字符"
					},
					note:{
						maxlength:"输入的最大长度是250个字符"
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