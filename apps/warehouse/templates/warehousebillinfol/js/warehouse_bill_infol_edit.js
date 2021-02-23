function closeBillL(obj)
{
	$('body').modalmanager('loading');
	var url =$(obj).attr('data-url') ;
	var id = '<%$view->get_id()%>';
	var bill_no = '<%$view->get_bill_no()%>';
	bootbox.confirm("确定取消吗?", function(result) {
		if (result == true) {
			$.post(url,{id:id,bill_no:bill_no},function(data){
				$('.modal-scrollable').trigger('click');
				if(data.success==1){
					bootbox.alert({
						message: data.error,
						buttons: {
							ok: {
								label: '确定'
							}
						},
						animate: true,
						closeButton: false,
						title: "提示信息" ,
					});
					util.retrieveReload();
				}
				else{
					bootbox.alert({
						message: data.error,
						buttons: {
							ok: {
								label: '确定'
							}
						},
						animate: true,
						closeButton: false,
						title: "提示信息" ,
					});
					return false;
				}
			});
		}
	});
}

function downLoadEditExcel(obj){
	var order_typt = $("#nva-tab li").children('a[href="#'+getID()+'"]').html().substr(0,1);
	var goods_sn = $('#warehouse_bill_l_edit input[name="bill_no"]').val();
	var url = $(obj).attr('data-url')+'&order_id='+goods_sn+'&order_type='+order_typt;
	location.href=url;
}

function importEditJs(o){
	$('body').modalmanager('loading');//进度条和遮罩
	var goods_sn = $('#warehouse_bill_l_edit input[name="bill_no"]').val();
	$.post($(o).attr('data-url'),{goods_sn:goods_sn},function(data){
		if(data==0){
			bootbox.alert('没有响应的文件,请先在excel导入数据');
		}else{
			$('#InfoLedit').append(data);
			bootbox.alert('文件已导入成功！');
			$('#imjs').removeClass('red').addClass('green').attr('disabled','disabled');
			//下面这个函数是为了导入的html  追加到table中 只找到了值
			//var creategoodsinfo = JSON.parse($('input[name="create_goods_grid"]').val());

		}
		$('.modal-scrollable').trigger('click');//关闭遮罩
	})
}

$import(["public/js/select2/select2.min.js",
	"public/js/jquery-zero/ZeroClipboard.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",],function(){
	var info_id= '<%$info.id%>';

	var obj = function(){
		var initElements = function(){
			$('#warehouse_bill_l_edit select[name="prc_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			
			}).change(function(e){
				$(this).valid();
			});	
			
			$('#warehouse_bill_l_edit select[name="put_in_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			
			}).change(function(e){
				$(this).valid();
			});	
			
			$('#warehouse_bill_l_edit select[name="jiejia"]').select2({
				placeholder: "请选择",
				allowClear: true
			
			}).change(function(e){
				$(this).valid();
			});	
			
			$('#warehouse_bill_l_edit select[name="to_company_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			
			}).change(function(e){
				$(this).valid();
			});	
			
			$('#warehouse_bill_l_edit select[name="to_warehouse_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			
			}).change(function(e){
				$(this).valid();
			});	
		};
		//表单验证和提交
		var handleForm = function(){
			var url = 'index.php?mod=warehouse&con=WarehouseBillInfoL&act=update';
			var options1 = {
				url: url,
				error:function ()
				{
					bootbox.alert('请求超时，请检查链接');
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert("修改成功!");
						util.retrieveReload();
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						bootbox.alert(data.error ? data.error : (data ? data :'程序异常'));
					}
				},
				error:function(){
					$('.modal-scrollable').trigger('click');
					bootbox.alert("数据加载失败");
				}
			};

			$('#warehouse_bill_l_edit').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					ship_num: {
						required: true
					},
/*					prc_id: {
						required: true
					}*/
				},
				messages: {
					ship_num: {
						required: "出货单号不能为空.",
					},
/*					prc_id: {
						required: "供应商不能为空.",
					}*/
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
					$("#warehouse_bill_l_edit").ajaxSubmit(options1);
				}
			});
		};
		var initData = function(){
			util.batchCopyGoodsid('<%$view->get_id()%>','batch_copy_goodsid_l_edit');
			var url = 'index.php?mod=warehouse&con=WarehouseBillPay&act=show';
			$.post(url,{bill_id:info_id},function(data){
				if(data.success==1){
					$('#warehouse_bill_pay_e_l').html(data.content);
				}
				else{
					bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
				}
			});
		};
		var from_table = function(){
			$.ajax({
				url:"index.php?mod=warehouse&con=WarehouseBillInfoL&act=mkJson",
				dataType:"json",
				type:"POST",
				data:{'id':info_id},
				success:function(res){
					from_table_data(res.id,res.data,res.title,res.columns);
				}
			});
		}
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

