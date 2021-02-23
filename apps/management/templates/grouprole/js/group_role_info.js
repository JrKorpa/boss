$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'group_role_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=GroupRole&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	var obj = function(){
		var initElements = function(){
			$('#group_role_info select[name="role_id[]"]').select2({
				placeholder: "请选择",
				allowClear: true,
				minimumInputLength: 2
			}).change(function(e){
				$(this).valid();
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
					$('#group_role_info :submit').removeAttr('disabled');//解锁
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
						util.xalert(
							info_id ? "修改成功!": "添加成功!",
							function(){
								group_role_search_page(util.getItem("orl"));
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
					'role_id[]':{
						required:true
					}	
				},
				messages: {
					'role_id[]':{
						required:'请选择角色'
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
			$('#group_role_info :reset').on('click',function(){
				$('#group_role_info select[name="role_id[]"]').select2("val","");
			})	
		}
	
		return {
			init:function(){//注意顺序，页面的default值设置必须在表单提交设置之后
				initElements();
				handleForm();
				initData();
			}
		}	
	}();
	obj.init();
});