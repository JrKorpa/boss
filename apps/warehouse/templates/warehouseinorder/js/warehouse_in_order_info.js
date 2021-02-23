$import(["public/js/select2/select2.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",
	"public/js/table_data_ed.js",],function(){
	var info_id= '<%$view->get_id()%>';

	var obj = function(){
		var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
			$('#warehouse_in_order_info select[name="put_in_type"],#warehouse_in_order_info select[name="order_type"],#warehouse_in_order_info select[name="company_id"],#warehouse_in_order_info select[name="prc_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
		};
		//表单验证和提交
		var handleForm = function(){
			var url = info_id ? 'index.php?mod=warehouse&con=WarehouseInOrder&act=update' : 'index.php?mod=warehouse&con=WarehouseInOrder&act=insert';
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
						alert(info_id ? "修改成功!": "添加成功!");
						if (info_id)
						{//刷新当前页
							util.page(util.getItem('url'));
						}
						else
						{//刷新首页
							warehouse_in_order_search_page(util.getItem("orl"));
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

			$('#warehouse_in_order_info').validate({
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
					$("#warehouse_in_order_info").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#warehouse_in_order_info input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#warehouse_in_order_info').validate().form()) {
						$('#warehouse_in_order_info').submit();
					}
					else
					{
						return false;
					}
				}
			});
		};
		var initData = function(){
			$('#warehouse_in_order_info select[name="order_type"]').change(function(){
				//alert('123');
				var order_type = $(this).val();
				if(order_type == 2){
					var order_no = $('#warehouse_in_order_info input[name="order_no"]').val();
					var rs = /RS/g;
					var new_order_no = order_no.replace(rs,"AS");
					$('#warehouse_in_order_info input[name="order_no"]').val(new_order_no)
				}else if(order_type == 1){
					var order_no = $('#warehouse_in_order_info input[name="order_no"]').val();
					var rs = /AS/g;
					var new_order_no = order_no.replace(rs,"RS");
					$('#warehouse_in_order_info input[name="order_no"]').val(new_order_no)
				}
			});
		};

		var from_table = function(){
			$.ajax({
				//url:"public/json/load.json",
				url:"index.php?mod=warehouse&con=WarehouseInOrder&act=mkJson",
				dataType:"json",
				type:"POST",
				data:{'id':info_id},
				success:function(res) {
					from_table_data(res.id,res.data,res.title,res.columns);
				}
			});
			//保存值
			$("body").find("#from_table_data_btn").click(function() {
				if ($("#from_table_data").find("td").hasClass("htInvalid") == true) {
					$("#from_table_data").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
					return false;
				}else{
					$("#from_table_data").prev("p").addClass('text-success').text("保存成功");
					//console.log($("#from_table_data").handsontable('getData'));
					var save = {'data':$("#from_table_data").handsontable('getData')};
					$.ajax({
					    url:"index.php?mod=warehouse&con=WarehouseInOrder&act=getJson",
					    data:save,
					    dataType:"json",
					    type:"POST",
					    success:function(res) {
					      //if (res.result === "ok") {
					      //  console.text("数据保存");
					      //} else {
					      //  console.text("保存错误");
					      //}
					    }
					});
				}
			});
		};
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
				from_table();
			}
		}
	}();
	obj.init();
});

