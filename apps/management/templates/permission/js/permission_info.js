$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'permission_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=Permission&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';
	
	var permission_info_type = '<%$view->get_type()%>';
	var permission_info_resource_id = '<%$view->get_resource_id()%>';
	var permissionInfogObj = function(){
		var initElements = function(){
			$('#permission_info select[name="type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				//$(this).removeData("previousValue").valid();  
				$(this).valid();
				$('#permission_info select[name="resource_id"]').empty();
				$('#permission_info select[name="resource_id"]').append('<option value=""></option>');
				var _t = $(this).val();
				if (_t)
				{
					$.post('index.php?mod=management&con=permission&act=getResource',{type:_t},function(data){
						$('#permission_info select[name="resource_id"]').append(data);
						if (info_id && _t==permission_info_type)
						{
							$('#permission_info select[name="resource_id"]').select2('val',permission_info_resource_id).change();
						}
						else
						{
							$('#permission_info select[name="resource_id"]').select2('val','').change();
						}
					});
				}
				else
				{
					$('#permission_info select[name="resource_id"]').select2('val',"").change();
				}
			});//validator与select2冲突的解决方案是加change事件

			$('#permission_info select[name="resource_id"]').select2({
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
									permission_search_page(util.getItem("orl"));
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
				errorElement: 'span', //default input error permission container
				errorClass: 'help-block', // default input error permission class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					name: {
						required: true
					},
					code:{
						required: true
					},
					type:{
						required: true
					},
					resource_id:{
						required: true
					}

				},
				messages: {
					name: {
						required: "权限名称必须填写."
					},
					code: {
						required: "编码必须填写."
					},
					type: {
						required: "资源类型必须填写."
					},
					resource_id: {
						required: "目标资源必须填写."
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
		var initData = function () {
			$('#permission_info :reset').on('click', function () {
				$('#permission_info select[name="type"]').select2("val", permission_info_type).change();
			})
			if (info_id) {//修改
				$('#permission_info :reset').click();
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
	permissionInfogObj.init();
});