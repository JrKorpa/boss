$import(["public/js/select2/select2.min.js"],function(){
	var info_form_id = 'material_bill_info_ck';//form表单id
	var info_form_base_url = 'index.php?mod=warehouse&con=MaterialBill&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	var obj = function(){
		var initElements = function(){
			$('#'+info_form_id+' select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			
		
			//单选美化
//			var test = $("#"+info_form_id+" input[type='radio']:not(.toggle, .star, .make-switch)");
//			if (test.size() > 0) {
//				test.each(function () {
//					if ($(this).parents(".checker").size() == 0) {
//						$(this).show();
//						$(this).uniform();
//					}
//				});
//			}
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
//			$('#'+info_form_id+' select').select2({
//				placeholder: "请选择",
//				allowClear: true,
////				minimumInputLength: 2
//			}).change(function(e){
//				$(this).valid();
//			});	
		};
		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+(info_id ? 'update' : 'insertCK');
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout(info_form_id);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(info_form_id);
				},
				success: function(data) {
					$('#'+info_form_id+' :submit').removeAttr('disabled');//解锁
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
						util.xalert(
							info_id ? "修改成功!": "添加成功!",
							function(){								
								if (info_id)
								{//刷新当前页
									util.page(util.getItem('url'));
								}
								else
								{//刷新首页
									material_bill_search_page(util.getItem("orl"));
									
									var tab_id = 'materialbill-'+data.bill_id;
									var tab_title = data.bill_no;
									var prefix = "#materialbill";									
									var tab_url ='index.php?mod=warehouse&con=MaterialBill&act=show&id='+data.bill_id;
									$('#nva-tab li').each(function(){					   
										var that = this;
										var href = $(that).children('a').attr('href');
										href = href.split('-')[0];	
										if (href==prefix)
										{
											$(that).children('i').trigger("click");											
										}											
									});
									new_tab(tab_id,tab_title,tab_url);
								}
							}
						);
					}
					else
					{
						util.error(data);//错误处理
					}
				}
			};

			$('#'+info_form_id).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					'bill_type':{
						required:true	
					},
					
					/*'department_id':{
						required:true
					},
					/*'supplier_id':{
						required:true	
					},*/
					
				},
				messages: {
                   
					'bill_type':{
						required:"单据类型不能为空"	
					},
					/*'department_id':{
						required:"销售渠道不能为空",
					},
					/*'supplier_id':{
						required:"供应商不能为空"	
					},*/
				},

				highlight: function (element) { // hightlight error inputs
					$(element)
						.closest('.form-group').addClass('has-error'); // set error class to the control group
					//$(element).focus();
				},

				success: function (label) {
					label.closest('.form-group').removeClass('has-error');
					label.remove();
				},

				errorPlacement: function (error, element) {
					error.insertAfter(element.closest('.form-control'));
				},

				submitHandler: function (form) {
					$("#"+info_form_id).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id).validate().form();
				}
			});
		};
		var initData = function(){
			$('#'+info_form_id+' :submit').on('click',function(){
				var bill_type = $("select[name='bill_type']").select2('val');
				var department_id = $("select[name='department_id']").select2('val');
				
				if(bill_type != 'WB' && department_id == ''){
					alert("销售渠道不能为空");return false;
				}
			})
			
			
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
			    $('#'+info_form_id+' select').select2('val','').change();//single
//				$('#'+info_form_id+' select[name="xxxx"]').select2('val',[]).change();//multiple
			});		
		};
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	obj.init();
});