function user_channel_copy_check_all_channel()
{
	var ids = [];
	$('#user_channel_copy_info select[name="channel_id[]"] option').each(function(){
		if ($(this).val())
		{
			ids.push($(this).val());
		}
	});
	$('#user_channel_copy_info select[name="channel_id[]"]').select2('val',ids).change();        
}

$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'user_channel_copy_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=UserChannel&act=';//基本提交路径

	var obj = function(){
		var initElements = function(){
			$('#'+info_form_id+' select[name="user_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				$('#'+info_form_id+' select[name="channel_id[]"]').empty();
				$('#'+info_form_id+' select[name="channel_id[]"]').append('<option value=""></option>');
				var _t = $(this).select2('val');
				if (_t)
				{
					var id = $('#'+info_form_id+' input[name="id"]').val();
					$.post('index.php?mod=management&con=UserChannel&act=getChannels',{user_id:_t,id:id},function(data){
						$('#'+info_form_id+' select[name="channel_id[]"]').append(data);
					});					
				}
				else
				{
					$('#'+info_form_id+' select[name="channel_id[]"]').val([]).change();
				}

			});

			$('#'+info_form_id+' select[name="channel_id[]"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
		}

		var handleForm = function(){
			var url = info_form_base_url+'savePermission';
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
						util.xalert("授权成功!");
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
					user_id: {
						required: true
					},
					"channel_id[]":{
						required:true
					}
				},

				messages: {
					user_id: {
						required: "请选择用户."
					},
					"channel_id[]":{
						required:"请选择渠道"
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
			$('#'+info_form_id+' :reset').on('click',function(){
				$('#'+info_form_id+' select[name="user_id"]').select2("val",'').change();
			})	
		}
	
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