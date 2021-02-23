function user_warehouse_batch_info_check_all(o)
{
	$(o).attr('disabled','disabled');
	var ids = [];
	$('#user_warehouse_batch_info select[name="house_id[]"] option').each(function(){
		if ($(this).val())
		{
			ids.push($(this).val());
		}
	});
	$('#user_warehouse_batch_info select[name="house_id[]"]').select2('val',ids).change();
	$(o).removeAttr('disabled');
}



//匿名回调
$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'user_warehouse_batch_info';//form表单id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			//初始化下拉组件
			$('#'+info_form_id+' select[name="user_id[]"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});	
			$('#'+info_form_id+' select[name="company_id[]"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
                                $('#'+info_form_id+' select[name="house_id[]"]').empty();
				$('#'+info_form_id+' select[name="house_id[]"]').append('<option value=""></option>');
                                var _t = $(this).select2('val');
				if (_t)
				{
					$.post('index.php?mod=management&con=UserWarehouse&act=getCompanyHouses',{company_id:_t},function(data){
						$('#'+info_form_id+' select[name="house_id[]"]').append(data);
					});
				}
			});
                        $('#'+info_form_id+' select[name="house_id[]"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			
			var test = $('#'+info_form_id+' input[name="auth_check"]:not(.toggle, .make-switch)');
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
		};
		
		var handleForm = function(){
			var url = 'index.php?mod=management&con=UserWarehouse&act=batchInsert';

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
						util.xalert("添加成功!",
							function(){
								user_warehouse_search_page(util.getItem("orl"));
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
					user_id: {
						required: true
					},
                                        'company_id[]': {
						required: true
					},
					'house_id[]': {
						required: true
					}
				},
				messages: {
					user_id: {
						required: "请选择库管."
					},
                                        'company_id[]': {
						required: "请选择仓库所属公司."
					},
					'house_id[]': {
						required: "请选择仓库."
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
				$('#'+info_form_id+' select[name="user_id"]').select2("val",'');
				$('#'+info_form_id+' select[name="company_id[]"]').select2("val",[]).change();
				$('#'+info_form_id+' select[name="house_id[]"]').select2("val",[]);
				var test = $('#'+info_form_id+' input[name="auth_check"]:not(.toggle, .make-switch)');
				if (test.size() > 0) {
					test.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}
			})
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	obj.init();
});