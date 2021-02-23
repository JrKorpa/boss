$import('public/js/select2/select2.min.js',function(){
	var info_form_id = 're_peishi_list_info';//form表单id
	var info_form_base_url = 'index.php?mod=processor&con=PeishiList&act=';//基本提交路径

	var obj = function(){
		var initElements = function(){
				$('#'+info_form_id+' select').select2({
					placeholder: "请选择",
					allowClear: true,
				}).change(function(e){
					$(this).valid();
				});	
		};
		
		//表单验证和提交
		var handleForm = function(){
			var url ='index.php?mod=processor&con=PeishiList&act=rePeishiSave';
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
							"保存成功!",
							function(){
								util.page(util.getItem('url'));	
							}
						);
						//util.retrieveReload();
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
					'peishi_status':{
							required:true
					},
					'remark':{
							required:true
					}
				},
				messages: {
					'peishi_status':{
						required:'请选择配石状态'
					},
					'remark':{
						required:'请填写重新配石原因'
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
                 $('#'+info_form_id+' select').select2('val','');
			});		
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

