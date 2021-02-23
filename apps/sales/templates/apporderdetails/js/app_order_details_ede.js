$import(["public/js/select2/select2.min.js","public/js/jquery.validate.extends.js"],function(){
    var info_form_id = 'app_order_details_ede';//form表单id
    var info_form_base_url = 'index.php?mod=sales&con=AppOrderDetails&act=';//基本提交路径
    var patt=/[^\d-\.]+/g;
    var obj = function(){
        var initElements = function(){
            //初始化单选按钮组
           //处理价格问题
            $('#'+info_form_id+' input').blur(function(){
                var d=$(this);
                if(d.hasClass('fpj')||d.hasClass('gpj')){
                    var gsum = 0;
                    var fsum = 0;
                    var v=d.val();
                    v = v.replace(patt,"");
                    if(v==""){
                        d.val(0);
                        v=0;
                    }
                    v=parseFloat(v);
                    if(d.hasClass('gpj')){
                        d.siblings(".gpa").val(parseFloat(d.siblings(".gpy").val())+v);
                    }else{
                        d.siblings(".fpa").val(parseFloat(d.siblings(".fpy").val())+v);
                    }

                    $('#favorable_price').css('color','');
                    $('#favorabgoods_pricele_price').css('color','');
                    $('#'+info_form_id+' input[class*=gpa]').each(
                        function(k,v){
                            gsum+=parseFloat($(this).val());
                        }
                    )
                    $('#'+info_form_id+' input[class*=fpa]').each(
                        function(k,v){
                            fsum+=parseFloat($(this).val());
                        }
                    )
                    var agsum=(parseFloat($('#'+info_form_id+' input[name=order_g_p]').val())-parseFloat(gsum)).toFixed(2);
                    var afsum=(parseFloat($('#'+info_form_id+' input[name=order_g_f]').val())-parseFloat(fsum)).toFixed(2);
                    //debugger;
                    $('#goods_price').html(agsum);
                    $('#favorable_price').html(afsum);
                }else{
                    return false;
                }
            })

        }


        var initData = function(){}

        var handleForm = function(){
            var url = info_form_base_url+'ValenceDelete';
            var options1 = {
                url: url,
                error:function ()
                {
                    util.timeout(info_form_id);
                },
                beforeSubmit:function(frm,jq,op){
                    if(parseFloat($('#favorable_price').html())!=0){
                        util.xalert(
                            "这个商品的优惠没有完全分出去");
                        $('#favorable_price').css('color','red');
                        return false;
                    }

                    if(parseFloat($('#goods_price').html())!=0){
                        util.xalert(
                            "这个商品的价格没有完全分出去");
                        $('#goods_price').css('color','red');
                        return false;
                    }

                    return util.lock(info_form_id);
                },
                success: function(data) {
                    $('#'+info_form_id+' :submit').removeAttr('disabled');//解锁
                    if(data.success == 1 ){
                        $('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
                        util.xalert(
                            "成功",
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
                    title: {

                    }
                },

                messages: {
                    title: {

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