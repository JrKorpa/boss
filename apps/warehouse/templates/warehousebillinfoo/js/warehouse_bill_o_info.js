function check_order(obj){
	var order_sn = $(obj).val();
	if(order_sn != ''){
		var url = "index.php?mod=warehouse&con=WarehouseBill&act=CheckOrderSn&order_sn="+order_sn;
		$.get(url , '' , function(res){
			if(res.success != 1){
				util.xalert("系统找不到该订单号:"+order_sn);
				return false;
			}
		});
	}
}

$import(["public/js/select2/select2.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",
		],function(){
	var info_id= '<%$view->get_id()%>';
//alert(info_id);
	var WarehouseBillCobj = function(){
		var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
		$('#warehouse_bill_info_o select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

			$('#warehouse_bill_info_o select[name="to_company_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function (e){
				$(this).valid();
				var _t = $(this).val();
				if (_t) {
					$.post('index.php?mod=warehouse&con=WarehouseBillInfoO&act=getTowarehouseId', {'id': _t}, function (data) {
						$('#warehouse_bill_info_o select[name="to_warehouse_id"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
						$('#warehouse_bill_info_o select[name="to_warehouse_id"]').change();
					});
				}else{
					$('#warehouse_bill_info_o select[name="to_warehouse_id"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
				}
			});
			
			
			$('#warehouse_bill_info_o input[name="order_sn"]').on('blur',function(e){
                if($(e.currentTarget).attr('readonly'))
                    return;
				$('#warehouse_bill_info_o input[name="consignee"]').val('');
				//$('#warehouse_bill_info_o input[name="rec_id"]').val('');
				var order_sn = $(this).val();
				if(order_sn == ''){
                    $('#warehouse_bill_info_o input[name="consignee"]').removeAttr('readonly');
					return;
				}else{
                    $('#warehouse_bill_info_o input[name="consignee"]').attr('readonly',true);
                }
				$.ajax({
					type:'POST',
					url:'index.php?mod=repairorder&con=AppOrderWeixiu&act=getConsignee',
					data:{'order_sn':order_sn},
					dtatType:'text',
					success:function(data){
						$('#warehouse_bill_info_o input[name="consignee"]').val(data.consignee);
						//$('#warehouse_bill_info_o input[name="rec_id"]').val(data.bc_sn);
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
				//url:"public/json/load.json",
				url:"index.php?mod=warehouse&con=WarehouseBillInfoO&act=mkJson",
				dataType:"json",
				type:"POST",
				data:{'id':info_id},
				success:function(res) {
					from_table_data_o(res.id,res.data,res.title,res.columns);
				}
			});
			//保存值from_table_data_e
			$("body").find("#from_table_data_btn_o").click(function() {
					$("#warehouse_bill_info_o #from_table_data_o").prev("p").text("")
					if ($("#warehouse_bill_info_o #from_table_data_o").find("td").hasClass("htInvalid") == true) {
						$("#warehouse_bill_info_o #from_table_data_o").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
						return false;
					}
					if ($("#warehouse_bill_info_o #to_company_id").val()=="") {
						util.xalert("请选择入库公司");
						return false;
					}
					if ($("#warehouse_bill_info_o select[name='to_warehouse_id']").val()=="") {
						util.xalert("请选择入库仓库");
						return false;
					}
					var company=document.getElementById('to_company_id');
					var index=company.selectedIndex; //序号，取当前选中选项的序号
					var save = {
						'data':$("#warehouse_bill_info_o #from_table_data_o").handsontable('getData'),
						'to_company_id':$("#warehouse_bill_info_o #to_company_id").val(),
						'to_warehouse_id':$("#warehouse_bill_info_o select[name='to_warehouse_id']").val(),
						'bill_note':$("#warehouse_bill_info_o #bill_note").val(),
						'order_sn':$("#warehouse_bill_info_o #order_sn").val(),
						'to_company_name':company.options[index].text,
					};
                    $('#from_table_data_btn_o').attr('disabled','disabled');
					$.ajax({
					url:info_id?"index.php?mod=warehouse&con=WarehouseBillInfoO&act=update":"index.php?mod=warehouse&con=WarehouseBillInfoO&act=insert",
					data:save,
					dataType:"json",
					type:"POST",
					success:function(data) {

								if(data.success == 1 ){
								$('.modal-scrollable').trigger('click');//关闭遮罩
								util.xalert('添加成功');
								util.closeTab();
								var jump_url = 'index.php?mod=warehouse&con=WarehouseBillInfoO&act=edit';
								util.buildEditTab(data.id,jump_url,84);//84编辑url id

							}else{
                                $('#from_table_data_btn_o').removeAttr('disabled');
								$('body').modalmanager('removeLoading');//关闭进度条
								bootbox.alert(data.error ? data.error : (data ? data :'程序异常'));
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
	WarehouseBillCobj.init();
});

