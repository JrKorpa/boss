//取消单据
function closeBillE(obj)
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
						message : data.error,
						buttons : {
							ok : {
								label : '确定'
							}
						},
						animate : true,
						closeButton : false,
						title : "提示信息",
					});
					util.retrieveReload();
				}
				else{
					bootbox.alert({
						message : data.error,
						buttons : {
							ok : {
								label : '确定'
							}
						},
						animate : true,
						closeButton : false,
						title : "提示信息",
					});
					util.retrieveReload();
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

	var WarehouseBillEEditobj1 = function(){
		var initElements1 = function(){
			if (!jQuery().uniform) {
				return;
			}
			$('#warehouse_bill_e_info_edit select[name="from_company_id_edit"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
		};

		var from_table1 = function(){
			$.ajax({
				url:"index.php?mod=warehouse&con=WarehouseBillInfoE&act=mkJsonEdit",
				dataType:"json",
				type:"POST",
				data:{'id':info_id},
				success:function(res) {
					//alert(res.id);
					from_table_data_e_edit(res.id,res.data,res.title,res.columns);
				}
			});
			//保存值from_table_data_e
			$("body").find("#from_table_data_btn_e_edit").click(function() 
			{
					if ($("#from_table_data_e_edit").find("td").hasClass("htInvalid") == true) 
					{
						util.xalert("表单有错误信息，请更正再保存！");
						return false;
					}
					if ($("#from_company_id_edit").val()=="")
					{
						util.xalert("请选择公司");
						return false;
					}

					var from_company_id = $('#from_company_id').val();
					var from_company_name = $('#from_company_name').val();
					var save = {
						'data':$("#from_table_data_e_edit").handsontable('getData'),
						'from_company_id':from_company_id,
						'from_company_name':from_company_name,
						'bill_note':$("#bill_note_edit").val(),
						'id':'<%$view->get_id()%>',
						'bill_no':'<%$view->get_bill_no()%>'
					};
					//alert(save);
					$.ajax({
					url:info_id?"index.php?mod=warehouse&con=WarehouseBillInfoE&act=update":"index.php?mod=warehouse&con=WarehouseBillInfoE&act=insert",
					data:save,
					dataType:"json",
					type:"POST",
					success:function(res) {
							if (res.success == 1)
							{
								bootbox.alert({
									message : res.error,
									buttons : {
										ok : {
											label : '确定'
										}
									},
									animate : true,
									closeButton : false,
									title : "提示信息",
								});
								util.retrieveReload();
							}
							else
							{
								bootbox.alert({
									message : res.error,
									buttons : {
										ok : {
											label : '确定'
										}
									},
									animate : true,
									closeButton : false,
									title : "提示信息",
								});
							}
						}
					});
					return false;

			});
			//保存值from_table_data_e
			$("body").find("#warehouse_bill_info_e_check").click(function() 
			{
				$.ajax({
					url:"index.php?mod=warehouse&con=WarehouseBillInfoE&act=check",
					dataType:"json",
					type:"POST",
					data:{'id':info_id},
					success:function(res) {
						alert(res);
					}
				});
				//审核单价
				return false;
			});
		};

		var initData = function(){
			//批量复制货号
			util.batchCopyGoodsid('<%$view->get_id()%>','batch_copy_goodsid_e_edit_q');
		}
		return {
			init:function(){
				initElements1();//处理表单元素
				from_table1();
				initData();
			}
		}
	}();
	WarehouseBillEEditobj1.init();

});
