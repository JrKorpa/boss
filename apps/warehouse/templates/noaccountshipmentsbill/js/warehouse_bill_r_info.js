$import(["public/js/select2/select2.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",
		],function(){
	var info_id= '<%$view->get_id()%>';
	var WarehouseBillCobj = function(){
		var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
		$('#warehouse_bill_r_info select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			
			$('#warehouse_bill_r_info input[name="order_sn"]').on('blur',function(e){
                if($(e.currentTarget).attr('readonly'))
                    return;
				$('#warehouse_bill_r_info input[name="consignee"]').val('');
				//$('#warehouse_bill_r_info input[name="rec_id"]').val('');
				var order_sn = $(this).val();
				//if(order_sn == ''){
                    //$('#warehouse_bill_r_info input[name="consignee"]').removeAttr('readonly');
					//return;
				//}else{
                    //$('#warehouse_bill_r_info input[name="consignee"]').attr('readonly',true);
                //}
				$.ajax({
					type:'POST',
					url:'index.php?mod=repairorder&con=AppOrderWeixiu&act=getConsignee',
					data:{'order_sn':order_sn},
					dtatType:'text',
					success:function(data){
						$('#warehouse_bill_r_info input[name="consignee"]').val(data.consignee);
						//$('#warehouse_bill_r_info input[name="rec_id"]').val(data.bc_sn);
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
			$.ajax({
				url:"index.php?mod=warehouse&con=NoaccountShipmentsBill&act=mkJson",
				dataType:"json",
				type:"POST",
				data:{'id':info_id},
				success:function(res) {
					from_table_data_r(res.id,res.data,res.title,res.columns);
				}
			});
			//保存值from_table_data_e
			$("body").find("#from_table_data_btn_r").click(function() {
					$("#warehouse_bill_r_info #from_table_data_r").prev("p").text("")
					if ($("#warehouse_bill_r_info #from_table_data_r").find("td").hasClass("htInvalid") == true) {
						$("#warehouse_bill_r_info #from_table_data_r").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
						return false;
					}
					if ($("#warehouse_bill_r_info #from_company_id_c").val()=="") {
						util.xalert("请选择公司");
						return false;
					}
                    if ($("#warehouse_bill_r_info #bill_note").val()=="") {
                        util.xalert("请填写备注信息");
                        return false;
                    }
                    var virtual_id = $("#warehouse_bill_r_info #virtual_id").val();
                    if (virtual_id == '') {
                        util.xalert("请输入需要调拨的无帐修退流水号，批量请换行隔开");
                        return false;
                    }
				/*	if ($("#warehouse_bill_r_info #pro_id_c").val()=="") {
						//alert("请选择加工商");
						util.xalert("请选择加工商");
						return false;
					}
					if ($("#warehouse_bill_r_info #chuku_type").val()=="") {
						//alert("请选择出库类型");
						util.xalert("请选择出库类型");
						return false;
					}*/
					var company=document.getElementById('from_company_id_c');
					var index=company.selectedIndex; //序号，取当前选中选项的序号
					var save = {
						'data':$("#warehouse_bill_r_info #from_table_data_r").handsontable('getData'),
						'from_company_id':$("#warehouse_bill_r_info #from_company_id_c").val(),
						'bill_note':$("#warehouse_bill_r_info #bill_note").val(),
						'order_sn':$("#warehouse_bill_r_info #order_sn").val(),
                        'virtual_id':virtual_id,
						'from_company_name':company.options[index].text
						
					};
                    $('#from_table_data_btn_r').attr('disabled','disabled');
					$.ajax({
					url:info_id?"index.php?mod=warehouse&con=NoaccountShipmentsBill&act=update":"index.php?mod=warehouse&con=NoaccountShipmentsBill&act=insert",
					data:save,
					dataType:"json",
					type:"POST",
					success:function(data) {
								if(data.success == 1 ){
								$('.modal-scrollable').trigger('click');//关闭遮罩
								util.xalert('添加成功');
								util.closeTab();
								var jump_url = 'index.php?mod=warehouse&con=VirtualReturnBill&act=show';
								util.buildEditTab(data.id,jump_url,data.id,'无账修退发货单');//84编辑url id
							}else{
                                $('#from_table_data_btn_r').removeAttr('disabled');
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

