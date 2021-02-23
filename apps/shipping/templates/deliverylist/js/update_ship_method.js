$import("public/js/select2/select2.min.js",function(){
	var id = '<%$view->get_id()%>';
	//闭包
	var updateShipMethod = function(){

		var initElements=function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			}
			//初始化下拉组件
			$('#uodate_ship_method select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件
		}
		//表单验证和提交
		var handleForm = function(){
			var url = 'index.php?mod=shipping&con=DeliveryList&act=updateShipMethod&id='+id+'&a=1';
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
						bootbox.alert("修改成功");
						$('.modal-scrollable').trigger('click');//关闭遮罩
						//刷新首页
						logistics_delivery_search_page(util.getItem("orl"));

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

			$('#uodate_ship_method').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					express_id: {
						required: true,
					},
				},

				messages: {
					express_id: {
						required: "请选择快递方式.",
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
					$("#uodate_ship_method").ajaxSubmit(options1);
				}
			});

			$('#uodate_ship_method input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#uodate_ship_method').validate().form()) {
						$('#uodate_ship_method').submit();
					}
					else
					{
						return false;
					}
				}
			});
		};
		var initData=function(){
			$('#uodate_ship_method :reset').on('click',function(){
				$('#uodate_ship_method select[name="express_id"]').select2("val",'').change();
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
	updateShipMethod.init();
});