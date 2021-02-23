function goods_info_search_page_s(url){
    util.page(url);
}
$import("public/js/select2/select2.min.js",function(){
    var info_form_id = 'external_order_info';//form表单id
    var info_form_base_url = 'index.php?mod=sales&con=ExternalOrder&act=';
    var obj = function(){
        var initElements = function(){
            $('#'+info_form_id+' input[name=exter_order_num]').focus();
            $('#'+info_form_id+' select[name=order_source]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                var source=$(this).val();
                if(source!=''){
                    $('#ExternalInfo').empty();
                }
                var out_sn =$('#'+info_form_id+' input[name=exter_order_num]').val();
                if(source==''||out_sn==''){
                    $('#'+info_form_id+' input[name=kela_order_sn]').attr('readonly',true);
                }else{
                    $('#'+info_form_id+' input[name=kela_order_sn]').attr('readonly',false);
                }
            });

            $('#'+info_form_id+' input[name=exter_order_num]').blur(function(){
                var source= $('#'+info_form_id+' select[name=order_source]').select2('val');
                var out_sn =$(this).val();
                if(source==''||out_sn==''){
                    $('#'+info_form_id+' input[name=kela_order_sn]').attr('readonly',true);
                }else{
                    $('#'+info_form_id+' input[name=kela_order_sn]').attr('readonly',false).trigger('blur');
                }
            });
            $('#exter_order_num_search').on('click',function(){
				$('body').modalmanager('loading');//进度条和遮罩							 
                var order_id = $('#'+info_form_id+' input[name=exter_order_num]').val();
                var order_source = $('#'+info_form_id+' select[name=order_source]').select2('val');
                if(order_id==''||order_source==''){
                    util.xalert('外部订单号和订单来源均不能为空');
                    return false;
                }
                var ourl = info_form_base_url+'getExternalOrdeInfo';
                var data ={order_source:order_source,order_id:order_id}
                $.ajax({url:ourl,data:data,type: "POST",async:true,success:function(data){
						$('body').modalmanager('removeLoading');//关闭进度条
						$('#ExternalInfo').html(data);  
						$('#s_goods_info').attr('disabled',false);						
					}
                })
            });
            $('#'+info_form_id+' input[name=kela_order_sn]').on('keyup',function(){
                var span =$(this).siblings('span');
                var preg = /\D*/g;
                $(this).val($.trim($(this).val()).replace(preg,''));
                if($(this).val()==''){
                    span.empty().html('');
                    $('#ext_order_info_base').css('display','block');
                    $('#'+info_form_id+' input[name=flag]').val('');

                }
                if($(this).val().length==14){
                    var ordersn =  $(this).val();
                    var outordersn =  $('#'+info_form_id+' input[name=exter_order_num]').val();
                    var order_source=$('#'+info_form_id+' select[name=order_source]').select2('val');
                    var url=info_form_base_url+'getRelOutsn';
                    $.post(url,{ordersn:ordersn,outordersn:outordersn,order_source:order_source},function(data){
                        if(data.success==1){
                            $('#'+info_form_id+' input[name=flag]').val('1');
                            span.empty().html('该BDD订单可以追加该外部订单号').css('color','green');
                            $('#ext_order_info_base').css('display','none');
                        }else{
                            $('#'+info_form_id+' input[name=flag]').val('0');
                            span.empty().html(data.error).css('color','red');
                        }
                    });
                }

            });
            $('#'+info_form_id+' input[name=kela_order_sn]').on('blur',function(){
                if($(this).val()==''){
                    return false;
                }
                if($(this).val().length!=14){
                    var span =$(this).siblings('span');
                    $('#'+info_form_id+' input[name=flag]').val('0');
                    span.empty().html('BDD订单号不符合规范').css('color','red');
                }else{
                    $(this).trigger('keyup');
                }
            })
           };


        //表单验证和提交
        var handleForm = function(){
            var url = info_form_base_url+'insert';
            var options1 = {
                url: url,
                error:function ()
                {
                    util.timeout(info_form_id);
                },
                beforeSubmit:function(frm,jq,op){
                    //校验货号重复的问题
                    var arrgoodsid=new Array();
                    var cf=1;
                    $('#'+info_form_id+" input[name='ms[goods_id][]']").each(function(i,e){
                        if($(e).val()!=''){
                            if($.inArray($(e).val(),arrgoodsid)!=-1){
                               cf=2;
                            }
                            arrgoodsid.push($(e).val());
                        }
                    });
                    if(cf==2){
                        util.xalert('货号有重复禁止提交');
                        return false;
                    }
                    //所有的款号必须填写
                    var kh=1;
                    $('#'+info_form_id+" input[name='ms[goods_sn][]']").each(function(i,e){
                            if($(e).val()==''){
                                kh=2;
                            }
                    });
                    if(kh==2){
                        util.xalert('所有商品款号必须填写');
                        return false;
                    }
                    $('#'+info_form_id+" select[name='ms[is_occupation][]']").each(function(i,e){
                        if($(e).select2('val')===''){
                            kh=3;
                        }
                    });
                    if(kh==3){
                        util.xalert('亲，请选择是否占用备货名额');
                        return false;
                    }
                    if($('#'+info_form_id+' input[name=flag]').val()==="0"){
                        util.xalert('追加的BDD订单不符合追加要求请检查');
                        return false;
                    }

                    // 备注里面有‘金额’和数字，给出以下提醒
                    var remark = $('#'+info_form_id+' textarea[name=order_remark]').val();
                    if ( /\d/.test(remark) && /金额/.test(remark)) {
                        return window.confirm("请确认订单总金额，已支付金额，优惠金额没有问题！？");
                    }
                    return util.lock(info_form_id);
                },
                success: function(data) {

                    if(data.success == 1 )
                    {
                        $('#'+info_form_id+' :submit').removeAttr('disabled');//解锁
                        $('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
                        if($('#'+info_form_id+' input[name=flag]').val()==1){
                            $('#ExternalInfo').empty();
                            $('#'+info_form_id+' input[name=exter_order_num]').focus();
                            $('#'+info_form_id+' input[name=kela_order_sn]').focus();
                            util.xalert("追加成功");
                        }else{
                            util.xalert( "您的订单号为"+data.error,function(){
                                //模拟跳转到详情页并不会造成任何js冲突的方法
                                $('#spanAid').html('<a href="javascript:;" class="tab-con-a" style="display:none;" id="jumpvieworder" data-title="'+data.error+'" data-id="baseorderinfo-'+data.order_id+'" data-url="index.php?mod=sales&con=BaseOrderInfo&act=show&order_sn='+data.error+'">'+data.error+'</a>');
                                $('#jumpvieworder').trigger('click');
                                $('#ExternalInfo').empty();
                                $('#'+info_form_id+' select[name=order_source]').select2('val','');
                                $('#'+info_form_id+' input[name=exter_order_num]').val('');
                                //关闭你想关闭的菜单的方法
                               // $('a[href="#tab-186"]').parent().children('i').trigger('click');
                                //可以跳转到列表页方法
                                //var $li = $("#nva-tab li").children('a[href="#tab-<%$menu.id%>"]');
                                //if ($li.length == 1) {
                                //    util.syncTab("<%$menu.id%>");
                                //}
                                //new_tab("tab-<%$menu.id%>","<%$menu.label%>","<%$menu.url%>");
                            });
                        }


                    }else{
                        $('#'+info_form_id+' :submit').removeAttr('disabled',false);//解锁
                        util.error(data);
                    }
                }
            };

            $('#'+info_form_id).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    exter_order_num:{
                        required:true,
                    }
                },
                messages: {
                    exter_order_num:{
                        required:"外部订单号必须填写",
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
                $('#'+info_form_id+' select[name="order_source"]').select2("val",'');})
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