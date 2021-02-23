//盘点完成按钮
function OffPandian(obj){
	var url = $(obj).attr('data-url');
	var bill_id = $('#warehouse_bill_info_w_box input[name=bill_id]').val();
	var bill_note = $('#warehouse_bill_info_w_box input[name=bill_note]').val();
	bootbox.confirm("确定盘点完成吗?", function(result){
		if (result == true) {
			$('body').modalmanager('loading');//进度条和遮罩
			$.get(url+'&bill_id='+bill_id+'&bill_note='+bill_note,'', function(res){
				if(res.success == 1 ){
					$('.modal-scrollable').trigger('click');//关闭遮罩
					util.xalert(
						res.error,
						function(){
						util.retrieveReload(obj);
					});
				}else{
					$('body').modalmanager('removeLoading');//关闭进度条
					util.xalert(
						res.error ? res.error : '程序异常',
						function(){
							// util.retrieveReload(obj);
						});
					return;
				}
			})
		}
	});
}


//切换柜位
function qieBox(obj){
	$('body').modalmanager('loading');//进度条和遮罩
	var url = $(obj).attr('data-url');
	var bill_id = $('#warehouse_bill_info_w_box input[name=bill_id]').val();
	$.get(url+'&bill_id='+bill_id,'', function(res){
		if(res.success == 1 ){
			$('.modal-scrollable').trigger('click');//关闭遮罩
			util.xalert(
				res.error,
				function(){
				util.retrieveReload(obj);
			});
		}else{
			$('body').modalmanager('removeLoading');//关闭进度条
			util.xalert(
				res.error ? res.error : '程序异常',
				function(){
					// util.retrieveReload(obj);
				});
			return;
		}
	})
}

//取消
function closePandian(obj){
	var bill_id = '<%$info.id%>';
	var url = $(obj).attr('data-url');
	url = url+'&bill_id='+bill_id;
	bootbox.confirm("确定取消单据吗?", function(result){
		if (result == true) {
			$('body').modalmanager('loading');//进度条和遮罩
			$.get(url , '' , function(res){
				if(res.success == 1){
					$('.modal-scrollable').trigger('click');//关闭遮罩
					util.xalert(
						res.error,
						function(){
						util.retrieveReload(obj);
					});
				}else{
					$('body').modalmanager('removeLoading');//关闭进度条
					util.xalert(
						res.error ? res.error : '程序异常',
						function(){
							// util.retrieveReload(obj);
						});
					return;
				}
			})
		}
	});
}

//动态修改盘点单备注
function subnote(obj){
	var bill_note = $(obj).val();
	var bill_id = $(obj).attr('bill_id');
	var data = {
		'bill_note' : bill_note,
		'bill_id' : bill_id,
	};
	$.post('index.php?mod=warehouse&con=WarehouseBillInfoW&act=InsertBillNote' , data , function(res){

	})
}

//导出结果
function downCsv(obj){
	var bill_id = '<%$info.id%>';
	var down_url = 'index.php?mod=warehouse&con=WarehouseBillInfoW&act=downCsv&bill_id='+bill_id;
	window.open(down_url);
}

//导出柜位结果
function downGuiweiCsv(obj){
	var bill_id = '<%$info.id%>';
	var down_url = 'index.php?mod=warehouse&con=WarehouseBillInfoW&act=downGuiweiCsv&bill_id='+bill_id;
	window.open(down_url);
}

$import(['public/js/select2/select2.min.js'] , function(){
	var obj = function(){
		var initElements = function(){
			//刚进来 不显示货号的输入框
			$('#input_goods').css('display','none');
		};

        var submitCheck = function(ext){
            var info_form_id = "warehouse_bill_info_w_box";
            //var id = $('#'+info_form_id+' [name="id"]').val();
            var bill_id = $('#'+info_form_id+' [name="bill_id"]').val();
            var goods_id = $('#'+info_form_id+' [name="goods_id"]').val();
            var box_sn = $('#'+info_form_id+' [name="box_sn"]').val();
            var warehouse_id = $('#'+info_form_id+' [name="warehouse_id"]').val();
            $.ajax({
                url: 'index.php?mod=warehouse&con=WarehouseBillInfoW&act=GetBoxPandian',
                type: 'POST',
                dataType: 'json',
                data: {warehouse_id: warehouse_id, goods_id: goods_id, box_sn: box_sn, bill_id: bill_id, affirm: ext},
            })
            .always(function(data) {
                if(data.success == 1 ){
                    $('.modal-scrollable').trigger('click');//关闭遮罩
                    /*util.xalert(data.error,function(){
                        util.retrieveReload(obj);
                    });*/
                    if(data.is_goods == 1){     //输入货号提交盘点
                        $('#paned').text(data.row);
                        $('#count1').text(data.count1);
                        //盘盈时 弹出框提示
                        /*if(data.error.indexOf('盘盈'  > 0)){
                            util.xalert(data.error);
                        }*/
                        $('#pandianed_list').find('p:first').before('<p>'+data.error+'</p>');
                        $('#warehouse_bill_info_w_box input[name=goods_id]').val('');
                    }else{          //输入柜位，切换柜位
                        //util.retrieveReload(obj);
                        
                        //隐匿柜位输入框
                        var xbox = $('#input_box input[name="box_sn"]').val();
                        $('#input_box').css('display','none');
                        $('#text_box').css('display','').text(xbox);

                        $('#goods_num').text(data.goods_num)    //变更单据的货品总量

                        //显示货号input
                        $('#input_goods').css('display','');
                        $('#input_goods input[name="goods_id"]').removeAttr('disabled');
                    }
                }else{
                    $('body').modalmanager('removeLoading');//关闭进度条
                    util.xalert(
                        data.error ? data.error : '程序异常',
                        function(){
                            // util.retrieveReload(obj);
                        });
                    return;
                }
            });
        };

		//表单验证和提交
		var handleForm = function(){
			var url = 'index.php?mod=warehouse&con=WarehouseBillInfoW&act=GetBoxPandian';

			var options1 = {
				url: url,
				error:function ()
				{
					$('.modal-scrollable').trigger('click');
					util.xalert("请求超时，请检查链接");
					return;
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						/*util.xalert(data.error,function(){
							util.retrieveReload(obj);
						});*/
						if(data.is_goods == 1){		//输入货号提交盘点
							$('#paned').text(data.row);
							$('#count1').text(data.count1);
							//盘盈时 弹出框提示
							/*if(data.error.indexOf('盘盈'  > 0)){
								util.xalert(data.error);
							}*/
							$('#pandianed_list').find('p:first').before('<p>'+data.error+'</p>');
							$('#warehouse_bill_info_w_box input[name=goods_id]').val('');
						}else{			//输入柜位，切换柜位
							//util.retrieveReload(obj);
							
							//隐匿柜位输入框
							var xbox = $('#input_box input[name="box_sn"]').val();
							$('#input_box').css('display','none');
							$('#text_box').css('display','').text(xbox);

							$('#goods_num').text(data.goods_num)	//变更单据的货品总量

							//显示货号input
							$('#input_goods').css('display','');
							$('#input_goods input[name="goods_id"]').removeAttr('disabled');
						}
					}else{
                        if(data.affirm == 1){
                            bootbox.confirm(data.error, function(result) {
                            if (result == true) {
                                    submitCheck(data.affirm);//确认
                                }else{
                                    //$('#warehouse_pandian_plan_info_start input[name=goods_id]').val('');
                                }
                            });
                        }else{
                            $('body').modalmanager('removeLoading');//关闭进度条
                            util.xalert(
                                data.error ? data.error : '程序异常',
                                function(){
                                    // util.retrieveReload(obj);
                                });
                            return;
                        }
					}
				}
			};

			$('#warehouse_bill_info_w_box').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					box_sn : {required: true, maxlength: 25},
				},
				messages: {
					box_sn : {required: "请输入柜位号", maxlength: "柜位号超过了25个字符"},
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
					$("#warehouse_bill_info_w_box").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#warehouse_bill_info_w_box input').keypress(function (e) {
				if (e.which == 13) {
					$('#warehouse_bill_info_w_box').validate().form()
				}
			});
		};

		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//提交处理数据
			}
		}
	}();
	obj.init();
});