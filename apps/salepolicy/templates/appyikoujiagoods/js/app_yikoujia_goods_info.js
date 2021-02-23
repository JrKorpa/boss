$import(function(){
	var info_id= "<%$data['id']|default:''%>";

	var obj = function(){
		var initElements = function(){
			$('#app_yikoujia_goods_info select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
        };
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_id ? 'index.php?mod=salepolicy&con=AppYikoujiaGoods&act=update' : 'index.php?mod=salepolicy&con=AppYikoujiaGoods&act=insert';
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
							title: "提示信息",
							callback:function(){
								if (data._cls)
								{
									util.retrieveReload();
									util.syncTab(data.tab_id);
								}
								else
								{//刷新首页
									app_salepolicy_goods_search_page(util.getItem("orl3"));
									//util.page('index.php?mod=management&con=application&act=search');
								}
							}
						});  



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

			$('#app_yikoujia_goods_info').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					goods_sn: {
						required: true
					},
					caizhi: {
						required: true,
						number:true
					},
					small: {
						required: false,
						number:true
					},
					sbig: {
						required: false,
						number:true
					},
					price: {
						number:true
					}
					
					
				},
				messages: {
					goods_sn: {
						required: "款号不能为空."
					},
					caizhi: {
						required: "材质不能为空."
					},
					small: {
						number: "镶口最小值必须为数字",
					},
					sbig: {
						number: "镶口最大值必须为数字",
					},
					price: {
						number: "销售价必须为数字",
					},
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
					$("#app_yikoujia_goods_info").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#app_yikoujia_goods_info input').keypress(function (e) {
				if (e.which == 13) {
					$('#app_yikoujia_goods_info').validate().form()
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