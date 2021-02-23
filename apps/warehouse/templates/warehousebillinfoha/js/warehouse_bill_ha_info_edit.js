function closeBillH(obj)
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


$import(["public/js/select2/select2.min.js",
	"public/js/jquery-zero/ZeroClipboard.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",
		],function(){
	var info_id= '<%$view->get_id()%>';

	var WarehouseBillCEditobj = function(){
		var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}


		$('#warehouse_bill_ha_info_edit select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
		};
		//表单验证和提交
		var handleForm = function(){
		};
		var initData = function(){
			util.batchCopyGoodsid('<%$view->get_id()%>','batch_copy_goodsid_o_edit');
		};
		var from_table = function(){
			//alert(33);
			$.ajax({
				url:"index.php?mod=warehouse&con=WarehouseBillInfoHA&act=mkJson",
				dataType:"json",
				type:"POST",
				data:{'id':info_id},
				success:function(res) {
					from_table_data_ha("#from_table_data_ha",res.data_bill_ha,res.title,res.columns);

					//from_table_data_ha_edit(res.id,res.data_bill_h,res.title,res.columns , info_id);
				}
			});
			//alert(121212);
			//保存值from_table_data_e
			$("body").find("#from_table_data_btn_ha_edit").click(function() 
			{

					if ($("#warehouse_bill_ha_info_edit #from_table_data_ha_edit").find("td").hasClass("htInvalid") == true) 
					{
						$("#warehouse_bill_ha_info_edit #from_table_data_ha_edit").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
						return false;
					}
					var save = {
						'data':$("#warehouse_bill_ha_info_edit #from_table_data_ha").handsontable('getData'),
						'id':info_id,
						'bill_note':$("#warehouse_bill_ha_info_edit [name='bill_note']").val(),
						'create_time_start':$("#warehouse_bill_ha_info_edit [name ='create_time_start']").val(),
						'to_customer_id':$("#warehouse_bill_info_ha [name='to_customer_id']").val()
					};
					$.ajax({
					url:info_id?"index.php?mod=warehouse&con=WarehouseBillInfoHA&act=update":"index.php?mod=warehouse&con=WarehouseBillInfoHA&act=insert",
					data:save,
					dataType:"json",
					type:"POST",
					success:function(res) {
							if (res.success == 1)
							{
								bootbox.alert({
									message: res.error,
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
							else
							{
								bootbox.alert({
									message: res.error,
									buttons: {
										ok: {  
											label: '确定'  
										}  
									},
									animate: true, 
									closeButton: false,
									title: "提示信息" ,
								});
							}
						}
					});
					return false;

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
	WarehouseBillCEditobj.init();
});

