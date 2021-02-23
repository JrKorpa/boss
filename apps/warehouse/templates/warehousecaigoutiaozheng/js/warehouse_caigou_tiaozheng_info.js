$import("public/js/select2/select2.min.js",function(){
	var id= '<%$view->get_id()%>';
	//闭包


	var WarehouseCaigouTZInfoObj = function(){

		var initElements=function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			};
				//初始化组件
			$('#warehouse_caigou_tiaozheng_info select[name="type"],#warehouse_caigou_tiaozheng_info select[name="shuoming"]').select2({
					placeholder: "请选择",
					allowClear: true
				}).change(function(e){
					$(this).valid();
				});
			//重置
				$('#warehouse_caigou_tiaozheng_info :reset').on('click',function(){
				$('#warehouse_caigou_tiaozheng_info select[name="type"],#warehouse_caigou_tiaozheng_info select[name="shuoming"]').select2("val","");
			});
		}
		//表单验证和提交
		var handleForm = function(){
			var url = id ? 'index.php?mod=warehouse&con=WarehouseCaigouTiaozheng&act=update' : 'index.php?mod=warehouse&con=WarehouseCaigouTiaozheng&act=insert';
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
						alert(id ? "修改成功!": "添加成功!");
						$('.modal-scrollable').trigger('click');//关闭遮罩
						if (id)
						{//刷新当前页
							util.page(util.getItem('url'));
						}
						else
						{//刷新首页
							warehouse_caigou_tiaozheng_search_page(util.getItem("orl"));

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

			$('#warehouse_caigou_tiaozheng_info').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					type:{
						required:true
					},
					shuoming:{
							required:true
					}

				},

				messages: {
					type:{
						required:"请选择货品类型"
					},
					shuoming:{
							required:"请选择调价说明"
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
					//验证
					var file = $("#cgchengben_file").val();
					if (file == "")
					{
						alert("请选择上传文件");
						return false;
					}
					$("#warehouse_caigou_tiaozheng_info").ajaxSubmit(options1);
				}
			});

			$('#warehouse_caigou_tiaozheng_info input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#warehouse_caigou_tiaozheng_info').validate().form()) {
						$('#warehouse_caigou_tiaozheng_info').submit();
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
	WarehouseCaigouTZInfoObj.init();
});