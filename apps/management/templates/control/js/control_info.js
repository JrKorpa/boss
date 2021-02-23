$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'control_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=Control&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	var con_type_id='<%$view->get_type()%>';
	var con_parent_id ='<%$view->get_parent_id()%>';
	var con_application_id ='<%$view->get_application_id()%>';
	var ControlInfo = function(){
		var initElements = function(){
			$('#'+info_form_id+' select[name="application_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

			$('#'+info_form_id+' select[name="parent_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			}).attr('readOnly',true);

			$('#'+info_form_id+' select[name="type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				var type = $(this).val();
				if(type==3)
				{
					$('#'+info_form_id+' select[name="parent_id"]').select2("val",con_parent_id).attr('readOnly',false).change();
				}
				else
				{
					$('#'+info_form_id+' select[name="parent_id"]').select2("val","").attr('readOnly',true).change();
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
									control_search_page(util.getItem("orl"));
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
						checkCN:true,
						maxlength:20
					},
					code: {
						required: true,
						checkLetter:true,
						maxlength:40
					},
					application_id:{
						required: true
					},
					type:{
						required:true
					}
				},

				messages: {
					label: {
						required: "控制器显示名称不能为空.",
						maxlength:"输入长度最多是20"
					},
					code: {
						required: "控制器名不能为空.",
						maxlength:"输入长度最多是40"
					},
					application_id:{
						required: "所属项目不能为空."
					},
					type:{
						required:"对象类型不能为空."
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
				$('#'+info_form_id+' select[name="application_id"]').select2("val",con_application_id).change();
				$('#'+info_form_id+' select[name="type"]').select2('val',con_type_id).change();
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

	ControlInfo.init();
});