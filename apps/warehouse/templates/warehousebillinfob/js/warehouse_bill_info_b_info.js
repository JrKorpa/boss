
/** 取消单据 **/
function closeBillB(obj){
	$('body').modalmanager('loading');
	var url =$(obj).attr('data-url') ;
	var id = '<%$view_bill->get_id()%>';
	var bill_no = '<%$view_bill->get_bill_no()%>';

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
					return false;
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
	"public/js/jquery.handsontable.full.js",],function(){
	var info_id= '<%$view_bill->get_id()%>';

	var obj = function(){
		var initElements = function(){
			if(info_id){
				//$('a[href="#tab-89"]').parent().children('i').trigger('click');
				var txt = $('#nva-tab li a');
				txt.each(function(i){
					if($.trim($(this).text()) == '退货返厂单'){
						$(this).parent().children('i').trigger('click');
					}
				});
			}
		};

		//表单验证和提交
		var from_table = function(){
			$.ajax({
				url:"index.php?mod=warehouse&con=WarehouseBillInfoB&act=mkJson",
				dataType:"json",
				type:"POST",
				data:{'id':info_id},
				success:function(res) {
					from_table_data_bill_b(res.id,res.data_bill_b,res.title,res.columns);
				}
			});


			//保存值
			$("body").find("#from_table_data_info_b_btn").click(function(){
				$('body').modalmanager('loading');//进度条和遮罩
				/** 获取表单数据 **/
				var pid = $('#warehouse_bill_info_b_info select[name="pid"]').val();
				var in_warehouse_type = $('#in_warehouse_type').val();
				var from_company = $('#from_company').val();
				var kela_order_sn = $('#warehouse_bill_info_b_info input[name="kela_order_sn"]').val();
				var bill_note = $('#bill_note').val();
				var amountTotal = $("input[name=amountTotal]").val();

				if ($("#warehouse_bill_info_b_info").find("td").hasClass("htInvalid") == true) {
					$("#warehouse_bill_info_b_info").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
					return false;
				}else{
					var save = {
						'data':$("#from_table_data_bill_b").handsontable('getData'),
						'pid':pid,
						'in_warehouse_type':in_warehouse_type,
						'kela_order_sn':kela_order_sn,
						'bill_note':bill_note,
						'bill_note':bill_note,
						'id':info_id,
						'from_company':from_company,
						'amountTotal':amountTotal,
					};
                    $('#from_table_data_info_b_btn').attr('disabled','disabled');
					$.ajax({
						url: info_id ? "index.php?mod=warehouse&con=WarehouseBillInfoB&act=update" : "index.php?mod=warehouse&con=WarehouseBillInfoB&act=insert",
						data:save,
						dataType:"json",
						type:"POST",
						success:function(res) {
							$('.modal-scrollable').trigger('click');//关闭遮罩
							if (res.success == "1") {
								bootbox.alert({
									message: info_id ? '修改退货返厂单成功' : res.error,
									buttons: {
										ok: {
											label: '确定'
										}
									},
									animate: true,
									closeButton: false,
									title: "提示信息" ,
								});
								if(info_id){
									$('.modal-scrollable').trigger('click');//关闭遮罩
								}else{
									var jump_url = 'index.php?mod=warehouse&con=WarehouseBillInfoB&act=edit';
									//x_id 新生成单据的主键ID  label 新生成单据的单号
									util.closeTab(res.x_id);
									util.buildEditTab(res.x_id,jump_url,res.tab_id,res.label);
									// util.retrieveReload();
								}
							} else {
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

                    $('#from_table_data_info_b_btn').removeAttr('disabled');
				}
			});
		};

		var initData = function(){
			//批量复制货号
			util.batchCopyGoodsid('<%$view_bill->get_id()%>','batch_copy_goodsid_b_e');
			if (!jQuery().uniform) {
				return;
			}
            $('#warehouse_bill_info_b_info select[name="in_warehouse_type"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });
			$('#warehouse_bill_info_b_info select[name="from_company"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
                var _t = $(this).val();
                var b=new Array();
                b=_t.split("|");
                //console.log(b[2] == 1);
                if(b[2] == 1){//为供应商or省代公司
                    $('#warehouse_bill_info_b_info select[name="in_warehouse_type"]').select2('val',5).attr('readonly',true).change();
                }else{
                    $('#warehouse_bill_info_b_info select[name="in_warehouse_type"]').select2('val','').attr('readonly',false).change();
                }
                if(_t){
                    $.post('index.php?mod=warehouse&con=WarehouseBillInfoB&act=getCompayList', {'is_sd':b[5],'is_gys':b[3],'type_company': b[4]}, function (data) {
                        $('#warehouse_bill_info_b_info select[name="pid"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
                        $('#warehouse_bill_info_b_info select[name="pid"]').change();
                    });
                }else{
                    $('#warehouse_bill_info_b_info select[name="pid"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
                }
                
			});
            $('#warehouse_bill_info_b_info select[name="pid"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });

			var url = 'index.php?mod=warehouse&con=WarehouseBillPay&act=show&bill_type=B';
			$.post(url,{bill_id:info_id},function(data){
				if(data.success==1){
					$('#warehouse_bill_pay_e_b').html(data.content);
				}
				else{
					bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
				}
			});

		};

		return {
			init:function(){
				initElements();//处理表单元素
				from_table();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	obj.init();
});
