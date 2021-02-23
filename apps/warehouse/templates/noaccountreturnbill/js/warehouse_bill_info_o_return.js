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
		$('#warehouse_bill_info_o_return select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

			$('#warehouse_bill_info_o_return select[name="from_company_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function (e){
				$(this).valid();
				var _t = $(this).val();
				if (_t) {
					$.post('index.php?mod=warehouse&con=NoaccountReturnBill&act=getTowarehouseId', {'id': _t}, function (data) {
						$('#warehouse_bill_info_o_return select[name="from_warehouse_id"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
						$('#warehouse_bill_info_o_return select[name="from_warehouse_id"]').change();
					});
				}else{
					$('#warehouse_bill_info_o_return select[name="from_warehouse_id"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
				}
			});

            $('#warehouse_bill_info_o_return select[name="place_company_id"]').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function (e){
                $(this).valid();
                var _t = $(this).val();
                var b=new Array();
                b=_t.split("|");
                if (_t) {
                    $.post('index.php?mod=warehouse&con=NoaccountReturnBill&act=getTowarehouseId', {'id': b[0]}, function (data) {
                        $('#warehouse_bill_info_o_return select[name="place_warehouse_id"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
                        $('#warehouse_bill_info_o_return select[name="place_warehouse_id"]').change();
                    });
                }else{
                    $('#warehouse_bill_info_o_return select[name="place_warehouse_id"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
                }
            });
			
			
			$('#warehouse_bill_info_o_return input[name="order_sn"]').on('blur',function(e){
                if($(e.currentTarget).attr('readonly'))
                    return;
				$('#warehouse_bill_info_o_return input[name="consignee"]').val('');
				//$('#warehouse_bill_info_o_return input[name="rec_id"]').val('');
				var order_sn = $(this).val();
				if(order_sn == ''){
                    $('#warehouse_bill_info_o_return input[name="consignee"]').removeAttr('readonly');
					return;
				}else{
                    $('#warehouse_bill_info_o_return input[name="consignee"]').attr('readonly',true);
                }
				$.ajax({
					type:'POST',
					url:'index.php?mod=repairorder&con=AppOrderWeixiu&act=getConsignee',
					data:{'order_sn':order_sn},
					dtatType:'text',
					success:function(data){
						$('#warehouse_bill_info_o_return input[name="consignee"]').val(data.consignee);
						//$('#warehouse_bill_info_o_return input[name="rec_id"]').val(data.bc_sn);
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
				url:"index.php?mod=warehouse&con=NoaccountReturnBill&act=mkJson",
				dataType:"json",
				type:"POST",
				data:{'id':info_id},
				success:function(res) {
					from_table_data_o(res.id,res.data,res.title,res.columns);
				}
			});
			//保存值from_table_data_e
			$("body").find("#from_table_data_btn_o_return").click(function() {
					$("#warehouse_bill_info_o_return #from_table_data_o").prev("p").text("")
					if ($("#warehouse_bill_info_o_return #from_table_data_o").find("td").hasClass("htInvalid") == true) {
						$("#warehouse_bill_info_o_return #from_table_data_o").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
						return false;
					}
					if ($("#warehouse_bill_info_o_return #from_company_id").val()=="") {
						util.xalert("亲^_^，请选择入库公司");
						return false;
					}
					if ($("#warehouse_bill_info_o_return select[name='from_warehouse_id']").val()=="") {
						util.xalert("亲^_^，请选择入库仓库");
						return false;
					}
                    if ($("#warehouse_bill_info_o_return select[name='caizhi']").val()=="") {
                        util.xalert("亲^_^，请选择材质");
                        return false;
                    }
                    if ($("#warehouse_bill_info_o_return select[name='ingredient_color']").val()=="") {
                        util.xalert("亲^_^，请选择材质颜色");
                        return false;
                    }
					if ($("#warehouse_bill_info_o_return #main_stone_weight").val()=="") {
                        util.xalert("亲^_^，请输入主石重");
                        return false;
                    }
					if ($("#warehouse_bill_info_o_return #main_stone_num").val()=="") {
                        util.xalert("亲^_^，请输入主石粒数");
                        return false;
                    }
					if ($("#warehouse_bill_info_o_return #style_type").val()=="") {
                        util.xalert("亲^_^，请选择款式分类");
                        return false;
                    }
                    if ($("#warehouse_bill_info_o_return #resale_price").val()=="") {
                        util.xalert("亲^_^，请输入零售价");
                        return false;
                    }
                    if ($("#warehouse_bill_info_o_return #remark").val()=="") {
                        util.xalert("亲^_^，请输入备注");
                        return false;
                    }
					var company=document.getElementById('from_company_id');
					var index=company.selectedIndex; //序号，取当前选中选项的序号
					var save = {
						'data':$("#warehouse_bill_info_o_return #from_table_data_o").handsontable('getData'),
                        'business_type':$("#warehouse_bill_info_o_return #business_type").val(),
						'from_company_id':$("#warehouse_bill_info_o_return #from_company_id").val(),
                        'from_company_name':company.options[index].text,
						'from_warehouse_id':$("#warehouse_bill_info_o_return select[name='from_warehouse_id']").val(),
						'style_sn':$("#warehouse_bill_info_o_return #style_sn").val(),
                        'guest_name':$("#warehouse_bill_info_o_return #guest_name").val(),
                        'guest_contact':$("#warehouse_bill_info_o_return #guest_contact").val(),
                        'gold_weight':$("#warehouse_bill_info_o_return #gold_weight").val(),
                        'finger_circle':$("#warehouse_bill_info_o_return #finger_circle").val(),
                        'credential_num':$("#warehouse_bill_info_o_return #credential_num").val(),
                        'main_stone_weight':$("#warehouse_bill_info_o_return #main_stone_weight").val(),
                        'main_stone_num':$("#warehouse_bill_info_o_return #main_stone_num").val(),
                        'deputy_stone_weight':$("#warehouse_bill_info_o_return #deputy_stone_weight").val(),
                        'deputy_stone_num':$("#warehouse_bill_info_o_return #deputy_stone_num").val(),
                        'resale_price':$("#warehouse_bill_info_o_return #resale_price").val(),
                        'out_goods_id':$("#warehouse_bill_info_o_return #out_goods_id").val(),
                        'exist_account_gid':$("#warehouse_bill_info_o_return #exist_account_gid").val(),
                        'remark':$("#warehouse_bill_info_o_return #remark").val(),
						'order_sn':$("#warehouse_bill_info_o_return #order_sn").val(),
                        'torr_type':$("#warehouse_bill_info_o_return select[name='torr_type']").val(),
                        'caizhi':$("#warehouse_bill_info_o_return select[name='caizhi']").val(),
                        'ingredient_color':$("#warehouse_bill_info_o_return select[name='ingredient_color']").val(),
                        'style_type':$("#warehouse_bill_info_o_return select[name='style_type']").val(),
                        'product_line':$("#warehouse_bill_info_o_return select[name='product_line']").val(),
                        'place_company_id':$("#warehouse_bill_info_o_return select[name='place_company_id']").val(),
                        'place_warehouse_id':$("#warehouse_bill_info_o_return select[name='place_warehouse_id']").val(),
						'weixiu_fee':$("#warehouse_bill_info_o_return input[name='weixiu_fee']").val(),
					};
                    $('#from_table_data_btn_o_return').attr('disabled','disabled');
					$.ajax({
					url:info_id?"index.php?mod=warehouse&con=NoaccountReturnBill&act=update":"index.php?mod=warehouse&con=NoaccountReturnBill&act=insert",
					data:save,
					dataType:"json",
					type:"POST",
					success:function(data) {

								if(data.success == 1 ){
								$('.modal-scrollable').trigger('click');//关闭遮罩
								util.xalert('添加成功');
								util.closeTab();
								var jump_url = 'index.php?mod=warehouse&con=VirtualReturnBill&act=show';
								util.buildEditTab(data.id,jump_url,data.id,'无账维修退货单详情');//84编辑url id

							}else{
                                $('#from_table_data_btn_o_return').removeAttr('disabled');
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

