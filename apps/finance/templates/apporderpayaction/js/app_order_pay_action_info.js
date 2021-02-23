$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"], function() {

    var obj = function() {
        var initElements = function() {
            //下拉列表美化
            $(".is_div").show();
            $('#app_order_pay_action_info select[name="pay_type[]"]').select2({
                placeholder: "请选择支付类型",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
                var val = $(this).find('option:selected').text();
                if(val == '现金'){
                    $(this).parents("div").parent().next(".is_div").hide();
                    $(this).parents("div").parent().next().next(".is_div_end").hide();
                }else if(val=='旧订单转单' || val=='以旧换新/转单'){
                    $(this).parents("div").parent().next().next(".is_div_end").show();
                    $(this).parents("div").parent().next(".is_div").hide();
                }else{
                    $(this).parents("div").parent().next(".is_div").show();
                    $(this).parents("div").parent().next().next(".is_div_end").hide();
                }
            });//validator与select2冲突的解决方案是加change事件
            $('#app_order_pay_action_info select[name="department[]"]').select2({
                placeholder: "请选择协助销售渠道",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });
            //时间控件
            if ($.datepicker) {
                $('.date-picker').datepicker({
                    format: 'yyyy-mm-dd',
                    rtl: App.isRTL(),
                    autoclose: true,
                    clearBtn: true
                });
                $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
            }
        };
        //表单验证和提交
        var handleForm = function() {
            var url = 'index.php?mod=finance&con=AppOrderPayAction&act=inserts';
//            var url = 'index.php?mod=finance&con=AppOrderPayAction&act=insert';
            var options1 = {
                url: url,
                error: function(){
                    $('.modal-scrollable').trigger('click');
                    bootbox.alert({
                        message: "请求超时，请检查链接",
                        buttons: {
                            ok: {
                                label: '确定'
                            }
                        },
                        animate: true,
                        closeButton: false,
                        title: "提示信息"
                    });
                    return;
                },
                beforeSubmit: function(frm, jq, op) {
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function(data) {
                    if (data.success == 1) {
                        $('.modal-scrollable').trigger('click');//关闭遮罩
                        bootbox.alert({
                            message: "点款成功!",
                            buttons: {
                                ok: {
                                    label: '确定'
                                }
                            },
                            animate: true,
                            closeButton: false,
                            title: "提示信息",
                            callback: function() {
                                if (data._cls)
                                {
                                    util.retrieveReload();
                                    util.syncTab(data.tab_id);
                                }
                                else
                                {
                                    //刷新首页
                                    //app_order_pay_action_search_page(util.getItem("orl"));
                                    //刷新当前页
                                    //util.page(util.getItem("url"));
                                    var $li = $("#nva-tab li").children('a[href="#tab-<%$menu.id%>"]');
                                    if ($li.length == 1) {
                                        util.syncTab("<%$menu.id%>");
                                    }
                                    util.closeTab();
                                    new_tab("tab-<%$menu.id%>","<%$menu.label%>","<%$menu.url%>");
                                }
                            }
                        });
                    } else {
                        $('body').modalmanager('removeLoading');//关闭进度条
                        bootbox.alert({
                            message: data.error ? data.error : (data ? data : '程序异常'),
                            buttons: {
                                ok: {
                                    label: '确定'
                                }
                            },
                            animate: true,
                            closeButton: false,
                            title: "提示信息"
                        });
                        return;
                    }
                }
            };
            $('#app_order_pay_action_info').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
//					'order_deposit[]': {
//						required: true,
//						isFloat:true
//					},
//					'pay_time[]': {
//						required: true,
//					},
//					pay_type: {
//						required: true,
//					},
                },
                messages: {
//					'order_deposit[]': {
//                        required:'不能为空',
//						isFloat: "请输入正数"
//					},
//					'pay_time[]': {
//						required: "请输入支付时间."
//					},
//					pay_type: {
//						required: "请输入支付类型."
//					},
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
                    $("#app_order_pay_action_info").ajaxSubmit(options1);
                }
            });
            //回车提交
            $('#app_order_pay_action_info input').keypress(function(e) {
                if (e.which == 13) {
                    $('#app_order_pay_action_info').validate().form()
                }
            });
        };
        var initData = function() {
            $('#app_order_pay_action_info :reset').on('click',function(){
                $('#app_order_pay_action_info select[name="pay_type[]"]').select2("val",'');
            })
        };
        /*添加支付方式*/
        var addPayBox = function() {
            $('#app_order_pay_action_info').on('click','button[class="btn btn-primary"]',function(){
                var flag = '<%$flag%>';
                $.post('index.php?mod=finance&con=AppOrderPayAction&act=add',{'flag':flag},function(data){
                    var a = $("#app_order_pay_action_info_addbox").find('portlet-title');
                    $("#app_order_pay_action_info_addbox").append(data.content);
                    initElements();
                },'json');
                
            })
        };
        return {
            init: function() {
                initElements();//处理表单元素
                handleForm();//处理表单验证和提交
                initData();//处理表单重置和其他特殊情况
                addPayBox();//添加支付方方式
            }
        }
    }();
    obj.init();
});
//修改支付方式
function checkpaytype222(it) {
    debugger;
    var pay_count = $("input[name='order_deposit[]']").length;
    var box_num = $("input[name='deposit_sn[]']").index(it);
    var deposit_sn = $("input[name='deposit_sn[]']").eq(box_num).val();
    
    if(deposit_sn){
        $("input[name='order_deposit[]']").eq(box_num).attr('readonly',true);
    }else{
        $("input[name='order_deposit[]']").eq(box_num).attr('readonly',false);
    }
    var same = 0;
    if (pay_count > 1) {
        for (var i = 0; i < pay_count; i++) {
            var old_sn = $("input[name='deposit_sn[]']").eq(i).val();
            if (receipt_sn != '' && receipt_sn == old_sn) {
                same++;
            }
        }
        if (same == 2) {
            alert("定金收据号已经重复");
            $("input[name='deposit_sn[]']").eq(box_num).val('');
            return false;
        }
    }
    var receipt_sn = $(it).val();
    if (receipt_sn == '') {
        return false;
    }
    if (receipt_sn == 0) {
        return false;
    }
    $.post("index.php?mod=finance&con=AppReceiptDeposit&act=getListBySn", {'deposit_sn': receipt_sn}, function(data) {
        if (data.success == 1) {
            var item = data.ret;
            if(item.pay_type==3){
            	$(".is_div").hide();
            }
            $("input[name='order_deposit[]']").eq(box_num).val(item.pay_fee);
            $("select[name='pay_type[]']").eq(box_num).select2("val", item.pay_type).change(function(){
                var val = $("select[name='pay_type[]']").eq(box_num).val();
                var itN = $("select[name='pay_type[]']").eq(box_num);
                var is_div = getNameObj('is_div');
            });
            $("input[name='pay_time[]']").eq(box_num).val(item.pay_time);
            $("input[name='card_no[]']").eq(box_num).val(item.card_no);
			$("input[name='card_voucher[]']").eq(box_num).val(item.card_voucher);
        }else{
            alert(data.error);
            $("input[name='deposit_sn[]']").eq(box_num).val('');

        }
    }, 'json');
}
