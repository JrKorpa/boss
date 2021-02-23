$import(function() {
    var info_form_id = 'app_receive_apply_check';//form表单id
    var info_form_base_url = 'index.php?mod=finance&con=AppReceiveApply&act=';//基本提交路径

    var obj = function() {
        var initElements = function() {};

        //表单验证和提交
        var handleForm = function() {
            var url = info_form_base_url + 'check';
            var options1 = {
                url: url,
                error: function()
                {
                    util.timeout(info_form_id);
                },
                beforeSubmit: function(frm, jq, op) {
                    return util.lock(info_form_id);
                },
                success: function(data) {
                    $('#' + info_form_id + ' :submit').removeAttr('disabled');//解锁
                    $('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
                    if (data.success == 1) {
                        //查看页刷新当前页签
                        //util.retrieveReload(data.tab_id);
                        //打开一个新页签
						bootbox.confirm({  
							buttons: {  
								confirm: {  
									label: '确认审核' 
								},  
								cancel: {  
									label: '放弃'  
								}  
							},  
							message: "订单金额/退货贷款 合计："+data.external_total_all+"<br />出入库单据销售价 合计："+data.shijia+"<br/>差异："+data.cha, 
							closeButton: false,
							callback: function(result) {  
								if (result == true) {
									$('body').modalmanager('loading');
									setTimeout(function(){
										$.post(info_form_base_url+'checkOver',{id:data.id,check_sale_number:data.check_sale_number},function(data){
											if(data.success==1)
											{
												$('.modal-scrollable').trigger('click');
												util.xalert("操作成功",function(){
													util.retrieveReload(obj);								
												});
												return ;
											}
											else{
												util.error(data);
											}
										});
									}, 0);
								}
							},  
							title: "信息确认", 
						});
                       // util._pop(info_form_base_url+"checkVerify",{'id':data.id,'check_sale_number':data.check_sale_number,'tab_id':data.tab_id});
                    }
                    else
                    {
                        util.error(data);//错误处理
                    }
                }
            };

            $('#' + info_form_id).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    check_sale_numberss: {
                        required: true
                    },
                },
                messages: {
                    check_sale_numberss: {
                        required: '不能为空！'
                    },
                },
                highlight: function(element) { // hightlight error inputs
                    $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
                    //$(element).focus();
                },
                success: function(label) {
                    label.closest('.form-group').removeClass('has-error');
                    label.remove();
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.closest('.form-control'));
                },
                submitHandler: function(form) {
                    $("#" + info_form_id).ajaxSubmit(options1);
                }
            });
            //回车提交
            $('#' + info_form_id + ' input').keypress(function(e) {
                if (e.which == 13) {
                    $('#' + info_form_id).validate().form();
                }
            });
        };
        var initData = function() {};
        return {
            init: function() {
                initElements();//处理表单元素
                handleForm();//处理表单验证和提交
                initData();//处理表单重置和其他特殊情况
            }
        }
    }();
    obj.init();
});