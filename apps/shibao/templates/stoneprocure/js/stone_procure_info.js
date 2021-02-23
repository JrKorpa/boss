$import(["public/js/select2/select2.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",
],function(){
	var info_form_id = 'stone_procure_info';//form表单id
	var info_form_base_url = 'index.php?mod=shibao&con=StoneProcure&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	var obj = function(){
		var initElements = function(){
			//单选美化
			//var test = $("#"+info_form_id+" input[type='radio']:not(.toggle, .star, .make-switch)");
			//if (test.size() > 0) {
			//	test.each(function () {
			//		if ($(this).parents(".checker").size() == 0) {
			//			$(this).show();
			//			$(this).uniform();
			//		}
			//	});
			//}
			//复选美化
//			var test = $("#"+info_form_id+" input[type='checkbox']:not(.toggle, .make-switch)");
//			if (test.size() > 0) {
//				test.each(function () {
//					if ($(this).parents(".checker").size() == 0) {
//						$(this).show();
//						$(this).uniform();
//					}
//				});
//			}
			//时间选择器 需要引入"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"
//			if ($.datepicker) {
//				$('.date-picker').datepicker({
//					format: 'yyyy-mm-dd',
//					rtl: App.isRTL(),
//					autoclose: true
//				});
//				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
//			}
			//下拉美化 需要引入"public/js/select2/select2.min.js"
			$('#'+info_form_id+' select').select2({
				placeholder: "请选择",
				allowClear: true,
//				minimumInputLength: 2
			}).change(function(e){
				$(this).valid();
			});

			$('#stone_procure_close_btn').click(function(){
				//$('.modal-scrollable').trigger('click');
				util.closeTab();
			});

		};

		var mk_table = function () {
			$.ajax({
				url : info_form_base_url+"mkJson",
				dataType : "json",
				type : "POST",
				data : {
					'id' : info_id
				},
				success : function(res){
					from_table_data_stone_p(res.id,res.data,res.title,res.columns);
				}
			});
			//保存值
			$("body").find("#stone_procure_info_submit_btn").click(function(){
				/** 获取表单数据 **/
				var pro_type = $('#stone_procure_info select[name="pro_type"]').val();
				var pro_ct = $('#stone_procure_info input[name="pro_ct"]').val();
				var is_batch = $('#stone_procure_info select[name="is_batch"]').val();
				var note = $('#stone_procure_info input[name="note"]').val();
				if ($("#stone_procure_detail").find("td").hasClass("htInvalid") == true) {
					$("#stone_procure_detail").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
					return false;
				}else{
					var data = $("#stone_procure_detail").handsontable('getData')
					var save = {
						'data':data,
						'pro_type':pro_type,
						'note':note,
						'id':info_id
					};
					if(pro_type==''){util.xalert('请选择采购方式');return;}
					if (typeof data[0] === "object" && !(data[0] instanceof Array)){
						var hasProp = false;
						for (var prop in data[0]){
							hasProp = true;
							break;
						}
						if (!hasProp){
							util.xalert('请填写采购明细内容');return;
						}
					}

					$.ajax({
						url: info_form_base_url+(info_id ? 'update' : 'insert'),
						data:save,
						dataType:"json",
						type:"POST",
						beforeSend:function(){
							return util.lock(info_form_id);
						},
						success:function(data) {
							$('#'+info_form_id+' :submit').removeAttr('disabled');//解锁
							if(data.success == 1 ){
								$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
								util.xalert(
									info_id ? "修改成功!": "添加成功!",
									function(){
										util.closeTab();
										stone_procure_search_page(util.getItem("orl"));
									}
								);
							}
							else
							{
								util.error(data);//错误处理
							}
						}
					});
				}
			});

		}

		var initData = function(){
			$('#'+info_form_id+' :reset').on('click',function(){

				//单选按钮组重置
//				$("#"+info_form_id+" input[name='xx'][value='"+xx+"']").attr('checked','checked');
//				var test = $("#"+info_form_id+" input[name='xx']:not(.toggle, .star, .make-switch)");
//				if (test.size() > 0) {
//					test.each(function () {
//						if ($(this).parents(".checker").size() == 0) {
//							$(this).show();
//							$(this).uniform();
//						}
//					});
//				}

				//复选按钮重置
//				if (xxx)
//				{
//					$("#"+info_form_id+" input[name='xxx']").attr('checked',true);
//				}
//				else
//				{
//					$("#"+info_form_id+" input[name='xxx']").attr('checked',false);
//				}
//
//				var test = $("#"+info_form_id+" input[name='xxx']:not(.toggle, .make-switch)");
//				if (test.size() > 0) {
//					test.each(function () {
//						if($(this).attr('checked')=='checked')
//						{
//							$(this).parent().addClass('checked');
//						}
//						else
//						{
//							$(this).parent().removeClass('checked');
//						}
//					});
//				}
				//下拉置空
//				$('#'+info_form_id+' select[name="xxxx"]').select2('val','').change();//single
//				$('#'+info_form_id+' select[name="xxxx"]').select2('val',[]).change();//multiple
			});		
		};
		return {
			init:function(){
				initElements();//处理表单元素
				initData();//处理表单重置和其他特殊情况
				mk_table();//处理JS_table
			}
		}
	}();
	obj.init();
});