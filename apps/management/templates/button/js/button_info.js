$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'button_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=Button&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	var button_info_function_id= '<%$view->get_function_id()%>';
	var button_info_class_id= '<%$view->get_class_id()%>';
	var button_info_icon_id= '<%$view->get_icon_id()%>';
	var button_info_type_id ='<%$view->get_type()%>'
	var button_info_a_id= '<%$view->get_a_id()%>';
	var button_info_c_id= '<%$view->get_c_id()%>';
	var button_info_o_id= '<%$view->get_o_id()%>';
	var button_info_cust_function= '<%$view->get_cust_function()%>';
	var button_info_data_title= '<%$view->get_data_title()%>';
	var button_info_data_url= '<%$view->get_data_url()%>';

	function button_info_format(state) {
		return '<i class="fa '+state.text+'"></i> '+state.text;
	}

	function button_info_format1(state) {
		return '<i class="btn btn-sm '+state.text+'"></i> '+state.text;
	}

	var ButtonInfo = function(){
		var initElements = function(){
			$('#'+info_form_id+' select[name="icon_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
				formatResult: button_info_format,
				formatSelection: button_info_format,
				escapeMarkup: function(m) { return m; }
			}).change(function(e){
				$(this).valid();
			});

			$('#'+info_form_id+' select[name="class_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
				formatResult: button_info_format1,
				formatSelection: button_info_format1,
				escapeMarkup: function(m) { return m; }
			}).change(function(e){
				$(this).valid();
			});

			$('#'+info_form_id+' select[name="type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				$('#'+info_form_id+' select[name="function_id"]').attr('readOnly',false);
				$('#'+info_form_id+' select[name="function_id"]').empty();
				$('#'+info_form_id+' select[name="function_id"]').append('<option value=""></option>');

				var t_v = $(this).val();
				if(t_v){
					$.post(info_form_base_url+'listAll',{type:t_v},function(data){
						$('#'+info_form_id+' select[name="function_id"]').append(data);
						if(button_info_function_id){
							$('#'+info_form_id+' select[name="function_id"]').select2('val',button_info_function_id).change();
						}
						else
						{
							$('#'+info_form_id+' select[name="function_id"]').select2('val','').change();
						}
					});
				}
				else
				{
					$('#'+info_form_id+' select[name="function_id"]').select2('val','').attr('readOnly',false).change();
				}
			});

			$('#'+info_form_id+' select[name="function_id"]').select2({
				placeholder: "请先选择类型",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				var _t = $(this).val();
				if(_t){
					var functiontips = $('#'+info_form_id+' select[name="function_id"] > option[value="'+_t+'"]').attr('title');
					$('#functiontips').val(functiontips);
				}
				else
				{
					$('#functiontips').val('');
				}
				if (_t==21)
				{//自定义事件
					$('#'+info_form_id+' input[name="cust_function"]').val(button_info_cust_function).attr('readOnly',false);
				}
				else
				{
					$('#'+info_form_id+' input[name="cust_function"]').val('').attr('readOnly',true);
				}
	
				if (_t)
				{
					if (_t<5)
					{
						$('#'+info_form_id+' select[name="a_id"]').select2("val","").attr('readOnly',true).change();
					}
					else
					{
						$('#'+info_form_id+' select[name="a_id"]').attr('readOnly',false).change();
					}
				}
				else
				{
					$('#'+info_form_id+' select[name="a_id"]').val('').attr('readOnly',true).change();
				}
			}).attr('readOnly',true);

			$('#'+info_form_id+' input[name="cust_function"]').attr('readOnly',true);

			$('#'+info_form_id+' select[name="a_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				$('#'+info_form_id+' select[name="c_id"]').attr('readOnly',false);
				$('#'+info_form_id+' select[name="c_id"]').empty();
				$('#'+info_form_id+' select[name="c_id"]').append('<option value=""></option>');
				var _t = $(this).val();
				if (_t)
				{
					$.post(info_form_base_url+'getControls',{app_id:_t},function(data){
						$('#'+info_form_id+' select[name="c_id"]').append(data);
						if (button_info_a_id && _t==button_info_a_id)
						{
							$('#'+info_form_id+' select[name="c_id"]').select2('val',button_info_c_id).change();
						}
						else
						{
							$('#'+info_form_id+' select[name="c_id"]').change();
						}
					});
				}
				else
				{
					$('#'+info_form_id+' select[name="c_id"]').select2('val','').attr('readOnly',true).change();
				}
			}).attr('readOnly',true);

			$('#'+info_form_id+' select[name="c_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				$('#'+info_form_id+' select[name="o_id"]').attr('readOnly',false);
				$('#'+info_form_id+' select[name="o_id"]').empty();
				$('#'+info_form_id+' select[name="o_id"]').append('<option value=""></option>');
				var _t = $(this).val();
				if (_t)
				{
					if (button_info_data_title && _t==button_info_c_id)
					{
						$('#'+info_form_id+' input[name="data_title"]').val(button_info_data_title);
					}
					else
					{
						$('#'+info_form_id+' input[name="data_title"]').val($(this).find("option:selected").attr('label'));
					}
					$.post(info_form_base_url+'getOperations',{c_id:_t},function(data){
						$('#'+info_form_id+' select[name="o_id"]').append(data);
						if (button_info_c_id && _t==button_info_c_id)
						{
							$('#'+info_form_id+' select[name="o_id"]').select2("val",button_info_o_id).change();
						}
						else
						{
							$('#'+info_form_id+' select[name="o_id"]').select2("val",'').change();
						}
					});
				}
				else
				{
					$('#'+info_form_id+' select[name="o_id"]').select2("val",'').attr('readOnly',true).change();
				}
			}).attr('readOnly',true);

			$('#'+info_form_id+' select[name="o_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				var _t = $('#'+info_form_id+' select[name="o_id"]').val();
				if (_t)
				{
					var str = 'index.php?';
					str+="mod="+$('#'+info_form_id+' select[name="a_id"]').find("option:selected").attr('mod');
					str+="&con="+$('#'+info_form_id+' select[name="c_id"]').find("option:selected").attr('con');
					str+="&act="+$(this).find("option:selected").attr('act');
					$('#'+info_form_id+' input[name="data_url"]').val(str); 
				}
			}).attr('readOnly',true);
			
			setTimeout(function(){
				$('#'+info_form_id+' input[name="data_url"]').val(button_info_data_url);	   
			},500);			
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
					if($('#'+info_form_id+' select[name="function_id"]').val()==21){
						if($('#'+info_form_id+' input[name="cust_function"]').val()==''){
							util.xalert('自定义函数名必填');
							return false;
						}
					}

					if ($('#'+info_form_id+' select[name="function_id"]').val()>20)
					{
						if($('#'+info_form_id+' select[name="a_id"]').val()==''){
							util.xalert('所属项目必填');
							return false;
						}
						
						if($('#'+info_form_id+' select[name="c_id"]').val()==''){
							util.xalert('所属文件必填');
							return false;
						}
						if($('#'+info_form_id+' input[name="data_url"]').val()==''){
							util.xalert('请求地址不能为空');
							return false;
						}
					}
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
									button_search_page(util.getItem("orl"));
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
						maxlength:10,
						checkCN:true
					},
					icon_id: {
						required: true
					},
					class_id: {
						required: true
					},
					type: {
						required: true
					},
					function_id: {
						required: true
					},
					tips:{
						maxlength:20
					},
					data_title:{
						maxlength:10,
						checkName:true
					}
				},

				messages: {
					label: {
						required: "按钮名称不能为空.",
						maxlength:"最多输入10个字符"
					},
					icon_id: {
						required: "图标不能为空."
					},
					class_id: {
						required: "样式不能为空."
					},
					type: {
						required: '按钮类型必填'
					},
					function_id: {
						required: "事件不能为空."
					},
					tips:{
						maxlength:"最多输入20个字符"
					},
					data_title:{
						maxlength:"最多输入10个字符"
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

			$('#'+info_form_id+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id).validate().form();
				}
			});	
		};
		var initData = function(){
			$('#'+info_form_id+' :reset').on('click',function(){
				$('#'+info_form_id+' select[name="icon_id"]').select2("val",button_info_icon_id);
				$('#'+info_form_id+' select[name="class_id"]').select2("val",button_info_class_id);
				$('#'+info_form_id+' select[name="type"]').select2("val",button_info_type_id).change();
			});

			if (info_id)
			{//修改
				$('#'+info_form_id+' :reset').click();
			}
		}
		return {
			init:function(){//注意顺序，页面的default值设置必须在表单提交设置之后
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	ButtonInfo.init();
});