$import(function(){
	var info_form_id = 'dict_item_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=DictItem&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';//记录主键
	var DictItemInfoObj = function(){
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
					$('#'+info_form_id+' :submit').removeAttr('disabled');
					if(data.success == 1 )
					{
						$('.modal-scrollable').trigger('click');//关闭遮罩
						util.xalert(
							info_id ? "修改成功!": "添加成功!",
							function(){
								util.retrieveReload();
								if (data.tab_id)
								{
									util.syncTab(data.tab_id);
								}
							}
						);
					}
					else
					{
						util.error(data);
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
						maxlength:20
					},
					note:{
						maxlength:200
					}
				},

				messages: {
					label: {
						required: "枚举值不能为空.",
						maxlength:"输入长度最多是20个字符"
					},
					note:{
						maxlength:"输入长度最多是200个字符"
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
					$('#'+info_form_id).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id).validate().form()
				}
			});
		};
		var initData = function(){
		
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	DictItemInfoObj.init();
});