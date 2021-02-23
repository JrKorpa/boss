$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'customer_sources_info';//form表单id 
	var info_form_base_url = 'index.php?mod=management&con=CustomerSources&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';
	var source_class= '<%$view->get_source_class()%>';
	var source_type= '<%$view->get_source_type()%>';
	var source_own_id= '<%$view->get_source_own_id()%>';
    var fenlei='<%$view->get_fenlei()%>';
	var obj = function(){
		var initElements = function(){
			$('#'+info_form_id+' select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(){
				$(this).valid();
			});
			$('#'+info_form_id+' input[name="source_code"]').change(function(){
				$(this).valid();
			});
            
            var test = $("#customer_sources_info input[name='is_enabled']:not(.toggle, .star, .make-switch),#customer_sources_info input[name='is_cod']:not(.toggle, .star, .make-switch),#customer_sources_info input[name='is_web']:not(.toggle, .star, .make-switch),#customer_sources_info input[name='is_display']:not(.toggle, .star, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
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
									customer_sources_search_page(util.getItem("orl"));
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
					source_name:{required: true},
					source_code:{required: true,checkFields:true},
					source_class:{required: true},
					source_type:{required: true},
                    fenlei:{required:true}
				},
				messages: {
					source_name:{required: "来源名称必填"},
					source_code:{required: "来源编码必填"},
					source_class:{required: "请选择分类"},
					source_type:{required: "请选择分类"},
                    fenlei:{required: "请选择客户来源"}
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
				$('#'+info_form_id+' select[name="source_class"]').select2("val",source_class).change();
				$('#'+info_form_id+' select[name="source_type"]').select2("val",source_type).change();
                $('#'+info_form_id+' select[name="source_own_id"]').select2("val",source_own_id).change();
                $('#'+info_form_id+' select[name="fenlei"]').select2("val",fenlei).change();
			});
			if (info_id)
			{//修改
				$('#'+info_form_id+' :reset').click();
			}
		
			//end
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