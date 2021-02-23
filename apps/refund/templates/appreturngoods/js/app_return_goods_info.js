<!--
//复选框组美化
var test = $("#app_return_goods_box input[type='checkbox']:not(.toggle, .make-switch)");
if (test.size() > 0) {
    test.each(function () {
    if ($(this).parents(".checker").size() == 0) {
        $(this).show();
        $(this).uniform();
    }
  });
}

// table 复选框全选
$('#app_return_goods_box .group-checkable').change(function () {
  var set = $(this).attr("data-set");
    var checked = $(this).is(":checked");
    $(set).each(function () {
        if (checked) {
            $(this).attr("checked", true);
            $(this).parents('tr').addClass("active");
        } else {
            $(this).attr("checked", false);
            $(this).parents('tr').removeClass("active");
        }                    
    });
    $.uniform.update(set);
});
$('#app_return_goods_box').live('change', 'tbody tr .checkboxes', function(){
    $(this).parents('tr').toggleClass("active");
});
//-->
$import(["public/js/select2/select2.min.js"],function(){
	var info_form_id = 'app_return_goods_info';//form表单id
	var info_form_base_url = 'index.php?mod=refund&con=AppReturnGoods&act=';//基本提交路径
	var info_id= '<%$view->get_return_id()%>';
	var obj = function(){
		var initElements = function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			}
			var test = $("#app_return_goods_info input[type='radio']:not(.toggle, .star, .make-switch),#app_return_goods_info input[type='checkbox']:not(.toggle, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
			$('#app_return_goods_info select[name="zuandan_reason_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});	

			$('#app_return_goods_info_container a[name="g"]').on('click',function(){
				$("#app_return_goods_info_container").hide();
				$("#app_return_goods_info_bg").hide();
			})
			
			$("#portlet_box").hide();
		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+(info_id ? 'update' : 'insert');
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
							info_id ? "修改成功!": "添加成功!",
							function(){
								if (data._cls)
								{//查看编辑
									util.retrieveReload();//刷新查看页签
									util.syncTab(data.tab_id);//刷新数据主列表，无法定位到分页（有可能数据列表页签已经关闭，也有可能是其他对象穿透查看，所以分页函数不一定存在）
								}
								else
								{
									if (info_id)
									{//刷新当前页
										util.page(util.getItem('url'));
									}
									else
									{
                                        var $li = $("#nva-tab li").children('a[href="#tab-<%$menu.id%>"]');
                                        if ($li.length == 1) {
                                            util.syncTab("<%$menu.id%>");
                                        }
                                        util.closeTab();
                                        new_tab("tab-<%$menu.id%>","<%$menu.label%>","<%$menu.url%>");
									}
								}
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
		};
		var initData = function(){
		
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


var change_return_by=function(){
	//验证
	$("#portlet_box").hide();
	$('#app_return_goods_box .group-checkable').attr("checked", false).parents(".checked").removeClass('checked');
}

var change_return_type=function(it){
	if(it==1){
		$("#daka").hide();
		$("#xianjin").hide();
		$("#zhuandan").show();
	}else if(it==2){
		$("#zhuandan").hide();
		$("#xianjin").hide();	
		$("#daka").show();
	}else if(it==3){
		$("#zhuandan").hide();
		$("#daka").hide();
		$("#xianjin").show();
	}
}

var start_step2=function(){
	//验证
	var return_by = $("input[name=return_by]:checked").val();
	var return_type = $("input[name=return_type]:checked").val();
	var order_sn = $("#app_return_goods_info input[name='order_sn']").val();
    var helped = $("#helped:checked").val();

	if(typeof(helped)=='undefined'){
		util.xalert('请先阅读退款帮助!');
		return false;
	}
	if(typeof(return_by)=='undefined' || return_by==''){
		util.xalert('请选择退款方式!');
		return false;
	}else if(typeof(return_type)=='undefined' || return_type==''){
		util.xalert('请选择退款类型!');
		return false;
	}else if(typeof(order_sn)=='undefined' || order_sn==''){
		util.xalert('请输入订单号!');
		return false;
	}
	

	$.ajax({
		type:"POST",
		url: 'index.php?mod=refund&con=AppReturnGoods&act=check',
		data: {
			order_sn:order_sn,
            return_by:return_by,
			is_ajax:1
		},
		dataType: "json",
		async:false,
		success: function(res){
			if(res.success==1){
				var return_by = $("input[name=return_by]:checked").val();
				if(return_by=='1'){
					if(res.goods_num=='0'){
						util.xalert('订单商品不存在!');
						return false;
					}
				}
				$("#portlet_box").show();
				$("#portlet_box_fa > .portlet-title").trigger("click");
				$("#order_sn2").html(res.content.order_sn);
				$("#money_paid2").html(res.content.money_paid);
				$("#money_unpaid2").html(res.content.order_amount);
				var goods=res.goods;
				var goods_list_html = '';
				if(typeof(goods)!='undefined'&&goods){
					var len=res.goods_num;
					for(var i=0;i<len;i++){
						var apply_amount = "";
						goods_list_html+='<tr>';
						goods_list_html+='<td><input class="checkboxes" type="checkbox" name="order_goods_id[]" value="'+goods[i].rec_id+'"/></td>';
						goods_list_html+='<td>'+(i+1)+'</td>';
						if(return_by==1){
						     goods_list_html+='<td><input class="form-control" type="text" name="apply_amount['+goods[i].rec_id+']" value="'+goods[i].goods_paid+'" readonly/></td>';	    
					   }else{
							goods_list_html+='<td><input class="form-control" type="text" name="apply_amount['+goods[i].rec_id+']" value=""/></td>';	
						}
						goods_list_html+='<td><input class="form-control" type="text" name="price_fee['+goods[i].rec_id+']" value=""/></th>';
						goods_list_html+='<td style="text-align:center">'+goods[i].rec_id +'货号:'+goods[i].goods_id+','+ goods[i].goods_name+'('+goods[i].goods_price+'/已付余额'+goods[i].goods_paid+')</td>';
						goods_list_html+='</tr>';
						
					}
				}
				$("#app_return_goods_list").html(goods_list_html);
				util.hover();
				util.check(util.getItem('listDIV'));
				/*$("#app_return_goods_list input[name='order_goods_id[]']").change(function(){
					var return_by = $("input[name=return_by]:checked").val();
					var order_goods_id = $(this).val();
					var apply_amountObj = $("#app_return_goods_list input[name=\"apply_amount['"+order_goods_id+"']\"]");
					if(return_by=='1'){
					   apply_amountObj.val($(this).attr('goods_price'));
					   $("#app_return_goods_list .apply_amount").attr('readonly',true);
					}else{
					   $("#app_return_goods_list .apply_amount").attr('readonly',false);
					}
				});*/
			}else{
				util.xalert(res.error);return false;
			}
		}
	});
}

var refund_goods_id=function(it){
	var return_by = $("input[name=return_by]:checked").val();
	bootbox.confirm("你确定要退商品吗?", function(result) {
		if (result == true) {
            if(return_by==2){
                util.xalert('由于你选择的退款方式出错，请重新选择！');
                start_step1();
                $("input[name=return_by]")[1].focus();
            }
		}else{
			$("#t2").find('input[type="radio"]').attr('checked', false);
		}
	});
}

var start_step1=function(){
	$("#portlet_box").hide();
	if ($("#portlet_box_fa .portlet-body").is(":hidden")) {
		console.log(1);
		$("#portlet_box_fa .portlet-title").trigger('click');
	};	
}
/*
//转单申请提交
var submit_apply=function(){
	//验证
	var return_by = $("input[name=return_by]:checked").val();
	var return_type = $("input[name=return_type]:checked").val();
	var order_sn = $("#app_return_goods_info input[name='order_sn']").val();
	var helped = $("#helped:checked").val();
	if(typeof(helped)=='undefined'){
		util.xalert('请先阅读退款帮助!');
		return false;
	}
	if(return_by==''){
		util.xalert('请选择退款方式!');
		return false;
	}else if(return_type==''){
		util.xalert('请选择退款类型!');
		return false;
	}else if(order_sn==''){
		util.xalert('请输入订单号!');
		return false;
	}
	var data=new Object();
	data['is_ajax']=1;
	if(return_by=='1'){
		var order_goods_id = parseInt($("input[name=t]:checked").val());
		if(isNaN(order_goods_id) || order_goods_id<=0){
			util.xalert('请选择退款商品');
			return false;
		}
		data['order_goods_id']=order_goods_id;
	}
	var apply_amount=parseFloat($("#apply_amount").val());
	var price_fee=parseFloat($("#price_fee").val());
	if(isNaN(apply_amount) || apply_amount<=0){
		util.xalert('请输入申请退款金额!');
		return false;
	}
	if(isNaN(price_fee) || price_fee<0){
		util.xalert('手续费金额为空或不合法!');
		return false;
	}
	data['order_sn']=order_sn;
	data['return_by']=return_by;
	data['return_type']=return_type;
	data['apply_amount']=apply_amount;
	data['price_fee']=price_fee;
	if(return_type=='1'){
		data['return_res']=$("#return_res1").val();
		data['zuandan_reason_id']=$("#zuandan_reason_id").val();
	}else if(return_type=='2'){
		data['return_res']=$("#return_res2").val();
		data['consignee']=$("#consignee2").val();
		data['bank_name']=$("#bank_name2").val();
		data['return_card']=$("#return_card2").val();
		data['mobile']=$("#mobile2").val();
	}else if(return_type=='3'){
		data['consignee']=$("#consignee3").val();
		data['return_card']=$("#return_card3").val();
		data['mobile']=$("#mobile3").val();
		data['return_res']=$("#return_res3").val();
	}
	$.ajax({
		type:"POST",
		url: 'index.php?mod=refund&con=AppReturnGoods&act=applyPost',
		data: data,
		dataType: "json",
		async:false,
		success: function(res){
			if(res.success==1){
				util.xalert(res.content);
				$("button[type=button]").trigger('click');//解锁
			}else{
				util.xalert(res.error);return false;
			}
		}
	});
}
*/
function Execution(){ //js file;

	document.getElementById('app_return_goods_info_bg').style.display='block';

    //根据ID返回dom元素 
    var $ = function(id){return document.getElementById(id);} 
    //返回dom元素的当前某css值 
    var getCss = function(obj,name){ 
        if(obj.currentStyle) {//for ie ;
            return obj.currentStyle[name]; 
        }else { // for ff;
            var style = document.defaultView.getComputedStyle(obj,null); 
            return style[name]; 
        } 
    } 
     
    var show = function(obj,speed){ 
        obj = $(obj); 
        if (!speed) { 
            obj.style.display = 'block'; 
            return; 
        }
        var initH = 0 , initW = 0;
        //获取dom的宽与高 
        var oWidth = getCss(obj,'width').replace('px',''), oHeight = getCss(obj,'height').replace('px',''); 
        //每次dom的递减数(等比例) 
        var wcut = 2*(+oWidth.replace('px','') / +oHeight.replace('px','')),hcut = 2; 
        //处理动画函数 
        var process = function(){ 
            obj.style.overflow = 'hidden';
            obj.style.display = 'block';
            obj.style.overflow = 'scroll';
            obj.style.zIndex = 10011;
            obj.style.border = '4px solid #000000';
            initW = (initW+wcut) > oWidth ? oWidth : initW+wcut; 
            initH = (initH+hcut) > oHeight ? oHeight : initH+hcut; 
            //判断是否减完了 
            if(initW !== oWidth || initH !== oHeight) { 
                obj.style.width = initW+'px'; 
                obj.style.height = initH+'px'; 

                setTimeout(function(){process();},speed); 
            }else { 
                //加完后，设置属性为显示以及原本dom的宽与高; 
                obj.style.width = oWidth+'px';
                obj.style.height = oHeight+'px';

            } 
        } 
        //process(); 
    } 
	setDivCenter();
  document.getElementById('app_return_goods_info_container').style.display='block';
  document.getElementById('app_return_goods_info_container').style.zIndex = 10011;
  document.getElementById('app_return_goods_info_container').style.overflow = 'hidden';
  document.getElementById('app_return_goods_info_container').style.overflow = 'scroll';
  document.getElementById('app_return_goods_info_container').style.border = '4px solid #000000';
  //show("app_return_goods_info_container",3);   
}

//让指定的DIV始终显示在屏幕正中间  
function setDivCenter(){  
	var top = ($("#app_return_goods_info_body").height() - $("#app_return_goods_info_container").height())/2;  
	var left = ($("#app_return_goods_info_body").width() - $("#app_return_goods_info_container").width())/2;  
	var scrollTop = $(document).scrollTop();  
	var scrollLeft = $(document).scrollLeft(); 
	$("#app_return_goods_info_container").css( { position : 'absolute', top : top + scrollTop, left : left + scrollLeft } ).show(); 
}  

$(window).resize(function(){
	//Execution();
});
