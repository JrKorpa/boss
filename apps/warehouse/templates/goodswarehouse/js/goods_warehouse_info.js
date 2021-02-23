$import("public/js/jquery-tags-input/jquery.tagsinput.min.js",function(){
	var info_id= '<%$view->get_id()%>';
	var warehouse_warehouse_id = '<%$view->get_warehouse_id()%>';
	var obj = function(){
		var initElements = function(){
			//初始化下拉组件
			$('#goods_warehouse_info select[name="warehouse_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function (e) {
  				$(this).valid();
				$('#goods_warehouse_info select[name="box_id"]').empty();
				$('#goods_warehouse_info select[name="box_id"]').append('<option value=""></option>');
				var _t = $(this).val();
				if (_t) {
					$.post('index.php?mod=warehouse&con=GoodsWarehouse&act=getBox', {'id': _t}, function (data) {
						$('#goods_warehouse_info select[name="box_id"]').append(data);
					});
					$('#goods_warehouse_info select[name="box_id"]').change();
				}
			});


			$('#goods_warehouse_info select[name="box_id"]').select2({
			    placeholder: "请选择",
			    allowClear: true,
			});
		};

		//表单验证和提交
		var handleForm = function(){
			var url = info_id ? 'index.php?mod=warehouse&con=GoodsWarehouse&act=update' : 'index.php?mod=warehouse&con=GoodsWarehouse&act=insert';
			var options1 = {
				url: url,
				error:function ()
				{
					$('.modal-scrollable').trigger('click');
					bootbox.alert('请求超时，请检查链接');
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						if (info_id)
						{//刷新当前页
							util.page(util.getItem('url'));
						}
						else
						{//刷新首页
							goods_warehouse_search_page(util.getItem("orl"));
							//util.page('index.php?mod=management&con=application&act=search');
						}
						bootbox.alert(info_id ? " 修改成功!": "添加成功!");
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						bootbox.alert(data.error ? data.error : (data ? data :'程序异常'));
					}
				}
			};

			$('#goods_warehouse_info').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {

				},
				messages: {

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
					$("#goods_warehouse_info").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#goods_warehouse_info input').keypress(function (e) {
				if (e.which == 13) {
					$('#goods_warehouse_info').submit();
				}
				// $('.modal-scrollable').trigger('click');//关闭遮罩
			});

		};
		var initData = function(){
			//下拉重置
			$('#goods_warehouse_info :reset').on('click',function(){
				$('#goods_warehouse_info select[name="warehouse_id"]').select2("val",warehouse_warehouse_id).change();
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