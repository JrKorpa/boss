$import("public/js/select2/select2.min.js",function(){
	var rel_id= '<%$view->get_rel_id()%>';
	var cat_type_id = '<%$view->get_cat_type_id()%>';
	var attribute_id= '<%$view->get_attribute_id()%>';
	var product_type_id= '<%$view->get_product_type_id()%>';
	var is_show= '<%$view->get_is_show()%>';
	var is_default= '<%$view->get_is_default()%>';
	var is_require = '<%$view->get_is_require()%>';
	var status= '<%$view->get_status()%>';

	var Obj = function(){
		var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
			//初始化下拉框组
			$('#rel_cat_attribute_info select').select2({
				placeholder: "请选择",
				allowClear: true,
				escapeMarkup: function(m) { return m; }
			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件
			//初始化单选按钮组
			var test = $("#rel_cat_attribute_info input[type='radio']");
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
			var url = rel_id ? 'index.php?mod=style&con=RelCatAttribute&act=update' : 'index.php?mod=style&con=RelCatAttribute&act=insert';
			var options1 = {
				url: url,
				error:function ()
				{
					alert('请求超时，请检查链接');
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						//alert(rel_id ? "修改成功!": "添加成功!");
						util.xalert(rel_id ? "修改成功!": "添加成功!");
						if (rel_id)
						{//刷新当前页
							util.page(util.getItem('url'));
						}
						else
						{//刷新首页
							rel_cat_attribute_search_page(util.getItem("orl"));
							//util.page('index.php?mod=management&con=application&act=search');
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

			$('#rel_cat_attribute_info').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					cat_type_id: {
						required: true
					},
					attribute_id:{
						required:true
					},
					product_type_id:{
						required:true
					}
					
				},
				messages: {
					cat_type_id: {
						required: "分类名称不能为空."
					},
					attribute_id:{
						required: "属性不能为空."
					},
					product_type_id:{
						required: "产品线不能为空."
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
					$("#rel_cat_attribute_info").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#rel_cat_attribute_info input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#rel_cat_attribute_info').validate().form()) {
						$('#rel_cat_attribute_info').submit();
					}
					else
					{
						return false;
					}
				}
			});
		};
		var initData = function(){
			//下拉组件重置
			$('#rel_cat_attribute_info :reset').on('click',function(){
				//产品线
				$('#rel_cat_attribute_info select[name="product_type_id"]').select2("val",product_type_id);
				//分类
				$('#rel_cat_attribute_info select[name="cat_type_id"]').select2("val",cat_type_id);
				//属性
				$('#rel_cat_attribute_info select[name="attribute_id"]').select2("val",attribute_id);
				
				//状态
				$("#rel_cat_attribute_info input[name='status'][value='"+status+"']").attr('checked','checked');
				var test = $("#rel_cat_attribute_info input[name='status']:not(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}

				//是否显示
				$("#rel_cat_attribute_info input[name='is_show'][value='"+is_show+"']").attr('checked','checked');
				var test = $("#rel_cat_attribute_info input[name='is_show']:not(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}
				
				//是否默认
			    $("#rel_cat_attribute_info input[name='is_default'][value='"+is_default+"']").attr('checked','checked');
				var test = $("#rel_cat_attribute_info input[name='is_default']:not(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}

				//是否必填
				$("#rel_cat_attribute_info input[name='is_require'][value='"+is_require+"']").attr('checked','checked');
				var test = $("#rel_cat_attribute_info input[name='is_require']:not(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}



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
	Obj.init();
});