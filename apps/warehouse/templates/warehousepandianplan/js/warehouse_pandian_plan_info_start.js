function nextBox(obj){
	var data = {
		'id': $('#warehouse_pandian_plan_info_start input[name=id]').val(),
		'box_sn': $('#warehouse_pandian_plan_info_start input[name=box_sn]').val(),
	};
	$('body').modalmanager('loading');
	$.post('index.php?mod=warehouse&con=WarehousePandianPlan&act=qieBox', data , function(res){
		$('.modal-scrollable').trigger('click');
		if(res.success == 1){
			util.xalert(res.error, function(){
				if(res.error == '最后一个柜位盘点已经完成'){

				}
				$('#warehouse_pandian_plan_info_start input[name=goods_id]').attr('disabled', 'disabled');
				util.retrieveReload();
			})
		}else{
			util.error(res);
			return false;
		}
	})
}

$import(['public/js/select2/select2.min.js'], function(){
	var info_form_id = 'warehouse_pandian_plan_info_start';//form表单id
	var info_form_base_url = 'index.php?mod=warehouse&con=WarehousePandianPlan&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	var obj = function(){
		var initElements = function(){

		};

        var submitCheck = function(ext){
            var id = $('#'+info_form_id+' [name="id"]').val();
            var goods_id = $('#'+info_form_id+' [name="goods_id"]').val();
            var box_sn = $('#'+info_form_id+' [name="box_sn"]').val();
            $.ajax({
                url: info_form_base_url+'CreatePandianGoods',
                type: 'POST',
                dataType: 'json',
                data: {id: id, goods_id: goods_id, box_sn: box_sn, affirm: ext},
            })
            .always(function(data) {
                if(data.success == 0){
                    util.xalert(data.error);return;
                }else{
                    $('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
                    $('#show_tishi').html(data.error);
                    $('#num').html(data.num);
                    $('#warehouse_pandian_plan_info_start input[name=goods_id]').val('');                    
                }
            });
        };

		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+(info_id ? 'CreatePandianGoods' : 'insert');
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
						/*util.xalert(
							data.error,
							function(){
								//刷新页面

								//清空input
								// $('#warehouse_pandian_plan_info_start input[name=goods_id]').val('');
								util.retrieveReload();
							}
						);*/
						$('#show_tishi').html(data.error);
						$('#num').html(data.num);
						$('#warehouse_pandian_plan_info_start input[name=goods_id]').val('');
					}
					else
					{
                        if(data.affirm == 1){
                            bootbox.confirm(data.error, function(result) {
                            if (result == true) {
                                    submitCheck(data.affirm);//确认
                                }else{
                                    $('#warehouse_pandian_plan_info_start input[name=goods_id]').val('');
                                }
                            });
                        }else{
                            util.error(data);//错误处理
                        }
					}
				}
			};

			$('#'+info_form_id).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					'type':{
						required:true
					},
//					abc:{
//						maxlength:20
//					}
				},
				messages: {
					'type':{
						required:'请选择抽验仓'
					},
//					abc:{
//						maxlength:'最多输入20个字符'
//					}

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
				//下拉置空
				$('#'+info_form_id+' select[name="type"]').select2('val','').change();//single
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