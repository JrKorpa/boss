function closeBillR(obj)
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
		$('#warehouse_bill_r_info_edit select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			
			$('#warehouse_bill_r_info_edit input[name="order_sn"]').on('blur',function(e){
                if($(e.currentTarget).attr('readonly'))
                    return;
				$('#warehouse_bill_r_info_edit input[name="consignee"]').val('');
				//$('#warehouse_bill_r_info_edit input[name="rec_id"]').val('');
				var order_sn = $(this).val();
				if(order_sn == ''){
                    $('#warehouse_bill_r_info_edit input[name="consignee"]').removeAttr('readonly');
					return;
				}else{
                    $('#warehouse_bill_r_info_edit input[name="consignee"]').attr('readonly',true);
                }
				$.ajax({
					type:'POST',
					url:'index.php?mod=repairorder&con=AppOrderWeixiu&act=getConsignee',
					data:{'order_sn':order_sn},
					dtatType:'text',
					success:function(data){
						$('#warehouse_bill_r_info_edit input[name="consignee"]').val(data.consignee);
						//$('#warehouse_bill_r_info_edit input[name="rec_id"]').val(data.bc_sn);
					}
				});
			});
			
		};
		//表单验证和提交
		var handleForm = function(){
		};
		var initData = function(){
		};
		var from_table = function(){
			//alert(33);
			$.ajax({
				url:"index.php?mod=warehouse&con=WarehouseBillInfoR&act=mkJson",
				dataType:"json",
				type:"POST",
				data:{'id':info_id},
				success:function(res) {
					from_table_data_r_edit(res.id,res.data,res.title,res.columns , info_id);
				}
			});
			//保存值from_table_data_e
			$("body").find("#from_table_data_btn_r_edit").click(function() 
			{

					if ($("#warehouse_bill_r_info_edit #from_table_data_r_edit").find("td").hasClass("htInvalid") == true) 
					{
						$("#warehouse_bill_r_info_edit #from_table_data_r_edit").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
						return false;
					}
					if ($("#warehouse_bill_r_info_edit #from_company_id_r_edit").val()=="")
					{
						alert("请选择公司");
						return false;
					}
				
					var company=document.getElementById('from_company_id_c');
					var index=company.selectedIndex; //序号，取当前选中选项的序号
					var save = {
						'data':$("#warehouse_bill_r_info_edit #from_table_data_r_edit").handsontable('getData'),
						'from_company_id':$("#warehouse_bill_r_info_edit #from_company_id_c").val(),
						'id':'<%$view->get_id()%>',
						'bill_no':'<%$view->get_bill_no()%>',
						'bill_note':$("#warehouse_bill_r_info_edit #bill_note").val(),
						'order_sn':$("#warehouse_bill_r_info_edit #order_sn").val(),
						'from_company_name':company.options[index].text
					};
					$.ajax({
					url:info_id?"index.php?mod=warehouse&con=WarehouseBillInfoR&act=update":"index.php?mod=warehouse&con=WarehouseBillInfor&act=insert",
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

