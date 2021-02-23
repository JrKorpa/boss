util.hover();
//登记快递单号
function register(obj){
	var url = "index.php?mod=shipping&con=BatchExpress&act=registerExpress";
	var data = "id="+$("#register_express_form input[name='file_id']").val();
	$("#register_express_form .freight_no").each(function(){
		var key = $(this).attr("name");
		var val = $(this).val();
		data += "&"+key+"="+val;
	});
	$.post(url,data,function(res){
		if(res.success){
			util.xalert("操作成功",function(){
			    util.retrieveReload();							
			});	
		}else if(res.error){
			util.xalert(res.error);	
		}else{
		   util.xalert(res.toString());	
		}							 
    },'json');
}
$("#register_express_form .freight_no").keypress(function(event){
														 
	if(event.keyCode==13 && $(this).val()!=''){
		var tabIndex = parseInt($(this).attr("tabindex"))+1;	
		$("#register_express_form .freight_no").parent().parent().removeClass("tab_click");
		$("#register_express_form .freight_no[tabindex=" + tabIndex + "]").parent().parent().addClass("tab_click");
		$("#register_express_form .freight_no[tabindex=" + tabIndex + "]").focus();
	}
});

function exportExpress(obj){
	var id=$("#register_express_form input[name='file_id']").val();
	var url=$(obj).attr("data-url")+"&id="+id;	
    location.href=url;	
}

