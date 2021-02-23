//修改快递方式
function updateship(obj){
    var order_sn = '<%$data['order_sn']%>';     //获取当前的订单号
    var express_id = '<%$data.express_id%>';   //获取当前的快递方式
    var send_status = '<%$data['send_good_status']%>'; //发货状态
    util._pop($(obj).attr('data-url'),{'order_sn':order_sn,'express_id':express_id , 'send_status':send_status});
}

//添加订单操作流水
function addliushui(obj){
    var url = $(obj).attr('data-url');
    var order_no = '<%$data['order_sn']%>';
    util._pop(url+'&order_no='+order_no);
}

function ship_freighr_submit(){

	//var is_piao = document.getElementsByName("is_piao")[0].checked
	var order_amount_all =$("#order_amount_all").val();
	var invoice_amount =$("#invoice_amount").val();
	var invoice_title =$("#invoice_title").val();
    var invoice_type =$("#invoice_type").val();
	var invoice_num =$("#invoice_num").val();
	var is_invoice =$("#is_invoice").val();
    var invoice_notice =$("#invoice_notice").val();
	if(is_invoice!=0 ){
        /*
		if(order_amount_all>100&&!invoice_num){
    		if(invoice_amount!=0 && invoice_title && invoice_type!=2){
        			 if (!confirm("此单还未开发票，请确认是否发货？")) 
        			{
        				return false;
        			}
    		}		
	    }*/
        if(invoice_notice=='yes'){
                if (!confirm("此单还未开发票，请确认是否发货？")){
                    return false;
                }            
        }
	}
	

	$('#ship_freight_order_info').submit();
   
}

//添加快递
function add_ship(obj){
   	 var order_sn = '<%$data['order_sn']%>';     //获取当前的订单号
   	 var send_status = '<%$data['send_good_status']%>'; //发货状态
     var consignee = '<%$data['consignee']%>'; //收件人
     var address = '<%$region->getRegionName($data['province_id'])%>省<%$region->getRegionName($data['city_id'])%>市<%$region->getRegionName($data['regional_id'])%><%$data['address']%>'; //收件地址
     var tel = '<%$data['tel']%>'; //手机
     var order_amount = '<%$data['order_amount']%>'; //订单金额
     var customer_source_id = '<%$data['department_id']%>'; //渠道
     var id = '<%$data['id']%>'; //渠道
     var order_status = '<%$data['order_status']%>'; //渠道
     var order_pay_status = '<%$data['order_pay_status']%>'; //渠道
     var consignee2 = '<%$data['consignee2']%>'; //渠道
     var tel2 = '<%$data['tel2']%>'; //渠道
     var address2 = '<%$data['address2']%>'; //渠道
    util._pop($(obj).attr('data-url'),{'order_sn':order_sn, 'send_status':send_status, 'consignee':consignee, 'address':address, 'tel':tel, 'order_amount':order_amount, 'customer_source_id':customer_source_id, 'id':id, 'order_status':order_status, 'order_pay_status':order_pay_status,'consignee2':consignee2, 'tel2':tel2, 'address2':address2});
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
				    $('body').modalmanager('loading');//进度条和遮罩
                    var goods_sn = goods_sn_box.val();
                    goods_sn = goods_sn.replace('.0', ''); //特别处理
                    var order_sn = $('#ship_freight_order_info_table1 input[name="order_no"]').val();
					//alert(goods_sn);alert(order_sn);return false;
                    var url = 'index.php?mod=shipping&con=ShipFreight&act=checkGoodsSN';
                    $.post(url,{'goods_sn':goods_sn,'order_sn':order_sn},function(e){
						$('body').modalmanager('removeLoading');//关闭进度条
                        var check_box = $('#ship_freight_goods_sn_check');
                        var send_box = $('#ship_freight_goods_sn_send');
                        goods_sn_box.val('');
                        if(e == 1){
                            check_box.css("background","#FFF").empty().append('<span class="btn btn-success" type="button">正确</span>');
                            var _g_ids = [];
                            $('#ship_freight_order_info_table2 input[name="sn_arr[]"]').each(function(){
                                _g_ids.push($(this).val());
                            });
                            var flag = true;
                            for(var x in _g_ids){
                                if(_g_ids[x] == goods_sn){
                                    flag = false;
                                    break;
                                }
                            }
                            if(flag){
                                send_box.append("<input type='hidden' class='form-control' name='sn_arr[]' value='"+goods_sn+"'/>");
                                $('#ship_freight_order_goods_'+goods_sn+' > td').css("background","gray");
                            }
                            //是否验货完毕
                            var num = '<%$num%>';
                            var pass = $('#ship_freight_order_info_table2 input[name="sn_arr[]"]').length;
                            if(pass >= num){
                                goods_sn_box.val('验货完毕').attr('disabled','disabled');
                               // $('#ship_freight_order_info_table2 input[name="freight_no"]').focus();
                               var freight_no=$('#ship_freight_order_info_table2 input[name="freight_no"]').val();
                               //货号验证成功直接进行提交
                               if(freight_no=='unfined'||freight_no.trim()==''){
                            	   check_box.css("background","red").empty().append('<span class="btn dark" type="button">请输入快递单号</span>')
                               }else{
                            	   ship_freighr_submit();
                               }
                            }
                         
                        }else{
                            check_box.css("background","red").empty().append('<span class="btn dark" type="button">错误</span>')
                        }
                    });
					return false;
                }
            });

           var freight_no_key_send = $('#ship_freight_order_info_table2 input[name="freight_no"]');
            freight_no_key_send.keypress(function(e){
                var freight_no = freight_no_key_send.val();
                if((e.keyCode == "13") && freight_no != '' )
                {
                   //ship_freighr_submit();
                   $('#ship_freight_order_info_table2 input[name="goods_sn"]').focus();
                }
            });

        };

        var handleForm = function(){
 		
            var url = 'index.php?mod=shipping&con=ShipFreight&act=insert&invoiceTip=1';
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
                        //util.xalert("操作成功!",function(){
							//刷新页面
                        /*    
                        if(data.invoice_num != null){
                            util.xalert("操作成功!发票编号:"+data.invoice_num);                            
                            //$('#ship_freight_search_list').empty(); 
						    //$('#tip').html('<span>  发货成功......发票编号:'+data.invoice_num+'</span>');
                        }*/
                            $('#tip').html('<span>  发货成功......发票编号:'+data.invoice_num+'</span>');
                            $('#tip span').fadeOut(2000);
    						setTimeout("util.retrieveReload()",2000);	
                        					
                    }
                    else
                    {    
					    if(data.invoiceTip==1){
							 util.xalert(data.error,function(){
							     $("#"+info_form_id).ajaxSubmit(options2);
							 });
						}else{
							util.xalert(data.error,function(){
								$('#ship_freight_search_form input[name="order_no"]').val("");
								$('#ship_freight_search_form input[name="order_no"]').focus();
							});//错误处理
						}
                    }
                }
            };
			var url2 = 'index.php?mod=shipping&con=ShipFreight&act=insert&invoiceTip=2';
            var options2 = {
                url: url2,
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
                      //  util.xalert("操作成功!",function(){
							//刷新页面
						$('#tip').html('<span>  发货成功......</span>');
						$('#tip span').fadeOut(2000);
						setTimeout("util.retrieveReload()",2000);						
                    }
                    else
                    {    
                        util.xalert(data.error,function(){
                            $('#ship_freight_search_form input[name="order_no"]').val("");
                            $('#ship_freight_search_form input[name="order_no"]').focus();
                        });//错误处理
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
                $('#ship_freight_order_express').bind("click", function(){
                    var order_sn = '<%$data['order_sn']%>';     //获取当前的订单号
                    var express_id = '<%$data.express_id%>';
                    $.post('index.php?mod=shipping&con=ShipFreight&act=orderExpress',{'order_sn':order_sn,'express_id':express_id,'express_order_id':''},function(data) {
                        if(data.result==1){
                            $('#ship_freight_no').val(data.express_no);
                            $('#ship_freight_order_express').attr('disabled','true');  
                            $('.green-stripe').html("");                                     
                            var url = 'index.php?mod=shipping&con=ShipFreight&act=print_express&order_no=<%$data['order_sn']%>&express_id='+express_id+'&express_order_id='+data.express_order_id;
                            window.open(url,'_blank','fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,_blank');
                            $('.green-stripe').append('<i class="fa fa-print"></i> <a onclick="window.open(\''+url+'\',\'\',\'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false\')"  target="_blank">打印快递单</a>');

                      }else
                            util.xalert(data.error);
                    }); 
                    $('#ship_freight_order_info_table2 input[name="goods_sn"]').focus();
                });
        };


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