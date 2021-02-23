function closeBillC(obj)
{
	$('body').modalmanager('loading');
	var url =$(obj).attr('data-url') ;
	var id = '<%$view->get_id()%>';
	bootbox.confirm("确定取消吗?", function(result) {
		if (result == true) {
			$.post(url,{id:id},function(data){
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
		$('#warehouse_bill_c_info_edit select').select2({
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
			//批量复制货号
			util.batchCopyGoodsid('<%$view->get_id()%>','batch_copy_goodsid_c_edit');
		};
		var from_table = function(){
			//alert(33);
			$.ajax({
				url:"index.php?mod=warehouse&con=WarehouseBillInfoC&act=mkJson",
				dataType:"json",
				type:"POST",
				data:{'id':info_id},
				success:function(res) {
					from_table_data_c(res.id,res.data_bill_c,res.title,res.columns);
					//from_table_data_c_edit(res.id,res.data,res.title,res.columns , info_id);
				}
			});
			//保存值from_table_data_e
			$("body").find("#from_table_data_btn_c_edit").click(function()
			{
					if ($("#warehouse_bill_c_info_edit #from_table_data_c_edit").find("td").hasClass("htInvalid") == true)
					{
						$("#warehouse_bill_c_info_edit #from_table_data_c_edit").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
						return false;
					}
					if ($("#warehouse_bill_c_info_edit #from_company_id_c_edit").val()=="")
					{
						alert("请选择公司");
						return false;
					}
					if ($("#warehouse_bill_c_info_edit #pro_id_c_edit").val()=="")
					{
						alert("请选择加工商");
						return false;
					}
					if ($("#warehouse_bill_c_info_edit #chuku_type_edit").val()=="")
					{
						alert("请选择出库类型");
						return false;
					}
					var company=document.getElementById('from_company_id_c');
					var index=company.selectedIndex; //序号，取当前选中选项的序号
					var save = {
						//'data':$("#warehouse_bill_c_info_edit #from_table_data_c_edit").handsontable('getData'),
						'data':$("#warehouse_bill_c_info_edit #from_table_data_c").handsontable('getData'),
						'from_company_id':$("#warehouse_bill_c_info_edit #from_company_id_c").val(),
						'from_company_name':company.options[index].text,
						'bill_note':$("#warehouse_bill_c_info_edit #bill_note_c_edit").val(),
						'id':'<%$view->get_id()%>',
						'bill_no':'<%$view->get_bill_no()%>',
						'pro_id':$("#warehouse_bill_c_info_edit #pro_id_c_edit").val(),
						'chuku_type':$("#warehouse_bill_c_info_edit #chuku_type_edit").val(),
						'order_sn':$("#warehouse_bill_c_info_edit #order_sn_c_edit").val()
					};
									//	debugger;
					//alert(save);
					$.ajax({
					url:info_id?"index.php?mod=warehouse&con=WarehouseBillInfoC&act=update":"index.php?mod=warehouse&con=WarehouseBillInfoC&act=insert",
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

