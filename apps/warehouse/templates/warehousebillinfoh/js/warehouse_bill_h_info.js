

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

			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}

		$('#warehouse_bill_info_h select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

		$('#warehouse_bill_info_h select[name="to_customer_id"]').select2({
			placeholder: "请选择",
			allowClear: true,
		}).change(function (e){
			$(this).valid();
			var _t = $(this).val();
			if (_t) {

				if(confirm("确定要选择该退货客户吗？"))
				 {
					$('#warehouse_bill_info_h select[name="to_customer_id"]').attr('disabled', true);
				 
				 }
								
			}else{
				if(confirm("确定要选择该退货客户吗？"))
				 {
					$('#warehouse_bill_info_h select[name="to_customer_id"]').attr('disabled', true);
				}
				
			}
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
				url:"index.php?mod=warehouse&con=WarehouseBillInfoH&act=mkJson",
				dataType:"json",
				type:"POST",
				data:{'id':info_id},
				success:function(res) {
					from_table_data_h(res.id,res.data_bill_h,res.title,res.columns);
				}
			});
			//保存值from_table_data_e
			$("body").find("#from_table_data_btn_h").click(function() {
					$("#warehouse_bill_info_h #from_table_data_h").prev("p").text("")
					if ($("#warehouse_bill_info_h #from_table_data_h").find("td").hasClass("htInvalid") == true) {
						$("#warehouse_bill_info_h #from_table_data_h").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
						return false;
					}
					
				if ($("#warehouse_bill_info_h #to_warehouse_id").val()=="") {
						//alert("请选择加工商");
						util.xalert("请选择入库仓！");
						return false;
					}
					/*
					if ($("#warehouse_bill_r_info #chuku_type").val()=="") {
						//alert("请选择出库类型");
						util.xalert("请选择出库类型");
						return false;
					}*/
					//var company=document.getElementById('to_company_id');
					//var index=company.selectedIndex; //序号，取当前选中选项的序号
					var save = {
						'data':$("#warehouse_bill_info_h #from_table_data_h").handsontable('getData'),
						'bill_note':$("#warehouse_bill_info_h [name='bill_note']").val(),
						'create_time_start':$("#warehouse_bill_info_h [name ='create_time_start']").val(),
						'to_warehouse_id':$("#warehouse_bill_info_h [name='to_warehouse_id']").val(),
						'to_customer_id':$("#warehouse_bill_info_h [name='to_customer_id']").val(),
                        'to_company_id':$("#warehouse_bill_info_h [name='to_company_id']").val()

							
					};

                    $('#from_table_data_btn_h').attr('disabled','disabled');    //锁定表单submit按钮
					$.ajax({
					url:info_id?"index.php?mod=warehouse&con=WarehouseBillInfoH&act=update":"index.php?mod=warehouse&con=WarehouseBillInfoH&act=insert",
					data:save,
					dataType:"json",
					type:"POST",
					success:function(data) {

								if(data.success == 1 ){
								$('.modal-scrollable').trigger('click');//关闭遮罩
								util.xalert('添加成功');
								//util.closeTab();
								var jump_url = 'index.php?mod=warehouse&con=WarehouseBillInfoH&act=edit';
									util.closeTab(data.x_id);
									util.buildEditTab(data.id,jump_url,data.tab_id,data.label);								
							}else{
                                $('#from_table_data_btn_h').removeAttr('disabled');     //表单解锁
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

