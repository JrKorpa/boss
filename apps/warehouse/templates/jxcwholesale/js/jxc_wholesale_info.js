$import("public/js/select2/select2.min.js", function(){
	var info_form_id = 'jxc_wholesale_info';//form表单id
	var info_form_base_url = 'index.php?mod=warehouse&con=JxcWholesale&act=';//基本提交路径
	var info_id= '<%$view->get_wholesale_id()%>';

	var obj = function(){
		var initElements = function(){
		
			//下拉美化 需要引入"public/js/select2/select2.min.js"
			$('#'+info_form_id+' select[name="sign_required"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				var opt = $(this).val();
				if (opt == '1') {
					$('#'+info_form_id+' select[name="sign_company"]').attr('disabled', false);
				} else {
					console.log('aaa');
					$('#'+info_form_id+' select[name="sign_company"]').attr('disabled', true);
				}
			});	
			
			$('#'+info_form_id+' select[name="sign_company"]').select2({
				placeholder: "请选择",
				allowClear: true
			});	
			
			if ($('#'+info_form_id+' select[name="sign_required"]').val() == '0' ) {
				$('#'+info_form_id+' select[name="sign_company"]').select2('val','').attr('disabled', true);
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
								if (data._cls)
								{//查看编辑
									util.retrieveReload();//刷新查看页签
									util.syncTab(data.tab_id);//刷新数据主列表，无法定位到分页（有可能数据列表页签已经关闭，也有可能是其他对象穿透查看，所以分页函数不一定存在）
								}
								else
								{
									if (info_id)
									{//刷新当前页
										util.page(util.getItem('url'));
									}
									else
									{//刷新首页
										jxc_wholesale_search_page(util.getItem("orl"));
									}
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
					'wholesale_name':{
						required:true,
						maxlength:100
					},
					'wholesale_credit':{
						required:true,
						maxlength:20
					},
					/*
					'sign_company' : {
						required : true,
						depends : function(e) {
							return $('#'+info_form_id+' select[name="sign_required"]').val() == '1';
						}
					}*/
				},
				messages: {
					'wholesale_name':{
						required:'请输入客户编号',
						maxlength:'最多输入100个字符'
					},
					'wholesale_credit':{
						required:'请输入客户编号',
						maxlength:'最多输入20个字符'
					},
					'sign_company' : {
						required:'请选择签收公司'
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
					if ($('#'+info_form_id+' select[name="sign_required"]').val() == '1'
						&& $('#'+info_form_id+' select[name="sign_company"]').val() =='' ) {
						alert('请选择签收公司');
						return;
					}	
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
				//下拉置空
				$('#'+info_form_id+' select[name="sign_company"]').select2('val','');
				$('#'+info_form_id+' select[name="sign_required"]').select2('val','');
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