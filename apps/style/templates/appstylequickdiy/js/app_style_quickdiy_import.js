$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'app_style_quickdiy_import';//form表单id
	var info_form_base_url = 'index.php?mod=style&con=AppStyleQuickdiy&act=import_file';//基本提交路径

	var obj = function(){
		var initElements = function(){
		            
		};

		//表单验证和提交
		var handleForm = function(){
			var options1 = {
				url: info_form_base_url,
				error:function ()
				{
					util.timeout(info_form_id);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(info_form_id);
				},
				success: function(data) {
					$('#'+info_form_id+' :submit').removeAttr('disabled');
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');
						util.xalert("上传成功！",function(){
							app_style_quickdiy_search_page(util.getItem("orl"));					 
						 });
					}else{
						util.error(data);
					}
				}
			};

			$('#'+info_form_id).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
                    quickdiy_file:{required:true},
				},
				messages: {
                    quickdiy_file:{required:"上传文件必选"},
				},

				highlight: function (element) { // hightlight error inputs
					$(element).closest('.form-group').addClass('has-error'); 
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

		};
		var initData = function(){

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