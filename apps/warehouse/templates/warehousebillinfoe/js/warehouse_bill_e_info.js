$import(["public/js/select2/select2.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",
		],function(){
	var info_id= '<%$view->get_id()%>';

	var WarehouseBillEobj = function(){
		var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
			$('#warehouse_bill_e_info select[name="from_company_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				//$(this).prop("disabled", true);//改变后禁止选择 true为禁止，false为非禁止
			});
			$('#from_company_btn').on('click',function(){
       $('#warehouse_bill_e_info select[name="from_company_id"]').prop("disabled", true);
      });
			
		};
		//表单验证和提交
		var handleForm = function(){
		};
		var initData = function(){
			//重置操作
      $('#warehouse_bill_e_info :reset').on('click',function(){
       $('#warehouse_bill_e_info select[name="from_company_id"]').select2('val','').change();
      });
		};
		var from_table = function(){
			$.ajax({
				url:"index.php?mod=warehouse&con=WarehouseBillInfoE&act=mkJson",
				dataType:"json",
				type:"POST",
				data:{'id':info_id},
				success:function(res) {
					// alert(res.id);
					from_table_data_e(res.id,res.data,res.title,res.columns);
				}
			});
			//保存值from_table_data_e
			$("body").find("#from_table_data_btn_e").click(function()
			{
				//var id = <%$view->get_id()%>;
				$("#from_table_data_e").prev("p").text("");
				if ($("#from_table_data_e").find("td").hasClass("htInvalid") == true)
				{
					$("#from_table_data_e").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
					return false;
				}
				if ($("#from_company_id").val()=="")
				{
					//alert("请选择公司");
					util.xalert("请选择公司");
					return false;
				}
				var company=document.getElementById('from_company_id');
				var index=company.selectedIndex; //序号，取当前选中选项的序号
				var save = {
					'data':$("#from_table_data_e").handsontable('getData'),
					'from_company_id':$("#from_company_id").val(),
					'bill_note':$("#bill_note").val(),
					'id':'<%$view->get_id()%>',
					'bill_no':'<%$view->get_bill_no()%>'
				};

                $('#from_table_data_btn_e').attr('disabled','disabled');    //锁定提交按钮

				$.ajax({
				url:info_id?"index.php?mod=warehouse&con=WarehouseBillInfoE&act=update":"index.php?mod=warehouse&con=WarehouseBillInfoE&act=insert",
				data:save,
				dataType:"json",
				type:"POST",
				success:function(data) {
						if(data.success == 1 ){
							$('.modal-scrollable').trigger('click');//关闭遮罩
							util.xalert('添加成功');
							util.closeTab();
							var jump_url = 'index.php?mod=warehouse&con=WarehouseBillInfoE&act=edit';
							util.buildEditTab(data.id,jump_url,84);//84编辑url id
						}else{
                            $('#from_table_data_btn_e').removeAttr('disabled');       //解锁submit按钮
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
	WarehouseBillEobj.init();
});

