$import(['public/js/select2/select2.min.js','public/js/jquery.validate.extends.js'],function(){
	var info_id= "<%$view->getField('id')%>";
    var good_type = "<%$view->getField('good_type')%>";

	var obj = function(){
		var initElements = function(){
			$('#diachannel_jiajialv_info select').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });
			//下拉组件重置
			$('#diachannel_jiajialv_info :reset').on('click',function(){
				$('#diachannel_jiajialv_info select').select2("val",'');
				$('#diachannel_jiajialv_info input').val('');
			})
			
		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_id ? 'index.php?mod=diamond&con=DiaChannelJiajialv&act=edit&ispost=1' : 'index.php?mod=diamond&con=DiaChannelJiajialv&act=add&ispost=1';
			var options1 = {
				url: url,
				error:function ()
				{
					$('.modal-scrollable').trigger('click');
					bootbox.alert({   
						message: "请求超时，请检查链接",
						buttons: {  
								   ok: {  
										label: '确定'  
									}  
								},
						animate: true, 
						closeButton: false,
						title: "提示信息" 
					});
					return;
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert({   
							message: info_id ? "修改成功!": "添加成功!",
							buttons: {  
									   ok: {  
											label: '确定'  
										}  
									},
							animate: true, 
							closeButton: false,
							title: "提示信息" 
						});  

						if (data._cls)
						{
							util.retrieveReload();
							util.syncTab(data.tab_id);
						}
						else
						{//刷新首页
							diachannel_jiajialv_search_page(util.getItem("orl"));
							//util.page('index.php?mod=management&con=application&act=search');
						}

					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						bootbox.alert({   
							message: data.error ? data.error : (data ? data :'程序异常'),
							buttons: {  
									   ok: {  
											label: '确定'  
										}  
									},
							animate: true, 
							closeButton: false,
							title: "提示信息" 
						});
						return;
					}
				}
			};

			$('#diachannel_jiajialv_info').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					channel_id :{
						required: true
					},
					carat_min: {
						required: true,
						number:true
					},
					carat_max: {
						required: true,
						number:true
					},
					jiajialv: {
						required: true,
						number:true
					},
					good_type: {
						required: true
					},
					cert:{
						required: true
					}
				},
				messages: {
					
					channel_id : {
						required: '销售渠道不能为空.'
					},
					
					carat_min: {
                        required: "最小钻重不能为空.",
						number:"最小钻重只能填数字."
					},
					carat_max: {
                        required: "最大钻重不能为空.",
						number:"最大钻重只能填数字."
					},
					jiajialv: {
                        required: "加价不能为空.",
						number:"加价只能填数字."
					},
					good_type: {
                        required: "货品类型不能为空."
					},
					cert: {
						required: '证书类型不能为空.'
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
					$("#diachannel_jiajialv_info").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#diachannel_jiajialv_info input').keypress(function (e) {
				if (e.which == 13) {
					$('#diachannel_jiajialv_info').validate().form()
				}
			});
		};
		var initData = function(){
			//下拉组件重置
			$('#diachannel_jiajialv_info :reset').on('click',function(){
				$('#diachannel_jiajialv_info select[name="good_type"]').select2("val",[]);
			})	
		
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