$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'menu_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=Menu&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';
	
	var menu_info_group_id= '<%$view->get_group_id()%>';
	var menu_info_application_id= '<%$view->get_application_id()%>';
	var menu_info_c_id= '<%$view->get_c_id()%>';
	var menu_info_o_id= '<%$view->get_o_id()%>';
	var menu_info_icon= '<%$view->get_icon()%>';
	var menu_info_is_enabled= '<%$view->get_is_enabled()%>';
	var menu_info_type= '<%$view->get_type()%>';

	function menu_format_icon(state) {
		return '<i class="fa '+state.text+'"></i> '+state.text;
	}

	var MenuInfoObj=function(){
		var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
			$('#menu_info input[name="url"]').attr('readOnly',true);

			var test = $("#menu_info input[name='is_enabled']:not(.toggle, .star, .make-switch),#menu_info input[name='is_out']");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}

			$('#menu_info select[name="icon"]').select2({
				placeholder: "请选择",
				allowClear: true,
				formatResult: menu_format_icon,
				formatSelection: menu_format_icon,
				escapeMarkup: function(m) { return m; }
			}).change(function(e){
				$(this).valid();
			});

			$('#menu_info select[name="type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

			$('#menu_info select[name="group_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				$('#menu_info select[name="c_id"]').empty();
				$('#menu_info select[name="c_id"]').append('<option value=""></option>');
				var pid = $(this).find("option:selected").attr('pid');
				var _t = $(this).select2('val');
				if (_t)
				{
					var str = 'index.php?';
					str+="mod="+$(this).find("option:selected").attr('cls');
					$('#menu_info input[name="url"]').val(str);
					$('#menu_info input[name="application_id"]').val(pid);
					$.post('index.php?mod=management&con=menu&act=getControls',{app_id:pid},function(data){
						$('#menu_info select[name="c_id"]').append(data);
						if (_t==menu_info_group_id || pid==menu_info_application_id)
						{
							$('#menu_info select[name="c_id"]').select2("val",menu_info_c_id).change();
						}
					});
				}
				else
				{
					$('#menu_info input[name="url"]').val('');
					$('#menu_info input[name="application_id"]').val(0);
					$('#menu_info select[name="c_id"]').val(0).change();
				}
			});

			$('#menu_info select[name="c_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){

				$(this).valid();
				var pattern  =  /([A-Z])/g;
				var selectStr = $(this).find("option:selected").attr('con');
				if(selectStr==null){
					selectStr="";
				}
				var selectStr= selectStr.replace(pattern,"_$1").toUpperCase();

				if (selectStr.substr(0,1)=='_')
				{
					selectStr = selectStr.substr(1);
				}

				$('#menu_info input[name="code"]').val(selectStr);

				$('#menu_info select[name="o_id"]').empty();
				$('#menu_info select[name="o_id"]').append('<option value=""></option>');
				var _t = $(this).select2('val');
				if (_t)
				{
					var str = 'index.php?';
					str+="mod="+$('#menu_info select[name="group_id"]').find("option:selected").attr('cls');
					str+="&con="+$(this).find("option:selected").attr('con');
					$('#menu_info input[name="url"]').val(str);

					$.post('index.php?mod=management&con=menu&act=getOperations',{c_id:_t},function(data){
						$('#menu_info select[name="o_id"]').append(data);
						if (_t==menu_info_c_id)
						{
							$('#menu_info select[name="o_id"]').select2("val",menu_info_o_id).change();
						}
					});
				}
				else
				{
					$('#menu_info select[name="o_id"]').change();
				}
			});

			$('#menu_info select[name="o_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				var _t = $(this).select2('val');
				if (_t)
				{
					var str = 'index.php?';
					str+="mod="+$('#menu_info select[name="group_id"]').find("option:selected").attr('cls');
					str+="&con="+$('#menu_info select[name="c_id"]').find("option:selected").attr('con');
					str+="&act="+$(this).find("option:selected").attr('act');
					$('#menu_info input[name="url"]').val(str);
				}
				else
				{
					$('#menu_info input[name="url"]').val('');
				}
			});
		
		}

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
									menu_search_page(util.getItem("orl"));
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

			$('#menu_info').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					label: {
						required: true,
						checkName:true,
						maxlength:20
					},
					code:{
						checkField:true,
						maxlength:40
					},
					icon: {
						required: true
					},
					group_id: {
						required: true
					},
					c_id: {
						required: true
					},
					o_id:{
						required: true
					},
					type:{
						required: true
					}
				},

				messages: {
					label: {
						required: "按钮名称不能为空.",
						maxlength:"输入长度最多是20"
					},
					code:{
						required:"菜单编码不能为空",
						maxlength:"输入长度最多是40"
					},
					icon: {
						required: "图标不能为空.",
						maxlength:"输入长度最多是40"
					},
					group_id: {
						required: "菜单组不能为空."
					},
					c_id: {
						required: "所属文件不能为空."
					},
					o_id:{
						required: "请求操作不能为空."
					},
					type:{
						required: "请选择类型."
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

		var initData=function(){
			$('#menu_info :reset').on('click',function(){
				$('#menu_info select[name="icon"]').select2("val",menu_info_icon).change();
				$('#menu_info select[name="group_id"]').select2("val",menu_info_group_id).change();
				$('#menu_info select[name="type"]').select2("val",menu_info_type).change();
				
				$("#menu_info input[name='is_enabled'][value='" + menu_info_is_enabled + "']").attr("checked", "checked");

				var test = $("#menu_info input[name='is_enabled']:not(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}
			})
			if (info_id)
			{//修改
				$('#menu_info :reset').click();
			}
			
		}

		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	
	MenuInfoObj.init();
});