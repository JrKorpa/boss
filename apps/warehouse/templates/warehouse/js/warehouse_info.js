$import("public/js/select2/select2.min.js",function(){
	var warehouse_wid= '<%$view->get_id()%>';
	var warehouse_name = '<%$view->get_name()%>';
	var warehouse_is_detele = '<%$view->get_is_delete()%>';
	//var pid = '<%$view->get_pid()%>';
	//闭包


	var WarehouseInfoObj = function(){

		var initElements=function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			}
			var test = $("#warehouse_info input[name='is_delete']:not(.toggle, .star, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
			//初始化下拉组件
			$('#warehouse_info select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件
		}
		//表单验证和提交
		var handleForm = function(){
			var url = warehouse_wid ? 'index.php?mod=warehouse&con=Warehouse&act=update' : 'index.php?mod=warehouse&con=Warehouse&act=insert';
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
						alert(warehouse_wid ? "修改成功!": "添加成功!");
						$('.modal-scrollable').trigger('click');//关闭遮罩
						if (warehouse_wid)
						{//刷新当前页
							util.page(util.getItem('url'));
						}
						else
						{//刷新首页
							warehouse_search_page(util.getItem("orl"));

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

			$('#warehouse_info').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					name: {
						required: true,
					},
					code: {
						required: true,
					},
					pid:{
						required: true,
					}

				},

				messages: {
					name: {
						required: "仓库名称不能为空.",
					},
					code: {
						required: "仓库编号不能为空."
					},
					pid:{
						required: "请选择公司."
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
					$("#warehouse_info").ajaxSubmit(options1);
				}
			});

			$('#warehouse_info input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#warehouse_info').validate().form()) {
						$('#warehouse_info').submit();
					}
					else
					{
						return false;
					}
				}
			});
		};
		var initData=function(){
			$('#warehouse_info :reset').on('click',function(){
				$('#warehouse_info select[name="pid"]').select2("val",'').change();
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
	WarehouseInfoObj.init();
});