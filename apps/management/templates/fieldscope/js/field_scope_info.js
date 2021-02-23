$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'field_scope_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=FieldScope&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';
	var field_scope_info_c_id= '<%$view->get_c_id()%>';

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

			$('#'+info_form_id+' select[name="c_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				if ($(this).val())
				{
					if (!$('#'+info_form_id+' input[name="code"]').val())
					{
						var selectStr = $(this).find("option:selected").attr('mod');
						var pattern  =  /([A-Z])/g;
						if(selectStr==null){
							selectStr="";
						}
						var selectStr= selectStr.replace(pattern,"_$1").toLowerCase();

						if (selectStr.substr(0,1)=='_')
						{
							selectStr = selectStr.substr(1);
						}
						$('#'+info_form_id+' input[name="code"]').val(selectStr);
					}					
				}
				else
				{
					$('#'+info_form_id+' input[name="code"]').val('');
				}
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
									field_scope_search_page(util.getItem("orl"));
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
					label:{
						required:true,
						checkCN:true,
						maxlength:20
					},
					c_id:{
						required:true
					},
					code:{
						required:true,
						maxlength:50					
					}
				},
				messages: {
					label:{
						required:"请填写属性标识",
						maxlength:"属性标识最多20个字符"
					},
					c_id:{
						required:"请选择控制器"
					},
					code:{
						required:"请填写属性编码",
						maxlength:"属性编码最多50个字符"					
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
			$('#'+info_form_id+' :reset').on('click',function(){
				$('#'+info_form_id+' select[name="c_id"]').select2("val",field_scope_info_c_id);
			});

			if (info_id)
			{//修改
				$('#'+info_form_id+' :reset').click();
			}		
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