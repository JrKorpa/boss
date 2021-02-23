var warehouse_bill_t_add_user_id = '<%$smarty.session.userId%>';
function downLoadExcel_t(o){
	var url = $(o).attr('data-url')+"&user_id="+ warehouse_bill_t_add_user_id;
	location.href = url;
}

function importJs_t(o){
	$('body').modalmanager('loading');//进度条和遮罩
	$.post($(o).attr('data-url'),{user_id:warehouse_bill_t_add_user_id},function(data){
		if(data==0){
			bootbox.alert('没有响应的文件,请先在excel导入数据');
		}else{
			$('#InfoT').append(data);
			bootbox.alert('文件已导入成功！');
			document.getElementById("goods_num").value = $("input[name='create_goods_cnt']").val();
			document.getElementById("goods_total").value = $("input[name='create_goods_num']").val();
			$('#imjs').removeClass('red').addClass('green').attr('disabled','disabled')
		}
		$('.modal-scrollable').trigger('click');//关闭遮罩
	})
}

$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	var obj = function(){
		var initElements = function(){
			//时间选择器 需要引入
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}

			$('#warehouse_bill_t_add select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

		}
		var initData = function(){
			$('#warehouse_bill_t_add :reset').on('click',function(){
				$('#warehouse_bill_t_add select').select2('val','');
			});
			//var _id = $("#nva-tab li").children('a[href="#'+getID()+'"]').siblings('i').attr('data-id').split('-').pop();
			//$("#warehouse_bill_t_add input[name='tab_id']").val(_id);
		}
		var handleForm = function(){
			var url = 'index.php?mod=warehouse&con=WarehouseBillInfoT&act=insert';
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout("warehouse_bill_t_add");
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock("warehouse_bill_t_add");
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						util.xalert('添加成功'+data.infomsg,function(){
							$('#warehouse_bill_t_add :submit').attr('disabled','disabled');
							var jump_url = 'index.php?mod=warehouse&con=WarehouseBillInfoT&act=edit';
							util.closeTab(data.x_id);
							util.buildEditTab(data.x_id,jump_url,data.tab_id,data.label);//这里84的作用是刷新单据列表页的list内容如果没有 就不刷新
						});

					}else{
						$('#warehouse_bill_t_add :submit').attr('disabled',false);
						util.error(data);//错误处理
					}
				}
			};
			$('#warehouse_bill_t_add').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					ship_num: {
						required: true
					}
				},
				messages: {
					ship_num: {
						required: "出货单号不能为空."
					}
				},

				highlight: function (element) { // hightlight error inputs
					$(element).closest('.form-group').addClass('has-error'); // set error class to the control group
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
					$("#warehouse_bill_t_add").ajaxSubmit(options1);
				}
			});

			//回车提交
			$('#warehouse_bill_t_add input').keypress(function (e) {
				if (e.which == 13) {
					$('#warehouse_bill_t_add').validate().form();
				}
			});
		}
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	obj.init();

});