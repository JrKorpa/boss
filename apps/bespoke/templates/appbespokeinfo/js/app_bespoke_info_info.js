$import(["public/js/select2/select2.min.js",
"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	var app_bespoke_info_info_id ='<%$view->get_bespoke_id()%>';
	var department_id ='<%$view->get_department_id()%>';
	var customer_source_id = '<%$view->get_customer_source_id()%>';
	var baseMemberInfoInfogObj = function(){
		var initElements = function(){};
			if (!jQuery().uniform) {
				return;
			}
			$('#app_bespoke_info_info select[name="cause_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
                $(this).valid();
                $('#app_bespoke_info_info select[name="department_id"]').empty();
                $('#app_bespoke_info_info select[name="department_id"]').append('<option value=""></option>');
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=bespoke&con=AppBespokeInfo&act=getDepartment', {dep: _t}, function(data) {
                        $('#app_bespoke_info_info select[name="department_id"]').append(data);
                    });
                }
				$('#app_bespoke_info_info select[name="department_id"]').change();
			});

			$('#app_bespoke_info_info select[name="department_id"]').select2({
				placeholder: "请选择销售渠道",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#app_bespoke_info_info_daodian select[name="make_order"]').select2({
				placeholder: "请选择销售顾问",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

			$('#app_bespoke_info_info select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
		var handleForm = function(){
			//表单验证和提交
			var url = app_bespoke_info_info_id ? 'index.php?mod=bespoke&con=AppBespokeInfo&act=update' : 'index.php?mod=bespoke&con=AppBespokeInfo&act=insert';
			var options1 = {
				url: url,
				error:function ()
				{
					bootbox.alert('请求超时，请检查链接');
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert({
							message: app_bespoke_info_info_id ? "修改成功!": "添加成功!",
                            buttons: {
                                ok: {
                                    label: '确定'									
                                }
                            },
                            animate: true,
                            closeButton: false,
                            title: "提示信息",
							callback: function () {  
								$("#app_bespoke_info_search_form").submit(); 
							} 																
						});						 
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						bootbox.alert(data.error ? data.error : (data ? data :'程序异常'));
					}
				},
				error:function(){
					$('.modal-scrollable').trigger('click');
					bootbox.alert("数据加载失败");
				}
			};

			$('#app_bespoke_info_info').validate({
				errorElement: 'span', //default input error app_bespoke_info container
				errorClass: 'help-block', // default input error app_bespoke_info class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
				 customer_mobile: {
		                        required: true,
		                        maxlength:11,
		                        isMobile:true
		                    },
				},
				messages: {
                    customer_mobile: {
                        required: "客户手机不能为空.",
                        maxlength:"客户手机不能超过11位",
                        isMobile:"客户手机格式不正确！",
                    },
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
					$("#app_bespoke_info_info").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#app_bespoke_info_info input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#app_bespoke_info_info').validate().form()) {
						$('#app_bespoke_info_info').submit();
					}
					else
					{
						return false;
					}
				}
			});
		}
		var initData = function(){
				//下拉组件重置
			$('#app_bespoke_info_info :reset').on('click',function(){
				$('#app_bespoke_info_info select[name="department_id"]').select2("val",department_id);
				$('#app_bespoke_info_info select[name="customer_source_id"]').select2("val",customer_source_id);
			})
		}
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	baseMemberInfoInfogObj.init();
});

$("#app_bespoke_info_info input[name='customer_mobile']").blur(function(e){
		var _t = $(this).val();
		$.post('index.php?mod=bespoke&con=AppBespokeInfo&act=getUserBymobile', {mobile: _t}, function(data) {
			if(data.success==1){

				$("#app_bespoke_info_info input[name='customer']").val(data.content.member_name);
				//$("#app_bespoke_info_info select[name='customer_source_id']").val(data.content.customer_source_id);
				//$('#app_bespoke_info_info select[name="customer_source_id"]').select2("val", data.content.customer_source_id,true);
				//$('#app_bespoke_info_info select[name="customer_source_id"]').change(); 
				
				//$("#app_bespoke_info_info select[name='department_id']").val(data.content.department_id);
				//$('#app_bespoke_info_info select[name="department_id"]').select2("val", data.content.department_id,true);
				//$('#app_bespoke_info_info select[name="department_id"]').change();
				
				$("#app_bespoke_info_info input[name='customer_email']").val(data.content.member_email);
			}else{
				$("#app_bespoke_info_info input[name='customer']").val('');
				//$("#app_bespoke_info_info select[name='customer_source_id']").val('');
				//$('#app_bespoke_info_info select[name="customer_source_id"]').select2("val", '', true);
				//$('#app_bespoke_info_info select[name="customer_source_id"]').change();                
				$("#app_bespoke_info_info input[name='customer_email']").val('');
               return false;
            }
		});

});

$("#app_bespoke_info_info_daodian button[type='button']").click(function(){
	var bespoke_id ='<%$view->get_bespoke_id()%>';
	var make_order_name=$("#make_order").val();
	var status=$("#status").val();
	var re_lot_code=$("#re_lot_code").val();
	var edit=$("#app_bespoke_info_info_daodian input[name='edit']").val();
	var tab_id=0;
	if(make_order_name==''){
		bootbox.alert('请选择销售顾问.');return false;
	}
	
	if(edit==1){
		var b='BespokeRestatused';
	}else if(edit==2){
		var b='accecipt_maned';
	}else if(edit==22){
        var b='batch_accecipt_maned';
        var bespoke_id=$("#app_bespoke_info_info_daodian input[name='batch_ids']").val();
    }else{
		var b='BespokeRestatus';		
	}
	//bootbox.confirm("", function(result) {
	//	if (result == true) {
	//		setTimeout(function(){
                var params = {id:bespoke_id,make_order:make_order_name,status:status,re_lot_code: re_lot_code};
				$.post('index.php?mod=bespoke&con=AppBespokeInfo&act='+b,params,function(data){
					$('.modal-scrollable').trigger('click');
					if(data.success==1){
						bootbox.alert('提交成功');
						$('.modal-scrollable').trigger('click');
                        //if(b=='accecipt_maned' || b=='batch_accecipt_maned'){
                        //    util.syncTab(61);
                        //}else{
                        //    util.retrieveReload();
                        //    util.syncTab(tab_id);
                        //}
						util.page(util.getItem('url'));
                        to_look_into();
					}
					else{
						bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
					}
				});
		//	}, 0);
	//	}
	//});

});