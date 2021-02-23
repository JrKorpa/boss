var form_edit_id = "goodsprice_by_style_edit";
var style_sn = "<%$style_sn|default:''%>";
var form_price_edit_id = "goodsprice_by_style_edit_price_update";
$import("public/js/select2/select2.min.js", function() {
	    var Obj = function() {
        var initElements = function() {
            
        };

        //表单验证和提交
   var handleForm = function() {
	   
         var url = 'index.php?mod=style&con=GoodsPriceByStyle&act=';
         var options1 = {
                url: url+(style_sn?"update":"insert"),
                error: function()
                {
                    alert('请求超时，请检查链接');
                },
                beforeSubmit: function(frm, jq, op) {
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function(data) {
					$('#'+form_edit_id+' :submit').removeAttr('disabled');//解锁
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
						util.xalert(
							"保存成功!",
							function(){
								setAttrHtml();					
							}
						);
					}
					else	
					{
						util.error(data);//错误处理
					}
					
                },
                error:function() {
                    $('.modal-scrollable').trigger('click');
                    alert("数据加载失败");
                }
        };
        var options2 = {
                url: "index.php?mod=style&con=GoodsPriceByStyle&act=updateAttrPrice",
                error: function()
                {
                    alert('请求超时，请检查链接');
                },
                beforeSubmit: function(frm, jq, op) {
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function(data) {
					$('#'+form_edit_id+' :submit').removeAttr('disabled');//解锁
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
						util.xalert(
							"保存成功!",
							function(){
								//setAttrPriceHtml();
								util.retrieveReload();//刷新查看页签
								//util.syncTab(data.tab_id);								
							}
						);
					}
					else	
					{
						util.error(data);//错误处理
					}
					
                },
                error:function() {
                    $('.modal-scrollable').trigger('click');
                    alert("数据加载失败");
                }
        };
		
		$('#'+form_edit_id).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    price: {
                        required: true
                    },
                },
                messages: {
                    price: {
                        required: "定价不能为0."
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
                    $("#"+form_edit_id).ajaxSubmit(options1);
                }
        });
		$('#'+form_price_edit_id).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    price: {
                        required: true
                    },
                },
                messages: {
                    price: {
                        required: "定价不能为0."
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
                    $("#"+form_price_edit_id).ajaxSubmit(options2);
                }
        });
   }
	var initData = function() {
	    if(style_sn !=""){
			setAttrHtml();
		}
		$("#reload_style_price_edit").click(function(){
			setAttrHtml();										 
	    });
	};
	return {
		init: function() {
			initElements();//处理表单元素
			handleForm();//处理表单验证和提交
			initData();//处理表单重置和其他特殊情况
		}
	}
}();
Obj.init();												 
													 
													 
													 
function setAttrHtml(){		
	var style_sn = $('#'+form_edit_id+' input[name="style_sn"]').val();
	if($.trim(style_sn)==""){
		return;
	}
	$('body').modalmanager('loading');//进度条和遮罩
	var url = "index.php?mod=style&con=GoodsPriceByStyle&act=getAttrHtml";
	$.post(url,"style_sn="+style_sn,function(res){
		 $('#'+form_edit_id+' #attr_data_content').html(res);
		 $('.modal-scrollable').trigger('click');
	});
    setAttrPriceHtml();
	setAttrSalepolicyHtml();
	setAttrSalepolicyFormHtml();
}
function setAttrPriceHtml(){
    var style_sn = $('#'+form_edit_id+' input[name="style_sn"]').val();
	$.post('index.php?mod=style&con=GoodsPriceByStyle&act=getAttrPriceList',"style_sn="+style_sn,function(res){
	     $("#goodsprice_by_style_price").html(res);
	});	
}
function setAttrSalepolicyHtml(){
	var style_sn = $('#'+form_edit_id+' input[name="style_sn"]').val();
	$.post('index.php?mod=style&con=GoodsPriceByStyle&act=getAttrSalepolicyList',"style_sn="+style_sn,function(res){
	     $("#goodsprice_by_style_salepolicy").html(res);
	});
}

function setAttrSalepolicyFormHtml(){
	var style_sn = $('#'+form_edit_id+' input[name="style_sn"]').val();
	$.post('index.php?mod=style&con=GoodsPriceByStyle&act=getAttrSalepolicyForm',"style_sn="+style_sn,function(res){
	     $("#style_salepolicy_form").html(res);
	});
}

$('#'+form_edit_id+' input[name="style_sn"]').keypress(function(){
	if(event.keyCode==13 && $.trim($(this).val())!=''){
		setAttrHtml();	
		return false;
	}		
});
$('#'+form_edit_id+' .search_btn').click(function(){
	setAttrHtml();													 
});



});