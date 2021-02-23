$import(function(){
	var info_form_id = 'app_order_details_daijinquan_info';//form表单id
	var info_form_base_url = 'index.php?mod=sales&con=AppOrderDetails&act=';//基本提交路径

	var obj = function(){
		var initElements = function(){
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
			$('#'+info_form_id+' select[name="check_user"]').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function(e){
				var name = $(this).find('option:selected').text();
				$("#app_order_details_daijinquan_info input[name='check_user_name']").val(name);
				$(this).valid();
			});	
		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+'daijinquan_insert';
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
							"添加成功!",
							function(){
								if (data._cls)
								{//查看编辑
									util.retrieveReload();//刷新查看页签
									util.syncTab(data.tab_id);//刷新数据主列表，无法定位到分页（有可能数据列表页签已经关闭，也有可能是其他对象穿透查看，所以分页函数不一定存在）
								}
								else
								{
                                    util.retrieveReload();//刷新查看页签
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
					'daijinquan_code':{
						required:true,
					}
				},
				messages: {
					'daijinquan_code':{
						required:'请输入兑换码'
					}
					
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
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	obj.init();
});


function check_daijinquan_code(){
	var daijinquan_code = $("#app_order_details_daijinquan_info input[name='daijinquan_code']").val();
	if(!daijinquan_code){
		return false;
	}
	var departmentid = $("#app_order_details_daijinquan_info input[name='departmentid']").val();
    var detail_id = $("#app_order_details_daijinquan_info input[name='id']").val();

	$.post("index.php?mod=sales&con=AppOrderDetails&act=checkDaijinquanCode", {'daijinquan_code':daijinquan_code,'departmentid':departmentid,'detail_id':detail_id}, function(data) {
		if(data){
			if(data.success==1){
		   	  $("#app_order_details_daijinquan_info input[name='daijinquan_price']").val(data.return_msg.exchange_item);
                $("#app_order_details_show_point").html(data.return_msg.point_string);
		    }else{
		       util.xalert(data.error);
               $("#app_order_details_show_point").html('');
		       $("#app_order_details_daijinquan_info input[name='daijinquan_price']").val('');
		    }   
		}else{
			util.xalert('没有该优惠券信息');
			$("#app_order_details_daijinquan_info input[name='daijinquan_price']").val('');
			return false;
		}
	},'json')
}