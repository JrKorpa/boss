function ship_freighr_submit(){
    $('#ship_freight_order_info').submit();
}

$import(["public/js/select2/select2.min.js",
    "public/js/jquery-tags-input/jquery.tagsinput.css",
    "public/js/jquery-tags-input/jquery.tagsinput.min.js"
],function(){
    var info_form_id = 'ship_freight_order_info';
    var obj = function(){
        var initElements = function(){
            $('#ship_freight_order_info_table2 select[name="express_id"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });
            var goods_sn_box = $('#ship_freight_order_info_table2 input[name="goods_sn"]');
            goods_sn_box.keypress(function(e){
                if(e.keyCode == "13")
                {
                    var goods_sn = goods_sn_box.val();
                    var order_sn = $('#ship_freight_order_info_table1 input[name="order_no"]').val();
					//alert(goods_sn);alert(order_sn);return false;
                    var url = 'index.php?mod=shipping&con=ShipFreight&act=checkGoodsSN';
                    $.post(url,{'goods_sn':goods_sn,'order_sn':order_sn},function(e){
                        var check_box = $('#ship_freight_goods_sn_check');
                        var send_box = $('#ship_freight_goods_sn_send');
                        goods_sn_box.val('');
                        if(e == 1){
                            check_box.css("background","#FFF").empty().append('<span class="btn btn-success" type="button">正确</span>');
                            send_box.append("<input type='hidden' class='form-control' name='sn_arr[]' value='"+goods_sn+"'/>");
                           $('#ship_freight_order_goods_'+goods_sn+' > td').css("background","gray");

                        }else{
                            check_box.css("background","red").empty().append('<span class="btn dark" type="button">错误</span>')
                        }
                    });
                }
            })
        };

        var handleForm = function(){
            var url = 'index.php?mod=shipping&con=ShipFreight&act=insert';
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
                        util.xalert("操作成功!",function(){
                            $('#ship_freight_search_form input[name="order_no"]').val("");
                            $('#ship_freight_search_form button')[0].click();
                        });
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
                    order_no:{required:true},
                    consignee:{required:true},
                    cons_address:{required:true},
                    express_id:{required:true}
                },
                messages: {
                    order_no:{required:"订单号必填"},
                    consignee:{required:"收件人必填"},
                    cons_address:{required:"收件地址必填"},
                    express_id:{required:"请选择快递公司"}
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

        };

        var initData = function(){

        };

        /*var freight = function(){
            //发货button
            $('.deliver_btn').click(function(){
                var express = $('#ship_freight_order_info_table2 select[name="express_id"]').find('option:selected').val();
                var freight_no = $('#ship_freight_order_info_table2 input[name="freight_no"]').val();
                var order_no = $('#ship_freight_order_info_table1 input[name="order_no"]').val();
                var consignee = $('#ship_freight_order_info_table1 input[name="consignee"]').val();
                var cons_mobile = $('#ship_freight_order_info_table1 input[name="cons_mobile"]').val();
                var cons_tel = $('#ship_freight_order_info_table1 input[name="cons_tel"]').val();
                var cons_address = $('#ship_freight_order_info_table1 input[name="cons_address"]').val();
                var note = $('#ship_freight_order_info_table1 input[name="note"]').val();
                if(express =="" || freight_no=="" ){
                    util.xalert("请填写快递信息！");return false;
                }else{
                    //todo 保存快递信息
                    var url = 'index.php?mod=shipping&con=ShipFreight&act=insert';
                    var data = {
                        'express_id':express,'freight_no':freight_no,'order_no':order_no,
                        'consignee':consignee,'cons_mobile':cons_mobile,'cons_tel':cons_tel,
                        'cons_address':cons_address,'note':note
                    };
                    $.post(url,data,function(e){
                        //alert(e);return false;
                        util.xalert(e?"发货成功！":"发货失败！");
                    });
                }

            });
        };*/

        return {
            init:function(){
                initElements();//处理表单元素
                handleForm();//处理表单验证和提交
                initData();//处理表单重置和其他特殊情况
                //freight();
            }
        }
    }();

    obj.init();
})

