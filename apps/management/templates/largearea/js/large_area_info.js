$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'large_area_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=LargeArea&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	var large_area_info_parent_id = '<%$view->get_parent_id()%>';
	var obj = function(){
		var initElements = function(){
			$('#'+info_form_id+' select[name="parent_id"]').select2({
				placeholder: "默认顶级",
				allowClear: true
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
									util.page(util.getItem('url'));
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
				errorElement: 'span', //default input error largearea container
				errorClass: 'help-block', // default input error largearea class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					name:{
                        required: true,
						checkCN:true
					}
				},
				messages: {
					name:{
                        required:"大区名称必填写."
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
		var initData = function(){
			$('#'+info_form_id+' :reset').on('click',function(){
				$('#'+info_form_id+' select[name="parent_id"]').select2("val",large_area_info_parent_id);
			})
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	obj.init();
});




