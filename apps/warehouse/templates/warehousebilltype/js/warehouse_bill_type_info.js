$import("public/js/select2/select2.min.js",function(){
	var id= '<%$view->get_id()%>';
	//闭包


	var warehousebilltypeinfoInfoObj = function(){

		var initElements=function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			}
			var test = $("#warehouse_bill_type_info input[name='is_enabled']:not(.toggle, .star, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
			var test1 = $("#warehouse_bill_type_info input[name='in_out']:not(.toggle, .star, .make-switch)");
			if (test1.size() > 0) {
				test1.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
		}
		//表单验证和提交
		var handleForm = function(){
			var url = id ? 'index.php?mod=warehouse&con=WarehouseBillType&act=update' : 'index.php?mod=warehouse&con=WarehouseBillType&act=insert';
			var options1 = {
				url: url,
				error:function ()
				{
					alert('请求超时，请检查链接');
				},
				beforeSubmit:function(frm,jq,op){
					//console.log(frm);return false;
					// debugger;
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						alert(id ? "修改成功!": "添加成功!");
						$('.modal-scrollable').trigger('click');//关闭遮罩
						if (id)
						{//刷新当前页
							util.page(util.getItem('url'));

						}
						else
						{//刷新首页
							warehouse_bill_type_search_page(util.getItem("orl"));

						}
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						alert(data.error ? data.error : (data ? data :'程序异常'));
					}
				}, 
				error:function(){
					$('.modal-scrollable').trigger('click');
					alert("数据加载失败");  
				}
			};

			$('#warehouse_bill_type_info').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					type_name: {
						required: true,
					},
							//6 位大写字母
					type_SN: {
						required: true,
						checkLetter:true
					}		
				},

				messages: {
					type_name: {
						required: "仓储单据类型名称不能为空.",
					},
					type_SN: {
						required: "仓储单据字母标识不能为空.",
						checkLetter:"只能输入字母"
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
					$("#warehouse_bill_type_info").ajaxSubmit(options1);
				}
			});

			$('#warehouse_bill_type_info input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#warehouse_bill_type_info').validate().form()) {
						$('#warehouse_bill_type_info').submit();
					}
					else
					{
						return false;
					}
				}
			});	
		};
		var initData=function(){

			
		}

		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	warehousebilltypeinfoInfoObj.init();
});