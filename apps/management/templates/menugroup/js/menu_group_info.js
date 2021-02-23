$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'menu_group_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=MenuGroup&act=';//基本提交路径

	var info_id= '<%$view->get_id()%>';//记录主键
	var menu_group_icon= '<%$view->get_icon()%>';
	var menu_group_is_enabled= '<%$view->get_is_enabled()%>';

	function menu_group_format(state) {
		return '<i class="fa '+state.text+'"></i> '+state.text;
	}

	var obj = function(){
		var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
			var test = $("#"+info_form_id+" input[name='is_enabled']:not(.toggle, .star, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}

			$('#'+info_form_id+' select[name="icon"]').select2({
				placeholder: "请选择",
				allowClear: true,
				formatResult: menu_group_format,
				formatSelection: menu_group_format,
				escapeMarkup: function(m) { return m; }
			});	
		};
		
		//表单验证和提交
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
					$('.modal-scrollable').trigger('click');//关闭遮罩
					if(data.success == 1 ){
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
						maxlength:10,
						checkCN:true
					}
				},
				messages: {
					label: {
						required: "分组名称不能为空.",
						maxlength:"输入长度最多是10"
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
			$('#'+info_form_id+' :reset').on('click',function(){
				$('#'+info_form_id+' select[name="icon"]').select2("val",menu_group_icon);
				$("#"+info_form_id+" input[name='is_enabled'][value='"+menu_group_is_enabled+"']").attr('checked','checked');

				var test = $("#"+info_form_id+" input[name='is_enabled']:not(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}
			})		
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