$import(["public/js/select2/select2.min.js","public/js/jquery.validate.extends.js"],function(){
    var info_form_id = 'app_order_details_rai';//form表单id
    var info_form_base_url = 'index.php?mod=sales&con=AppOrderDetails&act=';//基本提交路径
    var order_amount=parseFloat(<%$acountinfo.order_amount%>.toFixed(2));
    var goods_amount=parseFloat(<%$acountinfo.goods_amount%>.toFixed(2));
    var order_favorable_price=parseFloat(<%$acountinfo.favorable_price%>.toFixed(2));
    var goods_price=parseFloat(<%$view->get_goods_price()%>.toFixed(2));
    var patt=/[^\d\.]/g;
    var obj = function(){
        var initElements = function(){
            //处理价格
            $('#'+info_form_id+" input[name=xzprice]").keyup(function(){
                var v = $(this).val();
                $(this).val(v);
                v=parseFloat(v);
                $("#order_price").html((order_amount+v).toFixed(2));
                $("#goods_amount").html((goods_amount+v).toFixed(2));
            })
            $('#'+info_form_id+" input[name=realprice]").blur(function(){
                var v = $(this).val();
                $(this).val(v);
                v=parseFloat(v);
                diff = parseFloat((v - goods_price).toFixed(2));
                $("#order_price").html(parseFloat(order_amount.toFixed(2))+diff);
                $("#goods_amount").html(parseFloat(goods_amount.toFixed(2))+diff);
				$('#'+info_form_id+" input[name=xzprice]").val(diff.toFixed(2));
            })
        }


        var initData = function(){}
        var handleForm = function(){
            var url = info_form_base_url+'UpdateGoodsPrice';
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
                            "增加成功",
                            function(){
                              util.retrieveReload();//刷新查看页签
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

                },

                messages: {

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
        }



        return {

            init:function(){
                initElements();//美化表单元素
                handleForm();
                initData();//处理重置按钮
            }
        }

    }();

    obj.init();
});