$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'application_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=Application&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	var application_info_icon = '<%$view->get_icon()%>';
	var application_info_is_enabled = '<%$view->get_is_enabled()%>';
	
	//闭包
	function format_application_info_icon(state) {
		return '<i class="fa '+state.text+'"></i> '+state.text;
	}

	var ApplicationInfoObj = function(){
		var initElements = function(){
			//初始化单选按钮组
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

			//初始化下拉组件
			$('#'+info_form_id+' select[name="icon"]').select2({
				placeholder: "请选择",
				allowClear: true,
				formatResult: format_application_info_icon,
				formatSelection: format_application_info_icon,
				escapeMarkup: function(m) { return m; }
			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件	
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
					$('#'+info_form_id+' :submit').removeAttr('disabled');//解锁
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
						//$('body').modalmanager('removeLoading');//关闭进度条
						util.xalert(
							info_id ? "修改成功!": "添加成功!",
							function(){
								if (info_id)
								{//刷新当前页
									util.page(util.getItem('url'));
								}
								else
								{//刷新首页
									application_search_page(util.getItem("orl"));
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
					},
					code: {
						required: true,
						maxlength:40,
						checkLetter:true
					}
				},
				messages: {
					label: {
						required: "项目名称不能为空.",
						maxlength:"输入长度最多是10"
					},
					code: {
						required: "项目文件夹不能为空.",
						maxlength:"输入长度最多是40"
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
			//下拉组件重置
			$('#'+info_form_id+' :reset').on('click',function(){
				$('#'+info_form_id+' select[name="icon"]').select2("val",application_info_icon);
				$("#"+info_form_id+" input[name='is_enabled'][value='"+application_info_is_enabled+"']").attr('checked','checked');

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
	ApplicationInfoObj.init();
});